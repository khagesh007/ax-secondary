<?php

namespace App\Http\Controllers\Admin\Location;

use App\Http\Controllers\Controller;
use App\Models\CommunityProperty;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class CommunityPropertyController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies("Community Properties List"), 401);
        $regions = Region::where("status", true)->get(["id", "name"]);
        return view('admin.location.communities-properties', compact("regions"));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            "region_id" => "required|exists:regions,id",
            "community_id" => "required|exists:communities,id",
            "sub_community_id" => "required|exists:sub_communities,id",
            "name" => "required|string",
            "alternate_name" => "nullable|string",
            "description" => "nullable|string",
            "path_name" => "required|string",
            "alternate_path_name" => "nullable|string",
            "latitude" => "required|numeric|between:-90,90",
            "longitude" => "required|numeric|between:-180,180",
            "status" => "nullable",
            "default_use" => "nullable",
            "alternate_use" => "nullable",
            "pf_id" => "nullable|string",
            "pf_path" => "nullable|string",
        ]);

        try {

            $validatedData["slug"] = Str::slug($request->name, "-");
            $validatedData["status"] = $request->boolean("status");
            $validatedData["alternate_use"] = $request->boolean("alternate_use");
            $validatedData["default_use"] = $request->boolean("default_use");
            $validatedData["created_by_id"] = auth()->id();
            $location = CommunityProperty::create($validatedData);

            if($location){
                $portal_ids = json_decode($location->portal_ids, true) ?? [];
                $portal_ids['property_finder_id'] = $request->pf_id;
                $portal_ids['pf_path'] = $request->pf_path;
                $location->portal_ids = json_encode($portal_ids);
                $location->save();
            }

            $result = array('status' => '1', 'msg' => 'Community property added successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }

    public function datatable(Request $request)
    {
        $columns = array(
            0 => 'updated_at',
            1 => 'id',
            2 => 'name',
            3 => 'alternate_name',
            4 => 'region_id',
            5 => 'community_id',
            6 => 'sub_community_id',
            7 => 'updated_by_id',
            8 => 'updated_at',
            9 => 'created_by_id',
            10 => 'created_at',
            11 => 'status',
        );
        $searchKeyWord = htmlspecialchars($request['search']['value']);
        $totalData = CommunityProperty::count();
        $totalFiltered = $totalData;

        // if (!empty($searchKeyWord)) {
        //     $data = CommunityProperty::where(function ($q) use ($searchKeyWord) {
        //         $q->where('name', 'LIKE', '%'.$searchKeyWord.'%');
        //     })->with('createdBy', 'updatedBy', 'region', "community", "subCommunity")
        //     ->limit($request['length'])->get();
        //     $totalFiltered = $data->count();
        // } else {
        //     $data = CommunityProperty::orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
        //         ->offset($request['start'])
        //         ->limit($request['length'])
        //         ->with('createdBy', 'updatedBy', 'region', "community", "subCommunity")

        //         ->get();
        //         $totalFiltered = $data->count();
        // }

        if (!empty($searchKeyWord)) {
            $data_filtered = CommunityProperty::where(function ($q) use ($searchKeyWord) {
                $q->where('name', 'LIKE', '%' . $searchKeyWord . '%');
                $q->orWhere('alternate_name', 'LIKE', '%' . $searchKeyWord . '%');
            })->when($request->search_communities, function ($q) use ($request) {

                if (gettype($request->search_communities) == 'string') {
                    $request->search_communities = array($request->search_communities);
                }
                $q->whereIn('community_id', $request->search_communities);
            })
                ->when($request->search_sub_communities, function ($q) use ($request) {
                    if (gettype($request->search_sub_communities) == 'string') {
                        $request->search_sub_communities = array($request->search_sub_communities);
                    }
                    $q->whereIn('sub_community_id', $request->search_sub_communities);
                })
                ->when($request->search_community_property, function ($q) use ($request) {
                    $q->where('name', 'LIKE', '%' . $request->search_community_property . '%')->orWhere('alternate_name', 'LIKE', '%' . $request->search_community_property . '%');
                })->with('createdBy', 'updatedBy', 'region', "community", "subCommunity");

            $totalFiltered = $data_filtered->count();
            $data = $data_filtered->limit($request['length'])->get();
        } else {
            $data_filtered = CommunityProperty::when($request->search_communities, function ($q) use ($request) {
                if (gettype($request->search_communities) == 'string') {
                    $request->search_communities = array($request->search_communities);
                }
                $q->whereIn('community_id', $request->search_communities);
            })
                ->when($request->search_sub_communities, function ($q) use ($request) {
                    if (gettype($request->search_sub_communities) == 'string') {
                        $request->search_sub_communities = array($request->search_sub_communities);
                    }
                    $q->whereIn('sub_community_id', $request->search_sub_communities);
                })
                ->when($request->search_community_property, function ($q) use ($request) {
                    $q->where('name', 'LIKE', '%' . $request->search_community_property . '%')->orWhere('alternate_name', 'LIKE', '%' . $request->search_community_property . '%');
                })
                ->orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
                ->with('createdBy', 'updatedBy', 'region', "community", "subCommunity");
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

    public function getCommunityProperty(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:community_properties,id',
        ]);
        try {
            $cat = CommunityProperty::find($request->id);
            $result = array('status' => '1', 'msg' => 'Community property details fetched successfully', 'data' => $cat);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            "id" => "required|exists:community_properties,id",
            "region_id" => "required|exists:regions,id",
            "community_id" => "required|exists:communities,id",
            "sub_community_id" => "required|exists:sub_communities,id",
            "name" => "required|string",
            "alternate_name" => "nullable|string",
            "description" => "nullable|string",
            "path_name" => "required|string",
            "alternate_path_name" => "nullable|string",
            "latitude" => "required|numeric|between:-90,90",
            "longitude" => "required|numeric|between:-180,180",
            "status" => "nullable",
            "default_use" => "nullable",
            "alternate_use" => "nullable",
        ]);

        try {

            $validatedData["slug"] = Str::slug($request->name, "-");
            $validatedData["status"] = $request->boolean("status");
            $validatedData["alternate_use"] = $request->boolean("alternate_use");
            $validatedData["default_use"] = $request->boolean("default_use");
            $validatedData["updated_by_id"] = auth()->id();

            $community = CommunityProperty::find($request->id)->update($validatedData);
            $result = array('status' => '1', 'msg' => 'Community property updated successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:community_properties',
            'action' => 'required',
        ]);

        $status = CommunityProperty::find($request->id);
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
            'id' => 'required|exists:community_properties',
        ]);

        $status = CommunityProperty::find($request->id);
        if ($status->delete()) {
            $result = array('status' => '1', 'msg' => 'Community property deleted successfully');
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
            if (CommunityProperty::whereIn('id', $ids)->delete()) {
                $result = array('status' => '1', 'msg' => count($ids) . ' Community properties deleted successfully');
            } else {
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }
        }
        return json_encode($result);
    }
}
