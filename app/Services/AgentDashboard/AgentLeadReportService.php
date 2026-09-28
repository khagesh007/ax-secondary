<?php
namespace App\Services\AgentDashboard;

use App\Models\Lead;
use App\Models\LeadStatus;
use App\Models\Medium;
use App\Models\Portal;
use App\Models\Source;
use App\Models\User;
use App\Services\Dashboard\DashboardReportFilterService;
use App\Traits\PropertyFinderTrait;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class AgentLeadReportService
{
    use PropertyFinderTrait;
    protected $filters;

    public function __construct(DashboardReportFilterService $filters)
    {
        $this->filters = $filters;
    }

    public function summary($request, $params)
    {


        $agents                     = [];
        $agents = User::whereIn('id', $params['user_ids'])->where('status', true)->whereHas("roles", fn($q) => $q->whereIn("name", ["Agent"]))->pluck('name', 'id');
        $data = [
            'overview'                    => $this->sourceWiseMediumWiseSummary($request, $params),
            'source_wise_lead_count' => $this->sourceWiseLeadData($params),
            'medium_wise_lead_count'      => $this->mediumWiseLeadCounts($request, $params, $agents),
            'lead_status_count_with_user' => $this->getLeadStatusCountWithUser($request, $params),
        ];
        return $data;
    }

    public function sourceWiseMediumWiseSummary(Request $request, $params): array
    {
        $start_date  = $params['start_date'];
        $end_date    = $params['end_date'];
        $childrenIds = $params['user_ids'];

        $excludedUsers = $this->filters->getExcludedUsers();
        $sources = Source::whereIn('name', ['Property Finder', 'Bayut', 'Dubizzle'])->pluck('name', 'id');
        $mediums = Medium::whereIn('name', ['Email', 'WhatsApp', 'Call'])->pluck('name', 'id');

        $query = Lead::query();

        if ($start_date && $end_date) {
            $query = $this->filters->dateFilter($query, "leads.created_at", $start_date, $end_date);
        }

        $caseSource = "CASE";
        foreach ($sources as $id => $name) {
            $caseSource .= " WHEN leads.source_id = $id THEN '$name'";
        }
        $caseSource .= " ELSE 'Other' END as source_name,";

        $caseMedium = "CASE";
        foreach ($mediums as $id => $name) {
            $caseMedium .= " WHEN leads.lead_medium_id = $id THEN '$name'";
        }
        $caseMedium .= " ELSE 'Other' END as medium_name,";

        $selectQuery = $caseSource . $caseMedium . " COUNT(leads.id) as count";

        $leadsData = $query->selectRaw($selectQuery)
            ->leftJoin("sources", "sources.id", "=", "leads.source_id")
            ->leftJoin("mediums", "mediums.id", "=", "leads.lead_medium_id")
            ->join("users", function ($join) use ($request) {
                $query = $join->on("leads.agent_id", "=", "users.id");
            })
            ->whereNotNull("leads.agent_id")

            ->when($params['filter_by_users'], function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds)->whereNull("converted_to_listing_lead");
            })
            ->when(auth()->user()->hasRole("Admin - Abu Dhabi"), function ($q) {
                $q->where("leads.region_id", 2);
            })
            ->when(!$params['show_inactive_users'], function ($q) use ($params) {
                $q->where("users.status", true);
            })

            ->when(($params['show_archived_leads'] && $params['isSuperAdmin']), fn($q) => $q->where("leads.status", true)->where('is_archive', false))
            ->groupBy("source_name", "medium_name")
            ->whereNotIn('agent_id', $excludedUsers)
            ->get()
            ->makeHidden(['title'])
            ->toArray();

        $overView = [];

        foreach ($leadsData as $data) {
            $source = $data['source_name'];
            $medium = strtolower($data['medium_name']);
            $count  = $data['count'];


            if (! isset($overView[$source])) {
                $overView[$source] = [
                    'name'    => $source,
                    'counter' => [
                        'email'    => 0,
                        'whatsapp' => 0,
                        'call'     => 0,
                        'other'    => 0,
                    ],
                ];
            }

            // Assign counts based on medium type
            if (in_array($medium, ['email', 'whatsapp', 'call'])) {
                $overView[$source]['counter'][$medium] = $count;
            } else {
                $overView[$source]['counter']['other'] += $count; // Accumulate counts for "Other"
            }
        }

