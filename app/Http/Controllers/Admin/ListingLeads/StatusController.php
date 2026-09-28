<?php

namespace App\Http\Controllers\Admin\ListingLeads;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ListingLeadStatus;
use App\Traits\FileTrait;
use Gate;


class StatusController extends Controller
{
    use FileTrait;

    public function index()
    {
        abort_if(Gate::denies("Lead Statuses List"), 401);
        return view('admin.listingLeadStatus.index');
    }

    public function store(Request $request)
    {

        $validator = \Validator::make($request->all(), [

            'name' => 'required|string',
            'icon' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'order' => 'nullable|string',
            'status_badge' => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => '2', 'errors' => $validator->errors()]);
        }

        try {

            $type = new ListingLeadStatus();
            $type->name = $request->name;
            $type->badge = $request->status_badge;
            $type->order = $request->order??0;
            $type->icon = $request->icon;
            $type->status = $request->status == 'on' ? 1 : 0;
            $type->created_by_id = auth()->user()->id;

            if ($request->has('thumbnail')) {
                $image = $this->uploadImage($request->file("thumbnail"));
                $type->thumbnail = $image;
            }
            if ($request->has('image')) {
                $image = $this->uploadImage($request->file("image"));
                $type->image = $image;
            }
            $type->save();

            $result = array('status' => '1', 'msg' => 'Listing Lead status added successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }

    public function statusForDatatable(Request $request)
    {
        $columns = array(
            0 => 'order',
            1 => 'order',
            2 => 'name',
        );
        $searchKeyWord = htmlspecialchars($request['search']['value']);
        $totalData = ListingLeadStatus::count();
        $totalFiltered = $totalData;

        if (!empty($searchKeyWord)) {
            $data_filtered = ListingLeadStatus::where(function ($q) use ($searchKeyWord) {
                $q->where('name', 'LIKE', '%' . $searchKeyWord . '%');
            })->with('created_by','updated_by')->orderBy('order', 'asc');
            $totalFiltered = $data_filtered->count();
            $data = $data_filtered->limit($request['length'])->orderBy('order', 'asc')->get();
        } else {
            $data_filtered = ListingLeadStatus::orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
            ->with('created_by','updated_by')->orderBy('order', 'asc');
            $totalFiltered = $data_filtered->count();
            $data = $data_filtered->limit($request['length'])->offset($request['start'])->orderBy('order', 'asc')->get();
        }

        $json_data = array(
            "draw" => intval($request['draw']),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data,
        );

        echo json_encode($json_data);
    }

    public function getStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:lead_statuses,id',
        ]);
        try {
            $cat = ListingLeadStatus::find($request->id);
            $result = array('status' => '1', 'msg' => 'Listing Lead status details fetched successfully', 'data' => $cat);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function update(Request $request)
    {
        //  dd($request->all());
        $request->validate([
            'id' => 'required|exists:lead_statuses,id',
            'name' => 'required|string',
            'icon' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'order' => 'nullable|string',
            'status_badge' => 'nullable|string',

        ]);

        try {
            $type = ListingLeadStatus::find($request->id);
            $type->name = $request->name;
            $type->badge = $request->status_badge;
            $type->order = $request->order??0;
            $type->icon = $request->icon;
            $type->status = $request->status == 'on' ? 1 : 0;
            $type->updated_by_id = auth()->user()->id;

            if ($request->has('thumbnail')) {
                $image = $this->uploadImage($request->file("thumbnail"));
                $type->thumbnail = $image;
            }
            if ($request->has('image')) {
                $image = $this->uploadImage($request->file("image"));
                $type->image = $image;
            }

            $type->save();
            $result = array('status' => '1', 'msg' => 'Listing Lead status details updated successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:lead_statuses',
            'action' => 'required',
        ]);

        $status = ListingLeadStatus::find($request->id);
        $status->status = $request->boolean('action');
        $status->updated_by_id = auth()->user()->id;
        if ($status->save()) {
            $result = array('status' => '1', 'msg' => 'Listing Lead status updated successfully');
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:lead_statuses',

        ]);

        $type = ListingLeadStatus::find($request->id);
        if ($type->delete()) {
            $result = array('status' => '1', 'msg' => 'Listing Lead status deleted successfully');
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function deleteMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',

        ]);
        if (gettype($request->ids) == 'array' && count($request->ids) > 0) {
            $ids = $request->ids;
            if (ListingLeadStatus::whereIn('id', $ids)->delete()) {
                $result = array('status' => '1', 'msg' => count($ids) . 'Listing Lead status deleted successfully');
            } else {
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }

        }

        return json_encode($result);
    }
}
