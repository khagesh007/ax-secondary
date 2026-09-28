<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Models\Community;
use App\Models\Contact;
use App\Models\Event;
use App\Models\EventType;
use App\Models\Group;
use App\Models\Lead;
use App\Models\LeadNote;
use App\Models\LeadStatus;
use App\Models\ListingLead;
use App\Models\ListingLeadStatus;
use App\Models\Medium;
use App\Models\Portal;
use App\Models\Property;
use App\Models\PropertyNote;
use App\Models\Source;
use App\Models\Type;
use App\Models\User;
use App\Models\UserLocation;
use App\Models\UserMeta;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class GroupReportController extends Controller
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

        return view('admin.reports.group')
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

        // dd($this->eventsByAgents($request->groupIds, $request->duration));

        if ($request->type == 'stats') {
            $data = $this->getPropertiesStatus($request->groupIds, $request->duration);
            return view('admin.reports.partials.groups._overall_stats')
                ->with('groupdata', $data);
        }
        if ($request->type == 'propertyStats') {
            $data = $this->listingOverview($request->groupIds, $request->duration);
            return view('admin.reports.partials.groups._listing_stats')
                ->with('groupdata', $data);
        }
        if ($request->type == 'propertyQuality') {
            $data = $this->propertyQuality($request->groupIds, $request->duration);
            return view('admin.reports.partials.groups._listing_quality')
                ->with('groupdata', $data);
        }

        if ($request->type == 'propertyLevels') {
            $data = $this->propertyLevels($request->groupIds, $request->duration);
            return view('admin.reports.partials.groups._listing_portals_levels')
                ->with('groupdata', $data);
        }

        if ($request->type == 'userMetaData') {
            $data = $this->userMetaData($request->groupIds, $request->duration);
            return view('admin.reports.partials.groups._meta_stats')
                ->with('groupdata', $data);
        }

        if ($request->type == 'portalStats') {
            $data = $this->portalsStatus($request->groupIds, $request->duration);
            return view('admin.reports.partials.groups._portal_stats')
                ->with('groupdata', $data);
        }
        if ($request->type == 'eventsStats') {
            $data = $this->geteventsStats($request->groupIds, $request->duration);
            return view('admin.reports.partials.groups._events_stats')
                ->with('groupdata', $data);
        }

        if ($request->type == 'locationStats') {
            $data = $this->getLocationStats($request->groupIds, $request->duration);
            return view('admin.reports.partials.groups._area_wise')
                ->with('groupdata', $data);
        }

        if ($request->type == 'propertySplit') {
            $listingStats = $this->listingStatsOverview($request->groupIds, $request->duration);

            $primary_properties = $listingStats['primary_properties'];
            $secondary_properties = $listingStats['secondary_properties'];

            $truePublishions = $this->getTruePublish($request->groupIds, $request->duration);
            $getForStatus = $this->getForStatus($request->groupIds, $request->duration);

            $commercial_for_rent = $getForStatus['commercial_for_rent'];
            $commercial_for_sale = $getForStatus['commercial_for_sale'];
            $residential_for_rent = $getForStatus['residential_for_rent'];
            $residential_for_sale = $getForStatus['residential_for_sale'];

            return view('admin.reports.partials.groups._listing_overview')
                ->with('primary_properties', $primary_properties)
                ->with('secondary_properties', $secondary_properties)
                ->with('truePublishions', $truePublishions)
                ->with('commercial_for_rent', $commercial_for_rent)
                ->with('commercial_for_sale', $commercial_for_sale)
                ->with('residential_for_rent', $residential_for_rent)
                ->with('residential_for_sale', $residential_for_sale);
        }

        if ($request->type == 'userMetaSplit') {
            $userStats = $this->userMetaSplit($request->groupIds, $request->duration);
            $warnings = $userStats['warnings'];
            $suspensions = $userStats['suspensions'];
            $warningsPercentage = $userStats['warningsPercentage'];
            $suspensionsPercentage = $userStats['suspensionsPercentage'];
            $systemUsers = $userStats['systemUsers'];
            $topAgents = $userStats['topAgents'];
            $leadsAgents = $userStats['leadsAgents'];
            return view('admin.reports.partials.groups._users_overview')
                ->with('warnings', $warnings)
                ->with('suspensions', $suspensions)
                ->with('warningsPercentage', $warningsPercentage)
                ->with('suspensionsPercentage', $suspensionsPercentage)
                ->with('systemUsers', $systemUsers)
                ->with('topAgents', $topAgents)
                ->with('leadsAgents', $leadsAgents);

        }
        if ($request->type == 'whatsappAverageResponse') {
            $userStats = $this->whatsappAverageResponse($request->groupIds, $request->duration);

        }

        if ($request->type == 'LeadStatus') {

            $data = $this->leadsOverview($request->groupIds, $request->duration);
            return view('admin.reports.partials.groups._leads_status')
                ->with('groupdata', $data);
        }

        if ($request->type == 'userPropertyMetaReport') {
            $bayut = $this->userPropertyMetaReportBayut($request->groupIds, $request->duration);
            $dubizzle = $this->userPropertyMetaReportDubizzle($request->groupIds, $request->duration);
            $propertyfinder = $this->userPropertyMetaReportPropertyFinder($request->groupIds, $request->duration);
            $data = [
                'propertyfinder' => $propertyfinder,
                'bayut' => $bayut,
                'dubizzle' => $dubizzle,

            ];
            return response()->json(['status' => '1', 'data' => $data]);
            return view('admin.reports.partials.groups._portals_meta_user')->with('groupdata', $data);
        }

        if ($request->type == 'leadsSources') {
            $data = $this->leadsSourceOverview($request->groupIds, $request->duration);
            return view('admin.reports.partials.groups._lead_source')
                ->with('groupdata', $data);
        }

        if ($request->type == 'ListingLeadStatus') {
            $data = $this->listingLeadsOverview($request->groupIds, $request->duration);
            return view('admin.reports.partials.groups._listingleads_status')
                ->with('groupdata', $data);
        }
        if ($request->type == 'ListingLeadSource') {
            $data = $this->ListingLeadSource($request->groupIds, $request->duration);
            // dd($data);
            return view('admin.reports.partials.groups._listingleads_source')
                ->with('groupdata', $data);
        }

        if ($request->type == 'leadsSplit') {
            $data = $this->LeadsSplitOverview($request->groupIds, $request->duration);
            $statusNames = $data['statusNames'];
            $leadCounts = $data['leadCounts'];

            array_multisort($leadCounts, SORT_DESC, $statusNames);

            $sourceNames = $data['sourceNames'];
            $leadSourceCounts = $data['leadSourceCounts'];

            $typeNames = $data['typeNames'];
            $leadTypeCounts = $data['leadTypeCounts'];

            $mediumNames = $data['mediumNames'];
            $leadMediumCounts = $data['leadMediumCounts'];
            return response()->json(['status' => '1', 'data' => $data]);

            return view('admin.reports.partials.groups._leads_overview')
                ->with('statusNames', $statusNames)
                ->with('leadCounts', $leadCounts)
                ->with('sourceNames', $sourceNames)
                ->with('leadSourceCounts', $leadSourceCounts)
                ->with('typeNames', $typeNames)
                ->with('leadTypeCounts', $leadTypeCounts)
                ->with('mediumNames', $mediumNames)
                ->with('leadMediumCounts', $leadMediumCounts);
        }
        if ($request->type == 'listingleadsSplit') {
            $data = $this->listingLeadsSplitOverview($request->groupIds, $request->duration);
            return response()->json(['status' => '1', 'data' => $data]);

        }

        if ($request->type == 'leadsSplitScatter') {
            $data = $this->leadsSplitScatter($request->groupIds, $request->duration);
            return response()->json(['status' => '1', 'data' => $data]);
        }
        if ($request->type == 'listingLeadsSplitScatter') {
            $data = $this->listingLeadsSplitScatter($request->groupIds, $request->duration);
            return response()->json(['status' => '1', 'data' => $data]);
        }

        if ($request->type == 'listingLeadsSplitScatterSource') {
            $data = $this->listingLeadsSplitScatterSource($request->groupIds, $request->duration);
            return response()->json(['status' => '1', 'data' => $data]);
        }

        if ($request->type == 'leadsStatusScatter') {
            $data = $this->leadsStatusScatter($request->groupIds);
            // return response()->json(['status' => '1', 'data' => $data]);
            return view('admin.reports.partials.groups._leads_status_scatter')
                ->with('datasets', $data);
        }

        if ($request->type == 'getAvgResponseRateOfAgents') {
            $data = $this->getAvgResponseRateOfAgents($request->groupIds, $request->duration);
            return view('admin.reports.partials.groups._avg_response_rate_table')
                ->with('datasets', $data);
        }
        if ($request->type == 'getTotalNumberOfWarningsandSuspensions') {
            $data = $this->getTotalNumberOfWarningsandSuspensions($request->groupIds, $request->duration);
            return view('admin.reports.partials.groups._agents_termination_report')
                ->with('datasets', $data);
        }
        if ($request->type == 'eventsByAgents') {
            $data = $this->eventsByAgents($request->groupIds, $request->duration);
            return response()->json(['status' => '1', 'data' => $data]);
        }

    }

    private function getPropertiesStatus($groupIds, $duration)
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

    private function userMetaData($groupIds, $duration)
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

            $meta_warning_query = UserMeta::whereIn("user_id", $agents->pluck("id")->toArray())->where('tag', 'lead_inactivity_warning');
            $meta_suspension_query = UserMeta::whereIn("user_id", $agents->pluck("id")->toArray())->where('tag', 'lead_inactivity_deactivate');

            $warnings = $this->queryDecider($meta_warning_query, 'created_at', $duration)->count();
            $suspensions = $this->queryDecider($meta_suspension_query, 'created_at', $duration)->count();

            $totalAgents = $agents->count();
            $warningsPercentage = ($warnings / $totalAgents) * 100;
            $suspensionsPercentage = ($suspensions / $totalAgents) * 100;
            $warningsPercentage = number_format((float) $warningsPercentage, 0, '.', '');
            $suspensionsPercentage = number_format((float) $suspensionsPercentage, 0, '.', '');

            $groupdata[$group->id] = [
                'group_id' => $group->id,
                'group_name' => $group->name,
                'group_color' => $group->color,
                'warnings' => $warnings,
                'suspensions' => $suspensions,
                'warningsPercentage' => $warningsPercentage,
                'suspensionsPercentage' => $suspensionsPercentage,
            ];

        }

        return $groupdata;
    }

    private function testFunction()
    {
        return $this->listingOverview(3);
    }

    private function portalsStatus($groupIds, $duration)
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

            $portalWiseCounts = Portal::where('status', true)
                ->withCount(['properties' => function ($query) use ($notAdmin, $agents, $duration) {
                    $query->where('is_live', '1');
                    $query->whereNot('completion_status', 'Off-Plan Primary');

                    if ($notAdmin) {
                        $query = $this->queryDecider($query, 'properties.created_at', $duration); // Modify this based on your requirement
                        $query->whereIn('agent_id', $agents->pluck("id")->toArray());
                    }
                }])->get(['id', 'name', 'thumbnail']);
            // $portalWiseCounts = $this->queryDecider($portalWiseCountsQuery, 'created_at', $duration)->get(['id', 'name', 'thumbnail']);
            $groupdata[$group->id] = $portalWiseCounts;

        }
        return $groupdata;
    }

    private function agentListingReport($groupIds)
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

            foreach ($users as $user) {
                foreach ($locations as $location) {
                    $query = Property::where('agent_id', $user->id)
                        ->where('community_id', $location->id);
                    // if ($request->portal) {
                    //     $query->whereHas('portals', function ($q) use ($request) {
                    //         $q->where('portal_id', $request->portal);
                    //     });
                    // }

                    // if ($request->status) {
                    //     $query->where('publish_status', $request->status);
                    // }
                    // if ($request->is_live) {
                    //     $query->where('is_live', $request->is_live);
                    // }

                    $count = $query->count();
                    $agentLocationCounts[$user->name][$location->name] = $count;
                }

                $not_query = Property::where('agent_id', $user->id)
                    ->whereNotIn('community_id', $locationIds);

                // if ($request->status) {
                //     $not_query->where('publish_status', $request->status);
                // }
                // if ($request->is_live) {
                //     $not_query->where('is_live', 1);
                // }
                // if ($request->portal) {
                //     $not_query->whereHas('portals', function ($q) use ($request) {
                //         $q->where('portal_id', $request->portal);
                //     });
                // }

                $notcount = $not_query->count();

                $agentLocationCounts[$user->name]['Others'] = $notcount;
            }

            $groupdata[$group->id] = [
                'agentLocationCounts' => $agentLocationCounts,
                'agents' => $agents,
                'locations' => $locations,
                // 'status' => $request->status,
                'portals' => Portal::where('status', 1)->get(),
                // 'selected_portal' => $request->portal ?? null,
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

    public function locationOverview(Request $request)
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
        return view('admin.reports.details.group_location_overview')
            ->with('group', $group)
            ->with('agents', $agents);
    }

    public function locationOverviewFetch(Request $request)
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

        foreach ($users as $user) {
            foreach ($locations as $location) {
                $query = Property::where('agent_id', $user->id)->where('community_id', $location->id);

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
                $query = $this->queryDecider($query, 'created_at', $request->duration);
                $count = $query->count();
                $agentLocationCounts[$user->name][$location->name] = $count;
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
            $not_query = $this->queryDecider($not_query, 'created_at', $request->duration);
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
            // 'portalWiseCounts' => $portalWiseCounts,
        ]);
    }

    public function eventsOverview(Request $request)
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
        return view('admin.reports.details.group_events_overview')
            ->with('group', $group)
            ->with('agents', $agents);
    }

    public function eventsMonthlyOverview(Request $request)
    {
        // dd($request->all());
        $group = Group::where('id', $request->group_id)->first();
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

        // Get the current date.
        $currentDate = Carbon::now();

        // Create an array to store the labels (months) for the last 12 months.
        $labels = [];

        // Create an array to store event data for each month.
        $eventData = [];

        for ($i = 0; $i < 12; $i++) {
            $currentDate = now(); // Assuming you're using Carbon or a similar library for date manipulation
            $labels = [];
            $eventData = [];

            for ($i = 0; $i < 12; $i++) {
                $monthYear = $currentDate->format('Y-m'); // Format as "YYYY-MM"
                $labels[] = $monthYear;

                $events = Event::select('event_types.name as event_type_name')
                    ->join('event_types', 'events.event_type_id', '=', 'event_types.id')
                    ->whereYear('events.created_at', $currentDate->year)
                    ->whereMonth('events.created_at', $currentDate->month)
                    ->whereIn("agent_id", $agents->pluck("id")->toArray())->get();

                $eventTypeCounts = [];

                foreach ($events as $event) {
                    $eventTypeName = $event->event_type_name;
                    if (!isset($eventTypeCounts[$eventTypeName])) {
                        $eventTypeCounts[$eventTypeName] = 1;
                    } else {
                        $eventTypeCounts[$eventTypeName]++;
                    }
                }

                $eventData[$monthYear] = $eventTypeCounts;

                // Move to the previous month for the next iteration
                $currentDate = $currentDate->subMonths(1);
            }
        }

        return [
            'labels' => $labels, // Labels for X-axis (months).
            'data' => $eventData, // Data for each month.
        ];

        $data['eventsMonthly'] = [
            'labels' => $labels, // Labels for X-axis (months).
            'data' => $eventData, // Data for each month.
        ];

        return response()->json(['status' => '1', 'data' => $data]);

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

    public function listingStatsOverviewBlade(Request $request)
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

        return view('admin.reports.details.group_listings_overview')
            ->with('group', $group)
            ->with('agents', $agents);
    }

    public function UserStatsOverviewBlade(Request $request)
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

        return view('admin.reports.details.group_user_overview')
            ->with('group', $group)
            ->with('agents', $agents);
    }

    public function leadsStatsOverviewBlade(Request $request)
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

        $baseQuery = Lead::whereIn('agent_id', $agentIds)->where('status', 1)->where('is_archive', 0);
        $whatsapp_avg_query = clone $baseQuery;

        $whatsapp_avg = $this->queryDecider($whatsapp_avg_query, 'leads.created_at', $request->duration)
            ->join('shortlinks', 'leads.id', '=', 'shortlinks.lead_id')
            ->whereNotNull('shortlinks.opted_in')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(SECOND, shortlinks.created_at, shortlinks.opted_in)) as average_diff'))
            ->value('average_diff');

        $total_leads_query = clone $baseQuery;

        $leads_received = $this->queryDecider($total_leads_query, 'leads.created_at', $request->duration)->count();

        $leads_received_thirty_quert = clone $baseQuery;
        $leads_received_thirty = $leads_received_thirty_quert->where('leads.created_at', '>=', Carbon::now()->subDays(30))->count();

        $leadsPerDay = [];

        foreach ($agents as $agentId) {
            $averageLeads_query = Lead::where('agent_id', $agentId->id)->where('status', 1)->where('is_archive', 0)
                ->selectRaw('COUNT(*) as count, DATE(created_at) as date')
                ->groupBy('date');
            $averageLeads = $this->queryDecider($averageLeads_query, 'created_at', $request->duration)->get();

            $totalLeads = $averageLeads->sum('count');
            $totalDays = $averageLeads->count();

            if ($totalDays > 0) {
                $averageLeadsPerDay = $totalLeads / $totalDays;
                $leadsPerDay[$agentId->id] = $averageLeadsPerDay;
            } else {
                $leadsPerDay[$agentId->id] = 0; // Handle the case where there are no leads for an agent.
            }
            $whatsAppbaseQuery = Lead::where('agent_id', $agentId->id)->where('status', 1)->where('is_archive', 0);

            $whatsapp_avg_agents[$agentId->full_name] = $this->queryDecider($whatsAppbaseQuery, 'leads.created_at', $request->duration)
                ->join('shortlinks', 'leads.id', '=', 'shortlinks.lead_id')
                ->whereNotNull('shortlinks.opted_in')
                ->select(DB::raw('AVG(TIMESTAMPDIFF(SECOND, shortlinks.created_at, shortlinks.opted_in)) as average_diff'))
                ->value('average_diff');

            $whatsapp_avg_agents_collection = collect($whatsapp_avg_agents);
        }

        $averageLeadsPerDay = array_sum($leadsPerDay) / count($leadsPerDay);
        $averageLeadsPerDay = number_format((float) $averageLeadsPerDay, 2, '.', '');

        $successfulLeadsQuery = Lead::whereIn('agent_id', $agentIds)
            ->where('lead_status_id', 9)
            ->where('status', 1)->where('is_archive', 0);
        $successfulLeads = $this->queryDecider($successfulLeadsQuery, 'created_at', $request->duration)->count();

        $successfulLeads_thirty = Lead::whereIn('agent_id', $agentIds)
            ->where('lead_status_id', 9)
            ->where('status', 1)->where('is_archive', 0)
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->count();

        if ($leads_received > 0) {
            $successRate = ($successfulLeads / $leads_received) * 100;
        } else {
            $successRate = 0;
        }
        if ($leads_received_thirty > 0) {
            $successRate_thirty = ($successfulLeads_thirty / $leads_received_thirty) * 100;
        } else {
            $successRate_thirty = 0;
        }

        $successRate = number_format((float) $successRate, 2, '.', '');
        $successRate_thirty = number_format((float) $successRate_thirty, 2, '.', '');

        $super_agent = User::whereIn('id', $agentIds)
            ->withCount(['leads' => function ($query) use ($request) {
                $this->queryDecider($query, 'created_at', $request->duration);
            }])
            ->orderByDesc('leads_count')
            ->first();

        return view('admin.reports.details.group_leads_overview')
            ->with('group', $group)
            ->with('agents', $agents)
            ->with('whatsapp_avg', $whatsapp_avg)
            ->with('averageLeadsPerDay', $averageLeadsPerDay)
            ->with('leads_received', $leads_received)
            ->with('leads_received_thirty', $leads_received_thirty)
            ->with('successfulLeads', $successfulLeads)
            ->with('successfulLeads_thirty', $successfulLeads_thirty)
            ->with('successRate', $successRate)
            ->with('successRate_thirty', $successRate_thirty)
            ->with('whatsapp_avg_agents_collection', $whatsapp_avg_agents_collection)
            ->with('super_agent', $super_agent);

    }

    private function getAvgResponseRateOfAgents($groupId, $duration)
    {
        $group = Group::where('id', $groupId)->first();
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
        foreach ($agents as $agentId) {
            $averageLeads_query = Lead::where('agent_id', $agentId->id)->where('status', 1)->where('is_archive', 0)
                ->selectRaw('COUNT(*) as count, DATE(created_at) as date')
                ->groupBy('date');
            $averageLeads = $this->queryDecider($averageLeads_query, 'created_at', $duration)->get();
            $totalLeads = $averageLeads->sum('count');
            $totalDays = $averageLeads->count();

            if ($totalDays > 0) {
                $averageLeadsPerDay = $totalLeads / $totalDays;
                $leadsPerDay[$agentId->id] = $averageLeadsPerDay;
            } else {
                $leadsPerDay[$agentId->id] = 0; // Handle the case where there are no leads for an agent.
            }
            $whatsAppbaseQuery = Lead::where('agent_id', $agentId->id)->where('status', 1)->where('is_archive', 0);

            $whatsapp_avg_agents[$agentId->full_name] = $this->queryDecider($whatsAppbaseQuery, 'leads.created_at', $duration)
                ->join('shortlinks', 'leads.id', '=', 'shortlinks.lead_id')
                ->whereNotNull('shortlinks.opted_in')
                ->select(DB::raw('AVG(TIMESTAMPDIFF(SECOND, shortlinks.created_at, shortlinks.opted_in)) as average_diff'))
                ->value('average_diff');
            $whatsapp_avg_agents_collection = collect($whatsapp_avg_agents);
        }

        return $whatsapp_avg_agents_collection;
        // return view('admin.reports.partials.groups._avg_response_rate_table')
        // ->with('whatsapp_avg_agents_collection', $whatsapp_avg_agents_collection);

    }

    private function getTotalNumberOfWarningsandSuspensions($groupId, $duration)
    {
        $group = Group::where('id', $groupId)->first();
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

        // foreach ($agents as $agentId) {
        //     $baseQuery = UserMeta::where('user_id', $agentId->id);
        //     $warnings[$agentId->full_name] = $this->queryDecider($baseQuery, 'created_at', $duration)->where('tag', 'lead_inactivity_warning')->count();
        //     $suspensions[$agentId->full_name] = $this->queryDecider($baseQuery, 'created_at', $duration)->where('tag', 'lead_inactivity_deactivate')->count();
        // }

        // // Create a merged collection
        // $mergedCollection = collect([
        //     'warnings' => collect($warnings)->sortDesc(),
        //     'suspensions' => collect($suspensions)->sortDesc(),
        // ]);

        // return $mergedCollection;

        $agentsData = [];

        foreach ($agents as $agentId) {
            $baseQuery = UserMeta::where('user_id', $agentId->id);
            $warning_base_quert = clone $baseQuery;

            $warningsCount = $this->queryDecider($warning_base_quert, 'created_at', $duration)
                ->where('tag', 'lead_inactivity_warning')->count();
            $suspension_base_query = clone $baseQuery;
            $suspensionsCount = $this->queryDecider($suspension_base_query, 'created_at', $duration)
                ->where('tag', 'lead_inactivity_deactivate')->count();

            $agentsData[] = [
                'name' => $agentId->full_name,
                'warnings' => $warningsCount,
                'suspensions' => $suspensionsCount,
            ];
        }
        return $agentsData;
        // make collection
        $agentsDataCollection = collect($agentsData);
        return $agentsDataCollection;

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

    private function listingStatsOverview($groupId, $duration)
    {
        $agentIds = $this->getGroupUsers($groupId);

        $baseQuery = Property::where('is_live', 1)->whereIn('agent_id', $agentIds);
        // $baseQuery = $this->queryDecider($baseQuery, 'created_at', $duration);
        $primary_properties_base = Property::where('is_live', 1)->whereIn('agent_id', $agentIds)->whereIn('completion_status', ['Ready Primary', 'Off-Plan Primary']);
        $primary_properties = $this->queryDecider($primary_properties_base, 'created_at', $duration)->get()->count();

        $secondary_properties_base = Property::where('is_live', 1)->whereIn('agent_id', $agentIds)->whereIn('completion_status', ['Ready Secondary', 'Off-Plan Secondary']);
        $secondary_properties = $this->queryDecider($secondary_properties_base, 'created_at', $duration)->get()->count();

        return [
            'primary_properties' => $primary_properties,
            'secondary_properties' => $secondary_properties,
        ];
    }

    private function userMetaSplit($groupId, $duration)
    {
        $agentIds = $this->getGroupUsers($groupId);

        $agents = User::whereIn('id', $agentIds)->get();
        $meta_warning_query = UserMeta::whereIn("user_id", $agents->pluck("id")->toArray())->where('tag', 'lead_inactivity_warning');
        $meta_suspension_query = UserMeta::whereIn("user_id", $agents->pluck("id")->toArray())->where('tag', 'lead_inactivity_deactivate');

        $warnings = $this->queryDecider($meta_warning_query, 'created_at', $duration)->count();
        $suspensions = $this->queryDecider($meta_suspension_query, 'created_at', $duration)->count();

        $totalAgents = $agents->count();
        $warningsPercentage = ($warnings / $totalAgents) * 100;
        $suspensionsPercentage = ($suspensions / $totalAgents) * 100;
        $warningsPercentage = number_format((float) $warningsPercentage, 0, '.', '');
        $suspensionsPercentage = number_format((float) $suspensionsPercentage, 0, '.', '');

        // from the agents find in Leads table which agent has most created_at and updated_at

        foreach ($agents as $agent) {
            $frequency = 0;
            $baseQuery = Lead::whereNull('converted_to_listing_lead')
                ->where('status', 1)
                ->where('is_archive', 0)
                ->where('agent_id', $agent->id)
                ->where(function ($q) use ($agent) {
                    $q->where('created_by_id', $agent->id)
                        ->orWhere('updated_by_id', $agent->id);
                });
            $leadCount = $this->queryDecider($baseQuery, 'created_at', $duration)->count();

            $BaseleadNotesCount = LeadNote::where('created_by_id', $agent->id);
            $leadNotesCount = $this->queryDecider($BaseleadNotesCount, 'created_at', $duration)->count();

            $combinedLeadsCount = $leadCount + $leadNotesCount;

            $frequency += $combinedLeadsCount;

            $BaselistingCount = Property::where('is_archived', 0)
                ->where('agent_id', $agent->id)->where(function ($q) use ($agent) {
                $q->where('created_by_id', $agent->id)
                    ->orWhere('updated_by_id', $agent->id);
            });
            $listingCount = $this->queryDecider($BaselistingCount, 'created_at', $duration)->count();

            $baseListingNotesCount = PropertyNote::where('created_by_id', $agent->id);
            $listingNotesCount = $this->queryDecider($baseListingNotesCount, 'created_at', $duration)->count();

            $combinedListingCount = $listingCount + $listingNotesCount;

            $frequency += $combinedListingCount;

            $baseListingLeadsCount = ListingLead::where('is_archive', 0)
                ->where('agent_id', $agent->id)->where(function ($q) use ($agent) {
                $q->where('created_by_id', $agent->id)
                    ->orWhere('updated_by_id', $agent->id);
            });
            $listingLeadsCount = $this->queryDecider($baseListingLeadsCount, 'created_at', $duration)->count();

            $baseListingLeadNotesCount = ListingLead::where('created_by_id', $agent->id);
            $listingLeadNotesCount = $this->queryDecider($baseListingLeadNotesCount, 'created_at', $duration)->count();

            $combinedListingLeadCount = $listingLeadsCount + $listingLeadNotesCount;

            $frequency += $combinedListingLeadCount;

            $key = $agent->full_name;
            $agentsLeadCount[$key] = $frequency;

            $agentsLeadCountID[$agent->id] = $frequency;

        }

        arsort($agentsLeadCount);
        arsort($agentsLeadCountID);

        $_agentsLeadCountID = array_slice($agentsLeadCountID, 0, 10, true);
        $_last_agentsLeadCountID = array_slice($agentsLeadCountID, -10, 10, true);

        $__agentsLeadCountID = array_keys($_agentsLeadCountID);
        $__last_agentsLeadCountID = array_keys($_last_agentsLeadCountID);

        $topAgents = User::whereHas("roles", fn($q) => $q->whereIn("name", ["Agent"]))->whereIn('id', $__agentsLeadCountID)
            ->orderByRaw(DB::raw("FIELD(id, " . implode(',', $__agentsLeadCountID) . ")"))
            ->get();
        $leadsAgents = User::whereHas("roles", fn($q) => $q->whereIn("name", ["Agent"]))->whereIn('id', $__last_agentsLeadCountID)
            ->orderByRaw(DB::raw("FIELD(id, " . implode(',', $__last_agentsLeadCountID) . ")"))
            ->get();

        return [
            'warnings' => $warnings,
            'suspensions' => $suspensions,
            'warningsPercentage' => $warningsPercentage,
            'suspensionsPercentage' => $suspensionsPercentage,
            'systemUsers' => $agentsLeadCount,
            'topAgents' => $topAgents,
            'leadsAgents' => $leadsAgents,
        ];
    }

    private function userPropertyMetaReportBayut($groupIds, $duration)
    {
        $agentIds = $this->getGroupUsers($groupIds);
        $agents = User::whereIn('id', $agentIds)->get();
        $rankThresholds = [40, 50, 60, 70, 80, 90, 100];
        $pf_rank = [];
        $dates = [];
        $portal = Portal::where('name', 'Bayut')->first();

        foreach ($rankThresholds as $threshold) {
            $upper = $threshold + 10;
            $baseQuery = Property::whereNot('completion_status', 'Off-Plan Primary')->where('region_id', 1);
            $query = $this->queryDecider($baseQuery, 'properties.created_at', $duration);
            $count = $query->whereHas('propertyMeta', function ($query) use ($threshold, $upper) {
                if ($threshold == 100) {
                    $query->where('meta_key', 'bayut_rank')
                        ->where('meta_value', '=', $threshold);
                } else {
                    $query->where('meta_key', 'bayut_rank')
                        ->where('meta_value', '=>', $threshold)->where('meta_value', '<=', $upper);
                }
            })->whereHas('portals', function ($q) use ($portal) {
                $q->where('portal_id', $portal->id);
            })->whereIn('agent_id', $agentIds)->where('is_live', '1')->count();
            if ($threshold == 100) {
                $pf_rank[$threshold] = $count;
            } else {
                $pf_rank[$threshold . '-' . $upper] = $count;
            }
        }

        $data['ranks'] = $pf_rank;
        $baseQuery = Property::whereNot('completion_status', 'Off-Plan Primary')->where('region_id', 1);
        $query = $this->queryDecider($baseQuery, 'properties.created_at', $duration);

        $verified_count = $query->whereHas('propertyMeta', function ($query) {
            $query->where('meta_key', 'bayut_review_status')
                ->where('meta_value', 'Approved');
        })->whereHas('portals', function ($q) use ($portal) {
            $q->where('portal_id', $portal->id);
        })->whereIn('agent_id', $agentIds)->where('is_live', '1')->count();

        $baseQuery = Property::whereNot('completion_status', 'Off-Plan Primary')->where('region_id', 1);
        $query = $this->queryDecider($baseQuery, 'properties.created_at', $duration);

        $data['trucheck'] = $query->whereHas('propertyMeta', function ($query) {
            $query->where('meta_key', 'bayut_trucheck_status')
                ->where('meta_value', 'Truchecked');
        })->whereHas('portals', function ($q) use ($portal) {
            $q->where('portal_id', $portal->id);
        })->whereIn('agent_id', $agentIds)->where('is_live', '1')->count() ?? 0;

        $baseQuery = Property::whereNot('completion_status', 'Off-Plan Primary')->where('region_id', 1);
        $query = $this->queryDecider($baseQuery, 'properties.created_at', $duration);

        $data['checked'] = $query->whereHas('propertyMeta', function ($query) {
            $query->where('meta_key', 'bayut_trucheck_status')
                ->where('meta_value', 'checked');
        })->whereHas('portals', function ($q) use ($portal) {
            $q->where('portal_id', $portal->id);
        })->whereIn('agent_id', $agentIds)->where('is_live', '1')->count() ?? 0;

        //calculate average of pf_rank
        $baseQuery = Property::whereNot('completion_status', 'Off-Plan Primary')->where('region_id', 1);
        $query = $this->queryDecider($baseQuery, 'properties.created_at', $duration);

        $avg_rank = $query->join('property_meta', function ($join) {
            $join->on('properties.id', '=', 'property_meta.property_id')
                ->where('property_meta.meta_key', '=', 'bayut_rank');
        })->whereHas('portals', function ($q) use ($portal) {
            $q->where('portal_id', $portal->id);
        })->whereIn('agent_id', $agentIds)->avg('property_meta.meta_value');
        $data['avg_rank'] = round($avg_rank);

        $data['verified'] = $verified_count;
        $baseQuery = Property::whereNot('completion_status', 'Off-Plan Primary')->where('region_id', 1);
        $query = $this->queryDecider($baseQuery, 'properties.created_at', $duration);

        $data['portal_count'] = $query->whereHas('portals', function ($q) use ($portal) {
            $q->where('portal_id', $portal->id);
        })->whereIn('agent_id', $agentIds)->where('is_live', '1')->count();

        $levels = ['Standard', 'Signature', 'Hot'];
        $rank = [];
        $total_count = 0;
        foreach ($levels as $level) {
            $baseQuery = Property::whereNot('completion_status', 'Off-Plan Primary')->where('region_id', 1);
            $query = $this->queryDecider($baseQuery, 'properties.created_at', $duration);
            $count = $query->whereHas('propertyMeta', function ($query) use ($level) {
                $query->where('meta_key', 'bayut_level')
                    ->where('meta_value', $level);
            })->whereHas('portals', function ($q) use ($portal) {
                $q->where('portal_id', $portal->id);
            })->whereIn('agent_id', $agentIds)->where('is_live', '1')->count();
            $rank[$level] = $count;
            $total_count += $count;

        }
        $rank['Standard'] = $data['portal_count'] - $total_count ?? 0; //because we don't have data for bayut_level = Standard

        $data['level'] = $rank;

        return $data;
    }

    private function userPropertyMetaReportDubizzle($groupIds, $duration)
    {
        $agentIds = $this->getGroupUsers($groupIds);
        $agents = User::whereIn('id', $agentIds)->get();

        $rankThresholds = [40, 50, 60, 70, 80, 90, 100];
        $rank = [];
        $dates = [];
        $portal = Portal::where('name', 'Dubizzle')->first();

        foreach ($rankThresholds as $threshold) {
            $upper = $threshold + 10;
            $baseQuery = Property::whereNot('completion_status', 'Off-Plan Primary')->where('region_id', 1);
            $query = $this->queryDecider($baseQuery, 'properties.created_at', $duration);

            $count = $query->whereHas('propertyMeta', function ($query) use ($threshold, $upper) {
                if ($threshold == 100) {
                    $query->where('meta_key', 'dubizzle_rank')
                        ->where('meta_value', '=', $threshold);
                } else {
                    $query->where('meta_key', 'dubizzle_rank')
                        ->where('meta_value', '>', $threshold)->where('meta_value', '<', $upper);
                }

            })->whereHas('portals', function ($q) use ($portal) {
                $q->where('portal_id', $portal->id);
            })->whereIn('agent_id', $agentIds)->count();
            if ($threshold == 100) {
                $rank[$threshold] = $count;
            } else {
                $rank[$threshold . '-' . $upper] = $count;
            }

        }
        $data['ranks'] = $rank;

        $baseQuery = Property::whereNot('completion_status', 'Off-Plan Primary')->where('region_id', 1);
        $query = $this->queryDecider($baseQuery, 'properties.created_at', $duration);

        $data['trucheck'] = $query->whereHas('propertyMeta', function ($query) {
            $query->where('meta_key', 'dubizzle_trucheck_status')
                ->where('meta_value', 'checked');
        })->whereHas('portals', function ($q) use ($portal) {
            $q->where('portal_id', $portal->id);
        })->where('is_live', '1')->whereIn('agent_id', $agentIds)->count() ?? 0;

        $baseQuery = Property::whereNot('completion_status', 'Off-Plan Primary')->where('region_id', 1);
        $query = $this->queryDecider($baseQuery, 'properties.created_at', $duration);

        $data['trucheck'] = $query->whereHas('propertyMeta', function ($query) {
            $query->where('meta_key', 'dubizzle_trucheck_status')
                ->where('meta_value', 'Truchecked');
        })->whereHas('portals', function ($q) use ($portal) {
            $q->where('portal_id', $portal->id);
        })->where('is_live', '1')->whereIn('agent_id', $agentIds)->count() ?? 0;

        $baseQuery = Property::whereNot('completion_status', 'Off-Plan Primary')->where('region_id', 1);
        $query = $this->queryDecider($baseQuery, 'properties.created_at', $duration);

        $data['checked'] = $query->whereHas('propertyMeta', function ($query) {
            $query->where('meta_key', 'dubizzle_trucheck_status')
                ->where('meta_value', 'checked');
        })->whereHas('portals', function ($q) use ($portal) {
            $q->where('portal_id', $portal->id);
        })->where('is_live', '1')->whereIn('agent_id', $agentIds)->count() ?? 0;

        //calculate average of rank
        $baseQuery = Property::whereNot('completion_status', 'Off-Plan Primary')->where('region_id', 1);
        $query = $this->queryDecider($baseQuery, 'properties.created_at', $duration);

        $avg_rank = $query->join('property_meta', function ($join) {
            $join->on('properties.id', '=', 'property_meta.property_id')
                ->where('property_meta.meta_key', '=', 'dubizzle_rank');
        })->whereHas('portals', function ($q) use ($portal) {
            $q->where('portal_id', $portal->id);
        })->where('is_live', '1')->whereIn('agent_id', $agentIds)->avg('property_meta.meta_value');
        $data['avg_rank'] = round($avg_rank);

        //  $data['verified'] =  $verified_count;
        $baseQuery = Property::whereNot('completion_status', 'Off-Plan Primary')->where('region_id', 1);
        $query = $this->queryDecider($baseQuery, 'properties.created_at', $duration);

        $data['portal_count'] = $query->whereHas('portals', function ($q) use ($portal) {
            $q->where('portal_id', $portal->id);
        })->where('is_live', '1')->whereIn('agent_id', $agentIds)->count();
        $levels = ['Premium', 'Featured','Standard'];
        $rank = [];
        $total_count = 0;
        foreach ($levels as $level) {
            $baseQuery = Property::whereNot('completion_status', 'Off-Plan Primary')->where('region_id', 1);
            $query = $this->queryDecider($baseQuery, 'properties.created_at', $duration);

            $count = $query->whereHas('propertyMeta', function ($query) use ($level) {
                $query->where('meta_key', 'dubizzle_level')
                    ->where('meta_value', $level);
            })->whereHas('portals', function ($q) use ($portal) {
                $q->where('portal_id', $portal->id);
            })->where('is_live', '1')->whereIn('agent_id', $agentIds)->count();
            $rank[$level] = $count;
            $total_count += $count;
            // $pf_level[$level] = PropertyMeta::where('meta_key', 'pf_level')->where('meta_value', $level)->count();

        }
        $rank['Standard'] = $data['portal_count'] - $total_count + $rank['Standard'] ?? 0; //because we don't have data for bayut_level = Standard

        $data['level'] = $rank;
        return $data;
    }

    private function userPropertyMetaReportPropertyFinder($groupIds, $duration)
    {
        $rankThresholds = [40, 50, 60, 70, 80, 90, 100];
        $pf_rank = [];
        $dates = [];
        $portal = Portal::where('name', 'Property Finder')->first();
        $source = Source::where('name', 'Property Finder')->first();

        $agentIds = $this->getGroupUsers($groupIds);
        $agents = User::whereIn('id', $agentIds)->get();

        foreach ($rankThresholds as $threshold) {
            $upper = $threshold + 10;
            $baseQuery = Property::whereNot('completion_status', 'Off-Plan Primary')->where('region_id', 1);
            $query = $this->queryDecider($baseQuery, 'properties.created_at', $duration);

            $count_query = $query->whereHas('propertyMeta', function ($query) use ($threshold, $upper) {
                if ($threshold == 100) {
                    $query->where('meta_key', 'pf_rank')
                        ->where('meta_value', '=', $threshold);
                } else {
                    $query->where('meta_key', 'pf_rank')
                        ->where('meta_value', '>', $threshold)->where('meta_value', '<', $upper);
                }
            })->whereHas('portals', function ($q) use ($portal) {
                $q->where('portal_id', $portal->id);
            })->whereIn('agent_id', $agentIds)->where('is_live', '1');

            $count = $this->queryDecider($count_query, 'properties.created_at', $duration)->count();

            if ($threshold == 100) {
                $pf_rank[$threshold] = $count;
            } else {
                $pf_rank[$threshold . '-' . $upper] = $count;
            }

        }

        $data['pf_rank'] = $pf_rank;
        $baseQuery = Property::whereNot('completion_status', 'Off-Plan Primary')->where('region_id', 1);
        $query = $this->queryDecider($baseQuery, 'properties.created_at', $duration);

        $verified_count_query = $query->whereHas('propertyMeta', function ($query) {
            $query->where('meta_key', 'pf_verified')
                ->where('meta_value', '1');
        })->whereHas('portals', function ($q) use ($portal) {
            $q->where('portal_id', $portal->id);
        })->where('is_live', '1')->whereIn('agent_id', $agentIds);

        $verified_count = $this->queryDecider($verified_count_query, 'properties.created_at', $duration)->count();
        $baseQuery = Property::whereNot('completion_status', 'Off-Plan Primary')->where('region_id', 1);
        $query = $this->queryDecider($baseQuery, 'properties.created_at', $duration);

        $online_query = $query->whereHas('propertyMeta', function ($query) {
            $query->where('meta_key', 'pf_status')
                ->where('meta_value', 'Online');
        })->whereHas('portals', function ($q) use ($portal) {
            $q->where('portal_id', $portal->id);
        })->where('is_live', '1')->whereIn('agent_id', $agentIds);

        $data['online'] = $this->queryDecider($online_query, 'properties.created_at', $duration)->count();
        $baseQuery = Property::whereNot('completion_status', 'Off-Plan Primary')->where('region_id', 1);
        $query = $this->queryDecider($baseQuery, 'properties.created_at', $duration);

        $offline_query = $query->whereHas('propertyMeta', function ($query) {
            $query->where('meta_key', 'pf_status')
                ->where('meta_value', 'Offline');
        })->whereHas('portals', function ($q) use ($portal) {
            $q->where('portal_id', $portal->id);
        })->where('is_live', '1')->whereIn('agent_id', $agentIds);
        $data['offline'] = $this->queryDecider($offline_query, 'properties.created_at', $duration)->count();

        //calculate average of pf_rank
        $baseQuery = Property::whereNot('completion_status', 'Off-Plan Primary')->where('region_id', 1);
        $query = $this->queryDecider($baseQuery, 'properties.created_at', $duration);

        $pf_avg_rank = $query->join('property_meta', function ($join) {
            $join->on('properties.id', '=', 'property_meta.property_id')
                ->where('property_meta.meta_key', '=', 'pf_rank');
        })->whereHas('portals', function ($q) use ($portal) {
            $q->where('portal_id', $portal->id);
        })->where('is_live', '1')->whereIn('agent_id', $agentIds)->avg('property_meta.meta_value');

        $data['avg_pf_rank'] = round($pf_avg_rank);

        $data['pf_verified'] = $verified_count;
        $baseQuery = Property::whereNot('completion_status', 'Off-Plan Primary')->where('region_id', 1);
        $query = $this->queryDecider($baseQuery, 'properties.created_at', $duration);

        $data['portal_count'] = $query->whereHas('portals', function ($q) use ($portal) {
            $q->where('portal_id', $portal->id);
        })->where('is_live', '1')->whereIn('agent_id', $agentIds)->count();

        $levels = ['Standard', 'Featured', 'Premium'];
        $pf_level = [];
        $total_count = 0;
        foreach ($levels as $level) {
            $baseQuery = Property::whereNot('completion_status', 'Off-Plan Primary')->where('region_id', 1);
            $query = $this->queryDecider($baseQuery, 'properties.created_at', $duration);

            $count_query = $query->whereHas('propertyMeta', function ($query) use ($level) {
                $query->where('meta_key', 'pf_level')
                    ->where('meta_value', $level);
            })->whereHas('portals', function ($q) use ($portal) {
                $q->where('portal_id', $portal->id);
            })->where('is_live', '1')->whereIn('agent_id', $agentIds);

            $count = $this->queryDecider($count_query, 'properties.created_at', $duration)->count();

            $pf_level[$level] = $count;
            $total_count += $count;
            // $pf_level[$level] = PropertyMeta::where('meta_key', 'pf_level')->where('meta_value', $level)->count();

        }
        $pf_level['Standard'] = $data['portal_count'] - $total_count + $pf_level['Standard'] ?? 0;

        $data['level'] = $pf_level;
        //    $averageDifference = Shortlink::where('source_id', $source->id)->whereNotNull('opted_in')
        //        ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, created_at, opted_in)) AS average_difference')
        //        ->get();

        $averageDifference = Lead::whereIn('agent_id', $agentIds)
            ->join('shortlinks', 'leads.id', '=', 'shortlinks.lead_id')
            ->whereNotNull('shortlinks.opted_in')
            ->where('shortlinks.source_id', $source->id)
            ->select(DB::raw('AVG(TIMESTAMPDIFF(SECOND, shortlinks.created_at, shortlinks.opted_in)) as average_diff'))
            ->value('average_diff');

        $data['whatsapp_avg_res'] = $averageDifference ?? 0;

        return $data;
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

    private function propertyLevels($groupIds, $duration)
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

            // bayut Portal Levels
            $portal_bayut_query_one = Property::whereNot('completion_status', 'Off-Plan Primary')->where('region_id', 1);
            $portal_bayut_query_two = $this->queryDecider($portal_bayut_query_one, 'created_at', $duration);

            $bayut['portal_count'] = $portal_bayut_query_two->whereHas('portals', function ($q) use ($portal_bayut) {
                $q->where('portal_id', $portal_bayut->id);
            })->whereIn('agent_id', $agents->pluck("id")->toArray())->where('is_live', '1')->count();

            $levels = ['Standard', 'Signature', 'Hot'];
            $rank = [];
            $total_count = 0;
            foreach ($levels as $level) {
                $properties_bayut_query_one = Property::whereNot('completion_status', 'Off-Plan Primary')->where('region_id', 1);
                $properties_bayut_query_two = $this->queryDecider($properties_bayut_query_one, 'created_at', $duration);
                $count = $properties_bayut_query_two->whereHas('propertyMeta', function ($query) use ($level) {
                    $query->where('meta_key', 'bayut_level')
                        ->where('meta_value', $level);
                })
                    ->whereHas('portals', function ($q) use ($portal_bayut) {
                        $q->where('portal_id', $portal_bayut->id);
                    })
                    ->whereIn('agent_id', $agents->pluck("id")->toArray())
                    ->where('is_live', '1')
                    ->count();

                $rank[$level] = $count;
                $total_count += $count;

            }
            $rank['Standard'] = $bayut['portal_count'] - $total_count ?? 0; //because we don't have data for bayut_level = Standard
            $bayut['level'] = $rank;

            $bayut_standard = $bayut['level']['Standard'] ?? 0;
            $bayut_signature = $bayut['level']['Signature'] ?? 0;
            $bayut_hot = $bayut['level']['Hot'] ?? 0;

            $portal_pf_query_one = Property::whereNot('completion_status', 'Off-Plan Primary')->where('region_id', 1);
            $portal_pf_query_two = $this->queryDecider($portal_pf_query_one, 'created_at', $duration);

            $property_finder['portal_count'] = $portal_pf_query_two->whereHas('portals', function ($q) use ($portal_pf) {
                $q->where('portal_id', $portal_pf->id);
            })->where('is_live', '1')->whereIn('agent_id', $agents->pluck("id")->toArray())->count();

            $levels = ['Standard', 'Featured', 'Premium'];
            $pf_level = [];
            $total_count = 0;
            foreach ($levels as $level) {
                $properties_pf_query_one = Property::whereNot('completion_status', 'Off-Plan Primary')->where('region_id', 1);
                $properties_pf_query_two = $this->queryDecider($properties_pf_query_one, 'created_at', $duration);

                $count_query = $properties_pf_query_two->whereHas('propertyMeta', function ($query) use ($level) {
                    $query->where('meta_key', 'pf_level')
                        ->where('meta_value', $level);
                })->whereHas('portals', function ($q) use ($portal_pf) {
                    $q->where('portal_id', $portal_pf->id);
                })->where('is_live', '1')->whereIn('agent_id', $agents->pluck("id")->toArray());

                $count = $this->queryDecider($count_query, 'properties.created_at', $duration)->count();

                $pf_level[$level] = $count;
                $total_count += $count;
                // $pf_level[$level] = PropertyMeta::where('meta_key', 'pf_level')->where('meta_value', $level)->count();

            }
            $pf_level['Standard'] = $property_finder['portal_count'] - $total_count + $pf_level['Standard'] ?? 0;

            $property_finder['level'] = $pf_level;

            // dubizzle properties Level

            $portal_dubizzle_query_one = Property::whereNot('completion_status', 'Off-Plan Primary')->where('region_id', 1);
            $portal_dubizzle_query_two = $this->queryDecider($portal_dubizzle_query_one, 'created_at', $duration);

            $dubizzle['portal_count'] = $portal_dubizzle_query_two->whereHas('portals', function ($q) use ($portal_dubizzle) {
                $q->where('portal_id', $portal_dubizzle->id);
            })->where('is_live', '1')->whereIn('agent_id', $agents->pluck("id")->toArray())->count();

            $levels = ['Standard', 'Featured'];
            $rank = [];
            $total_count = 0;
            foreach ($levels as $level) {
                $properties_dubizzle_query_one = Property::whereNot('completion_status', 'Off-Plan Primary')->where('region_id', 1);
                $properties_dubizzle_query_two = $this->queryDecider($properties_dubizzle_query_one, 'created_at', $duration);

                $count = $properties_dubizzle_query_two->whereHas('propertyMeta', function ($query) use ($level) {
                    $query->where('meta_key', 'dubizzle_level')
                        ->where('meta_value', $level);
                })->whereHas('portals', function ($q) use ($portal_dubizzle) {
                    $q->where('portal_id', $portal_dubizzle->id);
                })->where('is_live', '1')->whereIn('agent_id', $agents->pluck("id")->toArray())->count();
                $rank[$level] = $count;
                $total_count += $count;
                // $pf_level[$level] = PropertyMeta::where('meta_key', 'pf_level')->where('meta_value', $level)->count();
            }
            $rank['Standard'] = $dubizzle['portal_count'] - $total_count + $rank['Standard'] ?? 0; //because we don't have data for bayut_level = Standard

            $dubizzle['level'] = $rank;

            $groupdata[$group->id] = [
                'group_id' => $group->id,
                'group_name' => $group->name,
                'group_color' => $group->color,
                'property_finder' => $property_finder,
                'bayut' => $bayut,
                'dubizzle' => $dubizzle,
            ];

        }
        return $groupdata;
    }

    private function getTruePublish($group_id, $duration)
    {
        $agentIds = $this->getGroupUsers($group_id);

        $baseQuery = Property::whereIn('agent_id', $agentIds)->where('is_live', 1)->where('is_archived', false)->whereHas('portals', function ($query) {
            $query->whereIn('portal_id', [1, 2]);
        });
        $properties = $this->queryDecider($baseQuery, 'created_at', $duration)->get()->count();

        return $properties;

    }

    private function getForStatus($group_id, $duration)
    {
        $agentIds = $this->getGroupUsers($group_id);

        $old_baseQuery = Property::whereIn('agent_id', $agentIds)
            ->where('is_live', 1)
            ->where('is_archived', false);
        $baseQuery = $this->queryDecider($old_baseQuery, 'created_at', $duration);

        $commercial_for_rent = clone $baseQuery;
        $commercial_for_sale = clone $baseQuery;
        $residential_for_rent = clone $baseQuery;
        $residential_for_sale = clone $baseQuery;

        $commercial_for_rent = $commercial_for_rent->where('for', 'Commercial for rent')->count();
        $commercial_for_sale = $commercial_for_sale->where('for', 'Commercial for sale')->count();
        $residential_for_rent = $residential_for_rent->where('for', 'Residential for rent')->count();
        $residential_for_sale = $residential_for_sale->where('for', 'Residential for sale')->count();

        return [
            'commercial_for_rent' => $commercial_for_rent,
            'commercial_for_sale' => $commercial_for_sale,
            'residential_for_rent' => $residential_for_rent,
            'residential_for_sale' => $residential_for_sale,
        ];
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

    private function LeadsSplitOverview($group_id, $duration)
    {
        $agentIds = $this->getGroupUsers($group_id);

        $baseQuery = Lead::whereNull('converted_to_listing_lead')->where('status', 1)->where('is_archive', 0)->whereIn('agent_id', $agentIds);
        $baseQuery = $this->queryDecider($baseQuery, 'created_at', $duration);

        $statusCountsQuery = clone $baseQuery;
        $sourceCountsQuery = clone $baseQuery;
        $typeCountsQuery = clone $baseQuery;
        $mediumCountsQuery = clone $baseQuery;

        $statusCounts = $statusCountsQuery->select('lead_status_id', DB::raw('COUNT(*) as count'))
            ->groupBy('lead_status_id')->get(['lead_status_id', DB::raw('COUNT(*) as count')]);

        $leadCounts = [];
        $statusNames = [];

        foreach ($statusCounts as $statusCount) {
            $status_count[$statusCount->lead_status_id] = $statusCount->count;
            $eventType = LeadStatus::find($statusCount->lead_status_id);
            $statusNames[] = $eventType ? $eventType->name . ': ' . $statusCount->count : 'Null: ' . $statusCount->count;
            $leadCounts[] = $statusCount->count;
        }

        $sourceCounts = $sourceCountsQuery->select('source_id', DB::raw('COUNT(*) as count'))
            ->groupBy('source_id')
            ->get(['source_id', DB::raw('COUNT(*) as count')]);

        $leadSourceCounts = [];
        $sourceNames = [];

        foreach ($sourceCounts as $statusCount) {
            $status_count[$statusCount->source_id] = $statusCount->count;
            $eventType = Source::find($statusCount->source_id);
            $sourceNames[] = $eventType ? $eventType->name . ': ' . $statusCount->count : 'Null: ' . $statusCount->count;
            $leadSourceCounts[] = $statusCount->count;
        }

        $typeCounts = $typeCountsQuery->select('type_id', DB::raw('COUNT(*) as count'))
            ->groupBy('type_id')
            ->get(['type_id', DB::raw('COUNT(*) as count')]);

        $leadTypeCounts = [];
        $typeNames = [];

        foreach ($typeCounts as $statusCount) {
            $status_count[$statusCount->type_id] = $statusCount->count;
            $eventType = Type::find($statusCount->type_id);
            $typeNames[] = $eventType ? $eventType->name . ': ' . $statusCount->count : 'Null: ' . $statusCount->count;
            $leadTypeCounts[] = $statusCount->count;
        }

        $mediumCounts = $mediumCountsQuery->select('lead_medium_id', DB::raw('COUNT(*) as count'))
            ->groupBy('lead_medium_id')
            ->get(['lead_medium_id', DB::raw('COUNT(*) as count')]);

        $leadMediumCounts = [];
        $mediumNames = [];

        foreach ($mediumCounts as $statusCount) {
            $status_count[$statusCount->lead_medium_id] = $statusCount->count;
            $eventType = Medium::find($statusCount->lead_medium_id);
            $mediumNames[] = $eventType ? $eventType->name . ': ' . $statusCount->count : 'Null: ' . $statusCount->count;
            $leadMediumCounts[] = $statusCount->count;
        }

        array_multisort($leadCounts, SORT_DESC, $statusNames);
        array_multisort($leadSourceCounts, SORT_DESC, $sourceNames);
        array_multisort($leadTypeCounts, SORT_DESC, $typeNames);
        array_multisort($leadMediumCounts, SORT_DESC, $mediumNames);

        return [
            'statusNames' => $statusNames,
            'leadCounts' => $leadCounts,
            'sourceNames' => $sourceNames,
            'leadSourceCounts' => $leadSourceCounts,
            'typeNames' => $typeNames,
            'leadTypeCounts' => $leadTypeCounts,
            'mediumNames' => $mediumNames,
            'leadMediumCounts' => $leadMediumCounts,
        ];

    }

    private function leadsSplitScatter($group_id, $duration)
    {

        $agentIds = $this->getGroupUsers($group_id);

        $start_date = now()->subDays(60);
        $end_date = now();

        $baseQuery = Lead::whereIn('agent_id', $agentIds);
        $leads = $this->queryDecider($baseQuery, 'created_at', $duration)->get();

        $data = $leads->map(function ($lead) {
            // make them like of same time zone
            $lead->created_at = $lead->created_at->setTimezone('Asia/Dubai');
            return [
                'x' => $lead->created_at->format('Y-m-d'),
                'y' => $lead->created_at->format('H:i:s'),
            ];

            // return [
            //     'x' => $lead->created_at->format('Y-m-d'),
            //     'y' => $lead->created_at->format('H:i:s'),
            // ];
        });

        return $data;

        // return response()->json($data);
    }

    private function leadsStatusScatter()
    {
        $start_date = now()->subDays(7);
        $end_date = now();

        $leads = Lead::whereBetween('created_at', [$start_date, $end_date])
            ->select('lead_status_id')
            ->get()
            ->groupBy('lead_status_id');
        $colors = [
            1 => '#FF5733',
            2 => '#33FF57',

        ];

        $datasets = [];

        foreach ($leads as $statusId => $statusLeads) {
            $data = $statusLeads->map(function ($lead) {
                return [
                    'x' => $lead->created_at->format('Y-m-d'),
                    'y' => $lead->created_at->format('H:i:s'),
                ];
            });

            $datasets[] = [
                'name' => LeadStatus::find($statusId)->name, // Assumes LeadStatus model and name attribute
                'data' => $data,
                'color' => $colors[$statusId],
            ];
        }

        return $datasets;
    }

    private function getGroupUsers($group_id)
    {
        $group = Group::where('id', $group_id)->first();
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
        $agentIds = $agents->pluck('id')->toArray();
        return $agentIds;
    }

    private function listingLeadsSplitOverview($group_id, $duration)
    {
        $agentIds = $this->getGroupUsers($group_id);
        $baseQuery = ListingLead::where('is_archive', 0)->whereIn('agent_id', $agentIds);
        $baseQuery = $this->queryDecider($baseQuery, 'created_at', $duration);

        $statusCountsQuery = clone $baseQuery;
        $sourceCountsQuery = clone $baseQuery;
        $typeCountsQuery = clone $baseQuery;
        $mediumCountsQuery = clone $baseQuery;

        $statusCounts = $statusCountsQuery->select('status_id', DB::raw('COUNT(*) as count'))
            ->groupBy('status_id')->get(['status_id', DB::raw('COUNT(*) as count')]);

        $leadCounts = [];
        $statusNames = [];

        foreach ($statusCounts as $statusCount) {
            $status_count[$statusCount->status_id] = $statusCount->count;
            $eventType = ListingLeadStatus::find($statusCount->status_id);
            $statusNames[] = $eventType ? $eventType->name . ': ' . $statusCount->count : 'Null: ' . $statusCount->count;
            $leadCounts[] = $statusCount->count;
        }

        $sourceCounts = $sourceCountsQuery->select('source_id', DB::raw('COUNT(*) as count'))
            ->groupBy('source_id')
            ->get(['source_id', DB::raw('COUNT(*) as count')]);

        $leadSourceCounts = [];
        $sourceNames = [];

        foreach ($sourceCounts as $statusCount) {
            $status_count[$statusCount->source_id] = $statusCount->count;
            $eventType = Source::find($statusCount->source_id);
            $sourceNames[] = $eventType ? $eventType->name . ': ' . $statusCount->count : 'Null: ' . $statusCount->count;
            $leadSourceCounts[] = $statusCount->count;
        }

        $typeCounts = $typeCountsQuery->select('type_id', DB::raw('COUNT(*) as count'))
            ->groupBy('type_id')
            ->get(['type_id', DB::raw('COUNT(*) as count')]);

        $leadTypeCounts = [];
        $typeNames = [];

        foreach ($typeCounts as $statusCount) {
            $status_count[$statusCount->type_id] = $statusCount->count;
            $eventType = Type::find($statusCount->type_id);
            $typeNames[] = $eventType ? $eventType->name . ': ' . $statusCount->count : 'Null: ' . $statusCount->count;
            $leadTypeCounts[] = $statusCount->count;
        }

        $mediumCounts = $mediumCountsQuery->select('medium_id', DB::raw('COUNT(*) as count'))
            ->groupBy('medium_id')
            ->get(['medium_id', DB::raw('COUNT(*) as count')]);

        $leadMediumCounts = [];
        $mediumNames = [];

        foreach ($mediumCounts as $statusCount) {
            $status_count[$statusCount->medium_id] = $statusCount->count;
            $eventType = Medium::find($statusCount->medium_id);
            $mediumNames[] = $eventType ? $eventType->name . ': ' . $statusCount->count : 'Null: ' . $statusCount->count;
            $leadMediumCounts[] = $statusCount->count;
        }
        array_multisort($leadCounts, SORT_DESC, $statusNames);
        array_multisort($leadSourceCounts, SORT_DESC, $sourceNames);
        array_multisort($leadTypeCounts, SORT_DESC, $typeNames);
        array_multisort($leadMediumCounts, SORT_DESC, $mediumNames);

        return [
            'statusNames' => $statusNames,
            'leadCounts' => $leadCounts,
            'sourceNames' => $sourceNames,
            'leadSourceCounts' => $leadSourceCounts,
            'typeNames' => $typeNames,
            'leadTypeCounts' => $leadTypeCounts,
            'mediumNames' => $mediumNames,
            'leadMediumCounts' => $leadMediumCounts,
        ];

    }

    private function listingLeadsSplitScatter($group_id, $duration)
    {

        $agentIds = $this->getGroupUsers($group_id);

        $start_date = now()->subDays(30);
        $end_date = now();

        $baseQuery = ListingLead::whereIn('agent_id', $agentIds)->where('is_archive', 0);
        $leads = $this->queryDecider($baseQuery, 'created_at', $duration)->get();

        // whereBetween('created_at', [$start_date, $end_date])
        // ->get();

        $data = $leads->map(function ($lead) {
            return [
                'x' => $lead->created_at->format('Y-m-d'),
                'y' => $lead->created_at->format('H:i:s'),
            ];
        });

        return $data;
    }

    private function listingLeadsSplitScatterSource($group_id, $duration)
    {
        $agentIds = $this->getGroupUsers($group_id);

        $start_date = now()->subDays(30);
        $end_date = now();

        $baseQuery = ListingLead::select(
            DB::raw('DATE(created_at) as x'), // Extract date from created_at
            DB::raw('TIME(created_at) as y'), // Extract time from created_at
            'source_id'
        )->whereIn('agent_id', $agentIds)
            ->groupBy(DB::raw('DATE(created_at)'), DB::raw('TIME(created_at)'), 'source_id');
        $leads = $this->queryDecider($baseQuery, 'created_at', $duration)->get();

        $data = $leads->map(function ($lead) {
            return [
                'x' => $lead->x,
                'y' => $lead->y,
                'source_id' => $lead->source_id,
            ];
        });

        return $data;
    }

    private function _eventsByAgents($groupIds, $duration)
    {
        $data = [];
        $group = Group::where('id', $groupIds)->first();
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

        foreach ($agents as $agent) {
            $data[$agent->name] = $this->getUserEvents($agent->id, $duration);
        }

        $all_events_query = Event::whereIn('agent_id', $agents->pluck("id")->toArray());
        $all_events = $this->queryDecider($all_events_query, 'created_at', $duration)->get();
        $all_events_count = [];
        $eventTypes = EventType::where('status', true)->get();
        foreach ($eventTypes as $eventType) {
            $all_events_count[$eventType->name] = $all_events->where('event_type_id', $eventType->id)->count();
        }

        return $userEventCounts;
        return [
            'data' => $data,
            'all_events_count' => $all_events_count,
        ];
    }
    private function ___eventsByAgents($groupIds, $duration)
    {

        $data = [];
        $all_events_count = [];

        $group = Group::where('id', $groupIds)->first();
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

        // Get all event types
        $eventTypes = EventType::where('status', true)->get()->pluck('name', 'id')->toArray();

        // Initialize event counts for each event type for each user type
        foreach ($eventTypes as $eventTypeId => $eventTypeName) {
            foreach ($agents as $agent) {
                $all_events_count_users[$eventTypeName][$agent->name] = 0;
            }
            $all_events_count_users[$eventTypeName]['All'] = 0;
        }
        foreach ($agents as $agent) {
            $data[$agent->name] = $this->getUserEvents($agent->id, $duration);

            // Get events for each agent
            $agentEvents = Event::where('agent_id', $agent->id)->get();

            // Count events for each event type for the current agent
            foreach ($agentEvents as $event) {
                $eventTypeName = $eventTypes[$event->event_type_id];
                $all_events_count_users[$eventTypeName][$agent->name]++;
            }

        }

        foreach ($agents as $agent) {
            $data[$agent->name] = $this->getUserEvents($agent->id, $duration);
        }

        $all_events_query = Event::whereIn('agent_id', $agents->pluck("id")->toArray());
        $all_events = $this->queryDecider($all_events_query, 'created_at', $duration)->get();
        $all_events_count = [];
        $eventTypes = EventType::where('status', true)->get();
        foreach ($eventTypes as $eventType) {
            $all_events_count[$eventType->name] = $all_events->where('event_type_id', $eventType->id)->count();
        }

        return [
            'data' => $data,
            'all_events_count' => $all_events_count,
            'all_events_count_users' => $all_events_count_users,

        ];
    }
    private function eventsByAgents($groupIds, $duration)
    {
        $data = [];
        $all_events_count = [];
        $all_events_count_users = [];
        $userTotalEventCounts = []; // Separate variable to hold username => total count

        $group = Group::where('id', $groupIds)->first();
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
        $eventTypes = EventType::where('status', true)->get()->pluck('name', 'id')->toArray();

        foreach ($eventTypes as $eventTypeId => $eventTypeName) {
            foreach ($agents as $agent) {
                $all_events_count_users[$eventTypeName][$agent->name] = 0;
            }
            $all_events_count_users[$eventTypeName]['All'] = 0;
        }

        foreach ($agents as $agent) {
            $userEventCount = 0;

            $BaseagentEvents = Event::where('agent_id', $agent->id);
            $agentEvents = $this->queryDecider($BaseagentEvents, 'created_at', $duration)->get();

            foreach ($agentEvents as $event) {
                if (!isset($eventTypes[$event->event_type_id])) {
                    continue;
                }
                // Skip if event type is not found (deleted
                $eventTypeName = $eventTypes[$event->event_type_id];
                $all_events_count_users[$eventTypeName][$agent->name]++;
                $userEventCount++;
            }

            $all_events_count_users['All'][$agent->name] = $userEventCount;

            $data[$agent->name] = [
                'events_count' => $userEventCount,
                'events_by_type' => $this->getUserEvents($agent->id, $duration),
            ];

            $userTotalEventCounts[$agent->name] = $userEventCount;
        }

        $all_events_query = Event::whereIn('agent_id', $agents->pluck("id")->toArray());
        $all_events = $this->queryDecider($all_events_query, 'created_at', $duration)->get();

        foreach ($eventTypes as $eventType) {
            $all_events_count[$eventType] = $all_events->where('event_type_id', array_search($eventType, $eventTypes))->count();
        }

        return [
            'data' => $data,
            'all_events_count' => $all_events_count,
            'all_events_count_users' => $all_events_count_users,
            'user_total_event_counts' => $userTotalEventCounts,
        ];
    }

    public function eventsOverviewFetch(Request $request)
    {
        $data = [];

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

        $agents = $agentsQuery->get();
        // foreach ($agents as $agent) {
        //     $data[$agent->name] = $this->getUserEvents($agent->id, $request->duration);
        // }
        foreach ($agents as $agent) {
            $agentData = $this->getUserEvents($agent->id, $request->duration);
            $agentData['image'] = $agent->image; // Assuming 'image' is the attribute holding the image URL

            $data[$agent->name] = $agentData;
        }
        // dd($data);
        return view('admin.reports.partials._agents_events_stats')
            ->with('data', $data);

    }

    private function getUserEvents($id, $duration)
    {
        $eventCounts = [];

        $allEventTypes = EventType::where('status', true)->pluck('id');

        $events_query = Event::where('agent_id', $id);
        $events = $this->queryDecider($events_query, 'created_at', $duration)

            ->get()->groupBy('event_type_id');

        foreach ($allEventTypes as $eventTypeId) {
            $eventCounts[EventType::find($eventTypeId)->name ?? 'Null'] = 0;
        }

        if (isset($events[null])) {
            $eventCounts['Null'] = $events[null]->count();
        } else {
            $eventCounts['Null'] = 0;
        }

        foreach ($events as $eventTypeId => $eventGroup) {
            $eventCounts[EventType::find($eventTypeId)->name ?? 'Null'] = $eventGroup->count();
        }
        return $eventCounts;
    }

    private function countEventsOfEachType($agentId, $duration)
    {
        $baseQuery = Event::where('agent_id', $agentId);
        $events = $this->queryDecider($baseQuery, 'created_at', $duration)->get();

        $eventTypes = EventType::where('status', true)->get();

        $data = [];

        foreach ($eventTypes as $eventType) {
            $data[$eventType->name] = $events->where('event_type_id', $eventType->id)->count();
        }

        return $data;

    }

    // private function queryDecider($query, $column, $operator)
    // {
    //     // custom&from=11-28-2023&to=11-28-2023
    //     if (strpos($operator, '&') !== false) {
    //         // now check to and from in the string
    //         $operator = explode('&', $operator);
    //         $start_date = Carbon::parse($operator[1])->format('Y-m-d');
    //         $end_date = Carbon::parse($operator[2])->format('Y-m-d');

    //         dd($start_date, $end_date);
    //         return $query->whereBetween($column, [$start_date, $end_date]);
    //     }else{
    //         dd('Else Case');
    //         return $this->queryDecider($query, $column, $operator);
    //     }
    // }

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
