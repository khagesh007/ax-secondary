<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use DB;
class FolderDbRefresh extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'folder:db-refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will drop and run migration related to document module';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $migrations = [
            [
                'table' => 'folder_media',
                'migration' => '2023_11_13_113519_create_folder_media_table.php'
            ],
            [
                'table' => 'folder_tags',
                'migration' => '2023_11_21_094357_create_folder_tags_table.php'
            ],
            [
                'table' => 'folders',
                'migration' => '2023_11_09_132919_create_folders_table.php'
            ],
        ];
        foreach ($migrations as $migration) {
            $this->dbWorker($migration['table'], $migration['migration']);
        }

        return Command::SUCCESS;
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
