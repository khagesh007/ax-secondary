<?php

namespace App\Http\Controllers\Api\V1\Parameters;

use App\Http\Controllers\Api\BaseController;
use App\Models\Bath;
use App\Models\Bed;
use App\Models\Contact;
use App\Models\Portal;
use App\Models\PropertyAmenity;
use App\Models\PropertyCategory;
use App\Models\Type;
use App\Models\Medium;
use App\Models\Source;
use App\Models\PropertyStatus;
use Illuminate\Http\Request;
use App\Http\Resources\Bed\BedResource;
use App\Http\Resources\Bath\BathResource;
use App\Http\Resources\PropertyCategory\PropertyCategoryResource;
use App\Http\Resources\Location\LocationResource;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\PropertyAmenity\PropertyAmenityResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
class ParameterController extends BaseController
{

/**
 *
 *
 * Owners
 *
 * Get Owners
 * @group Parameters
 * @authenticated
 * @queryParam agent_id int required  Get contacts of the agent.Example: 4
 * @header Accept application/json

 *
 * @return \Illuminate\Http\JsonResponse
 * @response 200 scenario="Data found"
 * {
{
"status": true,
"code": 200,
"message": "Data fetched",
"data": [
{
"id": 5298,
"title": "Mr.",
"first_name": "Shabnam",
"last_name": "Ashraf",
"email": null,
"mobile": "+971 50 104 1711",
"agent_id": 4
},
{
"id": 5299,
"title": "Mrs.",
"first_name": "Shirin",
"last_name": "Maswala",
"email": null,
"mobile": "+971 50 884 8081",
"agent_id": 4
},
]
}

 *}
 * @param  \Illuminate\Http\Request  $request
 * @return \Illuminate\Http\Response
 * @response 200 scenario="Owner not found"
 * {
"status": false,
"code": 500,
"message": "No Data found",
"data": []
}
 */
    public function getOwners(Request $request)
    {
        // info($request->all());
        $request->validate([
            'agent_id' => 'required|exists:users,id',
        ]);
        try {
            $data = \App\Models\Contact::where('agent_id', $request->agent_id)->get(['id', 'title', 'first_name', 'last_name', 'email', 'mobile', 'agent_id']);
            return $this->sendResponse(true, 'Data fetched', $data, 200);
        } catch (\Throwable $th) {
            return $this->sendResponse(false, 'Something Went Wrong', $th->getMessage(), 500);
        }
    }

    /**
     *
     *
     * Categories
     *
     * Get Categories
     * @group Parameters
     * @authenticated
    Example: abc
     *
     *
     *
     * @header Accept application/json

     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
     * {
    {
    "status": true,
    "code": 200,
    "message": "Data fetched",
    "data": [
    {
    "id": 1,
    "title": "Apartment",
    "slug": "apartment",
    "thumbnail": null,
    "image": null,
    "color_code": null,
    "status": 1,
    "order": 0,
    "badge": null,
    "icon": null,
    "created_by_id": null,
    "updated_by_id": 263,
    "created_at": "2023-02-14T04:30:33.000000Z",
    "updated_at": "2023-06-26T15:31:16.000000Z",
    "deleted_at": null
    },
    {
    "id": 2,
    "title": "Villa",
    "slug": "villa",
    "thumbnail": null,
    "image": null,
    "color_code": null,
    "status": 1,
    "order": 0,
    "badge": null,
    "icon": null,
    "created_by_id": null,
    "updated_by_id": 263,
    "created_at": "2023-02-14T04:30:33.000000Z",
    "updated_at": "2023-06-26T15:22:02.000000Z",
    "deleted_at": null
    },
    ],
    "page": 1,
    "per_page": 10
    }
     *}
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @response 200 scenario="No Data found"
     * {
    "status": false,
    "code": 200,
    "message": "No Data found",
    "data": []
    }
     */

    public function getCategory(Request $request)
    {
        try {
            $data = PropertyCategory::with('portals.portal')->where('status', true)->orderBy('order', 'asc')->get();
            return $this->sendResponse(true, 'Data fetched', $data, 200);
        } catch (\Throwable $th) {
            return $this->sendResponse(false, 'Something Went Wrong', $th->getMessage(), 500);
        }
    }
    public function getTypeList(Request $request)
    {
        try {
            $perPage = request('per_page', 10);
            $page = request('page', 1);
            $data = PropertyCategory::with('portals.portal')->where('status', true)->orderBy('order', 'asc')->paginate($perPage, ['*'], 'page', $page);
            $data = PropertyCategoryResource::collection($data);
            return $this->sendResponse(true, 'Data fetched', $data, 200);
        } catch (\Throwable $th) {
            return $this->sendResponse(false, 'Something Went Wrong', $th->getMessage(), 500);
        }
    }
    public function getLocationList(Request $request)
    {
        try {
            $searchterm = $request->input('keyword');
            $perPage = (int) $request->input('per_page', 10);
            $page = (int) $request->input('page', 1);

            $communities = \App\Models\Community::when($searchterm, function ($query, $searchterm) {
                    return $query->where('name', 'LIKE', '%' . $searchterm . '%');
                })
                ->get()
                ->map(function ($item) {
                    $item['type'] = 'community';
                    return $item;
                });

            $sub_communities = \App\Models\SubCommunity::when($searchterm, function ($query, $searchterm) {
                    return $query->where('name', 'LIKE', '%' . $searchterm . '%');
                })
                ->get()
                ->map(function ($item) {
                    $item['type'] = 'sub_community';
                    return $item;
                });

            $community_properties = \App\Models\CommunityProperty::when($searchterm, function ($query, $searchterm) {
                    return $query->where('name', 'LIKE', '%' . $searchterm . '%');
                })
                ->get()
                ->map(function ($item) {
                    $item['type'] = 'community_property';
                    return $item;
                });

            // Merge and sort all results
            $merged = $communities->merge($sub_communities)->merge($community_properties)->sortBy('name');
            $merged = LocationResource::collection($merged);
            // Paginate manually
            $results = new LengthAwarePaginator(
                $merged->forPage($page, $perPage)->values(),
                $merged->count(),
                $perPage,
                $page,
                ['path' => url()->current()]
            );

            $response = [
                'status' => 200,
                'title' => 'Data fetched',
                'data'    => $results->items(),
                'message' => 'Matching Listings',
                'page' => $page,
                'per_page' => $perPage,
                'total_records' => $results->total(),
            ];
            return response()->json($response, 200);

        } catch (\Throwable $th) {
            return $this->sendResponse(false, 'Something Went Wrong', $th->getMessage(), 500);
        }
    }


