<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use DB;
class DealDbRefresh extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deal:db-refresh {table?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will drop and run migration related to deals module';

    /**
     * Execute the console command.
     *
     * @return int
     */

    public function handle()
    {

        $table = $this->argument('table');


        $migrations = [
            [
                'table' => 'deal_notes',
                'migration' => '2022_10_04_183155_create_deal_notes_table.php'
            ],
            [
                'table' => 'deal_split_commissions',
                'migration' => '2023_06_03_123627_create_deal_split_commissions_table.php'
            ],
            [
                'table' => 'deal_splits',
                'migration' => '2023_06_03_123042_create_deal_splits_table.php'
            ],
            [
                'table' => 'deal_meta',
                'migration' => '2023_07_11_164833_create_deal_metas_table.php'
            ],
            [
                'table' => 'deal_media',
                'migration' => '2023_07_11_171341_create_deal_media_table.php'
            ],[
                'table' => 'deal_leads',
                'migration' => '2023_07_12_101240_create_deal_leads_table.php'
            ],[
                'table' => 'deal_tasks',
                'migration' => '2023_07_17_155445_create_deal_tasks_table.php'
            ],
            [
                'table' => 'deal_deductibles',
                'migration' => '2023_09_06_105219_create_deal_deductibles_table.php'
            ],
            [
                'table' => 'deal_invites',
                'migration' => '2023_11_25_144234_create_deal_invites_table.php'
            ],
//            [
//                'table' => 'commission_slabs',
//                'migration' => '2023_06_02_103741_create_commission_slabs_table.php'
//            ],
//            [
//                'table' => 'commission_slab_limits',
//                'migration' => '2023_06_02_103847_create_commission_slab_limits_table.php'
//            ]
//            ,[
//                'table' => 'commission_slab_users',
//                'migration' => '2023_06_02_103904_create_commission_slab_users_table.php'
//            ],
            [
                'table' => 'deals',
                'migration' => '2022_09_13_071907_create_deals_table.php'
            ],
            [
                'table' => 'deal_splits',
                'migration' => '2023_06_03_123042_create_deal_splits_table.php'
            ],
            [
                'table' => 'deal_split_commissions',
                'migration' => '2023_06_03_123627_create_deal_split_commissions_table.php'
            ],
            [
                'table' => 'deal_media',
                'migration' => '2023_07_11_171341_create_deal_media_table.php'
            ],
            [
                'table' => 'contact_media',
                'migration' => '2023_10_10_145036_create_contact_media_table.php'
            ],
            [
                'table' => 'lead_media',
                'migration' => '2023_07_11_171300_create_lead_media_table.php'
            ], [
                'table' => 'deal_approvals',
                'migration' => '2023_12_30_133128_create_deal_approvals_table.php'
            ],
//            [
//                'table' => 'deal_leads',
//                'migration' => '2023_07_12_101240_create_deal_leads_table.php'
//            ],
            // [
            //     'table' => 'deal_commission_sources',
            //     'migration' => '2023_11_22_121251_create_deal_commission_sources_table.php'
            // ]

        ];

        if($table){
            $migration = collect($migrations)->where('table',$table)->first();
            $this->dbWorker($migration['table'], $migration['migration']);
            return Command::SUCCESS;
        }else{
            foreach ($migrations as $migration) {
                $this->dbWorker($migration['table'], $migration['migration']);
            }
            return Command::SUCCESS;
        }
    }

    private function dbWorker($tableName, $migration = null)
    {
        if (Schema::hasTable($tableName)) {
            $this->info("Dropping $tableName table...");
            //drop table without checking foreign key
             DB::statement('SET FOREIGN_KEY_CHECKS=0;');
             Schema::dropIfExists($tableName);
            $this->info("$tableName table dropped successfully.");
        } else {
            $this->info("$tableName table does not exist.");
        }
        //remove migration from migrations table if exists
        $migration_name = substr_replace($migration, '', -4);
        if(DB::table('migrations')->where('migration', $migration_name)->exists()){
            DB::table('migrations')->where('migration', $migration_name)->delete();
        }
        $migrationPath = database_path('migrations/' . $migration);
        if (file_exists($migrationPath)) {
            $this->info("Running migration $migration...");
            Artisan::call('migrate', [
                '--path' => 'database/migrations/' . $migration,
            ]);
            $this->info("Migration completed successfully.");
        } else {
            $this->error("Migration file not found: $migrationPath");
        }
    }
}
