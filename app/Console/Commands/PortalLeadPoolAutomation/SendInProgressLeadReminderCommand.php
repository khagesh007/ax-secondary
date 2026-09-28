<?php

namespace App\Console\Commands\PortalLeadPoolAutomation;

use App\Models\Group;
use App\Models\Lead;
use App\Models\LeadNote;
use App\Models\User;
use App\Traits\TelegramTrait;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendInProgressLeadReminderCommand extends Command
{
    use TelegramTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'portal-lead-pool-automation:in-progress-lead-send-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a reminder to update the lead if it is in the "In Progress" status and there is no activity (e.g., calendar event or note) for 36 hours.';

    private function sendMessageToManager($manager, $agentMessages)
    {
        $message = "<b>Reminder to Update Leads</b>\n\n";
        $message .= "Hi {$manager->name},\n\n";
        $message .= "The following agents have leads in <b>\"In Progress\"</b> Status for more than <b>36 hours</b> with no updates.\n\n";
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
        $exceptUserIds = [6];
        $exemptAgents = getExemptedAgentIdsFromLeadsTransfer();
        $groups = Group::with(["user"])
            ->whereNull("parent_id")
            ->get();

        $cutOffDate = "2025-07-04";

        $thirtySixHoursAgo = Carbon::now()->subHours(36);

        $reminderNote = "A reminder has been sent to update this lead, which is currently in the 'In Progress' status.";

        foreach ($groups as $group) {
            $manager = $group->user;
            if ($manager) {
                $users = User::whereHas('roles', function ($q) {
                    $q->where('name', 'Agent');
                })
                    ->whereNotIn('id', array_merge($exceptUserIds, $exemptAgents))
                    ->where('status', true)
                    ->where('parent_id', $manager->id)
                    ->whereNotNull('telegram_chatid')
                    ->withCount([
                            'leads' => function ($query) use ($thirtySixHoursAgo, $cutOffDate, $reminderNote) {
                                $query->where('in_progress_time', '<=', $thirtySixHoursAgo)
                                    ->whereNull('in_progress_reminder_sent_at')
                                    ->where('portal_lead', false)
                                    ->where('lead_status_id', 5) // In Progress
                                    ->where('region_id', '<>', 2)
                                    ->where('is_archive', false)
                                    ->whereNull('converted_to_listing_lead')
                                    ->whereNull('deleted_at')
                                    ->whereDate('created_at', '>=', $cutOffDate)
                                    ->whereDoesntHave('events', function ($q) use ($thirtySixHoursAgo) {
                                        $q->where('created_at', '>=', $thirtySixHoursAgo);
                                    })
                                    ->whereDoesntHave('notes', function ($q) use ($thirtySixHoursAgo, $reminderNote) {
                                        $q->where('created_at', '>=', $thirtySixHoursAgo)
                                            ->where('note', '<>', $reminderNote);
                                    });
                            }]
                    )
                    ->having('leads_count', '>', 0)
                    ->get();

                $managerMessage = "";

                foreach ($users as $user) {
                    if ($user->leads_count > 0) {
                        $managerMessage .= "{$user->name} - {$user->leads_count} lead(s)\n";

                        $message = "<b>🔔 Reminder ❗ to Take Action On Your 'In Progress' Leads</b>\n\n";
                        $message .= "Hi {$user->name},\n\n";
                        $message .= "You have <b>{$user->leads_count} lead(s)</b> in <b>\"In Progress\"</b> status for more than <b>36 hours</b> with no updates.\n\n";
                        $message .= "Please review and take the necessary action. Otherwise, these leads will be moved to the <b>Portal Lead Pool</b>, and you will no longer be able to retrieve them..\n\n";

                        $leads = Lead::where('in_progress_time', '<=', $thirtySixHoursAgo)
                            ->whereNull('in_progress_reminder_sent_at')
                            ->where('portal_lead', false)
                            ->where('lead_status_id', 5) // In Progress
                            ->where('region_id', '<>', 2)
                            ->where('is_archive', false)
                            ->whereNull('converted_to_listing_lead')
                            ->whereNotIn('agent_id', array_merge($exceptUserIds, $exemptAgents))
                            ->where('agent_id', $user->id)
                            ->whereDate('created_at', '>=', $cutOffDate)
                            ->whereDoesntHave('events', function ($q) use ($thirtySixHoursAgo) {
                                $q->where('created_at', '>=', $thirtySixHoursAgo);
                            })
                            ->whereDoesntHave('notes', function ($q) use ($thirtySixHoursAgo, $reminderNote) {
                                $q->where('created_at', '>=', $thirtySixHoursAgo)
                                    ->where('note', '<>', $reminderNote);
                            })
                            ->get();

                        if ($user->leads_count < 10) {
                            $message .= "<b>Leads with No Updates</b>:\n";
                            foreach ($leads as $index => $lead) {
                                $name = $lead->first_name ?? "New";
                                $message .= ($index + 1) . " - {$lead->id} - {$name}\n";
                            }
                        }

                        $this->sendTelegramMessageHtml($message, [$user->telegram_chatid], null, null, 'notification_bot');

                        foreach ($leads as $lead) {
                            $lead->update([
                                'in_progress_reminder_sent_at' => Carbon::now(),
                            ]);

                            $leadNote = new LeadNote();
                            $leadNote->lead_id = $lead->id;
                            $leadNote->note = $reminderNote;
                            $leadNote->save();
                        }
                    }
                }

                if (!empty($managerMessage)) {
                    $this->sendMessageToManager($manager, $managerMessage);
                }
            }
        }


        return Command::SUCCESS;
    }
}
