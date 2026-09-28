<?php

namespace App\Services\Dashboard;

use App\Models\Event;
use App\Models\EventType;
use App\Models\User;
use App\Models\UserLastActivity;
use App\Services\Dashboard\DashboardReportFilterService;
use Carbon\Carbon;
use DB;

class ActivityReportService
{

    protected $filters;


    public function __construct(DashboardReportFilterService $filters)
    {
        $this->filters = $filters;

    }


    public function summary($request, $params)
    {
        $last_activity = $this->getAgentLastActivity($params);
        $data = [
            'agents_event' => $this->getEventStatus($request,$params),
            'last_activity' => $last_activity,
        ];
        return $data;
    }

    public function getEventStatus($request, $params)
    {
        $notAdmin = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }
        $start_date = $params['start_date'];
        $end_date = $params['end_date'];

        $startDate = Carbon::parse($start_date)->format("Y-m-d");
        $endDate = Carbon::parse($end_date)->format("Y-m-d");
        $event_types = EventType::where('status', true)->pluck('name', 'id')->toArray();

        $eventTypeReport = Event::with('agent')

            ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->whereNotIn('agent_id', $this->filters->getExcludedUsers())
            ->when($params['filter_by_users'], function ($q) use ($params) {
                $q->whereIn("agent_id", $params['user_ids']);
            })
            ->whereHas('agent', function ($query) {
                $query->where('status', true);
            })->select(
                'agent_id',
                'event_type_id',
                DB::raw("COUNT(*) as event_count") // Count properties per publish status
            )
            ->groupBy('agent_id', 'event_type_id')
            ->with(['agent','agent.parent','agent.parent.group'])
            ->get();
        $reportData = [];
        $finalReport = []; // Initialize the final output array


        foreach ($eventTypeReport as $event) {
            $agentName = $event->agent->full_name ?? '-';
            $type = $event->event_type_id;
            $typeNames = $event->type->name ?? '-';
            $eventCount = $event->event_count;


            if (!isset($reportData[$agentName])) {
                $reportData[$agentName] = [];
            }

            // Add the count for this publish status
            $reportData[$agentName][$typeNames] = $eventCount;
            $reportData[$agentName]['image'] = $event->agent->thumb_url;
            $reportData[$agentName]['manager'] = $event?->agent?->parent?->name ?? '';
            $reportData[$agentName]['group'] = $event?->agent?->parent?->group?->name ?? '';
        }
        foreach ($reportData as $agentName => $statuses) {
            $agentStatuses = [];
            foreach ($statuses as $typeName => $count) {
                $agentStatuses[] = [
                    'name' => $typeName,
                    'count' => $count,
                ];
            }
            foreach ($event_types as $key => $value) {
                if (!isset($reportData[$agentName][$value])) {
                    $agentStatuses[] = [
                        'name' => $value,
                        'count' => 0,
                    ];
                }
            }
            $stats = collect($agentStatuses);
            //unset image, manager and group from the collection
            $stats->forget('image');
            $stats->forget('manager');
            $stats->forget('group');
            $stats = $stats->reject(function ($item) {
                return in_array($item['name'], ['image', 'manager', 'group']);
            });

            $finalReport[] = [
                'agent_name' => $agentName,
                'image' => $statuses['image'],
                'manager' => $statuses['manager'],
                'group' => $statuses['group'],
                'statuses' => $stats->values()->all(),
            ];
        }

        return $finalReport;

    }


    public function getAgentLastActivity($params)
    {
        $collection = collect();
        $exempted_users = [
            1403, 307, 4, 172, 1046, 1047, 1081, 1215, 1432, 1522

        ];
        $excludedUsers = $this->filters->getExcludedUsers();
        $excludedUsers = array_merge($exempted_users, $excludedUsers);
        $users = User::where('is_report_exempt', false)
            ->whereNotIn('id', $excludedUsers)
            ->when($params['filter_by_users'], function ($q) use ($params) {
                $q->whereIn("id", $params['user_ids']);
            })
            ->when(!$params['show_inactive_users'], function ($q) {
                $q->where('status', true);
            })
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })->select('id', 'name')->with(['parent','parent.group'])->get();

        foreach ($users as $user) {
            $last_act = UserLastActivity::where('user_id', $user->id)->first();
            $last_time = $last_act->last_active ?? null;
            if ($last_time != null) {
                $last_login = new \DateTime($last_time);
                $current_time = new \DateTime();
                $time_difference = $current_time->diff($last_login);

                $hours = $time_difference->h + ($time_difference->days * 24);
                $minutes = $time_difference->i;

                $dayHours = formatTimeReport($hours);
                $tempMsg = $dayHours . " " . $minutes."m ago";
                $collection->push([
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'image' => $user->thumb_url,
                    'group' => $user?->parent?->group->name ?? '',
                    'manager' =>$user?->parent?->name ?? '',
                    'hours' => $hours,
                    'message' => $tempMsg
                ]);
            }
        }

        $sorted = $collection->sortBy('hours');
        $sorted->values()->all();
        $collection = $sorted->reverse();
       //remove the key from the collection
        return array_values($collection->toArray());

    }
}
