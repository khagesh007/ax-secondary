<?php

namespace App\Console\Commands\PortalLeadPoolAutomation;

use App\Models\Lead;
use App\Models\LeadNote;
use App\Traits\TelegramTrait;
use Carbon\Carbon;
use Illuminate\Console\Command;

class TransferUnderOfferLeadsToPoolCommand extends Command
{
    use TelegramTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'portal-lead-pool-automation:under-offer-lead-transfer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically transfer the lead to the Portal Lead Pool if it remains in the "Under Offer" status without activity for 10 days.';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $exceptUserIds = [6];
        $exemptAgents = getExemptedAgentIdsFromLeadsTransfer();

        $cutOffDate = "2025-07-04";

        $tenDaysAgo = Carbon::now()->subDays(10);

        $leads = Lead::where('under_offer_time', '<=', $tenDaysAgo)
            ->where('lead_status_id', 13) // Under Offer
            ->where('portal_lead', false)
            ->whereNull('portal_lead_transferred_date')
            ->whereNotIn('agent_id', array_merge($exceptUserIds, $exemptAgents))
            ->where('region_id', '<>', 2)
            ->where('is_archive', false)
            ->whereNull('converted_to_listing_lead')
            ->whereDate('created_at', '>=', $cutOffDate)
            ->whereDoesntHave('events', function ($q) use ($tenDaysAgo) {
                $q->where('created_at', '>=', $tenDaysAgo);
            })
            ->whereDoesntHave('notes', function ($q) use ($tenDaysAgo) {
                $q->where('created_at', '>=', $tenDaysAgo)
                    ->whereNotIn('note', [
                        "A warning has been sent to update this lead, which is currently in the 'Under Offer' status."
                    ]);
            })
            ->get();

        foreach ($leads as $lead) {
            $lead->previous_agent_id = $lead->agent_id;
            $lead->agent_id = null;
            $lead->portal_lead = true;
            $lead->portal_lead_transferred_date = now();
            $lead->taken_from_portal_lead = false;
            $lead->save();

            $leadNote = new LeadNote();
            $leadNote->lead_id = $lead->id;
            $leadNote->note = "You have lost this lead due to not taking action within the required timeframe. This lead has now been transferred to Portal Lead Pool, and you will not be able to reclaim this. \nThe lead is currently in ‘Under Offer’ status.";
            $leadNote->save();
        }

        return Command::SUCCESS;
    }
}
