<?php

namespace App\Http\Controllers\Admin\Contact;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactStatus;
use Auth;
use Illuminate\Support\Facades\Gate;
use App\Traits\FileTrait;

class StatusController extends Controller
{
    use FileTrait;

    public function index()
    {
        abort_if(Gate::denies("Contact Statuses List"), 401);
        return view('admin.contact.status');
    }

    public function store(Request $request)
    {

        $validator = \Validator::make($request->all(), [
            'name' => 'required|string',
            'icon' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'order' => 'nullable|string',
            'status_badge' => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => '2', 'errors' => $validator->errors()]);
        }
        try {

            $obj = new ContactStatus();
            $obj->name = $request->name;
            $obj->badge = $request->status_badge;
            $obj->icon = $request->icon;
            $obj->order = $request->order??0;
            $obj->status = $request->status == 'on' ? 1 : 0;
            $obj->created_by_id = auth()->user()->id;

            if ($request->has('thumbnail')) {
                $image = $this->uploadImage($request->file("thumbnail"));
                $obj->thumbnail = $image;
            }
            if ($request->has('image')) {
                $image = $this->uploadImage($request->file("image"));
                $obj->image = $image;
            }

            $obj->save();
            $result = array('status' => '1', 'msg' => 'Contact status added successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }

    public function statusForDatatable(Request $request)
    {
        $columns = array(
            0 => 'updated_at',
            1 => 'id',
            2 => 'name'
        );
        $searchKeyWord = htmlspecialchars($request['search']['value']);
        $totalData = ContactStatus::count();
        $totalFiltered = $totalData;

    
        if (!empty($searchKeyWord)) {
            $data_filtered = ContactStatus::where(function ($q) use ($searchKeyWord) {
                $q->where('name', 'LIKE', '%' . $searchKeyWord . '%');
            })->with('created_by', 'updated_by');

            $totalFiltered = $data_filtered->count();
            $data = $data_filtered->limit($request['length'])->get();
        } else {
            $data_filtered = ContactStatus::orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
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


    public function getStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:contact_statuses,id',
        ]);
        try {
            $cat = ContactStatus::find($request->id);
            $result = array('status' => '1', 'msg' => 'Contact status details fetched successfully', 'data' => $cat);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }


    public function update(Request $request)
    {
      
        $request->validate([
            'id' => 'required|exists:contact_statuses,id',
            'name' => 'required|string',
            'icon' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'order' => 'nullable|string',
            'status_badge' => 'nullable|string',
        ]);

        try {
            $type = ContactStatus::find($request->id);
            $type->name = $request->name;
            $type->badge = $request->status_badge;
            $type->order = $request->order??0;
            $type->icon = $request->icon;
            $type->status = $request->status == 'on' ? 1 : 0;
            $type->updated_by_id = auth()->user()->id;

            if ($request->has('thumbnail')) {
                $image = $this->uploadImage($request->file("thumbnail"));
                $type->thumbnail = $image;
            }
            if ($request->has('image')) {
                $image = $this->uploadImage($request->file("image"));
                $type->image = $image;
            }
            $type->save();
            $result = array('status' => '1', 'msg' => 'Contact status details updated successfully');
        } catch (\Exception $exception) {
            // dd($exception->getMessage());
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:contact_statuses',
            'action' => 'required',
        ]);

        $status = ContactStatus::find($request->id);
        $status->status = $request->boolean('action');
        $status->updated_by_id = auth()->user()->id;
        if ($status->save()) {
            $result = array('status' => '1', 'msg' => 'Contact status updated successfully');
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:contact_statuses',

        ]);

        $type = ContactStatus::find($request->id);
        if ($type->delete()) {
            $result = array('status' => '1', 'msg' => 'Contact status deleted successfully');
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
            if (ContactStatus::whereIn('id', $ids)->delete()) {
                $result = array('status' => '1', 'msg' => count($ids).' Contact status deleted successfully');
            } else {
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }

        }

        return json_encode($result);
    }
}
