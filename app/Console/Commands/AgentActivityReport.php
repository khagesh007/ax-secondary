<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\Lead;
use App\Models\LeadNote;
use App\Models\ListingLead;
use App\Models\ListingLeadNote;
use App\Models\Property;
use App\Models\PropertyNote;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Telegram\Bot\Laravel\Facades\Telegram;

class AgentActivityReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agent:activityReport';
    protected $exempted = [1432, 172, 1046, 1317, 1403, 1044, 1047, 1433, 1444, 1445, 1446, 1447, 1434, 1402, 1215, 1119, 1084, 1081, 1045,1386,1390];

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function __handle()
    {
        $number_of_days = 30;
        $duration = 'from=' . Carbon::now()->subDays($number_of_days)->format('m-d-Y') . '&to=' . Carbon::now()->format('m-d-Y');
        $this->exempted = [1046, 1317, 1403, 1044, 1047, 1433, 1444, 1445, 1446, 1447, 1434, 1402, 1215, 1119, 1084, 1081, 1045];
        $agents = User::where('status', true)
            ->whereHas("roles", fn($q) => $q->whereIn("name", ["Agent"]))
            ->whereNotIn('id', $this->exempted)
            ->whereNotBetween('created_at', [now()->subDays(30), now()])
            ->get();
        $agents_with_zero = 0;
        foreach ($agents as $agent) {
            $frequency = 0;
            $baseQuery = Lead::whereNull('converted_to_listing_lead')
                ->where('status', 1)
                ->where('is_archive', 0)
                ->where('agent_id', $agent->id)
                ->where(function ($q) use ($agent) {
                    $q->where('created_by_id', $agent->id)
                        ->orWhere('updated_by_id', $agent->id);
                });
            $leadCount = $this->queryDecider($baseQuery, 'updated_at', $duration)->count();

            $BaseleadNotesCount = LeadNote::where('created_by_id', $agent->id);
            $leadNotesCount = $this->queryDecider($BaseleadNotesCount, 'updated_at', $duration)->count();

            $combinedLeadsCount = $leadCount + $leadNotesCount;

            $frequency += $combinedLeadsCount;

            $BaselistingCount = Property::where('is_archived', 0)
                ->where('agent_id', $agent->id)->where(function ($q) use ($agent) {
                $q->where('created_by_id', $agent->id)
                    ->orWhere('updated_by_id', $agent->id);
            });
            $listingCount = $this->queryDecider($BaselistingCount, 'updated_at', $duration)->count();

            $baseListingNotesCount = PropertyNote::where('created_by_id', $agent->id);
            $listingNotesCount = $this->queryDecider($baseListingNotesCount, 'updated_at', $duration)->count();

            $combinedListingCount = $listingCount + $listingNotesCount;

            $frequency += $combinedListingCount;

            $baseListingLeadsCount = ListingLead::where('is_archive', 0)
                ->where('agent_id', $agent->id)->where(function ($q) use ($agent) {
                $q->where('created_by_id', $agent->id)
                    ->orWhere('updated_by_id', $agent->id);
            });
            $listingLeadsCount = $this->queryDecider($baseListingLeadsCount, 'updated_at', $duration)->count();

            $baseListingLeadNotesCount = ListingLeadNote::where('created_by_id', $agent->id);
            $listingLeadNotesCount = $this->queryDecider($baseListingLeadNotesCount, 'updated_at', $duration)->count();

            $combinedListingLeadCount = $listingLeadsCount + $listingLeadNotesCount;

            $frequency += $combinedListingLeadCount;

            $baseCalenderEventsCount = Event::where('agent_id', $agent->id)->where(function ($q) use ($agent) {
                $q->where('created_by_id', $agent->id)
                    ->orWhere('updated_by_id', $agent->id);
            });
            $calenderEventsCount = $this->queryDecider($baseCalenderEventsCount, 'updated_at', $duration)->count();

            $frequency += $calenderEventsCount;

            $key = $agent->full_name;
            $agentsLeadCount[$key] = $frequency;

            $agentsLeadCountID[$agent->id] = $frequency;
            if ($frequency == 0) {
                $agents_with_zero++;
            }

        }

        arsort($agentsLeadCount);
        arsort($agentsLeadCountID);

        $_agentsLeadCountID = array_slice($agentsLeadCountID, 0, 20, true);
        $_last_agentsLeadCountID = array_slice($agentsLeadCountID, -20, 20, true);

        $__agentsLeadCountID = array_keys($_agentsLeadCountID);
        $__last_agentsLeadCountID = array_keys($_last_agentsLeadCountID);

        $topAgents = User::whereHas("roles", fn($q) => $q->whereIn("name", ["Agent"]))->whereIn('id', $__agentsLeadCountID)
            ->orderByRaw(DB::raw("FIELD(id, " . implode(',', $__agentsLeadCountID) . ")"))
            ->get();
        $leadsAgents = User::whereHas("roles", fn($q) => $q->whereIn("name", ["Agent"]))->whereIn('id', $__last_agentsLeadCountID)
            ->orderByRaw(DB::raw("FIELD(id, " . implode(',', $__last_agentsLeadCountID) . ")"))
            ->get();

        $message = "";
        $message = "<b>AX Secondary CRM</b>\n\n";
        $message .= "<b>Agent Activity Report</b>\n";
        $message .= "----------------------------------------------\n";
        $message .= "Top 20 most active agents in the last " . $number_of_days . " Days.\n";
        $message .= "----------------------------------------------\n";
        foreach ($topAgents as $agent) {
            $message .= ' - ' . $agent->full_name . " - " . $agentsLeadCount[$agent->full_name] . " activities\n";
        }
        $message .= "----------------------------------------------\n";
        $message .= "Top 20 least active agents in the last " . $number_of_days . " Days.\n";
        $message .= "----------------------------------------------\n";
        foreach ($leadsAgents as $agent) {
            $message .= ' - ' . $agent->full_name . " - " . $agentsLeadCount[$agent->full_name] . " activities\n";
        }
        $message .= "----------------------------------------------\n";
        $message .= "<b>Total Agents with 0 Activity in the last" . $number_of_days . " days :</b> " . $agents_with_zero . "\n";
        $message .= "----------------------------------------------\n";
        $message .= "Report generated at " . Carbon::now()->format('d-m-Y H:i:s') . "\n";
        $message .= "----------------------------------------------\n";
        // $message .= "This report is based on the following activities for the past ".$number_of_days." days:\n";
        // $message .= "----------------------------------------------\n";
        // $message .= "1. Leads\n";
        // $message .= "2. Lead Notes\n";
        // $message .= "3. Listings\n";
        // $message .= "4. Listing Notes\n";
        // $message .= "5. Listing Leads\n";
        // $message .= "6. Listing Lead Notes\n";
        // $message .= "7. Calender Events\n";
        $message .= "----------------------------------------------\n";

        $telegram = Telegram::bot('notification_bot');
        $telegramMessage = $this->sendTelegramMessage($telegram, $message);
        $this->DetailReport();

        return Command::SUCCESS;
    }

    public function _handle()
    {
        $number_of_days = 30;
        $duration = 'from=' . Carbon::now()->subDays($number_of_days)->format('m-d-Y') . '&to=' . Carbon::now()->format('m-d-Y');
        $this->exempted = [1046, 1317, 1403, 1044, 1047, 1433, 1444, 1445, 1446, 1447, 1434, 1402, 1215, 1119, 1084, 1081, 1045];

        $agents = User::where('status', true)
            ->whereHas("roles", fn($q) => $q->whereIn("name", ["Agent"]))
            ->whereNotIn('id', $this->exempted)
            ->whereNotBetween('created_at', [now()->subDays(30), now()])
            ->get();

        $agents_with_activities = [];
        $agents_with_zero = [];

        foreach ($agents as $agent) {
            $frequency = 0;
            $baseQuery = Lead::whereNull('converted_to_listing_lead')
                ->where('status', 1)
                ->where('is_archive', 0)
                ->where('agent_id', $agent->id)
                ->where(function ($q) use ($agent) {
                    $q->where('created_by_id', $agent->id)
                        ->orWhere('updated_by_id', $agent->id);
                });

            $leadCount = $this->queryDecider($baseQuery, 'updated_at', $duration)->count();

            $BaseleadNotesCount = LeadNote::where('created_by_id', $agent->id);
            $leadNotesCount = $this->queryDecider($BaseleadNotesCount, 'updated_at', $duration)->count();

            $combinedLeadsCount = $leadCount + $leadNotesCount;

            $frequency += $combinedLeadsCount;

            $BaselistingCount = Property::where('is_archived', 0)
                ->where('agent_id', $agent->id)->where(function ($q) use ($agent) {
                $q->where('created_by_id', $agent->id)
                    ->orWhere('updated_by_id', $agent->id);
            });
            $listingCount = $this->queryDecider($BaselistingCount, 'updated_at', $duration)->count();

            $baseListingNotesCount = PropertyNote::where('created_by_id', $agent->id);
            $listingNotesCount = $this->queryDecider($baseListingNotesCount, 'updated_at', $duration)->count();

            $combinedListingCount = $listingCount + $listingNotesCount;

            $frequency += $combinedListingCount;

            $baseListingLeadsCount = ListingLead::where('is_archive', 0)
                ->where('agent_id', $agent->id)->where(function ($q) use ($agent) {
                $q->where('created_by_id', $agent->id)
                    ->orWhere('updated_by_id', $agent->id);
            });
            $listingLeadsCount = $this->queryDecider($baseListingLeadsCount, 'updated_at', $duration)->count();

            $baseListingLeadNotesCount = ListingLeadNote::where('created_by_id', $agent->id);
            $listingLeadNotesCount = $this->queryDecider($baseListingLeadNotesCount, 'updated_at', $duration)->count();

            $combinedListingLeadCount = $listingLeadsCount + $listingLeadNotesCount;

            $frequency += $combinedListingLeadCount;

            $baseCalenderEventsCount = Event::where('agent_id', $agent->id)->where(function ($q) use ($agent) {
                $q->where('created_by_id', $agent->id)
                    ->orWhere('updated_by_id', $agent->id);
            });
            $calenderEventsCount = $this->queryDecider($baseCalenderEventsCount, 'updated_at', $duration)->count();

            $frequency += $calenderEventsCount;

            $key = $agent->full_name;

            if ($frequency > 0) {
                $agents_with_activities[$key] = $frequency;
            } else {
                $agents_with_zero[$key] = $frequency;
            }
        }

        // Sort agents with activities by activity count in descending order
        arsort($agents_with_activities);
        // Extract the first 20 agents with activities
        $top_agents = array_slice($agents_with_activities, 0, 20, true);
        // Extract the next 20 agents with activities
        $least_active_agents = array_slice($agents_with_activities, 20, 20, true);

        // Sort agents with zero activities alphabetically
        ksort($agents_with_zero);

        $message = "";
        $message = "<b>AX Secondary CRM</b>\n\n";
        $message .= "<b>Agent Activity Report</b>\n";
        $message .= "----------------------------------------------\n";
        $message .= "Top 20 most active agents in the last " . $number_of_days . ".\n";
        $message .= "----------------------------------------------\n";
        foreach ($top_agents as $agent => $frequency) {
            $message .= ' - ' . $agent . " - " . $frequency . " activities\n";
        }
        $message .= "----------------------------------------------\n";
        $message .= "Top 20 least active agents in the last " . $number_of_days . ".\n";
        $message .= "----------------------------------------------\n";
        foreach ($least_active_agents as $agent => $frequency) {
            $message .= ' - ' . $agent . " - " . $frequency . " activities\n";
        }
        $message .= "----------------------------------------------\n";
        $message .= "Agents with 0 Activity:\n";
        $message .= "----------------------------------------------\n";
        foreach ($agents_with_zero as $agent => $frequency) {
            $message .= ' - ' . $agent . "\n";
        }
        $message .= "----------------------------------------------\n";
        $message .= "Report generated at " . Carbon::now()->format('d-m-Y H:i:s') . "\n";
        $message .= "----------------------------------------------\n";

        $telegram = Telegram::bot('notification_bot');
        $telegramMessage = $this->sendTelegramMessage($telegram, $message);
        // $this->DetailReport();
        $this->allUsersActivies();
        return Command::SUCCESS;
    }
    public function handle()
    {
        $number_of_days = 30;
        $duration = 'from=' . Carbon::now()->subDays($number_of_days)->format('m-d-Y') . '&to=' . Carbon::now()->format('m-d-Y');

        $agents = User::where('status', true)
            ->whereHas("roles", fn($q) => $q->whereIn("name", ["Agent"]))
            ->whereNotIn('id', $this->exempted)
            ->whereNotBetween('created_at', [now()->subDays(30), now()])
            // ->where('bitrix_role', 'like', '%Secondary%')
            // ->where('bitrix_role', 'not like', '%Secondary Admin%')
            // ->where('bitrix_role', 'not like', '%Primary%')
            ->get();

        $agents_with_activities = [];
        $agents_with_zero = [];
        // dd(lead::where('updated_by_id',1299)->orWhere('created_by_id',1299)->count());
        foreach ($agents as $agent) {
            $frequency = 0;


            $baseQuery = Lead::
            // whereNull('converted_to_listing_lead')
                where('status', 1)
                ->where('is_archive', 0)
                ->where('agent_id', $agent->id)
                ->where('updated_by_id', $agent->id)
                ->where(function ($q) use ($agent) {
                    $q->where('created_by_id', $agent->id)
                        ->orWhere('updated_by_id', $agent->id);
                })
                ;

            $leadCount = $this->queryDecider($baseQuery, 'updated_at', $duration)->count();
            // if($agent->id==1299){
            //     dd($leadCount);
            // }
            $BaseleadNotesCount = LeadNote::where('created_by_id', $agent->id);
            $leadNotesCount = $this->queryDecider($BaseleadNotesCount, 'updated_at', $duration)->count();

            $combinedLeadsCount = $leadCount + $leadNotesCount;

            $frequency += $combinedLeadsCount;

            $BaselistingCount = Property::where('is_archived', 0)
                ->where('agent_id', $agent->id)->where(function ($q) use ($agent) {
                    $q->where('created_by_id', $agent->id)
                        ->orWhere('updated_by_id', $agent->id);
                });
            $listingCount = $this->queryDecider($BaselistingCount, 'updated_at', $duration)->count();

            $baseListingNotesCount = PropertyNote::where('created_by_id', $agent->id);
            $listingNotesCount = $this->queryDecider($baseListingNotesCount, 'updated_at', $duration)->count();

            $combinedListingCount = $listingCount + $listingNotesCount;

            $frequency += $combinedListingCount;

            $baseListingLeadsCount = ListingLead::where('is_archive', 0)
                ->where('agent_id', $agent->id)->where(function ($q) use ($agent) {
                    $q->where('created_by_id', $agent->id)
                        ->orWhere('updated_by_id', $agent->id);
                });
            $listingLeadsCount = $this->queryDecider($baseListingLeadsCount, 'updated_at', $duration)->count();

            $baseListingLeadNotesCount = ListingLeadNote::where('created_by_id', $agent->id);
            $listingLeadNotesCount = $this->queryDecider($baseListingLeadNotesCount, 'updated_at', $duration)->count();

            $combinedListingLeadCount = $listingLeadsCount + $listingLeadNotesCount;

            $frequency += $combinedListingLeadCount;

            $baseCalenderEventsCount = Event::where('agent_id', $agent->id)->where(function ($q) use ($agent) {
                $q->where('created_by_id', $agent->id)
                    ->orWhere('updated_by_id', $agent->id);
            });
            $calenderEventsCount = $this->queryDecider($baseCalenderEventsCount, 'updated_at', $duration)->count();

            $frequency += $calenderEventsCount;

            // if($agent->id==1299){
            //     dd($frequency);
            // }

            $key = $agent->full_name;

            if ($frequency > 0) {
                $agents_with_activities[$key] = $frequency;
            } else {
                $agents_with_zero[$key] = $frequency;
                $__agents_with_zero[$key] = $frequency;

            }
        }

        // Sort agents with activities by activity count in descending order
        arsort($agents_with_activities);

        // Extract the first 20 agents with activities
        $top_agents = array_slice($agents_with_activities, 0, 20, true);

        // Extract the next 20 agents with activities
        $_agents_with_activities = array_slice($agents_with_activities, 20, null, true);

        // Extract agents with zero activities
        $agents_with_zero = [];
        foreach ($agents_with_activities as $agent => $frequency) {
            if ($frequency == 0) {
                $agents_with_zero[$agent] = $frequency;
                unset($agents_with_activities[$agent]);
            }
        }

        // Sort agents with least activities by activity count in ascending order
        asort($agents_with_activities);

        // Merge arrays
        $sorted_agents = $agents_with_zero + $agents_with_activities;

        // Extract the next 20 agents with activities
        $least_active_agents = array_slice($sorted_agents, 0, 20, true);

        $message = "<b>AX Secondary CRM</b>\n\n";
        $message .= "<b>Agent Activity Report</b>\n";
        $message .= "--------------------------------------------\n";
        $message .= "20 most active agents in the last " . $number_of_days . " Days.\n";
        $message .= "--------------------------------------------\n";
        foreach ($top_agents as $agent => $frequency) {
            $message .= ' - ' . $agent . " - " . $frequency . " activities\n";
        }
        $message .= "--------------------------------------------\n";
        $message .= "20 least active agents in the last " . $number_of_days . " Days.\n";
        $message .= "--------------------------------------------\n";

        // revert the array to get the least active agents
        arsort($least_active_agents);

        foreach ($least_active_agents as $agent => $frequency) {
            $message .= ' - ' . $agent . " - " . $frequency . " activities\n";
        }
        $message .= "--------------------------------------------\n";
        $message .= "Agents with 0 Activity:\n";
        $message .= "--------------------------------------------\n";
        foreach ($__agents_with_zero as $agent => $frequency) {
            $message .= ' - ' . $agent . "\n";
        }
        $message .= "--------------------------------------------\n";
        $message .= "Report generated at " . Carbon::now()->format('d-m-Y H:i:s') . "\n";
        $message .= "--------------------------------------------\n";

        $telegram = Telegram::bot('notification_bot');
        $telegramMessage = $this->sendTelegramMessage($telegram, $message);
        // $this->DetailReport();
        // $this->allUsersActivies();
        return Command::SUCCESS;
    }




    public function allUsersActivies()
    {
        $number_of_days = 30;
        $duration = 'from=' . Carbon::now()->subDays($number_of_days)->format('m-d-Y') . '&to=' . Carbon::now()->format('m-d-Y');

        $agents = User::where('status', true)
            ->whereHas("roles", fn($q) => $q->whereIn("name", ["Agent"]))
            ->whereNotIn('id', $this->exempted)
            ->whereNotBetween('created_at', [now()->subDays(30), now()])
            ->get();

        $agent_activities = [];

        foreach ($agents as $agent) {
            $frequency = 0;
            $baseQuery = Lead::whereNull('converted_to_listing_lead')
                ->where('status', 1)
                ->where('is_archive', 0)
                ->where('agent_id', $agent->id)
                ->where(function ($q) use ($agent) {
                    $q->where('created_by_id', $agent->id)
                        ->orWhere('updated_by_id', $agent->id);
                });

                $leadCount = $this->queryDecider($baseQuery, 'updated_at', $duration)->count();

                $BaseleadNotesCount = LeadNote::where('created_by_id', $agent->id);
                $leadNotesCount = $this->queryDecider($BaseleadNotesCount, 'updated_at', $duration)->count();

                $combinedLeadsCount = $leadCount + $leadNotesCount;

                $frequency += $combinedLeadsCount;

                $BaselistingCount = Property::where('is_archived', 0)
                    ->where('agent_id', $agent->id)->where(function ($q) use ($agent) {
                    $q->where('created_by_id', $agent->id)
                        ->orWhere('updated_by_id', $agent->id);
                });
                $listingCount = $this->queryDecider($BaselistingCount, 'updated_at', $duration)->count();

                $baseListingNotesCount = PropertyNote::where('created_by_id', $agent->id);
                $listingNotesCount = $this->queryDecider($baseListingNotesCount, 'updated_at', $duration)->count();

                $combinedListingCount = $listingCount + $listingNotesCount;

                $frequency += $combinedListingCount;

                $baseListingLeadsCount = ListingLead::where('is_archive', 0)
                    ->where('agent_id', $agent->id)->where(function ($q) use ($agent) {
                    $q->where('created_by_id', $agent->id)
                        ->orWhere('updated_by_id', $agent->id);
                });
                $listingLeadsCount = $this->queryDecider($baseListingLeadsCount, 'updated_at', $duration)->count();

                $baseListingLeadNotesCount = ListingLeadNote::where('created_by_id', $agent->id);
                $listingLeadNotesCount = $this->queryDecider($baseListingLeadNotesCount, 'updated_at', $duration)->count();

                $combinedListingLeadCount = $listingLeadsCount + $listingLeadNotesCount;

                $frequency += $combinedListingLeadCount;

                $baseCalenderEventsCount = Event::where('agent_id', $agent->id)->where(function ($q) use ($agent) {
                    $q->where('created_by_id', $agent->id)
                        ->orWhere('updated_by_id', $agent->id);
                });
                $calenderEventsCount = $this->queryDecider($baseCalenderEventsCount, 'updated_at', $duration)->count();

                $frequency += $calenderEventsCount;

            $key = $agent->full_name;
            $agent_activities[$key] = $frequency;
        }

        // Sort agents with activities by activity count in descending order
        arsort($agent_activities);

        $message = "";
        $message = "<b>AX Secondary CRM</b>\n\n";
        $message .= "<b>Agent Activity Report</b>\n";
        $message .= "----------------------------------------------\n";
        $message .= "Agents with Activities\n";
        $message .= "----------------------------------------------\n";
        foreach ($agent_activities as $agent => $frequency) {
            $message .= ' - ' . $agent . " - " . $frequency . " activities\n";
        }
        $message .= "----------------------------------------------\n";
        $message .= "Report generated at " . Carbon::now()->format('d-m-Y H:i:s') . "\n";
        $message .= "----------------------------------------------\n";

        $telegram = Telegram::bot('notification_bot');
        $telegramMessage = $this->sendTelegramMessage($telegram, $message);
        // $this->DetailReport();

        return Command::SUCCESS;
    }

    private function __DetailReport()
    {
        $number_of_days = 90;
        $duration = 'from=' . Carbon::now()->subDays($number_of_days)->format('m-d-Y') . '&to=' . Carbon::now()->format('m-d-Y');
        $agents = User::where('status', true)->whereHas("roles", fn($q) => $q->whereIn("name", ["Agent"]))->get();
        foreach ($agents as $agent) {
            $frequency = 0;
            $baseQuery = Lead::whereNull('converted_to_listing_lead')
                ->where('status', 1)
                ->where('is_archive', 0)
                ->where('agent_id', $agent->id)
                ->where(function ($q) use ($agent) {
                    $q->where('created_by_id', $agent->id)
                        ->orWhere('updated_by_id', $agent->id);
                });
            $leadCount = $this->queryDecider($baseQuery, 'updated_at', $duration)->count();

            $BaseleadNotesCount = LeadNote::where('created_by_id', $agent->id);
            $leadNotesCount = $this->queryDecider($BaseleadNotesCount, 'updated_at', $duration)->count();

            $combinedLeadsCount = $leadCount + $leadNotesCount;

            $frequency += $combinedLeadsCount;

            $BaselistingCount = Property::where('is_archived', 0)
                ->where('agent_id', $agent->id)->where(function ($q) use ($agent) {
                $q->where('created_by_id', $agent->id)
                    ->orWhere('updated_by_id', $agent->id);
            });
            $listingCount = $this->queryDecider($BaselistingCount, 'updated_at', $duration)->count();

            $baseListingNotesCount = PropertyNote::where('created_by_id', $agent->id);
            $listingNotesCount = $this->queryDecider($baseListingNotesCount, 'updated_at', $duration)->count();

            $combinedListingCount = $listingCount + $listingNotesCount;

            $frequency += $combinedListingCount;

            $baseListingLeadsCount = ListingLead::where('is_archive', 0)
                ->where('agent_id', $agent->id)->where(function ($q) use ($agent) {
                $q->where('created_by_id', $agent->id)
                    ->orWhere('updated_by_id', $agent->id);
            });
            $listingLeadsCount = $this->queryDecider($baseListingLeadsCount, 'updated_at', $duration)->count();

            $baseListingLeadNotesCount = ListingLead::where('created_by_id', $agent->id);
            $listingLeadNotesCount = $this->queryDecider($baseListingLeadNotesCount, 'updated_at', $duration)->count();

            $combinedListingLeadCount = $listingLeadsCount + $listingLeadNotesCount;

            $frequency += $combinedListingLeadCount;

            $baseCalenderEventsCount = Event::where('agent_id', $agent->id)->where(function ($q) use ($agent) {
                $q->where('created_by_id', $agent->id)
                    ->orWhere('updated_by_id', $agent->id);
            });
            $calenderEventsCount = $this->queryDecider($baseCalenderEventsCount, 'updated_at', $duration)->count();

            $frequency += $calenderEventsCount;

            $key = $agent->full_name;
            $agentsLeadCount[$key] = $frequency;

            $agentsLeadCountID[$agent->id] = $frequency;

        }

        arsort($agentsLeadCount);
        arsort($agentsLeadCountID);

        $_agentsLeadCountID = array_slice($agentsLeadCountID, 0, 20, true);
        $_last_agentsLeadCountID = array_slice($agentsLeadCountID, -20, 20, true);

        $__agentsLeadCountID = array_keys($_agentsLeadCountID);
        $__last_agentsLeadCountID = array_keys($_last_agentsLeadCountID);

        $topAgents = User::whereHas("roles", fn($q) => $q->whereIn("name", ["Agent"]))->whereIn('id', $__agentsLeadCountID)
            ->orderByRaw(DB::raw("FIELD(id, " . implode(',', $__agentsLeadCountID) . ")"))
            ->get();
        $leadsAgents = User::whereHas("roles", fn($q) => $q->whereIn("name", ["Agent"]))->whereIn('id', $__last_agentsLeadCountID)
            ->orderByRaw(DB::raw("FIELD(id, " . implode(',', $__last_agentsLeadCountID) . ")"))
            ->get();

        $message = "";
        $message = "<b>AX Secondary CRM</b>\n\n";
        $message .= "Agent Activity Report\n";
        $message .= "----------------------------------------------\n";
        $message .= "Top 20 Agents with most activity in the last 90 days\n";
        $message .= "----------------------------------------------\n";
        foreach ($topAgents as $agent) {
            $message .= $agent->full_name . " - " . $agentsLeadCount[$agent->full_name] . " activities\n";
            $message .= "-- Notes: " . $leadNotesCount . "\n";
            $message .= "-- Listing: " . $listingCount . "\n";
            $message .= "-- Listing Notes: " . $listingNotesCount . "\n";
            $message .= "-- Calendar Events: " . $calenderEventsCount . "\n";
        }
        $message .= "----------------------------------------------\n";
        $message .= "Last 20 Agents with least activity in the last 90 days\n";
        $message .= "----------------------------------------------\n";
        foreach ($leadsAgents as $agent) {
            $message .= $agent->full_name . " - " . $agentsLeadCount[$agent->full_name] . " activities\n";
            $message .= "-- Notes: " . $leadNotesCount . "\n";
            $message .= "-- Listing: " . $listingCount . "\n";
            $message .= "-- Listing Notes: " . $listingNotesCount . "\n";
            $message .= "-- Calendar Events: " . $calenderEventsCount . "\n";
        }

        $telegram = Telegram::bot('notification_bot');
        $telegramMessage = $this->sendTelegramMessage($telegram, $message);
    }

    private function DetailReport()
    {
        $number_of_days = 30;
        $duration = 'from=' . Carbon::now()->subDays($number_of_days)->format('m-d-Y') . '&to=' . Carbon::now()->format('m-d-Y');
        $agents = User::where('status', true)
            ->whereHas("roles", fn($q) => $q->whereIn("name", ["Agent"]))
            ->whereNotIn('id', $this->exempted)
            ->whereNotBetween('created_at', [now()->subDays($number_of_days), now()])
            ->get();

        foreach ($agents as $agent) {
            $frequency = 0;
            $baseQuery = Lead::whereNull('converted_to_listing_lead')
                ->where('status', 1)
                ->where('is_archive', 0)
                ->where('agent_id', $agent->id)
                ->where(function ($q) use ($agent) {
                    $q->where('created_by_id', $agent->id)
                        ->orWhere('updated_by_id', $agent->id);
                });
            $leadCount = $this->queryDecider($baseQuery, 'updated_at', $duration)->count();

            $BaseleadNotesCount = LeadNote::where('created_by_id', $agent->id);
            $leadNotesCount = $this->queryDecider($BaseleadNotesCount, 'updated_at', $duration)->count();

            $combinedLeadsCount = $leadCount + $leadNotesCount;

            $frequency += $combinedLeadsCount;

            $BaselistingCount = Property::where('is_archived', 0)
                ->where('agent_id', $agent->id)->where(function ($q) use ($agent) {
                $q->where('created_by_id', $agent->id)
                    ->orWhere('updated_by_id', $agent->id);
            });
            $listingCount = $this->queryDecider($BaselistingCount, 'updated_at', $duration)->count();

            $baseListingNotesCount = PropertyNote::where('created_by_id', $agent->id);
            $listingNotesCount = $this->queryDecider($baseListingNotesCount, 'updated_at', $duration)->count();

            $combinedListingCount = $listingCount + $listingNotesCount;

            $frequency += $combinedListingCount;

            $baseListingLeadsCount = ListingLead::where('is_archive', 0)
                ->where('agent_id', $agent->id)->where(function ($q) use ($agent) {
                $q->where('created_by_id', $agent->id)
                    ->orWhere('updated_by_id', $agent->id);
            });
            $listingLeadsCount = $this->queryDecider($baseListingLeadsCount, 'updated_at', $duration)->count();

            $baseListingLeadNotesCount = ListingLeadNote::where('created_by_id', $agent->id);
            $listingLeadNotesCount = $this->queryDecider($baseListingLeadNotesCount, 'updated_at', $duration)->count();

            $combinedListingLeadCount = $listingLeadsCount + $listingLeadNotesCount;

            $frequency += $combinedListingLeadCount;

            $baseCalenderEventsCount = Event::where('agent_id', $agent->id)->where(function ($q) use ($agent) {
                $q->where('created_by_id', $agent->id)
                    ->orWhere('updated_by_id', $agent->id);
            });
            $calenderEventsCount = $this->queryDecider($baseCalenderEventsCount, 'updated_at', $duration)->count();

            $frequency += $calenderEventsCount;

            $key = $agent->full_name;
            $agentsLeadCount[$key] = $frequency;

            $agentsLeadCountID[$agent->id] = $frequency;
        }

        arsort($agentsLeadCount);
        arsort($agentsLeadCountID);

        $_agentsLeadCountID = array_slice($agentsLeadCountID, 0, 10, true);
        $_last_agentsLeadCountID = array_slice($agentsLeadCountID, -10, 10, true);

        $__agentsLeadCountID = array_keys($_agentsLeadCountID);
        $__last_agentsLeadCountID = array_keys($_last_agentsLeadCountID);

        $topAgents = User::whereHas("roles", fn($q) => $q->whereIn("name", ["Agent"]))->whereIn('id', $__agentsLeadCountID)
            ->orderByRaw(DB::raw("FIELD(id, " . implode(',', $__agentsLeadCountID) . ")"))
            ->get();
        $leadsAgents = User::whereHas("roles", fn($q) => $q->whereIn("name", ["Agent"]))->whereIn('id', $__last_agentsLeadCountID)
            ->orderByRaw(DB::raw("FIELD(id, " . implode(',', $__last_agentsLeadCountID) . ")"))
            ->get();

        $message = "<b>AX Secondary CRM</b>\n\n";
        $message .= "<b>Agent Activity Report</b>\n";
        $message .= "----------------------------------------------\n";
        $message .= "Top 10 Agents with most activity in the last " . $number_of_days . " days\n";
        $message .= "----------------------------------------------\n";

        foreach ($topAgents as $agent) {
            $baseQuery = Lead::whereNull('converted_to_listing_lead')
                ->where('status', 1)
                ->where('is_archive', 0)
                ->where('agent_id', $agent->id)
                ->where(function ($q) use ($agent) {
                    $q->where('created_by_id', $agent->id)
                        ->orWhere('updated_by_id', $agent->id);
                });
            $leadCount = $this->queryDecider($baseQuery, 'updated_at', $duration)->count();

            $BaseleadNotesCount = LeadNote::where('created_by_id', $agent->id);
            $leadNotesCount = $this->queryDecider($BaseleadNotesCount, 'updated_at', $duration)->count();
            $leadNotesCount = $this->queryDecider($BaseleadNotesCount, 'updated_at', $duration)->count();

            $BaselistingCount = Property::where('is_archived', 0)
                ->where('agent_id', $agent->id)->where(function ($q) use ($agent) {
                $q->where('created_by_id', $agent->id)
                    ->orWhere('updated_by_id', $agent->id);
            });
            $listingCount = $this->queryDecider($BaselistingCount, 'updated_at', $duration)->count();

            $baseListingNotesCount = PropertyNote::where('created_by_id', $agent->id);
            $listingNotesCount = $this->queryDecider($baseListingNotesCount, 'updated_at', $duration)->count();
            $baseListingLeadsCount = ListingLead::where('is_archive', 0)
                ->where('agent_id', $agent->id)->where(function ($q) use ($agent) {
                $q->where('created_by_id', $agent->id)
                    ->orWhere('updated_by_id', $agent->id);
            });
            $listingLeadsCount = $this->queryDecider($baseListingLeadsCount, 'updated_at', $duration)->count();

            $baseListingLeadNotesCount = ListingLeadNote::where('created_by_id', $agent->id);
            $listingLeadNotesCount = $this->queryDecider($baseListingLeadNotesCount, 'updated_at', $duration)->count();

            $baseCalenderEventsCount = Event::where('agent_id', $agent->id)->where(function ($q) use ($agent) {
                $q->where('created_by_id', $agent->id)
                    ->orWhere('updated_by_id', $agent->id);
            });
            $calenderEventsCount = $this->queryDecider($baseCalenderEventsCount, 'updated_at', $duration)->count();

            $message .= $agent->full_name . " - " . $agentsLeadCount[$agent->full_name] . " activities\n";
            $message .= "-- Leads: " . $leadCount . "\n";
            $message .= "-- Notes: " . $leadNotesCount . "\n";
            $message .= "-- Listing: " . $listingCount . "\n";
            $message .= "-- Listing Notes: " . $listingNotesCount . "\n";
            $message .= "-- Listing Leads: " . $listingLeadsCount . "\n";
            $message .= "-- Listing Lead Notes: " . $listingLeadNotesCount . "\n";
            $message .= "-- Calendar Events: " . $calenderEventsCount . "\n\n";
        }

        $message .= "----------------------------------------------\n";
        $message .= "Last 10 Agents with least activity in the last " . $number_of_days . " days\n";
        $message .= "----------------------------------------------\n";

        foreach ($leadsAgents as $agent) {
            $baseQuery = Lead::whereNull('converted_to_listing_lead')
                ->where('status', 1)
                ->where('is_archive', 0)
                ->where('agent_id', $agent->id)
                ->where(function ($q) use ($agent) {
                    $q->where('created_by_id', $agent->id)
                        ->orWhere('updated_by_id', $agent->id);
                });
            $leadCount = $this->queryDecider($baseQuery, 'updated_at', $duration)->count();

            $BaseleadNotesCount = LeadNote::where('created_by_id', $agent->id);
            $leadNotesCount = $this->queryDecider($BaseleadNotesCount, 'updated_at', $duration)->count();
            $leadNotesCount = $this->queryDecider($BaseleadNotesCount, 'updated_at', $duration)->count();

            $BaselistingCount = Property::where('is_archived', 0)
                ->where('agent_id', $agent->id)->where(function ($q) use ($agent) {
                $q->where('created_by_id', $agent->id)
                    ->orWhere('updated_by_id', $agent->id);
            });
            $listingCount = $this->queryDecider($BaselistingCount, 'updated_at', $duration)->count();

            $baseListingNotesCount = PropertyNote::where('created_by_id', $agent->id);
            $listingNotesCount = $this->queryDecider($baseListingNotesCount, 'updated_at', $duration)->count();
            $baseListingLeadsCount = ListingLead::where('is_archive', 0)
                ->where('agent_id', $agent->id)->where(function ($q) use ($agent) {
                $q->where('created_by_id', $agent->id)
                    ->orWhere('updated_by_id', $agent->id);
            });
            $listingLeadsCount = $this->queryDecider($baseListingLeadsCount, 'updated_at', $duration)->count();

            $baseListingLeadNotesCount = ListingLeadNote::where('created_by_id', $agent->id);
            $listingLeadNotesCount = $this->queryDecider($baseListingLeadNotesCount, 'updated_at', $duration)->count();

            $baseCalenderEventsCount = Event::where('agent_id', $agent->id)->where(function ($q) use ($agent) {
                $q->where('created_by_id', $agent->id)
                    ->orWhere('updated_by_id', $agent->id);
            });
            $calenderEventsCount = $this->queryDecider($baseCalenderEventsCount, 'updated_at', $duration)->count();

            $message .= $agent->full_name . " - " . $agentsLeadCount[$agent->full_name] . " activities\n";
            $message .= "-- Leads: " . $leadCount . "\n";
            $message .= "-- Notes: " . $leadNotesCount . "\n";
            $message .= "-- Listing: " . $listingCount . "\n";
            $message .= "-- Listing Notes: " . $listingNotesCount . "\n";
            $message .= "-- Listing Leads: " . $listingLeadsCount . "\n";
            $message .= "-- Listing Lead Notes: " . $listingLeadNotesCount . "\n";
            $message .= "-- Calendar Events: " . $calenderEventsCount . "\n\n";
        }
        $message .= "----------------------------------------------\n";

        $telegram = Telegram::bot('notification_bot');
        $telegramMessage = $this->sendTelegramMessage($telegram, $message);
    }

    private function queryDecider($query, $column, $operator)
    {

        if (strpos($operator, '&') !== false) {
            $operator = explode('&', $operator);
            $from = null;
            $to = null;
            foreach ($operator as $item) {
                if (strpos($item, 'from=') !== false) {
                    $from = Carbon::createFromFormat('m-d-Y', str_replace('from=', '', $item))->format('Y-m-d');
                } elseif (strpos($item, 'to=') !== false) {
                    $to = Carbon::createFromFormat('m-d-Y', str_replace('to=', '', $item))->format('Y-m-d');
                }
            }
            if ($from !== null && $to !== null) {
                return $query->whereBetween($column, [$from, $to]);
            } else {
                dd('Missing from or to date');
            }
        } else {

            return $this->queryBuilder($query, $column, $operator);
        }
    }

    public function sendTelegramMessage($telegram, $message)
    {
        try {
            if (env('APP_ENV') == 'production') {
                $chatids = [
                    '-1001435755800',
                    '1473874718',
                    '5970238984',
                ];

                foreach ($chatids as $chatid) {
                    $telegram->sendMessage([
                        'chat_id' => $chatid,
                        'text' => $message,
                        'parse_mode' => 'html',
                    ]);
                }

            } else {

                $chatids = [
                    '5970238984',
                    // '1417490671',
                ];
                foreach ($chatids as $chatid) {
                    $telegram->sendMessage([
                        'chat_id' => $chatid,
                        'text' => $message,
                        'parse_mode' => 'html',
                    ]);
                }
            }

            return true;
        } catch (\Throwable $th) {
            \Log::info('Telegram error log: ' . $th->getMessage() . " chat_id is: -1001435755800 message is: " . $message);
            return false;
        }
    }
}
