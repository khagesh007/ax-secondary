<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Testing\BayutLeadsFetchJobController;
use App\Http\Controllers\Dubizzle\DubizzleLeadImportController;



class BayutLeadImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bayut:leadImport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports leads from Bayut';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        (new BayutLeadsFetchJobController())->handle();
        (new BayutLeadsFetchJobController())->handleAbuDhabi();
        (new BayutLeadsFetchJobController())->handleCommercial();


        // (new DubizzleLeadImportController())->handle();
        // (new DubizzleLeadImportController())->handleAbuDhabi();
        // (new DubizzleLeadImportController())->handleCommercial();

        return Command::SUCCESS;
    }
}
