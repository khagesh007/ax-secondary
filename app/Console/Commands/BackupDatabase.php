<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:mysql';

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
        $dbName = config('database.connections.mysql.database');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');
        $fileName = 'secondary-backup.sql';
        $filePath = public_path("/backups/".$fileName);

        $command = sprintf(
            'mysqldump -u%s -p%s %s > %s',
            escapeshellarg($dbUser),
            escapeshellarg($dbPass),
            escapeshellarg($dbName),
            escapeshellarg($filePath)
        );

        $this->info("📦 Creating backup...");
        $result = null;
        $output = null;
        exec($command, $output, $result);

        if ($result !== 0) {
            $this->error('❌ Backup failed.');
            return Command::FAILURE;
        }

        $url = url("backups/".$fileName);
        $this->info("✅ Backup created successfully.");
        $this->info("📥 Download URL: $url");

        return Command::SUCCESS;
    }
}
