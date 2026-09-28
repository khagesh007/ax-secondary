<?php

namespace App\Http\Controllers\Admin\Property;

use App\Models\Portal;
use App\Models\PropertyAmenityPortal;
use App\Traits\FileTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PropertyAmenity;
use Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class FeatureController extends Controller
{
    use FileTrait;

    public function index()
    {
        abort_if(Gate::denies("Property Amenities List"), 401);
        $portals = Portal::where("status", true)->get();
        return view('admin.property.feature', compact("portals"));
    }

    public function store(Request $request)
    {
        $request->validate([
            "title" => "required|string",
            'thumbnail' => 'nullable|mimes:jpg,jpeg,png,svg',
            'image' => 'nullable|mimes:jpg,jpeg,png,svg',
            'color_code' => 'nullable|string',
            'portals' => 'required|array',
            'status_badge' => 'nullable',
            'portals.*.title' => 'required|string',
            'portals.*.type' => 'nullable|string',
        ]);

        try {
            $thumbnail = null;
            $image = null;
            if ($request->hasFile("thumbnail")) {
                if($request->thumbnail->getClientOriginalExtension()=='svg'){
                    $thumbnail = $this->uploadSvg(file: $request->file("thumbnail"));
                }else{
                    $thumbnail = $this->uploadImage(file: $request->file("thumbnail"), prefix: "thumb");
                }
            }


            if ($request->hasFile("image")) {
                if($request->image->getClientOriginalExtension()=='svg'){
                    $image = $this->uploadSvg(file: $request->file("image"));
                }else{
                    $image = $this->uploadImage(file: $request->file("image"), prefix: "image");
                }
            }

            $category = PropertyAmenity::create([
                "title" => $request->title,
                'slug' => Str::slug($request->title, "-"),
                "color_code" => $request->color_code,
                'thumbnail' => $thumbnail,
                'image' => $image,
                'icon' => $request->icon,
                "order" => $request->order??0,
                "badge" => $request->status_badge,
                "status" => $request->boolean("status"),
                "created_by_id" => auth()->id(),
            ]);

            foreach ($request->portals ?? [] as $id => $portal) {
                if (!empty($portal["title"])) {
                    PropertyAmenityPortal::create([
                        "property_amenity_id" => $category->id,
                        "portal_id" => $id,
                        "title" => $portal["title"],
                        "type" => $portal["type"] ?? null,
                        "created_by_id" => auth()->id(),
                    ]);
                }
            }
            $result = array('status' => '1', 'msg' => 'Property feature added successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }

    public function featureForDatatable(Request $request)
    {
        $columns = array(
            0 => 'updated_at',
            1 => 'id',
            2 => 'title'

        );
        $searchKeyWord = htmlspecialchars($request['search']['value']);
        $totalData = PropertyAmenity::count();
        $totalFiltered = $totalData;



        if (!empty($searchKeyWord)) {
            $data_filtered = PropertyAmenity::where(function ($q) use ($searchKeyWord) {
                $q->where('title', 'LIKE', '%' . $searchKeyWord . '%');
            })->with('created_by', 'updated_by');

            $totalFiltered = $data_filtered->count();
            $data = $data_filtered->limit($request['length'])->get();
        } else {
            $data_filtered = PropertyAmenity::orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
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

    public function getFeature(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:property_amenities,id',
        ]);
        try {
            $cat = PropertyAmenity::find($request->id);
            $cat->load(["portals.portal"]);
            $result = array('status' => '1', 'msg' => 'Feature details fetched successfully', 'data' => $cat);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:property_amenities,id',
            "title" => "required|string",
            'thumbnail' => 'nullable|mimes:jpg,jpeg,png,svg',
            'image' => 'nullable|mimes:jpg,jpeg,png,svg',
            'color_code' => 'nullable|string',
            'portals' => 'required|array',
            'status_badge' => 'nullable',
            'portals.*.title' => 'required|string',
            'portals.*.type' => 'nullable|string',
        ]);

        try {
            $category = PropertyAmenity::find($request->id);

            $thumbnail = $category->thumbnail;
            $image = $category->image;
            if ($request->thumbnail) {
                if($request->thumbnail->getClientOriginalExtension()=='svg'){
                    $thumbnail = $this->uploadSvg(file: $request->file("thumbnail"));
                }else{
                    $thumbnail = $this->uploadImage(file: $request->file("thumbnail"), prefix: "thumb");
                }
            }

            if ($request->hasFile("image")) {
                if($request->image->getClientOriginalExtension()=='svg'){
                    $image = $this->uploadSvg(file: $request->file("image"));
                }else{
                    $image = $this->uploadImage(file: $request->file("image"), prefix: "image");
                }
            }

            $category->update([
                "title" => $request->title,
                'slug' => Str::slug($request->title, "-"),
                "color_code" => $request->color_code,
                'thumbnail' => $thumbnail,
                'image' => $image,
                'icon' => $request->icon,
                "order" => $request->order??0,
                "badge" => $request->status_badge,
                "status" => $request->boolean("status"),
                "updated_by_id" => auth()->id(),
            ]);

            foreach ($request->portals ?? [] as $id => $portal) {
                if (!empty($portal["title"])) {
                    PropertyAmenityPortal::updateOrCreate([
                        "property_amenity_id" => $category->id,
                        "portal_id" => $id,
                    ], [
                        "title" => $portal["title"],
                        "type" => $portal["type"] ?? null,
                        "created_by_id" => auth()->id(),
                        "updated_by_id" => auth()->id(),
                    ]);
                } else {
                    PropertyAmenityPortal::where("property_amenity_id", $category->id)->where("portal_id",
                        $id)->delete();
                }
            }

            $result = array('status' => '1', 'msg' => 'Feature details updated successfully');
        } catch (\Exception $exception) {
            // dd($exception->getMessage());
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:property_amenities',
            'action' => 'required',
        ]);

        $status = PropertyAmenity::find($request->id);
        $status->status = $request->boolean('action');
        $status->updated_by_id = auth()->user()->id;
        if ($status->save()) {
            $result = array('status' => '1', 'msg' => ' Feature updated successfully');
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:property_amenities',

        ]);

        $status = PropertyAmenity::find($request->id);
        if ($status->delete()) {
            $result = array('status' => '1', 'msg' => 'Feature deleted successfully');
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
            if (PropertyAmenity::whereIn('id', $ids)->delete()) {
                $result = array('status' => '1', 'msg' => count($ids).' Feature deleted successfully');
            } else {
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }

        }

        return json_encode($result);
    }
}

