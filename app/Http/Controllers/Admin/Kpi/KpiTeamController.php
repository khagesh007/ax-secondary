<?php

namespace App\Http\Controllers\Admin\Kpi;

use App\Http\Controllers\Controller;
use App\Models\KpiTarget;
use App\Models\KpiTeam;
use App\Models\KpiTeamData;
use App\Models\Property;
use App\Models\User;
use App\Models\Group;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Gate;

class KpiTeamController extends Controller
{
    public function dashboard(Request $request)
    {
        abort_if(Gate::denies("Team KPI Reports"), 401);
        $data['superAdmin'] = auth()->user()->hasRole(['Super Admin','Admin','Head of Secondary']);
        $data['isSuperAdmin'] = $data['superAdmin'];
//        if (!$data['superAdmin']) {
//            abort(404, 'You are not allowed to access this page');
//        }
        $month = $request->month ?? date('Y-m');

        $start_date = \Carbon\Carbon::createFromFormat("Y-m", $month)->startOfMonth()->format('Y-m-d');
        $end_date = \Carbon\Carbon::createFromFormat("Y-m", $month)->endOfMonth()->format('Y-m-d');

        $selectedGroup = null;
        if (!$data['isSuperAdmin']) {
            $selectedGroup = Group::find(auth()->user()->group_id);
        }


        $data['selectedGroup'] = $selectedGroup;
        $data['groups'] = Group::with(["user"])
            ->whereNull("parent_id")
            ->orderBy("order")
            ->get();
        $data['reports'] = [];
        foreach ($data['groups'] as $group) {
            $data['reports'][$group->id] = KpiTeam::where('group_id', $group->id)->where('start_date', '>=', $start_date)->where('end_date', '<=', $end_date)->with('result')->get();
            $data['team_data'][$group->id] = KpiTeamData::where('group_id', $group->id)->where('start_date', '>=', $start_date)->where('end_date', '<=', $end_date)->get();
        }

        return view('admin.kpi_v2.team.dashboard')->with($data);
    }

    public function teamTargetSetup()
    {
        abort_if(Gate::denies("Team KPI Reports"), 401);
        $data['superAdmin'] = auth()->user()->hasRole(['Super Admin','Admin','Head of Secondary']);
        $data['selectedGroup'] = Group::find(auth()->user()->group_id);
//        if (!$data['superAdmin']) {
//            abort(404, 'You are not allowed to access this page');
//        }

        if (!empty($request->manager) && $data['superAdmin']) {
            $user = User::find($request->manager);
            $selectedGroup = Group::find($user->group_id ?? null);
        }
        $data['isSuperAdmin'] = $data['superAdmin'];


        $data['groups'] = Group::with(["user"])
            ->whereNull("parent_id")
            ->orderBy("order")
            ->get();

        $data['authUser'] = auth()->user();

        return view('admin.kpi_v2.team.setup')->with($data);
    }

    public function teamReportPage()
    {
        abort_if(Gate::denies("Team KPI Reports"), 401);
        return view('admin.kpi_v2.teamReport');
    }

    public function saveBulkTarget(Request $request)
    {

        $request->validate([
            'month' => 'required|string|date_format:Y-m',
        ]);

        $start_date = \Carbon\Carbon::createFromFormat("Y-m", $request->month)->startOfMonth()->format('Y-m-d');
        $end_date = \Carbon\Carbon::createFromFormat("Y-m", $request->month)->endOfMonth()->format('Y-m-d');
        $duration = getDaysInMonth($request->month);;

        if ($duration == 0) {
            $duration = 1;
        }

        $groups = [];

        if ($request->assign_to == "selected") {
            $groupIds = $request->group_ids;
            $groups = Group::with(["user"])
                ->whereNull("parent_id")
                ->whereIn("id", $groupIds)
                ->orderBy("order")
                ->get();
        }else{
            $groups = Group::with(["user"])
                ->whereNull("parent_id")
                ->orderBy("order")
                ->get();
        }

        foreach ($groups as $group) {
            if ($group->user) {
                try {
                    DB::beginTransaction();
                    foreach ($request->targets ?? [] as $key => $target) {


                        if (!empty($request->targets[$key])) {
                            $already = KpiTeam::where('group_id', $group->id)->where('manager_id', $group?->user?->id)->where('kpi', $key)->where('start_date', $start_date)->where('end_date', $end_date)->first();

                            $target_value = preg_replace('/[^0-9]/', '', $request->targets[$key]);
                            if ($already) {
                                $already->target = $target_value;
                                $already->save();
                            } else {
                                $kpi = new KpiTeam();
                                $kpi->manager_id = $group?->user?->id ?? null;
                                $kpi->group_id = $group->id;
                                $kpi->model = getKpiModel($key);
                                $kpi->kpi = $key;
                                $kpi->target = $target_value;
                                $kpi->duration = $duration;
                                $kpi->start_date = $start_date;
                                $kpi->end_date = $end_date;
                                $kpi->created_at = now();
                                $kpi->created_by_id = auth()->id();
                                $kpi->save();
                            }
                        }
                    }
                    DB::commit();

                } catch (\Throwable $throwable) {
                    print_r($throwable->getMessage());
                    DB::rollBack();
                    //continue;
                    $result = array('status' => '0', 'msg' => ' Kpi not created', 'data' => []);
                    return response()->json($result);
                }
            }
        }

        $result = array('status' => '1', 'msg' => ' Kpi created', 'data' => []);
        return response()->json($result);

        //  dispatch(new ProcessAgentKpi());


    }