    /**
     *
     *
     * Types
     *
     * Get Types
     * @group Parameters
     * @authenticated
    Example: abc
     *
     *
     *
     * @header Accept application/json

     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
     * {
    {
    "status": true,
    "code": 200,
    "message": "Data fetched",
    "data": [
    {
    "id": 11,
    "name": "Unspecified",
    "status": 1,
    "is_contact_type": 1,
    "is_lead_type": 0,
    "order": 21,
    "icon": null,
    "badge": null,
    "created_by_id": 1,
    "updated_by_id": 262,
    "created_at": "2023-03-11T06:48:50.000000Z",
    "updated_at": "2023-05-31T06:58:16.000000Z",
    "deleted_at": null,
    "image": null,
    "thumbnail": null
    },
    {
    "id": 10,
    "name": "Other",
    "status": 1,
    "is_contact_type": 1,
    "is_lead_type": 1,
    "order": 20,
    "icon": null,
    "badge": null,
    "created_by_id": null,
    "updated_by_id": 1,
    "created_at": "2023-02-14T04:30:33.000000Z",
    "updated_at": "2023-05-23T12:07:48.000000Z",
    "deleted_at": null,
    "image": null,
    "thumbnail": null
    },
    ],
    "page": 1,
    "per_page": 10
    }
     *}
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @response 200 scenario="No Data found"
     * {
    "status": false,
    "code": 200,
    "message": "No Data found",
    "data": []
    }
     */
    public function getType(Request $request)
    {
        try {
            $data = Type::where('status', true)->orderBy('order', 'desc')->get();
            return $this->sendResponse(true, 'Data fetched', $data, 200);
        } catch (\Throwable $th) {
            return $this->sendResponse(false, 'Something Went Wrong', $th->getMessage(), 500);
        }

    }

    /**
     *
     *
     * Beds
     *
     * Get Beds
     * @group Parameters
     * @authenticated
    Example: abc
     *
     *
     *
     * @header Accept application/json

     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
     * {
    {
    "status": true,
    "code": 200,
    "message": "Data fetched",
    "data": [
    {
    "id": 24,
    "name": "NA",
    "status": 1,
    "order": 24,
    "created_by_id": 173,
    "updated_by_id": 1,
    "deleted_at": null,
    "created_at": "2023-04-14T09:38:04.000000Z",
    "updated_at": "2023-05-31T12:49:55.000000Z"
    },
    {
    "id": 23,
    "name": "22 beds",
    "status": 1,
    "order": 22,
    "created_by_id": null,
    "updated_by_id": 1,
    "deleted_at": null,
    "created_at": "2023-02-14T04:30:31.000000Z",
    "updated_at": "2023-11-08T14:12:13.000000Z"
    },
    ],
    "page": 1,
    "per_page": 10
    }
     *}
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @response 200 scenario="No Data found"
     * {
    "status": false,
    "code": 200,
    "message": "No Data found",
    "data": []
    }
     */
    public function getBeds(Request $request)
    {
        try {
            $data = Bed::where('status', true)->orderBy('order', 'desc')->get();
            return $this->sendResponse(true, 'Data fetched', $data, 200);
        } catch (\Throwable $th) {
            return $this->sendResponse(false, 'Something Went Wrong', $th->getMessage(), 500);
        }

    }
    public function getBedList(Request $request)
    {
        try {
            $perPage = request('per_page', 10);
            $page = request('page', 1);
            $data = Bed::where('status', true)->orderBy('order', 'asc')->paginate($perPage, ['*'], 'page', $page);
            $data = BedResource::collection($data);
            return $this->sendResponse(true, 'Data fetched', $data, 200);
        } catch (\Throwable $th) {
            return $this->sendResponse(false, 'Something Went Wrong', $th->getMessage(), 500);
        }
    }

