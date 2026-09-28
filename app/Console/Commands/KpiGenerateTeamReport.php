<?php
namespace App\Console\Commands;

use App\Models\Group;
use App\Models\KpiTeam;
use App\Models\KpiTeamAgentReport;
use App\Models\KpiTeamData;
use App\Models\KpiTeamReport;
use App\Models\KpiTeamUserReport;
use App\Models\PfCreditHistory;
use App\Models\Pipeline;
use App\Models\User;
use App\Models\UserThirdPartyData;
use Carbon\Carbon;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class KpiGenerateTeamReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    //take parameter from command line for lets say 2025-01-01
    protected $signature = 'kpi:generate-team-report {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will generate team for all the zones and managers';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $date = $this->argument('date');
        if($date ==''){
            $date = Carbon::now()->format('Y-m-d');
        }

        $this->info("kpi:generate-team-report - Generating Team KPI Report for: " . $date);

        $start_date = Carbon::parse($date)->startOfMonth()->format('Y-m-d');
        $end_date = Carbon::parse($date)->endOfMonth()->format('Y-m-d');

        $kpis    = KpiTeam::whereDate('start_date', '>=', $start_date)->whereDate('end_date', '<=', $end_date)->get();

        foreach ($kpis as $kpi) {
            $manager  = User::find($kpi->manager_id);
            $agentIds = User::whereHas("roles", fn($q) => $q->whereIn("name", ["Agent"]))
                ->where("parent_id", $manager->id)->pluck('id')->toArray();

            //->where('status', true)

            // $childrenIds = renderActiveUserChildren($manager);
            //     $agentIds = User::where("status", 1)
            //         ->whereHas("roles", function ($q) {
            //             $q->whereIn("name", ["Agent"]);
            //         })
            //         ->whereIn("id", $childrenIds)
            //         ->where('status', 1)
            //         ->pluck("id");
            $deal_app_data = $this->getReportFromDealApp($kpi->group_id, $kpi->manager_id, $kpi->start_date, $kpi->end_date, $agentIds);
            switch ($kpi->kpi) {
                // case 'average_transaction_value':
                //     $this->avgTransactionValueProcess($kpi,$deal_app_data);
                //     break;
                case 'sales_value':
                    $this->avgTransactionValueProcess($kpi, $deal_app_data);
                    break;
                case 'pipelines':
                    $this->pipelinesProcess($kpi);
                    break;

            }
        }

        $pf_dates = getPfCreditDurationDates($date);
        $pf_credit_targets  = KpiTeam::where('kpi','pf_credit_limit')
            ->whereDate('start_date', '>=', $pf_dates['start_date'])
            ->whereDate('end_date', '<=', $pf_dates['end_date'])
            ->get();
        foreach ($pf_credit_targets as $kpi) {
            $this->calculatePFCreditLimit($kpi, $pf_dates['start_date'], $pf_dates['end_date']);
        }

        $bayutDubizzleDates = getBayutCreditDurationDates($date);
        $bayut_credit_targets  = KpiTeam::where('kpi','bayut_credit_limit')
            ->whereDate('start_date', '>=', $bayutDubizzleDates['start_date'])
            ->whereDate('end_date', '<=', $bayutDubizzleDates['end_date'])
            ->get();
        foreach ($bayut_credit_targets as $kpi) {
            $this->calculateBayutCreditLimit($kpi, $bayutDubizzleDates['start_date'], $bayutDubizzleDates['end_date'], 'bayut');
        }
        $dubizzle_credit_targets = KpiTeam::where('kpi','dubizzle_credit_limit')
            ->whereDate('start_date', '>=', $bayutDubizzleDates['start_date'])
            ->whereDate('end_date', '<=', $bayutDubizzleDates['end_date'])
            ->get();
        foreach ($dubizzle_credit_targets as $kpi) {
            $this->calculateBayutCreditLimit($kpi, $bayutDubizzleDates['start_date'], $bayutDubizzleDates['end_date'], 'dubizzle');
        }

        $this->info("kpi:generate-team-report - Generated Team KPI Report for: " . $date);
    }

    protected function getReportFromDealApp($teamId, $parentId, $startDate, $endDate, $agents)
    {
        $payload = [
            'team_id'    => $teamId,
            'parent_id'  => $parentId,
            'start_date' => $startDate,
            'end_date'   => $endDate,
            'agents'     => $agents,
        ];
        $response = Http::withHeaders([
            'token' => env('AXDEAL_SECRET_KEY'),
        ])->post('https://deals.axcrm.ae/backend/api/report/get-team-report', $payload);
//               $response = Http::withHeaders([
//                    'token' => env('AXDEAL_SECRET_KEY'),
//                    ])->post('http://127.0.0.1:8000/api/report/get-team-report', $payload);

        if ($response->status() == 200) {
            return $response->json();
            // $agent_deal_count = $json['agent_deal_count'];
        } else {
            return [];
        }
    }

    private function generateCommonKpiReport($kpi_temp, $date, $deal_app_data)
    {

        $start_date              = $kpi_temp->start_date;
        $end_date                = $kpi_temp->end_date;
        $total_deal_count        = $deal_app_data['team']['total_deal_count'] ?? 0;
        $avg_gross_commission    = $deal_app_data['team']['avg_commission_percentage'] ?? 0;
        $total_sales_value       = $deal_app_data['team']['sales_value'] ?? 0;
        $total_gross_revenue     = $deal_app_data['team']['total_gross_revenue'] ?? 0;
        $total_sales_revenue     = $deal_app_data['team']['total_sales_revenue'] ?? 0;
        $total_rental_revenue    = $deal_app_data['team']['total_rental_revenue'] ?? 0;
        $total_rental_commission = $deal_app_data['team']['total_rental_commission'] ?? 0;
        $total_sales_commission  = $deal_app_data['team']['total_sales_commission'] ?? 0;
        $company_revenue         = $deal_app_data['team']['company_revenue'] ?? 0;
        $avg_company_commission         = $deal_app_data['team']['avg_company_commission'] ?? 0;
        $avg_rental_commission         = $deal_app_data['team']['avg_rental_commission'] ?? 0;
        $avg_sales_commission         = $deal_app_data['team']['avg_sales_commission'] ?? 0;

        $total_gross_commission_amount = $deal_app_data['team']["total_commission_amount"] ?? 0;

            $avg_commission_percentage = $deal_app_data['team']["avg_commission_per"] ?? 0;

            $gross_commission_amount = $deal_app_data['team']["total_gross_comm"] ?? 0;

        $other_targets = [
            //  'total_deals',
            'gross_revenue',
            'sales_revenue',
            'rental_revenue',
            'company_revenue',
            'sales_commission',
            'rental_commission',
            'avg_gross_commission',
            'avg_company_commission',
            'gross_commission_amount',

            //'sales_deal_count',
            //'rental_deals_count',
//            'avg_rental_revenue',
//            'avg_sales_revenue',
//            'avg_rental_commission',
//            'avg_sales_commission',

        ];
        $data = [];

        foreach ($other_targets as $target) {
            foreach ($date as $value) {
                $data[$value] = 0;
            }
            $total = 0;
            if ($target == 'total_deals' || $target == 'sales_deal_count' || $target == 'rental_deals_count') {
                $total = 0;
            } else if ($target == 'avg_gross_commission') {
                $total = $avg_commission_percentage;
            } else if ($target == 'gross_revenue') {
                $total = $total_gross_revenue;
            } else if ($target == 'sales_revenue') {
                $total = $total_sales_revenue;
            } else if ($target == 'rental_revenue') {
                $total = $total_rental_revenue;
            } else if ($target == 'company_revenue') {
                $total = $company_revenue;
            } else if ($target == 'sales_commission') {
                $total = $total_sales_commission;
            } else if ($target == 'rental_commission') {
                $total = $total_rental_commission;
            }else if($target == 'avg_company_commission'){
                $total = $avg_company_commission;
            }else if($target == 'gross_commission_amount'){
                $total = $gross_commission_amount;
            }
            else {
                $total = 0;
            }

            $ktd             = KpiTeamData::where('group_id', $kpi_temp->group_id)->where('kpi', $target)->where('start_date', $start_date)->first() ?? new KpiTeamData();
            $ktd->group_id   = $kpi_temp->group_id;
            $ktd->start_date = $start_date;
            $ktd->end_date   = $end_date;
            $ktd->kpi        = $target;
            $ktd->total      = $total;
            $ktd->count      = $total;
            $ktd->data       = json_encode($data);
            $ktd->save();

        }
    }

    private function userReportGenerate($kpi, $date)
    {
        $group = Group::with(["user"])
            ->whereNull("parent_id")
            ->orderBy("order")
            ->where('id', $kpi->group_id)
            ->first();
        if (!$group) {

            return;
        }
        $manager = $group->user;

        $childrenIds = renderActiveUserChildren($manager);

        $agents = User::where("status", 1)
            ->whereHas("roles", function ($q) {
                $q->whereIn("name", ["Agent"]);
            })
            ->whereIn("id", $childrenIds)
            ->where('status', 1)
            ->get();
        $start_date = new DateTime($kpi->start_date);
        $end_date   = new DateTime($kpi->end_date);

        foreach ($agents as $agent) {
            $data = [];

            foreach ($date as $value) {
                $data[$value] = 0;
            }
            $user_report                      = KpiTeamUserReport::where('user_id', $agent->id)->where('kpi_id', $kpi->id)->first() ?? new KpiTeamUserReport();
            $user_report->kpi_id              = $kpi->id;
            $user_report->user_id             = $agent->id;
            $user_report->manager_id          = $kpi->manager_id ?? null;
            $user_report->group_id            = $kpi->group_id;
            $user_report->model               = $kpi->model;
            $user_report->target              = $kpi->target;
            $user_report->achieved            = 0;
            $user_report->achieved_percentage = 0;
            $user_report->average_per_day     = round($kpi->target / $kpi->duration);
            $user_report->start_date          = $kpi->start_date;
            $user_report->end_date            = $kpi->end_date;
            $user_report->data                = json_encode($data);
            $user_report->save();
        }

    }

    private function rentalDealsProcess($kpi)
    {

        $start_date = new DateTime($kpi->start_date);
        $end_date   = new DateTime($kpi->end_date);

        $date         = [];
        $current_date = clone $start_date;
        while ($current_date <= $end_date) {
            $date[] = $current_date->format('Y-m-d');

            $current_date->modify('+1 day');
            if ($current_date->format('Y-m-d') == date('Y-m-d')) {
                break;
            }
        }

        $data = [];
        foreach ($date as $value) {
            $data[$value] = 0;
        }
        $report                      = KpiTeamReport::where('kpi_id', $kpi->id)->first() ?? new KpiTeamReport();
        $report->kpi_id              = $kpi->id;
        $report->manager_id          = $kpi->manager_id ?? null;
        $report->group_id            = $kpi->group_id ?? null;
        $report->model               = $kpi->model;
        $report->target              = $kpi->target;
        $report->achieved            = array_sum($data);
        $report->average_per_day     = round($report->target / $kpi->duration);
        $report->achieved_percentage = round(($report->achieved / $report->target) * 100);
        $report->data                = json_encode($data);
        $report->start_date          = $kpi->start_date;
        $report->end_date            = $kpi->end_date;
        $report->save();

        $this->userReportGenerate($kpi, $date);
    }

    private function rentalGrossRevenueProcess($kpi)
    {
        $start_date = new DateTime($kpi->start_date);
        $end_date   = new DateTime($kpi->end_date);

        $date         = [];
        $current_date = clone $start_date;
        while ($current_date <= $end_date) {
            $date[] = $current_date->format('Y-m-d');

            $current_date->modify('+1 day');
            if ($current_date->format('Y-m-d') == date('Y-m-d')) {
                break;
            }
        }
        //  $this->generateCommonKpiReport($kpi, $date);
        $data = [];
        foreach ($date as $value) {
            $data[$value] = 0; //$kpi->target
        }
        $report                      = KpiTeamReport::where('kpi_id', $kpi->id)->first() ?? new KpiTeamReport();
        $report->kpi_id              = $kpi->id;
        $report->manager_id          = $kpi->manager_id ?? null;
        $report->group_id            = $kpi->group_id ?? null;
        $report->model               = $kpi->model;
        $report->target              = $kpi->target;
        $report->achieved            = 0; //array_sum($data);
        $report->average_per_day     = round($report->target / $kpi->duration);
        $report->achieved_percentage = round(($report->achieved / $report->target) * 100);
        $report->data                = json_encode($data);
        $report->start_date          = $kpi->start_date;
        $report->end_date            = $kpi->end_date;
        $report->save();

        $this->userReportGenerate($kpi, $date);
    }
    private function salesGrossRevenueProcess($kpi)
    {
        $start_date = new DateTime($kpi->start_date);
        $end_date   = new DateTime($kpi->end_date);

        $date         = [];
        $current_date = clone $start_date;
        while ($current_date <= $end_date) {
            $date[] = $current_date->format('Y-m-d');

            $current_date->modify('+1 day');
            if ($current_date->format('Y-m-d') == date('Y-m-d')) {
                break;
            }
        }
        // $this->generateCommonKpiReport($kpi, $date);
        $data = [];
        foreach ($date as $value) {
            $data[$value] = 0;
        }
        $report             = KpiTeamReport::where('kpi_id', $kpi->id)->first() ?? new KpiTeamReport();
        $report->kpi_id     = $kpi->id;
        $report->manager_id = $kpi->manager_id ?? null;
        $report->group_id   = $kpi->group_id ?? null;
        $report->model      = $kpi->model;
        $report->target     = $kpi->target;
        // $report->achieved = array_sum($data);
        $report->achieved = 0;
        //$report->average_per_day = round($report->target / $kpi->duration);
        $report->average_per_day = 0;
        // $report->achieved_percentage = round(($report->achieved / $report->target) * 100);
        $report->achieved_percentage = 0;
        $report->data                = json_encode($data);
        $report->start_date          = $kpi->start_date;
        $report->end_date            = $kpi->end_date;
        $report->save();

        $this->userReportGenerate($kpi, $date);
    }
    private function avgTransactionValueProcess($kpi, $deal_app_data)
    {
        $start_date        = new DateTime($kpi->start_date);
        $end_date          = new DateTime($kpi->end_date);
        $total_sales_value = $deal_app_data['team']['sales_value'] ?? 0;

        $date         = [];
        $current_date = clone $start_date;
        while ($current_date <= $end_date) {
            $date[] = $current_date->format('Y-m-d');

            $current_date->modify('+1 day');
            if ($current_date->format('Y-m-d') == date('Y-m-d')) {
                break;
            }
        }
        $this->generateCommonKpiReport($kpi, $date, $deal_app_data);

        $data = [];
        foreach ($date as $value) {
            $data[$value] = 0;
        }
        //keep achieved always less than target
        $achieved = $kpi->target - 0;
        if ($kpi->kpi == 'sales_value') {
            $achieved = $total_sales_value;
        }
        $report             = KpiTeamReport::where('kpi_id', $kpi->id)->first() ?? new KpiTeamReport();
        $report->kpi_id     = $kpi->id;
        $report->manager_id = $kpi->manager_id ?? null;
        $report->group_id   = $kpi->group_id ?? null;
        $report->model      = $kpi->model;
        $report->target     = $kpi->target;
        $report->achieved   = $achieved;
        //$report->average_per_day = round($report->target / $kpi->duration);
        $report->average_per_day = 0;
        // $report->achieved_percentage = round(($report->achieved / $report->target) * 100);
        $report->achieved_percentage = 0;
        $report->data                = json_encode($deal_app_data);
        $report->start_date          = $kpi->start_date;
        $report->end_date            = $kpi->end_date;
        $report->save();

        $this->userReportGenerate($kpi, $date);
        $this->processAgentReport($kpi->group_id, $kpi->manager_id);
    }
    private function getTeamUser($parent_id): array
    {
        return User::whereHas("roles", fn($q) => $q->whereIn("name", ["Agent"]))
            ->where("parent_id", $parent_id)
            ->where('status', true)->pluck('id')->toArray();
    }
    private function pipelinesProcess($kpi)
    {
        $start_date        = new DateTime($kpi->start_date);
        $end_date          = new DateTime($kpi->end_date);
        $manager_id = $kpi->manager_id;
        $children_ids = $this->getTeamUser($manager_id);
        $achieved = Pipeline::whereBetween('created_at', [$start_date, $end_date])
            ->whereIn('user_id', $children_ids)
            ->where('status','Active')
            ->count();
        info("Manager ID: $manager_id, Achieved Pipelines: $achieved");
        $report             = KpiTeamReport::where('kpi_id', $kpi->id)->first() ?? new KpiTeamReport();
        $report->kpi_id     = $kpi->id;
        $report->manager_id = $kpi->manager_id ?? null;
        $report->group_id   = $kpi->group_id ?? null;
        $report->model      = $kpi->model;
        $report->target     = $kpi->target;
        $report->achieved   = $achieved;
        //$report->average_per_day = round($report->target / $kpi->duration);
        $report->average_per_day = 0;
        // $report->achieved_percentage = round(($report->achieved / $report->target) * 100);
        $report->achieved_percentage = 0;
        $report->start_date          = $kpi->start_date;
        $report->end_date            = $kpi->end_date;
        $report->save();
    }

    private function processAgentReport($groupId, $managerId, $startDate = null, $endDate = null)
    {

        if (is_null($startDate)) {
            $startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        }
        if (is_null($endDate)) {
            $endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        }

        $manager = User::find($managerId);

        $childrenIds = renderActiveUserChildren($manager);

        $agentIds = User::where("status", 1)
            ->whereHas("roles", function ($q) {
                $q->whereIn("name", ["Agent"]);
            })
            ->whereIn("id", $childrenIds)
            ->where('status', 1)
            ->pluck("id");

        $start_date = new DateTime($startDate);
        $end_date   = new DateTime($endDate);

        $kpis = [
            'gross_revenue',
            'sales_revenue',
            'rental_revenue',
            'sales_commission',
            'rental_commission',
            'avg_gross_commission',
        ];

        foreach ($agentIds as $agentId) {
            foreach ($kpis as $kpi) {
                $dailyData    = [];
                $current_date = clone $start_date;
                while ($current_date <= $end_date) {
                    $dailyData[$current_date->format('Y-m-d')] = $kpi == "avg_gross_commission" ? 0 : 0;
                    $current_date->modify('+1 day');
                }

                $report = KpiTeamAgentReport::where("agent_id", $agentId)->where('kpi', $kpi)
                    ->where('start_date', '>=', $startDate)
                    ->where('end_date', '<=', $endDate)
                    ->first() ?? new KpiTeamAgentReport();

                $report->manager_id = $managerId;
                $report->group_id   = $groupId;
                $report->agent_id   = $agentId;

                $count = $kpi == "avg_gross_commission" ? 0 : 0;

                $kpiTitle = ucwords(str_replace('_', ' ', $kpi));

                $report->model     = getKpiModel($kpi);
                $report->kpi       = $kpi;
                $report->kpi_title = $kpiTitle;
                $report->total     = $count;
                $report->count     = $count;
                $report->data      = [
                    [
                        'title' => $kpiTitle,
                        'count' => $report->count,
                    ],
                ];
                $report->daily_data = $dailyData;

                $report->start_date = $startDate;
                $report->end_date   = $endDate;
                $report->duration   = Carbon::parse($startDate)->daysInMonth;
                $report->save();
            }
        }
    }

    private function calculateBayutCreditLimit($kpi, $startDate, $endDate, $portal = 'bayut')
    {
        $userIds = User::whereHas("roles", function ($q) {
            $q->whereIn("name", ["Agent"]);
        })
            ->where("parent_id", $kpi->manager_id)
            ->where('status', true)
            ->pluck('id');
        $consumed = abs(UserThirdPartyData::whereIn('user_id', $userIds)
            ->where('portal', $portal.'_credit')
            ->where('start_date', '>=', $startDate)
            ->where('end_date', '<=', $endDate)
            ->sum('useful_data->consumed'));

        $report                      = KpiTeamReport::where('kpi_id', $kpi->id)->first() ?? new KpiTeamReport();
        $report->kpi_id              = $kpi->id;
        $report->manager_id          = $kpi->manager_id ?? null;
        $report->group_id            = $kpi->group_id ?? null;
        $report->model               = $kpi->model;
        $report->target              = $kpi->target;
        $report->achieved            = $consumed;
        $report->average_per_day     = round($report->target / $kpi->duration);
        $report->achieved_percentage = round(($report->achieved / $report->target) * 100);
        $report->data                = json_encode([
            [
                'title' => ($portal == 'bayut' ? 'Bayut' : 'Dubizzle') . ' Credit Consumed',
                'count' => $consumed,
            ],
        ]);
        $report->start_date          = $kpi->start_date;
        $report->end_date            = $kpi->end_date;
        $report->save();
    }

    private function calculatePFCreditLimit($kpi, $startDate, $endDate)
    {
        $userIds = User::whereHas("roles", function ($q) {
                $q->whereIn("name", ["Agent"]);
            })
            ->where("parent_id", $kpi->manager_id)
            ->where('status', true)
            ->pluck('id');

        $pf_credit = PfCreditHistory::whereIn('user_id', $userIds)
            ->whereDate('transaction_time', '>=', $startDate)
            ->whereDate('transaction_time', '<=', $endDate)
            ->orderBy('transaction_time', 'desc')
            ->first();

        if ($pf_credit){
            $used_credit = $kpi->target - $pf_credit->balance;

            $report                      = KpiTeamReport::where('kpi_id', $kpi->id)->first() ?? new KpiTeamReport();
            $report->kpi_id              = $kpi->id;
            $report->manager_id          = $kpi->manager_id ?? null;
            $report->group_id            = $kpi->group_id ?? null;
            $report->model               = $kpi->model;
            $report->target              = $kpi->target;
            $report->achieved            = $used_credit;
            $report->average_per_day     = round($report->target / $kpi->duration);
            $report->achieved_percentage = round(($report->achieved / $report->target) * 100);
            $report->data                = json_encode([
                [
                    'title' => 'Property Finder Credit',
                    'count' => $pf_credit->balance,
                ],
            ]);
            $report->start_date          = $kpi->start_date;
            $report->end_date            = $kpi->end_date;
            $report->save();
        }

    }

}
