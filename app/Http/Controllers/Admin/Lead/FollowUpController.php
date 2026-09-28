<?php

namespace App\Http\Controllers\Admin\Lead;


use App\Models\Event;
use App\Models\Lead;
use App\Http\Controllers\Controller;
use App\Models\CallLog;
use App\Models\LeadAgent;
use App\Models\LeadFollowUp;
use App\Models\Meeting;
use App\Models\Task;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class FollowUpController extends Controller
{
    // public function storeCallFollowUp(Request $request)
    // {
    //     $validatedData = $request->validate([
    //         "lead_id" => "nullable|exists:leads,id",
    //         "time"=>"required",
    //         "note" => "nullable",
    //     ]);

    //     try {
    //         $obj = new LeadFollowUp();
    //         $obj->lead_id = $validatedData["lead_id"];
    //         $obj->type = "CALL";
    //         $obj->time = $validatedData["time"];
    //         $obj->note = $validatedData["note"];
    //         $obj->created_by_id = Auth::id();
    //         if($obj->save()){
    //             return response()->json([
    //                 "status" => "1",
    //                 "message" => "Call Follow Up Created Successfully",
    //                 "data" => $obj
    //             ]);
    //         }else{
    //             return response()->json([
    //                 "status" => "0",
    //                 "message" => "Call Follow Up Creation Failed",
    //                 "data" => null
    //             ]);
    //         }
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             "status" => "2",
    //             'msg' => 'Unable to create follow up',
    //             "error" => $e->getMessage(),
    //         ]);
    //     }
    // }
    public function storeTaskFollowUp(Request $request)
    {
        $validatedData = $request->validate([
            "lead_id" => "nullable|exists:leads,id",
            "time" => "required",
            "note" => "nullable",
        ]);

        try {
            $obj = new LeadFollowUp();
            $obj->lead_id = $validatedData["lead_id"];
            $obj->type = "TASK";
            $obj->time = $validatedData["time"];
            $obj->note = $validatedData["note"];
            $obj->created_by_id = Auth::id();
            if ($obj->save()) {
                return response()->json([
                    "status" => "1",
                    "message" => "Task Follow Up Created Successfully",
                    "data" => $obj
                ]);
            } else {
                return response()->json([
                    "status" => "0",
                    "message" => "Task Follow Up Creation Failed",
                    "data" => null
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                "status" => "2",
                'msg' => 'Unable to create Task follow up',
                "error" => $e->getMessage(),
            ],500);
        }
    }

    public function storeMeeting(Request $request)
    {
        $validatedData = $request->validate([
            "lead_id" => "nullable|exists:leads,id",
            "subject" => "required",
            "note" => "nullable",
            "location" => "nullable",
            "attendee" => "nullable",
            "start_time" => "nullable",
            "end_time" => "nullable",
            "description" => "nullable",
            "attendees" => "nullable|array",
            "attendees.*" => "nullable|exists:users,id",
        ]);

        try {
            $lead = Lead::find($validatedData["lead_id"]);
            $obj = new LeadFollowUp();
            $obj->lead_id = $validatedData["lead_id"];
            $obj->type = "MEETING";
            $obj->created_by_id = Auth::id();
            if ($obj->save()) {
                $meeting = new Meeting();
                $meeting->user_id = $lead->agent_id;
                $meeting->lead_follow_up_id = $obj->id;
                $meeting->lead_id = $validatedData["lead_id"];
                $meeting->subject = $validatedData["subject"];
                $meeting->location = $validatedData["location"];
                $meeting->start_time = $validatedData["start_time"];
                $meeting->end_time = $validatedData["end_time"];
                $meeting->description = $validatedData["description"];
                $meeting->status = 'PENDING';
                $original   =  $meeting->getOriginal() ?? [];
                $dirty      = $meeting->getDirty() ?? [];
                $meeting->created_by_id = Auth::id();
                if ($meeting->save()) {
                    $meeting->attendees()->sync($request->attendees ?? []);
                    $event_create = (isset($request->create_event) && $request->create_event == false) ? false : true;
                    if($event_create == true) {
                        $event = new Event();
                        $event->agent_id = $lead->agent_id;
                        $event->lead_id = $validatedData["lead_id"];
//                    $event->meeting_id = $meeting->id;
                        $event->type = 'Meeting';
                        $event->title = $validatedData["subject"];
                        $event->start_time = $validatedData["start_time"];
                        $event->end_time = $validatedData["end_time"];
                        $event->property_id = $lead->property_id ?? null;
                        $event->location = $meeting->location;
                        $event->description = $meeting->description;
                        $event->save();
                        $event->attendees()->sync($request->attendees ?? []);
                    }

                    $arr = ['attributes' => $dirty,
                        'old' => $original,'lead_id' =>  $meeting->lead_id];
                    activity()
                        ->performedOn($meeting)
                        ->causedBy(auth()->user()->id)
                        ->withProperties($arr)
                        ->event('created')
                        ->log(auth()->user()->first_name . ' has created a follow-up meeting');

                    return response()->json([
                        "status" => "1",
                        "message" => "Follow-up Meeting Created Successfully",
                        "data" => $meeting
                    ]);
                } else {
                    return response()->json([
                        "status" => "0",
                        "message" => "Meeting Creation Failed",
                        "data" => null
                    ]);
                }

            } else {
                return response()->json([
                    "status" => "0",
                    "message" => "Task Follow Up Creation Failed",
                    "data" => null
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                "status" => "2",
                'msg' => 'Unable to create Task follow up',
                "error" => $e->getMessage(),
            ],500);
        }
    }

    public function updateMeeting(Request $request)
    {
        $validatedData = $request->validate([
            "id" => "required|exists:meetings,id",
            "lead_id" => "required|exists:leads,id",
            "subject" => "required",
            "note" => "nullable",
            "location" => "nullable",
            "attendee" => "nullable",
            "start_time" => "nullable",
            "end_time" => "nullable",
            "description" => "nullable",
            "attendees" => "nullable|array",
            "attendees.*" => "nullable|exists:users,id",
        ]);

        try {
            $meeting = Meeting::find($request->id);
            $meeting->subject = $validatedData["subject"];
            $meeting->location = $validatedData["location"];
            $meeting->start_time = $validatedData["start_time"];
            $meeting->end_time = $validatedData["end_time"];
            $meeting->description = $validatedData["description"];
            $original   =  $meeting->getOriginal() ?? [];
            $dirty      = $meeting->getDirty() ?? [];
            $meeting->updated_by_id = auth()->id();
            if($meeting->save()){
                $arr = ['attributes' => $dirty,
                    'old' => $original,'lead_id' =>  $meeting->lead_id];
                activity()
                    ->performedOn($meeting)
                    ->causedBy(auth()->user()->id)
                    ->withProperties($arr)
                    ->event('created')
                    ->log(auth()->user()->first_name . ' has changed follow-up meeting status to '.$request->status);
            }
            return response()->json([
                "status" => "1",
                "message" => "Follow-up Meeting updated Successfully",
                "data" => $meeting
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "2",
                'msg' => 'Unable to create Task follow up',
                "error" => $e->getMessage(),
            ],500);
        }
    }

    public function getMeeting(Request $request)
    {
        $request->validate([
            "meeting_id" => "required|exists:meetings,id"
        ]);

        $meeting = Meeting::find($request->meeting_id);

        $meeting->load(["attendees"]);

        if(!empty($meeting)){
            return response()->json(['status' => '1', 'msg' => 'Meeting Found', 'data' => $meeting]);
        }else{
            return response()->json(['status' => '0', 'msg' => 'Contact Not Found']);
        }
    }

    public function meetingStatus(Request $request)
    {
        $validatedData = $request->validate([
            "meeting_id" => "required|exists:meetings,id",
            "status" => "required|string",
        ]);

        try {
            $meeting = Meeting::find($request->meeting_id);
            $meeting->status = $request->status;
            $original   =  $meeting->getOriginal() ?? [];
            $dirty      = $meeting->getDirty() ?? [];
            if($meeting->save()){
                $arr = ['attributes' => $dirty,
                    'old' => $original,'lead_id' =>  $meeting->lead_id];
                activity()
                    ->performedOn($meeting)
                    ->causedBy(auth()->user()->id)
                    ->withProperties($arr)
                    ->event('created')
                    ->log(auth()->user()->first_name . ' has changed follow-up meeting status to '.$request->status);
            }
            return response()->json([
                "status" => "1",
                "msg" => "Meeting status changed successfully",
                "data" => $meeting
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "2",
                'msg' => 'Unable to change status',
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    public function storeTask(Request $request)
    {

        $validatedData = $request->validate([
            "lead_id" => "nullable|exists:leads,id",
            "subject" => "required",
            "priority" => "required",
            "description" => "nullable",
            "due_date" => "required",
        ]);
        try {
            $lead = Lead::find($validatedData["lead_id"]);
            $obj = new LeadFollowUp();
            $obj->lead_id = $validatedData["lead_id"];
            $obj->type = "TASK";
            $obj->created_by_id = Auth::id();
            if ($obj->save()) {
                $meeting = new Task();
                $meeting->user_id = $lead->agent_id;
                $meeting->lead_follow_up_id = $obj->id;
                $meeting->lead_id = $validatedData["lead_id"];
                $meeting->subject = $validatedData["subject"];
                $meeting->priority = $validatedData["priority"];
                $meeting->due_date = $validatedData["due_date"];
                $meeting->description = $validatedData["description"];
                $meeting->status = 'pending';
                $meeting->created_by_id = Auth::id();
                $original   =  $meeting->getOriginal() ?? [];
                $dirty      = $meeting->getDirty() ?? [];
                if ($meeting->save()) {
                    $lead->follow_up_date = $validatedData["due_date"];
                    $lead->save();
                    $arr = ['attributes' => $dirty,
                        'old' => $original,'lead_id' =>  $meeting->lead_id];
                    activity()
                        ->performedOn($meeting)
                        ->causedBy(auth()->user()->id)
                        ->withProperties($arr)
                        ->event('created')
                        ->log(auth()->user()->first_name . ' has created a follow-up task');

                    $event_create = (isset($request->create_event) && $request->create_event == false) ? false : true;
                    if($event_create == true) {
                        $event = new Event();
                        $event->agent_id = $lead->agent_id ?? null;
                        $event->lead_id = $validatedData["lead_id"];
                        $event->priority = $validatedData["priority"];
                        $event->type    = 'Task';
                        $event->title   = $validatedData["subject"];
                        $event->start_time = $validatedData["due_date"];
                        $event->end_time = $validatedData["due_date"];
                        $event->description = $meeting->description;
                        $event->save();
                    }
                    return response()->json([
                        "status" => "1",
                        "message" => "Follow-up task Created Successfully",
                        "data" => $meeting
                    ]);
                } else {
                    return response()->json([
                        "status" => "0",
                        "message" => "Task Creation Failed",
                        "data" => null
                    ]);
                }

            } else {
                return response()->json([
                    "status" => "0",
                    "message" => "Task Follow Up Creation Failed",
                    "data" => null
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                "status" => "2",
                'msg' => 'Unable to create Task follow up',
                "error" => $e->getMessage(),
            ],500);
        }
    }

    public function updateTask(Request $request)
    {
        $validatedData = $request->validate([
            "id" => "nullable|exists:tasks,id",
            "subject" => "required",
            "priority" => "required",
            "description" => "nullable",
            "due_date" => "required",
        ]);

        try {
            $task = Task::find($request->id);
            $task->subject = $validatedData["subject"];
            $task->priority = $validatedData["priority"];
            $task->due_date = $validatedData["due_date"];
            $task->description = $validatedData["description"];
            $task->updated_by_id = auth()->id();
            $original   =  $task->getOriginal() ?? [];
            $dirty      = $task->getDirty() ?? [];
            if($task->save()){
                $arr = ['attributes' => $dirty,
                    'old' => $original,'lead_id' =>  $task->lead_id];
                activity()
                    ->performedOn($task)
                    ->causedBy(auth()->user()->id)
                    ->withProperties($arr)
                    ->event('created')
                    ->log(auth()->user()->first_name . ' has updated a follow-up task');
            }
            return response()->json([
                "status" => "1",
                "message" => "Follow-up task updated Successfully",
                "data" => $task
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "2",
                'msg' => 'Unable to update Task follow up',
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    public function getTask(Request $request)
    {
        $request->validate([
            "task_id" => "required|exists:tasks,id"
        ]);

        $task = Task::find($request->task_id);

        if(!empty($task)){
            return response()->json(['status' => '1', 'msg' => 'Task Found', 'data' => $task]);
        }else{
            return response()->json(['status' => '0', 'msg' => 'Contact Not Found']);
        }
    }

    public function taskStatus(Request $request)
    {
        $validatedData = $request->validate([
            "task_id" => "required|exists:tasks,id",
            "status" => "required|string",
        ]);

        try {
            $task = Task::find($request->task_id);
            $task->status = $request->status;
            $original   =  $task->getOriginal() ?? [];
            $dirty      = $task->getDirty() ?? [];
            if($task->save()){
                $arr = ['attributes' => $dirty,
                    'old' => $original,'lead_id' => $task->lead_id];
                activity()
                    ->performedOn($task)
                    ->causedBy(auth()->user()->id)
                    ->withProperties($arr)
                    ->event('created')
                    ->log(auth()->user()->first_name . ' has changed follow-up task status to '.$request->status);
            }
            return response()->json([
                "status" => "1",
                "msg" => "Task status changed successfully",
                "data" => $task
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "2",
                'msg' => 'Unable to change status',
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    public function getCallLog(Request $request)
    {
        // dd($request->all());
        if(!$request?->id){
            return response()->json(['status' => '0', 'msg' => 'Call log not found']);
        }
        $log = CallLog::with('leads')->whereHas('leads', function($q) use($request){
                                    $q->where('lead_id', $request->id);
                                })->get();
                                
        // $log = CallLog::where('lead_id', $request->id)->get();
        if (count($log) > 0) {
            return response()->json(['status' => '1', 'msg' => 'Call log found', "call_log" => $log]);
        } else {
            return response()->json(['status' => '0', 'msg' => 'Call log not found']);
        }
    }

    public function getMeetingLog(Request $request)
    {
        $log = Meeting::where('lead_id', $request->id)->get();
        if (count($log) > 0) {
            return response()->json(['status' => '1', 'msg' => 'Meeting log found', "log" => $log]);
        } else {
            return response()->json(['status' => '0', 'msg' => 'Meeting log not found']);
        }
    }

    public function getTasks(Request $request)
    {
        $log = Task::where('lead_id', $request->id)->get();
        if (count($log) > 0) {
            return response()->json(['status' => '1', 'msg' => 'Task log found', "log" => $log]);
        } else {
            return response()->json(['status' => '0', 'msg' => 'Task log not found']);
        }
    }
}
