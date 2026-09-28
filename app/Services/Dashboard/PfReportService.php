<?php

namespace App\Services\Dashboard;




use App\Models\Portal;
use App\Models\Property;
use App\Models\PropertyMeta;
use App\Models\PortalMeta;
use App\Models\User;
use App\Models\UserThirdPartyData;
use DB;
use Carbon\Carbon;
class PfReportService
{
    public function __construct()
    {

    }
    public function summary($request, $params)
    {
        $portal = Portal::where('name', 'Property Finder')->first();
        $agents = [];
        if($params['isSuperAdmin']) {
            $agents = User::where('status', true)->whereHas("roles", fn($q) => $q->whereIn("name", ["Agent"]))->pluck('name','id');
            $user_ids = $agents->keys()->toArray();
            $params['user_ids'] = $user_ids;
        }
        $counters = $this->getDataCounts($portal,$params);
        $data = [
            'overview' => [
                'quality_score' => $this->getPfListingQualityScore($portal,$params),
            ],
            'counter' => $counters,

        ];
        return $data;
    }

    public function getPfListingQualityScore($portal,$params) : string
    {
        $user_ids = $params['user_ids'];
        $score =  PropertyMeta::where('meta_key', 'pf_rank')
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
            ->avg('meta_value') ?? 0;
        return number_format($score, 2);

    }

    public function getDataCounts($portal,$params) : array
    {

        $super_agents = PortalMeta::getPortalMeta($portal->id, 'pf_total_super_agents', $params['start_date']);
        $total_agents = PortalMeta::getPortalMeta($portal->id, 'pf_total_agents', $params['start_date']);
        $last_updated = PortalMeta::where('portal_id', $portal->id)->where('meta_key', 'pf_live_listings')->orderBy('updated_at','DESC')->first()->updated_at ?? null;
        $verified_listings = (int)PortalMeta::getPortalMeta($portal->id, 'pf_verified_listings', $params['start_date'])  ?? 0;
        $live_listings = (int)PortalMeta::getPortalMeta($portal->id, 'pf_live_listings', $params['start_date']) ?? 0;
        $counters['live_listings'] = $live_listings;
        $counters['standard'] = (int)PortalMeta::getPortalMeta($portal->id, 'pf_live_listings_standard', $params['start_date']) ?? 0;
        $counters['featured'] = (int)PortalMeta::getPortalMeta($portal->id, 'pf_live_listings_featured', $params['start_date']) ?? 0;
        $counters['premium'] = (int)PortalMeta::getPortalMeta($portal->id, 'pf_live_listings_premium', $params['start_date'])  ?? 0;
        $counters['verified'] = $verified_listings;
        $counters['unverified'] = $live_listings - $verified_listings;
        $counters['total_agents'] = (int)$total_agents;
        $counters['super_agents'] = (int)$super_agents;
        $counters['non_super_agents'] = $total_agents > 0 ? $total_agents - $super_agents : 0;
        $counters['last_updated_at'] =  $last_updated ? $last_updated->format('Y-m-d H:i A') : null;
        return $counters;

    }

}
