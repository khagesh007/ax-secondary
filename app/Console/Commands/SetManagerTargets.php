<?php

namespace App\Console\Commands;

use App\Models\KpiTeam;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SetManagerTargets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'set:manager-targets {year?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set manager targets for all months in the given year';

    private function updateQuota($startDate, $endDate, $managerId, $groupId, $target, $kpiKey)
    {
        $kpiTarget = KpiTeam::where('manager_id', $managerId)
            ->where('start_date', '>=', $startDate)
            ->where('end_date', '<=', $endDate)
            ->where('kpi', $kpiKey)
            ->first();

        if (!$kpiTarget) {
            $kpi = new KpiTeam();
            $kpi->manager_id = $managerId;
            $kpi->group_id = $groupId;
            $kpi->model = null;
            $kpi->kpi = $kpiKey;
            $kpi->target = $target;
            $kpi->duration = Carbon::parse($endDate)->diffInDays($startDate) + 1;
            $kpi->start_date = $startDate;
            $kpi->end_date = $endDate;
            $kpi->created_at = now();
            $kpi->save();
        }
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $year = $this->argument('year');

        if (strlen($year) > 0 && strlen($year) != 4) {
            $this->info('Year must be 4 digits');;
        }

        $managers = [
            [
                'id' => 16,
                'name' => 'Gabriela Brunariova',
                'pf_limit' => 20000,
                'bayut_limit' => 45000,
            ],
            [
                'id' => 17,
                'name' => 'Karim Ahmed',
                'pf_limit' => 72000,
                'bayut_limit' => 140000,
            ],
            [
                'id' => 23,
                'name' => 'Otabek Inogamov',
                'pf_limit' => 86000,
                'bayut_limit' => 140000,
            ],
            [
                'id' => 65,
                'name' => 'Ben Thomas',
                'pf_limit' => 15000,
                'bayut_limit' => 30000,
            ],
            [
                'id' => 129,
                'name' => 'Azim Choudry',
                'pf_limit' => 20000,
                'bayut_limit' => 35000,
            ],
            [
                'id' => 1165,
                'name' => 'Sam Ayache',
                'pf_limit' => 26000,
                'bayut_limit' => 70000,
            ],
            [
                'id' => 1310,
                'name' => 'Natascha Louise Ford',
                'pf_limit' => 26000,
                'bayut_limit' => 40000,
            ],
        ];

        for ($month = 1; $month <= 13; $month++) {
            $date = Carbon::create($year, $month, 1)->format('Y-m-d');
            $bayutDates = getBayutCreditDurationDates($date);
            $pfDates = getPfCreditDurationDates($date);
            foreach ($managers as $item) {
                $manager = User::find($item['id']);
                if ($manager){
                    $this->updateQuota($bayutDates['start_date'], $bayutDates['end_date'], $manager->id, $manager->group_id, $item['bayut_limit'], 'bayut_credit_limit');
                    $this->updateQuota($pfDates['start_date'], $pfDates['end_date'], $manager->id, $manager->group_id, $item['pf_limit'], 'pf_credit_limit');
                }
            }
        }
        return Command::SUCCESS;
    }
}
