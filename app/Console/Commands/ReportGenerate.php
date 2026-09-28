<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ReportGenerate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will trigger all the reports to be generated for all modules.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Generating reports for all modules...');

        return Command::SUCCESS;
    }
}
