<?php
namespace App\Services\AgentDashboard;

use App\Models\Lead;
use App\Models\LeadStatus;
use App\Models\Medium;
use App\Models\Portal;
use App\Models\PortalMeta;
use App\Models\Property;
use App\Models\Source;
use App\Models\User;
use App\Models\UserThirdPartyData;
use App\Services\Dashboard\DashboardReportFilterService;
use App\Traits\PropertyFinderTrait;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class AgentListingReportService
{
    use PropertyFinderTrait;
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



        $data = [
            'agent_listings_by_statuses' => $this->getAgentListingCountByStatuses($request, $params),
            'listing_counts_by_publish_status' => $this->getListingCountsByPublishStatus($params),
            'listing_counts_by_category' => $this->getListingCountsByCategory($params),
        ];
        return $data;
    }

    public function bayutSummary($params)
    {
        $baut_data = UserThirdPartyData::whereIn('user_id', $params['user_ids'])
            ->where('portal', 'bayut')
            ->where('start_date', '>=', $params['start_date'])
            ->where('end_date', '<=', $params['end_date'])
            ->orderByDesc('portal_data_updated_at')
            ->first();
        $bayutLatestUsefulData = json_decode($baut_data->useful_data ?? null, true);


        return [
            "overview" => [
                "quality_score" => "0",
            ],
            "counter" => [
                "active_listings" => $bayutLatestUsefulData['total_active_listings'] ?? 0,
                "active_rental_listings" => 0,
                "active_sale_listings" => 0,
                "agents_total" => 0,
                "agents_truBroker" => 0,
                "basic" => $bayutLatestUsefulData['active_basic_listings'] ?? 0,
                "hot" => $bayutLatestUsefulData['active_hot_listings'] ?? 0,
                "signature" => $bayutLatestUsefulData['active_signature_listings'] ?? 0,
                "listing_checked" => $bayutLatestUsefulData['active_checked_listings'] ?? 0,
                "listing_other" => 0,
                "listing_truChecked" => $bayutLatestUsefulData['active_trucheck_listings'] ?? 0,
                "listings_live" => $bayutLatestUsefulData['total_active_listings'] ?? 0,
                "last_updated_at" => $baut_data?->updated_at?->format('Y-m-d H:i A') ?? '',
            ],
        ];
    }

    public function dubizzleSummary($params) : array
    {
        $portal = Portal::where('name', 'Bayut')->first();
        $dubizzleLatestData = UserThirdPartyData::where('user_id', $params['user_id'])
            ->where('portal', 'dubizzle')
            ->where('start_date', '>=', $params['start_date'])
            ->where('end_date', '<=', $params['end_date'])
            ->orderByDesc('portal_data_updated_at')->first();
        $dubizzleLatestUsefulData = json_decode($dubizzleLatestData->useful_data ?? null, true);
      $live_listings = $dubizzleLatestUsefulData['num_live_listings'] ?? 0;
      $verified_listings = $dubizzleLatestUsefulData['verifiedListingsCount'] ?? 0;
      $unverified_listings = $live_listings - $verified_listings;
        return [
            "overview" => [
                "quality_score" => "0.0",
            ],
            "counter" => [
                "active_listings" => $live_listings ?? 0,
                "active_rental_listings" => 0,
                "active_sale_listings" => 0,
                "agents_total" => 0,
                "featured" => (int)($dubizzleLatestUsefulData['quota_featured'] ?? 0),
                "premium" => (int)($dubizzleLatestUsefulData['num_live_listings_featured'] ?? 0),
                "standard" => (int)($dubizzleLatestUsefulData['num_live_listings_standard'] ?? 0),
                "listing_unverified" => (int)$unverified_listings,
                "listing_verified" => (int)($dubizzleLatestUsefulData['verifiedListingsCount'] ?? 0),
                "listings_live" => (int)($dubizzleLatestUsefulData['num_live_listings'] ?? 0),
                "last_updated_at" => $dubizzleLatestData?->portal_data_updated_at
                    ? date('Y-m-d H:i A', strtotime($dubizzleLatestData->portal_data_updated_at))
                    : '',
            ],
        ];

    }public function getAgentListingCountByStatuses($request, $params)
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
    public function getListingCountsByPublishStatus($params): array
    {
        $publishedCount = Property::where('is_live', '1')
            ->when($params['filter_by_users'], function ($q) use ($params) {
                $q->whereIn("agent_id", $params['user_ids']);
            })
//            ->when($params['start_date'] && $params['end_date'] && !$params['exempt_listing_date_filter'], function ($q) use ($params) {
//                $q->whereBetween('created_at', [$params['start_date'], $params['end_date']]);
//            })
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
//            ->when($params['start_date'] && $params['end_date'] && !$params['exempt_listing_date_filter'], function ($q) use ($params) {
//                $q->whereBetween('created_at', [$params['start_date'], $params['end_date']]);
//            })
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
//            ->when($params['start_date'] && $params['end_date'] && ! $params['exempt_listing_date_filter'], function ($q) use ($params) {
//                $q->whereBetween('created_at', [$params['start_date'], $params['end_date']]);
//            })
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
//            ->when($params['start_date'] && $params['end_date'] && !$params['exempt_listing_date_filter'], function ($q) use ($params) {
//                $q->whereBetween('created_at', [$params['start_date'], $params['end_date']]);
//            })
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
//            ->when($params['start_date'] && $params['end_date'] && !$params['exempt_listing_date_filter'], function ($q) use ($params) {
//                $q->whereBetween('created_at', [$params['start_date'], $params['end_date']]);
//            })
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



}
