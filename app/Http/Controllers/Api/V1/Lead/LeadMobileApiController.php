<?php

namespace App\Http\Controllers\Api\V1\Lead;

use App\Http\Controllers\Admin\Lead\LeadController;
use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\V1\Lead\GetLeadListRequest;
use App\Http\Resources\ContactAXResource;
use App\Http\Resources\LeadAxResource;
use App\Http\Resources\LeadNoteResource;
use App\Http\Resources\LeadResource;
use App\Http\Tasks\Api\V1\Lead\LeadListTaskV2;
use App\Http\Tasks\Api\V1\Lead\SearchLeadListTaskV2;
use App\Models\CallLog;
use App\Models\Community;
use App\Models\CommunityProperty;
use App\Models\Contact;
use App\Models\Event;
use App\Models\EventType;
use App\Models\Lead;
use App\Models\LeadCriteria;
use App\Models\LeadNote;
use App\Models\LeadStatus;
use App\Models\ListingLead;
use App\Models\ListingLeadNote;
use App\Models\Medium;
use App\Models\Property;
use App\Models\Source;
use App\Models\SubCommunity;
use App\Models\Type;
use App\Models\User;
use App\Traits\CallTrait;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Validator;

class LeadMobileApiController extends BaseController
{
    use CallTrait;
    /**
     *
     *
     * Lead List
     *
     * Get a list of leads. with pagination
     * @group Mobile Leads
     * @authenticated
     * @header Accept application/json
     * @queryParam search string Search by first name, last name, email or mobile Example: John, Doe, johan@demo.com, 1234567890
     * @queryParam agent_id int Agent ID Example: 1
     * @queryParam lead_status_id int Lead Status ID Example: 1
     * @queryParam medium_id int Medium ID Example: 1
     * @queryParam source_id int Source ID Example: 1
     * @queryParam type_id int Type ID Example: 1
     * @queryParam min_bed_id int Minimum bed id Example: 1
     * @queryParam max_bed_id int Max bed id Example: 1
     * @queryParam min_bath_id int Minimum bath id Example: 1
     * @queryParam max_bath_id int Maximum bath ID Example: 1
     * @queryParam furnished int furnished Example: 1
     * @queryParam communities int for search in communities Example: 1
     * @queryParam sub_communities int for search in sub communities Example: 1
     * @queryParam type_id int Type ID Example: 1
     * @queryParam type_id int Type ID Example: 1
     * @queryParam region_id int Region ID Example: 1
     * @queryParam created_at string Created at Example: 2023-04-24
     * @queryParam updated_at string Updated at Example: 2023-04-24
     * @queryParam page int Page number Example: 1
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
     * {
    "status": true,
    "code": 200,
    "message": "Lead list",
    "data": {
    "leads": {
    "current_page": 1,
    "data": [
    {
    "id": 20364,
    "first_name": "suresh",
    "last_name": "chandra",
    "email": "rahat@demo.com",
    "mobile": "+971435435345",
    "phone": "+97176786768",
    "whatsapp": "+971435435345",
    "address": "Sec-1 shankar nagar",
    "company": "Ravi Industries",
    "dob": "2023-04-24",
    "gender": "Male",
    "for": [
    "Residential for sale"
    ],
    "nationality": {
    "id": 101,
    "name": "India",
    "code": "IN"
    },
    "lead_status": {
    "id": 1,
    "name": "Open"
    },
    "lead_sub_status": {
    "id": 4,
    "name": "Not yet contacted"
    },
    "lead_type": {
    "id": 1,
    "name": "Tenant"
    },
    "lead_source": {
    "id": 2,
    "name": "Agent External"
    },
    "lead_medium": {
    "id": 3,
    "name": "SMS"
    },
    "agent": {
    "id": 236,
    "name": "khagesh chandrakar",
    "full_name": "khagesh chandrakar",
    "first_name": "khagesh",
    "last_name": "chandrakar",
    "position": "Agent",
    "email": "khagesh.office@gmail.com",
    "secondary_email": "khagesh.office@gmail.com",
    "mobile": "+97154343535",
    "thumbnail": "https://s3.eu-west-1.amazonaws.com/secondary.crm/images/avatar/thumb_168233832720230424161207.jpeg",
    "image": "https://s3.eu-west-1.amazonaws.com/secondary.crm/images/avatar/168233832720230424161207.jpeg",
    "avatar": null,
    "telegram_username": "khageshoo7"
    },
    "additional_agent": [
    {
    "id": 4,
    "name": null,
    "full_name": null,
    "first_name": "Dimitry",
    "last_name": "Zolotco",
    "position": null,
    "email": "dmitri.zolotco@axcapmail.com",
    "secondary_email": null,
    "mobile": "971522036708",
    "thumbnail": null,
    "image": "https://s3-ap-southeast-1.amazonaws.com/mycrm-pro-accounts-v2/user/avatar/2226/77YmhGZnqtTawMxX.jpeg",
    "avatar": null,
    "telegram_username": null
    },
    {
    "id": 5,
    "name": null,
    "full_name": null,
    "first_name": "Muhammad",
    "last_name": "Mohsan",
    "position": null,
    "email": "m.mohsan@axcapital.ae",
    "secondary_email": null,
    "mobile": "+971521785458",
    "thumbnail": null,
    "image": null,
    "avatar": null,
    "telegram_username": null
    }
    ],
    "property": {
    "id": 3803,
    "ref_no": "axc-3962171",
    "title": "Luxury Brand New Burjkhailfa High Floor",
    "description": "AX CAPITAL Real Estate is offering to the market this amazing and high-quality finishing 1BR Apartment in Sobha Waves, MBR City\r\n\r\nProperty Details:\r\n\r\n- Community: Sobha Hartland, MBR City\r\n- Building: Sobha Waves (Tower B)\r\n- Type: 1 bedroom, 1 bathroom, Kitchen Appliances\r\n- Area: 519.03 sqft\r\n- Parking: 1 Parking space\r\n- Cheque: 02 cheque\r\n- Status: Ready\r\n\r\nAmenities:\r\n\r\n- Central air conditioning\r\n- Built-in wardrobes\r\n- Floor-to-ceiling windows\r\n- Grand lobby\r\n- Gymnasium\r\n- Swimming pool\r\n- Health club\r\n- Sunbathing areas\r\n- Security 24/7\r\n\r\nSobha Waves is a residential Building in Sobha Hartland, MBR City. Overlooking Al Khail Road, the buildings have 28 floors and offer 1 and 2-bedroom apartments. In-house facilities include parking, security, and fitness options.\r\n\r\nMohammed Bin Rashid City (MBR City) is a well-planned community in Dubai. It is located far from the city’s central areas yet easily accessible via Al Khail Road and Dubai – Al Ain Road. Residents can find shopping, dining, leisure, education, and healthcare facilities near the complex.\r\n\r\nFitness options in Sobha Hartland Creek Vista include a gymnasium and swimming pool, whereas children get a dedicated play area. Concierge staff in the spacious lobby welcomes guests and visitors. One can easily travel around using major highways and public transport.\r\n\r\nAlternatively, visit our website [link not available] where you will find an extensive selection of properties available for sale and rent.\r\nAsk us about:\r\n\r\n* Off-Plan\r\n* Property Management\r\n* Interior Design (Ax Design)\r\n* Exclusive Off-Plan\r\n* Short Term Let",
    "for": "Residential for sale",
    "unit_no": "767868",
    "publish_status": "PUBLISHED",
    "photos": [
    {
    "full": "https://s3.eu-west-1.amazonaws.com/secondary.crm/images/listings/168234076220230424165242t4ThqojRWd.jpg",
    "thumbnail": "https://s3.eu-west-1.amazonaws.com/secondary.crm/images/listings/thumb_168234076220230424165242t4ThqojRWd.jpg"
    }
    ]
    },
    "criteria": {
    "id": 62,
    "lead_id": 20364,
    "min_price": "10000",
    "max_price": "110000",
    "min_bath_id": 1,
    "max_bath_id": 4,
    "min_bed_id": 1,
    "max_bed_id": 4,
    "communities": null,
    "furnished": "FURNISHED",
    "created_at": "2023-04-24T05:27:01.000000Z",
    "locations": [
    {
    "id": 1,
    "name": "Al Aweer",
    "slug": "al-aweer",
    "description": null,
    "path_name": "Al Aweer, Dubai",
    "latitude": "25.1784957",
    "longitude": "55.545002"
    },
    {
    "id": 2,
    "name": "Zabeel",
    "slug": "zabeel",
    "description": null,
    "path_name": "Zabeel, Dubai",
    "latitude": "25.2182542",
    "longitude": "55.2977618"
    },
    {
    "id": 2,
    "name": "Warsan Estate",
    "slug": "warsan-estate",
    "description": null,
    "path_name": "Warsan Estate, Al Aweer, Dubai",
    "latitude": "25.18356046",
    "longitude": "55.55390596"
    },
    {
    "id": 1,
    "name": "Al Aweer Village",
    "slug": "al-aweer-village",
    "description": null,
    "path_name": "Al Aweer Village, Al Aweer, Dubai",
    "latitude": "25.1756573",
    "longitude": "55.5469032"
    }
    ]
    },
    "priority": "Urgent",
    "status": 1,
    "lead_assignment_date": null,
    "follow_up_date": "2023-04-25",
    "viewing_date": null,
    "created_at": "2023-04-24T05:27:01.000000Z",
    "updated_at": "2023-04-24T12:20:30.000000Z"
    },
    {
    "id": 20363,
    "first_name": "Elena",
    "last_name": "Doe",
    "email": "elena442@gmail.com",
    "mobile": "+971562870158",
    "phone": null,
    "whatsapp": "971562870158",
    "address": null,
    "company": null,
    "dob": null,
    "gender": null,
    "for": [
    "Residential for sale"
    ],
    "nationality": null,
    "lead_status": {
    "id": 1,
    "name": "Open"
    },
    "lead_sub_status": {
    "id": 4,
    "name": "Not yet contacted"
    },
    "lead_type": {
    "id": 2,
    "name": "Buyer"
    },
    "lead_source": {
    "id": 31,
    "name": "Property Finder"
    },
    "lead_medium": {
    "id": 1,
    "name": "Email"
    },
    "agent": {
    "id": 160,
    "name": "Aida Cheraghalian",
    "full_name": "Aida Cheraghalian",
    "first_name": "Aida",
    "last_name": "Cheraghalian",
    "position": "Agent",
    "email": "a.cheraghalian@axcapital.ae",
    "secondary_email": null,
    "mobile": "+971529264805",
    "thumbnail": null,
    "image": null,
    "avatar": null,
    "telegram_username": null
    },
    "additional_agent": [],
    "property": {
    "id": 3457,
    "ref_no": "axc-3961828",
    "title": "Private Beach Access | 3 Bedrooms | Genuine",
    "description": "AX CAPITAL Real Estate is pleased to present this 3-Bedroom apartment located in Marina Vista, EMAAR Beachfront.\r\n\r\nProperty Details:\r\n\r\n- 3 Bedroom\r\n- 4 Bathroom\r\n- Furnished\r\n- Parking Space\r\n- Powder Room\r\n- 2 Balconies\r\n- Sea and Marina Skyline Views\r\n- Reference no. axc-3961828\r\n\r\nMarina Vista is set in one of the most prestigious locations on the Emaar Beachfront. It's one of the first residences as you drive into the private island and nearest to Dubai Marina, which can be easily reached on foot. It also provides easy access to Sheikh Zayed Road whenever you wish to go on a city adventure.\r\n\r\nAre you searching for a brand-new development, built by the best developer in Dubai? Do you want to live on a private beach with amazing sea views and the Dubai Marina skyline? Well, look no further.\r\n\r\nThis 3-bedroom apartment at Marina Vista, Emaar Beachfront comprises of an open-plan Living Room and Kitchen/Diner with a wrap-around balcony. Then you enjoy a powder room, 3 bedrooms, and 4 bathrooms. The master balcony has access to a second balcony.\r\n\r\nPlease note all measurements and information are given to the best of our knowledge.",
    "for": "Residential for sale",
    "unit_no": "-",
    "publish_status": "PUBLISHED",
    "photos": [
    {
    "full": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/listings/167956111120230323084511mR3DuRpHVW.jpg",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/listings/thumb_167956111120230323084511mR3DuRpHVW.jpg"
    },
    {
    "full": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/listings/167956111420230323084514HMsH4QjHgE.jpg",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/listings/thumb_167956111420230323084514HMsH4QjHgE.jpg"
    },
    {
    "full": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/listings/167956112220230323084522RfMo0t8Erk.jpg",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/listings/thumb_167956112220230323084522RfMo0t8Erk.jpg"
    },
    {
    "full": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/listings/167956111020230323084510IBA7glYsSn.jpg",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/listings/thumb_167956111020230323084510IBA7glYsSn.jpg"
    },
    {
    "full": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/listings/167956110320230323084503WEkXcSMNHA.jpg",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/listings/thumb_167956110320230323084503WEkXcSMNHA.jpg"
    },
    {
    "full": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/listings/167956112520230323084525MzCXVhjKEO.jpg",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/listings/thumb_167956112520230323084525MzCXVhjKEO.jpg"
    },
    {
    "full": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/listings/167956112120230323084521hRULVS1M4o.jpg",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/listings/thumb_167956112120230323084521hRULVS1M4o.jpg"
    },
    {
    "full": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/listings/167956110420230323084504eDsxOSmYpn.jpg",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/listings/thumb_167956110420230323084504eDsxOSmYpn.jpg"
    },
    {
    "full": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/listings/16795611072023032308450712cs4EXTFo.jpg",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/listings/thumb_16795611072023032308450712cs4EXTFo.jpg"
    },
    {
    "full": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/listings/167956110820230323084508rGsHWmYzNL.jpg",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/listings/thumb_167956110820230323084508rGsHWmYzNL.jpg"
    },
    {
    "full": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/listings/167956111720230323084517xRQGRxKBj7.jpg",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/listings/thumb_167956111720230323084517xRQGRxKBj7.jpg"
    },
    {
    "full": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/listings/1679561118202303230845183BSTiKMlaU.jpg",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/listings/thumb_1679561118202303230845183BSTiKMlaU.jpg"
    },
    {
    "full": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/listings/167956112720230323084527dy77QsNLeu.jpg",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/listings/thumb_167956112720230323084527dy77QsNLeu.jpg"
    },
    {
    "full": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/listings/1679561130202303230845305Ay0dydPfx.jpg",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/listings/thumb_1679561130202303230845305Ay0dydPfx.jpg"
    },
    {
    "full": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/listings/167956111420230323084514JXRKIh89es.jpg",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/listings/thumb_167956111420230323084514JXRKIh89es.jpg"
    }
    ]
    },
    "criteria": {
    "id": 59,
    "lead_id": 20363,
    "min_price": "10000",
    "max_price": "110000",
    "min_bath_id": 1,
    "max_bath_id": 4,
    "min_bed_id": 2,
    "max_bed_id": 3,
    "communities": null,
    "furnished": "FURNISHED",
    "created_at": "2023-04-20T11:47:33.000000Z",
    "locations": [
    {
    "id": 1,
    "name": "Al Aweer",
    "slug": "al-aweer",
    "description": null,
    "path_name": "Al Aweer, Dubai",
    "latitude": "25.1784957",
    "longitude": "55.545002"
    },
    {
    "id": 2,
    "name": "Zabeel",
    "slug": "zabeel",
    "description": null,
    "path_name": "Zabeel, Dubai",
    "latitude": "25.2182542",
    "longitude": "55.2977618"
    },
    {
    "id": 1,
    "name": "Al Aweer Village",
    "slug": "al-aweer-village",
    "description": null,
    "path_name": "Al Aweer Village, Al Aweer, Dubai",
    "latitude": "25.1756573",
    "longitude": "55.5469032"
    },
    {
    "id": 3,
    "name": "Zabeel 1",
    "slug": "zabeel-1",
    "description": null,
    "path_name": "Zabeel 1, Zabeel, Dubai",
    "latitude": "25.22358",
    "longitude": "55.30603"
    },
    {
    "id": 2,
    "name": "Warsan Estate",
    "slug": "warsan-estate",
    "description": null,
    "path_name": "Warsan Estate, Al Aweer, Dubai",
    "latitude": "25.18356046",
    "longitude": "55.55390596"
    },
    {
    "id": 4,
    "name": "Zabeel 2",
    "slug": "zabeel-2",
    "description": null,
    "path_name": "Zabeel 2, Zabeel, Dubai",
    "latitude": "25.207908",
    "longitude": "55.2977618"
    }
    ]
    },
    "priority": null,
    "status": 1,
    "lead_assignment_date": null,
    "follow_up_date": null,
    "viewing_date": null,
    "created_at": "2023-04-18T08:46:31.000000Z",
    "updated_at": "2023-04-21T08:44:44.000000Z"
    },
     * }
     * ],
    "first_page_url": "http://127.0.0.1:8000/api/v1/leads?page=1",
    "from": 1,
    "last_page": 867,
    "last_page_url": "http://127.0.0.1:8000/api/v1/leads?page=867",
    "links": [
    {
    "url": null,
    "label": "&laquo; Previous",
    "active": false
    },
    {
    "url": "http://127.0.0.1:8000/api/v1/leads?page=1",
    "label": "1",
    "active": true
    },
    {
    "url": "http://127.0.0.1:8000/api/v1/leads?page=2",
    "label": "2",
    "active": false
    },
    {
    "url": "http://127.0.0.1:8000/api/v1/leads?page=3",
    "label": "3",
    "active": false
    },
    {
    "url": "http://127.0.0.1:8000/api/v1/leads?page=4",
    "label": "4",
    "active": false
    },
    {
    "url": "http://127.0.0.1:8000/api/v1/leads?page=5",
    "label": "5",
    "active": false
    },
    {
    "url": "http://127.0.0.1:8000/api/v1/leads?page=6",
    "label": "6",
    "active": false
    },
    {
    "url": "http://127.0.0.1:8000/api/v1/leads?page=7",
    "label": "7",
    "active": false
    },
    {
    "url": "http://127.0.0.1:8000/api/v1/leads?page=8",
    "label": "8",
    "active": false
    },
    {
    "url": "http://127.0.0.1:8000/api/v1/leads?page=9",
    "label": "9",
    "active": false
    },
    {
    "url": "http://127.0.0.1:8000/api/v1/leads?page=10",
    "label": "10",
    "active": false
    },
    {
    "url": null,
    "label": "...",
    "active": false
    },
    {
    "url": "http://127.0.0.1:8000/api/v1/leads?page=866",
    "label": "866",
    "active": false
    },
    {
    "url": "http://127.0.0.1:8000/api/v1/leads?page=867",
    "label": "867",
    "active": false
    },
    {
    "url": "http://127.0.0.1:8000/api/v1/leads?page=2",
    "label": "Next &raquo;",
    "active": false
    }
    ],
    "next_page_url": "http://127.0.0.1:8000/api/v1/leads?page=2",
    "path": "http://127.0.0.1:8000/api/v1/leads",
    "per_page": 10,
    "prev_page_url": null,
    "to": 10,
    "total": 8670
    }
    }
     *}
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @response 200 scenario="leads not found"
     * {
    "status": false,
    "code": 200,
    "message": "No Leads found",
    "data": []
    }
     */
    public function index(GetLeadListRequest $request, LeadListTaskV2 $task)
    {
        if (!auth()->user()->can('Leads List')) {
            return $this->sendResponse(false, 'You are not authorized to access this resource', [], 403);
        }
        $response = $task->run($request);
        return $this->sendResponse($response['status'], $response['message'], $response['data'], $response['code']);
    }

