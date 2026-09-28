<?php

namespace App\Console\Commands;
use App\Jobs\AgentListingLeadReport;
use App\Jobs\ManagerListingLeadReport;

use Illuminate\Console\Command;

class ListingLeadReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agent:ListingLeadReport';

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
        dispatch(new AgentListingLeadReport());
        dispatch(new ManagerListingLeadReport());
        return Command::SUCCESS;
    }
}
