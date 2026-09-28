<?php

namespace App\Services\Kpi;

use App\Http\Resources\Kpi\KpiUserTargetResource;
use App\Models\BayutCreditHistory;
use App\Models\Group;
use App\Models\KpiOtherReport;
use App\Models\KpiTarget;
use App\Models\KpiTeam;
use App\Models\KpiTeamData;
use App\Models\PfCreditHistory;
use App\Models\Property;
use App\Models\User;
use App\Models\UserThirdPartyData;
use App\Services\Dashboard\DashboardReportFilterService;
use Carbon\Carbon;

class KpiTeamService
{

   // protected $filters;
//DashboardReportFilterService $filters
    public function __construct()
    {
        //$this->filters = $filters;
    }

    public function getTeamOverview($params){

        $start_date = $params['start_date'];
        $end_date   = $params['end_date'];
        if($params['is_super_admin']) {
            $groups = Group::with(["user"])->get();
        }else{
            $groups = Group::where('id',auth()->user()->group_id)->get();
        }
        $sales_target = 0; $sales_achieved = 0; $sales_percentage = 0;
        $team_users_target = null; $team_users_achieved = null; $team_users_percentage = null;
        $team_live_listing_target = 0;
        $team_live_listing_achieved = 0;
        $team_new_listing_target = 0;
        $team_new_listing_achieved = 0;
        $team_tru_broker_target = 0;
        $team_tru_broker_achieved = 0;
        $team_super_agent_target = 0;
        $team_super_agent_achieved = 0;
        $team_tru_checked_listings_target = 0;
        $team_tru_checked_listings_achieved = 0;
        $team_pipelines_target = 0;
        $team_pipelines_achieved = 0;
        foreach ($groups as $group){
            $kpiTeams = KpiTeam::where('group_id', $group->id)->where('kpi','sales_value')->where('start_date', '>=', $start_date)->where('end_date', '<=', $end_date)->with('result')->first();
            $sales_target+= $kpiTeams?->target ?? 0;
            $sales_achieved+= $kpiTeams?->result?->achieved ?? 0;
            $teamPipelineTargets = KpiTeam::where('group_id', $group->id)->where('kpi','pipelines')->where('start_date', '>=', $start_date)->where('end_date', '<=', $end_date)->with('result')->first();
            $team_pipelines_target+= $teamPipelineTargets?->target ?? 0;
            $team_pipelines_achieved+= $teamPipelineTargets?->result?->achieved ?? 0;

            $user = $group->user;
            $agents = User::whereHas("roles", fn($q) => $q->whereIn("name", ["Agent"]))
                ->where("parent_id", $user->id)->where('status', true)->get();
            $total_target = 0; $total_achieved = 0;

            $team_users_target = $this->processTeamUserData($group,$agents,$start_date,$end_date);

            $team_live_listing_target+= $team_users_target['live_listings']['total_target'];
            $team_live_listing_achieved+= $team_users_target['live_listings']['total_achieved'];
            $team_new_listing_target+= $team_users_target['new_listings']['total_target'];
            $team_new_listing_achieved+= $team_users_target['new_listings']['total_achieved'];
            $team_tru_broker_target+= (int)$team_users_target['tru_broker']['total_target'];
            $team_tru_broker_achieved+= $team_users_target['tru_broker']['total_achieved'];
            $team_super_agent_target+= (int)$team_users_target['super_agent']['total_target'];
            $team_super_agent_achieved+= $team_users_target['super_agent']['total_achieved'];
            $team_tru_checked_listings_target+= $team_users_target['tru_checked_listings']['total_target'];
            $team_tru_checked_listings_achieved+= $team_users_target['tru_checked_listings']['total_achieved'];

        }
        $data = [];
        $data['sales_value']['target'] = $sales_target;
        $data['sales_value']['target_text'] = formatNumberMetricPrefixes($sales_target);
        $data['sales_value']['achieved'] = $sales_achieved;
        $data['sales_value']['achieved_text'] = formatNumberMetricPrefixes($sales_achieved);
        $data['sales_value']['percentage'] = $sales_target > 0 ? round(($sales_achieved / $sales_target) * 100, 2) : 0;
        $data['sales_value']['kpi_title'] = 'Sales Target';
        $data['sales_value']['kpi'] = 'sales_value';
        $data['live_listings']['target'] = $team_live_listing_target;
        $data['live_listings']['target_text'] = $team_live_listing_target;
        $data['live_listings']['achieved'] = $team_live_listing_achieved;
        $data['live_listings']['achieved_text'] = $team_live_listing_achieved;
        $data['live_listings']['percentage'] = $team_live_listing_target > 0 ? round(($team_live_listing_achieved / $team_live_listing_target) * 100, 2) : 0;
        $data['live_listings']['kpi_title'] = 'Live Listings';
        $data['live_listings']['kpi'] = 'live_listings';
        $data['new_listings']['target'] = $team_new_listing_target;
        $data['new_listings']['target_text'] = $team_new_listing_target;
        $data['new_listings']['achieved'] = $team_new_listing_achieved;
        $data['new_listings']['achieved_text'] = $team_new_listing_achieved;
        $data['new_listings']['percentage'] = $team_new_listing_target > 0 ? round(($team_new_listing_achieved / $team_new_listing_target) * 100, 2) : 0;
        $data['new_listings']['kpi_title'] = 'New Listings';
        $data['new_listings']['kpi'] = 'new_listings';

        $data['tru_checked_listings']['target'] = $team_tru_checked_listings_target;
        $data['tru_checked_listings']['target_text'] = $team_tru_checked_listings_target;
        $data['tru_checked_listings']['achieved'] = $team_tru_checked_listings_achieved;
        $data['tru_checked_listings']['achieved_text'] = $team_tru_checked_listings_achieved;
        $data['tru_checked_listings']['percentage'] = $team_tru_checked_listings_target > 0 ? round(($team_tru_checked_listings_achieved / $team_tru_checked_listings_target) * 100, 2) : 0;
        $data['tru_checked_listings']['kpi_title'] = 'TruChecked Listings';
        $data['tru_checked_listings']['kpi'] = 'tru_checked_listings';

        $data['tru_broker']['target'] = $team_tru_broker_target;
        $data['tru_broker']['target_text'] = $team_tru_broker_target;
        $data['tru_broker']['achieved'] = $team_tru_broker_achieved;
        $data['tru_broker']['achieved_text'] = $team_tru_broker_achieved;
        $data['tru_broker']['percentage'] = $team_tru_broker_target > 0 ? round(($team_tru_broker_achieved / $team_tru_broker_target) * 100, 2) : 0;
        $data['tru_broker']['kpi_title'] = 'TruBroker';
        $data['tru_broker']['kpi'] = 'truBroker';
        $data['super_agent']['target'] = $team_super_agent_target;
        $data['super_agent']['target_text'] = $team_super_agent_target;
        $data['super_agent']['achieved'] = $team_super_agent_achieved;
        $data['super_agent']['achieved_text'] = $team_super_agent_achieved;
        $data['super_agent']['percentage'] = $team_super_agent_target > 0 ? round(($team_super_agent_achieved / $team_super_agent_target) * 100, 2) : 0;
        $data['super_agent']['kpi_title'] = 'Super Agent';
        $data['super_agent']['kpi'] = 'superAgent';
        $data['pipelines']['target'] = $team_pipelines_target;
        $data['pipelines']['target_text'] = $team_pipelines_target;
        $data['pipelines']['achieved'] = $team_pipelines_achieved;
        $data['pipelines']['achieved_text'] = $team_pipelines_achieved;
        $data['pipelines']['percentage'] = $team_pipelines_target > 0 ? round(($team_pipelines_achieved / $team_pipelines_target) * 100, 2) : 0;
        $data['pipelines']['kpi_title'] = 'Pipelines';
        $data['pipelines']['kpi'] = 'pipelines';

        $is_sales_director = auth()->user()->hasRole('Sales Director');
        $permissions = [
            'refresh_teams' => true,
            'setup_team_kpi' => (bool)$params['is_super_admin'],
            'refresh_agents' => true,
            'setup_agent_kpi' => (bool)$is_sales_director || (bool)$params['is_super_admin'],
        ];

        $result = [
            'status' => true,
            'data' => $data,
            'permissions' => $permissions,
        ];
        return response()->json($result);
    }

