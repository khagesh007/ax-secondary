<?php

namespace App\Listeners;

use App\Events\DealNoteAdded;
use App\Models\Deal;
use App\Models\NotificationEvent;
use App\Models\User;
use App\Traits\NotificationManager;
use App\Traits\PusherTrait;
use App\Traits\TelegramTrait;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DealNoteAddedListener
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

    private function telegramMsgToUser($sendUser, $notificationRecepient, $dealNote, $deal, $header)
    {

        $message = $header;

        $stepName = "Property Information";
        if ($dealNote->step == 1) {
            $stepName = "Property Information";
        }

        if ($dealNote->step == 2) {
            $stepName = ($deal->type == "Rental" ? "Owner" : "Seller") . " Information";
        }

        if ($dealNote->step == 3) {
            $stepName = ($deal->type == "Rental" ? "Tenants" : "Buyers") . " Information";
        }

        if ($dealNote->step == 4) {
            $stepName = "Attachments";
        }

        if ($dealNote->step == 5) {
            $stepName = "Finance";
        }

        $message .= " at " . $stepName . ".";
        $message .= $dealNote->note;

        $this->sendTelegramMessage($message, [$notificationRecepient->telegram_chatid], 'notification_bot');
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\DealNoteAdded  $event
     * @return void
     */
    public function handle(DealNoteAdded $event)
    {
        $dealNote = $event->dealNote;

        $deal = Deal::find($dealNote->deal_id);


        $notification_event = NotificationEvent::where('class_name', 'DealNoteAdded')->where('status', true)->first();

        if (!empty($notification_event)) {
            $notificationRecepient = null;
            $dealNoteCreatedBy = User::find($dealNote->created_by_id);
            if ($dealNoteCreatedBy->hasRoles(["Super Admin", "Admin"])) {
                $notificationRecepient = User::find($deal->agent_id);
            } else {
                $notificationRecepient = User::where("roles", function ($q) {
                    $q->where("name", "Super Admin");
                    $q->orWhere("name", "Admin");
                })->first();
            }
            if (!is_null($notificationRecepient)) {
                $process = $this->dealNoteCreatedProcess($dealNote, $notification_event, $notificationRecepient->id);
                if (!empty($notification_event) && !empty($process)) {
                    $arr['notification'] = $process['notification'];
                    $arr['noti_title'] = $process['notification']['title'];
                    $arr['noti_message'] = $process['notification']['message'];

                    $arr['subscriber'] = [$notificationRecepient->id];

                    $arr['url'] = $process['url'];
                    $medium = json_decode($notification_event->medium);
                    if (in_array('WEB', $medium)) {
                        $this->pusherSingleChannel('web-notification', DealNoteAdded::class, $arr);
                    }

                    if (in_array('EMAIL', $medium)) {
                    }

                    if (in_array('TELEGRAM', $medium)) {
                        $header = $dealNoteCreatedBy->first_name . " has added note for deal #" . $deal->id;
                        $this->telegramMsgToUser($dealNoteCreatedBy, $notificationRecepient, $dealNote, $deal, $header);
                    }
                }
            }
        }
    }
}
