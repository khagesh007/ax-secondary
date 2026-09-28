<?php

namespace App\Http\Controllers\Admin\Property;

use App\Models\Portal;
use App\Models\PropertyCategoryPortal;
use App\Traits\FileTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PropertyCategory;
use Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    use FileTrait;

    public function index()
    {
        abort_if(Gate::denies("Property Categories List"), 401);
        $portals = Portal::where("status", true)->get();
        return view('admin.property.category', compact("portals"));
    }

    public function store(Request $request)
    {
        $request->validate([
            "title" => "required|string",
            'thumbnail' => 'nullable|mimes:jpg,jpeg,png',
            'image' => 'nullable|mimes:jpg,jpeg,png',
            'color_code' => 'nullable|string',
            'portals' => 'required|array',
            'portals.*.title' => 'required|string',
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

            $category = PropertyCategory::create([
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
                    PropertyCategoryPortal::create([
                        "property_category_id" => $category->id,
                        "portal_id" => $id,
                        "title" => $portal["title"],
                        "created_by_id" => auth()->id(),
                    ]);
                }
            }
            $result = array('status' => '1', 'msg' => 'Property category added successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }

    public function categoriesForDatatable(Request $request)
    {
        $columns = array(
            0 => 'updated_at',
            1 => 'id',
            2 => 'title'
        );
        $searchKeyWord = htmlspecialchars($request['search']['value']);
        $totalData = PropertyCategory::count();
        $totalFiltered = $totalData;

       

        if (!empty($searchKeyWord)) {
            $data_filtered = PropertyCategory::where(function ($q) use ($searchKeyWord) {
                $q->where('title', 'LIKE', '%' . $searchKeyWord . '%');
            })->with('created_by', 'updated_by');

            $totalFiltered = $data_filtered->count();
            $data = $data_filtered->limit($request['length'])->get();
        } else {
            $data_filtered = PropertyCategory::orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
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

    public function getCategory(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:property_categories,id',
        ]);
        try {
            $cat = PropertyCategory::find($request->id);
            $cat->load(["portals.portal"]);
            $result = array('status' => '1', 'msg' => 'Category details fetched successfully', 'data' => $cat);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:property_categories,id',
            "title" => "required|string",
            'thumbnail' => 'nullable|mimes:jpg,jpeg,png',
            'image' => 'nullable|mimes:jpg,jpeg,png',
            'color_code' => 'nullable|string',
            'portals' => 'required|array',
            'portals.*.title' => 'required|string',
        ]);

        try {
            $category = PropertyCategory::find($request->id);

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
                'icon' => $request->icon,
                "order" => $request->order??0,
                "badge" => $request->status_badge,
                
                "status" => $request->boolean("status"),
                "updated_by_id" => auth()->id(),
            ]);

            foreach ($request->portals ?? [] as $id => $portal) {
                if (!empty($portal["title"])) {
                    PropertyCategoryPortal::updateOrCreate([
                        "property_category_id" => $category->id,
                        "portal_id" => $id,
                    ], [
                        "title" => $portal["title"],
                        "created_by_id" => auth()->id(),
                        "updated_by_id" => auth()->id(),
                    ]);
                } else {
                    PropertyCategoryPortal::where("property_category_id", $category->id)->where("portal_id",
                        $id)->delete();
                }
            }

            $result = array('status' => '1', 'msg' => 'Category details updated successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:property_categories',
            'action' => 'required',
        ]);

        $category = PropertyCategory::find($request->id);
        $category->status = $request->boolean('action');
        $category->updated_by_id = auth()->user()->id;
        if ($category->save()) {
            $result = array('status' => '1', 'msg' => 'Category status updated successfully');
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:property_categories',
        ]);

        $category = PropertyCategory::find($request->id);
        if ($category->delete()) {
            $result = array('status' => '1', 'msg' => 'Category deleted successfully');
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
            if (PropertyCategory::whereIn('id', $ids)->delete()) {
                $result = array('status' => '1', 'msg' => count($ids).' Categories deleted successfully');
            } else {
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }

        }
        return json_encode($result);
    }
}
