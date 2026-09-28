<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Admin\Dashboard\DashboardController;
use App\Http\Controllers\Controller;
use App\Models\Community;
use App\Models\SubCommunity;
use App\Models\CommunityProperty;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\EventType;
use App\Models\Lead;
use App\Models\LeadStatus;
use App\Models\Portal;
use App\Models\Property;
use App\Models\Source;
use App\Models\User;
use App\Models\UserLocation;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Spatie\Activitylog\Models\Activity;
class AgentReportController extends Controller
{
    public function index(Request $request)
    {
        $data["portals"] = Portal::where('status', true)->get();
        $childrenIds = [];
        if ($request->has('agent') && $request->agent != null) {
            $agent = User::find($request->agent);
            $notAdmin = $agent->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);

            if ($notAdmin) {
                $childrenIds = getUserChildrenIdsOnly($request->agent);
                $childrenIds[] = $request->agent;
            }
            $data['user'] = $agent;
            $data['languages'] = $agent->languages;
        } else {
            $notAdmin = false;
            if ($notAdmin) {
                $childrenIds = getUserChildrenIds(auth()->user()->id);
            }
            $data['user'] = auth()->user();
            $data['languages'] = auth()->user()->languages;

        }

    //    $properties = Property::whereIn('agent_id',$childrenIds)->where('community_id',82)->count();
    //    dd($properties);

        $portals = Portal::where("status", true)->get();
        $portalWiseCounts = [];
        $portalWiseCounts = Portal::where('status', true)
            ->withCount(['properties' => function ($query) use ($notAdmin, $childrenIds) {
                $query->where('is_live', '1');
                if ($notAdmin) {
                    $query->whereIn('agent_id', $childrenIds);
                }
            }])
            ->get(['id', 'name', 'thumbnail']);

        $data["portalWiseCounts"] = $portalWiseCounts;

        $data["propertyCount"] = Property::where('is_live', '1')->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count();

        $data["contactCount"] = Contact::where('status', 1)->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count();
        $data["leadCount"] = Lead::where('status', 1)->where(function ($q) {
            $q->whereHas('agent', function ($q) {
                $q->where('status', 1);
            })->orWhereNull('agent_id');
        })->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->where('status', 1)
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->whereNull('converted_to_listing_lead')
            ->where('is_archive', '0')->count();
        $data["dealCount"] = Deal::when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count();
        $data['event_types'] = EventType::where('status', 1)->get();
        $notAdmin = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);

        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->user()->id, true);
        }
        $data['agents'] = User::whereHas("roles", function ($q) {
            $q->whereIn("name", ["Agent", "Line Manager", "Team Leader", "Sales Director", "Head of Secondary"]);
        })->when($notAdmin, fn($q) => $q->whereIn("parent_id", $childrenIds)->orWhere('id', auth()->user()->id))->where("status", true)->get();
        $data['wa_average_diff'] = Lead::whereIn('agent_id', $childrenIds)
            ->join('shortlinks', 'leads.id', '=', 'shortlinks.lead_id')
            ->whereNotNull('shortlinks.opted_in')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(SECOND, shortlinks.created_at, shortlinks.opted_in)) as average_diff'))
            ->value('average_diff');

        return view('admin.reports.agent', $data);
    }

    public function dashboardPieData(Request $request)
    {
        abort_if(Gate::denies("Dashboard"), 401);
        $dates = [];
        $user_id = ($request->has('user_id') && $request->user_id != '') ? $request->user_id : '';
        if ($user_id != '') {
            $user = User::find($user_id);
            $dates = $request->dates;
        } else {
            $user = auth()->user();
        }

        $notAdmin = $user->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIdsOnly($request->user_id);
            $childrenIds[] = $request->user_id;
        }

        $portals = Portal::where('status', true)->pluck('name', 'id')->toArray();
        $portal_ids = array_keys($portals);
        //$portal_name = array_values($portals);
        //calculation for rent
        $rent_property = Property::select('property_portals.portal_id', DB::raw('count(*) as total'))
            ->leftjoin('property_portals', 'properties.id', '=', 'property_portals.property_id')
            ->groupBy('property_portals.portal_id')
            ->whereIn('properties.for', ['Residential for rent', 'Commercial for rent'])
            ->whereIn('property_portals.portal_id', $portal_ids)
