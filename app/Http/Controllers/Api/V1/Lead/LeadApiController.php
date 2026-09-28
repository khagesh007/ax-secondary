<?php

namespace App\Http\Controllers\Api\V1\Lead;

use App\Events\LeadCreated;
use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\V1\Lead\GetLeadListRequest;
use App\Http\Requests\Api\V1\Lead\GetLeadRequest;
use App\Http\Requests\Api\V1\Lead\LeadRequest;
use App\Http\Tasks\Api\V1\Lead\LeadListTask;
use App\Http\Tasks\Api\V1\Lead\LeadShow;
use App\Models\Lead;
use App\Models\LeadNote;
use App\Models\Medium;
use App\Models\Community;
use App\Models\SubCommunity;
use App\Models\CommunityProperty;
use App\Models\LeadCriteriaLocation;
use App\Models\LeadCriteria;
use App\Models\Property;
use App\Models\Source;
use App\Models\Type;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Country;
use App\Jobs\ProcessLead;
use App\Models\LeadStatus;
use App\Models\LeadBitrixResponse;
use App\Jobs\UpdateLeadOnBitrix;
use App\Http\Resources\LeadDataHubApiResource;

class LeadApiController extends BaseController
{

    public function __construct() {}
    /**
     *
     *
     * Lead List
     *
     * Get a list of leads. with pagination
     * @group Leads
     * @authenticated
     * @header Accept application/json
     * @queryParam search string Search by first name, last name, email or mobile Example: John, Doe, johan@demo.com, 1234567890
     * @queryParam agent_id int Agent ID Example: 1
     * @queryParam lead_status_id int Lead Status ID Example: 1
     * @queryParam medium_id int Medium ID Example: 1
     * @queryParam source_id int Source ID Example: 1
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
    public function index(GetLeadListRequest $request, LeadListTask $task)
    {
        if (!auth()->user()->can('Leads List')) {
            return $this->sendResponse(false, 'You are not authorized to access this resource', [], 403);
        }

        $response = $task->run($request);
        return $this->sendResponse($response['status'], $response['message'], $response['data'], $response['code']);
    }
    /**
     * Create a Lead
     *
     * Create a Lead.
     * @group Leads
     * @authenticated
     * @header Accept application/json

     * @bodyParam customer_mobile string required Customer Number is required. Example: +9715285858176
     * @bodyParam source_name string required  Soure of the lead is requried. Example: Facebook
     * @bodyParam medium_name string required  Medium of the lead is requried. Example: API
     * @bodyParam reference_no string  Reference number of the property. Example: axc-232332
     * @bodyParam customer_name string  Customer Name. Example: John Doe
     * @bodyParam customer_email email  Customer Email. Example: f.shabbir@axcapital.ae
     * @bodyParam message text  Any message. Example: Lorem Impsum...
     * @bodyParam return_data boolean  Example: Lorem Impsum...
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
    public function store(Request $request)
    {
        // abort_if($request->api_token != "fYlQO814toCEdmdp", 404);

        try {

            // CHECK BASED ON mobile, email, source, property, agent and name as %like% and put medium on note
            $validatedData = $request->validate([
                "customer_name" => "nullable|string",
                "customer_mobile" => "required|string",
                "customer_email" => "nullable|email",
                "source_name" => "required|string",
                "medium_name" => "required|string",
                "reference_no" => "nullable|string",
                "message" => "nullable|string",
                "return_data" => "nullable",
            ]);

            $name = $validatedData['customer_name'] ?? "";
            $email = $validatedData['customer_email'] ?? "";
            $mobile = validatePhoneNumber($validatedData['customer_mobile']);


            if (checkBlocklist($mobile, Lead::class, $request)) {
                $result = array('status' => '0', 'msg' => 'This number is in Blacklist. Please do not contact with this number.', 'error' => 'This number is in Blacklist. Please do not contact with this number.');
                return json_encode($result);
            }


            $source_name = $validatedData['source_name'];
            $medium_name = $validatedData['medium_name'];
            $ref_no = $validatedData['reference_no'] ?? null;
            $message = $validatedData['message'] ?? "";

            $lead_source = Source::where('is_lead_source', 1)->where("name", 'LIKE', '%' . $source_name . '%')->first();
            $medium = Medium::where("name", 'LIKE', '%' . $medium_name . '%')->pluck("id")->first();

            //$lead = Lead::where('email', $validatedData['customer_email'])->orWhere('mobile', $validatedData['customer_mobile'])->first();

            $property = null;

            if ($ref_no) {
                $property = Property::where('ref_no', $ref_no)->with('agent')->first();
            }
            $note = "
                Message: $message <br>
                Name: $name <br>
                Email: $email <br>
                Mobile: $mobile <br>
                Property Ref no. : $ref_no <br>
                Lead Source:  $source_name <br>
                Medium:  $medium_name";

            $lead = null;
            if (!is_null($property)) {
                $lead = Lead::when($email, function ($query) use ($email) {
                    $query->where('email', $email);
                })
                    ->when(!$email, function ($query) use ($email) {
                        $query->where('email', "");
                    })


                    ->where('mobile', $mobile)
                    ->where('lead_medium_id', $medium)
                    ->where('source_id', $lead_source?->id)
                    ->when($property != null, function ($query) use ($property) {
                        $query->where('property_id', $property?->id);
                    })
                    ->when($property != null && $property?->agent_id, function ($query) use ($property) {
                        $query->where('agent_id', $property?->agent_id);
                    })
                    ->first();
            }

            if (!is_null($lead)) {
                $lead->update([
                    'first_name' => 'DUPLICATE- ' . $lead->first_name,
                    'updated_at' => now(),
                ]);
                $lead->isDuplicate = true;
                event(new LeadCreated($lead));
                LeadNote::create([
                    "lead_id" => $lead->id,
                    "note" => $note,
                ]);
                if (isset($validatedData['return_data']) && $validatedData['return_data'] && $property) {
                    $data = array();
                    $data['property'] = [
                        "reference_no" => $property->ref_no ?? null,
                        "unit_no" => $property->unit_no ?? null,
                        "price" => $property?->price?->price ?? null,
                        "location" => $property?->location ?? null,
                        "agent" => [
                            "name" => $property?->agent->name,
                            "email" => $property?->agent?->email,
                            "telegram_chatid" => $property?->agent?->telegram_chatid,
                        ],
                    ];
                    return $this->sendResponse(true, 'Lead already exists.', $data, 200);
                    // return response()->json(['status' => 'success', 'message' => 'Lead created successfully', 'data' => $data]);
                } else {
                    return $this->sendResponse(true, 'Lead already exists.', null, 200);
                }
            } else {
                $obj = new Lead();
                $obj->first_name = $name;
                $obj->property_id = $property?->id;
                $obj->agent_id = $property?->agent_id ?? null;
                $obj->email = $email;
                $obj->for = $property?->for ?? "Unspecified";
                $obj->mobile = $mobile;
                $obj->whatsapp = $mobile;
                $obj->lead_medium_id = $medium;
                $obj->message = $message;
                $obj->source_id = $lead_source?->id ?? 1;
                $obj->lead_status_id = 1; // Open
                //  $obj->lead_sub_status_id = 4;  // Not yet contacted

                // Code for getting Lead Type----START
                if ($property?->for == 'Residential for sale') {
                    $leadTyped = Type::where('name', 'LIKE', '%Buyer%')->first();
                    $obj->type_id = $leadTyped->id ?? 2;
                } else if ($property?->for == 'Residential for rent') {
                    $leadTyped = Type::where('name', 'LIKE', '%Tenant%')->first();
                    $obj->type_id = $leadTyped->id ?? 2;
                } else if ($property?->for == 'Commercial for sale') {
                    $leadTyped = Type::where('name', 'LIKE', '%Buyer%')->first();
                    $obj->type_id = $leadTyped->id ?? 2;
                } else if ($property?->for == 'Commercial for rent') {
                    $leadTyped = Type::where('name', 'LIKE', '%Tenant%')->first();
                    $obj->type_id = $leadTyped->id ?? 2;
                } else {
                    $leadTyped = Type::where('name', 'LIKE', '%Other%')->first();
                    $obj->type_id = $leadTyped->id ?? 10;
                }

                // Code for getting Lead Type ----END
                if ($obj->save()) {
                    event(new LeadCreated($obj));
                    LeadNote::create([
                        "lead_id" => $obj->id,
                        "note" => $note,
                    ]);
                    $criteria = null;
                    $community_property = null;
                    $region = null;
                    $community = null;
                    $sub_community = null;


                    if ($property) {
                        $criteria = getPropertyCriteria($property->ref_no);
                        if ((!$community) && $criteria['community_id']) {
                            $community = Community::find($criteria['community_id']);
                        }
                        if ((!$sub_community) && $criteria['subCommunity_id']) {
                            $sub_community = SubCommunity::find($criteria['subCommunity_id']);
                        }
                        if ((!$community_property) && $criteria['communityProperty']) {
                            $community_property = CommunityProperty::find($criteria['communityProperty']);
                        }

                        $leadCriteria = new LeadCriteria();
                        $leadCriteria->lead_id = $obj->id;
                        $leadCriteria->furnished = $property->furnished;
                        $leadCriteria->min_bed_id = $criteria['bed_id'] ?? null;
                        $leadCriteria->max_bed_id = $criteria['bed_id'] ?? null;
                        $leadCriteria->min_bath_id = $criteria['bath_id'] ?? null;
                        $leadCriteria->max_bath_id = $criteria['bath_id'] ?? null;
                        $leadCriteria->min_price = $criteria['minPrice'] ?? null;
                        $leadCriteria->max_price = $criteria['maxPrice'] ?? null;
                        $leadCriteria->save();
                        if ($community) {
                            LeadCriteriaLocation::create([
                                'lead_criteria_id' => $leadCriteria->id,
                                'parentable_type' => Community::class,
                                'parentable_id' => $community->id,
                            ]);
                        }

                        if ($sub_community) {
                            LeadCriteriaLocation::create([
                                'lead_criteria_id' => $leadCriteria->id,
                                'parentable_type' => SubCommunity::class,
                                'parentable_id' => $sub_community->id,
                            ]);
                        }

                        if ($community_property) {
                            LeadCriteriaLocation::create([
                                'lead_criteria_id' => $leadCriteria->id,
                                'parentable_type' => CommunityProperty::class,
                                'parentable_id' => $community_property->id,
                            ]);
                        }
                        $leadCriteria->view = $criteria['view'];
                        $leadCriteria->save();
                    }



                    if (isset($validatedData['return_data']) && $validatedData['return_data'] && $property) {
                        $data = array();

                        $data['property'] = [
                            'lead_id' => $obj->id,
                            "reference_no" => $property->ref_no ?? null,
                            "unit_no" => $property->unit_no ?? null,
                            "price" => $property?->price?->price ?? null,
                            "location" => $property?->location ?? null,
                            "agent" => [
                                "name" => $property?->agent->name,
                                "email" => $property?->agent?->email,
                                "telegram_chatid" => $property?->agent?->telegram_chatid,
                            ],
                        ];
                        return $this->sendResponse(true, 'Lead created successfully', $data, 200);
                        // return response()->json(['status' => 'success', 'message' => 'Lead created successfully', 'data' => $data]);
                    }
                    return $this->sendResponse(true, 'Lead created successfully', $obj, 200);
                    // return response()->json(['status' => 'success', 'message' => 'Lead created successfully']);
                } else {
                    return $this->sendResponse(false, 'Lead not created', null, 500);
                    // return response()->json(['status' => 'error', 'message' => 'Lead not created']);
                }

                return $this->sendResponse(false, 'Lead not created', null, 500);
            }
            // return response()->json(['status' => 'error', 'message' => 'Lead not created']);
            // }
        } catch (\Throwable $th) {
            Log::channel('discord')->error("Error in store function in LeadApiController.php " . $th->getMessage());

            // dd($th);
            return $this->sendResponse(false, $th->getMessage(), null, 400);
        }
    }

    /**
     * Lead details
     *
     * Get details of a lead.
     * @group Leads
     *
     * @authenticated
     * @param  \Illuminate\Http\Request  $request
     *
     * @urlParam id int required  ID of the lead. Example: 1
     *
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
    "status": true,
    "code": 200,
    "message": "Lead details",
    "data": {
    "lead": {
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
    }
    }
    }
     *
     *
     */
    public function show($id, GetLeadRequest $request, LeadShow $task)
    {
        $response = $task->run($request);
        return $this->sendResponse($response['status'], $response['message'], $response['data'], $response['code']);
    }
    /**
     * Delete a Lead
     *
     * Delete a lead.
     * @group Leads
     * @authenticated
     * @header Accept application/json
     * @bodyParam id integer required The id of the lead. Example: 1
     * @response 200 scenario=success
     * {
    "status": true,
    "code": 200,
    "message": "Lead deleted successfully",
    "data": null
    }
     *
     * @response 200 scenario="not found"
     * {
    "status": false,
    "code": 200,
    "message": "Lead not found",
    "data": null
    }
     *
     * @response 422 scenario="validation error"
     * {
    "message": "The selected id is invalid.",
    "errors": {
    "id": [
    "The selected id is invalid."
    ]
    }
    }
     */
    public function destroy(LeadRequest $request)
    {
        if (!auth()->user()->can('Leads Delete')) {
            return $this->sendResponse(false, 'You are not authorized to access this resource', [], 403);
        }
        try {
            if (Lead::where('id', $request->id)->doesntExist()) {
                return $this->sendResponse(false, 'Lead not found', null, 200);
            }

            $lead = Lead::find($request->id);
            if ($lead->delete()) {
                return $this->sendResponse(true, 'Lead deleted successfully', null, 200);
            } else {
                return $this->sendResponse(false, 'Lead not deleted', null, 400);
            }
        } catch (\Exception $e) {
            Log::channel('discord')->error("Error in destroy function in LeadApiController.php " . $e->getMessage());

            return $this->sendResponse(false, 'Lead not deleted', null, 500);
        }
    }

