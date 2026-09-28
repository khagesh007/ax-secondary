<?php

namespace App\Http\Controllers\Admin\Location;

use App\Models\Region;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use DataTables;
class RegionController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies("Regions List"), 401);
        return view('admin.location.region');
    }

    public function store(Request $request)
    {
        $request->validate([
            "name" => "required|string",
            "status" => "nullable",
        ]);

        try {
            $category = Region::create([
                "name" => $request->name,
                "status" => $request->boolean("status"),
                "created_by_id" => auth()->id(),
            ]);
            $result = array('status' => '1', 'msg' => 'Region added successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }

    public function regionForDatatable(Request $request)
    {
       

        $query = Region::select('regions.*');
            

            return DataTables::of(($query)->with('created_by', 'updated_by'))
            ->make(true);

        // dd($request->all());
        $columns = array(
            0 => 'updated_at',
            1 => 'id',
            2 => 'name',
            3 => 'updated_by_id',
            4 => 'updated_at',
            5 => 'created_by_id',
            6 => 'created_at',
            7 => 'status',
        );
        $searchKeyWord = htmlspecialchars($request['search']['value']);
        $totalData = Region::count();
        $totalFiltered = $totalData;

        

        

        if (!empty($searchKeyWord)) {
            $data_filtered = Region::where(function ($q) use ($searchKeyWord) {
                $q->where('name', 'LIKE', '%' . $searchKeyWord . '%');
            })->with('created_by', 'updated_by');

            $totalFiltered = $data_filtered->count();
            $data = $data_filtered->limit($request['length'])->get();
        } else {
            $data_filtered = Region::orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
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

    public function getRegion(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:regions,id',
        ]);
        try {
            $cat = Region::find($request->id);
            $result = array('status' => '1', 'msg' => 'Region details fetched successfully', 'data' => $cat);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:regions,id',
            "name" => "required|string",
            "status" => "nullable",
        ]);

        try {
            $category = Region::find($request->id);
            $category->update([
                "name" => $request->name,
                "status" => $request->boolean("status"),
                "updated_by_id" => auth()->id(),
            ]);
            $result = array('status' => '1', 'msg' => 'Region details updated successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:regions',
            'action' => 'required',
        ]);

        $status = Region::find($request->id);
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
            'id' => 'required|exists:regions',
        ]);

        $status = Region::find($request->id);
        if ($status->delete()) {
            $result = array('status' => '1', 'msg' => 'Region deleted successfully');
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
            if (Region::whereIn('id', $ids)->delete()) {
                $result = array('status' => '1', 'msg' => count($ids).' Regions deleted successfully');
            } else {
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }
        }
        return json_encode($result);
    }
}
