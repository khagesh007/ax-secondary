<?php

namespace App\Http\Controllers\Admin\Kpi;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\KpiTarget;
use App\Models\KpiTeam;
use App\Models\KpiTeamAgentReport;
use App\Models\KpiTeamData;
use App\Models\User;
use App\Models\KpiTeamUserReport;
use App\Http\Controllers\Admin\Kpi\Resources\KpiTeamAgentReportResource;
use App\Models\UserMeta;
use App\Models\UserThirdPartyData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;

class KpiTeamReportController extends Controller
{
    public function teamReportPage($id)
    {
        abort_if(Gate::denies("Team KPI Reports"), 401);
        $data['group'] = Group::where('id', $id)->with('user')->first();
        if(empty($data['group'])){
          abort(404, 'Group not found');
        }
        $month = request()->month ?? date('Y-m');
        $start_date = \Carbon\Carbon::createFromFormat("Y-m", $month)->startOfMonth()->format('Y-m-d');
        $end_date = \Carbon\Carbon::createFromFormat("Y-m", $month)->endOfMonth()->format('Y-m-d');
        $data['reports'] = KpiTeam::where('group_id', $data['group']->id)->where('start_date', $start_date)->with('result')->get();
        $data['kpi_without_target'] =  KpiTeamData::where('group_id', $data['group']->id)->where('start_date', '>=', $start_date)->where('end_date', '<=', $end_date)->get();
        $data['avg_gross_commission'] =  KpiTeamData::where('group_id', $data['group']->id)->where('kpi','avg_gross_commission')->where('start_date', '>=', $start_date)->where('end_date', '<=', $end_date)->first();
        return view('admin.kpi_v2.team.teamReport')->with($data);
    }

    public function getTargetReport(Request $request)
    {
        $month = $request->month ?? date('Y-m');
        $agent_ids = KpiTarget::where('start_date', 'like', $month . '%')->pluck('agent_id')->unique()->toArray();
        $agents = User::whereIn('id', $agent_ids)->get();
        $data = [];
        foreach ($agents as $agent) {
            $data[$agent->id] = [
                'agent' => $agent,
                'targets' => KpiTarget::where('agent_id', $agent->id)->where('start_date', 'like', $month . '%')->with('result')->get(),
            ];
        }
        $result = [
            'status' => '1',
            'data' => $data,
        ];
        return response()->json($result);
    }

    public function getResultBreakdown(Request $request)
    {
        $period = $request->month ?? date('Y-m');
        $group_id = $request->group_id;
        $kpi = $request->kpi;

        $startDate = \Carbon\Carbon::createFromFormat("Y-m", $period)->startOfMonth()->format('Y-m-d');
        $endDate = \Carbon\Carbon::createFromFormat("Y-m", $period)->endOfMonth()->format('Y-m-d');
        $year = substr($period, 0, 4);
        $monthNum = substr($period, 5, 2);
        $data['target'] = KpiTeam::where('group_id', $group_id)->where('kpi', $kpi)->where('start_date', 'like', $period . '%')->with('result')->first();
        $data['kpi_title'] = getKpiFullText($data['target']->kpi);
        $result = [
            'status' => '1',
            'data' => $data,
        ];
        return response()->json($result);
    }
    protected function getReportFromDealApp($teamId, $parentId, $startDate, $endDate, $agents){
        $payload = [
            'team_id' => $teamId,
            'parent_id' => $parentId,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'agents' => $agents
        ];
        $response = Http::withHeaders([
            'token' => env('AXDEAL_SECRET_KEY'),
        ])->post('https://deals.axcrm.ae/backend/api/report/get-team-report', $payload);
//               $response = Http::withHeaders([
//                    'token' => env('AXDEAL_SECRET_KEY'),
//                    ])->post('http://127.0.0.1:8000/api/report/get-team-report', $payload);

        if($response->status() == 200){
            return $response->json();
            // $agent_deal_count = $json['agent_deal_count'];
        }else{
            return [];
        }
    }

