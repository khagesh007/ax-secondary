<?php

namespace App\Console\Commands;

use App\Models\Property;
use Illuminate\Console\Command;
use Telegram\Bot\Laravel\Facades\Telegram;

class NewlyPublishedProperty extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'property:newlyPublished';

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

        $properties = Property::where('publish_status', 'PUBLISHED')->whereBetween('published_date', [now()->subDay(), now()])->get();

        if (env('APP_ENV') == 'local') {
            $properties = Property::where('publish_status', 'PUBLISHED')->limit(30)->get();
        }

        $message = "";
        $message = "<b>AX Secondary CRM</b>\n\n";
        $message .= "<b>Recently Published Listing Report</b>\n";
        $message .= "--------------------------------------------\n";
        $message .= "Good Evening,\n\n";
        $message .= 'Here is the summary of Published Listing in the past 24 hours.' . "\n";
        $message .= "--------------------------------------------\n";
        $counter = 0;
        $res_for_sale = 0;
        $res_for_rent = 0;
        $com_for_sale = 0;
        $com_for_rent = 0;
        foreach ($properties as $property) {
            if ($property->for == 'Residential for sale') {
                $res_for_sale++;
            }
            if ($property->for == 'Residential for rent') {
                $res_for_rent++;
            }
            if ($property->for == 'Commercial for sale') {
                $com_for_sale++;
            }
            if ($property->for == 'Commercial for rent') {
                $com_for_rent++;
            }
            if ($counter > 15) {
                continue;
            }

            $location = $property?->communityProperty?->path_name ?? $property?->subCommunity?->path_name;
            $message .= "<b>Purpose:</b> " . emojiDecider($property?->for) . " " . $property?->for . "\n";
            $message .= "<b>Location:</b> " . $location . "\n";
            $message .= "<b>Price:</b> AED " . priceFormatter($property?->price?->price) . "\n";
            $message .= "<b>Details:</b> " . $property?->ref_no . " | " . $property?->bed?->name . ", " . $property?->bath?->name . " \n";
            $message .= "<b>Agent:</b> " . $property?->agent?->full_name . "\n";
            $message .= "--------------------------------------------\n";
            $counter++;
        }

        if ($properties->count() > 15) {
            $total_properties = $properties->count() - 15;
            $message .= "...... ".$total_properties." more (Please login to CRM to view all)\n\n";
        }

        $message .= "<b>Residential for sale: </b>" . $res_for_sale . "  \n";
        $message .= "<b>Residential for rent: </b>" . $res_for_rent . "  \n";
        $message .= "<b>Commercial for sale: </b>" . $com_for_sale . "  \n";
        $message .= "<b>Commercial for rent: </b>" . $com_for_rent . "  \n";

        $message .= "<b>Total Published Listing in the past 24 hours: </b>" . $properties->count() . "  \n";
        $telegram = Telegram::bot('notification_bot');
        $telegramMessage = $this->sendTelegramMessage($telegram, $message);

        return Command::SUCCESS;
    }

    public function sendTelegramMessage($telegram, $message)
    {

        if (env('APP_ENV') == 'production') {

            $chatids = [
                '1669212762',
                '1417490671',
                '5970238984'
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
                    \Log::info('Telegram error log: ' . $th->getMessage() . " chat_id is: ".$chatid." message is: " . $message);
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
                \Log::info('Telegram error log: ' . $th->getMessage() . " chat_id is: ".$chatid." message is: " . $message);
                continue;
            }
            }

        }

    }
}
