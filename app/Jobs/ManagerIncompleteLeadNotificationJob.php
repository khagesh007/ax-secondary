<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Lead;
use App\Traits\TelegramTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ManagerIncompleteLeadNotificationJob implements ShouldQueue
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

        $managers = User::whereNot('id', 172)->whereHas("roles", function ($q) {
            $q->whereIn("name", ["Line Manager", "Team Leader", "Sales Director"]);
        })
            ->where("status", true)
            ->get();

        foreach ($managers as $manager) {

            $message = "<b>AX Secondary CRM</b>\n";
            $message .= "\n";
            $message .= "🚨<b>Leads with `INCOMPLETE DETAILS`</b>🚨\n";
            $message .= "\n";
            $message .= "Good Evening <b>" . $manager->full_name . '</b>,' . "\n";
            $managerLeads = $manager->leads()
                ->where("status", true)
                ->where('is_archive', 0)
                ->whereNull('converted_to_listing_lead')
                ->whereNotIn('lead_status_id', [4, 12])
                ->whereDate('created_at', '>', $cutoff)
                ->where(function ($query) {
                    $query->where('first_name', 'LIKE', '%new%')->orWhereNull('first_name');
                });
            $manager_count = $managerLeads->count();

            $agent_ids = getUserChildrenIdsOnly($manager->id);

            $key = array_search($manager->id, $agent_ids);
            if ($key !== false) {
                array_splice($agent_ids, $key, 1);
            }
            if ($agent_ids) {
                $agentsWithLeads = User::whereNot('id', 172)->whereIn("id", $agent_ids)
                    ->whereHas('leads', function ($query) use ($cutoff) {
                        $query->whereNull('property_id')
                            ->where('status', 1)
                            ->whereNotIn('lead_status_id', [4, 12])
                            ->where('is_archive', 0)
                            ->whereNull('converted_to_listing_lead')
                            ->where(function ($query) {
                                $query->where('first_name', 'LIKE', '%new%')->orWhereNull('first_name');
                            })
                            ->whereDate('created_at', '>', $cutoff);
                    })
                    ->withCount(['leads as new_leads_count' => function ($query) use ($cutoff) {
                        $query->whereNull('property_id')
                            ->whereNotIn('lead_status_id', [4, 12])
                            ->where('is_archive', 0)
                            ->where('status', 1)
                            ->whereNull('converted_to_listing_lead')
                            ->where(function ($query) {
                                $query->where('first_name', 'LIKE', '%new%')->orWhereNull('first_name');
                            })->whereDate('created_at', '>', $cutoff);
                    }])
                    ->where('status', true)
                    ->orderByDesc('new_leads_count')
                    ->get();

                $totalLeadsOfAgents = Lead::where('status', true)
                    ->whereHas('agent', function ($query) use ($agent_ids) {
                        $query->whereIn('id', $agent_ids)->whereNot('id', 172)->where('status', true);
                    })
                    ->where(function ($query) {
                        $query->where('first_name', 'LIKE', '%new%')->orWhereNull('first_name');
                    })
                    ->where('is_archive', 0)
                    ->whereNull('converted_to_listing_lead')
                    ->whereNull('property_id')
                    ->whereNotIn('lead_status_id', [4, 12])
                    ->where('status', 1)
                    ->whereDate('created_at', '>', $cutoff)
                    ->count();
                $totalCount = 0;
                if ($agentsWithLeads->count() > 0) {
                    $message .= "Here's the Lead Report of your team!\n\n";
                }
                if ($manager_count > 0) {
                    $message .=  "You're currently having " . $manager_count . " lead(s) with 'INCOMPLETE DETAILS'. ";
                }
                if ($agentsWithLeads->count() > 0 && $manager_count > 0) {
                    $message .= "Here's the breakdown by individual:\n\n";
                }
                foreach ($agentsWithLeads as $agentWithLeads) {
                    $message .= "{$agentWithLeads->name}: <b>{$agentWithLeads->new_leads_count}</b>\n";
                    // $totalCount += $agentWithLeads['lead_count'];
                }
                // if($agentsWithTwoNewLeads){
                //     $message.= $agentsWithTwoNewLeads." agents with 2 new leads.\n";
                // }
                // if($agentsWithOneNewLead){
                //     $message.= $agentsWithOneNewLead." agents with 1 new lead.\n";
                // }
                if ($agentsWithLeads->count() > 0) {
                    $message .= '<b>Total' . ': ' . $totalLeadsOfAgents . "</b>.";
                }
                $message .= "\n\n" . '<b>Note:</b> Incomplete details include missing Client Name and/or Property Reference Number for the relevant inquiry' . "\n\n";
                $message .= "==========================\n";
                $message .= '<b>Note:</b> This report displays data received after ' . $cutoff . ".\n\n";
                $message .= "📅 <b>Report Generated on:</b> " . now()->format('Y-m-d H:i:s') . "\n\n";


                if (($agentsWithLeads->count() > 0 || $manager_count > 0) && $manager->telegram_chatid) {
                    $this->sendTelegramMessageHtml($message, [$manager->telegram_chatid], null, null, 'lead_bot');
                }
            }
            if (env('APP_ENV') == 'local') {
                break;
            }
        }
    }
}
