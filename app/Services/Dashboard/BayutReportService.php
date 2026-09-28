<?php

namespace App\Services\Dashboard;


use App\Models\Portal;
use App\Models\Property;
use App\Models\PropertyMeta;
use App\Models\PortalMeta;
use App\Models\User;
use App\Models\UserMeta;
use App\Models\UserThirdPartyData;

class BayutReportService
{
    public function __construct()
    {

    }

    public function summary($request, $params)
    {

        $portal = Portal::where('name', 'Bayut')->first();
        $agents = [];
        if ($params['isSuperAdmin']) {
            $agents = User::where('status', true)->whereHas("roles", fn($q) => $q->whereIn("name", ["Agent"]))->pluck('name', 'id');
            $user_ids = $agents->keys()->toArray();
            $params['user_ids'] = $user_ids;
        }
        $agent_wise_listings = $this->agentWiseListingsBayut($portal, $params, $agents);
        $listing_count_by_status = $this->getListingCountsByStatusBayut($portal, $params, $agents);
        $counters = $this->getDataCountsBayut($portal, $params);
        $data = [
            'overview' => [
                'quality_score' => $this->getBayutListingQualityScore($portal, $params),
            ],
            'counter' => $counters,
            'agent_wise_listings' => $agent_wise_listings,
            'listing_counts_by_status' => $listing_count_by_status
        ];
        return $data;
    }

    public function getDataCountsBayut($portal, $params): array
    {
        $last_updated = PortalMeta::where('portal_id', $portal->id)->where('meta_key', 'bayut_basic_listings')->orderBy('updated_at','DESC')->first()->updated_at ?? null;
        //$bayut_levels = $this->getPfSpecificListingCounts($portal, $params);
        $total_tru_broker_agents = (int)PortalMeta::getPortalMeta($portal->id, 'bayut_tru_brokers_count', $params['start_date']) ?? 0;
        $total_active_agents = (int)PortalMeta::getPortalMeta($portal->id, 'bayut_total_active_agents', $params['start_date']) ?? 0;
        $counters['listings_live'] = $this->getListingCounts($portal, $params);
        $counters['listing_truChecked'] = (int)PortalMeta::getPortalMeta($portal->id, 'bayut_tru_checked_listings', $params['start_date']) ?? 0;
        $counters['listing_checked'] = (int)PortalMeta::getPortalMeta($portal->id, 'bayut_checked_listings', $params['start_date']) ?? 0;
        $counters['active_listings'] = (int)PortalMeta::getPortalMeta($portal->id, 'bayut_active_listings', $params['start_date']) ?? 0;
        
      //  $counters['listing_other'] =  $counters['listings_live'] - $counters['listing_truChecked'] - $counters['listing_checked'];
        $counters['listing_other'] =  $counters['active_listings'] - $counters['listing_truChecked'] - $counters['listing_checked'];
        $counters['signature'] = (int)PortalMeta::getPortalMeta($portal->id, 'bayut_signature_listings', $params['start_date']) ?? 0;
        $counters['hot'] = (int)PortalMeta::getPortalMeta($portal->id, 'bayut_hot_listings', $params['start_date']) ?? 0;
        $counters['basic'] = (int)PortalMeta::getPortalMeta($portal->id, 'bayut_basic_listings', $params['start_date']) ?? 0;
        $counters['active_sale_listings'] = (int)PortalMeta::getPortalMeta($portal->id, 'bayut_active_sale_listings', $params['start_date']) ?? 0;
        $counters['active_rental_listings'] = (int)PortalMeta::getPortalMeta($portal->id, 'bayut_active_rental_listings', $params['start_date']) ?? 0;
        $counters['agents_total'] = $total_active_agents - $total_tru_broker_agents;
        $counters['agents_truBroker'] = $total_tru_broker_agents;
        $counters['last_updated_at'] =  $last_updated ? $last_updated->format('Y-m-d H:i A') : null;
        return $counters;

    }

    public function getTruBrokerAgentCount($params): int
    {
        $user_ids = $params['user_ids'];
        $start_date = $params['start_date'] ?? date('Y-m-d');
        $end_date = $params['end_date'] ?? date('Y-m-d');

        return UserThirdPartyData::where('portal', 'bayut')
            ->when(($start_date != null && $end_date != null), function ($q) use ($start_date,$end_date) {
                $q->where('start_date', '>=', $start_date)->where('end_date', '<=', $end_date);
            })
            ->when($params['filter_by_users'], function ($q) use ($user_ids) {
                $q->whereIn('user_id', $user_ids);
            })->where('dump_meta->bayut_is_trubroker_badge_permitted_curr_month', '1')->count() ?? 0;
    }