    public function getAgentReport(Request $request)
    {
        $group_id = $request->group_id;
        $month = $request->month ?? date('Y-m');
        $start_date = \Carbon\Carbon::createFromFormat("Y-m", $month)->startOfMonth()->format('Y-m-d');
        $group = Group::where('id', $group_id)->with('user')->first();
        $manager = $group->user;

        $agents = User::whereHas("roles", fn($q) => $q->whereIn("name", ["Agent"]))
            ->where("parent_id", $manager->id)->where('status', true)->get();
        $kpi_report = [];

        $startDate = \Carbon\Carbon::createFromFormat("Y-m", $request->month)->startOfMonth()->format('Y-m-d');
        $endDate = \Carbon\Carbon::createFromFormat("Y-m", $request->month)->endOfMonth()->format('Y-m-d');


      $data_from_deal = $this->getReportFromDealApp($group_id, $manager->id, $startDate, $endDate, $agents->pluck('id')->toArray());
        $data_from_deal  = $data_from_deal['team'] ?? [];
        foreach ($agents as $agent) {

            $target = KpiTarget::where("kpi", "sales_target")
                ->where("agent_id", $agent->id)
                ->where("start_date", ">=", $startDate)
                ->where("end_date", "<=", $endDate)
                ->with('result')
                ->first();
            $reports = KpiTeamAgentReport::where('agent_id', $agent->id)->where('group_id', $group->id)->where("start_date", ">=", $startDate)->where("end_date", "<=", $endDate)->get();
            $total_achieved = $target->result->achieved ?? 0;
            $total_target = $target->target ?? 0;

            $kpi_report[$agent->id] = [
                'agent' => [
                    'id' => $agent->id,
                    'name' => $agent->name,
                    'full_name' => $agent->full_name,
                    'email' => $agent->email,
                    'thumbnail' => $agent->thumbnail,
                    'position' => $agent->position,
                    'achieved' => $total_achieved,
                    'target' => $total_target,
                ],
                'target' => $target,
                'reports' => KpiTeamAgentReportResource::collection($reports),
            ];
        }
        //make collection of $kpi_report and sort by achieved
        $kpi_report = collect($kpi_report)->sortByDesc('agent.achieved')->values()->all();

        $data['kpi_report'] = $kpi_report;

        $deal_data = [
            'total_deals' => $data_from_deal['total_deal_count'] ?? 0,
            'gross_revenue' => $data_from_deal['total_gross_revenue'] ?? 0,
            'sales_deal_count' => $data_from_deal['sales_deal_count'] ?? 0,
            'rental_deals_count' => $data_from_deal['rental_deals_count'] ?? 0,
            'rental_revenue' => $data_from_deal['total_rental_revenue'] ?? 0,
            'sales_revenue' => $data_from_deal['total_sales_revenue'] ?? 0,
            'avg_rental_revenue' => $data_from_deal['avg_rental_revenue'] ?? 0,
            'avg_sales_revenue' => $data_from_deal['avg_sales_revenue'] ?? 0,
            'avg_rental_commission' => $data_from_deal['avg_rental_commission'] ?? 0,
            'avg_sales_commission' => $data_from_deal['avg_sales_commission'] ?? 0,
        ];
        $data['deal_data'] = $deal_data;
        $data['kpi_without_target'] =  KpiTeamData::where('group_id', $group_id)->where('start_date', '>=',$startDate)->where('end_date', '<=',$endDate)->pluck('total','kpi');
        $result = [
            'status' => '1',
            'data' => $data,
        ];
        return response()->json($result);
    }

public function getTeamData(Request $request)
{
    $month = $request->month ?? date('Y-m');
    $group_id = $request->group_id;
    $start_date = \Carbon\Carbon::createFromFormat("Y-m", $month)->startOfMonth()->format('Y-m-d');
    $end_date = \Carbon\Carbon::createFromFormat("Y-m", $month)->endOfMonth()->format('Y-m-d');
    $kpi = $request->kpi;
    $data = KpiTeamData::where('group_id',$group_id)->where('kpi',$kpi)->where('start_date',$start_date)->first();
    if(empty($data)){
       $result = [
            'status' => '0',
            'message' => 'No data found',
        ];
    }else{
        $result = [
            'status' => '1',
            'message' => 'Data found',
            'data' => $data,
        ];
    }
    return response()->json($result);
}
    public function getTeamsOverviewData(Request $request)
    {
        $month = $request->month ?? date('Y-m');
        $start_date = \Carbon\Carbon::createFromFormat("Y-m", $month)->startOfMonth()->format('Y-m-d');
        $end_date = \Carbon\Carbon::createFromFormat("Y-m", $month)->endOfMonth()->format('Y-m-d');

        $groups = Group::with(["user"])
            ->whereNull("parent_id")
            ->orderBy("order")
            ->get();
        $data = [];
        foreach ($groups as $group){
            $selectedGroup = Group::find($group->id);
            $user = $selectedGroup->user;
            $agentIds = User::whereHas("roles", fn($q) => $q->whereIn("name", ["Agent"]))
                ->where("parent_id", $user->id)->where('status', true)->pluck('id')->toArray();

            $data[$group->id] = [
                'group' => $group,
                'group_parent_user' => $user,
                'tru_brokers' => UserThirdPartyData::where('portal', 'bayut')->where('start_date', '>=', $start_date)->where('end_date', '<=', $end_date)->whereIn('user_id', $agentIds)->where('dump_meta->bayut_is_trubroker_badge_permitted_curr_month', '1')->count(),
                'super_agent' => UserThirdPartyData::where('portal', 'property_finder')->where('start_date', '>=', $start_date)->where('end_date', '<=', $end_date)->whereIn('user_id', $agentIds)->where('dump_meta->pf_is_super_agent', '1')->count(),
//                'super_agents' => KpiTeam::where('group_id', $group->id)->where('start_date', '>=', $start_date)->where('end_date', '<=', $end_date)->with('result')->get(),
//                'team_target' => KpiTarget::where('group_id', $group->id)->where('start_date', 'like', $month . '%')->with('result')->get(),
//                'team_report_data' => $this->getReportFromDealApp($group->id, $user->id, $start_date, $end_date, $agentIds),
            ];
        }
        $result = [
            'status' => '1',
            'data' => $data,
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
            'tru_broker' => ['total_target' => $agents->count(), 'total_achieved' => 0],
            'super_agent' => ['total_target' => $agents->count(), 'total_achieved' => 0],
        ];
        $total_tru_broker = 0;
        $total_super_agent = 0;
        foreach($agents as $agent) {
            $targets = KpiTarget::where('agent_id', $agent->id)->where("start_date", ">=", $start_date)
                ->where("end_date", "<=", $end_date)->with('result')->get();

            foreach ($targets as $target) {
                $team_user_totals[$target->kpi]['total_target']+= $target->result?->target;
                $team_user_totals[$target->kpi]['total_achieved']+= $target->result->achieved;
            }

            $bayutLatestUsefulData = json_decode(UserThirdPartyData::where('user_id', $agent->id)
                ->where('portal', 'bayut')
                ->where('start_date', '>=', $currentMonthStartDate)
                ->where('end_date', '<=', $currentMonthEndDate)
                ->orderByDesc('portal_data_updated_at')
                ->first()->useful_data ?? null, true);

            $total_tru_broker+= ($bayutLatestUsefulData['trubroker'] ?? "") == "Active" ? 1 : 0;

            $pfUsefulData = json_decode(UserThirdPartyData::where('user_id', $agent->id)
                ->where('portal', 'property_finder')
                ->where('end_date', '<=', $currentMonthEndDate)
                ->orderByDesc('portal_data_updated_at')
                ->first()->useful_data ?? null, true);

            $total_super_agent+= ($pfUsefulData['is_super_agent'] ?? "") ? 1 : 0;
        }
        $team_user_totals['tru_broker']['total_achieved'] = $total_tru_broker;
        $team_user_totals['super_agent']['total_achieved'] = $total_super_agent;

        return $team_user_totals;
    }
    public function getTeamTotals(Request $request){
        $month = $request->month ?? date('Y-m');
        $start_date = \Carbon\Carbon::createFromFormat("Y-m", $month)->startOfMonth()->format('Y-m-d');
        $end_date = \Carbon\Carbon::createFromFormat("Y-m", $month)->endOfMonth()->format('Y-m-d');
    $groups = Group::with(["user"])->get();
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

    foreach ($groups as $group){
     $kpiTeams = KpiTeam::where('group_id', $group->id)->where('kpi','sales_value')->where('start_date', '>=', $start_date)->where('end_date', '<=', $end_date)->with('result')->first();
        $sales_target+= $kpiTeams->target;
        $sales_achieved+= $kpiTeams?->result?->achieved ?? 0;
        $user = $group->user;
        $agents = User::whereHas("roles", fn($q) => $q->whereIn("name", ["Agent"]))
            ->where("parent_id", $user->id)->where('status', true)->get();
        $total_target = 0; $total_achieved = 0;

        $team_users_target = $this->processTeamUserData($group,$agents,$start_date,$end_date);

        $team_live_listing_target+= $team_users_target['live_listings']['total_target'];
        $team_live_listing_achieved+= $team_users_target['live_listings']['total_achieved'];
        $team_new_listing_target+= $team_users_target['new_listings']['total_target'];
        $team_new_listing_achieved+= $team_users_target['new_listings']['total_achieved'];
        $team_tru_broker_target+= $team_users_target['tru_broker']['total_target'];
        $team_tru_broker_achieved+= $team_users_target['tru_broker']['total_achieved'];
        $team_super_agent_target+= $team_users_target['super_agent']['total_target'];
        $team_super_agent_achieved+= $team_users_target['super_agent']['total_achieved'];

    }
    $data = [];
    $data['sales_value']['target'] = $sales_target;
    $data['sales_value']['achieved'] = $sales_achieved;
    $data['sales_value']['percentage'] = $sales_target > 0 ? round(($sales_achieved / $sales_target) * 100, 2) : 0;
    $data['sales_value']['kpi_title'] = getKpiFullText('sales_value');
    $data['sales_value']['kpi'] = 'sales_value';
    $data['live_listings']['target'] = $team_live_listing_target;
    $data['live_listings']['achieved'] = $team_live_listing_achieved;
    $data['live_listings']['percentage'] = $team_live_listing_target > 0 ? round(($team_live_listing_achieved / $team_live_listing_target) * 100, 2) : 0;
    $data['live_listings']['kpi_title'] = 'Live Listings';
    $data['live_listings']['kpi'] = 'live_listings';
    $data['new_listings']['target'] = $team_new_listing_target;
    $data['new_listings']['achieved'] = $team_new_listing_achieved;
    $data['new_listings']['percentage'] = $team_new_listing_target > 0 ? round(($team_new_listing_achieved / $team_new_listing_target) * 100, 2) : 0;
    $data['new_listings']['kpi_title'] = 'New Listings';
    $data['new_listings']['kpi'] = 'new_listings';
    $data['tru_broker']['target'] = $team_tru_broker_target;
    $data['tru_broker']['achieved'] = $team_tru_broker_achieved;
    $data['tru_broker']['percentage'] = $team_tru_broker_target > 0 ? round(($team_tru_broker_achieved / $team_tru_broker_target) * 100, 2) : 0;
    $data['tru_broker']['kpi_title'] = 'Tru Broker';
    $data['tru_broker']['kpi'] = 'truBroker';
    $data['super_agent']['target'] = $team_super_agent_target;
    $data['super_agent']['achieved'] = $team_super_agent_achieved;
    $data['super_agent']['percentage'] = $team_super_agent_target > 0 ? round(($team_super_agent_achieved / $team_super_agent_target) * 100, 2) : 0;
    $data['super_agent']['kpi_title'] = 'Super Agent';
    $data['super_agent']['kpi'] = 'superAgent';


        $result = [
            'status' => '1',
            'data' => $data,
        ];
        return response()->json($result);
    }
}
