<?php

namespace App\Http\Controllers\Admin\Source;

use App\Http\Controllers\Controller;
use App\Models\Source;
use App\Traits\FileTrait;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Response;

class SourceController extends Controller
{
    use FileTrait;

    public function index()
    {
        abort_if(Gate::denies("Sources List"), 401);
        return view('admin.source.index');
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
            $obj = new Source();
            $obj->name = $request->name;
            $obj->order = $request->order??0;
            $obj->badge = $request->status_badge;
            $obj->icon = $request->icon;
            $obj->status = $request->status == 'on' ? 1 : 0;
            $obj->is_contact_source = $request->is_contact_source == 'on' ? 1 : 0;
            $obj->is_lead_source = $request->is_lead_source == 'on' ? 1 : 0;
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
            $result = array('status' => '1', 'msg' => 'Contact source added successfully');
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
        $totalData = Source::count();
        $totalFiltered = $totalData;

        if (!empty($searchKeyWord)) {
            $data_filtered = Source::where(function ($q) use ($searchKeyWord) {
                $q->where('name', 'LIKE', '%' . $searchKeyWord . '%');
            })->with('created_by', 'updated_by');

            $totalFiltered = $data_filtered->count();
            $data = $data_filtered->limit($request['length'])->get();
        } else {
            $data_filtered = Source::orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
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
        $totalData = Source::count();
        $totalFiltered = $totalData;

        if (!empty($searchKeyWord)) {
            $data = Source::where('is_lead_source', 1)->where(function ($q) use ($searchKeyWord) {
                $q->where('name', 'LIKE', '%' . $searchKeyWord . '%');
            })->with('created_by', 'updated_by')->get();
        } else {
            $data = Source::where('is_lead_source', 1)->orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
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
        $totalData = Source::count();
        $totalFiltered = $totalData;

        if (!empty($searchKeyWord)) {
            $data = Source::where('is_contact_source', 1)->where(function ($q) use ($searchKeyWord) {
                $q->where('name', 'LIKE', '%' . $searchKeyWord . '%');
            })->with('created_by', 'updated_by')
                ->limit($request['length'])->get();
            $totalFiltered = $data->count();

        } else {
            $data = Source::where('is_contact_source', 1)->orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
                ->offset($request['start'])
                ->limit($request['length'])
                ->with('created_by', 'updated_by')
                ->get();
            $totalFiltered = $data->count();

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
            'id' => 'required|exists:sources,id',
        ]);
        try {
            $cat = Source::find($request->id);
            $result = array('status' => '1', 'msg' => 'Source details fetched successfully', 'data' => $cat);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:sources,id',
            'name' => 'required|string',
            'icon' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'order' => 'nullable|string',
            'status_badge' => 'nullable|string',
        ]);

        try {
            $cat = Source::find($request->id);
            $cat->name = $request->name;
            $cat->badge = $request->status_badge;
            $cat->icon = $request->icon;
            $cat->status = $request->status == 'on' ? 1 : 0;
            $cat->is_lead_source = $request->is_lead_source == 'on' ? 1 : 0;
            $cat->is_contact_source = $request->is_contact_source == 'on' ? 1 : 0;
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
            $result = array('status' => '1', 'msg' => 'Contact source details updated successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }
    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:sources',
            'action' => 'required',
        ]);

        $status = Source::find($request->id);
        $status->status = $request->boolean('action');
        $status->updated_by_id = auth()->user()->id;
        if ($status->save()) {
            $result = array('status' => '1', 'msg' => ' source updated successfully');
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }
    public function updateLeadStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:sources',
            'action' => 'required',
        ]);

        $status = Source::find($request->id);
        $status->is_lead_source = $request->boolean('action');
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
            'id' => 'required|exists:sources',
            'action' => 'required',
        ]);

        $status = Source::find($request->id);
        $status->is_contact_source = $request->boolean('action');
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
            'id' => 'required|exists:sources',

        ]);

        $status = Source::find($request->id);
        if ($status->delete()) {
            $result = array('status' => '1', 'msg' => 'sources deleted successfully');
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

            if (Source::whereIn('id', $ids)->delete()) {
                $result = array('status' => '1', 'msg' => count($ids) . ' Sources deleted successfully');
            } else {
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }

        }

        return json_encode($result);
    }

    public function deleteThumbnail(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:sources',
        ]);
        $source = Source::find($request->id);
        $source->thumbnail = null;
        if ($source->save()) {
            $result = array('status' => '1', 'msg' => 'Thumbnail deleted successfully');
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }



}
