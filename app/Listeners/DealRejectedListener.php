<?php

namespace App\Listeners;

use App\Events\DealCreated;
use App\Events\DealRejected;
use App\Mail\DealCreatedMail;
use App\Mail\DealRejectedMail;
use App\Models\NotificationEvent;
use App\Models\Property;
use App\Models\User;
use App\Traits\NotificationManager;
use App\Traits\PusherTrait;
use App\Traits\TelegramTrait;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class DealRejectedListener
{
    use NotificationManager, TelegramTrait, PusherTrait;
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
     * @param  \App\Events\DealRejected  $event
     * @return void
     */
    private function telegramMessage($deal, $property,$deal_approval)
    {

        $message = "⚠️ Deal [# $deal->id] has been Rejected by ".$deal_approval->actionBy->name.".
<b>Reason:</b> $deal_approval->comment

<b>Deal Type:</b> $deal->type
<b>Property:</b> $property?->ref_no - " . $property?->bed->name . " , " . $property?->bath->name . ",  $property?->location
<b>Property Type:</b> $property?->category
<b>Agent:</b> " . $deal?->agent?->first_name . " " . $deal?->agent?->last_name . "

<b>Transaction Details:</b>
<b>Listing Price:</b> AED " . number_format($property?->price?->price) . "
<b>Deal Value:</b> AED " . number_format($deal->deal_price);
        $message .= "

AX Secondary CRM
=================================
        ";
        return $message;
    }
    public function handle(DealRejected $event)
    {
        $notification_event = NotificationEvent::where('class_name', 'DealRejected')->where('status', true)->first();
        $deal_approval = $event->approval;

        if(!empty($notification_event)) {

            $process = $this->dealRejectedProcess($event->deal, $notification_event, $deal_approval);
            $property = Property::where('id', $event->deal->property_id)->first();

            if (!empty($notification_event) && !empty($process)) {
                $arr['notification'] = $process['notification'];
                $arr['noti_title']   = $process['notification']['title'];
                $arr['noti_message'] = $process['notification']['message'];
                $arr['subscriber']   = $process['users'];
                $arr['url']          = $process['url'];
                $arr['rejected_by']          = $process['rejected_by'];

                $medium = json_decode($notification_event->medium);
                if (in_array('WEB', $medium)) {
                    $this->pusherSingleChannel('web-notification', DealCreated::class, $arr);
                }
                $noti_recipients = User::whereIn('id', $process['users'])->select('id', 'name', 'email','telegram_chatid')->get();
                if (in_array('EMAIL', $medium)) {
                    // $mailUser = User::where('email','khagesh.office@gmail.com')->first();
                    foreach ($noti_recipients as $mailUser) {
                        Mail::to($mailUser->email)->send(new DealRejectedMail($event->deal,  $mailUser, $deal_approval));
                    }
                }

                if (in_array('TELEGRAM', $medium)) {

             $message = $this->telegramMessage($event->deal, $property,$deal_approval);
                    $testing_chat_id = dealTestingChatId();

                    foreach ($noti_recipients as $usr) {
                        $chat_id = $testing_chat_id != null ? $testing_chat_id : $usr->telegram_chatid;
                        if ($chat_id != null) {
                            $greetings = "Hi $usr->name,";
                            $message_wg = $greetings . "\n\n" . $message;
                            $this->sendTelegramMessageHtml($message_wg, [$chat_id]);
                        }
                    }




                }

            }
        }
    }
}
