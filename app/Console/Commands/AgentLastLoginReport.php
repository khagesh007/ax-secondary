<?php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Telegram\Bot\Laravel\Facades\Telegram;
use App\Models\Lead;
use App\Models\LeadNote;
use App\Models\Property;
use App\Models\PropertyNote;
use App\Models\ListingLead;
use App\Models\ListingLeadNote;


class AgentLastLoginReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agent:lastLoginReport';
    protected $counter = 0;

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

        $users = User::where('status', true)->whereHas('roles',function($q){
            $q->where('name','Agent');
        })->chunk(50, function ($chunk) {

            $message = "";
            $message = "<b>AX Secondary CRM</b>\n\n";
            $message .= "⏰ Agent Activity Status in Secondary CRM ⏰\n";
            $message .= "--------------------------------------------\n";

            if ($this->counter == 0) {
                $message .= "Good Evening,\n\n";
                $message .= 'Here is the summary of Published Listing in the past 24 hours.' . "\n";
                $message .= "--------------------------------------------\n";
            }

            $this->counter++;

            foreach ($chunk as $user) {


                if ($user->last_login == null) {
                    $message .= " - <b>" . $user->full_name . "</b> has not logged in yet.\n";
                } else {
                    $last_login = Carbon::parse($user->last_login);
                    $current_time = Carbon::now();
                    $time_difference = $current_time->diff($last_login);

                    $hours = $time_difference->h + ($time_difference->d * 24);
                    $minutes = $time_difference->i;
                    if ($hours > 0) {
                        $message .= " - <b>" . $user->full_name . "</b> : " . $hours . " hours and " . $minutes . " minutes ago\n";
                    } else {
                        $message .= " - <b>" . $user->full_name . "</b> : " . $minutes . " minutes ago\n";
                    }
                }

            }

            $telegram = Telegram::bot('notification_bot');
            $telegramMessage = $this->sendTelegramMessage($telegram, $message);

        });

        return Command::SUCCESS;
    }

    public function sendTelegramMessage($telegram, $message)
    {

        if (env('APP_ENV') == 'production') {

            $chatids = [
                '1417490671',
                '5970238984',
            ];

            foreach ($chatids as $chatid) {
                try {
                    $telegram->sendMessage([
                        'chat_id' => $chatid,
                        'text' => $message,
                        'parse_mode' => 'html',
                    ]);
                    \Log::info('Telegram message sent to chat_id: ' . $chatid . ' message is: ' . $message);
                } catch (\Throwable $th) {
                    \Log::info('Telegram error log: ' . $th->getMessage() . " chat_id is: " . $chatid . " message is: " . $message);
                    continue;
                }
            }

        } else {

            $chatids = [
                '5970238984',
                // '1417490671',
            ];
            foreach ($chatids as $chatid) {
                try {
                    $telegram->sendMessage([
                        'chat_id' => $chatid,
                        'text' => $message,
                        'parse_mode' => 'html',
                    ]);
                    \Log::info('Telegram message sent to chat_id: ' . $chatid . ' message is: ' . $message);

                } catch (\Throwable $th) {
                    \Log::info('Telegram error log: ' . $th->getMessage() . " chat_id is: " . $chatid . " message is: " . $message);
                    continue;
                }
            }

        }

    }
}
