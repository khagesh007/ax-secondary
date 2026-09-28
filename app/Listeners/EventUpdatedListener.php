<?php

namespace App\Listeners;

use App\Events\EventCreated;
use App\Events\EventUpdated;
use App\Mail\EventInvitationMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class EventUpdatedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(EventUpdated $event)
    {
        $calendar = $event->event;
        if ($calendar->email_to_attendees){
            $attendees = $calendar->attendees;
            foreach ($attendees ?? [] as $attendee){
                Mail::to($attendee)->send(new EventInvitationMail($calendar, $attendee));
            }
        }
    }
}
