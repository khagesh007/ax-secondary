<?php


namespace App\Http\Library\Api;

use App\Http\Library\BaseLib;
use App\Http\Resources\Property\PropertyResource;
use App\Models\Portal;
use App\Models\Property;
use Illuminate\Http\Request;

class GenericPortal extends BaseLib
{

    public function run(Request $request, $withoutUnitNo = true)
    {
        $perPage = request('per_page', 10);
        $page = request('page', 1); // Default to page 1 if not specified

        $publish_status = request('publish_status', null);

        $query = Property::when($publish_status == "POCKET", function ($q) {
                $q->where('publish_status', 'POCKET');
            }, function ($q) {
                $q->where('is_live', true);
            });

        if ($request->has('property_type')) {
            $query->where('property_type_id', $request->property_type);
        }

        $mainPortal = Portal::where("slug", "generic-portal")->latest()->first();
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
            'floorPlan'])->orderBy('properties.created_at', 'desc')->paginate($perPage, ['*'], 'page', $page);
        $param = ['portal' => $mainPortal];
        $properties = PropertyResource::customCollection($properties, $param, $withoutUnitNo);
        return $this->sendResponse(true, 'Property List', compact('properties'), 200);
    }
}
