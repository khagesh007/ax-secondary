<?php

namespace App\Jobs;

use App\Events\EventCreated;
use App\Models\NotificationEvent;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendEventReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
public $event;
public $diffence;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($event,$diffence)
    {
        $this->event = $event;
        $this->diffence = $diffence;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $notification_event = NotificationEvent::where('class_name', 'EventCreated')->where('status', true)->first();
        if(!empty($notification_event)) {
            $process = $this->eventCreatedProcess($event->event, $notification_event);
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
                $noti_recipients = User::whereIn('id', $process['users'])->select('id', 'name', 'email','telegram_chatid')->get();
//               if (in_array('EMAIL', $medium)) {
//                           // $mailUser = User::where('email','khagesh.office@gmail.com')->first();
//                  foreach ($noti_recipients as $mailUser) {
//                       Mail::to($mailUser->email)->send(new EventInvitationMail($calendar,  $mailUser));
//                   }
//               }
                if (in_array('TELEGRAM', $medium)) {

                    $header = '<b>New calendar Event Created</b>';

                    foreach ($noti_recipients as $usr) {
                        if ($usr->telegram_chatid != null) {
                            $message = $header;
                            $message .= '
Event Type: Task
Dates: ' . date('d/m/y H:i',strtotime($event->event->start_time)) . ' - ' . date('d/m/y H:i',strtotime($event->event->end_time)) . '
Location: ' . $event->event->location . '
Contact: ' . $event->event->contact?->first_name . ' ' . $event->event->contact?->last_name . '
Lead: ' . $event->event->lead?->first_name . ' ' . $event->event->lead?->last_name . '
Title: ' . $event->event->title . '
Description: ' . $event->event->description . '
Please have a look. <a href="' . $arr['url'] . '">View</a>
AX Secondary CRM';
                            $this->sendTelegramMessageHtml($message, [$usr->telegram_chatid]);
                        }
                    }




                }

            }
        }
    }
}