    private function processTeamUserData($group,$agents,$start_date,$end_date){
        $team_user_totals = null;
        $kpis = ['sales_target','live_listings','new_listings'];

        $currentMonthStartDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $currentMonthEndDate = Carbon::now()->endOfMonth()->format('Y-m-d');


        $team_user_totals = [
            'sales_target' => ['total_target' => 0, 'total_achieved' => 0],
            'live_listings' => ['total_target' => 0, 'total_achieved' => 0],
            'new_listings' => ['total_target' => 0, 'total_achieved' => 0],
            'tru_checked_listings' => ['total_target' => 0, 'total_achieved' => 0],
            'tru_broker' => ['total_target' => $agents->count(), 'total_achieved' => 0],
            'super_agent' => ['total_target' => $agents->count(), 'total_achieved' => 0],
            'pipelines' => ['total_target' => 0, 'total_achieved' => 0],
        ];
        $total_tru_broker = 0;
        $total_super_agent = 0;
        $total_truChecked_listings = 0;
        $total_active_listing = 0;
        foreach($agents as $agent) {
            $targets = KpiTarget::where('agent_id', $agent->id)->where("start_date", ">=", $start_date)
                ->where("end_date", "<=", $end_date)->with('result')->get();

            foreach ($targets as $target) {
                $team_user_totals[$target->kpi]['total_target']+= (int)$target->result?->target ?? 0;
                $team_user_totals[$target->kpi]['total_achieved']+= (float)$target->result->achieved ?? 0;
            }

            $bayutLatestUsefulData = json_decode(UserThirdPartyData::where('user_id', $agent->id)
                ->where('portal', 'bayut')
                ->where('start_date', '>=', $currentMonthStartDate)
                ->where('end_date', '<=', $currentMonthEndDate)
                ->orderByDesc('portal_data_updated_at')
                ->first()->useful_data ?? null, true);

            $total_tru_broker+= ($bayutLatestUsefulData['trubroker'] ?? "") == "Active" ? 1 : 0;
            $total_truChecked_listings+= $bayutLatestUsefulData['active_trucheck_listings'] ?? 0;
            $total_active_listing+= $bayutLatestUsefulData['total_active_listings'] ?? 0;

            $pfUsefulData = json_decode(UserThirdPartyData::where('user_id', $agent->id)
                ->where('portal', 'property_finder')
                ->where('end_date', '<=', $currentMonthEndDate)
                ->orderByDesc('portal_data_updated_at')
                ->first()->useful_data ?? null, true);

            $total_super_agent+= ($pfUsefulData['is_super_agent'] ?? "") ? 1 : 0;
        }
        $team_user_totals['tru_broker']['total_achieved'] = $total_tru_broker;
        $team_user_totals['super_agent']['total_achieved'] = $total_super_agent;
        $team_user_totals['tru_checked_listings']['total_target'] = $total_active_listing;
        $team_user_totals['tru_checked_listings']['total_achieved'] = $total_truChecked_listings;

        return $team_user_totals;
    }


