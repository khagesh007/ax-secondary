<?php

namespace App\Http\Controllers\Admin\DashboardV3;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Portal;
use App\Models\Property;
use App\Models\User;
use App\Services\Dashboard\AgentReportService;
use App\Services\Dashboard\BayutReportService;
use App\Services\Dashboard\CallReportService;
use App\Services\Dashboard\DubizzleReportService;
use App\Services\Dashboard\LeadReportService;
use App\Services\Dashboard\ListingReportService;
use App\Services\Dashboard\PerformanceReportService;
use App\Services\Dashboard\PfReportService;
use App\Services\Dashboard\ActivityReportService;
use App\Services\Dashboard\LeadByListingReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DashboardReportController extends Controller
{

    protected $bayutService;
    protected $pfService;
    protected $dubizzleService;
    protected $leadReportService;
    protected $listingReportService;
    protected $agentReportService;
    protected $performanceReportService;
    protected $listingService;
    protected $callService;
    protected $leadService;
    protected $agentService;
    protected $performanceService;
    protected $callReportService;
    protected $activityReportService;
    protected $leadByListingReportService;

    public function __construct(
        BayutReportService $bayutService,
        PfReportService $pfService,
        DubizzleReportService $dubizzleService,
        LeadReportService $leadReportService,
        ListingReportService $listingReportService,
        AgentReportService $agentReportService,
        PerformanceReportService $performanceReportService,
        CallReportService $callReportService,
        ActivityReportService $activityReportService,
        LeadByListingReportService $leadByListingReportService
    ) {
        $this->bayutService       = $bayutService;
        $this->pfService          = $pfService;
        $this->dubizzleService    = $dubizzleService;
        $this->leadService        = $leadReportService;
        $this->listingService     = $listingReportService;
        $this->agentService       = $agentReportService;
        $this->performanceService = $performanceReportService;
        $this->callService        = $callReportService;
        $this->activityService    = $activityReportService;
        $this->leadByListingReportService = $leadByListingReportService;
    }

    public function getTeamUser($parent_id, $show_inactive_users): array
    {
        return User::whereHas("roles", fn($q) => $q->whereIn("name", ["Agent"]))
            ->where("parent_id", $parent_id)
            ->when(!$show_inactive_users, function ($q) {
                $q->where('status', true);
            })->pluck('id')->toArray();
    }
    public function getChildrenIds($notAdmin, $parent_id = null): array
    {
        if ($parent_id != null) {
            return getUserChildrenIds($parent_id);
        } else {
            if ($notAdmin) {
                return getUserChildrenIds(auth()->user()->id);
            } else {
                return [];
            }
        }
    }

    public function initialReqPoint(Request $request)
    {
        try{
            //first we prepare the initial parameters for the request then will pass it to the specific functions
            $notAdmin            = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent", "Primary Agent"]);
            $date                = $request->date;
            $filter_by_users     = false;
            $show_inactive_users = $request->boolean('inactiveUsers');
            if (isset($request->group) && $request->group != 'secondary') {
                $group          = 'group';
                $selected_group = Group::where('name', $request->group)->with('user')->first();
                if ($selected_group == null) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Group not found',
                    ]);
                }
                $user_ids        = $this->getTeamUser($selected_group->user->id, $show_inactive_users);
                $group_id        = $selected_group->id;
                $filter_by_users = true;
            } else if (isset($request->group) && ($request->group === 'secondary' || $request->group == '')) {
                $group    = 'secondary';
                $group_id = null;
                $user_ids = $this->getChildrenIds($notAdmin);
            } else {
                $group    = 'secondary';
                $group_id = null;
                $user_ids = $this->getChildrenIds($notAdmin);
            }

            $params = [
                'tab'                        => $request->tab,
                'date'                       => $date,
                'start_date'                 => $request->start_date != null ? Carbon::parse($request->start_date)->format('Y-m-d') : null,
                'end_date'                   => $request->end_date != null ? Carbon::parse($request->end_date)->format('Y-m-d') : null,
                'group'                      => $group,
                'group_id'                   => $group_id,
                'isSuperAdmin'               => !$notAdmin,
                'filter_by_users'            => $filter_by_users,
                'show_inactive_users'        => $show_inactive_users,
                'show_archived_leads'        => $request->boolean('archivedLeads'),
                'exempt_listing_date_filter' => $request->boolean('exceptListingDateFilter'),
                'user_ids'                   => $user_ids,
                'show_deleted_leads'         => $request->boolean('deletedLeads'),
            ];
            if ($params['isSuperAdmin'] && $params['group'] != 'all') {
                $agents = User::where('status', true)->whereIn('id', $params['user_ids'])->whereHas("roles", fn($q) => $q->whereIn("name", ["Agent"]))->pluck('name', 'id');
            } else if ($params['isSuperAdmin'] && $params['group'] == 'all') {
                $agents             = User::where('status', true)->whereHas("roles", fn($q) => $q->whereIn("name", ["Agent"]))->pluck('name', 'id');
                $user_ids           = $agents->keys()->toArray();
                $params['user_ids'] = $user_ids;
            } else {
                $agents = [];
            }

            $portal_pf       = Portal::where('name', 'Property Finder')->first();
            $portal_bayut    = Portal::where('name', 'Bayut')->first();
            $portal_dubizzle = Portal::where('name', 'Dubizzle')->first();

            $data = [];
            //-----=------=-------------// now we start the data collection  //-----=------=-------------//
            if ($params['tab'] == 'overall') {
                $data = [


                    'overall'         => [
                        'top_10_listing_agents'    => $this->listingService->getTop10Lister($params, $agents),
                        'listing_counts_by_status' => $this->listingService->getListingCountsByStatus($params),
                    ],
                    'property_finder' => [
                        'overview' => [
                            'quality_score' => $this->pfService->getPfListingQualityScore($portal_pf, $params),
                        ],
                        'counter'  => $this->pfService->getDataCounts($portal_pf, $params),
                    ],
                    'bayut'           => [
                        'overview' => [
                            'quality_score' => $this->bayutService->getBayutListingQualityScore($portal_bayut, $params),
                        ],
                        'counter'  => $this->bayutService->getDataCountsBayut($portal_bayut, $params),
                    ],
                    'dubizzle'        => [
                        'overview' => [
                            'quality_score' => $this->dubizzleService->getDubizzleListingQualityScore($portal_dubizzle, $params),
                        ],
                        'counter'  => $this->dubizzleService->getDataCountsDubizzle($portal_dubizzle, $params),
                    ],
                ];

                $data['summary'] = [
                    'listings' => $this->getTotalListingCountSummary($params),
                    'leads' => $this->leadService->getTotalLeadsCountSummary($params),
                    'deals' => $this->getTotalDealsCountSummary($params),
                ];
            } else if ($params['tab'] == 'property-finder') {
                $data = $this->pfService->summary($request, $params);
            } else if ($params['tab'] == 'bayut') {
                $data = $this->bayutService->summary($request, $params);
            } else if ($params['tab'] == 'dubizzle') {
                $data = $this->dubizzleService->summary($request, $params);
            } else if ($params['tab'] == 'lead-insight') {
                $data = $this->leadService->summary($request, $params);
            } else if ($params['tab'] == 'listing-insight') {
                $data = $this->listingService->summary($request, $params);
            } else if ($params['tab'] == 'agent-insight') {
                $data = $this->agentService->summary($request, $params);
            } else if ($params['tab'] == 'performance-insight') {
                $data = $this->performanceService->summary($request, $params);
            } else if ($params['tab'] == 'call-insight') {
                $data = $this->callService->summary($request, $params);
            }else if ($params['tab'] == 'activity-insight') {
                $data = $this->activityService->summary($request, $params);
            }else if ($params['tab'] == 'lead-by-listing') {
                $data = $this->leadByListingReportService->summary($request, $params);
            }
            return response()->json([
                'status' => true,
                'data'   => $data,
            ]);
        }catch (\Exception $e){

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);
        }
    }
    public function runCommand(Request $request)
    {
        try {
            $flag = $request->flag;
            if ($flag == 'bayut-listings') {
                \Artisan::call('update:bayutListingInsights');
            } else if ($flag == 'dubizzle-listings') {
                \Artisan::call('update:bayutListingInsights');
            } else if ($flag == 'pf-listings') {
                \Artisan::call('update:pfInsight');
            }
            $result = [
                'status'  => true,
                'message' => 'Command executed successfully',
            ];
        }catch (\Exception $e){
           $result = [
                'status'  => false,
                'message' =>'Something went wrong!',
               'error' => $e->getMessage()
            ];
        }
        return response()->json($result);

    }

    public function getDepartmentList() : \Illuminate\Http\JsonResponse
    {
        $isSuperAdmin = auth()->user()->hasRole(['Super Admin','Admin','Head of Secondary']);

        $groups = null;
        $data = [];
        if (!$isSuperAdmin) {
            $groups = Group::where('id',auth()->user()->group_id)->pluck('name')->toArray();
        }else{
            $data[] = [
                'name' => 'Secondary',
                'value' => 'secondary',
            ];
            $groups = Group::pluck('name')->toArray();
        }

        foreach ($groups as $key => $group) {
            $data[] = [

                'name' => 'Secondary '.$group,
                'value' => $group
            ];
        }
        return response()->json([
            'status' => true,
            'data'   => $data,
            "is_agent" => auth()->user()->hasRole(["Agent"]),
        ]);
    }

    public function getTotalListingCountSummary($params)
    {

        $total_listings = Property::where('is_live', '1')->where('region_id', 1)
            ->when($params['filter_by_users'], fn($query) => $query->whereIn("agent_id", $params['user_ids']))
            ->whereHas('agent', function ($q) {
                $q->where('is_commercial', false);
            })
            ->count();
        $total_property_finder = Property::where('is_live', 1)
            ->where('region_id', 1)
            ->whereHas('portals', function ($query) {
                $query->where('portal_id', 1);
            })
            ->when($params['filter_by_users'], fn($query) => $query->whereIn("agent_id", $params['user_ids']))
            ->whereHas('agent', function ($q) {
                $q->where('is_commercial', false);
            })
            ->count();
        $total_bayut = Property::where('is_live', 1)
            ->where('region_id', 1)
            ->when($params['filter_by_users'], fn($query) => $query->whereIn("agent_id", $params['user_ids']))
            ->whereHas('portals', function ($query) {
                $query->where('portal_id', 2);
            })
            ->whereHas('agent', function ($q) {
                $q->where('is_commercial', false);
            })
            ->count();
        $total_dubizzle = Property::where('is_live', 1)
            ->where('region_id', 1)
            ->when($params['filter_by_users'], fn($query) => $query->whereIn("agent_id", $params['user_ids']))
            ->whereHas('portals', function ($query) {
                $query->where('portal_id', '=', 3);
            })
            ->whereHas('agent', function ($q) {
                $q->where('is_commercial', false);
            })
            ->count();
        $total_other = Property::where('is_live', 1)
            ->where('region_id', 1)
            ->when($params['filter_by_users'], fn($query) => $query->whereIn("agent_id", $params['user_ids']))
            ->whereHas('portals', function ($query) {
                $query->whereNotIn('portal_id', [1, 2, 3]);
            })
            ->whereHas('agent', function ($q) {
                $q->where('is_commercial', false);
            })
            ->count();

        return [
            'total_listings' => $total_listings,
            'total_property_finder' => $total_property_finder,
            'total_bayut' => $total_bayut,
            'total_dubizzle' => $total_dubizzle,
            'total_other'   =>  $total_other,
        ];
    }



    public function getTotalDealsCountSummary($params)
    {


        $start_date = $params['start_date'];
        $end_date = $params['end_date'];
        $group = $params['group_id'];
        $group = Group::find($group);

        if ($group) {
            $result = Http::withHeaders([
                'token' => env('AXDEAL_SECRET_KEY'),
            ])->get('https://deals.axcrm.ae/backend/api/get-deals-sales-value?start_date=' . $start_date . '&end_date=' . $end_date . '&group=' . $group->name);
        } else {
            $result = Http::withHeaders([
                'token' => env('AXDEAL_SECRET_KEY'),
            ])->get('https://deals.axcrm.ae/backend/api/get-deals-sales-value?start_date=' . $start_date . '&end_date=' . $end_date);
        }

        $data = $result->json();

        $total_sale_value = $data['total_sale_value'];
        $total_secondary_sales_sale_value = $data['secondary_sales_value'];
        $total_primary_sale_value = $data['primary_sales_value'];
        $total_rental_sale_value = $data['rental_sales_value'];
        $total_other_deals_value = $data['other_sales_value'];

        return [
            'total_sale_value' => $total_sale_value,
            'total_secondary_sales_sale_value' => $total_secondary_sales_sale_value,
            'total_primary_sale_value' => $total_primary_sale_value,
            'total_rental_sale_value' => $total_rental_sale_value,
            'total_other_deals_value'   =>  $total_other_deals_value,
        ];
    }

    public function numberFormatMillisons($number)
    {
        if ($number > 1000000000) {
            return round($number / 1000000000, 2) . 'B';
        } else if ($number > 1000000) {
            return round($number / 1000000, 2) . 'M';
        } else if ($number > 1000) {
            return round($number / 1000, 2) . 'K';
        } else {
            return $number;
        }
    }






    public function leadInsight(Request $request)
    {

        return response()->json([
            'status' => true,
            'data'   => [],
        ]);
    }

    public function propertyFinderData(Request $request)
    {
        $notAdmin    = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent", "Primary Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->user()->id);
        }
        $portal = Portal::where('name', 'Property Finder')->first();

        $data['pf'] = Property::whereNot('completion_status', 'Off-Plan Primary')
            ->where('region_id', 1)
            ->whereHas('portals', function ($q) use ($portal) {
                $q->where('portal_id', $portal->id);
            })->where('is_live', '1')
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->count();
    }
}
