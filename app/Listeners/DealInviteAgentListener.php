<?php

namespace App\Listeners;

use App\Events\DealInviteAgent;
use App\Events\DealSubmittedForApproval;
use App\Mail\DealForApprovalMail;
use App\Mail\DealInviteAgentMail;
use App\Models\Deal;
use App\Models\NotificationEvent;
use App\Models\Property;
use App\Models\User;
use App\Traits\NotificationManager;
use App\Traits\PusherTrait;
use App\Traits\TelegramTrait;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class DealInviteAgentListener
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

   private function telegramMessageAgent($deal, $property,$invited_by)
    {
        $message = $invited_by->name." has Invited you to add buyer’s or tenant details for a split Deal [$deal->id].

<b>Deal Type:</b> $deal->type
<b>Property:</b> $property?->ref_no - " . $property?->bed->name . " , " . $property?->bath->name . ",  $property?->location
<b>Property Type:</b>  $property?->category
<b>Agent:</b> " . $deal?->agent?->first_name . " " . $deal?->agent?->last_name . "

<b>Transaction Details:
Listing Price:</b> AED " . number_format($property?->price?->price) . "
<b>Deal Value:</b> AED " . number_format($deal->deal_price);
        $message .= "

AX Secondary CRM
==========================
        ";
        return $message;
        }
    public function handle(DealInviteAgent $event)
    {
        $notification_event = NotificationEvent::where('class_name', 'DealInviteAgent')->where('status', true)->first();
        $invited_by = User::where('id', $event->invitation->invited_by_id)->first();

        $invite_to = User::where('id', $event->invitation->invited_to_id)->first();
        $deal = Deal::where('id', $event->invitation->deal_id)->first();

        if (!empty($notification_event)) {
            $property = Property::where('id', $deal->property_id)->first();
            $process = $this->dealAgentInviteProcess($deal, $notification_event, $invited_by, $invite_to);


            if (!empty($notification_event) && !empty($process)) {
                $arr['notification'] = $process['notification'];
                $arr['noti_title'] = $process['notification']['title'];
                $arr['noti_message'] = $process['notification']['message'];
                $arr['subscriber'] = $process['users'];
                $arr['url'] = route('admin.deals_revamped.dealWithInviteLink', ['invite_code'=>$event->invitation->invite_code]).'?deal_id='.$deal->id.'#step-3';
                $medium = json_decode($notification_event->medium);
                if (in_array('WEB', $medium)) {
                    $this->pusherSingleChannel('web-notification', DealInviteAgent::class, $arr);
                }
                $noti_recipients = User::whereIn('id', $process['users'])->select('id', 'name', 'email', 'telegram_chatid')->get();
//                if (in_array('EMAIL', $medium)) {
//                    foreach ($noti_recipients as $mailUser) {
//                        Mail::to($mailUser->email)->send(new DealInviteAgentMail($event->deal, $mailUser));
//                    }
//                }
                if (in_array('TELEGRAM', $medium)) {
                    $testing_chat_id = dealTestingChatId();
                    $agent_chat_id = $testing_chat_id != null ? $testing_chat_id : $invite_to->telegram_chatid;
                    $agent_message = $this->telegramMessageAgent($deal, $property,$invited_by);
                    $this->sendTelegramMessageHtml($agent_message, [$agent_chat_id],$arr['url'], 'Take Action','notification_bot',true);

//                    $message = $this->telegramMessageAdmin($event->deal, $property);
//
//                    foreach ($noti_recipients as $usr) {
//                        if ($usr->telegram_chatid != null) {
//                            $chat_id = $testing_chat_id != null ? $testing_chat_id : $usr->telegram_chatid;
//
//                            $this->sendTelegramMessageHtml($message, [$chat_id], $process['url'], 'Take Action');
//                        }
//                    }


                }

            }
        }
    }
}
