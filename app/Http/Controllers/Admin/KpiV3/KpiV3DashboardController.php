<?php

namespace App\Http\Controllers\Admin\KpiV3;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\KpiTarget;
use App\Models\KpiTeam;
use App\Models\Property;
use App\Models\User;
use App\Services\Kpi\KpiTeamService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KpiV3DashboardController extends Controller
{
    protected $kpiTeamService;
    public function __construct(KpiTeamService $kpiTeamService)
    {
        $this->kpiTeamService = $kpiTeamService;
    }
    public function index()
    {
        return view('admin.kpi_v3.index');
    }



    public function kpiOverview(Request $request){
        $month = $request->month ?? date('Y-m');
        $isSuperAdmin = auth()->user()->hasRole(['Super Admin','Admin','Head of Secondary']);

        $group = null;
        $data = [];
        if (!$isSuperAdmin) {
            $group = Group::where('id',auth()->user()->group_id)->first();
        }
        $params = [
            'month' => $month,
            'start_date' => Carbon::createFromFormat("Y-m", $month)->startOfMonth()->format('Y-m-d'),
            'end_date' => Carbon::createFromFormat("Y-m", $month)->endOfMonth()->format('Y-m-d'),
            'group_id' => $group->id ?? null,
            'group' => $group->name ?? null,
            'is_super_admin' => $isSuperAdmin,
        ];
        return $this->kpiTeamService->getTeamOverview($params);
    }
    public function teamSummary(Request $request)
    {
        $month = $request->month ?? date('Y-m');
        $isSuperAdmin = auth()->user()->hasRole(['Super Admin','Admin','Head of Secondary']);

        $group = null;
        $data = [];
        if (!$isSuperAdmin) {
            $group = Group::where('id',auth()->user()->group_id)->first();
        }
        $params = [
            'month' => $month,
            'start_date' => Carbon::createFromFormat("Y-m", $month)->startOfMonth()->format('Y-m-d'),
            'end_date' => Carbon::createFromFormat("Y-m", $month)->endOfMonth()->format('Y-m-d'),
            'group_id' => $group->id ?? null,
            'group' => $group->name ?? null,
            'is_super_admin' => $isSuperAdmin,
        ];

        return $this->kpiTeamService->getTeamSummary($params);
    }

    public function teamMemberOverview(Request $request)
    {

            $month = $request->month;
            $group_id = $request->group_id;
        $isSuperAdmin = auth()->user()->hasRole(['Super Admin','Admin','Head of Secondary']);

        $group = null;
        $data = [];

            $group = Group::where('id',$group_id)->first();

        $params = [
            'month' => $month,
            'start_date' => Carbon::createFromFormat("Y-m", $month)->startOfMonth()->format('Y-m-d'),
            'end_date' => Carbon::createFromFormat("Y-m", $month)->endOfMonth()->format('Y-m-d'),
            'group_id' => $group->id ?? null,
            'group' => $group->name ?? null,
            'is_super_admin' => $isSuperAdmin,
        ];

        return $this->kpiTeamService->groupMemberOverview($params);
    }

    public function teamAgentReports(Request $request)
    {
        $month = $request->month;
        $group_id = $request->group_id;
        $isSuperAdmin = auth()->user()->hasRole(['Super Admin','Admin','Head of Secondary']);

        $group = null;
        $data = [];

        $group = Group::where('id',$group_id)->first();

        $params = [
            'month' => $month,
            'start_date' => Carbon::createFromFormat("Y-m", $month)->startOfMonth()->format('Y-m-d'),
            'end_date' => Carbon::createFromFormat("Y-m", $month)->endOfMonth()->format('Y-m-d'),
            'group_id' => $group->id ?? null,
            'group' => $group->name ?? null,
            'is_super_admin' => $isSuperAdmin,
        ];

        return $this->kpiTeamService->getGroupAgentKpiReport($params);
    }


    public function saveAgentTarget(Request $request)
    {

        $request->validate([
            'agent_id' => 'nullable|exists:users,id',
            'month' => 'required|string|date_format:Y-m',
            'manager_id' => 'nullable|exists:users,id',
        ]);


        $start_date = \Carbon\Carbon::createFromFormat("Y-m", $request->month)->startOfMonth()->format('Y-m-d');
        $end_date = \Carbon\Carbon::createFromFormat("Y-m", $request->month)->endOfMonth()->format('Y-m-d');
        $duration = getDaysInMonth($request->month);;

        if ($duration == 0) {
            $duration = 1;
        }


        try {
            DB::beginTransaction();
            $targets = [
                'new_listings' => $request->new_listings,
                'live_listings' => $request->live_listings,
                'sales_target' => $request->sales_target,
                'pipelines' => $request->pipelines,
                'bayut_credit_limit' => $request->bayut_credit_limit,
                'dubizzle_credit_limit' => $request->dubizzle_credit_limit,
                'pf_credit_limit' => $request->pf_credit_limit,
            ];
            foreach ($targets ?? [] as $key2 => $target) {
                if($key2 == 'bayut_credit_limit' || $key2 == 'dubizzle_credit_limit'){
                    $dates = getBayutCreditDurationDates();
                    $start_date = $dates['start_date'];
                    $end_date = $dates['end_date'];
                }
                if($key2 == 'pf_credit_limit'){
                    $dates = getPfCreditDurationDates();
                    $start_date = $dates['start_date'];
                    $end_date = $dates['end_date'];
                }

                    $already = KpiTarget::where('agent_id', $request->agent_id)->where('kpi', $key2)->where('start_date', $start_date)->where('end_date', $end_date)->first();
                    $target_value = str_replace(',', '', $target);
                    if ($already) {
                        $already->target = $target_value;
                        $already->save();
                    } else {
                        $kpi = new KpiTarget();
                        $kpi->agent_id = $request->agent_id;
                        $kpi->manager_id = $request->manager_id;
                        $kpi->model = getKpiModel($key2);
                        $kpi->kpi = $key2;
                        $kpi->target = $target_value;
                        $kpi->duration = $duration;
                        $kpi->start_date = $start_date;
                        $kpi->end_date = $end_date;
                        $kpi->created_at = now();
                        $kpi->created_by_id = auth()->id();
                        $kpi->save();
                    }

            }

            DB::commit();
            $result = array('status' => true, 'msg' => 'Kpi data updated');
        } catch (\Exception $e) {
            DB::rollBack();
            $result = array('status' => false, 'msg' => 'Kpi data updated','error'=>$e->getMessage());

        }

        //  dispatch(new ProcessAgentKpi());

        return response()->json($result);

    }
    public function saveGroupTarget(Request $request)
    {

        $request->validate([
            'group_id' => 'nullable|exists:groups,id',
            'month' => 'required|string|date_format:Y-m',
        ]);


        $start_date = \Carbon\Carbon::createFromFormat("Y-m", $request->month)->startOfMonth()->format('Y-m-d');
        $end_date = \Carbon\Carbon::createFromFormat("Y-m", $request->month)->endOfMonth()->format('Y-m-d');
        $duration = getDaysInMonth($request->month);;

        if ($duration == 0) {
            $duration = 1;
        }

        $group = Group::find($request->group_id);
        $group->load(["user"]);


        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

                if ($request->sales_target){
                    $already = KpiTeam::where('group_id', $request->group_id)->where('kpi', 'sales_value')->where('start_date', $start_date)->where('end_date', $end_date)->first();
                    $target_value = preg_replace('/[^0-9]/', '', $request->sales_target);
                    if ($already) {
                        //make it million
                        $already->target = $target_value;
                        $already->save();
                    } else {
                        $kpi = new KpiTeam();
                        $kpi->manager_id = $group?->user?->id ?? null;
                        $kpi->group_id = $group->id;
                        $kpi->model = 'Deal';
                        $kpi->kpi = 'sales_value';
                        $kpi->target = $target_value;
                        $kpi->duration = $duration;
                        $kpi->start_date = $start_date;
                        $kpi->end_date = $end_date;
                        $kpi->created_at = now();
                        $kpi->created_by_id = auth()->id();
                        $kpi->save();
                    }
                }

                $date = $request->month . '-' . date('d');

                if ($request->pf_credit_limit){
                    $dates = getPfCreditDurationDates($date);
                    $already = KpiTeam::where('group_id', $request->group_id)
                        ->where('kpi', 'pf_credit_limit')
                        ->where('start_date', $dates['start_date'])->where('end_date', $dates['end_date'])
                        ->first();
                    $target_value = preg_replace('/[^0-9]/', '', $request->pf_credit_limit);
                    if ($already) {
                        //make it million
                        $already->target = $target_value;
                        $already->save();
                    } else {
                        $kpi = new KpiTeam();
                        $kpi->manager_id = $group?->user?->id ?? null;
                        $kpi->group_id = $group->id;
                        $kpi->model = null;
                        $kpi->kpi = 'pf_credit_limit';
                        $kpi->target = $target_value;
                        $kpi->duration = Carbon::parse($dates['end_date'])->diffInDays($dates['start_date']) + 1;
                        $kpi->start_date = $dates['start_date'];
                        $kpi->end_date = $dates['end_date'];
                        $kpi->created_at = now();
                        $kpi->created_by_id = auth()->id();
                        $kpi->save();
                    }
                }

                if ($request->bayut_credit_limit){
                    $dates = getBayutCreditDurationDates($date);
                    $already = KpiTeam::where('group_id', $request->group_id)
                        ->where('kpi', 'bayut_credit_limit')
                        ->where('start_date', $dates['start_date'])->where('end_date', $dates['end_date'])
                        ->first();
                    $target_value = preg_replace('/[^0-9]/', '', $request->bayut_credit_limit);
                    if ($already) {
                        //make it million
                        $already->target = $target_value;
                        $already->save();
                    } else {
                        $kpi = new KpiTeam();
                        $kpi->manager_id = $group?->user?->id ?? null;
                        $kpi->group_id = $group->id;
                        $kpi->model = null;
                        $kpi->kpi = 'bayut_credit_limit';
                        $kpi->target = $target_value;
                        $kpi->duration = Carbon::parse($dates['end_date'])->diffInDays($dates['start_date']) + 1;
                        $kpi->start_date = $dates['start_date'];
                        $kpi->end_date = $dates['end_date'];
                        $kpi->created_at = now();
                        $kpi->created_by_id = auth()->id();
                        $kpi->save();
                    }
                }

                if ($request->dubizzle_credit_limit){
                    $dates = getBayutCreditDurationDates($date);
                    $already = KpiTeam::where('group_id', $request->group_id)
                        ->where('kpi', 'dubizzle_credit_limit')
                        ->where('start_date', $dates['start_date'])->where('end_date', $dates['end_date'])
                        ->first();
                    $target_value = preg_replace('/[^0-9]/', '', $request->dubizzle_credit_limit);
                    if ($already) {
                        //make it million
                        $already->target = $target_value;
                        $already->save();
                    } else {
                        $kpi = new KpiTeam();
                        $kpi->manager_id = $group?->user?->id ?? null;
                        $kpi->group_id = $group->id;
                        $kpi->model = null;
                        $kpi->kpi = 'dubizzle_credit_limit';
                        $kpi->target = $target_value;
                        $kpi->duration = Carbon::parse($dates['end_date'])->diffInDays($dates['start_date']) + 1;
                        $kpi->start_date = $dates['start_date'];
                        $kpi->end_date = $dates['end_date'];
                        $kpi->created_at = now();
                        $kpi->created_by_id = auth()->id();
                        $kpi->save();
                    }
                }
                if ($request->pipelines){
                    $already = KpiTeam::where('group_id', $request->group_id)
                        ->where('kpi', 'pipelines')
                        ->where('start_date', $start_date)->where('end_date', $end_date)
                        ->first();
                    $target_value = preg_replace('/[^0-9]/', '', $request->pipelines);
                    if ($already) {
                        $already->target = $target_value;
                        $already->save();
                    } else {
                        $kpi = new KpiTeam();
                        $kpi->manager_id = $group?->user?->id ?? null;
                        $kpi->group_id = $group->id;
                        $kpi->model = null;
                        $kpi->kpi = 'pipelines';
                        $kpi->target = $target_value;
                        $kpi->duration = $duration;
                        $kpi->start_date = $start_date;
                        $kpi->end_date = $end_date;
                        $kpi->created_at = now();
                        $kpi->created_by_id = auth()->id();
                        $kpi->save();
                    }
                }

            $result = array('status' => true, 'msg' => 'Kpi data updated', 'data' => []);
            DB::commit();
        } catch (\Exception $e) {

            DB::rollBack();
//            throw $th;
            $result = array('status' => false, 'msg' => 'Something went wrong contact support!','error'=>$e->getMessage(), 'data' => []);
        }

        //  dispatch(new ProcessAgentKpi());

        return response()->json($result);

    }

}
