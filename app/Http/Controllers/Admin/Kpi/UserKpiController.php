<?php

namespace App\Http\Controllers\Admin\Kpi;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessAgentKpi;
use App\Models\Kpi;
use App\Models\KpiStatistic;
use App\Models\Lead;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class UserKpiController extends Controller
{
    private function isArray($key)
    {
        return gettype(\request($key)) == 'array' && count(\request($key)) > 0;
    }

    public function index()
    {
        $superAdmin = auth()->user()->hasRole(['Super Admin']);
        $notAdmin = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        $agents = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['Agent']);
        })
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->where(function ($q) use ($childrenIds) {
                    $q->whereIn('id', $childrenIds);
                });
            })
            ->when(auth()->user()->hasRole("Admin - Abu Dhabi"), function ($q) {
                $q->where('region_id', 2);
            })
            ->where('status', 1)
            ->get();

        $users = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['Sales Director', 'Team Leader', 'Line Manager']);
        })->whereHas('children')
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->where(function ($q) use ($childrenIds) {
                    $q->whereIn('id', $childrenIds);
                });
            })
            ->when(auth()->user()->hasRole("Admin - Abu Dhabi"), function ($q) {
                $q->where('region_id', 2);
            })
            ->where('status', 1)
            ->get();

        return view('admin.kpi.index')
            ->with('users', $users)
            ->with('agents', $agents);
    }

    public function datatable(Request $request)
    {
        // dd($request->all());
        $superAdmin = auth()->user()->hasRole(['Super Admin']);
        $notAdmin = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        $query = User::select('users.*')->when($notAdmin, function ($q) use ($childrenIds) {
            $q->where(function ($q) use ($childrenIds) {
                $q->whereIn('id', $childrenIds);
            });
        })
            ->when(auth()->user()->hasRole("Admin - Abu Dhabi"), function ($q) {
                $q->where('region_id', 2);
            });

        if ($request->agent_id) {
            $this->isArray("agent_id") ? $query->whereIn("id", \request("agent_id")) : $query->where("id", \request("agent_id"));

        }

        $query = $query->with(['kpis' => function ($query) use ($request) {
            if ($request->start_date) {

                $value = is_array($request->start_date) ? $request->start_date : explode(' to ', $request->start_date);
                if (count($value) === 1) {
                    $query = $query->whereDate('start_date', '=', $value[0]);
                } else {
                    $query = $query->whereDate('start_date', '>=', $value[0])->whereDate('start_date', '<=', $value[1]);
                }

            }
            if ($request->end_date) {
                $value = is_array($request->end_date) ? $request->end_date : explode(' to ', $request->end_date);
                if (count($value) === 1) {
                    $query = $query->whereDate('end_date', '=', $value[0]);
                } else {
                    $query = $query->whereDate('end_date', '>=', $value[0])->whereDate('end_date', '<=', $value[1]);
                }
            }
            if ($request->kpi) {
                $this->isArray("kpi") ? $query->whereIn("kpi", \request("kpi")) : $query->where("kpi", \request("kpi"));
            }
            $query->with('stats');
        }]);

        return DataTables::of($query->whereHas('kpis', function ($q) use ($request) {
            if ($request->start_date) {
                $value = is_array($request->start_date) ? $request->start_date : explode(' to ', $request->start_date);
                if (count($value) === 1) {
                    $q = $q->whereDate('start_date', '=', $value[0]);
                } else {
                    $q = $q->whereDate('start_date', '>=', $value[0])->whereDate('start_date', '<=', $value[1]);
                }
            }
            if ($request->end_date) {
                $value = is_array($request->end_date) ? $request->end_date : explode(' to ', $request->end_date);
                if (count($value) === 1) {
                    $q = $q->whereDate('end_date', '=', $value[0]);
                } else {
                    $q = $q->whereDate('end_date', '>=', $value[0])->whereDate('end_date', '<=', $value[1]);
                }
            }
            if ($request->kpi) {
                $this->isArray("kpi") ? $q->whereIn("kpi", \request("kpi")) : $q->where("kpi", \request("kpi"));

                // $q->whereIn('kpi', $request->kpi);
            }
        })->when($notAdmin, function ($q) use ($childrenIds) {
            $q->where(function ($q) use ($childrenIds) {
                $q->whereIn('id', $childrenIds);
            });
        })
                ->when(auth()->user()->hasRole("Admin - Abu Dhabi"), function ($q) {
                    $q->where('region_id', 2);
                }))->make(true);

    }

    public function getUsers(Request $request)
    {
        $request->validate([
            'agent_id' => 'required|exists:users,id',
        ]);
        $user = User::find($request->agent_id);
        $notAdmin = $user->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent", "Head of Secondary"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds($user->id);
        }
        $query = User::whereHas("roles", fn($q) => $q->whereIn("name", ["Sales Director", "Line Manager", "Team Leader", "Agent", "Head of Secondary"]));
        $agents = $query->whereIn("parent_id", $childrenIds);
        $agents = $query->where('status', 1)->orderBy('first_name', 'asc')->get();

        return view('admin.kpi.includes._agents')
            ->with('users', $agents);
    }

    public function getModelKpi(Request $request)
    {
        $request->validate([
            'model' => 'required',
        ]);

        return view('admin.kpi.includes._model_kpi')
            ->with('model', $request->model);
    }

    public function view(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);
        $kpi = Kpi::find($request->id);
        $agents = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['Agent']);
        })

            ->where('status', 1)
            ->get();
        $users = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['Sales Director', 'Team Leader', 'Line Manager']);
        })
            ->whereHas('children')
            ->where('status', 1)
            ->get();
        return view('admin.kpi.includes._edit')
            ->with('kpi', $kpi)
            ->with('users', $users)
            ->with('agents', $agents);
    }

    public function getUserKpi(Request $request)
    {
        $request->validate([
            'agent_id' => 'required|exists:users,id',
        ]);
        $user = User::find($request->agent_id);
        $kpis = Kpi::where('agent_id', $request->agent_id)->get();
        return view('admin.kpi.kpi')
            ->with('kpis', $kpis)
            ->with('user', $user);
    }
    public function saveParameters(Request $request)
    {

        $request->validate([
            'agent_id' => 'required|exists:users,id',
            'model' => 'nullable',
            'target' => 'required',
        ]);

        // $start_date = now()->subDays($request->duration)->format('Y-m-d');
        // $end_date = now()->format('Y-m-d');
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        // count number of days between start and end date
        $start = \Carbon\Carbon::parse($start_date);
        $end = \Carbon\Carbon::parse($end_date);
        $duration = $start->diffInDays($end);

        if ($duration == 0) {
            $duration = 1;
        }

        foreach ($request->agent_id as $key => $value) {
            try {
                DB::beginTransaction();
                if ($request->target[$key] == null) {

                } else {
                    $already = Kpi::where('agent_id', $value)->where('kpi', $request->kpi)->where('start_date', $start_date)->where('end_date', $end_date)->first();
                    if ($already) {
                        $already->target = $request->target;
                        $already->save();
                    } else {
                        $kpi = new Kpi();
                        $kpi->agent_id = $value;
                        $kpi->manager_id = $request->manager_id;
                        $kpi->model = getKpiModel($request->model);
                        $kpi->kpi = $request->kpi;
                        $kpi->target = $request->target;
                        $kpi->duration = $duration;
                        $kpi->start_date = $start_date;
                        $kpi->end_date = $end_date;
                        $kpi->created_at = now();
                        $kpi->save();
                    }

                }

                DB::commit();
            } catch (\Throwable $th) {

                DB::rollBack();
                throw $th;
                continue;
            }

        }
        dispatch(new ProcessAgentKpi());
        $result = array('status' => '1', 'msg' => ' Kpi created', 'data' => []);
        return response()->json($result);

    }

    public function getkpiStats(Request $request)
    {
        $request->validate([
            'kpi' => 'required',
        ]);

        $kpi = KpiStatistic::where('kpi_id', $request->kpi)->first();

        return view('admin.kpi.includes._kpi_stats')
            ->with('kpi', $kpi);
    }

    public function viewPerformace(Request $request, $id)
    {

        $kpis = Kpi::where('agent_id', $id);
        if ($request->duration) {
            $kpis = $kpis->where('duration', $request->duration);
        }

        // "start_from" => "20-05-2024"
        // "start_to" => "20-05-2024"
        // "end_from" => "20-05-2024"
        // "end_to" => "20-05-2024"

        if ($request->start_from) {
            $kpis = $kpis->where('start_date', '>=', $request->start_from);
        }
        if ($request->start_to) {
            $kpis = $kpis->where('start_date', '<=', $request->start_to);
        }
        if ($request->end_from) {
            $kpis = $kpis->where('end_date', '>=', $request->end_from);
        }
        if ($request->end_to) {
            $kpis = $kpis->where('end_date', '<=', $request->end_to);
        }

        $kpis = $kpis->get();

        $agent = User::find($id);
        $unique = $kpis->unique('kpi');

        // count total target
        $total_target = 0;
        $total_achieved = 0;
        foreach ($kpis as $kpi) {
            if ($kpi->stats) {
                $total_target += $kpi->stats->target;
                $total_achieved += $kpi->stats->achieved;
            }
        }
        $wa_average_diff = Lead::where('agent_id', $agent->id)
            ->join('shortlinks', 'leads.id', '=', 'shortlinks.lead_id')
            ->whereNotNull('shortlinks.opted_in')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(SECOND, shortlinks.created_at, shortlinks.opted_in)) as average_diff'))
            ->value('average_diff');

        $total_listings = Property::where('agent_id', $agent->id)->count();

        // Step 1: Retrieve the agent's creation date
        $agentCreationDate = User::where('id', $agent->id)->value('created_at');
        // Step 2: Calculate the total number of leads received by the agent up to now
        $totalLeads = Lead::where('agent_id', $agent->id)->count();
        // Step 3: Calculate the total number of days since the agent was created
        $totalDays = now()->diffInDays($agentCreationDate);
        // Step 4: Calculate the average number of leads per day
        $averageLeadsPerDay = $totalLeads / $totalDays;
        // Optionally, you might want to round the average to a certain decimal place
        $averageLeadsPerDay = round($averageLeadsPerDay, 2);

        return view('admin.kpi.view')
            ->with('kpis', $kpis)
            ->with('agent', $agent)
            ->with('unique', $unique)
            ->with('total_target', $total_target)
            ->with('total_achieved', $total_achieved)
            ->with('wa_average_diff', $wa_average_diff)
            ->with('total_listings', $total_listings)
            ->with('averageLeadsPerDay', $averageLeadsPerDay);
    }

    public function updateParameters(Request $request)
    {
        {

            $request->validate([
                // 'agent_id' => 'required|exists:users,id',
                'target' => 'required',
            ]);

            // $start_date = now()->subDays($request->duration)->format('Y-m-d');
            // $end_date = now()->format('Y-m-d');
            $start_date = $request->start_date;
            $end_date = $request->end_date;

            $start = \Carbon\Carbon::parse($start_date);
            $end = \Carbon\Carbon::parse($end_date);
            $duration = $start->diffInDays($end);

            if ($duration == 0) {
                $duration = 1;
            }
            // foreach ($request->agent_id as $key => $value) {
                try {
                    DB::beginTransaction();
                    if ($request->target == null) {

                    } else {
                        // $already = Kpi::where('id',$request->id)->first();
                        // if ($already) {
                        //     $already->is_processed = false;
                        //     $already->target = $request->target;
                        //     $already->save();
                        // } else {
                            $kpi = Kpi::where('id', $request->id)->first();
                            // $kpi->agent_id = $value;
                            // $kpi->manager_id = $request->manager_id;
                            // $kpi->model = getKpiModel($request->model);
                            // $kpi->kpi = $request->kpi;
                            $kpi->target = $request->target;
                            $kpi->duration = $duration;
                            $kpi->start_date = $start_date;
                            $kpi->end_date = $end_date;
                            $kpi->is_processed = false;
                            $kpi->save();
                        // }

                    }

                    DB::commit();
                } catch (\Throwable $th) {

                    DB::rollBack();
                    // throw $th;
                    $result = array('status' => '1', 'msg' => $th->getMessage(), 'data' => []);
                    return response()->json($result);

                    // continue;
                }

            // }
            dispatch(new ProcessAgentKpi());
            $result = array('status' => '1', 'msg' => ' Kpi Updated', 'data' => []);
            return response()->json($result);

        }
    }
}
