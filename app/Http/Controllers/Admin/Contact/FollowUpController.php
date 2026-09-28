<?php

namespace App\Http\Controllers\Admin\Contact;


use App\Models\Contact;
use App\Models\ContactFollowUp;
use App\Http\Controllers\Controller;
use App\Models\CallLog;
use App\Models\Event;
use App\Models\Meeting;
use App\Models\Task;
use Auth;
use Illuminate\Http\Request;

class FollowUpController extends Controller
{
    public function storeTaskFollowUp(Request $request)
    {
        $validatedData = $request->validate([
            "contact_id" => "required|exists:contacts,id",
            "time" => "required",
            "note" => "nullable",
        ]);

        try {
            $obj = new ContactFollowUp();
            $obj->contact_id = $validatedData["contact_id"];
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
            ]);
        }
    }

    public function storeMeeting(Request $request)
    {
        $validatedData = $request->validate([
            "contact_id" => "required|exists:contacts,id",
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
            $contact = Contact::find($validatedData["contact_id"]);
            $obj = new ContactFollowUp();
            $obj->contact_id = $validatedData["contact_id"];
            $obj->type = "MEETING";
            $obj->created_by_id = Auth::id();
            if ($obj->save()) {

                $meeting = new Meeting();
                $meeting->user_id = $contact->agent_id;
                $meeting->contact_follow_up_id = $obj->id;
                $meeting->contact_id = $validatedData["contact_id"];
                $meeting->subject = $validatedData["subject"];
                $meeting->location = $validatedData["location"];
                $meeting->start_time = $validatedData["start_time"];
                $meeting->end_time = $validatedData["end_time"];
                $meeting->description = $validatedData["description"];
                $meeting->status = 'PENDING';
                $meeting->created_by_id = Auth::id();
                if ($meeting->save()) {

                    $meeting->attendees()->sync($request->attendees ?? []);
                    $event_create = (isset($request->create_event) && $request->create_event == false) ? false : true;
                    if($event_create == true) {
                        $event = new Event();
                        $event->agent_id = $contact->agent_id ?? null;
                        $event->contact_id = $validatedData["contact_id"];
//                    $event->meeting_id = $meeting->id;
                        $event->type = 'Meeting';
                        $event->title = $validatedData["subject"];
                        $event->start_time = $validatedData["start_time"];
                        $event->end_time = $validatedData["end_time"];
                        $event->location = $meeting->location;
                        $event->description = $meeting->description;
                        $event->save();
                        $event->attendees()->sync($request->attendees ?? []);
                    }

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
            ]);
        }
    }

    public function updateMeeting(Request $request)
    {
        $validatedData = $request->validate([
            "id" => "required|exists:meetings,id",
            "contact_id" => "required|exists:contacts,id",
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
            $meeting->updated_by_id = auth()->id();
            $meeting->save();
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
            ]);
        }
    }

    public function getMeeting(Request $request)
    {
        $request->validate([
            "meeting_id" => "required|exists:meetings,id"
        ]);

        $meeting = Meeting::find($request->meeting_id);

        $meeting->load(["attendees"]);

        if (!empty($meeting)) {
            return response()->json(['status' => '1', 'msg' => 'Meeting Found', 'data' => $meeting]);
        } else {
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
            $meeting->save();
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
            ]);
        }
    }

    public function storeTask(Request $request)
    {
        $validatedData = $request->validate([
            "contact_id" => "nullable|exists:contacts,id",
            "subject" => "required",
            "priority" => "required",
            "description" => "nullable",
            "due_date" => "required",
        ]);

        try {
            $contact = Contact::find($validatedData["contact_id"]);
            $obj = new ContactFollowUp();
            $obj->contact_id = $validatedData["contact_id"];
            $obj->type = "TASK";
            $obj->created_by_id = Auth::id();
            if ($obj->save()) {

                $meeting = new Task();
                $meeting->user_id = $contact->agent_id;
                $meeting->contact_follow_up_id = $obj->id;
                $meeting->contact_id = $validatedData["contact_id"];
                $meeting->subject = $validatedData["subject"];
                $meeting->priority = $validatedData["priority"];
                $meeting->due_date = $validatedData["due_date"];
                $meeting->description = $validatedData["description"];
                $meeting->status = 'pending';
                $meeting->created_by_id = Auth::id();
                if ($meeting->save()) {

                    $event_create = (isset($request->create_event) && $request->create_event == false) ? false : true;
                    if($event_create == true) {
                        $event = new Event();
                        $event->agent_id = $contact->agent_id ?? null;
                        $event->contact_id = $validatedData["contact_id"];
                        $event->priority = strtolower($validatedData["priority"]);
                        $event->type = 'Task';
                        $event->title = $validatedData["subject"];
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
            ]);
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
            $task->save();
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
            ]);
        }
    }

    public function getTask(Request $request)
    {
        $request->validate([
            "task_id" => "required|exists:tasks,id"
        ]);

        $task = Task::find($request->task_id);

        if (!empty($task)) {
            return response()->json(['status' => '1', 'msg' => 'Task Found', 'data' => $task]);
        } else {
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
            $task->save();
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
            ]);
        }
    }

    public function getCallLog(Request $request)
    {
        if (\Auth::check()) {
            $notAdmin = auth()->user()->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent","Telesales", "Telesales Head"]);
            $childrenIds = getUserChildrenIds(auth()->id());
            $log = CallLog::where('contact_id', $request->id)->when($notAdmin, function($q) use($childrenIds){
                $q->whereIn('user_id', $childrenIds);
            })->get();
        }

        if (count($log) > 0) {
            return response()->json(['status' => '1', 'msg' => 'Call log found', "call_log" => $log]);
        } else {
            return response()->json(['status' => '0', 'msg' => 'Call log not found']);
        }
    }

    public function getMeetingLog(Request $request)
    {
        $log = Meeting::where('contact_id', $request->id)->get();
        if (count($log) > 0) {
            return response()->json(['status' => '1', 'msg' => 'Meeting log found', "log" => $log]);
        } else {
            return response()->json(['status' => '0', 'msg' => 'Meeting log not found']);
        }
    }

    public function getTasks(Request $request)
    {
        $log = Task::where('contact_id', $request->id)->get();
        if (count($log) > 0) {
            return response()->json(['status' => '1', 'msg' => 'Task log found', "log" => $log]);
        } else {
            return response()->json(['status' => '0', 'msg' => 'Task log not found']);
        }
    }
}
