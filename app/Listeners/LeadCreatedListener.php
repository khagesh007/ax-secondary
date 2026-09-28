<?php

namespace App\Listeners;

use App\Events\LeadCreated;
use App\Models\Lead;
use App\Models\User;
use App\Traits\TelegramTrait;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LeadCreatedListener
{
    use TelegramTrait;

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
     * @param  \App\Events\LeadCreated  $event
     * @return void
     */
    public function handle(LeadCreated $event)
    {
        //\Log::info($event->lead);
    }

    private function sendTelegramNotification(Lead $lead)
    {
        $user = User::find($lead->agent_id);
        if ($user->telegram_chatid) {
            $fiveHourTime = Carbon::now()->addHours(5)->format('H:i A');
            $message = "<b>Reminder to Update Your Lead</b>\n\n";
            $message .= "Hi {$user->name},\n\n";
            $message .= "This is a reminder to update your lead within the next <b>5 hours(until {$fiveHourTime})</b>.\n\n";

            $message .= "<b>Lead(s) Details:</b>\n\n";
            $message .= "- <a href='" . route('admin.work.leads.index') . "?id=" . $lead->id . "'>" . $lead->id . "</a>";

            $this->sendTelegramMessageHtml($message, [$user->telegram_chatid], null, null, 'lead_bot');
        }


    }
}
