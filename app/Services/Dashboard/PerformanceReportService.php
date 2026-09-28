<?php

namespace App\Services\Dashboard;




use App\Models\Lead;
use App\Models\Portal;
use App\Models\Property;
use App\Models\PropertyMeta;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;
use App\Services\Dashboard\DashboardReportFilterService;

class PerformanceReportService
{

    protected $filters;

    public function __construct(DashboardReportFilterService $filters)
    {
        $this->filters = $filters;
    }
    public function summary($request, $params)
    {
        $tab = $params['tab'];
        $data = [
            'last_event_since' => [],//$this->lastEventSince($request, $params),
            'leads_in_new_status' => $this->leadsInNewStatus($request, $params),
            'incomplete_leads' => $this->incompleteLeadCountsByAgent($request, $params),
            'suspension_counts' => $this->suspensionCounts($request, $params),
            // 'zero_deals_since_joining' => $this->zeroDealsSinceJoining($request, $params),
            'days_since_last_deal' => $this->daysSinceLastDeal($params),
            'highest_no_of_leads_lost' => $this->highestNoOfLeadsLost($params),
        ];
        return $data;
    }

    public function daysSinceLastDeal($params, $limit = 10)
    {
        $users = User::whereHas("roles", function ($query) {
            $query->where("name", "Agent");
        })
            ->whereNotIn('id', $this->filters->getExcludedUsers())
            ->when($params['filter_by_users'], fn($query) => $query->whereIn("id", $params['user_ids']))
            ->when(!$params['show_inactive_users'], fn($q) => $q->where("status", true))
            ->when($params['group'] == 'secondary', function ($q) use ($params) {
                $q->where("region_id", 1);
            })
            ->whereDate('last_deal_date', '>=', '2025-01-01')
            ->orderBy('last_deal_date')
            ->take($limit)
            ->get();

        $data = [];
        foreach ($users as $user) {
            $daysSinceLastDeal = now()->diffInDays($user->last_deal_date);
            $data[] = [
                'agent_id' => $user->id,
                'name' => $user?->name,
                'image' => $user?->thumb_url,
                'days_since_last_deal' => $daysSinceLastDeal,
                'manager' => $user?->parent?->name ?? '',
                'group' => $user->parent?->group?->name ?? ''
            ];
        }
        return $data;
    }

//    public function zeroDealsSinceJoining($request, $params)
//    {
//        $twoMonthsAgo = Carbon::now()->subMonths(2);
//        $childrenIds = $params['user_ids'];
//        $noDealAgents = User::query();
//        // $this->applyUserDashboardFilter($noDealAgents, $request);
//        $noDealAgents = $noDealAgents->when($params['filter_by_users'], fn($query) => $query->whereIn("id", $childrenIds))
//            ->whereHas('roles', function ($q) {
//                $q->where('name', 'Agent');
//            })
//            ->where('joining_date', '<', $twoMonthsAgo)
//            ->whereNull('last_deal_date')
//            ->orderBy('joining_date', 'desc')
//            ->limit(10)
//            ->when(!$params['show_inactive_users'], fn($q) => $q->where("status", true))
//            ->whereNotIn('id', $this->filters->getExcludedUsers())
//            ->select('id', 'name', 'joining_date')
//            ->get();
//
//        return $noDealAgents;
//    }

