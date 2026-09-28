<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CalendarReportController extends Controller
{
    public function index(Request $request)
    {
        $childrenIds = [];
        if ($request->has('agent') && $request->agent != null) {
            $agent = User::find($request->agent);
            $notAdmin = $agent->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);

            if ($notAdmin) {
                $childrenIds = getUserChildrenIdsOnly($request->agent);
                $childrenIds[] = $request->agent;
            }
            $data['user'] = $agent;
        } else {
            $notAdmin = false;
            if ($notAdmin) {
                $childrenIds = getUserChildrenIds(auth()->user()->id);
            }
            $data['user'] = auth()->user();
        }

        return view('admin.reports.calendar', $data);
    }

    public function dashboardDecider()
    {
        if (auth()->user()->hasRole('Admin')) {
            return $this->adminDashboard();
        } else if (auth()->user()->hasRole('Agent')) {
            return $this->agentDashboard();
        } else {
            return $this->agentDashboard();
        }
    }

    public function adminDashboard()
    {
        $data['events'] = $this->eventsOverview('');
        return view('admin.reports.calendar_overview', $data);
    }

    public function agentDashboard()
    {
        $data['events'] = $this->eventsOverview('');
        $data['eventsMonthly'] = $this->eventsMonthlyOverview('');
        return view('admin.reports.calendar_overview', $data);
    }

    public function calenderActivities(Request $request)
    {
        // dd($request->all());
        $data['events'] = $this->eventsOverview($request->agent_id);
        $data['eventsMonthly'] = $this->eventsMonthlyOverview($request->agent_id);
        $data['employees'] = $this->findAgentsWithEventsOutsideTimeframe('');
        // $data['eventsStatsAgentWise'] = $this->getEventsStatsAgentWise();
        return response()->json(['status' => '1', 'data' => $data]);

    }

    public function eventsOverview($agent_id = null)
    {
        if ($agent_id !== null) {
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
        $events = Event::get()->groupBy('event_type_id');
        $eventCounts = [];

        foreach ($events as $eventTypeId => $eventGroup) {
            $eventCounts[$eventTypeId] = $eventGroup->count();
        }

        foreach ($eventCounts as $key => $value) {
            $eventCounts[EventType::find($key)->name ?? 'Null'] = $value;
            unset($eventCounts[$key]);
        }
        return $eventCounts;
    }

    public function eventsMonthlyOverview($agent_id = null)
    {
        // Get the current date.
        $currentDate = Carbon::now();

        // Create an array to store the labels (months) for the last 12 months.
        $labels = [];

        // Create an array to store event data for each month.
        $eventData = [];

        for ($i = 0; $i < 12; $i++) {
            // Calculate the date for the current month.
            $date = $currentDate->subMonths(1);
            $monthYear = $date->format('Y-m'); // Format as "YYYY-MM"

            // Store the label (month) in the labels array.
            $labels[] = $monthYear;

            // Retrieve event data for the current month, including event type counts.
            $events = Event::select('event_types.name as event_type_name')
                ->join('event_types', 'events.event_type_id', '=', 'event_types.id')
                ->whereYear('events.created_at', $date->year)
                ->whereMonth('events.created_at', $date->month)
                ->get();

            // Initialize an array to store event type counts for the current month.
            $eventTypeCounts = [];

            // Count the occurrences of each event type for the current month.
            foreach ($events as $event) {
                $eventTypeName = $event->event_type_name;
                if (!isset($eventTypeCounts[$eventTypeName])) {
                    $eventTypeCounts[$eventTypeName] = 1;
                } else {
                    $eventTypeCounts[$eventTypeName]++;
                }
            }

            // Store the event type counts for the current month.
            $eventData[$monthYear] = $eventTypeCounts;
        }
        return [
            'labels' => $labels, // Labels for X-axis (months).
            'data' => $eventData, // Data for each month.
        ];
    }

    public function findAgentsWithEventsOutsideTimeframe()
    {
        $today = Carbon::today();
        $startTime = $today->copy()->addMinutes(30);
        $endTime = $today->copy()->addHour();
        $outOfficeAgents = User::where('status',1)->whereHas('roles', function ($query) {
            $query->where('name', 'Agent');
        })->whereHas('events', function ($query) use ($today, $startTime, $endTime) {
            $query->where(function ($q) use ($startTime, $endTime) {

                $q->where('end_time', '<', $startTime);

                $q->orWhere('start_time', '>', $endTime);
            })->whereDate('start_time', $today); // Only events for today.
        })->get();
        $outOfficeAgentsCount = User::where('status',1)->whereHas('roles', function ($query) {
            $query->where('name', 'Agent');
        })->whereHas('events', function ($query) use ($today, $startTime, $endTime) {
            $query->where(function ($q) use ($startTime, $endTime) {
                $q->where('end_time', '<', $startTime);
                $q->orWhere('start_time', '>', $endTime);
            })->whereDate('start_time', $today); // Only events for today.
        })->count();

        $inOfficeAgents = User::where('status',1)->whereHas('roles', function ($query) {
            $query->where('name', 'Agent');
        })->whereDoesntHave('events', function ($query) use ($today, $startTime, $endTime) {
            $query->where(function ($q) use ($startTime, $endTime) {
                $q->where('end_time', '<', $startTime);
                $q->orWhere('start_time', '>', $endTime);
            })->whereDate('start_time', $today); // Only events for today.
        })->get();

        $inOfficeAgentsCount = User::where('status',1)->whereHas('roles', function ($query) {
            $query->where('name', 'Agent');
        })->whereDoesntHave('events', function ($query) use ($today, $startTime, $endTime) {
            $query->where(function ($q) use ($startTime, $endTime) {
                $q->where('end_time', '<', $startTime);
                $q->orWhere('start_time', '>', $endTime);
            })->whereDate('start_time', $today); // Only events for today.
        })->count();

        return [
            'in_office_agents' => $inOfficeAgents,
            'in_office_agents_count' => $inOfficeAgentsCount,
            'out_office_agents' => $outOfficeAgents,
            'out_office_agents_count' => $outOfficeAgentsCount,
        ];
    }


    public function getEventsStatsAgentWise(){
        $data = [];
        $agents = User::where('status',1)->whereHas('roles', function ($query) {
            $query->where('name', 'Agent');
        })->get();
        foreach ($agents as $agent) {
            $data[$agent->name] = $this->eventsOverview($agent->id);
        }
        return $data;
    }

    function agentEventsReport(){
        $data = [];
        $agents = User::where('status',1)->whereHas('roles', function ($query) {
            $query->where('name', 'Agent');
        })->get();

        foreach ($agents as $agent) {
            $data[$agent->name] = $this->getUserEvents($agent->id);
        }
        return view('admin.reports.partials._agents_events_stats')
        ->with('data',$data);
    }


    public function getUserEvents($id){
        $eventCounts = [];

        // Get all event types
        $allEventTypes = EventType::pluck('id');

        // Get events for the user grouped by event type
        $events = Event::where('agent_id', $id)->get()->groupBy('event_type_id');

        // Initialize event counts for all event types to 0
        foreach ($allEventTypes as $eventTypeId) {
            $eventCounts[EventType::find($eventTypeId)->name ?? 'Null'] = 0;
        }

        // Handle the case where event_type_id is null
        if (isset($events[null])) {
            $eventCounts['Null'] = $events[null]->count();
        } else {
            $eventCounts['Null'] = 0;
        }

        // Update event counts based on the user's events
        foreach ($events as $eventTypeId => $eventGroup) {
            $eventCounts[EventType::find($eventTypeId)->name ?? 'Null'] = $eventGroup->count();
        }

        return $eventCounts;
    }




}
