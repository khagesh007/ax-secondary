<?php

namespace App\Http\Controllers\Admin\Lead;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\LeadSource;
use Auth;
use Illuminate\Support\Facades\Gate;

class SourceController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies("Beds List"), 401);
        return view('admin.lead.source');
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

            $obj = new LeadSource();
            $obj->name = $request->name;
            $obj->status = $request->status == 'on' ? 1 : 0;
            $obj->created_by_id = Auth::user()->id;
            $obj->save();
            $result = array('status' => '1', 'msg' => 'Lead source added successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }

    public function sourceForDatatable(Request $request)
    {
        $columns = array(
            0 => 'id',
            1 => 'id',
            2 => 'name'
        );
        $searchKeyWord = htmlspecialchars($request['search']['value']);
        $totalData = LeadSource::count();
        $totalFiltered = $totalData;

        if (!empty($searchKeyWord)) {
            $data = LeadSource::where(function ($q) use ($searchKeyWord) {
                $q->where('name', 'LIKE', '%'.$searchKeyWord.'%');
            })->with('created_by', 'updated_by')->get();
        } else {
            $data = LeadSource::orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
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


    public function getSource(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:lead_sources,id',
        ]);
        try {
            $cat = LeadSource::find($request->id);
            $result = array('status' => '1', 'msg' => 'Lead source details fetched successfully', 'data' => $cat);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }


    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:lead_sources,id',
            'name' => 'required|string',
        ]);

        try {
            $source = LeadSource::find($request->id);
            $source->name = $request->name;
            $source->status = $request->status == 'on' ? 1 : 0;
            $source->updated_by_id = auth()->user()->id;
            $source->save();
            $result = array('status' => '1', 'msg' => 'Lead source details updated successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:lead_sources',
            'action' => 'required',
        ]);

        $type = LeadSource::find($request->id);
        $type->status = $request->boolean('action');
        $type->updated_by_id = auth()->user()->id;
        if ($type->save()) {
            $result = array('status' => '1', 'msg' => 'Lead source status updated successfully');
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:lead_sources',

        ]);

        $type = LeadSource::find($request->id);
        if ($type->delete()) {
            $result = array('status' => '1', 'msg' => 'Lead source deleted successfully');
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
            if (LeadSource::whereIn('id', $ids)->delete()) {
                $result = array('status' => '1', 'msg' => count($ids).' Lead source deleted successfully');
            } else {
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }

        }

        return json_encode($result);
    }
}
