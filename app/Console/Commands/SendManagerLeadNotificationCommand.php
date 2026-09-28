<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
class SendManagerLeadNotificationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'manager:incompleteleadnotification';

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
        $users = User::where('status',true)->limit(10)->get()
        ;

        foreach($users as $user ){
            info(parentFinder($user->bitrix_role));
            sleep(2);
        }

        return Command::SUCCESS;
    }
}
