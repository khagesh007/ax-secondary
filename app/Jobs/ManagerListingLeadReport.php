<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\ListingLead;
use App\Traits\TelegramTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ManagerListingLeadReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, TelegramTrait;

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
        $managers = User::whereNot('id',172)->whereHas("roles", function ($q) {
            $q->whereIn("name", ["Line Manager", "Team Leader", "Sales Director"]);
        })->where("status", true)->get();
        foreach ($managers as $manager) {
            $message = "<b>AX Secondary CRM</b>\n";
            $message .= "\n";
            $message .= "‼️<b>Listing Leads in `ASSIGNED` Status</b>‼️\n";
            $message .= "\n";
            $message .= "Good Evening <b>" . $manager->full_name . '</b>,' . "\n";
            $managerLeads = $manager->listingLeads()
                                   ->where('is_archive', 0)
                                   ->where("status_id", 2);

            $manager_count = $managerLeads->count();

            $agent_ids = getUserChildrenIdsOnly($manager->id);

            $key = array_search($manager->id, $agent_ids);
            if ($key !== false) {
                array_splice($agent_ids, $key, 1);
            }

            if($agent_ids){
                $agentsWithLeads = User::whereNot('id',172)->whereIn("id", $agent_ids)
                    ->whereHas('listingLeads', function ($query) {
                        $query->where('status_id', 2)
                            ->where('is_archive', 0);
                    })
                    ->withCount(['listingLeads as new_leads_count' => function ($query) {
                        $query->where('status_id', 2)
                            ->where('is_archive', 0);
                    }])
                    ->where('status', true)
                    ->orderByDesc('new_leads_count')
                    ->get();

                $totalLeadsOfAgents = ListingLead::where('status_id', 2)
                    ->whereHas('agent',function($query) use ($agent_ids) {
                        $query->whereNot('id',172)->whereIn('id', $agent_ids);
                    })
                    ->where('is_archive', 0)
                    ->count();



                // $agentsWithTwoNewLeads = User::whereHas('listingLeads', function ($query) {
                //         $query->select('agent_id')
                //             ->where('status_id', 2)
                //             ->where('is_archive', 0)
                //             ->groupBy('agent_id')
                //             ->havingRaw('COUNT(*) = 2');
                //     })
                //     ->where('status', true)
                //     ->whereIn('id', $agent_ids)
                //     ->count();

                // $agentsWithOneNewLead = User::whereHas('listingLeads', function ($query) {
                //         $query->select('agent_id')
                //             ->where('status_id', 2)
                //             ->where('is_archive', 0)
                //             ->groupBy('agent_id')
                //             ->havingRaw('COUNT(*) = 1');
                //     })
                //     ->where('status', true)
                //     ->whereIn('id', $agent_ids)
                //     ->count();

                $totalCount = 0;
                if($agentsWithLeads->count() > 0){
                    $message .= "Here's the Listing Lead Report of your team!\n\n";
                }
                if ($manager_count > 0) {
                    $message .=  "You're currently having ".$manager_count." Listing lead(s) in 'Assigned' status. ";
                }
                if($agentsWithLeads->count() > 0 && $manager_count > 0){
                    $message .= "Below is the list of Listing lead(s) in 'Assigned' status on your team. This means these Listing Leads have been <b>assigned</b> to these agents, and <b>not actioned</b> by them.\n\n";
                    $message .= "Here's the breakdown by individual:\n\n";
                }
                foreach ($agentsWithLeads as $agentWithLeads) {
                    $message .= "{$agentWithLeads->name}: <b>{$agentWithLeads->new_leads_count}</b>\n";
                    // $totalCount += $agentWithLeads['lead_count'];
                }
                // if($agentsWithTwoNewLeads){
                //     $message.= $agentsWithTwoNewLeads." agents with 2 Assigned Listing leads.\n";
                // }
                // if($agentsWithOneNewLead){
                //     $message.= $agentsWithOneNewLead." agents with 1 Assigned Listing lead.\n";
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
