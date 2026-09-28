<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Api\XMLFeedV2Controller;

class ProcessHouzaXML extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:houzaxml';

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
        $xmlFeedController = new XMLFeedV2Controller();
        $xmlFeedController->generateHouzaXMLFile();
        return Command::SUCCESS;
    }
}
