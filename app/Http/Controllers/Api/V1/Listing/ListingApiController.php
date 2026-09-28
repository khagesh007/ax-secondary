<?php

namespace App\Http\Controllers\Api\V1\Listing;

use App\Events\ChangesRequested;
use App\Events\PocketRequest;
use App\Events\PropertyUpdated;
use App\Events\PublishRequested;
use App\Events\UnpublishRequested;
use App\Http\Controllers\Admin\Listing\PropertyController;
use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\V1\Listing\GetPropertyListRequest;
use App\Http\Resources\ListingResource;
use App\Http\Resources\ListingResourceDetailed;
use App\Http\Resources\PropertyNoteResource;
use App\Http\Resources\Property\PropertyResource;
use App\Http\Resources\Property\PropertyResourceV2;
use App\Http\Tasks\Api\V1\Listing\PropertyListTask;
use App\Http\Tasks\Api\V1\Listing\SearchPropertyList;
use App\Http\Tasks\Api\V1\Listing\PropertyListTaskV2;
use App\Mail\Admin\PropertyUnpublishRequestMail;
use App\Models\Contact;
use App\Models\Lead;
use App\Models\Portal;
use App\Models\Property;
use App\Models\PropertyCycle;
use App\Models\PropertyFeatures;
use App\Models\PropertyMedia;
use App\Models\PropertyNote;
use App\Models\PropertyPortal;
use App\Models\PropertyPrice;
use App\Models\Region;
use App\Models\SubCommunity;
use App\Traits\FileTrait;
use App\Traits\FiltersTrait;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Log;
use Validator;

class ListingApiController extends BaseController
{
    use FiltersTrait;
    use FileTrait;
    public function __construct()
    {

    }
    /**
     *
     *
     * Property List
     *
     * Get a list of Properties. with pagination
     * @group Listings
     * @authenticated
     *
     * @queryParam search string Search by first name, last name, email or mobile Example: John, Doe, johan@demo.com, 1234567890
     * @queryParam page int Page number Example: 1
     * @queryParam ref_no string ref_no for the property.Example: axc-123456
     * @queryParam agent_id string agent_id for the property.Example: 23
     * @queryParam rera_permit_number string Rera permit number for the property.Example: 23
     *
     * @queryParam for string for for the property.Example: Residential for sale
     * @queryParam property_category_id string property_category_id for the property.Example: 5
     * @queryParam bed_id string bed_id for the property.Example: 2
     * @queryParam bath_id string bath_id for the property.Example: 1
     * @queryParam region_id string region_id for the property.Example: 1
     * @queryParam community_id string community_id for the property.Example: 1
     * @queryParam sub_community_id string sub_community_id for the property.Example: 1
     * @queryParam community_property_id string community_property_id for the property.Example: 1
     * @queryParam completion_status string completion_status for the property.Example: Ready Secondary
     * @queryParam furnished string furnished for the property.Example: UNFURNISHED
     * @queryParam publish_status string publish_status for the property.Example: PUBLISHED
     * @queryParam created_at string created_at for the property.2023-02-14 07:35:24
     * @queryParam updated_at string updated_at for the property.2023-02-14 07:35:24
     * @queryParam updated_by_id string Who Updated the property.13
     * @queryParam price_from string for price from.13
     * @queryParam price_to string for price from.13
     * @queryParam property_plot_size_from string for plot_size from.13
     * @queryParam property_plot_size_to string for plot_size to.13

     * @queryParam property_build_up_area_from string for build up area from.13
     * @queryParam property_build_up_area_to string for build up area to.13
     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
     * {

     * }
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @response 200 scenario="Property not found"
     * {
    "status": false,
    "code": 200,
    "message": "Listings data not found",
    "data": []
    }
     */
    public function _index(GetPropertyListRequest $request, PropertyListTask $task)
    {
        // dd($request->all());
        $response = $task->run($request);
        Log::channel('mobileApi')->info('Request From' . auth()->user()->email, ['request' => $request->all()]);

        return $this->sendResponse($response['status'], $response['message'], $response['data'], $response['code']);
    }

    public function index(GetPropertyListRequest $request, PropertyListTask $task)
    {
        // Run the task to get the response
        $response = $task->run($request);

        // Log the request with the user's email
        Log::channel('mobileApi')->info('Request From ' . auth()->user()->email, ['request' => $request->all()]);

        // Generate an ETag based on the response data
        $etag = md5(json_encode($response['data']));

        // Check if the client has a cached version of the response
        if ($request->getETags() && in_array($etag, $request->getETags())) {
            // If the ETag matches, return a 304 Not Modified response
            return response('', 304);
        }

        // Set cache headers
        return $this->sendResponse($response['status'], $response['message'], $response['data'], $response['code'])
            ->header('Cache-Control', 'public, max-age=3600') // Cache for 1 hour
            ->header('ETag', $etag) // Send the ETag
            ->header('Last-Modified', gmdate('D, d M Y H:i:s', time()) . ' GMT'); // Optional Last-Modified header
    }

    /**
     *
     *
     * Property List Post
     *
     * Get a list of Properties. with pagination
     * @group Listings
     * @authenticated
     *
     * @queryParam search string Search by first name, last name, email or mobile Example: John, Doe, johan@demo.com, 1234567890
     * @queryParam page int Page number Example: 1
     * @queryParam ref_no string ref_no for the property.Example: axc-123456
     * @queryParam agent_id string agent_id for the property.Example: 23
     * @queryParam rera_permit_number string Rera permit number for the property.Example: 23
     *
     * @queryParam for string for for the property.Example: Residential for sale
     * @queryParam property_category_id string property_category_id for the property.Example: 5
     * @queryParam bed_id string bed_id for the property.Example: 2
     * @queryParam bath_id string bath_id for the property.Example: 1
     * @queryParam completion_status string completion_status for the property.Example: Ready Secondary
     * @queryParam furnished string furnished for the property.Example: UNFURNISHED
     * @queryParam publish_status string publish_status for the property.Example: PUBLISHED
     * @queryParam created_at string created_at for the property.2023-02-14 07:35:24
     * @queryParam updated_at string updated_at for the property.2023-02-14 07:35:24
     * @queryParam updated_by_id string Who Updated the property.13

     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
     * {

     * }
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @response 200 scenario="Property not found"
     * {
    "status": false,
    "code": 200,
    "message": "Listings data not found",
    "data": []
    }
     */
    public function indexPost(GetPropertyListRequest $request, PropertyListTask $task)
    {
        $response = $task->run($request);
        Log::channel('mobileApi')->info('Request From' . auth()->user()->email, ['request' => $request->all()]);

        return $this->sendResponse($response['status'], $response['message'], $response['data'], $response['code']);
    }

    public function searchListings(GetPropertyListRequest $request, SearchPropertyList $task)
    {
        $response = $task->run($request);
        Log::channel('mobileApi')->info('Request From' . auth()->user()->email, ['request' => $request->all()]);

        return $this->sendResponse($response['status'], $response['message'], $response['data'], $response['code']);
    }

    public function filteredSearch(GetPropertyListRequest $request, PropertyListTaskV2 $task)
    {
        $notAdmin = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent", "Primary Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }
        $q = Property::leftJoin('property_prices as pp', 'properties.id', '=', 'pp.property_id');

        $response = $task->run($request);
        Log::channel('mobileApi')->info('Request From' . auth()->user()->email, ['request' => $request->all()]);

        return $this->sendResponse($response['status'], $response['message'], $response['data'], $response['code']);
    }
    //
    public function getAllProperties(Request $request)
    {
        Log::channel('mobileApi')->info('Request From' . auth()->user()->email, ['request' => $request->all()]);
        // dd($request->all());
        $query = Property::query();

        // $query->where('is_live', true);
        $query->where('is_live', true);
        $query->orderBy('updated_at', 'desc');
        $query->when(!empty($request->search), function ($q) use ($request) {
            return $q->where('title', 'like', '%' . $request->search . '%')
                ->orWhere('unit_no', 'like', '%' . $request->search . '%')
                ->orWhere('ref_no', 'like', '%' . $request->search . '%')
                ->orWhere('description', 'like', '%' . $request->search . '%');
        });

        $query->with(['bed', 'details',
            'agent',
            'propertyCategory.portals',
            'price',
            'bath.portals',
            'amenities.amenity.portals',
            'community.region',
            'subCommunity',
            'communityProperty',
            'photos',
            'floorPlan']);
        $list_per_page = '10';
        $lead_count = $query->count();
        $leads = $query->paginate($list_per_page);

        if ($lead_count > 0) {
            $leads->data = PropertyResource::collection($leads);
            $leads->appends($request->query())->links();
        } else {
            return $this->sendResponse(false, 'Property not found', [], 200);
        }

        return $this->sendResponse(200, 'Properties', $leads, 200);

        // return $this->sendResponse(true, 'Properties data found', PropertyResource::collection($leads), 200);
    }
    /**
     * Listing details
     *
     * Get details of a listing.
     * @group Listings
     * @authenticated
     * @param  \Illuminate\Http\Request  $request
     *
     * @urlParam ref_no string required The ref_no of the Listing. Example: axc_123456

     * @response 200 scenario="not found"{
     * "status": false,
     * "code": 404,
     * "message": "Lead not found",
     * "data": []
     * }
     *
     * @response 200 scenario="success"
     *
     * {


     *   }
     *
     *
     */
    public function show($ref_no, GetPropertyListRequest $request, PropertyListTask $task)
    {
        $response = $task->run($request);
        Log::channel('mobileApi')->info('Request From' . auth()->user()->email, ['request' => $request->all()]);
        return $this->sendResponse($response['status'], $response['message'], $response['data'], $response['code']);
    }
    public function view($ref_no)
    {
        // Log::channel('mobileApi')->info('Request From' . auth()->user()->email ,[ 'request' => $request->all()]);
        $property = Property::with(['bed', 'details',
            'agent',
            'propertyCategory.portals',
            'price',
            'bath.portals',
            'amenities.amenity.portals',
            'community.region',
            'subCommunity',
            'communityProperty',
            'photos',
            'floorPlan'])->where('ref_no', $ref_no)->with(['agent', 'bed', 'bath', 'amenities', 'community', 'subCommunity', 'communityProperty', 'photos', 'floorPlan', 'price', 'details'])->first();
        if (!$property) {
            return $this->sendResponse(false, 'Property not found', [], 200);
        }
        return $this->sendResponse(true, 'Property details', new PropertyResourceV2($property), 200);
    }
    /**
     * Agent Listings
     *
     * Get a list of agent's Properties with pagination
     * @group Listings
     * @authenticated
     *
     * @queryParam search string Search by title, unit_no, ref_no or description Example: Luxurious, 11111, axc-123456, luxurious
     * @queryParam page int Page number Example: 1
     * @queryParam per_page int Per page data Example: 10
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
     * {
     *     "status": 200,
     *     "code": 200,
     *     "message": "Properties",
     *     "data": {
     *     "current_page": 1,
     *     "data": [
     *         {
     *             "id": 1,
     *             "ref_no": "axc-123456",
     *             "title": "Brand New | Sea View | Resort-Style Living",
     *             "unit_no": "1",
     *             "location": "The Moon, Palm Jumeirah, Dubai",
     *             "community": "Palm Jumeirah",
     *             "alternate_community": null,
     *             "sub_community": "The Moon",
     *             "alternate_sub_community": null,
     *             "community_property": "Moon",
     *             "alternate_community_property": null,
     *             "for": "Residential for rent",
     *             "plot_size": null,
     *             "built_up_area": "2222",
     *             "is_live": 1,
     *             "agent": {
     *                 "name": "John Doe",
     *                 "first_name": "John",
     *                 "last_name": "Doe",
     *                 "email": "j.doe@axcapital.ae",
     *                 "secondary_email": null,
     *                 "mobile": "+9711111111111",
     *                 "license_no": "11111",
     *                 "photo": "https://linktophoto"
     *             }
     *         }
     *         ],
     *     "first_page_url": "http://secondary.lan/api/v1/listings/get-agent-listings?page=1",
     *     "from": 1,
     *     "last_page": 1,
     *     "last_page_url": "http://secondary.lan/api/v1/listings/get-agent-listings?page=1",
     *     "links": [
     *         {
     *             "url": null,
     *             "label": "&laquo; Previous",
     *             "active": false
     *         },
     *         {
     *             "url": "http://secondary.lan/api/v1/listings/get-agent-listings?page=1",
     *             "label": "1",
     *             "active": true
     *         },
     *         {
     *             "url": null,
     *             "label": "Next &raquo;",
     *             "active": false
     *         }
     *     ],
     *     "next_page_url": null,
     *     "path": "http://secondary.lan/api/v1/listings/get-agent-listings",
     *     "per_page": 10,
     *     "prev_page_url": null,
     *     "to": 8,
     *     "total": 8
     * },
     * "page": 1,
     * "per_page": 10
     * }
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @response 200 scenario="Property not found"
     * {
     * "status": false,
     * "code": 200,
     * "message": "Property not found",
     * "data": []
     * }
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @response 200 scenario="Not Authorised"
     * {
     * "status": false,
     * "code": 200,
     * "message": "Not Authorised",
     * "data": []
     * }
     */
    public function getAgentListings(Request $request)
    {
        Log::channel('mobileApi')->info('Request From' . auth()->user()->email, ['request' => $request->all()]);

        $notAdmin = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent", "Primary Agent"]);
        $admin = auth()->user()->hasRole(["Admin", "Super Admin"]);

        if (!($notAdmin || $admin)) {
            return $this->sendResponse(false, 'Not Authorised', [], 200);
        }

        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }
        $query = Property::query();

        $query->when($notAdmin, function ($q) use ($childrenIds) {
            $q->where(function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            });
        });
        $query->where('is_live', true);
        $query->orderBy('id', 'desc');
        $query->when(!empty($request->search), function ($q) use ($request) {
            return $q->where('title', 'like', '%' . $request->search . '%')
                ->orWhere('unit_no', 'like', '%' . $request->search . '%')
                ->orWhere('ref_no', 'like', '%' . $request->search . '%')
                ->orWhere('description', 'like', '%' . $request->search . '%');
        });
        $query->with([
            'bed', 'details',
            'agent',
            'propertyCategory.portals',
            'price',
            'bath.portals',
            'amenities.amenity.portals',
            'community.region',
            'subCommunity',
            'communityProperty',
            'photos',
            'floorPlan',
        ]);
        $list_per_page = $request->per_page ?? 10;
        $lead_count = $query->count();
        $leads = $query->paginate($list_per_page);

        if ($lead_count > 0) {
            $leads->data = PropertyResource::collection($leads);
            $leads->appends($request->query())->links();
        } else {
            return $this->sendResponse(false, 'Property not found', [], 200);
        }

        return $this->sendResponse(200, 'Properties', $leads, 200);
    }

    /**
     *
     *
     * Listing Create
     *
     * Store Listing.
     * @group Listings
     * @authenticated
     * @queryParam agent_Id int required int Agents id.Example: 4
     * @queryParam owner_id int owners id.Example: 31
     * @queryParam developer_id int developer id.Example: 3
     * @queryParam for string required Category of the property.Example: Residential for rent
     * @queryParam property_category_id int required Property Category for the property.Example: 4
     * @queryParam bed_id int Beds id for the property.Example: 4
     * @queryParam bath_id int Baths id for the property.Example: 4
     * @queryParam location_id int location id for the property.Example: 7876
     * @queryParam location_type string Location type for the property.Example: 7876?sub_community
     * @queryParam unit_no string Unit no for the property.Example: 78UC
     * @queryParam built_up_area string build up area for the property.Example: 120
     * @queryParam plot_size string plot_size for the property.Example: 70
     * @queryParam publish_status required string publish_status for the property.Example: DRAFT
     * @queryParam rera_permit_number string rera_permit_number for the property.Example: 9009878
     * @queryParam parking string parking for the property.Example: 1
     * @queryParam build_year string build_year for the property.Example: 2023
     * @queryParam payment_method string payment_method for the property.Example: cash
     * @queryParam down_payment_price string down_payment_price for the property.Example: 12908
     * @queryParam title string title for the property.Example: Luxury Living | 3 Bedroom | Lagoon View
     * @queryParam price string price for the property.Example: 120000000
     * @queryParam description string description for the property.Example: lorem ipsum
     * @queryParam features string features for the property.Example: array of amenities [1,2,3,4,5]
     * @queryParam furnished string furnished for the property.Example: FURNISHED
     * @queryParam completion_status string completion_status for the property.Example: Off-Plan Secondary
     * @queryParam property_status_id int property_status_id for the property.Example: 1
     * @queryParam availability_date string availability_date for the property. Example: 2023-08-08
     * @queryParam exclusive string exclusive for the property.Example: 1
     * @queryParam rented_at string rented_at for the property.Example: 2023-08-08
     * @queryParam rented_until string rented_until for the property.Example: 2023-10-08
     * @queryParam next_available string next_available for the property.Example: 2023-10-10
     * @queryParam remind string remind for the property.Example: 1
     * @queryParam portals string portals for the property.Example: array of portal ids [1,2,3]
     * @queryParam floor_plan string floor_plan for the property.Example: file of floor plan
     * @queryParam video_link string video_link for the property.Example: https://linktoqr
     * @queryParam video_360_link string video_360_link for the property.Example: https://linktoqr
     * @queryParam qr_link string qr_link for the property.Example: https://linktoqr
     * @queryParam cheques string cheques for the property.Example: 1908
     * @queryParam title_deed_text string title_deed_text for the property.Example: 1908
     * @queryParam other_documents string other_documents for the property.Example: 1908
     * @queryParam contract_start string contract_start for the property.Example: 2023-7-10
     * @queryParam contract_end string contract_end for the property.Example: 2023-10-10
     *
     *
     *
     * @header Accept application/json
     * @header X-Requested-With XMLHttpRequest

     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
     * {
    {
    "status": "1",
    "msg": "Listing Added Successfully",
    "id": 16681
    }
     *}
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @response 200 scenario="User not found"
     * {
    "status": false,
    "code": 500,
    "msg": "Something Went wrong",
    }
     */

    public function createListing(Request $request)
    {
        if ($request->portals) {
            $datatype = gettype($request->portals);
            Log::channel('mobileApi')->info('Datatype of Portals is ' . $datatype);
        }

        $propertyContriller = new PropertyController();
        $response = $propertyContriller->storeRevamped($request);
        Log::channel('mobileApi')->info('Request From' . auth()->user()->email, ['request' => $request->all()]);
        return $response;
    }

