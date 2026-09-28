<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Portal;
use App\Models\Property;
use App\Models\User;
use App\Models\UserMeta;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class GroupReportV5Controller extends Controller
{


    private function applyDateDurationFilter($query, $columName, $operator)
    {
        // custom&from=11-28-2023&to=11-28-2023
        if (strpos($operator, "&") !== false) {
            $operator = explode("&", $operator);
            $startDate = null;
            $endDate = null;
            foreach ($operator as $item) {
                if (strpos($item, "from=") !== false) {
                    $startDate = Carbon::createFromFormat('m-d-Y', str_replace('from=', '', $item))->format('Y-m-d');
                } elseif (strpos($item, 'to=') !== false) {
                    $endDate = Carbon::createFromFormat('m-d-Y', str_replace('to=', '', $item))->format('Y-m-d');
                }
            }
            if ($startDate !== null && $endDate !== null) {
                return $query->whereBetween($columName, [$startDate, $endDate]);
            }
            return $query;
        }
        return match ($operator) {
            "today" => $query->whereDate($columName, Carbon::today()->format("Y-m-d")),
            "yesterday" => $query->whereDate($columName, Carbon::today()->subDay()->format("Y-m-d")),
            "last_month" => $query->whereBetween($columName, [Carbon::today()->subMonth()->startOfMonth()->format("Y-m-d"), Carbon::today()->subMonth()->endOfMonth()->format("Y-m-d")]),
            "last_week" => $query->whereBetween($columName, [Carbon::today()->subWeek()->startOfWeek()->format("Y-m-d"), Carbon::today()->subWeek()->endOfWeek()->format("Y-m-d")]),
            "this_month" => $query->whereBetween($columName, [Carbon::today()->startOfMonth()->format("Y-m-d"), Carbon::today()->endOfMonth()->format("Y-m-d")]),
            "this_week" => $query->whereBetween($columName, [Carbon::today()->startOfWeek()->format("Y-m-d"), Carbon::today()->endOfWeek()->format("Y-m-d")]),
            "last_7_days" => $query->whereBetween($columName, [Carbon::today()->subDays(7)->format("Y-m-d"), Carbon::today()->format("Y-m-d")]),
            "last_14_days" => $query->whereBetween($columName, [Carbon::today()->subDays(14)->format("Y-m-d"), Carbon::today()->format("Y-m-d")]),
            "last_21_days" => $query->whereBetween($columName, [Carbon::today()->subDays(21)->format("Y-m-d"), Carbon::today()->format("Y-m-d")]),
            "last_28_days" => $query->whereBetween($columName, [Carbon::today()->subDays(28)->format("Y-m-d"), Carbon::today()->format("Y-m-d")]),
            "last_30_days" => $query->whereBetween($columName, [Carbon::today()->subDays(30)->format("Y-m-d"), Carbon::today()->format("Y-m-d")]),
            "last_60_days" => $query->whereBetween($columName, [Carbon::today()->subDays(60)->format("Y-m-d"), Carbon::today()->format("Y-m-d")]),
            "last_90_days" => $query->whereBetween($columName, [Carbon::today()->subDays(90)->format("Y-m-d"), Carbon::today()->format("Y-m-d")]),
            "last_120_days" => $query->whereBetween($columName, [Carbon::today()->subDays(120)->format("Y-m-d"), Carbon::today()->format("Y-m-d")]),
            "last_180_days" => $query->whereBetween($columName, [Carbon::today()->subDays(180)->format("Y-m-d"), Carbon::today()->format("Y-m-d")]),
            "last_365_days" => $query->whereBetween($columName, [Carbon::today()->subDays(365)->format("Y-m-d"), Carbon::today()->format("Y-m-d")]),
            default => $query
        };
    }

    private function prepareEventStatistics($groupIds, $duration = "all")
    {
        $groups = Group::whereIn('id', $groupIds)->orderBy('order', 'asc')->get();

        $groupData = [
            "statuses" => [],
            "data" => null
        ];

        foreach ($groups as $group) {

            $agentIds = User::where("status", 1)
                ->whereHas("roles", function ($q) {
                    $q->whereIn("name", ["Agent", "Line Manager", "Team Leader", "Sales Director", "Head of Secondary"]);
                })
                ->where("users.zone_id", $group->id)
                ->pluck("id");

            $events = DB::table(DB::raw("(SELECT DISTINCT id AS status_id, name AS status FROM event_types) as status_table"))
                ->leftJoin("events", function ($join) use ($agentIds, $duration) {
                    $query = $join->on("status_table.status_id", "=", "events.event_type_id")
                        ->whereIn("agent_id", $agentIds);
                    $this->applyDateDurationFilter($query, "events.created_at", $duration);
                })
                ->select("status_table.status_id", "status_table.status", DB::raw("COALESCE(COUNT(events.id), 0) as count"))
                ->groupBy(["status_table.status_id", "status_table.status"])
                ->orderBy("status_table.status")
                ->get()
                ->toArray();

            $noOfUsers = count($agentIds);
            $counts = [];
            $averages = [];

            foreach ($events as $event) {
                $statusSlug = str_replace(" ", "_", $event->status_id . " " . $event->status);
                $counts[$statusSlug] = $event->count;
                $averages[$statusSlug] = round($noOfUsers > 0 ? ($event->count / $noOfUsers) : 0, 2);
            }

            $groupData["data"][$group->name] = [
                "id" => $group->id,
                "name" => $group->name,
                "color" => $group->color,
                "counts" => collect($events)->pluck("count")->all(),
                "statuses" => collect($events)->pluck("status")->all(),
                "status_ids" => collect($events)->pluck("status_id")->all(),
                "status_wise_counts" => $counts,
                "status_wise_averages" => $averages,
                "total" => collect($events)->sum("count")
            ];
        }

        $firstIndex = array_keys($groupData["data"] ?? [])[0] ?? false;

        $groupData["group_names"] = collect($groupData["data"])->pluck("name")->all();
        $groupData["group_ids"] = collect($groupData["data"])->pluck("id")->all();

        if ($firstIndex !== false) {
            $groupData["statuses"] = $groupData["data"][$firstIndex]["statuses"];
        }


        $totalAverageData = [];
        foreach ($groupData["data"] as $team) {
            foreach ($team["status_wise_averages"] as $statusSlug => $average) {
                if (!empty($totalAverageData[$statusSlug])) {
                    $totalAverageData[$statusSlug] += $average;
                } else {
                    $totalAverageData[$statusSlug] = $average;
                }
            }
        }

        $groupData["total_averages"] = $totalAverageData;

        return $groupData;
    }

    private function prepareDaysWithoutDaysStatistics($groupIds)
    {
        $groups = Group::whereIn('id', $groupIds)->orderBy('order', 'asc')->get();

        $groupData = [
            "data" => null
        ];

        foreach ($groups as $group) {

            $agentIds = User::where("status", 1)
                ->whereHas("roles", function ($q) {
                    $q->whereIn("name", ["Agent", "Line Manager", "Team Leader", "Sales Director", "Head of Secondary"]);
                })
                ->where("users.zone_id", $group->id)
                ->pluck("id");


            $users = User::whereIn("id", $agentIds)
                ->selectRaw("id, name, DATEDIFF(NOW(), last_deal_date) AS days_since_last_deal")
                ->whereNotNull("last_deal_date")
                ->get();

            $counts = [];

            foreach ($users as $user) {
                $counts[str_replace(" ", "_", $user->id . " " . $user->name)] = $user->days_since_last_deal;
            }

            $groupData["data"][$group->name] = [
                "id" => $group->id,
                "name" => $group->name,
                "color" => $group->color,
                "counts" => collect($users)->pluck("days_since_last_deal")->all(),
                "users" => collect($users)->pluck("name")->all(),
                "user_ids" => collect($users)->pluck("id")->all(),
                "status_wise_counts" => $counts,
                "total" => collect($users)->max("days_since_last_deal") ?? 0,
            ];
        }

        $groupData["group_names"] = collect($groupData["data"])->pluck("name")->all();
        $groupData["group_ids"] = collect($groupData["data"])->pluck("id")->all();

        return $groupData;
    }

    private function prepareListingStatistics($groupIds, $duration = "all")
    {
        $groups = Group::whereIn('id', $groupIds)->orderBy('order', 'asc')->get();

        $groupData = [
            "statuses" => [],
            "data" => null
        ];

        foreach ($groups as $group) {

            $agentIds = User::where("status", 1)
                ->whereHas("roles", function ($q) {
                    $q->whereIn("name", ["Agent", "Line Manager", "Team Leader", "Sales Director", "Head of Secondary"]);
                })
                ->where("users.zone_id", $group->id)
                ->pluck("id");

            $listings = DB::table(DB::raw("(SELECT DISTINCT publish_status FROM properties) as status_table"))
                ->leftJoin("properties", function ($join) use ($agentIds, $duration) {
                    $query = $join->on("status_table.publish_status", "=", "properties.publish_status")
                        ->whereNot("completion_status", "Off-Plan Primary")
                        ->whereIn("agent_id", $agentIds);
                    $this->applyDateDurationFilter($query, "properties.created_at", $duration);
                })
                ->select("status_table.publish_status", DB::raw("COALESCE(COUNT(properties.id), 0) as count"))
                ->groupBy("status_table.publish_status")
                ->orderByDesc("status_table.publish_status")
                ->get()
                ->toArray();


            $noOfUsers = count($agentIds);
            $counts = [];
            $averages = [];

            foreach ($listings as $listing) {
                $statusSlug = str_replace(" ", "_", $listing->publish_status);
                $counts[$statusSlug] = $listing->count;
                $averages[$statusSlug] = round($noOfUsers > 0 ? ($listing->count / $noOfUsers) : 0, 2);
            }

            $groupData["data"][$group->name] = [
                "id" => $group->id,
                "name" => $group->name,
                "color" => $group->color,
                "counts" => collect($listings)->pluck("count")->all(),
                "statuses" => collect($listings)->pluck("publish_status")->all(),
                "status_wise_counts" => $counts,
                "status_wise_averages" => $averages,
                "total" => collect($listings)->sum("count"),
            ];
        }

        $firstIndex = array_keys($groupData["data"] ?? [])[0] ?? false;

        $groupData["group_names"] = collect($groupData["data"])->pluck("name")->all();
        $groupData["group_ids"] = collect($groupData["data"])->pluck("id")->all();

        if ($firstIndex !== false) {
            $groupData["statuses"] = $groupData["data"][$firstIndex]["statuses"];
        }

        $totalAverageData = [];
        foreach ($groupData["data"] as $team) {
            foreach ($team["status_wise_averages"] as $statusSlug => $average) {
                if (!empty($totalAverageData[$statusSlug])) {
                    $totalAverageData[$statusSlug] += $average;
                } else {
                    $totalAverageData[$statusSlug] = $average;
                }
            }
        }

        $groupData["total_averages"] = $totalAverageData;

        return $groupData;
    }

    private function prepareLeadStatistics($groupIds, $duration = "all")
    {
        $groups = Group::whereIn('id', $groupIds)->orderBy('order', 'asc')->get();

        $groupData = [
            "statuses" => [],
            "data" => null
        ];

        foreach ($groups as $group) {

            $agentIds = User::where("status", 1)
                ->whereHas("roles", function ($q) {
                    $q->whereIn("name", ["Agent", "Line Manager", "Team Leader", "Sales Director", "Head of Secondary"]);
                })
                ->where("users.zone_id", $group->id)
                ->pluck("id");


            $leads = DB::table(DB::raw("(SELECT DISTINCT id AS status_id, name AS status FROM lead_statuses) as status_table"))
                ->leftJoin("leads", function ($join) use ($agentIds, $duration) {
                    $query = $join->on("status_table.status_id", "=", "leads.lead_status_id")
                        ->whereIn("agent_id", $agentIds);
                    $this->applyDateDurationFilter($query, "leads.created_at", $duration);
                })
                ->select("status_table.status_id", "status_table.status", DB::raw("COALESCE(COUNT(leads.id), 0) as count"))
                ->groupBy(["status_table.status_id", "status_table.status"])
                ->orderBy("status_table.status")
                ->get()
                ->toArray();

            $noOfUsers = count($agentIds);
            $counts = [];
            $averages = [];

            foreach ($leads as $lead) {
                $statusSlug = str_replace(" ", "_", $lead->status_id . " " . $lead->status);
                $counts[$statusSlug] = $lead->count;
                $averages[$statusSlug] = round($noOfUsers > 0 ? ($lead->count / $noOfUsers) : 0, 2);
            }

            $groupData["data"][$group->name] = [
                "id" => $group->id,
                "name" => $group->name,
                "color" => $group->color,
                "counts" => collect($leads)->pluck("count")->all(),
                "statuses" => collect($leads)->pluck("status")->all(),
                "status_ids" => collect($leads)->pluck("status_id")->all(),
                "status_wise_counts" => $counts,
                "status_wise_averages" => $averages,
                "total" => collect($leads)->sum("count"),
            ];
        }

        $firstIndex = array_keys($groupData["data"] ?? [])[0] ?? false;

        $groupData["group_names"] = collect($groupData["data"])->pluck("name")->all();
        $groupData["group_ids"] = collect($groupData["data"])->pluck("id")->all();

        if ($firstIndex !== false) {
            $groupData["statuses"] = $groupData["data"][$firstIndex]["statuses"];
        }

        $totalAverageData = [];
        foreach ($groupData["data"] as $team) {
            foreach ($team["status_wise_averages"] as $statusSlug => $average) {
                if (!empty($totalAverageData[$statusSlug])) {
                    $totalAverageData[$statusSlug] += $average;
                } else {
                    $totalAverageData[$statusSlug] = $average;
                }
            }
        }

        $groupData["total_averages"] = $totalAverageData;
        return $groupData;
    }

    private function prepareLeadSourceStatistics($groupIds, $duration = "all")
    {
        $groups = Group::whereIn('id', $groupIds)->orderBy('order', 'asc')->get();

        $groupData = [
            "statuses" => [],
            "data" => null
        ];

        foreach ($groups as $group) {

            $agentIds = User::where("status", 1)
                ->whereHas("roles", function ($q) {
                    $q->whereIn("name", ["Agent", "Line Manager", "Team Leader", "Sales Director", "Head of Secondary"]);
                })
                ->where("users.zone_id", $group->id)
                ->pluck("id");

            $leads = DB::table(DB::raw("(SELECT DISTINCT id AS status_id, name AS status FROM sources) as status_table"))
                ->leftJoin("leads", function ($join) use ($agentIds, $duration) {
                    $query = $join->on("status_table.status_id", "=", "leads.source_id")
                        ->whereIn("agent_id", $agentIds);
                    $this->applyDateDurationFilter($query, "leads.created_at", $duration);
                })
                ->select("status_table.status_id", "status_table.status", DB::raw("COALESCE(COUNT(leads.id), 0) as count"))
                ->groupBy(["status_table.status_id", "status_table.status"])
                ->orderBy("status_table.status")
                ->get()
                ->toArray();

            $noOfUsers = count($agentIds);
            $counts = [];
            $averages = [];

            foreach ($leads as $lead) {
                $statusSlug = str_replace(" ", "_", $lead->status_id . " " . $lead->status);
                $counts[$statusSlug] = $lead->count;
                $averages[$statusSlug] = round($noOfUsers > 0 ? ($lead->count / $noOfUsers) : 0, 2);
            }

            $groupData["data"][$group->name] = [
                "id" => $group->id,
                "name" => $group->name,
                "color" => $group->color,
                "counts" => collect($leads)->pluck("count")->all(),
                "statuses" => collect($leads)->pluck("status")->all(),
                "status_ids" => collect($leads)->pluck("status_id")->all(),
                "status_wise_counts" => $counts,
                "status_wise_averages" => $averages,
                "total" => collect($leads)->sum("count"),
            ];
        }

        $firstIndex = array_keys($groupData["data"] ?? [])[0] ?? false;

        $groupData["group_names"] = collect($groupData["data"])->pluck("name")->all();
        $groupData["group_ids"] = collect($groupData["data"])->pluck("id")->all();

        if ($firstIndex !== false) {
            $groupData["statuses"] = $groupData["data"][$firstIndex]["statuses"];
        }

        $totalAverageData = [];
        foreach ($groupData["data"] as $team) {
            foreach ($team["status_wise_averages"] as $statusSlug => $average) {
                if (!empty($totalAverageData[$statusSlug])) {
                    $totalAverageData[$statusSlug] += $average;
                } else {
                    $totalAverageData[$statusSlug] = $average;
                }
            }
        }

        $groupData["total_averages"] = $totalAverageData;

        return $groupData;
    }

    private function prepareListingLeadStatistics($groupIds, $duration = "all")
    {
        $groups = Group::whereIn('id', $groupIds)->orderBy('order', 'asc')->get();

        $groupData = [
            "statuses" => [],
            "data" => null
        ];

        foreach ($groups as $group) {

            $agentIds = User::where("status", 1)
                ->whereHas("roles", function ($q) {
                    $q->whereIn("name", ["Agent", "Line Manager", "Team Leader", "Sales Director", "Head of Secondary"]);
                })
                ->where("users.zone_id", $group->id)
                ->pluck("id");

            $leads = DB::table(DB::raw("(SELECT DISTINCT id AS status_id, name AS status FROM listing_lead_statuses) as status_table"))
                ->leftJoin("listing_leads", function ($join) use ($agentIds, $duration) {
                    $query = $join->on("status_table.status_id", "=", "listing_leads.status_id")
                        ->whereIn("agent_id", $agentIds);
                    $this->applyDateDurationFilter($query, "listing_leads.created_at", $duration);
                })
                ->select("status_table.status_id", "status_table.status", DB::raw("COALESCE(COUNT(listing_leads.id), 0) as count"))
                ->groupBy(["status_table.status_id", "status_table.status"])
                ->orderBy("status_table.status")
                ->get()
                ->toArray();

            $noOfUsers = count($agentIds);
            $counts = [];
            $averages = [];

            foreach ($leads as $lead) {
                $statusSlug = str_replace(" ", "_", $lead->status_id . " " . $lead->status);
                $counts[$statusSlug] = $lead->count;
                $averages[$statusSlug] = round($noOfUsers > 0 ? ($lead->count / $noOfUsers) : 0, 2);
            }

            $groupData["data"][$group->name] = [
                "id" => $group->id,
                "name" => $group->name,
                "color" => $group->color,
                "counts" => collect($leads)->pluck("count")->all(),
                "statuses" => collect($leads)->pluck("status")->all(),
                "status_ids" => collect($leads)->pluck("status_id")->all(),
                "status_wise_counts" => $counts,
                "status_wise_averages" => $averages,
                "total" => collect($leads)->sum("count"),
            ];
        }

        $firstIndex = array_keys($groupData["data"] ?? [])[0] ?? false;

        $groupData["group_names"] = collect($groupData["data"])->pluck("name")->all();
        $groupData["group_ids"] = collect($groupData["data"])->pluck("id")->all();

        if ($firstIndex !== false) {
            $groupData["statuses"] = $groupData["data"][$firstIndex]["statuses"];
        }

        $totalAverageData = [];
        foreach ($groupData["data"] as $team) {
            foreach ($team["status_wise_averages"] as $statusSlug => $average) {
                if (!empty($totalAverageData[$statusSlug])) {
                    $totalAverageData[$statusSlug] += $average;
                } else {
                    $totalAverageData[$statusSlug] = $average;
                }
            }
        }

        $groupData["total_averages"] = $totalAverageData;
        return $groupData;
    }

    private function prepareListingLeadSourceStatistics($groupIds, $duration = "all")
    {
        $groups = Group::whereIn('id', $groupIds)->orderBy('order', 'asc')->get();

        $groupData = [
            "statuses" => [],
            "data" => null
        ];

        foreach ($groups as $group) {

            $agentIds = User::where("status", 1)
                ->whereHas("roles", function ($q) {
                    $q->whereIn("name", ["Agent", "Line Manager", "Team Leader", "Sales Director", "Head of Secondary"]);
                })
                ->where("users.zone_id", $group->id)
                ->pluck("id");

            $leads = DB::table(DB::raw("(SELECT DISTINCT id AS status_id, name AS status FROM sources) as status_table"))
                ->leftJoin("listing_leads", function ($join) use ($agentIds, $duration) {
                    $query = $join->on("status_table.status_id", "=", "listing_leads.source_id")
                        ->whereIn("agent_id", $agentIds);
                    $this->applyDateDurationFilter($query, "listing_leads.created_at", $duration);
                })
                ->select("status_table.status_id", "status_table.status", DB::raw("COALESCE(COUNT(listing_leads.id), 0) as count"))
                ->groupBy(["status_table.status_id", "status_table.status"])
                ->orderBy("status_table.status")
                ->get()
                ->toArray();

            $noOfUsers = count($agentIds);
            $counts = [];
            $averages = [];

            foreach ($leads as $lead) {
                $statusSlug = str_replace(" ", "_", $lead->status_id . " " . $lead->status);
                $counts[$statusSlug] = $lead->count;
                $averages[$statusSlug] = round($noOfUsers > 0 ? ($lead->count / $noOfUsers) : 0, 2);
            }

            $groupData["data"][$group->name] = [
                "id" => $group->id,
                "name" => $group->name,
                "color" => $group->color,
                "counts" => collect($leads)->pluck("count")->all(),
                "statuses" => collect($leads)->pluck("status")->all(),
                "status_ids" => collect($leads)->pluck("status_id")->all(),
                "status_wise_counts" => $counts,
                "status_wise_averages" => $averages,
                "total" => collect($leads)->sum("count"),
            ];
        }

        $firstIndex = array_keys($groupData["data"] ?? [])[0] ?? false;

        $groupData["group_names"] = collect($groupData["data"])->pluck("name")->all();
        $groupData["group_ids"] = collect($groupData["data"])->pluck("id")->all();

        if ($firstIndex !== false) {
            $groupData["statuses"] = $groupData["data"][$firstIndex]["statuses"];
        }

        $totalAverageData = [];
        foreach ($groupData["data"] as $team) {
            foreach ($team["status_wise_averages"] ?? [] as $statusSlug => $average) {
                if (!empty($totalAverageData[$statusSlug])) {
                    $totalAverageData[$statusSlug] += $average;
                } else {
                    $totalAverageData[$statusSlug] = $average;
                }
            }
        }

        $groupData["total_averages"] = $totalAverageData;
        return $groupData;
    }

    private function prepareLocationStatistics($groupIds, $duration = "all")
    {
        $groups = Group::whereIn('id', $groupIds)->orderBy('order', 'asc')->get();

        $groupData = [
            "statuses" => [],
            "data" => null
        ];

        foreach ($groups as $group) {
            $agentIds = User::where("status", 1)
                ->whereHas("roles", function ($q) {
                    $q->whereIn("name", ["Agent", "Line Manager", "Team Leader", "Sales Director", "Head of Secondary"]);
                })
                ->where("users.zone_id", $group->id)
                ->pluck("id");

            $listings = DB::table(DB::raw("(SELECT DISTINCT id AS status_id, name AS status FROM communities) as status_table"))
                ->leftJoin("properties", function ($join) use ($agentIds, $duration) {
                    $query = $join->on("status_table.status_id", "=", "properties.community_id")
                        ->whereIn("agent_id", $agentIds);
                    $this->applyDateDurationFilter($query, "properties.created_at", $duration);
                })
                ->select("status_table.status_id", "status_table.status", DB::raw("COALESCE(COUNT(properties.id), 0) as count"))
                ->groupBy(["status_table.status_id", "status_table.status"])
                ->orderBy("status_table.status")
                ->get()
                ->toArray();

            $noOfUsers = count($agentIds);
            $counts = [];
            $averages = [];

            foreach ($listings as $listing) {
                $statusSlug = str_replace(" ", "_", $listing->status_id . " " . $listing->status);
                $counts[$statusSlug] = $listing->count;
                $averages[$statusSlug] = round($noOfUsers > 0 ? ($listing->count / $noOfUsers) : 0, 2);
            }

            $groupData["data"][$group->name] = [
                "id" => $group->id,
                "name" => $group->name,
                "color" => $group->color,
                "counts" => collect($listings)->pluck("count")->all(),
                "statuses" => collect($listings)->pluck("status")->all(),
                "status_ids" => collect($listings)->pluck("status_id")->all(),
                "status_wise_counts" => $counts,
                "status_wise_averages" => $averages,
                "total" => collect($listings)->sum("count"),
            ];
        }

        $firstIndex = array_keys($groupData["data"] ?? [])[0] ?? false;

        $groupData["group_names"] = collect($groupData["data"])->pluck("name")->all();
        $groupData["group_ids"] = collect($groupData["data"])->pluck("id")->all();

        if ($firstIndex !== false) {
            $groupData["statuses"] = $groupData["data"][$firstIndex]["statuses"];
        }

        $totalAverageData = [];
        foreach ($groupData["data"] as $team) {
            foreach ($team["status_wise_averages"] ?? [] as $statusSlug => $average) {
                if (!empty($totalAverageData[$statusSlug])) {
                    $totalAverageData[$statusSlug] += $average;
                } else {
                    $totalAverageData[$statusSlug] = $average;
                }
            }
        }

        $groupData["total_averages"] = $totalAverageData;
        return $groupData;
    }

    private function prepareDealStatistics($groupIds, $duration = "all")
    {
        $groups = Group::whereIn('id', $groupIds)->orderBy('order', 'asc')->get();

        $groupData = [
            "statuses" => [],
            "data" => null
        ];

        foreach ($groups as $group) {
            $agentIds = User::where("status", 1)
                ->whereHas("roles", function ($q) {
                    $q->whereIn("name", ["Agent", "Line Manager", "Team Leader", "Sales Director", "Head of Secondary"]);
                })
                ->where("users.zone_id", $group->id)
                ->pluck("id");

            $deals = DB::table(DB::raw("(SELECT DISTINCT status FROM deals) as status_table"))
                ->leftJoin("deals", function ($join) use ($agentIds, $duration) {
                    $query = $join->on("status_table.status", "=", "deals.status")
                        ->whereIn("agent_id", $agentIds);
                    $this->applyDateDurationFilter($query, "deals.created_at", $duration);
                })
                ->select("status_table.status", DB::raw("COALESCE(COUNT(deals.id), 0) as count"))
                ->groupBy("status_table.status")
                ->orderByDesc("status_table.status")
                ->get()
                ->toArray();


            $noOfUsers = count($agentIds);
            $counts = [];
            $averages = [];

            foreach ($deals as $deal) {
                $statusSlug = str_replace(" ", "_", $deal->status);
                $counts[$statusSlug] = $deal->count;
                $averages[$statusSlug] = round($noOfUsers > 0 ? ($deal->count / $noOfUsers) : 0, 2);
            }

            $groupData["data"][$group->name] = [
                "id" => $group->id,
                "name" => $group->name,
                "color" => $group->color,
                "counts" => collect($deals)->pluck("count")->all(),
                "statuses" => collect($deals)->pluck("status")->all(),
                "status_wise_counts" => $counts,
                "status_wise_averages" => $averages,
                "total" => collect($deals)->sum("count"),
            ];
        }

        $firstIndex = array_keys($groupData["data"] ?? [])[0] ?? false;

        $groupData["group_names"] = collect($groupData["data"])->pluck("name")->all();
        $groupData["group_ids"] = collect($groupData["data"])->pluck("id")->all();

        if ($firstIndex !== false) {
            $groupData["statuses"] = $groupData["data"][$firstIndex]["statuses"];
        }

        $totalAverageData = [];
        foreach ($groupData["data"] as $team) {
            foreach ($team["status_wise_averages"] ?? [] as $statusSlug => $average) {
                if (!empty($totalAverageData[$statusSlug])) {
                    $totalAverageData[$statusSlug] += $average;
                } else {
                    $totalAverageData[$statusSlug] = $average;
                }
            }
        }

        $groupData["total_averages"] = $totalAverageData;
        return $groupData;
    }

    private function prepareListingQualityStatistics($groupIds, $duration = "all")
    {
        $groups = Group::whereIn('id', $groupIds)->orderBy('order', 'asc')->get();

        $groupData = [
            "statuses" => ["Property Finder", "Bayut", "Dubizzle"],
            "data" => null
        ];

        foreach ($groups as $group) {

            $agentIds = User::where("status", 1)
                ->whereHas("roles", function ($q) {
                    $q->whereIn("name", ["Agent", "Line Manager", "Team Leader", "Sales Director", "Head of Secondary"]);
                })
                ->where("users.zone_id", $group->id)
                ->pluck("id");


            $selectQuery = "ROUND(AVG(CASE WHEN property_meta.meta_key = 'pf_rank' THEN property_meta.meta_value END), 2)  AS pf_rank_average,";
            $selectQuery .= "ROUND(AVG(CASE WHEN property_meta.meta_key = 'bayut_rank' THEN property_meta.meta_value END), 2) AS bayut_rank_average,";
            $selectQuery .= "ROUND(AVG(CASE WHEN property_meta.meta_key = 'dubizzle_rank' THEN property_meta.meta_value END), 2) AS dubizzle_rank_arverage";

            $query = Property::selectRaw($selectQuery)
                ->join("property_meta", function ($join) use ($agentIds) {
                    $join->on("property_meta.property_id", "=", "properties.id");
                })
                ->where("properties.is_live", true)
                ->whereIn("agent_id", $agentIds);

            $query = $this->applyDateDurationFilter($query, "properties.created_at", $duration);

            $rank = $query->first();

            $counts = [
                str_replace(" ", "_", "Property Finder") => $rank->pf_rank_average ?? 0,
                str_replace(" ", "_", "Bayut") => $rank->bayut_rank_average ?? 0,
                str_replace(" ", "_", "Dubizzle") => $rank->dubizzle_rank_arverage ?? 0,
            ];

            $groupData["data"][$group->name] = [
                "id" => $group->id,
                "name" => $group->name,
                "color" => $group->color,
                "counts" => [$rank->pf_rank_average ?? 0, $rank->bayut_rank_average ?? 0, $rank->dubizzle_rank_arverage ?? 0],
                "statuses" => $groupData["statuses"],
                "status_wise_counts" => $counts,
            ];
        }

        $groupData["group_names"] = collect($groupData["data"])->pluck("name")->all();
        $groupData["group_ids"] = collect($groupData["data"])->pluck("id")->all();

        return $groupData;
    }

    private function prepareListingLevelStatistics($groupIds, $duration = "all")
    {

        $groups = Group::whereIn('id', $groupIds)->orderBy('order', 'asc')->get();

        $portals = Portal::whereIn("name", ["Property Finder", "bayut", "Dubizzle"])->get(["id", "name"]);

        foreach ($portals as $portal) {

            $groupData["data"][$portal->id] = [
                "id" => $portal->id,
                "name" => $portal->name,
                "unique_key" => str_replace(" ", "-", $portal->name),
                "groups" => []
            ];

            foreach ($groups as $group) {

                $agentIds = User::where("status", 1)
                    ->whereHas("roles", function ($q) {
                        $q->whereIn("name", ["Agent", "Line Manager", "Team Leader", "Sales Director", "Head of Secondary"]);
                    })
                    ->where("users.zone_id", $group->id)
                    ->pluck("id");


                $metaKey = "";
                if ($portal->name == "Property Finder") {
                    $metaKey = "pf_level";
                }

                if ($portal->name == "Bayut") {
                    $metaKey = "bayut_level";
                }

                if ($portal->name == "Dubizzle") {
                    $metaKey = "dubizzle_level";
                }

                $levels = DB::table(DB::raw("(SELECT DISTINCT meta_value AS level FROM property_meta WHERE meta_key = '{$metaKey}' AND meta_value != '') AS level_table"))
                    ->leftJoin("property_meta", function ($join) use ($metaKey) {
                        $join->on("level_table.level", "=", "property_meta.meta_value")
                            ->where("property_meta.meta_key", $metaKey);
                    })
                    ->leftJoin("properties", function ($join) use ($agentIds, $duration) {
                        $query = $join->on("property_meta.property_id", "=", "properties.id")
                            ->where("properties.is_live", true)
                            ->whereIn("agent_id", $agentIds);
                        $this->applyDateDurationFilter($query, "properties.created_at", $duration);
                    })
                    ->select("level_table.level AS level", DB::raw("COUNT(DISTINCT properties.id) AS count"))
                    ->groupBy("level_table.level")
                    ->get();

                $groupData["data"][$portal->id]["groups"][$group->id] = [
                    "id" => $group->id,
                    "name" => $group->name,
                    "color" => $group->color,
                    "counts" => collect($levels)->pluck("count")->all(),
                    "statuses" => collect($levels)->pluck("level")->all(),
                    "total" => collect($levels)->sum("count"),
                ];
            }
        }


        return $groupData;
    }

    private function prepareListingVerifiedStatistics($groupIds, $duration = "all")
    {

        $groups = Group::whereIn('id', $groupIds)->orderBy('order', 'asc')->get();

        $portals = Portal::whereIn("name", ["Property Finder", "bayut"])->get(["id", "name"]);

        foreach ($portals as $portal) {

            $groupData["data"][$portal->id] = [
                "id" => $portal->id,
                "name" => $portal->name,
                "unique_key" => str_replace(" ", "-", $portal->name),
                "groups" => []
            ];

            foreach ($groups as $group) {
                $agentIds = User::where("status", 1)
                    ->whereHas("roles", function ($q) {
                        $q->whereIn("name", ["Agent", "Line Manager", "Team Leader", "Sales Director", "Head of Secondary"]);
                    })
                    ->where("users.zone_id", $group->id)
                    ->pluck("id");


                $types = [];
                if ($portal->name == "Property Finder") {
                    $types = [
                        [
                            "name" => "Verified",
                            "key" => "pf_verified",
                            "value" => "1",
                            "count" => 0,
                        ]
                    ];
                }

                if ($portal->name == "Bayut") {
                    $types = [
                        [
                            "name" => "Verified",
                            "key" => "bayut_review_status",
                            "value" => "Approved",
                            "count" => 0,
                        ],
                        [
                            "name" => "Trucheck",
                            "key" => "bayut_trucheck_status",
                            "value" => "Truchecked",
                            "count" => 0,
                        ],
                        [
                            "name" => "Checked",
                            "key" => "bayut_trucheck_status",
                            "value" => "checked",
                            "count" => 0,
                        ]
                    ];
                }

                foreach ($types as $index =>  $type) {
                    $count = DB::table("property_meta")
                        ->join("properties as p", "p.id", "=", "property_meta.property_id")
                        ->where("meta_key", $type["key"])
                        ->where("meta_value", $type["value"])
                        ->distinct("property_meta.property_id")
                        ->join("properties", function ($join) use ($agentIds, $duration) {
                        $query = $join->on("property_meta.property_id", "=", "properties.id")
                            ->where("properties.is_live", true)
                            ->whereIn("properties.agent_id", $agentIds);
                        $this->applyDateDurationFilter($query, "properties.created_at", $duration);
                    })->count("property_meta.property_id");

                    $types[$index]["count"] = $count;
                }

                $groupData["data"][$portal->id]["groups"][$group->id] = [
                    "id" => $group->id,
                    "name" => $group->name,
                    "color" => $group->color,
                    "counts" => collect($types)->pluck("count")->all(),
                    "statuses" => collect($types)->pluck("name")->all(),
                    "total" => collect($types)->sum("count"),
                ];
            }
        }
        return $groupData;
    }

    private function prepareUserMetaStatistics($groupIds, $duration = "all")
    {
        $groups = Group::whereIn('id', $groupIds)->orderBy('order', 'asc')->get();

        $groupData = [
            "statuses" => [],
            "data" => null
        ];

        foreach ($groups as $group) {
            $agentIds = User::where("status", 1)
                ->whereHas("roles", function ($q) {
                    $q->whereIn("name", ["Agent", "Line Manager", "Team Leader", "Sales Director", "Head of Secondary"]);
                })
                ->where("users.zone_id", $group->id)
                ->pluck("id");

            $selectQuery = "COUNT(CASE WHEN tag = 'lead_inactivity_warning' THEN 1 ELSE NULL END) AS warnings,";
            $selectQuery .= "COUNT(CASE WHEN tag = 'lead_inactivity_deactivate' THEN 1 ELSE NULL END) AS suspensions";

            $query = UserMeta::selectRaw($selectQuery)->whereIn("user_id", $agentIds);

            $query = $this->applyDateDurationFilter($query, "created_at", $duration);

            $count = $query->first();

            $noOfUsers = count($agentIds);

            $counts = [
                "Warnings" => $count->warnings ?? 0,
                "Suspensions" => $count->suspensions ?? 0
            ];

            $averages = [
                "Warnings" => round($noOfUsers > 0 ? (($count->warnings ?? 0) / $noOfUsers) : 0, 2),
                "Suspensions" => round($noOfUsers > 0 ? (($count->suspensions ?? 0) / $noOfUsers) : 0, 2)
            ];

            $groupData["data"][$group->name] = [
                "id" => $group->id,
                "name" => $group->name,
                "color" => $group->color,
                "counts" => [$count->warnings ?? 0, $count->suspensions ?? 0],
                "statuses" => ["Warnings", "Suspensions"],
                "status_wise_counts" => $counts,
                "status_wise_averages" => $averages,
                "total" => ($count->warnings ?? 0) + ($count->suspensions ?? 0),
            ];
        }

        $firstIndex = array_keys($groupData["data"] ?? [])[0] ?? false;

        $groupData["group_names"] = collect($groupData["data"])->pluck("name")->all();
        $groupData["group_ids"] = collect($groupData["data"])->pluck("id")->all();

        if ($firstIndex !== false) {
            $groupData["statuses"] = $groupData["data"][$firstIndex]["statuses"];
        }

        $totalAverageData = [];
        foreach ($groupData["data"] as $team) {
            foreach ($team["status_wise_averages"] ?? [] as $statusSlug => $average) {
                if (!empty($totalAverageData[$statusSlug])) {
                    $totalAverageData[$statusSlug] += $average;
                } else {
                    $totalAverageData[$statusSlug] = $average;
                }
            }
        }

        $groupData["total_averages"] = $totalAverageData;
        return $groupData;
    }

    private function prepareCallLogStatistics($groupIds, $duration = "all")
    {
        $groups = Group::whereIn('id', $groupIds)->orderBy('order', 'asc')->get();

        $groupData = [
            "statuses" => [],
            "data" => null
        ];

        foreach ($groups as $group) {

            $agentIds = User::where("status", 1)
                ->whereHas("roles", function ($q) {
                    $q->whereIn("name", ["Agent", "Line Manager", "Team Leader", "Sales Director", "Head of Secondary"]);
                })
                ->where("users.zone_id", $group->id)
                ->pluck("id");

            $calls = DB::table(DB::raw("(SELECT DISTINCT call_type AS status FROM call_logs) as status_table"))
                ->leftJoin("call_logs", function ($join) use ($agentIds, $duration) {
                    $query = $join->on("status_table.status", "=", "call_logs.call_type")
                        ->whereIn("user_id", $agentIds);
                    $this->applyDateDurationFilter($query, "call_logs.created_at", $duration);
                })
                ->select("status_table.status", DB::raw("COALESCE(COUNT(call_logs.id), 0) as count"))
                ->groupBy(["status_table.status"])
                ->orderBy("status_table.status")
                ->get()
                ->toArray();

            $noOfUsers = count($agentIds);
            $counts = [];
            $averages = [];

            foreach ($calls as $call) {
                $statusSlug = str_replace(" ", "_", $call->status);
                $counts[$statusSlug] = $call->count;
                $averages[$statusSlug] = round($noOfUsers > 0 ? ($call->count / $noOfUsers) : 0, 2);
            }

            $groupData["data"][$group->name] = [
                "id" => $group->id,
                "name" => $group->name,
                "color" => $group->color,
                "counts" => collect($calls)->pluck("count")->all(),
                "statuses" => collect($calls)->pluck("status")->all(),
                "status_wise_counts" => $counts,
                "status_wise_averages" => $averages,
                "total" => collect($calls)->sum("count"),
            ];
        }

        $firstIndex = array_keys($groupData["data"] ?? [])[0] ?? false;

        $groupData["group_names"] = collect($groupData["data"])->pluck("name")->all();
        $groupData["group_ids"] = collect($groupData["data"])->pluck("id")->all();

        if ($firstIndex !== false) {
            $groupData["statuses"] = $groupData["data"][$firstIndex]["statuses"];
        }

        $totalAverageData = [];
        foreach ($groupData["data"] as $team) {
            foreach ($team["status_wise_averages"] ?? [] as $statusSlug => $average) {
                if (!empty($totalAverageData[$statusSlug])) {
                    $totalAverageData[$statusSlug] += $average;
                } else {
                    $totalAverageData[$statusSlug] = $average;
                }
            }
        }

        $groupData["total_averages"] = $totalAverageData;
        return $groupData;
    }

    public function index(Request $request)
    {
        if (!auth()->user()->can('Group Reports')) {
            abort(403, 'Unauthorized action.');
        }

        $groups = Group::with(["user"])
            ->whereNull("parent_id")
            ->orderBy("order")
            ->get();

        $loggedInUser = auth()->user();

        return view("admin.reports.v5.group", compact("groups", "loggedInUser"));
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

        if ($request->type == "eventStatistics") {
            $groupData = $this->prepareEventStatistics($zoneIds, $request->duration);
            return view('admin.reports.v5.partials._event_statistics')
                ->with('groupData', $groupData);
        }

        if ($request->type == "daysWithoutDeals") {
            $groupData = $this->prepareDaysWithoutDaysStatistics($zoneIds);
            return view('admin.reports.v5.partials._days_without_deals')
                ->with('groupData', $groupData);
        }

        if ($request->type == "listingStatistics") {
            $groupData = $this->prepareListingStatistics($zoneIds, $request->duration);
            return view('admin.reports.v5.partials._listing_statistics')
                ->with('groupData', $groupData);
        }

        if ($request->type == "leadStatistics") {
            $groupData = $this->prepareLeadStatistics($zoneIds, $request->duration);
            return view('admin.reports.v5.partials._lead_statistics')
                ->with('groupData', $groupData);
        }

        if ($request->type == "leadSourceStatistics") {
            $groupData = $this->prepareLeadSourceStatistics($zoneIds, $request->duration);
            return view('admin.reports.v5.partials._lead_source_statistics')
                ->with('groupData', $groupData);
        }

        if ($request->type == "listingLeadStatistics") {
            $groupData = $this->prepareListingLeadStatistics($zoneIds, $request->duration);
            return view('admin.reports.v5.partials._listing_lead_statistics')
                ->with('groupData', $groupData);
        }

        if ($request->type == "listingLeadSourceStatistics") {
            $groupData = $this->prepareListingLeadSourceStatistics($zoneIds, $request->duration);
            return view('admin.reports.v5.partials._listing_lead_source_statistics')
                ->with('groupData', $groupData);
        }

        if ($request->type == "locationStatistics") {
            $groupData = $this->prepareLocationStatistics($zoneIds, $request->duration);
            return view('admin.reports.v5.partials._location_statistics')
                ->with('groupData', $groupData);
        }

        if ($request->type == "listingQualityStatistics") {
            $groupData = $this->prepareListingQualityStatistics($zoneIds, $request->duration);
            return view('admin.reports.v5.partials._listing_quality_statistics')
                ->with('groupData', $groupData);
        }

        if ($request->type == "listingLevelStatistics") {
            $groupData = $this->prepareListingLevelStatistics($zoneIds, $request->duration);
            return view('admin.reports.v5.partials._listing_level_statistics')
                ->with('groupData', $groupData);
        }

        if ($request->type == "listingVerifiedStatistics") {
            $groupData = $this->prepareListingVerifiedStatistics($zoneIds, $request->duration);
            return view('admin.reports.v5.partials._listing_verified_statistics')
                ->with('groupData', $groupData);
        }

        if ($request->type == "dealStatistics") {
            $groupData = $this->prepareDealStatistics($zoneIds, $request->duration);
            return view('admin.reports.v5.partials._deal_statistics')
                ->with('groupData', $groupData);
        }

        if ($request->type == "userMetaStatistics") {
            $groupData = $this->prepareUserMetaStatistics($zoneIds, $request->duration);
            return view('admin.reports.v5.partials._user_meta_statistics')
                ->with('groupData', $groupData);
        }

        if ($request->type == "callLogStatistics") {
            $groupData = $this->prepareCallLogStatistics($zoneIds, $request->duration);
            return view('admin.reports.v5.partials._call_log_statistics')
                ->with('groupData', $groupData);
        }

        return null;
    }
}
