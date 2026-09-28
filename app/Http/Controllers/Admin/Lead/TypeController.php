<?php

namespace App\Http\Controllers\Admin\Lead;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\LeadType;
use Auth;
use Illuminate\Support\Facades\Gate;

class TypeController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies("Lead Types List"), 401);
        return view('admin.lead.type');
    }

    public function store(Request $request){

        $validator = \Validator::make($request->all(), [
            'name' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => '2', 'errors' => $validator->errors()]);
        }

        try {

            $obj                     = new LeadType();
            $obj->name               = $request->name;
            $obj->status             = $request->status == 'on' ? 1 : 0;
            $obj->created_by_id      = Auth::user()->id;
            $obj->save();
            $result = array('status' => '1', 'msg' => 'Lead type added successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }

    public function typeForDatatable(Request $request)
    {
        $columns = array(
            0 => 'id',
            1 => 'id',
            2 => 'name'
        );
        $searchKeyWord = htmlspecialchars($request['search']['value']);
        $totalData = LeadType::count();
        $totalFiltered = $totalData;

        if (!empty($searchKeyWord)) {
            $data = LeadType::where(function ($q) use ($searchKeyWord) {
                $q->where('name', 'LIKE', '%' . $searchKeyWord . '%');
            })->with('created_by','updated_by')->get();
        } else {
            $data = LeadType::orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
                ->offset($request['start'])
                ->limit($request['length'])
                ->with('created_by', 'updated_by')
                ->get();
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
            'id' => 'required|exists:lead_types,id',
        ]);
        try {
            $cat = LeadType::find($request->id);
            $result = array('status' => '1', 'msg' => 'Lead type details fetched successfully', 'data' => $cat);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }


    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:lead_types,id',
            'name' => 'required|string',
        ]);

        try {
            $type = LeadType::find($request->id);
            $type->name = $request->name;
            $type->status = $request->status == 'on' ? 1 : 0;
            $type->updated_by_id = auth()->user()->id;
            $type->save();
            $result = array('status' => '1', 'msg' => 'Lead type details updated successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:lead_types',
            'action' => 'required',
        ]);

        $type = LeadType::find($request->id);
        $type->status = $request->boolean('action');
        $type->updated_by_id = auth()->user()->id;
        if ($type->save()) {
            $result = array('status' => '1', 'msg' => 'Lead type updated successfully');
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:lead_types',

        ]);

        $type = LeadType::find($request->id);
        if ($type->delete()) {
            $result = array('status' => '1', 'msg' => ' Lead type deleted successfully');
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
        if(gettype($request->ids) == 'array'  && count($request->ids) > 0){
            $ids = $request->ids;
            if(LeadType::whereIn('id', $ids)->delete()){
                $result = array('status' => '1', 'msg' => count($ids).' Lead type deleted successfully');
            } else {
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }

        }

        return json_encode($result);
    }
}
