<?php

namespace App\Http\Controllers\Api\V1\Calander;

use App\Events\EventCreated;
use App\Events\EventCreatedMobile;
use App\Events\EventUpdated;
use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\EventResource;
use App\Jobs\CreateGoogleCalendarEvent;
use App\Models\Contact;
use App\Models\ContactNote;
use App\Models\Event;
use App\Models\EventType;
use App\Models\Lead;
use App\Models\LeadNote;
use App\Models\LeadStatus;
use App\Models\ListingLeadNote;
use App\Models\Property;
use App\Models\PropertyNote;
use App\Models\User;
use App\Models\UserOAuthToken;
use App\Traits\GoogleCalendarEventTrait;
use App\Traits\OAuthTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CalanderApiController extends BaseController
{
    use GoogleCalendarEventTrait, OAuthTrait;

    /**
     *
     *
     * Get Events
     *
     * @group Calander
     * @authenticated
     * @header Accept application/json
     * @urlParam start required The start date of the event. Example: 2021-09-09
     * @urlParam end required The end date of the event. Example: 2021-09-09
     * @queryParam agents optional The id of the agent. Example: 12
     * @queryParam type optional The id of the event type. Example: 1

     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
     * {
    {
    "status": true,
    "code": 200,
    "message": "Events Fetched",
    "data": [
    {
    "title": "testing",
    "start": "2024-08-17 15:57:00",
    "id": 16600,
    "end": "2024-08-17 16:27:00",
    "agent": {
    "id": 172,
    "bitrix_role": null,
    "entity": null,
    "group_id": null,
    "parent_id": 173,
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
    "last_login": "2024-08-21 10:21:51",
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
    "call_tracking": 0,
    "call_lead": 0,
    "is_commercial": 0,
    "created_by_id": 1,
    "updated_by_id": 172,
    "otp": "628061",
    "created_at": "2023-02-15T06:42:31.000000Z",
    "updated_at": "2024-08-21T06:21:36.000000Z",
    "deleted_at": null,
    "google2fa_secret": "OFVVWRMYVTGVLVIS",
    "telegram2fa_secret": null,
    "back_up_google2fa_secret": null,
    "login_attempt": 0,
    "joining_date": "2019-02-28 11:39:59",
    "last_deal_date": null,
    "is_report_exempt": 0,
    "zone_id": null,
    "full_name": "Muhammad Saim Aziz",
    "thumb_url": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/thumb_168457219720230520124317.jpg",
    "image_url": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/168457219720230520124317.jpg",
    "role_list": [
    "Agent"
    ]
    },
    "className": "bg-soft-primary text-primary text-white"
    },
    {
    "title": "Meeting arranged with contact Deekendra Ksksj and lead Test Lead",
    "start": "2024-08-19 12:55:00",
    "id": 16653,
    "end": "2024-08-19 13:25:00",
    "agent": {
    "id": 172,
    "bitrix_role": null,
    "entity": null,
    "group_id": null,
    "parent_id": 173,
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
    "last_login": "2024-08-21 10:21:51",
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
    "call_tracking": 0,
    "call_lead": 0,
    "is_commercial": 0,
    "created_by_id": 1,
    "updated_by_id": 172,
    "otp": "628061",
    "created_at": "2023-02-15T06:42:31.000000Z",
    "updated_at": "2024-08-21T06:21:36.000000Z",
    "deleted_at": null,
    "google2fa_secret": "OFVVWRMYVTGVLVIS",
    "telegram2fa_secret": null,
    "back_up_google2fa_secret": null,
    "login_attempt": 0,
    "joining_date": "2019-02-28 11:39:59",
    "last_deal_date": null,
    "is_report_exempt": 0,
    "zone_id": null,
    "full_name": "Muhammad Saim Aziz",
    "thumb_url": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/thumb_168457219720230520124317.jpg",
    "image_url": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/168457219720230520124317.jpg",
    "role_list": [
    "Agent"
    ]
    },
    "className": "bg-soft-info text-info text-white"
    }
    ],
    "page": 1,
    "per_page": 10
    }
     *}
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @response 200 scenario="Owner not found"
    {
    "status": false,
    "code": 500,
    "message": "No Data found",
    "data": []
    }
     */

    public function index(Request $request)
    {
        $startDate = Carbon::parse($request->start)->format('Y-m-d H:i:s');
        $endDate = Carbon::parse($request->end)->format('Y-m-d H:i:s');

        $notAdmin = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }
        $events = Event::where("start_time", ">=", $startDate)
            ->where("end_time", "<=", $endDate)
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->where(function ($q1) use ($childrenIds) {
                    $q1->whereIn("agent_id", $childrenIds);
                    $q1->orWhereHas("attendees", fn($q2) => $q2->whereIn("user_id", $childrenIds));
                });

            })
            ->when($request->agents, function ($q) {
                $agents = explode(",", \request("agents"));
                $q->where(function ($q1) use ($agents) {
                    $q1->whereIn("agent_id", $agents);
                    $q1->orWhereHas("attendees", fn($q2) => $q2->whereIn("user_id", $agents));
                });

            })
            ->when($request->type, function ($q) {
                $type = explode(",", \request("type"));
                $q->whereIn("event_type_id", $type);
            })->get();

        $typeBadge = EventType::pluck("badge", "id")->toArray();
        $data = [];

        $data = EventResource::collection($events);

        return $this->sendResponse(true, 'Events Fetched', $data, 200);
    }
    public function __index(Request $request)
    {
        $startDate = Carbon::parse($request->start)->format('Y-m-d H:i:s');
        $endDate = Carbon::parse($request->end)->format('Y-m-d H:i:s');

        $notAdmin = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        // Get per_page from the request or set a default (e.g., 15)
        $perPage = $request->get('per_page', 10);

        $query = Event::where("start_time", ">=", $startDate)
            ->where("end_time", "<=", $endDate)
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->where(function ($q1) use ($childrenIds) {
                    $q1->whereIn("agent_id", $childrenIds);
                    $q1->orWhereHas("attendees", fn($q2) => $q2->whereIn("user_id", $childrenIds));
                });
            })
            ->when($request->agents, function ($q) {
                $agents = explode(",", \request("agents"));
                $q->where(function ($q1) use ($agents) {
                    $q1->whereIn("agent_id", $agents);
                    $q1->orWhereHas("attendees", fn($q2) => $q2->whereIn("user_id", $agents));
                });
            })
            ->when($request->type, function ($q) {
                $type = explode(",", \request("type"));
                $q->whereIn("event_type_id", $type);
            });
            $events_count = $query->count();

            // Use paginate() to add pagination, passing $perPage
            $events = $query->paginate($perPage);
            if ($events_count > 0) {
                $events->data = EventResource::collection($events);
                $events->appends($request->query())->links();
            } else {
                return $this->sendResponse(false, 'No events found', [], 200);
            }
            // dd($leads);
            return $this->sendResponse(true, 'Lead list', compact('events'), 200);
    }

    /**
     *
     *
     * Get Event Types
     *
     * @group Calander
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
    "page": 1,
    "per_page": 10
    }

     *}
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @response 200 scenario="Owner not found"
    {
    "status": false,
    "code": 500,
    "message": "No Data found",
    "data": []
    }
     */

    public function eventTypes()
    {
        try {
            $data = \App\Models\EventType::where('status', true)->get();
            return $this->sendResponse(true, 'Data fetched', $data, 200);
        } catch (\Throwable $th) {
            return $this->sendResponse(false, 'Something Went Wrong', $th->getMessage(), 500);
        }
    }

    /**
     *
     *
     *
     * Create Event
     * @group Calander
     * @authenticated
     * @header Accept application/json

     * @bodyParam agent_id integer for agent_id. Example: 12
     * @bodyParam start_time string for start_time. Example: 2021-09-09 10:00
     * @bodyParam end_time string for end_time. Example: 2021-09-09 11:00
     * @bodyParam event_type_id integer for event_type_id. Example: 1
     * @bodyParam property_id integer for property_id. Example: 1
     * @bodyParam listing_lead_id integer for listing_lead_id. Example: 1
     * @bodyParam priority string for priority. Example: High
     * @bodyParam title string for title. Example: Meeting
     * @bodyParam location string for location. Example: Dubai
     * @bodyParam description string for description. Example: Meeting with client
     * @bodyParam attendees array for attendees. Example: [1,2,3]
     * @bodyParam send_notification boolean for send_notification. Example: 1
     * @bodyParam reminder_to_attendees boolean for reminder_to_attendees. Example: 1
     * @bodyParam contact_id integer for contact_id. Example: 1
     * @bodyParam lead_id integer for lead_id. Example: 1
     * @bodyParam all_attendees string for all_attendees. Example: on
     * @bodyParam all_team_attendees string for all_team_attendees. Example: on
     * @bodyParam reminder string for reminder. Example: 5
     * @bodyParam reminder_frequency string for reminder_frequency. Example: Minutes
     * @bodyParam event_recurring boolean for event_recurring. Example: 1
     * @bodyParam recurring_frequency string for recurring_frequency. Example: daily
     *
     *
     *
     *
     * @header Accept application/json
     * @header X-Requested-With XMLHttpRequest

     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
     * {

    "status": "1",
    "msg": "Event Added Successfully",
    "data": []

     *}
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @response 200 scenario="Owner not found"
     * {




     */

    public function create(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "agent_id" => "required|exists:users,id",
            "start_time" => "required",
            "end_time" => "required",
            "event_type_id" => "required|exists:event_types,id",
            "property_id" => "nullable|exists:properties,id",
            "listing_lead_id" => "nullable|exists:listing_leads,id",
            "priority" => "nullable|string",
            "title" => "required|string",
            "location" => "nullable|string",
            "description" => "nullable|string",
            "attendees" => "nullable|array",
            "attendees.*" => "nullable|exists:users,id",
            "send_notification" => "nullable",
            "reminder_to_attendees" => "nullable",
            "contact_id" => "nullable|exists:contacts,id",
            "lead_id" => "nullable|exists:leads,id",
            //add custom error msg
            "all_attendees" => 'nullable',
            "all_team_attendees" => 'nullable',
            "reminder" => "required_if:reminder_to_attendees,1",
            "reminder_frequency" => "required_if:reminder_to_attendees,1",
            "event_recurring" => "nullable",
            "recurring_frequency" => "nullable",
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(false, 'Validation Error', $validator->errors(), 422);
        }
        $validatedData = $validator->validated();
        if ($request->reminder_to_attendees == 1 && $request->reminder < 5 && $request->reminder_frequency == 'Minutes') {
            return $this->sendResponse(false, 'Reminder should be equal or greater than 5 minutes.', [], 200);
        }
        if ($request->event_recurring == true) {

            if ($request->recurring_frequency == 'daily') {
                if ($request->recurring_count == '' && $request->recurring_end_date == null) {
                    return $this->sendResponse(false, 'Recurring count or recurring end date is required for daily recurring event.', [], 200);
                }
            } else if ($request->recurring_frequency == 'weekly') {
                $reoccurringDays = $request->reoccurringDays != '' ? explode(',', $request->reoccurringDays) : [];

                if ($reoccurringDays == null || count($reoccurringDays) == 0) {
                    return $this->sendResponse(false, 'Recurring days is required for weekly recurring event.', [], 200);
                }
            }
        }
        // dd($validatedData);
        // try {
        $validatedData["created_by_id"] = auth()->id();
        $validatedData["send_notification"] = $request->boolean("send_notification");
        $validatedData["reminder_to_attendees"] = $request->boolean("reminder_to_attendees");
        //$event = Event::create($validatedData);
        $start_date = date('Y-m-d H:i', strtotime($validatedData["start_time"]));
        $end_date = date('Y-m-d H:i', strtotime($validatedData["end_time"]));

        $event = new Event();
        $event->agent_id = $validatedData["agent_id"];
        $event->contact_id = $validatedData['contact_id'] ?? null;
        $event->lead_id = $validatedData['lead_id'] ?? null;
        $event->property_id = $validatedData["property_id"] ?? null;
        $event->listing_lead_id = $validatedData["listing_lead_id"] ?? null;
        $event->start_time = $start_date;
        $event->end_time = $end_date;
        $event->event_type_id = $validatedData["event_type_id"];
        $event->priority = $validatedData["priority"] ?? null;
        $event->title = $validatedData['title'];
        $event->location = $validatedData['location'];
        $event->description = $validatedData['description'];
        $event->created_by_id = $validatedData['created_by_id'];
        $event->reminder = $validatedData['reminder'];
        $event->send_notification = $validatedData["send_notification"];
        $event->reminder_frequency = $validatedData['reminder_frequency'];
        $event->reminder_to_attendees = $validatedData['reminder_to_attendees'];
        $original = $event->getOriginal() ?? [];
        $dirty = $event->getDirty() ?? [];
        $event->save();
        if (isset($request->all_attendees) && $request->all_attendees == 'on') {
            $all_user = User::where('status', '1')->pluck('id');
            $event->attendees()->sync($all_user ?? []);
        } elseif (isset($request->all_team_attendees) && $request->all_team_attendees == 'on') {
            $childrenIds = getUserChildrenIdsOnly(auth()->user()->id);
            $event->attendees()->sync($childrenIds ?? []);
        } else {
            $event->attendees()->sync($request->attendees ?? []);
        }

        if (isset($validatedData["lead_id"]) && $validatedData["lead_id"] != null) {
            $lead = Lead::find($validatedData["lead_id"]);
            $lead->follow_up_date = date('Y-m-d', strtotime($validatedData["end_time"]));
            $lead->save();
        }
        $arr = ['attributes' => $dirty,
            'old' => $original, 'lead_id' => $event->lead_id,
            'contact_id' => $event->contact_id,
            'property_id' => $event->property_id,
        ];

        activity()->performedOn($event)
            ->causedBy(auth()->user())
            ->withProperties($arr)
            ->event('created')
            ->log(auth()->user()->first_name . ' has created a new calendar ' . $event->type?->name . ' event');

        $noteData = [
            'lead_id' => $event->lead_id ?? null,
            'contact_id' => $event->contact_id ?? null,
            'property_id' => $event->property_id ?? null,
            'listing_lead_id' => $event->listing_lead_id ?? null,
            'note' => $event->type?->name . " event has been created titled " . $request->title . " on " . date('d/m/y', strtotime($request->start_time)) . " " . date('h:ia', strtotime($request->start_time)) . " - " . date('h:ia', strtotime($request->end_time)),
            'created_by_id' => auth()->id(),
        ];

        $event->property_id != null ? PropertyNote::create($noteData) : '';
        $event->contact_id != null ? ContactNote::create($noteData) : '';
        $event->lead_id != null ? LeadNote::create($noteData) : '';
        $event->listing_lead_id != null ? ListingLeadNote::create($noteData) : '';

        if ($event->lead_id != null && $event->type->name == 'Viewing') {
            $lead_status = LeadStatus::where('name', 'Viewing Arranged')->first();
            $lead = Lead::find($event->lead_id);
            $lead->lead_status_id = $lead_status->id ?? null;
            $lead->save();
        }
        if ($request->event_recurring == true) {

            $recurring_data = [
                'frequency' => $request->recurring_frequency,
                'recurring_date' => $start_date,
                'recurring_count' => $request->recurring_count,
            ];
            $event->recurring_data = $recurring_data;
            $event->save();
            $this->createRecurringEvent($request, $event);

        }

        $user = User::find($event->agent_id);
        $userOAuthToken = UserOAuthToken::where("user_id", $user->id)->where("type", "google")->first();
        if (!is_null($userOAuthToken)) {
            CreateGoogleCalendarEvent::dispatch($event);
//            $event->load(["attendees:id,email"]);
//            $attendees = $event->attendees->pluck("email")->map(function ($data) {
//                return ["email" => $data];
//            })->all();
//            if ($this->checkIfGoogleAccessTokenExipred($userOAuthToken->token)) {
//                $accessToken = $this->regenerateGoogleAccessToken($userOAuthToken->token);
//                $userOAuthToken->token = $accessToken;
//                $userOAuthToken->save();
//            }
//            $googleCalendarEventId = $this->createEvent($event, $user->email, $userOAuthToken->token, $attendees);
//            // $googleCalendarEventId = $this->createEvent($event, env("GOOGLE_CALENDAR_ID"), $userOAuthToken->token, $attendees); // Use this for testing
//            if (!is_null($googleCalendarEventId)) {
//                $event->google_calendar_event_id = $googleCalendarEventId;
//                $event->save();
//            }
        }

        event(new EventCreated($event));
        // event(new EventCreatedMobile($event, 'created'));

        return $this->sendResponse(true, 'Event Added Successfully.', ['id' => $event->id], 200);
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
        })->when($request->search, function ($q) {
            $q->where(function ($q2) {
                $q2->where("first_name", "LIKE", "%" . \request("search") . "%")
                    ->orWhere("last_name", "LIKE", "%" . \request("search") . "%")
                    ->orWhere("mobile", "LIKE", "%" . \request("search") . "%")
                    ->orWhere("email", "LIKE", "%" . \request("search") . "%");
            });

        })->where('status', true)->select('id', 'first_name', 'last_name', 'mobile', 'email')->orderBy('updated_at', 'DESC')->limit('30')->get();

        return $this->sendResponse(true, 'Contacts Fetched', [
            "data" => $data,
        ], 200);

    }

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
        })->when($request->search, function ($q) {
            $q->where(function ($q2) {
                $q2->where("first_name", "LIKE", "%" . \request("search") . "%")
                    ->orWhere("last_name", "LIKE", "%" . \request("search") . "%")
                    ->orWhere("mobile", "LIKE", "%" . \request("search") . "%")
                    ->orWhere("email", "LIKE", "%" . \request("search") . "%");
            });
        })->where('status', true)
            ->select('id', 'agent_id', 'first_name', 'last_name', 'mobile', 'email', 'source_id')
            ->orderBy('updated_at', 'DESC')
            ->limit('300')->with('leadSource:id,name')->get();

        return $this->sendResponse(true, 'Leads Fetched', [
            "data" => $data,
        ], 200);
    }

    /**
     *
     *
     * Get Agent Properties
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
    "message": "Listings Fetched",
    "data": {
    "data": [
    {
    "id": 20442,
    "my_crm_id": null,
    "ref_no": "axc-3978747",
    "title": null,
    "description": null,
    "listing_source": null,
    "for": "Residential for sale",
    "region_id": 1,
    "community_id": 10,
    "sub_community_id": 30,
    "community_property_id": null,
    "property_category_id": 1,
    "bed_id": 1,
    "bath_id": 1,
    "unit_no": "101",
    "built_up_area": null,
    "plot_size": null,
    "street": null,
    "floor": null,
    "view": null,
    "parking": null,
    "dewa_number": null,
    "rera_permit_number": null,
    "rera_issue_date": null,
    "rera_expiry_date": null,
    "rera_validation_url": null,
    "property_type_id": null,
    "property_status_id": null,
    "completion_status": null,
    "rented_at": null,
    "rented_until": null,
    "next_available": null,
    "availability_date": null,
    "remind": null,
    "furnished": null,
    "featured": 0,
    "exclusive": 0,
    "exclusive_start": null,
    "exclusive_end": null,
    "hot": 0,
    "publish_status": "DRAFT",
    "admin_unpublish_reason": null,
    "agent_id": 172,
    "previous_agent_id": null,
    "external_agent_id": null,
    "telesales_id": null,
    "owner_id": 18180,
    "developer_id": null,
    "cheques": null,
    "mycrm_updated": 0,
    "created_by_id": 172,
    "updated_by_id": null,
    "video_link": null,
    "video_360_link": null,
    "qr_link": null,
    "title_deed_text": null,
    "data_version": 1,
    "is_archived": 0,
    "payment_method": null,
    "down_payment_price": null,
    "build_year": null,
    "created_at": "2024-07-15T14:38:03.000000Z",
    "updated_at": "2024-07-15T14:38:03.000000Z",
    "deleted_by_id": null,
    "deleted_at": null,
    "state": null,
    "is_live": 0,
    "request_status_type": null,
    "PASSWORD": null,
    "villa_type": null,
    "contract_start": null,
    "contract_end": null,
    "contract_number": null,
    "published_date": null,
    "unpublished_date": null,
    "unable_to_generate_qr": 0,
    "offplan_portal_fetch": 0,
    "republished_date": null,
    "first_image": null,
    "category": "Apartment",
    "location": "The Residences at District One, Mohammed Bin Rashid City, Dubai",
    "my_property": true,
    "bed": {
    "id": 1,
    "name": "Studio",
    "status": 1,
    "order": 0,
    "created_by_id": null,
    "updated_by_id": 1,
    "deleted_at": null,
    "created_at": "2023-02-14T03:30:31.000000Z",
    "updated_at": "2023-07-24T10:36:31.000000Z"
    },
    "price": null,
    "property_category": {
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
    "created_at": "2023-02-14T03:30:33.000000Z",
    "updated_at": "2023-06-26T14:31:16.000000Z",
    "deleted_at": null
    },
    "community_property": null,
    "sub_community": {
    "id": 30,
    "region_id": 1,
    "community_id": 10,
    "name": "The Residences at District One",
    "alternate_name": null,
    "slug": "the-residences-at-district-one",
    "description": null,
    "path_name": "The Residences at District One, Mohammed Bin Rashid City, Dubai",
    "alternate_path_name": null,
    "latitude": "25.171461",
    "longitude": "55.2758",
    "created_by_id": 1,
    "updated_by_id": 1,
    "status": 1,
    "created_at": "2020-04-29T19:54:06.000000Z",
    "updated_at": "2020-04-29T19:54:06.000000Z",
    "deleted_at": null
    }
    },
    {
    "id": 20441,
    "my_crm_id": null,
    "ref_no": "axc-3978746",
    "title": null,
    "description": null,
    "listing_source": null,
    "for": "Residential for sale",
    "region_id": 1,
    "community_id": 10,
    "sub_community_id": 30,
    "community_property_id": null,
    "property_category_id": 1,
    "bed_id": 1,
    "bath_id": 1,
    "unit_no": "101",
    "built_up_area": null,
    "plot_size": null,
    "street": null,
    "floor": null,
    "view": null,
    "parking": null,
    "dewa_number": null,
    "rera_permit_number": null,
    "rera_issue_date": null,
    "rera_expiry_date": null,
    "rera_validation_url": null,
    "property_type_id": null,
    "property_status_id": null,
    "completion_status": null,
    "rented_at": null,
    "rented_until": null,
    "next_available": null,
    "availability_date": null,
    "remind": null,
    "furnished": null,
    "featured": 0,
    "exclusive": 0,
    "exclusive_start": null,
    "exclusive_end": null,
    "hot": 0,
    "publish_status": "DRAFT",
    "admin_unpublish_reason": null,
    "agent_id": 172,
    "previous_agent_id": null,
    "external_agent_id": null,
    "telesales_id": null,
    "owner_id": 18179,
    "developer_id": null,
    "cheques": null,
    "mycrm_updated": 0,
    "created_by_id": 172,
    "updated_by_id": null,
    "video_link": null,
    "video_360_link": null,
    "qr_link": null,
    "title_deed_text": null,
    "data_version": 1,
    "is_archived": 0,
    "payment_method": null,
    "down_payment_price": null,
    "build_year": null,
    "created_at": "2024-07-15T14:37:29.000000Z",
    "updated_at": "2024-07-15T14:37:29.000000Z",
    "deleted_by_id": null,
    "deleted_at": null,
    "state": null,
    "is_live": 0,
    "request_status_type": null,
    "PASSWORD": null,
    "villa_type": null,
    "contract_start": null,
    "contract_end": null,
    "contract_number": null,
    "published_date": null,
    "unpublished_date": null,
    "unable_to_generate_qr": 0,
    "offplan_portal_fetch": 0,
    "republished_date": null,
    "first_image": null,
    "category": "Apartment",
    "location": "The Residences at District One, Mohammed Bin Rashid City, Dubai",
    "my_property": true,
    "bed": {
    "id": 1,
    "name": "Studio",
    "status": 1,
    "order": 0,
    "created_by_id": null,
    "updated_by_id": 1,
    "deleted_at": null,
    "created_at": "2023-02-14T03:30:31.000000Z",
    "updated_at": "2023-07-24T10:36:31.000000Z"
    },
    "price": null,
    "property_category": {
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
    "created_at": "2023-02-14T03:30:33.000000Z",
    "updated_at": "2023-06-26T14:31:16.000000Z",
    "deleted_at": null
    },
    "community_property": null,
    "sub_community": {
    "id": 30,
    "region_id": 1,
    "community_id": 10,
    "name": "The Residences at District One",
    "alternate_name": null,
    "slug": "the-residences-at-district-one",
    "description": null,
    "path_name": "The Residences at District One, Mohammed Bin Rashid City, Dubai",
    "alternate_path_name": null,
    "latitude": "25.171461",
    "longitude": "55.2758",
    "created_by_id": 1,
    "updated_by_id": 1,
    "status": 1,
    "created_at": "2020-04-29T19:54:06.000000Z",
    "updated_at": "2020-04-29T19:54:06.000000Z",
    "deleted_at": null
    }
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

    public function getProperties(Request $request)
    {
        $user_id = ($request->has('agent_id') && $request->agent_id != '') ? $request->agent_id : auth()->id();
        if ($user_id != '') {
            $user = User::find($user_id);
            $dates = $request->dates;
        } else {
            $user = auth()->user();
        }
        $notAdmin = $user->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds($user_id);
        }
        $data = Property::when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->when($request->term, function ($q) use ($request) {
            $term = isset($request->term) ? $request->term : '';
            $q->where(function ($qry) use ($term) {
                $qry->where(\DB::raw('lower(ref_no)'), 'like', '%' . strtolower($term) . '%')
                    ->orWhere(\DB::raw('lower(title)'), 'like', '%' . strtolower($term) . '%')
                    ->orWhere(\DB::raw('lower(unit_no)'), 'like', '%' . strtolower($term) . '%')
                ->orWhereHas('communityProperty', function ($q) use ($term) {
                    $q->where(\DB::raw('lower(path_name)'), 'like', '%' . strtolower($term) . '%');
                })->orWhereHas('subCommunity', function ($q) use ($term) {
                    $q->where(\DB::raw('lower(path_name)'), 'like', '%' . strtolower($term) . '%');
                })->orWhereHas('community', function ($q) use ($term) {
                    $q->where(\DB::raw('lower(path_name)'), 'like', '%' . strtolower($term) . '%');
                })->orWhereHas('owner', function ($q) use ($term) {
                    $q->where(\DB::raw('lower(mobile)'), 'like', '%' . strtolower($term) . '%');
                })->orWhereHas('price', function ($q) use ($term) {
                        $q->where(\DB::raw('lower(price)'), 'like', '%' . strtolower($term) . '%');
                    });
            });
        })->whereNotIn('publish_status',['UNPUBLISHED','REFUSED'])->orderBy('updated_at', 'DESC')->with('bed', 'price')->paginate();

        //->whereNotIn('publish_status', ['ARCHIVE'])
        return $this->sendResponse(true, 'Listings Fetched', [
            "data" => $data->items(),
            'current_page' => $data->currentPage(),
            'last_page' => $data->lastPage(),
            'per_page' => $data->perPage(),
            'total' => $data->total(),
            'prev_page_url' => $data->previousPageUrl(),
            'next_page_url' => $data->nextPageUrl(),
        ], 200);
    }

    /**
     *
     *
     * Generate Title for Event
     *
     * @group Calander
     * @authenticated
     * @header Accept application/json
     * @urlParam agent_id required The id of the agent. Example: 12
     * @urlParam type_id required The id of the event type. Example: 1
     * @bodyParam contact_id integer for contact_id. Example: 1
     * @bodyParam lead_id integer for lead_id. Example: 1

     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
     * {
    {
    "status": true,
    "code": 200,
    "message": "Title Generated",
    "data": {
    "title": "Meeting arranged with contact Shabnam Ashraf and lead Ali "
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
     * }
     * @response 401 scenario="Validation Error"
     * {
    {
    "status": false,
    "code": 422,
    "message": "Validation Error",
    "data": {
    "agent_id": [
    "The agent id field is required."
    ],
    "type_id": [
    "The type id field is required."
    ]
    },
    "page": 1,
    "per_page": 10
    }
     */

    public function generateTitle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "agent_id" => "required|exists:users,id",
            'type_id' => 'required|exists:event_types,id',
            'contact_id' => 'nullable|exists:contacts,id',
            'lead_id' => 'nullable|exists:leads,id',
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(false, 'Validation Error', $validator->errors(), 422);
        }

        $event_type = EventType::find($request->type_id);
        $agent = User::find($request->agent_id);
        $lead = Lead::find($request->lead_id);
        $contact_id = $request->contact_id;
        if ($contact_id && $lead) {
            $contact = Contact::find($contact_id);
            $title = $event_type->name . ' arranged with contact ' . $contact->first_name . ' ' . $contact->last_name . ' and lead ' . $lead->first_name . ' ' . $lead->last_name;
        } else {
            if ($contact_id) {
                $contact = Contact::find($contact_id);
                $title = $event_type->name . ' arranged with contact ' . $contact->first_name . ' ' . $contact->last_name;
            }
            if ($lead) {
                $title = $event_type->name . ' arranged with lead ' . $lead->first_name . ' ' . $lead->last_name;
            }

        }

        return $this->sendResponse(true, 'Title Generated', [
            "title" => $title,
        ], 200);

    }

    /**
     *
     *
     *
     * Update Event
     * @group Calander
     * @authenticated
     * @header Accept application/json

     * @bodyParam id integer for event id. Example: 12
     * @bodyParam agent_id integer for agent_id. Example: 12
     * @bodyParam start_time string for start_time. Example: 2021-09-09 10:00
     * @bodyParam end_time string for end_time. Example: 2021-09-09 11:00
     * @bodyParam event_type_id integer for event_type_id. Example: 1
     * @bodyParam property_id integer for property_id. Example: 1
     * @bodyParam listing_lead_id integer for listing_lead_id. Example: 1
     * @bodyParam priority string for priority. Example: High
     * @bodyParam title string for title. Example: Meeting
     * @bodyParam location string for location. Example: Dubai
     * @bodyParam description string for description. Example: Meeting with client
     * @bodyParam attendees array for attendees. Example: [1,2,3]
     * @bodyParam send_notification boolean for send_notification. Example: 1
     * @bodyParam reminder_to_attendees boolean for reminder_to_attendees. Example: 1
     * @bodyParam contact_id integer for contact_id. Example: 1
     * @bodyParam lead_id integer for lead_id. Example: 1
     * @bodyParam all_attendees string for all_attendees. Example: on
     * @bodyParam all_team_attendees string for all_team_attendees. Example: on
     * @bodyParam reminder string for reminder. Example: 5
     * @bodyParam reminder_frequency string for reminder_frequency. Example: Minutes
     * @bodyParam event_recurring boolean for event_recurring. Example: 1
     * @bodyParam recurring_frequency string for recurring_frequency. Example: daily
     *
     *
     *
     *
     * @header Accept application/json
     * @header X-Requested-With XMLHttpRequest

     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
     * {

    "status": "1",
    "msg": "Event Updated Successfully",
    "data": []

     *}
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @response 200 scenario="Owner not found"
     * {

     */

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            "id" => "required|exists:events,id",
            "agent_id" => "required|exists:users,id",
            "start_time" => "required",
            "end_time" => "required",
            "event_type_id" => "required|exists:event_types,id",
            "priority" => "nullable|string",
            "title" => "required|string",
            "location" => "required|string",
            "description" => "nullable|string",
            "attendees" => "nullable|array",
            "attendees.*" => "nullable|exists:users,id",
            "email_to_attendees" => "nullable",
            "reminder" => "nullable|numeric",
            "reminder_frequency" => "nullable|string",
            "reminder_to_attendees" => "nullable",
            "contact_id" => "nullable|exists:contacts,id",
            "lead_id" => "nullable|exists:leads,id",
            'property_id' => 'nullable|exists:properties,id',
        ]);
        if ($request->reminder_to_attendees == 1 && $request->reminder < 5 && $request->reminder_frequency == 'Minutes') {
            return $this->sendresponse(false, 'Reminder should be equal or greater than 5 minutes.', [], 200);
            // return response()->json(['status' => '0', 'msg' => 'Reminder should be equal or greater than 5 minutes.']);
        }
        try {
            $validatedData["updated_by_id"] = auth()->id();
            $validatedData["send_notification"] = isset($request->send_notification) ?? 0;

            $validatedData["reminder_to_attendees"] = $request->boolean("reminder_to_attendees");
            $start_date = date('Y-m-d H:i', strtotime($validatedData["start_time"]));
            $end_date = date('Y-m-d H:i', strtotime($validatedData["end_time"]));
            $event = Event::find($request->id);
            $event->agent_id = $validatedData["agent_id"];
            $event->contact_id = $validatedData['contact_id'] ?? null;
            $event->lead_id = $validatedData['lead_id'] ?? null;
            $event->property_id = $validatedData["property_id"] ?? null;
            $event->start_time = $start_date;
            $event->end_time = $end_date;
            $event->event_type_id = $validatedData["event_type_id"];
            $event->priority = $validatedData["priority"] ?? null;
            $event->title = $validatedData['title'];
            $event->location = $validatedData['location'];
            $event->description = $validatedData['description'];
            $event->created_by_id = auth()->id();
            $event->send_notification = $validatedData["send_notification"];
            $event->reminder = $validatedData['reminder'];
            $event->reminder_frequency = isset($validatedData['reminder_frequency']) ? $validatedData['reminder_frequency'] : null;
            $event->reminder_to_attendees = isset($validatedData['reminder_to_attendees']) ? $validatedData['reminder_to_attendees'] : 0;
            $event->save();

            if (isset($request->all_attendees) && $request->all_attendees == 'on') {
                $all_user = User::where('status', '1')->pluck('id');
                $event->attendees()->sync($all_user ?? []);
            } else {
                $event->attendees()->sync($request->attendees ?? []);
            }

//            //google calendar sync

            //    if (!is_null($event->google_calendar_event_id)) {
            //        $user = User::find($event->agent_id);
            //        $userOAuthToken = UserOAuthToken::where("user_id", $user->id)->where("type", "google")->first();
            //        if (!is_null($userOAuthToken)) {

            //            $event->load(["attendees:id,email"]);
            //            $attendees = $event->attendees->pluck("email")->map(function ($data) {
            //                return ["email" => $data];
            //            })->all();
            //            if ($this->checkIfGoogleAccessTokenExipred($userOAuthToken->token)) {
            //                $accessToken = $this->regenerateGoogleAccessToken($userOAuthToken->token);
            //                $userOAuthToken->token = $accessToken;
            //                $userOAuthToken->save();
            //            }
            //            $googleCalendarEventId = $this->updateEvent($event, $user->email, $userOAuthToken->token, $attendees);
            //            // $googleCalendarEventId = $this->updateEvent($event, env("GOOGLE_CALENDAR_ID"), $userOAuthToken->token, $attendees); // Use this for testing
            //            if (!is_null($googleCalendarEventId)) {
            //                $event->google_calendar_event_id = $googleCalendarEventId;
            //                $event->save();
            //            }
            //        }
            //    }

            event(new EventUpdated($event));
            // event(new EventCreatedMobile($event, 'updated'));

            return $this->sendResponse(true, 'Event Updated Successfully', [], 200);
            return response()->json(['status' => '1', 'msg' => 'Event updated Successfully.']);

        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => "Error in event Controller " . $exception->getMessage());
        }
        return json_encode($result);
    }

