<?php

namespace App\Console\Commands\PortalLeadPoolAutomation;

use App\Models\Group;
use App\Models\Lead;
use App\Models\LeadNote;
use App\Models\User;
use App\Traits\TelegramTrait;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendNewLeadWarningCommand extends Command
{
    use TelegramTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'portal-lead-pool-automation:new-lead-send-warning';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a warning 3 hours before the 24-hour deadline (i.e., at 21 hours) if the lead is still in the "New" status, notifying the user that it will soon be transferred to the Portal Lead Pool.';

    private function sendMessageToManager($manager, $agentMessages, $transferTime)
    {
        $message = "<b>Leads at Risk of Transfer</b>\n\n";
        $message .= "Hi {$manager->name},\n\n";
        $message .= "The following agents have leads in <b>\"New\"</b> Status that will be transferred to the <b>Portal Lead Pool</b> if left unactioned within the next <b>3 hours (until {$transferTime})</b>: Please note that once transferred, agent will not be able to reclaim these leads.\n\n";
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

        $betweenStartTime = Carbon::now()->addHours(3)->startOfHour()->subMinutes(15);
        $betweenEndTime = Carbon::now()->addHours(3)->endOfHour()->addMinutes(15);

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
                        'leads' => function ($query) use ($betweenStartTime, $betweenEndTime) {
                            $query->where('portal_lead', false)
                                ->whereNull('portal_lead_transferred_date')
                                ->whereNull('transfer_to_portal_lead_warning_sent_at')
                                ->where('lead_status_id', 1) //New
                                ->where('is_archive', false)
                                ->whereNull('converted_to_listing_lead')
                                ->where('region_id', '<>', 2)
                                ->whereBetween('transfer_to_portal_lead_time', [$betweenStartTime, $betweenEndTime]);
                        }
                    ])
                    ->having('leads_count', '>', 0)
                    ->get();


                $managerMessage = "";

                $threeHoursLater = Carbon::now()->addHours(3);

                $transferTime = $threeHoursLater->format('h:i A');

                foreach ($users as $user) {
                    if ($user->leads_count > 0) {
                        $managerMessage .= "{$user->name} - {$user->leads_count} lead(s)\n";

                        $message = "<b>Urgent: Action Required on Your Leads</b>\n\n";
                        $message .= "Hi {$user->name},\n\n";
                        $message .= "You currently have <b>{$user->leads_count} lead(s)</b> in <b>\"New\"</b> Status. If these leads remain unactioned for the next <b>3 hours (until {$transferTime})</b>, they will be <b>removed from your account</b> and transferred to the <b>Portal Lead Pool</b>. Please note that once transferred, you will not be able to reclaim these leads.\n\n";
                        $message .= "⚠️ Failure to act will significantly impact your KPI.\n\n";
                        $message .= "📅 Report Generated On: " . Carbon::now()->format('F d, Y - h:i A') . "\n\n";

                        $leads = Lead::where('portal_lead', false)
                            ->whereNull('portal_lead_transferred_date')
                            ->whereNull('transfer_to_portal_lead_warning_sent_at')
                            ->where('lead_status_id', 1) //New
                            ->where('is_archive', false)
                            ->whereNull('converted_to_listing_lead')
                            ->where('region_id', '<>', 2)
                            ->whereBetween('transfer_to_portal_lead_time', [$betweenStartTime, $betweenEndTime])
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
                                'transfer_to_portal_lead_warning_sent_at' => Carbon::now(),
                            ]);
                            $leadNote = new LeadNote();
                            $leadNote->lead_id = $lead->id;
                            $leadNote->note = "A warning has been sent to update this lead, which is currently in the 'New' status.";
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
