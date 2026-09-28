<?php

namespace App\Http\Controllers\Admin\Event;


use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Lead;
use App\Models\LeadFollowUp;
use App\Models\Meeting;
use App\Traits\GoogleCalendarTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class LeadEventController extends Controller
{



    public function meetingStatus(Request $request)
    {
        $validatedData = $request->validate([
            "meeting_id" => "required|exists:events,id",
            "status" => "required|string",
        ]);

        try {
            $meeting = Event::find($request->meeting_id);
            $meeting->status = $request->status;
            $original = $meeting->getOriginal() ?? [];
            $dirty = $meeting->getDirty() ?? [];
            if ($meeting->save()) {
                $arr = ['attributes' => $dirty,
                    'old' => $original, 'lead_id' => $meeting->lead_id];
                activity()
                    ->performedOn($meeting)
                    ->causedBy(auth()->user()->id)
                    ->withProperties($arr)
                    ->event('created')
                    ->log(auth()->user()->first_name . ' has changed follow-up meeting status to ' . $request->status);
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
                "error" => "Error in LeadEventController". $e->getMessage(),
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

            $event = new Event();
            $event->agent_id = $lead->agent_id ?? null;
            $event->lead_id = $validatedData["lead_id"];
            $event->priority = $validatedData["priority"];
            $event->type = 'Task';
            $event->title = $validatedData["subject"];
            $event->start_time = $validatedData["due_date"];
            $event->end_time = $validatedData["due_date"];
            $event->description = $validatedData["description"];
            $event->created_by_id = auth()->user()->id;
            $original = $event->getOriginal() ?? [];
            $dirty = $event->getDirty() ?? [];
            if ($event->save()) {
                $arr = ['attributes' => $dirty,
                    'old' => $original, 'lead_id' => $event->lead_id];
                activity()
                    ->performedOn($event)
                    ->causedBy(auth()->user()->id)
                    ->withProperties($arr)
                    ->event('created')
                    ->log(auth()->user()->first_name . ' has created a follow-up task');

                return response()->json([
                    "status" => "1",
                    "message" => "Follow-up task Created Successfully",
                    "data" => $event
                ]);
            } else {
                return response()->json([
                    "status" => "0",
                    "message" => "Task Creation Failed",
                    "data" => null
                ]);
            }


        } catch (\Exception $e) {
            return response()->json([
                "status" => "2",
                'msg' => 'Unable to create Task follow up',
                "error" => "Error in LeadEventController". $e->getMessage(),
            ], 500);
        }
    }
    public function updateTask(Request $request)
    {
        $validatedData = $request->validate([
            "id" => "nullable|exists:events,id",
            "subject" => "required",
            "priority" => "required",
            "description" => "nullable",
            "due_date" => "required",
        ]);

        try {
            $task = Event::find($request->id);
            $task->title    = $validatedData["subject"];
            $task->priority     = $validatedData["priority"];
            $task->start_time = $validatedData["due_date"];
            $task->end_time = $validatedData["due_date"];
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
                "message" => "Lead task updated Successfully",
                "data" => $task
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "2",
                'msg' => 'Unable to update Task follow up',
                "error" => "Error in LeadEventController". $e->getMessage(),
            ], 500);
        }
    }


    public function getTask(Request $request)
    {
        $request->validate([
            "task_id" => "required|exists:events,id"
        ]);

        $task = Event::find($request->task_id);

        if(!empty($task)){
            return response()->json(['status' => '1', 'msg' => 'Task Found', 'data' => $task]);
        }else{
            return response()->json(['status' => '0', 'msg' => 'Contact Not Found']);
        }
    }
    public function taskStatus(Request $request)
    {
        $validatedData = $request->validate([
            "task_id" => "required|exists:events,id",
            "status" => "required|string",
        ]);

        try {
            $task = Event::find($request->task_id);
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
                "error" => "Error in LeadEventController". $e->getMessage(),
            ], 500);
        }
    }
}
