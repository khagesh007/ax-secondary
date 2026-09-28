<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\Group;
use App\Models\KpiOtherReport;
use App\Models\KpiReport;
use App\Models\KpiTarget;
use App\Models\Lead;
use App\Models\PfCreditHistory;
use App\Models\Pipeline;
use App\Models\Property;
use App\Models\PropertyCycle;
use App\Models\User;
use App\Models\UserThirdPartyData;
use Carbon\Carbon;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class KpiGenerateReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kpi:generate-report {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $axContactCalls = [];
    protected $axContactMessages = [];
    protected $axTelelogsCalls = [];

    private function getAndSetAxContactCalls($startDate, $endDate)
    {
        try {

            $data = Http::get("https://contacts.axcrm.ae/api/calls-count-by-agent", [
                "start_date" => $startDate,
                "end_date" => $endDate,
            ])->json();

            $this->axContactCalls = $data["data"]["data"] ?? [];

        } catch (\Throwable $throwable) {
            $this->axContactCalls = [];
        }
    }

    private function getAndSetAxContactMessages($startDate, $endDate)
    {
        try {

            $data = Http::get("https://contacts.axcrm.ae/api/messages-count-by-agent", [
                "start_date" => $startDate,
                "end_date" => $endDate,
            ])->json();

            $this->axContactMessages = $data["data"]["data"] ?? [];

        } catch (\Throwable $throwable) {
            $this->axContactMessages = [];
        }
    }

    private function getAndSetAxTelelogsCalls($startDate, $endDate)
    {
        try {

            $data = Http::get("https://telelog.axcrm.ae/backend/api/get-call-counts-by-date", [
                "start_date" => $startDate,
                "end_date" => $endDate,
            ])->json();

            $this->axTelelogsCalls = $data["data"] ?? [];

        } catch (\Throwable $throwable) {
            $this->axTelelogsCalls = [];
        }
    }

    private function getReportFromDealApp($agentId, $startDate, $endDate)
    {
        $agent = User::where('id', $agentId)->with(['parent'])->first();
        $payload = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'agent_id' => $agentId,
            'zone' => $agent?->parent?->group?->name ?? '',
        ];

        $response = Http::withHeaders([
            'token' => env('AXDEAL_SECRET_KEY'),
        ])->post('https://deals.axcrm.ae/backend/api/report/get-agent-report', $payload);
        if ($response->status() == 200) {
            return $response->json() ?? [];
        } else {
            return [];
        }
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $date = $this->argument('date');
        if ($date == '') {
            $date = Carbon::now()->format('Y-m-d');
        }

        $this->info("kpi:generate-report - Generating KPI Report for: " . $date);

        $startDate = Carbon::parse($date)->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::parse($date)->endOfMonth()->format('Y-m-d');


        $kpis = KpiTarget::whereDate('start_date', '>=', $startDate)->whereDate('end_date', '<=', $endDate)->get();

        foreach ($kpis as $kpi) {
            switch ($kpi->kpi) {
                case 'live_listings':
                    $this->processLiveListingKpi($kpi);
                    break;
                case 'new_listings':
                    $this->processNewListingKpi($kpi);
                    break;
                case 'sales_target':
                    $this->processSalesTargetKpi($kpi);
                    break;
                case 'pipelines':
                    $this->processPipelinesKpi($kpi);
                    break;

            }
        }

        $this->getAndSetAxContactCalls($startDate, $endDate);
        $this->getAndSetAxContactMessages($startDate, $endDate);
        $this->getAndSetAxTelelogsCalls($startDate, $endDate);

        $groups = Group::with(['user'])->whereNull('parent_id')->get();
        foreach ($groups as $group) {
            "Processing Group: " . $group->id . "\n";
            if (!is_null($group->user)) {
                $agentIds = User::whereHas("roles", fn($q) => $q->whereIn("name", ["Agent"]))
                    ->where("parent_id", $group?->user?->id)->where('status', true)->pluck('id')->toArray();

                foreach ($agentIds as $agentId) {
                    $this->processCallLogKpiReport($group->user->id, $agentId, $startDate, $endDate);
                    $this->processLeadKpiReport($group->user->id, $agentId, $startDate, $endDate);
                    $this->processViewingsKpi($group->user->id, $agentId, $startDate, $endDate);
                    $this->processMeetingsKpi($group->user->id, $agentId, $startDate, $endDate);
                    $this->processFollowUpsKpi($group->user->id, $agentId, $startDate, $endDate);
                }
            }
        }
    }

    private function calculatePfCredit($kpi)
    {


        $start_date = Carbon::parse($kpi->start_date)->startOfDay();
        $end_date = Carbon::parse($kpi->end_date)->endOfDay();

        $pf_credit = PfCreditHistory::where('transaction_time', '>=', $start_date)
            ->where('transaction_time', '<=', $end_date)
            ->where('user_id', $kpi->agent_id)
            ->orderBy('transaction_time', 'desc')
            ->first();
        if ($pf_credit) {

            $already = KpiReport::where('kpi_id', $kpi->id)->first();
            if ($already) {
                $stats = $already;
            } else {
                $stats = new KpiReport();
            }
            $used_credit = $kpi->target - $pf_credit->balance;
            if ($stats->target > 0) {
                $achieved_percentage = round(($used_credit / $kpi->target) * 100);
            } else {
                $achieved_percentage = 0;
            }

            $stats->kpi_id = $kpi->id;
            $stats->average_per_day = 0;
            $stats->user_id = $kpi->agent_id;
            $stats->target = $kpi->target;
            $stats->achieved = $used_credit;
            $stats->achieved_percentage = $achieved_percentage;
            $stats->data = [
                [
                    'title' => 'PF Credit Limit',
                    'count' => $pf_credit->balance,
                ],
            ];
            $stats->daily_data = [];
            $stats->start_date = $kpi->start_date;
            $stats->end_date = $kpi->end_date;
            $stats->save();
        }
    }
    private function processPipelinesKpi($kpi)
    {
        $pipeline_count = Pipeline::where('user_id', $kpi->agent_id)
            ->where('status', '=', 'ACTIVE')
            ->whereBetween('created_at', [$kpi->start_date, $kpi->end_date])
            ->count();

        $target = preg_replace('/[^0-9]/', '', $kpi->target);
        $already = KpiReport::where('kpi_id', $kpi->id)->first();
        if ($already) {
            $stats = $already;
        } else {
            $stats = new KpiReport();
        }
        if ($stats->target > 0) {
            $achieved_percentage = round((count($pipeline_count) / $stats->target) * 100);
        } else {
            $achieved_percentage = 0;
        }
        $stats->kpi_id = $kpi->id;
        $stats->average_per_day = 0;
        $stats->user_id = $kpi->agent_id;
        $stats->target = $target;
        $stats->achieved = $pipeline_count;
        $stats->achieved_percentage = $achieved_percentage;
        $stats->data = null;
        $stats->daily_data = null;
        $stats->start_date = $kpi->start_date;
        $stats->end_date = $kpi->end_date;
        $stats->save();
    }
    private function processLiveListingKpi($kpi)
    {
        $dailyData = [];

        $listings = Property::where('agent_id', $kpi->agent_id)
            ->where('is_live', true)
            ->get();

        foreach ($listings as $listing) {
            $date = (new DateTime($listing->published_date))->format('Y-m-d'); // Convert to DateTime object
            if (!isset($dailyData[$date])) {
                $dailyData[$date] = 0;
            } else {
                $dailyData[$date]++;
            }
        }

        $target = preg_replace('/[^0-9]/', '', $kpi->target);
        $average_per_day = round($target / $kpi->duration);

        $already = KpiReport::where('kpi_id', $kpi->id)->first();
        if ($already) {
            $stats = $already;
        } else {
            $stats = new KpiReport();
        }
        if ($stats->target > 0) {
            $achieved_percentage = round((count($listings) / $stats->target) * 100);
        } else {
            $achieved_percentage = 0;
        }

        $stats->kpi_id = $kpi->id;
        $stats->average_per_day = $average_per_day;
        $stats->user_id = $kpi->agent_id;
        $stats->target = $target;
        $stats->achieved = count($listings);
        $stats->achieved_percentage = $achieved_percentage;
        $stats->data = [
            [
                'title' => 'Live Listings',
                'count' => $stats->achieved,
            ],
        ];
        $stats->daily_data = $dailyData;
        $stats->start_date = $kpi->start_date;
        $stats->end_date = $kpi->end_date;
        $stats->save();
    }

    private function processNewListingKpi($kpi)
    {
        $start_date = new DateTime($kpi->start_date);
        $end_date = new DateTime($kpi->end_date);

        $dailyData = [];
        $current_date = clone $start_date;
        while ($current_date <= $end_date) {
            $dailyData[$current_date->format('Y-m-d')] = 0;
            $current_date->modify('+1 day');
        }

        $latestCycles = PropertyCycle::select('property_id', DB::raw('MAX(created_at) as max_created_at'))
            ->groupBy('property_id');

        $listings = Property::select([
            'properties.id',
            'properties.ref_no',
            'properties.published_date',
            'properties.publish_status',
            'properties.created_at',
            'properties.created_by_id',
            'pc.status',
            'pc.created_at as cycle_created_at',
        ])
            ->join('property_cycles as pc', 'properties.id', '=', 'pc.property_id')
            ->joinSub($latestCycles, 'latest', function ($join) {
                $join->on('pc.property_id', '=', 'latest.property_id')
                    ->on('pc.created_at', '=', 'latest.max_created_at');
            })
            ->where('properties.agent_id', $kpi->agent_id)
            ->where('pc.status', 'PUBLISHED')
            ->whereDate('pc.created_at', '>=', $start_date->format('Y-m-d'))
            ->whereDate('pc.created_at', '<=', $end_date->format('Y-m-d'))
            ->where('properties.ref_no', 'NOT LIKE', 'axc-%-r%')
            ->where('properties.is_live', true)
            ->whereIn('properties.publish_status', ['PUBLISHED', 'PENDING'])
            ->orderByDesc('pc.created_at')
            ->distinct()
            ->get();

        foreach ($listings as $listing) {
            $date = (new DateTime($listing->cycle_created_at))->format('Y-m-d'); // Convert to DateTime object
            $dailyData[$date]++;
        }

        $target = preg_replace('/[^0-9]/', '', $kpi->target);

        $average_per_day = round($target / $kpi->duration);

        $already = KpiReport::where('kpi_id', $kpi->id)->first();
        if ($already) {
            $stats = $already;
        } else {
            $stats = new KpiReport();
        }
        if ($stats->target > 0) {
            $achieved_percentage = round((count($listings) / $stats->target) * 100);
        } else {
            $achieved_percentage = 0;
        }
        $stats->kpi_id = $kpi->id;
        $stats->average_per_day = $average_per_day;
        $stats->user_id = $kpi->agent_id;
        $stats->target = $target;
        $stats->achieved = count($listings);
        $stats->achieved_percentage = $achieved_percentage;
        $stats->data = [
            [
                'title' => 'New Listings',
                'count' => $stats->achieved,
            ],
        ];
        $stats->daily_data = $dailyData;
        $stats->start_date = $kpi->start_date;
        $stats->end_date = $kpi->end_date;
        $stats->save();
    }

    private function processSalesTargetKpi($kpi)
    {

        $salesTarget = $this->getReportFromDealApp($kpi->agent_id, $kpi->start_date, $kpi->end_date);

        $start_date = new DateTime($kpi->start_date);
        $end_date = new DateTime($kpi->end_date);

        $dailyData = [];
        $current_date = clone $start_date;
        while ($current_date <= $end_date) {
            $dt = $current_date->format('Y-m-d');
            $dailyData[$dt] = [
                [
                    'title' => 'Average Gross revenue',
                    'count' => $salesTarget["day_wise_data"][$dt]["gross_revenue"] ?? 0,
                ],
                [
                    'title' => 'Average Gross Revenue Percentage',
                    'count' => 0,
                ],
                [
                    'title' => 'Average Rental Revenue',
                    'count' => $salesTarget["day_wise_data"][$dt]["rental_revenue"] ?? 0,
                ],
                [
                    'title' => 'Average Sales Revenue',
                    'count' => $salesTarget["day_wise_data"][$dt]["sales_revenue"] ?? 0,
                ],
                [
                    'title' => 'Total Number of Deals',
                    'count' => $salesTarget["day_wise_data"][$dt]["deal_count"] ?? 0,
                ],
            ];
            $current_date->modify('+1 day');
        }
        //$gross_commission_amount = $salesTarget["total_commission_amount"];


        $already = KpiReport::where('kpi_id', $kpi->id)->first();
        if ($already) {
            $stats = $already;
        } else {
            $stats = new KpiReport();
        }
        if ($stats->target > 0) {
            $achieved_percentage = round((($salesTarget["total_sales_value"] ?? 0) / $stats->target) * 100);
        } else {
            $achieved_percentage = 0;
        }
        $target = preg_replace('/[^0-9]/', '', $kpi->target);
        $stats->kpi_id = $kpi->id;
        $stats->average_per_day = round($target / $kpi->duration);
        $stats->user_id = $kpi->agent_id;
        $stats->target = $target;
        $stats->achieved = $salesTarget["total_sales_value"] ?? 0;
        $stats->achieved_percentage = $achieved_percentage ?? 0;
        $stats->data = [
            [
                'title' => 'Average Gross Revenue',
                'count' => sprintf("%.2f", floor(($salesTarget["avg_gross_commission"] ?? 0) * 100) / 100) ?? 0,
            ],
            [
                'title' => 'Average Gross Revenue Percentage',
                'count' => $salesTarget['avg_commission_per'] ?? 0,
            ],
            [
                'title' => 'Average Gross Commission Percentage',
                'count' => $salesTarget['avg_commission_per'] ?? 0,
            ],
            [
                'title' => 'Total Gross Revenue',
                'count' => $salesTarget["total_gross_revenue"] ?? 0,
            ],
            [
                'title' => 'Average Rental Revenue',
                'count' => $salesTarget["avg_rental_revenue"] ?? 0,
            ],
            [
                'title' => 'Average Sales Revenue',
                'count' => $salesTarget["avg_sales_revenue"] ?? 0,
            ],
            [
                'title' => 'Total Gross Commission',
                'count' => $salesTarget["total_gross_comm"] ?? 0,
            ],
            [
                'title' => 'Total Number of Deals',
                'count' => $salesTarget["deal_count"] ?? 0,
            ],
        ];

        $stats->daily_data = $dailyData;
        $stats->start_date = $kpi->start_date;
        $stats->end_date = $kpi->end_date;
        $stats->save();

        $this->processDealKpiReport($kpi->manager_id, $kpi->agent_id, $kpi->start_date, $kpi->end_date, $salesTarget);
    }

    private function processCallLogKpiReport($managerId, $agentId, $startDate = null, $endDate = null)
    {
        if (is_null($startDate)) {
            $startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        }
        if (is_null($endDate)) {
            $endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        }

        $start_date = new DateTime($startDate);
        $end_date = new DateTime($endDate);

        $axContactCallIndex = array_search($agentId, array_column($this->axContactCalls, "secondary_user_id"));
        $axContactCall = null;
        if ($axContactCallIndex !== false) {
            $axContactCall = $this->axContactCalls[$axContactCallIndex];
        }

        $axContactMessageIndex = array_search($agentId, array_column($this->axContactMessages, "secondary_user_id"));
        $axContactMessage = null;
        if ($axContactMessageIndex !== false) {
            $axContactMessage = $this->axContactMessages[$axContactMessageIndex];
        }

        $axTelelogsCallIndex = array_search($agentId, array_column($this->axTelelogsCalls, "secondary_user_id"));
        $axTelelogsCall = null;
        if ($axTelelogsCallIndex !== false) {
            $axTelelogsCall = $this->axTelelogsCalls[$axTelelogsCallIndex];
        }

        $dailyData = [];
        $current_date = clone $start_date;
        while ($current_date <= $end_date) {
            $dt = $current_date->format('Y-m-d');
            $dailyData[$dt] = [
                [
                    'title' => 'AX Contacts Calls',
                    'count' => $axContactCall["count_by_date"][$dt] ?? 0,
                ],
                [
                    'title' => 'AX Contacts WhatsApp Messages',
                    'count' => $axContactMessage["count_by_date"][$dt] ?? 0,
                ],
                [
                    'title' => 'Dialed Calls',
                    'count' => $axTelelogsCall["count_by_date"][$dt]["dialed"] ?? 0,
                ],
                [
                    'title' => 'Received Calls',
                    'count' => $axTelelogsCall["count_by_date"][$dt]["received"] ?? 0,
                ],
                [
                    'title' => 'Missed Calls',
                    'count' => $axTelelogsCall["count_by_date"][$dt]["missed"] ?? 0,
                ],
            ];
            $current_date->modify('+1 day');
        }

        $already = KpiOtherReport::where('agent_id', $agentId)
            ->where('kpi', 'calls')
            ->where('start_date', '>=', $startDate)
            ->where('end_date', '<=', $endDate)
            ->first();
        if ($already) {
            $stats = $already;
        } else {
            $stats = new KpiOtherReport();
        }

        $stats->manager_id = $managerId;
        $stats->agent_id = $agentId;
        $stats->model = getKpiModel("Call Logs");
        $stats->kpi = "calls";
        $stats->kpi_title = "Calls";
        $stats->total = ($axContactCall["count"] ?? 0) + ($axContactMessage["count"] ?? 0) + ($axTelelogsCall["total"] ?? 0);
        $stats->count = ($axContactCall["count"] ?? 0) + ($axContactMessage["count"] ?? 0) + ($axTelelogsCall["total"] ?? 0);
        $stats->data = [
            [
                'title' => 'AX Contacts Calls',
                'count' => $axContactCall["count"] ?? 0,
            ],
            [
                'title' => 'AX Contacts WhatsApp Messages',
                'count' => $axContactMessage["count"] ?? 0,
            ],
            [
                'title' => 'Dialed Calls',
                'count' => $axTelelogsCall["dialed"] ?? 0,
            ],
            [
                'title' => 'Received Calls',
                'count' => $axTelelogsCall["received"] ?? 0,
            ],
            [
                'title' => 'Missed Calls',
                'count' => $axTelelogsCall["missed"] ?? 0,
            ],
        ];
        $stats->daily_data = $dailyData;
        $stats->start_date = $startDate;
        $stats->end_date = $endDate;
        $stats->duration = Carbon::parse($startDate)->daysInMonth;
        $stats->save();
    }

    private function processLeadKpiReport($managerId, $agentId, $startDate = null, $endDate = null)
    {
        if (is_null($startDate)) {
            $startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        }
        if (is_null($endDate)) {
            $endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        }

        $start_date = new DateTime($startDate);
        $end_date = new DateTime($endDate);

        $dailyData = [];
        $current_date = clone $start_date;
        while ($current_date <= $end_date) {
            $dailyData[$current_date->format('Y-m-d')] = 0;
            $current_date->modify('+1 day');
        }

        $leads = Lead::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->where('agent_id', $agentId)
            ->whereNull('converted_to_listing_lead')
            ->where('is_archive', false)
            ->where('portal_lead', false)
            ->get();

        foreach ($leads as $lead) {
            $date = (new DateTime($lead->created_at))->format('Y-m-d'); // Convert to DateTime object
            $dailyData[$date]++;
        }

        $already = KpiOtherReport::where('agent_id', $agentId)
            ->where('kpi', 'leads')
            ->where('start_date', '>=', $startDate)
            ->where('end_date', '<=', $endDate)
            ->first();
        if ($already) {
            $stats = $already;
        } else {
            $stats = new KpiOtherReport();
        }

        $count = count($leads);

        $stats->manager_id = $managerId;
        $stats->agent_id = $agentId;
        $stats->model = getKpiModel("leads");
        $stats->kpi = "leads";
        $stats->kpi_title = "Leads";
        $stats->total = $count;
        $stats->count = $count;
        $stats->data = [
            [
                'title' => 'Leads',
                'count' => $stats->count,
            ],
        ];
        $stats->daily_data = $dailyData;

        $stats->start_date = $startDate;
        $stats->end_date = $endDate;
        $stats->duration = Carbon::parse($startDate)->daysInMonth;
        $stats->save();
    }

    private function processDealKpiReport($managerId, $agentId, $startDate, $endDate, $salesTarget)
    {
        $start_date = new DateTime($startDate);
        $end_date = new DateTime($endDate);

        $dailyData = [];
        $current_date = clone $start_date;
        while ($current_date <= $end_date) {
            $dt = $current_date->format('Y-m-d');
            $dailyData[$dt] = $salesTarget["day_wise_data"][$dt]["deal_count"] ?? 0;
            $current_date->modify('+1 day');
        }

        $already = KpiOtherReport::where('agent_id', $agentId)
            ->where('kpi', 'deals')
            ->where('start_date', '>=', $startDate)
            ->where('end_date', '<=', $endDate)
            ->first();
        if ($already) {
            $stats = $already;
        } else {
            $stats = new KpiOtherReport();
        }

        $count = $salesTarget["total_deal_count"] ?? 0;

        $stats->manager_id = $managerId;
        $stats->agent_id = $agentId;
        $stats->model = getKpiModel("deals");
        $stats->kpi = "deals";
        $stats->kpi_title = "Deals";
        $stats->total = $count;
        $stats->count = $count;
        $stats->data = [
            [
                'title' => 'Deals',
                'count' => $stats->count,
            ],
        ];
        $stats->daily_data = $dailyData;
        $stats->start_date = $startDate;
        $stats->end_date = $endDate;
        $stats->duration = Carbon::parse($startDate)->daysInMonth;
        $stats->save();
    }

    private function processViewingsKpi($managerId, $agentId, $startDate = null, $endDate = null)
    {
        if (is_null($startDate)) {
            $startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        }
        if (is_null($endDate)) {
            $endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        }

        $start_date = new DateTime($startDate);
        $end_date = new DateTime($endDate);

        $dailyData = [];
        $current_date = clone $start_date;
        while ($current_date <= $end_date) {
            $dailyData[$current_date->format('Y-m-d')] = 0;
            $current_date->modify('+1 day');
        }

        $events = Event::whereDate('start_time', '>=', $startDate)
            ->whereDate('start_time', '<=', $endDate)
            ->where(function ($q) use ($agentId) {
                $q->where('agent_id', $agentId)
                    ->orWhereHas("attendees", fn($q2) => $q2->whereIn("user_id", [$agentId]));
            })
            ->whereHas('type', function ($query) {
                $query->where('name', 'Viewing');
            })
            ->get();

        foreach ($events as $event) {
            $date = (new DateTime($event->start_time))->format('Y-m-d'); // Convert to DateTime object
            $dailyData[$date]++;
        }

        $already = KpiOtherReport::where('agent_id', $agentId)
            ->where('kpi', 'viewings')
            ->where('start_date', '>=', $startDate)
            ->where('end_date', '<=', $endDate)
            ->first();
        if ($already) {
            $stats = $already;
        } else {
            $stats = new KpiOtherReport();
        }

        $count = count($events);

        $stats->manager_id = $managerId;
        $stats->agent_id = $agentId;
        $stats->model = getKpiModel("viewings");
        $stats->kpi = "viewings";
        $stats->kpi_title = "Viewings";
        $stats->total = $count;
        $stats->count = $count;
        $stats->data = [
            [
                'title' => 'Viewings',
                'count' => $stats->count,
            ],
        ];
        $stats->daily_data = $dailyData;
        $stats->start_date = $startDate;
        $stats->end_date = $endDate;
        $stats->duration = Carbon::parse($startDate)->daysInMonth;
        $stats->save();
    }

    private function processMeetingsKpi($managerId, $agentId, $startDate = null, $endDate = null)
    {
        if (is_null($startDate)) {
            $startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        }
        if (is_null($endDate)) {
            $endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        }

        $start_date = new DateTime($startDate);
        $end_date = new DateTime($endDate);

        $dailyData = [];
        $current_date = clone $start_date;
        while ($current_date <= $end_date) {
            $dailyData[$current_date->format('Y-m-d')] = 0;
            $current_date->modify('+1 day');
        }

        $events = Event::whereDate('start_time', '>=', $startDate)
            ->whereDate('start_time', '<=', $endDate)
            ->where(function ($q) use ($agentId) {
                $q->where('agent_id', $agentId)
                    ->orWhereHas("attendees", fn($q2) => $q2->whereIn("user_id", [$agentId]));
            })
            ->whereHas('type', function ($query) {
                $query->where('name', 'Meeting');
            })
            ->get();

        foreach ($events as $event) {
            $date = (new DateTime($event->start_time))->format('Y-m-d'); // Convert to DateTime object
            $dailyData[$date]++;
        }

        $already = KpiOtherReport::where('agent_id', $agentId)
            ->where('kpi', 'meetings')
            ->where('start_date', '>=', $startDate)
            ->where('end_date', '<=', $endDate)
            ->first();
        if ($already) {
            $stats = $already;
        } else {
            $stats = new KpiOtherReport();
        }

        $count = count($events);

        $stats->manager_id = $managerId;
        $stats->agent_id = $agentId;
        $stats->model = getKpiModel("meetings");
        $stats->kpi = "meetings";
        $stats->kpi_title = "Meetings";
        $stats->total = $count;
        $stats->count = $count;
        $stats->data = [
            [
                'title' => 'Meetings',
                'count' => $stats->count,
            ],
        ];
        $stats->daily_data = $dailyData;

        $stats->start_date = $startDate;
        $stats->end_date = $endDate;
        $stats->duration = Carbon::parse($startDate)->daysInMonth;
        $stats->save();
    }

    private function processFollowUpsKpi($managerId, $agentId, $startDate = null, $endDate = null)
    {
        if (is_null($startDate)) {
            $startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        }
        if (is_null($endDate)) {
            $endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        }

        $start_date = new DateTime($startDate);
        $end_date = new DateTime($endDate);

        $dailyData = [];
        $current_date = clone $start_date;
        while ($current_date <= $end_date) {
            $dailyData[$current_date->format('Y-m-d')] = 0;
            $current_date->modify('+1 day');
        }

        $events = Event::whereDate('start_time', '>=', $start_date->format('Y-m-d'))
            ->whereDate('start_time', '<=', $end_date->format('Y-m-d'))
            ->where(function ($q) use ($agentId) {
                $q->where('agent_id', $agentId)
                    ->orWhereHas("attendees", fn($q2) => $q2->whereIn("user_id", [$agentId]));
            })
            ->whereHas('type', function ($query) {
                $query->where('name', 'Follow-up Reminder');
            })
            ->get();

        foreach ($events as $event) {
            $date = (new DateTime($event->start_time))->format('Y-m-d'); // Convert to DateTime object
            $dailyData[$date]++;
        }

        $already = KpiOtherReport::where('agent_id', $agentId)
            ->where('kpi', 'follow-up')
            ->where('start_date', '>=', $startDate)
            ->where('end_date', '<=', $endDate)
            ->first();
        if ($already) {
            $stats = $already;
        } else {
            $stats = new KpiOtherReport();
        }

        $count = array_sum($dailyData);

        $stats->manager_id = $managerId;
        $stats->agent_id = $agentId;
        $stats->model = getKpiModel("follow-up");
        $stats->kpi = "follow-up";
        $stats->kpi_title = "Follow-up";
        $stats->total = $count;
        $stats->count = $count;
        $stats->data = [
            [
                'title' => 'Follow-up',
                'count' => $stats->count,
            ],
        ];
        $stats->daily_data = $dailyData;

        $stats->start_date = $startDate;
        $stats->end_date = $endDate;
        $stats->duration = Carbon::parse($startDate)->daysInMonth;
        $stats->save();
    }

    private function calculateBayutCredit($kpi, $startDate, $endDate, $portal = 'bayut')
    {

        $user_third_party_data = UserThirdPartyData::where('user_id', $kpi->agent_id)
            ->where('start_date', '>=', $startDate)->where('end_date', '<=', $endDate)
            ->where('portal', $portal . '_credit')
            ->first();
        if (!is_null($user_third_party_data)) {
            $usefulData = json_decode($user_third_party_data->useful_data, true);

            if ($usefulData) {
                $already = KpiReport::where('kpi_id', $kpi->id)->first();
                if ($already) {
                    $stats = $already;
                } else {
                    $stats = new KpiReport();
                }
                $consumed = abs($usefulData['consumed']);
                if ($stats->target > 0) {
                    $achieved_percentage = round(($consumed / $kpi->target) * 100);
                } else {
                    $achieved_percentage = 0;
                }

                $stats->kpi_id = $kpi->id;
                $stats->average_per_day = 0;
                $stats->user_id = $kpi->agent_id;
                $stats->target = $kpi->target;
                $stats->achieved = $consumed;
                $stats->achieved_percentage = $achieved_percentage;
                $stats->data = [
                    [
                        'title' => ($portal == 'bayut' ? 'Bayut' : 'Dubizzle') . ' Credit Consumed',
                        'count' => $consumed,
                    ],
                ];
                $stats->daily_data = [];
                $stats->start_date = $kpi->start_date;
                $stats->end_date = $kpi->end_date;
                $stats->save();
            }
        }
    }
}