    public function saveIndividualTarget(Request $request)
    {

        $request->validate([
            'groups' => 'nullable|array',
            'groups.*' => 'nullable|exists:groups,id',
            'month' => 'required|string|date_format:Y-m',
        ]);


        $start_date = \Carbon\Carbon::createFromFormat("Y-m", $request->month)->startOfMonth()->format('Y-m-d');
        $end_date = \Carbon\Carbon::createFromFormat("Y-m", $request->month)->endOfMonth()->format('Y-m-d');
        $duration = getDaysInMonth($request->month);;

        if ($duration == 0) {
            $duration = 1;
        }

        foreach ($request->groups as $key => $value) {
            $group = Group::find($value);
            try {
                \Illuminate\Support\Facades\DB::beginTransaction();
                foreach ($request->targets[$value] ?? [] as $key2 => $target) {
                    if (!empty($request->targets[$value][$key2])){
                        $already = KpiTeam::where('group_id', $value)->where('manager_id', $group?->user?->id)->where('kpi', $key2)->where('start_date', $start_date)->where('end_date', $end_date)->first();
                        if ($already) {
                           // $already->target = $request->targets[$value][$key2] * 1000000;
                            //remove other character and keep numbers only
                            $target = preg_replace('/[^0-9]/', '', $request->targets[$value][$key2]);
                            $already->target = $target ?? 0;
                            $already->save();
                        } else {
                            $target = preg_replace('/[^0-9]/', '', $request->targets[$value][$key2]);
                            $kpi = new KpiTeam();
                            $kpi->manager_id = $group?->user?->id ?? null;
                            $kpi->group_id = $group->id;
                            $kpi->model = getKpiModel($key2);
                            $kpi->kpi = $key2;
                            $kpi->target = $target ?? 0;
                            $kpi->duration = $duration;
                            $kpi->start_date = $start_date;
                            $kpi->end_date = $end_date;
                            $kpi->created_at = now();
                            $kpi->created_by_id = auth()->id();
                            $kpi->save();
                        }
                    }
                }

                DB::commit();
            } catch (\Throwable $th) {

                DB::rollBack();
                throw $th;
                continue;
            }



        }
        //  dispatch(new ProcessAgentKpi());
        $result = array('status' => '1', 'msg' => ' Kpi created', 'data' => []);
        return response()->json($result);

    }

    public function getReports(Request $request)
    {
        $request->validate([
            'month' => 'required|string|date_format:Y-m',
        ]);

        $groups = Group::with(["user"])
            ->whereNull("parent_id")
            ->orderBy("order")
            ->get();

        $startDate = \Carbon\Carbon::createFromFormat("Y-m", $request->month)->startOfMonth()->format('Y-m-d');
        $endDate = \Carbon\Carbon::createFromFormat("Y-m", $request->month)->endOfMonth()->format('Y-m-d');

        $data = [];

        foreach ($groups as $group) {
            $data[$group->id] = [
                "group" => $group,
                "targets" => KpiTeam::where('group_id', $group->id)->where("start_date", ">=", $startDate)
                    ->where("end_date", "<=", $endDate)->with('result')->get(),
            ];
        }

        $result = [
            'status' => '1',
            'data' => $data
        ];
        return response()->json($result);
    }

    public function getIndividualTarget(Request $request)
    {
        $request->validate([
            "group_id" => "nullable|exists:groups,id",
        ]);

        $group = Group::find($request->group_id);
        $group->load(["user"]);


        $start_date = \Carbon\Carbon::createFromFormat("Y-m", $request->month)->startOfMonth()->format('Y-m-d');
        $end_date = \Carbon\Carbon::createFromFormat("Y-m", $request->month)->endOfMonth()->format('Y-m-d');
        $targets = KpiTeam::where('group_id', $group->id)->where("manager_id", $group->user?->id)->where('start_date', $start_date)->where('end_date', $end_date)->get();

        if(!$targets){
            $targets = KpiTeam::where('group_id', $group->id)->where("manager_id", $group->user?->id)->where('start_date', 'like', $request->month . '%')->get();
        }
        if (!$targets) {
            return response()->json(['status' => '0', 'msg' => 'No data found']);
        }

        $result = [
            'status' => '1',
            'data' => [
                'group' => $group,
                'targets' => $targets
            ]
        ];
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
            foreach ($request->targets ?? [] as $key2 => $target) {
                if (!empty($request->targets[$key2])){
                    $already = KpiTeam::where('group_id', $group->id)->where('manager_id', $group?->user?->id)->where('kpi', $key2)->where('start_date', $start_date)->where('end_date', $end_date)->first();
                   $target_value = preg_replace('/[^0-9]/', '', $request->targets[$key2]);
                    if ($already) {
                        //make it million
                        $already->target = $target_value;
                        $already->save();
                    } else {
                        $kpi = new KpiTeam();
                        $kpi->manager_id = $group?->user?->id ?? null;
                        $kpi->group_id = $group->id;
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
            }

            DB::commit();
        } catch (\Throwable $th) {

            DB::rollBack();
            throw $th;
        }

        //  dispatch(new ProcessAgentKpi());
        $result = array('status' => '1', 'msg' => ' Kpi created', 'data' => []);
        return response()->json($result);

    }


}
