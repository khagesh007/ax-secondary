<?php

namespace App\Console\Commands\PortalLeadPoolAutomation;

use App\Models\Group;
use App\Models\Lead;
use App\Models\LeadNote;
use App\Models\User;
use App\Traits\TelegramTrait;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendInProgressLeadWarningCommand extends Command
{
    use TelegramTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'portal-lead-pool-automation:in-progress-lead-send-warning';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a warning 4 hours before the 72-hour deadline (i.e., at 68 hours) if the lead is still in "In Progress" status without activity, indicating it will soon be transferred to the Portal Lead Pool.';

    private function sendMessageToManager($manager, $agentMessages, $transferTime)
    {
        $message = "<b>Leads at Risk of Transfer</b>\n\n";
        $message .= "Hi {$manager->name},\n\n";
        $message .= "The following agents have leads in <b>\"In Progress\"</b> Status that will be transferred to the <b>Portal Lead Pool</b> if left unactioned within the next <b>4 hours (until {$transferTime})</b>: Please note that once transferred, agent will not be able to reclaim these leads.\n\n";
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
            "A reminder has been sent to update this lead, which is currently in the 'In Progress' status.",
            "A warning has been sent to update this lead, which is currently in the 'In Progress' status.",
        ];

        $exceptUserIds = [6];
        $exemptAgents = getExemptedAgentIdsFromLeadsTransfer();

        $betweenStartTime = Carbon::now()->subHours(68)->startOfHour()->subMinutes(15);
        $betweenEndTime = Carbon::now()->subHours(68)->endOfHour()->addMinutes(15);

        $sixtyEightHoursAgo = Carbon::now()->subHours(68);

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
                        'leads' => function ($query) use ($cutOffDate, $betweenStartTime, $betweenEndTime, $sixtyEightHoursAgo, $exemptNotes) {
                            $query->where('portal_lead', false)
                                ->whereNull('portal_lead_transferred_date')
                                ->whereNull('in_progress_warning_sent_at')
                                ->where('lead_status_id', 5) // "In Progress"
                                ->where('is_archive', false)
                                ->whereNull('converted_to_listing_lead')
                                ->where('region_id', '<>', 2)
                                ->whereDate('created_at', '>=', $cutOffDate)
                                ->whereBetween('in_progress_time', [$betweenStartTime, $betweenEndTime])
                                ->whereDoesntHave('events', function ($q) use ($sixtyEightHoursAgo) {
                                    $q->where('created_at', '>=', $sixtyEightHoursAgo);
                                })
                                ->whereDoesntHave('notes', function ($q) use ($sixtyEightHoursAgo, $exemptNotes) {
                                    $q->where('created_at', '>=', $sixtyEightHoursAgo)
                                        ->whereNotIn('note', $exemptNotes);
                                });
                        }
                    ])
                    ->having('leads_count', '>', 0)
                    ->get();

                $managerMessage = "";

                $threeHoursLater = Carbon::now()->addHours(4);

                $transferTime = $threeHoursLater->format('h:i A');

                foreach ($users as $user) {
                    if ($user->leads_count > 0){
                        $managerMessage .= "{$user->name} - {$user->leads_count} lead(s)\n";

                        $message = "<b>Urgent: Action Required on Your Leads</b>\n\n";
                        $message .= "Hi {$user->name},\n\n";
                        $message .= "You currently have <b>{$user->leads_count} lead(s)</b> in <b>\"In Progress\"</b> Status. If these leads remain unactioned for the next <b>4 hours (until {$transferTime})</b>, they will be <b>removed from your account</b> and transferred to the <b>Portal Lead Pool</b>. Please note that once transferred, you will not be able to reclaim these leads.\n\n";
                        $message .= "⚠️ Failure to act will significantly impact your KPI.\n\n";
                        $message .= "📅 Report Generated On: " . Carbon::now()->format('F d, Y - h:i A'). "\n\n";

                        $leads = Lead::where('portal_lead', false)
                            ->whereNull('portal_lead_transferred_date')
                            ->whereNull('in_progress_warning_sent_at')
                            ->where('lead_status_id', 5) // "In Progress"
                            ->where('region_id', '<>', 2)
                            ->where('is_archive', false)
                            ->whereNull('converted_to_listing_lead')
                            ->whereDate('created_at', '>=', $cutOffDate)
                            ->whereBetween('in_progress_time', [$betweenStartTime, $betweenEndTime])
                            ->whereDoesntHave('events', function ($q) use ($sixtyEightHoursAgo) {
                                $q->where('created_at', '>=', $sixtyEightHoursAgo);
                            })
                            ->whereDoesntHave('notes', function ($q) use ($sixtyEightHoursAgo, $exemptNotes) {
                                $q->where('created_at', '>=', $sixtyEightHoursAgo)
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
                                'in_progress_warning_sent_at' => Carbon::now(),
                            ]);

                            $leadNote = new LeadNote();
                            $leadNote->lead_id = $lead->id;
                            $leadNote->note = "A warning has been sent to update this lead, which is currently in the 'In Progress' status.";
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
