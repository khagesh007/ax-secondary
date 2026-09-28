<?php

namespace App\Http\Controllers\Admin\Lead;

use App\Http\Controllers\Controller;
use App\Models\LeadStatus;
use App\Traits\FileTrait;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class StatusController extends Controller
{
    use FileTrait;

    public function index()
    {
        abort_if(Gate::denies("Lead Statuses List"), 401);
        return view('admin.lead.status');
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

            $type = new LeadStatus();
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

            $result = array('status' => '1', 'msg' => 'Lead status added successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }

    public function statusForDatatable(Request $request)
    {
        $columns = array(
            0 => 'id',
            1 => 'id',
            2 => 'name',
        );
        $searchKeyWord = htmlspecialchars($request['search']['value']);
        $totalData = LeadStatus::count();
        $totalFiltered = $totalData;

        // if (!empty($searchKeyWord)) {
        //     $data = LeadStatus::where(function ($q) use ($searchKeyWord) {
        //         $q->where('name', 'LIKE', '%'.$searchKeyWord.'%');
        //     })->with('created_by', 'updated_by')->limit($request['length'])->get();
        //     $totalFiltered = $data->count();

        // } else {
        //     $data = LeadStatus::orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
        //         ->offset($request['start'])
        //         ->limit($request['length'])
        //         ->with('created_by', 'updated_by')
        //         ->get();
        //         $totalFiltered = $data->count();

        // }

        if (!empty($searchKeyWord)) {
            $data_filtered = LeadStatus::where(function ($q) use ($searchKeyWord) {
                $q->where('name', 'LIKE', '%' . $searchKeyWord . '%');
            })->with('created_by', 'updated_by');

            $totalFiltered = $data_filtered->count();
            $data = $data_filtered->limit($request['length'])->get();
        } else {
            $data_filtered = LeadStatus::orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
                ->with('created_by', 'updated_by');
            $totalFiltered = $data_filtered->count();
            $data = $data_filtered->limit($request['length'])->offset($request['start'])->get();
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
            $cat = LeadStatus::find($request->id);
            $result = array('status' => '1', 'msg' => 'Lead status details fetched successfully', 'data' => $cat);
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
            $type = LeadStatus::find($request->id);
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
            $result = array('status' => '1', 'msg' => 'Lead status details updated successfully');
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

        $status = LeadStatus::find($request->id);
        $status->status = $request->boolean('action');
        $status->updated_by_id = auth()->user()->id;
        if ($status->save()) {
            $result = array('status' => '1', 'msg' => 'Lead  status updated successfully');
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

        $type = LeadStatus::find($request->id);
        if ($type->delete()) {
            $result = array('status' => '1', 'msg' => 'Lead status deleted successfully');
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
            if (LeadStatus::whereIn('id', $ids)->delete()) {
                $result = array('status' => '1', 'msg' => count($ids) . ' Lead status deleted successfully');
            } else {
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }

        }

        return json_encode($result);
    }
}
