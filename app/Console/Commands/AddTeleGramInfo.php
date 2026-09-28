<?php

namespace App\Console\Commands;
use Excel;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use App\Imports\UsersImport;
use Illuminate\Http\Request;
class AddTeleGramInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:tg';

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
        $result = File::exists('public/user_admin.xlsx');
        $res = Excel::import(new UsersImport, 'public/user_admin.xlsx');
        return Command::SUCCESS;
    }
}
