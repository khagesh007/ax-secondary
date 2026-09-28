<?php

namespace App\Listeners;


use App\Events\ExclusiveExpiring;
use App\Mail\ExclusiveExpiringMail;
use App\Models\NotificationEvent;
use App\Models\User;
use App\Traits\NotificationManager;
use App\Traits\PusherTrait;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class ExclusiveExpiringListener
{
    use NotificationManager, PusherTrait;
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
     * @param  \App\Events\ExclusiveExpiring  $event
     * @return void
     */
    public function handle(ExclusiveExpiring $event)
    {
     try {
         $notification_event = NotificationEvent::where('class_name', 'ExclusiveExpiring')->where('status',1)->first();
         if(!empty($notification_event)) {
             $process = $this->exclusiveExpiringProcess($event->property, $event->days, $notification_event);
             if (!empty($process)) {
                 $arr = [];
                 $arr['notification'] = $process['notification'];
                 $arr['noti_title'] = $process['notification']['title'];
                 $arr['noti_message'] = $process['notification']['message'];
                 $arr['subscriber'] = $process['users'];
                 $medium = json_decode($notification_event?->medium) ?? [];

                 if (in_array('WEB', $medium)) {
                     $this->pusherSingleChannel('web-notification', ExclusiveExpiring::class, $arr);
                 }

                 if (in_array('EMAIL', $medium)) {
                     $mail_recipients = User::whereIn('id', $process['users'])->select('id', 'name', 'email')->get();
                     foreach ($mail_recipients as $mailUser) {
                         Mail::to($mailUser->email)->send(new ExclusiveExpiringMail($event->property, $event->days, $mailUser, $process));
                     }
                 }

             }
         }
     } catch (\Exception $e) {
         \Log::info('Error Import INDEX no.   Data: '.$e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
     }
    }
}
