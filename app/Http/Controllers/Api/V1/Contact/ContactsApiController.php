<?php

namespace App\Http\Controllers\Api\V1\Contact;

use App\Http\Controllers\Admin\Contact\ContactController;
use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use App\Models\ContactStatus;
use App\Models\Source;
use App\Models\Type;
use Illuminate\Http\Request;
use Validator;

class ContactsApiController extends BaseController
{

/**
 *
 *
 * Contacts List
 *
 * Get a list of Contacts.
 * @group Contacts
 * @authenticated
 * @queryParam email email for Filtration based on email . Example: abc@axcapital.ae
 * @queryParam first_name first_name for Filtration based on First Name. Example: abc
 * @queryParam last_name last_name for Filtration based on Last Name. Example: abc
 * @queryParam agent_id agent_id for Filtration based on Agent.
 * @queryParam mobile mobile for Filtration based on Contact Mobile.
 * @queryParam type type for Filtration based on Contact type.
 * @queryParam source source for Filtration based on Contact source.
 * @queryParam gender gender for Filtration based on Contact gender.
 * @queryParam dob dob for Filtration based on Contact dob.
 * @queryParam created_at created_at for Filtration based on Contact created_at.
 * @queryParam updated_at updated_at for Filtration based on Contact updated_at.
 * @queryParam bitrix_id bitrix_id for Filtration based on Bitrix Id.
 * @queryParam deleted deleted for fetching deleted contacts. Example: true
 * @queryParam key key  for Filtration based on certain column. Example: first_name
 * @queryParam key_value key_value  for Filtration based on provided column in key value. Example: abc
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
"message": "5620 Contact(s) data found",
"data": [
{
"id": 8838,
"first_name": "Test Owner",
"last_name": "Test",
"email": "saim@test.com",
"mobile": "+971588159645",
"processed_mobile": "+971588159645",
"agent": {
"id": 172,
"full_name": "Muhammad Saim Aziz",
"mobile": "+971588159647",
"email": "smirza277@gmail.com"
},
"secondary_agent": [
{
"id": 2,
"full_name": "Kairatbek Saparaliev",
"mobile": "+971521301689",
"email": "k.saparaliev@axcapital.ae"
},
{
"id": 254,
"full_name": "Muhammad Saim Aziz",
"mobile": "+971588159647",
"email": "smirza277@gmail.com"
}
]
},
{
"id": 5298,
"first_name": "Shabnam",
"last_name": "Ashraf",
"email": null,
"mobile": "+971 50 104 1711",
"processed_mobile": "+971501041711",
"agent": {
"id": 4,
"full_name": "Dimitry Zolotco",
"mobile": "+971522036708",
"email": "dmitri.zolotco@axcapmail.com"
},
"secondary_agent": null
},
{
"id": 5299,
"first_name": "Shirin",
"last_name": "Maswala",
"email": null,
"mobile": "+971 50 884 8081",
"processed_mobile": "+971508848081",
"agent": {
"id": 4,
"full_name": "Dimitry Zolotco",
"mobile": "+971522036708",
"email": "dmitri.zolotco@axcapmail.com"
},
"secondary_agent": null
},
{
"id": 5300,
"first_name": "Dhiraj",
"last_name": "Saluja",
"email": null,
"mobile": "+971 55 108 5548",
"processed_mobile": "+971551085548",
"agent": {
"id": 4,
"full_name": "Dimitry Zolotco",
"mobile": "+971522036708",
"email": "dmitri.zolotco@axcapmail.com"
},
"secondary_agent": null
},
{
"id": 5301,
"first_name": "Babul",
"last_name": "Sunchuri",
"email": "dilunagari@yahoo.com",
"mobile": "+971 56 403 5235",
"processed_mobile": "+971564035235",
"agent": {
"id": 4,
"full_name": "Dimitry Zolotco",
"mobile": "+971522036708",
"email": "dmitri.zolotco@axcapmail.com"
},
"secondary_agent": null
},
{
"id": 5302,
"first_name": "Jianmin",
"last_name": "He",
"email": null,
"mobile": "+971 50 644 4153",
"processed_mobile": "+971506444153",
"agent": {
"id": 4,
"full_name": "Dimitry Zolotco",
"mobile": "+971522036708",
"email": "dmitri.zolotco@axcapmail.com"
},
"secondary_agent": null
},
{
"id": 5303,
"first_name": "Dr.",
"last_name": "Nazzer",
"email": null,
"mobile": "+966 50 013 9868",
"processed_mobile": "+966500139868",
"agent": {
"id": 4,
"full_name": "Dimitry Zolotco",
"mobile": "+971522036708",
"email": "dmitri.zolotco@axcapmail.com"
},
"secondary_agent": null
},
{
"id": 5304,
"first_name": "Aisha Mubrak",
"last_name": "Alnakhi",
"email": null,
"mobile": "+971 50 481 8816",
"processed_mobile": "+971504818816",
"agent": {
"id": 4,
"full_name": "Dimitry Zolotco",
"mobile": "+971522036708",
"email": "dmitri.zolotco@axcapmail.com"
},
"secondary_agent": null
},
{
"id": 5305,
"first_name": "Muhammad",
"last_name": "Akif",
"email": "arif_his@yahoo.com",
"mobile": "+971 55 750 1385",
"processed_mobile": "+971557501385",
"agent": {
"id": 4,
"full_name": "Dimitry Zolotco",
"mobile": "+971522036708",
"email": "dmitri.zolotco@axcapmail.com"
},
"secondary_agent": null
},
}
]
}
 *}
 * @param  \Illuminate\Http\Request  $request
 * @return \Illuminate\Http\Response
 * @response 200 scenario="User not found"
 * {
"status": false,
"code": 200,
"message": "No Contacts found",
"data": []
}
 */

