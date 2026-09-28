<?php

namespace App\Http\Controllers\Admin\Event;

use App\Http\Controllers\Controller;
use App\Models\EventType;
use App\Traits\FileTrait;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class EventTypeController extends Controller
{
    use FileTrait;

    public function index()
    {
      //  abort_if(Gate::denies("Event Type List"), 401);
        return view('admin.events.type');
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

            $type = new EventType();
            $type->name = $request->name;
            $type->badge = $request->status_badge;
            $type->order = $request->order??0;
            $type->icon = $request->icon;
            $type->status = $request->status == 'on' ? 1 : 0;
            $type->created_by_id = auth()->user()->id;

            if ($request->has('thumbnail')) {
                $image = $this->uploadImage($request->file("thumbnail"));
                $type->thumbnail = $image;
            }
            if ($request->has('image')) {
                $image = $this->uploadImage($request->file("image"));
                $type->image = $image;
            }
            $type->save();

            $result = array('status' => '1', 'msg' => 'Event type added successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => "Error in Event type Controller ".$exception->getMessage());
        }
        return json_encode($result);
    }

    public function datatable(Request $request)
    {
        $columns = array(
            0 => 'id',
            1 => 'id',
            2 => 'name',
            3 => 'order',
        );
        $searchKeyWord = htmlspecialchars($request['search']['value']);
        $totalData = EventType::count();
        $totalFiltered = $totalData;


        if (!empty($searchKeyWord)) {
            $data_filtered = EventType::where(function ($q) use ($searchKeyWord) {
                $q->where('name', 'LIKE', '%' . $searchKeyWord . '%');
            })->with('created_by', 'updated_by');

            $totalFiltered = $data_filtered->count();
            $data = $data_filtered->limit($request['length'])->get();
        } else {
            $data_filtered = EventType::orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
                ->with('created_by', 'updated_by');
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

    public function getStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:event_types,id',
        ]);
        try {
            $cat = EventType::find($request->id);
            $result = array('status' => '1', 'msg' => 'Event type fetched successfully', 'data' => $cat);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function update(Request $request)
    {
        //  dd($request->all());
        $request->validate([
            'id' => 'required|exists:event_types,id',
            'name' => 'required|string',
            'icon' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'order' => 'nullable|string',
            'status_badge' => 'nullable|string',

        ]);

        try {
            $type = EventType::find($request->id);
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
            $result = array('status' => '1', 'msg' => 'Event type details updated successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:event_types,id',
            'action' => 'required',
        ]);

        $status = EventType::find($request->id);
        $status->status = $request->boolean('action');
        $status->updated_by_id = auth()->user()->id;
        if ($status->save()) {
            $result = array('status' => '1', 'msg' => 'Event type updated successfully');
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:event_types,id',

        ]);

        $type = EventType::find($request->id);
        if ($type->delete()) {
            $result = array('status' => '1', 'msg' => 'Event type deleted successfully');
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function deleteMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|exists:event_types,id',

        ]);
        if (gettype($request->ids) == 'array' && count($request->ids) > 0) {
            $ids = $request->ids;
            if (EventType::whereIn('id', $ids)->delete()) {
                $result = array('status' => '1', 'msg' => count($ids) . ' Event types deleted successfully');
            } else {
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }

        }

        return json_encode($result);
    }
    public function getAllTypes(){
        $types = EventType::where('status',1)->get();
        if (!empty($types)) {
            $result = array('status' => '1', 'msg' => ' Event types fetched successfully','data'=>$types);
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return response()->json($result);
    }
}
