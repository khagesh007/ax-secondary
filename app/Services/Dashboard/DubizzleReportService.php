<?php

namespace App\Services\Dashboard;




use App\Models\Portal;
use App\Models\Property;
use App\Models\PropertyMeta;
use App\Models\PortalMeta;
use App\Models\User;

class DubizzleReportService
{

    public function __construct()
    {

    }
    public function summary($request, $params)
    {

        $portal = Portal::where('name', 'Dubizzle')->first();
        $agents = [];
        if ($params['isSuperAdmin']) {
            $agents = User::where('status', true)->whereHas("roles", fn($q) => $q->whereIn("name", ["Agent"]))->pluck('name', 'id');
            $user_ids = $agents->keys()->toArray();
            $params['user_ids'] = $user_ids;
        }
        $agent_wise_listings = $this->agentWiseListingsDubizzle($portal, $params, $agents);
        $listing_count_by_status = $this->getListingCountsByStatusBayut($portal, $params, $agents);
        $counters = $this->getDataCountsDubizzle($portal, $params);
        $data = [
            'overview' => [
                'quality_score' => $this->getDubizzleListingQualityScore($portal, $params),
            ],
            'counter' => $counters,
            'agent_wise_listings' => $agent_wise_listings,
            'listing_counts_by_status' => $listing_count_by_status
        ];
        return $data;
    }

    public function getDataCountsDubizzle($portal,$params) : array
    {
        $last_updated = PortalMeta::where('portal_id', $portal->id)->where('meta_key', 'dubizzle_basic_listings')->orderBy('updated_at','DESC')->first()->updated_at ?? null;
        $dubizzle_levels = $this->getDubizzleSpecificListingCounts($portal,$params);
        $active_listings = (int)PortalMeta::getPortalMeta($portal->id, 'dubizzle_active_listings', $params['start_date']) ?? 0;
        $verified_listings = (int)PortalMeta::getPortalMeta($portal->id, 'dubizzle_verified_listings', $params['start_date']) ?? 0;
        $counters['listings_live'] = $this->getListingCounts($portal,$params);
        $counters['listing_unverified'] = $active_listings - $verified_listings;
        $counters['listing_verified'] = $verified_listings;
        $counters['premium'] = (int)PortalMeta::getPortalMeta($portal->id, 'dubizzle_premium_listings', $params['start_date']) ?? 0;
        $counters['featured'] = (int)PortalMeta::getPortalMeta($portal->id, 'dubizzle_featured_listings', $params['start_date']) ?? 0;
        $counters['standard'] = (int)PortalMeta::getPortalMeta($portal->id, 'dubizzle_basic_listings', $params['start_date']) ?? 0;
        $counters['active_listings'] = $active_listings;
        $counters['active_sale_listings'] = (int)PortalMeta::getPortalMeta($portal->id, 'dubizzle_sale_listings', $params['start_date']) ?? 0;
        $counters['active_rental_listings'] = (int)PortalMeta::getPortalMeta($portal->id, 'dubizzle_rent_listings', $params['start_date']) ?? 0;
        $counters['agents_total'] = 0;
        $counters['last_updated_at'] =  $last_updated ? $last_updated->format('Y-m-d H:i A') : null;
        return $counters;

    }

    public function getDubizzleListingQualityScore($portal, $params): string
    {
        $user_ids = $params['user_ids'];
        $averageRank = round(PropertyMeta::where('meta_key', 'dubizzle_rank')
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
            ->avg('meta_value'), 2);
        return number_format($averageRank, 2);
    }
    public function getDubizzleSpecificListingCounts($portal, $params): array
    {
        $user_ids = $params['user_ids'];

        $bayutLevelCounts = [];
        $uniqueBayutLevels = ['Premium', 'Standard', 'Listing'];
        foreach ($uniqueBayutLevels as $bayutLevel) {
            $count = Property::whereHas('propertyMeta', function ($query) use ($bayutLevel) {
                $query->where('meta_key', 'dubizzle_level')
                    ->where('meta_value', $bayutLevel);
            })
                ->where('is_live', true)
                ->when(!$params['isSuperAdmin'], function ($q) use ($user_ids) {
                    $q->where(function ($q) use ($user_ids) {
                        $q->whereIn("agent_id", $user_ids);
                    });
                })
//
//                ->when(! empty($dates), function ($q) use ($dates) {
//                    $q->whereDate('properties.created_at', '>=', $dates['startDate'])->whereDate('properties.created_at', '<=', $dates['endDate']);
//                })
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
        return $bayutLevelCounts;

    }

    public function agentWiseListingsDubizzle($portal,$params,$agents) : array
    {
        $user_ids = $params['user_ids'];
        $listings = [];
        try {
            foreach ($agents as $user_id => $name) {
                $listings[] = [
                    'name' => $name,
                    'count' => Property::whereNot('completion_status', 'Off-Plan Primary')
                        ->where('region_id', 1)
                        ->whereHas('portals', function ($q) use ($portal) {
                            $q->where('portal_id', $portal->id);
                        })->where('is_live', '1')
                        ->where("agent_id", $user_id)
                        ->count() ?? 0
                ];
            }
            return $listings;
        }catch (\Exception $e){
            info('Error in DashboardV3PfController agentWiseListings '.json_encode($e->getMessage()));
            return [];
        }

    }
    public function getListingCounts($portal,$params) : int
    {
        $user_ids = $params['user_ids'];
        return Property::whereNot('completion_status', 'Off-Plan Primary')
            ->where('region_id', 1)
            ->whereHas('portals', function ($q) use ($portal) {
                $q->where('portal_id', $portal->id);
            })->where('is_live', '1')
            ->when(!$params['isSuperAdmin'], function ($q) use ($user_ids) {
                $q->whereIn("agent_id",  $user_ids);
            })->count() ?? 0;
    }
    public function getListingCountsByStatus($portal,$params,$agents) : array
    {
        $user_ids = $params['user_ids'];
        $listings = [];
        $statuses = ['PENDING','DRAFT','UNPUBLISHED','PUBLISHED','ARCHIVED'];
        try {
            foreach ($statuses as $status) {
                $listings[$status] = Property::whereNot('completion_status', 'Off-Plan Primary')
                    ->where('region_id', 1)
                    ->whereHas('portals', function ($q) use ($portal) {
                        $q->where('portal_id', $portal->id);
                    })
                    ->where('publish_status',$status)
                    ->whereIn("agent_id", $user_ids)
                    ->count();
            }
            return $listings;
        }catch (\Exception $e){
            info('Error in DashboardV3PfController agentWiseListings '.json_encode($e->getMessage()));
            return [];
        }

    }
    public function getListingCountsByStatusBayut($portal,$params,$agents) : array
    {
        $user_ids = $params['user_ids'];
        $listings = [];
        $statuses = ['PENDING','DRAFT','UNPUBLISHED','PUBLISHED','ARCHIVED'];
        try {
            foreach ($statuses as $status) {
                $listings[$status] = Property::whereNot('completion_status', 'Off-Plan Primary')
                    ->where('region_id', 1)
                    ->whereHas('portals', function ($q) use ($portal) {
                        $q->where('portal_id', $portal->id);
                    })
                    ->where('publish_status',$status)
                    ->whereIn("agent_id", $user_ids)
                    ->count();
            }
            return $listings;
        }catch (\Exception $e){
            info('Error in DashboardV3PfController agentWiseListings '.json_encode($e->getMessage()));
            return [];
        }

    }

}