    public function getTeamSummary($params)
    {
        if($params['is_super_admin']) {
            $groups = Group::with(["user"])->get();
        }else{
            $groups = Group::where('id',auth()->user()->group_id)->get();
        }
        $start_date = $params['start_date'];
        $end_date   = $params['end_date'];
        $data = [];
        foreach ($groups as $group){
            $data[$group->name]['user'] = [
                'id' => $group->user->id,
                'name' => $group->user->name,
                'image' => $group->user->thumb_url,
            ];
           $avg_gross_commission = KpiTeamData::where('group_id', $group->id)->where('kpi','avg_gross_commission')->where('start_date', '>=', $start_date)->where('end_date', '<=', $end_date)->first();
           $gross_commission_amount = KpiTeamData::where('group_id', $group->id)->where('kpi','gross_commission_amount')->where('start_date', '>=', $start_date)->where('end_date', '<=', $end_date)->first();
           $avg_gross_total = $avg_gross_commission?->total ?? 0;
            $data[$group->name]['reports'] = [
                'Avg. Gross Commission' => $avg_gross_total ? round($avg_gross_total,2).'%' : 0,
                'Gross Commission Amount' => $gross_commission_amount?->total ? formatNumberMetricPrefixes($gross_commission_amount?->total) : 0,
            ];
            $data[$group->name]['group'] = [
                'id' => $group->id,
                'name' => $group->name,
            ];
            $data[$group->name]['team_targets'] = $this->processTeamTargetData($group,$start_date,$end_date);
            $data[$group->name]['targets'] = $this->processMemberTargetData($group,$start_date,$end_date,$params);
            $data[$group->name]['credit_targets'] = $this->processTeamCreditTargetData($group, $params['month'] != date('Y-m') ? $params['end_date'] : date('Y-m-d'));;
            $data[$group->name]['permissions'] = [
                'view' => false,
                'edit' => (bool)$params['is_super_admin'],
                'team_kpi_report' => false,
                'view_url' => route('reports.team.kpi-v2.get.report.page', ['id' => $group->id])."?month=".$params['month'],
            ];
        }

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }

