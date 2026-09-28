<?php

namespace App\Http\Controllers\Admin\Property;

use App\Traits\FileTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PropertyType;
use Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class PropertyTypeController extends Controller
{
    use FileTrait;

    public function index()
    {
        abort_if(Gate::denies("Property Types List"), 401);
        return view('admin.property.type');
    }

    public function store(Request $request)
    {
        $request->validate([
            "title" => "required|string",
            'thumbnail' => 'nullable|mimes:jpg,jpeg,png',
            'image' => 'nullable|mimes:jpg,jpeg,png',
            'color_code' => 'nullable|string',
        ]);

        try {
            $thumbnail = null;
            $image = null;
            if ($request->hasFile("thumbnail")) {
                $thumbnail = $this->uploadImage(file: $request->file("thumbnail"), prefix: "thumb");
            }

            if ($request->hasFile("image")) {
                $image = $this->uploadImage($request->file("image"));
            }

            $category = PropertyType::create([
                "title" => $request->title,
                'slug' => Str::slug($request->title, "-"),
                "color_code" => $request->color_code,
                'thumbnail' => $thumbnail,
                'image' => $image,
                "status" => $request->boolean("status"),
                "created_by_id" => auth()->id(),
            ]);
            $result = array('status' => '1', 'msg' => 'Property type added successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }

    public function typesForDatatable(Request $request)
    {
        $columns = array(
            0 => 'updated_at',
            1 => 'id',
            2 => 'title'
        );
        $searchKeyWord = htmlspecialchars($request['search']['value']);
        $totalData = PropertyType::count();
        $totalFiltered = $totalData;

       

        if (!empty($searchKeyWord)) {
            $data_filtered = PropertyType::where(function ($q) use ($searchKeyWord) {
                $q->where('title', 'LIKE', '%' . $searchKeyWord . '%');
            })->with('created_by', 'updated_by');

            $totalFiltered = $data_filtered->count();
            $data = $data_filtered->limit($request['length'])->get();
        } else {
            $data_filtered = PropertyType::orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
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
            'id' => 'required|exists:property_types,id',
        ]);
        try {
            $cat = PropertyType::find($request->id);
            $result = array('status' => '1', 'msg' => 'Type details fetched successfully', 'data' => $cat);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }


    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:property_types,id',
            "title" => "required|string",
            'thumbnail' => 'nullable|mimes:jpg,jpeg,png',
            'image' => 'nullable|mimes:jpg,jpeg,png',
            'color_code' => 'nullable|string',
        ]);

        try {
            $category = PropertyType::find($request->id);

            $thumbnail = $category->thumbnail;
            $image = $category->image;
            if ($request->hasFile("thumbnail")) {
                $thumbnail = $this->uploadImage(file: $request->file("thumbnail"), prefix: "thumb");
            }

            if ($request->hasFile("image")) {
                $image = $this->uploadImage($request->file("image"));
            }
            $category->update([
                "title" => $request->title,
                'slug' => Str::slug($request->title, "-"),
                "color_code" => $request->color_code,
                'thumbnail' => $thumbnail,
                'image' => $image,
                "status" => $request->boolean("status"),
                "updated_by_id" => auth()->id(),
            ]);
            $result = array('status' => '1', 'msg' => 'Property type details updated successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:property_types',
            'action' => 'required',
        ]);

        $type = PropertyType::find($request->id);
        $type->status = $request->boolean('action');
        $type->updated_by_id = auth()->user()->id;
        if ($type->save()) {
            $result = array('status' => '1', 'msg' => 'Type status updated successfully');
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:property_types',

        ]);

        $type = PropertyType::find($request->id);
        if ($type->delete()) {
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
        if (gettype($request->ids) == 'array' && count($request->ids) > 0) {
            $ids = $request->ids;
            if (PropertyType::whereIn('id', $ids)->delete()) {
                $result = array('status' => '1', 'msg' => count($ids).' Type deleted successfully');
            } else {
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }
        }
        return json_encode($result);
    }
}
