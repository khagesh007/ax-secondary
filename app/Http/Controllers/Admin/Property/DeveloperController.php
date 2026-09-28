<?php

namespace App\Http\Controllers\Admin\Property;

use App\Models\CallLog;
use App\Models\ContactAgent;
use App\Models\Note;
use App\Traits\CallTrait;
use App\Traits\FiltersTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Contact;
use App\Models\ContactNote;
use App\Models\ContactSource;
use App\Models\ContactStatus;
use App\Models\ContactType;
use App\Models\Country;
use App\Models\Developer;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class DeveloperController extends Controller
{
    use FiltersTrait, CallTrait;

    public function index()
    {
       // abort_if(Gate::denies("Contacts List"), 401);
       $data = array();
        return view('admin.property.developer')->with($data);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            "short_name" => "required|string",
            "full_name" => "required|string",
            "phone" => "nullable|numeric",
            "description" => "nullable|string",
            "contact_person" => "nullable|string",
            "email" => "nullable|email",
            "website" => "nullable|string",
            //"logo" => "nullable|file",
        ]);

        try {
            $validatedData["created_by_id"] = auth()->id();
           $obj = new Developer();
           $obj->short_name = $request->short_name;
           $obj->full_name = $request->full_name;
           $obj->contact_person = $request->contact_person;
           $obj->phone = $request->phone;
           $obj->email = $request->email;
           $obj->website = $request->website;
           $obj->description = $request->description;
           $obj->save();

            return response()->json(['status' => '1', 'msg' => 'Developer Added Successfully', "id" => $obj->id]);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }
    public function update(Request $request)
    {

        $validatedData = $request->validate([
            "id" => "required|exists:developers,id",
            "short_name" => "required|string",
            "full_name" => "required|string",
            "phone" => "nullable|numeric",
            "description" => "nullable|string",
            "contact_person" => "nullable|string",
            "email" => "nullable|email",
            "website" => "nullable|string",
        ]);

        try {

            $obj = Developer::find($request->id);
            $obj->short_name = $request->short_name;
            $obj->full_name = $request->full_name;
            $obj->contact_person = $request->contact_person;
            $obj->phone = $request->phone;
            $obj->email = $request->email;
            $obj->website = $request->website;
            $obj->description = $request->description;
            $obj->save();
            return response()->json(['status' => '1', 'msg' => 'Developer Updated Successfully', "id" => $obj->id]);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }

    public function dataTable(Request $request)
    {
        $columns = array(
            0 => 'updated_at',
            1 => 'id',
            2 => 'short_name',

        );


        $searchKeyWord = htmlspecialchars($request['search']['value']);

        $totalData = Developer::count();
        $totalFiltered = $totalData;



        if (!empty($searchKeyWord)) {
            $data_filtered = Developer::where(function ($q) use ($searchKeyWord) {
                $q->where('full_name', 'LIKE', '%' . $searchKeyWord . '%')
                ->orWhere('email', 'LIKE', '%' . $searchKeyWord . '%')
                ->orWhere('phone', 'LIKE', '%' . $searchKeyWord . '%');
            })->with('created_by', 'updated_by');

            $totalFiltered = $data_filtered->count();
            $data = $data_filtered->limit($request['length'])->get();
        } else {
            $data_filtered = Developer::orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
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
    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:developers',
            'action' => 'required',
        ]);

        $status = Developer::find($request->id);
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
            'id' => 'required|exists:developers',
        ]);
        $status = Developer::find($request->id);

        if ($status->delete()) {
            $result = array('status' => '1', 'msg' => 'Developer deleted successfully');
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }
    public function getData(Request $request)
    {
        $request->validate([
            "id" => "required|exists:developers"
        ]);
        $obj = Developer::where('id', $request->id)->first();
        if (!empty($obj)) {
            return response()->json(['status' => '1', 'msg' => 'Data Found', 'data' => $obj]);
        } else {
            return response()->json(['status' => '0', 'msg' => 'Data Not Found']);
        }
    }
    public function deleteMultiple(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'ids' => 'required|array',

        ]);
        if (gettype($request->ids) == 'array'  && count($request->ids) > 0) {
            $ids = $request->ids;
            if (Developer::whereIn('id', $ids)->delete()) {
                $result = array('status' => '1', 'msg' => count($ids) . ' developer deleted successfully');
            } else {
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }
        }

        return json_encode($result);
    }


}