    private function processTeamTargetData($group,$start_date,$end_date)
    {
        $team_summary = null;
        $kpis = ['sales_value','pipelines'];
        $kpi_titles = [
            'sales_value' => 'Sales Value',
            'pipelines' => 'Pipelines',
        ];
        $team_summary = [];
        foreach ($kpis as $kpi){
            $kpi_for_team = KpiTeam::where('group_id',$group->id)->where('kpi',$kpi)->where('start_date', '>=', $start_date)->where('end_date', '<=', $end_date)->with('result')->first();
            if(!$kpi_for_team){
                continue;
            }
            $target = $kpi_for_team->target;
            $achieved = $kpi_for_team->result->achieved ?? 0;
            $team_summary[$kpi]['target'] = $kpi_for_team->target;
            $team_summary[$kpi]['target_text'] = formatNumberMetricPrefixes($kpi_for_team->target) ?? '';
            $team_summary[$kpi]['achieved'] = $kpi_for_team->result->achieved ?? 0;
            $team_summary[$kpi]['achieved_text'] = formatNumberMetricPrefixes($kpi_for_team->result->achieved ?? 0) ?? '';
            $team_summary[$kpi]['percentage'] = $target > 0 ? round(($achieved / $target) * 100, 2) : 0;
            $team_summary[$kpi]['kpi_title'] = $kpi_titles[$kpi];
            $team_summary[$kpi]['kpi'] = $kpi;
        }
        return $team_summary;
    }
    private function processMemberTargetData($group,$start_date,$end_date,$params)
    {
        $team_summary = null;
        $kpis = ['sales_value','live_listings','new_listings','tru_broker','super_agent'];
        $kpi_titles = [
            'sales_target' => 'Sales Target',
            'live_listings' => 'Live Listings',
            'new_listings' => 'New Listings',
            'tru_checked_listings' => 'TruChecked Listings',
            'tru_broker' => 'TruBroker',
            'super_agent' => 'Super Agent',
        ];
        $team_summary = [];
        $team_users_ids = User::whereHas("roles", fn($q) => $q->whereIn("name", ["Agent"]))
            ->where("parent_id", $group->user->id)->where('status',true)->pluck('id');

        foreach ($kpi_titles as $key => $kpi){


            if($key == 'tru_broker'){
                $target = count($team_users_ids);
                $achieved = UserThirdPartyData::whereIn('user_id',$team_users_ids)
                    ->where('portal', 'bayut')
                    ->where('start_date', '>=', $start_date)
                    ->where('end_date', '<=', $end_date)
                    ->where('useful_data->trubroker', 'Active')
                    ->count() ?? 0;
                $team_summary[$key]['target'] = $target;
                $team_summary[$key]['target_text'] = $key == 'sales_target' ? formatNumberMetricPrefixes($target) : $target;
                $team_summary[$key]['achieved'] = $achieved;
                $team_summary[$key]['achieved_text'] = $key == 'sales_target' ? formatNumberMetricPrefixes($achieved) : $achieved;
                $team_summary[$key]['percentage'] = $target > 0 ? round(($achieved / $target) * 100, 2) : 0;
                $team_summary[$key]['kpi_title'] = $kpi;
                $team_summary[$key]['kpi'] = $key;

            }else if($key == 'super_agent'){
                $target = count($team_users_ids);

                $achieved = 0;
                foreach ($team_users_ids as $user_id){
                    $pfUsefulData = json_decode(UserThirdPartyData::where('user_id', $user_id)
                        ->where('portal', 'property_finder')
                        ->whereRaw('DATE_FORMAT(end_date, "%Y-%m") = ?', [$params['month']])
                        ->orderByDesc('portal_data_updated_at')
                        ->first()->useful_data ?? null, true);
                    if($pfUsefulData !=null) {
                        if ($pfUsefulData['is_super_agent'] || $pfUsefulData['is_super_agent'] == 1) {
                            $achieved++;
                        }
                    }
                }



                $team_summary[$key]['target'] = $target;
                $team_summary[$key]['target_text'] = $key == 'sales_target' ? formatNumberMetricPrefixes($target) : $target;
                $team_summary[$key]['achieved'] = $achieved;
                $team_summary[$key]['achieved_text'] = $key == 'sales_target' ? formatNumberMetricPrefixes($achieved) : $achieved;
                $team_summary[$key]['percentage'] = $target > 0 ? round(($achieved / $target) * 100, 2) : 0;
                $team_summary[$key]['kpi_title'] = $kpi;
                $team_summary[$key]['kpi'] = $key;

            }else if($key == 'tru_checked_listings'){


                $achieved = 0; $target = 0;
                foreach ($team_users_ids as $user_id){
                    $bayutLatestUsefulData = json_decode(UserThirdPartyData::where('user_id', $user_id)
                        ->where('portal', 'bayut')
                        ->where('start_date', '>=', $params['start_date'])
                        ->where('end_date', '<=', $params['end_date'])
                        ->orderByDesc('portal_data_updated_at')
                        ->first()->useful_data ?? null, true);
                    if($bayutLatestUsefulData !=null) {
                        $target+= $bayutLatestUsefulData['total_active_listings'] ?? 0;
                        $achieved+= $bayutLatestUsefulData['active_trucheck_listings'] ?? 0;
                    }
                }



                $team_summary[$key]['target'] = $target;
                $team_summary[$key]['target_text'] = $key == 'sales_target' ? formatNumberMetricPrefixes($target) : $target;
                $team_summary[$key]['achieved'] = $achieved;
                $team_summary[$key]['achieved_text'] = $key == 'sales_target' ? formatNumberMetricPrefixes($achieved) : $achieved;
                $team_summary[$key]['percentage'] = $target > 0 ? round(($achieved / $target) * 100, 2) : 0;
                $team_summary[$key]['kpi_title'] = $kpi == 'Tru Broker' ? 'TruBroker' : $kpi;
                $team_summary[$key]['kpi'] = $key;

            }else {
//                $target  = KpiTarget::whereIn('agent_id',$team_users_ids)
//                    ->where('kpi',$key)
//                    ->where("start_date", ">=", $start_date)
//                    ->where("end_date", "<=", $end_date)->with('result')->sum('target');

                $kpiTargets = KpiTarget::whereIn('agent_id', $team_users_ids)
                    ->where('kpi',$key)
                    ->where("start_date", ">=", $start_date)
                    ->where("end_date", "<=", $end_date)
                    ->with('result')
                    ->get();

                $target = $kpiTargets->sum(function ($kpiTarget) {
                   // echo $kpiTargets?->target.'---'.$kpiTargets->manager_id.'---'. $kpiTargets->agent_id.'||||';
                  // echo gettype($kpiTarget->target).'---'. $kpiTarget->target.'('.$kpiTarget->manager_id.')||| ';
                    return $kpiTarget->target !=null ? $kpiTarget->target : 0;
                });

                $achieved = $kpiTargets->sum(function ($kpiTarget) {
                    return $kpiTarget->result ? $kpiTarget->result->achieved : 0;
                });
                $key = $key == 'sales_target' ? 'sales_value' : $key;
                $team_summary[$key]['target'] = $target;
                $team_summary[$key]['target_text'] = $key == 'sales_value' ? formatNumberMetricPrefixes($target) : $target;
                $team_summary[$key]['achieved'] = $achieved;
                $team_summary[$key]['achieved_text'] = $key == 'sales_value' ? formatNumberMetricPrefixes($achieved) : $achieved;
                $team_summary[$key]['percentage'] = $target > 0 ? round(($achieved / $target) * 100, 2) : 0;
                $team_summary[$key]['kpi_title'] = $kpi;
                $team_summary[$key]['kpi'] = $key;
            }
        }
    return $team_summary;

    }

