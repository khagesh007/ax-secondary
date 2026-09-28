<?php

namespace App\Http\Controllers\Admin\CriteriaMatching;

use App\Http\Controllers\Controller;
use App\Models\Bath;
use App\Models\Bed;
use App\Models\Lead;
use App\Models\Property;
use App\Models\PropertyCategory;
use Illuminate\Http\Request;

class CriteriaMatchingController extends Controller
{
    public function index()
    {
        $data['beds'] = Bed::where('status', true)->orderBy('order', 'asc')->get();
        $data['baths'] = Bath::where('status', true)->orderBy('order', 'asc')->get();
        $data['property_categories'] = PropertyCategory::where('status', true)->orderBy('order', 'asc')->get();
        $data['properties'] = Property::latest()->limit(10)->get();
        $data['leads'] = Lead::latest()->limit(10)->get();
        return view('admin.criteria_matching.index')->with($data);
    }

    public function fetchDetails(Request $request)
    {

        $request->validate([
            'model_type' => 'required',
            'model_id' => 'required',
        ]);

        if ($request->model_type == 'Property') {
            $model = Property::with('price','community','subCommunity','communityProperty')->where('id', 'LIKE', $request->model_id)->first();
            if (!$model) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Model not found',
                ]);
            }
            return response()->json([
                'status' => '1',
                'model_type' => 'Property',
                'data' => $model,
            ]);

        } else if ($request->model_type == 'Lead') {
            $model = Lead::with(['criteria', 'property', 'criteria.locations.parentable', 'leadType', 'leadAgents', 'leadSubStatus', 'leadStatus', 'createdBy', 'updatedBy', 'leadMedium'])->where('id', 'LIKE', $request->model_id)->first();
            if (!$model) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Model not found',
                ]);
            }
            return response()->json([
                'status' => '1',
                'model_type' => 'Lead',
                'data' => $model,
            ]);
        }

        return false;
    }

    public function returnModel(Request $request)
    {


        if ($request->model_type == 'Property') {
            $data = Lead::where('status', true)
                ->when($request->bed_id, function ($query) use ($request) {
                    $query->whereHas('criteria', function ($q) use ($request) {
                        $q->whereBetween('min_bed_id', [$request->bed_id, $request->bed_id]);
                    });
                })
                ->when($request->bath_id, function ($query) use ($request) {
                    $query->whereHas('criteria', function ($q) use ($request) {
                        $q->whereBetween('min_bath_id', [$request->bath_id, $request->bath_id]);
                    });
                })
                ->when($request->min_price && !$request->max_price, function ($query) use ($request) {
                    $price = $request->min_price;
                    $query->whereHas('criteria', function ($q) use ($price) {
                        $q->whereBetween('min_price', [$price - ($price * 0.10), $price + ($price * 0.10)]);
                    });
                })
                ->when($request->min_price && $request->max_price, function ($query) use ($request) {
                    $query->whereHas('criteria', function ($q) use ($request) {
                        $q->whereBetween('min_price', [$request->min_price, $request->max_price]);
                    });
                })
                ->when($request->sub_communities, function ($query) use ($request) {

                    $subCommunityIds = [];
                    $communityPropertyIds = [];

                    foreach ($request->sub_communities as $subCommunity) {
                        [$id, $type] = explode(':', $subCommunity);

                        if ($type === 'sub_community') {
                            $subCommunityIds[] = $id;
                        } elseif ($type === 'community_property') {
                            $communityPropertyIds[] = $id;
                        }
                    }

                    // info($communityPropertyIds);

                    $query->when(count($subCommunityIds) > 0, function ($query) use ($subCommunityIds, $communityPropertyIds) {


                        $query->whereHas('criteria', function ($q) use ($subCommunityIds, $communityPropertyIds) {


                            $q->whereHas("locations", function ($q) use ($subCommunityIds, $communityPropertyIds) {

                                $q->where(function ($q) use ($subCommunityIds , $communityPropertyIds) {

                                    $q->whereIn('parentable_id', $subCommunityIds)->where('parentable_type', 'App\Models\SubCommunity');

                                })->orWhere(function ($q) use ($communityPropertyIds) {

                                    $q->whereIn('parentable_id', $communityPropertyIds)->where('parentable_type', 'App\Models\CommunityProperty');
                                });
                            });


                        });
                    });
                })

                ->orderBy('created_at', 'desc')
                ->with(['criteria', 'property', 'criteria.locations.parentable', 'leadType', 'leadAgents', 'leadSubStatus', 'leadStatus', 'createdBy', 'updatedBy', 'leadMedium'])
                ->limit(10)
                ->get();


                // dd($data);
            return view('admin.criteria_matching.partials._leads')->with('data', $data);

        } else if ($request->model_type == 'Lead') {


            $data = Property::with('propertyCategory', 'subCommunity', 'communityProperty', 'propertyCategory',
            'agent', 'createdBy', 'updatedBy', 'portals.portal', 'price', 'propertyStatus', 'bed', 'community')
            ->where('is_live', true)
            ->when($request->bed_id, function ($query) use ($request) {
                $query->where('bed_id', $request->bed_id);
            })
            ->when($request->bath_id, function ($query) use ($request) {
                $query->where('bath_id', $request->bath_id);
            })
            ->when($request->property_category_id, function ($query) use ($request) {
                $query->where('property_category_id', $request->property_category_id);
            })
            ->when(isset($request->min_price), function ($query) use ($request) {
                $query->whereHas('price', function ($q) use ($request) {
                    $price_from = $request->min_price - ($request->min_price * 0.10);
                    $price_to = $request->min_price + ($request->min_price * 0.10);
                    $q->whereBetween('price', [$price_from, $price_to]);
                });
            })
            ->when(isset($request->min_price, $request->max_price), function ($query) use ($request) {
                $query->whereHas('price', function ($q) use ($request) {
                    $q->whereBetween('price', [$request->min_price, $request->max_price]);
                });
            })
            ->when($request->sub_communities, function ($query) use ($request) {


                $subCommunityIds = [];
                $communityPropertyIds = [];

                foreach ($request->sub_communities as $subCommunity) {
                    [$id, $type] = explode(':', $subCommunity);

                    if ($type === 'sub_community') {
                        $subCommunityIds[] = $id;
                    } elseif ($type === 'community_property') {
                        $communityPropertyIds[] = $id;
                    }
                }

                $query->when(count($subCommunityIds) > 0, function ($query) use ($subCommunityIds) {
                    $query->whereIn('sub_community_id', $subCommunityIds);
                })
                ->when(count($communityPropertyIds) > 0, function ($query) use ($communityPropertyIds) {
                    $query->whereIn('community_property_id', $communityPropertyIds);
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();

            //  dd($data);
            return view('admin.criteria_matching.partials._properties')->with('data', $data);

        }

        return false;
    }

}
