<?php

namespace App\Http\Controllers\Api\V1\Dashboard;

use App\Http\Controllers\Api\BaseController;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\EventType;
use App\Models\Lead;
use App\Models\LeadStatus;
use App\Models\ListingLead;
use App\Models\Portal;
use App\Models\Property;
use App\Models\Source;
use App\Models\User;
use App\Models\UserAppLogin;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DashboardApiController extends BaseController
{

    /**
     *
     *
     *
     * Basic Dashboard Stats
     * @group Dashboard
     * @authenticated
     * @header Accept application/json
    Example: abc
     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
    {
    "status": true,
    "code": 200,
    "message": "Dashboard Stats",
    "data": {
    "portalWiseCounts": [
    {
    "id": 1,
    "name": "Property Finder",
    "short_name": "PF",
    "slug": "property-finder",
    "quota": 50000,
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb167644265820230215063058.png",
    "image": "https://s3.eu-west-1.amazonaws.com/secondary.crm/images/thumb166970050120221129054141.png",
    "xml_url": "dqfr6fzvKJiT8uzc",
    "token": "UqFHM.HYLrZih4zMAbojYN2yJFreRehHASHtctyx",
    "token_abu_dhabi": null,
    "token_commercial": null,
    "status": 1,
    "created_by_id": null,
    "updated_by_id": 1,
    "deleted_at": null,
    "created_at": "2022-09-13T19:54:06.000000Z",
    "updated_at": "2024-03-28T11:39:13.000000Z",
    "properties_count": 0
    },
    {
    "id": 2,
    "name": "Bayut",
    "short_name": "BYT",
    "slug": "bayut",
    "quota": 50000,
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb167644259820230215062958.png",
    "image": "https://s3.eu-west-1.amazonaws.com/secondary.crm/images/thumb166970051320221129054153.png",
    "xml_url": "ZWSZBE97qzEvH50R",
    "token": "Bearer 22339714|9AOyqMcUJL2n6NA3cURb4LNSgcMIBxZA3r0O00I0",
    "token_abu_dhabi": "Bearer 22694475|zAIOfLFgNzA89H6jBKCSqBIy449KNyfcnCaVCpal",
    "token_commercial": "Bearer 22695376|flcCEFIy4pSaa124vm75pYY7tsSKYAOFE00C8zsC",
    "status": 1,
    "created_by_id": null,
    "updated_by_id": 1041,
    "deleted_at": null,
    "created_at": "2022-09-13T19:54:06.000000Z",
    "updated_at": "2024-06-20T06:27:57.000000Z",
    "properties_count": 0
    },
    {
    "id": 3,
    "name": "Dubizzle",
    "short_name": "DBZL",
    "slug": "dubizzle",
    "quota": 50000,
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb167644264020230215063040.png",
    "image": "https://s3.eu-west-1.amazonaws.com/secondary.crm/images/thumb166970052320221129054203.png",
    "xml_url": "PMfs128DSiFud3Ok",
    "token": null,
    "token_abu_dhabi": null,
    "token_commercial": null,
    "status": 1,
    "created_by_id": null,
    "updated_by_id": 1,
    "deleted_at": null,
    "created_at": "2022-09-13T19:54:06.000000Z",
    "updated_at": "2023-02-15T02:30:40.000000Z",
    "properties_count": 0
    },
    {
    "id": 4,
    "name": "Private Site",
    "short_name": "PS",
    "slug": "private-site",
    "quota": 50000,
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb167644314620230215063906.png",
    "image": null,
    "xml_url": "1c415SE30vRbVwqm",
    "token": null,
    "token_abu_dhabi": null,
    "token_commercial": null,
    "status": 1,
    "created_by_id": null,
    "updated_by_id": 1,
    "deleted_at": null,
    "created_at": "2022-09-13T19:54:06.000000Z",
    "updated_at": "2023-03-08T01:46:44.000000Z",
    "properties_count": 0
    },
    {
    "id": 5,
    "name": "Generic Portal",
    "short_name": "Generic",
    "slug": "generic-portal",
    "quota": 50000,
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb167647279620230215145316.png",
    "image": null,
    "xml_url": "JlRMxxZal4EOWjY7",
    "token": null,
    "token_abu_dhabi": null,
    "token_commercial": null,
    "status": 1,
    "created_by_id": null,
    "updated_by_id": 173,
    "deleted_at": null,
    "created_at": "2022-09-13T19:54:06.000000Z",
    "updated_at": "2023-02-15T10:53:16.000000Z",
    "properties_count": 0
    },
    {
    "id": 6,
    "name": "Right Move",
    "short_name": "RM",
    "slug": "right-move",
    "quota": 50000,
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb167931012820230320110208.png",
    "image": null,
    "xml_url": "MtWiWJZQLG1aUiT5",
    "token": null,
    "token_abu_dhabi": null,
    "token_commercial": null,
    "status": 1,
    "created_by_id": null,
    "updated_by_id": 173,
    "deleted_at": null,
    "created_at": "2022-09-13T19:54:06.000000Z",
    "updated_at": "2023-03-20T07:02:09.000000Z",
    "properties_count": 0
    },
    {
    "id": 7,
    "name": "James Edition",
    "short_name": "James Edition",
    "slug": "james-edition",
    "quota": 50000,
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb167931007520230320110115.png",
    "image": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/167930878620230320103946.png",
    "xml_url": "TnCHuVMHBS2SEFEf",
    "token": null,
    "token_abu_dhabi": null,
    "token_commercial": null,
    "status": 1,
    "created_by_id": 173,
    "updated_by_id": 173,
    "deleted_at": null,
    "created_at": "2023-03-20T06:39:46.000000Z",
    "updated_at": "2023-03-20T07:01:15.000000Z",
    "properties_count": 0
    },
    {
    "id": 9,
    "name": "Emirates.Estate",
    "short_name": "emirates-estate",
    "slug": "emiratesestate",
    "quota": 20000,
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb168552758720230531140627.png",
    "image": null,
    "xml_url": "eq4pWUyRAp5Vt0Fi",
    "token": null,
    "token_abu_dhabi": null,
    "token_commercial": null,
    "status": 1,
    "created_by_id": 173,
    "updated_by_id": null,
    "deleted_at": null,
    "created_at": "2023-05-31T10:06:28.000000Z",
    "updated_at": "2023-05-31T10:06:28.000000Z",
    "properties_count": 0
    },
    {
    "id": 10,
    "name": "Yandex",
    "short_name": "Yandex",
    "slug": "yandex",
    "quota": 10000,
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb168777343020230626135710.png",
    "image": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/168777319020230626135310.png",
    "xml_url": "KJ2yv092SPAPbXsP",
    "token": null,
    "token_abu_dhabi": null,
    "token_commercial": null,
    "status": 1,
    "created_by_id": 173,
    "updated_by_id": 173,
    "deleted_at": null,
    "created_at": "2023-06-26T09:53:10.000000Z",
    "updated_at": "2023-06-26T09:57:10.000000Z",
    "properties_count": 0
    }
    ],
    "propertyCount": 0,
    "propertyForRentCount": 0,
    "propertyForSaleCount": 0,
    "contactCount": 5,
    "leadCount": 81,
    "event_types": [
    {
    "id": 1,
    "name": "Viewing",
    "status": 1,
    "icon": "fa fa-eye",
    "badge": "bg-soft-success text-success",
    "thumbnail": null,
    "image": null,
    "order": 1,
    "created_by_id": null,
    "updated_by_id": 263,
    "created_at": "2023-05-25T10:11:33.000000Z",
    "updated_at": "2023-05-25T10:31:01.000000Z",
    "deleted_at": null
    },
    {
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
    },
    {
    "id": 8,
    "name": "Other",
    "status": 1,
    "icon": "fa fa-question",
    "badge": "bg-soft-primary text-primary",
    "thumbnail": null,
    "image": null,
    "order": 50,
    "created_by_id": null,
    "updated_by_id": 263,
    "created_at": "2023-05-25T10:11:33.000000Z",
    "updated_at": "2023-10-26T09:02:11.000000Z",
    "deleted_at": null
    },
    {
    "id": 9,
    "name": "Follow-up Reminder",
    "status": 1,
    "icon": "ri ri-calendar",
    "badge": "bg-soft-warning text-warning",
    "thumbnail": null,
    "image": null,
    "order": 3,
    "created_by_id": 263,
    "updated_by_id": null,
    "created_at": "2023-06-12T07:38:03.000000Z",
    "updated_at": "2023-06-12T07:38:03.000000Z",
    "deleted_at": null
    },
    {
    "id": 10,
    "name": "Property Shoot",
    "status": 1,
    "icon": "ri ri-camera",
    "badge": "bg-soft-success text-success",
    "thumbnail": null,
    "image": null,
    "order": 5,
    "created_by_id": 263,
    "updated_by_id": null,
    "created_at": "2023-06-20T05:22:06.000000Z",
    "updated_at": "2023-06-20T05:22:06.000000Z",
    "deleted_at": null
    },
    {
    "id": 11,
    "name": "Leave",
    "status": 1,
    "icon": "ri ri-calendar",
    "badge": "bg-soft-danger text-danger",
    "thumbnail": null,
    "image": null,
    "order": 10,
    "created_by_id": 263,
    "updated_by_id": null,
    "created_at": "2023-10-26T08:45:51.000000Z",
    "updated_at": "2023-10-26T08:45:51.000000Z",
    "deleted_at": null
    },
    {
    "id": 12,
    "name": "Vacation",
    "status": 1,
    "icon": "ri ri-calendar",
    "badge": "bg-soft-warning text-warning",
    "thumbnail": null,
    "image": null,
    "order": 10,
    "created_by_id": 263,
    "updated_by_id": null,
    "created_at": "2023-10-26T08:46:11.000000Z",
    "updated_at": "2023-10-26T08:46:11.000000Z",
    "deleted_at": null
    }
    ],
    "dealCount": 39
    },
    "page": 1,
    "per_page": 10
    }
     */
    public function dashboard(Request $request)
    {
        $userAppLogin = UserAppLogin::where("user_id", auth()->id())->where("app_version", ">=", "2.1")->latest()->first();
        if (is_null($userAppLogin)) {
            return $this->sendResponse(true, 'Action Required: Please install the latest version of the application.', null, 401);
        }

        $notAdmin = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }
        $portalWiseCounts = Portal::where('status', true)
            ->withCount(['properties' => function ($query) use ($notAdmin, $childrenIds) {
                $query->where('is_live', '1');
                $query->whereNot('completion_status', 'Off-Plan Primary');

                if ($notAdmin) {
                    $query->whereIn('agent_id', $childrenIds);
                }
            }])
            ->get(['id', 'name', 'thumbnail']);

        $portalWiseCounts = $portalWiseCounts;
        $propertyCount = Property::whereNot('completion_status', 'Off-Plan Primary')->where('is_live', '1')->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count();
        $propertyForRentCount = Property::whereNot('completion_status', 'Off-Plan Primary')->where('is_live', "1")->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count();
        $propertyForSaleCount = Property::whereNot('completion_status', 'Off-Plan Primary')->where('is_live', "1")->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count();
        $contactCount = Contact::where('status', 1)->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count();
        $leadCount = Lead::where(function ($q) {
            $q->whereHas('agent', function ($q) {
                $q->where('status', 1);
            })->orWhereNull('agent_id');
        })->where('status', 1)
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->whereNull('converted_to_listing_lead')
            ->where('is_archive', '0')
            ->count();
        $dealCount = Deal::when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count();
        $event_types = EventType::where('status', 1)->get();

        $data = [
            'portalWiseCounts' => $portalWiseCounts,
            'propertyCount' => $propertyCount,
            'propertyForRentCount' => $propertyForRentCount,
            'propertyForSaleCount' => $propertyForSaleCount,
            'contactCount' => $contactCount,
            'leadCount' => $leadCount,
            'event_types' => $event_types,
            'dealCount' => $dealCount,
        ];

        return $this->sendResponse(true, 'Dashboard Stats', $data, 200);
    }

    /**
     *
     *
     *
     * Listing Overview
     * @group Dashboard
     * @authenticated
     * @header Accept application/json
     * @bodyParam agent_id required Agent ID

    Example: abc
     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
    {
    "status": true,
    "code": 200,
    "message": "Dashboard Stats",
    "data": {
    "listing_overview": [
    {
    "publish_status": "PUBLISHED",
    "count": 1,
    "first_image": null,
    "category": null,
    "location": null,
    "my_property": false,
    "property_category": null,
    "community_property": null,
    "sub_community": null,
    "community": null
    },
    {
    "publish_status": "POCKET",
    "count": 2,
    "first_image": null,
    "category": null,
    "location": null,
    "my_property": false,
    "property_category": null,
    "community_property": null,
    "sub_community": null,
    "community": null
    },
    {
    "publish_status": "DRAFT",
    "count": 24,
    "first_image": null,
    "category": null,
    "location": null,
    "my_property": false,
    "property_category": null,
    "community_property": null,
    "sub_community": null,
    "community": null
    }
    ],
    "listing_published_in": {
    "counts": {
    "In last 7 days": 0,
    "In last 14 days": 0,
    "In last 30 days": 0,
    "Last 3 months": 0
    },
    "totalProperties": 1
    }
    },
    "page": 1,
    "per_page": 10
    }
     */

    public function dashboardOverView(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'agent_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(false, 'Validation Error', $validator->errors(), 422);
        }

        $agent_id = ($request->has('agent_id') && $request->agent_id != '') ? $request->agent_id : '';
        $data['listing_overview'] = $this->listingOverview($agent_id);
        $data['listing_published_in'] = $this->propertiesPublishedIn($agent_id);
        return $this->sendResponse(true, 'Dashboard Stats', $data, 200);
    }
    public function agentListingOverview()
    {
        //        if(Cache::has('dashboard_agents_overview')){
        //            return response()->json(['status' => '1', 'data' => Cache::get('dashboard_agents_overview'),'data_type'=>'cached']);
        //        }

        $data['users'] = $users = User::whereHas('roles', function ($q) {
            $q->where('name', 'Agent');
        })->where('email', '!=', 'dmitri.zolotco@axcapmail.com')->select('id', 'name')
            ->get();
        $agent_listings = [];
        foreach ($users as $user) {
            $agent_listings[$user->id]['name'] = $user->name;
            $agent_listings[$user->id]['for_residential_rent'] = Property::whereNot('completion_status', 'Off-Plan Primary')->where('agent_id', $user->id)->where(function ($q) {
                $q->where('for', 'Residential for rent')
                    ->orWhere('for', 'Commercial for rent');
            })->where('publish_status', 'PUBLISHED')->count();
            $agent_listings[$user->id]['for_commercial_rent'] = Property::whereNot('completion_status', 'Off-Plan Primary')->where('agent_id', $user->id)->where(function ($q) {
                $q->where('for', 'Commercial for rent');
            })->where('publish_status', 'PUBLISHED')->count();
            $agent_listings[$user->id]['for_residential_sale'] = Property::whereNot('completion_status', 'Off-Plan Primary')->where('agent_id', $user->id)->where('for', 'Residential for sale')->where('publish_status', 'PUBLISHED')->count();
            $agent_listings[$user->id]['for_commercial_sale'] = Property::whereNot('completion_status', 'Off-Plan Primary')->where('agent_id', $user->id)->where('for', 'Commercial for sale')->where('publish_status', 'PUBLISHED')->count();
        }
        $data['agent_listings'] = $agent_listings;
        //Cache::put('dashboard_agents_overview', $data, 30);
        return response()->json(['status' => '1', 'data' => $data, 'data_type' => 'fresh']);
    }
    public function listingOverview($agent_id)
    {
        if ($agent_id != '') {
            $user = User::find($agent_id);
        } else {
            $user = auth()->user();
        }

        $notAdmin = $user->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIdsOnly($user->id);
            $childrenIds[] = $user->id;
        }

        $statusCounts = Property::whereNot('completion_status', 'Off-Plan Primary')->select('publish_status', DB::raw('COUNT(*) as count'))
            ->whereIn('publish_status', ['PUBLISHED', 'POCKET', 'UNPUBLISHED', 'DRAFT', 'PENDING', 'REFUSED'])
            ->groupBy('publish_status')
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })
            ->get(['publish_status', DB::raw('COUNT(*) as count')]);
        return $statusCounts;
    }
    public function propertiesPublishedIn($agent_id)
    {
        if ($agent_id != '') {
            $user = User::find($agent_id);
        } else {
            $user = auth()->user();
        }
        $notAdmin = $user->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIdsOnly($user->id);
            $childrenIds[] = $user->id;
        }

        $counts = [];
        $today = Carbon::now();

        $intervals = [
            ['days', 7],
            ['days', 14],
            ['days', 30],
            ['months', 3],
        ];
        $totalProperties = Property::whereNot('completion_status', 'Off-Plan Primary')->where('publish_status', 'PUBLISHED')->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count();

        $now = Carbon::now();
        $sevenDaysAgo = Carbon::now()->subDays(7);
        $fourteenDaysAgo = Carbon::now()->subDays(14); // Resets to 14 days ago
        $thirtyDaysAgo = Carbon::now()->subDays(30); // Resets to 30 days ago
        $threeMonthsAgo = Carbon::now()->subMonths(3);

        $counts = [
            'In last 7 days' => Property::whereNot('completion_status', 'Off-Plan Primary')->where('published_date', '>=', $sevenDaysAgo)->where('publish_status', 'PUBLISHED')->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->count(),
            'In last 14 days' => Property::whereNot('completion_status', 'Off-Plan Primary')->whereBetween('published_date', [$fourteenDaysAgo, $now])->where('publish_status', 'PUBLISHED')->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->count(),
            'In last 30 days' => Property::whereNot('completion_status', 'Off-Plan Primary')->whereBetween('published_date', [$thirtyDaysAgo, $now])->where('publish_status', 'PUBLISHED')->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->count(),
            'Last 3 months' => Property::whereNot('completion_status', 'Off-Plan Primary')->where('published_date', '>=', $threeMonthsAgo)->where('publish_status', 'PUBLISHED')->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->count(),
        ];

        return [
            'counts' => $counts,
            'totalProperties' => $totalProperties,
        ];

    }

    /**
     *
     *
     *
     * Basic Dashboard Pie Stats
     * @group Dashboard
     * @authenticated
     * @header Accept application/json
    Example: abc
     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
    {
    "status": true,
    "code": 200,
    "message": "Pie Stats",
    "data": {
    "datasets": {
    "rent_portal_pie_chart": {
    "label": {
    "1": "Property Finder",
    "2": "Bayut",
    "3": "Dubizzle",
    "4": "Private Site",
    "5": "Generic Portal",
    "6": "Right Move",
    "7": "James Edition",
    "9": "Emirates.Estate",
    "10": "Yandex"
    },
    "data": [],
    "backgroundColor": [
    "rgba(255, 99, 132, 0.2)",
    "rgba(54, 162, 235, 0.2)",
    "rgba(255, 206, 86, 0.2)",
    "rgba(75, 192, 192, 0.2)",
    "rgba(153, 102, 255, 0.2)",
    "rgba(255, 159, 64, 0.2)"
    ],
    "borderColor": [
    "rgba(255, 99, 132, 1)",
    "rgba(54, 162, 235, 1)",
    "rgba(255, 206, 86, 1)",
    "rgba(75, 192, 192, 1)",
    "rgba(153, 102, 255, 1)",
    "rgba(255, 159, 64, 1)"
    ],
    "borderWidth": 1
    },
    "sale_portal_pie_chart": {
    "label": {
    "1": "Property Finder",
    "2": "Bayut",
    "3": "Dubizzle",
    "4": "Private Site",
    "5": "Generic Portal",
    "6": "Right Move",
    "7": "James Edition",
    "9": "Emirates.Estate",
    "10": "Yandex"
    },
    "data": [],
    "backgroundColor": [
    "rgba(255, 99, 132, 0.2)",
    "rgba(54, 162, 235, 0.2)",
    "rgba(255, 206, 86, 0.2)",
    "rgba(75, 192, 192, 0.2)",
    "rgba(153, 102, 255, 0.2)",
    "rgba(255, 159, 64, 0.2)"
    ],
    "borderColor": [
    "rgba(255, 99, 132, 1)",
    "rgba(54, 162, 235, 1)",
    "rgba(255, 206, 86, 1)",
    "rgba(75, 192, 192, 1)",
    "rgba(153, 102, 255, 1)",
    "rgba(255, 159, 64, 1)"
    ],
    "borderWidth": 1
    }
    }
    },
    "page": 1,
    "per_page": 10
    }
     */
    public function dashboardPieData(Request $request)
    {
        //abort_if(Gate::denies("Dashboard"), 401);
        $dates = [];

        $notAdmin = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        $portals = Portal::where('status', true)->pluck('name', 'id')->toArray();
        $portal_ids = array_keys($portals);
        //$portal_name = array_values($portals);
        //calculation for rent
        $rent_property = Property::whereNot('completion_status', 'Off-Plan Primary')->select('property_portals.portal_id', DB::raw('count(*) as total'))
            ->leftjoin('property_portals', 'properties.id', '=', 'property_portals.property_id')
            ->groupBy('property_portals.portal_id')
            ->whereIn('properties.for', ['Residential for rent', 'Commercial for rent'])
            ->where('properties.publish_status', 'PUBLISHED')
            ->whereIn('property_portals.portal_id', $portal_ids)
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->get();
        $rentValues = [];
        foreach ($rent_property as $key => $value) {
            $rentValues[$value->portal_id] = $value->total;
        }
        $sale_property = Property::whereNot('completion_status', 'Off-Plan Primary')->select('property_portals.portal_id', DB::raw('count(*) as total'))
            ->leftjoin('property_portals', 'properties.id', '=', 'property_portals.property_id')
            ->groupBy('property_portals.portal_id')
            ->whereIn('property_portals.portal_id', $portal_ids)
            ->where('properties.publish_status', 'PUBLISHED')
            ->whereIn('properties.for', ['Residential for sale', 'Commercial for sale'])
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })
            ->when(!empty($dates), function ($q) use ($dates) {
                $q->whereDate('properties.created_at', '>=', $dates['startDate'])->whereDate('properties.created_at', '<=', $dates['endDate']);
            })->get();

        $saleValues = [];
        foreach ($sale_property as $key => $value) {
            $saleValues[$value->portal_id] = $value->total;
        }

        $data = [

            "datasets" => ["rent_portal_pie_chart" =>
                [
                    "label" => $portals,
                    "data" => $rentValues,
                    "backgroundColor" => [
                        "rgba(255, 99, 132, 0.2)",
                        "rgba(54, 162, 235, 0.2)",
                        "rgba(255, 206, 86, 0.2)",
                        "rgba(75, 192, 192, 0.2)",
                        "rgba(153, 102, 255, 0.2)",
                        "rgba(255, 159, 64, 0.2)",
                    ],
                    "borderColor" => [
                        "rgba(255, 99, 132, 1)",
                        "rgba(54, 162, 235, 1)",
                        "rgba(255, 206, 86, 1)",
                        "rgba(75, 192, 192, 1)",
                        "rgba(153, 102, 255, 1)",
                        "rgba(255, 159, 64, 1)",
                    ],
                    "borderWidth" => 1,
                ],
                "sale_portal_pie_chart" =>
                [
                    "label" => $portals,
                    "data" => $saleValues,
                    "backgroundColor" => [
                        "rgba(255, 99, 132, 0.2)",
                        "rgba(54, 162, 235, 0.2)",
                        "rgba(255, 206, 86, 0.2)",
                        "rgba(75, 192, 192, 0.2)",
                        "rgba(153, 102, 255, 0.2)",
                        "rgba(255, 159, 64, 0.2)",
                    ],
                    "borderColor" => [
                        "rgba(255, 99, 132, 1)",
                        "rgba(54, 162, 235, 1)",
                        "rgba(255, 206, 86, 1)",
                        "rgba(75, 192, 192, 1)",
                        "rgba(153, 102, 255, 1)",
                        "rgba(255, 159, 64, 1)",
                    ],
                    "borderWidth" => 1,
                ],
            ],
        ];
        return $this->sendResponse(true, 'Pie Stats', $data, 200);
    }
    /**
     *
     *
     *
     * Basic Dashboard Property Finder Stats
     * @group Dashboard
     * @authenticated
     * @header Accept application/json
    Example: abc
     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
    {
    "status": true,
    "code": 200,
    "message": "Property Finder Stats",
    "data": {
    "pf_rank": {
    "40-50": 0,
    "50-60": 0,
    "60-70": 0,
    "70-80": 0,
    "80-90": 0,
    "90-100": 0,
    "100": 0
    },
    "online": 0,
    "offline": 0,
    "avg_pf_rank": 0,
    "pf_verified": 0,
    "portal_count": 0,
    "level": {
    "Standard": 0,
    "Featured": 0,
    "Premium": 0
    },
    "whatsapp_avg_res": 0
    },
    "page": 1,
    "per_page": 10
    }
     */
    public function propertyFinderData(Request $request)
    {
        $rankThresholds = [40, 50, 60, 70, 80, 90, 100];
        $pf_rank = [];
        $dates = [];
        $portal = Portal::where('name', 'Property Finder')->first();
        $source = Source::where('name', 'Property Finder')->first();
        $user_id = ($request->has('user_id') && $request->user_id != '') ? $request->user_id : '';
        if ($user_id != '') {
            $user = User::find($user_id);
            $dates = $request->dates;
        } else {
            $user = auth()->user();
        }

        $notAdmin = $user->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];

        if ($notAdmin) {
            $user_id = ($request->has('user_id') && $request->user_id != '') ? $request->user_id : auth()->id();
            $childrenIds = getUserChildrenIdsOnly($user_id);
            $childrenIds[] = $user_id;

        }

        foreach ($rankThresholds as $threshold) {
            $upper = $threshold + 10;
            $count = Property::whereNot('completion_status', 'Off-Plan Primary')
                ->where('region_id', 1)
                ->whereHas('propertyMeta', function ($query) use ($threshold, $upper) {
                    if ($threshold == 100) {
                        $query->where('meta_key', 'pf_rank')
                            ->where('meta_value', '=', $threshold);
                    } else {
                        $query->where('meta_key', 'pf_rank')
                            ->where('meta_value', '>', $threshold)->where('meta_value', '<', $upper);
                    }

                })->whereHas('portals', function ($q) use ($portal) {
                $q->where('portal_id', $portal->id);
            })->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->when(!empty($dates), function ($q) use ($dates) {
                $q->whereDate('properties.created_at', '>=', $dates['startDate'])->whereDate('properties.created_at', '<=', $dates['endDate']);
            })->where('is_live', '1')->count();
            if ($threshold == 100) {
                $pf_rank[$threshold] = $count;
            } else {
                $pf_rank[$threshold . '-' . $upper] = $count;
            }

        }
        $data['pf_rank'] = $pf_rank;
        $verified_count = Property::whereNot('completion_status', 'Off-Plan Primary')
            ->whereHas('propertyMeta', function ($query) {
                $query->where('meta_key', 'pf_verified')
                    ->where('meta_value', '1');
            })->whereHas('portals', function ($q) use ($portal) {
            $q->where('portal_id', $portal->id);
        })->where('is_live', '1')->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->when(!empty($dates), function ($q) use ($dates) {
            $q->whereDate('properties.created_at', '>=', $dates['startDate'])->whereDate('properties.created_at', '<=', $dates['endDate']);
        })->count();

        $data['online'] = Property::whereNot('completion_status', 'Off-Plan Primary')
            ->where('region_id', 1)
            ->whereHas('propertyMeta', function ($query) {
                $query->where('meta_key', 'pf_status')
                    ->where('meta_value', 'Online');
            })->whereHas('portals', function ($q) use ($portal) {
            $q->where('portal_id', $portal->id);
        })->where('is_live', '1')->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->when(!empty($dates), function ($q) use ($dates) {
            $q->whereDate('properties.created_at', '>=', $dates['startDate'])->whereDate('properties.created_at', '<=', $dates['endDate']);
        })->count();
        $data['offline'] = Property::whereNot('completion_status', 'Off-Plan Primary')
            ->where('region_id', 1)
            ->whereHas('propertyMeta', function ($query) {
                $query->where('meta_key', 'pf_status')
                    ->where('meta_value', 'Offline');
            })->whereHas('portals', function ($q) use ($portal) {
            $q->where('portal_id', $portal->id);
        })->where('is_live', '1')->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->when(!empty($dates), function ($q) use ($dates) {
            $q->whereDate('properties.created_at', '>=', $dates['startDate'])->whereDate('properties.created_at', '<=', $dates['endDate']);
        })->count();

        //calculate average of pf_rank
        $pf_avg_rank = Property::whereNot('completion_status', 'Off-Plan Primary')
            ->where('region_id', 1)
            ->join('property_meta', function ($join) {
                $join->on('properties.id', '=', 'property_meta.property_id')
                    ->where('property_meta.meta_key', '=', 'pf_rank');
            })->whereHas('portals', function ($q) use ($portal) {
            $q->where('portal_id', $portal->id);
        })->where('is_live', '1')->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->when(!empty($dates), function ($q) use ($dates) {
            $q->whereDate('properties.created_at', '>=', $dates['startDate'])->whereDate('properties.created_at', '<=', $dates['endDate']);
        })->avg('property_meta.meta_value');
        $data['avg_pf_rank'] = round($pf_avg_rank);

        $data['pf_verified'] = $verified_count;
        $data['portal_count'] = Property::whereNot('completion_status', 'Off-Plan Primary')
            ->where('region_id', 1)
            ->whereHas('portals', function ($q) use ($portal) {
                $q->where('portal_id', $portal->id);
            })->where('is_live', '1')
            ->when(!empty($dates), function ($q) use ($dates) {
                $q->whereDate('properties.created_at', '>=', $dates['startDate'])->whereDate('properties.created_at', '<=', $dates['endDate']);
            })
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->count();

        $levels = ['Standard', 'Featured', 'Premium'];
        $pf_level = [];
        $total_count = 0;
        foreach ($levels as $level) {
            $count = Property::whereNot('completion_status', 'Off-Plan Primary')
                ->where('region_id', 1)
                ->whereHas('propertyMeta', function ($query) use ($level) {
                    $query->where('meta_key', 'pf_level')
                        ->where('meta_value', $level);
                })->whereHas('portals', function ($q) use ($portal) {
                $q->where('portal_id', $portal->id);
            })->where('is_live', '1')
                ->when($notAdmin, function ($q) use ($childrenIds) {
                    $q->whereIn("agent_id", $childrenIds);
                })->when(!empty($dates), function ($q) use ($dates) {
                $q->whereDate('properties.created_at', '>=', $dates['startDate'])->whereDate('properties.created_at', '<=', $dates['endDate']);
            })->count();
            $pf_level[$level] = $count;
            $total_count += $count;
            // $pf_level[$level] = PropertyMeta::where('meta_key', 'pf_level')->where('meta_value', $level)->count();

        }
        $pf_level['Standard'] = $data['portal_count'] - $total_count + $pf_level['Standard'] ?? 0;

        $data['level'] = $pf_level;
        //        $averageDifference = Shortlink::where('source_id', $source->id)->whereNotNull('opted_in')
        //            ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, created_at, opted_in)) AS average_difference')
        //            ->get();
        $averageDifference = Lead::whereIn('agent_id', $childrenIds)
            ->join('shortlinks', 'leads.id', '=', 'shortlinks.lead_id')
            ->whereNotNull('shortlinks.opted_in')
            ->where('shortlinks.source_id', $source->id)
            ->select(DB::raw('AVG(TIMESTAMPDIFF(SECOND, shortlinks.created_at, shortlinks.opted_in)) as average_diff'))
            ->value('average_diff');

        $data['whatsapp_avg_res'] = $averageDifference ?? 0;

        return $this->sendResponse(true, 'Property Finder Stats', $data, 200);
    }
    /**
     *
     *
     *
     * Basic Dashboard Bayut Stats
     * @group Dashboard
     * @authenticated
     * @header Accept application/json
    Example: abc
     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
    {
    "status": true,
    "code": 200,
    "message": "Bayut Stats",
    "data": {
    "ranks": {
    "40-50": 0,
    "50-60": 0,
    "60-70": 0,
    "70-80": 0,
    "80-90": 0,
    "90-100": 0,
    "100": 0
    },
    "trucheck": 0,
    "checked": 0,
    "avg_rank": 0,
    "verified": 0,
    "portal_count": 0,
    "level": {
    "Standard": 0,
    "Signature": 0,
    "Hot": 0
    }
    },
    "page": 1,
    "per_page": 10
    }
     */
    public function bayutData(Request $request)
    {
        $rankThresholds = [40, 50, 60, 70, 80, 90, 100];
        $pf_rank = [];
        $dates = [];
        $portal = Portal::where('name', 'Bayut')->first();
        $user_id = ($request->has('user_id') && $request->user_id != '') ? $request->user_id : auth()->id();
        if ($user_id != '') {
            $user = User::find($user_id);
            $dates = $request->dates;
        } else {
            $user = auth()->user();
        }

        $notAdmin = $user->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {

            // $childrenIds = getUserChildrenIds($user_id);
            $childrenIds = getUserChildrenIdsOnly($user_id);
            $childrenIds[] = $user_id;
        }

        foreach ($rankThresholds as $threshold) {
            $upper = $threshold + 10;
            $count = Property::whereNot('completion_status', 'Off-Plan Primary')->whereHas('propertyMeta', function ($query) use ($threshold, $upper) {
                if ($threshold == 100) {
                    $query->where('meta_key', 'bayut_rank')
                        ->where('meta_value', '=', $threshold);
                } else {
                    $query->where('meta_key', 'bayut_rank')
                        ->where('meta_value', '=>', $threshold)->where('meta_value', '<=', $upper);
                }

            })->whereHas('portals', function ($q) use ($portal) {
                $q->where('portal_id', $portal->id);
            })->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->when(!empty($dates), function ($q) use ($dates) {
                $q->whereDate('properties.created_at', '>=', $dates['startDate'])->whereDate('properties.created_at', '<=', $dates['endDate']);
            })->where('is_live', '1')->count();
            if ($threshold == 100) {
                $pf_rank[$threshold] = $count;
            } else {
                $pf_rank[$threshold . '-' . $upper] = $count;
            }

        }
        $data['ranks'] = $pf_rank;
        $verified_count = Property::whereNot('completion_status', 'Off-Plan Primary')->whereHas('propertyMeta', function ($query) {
            $query->where('meta_key', 'bayut_review_status')
                ->where('meta_value', 'Approved');
        })->whereHas('portals', function ($q) use ($portal) {
            $q->where('portal_id', $portal->id);
        })->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->when(!empty($dates), function ($q) use ($dates) {
            $q->whereDate('properties.created_at', '>=', $dates['startDate'])->whereDate('properties.created_at', '<=', $dates['endDate']);
        })->where('is_live', '1')->count();

        $data['trucheck'] = Property::whereNot('completion_status', 'Off-Plan Primary')->whereHas('propertyMeta', function ($query) {
            $query->where('meta_key', 'bayut_trucheck_status')
                ->where('meta_value', 'Truchecked');
        })->whereHas('portals', function ($q) use ($portal) {
            $q->where('portal_id', $portal->id);
        })->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->when(!empty($dates), function ($q) use ($dates) {
            $q->whereDate('properties.created_at', '>=', $dates['startDate'])->whereDate('properties.created_at', '<=', $dates['endDate']);
        })->where('is_live', '1')->count() ?? 0;

        $data['checked'] = Property::whereNot('completion_status', 'Off-Plan Primary')->whereHas('propertyMeta', function ($query) {
            $query->where('meta_key', 'bayut_trucheck_status')
                ->where('meta_value', 'checked');
        })->whereHas('portals', function ($q) use ($portal) {
            $q->where('portal_id', $portal->id);
        })->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->when(!empty($dates), function ($q) use ($dates) {
            $q->whereDate('properties.created_at', '>=', $dates['startDate'])->whereDate('properties.created_at', '<=', $dates['endDate']);
        })->where('is_live', '1')->count() ?? 0;

        //calculate average of pf_rank
        $avg_rank = Property::whereNot('completion_status', 'Off-Plan Primary')->join('property_meta', function ($join) {
            $join->on('properties.id', '=', 'property_meta.property_id')
                ->where('property_meta.meta_key', '=', 'bayut_rank');
        })->whereHas('portals', function ($q) use ($portal) {
            $q->where('portal_id', $portal->id);
        })->where('is_live', '1')->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->when(!empty($dates), function ($q) use ($dates) {
            $q->whereDate('properties.created_at', '>=', $dates['startDate'])->whereDate('properties.created_at', '<=', $dates['endDate']);
        })->avg('property_meta.meta_value');
        $data['avg_rank'] = round($avg_rank);

        $data['verified'] = $verified_count;

        $data['portal_count'] = Property::whereNot('completion_status', 'Off-Plan Primary')->whereHas('portals', function ($q) use ($portal) {
            $q->where('portal_id', $portal->id);
        })->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->when(!empty($dates), function ($q) use ($dates) {
            $q->whereDate('properties.created_at', '>=', $dates['startDate'])->whereDate('properties.created_at', '<=', $dates['endDate']);
        })->where('is_live', '1')->count();

        $levels = ['Standard', 'Signature', 'Hot'];
        $rank = [];
        $total_count = 0;
        foreach ($levels as $level) {
            $count = Property::whereNot('completion_status', 'Off-Plan Primary')->whereHas('propertyMeta', function ($query) use ($level) {
                $query->where('meta_key', 'bayut_level')
                    ->where('meta_value', $level);
            })->whereHas('portals', function ($q) use ($portal) {
                $q->where('portal_id', $portal->id);
            })->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->when(!empty($dates), function ($q) use ($dates) {
                $q->whereDate('properties.created_at', '>=', $dates['startDate'])->whereDate('properties.created_at', '<=', $dates['endDate']);
            })->where('is_live', '1')->count();
            $rank[$level] = $count;
            $total_count += $count;
            // $pf_level[$level] = PropertyMeta::where('meta_key', 'pf_level')->where('meta_value', $level)->count();

        }
        $rank['Standard'] = $data['portal_count'] - $total_count ?? 0; //because we don't have data for bayut_level = Standard

        $data['level'] = $rank;

        return $this->sendResponse(true, 'Bayut Stats', $data, 200);
    }
    /**
     *
     *
     *
     * Basic Dashboard Dubizzle Stats
     * @group Dashboard
     * @authenticated
     * @header Accept application/json
    Example: abc
     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
    {
    "status": true,
    "code": 200,
    "message": "Dubizzle Stats",
    "data": {
    "ranks": {
    "40-50": 0,
    "50-60": 0,
    "60-70": 0,
    "70-80": 0,
    "80-90": 0,
    "90-100": 0,
    "100": 0
    },
    "trucheck": 0,
    "checked": 0,
    "avg_rank": 0,
    "portal_count": 0,
    "level": {
    "Premium": 0,
    "Featured": 0,
    "Standard": 0
    }
    },
    "page": 1,
    "per_page": 10
    }
     */
    public function dubbizleData(Request $request)
    {
        $rankThresholds = [40, 50, 60, 70, 80, 90, 100];
        $rank = [];
        $dates = [];
        $portal = Portal::where('name', 'Dubizzle')->first();
        $user_id = ($request->has('user_id') && $request->user_id != '') ? $request->user_id : auth()->id();
        if ($user_id != '') {
            $user = User::find($user_id);
            $dates = $request->dates;
        } else {
            $user = auth()->user();
        }
        $notAdmin = $user->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            //$childrenIds = getUserChildrenIds($user_id);
            $childrenIds = getUserChildrenIdsOnly($user_id);
            $childrenIds[] = $user_id;
        }
        foreach ($rankThresholds as $threshold) {
            $upper = $threshold + 10;
            $count = Property::whereNot('completion_status', 'Off-Plan Primary')->whereHas('propertyMeta', function ($query) use ($threshold, $upper) {
                if ($threshold == 100) {
                    $query->where('meta_key', 'dubizzle_rank')
                        ->where('meta_value', '=', $threshold);
                } else {
                    $query->where('meta_key', 'dubizzle_rank')
                        ->where('meta_value', '>', $threshold)->where('meta_value', '<', $upper);
                }

            })->whereHas('portals', function ($q) use ($portal) {
                $q->where('portal_id', $portal->id);
            })->where('is_live', '1')->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->count();
            if ($threshold == 100) {
                $rank[$threshold] = $count;
            } else {
                $rank[$threshold . '-' . $upper] = $count;
            }

        }
        $data['ranks'] = $rank;
        //        $verified_count = Property::whereHas('propertyMeta', function ($query) {
        //            $query->where('meta_key', 'bayut_review_status')
        //                ->where('meta_value', 'Approved');
        //        })->whereHas('portals',function ($q) use($portal){
        //            $q->where('portal_id',$portal->id);
        //        })->where('publish_status','PUBLISHED')->when($notAdmin, function ($q) use ($childrenIds) {
        //            $q->whereIn("agent_id", $childrenIds);
        //        })->count();

        $data['trucheck'] = Property::whereNot('completion_status', 'Off-Plan Primary')->whereHas('propertyMeta', function ($query) {
            $query->where('meta_key', 'dubizzle_trucheck_status')
                ->where('meta_value', 'checked');
        })->whereHas('portals', function ($q) use ($portal) {
            $q->where('portal_id', $portal->id);
        })->where('is_live', '1')->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count() ?? 0;

        $data['trucheck'] = Property::whereNot('completion_status', 'Off-Plan Primary')->whereHas('propertyMeta', function ($query) {
            $query->where('meta_key', 'dubizzle_trucheck_status')
                ->where('meta_value', 'Truchecked');
        })->whereHas('portals', function ($q) use ($portal) {
            $q->where('portal_id', $portal->id);
        })->where('is_live', '1')->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count() ?? 0;

        $data['checked'] = Property::whereNot('completion_status', 'Off-Plan Primary')->whereHas('propertyMeta', function ($query) {
            $query->where('meta_key', 'dubizzle_trucheck_status')
                ->where('meta_value', 'checked');
        })->whereHas('portals', function ($q) use ($portal) {
            $q->where('portal_id', $portal->id);
        })->where('is_live', '1')->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count() ?? 0;

        //calculate average of rank
        $avg_rank = Property::whereNot('completion_status', 'Off-Plan Primary')->join('property_meta', function ($join) {
            $join->on('properties.id', '=', 'property_meta.property_id')
                ->where('property_meta.meta_key', '=', 'dubizzle_rank');
        })->whereHas('portals', function ($q) use ($portal) {
            $q->where('portal_id', $portal->id);
        })->where('is_live', '1')->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->avg('property_meta.meta_value');
        $data['avg_rank'] = round($avg_rank);

        //  $data['verified'] =  $verified_count;
        $data['portal_count'] = Property::whereNot('completion_status', 'Off-Plan Primary')->whereHas('portals', function ($q) use ($portal) {
            $q->where('portal_id', $portal->id);
        })->where('is_live', '1')->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count();
        $levels = ['Premium', 'Featured', 'Standard'];
        $rank = [];
        $total_count = 0;
        foreach ($levels as $level) {
            $count = Property::whereNot('completion_status', 'Off-Plan Primary')->whereHas('propertyMeta', function ($query) use ($level) {
                $query->where('meta_key', 'dubizzle_level')
                    ->where('meta_value', $level);
            })->whereHas('portals', function ($q) use ($portal) {
                $q->where('portal_id', $portal->id);
            })->where('is_live', '1')->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->count();
            $rank[$level] = $count;
            $total_count += $count;
            // $pf_level[$level] = PropertyMeta::where('meta_key', 'pf_level')->where('meta_value', $level)->count();

        }
        $rank['Standard'] = $data['portal_count'] - $total_count + $rank['Standard'] ?? 0; //because we don't have data for bayut_level = Standard

        $data['level'] = $rank;
        return $this->sendResponse(true, 'Dubizzle Stats', $data, 200);
    }

    /**
     *
     *
     *
     * Leads Overview
     * @group Dashboard
     * @authenticated
     * @header Accept application/json
     * @bodyParam agent_id required Agent ID

    Example: abc
     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
    {
    "status": "1",
    "data": {
    "lead_by_status": {
    "New": 3180,
    "Unspecified": 431,
    "Contacted": 93655,
    "Viewing Arranged": 2324,
    "Qualified": 2216,
    "Successful": 579,
    "Not Answered": 15488,
    "Unsuccessful": 6696
    },
    "lead_by_source": {
    "Agent": 16,
    "Agent External": 82,
    "Agent Internal": 32,
    "Bayut": 27510,
    "Business Card": 3,
    "Client referral": 467,
    "Cold Call": 10,
    "Database": 14,
    "Developer": 1,
    "Direct Call": 9,
    "Direct Client": 34,
    "Dubizzle": 11290,
    "Email campaign": 9,
    "Existing client": 3,
    "Facebook": 1428,
    "Friend or Relative": 29,
    "Instagram": 951,
    "JamesEdition": 13,
    "Open House": 1,
    "Personal Referral": 63,
    "Property Finder": 40980,
    "Referral": 10,
    "Roadshow": 106,
    "Search Engine": 1,
    "Walk-in": 2,
    "Website": 10,
    "Whatsapp": 1053,
    "Word of Mouth": 3,
    "Youtube": 2,
    "Other": 122,
    "Online": 2,
    "Zoom Property": 12,
    "Unspecified": 5482,
    "CRM": 6,
    "Bitrix-unspecified": 3347,
    "AX CAPITAL": 6781,
    "Chat2Desk": 1,
    "Live chat": 188,
    "Listing Portal": 1229,
    "Emirates.Estate": 9,
    "AX Corporate": 2,
    "LinkedIn": 1,
    "Emaar Stand": 1,
    "Houza": 62,
    "Telesales": 5224,
    "Front Desk Admin": 6,
    "AX CONTACTS": 5,
    "Yandex": 5
    },
    "last6month": {
    "months": [
    "February",
    "March",
    "April",
    "May",
    "June",
    "July"
    ],
    "newLeads": [
    13463,
    12805,
    10496,
    11671,
    9516,
    426
    ],
    "successful": [
    12,
    21,
    16,
    12,
    12,
    0
    ],
    "unsuccessful": [
    449,
    544,
    431,
    427,
    322,
    3
    ]
    }
    },
    "data_type": "fresh"
    }
     */

    public function getLeads(Request $request)
    {
        $agent_id = ($request->has('agent_id') && $request->agent_id != '') ?? '';
        if ($agent_id != '') {
            $user = User::find($agent_id);
        } else {
            $user = auth()->user();
        }
        if (Cache::has('dashboard_lead_data' . $user->id)) {
            return response()->json(['status' => '1', 'data' => Cache::get('dashboard_lead_data' . $user->id), 'data_type' => 'cached']);
        }
        $notAdmin = $user->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds($user->id);
        }
        $statuses = LeadStatus::where('status', '1')->get();
        $sources = Source::where('status', '1')->where('is_lead_source', '1')->get();
        $leads = [];
        $lead_source = [];
        $lead_status = [];

        foreach ($statuses as $status) {
            $lead_status[$status->name] = Lead::where('lead_status_id', $status->id)
                ->when($notAdmin, function ($q) use ($childrenIds) {
                    $q->whereIn("agent_id", $childrenIds);
                })->where(function ($q) {
                $q->whereHas('agent', function ($q) {
                    $q->where('status', 1);
                })->orWhereNull('agent_id');
            })->whereNull('converted_to_listing_lead')
                ->where('is_archive', '0')->where('status', '1')->count();
        }

        foreach ($sources as $source) {
            $count = Lead::where('source_id', $source->id)->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->where(function ($q) {
                $q->whereHas('agent', function ($q) {
                    $q->where('status', 1);
                })->orWhereNull('agent_id');
            })->where('status', 1)->whereNull('converted_to_listing_lead')
                ->where('is_archive', '0')->whereNotNull('agent_id')->count();
            if ($count > 0) {
                $lead_source[$source->name] = $count;
            }
        }
        $leads['lead_by_status'] = $lead_status;
        $leads['lead_by_source'] = $lead_source;
        $month = [];
        $leads6month = [];
        $newLeads = [];
        $successfulleads = [];
        $unsuccessfulleads = [];
        $startMonth = date('F', strtotime("-5 month"));

        $startMonth = date('Y-m-01'); // Start from the first day of the current month
        $leads6month = ['months' => [], 'newLeads' => [], 'successful' => [], 'unsuccessful' => []];
        // dd($childrenIds);
        for ($i = 0; $i < 6; $i++) {
            $targetMonth = date('Y-m-01', strtotime("-$i months", strtotime($startMonth)));

            $leads6month['months'][] = date('F', strtotime($targetMonth));

            $leads6month['newLeads'][] = Lead::whereYear('created_at', '=', date('Y', strtotime($targetMonth)))
                ->whereMonth('created_at', '=', date('m', strtotime($targetMonth)))
                ->when($notAdmin, function ($q) use ($childrenIds) {
                    $q->whereIn("agent_id", $childrenIds);
                })
                ->where('is_archive', '0')
                ->where('status', 1)
                ->count();

            $leads6month['successful'][] = Lead::whereYear('created_at', '=', date('Y', strtotime($targetMonth)))
                ->whereMonth('created_at', '=', date('m', strtotime($targetMonth)))
                ->where('lead_status_id', 9)
                ->when($notAdmin, function ($q) use ($childrenIds) {
                    $q->whereIn("agent_id", $childrenIds);
                })
                ->where(function ($q) {
                    $q->whereHas('agent', function ($q) {
                        $q->where('status', 1);
                    })->orWhereNull('agent_id');
                })
                ->where('status', 1)
                ->whereNull('converted_to_listing_lead')
                ->where('is_archive', '0')
                ->whereNotNull('agent_id')
                ->count();

            $leads6month['unsuccessful'][] = Lead::whereYear('created_at', '=', date('Y', strtotime($targetMonth)))
                ->whereMonth('created_at', '=', date('m', strtotime($targetMonth)))
                ->where('lead_status_id', 12)
                ->when($notAdmin, function ($q) use ($childrenIds) {
                    $q->whereIn("agent_id", $childrenIds);
                })
                ->where(function ($q) {
                    $q->whereHas('agent', function ($q) {
                        $q->where('status', 1);
                    })->orWhereNull('agent_id');
                })
                ->where('status', 1)
                ->whereNull('converted_to_listing_lead')
                ->where('is_archive', '0')
                ->whereNotNull('agent_id')
                ->count();
        }
        // reverse the array
        $leads6month['months'] = array_reverse($leads6month['months']);
        $leads6month['newLeads'] = array_reverse($leads6month['newLeads']);
        $leads6month['successful'] = array_reverse($leads6month['successful']);
        $leads6month['unsuccessful'] = array_reverse($leads6month['unsuccessful']);

        $leads['last6month'] = $leads6month;

        // dd($leads);

        if ($request->agent_id != '') {
            $user = User::find($request->agent_id);
        } else {
            $user = auth()->user();
        }
        $notAdmin = $user->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIdsOnly($user->id);
            $childrenIds[] = $user->id;
        }


        $totalLeads = Lead::when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count();

        $now = Carbon::now();
        $sevenDaysAgo = Carbon::now()->subDays(7);
        $fourteenDaysAgo = Carbon::now()->subDays(14); // Resets to 14 days ago
        $thirtyDaysAgo = Carbon::now()->subDays(30); // Resets to 30 days ago
        $threeMonthsAgo = Carbon::now()->subMonths(3);


        $counts = [
            'In last 7 days' => Lead::where('created_at', '>=', $sevenDaysAgo)->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->count(),
            'In last 14 days' => Lead::whereBetween('created_at', [$fourteenDaysAgo, $now])->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->count(),
            'In last 30 days' => Lead::whereBetween('created_at', [$thirtyDaysAgo, $now])->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->count(),
            'Last 3 months' => Lead::where('created_at', '>=', $threeMonthsAgo)->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->count(),
        ];
        $leads['variations'] = [
            'counts' => $counts,
            'totalLeads' => $totalLeads
        ];

        Cache::put('dashboard_lead_data' . auth()->id(), $leads, 30);
        return response()->json(['status' => '1', 'data' => $leads, 'data_type' => 'fresh']);
    }



    public function recentLeads($agent_id = '')
    {
        if ($agent_id != '') {
            $user = User::find($agent_id);
        } else {
            $user = auth()->user();
        }
        $notAdmin = $user->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIdsOnly($user->id);
            $childrenIds[] = $user->id;
        }


        $totalLeads = Lead::when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count();

        $now = Carbon::now();
        $sevenDaysAgo = Carbon::now()->subDays(7);
        $fourteenDaysAgo = Carbon::now()->subDays(14); // Resets to 14 days ago
        $thirtyDaysAgo = Carbon::now()->subDays(30); // Resets to 30 days ago
        $threeMonthsAgo = Carbon::now()->subMonths(3);


        $counts = [
            'In last 7 days' => Lead::where('created_at', '>=', $sevenDaysAgo)->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->count(),
            'In last 14 days' => Lead::whereBetween('created_at', [$fourteenDaysAgo, $now])->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->count(),
            'In last 30 days' => Lead::whereBetween('created_at', [$thirtyDaysAgo, $now])->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->count(),
            'Last 3 months' => Lead::where('created_at', '>=', $threeMonthsAgo)->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->count(),
        ];
        $data = [
            'counts' => $counts,
            'totalLeads' => $totalLeads
        ];
        return response()->json(['status' => '1', 'data' => $data]);


    }

    /**
     *
     *
     *
     * Listing Leads Overview
     * @group Dashboard
     * @authenticated
     * @header Accept application/json
     * @bodyParam agent_id required Agent ID
    Example: abc
     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
    {
    "status": true,
    "code": 200,
    "message": "Listing Leads Stats",
    "data": {
    "counts": {
    "In last 7 days": 0,
    "In last 14 days": 0,
    "In last 30 days": 6,
    "Last 3 months": 21
    },
    "totalLeads": 27
    },
    "page": 1,
    "per_page": 10
    }
     */
    public function recentListingLeads(Request $request)
    {
        $agent_id = $request->agent_id ?? '';
        if ($agent_id != '') {
            $user = User::find($agent_id);
        } else {
            $user = auth()->user();
        }
        $notAdmin = $user->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIdsOnly($user->id);
            $childrenIds[] = $user->id;
        }

        $totalLeads = ListingLead::when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count();

        $now = Carbon::now();
        $sevenDaysAgo = Carbon::now()->subDays(7);
        $fourteenDaysAgo = Carbon::now()->subDays(14); // Resets to 14 days ago
        $thirtyDaysAgo = Carbon::now()->subDays(30); // Resets to 30 days ago
        $threeMonthsAgo = Carbon::now()->subMonths(3);

        $counts = [
            'In last 7 days' => ListingLead::where('created_at', '>=', $sevenDaysAgo)->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->count(),
            'In last 14 days' => ListingLead::whereBetween('created_at', [$fourteenDaysAgo, $now])->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->count(),
            'In last 30 days' => ListingLead::whereBetween('created_at', [$thirtyDaysAgo, $now])->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->count(),
            'Last 3 months' => ListingLead::where('created_at', '>=', $threeMonthsAgo)->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->count(),
        ];
        $data = [
            'counts' => $counts,
            'totalLeads' => $totalLeads,
        ];

        // return response()->json(['status' => '1', 'data' => $data]);

        return $this->sendResponse(true, 'Listing Leads Stats', $data, 200);

    }




}