    public function groupMemberOverview($params)
    {

        $selectedGroup = Group::find($params['group_id']);

        $user = $selectedGroup->user;

        $agents = User::whereHas("roles", fn($q) => $q->whereIn("name", ["Agent"]))
            ->where("parent_id", $user->id)->where('status', true)->select('id','name','thumbnail','pp_enabled','pp_name','pp_photo','pp_license_no')->get();

        $startDate = $params['start_date'];
        $endDate = $params['end_date'];

        $prev_month_start_date = Carbon::createFromFormat("Y-m", $params['month'])->subMonth()->startOfMonth()->format('Y-m-d');
        $prev_month_end_date = Carbon::createFromFormat("Y-m", $params['month'])->subMonth()->endOfMonth()->format('Y-m-d');

        $currentMonthStartDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $currentMonthEndDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        $data = [];

        foreach($agents as $agent){
            $targets = KpiTarget::where('agent_id', $agent->id)->where("start_date", ">=", $startDate)
                ->where("end_date", "<=", $endDate)->with('result')->get();

            $user_targets = collect($targets);
             $targets = KpiUserTargetResource::collection($targets);

             $creditData = [];

            $pf_dates = getPfCreditDurationDates($params['month'] != date('Y-m') ? $endDate : date('Y-m-d'));

            $pfCreditHistory = PfCreditHistory::where('user_id', $agent->id)
                ->where('transaction_time', '>=', $pf_dates['start_date'])
                ->where('transaction_time', '<=', $pf_dates['end_date'])
                ->latest()->first();

            if($pfCreditHistory){
                $creditData[] = [
                    'kpi_title' => 'PF Credit Balance',
                    'total' => $pfCreditHistory->balance ?? 0,
                    'icon' => 'mdi-credit-card'
                ];
            }

            $bd_dates = getBayutCreditDurationDates($params['month'] != date('Y-m') ? $endDate : date('Y-m-d'));
            $bayutCreditHistories = UserThirdPartyData::where('user_id', $agent->id)
                ->whereIn('portal', ['bayut_credit'])
                ->where("start_date", ">=", $bd_dates['start_date'])
                ->where("end_date", "<=", $bd_dates['end_date'])
                ->get();

            foreach ($bayutCreditHistories as $bayutCreditHistory){
                $creditData[] = [
                    'kpi_title' => ($bayutCreditHistory->portal == 'bayut_credit' ? 'Bayut' : 'Dubizzle'). ' Credit Consumed',
                    'total' => json_decode($bayutCreditHistory->useful_data, true)['debit'] ?? 0,
                    'icon' => 'mdi-credit-card'
                ];
            }

            $bayutLatestUsefulData = json_decode(UserThirdPartyData::where('user_id', $agent->id)
                ->where('portal', 'bayut')
                ->where('start_date', '>=', $currentMonthStartDate)
                ->where('end_date', '<=', $currentMonthEndDate)
                ->orderByDesc('portal_data_updated_at')
                ->first()->useful_data ?? null, true);

            $bayutPreviousMonthUsefulData = json_decode(UserThirdPartyData::where('user_id', $agent->id)
                ->where('portal', 'bayut')
                ->where('start_date', '>=', $prev_month_start_date)
                ->where('end_date', '<=', $prev_month_end_date)
                ->orderByDesc('portal_data_updated_at')
                ->first()->useful_data ?? null, true);


            $pfUsefulData = json_decode(UserThirdPartyData::where('user_id', $agent->id)
                ->where('portal', 'property_finder')
                ->where('end_date', '<=', $currentMonthEndDate)
                ->orderByDesc('portal_data_updated_at')
                ->first()->useful_data ?? null, true);

            $pfPreviousMonthUsefulData = json_decode(UserThirdPartyData::where('user_id', $agent->id)
                ->where('portal', 'property_finder')
                ->where('end_date', '<=', $prev_month_end_date)
                ->orderByDesc('portal_data_updated_at')
                ->first()->useful_data ?? null, true);

            $truCheckedTarget = $bayutLatestUsefulData['total_active_listings'] ?? 0;
            $truCheckedAchieved = $bayutLatestUsefulData['active_trucheck_listings'] ?? 0;
            $targets = $targets->collection->push((object)[
                'target' => $truCheckedTarget,
                'target_text' => (string)$truCheckedTarget,
                'achieved' => $truCheckedAchieved,
                'achieved_text' => (string)$truCheckedAchieved,
                'percentage' => $truCheckedTarget > 0 ? round(($truCheckedAchieved / $truCheckedTarget) * 100, 2) : 0,
                'kpi_title' => 'TruChecked Listings',
                'kpi' => 'tru_checked_listings'
            ]);


            $other_reports = KpiOtherReport::where('agent_id', $agent->id)->where("start_date", ">=", $startDate)
                ->where("end_date", "<=", $endDate)->whereNotIn('kpi_title',['Meetings','Follow-up'])->select('kpi_title','total')->get();





            $icons = [
                'Calls' => 'mdi-phone-outline',
                'Leads' => 'mdi-account-filter-outline',
                'Deals' => 'mdi-handshake-outline',
                'Viewings' => 'mdi-eye-outline'
            ];
          $otherReport = [];
           foreach ($other_reports as $report) {
               $otherReport[] = [
                     'kpi_title' => $report->kpi_title,
                     'total' => $report->total ?? 0,
                   'icon' => $icons[$report->kpi_title] ?? 'mdi-account-outline'
               ];
           }

            $pf_verified_agent = $pfUsefulData['is_verified'] ?? 0;


              $user_targets = $user_targets->where('kpi','sales_target')->first();
              $other_data = $user_targets->result->data ?? [];
            $total_gross_commission = 0; $avg_gross_commission_percentage = 0;
              if(count($other_data)>0){
                    $total_gross_commission_data = collect($other_data)->where('title','Total Gross Commission')->first();
                  $total_gross_commission = $total_gross_commission_data['count'] ?? 0;
                    $avg_gross_commission_per_data= collect($other_data)->where('title','Average Gross Commission Percentage')->first();
                    $avg_gross_commission_percentage = $avg_gross_commission_per_data['count'] ?? 0;
              }

              $previous_month_data = [];
              $prev_month_active_bayut_listing = $bayutPreviousMonthUsefulData['total_active_listings'] ?? 0;
              $previous_month_data[] = [
                  'title' => 'TruChecked Listings',
                  'target' => $bayutPreviousMonthUsefulData['total_active_listings'] ?? 0,
                  'achieved' => $bayutPreviousMonthUsefulData['active_trucheck_listings'] ?? 0,
                  'percentage' =>  $prev_month_active_bayut_listing > 0 ? round(($bayutPreviousMonthUsefulData['active_trucheck_listings'] / $prev_month_active_bayut_listing) * 100, 2) : 0,
                  'result' => $bayutPreviousMonthUsefulData['active_trucheck_listings'] ?? 0,

              ];

              $public_profile = null;
              if($agent->pp_enabled){
                    $public_profile = [
                        'name' => $agent->pp_name,
                        'image' => $agent->pp_photo,
                        'license_no' => $agent->pp_license_no,
                    ];
              }
            $can_edit_agent_kpi = (auth()->user()->hasRole('Sales Director') || auth()->user()->hasRole('Super Admin')) && $user->id == auth()->user()->id;
            $data[] = [
                'agent' => [
                    'id' => $agent->id,
                    'name' => $agent->name,
                    'image_url' => $agent->thumbnail,
                    'full_image' => $agent->image_url,
                    'role_list' => $agent->role_list,
                ],
                'public_profile' => $public_profile,
                'targets' => $targets,


                'other_reports' => $otherReport,
                'reports' =>[
                    "Avg Commission:" => $avg_gross_commission_percentage."% (".formatNumberMetricPrefixes($total_gross_commission).")",
                ],
                'previous_month_data' => $previous_month_data,
                'flags' => [
                    'pf_verified' => $pf_verified_agent,
                    'is_truBroker' => ($bayutLatestUsefulData['trubroker'] ?? "") == "Active" ? 1 : 0,
                    'is_super_agent' => ($pfUsefulData['is_super_agent'] ?? "") ? 1 : 0,
                    'prev_truBroker' => ($bayutPreviousMonthUsefulData['trubroker'] ?? "") == "Active" ? 1 : 0,
                    'prev_super_agent' => ($pfPreviousMonthUsefulData['is_super_agent'] ?? "") ? 1 : 0,
                ],
                "total_achieved" => $targets->sum('achieved'),
                'permissions' => [
                    'view' => true,
                    'edit' => (bool)$can_edit_agent_kpi,
                    'view_url' => route('reports.group.kpi-v2.view', ['agentId' => $agent->id])."?month=".$params['month'],
                ],
                'portal_target' => [],
                'credit_data' => $creditData,
            ];
        }

        $data = collect($data)->sortByDesc(function ($item) {
            return $item['total_achieved'];
        })->values()->toArray();

        $result = [
            'status' => '1',
            'data' => $data
        ];
        return response()->json($result);
    }

