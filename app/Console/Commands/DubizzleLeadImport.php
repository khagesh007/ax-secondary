<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Dubizzle\DubizzleLeadImportController;

class DubizzleLeadImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dubizzle:leadImport';

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
        (new DubizzleLeadImportController())->handle();
        (new DubizzleLeadImportController())->handleAbuDhabi();
        (new DubizzleLeadImportController())->handleCommercial();
        return Command::SUCCESS;
    }
}