    public function getBayutListingQualityScore($portal, $params): string
    {
        $user_ids = $params['user_ids'];

        $averageBayutRank = PropertyMeta::where('meta_key', 'bayut_rank')
            ->join('properties', 'property_meta.property_id', '=', 'properties.id')
            ->where('properties.is_live', true)
            ->when(!$params['isSuperAdmin'], function ($q) use ($user_ids) {
                $q->where(function ($q) use ($user_ids) {
                    $q->whereIn("properties.agent_id", $user_ids);
                });
            })
//            ->when(! empty($dates), function ($q) use ($dates) {
//                // Filter by created_at date range from the properties table
//                $q->whereDate('properties.created_at', '>=', $dates['startDate'])
//                    ->whereDate('properties.created_at', '<=', $dates['endDate']);
//            })
            ->avg('meta_value') ?? "0";

        return number_format($averageBayutRank, 2);

    }
    public function bayutVerfiedListingCount($portal,$params) : int
    {
       // $user_ids = $params['user_ids'];


    }

    public function getPfSpecificListingCounts($portal, $params): array
    {
        $user_ids = $params['user_ids'];
        $uniqueBayutLevels = ['Signature', 'Hot', 'Premium',];

        // Count the number of properties for each bayut_level
        $bayutLevelCounts = [];
        foreach ($uniqueBayutLevels as $bayutLevel) {
            $count = Property::whereHas('propertyMeta', function ($query) use ($bayutLevel) {
                $query->where('meta_key', 'bayut_level')
                    ->where('meta_value', $bayutLevel);
            })
                ->when(!$params['isSuperAdmin'], function ($q) use ($user_ids) {
                    $q->where(function ($q) use ($user_ids) {
                        $q->whereIn("agent_id", $user_ids);
                    });
                })
//                ->when(!empty($dates), function ($q) use ($dates) {
//                    $q->whereDate('properties.created_at', '>=', $dates['startDate'])->whereDate('properties.created_at', '<=', $dates['endDate']);
//                })
                ->where('is_live', true)
                ->count();

            $bayutLevelCounts[$bayutLevel] = $count;
        }
            // replace premium with Basic label
        $bayutLevelCounts['Basic'] = $bayutLevelCounts['Premium'];
        unset($bayutLevelCounts['Premium']);
        return $bayutLevelCounts;
    }

    public function agentWiseListingsBayut($portal, $params, $agents): array
    {
        $user_ids = $params['user_ids'];
        $listings = [];
        try {
            foreach ($agents as $user_id => $name) {
                $listings[] = [
                    'name' => $name,
                    'count' => Property::where('region_id', 1)
                            ->whereHas('portals', function ($q) use ($portal) {
                                $q->where('portal_id', $portal->id);
                            })->where('is_live', '1')
                            ->where("agent_id", $user_id)
                            ->count() ?? 0
                ];
            }
            return $listings;
        } catch (\Exception $e) {
            info('Error in BayutReportService agentWiseListings ' . json_encode($e->getMessage()));
            return [];
        }

    }

    public function getListingCounts($portal, $params): int
    {
        $user_ids = $params['user_ids'];
        return Property::where('region_id', 1)
            ->whereHas('portals', function ($q) use ($portal) {
                $q->where('portal_id', $portal->id);
            })->where('is_live', '1')
            ->when(!$params['isSuperAdmin'], function ($q) use ($user_ids) {
                $q->whereIn("agent_id", $user_ids);
            })->count() ?? 0;
    }

    public function getListingCountsByStatus($portal, $params, $agents): array
    {
        $user_ids = $params['user_ids'];
        $listings = [];
        $statuses = ['PENDING', 'DRAFT', 'UNPUBLISHED', 'PUBLISHED', 'ARCHIVED'];
        try {
            foreach ($statuses as $status) {
                $listings[$status] = Property::where('region_id', 1)
                    ->whereHas('portals', function ($q) use ($portal) {
                        $q->where('portal_id', $portal->id);
                    })
                    ->where('publish_status', $status)
                    ->whereIn("agent_id", $user_ids)
                    ->count();
            }
            return $listings;
        } catch (\Exception $e) {
            info('Error in BayutReportService agentWiseListings ' . json_encode($e->getMessage()));
            return [];
        }

    }

    public function getListingCountsByStatusBayut($portal, $params, $agents): array
    {
        $user_ids = $params['user_ids'];
        $listings = [];
        $statuses = ['PENDING', 'DRAFT', 'UNPUBLISHED', 'PUBLISHED', 'ARCHIVED'];
        try {
            foreach ($statuses as $status) {
                $listings[$status] = Property::where('region_id', 1)
                    ->whereHas('portals', function ($q) use ($portal) {
                        $q->where('portal_id', $portal->id);
                    })
                    ->where('publish_status', $status)
                    ->whereIn("agent_id", $user_ids)
                    ->count();
            }
            return $listings;
        } catch (\Exception $e) {
            info('Error in BayutReportService agentWiseListings ' . json_encode($e->getMessage()));
            return [];
        }

    }
}
