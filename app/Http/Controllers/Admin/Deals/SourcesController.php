<?php

namespace App\Http\Controllers\Admin\Deals;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DealSource;
use Auth;
use Illuminate\Support\Facades\Gate;

class SourcesController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies("Deal Sources List"), 401);
        return view('admin.deals.source');
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

            $obj = new DealSource();
            $obj->name = $request->name;
            $obj->status = $request->status == 'on' ? 1 : 0;
            $obj->created_by_id = Auth::user()->id;
            $obj->save();
            $result = array('status' => '1', 'msg' => 'Deals source added successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }

    public function sourceForDatatable(Request $request)
    {
        $columns = array(
            0 => 'updated_at',
            1 => 'id',
            2 => 'name'

        );
        $searchKeyWord = htmlspecialchars($request['search']['value']);
        $totalData = DealSource::count();
        $totalFiltered = $totalData;

       


        if (!empty($searchKeyWord)) {
            $data_filtered = DealSource::where(function ($q) use ($searchKeyWord) {
                $q->where('name', 'LIKE', '%' . $searchKeyWord . '%');
            })->with('created_by', 'updated_by');

            $totalFiltered = $data_filtered->count();
            $data = $data_filtered->limit($request['length'])->get();
        } else {
            $data_filtered = DealSource::orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
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

    public function getSource(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:deal_sources,id',
        ]);
        try {
            $cat = DealSource::find($request->id);
            $result = array('status' => '1', 'msg' => 'Source details fetched successfully', 'data' => $cat);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:deal_sources,id',
            'name' => 'required|string',
        ]);

        try {
            $cat = DealSource::find($request->id);
            $cat->name = $request->name;
            $cat->status = $request->status == 'on' ? 1 : 0;
            $cat->updated_by_id = auth()->user()->id;
            $cat->save();
            $result = array('status' => '1', 'msg' => 'Deals source details updated successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:deal_sources',
            'action' => 'required',
        ]);

        $status = DealSource::find($request->id);
        $status->status = $request->boolean('action');
        $status->updated_by_id = auth()->user()->id;
        if ($status->save()) {
            $result = array('status' => '1', 'msg' => ' source updated successfully');
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:deal_sources',

        ]);

        $status = DealSource::find($request->id);
        if ($status->delete()) {
            $result = array('status' => '1', 'msg' => 'sources deleted successfully');
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
            if (DealSource::whereIn('id', $ids)->delete()) {
                $result = array('status' => '1', 'msg' => count($ids).' Sources deleted successfully');
            } else {
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }

        }

        return json_encode($result);
    }

}