    public function searchLeads(GetLeadListRequest $request, SearchLeadListTaskV2 $task)
    {
        if (!auth()->user()->can('Leads List')) {
            return $this->sendResponse(false, 'You are not authorized to access this resource', [], 403);
        }
        $response = $task->run($request);
        return $this->sendResponse($response['status'], $response['message'], $response['data'], $response['code']);
    }

    // "agent_id" => "nullable|exists:users,id",
    // "for" => "nullable|array",
    // "first_name" => "nullable|string",
    // "last_name" => "nullable|string",
    // "mobile" => "nullable|regex:/^\+\d{1,3}\d{6,14}$/|min:8",
    // "phone" => "nullable|regex:/^\+\d{1,3}\d{6,14}$/|min:8",
    // "whatsapp" => "nullable|string|min:8",
    // "email" => "nullable|email:rfc,dns",
    // "nationality_id" => "nullable|exists:countries,id",
    // "gender" => "nullable|string",
    // "dob" => "nullable|date",
    // "follow_up_date" => "nullable|date",
    // "address" => "nullable|string",
    // "company" => "nullable|string",
    // "property_id" => "nullable|exists:properties,id",
    // "type_id" => "nullable|exists:types,id",
    // "source_id" => "nullable|exists:sources,id",
    // "lead_medium_id" => "nullable|exists:mediums,id",
    // "lead_status_id" => "nullable|exists:lead_statuses,id",
    // // "lead_sub_status_id" => "nullable|exists:lead_sub_statuses,id",

    // "telesales_head_id" => "nullable|exists:users,id",
    // "telesales_id" => "nullable|exists:users,id",

    // "priority" => "nullable|string",

    /**
     * Create a Mobile Lead
     *
     * Create a Mobile Lead.
     * @group Mobile Leads
     * @authenticated
     * @header Accept application/json


     * @bodyParam agent_id integer optional Agent ID. Example: +9715285858176
     * @bodyParam for array optional For
     * @bodyParam first_name string optional First Name
     * @bodyParam last_name string optional Last Name
     * @bodyParam mobile string optional Mobile
     * @bodyParam phone string optional Phone
     * @bodyParam whatsapp string optional Whatsapp
     * @bodyParam email string optional Email
     * @bodyParam nationality_id integer
     * @bodyParam gender string optional
     * @bodyParam dob date optional
     * @bodyParam follow_up_date date optional
     * @bodyParam address string optional
     * @bodyParam company string optional
     * @bodyParam property_id integer optional Property ID
     * @bodyParam type_id integer optional Type ID
     * @bodyParam source_id integer optional Source ID
     * @bodyParam lead_medium_id integer optional Lead Medium ID
     * @bodyParam lead_status_id integer optional Lead Status ID
     * @bodyParam lead_sub_status_id integer optional Lead Sub Status ID
     * @bodyParam telesales_head_id integer optional Telesales Head ID
     * @bodyParam telesales_id integer optional Telesales ID
     * @bodyParam priority string optional Priority
     * @bodyParam min_bed_id string optional Priority
     * @bodyParam max_bed_id string optional Priority
     * @bodyParam min_bath_id string optional Priority
     * @bodyParam max_bath_id string optional Priority
     * @bodyParam min_price string optional Priority
     * @bodyParam max_price string optional Priority
     * @bodyParam communities string optional Priority
     * @bodyParam sub_communities string optional Priority
     * @bodyParam furnished string optional Priority
     * @bodyParam view string optional Priority
     *
     * @response 200 scenario="Without Property"
     * {
    "status": true,
    "code": 200,
    "message": "Lead created successfully",
    "data": {
    "first_name": "Fraz Shabbir",
    "property_id": null,
    "agent_id": 7,
    "email": "f.shabbir@axcapital.ae",
    "for": "Unspecified",
    "mobile": "+971585858176",
    "whatsapp": "+971585858176",
    "lead_medium_id": 3,
    "source_id": 47,
    "lead_type_id": 10,
    "type_id": 10,
    "updated_at": "2023-05-09T08:21:53.000000Z",
    "created_at": "2023-05-09T08:21:53.000000Z",
    "id": 23207
    }

     * @response 200 scenario="With Property"
     * {
    "status": true,
    "code": 200,
    "message": "Lead created successfully",
    "data": {
    "property": {
    "reference_no": "axc-1862607",
    "unit_no": "-",
    "price": null,
    "location": "Palme Couture, Palm Jumeirah, Dubai",
    "agent": {
    "name": "Dimitry",
    "email": "dmitri.zolotco@axcapmail.com",
    "telegram_chatid": null
    }
    }
    }
     * @response 200 scenario="Validation Error"
     * {
    "status": false,
    "code": 422,
    "message": "Validation Error",
    "data": {
    "customer_mobile": [
    "The customer mobile field is required."
    ]
    }
     * }
     * @response 400 scenario="Exception  "
     * {
    "status": false,
    "code": 500,
    "message": "EXCEPTION MESSAGE",
    "data": null
     * }*/
    public function createLead(Request $request)
    {
        $LeadController = new LeadController();
        $response = $LeadController->store($request);
        return $response;
    }

    /**
     * Update a Mobile Lead
     *
     * Update a Mobile Lead.
     * @group Mobile Leads
     * @authenticated
     * @header Accept application/json


     * @bodyParam agent_id integer optional Agent ID. Example: +9715285858176
     * @bodyParam for array optional For
     * @bodyParam first_name string optional First Name
     * @bodyParam last_name string optional Last Name
     * @bodyParam mobile string optional Mobile
     * @bodyParam phone string optional Phone
     * @bodyParam whatsapp string optional Whatsapp
     * @bodyParam email string optional Email
     * @bodyParam nationality_id integer
     * @bodyParam gender string optional
     * @bodyParam dob date optional
     * @bodyParam follow_up_date date optional
     * @bodyParam address string optional
     * @bodyParam company string optional
     * @bodyParam property_id integer optional Property ID
     * @bodyParam type_id integer optional Type ID
     * @bodyParam source_id integer optional Source ID
     * @bodyParam lead_medium_id integer optional Lead Medium ID
     * @bodyParam lead_status_id integer optional Lead Status ID
     * @bodyParam lead_sub_status_id integer optional Lead Sub Status ID
     * @bodyParam telesales_head_id integer optional Telesales Head ID
     * @bodyParam telesales_id integer optional Telesales ID
     * @bodyParam priority string optional Priority
     * @bodyParam min_bed_id string optional Priority
     * @bodyParam max_bed_id string optional Priority
     * @bodyParam min_bath_id string optional Priority
     * @bodyParam max_bath_id string optional Priority
     * @bodyParam min_price string optional Priority
     * @bodyParam max_price string optional Priority
     * @bodyParam communities string optional Priority
     * @bodyParam sub_communities string optional Priority
     * @bodyParam furnished string optional Priority
     * @bodyParam view string optional Priority
     *
     * @response 200 scenario="Without Property"
     * {
    "status": true,
    "code": 200,
    "message": "Lead updated successfully",
    "data": {
    "first_name": "Fraz Shabbir",
    "property_id": null,
    "agent_id": 7,
    "email": "f.shabbir@axcapital.ae",
    "for": "Unspecified",
    "mobile": "+971585858176",
    "whatsapp": "+971585858176",
    "lead_medium_id": 3,
    "source_id": 47,
    "lead_type_id": 10,
    "type_id": 10,
    "updated_at": "2023-05-09T08:21:53.000000Z",
    "created_at": "2023-05-09T08:21:53.000000Z",
    "id": 23207
    }

     * @response 200 scenario="With Property"
     * {
    "status": true,
    "code": 200,
    "message": "Lead updated successfully",
    "data": {
    "property": {
    "reference_no": "axc-1862607",
    "unit_no": "-",
    "price": null,
    "location": "Palme Couture, Palm Jumeirah, Dubai",
    "agent": {
    "name": "Dimitry",
    "email": "dmitri.zolotco@axcapmail.com",
    "telegram_chatid": null
    }
    }
    }
     * @response 200 scenario="Validation Error"
     * {
    "status": false,
    "code": 422,
    "message": "Validation Error",
    "data": {
    "customer_mobile": [
    "The customer mobile field is required."
    ]
    }
     * }
     * @response 400 scenario="Exception  "
     * {
    "status": false,
    "code": 500,
    "message": "EXCEPTION MESSAGE",
    "data": null
     * }*/

