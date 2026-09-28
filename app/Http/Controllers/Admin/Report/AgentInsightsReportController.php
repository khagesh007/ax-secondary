<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\User;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AgentInsightsReportController extends Controller
{
    private function getStartAndEndDate($date = null, $format = "Y-m-d"): array
    {
        $startDate = null;
        $endDate = null;
        if (strpos($date, " - ") !== false) {
            $dates = explode(" - ", $date);
            $startDate = $dates[0] ?? null;
            $endDate = $dates[1] ?? null;
        }

        if ($startDate !== null && $endDate !== null) {
            $startDate = Carbon::createFromFormat("d/m/Y", $startDate)->format($format);
            $endDate = Carbon::createFromFormat("d/m/Y", $endDate)->format($format);
        } else {
            $startDate = Carbon::now()->subDays(90)->format($format);
            $endDate = Carbon::now()->format($format);
        }

        return [
            "start_date" => $startDate,
            "end_date" => $endDate
        ];
    }

    private function applyDateFilter($query, $columnName, $date)
    {
        $dates = $this->getStartAndEndDate($date);
        $startDate = $dates["start_date"];
        $endDate = $dates["end_date"];

        return $query->whereBetween($columnName, [$startDate, $endDate]);
    }

    private function prepareHighAndLeastNoOfActivityData_bkp($groupIds = [], $date = null)
    {
        $dates = $this->getStartAndEndDate($date);
        $startDate = $dates["start_date"];
        $endDate = $dates["end_date"];

        $bindings = [
            $startDate, $endDate, // For leads
            $startDate, $endDate, // For lead_notes
            $startDate, $endDate, // For properties
            $startDate, $endDate, // For property_notes
            $startDate, $endDate, // For listing_leads
            $startDate, $endDate, // For listing_lead_notes
            $startDate, $endDate, // For events
        ];

        $leadCountQuery = "(SELECT COUNT(*) FROM leads l WHERE l.agent_id = users.id AND l.converted_to_listing_lead IS NULL AND l.status = 1 AND (l.created_by_id = users.id OR l.updated_by_id = users.id) AND l.created_at BETWEEN ? AND ?)";

        $leadNoteCountQuery = "(SELECT COUNT(*) FROM lead_notes ln WHERE ln.created_by_id = users.id AND ln.created_at BETWEEN ? AND ?)";

        $listingCountQuery = "(SELECT COUNT(*) FROM properties p WHERE p.agent_id = users.id AND p.is_archived = 0 AND (p.created_by_id = users.id OR p.updated_by_id = users.id) AND p.created_at BETWEEN ? AND ?)";

        $listingNoteCountQuery = "(SELECT COUNT(*) FROM property_notes pn WHERE pn.created_by_id = users.id AND pn.created_at BETWEEN ? AND ?)";

        $listingLeadCountQuery = "(SELECT COUNT(*) FROM listing_leads ll WHERE ll.agent_id = users.id AND (ll.created_by_id = users.id OR ll.updated_by_id = users.id) AND ll.created_at BETWEEN ? AND ?)";

        $listingLeadNoteCountQuery = "(SELECT COUNT(*) FROM listing_lead_notes lln WHERE lln.created_by_id = users.id AND lln.created_at BETWEEN ? AND ?)";

        $eventCountQuery = "(SELECT COUNT(*) FROM events e WHERE (e.created_by_id = users.id OR e.updated_by_id = users.id) AND e.created_at BETWEEN ? AND ?)";

        $selectQuery = "($leadCountQuery + $leadNoteCountQuery + $listingCountQuery + $listingNoteCountQuery + $listingLeadCountQuery + $listingLeadNoteCountQuery + $eventCountQuery) AS count";

        $highActivityUsers = User::select(
            "users.name as user_name",
            "users.id as user_id",
            "groups.name AS zone_name",
            "parent.name AS parent_name",
            "users.thumbnail AS user_thumbnail",
            DB::raw($selectQuery))
            ->setBindings($bindings)
            ->leftJoin("groups", "groups.id", "users.zone_id")
            ->leftJoin("users as parent", "parent.id", "users.parent_id")
            ->when(!empty($groupIds), fn($query) => $query->whereIn("users.zone_id", $groupIds))
            ->whereHas("roles", function ($query) {
                $query->where("name", "Agent");
            })
            ->orderByDesc("count")
            ->take(5)
            ->get();

        $leastActivityUsers = User::select(
            "users.name as user_name",
            "users.id as user_id",
            "groups.name AS zone_name",
            "parent.name AS parent_name",
            "users.thumbnail AS user_thumbnail",
            DB::raw($selectQuery))
            ->setBindings($bindings)
            ->leftJoin("groups", "groups.id", "users.zone_id")
            ->leftJoin("users as parent", "parent.id", "users.parent_id")
            ->when(!empty($groupIds), fn($query) => $query->whereIn("users.zone_id", $groupIds))
            ->whereHas("roles", function ($query) {
                $query->where("name", "Agent");
            })
            ->orderBy("count")
            ->take(5)
            ->get();

        return [
            "high_counts" => $highActivityUsers,
            "least_counts" => $leastActivityUsers
        ];
    }

    private function prepareHighAndLeastNoOfActivityData($groupIds = [], $date = null)
    {
        $selectQuery = "users.name AS user_name,";
        $selectQuery .= "users.id AS user_id,";
        $selectQuery .= "groups.name AS zone_name,";
        $selectQuery .= "parent.name AS parent_name,";
        $selectQuery .= "users.thumbnail AS user_thumbnail,";
        $selectQuery .= "COUNT(events.id) as count";

        $highActivityUsers = User::selectRaw($selectQuery)
            ->join("events", function ($join) use ($date) {
                $query = $join->on("users.id", "=", "events.created_by_id")
                    ->orOn("events.updated_by_id", "=", "users.id");
                $this->applyDateFilter($query, "events.created_at", $date);
            })
            ->leftJoin("groups", "groups.id", "users.zone_id")
            ->leftJoin("users as parent", "parent.id", "users.parent_id")
            ->where("users.status", true)
            ->when(!empty($groupIds), fn($query) => $query->whereIn("users.zone_id", $groupIds))
            ->whereHas("roles", function ($query) {
                $query->where("name", "Agent");
            })
            ->groupBy("user_name")
            ->groupBy("user_name")
            ->groupBy("user_id")
            ->orderByDesc("count")
            ->take(10)
            ->get();

        $leastActivityUsers = User::selectRaw($selectQuery)
            ->join("events", function ($join) use ($date) {
                $query = $join->on("users.id", "=", "events.created_by_id")
                    ->orOn("events.updated_by_id", "=", "users.id");
                $this->applyDateFilter($query, "events.created_at", $date);
            })
            ->leftJoin("groups", "groups.id", "users.zone_id")
            ->leftJoin("users as parent", "parent.id", "users.parent_id")
            ->where("users.status", true)
            ->when(!empty($groupIds), fn($query) => $query->whereIn("users.zone_id", $groupIds))
            ->whereHas("roles", function ($query) {
                $query->where("name", "Agent");
            })
            ->groupBy("user_name")
            ->groupBy("user_name")
            ->groupBy("user_id")
            ->orderBy("count")
            ->take(10)
            ->get();

        return [
            "high_counts" => $highActivityUsers,
            "least_counts" => $leastActivityUsers
        ];
    }

    private function prepareHighestNoOfLeadData($groupIds = [], $date = null)
    {
        $selectQuery = "users.name AS user_name,";
        $selectQuery .= "users.id AS user_id,";
        $selectQuery .= "groups.name AS zone_name,";
        $selectQuery .= "parent.name AS parent_name,";
        $selectQuery .= "users.thumbnail AS user_thumbnail,";
        $selectQuery .= "COUNT(leads.id) as count";

        $overallCounts = User::selectRaw($selectQuery)
            ->join("leads", function ($join) use ($date) {
                $query = $join->on("users.id", "=", "leads.agent_id");
                $this->applyDateFilter($query, "leads.created_at", $date);
            })
            ->join("sources", "leads.source_id", "=", "sources.id")
            ->leftJoin("groups", "groups.id", "users.zone_id")
            ->leftJoin("users as parent", "parent.id", "users.parent_id")
            ->where("users.status", true)
            ->when(!empty($groupIds), fn($query) => $query->whereIn("users.zone_id", $groupIds))
            ->whereHas("roles", function ($query) {
                $query->where("name", "Agent");
            })
            ->groupBy("user_name")
            ->groupBy("user_name")
            ->groupBy("user_id")
            ->orderByDesc("count")
            ->take(10)
            ->get();

        $propertyFinderCounts = User::selectRaw($selectQuery)
            ->join("leads", function ($join) use ($date) {
                $query = $join->on("users.id", "=", "leads.agent_id");
                $this->applyDateFilter($query, "leads.created_at", $date);
            })
            ->join("sources", "leads.source_id", "=", "sources.id")
            ->leftJoin("groups", "groups.id", "users.zone_id")
            ->leftJoin("users as parent", "parent.id", "users.parent_id")
            ->where("sources.name", "Property Finder")
            ->where("users.status", true)
            ->when(!empty($groupIds), fn($query) => $query->whereIn("users.zone_id", $groupIds))
            ->whereHas("roles", function ($query) {
                $query->where("name", "Agent");
            })
            ->groupBy("user_name")
            ->groupBy("user_name")
            ->groupBy("user_id")
            ->orderByDesc("count")
            ->take(10)
            ->get();

        $bayutCounts = User::selectRaw($selectQuery)
            ->join("leads", function ($join) use ($date) {
                $query = $join->on("users.id", "=", "leads.agent_id");
                $this->applyDateFilter($query, "leads.created_at", $date);
            })
            ->join("sources", "leads.source_id", "=", "sources.id")
            ->leftJoin("groups", "groups.id", "users.zone_id")
            ->leftJoin("users as parent", "parent.id", "users.parent_id")
            ->where("sources.name", "Bayut")
            ->where("users.status", true)
            ->when(!empty($groupIds), fn($query) => $query->whereIn("users.zone_id", $groupIds))
            ->whereHas("roles", function ($query) {
                $query->where("name", "Agent");
            })
            ->groupBy("user_name")
            ->groupBy("user_name")
            ->groupBy("user_id")
            ->orderByDesc("count")
            ->take(10)
            ->get();

        $dubizzleCounts = User::selectRaw($selectQuery)
            ->join("leads", function ($join) use ($date) {
                $query = $join->on("users.id", "=", "leads.agent_id");
                $this->applyDateFilter($query, "leads.created_at", $date);
            })
            ->join("sources", "leads.source_id", "=", "sources.id")
            ->leftJoin("groups", "groups.id", "users.zone_id")
            ->leftJoin("users as parent", "parent.id", "users.parent_id")
            ->where("sources.name", "Dubizzle")
            ->where("users.status", true)
            ->when(!empty($groupIds), fn($query) => $query->whereIn("users.zone_id", $groupIds))
            ->whereHas("roles", function ($query) {
                $query->where("name", "Agent");
            })
            ->groupBy("user_name")
            ->groupBy("user_name")
            ->groupBy("user_id")
            ->orderByDesc("count")
            ->take(10)
            ->get();

        return [
            "overall_counts" => $overallCounts,
            "property_finder_counts" => $propertyFinderCounts,
            "bayut_counts" => $bayutCounts,
            "dubizzle_counts" => $dubizzleCounts,
        ];
    }

    private function prepareHighestNoOfListingData($groupIds = [], $date = null)
    {
        $selectQuery = "users.name AS user_name,";
        $selectQuery .= "users.id AS user_id,";
        $selectQuery .= "groups.name AS zone_name,";
        $selectQuery .= "parent.name AS parent_name,";
        $selectQuery .= "users.thumbnail AS user_thumbnail,";
        $selectQuery .= "COUNT(properties.id) AS count";

        $overallCounts = User::selectRaw($selectQuery)
            ->join("properties", function ($join) use ($date) {
                $query = $join->on("users.id", "=", "properties.agent_id");
                $this->applyDateFilter($query, "properties.created_at", $date);
            })
            ->join("property_portals", "properties.id", "property_portals.property_id")
            ->join("portals", "portals.id", "property_portals.portal_id")
            ->leftJoin("groups", "groups.id", "users.zone_id")
            ->leftJoin("users as parent", "parent.id", "users.parent_id")
            ->where("properties.is_live", true)
            ->where("users.status", true)
            ->when(!empty($groupIds), fn($query) => $query->whereIn("users.zone_id", $groupIds))
            ->whereHas("roles", function ($query) {
                $query->where("name", "Agent");
            })
            ->groupBy("user_name")
            ->groupBy("user_name")
            ->groupBy("user_id")
            ->orderByDesc("count")
            ->take(10)
            ->get();

        $propertyFinderCounts = User::selectRaw($selectQuery)
            ->join("properties", function ($join) use ($date) {
                $query = $join->on("users.id", "=", "properties.agent_id");
                $this->applyDateFilter($query, "properties.created_at", $date);
            })
            ->join("property_portals", "properties.id", "property_portals.property_id")
            ->join("portals", "portals.id", "property_portals.portal_id")
            ->leftJoin("groups", "groups.id", "users.zone_id")
            ->leftJoin("users as parent", "parent.id", "users.parent_id")
            ->where("properties.is_live", true)
            ->where("users.status", true)
            ->when(!empty($groupIds), fn($query) => $query->whereIn("users.zone_id", $groupIds))
            ->whereHas("roles", function ($query) {
                $query->where("name", "Agent");
            })
            ->where("portals.name", "Property Finder")
            ->groupBy("user_name")
            ->groupBy("user_name")
            ->groupBy("user_id")
            ->orderByDesc("count")
            ->take(10)
            ->get();

        $bayutCounts = User::selectRaw($selectQuery)
            ->join("properties", function ($join) use ($date) {
                $query = $join->on("users.id", "=", "properties.agent_id");
                $this->applyDateFilter($query, "properties.created_at", $date);
            })
            ->join("property_portals", "properties.id", "property_portals.property_id")
            ->join("portals", "portals.id", "property_portals.portal_id")
            ->leftJoin("groups", "groups.id", "users.zone_id")
            ->leftJoin("users as parent", "parent.id", "users.parent_id")
            ->where("properties.is_live", true)
            ->where("users.status", true)
            ->when(!empty($groupIds), fn($query) => $query->whereIn("users.zone_id", $groupIds))
            ->whereHas("roles", function ($query) {
                $query->where("name", "Agent");
            })
            ->where("portals.name", "Bayut")
            ->groupBy("user_name")
            ->groupBy("user_name")
            ->groupBy("user_id")
            ->orderByDesc("count")
            ->take(10)
            ->get();

        $dubizzleCounts = User::selectRaw($selectQuery)
            ->join("properties", function ($join) use ($date) {
                $query = $join->on("users.id", "=", "properties.agent_id");
                $this->applyDateFilter($query, "properties.created_at", $date);
            })
            ->join("property_portals", "properties.id", "property_portals.property_id")
            ->join("portals", "portals.id", "property_portals.portal_id")
            ->leftJoin("groups", "groups.id", "users.zone_id")
            ->leftJoin("users as parent", "parent.id", "users.parent_id")
            ->where("properties.is_live", true)
            ->where("users.status", true)
            ->when(!empty($groupIds), fn($query) => $query->whereIn("users.zone_id", $groupIds))
            ->whereHas("roles", function ($query) {
                $query->where("name", "Agent");
            })
            ->where("portals.name", "Dubizzle")
            ->groupBy("user_name")
            ->groupBy("user_name")
            ->groupBy("user_id")
            ->orderByDesc("count")
            ->take(10)
            ->get();

        return [
            "overall_counts" => $overallCounts,
            "property_finder_counts" => $propertyFinderCounts,
            "bayut_counts" => $bayutCounts,
            "dubizzle_counts" => $dubizzleCounts,
        ];
    }

    private function prepareHighestNoOfQualityScoreData($groupIds = [], $date = null)
    {
        $userSelectQuery = "users.name AS user_name,";
        $userSelectQuery .= "users.id AS user_id,";
        $userSelectQuery .= "groups.name AS zone_name,";
        $userSelectQuery .= "parent.name AS parent_name,";
        $userSelectQuery .= "users.thumbnail AS user_thumbnail,";

        $selectQuery = $userSelectQuery . "COALESCE(ROUND(AVG(CASE WHEN property_meta.meta_key = 'pf_rank' THEN property_meta.meta_value END), 2), 0) AS score";

        $propertyFinderScores = User::selectRaw($selectQuery)
            ->join("properties", function ($join) use ($date) {
                $query = $join->on("users.id", "=", "properties.agent_id");
                $this->applyDateFilter($query, "properties.created_at", $date);
            })
            ->join("property_meta", "properties.id", "property_meta.property_id")
            ->leftJoin("groups", "groups.id", "users.zone_id")
            ->leftJoin("users as parent", "parent.id", "users.parent_id")
            ->where("users.status", true)
            ->when(!empty($groupIds), fn($query) => $query->whereIn("users.zone_id", $groupIds))
            ->whereHas("roles", function ($query) {
                $query->where("name", "Agent");
            })
            ->groupBy("user_name")
            ->groupBy("user_name")
            ->groupBy("user_id")
            ->orderByDesc("score")
            ->take(10)
            ->get();

        $selectQuery = $userSelectQuery . "COALESCE(ROUND(AVG(CASE WHEN property_meta.meta_key = 'bayut_rank' THEN property_meta.meta_value END), 2), 0) AS score";
        $bayutScores = User::selectRaw($selectQuery)
            ->join("properties", function ($join) use ($date) {
                $query = $join->on("users.id", "=", "properties.agent_id");
                $this->applyDateFilter($query, "properties.created_at", $date);
            })
            ->join("property_meta", "properties.id", "property_meta.property_id")
            ->leftJoin("groups", "groups.id", "users.zone_id")
            ->leftJoin("users as parent", "parent.id", "users.parent_id")
            ->where("users.status", true)
            ->when(!empty($groupIds), fn($query) => $query->whereIn("users.zone_id", $groupIds))
            ->whereHas("roles", function ($query) {
                $query->where("name", "Agent");
            })
            ->groupBy("user_name")
            ->groupBy("user_name")
            ->groupBy("user_id")
            ->orderByDesc("score")
            ->take(10)
            ->get();

        $selectQuery = $userSelectQuery . "COALESCE(ROUND(AVG(CASE WHEN property_meta.meta_key = 'dubizzle_rank' THEN property_meta.meta_value END), 2), 0) AS score";
        $dubizzleScores = User::selectRaw($selectQuery)
            ->join("properties", function ($join) use ($date) {
                $query = $join->on("users.id", "=", "properties.agent_id");
                $this->applyDateFilter($query, "properties.created_at", $date);
            })
            ->join("property_meta", "properties.id", "property_meta.property_id")
            ->leftJoin("groups", "groups.id", "users.zone_id")
            ->leftJoin("users as parent", "parent.id", "users.parent_id")
            ->where("users.status", true)
            ->when(!empty($groupIds), fn($query) => $query->whereIn("users.zone_id", $groupIds))
            ->whereHas("roles", function ($query) {
                $query->where("name", "Agent");
            })
            ->groupBy("user_name")
            ->groupBy("user_name")
            ->groupBy("user_id")
            ->orderByDesc("score")
            ->take(10)
            ->get();

        return [
            "property_finder_scores" => $propertyFinderScores,
            "bayut_scores" => $bayutScores,
            "dubizzle_scores" => $dubizzleScores,
        ];
    }

    private function prepareUserWiseInsightsData($groupIds = [], $date = null)
    {
        $propertySubQuery = DB::table("properties")
            ->select(
                "agent_id",
                DB::raw("COUNT(id) as listings_count"),
                DB::raw('AVG(DATEDIFF(NOW(), properties.created_at)) AS average_listings_duration'),
            )
            ->groupBy("agent_id");

        $propertySubQuery = $this->applyDateFilter($propertySubQuery, "properties.created_at", $date);

        $leadsSubQuery = DB::table("leads")
            ->select(
                "agent_id",
                DB::raw("COUNT(id) as leads_count"),
                DB::raw("COUNT(CASE WHEN lead_medium_id = 4 THEN 1 END) AS wa_leads_count"),
                DB::raw("COUNT(CASE WHEN lead_medium_id = 1 THEN 1 END) AS email_leads_count"),
                DB::raw("COUNT(CASE WHEN lead_medium_id = 2 THEN 1 END) AS call_leads_count"),
            )
            ->whereNull("converted_to_listing_lead")
            ->groupBy("agent_id");

        $leadsSubQuery = $this->applyDateFilter($leadsSubQuery, "leads.created_at", $date);

        $propertyMetaSubQuery = DB::table("properties as p")
            ->select(
                "p.agent_id",
                DB::raw("AVG(CASE WHEN pm.meta_key = 'pf_rank' THEN pm.meta_value END) as pf_score"),
                DB::raw("AVG(CASE WHEN pm.meta_key = 'bayut_rank' THEN pm.meta_value END) as bayut_score"),
                DB::raw("AVG(CASE WHEN pm.meta_key = 'dubizzle_rank' THEN pm.meta_value END) as dubizzle_score"),
                DB::raw("COUNT(CASE WHEN pm.meta_key = 'pf_verified' AND pm.meta_value = '1' THEN 1 END) AS pf_verified"),
                DB::raw("COUNT(CASE WHEN pm.meta_key = 'bayut_review_status' AND pm.meta_value = 'Approved' THEN 1 END) AS bayut_verified"),
                DB::raw("COUNT(CASE WHEN pm.meta_key = 'bayut_trucheck_status' AND pm.meta_value = 'Truchecked' THEN 1 END) AS bayut_truchecked"),
                DB::raw("COUNT(CASE WHEN pm.meta_key = 'bayut_trucheck_status' AND pm.meta_value = 'checked' THEN 1 END) AS bayut_checked"),
            )
            ->join("property_meta as pm", "p.id", "=", "pm.property_id")
            ->groupBy("p.agent_id");

        $propertyMetaSubQuery = $this->applyDateFilter($propertyMetaSubQuery, "p.created_at", $date);


        $whatsAppLeadSubQuery = DB::table("leads as l")
            ->select(
                "l.agent_id",
                DB::raw("(AVG(TIMESTAMPDIFF(SECOND, sl.created_at, sl.opted_in)) / 60) AS wa_response_time"),
                DB::raw("(COUNT(CASE WHEN sl.opted_in IS NOT NULL THEN l.id END) / COUNT(l.id)) * 100 AS wa_response_rate"),
            )
            ->join("shortlinks as sl", "l.id", "=", "sl.lead_id")
            ->groupBy("l.agent_id");

        $whatsAppLeadSubQuery = $this->applyDateFilter($whatsAppLeadSubQuery, "l.created_at", $date);

        $callsSubQuery = DB::table("call_logs")
            ->select(
                "user_id",
                DB::raw("(COUNT(CASE WHEN call_ended_at IS NOT NULL AND call_duration > 0 THEN call_logs.id END) / COUNT(id)) * 100 AS call_response_rate")
            )
            ->whereNotNull("lead_id")
            ->groupBy("user_id");

        $callsSubQuery = $this->applyDateFilter($callsSubQuery, "call_logs.call_ended_at", $date);


        $users = User::leftJoinSub($propertySubQuery, "p", "users.id", "=", "p.agent_id")
            ->leftJoinSub($leadsSubQuery, "l", "users.id", "=", "l.agent_id")
            ->leftJoinSub($propertyMetaSubQuery, "pm", "users.id", "=", "pm.agent_id")
            ->leftJoinSub($whatsAppLeadSubQuery, "wal", "users.id", "=", "wal.agent_id")
            ->leftJoinSub($callsSubQuery, "cl", "users.id", "=", "cl.user_id")
            ->leftJoin("groups", "groups.id", "users.zone_id")
            ->leftJoin("users as parent", "parent.id", "users.parent_id")
            ->select(
                "users.name as user_name",
                "users.id as user_id",
                "groups.name AS zone_name",
                "parent.name AS parent_name",
                "users.thumbnail AS user_thumbnail",
                DB::raw("COALESCE(p.listings_count, 0) as listings_count"),
                DB::raw("ROUND(COALESCE(p.average_listings_duration, 0), 0) AS average_listings_duration"),
                DB::raw("ROUND(COALESCE(pm.pf_score, 0), 2) as pf_score"),
                DB::raw("ROUND(COALESCE(pm.bayut_score, 0), 2) as bayut_score"),
                DB::raw("ROUND(COALESCE(pm.dubizzle_score, 0), 2) as dubizzle_score"),
                DB::raw("COALESCE(pm.pf_verified, 0) AS pf_verified"),
                DB::raw("COALESCE(pm.bayut_verified, 0) AS bayut_verified"),
                DB::raw("COALESCE(l.leads_count, 0) as leads_count"),
                DB::raw("COALESCE(l.wa_leads_count, 0) as wa_leads_count"),
                DB::raw("COALESCE(l.email_leads_count, 0) AS email_leads_count"),
                DB::raw("COALESCE(l.call_leads_count, 0) AS call_leads_count"),

                DB::raw("IF(wal.wa_response_time IS NULL, NULL, ROUND(wal.wa_response_time, 2)) AS wa_response_time"),
                DB::raw("ROUND(COALESCE(wal.wa_response_rate, 0), 2) AS wa_response_rate"),

                DB::raw("ROUND(COALESCE(cl.call_response_rate, 0), 2) AS call_response_rate"),
            )
            ->where("users.status", true)
            ->when(!empty($groupIds), fn($query) => $query->whereIn("users.zone_id", $groupIds))
            ->whereHas("roles", function ($query) {
                $query->where("name", "Agent");
            })
            ->get();

        return $users;
    }

    public function index(Request $request)
    {
        if (!auth()->user()->can('Agent Insights Reports')) {
            abort(403, 'Unauthorized action.');
        }

        $groups = Group::with(["user"])
            ->whereNull("parent_id")
            ->orderBy("order")
            ->get();

        $dates = $this->getStartAndEndDate($request->date, "d-m-Y");

        $startDate = $dates["start_date"];
        $endDate = $dates["end_date"];

        $loggedInUser = auth()->user();

        return view("admin.reports.agent_insights.index", compact("groups", "startDate", "endDate", "loggedInUser"));
    }

    public function getView(Request $request): View|null
    {
        $loggedInUser = auth()->user();

        $zoneIds = [];
        if (!is_null($loggedInUser->zone_id)){
            $zoneIds[] = $loggedInUser->zone_id;
        }else{
            $zoneIds = $request->groupIds;
        }

        if ($request->type == "agentWiseInsights") {
            $insights = $this->prepareUserWiseInsightsData($zoneIds, $request->date);
            return view("admin.reports.agent_insights.partials.agent_wise_insights", compact("insights"));
        }

        if ($request->type == "overviewInsights") {
            $insights = [
                "high_and_least_no_of_activities" => $this->prepareHighAndLeastNoOfActivityData($zoneIds, $request->date),
                "highest_no_of_leads" => $this->prepareHighestNoOfLeadData($zoneIds, $request->date),
                "highest_no_of_listings" => $this->prepareHighestNoOfListingData($zoneIds, $request->date),
                "highest_no_of_quality_scores" => $this->prepareHighestNoOfQualityScoreData($zoneIds, $request->date),
            ];

            return view("admin.reports.agent_insights.partials.overview_insights", compact("insights"));
        }
        return null;
    }

    public function updateUsersZone()
    {
        $users = User::whereIn("id", [9, 23, 1165, 1310])->get();
        foreach ($users as $user){
            $childrenIds = getUserChildrenIdsByID($user->id);
            User::whereIn("id", $childrenIds)->whereNull("zone_id")->update(["zone_id" => $user->zone_id]);
        }
    }
}
