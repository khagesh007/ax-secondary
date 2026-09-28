<?php

namespace App\Listeners;

use App\Events\DealApproved;
use App\Models\NotificationEvent;
use App\Models\Property;
use App\Models\User;
use App\Traits\NotificationManager;
use App\Traits\PusherTrait;
use App\Traits\TelegramTrait;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DealApprovedListener
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
     * @param  \App\Events\DealApproved  $event
     * @return void
     */
    private function telegramMessage($deal, $property)
    {

        $message = "🎉 Congratulations! Deal [# $deal->id] has been <b>Approved</b> and sent to Finance.

Deal Type: $deal->type
Property: $property?->ref_no - " . $property?->bed->name . " , " . $property?->bath->name . ",  $property?->location
Property Type:  $property?->category
Agent: " . $deal?->agent?->first_name . " " . $deal?->agent?->last_name . "

Transaction Details:
Listing Price: AED " . number_format($property?->price?->price) . "
Deal Value: AED " . number_format($deal->deal_price);
        $message .= "

AX Secondary CRM
=================================
        ";
        return $message;
    }
    public function handle(DealApproved $event)
    {
        $notification_event = NotificationEvent::where('class_name', 'DealApproved')->where('status', true)->first();

        if(!empty($notification_event)) {
            $property = Property::where('id', $event->deal->property_id)->first();
            $process = $this->dealApprovedProcess($event->deal, $notification_event);
            if (!empty($notification_event) && !empty($process)) {
                $arr['notification'] = $process['notification'];
                $arr['noti_title'] = $process['notification']['title'];
                $arr['noti_message'] = $process['notification']['message'];
                $arr['subscriber'] = $process['users'];
                $arr['url'] = $process['url'];
                $medium = json_decode($notification_event->medium);
                if (in_array('WEB', $medium)) {
                    $this->pusherSingleChannel('web-notification', DealApproved::class, $arr);
                }
                $noti_recipients = User::whereIn('id', $process['users'])->select('id', 'name', 'email','telegram_chatid')->get();
                if (in_array('EMAIL', $medium)) {
                    // $mailUser = User::where('email','khagesh.office@gmail.com')->first();
//                    foreach ($noti_recipients as $mailUser) {
//                        Mail::to($mailUser->email)->send(new EventInvitationMail($calendar,  $mailUser));
//                    }
                }
                if (in_array('TELEGRAM', $medium)) {
                    $testing_chat_id = dealTestingChatId();
                    foreach ($noti_recipients as $usr) {
                        $chat_id = $testing_chat_id != null ? $testing_chat_id : $usr->telegram_chatid;
                        if ($chat_id != null) {
                            $greetings = "Hi $usr->name,";
                            $message = $this->telegramMessage($event->deal, $property);
                            $message_wg = $greetings . "\n\n" . $message;
                            $this->sendTelegramMessageHtml($message_wg, [$chat_id]);
                        }
                    }




                }

            }
        }
    }
}