/**
 *
 *
 * Listing Update
 *
 * Update Listing.
 * @group Listings
 * @authenticated
 * @queryParam id int required int Property id.Example: 4
 * @queryParam agent_Id int required int Agents id.Example: 4
 * @queryParam owner_id int owners id.Example: 31
 * @queryParam developer_id int developer id.Example: 3
 * @queryParam for string required Category of the property.Example: Residential for rent
 * @queryParam property_category_id int required Property Category for the property.Example: 4
 * @queryParam bed_id int Beds id for the property.Example: 4
 * @queryParam bath_id int Baths id for the property.Example: 4
 * @queryParam location_id int location id for the property.Example: 7876
 * @queryParam location_type string Location type for the property.Example: 7876?sub_community
 * @queryParam unit_no string Unit no for the property.Example: 78UC
 * @queryParam built_up_area string build up area for the property.Example: 120
 * @queryParam plot_size string plot_size for the property.Example: 70
 * @queryParam publish_status required string publish_status for the property.Example: DRAFT
 * @queryParam rera_permit_number string rera_permit_number for the property.Example: 9009878
 * @queryParam parking string parking for the property.Example: 1
 * @queryParam build_year string build_year for the property.Example: 2023
 * @queryParam payment_method string payment_method for the property.Example: cash
 * @queryParam down_payment_price string down_payment_price for the property.Example: 12908
 * @queryParam title string title for the property.Example: Luxury Living | 3 Bedroom | Lagoon View
 * @queryParam price string price for the property.Example: 120000000
 * @queryParam description string description for the property.Example: lorem ipsum
 * @queryParam features string features for the property.Example: array of amenities [1,2,3,4,5]
 * @queryParam furnished string furnished for the property.Example: FURNISHED
 * @queryParam completion_status string completion_status for the property.Example: Off-Plan Secondary
 * @queryParam property_status_id int property_status_id for the property.Example: 1
 * @queryParam availability_date string availability_date for the property.Example 2023-08-08
 * @queryParam exclusive string exclusive for the property.Example: 1
 * @queryParam rented_at string rented_at for the property.Example: 2023-08-08
 * @queryParam rented_until string rented_until for the property.Example: 2023-10-08
 * @queryParam next_available string next_available for the property.Example: 2023-10-10
 * @queryParam remind string remind for the property.Example: 1
 * @queryParam portals string portals for the property.Example: array of portal ids [1,2,3]
 * @queryParam floor_plan string floor_plan for the property.Example: file of floor plan
 * @queryParam video_link string video_link for the property.Example: https://linktoqr
 * @queryParam video_360_link string video_360_link for the property.Example: https://linktoqr
 * @queryParam qr_link string qr_link for the property.Example: https://linktoqr
 * @queryParam cheques string cheques for the property.Example: 1908
 * @queryParam title_deed_text string title_deed_text for the property.Example: 1908
 * @queryParam other_documents string other_documents for the property.Example: 1908
 * @queryParam contract_start string contract_start for the property.Example: 2023-7-10
 * @queryParam contract_end string contract_end for the property.Example: 2023-10-10
 * @queryParam isPublishRequest int send isPublishRequest 1 for send for live.Example: 1
 * @queryParam isPocketRequest int send isPocketRequest 1 for send for saving listing as Pocket.Example: 1
 *
 * @header Accept application/json
 * @header X-Requested-With XMLHttpRequest

 *
 * @return \Illuminate\Http\JsonResponse
 * @response 200 scenario="Data found"
 * {
{
"status": "1",
"msg": "Listing Updated Successfully",
"id": 16681
}
 *}
 * @param  \Illuminate\Http\Request  $request
 * @return \Illuminate\Http\Response
 * @response 200 scenario="User not found"
 * {
"status": false,
"code": 500,
"msg": "Something Went wrong",
}
 */

    public function updateListing(Request $request)
    {
        Log::channel('mobileApi')->info('Request From ' . auth()->user()->email, ['request' => $request->all()]);
        if ($request->portals) {
            $datatype = gettype($request->portals);
            Log::channel('mobileApi')->info('Datatype of Portals is ' . $datatype);
        }
        // Checking if the user is authorized to perform this action
        $property = Property::find($request->id);
        // dd($request->all());
        $notAdmin = $this->notAdmin();
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
            if (!in_array($property->agent_id, $childrenIds)) {
                $result = array('status' => '0', 'msg' => 'You are not authorized to perform this action', 'error' => '', 'line' => '');
                return response()->json($result, 422);
            }
        }
        // Checking if the user is authorized to perform this action
        $rules = [
            "id" => "required|exists:properties,id",
            "agent_id" => "required|exists:users,id",
            "owner_id" => "nullable|exists:contacts,id",
            "developer_id" => "nullable|exists:developers,id",
            // "for" => "required|string",
            "property_category_id" => "required|exists:property_categories,id",
            "bed_id" => "nullable|exists:beds,id",
            "bath_id" => "nullable|exists:baths,id",
            "location_type" => "required|string",
            "location_id" => "required",
            "unit_no" => "required",
            "built_up_area" => "nullable",
            "plot_size" => "nullable",
            "publish_status" => "nullable",
            // "rera_permit_number" => "required_if:publish_status,PUBLISHED",
            "parking" => "nullable|numeric",
            "title" => "nullable|string|min:35|max:50",
            "price" => "nullable",
            "payment_method" => "required",
            "down_payment_price" => "nullable",
            "build_year" => "nullable|numeric",
            "description" => "nullable|string|min:750|max:2500",
            "features" => "nullable|array",
            "furnished" => "nullable",
            "completion_status" => "nullable|string",
            "property_status_id" => "nullable|exists:property_statuses,id",
            "availability_date" => "nullable|date",
            "exclusive" => "nullable",
            "rented_at" => "nullable|numeric",
            "rented_until" => "nullable|date",
            "next_available" => "nullable|date",
            "remind" => "nullable|string",
            "portals" => "nullable|array",
            "floor_plan" => ['nullable', 'mimes:jpg,jpeg,png'],
            "video_link" => "nullable|string",
            "video_360_link" => "nullable|string",
            "qr_link" => "nullable|string",
            "cheques" => "nullable|string",
            "title_deed_text" => "nullable|string",
            "other_documents" => "nullable|array",
            "contract_start" => "nullable|date",
            "contract_end" => "nullable|date|after_or_equal:contract_start",
            // 'contract_number' => 'nullable|string',
        ];

        if ($request->isPublishRequest || $request->publish_status == "PUBLISHED") {
            /* If Property is going to be published or pocket use this validation rule */
            $rules["title"] = "required|string|min:35|max:50";
            $rules["description"] = "required|string|min:750|max:2500";
            $rules["price"] = "required";
            $rules["owner_id"] = "required|exists:contacts,id";
            $rules["bed_id"] = "required|exists:beds,id";
            $rules["bath_id"] = "required|exists:baths,id";
            $rules["built_up_area"] = "required";
            $rules["parking"] = "required|numeric";
            $rules["furnished"] = "required";
            $rules["exclusive"] = "required";
            $rules["contract_start"] = "required|date";
            $rules["contract_end"] = "required|date|after_or_equal:contract_start";
        }

        if ($property->is_live || $property->publish_status == "PUBLISHED") {
            $rules["for"] = "nullable|string";
            $rules["property_category_id"] = "nullable|string";
            $rules["unit_no"] = "nullable|string";
        } else {
            $rules["for"] = "required|string";
            $rules["property_category_id"] = "required|string";
            $rules["unit_no"] = "required|string";
        }

        $validatedData = $request->validate($rules);

        try {
            /* This function gets the communityProperty and subCommunity ids */
            $validatedData = $this->getLocationData($validatedData);
            if ($validatedData['down_payment_price']) {
                $validatedData['down_payment_price'] = str_replace(",", "", $validatedData['down_payment_price']) ?? null;
            }

            /* Owner email required to publish the listing */
            if ($request->isPublishRequest == '1') {
                // get owner contact
                $owner = Contact::find($request->owner_id);
                // if no email then return error
                if (empty($owner->email)) {
                    return response()->json(['msg' => 'Owner email is required'], 422);
                }
            }
            /* For Pocket listing atleast one image is required */
            if (($request->publish_status == 'POCKET' || $request->isPocketRequest) && (!$request->isPublishRequest)) {
                if ($property->photos->count() == 0) {
                    return response()->json(['msg' => 'Please add at least one photo'], 422);
                }
            }

            $approval = false;

            if ($request->contract_start) {
                $contract_start = date('Y-m-d', strtotime($request->contract_start));
                $validatedData['contract_start'] = $contract_start;
                if ($request->contract_end) {
                    $contract_end = date('Y-m-d', strtotime($request->contract_end));
                    $validatedData['contract_end'] = $contract_end;
                } else {
                    $validatedData['contract_end'] = date('Y-m-d', strtotime("+2 months", strtotime($contract_start)));
                }
            }

            if ($request->exclusive == 1) {
                $exclusive_dates = explode("to", $request->exclusive_dates);
                $exclusive_start = date('Y-m-d', strtotime($exclusive_dates[0]));
                $validatedData["exclusive_start"] = $exclusive_start ?? null;

                if (isset($exclusive_dates[1])) {
                    $exclusive_end = date('Y-m-d', strtotime($exclusive_dates[1]));
                    $validatedData["exclusive_end"] = $exclusive_end;
                } else {
                    $validatedData["exclusive_end"] = null;
                }

            }
            /* If Publish Status is null for any reason keep the same as old */

            $validatedData["publish_status"] = $validatedData['publish_status'] ?? $property->publish_status;
            if ($notAdmin && (in_array($property->publish_status, ["PUBLISHED", "POCKET"]) || $validatedData['publish_status'] == "POCKET" || $property->is_live)) {
                $approval = true;
                if ($property->is_live == true && $validatedData['publish_status'] == "DRAFT") {
                    $validatedData['publish_status'] = $property->publish_status;
                }
            }
            $validatedData["view"] = json_encode($request->view);
            $validatedData["villa_type"] = json_encode($request->villa_type) ?? null;
            $orignal = $property->getOriginal();

            // This code might not be used in new approach
            if (($request->isPocketRequest || $validatedData['publish_status'] == "POCKET") && $property->publish_status != "POCKET") {
                $propertyObj2 = Property::find($property->id);
                $propertyObj2->withoutApproval()->update(['publish_status' => "PENDING", "request_status_type" => "POCKET"]);
                PropertyNote::create([
                    "property_id" => $request->id,
                    "note" => "Publish as pocket request created",
                    "created_by_id" => auth()->id(),
                ]);
                event(new PocketRequest($property));

            }
            // dd($approval);
            /* If Approval is requied for agent use the below function */
            if ($approval) {

                $existingAttributes = $property->getAttributes();
                $commonKeys = array_intersect_key($validatedData, $existingAttributes);
                $changes = array_diff_assoc($commonKeys, $existingAttributes);
                $price_of_property = $property?->price?->price ?? null;

                $requestedPrice = str_replace(",", "", $request->price);

                if ($changes || $price_of_property != $requestedPrice) {
                    $validatedData["updated_by_id"] = auth()->id();
                    $validatedData["updated_at"] = now();
                    event(new ChangesRequested($property));
                }
                $property->property_status_id = $request['property_status_id'];
                $property->update($validatedData);

            } else {

                if ($validatedData["publish_status"] == 'PUBLISHED') {

                    $validatedData['is_live'] = true;
                    if ($validatedData["publish_status"] == "PUBLISHED" && $property->publish_status != "PUBLISHED") {
                        $validatedData['published_date'] = now();
                    }

                    $validatedData['request_status_type'] = "PUBLISHED";
                    $validatedData["updated_by_id"] = auth()->id();
                    $validatedData["updated_at"] = now();
                }
                // If Property is live and someone changes the status if will be removed from live
                if ($property->is_live == true && (!in_array($validatedData["publish_status"], ["PENDING", "REFUSED", "PUBLISHED"])) && auth()->user()->hasRole(["Admin", "Super Admin"])
                ) {
                    $validatedData['is_live'] = false;
                    if ($property->unpublished_date == null) {
                        $validatedData['unpublished_date'] = now();
                    }
                    $validatedData['request_status_type'] = null;
                }
                if ($validatedData["publish_status"] == "POCKET") {
                    $validatedData['is_live'] = false;
                    $validatedData['request_status_type'] = "POCKET";
                }
                $property->property_status_id = $request['property_status_id'];
                $property->withoutApproval()->update($validatedData);

            }

            // additional logging
            $dirty = !empty($property->getDirty()) ? $property->getDirty() : $property->getChanges();
            $orignal = collect($orignal)
                ->only(collect($dirty)->keys())
                ->toArray();
            $arr = ['attributes' => $dirty,
                'old' => !empty($orignal) ? $orignal : $property->getOriginalMatchingChanges()];

            activity()
                ->performedOn($property)
                ->causedBy(auth()->user())
                ->withProperties($arr)
                ->event('updated')
                ->log(auth()->user()->first_name . ' has updated listing ' . $property->ref_no);

            if (isset($validatedData['price'])) {
                // dd($approval);
                $price_obj = PropertyPrice::where('property_id', $property->id)->first() ?? new PropertyPrice();
                $price_obj->property_id = $property->id;
                $old_price = $price_obj->price;
                $price_obj->price = str_replace(",", "", $request->price);
                if ($approval) {
                    $price_obj->save();
                    if ($changes || $price_of_property != $requestedPrice) {
                        $propertyObj2 = Property::find($property->id);
                        $propertyObj2->withoutApproval()->update(['publish_status' => 'PENDING']);
                    }

                    $original = $price_obj->getOriginal() ?? [];
                    $dirty = $price_obj->getDirty() ?? [];
                    //log activity
                    if (strval($old_price) !== strval(cleanData($request->price))) {

                        $arr = ['attributes' => $dirty,
                            'old' => $original, 'property_id' => $property->id];

                        activity()
                            ->performedOn($price_obj)
                            ->causedBy(auth()->user())
                            ->withProperties($arr)
                            ->event('created')
                            ->log(auth()->user()->first_name . ' has requested to change the price.');
                    }
                } else {
                    // dd($price_obj);
                    $original = $price_obj->getOriginal() ?? [];
                    $dirty = $price_obj->getDirty() ?? [];
                    $old_price_obj = $price_obj->price_on_application;

                    $price_obj->price_on_application = (isset($request->price_on_application) ? 1 : 0);
                    $arr = ['attributes' => $dirty,
                        'old' => $original, 'property_id' => $property->id];
                    if (strval($old_price) !== strval(cleanData($request->price))) {

                        activity()
                            ->performedOn($price_obj)
                            ->causedBy(auth()->user())
                            ->withProperties($arr)
                            ->event('created')
                            ->log(auth()->user()->first_name . ' has changed the price.');
                    }
                    $price_obj->withoutApproval()->save();

                }
            }

            if ($request->hasFile("floor_plan")) {
                $request->validate([
                    "floor_plan" => ['nullable', 'mimes:jpg,jpeg,png'],
                ]);

                $images = $this->uploadImageAndThumbWithWatermark($request->file("floor_plan"));
                if (!empty($images)) {
                    $floorPlanChanged = null;

                    if ($approval) {
                        $propObj2 = Property::find($property->id);
                        $propObj2->withoutApproval()->update(['publish_status' => 'PENDING']);
                        $floorPlan = PropertyMedia::where('property_id', $property->id)->where('type', "Floor Plan")->exists();
                        $floorPlanChanged = ($floorPlan ? "Updated" : "Added");
                    }
                    PropertyMedia::updateOrCreate([
                        "property_id" => $property->id,
                        "type" => "Floor Plan",
                    ], [
                        "path" => $images["image"] ?? null,
                        "media_changed" => $floorPlanChanged,
                        "original" => $images["original"] ?? null,
                        "thumbnail" => $images["thumbnail"] ?? null,
                        "created_by_id" => auth()->id(),
                    ]);

                }
            }

            if ($request->hasFile("title_deed")) {
                $path = $this->uploadDocument($request->file("title_deed"), "listings/" . $property->ref_no . "/Title Deed");
                if (!empty($path)) {

                    $titleDeedChanged = null;
                    if ($approval) {
                        $propObj2 = Property::find($property->id);
                        $propObj2->withoutApproval()->update(['publish_status' => 'PENDING']);

                        $titleDeed = PropertyMedia::where('property_id', $property->id)->where('type', 'Other')->where('title', 'Title Deed')->exists();

                        $titleDeedChanged = $titleDeed ? "Updated" : "Added";
                    }
                    $title_deed = PropertyMedia::where('property_id', $property->id)->where('type', 'Other')->where('title', 'Title Deed')->first() ?? new PropertyMedia();
                    $title_deed->property_id = $property->id;
                    $title_deed->type = 'Other';
                    $title_deed->media_changed = $titleDeedChanged;
                    $title_deed->password = $request->title_deed_password;
                    $title_deed->title = 'Title Deed';
                    $title_deed->path = $path ?? null;
                    $title_deed->created_by_id = auth()->id();
                    $original = $title_deed->getOriginal() ?? [];
                    $dirty = $title_deed->getDirty() ?? [];
                    $title_deed->save();
                    //log activity

                    $arr = ['attributes' => $dirty,
                        'old' => $original, 'property_id' => $property->id];
                    activity()
                        ->performedOn($title_deed)
                        ->causedBy(auth()->user())
                        ->withProperties($arr)
                        ->event('created')
                        ->log(auth()->user()->first_name . ' has uploaded property title deed document');
                }

            }

            if ($request->title_deed_password) {
                $document = PropertyMedia::where('property_id', $property->id)->where('type', 'Other')->where('title', 'Title Deed')->first();
                if ($document) {
                    $document->password = $request->title_deed_password;
                    $document->save();
                }
            }

            if ($request->hasFile("owner_passport_copy")) {
                // dd($request->owner_passport_copy_password);
                $path = $this->uploadDocument($request->file("owner_passport_copy"), "listings/" . $property->ref_no . "/Owner Passport Copy");
                if (!empty($path)) {

                    $ownerPassportChanged = null;
                    if ($approval) {
                        $propObj2 = Property::find($property->id);
                        $propObj2->withoutApproval()->update(['publish_status' => 'PENDING']);
                        $ownerPassport = PropertyMedia::where('property_id', $property->id)->where('type', 'Other')->where('title', 'Owner Passport Copy')->exists();

                        $ownerPassportChanged = $ownerPassport ? "Updated" : "Added";
                    }
                    $owner_passport = PropertyMedia::where('property_id', $property->id)->where('type', 'Other')->where('title', 'Owner Passport Copy')->first() ?? new PropertyMedia();
                    $owner_passport->property_id = $property->id;
                    $owner_passport->type = 'Other';
                    $owner_passport->media_changed = $ownerPassportChanged;
                    $owner_passport->password = $request->owner_passport_copy_password;

                    $owner_passport->title = 'Owner Passport Copy';
                    $owner_passport->path = $path ?? null;
                    $owner_passport->created_by_id = auth()->id();
                    $original = $owner_passport->getOriginal() ?? [];
                    $dirty = $owner_passport->getDirty() ?? [];
                    $owner_passport->save();
                    //log activity

                    $arr = ['attributes' => $dirty,
                        'old' => $original, 'property_id' => $property->id];
                    activity()
                        ->performedOn($owner_passport)
                        ->causedBy(auth()->user())
                        ->withProperties($arr)
                        ->event('created')
                        ->log(auth()->user()->first_name . ' has uploaded owner passport copy');
                }
            }

            if ($request->owner_passport_copy_password) {
                $document = PropertyMedia::where('property_id', $property->id)->where('type', 'Other')->where('title', 'Owner Passport Copy')->first();
                if ($document) {
                    $document->password = $request->owner_passport_copy_password;
                    $document->save();
                }
            }

            if ($request->hasFile("signed_listing_form")) {
                // dd($request->signed_listing_form_password);

                $path = $this->uploadDocument($request->file("signed_listing_form"), "listings/" . $property->ref_no . "/Signed Listing Form");
                if (!empty($path)) {

                    $signedFormChanged = null;
                    if ($approval) {
                        $propObj2 = Property::find($property->id);
                        $propObj2->withoutApproval()->update(['publish_status' => 'PENDING']);
                        $signedForm = PropertyMedia::where('property_id', $property->id)->where('type', 'Other')->where('title', 'Signed Listing Form')->exists();

                        $signedFormChanged = $signedForm ? "Updated" : "Added";
                    }
                    $signed_form = PropertyMedia::where('property_id', $property->id)->where('type', 'Other')->where('title', 'Signed Listing Form')->first() ?? new PropertyMedia();
                    $signed_form->property_id = $property->id;
                    $signed_form->type = 'Other';
                    $signed_form->password = $request->signed_listing_form_password;

                    $signed_form->title = 'Signed Listing Form';
                    $signed_form->media_changed = $signedFormChanged;
                    $signed_form->path = $path ?? null;
                    $signed_form->created_by_id = auth()->id();
                    $orignal = $signed_form->getOriginal() ?? [];
                    $dirty = $signed_form->getDirty() ?? [];
                    $signed_form->save();
                    //log activity
                    $arr = ['attributes' => $dirty,
                        'old' => $orignal, 'property_id' => $property->id];
                    activity()
                        ->performedOn($signed_form)
                        ->causedBy(auth()->user())
                        ->withProperties($arr)
                        ->event('created')
                        ->log(auth()->user()->first_name . ' has uploaded signed listing form');
                }
            }
            if ($request->signed_listing_form_password) {
                $document = PropertyMedia::where('property_id', $property->id)->where('type', 'Other')->where('title', 'Signed Listing Form')->first();
                if ($document) {
                    $document->password = $request->signed_listing_form_password;
                    $document->save();
                }
            }

            // dd($request->other_documents);
            foreach ($request->other_documents ?? [] as $key => $document) {

                if (!empty($document["file"])) {
                    $path = $this->uploadDocument($document["file"], "listings/" . $property->ref_no . "/" . $document['title']);
                    if (!empty($path)) {
                        if ($request->other_documents_old_id[$key]['old_id']) {
                            $media = PropertyMedia::where("id", $request->other_documents_old_id[$key]['old_id'])->where("property_id", $property->id)->update(['path' => $path ?? null, 'password' => $document['password']]);
                            $custom_doc = PropertyMedia::find($request->other_documents_old_id[$key]['old_id']);
                            $custom_doc->path = $path ?? null;
                            $orignal = $custom_doc->getOriginal() ?? [];
                            $dirty = $custom_doc->getDirty() ?? [];
                            $custom_doc->save();

                        } else {
                            $custom_doc = new PropertyMedia();
                            $custom_doc->property_id = $property->id;
                            $custom_doc->type = 'Other';
                            $custom_doc->title = $document["title"] ?? "Other";
                            $custom_doc->path = $path ?? null;
                            $custom_doc->password = $document['password'];
                            $custom_doc->created_by_id = auth()->id();
                            $orignal = $custom_doc->getOriginal() ?? [];
                            $dirty = $custom_doc->getDirty() ?? [];
                            $custom_doc->save();

                        }
                        $arr = ['attributes' => $dirty,
                            'old' => $orignal, 'property_id' => $property->id];
                        activity()
                            ->performedOn($custom_doc)
                            ->causedBy(auth()->user())
                            ->withProperties($arr)
                            ->event('created')
                            ->log(auth()->user()->first_name . ' has uploaded ' . $custom_doc->title . ' document');
                    }

                } else {
                    if ($request->other_documents_old_id[$key]['old_id']) {
                        $custom_doc = PropertyMedia::find($request->other_documents_old_id[$key]['old_id']);
                        $custom_doc->password = $document['password'];
                        $custom_doc->title = $document['title'];
                        $custom_doc->save();
                    }
                }
            }
            if (auth()->user()->hasRole(["Admin", "Super Admin", "Admin - Abu Dhabi", 'Agent'])) {
                // dd($request->all());
                $old_portals = PropertyPortal::where("property_id", $property->id)->pluck("portal_id")->toArray();
                PropertyPortal::where("property_id", $property->id)->whereNotIn("portal_id", $request->portals ?? [])->delete();
                foreach ($request->portals ?? [] as $portal) {
                    PropertyPortal::updateOrCreate([
                        "property_id" => $property->id,
                        "portal_id" => $portal,
                    ], [
                        "created_by_id" => auth()->id(),
                        "updated_by_id" => auth()->id(),
                    ]);
                }
                // make activity log
                $new_portals = PropertyPortal::where("property_id", $property->id)->pluck("portal_id")->toArray();
                $diff = array_diff($old_portals, $new_portals);
                // dd($diff, $old_portals, $new_portals);
                if (!empty($diff)) {
                    $portal = Portal::whereIn("id", $diff)->pluck("name")->toArray();

                    // names of old portals in array
                    $old = Portal::whereIn("id", $old_portals)->pluck("name")->toArray();
                    // names of new portals in array
                    $new = Portal::whereIn("id", $new_portals)->pluck("name")->toArray();

                    $arr = ['attributes' => $new,
                        'old' => $old, 'property_id' => $property->id];
                    activity()
                        ->performedOn($property)
                        ->causedBy(auth()->user())
                        ->withProperties($arr)
                        ->event('created')
                        ->log(auth()->user()->first_name . ' has updated portals for listing ' . $property->ref_no);
                }

            }

            PropertyFeatures::where("property_id", $property->id)->whereNotIn("property_amenity_id",
                $request->features ?? [])->delete();
            foreach ($request->features ?? [] as $feature) {
                PropertyFeatures::updateOrCreate([
                    "property_id" => $property->id,
                    "property_amenity_id" => $feature,
                ], [
                    "created_by_id" => auth()->id(),
                    "updated_by_id" => auth()->id(),
                ]);
            }
            if ($request->isPublishRequest) {
                $params = new Request();

                $params->replace(['id' => $property->id, 'request_note' => "Publish Request Sent", "return_array" => true]);
                $publish_request = $this->publishRequest($params);
                // dd($publish_request);
                if ($publish_request['status'] == "1") {
                    // $propertyObj2 = Property::find($property->id);
                    // $propertyObj2->withoutApproval()->update(['publish_status' => 'PENDING']);
                    $result = array('status' => '1', 'msg' => 'Listing added and Listing publish request sent successfully.', "id" => $property->id);
                    return json_encode($result);
                }
            }

            $result = array('status' => '1', 'msg' => 'Listing updated successfully', "id" => $property->id);

            event(new PropertyUpdated($property));
        } catch (\Exception $exception) {
            info($exception);
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage(), 'line' => $exception->getLine());
            return response()->json($result, 500);
        }
        return json_encode($result);
    }

    public function _updateListing(Request $request)
    {
        // Checking if the user is authorized to perform this action
        $property = Property::find($request->id);
        $notAdmin = $this->notAdmin();
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
            if (!in_array($property->agent_id, $childrenIds)) {
                $result = array('status' => '0', 'msg' => 'You are not authorized to perform this action', 'error' => '', 'line' => '');
                return response()->json($result, 422);
            }
        }
        // Checking if the user is authorized to perform this action
        $rules = [
            "id" => "required|exists:properties,id",
            "agent_id" => "required|exists:users,id",
            "owner_id" => "nullable|exists:contacts,id",
            "developer_id" => "nullable|exists:developers,id",
            // "for" => "required|string",
            "property_category_id" => "required|exists:property_categories,id",
            "bed_id" => "nullable|exists:beds,id",
            "bath_id" => "nullable|exists:baths,id",
            "location_type" => "required|string",
            "location_id" => "required",
            "unit_no" => "required",
            "built_up_area" => "nullable",
            "plot_size" => "nullable",
            "publish_status" => "nullable",
            "rera_permit_number" => "required_if:publish_status,PUBLISHED",
            "parking" => "nullable|numeric",
            "title" => "nullable|string|min:35|max:50",
            "price" => "nullable",
            "payment_method" => "required",
            "down_payment_price" => "nullable",
            "build_year" => "nullable|numeric",
            "description" => "nullable|string|min:750|max:2500",
            "features" => "nullable|array",
            "furnished" => "nullable",
            "completion_status" => "nullable|string",
            "property_status_id" => "nullable|exists:property_statuses,id",
            "availability_date" => "nullable|date",
            "exclusive" => "nullable",
            "rented_at" => "nullable|numeric",
            "rented_until" => "nullable|date",
            "next_available" => "nullable|date",
            "remind" => "nullable|string",
            "portals" => "nullable|array",
            "floor_plan" => ['nullable', 'mimes:jpg,jpeg,png'],
            "video_link" => "nullable|string",
            "video_360_link" => "nullable|string",
            "qr_link" => "nullable|string",
            "cheques" => "nullable|string",
            "title_deed_text" => "nullable|string",
            "other_documents" => "nullable|array",
            "contract_start" => "nullable|date",
            "contract_end" => "nullable|date|after_or_equal:contract_start",
            // 'contract_number' => 'nullable|string',
        ];

        if ($request->isPublishRequest || $request->publish_status == "PUBLISHED") {
            /* If Property is going to be published or pocket use this validation rule */
            $rules["title"] = "required|string|min:35|max:50";
            $rules["description"] = "required|string|min:750|max:2500";
            $rules["price"] = "required";
            $rules["owner_id"] = "required|exists:contacts,id";
            $rules["bed_id"] = "required|exists:beds,id";
            $rules["bath_id"] = "required|exists:baths,id";
            $rules["built_up_area"] = "required";
            $rules["parking"] = "required|numeric";
            $rules["furnished"] = "required";
            $rules["exclusive"] = "required";
            $rules["contract_start"] = "required|date";
            $rules["contract_end"] = "required|date|after_or_equal:contract_start";
        }

        if ($property->is_live || $property->publish_status == "PUBLISHED") {
            $rules["for"] = "nullable|string";
            $rules["property_category_id"] = "nullable|string";
            $rules["unit_no"] = "nullable|string";
        } else {
            $rules["for"] = "required|string";
            $rules["property_category_id"] = "required|string";
            $rules["unit_no"] = "required|string";
        }

        $validatedData = $request->validate($rules);

        try {
            /* This function gets the communityProperty and subCommunity ids */
            $validatedData = $this->getLocationData($validatedData);
            if ($validatedData['down_payment_price']) {
                $validatedData['down_payment_price'] = str_replace(",", "", $validatedData['down_payment_price']) ?? null;
            }

            /* Owner email required to publish the listing */
            if ($request->isPublishRequest == '1') {
                // get owner contact
                $owner = Contact::find($request->owner_id);
                // if no email then return error
                if (empty($owner->email)) {
                    return response()->json(['msg' => 'Owner email is required'], 422);
                }
            }
            /* For Pocket listing atleast one image is required */
            if (($request->publish_status == 'POCKET' || $request->isPocketRequest) && (!$request->isPublishRequest)) {
                if ($property->photos->count() == 0) {
                    return response()->json(['msg' => 'Please add at least one photo'], 422);
                }
            }

            $approval = false;

            if ($request->contract_start) {
                $contract_start = date('Y-m-d', strtotime($request->contract_start));
                $validatedData['contract_start'] = $contract_start;
                if ($request->contract_end) {
                    $contract_end = date('Y-m-d', strtotime($request->contract_end));
                    $validatedData['contract_end'] = $contract_end;
                } else {
                    $validatedData['contract_end'] = date('Y-m-d', strtotime("+2 months", strtotime($contract_start)));
                }
            }

            if ($request->exclusive == 1) {
                $exclusive_dates = explode("to", $request->exclusive_dates);
                $exclusive_start = date('Y-m-d', strtotime($exclusive_dates[0]));
                $validatedData["exclusive_start"] = $exclusive_start ?? null;

                if (isset($exclusive_dates[1])) {
                    $exclusive_end = date('Y-m-d', strtotime($exclusive_dates[1]));
                    $validatedData["exclusive_end"] = $exclusive_end;
                } else {
                    $validatedData["exclusive_end"] = null;
                }

            }
            /* If Publish Status is null for any reason keep the same as old */

            $validatedData["publish_status"] = $validatedData['publish_status'] ?? $property->publish_status;
            if ($notAdmin && (in_array($property->publish_status, ["PUBLISHED", "POCKET"]) || $validatedData['publish_status'] == "POCKET" || $property->is_live)) {
                $approval = true;
                if ($property->is_live == true && $validatedData['publish_status'] == "DRAFT") {
                    $validatedData['publish_status'] = $property->publish_status;
                }
            }
            $validatedData["view"] = json_encode($request->view);
            $validatedData["villa_type"] = json_encode($request->villa_type) ?? null;
            $orignal = $property->getOriginal();
            // dd($orignal);

            // This code might not be used in new approach
            if (($request->isPocketRequest || $validatedData['publish_status'] == "POCKET") && $property->publish_status != "POCKET") {
                $propertyObj2 = Property::find($property->id);
                $propertyObj2->withoutApproval()->update(['publish_status' => "PENDING", "request_status_type" => "POCKET"]);
                PropertyNote::create([
                    "property_id" => $request->id,
                    "note" => "Publish as pocket request created",
                    "created_by_id" => auth()->id(),
                ]);
                event(new PocketRequest($property));

            }

            /* If Approval is requied for agent use the below function */
            if ($approval) {

                $existingAttributes = $property->getAttributes();
                $commonKeys = array_intersect_key($validatedData, $existingAttributes);
                $changes = array_diff_assoc($commonKeys, $existingAttributes);
                $price_of_property = $property?->price?->price ?? null;

                $requestedPrice = str_replace(",", "", $request->price);

                if ($changes || $price_of_property != $requestedPrice) {
                    $validatedData["updated_by_id"] = auth()->id();
                    $validatedData["updated_at"] = now();
                    event(new ChangesRequested($property));
                }
                $property->update($validatedData);

            } else {

                if ($validatedData["publish_status"] == 'PUBLISHED') {

                    $validatedData['is_live'] = true;
                    if ($validatedData["publish_status"] == "PUBLISHED" && $property->publish_status != "PUBLISHED") {
                        $validatedData['published_date'] = now();
                    }

                    $validatedData['request_status_type'] = "PUBLISHED";
                    $validatedData["updated_by_id"] = auth()->id();
                    $validatedData["updated_at"] = now();
                }
                // If Property is live and someone changes the status if will be removed from live
                if ($property->is_live == true && (!in_array($validatedData["publish_status"], ["PENDING", "REFUSED", "PUBLISHED"])) && auth()->user()->hasRole(["Admin", "Super Admin"])
                ) {
                    $validatedData['is_live'] = false;
                    if ($property->unpublished_date == null) {
                        $validatedData['unpublished_date'] = now();
                    }
                    $validatedData['request_status_type'] = null;
                }
                if ($validatedData["publish_status"] == "POCKET") {
                    $validatedData['is_live'] = false;
                    $validatedData['request_status_type'] = "POCKET";
                }
                $property->withoutApproval()->update($validatedData);

            }
            $dirty = !empty($property->getDirty()) ? $property->getDirty() : $property->getChanges();

            $dirty = !empty($property->getDirty()) ? $property->getDirty() : $property->getChanges();
            $orignal = collect($orignal)
                ->only(collect($dirty)->keys())
                ->toArray();

            $arr = ['attributes' => $dirty, 'old' => !empty($orignal) ? $orignal : $property->getOriginalMatchingChanges()];
            activity()
                ->performedOn($property)
                ->causedBy(auth()->user())
                ->withProperties($arr)
                ->event('updated')
                ->log(auth()->user()->first_name . ' has updated listing ' . $property->ref_no);

            if (isset($validatedData['price'])) {
                $price_obj = PropertyPrice::where('property_id', $property->id)->first() ?? new PropertyPrice();
                $price_obj->property_id = $property->id;
                $old_price = $price_obj->price;
                $price_obj->price = str_replace(",", "", $request->price);

                if ($approval) {
                    $price_obj->save();
                    if ($changes || $price_of_property != $requestedPrice) {
                        $propertyObj2 = Property::find($property->id);
                        $propertyObj2->withoutApproval()->update(['publish_status' => 'PENDING']);
                    }

                    $original = $price_obj->getOriginal() ?? [];
                    $dirty = $price_obj->getDirty() ?? [];
                    //log activity
                    if (strval($old_price) !== strval(cleanData($request->price))) {
                        $arr = ['attributes' => $dirty, 'old' => $original, 'property_id' => $property->id];
                        activity()
                            ->performedOn($price_obj)
                            ->causedBy(auth()->user())
                            ->withProperties($arr)
                            ->event('created')
                            ->log(auth()->user()->first_name . ' has requested to change the price.');
                    }

                } else {
                    $original = $price_obj->getOriginal() ?? [];
                    $dirty = $price_obj->getDirty() ?? [];
                    $price_obj->price_on_application = (isset($request->price_on_application) ? 1 : 0);
                    $arr = ['attributes' => $dirty,
                        'old' => $original, 'property_id' => $property->id];

                    // dd(strval($old_price) !== strval(cleanData($request->price)));
                    if (strval($old_price) !== strval(cleanData($request->price))) {
                        activity()
                            ->performedOn($price_obj)
                            ->causedBy(auth()->user())
                            ->withProperties($arr)
                            ->event('created')
                            ->log(auth()->user()->first_name . ' has changed the price.');
                    }

                    $price_obj->withoutApproval()->save();
                }
            }

            if ($request->hasFile("floor_plan")) {
                $request->validate([
                    "floor_plan" => ['nullable', 'mimes:jpg,jpeg,png'],
                ]);

                $images = $this->uploadImageAndThumbWithWatermark($request->file("floor_plan"));
                if (!empty($images)) {
                    $floorPlanChanged = null;

                    if ($approval) {
                        $propObj2 = Property::find($property->id);
                        $propObj2->withoutApproval()->update(['publish_status' => 'PENDING']);
                        $floorPlan = PropertyMedia::where('property_id', $property->id)->where('type', "Floor Plan")->exists();
                        $floorPlanChanged = ($floorPlan ? "Updated" : "Added");
                    }
                    PropertyMedia::updateOrCreate([
                        "property_id" => $property->id,
                        "type" => "Floor Plan",
                    ], [
                        "path" => $images["image"] ?? null,
                        "media_changed" => $floorPlanChanged,
                        "original" => $images["original"] ?? null,
                        "thumbnail" => $images["thumbnail"] ?? null,
                        "created_by_id" => auth()->id(),
                    ]);

                }
            }

            if ($request->hasFile("title_deed")) {
                $path = $this->uploadDocument($request->file("title_deed"), "listings/" . $property->ref_no . "/Title Deed");
                if (!empty($path)) {

                    $titleDeedChanged = null;
                    if ($approval) {
                        $propObj2 = Property::find($property->id);
                        $propObj2->withoutApproval()->update(['publish_status' => 'PENDING']);

                        $titleDeed = PropertyMedia::where('property_id', $property->id)->where('type', 'Other')->where('title', 'Title Deed')->exists();

                        $titleDeedChanged = $titleDeed ? "Updated" : "Added";
                    }
                    $title_deed = PropertyMedia::where('property_id', $property->id)->where('type', 'Other')->where('title', 'Title Deed')->first() ?? new PropertyMedia();
                    $title_deed->property_id = $property->id;
                    $title_deed->type = 'Other';
                    $title_deed->media_changed = $titleDeedChanged;
                    $title_deed->password = $request->title_deed_password;
                    $title_deed->title = 'Title Deed';
                    $title_deed->path = $path ?? null;
                    $title_deed->created_by_id = auth()->id();
                    $original = $title_deed->getOriginal() ?? [];
                    $dirty = $title_deed->getDirty() ?? [];
                    $title_deed->save();
                    //log activity

                    $arr = ['attributes' => $dirty,
                        'old' => $original, 'property_id' => $property->id];
                    activity()
                        ->performedOn($title_deed)
                        ->causedBy(auth()->user())
                        ->withProperties($arr)
                        ->event('created')
                        ->log(auth()->user()->first_name . ' has uploaded property title deed document');
                }

            }

            if ($request->title_deed_password) {
                $document = PropertyMedia::where('property_id', $property->id)->where('type', 'Other')->where('title', 'Title Deed')->first();
                if ($document) {
                    $document->password = $request->title_deed_password;
                    $document->save();
                }
            }

            if ($request->hasFile("owner_passport_copy")) {
                // dd($request->owner_passport_copy_password);
                $path = $this->uploadDocument($request->file("owner_passport_copy"), "listings/" . $property->ref_no . "/Owner Passport Copy");
                if (!empty($path)) {

                    $ownerPassportChanged = null;
                    if ($approval) {
                        $propObj2 = Property::find($property->id);
                        $propObj2->withoutApproval()->update(['publish_status' => 'PENDING']);
                        $ownerPassport = PropertyMedia::where('property_id', $property->id)->where('type', 'Other')->where('title', 'Owner Passport Copy')->exists();

                        $ownerPassportChanged = $ownerPassport ? "Updated" : "Added";
                    }
                    $owner_passport = PropertyMedia::where('property_id', $property->id)->where('type', 'Other')->where('title', 'Owner Passport Copy')->first() ?? new PropertyMedia();
                    $owner_passport->property_id = $property->id;
                    $owner_passport->type = 'Other';
                    $owner_passport->media_changed = $ownerPassportChanged;
                    $owner_passport->password = $request->owner_passport_copy_password;

                    $owner_passport->title = 'Owner Passport Copy';
                    $owner_passport->path = $path ?? null;
                    $owner_passport->created_by_id = auth()->id();
                    $original = $owner_passport->getOriginal() ?? [];
                    $dirty = $owner_passport->getDirty() ?? [];
                    $owner_passport->save();
                    //log activity

                    $arr = ['attributes' => $dirty,
                        'old' => $original, 'property_id' => $property->id];
                    activity()
                        ->performedOn($owner_passport)
                        ->causedBy(auth()->user())
                        ->withProperties($arr)
                        ->event('created')
                        ->log(auth()->user()->first_name . ' has uploaded owner passport copy');
                }
            }

            if ($request->owner_passport_copy_password) {
                $document = PropertyMedia::where('property_id', $property->id)->where('type', 'Other')->where('title', 'Owner Passport Copy')->first();
                if ($document) {
                    $document->password = $request->owner_passport_copy_password;
                    $document->save();
                }
            }

            if ($request->hasFile("signed_listing_form")) {
                // dd($request->signed_listing_form_password);

                $path = $this->uploadDocument($request->file("signed_listing_form"), "listings/" . $property->ref_no . "/Signed Listing Form");
                if (!empty($path)) {

                    $signedFormChanged = null;
                    if ($approval) {
                        $propObj2 = Property::find($property->id);
                        $propObj2->withoutApproval()->update(['publish_status' => 'PENDING']);
                        $signedForm = PropertyMedia::where('property_id', $property->id)->where('type', 'Other')->where('title', 'Signed Listing Form')->exists();

                        $signedFormChanged = $signedForm ? "Updated" : "Added";
                    }
                    $signed_form = PropertyMedia::where('property_id', $property->id)->where('type', 'Other')->where('title', 'Signed Listing Form')->first() ?? new PropertyMedia();
                    $signed_form->property_id = $property->id;
                    $signed_form->type = 'Other';
                    $signed_form->password = $request->signed_listing_form_password;

                    $signed_form->title = 'Signed Listing Form';
                    $signed_form->media_changed = $signedFormChanged;
                    $signed_form->path = $path ?? null;
                    $signed_form->created_by_id = auth()->id();
                    $orignal = $signed_form->getOriginal() ?? [];
                    $dirty = $signed_form->getDirty() ?? [];
                    $signed_form->save();
                    //log activity
                    $arr = ['attributes' => $dirty,
                        'old' => $orignal, 'property_id' => $property->id];
                    activity()
                        ->performedOn($signed_form)
                        ->causedBy(auth()->user())
                        ->withProperties($arr)
                        ->event('created')
                        ->log(auth()->user()->first_name . ' has uploaded signed listing form');
                }
            }
            if ($request->signed_listing_form_password) {
                $document = PropertyMedia::where('property_id', $property->id)->where('type', 'Other')->where('title', 'Signed Listing Form')->first();
                if ($document) {
                    $document->password = $request->signed_listing_form_password;
                    $document->save();
                }
            }

            // dd($request->other_documents);
            foreach ($request->other_documents ?? [] as $key => $document) {

                if (!empty($document["file"])) {
                    $path = $this->uploadDocument($document["file"], "listings/" . $property->ref_no . "/" . $document['title']);
                    if (!empty($path)) {
                        if ($request->other_documents_old_id[$key]['old_id']) {
                            $media = PropertyMedia::where("id", $request->other_documents_old_id[$key]['old_id'])->where("property_id", $property->id)->update(['path' => $path ?? null, 'password' => $document['password']]);
                            $custom_doc = PropertyMedia::find($request->other_documents_old_id[$key]['old_id']);
                            $custom_doc->path = $path ?? null;
                            $orignal = $custom_doc->getOriginal() ?? [];
                            $dirty = $custom_doc->getDirty() ?? [];
                            $custom_doc->save();

                        } else {
                            $custom_doc = new PropertyMedia();
                            $custom_doc->property_id = $property->id;
                            $custom_doc->type = 'Other';
                            $custom_doc->title = $document["title"] ?? "Other";
                            $custom_doc->path = $path ?? null;
                            $custom_doc->password = $document['password'];
                            $custom_doc->created_by_id = auth()->id();
                            $orignal = $custom_doc->getOriginal() ?? [];
                            $dirty = $custom_doc->getDirty() ?? [];
                            $custom_doc->save();

                        }
                        $arr = ['attributes' => $dirty,
                            'old' => $orignal, 'property_id' => $property->id];
                        activity()
                            ->performedOn($custom_doc)
                            ->causedBy(auth()->user())
                            ->withProperties($arr)
                            ->event('created')
                            ->log(auth()->user()->first_name . ' has uploaded ' . $custom_doc->title . ' document');
                    }

                } else {
                    if ($request->other_documents_old_id[$key]['old_id']) {
                        $custom_doc = PropertyMedia::find($request->other_documents_old_id[$key]['old_id']);
                        $custom_doc->password = $document['password'];
                        $custom_doc->title = $document['title'];
                        $custom_doc->save();
                    }
                }
            }
            if (auth()->user()->hasRole(["Admin", "Super Admin", "Admin - Abu Dhabi"])) {
                PropertyPortal::where("property_id", $property->id)->whereNotIn("portal_id", $request->portals ?? [])->delete();
                foreach ($request->portals ?? [] as $portal) {

                    PropertyPortal::updateOrCreate([
                        "property_id" => $property->id,
                        "portal_id" => $portal,
                    ], [
                        "created_by_id" => auth()->id(),
                        "updated_by_id" => auth()->id(),
                    ]);
                }
            }
            PropertyFeatures::where("property_id", $property->id)->whereNotIn("property_amenity_id",
                $request->features ?? [])->delete();
            foreach ($request->features ?? [] as $feature) {
                PropertyFeatures::updateOrCreate([
                    "property_id" => $property->id,
                    "property_amenity_id" => $feature,
                ], [
                    "created_by_id" => auth()->id(),
                    "updated_by_id" => auth()->id(),
                ]);
            }
            if ($request->isPublishRequest) {
                $params = new Request();

                $params->replace(['id' => $property->id, 'request_note' => "Publish Request Sent", "return_array" => true]);
                $publish_request = $this->publishRequest($params);
                // dd($publish_request);
                if ($publish_request['status'] == "1") {
                    // $propertyObj2 = Property::find($property->id);
                    // $propertyObj2->withoutApproval()->update(['publish_status' => 'PENDING']);
                    $result = array('status' => '1', 'msg' => 'Listing added and Listing publish request sent successfully.', "id" => $property->id);
                    return json_encode($result);
                }
            }

            $result = array('status' => '1', 'msg' => 'Listing updated successfully', "id" => $property->id);
            if (auth()->user()->id == '172' || auth()->user()->hasRole('Super Admin')) {
                event(new PropertyUpdated($property));
            }
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage(), 'line' => $exception->getLine());
            return response()->json($result, 500);
        }

        return json_encode($result);
    }

    public function notAdmin()
    {
        $notAdmin = auth()->user()->hasRole(["Primary Sales Director", "Primary Sales Manager", "Sales Director", "Line Manager", "Team Leader", "Agent", "Primary Agent"]);
        return $notAdmin;
    }

    public function publishRequest(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:properties,id',
            'request_note' => 'required|string',
            'return_array' => 'nullable|boolean',
        ]);

        try {
            $portals = Portal::whereIn("name", ["Property Finder", "Bayut", "Dubizzle", "Generic portal"])->get();
            $property = Property::find($request->id);

            // Checking if the person has access to unpublish this listing
            $notAdmin = auth()->user()->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);

            $childrenIds = [];
            if ($notAdmin) {
                $childrenIds = getUserChildrenIds(auth()->id());
                if (!in_array($property->agent_id, $childrenIds)) {
                    $result = array('status' => '0', 'message' => 'You are not authorized to perform this action', 'error' => '', 'line' => '');
                    return response()->json($result, 500);
                }
            }
            $property->publish_status = "PENDING";
            $property->request_status_type = "PUBLISHED";
            $property->updated_by_id = auth()->id();
            $property->withoutApproval()->save();

            //  // Adding is Live here so we can know from frontend that this listing came for published
            // $propertyObj2 = Property::find($request->id);
            // $propertyObj2->publish_status = "PUBLISHED";
            // $propertyObj2->is_live = 1;
            // $propertyObj2->save();

            PropertyNote::create([
                "property_id" => $request->id,
                "note" => "Request Publish: " . $request->request_note,
                "created_by_id" => auth()->id(),
            ]);
            foreach ($portals as $key => $portal) {
                PropertyPortal::updateOrCreate(["property_id" => $property->id, "portal_id" => $portal->id]);
            }

            event(new PublishRequested($property));
            $adminEmail = getAdminEmail();
            if (!empty($adminEmail)) {
                //Mail::to($adminEmail)->send(new PropertyPublishRequestMail($property, auth()->user()));
            }
            $result = array('status' => '1', 'msg' => "Listing publish request sent successfully.");
            if ($request->return_array) {
                return $result;
            }
            return json_encode($result);
        } catch (\Exception $exception) {
            info($exception);
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            return json_encode($result);
        }
    }

    public function getLocationData($params)
    {
        /* This function can be used to fetch the location ids for store and update */

        if ($params['location_type'] == "community_property") {
            $communityProperty = CommunityProperty::find(str_replace("?community_property", "", $params['location_id']));
            $params["community_property_id"] = $communityProperty->id ?? null;
            $params["sub_community_id"] = $communityProperty->sub_community_id ?? null;
            $params["community_id"] = $communityProperty->community_id ?? null;
            $params["region_id"] = $communityProperty->region_id ?? null;
        } else {
            $subCommunity = SubCommunity::find(str_replace("?sub_community", "", $params['location_id']));
            $params["community_property_id"] = null;
            $params["sub_community_id"] = $subCommunity->id ?? null;
            $params["community_id"] = $subCommunity->community_id ?? null;
            $params["region_id"] = $subCommunity->region_id ?? null;
        }
        return $params;
    }
