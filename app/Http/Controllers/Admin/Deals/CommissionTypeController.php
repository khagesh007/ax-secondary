<?php

namespace App\Http\Controllers\Admin\Deals;

use App\Models\CommissionType;
use App\Models\Portal;
use App\Models\PropertyAmenityPortal;
use App\Traits\FileTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PropertyAmenity;
use Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class CommissionTypeController extends Controller
{
    use FileTrait;

    public function index()
    {
        abort_if(Gate::denies("Deal Commission Type List"), 401);
        $portals = Portal::where("status", true)->get();
        return view('admin.deals.parameters.commissionType', compact("portals"));
    }

    public function store(Request $request)
    {
        $request->validate([
            "name" => "required|string",
            'thumbnail' => 'nullable|mimes:jpg,jpeg,png,svg',
            'image' => 'nullable|mimes:jpg,jpeg,png,svg',
            'color_code' => 'nullable|string',
            'status_badge' => 'nullable',
            'description' => 'nullable|string',
            'property_type' => 'nullable|string',
            'lead_type' => 'nullable|string',
            'order' => 'nullable|integer',
            'icon' => 'nullable|string',
          'status' => 'nullable|string',
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
            $obj = new CommissionType();
            $obj->name = $request->name;
            $obj->slug = Str::slug($request->name, "-");
            $obj->description = $request->description;
            $obj->status = $request->has("status") ? 1 : 0;
            $obj->order = $request->order??0;
            $obj->property_type = $request->property_type;
            $obj->lead_type = $request->lead_type;
            $obj->thumbnail = $thumbnail;
            $obj->image = $image;
            $obj->icon = $request->icon;
            $obj->badge = $request->status_badge;
            $obj->created_by_id = auth()->id();
            $obj->save();
            $result = array('status' => '1', 'msg' => 'Property feature added successfully');
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
            2 => 'title'

        );
        $searchKeyWord = htmlspecialchars($request['search']['value']);
        $totalData = CommissionType::count();
        $totalFiltered = $totalData;



        if (!empty($searchKeyWord)) {
            $data_filtered = CommissionType::where(function ($q) use ($searchKeyWord) {
                $q->where('name', 'LIKE', '%' . $searchKeyWord . '%');
            })->with('created_by', 'updated_by');

            $totalFiltered = $data_filtered->count();
            $data = $data_filtered->limit($request['length'])->get();
        } else {
            $data_filtered = CommissionType::orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
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

    public function get(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:commission_types,id',
        ]);
        try {
            $commission_type = CommissionType::find($request->id);

            $result = array('status' => '1', 'msg' => 'Commission type fetched successfully', 'data' => $commission_type);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:commission_types,id',
            "name" => "required|string",
            'thumbnail' => 'nullable|mimes:jpg,jpeg,png,svg',
            'image' => 'nullable|mimes:jpg,jpeg,png,svg',
            'color_code' => 'nullable|string',
            'status_badge' => 'nullable',
            'description' => 'nullable|string',
            'property_type' => 'nullable|string',
            'lead_type' => 'nullable|string',
            'order' => 'nullable|integer',
            'icon' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        try {
            $commission_type = CommissionType::find($request->id);

            $thumbnail = $commission_type->thumbnail;
            $image =$commission_type->image;
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

            $obj = CommissionType::find($request->id);
            $obj->name = $request->name;
            $obj->slug = Str::slug($request->name, "-");
            $obj->description = $request->description;
            $obj->status = $request->has("status") ? 1 : 0;
            $obj->order = $request->order??0;
            $obj->property_type = $request->property_type;
            $obj->lead_type = $request->lead_type;
            $obj->thumbnail = $thumbnail;
            $obj->image = $image;
            $obj->icon = $request->icon;
            $obj->badge = $request->status_badge;
            $obj->updated_by_id = auth()->id();
            $obj->save();



            $result = array('status' => '1', 'msg' => 'Commission type updated successfully');
        } catch (\Exception $exception) {
            // dd($exception->getMessage());
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:commission_types,id',
            'action' => 'required',
        ]);

        $ct = CommissionType::find($request->id);
        $ct->status = $request->boolean('action');
        $ct->updated_by_id = auth()->user()->id;
        if ($ct->save()) {
            $result = array('status' => '1', 'msg' => 'Commission type status updated successfully');
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:commission_types,id',

        ]);

        $status = CommissionType::find($request->id);
        if ($status->delete()) {
            $result = array('status' => '1', 'msg' => 'Commission type deleted successfully');
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
            if (CommissionType::whereIn('id', $ids)->delete()) {
                $result = array('status' => '1', 'msg' => count($ids).' Commission type successfully');
            } else {
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }

        }

        return json_encode($result);
    }
}

