<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use DB;

class PruneTelescope extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'prune:telescope';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prune old Telescope entries';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $daysToKeep = 4; // Adjust this value as needed
            $beforeDate = now()->subDays($daysToKeep);

            DB::connection('log_db')->table('telescope_entries')->where('created_at', '<', $beforeDate)->delete();
        } catch (\Exception $e) {
            //$this->error('Unable to prune Telescope entries.');
            $this->error($e->getMessage());
        }


        return Command::SUCCESS;
    }
}
