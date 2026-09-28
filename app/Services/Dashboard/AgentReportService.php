<?php

namespace App\Services\Dashboard;

use App\Models\User;
use App\Services\Dashboard\DashboardReportFilterService;

class AgentReportService
{

    protected $filters;

    public function __construct(DashboardReportFilterService $filters)
    {
        $this->filters = $filters;
    }
    public function summary($request, $params)
    {
        $data = [
            'highest_no_of_leads' => $this->highestNoOfLeadData($params),
            'highest_no_of_listings' => $this->highestNoOfListingData($params),
            'highest_no_of_quality_score' => $this->highestNoOfQualityScoreData($params),
            'activities' => $this->highAndLeastNoOfActivityData($params),
            'viewings' => $this->highestNoOfViewingData($params),
        ];
        return $data;
    }

    public function highestNoOfLeadData($params, $limit = 10): array
    {

        $query = User::query();

        $selectQuery = "users.name AS user_name,";
        $selectQuery .= "users.id AS user_id,";
        $selectQuery .= "MAX(users.thumbnail) AS user_thumbnail,";
        $selectQuery .= "COUNT(DISTINCT leads.id) as count";

        $query->selectRaw($selectQuery)->join("leads", function ($join) use ($params) {
            $join->on("users.id", "=", "leads.agent_id")
                ->where(function ($query) use ($params) {
                    $query->whereNull("leads.deleted_at")->when($params['show_deleted_leads'], fn($q1) => $q1->orWhereNotNull("leads.deleted_at"));
                })
                ->whereNotNull("leads.agent_id")
                ->when($params['filter_by_users'], function ($q) use ($params) {
                    $q->whereIn("agent_id", $params['user_ids']);
                })
                ->when($params['filter_by_users'], function ($q) use ($params) {
                    $q->where(function ($q) use ($params) {
                        $q->whereIn("agent_id", $params['user_ids'])->whereNull("converted_to_listing_lead");
                    });
                })
                ->when(auth()->user()->hasRole("Admin - Abu Dhabi"), function ($q) {
                    $q->where("leads.region_id", 2);
                })
                ->when(!($params['show_archived_leads'] && $params['isSuperAdmin']), fn($q) => $q->where("leads.status", true)->where("is_archive", false))
                ->when(!empty($params['start_date']), fn($q) => $q->whereDate("leads.created_at", ">=", $params['start_date']))
                ->when(!empty($params['end_date']), fn($q) => $q->whereDate("leads.created_at", "<=", $params['end_date']));
        })
            ->leftJoin("sources", "leads.source_id", "=", "sources.id")
            ->when($params['filter_by_users'], fn($query) => $query->whereIn("users.id", $params['user_ids']))
            ->when(!$params['show_inactive_users'], fn($q) => $q->where("users.status", true))
            ->when($params['group'] == 'secondary', function ($q) use ($params) {
                $q->where("users.region_id", 1);
            })
            ->whereHas("roles", function ($query) {
                $query->where("name", "Agent");
            })
            ->whereNotIn('users.id', $this->filters->getExcludedUsers())
            ->when(!$params['show_inactive_users'], function ($q) use ($params) {
                    $q->where("users.status", true);
            })
            ->groupBy("users.id")
            ->groupBy("users.name")
            ->orderByDesc("count")

            ->take($limit);

        $overallCountQuery = $query;
        $propertyFinderCountQuery = clone $query;
        $bayutCountQuery = clone $query;
        $dubizzleCountQuery = clone $query;

        $overallCounts = $overallCountQuery->get();
        $overAllCountData = [];
        foreach ($overallCounts as $oac){
            $user = User::where('id',$oac->user_id)->with(['parent','parent.group'])->first();
            $overAllCountData[] = [
                'user_name' => $oac->user_name,
                'user_id' => $oac->user_id,
                'count' => $oac->count,
                'image' => $user->thumb_url,
                'group' => $user?->parent?->group?->name,
                'manager' => $user?->parent?->name,
            ];
        }
        $propertyFinderCounts = $propertyFinderCountQuery->where("sources.name", "Property Finder")->get();
        $propertyFinderData = [];
        foreach ($propertyFinderCounts as $pfc){
            $user = User::where('id',$pfc->user_id)->with(['parent','parent.group'])->first();
            $propertyFinderData[] = [
                'user_name' => $pfc->user_name,
                'user_id' => $pfc->user_id,
                'count' => $pfc->count,
                'image' => $user->thumb_url,
                'group' => $user?->parent?->group?->name,
                'manager' => $user?->parent?->name,
            ];
        }
        $bayutCounts = $bayutCountQuery->where("sources.name", "Bayut")->get();
        $bayutCountsData = [];
        foreach ($bayutCounts as $bc){
            $user = User::where('id',$bc->user_id)->with(['parent','parent.group'])->first();
            $bayutCountsData[] = [
                'user_name' => $bc->user_name,
                'user_id' => $bc->user_id,
                'count' => $bc->count,
                'image' => $user->thumb_url,
                'group' => $user?->parent?->group?->name,
                'manager' => $user?->parent?->name,
            ];
        }
        $dubizzleCounts = $dubizzleCountQuery->where("sources.name", "Dubizzle")->get();
        $dubizzleCountsData = [];
        foreach ($dubizzleCounts as $dc){
            $user = User::where('id',$dc->user_id)->with(['parent','parent.group'])->first();
            $dubizzleCountsData[] = [
                'user_name' => $dc->user_name,
                'user_id' => $dc->user_id,
                'count' => $dc->count,
                'image' => $user->thumb_url,
                'group' => $user?->parent?->group?->name,
                'manager' => $user?->parent?->name,
            ];
        }
        return [
            "overall_counts" => $overAllCountData,
            "property_finder_counts" => $propertyFinderData,
            "bayut_counts" => $bayutCountsData,
            "dubizzle_counts" => $dubizzleCountsData,
        ];
    }

