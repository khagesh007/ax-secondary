<?php

namespace App\Console\Commands;

use App\Jobs\AgentCalendarReport;
use App\Jobs\ManagerCalendarReport;
use Illuminate\Console\Command;

class AgentCalendarReportGenerator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agent:CalendarReportGenerator';

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
        dispatch(new AgentCalendarReport());
        dispatch(new ManagerCalendarReport());
        return Command::SUCCESS;
    }
}
