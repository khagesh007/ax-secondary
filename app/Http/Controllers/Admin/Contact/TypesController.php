<?php

namespace App\Http\Controllers\Admin\Contact;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactType;
use Auth;
use Illuminate\Support\Facades\Gate;

class TypesController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies("Contact Types List"), 401);
        return view('admin.contact.types');
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

            $obj = new ContactType();
            $obj->name = $request->name;
            $obj->status = $request->status == 'on' ? 1 : 0;
            $obj->created_by_id = Auth::user()->id;
            $obj->save();
            $result = array('status' => '1', 'msg' => 'Contact type added successfully');
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
        $totalData = ContactType::count();
        $totalFiltered = $totalData;

        if (!empty($searchKeyWord)) {
            $data = ContactType::where(function ($q) use ($searchKeyWord) {
                $q->where('name', 'LIKE', '%'.$searchKeyWord.'%');
            })->with('created_by', 'updated_by')->get();
        } else {
            $data = ContactType::orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
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
            'id' => 'required|exists:contact_types,id',
        ]);
        try {
            $cat = ContactType::find($request->id);
            $result = array('status' => '1', 'msg' => 'Contact type details fetched successfully', 'data' => $cat);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }


    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:contact_types,id',
            'name' => 'required|string',
        ]);

        try {
            $type = ContactType::find($request->id);
            $type->name = $request->name;
            $type->status = $request->status == 'on' ? 1 : 0;
            $type->updated_by_id = auth()->user()->id;
            $type->save();
            $result = array('status' => '1', 'msg' => 'Contact type details updated successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:contact_types',
            'action' => 'required',
        ]);

        $status = ContactType::find($request->id);
        $status->status = $request->boolean('action');
        $status->updated_by_id = auth()->user()->id;
        if ($status->save()) {
            $result = array('status' => '1', 'msg' => 'Contact type updated successfully');
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:contact_types',

        ]);

        $type = ContactType::find($request->id);
        if ($type->delete()) {
            $result = array('status' => '1', 'msg' => 'Contact type deleted successfully');
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
            if (ContactType::whereIn('id', $ids)->delete()) {
                $result = array('status' => '1', 'msg' => count($ids).' Contact type deleted successfully');
            } else {
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }

        }

        return json_encode($result);
    }

}
