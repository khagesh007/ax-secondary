<?php

namespace App\Console\Commands\PortalLeadPoolAutomation;

use App\Models\Group;
use App\Models\Lead;
use App\Models\LeadNote;
use App\Models\User;
use App\Traits\TelegramTrait;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendUnderOfferLeadWarningCommand extends Command
{
    use TelegramTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'portal-lead-pool-automation:under-offer-lead-send-warning';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a warning 1 day before the 10-day inactivity deadline (i.e., on the 9th day) for leads in \'Under Offer\' status with no activity, informing agents that the lead will be transferred to the Portal Lead Pool if no action is taken.';

    private function sendMessageToManager($manager, $agentMessages, $transferTime)
    {
        $message = "<b>Leads at Risk of Transfer</b>\n\n";
        $message .= "Hi {$manager->name},\n\n";
        $message .= "The following agents have leads in <b>\"Under Offer\"</b> Status that will be transferred to the <b>Portal Lead Pool</b> if left unactioned within the next <b>1 day (until {$transferTime})</b>: Please note that once transferred, agent will not be able to reclaim these leads.\n\n";
        $message .= $agentMessages;

        if ($manager->telegram_chatid) {
            $this->sendTelegramMessageHtml($message, [$manager->telegram_chatid], null, null, 'notification_bot');
        }
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $exemptNotes = [
            "A warning has been sent to update this lead, which is currently in the 'Under Offer' status.",
        ];

        $exceptUserIds = [6];
        $exemptAgents = getExemptedAgentIdsFromLeadsTransfer();

        $betweenStartTime = Carbon::now()->subDays(9)->startOfHour()->subMinutes(15);
        $betweenEndTime = Carbon::now()->subDays(9)->endOfHour()->addMinutes(15);

        $nineDaysAgo = Carbon::now()->subDays(9);

        $cutOffDate = "2025-07-04";

        $groups = Group::with(["user"])
            ->whereNull("parent_id")
            ->get();

        foreach ($groups as $group) {
            $manager = $group->user;
            if ($manager) {
                $users = User::whereNotIn('id', array_merge($exceptUserIds, $exemptAgents))
                    ->whereNotNull('telegram_chatid')
                    ->whereHas('roles', function ($q) {
                        $q->where('name', 'Agent');
                    })
                    ->where('status', true)
                    ->where('parent_id', $manager->id)
                    ->withCount([
                        'leads' => function ($query) use ($cutOffDate, $betweenStartTime, $betweenEndTime, $nineDaysAgo, $exemptNotes) {
                            $query->where('portal_lead', false)
                                ->whereNull('portal_lead_transferred_date')
                                ->whereNull('under_offer_warning_sent_at')
                                ->where('lead_status_id', 13) // "Under Offer"
                                ->where('is_archive', false)
                                ->whereNull('converted_to_listing_lead')
                                ->where('region_id', '<>', 2)
                                ->whereDate('created_at', '>=', $cutOffDate)
                                ->whereBetween('under_offer_time', [$betweenStartTime, $betweenEndTime])
                                ->whereDoesntHave('events', function ($q) use ($nineDaysAgo) {
                                    $q->where('created_at', '>=', $nineDaysAgo);
                                })
                                ->whereDoesntHave('notes', function ($q) use ($nineDaysAgo, $exemptNotes) {
                                    $q->where('created_at', '>=', $nineDaysAgo)
                                        ->whereNotIn('note', $exemptNotes);
                                });
                        }
                    ])
                    ->having('leads_count', '>', 0)
                    ->get();

                $managerMessage = "";

                $oneDayLater = Carbon::now()->addDay();

                $transferTime = $oneDayLater->format('h:i A');

                foreach ($users as $user) {
                    if ($user->leads_count > 0){
                        $managerMessage .= "{$user->name} - {$user->leads_count} lead(s)\n";

                        $message = "<b>Urgent: Action Required on Your Leads</b>\n\n";
                        $message .= "Hi {$user->name},\n\n";
                        $message .= "You currently have <b>{$user->leads_count} lead(s)</b> in <b>\"Under Offer\"</b> Status. If these leads remain unactioned for the next <b>1 day (until {$transferTime})</b>, they will be <b>removed from your account</b> and transferred to the <b>Portal Lead Pool</b>. Please note that once transferred, you will not be able to reclaim these leads.\n\n";
                        $message .= "⚠️ Failure to act will significantly impact your KPI.\n\n";
                        $message .= "📅 Report Generated On: " . Carbon::now()->format('F d, Y - h:i A'). "\n\n";

                        $leads = Lead::where('portal_lead', false)
                            ->whereNull('portal_lead_transferred_date')
                            ->whereNull('under_offer_warning_sent_at')
                            ->where('lead_status_id', 13) // "Under Offer"
                            ->where('region_id', '<>', 2)
                            ->where('is_archive', false)
                            ->whereNull('converted_to_listing_lead')
                            ->whereDate('created_at', '>=', $cutOffDate)
                            ->whereBetween('under_offer_time', [$betweenStartTime, $betweenEndTime])
                            ->whereDoesntHave('events', function ($q) use ($nineDaysAgo) {
                                $q->where('created_at', '>=', $nineDaysAgo);
                            })
                            ->whereDoesntHave('notes', function ($q) use ($nineDaysAgo, $exemptNotes) {
                                $q->where('created_at', '>=', $nineDaysAgo)
                                    ->whereNotIn('note', $exemptNotes);
                            })
                            ->whereNotIn('agent_id', array_merge($exceptUserIds, $exemptAgents))
                            ->where('agent_id', $user->id)
                            ->get();

                        if ($user->leads_count < 10){
                            $message .= "<b>Leads with No Updates</b>:\n";
                            foreach ($leads as $index => $lead) {
                                $name = $lead->first_name ?? "New";
                                $message .= ($index + 1) . " - {$lead->id} - {$name}\n";
                            }
                        }

                        $this->sendTelegramMessageHtml($message, [$user->telegram_chatid], null, null, 'notification_bot');

                        foreach ($leads as $lead) {
                            $lead->update([
                                'under_offer_warning_sent_at' => Carbon::now(),
                            ]);

                            $leadNote = new LeadNote();
                            $leadNote->lead_id = $lead->id;
                            $leadNote->note = "A warning has been sent to update this lead, which is currently in the 'Under Offer' status.";
                            $leadNote->save();
                        }
                    }
                }

                if (!empty($managerMessage)) {
                    if (!in_array($manager->id, $exceptUserIds)) {
                        $this->sendMessageToManager($manager, $managerMessage, $transferTime);
                    }
                }
            }
        }


        return Command::SUCCESS;
    }
}
