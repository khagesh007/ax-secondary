<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\AgentIncompleteLeadNotificationJob;
use App\Jobs\ManagerIncompleteLeadNotificationJob;

class IncompleteLeadReportGenerator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agent:IncompleteLeadReport';

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
        dispatch(new AgentIncompleteLeadNotificationJob());
        dispatch(new ManagerIncompleteLeadNotificationJob());
        return Command::SUCCESS;
    }
}