    /**
     *
     *
     * Baths
     *
     * Get Baths
     * @group Parameters
     * @authenticated
    Example: abc
     *
     *
     *
     * @header Accept application/json

     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
     * {
    {
    "status": true,
    "code": 200,
    "message": "Data fetched",
    "data": [
    {
    "id": 21,
    "name": "NA",
    "status": 1,
    "order": 21,
    "created_by_id": 173,
    "updated_by_id": 1,
    "deleted_at": null,
    "created_at": "2023-04-14T09:42:19.000000Z",
    "updated_at": "2023-05-31T12:47:27.000000Z"
    },
    {
    "id": 20,
    "name": "20 baths",
    "status": 1,
    "order": 20,
    "created_by_id": null,
    "updated_by_id": 1,
    "deleted_at": null,
    "created_at": "2023-02-14T04:30:31.000000Z",
    "updated_at": "2023-11-08T14:14:20.000000Z"
    },
    ],
    "page": 1,
    "per_page": 10
    }
     *}
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @response 200 scenario="No Data found"
     * {
    "status": false,
    "code": 200,
    "message": "No Data found",
    "data": []
    }
     */
    public function getBaths(Request $request)
    {
        try {
            $data = Bath::where('status', true)->orderBy('order', 'desc')->get();
            return $this->sendResponse(true, 'Data fetched', $data, 200);
        } catch (\Throwable $th) {
            return $this->sendResponse(false, 'Something Went Wrong', $th->getMessage(), 500);
        }
    }

    public function getBathList(Request $request)
    {
        try {
            $perPage = request('per_page', 10);
            $page = request('page', 1);
            $data = Bath::where('status', true)->orderBy('order', 'asc')->paginate($perPage, ['*'], 'page', $page);
            $data = BathResource::collection($data);

            return $this->sendResponse(true, 'Data fetched', $data, 200);

            return $this->sendResponse(true, 'Data fetched', $data, 200);
        } catch (\Throwable $th) {
            return $this->sendResponse(false, 'Something Went Wrong', $th->getMessage(), 500);
        }
    }
    public function getCategoryList()
    {
        try {
            $data = \App\Models\Lead::FOR_SELECT;
            return $this->sendResponse(true, 'Data fetched', $data, 200);
        } catch (\Throwable $th) {
            return $this->sendResponse(false, 'Something Went Wrong', $th->getMessage(), 500);
        }
    }

    /**
     *
     *
     * Payment Methods
     *
     * Get Payment Methods
     * @group Parameters
     * @authenticated
    Example: abc
     *
     *
     *
     * @header Accept application/json

     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
     * {
    {
    "status": true,
    "code": 200,
    "message": "Data fetched",
    "data": [
    "cash": "Cash",
    "installments": "Installments",
    "cash,installments": "Cash, Installments"
    ],
    "page": 1,
    "per_page": 10
    }
     *}
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @response 200 scenario="No Data found"
     * {
    "status": false,
    "code": 200,
    "message": "No Data found",
    "data": []
    }
     */
    public function getPaymentMethods(Request $request)
    {
        try {
            $data = [
                ['key' => 'cash', 'value' => 'Cash'],
                ['key' => 'installments', 'value' => 'Installments'],
                ['key' => 'cash,installments', 'value' => 'Cash, Installments'],

            ];
            return $this->sendResponse(true, 'Data fetched', $data, 200);
        } catch (\Throwable $th) {
            return $this->sendResponse(false, 'Something Went Wrong', $th->getMessage(), 500);
        }
    }

    /**
     *
     *
     * Amenities
     *
     * Get Amenities
     * @group Parameters
     * @authenticated
    Example: abc
     *
     *
     *
     * @header Accept application/json

     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
     * {
    {
    "status": true,
    "code": 200,
    "message": "Data fetched",
    "data": [
    {
    "id": 23,
    "title": "Part furnished",
    "slug": "part-furnished",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/amenities/00zITIeAm3R3wCwohmRn1dZfTbOfq4kKm8dJn5aD.svg",
    "image": null,
    "color_code": null,
    "status": 1,
    "order": 12,
    "badge": "badge bg-primary",
    "icon": "ri-bar-chart-2-fill",
    "type": "Features",
    "amenity_type": "private",
    "created_by_id": null,
    "updated_by_id": 1,
    "created_at": "2023-02-14T04:30:35.000000Z",
    "updated_at": "2023-05-04T13:01:41.000000Z",
    "deleted_at": null
    },
    {
    "id": 1,
    "title": "Balcony",
    "slug": "balcony",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/amenities/xNvKzK6Cd10El5uiVfHiZJPmKFaaqXQl6g3aLnnk.svg",
    "image": null,
    "color_code": null,
    "status": 1,
    "order": 0,
    "badge": null,
    "icon": null,
    "type": "Features",
    "amenity_type": "private",
    "created_by_id": null,
    "updated_by_id": 263,
    "created_at": "2023-02-14T04:30:35.000000Z",
    "updated_at": "2023-06-27T06:55:53.000000Z",
    "deleted_at": null
    },
    ],
    "page": 1,
    "per_page": 10
    }
     *}
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @response 200 scenario="No Data found"
     * {
    "status": false,
    "code": 200,
    "message": "No Data found",
    "data": []
    }
     */
    public function getAmenities(Request $request)
    {
        try {
            $data = PropertyAmenity::with('portals.portal')->where('status', true)->orderBy('order', 'desc')->get();
            return $this->sendResponse(true, 'Data fetched', $data, 200);
        } catch (\Throwable $th) {
            return $this->sendResponse(false, 'Something Went Wrong', $th->getMessage(), 500);
        }
    }
    public function getAmenitiesList()
    {
        try {
            $perPage = request('per_page', 10);
            $page = request('page', 1);
            $data = PropertyAmenity::where('status', true)->get();
            $data = PropertyAmenityResource::collection($data);
            return $this->sendResponse(true, 'Data fetched', $data, 200);
        } catch (\Throwable $th) {
            return $this->sendResponse(false, 'Something Went Wrong', $th->getMessage(), 500);
        }
    }