    public function getGroupAgentKpiReport($params){
        $selectedGroup = Group::find($params['group_id']);

        $user = $selectedGroup->user;

        $agents = User::whereHas("roles", fn($q) => $q->whereIn("name", ["Agent"]))
            ->where("parent_id", $user->id)->where('status', true)->get();

        $startDate = $params['start_date'];
        $endDate = $params['end_date'];


        $data = [];
        foreach($agents as $agent) {
            $pfUsefulData = json_decode(UserThirdPartyData::where('user_id', $agent->id)
                ->where('portal', 'property_finder')
                ->where('end_date', '<=', $endDate)
                ->orderByDesc('portal_data_updated_at')
                ->first()->useful_data ?? null, true);
            if($pfUsefulData){
            $super_agent = ($pfUsefulData['is_super_agent'] || $pfUsefulData['is_super_agent'] == 1) ? 'Yes' : 'No';
            }else{
                $super_agent = 'No';
            }
            $prev_month_start_date = Carbon::createFromFormat("Y-m", $params['month'])->subMonth()->startOfMonth()->format('Y-m-d');
            $prev_month_end_date = Carbon::createFromFormat("Y-m", $params['month'])->subMonth()->endOfMonth()->format('Y-m-d');
            $bayutPreviousMonthUsefulData = json_decode(UserThirdPartyData::where('user_id', $agent->id)
            ->where('portal', 'bayut')
            ->where('start_date', '>=', $prev_month_start_date)
            ->where('end_date', '<=', $prev_month_end_date)
            ->orderByDesc('portal_data_updated_at')
            ->first()->useful_data ?? null, true);
            $pfPreviousMonthUsefulData = json_decode(UserThirdPartyData::where('user_id', $agent->id)
            ->where('portal', 'property_finder')
            ->where('end_date', '<=', $prev_month_end_date)
            ->orderByDesc('portal_data_updated_at')
            ->first()->useful_data ?? null, true);
            $prev_super_agent = ($pfPreviousMonthUsefulData['is_super_agent'] ?? "") ? 'Yes' : 'No';
            $prev_truBroker = ($bayutPreviousMonthUsefulData['trubroker'] ?? "") == "Active" ? 'Yes' : 'No';
            $dubizzleLatestData = UserThirdPartyData::where('user_id', $agent->id)
                ->where('portal', 'dubizzle')
                ->where('start_date', '>=', $startDate)
                ->where('end_date', '<=', $endDate)
                ->orderByDesc('portal_data_updated_at')->first();
            $dubizzleLatestUsefulData = json_decode($dubizzleLatestData->useful_data ?? null, true);
            $dubizzle_premium = $dubizzleLatestUsefulData['num_live_listings_featured'] ?? 0;
            $dubizzle_featured = $dubizzleLatestUsefulData['quota_featured'] ?? 0;
            $bayutLatestData = UserThirdPartyData::where('user_id', $agent->id)->where('portal', 'bayut')
                ->where('start_date', '>=', $startDate)
                ->where('end_date', '<=', $endDate)
                ->orderByDesc('portal_data_updated_at')->first();
            $bayutLatestUsefulData = json_decode($bayutLatestData->useful_data ?? null, true);
            $bayut_checked_listings = $bayutLatestUsefulData['active_checked_listings'] ?? 0;
            $bayut_tru_checked = $bayutLatestUsefulData['active_trucheck_listings'] ?? 0;
            $bayut_total_live = $bayutLatestUsefulData['total_active_listings'] ?? 0;
            $tru_broker = $bayutLatestUsefulData['trubroker'] ?? '';
            if($bayutLatestUsefulData != null || is_array($bayutLatestUsefulData)){
                $tru_broker = ($bayutLatestUsefulData['trubroker'] ?? "") == "Active" ? "Yes" : "No";
            }else{
                $tru_broker = "No";
            }
            if($prev_truBroker == 'Yes' && $tru_broker == 'No'){
                $tru_broker = 'Lost it';
            }
            if($prev_super_agent == 'Yes' && $super_agent == 'No'){
                $super_agent = 'Lost it';
            }

            $tru_checked_percentage = $bayut_total_live > 0 ? round(($bayut_tru_checked / $bayut_total_live) * 100, 2) : 0;
            $checked_listing_percentage = $bayut_total_live > 0 ? round(($bayut_checked_listings / $bayut_total_live) * 100, 2) : 0;
            $new_listings = Property::whereDate('published_date', '>=', $startDate)
                ->whereDate('published_date', '<=', $endDate)
                ->where('agent_id', $agent->id)
                ->where('ref_no', 'NOT LIKE', 'axc-%-r%')
                ->where('is_live', true)
                ->where('unpublished_date',null)
                ->count();
            $rePublishedCount = Property::where('is_live', '1')
                ->where("agent_id", $agent->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('ref_no', 'LIKE', 'axc-%-r%')
                ->count() ?? 0;
            $publishedCount = Property::where('is_live', '1')
                ->where("agent_id", $agent->id)
               ->whereBetween('created_at', [$startDate, $endDate])
                ->where('ref_no', 'NOT LIKE', 'axc-%-r%')
                ->whereNull('republished_date')
                ->count() ?? 0;
            $total_listing = Property::where('agent_id', $agent->id)->where('is_live',true)->count();

            $verified_listing_percentage = (int)($pfUsefulData['verified_listing_rate'] ?? 0);

             if($verified_listing_percentage == 0){
                $verified_listings = 0;
            }else if($verified_listing_percentage > 0 && $verified_listing_percentage < 100){
               $verified_listings = round(($verified_listing_percentage / 100) * $total_listing);
            }else if($verified_listing_percentage == 100){
                $verified_listings = $total_listing;
            }else{
                $verified_listings = Property::where('agent_id', $agent->id)->where('is_live', true)->whereHas('propertyMeta', function ($q) {
                    $q->where('meta_key', 'pf_verified')->where('meta_value', '1');
                })->count();
            }

            $ready_properties = Property::where('agent_id', $agent->id)->where('is_live', true)->where('completion_status', 'Ready Secondary')->count();
            $offplan_properties = Property::where('agent_id', $agent->id)->where('is_live', true)->where('completion_status', 'Off-Plan Secondary')->count();

            $data[] = [
                'id' => $agent->id,
                'name' => $agent->name,
                'agent_start_date' => $agent->joining_date ? date('d-M-y', strtotime($agent->joining_date)) : '',
                'total_listings' => $total_listing,
                'no_of_premium' => $dubizzle_premium,
                'no_of_featured' => $dubizzle_featured,
                're_listed' => $rePublishedCount,
                'new_published' => $publishedCount,
                'new_listings' => $new_listings,
                'verified_listings' => $verified_listings,
                'percentage_of_verified_listings' => $verified_listing_percentage.'%',
                'checked_listings' => $bayut_checked_listings,
                'checked_listing_percentage' => $checked_listing_percentage.'%',
                'tru_checked_offplan' => $ready_properties,
                'tru_checked_ready' => $offplan_properties,
                'total_tru_checked' => $bayut_tru_checked,
                'tru_checked_percentage' =>$tru_checked_percentage.'%',
                'eligible_tru_check' => 0,
                'super_agent' => $super_agent,
                'tru_broker' => $tru_broker,
            ];
        }
        $result = [
            'status' => true,
            'data' => $data
        ];
        return response()->json($result);
    }

    private function processTeamCreditTargetData($group, $date)
    {
        $team_summary = [];
        $dates = getPfCreditDurationDates($date);
        $pfCredit = KpiTeam::where('group_id', $group->id)
            ->where('kpi','pf_credit_limit')
            ->where('start_date', '>=', $dates['start_date'])
            ->where('end_date', '<=', $dates['end_date'])
            ->with('result')
            ->first();
        if (!empty($pfCredit)) {
            $target = $pfCredit->target;
            $achieved = $pfCredit->result->achieved ?? 0;
            $team_summary['pf_credit_limit']['target'] = $target;
            $team_summary['pf_credit_limit']['target_text'] = formatNumberMetricPrefixes($target) ?? '';
            $team_summary['pf_credit_limit']['achieved'] = $achieved;
            $team_summary['pf_credit_limit']['achieved_text'] = formatNumberMetricPrefixes($achieved) ?? '';
            $team_summary['pf_credit_limit']['percentage'] = $pfCredit->result->achieved_percentage ?? 0;
            $team_summary['pf_credit_limit']['kpi_title'] = 'Property Finder Credit Limit';;
            $team_summary['pf_credit_limit']['kpi'] = 'pf_credit_limit';
            $team_summary['pf_credit_limit']['start_date'] = $pfCredit->start_date;
            $team_summary['pf_credit_limit']['end_date'] = $pfCredit->end_date;
        }

        $dates = getBayutCreditDurationDates($date);

        $pfCredit = KpiTeam::where('group_id', $group->id)
            ->where('kpi','bayut_credit_limit')
            ->where('start_date', '>=', $dates['start_date'])
            ->where('end_date', '<=', $dates['end_date'])
            ->with('result')
            ->first();
        if (!empty( $pfCredit)) {
            $target = $pfCredit->target;
            $achieved = $pfCredit->result->achieved ?? 0;
            $team_summary['bayut_credit_limit']['target'] = $target;
            $team_summary['bayut_credit_limit']['target_text'] = formatNumberMetricPrefixes($target) ?? '';
            $team_summary['bayut_credit_limit']['achieved'] = $achieved;
            $team_summary['bayut_credit_limit']['achieved_text'] = formatNumberMetricPrefixes($achieved) ?? '';
            $team_summary['bayut_credit_limit']['percentage'] = $pfCredit->result->achieved_percentage ?? 0;
            $team_summary['bayut_credit_limit']['kpi_title'] = 'Bayut Credit Limit';;
            $team_summary['bayut_credit_limit']['kpi'] = 'bayut_credit_limit';
            $team_summary['bayut_credit_limit']['start_date'] = $pfCredit->start_date;
            $team_summary['bayut_credit_limit']['end_date'] = $pfCredit->end_date;
        }

        $pfCredit = KpiTeam::where('group_id', $group->id)
            ->where('kpi','dubizzle_credit_limit')
            ->where('start_date', '>=', $dates['start_date'])
            ->where('end_date', '<=', $dates['end_date'])
            ->with('result')
            ->first();
        if (!empty( $pfCredit)) {
            $target = $pfCredit->target;
            $achieved = $pfCredit->result->achieved ?? 0;
            $team_summary['dubizzle_credit_limit']['target'] = $target;
            $team_summary['dubizzle_credit_limit']['target_text'] = formatNumberMetricPrefixes($target) ?? '';
            $team_summary['dubizzle_credit_limit']['achieved'] = $achieved;
            $team_summary['dubizzle_credit_limit']['achieved_text'] = formatNumberMetricPrefixes($achieved) ?? '';
            $team_summary['dubizzle_credit_limit']['percentage'] = $pfCredit->result->achieved_percentage ?? 0;
            $team_summary['dubizzle_credit_limit']['kpi_title'] = 'Dubizzle Credit Limit';;
            $team_summary['dubizzle_credit_limit']['kpi'] = 'dubizzle_credit_limit';
            $team_summary['dubizzle_credit_limit']['start_date'] = $pfCredit->start_date;
            $team_summary['dubizzle_credit_limit']['end_date'] = $pfCredit->end_date;
        }
        return $team_summary;
    }
}
