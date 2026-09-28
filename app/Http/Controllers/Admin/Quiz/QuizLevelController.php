<?php

namespace App\Http\Controllers\Admin\Quiz;


use App\Models\Quiz;
use App\Models\QuizLevel;
use App\Models\Region;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use App\Traits\FileTrait;
class QuizLevelController extends Controller
{
    use FileTrait;

public function index()
{
    return view('admin.quiz.level');

}
//    public function store(Request $request)
//    {
//        $validator = \Validator::make($request->all(), [
//            'name' => 'required|string',
//            'description' => 'nullable|string',
//            'quiz_id' => 'required|integer',
//            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
//        ]);
//        if ($validator->fails()) {
//            return response()->json(['status' => '2', 'errors' => $validator->errors()]);
//        }
//
//        try {
//
//            $obj = new QuizLevel();
//            $obj->name = $request->name;
//            $obj->description = $request->description;
//            $obj->quiz_id = $request->quiz_id;
//            $obj->created_by_id = \Auth::user()->id;
//            if($obj->save()){
//                $result = array('status' => '1', 'msg' => 'Quiz level created successfully');
//            }else{
//                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
//            }
//
//
//        } catch (\Exception $exception) {
//            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
//        }
//        return json_encode($result);
//    }
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

            $obj = new QuizLevel();
            $obj->name = $request->name;
            $obj->badge = $request->status_badge;
            $obj->icon = $request->icon;
            $obj->order = $request->order??0;
            $obj->status = $request->status == 'on' ? 1 : 0;
            $obj->created_by_id = auth()->user()->id;

            if ($request->has('thumbnail')) {
                $image = $this->uploadImage($request->file("thumbnail"));
                $obj->thumbnail = $image;
            }
            if ($request->has('image')) {
                $image = $this->uploadImage($request->file("image"));
                $obj->image = $image;
            }

            $obj->save();
            $result = array('status' => '1', 'msg' => 'Quiz level added successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }
    public function statusForDatatable(Request $request)
    {
        $columns = array(
            0 => 'updated_at',
            1 => 'id',
            2 => 'name'
        );
        $searchKeyWord = htmlspecialchars($request['search']['value']);
        $totalData = QuizLevel::count();
        $totalFiltered = $totalData;


        if (!empty($searchKeyWord)) {
            $data_filtered = QuizLevel::where(function ($q) use ($searchKeyWord) {
                $q->where('name', 'LIKE', '%' . $searchKeyWord . '%');
            })->with('created_by', 'updated_by');

            $totalFiltered = $data_filtered->count();
            $data = $data_filtered->limit($request['length'])->get();
        } else {
            $data_filtered = QuizLevel::orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
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
            'id' => 'required|exists:quiz_levels,id',
        ]);
        try {
            $data = QuizLevel::find($request->id);
            $result = array('status' => '1', 'msg' => 'Quiz level details fetched successfully', 'data' => $data);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function update(Request $request)
    {
        try{
            $validator = \Validator::make($request->all(), [
                'id' => 'required|exists:quiz_levels',
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
            $obj = QuizLevel::find($request->id);
            $obj->name = $request->name;
            $obj->badge = $request->status_badge;
            $obj->icon = $request->icon;
            $obj->order = $request->order??0;
            $obj->status = $request->status == 'on' ? 1 : 0;
            $obj->updated_by_id = auth()->user()->id;

            if ($request->has('thumbnail')) {
                $image = $this->uploadImage($request->file("thumbnail"));
                $obj->thumbnail = $image;
            }
            if ($request->has('image')) {
                $image = $this->uploadImage($request->file("image"));
                $obj->image = $image;
            }

            $obj->save();
            $result = array('status' => '1', 'msg' => 'Quiz level updated successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }
    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:quiz_levels',
            'action' => 'required',
        ]);

        $status = QuizLevel::find($request->id);
        $status->status = $request->boolean('action');
        $status->updated_by_id = auth()->user()->id;
        if ($status->save()) {
            $result = array('status' => '1', 'msg' => 'Quiz level status updated successfully');
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }
    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:quiz_levels',

        ]);

        $type = QuizLevel::find($request->id);
        if ($type->delete()) {
            $result = array('status' => '1', 'msg' => 'Quiz level deleted successfully');
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
            if (QuizLevel::whereIn('id', $ids)->delete()) {
                $result = array('status' => '1', 'msg' => count($ids).' quiz level deleted successfully');
            } else {
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }

        }

        return json_encode($result);
    }
}
