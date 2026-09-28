<?php
namespace App\Http\Controllers\Admin\Pipeline;

use App\Http\Controllers\Controller;
use App\Models\Pipeline;
use App\Models\User;
use App\Models\Group;
use App\Services\Dashboard\DashboardReportFilterService;
use DB;
use Illuminate\Http\Request;

class PipelineDashboardController extends Controller
{
    protected $filters;

    public function __construct(DashboardReportFilterService $filters)
    {
        $this->filters = $filters;
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
                return [];
            }
        }
    }

    public function landing(Request $request)
    {
        $notAdmin        = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent", "Primary Agent"]);
        $date            = $request->date;
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
            $user_ids        = $this->getTeamUser($selected_group->user->id);
            $group_id        = $selected_group->id;
            $filter_by_users = true;
        } else if (isset($request->group) && ($request->group === 'secondary' || $request->group == '')) {
            $group    = 'secondary';
            $group_id = null;
            $user_ids = $this->getChildrenIds($notAdmin);
        } else {
            $group    = 'secondary';
            $group_id = null;
            $user_ids = $this->getChildrenIds($notAdmin);
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

        if ($request->tab == "overall") {
            $data = $this->overview($params);

        } else if ($request->tab == "user_based_data") {
            $data = $this->userStatusData($params);

        }else if ($request->tab == "team_based_data") {
            $data = $this->teamData($params);

        }else if ($request->tab == "extra_data") {
            $data = $this->extraData($params);

        } else {
            $data = [];
        }
        return response()->json([
            'status'  => 'success',
            'message' => 'Pipeline landing data retrieved successfully',
            'data'    => $data,
        ]);
    }

    public function overview($params)
    {

        $excludedUsers = $this->filters->getExcludedUsers();
        $childrenIds   = $params['user_ids'];
        $won_deal_value = Pipeline::where('status', 'Won')->when
        ($params['start_date'] && $params['end_date'], function ($q) use ($params) {
            $q->whereBetween('created_at', [$params['start_date'], $params['end_date']]);
        })->when($params['filter_by_users'], function ($q) use ($childrenIds, $excludedUsers) {
            $q->whereIn("user_id", $childrenIds)
                ->whereNotIn('user_id', $excludedUsers);
        })->sum('deal_value');
        $commission_value = Pipeline::when($params['start_date'] && $params['end_date'], function ($q) use ($params) {
            $q->whereBetween('created_at', [$params['start_date'], $params['end_date']]);
        })->when($params['filter_by_users'], function ($q) use ($childrenIds, $excludedUsers) {
            $q->whereIn("user_id", $childrenIds)
                ->whereNotIn('user_id', $excludedUsers);
        })->where('status','Won')->sum('commission');
        //calculate  % of commission from $won_deal_value and $commission_value
        $commission_percentage = $won_deal_value > 0 ? ($commission_value / $won_deal_value) * 100 : 0;
        $overview      = [
            'total_deal_value'  => Pipeline::when($params['start_date'] && $params['end_date'], function ($q) use ($params) {
                $q->whereBetween('created_at', [$params['start_date'], $params['end_date']]);
            })
                ->when($params['filter_by_users'], function ($q) use ($childrenIds, $excludedUsers) {
                    $q->whereIn("user_id", $childrenIds)
                        ->whereNotIn('user_id', $excludedUsers);
                })
                ->sum('deal_value'),
            'active_deal_value' => Pipeline::where('status', 'Active')->when($params['start_date'] && $params['end_date'], function ($q) use ($params) {
                $q->whereBetween('created_at', [$params['start_date'], $params['end_date']]);
            })->when($params['filter_by_users'], function ($q) use ($childrenIds, $excludedUsers) {
                $q->whereIn("user_id", $childrenIds)
                    ->whereNotIn('user_id', $excludedUsers);
            })->sum('deal_value'),
            'active_deal_commission' => Pipeline::where('status', 'Active')->when($params['start_date'] && $params['end_date'], function ($q) use ($params) {
                $q->whereBetween('created_at', [$params['start_date'], $params['end_date']]);
            })->when($params['filter_by_users'], function ($q) use ($childrenIds, $excludedUsers) {
                $q->whereIn("user_id", $childrenIds)
                    ->whereNotIn('user_id', $excludedUsers);
            })->sum('commission'),
            'won_deal_value'    => $won_deal_value,
            'won_deal_commission' => Pipeline::where('status', 'Won')->when($params['start_date'] && $params['end_date'], function ($q) use ($params) {
                $q->whereBetween('created_at', [$params['start_date'], $params['end_date']]);
            })->when($params['filter_by_users'], function ($q) use ($childrenIds, $excludedUsers) {
                $q->whereIn("user_id", $childrenIds)
                    ->whereNotIn('user_id', $excludedUsers);
            })->sum('commission'),

            'lost_deal_value'   => Pipeline::where('status', 'Lost')->when($params['start_date'] && $params['end_date'], function ($q) use ($params) {
                $q->whereBetween('created_at', [$params['start_date'], $params['end_date']]);
            })->when($params['filter_by_users'], function ($q) use ($childrenIds, $excludedUsers) {
                $q->whereIn("user_id", $childrenIds)
                    ->whereNotIn('user_id', $excludedUsers);
            })->sum('deal_value'),
            'lost_deal_commission' => Pipeline::where('status', 'Lost')->when($params['start_date'] && $params['end_date'], function ($q) use ($params) {
                $q->whereBetween('created_at', [$params['start_date'], $params['end_date']]);
            })->when($params['filter_by_users'], function ($q) use ($childrenIds, $excludedUsers) {
                $q->whereIn("user_id", $childrenIds)
                    ->whereNotIn('user_id', $excludedUsers);
            })->sum('commission'),
            'total'             => Pipeline::when($params['start_date'] && $params['end_date'], function ($q) use ($params) {
                $q->whereBetween('created_at', [$params['start_date'], $params['end_date']]);
            })->when($params['filter_by_users'], function ($q) use ($childrenIds, $excludedUsers) {
                $q->whereIn("user_id", $childrenIds)
                    ->whereNotIn('user_id', $excludedUsers);
            })->count(),
            'closing'           => Pipeline::when($params['start_date'] && $params['end_date'], function ($q) use ($params) {
                $q->whereBetween('closing_date', [$params['start_date'], $params['end_date']]);
            })->when($params['filter_by_users'], function ($q) use ($childrenIds, $excludedUsers) {
                $q->whereIn("user_id", $childrenIds)
                    ->whereNotIn('user_id', $excludedUsers);
            })->count(),
            'closed'  => Pipeline::whereIn('status', ['Won'])
                ->when($params['start_date'] && $params['end_date'], function ($q) use ($params) {
                $q->whereBetween('closing_date', [$params['start_date'], $params['end_date']]);
            })->when($params['filter_by_users'], function ($q) use ($childrenIds, $excludedUsers) {
                $q->whereIn("user_id", $childrenIds)
                    ->whereNotIn('user_id', $excludedUsers);
            })->count(),
            'active'   => Pipeline::where('status', 'Active')->when($params['start_date'] && $params['end_date'], function ($q) use ($params) {
                $q->whereBetween('created_at', [$params['start_date'], $params['end_date']]);
            })->when($params['filter_by_users'], function ($q) use ($childrenIds, $excludedUsers) {
                $q->whereIn("user_id", $childrenIds)
                    ->whereNotIn('user_id', $excludedUsers);
            })->count(),
            'won'  => Pipeline::where('status', 'Won')->when($params['start_date'] && $params['end_date'], function ($q) use ($params) {
                $q->whereBetween('created_at', [$params['start_date'], $params['end_date']]);
            })->when($params['filter_by_users'], function ($q) use ($childrenIds, $excludedUsers) {
                $q->whereIn("user_id", $childrenIds)
                    ->whereNotIn('user_id', $excludedUsers);
            })->count(),
            'lost'  => Pipeline::where('status', 'Lost')->when($params['start_date'] && $params['end_date'], function ($q) use ($params) {
                $q->whereBetween('created_at', [$params['start_date'], $params['end_date']]);
            })->when($params['filter_by_users'], function ($q) use ($childrenIds, $excludedUsers) {
                $q->whereIn("user_id", $childrenIds)
                    ->whereNotIn('user_id', $excludedUsers);
            })->count(),
            'commission_value' => $commission_value,
            'commission_percentage' => round($commission_percentage,2),
            'sale_pipelines' => Pipeline::where('status', 'Active')->when($params['start_date'] && $params['end_date'], function ($q) use ($params) {
                $q->whereBetween('created_at', [$params['start_date'], $params['end_date']]);
            })->when($params['filter_by_users'], function ($q) use ($childrenIds, $excludedUsers) {
                $q->whereIn("user_id", $childrenIds)
                    ->whereNotIn('user_id', $excludedUsers);
            })->where('deal_type', 'Sale')->count(),
        'rent_pipelines' => Pipeline::where('status', 'Active')->when($params['start_date'] && $params['end_date'], function ($q) use ($params) {
                $q->whereBetween('created_at', [$params['start_date'], $params['end_date']]);
            })->when($params['filter_by_users'], function ($q) use ($childrenIds, $excludedUsers) {
                $q->whereIn("user_id", $childrenIds)
                    ->whereNotIn('user_id', $excludedUsers);
            })->where('deal_type', 'Rent')->count(),
            'sale_deal_value' => Pipeline::where('status', 'Active')->when($params['start_date'] && $params['end_date'], function ($q) use ($params) {
                $q->whereBetween('created_at', [$params['start_date'], $params['end_date']]);
            })->when($params['filter_by_users'], function ($q) use ($childrenIds, $excludedUsers) {
                $q->whereIn("user_id", $childrenIds)
                    ->whereNotIn('user_id', $excludedUsers);
            })->where('deal_type', 'Sale')->sum('deal_value'),
            'rent_deal_value' => Pipeline::where('status', 'Active')->when($params['start_date'] && $params['end_date'], function ($q) use ($params) {
                $q->whereBetween('created_at', [$params['start_date'], $params['end_date']]);
            })->when($params['filter_by_users'], function ($q) use ($childrenIds, $excludedUsers) {
                $q->whereIn("user_id", $childrenIds)
                    ->whereNotIn('user_id', $excludedUsers);
            })->where('deal_type', 'Rent')->sum('deal_value'),

        ];

        return [
            'overview'             => $overview,
            'status_data'          => [
                ['name' => 'Active', 'value' => $overview['active']],
                ['name' => 'Won', 'value' => $overview['won']],
                ['name' => 'Lost', 'value' => $overview['lost']],
            ]
        ];

    }
    public function extraData($params)
    {

        return [
            'priority_data'        => $this->pipelineByPriority($params),
            'priority_status_data' => $this->pipelineByPriorityAndStatus($params),
            'sale_rent_commission' => $this->getSaleRentCommissionsCounts($params),

        ];

    }
    public function statusData($params)
    {
        $pipelines = Pipeline::when($params['start_date'] && $params['end_date'], function ($q) use ($params) {
            $q->whereBetween('created_at', [$params['start_date'], $params['end_date']]);
        })->get();

        $statusData = $pipelines->groupBy('status')->map(function ($group) {
            return [
                'name'  => $group->first()->status,
                'value' => $group->count(),
            ];
        })->values()->toArray();

        return [
            'status_data' => $statusData,
        ];
    }

    public function userStatusData($params)
    {
        $excludedUsers = $this->filters->getExcludedUsers();
        $childrenIds   = $params['user_ids'];
        $users = User::when($params['filter_by_users'], function ($q) use ($childrenIds, $excludedUsers) {
            $q->whereIn('id', $childrenIds)
                ->whereNotIn('id', $excludedUsers);
        })
            ->select('id', 'name')->get();
        return [
            'user_data_by_status' => $this->getPipelineStatusByUsers($params,$users),
            'user_deal_values' => $this->getUserDealValue($params,$users),
            'user_deal_counts' => $this->getUserDealCount($params,$users),
        ];
    }
    public function teamData($params)
    {
        return [
           // 'user_data_by_status' => $this->getPipelineStatusByUsers($params),
            'group_deal_values' => $this->getGroupDealValue($params),
        ];
    }

    public function pipelineByPriority($params)
    {
         $excludedUsers = $this->filters->getExcludedUsers();
        $childrenIds   = $params['user_ids'];


        return [
            ['name' => 'High', 'value' => Pipeline::where('priority', 'High')->when($params['start_date'] && $params['end_date'], function ($q) use ($params) {
                    $q->whereBetween('created_at', [$params['start_date'], $params['end_date']]);
                })->when($params['filter_by_users'], function ($q) use ($childrenIds, $excludedUsers) {
                    $q->whereIn("user_id", $childrenIds)
                        ->whereNotIn('user_id', $excludedUsers);
                })->count() ?? 0],
            ['name' => 'Medium', 'value' => Pipeline::where('priority', 'Medium')->when($params['start_date'] && $params['end_date'], function ($q) use ($params) {
                    $q->whereBetween('created_at', [$params['start_date'], $params['end_date']]);
                })->when($params['filter_by_users'], function ($q) use ($childrenIds, $excludedUsers) {
                    $q->whereIn("user_id", $childrenIds)
                        ->whereNotIn('user_id', $excludedUsers);
                })->count() ?? 0],
            ['name' => 'Low', 'value' => Pipeline::where('priority', 'Low')->when($params['start_date'] && $params['end_date'], function ($q) use ($params) {
                    $q->whereBetween('created_at', [$params['start_date'], $params['end_date']]);
                })->when($params['filter_by_users'], function ($q) use ($childrenIds, $excludedUsers) {
                    $q->whereIn("user_id", $childrenIds)
                        ->whereNotIn('user_id', $excludedUsers);
                })->count() ?? 0],
        ];


    }
    public function pipelineByPriorityAndStatus($params): array
    {
         $excludedUsers = $this->filters->getExcludedUsers();
        $childrenIds   = $params['user_ids'];
        $priorities = ['High', 'Medium', 'Low'];
        foreach ($priorities as $priority) {
                $result[$priority] = [
                    'Active' => Pipeline::where('priority', $priority)
                        ->where('status', 'Active')
                        ->when($params['start_date'] && $params['end_date'], function ($q) use ($params) {
                            $q->whereBetween('created_at', [$params['start_date'], $params['end_date']]);
                        })->when($params['filter_by_users'], function ($q) use ($childrenIds, $excludedUsers) {
                            $q->whereIn('user_id', $childrenIds)
                                ->whereNotIn('user_id', $excludedUsers);
                        })->count() ?? 0,
                    'Won'    => Pipeline::where('priority', $priority)
                        ->where('status', 'Won')
                        ->when($params['start_date'] && $params['end_date'], function ($q) use ($params) {
                            $q->whereBetween('created_at', [$params['start_date'], $params['end_date']]);
                        })->when($params['filter_by_users'], function ($q) use ($childrenIds, $excludedUsers) {
                            $q->whereIn('user_id', $childrenIds)
                                ->whereNotIn('user_id', $excludedUsers);
                        })->count() ?? 0,
                    'Lost'   => Pipeline::where('priority', $priority)
                        ->where('status', 'Lost')
                        ->when($params['start_date'] && $params['end_date'], function ($q) use ($params) {
                            $q->whereBetween('created_at', [$params['start_date'], $params['end_date']]);
                        })->when($params['filter_by_users'], function ($q) use ($childrenIds, $excludedUsers) {
                            $q->whereIn('user_id', $childrenIds)
                                ->whereNotIn('user_id', $excludedUsers);
                        })->count() ?? 0,
                ];

        }

        return $result;

    }

    public function getPipelineStatusByUsers($params,$users)
    {


        $statusData = [];

        foreach ($users as $user) {
            // Get status counts for each user
            $statusCounts = Pipeline::select('status', DB::raw('count(*) as count'))
                ->where('user_id', $user->id)
                ->when($params['start_date'] && $params['end_date'], function ($q) use ($params) {
                    $q->whereBetween('created_at', [$params['start_date'], $params['end_date']]);
                })
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            // Ensure all status types are present with default count of 0
            $allStatuses = ['Active', 'Won', 'Lost'];
            $statuses    = [];

            foreach ($allStatuses as $status) {
                $statuses[] = [
                    'name'  => $status,
                    'count' => $statusCounts[$status] ?? 0,
                ];
            }

            $statusData[] = [
                'agent_id'   => $user->id,
                'agent_name' => $user->name,
                'group'      => $user->group?->name ?? 'N/A',
                'image'      => $user->image_url,
                'manager'    => $user->parent?->name ?? 'N/A',
                'total'      => array_sum(array_column($statuses, 'count')),
                'statuses'   => $statuses,
            ];
        }

        return $statusData;
    }
    public function getSaleRentCommissionsCounts($params)
    {
        $excludedUsers = $this->filters->getExcludedUsers();
        $childrenIds   = $params['user_ids'];
        return [
            'sale' => Pipeline::where('deal_type', 'Sale')
                ->when($params['start_date'] && $params['end_date'], function ($q) use ($params) {
                    $q->whereBetween('created_at', [$params['start_date'], $params['end_date']]);
                })->when($params['filter_by_users'], function ($q) use ($childrenIds, $excludedUsers) {
                    $q->whereIn("user_id", $childrenIds)
                        ->whereNotIn('user_id', $excludedUsers);
                })->where('status','Active')->sum('commission'),
            'rent' => Pipeline::where('deal_type', 'Rent')
                ->when($params['start_date'] && $params['end_date'], function ($q) use ($params) {
                    $q->whereBetween('created_at', [$params['start_date'], $params['end_date']]);
                })->when($params['filter_by_users'], function ($q) use ($childrenIds, $excludedUsers) {
                    $q->whereIn("user_id", $childrenIds)
                        ->whereNotIn('user_id', $excludedUsers);
                })->where('status','Active')->sum('commission'),
        ];

    }
    public function getUserDealValue($params,$users)
    {


        $statusData = [];

        foreach ($users as $user) {
            // Get status counts for each user
            $deal_value = Pipeline::where('user_id', $user->id)
                ->when($params['start_date'] && $params['end_date'], function ($q) use ($params) {
                    $q->whereBetween('created_at', [$params['start_date'], $params['end_date']]);
                })->sum('deal_value');

            $statuses    = [];
                $statuses[] = [
                    'name'  => 'Deal Value',
                    'count' => $deal_value ?? 0,
                ];
            $statusData[] = [
                'agent_id'   => $user->id,
                'agent_name' => $user->name,
                'group'      => $user->group?->name ?? 'N/A',
                'image'      => $user->thumb_url,
                'manager'    => $user->parent?->name ?? 'N/A',
                'total'      => array_sum(array_column($statuses, 'count')),
                'statuses'   => $statuses,
            ];
        }

        return $statusData;
    }
    public function getUserDealCount($params,$users)
    {


        $statusData = [];

        foreach ($users as $user) {
            // Get status counts for each user
            $deal_count = Pipeline::where('user_id', $user->id)
                ->when($params['start_date'] && $params['end_date'], function ($q) use ($params) {
                    $q->whereBetween('created_at', [$params['start_date'], $params['end_date']]);
                })->count();

            $statuses    = [];
            $statuses[] = [
                'name'  => 'Deal Value',
                'count' => $deal_count ?? 0,
            ];
            $statusData[] = [
                'agent_id'   => $user->id,
                'agent_name' => $user->name,
                'group'      => $user->group?->name ?? 'N/A',
                'image'      => $user->thumb_url,
                'manager'    => $user->parent?->name ?? 'N/A',
                'total'      => array_sum(array_column($statuses, 'count')),
                'statuses'   => $statuses,
            ];
        }

        return $statusData;
    }

    public function getGroupDealValue($params)
    {
        $data = [];
        if($params['group'] == 'secondary') {
    $groups = Group::with('user')->get();
    foreach ($groups as $group) {
        $user_ids = $this->getTeamUser($group->user->id);
        $deal_value = Pipeline::whereIn('user_id', $user_ids)
            ->when($params['start_date'] && $params['end_date'], function ($q) use ($params) {
                $q->whereBetween('created_at', [$params['start_date'], $params['end_date']]);
            })->where('status','Active')->sum('deal_value');
        $group->deal_value = $deal_value;
        $commission_value = Pipeline::whereIn('user_id', $user_ids)
            ->when($params['start_date'] && $params['end_date'], function ($q) use ($params) {
                $q->whereBetween('created_at', [$params['start_date'], $params['end_date']]);
            })->where('status','Active')->sum('commission');
        $data[] = [
            'group_id' => $group->id,
            'group_name' => $group->name,
            'deal_value' => $deal_value,
            'user_id' => $group->user->id,
            'agent_name' => '('.$group->name.') '.$group->user->name,
            'image' => $group->user->thumb_url,
            'statuses'   => [[
                'name' => 'Deal Value',
                'count' => $deal_value
            ],
            [
                'name' => 'Commission',
                'count' => $commission_value
            ]
                ]
        ];
    }
    return $data;
        }else{

            $group = Group::where('name', $params['group'])->with('user')->first();

            if ($group) {
                $user_ids = $this->getTeamUser($group->user->id);
                $deal_value = Pipeline::whereIn('user_id', $user_ids)
                    ->when($params['start_date'] && $params['end_date'], function ($q) use ($params) {
                        $q->whereBetween('created_at', [$params['start_date'], $params['end_date']]);
                    })->sum('deal_value');
                 $data[] = [
                    'group_id' => $group->id,
                    'group_name' => $group->name,
                    'deal_value' => $deal_value,
                    'user_id' => $group->user->id,
                    'agent_name' => '('.$group->name.') '.$group->user->name,
                     'image' => $group->user->thumb_url,
                    'statuses'   => [[
                        'name' => 'Deal Value',
                        'count' => $deal_value
                    ]]
                ];
                return $data;
            }


        }
    }

}
