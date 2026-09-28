<?php

namespace App\Listeners;

use App\Events\EventCreated;
use App\Events\EventReminder;
use App\Mail\EventReminderMail;
use App\Models\NotificationEvent;
use App\Models\User;
use App\Traits\NotificationManager;
use App\Traits\PusherTrait;
use App\Traits\TelegramTrait;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class EventReminderListener
{
use NotificationManager, TelegramTrait, PusherTrait;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\EventReminder  $event
     * @return void
     */
    public function handle(EventReminder $event)
    {

        $notification_event = NotificationEvent::where('class_name', 'EventReminder')->where('status', true)->first();
        $event_starts_in = getTimeDifference($event->event->start_time);
        if(!empty($notification_event)) {

            $process = $this->eventReminderProcess($event->event, $notification_event);

            $calendar = $event->event->load('type');
            if (!empty($notification_event) && !empty($process)) {

                $arr['notification'] = $process['notification'];
                $arr['noti_title'] = $process['notification']['title'];
                $arr['noti_message'] = $process['notification']['message'];
                $arr['subscriber'] = $process['users'];
                $arr['url'] = $process['url'];
                $medium = json_decode($notification_event->medium);
                if (in_array('WEB', $medium)) {
                    $this->pusherSingleChannel('web-notification', EventCreated::class, $arr);
                }
                $noti_recipients = User::whereIn('id', $process['users'])->where('status','1')->select('id', 'name', 'email','telegram_chatid')->get();
               if (in_array('EMAIL', $medium)) {
                           // $mailUser = User::where('email','khagesh.office@gmail.com')->first();
                  foreach ($noti_recipients as $mailUser) {
                       \Mail::to($mailUser->email)->send(new EventReminderMail($calendar,  $mailUser));
                   }
               }
                if (in_array('TELEGRAM', $medium)) {

                    $header = '<b>Calendar Event Reminder</b>';

                    foreach ($noti_recipients as $usr) {
                        if ($usr->telegram_chatid != null) {
                            $message = $header;
                            $message .= '

Just a friendly reminder about the upcoming calendar event titled "'.$event->event->title.'." It\'s set to begin in <b>'.$event_starts_in.'</b>

                            ==========================';
                            $this->sendTelegramMessageHtml($message, [$usr->telegram_chatid]);
                        }
                    }




                }

            }
        }
    }
}