    public function storeZapierWebhook(Request $request)
    {

        try {
            info($request);
            // Get the bitrix id and send response
            // $validatedData = $request->validate([
            //     "bitrix_lead_id" => "required|string",
            //     "from_agent_id" => "nullable|string",
            //     "to_agent_id" => "nullable|string",
            // ]);
            //ProcessLead::dispatch($validatedData);
            return $this->sendResponse(true, 'Lead processed with zapier webhook', true, 200);
        } catch (\Throwable $th) {
            Log::channel('discord')->error("Error in store function in LeadApiController.php " . $th->getMessage());

            // dd($th);
            return $this->sendResponse(false, $th->getMessage(), null, 400);
        }
    }

    /**
     *
     * Store Lead Webhook
     * @group Leads
     * @authenticated
     * @header Accept application/json
     * @bodyParam bitrix_lead_id string required The id of the bitrix lead. Example: 1067
     * @bodyParam from_agent_id string The bitrix id of the user. Example: 155
     * @bodyParam to_agent_id string The bitrix id of the user. Example: 222
     * @response 200 scenario=success
     * {
    "status": true,
    "code": 200,
    "message": "Lead processed",
    "data": null
    }
     *
     * @response 422 scenario="validation error"
    {
    "status": false,
    "code": 400,
    "message": "The bitrix lead id field is required.",
    "data": null
}
    }
     */

