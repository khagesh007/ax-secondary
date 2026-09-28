<?php

namespace App\Http\Controllers\Admin\Deals;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DealSubStatus;
use Auth;
use Illuminate\Support\Facades\Gate;
use App\Traits\FileTrait;

class DealSubStatusController extends Controller
{
    use FileTrait;
    public function index()
    {
        abort_if(Gate::denies("Deal Sub Statuses List"), 401);
        return view('admin.deals.sub_status');
    }

    public function store(Request $request)
    {
        $request->validate([
            "name" => "required|string",
            // 'thumbnail' => 'nullable|string',
            // 'image' => 'nullable|string',
            // 'color_code' => 'nullable|string',

            'name' => 'required|string',
            'icon' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'order' => 'nullable|string',
            'status_badge' => 'nullable|string',
        ]);

        try {

            $obj = new DealSubStatus();
            $obj->name = $request->name;
            $obj->badge = $request->status_badge;
            $obj->order = $request->order??0;
            $obj->icon = $request->icon;
            $obj->status = $request->status == 'on' ? 1 : 0;
            $obj->created_by_id = auth()->user()->id;

            if ($request->has('thumbnail')) {
                $image = $this->uploadImage($request->file("thumbnail"));
                $obj->thumbnail = $image;
            }
            if ($request->has('image')) {
                $image = $this->uploadImage($request->file("image"));
                $obj->image = $image;
            }
            $obj->save();

            // $category = DealSubStatus::create([
            //     "name" => $request->name,
            //     // 'slug' => Str::slug($request->title, "-"),
            //     // "color_code" => $request->color_code,
            //     "status" => $request->boolean("status"),
            //     "created_by_id" => auth()->id(),
            // ]);
            $result = array('status' => '1', 'msg' => 'Deal sub status added successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }

    public function dealsForDatatable(Request $request)
    {
        $columns = array(
            0 => 'updated_at',
            1 => 'id',
            2 => 'name'
        );
        $searchKeyWord = htmlspecialchars($request['search']['value']);
        $totalData = DealSubStatus::count();
        $totalFiltered = $totalData;

       

        if (!empty($searchKeyWord)) {
            $data_filtered = DealSubStatus::where(function ($q) use ($searchKeyWord) {
                $q->where('name', 'LIKE', '%' . $searchKeyWord . '%');
            })->with('created_by', 'updated_by');

            $totalFiltered = $data_filtered->count();
            $data = $data_filtered->limit($request['length'])->get();
        } else {
            $data_filtered = DealSubStatus::orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
                ->with('created_by', 'updated_by');
            $totalFiltered = $data_filtered->count();
            $data = $data_filtered->limit($request['length'])->offset($request['start'])->get();
        }
        $json_data = array(
            "draw" => intval($request['draw']),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );

        echo json_encode($json_data);
    }

    public function getSubDeals(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:deal_sub_statuses,id',
        ]);
        try {
            $cat = DealSubStatus::find($request->id);
            $result = array('status' => '1', 'msg' => 'Deal sub status details fetched successfully', 'data' => $cat);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }


    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:deal_sub_statuses,id',
            "name" => "required|string",
            'icon' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'order' => 'nullable|string',
            'status_badge' => 'nullable|string',
            // 'thumbnail' => 'nullable|string',
            // 'image' => 'nullable|string',
            // 'color_code' => 'nullable|string',
        ]);

        try {
            // $category = DealSubStatus::find($request->id);
            // $category->update([
            //     "name" => $request->name,
            //     // 'slug' => Str::slug($request->title, "-"),
            //     // "color_code" => $request->color_code,
            //     "status" => $request->boolean("status"),
            //     "updated_by_id" => auth()->id(),
            // ]);


            $type = DealSubStatus::find($request->id);
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

            $result = array('status' => '1', 'msg' => 'Deal sub status details updated successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:deal_sub_statuses',
            'action' => 'required',
        ]);

        $type = DealSubStatus::find($request->id);
        $type->status = $request->boolean('action');
        $type->updated_by_id = auth()->user()->id;
        if ($type->save()) {
            $result = array('status' => '1', 'msg' => 'Deal sub status updated successfully');
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:deal_sub_statuses,id',

        ]);

        $type = DealSubStatus::find($request->id);
        if ($type->delete()) {
            $result = array('status' => '1', 'msg' => 'Deal sub status deleted successfully');
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
            if (DealSubStatus::whereIn('id', $ids)->delete()) {
                $result = array('status' => '1', 'msg' => count($ids).' Deal sub status deleted successfully');
            } else {
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }
        }
        return json_encode($result);
    }
}
