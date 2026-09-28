<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Models\Portal;
use App\Traits\FileTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PropertyCategory;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class PortalController extends Controller
{
    use FileTrait;

    public function index()
    {
        abort_if(Gate::denies("Portals List"), 401);
        return view('admin.settings.portals.index');
    }

    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string',
            'short_name' => 'required|string',
            'quota' => 'nullable|numeric',
            'token' => 'nullable|string',
            'token' => 'nullable|string',
            'token_abu_dhabi' => 'nullable|string',
            'token_commercial' => 'nullable|string',
            'thumbnail' => 'nullable|mimes:jpg,jpeg,png',
            'image' => 'nullable|mimes:jpg,jpeg,png',
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

            Portal::create([
                'name' => $request->name,
                'short_name' => $request->short_name,
                'slug' => Str::slug($request->name, "-"),
                'xml_url' => Str::random(),
                'quota' => $request->quota,
                'token' => $request->token,
                'token_abu_dhabi' => $request->token_abu_dhabi,
                'token_commercial' => $request->token_commercial,
                'thumbnail' => $thumbnail,
                'image' => $image,
                'status' => $request->boolean("status"),
                'created_by_id' => auth()->id(),
            ]);
            $result = array('status' => '1', 'msg' => 'Portal added successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }

    public function dt(Request $request)
    {
        $columns = array(
            0 => 'updated_at',
            1 => 'id',
            2 => 'name',
            3 => 'short_name',
            4 => 'xml_url',
            5 => 'updated_by_id',
            6 => 'updated_at',
            7 => 'created_by_id',
            8 => 'created_at',
            9 => 'status',
        );
        $searchKeyWord = htmlspecialchars($request['search']['value']);
        $totalData = Portal::count();
        $totalFiltered = $totalData;



        if (!empty($searchKeyWord)) {
            $data_filtered = Portal::where(function ($q) use ($searchKeyWord) {
                $q->where('name', 'LIKE', '%' . $searchKeyWord . '%');
                $q->orWhere('short_name', 'LIKE', '%'.$searchKeyWord.'%');
            })->with('createdBy', 'updatedBy');

            $totalFiltered = $data_filtered->count();
            $data = $data_filtered->limit($request['length'])->get();
        } else {
            $data_filtered = Portal::orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
                ->with('createdBy', 'updatedBy');
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

    public function getPortal(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:portals,id',
        ]);
        try {
            $cat = Portal::find($request->id);
            $result = array('status' => '1', 'msg' => 'Portal details fetched successfully', 'data' => $cat);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:portals,id',
            'name' => 'required|string',
            'short_name' => 'required|string',
            'quota' => 'nullable|numeric',
            'token' => 'nullable|string',
            'token_abu_dhabi' => 'nullable|string',
            'token_commercial' => 'nullable|string',
            'thumbnail' => 'nullable|mimes:jpg,jpeg,png',
            'image' => 'nullable|mimes:jpg,jpeg,png',
        ]);

        try {
            $portal = Portal::find($request->id);
            $thumbnail = $portal->thumbnail;
            $image = $portal->image;
            if ($request->hasFile("thumbnail")) {
                $thumbnail = $this->uploadImage(file: $request->file("thumbnail"), prefix: "thumb");
            }

            if ($request->hasFile("image")) {
                $image = $this->uploadImage($request->file("image"));
            }
            $xmlUrl = $portal->xml_url;
            if (empty($xmlUrl)){
                $xmlUrl = Str::random();
            }
            $portal->update([
                'name' => $request->name,
                'short_name' => $request->short_name,
                'slug' => Str::slug($request->name, "-"),
                'quota' => $request->quota,
                'token' => $request->token,
                'token_abu_dhabi' => $request->token_abu_dhabi,
                'token_commercial' => $request->token_commercial,
                'thumbnail' => $thumbnail,
                'image' => $image,
                'xml_url' => $xmlUrl,
                'status' => $request->boolean("status"),
                'updated_by_id' => auth()->id(),
            ]);
            $result = array('status' => '1', 'msg' => 'Portal details updated successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:portals',
            'action' => 'required',
        ]);

        $category = Portal::find($request->id);
        $category->status = $request->boolean('action');
        $category->updated_by_id = auth()->id();
        if ($category->save()) {
            $result = array('status' => '1', 'msg' => 'Portal status updated successfully');
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:portals',

        ]);

        $category = Portal::find($request->id);
        if ($category->delete()) {
            $result = array('status' => '1', 'msg' => 'Portal deleted successfully');
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function deleteMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|exists:portals,id',
        ]);
        if (gettype($request->ids) == 'array' && count($request->ids) > 0) {
            $ids = $request->ids;
            if (Portal::whereIn('id', $ids)->delete()) {
                $result = array('status' => '1', 'msg' => count($ids).' Categories deleted successfully');
            } else {
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }
        }
        return json_encode($result);
    }
}
