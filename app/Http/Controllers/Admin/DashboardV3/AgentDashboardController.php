<?php

namespace App\Http\Controllers\Admin\DashboardV3;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Portal;
use App\Models\Property;
use App\Models\User;
use App\Services\Dashboard\ActivityReportService;
use App\Services\AgentDashboard\AgentDashboardService;
use App\Services\AgentDashboard\AgentLeadReportService;
use App\Services\AgentDashboard\AgentListingReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AgentDashboardController extends Controller
{

    protected $agentDashboardService;
    protected $leadService;

    protected $listingService;

    public function __construct(
        AgentDashboardService $agentDashboardService,
        AgentLeadReportService $leadService,
        AgentListingReportService $listingService

    ) {
        $this->agentService       = $agentDashboardService;
        $this->leadService        = $leadService;
        $this->listingService     = $listingService;
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

    public function landingReq(Request $request)
    {
        //first we prepare the initial parameters for the request then will pass it to the specific functions
        $notAdmin            = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent", "Primary Agent"]);
        $date                = $request->date;

        $show_inactive_users = $request->boolean('inactiveUsers');
            $user_ids = [auth()->user()->id];

        $params = [
            'tab'                        => $request->tab,
            'date'                       => $date,
            'start_date'                 => $request->start_date != null ? Carbon::parse($request->start_date)->format('Y-m-d') : null,
            'end_date'                   => $request->end_date != null ? Carbon::parse($request->end_date)->format('Y-m-d') : null,
            'filter_by_users'            => true,
            'group'                       => 'secondary',
            'show_inactive_users'        => $show_inactive_users,
            'show_archived_leads'        => $request->boolean('archivedLeads'),
            'exempt_listing_date_filter' => $request->boolean('exceptListingDateFilter'),
            'user_ids'                   => $user_ids,
            'user_id'                    => auth()->user()->id,
            'show_deleted_leads'         => $request->boolean('deletedLeads'),
        ];


        $data = [];
        //-----=------=-------------// now we start the data collection  //-----=------=-------------//
        if ($params['tab'] == 'overall') {
        $data = [
            'overall' => [
                'top_10_listing_agents' =>[],
                'listing_counts_by_status' => [],
            ],
            'property_finder' => [
                'overview' => [
                    'quality_score' => "0",
                ],
                'counter' => [
                    'live_listings' =>0,
                    'standard' => 0,
                    'featured' => 0,
                    'premium' => 0,
                    'verified' => 0,
                    'unverified' => 0,
                    'total_agents' => 0,
                    'super_agents' => 0,
                    'non_super_agents' => 0,
                    'last_updated_at' => "2025-03-26 11:02 AM",
                ],
            ],
            "bayut" =>$this->listingService->bayutSummary($params),
            "dubizzle" => $this->listingService->dubizzleSummary($params),
            'summary' => [
                'listings' => $this->agentService->getListingData($params),
                'leads' => $this->agentService->getLeadsData($params),
                'deals' => $this->agentService->getDealSummary($params),
            ],
            'logged_user' => User::find(auth()->user()->id),
        ];

        } else if ($params['tab'] == 'lead-insight') {
            $data = $this->leadService->summary($request, $params);
        }else if ($params['tab'] == 'listing-insight') {
            $data = $this->listingService->summary($request, $params);
        }





        return response()->json([
            'status' => true,
            'data'   => $data,
        ]);
    }

}
