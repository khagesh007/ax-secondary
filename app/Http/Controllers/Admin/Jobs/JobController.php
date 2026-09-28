<?php

namespace App\Http\Controllers\Admin\Jobs;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobNote;
use App\Models\User;
use App\Traits\FiltersTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class JobController extends Controller
{
    use FiltersTrait;

    public function index()
    {
        abort_if(Gate::denies("Jobs List"), 401);
        $notAdmin = auth()->user()->hasRole(["Sales Director","Line Manager", "Agent"]);
        $childrenIds = [];
        if ($notAdmin){
            $childrenIds = getUserChildrenIds(auth()->id());
        }
        $data['agents'] = User::whereHas("roles", function ($q) {
            $q->whereIn("name", ["Agent"]);
        })->when($notAdmin, fn($q) => $q->whereIn("parent_id", $childrenIds))->where("status", true)->get();
        return view("admin.jobs.index")->with($data);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            "requested_by_id" => "required|exists:users,id",
            "type" => "required|string",
            "description" => "nullable|string",
            "priority" => "required|string",
            "deadline_date" => "nullable",
            "assigned_to_id" => "nullable|exists:users,id",
            "property_id" => "nullable|exists:properties,id",
        ]);

        try {
            $validatedData["created_by_id"] = auth()->id();
            $validatedData["job_status"] = "Created";
            $job = Job::create($validatedData);

            return response()->json(['status' => '1', 'msg' => 'Job Added Successfully.']);

        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }

    public function getJob(Request $request)
    {
        $request->validate([
            "id" => "required|exists:jobs,id"
        ]);
        $job = Job::find($request->id);
        if(!is_null($job)){
            return response()->json(['status' => '1', 'msg' => 'Job Found', 'data' => $job]);
        }else{
            return response()->json(['status' => '0', 'msg' => 'Job Not Found']);
        }
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            "id" => "required|exists:jobs,id",
            "requested_by_id" => "required|exists:users,id",
            "type" => "required|string",
            "description" => "nullable|string",
            "priority" => "required|string",
            "deadline_date" => "nullable",
            "assigned_to_id" => "nullable|exists:users,id",
            "approved_by_id" => "nullable|exists:users,id",
            "job_status" => "nullable|string",
            "property_id" => "nullable|exists:properties,id",
        ]);

        try {
            $job = Job::find($request->id);
            $validatedData["updated_by_id"] = auth()->id();
            $validatedData["approved_by_id"] = $request->approved_by_id ?? $job->approved_by_id;
            $validatedData["job_status"] = $request->job_status ?? $job->job_status;
            $job->update($validatedData);

            return response()->json(['status' => '1', 'msg' => 'Job updated Successfully.']);

        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:jobs',
        ]);

        $type = Job::find($request->id);
        $this->deleteEvent($type);
        if ($type->delete()) {
            $result = array('status' => '1', 'msg' => 'Job deleted successfully');
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
            if (Job::whereIn('id', $ids)->delete()) {
                $result = array('status' => '1', 'msg' => count($ids).' Jobs deleted successfully');
            } else {
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }
        }

        return json_encode($result);
    }

    public function dataTable(Request $request)
    {
        $columns = array(
            0 => 'id',
            1 => 'id',
            2 => 'type',
            3 => 'requested_by_id',
            4 => 'assigned_to_id',
            5 => 'approved_by_id',
            6 => 'deadline_date',
            7 => 'priority',
            8 => 'created_by_id',
            9 => 'updated_at',
            10 => 'job_status',
        );

        $notAdmin = auth()->user()->hasRole(["Sales Director","Line Manager", "Agent"]);
        $childrenIds = [];
        if ($notAdmin){
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        $searchKeyWord = htmlspecialchars($request['search']['value']);
        $q = Job::query();
        $totalData = $this->applyJobFilters($q, $request)->when($notAdmin, function ($q) use ($childrenIds){
            $q->whereIn("requested_by_id", $childrenIds);
            $q->orWhereIn("assigned_to_id", $childrenIds);
            $q->orWhereIn("approved_by_id", $childrenIds);
            $q->orWhereIn("created_by_id", $childrenIds);
        })->count();
        $totalFiltered = $totalData;

        if (!empty($searchKeyWord)) {
            $query = Job::query();
            $data = $this->applyJobFilters($query, $request)->with('createdBy', 'requestedBy', 'approvedBy', 'assignedTo')
                ->when($notAdmin, function ($q) use ($childrenIds){
                    $q->whereIn("requested_by_id", $childrenIds);
                    $q->orWhereIn("assigned_to_id", $childrenIds);
                    $q->orWhereIn("approved_by_id", $childrenIds);
                    $q->orWhereIn("created_by_id", $childrenIds);
                })->get();
        } else {
            $query = Job::query();
            $data = $this->applyJobFilters($query, $request)->orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
                ->offset($request['start'])
                ->limit($request['length'])
                ->with('createdBy', 'requestedBy', 'approvedBy', 'assignedTo')
                ->when($notAdmin, function ($q) use ($childrenIds){
                    $q->whereIn("requested_by_id", $childrenIds);
                    $q->orWhereIn("assigned_to_id", $childrenIds);
                    $q->orWhereIn("approved_by_id", $childrenIds);
                    $q->orWhereIn("created_by_id", $childrenIds);
                })
                ->get();
        }

        $json_data = array(
            "draw" => intval($request['draw']),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );

        echo json_encode($json_data);
    }

    public function addNote(Request $request)
    {
        $request->validate([
            'job_id' => 'required|exists:jobs,id',
            'note' => 'required|string',
        ]);

        try {
            JobNote::create([
                "job_id" => $request->job_id,
                "note" => $request->note,
                "created_by_id" => auth()->id(),
            ]);
            $result = array('status' => '1', 'msg' => "Job note added successfully.");
            return json_encode($result);
        }catch (\Exception $exception){
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            return json_encode($result);
        }
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:jobs',
            'status' => 'required|string',
        ]);

        $status = Job::find($request->id);
        if ($request->status == "Approved"){
            $status->approved_by_id = auth()->id();
        }
        $status->job_status = $request->status;
        $status->updated_by_id = auth()->id();
        if ($status->save()) {
            $result = array('status' => '1', 'msg' => 'Deal status updated successfully');
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }
}
