<?php

namespace App\Services\Dashboard;


use App\Models\CallLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Http;

class CallReportService
{

    protected $filters;

    public function __construct(DashboardReportFilterService $filters)
    {
        $this->filters = $filters;
    }

    public function summary($request, $params)
    {
        $data = [
//            'call_log_user_report' => $this->getLast7DaysUserReport($request, $params),
            'call_log_by_status' => $this->getStatusWiseCallCount($params),
            'ax_contacts_call_log_by_status' => $this->getAXContactsStatusWiseCallCount($params),
            'ax_contacts_messages_by_status' => $this->getAXContactsStatusWiseMessagesCount($params),
        ];
        return $data;
    }

    public function getLast7DaysUserReport(Request $request, $params)
    {

        $childrenIds = $params['user_ids'];


        $startDate = Carbon::now()->subDays(6)->startOfDay();
        $endDate = Carbon::now()->endOfDay();
        $startDate = "2024-11-01";
        $endDate = date('Y-m-d');

        // Retrieve logs for the last 7 days with user name, grouped by user name and call_type
        $last7DaysReport = CallLog::whereBetween('created_at', [$startDate, $endDate])
            ->with('user') // Eager load the user relationship
            ->when($params['filter_by_users'], function ($q) use ($childrenIds) {
                $q->whereIn("user_id", $childrenIds);
            })
            ->select('user_id', 'call_type', DB::raw('count(*) as total'))
            ->groupBy('user_id', 'call_type')
            ->orderBy('user_id')
            ->get();

        // Format the result as an associative array with user names as top-level keys
        $formattedReport = [];
        $callTypes = [];

        foreach ($last7DaysReport as $log) {
            $userName = $log->user->full_name ?? '-'; // Get the user's name from the relationship
            $type = $log->call_type;

            // Collect unique call types for chart labels
            if (!in_array($type, $callTypes)) {
                $callTypes[] = $type;
            }

            // Organize data by user name and call type
            $formattedReport[$userName][$type] = $log->total;
        }

        // Prepare the final report with missing call types initialized to zero
        $report = [];
        foreach ($formattedReport as $userName => $calls) {
            foreach ($callTypes as $type) {
                $report[$userName][$type] = $calls[$type] ?? 0;
            }
        }

        return [
            'report' => $report,
            'callTypes' => $callTypes,
        ];
    }

    public function getStatusWiseCallCount($params)
    {
        $userIds = User::whereHas("roles", function ($query) {
                $query->where("name", "Agent");
            })
            ->when($params['filter_by_users'], fn($query) => $query->whereIn("id", $params['user_ids']))
            ->when(!$params['show_inactive_users'], fn($q) => $q->where("status", true))
            ->when($params['group'] == 'secondary', function ($q) use ($params) {
                $q->where("region_id", 1);
            })
            ->whereNotIn('id', $this->filters->getExcludedUsers())
            ->pluck('id')->all();


        $response = Http::withHeaders([
            'token' => env('AX_TELELOGS_API_SECRET_KEY'),
        ])->get('https://telelog.axcrm.ae/backend/api/get-status-and-user-wise-call-count-by-date', [
            'users' => $userIds,
            'start_date' => $params['start_date'],
            'end_date' => $params['end_date'],
        ]);

        if ($response->status() == 200) {
            $res = $response->json()['data'] ?? [];
            foreach ($res as $key => $value) {
                $user = User::where('id',$value['secondary_user_id'])->with(['parent','parent.group'])->first();
                $data[$key]['agent_name'] = $value['agent_name'];
                $data[$key]['image'] = $user->thumb_url ?? '';
                $data[$key]['manager'] = $user->parent->name ?? '';
                $data[$key]['group'] = $user?->parent?->group?->name ?? '';
                $data[$key]['statuses'] = $value['statuses'];
            }
            return $data ?? [];
        } else {
            return [];
        }
    }

    public function getAXContactsStatusWiseCallCount($params)
    {
        $userIds = User::whereHas("roles", function ($query) {
                $query->where("name", "Agent");
            })
            ->when($params['filter_by_users'], fn($query) => $query->whereIn("id", $params['user_ids']))
            ->when(!$params['show_inactive_users'], fn($q) => $q->where("status", true))
            ->when($params['group'] == 'secondary', function ($q) use ($params) {
                $q->where("region_id", 1);
            })
            ->whereNotIn('id', $this->filters->getExcludedUsers())
            ->pluck('id')->all();


        $response = Http::get('https://contacts.axcrm.ae/api/calls-count-by-call-status-agent-date', [
            'users' => $userIds,
            'start_date' => $params['start_date'],
            'end_date' => $params['end_date'],
        ]);

        if ($response->status() == 200) {
            return $response->json()['data'] ?? [];
        } else {
            return [];
        }
    }

    public function getAXContactsStatusWiseMessagesCount($params)
    {
        $userIds = User::whereHas("roles", function ($query) {
                $query->where("name", "Agent");
            })
            ->when($params['filter_by_users'], fn($query) => $query->whereIn("id", $params['user_ids']))
            ->when(!$params['show_inactive_users'], fn($q) => $q->where("status", true))
            ->when($params['group'] == 'secondary', function ($q) use ($params) {
                $q->where("region_id", 1);
            })
            ->whereNotIn('id', $this->filters->getExcludedUsers())
            ->pluck('id')->all();


        $response = Http::get('https://contacts.axcrm.ae/api/messages-count-by-message-status-agent-date', [
            'users' => $userIds,
            'start_date' => $params['start_date'],
            'end_date' => $params['end_date'],
        ]);

        if ($response->status() == 200) {
            return $response->json()['data'] ?? [];
        } else {
            return [];
        }
    }

}
