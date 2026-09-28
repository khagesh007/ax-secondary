<?php

namespace App\Services\Dashboard;

use App\Models\Property;
use App\Models\User;
use App\Services\Dashboard\DashboardReportFilterService;
use Carbon\Carbon;
use DB;

class ListingReportService
{
    protected $filters;

    public function __construct(DashboardReportFilterService $filters)
    {
        $this->filters = $filters;
    }

    public function summary($request, $params)
    {
        $tab                 = $params['tab'];
        $date                = $params['date'];
        $show_inactive_users = $params['show_inactive_users'];
        $show_archived_leads = $params['show_archived_leads'];

        $agents = [];
        if ($params['isSuperAdmin']) {
            $agents             = User::where('status', true)->whereIn('id', $params['user_ids'])->whereHas("roles", fn($q) => $q->whereIn("name", ["Agent"]))->pluck('name', 'id');
            $user_ids           = $agents->keys()->toArray();
        }

        $data = [
            'agent_listings_by_statuses' => $this->getAgentListingCountByStatuses($request, $params),
            'listing_counts_by_publish_status' => $this->getListingCountsByPublishStatus($params),
            'listing_counts_by_category' => $this->getListingCountsByCategory($params),
        ];
        return $data;
    }

    public function getListingCountsByPublishStatus($params): array
    {
        $publishedCount = Property::where('is_live', '1')
            ->when($params['filter_by_users'], function ($q) use ($params) {
                $q->whereIn("agent_id", $params['user_ids']);
            })
            ->when($params['start_date'] && $params['end_date'] && !$params['exempt_listing_date_filter'], function ($q) use ($params) {
                $q->whereBetween('created_at', [$params['start_date'], $params['end_date']]);
            })
            ->when($params['group'] == 'secondary', function ($q) {
                $q->whereHas("agent", function ($query) {
                    $query->where("users.region_id", 1);
                });
            })
            ->whereNotIn('agent_id', $this->filters->getExcludedUsers())
            ->where('ref_no', 'NOT LIKE', 'axc-%-r%')
            ->whereNull('republished_date')
            ->count() ?? 0;

        $unpublishedCount = Property::where('is_live', '1')
            ->when($params['filter_by_users'], function ($q) use ($params) {
                $q->whereIn("agent_id", $params['user_ids']);
            })
            ->when($params['start_date'] && $params['end_date'] && !$params['exempt_listing_date_filter'], function ($q) use ($params) {
                $q->whereBetween('created_at', [$params['start_date'], $params['end_date']]);
            })
            ->when($params['group'] == 'secondary', function ($q) {
                $q->whereHas("agent", function ($query) {
                    $query->where("users.region_id", 1);
                });
            })
            ->whereNotIn('agent_id', $this->filters->getExcludedUsers())
            ->where('ref_no', 'NOT LIKE', 'axc-%-r%')
            ->whereNull('republished_date')
            ->whereNull('published_date')
            ->count() ?? 0;

        $rePublishedCount = Property::where('is_live', '1')
            ->when($params['filter_by_users'], function ($q) use ($params) {
                $q->whereIn("agent_id", $params['user_ids']);
            })
            ->when($params['start_date'] && $params['end_date'] && ! $params['exempt_listing_date_filter'], function ($q) use ($params) {
                $q->whereBetween('created_at', [$params['start_date'], $params['end_date']]);
            })
            ->when($params['group'] == 'secondary', function ($q) {
                $q->whereHas("agent", function ($query) {
                    $query->where("users.region_id", 1);
                });
            })
            ->whereNotIn('agent_id', $this->filters->getExcludedUsers())
            ->where('ref_no', 'LIKE', 'axc-%-r%')
            ->count() ?? 0;

        return [
            'published' => $publishedCount,
            'unpublished' => $unpublishedCount,
            'republished' => $rePublishedCount,
        ];
    }

