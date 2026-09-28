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

class AgentIncompleteLeadNotificationJob implements ShouldQueue
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
        $cutoff = config('configurations.cutoff_date');
        $agents = User::whereNot('id', 172)->where('status', true)->whereHas('roles', function ($query) {
            $query->where('name', 'Agent');
        })->get();
        foreach ($agents as $agent) {
            $lead_count = Lead::where('agent_id', $agent->id)
                ->where('status', true)
                ->where('is_archive', 0)
                ->whereNull('converted_to_listing_lead')
                ->whereNotIn('lead_status_id', [4, 12])
                ->where(function ($query) {
                    $query->where('first_name', 'LIKE', '%new%')->orWhereNull('first_name');
                })
                ->whereDate('created_at', '>', $cutoff)
                ->count();

            $message = "<b>AX Secondary CRM</b>\n\n";
            $message .= "🚨<b>Leads with `INCOMPLETE DETAILS`</b>🚨\n\n";
            $message .= "Hi " . $agent->full_name . ',' . "\n\n";
            $message .= "Currently, you have <b>" . $lead_count . " Leads</b> with <b>INCOMPLETE DETAILS</b>." . "\n\n";
            $message .= "Please note: Having Leads with `INCOMPLETE DETAILS` negatively impacts your KPI’s." . "\n";
            $message .= 'This may even result in leads being taken away from you.' . "\n\n";
            $message .= 'Please manage your leads at the earliest.' . "\n\n";
            $message .= '<b>Note:</b> Incomplete details include missing Client Name and/or Property Reference Number for the relevant inquiry' . "\n\n";
            $message .= '<b>Note:</b> This report displays data received after ' . $cutoff . ".\n\n";
            $message .= "==========================\n";
            if ($lead_count > 2) {
                if ($agent->telegram_chatid) {
                    $this->sendTelegramMessageHtml($message, [$agent->telegram_chatid], null, null, 'lead_bot');
                }
            }

            if (env('APP_ENV') == 'local') {
                break;
            }
        }
    }
}
