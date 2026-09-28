<?php

namespace App\Http\Controllers\Admin\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadStatus;
use App\Models\ListingLead;
use App\Models\NotificationUser;
use App\Models\Portal;
use App\Models\Property;
use App\Models\PropertyMeta;
use App\Models\Source;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PrimaryDashboardController extends Controller
{
    public function dashboardPieData(Request $request)
    {
        //abort_if(Gate::denies("Dashboard"), 401);
        $dates = [];

        $notAdmin = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        $portals = Portal::where('status', true)->pluck('name', 'id')->toArray();
        $portal_ids = array_keys($portals);
        //$portal_name = array_values($portals);
        //calculation for rent
        $rent_property = Property::whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary'])->select('property_portals.portal_id', DB::raw('count(*) as total'))
            ->leftjoin('property_portals', 'properties.id', '=', 'property_portals.property_id')
            ->groupBy('property_portals.portal_id')
            ->whereIn('properties.for', ['Residential for rent', 'Commercial for rent'])
            ->where('properties.publish_status', 'PUBLISHED')
            ->whereIn('property_portals.portal_id', $portal_ids)
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->get();
        $rentValues = [];
        foreach ($rent_property as $key => $value) {
            $rentValues[$value->portal_id] = $value->total;
        }
        $sale_property = Property::whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary'])->select('property_portals.portal_id', DB::raw('count(*) as total'))
            ->leftjoin('property_portals', 'properties.id', '=', 'property_portals.property_id')
            ->groupBy('property_portals.portal_id')
            ->whereIn('property_portals.portal_id', $portal_ids)
            ->where('properties.publish_status', 'PUBLISHED')
            ->whereIn('properties.for', ['Residential for sale', 'Commercial for sale'])
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })
            ->when(!empty($dates), function ($q) use ($dates) {
                $q->whereDate('properties.created_at', '>=', $dates['startDate'])->whereDate('properties.created_at', '<=', $dates['endDate']);
            })->get();

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

    public function propertyFinderData(Request $request)
    {
        $rankThresholds = [40, 50, 60, 70, 80, 90, 100];
        $pf_rank = [];
        $dates = [];
        $portal = Portal::where('name', 'Property Finder')->first();
        $source = Source::where('name', 'Property Finder')->first();
        $user_id = ($request->has('user_id') && $request->user_id != '') ? $request->user_id : '';
        if ($user_id != '') {
            $user = User::find($user_id);
            $dates = $request->dates;
        } else {
            $user = auth()->user();
        }

        $notAdmin = $user->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
//
        if ($notAdmin) {
            $user_id = ($request->has('user_id') && $request->user_id != '') ? $request->user_id : auth()->id();
            $childrenIds = getUserChildrenIdsOnly($user_id);
            $childrenIds[] = $user_id;

        }

        foreach ($rankThresholds as $threshold) {
            $upper = $threshold + 10;
            $count = Property::whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary'])
                ->where('region_id', 1)
                ->whereHas('propertyMeta', function ($query) use ($threshold, $upper) {
                    if ($threshold == 100) {
                        $query->where('meta_key', 'pf_rank')
                            ->where('meta_value', '=', $threshold);
                    } else {
                        $query->where('meta_key', 'pf_rank')
                            ->where('meta_value', '>', $threshold)->where('meta_value', '<', $upper);
                    }

                })->whereHas('portals', function ($q) use ($portal) {
                $q->where('portal_id', $portal->id);
            })->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->when(!empty($dates), function ($q) use ($dates) {
                $q->whereDate('properties.created_at', '>=', $dates['startDate'])->whereDate('properties.created_at', '<=', $dates['endDate']);
            })->where('is_live', '1')->count();
            if ($threshold == 100) {
                $pf_rank[$threshold] = $count;
            } else {
                $pf_rank[$threshold . '-' . $upper] = $count;
            }

        }
        $data['pf_rank'] = $pf_rank;
        $verified_count = Property::whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary'])
            ->whereHas('propertyMeta', function ($query) {
                $query->where('meta_key', 'pf_verified')
                    ->where('meta_value', '1');
            })->whereHas('portals', function ($q) use ($portal) {
            $q->where('portal_id', $portal->id);
        })->where('is_live', '1')->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->when(!empty($dates), function ($q) use ($dates) {
            $q->whereDate('properties.created_at', '>=', $dates['startDate'])->whereDate('properties.created_at', '<=', $dates['endDate']);
        })->count();

        $data['online'] = Property::whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary'])
            ->where('region_id', 1)
            ->whereHas('propertyMeta', function ($query) {
                $query->where('meta_key', 'pf_status')
                    ->where('meta_value', 'Online');
            })->whereHas('portals', function ($q) use ($portal) {
            $q->where('portal_id', $portal->id);
        })->where('is_live', '1')->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->when(!empty($dates), function ($q) use ($dates) {
            $q->whereDate('properties.created_at', '>=', $dates['startDate'])->whereDate('properties.created_at', '<=', $dates['endDate']);
        })->count();
        $data['offline'] = Property::whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary'])
            ->where('region_id', 1)
            ->whereHas('propertyMeta', function ($query) {
                $query->where('meta_key', 'pf_status')
                    ->where('meta_value', 'Offline');
            })->whereHas('portals', function ($q) use ($portal) {
            $q->where('portal_id', $portal->id);
        })->where('is_live', '1')->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->when(!empty($dates), function ($q) use ($dates) {
            $q->whereDate('properties.created_at', '>=', $dates['startDate'])->whereDate('properties.created_at', '<=', $dates['endDate']);
        })->count();

        //calculate average of pf_rank
        $pf_avg_rank = Property::whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary'])
            ->where('region_id', 1)
            ->join('property_meta', function ($join) {
                $join->on('properties.id', '=', 'property_meta.property_id')
                    ->where('property_meta.meta_key', '=', 'pf_rank');
            })->whereHas('portals', function ($q) use ($portal) {
            $q->where('portal_id', $portal->id);
        })->where('is_live', '1')->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->when(!empty($dates), function ($q) use ($dates) {
            $q->whereDate('properties.created_at', '>=', $dates['startDate'])->whereDate('properties.created_at', '<=', $dates['endDate']);
        })->avg('property_meta.meta_value');
        $data['avg_pf_rank'] = round($pf_avg_rank);

        $data['pf_verified'] = $verified_count;
        $data['portal_count'] = Property::whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary'])
            ->where('region_id', 1)
            ->whereHas('portals', function ($q) use ($portal) {
                $q->where('portal_id', $portal->id);
            })->where('is_live', '1')
            ->when(!empty($dates), function ($q) use ($dates) {
                $q->whereDate('properties.created_at', '>=', $dates['startDate'])->whereDate('properties.created_at', '<=', $dates['endDate']);
            })
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->count();

        $levels = ['Standard', 'Featured', 'Premium'];
        $pf_level = [];
        $total_count = 0;
        foreach ($levels as $level) {
            $count = Property::whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary'])
                ->where('region_id', 1)
                ->whereHas('propertyMeta', function ($query) use ($level) {
                    $query->where('meta_key', 'pf_level')
                        ->where('meta_value', $level);
                })->whereHas('portals', function ($q) use ($portal) {
                $q->where('portal_id', $portal->id);
            })->where('is_live', '1')
                ->when($notAdmin, function ($q) use ($childrenIds) {
                    $q->whereIn("agent_id", $childrenIds);
                })->when(!empty($dates), function ($q) use ($dates) {
                $q->whereDate('properties.created_at', '>=', $dates['startDate'])->whereDate('properties.created_at', '<=', $dates['endDate']);
            })->count();
            $pf_level[$level] = $count;
            $total_count += $count;
            // $pf_level[$level] = PropertyMeta::where('meta_key', 'pf_level')->where('meta_value', $level)->count();

        }
        $pf_level['Standard'] = $data['portal_count'] - $total_count + $pf_level['Standard'] ?? 0;

        $data['level'] = $pf_level;
//        $averageDifference = Shortlink::where('source_id', $source->id)->whereNotNull('opted_in')
//            ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, created_at, opted_in)) AS average_difference')
//            ->get();
        $averageDifference = Lead::whereHas('property', function ($q) {
            $q->whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary']);
        })->whereIn('agent_id', $childrenIds)
            ->join('shortlinks', 'leads.id', '=', 'shortlinks.lead_id')
            ->whereNotNull('shortlinks.opted_in')
            ->where('shortlinks.source_id', $source->id)
            ->select(DB::raw('AVG(TIMESTAMPDIFF(SECOND, shortlinks.created_at, shortlinks.opted_in)) as average_diff'))
            ->value('average_diff');

        $data['whatsapp_avg_res'] = $averageDifference ?? 0;

        return response()->json(['status' => '1', 'data' => $data]);
    }

    public function bayutData(Request $request)
    {
        $rankThresholds = [40, 50, 60, 70, 80, 90, 100];
        $pf_rank = [];
        $dates = [];
        $portal = Portal::where('name', 'Bayut')->first();
        $user_id = ($request->has('user_id') && $request->user_id != '') ? $request->user_id : auth()->id();
        if ($user_id != '') {
            $user = User::find($user_id);
            $dates = $request->dates;
        } else {
            $user = auth()->user();
        }

        $notAdmin = $user->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {

            // $childrenIds = getUserChildrenIds($user_id);
            $childrenIds = getUserChildrenIdsOnly($user_id);
            $childrenIds[] = $user_id;
        }

        foreach ($rankThresholds as $threshold) {
            $upper = $threshold + 10;
            $count = Property::whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary'])->whereHas('propertyMeta', function ($query) use ($threshold, $upper) {
                if ($threshold == 100) {
                    $query->where('meta_key', 'bayut_rank')
                        ->where('meta_value', '=', $threshold);
                } else {
                    $query->where('meta_key', 'bayut_rank')
                        ->where('meta_value', '=>', $threshold)->where('meta_value', '<=', $upper);
                }

            })->whereHas('portals', function ($q) use ($portal) {
                $q->where('portal_id', $portal->id);
            })->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->when(!empty($dates), function ($q) use ($dates) {
                $q->whereDate('properties.created_at', '>=', $dates['startDate'])->whereDate('properties.created_at', '<=', $dates['endDate']);
            })->where('is_live', '1')->count();
            if ($threshold == 100) {
                $pf_rank[$threshold] = $count;
            } else {
                $pf_rank[$threshold . '-' . $upper] = $count;
            }

        }
        $data['ranks'] = $pf_rank;
        $verified_count = Property::whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary'])->whereHas('propertyMeta', function ($query) {
            $query->where('meta_key', 'bayut_review_status')
                ->where('meta_value', 'Approved');
        })->whereHas('portals', function ($q) use ($portal) {
            $q->where('portal_id', $portal->id);
        })->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->when(!empty($dates), function ($q) use ($dates) {
            $q->whereDate('properties.created_at', '>=', $dates['startDate'])->whereDate('properties.created_at', '<=', $dates['endDate']);
        })->where('is_live', '1')->count();

        $data['trucheck'] = Property::whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary'])->whereHas('propertyMeta', function ($query) {
            $query->where('meta_key', 'bayut_trucheck_status')
                ->where('meta_value', 'Truchecked');
        })->whereHas('portals', function ($q) use ($portal) {
            $q->where('portal_id', $portal->id);
        })->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->when(!empty($dates), function ($q) use ($dates) {
            $q->whereDate('properties.created_at', '>=', $dates['startDate'])->whereDate('properties.created_at', '<=', $dates['endDate']);
        })->where('is_live', '1')->count() ?? 0;

        $data['checked'] = Property::whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary'])->whereHas('propertyMeta', function ($query) {
            $query->where('meta_key', 'bayut_trucheck_status')
                ->where('meta_value', 'checked');
        })->whereHas('portals', function ($q) use ($portal) {
            $q->where('portal_id', $portal->id);
        })->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->when(!empty($dates), function ($q) use ($dates) {
            $q->whereDate('properties.created_at', '>=', $dates['startDate'])->whereDate('properties.created_at', '<=', $dates['endDate']);
        })->where('is_live', '1')->count() ?? 0;

        //calculate average of pf_rank
        $avg_rank = Property::whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary'])->join('property_meta', function ($join) {
            $join->on('properties.id', '=', 'property_meta.property_id')
                ->where('property_meta.meta_key', '=', 'bayut_rank');
        })->whereHas('portals', function ($q) use ($portal) {
            $q->where('portal_id', $portal->id);
        })->where('is_live', '1')->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->when(!empty($dates), function ($q) use ($dates) {
            $q->whereDate('properties.created_at', '>=', $dates['startDate'])->whereDate('properties.created_at', '<=', $dates['endDate']);
        })->avg('property_meta.meta_value');
        $data['avg_rank'] = round($avg_rank);

        $data['verified'] = $verified_count;

        $data['portal_count'] = Property::whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary'])->whereHas('portals', function ($q) use ($portal) {
            $q->where('portal_id', $portal->id);
        })->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->when(!empty($dates), function ($q) use ($dates) {
            $q->whereDate('properties.created_at', '>=', $dates['startDate'])->whereDate('properties.created_at', '<=', $dates['endDate']);
        })->where('is_live', '1')->count();

        $levels = ['Standard', 'Signature', 'Hot'];
        $rank = [];
        $total_count = 0;
        foreach ($levels as $level) {
            $count = Property::whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary'])->whereHas('propertyMeta', function ($query) use ($level) {
                $query->where('meta_key', 'bayut_level')
                    ->where('meta_value', $level);
            })->whereHas('portals', function ($q) use ($portal) {
                $q->where('portal_id', $portal->id);
            })->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->when(!empty($dates), function ($q) use ($dates) {
                $q->whereDate('properties.created_at', '>=', $dates['startDate'])->whereDate('properties.created_at', '<=', $dates['endDate']);
            })->where('is_live', '1')->count();
            $rank[$level] = $count;
            $total_count += $count;
            // $pf_level[$level] = PropertyMeta::where('meta_key', 'pf_level')->where('meta_value', $level)->count();

        }
        $rank['Standard'] = $data['portal_count'] - $total_count ?? 0; //because we don't have data for bayut_level = Standard

        $data['level'] = $rank;

        return response()->json(['status' => '1', 'data' => $data]);
    }

    public function dubbizleData(Request $request)
    {
        $rankThresholds = [40, 50, 60, 70, 80, 90, 100];
        $rank = [];
        $dates = [];
        $portal = Portal::where('name', 'Dubizzle')->first();
        $user_id = ($request->has('user_id') && $request->user_id != '') ? $request->user_id : auth()->id();
        if ($user_id != '') {
            $user = User::find($user_id);
            $dates = $request->dates;
        } else {
            $user = auth()->user();
        }
        $notAdmin = $user->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            //$childrenIds = getUserChildrenIds($user_id);
            $childrenIds = getUserChildrenIdsOnly($user_id);
            $childrenIds[] = $user_id;
        }
        foreach ($rankThresholds as $threshold) {
            $upper = $threshold + 10;
            $count = Property::whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary'])->whereHas('propertyMeta', function ($query) use ($threshold, $upper) {
                if ($threshold == 100) {
                    $query->where('meta_key', 'dubizzle_rank')
                        ->where('meta_value', '=', $threshold);
                } else {
                    $query->where('meta_key', 'dubizzle_rank')
                        ->where('meta_value', '>', $threshold)->where('meta_value', '<', $upper);
                }

            })->whereHas('portals', function ($q) use ($portal) {
                $q->where('portal_id', $portal->id);
            })->where('is_live', '1')->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->count();
            if ($threshold == 100) {
                $rank[$threshold] = $count;
            } else {
                $rank[$threshold . '-' . $upper] = $count;
            }

        }
        $data['ranks'] = $rank;
