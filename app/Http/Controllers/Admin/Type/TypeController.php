<?php

namespace App\Http\Controllers\Admin\Type;

use App\Http\Controllers\Controller;
use App\Models\Type;
use App\Traits\FileTrait;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Response;

class TypeController extends Controller
{

    use FileTrait;

    public function index()
    {
        abort_if(Gate::denies("Contact Sources List"), 401);
        return view('admin.type.index');
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
            $obj = new Type();
            $obj->name = $request->name;
            $obj->order = $request->order??0;
            $obj->icon = $request->icon;
            $obj->badge = $request->status_badge;
            $obj->status = $request->status == 'on' ? 1 : 0;
            $obj->is_contact_type = $request->is_contact_type == 'on' ? 1 : 0;
            $obj->is_lead_type = $request->is_lead_type == 'on' ? 1 : 0;
            $obj->created_by_id = Auth::user()->id;
            
            if ($request->has('thumbnail')) {
                $image = $this->uploadImage($request->file("thumbnail"));
                $obj->thumbnail = $image;
            }
            if ($request->has('image')) {
                $image = $this->uploadImage($request->file("image"));
                $obj->image = $image;
            }

            $obj->save();
            $result = array('status' => '1', 'msg' => 'Type added successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }

    public function sourceForDatatable(Request $request)
    {

        // dd($request->all());
        $columns = array(
            0 => 'updated_at',
            1 => 'id',
            2 => 'name',
            3 => 'thumbnail',

        );
        $searchKeyWord = htmlspecialchars($request['search']['value']);
        $totalData = Type::count();
        $totalFiltered = $totalData;

        // if (!empty($searchKeyWord)) {
        //     $data_filtered = Type::where(function ($q) use ($searchKeyWord) {
        //         $q->where('name', 'LIKE', '%' . $searchKeyWord . '%');
        //     })->with('created_by', 'updated_by');

        //     $totalFiltered = $data_filtered->count();
        //     $data = $data_filtered->limit($request['length'])->get();
        // } else {
        //     $data_filtered = Type::orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
        //         ->offset($request['start'])
        //         ->with('created_by', 'updated_by');

        //         $totalFiltered = $data_filtered->count();
        //         $data = $data_filtered->limit($request['length'])->offset($request['start'])->get();

        // }

        if (!empty($searchKeyWord)) {
            $data_filtered = Type::where(function ($q) use ($searchKeyWord) {
                $q->where('name', 'LIKE', '%' . $searchKeyWord . '%');
            })->with('created_by', 'updated_by');

            $totalFiltered = $data_filtered->count();
            $data = $data_filtered->limit($request['length'])->get();
        } else {
            $data_filtered = Type::orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
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
        $totalData = Type::count();
        $totalFiltered = $totalData;

        if (!empty($searchKeyWord)) {
            $data = Source::where('is_lead_type', 1)->where(function ($q) use ($searchKeyWord) {
                $q->where('name', 'LIKE', '%' . $searchKeyWord . '%');
            })->with('created_by', 'updated_by')->get();
        } else {
            $data = Source::where('is_lead_type', 1)->orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
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
        $totalData = Type::count();
        $totalFiltered = $totalData;

        if (!empty($searchKeyWord)) {
            $data = Type::where('is_contact_type', 1)->where(function ($q) use ($searchKeyWord) {
                $q->where('name', 'LIKE', '%' . $searchKeyWord . '%');
            })->with('created_by', 'updated_by')->get();
        } else {
            $data = Type::where('is_contact_type', 1)->orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
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
            'id' => 'required|exists:types,id',
        ]);
        try {
            $cat = Type::find($request->id);
            $result = array('status' => '1', 'msg' => 'Type details fetched successfully', 'data' => $cat);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:types,id',
            'name' => 'required|string',
            'icon' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'order' => 'nullable|string',
            'status_badge' => 'nullable|string'
            ]);

        try {
           
            $cat = Type::find($request->id);
            $cat->name = $request->name;
            $cat->icon = $request->icon;
            $cat->badge = $request->status_badge;
            $cat->status = $request->status == 'on' ? 1 : 0;
            $cat->is_lead_type = $request->is_lead_type == 'on' ? 1 : 0;
            $cat->is_contact_type = $request->is_contact_type == 'on' ? 1 : 0;
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
            $result = array('status' => '1', 'msg' => 'Type details updated successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }
    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:types',
            'action' => 'required',
        ]);

        $status = Type::find($request->id);
        $status->status = $request->boolean('action');
        $status->updated_by_id = auth()->user()->id;
        if ($status->save()) {
            $result = array('status' => '1', 'msg' => 'Type Status updated successfully');
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }
    public function updateLeadStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:types',
            'action' => 'required',
        ]);

        $status = Type::find($request->id);
        $status->is_lead_type = $request->boolean('action');
        $status->updated_by_id = auth()->user()->id;
        if ($status->save()) {
            $result = array('status' => '1', 'msg' => ' Lead Status successfully');
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function updateContactStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:types',
            'action' => 'required',
        ]);

        $status = Type::find($request->id);
        $status->is_contact_type = $request->boolean('action');
        $status->updated_by_id = auth()->user()->id;
        if ($status->save()) {
            $result = array('status' => '1', 'msg' => 'Type Contact Status successfully');
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:types',

        ]);

        $status = Type::find($request->id);
        if ($status->leads->count() != 0) {
            $result = array('status' => '0', 'msg' => 'Cannot Delete this Type, it is used in Leads');
            return json_encode($result);
        }

        if ($status->delete()) {
            $result = array('status' => '1', 'msg' => 'Type deleted successfully');
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
        // $count_relation_data = [];
        if (gettype($request->ids) == 'array' && count($request->ids) > 0) {
            $ids = $request->ids;
            // foreach ($request->ids as $id) {
            //     $type = Type::find($id);
            //     $relationships = $type->relationships;
            //     if ($relationships) {

            //         foreach ($relationships as $key => $relation) {
            //             if ($type->$relation()->count() == 0) {
            //                 continue;
            //             }
            //             $count_relation_data[$key] = $type->$relation()->count();
            //         }
            //         if (count($count_relation_data) > 0) {
            //             return Response::json(['error_related_data' => $count_relation_data, 201]);

            //         }
            //     }
            // }

            if (Type::whereIn('id', $ids)->delete()) {
                $result = array('status' => '1', 'msg' => count($ids) . ' Type deleted successfully');
            } else {
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }

        }

        return json_encode($result);
    }

    public function delete_listing_hard(Request $request)
    {
        // dd($request->all());

        // if (\Auth::user()->hasPermissionTo('Delete Rent listing'))
        // {
        $model = $request->model;
        request()->validate(['type_id' => 'required']);
        $type_id = $request->get('type_id');
        $data = $model::where('id', $type_id)->first();
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