    public function updateLead(Request $request)
    {
        $LeadController = new LeadController();
        $response = $LeadController->update($request);
        return $response;
    }

    /**
     *
     *
     *
     * Get Statuses
     * @group Mobile Leads
     * @authenticated
     * @header Accept application/json
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
    "status": 200,
    "code": 200,
    "message": "Data found",
    "data": [
    {
    "id": 1,
    "name": "New",
    "status": 1,
    "order": 10,
    "thumbnail": null,
    "image": null,
    "icon": "bx bxs-user-plus",
    "badge": "badge color-bg-17",
    "created_by_id": null,
    "updated_by_id": 173,
    "created_at": "2023-02-14T03:30:33.000000Z",
    "updated_at": "2023-05-24T10:29:55.000000Z",
    "deleted_at": null
    },
    {
    "id": 10,
    "name": "Not Answered",
    "status": 1,
    "order": 12,
    "thumbnail": null,
    "image": null,
    "icon": "bx bxs-phone-off",
    "badge": "badge color-bg-16",
    "created_by_id": 262,
    "updated_by_id": 1,
    "created_at": "2023-05-24T09:47:58.000000Z",
    "updated_at": "2023-05-25T12:29:07.000000Z",
    "deleted_at": null
    },
    ],
    "page": 1,
    "per_page": 10
    }
     *}
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @response 200 scenario="User not found"
     * {
    "status": false,
    "code": 200,
    "message": "No Data found",
    "data": []
    }
     */

    public function getStatuses(Request $request)
    {
        $statuses = LeadStatus::where('id', '!=', 3)->where('status', true)->orderBy('order', 'asc')->get();
        return $this->sendResponse(200, 'Data found', $statuses, 200);

    }

    /**
     *
     *
     *
     * Save Lead Reason
     * @group Mobile Leads
     * @authenticated
     * @header Accept application/json
    Example: abc
     *
     *
     *
     * @header Accept application/json

     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Lead reason saved."
    {
    "status": true,
    "code": 200,
    "message": "Lead reason saved.",
    "data": null,
    "page": 1,
    "per_page": 10
    }
     *}
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @response 500 scenario="Something Went Wring"
     * {
    "status": false,
    "code": 200,
    "message": "Something Went Wring",
    "data": []
    }
     */
    public function saveReason(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'lead_reason' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(false, 'Validation Error', $validator->errors(), 422);
        }

