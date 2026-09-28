<?php

namespace App\Http\Controllers\Admin\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\CallLog;
use App\Models\Community;
use App\Models\CommunityProperty;
use App\Models\Deal;
use App\Models\Event;
use App\Models\Group;
use App\Models\Lead;
use App\Models\ListingLead;
use App\Models\Portal;
use App\Models\Property;
use App\Models\PropertyMeta;
use App\Models\Source;
use App\Models\SubCommunity;
use App\Models\User;
use App\Models\UserLocation;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DB;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardV2Controller extends Controller
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

    private function applyDateFilter($query, $columnName, $date)
    {
        $dates     = $this->getStartAndEndDate($date);
        $startDate = $dates["start_date"];
        $endDate   = $dates["end_date"];

        return $query->whereDate($columnName, ">=", $startDate)->whereDate($columnName, "<=", $endDate);
    }

    private function checkIfSecondaryDashboard($dashboard): bool
    {
        return str_contains($dashboard, "secondary");
    }

    private function getSecondaryDashboardZoneId($dashboard): string | null
    {
        return Group::where("name", str_replace("secondary-", "", $dashboard))->first()?->id ?? null;
    }

    private function applyUserDashboardFilter($query, $request, $joinTable = ""): void
    {
        $query->when(! $request->boolean("inactiveUsers"), fn($q) => $q->where("users.status", true));
        $zoneId    = $this->getSecondaryDashboardZoneId($request->dashboard);
        $dashboard = $this->checkIfSecondaryDashboard($request->dashboard) ? "secondary" : $request->dashboard;
        if ($dashboard === "abu-dhabi") {
            $abuDbahiZoneId = Group::where("name", "Abu Dhabi")->first()?->id ?? null;
        }
        match ($dashboard) {
            "primary" => $query->where(function ($q) {
                $q->where("users.bitrix_role", "LIKE", "%Primary%")
                    ->where("users.bitrix_role", "not like", "%Secondary%")
                    ->where("users.bitrix_role", "not like", "%Abu-Dhabi Team%")
                    ->where("users.bitrix_role", "not like", "%Primary Admin%");
            }),
            "secondary" => $query->when($zoneId != null, fn($q) => $q->where("users.zone_id", $zoneId))
                ->where(function ($q) {
                    $q->where("users.bitrix_role", "LIKE", "%Secondary%")
                        ->where("users.bitrix_role", "not like", "%Secondary Admin%")
                        ->where("users.bitrix_role", "not like", "%Primary%");
                }),
            "abu-dhabi" => $query->where("users.region_id", 2)->where("users.zone_id", $abuDbahiZoneId)->when(! empty($joinTable), fn($q) => $q->where($joinTable . ".region_id", 2)),
            "chinese" => $query->where("users.bitrix_role", "LIKE", "%Chinese%"),
            default => $query
        };
    }

    private function prepareListingsData(Request $request, $date = null)
    {
        $notAdmin    = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent", "Primary Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->user()->id);
        }
        $portal = Portal::where('name', 'Property Finder')->first();

        $data['pf'] = Property::whereNot('completion_status', 'Off-Plan Primary')
            ->where('region_id', 1)
            ->whereHas('portals', function ($q) use ($portal) {
                $q->where('portal_id', $portal->id);
            })->where('is_live', '1')
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->count();
        $portal        = Portal::where('name', 'Bayut')->first();
        $data['bayut'] = Property::whereNot('completion_status', 'Off-Plan Primary')
            ->where('region_id', 1)
            ->whereHas('portals', function ($q) use ($portal) {
                $q->where('portal_id', $portal->id);
            })->where('is_live', '1')
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->count();
        $portal = Portal::where('name', 'Dubizzle')->first();

        $data['dubizzle'] = Property::whereNot('completion_status', 'Off-Plan Primary')
            ->where('region_id', 1)
            ->whereHas('portals', function ($q) use ($portal) {
                $q->where('portal_id', $portal->id);
            })->where('is_live', '1')
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->count();

        return $data;
    }

    private function prepareLeadsData(Request $request, $date = null)
    {
        $query = Lead::query();
        $this->applyDateFilter($query, "leads.created_at", $date);

        $superAdmin  = auth()->user()->hasRole(['Super Admin']);
        $notAdmin    = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        $selectQuery = "COUNT(DISTINCT leads.id) as total,";
        $selectQuery .= "COUNT(DISTINCT CASE WHEN sources.name = 'Property Finder' THEN leads.id END) AS pf,";
        $selectQuery .= "COUNT(DISTINCT CASE WHEN sources.name = 'Bayut' THEN leads.id END) AS bayut,";
        $selectQuery .= "COUNT(DISTINCT CASE WHEN sources.name = 'Dubizzle' THEN leads.id END) AS dubizzle,";
        $selectQuery .= "COUNT(DISTINCT CASE WHEN sources.name NOT IN('Property Finder', 'Bayut', 'Dubizzle') THEN leads.id END) AS other";

        $leadCounts = $query->selectRaw($selectQuery)
            ->leftJoin("sources", "sources.id", "=", "leads.source_id")
            ->join("users", function ($join) use ($request) {
                $query = $join->on("leads.agent_id", "=", "users.id");
                $this->applyUserDashboardFilter($query, $request, "leads");
            })
            ->whereNotNull("leads.agent_id")
            ->when($request->boolean("deletedLeads"), fn($q) => $q->withTrashed())
            ->when($notAdmin && (! auth()->user()->hasPermissionTo("Lead Pool List")), function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })
            ->when($notAdmin && auth()->user()->hasPermissionTo("Lead Pool List"), function ($q) use ($childrenIds) {
                $q->where(function ($q) use ($childrenIds) {
                    $q->whereIn("agent_id", $childrenIds)->whereNull("converted_to_listing_lead");
                });
            })
            ->when(auth()->user()->hasRole("Admin - Abu Dhabi"), function ($q) {
                $q->where("leads.region_id", 2);
            })
            ->when(! ($request->boolean("archivedLeads") && $superAdmin), fn($q) => $q->where("leads.status", true)->where('is_archive', false))
            ->first();
        // dd($leadCounts);
        return $leadCounts;
    }

    private function prepareDealsData($isAdmin = false, $userIds = [], $date = null, $dashboard = null, $inactiveUsersAlso = false)
    {
        $query = Deal::query();
        $this->applyDateFilter($query, "deals.created_at", $date);

        $selectQuery = "COUNT(DISTINCT deals.id) as total";

        $query->selectRaw($selectQuery)
            ->when(! $isAdmin, fn($query) => $query->whereIn("agent_id", $userIds))
            ->whereHas("agent", function ($q) use ($inactiveUsersAlso) {
                $q->when(! $inactiveUsersAlso, fn($q1) => $q1->where("status", true));
            });

        if ($dashboard == "primary") {
            $query->whereHas("property", function ($q) {
                $q->whereIn("completion_status", ["Off-Plan Primary", "Ready Primary"]);
            });
        } elseif ($this->checkIfSecondaryDashboard($dashboard)) {
            $zoneId = $this->getSecondaryDashboardZoneId($dashboard);
            $query->when($zoneId != null, function ($q) use ($zoneId) {
                $q->whereHas("agent", function ($q1) use ($zoneId) {
                    $q1->where("zone_id", $zoneId);
                });
            })
                ->whereHas("property", function ($q) {
                    $q->where("region_id", "<>", 2);
                });
        } elseif ($dashboard == "abu-dhabi") {
            $query->whereHas("property", function ($q) {
                $q->where("region_id", 2);
            });
        }
        // chinese team and all dashboard have same query
        return $query->first();
    }




    private function preparePropertyFinderData(Request $request, $date = null)
    {
        $pf_rank = [];
        $dates   = [];
        $portal  = Portal::where('name', 'Property Finder')->first();
        $source  = Source::where('name', 'Property Finder')->first();
        $user_id = ($request->has('user_id') && $request->user_id != '') ? $request->user_id : '';
        if ($user_id != '') {
            $user  = User::find($user_id);
            $dates = $request->dates;
        } else {
            $user = auth()->user();
        }

        $notAdmin    = $user->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];

        if ($notAdmin) {
            $user_id       = ($request->has('user_id') && $request->user_id != '') ? $request->user_id : auth()->id();
            $childrenIds   = getUserChildrenIdsOnly($user_id);
            $childrenIds[] = $user_id;
        }

        // Total number of properties where is_live = true
        $totalPropertiesCount = Property::whereHas('propertyMeta', function ($query) {
            // Ensure property has 'bayut_status' as 'active'

            $query->where('meta_key', 'pf_status')
                ->where('meta_value', 'Online');
        })
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->when(! empty($dates), function ($q) use ($dates) {
                $q->whereDate('properties.created_at', '>=', $dates['startDate'])->whereDate('properties.created_at', '<=', $dates['endDate']);
            })
            ->where('is_live', true) // Only consider live properties
            ->whereHas('propertyMeta', function ($query) {
                // Ensure property has 'bayut_level' (this is needed for the total count)
                $query->where('meta_key', 'pf_level');
            })
            ->count();

        // Get all unique bayut_levels
        $uniqueBayutLevels = PropertyMeta::where('meta_key', 'pf_level')
            ->distinct()
            ->pluck('meta_value');

        // Count the number of properties for each bayut_level
        $bayutLevelCounts  = [];
        $uniqueBayutLevels = ['Premium', 'Featured', 'Standard'];
        foreach ($uniqueBayutLevels as $bayutLevel) {
            $count = Property::whereHas('propertyMeta', function ($query) use ($bayutLevel) {
                $query->where('meta_key', 'pf_level')
                    ->where('meta_value', $bayutLevel);
            })
                ->when($notAdmin, function ($q) use ($childrenIds) {
                    $q->whereIn("agent_id", $childrenIds);
                })->when(! empty($dates), function ($q) use ($dates) {
                    $q->whereDate('properties.created_at', '>=', $dates['startDate'])->whereDate('properties.created_at', '<=', $dates['endDate']);
                })
                ->where('publish_status', 'Published')
                ->where('is_live', true)
                ->count();

            $bayutLevelCounts[$bayutLevel] = $count;
        }
        // dd($bayutLevelCounts);
        // Average pf_rank
        $averageBayutRank = round(PropertyMeta::where('meta_key', 'pf_rank')
            ->join('properties', 'property_meta.property_id', '=', 'properties.id')
            ->where('properties.is_live', true)
            ->when($notAdmin, function ($q) use ($childrenIds) {
                // Filter by agent_id from the properties table
                $q->whereIn("properties.agent_id", $childrenIds);
            })
            ->when(! empty($dates), function ($q) use ($dates) {
                // Filter by created_at date range from the properties table
                $q->whereDate('properties.created_at', '>=', $dates['startDate'])
                    ->whereDate('properties.created_at', '<=', $dates['endDate']);
            })
            ->avg('meta_value'), 2);

        // Count properties based on pf_rank ranges
        $rankRanges = [
            '0-40'   => 0,
            '41-60'  => 0,
            '61-80'  => 0,
            '81-100' => 0,
        ];

        $propertyRanks = PropertyMeta::where('meta_key', 'pf_rank')
            ->join('properties', 'property_meta.property_id', '=', 'properties.id')
            ->where('properties.is_live', true)
            ->when($notAdmin, function ($q) use ($childrenIds) {
                // Filter by agent_id from the properties table
                $q->whereIn("properties.agent_id", $childrenIds);
            })
            ->when(! empty($dates), function ($q) use ($dates) {
                // Filter by created_at date range from the properties table
                $q->whereDate('properties.created_at', '>=', $dates['startDate'])
                    ->whereDate('properties.created_at', '<=', $dates['endDate']);
            })
            ->pluck('meta_value');

        foreach ($propertyRanks as $rank) {
            if ($rank >= 0 && $rank <= 40) {
                $rankRanges['0-40']++;
            } elseif ($rank >= 41 && $rank <= 60) {
                $rankRanges['41-60']++;
            } elseif ($rank >= 61 && $rank <= 80) {
                $rankRanges['61-80']++;
            } elseif ($rank >= 81 && $rank <= 100) {
                $rankRanges['81-100']++;
            }
        }

        // Percentage of properties where bayut_trucheck_status is 'trucheckStatus'
        $trucheckStatusCount = PropertyMeta::where('meta_key', 'pf_verified')
            ->where('meta_value', '1')
            ->join('properties', 'property_meta.property_id', '=', 'properties.id')
            ->where('properties.is_live', true)
            ->when($notAdmin, function ($q) use ($childrenIds) {
                // Filter by agent_id from the properties table
                $q->whereIn("properties.agent_id", $childrenIds);
            })
            ->when(! empty($dates), function ($q) use ($dates) {
                // Filter by created_at date range from the properties table
                $q->whereDate('properties.created_at', '>=', $dates['startDate'])
                    ->whereDate('properties.created_at', '<=', $dates['endDate']);
            })
            ->count();

        $trucheckStatusPercentage = $totalPropertiesCount > 0
            ? ($trucheckStatusCount / $totalPropertiesCount) * 100
            : 0;

        // Count of pf_status as active and inactive
        $bayutStatusCounts = PropertyMeta::where('meta_key', 'pf_status')
            ->whereIn('meta_value', ['Online', 'Offline'])
            ->join('properties', 'property_meta.property_id', '=', 'properties.id')
            ->where('properties.is_live', true)
            ->when($notAdmin, function ($q) use ($childrenIds) {
                // Filter by agent_id from the properties table
                $q->whereIn("properties.agent_id", $childrenIds);
            })
            ->when(! empty($dates), function ($q) use ($dates) {
                // Filter by created_at date range from the properties table
                $q->whereDate('properties.created_at', '>=', $dates['startDate'])
                    ->whereDate('properties.created_at', '<=', $dates['endDate']);
            })

            ->select('meta_value', \DB::raw('count(*) as count'))
            ->groupBy('meta_value')
            ->pluck('count', 'meta_value')
            ->toArray();

        $verified_count = Property::whereNot('completion_status', 'Off-Plan Primary')
            ->whereHas('propertyMeta', function ($query) {
                $query->where('meta_key', 'pf_verified')
                    ->where('meta_value', true);
            })
            ->whereHas('portals', function ($q) use ($portal) {
                $q->where('portal_id', $portal->id);
            })
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })
            ->when(! empty($dates), function ($q) use ($dates) {
                $q->whereDate('properties.created_at', '>=', $dates['startDate'])->whereDate('properties.created_at', '<=', $dates['endDate']);
            })
            // ->where('is_live', '1')
            ->count();
        // dd($verified_count);
        // Prepare all data
        $result = [
            'total_properties_count'     => $totalPropertiesCount,
            'pf_level_counts'            => $bayutLevelCounts,
            'average_pf_rank'            => $averageBayutRank,
            'pf_rank_ranges'             => $rankRanges,
            'trucheckStatusCount'        => $trucheckStatusCount,
            'trucheck_status_percentage' => round($trucheckStatusPercentage, 2),
            'pf_status_counts'           => $bayutStatusCounts,
            'verified_count'             => $verified_count,
        ];

        return $result;
    }

    private function prepareBayutData(Request $request, $date = null)
    {
        $pf_rank = [];
        $dates   = [];
        $portal  = Portal::where('name', 'Bayut')->first();
        $source  = Source::where('name', 'Bayut')->first();
        $user_id = ($request->has('user_id') && $request->user_id != '') ? $request->user_id : '';
        if ($user_id != '') {
            $user  = User::find($user_id);
            $dates = $request->dates;
        } else {
            $user = auth()->user();
        }

        $notAdmin    = $user->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];

        if ($notAdmin) {
            $user_id       = ($request->has('user_id') && $request->user_id != '') ? $request->user_id : auth()->id();
            $childrenIds   = getUserChildrenIdsOnly($user_id);
            $childrenIds   = getUserChildrenIdsOnly(23);
            $childrenIds[] = $user_id;
        }
        // Total number of properties where is_live = true
        $totalPropertiesCount = Property::whereHas('propertyMeta', function ($query) {
            // Ensure property has 'bayut_status' as 'active'
            $query->where('meta_key', 'bayut_status')
                ->where('meta_value', 'active');
        })
            ->where('is_live', true) // Only consider live properties
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->when(! empty($dates), function ($q) use ($dates) {
                $q->whereDate('properties.created_at', '>=', $dates['startDate'])->whereDate('properties.created_at', '<=', $dates['endDate']);
            })
            ->whereHas('propertyMeta', function ($query) {
                // Ensure property has 'bayut_level' (this is needed for the total count)
                $query->where('meta_key', 'bayut_level');
            })
            ->count();

        // Get all unique bayut_levels
        $uniqueBayutLevels = PropertyMeta::where('meta_key', 'bayut_level')
            ->distinct()
            ->pluck('meta_value');
        $uniqueBayutLevels = ['Signature', 'Hot', 'Premium',];

        // Count the number of properties for each bayut_level
        $bayutLevelCounts = [];
        foreach ($uniqueBayutLevels as $bayutLevel) {
            $count = Property::whereHas('propertyMeta', function ($query) use ($bayutLevel) {
                $query->where('meta_key', 'bayut_level')
                    ->where('meta_value', $bayutLevel);
            })
                ->when($notAdmin, function ($q) use ($childrenIds) {
                    $q->whereIn("agent_id", $childrenIds);
                })->when(! empty($dates), function ($q) use ($dates) {
                    $q->whereDate('properties.created_at', '>=', $dates['startDate'])->whereDate('properties.created_at', '<=', $dates['endDate']);
                })
                ->where('is_live', true)
                ->count();

            $bayutLevelCounts[$bayutLevel] = $count;
        }
        // replace premium with Basic label
        $bayutLevelCounts['Basic'] = $bayutLevelCounts['Premium'];
        unset($bayutLevelCounts['Premium']);



        // Average bayut_rank
        $averageBayutRank = round(PropertyMeta::where('meta_key', 'bayut_rank')
            ->join('properties', 'property_meta.property_id', '=', 'properties.id')
            ->where('properties.is_live', true)
            ->when($notAdmin, function ($q) use ($childrenIds) {
                // Filter by agent_id from the properties table
                $q->whereIn("properties.agent_id", $childrenIds);
            })
            ->when(! empty($dates), function ($q) use ($dates) {
                // Filter by created_at date range from the properties table
                $q->whereDate('properties.created_at', '>=', $dates['startDate'])
                    ->whereDate('properties.created_at', '<=', $dates['endDate']);
            })
            ->avg('meta_value'), 2);

        // Count properties based on bayut_rank ranges
        $rankRanges = [
            '0-40'   => 0,
            '41-60'  => 0,
            '61-80'  => 0,
            '81-100' => 0,
        ];

        $propertyRanks = PropertyMeta::where('meta_key', 'bayut_rank')
            ->join('properties', 'property_meta.property_id', '=', 'properties.id')
            ->where('properties.is_live', true)
            ->when($notAdmin, function ($q) use ($childrenIds) {
                // Filter by agent_id from the properties table
                $q->whereIn("properties.agent_id", $childrenIds);
            })
            ->when(! empty($dates), function ($q) use ($dates) {
                // Filter by created_at date range from the properties table
                $q->whereDate('properties.created_at', '>=', $dates['startDate'])
                    ->whereDate('properties.created_at', '<=', $dates['endDate']);
            })
            ->pluck('meta_value');

        foreach ($propertyRanks as $rank) {
            if ($rank >= 0 && $rank <= 40) {
                $rankRanges['0-40']++;
            } elseif ($rank >= 41 && $rank <= 60) {
                $rankRanges['41-60']++;
            } elseif ($rank >= 61 && $rank <= 80) {
                $rankRanges['61-80']++;
            } elseif ($rank >= 81 && $rank <= 100) {
                $rankRanges['81-100']++;
            }
        }

        // Percentage of properties where bayut_trucheck_status is 'trucheckStatus'
        $trucheckStatusCount = PropertyMeta::where('meta_key', 'bayut_trucheck_status')
            ->where('meta_value', 'Truchecked')
            ->join('properties', 'property_meta.property_id', '=', 'properties.id')
            ->where('properties.is_live', true)
            ->when($notAdmin, function ($q) use ($childrenIds) {
                // Filter by agent_id from the properties table
                $q->whereIn("properties.agent_id", $childrenIds);
            })
            ->when(! empty($dates), function ($q) use ($dates) {
                // Filter by created_at date range from the properties table
                $q->whereDate('properties.created_at', '>=', $dates['startDate'])
                    ->whereDate('properties.created_at', '<=', $dates['endDate']);
            })
            ->count();

        $trucheckStatusPercentage = $totalPropertiesCount > 0
            ? ($trucheckStatusCount / $totalPropertiesCount) * 100
            : 0;

        // Count of bayut_status as active and inactive
        $bayutStatusCounts = PropertyMeta::where('meta_key', 'bayut_status')
            ->whereIn('meta_value', ['active', 'inactive'])
            ->join('properties', 'property_meta.property_id', '=', 'properties.id')
            ->where('properties.is_live', true)
            ->when($notAdmin, function ($q) use ($childrenIds) {
                // Filter by agent_id from the properties table
                $q->whereIn("properties.agent_id", $childrenIds);
            })
            ->when(! empty($dates), function ($q) use ($dates) {
                // Filter by created_at date range from the properties table
                $q->whereDate('properties.created_at', '>=', $dates['startDate'])
                    ->whereDate('properties.created_at', '<=', $dates['endDate']);
            })
            ->select('meta_value', \DB::raw('count(*) as count'))
            ->groupBy('meta_value')
            ->pluck('count', 'meta_value')
            ->toArray();

        $verified_count = Property::whereNot('completion_status', 'Off-Plan Primary')->whereHas('propertyMeta', function ($query) {
            $query->where('meta_key', 'bayut_review_status')
                ->where('meta_value', 'Approved');
        })
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->when(! empty($dates), function ($q) use ($dates) {
                $q->whereDate('properties.created_at', '>=', $dates['startDate'])->whereDate('properties.created_at', '<=', $dates['endDate']);
            })
            ->whereHas('portals', function ($q) use ($portal) {
                $q->where('portal_id', $portal->id);
            })
            // ->when($notAdmin, function ($q) use ($childrenIds) {
            //     $q->whereIn("agent_id", $childrenIds);
            // })
            // ->when(! empty($dates), function ($q) use ($dates) {
            //     $q->whereDate('properties.created_at', '>=', $dates['startDate'])->whereDate('properties.created_at', '<=', $dates['endDate']);
            // })
            ->where('is_live', '1')->count();

        // Prepare all data
        $result = [
            'total_properties_count'     => $totalPropertiesCount,
            'bayut_level_counts'         => $bayutLevelCounts,
            'average_bayut_rank'         => $averageBayutRank,
            'bayut_rank_ranges'          => $rankRanges,
            'trucheckStatusCount'        => $trucheckStatusCount,
            'trucheck_status_percentage' => round($trucheckStatusPercentage, 2),
            'bayut_status_counts'        => $bayutStatusCounts,
            'verified_count'             => $verified_count,
        ];

        return $result;
    }



    private function prepareDubizzleData(Request $request, $date = null)
    {
        $pf_rank = [];
        $dates   = [];
        $portal  = Portal::where('name', 'Dubizzle')->first();
        $source  = Source::where('name', 'Dubizzle')->first();
        $user_id = ($request->has('user_id') && $request->user_id != '') ? $request->user_id : '';
        if ($user_id != '') {
            $user  = User::find($user_id);
            $dates = $request->dates;
        } else {
            $user = auth()->user();
        }

        $notAdmin    = $user->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];

        if ($notAdmin) {
            $user_id       = ($request->has('user_id') && $request->user_id != '') ? $request->user_id : auth()->id();
            $childrenIds   = getUserChildrenIdsOnly($user_id);
            $childrenIds   = getUserChildrenIdsOnly(23);
            $childrenIds[] = $user_id;
        }
        // Total number of properties where is_live = true
        $totalPropertiesCount = Property::whereHas('propertyMeta', function ($query) {
            // Ensure property has 'bayut_status' as 'active'
            $query->where('meta_key', 'dubizzle_status')
                ->where('meta_value', 'active');
        })
            ->where('is_live', true) // Only consider live properties
            ->whereHas('propertyMeta', function ($query) {
                // Ensure property has 'dubizzle_level' (this is needed for the total count)
                $query->where('meta_key', 'dubizzle_level');
            })
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->when(! empty($dates), function ($q) use ($dates) {
                $q->whereDate('properties.created_at', '>=', $dates['startDate'])->whereDate('properties.created_at', '<=', $dates['endDate']);
            })
            ->count();

        // Get all unique dubizzle_levels
        $uniqueBayutLevels = PropertyMeta::where('meta_key', 'dubizzle_level')
            ->distinct()
            ->pluck('meta_value');

        // Count the number of properties for each dubizzle_level
        $bayutLevelCounts = [];
        $uniqueBayutLevels = ['Premium', 'Standard', 'Listing'];
        foreach ($uniqueBayutLevels as $bayutLevel) {
            $count = Property::whereHas('propertyMeta', function ($query) use ($bayutLevel) {
                $query->where('meta_key', 'dubizzle_level')
                    ->where('meta_value', $bayutLevel);
            })
                ->where('is_live', true)
                ->when($notAdmin, function ($q) use ($childrenIds) {
                    $q->whereIn("agent_id", $childrenIds);
                })->when(! empty($dates), function ($q) use ($dates) {
                    $q->whereDate('properties.created_at', '>=', $dates['startDate'])->whereDate('properties.created_at', '<=', $dates['endDate']);
                })
                ->count();

            $bayutLevelCounts[$bayutLevel] = $count;
        }

        // listing => Standard
        // Premium = Premium
        // Standard = Featured

        $bayutLevelCounts['Featured'] = $bayutLevelCounts['Standard'];
        unset($bayutLevelCounts['Standard']);

        $bayutLevelCounts['Standard'] = $bayutLevelCounts['Listing'];
        unset($bayutLevelCounts['Listing']);



        // $bayutLevelCounts['Featured'] =$totalPropertiesCount - ($bayutLevelCounts['Premium']+$bayutLevelCounts['Standard']);
        // unset($bayutLevelCounts['Listing']);

        // Average dubizzle_rank
        $averageBayutRank = round(PropertyMeta::where('meta_key', 'dubizzle_rank')
            ->join('properties', 'property_meta.property_id', '=', 'properties.id')
            ->where('properties.is_live', true)
            ->when($notAdmin, function ($q) use ($childrenIds) {
                // Filter by agent_id from the properties table
                $q->whereIn("properties.agent_id", $childrenIds);
            })
            ->when(! empty($dates), function ($q) use ($dates) {
                // Filter by created_at date range from the properties table
                $q->whereDate('properties.created_at', '>=', $dates['startDate'])
                    ->whereDate('properties.created_at', '<=', $dates['endDate']);
            })
            ->avg('meta_value'), 2);

        // Count properties based on dubizzle_rank ranges
        $rankRanges = [
            '0-40'   => 0,
            '41-60'  => 0,
            '61-80'  => 0,
            '81-100' => 0,
        ];

        $propertyRanks = PropertyMeta::where('meta_key', 'dubizzle_rank')
            ->join('properties', 'property_meta.property_id', '=', 'properties.id')
            ->where('properties.is_live', true)
            ->when($notAdmin, function ($q) use ($childrenIds) {
                // Filter by agent_id from the properties table
                $q->whereIn("properties.agent_id", $childrenIds);
            })
            ->when(! empty($dates), function ($q) use ($dates) {
                // Filter by created_at date range from the properties table
                $q->whereDate('properties.created_at', '>=', $dates['startDate'])
                    ->whereDate('properties.created_at', '<=', $dates['endDate']);
            })
            ->pluck('meta_value');

        foreach ($propertyRanks as $rank) {
            if ($rank >= 0 && $rank <= 40) {
                $rankRanges['0-40']++;
            } elseif ($rank >= 41 && $rank <= 60) {
                $rankRanges['41-60']++;
            } elseif ($rank >= 61 && $rank <= 80) {
                $rankRanges['61-80']++;
            } elseif ($rank >= 81 && $rank <= 100) {
                $rankRanges['81-100']++;
            }
        }

        // Percentage of properties where dubizzle_trucheck_status is 'trucheckStatus'
        $trucheckStatusCount = PropertyMeta::where('meta_key', 'dubizzle_trucheck_status')
            ->where('meta_value', 'Truchecked')
            ->join('properties', 'property_meta.property_id', '=', 'properties.id')
            ->where('properties.is_live', true)
            ->when($notAdmin, function ($q) use ($childrenIds) {
                // Filter by agent_id from the properties table
                $q->whereIn("properties.agent_id", $childrenIds);
            })
            ->when(! empty($dates), function ($q) use ($dates) {
                // Filter by created_at date range from the properties table
                $q->whereDate('properties.created_at', '>=', $dates['startDate'])
                    ->whereDate('properties.created_at', '<=', $dates['endDate']);
            })
            ->count();

        $trucheckStatusPercentage = $totalPropertiesCount > 0
            ? ($trucheckStatusCount / $totalPropertiesCount) * 100
            : 0;

        // Count of dubizzle_status as active and inactive
        $bayutStatusCounts = PropertyMeta::where('meta_key', 'dubizzle_status')
            ->whereIn('meta_value', ['active', 'inactive'])
            ->join('properties', 'property_meta.property_id', '=', 'properties.id')
            ->where('properties.is_live', true)
            ->when($notAdmin, function ($q) use ($childrenIds) {
                // Filter by agent_id from the properties table
                $q->whereIn("properties.agent_id", $childrenIds);
            })
            ->when(! empty($dates), function ($q) use ($dates) {
                // Filter by created_at date range from the properties table
                $q->whereDate('properties.created_at', '>=', $dates['startDate'])
                    ->whereDate('properties.created_at', '<=', $dates['endDate']);
            })
            ->select('meta_value', \DB::raw('count(*) as count'))
            ->groupBy('meta_value')
            ->pluck('count', 'meta_value')
            ->toArray();

        $verified_count = Property::whereNot('completion_status', 'Off-Plan Primary')->whereHas('propertyMeta', function ($query) {
            $query->where('meta_key', 'dubizzle_review_status')
                ->where('meta_value', 'Approved');
        })->whereHas('portals', function ($q) use ($portal) {
            $q->where('portal_id', $portal->id);
        })
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->when(! empty($dates), function ($q) use ($dates) {
                $q->whereDate('properties.created_at', '>=', $dates['startDate'])->whereDate('properties.created_at', '<=', $dates['endDate']);
            })
            ->where('is_live', '1')->count();

        // Prepare all data
        $result = [
            'total_properties_count'     => $totalPropertiesCount,
            'dubizzle_level_counts'      => $bayutLevelCounts,
            'average_dubizzle_rank'      => $averageBayutRank,
            'dubizzle_rank_ranges'       => $rankRanges,
            'trucheckStatusCount'        => $trucheckStatusCount,
            'trucheck_status_percentage' => round($trucheckStatusPercentage, 2),
            'dubizzle_status_counts'     => $bayutStatusCounts,
            'verified_count'             => $verified_count,
        ];

        return $result;
    }


    private function prepareHighAndLeastNoOfActivityData(Request $request, $date = null, $limit = 10)
    {

        $notAdmin    = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent", "Primary Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        $selectQuery = "users.name AS user_name,";
        $selectQuery .= "users.id AS user_id,";
        $selectQuery .= "groups.name AS zone_name,";
        $selectQuery .= "parent.name AS parent_name,";
        $selectQuery .= "MAX(users.thumbnail) AS user_thumbnail,";
        $selectQuery .= "SUM(user_activity_counts.count) as count";

        $baseQuery = User::selectRaw($selectQuery);
        $this->applyUserDashboardFilter($baseQuery, $request);
        $baseQuery->leftJoin("groups", "groups.id", "users.zone_id")
            ->leftJoin("users as parent", "parent.id", "users.parent_id")
            ->join("user_activity_counts", function ($join) use ($date) {
                $query = $join->on("user_activity_counts.user_id", "users.id");
                $this->applyDateFilter($query, "user_activity_counts.reporting_date", $date);
            })
            ->whereHas("roles", function ($query) {
                $query->where("name", "Agent");
            })
            ->when($notAdmin, fn($query) => $query->whereIn("users.id", $childrenIds))
            ->groupBy("users.id")
            ->groupBy("users.name")
            ->groupBy("groups.name")
            ->groupBy("parent.name")
            ->take($limit);

        $highActivityUsersQuery  = $baseQuery;
        $leastActivityUsersQuery = clone $baseQuery;

        $highActivityUsers  = $highActivityUsersQuery->orderByDesc("count")->get();
        $leastActivityUsers = $leastActivityUsersQuery->orderBy("count")->get();
        // dd($highActivityUsers);
        return [
            "high_counts"  => $highActivityUsers,
            "least_counts" => $leastActivityUsers,
        ];
    }

    private function prepareHighestNoOfLeadData(Request $request, $date = null, $limit = 10): array
    {
        $query = User::query();
        $this->applyDateFilter($query, "leads.created_at", $date);

        $superAdmin  = auth()->user()->hasRole(['Super Admin']);
        $notAdmin    = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        $selectQuery = "users.name AS user_name,";
        $selectQuery .= "users.id AS user_id,";
        $selectQuery .= "groups.name AS zone_name,";
        $selectQuery .= "parent.name AS parent_name,";
        $selectQuery .= "MAX(users.thumbnail) AS user_thumbnail,";
        $selectQuery .= "COUNT(DISTINCT leads.id) as count";

        $query->selectRaw($selectQuery);
        $this->applyUserDashboardFilter($query, $request);
        $query->leftJoin("groups", "groups.id", "users.zone_id")
            ->leftJoin("users as parent", "parent.id", "users.parent_id")
            ->join("leads", function ($join) use ($date, $request, $superAdmin, $notAdmin, $childrenIds) {
                $join->on("users.id", "=", "leads.agent_id")
                    ->where(function ($query) use ($request) {
                        $query->whereNull("leads.deleted_at")->when($request->boolean("deletedLeads"), fn($q1) => $q1->orWhereNotNull("leads.deleted_at"));
                    })
                    ->whereNotNull("leads.agent_id")
                    ->when($notAdmin && (! auth()->user()->hasPermissionTo("Lead Pool List")), function ($q) use ($childrenIds) {
                        $q->whereIn("agent_id", $childrenIds);
                    })
                    ->when($notAdmin && auth()->user()->hasPermissionTo("Lead Pool List"), function ($q) use ($childrenIds) {
                        $q->where(function ($q) use ($childrenIds) {
                            $q->whereIn("agent_id", $childrenIds)->whereNull("converted_to_listing_lead");
                        });
                    })
                    ->when(auth()->user()->hasRole("Admin - Abu Dhabi"), function ($q) {
                        $q->where("leads.region_id", 2);
                    })
                    ->when(! ($request->boolean("archivedLeads") && $superAdmin), fn($q) => $q->where("leads.status", true)->where("is_archive", false));
            })
            ->leftJoin("sources", "leads.source_id", "=", "sources.id")
            ->when($notAdmin, fn($query) => $query->whereIn("users.id", $childrenIds))
            ->when(! $request->boolean("inactiveUsers"), fn($q) => $q->where("users.status", true))
            ->whereHas("roles", function ($query) {
                $query->where("name", "Agent");
            })
            ->groupBy("users.id")
            ->groupBy("users.name")
            ->groupBy("groups.name")
            ->groupBy("parent.name")
            ->orderByDesc("count")
            ->take($limit);

        $overallCountQuery        = $query;
        $propertyFinderCountQuery = clone $query;
        $bayutCountQuery          = clone $query;
        $dubizzleCountQuery       = clone $query;

        $overallCounts        = $overallCountQuery->get();
        $propertyFinderCounts = $propertyFinderCountQuery->where("sources.name", "Property Finder")->get();
        $bayutCounts          = $bayutCountQuery->where("sources.name", "Bayut")->get();
        $dubizzleCounts       = $dubizzleCountQuery->where("sources.name", "Dubizzle")->get();

        return [
            "overall_counts"         => $overallCounts,
            "property_finder_counts" => $propertyFinderCounts,
            "bayut_counts"           => $bayutCounts,
            "dubizzle_counts"        => $dubizzleCounts,
        ];
    }

    private function prepareHighestNoOfListingData(Request $request, $date = null, $limit = 10)
    {
        $notAdmin    = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent", "Primary Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        $selectQuery = "users.name AS user_name,";
        $selectQuery .= "users.id AS user_id,";
        $selectQuery .= "groups.name AS zone_name,";
        $selectQuery .= "parent.name AS parent_name,";
        $selectQuery .= "MAX(users.thumbnail) AS user_thumbnail,";
        $selectQuery .= "COUNT(DISTINCT properties.id) AS count";

        $baseQuery = User::selectRaw($selectQuery);
        $this->applyUserDashboardFilter($baseQuery, $request);
        $baseQuery->join("properties", function ($join) use ($date, $request, $notAdmin, $childrenIds) {
            $join->on("users.id", "=", "properties.agent_id")
                ->when(auth()->user()->hasRole("Agent"), function ($q) use ($childrenIds) {
                    $q->where("agent_id", $childrenIds);
                })
                ->when(auth()->user()->hasRole("Admin - Abu Dhabi"), function ($q) {
                    $q->where("properties.region_id", 2);
                })
                ->when($notAdmin, function ($q) use ($childrenIds) {
                    $q->where(function ($q) use ($childrenIds) {
                        $q->whereIn("agent_id", $childrenIds)->orWhere("publish_status", "POCKET")->orWhere('is_live', true);
                    });
                })
                ->whereNotNull("agent_id")
                ->where("is_live", true);
        })
            ->join("property_portals", "properties.id", "property_portals.property_id")
            ->join("portals", "portals.id", "property_portals.portal_id")
            ->leftJoin("groups", "groups.id", "users.zone_id")
            ->leftJoin("users as parent", "parent.id", "users.parent_id")
            ->whereHas("roles", function ($query) {
                $query->where("name", "Agent");
            })
            ->when($notAdmin, fn($query) => $query->whereIn("users.id", $childrenIds))
            ->groupBy("users.id")
            ->groupBy("users.name")
            ->groupBy("groups.name")
            ->groupBy("parent.name")
            ->orderByDesc("count")
            ->take($limit);

        $overallCountQuery        = $baseQuery;
        $propertyFinderCountQuery = clone $baseQuery;
        $dubizzleCountQuery       = clone $baseQuery;
        $bayutCountQuery          = clone $baseQuery;

        $overallCounts        = $overallCountQuery->get();
        $bayutCounts          = $propertyFinderCountQuery->where("portals.name", "Property Finder")->get();
        $propertyFinderCounts = $dubizzleCountQuery->where("portals.name", "Bayut")->get();
        $dubizzleCounts       = $bayutCountQuery->where("portals.name", "Dubizzle")->get();

        return [
            "overall_counts"         => $overallCounts,
            "property_finder_counts" => $propertyFinderCounts,
            "bayut_counts"           => $bayutCounts,
            "dubizzle_counts"        => $dubizzleCounts,
        ];
    }

    private function prepareHighestNoOfQualityScoreData(Request $request, $date = null, $limit = 10)
    {
        $notAdmin    = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent", "Primary Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        $selectQuery = "users.name AS user_name,";
        $selectQuery .= "users.id AS user_id,";
        $selectQuery .= "groups.name AS zone_name,";
        $selectQuery .= "parent.name AS parent_name,";
        $selectQuery .= "MAX(users.thumbnail) AS user_thumbnail,";

        $baseQuery = User::query();
        $this->applyUserDashboardFilter($baseQuery, $request);
        $baseQuery->join("properties", function ($join) use ($date, $request, $notAdmin, $childrenIds) {
            $join->on("users.id", "=", "properties.agent_id")
                ->when(auth()->user()->hasRole("Agent"), function ($q) use ($childrenIds) {
                    $q->where("agent_id", $childrenIds);
                })
                ->when(auth()->user()->hasRole("Admin - Abu Dhabi"), function ($q) {
                    $q->where("properties.region_id", 2);
                })
                ->when($notAdmin, function ($q) use ($childrenIds) {
                    $q->where(function ($q) use ($childrenIds) {
                        $q->whereIn("agent_id", $childrenIds)->orWhere("publish_status", "POCKET")->orWhere('is_live', true);
                    });
                })
                ->whereNotNull("agent_id")
                ->where("is_live", true);
        })
            ->join("property_meta", "properties.id", "property_meta.property_id")
            ->join("property_portals", "properties.id", "property_portals.property_id")
            ->join("portals", "portals.id", "property_portals.portal_id")
            ->leftJoin("groups", "groups.id", "users.zone_id")
            ->leftJoin("users as parent", "parent.id", "users.parent_id")
            ->whereHas("roles", function ($query) {
                $query->where("name", "Agent");
            })
            ->when($notAdmin, fn($query) => $query->whereIn("users.id", $childrenIds))
            ->groupBy("users.id")
            ->groupBy("users.name")
            ->groupBy("groups.name")
            ->groupBy("parent.name")
            ->orderByDesc("count")
            ->take($limit);

        $propertyFinderBaseQuery = $baseQuery;
        $bayutBaseQuery          = clone $baseQuery;
        $dubizzleBaseQuery       = clone $baseQuery;

        $propertyFinderScores = $propertyFinderBaseQuery->selectRaw($selectQuery . "COALESCE(ROUND(AVG(CASE WHEN property_meta.meta_key = 'pf_rank' THEN property_meta.meta_value END), 2), 0) AS count")->get();
        $bayutScores          = $bayutBaseQuery->selectRaw($selectQuery . "COALESCE(ROUND(AVG(CASE WHEN property_meta.meta_key = 'bayut_rank' THEN property_meta.meta_value END), 2), 0) AS count")->get();
        $dubizzleScores       = $dubizzleBaseQuery->selectRaw($selectQuery . "COALESCE(ROUND(AVG(CASE WHEN property_meta.meta_key = 'dubizzle_rank' THEN property_meta.meta_value END), 2),0) AS count")->get();

        return [
            "property_finder_counts" => $propertyFinderScores,
            "bayut_counts"           => $bayutScores,
            "dubizzle_counts"        => $dubizzleScores,
        ];
    }

    private function prepareLeadOverviewSourceWiseData(Request $request, $date = null)
    {
        $query = Lead::query();
        $query = $this->applyDateFilter($query, "leads.created_at", $date);

        $superAdmin  = auth()->user()->hasRole(['Super Admin']);
        $notAdmin    = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        $selectQuery = "COUNT(DISTINCT CASE WHEN sources.name = 'Property Finder' THEN leads.id END) AS pf_count,";
        $selectQuery .= "COUNT(DISTINCT CASE WHEN sources.name = 'Bayut' THEN leads.id END) AS bayut_count,";
        $selectQuery .= "COUNT(DISTINCT CASE WHEN sources.name = 'Dubizzle' THEN leads.id END) AS dubizzle_count,";
        $selectQuery .= "COUNT(DISTINCT CASE WHEN sources.name NOT IN('Property Finder', 'Bayut', 'Dubizzle') THEN leads.id END) AS other_count";

        $leadCounts = $query->selectRaw($selectQuery)
            ->leftJoin("sources", "sources.id", "=", "leads.source_id")
            ->join("users", function ($join) use ($request) {
                $query = $join->on("leads.agent_id", "=", "users.id");
                $this->applyUserDashboardFilter($query, $request, "leads");
            })
            ->whereNotNull("leads.agent_id")
            ->when($request->boolean("deletedLeads"), fn($q) => $q->withTrashed())
            ->when($notAdmin && (! auth()->user()->hasPermissionTo("Lead Pool List")), function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })
            ->when($notAdmin && auth()->user()->hasPermissionTo("Lead Pool List"), function ($q) use ($childrenIds) {
                $q->where(function ($q) use ($childrenIds) {
                    $q->whereIn("agent_id", $childrenIds)->whereNull("converted_to_listing_lead");
                });
            })
            ->when(auth()->user()->hasRole("Admin - Abu Dhabi"), function ($q) {
                $q->where("leads.region_id", 2);
            })
            ->when(! ($request->boolean("archivedLeads") && $superAdmin), fn($q) => $q->where("leads.status", true)->where('is_archive', false))
            ->first();

        return $leadCounts->toArray();
    }

    private function prepareSourceWiseDateWiseLeadCountData(Request $request, $date = null): array
    {
        $query = Lead::query();
        $query = $this->applyDateFilter($query, "leads.created_at", $date);

        $superAdmin  = auth()->user()->hasRole(['Super Admin']);
        $notAdmin    = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        $selectQuery = "DATE(leads.created_at) as date,";
        $selectQuery .= "COUNT(DISTINCT CASE WHEN mediums.name = 'Email' THEN leads.id END) AS email_count,";
        $selectQuery .= "COUNT(DISTINCT CASE WHEN mediums.name = 'Call' THEN leads.id END) AS call_count,";
        $selectQuery .= "COUNT(DISTINCT CASE WHEN mediums.name = 'WhatsApp' THEN leads.id END) AS wa_count,";
        $selectQuery .= "COUNT(DISTINCT CASE WHEN mediums.name NOT IN ('Email', 'Call', 'WhatsApp') THEN leads.id END) AS other_count,";
        $selectQuery .= "COUNT(DISTINCT leads.id) AS total_count";

        $query->selectRaw($selectQuery)
            ->leftJoin("sources", "sources.id", "=", "leads.source_id")
            ->leftJoin("mediums", "leads.lead_medium_id", "=", "mediums.id")
            ->join("users", function ($join) use ($request) {
                $query = $join->on("leads.agent_id", "=", "users.id");
                $this->applyUserDashboardFilter($query, $request, "leads");
            })
            ->whereNotNull("leads.agent_id")
            ->when($request->boolean("deletedLeads"), fn($q) => $q->withTrashed())
            ->when($notAdmin && (! auth()->user()->hasPermissionTo("Lead Pool List")), function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })
            ->when($notAdmin && auth()->user()->hasPermissionTo("Lead Pool List"), function ($q) use ($childrenIds) {
                $q->where(function ($q) use ($childrenIds) {
                    $q->whereIn("agent_id", $childrenIds)->whereNull("converted_to_listing_lead");
                });
            })
            ->when(auth()->user()->hasRole("Admin - Abu Dhabi"), function ($q) {
                $q->where("leads.region_id", 2);
            })
            ->when(! ($request->boolean("archivedLeads") && $superAdmin), fn($q) => $q->where("leads.status", true)->where('is_archive', false))
            ->groupBy("date")
            ->orderBy("date");

        $pfLeadsQuery       = $query;
        $bayutLeadsQuery    = clone $query;
        $dubizzleLeadsQuery = clone $query;
        $otherLeadsQuery    = clone $query;

        $pfLeads       = $pfLeadsQuery->where("sources.name", "Property Finder")->get()->toArray();
        $bayutLeads    = $bayutLeadsQuery->where("sources.name", "Bayut")->get()->toArray();
        $dubizzleLeads = $dubizzleLeadsQuery->where("sources.name", "Dubizzle")->get()->toArray();
        $otherLeads    = $otherLeadsQuery->whereNotIn("sources.name", ["Property Finder", "Bayut", "Dubizzle"])->get()->toArray();

        $dates     = $this->getStartAndEndDate($date);
        $startDate = $dates["start_date"];
        $endDate   = $dates["end_date"];
        $period    = CarbonPeriod::create($startDate, $endDate);

        $data = [];
        foreach ($period as $date) {
            $formattedDate = $date->toDateString();
            $index         = array_search($formattedDate, array_column($pfLeads, "date"));
            if ($index !== false) {
                $data["pf"]["dates"][]        = $formattedDate;
                $data["pf"]["email_counts"][] = $pfLeads[$index]["email_count"];
                $data["pf"]["call_counts"][]  = $pfLeads[$index]["call_count"];
                $data["pf"]["wa_counts"][]    = $pfLeads[$index]["wa_count"];
                $data["pf"]["other_counts"][] = $pfLeads[$index]["other_count"];
            } else {
                $data["pf"]["dates"][]        = $formattedDate;
                $data["pf"]["email_counts"][] = 0;
                $data["pf"]["call_counts"][]  = 0;
                $data["pf"]["wa_counts"][]    = 0;
                $data["pf"]["other_counts"][] = 0;
            }

            $index = array_search($formattedDate, array_column($bayutLeads, "date"));
            if ($index !== false) {
                $data["bayut"]["dates"][]        = $formattedDate;
                $data["bayut"]["email_counts"][] = $bayutLeads[$index]["email_count"];
                $data["bayut"]["call_counts"][]  = $bayutLeads[$index]["call_count"];
                $data["bayut"]["wa_counts"][]    = $bayutLeads[$index]["wa_count"];
                $data["bayut"]["other_counts"][] = $bayutLeads[$index]["other_count"];
            } else {
                $data["bayut"]["dates"][]        = $formattedDate;
                $data["bayut"]["email_counts"][] = 0;
                $data["bayut"]["call_counts"][]  = 0;
                $data["bayut"]["wa_counts"][]    = 0;
                $data["bayut"]["other_counts"][] = 0;
            }

            $index = array_search($formattedDate, array_column($dubizzleLeads, "date"));
            if ($index !== false) {
                $data["dubizzle"]["dates"][]        = $formattedDate;
                $data["dubizzle"]["email_counts"][] = $dubizzleLeads[$index]["email_count"];
                $data["dubizzle"]["call_counts"][]  = $dubizzleLeads[$index]["call_count"];
                $data["dubizzle"]["wa_counts"][]    = $dubizzleLeads[$index]["wa_count"];
                $data["dubizzle"]["other_counts"][] = $dubizzleLeads[$index]["other_count"];
            } else {
                $data["dubizzle"]["dates"][]        = $formattedDate;
                $data["dubizzle"]["email_counts"][] = 0;
                $data["dubizzle"]["call_counts"][]  = 0;
                $data["dubizzle"]["wa_counts"][]    = 0;
                $data["dubizzle"]["other_counts"][] = 0;
            }

            $index = array_search($formattedDate, array_column($otherLeads, "date"));
            if ($index !== false) {
                $data["other"]["dates"][]        = $formattedDate;
                $data["other"]["email_counts"][] = $otherLeads[$index]["email_count"];
                $data["other"]["call_counts"][]  = $otherLeads[$index]["call_count"];
                $data["other"]["wa_counts"][]    = $otherLeads[$index]["wa_count"];
                $data["other"]["other_counts"][] = $otherLeads[$index]["other_count"];
            } else {
                $data["other"]["dates"][]        = $formattedDate;
                $data["other"]["email_counts"][] = 0;
                $data["other"]["call_counts"][]  = 0;
                $data["other"]["wa_counts"][]    = 0;
                $data["other"]["other_counts"][] = 0;
            }
        }

        return $data;
    }

    private function prepareLeadOverviewData(Request $request, $date = null)
    {
        $query = Lead::query();
        $query = $this->applyDateFilter($query, "leads.created_at", $date);

        $superAdmin  = auth()->user()->hasRole(['Super Admin']);
        $notAdmin    = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        $selectQuery = "mediums.name as medium_name,";
        $selectQuery .= "COUNT(leads.id) as count";

        return $query->selectRaw($selectQuery)
            ->leftJoin("mediums", "leads.lead_medium_id", "=", "mediums.id")
            ->join("users", function ($join) use ($request) {
                $query = $join->on("leads.agent_id", "=", "users.id");
                $this->applyUserDashboardFilter($query, $request, "leads");
            })
            ->whereNotNull("leads.agent_id")
            ->when($request->boolean("deletedLeads"), fn($q) => $q->withTrashed())
            ->when($notAdmin && (! auth()->user()->hasPermissionTo("Lead Pool List")), function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })
            ->when($notAdmin && auth()->user()->hasPermissionTo("Lead Pool List"), function ($q) use ($childrenIds) {
                $q->where(function ($q) use ($childrenIds) {
                    $q->whereIn("agent_id", $childrenIds)->whereNull("converted_to_listing_lead");
                });
            })
            ->when(auth()->user()->hasRole("Admin - Abu Dhabi"), function ($q) {
                $q->where("leads.region_id", 2);
            })
            ->when(! ($request->boolean("archivedLeads") && $superAdmin), fn($q) => $q->where("leads.status", true)->where('is_archive', false))
            ->groupBy("leads.lead_medium_id")
            ->groupBy("mediums.name")
            ->get()
            ->toArray();
    }

    private function prepareDateWiseLeadCountData(Request $request, $date = null): array
    {

        $query = Lead::query();
        $query = $this->applyDateFilter($query, "leads.created_at", $date);

        $superAdmin  = auth()->user()->hasRole(['Super Admin']);
        $notAdmin    = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        $selectQuery = "DATE(leads.created_at) as date,";
        $selectQuery .= "COUNT(CASE WHEN mediums.name = 'Email' THEN leads.id END) AS email_count,";
        $selectQuery .= "COUNT(CASE WHEN mediums.name = 'Call' THEN leads.id END) AS call_count,";
        $selectQuery .= "COUNT(CASE WHEN mediums.name = 'WhatsApp' THEN leads.id END) AS wa_count";

        $query->selectRaw($selectQuery)
            ->leftJoin("mediums", "leads.lead_medium_id", "=", "mediums.id")
            ->join("users", function ($join) use ($request) {
                $query = $join->on("leads.agent_id", "=", "users.id");
                $this->applyUserDashboardFilter($query, $request, "leads");
            })
            ->whereNotNull("leads.agent_id")
            ->when($request->boolean("deletedLeads"), fn($q) => $q->withTrashed())
            ->when($notAdmin && (! auth()->user()->hasPermissionTo("Lead Pool List")), function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })
            ->when($notAdmin && auth()->user()->hasPermissionTo("Lead Pool List"), function ($q) use ($childrenIds) {
                $q->where(function ($q) use ($childrenIds) {
                    $q->whereIn("agent_id", $childrenIds)->whereNull("converted_to_listing_lead");
                });
            })
            ->when(auth()->user()->hasRole("Admin - Abu Dhabi"), function ($q) {
                $q->where("leads.region_id", 2);
            })
            ->when(! ($request->boolean("archivedLeads") && $superAdmin), fn($q) => $q->where("leads.status", true)->where('is_archive', false))
            ->groupBy("date")
            ->orderBy("date");

        $leads = $query->get()->toArray();

        $dates     = $this->getStartAndEndDate($date);
        $startDate = $dates["start_date"];
        $endDate   = $dates["end_date"];
        $period    = CarbonPeriod::create($startDate, $endDate);

        $data = [];
        foreach ($period as $date) {
            $formattedDate = $date->toDateString();
            $index         = array_search($formattedDate, array_column($leads, "date"));
            if ($index !== false) {
                $data["dates"][]        = $formattedDate;
                $data["email_counts"][] = $leads[$index]["email_count"];
                $data["call_counts"][]  = $leads[$index]["call_count"];
                $data["wa_counts"][]    = $leads[$index]["wa_count"];
            } else {
                $data["dates"][]        = $formattedDate;
                $data["email_counts"][] = 0;
                $data["call_counts"][]  = 0;
                $data["wa_counts"][]    = 0;
            }
        }

        return $data;
    }

    private function prepareListingLeadOverviewSourceWiseData(Request $request, $date = null)
    {
        $query = ListingLead::query();
        $query = $this->applyDateFilter($query, "listing_leads.created_at", $date);

        $superAdmin  = auth()->user()->hasRole(['Super Admin']);
        $notAdmin    = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        $specificSources = ['Dialmydata', 'AX CONTACTS', 'AX CHAT'];

        // Query to count each source
        $counts = ListingLead::with('source')
            ->where('is_archive', false)
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })
            ->select('source_id', DB::raw('COUNT(*) as count'))
            ->whereHas('source', function ($query) use ($specificSources) {
                $query->whereIn('name', $specificSources);
            })
            ->groupBy('source_id')
            ->get()
            ->pluck('count', 'source.name')
            ->toArray();

        // Count all "Other" sources
        $otherCount = ListingLead::with('source')
            ->where('is_archive', false)
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })
            ->whereHas('source', function ($query) use ($specificSources) {
                $query->whereNotIn('name', $specificSources);
            })
            ->count();

        // Build the final result array
        $result = [
            'dialmydata' => $counts['Dialmydata'] ?? 0,
            'ax_contact' => $counts['AX CONTACTS'] ?? 0,
            'ax_chat'    => $counts['AX CHAT'] ?? 0,
            'other'      => $otherCount,
        ];

        return $result;
    }

    private function prepareSourceWiseDateWiseListingLeadCountData(Request $request, $date = null): array
    {
        $query = ListingLead::query();
        $query = $this->applyDateFilter($query, "listing_leads.created_at", $date);

        $superAdmin  = auth()->user()->hasRole(['Super Admin']);
        $notAdmin    = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        $selectQuery = "DATE(listing_leads.created_at) as date,";
        $selectQuery .= "COUNT(DISTINCT CASE WHEN sources.name = 'Dialmydata' THEN listing_leads.id END) AS dialmydata,";
        $selectQuery .= "COUNT(DISTINCT CASE WHEN sources.name = 'AX CHAT' THEN listing_leads.id END) AS ax_chat,";
        $selectQuery .= "COUNT(DISTINCT CASE WHEN sources.name = 'AX CONTACTS' THEN listing_leads.id END) AS ax_contact,";
        $selectQuery .= "COUNT(DISTINCT CASE WHEN sources.name NOT IN ('Dialmydata', 'AX CHAT', 'AX CONTACTS') THEN listing_leads.id END) AS other_count,";
        $selectQuery .= "COUNT(DISTINCT listing_leads.id) AS total_count";

        $query->where('is_archive', false)->selectRaw($selectQuery)
            ->leftJoin("sources", "sources.id", "=", "listing_leads.source_id")
            // ->leftJoin("mediums", "listing_leads.medium_id", "=", "mediums.id")
            ->join("users", function ($join) use ($request) {
                $query = $join->on("listing_leads.agent_id", "=", "users.id");
                $this->applyUserDashboardFilter($query, $request, "leads");
            })
            ->whereNotNull("listing_leads.agent_id")
            ->when($request->boolean("deletedLeads"), fn($q) => $q->withTrashed())
            ->when($notAdmin && (! auth()->user()->hasPermissionTo("Lead Pool List")), function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })
            ->when($notAdmin && auth()->user()->hasPermissionTo("Lead Pool List"), function ($q) use ($childrenIds) {
                $q->where(function ($q) use ($childrenIds) {
                    $q->whereIn("agent_id", $childrenIds);
                });
            })
            ->when(auth()->user()->hasRole("Admin - Abu Dhabi"), function ($q) {
                $q->where("listing_leads.region_id", 2);
            })
            // ->when(!($request->boolean("archivedLeads") && $superAdmin), fn($q) => $q->where("leads.status", true)->where('is_archive', false))
            ->groupBy("date")
            ->orderBy("date");

        $dialmydata          = $query;
        $axChatLeadsQuery    = clone $query;
        $axContactLeadsQuery = clone $query;
        $otherLeadsQuery     = clone $query;

        $dialmydataLeads = $dialmydata->where("sources.name", "Dialmydata")->get()->toArray();
        $axChatLeads     = $axChatLeadsQuery->where("sources.name", "AX CHAT")->get()->toArray();
        $axContactLeads  = $axContactLeadsQuery->where("sources.name", "AX CONTACTS")->get()->toArray();
        $otherLeads      = $otherLeadsQuery->whereNotIn("sources.name", ["Dialmydata", "AX CHAT", "AX CONTACT"])->get()->toArray();

        $dates     = $this->getStartAndEndDate($date);
        $startDate = $dates["start_date"];
        $endDate   = $dates["end_date"];
        $period    = CarbonPeriod::create($startDate, $endDate);

        $data = [];
        foreach ($period as $date) {
            $formattedDate = $date->toDateString();
            $index         = array_search($formattedDate, array_column($dialmydataLeads, "date"));
            if ($index !== false) {
                $data["dialmydata"]["dates"][]        = $formattedDate;
                $data["dialmydata"]["dialmydatas"][]  = $dialmydataLeads[$index]["dialmydata"];
                $data["dialmydata"]["ax_chats"][]     = $dialmydataLeads[$index]["ax_chat"];
                $data["dialmydata"]["ax_contacts"][]  = $dialmydataLeads[$index]["ax_contact"];
                $data["dialmydata"]["other_counts"][] = $dialmydataLeads[$index]["other_count"];
            } else {
                $data["dialmydata"]["dates"][]        = $formattedDate;
                $data["dialmydata"]["dialmydatas"][]  = 0;
                $data["dialmydata"]["ax_chats"][]     = 0;
                $data["dialmydata"]["ax_contacts"][]  = 0;
                $data["dialmydata"]["other_counts"][] = 0;
            }

            $index = array_search($formattedDate, array_column($axChatLeads, "date"));
            if ($index !== false) {
                $data["axchat"]["dates"][]        = $formattedDate;
                $data["axchat"]["dialmydatas"][]  = $axChatLeads[$index]["dialmydata"];
                $data["axchat"]["ax_chats"][]     = $axChatLeads[$index]["ax_chat"];
                $data["axchat"]["ax_contacts"][]  = $axChatLeads[$index]["ax_contact"];
                $data["axchat"]["other_counts"][] = $axChatLeads[$index]["other_count"];
            } else {
                $data["axchat"]["dates"][]        = $formattedDate;
                $data["axchat"]["dialmydatas"][]  = 0;
                $data["axchat"]["ax_chats"][]     = 0;
                $data["axchat"]["ax_contacts"][]  = 0;
                $data["axchat"]["other_counts"][] = 0;
            }

            $index = array_search($formattedDate, array_column($axContactLeads, "date"));
            if ($index !== false) {
                $data["axcontact"]["dates"][]        = $formattedDate;
                $data["axcontact"]["dialmydatas"][]  = $axContactLeads[$index]["dialmydata"];
                $data["axcontact"]["ax_chats"][]     = $axContactLeads[$index]["ax_chat"];
                $data["axcontact"]["ax_contacts"][]  = $axContactLeads[$index]["ax_contact"];
                $data["axcontact"]["other_counts"][] = $axContactLeads[$index]["other_count"];
            } else {
                $data["axcontact"]["dates"][]        = $formattedDate;
                $data["axcontact"]["dialmydatas"][]  = 0;
                $data["axcontact"]["ax_chats"][]     = 0;
                $data["axcontact"]["ax_contacts"][]  = 0;
                $data["axcontact"]["other_counts"][] = 0;
            }

            $index = array_search($formattedDate, array_column($otherLeads, "date"));
            if ($index !== false) {
                $data["other"]["dates"][]        = $formattedDate;
                $data["other"]["dialmydatas"][]  = $otherLeads[$index]["dialmydata"];
                $data["other"]["ax_chats"][]     = $otherLeads[$index]["ax_chat"];
                $data["other"]["ax_contacts"][]  = $otherLeads[$index]["ax_contact"];
                $data["other"]["other_counts"][] = $otherLeads[$index]["other_count"];
            } else {
                $data["other"]["dates"][]        = $formattedDate;
                $data["other"]["dialmydatas"][]  = 0;
                $data["other"]["ax_chats"][]     = 0;
                $data["other"]["ax_contacts"][]  = 0;
                $data["other"]["other_counts"][] = 0;
            }
        }
        // dd($data);
        return $data;
    }
    private function userReports(Request $request, $date)
    {
        $superAdmin  = auth()->user()->hasRole(['Super Admin']);
        $notAdmin    = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        $users = User::query();
        $this->applyUserDashboardFilter($users, $request);
        $users = $users->when($notAdmin, fn($query) => $query->whereIn("id", $childrenIds))
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })
            ->withCount('suspensions') // Assuming the name of the relationship is 'suspensions'
            ->orderByDesc('suspensions_count')
            ->get();

        $agents_data                       = [];
        $primary_agents_message_suspension = '';
        $primary_agents_message            = '';
        foreach ($users as $agent) {
            if ($agent->suspensions->count() > 2) {
                $primary_agents_message_suspension .= '  ' . $agent->full_name . ": " . $agent->suspensions->count() . "\n";
            }

            $lastEventDate      = $agent->events()->max('created_at');
            $daysSinceLastEvent = $lastEventDate ? now()->diffInDays($lastEventDate) : now()->diffInDays($agent->created_at);

            if ($daysSinceLastEvent > 7) {
                $agents_data[] = [
                    'name'        => $agent->full_name,
                    'days'        => $daysSinceLastEvent,
                    'image'       => $agent->thumb_url,
                    'bitrix_role' => $agent->bitrix_role,
                    'manager'     => $agent->parent ? $agent->parent->full_name : 'No Manager',
                ];
            }
        }

        usort($agents_data, function ($a, $b) {
            return $b['days'] - $a['days'];
        });
        $agents_data = collect($agents_data);

        $incompleteLeadsUsers = User::query();
        $this->applyUserDashboardFilter($incompleteLeadsUsers, $request);
        $incompleteLeadsUsers = $incompleteLeadsUsers->when($notAdmin, fn($query) => $query->whereIn("id", $childrenIds))
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })
            ->withCount([
                'leads' => function ($query) use ($request, $superAdmin, $notAdmin, $childrenIds, $date) {
                    $query->where(function ($query) use ($date) {
                        $query->where('first_name', 'LIKE', '%new%')->orWhereNull('first_name');
                    })
                        ->whereNull('converted_to_listing_lead')
                        ->whereNotIn('lead_status_id', [4, 12])
                        ->when($request->boolean("deletedLeads"), fn($q1) => $q1->withTrashed())
                        ->whereNotNull("agent_id")
                        ->when($notAdmin && (! auth()->user()->hasPermissionTo("Lead Pool List")), function ($q) use ($childrenIds) {
                            $q->whereIn("agent_id", $childrenIds);
                        })
                        ->when($notAdmin && auth()->user()->hasPermissionTo("Lead Pool List"), function ($q) use ($childrenIds) {
                            $q->where(function ($q) use ($childrenIds) {
                                $q->whereIn("agent_id", $childrenIds)->whereNull("converted_to_listing_lead");
                            });
                        })
                        ->when(auth()->user()->hasRole("Admin - Abu Dhabi"), function ($q) {
                            $q->where("region_id", 2);
                        })
                        ->when(! ($request->boolean("archivedLeads") && $superAdmin), fn($q) => $q->where("status", true)->where("is_archive", false))
                        ->whereNull('property_id')
                        ->whereDate('created_at', '>', '2024-01-15');
                    $this->applyDateFilter($query, "leads.created_at", $date);
                },
            ])
            ->orderBy('leads_count', 'desc')
            ->get();

        $newStatusLead = User::query();
        $this->applyUserDashboardFilter($newStatusLead, $request);
        $newStatusLead = $newStatusLead->when($notAdmin, fn($query) => $query->whereIn("id", $childrenIds))
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })
            ->withCount([
                'leads' => function ($query) use ($request, $superAdmin, $notAdmin, $childrenIds, $date) {
                    $query->where('lead_status_id', 1)
                        ->when($request->boolean("deletedLeads"), fn($q1) => $q1->withTrashed())
                        ->whereNotNull("agent_id")
                        ->when($notAdmin && (! auth()->user()->hasPermissionTo("Lead Pool List")), function ($q) use ($childrenIds) {
                            $q->whereIn("agent_id", $childrenIds);
                        })
                        ->when($notAdmin && auth()->user()->hasPermissionTo("Lead Pool List"), function ($q) use ($childrenIds) {
                            $q->where(function ($q) use ($childrenIds) {
                                $q->whereIn("agent_id", $childrenIds)->whereNull("converted_to_listing_lead");
                            });
                        })
                        ->when(auth()->user()->hasRole("Admin - Abu Dhabi"), function ($q) {
                            $q->where("region_id", 2);
                        })
                        ->when(! ($request->boolean("archivedLeads") && $superAdmin), fn($q) => $q->where("status", true)->where("is_archive", false));
                    $this->applyDateFilter($query, "leads.created_at", $date);
                },
            ])
            ->orderBy('leads_count', 'desc')
            ->get();

        $hours = 24;

        $sourceAgentIds = User::query();
        $this->applyUserDashboardFilter($sourceAgentIds, $request);
        $sourceAgentIds       = $sourceAgentIds->when($notAdmin, fn($query) => $query->whereIn("id", $childrenIds))->pluck("id");
        $sourceWithLeadsCount = Source::whereHas("leads", function ($query) use ($request, $superAdmin, $notAdmin, $childrenIds, $date, $sourceAgentIds, $hours) {
            $query->where('created_at', '>=', now()->subHours($hours))
                ->whereIn('agent_id', $sourceAgentIds)
                ->whereNull('converted_to_listing_lead')
                ->when($request->boolean("deletedLeads"), fn($q1) => $q1->withTrashed())
                ->whereNotNull("agent_id")
                ->when($notAdmin && (! auth()->user()->hasPermissionTo("Lead Pool List")), function ($q) use ($childrenIds) {
                    $q->whereIn("agent_id", $childrenIds);
                })
                ->when($notAdmin && auth()->user()->hasPermissionTo("Lead Pool List"), function ($q) use ($childrenIds) {
                    $q->where(function ($q) use ($childrenIds) {
                        $q->whereIn("agent_id", $childrenIds)->whereNull("converted_to_listing_lead");
                    });
                })
                ->when(auth()->user()->hasRole("Admin - Abu Dhabi"), function ($q) {
                    $q->where("region_id", 2);
                })
                ->when(! ($request->boolean("archivedLeads") && $superAdmin), fn($q) => $q->where("status", true)->where("is_archive", false));
            $this->applyDateFilter($query, "leads.created_at", $date);
        })->withCount(['leads as leads_count' => function ($query) use ($request, $superAdmin, $notAdmin, $childrenIds, $date, $sourceAgentIds, $hours) {
            $query->where('created_at', '>=', now()->subHours($hours))
                ->whereIn('agent_id', $sourceAgentIds)
                ->whereNull('converted_to_listing_lead')
                ->when($request->boolean("deletedLeads"), fn($q1) => $q1->withTrashed())
                ->whereNotNull("agent_id")
                ->when($notAdmin && (! auth()->user()->hasPermissionTo("Lead Pool List")), function ($q) use ($childrenIds) {
                    $q->whereIn("agent_id", $childrenIds);
                })
                ->when($notAdmin && auth()->user()->hasPermissionTo("Lead Pool List"), function ($q) use ($childrenIds) {
                    $q->where(function ($q) use ($childrenIds) {
                        $q->whereIn("agent_id", $childrenIds)->whereNull("converted_to_listing_lead");
                    });
                })
                ->when(auth()->user()->hasRole("Admin - Abu Dhabi"), function ($q) {
                    $q->where("region_id", 2);
                })
                ->when(! ($request->boolean("archivedLeads") && $superAdmin), fn($q) => $q->where("status", true)->where("is_archive", false));
            $this->applyDateFilter($query, "leads.created_at", $date);
        }])
            ->where('status', true)
            ->orderByDesc('leads_count')
            ->get();

        $twoMonthsAgo = Carbon::now()->subMonths(2);

        $noDealAgents = User::query();
        $this->applyUserDashboardFilter($noDealAgents, $request);
        $noDealAgents = $noDealAgents->when($notAdmin, fn($query) => $query->whereIn("id", $childrenIds))
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })
            ->where('joining_date', '<', $twoMonthsAgo)
            ->whereNull('last_deal_date')
            ->orderBy('joining_date', 'desc')
            ->get();

        $dealDaysCount = User::query();
        $this->applyUserDashboardFilter($dealDaysCount, $request);
        $dealDaysCount = $dealDaysCount->where('is_report_exempt', 0)
            ->when($notAdmin, fn($query) => $query->whereIn("id", $childrenIds))
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })
            ->where('joining_date', '<=', now()->subDays(60))
            ->orderBy('joining_date', 'desc')
            ->get();

        $dealDaysCount = $dealDaysCount->map(function ($agent) {
            $daysSinceLastDeal           = now()->diffInDays($agent->last_deal_date ?? $agent->joining_date ?? $agent->created_at);
            $agent->days_since_last_deal = $daysSinceLastDeal;
            return $agent;
        });
        $sorted_deals_days_count = $dealDaysCount->sortByDesc('days_since_last_deal');

        return [
            'users'                   => $users,
            'agents'                  => $agents_data,
            'incopmlete_leads_users'  => $incompleteLeadsUsers,
            'new_status_lead'         => $newStatusLead,
            'sourceWithLeadsCount'    => $sourceWithLeadsCount,
            'no_deal_agents'          => $noDealAgents,
            'sorted_deals_days_count' => $sorted_deals_days_count,
            'hours'                   => $hours,
        ];
    }

    public function index(Request $request)
    {
        // abort_if(Gate::denies("Dashboard V2"), 403);
        $dates = $this->getStartAndEndDate($request->date, "d-m-Y");

        $startDate = $dates["start_date"];
        $endDate   = $dates["end_date"];

        $dashboards = [
            [
                "name"  => "All",
                "value" => "all",
                "route" => route("root.v2", ["date" => $request->date, "inactiveUsers" => $request->inactiveUsers, "archivedLeads" => $request->archivedLeads, "deletedLeads" => $request->deletedLeads]),
            ],
            // [
            //     "name"  => "Primary",
            //     "value" => "primary",
            //     "route" => route("root.v2", ["dashboard" => "primary", "date" => $request->date, "inactiveUsers" => $request->inactiveUsers, "archivedLeads" => $request->archivedLeads, "deletedLeads" => $request->deletedLeads]),
            // ],
            [
                "name"  => "Secondary",
                "value" => "secondary",
                "route" => route("root.v2", ["dashboard" => "secondary", "date" => $request->date, "inactiveUsers" => $request->inactiveUsers, "archivedLeads" => $request->archivedLeads, "deletedLeads" => $request->deletedLeads]),
            ],
        ];

        $groups = Group::with(["user"])
            ->whereNull("parent_id")
            ->whereIn("name", ["S1", "S2", "S3", "S4", "S5"])
            ->orderBy("order")
            ->get();

        foreach ($groups as $group) {
            $dashboards[] = [
                "name"  => "Secondary " . $group->name,
                "value" => "secondary-" . $group->name,
                "route" => route("root.v2", ["dashboard" => "secondary-" . $group->name, "date" => $request->date, "inactiveUsers" => $request->inactiveUsers, "archivedLeads" => $request->archivedLeads, "deletedLeads" => $request->deletedLeads]),
            ];
        }

        $dashboards[] = [
            "name"  => "Abu Dhabi",
            "value" => "abu-dhabi",
            "route" => route("root.v2", ["dashboard" => "abu-dhabi", "date" => $request->date, "inactiveUsers" => $request->inactiveUsers, "archivedLeads" => $request->archivedLeads, "deletedLeads" => $request->deletedLeads]),
        ];

        // $dashboards[] = [
        //     "name"  => "Chinese",
        //     "value" => "chinese",
        //     "route" => route("root.v2", ["dashboard" => "chinese", "date" => $request->date, "inactiveUsers" => $request->inactiveUsers, "archivedLeads" => $request->archivedLeads, "deletedLeads" => $request->deletedLeads]),
        // ];

        $notAdmin = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);

        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->user()->id);
        }
        $agents = User::whereHas("roles", function ($q) {
            $q->whereIn("name", ["Agent", "Line Manager", "Team Leader", "Sales Director", "Head of Secondary"]);
        })->when($notAdmin, fn($q) => $q->whereIn("parent_id", $childrenIds)->orWhere('id', auth()->user()->id))->where("status", true)->get();

        if ($request->dashboard == "abu-dhabi" || $this->checkIfSecondaryDashboard($request->dashboard)) {
            if ($request->dashboard == "abu-dhabi") {
                $zone = Group::where("name", "Abu Dhabi")->first();
                $zone->load("user");
                $selectedUserId = $zone->user->id ?? null;
            } else {
                $zoneId = $this->getSecondaryDashboardZoneId($request->dashboard);
                if (! is_null($zoneId)) {
                    $zone = Group::find($zoneId);
                    $zone->load("user");
                    $selectedUserId = $zone->user->id ?? null;
                } else {
                    $selectedUserId = auth()->id();
                }
            }
        } else {
            $selectedUserId = auth()->id();
        }

        return view(
            'admin.dashboard.v2.index',
            compact(
                "dashboards",
                "startDate",
                "endDate",
                "groups",
                "agents",
                "selectedUserId"
            )
        );
    }

    public function getView(Request $request): View | null
    {
        $notAdmin    = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        if ($request->type == "counters") {
            $listingsCount = $this->prepareListingsData(request: $request, date: $request->date);
            $leadsCount    = $this->prepareLeadsData(request: $request, date: $request->date);
            $dealsCount    = $this->prepareDealsData(isAdmin: ! $notAdmin, userIds: $childrenIds, date: $request->date, dashboard: $request->dashboard, inactiveUsersAlso: $request->inactiveUsers);

            $dates = $this->getStartAndEndDate($request->date, "d-m-Y");

            $startDate = $dates["start_date"];
            $endDate   = $dates["end_date"];
            $noOfDays  = Carbon::parse($startDate)->diffInDays($endDate);
            return view(
                'admin.dashboard.v2.partials._counters',
                compact(
                    "listingsCount",
                    "leadsCount",
                    "dealsCount",
                    "noOfDays"
                )
            );
        }

        if ($request->type == "portalListings") {
            $propertyFinderData = $this->preparePropertyFinderData(request: $request, date: $request->date);

            $bayutData = $this->prepareBayutData(request: $request, date: $request->date);
            // dd($bayutData);
            $dubizzleData = $this->prepareDubizzleData(request: $request, date: $request->date);
            return view(
                'admin.dashboard.v2.partials._portal_listings',
                compact(
                    "propertyFinderData",
                    "bayutData",
                    "dubizzleData"
                )
            );
        }

        if ($request->type == "agentInsights") {
            $insights = [
                "high_and_least_no_of_activities" => $this->prepareHighAndLeastNoOfActivityData(request: $request, date: $request->date),
                "highest_no_of_leads"             => $this->prepareHighestNoOfLeadData(request: $request, date: $request->date),
                "highest_no_of_listings"          => $this->prepareHighestNoOfListingData(request: $request, date: $request->date),
                "highest_no_of_quality_scores"    => $this->prepareHighestNoOfQualityScoreData(request: $request, date: $request->date),
            ];
            return view(
                'admin.dashboard.v2.partials._agent_insights',
                compact(
                    "insights"
                )
            );
        }

        if ($request->type == "callLogs") {
            $calls = $this->periodsWeekDayStatistics(request: $request);
            return view('admin.dashboard.v2.partials._call_logs', [
                'report'    => $calls['report'],
                'callTypes' => $calls['callTypes'],
            ]);
        }
        if ($request->type == "callLogsAgents") {
            $calls = $this->getLast7DaysUserReport(request: $request);
            return view('admin.dashboard.v2.partials._call_logs_agents', [
                'report'    => $calls['report'],
                'callTypes' => $calls['callTypes'],
            ]);
        }

        if ($request->type == "callConnections") {
            $calls = $this->getCallConnectionReport(request: $request);
            // dd($calls);
            return view('admin.dashboard.v2.partials._call_connections_agents', [
                'report' => $calls['formattedReport'],
            ]);
        }

        if ($request->type == "callConnectionsTime") {
            $calls = $this->getCallConnectionReportWithAverageTime(request: $request);
            // dd($calls);
            return view('admin.dashboard.v2.partials._call_connections_average_agents', [
                'report' => $calls['formattedReport'],
            ]);
        }
        if ($request->type == "leadStatusReport") {
            $calls = $this->getLeadStatusReport(request: $request);
            return view('admin.dashboard.v2.partials._lead_status_report', [
                'report' => $calls['formattedReport'],
            ]);
        }
        if ($request->type == "listingLeadStatusReport") {
            $calls = $this->getListingLeadStatusReport(request: $request);
            return view('admin.dashboard.v2.partials._listing_lead_status_report', [
                'report' => $calls['formattedReport'],
            ]);
        }

        if ($request->type == "listingStatusReport") {
            $calls = $this->getListingStatusReport(request: $request);
            return view('admin.dashboard.v2.partials._listing_status_report', [
                'report' => $calls['formattedReport'],
            ]);
        }
        if ($request->type == "eventTypesReport") {
            $calls = $this->getEventStatusReport(request: $request);
            return view('admin.dashboard.v2.partials._event_type_report', [
                'report' => $calls['formattedReport'],
            ]);
        }

        if ($request->type == "leadInsights") {
            $insightData = [

                "overview_source_wise"                 => $this->prepareLeadOverviewSourceWiseData(request: $request, date: $request->date),
                "date_wise_source_wise_counts"         => $this->prepareSourceWiseDateWiseLeadCountData(request: $request, date: $request->date),
                "overview"                             => $this->prepareLeadOverviewData(request: $request, date: $request->date),
                "date_wise_counts"                     => $this->prepareDateWiseLeadCountData(request: $request, date: $request->date),

                "listing_overview_source_wise"         => $this->prepareListingLeadOverviewSourceWiseData(request: $request, date: $request->date),
                "listing_date_wise_source_wise_counts" => $this->prepareSourceWiseDateWiseListingLeadCountData(request: $request, date: $request->date),

            ];
            return view(
                'admin.dashboard.v2.partials._lead_insights',
                compact(
                    "insightData"
                )
            );
        }

        if ($request->type == "performanceReports") {
            $performanceReportData   = $this->userReports(request: $request, date: $request->date);
            $users                   = $performanceReportData["users"];
            $agents                  = $performanceReportData["agents"];
            $incomplete_lead_agents  = $performanceReportData["incopmlete_leads_users"];
            $new_status_lead         = $performanceReportData["new_status_lead"];
            $sourceWithLeadsCount    = $performanceReportData["sourceWithLeadsCount"];
            $no_deal_agents          = $performanceReportData["no_deal_agents"];
            $sorted_deals_days_count = $performanceReportData["sorted_deals_days_count"];
            $hours                   = $performanceReportData["hours"];
            return view(
                'admin.dashboard.v2.partials._performance_reports',
                compact(
                    "users",
                    "agents",
                    "incomplete_lead_agents",
                    "new_status_lead",
                    "sourceWithLeadsCount",
                    "no_deal_agents",
                    "sorted_deals_days_count",
                    "hours",
                )
            );
        }
        return null;
    }

    public function getData(Request $request)
    {
        $data = null;

        if ($request->type == "no_of_activity") {
            $data = $this->prepareHighAndLeastNoOfActivityData(request: $request, date: $request->date, limit: null);
        }

        if ($request->type == "no_of_leads") {
            $data = $this->prepareHighestNoOfLeadData(request: $request, date: $request->date, limit: null);
        }

        if ($request->type == "no_of_listings") {
            $data = $this->prepareHighestNoOfListingData(request: $request, date: $request->date, limit: null);
        }

        if ($request->type == "no_of_quality_scores") {
            $data = $this->prepareHighestNoOfQualityScoreData(request: $request, date: $request->date, limit: null);
        }

        return response([
            "status" => true,
            "data"   => $data,
        ]);
    }

    public function agentListingReport(Request $request)
    {
        $request->validate([
            'user_id'   => 'nullable',
            'portal_id' => 'nullable',
        ]);

        if (! empty($request->user_id)) {
            $agent = User::find($request->user_id);
        } else {
            $agent = User::find(auth()->id());
        }

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

        $agentIds    = $agents->pluck('id')->toArray();
        $locationIds = $managers_locations->pluck('parentable_id')->toArray();

        $agentLocationCounts = [];

        $users     = User::whereIn('id', $agentIds)->get();
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

                $count                                            = $query->count();
                $location_name                                    = $location->id . '_' . $location->name;
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
        return view('admin.dashboard.v2.partials._area_wise_listing_report', [
            'agentLocationCounts' => $agentLocationCounts,
            'agents'              => $agents,
            'locations'           => $locations,
            'status'              => $request->status,
            'portals'             => Portal::where('status', 1)->get(),
            'selected_portal'     => $request->portal ?? null,
        ]);
    }

    public function agentListingReportSubCommunity(Request $request)
    {
        $request->validate([
            'agent_id'      => 'required',
            'portal_id'     => 'nullable',
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

        $agents   = $agentsQuery->get();
        $agentIds = $agents->pluck('id')->toArray();

        $community = Community::where('name', $request->location_name)->orWhere('alternate_name', $request->location_name)->first();

        $managers_locations = SubCommunity::where('community_id', $community->id)->get();

        $agentLocationCounts    = [];
        $agentLocationCountsSub = [];

        $locationIds = $managers_locations->pluck('id')->toArray();

        $users              = User::whereIn('id', $agentIds)->get();
        $locations          = SubCommunity::whereIn('id', $locationIds)->get();
        $community_property = CommunityProperty::where('community_id', $community->id)->get();

        $counter = 0;

        foreach ($users as $user) {
            foreach ($locations as $location) {
                $query = Property::where('agent_id', $user->id)->where('community_id', $community->id)
                    ->where('sub_community_id', $location->id)->whereNull('community_property_id');
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
                $counter += $count;
                $location_name = $location->id . '_' . $location->name;

                $agentLocationCounts[$user->name][$location_name] = $count;
            }
        }
        $counter_2 = 0;
        foreach ($users as $user) {
            foreach ($community_property as $location_cp) {

                $query = Property::where('agent_id', $user->id)->where('community_id', $community->id)->where('community_property_id', $location_cp->id);
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
                $counter_2 += $count;
                $location_cp_name                                       = $location_cp->id . '_' . $location_cp->name;
                $agentLocationCountsSub[$user->name][$location_cp_name] = $count;
            }
        }

        return view('admin.dashboard.v2.partials._area_wise_listing_report_sub_community', [
            'agentLocationCounts'    => $agentLocationCounts,
            'agentLocationCountsSub' => $agentLocationCountsSub,
            'agents'                 => $agents,
            'locations'              => $locations,
            'community_property'     => $community_property,
            'status'                 => $request->status,
            'portals'                => Portal::where('status', 1)->get(),
            'selected_portal'        => $request->portal ?? null,
            'location_name'          => $request->location_name,
        ]);
    }

    public function periodsWeekDayStatistics(Request $request)
    {
        $notAdmin    = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        // Define the date range for the last 7 days
        $startDate = Carbon::now()->subDays(6)->startOfDay();
        $endDate   = Carbon::now()->endOfDay();

        // Get logs for the last 7 days grouped by date and call_type
        $last7DaysReport = CallLog::whereBetween('created_at', [$startDate, $endDate])
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("user_id", $childrenIds);
            })
            ->select(DB::raw('DATE(created_at) as date'), 'call_type', DB::raw('count(*) as total'))
            ->groupBy('date', 'call_type')
            ->orderBy('date')
            ->get();

        // Format the data into a structured associative array
        $formattedReport = [];
        $callTypes       = [];

        foreach ($last7DaysReport as $log) {
            $date = $log->date;
            $type = $log->call_type;

            // Add call type to the list of unique call types
            if (! in_array($type, $callTypes)) {
                $callTypes[] = $type;
            }

            // Organize data by date and call type
            $formattedReport[$date][$type] = $log->total;
        }

        // Prepare final report with all call types initialized to zero
        $dates  = collect(range(0, 6))->map(fn($day) => Carbon::now()->subDays($day)->toDateString())->reverse()->values();
        $report = [];

        foreach ($dates as $date) {
            foreach ($callTypes as $type) {
                $report[$date][$type] = $formattedReport[$date][$type] ?? 0;
            }
        }
        return [
            'dates'     => $dates,
            'report'    => $report,
            'callTypes' => $callTypes,
        ];
    }

    public function getLast7DaysUserReport(Request $request)
    {

        $notAdmin    = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        if ($request->date) {
            // date" => "01/11/2024 - 12/11/2024"
            $dates     = explode(" - ", $request->date);
            $startDate = Carbon::createFromFormat('d/m/Y', $dates[0])->startOfDay();
            $endDate   = Carbon::createFromFormat('d/m/Y', $dates[1])->endOfDay();
        } else {
            $startDate = Carbon::now()->subDays(6)->startOfDay();
            $endDate   = Carbon::now()->endOfDay();
        }

        // Retrieve logs for the last 7 days with user name, grouped by user name and call_type
        $last7DaysReport = CallLog::whereBetween('created_at', [$startDate, $endDate])
            ->with('user') // Eager load the user relationship
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("user_id", $childrenIds);
            })
            ->select('user_id', 'call_type', DB::raw('count(*) as total'))
            ->groupBy('user_id', 'call_type')
            ->orderBy('user_id')
            ->get();

        // Format the result as an associative array with user names as top-level keys
        $formattedReport = [];
        $callTypes       = [];

        foreach ($last7DaysReport as $log) {
            $userName = $log->user->full_name ?? '-'; // Get the user's name from the relationship
            $type     = $log->call_type;

            // Collect unique call types for chart labels
            if (! in_array($type, $callTypes)) {
                $callTypes[] = $type;
            }

            // Organize data by user name and call type
            $formattedReport[$userName][$type] = $log->total;
        }

        // Prepare the final report with missing call types initialized to zero
        $report = [];
        foreach ($formattedReport as $userName => $calls) {
            foreach ($callTypes as $type) {
                $report[$userName][$type] = $calls[$type] ?? 0;
            }
        }

        return [
            'report'    => $report,
            'callTypes' => $callTypes,
        ];
    }

    public function getCallConnectionReport(Request $request)
    {
        $notAdmin    = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        if ($request->date) {
            // date" => "01/11/2024 - 12/11/2024"
            $dates     = explode(" - ", $request->date);
            $startDate = Carbon::createFromFormat('d/m/Y', $dates[0])->startOfDay();
            $endDate   = Carbon::createFromFormat('d/m/Y', $dates[1])->endOfDay();
        } else {
            $startDate = Carbon::now()->subDays(6)->startOfDay();
            $endDate   = Carbon::now()->endOfDay();
        }

        // Retrieve and categorize calls as connected or missed
        $callConnectionReport = CallLog::whereBetween('created_at', [$startDate, $endDate])
            ->with('user') // Eager load the user relationship
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("user_id", $childrenIds);
            })
            ->select(
                'user_id',
                DB::raw("SUM(CASE WHEN call_duration > 0 THEN 1 ELSE 0 END) as connected_calls"),
                DB::raw("SUM(CASE WHEN call_duration = 0 OR call_duration IS NULL THEN 1 ELSE 0 END) as missed_calls")
            )
            ->groupBy('user_id')
            ->get();

        // Format the data with user names
        $formattedReport = [];
        foreach ($callConnectionReport as $log) {
            $userName                   = $log->user->full_name ?? '-'; // Access the user's name through the relationship
            $formattedReport[$userName] = [
                'connected_calls' => $log->connected_calls,
                'missed_calls'    => $log->missed_calls,
            ];
        }
        return [
            'formattedReport' => $formattedReport,
        ];
    }

    public function getCallConnectionReportWithAverageTime(Request $request)
    {
        $notAdmin    = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        if ($request->date) {
            $dates     = explode(" - ", $request->date);
            $startDate = Carbon::createFromFormat('d/m/Y', $dates[0])->startOfDay();
            $endDate   = Carbon::createFromFormat('d/m/Y', $dates[1])->endOfDay();
        } else {
            $startDate = Carbon::now()->subDays(6)->startOfDay();
            $endDate   = Carbon::now()->endOfDay();
        }

        // Retrieve and categorize calls with connected and missed counts, and calculate average duration
        $callConnectionReport = CallLog::whereBetween('created_at', [$startDate, $endDate])
            ->with('user') // Eager load the user relationship
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("user_id", $childrenIds);
            })
            ->select(
                'user_id',
                DB::raw("SUM(CASE WHEN call_duration > 0 THEN 1 ELSE 0 END) as connected_calls"),
                DB::raw("SUM(CASE WHEN call_duration = 0 OR call_duration IS NULL THEN 1 ELSE 0 END) as missed_calls"),
                DB::raw("AVG(TIMESTAMPDIFF(SECOND,
                COALESCE(call_started_at, created_at),
                call_ended_at)) as average_duration_seconds") // Calculate average duration in seconds
            )
            ->groupBy('user_id')
            ->get();

        // Format the data with user names
        $formattedReport = [];
        foreach ($callConnectionReport as $log) {
            $userName                   = $log->user->full_name ?? '-'; // Access the user's name through the relationship
            $formattedReport[$userName] = [
                'connected_calls'          => $log->connected_calls,
                'missed_calls'             => $log->missed_calls,
                'average_duration_seconds' => round($log->average_duration_seconds, 2), // Round to 2 decimal places
            ];
        }
        return [
            'formattedReport' => $formattedReport,
        ];
    }

    public function getLeadStatusReport(Request $request)
    {
        $notAdmin    = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        if ($request->date) {
            $dates     = explode(" - ", $request->date);
            $startDate = Carbon::createFromFormat('d/m/Y', $dates[0])->startOfDay();
            $endDate   = Carbon::createFromFormat('d/m/Y', $dates[1])->endOfDay();
        } else {
            $startDate = Carbon::now()->subDays(6)->startOfDay();
            $endDate   = Carbon::now()->endOfDay();
        }

        // Get lead counts grouped by user and status
        $leadStatusReport = Lead::with('agent')
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })
            ->where('status', true)
            ->where('is_archive', false)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereHas('agent', function ($query) {
                $query->where('status', true);
            })
            ->select(
                'agent_id',
                'lead_status_id',
                DB::raw("COUNT(*) as lead_count") // Count leads per status
            )
            ->groupBy('agent_id', 'lead_status_id')
            ->get();

        // Organize the data by user and status
        $reportData = [];
        foreach ($leadStatusReport as $lead) {
            $userName    = $lead->agent->full_name ?? '-'; // Access user's name
            $status      = $lead->lead_status_id;
            $statusTitle = $lead->leadStatus->name ?? 'Unknown Status'; // Use the lead status title or a default

            // Initialize user's data array if not already present
            if (!isset($reportData[$userName])) {
                $reportData[$userName] = [];
            }
            $reportData[$userName][$statusTitle] = $lead->lead_count;
        }
        foreach ($reportData as $key => $value) {
            $total = 0;
            foreach ($value as $k => $v) {
                $total += $v;
            }
            $reportData[$key]['Total'] = $total;
        }
        return [
            'formattedReport' => $reportData,
        ];
    }

    public function getListingLeadStatusReport(Request $request)
    {
        $notAdmin    = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        if ($request->date) {
            $dates     = explode(" - ", $request->date);
            $startDate = Carbon::createFromFormat('d/m/Y', $dates[0])->startOfDay();
            $endDate   = Carbon::createFromFormat('d/m/Y', $dates[1])->endOfDay();
        } else {
            $startDate = Carbon::now()->subDays(6)->startOfDay();
            $endDate   = Carbon::now()->endOfDay();
        }

        // Get lead counts grouped by user and status
        $leadStatusReport = ListingLead::with('agent')
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })
            ->where('is_archive', false)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereHas('agent', function ($query) {
                $query->where('status', true);
            })
            ->select(
                'agent_id',
                'status_id',
                DB::raw("COUNT(*) as lead_count") // Count leads per status
            )
            ->groupBy('agent_id', 'status_id')
            ->get();

        // Organize the data by user and status
        $reportData = [];
        foreach ($leadStatusReport as $lead) {
            $userName    = $lead->agent->full_name ?? '-'; // Access user's name
            $status      = $lead->status_id;
            $statusTitle = $lead->status->name ?? 'Unknown Status'; // Use the lead status title or a default

            // Initialize user's data array if not already present
            if (! isset($reportData[$userName])) {
                $reportData[$userName] = [];
            }
            $reportData[$userName][$statusTitle] = $lead->lead_count;
        }
        return [
            'formattedReport' => $reportData,
        ];
    }

    public function getListingStatusReport(Request $request)
    {
        $notAdmin    = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        if ($request->date) {
            $dates     = explode(" - ", $request->date);
            $startDate = Carbon::createFromFormat('d/m/Y', $dates[0])->startOfDay();
            $endDate   = Carbon::createFromFormat('d/m/Y', $dates[1])->endOfDay();
        } else {
            $startDate = Carbon::now()->subDays(6)->startOfDay();
            $endDate   = Carbon::now()->endOfDay();
        }

        $excludeAgents = [4, 307];

        $propertyStatusReport = Property::with('agent')
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotIn('agent_id', $excludeAgents)
            ->whereHas('agent', function ($query) {
                $query->where('status', true);
            })->select(
                'agent_id',
                'publish_status',
                DB::raw("COUNT(*) as property_count") // Count properties per publish status
            )
            ->groupBy('agent_id', 'publish_status')
            ->get();

        // Step 2: Organize data by agent name and publish status
        $reportData = [];
        foreach ($propertyStatusReport as $property) {
            $agentName = $property->agent->full_name ?? '-'; // Use agent's name or fallback to '-'
            $status    = $property->publish_status;

            // Initialize agent's data array if not already present
            if (! isset($reportData[$agentName])) {
                $reportData[$agentName] = [];
            }

            // Add the count for this publish status
            $reportData[$agentName][$status] = $property->property_count;
        }

        // Step 3: Pass data to the view
        return [
            'formattedReport' => $reportData,
        ];
    }

    public function getEventStatusReport(Request $request)
    {
        $notAdmin    = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        if ($request->date) {
            $dates     = explode(" - ", $request->date);
            $startDate = Carbon::createFromFormat('d/m/Y', $dates[0])->startOfDay();
            $endDate   = Carbon::createFromFormat('d/m/Y', $dates[1])->endOfDay();
        } else {
            $startDate = Carbon::now()->subDays(6)->startOfDay();
            $endDate   = Carbon::now()->endOfDay();
        }

        $excludeAgents = [4, 307];

        $eventTypeReport = Event::with('agent')
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotIn('agent_id', $excludeAgents)
            ->whereHas('agent', function ($query) {
                $query->where('status', true);
            })->select(
                'agent_id',
                'event_type_id',
                DB::raw("COUNT(*) as event_count") // Count properties per publish status
            )
            ->groupBy('agent_id', 'event_type_id')
            ->get();

        // Step 2: Organize data by agent name and publish status
        $reportData = [];
        foreach ($eventTypeReport as $event) {
            $agentName = $event->agent->full_name ?? '-'; // Use agent's name or fallback to '-'
            $type      = $event->event_type_id;
            $typeNames = $event->type->name ?? '-';

            // Initialize agent's data array if not already present
            if (! isset($reportData[$agentName])) {
                $reportData[$agentName] = [];
            }

            // Add the count for this publish status
            $reportData[$agentName][$typeNames] = $event->event_count;
        }

        // Step 3: Pass data to the view
        return [
            'formattedReport' => $reportData,
        ];
    }
}