//Convert Associative Array to Indexed Array
        $overView = array_values($overView);
        return $overView;

    }
    public function getTotalLeadsCountSummary($params)
    {
        if ($params['group_id'] != null) {
            $agents = User::where('status', true)->whereHas("roles", fn($q) => $q->whereIn("name", ["Agent"]))->whereIn('id', $params['user_ids'])->pluck('name', 'id');
        } else {
            $agents = [];
        }
        $source_counts = $this->sourceWiseLeadData($params, $agents);
        $source_data = [];
        foreach ($source_counts as $key => $value) {
            $source_data[$value['name']] = $value['count'];
        }

        $total_leads = Lead::where('region_id', 1)
            ->when($params['filter_by_users'], fn($query) => $query->whereIn("agent_id", $params['user_ids']))
            ->where('created_at', '>=', $params['start_date'])
            ->where('created_at', '<=', $params['end_date'])
            ->whereNotIn('agent_id', $this->filters->getExcludedUsers())
            ->count();
        $total_property_finder = $source_data['pf_count'];
        $total_bayut = $source_data['bayut_count'];
        $total_dubizzle = $source_data['dubizzle_count'];
        $total_other = $source_data['other_count'];

        return [
            'total_leads' => $total_leads,
            'total_property_finder' => $total_property_finder,
            'total_bayut' => $total_bayut,
            'total_dubizzle' => $total_dubizzle,
            'total_other'   =>  $total_other,
        ];
    }


    public function sourceWiseLeadData($params)
    {
        $start_date = $params['start_date'];
        $end_date   = $params['end_date'];
        $excludedUsers = $this->filters->getExcludedUsers();
        $childrenIds = $params['user_ids'];
        $query       = Lead::query();
        $query       = $this->filters->dateFilter($query, "leads.created_at", $start_date, $end_date);

        $selectQuery = "sources.name name,";
        $selectQuery .= "COUNT(leads.id) as count";
        $selectQuery = "COUNT(DISTINCT CASE WHEN sources.name = 'Property Finder' THEN leads.id END) AS pf_count,";
        $selectQuery .= "COUNT(DISTINCT CASE WHEN sources.name = 'Bayut' THEN leads.id END) AS bayut_count,";
        $selectQuery .= "COUNT(DISTINCT CASE WHEN sources.name = 'Dubizzle' THEN leads.id END) AS dubizzle_count,";
        $selectQuery .= "COUNT(DISTINCT CASE WHEN sources.name NOT IN('Property Finder', 'Bayut', 'Dubizzle') THEN leads.id END) AS other_count";

        $leadCounts = $query->selectRaw($selectQuery)
            ->leftJoin("sources", "sources.id", "=", "leads.source_id")
            ->join("users", function ($join) use ($params) {
                $query = $join->on("leads.agent_id", "=", "users.id");
            })
            ->when(!$params['show_inactive_users'], function ($q) use ($params) {
                $q->where("users.status", true);
            })
            ->whereNotNull("leads.agent_id")
            ->whereNotIn('agent_id', $excludedUsers)
            ->when($params['filter_by_users'], function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })
            ->when(auth()->user()->hasRole("Admin - Abu Dhabi"), function ($q) {
                $q->where("leads.region_id", 2);
            })

            ->first();
        $lead_counts = $leadCounts->toArray();
        //now unset title key
        unset($lead_counts['title']);
        $data = [];
        foreach ($lead_counts as $key => $value) {
            $data[] = [
                'name'  => $key,
                'count' => $value,
            ];
        }
        return $data;

    }

    public function mediumWiseLeadCounts(Request $request, $params, $agents)
    {

        $start_date = $params['start_date'];
        $end_date   = $params['end_date'];
        $excludedUsers = $this->filters->getExcludedUsers();
        $query       = Lead::query();
        $query       = $this->filters->dateFilter($query, "leads.created_at", $start_date, $end_date);
        $childrenIds = $params['user_ids'];
        $selectQuery = "mediums.name name,";
        $selectQuery .= "COUNT(leads.id) as count";
        return $query->selectRaw($selectQuery)
            ->leftJoin("mediums", "leads.lead_medium_id", "=", "mediums.id")
            ->join("users", function ($join) use ($request) {
                $query = $join->on("leads.agent_id", "=", "users.id");
            })
            ->when(!$params['show_inactive_users'], function ($q) use ($params) {
                $q->where("users.status", true);
            })
            ->whereNotNull("leads.agent_id")
            ->whereNotIn('agent_id', $excludedUsers)
            ->when($params['filter_by_users'], function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })
            ->when(auth()->user()->hasRole("Admin - Abu Dhabi"), function ($q) {
                $q->where("leads.region_id", 2);
            })
            ->groupBy("leads.lead_medium_id")
            ->groupBy("mediums.name")
            ->get()
            ->makeHidden(['title']) //this will remove title keyword auto coming from Lead model file
            ->toArray();
    }

    public function getLeadStatusCountWithUser($request, $params)
    {

        $childrenIds = $params['user_ids'];
        $excludedUsers = $this->filters->getExcludedUsers();
        if ($request->date) {
            $startDate = Carbon::parse($params['start_date'])->startOfDay();
            $endDate   = Carbon::parse($params['end_date'])->endOfDay();
        } else {
            $startDate = null;
            $endDate   = null;
        }

        try {

            $statuses = LeadStatus::where('status', true)->pluck('name', 'id')->toArray();

            $leadStatusReport = Lead::with('agent')
                ->when($params['filter_by_users'], function ($q) use ($childrenIds) {
                    $q->whereIn("agent_id", $childrenIds);
                })
                ->where('status', true)
                ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('created_at', [$startDate, $endDate]);
                })
                ->when($params['group'] == 'secondary', function ($q) {
                    $q->whereHas("agent", function ($query) {
                        $query->where("users.region_id", 1);
                    });
                })
                ->when(!$params['show_inactive_users'], function ($q) use ($params) {
                    $q->whereHas("agent", function ($query) use ($params) {
                        $query->where("users.status", true);
                    });
                })
                ->whereNotIn('agent_id', $excludedUsers)
                ->select(
                    'agent_id',
                    'lead_status_id',
                    DB::raw("COUNT(*) as lead_count") // Count leads per status
                )
                ->groupBy('agent_id', 'lead_status_id')
                ->get();

            $reportData = [];

            foreach ($leadStatusReport as $lead) {
                $agentName = $lead->agent->full_name ?? '';
                $statusId  = $lead->lead_status_id;
                if (! isset($reportData[$agentName])) {
                    $reportData[$agentName] = [
                        'agent_name' => $agentName,
                        'manager' => $lead?->agent?->parent?->full_name ?? '',
                        'group' => $lead?->agent?->parent?->group?->name ?? '',
                        'image' => $lead?->agent?->thumb_url ?? '',
                        'statuses'   => [],
                    ];

                    foreach ($statuses as $id => $name) {
                        $reportData[$agentName]['statuses'][$id] = [
                            'name'  => $name,
                            'count' => 0,
                        ];
                    }
                }

                if (isset($statuses[$statusId])) {
                    $reportData[$agentName]['statuses'][$statusId]['count'] = $lead->lead_count;
                }
            }

            $finalReport = array_values(array_map(function ($agent) {
                $agent['statuses'] = array_values($agent['statuses']); // Convert to indexed array
                return $agent;
            }, $reportData));

            return $finalReport;

        } catch (\Exception $e) {
            return $e->getMessage();
        }

    }

}