/**
 *
 *
 * Note to Listing
 *
 * Add Note to Listing.
 * @group Listings
 * @authenticated
 * @queryParam id int required Property id.Example: 4
 * @queryParam note string required Note for the listing.Example: lorem ipsum

 * @header Accept application/json
 * @header X-Requested-With XMLHttpRequest

 *
 * @return \Illuminate\Http\JsonResponse
 * @response 200 scenario="Data found"
 * {
{
"status": "1",
"msg": "Note added successfully",
}
 *}
 * @param  \Illuminate\Http\Request  $request
 * @return \Illuminate\Http\Response
 * @response 200 scenario="User not found"
 * {
"status": false,
"code": 500,
"msg": "Something Went wrong",
}
 */
    public function addNote(Request $request)
    {
        Log::channel('mobileApi')->info('Request From' . auth()->user()->email, ['request' => $request->all()]);
        // return 'add note to listing';
        $request->validate([
            "id" => "required|exists:properties,id",
            "note" => "required",
        ]);

        try {

            DB::beginTransaction();
            PropertyNote::create([
                "property_id" => $request->id,
                "note" => $request->note,
                "created_by_id" => auth()->id(),
            ]);
            DB::commit();

            return $this->sendResponse(true, 'Note added successfully', [], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->sendResponse(false, 'Something Went Wrong', $th->getMessage(), 500);
        }
    }

/**
 *
 *
 * Media Upload
 *
 * Media Upload.
 * @group Listings
 * @authenticated
 * @queryParam property_id int required Property id.Example: 4
 * @queryParam file string required Note for the listing.Example: lorem ipsum
 * @queryParam type string required document type.Example: photos,floor_plan,title_deed,owner_passport_copy,signed_listing_form,other
 * @queryParam title string title for any other document.Example: lorem ipsum
 * @queryParam password string password for document.Example: Pa$$W0rd
 * @header Accept application/json
 * @header X-Requested-With XMLHttpRequest

 *
 * @return \Illuminate\Http\JsonResponse
 * @response 200 scenario="Data found"
 * {
{
"status": "1",
"msg": "Note added successfully",
}
 *}
 * @param  \Illuminate\Http\Request  $request
 * @return \Illuminate\Http\Response
 * @response 200 scenario="User not found"
 * {
"status": false,
"code": 500,
"msg": "Something Went wrong",
}
 */

    public function uploadMedia(Request $request)
    {
        Log::channel('mobileApi')->info('Request From' . auth()->user()->email, ['request' => $request->all()]);

        $request->validate([
            "property_id" => "required|exists:properties,id",
            "file" => "required",
            'title' => 'nullable|string',
            'password' => 'nullable|string',
            'type' => 'required|in:photos,floor_plan,title_deed,owner_passport_copy,signed_listing_form,other',
        ]);

        try {

            switch ($request->type) {
                case "photos":
                    // $propertyContriller = new PropertyController();
                    // $response = $propertyContriller->uploadPhoto($request);
                    // return $response;

                    if ($request->watermark) {
                        $watermark = $request->watermark == "78645675" ? "assets/images/logo/watermarkv3.png" : '';
                    } else {
                        $watermark = '';
                    }

                    if (auth()->user()->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"])) {
                        $watermark = 'assets/images/logo/watermarkv3.png';
                    }
                    $property = Property::find($request->property_id);

                    // $watermark = $request->watermark=="78645675" ? "assets/images/logo/watermarkv3.png": '';
                    $data = $this->uploadImageAndThumbWithWatermark(file: $request->file("file"), path: "listings", watermark: $watermark);
                    if (!empty($data)) {
                        $media = new PropertyMedia();
                        if ($property) {
                            if ($property->is_live && !auth()->user()->hasRole(["Super Admin", "Admin"])) {
                                $propObj2 = Property::find($property->id);
                                $propObj2->withoutApproval()->update(['publish_status' => 'PENDING']);
                                $media->media_changed = "Added";
                            }
                        }

                        $media->property_id = $request->property_id;
                        $media->temp_id = $request->temp_id;
                        $media->path = $data["image"] ?? null;
                        $media->original = $data["original"] ?? null;
                        $media->thumbnail = $data["thumbnail"] ?? null;
                        $media->order = PropertyMedia::count() + 1;
                        $media->type = "Photos";
                        $media->created_by_id = auth()->id();
                        $dirty = $media->getDirty() ?? [];
                        $media->save();

                        //if (!auth()->user()->hasRole(["Super Admin", "Admin"])) {

                        //logging starts

                        $arr = ['attributes' => $dirty,
                            'old' => [], 'property_id' => $request->property_id];

                        activity()
                            ->performedOn($media)
                            ->causedBy(auth()->user())
                            ->withProperties($arr)
                            ->event('created')
                            ->log(auth()->user()->first_name . ' has uploaded an image ');
                        $result = array('status' => '1', 'msg' => "Photo uploaded successfully.", 'data' => $media);
                        return json_encode($result);

                    }
                    break;
                case "floor_plan":
                    $request->validate([
                        "file" => ['nullable', 'mimes:jpg,jpeg,png'],
                    ]);
                    $property = Property::find($request->property_id);
                    $images = $this->uploadImageAndThumbWithWatermark($request->file("file"));
                    if (!empty($images)) {
                        $media = PropertyMedia::create([
                            "property_id" => $property->id,
                            "type" => "Floor Plan",
                            "path" => $images["image"] ?? null,
                            "original" => $images["original"] ?? null,
                            "thumbnail" => $images["thumbnail"] ?? null,
                            "created_by_id" => auth()->id(),
                        ]);

                    }
                    $result = array('status' => '1', 'msg' => "Photo uploaded successfully.", 'data' => $media);
                    return json_encode($result);
                    break;
                case "title_deed":
                    $property = Property::find($request->property_id);
                    $path = $this->uploadDocument($request->file("file"), "listings/" . $property->ref_no . "/Title Deed");
                    if (!empty($path)) {
                        $media = PropertyMedia::create([
                            "property_id" => $property->id,
                            "type" => "Other",
                            'password' => $request->password,
                            "title" => "Title Deed",
                            "path" => $path ?? null,
                            "created_by_id" => auth()->id(),
                        ]);
                    }
                    $result = array('status' => '1', 'msg' => "Title Deed uploaded successfully.", 'data' => $media);
                    return json_encode($result);
                    break;
                case "owner_passport_copy":
                    $property = Property::find($request->property_id);
                    $path = $this->uploadDocument($request->file("file"), "listings/" . $property->ref_no . "/Owner Passport Copy");
                    if (!empty($path)) {
                        $media = PropertyMedia::create([
                            "property_id" => $property->id,
                            "type" => "Other",
                            "title" => "Owner Passport Copy",
                            'password' => $request->password,
                            "path" => $path ?? null,
                            "created_by_id" => auth()->id(),
                        ]);
                    }
                    $result = array('status' => '1', 'msg' => "Owner Passport Copy uploaded successfully.", 'data' => $media);
                    return json_encode($result);
                    break;
                case "signed_listing_form":
                    $property = Property::find($request->property_id);
                    $path = $this->uploadDocument($request->file("file"), "listings/" . $property->ref_no . "/Signed Listing Form");
                    if (!empty($path)) {
                        $media = PropertyMedia::create([
                            "property_id" => $property->id,
                            "type" => "Other",
                            "title" => "Signed Listing Form",
                            'password' => $request->password,
                            "path" => $path ?? null,
                            "created_by_id" => auth()->id(),
                        ]);
                    }
                    $result = array('status' => '1', 'msg' => "Signed Listing Form uploaded successfully.", 'data' => $media);
                    return json_encode($result);
                    break;
                case "other":
                    $property = Property::find($request->property_id);
                    $path = $this->uploadDocument($request->file("file"), "listings/" . $property->ref_no . "/" . $request->title ?? 'Other');
                    if (!empty($path)) {
                        $media = PropertyMedia::create([
                            "property_id" => $property->id,
                            "type" => "Other",
                            "title" => $request->title ?? "Other",
                            "path" => $path ?? null,
                            "created_by_id" => auth()->id(),
                        ]);
                    }
                    $result = array('status' => '1', 'msg' => "Other Document uploaded successfully.", 'data' => $media);
                    return json_encode($result);
                    break;
                default:
                    return $this->sendResponse(false, 'Invalid type', [], 200);
                    break;
            }

        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->sendResponse(false, 'Something Went Wrong', $th->getMessage(), 500);
        }
    }

/**
 *
 *
 * Media Upload
 *
 * Media Upload.
 * @group Listings
 * @authenticated
 * @queryParam property_id int required Property id.Example: 4
 * @queryParam file string required Note for the listing.Example: lorem ipsum
 * @queryParam type string required document type.Example: photos,floor_plan,title_deed,owner_passport_copy,signed_listing_form,other
 * @queryParam title string title for any other document.Example: lorem ipsum
 * @queryParam password string password for document.Example: Pa$$W0rd
 * @header Accept application/json
 * @header X-Requested-With XMLHttpRequest

 *
 * @return \Illuminate\Http\JsonResponse
 * @response 200 scenario="Data found"
 * {
{
"status": "1",
"msg": "Note added successfully",
}
 *}
 * @param  \Illuminate\Http\Request  $request
 * @return \Illuminate\Http\Response
 * @response 200 scenario="User not found"
 * {
"status": false,
"code": 500,
"msg": "Something Went wrong",
}
 */

    public function removePhoto(Request $request)
    {
        $request->validate([
            'media_id' => 'required|exists:property_media,id',
        ]);
        try {

            $media = PropertyMedia::find($request->media_id);
            $original = $media?->getOriginal() ?? [];
            $dirty = $media?->getDirty() ?? [];

            $environment = \App::environment();
            if ($media) {
                $property = [];
                if ($media->property_id) {
                    $property = Property::find($media->property_id);
                }
                // if ($environment == "production") {
                //     $this->removePhotoFromBucket($request->media_id);
                // }
                if ($property) {
                    if ($property->is_live && !auth()->user()->hasRole(["Super Admin", "Admin"])) {
                        $propObj2 = Property::find($property->id);
                        $propObj2->withoutApproval()->update(['publish_status' => 'PENDING']);
                        $media->media_changed = "Deleted";
                    }
                }

                $media->deleted_by_id = auth()->id();
                $media->save();
                $arr = ['attributes' => $dirty, 'old' => $original, 'property_id' => $media->property_id, 'media_id' => $media->id];
                $media->delete();

                return $this->sendResponse(true, 'Photo removed successfully.', [], 200);

            }
            return $this->sendResponse(true, 'Media Not Found', [], 200);

            return json_encode($result);
        } catch (\Throwable $th) {
            throw $th;
            return $this->sendResponse(false, $th->getMessage(), [], 500);
        }

    }

/**
 *
 *
 * Remove from Portal
 *
 * Remove from Portal
 * @group Listings
 * @authenticated
 * @queryParam property_id int required Property id.Example: 4
 * @queryParam portal_id int required Portal id.Example: 1

 * @header Accept application/json
 * @header X-Requested-With XMLHttpRequest

 *
 * @return \Illuminate\Http\JsonResponse
 * @response 200 scenario="Data found"
 * {
{
"status": "1",
"code": 200,
"msg": "Property removed from portal successfully",
}
 *}
 * @param  \Illuminate\Http\Request  $request
 * @return \Illuminate\Http\Response
 * @response 200 scenario="User not found"
 * {
"status": false,
"code": 500,
"msg": "Something Went wrong",
}
 */
    public function removeFromPortal(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'portal_id' => 'required|exists:portals,id',
        ]);

        try {
            $portal = Portal::find($request->portal_id);
            PropertyPortal::where("property_id", $request->property_id)->where("portal_id",
                $request->portal_id)->delete();
            PropertyNote::create([
                "property_id" => $request->property_id,
                "note" => "Listing has been Unpublished from " . $portal->name,
                "created_by_id" => auth()->id(),
            ]);
            return $this->sendResponse(true, 'Property removed from portal successfully.', [], 200);

        } catch (\Exception $exception) {

            return $this->sendResponse(false, 'Something went wrong!!.', [], 500);

        }

    }

