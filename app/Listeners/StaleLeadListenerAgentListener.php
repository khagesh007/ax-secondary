<?php

namespace App\Listeners;

use App\Events\StaleLeadAgentNotification;
use App\Mail\EventInvitationMail;
use App\Models\Event;
use App\Models\NotificationEvent;
use App\Models\User;
use App\Traits\TelegramTrait;
use Illuminate\Support\Facades\Mail;
use App\Traits\NotificationManager;
use App\Traits\PusherTrait;

class StaleLeadListenerAgentListener
{
    use NotificationManager, PusherTrait, TelegramTrait;

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
     * @param object $event
     * @return void
     */
    public function handle(StaleLeadAgentNotification $event)
    {

        $notification_event = NotificationEvent::where('class_name', 'StaleLeadAgentNotification')->where('status', true)->first();

        if (!empty($notification_event)) {
            $process = $this->eventCreatedProcess($event->event, $notification_event);
            $calendar = Event::where('id', $event->event->id)->with('type', 'contact', 'lead', 'property', 'attendees')->first();
            if (!empty($notification_event) && !empty($process)) {
                $arr['notification'] = $process['notification'];
                $arr['noti_title'] = $process['notification']['title'];
                $arr['noti_message'] = $process['notification']['message'];

                if ($event->event->send_notification == 1) {
                    $arr['subscriber'] = $process['users'];

                } else {
                    $arr['subscriber'] = [$event->event->agent_id];

                }

                $arr['url'] = $process['url'];
                $medium = json_decode($notification_event->medium);

                //remove $notification_event->agent_id from $process['users'] array
                $user_id = $event->event->agent_id;
                $user = User::where('id', $user_id)->first();

                $attendees = array_filter($arr['subscriber'], function ($element) use ($user_id) {
                    return $element !== $user_id;
                });

                $noti_recipients = User::whereIn('id', $attendees)->select('id', 'name', 'email', 'telegram_chatid')->get();

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
