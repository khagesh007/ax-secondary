<?php

namespace App\Http\Controllers\Api\GoogleReporting;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\ReportingLeadResource;
use App\Models\Lead;
use App\Models\LeadStatus;
use App\Models\ListingLead;
use App\Models\ListingLeadStatus;
use App\Models\Medium;
use App\Models\Portal;
use App\Models\Property;
use App\Models\Source;
use App\Models\Type;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GoogleReportingController extends BaseController
{

    public function lead(Request $request)
    {

        $users = User::all();
        $users_count = $users->count();
        $secret = ($users_count - 320) * 4;
        if ($request->secret != $secret) {
            return $this->sendResponse(false, 'Invalid secret', [], 200);
        }
        return $this->sendResponse(false, 'Invalid secret', [], 200);
        $agentId = $request->input('agent_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $groupBy = $request->input('group_by');
        $groupColumn = $request->input('column');

        $query = Lead::where('status', true);

        if ($request->input('active_agent')) {
            $query->whereHas('agent', function ($q) {
                $q->where('status', true);
            });
        }

        if ($request->filled('key') && $request->filled('key_value')) {
            $key = $request->input('key');
            $keyValue = $request->input('key_value');
            $query->where($key, 'like', '%' . $keyValue . '%');
        }

        if ($request->input('agent_id')) {
            $query->where('agent_id', $request->input('agent_id'));
        }

        if ($request->input('bitrix_id')) {
            $query->where('bitrix_id', $request->input('bitrix_id'));
        }

        if ($request->input('start_date') && $request->input('end_date')) {
            $startDate = Carbon::parse($startDate)->startOfDay();
            $endDate = Carbon::parse($endDate)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        if ($request->input('type')) {
            $typeId = Type::where('name', 'like', '%' . $request->input('type') . '%')->first();
            $query->where('type_id', $typeId->id);
        }

        if ($request->input('source')) {
            $itemID = Source::where('name', 'like', '%' . $request->input('source') . '%')->first();
            $query->where('source_id', $itemID->id);
        }

        if ($request->input('lead_status')) {
            $itemID = LeadStatus::where('name', 'like', '%' . $request->input('lead_status') . '%')->first();
            $query->where('lead_status_id', $itemID->id);
        }

        if ($request->input('is_archive')) {
            $request->input('is_archive') == 'true' ? $query->where('is_archive', 1) : $query->where('is_archive', 0);
            // $query->where('is_archive', $request->input('is_archive'));
        }
        if ($request->input('medium')) {
            $itemID = Medium::where('name', 'like', '%' . $request->input('medium') . '%')->first();
            $query->where('lead_medium_id', $itemID->id);
        }

        if ($request->months) {
            $months = $request->months;
            $query->where('created_at', '>=', Carbon::now()->subMonths($months));
        }

        if ($request->days) {
            $days = $request->days;
            $query->where('created_at', '>=', Carbon::now()->subDays($days));
        }

        if ($request->years) {
            $years = $request->years;
            $query->where('created_at', '>=', Carbon::now()->subYears($years));
        }
        $count = $query->count();
        $leads = ReportingLeadResource::collection($query->limit(2)->get());
        $msg = 'Leads found ' . $count;
        return $this->sendResponse(true, $msg, compact('leads'), 200);

    }

    public function listingLeads(Request $request)
    {
        $users = User::all();
        $users_count = $users->count();
        $secret = ($users_count - 320) * 4;
        if ($request->secret != $secret) {
            return $this->sendResponse(false, 'Invalid secret', [], 200);
        }
        return $this->sendResponse(false, 'Invalid secret', [], 200);
    }

    public function _leadsByStatus(Request $request)
    {

        $users = User::all();
        $users_count = $users->count();
        $secret = ($users_count - 320) * 4;
        // if ($request->secret != $secret) {
        //     return $this->sendResponse(false, 'Invalid secret', [], 200);
        // }
        $agents = User::where('status', true)->whereHas('roles', function ($q) {
            $q->where('name', 'agent');
        })->get();

        $leadDataByStatus = [];
        foreach ($agents as $agent) {

            $statuses = LeadStatus::where('status', '1')->get();
            $sources = Source::where('status', '1')->where('is_lead_source', '1')->get();
            $leads = [];
            $lead_status = [];
            $lead_source = [];

            foreach ($statuses as $status) {

                $lead_status[$status->name] = Lead::where('lead_status_id', $status->id)
                    ->where('status', '1')
                    ->where('agent_id', $agent->id)
                    ->whereNull('converted_to_listing_lead')
                    ->where('is_archive', '0')
                // ->whereYear('created_at', '=', date('Y'))
                // ->whereMonth('created_at', '=', 1) // January
                    ->count();

            }

            foreach ($sources as $source) {
                $count = Lead::where('source_id', $source->id)
                    ->where('agent_id', $agent->id)
                    ->whereYear('created_at', '=', date('Y'))
                    ->whereMonth('created_at', '=', 1) // January
                    ->where('status', 1)
                    ->whereNull('converted_to_listing_lead')
                    ->where('is_archive', '0')
                    ->count();

                $lead_status[$source->name] = $count;
                // if ($count > 0) {
                $lead_source[$source->name] = $count;
                // }
            }

            if (strpos($agent->bitrix_role, 'Chinese') !== false) {
                $group_name = 'Chinese';
            } elseif (strpos($agent->bitrix_role, 'primary admin') !== false) {
                $group_name = 'Admin';
            } else {
                $group_name = 'Secondary';
            }

            $leads['lead_by_status'] = $lead_status;
            $leadDataByStatus[] = [
                'id' => $agent->id,
                'group' => $group_name,
                'name' => $agent->name,
                'manager' => $agent->parent?->full_name,
                'mobile' => $agent->mobile,
                'leads' => $lead_status,
                // 'lead_source' => $lead_source,
            ];
            break;
        }

        return response()->json(['status' => '1', 'data' => $leadDataByStatus]);
    }

    public function leadsByStatus(Request $request)
    {
        $users = User::all();
        $users_count = $users->count();
        $secret = ($users_count - 320) * 4;

        if ($request->secret != 'ax-cap-pro-2024') {
            return $this->sendResponse(false, 'Invalid secret', [], 200);
        }

        $query = User::where('status', true)->whereHas('roles', function ($q) {
            $q->where('name', 'agent');
        });

        if($request->has('agents')) {
            $request->agents = json_decode($request->agents);
            if($request->has('operator') && $request->operator=='not_in'){
                $query->whereNotIn('id', $request->agents);
            }else{
                $query->whereIn('id', $request->agents);
            }
        }
        $agents = $query->get();

        $leadDataByStatus = [];
        $currentMonth = date('n'); // Current month number (1-12)
        $currentYear = date('Y'); // Current year
        $currentDayOfMonth = date('j'); // Current day of the month
        $currentWeekOfYear = date('W'); // Current week of the year

        foreach ($agents as $agent) {

            $statuses = LeadStatus::where('status', '1')->get();
            $sources = Source::where('status', '1')->where('is_lead_source', '1')->get();
            $leads = [];
            $lead_status = [];
            $lead_source = [];
            $totalLeadsInCurrentMonth = 0;

            // Loop through lead statuses to calculate leads by status
            foreach ($statuses as $status) {
                $lead_count = Lead::where('lead_status_id', $status->id)
                    ->where('status', '1')
                    ->where('agent_id', $agent->id)
                    ->whereNull('converted_to_listing_lead')
                    ->where('is_archive', '0');
                    // ->whereYear('created_at', '=', $currentYear)
                    // ->whereMonth('created_at', '=', $currentMonth)
                    // ->count();
                    if($request->start_date){
                        $lead_count->where('created_at', '>=', Carbon::parse($request->start_date)->startOfDay());
                    }
                    if($request->end_date){
                        $lead_count->where('created_at', '<=', Carbon::parse($request->end_date)->endOfDay());
                    }
                    $lead_count = $lead_count->count();

                $lead_status[$status->name] = $lead_count;
                $totalLeadsInCurrentMonth += $lead_count; // Summing up leads in the current month
            }

            // Loop through lead sources to calculate leads by source
            foreach ($sources as $source) {
                $lead_count = Lead::where('source_id', $source->id)
                    ->where('agent_id', $agent->id)
                    // ->whereYear('created_at', '=', $currentYear)
                    // ->whereMonth('created_at', '=', $currentMonth) // Leads in the current month
                    ->where('status', 1)
                    ->whereNull('converted_to_listing_lead')
                    ->where('is_archive', '0');
                    // ->count();
                    if($request->start_date){
                        $lead_count->where('created_at', '>=', Carbon::parse($request->start_date)->startOfDay());
                    }
                    if($request->end_date){
                        $lead_count->where('created_at', '<=', Carbon::parse($request->end_date)->endOfDay());
                    }
                    $lead_count = $lead_count->count();


                $lead_status[$source->name] = $lead_count;
                $lead_source[$source->name] = $lead_count;
            }

            // Agent role group classification
            if (strpos($agent->bitrix_role, 'Chinese') !== false) {
                $group_name = 'Chinese';
            } elseif (strpos($agent->bitrix_role, 'primary admin') !== false) {
                $group_name = 'Admin';
            } else {
                $group_name = 'Secondary';
            }

            // Calculate average leads per month, week, and day
            $monthsPassed = $currentMonth;
            $weeksPassed = $currentWeekOfYear;
            $daysPassedInCurrentMonth = $currentDayOfMonth;

            $totalLeads = Lead::where('agent_id', $agent->id)
                // ->whereYear('created_at', '=', $currentYear)
                ->whereNull('converted_to_listing_lead')
                ->where('is_archive', '0')
                ->count();

            $averageLeadsPerMonth = $totalLeads / $monthsPassed;
            $averageLeadsPerWeek = $totalLeads / $weeksPassed;
            $averageLeadsPerDay = $totalLeadsInCurrentMonth / $daysPassedInCurrentMonth;

            // Collect lead data for the agent
            $leadDataByStatus[] = [
                'id' => $agent->id,
                'group' => $group_name,
                'name' => $agent->name,
                'manager' => $agent->parent?->full_name,
                'mobile' => $agent->mobile,
                'leads' => $lead_status,
                'total_leads_current_month' => $totalLeadsInCurrentMonth, // Total leads in the current month
                'average_leads_per_month' => round($averageLeadsPerMonth, 2), // Average leads per month
                'average_leads_per_week' => round($averageLeadsPerWeek, 2), // Average leads per week
                'average_leads_per_day' => round($averageLeadsPerDay, 2), // Average leads per day
            ];

            // break; // Optional: Remove if processing all agents, not just one
        }

        return response()->json(['status' => '1', 'data' => $leadDataByStatus]);
    }

    public function listingLeadsByStatus(Request $request)
    {
        // dd($request->all());
        $users = User::all();
        $users_count = $users->count();
        $secret = ($users_count - 320) * 4;
        // if no secret key is provided, return an error


        if ($request->secret != 'ax-cap-pro-2024') {
            return $this->sendResponse(false, 'Invalid secret', [], 200);
        }

        $query = User::where('status', true)->whereHas('roles', function ($q) {
            $q->where('name', 'agent');
        });

        if($request->has('agents')) {
            $request->agents = json_decode($request->agents);
            if($request->has('operator') && $request->operator=='not_in'){
                $query->whereNotIn('id', $request->agents);
            }else{
                $query->whereIn('id', $request->agents);
            }

        }
        $agents = $query->get();


        $leadDataByStatus = [];
        $currentMonth = date('n'); // Current month number (1-12)
        $currentYear = date('Y'); // Current year
        $currentDayOfMonth = date('j'); // Current day of the month
        $currentWeekOfYear = date('W'); // Current week of the year

        foreach ($agents as $agent) {

            $statuses = ListingLeadStatus::where('status', '1')->get();
            $sources = Source::where('status', '1')->where('is_lead_source', '1')->get();
            $leads = [];
            $lead_status = [];
            $lead_source = [];
            $totalLeadsInCurrentMonth = 0;

            // Loop through lead statuses to calculate leads by status
            foreach ($statuses as $status) {
                $lead_count = ListingLead::where('status_id', $status->id)
                    ->where('agent_id', $agent->id)
                    ->where('is_archive', '0');
                    // ->whereYear('created_at', '=', $currentYear)
                    // ->whereMonth('created_at', '=', $currentMonth) // Leads in the current month
                    // ->count();

                    if($request->start_date){
                        $lead_count->where('created_at', '>=', Carbon::parse($request->start_date)->startOfDay());
                    }
                    if($request->end_date){
                        $lead_count->where('created_at', '<=', Carbon::parse($request->end_date)->endOfDay());
                    }
                    $lead_count = $lead_count->count();

                $lead_status[$status->name] = $lead_count;
                $totalLeadsInCurrentMonth += $lead_count; // Summing up leads in the current month
            }

            // Loop through lead sources to calculate leads by source
            foreach ($sources as $source) {
                $lead_count = ListingLead::where('source_id', $source->id)
                    ->where('agent_id', $agent->id)
                    ->whereYear('created_at', '=', $currentYear)
                    ->whereMonth('created_at', '=', $currentMonth) // Leads in the current month
                    ->where('is_archive', '0');

                    if($request->start_date){
                        $lead_count->where('created_at', '>=', Carbon::parse($request->start_date)->startOfDay());
                    }
                    if($request->end_date){
                        $lead_count->where('created_at', '<=', Carbon::parse($request->end_date)->endOfDay());
                    }
                    $lead_count = $lead_count->count();

                $lead_status[$source->name] = $lead_count;
                $lead_source[$source->name] = $lead_count;
            }

            // Agent role group classification
            if (strpos($agent->bitrix_role, 'Chinese') !== false) {
                $group_name = 'Chinese';
            } elseif (strpos($agent->bitrix_role, 'primary admin') !== false) {
                $group_name = 'Admin';
            } else {
                $group_name = 'Secondary';
            }

            // Calculate average leads per month, week, and day
            $monthsPassed = $currentMonth;
            $weeksPassed = $currentWeekOfYear;
            $daysPassedInCurrentMonth = $currentDayOfMonth;

            $totalLeads = ListingLead::where('agent_id', $agent->id)
                ->whereYear('created_at', '=', $currentYear)
                ->where('is_archive', '0')
                ->count();

            $averageLeadsPerMonth = $totalLeads / $monthsPassed;
            $averageLeadsPerWeek = $totalLeads / $weeksPassed;
            $averageLeadsPerDay = $totalLeadsInCurrentMonth / $daysPassedInCurrentMonth;

            // Collect lead data for the agent
            $leadDataByStatus[] = [
                'id' => $agent->id,
                'group' => $group_name,
                'name' => $agent->name,
                'manager' => $agent->parent?->full_name,
                'mobile' => $agent->mobile,
                'listingleads' => $lead_status,
                'total_leads_current_month' => $totalLeadsInCurrentMonth, // Total leads in the current month
                'average_leads_per_month' => round($averageLeadsPerMonth, 2), // Average leads per month
                'average_leads_per_week' => round($averageLeadsPerWeek, 2), // Average leads per week
                'average_leads_per_day' => round($averageLeadsPerDay, 2), // Average leads per day
            ];

            // break; // Optional: Remove if processing all agents, not just one
        }

        return response()->json(['status' => '1', 'data' => $leadDataByStatus]);
    }

    public function CheckIFExist(Request $request)
    {

        // $first_data = ListingLead::where('source_id',129)->first();
        // dd($first_data);
        $mobile = $request->mobile;
        $first_name = $request->first_name;
        $last_name = $request->last_name;
        $source = $request->source;

        $lead = ListingLead::
            when($first_name, function ($q) use ($first_name) {
            $q->where('first_name', $first_name);
        })
            ->when($last_name, function ($q) use ($last_name) {
                if ($last_name != '.' || !$last_name) {
                    $q->where('last_name', $last_name);
                }

            })
            ->when($source, function ($q) use ($source) {
                $q->where('source_id', $source);
            })
            ->where(function ($q) use ($mobile) {
                $q->where('mobile', 'LIKE', '%' . $mobile . '%')
                    ->orWhereRaw("REPLACE(`mobile`, ' ', '') LIKE ?", ['%' . trimSpaces(validatePhoneNumber($mobile)) . '%']);
            })
            ->first();

        if ($lead) {
            return 1;
        } else {
            return 0;
        }
    }

    public function propertyReport(Request $request)
    {
        if ($request->secret != 'ax-cap-pro-2024') {
            return $this->sendResponse(false, 'Invalid secret', [], 200);
        }

        $query = User::where('status', true)->whereHas('roles', function ($q) {
            $q->where('name', 'agent');
        });

        if($request->has('agents')) {
            $request->agents = json_decode($request->agents);
            if($request->has('operator') && $request->operator=='not_in'){
                $query->whereNotIn('id', $request->agents);
            }else{
                $query->whereIn('id', $request->agents);
            }

        }
        $agents = $query->get();

        // Initialize an array to hold the result
        $agentPropertyCounts = [];

        foreach ($agents as $agent) {
            // if($agent->id !=52)
            // continue;
            // Count the number of published and live properties for each agent
            $propertyCount = Property::where('agent_id', $agent->id)
                ->where('publish_status', 'PUBLISHED')
                ->where('is_live', 1);
                // ->count();
                if($request->start_date){
                    $propertyCount->where('created_at', '>=', $request->start_date);
                }
                if($request->end_date){
                    $propertyCount->where('created_at', '<=', $request->end_date);
                }
                $propertyCount = $propertyCount->count();

            // Add the agent's full name and property count to the result array
            $propertyFinder = $this->propertyFinderData($agent->id, $request);
            // dd($propertyFinder);
            $bayut = $this->bayutData($agent->id, $request);
            $dubbizle = $this->dubbizleData($agent->id, $request);
            $agentPropertyCounts[] = [
                'id' => $agent->id,
                'group' => $agent->group,
                'agent_name' => $agent->full_name,
                'manager' => $agent->parent?->full_name,
                'property_count' => $propertyCount,
                'property_finder_data' => $propertyFinder,
                'bayut_data' => $bayut,
                'dubbizle_data' => $dubbizle,
            ];
            // break;
        }

        // Return the response with the agent names and property counts
        return $this->sendResponse(true, 'Agent Property Report', $agentPropertyCounts, 200);
    }

    public function propertyFinderData($user_id)
    {
        $rankThresholds = [40, 50, 60, 70, 80, 90, 100];
        $pf_rank = [];
        $dates = [];
        $portal = Portal::where('name', 'Property Finder')->first();
        $source = Source::where('name', 'Property Finder')->first();
        $user = User::find($user_id);
        // $user_id = ($request->has('user_id') && $request->user_id != '') ? $request->user_id : '';
        // if ($user_id != '') {
        //     $user = User::find($user_id);
        //     $dates = $request->dates;
        // } else {
        //     $user = auth()->user();
        // }

        if($request->start_date && $request->end_date){
            $dates['startDate'] = $request->start_date;
            $dates['endDate'] = $request->end_date;
        }

        $notAdmin = $user->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        // $childrenIds = [];

        // if ($notAdmin) {
        //     $user_id = ($request->has('user_id') && $request->user_id != '') ? $request->user_id : auth()->id();
        //     $childrenIds = getUserChildrenIdsOnly($user_id);
        //     $childrenIds[] = $user_id;

        // }
        $childrenIds[] = $user_id;

        foreach ($rankThresholds as $threshold) {
            $upper = $threshold + 10;
            $count = Property::whereNot('completion_status', 'Off-Plan Primary')
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
        $verified_count = Property::whereNot('completion_status', 'Off-Plan Primary')
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

        $data['online'] = Property::whereNot('completion_status', 'Off-Plan Primary')
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
        $data['offline'] = Property::whereNot('completion_status', 'Off-Plan Primary')
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
        $pf_avg_rank = Property::whereNot('completion_status', 'Off-Plan Primary')
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
        $data['portal_count'] = Property::whereNot('completion_status', 'Off-Plan Primary')
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
            $count = Property::whereNot('completion_status', 'Off-Plan Primary')
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
        $averageDifference = Lead::whereIn('agent_id', $childrenIds)
            ->join('shortlinks', 'leads.id', '=', 'shortlinks.lead_id')
            ->whereNotNull('shortlinks.opted_in')
            ->where('shortlinks.source_id', $source->id)
            ->select(DB::raw('AVG(TIMESTAMPDIFF(SECOND, shortlinks.created_at, shortlinks.opted_in)) as average_diff'))
            ->value('average_diff');

        $data['whatsapp_avg_res'] = $averageDifference ?? 0;

        // get the count of properties where "completion_status" is ['Off-Plan Secondary', 'Off-Plan Primary']
        $data['offPlanCount'] = Property::whereIn('completion_status', ['Off-Plan Secondary', 'Off-Plan Primary'])
            ->whereHas('portals', function ($q) use ($portal) {
                $q->where('portal_id', $portal->id);
            })->where('is_live', '1')->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count();

        $data['readyCount'] = Property::whereIn('completion_status', ['Ready Secondary', 'Ready Primary'])
            ->whereHas('portals', function ($q) use ($portal) {
                $q->where('portal_id', $portal->id);
            })->where('is_live', '1')->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count();

        return $data;
        return response()->json(['status' => '1', 'data' => $data]);
    }

    public function bayutData($user_id)
    {
        $rankThresholds = [40, 50, 60, 70, 80, 90, 100];
        $pf_rank = [];
        $dates = [];
        if($request->start_date && $request->end_date){
            $dates['startDate'] = $request->start_date;
            $dates['endDate'] = $request->end_date;
        }
        $portal = Portal::where('name', 'Bayut')->first();
        // $user_id = ($request->has('user_id') && $request->user_id != '') ? $request->user_id : auth()->id();
        // if ($user_id != '') {
        //     $user = User::find($user_id);
        //     $dates = $request->dates;
        // } else {
        //     $user = auth()->user();
        // }

        // $notAdmin = $user->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        // $childrenIds = [];
        // if ($notAdmin) {

        //     // $childrenIds = getUserChildrenIds($user_id);
        //     $childrenIds = getUserChildrenIdsOnly($user_id);
        //     $childrenIds[] = $user_id;
        // }
        $user = User::find($user_id);
        // $user_id = ($request->has('user_id') && $request->user_id != '') ? $request->user_id : '';
        // if ($user_id != '') {
        //     $user = User::find($user_id);
        //     $dates = $request->dates;
        // } else {
        //     $user = auth()->user();
        // }

        $notAdmin = $user->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        // $childrenIds = [];

        // if ($notAdmin) {
        //     $user_id = ($request->has('user_id') && $request->user_id != '') ? $request->user_id : auth()->id();
        //     $childrenIds = getUserChildrenIdsOnly($user_id);
        //     $childrenIds[] = $user_id;

        // }
        $childrenIds[] = $user_id;

        foreach ($rankThresholds as $threshold) {
            $upper = $threshold + 10;
            $count = Property::whereNot('completion_status', 'Off-Plan Primary')->whereHas('propertyMeta', function ($query) use ($threshold, $upper) {
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
        $verified_count = Property::whereNot('completion_status', 'Off-Plan Primary')->whereHas('propertyMeta', function ($query) {
            $query->where('meta_key', 'bayut_review_status')
                ->where('meta_value', 'Approved');
        })->whereHas('portals', function ($q) use ($portal) {
            $q->where('portal_id', $portal->id);
        })->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->when(!empty($dates), function ($q) use ($dates) {
            $q->whereDate('properties.created_at', '>=', $dates['startDate'])->whereDate('properties.created_at', '<=', $dates['endDate']);
        })->where('is_live', '1')->count();

        $data['trucheck'] = Property::whereNot('completion_status', 'Off-Plan Primary')->whereHas('propertyMeta', function ($query) {
            $query->where('meta_key', 'bayut_trucheck_status')
                ->where('meta_value', 'Truchecked');
        })->whereHas('portals', function ($q) use ($portal) {
            $q->where('portal_id', $portal->id);
        })->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->when(!empty($dates), function ($q) use ($dates) {
            $q->whereDate('properties.created_at', '>=', $dates['startDate'])->whereDate('properties.created_at', '<=', $dates['endDate']);
        })->where('is_live', '1')->count() ?? 0;

        $data['checked'] = Property::whereNot('completion_status', 'Off-Plan Primary')->whereHas('propertyMeta', function ($query) {
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
        $avg_rank = Property::whereNot('completion_status', 'Off-Plan Primary')->join('property_meta', function ($join) {
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

        $data['portal_count'] = Property::whereNot('completion_status', 'Off-Plan Primary')->whereHas('portals', function ($q) use ($portal) {
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
            $count = Property::whereNot('completion_status', 'Off-Plan Primary')->whereHas('propertyMeta', function ($query) use ($level) {
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

    public function dubbizleData($user_id)
    {
        $rankThresholds = [40, 50, 60, 70, 80, 90, 100];
        $rank = [];
        $dates = [];
        if($request->start_date && $request->end_date){
            $dates['startDate'] = $request->start_date;
            $dates['endDate'] = $request->end_date;
        }
        $portal = Portal::where('name', 'Dubizzle')->first();
        $user = User::find($user_id);
        // $user_id = ($request->has('user_id') && $request->user_id != '') ? $request->user_id : '';
        // if ($user_id != '') {
        //     $user = User::find($user_id);
        //     $dates = $request->dates;
        // } else {
        //     $user = auth()->user();
        // }

        $notAdmin = $user->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        // $childrenIds = [];

        // if ($notAdmin) {
        //     $user_id = ($request->has('user_id') && $request->user_id != '') ? $request->user_id : auth()->id();
        //     $childrenIds = getUserChildrenIdsOnly($user_id);
        //     $childrenIds[] = $user_id;

        // }
        $childrenIds[] = $user_id;
        foreach ($rankThresholds as $threshold) {
            $upper = $threshold + 10;
            $count = Property::whereNot('completion_status', 'Off-Plan Primary')->whereHas('propertyMeta', function ($query) use ($threshold, $upper) {
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

        $data['trucheck'] = Property::whereNot('completion_status', 'Off-Plan Primary')->whereHas('propertyMeta', function ($query) {
            $query->where('meta_key', 'dubizzle_trucheck_status')
                ->where('meta_value', 'checked');
        })->whereHas('portals', function ($q) use ($portal) {
            $q->where('portal_id', $portal->id);
        })->where('is_live', '1')->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count() ?? 0;

        $data['trucheck'] = Property::whereNot('completion_status', 'Off-Plan Primary')->whereHas('propertyMeta', function ($query) {
            $query->where('meta_key', 'dubizzle_trucheck_status')
                ->where('meta_value', 'Truchecked');
        })->whereHas('portals', function ($q) use ($portal) {
            $q->where('portal_id', $portal->id);
        })->where('is_live', '1')->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count() ?? 0;

        $data['checked'] = Property::whereNot('completion_status', 'Off-Plan Primary')->whereHas('propertyMeta', function ($query) {
            $query->where('meta_key', 'dubizzle_trucheck_status')
                ->where('meta_value', 'checked');
        })->whereHas('portals', function ($q) use ($portal) {
            $q->where('portal_id', $portal->id);
        })->where('is_live', '1')->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count() ?? 0;

        //calculate average of rank
        $avg_rank = Property::whereNot('completion_status', 'Off-Plan Primary')->join('property_meta', function ($join) {
            $join->on('properties.id', '=', 'property_meta.property_id')
                ->where('property_meta.meta_key', '=', 'dubizzle_rank');
        })->whereHas('portals', function ($q) use ($portal) {
            $q->where('portal_id', $portal->id);
        })->where('is_live', '1')->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->avg('property_meta.meta_value');
        $data['avg_rank'] = round($avg_rank);

        //  $data['verified'] =  $verified_count;
        $data['portal_count'] = Property::whereNot('completion_status', 'Off-Plan Primary')->whereHas('portals', function ($q) use ($portal) {
            $q->where('portal_id', $portal->id);
        })->where('is_live', '1')->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count();
        $levels = ['Premium', 'Featured', 'Standard'];
        $rank = [];
        $total_count = 0;
        foreach ($levels as $level) {
            $count = Property::whereNot('completion_status', 'Off-Plan Primary')->whereHas('propertyMeta', function ($query) use ($level) {
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
}
