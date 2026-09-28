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
class AgentLastActivityReportManager extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agent:lastActivityReportManager';
    protected $counter = 0;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will send last activity report of agents to managers.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $managers = User::whereHas("roles", function ($q) {
            $q->whereIn("name", ["Line Manager", "Team Leader", "Sales Director"]);
        })->where('region_id', '!=', 2)->where("status", true)->get();
        foreach ($managers as $manager) {
            $collection = collect();
            $exempted_users = [
                1403, 307, 4, 172, 1046, 1047, 1081, 1215, 1432, 1522
            ];
            $users = User::where('is_report_exempt', false)
                ->where('status', true)
                ->where('parent_id', $manager->id)
                ->whereNotIn('id', $exempted_users)
                ->whereHas('roles', function ($q) {
                    $q->where('name', 'Agent');
                })->get();
            foreach ($users as $user) {
                $last_time = getUserLastActiveTime($user);
                $last_login_time =   $user?->last_login ?? null;
                $last_login_time = $last_login_time ? getSingleUnitTimeDiff($last_login_time) : null;

                $hours = 0;
                if ($last_time == null) {
                    $tempMsg = " - <b>" . $user->full_name . "</b> has No Activity.\n";
                    $minutes = 0;
                } else {
                    $last_login = new \DateTime($last_time);
                    $current_time = new \DateTime();
                    $time_difference = $current_time->diff($last_login);
                    $hours = $time_difference->h + ($time_difference->days * 24);
                    $minutes = $time_difference->i;
                    $dayHours = formatTimeReport($hours);
                    //remove space from string
                   // $dayHours = str_replace(' ', '', $dayHours);
                    if($last_login_time != null){
                        $tempMsg = "-<b>" . $user->full_name . "</b> (🕐" .$last_login_time.") " . $dayHours . $minutes . "m\n";
                    }else {
                        $tempMsg = "-<b>" . $user->full_name . "</b> ". $dayHours . $minutes . "m\n";
                    }
                }
                if ($hours < 1) {
                    if ($minutes > 0) {
                        $hours = $minutes / 60;
                    }
                }
                $collection->push([
                    'user_id' => $user->id,
                    'hours' => $hours,
                    'message' => $tempMsg
                ]);
            }
            $sorted = $collection->sortBy('hours');
            $sorted->values()->all();
            $collection = $sorted->reverse();
            $message = "";
            foreach ($collection as $item) {
                $message .= $item['message'];
            }
            if (!empty($message)) {
                $greeting = "";
                $greeting .= "<b>AX Secondary CRM</b>\n\n";
                $greeting .= "⏰ Agent Last Activity Report ⏰\n";
                $greeting .= "--------------------------------------------\n";
                $greeting .= 'Good Evening ' . $manager->first_name . ",\n\n";
                $greeting .= 'Here is the summary of agent activity in the Secondary CRM.' . "\n";
                $greeting .= "--------------------------------------------\n";

                $greeting .=$message;
                $telegram = Telegram::bot('notification_bot');
                $this->sendTelegramMessage($telegram, $greeting, $chatid = $manager->telegram_chatid);
                $message = '';
            }
        }

        return Command::SUCCESS;
    }

    public function sendTelegramMessage($telegram, $message,$chatid)
    {


        if (env('APP_ENV') == 'production') {

//            $chatids = [
//                '795165330',
//                '1417490671',
//                '5970238984',
//            ];

            //foreach ($chatids as $chatid) {
                try {
                    $telegram->sendMessage([
                        'chat_id' => $chatid,
                        'text' => $message,
                        'parse_mode' => 'html',
                    ]);
                  //  \Log::info('Telegram message sent to chat_id: ' . $chatid . ' message is: ' . $message);
                } catch (\Throwable $th) {
                    \Log::info('Telegram error log: ' . $th->getMessage() . " chat_id is: " . $chatid . " message is: " . $message);

                }
           // }

        } else {
\Log::info('manager chat id '.$chatid);
            $chatids = [
             //   '795165330',
//                '5970238984',
//                '1417490671',
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