    public function suspensionCounts($request, $params)
    {
        $childrenIds = $params['user_ids'];

        $users = User::query();
        $users = $users->when($params['filter_by_users'], fn($query) => $query->whereIn("id", $childrenIds))
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })
            ->when($params['group'] == 'secondary', function ($q) use ($params) {
                $q->where("region_id", 1);
            })
            ->withCount('suspensions') // Assuming the name of the relationship is 'suspensions'
            ->orderByDesc('suspensions_count')
            ->when(!$params['show_inactive_users'], fn($q) => $q->where("status", true))
            ->whereNotIn('id', $this->filters->getExcludedUsers())
            ->limit(10)
            ->get();
        $data = [];
        foreach ($users as $user) {
            $data[] = [
                'agent_id' => $user->id,
                'name' => $user->name,
                'image' => $user->thumb_url,
                'suspension_count' => $user->suspensions_count,
                'manager' => $user?->parent?->name ?? '',
                'group' => $user->parent?->group?->name ?? ''
            ];
        }
        return $data;
        //        $agents_data                       = [];
        //        $primary_agents_message_suspension = '';
        //        $primary_agents_message            = '';
        //        foreach ($users as $agent) {
        //            if ($agent->suspensions->count() > 2) {
        //                $primary_agents_message_suspension .= '  ' . $agent->full_name . ": " . $agent->suspensions->count() . "\n";
        //            }
        //
        //            $lastEventDate      = $agent->events()->max('created_at');
        //            $daysSinceLastEvent = $lastEventDate ? now()->diffInDays($lastEventDate) : now()->diffInDays($agent->created_at);
        //
        //            if ($daysSinceLastEvent > 7) {
        //                $agents_data[] = [
        //                    'name'        => $agent->full_name,
        //                    'days'        => $daysSinceLastEvent,
        //                    'image'       => $agent->thumb_url,
        //                    'bitrix_role' => $agent->bitrix_role,
        //                    'manager'     => $agent->parent ? $agent->parent->full_name : 'No Manager',
        //                ];
        //            }
        //        }
        //
        //        usort($agents_data, function ($a, $b) {
        //            return $b['days'] - $a['days'];
        //        });
        //        $agents_data = collect($agents_data);
        //        return $agents_data;
    }

    public function leadsInNewStatus($request, $params)
    {
        $start_date = $params['start_date'];
        $end_date   = $params['end_date'];
        //old code on DashboardV2Controller userReports
        $topUsers = Lead::select('agent_id', DB::raw('COUNT(*) as lead_count'))
            ->where('lead_status_id', 1)
            ->whereNotNull('agent_id')
            ->groupBy('agent_id')
            ->when($params['group'] == 'secondary', function ($q) use ($params) {
                $q->whereHas("agent", function ($query) {
                    $query->where("users.region_id", 1);
                });
            })
            ->when($params['filter_by_users'], function ($q) use ($params) {
                $q->whereIn("agent_id", $params['user_ids']);
            })
            ->when(!$params['show_inactive_users'], function ($q) use ($params) {
                $q->whereHas("agent", function ($query) use ($params) {
                    $query->where("users.status", true);
                });
            })
            //If we want to apply created date filter here
            ->when($start_date && $end_date, function ($q) use ($start_date, $end_date) {
                $q->whereBetween('created_at', [$start_date, $end_date]);
            })
            ->where('is_archive', 0)
            ->orderByDesc('lead_count')
            ->whereNull("converted_to_listing_lead")
            ->whereNotIn('agent_id', $this->filters->getExcludedUsers())
            ->limit(10)
            ->get();
        $data = [];
        foreach ($topUsers as $key => $lead) {
            $user = User::where('id', $lead->agent_id)->with(['parent','parent.group'])->first();
            $data[] = [
                'agent_id' => $lead->agent_id,
                'name' => $user->name,
                'image' => $user->thumb_url,
                'lead_count' => $lead->lead_count,
                'manager' => $user?->parent?->name ?? '',
                'group' => $user->parent?->group?->name ?? ''
            ];
        }

        return $data;
    }

    public function highestNoOfLeadsLost($params)
    {
        $start_date = $params['start_date'];
        $end_date   = $params['end_date'];
        $topUsers = Lead::select([
            'previous_agent_id',
            DB::raw('COUNT(leads.id) as lead_count')
        ])
            ->where('portal_lead', true)
            ->whereNotNull('previous_agent_id')
            ->when($params['group'] == 'secondary', function ($q) use ($params) {
                $q->whereHas("previousAgent", function ($query) {
                    $query->where("region_id", 1);
                });
            })
            ->when($params['filter_by_users'], function ($q) use ($params) {
                $q->whereIn("previous_agent_id", $params['user_ids']);
            })
            ->when(!$params['show_inactive_users'], function ($q) {
                $q->join('users as previous_agents', 'leads.previous_agent_id', '=', 'previous_agents.id')
                    ->where('previous_agents.status', true);
            })
            ->when($start_date && $end_date, function ($q) use ($start_date, $end_date) {
                $q->whereBetween('leads.created_at', [$start_date, $end_date]);
            })
            ->whereNotIn('previous_agent_id', $this->filters->getExcludedUsers())
            ->groupBy('previous_agent_id')
            ->orderByDesc('lead_count')
            ->limit(10)
            ->get();

        $data = [];
        foreach ($topUsers as $key => $lead) {
            $user = User::where('id', $lead->previous_agent_id)->with(['parent','parent.group'])->first();
            $data[] = [
                'agent_id' => $lead->previous_agent_id,
                'name' => $user->name,
                'image' => $user->thumb_url,
                'lead_count' => $lead->lead_count,
                'manager' => $user?->parent?->name ?? '',
                'group' => $user->parent?->group?->name ?? ''
            ];
        }

        return $data;
    }

    public function incompleteLeadCountsByAgent($request, $params)
    {
        $start_date = $params['start_date'];
        $end_date   = $params['end_date'];
        $incompleteLeadsUsers = Lead::select('agent_id', DB::raw('COUNT(*) as leads_count'))
            ->where(function ($query) {
                $query->where('first_name', 'LIKE', '%new%')->orWhereNull('first_name');
            })
            ->whereNull('converted_to_listing_lead')
            ->whereNotIn('lead_status_id', [4, 12])
            ->whereNotNull("agent_id")
            ->when($params['group'] == 'secondary', function ($q) use ($params) {
                $q->whereHas("agent", function ($query) {
                    $query->where("users.region_id", 1);
                });
            })
            ->when($params['filter_by_users'], function ($q) use ($params) {
                $q->whereIn("agent_id", $params['user_ids']);
            })
            ->when(!$params['show_inactive_users'], function ($q) use ($params) {
                $q->whereHas("agent", function ($query) use ($params) {
                    $query->where("users.status", true);
                });
            })
            // ->whereDate('created_at', '>', '2024-01-15')
            ->when($start_date && $end_date, function ($q) use ($start_date, $end_date) {
                $q->whereBetween('created_at', [$start_date, $end_date]);
            })
            ->where('is_archive', 0)
            //
            // ->whereNull('property_id')
            ->groupBy('agent_id')
            ->orderByDesc('leads_count')
            ->whereNotIn('agent_id', $this->filters->getExcludedUsers())
            ->limit(10)
            ->get();


        $finalReport = $incompleteLeadsUsers->map(function ($lead) {
            $user = User::where('id', $lead->agent_id)->with(['parent','parent.group'])->first();
            return [
                'agent_id' => $lead->agent_id,
                'name' => $user->name ?? 'Unknown',
                'leads_count' => $lead->leads_count,
                'image' => $user->thumb_url ?? null,
                'manager' => $user?->parent?->name ?? '',
                'group' => $user->parent?->group?->name ?? ''
            ];
        });
        return $finalReport;
    }

    public function lastEventSince($request, $params)
    {
        $childrenIds = $params['user_ids'];

        $users = User::query();
        // $this->applyUserDashboardFilter($users, $request);
        $users = $users->when($params['filter_by_users'], fn($query) => $query->whereIn("id", $childrenIds))
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })
            ->when($params['group'] == 'secondary', function ($q) use ($params) {
                $q->where("region_id", 1);
            })
            ->when(!$params['show_inactive_users'], function ($q) use ($params) {
                $q->where("status", true);
            })
            ->withCount('suspensions') // Assuming the name of the relationship is 'suspensions'
            ->orderByDesc('suspensions_count')
            ->whereNotIn('id', $this->filters->getExcludedUsers())
            ->get();

        $agents_data                       = [];
        $primary_agents_message_suspension = '';
        $primary_agents_message            = '';
        foreach ($users as $agent) {
            if ($agent->suspensions->count() > 2) {
                $primary_agents_message_suspension .= '  ' . $agent->full_name . ": " . $agent->suspensions->count() . "\n";
            }

            $lastEventDate      = $agent->events()->max('created_at');
            $daysSinceLastEvent = $lastEventDate ? now()->diffInDays($lastEventDate) : now()->diffInDays($agent->created_at);

            if ($daysSinceLastEvent > 7) {
                $agents_data[] = [
                    'name'        => $agent->full_name,
                    'days'        => $daysSinceLastEvent,
                    'image'       => $agent->thumb_url,
                    'bitrix_role' => $agent->bitrix_role,
                    'manager'     => $agent->parent ? $agent->parent->full_name : 'No Manager',
                    'group' => $agent->parent?->group?->name ?? '',
                ];
            }
        }

        usort($agents_data, function ($a, $b) {
            return $b['days'] - $a['days'];
        });
        $agents_data = collect($agents_data);
        return $agents_data;
    }


}