        try {
            $lead = Lead::find($request->id);
            $lead->reason = $request->lead_reason;
            $lead->lead_status_id = 12;
            $lead->save();
            return $this->sendResponse(true, 'Lead reason saved.', null, 200);

        } catch (\Exception $exception) {
            return $this->sendResponse(false, 'Something Went Wrong.', null, 500);
            return json_encode($result);
        }
    }

    /**
     *
     *
     *
     * Get Statuses
     * @group Mobile Leads
     * @authenticated
     * @header Accept application/json
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
    "status": 200,
    "code": 200,
    "message": "Data found",
    "data": [
    {
    "id": 31,
    "name": "Property Finder",
    "status": 1,
    "order": 5,
    "badge": null,
    "image": null,
    "icon": null,
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/168551642220230531110022.png",
    "is_contact_source": 1,
    "is_lead_source": 1,
    "created_by_id": 1,
    "updated_by_id": 262,
    "created_at": "2023-04-14T06:04:37.000000Z",
    "updated_at": "2023-05-31T07:10:11.000000Z",
    "deleted_at": null
    },
    {
    "id": 125,
    "name": "Houza",
    "status": 1,
    "order": 10,
    "badge": null,
    "image": null,
    "icon": null,
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/168551647220230531110112.png",
    "is_contact_source": 1,
    "is_lead_source": 1,
    "created_by_id": null,
    "updated_by_id": 262,
    "created_at": null,
    "updated_at": "2023-05-31T07:10:17.000000Z",
    "deleted_at": null
    },


    ],
    "page": 1,
    "per_page": 10
    }
     *}
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @response 200 scenario="User not found"
     * {
    "status": false,
    "code": 200,
    "message": "No Data found",
    "data": []
    }
     */
    public function getSources(Request $request)
    {
        $sources = Source::where('is_lead_source', 1)->where('status', true)->orderBy('order', 'asc')->get();
        return $this->sendResponse(200, 'Data found', $sources, 200);

    }

    /**
     *
     *
     *
     * Get Mediums
     * @group Mobile Leads
     * @authenticated
     * @header Accept application/json
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
    "status": 200,
    "code": 200,
    "message": "Data found",
    "data": [
    {
    "id": 1,
    "name": "Email",
    "status": 1,
    "is_contact_medium": 1,
    "is_lead_medium": 1,
    "order": 1,
    "thumbnail": null,
    "created_by_id": 1,
    "updated_by_id": 173,
    "created_at": "2023-04-13T09:54:55.000000Z",
    "updated_at": "2023-06-09T04:18:30.000000Z",
    "deleted_at": null,
    "badge": null,
    "image": null,
    "icon": "las la-envelope"
    },

    ],
    "page": 1,
    "per_page": 10
    }
     *}
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @response 200 scenario="User not found"
     * {
    "status": false,
    "code": 200,
    "message": "No Data found",
    "data": []
    }
     */

    public function getMediums(Request $request)
    {
        $mediums = Medium::where('is_lead_medium', 1)->where('status', true)->orderBy('order', 'asc')->get();
        return $this->sendResponse(200, 'Data found', $mediums, 200);

    }

    /**
     *
     *
     *
     * Get Types
     * @group Mobile Leads
     * @authenticated
     * @header Accept application/json
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
    "status": 200,
    "code": 200,
    "message": "Data found",
    "data": [
    {
    "id": 7,
    "name": "Agent",
    "status": 1,
    "is_contact_type": 1,
    "is_lead_type": 1,
    "order": 0,
    "icon": null,
    "badge": null,
    "created_by_id": null,
    "updated_by_id": 262,
    "created_at": "2023-02-14T03:30:33.000000Z",
    "updated_at": "2023-10-04T09:14:11.000000Z",
    "deleted_at": null,
    "image": null,
    "thumbnail": null
    },

    ],
    "page": 1,
    "per_page": 10
    }
     */
    public function getTypes(Request $request)
    {
        $types = Type::where('is_lead_type', 1)->where('status', true)->orderBy('order', 'asc')->get();
        return $this->sendResponse(200, 'Data found', $types, 200);

    }

    /**
     *
     *
     *
     * Get Agent Properties
     * @group Mobile Leads
     * @authenticated
     * @header Accept application/json
    Example: abc
     *
     *
     *
     * @header Accept application/json
     * @bodyParam agent_id int agent id for getting his properties. Example: 172

     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
    {
    "status": 200,
    "code": 200,
    "message": "Data found",
    "data": {
    "id": 13683,
    "my_crm_id": null,
    "ref_no": "axc-3972035",
    "title": "Huge Plot | Corner Unit | Single Row",
    "description": "Ax Capital Real Estate is delighted to present this beautiful 5 bedrooms Villa in Nad Al Sheba Gardens Phase 2 for Sale situated in the popular development area of Maydan.\r\n\r\n-Plot size: 8110.13sqft\r\n-Bulit up: 6487.35sqft\r\n-Bedrooms: 5\r\n-Completion: Q2 of 2025\r\n\r\nVilla Specifications:\r\n* Maid's Room.\r\n* Laundry Room.\r\n* Powder Room\r\n* In-Home Elevator\r\n* Double Height Living Space\r\n* Walk in Closets\r\n* Laundry Room\r\n* Floor-to-Ceiling Windows\r\n* High-End Finishing\r\n* Private parking\r\n* Gated community\r\n\r\nKey features and highlights of Nad Al Sheba Gardens:\r\n\r\n* 05 Minutes to Meydan Racetrack\r\n* 10 Minutes to Downtown\r\n* 10 Minutes to Dubai Hills Mall\r\n* 10 Minutes proximity to Major Schools in Dubai\r\n* 03 Minutes to Al Ain Road\r\n* 15 Minutes to Dubai International Airport\r\n\r\nCommunity Amenities:\r\n* Central Park\r\n* Primary/Secondary Schools\r\n* Cycling Tracks\r\n* Organic markets\r\n* Community farms\r\n* Kids Play area\r\n* Swimming pools\r\n* Outdoor Gyms\r\n* Sports Courts\r\n\r\nWith its meandering lanes and open front lawns, the private gated community of Nad Al Sheba Gardens welcomes you to live in Dubai at your own pace. In a space carefully designed to flow seamlessly with your everyday life as you nurture your inner and outer growth. Offering numerous types of luxury townhouses and villas, with sizes ranging from 243.22 sqm (2,618 sqft) to 610.06 sqm (6,567 sqft)\r\n\r\nThe community is designed in an organic manner, with meandering streets and open front lawns providing a unique character. The amenities in community go above and beyond the ordinary, which include beekeeping areas, community farms, world- class primary and secondary school, cycling tracks, multipurpose sports areas, training fields, clubhouses to name but a few.\r\n\r\nAlternatively visit our website https://www.axcapital.ae/ where you will find an extensive selection of properties available both for sale and for rent.\r\nAsk us about:\r\n\r\n* Off-Plan\r\n* Property Management\r\n* Interior Design (Ax Design) \r\n* Exclusive Off-Plan \r\n* Short Term Let",
    "listing_source": null,
    "for": "Residential for sale",
    "region_id": 1,
    "community_id": 112,
    "sub_community_id": 1947,
    "community_property_id": 3369,
    "property_category_id": 2,
    "bed_id": 6,
    "bath_id": 7,
    "unit_no": "0000",
    "built_up_area": "6487.35",
    "plot_size": "8110.07",
    "street": null,
    "floor": null,
    "view": "[\"Community View\",\"Garden View\"]",
    "parking": 3,
    "dewa_number": null,
    "rera_permit_number": null,
    "rera_issue_date": null,
    "rera_expiry_date": null,
    "rera_validation_url": null,
    "property_type_id": null,
    "property_status_id": 12,
    "completion_status": "Off-Plan Secondary",
    "rented_at": null,
    "rented_until": null,
    "next_available": null,
    "availability_date": "2024-01-11",
    "remind": null,
    "furnished": "UNFURNISHED",
    "featured": 0,
    "exclusive": 0,
    "exclusive_start": null,
    "exclusive_end": null,
    "hot": 0,
    "publish_status": "SOLD",
    "admin_unpublish_reason": null,
    "agent_id": 172,
    "previous_agent_id": null,
    "external_agent_id": null,
    "telesales_id": null,
    "owner_id": 14931,
    "developer_id": 6,
    "cheques": null,
    "mycrm_updated": 0,
    "created_by_id": 238,
    "updated_by_id": 172,
    "video_link": null,
    "video_360_link": null,
    "qr_link": null,
    "title_deed_text": null,
    "data_version": 1,
    "is_archived": 0,
    "payment_method": null,
    "down_payment_price": null,
    "build_year": null,
    "created_at": "2024-01-11T12:17:37.000000Z",
    "updated_at": "2024-04-23T13:49:57.000000Z",
    "deleted_by_id": null,
    "deleted_at": null,
    "state": null,
    "is_live": 0,
    "request_status_type": null,
    "PASSWORD": null,
    "villa_type": "[\"SR\"]",
    "contract_start": null,
    "contract_end": null,
    "contract_number": null,
    "published_date": null,
    "unpublished_date": null,
    "unable_to_generate_qr": 0,
    "offplan_portal_fetch": 0,
    "republished_date": null,
    "first_image": {
    "id": 359211,
    "property_id": 13683,
    "title": null,
    "description": null,
    "path": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/171351412520240419120845JKJR20P80H.jpg",
    "original": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/original_171351412520240419120845JKJR20P80H.jpg",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb_171351412520240419120845JKJR20P80H.jpg",
    "password": null,
    "note": null,
    "order": 285490,
    "type": "Photos",
    "temp_image": 0,
    "not_uploaded_on_s3": 0,
    "status": 1,
    "media_changed": null,
    "temp_id": null,
    "created_by_id": 1,
    "updated_by_id": null,
    "created_at": "2024-04-19T08:08:45.000000Z",
    "updated_at": "2024-04-19T08:08:45.000000Z",
    "deleted_at": null,
    "deleted_by_id": null
    },
    "category": "Villa",
    "location": "Nad Al Sheba Gardens - Phase 2, Nad Al Sheba 1, Nadd Al Sheba, Dubai",
    "my_property": true,
    "bed": {
    "id": 6,
    "name": "5 beds",
    "status": 1,
    "order": 5,
    "created_by_id": null,
    "updated_by_id": 1,
    "deleted_at": null,
    "created_at": "2023-02-14T03:30:31.000000Z",
    "updated_at": "2023-05-31T11:49:25.000000Z"
    },
    "property_category": {
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
    "created_at": "2023-02-14T03:30:33.000000Z",
    "updated_at": "2023-06-26T14:22:02.000000Z",
    "deleted_at": null
    },
    "community": {
    "id": 112,
    "region_id": 1,
    "name": "Nadd Al Sheba",
    "alternate_name": null,
    "slug": "nadd-al-sheba",
    "description": null,
    "path_name": "Nadd Al Sheba, Dubai",
    "alternate_path_name": null,
    "latitude": "25.1435458",
    "longitude": "55.3475331",
    "created_by_id": 1,
    "updated_by_id": 1,
    "status": 1,
    "created_at": "2020-04-29T19:54:06.000000Z",
    "updated_at": "2020-04-29T19:54:06.000000Z",
    "deleted_at": null
    },
    "sub_community": {
    "id": 1947,
    "region_id": 1,
    "community_id": 112,
    "name": "Nad Al Sheba 1",
    "alternate_name": null,
    "slug": "nad-al-sheba-1",
    "description": null,
    "path_name": "Nad Al Sheba 1, Nadd Al Sheba, Dubai",
    "alternate_path_name": null,
    "latitude": "25.1591193",
    "longitude": "55.3088196",
    "created_by_id": 1,
    "updated_by_id": 1,
    "status": 1,
    "created_at": "2020-04-29T19:54:06.000000Z",
    "updated_at": "2020-04-29T19:54:06.000000Z",
    "deleted_at": null
    },
    "portals": [],
    "price": {
    "id": 12766,
    "property_id": 13683,
    "price": "17000000",
    "prices": null,
    "price_on_application": 0,
    "created_at": "2024-01-11T12:17:37.000000Z",
    "updated_at": "2024-01-11T12:17:37.000000Z"
    },
    "agent": {
    "id": 172,
    "bitrix_role": null,
    "entity": null,
    "group_id": null,
    "parent_id": 11,
    "public_profile_id": null,
    "old_user_id": null,
    "slug": "muhammad-saim-aziz",
    "title": null,
    "name": "Muhammad Saim Aziz",
    "first_name": "Muhammad Saim",
    "last_name": "Aziz",
    "position": "Dev",
    "email": "smirza277@gmail.com",
    "secondary_email": "smirza277@gmail.com",
    "email_verified_at": null,
    "last_login": "2024-06-04 11:32:44",
    "mobile": "+971588159645",
    "api_access": 0,
    "api_rate_limit": 0,
    "telegram_username": null,
    "telegram_chatid": "5970238984",
    "call_tracking_user_id": null,
    "call_tracking_account_id": null,
    "bitrix_id": null,
    "website_link": null,
    "facebook_link": null,
    "instagram_link": null,
    "youtube_link": null,
    "linkedin_link": null,
    "bio": null,
    "license_no": null,
    "pp_enabled": null,
    "pp_name": null,
    "pp_photo": null,
    "pp_license_no": null,
    "country_id": null,
    "state_id": null,
    "nationality_id": null,
    "community_id": null,
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/thumb_168457219720230520124317.jpg",
    "image": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/168457219720230520124317.jpg",
    "avatar": null,
    "color_code": null,
    "status": 1,
    "region_id": 1,
    "traccar_status": 1,
    "device_identifier": "172",
    "port": "5055",
    "mic_status": 1,
    "device_token": "eeHUT4cDSpCXSLuXS9Vfnb:APA91bG25Q-F8MrTCeb7bP9wwCyUDQERFVChZ2rNZhjAm-OurL58LN8AcCfrI_1DeAaVAZ8J9wJccnEtQ3bfC9NMW7UnxohuQZzCixslZjwN3U5yt9uEAMgqsuTLCCPMzbE2fK7BUNLl",
    "call_tracking": 1,
    "call_lead": 0,
    "is_commercial": 0,
    "created_by_id": 1,
    "updated_by_id": 172,
    "created_at": "2023-02-15T06:42:31.000000Z",
    "updated_at": "2024-06-04T07:32:44.000000Z",
    "deleted_at": null,
    "google2fa_secret": "H6AUE7QXV7OAHEGK",
    "telegram2fa_secret": null,
    "back_up_google2fa_secret": null,
    "login_attempt": 0,
    "joining_date": "2019-02-28 11:39:59",
    "last_deal_date": null,
    "is_report_exempt": 0,
    "full_name": "Muhammad Saim Aziz",
    "thumb_url": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/thumb_168457219720230520124317.jpg",
    "image_url": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/168457219720230520124317.jpg",
    "role_list": [
    "Agent"
    ]
    },
    "photos": [
    {
    "id": 359211,
    "property_id": 13683,
    "title": null,
    "description": null,
    "path": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/171351412520240419120845JKJR20P80H.jpg",
    "original": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/original_171351412520240419120845JKJR20P80H.jpg",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb_171351412520240419120845JKJR20P80H.jpg",
    "password": null,
    "note": null,
    "order": 285490,
    "type": "Photos",
    "temp_image": 0,
    "not_uploaded_on_s3": 0,
    "status": 1,
    "media_changed": null,
    "temp_id": null,
    "created_by_id": 1,
    "updated_by_id": null,
    "created_at": "2024-04-19T08:08:45.000000Z",
    "updated_at": "2024-04-19T08:08:45.000000Z",
    "deleted_at": null,
    "deleted_by_id": null
    },
    {
    "id": 359212,
    "property_id": 13683,
    "title": null,
    "description": null,
    "path": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/171351412620240419120846hfGbncLEiQ.jpg",
    "original": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/original_171351412620240419120846hfGbncLEiQ.jpg",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb_171351412620240419120846hfGbncLEiQ.jpg",
    "password": null,
    "note": null,
    "order": 285491,
    "type": "Photos",
    "temp_image": 0,
    "not_uploaded_on_s3": 0,
    "status": 1,
    "media_changed": null,
    "temp_id": null,
    "created_by_id": 1,
    "updated_by_id": null,
    "created_at": "2024-04-19T08:08:47.000000Z",
    "updated_at": "2024-04-19T08:08:47.000000Z",
    "deleted_at": null,
    "deleted_by_id": null
    },
    {
    "id": 359213,
    "property_id": 13683,
    "title": null,
    "description": null,
    "path": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/171351417220240419120932Y9XZtqil2T.jpg",
    "original": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/original_171351417220240419120932Y9XZtqil2T.jpg",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb_171351417220240419120932Y9XZtqil2T.jpg",
    "password": null,
    "note": null,
    "order": 285492,
    "type": "Photos",
    "temp_image": 0,
    "not_uploaded_on_s3": 0,
    "status": 1,
    "media_changed": null,
    "temp_id": null,
    "created_by_id": 1,
    "updated_by_id": null,
    "created_at": "2024-04-19T08:09:33.000000Z",
    "updated_at": "2024-04-19T08:09:33.000000Z",
    "deleted_at": null,
    "deleted_by_id": null
    },
    {
    "id": 359214,
    "property_id": 13683,
    "title": null,
    "description": null,
    "path": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/171351417320240419120933NqEWadilMJ.jpg",
    "original": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/original_171351417320240419120933NqEWadilMJ.jpg",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb_171351417320240419120933NqEWadilMJ.jpg",
    "password": null,
    "note": null,
    "order": 285493,
    "type": "Photos",
    "temp_image": 0,
    "not_uploaded_on_s3": 0,
    "status": 1,
    "media_changed": null,
    "temp_id": null,
    "created_by_id": 1,
    "updated_by_id": null,
    "created_at": "2024-04-19T08:09:36.000000Z",
    "updated_at": "2024-04-19T08:09:36.000000Z",
    "deleted_at": null,
    "deleted_by_id": null
    },
    {
    "id": 359215,
    "property_id": 13683,
    "title": null,
    "description": null,
    "path": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/171351417620240419120936XCJ9BlJ9jk.jpg",
    "original": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/original_171351417620240419120936XCJ9BlJ9jk.jpg",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb_171351417620240419120936XCJ9BlJ9jk.jpg",
    "password": null,
    "note": null,
    "order": 285494,
    "type": "Photos",
    "temp_image": 0,
    "not_uploaded_on_s3": 0,
    "status": 1,
    "media_changed": null,
    "temp_id": null,
    "created_by_id": 1,
    "updated_by_id": null,
    "created_at": "2024-04-19T08:09:37.000000Z",
    "updated_at": "2024-04-19T08:09:37.000000Z",
    "deleted_at": null,
    "deleted_by_id": null
    },
    {
    "id": 359216,
    "property_id": 13683,
    "title": null,
    "description": null,
    "path": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/171351422020240419121020MocBp5sKxx.jpg",
    "original": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/original_171351422020240419121020MocBp5sKxx.jpg",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb_171351422020240419121020MocBp5sKxx.jpg",
    "password": null,
    "note": null,
    "order": 285494,
    "type": "Photos",
    "temp_image": 0,
    "not_uploaded_on_s3": 0,
    "status": 1,
    "media_changed": null,
    "temp_id": null,
    "created_by_id": 1,
    "updated_by_id": null,
    "created_at": "2024-04-19T08:10:21.000000Z",
    "updated_at": "2024-04-19T08:10:21.000000Z",
    "deleted_at": null,
    "deleted_by_id": null
    },
    {
    "id": 359217,
    "property_id": 13683,
    "title": null,
    "description": null,
    "path": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/171351422120240419121021IO6QpZgbl6.jpg",
    "original": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/original_171351422120240419121021IO6QpZgbl6.jpg",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb_171351422120240419121021IO6QpZgbl6.jpg",
    "password": null,
    "note": null,
    "order": 285495,
    "type": "Photos",
    "temp_image": 0,
    "not_uploaded_on_s3": 0,
    "status": 1,
    "media_changed": null,
    "temp_id": null,
    "created_by_id": 1,
    "updated_by_id": null,
    "created_at": "2024-04-19T08:10:22.000000Z",
    "updated_at": "2024-04-19T08:10:22.000000Z",
    "deleted_at": null,
    "deleted_by_id": null
    },
    {
    "id": 359218,
    "property_id": 13683,
    "title": null,
    "description": null,
    "path": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/17135142222024041912102262Snre6oCi.jpg",
    "original": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/original_17135142222024041912102262Snre6oCi.jpg",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb_17135142222024041912102262Snre6oCi.jpg",
    "password": null,
    "note": null,
    "order": 285496,
    "type": "Photos",
    "temp_image": 0,
    "not_uploaded_on_s3": 0,
    "status": 1,
    "media_changed": null,
    "temp_id": null,
    "created_by_id": 1,
    "updated_by_id": null,
    "created_at": "2024-04-19T08:10:23.000000Z",
    "updated_at": "2024-04-19T08:10:23.000000Z",
    "deleted_at": null,
    "deleted_by_id": null
    },
    {
    "id": 359219,
    "property_id": 13683,
    "title": null,
    "description": null,
    "path": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/171351422320240419121023fHrjIkbciN.jpg",
    "original": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/original_171351422320240419121023fHrjIkbciN.jpg",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb_171351422320240419121023fHrjIkbciN.jpg",
    "password": null,
    "note": null,
    "order": 285497,
    "type": "Photos",
    "temp_image": 0,
    "not_uploaded_on_s3": 0,
    "status": 1,
    "media_changed": null,
    "temp_id": null,
    "created_by_id": 1,
    "updated_by_id": null,
    "created_at": "2024-04-19T08:10:23.000000Z",
    "updated_at": "2024-04-19T08:10:23.000000Z",
    "deleted_at": null,
    "deleted_by_id": null
    },
    {
    "id": 359220,
    "property_id": 13683,
    "title": null,
    "description": null,
    "path": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/171351422420240419121024TlGsOBIuTt.jpg",
    "original": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/original_171351422420240419121024TlGsOBIuTt.jpg",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb_171351422420240419121024TlGsOBIuTt.jpg",
    "password": null,
    "note": null,
    "order": 285498,
    "type": "Photos",
    "temp_image": 0,
    "not_uploaded_on_s3": 0,
    "status": 1,
    "media_changed": null,
    "temp_id": null,
    "created_by_id": 1,
    "updated_by_id": null,
    "created_at": "2024-04-19T08:10:26.000000Z",
    "updated_at": "2024-04-19T08:10:26.000000Z",
    "deleted_at": null,
    "deleted_by_id": null
    },
    {
    "id": 359221,
    "property_id": 13683,
    "title": null,
    "description": null,
    "path": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/171351422620240419121026yoU6SnJsQp.jpg",
    "original": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/original_171351422620240419121026yoU6SnJsQp.jpg",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb_171351422620240419121026yoU6SnJsQp.jpg",
    "password": null,
    "note": null,
    "order": 285499,
    "type": "Photos",
    "temp_image": 0,
    "not_uploaded_on_s3": 0,
    "status": 1,
    "media_changed": null,
    "temp_id": null,
    "created_by_id": 1,
    "updated_by_id": null,
    "created_at": "2024-04-19T08:10:26.000000Z",
    "updated_at": "2024-04-19T08:10:26.000000Z",
    "deleted_at": null,
    "deleted_by_id": null
    },
    {
    "id": 362035,
    "property_id": 13683,
    "title": null,
    "description": null,
    "path": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/171377761820240422132018l0bLGYihn0.jpg",
    "original": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/original_171377761820240422132018l0bLGYihn0.jpg",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb_171377761820240422132018l0bLGYihn0.jpg",
    "password": null,
    "note": null,
    "order": 287718,
    "type": "Photos",
    "temp_image": 0,
    "not_uploaded_on_s3": 0,
    "status": 1,
    "media_changed": null,
    "temp_id": null,
    "created_by_id": 262,
    "updated_by_id": null,
    "created_at": "2024-04-22T09:20:20.000000Z",
    "updated_at": "2024-04-22T09:20:20.000000Z",
    "deleted_at": null,
    "deleted_by_id": null
    },
    {
    "id": 362036,
    "property_id": 13683,
    "title": null,
    "description": null,
    "path": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/1713777620202404221320204VLIZs1Jjm.jpg",
    "original": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/original_1713777620202404221320204VLIZs1Jjm.jpg",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb_1713777620202404221320204VLIZs1Jjm.jpg",
    "password": null,
    "note": null,
    "order": 287719,
    "type": "Photos",
    "temp_image": 0,
    "not_uploaded_on_s3": 0,
    "status": 1,
    "media_changed": null,
    "temp_id": null,
    "created_by_id": 262,
    "updated_by_id": null,
    "created_at": "2024-04-22T09:20:21.000000Z",
    "updated_at": "2024-04-22T09:20:21.000000Z",
    "deleted_at": null,
    "deleted_by_id": null
    }
    ],
    "community_property": {
    "id": 3369,
    "region_id": 1,
    "community_id": 112,
    "sub_community_id": 1947,
    "name": "Nad Al Sheba Gardens - Phase 2",
    "alternate_name": null,
    "slug": "nad-al-sheba-gardens-phase-2",
    "description": null,
    "path_name": "Nad Al Sheba Gardens - Phase 2, Nad Al Sheba 1, Nadd Al Sheba, Dubai",
    "alternate_path_name": null,
    "latitude": "25.13498511382361",
    "longitude": "55.3082090153433",
    "created_by_id": 146,
    "updated_by_id": null,
    "default_use": 1,
    "alternate_use": 1,
    "status": 1,
    "created_at": "2023-11-22T07:38:35.000000Z",
    "updated_at": "2023-11-22T07:38:35.000000Z",
    "deleted_at": null
    }
    },
    "page": 1,
    "per_page": 10
    }
     */
    public function getProperties(Request $request)
    {
        if (empty($request->agent_id)) {
            return $this->sendResponse(400, 'Agent  Id is required', [], 400);
        }

        $notAdmin = auth()->user()->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }
        $data = Property::when($request->for, function ($q) {
            $this->isArray(\request("for")) ? $q->whereIn("for", \request("for")) : $q->where("for", \request("for"));
        })->when($request->agent_id, function ($q) {
            $q->where("agent_id", \request("agent_id"));
        })->when($request->owner_id, function ($q) use ($notAdmin, $childrenIds) {
            $q->where("owner_id", \request("owner_id"))->when($notAdmin, function ($q) use ($notAdmin, $childrenIds) {
                $q->whereIn('agent_id', $childrenIds);
            });
        })->with(["bed", "propertyCategory", "community", "subCommunity", "portals.portal", "price", "agent", "bed", "photos"])->get();

        return $this->sendResponse(200, 'Data found', $data, 200);
    }
