<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class KpiRunCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kpi:run-commands';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

//        $this->call('kpi:generate-report', [
//            'date' => date('Y-m-d')
//        ]);
        \Artisan::call('kpi:generate-report',['date' => date('Y-m-d')]);
        \Artisan::call('kpi:generate-team-report',['date' => date('Y-m-d')]);
//        $this->call('kpi:generate-team-report', [
//            'date' => date('Y-m-d')
//        ]);

        return Command::SUCCESS;
    }
}
