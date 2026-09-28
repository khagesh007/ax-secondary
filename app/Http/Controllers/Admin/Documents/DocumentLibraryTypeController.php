<?php

namespace App\Http\Controllers\Admin\Documents;

use App\Http\Controllers\Controller;
use App\Models\DocumentLibraryType;
use App\Models\EventType;
use App\Traits\FileTrait;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use DataTables;
class DocumentLibraryTypeController extends Controller
{
    use FileTrait;

    public function index()
    {
        //  abort_if(Gate::denies("Event Type List"), 401);
        return view('admin.documents.docLibraryType');
    }
    public function store(Request $request){
        $validator = \Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'icon' => 'nullable|string',
            'badge' => 'nullable|string',
            'order' => 'nullable|integer',
            'status' => 'nullable',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => '2', 'errors' => $validator->errors()]);
        }
        try {
            $type = new DocumentLibraryType();
            $type->name = $request->name;
            $type->description = $request->description;
            $type->slug =  Str::slug($request->name, "-");
            $type->icon = $request->icon;
            $type->badge = $request->badge;
            $type->order = $request->order ?? 0;
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
            $result = array('status' => '1', 'msg' => 'Documents library type added successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => "Error in Event type Controller ".$exception->getMessage());
        }
        return response()->json($result);
    }
    public function update(Request $request){
        $validator = \Validator::make($request->all(), [
            'id' => 'required|exists:document_library_types,id',
            'name' => 'required|string',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'icon' => 'nullable|string',
            'badge' => 'nullable|string',
            'order' => 'nullable|integer',
            'status' => 'nullable',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => '2', 'errors' => $validator->errors()]);
        }
        try {
            $type = DocumentLibraryType::find($request->id);
            $type->name = $request->name;
            $type->description = $request->description;
            $type->slug =  Str::slug($request->name, "-");
            $type->icon = $request->icon;
            $type->badge = $request->badge;
            $type->order = $request->order ?? 0;
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
            $result = array('status' => '1', 'msg' => 'Documents library type updated successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => "Error in Event type Controller ".$exception->getMessage());
        }
        return response()->json($result);
    }

    public function datatable(Request $request)
    {
        $query = DocumentLibraryType::with(['createdBy', 'updatedBy']);
        return DataTables::of($query)
            ->make(true);
    }
    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:sources',
            'action' => 'required',
        ]);

        $status = DocumentLibraryType::find($request->id);
        $status->status = $request->boolean('action');
        $status->updated_by_id = auth()->user()->id;
        if ($status->save()) {
            $result = array('status' => '1', 'msg' => 'Documents library type status updated successfully');
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }
    public function get(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:document_library_types,id',
        ]);
        try {
            $cat = DocumentLibraryType::find($request->id);
            $result = array('status' => '1', 'msg' => 'Details fetched successfully', 'data' => $cat);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }
    public function delete(Request $request)
    {

        $request->validate([
            'ids' => 'required|array',
        ]);
        if (gettype($request->ids) == 'array' && count($request->ids) > 0) {
            $ids = $request->ids;

            if (DocumentLibraryType::whereIn('id', $ids)->delete()) {
                $result = array('status' => '1', 'msg' => count($ids) . ' documents type deleted successfully');
            } else {
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }

        }

        return json_encode($result);
    }
}