    /**
     *
     *
     * Furnishing Type
     *
     * Get Furnishing Type
     * @group Parameters
     * @authenticated
    Example: abc
     *
     *
     *
     * @header Accept application/json

     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
     * {
    {
    "status": true,
    "code": 200,
    "message": "Data fetched",
    "data": [
    {
    "key": "FURNISHED",
    "name": "FURNISHED"
    },
    {
    "key": "SEMI_FURNISHED",
    "name": "Semi Furnished"
    },
    {
    "key": "UNFURNISHED",
    "name": "Unfurnished"
    }
    ],
    "page": 1,
    "per_page": 10
    }
     *}
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @response 200 scenario="No Data found"
     * {
    "status": false,
    "code": 200,
    "message": "No Data found",
    "data": []
    }
     */
    public function getFurnishingType(Request $request)
    {
        try {
            // $data = \App\Models\Property::FURNISHED_SELECT;

            $data = [
                ['key' => 'FURNISHED', 'value' => 'FURNISHED'],
                ['key' => 'SEMI_FURNISHED', 'value' => 'Semi Furnished'],
                ['key' => 'UNFURNISHED', 'value' => 'Unfurnished'],
            ];

            return $this->sendResponse(true, 'Data fetched', $data, 200);
        } catch (\Throwable $th) {
            return $this->sendResponse(false, 'Something Went Wrong', $th->getMessage(), 500);

        }
    }

    /**
     *
     *
     * Publish Status
     *
     * Get Publish Status
     * @group Parameters
     * @authenticated
    Example: abc
     *
     *
     *
     * @header Accept application/json

     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
     * {
    {
    "status": true,
    "code": 200,
    "message": "Data fetched",
    "data": [
    {
    "key": "UNPUBLISHED",
    "value": "UNPUBLISHED"
    },
    {
    "key": "POCKET",
    "value": "POCKET"
    },
    {
    "key": "PUBLISHED",
    "value": "PUBLISHED"
    }
    ],
    "page": 1,
    "per_page": 10
    }
     *}
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @response 200 scenario="No Data found"
     * {
    "status": false,
    "code": 200,
    "message": "No Data found",
    "data": []
    }
     */

    public function getPublishStatus(Request $request)
    {
        try {
            $data = \App\Models\Property::PUBLISH_REQUEST_SELECT;
            $data = [
                ['key' => 'UNPUBLISHED', 'value' => 'UNPUBLISHED'],
                ['key' => 'POCKET', 'value' => 'POCKET'],
                ['key' => 'PUBLISHED', 'value' => 'PUBLISHED'],
            ];
            return $this->sendResponse(true, 'Data fetched', $data, 200);
        } catch (\Throwable $th) {
            return $this->sendResponse(false, 'Something Went Wrong', $th->getMessage(), 500);
        }
    }


     /**
     *
     *
     * Villa Types
     *
     * Get Villa Types
     * @group Parameters
     * @authenticated
    Example: abc
     *
     *
     *
     * @header Accept application/json

     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
     * {
    {
    "status": true,
    "code": 200,
    "message": "Data fetched",
    "data": [
        {
            "key": "SR",
            "value": "Single row"
        },
        {
            "key": "BB",
            "value": "Back to back"
        },
        {
            "key": "CP",
            "value": "Corner Plot"
        }
    ],
    "page": 1,
    "per_page": 10
}
     *}
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @response 200 scenario="No Data found"
     * {
    "status": false,
    "code": 200,
    "message": "No Data found",
    "data": []
    }
     */

     public function getVillaTypes(Request $request)
     {
         try {
             $data = \App\Models\Property::VILLA_TYPE;
             $data = [
                 ['key' => 'SR', 'value' => 'Single row'],
                 ['key' => 'BB', 'value' => 'Back to back'],
                 ['key' => 'CP', 'value' => 'Corner Plot'],
             ];
             return $this->sendResponse(true, 'Data fetched', $data, 200);
         } catch (\Throwable $th) {
             return $this->sendResponse(false, 'Something Went Wrong', $th->getMessage(), 500);
         }
     }


    public function getViews(Request $request)
    {
        try {
            $data = [
                ['key' => 'Beach View', 'value' => 'Beach View'],
                ['key' => 'Burj Al Arab View', 'value' => 'Burj Al Arab View'],
                ['key' => 'Burj Khalifa View', 'value' => 'Burj Khalifa View'],
                ['key' => 'City View', 'value' => 'City View'],
                ['key' => 'Community View', 'value' => 'Community View'],
                ['key' => 'Countryside View', 'value' => 'Countryside View'],
                ['key' => 'Creek View', 'value' => 'Creek View'],
                ['key' => 'Canal View', 'value' => 'Canal View'],
                ['key' => 'DIFC View', 'value' => 'DIFC View'],
                ['key' => 'Dubai Eye View', 'value' => 'Dubai Eye View'],
                ['key' => 'Dubai Fountain View', 'value' => 'Dubai Fountain View'],
                ['key' => 'Downtown Dubai View', 'value' => 'Downtown Dubai View'],
                ['key' => 'Garden View', 'value' => 'Garden View'],
                ['key' => 'Golf Course View', 'value' => 'Golf Course View'],
                ['key' => 'Island View', 'value' => 'Island View'],
                ['key' => 'Lake View', 'value' => 'Lake View'],
                ['key' => 'Marina View', 'value' => 'Marina View'],
                ['key' => 'Palm Jumeirah View', 'value' => 'Palm Jumeirah View'],
                ['key' => 'Park View', 'value' => 'Park View'],
                ['key' => 'Pool View', 'value' => 'Pool View'],
                ['key' => 'Sea View', 'value' => 'Sea View'],
                ['key' => 'Skyline View', 'value' => 'Skyline View'],
                ['key' => 'Sunrise View', 'value' => 'Sunrise View'],
                ['key' => 'Sunset View', 'value' => 'Sunset View'],
                ['key' => 'Waterfront View', 'value' => 'Waterfront View'],
            ];
            return $this->sendResponse(true, 'Data fetched', $data, 200);
        } catch (\Throwable $th) {
            return $this->sendResponse(false, 'Something Went Wrong', $th->getMessage(), 500);
        }
    }

