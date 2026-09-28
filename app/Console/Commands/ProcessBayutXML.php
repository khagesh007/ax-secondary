<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\XMLFeedV2Controller;
use Illuminate\Console\Command;

class ProcessBayutXML extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:bayutxml';

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
        $xmlFeedController->generateDubizzleBayutXMLFile(); //
        $xmlFeedController->generateBayutDubizzleAbuDhabiXMLFile(); //
        $xmlFeedController->generateBayutDubizzleCommercialXMLFile(); //

        // $xmlFeedController->generateDubizzleXMLFile();
        // $xmlFeedController->generateBayutXMLFile();


        return Command::SUCCESS;
    }
}
