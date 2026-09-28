<?php

namespace App\Http\Controllers\Admin\Deals;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DealType;
use Auth;
use Illuminate\Support\Facades\Gate;

class TypesController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies("Deal Types List"), 401);
        return view('admin.deals.types');
    }

    public function store(Request $request)
    {

        $validator = \Validator::make($request->all(), [
            'name' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => '2', 'errors' => $validator->errors()]);
        }

        try {

            $obj = new DealType();
            $obj->name = $request->name;
            $obj->status = $request->status == 'on' ? 1 : 0;
            $obj->created_by_id = Auth::user()->id;
            $obj->save();
            $result = array('status' => '1', 'msg' => 'Deal type added successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }

    public function typeForDatatable(Request $request)
    {
        $columns = array(
            0 => 'updated_at',
            1 => 'id',
            2 => 'name'
        );
        $searchKeyWord = htmlspecialchars($request['search']['value']);
        $totalData = DealType::count();
        $totalFiltered = $totalData;

      

        if (!empty($searchKeyWord)) {
            $data_filtered = DealType::where(function ($q) use ($searchKeyWord) {
                $q->where('name', 'LIKE', '%' . $searchKeyWord . '%');
            })->with('created_by', 'updated_by');

            $totalFiltered = $data_filtered->count();
            $data = $data_filtered->limit($request['length'])->get();
        } else {
            $data_filtered = DealType::orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
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


    public function getType(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:deal_types,id',
        ]);
        try {
            $cat = DealType::find($request->id);
            $result = array('status' => '1', 'msg' => 'Deal type details fetched successfully', 'data' => $cat);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }


    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:deal_types,id',
            'name' => 'required|string',
        ]);

        try {
            $type = DealType::find($request->id);
            $type->name = $request->name;
            $type->status = $request->status == 'on' ? 1 : 0;
            $type->updated_by_id = auth()->user()->id;
            $type->save();
            $result = array('status' => '1', 'msg' => 'Deal type details updated successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:deal_types',
            'action' => 'required',
        ]);

        $status = DealType::find($request->id);
        $status->status = $request->boolean('action');
        $status->updated_by_id = auth()->user()->id;
        if ($status->save()) {
            $result = array('status' => '1', 'msg' => 'Deal type updated successfully');
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:deal_types',

        ]);

        $type = DealType::find($request->id);
        if ($type->delete()) {
            $result = array('status' => '1', 'msg' => 'Deal type deleted successfully');
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
            if (DealType::whereIn('id', $ids)->delete()) {
                $result = array('status' => '1', 'msg' => count($ids).' Deal type deleted successfully');
            } else {
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }

        }

        return json_encode($result);
    }

}