    public function getListingCountsByCategory($params): array
    {
        $commercialCount = Property::where('is_live', '1')
            ->when($params['filter_by_users'], function ($q) use ($params) {
                $q->whereIn("agent_id", $params['user_ids']);
            })
            ->when($params['start_date'] && $params['end_date'] && !$params['exempt_listing_date_filter'], function ($q) use ($params) {
                $q->whereBetween('created_at', [$params['start_date'], $params['end_date']]);
            })
            ->when($params['group'] == 'secondary', function ($q) {
                $q->whereHas("agent", function ($query) {
                    $query->where("users.region_id", 1);
                });
            })
            ->whereNotIn('agent_id', $this->filters->getExcludedUsers())
            ->where(function ($q){
                $q->where('for', 'Commercial for sale')->orWhere('for', 'Commercial for rent');
            })
            ->count() ?? 0;

        $residentialCount = Property::where('is_live', '1')
            ->when($params['filter_by_users'], function ($q) use ($params) {
                $q->whereIn("agent_id", $params['user_ids']);
            })
            ->when($params['start_date'] && $params['end_date'] && !$params['exempt_listing_date_filter'], function ($q) use ($params) {
                $q->whereBetween('created_at', [$params['start_date'], $params['end_date']]);
            })
            ->when($params['group'] == 'secondary', function ($q) {
                $q->whereHas("agent", function ($query) {
                    $query->where("users.region_id", 1);
                });
            })
            ->whereNotIn('agent_id', $this->filters->getExcludedUsers())
            ->where(function ($q){
                $q->where('for', 'Residential for sale')->orWhere('for', 'Residential for rent');
            })
            ->count() ?? 0;



        return [
            'commercial' => $commercialCount,
            'residential' => $residentialCount,
        ];
    }

    public function getListingCounts($params): array
    {
        $user_ids   = $params['user_ids'];
        $start_date = $params['start_date'];
        $end_date   = $params['end_date'];
        return Property::where('is_live', '1')
            ->when($params['filter_by_users'], function ($q) use ($user_ids) {
                $q->whereIn("agent_id", $user_ids);
            })
            ->when($start_date && $end_date && ! $params['exempt_listing_date_filter'], function ($q) use ($start_date, $end_date) {
                $q->whereBetween('created_at', [$start_date, $end_date]);
            })
            ->when($params['group'] == 'secondary', function ($q) {
                $q->whereHas("agent", function ($query) {
                    $query->where("users.region_id", 1);
                });
            })
            ->whereNotIn('agent_id', $this->filters->getExcludedUsers())
            ->count() ?? 0;
    }

    public function getTop10Lister($params, $agents): array
    {
        $user_ids   = $params['user_ids'];
        $start_date = $params['start_date'];
        $end_date   = $params['end_date'];
        $excludedUsers = $this->filters->getExcludedUsers();
        //  try {
        $listings = Property::when($params['group'] == 'secondary', function ($q) {
                $q->whereHas("agent", function ($query) {
                    $query->where("users.region_id", 1);
                });
            })
            ->when(!$params['show_inactive_users'], function ($q) use ($params) {
                $q->whereHas("agent", function ($query) use ($params) {
                    $query->where("users.status", true);
                });
            })
            ->where('is_live', '1')
            ->whereNotIn('agent_id', $excludedUsers)
            ->when($params['filter_by_users'], function ($q) use ($user_ids) {
                $q->whereIn("agent_id", $user_ids);
            })
            ->when($start_date && $end_date && ! $params['exempt_listing_date_filter'], function ($q) use ($start_date, $end_date) {
                $q->whereBetween('created_at', [$start_date, $end_date]);
            })
            ->groupBy('agent_id')
            ->selectRaw('agent_id, COUNT(*) as property_count')
            ->orderByDesc('property_count')
            ->limit(10)
            ->pluck('property_count', 'agent_id')
            ->toArray();
        $data = [];
        if (count($agents) < 1) {
            $agents_ids = array_keys($listings);

            $agents = User::whereIn('id', $agents_ids)->with(['parent','group','parent.group'])->get();
        }else{
            $user_ids           = $agents->keys()->toArray();
            $agents = User::whereIn('id', $user_ids)->with(['parent','group','parent.group'])->get();
        }
        $agent_data = [];
        foreach ($agents as $key => $value) {
            $agent_data[$value->id] = [
                'name'  => $value->name,
                'image' => $value->thumb_url ?? '',
                'manager' => $value?->parent?->name ?? '',
                'group' => $value?->parent?->group?->name ?? '',
            ];
        }

        foreach ($listings as $key => $value) {
            $data[] = [
                'name'  => $agent_data[$key]['name'] ?? '',
                'image' => $agent_data[$key]['image'] ?? '',
                'manager' => $agent_data[$key]['manager'] ?? '',
                'group' => $agent_data[$key]['group'] ?? '',
                'count' => $value,
            ];
        }
        return $data;
        // }catch (\Exception $e){
        //     info('Error in DashboardV3PfController agentWiseListingsPf '.json_encode($e->getMessage()));
        //     return [];
        // }
    }
    public function agentWiseListingsPf($portal, $params, $agents): array
    {

        return [];
    }

