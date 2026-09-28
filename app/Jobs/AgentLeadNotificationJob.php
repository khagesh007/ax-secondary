<?php

namespace App\Jobs;

use App\Models\Lead;
use App\Models\User;
use App\Traits\TelegramTrait;
// use Telegram\Bot\Laravel\Facades\Telegram;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AgentLeadNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use TelegramTrait;
    public $tries = 3;

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
        $agents = User::whereNot('id',172)->where('status', true)->whereHas('roles', function ($query) {
            $query->where('name', 'Agent');
        })->get();
        foreach ($agents as $agent) {
            $lead_count = Lead::where('agent_id', $agent->id)
                            ->where('status', true)
                            ->where('lead_status_id', 1)
                            ->where('is_archive', 0)
                            ->whereNull('converted_to_listing_lead')
                            ->count();

            $message = "<b>AX Secondary CRM</b>\n\n";
            $message .= "Hi " . $agent->full_name . ',' . "\n\n";
            $message .= "Currently, you have <b>" . $lead_count . " Leads</b> in the <b>NEW</b> Status." . "\n\n";
            $message .= "Please note: Having Leads in `NEW` status negatively impacts your KPI’s." . "\n";
            $message .= 'This may even result in leads being taken away from you.' . "\n\n";
            $message .= 'Please manage your leads at the earliest.' . "\n\n";
            $message .= "==========================\n";
            if ($lead_count > 2) {
                if($agent->telegram_chatid){
                    $this->sendTelegramMessageHtml($message, [$agent->telegram_chatid], null, null, 'lead_bot');
                }
            }

        }
    }
}