//            ->when(!empty($dates), function ($q) use ($dates) {
//                $q->whereDate('properties.created_at', '>=', $dates['startDate'])->whereDate('properties.created_at', '<=', $dates['endDate']);
//            })
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->where('publish_status', 'PUBLISHED')->get();
        $rentValues = [];
        foreach ($rent_property as $key => $value) {
            $rentValues[$value->portal_id] = $value->total;
        }
        $sale_property = Property::select('property_portals.portal_id', DB::raw('count(*) as total'))
            ->leftjoin('property_portals', 'properties.id', '=', 'property_portals.property_id')
            ->groupBy('property_portals.portal_id')
            ->whereIn('property_portals.portal_id', $portal_ids)
            ->whereIn('properties.for', ['Residential for sale', 'Commercial for sale'])
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })
            ->when(!empty($dates), function ($q) use ($dates) {
                $q->whereDate('properties.created_at', '>=', $dates['startDate'])->whereDate('properties.created_at', '<=', $dates['endDate']);
            })->where('publish_status', 'PUBLISHED')->get();

        $saleValues = [];
        foreach ($sale_property as $key => $value) {
            $saleValues[$value->portal_id] = $value->total;
        }

        $data = [

            "datasets" => ["rent_portal_pie_chart" =>
                [
                    "label" => $portals,
                    "data" => $rentValues,
                    "backgroundColor" => [
                        "rgba(255, 99, 132, 0.2)",
                        "rgba(54, 162, 235, 0.2)",
                        "rgba(255, 206, 86, 0.2)",
                        "rgba(75, 192, 192, 0.2)",
                        "rgba(153, 102, 255, 0.2)",
                        "rgba(255, 159, 64, 0.2)",
                    ],
                    "borderColor" => [
                        "rgba(255, 99, 132, 1)",
                        "rgba(54, 162, 235, 1)",
                        "rgba(255, 206, 86, 1)",
                        "rgba(75, 192, 192, 1)",
                        "rgba(153, 102, 255, 1)",
                        "rgba(255, 159, 64, 1)",
                    ],
                    "borderWidth" => 1,
                ],
                "sale_portal_pie_chart" =>
                [
                    "label" => $portals,
                    "data" => $saleValues,
                    "backgroundColor" => [
                        "rgba(255, 99, 132, 0.2)",
                        "rgba(54, 162, 235, 0.2)",
                        "rgba(255, 206, 86, 0.2)",
                        "rgba(75, 192, 192, 0.2)",
                        "rgba(153, 102, 255, 0.2)",
                        "rgba(255, 159, 64, 0.2)",
                    ],
                    "borderColor" => [
                        "rgba(255, 99, 132, 1)",
                        "rgba(54, 162, 235, 1)",
                        "rgba(255, 206, 86, 1)",
                        "rgba(75, 192, 192, 1)",
                        "rgba(153, 102, 255, 1)",
                        "rgba(255, 159, 64, 1)",
                    ],
                    "borderWidth" => 1,
                ],
            ],
        ];

        return response()->json(['status' => '1', 'data' => $data]);
    }
    public function getRecentLeads(Request $request)
    {
        $agent_id = ($request->has('agent_id') && $request->agent_id != '') ? $request->agent_id : '';
        return (new DashboardController())->recentLeads($agent_id);
    }
    public function getRecentListingLeads(Request $request)
    {
        $agent_id = ($request->has('agent_id') && $request->agent_id != '') ? $request->agent_id : '';
        return (new DashboardController())->recentListingLeads($agent_id);
    }
    public function agentPortalData(Request $request)
    {

        if (auth()->user()->hasRole(["Super Admin", "Admin"])) {
            $data['users'] = $users = User::whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })->select('id', 'name')
                ->get();
            $notAdmin = false;
            $childrenIds = [];
        } else {
            $notAdmin = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);

            $childrenIds = [];
            if ($notAdmin) {
                $childrenIds = getUserChildrenIds(auth()->user()->id, true);
            }
            $data['users'] = $users = User::whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })->whereIn('id', $childrenIds)->select('id', 'name')
                ->get();
        }

        $portal = Portal::where('name', 'Property Finder')->first();
        $key = $request->get('key');
        $val = $request->get('val');
        $agent_listings = [];
        $portal_data = [];
        foreach ($users as $user) {
            $portal_data[$user->name] = Property::whereHas('propertyMeta', function ($query) use ($key, $val) {
                $query->where('meta_key', $key)
                    ->where('meta_value', '=', $val);
            })->whereHas('portals', function ($q) use ($portal) {
                $q->where('portal_id', $portal->id);
            })->where("agent_id", $user->id)->where('publish_status', 'PUBLISHED')->count();
        }

        //convert $leads to collection and sort by value
        $portal_data = collect($portal_data)->sort()->reverse()->toArray();

        $data['portal_data'] = $portal_data;

        return response()->json(['status' => '1', 'data' => $data, 'data_type' => 'fresh']);
    }


    public function agentPortalDatAbuDhabi(Request $request)
    {

        if (auth()->user()->hasRole(["Super Admin", "Admin"])) {
            $data['users'] = $users = User::whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })->select('id', 'name')
                ->get();
            $notAdmin = false;
            $childrenIds = [];
        } else {
            $notAdmin = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);

            $childrenIds = [];
            if ($notAdmin) {
                $childrenIds = getUserChildrenIds(auth()->user()->id, true);
            }
            $data['users'] = $users = User::whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })->whereIn('id', $childrenIds)->select('id', 'name')
                ->get();
        }

        $portal = Portal::where('name', 'Property Finder')->first();
        $key = $request->get('key');
        $val = $request->get('val');
        $agent_listings = [];
        $portal_data = [];
        foreach ($users as $user) {
            $portal_data[$user->name] = Property::where('region_id',2)->whereHas('propertyMeta', function ($query) use ($key, $val) {
                $query->where('meta_key', $key)
                    ->where('meta_value', '=', $val);
            })->whereHas('portals', function ($q) use ($portal) {
                $q->where('portal_id', $portal->id);
            })->where("agent_id", $user->id)->where('publish_status', 'PUBLISHED')->count();
        }

        //convert $leads to collection and sort by value
        $portal_data = collect($portal_data)->sort()->reverse()->toArray();

        $data['portal_data'] = $portal_data;

        return response()->json(['status' => '1', 'data' => $data, 'data_type' => 'fresh']);
    }

    public function agentPortalDataSecondary(Request $request)
    {

        if (auth()->user()->hasRole(["Super Admin", "Admin"])) {
            $data['users'] = $users = User::whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })->select('id', 'name')
                ->get();
            $notAdmin = false;
            $childrenIds = [];
        } else {
            $notAdmin = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);

            $childrenIds = [];
            if ($notAdmin) {
                $childrenIds = getUserChildrenIds(auth()->user()->id, true);
            }
            $data['users'] = $users = User::whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })->whereIn('id', $childrenIds)->select('id', 'name')
                ->get();
        }

        $portal = Portal::where('name', 'Property Finder')->first();
        $key = $request->get('key');
        $val = $request->get('val');
        $agent_listings = [];
        $portal_data = [];
        foreach ($users as $user) {
            $portal_data[$user->name] = Property::whereNot('region_id',2)->whereHas('propertyMeta', function ($query) use ($key, $val) {
                $query->where('meta_key', $key)
                    ->where('meta_value', '=', $val);
            })->whereHas('portals', function ($q) use ($portal) {
                $q->where('portal_id', $portal->id);
            })->where("agent_id", $user->id)->where('publish_status', 'PUBLISHED')->count();
        }

        //convert $leads to collection and sort by value
        $portal_data = collect($portal_data)->sort()->reverse()->toArray();

        $data['portal_data'] = $portal_data;

        return response()->json(['status' => '1', 'data' => $data, 'data_type' => 'fresh']);
    }

    public function agentPortalDataPrimary(Request $request)
    {


        if (auth()->user()->hasRole(["Super Admin", "Admin"])) {
            $data['users'] = $users = User::whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })->select('id', 'name')->where('bitrix_role','like','%Primary%')
                ->get();
            $notAdmin = false;
            $childrenIds = [];
        } else {
            $notAdmin = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);

            $childrenIds = [];
            if ($notAdmin) {
                $childrenIds = getUserChildrenIds(auth()->user()->id, true);
            }
            $data['users'] = $users = User::whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })->whereIn('id', $childrenIds)->select('id', 'name')
                ->get();
        }

        $portal = Portal::where('name', 'Property Finder')->first();
        $key = $request->get('key');
        $val = $request->get('val');
        $agent_listings = [];
        $portal_data = [];
        foreach ($users as $user) {
            $portal_data[$user->name] = Property::where('completion_status',['Ready Primary','Off-Plan Ready'])->whereHas('propertyMeta', function ($query) use ($key, $val) {
                $query->where('meta_key', $key)
                    ->where('meta_value', '=', $val);
            })->whereHas('portals', function ($q) use ($portal) {
                $q->where('portal_id', $portal->id);
            })->where("agent_id", $user->id)->whereHas('agent',function($q){
                $q->where('bitrix_role','like','%Primary%');
            })->where('publish_status', 'PUBLISHED')->count();
        }

        //convert $leads to collection and sort by value
        $portal_data = collect($portal_data)->sort()->reverse()->toArray();

        $data['portal_data'] = $portal_data;

        return response()->json(['status' => '1', 'data' => $data, 'data_type' => 'fresh']);
    }
    public function agentPortalDataChinese(Request $request)
    {


        if (auth()->user()->hasRole(["Super Admin", "Admin"])) {
            $data['users'] = $users = User::whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })->select('id', 'name')->where('bitrix_role','like','%Chinese%')
                ->get();
            $notAdmin = false;
            $childrenIds = [];
        } else {
            $notAdmin = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);

            $childrenIds = [];
            if ($notAdmin) {
                $childrenIds = getUserChildrenIds(auth()->user()->id, true);
            }
            $data['users'] = $users = User::whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })->whereIn('id', $childrenIds)->select('id', 'name')
                ->get();
        }

        $portal = Portal::where('name', 'Property Finder')->first();
        $key = $request->get('key');
        $val = $request->get('val');
        $agent_listings = [];
        $portal_data = [];
        foreach ($users as $user) {
            $portal_data[$user->name] = Property::whereHas('propertyMeta', function ($query) use ($key, $val) {
                $query->where('meta_key', $key)
                    ->where('meta_value', '=', $val);
            })->whereHas('portals', function ($q) use ($portal) {
                $q->where('portal_id', $portal->id);
            })->where("agent_id", $user->id)->whereHas('agent',function($q){
                $q->where('bitrix_role','like','%Primary%');
            })->where('publish_status', 'PUBLISHED')->count();
        }

        //convert $leads to collection and sort by value
        $portal_data = collect($portal_data)->sort()->reverse()->toArray();

        $data['portal_data'] = $portal_data;

        return response()->json(['status' => '1', 'data' => $data, 'data_type' => 'fresh']);
    }


    public function agentPortalDataCommercial(Request $request)
    {

        if (auth()->user()->hasRole(["Super Admin", "Admin"])) {
            $data['users'] = $users = User::whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })->select('id', 'name')
                ->get();
            $notAdmin = false;
            $childrenIds = [];
        } else {
            $notAdmin = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);

            $childrenIds = [];
            if ($notAdmin) {
                $childrenIds = getUserChildrenIds(auth()->user()->id, true);
            }
            $data['users'] = $users = User::whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })->whereIn('id', $childrenIds)->select('id', 'name')
                ->get();
        }

        $portal = Portal::where('name', 'Property Finder')->first();
        $key = $request->get('key');
        $val = $request->get('val');
        $agent_listings = [];
        $portal_data = [];
        foreach ($users as $user) {
            $portal_data[$user->name] = Property::whereIn('for',['Commercial for sale','Commercial for rent'])->whereHas('propertyMeta', function ($query) use ($key, $val) {
                $query->where('meta_key', $key)
                    ->where('meta_value', '=', $val);
            })->whereHas('portals', function ($q) use ($portal) {
                $q->where('portal_id', $portal->id);
            })->where("agent_id", $user->id)->where('publish_status', 'PUBLISHED')->count();
        }

        //convert $leads to collection and sort by value
        $portal_data = collect($portal_data)->sort()->reverse()->toArray();

        $data['portal_data'] = $portal_data;

        return response()->json(['status' => '1', 'data' => $data, 'data_type' => 'fresh']);
    }
    public function getLeads(Request $request)
    {
        $agent_id = ($request->has('agent_id') && $request->agent_id != '') ? $request->agent_id : '';
        $dates = [];
        if ($agent_id != '') {
            $user = User::find($agent_id);
            $dates = $request->dates;
        } else {
            $user = auth()->user();
        }
        if (Cache::has('dashboard_lead_data' . $user->id)) {
            return response()->json(['status' => '1', 'data' => Cache::get('dashboard_lead_data' . $user->id), 'data_type' => 'cached']);
        }
        $notAdmin = $user->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIdsOnly($user->id);
            $childrenIds[] = $user->id;
        }
        $statuses = LeadStatus::where('status', '1')->get();
        $sources = Source::where('status', '1')->where('is_lead_source', '1')->get();
        $leads = [];
        $lead_status = [];
        $lead_source = [];
        foreach ($statuses as $status) {
            $lead_status[$status->name] = Lead::where('lead_status_id', $status->id)
                ->when($notAdmin, function ($q) use ($childrenIds) {
                    $q->whereIn("agent_id", $childrenIds);
                })->when(!empty($dates), function ($q) use ($dates) {
                $q->whereDate('leads.created_at', '>=', $dates['startDate'])->whereDate('leads.created_at', '<=', $dates['endDate']);
            })->where('status', '1')
                ->whereNull('converted_to_listing_lead')
                ->where('is_archive', '0')->count();
        }
        foreach ($sources as $source) {
            $count = Lead::where('source_id', $source->id)->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->when(!empty($dates), function ($q) use ($dates) {
                $q->whereDate('leads.created_at', '>=', $dates['startDate'])->whereDate('leads.created_at', '<=', $dates['endDate']);
            })->where('status', 1)
                ->whereNull('converted_to_listing_lead')
                ->where('is_archive', '0')->count();
            if ($count > 0) {
                $lead_source[$source->name] = $count;
            }
        }
        $leads['lead_by_status'] = $lead_status;
        $leads['lead_by_source'] = $lead_source;
        $month = [];
        $leads6month = [];
        $newLeads = [];
        $successfulleads = [];
        $unsuccessfulleads = [];
        $startMonth = date('F', strtotime("-5 month"));
        // start month count from 6 month back
        for ($i = 0; $i < 6; $i++) {

            $leads6month['months'][] = date('F', strtotime($startMonth . ' +' . $i . ' month'));
            $leads6month['newLeads'][] = Lead::whereMonth('created_at', '=', date('m', strtotime($startMonth . ' +' . $i . ' month')))
                ->whereYear('created_at', '=', date('Y', strtotime($startMonth . ' +' . $i . ' month')))
                ->when($notAdmin, function ($q) use ($childrenIds) {
                    $q->whereIn("agent_id", $childrenIds);
                })
                ->count();
            $leads6month['successful'][] = Lead::whereMonth('created_at', '=', date('m', strtotime($startMonth . ' +' . $i . ' month')))
                ->whereYear('created_at', '=', date('Y', strtotime($startMonth . ' +' . $i . ' month')))
                ->where('lead_status_id', 9)
                ->when($notAdmin, function ($q) use ($childrenIds) {
                    $q->whereIn("agent_id", $childrenIds);
                })
                ->count();
            $leads6month['unsuccessful'][] = Lead::whereMonth('created_at', '=', date('m', strtotime($startMonth . ' +' . $i . ' month')))
                ->whereYear('created_at', '=', date('Y', strtotime($startMonth . ' +' . $i . ' month')))
                ->where('lead_status_id', 12)
                ->when($notAdmin, function ($q) use ($childrenIds) {
                    $q->whereIn("agent_id", $childrenIds);
                })
                ->count();
        }

        $leads['last6month'] = $leads6month;
        Cache::put('dashboard_lead_data' . auth()->id(), $leads, 30);
        return response()->json(['status' => '1', 'data' => $leads, 'data_type' => 'fresh']);
    }

    public function getActivityLog(Request $request)
    {
        $params = new Request();
        $params->replace($request->all());
        $subjectModel = "App\\Models\\User";
        $dates = $request->dates ?? [];

        // $res = Activity::where("causer_type", User::class)->where("causer_id", $request->subject_id)
        //     ->when(!empty($dates), function ($q) use ($dates) {
        //         $q->whereDate('activity_log.created_at', '>=', $dates['startDate'])->whereDate('activity_log.created_at', '<=', $dates['endDate']);
        //     })->latest()->take(10)->with(["subject", "causer"])->orderBy('created_at', 'desc')->limit(30)->get();

        $activities = Activity::where('causer_id', $request->subject_id)
            ->when(!empty($dates), function ($q) use ($dates) {
                $q->whereDate('activity_log.created_at', '>=', $dates['startDate'])->whereDate('activity_log.created_at', '<=', $dates['endDate']);
            })->orderBy('created_at', 'desc')->get()->groupBy(function ($item, $key) {
            return $item->created_at->format('Y-m-d');
        });

        $activities = $activities->map(function ($item, $key) {
            return $item->groupBy('subject_type');
        });
        // return $activities;
        return view('admin.reports.partials._activity_log', compact('activities'));

        return response()->json(['data' => $res]);

    }

    public function original_agentListingReport(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
        ]);

        $agent = User::find($request->user_id);

        $notAdmin = $agent->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds($agent->id);
        }

        $agents = User::where('status', true)->whereHas("roles", function ($q) {
            $q->whereIn("name", ["Agent", "Sales Director", "Team Leader", "Line Manager"]);
        })->when($notAdmin, fn($q) => $q->whereIn("id", $childrenIds))->get();

        $managers_locations = UserLocation::where('user_id', $request->user_id)->where('parentable_type', 'App\Models\Community')->get();

        $agentIds = $agents->pluck('id')->toArray();
        $locationIds = $managers_locations->pluck('parentable_id')->toArray();

        $agentLocationCounts = [];

        foreach ($agentIds as $agentId) {
            foreach ($locationIds as $locationId) {
                $agent = User::find($agentId);
                $location = Community::find($locationId);
                $count = Property::where('is_live', 1)->where('agent_id', $agentId)
                    ->where('community_id', $locationId)
                    ->count();
                $agentLocationCounts[$agent->name][$location->name] = $count;
            }

            $notcount = Property::where('is_live', 1)->where('agent_id', $agentId)
                ->whereNotIn('community_id', $locationIds)
                ->count();
            $agentLocationCounts[$agent->name]['Others'] = $notcount;
        }

        $locations = Community::whereIn('id', $managers_locations->pluck('parentable_id')->toArray())->get();

        return view('admin.reports.partials._area_wise_listing_report', [
            'agentLocationCounts' => $agentLocationCounts,
            'agents' => $agents,
            'locations' => $locations,
        ]);
    }


    public function agentListingReport(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'portal_id' => 'nullable',
        ]);

        $agent = User::find($request->user_id);

        $notAdmin = $agent->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);

        $agentsQuery = User::where('status', true)
            ->whereHas("roles", function ($q) {
                $q->whereIn("name", ["Agent", "Sales Director", "Team Leader", "Line Manager"]);
            });

        if ($notAdmin) {
            $childrenIds = getUserChildrenIdsByID($agent->id);
            $agentsQuery->whereIn("id", $childrenIds);
        }

        $agents = $agentsQuery->get();

        $managers_locations = UserLocation::where('user_id', $request->user_id)
            ->where('parentable_type', 'App\Models\Community')
            ->get();

        if ($agent->hasRole('Agent')) {
            $managers_locations = UserLocation::where('user_id', $agent->parent_id)->where('parentable_type', 'App\Models\Community')->get();
        }

        $agentIds = $agents->pluck('id')->toArray();
        $locationIds = $managers_locations->pluck('parentable_id')->toArray();

        $agentLocationCounts = [];

        $users = User::whereIn('id', $agentIds)->get();
        $locations = Community::whereIn('id', $locationIds)->get();

        foreach ($users as $user) {
            foreach ($locations as $location) {
                $query = Property::where('agent_id', $user->id)->where('community_id', $location->id);
                if($request->portal){
                    $query->whereHas('portals', function ($q) use ($request) {
                        $q->where('portal_id', $request->portal);
                    });
                }

                if ($request->status) {
                    $query->where('publish_status', $request->status);
                }
                if ($request->is_live) {
                    $query->where('is_live', $request->is_live);
                }

                $count = $query->count();
                $location_name = $location->id.'_'.$location->name;
                $agentLocationCounts[$user->name][$location_name] = $count;

            }

            $not_query = Property::where('agent_id', $user->id)
                ->whereNotIn('community_id', $locationIds);

                if ($request->status) {
                    $not_query->where('publish_status', $request->status);
                }
                if ($request->is_live) {
                    $not_query->where('is_live', 1);
                }
                if($request->portal){
                    $not_query->whereHas('portals', function ($q) use ($request) {
                        $q->where('portal_id', $request->portal);
                    });
                }

                $notcount =  $not_query->count();

            $agentLocationCounts[$user->name]['Others'] = $notcount;
        }
        // get no of properties for each portal
        // $portalWiseCounts = [];
        // $portalWiseCounts = Portal::where('status', true)
        //     ->withCount(['properties' => function ($query) use ($notAdmin, $childrenIds, $request) {
        //         $query->where('is_live', '1');

        //         if ($request->status) {
        //             $not_query->where('publish_status', $request->status);
        //         }

        //         if ($notAdmin) {
        //             $query->whereIn('agent_id', $childrenIds);
        //         }
        //     }])
        //     ->get(['id', 'name', 'thumbnail']);


            // dd($agentLocationCounts,$agents, $locations,);

        return view('admin.reports.partials._area_wise_listing_report', [
            'agentLocationCounts' => $agentLocationCounts,
            'agents' => $agents,
            'locations' => $locations,
            'status' => $request->status,
            'portals' => Portal::where('status', 1)->get(),
            'selected_portal' => $request->portal??null,
            // 'portalWiseCounts' => $portalWiseCounts,
        ]);
    }

    public function __agentListingReport(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'portal_id' => 'nullable',
        ]);

        $agent = User::find($request->user_id);

        $agentRoleNames = ["Agent", "Sales Director", "Team Leader", "Line Manager"];
        $notAdmin = $agent->hasRole($agentRoleNames);
        $childrenIds = [];

        if ($notAdmin) {
            $childrenIds = getUserChildrenIdsByID($agent->id);
        }

        $agentsQuery = User::where('status', true)
            ->whereHas("roles", function ($q) use ($agentRoleNames) {
                $q->whereIn("name", $agentRoleNames);
            });

        if ($notAdmin) {
            $agentsQuery->whereIn("id", $childrenIds);
        }

        $agents = $agentsQuery->get();

        $managers_locations = UserLocation::where('user_id', $request->user_id)
            ->where('parentable_type', 'App\Models\Community')
            ->get();

        if ($agent->hasRole('Agent')) {
            $managers_locations = UserLocation::where('user_id', $agent->parent_id)
                ->where('parentable_type', 'App\Models\Community')
                ->get();
        }

        $agentIds = $agents->pluck('id')->toArray();
        $locationIds = $managers_locations->pluck('parentable_id')->toArray();

        $agentLocationCounts = [];

        $users = User::whereIn('id', $agentIds)->get();
        $locations = Community::whereIn('id', $locationIds)->get();

        foreach ($users as $user) {
            foreach ($locations as $location) {
                $query = Property::where('agent_id', $user->id)
                    ->where('community_id', $location->id);

                if ($request->portal) {
                    $query->whereHas('portals', function ($q) use ($request) {
                        $q->where('portal_id', $request->portal);
                    });
                }

                if ($request->status) {
                    $query->where('publish_status', $request->status);
                }
                if ($request->is_live) {
                    $query->where('is_live', $request->is_live);
                }

                $count = $query->count();
                $location_name = $location->id . '_' . $location->name;
                $agentLocationCounts[$user->name][$location_name] = $count;
            }

            $not_query = Property::where('agent_id', $user->id)
                ->whereNotIn('community_id', $locationIds);

            if ($request->status) {
                $not_query->where('publish_status', $request->status);
            }
            if ($request->is_live) {
                $not_query->where('is_live', 1);
            }
            if ($request->portal) {
                $not_query->whereHas('portals', function ($q) use ($request) {
                    $q->where('portal_id', $request->portal);
                });
            }

            $notcount = $not_query->count();

            $agentLocationCounts[$user->name]['Others'] = $notcount;
        }

        return view('admin.reports.partials._area_wise_listing_report', [
            'agentLocationCounts' => $agentLocationCounts,
            'agents' => $agents,
            'locations' => $locations,
            'status' => $request->status,
            'portals' => Portal::where('status', 1)->get(),
            'selected_portal' => $request->portal ?? null,
        ]);
    }


    public function agentListingReportSubCommunity(Request $request)
    {
        $request->validate([
            'agent_id' => 'required',
            'portal_id' => 'nullable',
            'location_name' => 'required',
        ]);

        $agent = \App\Models\User::find($request->agent_id);

        $notAdmin = $agent->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);

        $agentsQuery = \App\Models\User::where('status', true)
            ->whereHas("roles", function ($q) {
                $q->whereIn("name", ["Agent", "Sales Director", "Team Leader", "Line Manager"]);
            });

        if ($notAdmin) {
            $childrenIds = getUserChildrenIdsByID($agent->id);
            $agentsQuery->whereIn("id", $childrenIds);
        }

        $agents = $agentsQuery->get();
        $agentIds = $agents->pluck('id')->toArray();

        $community = Community::where('name', $request->location_name)->orWhere('alternate_name', $request->location_name)->first();

        $managers_locations = SubCommunity::where('community_id', $community->id)->get();

        // if ($agent->hasRole('Agent')) {
        //     $community = Community::where('name', $request->location_name)->orWhere('alternate_name', $request->location_name)->first();
        //     $managers_locations = SubCommunity::where('community_id', $community->id)->get();
        //     $community_property = CommunityProperty::where('community_id', $community->id)->get();

        // }
        $agentLocationCounts = [];
        $agentLocationCountsSub = [];

        $locationIds = $managers_locations->pluck('id')->toArray();


        $users = User::whereIn('id', $agentIds)->get();
        $locations = SubCommunity::whereIn('id', $locationIds)->get();
        $community_property = CommunityProperty::where('community_id', $community->id)->get();


        $counter = 0;


        foreach ($users as $user) {
            foreach ($locations as $location) {
                $query = Property::where('agent_id', $user->id)->where('community_id',$community->id)
                ->where('sub_community_id', $location->id)->whereNull('community_property_id');
                if($request->portal){
                    $query->whereHas('portals', function ($q) use ($request) {
                        $q->where('portal_id', $request->portal);
                    });
                }

                if ($request->status) {
                    $query->where('publish_status', $request->status);
                }
                if ($request->is_live) {
                    $query->where('is_live', $request->is_live);
                }
                $count = $query->count();
                $counter += $count;
                $location_name = $location->id.'_'.$location->name;

                $agentLocationCounts[$user->name][$location_name] = $count;
            }
        }
        $counter_2  = 0;
        foreach ($users as $user) {
            foreach ($community_property as $location_cp) {

                $query = Property::where('agent_id', $user->id)->where('community_id', $community->id)->where('community_property_id', $location_cp->id);
                if($request->portal){
                    $query->whereHas('portals', function ($q) use ($request) {
                        $q->where('portal_id', $request->portal);
                    });
                }

                if ($request->status) {
                    $query->where('publish_status', $request->status);
                }
                if ($request->is_live) {
                    $query->where('is_live', $request->is_live);
                }

                $count = $query->count();
                $counter_2 += $count;
                $location_cp_name = $location_cp->id.'_'.$location_cp->name;
                $agentLocationCountsSub[$user->name][$location_cp_name] = $count;




            }
        }

        // dd($agentLocationCountsSub);
        return view('admin.reports.partials._area_wise_listing_report_sub_community', [
            'agentLocationCounts' => $agentLocationCounts,
            'agentLocationCountsSub' => $agentLocationCountsSub,
            'agents' => $agents,
            'locations' => $locations,
            'community_property' => $community_property,
            'status' => $request->status,
            'portals' => Portal::where('status', 1)->get(),
            'selected_portal' => $request->portal??null,
            'location_name' => $request->location_name,
        ]);
    }



    public function __________agentListingReport(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
        ]);

        $agent = User::find($request->user_id);

        $notAdmin = $agent->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds($agent->id);
        }

        $agents = User::where('status', true)->whereHas("roles", function ($q) {
            $q->whereIn("name", ["Agent", "Sales Director", "Team Leader", "Line Manager"]);
        })->when($notAdmin, fn($q) => $q->whereIn("id", $childrenIds))->get();

        $managers_locations = UserLocation::where('user_id', $request->user_id)->where('parentable_type', 'App\Models\Community')->get();

        if ($agent->hasRole('Agent')) {
            // dd($agent->parent_id);
            $managers_locations = UserLocation::where('user_id', $agent->parent_id)->where('parentable_type', 'App\Models\Community')->get();
        }

        $agentIds = $agents->pluck('id')->toArray();
        $locationIds = $managers_locations->pluck('parentable_id')->toArray();

        $agentLocationCounts = [];
        $agent = User::get();
        $location = Community::get();
        foreach ($agentIds as $agentId) {
            foreach ($locationIds as $locationId) {
                $agent = $agent->where('id', $agentId);
                $location = $location->where('id', $locationId);
                $count = Property::where('is_live', 1)->where('agent_id', $agentId)
                    ->where('community_id', $locationId)
                    ->count();
                $agentLocationCounts[$agent->name][$location->name] = $count;
            }

            $notcount = Property::where('is_live', 1)->where('agent_id', $agentId)
                ->whereNotIn('community_id', $locationIds)
                ->count();
            $agentLocationCounts[$agent->name]['Others'] = $notcount;
        }

        $locations = Community::whereIn('id', $managers_locations->pluck('parentable_id')->toArray())->get();
        // add others location to locations collection to show in report
        // Create a temporary entry for "Others"

        return view('admin.reports.partials._area_wise_listing_report', [
            'agentLocationCounts' => $agentLocationCounts,
            'agents' => $agents,
            'locations' => $locations,
        ]);
    }


}