    public function highestNoOfListingData($params, $limit = 10)
    {
        $selectQuery = "users.name AS user_name,";
        $selectQuery .= "users.id AS user_id,";
        $selectQuery .= "MAX(users.thumbnail) AS user_thumbnail,";
        $selectQuery .= "COUNT(DISTINCT properties.id) AS count";

        $baseQuery = User::selectRaw($selectQuery);
        $baseQuery->join("properties", function ($join) use ($params) {
            $join->on("users.id", "=", "properties.agent_id")
                ->when(auth()->user()->hasRole("Agent"), function ($q) use ($params) {
                    $q->where("agent_id", $params['user_ids']);
                })
                ->when(auth()->user()->hasRole("Admin - Abu Dhabi"), function ($q) {
                    $q->where("properties.region_id", 2);
                })
                ->when($params['filter_by_users'], function ($q) use ($params) {
                    $q->where(function ($q) use ($params) {
                        $q->whereIn("agent_id", $params['user_ids'])->orWhere("publish_status", "POCKET")->orWhere('is_live', true);
                    });
                })
                ->whereNotNull("agent_id")
                ->where("is_live", true)
                ->when(!$params['exempt_listing_date_filter'] && !empty($params['start_date']), fn($q) => $q->whereDate("properties.published_date", ">=", $params['start_date']))
                ->when(!$params['exempt_listing_date_filter'] && !empty($params['end_date']), fn($q) => $q->whereDate("properties.published_date", "<=", $params['end_date']));
        })
            ->join("property_portals", "properties.id", "property_portals.property_id")
            ->join("portals", "portals.id", "property_portals.portal_id")
            ->whereHas("roles", function ($query) {
                $query->where("name", "Agent");
            })
            ->whereNull('properties.unpublished_date')
            ->when($params['filter_by_users'], fn($query) => $query->whereIn("users.id", $params['user_ids']))
            ->when(!$params['show_inactive_users'], fn($q) => $q->where("users.status", true))
            ->when($params['group'] == 'secondary', function ($q) use ($params) {
                $q->where("users.region_id", 1);
            })
            ->whereNotIn('users.id', $this->filters->getExcludedUsers())
            ->groupBy("users.id")
            ->groupBy("users.name")
            ->orderByDesc("count")
            ->take($limit);

        $overallCountQuery = $baseQuery;
        $propertyFinderCountQuery = clone $baseQuery;
        $dubizzleCountQuery = clone $baseQuery;
        $bayutCountQuery = clone $baseQuery;

        $overallCounts = $overallCountQuery->get();
        $overallCountsData = [];
        foreach ($overallCounts as $oac){
            $user = User::where('id',$oac->user_id)->with(['parent','parent.group'])->first();
            $overallCountsData[] = [
                'user_name' => $oac->user_name,
                'user_id' => $oac->user_id,
                'count' => $oac->count,
                'image' => $user->thumb_url,
                'group' => $user?->parent?->group?->name,
                'manager' => $user?->parent?->name,
            ];
        }
        $bayutCounts = $propertyFinderCountQuery->where("portals.name", "Property Finder")->get();
        $bayutCountsData = [];
        foreach ($bayutCounts as $bc){
            $user = User::where('id',$bc->user_id)->with(['parent','parent.group'])->first();
            $bayutCountsData[] = [
                'user_name' => $bc->user_name,
                'user_id' => $bc->user_id,
                'count' => $bc->count,
                'image' => $user->thumb_url,
                'group' => $user?->parent?->group?->name,
                'manager' => $user?->parent?->name,
            ];
        }

        $propertyFinderCounts = $dubizzleCountQuery->where("portals.name", "Bayut")->get();
        $propertyFinderData = [];
        foreach ($propertyFinderCounts as $pfc){
            $user = User::where('id',$pfc->user_id)->with(['parent','parent.group'])->first();
            $propertyFinderData[] = [
                'user_name' => $pfc->user_name,
                'user_id' => $pfc->user_id,
                'count' => $pfc->count,
                'image' => $user->thumb_url,
                'group' => $user?->parent?->group?->name,
                'manager' => $user?->parent?->name,
            ];
        }
        $dubizzleCounts = $bayutCountQuery->where("portals.name", "Dubizzle")->get();
        $dubizzleCountsData = [];
        foreach ($dubizzleCounts as $dc){
            $user = User::where('id',$dc->user_id)->with(['parent','parent.group'])->first();
            $dubizzleCountsData[] = [
                'user_name' => $dc->user_name,
                'user_id' => $dc->user_id,
                'count' => $dc->count,
                'image' => $user->thumb_url,
                'group' => $user?->parent?->group?->name,
                'manager' => $user?->parent?->name,
            ];
        }
        return [
            "overall_counts" => $overallCountsData,
            "property_finder_counts" => $propertyFinderData,
            "bayut_counts" => $bayutCountsData,
            "dubizzle_counts" => $dubizzleCountsData,
        ];
    }

