<?php


namespace App\Http\Library\Api;

use App\Http\Library\BaseLib;
use App\Http\Resources\Property\PropertyResource;
use App\Models\Portal;
use App\Models\Property;
use Illuminate\Http\Request;

class PrivateSite extends BaseLib
{

    public function run(Request $request)
    {
        $query = Property::where('is_live', true);
        $mainPortal = Portal::where("slug", "private-site")->latest()->first();
        if (is_null($mainPortal)) {
            abort(404);
        }
        $properties = $query->whereHas("portals", function ($q) use ($mainPortal) {
            $q->where("portal_id", $mainPortal->id);
        })->with(['bed.portals' => function ($q) use ($mainPortal) {
            $q->where('portal_id', '=', $mainPortal->id);

        }, 'details',
            'agent',
            'propertyCategory.portals' => function ($q) use ($mainPortal) {
                $q->where('portal_id', '=', $mainPortal->id);

            },
            'price',
            'bath.portals' => function ($q) use ($mainPortal) {
                $q->where('portal_id', '=', $mainPortal->id);
            },
            'amenities' => function ($q) use ($mainPortal) {
                        $q->whereHas('amenity.portals', function($q) use ($mainPortal) {
                              $q->where('portal_id', '=', $mainPortal->id);
                          });
                    },
                    'amenities.amenity.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', '=', $mainPortal->id)
                          ->where('title', '!=', '');
                    },
            'community.region',
            'subCommunity',
            'communityProperty',
            'photos',
            'floorPlan'])->orderBy('properties.created_at', 'desc')->get();
        $param = ['portal' => $mainPortal];

        $properties = PropertyResource::customCollection($properties, $param);
        return $this->sendResponse(true, 'Property List', compact('properties'), 200);
    }
}
