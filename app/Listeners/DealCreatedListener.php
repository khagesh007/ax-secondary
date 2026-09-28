<?php

namespace App\Listeners;

use App\Events\DealCreated;
use App\Events\EventCreated;
use App\Mail\DealCreatedMail;
use App\Mail\EventInvitationMail;
use App\Models\Event;
use App\Models\NotificationEvent;
use App\Models\User;
use App\Traits\NotificationManager;
use App\Traits\PusherTrait;
use App\Traits\TelegramTrait;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class DealCreatedListener
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
     * @param  \App\Events\DealCreated  $event
     * @return void
     */
    public function handle(DealCreated $event)
    {

        $notification_event = NotificationEvent::where('class_name', 'DealCreated')->where('status', true)->first();

        if(!empty($notification_event)) {
            $process = $this->dealCreatedProcess($event->deal, $notification_event);


            if (!empty($notification_event) && !empty($process)) {
                $arr['notification'] = $process['notification'];
                $arr['noti_title']   = $process['notification']['title'];
                $arr['noti_message'] = $process['notification']['message'];
                $arr['subscriber']   = $process['users'];
                $arr['url']          = $process['url'];

                $medium = json_decode($notification_event->medium);
                if (in_array('WEB', $medium)) {
                    $this->pusherSingleChannel('web-notification', DealCreated::class, $arr);
                }
                $noti_recipients = User::whereIn('id', $process['users'])->select('id', 'name', 'email','telegram_chatid')->get();
                if (in_array('EMAIL', $medium)) {
                    // $mailUser = User::where('email','khagesh.office@gmail.com')->first();
                    foreach ($noti_recipients as $mailUser) {
                        Mail::to($mailUser->email)->send(new DealCreatedMail($event->deal,  $mailUser));
                    }
                }
                if (in_array('TELEGRAM', $medium)) {

                    $header = '<b>New Deal Created</b>';

                    $testing_chat_id = dealTestingChatId();
                    foreach ($noti_recipients as $usr) {

                        $agent_chat_id = $testing_chat_id != null ? $testing_chat_id : $usr->telegram_chatid;
                        if ($usr->telegram_chatid != null) {
                            $message = $header;
                            $message .="
Hi ".$usr->name.", A new deal has been created with following details.\n\n";
                            $message .= '
<b>Deal Type:</b> ' . $event->deal->type . '
<b>Contact:</b> ' . $event->deal?->contact?->first_name . ' '.$event->deal?->contact?->last_name . '
<b>Property:</b> ' . $event->deal?->property?->ref_no . ' - '.$event->deal?->property?->title . '


AX Secondary CRM
===================
';
                            $this->sendTelegramMessageHtml($message, [$agent_chat_id]);
                        }
                    }




                }

            }
        }

    }
}
