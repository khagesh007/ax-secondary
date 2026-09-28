<?php

namespace App\Console\Commands\PortalLeadPoolAutomation;

use App\Models\Lead;
use App\Models\LeadNote;
use App\Models\User;
use App\Traits\TelegramTrait;
use Carbon\Carbon;
use Illuminate\Console\Command;

class TransferNewLeadsToPoolCommand extends Command
{
    use TelegramTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'portal-lead-pool-automation:new-lead-transfer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically transfer the lead to the Portal Lead Pool if it remains in the "New" status for 24 hours.';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $exceptUserIds = [6];
        $exemptAgents = getExemptedAgentIdsFromLeadsTransfer();
        $now = Carbon::now();

        $users = User::whereNotIn('id', array_merge($exceptUserIds, $exemptAgents))
            ->whereNotNull('telegram_chatid')
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })
            ->where('status', true)
            ->withCount([
                'leads' => function ($query) use ($now) {
                $query->where('transfer_to_portal_lead_time', '<=', $now)
                    ->where('portal_lead', false)
                    ->whereNull('portal_lead_transferred_date')
                    ->where('lead_status_id', 1) //New
                    ->where('is_archive', false)
                    ->where('region_id', '<>', 2)
                    ->whereNull('converted_to_listing_lead');
                }
            ])
            ->having('leads_count', '>', 0)
            ->get();

        foreach ($users as $user) {
            if ($user->leads_count > 0){
                $leads = Lead::where('transfer_to_portal_lead_time', '<=', $now)
                    ->where('portal_lead', false)
                    ->whereNull('portal_lead_transferred_date')
                    ->where('lead_status_id', 1) //New
                    ->where('is_archive', false)
                    ->where('region_id', '<>', 2)
                    ->whereNull('converted_to_listing_lead')
                    ->whereNotIn('agent_id', array_merge($exceptUserIds, $exemptAgents))
                    ->where('agent_id', $user->id)
                    ->get();

                $message = "<b>Lead Loss Due to No Action</b>\n\n";
                $message .= "Hi {$user->name},\n\n";
                $message .= "You have <b>lost {$user->leads_count} lead(s)</b> due to not taking action within the required timeframe. These leads have now been transferred to <b>Portal Lead Pool</b>, and you will not be able to reclaim them.\n\n";
                $message .= "⚠️ Please note that this will significantly impact your KPI.\n\n";
                if ($user->leads_count <= 10){
                    $message .= "<b>Leads with No Updates</b>:\n";
                    foreach ($leads ?? [] as $index => $lead){
                        $name = $lead->first_name ?? "New";
                        $message .= ($index + 1). ". {$lead->id} - {$name}\n";
                    }
                }

                $this->sendTelegramMessageHtml($message, [$user->telegram_chatid], null, null, 'notification_bot');

                foreach ($leads as $lead) {
                    $lead->update([
                        'previous_agent_id' => $lead->agent_id,
                        'agent_id' => null,
                        'portal_lead' => true,
                        'transfer_to_portal_lead_time' => null,
                        'portal_lead_transferred_date' => now(),
                        'taken_from_portal_lead' => false,
                    ]);

                    $leadNote = new LeadNote();
                    $leadNote->lead_id = $lead->id;
                    $leadNote->note = "You have lost this lead due to not taking action within the required timeframe. This lead has now been transferred to Portal Lead Pool, and you will not be able to reclaim this. \nThe lead is currently in ‘New’ status";
                    $leadNote->save();
                }
            }
        }

        return Command::SUCCESS;
    }
}
