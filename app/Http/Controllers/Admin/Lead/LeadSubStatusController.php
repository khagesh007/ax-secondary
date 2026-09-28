<?php

namespace App\Http\Controllers\Admin\Lead;

use App\Http\Controllers\Controller;
use App\Models\LeadStatus;
use App\Models\LeadSubStatus;
use App\Traits\FileTrait;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class LeadSubStatusController extends Controller
{
    use FileTrait;

    public function index()
    {
        abort_if(Gate::denies("Lead Sub Statuses List"), 401);
        $statuses = LeadStatus::where("status", true)->get();
        return view('admin.lead.sub_status', compact("statuses"));
    }

    public function store(Request $request)
    {
        $request->validate([
            "name" => "required|string",
            "lead_status_id" => "required|string",
        ]);

        try {

            $category = new LeadSubStatus();

            $category->name = $request->name;
            $category->lead_status_id = $request->lead_status_id;
            $category->status = $request->boolean("status");
            $category->order = $request->order??0;
            $category->badge = $request->status_badge;
            $category->icon = $request->icon;
            $category->updated_by_id = auth()->id();
            $category->created_by_id = auth()->id();

            if ($request->has('thumbnail')) {
                $image = $this->uploadImage($request->file("thumbnail"));
                $category->thumbnail = $image;
            }
            if ($request->has('image')) {
                $image = $this->uploadImage($request->file("image"));
                $category->image = $image;
            }
            $category->save();

            $result = array('status' => '1', 'msg' => 'Lead sub status added successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }

    public function substatusForDatatable(Request $request)
    {
        $columns = array(
            0 => 'id',
            1 => 'id',
            2 => 'name',
        );
        $searchKeyWord = htmlspecialchars($request['search']['value']);
        $totalData = LeadSubStatus::count();
        $totalFiltered = $totalData;

        if (!empty($searchKeyWord)) {
            $data_filtered = LeadSubStatus::where(function ($q) use ($searchKeyWord) {
                $q->where('name', 'LIKE', '%' . $searchKeyWord . '%');
            })->with('created_by', 'updated_by');

            $totalFiltered = $data_filtered->count();
            $data = $data_filtered->limit($request['length'])->get();
        } else {
            $data_filtered = LeadSubStatus::orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
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
            'id' => 'required|exists:lead_sub_statuses,id',
        ]);
        try {
            $cat = LeadSubStatus::find($request->id);
            $cat->load("leadStatus");
            $result = array('status' => '1', 'msg' => 'Lead sub status details fetched successfully', 'data' => $cat);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function update(Request $request)
    {
       
        $request->validate([
            'id' => 'required|exists:lead_sub_statuses,id',
            "name" => "required|string",
            "lead_status_id" => "required|string",
        ]);

        try {
            $category = LeadSubStatus::find($request->id);

            $category->name = $request->name;
            $category->lead_status_id = $request->lead_status_id;
            $category->status = $request->boolean("status");
            $category->order = $request->order??0;
            $category->badge = $request->status_badge;
            $category->icon = $request->icon;
            $category->updated_by_id = auth()->id();

            if ($request->has('thumbnail')) {
                $image = $this->uploadImage($request->file("thumbnail"));
                $category->thumbnail = $image;
            }
            if ($request->has('image')) {
                $image = $this->uploadImage($request->file("image"));
                $category->image = $image;
            }
            $category->save();

            $result = array('status' => '1', 'msg' => 'Lead sub status details updated successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:lead_sub_statuses',
            'action' => 'required',
        ]);

        $type = LeadSubStatus::find($request->id);
        $type->status = $request->boolean('action');
        $type->updated_by_id = auth()->user()->id;
        if ($type->save()) {
            $result = array('status' => '1', 'msg' => 'Lead sub status updated successfully');
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:lead_sub_statuses,id',

        ]);

        $type = LeadSubStatus::find($request->id);
        if ($type->delete()) {
            $result = array('status' => '1', 'msg' => 'Lead sub status deleted successfully');
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
            if (LeadSubStatus::whereIn('id', $ids)->delete()) {
                $result = array('status' => '1', 'msg' => count($ids) . ' Lead sub status deleted successfully');
            } else {
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }
        }
        return json_encode($result);
    }

}