//        $verified_count = Property::whereHas('propertyMeta', function ($query) {
//            $query->where('meta_key', 'bayut_review_status')
//                ->where('meta_value', 'Approved');
//        })->whereHas('portals',function ($q) use($portal){
//            $q->where('portal_id',$portal->id);
//        })->where('publish_status','PUBLISHED')->when($notAdmin, function ($q) use ($childrenIds) {
//            $q->whereIn("agent_id", $childrenIds);
//        })->count();

        $data['trucheck'] = Property::whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary'])->whereHas('propertyMeta', function ($query) {
            $query->where('meta_key', 'dubizzle_trucheck_status')
                ->where('meta_value', 'checked');
        })->whereHas('portals', function ($q) use ($portal) {
            $q->where('portal_id', $portal->id);
        })->where('is_live', '1')->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count() ?? 0;

        $data['trucheck'] = Property::whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary'])->whereHas('propertyMeta', function ($query) {
            $query->where('meta_key', 'dubizzle_trucheck_status')
                ->where('meta_value', 'Truchecked');
        })->whereHas('portals', function ($q) use ($portal) {
            $q->where('portal_id', $portal->id);
        })->where('is_live', '1')->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count() ?? 0;

        $data['checked'] = Property::whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary'])->whereHas('propertyMeta', function ($query) {
            $query->where('meta_key', 'dubizzle_trucheck_status')
                ->where('meta_value', 'checked');
        })->whereHas('portals', function ($q) use ($portal) {
            $q->where('portal_id', $portal->id);
        })->where('is_live', '1')->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count() ?? 0;

        //calculate average of rank
        $avg_rank = Property::whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary'])->join('property_meta', function ($join) {
            $join->on('properties.id', '=', 'property_meta.property_id')
                ->where('property_meta.meta_key', '=', 'dubizzle_rank');
        })->whereHas('portals', function ($q) use ($portal) {
            $q->where('portal_id', $portal->id);
        })->where('is_live', '1')->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->avg('property_meta.meta_value');
        $data['avg_rank'] = round($avg_rank);

        //  $data['verified'] =  $verified_count;
        $data['portal_count'] = Property::whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary'])->whereHas('portals', function ($q) use ($portal) {
            $q->where('portal_id', $portal->id);
        })->where('is_live', '1')->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count();
        $levels = ['Premium', 'Featured','Standard'];
        $rank = [];
        $total_count = 0;
        foreach ($levels as $level) {

            $listingGGG = Property::whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary'])->whereHas('propertyMeta', function ($query) use ($level) {
                $query->where('meta_key', 'dubizzle_level')
                    ->where('meta_value', $level);
            })->whereHas('portals', function ($q) use ($portal) {
                $q->where('portal_id', $portal->id);
            })->where('is_live', '1')->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->get();
            info($listingGGG);
            $count = Property::whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary'])->whereHas('propertyMeta', function ($query) use ($level) {
                $query->where('meta_key', 'dubizzle_level')
                    ->where('meta_value', $level);
            })->whereHas('portals', function ($q) use ($portal) {
                $q->where('portal_id', $portal->id);
            })->where('is_live', '1')->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->count();
            $rank[$level] = $count;
            $total_count += $count;
            // $pf_level[$level] = PropertyMeta::where('meta_key', 'pf_level')->where('meta_value', $level)->count();

        }
        $rank['Standard'] = $data['portal_count'] - $total_count + $rank['Standard'] ?? 0; //because we don't have data for bayut_level = Standard

        $data['level'] = $rank;
        return response()->json(['status' => '1', 'data' => $data]);
    }

    public function getNotification()
    {
        $data = NotificationUser::where('user_id', auth()->user()->id)->where('read', 0)->limit(5)->orderBy('id', 'DESC')->with('notification')->get();
        $count = NotificationUser::where('user_id', auth()->user()->id)->where('read', 0)->count();
        return response()->json(['status' => '1', 'data' => $data, 'count' => $count]);
    }

    public function dashboardDecider()
    {
        if (auth()->user()->hasRole('Admin')) {
            return $this->adminDashboard();
        } else if (auth()->user()->hasRole('Agent')) {
            return $this->agentDashboard();
        } else {
            return $this->agentDashboard();
        }
    }

    public function agentDashboard()
    {
        $properties = Property::whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary'])->where('agent_id', auth()->user()->id)
            ->with('propertyMeta') // Eager load the propertyMeta relationship
            ->withCount(['propertyMeta as dubizzle_status_check_count' => function ($query) {
                $query->where('meta_key', 'dubizzle_trucheck_status');
            }])->get();
        $properties = Property::whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary'])->where('agent_id', auth()->user()->id)
            ->with('propertyMeta') // Eager load the propertyMeta relationship
            ->withCount(['propertyMeta as bayut_trucheck_status_count' => function ($query) {
                $query->where('meta_key', 'bayut_trucheck_status');
            }])->get();

        $trueCheckCount = 0;
        $checkCount = 0;
        foreach ($properties as $property) {
            foreach ($property->propertyMeta as $meta) {
                if ($meta->meta_value == 'truchecked') {
                    $trueCheckCount++;
                }
                if ($meta->meta_value == 'checked') {
                    $checkCount++;
                }
            }
        }

        $dubizzle_chart = [

            "datasets" => ["dubizzle_pie_chart" =>
                [
                    "label" => ["True Check", "Check"],
                    "data" => [$trueCheckCount, $checkCount],
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

        $data['meta']['dubizzle'] = $dubizzle_chart;

        return response()->json(['status' => '1', 'data' => $data, 'count' => $properties->count()]);
    }

    public function propertiesNotUpdatedIn($agent_id)
    {
        if ($agent_id != '') {
            $user = User::find($agent_id);
        } else {
            $user = auth()->user();
        }
        $notAdmin = $user->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIdsOnly($user->id);
            $childrenIds[] = $user->id;
        }

        $counts = [];
        $today = Carbon::now();

        $intervals = [
            ['days', 7],
            ['days', 14],
            ['days', 30],
            ['months', 3],
        ];
        $totalProperties = Property::whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary'])->where('publish_status', 'PUBLISHED')->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count();
        foreach ($intervals as $interval) {
            $count = Property::where('updated_at', '<', $today->sub(...$interval))
                ->where('publish_status', 'PUBLISHED')->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->count();

            $counts[$interval[1] . ' ' . $interval[0]] = $count;
        }
        return [
            'counts' => $counts,
            'totalProperties' => $totalProperties,
        ];

    }

    public function listingOverview($agent_id)
    {
        if ($agent_id != '') {
            $user = User::find($agent_id);
        } else {
            $user = auth()->user();
        }

        $notAdmin = $user->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIdsOnly($user->id);
            $childrenIds[] = $user->id;
        }

        $statusCounts = Property::whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary'])->select('publish_status', DB::raw('COUNT(*) as count'))
            ->whereIn('publish_status', ['PUBLISHED', 'POCKET', 'UNPUBLISHED', 'DRAFT'])
            ->groupBy('publish_status')
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })
            ->get(['publish_status', DB::raw('COUNT(*) as count')]);
        return $statusCounts;
    }

    public function propertiesPublishedIn($agent_id)
    {
        if ($agent_id != '') {
            $user = User::find($agent_id);
        } else {
            $user = auth()->user();
        }
        $notAdmin = $user->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIdsOnly($user->id);
            $childrenIds[] = $user->id;
        }

        $counts = [];
        $today = Carbon::now();

        $intervals = [
            ['days', 7],
            ['days', 14],
            ['days', 30],
            ['months', 3],
        ];
        $totalProperties = Property::whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary'])->where('publish_status', 'PUBLISHED')->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count();

        $now = Carbon::now();
        $sevenDaysAgo = Carbon::now()->subDays(7);
        $fourteenDaysAgo = Carbon::now()->subDays(14); // Resets to 14 days ago
        $thirtyDaysAgo = Carbon::now()->subDays(30); // Resets to 30 days ago
        $threeMonthsAgo = Carbon::now()->subMonths(3);

        $counts = [
            'In last 7 days' => Property::whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary'])->where('published_date', '>=', $sevenDaysAgo)->where('publish_status', 'PUBLISHED')->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->count(),
            'In last 14 days' => Property::whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary'])->whereBetween('published_date', [$fourteenDaysAgo, $now])->where('publish_status', 'PUBLISHED')->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->count(),
            'In last 30 days' => Property::whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary'])->whereBetween('published_date', [$thirtyDaysAgo, $now])->where('publish_status', 'PUBLISHED')->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->count(),
            'Last 3 months' => Property::whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary'])->where('published_date', '>=', $threeMonthsAgo)->where('publish_status', 'PUBLISHED')->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->count(),
        ];

        return [
            'counts' => $counts,
            'totalProperties' => $totalProperties,
        ];

    }

    public function recentLeads($agent_id = '')
    {
        if ($agent_id != '') {
            $user = User::find($agent_id);
        } else {
            $user = auth()->user();
        }
        $notAdmin = $user->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIdsOnly($user->id);
            $childrenIds[] = $user->id;
        }

        $totalLeads = Lead::whereHas('property', function ($q) {
            $q->whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary']);
        })->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count();

        $now = Carbon::now();
        $sevenDaysAgo = Carbon::now()->subDays(7);
        $fourteenDaysAgo = Carbon::now()->subDays(14); // Resets to 14 days ago
        $thirtyDaysAgo = Carbon::now()->subDays(30); // Resets to 30 days ago
        $threeMonthsAgo = Carbon::now()->subMonths(3);

        $counts = [
            'In last 7 days' => Lead::whereHas('property', function ($q) {
                $q->whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary']);
            })->where('created_at', '>=', $sevenDaysAgo)->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->count(),
            'In last 14 days' => Lead::whereHas('property', function ($q) {
                $q->whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary']);
            })->whereBetween('created_at', [$fourteenDaysAgo, $now])->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->count(),
            'In last 30 days' => Lead::whereHas('property', function ($q) {
                $q->whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary']);
            })->whereBetween('created_at', [$thirtyDaysAgo, $now])->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->count(),
            'Last 3 months' => Lead::whereHas('property', function ($q) {
                $q->whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary']);
            })->where('created_at', '>=', $threeMonthsAgo)->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->count(),
        ];
        $data = [
            'counts' => $counts,
            'totalLeads' => $totalLeads,
        ];
        return response()->json(['status' => '1', 'data' => $data]);

    }
    public function recentListingLeads($agent_id = '')
    {
        if ($agent_id != '') {
            $user = User::find($agent_id);
        } else {
            $user = auth()->user();
        }
        $notAdmin = $user->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIdsOnly($user->id);
            $childrenIds[] = $user->id;
        }

        $totalLeads = ListingLead::whereHas('property', function ($q) {
            $q->whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary']);
        })->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count();

        $now = Carbon::now();
        $sevenDaysAgo = Carbon::now()->subDays(7);
        $fourteenDaysAgo = Carbon::now()->subDays(14); // Resets to 14 days ago
        $thirtyDaysAgo = Carbon::now()->subDays(30); // Resets to 30 days ago
        $threeMonthsAgo = Carbon::now()->subMonths(3);

        $counts = [
            'In last 7 days' => ListingLead::whereHas('property', function ($q) {
                $q->whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary']);
            })->where('created_at', '>=', $sevenDaysAgo)->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->count(),
            'In last 14 days' => ListingLead::whereHas('property', function ($q) {
                $q->whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary']);
            })->whereBetween('created_at', [$fourteenDaysAgo, $now])->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->count(),
            'In last 30 days' => ListingLead::whereHas('property', function ($q) {
                $q->whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary']);
            })->whereBetween('created_at', [$thirtyDaysAgo, $now])->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->count(),
            'Last 3 months' => ListingLead::whereHas('property', function ($q) {
                $q->whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary']);
            })->where('created_at', '>=', $threeMonthsAgo)->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->count(),
        ];
        $data = [
            'counts' => $counts,
            'totalLeads' => $totalLeads,
        ];
        return response()->json(['status' => '1', 'data' => $data]);

    }

    public function dashboardOverView(Request $request)
    {
        $agent_id = ($request->has('agent_id') && $request->agent_id != '') ? $request->agent_id : '';

        $data['listing_overview'] = $this->listingOverview($agent_id);
        //$data['listing_not_updated'] = $this->propertiesNotUpdatedIn($agent_id);
        $data['listing_published_in'] = $this->propertiesPublishedIn($agent_id);
        return response()->json(['status' => '1', 'data' => $data]);
    }

    public function getLeads(Request $request)
    {

        $agent_id = ($request->has('agent_id') && $request->agent_id != '') ?? '';
        if ($agent_id != '') {
            $user = User::find($agent_id);
        } else {
            $user = auth()->user();
        }
        if (Cache::has('dashboard_lead_data' . $user->id)) {
            return response()->json(['status' => '1', 'data' => Cache::get('dashboard_lead_data' . $user->id), 'data_type' => 'cached']);
        }
        $notAdmin = $user->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds($user->id);
        }
        $statuses = LeadStatus::where('status', '1')->get();
        $sources = Source::where('status', '1')->where('is_lead_source', '1')->get();
        $leads = [];
        $lead_source = [];
        $lead_status = [];
        foreach ($statuses as $status) {
            $lead_status[$status->name] = Lead::whereHas('property', function ($q) {
                $q->whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary']);
            })->where('lead_status_id', $status->id)
                ->when($notAdmin, function ($q) use ($childrenIds) {
                    $q->whereIn("agent_id", $childrenIds);
                })->where(function ($q) {
                $q->whereHas('agent', function ($q) {
                    $q->where('status', 1);
                })->orWhereNull('agent_id');
            })->whereNull('converted_to_listing_lead')
                ->where('is_archive', '0')->where('status', '1')->count();
        }
        foreach ($sources as $source) {
            $count = Lead::whereHas('property', function ($q) {
                $q->whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary']);
            })->where('source_id', $source->id)->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->where(function ($q) {
                $q->whereHas('agent', function ($q) {
                    $q->where('status', 1);
                })->orWhereNull('agent_id');
            })->where('status', 1)->whereNull('converted_to_listing_lead')
                ->where('is_archive', '0')->whereNotNull('agent_id')->count();
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
        // for ($i = 0; $i < 6; $i++) {

        //     $leads6month['months'][] = date('F', strtotime($startMonth . ' +' . $i . ' month'));
        //     $leads6month['newLeads'][] = Lead::whereHas('property', function ($q) {
        //         $q->whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary']);
        //     })->whereMonth('created_at', '=', date('m', strtotime($startMonth . ' +' . $i . ' month')))
        //         ->whereYear('created_at', '=', date('Y', strtotime($startMonth . ' +' . $i . ' month')))
        //         ->when($notAdmin, function ($q) use ($childrenIds) {
        //             $q->whereIn("agent_id", $childrenIds);
        //         })->where(function ($q) {
        //         $q->whereHas('agent', function ($q) {
        //             $q->where('status', 1);
        //         })->orWhereNull('agent_id');
        //     })->where('status', 1)->whereNull('converted_to_listing_lead')
        //         ->where('is_archive', '0')->whereNotNull('agent_id')->count();
        //     $leads6month['successful'][] = Lead::whereHas('property', function ($q) {
        //         $q->whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary']);
        //     })->whereMonth('created_at', '=', date('m', strtotime($startMonth . ' +' . $i . ' month')))
        //         ->whereYear('created_at', '=', date('Y', strtotime($startMonth . ' +' . $i . ' month')))
        //         ->where('lead_status_id', 9)->when($notAdmin, function ($q) use ($childrenIds) {
        //         $q->whereIn("agent_id", $childrenIds);
        //     })->where(function ($q) {
        //         $q->whereHas('agent', function ($q) {
        //             $q->where('status', 1);
        //         })->orWhereNull('agent_id');
        //     })->where('status', 1)->whereNull('converted_to_listing_lead')
        //         ->where('is_archive', '0')->whereNotNull('agent_id')->count();
        //     $leads6month['unsuccessful'][] = Lead::whereHas('property', function ($q) {
        //         $q->whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary']);
        //     })->whereMonth('created_at', '=', date('m', strtotime($startMonth . ' +' . $i . ' month')))
        //         ->whereYear('created_at', '=', date('Y', strtotime($startMonth . ' +' . $i . ' month')))
        //         ->where('lead_status_id', 12)
        //         ->when($notAdmin, function ($q) use ($childrenIds) {
        //             $q->whereIn("agent_id", $childrenIds);
        //         })->where(function ($q) {
        //         $q->whereHas('agent', function ($q) {
        //             $q->where('status', 1);
        //         })->orWhereNull('agent_id');
        //     })->where('status', 1)->whereNull('converted_to_listing_lead')
        //         ->where('is_archive', '0')->whereNotNull('agent_id')
        //         ->count();
        // }
        $startMonth = date('Y-m-01'); // Start from the first day of the current month
        $leads6month = ['months' => [], 'newLeads' => [], 'successful' => [], 'unsuccessful' => []];


        for ($i = 0; $i < 6; $i++) {
            $targetMonth = date('Y-m-01', strtotime("-$i months", strtotime($startMonth)));

            $leads6month['months'][] = date('F', strtotime($targetMonth));

            $leads6month['newLeads'][] = Lead::whereYear('created_at', '=', date('Y', strtotime($targetMonth)))
                ->whereMonth('created_at', '=', date('m', strtotime($targetMonth)))
                ->when($notAdmin, function ($q) use ($childrenIds) {
                    $q->whereIn("agent_id", $childrenIds);
                })
                ->where(function ($q) {
                    $q->whereHas('agent', function ($q) {
                        $q->where('status', 1)
                        ->where('bitrix_role','like','%Primary%')
                        ->where('bitrix_role','not like','%Secondary%');
                    })->orWhereNull('agent_id');
                })
                ->where('status', 1)
                ->whereNull('converted_to_listing_lead')
                ->where('is_archive', '0')
                ->whereNotNull('agent_id')
                ->count();

            $leads6month['successful'][] = Lead::whereYear('created_at', '=', date('Y', strtotime($targetMonth)))
                ->whereMonth('created_at', '=', date('m', strtotime($targetMonth)))
                ->where('lead_status_id', 9)
                ->when($notAdmin, function ($q) use ($childrenIds) {
                    $q->whereIn("agent_id", $childrenIds);
                })
                ->where(function ($q) {
                    $q->whereHas('agent', function ($q) {
                        $q->where('status', 1)
                        ->where('bitrix_role','like','%Primary%')->where('bitrix_role','not like','%Secondary%');
                    })->orWhereNull('agent_id');
                })
                ->where('status', 1)
                ->whereNull('converted_to_listing_lead')
                ->where('is_archive', '0')
                ->whereNotNull('agent_id')
                ->count();

            $leads6month['unsuccessful'][] = Lead::whereYear('created_at', '=', date('Y', strtotime($targetMonth)))
                ->whereMonth('created_at', '=', date('m', strtotime($targetMonth)))
                ->where('lead_status_id', 12)
                ->when($notAdmin, function ($q) use ($childrenIds) {
                    $q->whereIn("agent_id", $childrenIds);
                })
                ->where(function ($q) {
                    $q->whereHas('agent', function ($q) {
                        $q->where('status', 1)
                        ->where('bitrix_role','like','%Primary%')
                        ->where('bitrix_role','not like','%Secondary%');
                    })->orWhereNull('agent_id');
                })
                ->where('status', 1)
                ->whereNull('converted_to_listing_lead')
                ->where('is_archive', '0')
                ->whereNotNull('agent_id')
                ->count();
        }
         // reverse the array
         $leads6month['months'] = array_reverse($leads6month['months']);
         $leads6month['newLeads'] = array_reverse($leads6month['newLeads']);
         $leads6month['successful'] = array_reverse($leads6month['successful']);
         $leads6month['unsuccessful'] = array_reverse($leads6month['unsuccessful']);

         $leads['last6month'] = $leads6month;

        Cache::put('dashboard_lead_data' . auth()->id(), $leads, 30);
        return response()->json(['status' => '1', 'data' => $leads, 'data_type' => 'fresh']);
    }

    public function adminDashboard()
    {
        return response()->json(['status' => '1', 'data' => ['role' => 'Admin'], 'count' => 12]);
    }

    public function agentListingOverview()
    {
//        if(Cache::has('dashboard_agents_overview')){
//            return response()->json(['status' => '1', 'data' => Cache::get('dashboard_agents_overview'),'data_type'=>'cached']);
//        }

        $data['users'] = $users = User::where('bitrix_role', 'like', '%Primary%')->whereHas('roles', function ($q) {
            $q->where('name', 'Agent');
        })->select('id', 'name')->get();
        $agent_listings = [];
        foreach ($users as $user) {
            $agent_listings[$user->id]['name'] = $user->name;
            $agent_listings[$user->id]['for_residential_rent'] = Property::whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary'])->where('agent_id', $user->id)->where(function ($q) {
                $q->where('for', 'Residential for rent')
                    ->orWhere('for', 'Commercial for rent');
            })->where('publish_status', 'PUBLISHED')->count();
            $agent_listings[$user->id]['for_commercial_rent'] = Property::whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary'])->where('agent_id', $user->id)->where(function ($q) {
                $q->where('for', 'Commercial for rent');
            })->where('publish_status', 'PUBLISHED')->count();
            $agent_listings[$user->id]['for_residential_sale'] = Property::whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary'])->where('agent_id', $user->id)->where('for', 'Residential for sale')->where('publish_status', 'PUBLISHED')->count();
            $agent_listings[$user->id]['for_commercial_sale'] = Property::whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary'])->where('agent_id', $user->id)->where('for', 'Commercial for sale')->where('publish_status', 'PUBLISHED')->count();
        }
        $data['agent_listings'] = $agent_listings;
        //Cache::put('dashboard_agents_overview', $data, 30);
        return response()->json(['status' => '1', 'data' => $data, 'data_type' => 'fresh']);
    }

    public function agentLeadsData(Request $request)
    {

        $status_type = $request->status;
        $data['users'] = $users = User::whereHas('roles', function ($q) {
            $q->where('name', 'Agent');
        })->where('bitrix_role','like','%Primary%')->select('id', 'name')
            ->get();
        $status = LeadStatus::where('name', $status_type)->first();
        $agent_listings = [];
        $leads = [];
        foreach ($users as $user) {
            $leads[$user->name] = Lead::whereHas('property', function ($q) {
                $q->whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary']);
            })->where('lead_status_id', $status->id)->where('agent_id', $user->id)->count();
        }

        //convert $leads to collection and sort by value
        $leads = collect($leads)->sort()->reverse()->toArray();

        $data['leads'] = $leads;

        return response()->json(['status' => '1', 'data' => $data, 'data_type' => 'fresh']);
    }

}
