<?php

namespace App\Listeners;

use App\Events\DealSubmittedForApproval;
use App\Events\SalesProgressionAssigned;
use App\Mail\DealForApprovalMail;
use App\Models\NotificationEvent;
use App\Models\Property;
use App\Models\User;
use App\Traits\NotificationManager;
use App\Traits\PusherTrait;
use App\Traits\TelegramTrait;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SalesProgressionAssignedListener
{
    use PusherTrait, NotificationManager, TelegramTrait;
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
        $message = "Congratulations! You have been assigned as Sales Progression for a new deal [# $deal->id].

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

    private function telegramMessageAdmin($deal, $property,$sales_person)
    {
        $message = $sales_person?->first_name . " " . $sales_person?->last_name . " has been assigned as sales progression person for deal [# " . $deal->id . "].

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
    public function handle(SalesProgressionAssigned $event)
    {

        $notification_event = NotificationEvent::where('class_name', 'SalesProgressionAssigned')->where('status', true)->first();

        if (!empty($notification_event)) {
            //$property = Property::where('id', $event->deal->property_id)->first();
            $sale_progression = $event->sale_progression;
            $deal   = $event->deal;
            $property = Property::where('id', $deal->property_id)->first();
            $sales_person = User::where('id',$sale_progression->conveyancer_id)->first();
            $process = $this->salesProgressionAssignedProcess($deal, $notification_event,$sale_progression,$sales_person);

            if (!empty($notification_event) && !empty($process)) {
                $arr['notification'] = $process['notification'];
                $arr['noti_title'] = $process['notification']['title'];
                $arr['noti_message'] = $process['notification']['message'];
                $arr['subscriber'] = $process['users'];
                $arr['url'] = $process['url'];

                $medium = json_decode($notification_event->medium);
                if (in_array('WEB', $medium)) {
                    $this->pusherSingleChannel('web-notification', DealSubmittedForApproval::class, $arr);
                }
                $noti_recipients = User::whereIn('id', $process['users'])->select('id', 'name', 'email', 'telegram_chatid')->get();
//                if (in_array('EMAIL', $medium)) {
//                    // $mailUser = User::where('email','khagesh.office@gmail.com')->first();
//                    foreach ($noti_recipients as $mailUser) {
//                        Mail::to($mailUser->email)->send(new DealForApprovalMail($event->deal, $mailUser));
//                    }
//                }
                if (in_array('TELEGRAM', $medium)) {
                    $testing_chat_id = dealTestingChatId();
                    $chat_id = $testing_chat_id != null ? $testing_chat_id : $sales_person->telegram_chatid;
                    $agent_message = $this->telegramMessageAgent($event->deal, $property);
                    $greeting = "Hi $sales_person->name,";
                    $agent_message = $greeting . "\n\n" . $agent_message;
                    $this->sendTelegramMessageHtml($agent_message, [$chat_id]);

                    $message = $this->telegramMessageAdmin($event->deal, $property, $sales_person);

                    foreach ($noti_recipients as $usr) {
                        $greetings = "Hi $usr->name,";
                        $message_wg = $greetings . "\n\n" . $message;
                        if ($usr->telegram_chatid != null) {
                            $chat_id = $testing_chat_id != null ? $testing_chat_id : $usr->telegram_chatid;

                            $this->sendTelegramMessageHtml($message_wg, [$chat_id]);
                        }
                    }


                }

            }
        }
    }
}