/**
 *
 *
 *
 * Get Event
 * @group Calander
 * @authenticated
 * @header Accept application/json

 * @bodyParam id integer for event id. Example: 12
 *
 * @header Accept application/json
 * @header X-Requested-With XMLHttpRequest

 *
 * @return \Illuminate\Http\JsonResponse
 * @response 200 scenario="Data found"
{
"status": true,
"code": 200,
"message": "Event Found",
"data": {
"data": {
"id": 16324,
"event_type_id": 2,
"google_calendar_event_id": null,
"start_time": "2024-08-20 12:50:00",
"end_time": "2024-08-20 13:01:00",
"priority": "High",
"event_status": null,
"title": "Meeting arranged with contact Sageer Hussain Zardari and lead Test Lead",
"location": "Dubai",
"description": "Meeting with client",
"invitation": null,
"reminder_status": 0,
"send_notification": 1,
"reminder_to_attendees": 0,
"reminder_date": null,
"reminder": 5,
"reminder_frequency": "Minutes",
"last_reminded": null,
"recurring": 0,
"recurring_data": null,
"agent_id": 172,
"contact_id": 18777,
"lead_id": 276351,
"property_id": null,
"created_by_id": 172,
"updated_by_id": null,
"status": "PENDING",
"public": 0,
"note": null,
"created_at": "2024-08-20T08:26:07.000000Z",
"updated_at": "2024-08-20T13:21:05.000000Z",
"deleted_at": null,
"listing_lead_id": null,
"attendees": [],
"contact": {
"id": 18777,
"bitrix_id": null,
"bitrix_index": 0,
"my_crm_id": null,
"title": null,
"first_name": "Sagheer",
"last_name": "Hussain",
"agent_id": 172,
"previous_agent_id": null,
"mobile": "+971585830076",
"email": "sagheer@test.com",
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
"contact_source_id": 129,
"contact_status_id": null,
"created_by_id": 172,
"updated_by_id": null,
"deleted_at": null,
"created_at": "2024-08-08T08:20:46.000000Z",
"updated_at": "2024-08-08T08:20:46.000000Z",
"deleted_by_id": null
},
"lead": {
"id": 276351,
"bitrix_id": null,
"my_crm_id": null,
"title": "Test Lead  -  - ",
"property_id": 16633,
"mail_number": null,
"first_name": "Test Lead",
"last_name": null,
"email": null,
"phone": "+971585073972",
"mobile": null,
"whatsapp": null,
"address": null,
"company": null,
"dob": null,
"gender": null,
"priority": null,
"nationality_id": null,
"country_id": null,
"state_id": null,
"city_id": null,
"lead_status_id": 9,
"lead_sub_status_id": null,
"lead_type_id": null,
"lead_source_id": null,
"source_id": 2,
"type_id": 2,
"lead_medium_id": 8,
"agent_id": 172,
"previous_agent_id": null,
"telesales_id": null,
"telesales_head_id": null,
"lead_assignment_date": null,
"follow_up_date": "2024-08-20",
"viewing_date": null,
"created_by_id": 172,
"updated_by_id": 172,
"for": [
"Residential for sale",
"Residential for rent"
],
"status": 1,
"region_id": 1,
"warning_sent": 0,
"assigned_agent_at": "2024-08-06 10:53:14",
"converted_to_listing_lead": null,
"is_archive": 0,
"subject": null,
"message": null,
"note": null,
"reason": null,
"pf_tracking_link": null,
"need_to_be_destroyed": 0,
"deleted_at": null,
"created_at": "2024-08-06T06:53:14.000000Z",
"updated_at": "2024-08-20T08:05:44.000000Z",
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
"id": 172,
"bitrix_role": null,
"entity": null,
"group_id": null,
"parent_id": 173,
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
"last_login": "2024-08-15 16:02:45",
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
"call_tracking": 0,
"call_lead": 0,
"is_commercial": 0,
"created_by_id": 1,
"updated_by_id": 172,
"otp": "628061",
"created_at": "2023-02-15T06:42:31.000000Z",
"updated_at": "2024-08-16T10:47:48.000000Z",
"deleted_at": null,
"google2fa_secret": null,
"telegram2fa_secret": null,
"back_up_google2fa_secret": "OFVVWRMYVTGVLVIS",
"login_attempt": 0,
"joining_date": "2019-02-28 11:39:59",
"last_deal_date": null,
"is_report_exempt": 0,
"zone_id": null,
"full_name": "Muhammad Saim Aziz",
"thumb_url": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/thumb_168457219720230520124317.jpg",
"image_url": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/168457219720230520124317.jpg",
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
},
"created_by": {
"id": 172,
"bitrix_role": null,
"entity": null,
"group_id": null,
"parent_id": 173,
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
"last_login": "2024-08-15 16:02:45",
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
"call_tracking": 0,
"call_lead": 0,
"is_commercial": 0,
"created_by_id": 1,
"updated_by_id": 172,
"otp": "628061",
"created_at": "2023-02-15T06:42:31.000000Z",
"updated_at": "2024-08-16T10:47:48.000000Z",
"deleted_at": null,
"google2fa_secret": null,
"telegram2fa_secret": null,
"back_up_google2fa_secret": "OFVVWRMYVTGVLVIS",
"login_attempt": 0,
"joining_date": "2019-02-28 11:39:59",
"last_deal_date": null,
"is_report_exempt": 0,
"zone_id": null,
"full_name": "Muhammad Saim Aziz",
"thumb_url": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/thumb_168457219720230520124317.jpg",
"image_url": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/168457219720230520124317.jpg",
"role_list": [
"Agent"
]
}
}
},
"page": 1,
"per_page": 10
}
 * @param  \Illuminate\Http\Request  $request
 * @return \Illuminate\Http\Response
 * @response 200 scenario="Owner not found"
 * {

 */
    public function getEvent(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "id" => "required|exists:events,id",

        ]);

        if ($validator->fails()) {
            return $this->sendResponse(false, 'Validation Error', $validator->errors(), 422);
        }

        $event = Event::find($request->id);

        if (auth()->user()->hasRole(['Admin', 'Super Admin', "Head of Secondary", "CEO"])) {
            $event = Event::find($request->id);
        } else {
            $notAdmin = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
            $childrenIds = [];

            if ($notAdmin) {
                $childrenIds = getUserChildrenIdsOnly(auth()->user()->id);
                $childrenIds[] = auth()->user()->id;

            }

            $event = Event::where('id', $request->id)->where(function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
                $q->orWhereHas("attendees", fn($q1) => $q1->where("user_id", auth()->user()->id));
            })->first();
        }
        if (!is_null($event)) {
            $event->load("attendees", "contact", "lead", "property", "agent", "type", "createdBy");
            // use resource
            $event = new EventResource($event);

            return $this->sendResponse(true, 'Event Found', ['data' => $event], 200);
        } else {
            return $this->sendResponse(false, 'Event Not Found', [], 200);
        }

    }

    /**
     *
     *
     *
     * Get Event Agents
     * @group Calander
     * @authenticated
     * @header Accept application/json

     * @bodyParam term string for searching. Example: abc
     *
     * @header Accept application/json
     * @header X-Requested-With XMLHttpRequest

     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
    {
    "status": true,
    "code": 200,
    "message": "Agents Fetched",
    "data": {
    "data": [
    {
    "text": " Bermet Begalieva",
    "id": 1296,
    "image": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/1701237655CBGdUmMxgX20231129100055.JPG",
    "mobile": "+971581648277",
    "email": "b.begalieva@axcapital.ae"
    }
    ]
    },
    "page": 1,
    "per_page": 10
    }
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @response 200 scenario="Owner not found"
     * {

     */
    public function getAgents(Request $request)
    {
        $term = isset($request->term) ? $request->term : '';

        $users = User::where(function ($q) use ($term) {
            $q->where(\DB::raw('lower(name)'), 'like', '%' . strtolower($term) . '%')
                ->orWhere(function ($q) use ($term) {
                    $q->where(\DB::raw('lower(first_name)'), 'like', '%' . strtolower($term) . '%')
                        ->orWhere(\DB::raw('lower(last_name)'), 'like', '%' . strtolower($term) . '%');
                })
                ->orWhere(\DB::raw('lower(email)'), 'like', '%' . strtolower($term) . '%');
        })
            ->when($request->agent_id, function ($q) {
                $q->where("id", '!=', \request("agent_id"));
            })
            ->when($request->roles, function ($q) use ($request) {
                $q->whereHas("roles", fn($q1) => $q1->whereIn("name", $request->roles));
            })
            ->where('status', true)
            ->orderBy('users.name', 'ASC')
            ->limit(30)
            ->get();

        $data = [];
        $i = 0;
        foreach ($users as $user) {
            //$roles = !empty($user->roles) ? $user->roles[0]?->name : null;
            $data[$i]['text'] = $user->first_name . ' ' . $user->last_name;
            $data[$i]['id'] = $user->id;
            $data[$i]['image'] = $user?->image_url ?? null;
            $data[$i]['mobile'] = $user?->mobile ?? null;
            $data[$i]['email'] = $user?->email ?? null;
            $i++;

        }
        return $this->sendResponse(true, 'Agents Fetched', ['data' => $data], 200);
    }

    /**
     *
     *
     *
     * Get Module Event
     * @group Calander
     * @authenticated
     * @header Accept application/json

     * @bodyParam column string for column name. Example: contact_id,lead_id,property_id
     * @bodyParam value string for value. Example: 1
     * @bodyParam type string for type. Example: Meeting
     *
     * @header Accept application/json
     * @header X-Requested-With XMLHttpRequest

     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
    {
    "status": true,
    "code": 200,
    "message": "Event log found",
    "data": {
    "log": [
    {
    "id": 16771,
    "event_type_id": 2,
    "google_calendar_event_id": null,
    "start_time": "2024-08-22 12:57:00",
    "end_time": "2024-08-22 13:27:00",
    "priority": "Medium",
    "event_status": null,
    "title": "Meeting arranged with contact mohamed Saeed Mohamed Amer and lead Violetta",
    "location": "das",
    "description": "dasdasdsad",
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
    "agent_id": 1539,
    "contact_id": 18959,
    "lead_id": 282425,
    "property_id": 20875,
    "created_by_id": 262,
    "updated_by_id": null,
    "status": "PENDING",
    "public": 0,
    "note": null,
    "created_at": "2024-08-22T11:57:31.000000Z",
    "updated_at": "2024-08-22T11:57:31.000000Z",
    "deleted_at": null,
    "listing_lead_id": null,
    "attendees": [],
    "contact": {
    "id": 18959,
    "bitrix_id": null,
    "bitrix_index": 0,
    "my_crm_id": null,
    "title": null,
    "first_name": "mohamed Saeed",
    "last_name": "Mohamed Amer",
    "agent_id": 1539,
    "previous_agent_id": null,
    "mobile": "+971501259126",
    "email": "mohameds@gmail.com",
    "whatsapp": "+971501259126",
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
    "contact_source_id": 1,
    "contact_status_id": null,
    "created_by_id": 1539,
    "updated_by_id": 1539,
    "deleted_at": null,
    "created_at": "2024-08-21T06:19:12.000000Z",
    "updated_at": "2024-08-21T08:43:21.000000Z",
    "deleted_by_id": null
    },
    "lead": {
    "id": 282425,
    "bitrix_id": null,
    "my_crm_id": null,
    "title": "Violetta  - +79277024222 - ",
    "property_id": 20875,
    "mail_number": null,
    "first_name": "Violetta",
    "last_name": null,
    "email": null,
    "phone": null,
    "mobile": "+79277024222",
    "whatsapp": "+79277024222",
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
    "source_id": 4,
    "type_id": 1,
    "lead_medium_id": 4,
    "agent_id": 1539,
    "previous_agent_id": null,
    "telesales_id": null,
    "telesales_head_id": null,
    "lead_assignment_date": null,
    "follow_up_date": "2024-08-22",
    "viewing_date": null,
    "created_by_id": null,
    "updated_by_id": 262,
    "for": [
    "Residential for rent"
    ],
    "status": 1,
    "region_id": 1,
    "warning_sent": 0,
    "assigned_agent_at": "2024-08-21 14:28:31",
    "converted_to_listing_lead": null,
    "is_archive": 0,
    "subject": null,
    "message": "Hi, I am interested in your property on Bayut: https://www.bayut.com/pm/9316214/6cffb0f3-4268-40bc-b8fc-7742bbc10347\nRef: axc-3979167",
    "note": null,
    "reason": null,
    "pf_tracking_link": "https://ovation-live.bayut.com/ingest/whatsAppContactStatus/?external_id=9316214&type=property&source=imported&sender=79277024222&destination=79277024222&message_type=contact_lead&lc=en_US",
    "need_to_be_destroyed": 0,
    "deleted_at": null,
    "created_at": "2024-08-21T10:28:31.000000Z",
    "updated_at": "2024-08-22T11:57:31.000000Z",
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
    "property": {
    "id": 20875,
    "my_crm_id": null,
    "ref_no": "axc-3979167",
    "title": "Spacious Studio | Ready To Move | Chiller Free",
    "description": "AX CAPITAL Real Estate is proud to offer you a spacious studio for rent in Azizi Riviera 4, Meydan One.\r\n\r\nRiviera invites its residents to feel at home and be a part of a diverse and multicultural community. Family-friendly outdoor activities, leisure, dining, and retail therapy are a slice of everyday life. In more ways than one, Riviera is creating a new paradigm for community living in Dubai. \r\n\r\nAzizi Riviera, a development by Azizi Developments, blends French Mediterranean design with contemporary architecture to create an ideal setting for modern community living. \r\n\r\nThe project consists of 69 mid-rise residential buildings, a large integrated retail district, stunning waterfront vistas, and lush greenery. \r\n\r\nAmenities:\r\n\r\n- BBQ Seating\r\n- BOCCE\r\n- Zen Garden\r\n- Yoga Zone\r\n- Pets Agility\r\n- Lounge\r\n- Outdoor Play\r\n- Swimming Pool\r\n\r\nReference No. axc-3979167\r\n\r\nAX CAPITAL was built upon a desire to create a real estate agency of the modern era where the customer is central to everything we do. With our values clearly defined, we are committed to continuously evolving as a real estate agency and it is this passion and dedication that marks the foundation of AX CAPITAL and makes us different.\r\n\r\nExplore Our Services:\r\n\r\n- AX MANAGEMENT\r\n- AX DESIGN\r\n- AX CORPORATE",
    "listing_source": null,
    "for": "Residential for rent",
    "region_id": 1,
    "community_id": 23,
    "sub_community_id": 85,
    "community_property_id": 93,
    "property_category_id": 1,
    "bed_id": 1,
    "bath_id": 1,
    "unit_no": "-",
    "built_up_area": "397",
    "plot_size": null,
    "street": null,
    "floor": null,
    "view": "[\"Community View\"]",
    "parking": 1,
    "dewa_number": null,
    "rera_permit_number": "71138280029",
    "rera_issue_date": "2024-02-05",
    "rera_expiry_date": "2025-02-05",
    "rera_validation_url": "https://trakheesi.dubailand.gov.ae/rev/madmoun/listing/validation?khevJujtDig=aefex25kmi3sbajfzccnkal3b3n5abznk0yadvjvrgmnjqxtz",
    "property_type_id": null,
    "property_status_id": 12,
    "completion_status": "Ready Secondary",
    "rented_at": null,
    "rented_until": null,
    "next_available": null,
    "availability_date": "2024-07-12",
    "remind": null,
    "furnished": "UNFURNISHED",
    "featured": 0,
    "exclusive": 0,
    "exclusive_start": null,
    "exclusive_end": null,
    "hot": 1,
    "publish_status": "PUBLISHED",
    "admin_unpublish_reason": null,
    "agent_id": 1539,
    "previous_agent_id": null,
    "external_agent_id": null,
    "telesales_id": null,
    "owner_id": 18378,
    "developer_id": null,
    "cheques": "1",
    "mycrm_updated": 0,
    "created_by_id": 1539,
    "updated_by_id": 127,
    "video_link": null,
    "video_360_link": null,
    "qr_link": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/listings/qr/original_669113d89914b.png",
    "title_deed_text": "-",
    "data_version": 1,
    "is_archived": 0,
    "payment_method": "cash",
    "down_payment_price": "45000",
    "build_year": null,
    "created_at": "2024-07-11T06:32:11.000000Z",
    "updated_at": "2024-08-14T12:20:36.000000Z",
    "deleted_by_id": null,
    "deleted_at": null,
    "state": null,
    "is_live": 1,
    "request_status_type": "PUBLISHED",
    "PASSWORD": null,
    "villa_type": "[null]",
    "contract_start": "10-07-2024",
    "contract_end": "09-10-2024",
    "contract_number": null,
    "published_date": null,
    "unpublished_date": null,
    "unable_to_generate_qr": 0,
    "offplan_portal_fetch": 0,
    "republished_date": null,
    "first_image": {
    "id": 441408,
    "property_id": 20875,
    "title": null,
    "description": null,
    "path_old": null,
    "original": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/listings/original_172077955220240712141912T1spijqd9Z.jpg",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/listings/thumb_172077955220240712141912T1spijqd9Z.jpg",
    "path": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/listings/watermarkv3/172077955220240712141912T1spijqd9Z.jpg",
    "password": null,
    "note": null,
    "order": 1,
    "type": "Photos",
    "temp_image": 0,
    "not_uploaded_on_s3": 0,
    "status": 1,
    "media_changed": null,
    "temp_id": null,
    "created_by_id": 1601,
    "updated_by_id": 1601,
    "created_at": "2024-07-12T10:19:14.000000Z",
    "updated_at": "2024-08-12T08:31:45.000000Z",
    "deleted_at": null,
    "deleted_by_id": null
    },
    "category": "Apartment",
    "location": "AZIZI Riviera 4, Meydan One, Meydan, Dubai",
    "my_property": false,
    "property_category": {
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
    "created_at": "2023-02-14T03:30:33.000000Z",
    "updated_at": "2023-06-26T14:31:16.000000Z",
    "deleted_at": null
    },
    "community_property": {
    "id": 93,
    "region_id": 1,
    "community_id": 23,
    "sub_community_id": 85,
    "name": "AZIZI Riviera 4",
    "alternate_name": null,
    "slug": "azizi-riviera-4",
    "description": null,
    "path_name": "AZIZI Riviera 4, Meydan One, Meydan, Dubai",
    "alternate_path_name": null,
    "latitude": "25.17290779597",
    "longitude": "55.30276216117",
    "created_by_id": 1,
    "updated_by_id": 1,
    "default_use": 1,
    "alternate_use": 1,
    "status": 1,
    "created_at": "2020-04-29T19:54:06.000000Z",
    "updated_at": "2020-04-29T19:54:06.000000Z",
    "deleted_at": null
    }
    },
    "agent": {
    "id": 1539,
    "bitrix_role": "Secondary, Karim",
    "entity": null,
    "group_id": null,
    "parent_id": 17,
    "public_profile_id": null,
    "old_user_id": null,
    "slug": "dora-ben-amor",
    "title": null,
    "name": "Dora Ben Amor",
    "first_name": "Dora",
    "last_name": "Ben Amor",
    "position": "Property Consultant",
    "email": "d.amor@axcapital.ae",
    "secondary_email": null,
    "email_verified_at": null,
    "last_login": "2024-08-21 09:50:20",
    "mobile": "+971561772585",
    "api_access": 0,
    "api_rate_limit": 0,
    "telegram_username": null,
    "telegram_chatid": "5970238984",
    "call_tracking_user_id": null,
    "call_tracking_account_id": null,
    "bitrix_id": 260891,
    "website_link": null,
    "facebook_link": null,
    "instagram_link": null,
    "youtube_link": null,
    "linkedin_link": null,
    "bio": null,
    "license_no": "54728",
    "pp_enabled": 1,
    "pp_name": "Tamara Jamali",
    "pp_photo": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/171773433320240607082533.jpg",
    "pp_license_no": "66638",
    "country_id": null,
    "state_id": null,
    "nationality_id": 75,
    "community_id": null,
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/thumb_1714802430mWvcZO4FEJ20240504100030.JPG",
    "image": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/1714802430mWvcZO4FEJ20240504100030.JPG",
    "avatar": null,
    "color_code": null,
    "status": 1,
    "region_id": 1,
    "traccar_status": 0,
    "device_identifier": "1539",
    "port": "5505",
    "mic_status": 0,
    "device_token": "ebiik91yTgWa_jtHIORaEu:APA91bEtJu3SupP71u5o8LdynVyuEwcLHMjr8muaXR1bXDI_jXCOSdAJaD8gkZGQis3-PlZh8c8jy7VwUPgQLr1xKrVpCzmsR9CihlXnil5zHYeO1cSy9sXDfbaKBKOgj403UGJIORfN",
    "call_tracking": 0,
    "call_lead": 0,
    "is_commercial": 0,
    "created_by_id": null,
    "updated_by_id": 1539,
    "otp": null,
    "created_at": "2024-04-26T10:31:08.000000Z",
    "updated_at": "2024-08-21T05:50:20.000000Z",
    "deleted_at": null,
    "google2fa_secret": null,
    "telegram2fa_secret": null,
    "back_up_google2fa_secret": "RQGROPOEEKMBFJ7R",
    "login_attempt": 0,
    "joining_date": null,
    "last_deal_date": null,
    "is_report_exempt": 0,
    "zone_id": 6,
    "full_name": "Dora Ben Amor",
    "thumb_url": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/thumb_1714802430mWvcZO4FEJ20240504100030.JPG",
    "image_url": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/1714802430mWvcZO4FEJ20240504100030.JPG",
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
    ]
    },
    "page": 1,
    "per_page": 10
    }
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @response 200 scenario="Owner not found"
     * {

     */

    public function getEventAllLog(Request $request)
    { //this can be used for fetching events based on column name and value and type

        $validator = Validator::make($request->all(), [
            'column' => 'required', //ex. contact_id or lead_id or property_id
            'value' => 'required', //ex. 1
            'type' => 'nullable', //ex. name from events table
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(false, 'Validation Error', $validator->errors(), 422);
        }

        $type = EventType::where('name', $request->type)->first();

        $log = Event::with(['type'])->where($request->column, $request->value)
            ->when($request->type != null, function ($query) use ($type) {
                if ($type != null) {
                    return $query->where('event_type_id', $type->id);
                }
            })->with("attendees", "contact", "lead", "property", "agent", "type")->orderBy('id', 'desc')->limit(10)->get();
        if (count($log) > 0) {

            return $this->sendResponse(true, 'Event log found', ['log' => EventResource::collection($log)], 200);

        } else {
            return $this->sendResponse(false, 'Event log not found', [], 404);
        }
    }



    /**
     *
     *
     *
     * Delete Event
     * @group Calander
     * @authenticated
     * @header Accept application/json

     * @bodyParam id required for event id. Example: 12
     *
     * @header Accept application/json
     * @header X-Requested-With XMLHttpRequest

     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
    {
    "status": true,
    "code": 200,
    "message": "Event deleted successfully",
    "data": null,
    "page": 1,
    "per_page": 10
}
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @response 200 scenario="Owner not found"
     * {

     */

    public function delete(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:events',
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(false, 'Validation Error', $validator->errors(), 422);
        }
        if(auth()->user()->hasRole(["Admin", "Super Admin"])) {
            $event = Event::find($request->id);
            if ($event->delete()) {
                return $this->sendResponse(true, 'Event deleted successfully', null, 200);
            } else {
                return $this->sendResponse(false, 'Something went wrong!!', null, 500);

            }
            return response()->json($result);
        }else {
            if (Event::where('id', $request->id)->where('agent_id', auth()->id())->exists()) {
                $event = Event::find($request->id);
                if ($event->delete()) {
                    return $this->sendResponse(true, 'Event deleted successfully', null, 200);
                } else {
                    return $this->sendResponse(false, 'Something went wrong!!', null, 500);
                }
            } else {
                return $this->sendResponse(false, 'Something went wrong!! Contact Support', null, 500);
            }
            return response()->json($result);
        }
    }
}
