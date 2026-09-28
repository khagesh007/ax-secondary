<?php

namespace App\Http\Controllers\Admin\Contact;

use App\Http\Controllers\Controller;
use App\Models\Medium;
use App\Traits\FileTrait;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MediumController extends Controller
{
    use FileTrait;
    public function index()
    {

        abort_if(Gate::denies("Mediums List"), 401);
        return view('admin.contact.medium');
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
            $obj = new Medium();
            $obj->name = $request->name;
            $obj->badge = $request->status_badge;
            $obj->icon = $request->icon;
            $obj->status = $request->status == 'on' ? 1 : 0;
            $obj->is_contact_medium = $request->is_contact_medium == 'on' ? 1 : 0;
            $obj->is_lead_medium = $request->is_lead_medium == 'on' ? 1 : 0;

            $obj->created_by_id = Auth::user()->id;

            $obj->order = $request->order??0;

            if ($request->has('thumbnail')) {
                $image = $this->uploadImage($request->file("thumbnail"));
                $obj->thumbnail = $image;
            }
            if ($request->has('image')) {
                $image = $this->uploadImage($request->file("image"));
                $obj->image = $image;
            }

            $obj->save();
            $result = array('status' => '1', 'msg' => 'Medium added successfully');
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
            2 => 'name',

        );
        $searchKeyWord = htmlspecialchars($request['search']['value']);
        $totalData = Medium::count();
        $totalFiltered = $totalData;

        if (!empty($searchKeyWord)) {
            $data_filtered = Medium::where(function ($q) use ($searchKeyWord) {
                $q->where('name', 'LIKE', '%' . $searchKeyWord . '%');
            })->with('created_by', 'updated_by');

            $totalFiltered = $data_filtered->count();
            $data = $data_filtered->limit($request['length'])->get();
        } else {
            $data_filtered = Medium::orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
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

    public function sourceForLeadsDatatable(Request $request)
    {
        $columns = array(
            0 => 'updated_at',
            1 => 'id',
            2 => 'name',

        );
        $searchKeyWord = htmlspecialchars($request['search']['value']);
        $totalData = Medium::count();
        $totalFiltered = $totalData;

        if (!empty($searchKeyWord)) {
            $data = Medium::where('is_lead_medium', 1)->where(function ($q) use ($searchKeyWord) {
                $q->where('name', 'LIKE', '%' . $searchKeyWord . '%');
            })->with('created_by', 'updated_by')->get();
        } else {
            $data = Medium::where('is_lead_medium', 1)->orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
                ->offset($request['start'])
                ->limit($request['length'])
                ->with('created_by', 'updated_by')
                ->get();
        }

        $json_data = array(
            "draw" => intval($request['draw']),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data,
        );

        echo json_encode($json_data);
    }

    public function sourceForContactsDatatable(Request $request)
    {
        $columns = array(
            0 => 'updated_at',
            1 => 'id',
            2 => 'name',

        );
        $searchKeyWord = htmlspecialchars($request['search']['value']);
        $totalData = Medium::count();
        $totalFiltered = $totalData;

        if (!empty($searchKeyWord)) {
            $data = Medium::where('is_contact_medium', 1)->where(function ($q) use ($searchKeyWord) {
                $q->where('name', 'LIKE', '%' . $searchKeyWord . '%');
            })->with('created_by', 'updated_by')->get();
        } else {
            $data = Medium::where('is_contact_medium', 1)->orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
                ->offset($request['start'])
                ->limit($request['length'])
                ->with('created_by', 'updated_by')
                ->get();
        }

        $json_data = array(
            "draw" => intval($request['draw']),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data,
        );

        echo json_encode($json_data);
    }

    public function getSource(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:mediums,id',
        ]);
        try {
            $cat = Medium::find($request->id);
            $result = array('status' => '1', 'msg' => 'Medium details fetched successfully', 'data' => $cat);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:mediums,id',
            'name' => 'required|string',
            'thumbnail' => 'nullable|mimes:jpg,jpeg,png',
            'image' => 'nullable|mimes:jpg,jpeg,png',
        ]);

        try {
            $cat = Medium::find($request->id);
            $cat->name = $request->name;
            $cat->badge = $request->status_badge;
            $cat->icon = $request->icon;
            $cat->status = $request->status == 'on' ? 1 : 0;
            $cat->is_lead_medium = $request->is_lead_medium == 'on' ? 1 : 0;
            $cat->is_contact_medium = $request->is_contact_medium == 'on' ? 1 : 0;
            $cat->updated_by_id = auth()->user()->id;

            $cat->order = $request->order??0;

            if ($request->has('thumbnail')) {
                $image = $this->uploadImage($request->file("thumbnail"));
                $cat->thumbnail = $image;
            }
            if ($request->has('image')) {
                $image = $this->uploadImage($request->file("image"));
                $cat->image = $image;
            }

            $cat->save();
            $result = array('status' => '1', 'msg' => ' Medium details updated successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }
    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:mediums',
            'action' => 'required',
        ]);

        $status = Medium::find($request->id);
        $status->status = $request->boolean('action');
        $status->updated_by_id = auth()->user()->id;
        if ($status->save()) {
            $result = array('status' => '1', 'msg' => ' Medium updated successfully');
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function updateLeadStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:mediums',
            'action' => 'required',
        ]);

        $status = Medium::find($request->id);
        $status->is_lead_medium = $request->boolean('action');
        $status->updated_by_id = auth()->user()->id;
        if ($status->save()) {
            $result = array('status' => '1', 'msg' => ' Medium Status successfully');
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function updateContactStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:mediums',
            'action' => 'required',
        ]);

        $status = Medium::find($request->id);
        $status->is_contact_medium = $request->boolean('action');
        $status->updated_by_id = auth()->user()->id;
        if ($status->save()) {
            $result = array('status' => '1', 'msg' => 'Contact Status successfully');
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:mediums',

        ]);

        $status = Medium::find($request->id);
        if ($status->delete()) {
            $result = array('status' => '1', 'msg' => 'Medium deleted successfully');
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
            if (Medium::whereIn('id', $ids)->delete()) {
                $result = array('status' => '1', 'msg' => count($ids) . ' Mediums deleted successfully');
            } else {
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }

        }

        return json_encode($result);
    }

    public function deleteThumbnail(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:mediums',
        ]);
        $medium = Medium::find($request->id);
        $medium->thumbnail = null;
        if ($medium->save()) {
            $result = array('status' => '1', 'msg' => 'Thumbnail deleted successfully');
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function delete_listing_hard(Request $request)
    {

        // if (\Auth::user()->hasPermissionTo('Delete Rent listing'))
        // {
        request()->validate(['medium_id' => 'required']);
        $medium_id = $request->get('medium_id');
        $data = Medium::where('id', $medium_id)->first();
        $relationships = $data->relationships;
        if ($relationships) {
            foreach ($relationships as $key => $relation) {
                $data->$relation()->delete();
            }
        }
        $data->delete();
        return Response::json(['message' => 'Type deleted successfully'], 200);

        // }
        // else
        // {
        //     abort(403, 'User does not have the right permissions.');
        // }

    }

}
