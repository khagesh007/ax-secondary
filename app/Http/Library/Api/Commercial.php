<?php


namespace App\Http\Library\Api;

use App\Http\Library\BaseLib;
use App\Http\Resources\Property\PropertyCommercialResource;
use App\Http\Resources\Property\PropertyResource;
use App\Models\Portal;
use App\Models\Property;
use Illuminate\Http\Request;

class Commercial extends BaseLib
{

    public function run(Request $request)
    {
        $query = Property::where('is_live', true);
        $mainPortal = Portal::where("slug", "private-site")->latest()->first();
        if (is_null($mainPortal)) {
            abort(404);
        }
        $properties = $query->whereIn('for', ['Commercial for rent','Commercial for sale'])->with([
            'bed',
            'details',
            'agent',
            'propertyCategory',
            'price',
            'bath',
            'amenities',
            'community.region',
            'subCommunity',
            'communityProperty',
            'photos',
            'floorPlan'
        ])->orderBy('properties.created_at', 'desc')->get();
        $param = ['portal' => $mainPortal];

        $properties = PropertyCommercialResource::customCollection($properties, $param);
        return $this->sendResponse(true, 'Property List', compact('properties'), 200);
    }
}
