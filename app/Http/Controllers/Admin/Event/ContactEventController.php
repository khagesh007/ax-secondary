<?php

namespace App\Http\Controllers\Admin\Event;


use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\ContactFollowUp;
use App\Models\Event;
use App\Models\Meeting;
use App\Models\Task;
use App\Traits\GoogleCalendarTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ContactEventController extends Controller
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
                "error" => "Error in ContactEventController". $e->getMessage(),
            ]);
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
               "error" => "Error in ContactEventController". $e->getMessage(),
            ]);
        }
    }


}
