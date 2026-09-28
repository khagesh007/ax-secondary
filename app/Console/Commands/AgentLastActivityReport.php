<?php

namespace App\Console\Commands;

use App\Models\Approval;
use App\Models\Contact;
use App\Models\ContactMedia;
use App\Models\ContactNote;
use App\Models\Event;
use App\Models\LeadMedia;
use App\Models\ModelUpdate;
use App\Models\PropertyMedia;
use App\Models\User;
use App\Models\UserLastActivity;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Telegram\Bot\Laravel\Facades\Telegram;
use App\Models\Lead;
use App\Models\LeadNote;
use App\Models\Property;
use App\Models\PropertyNote;
use App\Models\ListingLead;
use App\Models\ListingLeadNote;
use Spatie\Activitylog\Models\Activity;
class AgentLastActivityReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agent:lastActivityReport';
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
        $collection = collect();
        $exempted_users = [
            1403, 307, 4, 172,1046, 1047, 1081, 1215, 1432,1522

        ];

        $users = User::where('is_report_exempt',false)
            ->where('status', true)
            ->whereNotIn('id', $exempted_users)
            ->whereHas('roles',function($q){
            $q->where('name','Agent');
        })->chunk(50, function ($chunk) use ($collection) {



            foreach ($chunk as $user) {

                $last_activity = UserLastActivity::where('user_id', $user->id)->first();
             if($last_activity != null) {
                 $last_time = $last_activity->last_active;
             }else{
                    $last_time = null;
             }
                $hours = 0;
                if($last_time == null){
                    $tempMsg = " - <b>" . $user->full_name . "</b> has No Activity.\n";
                    $minutes = 0;
                }else{
//                    $last_login = Carbon::parse($last_time);
//                    $current_time = Carbon::now();
//                    $time_difference = $current_time->diff($last_login);
//
//                    $hours = $time_difference->h + ($time_difference->d * 24);

                    $last_login = new \DateTime($last_time);
                    $current_time = new \DateTime();
                    $time_difference = $current_time->diff($last_login);

                    $hours = $time_difference->h + ($time_difference->days * 24);
                    $minutes = $time_difference->i;

                    $dayHours =  formatTimeReport($hours);
                    $tempMsg = " - <b>" . $user->full_name . "</b> : ".$dayHours." ".$minutes. "m\n";


                }
                if($hours < 1) {
                    if ($minutes > 0){
                        $hours = $minutes / 60;
                    }
                }

                    $collection->push([
                        'user_id' => $user->id,
                        'hours' => $hours,
                        'message' => $tempMsg
                    ]);

                //sort the collection



            }


        });
        $sorted = $collection->sortBy('hours');
        $sorted->values()->all();
        $collection = $sorted->reverse();
        $message  = "";
        $isStart = true;
        foreach ($collection as $item) {

            if(!$isStart && $this->counter == 0) {
                $message .= "<b>AX Secondary CRM</b>\n\n";
                $message .= "⏰ Agent Last Activity Report  ⏰\n";
                $message .= "--------------------------------------------\n";
            }

            if ($this->counter == 0 && $isStart) {
                $message .= "<b>AX Secondary CRM</b>\n\n";
                $message .= "⏰ Agent Last Activity Report ⏰\n";
                $message .= "--------------------------------------------\n";
                $message .= "Good Evening,\n\n";
                $message .= 'Here is the summary of agent activity in the Secondary CRM.' . "\n";
                $message .= "--------------------------------------------\n";
            }
            $message .= $item['message'];

            $this->counter++;
            if ($this->counter == 50) {


                $telegram = Telegram::bot('notification_bot');
                $telegramMessage = $this->sendTelegramMessage($telegram, $message);
                $message = '';
                $this->counter = 0;
                $isStart = false;
            }
        }
        if (!empty($message)) {
            $telegram = Telegram::bot('notification_bot');
            $telegramMessage = $this->sendTelegramMessage($telegram, $message);
        }


        return Command::SUCCESS;
    }

    public function sendTelegramMessage($telegram, $message)
    {

        if (env('APP_ENV') == 'production') {

            $chatids = [
              //  '795165330',
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
                   // \Log::info('Telegram message sent to chat_id: ' . $chatid . ' message is: ' . $message);
                } catch (\Throwable $th) {
                    \Log::info('Telegram error log: ' . $th->getMessage() . " chat_id is: " . $chatid . " message is: " . $message);
                    continue;
                }
            }

        } else {

            $chatids = [
                '795165330',
//                '5970238984',
//                 '1417490671',
            ];
            foreach ($chatids as $chatid) {
                try {
                    $telegram->sendMessage([
                        'chat_id' => $chatid,
                        'text' => $message,
                        'parse_mode' => 'html',
                    ]);
                   // \Log::info('Telegram message sent to chat_id: ' . $chatid . ' message is: ' . $message);

                } catch (\Throwable $th) {
                    \Log::info('Telegram error log: ' . $th->getMessage() . " chat_id is: " . $chatid . " message is: " . $message);
                    continue;
                }
            }

        }

    }
}