    public function getListingCountsByStatus($params): array
    {

        $user_ids   = $params['user_ids'];
        $start_date = $params['start_date'];
        $end_date   = $params['end_date'];
        $excludedUsers = $this->filters->getExcludedUsers();
        $listings   = [];
        $statuses   = ['PENDING', 'DRAFT', 'UNPUBLISHED', 'PUBLISHED', 'ARCHIVED'];
        try {
            foreach ($statuses as $status) {
                $query = Property::query();
                if (! $params['exempt_listing_date_filter']) {
                    $query = $this->filters->dateFilter($query, "properties.created_at", $start_date, $end_date);
                }
                $count = $query->when($params['group'] == 'secondary', function ($q) {
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
                    ->where('publish_status', $status)
                    ->when($params['filter_by_users'], function ($q) use ($user_ids) {
                        $q->whereIn("agent_id", $user_ids);
                    })

                    ->count();
                $listings[] = [
                    'name'  => $status,
                    'count' => $count ?? 0,
                ];
            }
            return $listings;
        } catch (\Exception $e) {
            info('Error in ListingReportService agentWiseListingsPf ' . json_encode($e->getMessage()));
            return [];
        }
    }
    public function getAgentListingCountByStatuses($request, $params)
    {


        $childrenIds = $params['user_ids'];
        $start_date  = $params['start_date'];
        $end_date    = $params['end_date'];

        if ($start_date && $end_date) {

            $startDate = Carbon::parse($start_date)->startOfDay();
            $endDate   = Carbon::parse($end_date)->endOfDay();
        } else {
            $startDate = null;
            $endDate   = null;
        }

      //  $excludeAgents = [4, 307];

        $allStatuses = ['DRAFT', 'PENDING', 'PUBLISHED', 'UNPUBLISHED', 'ARCHIVED', 'REFUSED', 'POCKET'];

        $propertyStatusReport = Property::with('agent')
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
            ->when($startDate && $endDate && ! $params['exempt_listing_date_filter'], function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->whereNotIn('agent_id', $this->filters->getExcludedUsers())
            ->select(
                'agent_id',
                'publish_status',
                DB::raw("COUNT(*) as property_count") // Count properties per publish status
            )
            ->when($params['filter_by_users'], function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })
            ->groupBy('agent_id', 'publish_status')
            ->get();

        $reportData = [];

        foreach ($propertyStatusReport as $property) {
            $agentName = $property?->agent?->full_name ?? '-';
            $status    = $property->publish_status;

            if (! isset($reportData[$agentName])) {
                $reportData[$agentName] = [
                    'agent_name' => $agentName,
                    'manager'    => $property?->agent?->parent?->name ?? '',
                    'group'      => $property?->agent?->parent?->group?->name ?? '',
                    'image'      => $property?->agent?->thumb_url ?? '',
                    'statuses'   => [],
                ];

                // Initialize all statuses with 0 count
                foreach ($allStatuses as $s) {
                    $reportData[$agentName]['statuses'][$s] = [
                        'name'  => $s,
                        'count' => 0,
                    ];
                }
            }

            // Update the count for the specific status
            $reportData[$agentName]['statuses'][$status]['count'] = $property->property_count;
        }

        //Convert statuses from associative array to indexed array
        $finalReport = array_values(array_map(function ($agent) {
            $agent['statuses'] = array_values($agent['statuses']); // Convert to array
            return $agent;
        }, $reportData));

        return $finalReport;
    }
}