/**
 *
 *
 *
 * Get Property Criteria
 * @group Mobile Leads
 * @authenticated
 * @header Accept application/json
 * @bodyParam id int required for searching.

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
"status": 200,
"code": 200,
"message": "Data found",
"data": {
"community": "Dubai Marina",
"community_id": 82,
"subCommunity": "Princess Tower",
"subCommunity_id": 1054,
"communityProperty": null,
"price": "3700000",
"minPrice": 3330000,
"maxPrice": 4070000,
"for": "Residential for sale",
"bed": "3 beds",
"bed_id": 4,
"bath": "4 baths",
"bath_id": 4,
"furnished": "FURNISHED",
"view": "[\"Dubai Eye View\",\"Palm Jumeirah View\",\"Sea View\",\"Skyline View\",\"Sunrise View\",\"Sunset View\",\"Waterfront View\"]"
},
"page": 1,
"per_page": 10
}
 */

    public function propertyCriteria(Request $request)
    {
        if ($request->id) {
            $property = Property::find($request->id);
            if ($property) {
                $data = getPropertyCriteria($property->ref_no);
                return $this->sendResponse(200, 'Data found', $data, 200);

            }
        }

    }

    /**
     *
     *
     *
     * Get Communities
     * @group Mobile Leads
     * @authenticated
     * @header Accept application/json
    Example: abc
     *
     *
     *
     * @header Accept application/json
     * @bodyParam term string optional for searching. Example: jumeriah

     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
    {
    "status": 200,
    "code": 200,
    "message": "Communities Found",
    "data": [
    {
    "id": 1,
    "name": "Al Aweer",
    "path_name": "Al Aweer, Dubai",
    "alternate_path_name": null
    },
    {
    "id": 2,
    "name": "Zabeel",
    "path_name": "Zabeel, Dubai",
    "alternate_path_name": null
    },
    {
    "id": 3,
    "name": "Reem",
    "path_name": "Reem, Dubai",
    "alternate_path_name": null
    },
    {
    "id": 4,
    "name": "wasl gate",
    "path_name": "wasl gate, Dubai",
    "alternate_path_name": null
    },
    {
    "id": 5,
    "name": "Dragon City",
    "path_name": "Dragon City, Dubai",
    "alternate_path_name": null
    },
    {
    "id": 6,
    "name": "Dubai Outsource Zone",
    "path_name": "Dubai Outsource Zone, Dubai",
    "alternate_path_name": null
    },
    {
    "id": 7,
    "name": "Dubai Studio City",
    "path_name": "Dubai Studio City, Dubai",
    "alternate_path_name": null
    },
    {
    "id": 8,
    "name": "The Hills",
    "path_name": "The Hills, Dubai",
    "alternate_path_name": null
    },
    {
    "id": 9,
    "name": "Arabian Ranches 3",
    "path_name": "Arabian Ranches 3, Dubai",
    "alternate_path_name": null
    },
    {
    "id": 10,
    "name": "Mohammed Bin Rashid City",
    "path_name": "Mohammed Bin Rashid City, Dubai",
    "alternate_path_name": null
    }
    ],
    "page": 1,
    "per_page": 10
    }
     */
    public function getCommunities(Request $request)
    {
        $term = isset($request->term) ? $request->term : '';
        $communities = Community::where(\DB::raw('lower(name)'), 'like', '%' . strtolower($term) . '%')
            ->orWhere(function ($q) use ($term) {
                $q->orWhere(\DB::raw('lower(path_name)'), 'like', '%' . strtolower($term) . '%');
            })->limit('30')->select('id', 'name', 'path_name', 'alternate_path_name')->get();

        return $this->sendResponse(200, 'Communities Found', $communities, 200);

    }

    /**
     *
     *
     *
     * Get subCommunities and Community Properties
     * @group Mobile Leads
     * @authenticated
     * @header Accept application/json
    Example: abc
     *
     *
     *
     * @header Accept application/json
     * @bodyParam communities array optional for searching. Example: jumeriah
     * @bodyParam term string optional for searching. Example: jumeriah

     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
    {
    "status": 200,
    "code": 200,
    "message": "Sub Communities Found",
    "data": [
    {
    "text": "Safeer Tower, Dubai Marina, Dubai",
    "id": 987,
    "lat": "25.078615",
    "long": "55.141695",
    "path": "Safeer Tower, Dubai Marina, Dubai",
    "type": "sub_community"
    }
    ],
    "page": 1,
    "per_page": 10
    }
     */
    public function getSubCommunities(Request $request)
    {
        $q = $request->term;
        $term = '';
        if (!empty($q)) {
            $term = $request->term ?? '';
        }
        $communities = isset($request->communities) ? $request->communities : [];

        $subCommunities = SubCommunity::where(function ($q) use ($term) {
            $q->where(\DB::raw('lower(name)'), 'like', '%' . strtolower($term) . '%')
                ->orWhere(\DB::raw('lower(path_name)'), 'like', '%' . strtolower($term) . '%');
        })->where(function ($q) use ($communities) {
            if (count($communities) > 0) {
                $q->orWhereIn('community_id', $communities);
            } else {
                return false;
            }
        })->with("communityProperties")->limit('40')->get();
        $properties = CommunityProperty::where(function ($q) use ($term) {
            $q->orWhere(\DB::raw('lower(path_name)'), 'like', '%' . strtolower($term) . '%')
                ->where(\DB::raw('lower(name)'), 'like', '%' . strtolower($term) . '%');
        })->where(function ($q) use ($communities) {
            if (count($communities) > 0) {
                $q->orWhereIn('community_id', $communities);
            } else {
                return false;
            }
        })->limit('40')->get();
        $data = [];
        $i = 0;
        foreach ($subCommunities as $subc) {

            $data[$i]['text'] = $subc->path_name ?? $subc->name;
            $data[$i]['id'] = $subc->id;
            $data[$i]['lat'] = $subc->latitude;
            $data[$i]['long'] = $subc->longitude;
            $data[$i]['path'] = $subc->path_name;
            $data[$i]['type'] = 'sub_community';
            $i++;

        }
        $data2 = [];
        $i = 0;
        if (isset($properties)) {
            foreach ($properties as $property) {
                $data2[$i]['text'] = $property->path_name ?? $property->name;
                $data2[$i]['id'] = $property->id;
                $data2[$i]['lat'] = $property->latitude;
                $data2[$i]['long'] = $property->longitude;
                $data2[$i]['path'] = $property->path_name;
                $data2[$i]['type'] = 'community_property';
                $i++;
            }
        }

        $datas = array_merge($data, $data2);
        if (!empty($datas)) {
            return $this->sendResponse(200, 'Sub Communities Found', $datas, 200);

            return response([
                "data" => $datas,
            ]);
        } else {
            return $this->sendResponse(200, 'Sub Communities Found', [], 200);
            return response([
                "data" => [],
            ]);
        }

    }

