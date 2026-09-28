<?php

namespace App\Console\Commands;

use App\Models\BulkMessage;
use Illuminate\Console\Command;

class ProcessBulkSms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:bulksms';

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
        $messages = BulkMessage::where('status', 2)->whereHas('campaign', function ($query) {
            $query->where('status', 1);
        })->get();



        foreach ($messages as $message) {

            $phone_number = '971585858176';
            if (env('APP_ENV') == 'production') {
                $phone_number = $message->mobile;
            }

            try {
                $response = $this->sendSingleMessage($phone_number, $message->agent_id, $message->message, $message->short_url);
            } catch (\Throwable $th) {
                info($th);
                continue;
                throw $th;
            }

            $message->response = json_encode($response);
            if ($response[0]['ErrorCode'] == 0) {
                $message->status = 1;
                $message->save();
            } else {
                $message->status = 0;
                $message->save();
            }

            if (env('APP_ENV') != 'production') {
                break;
            }


        }
    }

    public function sendSingleMessage($phoneNumber, $agent_id, $message, $url)
    {

        $base_url = env('SMS_BASE_URL');
        // $extra_content = "\n\nClick the link below to choose your preferred communication method for our agent to contact you: ";
        // $extra_content .= urlShortner(route('public.smslinkClicked', $url));
        // $message = $message . ' ' . $extra_content;

        //testing only
        // $response = Telegram::bot('axcontactsbot')->sendMessage([
        //     'chat_id' => 6212690505,
        //     'text' => $message,
        //     'parse_mode' => 'HTML',
        // ]);
        //testing only

        $data = array(
            "source" => "AD-AXCAP",
            "destination" => [$phoneNumber],
            "text" => $message,
        );
        $data_string = json_encode($data);

        $ch = curl_init($base_url);
        curl_setopt($ch, CURLOPT_POST, true);
        //send json data
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: ' . env('SMS_AUTHORIZATION_HEADER'),
            'Content-Type: application/json',
        ));

        $response = curl_exec($ch);

        curl_close($ch);
        $response = json_decode($response, true);
        // if ($response[0]['ErrorCode'] == 0) {
        //     BulkSms::create([
        //         'to' => $phoneNumber,
        //         'agent_id' => $agent_id,
        //         'agent_secondary_id' => $agent_id,
        //         'status' => 'sent',
        //         'message' => $message,
        //         'link_clicked' => 0,
        //         'created_by_id' => $agent_id,
        //         'updated_by_id' => $agent_id,
        //     ]);

        //     Log::info("Message successfully sent to " . $phoneNumber . " By Agent: " . $agent_id);
        // } else {
        //     BulkSms::create([
        //         'to' => "+" . $phoneNumber,
        //         'agent_id' => $agent_id,
        //         'agent_secondary_id' => $agent_id,
        //         'status' => 'failed',
        //         'message' => $message,
        //         'link_clicked' => 0,
        //         'created_by_id' => $agent_id,
        //         'updated_by_id' => $agent_id,
        //     ]);
        //     Log::info("Message sending failed to " . $phoneNumber . " By Agent: " . $data["agent_id"]);

        // }
        return $response;
    }
}
