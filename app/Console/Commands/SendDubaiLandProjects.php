<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SendDubaiLandProjectsToAXDeals;

class SendDubaiLandProjects extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dubailand:projects';

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

        SendDubaiLandProjectsToAXDeals::dispatch();

        return Command::SUCCESS;
    }
}