/**
 *
 *
 *
 * Add Note
 * @group Mobile Leads
 * @authenticated
 * @header Accept application/json
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
"status": 200,
"code": 200,
"message": "Lead Note Added",
"data": {
"lead_id": "216037",
"note": "New Lead Note Added",
"created_by_id": 172,
"updated_at": "2024-06-06T09:47:46.000000Z",
"created_at": "2024-06-06T09:47:46.000000Z",
"id": 292176
},
"page": 1,
"per_page": 10
}
 */
    public function addNote(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'note' => 'required|string',
        ]);

        try {
            $obj = new LeadNote();
            $obj->lead_id = $request->lead_id;
            $obj->note = $request->note;
            $obj->created_by_id = auth()->id();
            $obj->save();
            $result = array('status' => '1', 'msg' => "Lead note added successfully.");
            return $this->sendResponse(200, 'Lead Note Added', $obj, 200);

        } catch (\Exception $exception) {
            return $this->sendResponse(500, 'Something went wrong!!', $exception->getMessage(), 500);

        }
    }

    /**
     *
     *
     *
     * Assign Lead to Myself
     * @group Mobile Leads
     * @authenticated
     * @header Accept application/json
     * @bodyParam id integer for lead id. Example: 12
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
    "status": true,
    "code": 200,
    "message": "Lead assigned successfully",
    "data": "Success",
    "page": 1,
    "per_page": 10
    }
     * @response 404 scenario="Lead already assigned or Lead not found"
    {
    "status": true,
    "code": 404,
    "message": "Lead already assigned or Lead not found",
    "data": "Success",
    "page": 1,
    "per_page": 10
    }

     * @response 403 scenario="You are not authorized"
    {
    "status": false,
    "code": 403,
    "message": "You are not authorized to assign leads to yourself ID missmatched.",
    "data": "Aborted",
    "page": 1,
    "per_page": 10
    }
     * @response 403 scenario="Limit Exceeded"
    {
    "status": false,
    "code": 403,
    "message": "You can not assign more than 15 leads in a day. Total leads assigned today: 15",
    "data": "Aborted",
    "page": 1,
    "per_page": 10
    }
     */
    public function assignLeadsToMyself(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'agent_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(false, 'Validation Error', $validator->errors(), 422);
        }

        $assigned_leads = Lead::where('assigned_by_id', auth()->user()->id)->whereDate('assigned_at', Carbon::today())->count();

        if (!auth()->user()->hasRole('Agent')) {
            return $this->sendResponse(false, 'You are not authorized to assign leads to yourself.', 'Aborted', 403);
        }

        if (auth()->user()->id != $request->agent_id) {
            return $this->sendResponse(false, 'You are not authorized to assign leads to yourself ID missmatched.', 'Aborted', 403);
        }
        $id = $request->id;
        if ($assigned_leads < 15) {
            $lead = Lead::where('id', $id)->whereNull('agent_id')->first();
            if ($lead) {
                $lead->agent_id = auth()->user()->id;
                $lead->lead_status_id = 1;
                $lead->assigned_by_id = auth()->user()->id;
                $lead->assigned_at = now();
                if ($lead->portal_lead){
                    $lead->portal_lead = false;
                    $lead->taken_from_portal_lead = true;
                    $lead->transfer_to_portal_lead_time = Carbon::now()->addHours(24);
                    $lead->taken_from_portal_lead_date = null;
                }
                $lead->save();

                $leadnote = new LeadNote();
                $leadnote->lead_id = $lead->id;
                $leadnote->status = 1;
                $leadnote->note = 'This lead has been claimed from the Lead Pool.';
                $leadnote->created_by_id = auth()->user()->id;
                $leadnote->save();

                activity()
                    ->performedOn($lead)
                    ->causedBy(auth()->user())
                    ->withProperties([])
                    ->event('updated')
                    ->log(auth()->user()->full_name . ' has added himself as Agent ');
                return $this->sendResponse(true, 'Lead assigned successfully', 'Success', 200);

            }
            return $this->sendResponse(true, 'Lead already assigned or Lead not found', 'Success', 404);

        } else {
            return $this->sendResponse(false, 'You can not assign more than 15 leads in a day. Total leads assigned today: ' . $assigned_leads . '.', 'Aborted', 403);
            return json_encode($result);
        }
        return json_encode($result);
    }

    /**
     *
     *
     *
     * Assign Bulk Leads to Myself
     * @group Mobile Leads
     * @authenticated
     * @header Accept application/json
     * @bodyParam ids array for lead ids will accept array of ids. Example: 12
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
    "status": true,
    "code": 200,
    "message": "2 Leads assigned successfully",
    "data": "Success",
    "page": 1,
    "per_page": 10
    }
     * @response 404 scenario="Lead already assigned or Lead not found"
    {
    "status": true,
    "code": 404,
    "message": "Lead already assigned or Lead not found",
    "data": "Success",
    "page": 1,
    "per_page": 10
    }

     * @response 403 scenario="You are not authorized"
    {
    "status": false,
    "code": 403,
    "message": "You are not authorized to assign leads to yourself ID missmatched.",
    "data": "Aborted",
    "page": 1,
    "per_page": 10
    }
     * @response 403 scenario="Limit Exceeded"
    {
    "status": false,
    "code": 403,
    "message": "You can not assign more than 15 leads in a day. Total leads assigned today: 15",
    "data": "Aborted",
    "page": 1,
    "per_page": 10
    }
     */
    public function bulkAssignLeadsToMyself(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'ids' => 'required',
            'agent_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(false, 'Validation Error', $validator->errors(), 422);
        }
        $assigned_leads = Lead::where('assigned_by_id', auth()->user()->id)->whereDate('assigned_at', Carbon::today())->count();

        if (!auth()->user()->hasRole('Agent')) {
            return $this->sendResponse(false, 'You are not authorized to assign leads to yourself.', 'Aborted', 403);
        }
        if (auth()->user()->id != $request->agent_id) {
            return $this->sendResponse(false, 'You are not authorized to assign leads to yourself ID missmatched.', 'Aborted', 403);
        }
        if (gettype($request->ids) == 'array' && count($request->ids) > 0) {
            $ids = $request->ids;
            $counter = 0;
            foreach ($ids as $id) {
                $assigned_leads = Lead::where('assigned_by_id', auth()->user()->id)->whereDate('assigned_at', Carbon::today())->count();
                if ($assigned_leads < 15) {

                    $lead = Lead::where('id', $id)->whereNull('agent_id')->first();
                    if ($lead) {
                        $lead->agent_id = auth()->user()->id;
                        $lead->lead_status_id = 1;
                        $lead->assigned_by_id = auth()->user()->id;
                        $lead->assigned_at = now();
                        $lead->save();
                        $counter++;

                        $leadnote = new LeadNote();
                        $leadnote->lead_id = $lead->id;
                        $leadnote->status = 1;
                        $leadnote->note = 'This lead has been claimed from the Lead Pool.';
                        $leadnote->created_by_id = auth()->user()->id;
                        $leadnote->save();

                        activity()
                            ->performedOn($lead)
                            ->causedBy(auth()->user())
                            ->withProperties([])
                            ->event('updated')
                            ->log(auth()->user()->full_name . ' has added himself as Agent ');
                    }

                } else {

                    return $this->sendResponse(false, 'You can not assign more than 15 leads in a day. Total leads assigned today: ' . $assigned_leads . '.', 'Aborted', 403);
                }

            }

            if ($counter > 0) {
                if ($request->telegram_notification == 'true') {
                    $message = "\n<b> AX Secondary CRM</b>\n\n<b>" . $counter . " Lead(s)</b> have been transferred to you by " . auth()->user()->first_name . ' ' . auth()->user()->last_name . '.' . "\n\n";
                    $environment = \App::environment();
                    if ($environment == "production") {
                        $this->sendTelegramMessageHtml($message, [auth()->user()->telegram_chatid], null, null, 'lead_bot');
                    }
                }
                return $this->sendResponse(true, $counter . ' Leads assigned successfully', 'Success', 200);
            } else {
                return $this->sendResponse(false, 'No leads assigned', 'Aborted', 403);
            }

        }

    }

    /**
     *
     *
     *
     * Lead Notes
     * @group Mobile Leads
     * @authenticated
     * @header Accept application/json
     * @bodyParam id requried for lead id. Example: 12
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
    "lead_id": 269096,
    "note": "Note of the lead",
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
            'id' => 'required',
            'agent_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(false, 'Validation Error', $validator->errors(), 422);
        }

        $lead = Lead::where('id', $request->id)->first();

        if (!$lead) {
            return $this->sendResponse(404, 'Lead not found', [], 404);
        }

        // if($lead->agent_id != $request->agent_id){
        //     return $this->sendResponse(403, 'You are not authorized', 'Aborted', 403);
        // }

        $leadNotes = LeadNote::with('createdBy')->where('lead_id', $request->id)->orderBy('id', 'desc')->get();

        // create resource
        $notes = LeadNoteResource::collection($leadNotes);

        return $this->sendResponse(200, 'Data found', $notes, 200);

    }

    /**
     *
     *
     *
     * Convert Lead to Listing
     * @group Mobile Leads
     * @authenticated
     * @header Accept application/json
     * @bodyParam lead_id requried for lead id. Example: 12
     * @bodyParam first_name requried for first name. Example: John
     * @bodyParam last_name requried for last name. Example: Doe
     * @bodyParam mobile requried for mobile. Example: 123456789
     * @bodyParam for requried for for. Example: Residential for sale
     * @bodyParam unit_no requried for unit_no. Example: 123
     * @bodyParam property_category_id requried for property_category_id. Example: 1
     * @bodyParam listing_location_type optional for listing_location_type. Example: community_property
     * @bodyParam listing_location_id optional for listing_location_id. Example: 1
     * @bodyParam email optional for email. Example:
     * @bodyParam agent_id optional for agent_id. Example: 1
     * @bodyParam bed_id optional for bed_id. Example: 1
     * @bodyParam bath_id optional for bath_id. Example: 1
     * @bodyParam listing_source_id optional for listing_source_id. Example: 1
     * @bodyParam listing_medium_id requried for listing_medium_id. Example: 1
     *
     *
    Example: abc
     *
     *
     *
     * @header Accept application/json

     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Lead to Listing converted successfully."
    {
    "status": 200,
    "code": 200,
    "message": "Lead to Listing converted successfully.",
    "data": 19805,
    "page": 1,
    "per_page": 10
    }

     * @response 400 scenario="Duplicate Listing Found"
    {
    "status": 400,
    "code": 400,
    "message": "Duplicate Lead Found with ID 19804",
    "data": 19804,
    "page": 1,
    "per_page": 10
    }

     */

    public function leadToListing(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'lead_id' => 'required',
            'mobile' => 'required',
            'for' => 'required',
            'unit_no' => 'required',
            'property_category_id' => 'required',
            'listing_location_type' => 'nullable',
            'listing_location_id' => 'nullable',
            'listing_source_id' => 'nullable',
            'listing_medium_id' => 'required',
            'listing_type_id' => 'nullable',

            'email' => 'nullable',
            'agent_id' => 'nullable',
            'bed_id' => 'nullable',
            'bath_id' => 'nullable',
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(false, 'Validation Error', $validator->errors(), 422);
        }

        $property = Property::where('unit_no', $request->unit_no)->where('bath_id', $request->bath_id)
            ->where('bed_id', $request->bed_id)->where('property_category_id', $request->property_category_id)
            ->where($request->input('listing_location_type') . "_id", $request->listing_location_id)
            ->where('for', $request->for)
            ->first();

        if ($property) {
            $result = array('status' => '3', 'msg' => 'Duplicate Listing Found with ref no ' . $property->ref_no, 'id' => $property->id);
            return $this->sendResponse(200, 'Duplicate Listing Found with ref no ' . $property->ref_no, $property->id, 200);

            return json_encode($result);
        }

        $listingLead = ListingLead::where('unit_no', $request->unit_no)->where('bath_id', $request->bath_id)
            ->where('bed_id', $request->bed_id)->where('property_category_id', $request->property_category_id)
            ->where($request->input('listing_location_type') . "_id", $request->listing_location_id)
            ->whereJsonContains('for', $request->for)
            ->first();

        if ($listingLead) {
            $result = array('status' => '3', 'msg' => 'Duplicate Lead Found with ID ' . $listingLead->id, 'id' => $listingLead->id);
            return $this->sendResponse(400, 'Duplicate Lead Found with ID ' . $listingLead->id, $listingLead->id, 400);
        }

        if ($request->agent_id && auth()->id() == 174) {
            $result = array('status' => '0', 'msg' => "Lead cannot be assigned");
            return $this->sendResponse(400, 'Lead cannot be assigned', '', 400);

            return json_encode($result);
        }
        try {
            // dd($request->all());
            $lead = Lead::find($request->lead_id);
            $leadCriteria = LeadCriteria::where('lead_id', $request->lead_id)->first(); // this needs to be saved in message
            DB::beginTransaction();
            $listing = new ListingLead();
            $listing->first_name = $request->first_name;
            $listing->last_name = $request->last_name;

            $listing->email = $request->email;
            $listing->mobile = validatePhoneNumber($request->mobile);
            $listing->unit_no = $request->unit_no;
            $listing->source_id = $request->listing_source_id ?? null;
            $listing->medium_id = $request->listing_medium_id;
            $listing->type_id = $request->listing_type_id;
            $listing->telesales_id = $request->listing_telesales_id;
            // $listing->title = $request->title;
            $listing->status_id = 1;

            $listing->for = $request->for;
            if ($request->agent_id) {
                $listing->agent_id = $request->agent_id;
                $listing->status_id = 2;
            }

            $listing->property_category_id = $request->property_category_id;
            $listing->bed_id = $request->bed_id;
            $listing->bath_id = $request->bath_id;

            $listing->note = $leadCriteria?->bitrix_data ?? null;

            if ($request->listing_location_type == "community_property") {
                $communityProperty = CommunityProperty::find($request->listing_location_id);
                $community_property_id = $communityProperty->id ?? null;
                $sub_community_id = $communityProperty->sub_community_id ?? null;
                $community_id = $communityProperty->community_id ?? null;
            } else {

                $subCommunity = SubCommunity::find($request->listing_location_id);
                $community_property_id = null;
                $sub_community_id = $subCommunity->id ?? null;
                $community_id = $subCommunity->community_id ?? null;
            }

            $listing->sub_community_id = $sub_community_id;
            $listing->community_id = $community_id;
            $listing->community_property_id = $community_property_id;
            $listing->created_by_id = auth()->id();
            $listing->created_at = now();
            $listing->save();

            if ($request->note) {
                $obj = new LeadNote();
                $obj->lead_id = $lead->id;
                $obj->note = $request->note;
                $obj->created_by_id = auth()->id();
                $obj->save();
            }

            $leadNote = LeadNote::where('lead_id', $request->lead_id)->get(); // this needs to be saved in notes
            foreach ($leadNote as $note) {
                $obj = new ListingLeadNote();
                $obj->listing_lead_id = $listing->id;
                $obj->note = 'Note (Migrated) => ' . $note?->note;
                $obj->save();
            }

            if ($lead) {
                $lead->type_id = $request->listing_type_id;
                $lead->converted_to_listing_lead = $listing->id;
                $lead->updated_by_id = auth()->id();
                $lead->status = 0;
                $lead->save();
            }

            DB::commit();

            $environment = \App::environment();
            if (auth()->user()->hasRole(['Telesales', 'Telesales Head', "Super Admin"])) {

                if ($environment == "production") {
                    $message = "<b>AX Secondary CRM</b>\nJust received a new <b>Listing Lead</b> via Telesales Agent " . auth()->user()->full_name . ". Kindly assign it to an agent.\n";
                    $message .= "\n<b> Property Details: </b> ";
                    $message .= "\n<b> ID: </b> " . $listing?->id;
                    $message .= "\n<b> Type: </b> " . $listing?->propertyCategory?->title;
                    $message .= "\n<b> Location: </b> " . $listing?->location;
                    $message .= "\n<b> Beds: </b> " . $listing?->bed?->name;
                    $message .= "\n\n<b> Owner Details: </b> ";
                    ($listing->first_name != " " ? $message .= "\n<b> Name: </b> " . $listing?->first_name . ' ' . $listing?->last_name : null);
                    ($listing->mobile ? $message .= "\n<b> Mobile: </b> " . $listing?->mobile : null);
                    ($listing->email ? $message .= "\n<b> Email: </b> " . $listing?->email : null);

                    $usersWithCommunity = UserLocation::where('parentable_type', 'App\Models\Community')
                        ->where('parentable_id', $listing->community_id)
                        ->pluck('user_id');
                    if ($usersWithCommunity) {
                        $agents = User::whereHas("roles", function ($q) {
                            $q->whereIn("name", ["Line Manager", "Team Leader", "Sales Director"]);
                        })
                            ->where('region_id', '<>', 2)
                            ->when($listing->community_id, function ($q) use ($usersWithCommunity) {
                                $q->whereIn('id', $usersWithCommunity);
                            })
                            ->where("status", true)->get();
                        foreach ($agents as $agent) {
                            if ($agent->telegram_chatid != null) {
                                $url = route('api.thirdparty.listingLeadAssignment', [Crypt::encryptString($listing->id), Crypt::encryptString($agent->id)]);

                                $this->sendTelegramMessageHtml($message, [$agent->telegram_chatid], $url, 'Assign Lead', 'lead_bot');
                            }
                        }
                    }
                }

            }

            // if ($environment == "local" && auth()->id() != 174) {
            //     $message = "<b>AX Secondary CRM</b>\n\n<b>New Listing Lead:</b>\nA new listing lead has been received. Please assign it to an agent.";
            //     $agents = User::whereHas("roles", function ($q) {
            //         $q->whereIn("name", ["Line Manager", "Team Leader"]);
            //     })->where("status", true)->get();
            //     foreach ($agents as $agent) {
            //         if ($agent->telegram_chatid != null) {
            //             $this->sendTelegramMessageHtml($message, [$agent->telegram_chatid], null, null, 'lead_bot');
            //         }
            //     }
            // }

            $result = array('status' => '1', 'msg' => "Lead to Listing converted successfully.", 'id' => $listing->id);
            return $this->sendResponse(true, 'Lead to Listing converted successfully.', $listing->id, 200);

        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }

    }

    /**
     *
     *
     *
     * Call Log of leads
     * @group Mobile Leads
     * @authenticated
     * @header Accept application/json
     * @bodyParam lead_id requried for lead id. Example: 12

     *
     *
    Example: abc
     *
     *
     *
     * @header Accept application/json

     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Lead to Listing converted successfully."
    {
    "status": 200,
    "code": 200,
    "message": "Call log found.",
    "data": [
    {
    "id": 681122,
    "lead_id": null,
    "lead_follow_up_id": null,
    "contact_id": null,
    "contact_follow_up_id": null,
    "client_number": "+4971199585005",
    "call_type": "OUTGOING",
    "internal_user_id": null,
    "user_id": 268,
    "call_started_at": "2024-09-11 09:57:33",
    "call_answered_at": "2024-09-11 09:57:34",
    "call_ended_at": "2024-09-11 09:57:36",
    "call_duration": "3",
    "call_status": null,
    "call_sid": null,
    "call_recording_url": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/calls/17260342632024091105574358538.m4a",
    "call_recording_sid": null,
    "json_dump": "{\"id\": \"389282\", \"user_id\": \"183\", \"call_type\": \"1\", \"entity_id\": \"1\", \"created_at\": \"2024-09-11 05:57:44+0000\", \"updated_at\": \"2024-09-11 05:57:44+0000\", \"user_email\": \"l.amurvelashvili@axcapital.ae\", \"call_status\": \"Completed\", \"caller_name\": \"Unknown\", \"call_duration\": \"3\", \"call_ended_at\": \"2024-09-11 05:57:36+00\", \"client_number\": \"+4971199585005\", \"call_recording\": \"https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/calls/17260342632024091105574358538.m4a\", \"call_started_at\": \"2024-09-11 05:57:33+00\", \"call_answered_at\": \"2024-09-11 05:57:34+00\", \"duration_mismatch\": \"0\", \"formatted_call_duration\": \"00:00:03\"}",
    "created_at": "2024-09-11T05:57:48.000000Z",
    "updated_at": "2024-09-11T05:57:48.000000Z",
    "deleted_at": null,
    "need_to_be_destroyed": 0,
    "leads": [
    {
    "id": 291591,
    "bitrix_id": null,
    "my_crm_id": null,
    "title": "New  - +4971199585005 - ",
    "property_id": null,
    "mail_number": 328873,
    "first_name": "New",
    "last_name": null,
    "email": null,
    "phone": null,
    "mobile": "+4971199585005",
    "whatsapp": "+4971199585005",
    "address": null,
    "company": null,
    "dob": null,
    "gender": null,
    "priority": null,
    "nationality_id": null,
    "country_id": null,
    "state_id": null,
    "city_id": null,
    "lead_status_id": 5,
    "lead_sub_status_id": null,
    "lead_type_id": null,
    "lead_source_id": null,
    "source_id": 13,
    "type_id": null,
    "lead_medium_id": 2,
    "agent_id": 268,
    "previous_agent_id": null,
    "telesales_id": null,
    "telesales_head_id": null,
    "lead_assignment_date": null,
    "follow_up_date": null,
    "viewing_date": null,
    "created_by_id": null,
    "updated_by_id": null,
    "for": null,
    "status": 1,
    "region_id": 1,
    "warning_sent": 0,
    "assigned_agent_at": "2024-09-11 00:50:08",
    "converted_to_listing_lead": null,
    "is_archive": 0,
    "subject": "You missed a call",
    "message": null,
    "note": null,
    "reason": null,
    "pf_tracking_link": null,
    "need_to_be_destroyed": 0,
    "deleted_at": null,
    "created_at": "2024-09-10T20:50:08.000000Z",
    "updated_at": "2024-09-11T07:25:15.000000Z",
    "deleted_by_id": null,
    "bitrix_index": 0,
    "utm_source": null,
    "utm_medium": null,
    "utm_campaign": null,
    "utm_term": null,
    "utm_content": null,
    "utm_url": null,
    "assigned_by_id": null,
    "assigned_at": null,
    "pivot": {
    "call_log_id": 681122,
    "lead_id": 291591
    }
    }
    ]
    }
    ],
    "page": 1,
    "per_page": 10
    }

     * @response 404 scenario="Call log not found"
    {
    "status": "0",
    "msg": "Call log not found"
    }

     */

    public function getCallLog(Request $request)
    {
        if (!$request?->lead_id) {
            return $this->sendResponse(false, 'Lead id is required.', null, 403);
        }
        $log = CallLog::with('leads')
            ->whereHas('leads', function ($q) use ($request) {
                $q->where('lead_id', $request->lead_id);
            })->get();
        if (count($log) > 0) {
            return $this->sendResponse(true, 'Call log found.', $log, 200);
        } else {
            return $this->sendResponse(false, 'Call log not found', null, 404);
        }
    }

    /**
     *
     *
     *
     * Events of Lead
     * @group Mobile Leads
     * @authenticated
     * @header Accept application/json
     * @bodyParam column requried for which coloumn. Example: lead_id
     * @bodyParam value requried for value. Example: 1
     * @bodyParam type optional for type. Example: name from events type table

     *
     *
    Example: abc
     *
     *
     *
     * @header Accept application/json

     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Event log found."

    {
    "status": 200,
    "code": 200,
    "message": "Event log found.",
    "data": [
    {
    "id": 17426,
    "event_type_id": 2,
    "google_calendar_event_id": null,
    "start_time": "2024-09-16 14:10:00",
    "end_time": "2024-09-16 14:40:00",
    "priority": "Medium",
    "event_status": null,
    "title": "Meeting arranged with contact Naji Alsalm and lead",
    "location": "das",
    "description": "dasdasd",
    "invitation": null,
    "reminder_status": 0,
    "send_notification": 1,
    "reminder_to_attendees": 0,
    "reminder_date": null,
    "reminder": null,
    "reminder_frequency": "Minutes",
    "last_reminded": null,
    "recurring": 0,
    "recurring_data": null,
    "agent_id": 1409,
    "contact_id": 19151,
    "lead_id": 291677,
    "property_id": null,
    "created_by_id": 262,
    "updated_by_id": null,
    "status": "PENDING",
    "public": 0,
    "note": null,
    "created_at": "2024-09-16T13:10:35.000000Z",
    "updated_at": "2024-09-16T13:10:35.000000Z",
    "deleted_at": null,
    "listing_lead_id": null,
    "attendees": [],
    "contact": {
    "id": 19151,
    "bitrix_id": null,
    "bitrix_index": 0,
    "my_crm_id": null,
    "title": null,
    "first_name": "Naji",
    "last_name": "Alsalm",
    "agent_id": 1409,
    "previous_agent_id": null,
    "mobile": "+971501288290",
    "email": "najisalem0@gmail.com",
    "whatsapp": "+971501288290",
    "address": "Dubai",
    "gender": "Male",
    "dob": "16-03-1985",
    "follow_up_date": null,
    "country_id": 213,
    "nationality_id": null,
    "passport": null,
    "passport_expiry": null,
    "phone": null,
    "company": null,
    "website": null,
    "status": 1,
    "region_id": 1,
    "contact_type_id": null,
    "contact_source_id": 3,
    "contact_status_id": null,
    "created_by_id": 1409,
    "updated_by_id": null,
    "deleted_at": null,
    "created_at": "2024-09-04T13:17:13.000000Z",
    "updated_at": "2024-09-04T13:17:13.000000Z",
    "deleted_by_id": null
    },
    "lead": {
    "id": 291677,
    "bitrix_id": null,
    "my_crm_id": "9559981",
    "title": "  - 0503881401 - ",
    "property_id": null,
    "mail_number": null,
    "first_name": null,
    "last_name": null,
    "email": null,
    "phone": "0503881401",
    "mobile": "0503881401",
    "whatsapp": "0503881401",
    "address": null,
    "company": null,
    "dob": null,
    "gender": null,
    "priority": null,
    "nationality_id": null,
    "country_id": null,
    "state_id": null,
    "city_id": null,
    "lead_status_id": 5,
    "lead_sub_status_id": null,
    "lead_type_id": 11,
    "lead_source_id": null,
    "source_id": 31,
    "type_id": null,
    "lead_medium_id": 2,
    "agent_id": 1409,
    "previous_agent_id": null,
    "telesales_id": null,
    "telesales_head_id": null,
    "lead_assignment_date": null,
    "follow_up_date": "2024-09-16",
    "viewing_date": null,
    "created_by_id": null,
    "updated_by_id": 262,
    "for": null,
    "status": 1,
    "region_id": 1,
    "warning_sent": 0,
    "assigned_agent_at": "2024-09-11 10:47:05",
    "converted_to_listing_lead": null,
    "is_archive": 0,
    "subject": null,
    "message": null,
    "note": null,
    "reason": null,
    "pf_tracking_link": null,
    "need_to_be_destroyed": 0,
    "deleted_at": null,
    "created_at": "2024-09-11T06:47:05.000000Z",
    "updated_at": "2024-09-16T13:10:35.000000Z",
    "deleted_by_id": null,
    "bitrix_index": 0,
    "utm_source": null,
    "utm_medium": null,
    "utm_campaign": null,
    "utm_term": null,
    "utm_content": null,
    "utm_url": null,
    "assigned_by_id": null,
    "assigned_at": null
    },
    "property": null,
    "agent": {
    "id": 1409,
    "bitrix_role": "Secondary, Azim",
    "entity": "Secondary",
    "group_id": null,
    "parent_id": 129,
    "public_profile_id": null,
    "old_user_id": null,
    "slug": "inoka-diddeniya",
    "title": null,
    "name": "Inoka Diddeniya",
    "first_name": "Inoka",
    "last_name": "Diddeniya",
    "position": "Property Consultant",
    "email": "i.diddeniya@axcapital.ae",
    "secondary_email": null,
    "email_verified_at": null,
    "last_login": "2024-09-11 09:31:43",
    "mobile": "+971581650321",
    "api_access": 0,
    "api_rate_limit": 0,
    "telegram_username": null,
    "telegram_chatid": "5970238984",
    "call_tracking_user_id": null,
    "call_tracking_account_id": null,
    "bitrix_id": 254612,
    "website_link": null,
    "facebook_link": null,
    "instagram_link": null,
    "youtube_link": null,
    "linkedin_link": null,
    "bio": null,
    "license_no": "63149",
    "pp_enabled": 1,
    "pp_name": "Greta Gasparyan",
    "pp_photo": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/172543022220240904101022.jpg",
    "pp_license_no": "69480",
    "country_id": null,
    "state_id": null,
    "nationality_id": null,
    "community_id": null,
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/thumb_1706248833zDM3dIX4Ma20240126100033.JPG",
    "image": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/1706248833zDM3dIX4Ma20240126100033.JPG",
    "avatar": null,
    "color_code": null,
    "status": 1,
    "region_id": 1,
    "traccar_status": 1,
    "device_identifier": "1409",
    "port": "5055",
    "mic_status": 0,
    "device_token": "c3FWWkzZSGiyRxknubZXiU:APA91bFqXhER6qbHJEN0UUDipPFHAf2OBb-C0iDk51ueakTRqYqj1iVNJ1RG-A_Kcx5KUiADRPnUkLkZRG5LlRJI0D3v51nhWfRpWtgLseTgIlWJVIacDo1xHQjw31cqoKtnYj1gZkOi",
    "call_tracking": 1,
    "call_lead": 0,
    "is_commercial": 0,
    "created_by_id": null,
    "updated_by_id": 1409,
    "otp": null,
    "created_at": "2024-01-18T05:19:30.000000Z",
    "updated_at": "2024-09-11T05:31:43.000000Z",
    "deleted_at": null,
    "google2fa_secret": null,
    "telegram2fa_secret": null,
    "back_up_google2fa_secret": "LUXY2XY5HOHPBLSA",
    "login_attempt": 0,
    "joining_date": "2024-01-22 00:00:00",
    "last_deal_date": null,
    "is_report_exempt": 0,
    "zone_id": 6,
    "full_name": "Inoka Diddeniya",
    "thumb_url": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/thumb_1706248833zDM3dIX4Ma20240126100033.JPG",
    "image_url": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/1706248833zDM3dIX4Ma20240126100033.JPG",
    "role_list": [
    "Agent"
    ]
    },
    "type": {
    "id": 2,
    "name": "Meeting",
    "status": 1,
    "icon": "fa fa-handshake-o",
    "badge": "bg-soft-info text-info",
    "thumbnail": null,
    "image": null,
    "order": 2,
    "created_by_id": null,
    "updated_by_id": 263,
    "created_at": "2023-05-25T10:11:33.000000Z",
    "updated_at": "2023-05-25T10:31:17.000000Z",
    "deleted_at": null
    }
    }
    ],
    "page": 1,
    "per_page": 10
    }
     * @response 404 scenario="Event log not found"
    {
    "status": 404,
    "code": 404,
    "message": "Event log not found",
    "data": null,
    "page": 1,
    "per_page": 10
    }

     */

    public function getEventAllLog(Request $request)
    {
        $request->validate([
            'column' => 'required', //ex. contact_id or lead_id or property_id
            'value' => 'required', //ex. 1
            'type' => 'nullable', //ex. name from events table
        ]);
        $type = EventType::where('name', $request->type)->first();
        $log = Event::where($request->column, $request->value)
            ->when($request->type != null, function ($query) use ($type) {
                if ($type != null) {
                    return $query->where('event_type_id', $type->id);
                }
            })->with("attendees", "contact", "lead", "property", "agent", "type")->orderBy('id', 'desc')->limit(10)->get();
        if (count($log) > 0) {
            return $this->sendResponse(true, 'Event log found.', $log, 200);
        } else {
            return $this->sendResponse(false, 'Event log not found', null, 404);

        }
    }

    /**
     *
     *
     *
     * Make Call
     * @group Mobile Leads
     * @authenticated
     * @header Accept application/json
     * @bodyParam lead_id requried for lead. Example: 1
     * @bodyParam dialer_id requried for user. Example: 1
     *


     *
     *
    Example: abc
     *
     *
     *
     * @header Accept application/json

     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Event log found."
    {
    "status": true,
    "code": 200,
    "message": "Call started successfully.",
    "data": null,
    "page": 1,
    "per_page": 10
    }


     * @response 422 scenario="Please update mobile number."
    {
    "status": false,
    "code": 422,
    "message": "Please update mobile number.",
    "data": null,
    "page": 1,
    "per_page": 10
    }


     * @response 200 scenario="Blocked number"
    {
    "status": false,
    "code": 400,
    "message": "Could not start the call, please try again",
    "data": null,
    "page": 1,
    "per_page": 10
    }

     * @response 200 scenario="Blocked number"


    {
    "status": false,
    "code": 200,
    "message": "This number is in Blacklist. Please do not contact with this number.",
    "data": null,
    "page": 1,
    "per_page": 10
    }

     * @response 500 scenario="Something went wrong!!"


    {
    "status": false,
    "code": 500,
    "message": "Something went wrong!!",
    "data": null,
    "page": 1,
    "per_page": 10
    }
     */

    public function startCall(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "lead_id" => "required|exists:leads,id",
            'dailer_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(false, 'Validation Error', $validator->errors(), 422);
        }

        try {

            $lead = Lead::find($request->lead_id);
            if (empty($lead->mobile)) {
                return $this->sendResponse(false, 'Please update mobile number.', null, 422);
            }

            $user = User::where('id', $request->dailer_id)->first();
            $pilot_testers = config('pilot_testing.call');
            if (checkBlocklist($lead->mobile, Lead::class, null)) {
                return $this->sendResponse(false, 'This number is in Blacklist. Please do not contact with this number..', null, 200);
            }
            $full_name = $lead->first_name ?? 'Null' . ' ' . $lead->last_name ?? 'Null';
            $callStared = $this->dialANumber($user->email, $lead->mobile, $full_name); // Uncomment this

            if ($callStared == "error") {
                $callStared = $this->makeCall($user->email, $lead->mobile); // Uncomment this
            }
            if ($callStared == "Make call posted" || $callStared == "success") {
                LeadNote::create([
                    "lead_id" => $lead->id,
                    "note" => "Call started",
                    "created_by_id" => auth()->id(),
                ]);
                return $this->sendResponse(true, 'Call started successfully.', null, 200);
            }

            return $this->sendResponse(false, 'Could not start the call, please try again.', null, 400);

        } catch (\Exception $exception) {
            return $this->sendResponse(false, 'Something went wrong!!', ['error' => $exception->getMessage()], 500);

            // $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
            // return response()->json($result, 400);
        }
    }

    public function getAxLeads(Request $request)
    {
        if (!$request->secret) {
            return $this->sendResponse(false, 'You are not authorized', [], 403);
        }
        if ($request->secret != 'd23f221f38f1d29ca5f8ege1dhbj8662') {
            return $this->sendResponse(false, 'You are not authorized', [], 403);
        }

        $query = Lead::query();

        // Filter by status
        $query->where('status', 1);

        // Filter by agent_id
        if ($request->agent_id) {
            if ($this->isArray("agent_id")) {
                $query->whereIn('agent_id', $request->agent_id);
            } else {
                $query->where('agent_id', $request->agent_id);
            }
        }

        // Filter by archive status
        if ($request->is_archive) {
            $query->where('is_archive', 1);
        } else {
            $query->whereIn('is_archive', [0, 1]);
        }

        // Apply search logic for mobile, email, and general search
        $query->where(function ($q) use ($request) {
            // Mobile Filter: search in mobile, phone, and whatsapp
            if (!empty($request->mobile)) {
                $searchKeyWord = trimSpaces(validatePhoneNumber($request->mobile));
                $q->orWhere(function ($q) use ($searchKeyWord) {
                    $q->whereRaw("REPLACE(`mobile`, ' ', '') LIKE ?", ['%' . $searchKeyWord . '%'])
                        ->orWhereRaw("REPLACE(`phone`, ' ', '') LIKE ?", ['%' . $searchKeyWord . '%'])
                        ->orWhereRaw("REPLACE(`whatsapp`, ' ', '') LIKE ?", ['%' . $searchKeyWord . '%']);
                });
            }

            // Email Filter: search only in the email field
            if (!empty($request->email)) {
                $q->orWhere('email', 'like', '%' . $request->email . '%');
            }



            if (!empty($request->search)) {
                $searchKeyWord = $request->search;

                $cleanedSearch = is_numeric($searchKeyWord) ? trimSpaces(validatePhoneNumber($searchKeyWord)) : null;


                $q->where(function ($query) use ($searchKeyWord, $cleanedSearch) {
                    // For string inputs, search relevant text fields
                    $query->where('first_name', 'like', '%' . $searchKeyWord . '%')
                    ->orWhere('last_name', 'like', '%' . $searchKeyWord . '%')
                    ->orWhere('id', 'like', '%' . $searchKeyWord . '%')
                    ->orWhere('email', 'like', '%' . $searchKeyWord . '%');

                    // For numeric inputs, add numeric-related search conditions
                    if (!is_null($cleanedSearch)) {
                        $query->orWhere('mobile', 'like', '%' . $cleanedSearch . '%')
                            ->orWhereRaw("REPLACE(`mobile`, ' ', '') LIKE ?", ['%' . $cleanedSearch . '%'])
                            ->orWhereRaw("REPLACE(`phone`, ' ', '') LIKE ?", ['%' . $cleanedSearch . '%'])
                            ->orWhereRaw("REPLACE(`whatsapp`, ' ', '') LIKE ?", ['%' . $cleanedSearch . '%']);
                    }
                });


            }


        });

// Ensure results are ordered
        $query->orderBy('updated_at', 'desc');

        $query->with(['criteria', 'criteria.locations.parentable', 'leadAgents.agent:id,first_name,last_name,email,mobile,image']);
        $list_per_page = '10';
        $lead_count = $query->count();
        $leads = $query->paginate($list_per_page);

        if ($lead_count > 0) {
            $leads->data = LeadAxResource::collection($leads);
            $leads->appends($request->query())->links();
        } else {
            return $this->sendResponse(false, 'No Leads found', [], 200,1,10, $request->all());
        }
        // dd($leads);
        return $this->sendResponse(true, 'Lead list', compact('leads'), 200);
    }

    public function getAxLead(Request $request, $id)
    {
        if (!$request->secret) {
            return $this->sendResponse(false, 'You are not authorized', [], 403);
        }
        if ($request->secret != 'd23f221f38f1d29ca5f8ege1dhbj8662') {
            return $this->sendResponse(false, 'You are not authorized', [], 403);
        }

        $lead = Lead::with(['criteria', 'criteria.locations.parentable', 'leadAgents.agent:id,first_name,last_name,email,mobile,image'])->find($id);
        if ($lead) {
            return $this->sendResponse(true, 'Lead found', new LeadResource($lead), 200);
        } else {
            return $this->sendResponse(false, 'Lead not found', [], 200);
        }
    }
    public function getSingleAxLead(Request $request, $id)
    {
        if (!$request->secret) {
            return $this->sendResponse(false, 'You are not authorized', [], 403);
        }
        if ($request->secret != 'd23f221f38f1d29ca5f8ege1dhbj8662') {
            return $this->sendResponse(false, 'You are not authorized', [], 403);
        }

        $lead = Lead::with(['criteria', 'criteria.locations.parentable', 'leadAgents.agent:id,first_name,last_name,email,mobile,image'])->find($id);
        if ($lead) {
            return $this->sendResponse(true, 'Lead found', new LeadResource($lead), 200);
        } else {
            return $this->sendResponse(false, 'Lead not found', [], 200);
        }
    }
    public function getAxContacts(Request $request)
    {
        if (!$request->secret) {
            return $this->sendResponse(false, 'You are not authorized', [], 403);
        }
        if ($request->secret != 'd23f221f38f1d29ca5f8ege1dhbj8662') {
            return $this->sendResponse(false, 'You are not authorized', [], 403);
        }

        $query = Contact::query();

        // Filter by status
        $query->where('status', 1);

        // Filter by agent_id
        if ($request->agent_id) {
            if ($this->isArray("agent_id")) {
                $query->whereIn('agent_id', $request->agent_id);
            } else {
                $query->where('agent_id', $request->agent_id);
            }
        }



        // Apply search logic for mobile, email, and general search
        $query->where(function ($q) use ($request) {
            // Mobile Filter: search in mobile, phone, and whatsapp
            if (!empty($request->mobile)) {
                $searchKeyWord = trimSpaces(validatePhoneNumber($request->mobile));
                $q->orWhere(function ($q) use ($searchKeyWord) {
                    $q->whereRaw("REPLACE(`mobile`, ' ', '') LIKE ?", ['%' . $searchKeyWord . '%'])
                        ->orWhereRaw("REPLACE(`phone`, ' ', '') LIKE ?", ['%' . $searchKeyWord . '%'])
                        ->orWhereRaw("REPLACE(`whatsapp`, ' ', '') LIKE ?", ['%' . $searchKeyWord . '%']);
                });
            }

            // Email Filter: search only in the email field
            if (!empty($request->email)) {
                $q->orWhere('email', 'like', '%' . $request->email . '%');
            }



            if (!empty($request->search)) {
                $searchKeyWord = $request->search;

                $cleanedSearch = is_numeric($searchKeyWord) ? trimSpaces(validatePhoneNumber($searchKeyWord)) : null;


                $q->where(function ($query) use ($searchKeyWord, $cleanedSearch) {
                    // For string inputs, search relevant text fields
                    $query->where('first_name', 'like', '%' . $searchKeyWord . '%')
                        ->orWhere('last_name', 'like', '%' . $searchKeyWord . '%')
                        ->orWhere('id', 'like', '%' . $searchKeyWord . '%')
                        ->orWhere('email', 'like', '%' . $searchKeyWord . '%');

                    // For numeric inputs, add numeric-related search conditions
                    if (!is_null($cleanedSearch)) {
                        $query->orWhere('mobile', 'like', '%' . $cleanedSearch . '%')
                            ->orWhereRaw("REPLACE(`mobile`, ' ', '') LIKE ?", ['%' . $cleanedSearch . '%'])
                            ->orWhereRaw("REPLACE(`phone`, ' ', '') LIKE ?", ['%' . $cleanedSearch . '%'])
                            ->orWhereRaw("REPLACE(`whatsapp`, ' ', '') LIKE ?", ['%' . $cleanedSearch . '%']);
                    }
                });


            }


        });

// Ensure results are ordered
        $query->orderBy('updated_at', 'desc');

      //  $query->with([]);
        $list_per_page = '10';
        $count = $query->count();
        $contacts = $query->paginate($list_per_page);

        if ($count > 0) {
            $contacts->data = ContactAXResource::collection($contacts);
            $contacts->appends($request->query())->links();
        } else {
            return $this->sendResponse(false, 'No Leads found', [], 200,1,10, $request->all());
        }
        // dd($leads);
        return $this->sendResponse(true, 'Contact list', compact('contacts'), 200);
    }
    private function isArray($key)
    {
        return gettype(\request($key)) == 'array' && count(\request($key)) > 0;
    }

    public function archiveLeads(Request $request)
    {
        $LeadController = new LeadController();
        $response = $LeadController->archive($request);
        return $response;
    }

    public function takeArchiveLead(Request $request)
    {
        $LeadController = new LeadController();
        $response = $LeadController->assignArchivedLeadsToMyself($request);
        return $response;
    }

    public function addQualifiedNote(Request $request)
    {
        $LeadController = new LeadController();
        $response = $LeadController->addQualifiedNote($request);
        return $response;
    }

    public function transferToPortalLeadPool(Request $request)
    {
        $LeadController = new LeadController();
        $response = $LeadController->transferToPortalLeadPool($request);
        return $response;
    }
}
