<?php


namespace App\Http\Controllers\Admin\Listing;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Services\Portal\PropertyFinderService;
use App\Traits\PropertyFinderTrait;
use Auth;
use Illuminate\Http\Request;

class ListingPortalController extends Controller
{
    public $propertyFinderService;
    use PropertyFinderTrait;

    public function __construct(PropertyFinderService $propertyFinderService)
    {
        $this->propertyFinderService = $propertyFinderService;
    }

    public function getPfListingUpgrades(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:properties,id',
        ]);
        $available_upgrades = $this->propertyFinderService->getUpgrades($request->id);
        return response()->json([
            'status' => true,
            'data' => $available_upgrades,
        ]);
    }
    public function upgradePfListing(Request $request)
    {

        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'feature' => 'required|string',
            'product_id' => 'required|string',
            'automatic_renewal' => 'required',
        ]);
        $params = [
            'property_id' => $request->property_id,
            'featureId' => $request->feature,
            'productId' => $request->product_id,
            'automatic_renewal' => (boolean)$request->automatic_renewal,
        ];
        $upgrade = $this->propertyFinderService->upgradeListing($params);
        if(is_null($upgrade)){
            return response()->json([
                'status' => false,
                'message' => 'Upgrade failed. Please try again later.',
            ], 500);
        }
        return response()->json([
            'status' => true,
            'data' => $upgrade,
        ]);
    }

    public function getPfListingDetails(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:properties,id',
        ]);
        $property = Property::find($request->id);
        $entity = $this->getEntityByPropertyForPFV2($property);

        $listing = $this->propertyFinderService->findListingByReference($property->ref_no, $property->id, $entity);
        if(is_null($listing)){
            return response()->json([
                'status' => false,
                'message' => 'Listing not found.',
            ]);
        }
        return response()->json([
            'status' => true,
            'data' => $listing,
            'html' => view('admin.listing.includes._pf_quality_score', compact('listing'))->render(),
        ]);
    }

}
