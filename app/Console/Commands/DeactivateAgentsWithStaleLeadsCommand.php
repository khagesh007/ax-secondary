<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DeactivateAgentsWithStaleLeadsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agentsstaleleads:cron';

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
        dispatch(new \App\Jobs\DeactivateAgentsWithStaleLeadsJob());

        return Command::SUCCESS;
    }
}
