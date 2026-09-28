<?php

namespace App\Traits;

use Carbon\Carbon;
use Spatie\GoogleCalendar\Event;

trait GoogleCalendarTrait
{
    public function addEvent($event, $attendees = [])
    {
        try {

            $newEvent = new Event();
            $newEvent->name = $event->title;
            $newEvent->startDateTime = Carbon::parse($event->start_time);
            $newEvent->endDateTime = Carbon::parse($event->end_time);
            $newEvent->description = $event->description;
            $newEvent->location = $event->location;

//            foreach ($attendees ?? [] as $attendee){
//                $newEvent->addAttendee([
//                    'email' => $attendee["email"],
//                    'name' => $attendee["name"],
//                ]);
//            }
            $newEvent->save();
            return $newEvent->id;
        }catch (\Exception $exception){
            return false;
        }
    }

    public function updateEvent($event, $attendees = [])
    {
        try {
            $newEvent = null;
            if (!is_null($event->google_calendar_id)){
                $newEvent = Event::find($event->google_calendar_id);
                if (is_null($newEvent)){
                    $newEvent = new Event();
                }
            }else{
                $newEvent = new Event();
            }
            $newEvent->name = $event->title;
            $newEvent->startDateTime = Carbon::parse($event->start_time);
            $newEvent->endDateTime = Carbon::parse($event->end_time);
            $newEvent->description = $event->description;
            $newEvent->location = $event->location;

//            foreach ($attendees ?? [] as $attendee){
//                $newEvent->addAttendee([
//                    'email' => $attendee["email"],
//                    'name' => $attendee["name"],
//                ]);
//            }
            $newEvent->save();
            return $newEvent->id;
        }catch (\Exception $exception){
            return false;
        }
    }

    public function deleteEvent($event)
    {
        try {
            if (!is_null($event->google_calendar_id)){
                $newEvent = Event::find($event->google_calendar_id);
                if (!is_null($newEvent)){
                    $newEvent->delete();
                }
            }
            return true;
        }catch (\Exception $exception){
            return false;
        }
    }
}