    /**
     *
     *
     * Completion Status
     *
     * Get Completion Status
     * @group Parameters
     * @authenticated
    Example: abc
     *
     *
     *
     * @header Accept application/json

     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
     * {
    {
    "status": true,
    "code": 200,
    "message": "Data fetched",
    "data": [
    {
    "key": "Ready Primary",
    "value": "Ready Primary"
    },
    {
    "key": "Ready Secondary",
    "value": "Ready Secondary"
    },
    {
    "key": "Off-Plan Primary",
    "value": "Off-Plan Primary"
    },
    {
    "key": "Off-Plan Secondary",
    "value": "Off-Plan Secondary"
    }
    ],
    "page": 1,
    "per_page": 10
    }
     *}
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @response 200 scenario="No Data found"
     * {
    "status": false,
    "code": 200,
    "message": "No Data found",
    "data": []
    }
     */
    public function getCompletionStatus(Request $request)
    {
        try {

            // $data = \App\Models\Property::COMPLETION_STATUS_SELECT;
            $data = [
                ['key' => 'Ready Primary', 'value' => 'Ready Primary'],
                ['key' => 'Ready Secondary', 'value' => 'Ready Secondary'],
                ['key' => 'Off-Plan Primary', 'value' => 'Off-Plan Primary'],
                ['key' => 'Off-Plan Secondary', 'value' => 'Off-Plan Secondary'],
            ];
            return $this->sendResponse(true, 'Data fetched', $data, 200);
        } catch (\Throwable $th) {
            return $this->sendResponse(false, 'Something Went Wrong', $th->getMessage(), 500);
        }
    }

    /**
     *
     *
     * Occupancy
     *
     * Get Occupancy
     * @group Parameters
     * @authenticated
    Example: abc
     *
     *
     *
     * @header Accept application/json

     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
     * {
    {
    "status": true,
    "code": 200,
    "message": "Data fetched",
    "data": [
    {
    "id": 4,
    "title": "Owner Occupied",
    "slug": null,
    "thumbnail": null,
    "image": null,
    "color_code": null,
    "status": 1,
    "order": 0,
    "badge": null,
    "icon": null,
    "created_by_id": null,
    "updated_by_id": null,
    "created_at": "2023-02-14T04:30:31.000000Z",
    "updated_at": "2023-02-14T04:30:31.000000Z",
    "deleted_at": null
    },
    {
    "id": 9,
    "title": "Sold",
    "slug": null,
    "thumbnail": null,
    "image": null,
    "color_code": null,
    "status": 1,
    "order": 0,
    "badge": null,
    "icon": null,
    "created_by_id": null,
    "updated_by_id": 173,
    "created_at": "2023-02-14T04:30:31.000000Z",
    "updated_at": "2023-05-10T15:41:08.000000Z",
    "deleted_at": null
    },
    ],
    "page": 1,
    "per_page": 10
    }
     *}
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @response 200 scenario="No Data found"
     * {
    "status": false,
    "code": 200,
    "message": "No Data found",
    "data": []
    }
     */
    public function getOccupancy(Request $request)
    {
        try {
            $data = PropertyStatus::where('status', true)->orderBy('order', 'desc')->get();
            return $this->sendResponse(true, 'Data fetched', $data, 200);
        } catch (\Throwable $th) {
            return $this->sendResponse(false, 'Something Went Wrong', $th->getMessage(), 500);
        }
    }

    /**
     *
     *
     * Exclusive
     *
     * Get Exclusive
     * @group Parameters
     * @authenticated
    Example: abc
     *
     *
     *
     * @header Accept application/json

     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
     * {
    {
    "status": true,
    "code": 200,
    "message": "Data fetched",
    "data": [
    {
    "id": 1,
    "name": "Yes"
    },
    {
    "id": 0,
    "name": "No"
    }
    ],
    "page": 1,
    "per_page": 10
    }
     *}
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @response 200 scenario="No Data found"
     * {
    "status": false,
    "code": 200,
    "message": "No Data found",
    "data": []
    }
     */
    public function getExclusive(Request $request)
    {
        try {
            $data = [
                ['id' => 1, 'name' => 'Yes'],
                ['id' => 0, 'name' => 'No'],
            ];
            return $this->sendResponse(true, 'Data fetched', $data, 200);
        } catch (\Throwable $th) {
            return $this->sendResponse(false, 'Something Went Wrong', $th->getMessage(), 500);
        }
    }

