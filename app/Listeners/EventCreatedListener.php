<?php

namespace App\Listeners;

use App\Events\EventCreated;
use App\Mail\EventInvitationMail;
use App\Models\Event;
use App\Models\NotificationEvent;
use App\Models\User;
use App\Traits\NotificationManager;
use App\Traits\PusherTrait;
use App\Traits\TelegramTrait;
use Illuminate\Support\Facades\Mail;

class EventCreatedListener
{
    use NotificationManager, PusherTrait, TelegramTrait;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {

    }

    private function telegramMsgToAttendees($usr, $event, $calendar, $header)
    {

        $message = $header;
        $message .= '

        <b>Event Type:</b> ' . $calendar?->type?->name . '
        <b>Dates:</b> ' . date('d/m/y h:i A', strtotime($event->event->start_time)) . ' - ' . date('d/m/y h:i A', strtotime($event->event->end_time));
        if ($event->event->location != "") {
            $message .= '
        <b>Location:</b> ' . $event->event->location;
        }
        if ($event->event->contact != "" && $event->event->contact != null) {
            $message .= '
            <b>Contact:</b> ' . $event->event->contact?->first_name . ' ' . $event->event->contact?->last_name;
        }
        if ($event->event->contact != "" && $event->event->contact != null) {
            $message .= '<b>Lead:</b> ' . $event->event->lead?->first_name . ' ' . $event->event->lead?->last_name;
        }
        if ($event->event->listing_lead != "" && $event->event->listing_lead != null) {
            $message .= '<b>Listing Lead:</b> ' . $event->event->listing_lead?->first_name . ' ' . $event->event->listing_lead?->last_name;
        }
        if ($event->event->contact != "" && $event->event->contact != null) {
            $message .= '
        <b>Property:</b> ' . $event->event?->property?->ref_no . ' ' . $event->event?->property?->title;
        }
        $message .= '
        <b>Title:</b> ' . $event->event->title;
        if ($event->event->description != "") {
            $message .= '
        <b>Description:</b> ' . $event->event->description;
        }
        $message .= '
        ==============================';

        $this->sendTelegramMessageHtml($message, [$usr->telegram_chatid], null, 'Add to Google Calendar', 'notification_bot', true);

    }

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle(EventCreated $event)
    {
        $notification_event = NotificationEvent::where('class_name', 'EventCreated')->where('status', true)->first();

        if (!empty($notification_event)) {
            $process = $this->eventCreatedProcess($event->event, $notification_event);
            $calendar = Event::where('id', $event->event->id)->with('type', 'contact', 'lead', 'property', 'attendees')->first();
            if (!empty($notification_event) && !empty($process)) {
                $arr['notification'] = $process['notification'];
                $arr['noti_title'] = $process['notification']['title'];
                $arr['noti_message'] = $process['notification']['message'];
                //  $arr['subscriber'] = $process['users'];

                if ($event->event->send_notification == 1) {
                    $arr['subscriber'] = $process['users'];

                } else {
                    $arr['subscriber'] = [$event->event->agent_id];

                }

                $arr['url'] = $process['url'];
//                $objDateTimeStart = new \DateTime($event->event->start_time);
//                $google_start_time = $objDateTimeStart->format('Ymd\THis\Z');
//                $objDateTimeEnd = new \DateTime($event->event->end_time);
//                $google_end_time = $objDateTimeEnd->format('Ymd\THis\Z');
//                $google_calendar_link = "https://www.google.com/calendar/event?action=TEMPLATE&text=" . $event?->event?->title . "&dates=" . $google_start_time . "4/" . $google_end_time . "4&details=" . $event?->event?->description . "&location=" . $event?->event?->location . "&trp=true";
                $medium = json_decode($notification_event->medium);
                if (in_array('WEB', $medium)) {
                    $this->pusherSingleChannel('web-notification', EventCreated::class, $arr);
                }
                //remove $notification_event->agent_id from $process['users'] array
                $user_id = $event->event->agent_id;
                $user = User::where('id', $user_id)->first();

                $attendees = array_filter($arr['subscriber'], function ($element) use ($user_id) {
                    return $element !== $user_id;
                });
                $noti_recipients = User::whereIn('id', $attendees)->select('id', 'name', 'email', 'telegram_chatid')->get();

                if (in_array('EMAIL', $medium)) {
                    // $mailUser = User::where('email','khagesh.office@gmail.com')->first();
                    foreach ($noti_recipients ?? [] as $mailUser) {
                        Mail::to($mailUser->email)->send(new EventInvitationMail($calendar, $mailUser));
                    }
                }
                if (in_array('TELEGRAM', $medium)) {
                    //        sending telegram message to main user
                    $header = " A new event titled <b>" . $event->event->title . "</b> has been added to your calendar. Please see the details below:";
                    $this->telegramMsgToAttendees($user, $event, $calendar, $header);
                    ///sending telegram message to attendees
                    $header = "You have received an invitation for an event titled '<b>" . $event->event->title . "</b>' from " . $user->name . " Please see the details below:";
                    foreach ($noti_recipients ?? [] as $usr) {

                        if ($usr->telegram_chatid != null) {
                            $this->telegramMsgToAttendees($usr, $event, $calendar, $header);
                        }
                    }

                }

            }
        }
    }

}