/**
 *
 *
 * Send Unpublish Request
 *
 * Send Unpublish Request
 * @group Listings
 * @authenticated
 * @queryParam id int required Property id.Example: 4
 * @queryParam request_note string required Note why you want unpublish id.Example: lorem ipsum

 * @header Accept application/json
 * @header X-Requested-With XMLHttpRequest

 *
 * @return \Illuminate\Http\JsonResponse
 * @response 200 scenario="Data found"
 * {
{
"status": "1",
"code": 200,
"msg": "Property removed from portal successfully",
}
 *}
 * @param  \Illuminate\Http\Request  $request
 * @return \Illuminate\Http\Response
 * @response 200 scenario="User not found"
 * {
"status": false,
"code": 500,
"msg": "Something Went wrong",
}
 */

    public function sendUnpublishRequest(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:properties,id',
            'request_note' => 'required|string',
        ]);

        try {

            $property = Property::find($request->id);
            $propertyObj2 = Property::find($request->id);
            // Checking if the person has access to unpublish this listing
            $notAdmin = auth()->user()->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);

            $childrenIds = [];
            if ($notAdmin) {
                $childrenIds = getUserChildrenIds(auth()->id());
                if (!in_array($property->agent_id, $childrenIds)) {
                    $result = array('status' => '0', 'message' => 'You are not authorized to perform this action', 'error' => '', 'line' => '');
                    return response()->json($result, 500);
                }
            }
            $property->update([
                "updated_by_id" => auth()->id(),
                "publish_status" => "UNPUBLISHED",
            ]);
            $propertyObj2->withoutApproval()->update([
                "publish_status" => "PENDING",
                "request_status_type" => "UNPUBLISHED",
            ]);
            PropertyNote::create([
                "property_id" => $request->id,
                "note" => "Request Unpublish: " . $request->request_note,
                "created_by_id" => auth()->id(),
            ]);
            event(new UnpublishRequested($property));
            $adminEmail = getAdminEmail();
            if (!empty($adminEmail)) {

                Mail::to($adminEmail)->send(new PropertyUnpublishRequestMail($property, auth()->user()));

            }
            return $this->sendResponse(true, 'Listing unpublish request sent successfully.', [], 200);

        } catch (\Exception $exception) {
            return $this->sendResponse(false, 'Something went wrong!!', [], 500);
        }
    }

