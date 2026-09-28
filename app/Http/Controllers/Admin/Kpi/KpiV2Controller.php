<?php

namespace App\Http\Controllers\Admin\Kpi;

use App\Http\Controllers\Controller;
use App\Jobs\KpiNotificationToAgents;
use App\Models\Group;
use App\Models\KpiOtherReport;
use App\Models\KpiTarget;
use App\Models\User;
use App\Models\UserMeta;
use App\Models\Property;
use App\Models\PropertyMeta;
use App\Models\Lead;
use App\Models\Source;
use App\Models\UserThirdPartyData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class KpiV2Controller extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies("Agent KPI Reports"), 401);
        $isSuperAdmin = auth()->user()->hasRole(['Super Admin']);
        $groups = Group::with(["user"])
            ->whereNull("parent_id")
            ->orderBy("order")
            ->get();

        $authUser = auth()->user();
        return view('admin.kpi_v2.dashboard', compact('groups', 'isSuperAdmin', 'authUser'));
    }

    public function setup(Request $request)
    {
        abort_if(Gate::denies("Agent KPI Reports"), 401);
        $superAdmin = auth()->user()->hasRole(['Super Admin','Admin','Head of Secondary']);
        $selectedGroup = Group::find(auth()->user()->group_id);

        if (!empty($request->manager) && $superAdmin) {
            $user = User::find($request->manager);
            $selectedGroup = Group::find($user->group_id ?? null);
        }else{
            $user = auth()->user();
        }

        $agents = User::whereHas("roles", fn($q) => $q->whereIn("name", ["Agent"]))
            ->where("parent_id", $user->id)->where('status', true)->get();

        $groups = Group::with(["user"])
            ->whereNull("parent_id")
            ->orderBy("order")
            ->get();

        $authUser = auth()->user();

        return view('admin.kpi_v2.setup')
            ->with('agents', $agents)
            ->with('groups', $groups)
            ->with('authUser', $authUser)
            ->with('isSuperAdmin', $superAdmin)
            ->with('selectedGroup', $selectedGroup);
    }

    public function saveBulkTarget(Request $request)
    {

        $request->validate([
            'agent_ids' => 'nullable|array',
            'agent_ids.*' => 'nullable|exists:users,id',
            'month' => 'required|string|date_format:Y-m',
            'manager_id' => 'required|exists:users,id',
        ]);

        $start_date = \Carbon\Carbon::createFromFormat("Y-m", $request->month)->startOfMonth()->format('Y-m-d');
        $end_date = \Carbon\Carbon::createFromFormat("Y-m", $request->month)->endOfMonth()->format('Y-m-d');
        $duration = getDaysInMonth($request->month);;
        $target_month = \Carbon\Carbon::createFromFormat("Y-m", $request->month)->format('F Y');
        $month_name = \Carbon\Carbon::createFromFormat("Y-m", $request->month)->format('F');
        if ($duration == 0) {
            $duration = 1;
        }

        $agentIds = [];
        if ($request->assign_to == "selected") {
            $agentIds = $request->agent_ids;
        }else{
            $user = User::find($request->manager_id);
            $agentIds = User::whereHas("roles", fn($q) => $q->whereIn("name", ["Agent"]))
                ->where("parent_id", $user->id)->where('status', true)->pluck('id');
        }

        foreach ($agentIds as $agentId){
            try {
                DB::beginTransaction();
                foreach ($request->targets ?? [] as $key => $target){
                    if (!empty($request->targets[$key])) {
                        $already = KpiTarget::where('agent_id', $agentId)->where('kpi', $key)->where('start_date', $start_date)->where('end_date', $end_date)->first();
                       $target_value = str_replace(',', '', $request->targets[$key]);

                        if ($already) {
                            $already->target = $target_value;
                            $already->save();
                        } else {
                            $kpi = new KpiTarget();
                            $kpi->agent_id = $agentId;
                            $kpi->manager_id = $request->manager_id;
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
               $agent = User::find($agentId);
              //sending notification to agent
                KpiNotificationToAgents::dispatch($agent, [
                    'sales_target' => $request->targets['sales_target'] ?? 0,
                    'new_listings' => $request->targets['new_listings'] ?? 0,
                    'overall_listings' => $request->targets['live_listings'] ?? 0,
                    'duration' => $target_month,
                    'month_name' => $month_name,
                ]);

                DB::commit();
            }catch (\Throwable $throwable){
                DB::rollBack();
                continue;
            }
        }

      //  dispatch(new ProcessAgentKpi());
        $result = array('status' => '1', 'msg' => ' Kpi created', 'data' => []);
        return response()->json($result);

    }

    public function saveIndividualTarget(Request $request)
    {

        $request->validate([
            'agents' => 'nullable|array',
            'agents.*' => 'nullable|exists:users,id',
            'month' => 'required|string|date_format:Y-m',
            'manager_id' => 'required|exists:users,id',
        ]);


        if (!preg_match('/^\d{4}-\d{2}$/', $request->month)) {
            return response()->json(['status' => '0', 'msg' => 'Invalid month format']);
        }

        $start_date = \Carbon\Carbon::createFromFormat("Y-m", $request->month)->startOfMonth()->format('Y-m-d');
        $end_date = \Carbon\Carbon::createFromFormat("Y-m", $request->month)->endOfMonth()->format('Y-m-d');
        $duration = getDaysInMonth($request->month);;
        $target_month = \Carbon\Carbon::createFromFormat("Y-m", $request->month)->format('F Y');
        $month_name = \Carbon\Carbon::createFromFormat("Y-m", $request->month)->format('F');
        if ($duration == 0) {
            $duration = 1;
        }

        foreach ($request->agents as $key => $value) {
            try {
                DB::beginTransaction();
                foreach ($request->targets[$value] ?? [] as $key2 => $target) {
                    if (!empty($request->targets[$value][$key2])){
                        $already = KpiTarget::where('agent_id', $value)->where('kpi', $key2)->where('start_date', $start_date)->where('end_date', $end_date)->first();

                      $target_value = str_replace(',', '', $request->targets[$value][$key2]);
                        if ($already) {
                            $already->target = $target_value;
                            $already->save();
                        } else {
                            $kpi = new KpiTarget();
                            $kpi->agent_id = $value;
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
                }
                $agent = User::find($value);
                //sending notification to agent
                KpiNotificationToAgents::dispatch($agent, [
                    'sales_target' => $request->targets[$value]['sales_target'] ?? 0,
                    'new_listings' => $request->targets[$value]['new_listings'] ?? 0,
                    'overall_listings' => $request->targets[$value]['live_listings'] ?? 0,
                    'duration' => $target_month,
                    'month_name' => $month_name,
                ]);
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

    public function  getIndividualTarget(Request $request)
    {
        $start_date = \Carbon\Carbon::createFromFormat("Y-m", $request->month)->startOfMonth()->format('Y-m-d');
        $end_date = \Carbon\Carbon::createFromFormat("Y-m", $request->month)->endOfMonth()->format('Y-m-d');
        $targets = KpiTarget::where('agent_id', $request->agent_id)->where('start_date', $start_date)->where('end_date', $end_date)->get();

        if(!$targets){
            $targets = KpiTarget::where('agent_id', $request->agent_id)->where('start_date', 'like', $request->month . '%')->get();
        }
        if (!$targets) {
           return response()->json(['status' => '0', 'msg' => 'No data found']);
        }

        $agent = User::find($request->agent_id);
        $result = [
            'status' => '1',
            'data' => [
                'agent' => $agent,
                'targets' => $targets
            ]
        ];
        return response()->json($result);
    }

    public function getReport(Request $request)
    {
        $request->validate([
            'group_id' => 'required|exists:groups,id',
            'month' => 'required|string|date_format:Y-m',
        ]);

        $selectedGroup = Group::find($request->group_id);

        $user = $selectedGroup->user;

        $agents = User::whereHas("roles", fn($q) => $q->whereIn("name", ["Agent"]))
            ->where("parent_id", $user->id)->where('status', true)->get();

        $startDate = \Carbon\Carbon::createFromFormat("Y-m", $request->month)->startOfMonth()->format('Y-m-d');
        $endDate = \Carbon\Carbon::createFromFormat("Y-m", $request->month)->endOfMonth()->format('Y-m-d');

        $prev_month_start_date = Carbon::createFromFormat("Y-m", $request->month)->subMonth()->startOfMonth()->format('Y-m-d');
        $prev_month_end_date = Carbon::createFromFormat("Y-m", $request->month)->subMonth()->endOfMonth()->format('Y-m-d');

        $currentMonthStartDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $currentMonthEndDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        $data = [];
        foreach($agents as $agent){
            $targets = KpiTarget::where('agent_id', $agent->id)->where("start_date", ">=", $startDate)
                ->where("end_date", "<=", $endDate)->with('result')->get();

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


            $data[] = [
                'agent' => $agent,
                'targets' => $targets,
                'is_truBroker' => ($bayutLatestUsefulData['trubroker'] ?? "") == "Active" ? 1 : 0,
                'is_super_agent' => ($pfUsefulData['is_super_agent'] ?? "") ? 1 : 0,
                'prev_truBroker' => ($bayutPreviousMonthUsefulData['trubroker'] ?? "") == "Active" ? 1 : 0,
                'prev_super_agent' => ($pfPreviousMonthUsefulData['is_super_agent'] ?? "") ? 1 : 0,
                'other_reports' => KpiOtherReport::where('agent_id', $agent->id)->where("start_date", ">=", $startDate)
                    ->where("end_date", "<=", $endDate)->get(),
                "total_achieved" => $targets->sum('result.achieved'),
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

    public function view(Request $request, $agentId)
    {

        if (auth()->user()->hasRole('Agent')) {
            abort_if(auth()->id() != $agentId, 401);
        }else{
            abort_if(Gate::denies("Agent KPI Reports"), 401);
        }
        $agent = User::where("id", $agentId)->first();
        $agent->load(["parent.group"]);
        $month = empty($request->month) ? date('Y-m') : $request->month;
        $startDate = \Carbon\Carbon::createFromFormat("Y-m", $month)->startOfMonth()->format('Y-m-d');
        $endDate = \Carbon\Carbon::createFromFormat("Y-m", $month)->endOfMonth()->format('Y-m-d');

        $currentMonthStartDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $currentMonthEndDate = Carbon::now()->endOfMonth()->format('Y-m-d');

        $targets = KpiTarget::where('agent_id', $agent->id)->where("start_date", ">=", $startDate)
            ->where("end_date", "<=", $endDate)->with('result')->get();
        $previousMonthTargets = KpiTarget::where('agent_id', $agent->id)
            ->where("start_date", ">=", Carbon::createFromFormat("Y-m", $month)->subMonth()->startOfMonth()->format('Y-m-d'))
            ->where("end_date", "<=", Carbon::createFromFormat("Y-m", $month)->subMonth()->endOfMonth()->format("Y-m-d"))
            ->with('result')->get();

        $otherReports = KpiOtherReport::where('agent_id', $agent->id)->where("start_date", ">=", $startDate)
            ->where("end_date", "<=", $endDate)->get();
        $previousMonthOtherReports = KpiOtherReport::where('agent_id', $agent->id)
            ->where("start_date", ">=", Carbon::createFromFormat("Y-m", $month)->subMonth()->startOfMonth()->format('Y-m-d'))
            ->where("end_date", "<=", Carbon::createFromFormat("Y-m", $month)->subMonth()->endOfMonth()->format("Y-m-d"))->get();

        $unpublished_listings_count = Property::whereDate('unpublished_date', '>=', $startDate)
            ->whereDate('unpublished_date', '<=', $endDate)
            ->where('agent_id', $agent->id)
            ->where('is_live', false)
            ->count();

        $previousMonthUnpublishedListingsCount = Property::whereDate('unpublished_date', '>=', Carbon::createFromFormat("Y-m", $month)->subMonth()->startOfMonth()->format('Y-m-d'))
            ->whereDate('unpublished_date', '<=', Carbon::createFromFormat("Y-m", $month)->subMonth()->endOfMonth()->format("Y-m-d"))
            ->where('agent_id', $agent->id)
            ->where('is_live', false)
            ->count();

        $pfData = UserThirdPartyData::where('user_id', $agent->id)
            ->where('portal', 'property_finder')
            ->orderByDesc('portal_data_updated_at')
            ->first();

        $pfUsefulData = json_decode($pfData->useful_data ?? null, true);

        $bayutData = UserThirdPartyData::where('user_id', $agent->id)
            ->where("start_date", ">=", $startDate)
            ->where("end_date", "<=", $endDate)
            ->where('portal', 'bayut')
            ->first();
        $bayutUsefulData = json_decode($bayutData->useful_data ?? null, true);


        $bayutLatestData = UserThirdPartyData::where('user_id', $agent->id)->where('portal', 'bayut')
            ->where('start_date', '>=', $currentMonthStartDate)
            ->where('end_date', '<=', $currentMonthEndDate)
            ->orderByDesc('portal_data_updated_at')->first();
        $bayutLatestUsefulData = json_decode($bayutLatestData->useful_data ?? null, true);


        $dubizzleData = UserThirdPartyData::where('user_id', $agent->id)
            ->where("start_date", ">=", $startDate)
            ->where("end_date", "<=", $endDate)
            ->where('portal', 'dubizzle')
            ->first();

        $dubizzleUsefulData = json_decode($dubizzleData->useful_data ?? null, true);

        $dubizzleLatestData = UserThirdPartyData::where('user_id', $agent->id)
            ->where('portal', 'dubizzle')
            ->where('start_date', '>=', $currentMonthStartDate)
            ->where('end_date', '<=', $currentMonthEndDate)
            ->orderByDesc('portal_data_updated_at')->first();
        $dubizzleLatestUsefulData = json_decode($dubizzleLatestData->useful_data ?? null, true);

        return view('admin.kpi_v2.agentReport')->with([
            'agent' => $agent,
            'targets' => $targets,
            "previousMonthTargets" => $previousMonthTargets,
            'otherReports' => $otherReports,
            "previousMonthOtherReports" => $previousMonthOtherReports,
            'unpublished_listings_count' => $unpublished_listings_count,
            'previousMonthUnpublishedListingsCount' => $previousMonthUnpublishedListingsCount,
            'pfData' => $pfData,
            'pfUsefulData' => $pfUsefulData,
            'bayutData' => $bayutData,
            'bayutUsefulData' => $bayutUsefulData,
            'bayutLatestData' => $bayutLatestData,
            'bayutLatestUsefulData' => $bayutLatestUsefulData,
            'dubizzleData' => $dubizzleData,
            'dubizzleLatestData' => $dubizzleLatestData,
            'dubizzleUsefulData' => $dubizzleUsefulData,
            'dubizzleLatestUsefulData' => $dubizzleLatestUsefulData,
        ]);
    }

    public function getBreakdownData(Request $request)
    {
        $period = $request->month ?? date('Y-m');
        $module = $request->kpi ?? 'live_listings';

        $startDate = \Carbon\Carbon::createFromFormat("Y-m", $period)->startOfMonth()->format('Y-m-d');
        $endDate = \Carbon\Carbon::createFromFormat("Y-m", $period)->endOfMonth()->format('Y-m-d');

        $data = [];
        if ($module == "live_listings" || $module == "new_listings" || $module == "sales_target") {
            $data = KpiTarget::where("agent_id", $request->agent_id)
                ->where("kpi", $module)
                ->where("start_date", ">=", $startDate)
                ->where("end_date", "<=", $endDate)
                ->with('result')
                ->first()->result->data ?? [];
        }else{
            $data = KpiOtherReport::where("agent_id", $request->agent_id)
                ->where("kpi", $module)
                ->where("start_date", ">=", $startDate)
                ->where("end_date", "<=", $endDate)
                ->first()->data ?? [];
        }
        $result = [
            'status' => '1',
            'data' => $data
        ];
        return response()->json($result);
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
            foreach ($request->targets ?? [] as $key2 => $target) {
                if (!empty($request->targets[$key2])){
                    $already = KpiTarget::where('agent_id', $request->agent_id)->where('kpi', $key2)->where('start_date', $start_date)->where('end_date', $end_date)->first();
                   $target_value = str_replace(',', '', $request->targets[$key2]);
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

    public function refreshAgentReports(Request $request){
       try{
        $month = $request->month ?? '';
        if(!preg_match('/^\d{4}-\d{2}$/', $month)){
            return response()->json(['status' => '0', 'msg' => 'Invalid month format']);
        }
        $start_date = \Carbon\Carbon::createFromFormat("Y-m", $month)->startOfMonth()->format('Y-m-d');
        \Artisan::call('pf:get-agent-insights');
        \Artisan::call('kpi:generate-report',['date' => $start_date]);
        $result = array('status' => true, 'msg' => 'Kpi data refreshed');
       }catch (\Throwable $th){
           $result = array('status' => false, 'msg' => 'Error occurred');
       }
        return response()->json($result);
    }

    public function refreshTeamReports(Request $request){
        try{
            $month = $request->month ?? '';
            if(!preg_match('/^\d{4}-\d{2}$/', $month)){
                return response()->json(['status' => '0', 'msg' => 'Invalid month format']);
            }
            $start_date = \Carbon\Carbon::createFromFormat("Y-m", $month)->startOfMonth()->format('Y-m-d');
         \Artisan::call('kpi:generate-team-report',['date' => $start_date]);
         $result = array('status' => true, 'msg' => 'Kpi data refreshed');
        }catch (\Throwable $th){
            $result = array('status' => false, 'msg' => 'Error occurred',
        'error' => $th->getMessage());

        }
         return response()->json($result);
     }

    public function getAgentListingData(Request $request)
    {
        // Find the agent using ID
        $agent = User::select('id')->find($request->agent_id);
        $month = $request->has('month') ? $request->input('month') : null;


        // Fetch live listings with necessary relationships and limit the columns
        $live_listings = Property::select(['id', 'is_live', 'ref_no', 'title', 'unit_no', 'community_id', 'sub_community_id', 'community_property_id'])
//                                  ->where('is_live', true)
                                  ->when($agent, function ($query) use ($agent) {
                                        $query->where('agent_id', $agent->id);
                                    })
                                  ->with(['leads' => function ($query) use ($month) {
                                    if ($month) {

                                        $query->whereYear('created_at', '=', date('Y', strtotime($month)))
                                              ->whereMonth('created_at', '=', date('m', strtotime($month)));
                                    }
                                    $query->select('property_id', 'source_id')
                                          ->groupBy('property_id', 'source_id')
                                          ->selectRaw('count(*) as count, property_id, source_id');
                                }])
                                  /*
                                  ->with(['leads' => function ($query) {
                                      $query->select('property_id', 'source_id')
                                            ->groupBy('property_id', 'source_id')
                                            ->selectRaw('count(*) as count, property_id, source_id');
                                  }])
                                    */
                                    ->get();


         // Sources data
        $sources = Source::whereIn('name', ['Property Finder', 'Bayut', 'Dubizzle'])->pluck('name', 'id');
        $selected_source_ids = $sources->keys();
        $other_sources = Source::whereNotIn('name', ['Property Finder', 'Bayut', 'Dubizzle'])->pluck('id');
        $listing_leads = [];

        foreach ($live_listings as $listing) {
            $leads = [];
            foreach ($listing->leads as $lead) {
                if ($selected_source_ids->contains($lead->source_id)) {
                    $source_name = $sources[$lead->source_id];
                } else {
                    $source_name = 'Other';
                }
                $leads[$source_name] = $lead->count;
            }
            // Calculate total leads for the current listing
            $leads['Other'] = Lead::where('property_id', $listing->id)
                                   ->whereNotIn('source_id', $selected_source_ids)
                                   ->whereIn('source_id', $other_sources)
                                   ->count();

            // Prepare the listing data with the leads
            $listing_data = [
                'total_leads' => array_sum($leads),
                'ref_no' => $listing->ref_no,
                'leads' => $leads,
                'title' => $listing->title,
                'unit_no' => $listing->unit_no,
                'image' => $listing->first_image?->thumbnail,
                'location' => $listing->location
            ];

            $listing_leads[] = $listing_data;
        }

        // Sort listings by total leads
        $listing_leads = collect($listing_leads)->sortByDesc('total_leads')->toArray();

        // Extract ref_no from listing_leads
        $ref_nos = array_column($listing_leads, 'ref_no');

        $data = [
            'ref_nos' => $ref_nos,
            'listing_lead_with_source' => $listing_leads,
            'sources' => ['Property Finder', 'Bayut', 'Dubizzle', 'Other']
        ];

        return response()->json(['status' => '1', 'data' => $data]);
    }


     public function kgetAgentListingData(Request $request){
        $agent = User::find($request->agent_id);
        $live_listings = Property::where('is_live', true)->where('agent_id', $agent->id)->get();
        $listing_lead_counts = [];
        $live_listing_ids = [];

$sources = Source::whereIn('name',['Property Finder','Bayut','Dubizzle'])->pluck('name','id');
$selected_source_ids = $sources->keys();
$other_sources = Source::whereNotIn('name',['Property Finder','Bayut','Dubizzle'])->pluck('id');
$listing_leads = [];
foreach ($live_listings as  $listing){

    $leads = [];
    foreach ($sources as $source_id => $source_name){
        $leads[$source_name] = Lead::where('property_id',$listing->id)
        ->where('source_id', $source_id)->count() ?? 0;
    }

        $leads['Other'] = Lead::where('property_id',$listing->id)->whereNotIn('source_id',$selected_source_ids)->whereIn('source_id', $other_sources)->count() ?? 0;
    $ll['total_leads'] = array_sum($leads);
    $ll['ref_no'] = $listing->ref_no;
    $ll['leads'] = $leads;
    $ll['title'] =  $listing->title;
    $ll['unit_no'] =  $listing->unit_no;
    $ll['image'] =  $listing?->first_image?->thumbnail;
    $listing_leads[] = $ll;
}


$listing_leads = collect($listing_leads)->sortByDesc(function ($value, $key) {
    return $value['total_leads']; // Sort by the value (lead count)
})
->toArray();
//get ref_no from $listing_leads
$ref_nos = array_column($listing_leads, 'ref_no');


       // $data['live_listing_lead_counts'] = $listing_lead_counts;

        $data['ref_nos'] = $ref_nos;
        $data['listing_lead_with_source'] = $listing_leads;
        $data['sources'] = ['Property Finder','Bayut','Dubizzle','Other'];
        return response()->json(['status' => '1', 'data' => $data]);
     }

     public function getListingMetaData(Request $request){
        $agent = User::find($request->agent_id);
        $live_listings = Property::where('is_live', true)->where('agent_id', $agent->id)->pluck('id');
        $data['bayut']['hot'] = PropertyMeta::whereIn('property_id', $live_listings)
        ->where('meta_key', 'bayut_level')->where('meta_value', 'Hot')->count() ?? 0;
        $data['bayut']['signature'] = PropertyMeta::whereIn('property_id', $live_listings)
        ->where('meta_key', 'bayut_level')->where('meta_value', 'Signature')->count() ?? 0;
        $data['bayut']['basic'] = PropertyMeta::whereIn('property_id', $live_listings)
        ->where('meta_key', 'bayut_level')->where('meta_value', 'Premium')->count() ?? 0;
        return response()->json(['status' => '1', 'data' => $data]);
     }
}
