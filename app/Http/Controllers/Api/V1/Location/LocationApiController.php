<?php

namespace App\Http\Controllers\Api\V1\Location;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\RegionResource;
use App\Models\Region;
use App\Models\Community;
use App\Models\SubCommunity;
use App\Models\CommunityProperty;
use Illuminate\Http\Request;
use App\Http\Resources\CommunityResource;
use App\Http\Resources\SubCommunityResource;
use App\Http\Resources\CommunityPropertyResource;

class LocationApiController extends BaseController
{
    public function regions(Request $request)
    {
        // dd($request->all());
        $query = Region::where('status', true);
        $regions = $query->get();
        $count = $query->count();
        return $this->sendResponse(true, $count . ' Region(s) data found', RegionResource::collection($regions), 200);
    }

    public function communities(Request $request)
    {
        $query = Community::with('region')->where('status', true);
        $regions = $query->get();
        $count = $query->count();
        return $this->sendResponse(true, $count . ' Community(s) data found', CommunityResource::collection($regions), 200);

    }

    public function subCommunities(Request $request)
    {
        $query = SubCommunity::with('community')->where('status', true);
        $regions = $query->get();
        $count = $query->count();
        return $this->sendResponse(true, $count . ' Sub Communities(s) data found', SubCommunityResource::collection($regions), 200);

    }

    public function communityProperties(Request $request)
    {
        $query = CommunityProperty::with('subCommunity','community')->where('status', true);
        $regions = $query->get();
        $count = $query->count();
        return $this->sendResponse(true, $count . 'Community Property(s) data found', CommunityPropertyResource::collection($regions), 200);

    }
}
