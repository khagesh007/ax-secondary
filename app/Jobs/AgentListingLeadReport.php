<?php

namespace App\Jobs;

use App\Models\ListingLead;
use App\Models\User;
use App\Traits\TelegramTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AgentListingLeadReport implements ShouldQueue
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
        $agents = User::whereNot('id',172)->where('status', true)->whereHas('roles', function ($query) {
            $query->where('name', 'Agent');
        })->get();
        foreach ($agents as $agent) {
            $listing_leads_count = ListingLead::where('agent_id', $agent->id)
                            ->where('status_id', 2)
                            ->where('is_archive', 0)
                            ->count();

            $message = "<b>AX Secondary CRM</b>\n\n";
            $message .= "‼️<b>Listing Leads in `ASSIGNED` Status</b>‼️\n";
            $message .= "Hi " . $agent->full_name . ',' . "\n\n";
            $message .= "Currently, you have <b>" . $listing_leads_count . " Listing Leads</b> in the <b>ASSIGNED</b> Status." . "\n\n";
            $message .= "Please note: Having Listing Leads in `ASSIGNED` status negatively impacts your KPI’s." . "\n";
            $message .= 'This may even result in Listing Leads being taken away from you.' . "\n\n";
            $message .= 'Please manage your Listing Leads at the earliest.' . "\n\n";
            $message .= "==========================\n";
            if ($listing_leads_count > 2) {
                if($agent->telegram_chatid){
                    $this->sendTelegramMessageHtml($message, [$agent->telegram_chatid], null, null, 'lead_bot');
                }
            }

        }
    }
}
