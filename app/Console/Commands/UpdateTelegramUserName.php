<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Traits\TelegramTrait;
use App\Models\User;

class UpdateTelegramUserName extends Command
{
    use TelegramTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-telegram-user-name';

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
        try{
            echo "update telegram user name start \n";
            $users = User::where('status', 1)->whereNull('telegram_username')->whereNotNull('telegram_chatid')->get();
            $userCount = count($users);
            Log::channel('bitrix_alert')->info("user count $userCount with null telegram user and have telegram chat id");
/*
            $NOTusers = User::where('status', 1)
    ->whereNotNull('telegram_username')
    ->whereNotNull('telegram_chatid')
    ->get();

$userCount = $NOTusers->count();
$userNames = $NOTusers->pluck('name')->implode(', ');

Log::channel('bitrix_alert')->info("User count: $userCount with NOT null Telegram username and chat ID. Users: $userNames");

*/
            
            foreach($users as $user) {
                $telegramUser = $this->getTelegramUsername($user->telegram_chatid);
                $user->telegram_username = $telegramUser;
                $user->save();
                //Log::channel('bitrix_alert')->info("user $user->name updated with $user->telegram_username");
               // echo "user $user->name ($user->telegram_chatid) updated with $user->telegram_username \n";
                Log::channel('bitrix_alert')->info("user $user->name id ($user->id) telegram chat id ($user->telegram_chatid) updated with $user->telegram_username");
            }
            
            echo "update telegram user name end";
        }catch (\Exception $e) {
            Log::channel('bitrix_alert')->info("error", [
                'message' => 'UpdateTelegramUserName failed',
                'status' => $e->getCode(),
                'body' => $e->getMessage(),
            ]);
        }
    }
}