    public function highestNoOfQualityScoreData($params, $limit = 10)
    {
        $selectQuery = "users.name AS user_name,";
        $selectQuery .= "users.id AS user_id,";
        $selectQuery .= "MAX(users.thumbnail) AS user_thumbnail,";

        $baseQuery = User::query();
        $baseQuery->join("properties", function ($join) use ($params) {
            $join->on("users.id", "=", "properties.agent_id")
                ->when(auth()->user()->hasRole("Agent"), function ($q) use ($params) {
                    $q->where("agent_id", $params['user_ids']);
                })
                ->when(auth()->user()->hasRole("Admin - Abu Dhabi"), function ($q) {
                    $q->where("properties.region_id", 2);
                })
                ->when($params['filter_by_users'], function ($q) use ($params) {
                    $q->where(function ($q) use ($params) {
                        $q->whereIn("agent_id", $params['user_ids'])->orWhere("publish_status", "POCKET")->orWhere('is_live', true);
                    });
                })
                ->whereNotNull("agent_id")
                ->where("is_live", true)
                ->when(!$params['exempt_listing_date_filter'] && !empty($params['start_date']), fn($q) => $q->whereDate("properties.published_date", ">=", $params['start_date']))
                ->when(!$params['exempt_listing_date_filter'] && !empty($params['end_date']), fn($q) => $q->whereDate("properties.published_date", "<=", $params['end_date']));
        })
            ->join("property_meta", "properties.id", "property_meta.property_id")
            ->join("property_portals", "properties.id", "property_portals.property_id")
            ->join("portals", "portals.id", "property_portals.portal_id")
            ->whereHas("roles", function ($query) {
                $query->where("name", "Agent");
            })
            ->whereNull('properties.unpublished_date')
            ->when($params['filter_by_users'], fn($query) => $query->whereIn("users.id", $params['user_ids']))
            ->when(!$params['show_inactive_users'], fn($q) => $q->where("users.status", true))
            ->when($params['group'] == 'secondary', function ($q) use ($params) {
                $q->where("users.region_id", 1);
            })
            ->whereNotIn('users.id', $this->filters->getExcludedUsers())
            ->groupBy("users.id")
            ->groupBy("users.name")
            ->orderByDesc("count")
            ->take($limit);

        $propertyFinderBaseQuery = $baseQuery;
        $bayutBaseQuery = clone $baseQuery;
        $dubizzleBaseQuery = clone $baseQuery;

        $propertyFinderScores = $propertyFinderBaseQuery->selectRaw($selectQuery . "COALESCE(ROUND(AVG(CASE WHEN property_meta.meta_key = 'pf_rank' THEN property_meta.meta_value END), 2), 0) AS count")->get();
        $bayutScores = $bayutBaseQuery->selectRaw($selectQuery . "COALESCE(ROUND(AVG(CASE WHEN property_meta.meta_key = 'bayut_rank' THEN property_meta.meta_value END), 2), 0) AS count")->get();
        $dubizzleScores = $dubizzleBaseQuery->selectRaw($selectQuery . "COALESCE(ROUND(AVG(CASE WHEN property_meta.meta_key = 'dubizzle_rank' THEN property_meta.meta_value END), 2),0) AS count")->get();

        $propertyFinderData = [];
        foreach ($propertyFinderScores as $pfs){
            $user = User::where('id',$pfs->user_id)->with(['parent','parent.group'])->first();
            $propertyFinderData[] = [
                'user_name' => $pfs->user_name,
                'user_id' => $pfs->user_id,
                'count' => $pfs->count,
                'image' => $user->thumb_url,
                'group' => $user?->parent?->group?->name,
                'manager' => $user?->parent?->name,
            ];
        }
        $bayutScoreData = [];
        foreach ($bayutScores as $bs){
            $user = User::where('id',$bs->user_id)->with(['parent','parent.group'])->first();
            $bayutScoreData[] = [
                'user_name' => $bs->user_name,
                'user_id' => $bs->user_id,
                'count' => $bs->count,
                'image' => $user->thumb_url,
                'group' => $user?->parent?->group?->name,
                'manager' => $user?->parent?->name,
            ];
        }
        $dubizzleScoreData = [];
        foreach ($dubizzleScores as $ds){
            $user = User::where('id',$ds->user_id)->with(['parent','parent.group'])->first();
            $dubizzleScoreData[] = [
                'user_name' => $ds->user_name,
                'user_id' => $ds->user_id,
                'count' => $ds->count,
                'image' => $user->thumb_url,
                'group' => $user?->parent?->group?->name,
                'manager' => $user?->parent?->name,
            ];
        }
        return [
            "property_finder_counts" => $propertyFinderData,
            "bayut_counts" => $bayutScoreData,
            "dubizzle_counts" => $dubizzleScoreData,
        ];
    }

