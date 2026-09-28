<?php

namespace App\Console\Commands\PortalLeadPoolAutomation;

use App\Models\Lead;
use App\Models\LeadNote;
use App\Models\User;
use App\Traits\TelegramTrait;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendNewLeadReminderCommand extends Command
{
    use TelegramTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'portal-lead-pool-automation:new-lead-send-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a reminder to update the lead if it remains in the "New" status for more than 5 hours.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $exceptUserIds = [6];
        $exemptAgents = getExemptedAgentIdsFromLeadsTransfer();

        $fiveHoursAgo = Carbon::now()->subHours(5);

        $cutOffDate = "2025-04-08";

        $users = User::whereNotIn('id', array_merge($exceptUserIds, $exemptAgents))
            ->whereNotNull('telegram_chatid')
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })
            ->where('status', true)
            ->withCount([
                    'leads' => function ($query) use ($cutOffDate, $fiveHoursAgo) {
                        $query->whereDate('created_at', '>=', $cutOffDate)
                            ->where(function ($query) use ($fiveHoursAgo) {
                                $query->where('created_at', '<=', $fiveHoursAgo)
                                    ->orWhere(function ($q) use ($fiveHoursAgo) {
                                        $q->whereNotNull('taken_from_portal_lead_date')
                                            ->where('taken_from_portal_lead_date', '<=', $fiveHoursAgo);
                                    });
                            })
                            ->whereNull('transfer_to_portal_lead_reminder_sent_at')
                            ->where('lead_status_id', 1) // New
                            ->where('is_archive', false)
                            ->where('portal_lead', false)
                            ->where('region_id', '<>', 2)
                            ->whereNull('converted_to_listing_lead');
                    }]
            )
            ->having('leads_count', '>', 0)
            ->get();


        foreach ($users as $user) {
            if ($user->leads_count > 0) {
                $message = "Reminder:<b> Take Action On Your New Leads</b>\n\n";
                $message .= "Hi {$user->name},\n\n";
                $message .= "You have <b>{$user->leads_count} lead(s)</b> in <b>\"New\"</b> status for more than <b>5 hours</b> without any updates. Please review the lead and take the necessary actions.\n\n";

                $leads = Lead::whereDate('created_at', '>=', $cutOffDate)
                    ->where(function ($query) use ($fiveHoursAgo) {
                        $query->where('created_at', '<=', $fiveHoursAgo)
                            ->orWhere(function ($q) use ($fiveHoursAgo) {
                                $q->whereNotNull('taken_from_portal_lead_date')
                                    ->where('taken_from_portal_lead_date', '<=', $fiveHoursAgo);
                            });
                    })
                    ->whereNull('transfer_to_portal_lead_reminder_sent_at')
                    ->where('lead_status_id', 1) //New
                    ->where('is_archive', false)
                    ->where('portal_lead', false)
                    ->where('region_id', '<>', 2)
                    ->whereNull('converted_to_listing_lead')
                    ->whereNotIn('agent_id', array_merge($exceptUserIds, $exemptAgents))
                    ->where('agent_id', $user->id)
                    ->get();

                if ($user->leads_count <= 10) {
                    $message .= "<b>Leads with No Updates</b>:\n";
                    foreach ($leads ?? [] as $index => $lead) {
                        $name = $lead->first_name ?? "New";
                        $message .= ($index + 1) . ". {$lead->id} - {$name}\n";
                    }
                }

                $this->sendTelegramMessageHtml($message, [$user->telegram_chatid], null, null, 'notification_bot');

                foreach ($leads as $lead) {
                    $lead->update([
                        'transfer_to_portal_lead_reminder_sent_at' => Carbon::now(),
                    ]);
                    $leadNote = new LeadNote();
                    $leadNote->lead_id = $lead->id;
                    $leadNote->note = "A reminder has been sent to update this lead, which is currently in the 'New' status.";
                    $leadNote->save();
                }
            }
        }


        return Command::SUCCESS;
    }
}
