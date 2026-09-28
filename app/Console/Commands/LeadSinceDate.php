<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Telegram\Bot\Laravel\Facades\Telegram;

class LeadSinceDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lead:countWithUser {since}';
    protected $counter = 0;
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Count the number of leads since a given date';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $date_from = $this->argument('since');
        $date_from = Carbon::parse($date_from);
        //format date in this format January 1st, 2024
        $formatted_date = $date_from->format('F jS, Y');
        $collection = collect();
        $users = User::where('is_report_exempt', false)
            ->where('status', true)
            ->where('entity', 'Secondary')
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })->chunk(50, function ($chunk) use ($collection,$date_from) {
                foreach ($chunk as $user) {
                    $leads = $user->leads()->whereIn("is_archive", [0, 1])->where('created_at', '>=', $date_from)->count();
                    // $this->info($user->name . ' has ' . $leads);
                    $collection->push([
                        'user_id' => $user->id,
                        'name' => $user->name,
                        'leads' => $leads,
                        'message' => $user->name . ': ' . $leads
                    ]);
                }
            });
        $sorted = $collection->sortBy('leads');
        $sorted->values()->all();
        $collection = $sorted->reverse();
        $message  = "";
        $isStart = true;
        foreach ($collection as $item) {

            if(!$isStart && $this->counter == 0) {
                $message .= "<b>AX Secondary CRM</b>\n\n";
                $message .= " 📈  Number of leads received since $formatted_date 📈\n";
                $message .= "--------------------------------------------\n";
                $message.= "\n<b>SECONDARY</b> \n";
            }

            if ($this->counter == 0 && $isStart) {
                $message .= "<b>AX Secondary CRM</b>\n\n";
                $message .= " 📈  Number of leads received since $formatted_date 📈\n";
                $message .= "--------------------------------------------\n";

                $message.= "\n<b>SECONDARY</b> \n";
            }
            $message .= $item['message']."\n";

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
                '795165330',
                '1417490671',
                //'5970238984',
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
               // '1417490671',
//                '5970238984',
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