    public function index(Request $request)
    {
        $query = Contact::where('status', true);

        if ($request->filled('key') && $request->filled('key_value')) {
            $key = $request->input('key');
            $keyValue = $request->input('key_value');
            $query->where($key, 'like', '%' . $keyValue . '%');
        }
        if ($request->input('id')) {
            $query->where('id', $request->input('id'));
        }
        if ($request->input('bitrix_id')) {
            $query->where('bitrix_id', $request->input('bitrix_id'));
        }

        if ($request->input('nationality_id')) {
            $query->whereIn('nationality_id', $request->input('nationality_id'));
        }
        if ($request->input('last_name')) {
            $query->where('last_name', $request->input('last_name'));
        }
        if ($request->input('first_name')) {
            $query->where('first_name', $request->input('first_name'));
        }
        if ($request->input('agent_id')) {
            $query->whereIn('agent_id', $request->input('agent_id'));
        }

        if ($request->input('with_property')) {
            $query->has('activeProperties');
        }

        if ($request->input('email')) {
            $query->where('email', $request->input('email'));
        }

        if ($request->input('mobile')) {
            $query->where('mobile', $request->input('mobile'));
        }

        if ($request->input('gender')) {
            $query->whereIn('gender', $request->input('gender'));
        }

        if ($request->input('type')) {
            $query->whereIn('contact_type_id', $request->input('type'));
        }
        if ($request->input('type')) {
            $query->whereIn('contact_type_id', $request->input('type'));
        }

        if ($request->input('source')) {
            $query->whereIn('contact_source_id', $request->input('source'));
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

        if ($request->input('deleted') && $request->input('deleted') == 'true') {
            $query->withTrashed();
        }
        $query->orderBy('updated_at', 'desc');
        $contacts = $query->get();
        $count = $contacts->count();
        return $this->sendResponse(true, $count . ' Contact(s) data found', ContactResource::collection($contacts), 200);

    }
    /**
     *
     *
     * Agent Contacts
     *
     * Get Agent Contacts.
     * @group Contacts
     * @authenticated
     * @queryParam email email for Filtration based on email . Example: abc@axcapital.ae
     * @queryParam page page for pagination . Example: 2
     * @queryParam per_page per_page for records per page . Example: 10
     * @queryParam first_name first_name for Filtration based on First Name. Example: abc
     * @queryParam last_name last_name for Filtration based on Last Name. Example: abc
     * @queryParam agent_id agent_id for Filtration based on Agent.
     * @queryParam mobile mobile for Filtration based on Contact Mobile.
     * @queryParam type type for Filtration based on Contact type.
     * @queryParam source source for Filtration based on Contact source.
     * @queryParam gender gender for Filtration based on Contact gender.
     * @queryParam dob dob for Filtration based on Contact dob.
     * @queryParam created_at created_at for Filtration based on Contact created_at.
     * @queryParam updated_at updated_at for Filtration based on Contact updated_at.
     * @queryParam bitrix_id bitrix_id for Filtration based on Bitrix Id.
     * @queryParam deleted deleted for fetching deleted contacts. Example: true
     * @queryParam key key  for Filtration based on certain column. Example: first_name
     * @queryParam key_value key_value  for Filtration based on provided column in key value. Example: abc
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
    "message": "5620 Contact(s) data found",
    "data": [
    {
    "id": 8838,
    "first_name": "Test Owner",
    "last_name": "Test",
    "email": "saim@test.com",
    "mobile": "+971588159645",
    "processed_mobile": "+971588159645",
    "agent": {
    "id": 172,
    "full_name": "Muhammad Saim Aziz",
    "mobile": "+971588159647",
    "email": "smirza277@gmail.com"
    },
    "secondary_agent": [
    {
    "id": 2,
    "full_name": "Kairatbek Saparaliev",
    "mobile": "+971521301689",
    "email": "k.saparaliev@axcapital.ae"
    },
    {
    "id": 254,
    "full_name": "Muhammad Saim Aziz",
    "mobile": "+971588159647",
    "email": "smirza277@gmail.com"
    }
    ]
    },
    {
    "id": 5298,
    "first_name": "Shabnam",
    "last_name": "Ashraf",
    "email": null,
    "mobile": "+971 50 104 1711",
    "processed_mobile": "+971501041711",
    "agent": {
    "id": 4,
    "full_name": "Dimitry Zolotco",
    "mobile": "+971522036708",
    "email": "dmitri.zolotco@axcapmail.com"
    },
    "secondary_agent": null
    },
    {
    "id": 5299,
    "first_name": "Shirin",
    "last_name": "Maswala",
    "email": null,
    "mobile": "+971 50 884 8081",
    "processed_mobile": "+971508848081",
    "agent": {
    "id": 4,
    "full_name": "Dimitry Zolotco",
    "mobile": "+971522036708",
    "email": "dmitri.zolotco@axcapmail.com"
    },
    "secondary_agent": null
    },
    {
    "id": 5300,
    "first_name": "Dhiraj",
    "last_name": "Saluja",
    "email": null,
    "mobile": "+971 55 108 5548",
    "processed_mobile": "+971551085548",
    "agent": {
    "id": 4,
    "full_name": "Dimitry Zolotco",
    "mobile": "+971522036708",
    "email": "dmitri.zolotco@axcapmail.com"
    },
    "secondary_agent": null
    },
    {
    "id": 5301,
    "first_name": "Babul",
    "last_name": "Sunchuri",
    "email": "dilunagari@yahoo.com",
    "mobile": "+971 56 403 5235",
    "processed_mobile": "+971564035235",
    "agent": {
    "id": 4,
    "full_name": "Dimitry Zolotco",
    "mobile": "+971522036708",
    "email": "dmitri.zolotco@axcapmail.com"
    },
    "secondary_agent": null
    },
    {
    "id": 5302,
    "first_name": "Jianmin",
    "last_name": "He",
    "email": null,
    "mobile": "+971 50 644 4153",
    "processed_mobile": "+971506444153",
    "agent": {
    "id": 4,
    "full_name": "Dimitry Zolotco",
    "mobile": "+971522036708",
    "email": "dmitri.zolotco@axcapmail.com"
    },
    "secondary_agent": null
    },
    {
    "id": 5303,
    "first_name": "Dr.",
    "last_name": "Nazzer",
    "email": null,
    "mobile": "+966 50 013 9868",
    "processed_mobile": "+966500139868",
    "agent": {
    "id": 4,
    "full_name": "Dimitry Zolotco",
    "mobile": "+971522036708",
    "email": "dmitri.zolotco@axcapmail.com"
    },
    "secondary_agent": null
    },
    {
    "id": 5304,
    "first_name": "Aisha Mubrak",
    "last_name": "Alnakhi",
    "email": null,
    "mobile": "+971 50 481 8816",
    "processed_mobile": "+971504818816",
    "agent": {
    "id": 4,
    "full_name": "Dimitry Zolotco",
    "mobile": "+971522036708",
    "email": "dmitri.zolotco@axcapmail.com"
    },
    "secondary_agent": null
    },
    {
    "id": 5305,
    "first_name": "Muhammad",
    "last_name": "Akif",
    "email": "arif_his@yahoo.com",
    "mobile": "+971 55 750 1385",
    "processed_mobile": "+971557501385",
    "agent": {
    "id": 4,
    "full_name": "Dimitry Zolotco",
    "mobile": "+971522036708",
    "email": "dmitri.zolotco@axcapmail.com"
    },
    "secondary_agent": null
    },
    }
    ]
    }
     *}
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @response 200 scenario="User not found"
     * {
    "status": false,
    "code": 200,
    "per_page": 20,
    "page": 2,
    "message": "No Contacts found",
    "data": []
    }
     */
    private function isArray($key)
    {
        return gettype(\request($key)) == 'array' && count(\request($key)) > 0;
    }
    public function agentContacts(Request $request)
    {
        $notAdmin = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent", "Primary Agent"]);
        $admin = auth()->user()->hasRole(["Admin", "Super Admin"]);

        if (!($notAdmin || $admin)) {
            return $this->sendResponse(false, 'Not Authorised', [], 200);
        }

        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        // $query = Contact::when($notAdmin, function ($q) use ($childrenIds) {
        //     $q->where(function ($q) use ($childrenIds) {
        //         $q->whereIn("agent_id", $childrenIds)->orWhereHas('contactAgents', function ($q) use ($childrenIds) {
        //             $q->whereIn('user_id', $childrenIds);
        //         });
        //     });
        // })
        // ->where('status', true);

        $query = Contact::when($notAdmin, function ($q) use ($childrenIds) {
            $q->where(function ($q) use ($childrenIds) {
                $q->whereIn('agent_id', $childrenIds)
                  ->orWhereHas('contactAgents', function ($q) use ($childrenIds) {
                      $q->whereIn('user_id', $childrenIds);
                  });
            });
        })
        ->where('status', true);


        if ($request->filled('key') && $request->filled('key_value')) {
            $key = $request->input('key');
            $keyValue = $request->input('key_value');
            $query->where($key, 'like', '%' . $keyValue . '%');
        }
        if ($request->input('bitrix_id')) {
            $query->where('bitrix_id', $request->input('bitrix_id'));
        }

        if ($request->input('first_name')) {
            $query->where('first_name', $request->input('first_name'));
        }

        if ($request->input('last_name')) {
            $query->where('last_name', $request->input('last_name'));
        }

        if ($request->input('agent_id')) {
            $query->where('agent_id', $request->input('agent_id'));
        }

        if ($request->input('email')) {
            $query->where('email', $request->input('email'));
        }

        if ($request->input('deleted') && $request->input('deleted') == 'true') {
            $query->withTrashed();
        }

        if ($request->input('mobile')) {
            $query->where('mobile', $request->input('mobile'));
        }

        if ($request->input('gender')) {
            // $query->whereIn('gender', $request->input('gender'));
            $this->isArray("gender") ? $query->whereIn('gender', \request("source")) : $query->where('contact_sgenderource_id', \request("gender"));

        }

        if ($request->input('type')) {
            // $query->whereIn('contact_type_id', $request->input('type'));
            // $this->isArray("type") ? $query->whereIn('contact_type_id', \request("type")) : $query->where('contact_type_id', \request("type"));

            // search for contact type in contactTypes relationship as contact_type_id and contact_id
            $query->whereHas('contactTypes', function ($q) {
                $this->isArray("type") ? $q->whereIn('contact_type_id', \request("type")) : $q->where('contact_type_id', \request("type"));
            });

        }
        // if ($request->input('type')) {
        //     $query->whereIn('contact_type_id', $request->input('type'));
        // }

        if ($request->input('source')) {
            // $query->whereIn('contact_source_id', $request->input('source'));
            $this->isArray("source") ? $query->whereIn('contact_source_id', \request("source")) : $query->where('contact_source_id', \request("source"));

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

        if ($request->input('with_property')) {
            $query->has('activeProperties');
        }

        $query->orderBy('updated_at', 'desc');

        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10); // Set the number of contacts per page
        $count = $query->count();
        $contacts = $query->paginate($perPage, ['*'], 'page', $page);
        //$count = $contacts->count();
        return $this->sendResponse(true, $count . ' Contact(s) data found', ContactResource::collection($contacts), 200, $page, $perPage);

    }
    /**
     *
     *
     * Contact Sources
     *
     * Get Contact Sources.
     * @group Contacts
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
    "message": "2 Record(s) data found",
    "data": [
    {
    "id": 1,
    "name": "Agent",
    "status": 1,
    "order": 1000,
    "badge": null,
    "image": null,
    "icon": null,
    "thumbnail": null,
    "is_contact_source": 1,
    "is_lead_source": 1,
    "created_by_id": 1,
    "updated_by_id": null,
    "created_at": "2023-04-14T05:56:39.000000Z",
    "updated_at": "2023-04-14T05:56:39.000000Z",
    "deleted_at": null
    },
    {
    "id": 2,
    "name": "Agent External",
    "status": 1,
    "order": 1000,
    "badge": null,
    "image": null,
    "icon": null,
    "thumbnail": null,
    "is_contact_source": 1,
    "is_lead_source": 1,
    "created_by_id": 1,
    "updated_by_id": null,
    "created_at": "2023-04-14T05:56:51.000000Z",
    "updated_at": "2023-04-14T05:56:51.000000Z",
    "deleted_at": null
    },
    ]
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

    public function contactSources(Request $request)
    {

        $query = Source::where('is_contact_source', 1)->where('status', true);

        $data = $query->get();
        $count = $data->count();
        return $this->sendResponse(true, $count . ' Record(s) found', $data, 200);

    }
    /**
     *
     *
     * Contact Types
     *
     * Get Contact Types.
     * @group Contacts
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
    "message": "2 Records found",
    "data": [
    {
    "id": 1,
    "name": "Tenant",
    "status": 1,
    "is_contact_type": 1,
    "is_lead_type": 1,
    "order": 1,
    "icon": null,
    "badge": null,
    "created_by_id": null,
    "updated_by_id": 262,
    "created_at": "2023-02-14T03:30:33.000000Z",
    "updated_at": "2023-05-31T05:58:11.000000Z",
    "deleted_at": null,
    "image": null,
    "thumbnail": null
    },
    {
    "id": 2,
    "name": "Buyer",
    "status": 1,
    "is_contact_type": 1,
    "is_lead_type": 1,
    "order": 2,
    "icon": null,
    "badge": null,
    "created_by_id": null,
    "updated_by_id": 262,
    "created_at": "2023-02-14T03:30:33.000000Z",
    "updated_at": "2023-05-31T05:58:29.000000Z",
    "deleted_at": null,
    "image": null,
    "thumbnail": null
    },
    ]
    }
     *}
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @response 200 scenario="User not found"
     * {
    "status": false,
    "code": 200,
    "message": "No Contacts found",
    "data": []
    }
     */

    public function contactTypes(Request $request)
    {

        $query = Type::where('is_contact_type', 1)->where('status', true);

        $data = $query->get();
        $count = $data->count();
        return $this->sendResponse(true, $count . ' Record(s) found', $data, 200);

    }
    /**
     *
     *
     * Contact Statuses
     *
     * Get Contact Statuses.
     * @group Contacts
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
    "message": "2 Record(s) found",
    "data": [
    {
    "id": 1,
    "name": "Open",
    "status": 1,
    "created_by_id": null,
    "updated_by_id": null,
    "created_at": "2023-02-14T03:30:32.000000Z",
    "updated_at": "2023-02-14T03:30:32.000000Z",
    "deleted_at": null,
    "order": 0,
    "thumbnail": null,
    "image": null,
    "icon": null,
    "badge": null
    },
    {
    "id": 2,
    "name": "Closed",
    "status": 1,
    "created_by_id": null,
    "updated_by_id": null,
    "created_at": "2023-02-14T03:30:32.000000Z",
    "updated_at": "2023-02-14T03:30:32.000000Z",
    "deleted_at": null,
    "order": 0,
    "thumbnail": null,
    "image": null,
    "icon": null,
    "badge": null
    },
    ]
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

    public function contactStatuses(Request $request)
    {

        $query = ContactStatus::where('status', true);

        $data = $query->get();
        $count = $data->count();
        return $this->sendResponse(true, $count . ' Record(s) found', $data, 200);

    }
/**
 *
 *
 * Contact Create
 *
 * Store Contact.
 * @group Contacts
 * @authenticated
 * @bodyParam email email optional unique. Example: abc@axcapital.ae
 * @bodyParam first_name string required. Example: John
 * @bodyParam last_name string required. Example: Doe
 * @bodyParam agent_id integer required to assign the agent.
 * @bodyParam mobile string required.
 * @bodyParam whatsapp string optional.
 * @bodyParam address string optional.
 * @bodyParam gender string optional.
 * @bodyParam dob date optional for date of birth Y-m-d.
 * @bodyParam follow_up_date date optional Y-m-d.
 * @bodyParam nationality_id integer optional.
 * @bodyParam country_id integer optional.
 * @bodyParam passport string optional.
 * @bodyParam passport_expiry date optional.
 * @bodyParam phone string optional.
 * @bodyParam company string optional.
 * @bodyParam website string optional.
 * @bodyParam contact_types array optional.
 * @bodyParam contact_source_id integer required.
 * @bodyParam contact_status_id integer optional.


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
"msg": "Contact Added Successfully",
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

    public function createContact(Request $request)
    {
        // send this request element to the store method in ContactController
        $contactController = new ContactController();
        $response = $contactController->store($request);
        return $response;
    }
    /**
     *
     *
     * Contact Details
     *
     * Get Contact Details.
     * @group Contacts
     * @authenticated
     * @bodyParam id integer required unique. Example: 1

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
    "msg": "Contact Found",
    "data": {
    "id": 16681,
    "bitrix_id": null,
    "bitrix_index": 0,
    "my_crm_id": null,
    "title": null,
    "first_name": "Test",
    "last_name": "Contact",
    "agent_id": 172,
    "previous_agent_id": null,
    "mobile": "+971588159883",
    "email": null,
    "whatsapp": null,
    "address": null,
    "gender": null,
    "dob": null,
    "follow_up_date": null,
    "country_id": null,
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
    "created_by_id": 1,
    "updated_by_id": 1,
    "deleted_at": null,
    "created_at": "2024-04-16T11:42:04.000000Z",
    "updated_at": "2024-04-16T11:42:04.000000Z",
    "deleted_by_id": null,
    "contact_types": [
    {
    "id": 3,
    "name": "Landlord",
    "status": 1,
    "is_contact_type": 1,
    "is_lead_type": 0,
    "order": 0,
    "icon": null,
    "badge": null,
    "created_by_id": null,
    "updated_by_id": 262,
    "created_at": "2023-02-14T03:30:33.000000Z",
    "updated_at": "2023-05-31T05:58:20.000000Z",
    "deleted_at": null,
    "image": null,
    "thumbnail": null,
    "pivot": {
    "contact_id": 16681,
    "contact_type_id": 3
    }
    }
    ],
    "contact_agents": []
    }
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

    public function getContact(Request $request)
    {
        abort_if(!auth()->user()->hasPermissionTo('Contacts View'), 403, 'You do not have permission.');


        $validator = Validator::make($request->all(), [
            "id" => "required|exists:contacts",
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(false, 'Validation Error', $validator->errors(), 422);
        }


        $notAdmin = auth()->user()->hasRole(["Head of Secondary","Sales Director", "Team Leader", "Line Manager", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }
        $contact = Contact::where('id', $request->id)
        ->when($notAdmin,function($q)use ($childrenIds){
            $q->where(function($q) use($childrenIds){
                $q->whereIn('agent_id', $childrenIds)->orWhereHas('contactAgents', function($q) use($childrenIds){
                    $q->whereIn('user_id', $childrenIds);
                });

            });
        })
        ->first();
        if (!empty($contact)) {
            $contact->load("contactTypes", "contactAgents");
            $contact = new ContactResource($contact);
            return $this->sendResponse(200, 'Contact Information Fetched', $contact, 200);

        } else {
            return $this->sendResponse(404, 'Contact Information Not found', 200);

        }
    }
    /**
     *
     *
     * Contact Update
     *
     * Update Contact.
     * @group Contacts
     * @authenticated
     * @bodyParam id integer required. 1
     * @bodyParam email email optional unique. Example: abc@axcapital.ae
     * @bodyParam first_name string required. Example: John
     * @bodyParam last_name string required. Example: Doe
     * @bodyParam agent_id integer required to assign the agent.
     * @bodyParam mobile string required.
     * @bodyParam whatsapp string optional.
     * @bodyParam address string optional.
     * @bodyParam gender string optional.
     * @bodyParam dob date optional for date of birth Y-m-d.
     * @bodyParam follow_up_date date optional Y-m-d.
     * @bodyParam nationality_id integer optional.
     * @bodyParam country_id integer optional.
     * @bodyParam passport string optional.
     * @bodyParam passport_expiry date optional.
     * @bodyParam phone string optional.
     * @bodyParam company string optional.
     * @bodyParam website string optional.
     * @bodyParam contact_types array optional.
     * @bodyParam contact_source_id integer required.
     * @bodyParam contact_status_id integer optional.


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
    "msg": "Contact Updated Successfully",
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

    public function updateContact(Request $request)
    {
        // send this request element to the store method in ContactController
        $contactController = new ContactController();
        $response = $contactController->update($request);
        return $response;
    }
}