    public function storeWebhook(Request $request)
    {

        try {

            // Get the bitrix id and send response
            $validatedData = $request->validate([
                "bitrix_lead_id" => "required|string",
                "from_agent_id" => "nullable|string",
                "to_agent_id" => "nullable|string",
            ]);
            ProcessLead::dispatch($validatedData);
            return $this->sendResponse(true, 'Lead processed', true, 200);
        } catch (\Throwable $th) {
            Log::channel('discord')->error("Error in store function in LeadApiController.php " . $th->getMessage());

            // dd($th);
            return $this->sendResponse(false, $th->getMessage(), null, 400);
        }
    }

    public function changeLeadAgent(Request $request)
    {
        try {
            $validatedData = $request->validate([
                "lead_id" => "required",
                "from_id" => "required",
                "to_id" => "required",
            ]);
            $lead = Lead::find($validatedData['lead_id']);
            $lead->agent_id = $validatedData['agent_id'];
            $lead->save();
            return $this->sendResponse(true, 'Lead agent changed successfully', null, 200);
        } catch (\Throwable $th) {
            Log::channel('discord')->error("Error in changeLeadAgent function in LeadApiController.php " . $th->getMessage());

            return $this->sendResponse(false, $th->getMessage(), null, 400);
        }
    }
    public function storeLeadFromBitrixId($bitrixId)
    {
        try {
            // Api User fields
            $apiSourceDetails   = "UF_CRM_1553506792";
            $apiLocation        = "UF_CRM_1553171363";
            $apiLeadType        = "UF_CRM_1561214850";
            $apiLanguage        = "UF_CRM_1553165368131";
            $apiNationality     = "UF_CRM_1553166948";


            // Api User fields end here

            $header  = array();
            $header[] = 'Content-Type: application/json';
            $header[] = 'Content-Length: 4000';
            $header[] = 'Connection: keep-alive';

            // UF_CRM_1553171363 is the key for location
            $response = Http::get('https://crm.axcap.ae/rest/1/8cd3l0kbdlndvtqf/crm.lead.userfield.list?filter[FIELD_NAME]=UF_CRM_1553171363');
            $res = $response->json();
            $locs = $res['result'][0]['LIST'];
            $locations = array();
            // Putting the locations in one dimension array so I can get the data by id based.
            foreach ($locs as $record) {
                $locations[$record['ID']] = $record['VALUE'];
            }
            // UF_CRM_1553165368131 is key for language
            $response = Http::get('https://crm.axcap.ae/rest/1/8cd3l0kbdlndvtqf/crm.lead.userfield.list?filter[FIELD_NAME]=UF_CRM_1553165368131');
            $res = $response->json();
            $langs = $res['result'][0]['LIST'];

            $languages = array();
            // Putting the languages in one dimension array so I can get the data by id based.
            foreach ($langs as $record) {
                $languages[$record['ID']] = $record['VALUE'];
            }
            // UF_CRM_1553166948 is key for nationality
            $response = Http::get('https://crm.axcap.ae/rest/1/8cd3l0kbdlndvtqf/crm.lead.userfield.list?filter[FIELD_NAME]=UF_CRM_1553166948');
            $res = $response->json();
            $nation = $res['result'][0]['LIST'];

            $nationalities = array();
            // Putting the languages in one dimension array so I can get the data by id based.
            foreach ($nation as $record) {
                $nationalities[$record['ID']] = $record['VALUE'];
            }

            // UF_CRM_1561214850 user field for lead type
            $lead_type_array = array(
                6793 => 2, // Buyer
                6794 => 3, // Landlord
                6814 => 2, // Buyer
                6795 => 1, // Tenant
                6796 => 3, // Landlord
                7263 => 12, // Agency
                7273 => 11, // Unspecified
                12245 => 11, // Unspecified
                17238 => 11, // Unspecified
            );
            $leadStatusesArray =  array(
                '4' => 5, //Not answered
                'IN_PROCESS' => 5, //Contacted
                "2" => 8, // Qualified
                '3' => 9, // Successful
                '6' => 5, // Contacted
                '1' => 1, // New
                'NEW' => 1,
                'PROCESSED' => 8, // Qualified
                'CONVERTED' => 8, // Successful
            );

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://crm.axcap.ae/rest/1/8cd3l0kbdlndvtqf/crm.lead.list',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array('ORDER[ID]' => 'ASC', 'FILTER[ID]' => $bitrixId, 'SELECT[0]' => '*', 'SELECT[1]' => 'PHONE', 'SELECT[2]' => 'EMAIL', 'SELECT[3]' => "UF_*"),
                CURLOPT_HTTPHEADER => array(
                    'Cookie: BITRIX_SM_SALE_UID=0; PHPSESSID=KpxDF0YR3tW047SdI7gmD1XxVm7x5Ytw'
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            $response = json_decode($response);
            $next = $response?->next ?? null;
            $updated = 0;
            foreach ($response?->result ?? [] as $res) {
                $mobile = '';
                $email = '';
                $rawMobile = '';
                if ($res->HAS_PHONE == 'Y' && $res?->PHONE[0]->VALUE != '') {
                    $mobile = $rawMobile = intval(preg_replace('/[^0-9]+/', '', $res->PHONE[0]->VALUE));
                    $mobile = mobileNumberFixer($mobile);
                }
                if ($res->HAS_EMAIL == 'Y' && $res?->EMAIL[0]->VALUE != '') {
                    $email = $res->EMAIL[0]->VALUE;
                    if (Str::length($email) < 5) {
                        $email = null;
                    }
                }

                $source = findSourceForBitrix($res->SOURCE_ID);

                $sourceName = findSourceNameForBitrix($res->SOURCE_ID);

                $source = Source::whereRaw('UPPER(name) LIKE ?', ['%' . strtoupper($source) . '%'])->pluck('id')->first() ?? null;
                $medium = findMediumForBitrix($res->SOURCE_ID);
                $medium = Medium::whereRaw('UPPER(name) LIKE ?', ['%' . strtoupper($medium) . '%'])->pluck('id')->first() ?? null;
                $lead = Lead::where('bitrix_id', $res->ID)->where('need_to_be_destroyed', 0)->first();

                if (!empty($lead)) {


                    $lead->disableLogging();
                    $lead->whatsapp = $mobile ?? null;
                    $lead->lead_medium_id = $lead->lead_medium_id != null ? $lead->lead_medium_id : $medium;
                    $lead->for = $lead->for != null ? $lead->for : 'Unspecified';
                    $lead->lead_status_id = $leadStatusesArray[$res->STATUS_ID] ?? 3; // 3 unspecified
                    // $lead->lead_sub_status_id = '8'; // 11 unspecified
                    if ($res->UF_CRM_1561214850) {
                        $lead->type_id = $lead_type_array[$res->UF_CRM_1561214850];
                    } else {
                        $lead->type_id = '11'; // 11 unspecified
                    }

                    $lead->source_id =  $source;
                    $lead->created_at = $res->DATE_CREATE;
                    $lead->updated_at = $res->DATE_MODIFY;
                    $lead->save();

                    $leadCriteria = LeadCriteria::where('lead_id', $lead->id)->first() ?? new LeadCriteria();

                    $leadCriteria->lead_id      = $lead->id;

                    $srcDetails = "";
                    if ($res->$apiSourceDetails) {
                        $srcDetails = $res->$apiSourceDetails; //UF_CRM_1553506792
                    }

                    $srcLocation = "";
                    if ($res->UF_CRM_1553171363) {
                        $srcLocation = $locations[$res->UF_CRM_1553171363] ?? null; //UF_CRM_1553171363
                    }
                    $srcLanguage = "";
                    if ($res->UF_CRM_1553165368131) {
                        $srcLanguage = $languages[$res->UF_CRM_1553165368131] ?? null; //UF_CRM_1553165368131
                    }
                    $srcNationality = "";
                    if ($res->$apiNationality) {
                        $srcNationality = $nationalities[$res->UF_CRM_1553166948] ?? null; //UF_CRM_1553166948
                    }



                    $leadCriteria->bitrix_data = 'Imported from Bitrix24
                                    <br> ID: ' . $res->ID . '
                                    <br> Source Details: ' . $srcDetails . '
                                    <br> Source Name: ' . $sourceName . '
                                    <br> Location: ' . $srcLocation . '
                                    <br> Nationlity: ' . $srcNationality . '
                                    <br> Language: ' . $srcLanguage . '
                                    <br> Imported at: ' . now();

                    $leadCriteria->save();


                    $updated++;
                } else { //if it's already imported from other system only create notes
                    $lead = Lead::where('email', $email)->where('mobile', $mobile)->where('source_id', $source)->where('need_to_be_destroyed', 0)->first();
                    if (!empty($lead)) {
                        if ($lead->bitrix_id == null) {
                            // DB::table('activity_log')->where('subject_type',Lead::class)->where('subject_id',$lead->id)->whereDate('created_at',">=","2023-05-04")->delete();
                            $lead->bitrix_id = $res->ID;
                            $lead->whatsapp = $mobile ?? null;
                            $lead->for = $lead->for != null ? $lead->for : 'Unspecified';
                            $lead->save();
                            $leadNote = new LeadNote();
                            $leadNote->lead_id = $lead->id;
                            $leadNote->note = 'Imported from Bitrix24
                                            <br> ID: ' . $res->ID . '
                                            <br> Comments: ' . $res->COMMENTS . '
                                            <br> Title: ' . $res->TITLE . '
                                            <br> Name: ' . $res->NAME . '
                                            <br> Second Name: ' . $res->SECOND_NAME . '
                                            <br> Last Name: ' . $res->LAST_NAME . '
                                            <br> Mobile: ' . $rawMobile . '
                                            <br> Email: ' . $email . '
                                            <br> SOURCE_ID: ' . $res->SOURCE_ID . '
                                            <br> Source Name: ' . $sourceName . '
                                            <br> Currency: ' . $res->CURRENCY_ID . '
                                            <br> Lead created at: ' . $res->DATE_CREATE . '
                                            <br> Imported at: ' . now();
                            $leadNote->save();


                            $updated++;
                        } else {
                            $lead->bitrix_id = $res->ID;
                            $lead->lead_medium_id = $medium;
                            $lead->source_id = $source;
                            $lead->created_at = $res->DATE_CREATE;
                            $lead->updated_at = $res->DATE_MODIFY;
                            $lead->save();
                        }
                        $leadCriteria = LeadCriteria::where('lead_id', $lead->id)->first() ?? new LeadCriteria();

                        $leadCriteria->lead_id      = $lead->id;

                        $srcDetails = "";
                        if ($res->$apiSourceDetails) {
                            $srcDetails = $res->$apiSourceDetails; //UF_CRM_1553506792
                        }

                        $srcLocation = "";
                        if ($res->UF_CRM_1553171363) {
                            $srcLocation = $locations[$res->UF_CRM_1553171363] ?? null; //UF_CRM_1553171363
                        }
                        $srcLanguage = "";
                        if ($res->UF_CRM_1553165368131) {
                            $srcLanguage = $languages[$res->UF_CRM_1553165368131] ?? null; //UF_CRM_1553165368131
                        }
                        $srcNationality = "";
                        if ($res->UF_CRM_1553166948) {
                            $srcNationality = $nationalities[$res->UF_CRM_1553166948] ?? null; //UF_CRM_1553165368131
                        }



                        $leadCriteria->bitrix_data = 'Imported from Bitrix24
                                        <br> ID: ' . $res->ID . '
                                        <br> Source Details: ' . $srcDetails . '
                                        <br> Source Name: ' . $sourceName . '
                                        <br> Location: ' . $srcLocation . '
                                        <br> Nationlity: ' . $srcNationality . '
                                        <br> Language: ' . $srcLanguage . '
                                        <br> Imported at: ' . now();

                        $leadCriteria->save();
                    } else {

                        $lead = new Lead();
                        $lead->bitrix_id = $res->ID;
                        $lead->first_name = $res->NAME;
                        $lead->last_name = $res->LAST_NAME;
                        $lead->mobile = $mobile ?? null;
                        $lead->whatsapp = $mobile ?? null;
                        $lead->for = 'Unspecified';
                        $lead->email = $email ?? null;
                        $lead->lead_medium_id = $medium;
                        $lead->source_id = $source;
                        $lead->lead_status_id = $leadStatusesArray[$res->STATUS_ID] ?? 3; // 3 unspecified
                        // $lead->lead_sub_status_id = '8'; // 11 unspecified

                        if ($res->UF_CRM_1561214850) {
                            $lead->type_id = $lead_type_array[$res->UF_CRM_1561214850] ?? '11';
                        } else {
                            $lead->type_id = '11'; // 11 unspecified
                        }
                        $lead->created_at = $res->DATE_CREATE;
                        $lead->updated_at = $res->DATE_MODIFY;
                        if ($lead->save()) {

                            $leadNote = new LeadNote();
                            $leadNote->lead_id = $lead->id;
                            $leadNote->note = 'Imported from Bitrix24
                                            <br> ID: ' . $res->ID . '
                                            <br> Comments: ' . $res->COMMENTS . '
                                            <br> Title: ' . $res->TITLE . '
                                            <br> Name: ' . $res->NAME . '
                                            <br> Second Name: ' . $res->SECOND_NAME . '
                                            <br> Last Name: ' . $res->LAST_NAME . '
                                            <br> Mobile: ' . $rawMobile . '
                                            <br> Email: ' . $email . '
                                            <br> SOURCE_ID: ' . $res->SOURCE_ID . '
                                            <br> Source Name: ' . $sourceName . '
                                            <br> Currency: ' . $res->CURRENCY_ID . '
                                            <br> Lead created at: ' . $res->DATE_CREATE . '
                                            <br> Lead updated at: ' . $res->DATE_MODIFY . '
                                            <br> Imported at ' . now();
                            $leadNote->save();
                        }
                        $leadCriteria = LeadCriteria::where('lead_id', $lead->id)->first() ?? new LeadCriteria();

                        $leadCriteria->lead_id      = $lead->id;

                        $srcDetails = "";
                        if ($res->$apiSourceDetails) {
                            $srcDetails = $res->$apiSourceDetails; //UF_CRM_1553506792
                        }

                        $srcLocation = "";
                        if ($res->UF_CRM_1553171363) {
                            $srcLocation = $locations[$res->UF_CRM_1553171363] ?? null; //UF_CRM_1553171363
                        }
                        $srcLanguage = "";
                        if ($res->UF_CRM_1553165368131) {
                            $srcLanguage = $languages[$res->UF_CRM_1553165368131] ?? null; //UF_CRM_1553165368131
                        }
                        $srcNationality = "";
                        if ($res->UF_CRM_1553166948) {
                            $srcNationality = $nationalities[$res->UF_CRM_1553166948] ?? null; //UF_CRM_1553166948
                        }



                        $leadCriteria->bitrix_data = 'Imported from Bitrix24
                                        <br> ID: ' . $res->ID . '
                                        <br> Source Details: ' . $srcDetails . '
                                        <br> Source Name: ' . $sourceName . '
                                        <br> Location: ' . $srcLocation . '
                                        <br> Nationlity: ' . $srcNationality . '
                                        <br> Language: ' . $srcLanguage . '
                                        <br> Imported at: ' . now();

                        $leadCriteria->save();
                    }
                }
            }
            return true;
        } catch (\Exception $e) {
            info('Error: ' . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
            return false;
        }
    }



    public function sendLeadToBitrix(Request $request)
    {
        // dd($request->all());
        try {
            // dd($leadData['first_name']);
            $webhookUrl = 'http://127.0.0.1:8002/api/webhook/get-lead';
            // $webhookUrl = 'https://api.axc.ae/webhook/b7698ece-08e0-4275-8636-b90adfec5f59';
            $lead = \App\Models\Lead::find($request->lead_id);
            $source_name = null;
            $portal_link = null;
            $property_details = null;

            if ($lead->agent_id == 4) {
                $property = \App\Models\Property::find($lead->property_id);
                $location_details = '';

                if ($property) {

                    if ($property->community_id != null) {
                        $community = \App\Models\Community::find($property->community_id);
                        $location_details = $location_details . $community->name ?? '';
                    }

                    if ($property->sub_community_id != null) {
                        $sub_community = \App\Models\SubCommunity::find($property->sub_community_id);
                        $location_details = $location_details . ', ' . $sub_community->name ?? '';
                    }
                    if ($property->community_property_id != null) {
                        $community_property = \App\Models\CommunityProperty::find($property->community_property_id);
                        $location_details = $location_details . ', ' . $community_property->name ?? '';
                    }




                    if ($lead['source_id'] != null) {

                        if ($lead['source_id'] == 4) {
                            $source_name = 12;
                            $portalLink = \App\Models\PropertyMeta::where('property_id', $property->id)->where('meta_key', 'bayut_url')->first();
                            $portal_link = $portalLink?->meta_value ?? null;
                        } else if ($lead['source_id'] == 13) {
                            $source_name = 24;
                            $portalLink = \App\Models\PropertyMeta::where('property_id', $property->id)->where('meta_key', 'dubizzle_url')->first();
                            $portal_link = $portalLink?->meta_value ?? null;
                        } else if ($lead['source_id'] == 31) {
                            $source_name = 15;
                            $portalLink = \App\Models\PropertyMeta::where('meta_key', 'pf_url')->where('property_id', $property->id)->first();
                            $portal_link = $portalLink?->meta_value ?? null;
                        } else {
                            $source_name = '';
                        }
                    }
                    $original_bath = \App\Models\Bath::find($property?->bath_id);
                    $original_bed = \App\Models\Bed::find($property?->bed_id);
                    if ($original_bath) {
                        $bath = ', ' . $original_bath->name;
                    } else {
                        $bath = '';
                    }
                    if ($original_bed) {
                        $bed = ', ' . $original_bed->name;
                    } else {
                        $bed = '';
                    }
                    $property_details = $property?->unit_no . $bath . $bed . ', ' . $property?->propertyCategory?->title . ', ' . $location_details;
                }

                $original_source = \App\Models\Source::find($lead['source_id']);
                $original_medium = \App\Models\Medium::find($lead['lead_medium_id']);


                $short_link = \App\Models\Shortlink::where('lead_id', $lead->id)->first();



                $arry = [
                    'id' => $lead->id,
                    'first_name' => $lead['first_name'] ?? null,
                    'last_name' => $lead['last_name'] ?? null,
                    'email' => $lead['email'] ?? null,
                    'mobile' => $lead['mobile'] ?? null,
                    'medium' => $original_medium?->name ?? null,
                    'message' => $lead['message'] ?? null,
                    'source' => $original_source?->name,
                    'listing_ref_no' => $property?->ref_no ?? null,
                    'listing_details' => $property_details ?? null,
                    'whatsapp_url' => $short_link->short_link ?? null,
                    'portal_url' => $portal_link ?? null,
                ];
                $postData = json_encode(['fields' => $arry]);

                $ch = curl_init();
                curl_setopt_array($ch, [
                    CURLOPT_URL => $webhookUrl,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => $postData,
                    CURLOPT_HTTPHEADER => [
                        'Content-Type: application/json',
                        'Content-Length: ' . strlen($postData),
                    ],
                    CURLOPT_RETURNTRANSFER => true,
                ]);
                $response = curl_exec($ch);
                if (curl_errno($ch)) {
                    Log::channel('discord')->error("ERROR IN SENDING DATA TO BITRIX.", [
                        'lead' => $arry,
                        'response' => $response,
                    ]);
                }

                LeadNote::create([
                    'lead_id' => $lead->id,
                    'note' => 'Lead has been transferred to Bitrix',
                    // 'created_by_id' => $lead->agent_id,
                    'status' => 1,
                ]);

                $lead->lead_status_id = 5;
                $lead->save();


                Log::channel('discord')->error("Lead Data Sent to BITRIX WEBHOOK ", [
                    'lead' => $arry,
                    'response' => $response,
                ]);

                curl_close($ch);
                return $this->sendResponse(true, 'Lead sent to bitrix', null, 200);
            }
        } catch (\Throwable $th) {
            Log::channel('discord')->error("Error In Sending Data To Bitrix.", [
                'ERROR' => $th,
            ]);
        }
    }

    public function updateAxLead(Request $request)
    {

        if (!$request->secret) {
            return $this->sendResponse(false, 'You are not authorized', [], 403);
        }

        if ($request->secret != env('AXDEAL_SECRET_KEY')) {
            return $this->sendResponse(false, 'You are not authorized', [], 403);
        }
        try {
            $requestLead = $request->lead;
            if ($requestLead['secondary_id']) {
                $lead = Lead::where('id', $requestLead['secondary_id'])->first();
                if ($lead) {
                    $validTitles = ['Mr.', 'Mrs.', 'Ms.', 'Miss.', 'Dr.', 'Prof.'];
                    $requestTitle = $requestLead['title'] ? $requestLead['title'] . "." : null;

                    if (!in_array($requestTitle, $validTitles)) {
                        $requestTitle = null;
                    }
                    $nationalityName = isset($requestLead['nationality']['name']) ? $requestLead['nationality']['name'] : null;
                    if ($nationalityName) {
                        if (Country::Where('name', $nationalityName)->first()) {
                            $lead->nationality_id = Country::Where('name', $nationalityName)->first()->id;
                        }
                    }
                    if (isset($requestLead['source'])) {
                        $sourceName = $requestLead['source'];
                        if (Source::where('name', $sourceName)->first()) {
                            $lead->source_id = Source::where('name', $sourceName)->first()->id;
                        } else {
                            $source = new Source;
                            $source->name = $sourceName;
                            $source->status = 1;
                            $source->save();
                            $lead->source_id = $source->id;
                        }
                    }
                    if (isset($requestLead['medium'])) {
                        $mediumName = $requestLead['medium'];
                        if (Medium::where('name', $mediumName)->first()) {
                            $lead->lead_medium_id = Medium::where('name', $mediumName)->first()->id;
                        } else {
                            $medium = new Medium;
                            $medium->name = $mediumName;
                            $medium->status = 1;
                            $medium->save();
                            $lead->lead_medium_id = $medium->id;
                        }
                    }
                    $lead->first_name = $requestLead['first_name'] ?? $lead->first_name;
                    $lead->last_name = $requestLead['last_name'] ?? $lead->last_name;
                    $lead->email = $requestLead['email'] ?? $lead->email;
                    $lead->title = $requestTitle;
                    $lead->phone = $requestLead['phone'] ?? $lead->phone;
                    $lead->mobile = $requestLead['mobile'] ?? $lead->mobile;
                    $lead->dob = $requestLead['dob'] ?? $lead->dob;
                    $lead->whatsapp = $requestLead['whatsapp'] ?? $lead->whatsapp;
                    $lead->address = $requestLead['residence_address'] ?? $lead->address;
                    $lead->gender = $requestLead['gender'] ?? $lead->gender;
                    // $lead->agent_id = $requestLead['agent_id'] ?? $lead->agent_id;
                    // $lead->passport_expiry = $requestLead['passport_expiry_date'] ?? $lead->passport_expiry;
                    // $lead->passport = $requestLead['passport_number'] ?? $lead->passport;
                    $lead->company = $requestLead['company_name'] ?? $lead->company_name;

                    if (isset($requestLead['announce_deal'])) {
                        if (LeadStatus::Where('name', 'Successful')->first()) {
                            $lead->lead_status_id = LeadStatus::Where('name', 'Successful')->first()->id;
                        }
                    }

                    if (isset($requestLead['contact_type'])) {
                        if (Type::Where('name', $requestLead['contact_type'])->first()) {
                            $lead->type_id = Type::Where('name', $requestLead['contact_type'])->first()->id;
                        }
                    }
                    $lead->save();
                    return $this->sendResponse(true, 'Lead Update saved.', null, 200);
                }else{
                    return $this->sendResponse(false, 'Lead Id not found', null, 200);
                }
            } else {
                return $this->sendResponse(false, 'Secondary Id not found', [], 200);
            }
        } catch (\Exception $exception) {
            return $this->sendResponse(false, 'Something Went Wrong.', ['error' => $exception->getMessage(),
                'line' => $exception->getLine(),
                'file' => $exception->getFile()
            ], 500);
        }
    }
    public function syncLeadBitrixResponse(Request $request)
    {
        try {
            $perPage = $request->get('length', 12);
            $leadId = $request->get('lead_id');
            $response = LeadBitrixResponse::when($leadId, function ($query) use ($leadId) {
                return $query->where('lead_id', $leadId);
            })->orderBy('id', 'desc')->paginate($perPage);
            return $this->sendResponse(true, 'Lead Bitrix Response', $response, 200);

        } catch (\Exception $exception) {
            return $this->sendResponse(false, 'Something Went Wrong.', ['error' => $exception->getMessage(),
                'line' => $exception->getLine(),
                'file' => $exception->getFile()
            ], 500);
        }
    }
    public function deleteLeadBitrixResponse()
    {
        try {
            $response = LeadBitrixResponse::whereNull('deleted_at')->forceDelete();
            return $this->sendResponse(true, 'Lead Bitrix Response', $response, 200);

        } catch (\Exception $exception) {
            return $this->sendResponse(false, 'Something Went Wrong.', ['error' => $exception->getMessage(),
                'line' => $exception->getLine(),
                'file' => $exception->getFile()
            ], 500);
        }
    }
    public function createLeadOnBitrix($leadId)
    {
        try {
            UpdateLeadOnBitrix::dispatch($leadId);
        } catch (\Throwable $exception) {
            return $this->sendException(error: $exception->getMessage());
        }
    }
    public function getLeads(Request $request)
    {
        try{
            $page = $request->input('page', 1);
            $perPage = $request->input('itemsPerPage', 10);
            $leads = Lead::with(['agent', 'leadSource', 'property', 'nationality', 'leadMedium', 'leadType', 'leadStatus', 'criteria',  'criteria.locations.parentable', 'updatedBy'])
                ->whereNull('bitrix_id')
                ->orderBy('id', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);
              
            $leads = LeadDataHubApiResource::collection($leads);
            return $this->sendPaginationResponse($leads, 'Data fetched', $page, $perPage, 'Leads', $leads->total());
        } catch (\Throwable $exception) {
            return $this->sendException(error: $exception->getMessage());
        }
    }
    public function getLead($id)
    {
        try{
            $lead = Lead::with(['agent', 'leadSource', 'property', 'nationality', 'leadMedium', 'leadType', 'leadStatus', 'criteria',  'criteria.locations.parentable', 'updatedBy'])
                ->whereNull('bitrix_id')
                ->where('id', $id)   // instead of find()
                ->first();

            $lead = new LeadDataHubApiResource($lead);

            return $this->sendDataResponse($lead, "Lead Data");

        } catch (\Throwable $exception) {
            return $this->sendException(error: $exception->getMessage());
        }
    }
    public function getLeadTypes(){
        try {
            $leadTypes = Type::where('status', 1)->get();
            return $this->sendResponse(true, 'Lead Types Retrieved Successfully', $leadTypes, 200);
        } catch (\Throwable $exception) {
            return $this->sendException(error: $exception->getMessage());
        }
    }
}
