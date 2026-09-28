<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Models\Community;
use App\Models\Contact;
use App\Models\Event;
use App\Models\EventType;
use App\Models\Group;
use App\Models\Lead;
use App\Models\LeadStatus;
use App\Models\ListingLead;
use App\Models\ListingLeadSource;
use App\Models\ListingLeadStatus;
use App\Models\Portal;
use App\Models\Property;
use App\Models\Source;
use App\Models\Type;
use App\Models\User;
use App\Models\UserLocation;
use App\Models\UserMeta;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class GroupReportV3Controller extends Controller
{
    public function index(Request $request)
    {

        if (!auth()->user()->can('Group Reports')) {
            abort(403, 'Unauthorized action.');
        }

        $users = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['Sales Director']);
        })->whereDoesntHave('group')
            ->whereHas('children')
            ->where('status', 1)
            ->get();

        $data_filtered = Group::with(['parent', 'user'])
            ->whereNull('parent_id')
            ->orderBy('order', 'asc')
            ->get();

        if ($request->child) {

            $users = User::whereHas('roles', function ($query) {
                $query->whereIn('name', ['Sales Director']);
            })->whereDoesntHave('group')
                ->whereHas('children')
                ->where('status', 1)
                ->get();

            $data_filtered = Group::with(['parent', 'user'])->where('parent_id', $request->child)->get();
            foreach ($data_filtered as $row) {
                $user_id = $row->user->id ?? null;
                $childCount = childcount($user_id) ?? 0;
                $row->child_count = $childCount;
            }

            return view('admin.reports.group')
                ->with('users', $users)
                ->with('data_filtered', $data_filtered);
        }

        foreach ($data_filtered as $row) {
            $user_id = $row->user->id ?? null;
            $childCount = childcount($user_id) ?? 0;
            $row->child_count = $childCount;
        }
        // dd($data_filtered);
        return view('admin.reports.v3.group')
            ->with('users', $users)
            ->with('data_filtered', $data_filtered);
    }
    public function processData(Request $request)
    {

        if (!auth()->user()->can('Group Reports')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'groupIds' => 'required',
            'type' => 'required',
            'duration' => 'nullable',
        ]);

        if ($request->type == 'stats') {
            $data = $this->getGroupBasicStats($request->groupIds, $request->duration);
            return view('admin.reports.v3.partials._basic_stats')
                ->with('groupdata', $data);
        }
        if ($request->type == 'leads') {
            $data = $this->leadsOverview($request->groupIds, $request->duration);
            return view('admin.reports.v3.partials._leads')
                ->with('groupdata', $data);
        }

        if ($request->type == 'listings') {
            $data = $this->listingOverview($request->groupIds, $request->duration);
            return view('admin.reports.v3.partials._listings')
                ->with('groupdata', $data);
        }
        if ($request->type == 'locations') {
            $data = $this->getLocationStats($request->groupIds, $request->duration);
            return view('admin.reports.v3.partials._location')
                ->with('groupdata', $data);
        }
        if ($request->type == 'leadSource') {
            $data = $this->leadsSourceOverview($request->groupIds, $request->duration);
            return view('admin.reports.v3.partials._source')
                ->with('groupdata', $data);
        }
        if ($request->type == 'listingComparison') {
            $data = $this->leadsSourceOverview($request->groupIds, $request->duration);
            return view('admin.reports.v3.partials._status')
                ->with('groupdata', $data);
        }
        if ($request->type == 'propertyQuality') {
            $data = $this->propertyQuality($request->groupIds, $request->duration);
            return view('admin.reports.v3.partials._listing_quality')
                ->with('groupdata', $data);
        }
        if ($request->type == 'eventsStats') {
            $data = $this->geteventsStats($request->groupIds, $request->duration);
            return view('admin.reports.v3.partials._events')
                ->with('groupdata', $data);
        }

        if ($request->type == 'ListingLeadStatus') {
            $data = $this->listingLeadsOverview($request->groupIds, $request->duration);
            return view('admin.reports.v3.partials._listing_leads_source')
                ->with('groupdata', $data);
        }
        if ($request->type == 'ListingLeadSource') {
            $data = $this->ListingLeadSource($request->groupIds, $request->duration);
            return view('admin.reports.v3.partials._listing_leads_source')
                ->with('groupdata', $data);
        }
        if ($request->type == 'daysWithoutDeal') {
            $data = $this->daysWithoutDeal($request->groupIds, $request->duration);
            return view('admin.reports.v3.partials._days_without_deal')
                ->with('groupdata', $data);
        }
        if ($request->type == 'meetingsCountPerDate') {
            $data = $this->meetingsCountPerDate($request->groupIds, $request->duration);
            // dd($data);
            return view('admin.reports.v3.partials._meetings_per_day')
                ->with('groupdata', $data);
        }

    }

    private function leadsOverview($groupIds, $duration)
    {
        $groups = Group::whereIn('id', $groupIds)->orderBy('order', 'asc')->get();
        foreach ($groups as $group) {
            $notAdmin = $group->user->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);
            $childrenIds = [];
            if ($notAdmin) {
                $childrenIds = getUserChildrenIdsByID($group->user->id);
            }
            $agents = User::where('status', 1)->whereHas("roles", function ($q) {
                $q->whereIn("name", ["Agent", "Line Manager", "Team Leader", "Sales Director", "Head of Secondary"]);
            })->when($notAdmin, function ($q) use ($childrenIds, $group) {
                $q->whereIn("parent_id", $childrenIds)
                    ->orWhere('id', $group->user->id);
            })->when($group->user->hasRole("Admin - Abu Dhabi"), fn($q) => $q->where("region_id", "2"))->get();

            // info("Leads New Status : ".Lead::whereIn("agent_id", $agents->pluck("id")->toArray())->where('lead_status_id',1)->count());

            $statusCountsQuery = Lead::where('status', 1)->where('is_archive', 0)->select('lead_status_id', DB::raw('COUNT(*) as count'))
                ->groupBy('lead_status_id')
                ->when($notAdmin, function ($q) use ($agents) {
                    $q->whereIn("agent_id", $agents->pluck("id")->toArray());
                });
            $statusCounts = $this->queryDecider($statusCountsQuery, 'created_at', $duration)->get(['lead_status_id', DB::raw('COUNT(*) as count')]);

            $leadCounts = [];
            $statusNames = [];
            foreach ($statusCounts as $statusCount) {
                $status_count[$statusCount->lead_status_id] = $statusCount->count;
                $eventType = LeadStatus::find($statusCount->lead_status_id);
                $statusNames[] = $eventType ? $eventType->name . ': ' . $statusCount->count : 'Null: ' . $statusCount->count;
                $leadCounts[] = $statusCount->count;
            }
            // rearrange the array to show the highest count first
            array_multisort($leadCounts, SORT_DESC, $statusNames);

            $groupdata[$group->id] = [
                'group_id' => $group->id,
                'group_name' => $group->name,
                'group_color' => $group->color,
                'statusNames' => $statusNames,
                'leadCounts' => $leadCounts,
            ];

        }
        return $groupdata;
    }
    public function ListingLeadSource($groupIds, $duration)
    {
        $groups = Group::whereIn('id', $groupIds)->orderBy('order', 'asc')->get();
        foreach ($groups as $group) {
            $notAdmin = $group->user->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);
            $childrenIds = [];
            if ($notAdmin) {
                $childrenIds = getUserChildrenIdsByID($group->user->id);
            }
            $agents = User::where('status', 1)->whereHas("roles", function ($q) {
                $q->whereIn("name", ["Agent", "Line Manager", "Team Leader", "Sales Director", "Head of Secondary"]);
            })->when($notAdmin, function ($q) use ($childrenIds, $group) {
                $q->whereIn("parent_id", $childrenIds)
                    ->orWhere('id', $group->user->id);
            })->when($group->user->hasRole("Admin - Abu Dhabi"), fn($q) => $q->where("region_id", "2"))->get();

            $baseQuery = ListingLead::select('source_id', DB::raw('COUNT(*) as count'))
                ->groupBy('source_id')
                ->when($notAdmin, function ($q) use ($agents) {
                    $q->whereIn("agent_id", $agents->pluck("id")->toArray());
                });
            $statusCounts = $this->queryDecider($baseQuery, 'created_at', $duration)->get(['source_id', DB::raw('COUNT(*) as count')]);

            $leadCounts = [];
            $statusNames = [];
            foreach ($statusCounts as $statusCount) {
                $status_count[$statusCount->source_id] = $statusCount->count;
                $eventType = Source::find($statusCount->source_id);
                $statusNames[] = $eventType ? $eventType->name . ': ' . $statusCount->count : 'Null: ' . $statusCount->count;
                $leadCounts[] = $statusCount->count;
            }
            array_multisort($leadCounts, SORT_DESC, $statusNames);

            $groupdata[$group->id] = [
                'group_id' => $group->id,
                'group_name' => $group->name,
                'group_color' => $group->color,
                'statusNames' => $statusNames,
                'leadCounts' => $leadCounts,
            ];
        }
        return $groupdata;
    }
    public function daysWithoutDeal($groupIds, $duration)
    {
        $groups = Group::whereIn('id', $groupIds)->orderBy('order', 'asc')->get();
        $groupdata = [];

        foreach ($groups as $group) {
            $notAdmin = $group->user->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);
            $childrenIds = [];

            if ($notAdmin) {
                $childrenIds = getUserChildrenIdsByID($group->user->id);
            }

            $agents = User::where('status', 1)
                ->whereHas("roles", function ($q) {
                    $q->whereIn("name", ["Agent", "Line Manager", "Team Leader", "Sales Director", "Head of Secondary"]);
                })
                ->when($notAdmin, function ($q) use ($childrenIds, $group) {
                    $q->whereIn("parent_id", $childrenIds)
                        ->orWhere('id', $group->user->id);
                })
                ->when($group->user->hasRole("Admin - Abu Dhabi"), fn($q) => $q->where("region_id", "2"))
                ->get();

            $agentNames = $agents->pluck('name')->toArray();
            $daysCount = $agents->map(function ($agent) {
                if ($agent->last_deal_date == null) {
                    return 0;
                }
                $lastDealDate = strtotime($agent->last_deal_date);
                $daysUntilToday = ceil((time() - $lastDealDate) / (60 * 60 * 24));
                return $daysUntilToday;
            })->toArray();

            $groupdata[$group->id] = [
                'group_id' => $group->id,
                'group_name' => $group->name,
                'group_color' => $group->color,
                'agentNames' => $agentNames,
                'daysCount' => $daysCount,
            ];
        }

        return $groupdata;
    }
    public function _meetingsCountPerDate($groupIds, $duration)
    {
        $groups = Group::whereIn('id', $groupIds)->orderBy('order', 'asc')->get();
        $groupdata = [];

        foreach ($groups as $group) {
            $notAdmin = $group->user->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);
            $childrenIds = [];

            if ($notAdmin) {
                $childrenIds = getUserChildrenIdsByID($group->user->id);
            }

            $agents = User::where('status', 1)
                ->whereHas("roles", function ($q) {
                    $q->whereIn("name", ["Agent", "Line Manager", "Team Leader", "Sales Director", "Head of Secondary"]);
                })
                ->when($notAdmin, function ($q) use ($childrenIds, $group) {
                    $q->whereIn("parent_id", $childrenIds)
                        ->orWhere('id', $group->user->id);
                })
                ->when($group->user->hasRole("Admin - Abu Dhabi"), fn($q) => $q->where("region_id", "2"))
                ->get();

            // Count events for each group where event_type_id is 2 and agent_id belongs only to agents
            $eventsCount = Event::whereBetween('start_time', [
                now()->subMonths(3)->startOfMonth(), // Previous 3 months
                now()->addMonths(3)->endOfMonth(), // Next 3 months
            ])
                ->where('event_type_id', 2)
                ->whereHas('agent', function ($query) use ($agents) {
                    $query->whereIn('id', $agents->pluck('id'));
                })
                ->count();

            $groupdata[$group->id] = [
                'group_id' => $group->id,
                'group_name' => $group->name,
                'group_color' => $group->color,
                'eventsCount' => $eventsCount, // Add events count to the result
            ];
        }

        return $groupdata;
    }

    public function ___meetingsCountPerDate($groupIds, $duration)
    {
        $groups = Group::whereIn('id', $groupIds)->orderBy('order', 'asc')->get();
        $groupdata = [];

        foreach ($groups as $group) {
            $notAdmin = $group->user->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);
            $childrenIds = [];

            if ($notAdmin) {
                $childrenIds = getUserChildrenIdsByID($group->user->id);
            }

            $agents = User::where('status', 1)
                ->whereHas("roles", function ($q) {
                    $q->whereIn("name", ["Agent", "Line Manager", "Team Leader", "Sales Director", "Head of Secondary"]);
                })
                ->when($notAdmin, function ($q) use ($childrenIds, $group) {
                    $q->whereIn("parent_id", $childrenIds)
                        ->orWhere('id', $group->user->id);
                })
                ->when($group->user->hasRole("Admin - Abu Dhabi"), fn($q) => $q->where("region_id", "2"))
                ->get();

            $eventsPerDay = [];
            $startDate = now()->subMonths(5)->startOfMonth();
            $endDate = now()->addMonths(1)->endOfMonth();
            $currentDate = clone $startDate;

            while ($currentDate <= $endDate) {
                // Count events for the current day
                $eventsCount = Event::whereDate('start_time', $currentDate)
                    ->where('event_type_id', 2)
                    ->whereHas('agent', function ($query) use ($agents) {
                        $query->whereIn('id', $agents->pluck('id'));
                    })
                    ->count();

                // Store the count for the current day
                $eventsPerDay[$currentDate->format('Y-m-d')] = $eventsCount;

                // Move to the next day
                $currentDate->addDay();
            }

            $groupdata[$group->id] = [
                'group_id' => $group->id,
                'group_name' => $group->name,
                'group_color' => $group->color,
                'eventsPerDay' => $eventsPerDay, // Add events count per day to the result
            ];
        }

        return $groupdata;
    }

    public function meetingsCountPerDate($groupIds, $duration)
{
    $groups = Group::whereIn('id', $groupIds)->orderBy('order', 'asc')->get();
    $groupdata = [];

    foreach ($groups as $group) {
        $notAdmin = $group->user->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);
        $childrenIds = [];

        if ($notAdmin) {
            $childrenIds = getUserChildrenIdsByID($group->user->id);
        }

        $agents = User::where('status', 1)
            ->whereHas("roles", function ($q) {
                $q->whereIn("name", ["Agent", "Line Manager", "Team Leader", "Sales Director", "Head of Secondary"]);
            })
            ->when($notAdmin, function ($q) use ($childrenIds, $group) {
                $q->whereIn("parent_id", $childrenIds)
                    ->orWhere('id', $group->user->id);
            })
            ->when($group->user->hasRole("Admin - Abu Dhabi"), fn($q) => $q->where("region_id", "2"))
            ->get();

        $eventsPerDay = [];
        $viewsPerDay = []; // New array for views per day
        $startDate = now()->subMonths(5)->startOfMonth();
        $endDate = now()->addMonths(1)->endOfMonth();
        $currentDate = clone $startDate;

        while ($currentDate <= $endDate) {
            // Count events for the current day with event_type_id = 1
            $eventsCount = Event::whereDate('start_time', $currentDate)
                ->where('event_type_id', 1) // Filter for event_type_id = 1
                ->whereHas('agent', function ($query) use ($agents) {
                    $query->whereIn('id', $agents->pluck('id'));
                })
                ->count();

            // Count views for the current day with event_type_id = 2
            $viewsCount = Event::whereDate('start_time', $currentDate)
                ->where('event_type_id', 2) // Filter for event_type_id = 2
                ->whereHas('agent', function ($query) use ($agents) {
                    $query->whereIn('id', $agents->pluck('id'));
                })
                ->count();

            // Store the counts for the current day
            $eventsPerDay[$currentDate->format('Y-m-d')] = $eventsCount;
            $viewsPerDay[$currentDate->format('Y-m-d')] = $viewsCount;
            $currentDate->addDay();
        }

        $groupdata[$group->id] = [
            'group_id' => $group->id,
            'group_name' => $group->name,
            'group_color' => $group->color,
            'eventsPerDay' => $eventsPerDay, // Add events count per day to the result
            'viewsPerDay' => $viewsPerDay, // Add views count per day to the result
        ];
    }

    return $groupdata;
}


    public function listingLeadsStatsOverviewBlade(Request $request)
    {
        $group = Group::where('id', $request->group_id)->first();

        $agent = User::find($group->user->id);

        $notAdmin = $agent->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);
        $agentsQuery = User::where('status', true)
            ->whereHas("roles", function ($q) {
                $q->whereIn("name", ["Agent", "Sales Director", "Team Leader", "Line Manager"]);
            });

        if ($notAdmin) {
            $childrenIds = getUserChildrenIdsByID($agent->id);
            $agentsQuery->whereIn("id", $childrenIds);
        }

        $agents = $agentsQuery->orderBy('first_name', 'asc')->get();
        $agentIds = $agents->pluck('id')->toArray();
        $baseQuery = ListingLead::where('agent_id', $agentIds)->where('is_archive', 0);

        $leads_received_query = ListingLead::whereIn('agent_id', $agentIds)->where('is_archive', 0);

        $leads_received = $this->queryDecider($leads_received_query, 'created_at', $request->duration)->count();

        $leads_received_thirty_query = clone $baseQuery;

        $leads_received_thirty = ListingLead::whereIn('agent_id', $agentIds)
            ->where('is_archive', 0)
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->count();

        $leadsPerDay = [];

        foreach ($agentIds as $agentId) {
            $averageLeads_query = ListingLead::where('agent_id', $agentId)->where('is_archive', 0)
                ->selectRaw('COUNT(*) as count, DATE(created_at) as date')
                ->groupBy('date');
            $averageLeads = $this->queryDecider($averageLeads_query, 'created_at', $request->duration)->get();

            $totalLeads = $averageLeads->sum('count');
            $totalDays = $averageLeads->count();

            if ($totalDays > 0) {
                $averageLeadsPerDay = $totalLeads / $totalDays;
                $leadsPerDay[$agentId] = $averageLeadsPerDay;
            } else {
                $leadsPerDay[$agentId] = 0; // Handle the case where there are no leads for an agent.
            }
        }

        $averageLeadsPerDay = array_sum($leadsPerDay) / count($leadsPerDay);
        $averageLeadsPerDay = number_format((float) $averageLeadsPerDay, 2, '.', '');

        $successfulLeads = ListingLead::whereIn('agent_id', $agentIds)
            ->where('status_id', 5)
            ->where('is_archive', 0)
            ->count();

        $successfulLeads_thirty = ListingLead::whereIn('agent_id', $agentIds)
            ->where('status_id', 5)
            ->where('is_archive', 0)
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->count();

        if ($leads_received > 0) {
            $successRate = ($successfulLeads / $leads_received) * 100;

            $successRate_thirty = ($successfulLeads_thirty / $leads_received_thirty) * 100;

        } else {
            $successRate = 0;
            $successRate_thirty = 0;
        }
        $successRate = number_format((float) $successRate, 2, '.', '');
        $successRate_thirty = number_format((float) $successRate_thirty, 2, '.', '');

        $super_agent = User::whereIn('id', $agentIds)
            ->withCount(['listingLeads' => function ($query) use ($request) {
                $this->queryDecider($query, 'created_at', $request->duration);
            }])
            ->orderByDesc('listing_leads_count')
            ->first();

        return view('admin.reports.details.group_listing_leads_overview')
            ->with('group', $group)
            ->with('agents', $agents)
            ->with('averageLeadsPerDay', $averageLeadsPerDay)
            ->with('leads_received', $leads_received)
            ->with('leads_received_thirty', $leads_received_thirty)
            ->with('successfulLeads', $successfulLeads)
            ->with('successfulLeads_thirty', $successfulLeads_thirty)
            ->with('successRate', $successRate)
            ->with('successRate_thirty', $successRate_thirty)
            ->with('super_agent', $super_agent);

    }
    private function getGroupBasicStats($groupIds, $duration)
    {
        $groups = Group::whereIn('id', $groupIds)->orderBy('order', 'asc')->get();
        foreach ($groups as $group) {

            $notAdmin = $group->user->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);
            $childrenIds = [];
            if ($notAdmin) {
                $childrenIds = getUserChildrenIdsByID($group->user->id);
            }
            $agents = User::where('status', 1)->whereHas("roles", function ($q) {
                $q->whereIn("name", ["Agent", "Line Manager", "Team Leader", "Sales Director", "Head of Secondary"]);
            })->when($notAdmin, function ($q) use ($childrenIds, $group) {
                $q->whereIn("parent_id", $childrenIds)
                    ->orWhere('id', $group->user->id);
            })->when($group->user->hasRole("Admin - Abu Dhabi"), fn($q) => $q->where("region_id", "2"))->get();

            $propertiesquery = Property::where('is_archived', 0)->whereNot('completion_status', 'Off-Plan Primary')->whereIn("agent_id", $agents->pluck("id")->toArray());
            $properties = $this->queryDecider($propertiesquery, 'created_at', $duration)->get();

            $groupedProperties = $properties->groupBy("publish_status");
            $groupedProperties = $groupedProperties->map(function ($item, $key) {
                return $item->count();
            });

            $sortedArray = $groupedProperties->toArray();
            arsort($sortedArray);
            $groupedProperties = collect($sortedArray);

            $leadquery = Lead::where('status', 1)->where('is_archive', 0)->whereIn("agent_id", $agents->pluck("id")->toArray());
            $leads = $this->queryDecider($leadquery, 'created_at', $duration)->get();
            $groupedLeads = $leads->groupBy("lead_status_id");
            $groupedLeads = $groupedLeads->map(function ($item, $key) {
                return $item->count();
            });

            $sortedArray = $groupedLeads->toArray();
            arsort($sortedArray);
            $groupedLeads = collect($sortedArray);

            $listingleadquery = ListingLead::where('is_archive', 0)->whereIn("agent_id", $agents->pluck("id")->toArray());
            $listingleads = $this->queryDecider($listingleadquery, 'created_at', $duration)->get();
            $groupedListingLeads = $listingleads->groupBy("status_id");
            $groupedListingLeads = $groupedListingLeads->map(function ($item, $key) {
                return $item->count();
            });

            $sortedArray = $groupedListingLeads->toArray();
            arsort($sortedArray);
            $groupedListingLeads = collect($sortedArray);

            $contactsquery = Contact::whereIn("agent_id", $agents->pluck("id")->toArray());
            $contacts = $this->queryDecider($contactsquery, 'created_at', $duration)->get();

            $meta_warning_query = UserMeta::whereIn("user_id", $agents->pluck("id")->toArray())->where('tag', 'lead_inactivity_warning');
            $meta_suspension_query = UserMeta::whereIn("user_id", $agents->pluck("id")->toArray())->where('tag', 'lead_inactivity_deactivate');
            // $groupedContacts = $leads->groupBy("contact_type_id");

            // $groupedContacts = $groupedContacts->map(function ($item, $key) {
            //     return $item->count();
            // });
            $warnings = $this->queryDecider($meta_warning_query, 'created_at', $duration)->count();
            $suspensions = $this->queryDecider($meta_suspension_query, 'created_at', $duration)->count();

            // info('Group Name' . $group->name . '--' . $group->user->full_name . '--' . $group->user->id . '--W-' . $warnings . '--S-' . $suspensions . '--A-' . $agents->count());

            $totalAgents = $agents->count();

            $warningsPercentage = ($warnings / $totalAgents) * 100;
            $suspensionsPercentage = ($suspensions / $totalAgents) * 100;
            // info('Group Name' . $group->name . '--' . $group->user->full_name . '--' . $group->user->id . '--W-' . $warningsPercentage . '--S-' . $suspensionsPercentage . '--A-' . $agents->count());
            //  only 2 decimal places
            $warningsPercentage = number_format((float) $warningsPercentage, 0, '.', '');
            $suspensionsPercentage = number_format((float) $suspensionsPercentage, 0, '.', '');

            $groupdata[$group->id] = [
                'group_id' => $group->id,
                'group_name' => $group->name,
                'group_color' => $group->color,
                'Group Name' => $group->user->full_name,
                'property' => $properties->count(),
                'property_status' => $groupedProperties,
                'leads' => $leads->count(),
                'leads_status' => $groupedLeads,
                'listingleads' => $listingleads->count(),
                'listingleads_status' => $groupedListingLeads,
                'contacts' => $contacts->count(),
                'warnings' => $warnings,
                'suspensions' => $suspensions,
                'warnings_rate' => $warningsPercentage . '%',
                'suspensions_rate' => $suspensionsPercentage . '%',
            ];
        }
        return $groupdata;
    }
    private function leadsSourceOverview($groupIds, $duration)
    {
        $groups = Group::whereIn('id', $groupIds)->orderBy('order', 'asc')->get();
        foreach ($groups as $group) {
            $notAdmin = $group->user->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);
            $childrenIds = [];
            if ($notAdmin) {
                $childrenIds = getUserChildrenIdsByID($group->user->id);
            }
            $agents = User::where('status', 1)->whereHas("roles", function ($q) {
                $q->whereIn("name", ["Agent", "Line Manager", "Team Leader", "Sales Director", "Head of Secondary"]);
            })->when($notAdmin, function ($q) use ($childrenIds, $group) {
                $q->whereIn("parent_id", $childrenIds)
                    ->orWhere('id', $group->user->id);
            })->when($group->user->hasRole("Admin - Abu Dhabi"), fn($q) => $q->where("region_id", "2"))->get();

            $statusCountsQuery = Lead::select('source_id', DB::raw('COUNT(*) as count'))
                ->groupBy('source_id')
                ->when($notAdmin, function ($q) use ($agents) {
                    $q->whereIn("agent_id", $agents->pluck("id")->toArray());
                });
            $statusCounts = $this->queryDecider($statusCountsQuery, 'created_at', $duration)->get(['source_id', DB::raw('COUNT(*) as count')]);

            $leadCounts = [];
            $sourceNames = [];
            foreach ($statusCounts as $statusCount) {
                $status_count[$statusCount->source_id] = $statusCount->count;
                $eventType = Source::find($statusCount->source_id);
                $sourceNames[] = $eventType ? $eventType->name . ': ' . $statusCount->count : 'Null: ' . $statusCount->count;
                $leadCounts[] = $statusCount->count;
            }

            array_multisort($leadCounts, SORT_DESC, $sourceNames);

            $groupdata[$group->id] = [
                'group_id' => $group->id,
                'group_name' => $group->name,
                'group_color' => $group->color,
                'sourceNames' => $sourceNames,
                'leadCounts' => $leadCounts,
            ];

        }
        return $groupdata;
    }
    private function _listingOverview($groupIds, $duration)
    {
        $groups = Group::whereIn('id', $groupIds)->orderBy('order', 'asc')->get();
        foreach ($groups as $group) {
            $notAdmin = $group->user->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);
            $childrenIds = [];
            if ($notAdmin) {
                $childrenIds = getUserChildrenIdsByID($group->user->id);
            }
            $agents = User::where('status', 1)->whereHas("roles", function ($q) {
                $q->whereIn("name", ["Agent", "Line Manager", "Team Leader", "Sales Director", "Head of Secondary"]);
            })->when($notAdmin, function ($q) use ($childrenIds, $group) {
                $q->whereIn("parent_id", $childrenIds)
                    ->orWhere('id', $group->user->id);
            })->when($group->user->hasRole("Admin - Abu Dhabi"), fn($q) => $q->where("region_id", "2"))->get();

            $statusCountsQuery = Property::where('is_archived', 0)->whereNot('completion_status', 'Off-Plan Primary')->select('publish_status', DB::raw('COUNT(*) as count'))
                ->groupBy('publish_status')
                ->when($notAdmin, function ($q) use ($agents) {
                    $q->whereIn("agent_id", $agents->pluck("id")->toArray());
                });
            $statusCounts = $this->queryDecider($statusCountsQuery, 'created_at', $duration)->get(['publish_status', DB::raw('COUNT(*) as count')]);

            foreach ($statusCounts as $statusCount) {
                $status_count[$statusCount->publish_status] = $statusCount->count ?? 0;
            }

            $groupdata[$group->id] = [
                'group_id' => $group->id,
                'group_name' => $group->name,
                'group_color' => $group->color,
                'published' => $status_count['PUBLISHED'] ?? 0,
                'pocket' => $status_count['POCKET'] ?? 0,
                'unpublished' => $status_count['UNPUBLISHED'] ?? 0,
                'draft' => $status_count['DRAFT'] ?? 0,
            ];

        }

        return $groupdata;
    }
    private function listingOverview($groupIds, $duration)
    {
        $groups = Group::whereIn('id', $groupIds)->orderBy('order', 'asc')->get();
        foreach ($groups as $group) {
            $notAdmin = $group->user->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);
            $childrenIds = [];
            if ($notAdmin) {
                $childrenIds = getUserChildrenIdsByID($group->user->id);
            }
            $agents = User::where('status', 1)->whereHas("roles", function ($q) {
                $q->whereIn("name", ["Agent", "Line Manager", "Team Leader", "Sales Director", "Head of Secondary"]);
            })->when($notAdmin, function ($q) use ($childrenIds, $group) {
                $q->whereIn("parent_id", $childrenIds)
                    ->orWhere('id', $group->user->id);
            })->when($group->user->hasRole("Admin - Abu Dhabi"), fn($q) => $q->where("region_id", "2"))->get();

            $statusCountsQuery = Property::where('is_archived', 0)->whereNot('completion_status', 'Off-Plan Primary')->select('publish_status', DB::raw('COUNT(*) as count'))
                ->groupBy('publish_status')
                ->when($notAdmin, function ($q) use ($agents) {
                    $q->whereIn("agent_id", $agents->pluck("id")->toArray());
                });
            $statusCounts = $this->queryDecider($statusCountsQuery, 'created_at', $duration)->get(['publish_status', DB::raw('COUNT(*) as count')]);

            foreach ($statusCounts as $statusCount) {
                $status_count[$statusCount->publish_status] = $statusCount->count ?? 0;
            }

            $groupdata[$group->id] = [
                'group_id' => $group->id,
                'group_name' => $group->name,
                'group_color' => $group->color,
                'published' => $status_count['PUBLISHED'] ?? 0,
                'pocket' => $status_count['POCKET'] ?? 0,
                'unpublished' => $status_count['UNPUBLISHED'] ?? 0,
                'draft' => $status_count['DRAFT'] ?? 0,
            ];

        }

        return $groupdata;
    }
    private function getLocationStats($groupIds, $duration)
    {
        $groups = Group::whereIn('id', $groupIds)->orderBy('order', 'asc')->get();
        foreach ($groups as $group) {
            $agent = User::find($group->user->id);

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
            $managers_locations = UserLocation::where('user_id', $agent->id)
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
            $locationNames = [];
            $propertyCounts = [];

            foreach ($locations as $location) {
                $locationNames[] = $location->name;

                $totalCount = 0;
                foreach ($users as $user) {
                    $baseQuery = Property::where('agent_id', $user->id)
                        ->where('community_id', $location->id);
                    $query = $this->queryDecider($baseQuery, 'created_at', $duration);

                    $count = $query->count();
                    $totalCount += $count;
                }

                $base_not_query = Property::where('agent_id', $user->id)->whereNotIn('community_id', $locationIds);
                $not_query = $this->queryDecider($base_not_query, 'created_at', $duration);

                $notcount = $not_query->count();
                $totalCount += $notcount;

                $propertyCounts[] = $totalCount;
            }
            array_multisort($propertyCounts, SORT_DESC, $locationNames);
            $groupdata[$group->id] = [
                'group_id' => $group->id,
                'group_name' => $group->name,

                'group_color' => $group->color,
                'group_color' => $group->color,
                'locationNames' => $locationNames,
                'propertyCounts' => $propertyCounts,
            ];

            // dd($groupdata[$group->id]);

        }
        return $groupdata;
    }
    private function propertyQuality($groupIds, $duration)
    {

        $groups = Group::whereIn('id', $groupIds)->orderBy('order', 'asc')->get();
        $portal_pf = Portal::where('name', 'Property Finder')->first();
        $portal_bayut = Portal::where('name', 'bayut')->first();
        $portal_dubizzle = Portal::where('name', 'Dubizzle')->first();

        foreach ($groups as $group) {
            $notAdmin = $group->user->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);
            $childrenIds = [];
            if ($notAdmin) {
                $childrenIds = getUserChildrenIdsByID($group->user->id);
            }
            $agents = User::where('status', 1)->whereHas("roles", function ($q) {
                $q->whereIn("name", ["Agent", "Line Manager", "Team Leader", "Sales Director", "Head of Secondary"]);
            })->when($notAdmin, function ($q) use ($childrenIds, $group) {
                $q->whereIn("parent_id", $childrenIds)
                    ->orWhere('id', $group->user->id);
            })->when($group->user->hasRole("Admin - Abu Dhabi"), fn($q) => $q->where("region_id", "2"))->get();

            //calculate average of pf_rank
            $query = Property::whereNot('completion_status', 'Off-Plan Primary')->where('region_id', 1);
            $search_query = $this->queryDecider($query, 'properties.created_at', $duration);

            $pf_avg_rank = $search_query->join('property_meta', function ($join) {
                $join->on('properties.id', '=', 'property_meta.property_id')
                    ->where('property_meta.meta_key', '=', 'pf_rank');
            })->whereHas('portals', function ($q) use ($portal_pf) {
                $q->where('portal_id', $portal_pf->id);
            })->where('is_live', '1')->whereIn('agent_id', $agents->pluck("id")->toArray())->avg('property_meta.meta_value');
            $query = Property::whereNot('completion_status', 'Off-Plan Primary')->where('region_id', 1);
            $search_query = $this->queryDecider($query, 'properties.created_at', $duration);
            $bayut_avg_rank = $search_query->join('property_meta', function ($join) {
                $join->on('properties.id', '=', 'property_meta.property_id')
                    ->where('property_meta.meta_key', '=', 'bayut_rank');
            })->whereHas('portals', function ($q) use ($portal_bayut) {
                $q->where('portal_id', $portal_bayut->id);
            })->whereIn('agent_id', $agents->pluck("id")->toArray())->avg('property_meta.meta_value');
            $query = Property::whereNot('completion_status', 'Off-Plan Primary')->where('region_id', 1);
            $search_query = $this->queryDecider($query, 'properties.created_at', $duration);
            $dubizzle_avg_rank = $search_query->join('property_meta', function ($join) {
                $join->on('properties.id', '=', 'property_meta.property_id')
                    ->where('property_meta.meta_key', '=', 'dubizzle_rank');
            })->whereHas('portals', function ($q) use ($portal_dubizzle) {
                $q->where('portal_id', $portal_dubizzle->id);
            })->where('is_live', '1')->whereIn('agent_id', $agents->pluck("id")->toArray())->avg('property_meta.meta_value');

            $groupdata[$group->id] = [
                'group_id' => $group->id,
                'group_name' => $group->name,
                'group_color' => $group->color,
                'pf_avg_rank' => round($pf_avg_rank),
                'bayut_avg_rank' => round($bayut_avg_rank),
                'dubizzle_avg_rank' => round($dubizzle_avg_rank),
            ];

        }
        return $groupdata;
    }

    private function geteventsStats($groupIds, $duration)
    {
        $groups = Group::whereIn('id', $groupIds)->orderBy('order', 'asc')->get();
        foreach ($groups as $group) {
            $agent = User::find($group->user->id);

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
            $managers_locations = UserLocation::where('user_id', $agent->id)
                ->where('parentable_type', 'App\Models\Community')
                ->get();

            if ($agent->hasRole('Agent')) {
                $managers_locations = UserLocation::where('user_id', $agent->parent_id)->where('parentable_type', 'App\Models\Community')->get();
            }

            $agentIds = $agents->pluck('id')->toArray();

            // find all events for the agents and counts them
            $eventsQuery = Event::whereIn('agent_id', $agentIds)->where('status', true);
            $events = $this->queryDecider($eventsQuery, 'created_at', $duration)->get()->groupBy('event_type_id');

            $eventCounts = [];
            $eventNames = [];

            foreach ($events as $eventTypeId => $eventGroup) {
                $eventCounts[] = $eventGroup->count();
                $eventType = EventType::find($eventTypeId);
                $eventNames[] = $eventType ? $eventType->name : 'Null';
            }

            array_multisort($eventCounts, SORT_DESC, $eventNames);

            $groupdata[$group->id] = [
                'group_id' => $group->id,
                'group_name' => $group->name,
                'group_color' => $group->color,
                'group_color' => $group->color,
                'eventNames' => $eventNames,
                'eventCounts' => $eventCounts,
            ];

            // dd($groupdata[$group->id]);
        }
        return $groupdata;
    }

    public function listingLeadsOverview($groupIds, $duration)
    {
        $groups = Group::whereIn('id', $groupIds)->orderBy('order', 'asc')->get();
        foreach ($groups as $group) {
            $notAdmin = $group->user->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);
            $childrenIds = [];
            if ($notAdmin) {
                $childrenIds = getUserChildrenIdsByID($group->user->id);
            }
            $agents = User::where('status', 1)->whereHas("roles", function ($q) {
                $q->whereIn("name", ["Agent", "Line Manager", "Team Leader", "Sales Director", "Head of Secondary"]);
            })->when($notAdmin, function ($q) use ($childrenIds, $group) {
                $q->whereIn("parent_id", $childrenIds)
                    ->orWhere('id', $group->user->id);
            })->when($group->user->hasRole("Admin - Abu Dhabi"), fn($q) => $q->where("region_id", "2"))->get();

            // info(ListingLead::whereIn("agent_id", $agents->pluck("id")->toArray())->where('status_id',1)->count());

            $baseQuery = ListingLead::select('status_id', DB::raw('COUNT(*) as count'))
                ->groupBy('status_id')
                ->when($notAdmin, function ($q) use ($agents) {
                    $q->whereIn("agent_id", $agents->pluck("id")->toArray());
                });
            $statusCounts = $this->queryDecider($baseQuery, 'created_at', $duration)->get(['status_id', DB::raw('COUNT(*) as count')]);

            $leadCounts = [];
            $statusNames = [];
            foreach ($statusCounts as $statusCount) {
                $status_count[$statusCount->status_id] = $statusCount->count;
                $eventType = ListingLeadStatus::find($statusCount->status_id);
                $statusNames[] = $eventType ? $eventType->name . ': ' . $statusCount->count : 'Null: ' . $statusCount->count;
                $leadCounts[] = $statusCount->count;
            }
            array_multisort($leadCounts, SORT_DESC, $statusNames);

            $groupdata[$group->id] = [
                'group_id' => $group->id,
                'group_name' => $group->name,
                'group_color' => $group->color,
                'statusNames' => $statusNames,
                'leadCounts' => $leadCounts,
            ];
        }
        return $groupdata;
    }

    private function queryDecider($query, $column, $operator)
    {
        // custom&from=11-28-2023&to=11-28-2023
        if (strpos($operator, '&') !== false) {
            $operator = explode('&', $operator);
            $from = null;
            $to = null;
            foreach ($operator as $item) {
                if (strpos($item, 'from=') !== false) {
                    $from = Carbon::createFromFormat('m-d-Y', str_replace('from=', '', $item))->format('Y-m-d');
                } elseif (strpos($item, 'to=') !== false) {
                    $to = Carbon::createFromFormat('m-d-Y', str_replace('to=', '', $item))->format('Y-m-d');
                }
            }
            if ($from !== null && $to !== null) {
                return $query->whereBetween($column, [$from, $to]);
            } else {
                dd('Missing from or to date');
            }
        } else {

            return $this->queryBuilder($query, $column, $operator);
        }
    }

    private function queryBuilder($query, $column, $operator)
    {

        switch ($operator) {
            case 'today':
                return $query->whereDate($column, '=', now()->format('Y-m-d'));
            case 'yesterday':
                return $query->whereDate($column, '=', now()->subDay()->format('Y-m-d'));
            case 'last_month':
                $startOfMonth = now()->subMonth()->startOfMonth();
                $endOfMonth = now()->subMonth()->endOfMonth();
                return $query->whereBetween($column, [$startOfMonth, $endOfMonth]);
            case 'last_week':
                return $query->whereBetween($column, [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]);
            case 'this_month':
                return $query->whereYear($column, now()->year)->whereMonth($column, now()->month);
            case 'this_week':
                return $query->whereBetween($column, [now()->startOfWeek(), now()->endOfWeek()]);
            case 'last_7_days':
                return $query->whereBetween($column, [now()->subDays(7), now()]);
            case 'last_14_days':
                return $query->whereBetween($column, [now()->subDays(14), now()]);
            case 'last_21_days':
                return $query->whereBetween($column, [now()->subDays(21), now()]);
            case 'last_28_days':
                return $query->whereBetween($column, [now()->subDays(28), now()]);
            case 'last_30_days':
                return $query->whereBetween($column, [now()->subDays(30), now()]);
            case 'last_60_days':
                return $query->whereBetween($column, [now()->subDays(60), now()]);
            case 'last_90_days':
                return $query->whereBetween($column, [now()->subDays(90), now()]);
            case 'last_120_days':
                return $query->whereBetween($column, [now()->subDays(120), now()]);
            case 'last_180_days':
                return $query->whereBetween($column, [now()->subDays(180), now()]);
            case 'last_365_days':
                return $query->whereBetween($column, [now()->subDays(365), now()]);
            case 'custom':
                return $query->whereBetween($column, [now()->subDays($operator), now()]);
            default:
                return $query;
                throw new \InvalidArgumentException('Invalid operator: ' . $operator);
        }
    }
}
