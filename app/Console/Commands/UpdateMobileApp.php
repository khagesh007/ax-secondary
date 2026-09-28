<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Traits\TelegramTrait;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class UpdateMobileApp extends Command
{
    use TelegramTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:mobileApp';

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
        $superAdminIds = [
            1844,
            173,
            1041,

        ];
        $supportTeam = User::find(1041);

        $userIds =\App\Models\User::whereHas("roles", fn($q) => $q->whereIn("name", ["Agent"]))->where('status', true)->pluck('id')->toArray();
        Log::channel('bitrix_alert')->info('agentCount');
        Log::channel('bitrix_alert')->info(count($userIds));
        $mergedIds = array_merge($superAdminIds, $userIds);
        
        foreach($userIds as $userId){
            $user = User::where('id',$userId)->where('status', true)->first();
            echo "User id is $userId\n";
            Log::channel('bitrix_alert')->info("User id is $userId name is $user->full_name");
            
            if($user){
                // SendApkAttachment with telegram
                $chatId = $user->telegram_chatid;
                if($chatId){

                    $message = "Good day {$user->full_name},\n\n";

                    $message .= "We’re pleased to announce that the <b>AX CHAT</b> Mobile App is now available.\n\n";
                    $message .= "<b>📲 First-Time Setup Instructions</b>\n";
                    $message .= "To get started, the AX CHAT app must be installed manually using the APK file provided below.\n\n";
                    $message .= "<b>🔔 After Installation</b>\n";
                    $message .= "Make sure to allow notification permissions when prompted.\n\n";
                    $message .= "<b>🔐 Login Options</b>\n";
                    $message .= "You have two ways to log in:\n\n";

                    $message .= "<b>Option 1:</b>\n";
                    $message .= "- Enter your email address and click Sign In.\n";
                    $message .= "- A 4-digit OTP will be sent to your Telegram bot (AX Secondary CRM).\n";
                    $message .= "- Enter the code to complete the login.\n\n";


                    $message .= "<b>Option 2:</b>\n";
                    $message .= "- If you have the latest version of the <b>AX Secondary Mobile App</b> (if not, please contact the IT team to update),\n";
                    $message .= "- Open the left-side menu and tap <b>AX CHAT</b>.\n\n";

                    $message .= "You will be logged in automatically, and no additional login will be required in the future.\n\n";
                    $message .= "If you need any support, feel free to contact the IT team\n";
                    $message .= "<a href=\"https://t.me/{$supportTeam->telegram_username}\">@{$supportTeam->telegram_username}</a>\n";

                    $vedio = false;
                    $val = $this->sendApkDocument($chatId,$message, $vedio);

                    if($val == false){
                        echo "The apk was not sent to $user->full_name\n";
                        Log::channel('bitrix_alert')->info("The apk was not sent to $user->full_name");
                    }else{
                        echo "The apk was sent to $user->full_name\n";
                        Log::channel('bitrix_alert')->info("The apk was sent to $user->full_name");
                    }
                } else{
                    echo "User $userId doesn't have chat id\n";
                    Log::channel('bitrix_alert')->info("User $user->full_name doesn't have chat id");

                }
            }
        }
        Log::channel('bitrix_alert')->info("The apk was sent to all agents");
        
        return Command::SUCCESS;
    }
}