    /**
     *
     *
     * Portals
     *
     * Get Portals
     * @group Parameters
     * @authenticated
    Example: abc
     *
     *
     *
     * @header Accept application/json

     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
     * {
    {
    "status": true,
    "code": 200,
    "message": "Data fetched",
    "data": [
    {
    "id": 23,
    "title": "Part furnished",
    "slug": "part-furnished",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/amenities/00zITIeAm3R3wCwohmRn1dZfTbOfq4kKm8dJn5aD.svg",
    "image": null,
    "color_code": null,
    "status": 1,
    "order": 12,
    "badge": "badge bg-primary",
    "icon": "ri-bar-chart-2-fill",
    "type": "Features",
    "amenity_type": "private",
    "created_by_id": null,
    "updated_by_id": 1,
    "created_at": "2023-02-14T04:30:35.000000Z",
    "updated_at": "2023-05-04T13:01:41.000000Z",
    "deleted_at": null
    },
    {
    "id": 1,
    "title": "Balcony",
    "slug": "balcony",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/amenities/xNvKzK6Cd10El5uiVfHiZJPmKFaaqXQl6g3aLnnk.svg",
    "image": null,
    "color_code": null,
    "status": 1,
    "order": 0,
    "badge": null,
    "icon": null,
    "type": "Features",
    "amenity_type": "private",
    "created_by_id": null,
    "updated_by_id": 263,
    "created_at": "2023-02-14T04:30:35.000000Z",
    "updated_at": "2023-06-27T06:55:53.000000Z",
    "deleted_at": null
    },
    ],
    "page": 1,
    "per_page": 10
    }
     *}
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @response 200 scenario="No Data found"
     * {
    "status": false,
    "code": 200,
    "message": "No Data found",
    "data": []
    }
     */
    public function getPortals(Request $request)
    {
        try {
            $data = Portal::where('status', true)->get();
            return $this->sendResponse(true, 'Data fetched', $data, 200);
        } catch (\Throwable $th) {
            return $this->sendResponse(false, 'Something Went Wrong', $th->getMessage(), 500);
        }
    }

/**
 *
 *
 * Regions
 *
 * Get Regions
 * @group Parameters
 * @authenticated
 * @header Accept application/json

 *
 * @return \Illuminate\Http\JsonResponse
 * @response 200 scenario="Data found"
 * {
{
"status": true,
"code": 200,
"message": "Data fetched",
"data": [
{
"id": 1,
"name": "Dubai",
"status": 1,
"created_by_id": 1,
"updated_by_id": 92,
"deleted_at": null,
"created_at": "2023-02-14T04:29:47.000000Z",
"updated_at": "2023-07-28T13:12:14.000000Z"
},
{
"id": 2,
"name": "Abu Dhabi",
"status": 1,
"created_by_id": null,
"updated_by_id": null,
"deleted_at": null,
"created_at": "2023-02-14T04:35:26.000000Z",
"updated_at": "2023-02-14T04:35:26.000000Z"
},
]
}

 *}
 * @param  \Illuminate\Http\Request  $request
 * @return \Illuminate\Http\Response
 * @response 200 scenario="Owner not found"
 * {
"status": false,
"code": 500,
"message": "No Data found",
"data": []
}
 */
    public function getRegions(Request $request)
    {
        try {
            $data = \App\Models\Region::where('status', true)->get();
            return $this->sendResponse(true, 'Data fetched', $data, 200);
        } catch (\Throwable $th) {
            return $this->sendResponse(false, 'Something Went Wrong', $th->getMessage(), 500);
        }
    }

/**
 *
 *
 * Community
 *
 * Get Community
 * @group Parameters
 * @authenticated
 * @queryParam region_id int   Get all communities of the region.Example: 4
 * @queryParam name string Get all community properties that matches name.Example: 134

 * @header Accept application/json

 *
 * @return \Illuminate\Http\JsonResponse
 * @response 200 scenario="Data found"
 * {
{
"status": true,
"code": 200,
"message": "Data fetched",
"data": [
{
"id": 1,
"region_id": 1,
"name": "Al Aweer",
"alternate_name": null,
"slug": "al-aweer",
"description": null,
"path_name": "Al Aweer, Dubai",
"alternate_path_name": null,
"latitude": "25.1784957",
"longitude": "55.545002",
"created_by_id": 1,
"updated_by_id": 1,
"status": 1,
"created_at": "2020-04-29T20:54:06.000000Z",
"updated_at": "2020-04-29T20:54:06.000000Z",
"deleted_at": null
},
{
"id": 2,
"region_id": 1,
"name": "Zabeel",
"alternate_name": null,
"slug": "zabeel",
"description": null,
"path_name": "Zabeel, Dubai",
"alternate_path_name": null,
"latitude": "25.2182542",
"longitude": "55.2977618",
"created_by_id": 1,
"updated_by_id": 1,
"status": 1,
"created_at": "2020-04-29T20:54:06.000000Z",
"updated_at": "2020-04-29T20:54:06.000000Z",
"deleted_at": null
},
]
}

 *}
 * @param  \Illuminate\Http\Request  $request
 * @return \Illuminate\Http\Response
 * @response 200 scenario="Owner not found"
 * {
"status": false,
"code": 500,
"message": "No Data found",
"data": []
}
 */