    public function highAndLeastNoOfActivityData($params, $limit = 10)
    {
        $selectQuery = "users.name AS user_name,";
        $selectQuery .= "users.id AS user_id,";
        $selectQuery .= "MAX(users.thumbnail) AS user_thumbnail,";
        $selectQuery .= "SUM(user_activity_counts.count) as count";

        $baseQuery = User::selectRaw($selectQuery)
            ->leftJoin('user_activity_counts', 'user_activity_counts.user_id', '=', 'users.id')
            ->whereHas("roles", function ($query) {
                $query->where("name", "Agent");
            })
            ->when(!empty($params['start_date']), fn($q) => $q->whereDate("user_activity_counts.reporting_date", ">=", $params['start_date']))
            ->when(!empty($params['end_date']), fn($q) => $q->whereDate("user_activity_counts.reporting_date", "<=", $params['end_date']))
            ->when($params['filter_by_users'], fn($query) => $query->whereIn("users.id", $params['user_ids']))
            ->when(!$params['show_inactive_users'], fn($q) => $q->where("users.status", true))
            ->when($params['group'] == 'secondary', function ($q) use ($params) {
                $q->where("users.region_id", 1);
            })
            ->whereNotIn('users.id', $this->filters->getExcludedUsers())
            ->groupBy("users.id")
            ->groupBy("users.name")
            ->take($limit);

        $highActivityUsersQuery = $baseQuery;
        $leastActivityUsersQuery = clone $baseQuery;

        $highActivityUsers = $highActivityUsersQuery->orderByDesc("count")->get();
        $leastActivityUsers = $leastActivityUsersQuery->orderBy("count")->get();

        $highestCountData = [];
        foreach ($highActivityUsers as $hc){
            $user = User::where('id',$hc->user_id)->with(['parent','parent.group'])->first();
            $highestCountData[] = [
                'user_name' => $hc->user_name,
                'user_id' => $hc->user_id,
                'count' => $hc->count,
                'image' => $user->thumb_url,
                'group' => $user?->parent?->group?->name,
                'manager' => $user?->parent?->name,
            ];
        }
        $leastCountData = [];
        foreach ($leastActivityUsers as $lc){
            $user = User::where('id',$lc->user_id)->with(['parent','parent.group'])->first();
            $leastCountData[] = [
                'user_name' => $lc->user_name,
                'user_id' => $lc->user_id,
                'count' => $lc->count,
                'image' => $user->thumb_url,
                'group' => $user?->parent?->group?->name,
                'manager' => $user?->parent?->name,
            ];
        }
        return [
            "high_counts" => $highestCountData,
            "least_counts" => $leastCountData,
        ];
    }

