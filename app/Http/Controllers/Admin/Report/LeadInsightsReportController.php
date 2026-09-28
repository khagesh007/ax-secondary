<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Models\CallLog;
use App\Models\Group;
use App\Models\Lead;
use App\Models\User;
use Carbon\CarbonPeriod;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class LeadInsightsReportController extends Controller
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
        }else{
            $startDate = Carbon::now()->subDays(90)->format($format);
            $endDate = Carbon::now()->format($format);
        }

        return [
            "start_date" => $startDate,
            "end_date" => $endDate
        ];
    }

    private function getDayByNumber($number)
    {
        return match ($number) {
            1 => "Sun",
            2 => "Mon",
            3 => "Tue",
            4 => "Wed",
            5 => "Thu",
            6 => "Fri",
            7 => "Sat",
            default => "Other"
        };
    }

    private function getTimeByNumber($number)
    {
        return match ($number) {
            0 => "12 AM",
            1 => "01 AM",
            2 => "02 AM",
            3 => "03 AM",
            4 => "04 AM",
            5 => "05 AM",
            6 => "06 AM",
            7 => "07 AM",
            8 => "08 AM",
            9 => "09 AM",
            10 => "10 AM",
            11 => "11 AM",
            12 => "12 PM",
            13 => "01 PM",
            14 => "02 PM",
            15 => "03 PM",
            16 => "04 PM",
            17 => "05 PM",
            18 => "06 PM",
            19 => "07 PM",
            20 => "08 PM",
            21 => "09 PM",
            22 => "10 PM",
            23 => "11 PM",
            default => "Other"
        };
    }

    private function applyDateFilter($query, $columnName, $date)
    {
        $dates = $this->getStartAndEndDate($date);
        $startDate = $dates["start_date"];
        $endDate = $dates["end_date"];

        return $query->whereBetween($columnName, [$startDate, $endDate]);
    }

    private function prepareLeadOverviewSourceWiseData($groupIds = [], $date = "")
    {
        $agentIds = User::where("status", 1)
            ->whereHas("roles", function ($q) {
                $q->whereIn("name", ["Agent"]);
            })
            ->when(!empty($groupIds), fn($query) => $query->whereIn("users.zone_id", $groupIds))
            ->pluck("id");

        $query = Lead::query();
        $query = $this->applyDateFilter($query, "leads.created_at", $date);
        return $query->select(
            DB::raw("COUNT(CASE WHEN sources.name = 'Property Finder' THEN leads.id END) AS pf_count"),
            DB::raw("COUNT(CASE WHEN sources.name = 'Bayut' THEN leads.id END) AS bayut_count"),
            DB::raw("COUNT(CASE WHEN sources.name = 'Dubizzle' THEN leads.id END) AS dubizzle_count"),
        )
            ->join("sources", "leads.source_id", "=", "sources.id")
            ->whereIn("agent_id", $agentIds)
            ->first()->toArray();
    }

    private function prepareSourceWiseDateWiseLeadCountData($groupIds = [], $date = "")
    {
        $agentIds = User::where("status", 1)
            ->whereHas("roles", function ($q) {
                $q->whereIn("name", ["Agent"]);
            })
            ->when(!empty($groupIds), fn($query) => $query->whereIn("users.zone_id", $groupIds))
            ->pluck("id");

        $query = Lead::query();
        $query = $this->applyDateFilter($query, "leads.created_at", $date);

        $pfLeads = $query->select(
            DB::raw("DATE(leads.created_at) as date"),
            DB::raw("COUNT(CASE WHEN mediums.name = 'Email' THEN leads.id END) AS email_count"),
            DB::raw("COUNT(CASE WHEN mediums.name = 'Call' THEN leads.id END) AS call_count"),
            DB::raw("COUNT(CASE WHEN mediums.name = 'WhatsApp' THEN leads.id END) AS wa_count")
        )
            ->where("sources.name", "Property Finder")
            ->join("sources", "leads.source_id", "=", "sources.id")
            ->join("mediums", "leads.lead_medium_id", "=", "mediums.id")
            ->whereIn("agent_id", $agentIds)
            ->groupBy("date")
            ->orderBy("date")
            ->get()
            ->toArray();

        $query = Lead::query();
        $query = $this->applyDateFilter($query, "leads.created_at", $date);
        $bayutLeads = $query->select(
            DB::raw("DATE(leads.created_at) as date"),
            DB::raw("COUNT(CASE WHEN mediums.name = 'Email' THEN leads.id END) AS email_count"),
            DB::raw("COUNT(CASE WHEN mediums.name = 'Call' THEN leads.id END) AS call_count"),
            DB::raw("COUNT(CASE WHEN mediums.name = 'WhatsApp' THEN leads.id END) AS wa_count")
        )
            ->where("sources.name", "Bayut")
            ->join("sources", "leads.source_id", "=", "sources.id")
            ->join("mediums", "leads.lead_medium_id", "=", "mediums.id")
            ->whereIn("agent_id", $agentIds)
            ->groupBy("date")
            ->orderBy("date")
            ->get()
            ->toArray();

        $query = Lead::query();
        $query = $this->applyDateFilter($query, "leads.created_at", $date);

        $dubizzleLeads = $query->select(
            DB::raw("DATE(leads.created_at) as date"),
            DB::raw("COUNT(CASE WHEN mediums.name = 'Email' THEN leads.id END) AS email_count"),
            DB::raw("COUNT(CASE WHEN mediums.name = 'Call' THEN leads.id END) AS call_count"),
            DB::raw("COUNT(CASE WHEN mediums.name = 'WhatsApp' THEN leads.id END) AS wa_count")
        )
            ->where("sources.name", "Dubizzle")
            ->join("sources", "leads.source_id", "=", "sources.id")
            ->join("mediums", "leads.lead_medium_id", "=", "mediums.id")
            ->whereIn("agent_id", $agentIds)
            ->groupBy("date")
            ->orderBy("date")
            ->get()
            ->toArray();

        $dates = $this->getStartAndEndDate($date);
        $startDate = $dates["start_date"];
        $endDate = $dates["end_date"];
        $period = CarbonPeriod::create($startDate, $endDate);

        $data = [];
        foreach ($period as $date) {
            $formattedDate = $date->toDateString();
            $index = array_search($formattedDate, array_column($pfLeads, "date"));
            if ($index !== false){
                $data["pf"]["dates"][] = $formattedDate;
                $data["pf"]["email_counts"][] = $pfLeads[$index]["email_count"];
                $data["pf"]["call_counts"][] = $pfLeads[$index]["call_count"];
                $data["pf"]["wa_counts"][] = $pfLeads[$index]["wa_count"];
            }else{
                $data["pf"]["dates"][] = $formattedDate;
                $data["pf"]["email_counts"][] = 0;
                $data["pf"]["call_counts"][] = 0;
                $data["pf"]["wa_counts"][] = 0;
            }

            $index = array_search($formattedDate, array_column($bayutLeads, "date"));
            if ($index !== false){
                $data["bayut"]["dates"][] = $formattedDate;
                $data["bayut"]["email_counts"][] = $bayutLeads[$index]["email_count"];
                $data["bayut"]["call_counts"][] = $bayutLeads[$index]["call_count"];
                $data["bayut"]["wa_counts"][] = $bayutLeads[$index]["wa_count"];
            }else{
                $data["bayut"]["dates"][] = $formattedDate;
                $data["bayut"]["email_counts"][] = 0;
                $data["bayut"]["call_counts"][] = 0;
                $data["bayut"]["wa_counts"][] = 0;
            }

            $index = array_search($formattedDate, array_column($dubizzleLeads, "date"));
            if ($index !== false){
                $data["dubizzle"]["dates"][] = $formattedDate;
                $data["dubizzle"]["email_counts"][] = $dubizzleLeads[$index]["email_count"];
                $data["dubizzle"]["call_counts"][] = $dubizzleLeads[$index]["call_count"];
                $data["dubizzle"]["wa_counts"][] = $dubizzleLeads[$index]["wa_count"];
            }else{
                $data["dubizzle"]["dates"][] = $formattedDate;
                $data["dubizzle"]["email_counts"][] = 0;
                $data["dubizzle"]["call_counts"][] = 0;
                $data["dubizzle"]["wa_counts"][] = 0;
            }
        }

        return $data;
    }

    private function prepareLeadOverviewData($groupIds = [], $date = "")
    {
        $agentIds = User::where("status", 1)
            ->whereHas("roles", function ($q) {
                $q->whereIn("name", ["Agent"]);
            })
            ->when(!empty($groupIds), fn($query) => $query->whereIn("users.zone_id", $groupIds))
            ->pluck("id");

        $query = Lead::query();
        $query = $this->applyDateFilter($query, "leads.created_at", $date);
        return $query->select(
            "mediums.name as medium_name",
            DB::raw("COUNT(leads.id) as count"),
        )
            ->leftJoin("mediums", "leads.lead_medium_id", "=", "mediums.id")
            ->whereIn("agent_id", $agentIds)
            ->groupBy("leads.lead_medium_id")
            ->get()->toArray();
    }

    private function prepareDateWiseLeadCountData($groupIds = [], $date = "")
    {
        $agentIds = User::where("status", 1)
            ->whereHas("roles", function ($q) {
                $q->whereIn("name", ["Agent"]);
            })
            ->when(!empty($groupIds), fn($query) => $query->whereIn("users.zone_id", $groupIds))
            ->pluck("id");

        $query = Lead::query();
        $query = $this->applyDateFilter($query, "leads.created_at", $date);

        $leads = $query->select(
            DB::raw("DATE(leads.created_at) as date"),
            DB::raw("COUNT(CASE WHEN mediums.name = 'Email' THEN leads.id END) AS email_count"),
            DB::raw("COUNT(CASE WHEN mediums.name = 'Call' THEN leads.id END) AS call_count"),
            DB::raw("COUNT(CASE WHEN mediums.name = 'WhatsApp' THEN leads.id END) AS wa_count")
        )
            ->whereIn("mediums.name", ["Email", "Call", "WhatsApp"])
            ->join("mediums", "leads.lead_medium_id", "=", "mediums.id")
            ->whereIn("agent_id", $agentIds)
            ->groupBy("date")
            ->orderBy("date")
            ->get()
            ->toArray();

        $dates = $this->getStartAndEndDate($date);
        $startDate = $dates["start_date"];
        $endDate = $dates["end_date"];
        $period = CarbonPeriod::create($startDate, $endDate);

        $data = [];
        foreach ($period as $date) {
            $formattedDate = $date->toDateString();
            $index = array_search($formattedDate, array_column($leads, "date"));
            if ($index !== false){
                $data["dates"][] = $formattedDate;
                $data["email_counts"][] = $leads[$index]["email_count"];
                $data["call_counts"][] = $leads[$index]["call_count"];
                $data["wa_counts"][] = $leads[$index]["wa_count"];
            }else{
                $data["dates"][] = $formattedDate;
                $data["email_counts"][] = 0;
                $data["call_counts"][] = 0;
                $data["wa_counts"][] = 0;
            }
        }

        return $data;
    }

    private function prepareCallOverviewData($groupIds = [], $date = "")
    {
        $agentIds = User::where("status", 1)
            ->whereHas("roles", function ($q) {
                $q->whereIn("name", ["Agent"]);
            })
            ->when(!empty($groupIds), fn($query) => $query->whereIn("users.zone_id", $groupIds))
            ->pluck("id");

        $query = CallLog::query();
        $query = $this->applyDateFilter($query, "created_at", $date);

        return $query->select(
            DB::raw("COUNT(id) AS total"),
            DB::raw("COUNT(CASE WHEN call_ended_at IS NOT NULL AND call_duration > 0 THEN id END) AS completed"),
            DB::raw("COUNT(CASE WHEN call_ended_at IS NULL AND (call_duration IS NULL OR call_duration <= 0) AND call_type = 'OUTGOING' THEN id END) AS no_answer"),
            DB::raw("COUNT(CASE WHEN call_ended_at IS NULL AND (call_duration IS NULL OR call_duration <= 0) AND call_type = 'INCOMING' THEN id END) AS missed"),
            DB::raw("ROUND(COUNT(CASE WHEN call_ended_at IS NOT NULL AND call_duration > 0 THEN id END) / COUNT(id) * 100, 2) AS response_rate")
        )
            ->whereIn("user_id", $agentIds)
            ->whereNotNull("lead_id")
            ->first();
    }

    private function prepareDateWiseCallCountData($groupIds = [], $date = "")
    {
        $agentIds = User::where("status", 1)
            ->whereHas("roles", function ($q) {
                $q->whereIn("name", ["Agent"]);
            })
            ->when(!empty($groupIds), fn($query) => $query->whereIn("users.zone_id", $groupIds))
            ->pluck("id");

        $query = CallLog::query();
        $query = $this->applyDateFilter($query, "call_logs.created_at", $date);

        $calls = $query->select(
            DB::raw("DATE(call_logs.created_at) AS date"),
            DB::raw("COUNT(CASE WHEN call_ended_at IS NOT NULL AND call_duration > 0 THEN call_logs.id END) AS completed"),
            DB::raw("COUNT(CASE WHEN call_ended_at IS NULL AND (call_duration IS NULL OR call_duration <= 0) AND call_type = 'OUTGOING' THEN call_logs.id END) AS no_answer"),
            DB::raw("COUNT(CASE WHEN call_ended_at IS NULL AND (call_duration IS NULL OR call_duration <= 0) AND call_type = 'INCOMING' THEN call_logs.id END) AS missed"),

            DB::raw("COUNT(CASE WHEN call_ended_at IS NOT NULL AND call_duration > 0 AND sources.name = 'Property Finder' THEN call_logs.id END) AS pf_completed"),
            DB::raw("COUNT(CASE WHEN call_ended_at IS NULL AND (call_duration IS NULL OR call_duration <= 0) AND call_type = 'OUTGOING' AND sources.name = 'Property Finder' THEN call_logs.id END) AS pf_no_answer"),
            DB::raw("COUNT(CASE WHEN call_ended_at IS NULL AND (call_duration IS NULL OR call_duration <= 0) AND call_type = 'INCOMING' AND sources.name = 'Property Finder' THEN call_logs.id END) AS pf_missed"),

            DB::raw("COUNT(CASE WHEN call_ended_at IS NOT NULL AND call_duration > 0 AND sources.name = 'Dubizzle' THEN call_logs.id END) AS dubizzle_completed"),
            DB::raw("COUNT(CASE WHEN call_ended_at IS NULL AND (call_duration IS NULL OR call_duration <= 0) AND call_type = 'OUTGOING' AND sources.name = 'Dubizzle' THEN call_logs.id END) AS dubizzle_no_answer"),
            DB::raw("COUNT(CASE WHEN call_ended_at IS NULL AND (call_duration IS NULL OR call_duration <= 0) AND call_type = 'INCOMING' AND sources.name = 'Dubizzle' THEN call_logs.id END) AS dubizzle_missed"),

            DB::raw("COUNT(CASE WHEN call_ended_at IS NOT NULL AND call_duration > 0 AND sources.name = 'Bayut' THEN call_logs.id END) AS bayut_completed"),
            DB::raw("COUNT(CASE WHEN call_ended_at IS NULL AND (call_duration IS NULL OR call_duration <= 0) AND call_type = 'OUTGOING' AND sources.name = 'Bayut' THEN call_logs.id END) AS bayut_no_answer"),
            DB::raw("COUNT(CASE WHEN call_ended_at IS NULL AND (call_duration IS NULL OR call_duration <= 0) AND call_type = 'INCOMING' AND sources.name = 'Bayut' THEN call_logs.id END) AS bayut_missed"),
        )
            ->join("leads", "leads.id", "call_logs.lead_id")
            ->join("sources", "leads.source_id", "=", "sources.id")
            ->whereIn("user_id", $agentIds)
            ->whereNotNull("lead_id")
            ->groupBy("date")
            ->orderBy("date")
            ->get()
            ->toArray();

        $dates = $this->getStartAndEndDate($date);
        $startDate = $dates["start_date"];
        $endDate = $dates["end_date"];
        $period = CarbonPeriod::create($startDate, $endDate);

        $data = [];
        foreach ($period as $date) {
            $formattedDate = $date->toDateString();
            $index = array_search($formattedDate, array_column($calls, "date"));
            if ($index !== false){
                $data["dates"][] = $formattedDate;
                $data["completed"][] = $calls[$index]["completed"];
                $data["no_answer"][] = $calls[$index]["no_answer"];
                $data["missed"][] = $calls[$index]["missed"];

                $data["pf_completed"][] = $calls[$index]["pf_completed"];
                $data["pf_no_answer"][] = $calls[$index]["pf_no_answer"];
                $data["pf_missed"][] = $calls[$index]["pf_missed"];

                $data["bayut_completed"][] = $calls[$index]["bayut_completed"];
                $data["bayut_no_answer"][] = $calls[$index]["bayut_no_answer"];
                $data["bayut_missed"][] = $calls[$index]["bayut_missed"];

                $data["dubizzle_completed"][] = $calls[$index]["dubizzle_completed"];
                $data["dubizzle_no_answer"][] = $calls[$index]["dubizzle_no_answer"];
                $data["dubizzle_missed"][] = $calls[$index]["dubizzle_missed"];
            }else{
                $data["dates"][] = $formattedDate;
                $data["completed"][] = 0;
                $data["no_answer"][] = 0;
                $data["missed"][] = 0;

                $data["pf_completed"][] = 0;
                $data["pf_no_answer"][] = 0;
                $data["pf_missed"][] = 0;

                $data["bayut_completed"][] = 0;
                $data["bayut_no_answer"][] = 0;
                $data["bayut_missed"][] = 0;

                $data["dubizzle_completed"][] = 0;
                $data["dubizzle_no_answer"][] = 0;
                $data["dubizzle_missed"][] = 0;
            }
        }

        return $data;
    }

    private function prepareDateWiseCallResponseRateData($groupIds = [], $date = "")
    {
        $agentIds = User::where("status", 1)
            ->whereHas("roles", function ($q) {
                $q->whereIn("name", ["Agent"]);
            })
            ->when(!empty($groupIds), fn($query) => $query->whereIn("users.zone_id", $groupIds))
            ->pluck("id");

        $query = CallLog::query();
        $query = $this->applyDateFilter($query, "call_logs.created_at", $date);

        $calls = $query->select(
            DB::raw("DATE(call_logs.created_at) AS date"),
            DB::raw("ROUND(COALESCE(COUNT(CASE WHEN call_ended_at IS NOT NULL AND call_duration > 0 THEN call_logs.id END) / COUNT(call_logs.id) * 100, 0), 2) AS response_rate"),
            DB::raw("ROUND(COALESCE(COUNT(CASE WHEN call_ended_at IS NOT NULL AND call_duration > 0 AND sources.name = 'Property Finder' THEN call_logs.id END) / COUNT(CASE WHEN sources.name = 'Property Finder' THEN call_logs.id END) * 100, 0), 2) AS pf_response_rate"),
            DB::raw("ROUND(COALESCE(COUNT(CASE WHEN call_ended_at IS NOT NULL AND call_duration > 0 AND sources.name = 'Bayut' THEN call_logs.id END) / COUNT(CASE WHEN sources.name = 'Bayut' THEN call_logs.id END) * 100, 0), 2) AS bayut_response_rate"),
            DB::raw("ROUND(COALESCE(COUNT(CASE WHEN call_ended_at IS NOT NULL AND call_duration > 0 AND sources.name = 'Dubizzle' THEN call_logs.id END) / COUNT(CASE WHEN sources.name = 'Dubizzle' THEN call_logs.id END) * 100, 0), 2) AS dubizzle_response_rate"),
        )
            ->join("leads", "leads.id", "call_logs.lead_id")
            ->join("sources", "leads.source_id", "=", "sources.id")
            ->whereNotNull("lead_id")
            ->whereIn("user_id", $agentIds)
            ->groupBy("date")
            ->orderBy("date")
            ->get()
            ->toArray();

        $dates = $this->getStartAndEndDate($date);
        $startDate = $dates["start_date"];
        $endDate = $dates["end_date"];
        $period = CarbonPeriod::create($startDate, $endDate);

        $data = [];
        foreach ($period as $date) {
            $formattedDate = $date->toDateString();
            $index = array_search($formattedDate, array_column($calls, "date"));
            if ($index !== false){
                $data["dates"][] = $formattedDate;
                $data["counts"][] = $calls[$index]["response_rate"];
                $data["pf_counts"][] = $calls[$index]["pf_response_rate"];
                $data["bayut_counts"][] = $calls[$index]["bayut_response_rate"];
                $data["dubizzle_counts"][] = $calls[$index]["dubizzle_response_rate"];
            }else{
                $data["dates"][] = $formattedDate;
                $data["counts"][] = 0.00;
                $data["pf_counts"][] = 0.00;
                $data["bayut_counts"][] = 0.00;
                $data["dubizzle_counts"][] = 0.00;
            }
        }

        return $data;
    }

    private function prepareWeekWiseCallCountData($groupIds = [], $date = "")
    {
        $agentIds = User::where("status", 1)
            ->whereHas("roles", function ($q) {
                $q->whereIn("name", ["Agent"]);
            })
            ->when(!empty($groupIds), fn($query) => $query->whereIn("users.zone_id", $groupIds))
            ->pluck("id");

        $query = CallLog::query();
        $query = $this->applyDateFilter($query, "call_logs.created_at", $date);

        $calls = $query->select(
            DB::raw("DAYOFWEEK(call_logs.created_at) AS weekday"),
            DB::raw("COUNT(CASE WHEN call_ended_at IS NOT NULL AND call_duration > 0 THEN call_logs.id END) AS completed"),
            DB::raw("COUNT(CASE WHEN call_ended_at IS NULL AND (call_duration IS NULL OR call_duration <= 0) AND call_type = 'OUTGOING' THEN call_logs.id END) AS no_answer"),
            DB::raw("COUNT(CASE WHEN call_ended_at IS NULL AND (call_duration IS NULL OR call_duration <= 0) AND call_type = 'INCOMING' THEN call_logs.id END) AS missed"),

            DB::raw("COUNT(CASE WHEN call_answered_at IS NOT NULL AND sources.name = 'Property Finder' THEN call_logs.id END) AS pf_completed"),
            DB::raw("COUNT(CASE WHEN call_ended_at IS NULL AND (call_duration IS NULL OR call_duration <= 0) AND call_type = 'OUTGOING' AND sources.name = 'Property Finder' THEN call_logs.id END) AS pf_no_answer"),
            DB::raw("COUNT(CASE WHEN call_ended_at IS NULL AND (call_duration IS NULL OR call_duration <= 0) AND call_type = 'INCOMING' AND sources.name = 'Property Finder' THEN call_logs.id END) AS pf_missed"),

            DB::raw("COUNT(CASE WHEN call_ended_at IS NOT NULL AND call_duration > 0 AND sources.name = 'Dubizzle' THEN call_logs.id END) AS dubizzle_completed"),
            DB::raw("COUNT(CASE WHEN call_ended_at IS NULL AND (call_duration IS NULL OR call_duration <= 0) AND call_type = 'OUTGOING' AND sources.name = 'Dubizzle' THEN call_logs.id END) AS dubizzle_no_answer"),
            DB::raw("COUNT(CASE WHEN call_ended_at IS NULL AND (call_duration IS NULL OR call_duration <= 0) AND call_type = 'INCOMING' AND sources.name = 'Dubizzle' THEN call_logs.id END) AS dubizzle_missed"),

            DB::raw("COUNT(CASE WHEN call_ended_at IS NOT NULL AND call_duration > 0 AND sources.name = 'Bayut' THEN call_logs.id END) AS bayut_completed"),
            DB::raw("COUNT(CASE WHEN call_ended_at IS NULL AND (call_duration IS NULL OR call_duration <= 0) AND call_type = 'OUTGOING' AND sources.name = 'Bayut' THEN call_logs.id END) AS bayut_no_answer"),
            DB::raw("COUNT(CASE WHEN call_ended_at IS NULL AND (call_duration IS NULL OR call_duration <= 0) AND call_type = 'INCOMING' AND sources.name = 'Bayut' THEN call_logs.id END) AS bayut_missed"),
        )
            ->join("leads", "leads.id", "call_logs.lead_id")
            ->join("sources", "leads.source_id", "=", "sources.id")
            ->whereNotNull("lead_id")
            ->whereIn("user_id", $agentIds)
            ->groupBy("weekday")
            ->orderBy("weekday")
            ->get()
            ->toArray();

        $data = [];
        for ($i = 1; $i <= 7; $i++) {
            $index = array_search($i, array_column($calls, "weekday"));
            $day = $this->getDayByNumber($i);
            if ($index !== false){
                $data["days"][] = $day;
                $data["weekdays"][] = $calls[$index]["weekday"];
                $data["completed"][] = $calls[$index]["completed"];
                $data["no_answer"][] = $calls[$index]["no_answer"];
                $data["missed"][] = $calls[$index]["missed"];

                $data["pf_completed"][] = $calls[$index]["pf_completed"];
                $data["pf_no_answer"][] = $calls[$index]["pf_no_answer"];
                $data["pf_missed"][] = $calls[$index]["pf_missed"];

                $data["bayut_completed"][] = $calls[$index]["bayut_completed"];
                $data["bayut_no_answer"][] = $calls[$index]["bayut_no_answer"];
                $data["bayut_missed"][] = $calls[$index]["bayut_missed"];

                $data["dubizzle_completed"][] = $calls[$index]["dubizzle_completed"];
                $data["dubizzle_no_answer"][] = $calls[$index]["dubizzle_no_answer"];
                $data["dubizzle_missed"][] = $calls[$index]["dubizzle_missed"];
            }else{
                $data["days"][] = $day;
                $data["weekdays"][] = $i;
                $data["completed"][] = 0;
                $data["no_answer"][] = 0;
                $data["missed"][] = 0;

                $data["pf_completed"][] = 0;
                $data["pf_no_answer"][] = 0;
                $data["pf_missed"][] = 0;

                $data["bayut_completed"][] = 0;
                $data["bayut_no_answer"][] = 0;
                $data["bayut_missed"][] = 0;

                $data["dubizzle_completed"][] = 0;
                $data["dubizzle_no_answer"][] = 0;
                $data["dubizzle_missed"][] = 0;
            }
        }
        return $data;
    }

    private function preparePerHourCallAverageData($groupIds = [], $date = "")
    {
        $agentIds = User::where("status", 1)
            ->whereHas("roles", function ($q) {
                $q->whereIn("name", ["Agent"]);
            })
            ->when(!empty($groupIds), fn($query) => $query->whereIn("users.zone_id", $groupIds))
            ->pluck("id");

        $query = CallLog::query();
        $query = $this->applyDateFilter($query, "call_logs.created_at", $date);

        $calls = $query->select(
            DB::raw("HOUR(call_logs.created_at) AS hour"),
            DB::raw("ROUND(COUNT(call_logs.id) / COUNT(DISTINCT DATE(call_logs.created_at)), 0) AS avg_calls"),
            DB::raw("ROUND(COUNT(CASE WHEN call_ended_at IS NOT NULL AND call_duration > 0 THEN call_logs.id END) / COUNT(DISTINCT DATE(call_logs.created_at)), 0) AS completed"),
            DB::raw("ROUND(COUNT(CASE WHEN call_ended_at IS NULL AND (call_duration IS NULL OR call_duration <= 0) AND call_type = 'INCOMING' THEN call_logs.id END) / COUNT(DISTINCT DATE(call_logs.created_at)), 0) AS no_answer"),
            DB::raw("ROUND(COUNT(CASE WHEN call_ended_at IS NULL AND (call_duration IS NULL OR call_duration <= 0) AND call_type = 'OUTGOING' THEN call_logs.id END) / COUNT(DISTINCT DATE(call_logs.created_at)), 0) AS missed"),

            DB::raw("ROUND(COALESCE(COUNT(CASE WHEN call_ended_at IS NOT NULL AND call_duration > 0 AND sources.name = 'Property Finder' THEN call_logs.id END) / COUNT(DISTINCT CASE WHEN sources.name = 'Property Finder' THEN DATE(call_logs.created_at) END), 0), 0) AS pf_completed"),
            DB::raw("ROUND(COALESCE(COUNT(CASE WHEN call_ended_at IS NULL AND (call_duration IS NULL OR call_duration <= 0) AND call_type = 'OUTGOING' AND sources.name = 'Property Finder' THEN call_logs.id END) / COUNT(DISTINCT CASE WHEN sources.name = 'Property Finder' THEN DATE(call_logs.created_at) END), 0), 0) AS pf_no_answer"),
            DB::raw("ROUND(COALESCE(COUNT(CASE WHEN call_ended_at IS NULL AND (call_duration IS NULL OR call_duration <= 0) AND call_type = 'INCOMING' AND sources.name = 'Property Finder' THEN call_logs.id END) / COUNT(DISTINCT CASE WHEN sources.name = 'Property Finder' THEN DATE(call_logs.created_at) END), 0), 0) AS pf_missed"),

            DB::raw("ROUND(COALESCE(COUNT(CASE WHEN call_ended_at IS NOT NULL AND call_duration > 0 AND sources.name = 'Bayut' THEN call_logs.id END) / COUNT(DISTINCT CASE WHEN sources.name = 'Bayut' THEN DATE(call_logs.created_at) END), 0), 0) AS bayut_completed"),
            DB::raw("ROUND(COALESCE(COUNT(CASE WHEN call_ended_at IS NULL AND (call_duration IS NULL OR call_duration <= 0) AND call_type = 'OUTGOING' AND sources.name = 'Bayut' THEN call_logs.id END) / COUNT(DISTINCT CASE WHEN sources.name = 'Bayut' THEN DATE(call_logs.created_at) END), 0), 0) AS bayut_no_answer"),
            DB::raw("ROUND(COALESCE(COUNT(CASE WHEN call_ended_at IS NULL AND (call_duration IS NULL OR call_duration <= 0) AND call_type = 'INCOMING' AND sources.name = 'Bayut' THEN call_logs.id END) / COUNT(DISTINCT CASE WHEN sources.name = 'Bayut' THEN DATE(call_logs.created_at) END), 0), 0) AS bayut_missed"),

            DB::raw("ROUND(COALESCE(COUNT(CASE WHEN call_ended_at IS NOT NULL AND call_duration > 0 AND sources.name = 'Dubizzle' THEN call_logs.id END) / COUNT(DISTINCT CASE WHEN sources.name = 'Dubizzle' THEN DATE(call_logs.created_at) END), 0), 0) AS dubizzle_completed"),
            DB::raw("ROUND(COALESCE(COUNT(CASE WHEN call_ended_at IS NULL AND (call_duration IS NULL OR call_duration <= 0) AND call_type = 'OUTGOING' AND sources.name = 'Dubizzle' THEN call_logs.id END) / COUNT(DISTINCT CASE WHEN sources.name = 'Dubizzle' THEN DATE(call_logs.created_at) END), 0), 0) AS dubizzle_no_answer"),
            DB::raw("ROUND(COALESCE(COUNT(CASE WHEN call_ended_at IS NULL AND (call_duration IS NULL OR call_duration <= 0) AND call_type = 'INCOMING' AND sources.name = 'Dubizzle' THEN call_logs.id END) / COUNT(DISTINCT CASE WHEN sources.name = 'Dubizzle' THEN DATE(call_logs.created_at) END), 0), 0) AS dubizzle_missed"),
        )
            ->join("leads", "leads.id", "call_logs.lead_id")
            ->join("sources", "leads.source_id", "=", "sources.id")
            ->whereNotNull("lead_id")
            ->whereIn("user_id", $agentIds)
            ->groupBy("hour")
            ->orderBy("hour")
            ->get()
            ->toArray();

        $data = [];
        for ($i = 0; $i <= 23; $i++) {
            $index = array_search($i, array_column($calls, "hour"));
            $time = $this->getTimeByNumber($i);
            if ($index !== false){
                $data["times"][] = $time;
                $data["hours"][] = $calls[$index]["hour"];
                $data["completed"][] = $calls[$index]["completed"];
                $data["no_answer"][] = $calls[$index]["no_answer"];
                $data["missed"][] = $calls[$index]["missed"];

                $data["pf_completed"][] = $calls[$index]["pf_completed"];
                $data["pf_no_answer"][] = $calls[$index]["pf_no_answer"];
                $data["pf_missed"][] = $calls[$index]["pf_missed"];

                $data["bayut_completed"][] = $calls[$index]["bayut_completed"];
                $data["bayut_no_answer"][] = $calls[$index]["bayut_no_answer"];
                $data["bayut_missed"][] = $calls[$index]["bayut_missed"];

                $data["dubizzle_completed"][] = $calls[$index]["dubizzle_completed"];
                $data["dubizzle_no_answer"][] = $calls[$index]["dubizzle_no_answer"];
                $data["dubizzle_missed"][] = $calls[$index]["dubizzle_missed"];
            }else{
                $data["times"][] = $time;
                $data["hours"][] = $i;
                $data["completed"][] = 0;
                $data["no_answer"][] = 0;
                $data["missed"][] = 0;

                $data["pf_completed"][] = 0;
                $data["pf_no_answer"][] = 0;
                $data["pf_missed"][] = 0;

                $data["bayut_completed"][] = 0;
                $data["bayut_no_answer"][] = 0;
                $data["bayut_missed"][] = 0;

                $data["dubizzle_completed"][] = 0;
                $data["dubizzle_no_answer"][] = 0;
                $data["dubizzle_missed"][] = 0;
            }
        }

        return $data;
    }

    private function prepareWhatsAppLeadOverviewData($groupIds = [], $date = "")
    {
        $agentIds = User::where("status", 1)
            ->whereHas("roles", function ($q) {
                $q->whereIn("name", ["Agent"]);
            })
            ->when(!empty($groupIds), fn($query) => $query->whereIn("users.zone_id", $groupIds))
            ->pluck("id");

        $query = Lead::query();
        $query = $this->applyDateFilter($query, "leads.created_at", $date);

        return $query->select(
            DB::raw("COUNT(leads.id) AS total"),
            DB::raw("ROUND(COUNT(CASE WHEN shortlinks.opted_in IS NOT NULL THEN leads.id END) / COUNT(leads.id) * 100, 2) AS response_rate"),
            DB::raw("ROUND(AVG(TIMESTAMPDIFF(SECOND, shortlinks.created_at, shortlinks.opted_in)) / 60, 2) as response_time"),
            DB::raw("COUNT(CASE WHEN shortlinks.opted_in IS NOT NULL THEN leads.id END) AS replied"),
            DB::raw("COUNT(CASE WHEN shortlinks.opted_in IS NULL THEN leads.id END) AS not_replied")
        )
            ->join("shortlinks", "shortlinks.lead_id", "=", "leads.id")
            ->whereIn("agent_id", $agentIds)
            ->first();
    }

    private function prepareDateWiseWhatsAppLeadCountData($groupIds = [], $date = "")
    {
        $agentIds = User::where("status", 1)
            ->whereHas("roles", function ($q) {
                $q->whereIn("name", ["Agent"]);
            })
            ->when(!empty($groupIds), fn($query) => $query->whereIn("users.zone_id", $groupIds))
            ->pluck("id");

        $query = Lead::query();
        $query = $this->applyDateFilter($query, "leads.created_at", $date);

        $leads = $query->select(
            DB::raw("DATE(leads.created_at) as date"),
            DB::raw("COUNT(leads.id) AS count"),
            DB::raw("COUNT(CASE WHEN sources.name = 'Property Finder' THEN leads.id END) AS pf_count"),
            DB::raw("COUNT(CASE WHEN sources.name = 'Bayut' THEN leads.id END) AS bayut_count"),
            DB::raw("COUNT(CASE WHEN sources.name = 'Dubizzle' THEN leads.id END) AS dubizzle_count"),
        )
            ->join("mediums", "leads.lead_medium_id",  "mediums.id")
            ->join("sources", "leads.source_id", "=", "sources.id")
            ->where("mediums.name", "WhatsApp")
            ->whereIn("agent_id", $agentIds)
            ->groupBy("date")
            ->orderBy("date")
            ->get()
            ->toArray();

        $dates = $this->getStartAndEndDate($date);
        $startDate = $dates["start_date"];
        $endDate = $dates["end_date"];
        $period = CarbonPeriod::create($startDate, $endDate);

        $data = [];
        foreach ($period as $date) {
            $formattedDate = $date->toDateString();
            $index = array_search($formattedDate, array_column($leads, "date"));
            if ($index !== false){
                $data["dates"][] = $formattedDate;
                $data["counts"][] = $leads[$index]["count"];
                $data["pf_counts"][] = $leads[$index]["pf_count"];
                $data["bayut_counts"][] = $leads[$index]["bayut_count"];
                $data["dubizzle_counts"][] = $leads[$index]["dubizzle_count"];
            }else{
                $data["dates"][] = $formattedDate;
                $data["counts"][] = 0;
                $data["pf_counts"][] =0;
                $data["bayut_counts"][] = 0;
                $data["dubizzle_counts"][] = 0;
            }
        }

        return $data;
    }

    private function prepareDateWiseWhatsAppLeadResponseRateData($groupIds = [], $date = "")
    {
        $agentIds = User::where("status", 1)
            ->whereHas("roles", function ($q) {
                $q->whereIn("name", ["Agent"]);
            })
            ->when(!empty($groupIds), fn($query) => $query->whereIn("users.zone_id", $groupIds))
            ->pluck("id");

        $query = Lead::query();
        $query = $this->applyDateFilter($query, "leads.created_at", $date);

        $leads = $query->select(
            DB::raw("DATE(leads.created_at) as date"),
            DB::raw("ROUND(COUNT(CASE WHEN shortlinks.opted_in IS NOT NULL THEN leads.id END) / COUNT(leads.id) * 100, 2) AS response_rate"),
            DB::raw("ROUND(COUNT(CASE WHEN shortlinks.opted_in IS NOT NULL AND sources.name = 'Property Finder' THEN leads.id END) / COUNT(CASE WHEN sources.name = 'Property Finder' THEN leads.id END) * 100, 2) AS pf_response_rate"),
            DB::raw("ROUND(COUNT(CASE WHEN shortlinks.opted_in IS NOT NULL AND sources.name = 'Bayut' THEN leads.id END) / COUNT(CASE WHEN sources.name = 'Bayut' THEN leads.id END) * 100, 2) AS bayut_response_rate"),
            DB::raw("ROUND(COUNT(CASE WHEN shortlinks.opted_in IS NOT NULL AND sources.name = 'Dubizzle' THEN leads.id END) / COUNT(CASE WHEN sources.name = 'Dubizzle' THEN leads.id END) * 100, 2) AS dubizzle_response_rate"),
        )
            ->join("shortlinks", "shortlinks.lead_id", "=", "leads.id")
            ->join("mediums", "leads.lead_medium_id",  "mediums.id")
            ->join("sources", "leads.source_id", "=", "sources.id")
            ->where("mediums.name", "WhatsApp")
            ->whereIn("agent_id", $agentIds)
            ->groupBy("date")
            ->orderBy("date")
            ->get()
            ->toArray();

        $dates = $this->getStartAndEndDate($date);
        $startDate = $dates["start_date"];
        $endDate = $dates["end_date"];
        $period = CarbonPeriod::create($startDate, $endDate);

        $data = [];
        foreach ($period as $date) {
            $formattedDate = $date->toDateString();
            $index = array_search($formattedDate, array_column($leads, "date"));
            if ($index !== false){
                $data["dates"][] = $formattedDate;
                $data["counts"][] = $leads[$index]["response_rate"];
                $data["pf_counts"][] = $leads[$index]["pf_response_rate"];
                $data["bayut_counts"][] = $leads[$index]["bayut_response_rate"];
                $data["dubizzle_counts"][] = $leads[$index]["dubizzle_response_rate"];
            }else{
                $data["dates"][] = $formattedDate;
                $data["counts"][] = 0;
                $data["pf_counts"][] = 0;
                $data["bayut_counts"][] = 0;
                $data["dubizzle_counts"][] = 0;
            }
        }

        return $data;
    }

    private function prepareDateWiseWhatsAppLeadResponseTimeData($groupIds = [], $date = "")
    {
        $agentIds = User::where("status", 1)
            ->whereHas("roles", function ($q) {
                $q->whereIn("name", ["Agent"]);
            })
            ->when(!empty($groupIds), fn($query) => $query->whereIn("users.zone_id", $groupIds))
            ->pluck("id");

        $query = Lead::query();
        $query = $this->applyDateFilter($query, "leads.created_at", $date);

        $leads = $query->select(
            DB::raw("DATE(leads.created_at) as date"),
            DB::raw("ROUND(AVG(TIMESTAMPDIFF(SECOND, shortlinks.created_at, shortlinks.opted_in)) / 60, 2) as response_time"),
            DB::raw("ROUND(AVG(CASE WHEN sources.name = 'Property Finder' THEN TIMESTAMPDIFF(SECOND, shortlinks.created_at, shortlinks.opted_in) ELSE NULL END) / 60, 2) as pf_response_time"),
            DB::raw("ROUND(AVG(CASE WHEN sources.name = 'Bayut' THEN TIMESTAMPDIFF(SECOND, shortlinks.created_at, shortlinks.opted_in) ELSE NULL END) / 60, 2) as bayut_response_time"),
            DB::raw("ROUND(AVG(CASE WHEN sources.name = 'Dubizzle' THEN TIMESTAMPDIFF(SECOND, shortlinks.created_at, shortlinks.opted_in) ELSE NULL END) / 60, 2) as dubizzle_response_time"),
        )
            ->join("shortlinks", "shortlinks.lead_id", "=", "leads.id")
            ->join("mediums", "leads.lead_medium_id",  "mediums.id")
            ->join("sources", "leads.source_id", "=", "sources.id")
            ->where("mediums.name", "WhatsApp")
            ->whereIn("agent_id", $agentIds)
            ->groupBy("date")
            ->orderBy("date")
            ->get()
            ->toArray();

        $dates = $this->getStartAndEndDate($date);
        $startDate = $dates["start_date"];
        $endDate = $dates["end_date"];
        $period = CarbonPeriod::create($startDate, $endDate);

        $data = [];
        foreach ($period as $date) {
            $formattedDate = $date->toDateString();
            $index = array_search($formattedDate, array_column($leads, "date"));
            if ($index !== false){
                $data["dates"][] = $formattedDate;
                $data["counts"][] = $leads[$index]["response_time"];
                $data["pf_counts"][] = $leads[$index]["pf_response_time"];
                $data["bayut_counts"][] = $leads[$index]["bayut_response_time"];
                $data["dubizzle_counts"][] = $leads[$index]["dubizzle_response_time"];
            }else{
                $data["dates"][] = $formattedDate;
                $data["counts"][] = 0;
                $data["pf_counts"][] = 0;
                $data["bayut_counts"][] = 0;
                $data["dubizzle_counts"][] = 0;
            }
        }

        return $data;
    }

    private function prepareWeekDayWiseWhatsAppLeadCountData($groupIds = [], $date = "")
    {
        $agentIds = User::where("status", 1)
            ->whereHas("roles", function ($q) {
                $q->whereIn("name", ["Agent"]);
            })
            ->when(!empty($groupIds), fn($query) => $query->whereIn("users.zone_id", $groupIds))
            ->pluck("id");

        $query = Lead::query();
        $query = $this->applyDateFilter($query, "leads.created_at", $date);

        $leads = $query->select(
            DB::raw("DAYOFWEEK(leads.created_at) AS weekday"),
            DB::raw("COUNT(leads.id) AS count"),
            DB::raw("COUNT(CASE WHEN sources.name = 'Property Finder' THEN leads.id END) AS pf_count"),
            DB::raw("COUNT(CASE WHEN sources.name = 'Bayut' THEN leads.id END) AS bayut_count"),
            DB::raw("COUNT(CASE WHEN sources.name = 'Dubizzle' THEN leads.id END) AS dubizzle_count"),
        )
            ->join("mediums", "leads.lead_medium_id",  "mediums.id")
            ->join("sources", "leads.source_id", "=", "sources.id")
            ->where("mediums.name", "WhatsApp")
            ->whereIn("agent_id", $agentIds)
            ->groupBy("weekday")
            ->orderBy("weekday")
            ->get()
            ->toArray();

        $data = [];

        for ($i = 1; $i <= 7; $i++) {
            $index = array_search($i, array_column($leads, "weekday"));
            $day = $this->getDayByNumber($i);
            if ($index !== false){
                $data["days"][] = $day;
                $data["weekdays"][] = $leads[$index]["weekday"];
                $data["counts"][] = $leads[$index]["count"];
                $data["pf_counts"][] = $leads[$index]["pf_count"];
                $data["bayut_counts"][] = $leads[$index]["bayut_count"];
                $data["dubizzle_counts"][] = $leads[$index]["dubizzle_count"];
            }else{
                $data["days"][] = $day;
                $data["weekdays"][] = $i;
                $data["counts"][] = 0;
                $data["pf_counts"][] = 0;
                $data["bayut_counts"][] = 0;
                $data["dubizzle_counts"][] = 0;
            }
        }
        return $data;
    }

    private function prepareHourWiseWhatsAppLeadCountData($groupIds = [], $date = "")
    {
        $agentIds = User::where("status", 1)
            ->whereHas("roles", function ($q) {
                $q->whereIn("name", ["Agent"]);
            })
            ->when(!empty($groupIds), fn($query) => $query->whereIn("users.zone_id", $groupIds))
            ->pluck("id");

        $query = Lead::query();
        $query = $this->applyDateFilter($query, "leads.created_at", $date);

        $leads = $query->select(
            DB::raw("HOUR(leads.created_at) AS hour"),
            DB::raw("ROUND(COUNT(leads.id) / COUNT(DISTINCT DATE(leads.created_at)), 0) AS count"),
            DB::raw("ROUND(COALESCE(COUNT(CASE WHEN sources.name = 'Property Finder' THEN leads.id END) / COUNT(DISTINCT CASE WHEN sources.name = 'Property Finder' THEN DATE(leads.created_at) END), 0), 0) AS pf_count"),
            DB::raw("ROUND(COALESCE(COUNT(CASE WHEN sources.name = 'Bayut' THEN leads.id END) / COUNT(DISTINCT CASE WHEN sources.name = 'Bayut' THEN DATE(leads.created_at) END), 0), 0) AS bayut_count"),
            DB::raw("ROUND(COALESCE(COUNT(CASE WHEN sources.name = 'Dubizzle' THEN leads.id END) / COUNT(DISTINCT CASE WHEN sources.name = 'Dubizzle' THEN DATE(leads.created_at) END), 0), 0) AS dubizzle_count"),
        )
            ->join("mediums", "leads.lead_medium_id",  "mediums.id")
            ->join("sources", "leads.source_id", "=", "sources.id")
            ->where("mediums.name", "WhatsApp")
            ->whereIn("agent_id", $agentIds)
            ->groupBy("hour")
            ->orderBy("hour")
            ->get()->toArray();

        $data = [];
        for ($i = 0; $i <= 23; $i++) {
            $index = array_search($i, array_column($leads, "hour"));
            $time = $this->getTimeByNumber($i);
            if ($index !== false){
                $data["times"][] = $time;
                $data["hours"][] = $leads[$index]["hour"];
                $data["counts"][] = $leads[$index]["count"];
                $data["pf_counts"][] = $leads[$index]["pf_count"];
                $data["bayut_counts"][] = $leads[$index]["bayut_count"];
                $data["dubizzle_counts"][] = $leads[$index]["dubizzle_count"];
            }else{
                $data["times"][] = $time;
                $data["hours"][] = $i;
                $data["counts"][] = 0;
                $data["pf_counts"][] = 0;
                $data["bayut_counts"][] = 0;
                $data["dubizzle_counts"][] = 0;
            }
        }

        return $data;
    }


    public function index(Request $request)
    {
        if (!auth()->user()->can('Lead Insights Reports')) {
            abort(403, 'Unauthorized action.');
        }

        $groups = Group::with(["user"])
            ->whereNull("parent_id")
            ->orderBy("order")
            ->get();

        $dates = $this->getStartAndEndDate($request->date, "d-m-Y");

        $startDate = $dates["start_date"];
        $endDate = $dates["end_date"];



        $sources = [
            [
                "name" => "Property Finder",
                "value" => "pf"
            ],
            [
                "name" => "Bayut",
                "value" => "bayut"
            ],
            [
                "name" => "Dubizzle",
                "value" => "dubizzle"
            ],
        ];

        $loggedInUser = auth()->user();

        return view("admin.reports.lead_insights.index", compact("groups", "startDate", "endDate", "sources", "loggedInUser"));
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

        if ($request->type == "leadInsights") {
            $insightData = [
                "overview_source_wise" => $this->prepareLeadOverviewSourceWiseData($zoneIds, $request->date),
                "date_wise_source_wise_counts" => $this->prepareSourceWiseDateWiseLeadCountData($zoneIds, $request->date),
                "overview" => $this->prepareLeadOverviewData($zoneIds, $request->date),
                "date_wise_counts" => $this->prepareDateWiseLeadCountData($zoneIds, $request->date),
            ];

            return view('admin.reports.lead_insights.partials._lead_insights', compact("insightData"));
        }

        if ($request->type == "callInsights") {

            $insightData = [
                "overview" => $this->prepareCallOverviewData($zoneIds, $request->date),
                "date_wise_counts" => $this->prepareDateWiseCallCountData($zoneIds, $request->date),
                "date_wise_response_rates" => $this->prepareDateWiseCallResponseRateData($zoneIds, $request->date),
                "week_day_wise_counts" => $this->prepareWeekWiseCallCountData($zoneIds, $request->date),
                "hour_wise_counts" => $this->preparePerHourCallAverageData($zoneIds, $request->date)
            ];

            $sources = [
                [
                    "name" => "Property Finder",
                    "value" => "pf"
                ],
                [
                    "name" => "Bayut",
                    "value" => "bayut"
                ],
                [
                    "name" => "Dubizzle",
                    "value" => "dubizzle"
                ],
            ];

            return view('admin.reports.lead_insights.partials._call_insights', compact("insightData", "sources"));
        }

        if ($request->type == "whatsAppInsights") {

            $insightData = [
                "overview" => $this->prepareWhatsAppLeadOverviewData($zoneIds, $request->date),
                "date_wise_counts" => $this->prepareDateWiseWhatsAppLeadCountData($zoneIds, $request->date),
                "date_wise_response_rates" => $this->prepareDateWiseWhatsAppLeadResponseRateData($zoneIds, $request->date),
                "date_wise_response_times" => $this->prepareDateWiseWhatsAppLeadResponseTimeData($zoneIds, $request->date),
                "week_day_wise_counts" => $this->prepareWeekDayWiseWhatsAppLeadCountData($zoneIds, $request->date),
                "hour_wise_counts" => $this->prepareHourWiseWhatsAppLeadCountData($zoneIds, $request->date),
            ];

            $sources = [
                [
                    "name" => "Property Finder",
                    "value" => "pf"
                ],
                [
                    "name" => "Bayut",
                    "value" => "bayut"
                ],
                [
                    "name" => "Dubizzle",
                    "value" => "dubizzle"
                ],
            ];

            return view('admin.reports.lead_insights.partials._whats_app_insights', compact("insightData", "sources"));
        }
        return null;
    }
}