    public function getCommunities(Request $request)
    {
        try {
            $query = \App\Models\Community::with(['region:id,name'])->where('status', true);

            if ($request->name) {
                $query->where('name', 'like', '%' . $request->name . '%')->orWhere('alternate_name', 'like', '%' . $request->name . '%');
            }
            if ($request->region_id) {
                $query->where('region_id', $request->region_id);
            }
            $data = $query->get();
            return $this->sendResponse(true, 'Data fetched', $data, 200);
        } catch (\Throwable $th) {
            return $this->sendResponse(false, 'Something Went Wrong', $th->getMessage(), 500);
        }
    }

    /**
     *
     *
     * Sub Communities
     *
     * Get Sub Communities
     * @group Parameters
     * @authenticated
     * @queryParam region_id int  Get all sub communities of the region.Example: 4
     * @queryParam community_id int  Get all sub communities of the community.Example: 34
     * @queryParam name string Get all sub community properties that matches name.Example: 134

     * @header Accept application/json

     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
     * {
    {
    "status": true,
    "code": 200,
    "message": "Data fetched",
    "data": [
    {
    "id": 1,
    "region_id": 1,
    "community_id": 1,
    "name": "Al Aweer Village",
    "alternate_name": null,
    "slug": "al-aweer-village",
    "description": null,
    "path_name": "Al Aweer Village, Al Aweer, Dubai",
    "alternate_path_name": null,
    "latitude": "25.1756573",
    "longitude": "55.5469032",
    "created_by_id": 1,
    "updated_by_id": 1,
    "status": 1,
    "created_at": "2020-04-29T20:54:06.000000Z",
    "updated_at": "2020-04-29T20:54:06.000000Z",
    "deleted_at": null
    },
    {
    "id": 2,
    "region_id": 1,
    "community_id": 1,
    "name": "Warsan Estate",
    "alternate_name": null,
    "slug": "warsan-estate",
    "description": null,
    "path_name": "Warsan Estate, Al Aweer, Dubai",
    "alternate_path_name": null,
    "latitude": "25.18356046",
    "longitude": "55.55390596",
    "created_by_id": 1,
    "updated_by_id": 1,
    "status": 1,
    "created_at": "2020-04-29T20:54:06.000000Z",
    "updated_at": "2020-04-29T20:54:06.000000Z",
    "deleted_at": null
    },
    ]
    }

     *}
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @response 200 scenario="Owner not found"
     * {
    "status": false,
    "code": 500,
    "message": "No Data found",
    "data": []
    }
     */

    public function getSubCommunities(Request $request)
    {
        try {
            $query = \App\Models\SubCommunity::with(['region:id,name', 'community:id,name'])->where('status', true);

            if ($request->name) {
                $query->where('name', 'like', '%' . $request->name . '%')->orWhere('alternate_name', 'like', '%' . $request->name . '%');
            }
            if ($request->region_id) {
                $query->where('region_id', $request->region_id);
            }
            if ($request->community_id) {
                $query->where('community_id', $request->community_id);
            }
            $data = $query->get();
            return $this->sendResponse(true, 'Data fetched', $data, 200);
        } catch (\Throwable $th) {
            return $this->sendResponse(false, 'Something Went Wrong', $th->getMessage(), 500);
        }
    }

    /**
     *
     *
     * Community
     * Get Community
     * @group Parameters
     * @authenticated
     * @queryParam region_id int  Get all community properties of the region.Example: 4
     * @queryParam community_id int Get all community properties of the community.Example: 34
     * @queryParam sub_community_id int Get all community properties of the sub community.Example: 134
     * @queryParam name string Get all community properties that matches name.Example: 134

     * @header Accept application/json

     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
     * {
    {
    "status": true,
    "code": 200,
    "message": "Data fetched",
    "data": [
    {
    "id": 1,
    "region_id": 1,
    "community_id": 1,
    "sub_community_id": 1,
    "name": "Desert Palm",
    "alternate_name": null,
    "slug": "desert-palm",
    "description": null,
    "path_name": "Desert Palm, Al Aweer Village, Al Aweer, Dubai",
    "alternate_path_name": null,
    "latitude": "25.16920802",
    "longitude": "55.44489209",
    "created_by_id": 1,
    "updated_by_id": 1,
    "default_use": 1,
    "alternate_use": 1,
    "status": 1,
    "created_at": "2020-04-29T20:54:06.000000Z",
    "updated_at": "2020-04-29T20:54:06.000000Z",
    "deleted_at": null
    },
    {
    "id": 2,
    "region_id": 1,
    "community_id": 2,
    "sub_community_id": 4,
    "name": "Khafajy Building",
    "alternate_name": null,
    "slug": "khafajy-building",
    "description": null,
    "path_name": "Khafajy Building, Zabeel 2, Zabeel, Dubai",
    "alternate_path_name": null,
    "latitude": "25.2466889",
    "longitude": "55.3089348",
    "created_by_id": 1,
    "updated_by_id": 1,
    "default_use": 1,
    "alternate_use": 1,
    "status": 1,
    "created_at": "2020-04-29T20:54:06.000000Z",
    "updated_at": "2020-04-29T20:54:06.000000Z",
    "deleted_at": null
    },
    ]
    }

     *}
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @response 200 scenario="Owner not found"
     * {
    "status": false,
    "code": 500,
    "message": "No Data found",
    "data": []
    }
     */
    public function getCommunityProperty(Request $request)
    {
        try {
            $query = \App\Models\CommunityProperty::with(['region:id,name', 'community:id,name', 'subCommunity:id,name'])->where('status', true);
            if ($request->name) {
                $query->where('name', 'like', '%' . $request->name . '%')->orWhere('alternate_name', 'like', '%' . $request->name . '%');
            }
            if ($request->region_id) {
                $query->where('region_id', $request->region_id);
            }
            if ($request->community_id) {
                $query->where('community_id', $request->community_id);
            }
            if ($request->sub_community_id) {
                $query->where('sub_community_id', $request->sub_community_id);
            }
            $data = $query->get();
            return $this->sendResponse(true, 'Data fetched', $data, 200);
        } catch (\Throwable $th) {
            return $this->sendResponse(false, 'Something Went Wrong', $th->getMessage(), 500);
        }
    }

