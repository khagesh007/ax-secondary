<?php

namespace App\Http\Controllers\Api\V2\Calendar;


use App\Http\Controllers\Api\BaseController;
use App\Models\Contact;
use App\Models\Lead;
use App\Models\User;
use App\Models\UserOAuthToken;
use App\Traits\GoogleCalendarEventTrait;
use App\Traits\OAuthTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CalendarApiV2Controller extends BaseController
{
    use GoogleCalendarEventTrait, OAuthTrait;

    /**
     *
     *
     * Get Agent Leads
     *
     * @group Calander
     * @authenticated
     * @header Accept application/json
     * @urlParam agent_id required The id of the agent. Example: 12

     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
     * {

    {
    "status": true,
    "code": 200,
    "message": "Leads Fetched",
    "data": {
    "data": [
    {
    "id": 263121,
    "agent_id": 172,
    "first_name": "Guest",
    "last_name": null,
    "mobile": "+971508817707",
    "email": null,
    "source_id": 41,
    "title": "Guest  - +971508817707 - ",
    "lead_source": {
    "id": 41,
    "name": "Whatsapp"
    }
    },
    {
    "id": 262974,
    "agent_id": 172,
    "first_name": "Sameer Ahmad",
    "last_name": null,
    "mobile": "971507238982",
    "email": "",
    "source_id": 109,
    "title": "Sameer Ahmad  - 971507238982 - ",
    "lead_source": {
    "id": 109,
    "name": "Bitrix-unspecified"
    }
    },
    {
    "id": 262998,
    "agent_id": 172,
    "first_name": null,
    "last_name": null,
    "mobile": "0524055667",
    "email": null,
    "source_id": 31,
    "title": "  - 0524055667 - ",
    "lead_source": {
    "id": 31,
    "name": "Property Finder"
    }
    },
    {
    "id": 263011,
    "agent_id": 172,
    "first_name": "New",
    "last_name": null,
    "mobile": "+18252518853",
    "email": null,
    "source_id": 50,
    "title": "New  - +18252518853 - ",
    "lead_source": {
    "id": 50,
    "name": "Unspecified"
    }
    },
    {
    "id": 263112,
    "agent_id": 172,
    "first_name": "New",
    "last_name": null,
    "mobile": "+18252518853",
    "email": null,
    "source_id": 50,
    "title": "New  - +18252518853 - ",
    "lead_source": {
    "id": 50,
    "name": "Unspecified"
    }
    },
    {
    "id": 263114,
    "agent_id": 172,
    "first_name": "Guest",
    "last_name": null,
    "mobile": "971588828707",
    "email": "",
    "source_id": 109,
    "title": "Guest  - 971588828707 - ",
    "lead_source": {
    "id": 109,
    "name": "Bitrix-unspecified"
    }
    },

    }
    ]
    },
    "page": 1,
    "per_page": 10
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

    public function getLeads(Request $request)
    {
        $user_id = ($request->has('agent_id') && $request->agent_id != '') ? $request->agent_id : auth()->id();
        if ($user_id != '') {
            $user = User::find($user_id);
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

        $data = Lead::when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->when($request->keyword, function ($q) {
            $q->where(function ($q2) {
                $q2->where("first_name", "LIKE", "%" . \request("keyword") . "%")
                    ->orWhere("last_name", "LIKE", "%" . \request("keyword") . "%")
                    ->orWhere("mobile", "LIKE", "%" . \request("keyword") . "%")
                    ->orWhere("email", "LIKE", "%" . \request("keyword") . "%");
            });
        })->where('status', true)
            ->select('id', 'agent_id', 'first_name', 'last_name', 'mobile', 'email', 'source_id')
            ->orderBy('updated_at', 'DESC')
           ->with('leadSource:id,name')->paginate('30');

        return $this->sendResponse(true, 'Leads Fetched', [
            "data" => $data,
        ], 200);
    }
    /**
     *
     *
     * Get Agent Contacts
     *
     * @group Calander
     * @authenticated
     * @header Accept application/json
     * @urlParam agent_id required The id of the agent. Example: 12
     * @queryParam keyword The keyword to search. Example: saad

     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
     * {
    {
    "status": true,
    "code": 200,
    "message": "Contacts Fetched",
    "data": {
    "data": [
    {
    "id": 18180,
    "first_name": "ALi",
    "last_name": "Azmat",
    "mobile": "+971585858176",
    "email": "saad@gmail.comss"
    },
    {
    "id": 18179,
    "first_name": "ALi",
    "last_name": "Azmat",
    "mobile": "+971585858176",
    "email": "saad@gmail.comss"
    },
    {
    "id": 18177,
    "first_name": "Fraz",
    "last_name": "Shabbir",
    "mobile": "+7561498786",
    "email": "uk.frazshabbir@gmail.com"
    },
    {
    "id": 18108,
    "first_name": "Deekendra",
    "last_name": "Kumar",
    "mobile": "+97190982849",
    "email": null
    },
    {
    "id": 17799,
    "first_name": "Deekendra",
    "last_name": "Kumar",
    "mobile": "+97190982869",
    "email": null
    },
    {
    "id": 17798,
    "first_name": "Deekendra",
    "last_name": "Kumar",
    "mobile": "+9098284997",
    "email": null
    },
    {
    "id": 14931,
    "first_name": "Saim - Tests",
    "last_name": "Owner",
    "mobile": "+971700070000",
    "email": "03idbq0p@duck.com"
    },
    {
    "id": 16516,
    "first_name": "New",
    "last_name": "Owner",
    "mobile": "+97155812435",
    "email": "newOwner@gmail.com"
    }
    ]
    },
    "page": 1,
    "per_page": 10
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

    public function getContacts(Request $request)
    {
        $user_id = ($request->has('agent_id') && $request->agent_id != '') ? $request->agent_id : auth()->id();
        if ($user_id != '') {
            $user = User::find($user_id);
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

        $data = Contact::when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->when($request->contact_type_id, function ($q) {
            $q->whereHas("contactTypes", fn($q1) => $q1->where("id", \request("contact_type_id")));
        })->when($request->keyword, function ($q) {
            $q->where(function ($q2) {
                $q2->where("first_name", "LIKE", "%" . \request("keyword") . "%")
                    ->orWhere("last_name", "LIKE", "%" . \request("keyword") . "%")
                    ->orWhere("mobile", "LIKE", "%" . \request("keyword") . "%")
                    ->orWhere("email", "LIKE", "%" . \request("keyword") . "%");
            });

        })->where('status', true)->select('id', 'first_name', 'last_name', 'mobile', 'email')
            ->orderBy('updated_at', 'DESC')->simplePaginate('30');

        return $this->sendResponse(true, 'Contacts Fetched', [
            "data" => $data,
        ], 200);

    }
}
