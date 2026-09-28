<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Models\User;
use App\Traits\TelegramTrait;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AgentCalendarReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, TelegramTrait;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $environment = \App::environment();
        $agents = User::whereNot('id',172)->where('status', true)->whereHas('roles', function ($query) {
            $query->where('name', 'Agent');
        })
        ->whereDoesntHave('events', function ($query) {
            $query->where('created_at', '>=', now()->subDays(7));
        })
        ->when($environment == 'local', function ($query) {
            $query->limit(5);
        })
        ->get();

        foreach ($agents as $agent) {

            $message = "<b>AX Secondary Calendar Activity Report</b>\n\n";
            $message .= "Dear " . $agent->full_name . ',' . "\n\n";
            $message .= "It has been more than <b>7 DAYS</b> since there have been <b>NO</b> recorded activities in the calendar, including Viewings, Meetings, and Follow-ups." . "\n\n";
            $message .= "Please note: Having no activity in the Calendar negatively impacts your KPI’s." . "\n\n";
            $message .= "This report is generated weekly, and we will keep monitoring and providing updates accordingly." . "\n\n";
            $message .= "==========================\n";
            if($agent->telegram_chatid){
                $this->sendTelegramMessageHtml($message, [$agent->telegram_chatid], null, null, 'notification_bot');
            }

        }
    }
}