    public function getCombinedLocation(Request $request)
    {
        try {
            $query = \App\Models\CommunityProperty::where('status', true);
            if ($request->name) {
                $query->where('name', 'like', '%' . $request->name . '%')->orWhere('alternate_name', 'like', '%' . $request->name . '%');
            }

            $querytwo = \App\Models\SubCommunity::where('status', true);
            if ($request->name) {
                $querytwo->where('name', 'like', '%' . $request->name . '%')->orWhere('alternate_name', 'like', '%' . $request->name . '%');
            }

            // Retrieve data from first query
            $data1 = $query->get();
            // Add location_type attribute to each model in the collection
            $data1->each(function ($item) {
                $item->location_type = 'communityProperty';
            });

            // Retrieve data from second query
            $data2 = $querytwo->get();
            // Add location_type attribute to each model in the collection
            $data2->each(function ($item) {
                $item->location_type = 'sub_community';
            });

            // Combine collections from both queries
            $data = $data1->merge($data2);

            return $this->sendResponse(true, 'Data fetched', $data, 200);
        } catch (\Throwable $th) {
            return $this->sendResponse(false, 'Something Went Wrong', $th->getMessage(), 500);
        }
    }



     /**
     *
     *
     * Listing Lead Status
     * Get Listing Lead Status
     * @group Parameters
     * @authenticated
     * @header Accept application/json

     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
     * {
    {
    "status": true,
    "code": 200,
    "message": "Data fetched",
    "data": [
        {
            "id": 1,
            "name": "New",
            "status": 1,
            "icon": "bx bxs-user-plus",
            "badge": "badge color-bg-17",
            "thumbnail": null,
            "image": null,
            "order": 20,
            "created_by_id": null,
            "updated_by_id": 262,
            "deleted_at": null,
            "deleted_by_id": null,
            "created_at": "2023-06-20T10:53:47.000000Z",
            "updated_at": "2023-08-08T05:57:19.000000Z"
        },
        {
            "id": 2,
            "name": "Assigned",
            "status": 1,
            "icon": "ri ri-thumb-up-line",
            "badge": "badge color-bg-10",
            "thumbnail": null,
            "image": null,
            "order": 40,
            "created_by_id": null,
            "updated_by_id": 262,
            "deleted_at": null,
            "deleted_by_id": null,
            "created_at": "2023-06-20T10:53:47.000000Z",
            "updated_at": "2023-08-08T06:45:32.000000Z"
        },
        {
            "id": 7,
            "name": "Not Answered",
            "status": 1,
            "icon": "bx bxs-phone-off",
            "badge": "badge color-bg-17",
            "thumbnail": null,
            "image": null,
            "order": 50,
            "created_by_id": null,
            "updated_by_id": 262,
            "deleted_at": null,
            "deleted_by_id": null,
            "created_at": null,
            "updated_at": "2023-08-08T06:45:06.000000Z"
        },

    ],
    "page": 1,
    "per_page": 10
}

     *}
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @response 200 scenario="Data not found"
     * {
    "status": false,
    "code": 500,
    "message": "No Data found",
    "data": []
    }
     */
    public function getListingLeadStatus(Request $request)
    {
        try {
            $data = \App\Models\ListingLeadStatus::where('status', true)->orderBy('order', 'asc')->get();
            return $this->sendResponse(true, 'Data fetched', $data, 200);
        } catch (\Throwable $th) {
            return $this->sendResponse(false, 'Something Went Wrong', $th->getMessage(), 500);
        }
    }


    public function getAllParameters(Request $request){
        $listingLeads = \App\Models\ListingLeadStatus::where('status', true)->orderBy('order', 'asc')->get();
        $portals = Portal::where('status', true)->get();
        $propertyCategory = PropertyCategory::where('status', true)->orderBy('order', 'asc')->get();
        $type = Type::where('status', true)->orderBy('order', 'desc')->get();
        $bed = Bed::where('status', true)->orderBy('order', 'desc')->get();
        $bath = Bath::where('status', true)->orderBy('order', 'desc')->get();
        $propertyStatus = PropertyStatus::where('status', true)->orderBy('order', 'desc')->get();
        $mediums = Medium::where('is_lead_medium', 1)->where('status', true)->orderBy('order', 'asc')->get();
        $sources = Source::where('is_lead_source', 1)->where('status', true)->orderBy('order', 'asc')->get();


        return [
            'listingLeads' => $listingLeads,
            'portals' => $portals,
            'propertyCategory' => $propertyCategory,
            'type' => $type,
            'bed' => $bed,
            'bath' => $bath,
            'propertyStatus' => $propertyStatus,
            'mediums' => $mediums,
            'sources' => $sources,
        ];


    }
}
