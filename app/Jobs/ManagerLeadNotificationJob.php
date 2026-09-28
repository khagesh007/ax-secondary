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

class ManagerLeadNotificationJob implements ShouldQueue
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
        $managers = User::whereHas("roles", function ($q) {
            $q->whereIn("name", ["Line Manager", "Team Leader", "Sales Director"]);
        })
        ->where('region_id', "<>", 2)
        ->where("status", true)
        ->get();

        foreach ($managers as $manager) {
            $message = "<b>AX Secondary CRM</b>\n";
            $message .= "\n";
            $message .= "⚠️<b>Leads in `NEW` Status</b>⚠️\n";
            $message .= "\n";
            $message .= "Good Evening <b>" . $manager->full_name . '</b>,' . "\n";
            $managerLeads = $manager->leads()
                                   ->where("status", true)
                                   ->where('is_archive', 0)
                                   ->whereNull('converted_to_listing_lead')
                                   ->where("lead_status_id", 1);

            $manager_count = $managerLeads->count();

            $agent_ids = getUserChildrenIdsOnly($manager->id);

            $key = array_search($manager->id, $agent_ids);
            if ($key !== false) {
                array_splice($agent_ids, $key, 1);
            }

            if($agent_ids){
                $agentsWithLeads = User::whereNot('id',172)->whereIn("id", $agent_ids)
                    ->whereHas('leads', function ($query) {
                        $query->where('lead_status_id', 1)
                            ->where('status', 1)
                            ->where('is_archive', 0)
                            ->whereNull('converted_to_listing_lead');
                    })
                    ->withCount(['leads as new_leads_count' => function ($query) {
                        $query->where('lead_status_id', 1)
                            ->where('is_archive', 0)
                            ->where('status', 1)
                            ->whereNull('converted_to_listing_lead');
                    }])
                    ->where('status', true)
                    ->orderByDesc('new_leads_count')
                    ->get();

                $totalLeadsOfAgents = Lead::where('status', true)
                    ->whereHas('agent',function($query) use ($agent_ids) {
                        $query->whereNot('id',172)->whereIn('id', $agent_ids)->where('status', true);
                    })
                    ->where('is_archive', 0)
                    ->whereNull('converted_to_listing_lead')
                    ->where('lead_status_id', 1)
                    ->where('status', 1)
                    ->count();



                // $agentsWithTwoNewLeads = User::whereHas('leads', function ($query) {
                //         $query->select('agent_id')
                //             ->where('lead_status_id', 1)
                //             ->where('is_archive', 0)
                //             ->where('status', 1)
                //             ->whereNull('converted_to_listing_lead')
                //             ->groupBy('agent_id')
                //             ->havingRaw('COUNT(*) = 2');
                //     })
                //     ->where('status', true)
                //     ->whereIn('id', $agent_ids)
                //     ->count();

                // $agentsWithOneNewLead = User::whereHas('leads', function ($query) {
                //         $query->select('agent_id')
                //             ->where('lead_status_id', 1)
                //             ->where('is_archive', 0)
                //             ->where('status', 1)
                //             ->whereNull('converted_to_listing_lead')
                //             ->groupBy('agent_id')
                //             ->havingRaw('COUNT(*) = 1');
                //     })
                //     ->where('status', true)
                //     ->whereIn('id', $agent_ids)
                //     ->count();

                $totalCount = 0;
                if($agentsWithLeads->count() > 0){
                    $message .= "Here's the Lead Report of your team!\n\n";
                }
                if ($manager_count > 0) {
                    $message .=  "You're currently having ".$manager_count." lead(s) in 'New' status. ";
                }
                if($agentsWithLeads->count() > 0 && $manager_count > 0){
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
                if($agentsWithLeads->count() > 0){
                    $message .= '<b>Total' . ': ' . $totalLeadsOfAgents . "</b>.\n";
                }
                $message .= "==========================\n";

                if (($agentsWithLeads->count() > 0 || $manager_count > 0) && $manager->telegram_chatid) {
                    $this->sendTelegramMessageHtml($message, [$manager->telegram_chatid], null, null, 'lead_bot');
                }
            }
        }
    }
}
