<?php

namespace App\Listeners;

use App\Events\DealCreated;
use App\Events\DealResubmitted;
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

class DealResubmittedListener
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

    private function telegramMessageAgent($deal, $property)
    {
        $message = "Deal [# $deal->id] has been <b>Resubmitted</b> and sent to admin for review.

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

    private function telegramMessageAdmin($deal, $property)
    {
        $message = $deal?->agent?->first_name . " " . $deal?->agent?->last_name . " has <b>Resubmitted</b> a Deal [# " . $deal->id . "] for your review.

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
    public function handle(DealResubmitted $event)
    {

        $notification_event = NotificationEvent::where('class_name', 'DealResubmitted')->where('status', true)->first();
        $deal_approval = $event->approval;
        if(!empty($notification_event)) {
            $process = $this->dealReSubmittedForApprovalProcess($event->deal, $notification_event, $deal_approval);
            $property = Property::where('id', $event->deal->property_id)->first();

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
                $noti_recipients = User::whereIn('id', $process['users'])->where('id','!=',$event->deal->agent_id)->select('id', 'name', 'email','telegram_chatid')->get();
//                if (in_array('EMAIL', $medium)) {
//                    // $mailUser = User::where('email','khagesh.office@gmail.com')->first();
//                    foreach ($noti_recipients as $mailUser) {
//                        Mail::to($mailUser->email)->send(new DealRejectedMail($event->deal,  $mailUser, $deal_approval));
//                    }
//                }

                if (in_array('TELEGRAM', $medium)) {


                    $testing_chat_id = dealTestingChatId();
                    $testing_admin_chat_id = dealTestingAdminChatId();
                    $agent_chat_id = $testing_chat_id != null ? $testing_chat_id : $event->deal->agent->telegram_chatid;
                    $agent_message = $this->telegramMessageAgent($event->deal, $property);
                    $this->sendTelegramMessageHtml($agent_message, [$agent_chat_id]);
                    $message = $this->telegramMessageAdmin($event->deal, $property);
                    foreach ($noti_recipients as $usr) {
                        $chat_id = $testing_admin_chat_id != null ? $testing_admin_chat_id : $usr->telegram_chatid;
                        if ($chat_id != null) {
                            $greetings = "Hi $usr->name,";
                            $message_wg = $greetings . "\n\n" . $message;
                            $this->sendTelegramMessageHtml($message_wg, [$usr->telegram_chatid]);
                        }
                    }




                }

            }
        }
    }
}
