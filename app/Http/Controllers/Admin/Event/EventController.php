<?php

namespace App\Http\Controllers\Admin\Event;

use App\Events\EventCreated;
use App\Events\EventCreatedMobile;
use App\Events\EventUpdated;
use App\Http\Controllers\Controller;
use App\Jobs\CreateGoogleCalendarEvent;
use App\Jobs\RecurringEventCreateJob;
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
use Illuminate\Support\Facades\Gate;

class EventController extends Controller
{
    use GoogleCalendarEventTrait, OAuthTrait;

    public function index()
    {
        abort_if(Gate::denies("Events List"), 401);
        $notAdmin = auth()->user()->hasRole(["Sales Director", "Line Manager", "Agent", "Photographer"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }
        $childrenIds[] = auth()->id();

        $data['agents'] = User::whereDoesntHave("roles", function ($q) {
            $q->whereIn("name", ["Admin", "Super Admin", "Telesales", "Telesales Head"]);
        })->when($notAdmin, fn($q) => $q->whereIn("id", $childrenIds))
            ->where("status", true)->get();
        $data['event_types'] = EventType::where('status', true)->orderBy('order', 'ASC')->get();
        $data['upcoming_events'] = Event::where('agent_id', auth()->id())->where('start_time', '>=', Carbon::now())->limit('10')->with('type')->get();
        return view("admin.events.index")->with($data);
    }
    public function index2()
    {
        abort_if(Gate::denies("Events List"), 401);
        $notAdmin = auth()->user()->hasRole(["Sales Director", "Line Manager", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }
        $data['agents'] = User::whereHas("roles", function ($q) {
            $q->whereIn("name", ["Agent"]);
        })->when($notAdmin, fn($q) => $q->whereIn("parent_id", $childrenIds))->where("status", true)->get();
        return view("admin.events.calendar")->with($data);
    }
    public function eventCalendar(Request $request)
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

        foreach ($events ?? [] as $event) {
            $data[] = [
                'title' => $event->title,
                'start' => $event->start_time,
                'id' => $event->id,
                'end' => $event->end_time,
                'className' => ($typeBadge[$event->event_type_id] ?? "") . ' text-white', //eventTitleBadgeColor($event->type)

            ];
        }

        return response($data);
    }
    public function getFormModal(Request $request)
    {
        $user_id = null;
        $user = null;
        $users = null;
        $properties = null;
        $property = null;
        $type = null;
        $showProperty = false;
        $showContact = "d-none";
        $showLead = "d-none";
        $property_id = null;
        $contact_id = null;
        $lead_id = null;
        $date = '';
        $title = '';

        $listing_lead_id = null;
        if (gettype($request->data) == 'array' && !empty($request->data)) {
            $data = $request->data;
            $listing_lead_id = $data['listing_lead']['id'] ?? null;
            $date = date('d-m-Y', strtotime($data['date'])) . ' 12:00:00';

            if (!empty($data['user'])) {
                $user_id = $data['user']['id'];
            }
            if (!empty($data['listing_lead'])) {
                $listing_lead_id = $data['listing_lead']['id'] ?? null;
                $listing_lead_name = $data['listing_lead']['name'] ?? null;
            }
            if (!empty($data['property'])) {
                $property_id = $data['property']['id'];
            }
            if (!empty($data['contact'])) {
                $contact_id = $data['contact']['id'];
            }
            if (!empty($data['lead'])) {
                $lead_id = $data['lead']['id'];
                $showLead = "";
            }
            if (!empty($data['date'])) {
                $date = $data['date'];
            }
            if (!empty($data['type'])) {
                $type = $data['type'];
                if ($type == 'Viewing' || $type == 'Property Shoot') {
                    $showProperty = true;
                    $showLead = "";
                } elseif ($type == 'Meeting' || $type == 'Task') {
                    $showProperty = false;
                    $showContact = $showLead = "";
                }
            }
        }

        if ($user_id != null) {
            $user = User::where('id', $user_id)->select('id', 'name', 'first_name', 'last_name', 'email', 'mobile')->first();
        } else {
            $user = User::where('id', auth()->user()->id)->select('id', 'name', 'first_name', 'last_name', 'email', 'mobile')->first();
            $users = null;
        }
        $notAdmin = $user->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {

            $childrenIds = getUserChildrenIdsOnly($user_id);
            $childrenIds[] = $user->id;
        }

        $property = $property_id != '' ? Property::where('id', $property_id)->with('owner')->first() : null;

        $contact = $contact_id != '' ? Contact::where('id', $contact_id)->first() : null;
        $lead = $lead_id != '' ? Lead::where('id', $lead_id)->first() : null;

        $event_types = EventType::where('status', true)->orderBy('order', 'ASC')->get();
        $event_type = EventType::where('name', $type)->first() ?? null;

        //        $agents = User::whereHas("roles", function ($q) {
        //            $q->whereIn("name", ["Agent"]);
        //        })->where("status", true)->get();

        if ($event_type?->name == 'Viewing') {

            if ($lead != null) {
                $title = 'Viewing arranged with lead ' . ($lead?->first_name ?? "") . ' ' . ($lead?->last_name ?? "") . ' for ' . ($property->category ?? "") . ' ' . ($property->unit_no ?? "") . ' -' . ($property->location ?? "") . ' Property Reference Number' . ($property->ref_no ?? "") . ' ';

                if (empty($property)) {

                    $properties = Property::where('agent_id', $user_id)->where('publish_status', 'PUBLISHED')->get();
                }
            } else {
                $title = 'Viewing arranged for  ' . ($property->category ?? "") . ' ' . ($property->unit_no ?? "") . ' -' . ($property->location ?? "") . ' ' . ($property->ref_no ?? "");
            }
        }
        if ($event_type?->name == 'Task') {

            $title = 'Some task has been scheduled with ' . ($contact?->first_name ?? "") . ' ' . ($contact?->last_name ?? "") . '' . ($lead?->first_name ?? "") . ' ' . ($lead?->last_name ?? "");
        }
        if ($event_type?->name == 'Meeting') {
            if ($listing_lead_id != null) {
                $title = 'A meeting has been scheduled with listing lead - ' . $listing_lead_name;
            } else {
                $title = 'A meeting has been scheduled with ' . ($contact?->first_name ?? "") . ' ' . ($contact?->last_name ?? "") . '' . ($lead?->first_name ?? "") . ' ' . ($lead?->last_name ?? "");
            }
        }
        if ($event_type?->name == 'Property Shoot') {
            $showLead = "d-none";
            if (empty($property)) {

                $properties = Property::whereIn('agent_id', $childrenIds)->with('owner')->get();
            }
        }
        if ($event_type?->name == 'Follow-up Reminder') {
            $showContact = "";
            $showLead = "";
            if ($property != null && $lead != null) {
                $title = 'Follow-up reminder has been set for ' . ($lead?->first_name ?? "") . ' ' . ($lead?->last_name ?? "");
            } elseif ($property != null) {
                $title = 'Follow-up reminder has been set for -';
            } elseif ($listing_lead_id != null) {
                $title = 'Follow-up reminder has been set for - listing lead ' . $listing_lead_name;
            } else {
                $title = 'Follow-up reminder has been set for ' . ($contact?->first_name ?? "") . ' ' . ($contact?->last_name ?? "") . ' ' . ($lead?->first_name ?? "") . ' ' . ($lead?->last_name ?? "");
            }
        }
        if ($listing_lead_id != null) {
            $showProperty = false;
            $showContact = "d-none";
            $showLead = "d-none";
        }

        $html = view('admin.events.include.addModal', get_defined_vars())->render();

        return response()->json(['status' => true, 'html' => $html, 'property' => $showProperty, 'contact' => $showContact, 'lead' => $showLead, 'title' => $title, 'date' => $date]);
    }
    public function generateDatesBetweenMonths(string $startDate, ?string $endDate, array $days, int $months = null): array
    {
        $startDate = Carbon::parse($startDate);

        if (!empty($endDate)) {
            $endDate = Carbon::parse($endDate);
            $months = $endDate->diffInMonths($startDate) + 1;
        }

        $dates = [];
        for ($i = 0; $i < $months; $i++) {
            $dates[] = $startDate->copy()->addMonths($i)->format('Y-m-d');

            //            foreach ($days as $day) {
            //                $date = $currentMonth->startOfMonth()->next($day);
            //                if ($date <= $startDate) {
            //                    $date = $date->addWeek();
            //                }
            //
            //                if ($endDate && $date > $endDate) {
            //                    continue;
            //                }
            //
            //                $dates[] = $date->format('Y-m-d');
            //            }
        }

        return $dates;
    }
    public function generateDatesBetweenWeeks($startDate, $endDate = null, $daysArray, $numOfWeeks = null)
    {
        //        echo 'Start Date: '.$startDate.'<br>';
        //        echo 'End Date: '.$endDate.'<br>';
        //        echo 'Days Array: '.json_encode($daysArray).'<br>';
        //        echo 'Number of Weeks: '.$numOfWeeks.'<br>';
        $dates = [];

        // Create Carbon instances for start and end dates
        $start = Carbon::parse($startDate)->startOfDay();
        $end = $endDate ? Carbon::parse($endDate)->startOfDay() : null;

        // Calculate the end date if it's not provided
        if (!$end && $numOfWeeks) {
            $end = $start->copy()->addWeeks($numOfWeeks);
        }

        // Calculate the number of weeks if it's not provided
        if (!$numOfWeeks && $end) {
            $numOfWeeks = $start->diffInWeeks($end);
        }

        // Adjust start date to the first occurrence of the daysArray
        // $start->next($daysArray[0]);
        //$start->next($daysArray[0]);

        // Iterate through each week
        for ($i = 0; $i <= $numOfWeeks; $i++) {
            // echo 'Week: '.$i.'<br>';
            // Iterate through each day of the week
            foreach ($daysArray as $day) {

                // Find the next occurrence of the specified day
                $nextOccurrence = $start->copy()->next($day);

                // Check if the next occurrence falls within the date range
                if ($end && $nextOccurrence->gt($end)) {
                    // If end date provided and next occurrence is beyond end date, stop generating
                    break 2;
                }

                if (!$end || $nextOccurrence->lte($end)) {
                    // Add the date to the array if within range
                    $dates[] = $nextOccurrence->toDateString();
                    //  echo $nextOccurrence->toDateString().'<br>';
                }
            }

            // Move the start date to the next week
            $start->addWeek();
        }

        return $dates;
    }

    public function generateDailyDates(string $startDate, string $startTime, ?string $endDate, ?int $recurringCount): array
    {

        $startDate = Carbon::parse($startDate);
        $dates = [];
        if ($recurringCount == 0 || $recurringCount == null || $recurringCount == '') {
            $endDate = Carbon::parse($endDate);
            $recurringCount = $startDate->diffInDays($endDate);
        }
        $recurringCount = $recurringCount + 1;

        if ($endDate != null || $endDate != '') {
            $i = 0;
            $recurringCount = $recurringCount + 1;
        }

        for ($i = 1; $i < $recurringCount; $i++) {

            $currentDate = $startDate->copy()->addDay($i);
            if ($endDate && $currentDate > $endDate) {
                $dates[] = $currentDate->format('Y-m-d') . ' ' . $startTime;
                break;
            }
            $dates[] = $currentDate->format('Y-m-d') . ' ' . $startTime;
        }

        return $dates;
    }

    protected function generateRecurringDates($request, $event)
    {
        $recurring_count = $request->recurring_count;
        $recurring_end_date = $request->recurring_end_date;
        $start_date = date('Y-m-d H:i', strtotime($request->start_time));
        $end_date = date('Y-m-d H:i', strtotime($request->end_time));
        $recurring_dates = [];
        $start_time = date('H:i', strtotime($request->start_time));
        $dates = [];

        if ($request->recurring_date != '') {
            $recurring_date = date('Y-m-d', strtotime($request->recurring_date));
            $dates[] = $recurring_date . ' ' . $start_time;
            return $dates;
        }
        if ($request->reoccurringDays != '') {
            if (is_array($request->reoccurringDays)) {
                $reoccurringDays = $request->reoccurringDays;
            } else {
                $reoccurringDays = explode(',', $request->reoccurringDays);
            }
        } else {
            $reoccurringDays = [];
        }
        if ($request->recurring_frequency == 'daily') {
            $dates = $this->generateDailyDates($start_date, $start_time, $recurring_end_date, $recurring_count);
        } else if ($request->recurring_frequency == 'weekly') {

            $dates = $this->generateDatesBetweenWeeks($start_date, $recurring_end_date, $reoccurringDays, $recurring_count);
        } else if ($request->recurring_frequency == 'monthly') {

            $dates = $this->generateDatesBetweenMonths($start_date, $recurring_end_date, $reoccurringDays, $recurring_count);
        }

        return $dates;
    }

    protected function createRecurringEvent($request, $event)
    {
        $start_date = date('Y-m-d H:i', strtotime($request->start_time));

        $end_date = date('H:i', strtotime($request->end_time));

        $dates = $this->generateRecurringDates($request, $event->start_time);

        $event_date = date('Y-m-d', strtotime($event->start_time)); // because selected start time event is already created

        $key = array_search($event_date, $dates);
        if ($key !== false) {
            unset($dates[$key]);
        }
        //        echo '<pre>';
        //        print_r($dates);
        //        echo '</pre>';
        //        exit();
        if (count($dates) > 0) {
            foreach ($dates ?? [] as $dateTime) {
                $request_data = $request->all();
                RecurringEventCreateJob::dispatch($request_data, $dateTime, $end_date, $event);
                // echo $request->start_time;
                //        $event = new Event();
                //        $event->agent_id = $request->agent_id;
                //        $event->contact_id = $request->contact_id ?? null;
                //        $event->lead_id = $request->lead_id ?? null;
                //        $event->property_id = $request->property_id ?? null;
                //        $event->listing_lead_id = $request->listing_lead_id ?? null;
                //        $event->start_time = $dateTime;
                //        $event->end_time = Carbon::parse($dateTime)->format('Y-m-d').' '.$end_date;
                //        $event->event_type_id = $request->event_type_id ?? null;
                //        $event->priority = $request->priority ?? null;
                //        $event->title = $request->title;
                //        $event->location = $request->location;
                //        $event->description = $request->description;
                //        $event->created_by_id = auth()->id();
                //        $event->reminder = $request->reminder;
                //        $event->send_notification = false;
                //        $event->reminder_frequency = $request->reminder_frequency;
                //        $event->reminder_to_attendees = false;
                //        $event->save();
                //
                //        if(isset($request->all_attendees) && $request->all_attendees == 'on'){
                //            $all_user = User::where('status','1')->pluck('id');
                //            $event->attendees()->sync($all_user ?? []);
                //        }elseif(isset($request->all_team_attendees) && $request->all_team_attendees == 'on'){
                //            $childrenIds = getUserChildrenIdsOnly(auth()->user()->id);
                //            $event->attendees()->sync($childrenIds ?? []);
                //        }else{
                //            $event->attendees()->sync($request->attendees ?? []);
                //        }
            }
        }
    }
    public function getEventSummary(Request $request)
    {
        $dates = $this->generateRecurringDates($request, null) ?? [];
        if (count($dates) > 0) {
            $message = 'Events on Following dates will be created:<br>';
            foreach ($dates as $date) {
                $message .= date('d/m/y', strtotime($date)) . '<br>';
            }
            $message .= 'Total ' . count($dates) . ' events will be created';
            return response()->json(['status' => true, 'message' => $message, 'error' => '']);
        } else {
            return response()->json(['status' => false, 'msg' => 'No dates found']);
        }
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
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
        if ($request->reminder_to_attendees == 1 && $request->reminder < 5 && $request->reminder_frequency == 'Minutes') {
            return response()->json(['status' => '0', 'msg' => 'Reminder should be equal or greater than 5 minutes.']);
        }
        if ($request->event_recurring == true) {

            if ($request->recurring_frequency == 'daily') {
                //check if recurring count is not empty or recurring end date is not null either one is required
                if ($request->recurring_count == '' && $request->recurring_end_date == null) {
                    return response()->json(['status' => '0', 'msg' => 'Recurring count or recurring end date is required for daily recurring event.']);
                }
            } else if ($request->recurring_frequency == 'weekly') {
                $reoccurringDays = $request->reoccurringDays != '' ? explode(',', $request->reoccurringDays) : [];

                if ($reoccurringDays == null || count($reoccurringDays) == 0) {
                    return response()->json(['status' => '0', 'msg' => 'Recurring days is required for weekly recurring event.']);
                }
            }
        }


         try {
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
            $property_id_from_event = $event->property_id ?? null;
            if (is_null($lead->property_id) && $property_id_from_event != null) {
                $lead->property_id = $property_id_from_event;
            }
            $lead->save();
        }
        $arr = [
            'attributes' => $dirty,
            'old' => $original,
            'lead_id' => $event->lead_id,
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
            $lead_status = LeadStatus::where('name', 'Qualified')->first();
            $lead = Lead::find($event->lead_id);
            $lead->lead_status_id = $lead_status->id;
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
            //CreateGoogleCalendarEvent
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
        //if($validatedData["send_notification"]) {
        event(new EventCreated($event));
        event(new EventCreatedMobile($event, 'created'));

        //  }

        return response()->json(['status' => '1', 'msg' => 'Event Added Successfully.']);

                } catch (\Exception $exception) {
                    $result = array('status' => '0',  'msg' => $exception->getMessage());
                }
    }

    public function getEvent(Request $request)
    {
        $request->validate([
            "id" => "required|exists:events,id",
        ]);
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
            return response()->json(['status' => '1', 'msg' => 'Event Found', 'data' => $event]);
        } else {
            return response()->json(['status' => '0', 'msg' => 'Event Not Found 2']);
        }
    }

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
        ]);
        if ($request->reminder_to_attendees == 1 && $request->reminder < 5 && $request->reminder_frequency == 'Minutes') {
            return response()->json(['status' => '0', 'msg' => 'Reminder should be equal or greater than 5 minutes.']);
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
            if (env('APP_ENV') == 'production') {
                if (!is_null($event->google_calendar_event_id)) {
                    $user = User::find($event->agent_id);
                    $userOAuthToken = UserOAuthToken::where("user_id", $user->id)->where("type", "google")->first();
                    if (!is_null($userOAuthToken)) {

                        $event->load(["attendees:id,email"]);
                        $attendees = $event->attendees->pluck("email")->map(function ($data) {
                            return ["email" => $data];
                        })->all();
                        if ($this->checkIfGoogleAccessTokenExipred($userOAuthToken->token)) {
                            $accessToken = $this->regenerateGoogleAccessToken($userOAuthToken->token);
                            $userOAuthToken->token = $accessToken;
                            $userOAuthToken->save();
                        }
                        $googleCalendarEventId = $this->updateEvent($event, $user->email, $userOAuthToken->token, $attendees);
                        // $googleCalendarEventId = $this->updateEvent($event, env("GOOGLE_CALENDAR_ID"), $userOAuthToken->token, $attendees); // Use this for testing
                        if (!is_null($googleCalendarEventId)) {
                            $event->google_calendar_event_id = $googleCalendarEventId;
                            $event->save();
                        }
                    }
                }
                //            // google calendar sync end
            }
            event(new EventUpdated($event));
            event(new EventCreatedMobile($event, 'created'));

            return response()->json(['status' => '1', 'msg' => 'Event updated Successfully.']);
        } catch (\Exception $exception) {
            // dd($exception);
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => "Error in event Controller " . $exception->getMessage());
        }
        return json_encode($result);
    }
    public function updateDate(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:events',
        ]);
        $newDate = Carbon::parse($request->date)->format('Y-m-d');
        $event = Event::find($request->id);
        $start_time = Carbon::parse($event->start_time);
        $end_time = Carbon::parse($event->end_time);
        $event->start_time = Carbon::parse($newDate . ' ' . $start_time->toTimeString());
        $event->end_time = Carbon::parse($newDate . ' ' . $end_time->toTimeString());
        if ($event->save()) {
            $result = array('status' => '1', 'msg' => 'Event updated successfully');
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return response()->json($result);
    }
    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:events',
        ]);
        if (auth()->user()->hasRole(["Admin", "Super Admin"])) {
            $event = Event::find($request->id);

            //            //google calendar delete
            //            if(!is_null($event->google_calendar_event_id)){
            //                $user = User::find($event->agent_id);
            //                $userOAuthToken = UserOAuthToken::where("user_id", $user->id)->where("type", "google")->first();
            //                if (!is_null($userOAuthToken)) {
            //                    if ($this->checkIfGoogleAccessTokenExipred($userOAuthToken->token)) {
            //                        $accessToken = $this->regenerateGoogleAccessToken($userOAuthToken->token);
            //                        $userOAuthToken->token = $accessToken;
            //                        $userOAuthToken->save();
            //                    }
            //                    $this->deleteEvent($event, $user->email, $userOAuthToken->token);
            //                    // $this->deleteEvent($event, env("GOOGLE_CALENDAR_ID"), $userOAuthToken->token); // Use this for testing
            //                }
            //            }  //google calendar delete end

            if ($event->delete()) {
                $result = array('status' => '1', 'msg' => 'Event deleted successfully');
            } else {
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }
            return response()->json($result);
        } else {
            if (Event::where('id', $request->id)->where('agent_id', auth()->id())->exists()) {
                $event = Event::find($request->id);
                //                //google calendar delete
                //                if(!is_null($event->google_calendar_event_id)){
                //                    $user = User::find($event->agent_id);
                //                    $userOAuthToken = UserOAuthToken::where("user_id", $user->id)->where("type", "google")->first();
                //                    if (!is_null($userOAuthToken)) {
                //                        if ($this->checkIfGoogleAccessTokenExipred($userOAuthToken->token)) {
                //                            $accessToken = $this->regenerateGoogleAccessToken($userOAuthToken->token);
                //                            $userOAuthToken->token = $accessToken;
                //                            $userOAuthToken->save();
                //                        }
                //                        $this->deleteEvent($event, $user->email, $userOAuthToken->token);
                //                        // $this->deleteEvent($event, env("GOOGLE_CALENDAR_ID"), $userOAuthToken->token); // Use this for testing
                //                    }
                //                } //google calendar delete end
                if ($event->delete()) {
                    $result = array('status' => '1', 'msg' => 'Event deleted successfully');
                } else {
                    $result = array('status' => '0', 'msg' => 'Something went wrong!!');
                }
            } else {
                $result = array('status' => '0', 'msg' => 'Something went wrong!! Contact Support');
            }
            return response()->json($result);
        }
    }
    public function getEventAllLog(Request $request)
    { //this can be used for fetching events based on column name and value and type
        $request->validate([
            'column' => 'required', //ex. contact_id or lead_id or property_id
            'value' => 'required', //ex. 1
            'type' => 'nullable', //ex. name from events table
        ]);
        $type = EventType::where('name', $request->type)->first();
        //        if (is_null($type)) {
        //            return response()->json(['status' => '0', 'msg' => 'Event type not found']);
        //        }
        $log = Event::where($request->column, $request->value)
            ->when($request->type != null, function ($query) use ($type) {
                if ($type != null) {
                    return $query->where('event_type_id', $type->id);
                }
            })->with("attendees", "contact", "lead", "property", "agent", "type")->orderBy('id', 'desc')->limit(10)->get();
        if (count($log) > 0) {
            return response()->json(['status' => '1', 'msg' => 'Event log found', "log" => $log]);
        } else {
            return response()->json(['status' => '0', 'msg' => 'Event log not found']);
        }
    }
    public function getUserEvents(Request $request)
    { //this
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'nullable', //ex. name from events table
            'date_start' => 'nullable',
            'date_end' => 'nullable',
        ]);
        $type = EventType::where('name', $request->type)->first();
        if (is_null($type)) {
            return response()->json(['status' => '0', 'msg' => 'Event type not found']);
        }
        $log = Event::where('agent_id', $request->user_id)
            ->when($request->type != null, function ($query) use ($type) {
                if ($type != null) {
                    return $query->where('event_type_id', $type->id);
                }
            })
            ->when($request->date_start, function ($query) use ($request) {
                return $query->where('start_time', '>=', Carbon::parse($request->date_start)->startOfDay());
            })->when($request->date_end, function ($query) use ($request) {
                return $query->where('end_time', '<=', Carbon::parse($request->date_end)->endOfDay());
            })->where('event_type_id', $type->id)->with('type')->get();
        if (count($log) > 0) {
            return response()->json(['status' => '1', 'msg' => 'Event log found', "data" => $log]);
        } else {
            return response()->json(['status' => '0', 'msg' => 'Event log not found']);
        }
    }
}
