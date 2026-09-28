<?php

namespace App\Console\Commands\PortalLeadPoolAutomation;

use App\Models\Lead;
use App\Models\LeadNote;
use App\Traits\TelegramTrait;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransferInProgressLeadsToPoolCommand extends Command
{
    use TelegramTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'portal-lead-pool-automation:in-progress-lead-transfer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically transfer the lead to the Portal Lead Pool if it remains in the "In Progress" status without any activity for 72 hours.';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $exceptUserIds = [6];
            $exemptAgents = getExemptedAgentIdsFromLeadsTransfer();

            $cutOffDate = "2025-07-04";
            $seventyTwoHoursAgo = Carbon::now()->subHours(72);

            $leads = Lead::where('in_progress_time', '<=', $seventyTwoHoursAgo)
                ->where('lead_status_id', 5) //In Progress
                ->where('portal_lead', false)
                ->whereNull('portal_lead_transferred_date')
                ->whereNotIn('agent_id', array_merge($exceptUserIds, $exemptAgents))
                ->where('region_id', '<>', 2)
                ->whereDate('created_at', '>=', $cutOffDate)
                ->whereNotNull('agent_id')
                ->where('is_archive', false)
                ->whereNull('converted_to_listing_lead')
                ->whereDoesntHave('events', function ($q) use ($seventyTwoHoursAgo) {
                    $q->where('created_at', '>=', $seventyTwoHoursAgo);
                })
                ->whereDoesntHave('notes', function ($q) use ($seventyTwoHoursAgo) {
                    $q->where('created_at', '>=', $seventyTwoHoursAgo)
                        ->whereNotIn('note', [
                            "A reminder has been sent to update this lead, which is currently in the 'In Progress' status.",
                            "A warning has been sent to update this lead, which is currently in the 'In Progress' status."
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
                $leadNote->note = "You have lost this lead due to not taking action within the required timeframe. This lead has now been transferred to Portal Lead Pool, and you will not be able to reclaim this. \nThe lead is currently in ‘In Progress’ status.";
                $leadNote->save();
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return Command::SUCCESS;
    }
}
