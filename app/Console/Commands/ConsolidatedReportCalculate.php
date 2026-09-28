<?php

namespace App\Console\Commands;

use App\Models\ConsolidatedReport;
use App\Models\KpiTarget;
use App\Models\User;
use Illuminate\Console\Command;

class ConsolidatedReportCalculate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ax:consolidated-report-calculate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command calculates the consolidated report for users. From KPI Report';

    /**
     * Execute the console command.
     *
     * @return int
     */
    function getLastDatesOfLast6Months(): array
{
    $dates = [];

    for ($i = 0; $i < 6; $i++) {
        $date = new \DateTime("last day of -$i month");
        $dates[] = $date->format('Y-m-d');
    }

    return $dates;
}
    private function calculateConsolidatedReport(User $agent)
    {
        $dates = $this->getLastDatesOfLast6Months();
        foreach ($dates as $date) {
            $target = KpiTarget::where('agent_id', $agent->id)->where('end_date', $date)->with('result')->first();
            $consolidatedReport = ConsolidatedReport::where('user_id', $agent->id)
                ->where('month', date('Y-m', strtotime($date)))
                ->first();

            if (!$consolidatedReport) {
                $consolidatedReport = new ConsolidatedReport();
                $consolidatedReport->user_id = $agent->id;
                $consolidatedReport->user_parent_id = $agent->parent_id;
                $consolidatedReport->month = date('Y-m', strtotime($date));
            }

            if ($target) {
                $consolidatedReport->target = $target->target;
                $consolidatedReport->last_recorded_date = $date;
                if ($target->result) {
                    $consolidatedReport->achievement = $target->result->achieved;
                } else {
                    $consolidatedReport->achievement = 0;
                }
            } else {
                $consolidatedReport->target = 0;
                $consolidatedReport->achievement = 0;
            }

            $consolidatedReport->save();
        }
    }

    public function handle()
    {
        $agents = User::whereHas('roles', function ($query) {
                $query->where('name', 'agent');
            })
            ->where('status', true)->get();
        foreach ($agents as $agent) {
            $this->calculateConsolidatedReport($agent);
        }

    }

}
