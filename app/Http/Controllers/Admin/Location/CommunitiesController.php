<?php

namespace App\Http\Controllers\Admin\Location;

use App\Models\Community;
use App\Models\Region;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use DataTables;
class CommunitiesController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies("Communities List"), 401);
        $regions = Region::where("status", true)->get(["id", "name"]);
        return view('admin.location.communities', compact("regions"));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            "region_id" => "required|exists:regions,id",
            "name" => "required|string",
            "alternate_name" => "nullable|string",
            "description" => "nullable|string",
            "path_name" => "required|string",
            "alternate_path_name" => "nullable|string",
            "latitude" => "required|numeric|between:-90,90",
            "longitude" => "required|numeric|between:-180,180",
            "status" => "nullable",
        ]);

        try {

            $validatedData["slug"] = Str::slug($request->name, "-");
            $validatedData["status"] = $request->boolean("status");
            $validatedData["created_by_id"] = auth()->id();

            Community::create($validatedData);
            $result = array('status' => '1', 'msg' => 'Community added successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }

    public function communityForDatatable(Request $request)
    {

        // $query = Community::select('regions.*');
            

        //     return DataTables::of(($query)->with('created_by', 'updated_by'))
        //     ->make(true);

        $columns = array(
            0 => 'updated_at',
            1 => 'id',
            2 => 'name',
            3 => 'alternate_name',
            4 => 'region_id',
            5 => 'updated_by_id',
            6 => 'updated_at',
            7 => 'created_by_id',
            8 => 'created_at',
            9 => 'status',
        );
        $searchKeyWord = htmlspecialchars($request['search']['value']);
        $totalData = Community::count();
        $totalFiltered = $totalData;

        // if (!empty($searchKeyWord)) {
        //     $data = Community::where(function ($q) use ($searchKeyWord) {
        //         $q->where('name', 'LIKE', '%'.$searchKeyWord.'%');
        //     })->with('createdBy', 'updatedBy', 'region')
        //     ->limit($request['length'])
        //     ->get();
        //     $totalFiltered = $data->count();
        // } else {
        //     $data = Community::orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
        //         ->offset($request['start'])
        //         ->limit($request['length'])
        //         ->with('createdBy', 'updatedBy', 'region')
        //         ->get();
        //         $totalFiltered = $data->count();
        // }


        // if (!empty($searchKeyWord)) {
        //     $data_filtered = Community::where(function ($q) use ($searchKeyWord) {
        //         $q->where('name', 'LIKE', '%' . $searchKeyWord . '%');
        //     })->with('createdBy', 'updatedBy', 'region');
        //     $totalFiltered = $data_filtered->count();
        //     $data = $data_filtered->limit($request['length'])->get();
        // } else {
        //     $data_filtered = Community::orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
        //         ->offset($request['start'])
        //         ->with('createdBy', 'updatedBy', 'region');
        //         $totalFiltered = $data_filtered->count();

        //     $data = $data_filtered->limit($request['length'])->get();
        // }


        if (!empty($searchKeyWord)) {
            $data_filtered = Community::where(function ($q) use ($searchKeyWord) {
                $q->where('name', 'LIKE', '%' . $searchKeyWord . '%');
                $q->orwhere('alternate_name', 'LIKE', '%' . $searchKeyWord . '%');
            })->with('createdBy', 'updatedBy', 'region');

            $totalFiltered = $data_filtered->count();
            $data = $data_filtered->limit($request['length'])->get();
        } else {
            $data_filtered = Community::orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
                ->with('createdBy', 'updatedBy', 'region');
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

    public function getCommunity(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:communities,id',
        ]);
        try {
            $cat = Community::find($request->id);
            $result = array('status' => '1', 'msg' => 'Community details fetched successfully', 'data' => $cat);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            "id" => "required|exists:communities,id",
            "region_id" => "required|exists:regions,id",
            "name" => "required|string",
            "alternate_name" => "nullable|string",
            "description" => "nullable|string",
            "path_name" => "required|string",
            "alternate_path_name" => "nullable|string",
            "latitude" => "required|numeric|between:-90,90",
            "longitude" => "required|numeric|between:-180,180",
            "status" => "nullable",
        ]);

        try {

            $validatedData["slug"] = Str::slug($request->name, "-");
            $validatedData["status"] = $request->boolean("status");
            $validatedData["updated_by_id"] = auth()->id();

            $community = Community::find($request->id)->update($validatedData);
            $result = array('status' => '1', 'msg' => 'Community updated successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:communities',
            'action' => 'required',
        ]);

        $status = Community::find($request->id);
        $status->status = $request->boolean('action');
        $status->updated_by_id = auth()->user()->id;
        if ($status->save()) {
            $result = array('status' => '1', 'msg' => 'Status updated successfully');
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:communities',
        ]);

        $status = Community::find($request->id);
        if ($status->delete()) {
            $result = array('status' => '1', 'msg' => 'Community deleted successfully');
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
            if (Community::whereIn('id', $ids)->delete()) {
                $result = array('status' => '1', 'msg' => count($ids).' Communities deleted successfully');
            } else {
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }
        }
        return json_encode($result);
    }
}
