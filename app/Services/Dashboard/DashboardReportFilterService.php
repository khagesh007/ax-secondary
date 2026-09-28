<?php

namespace App\Services\Dashboard;




use App\Models\Portal;
use App\Models\Property;
use App\Models\User;
use Carbon\Carbon;

class DashboardReportFilterService
{
    private function getStartAndEndDate($date = null, $format = "Y-m-d"): array
    {
        $startDate = null;
        $endDate   = null;
        if (strpos($date, " - ") !== false) {
            $dates     = explode(" - ", $date);
            $startDate = $dates[0] ?? null;
            $endDate   = $dates[1] ?? null;
        }

        if ($startDate !== null && $endDate !== null) {
            $startDate = \Illuminate\Support\Carbon::createFromFormat("d/m/Y", $startDate)->format($format);
            $endDate   = Carbon::createFromFormat("d/m/Y", $endDate)->format($format);
        } else {
            $startDate = Carbon::now()->subDays(90)->format($format);
            $endDate   = Carbon::now()->format($format);
        }

        return [
            "start_date" => $startDate,
            "end_date"   => $endDate,
        ];
    }

    public function dateFilter($query, $columnName, $start_date, $end_date)
    {
        $startDate = Carbon::parse($start_date)->format("Y-m-d");
        $endDate   = Carbon::parse($end_date)->format("Y-m-d");
        return $query->whereDate($columnName, ">=", $startDate)->whereDate($columnName, "<=", $endDate);
    }


    public function applyUserDashboardFilter($query, $request, $joinTable = "",$params): void
    {
        $zoneId    = $params['group_id'] ?? null;
       // $query->when(!$params['show_inactive_users'], fn($q) => $q->where("users.status", true));
        $zoneId    = null;
        $dashboard = '';
        if ($dashboard === "abu-dhabi") {
            $abuDbahiZoneId = Group::where("name", "Abu Dhabi")->first()?->id ?? null;
        }
        match ($dashboard) {
//            "primary" => $query->where(function ($q) {
//                $q->where("users.bitrix_role", "LIKE", "%Primary%")
//                    ->where("users.bitrix_role", "not like", "%Secondary%")
//                    ->where("users.bitrix_role", "not like", "%Abu-Dhabi Team%")
//                    ->where("users.bitrix_role", "not like", "%Primary Admin%");
//            }),
//            "secondary" => $query->when($zoneId != null, fn($q) => $q->where("users.zone_id", $zoneId))
//                ->where(function ($q) {
//                    $q->where("users.bitrix_role", "LIKE", "%Secondary%")
//                        ->where("users.bitrix_role", "not like", "%Secondary Admin%")
//                        ->where("users.bitrix_role", "not like", "%Primary%");
//                }),
//            "abu-dhabi" => $query->where("users.region_id", 2)->where("users.zone_id", $abuDbahiZoneId)->when(! empty($joinTable), fn($q) => $q->where($joinTable . ".region_id", 2)),
            "chinese" => $query->where("users.bitrix_role", "LIKE", "%Chinese%"),
            default => $query
        };
    }

    public function getExcludedUsers(): array
    {
        $excluded_users = [
            '1165','39','414',
          '221','1690','9','263','1081','1215','1047','1692','1729','1443','1444','1445','1446','1447','1434','1402','1119','1084'
        ];
      return $excluded_users;
    }

}