    public function highestNoOfViewingData($params, $limit = 10)
    {
        $selectQuery = "users.name AS user_name,";
        $selectQuery .= "users.id AS user_id,";
        $selectQuery .= "MAX(users.thumbnail) AS user_thumbnail,";
        $selectQuery .= "COUNT(DISTINCT events.id) AS count";

        $baseQuery = User::selectRaw($selectQuery)
            ->leftJoin('events', 'events.agent_id', '=', 'users.id')
            ->leftJoin('event_types', 'event_types.id', '=', 'events.event_type_id')
            ->whereHas("roles", function ($query) {
                $query->where("name", "Agent");
            })
            ->where('event_types.name', 'Viewing')
//            ->when(auth()->user()->hasRole("Agent"), function ($q) use ($params) {
//                $q->where("id", auth()->id());
//            })
            ->when(!$params['exempt_listing_date_filter'] && !empty($params['start_date']), fn($q) => $q->whereDate("events.start_time", ">=", $params['start_date']))
            ->when(!$params['exempt_listing_date_filter'] && !empty($params['end_date']), fn($q) => $q->whereDate("events.start_time", "<=", $params['end_date']))
            ->when($params['filter_by_users'], fn($query) => $query->whereIn("users.id", $params['user_ids']))
            ->when(!$params['show_inactive_users'], fn($q) => $q->where("users.status", true))
            ->when($params['group'] == 'secondary', function ($q) use ($params) {
                $q->where("users.region_id", 1);
            })
            ->whereNotIn('users.id', $this->filters->getExcludedUsers())
            ->groupBy("users.id")
            ->groupBy("users.name")
            ->take($limit);

        $highViewingUsersQuery = $baseQuery;
        $leastViewingUsersQuery = clone $baseQuery;

        $highestCounts =  $highViewingUsersQuery->orderByDesc("count")->get();
        $highestCountData = [];
        foreach ($highestCounts as $hc){
            $user = User::where('id',$hc->user_id)->with(['parent','parent.group'])->first();
            $highestCountData[] = [
                'user_name' => $hc->user_name,
                'user_id' => $hc->user_id,
                'count' => $hc->count,
                'image' => $user->thumb_url,
                'group' => $user?->parent?->group?->name,
                'manager' => $user?->parent?->name,
            ];
        }
        $leasetCounts = $leastViewingUsersQuery->orderBy("count")->get();
        $leastCountData = [];
        foreach ($leasetCounts as $lc){
            $user = User::where('id',$lc->user_id)->with(['parent','parent.group'])->first();
            $leastCountData[] = [
                'user_name' => $lc->user_name,
                'user_id' => $lc->user_id,
                'count' => $lc->count,
                'image' => $user->thumb_url,
                'group' => $user?->parent?->group?->name,
                'manager' => $user?->parent?->name,
            ];
        }
        return [
            "high_counts" =>$highestCountData,
            "least_counts" => $leastCountData,
        ];
    }
}
