<?php

namespace App\Http\Controllers\Admin\Location;

use App\Http\Controllers\Controller;
use App\Models\Region;
use App\Models\SubCommunity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class SubCommunityController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies("Sub Communities List"), 401);
        $regions = Region::where("status", true)->get(["id", "name"]);
        return view('admin.location.sub-communities', compact("regions"));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            "region_id" => "required|exists:regions,id",
            "community_id" => "required|exists:communities,id",
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
            SubCommunity::create($validatedData);
            $result = array('status' => '1', 'msg' => 'Sub-Community added successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }

    public function subCommunityForDatatable(Request $request)
    {
        // dd($request->all());
        $columns = array(
            0 => 'updated_at',
            1 => 'id',
            2 => 'name',
            3 => 'alternate_name',
            4 => 'community_id',
            5 => 'updated_by_id',
            6 => 'updated_at',
            7 => 'created_by_id',
            8 => 'created_at',
            9 => 'status',
        );
        $searchKeyWord = htmlspecialchars($request['search']['value']);
        $totalData = SubCommunity::count();
        $totalFiltered = $totalData;

        if (!empty($searchKeyWord)) {
            // dd('if');
            $data_filtered = SubCommunity::where(function ($q) use ($searchKeyWord) {
                $q->where('name', 'LIKE', '%' . $searchKeyWord . '%');
                $q->orwhere('alternate_name', 'LIKE', '%' . $searchKeyWord . '%');
            })
                ->when($request->search_communities, function ($q) use ($request) {

                    if (gettype($request->search_communities) == 'string') {
                        $request->search_communities = array($request->search_communities);
                    }

                    $q->whereIn('community_id', $request->search_communities);
                })

                ->when($request->search_sub_communities, function ($q) use ($request) {
                    $q->where('name', 'LIKE','%'.$request->search_sub_communities.'%')->orWhere('alternate_name', 'LIKE', '%'.$request->search_sub_communities.'%');
                })

                ->with('createdBy', 'updatedBy', 'region', "community");

            $totalFiltered = $data_filtered->count();
            $data = $data_filtered->limit($request['length'])->get();
        } else {
            // dd('else');
            $data_filtered = SubCommunity::when($request->search_communities, function ($q) use ($request) {
                if (gettype($request->search_communities) == 'string') {
                    $request->search_communities = array($request->search_communities);
                }
                $q->whereIn('community_id', $request->search_communities);
            })

            ->when($request->search_sub_communities, function ($q) use ($request) {
                $q->where('name', 'LIKE', '%'.$request->search_sub_communities.'%')->orWhere('alternate_name', 'LIKE', '%'.$request->search_sub_communities.'%');
            })
            ->orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
                ->with('createdBy', 'updatedBy', 'region', "community");

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

    public function getSubCommunity(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:sub_communities,id',
        ]);
        try {
            $cat = SubCommunity::find($request->id);
            $result = array('status' => '1', 'msg' => 'Sub-Community details fetched successfully', 'data' => $cat);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            "id" => "required|exists:sub_communities,id",
            "region_id" => "required|exists:regions,id",
            "community_id" => "required|exists:communities,id",
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

            $community = SubCommunity::find($request->id)->update($validatedData);
            $result = array('status' => '1', 'msg' => 'Sub-Community updated successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:sub_communities',
            'action' => 'required',
        ]);

        $status = SubCommunity::find($request->id);
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
            'id' => 'required|exists:sub_communities',
        ]);

        $status = SubCommunity::find($request->id);
        if ($status->delete()) {
            $result = array('status' => '1', 'msg' => 'Sub-Community deleted successfully');
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
            if (SubCommunity::whereIn('id', $ids)->delete()) {
                $result = array('status' => '1', 'msg' => count($ids) . ' Sub-Communities deleted successfully');
            } else {
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }
        }
        return json_encode($result);
    }
}
