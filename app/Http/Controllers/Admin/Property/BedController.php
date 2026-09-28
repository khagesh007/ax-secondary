<?php

namespace App\Http\Controllers\Admin\Property;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bed;
use App\Models\Portal;
use App\Models\BedPortal;
use Auth;
use Illuminate\Support\Facades\Gate;

class BedController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies("Beds List"), 401);
        $portals = Portal::where("status", true)->get();
        return view('admin.property.bed', compact('portals'));
    }

    public function store(Request $request)
    {
        $request->validate([
            "name" => "required|string",
            "order" => "required|integer",
            'portals' => 'required|array',
            'portals.*.title' => 'required|string',
        ]);

        try {
            $bed = Bed::create([
                "name" => $request->name,
                "order" => $request->order,
                "status" => $request->boolean("status"),
                "created_by_id" => auth()->id(),
            ]);
            foreach ($request->portals ?? [] as $id => $portal) {
                if (!empty($portal["title"]) || $portal["title"] == "0") {
                    BedPortal::create([
                        "bed_id" => $bed->id,
                        "portal_id" => $id,
                        "name" => $portal["title"],
                        "created_by_id" => auth()->id(),
                    ]);
                }
            }
            $result = array('status' => '1', 'msg' => 'Property bed added successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }

    public function bedForDatatable(Request $request)
    {
        $columns = array(
            0 => 'updated_at',
            1 => 'id',
            2 => 'name',
            3 => 'order',
        );
        $searchKeyWord = htmlspecialchars($request['search']['value']);
        $totalData = Bed::count();
        $totalFiltered = $totalData;

        if (!empty($searchKeyWord)) {
            $data_filtered = Bed::where(function ($q) use ($searchKeyWord) {
                $q->where('name', 'LIKE', '%' . $searchKeyWord . '%');
            })->with('created_by', 'updated_by');

            $totalFiltered = $data_filtered->count();
            $data = $data_filtered->limit($request['length'])->get();
        } else {
            $data_filtered = Bed::orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
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


    public function getBed(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:beds,id',
        ]);
        try {
            $cat = Bed::find($request->id);
            $cat->load(["portals.portal"]);
            $result = array('status' => '1', 'msg' => 'Property bed details fetched successfully', 'data' => $cat);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }


    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:beds,id',
            "name" => "required|string",
            "order" => "required|integer",
            'portals' => 'required|array',
            'portals.*.title' => 'required|string',
        ]);

        try {
            $bed = Bed::find($request->id);
            $bed->update([
                "name" => $request->name,
                "order" => $request->order,
                "status" => $request->boolean("status"),
                "updated_by_id" => auth()->id(),
            ]);
            foreach ($request->portals ?? [] as $id => $portal) {
                if (!empty($portal["title"]) || $portal["title"] == "0") {
                    BedPortal::updateOrCreate([
                        "bed_id" => $bed->id,
                        "portal_id" => $id,
                    ], [
                        "name" => $portal["title"],
                        "created_by_id" => auth()->id(),
                        "updated_by_id" => auth()->id(),
                    ]);
                } else {
                    BedPortal::where("bed_id", $bed->id)->where("portal_id", $id)->delete();
                }
            }
            $result = array('status' => '1', 'msg' => 'Property bed  details updated successfully');
        } catch (\Exception $exception) {
            return $exception;
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:beds',
            'action' => 'required',
        ]);

        $type = Bed::find($request->id);
        $type->status = $request->boolean('action');
        $type->updated_by_id = auth()->user()->id;
        if ($type->save()) {
            $result = array('status' => '1', 'msg' => 'Property bed status updated successfully');
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:beds',

        ]);

        $type = Bed::find($request->id);
        if ($type->delete()) {
            $result = array('status' => '1', 'msg' => 'Property bed deleted successfully');
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
            if (Bed::whereIn('id', $ids)->delete()) {
                $result = array('status' => '1', 'msg' => count($ids).' Property bed deleted successfully');
            } else {
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }
        }
        return json_encode($result);
    }
}
