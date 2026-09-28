<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Api\BaseController;
use App\Http\Controllers\Controller;
use App\Models\Bath;
use App\Models\Bed;
use App\Models\Property;
use App\Models\PropertyCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Group;

class FrontListingController extends Controller
{


    public function listing(Request $request)
    {

        try {

            $query = Property::with('agent', 'community', 'subCommunity', 'communityProperty', 'photos', 'bed', 'price', 'bath', 'propertyCategory', 'feature')->whereHas('photos');



            if ($request->has('search_location_id') && $request->input('search_location_id') != ''  && $request->has('search_location_type') && $request->input('search_location_type') != '') {
                $query->where($request->input('search_location_type') . '_id', $request->input('search_location_id'));
            }

            // Filter by bed
            if ($request->has('search_bed') && $request->input('search_bed') != '') {
                $query->where('bed_id', $request->input('search_bed'));
            }

            // Filter by bath
            if ($request->has('search_bath') && $request->input('search_bath') != '') {
                $query->where('bath_id', $request->input('search_bath'));
            }

            // Filter by search_category
            if ($request->has('search_category') && $request->input('search_category') != '') {
                $query->where('property_category_id', $request->input('search_category'));
            }

            // Filter by search_type
            if ($request->has('search_type') && $request->input('search_type') != [null]) {
                $query = $this->applySearchTypeFilter($query, $request->input('search_type'));
            } else {
                $query->whereIn('publish_status', ["PUBLISHED","POCKET"]);
            }

            // Filter by search_category
            if ($request->has('search_category') && $request->input('search_category') != '') {
                $query->where('property_category_id', $request->input('search_category'));
            }

            // search_zone
            if ($request->has('search_zone') && $request->input('search_zone') != '') {
                $query->whereHas('agent', function ($query) use ($request) {
                    $query->where('zone_id', $request->input('search_zone'));
                });
            }

            // search agent_id
            if ($request->has('search_agent') && $request->input('search_agent') != '') {
                $query->where('agent_id', $request->input('search_agent'));
            }

            // Filter by min price
            if ($request->has('min_price') && $request->input('min_price') != '') {
                $query->where('down_payment_price', '>=', $request->input('min_price'));
            }

            // Filter by max price
            if ($request->has('max_price') && $request->input('max_price') != '') {
                $query->where('down_payment_price', '<=', $request->input('max_price'));
            }

            // Filter by min size
            if ($request->has('min_size') && $request->input('min_size') != '') {
                $query->where('built_up_area', '>=', intval($request->input('min_size')));
            }

            // Filter by max size
            if ($request->has('max_size') && $request->input('max_size') != '') {

                $query->where('built_up_area', '<=', intval($request->input('max_size')));
            }

            if ($request->has('property_type') && $request->input('property_type') != '') {
                if($request->input('property_type')=='sale'){
                    $query->whereIn('for', ['Residential for sale', 'Commercial for sale']);
                }
                if($request->input('property_type')=='rent'){
                    $query->whereIn('for', ['Residential for rent', 'Commercial for rent']);
                }

            }


            if ($request->has('price_per_sqft') && $request->input('price_per_sqft') != '') {
                if($request->input('property_type')=='sale'){
                $pricePerSqft = $request->input('price_per_sqft');
                $query->whereHas('price', function ($query) use ($pricePerSqft) {
                    $minPricePerSqft = $pricePerSqft - 1000;
                    $maxPricePerSqft = $pricePerSqft + 1000;

                    $query->whereRaw('price / properties.built_up_area BETWEEN ? AND ?', [$minPricePerSqft, $maxPricePerSqft]);
                });
            }
            }

            if ($request->has('search_keywords') && $request->input('search_keywords') != '') {
                // dd($request->input('search_keywords'));
                $query->where(function ($query) use ($request) {
                    $query->where('title', 'like', '%' . $request->input('search_keywords') . '%')
                        ->orWhere('description', 'like', '%' . $request->input('search_keywords') . '%')
                        ->orWhereHas('community', function ($query) use ($request) {
                            $query->where('name', 'like', '%' . $request->input('search_keywords') . '%');
                        })
                        ->orWhereHas('subCommunity', function ($query) use ($request) {
                            $query->where('name', 'like', '%' . $request->input('search_keywords') . '%');
                        })
                        ->orWhereHas('communityProperty', function ($query) use ($request) {
                            $query->where('name', 'like', '%' . $request->input('search_keywords') . '%');
                        })
                        ->orWhereHas('agent', function ($query) use ($request) {
                            $query->where('first_name', 'like', '%' . $request->input('search_keywords') . '%');
                        })
                        ->orWhereHas('amenities', function ($query) use ($request) {
                            $query->where('title', 'like', '%' . $request->input('search_keywords') . '%');
                        })
                        ->orWhereHas('feature', function ($query) use ($request) {
                            $query->where('title', 'like', '%' . $request->input('search_keywords') . '%');
                        });

                });
            }


            //  //Filter Pocket
            // if($request->has('is_pocket') && $request->has('is_pocket') == 1 ){
            //     $query->whereIn('publish_status', ["POCKET","PUBLISHED"]);
            // }else{
            // }



            // Sorting
            $sortBy = $request->get('sort_by', 'new');
            switch ($sortBy) {
                case 'old':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'new':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'featured':
                    $query->where('featured', 1);
                    break;
                case 'price_asc':
                    $query->orderBy('down_payment_price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('down_payment_price', 'desc');
                    break;
                default:
                    $query->orderBy('created_at', 'DESC');
            }


            $pocket_count = Property::where('publish_status', 'POCKET')->count();

            $data['properties'] = $query->paginate(15);
            $data['beds'] = Bed::all();
            $data['baths'] = Bath::all();
            $data['categories'] = PropertyCategory::all();
            $data['pocket_count']  = $pocket_count;
            $data['groups'] = Group::whereNull("parent_id")->get(["id", "name"]);
            $data['agents'] = \App\Models\User::where('status', true)
            ->whereHas('roles', function ($roleQuery) {
                $roleQuery->where('name', 'Agent'); // Adjust based on your actual role name
            })->get();


            return view('front.listings.index')->with('data', $data);
        } catch (\Exception $e) {
            // Handle exceptions
            return response()->json([
                'status' => false,
                'message' => 'An error occurred. Please try again or contact support.' . json_encode($e->getMessage() . ' file: ' . $e->getFile()),
            ], 500);
        }
    }

    function applySearchTypeFilter($query, $searchTypes)
    {

        if ($searchTypes[0] == null) {
            array_shift($searchTypes);
        }

        if (count($searchTypes) == 2 && ($searchTypes[0] == 'sale' || $searchTypes[1] == 'sale') && ($searchTypes[0] == 'rent' || $searchTypes[1] == 'rent')) {
            $query->where('publish_status', "PUBLISHED");
            return $query;
        } else if (count($searchTypes) == 1 && $searchTypes[0] == 'sale') {
            $query->where('publish_status', "PUBLISHED");
            $query->where('for', 'like', '%sale%');
        } else if (count($searchTypes) == 1 && $searchTypes[0] == 'rent') {
            $query->where('publish_status', "PUBLISHED");
            $query->where('for', 'like', '%rent%');
        } else if (count($searchTypes) == 1 && $searchTypes[0] == 'pocket') {
            $query->whereIn('publish_status', ["POCKET"]);
        } else if (count($searchTypes) == 2 && ($searchTypes[0] == 'sale' || $searchTypes[1] == 'sale') && ($searchTypes[0] == 'pocket' || $searchTypes[1] == 'pocket')) {
            $query->where('for', 'like', '%sale%');
            $query->whereIn('publish_status', ["POCKET"]);
        } else if (count($searchTypes) == 2 && ($searchTypes[0] == 'rent' || $searchTypes[1] == 'rent') && ($searchTypes[0] == 'pocket' || $searchTypes[1] == 'pocket')) {
            $query->where('for', 'like', '%rent%');
            $query->whereIn('publish_status', ["POCKET", "PUBLISHED"]);
        } else {
            $query->whereIn('publish_status', ["POCKET", "PUBLISHED"]);
        }

        return $query;
    }

    public function listingDetails(Request $request)
    {
        $property = Property::whereIn('publish_status', ['PUBLISHED','POCKET'])->where('id', $request->id)
            ->with('agent', 'community', 'amenities', 'feature', 'subCommunity', 'communityProperty', 'photos', 'bed', 'price', 'bath', 'propertyCategory')->first();
        if(!$property){
            abort(404);
        }

        return view('front.listings.detail')->with('data', [
            'property' => $property
        ]);
    }


    public function search_location(Request $request)
    {

        $searchterm = $request->input('keyword');

        $communities = \App\Models\Community::where('name', 'LIKE', '%' . $searchterm . '%')->limit(5)->get()->map(function ($item) {
            $item['type'] = 'community';
            return $item;
        })->sortBy('name');

        $sub_communities = \App\Models\SubCommunity::where('name', 'LIKE', '%' . $searchterm . '%')->limit(5)->get()->map(function ($item) {
            $item['type'] = 'sub_community';
            return $item;
        })->sortBy('name');

        $community_properties = \App\Models\CommunityProperty::where('name', 'LIKE', '%' . $searchterm . '%')->limit(5)->get()->map(function ($item) {
            $item['type'] = 'community_property';
            return $item;
        })->sortBy('name');

        $merged = $communities->merge($sub_communities)->merge($community_properties);

        return response()->json($merged, 200);
    }
}