/**
 *
 *
 * Reorder Photos
 *
 * Reorder Photos
 * @group Listings
 * @authenticated
 * @queryParam property_id int required Property id.Example: 4
 * @queryParam ids string required ids of all photos in which order you want.Example: 389368,389375,389376,389374,389371,389369,389372,389377,389378,389381,389370,389379,389373,389380

 * @header Accept application/json
 * @header X-Requested-With XMLHttpRequest

 *
 * @return \Illuminate\Http\JsonResponse
 * @response 200 scenario="Photo orders has been changed successfully."
 * {
{
"status": true,
"code": 200,
"message": "Photo orders has been changed successfully.",
"data": [],
"page": 1,
"per_page": 10
}
 *}
 * @param  \Illuminate\Http\Request  $request
 * @return \Illuminate\Http\Response
 * @response 200 scenario="User not found"
 * {
"status": false,
"code": 500,
"msg": "Something Went wrong",
}
 */
    public function reOrderPhotos(Request $request)
    {

        if (auth()->user()->hasPermissionTo('Change Media Order') || auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Admin - Abu Dhabi')) {
            $request->validate([
                'ids' => 'required|',
                'property_id' => 'required|exists:properties,id',
            ]);

            try {
                $property = Property::find($request->property_id);

                if ($property->agent_id != auth()->id() && !auth()->user()->hasRole(['Super Admin', "Admin", "Admin - Abu Dhabi"])) {
                    $result = array('status' => '0', 'msg' => 'You do not have permission to change media order');
                    return json_encode($result);
                }

                $ids = explode(",", $request->ids);
                foreach ($ids ?? [] as $index => $id) {
                    if (!empty($id)) {
                        // PropertyMedia::find($id)->update(["order" => $index + 1]);
                        if (PropertyMedia::find($id)) {
                            $media = PropertyMedia::find($id);
                            if ($property->is_live && !auth()->user()->hasRole(["Admin", "SuperAdmin"])) {
                                $media->media_changed = "Order Updated";
                            }

                            $media->order = $index + 1;
                            $media->updated_by_id = auth()->id();
                            $media->save();

                        }
                    }
                }

                if ($property->is_live && !auth()->user()->hasRole(["Super Admin", "Admin"])) {
                    $property->withoutApproval()->update(['publish_status' => 'PENDING']);
                    activity()
                        ->performedOn($property)
                        ->causedBy(auth()->user())
                        ->withProperties([])
                        ->event('updated')
                        ->log('Images order has been changed by ' . auth()->user()->first_name);
                }
                return $this->sendResponse(true, 'Photo orders has been changed successfully.', [], 200);

            } catch (\Exception $exception) {
                return $this->sendResponse(false, 'Something went wrong!!.', [], 500);

            }
        } else {
            return $this->sendResponse(false, 'You do not have permission to change media order', [], 403);
        }
    }

    /**
     *
     *
     *
     * Duplicate Listing
     * @group Listings
     * @authenticated
     * @queryParam id int required Property id.Example: 4
     * @queryParam for int required Property Category (Residential for sale, Residential for rent, Commercial for sale, Commercial for rent). Residential for sale
     * @queryParam copy_images boolean to copy the image or not. Default is false
     * @queryParam duplicate_agent_id int required Agent id to whom the property will be assigned.Example: 4
     * @queryParam duplication_reason string required reason for duplication reason can be 'Relist (Agent Request)', 'Relist (Older than 3 months)', 'Offline in PF', 'Offline in Bayut / Dubizzle'.
     * @header Accept application/json
     * @header X-Requested-With XMLHttpRequest

     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Property duplicated successfully."
     * {
    {
    "status": true,
    "code": 200,
    "message": "Property duplicated successfully.",
    "data": [],
    "page": 1,
    "per_page": 10
    }
     *}
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @response 200 scenario="User not found"
     * {
    "status": false,
    "code": 500,
    "msg": "Something Went wrong",
    }
     */
    public function duplicateProperty(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:properties',
            'for' => 'required|string',
            'copy_images' => 'nullable',
            'duplicate_agent_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(false, 'Validation Error', $validator->errors(), 422);
        }

        DB::beginTransaction();
        try {
            $oldPropertyId = $request->id;
            $oldProperty = Property::find($oldPropertyId);
            $unit_no = $oldProperty->unit_no;
            $agent_id = $oldProperty->agent_id;
            $features = PropertyFeatures::where("property_id", $oldPropertyId)->get()->toArray();
            $notes = PropertyNote::where("property_id", $oldPropertyId)->get()->toArray();
            $portals = PropertyPortal::where("property_id", $oldPropertyId)->get()->toArray();
            $prices = PropertyPrice::where("property_id", $oldPropertyId)->get()->toArray();

            $old_ref_no = $oldProperty["ref_no"];
            $owner_id = $oldProperty["owner_id"];
            $categortID = $oldProperty["property_category_id"];
            $oldProperty = $oldProperty->toArray();
            $oldProperty["for"] = strtoupper($request->for);
            $oldProperty["ref_no"] = generatePropertyRefNo();
            $oldProperty["created_by"] = auth()->id();
            $oldProperty["updated_by"] = auth()->id();
            $oldProperty["publish_status"] = 'DRAFT';
            $oldProperty["is_live"] = false;
            $oldProperty["updated_at"] = Carbon::now();
            $oldProperty["created_at"] = Carbon::now();
            $oldProperty["unit_no"] = null;
            $oldProperty["title_deed_text"] = null;
            $oldProperty["contract_start"] = null;
            $oldProperty["contract_end"] = null;
            $oldProperty["agent_id"] = $request->duplicate_agent_id;

            $oldDescription = $oldProperty["description"];

            $property = new Property();
            $property->ref_no = $oldProperty["ref_no"];
            $property->withoutApproval()->save();

            if ($request->duplication_reason == 'Copy Content Only') {
                $property->description = $oldDescription;
                $property->title = 'Duplicate of ' . $old_ref_no;
                $property->for = strtoupper($request->for);
                $property->agent_id = $request->duplicate_agent_id;
                $property->publish_status = 'DRAFT';
                $property->is_live = false;
                $property->property_category_id = $categortID;
                $property->withoutApproval()->save();
            } else {
                $property->withoutApproval()->update($oldProperty);
            }

            if ($request->boolean("copy_images")) {

                $photos = PropertyMedia::where("property_id", $oldPropertyId)->where("type", "Photos")->get()->toArray();
                foreach ($photos ?? [] as $photo) {
                    $photo["property_id"] = $property->id;
                    //$imageUrl = $this->copyPhoto($photo["path"], "listings");
                    $imageV2Url = $this->copyPhoto($photo["path"], "listings/with_watermark"); // Need to be removed
                    $originalUrl = $this->copyPhoto($photo["original"], "listings");
                    $thumbUrl = $this->copyPhoto($photo["thumbnail"], "listings");
                    if (!empty($imageV2Url)) {
                        $media = PropertyMedia::create([
                            "property_id" => $property->id,
                            "path" => $imageV2Url ?? null,
                            "original" => $originalUrl ?? null,
                            "thumbnail" => $thumbUrl ?? null,
                            "order" => $photo['order'] ?? 10,
                            "type" => "Photos",
                            "created_by_id" => auth()->id(),
                        ]);
                    }
                }
            }

            // if ($request->duplication_reason == 'Copy Content Only'){
            foreach ($features ?? [] as $feature) {
                $feature["property_id"] = $property->id;
                PropertyFeatures::create($feature);
            }
            // }

            /*foreach ($notes ?? [] as $note){
            $note["property_id"] = $property->id;
            PropertyNote::create($note);
            }
             */
            if ($request->duplication_reason != 'Copy Content Only') {
                foreach ($portals ?? [] as $portal) {
                    $portal["property_id"] = $property->id;
                    PropertyPortal::create($portal);
                }
            }
            if ($request->duplication_reason != 'Copy Content Only') {
                foreach ($prices ?? [] as $price) {
                    $objp = new PropertyPrice();
                    $objp->property_id = $property->id;
                    $objp->price = $price['price'];
                    $objp->prices = $price['prices'];
                    $objp->price_on_application = $price['price_on_application'];
                    $objp->withoutApproval()->save();
                }
            }

            $reasons = ['Relist (Agent Request)', 'Relist (Older than 3 months)', 'Offline in PF', 'Offline in Bayut / Dubizzle'];
            if (isset($request->duplication_reason) && in_array($request->duplication_reason, $reasons)) {

                $p_notes = PropertyNote::where("property_id", $oldPropertyId)->get();
                foreach ($p_notes as $p_note) {
                    $new = new PropertyNote();
                    $new->property_id = $property->id;
                    $new->note = $p_note->note;
                    $new->created_by_id = auth()->id();
                    $new->save();
                }
                if ($p_notes->count() > 0) {
                    $note = new \App\Models\PropertyNote();
                    $note->note = '====================';
                    $note->created_by_id = auth()->id();
                    $note->property_id = $property->id;
                    $note->save();
                }
                // dd(!auth()->user()->hasRole('Agent'), $agent_id, auth()->id());
                if (!auth()->user()->hasRole('Agent') || $agent_id == auth()->id()) {
                    $property->unit_no = $unit_no;
                    $property->owner_id = $owner_id;
                    $property->withoutApproval()->save();
                    $docs = PropertyMedia::where("property_id", $oldPropertyId)->where("type", '!=', "Photos")->get()->toArray();
                    foreach ($docs ?? [] as $photo) {
                        $photo["property_id"] = $property->id;
                        $type = $photo['type'];
                        if ($type == null) {
                            $type = 'documents';
                        }

                        $imageUrl = $this->copyDocument($photo["path"], "listings/" . $property->ref_no . "/" . $type);
                        if (!empty($imageUrl)) {
                            $media = PropertyMedia::create([
                                "property_id" => $property->id,
                                "title" => $photo['title'],
                                "description" => $photo['description'],
                                "path" => $imageUrl ?? null,
                                "order" => $photo['order'] ?? 10,
                                "type" => $photo['type'],
                                "note" => $photo['note'],
                                "password" => $photo['type'],
                                "status" => $photo['status'],
                                "created_by_id" => auth()->id(),
                            ]);
                        }
                    }
                }

            } else if ($request->duplication_reason == 'Copy Content Only') {
                $p_notes = PropertyNote::where("property_id", $oldPropertyId)->get();
                if ($request->duplication_reason != 'Copy Content Only') {

                    foreach ($p_notes as $p_note) {
                        $new = new PropertyNote();
                        $new->property_id = $property->id;
                        $new->note = $p_note->note;
                        $new->created_by_id = auth()->id();
                        $new->save();
                    }
                    if ($p_notes->count() > 0) {
                        $note = new \App\Models\PropertyNote();
                        $note->note = '====================';
                        $note->created_by_id = auth()->id();
                        $note->property_id = $property->id;
                        $note->save();
                    }
                }

            }

            $note = new \App\Models\PropertyNote();
            $note->note = 'Listing created as copy of ' . $old_ref_no . ' with reason: ' . $request->duplication_reason;
            $note->created_by_id = auth()->id();
            $note->property_id = $property->id;
            $note->save();

            DB::commit();
            $result = array('status' => '1', 'msg' => 'Property duplicated successfully');
            return json_encode($result);
        } catch (\Exception $exception) {
            DB::rollBack();
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', "error" => $exception->getMessage(), "line" => $exception->getLine());
            // Return 500 server error
            return response()->json($result, 500);
            // $result = array('status' => '0', 'msg' => $exception->getMessage());
        }
        return json_encode($result);

    }

    /**
     *
     *
     *
     * Listing Notes
     * @group Listings
     * @authenticated
     * @header Accept application/json
     * @bodyParam ref_no requried for property ref no. Example: axc-3432452
     * @bodyParam agent_id integer for agent_id. Example: 12
    Example: abc
     *
     *
     *
     * @header Accept application/json

     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
    {
    "status": 200,
    "code": 200,
    "message": "Data found",
    "data": [
    {
    "id": 329350,
    "property_id": 269096,
    "note": "Note of the Listing",
    "status": 1,
    "created_by_id": 262,
    "updated_by_id": null,
    "created_at": "2024-07-25T10:34:44.000000Z",
    "updated_at": "2024-07-25T10:34:44.000000Z"
    }
    ],
    "page": 1,
    "per_page": 10
    }

     * @response 403 scenario="You are not authorized"
    {
    "status": 403,
    "code": 403,
    "message": "You are not authorized",
    "data": "Aborted",
    "page": 1,
    "per_page": 10
    }

     */

    public function getNotes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ref_no' => 'required',
            'agent_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(false, 'Validation Error', $validator->errors(), 422);
        }

        $listing = Property::where('ref_no', $request->ref_no)->first();

        if (!$listing) {
            return $this->sendResponse(404, 'Listing not found', [], 404);
        }

        $listingNotes = PropertyNote::with('createdBy')->where('property_id', $listing->id)->orderBy('id', 'desc')->get();

        $notes = PropertyNoteResource::collection($listingNotes);

        return $this->sendResponse(200, 'Data found', $notes, 200);

    }

    /**
     *
     *
     *
     * Listing Day Count
     * @group Listings
     * @authenticated
     * @header Accept application/json
     * @bodyParam id requried for property id. Example: 23
     * @bodyParam agent_id integer for agent_id. Example: 12
    Example: abc
     *
     *
     *
     * @header Accept application/json

     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
    {
    "status": 200,
    "code": 200,
    "message": "Days Count",
    "data": {
    "days":23
    },
    "page": 1,
    "per_page": 10
    }

     * @response 403 scenario="You are not authorized"
    {
    "status": 200,
    "code": 200,
    "message": "Days Count",
    "data": {
    "days": 0
    },
    "page": 1,
    "per_page": 10
    }

     */
    public function getNoOfDaysListingWasPublished(Request $request)
    {
        $property = Property::where('id', $request->id)->first();
        if (auth()->user()->hasRole('Agent')) {
            if ($property->agent_id != auth()->id()) {
                return $this->sendResponse(200, 'Days Count', ['days' => 0], 200);
            }
        }

        $proeprtyStatus = PropertyCycle::where('property_id', $property->id)->where('status', 'PUBLISHED')->orderBy('id', 'desc')->first();
        if ($proeprtyStatus) {
            $days = $proeprtyStatus->created_at->diffInDays(Carbon::now());
        } else {
            return $this->sendResponse(200, 'Days Count', ['days' => 0], 200);
        }
        return $this->sendResponse(200, 'Days Count', ['days' => $days], 200);
    }

    public function getAxListings(Request $request)
    {
        if (!$request->secret) {
            return $this->sendResponse(false, 'You are not authorized', [], 403);
        }
        if ($request->secret != 'd23f221f38f1d29ca5f8ege1dhbj8662') {
            return $this->sendResponse(false, 'You are not authorized', [], 403);
        }

        try {
            //code...

            $query = Property::query();

            if ($request->input('ref_no')) {
                $query->where('ref_no', 'like', '%' . $request->input('ref_no') . '%');
            }

            if ($request->input('agent_id')) {
                $this->isArray("agent_id") ? $query->whereIn("agent_id", \request("agent_id")) : $query->where("agent_id", \request("agent_id"));
            }
            if ($request->input('rera_permit_number')) {
                $query->where('rera_permit_number', $request->input('rera_permit_number'));
            }

            if ($request->input('for')) {
                $this->isArray("for") ? $query->whereIn("for", \request("for")) : $query->where("for", \request("for"));

            }

            if ($request->input('property_category_id')) {
                $this->isArray("property_category_id") ? $query->whereIn("property_category_id", \request("property_category_id")) : $query->where("property_category_id", \request("property_category_id"));

            }

            if ($request->input('bed_id')) {
                $this->isArray("bed_id") ? $query->whereIn("bed_id", \request("bed_id")) : $query->where("bed_id", \request("bed_id"));

            }

            if ($request->input('bath_id')) {
                $this->isArray("bath_id") ? $query->whereIn("bath_id", \request("bath_id")) : $query->where("bath_id", \request("bath_id"));

            }
            if ($request->input('completion_status')) {
                $this->isArray("completion_status") ? $query->whereIn("completion_status", \request("completion_status")) : $query->where("completion_status", \request("completion_status"));
            }

            if ($request->input('furnished')) {
                $this->isArray("furnished") ? $query->whereIn("furnished", \request("furnished")) : $query->where("furnished", \request("furnished"));
            }

            if ($request->input('publish_status')) {
                $this->isArray("publish_status") ? $query->whereIn("publish_status", \request("publish_status")) : $query->where("publish_status", \request("publish_status"));
            }

            if ($request->input('region_id')) {
                $this->isArray("region_id") ? $query->whereIn("region_id", \request("region_id")) : $query->where("region_id", \request("region_id"));
            }
            if ($request->input('community_id')) {
                $this->isArray("community_id") ? $query->whereIn("community_id", \request("community_id")) : $query->where("community_id", \request("community_id"));
            }

            if ($request->input('sub_community_id')) {
                $this->isArray("sub_community_id") ? $query->whereIn("sub_community_id", \request("sub_community_id")) : $query->where("sub_community_id", \request("sub_community_id"));
            }

            if ($request->input('community_property_id')) {
                $this->isArray("community_property_id") ? $query->whereIn("community_property_id", \request("community_property_id")) : $query->where("community_property_id", \request("community_property_id"));
            }



            if ($request->input('price_from') && !$request->input('price_to')) {
                $query->whereHas('price', function ($query) use ($request) {
                    $query->whereBetween('price', [$request->price_from, $request->price_to]);
                });
            }

            if ($request->input('price_to') && !$request->input('price_from')) {
                $query->whereHas('price', function ($query) use ($request) {
                    $query->where('price', '<=', $request->price_to);
                });
            }

            if ($request->input('price_from') && $request->input('price_to')) {
                $query->whereHas('price', function ($query) use ($request) {
                    $query->whereBetween('price', [$request->price_from, $request->price_to]);
                });
            }

            if ($request->input('property_plot_size_from') || $request->input('property_plot_size_to')) {
                $from = $request->input('property_plot_size_from', 0);
                $to = $request->input('property_plot_size_to', 10000);
                $query->whereBetween('plot_size', [$from, $to]);
            }
            // dd($request->all());
            // dd($request->input('property_build_up_area_from') || $request->input('property_build_up_area_to'));
            if ($request->input('property_build_up_area_from') || $request->input('property_build_up_area_to')) {
                $from = (float) $request->input('property_build_up_area_from', 0);
                $to = (float) $request->input('property_build_up_area_to', 10000);
                $query->whereBetween(DB::raw('CAST(built_up_area AS DECIMAL)'), [$from, $to]);
            }

            if ($request->input('created_at')) {
                $value = is_array($value) ? $value : explode(' to ', $value);
                if (count($value) === 1) {
                    $startOfDay = date('Y-m-d', strtotime($value[0])) . ' 00:00:00';
                    $endOfDay = date('Y-m-d', strtotime($value[0])) . ' 23:59:59';
                    $query = $query->where('created_at', '>=', $startOfDay)->where('created_at', '<=', $endOfDay);

                } else {
                    $startOfDay = date('Y-m-d', strtotime($value[0])) . ' 00:00:00';
                    $endOfDay = date('Y-m-d', strtotime($value[1])) . ' 23:59:59';
                    $query = $query->where('created_at', '>=', $startOfDay)->where('created_at', '<=', $endOfDay);
                }
            }

            if ($request->input('updated_at')) {
                $value = is_array($value) ? $value : explode(' to ', $value);
                if (count($value) === 1) {
                    $startOfDay = date('Y-m-d', strtotime($value[0])) . ' 00:00:00';
                    $endOfDay = date('Y-m-d', strtotime($value[0])) . ' 23:59:59';
                    $query = $query->where('updated_at', '>=', $startOfDay)->where('updated_at', '<=', $endOfDay);

                } else {
                    $startOfDay = date('Y-m-d', strtotime($value[0])) . ' 00:00:00';
                    $endOfDay = date('Y-m-d', strtotime($value[1])) . ' 23:59:59';
                    $query = $query->where('updated_at', '>=', $startOfDay)->where('updated_at', '<=', $endOfDay);
                }
            }
            if ($request->input('is_live')) {
                $query->where('is_live', true);
            }
            $query->when($request->ref_no, function ($query, $ref_no) {
                $query = $query->where('ref_no', $ref_no);
            });

            $query->where('is_archived', false)->orderBy('updated_at', 'desc');

            $query->when(!empty($request->search), function ($q) use ($request) {
                $searchTerm = '%' . $request->search . '%';
                return $q->where(function($q) use ($searchTerm) {
                    $q->where('unit_no', 'like',$searchTerm )
                    ->orWhere('ref_no', 'like', $searchTerm)

                    ->orWhereHas('communityProperty', function ($query) use ($searchTerm) {
                        $query->where('name', 'like', $searchTerm);
                    })
                    ->orWhereHas('subCommunity', function ($query) use ($searchTerm) {
                        $query->where('name', 'like', $searchTerm);
                    })
                    ->orWhereHas('community', function ($query) use ($searchTerm) {
                        $query->where('name', 'like', $searchTerm);
                    });
                });
            });
            $query->with(['bed', 'details',
                'agent',
                'propertyCategory.portals',
                'price',
                'bath.portals',
                'amenities.amenity.portals',
                'community.region',
                'subCommunity',
                'communityProperty',
                'photos',
                'floorPlan']);
            // $list_per_page = '50';
            $lead_count = $query->count();
            $leads = $query->get();

            if ($lead_count > 0) {

                return $this->sendResponse(true, 'Property list', ListingResource::collection($leads), 200);
                // $leads->appends($request->query())->links();
            } else {
                return $this->sendResponse(false, 'Property not found', [], 200);
            }

            return $this->sendResponse(true, 'Property list', compact('leads'), 200);
        } catch (\Throwable $th) {
            // dd($th);
            return $this->sendResponse(false, 'Something went wrong!!', $th->getMessage(), 500);

        }
    }

    public function getAxListing(Request $request, $ref_no)
    {
        // dd($request->all());
        if (!$request->secret) {
            return $this->sendResponse(false, 'You are not authorized', [], 403);
        }
        if ($request->secret != 'd23f221f38f1d29ca5f8ege1dhbj8662') {
            return $this->sendResponse(false, 'You are not authorized', [], 403);
        }

        $property = Property::where('ref_no', $ref_no)->with(['bed', 'details',
            'agent',
            'propertyCategory.portals',
            'price',
            'bath.portals',
            'amenities.amenity.portals',
            'community.region',
            'subCommunity',
            'communityProperty',
            'photos',
            'floorPlan'])->first();
        if ($property) {
            $property = new ListingResourceDetailed($property);
            return $this->sendResponse(true, 'Property found', compact('property'), 200);
        } else {
            return $this->sendResponse(false, 'Property not found', [], 200);
        }

    }

    private function isArray($key)
    {
        return gettype(\request($key)) == 'array' && count(\request($key)) > 0;
    }
}
