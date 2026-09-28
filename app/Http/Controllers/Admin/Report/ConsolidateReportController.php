<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Models\ConsolidatedReport;
use App\Models\Group;
use App\Models\User;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ConsolidateReportController extends Controller
{

    public function index(): View
    {
        return view('admin.reports.consolidateReport.index');
    }

    public function runCommand(){
        \Artisan::call('ax:consolidated-report-calculate');
    }
      public function getTeamUser($parent_id): array
    {
        return User::whereHas("roles", fn($q) => $q->whereIn("name", ["Agent"]))
            ->where("parent_id", $parent_id)
            ->where('status', true)->pluck('id')->toArray();
    }
       public function getChildrenIds($notAdmin, $parent_id = null): array
    {
        if ($parent_id != null) {
            return getUserChildrenIds($parent_id);
        } else {
            if ($notAdmin) {
                return getUserChildrenIds(auth()->user()->id);
            } else {
                return User::whereHas("roles", fn($q) => $q->whereIn("name", ["Agent"]))
            ->where('status', true)->pluck('id')->toArray();
            }
        }
    }
     public function landing(Request $request)
    {
        $notAdmin        = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent", "Primary Agent"]);
        $date            = $request->date;
        $agent_id       = $request->agent_id ?? null;
        $filter_by_users = false;
        if (isset($request->group) && $request->group != 'secondary') {
            $group          = $request->group;
            $selected_group = Group::where('name', $request->group)->with('user')->first();

            if ($selected_group == null) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Group not found',
                ]);
            }
            $user_ids        = $agent_id ? [$agent_id] : $this->getTeamUser($selected_group->user->id);
            $group_id        = $selected_group->id;
            $filter_by_users = true;
        } else if (isset($request->group) && ($request->group === 'secondary' || $request->group == '')) {
            $group    = 'secondary';
            $group_id = null;
            $user_ids =  $agent_id ? [$agent_id] : $this->getChildrenIds($notAdmin);
        } else {
            $group    = 'secondary';
            $group_id = null;
            $user_ids = $agent_id ? [$agent_id] : $this->getChildrenIds($notAdmin);
        }
        $filter_by_users = (bool)$notAdmin;

        $params = [
            'tab'             => $request->tab ?? "overall",
            'notAdmin'        => $notAdmin,
            'date'            => $date,
            'filter_by_users' => $filter_by_users,
            'start_date'      => $request->start_date,
            'end_date'        => $request->end_date,
            'user_ids'        => $user_ids,
            'group'           => $group,
        ];


       $data = $this->generateReport($params);
        return response()->json([
            'status'  => 'success',
            'message' => 'Data retrieved successfully',
            'data'    => $data,
        ]);
    }


    public function generateReport(array $params): array
    {

      $users = User::whereIn('id', $params['user_ids'])->get()->toArray();
      $data = [];
      $start_date = $params['start_date'] ?? Carbon::now()->subMonths(6)->format('Y-m');
      $end_date = $params['end_date'] ?? Carbon::now()->format('Y-m');

      foreach ($users as $user) {

          $consolidatedReport = ConsolidatedReport::where('user_id', $user['id'])
              ->where('month', '>=', $start_date)
              ->where('month', '<=', $end_date)
              ->orderBy('month', 'desc')
              ->get(['month', 'target', 'achievement as achieved'])
              ->toArray();

          $data[] = [
            'agent' => [
                'id' => $user['id'],
                'name' => $user['name'],
                'image' => $user['image'],
                'position' => $user['position']
            ],
            'report' => $consolidatedReport ?? []
          ];

      }

      return $data;
    }

}
