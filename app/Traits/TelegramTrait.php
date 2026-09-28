<?php

namespace App\Traits;

use App\Models\Lead;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Laravel\Facades\Telegram;
use Illuminate\Support\Facades\Http;
use Telegram\Bot\Api;
use Illuminate\Support\Facades\Log;

trait TelegramTrait
{
    /*
     * We have 3 Telegram bots
     * mybot - for all first bot used for every default messages
     * lead_bot - for all lead related messages
     * notification_bot - for all notification related messages
     *
     */

    private function prepareMessage($message)
    {
        $message = str_replace(array('+'), '\+', $message);
        $message = str_replace(array('-'), '\-', $message);
        $message = str_replace(array('.'), '\.', $message);
        $message = str_replace(array('_'), '\_', $message);
        // $message = str_replace(array('*'), '\*', $message);
        $message = str_replace(array('`'), '\`', $message);
        $message = str_replace(array('['), '\[', $message);
        $message = str_replace(array(']'), '\]', $message);
        $message = str_replace(array('('), '\(', $message);
        $message = str_replace(array(')'), '\)', $message);
        $message = str_replace(array('~'), '\~', $message);
        $message = str_replace(array('>'), '\>', $message);
        $message = str_replace(array('#'), '\#', $message);
        $message = str_replace(array('='), '\=', $message);
        return $message;
    }
    public function sendTelegramMessage($message, $chatIds, $bot = 'notification_bot')
    {
        $telegram = Telegram::bot($bot);
        $telegram->setTimeOut(30);
        if (count($chatIds) > 0) {
            $message = $this->prepareMessage($message);
            foreach ($chatIds as $value) {
                if ($value != '') {
                    try {
                        $telegram->sendMessage([
                            // 'chat_id' => '-1001742014326',
                            'chat_id' => $value,
                            'text' => $message,
                            'parse_mode' => 'MarkdownV2',
                        ]);
                        // $messageId = $response->getMessageId();
                    } catch (\Exception $e) {
                        Log::info('Telegram error log: ' . $e->getMessage() . " chat_id is: " . $value . " message is: " . $message);
                        continue;
                    }
                }
            }
        }

    }
    public function sendTelegramMessageHtml($message, $chatIds, $url = null, $button_title = 'Update Lead', $bot = 'notification_bot',$disable_web_app = false)
    {
        if (count($chatIds) > 0) {

            $telegram = Telegram::bot($bot);
            $telegram->setTimeOut(30);
            foreach ($chatIds as $value) {
                if ($value != '') {
                    try {
                        if ($url) {
                            if (env('APP_ENV') === 'local' || $disable_web_app == true) {
                                $telegram->sendMessage([
                                    // 'chat_id' => '-1001742014326',
                                    'chat_id' => $value,
                                    'text' => $message,
                                    'parse_mode' => 'html',
                                    'reply_markup' => json_encode([
                                        'inline_keyboard' => [
                                            [
                                                [
                                                    'text' => $button_title,
                                                    'url' => $url,
                                                ],
                                            ],
                                        ],
                                    ]),
                                    'disable_web_page_preview' => true,
                                ]);
                            } else {
                                $telegram->sendMessage([
                                    // 'chat_id' => '-1001742014326',
                                    'chat_id' => $value,
                                    'text' => $message,
                                    'parse_mode' => 'html',
                                    'reply_markup' => json_encode([
                                        'inline_keyboard' => [
                                            [
                                                [
                                                    'text' => $button_title,
                                                    'web_app' => [
                                                        'url' => $url,
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ]),
                                    'disable_web_page_preview' => true,
                                ]);
                            }

                        } else {
                            $telegram->sendMessage([
                                // 'chat_id' => '-1001742014326',
                                'chat_id' => $value,
                                'text' => $message,
                                'parse_mode' => 'html',
                            ]);
                        }

                        // $messageId = $response->getMessageId();
                    } catch (\Exception $e) {
                        Log::info('Telegram error log: ' . $e->getMessage() . " chat_id is: " . $value . " message is: " . $message);
                        continue;
                    }
                }
            }
        }

    }

    public function sendTelegramMessageWithAttachment($message, $chatIds, $url, $bot = 'notification_bot')
    {
        if (count($chatIds) > 0) {
            $telegram = Telegram::bot($bot);
            $telegram->setTimeOut(30);
            foreach ($chatIds as $value) {
                if ($value != '') {
                    try {

                        if ($url) {

                            $photoPath = $url;
                            $photo = InputFile::create($photoPath, 'photo.jpg');
                            $telegram->sendPhoto([
                                'chat_id' => $value,
                                'text' => $message,
                                'photo' => $photo,
                                'caption' => $message,
                                'parse_mode' => 'html',
                            ]);
                        } else {

                            $telegram->sendMessage([
                                'chat_id' => $value,
                                'text' => $message,
                                'parse_mode' => 'html',
                            ]);
                        }

                    } catch (\Exception $e) {
                        Log::info('Telegram error log: ' . $e->getMessage() . " chat_id is: " . $value . " message is: " . $message);
                        continue;
                    }
                }
            }
        }

    }

    public function get()
    {

        $response = Telegram::getMe();

        $botId = $response->getId();
        $firstName = $response->getFirstName();
        $username = $response->getUsername();
        print_r($response);
    }

    public function sendApkDocument($chatId, $msg, $vedio = null){
        $telegram = Telegram::bot('notification_bot');
        // $apkPath = public_path('assets/application/AX-Secondary-25June.apk');
        // $apkDocument = InputFile::create($apkPath, 'AX-Secondary-25June.apk');

        $apkPath = public_path('assets/application/AX CHAT 1.0.apk');
        $apkDocument = InputFile::create($apkPath, 'AX CHAT 1.0.apk');

        
        try {
            $telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $msg,
                'parse_mode' => 'html',
            ]);
            
            if($vedio)
            {
                $videoPath = public_path('assets/application/videos/apk-8-apr-2025.mp4');
                $videoDocument = InputFile::create($videoPath, 'apk-8-apr-2025.mp4');
                $telegram->sendVideo([
                    'chat_id' => $chatId,
                    'video' => $videoDocument,
                ]);
            }
            
            $telegram->sendDocument([
                'chat_id' => $chatId,
                'document' => $apkDocument,
            ]);
            

            return true;

        }
        catch (\Exception $e) {
            Log::info('Telegram error log: ' . $e->getMessage() . " chat_id is: " . $chatId . " message is: " . $msg);
            return false;
        }
    }
    public function sendChat()
    {

        $response = Telegram::sendMessage([
            'chat_id' => '-1001742014326',
            'text' => '`New Lead`
            *name:* khagesh
            *email:* khagesh\.office@gmail\.com
            *mobile:* 1234567890
            *source:* Property finder
            *note:* test note',
            'parse_mode' => 'MarkdownV2',
        ]);
        $messageId = $response->getMessageId();

    }
    public function sendLeadNote($note)
    {

        $lead = Lead::with('leadSource', 'property', 'leadStatus', 'leadSubStatus')->find($lead_id);
        $chat_ids = [];
        $phone = str_replace(array('+'), '\+', $lead->phone);
        $mobile = str_replace(array('+'), '\+', $lead->mobile);
        $whatsapp = str_replace(array('+'), '\+', $lead->whatsapp);
        $email = str_replace(array('.'), '\.', $lead->email);
        $email = str_replace(array('-'), '\-', $email);
        $followupdate = str_replace(array('-'), '\-', $lead->follow_up_date);
        $lead_source = $lead->leadSource?->name ?? '';
        $lead_status = $lead->leadStatus?->name ?? '';
        $leadSubStatus = $lead->leadSubStatus?->name ?? '';
        $property_ref_no = $lead->property?->ref_no ?? '-';
        $property_ref_no = str_replace(array('-'), '\-', $property_ref_no);
        $message = '`New Leads`
            *Priority:* ' . $lead->priority . '
            *Name:* ' . $lead->first_name . ' ' . $lead->last_name . '
            *Email:* ' . $email . '
            *Mobile:* ' . $mobile . '
            *Whatsapp:* ' . $mobile . '
            *Phone:* ' . $phone . '
            *Follow up date:* ' . $followupdate . '
            *Status:* ' . $lead_status . '
            *Sub Status:* ' . $leadSubStatus . '
            *Source:* ' . $lead_source . '
            *Property:* ' . $property_ref_no;

        $this->sendTelegramMessage($message, $chat_ids);

    }

    /// Multi Bot functions start here
    /*
     * We have 3 Telegram bots
     * mybot - for all first bot used for every default messages
     * lead_bot - for all lead related messages
     * notification_bot - for all notification related messages
     *
     */
    public function sendMessageOnTelegram($bot, $message, $chatIds)
    {

        if (count($chatIds) > 0) {
            $telegram = Telegram::bot($bot);
            $telegram->setTimeOut(30);
            $message = $this->prepareMessage($message);
            foreach ($chatIds as $value) {
                if ($value != '') {
                    try {
                        $telegram->sendMessage([
                            // 'chat_id' => '-1001742014326',
                            'chat_id' => $value,
                            'text' => $message,
                            'parse_mode' => 'MarkdownV2',
                        ]);
                        //   $messageId = $response->getMessageId();
                    } catch (\Exception $e) {
                        \Log::info('Telegram error log: ' . $e->getMessage() . " chat_id is: " . $value . " message is: " . $message);
                        continue;
                    }
                }
            }
        }

    }

    public function getTelegramUsername($chatId)
    {
        try {
            $token = '814391914:AAEp7zf2d3v-Y5XbHoRHbstciGhU1hrfUFg';
            if (!$token) {
                throw new \Exception("Telegram token not found.");
            }
            $telegram = new Api($token);
            $response = $telegram->getChat(['chat_id' => $chatId]);
            return $response['username'] ?? null; // Returns null if username is not set
        } catch (\Exception $e) {
            info('Error retrieving Telegram username: ' . $e->getMessage());
            return null;
        }
    }
    protected function getTestDealTelegramToken()
    {
        
        if(env('APP_ENV') === 'local'){
            $token = '5881070543:AAG9sMXeKvn6J6tIdlK6qzjYZvx_cHaPEbc';

        }else{
            //$token = '5627019585:AAGjFs6GKLJ9RV2VzxzufZI_kf';
            $token = '5881070543:AAG9sMXeKvn6J6tIdlK6qzjYZvx_cHaPEbc';
        }
        return $token;
    }

    public function sendMessage(
        $chatId,
        $message,
        $url = null,
        $parseMode = 'html',
        $isWebApp = false
    ) {
        try {
            $token = $this->getTestDealTelegramToken();
            if (!$token) {
                throw new \Exception("Telegram token not found.");
            }

            $telegram = new Api($token);

            $params = [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => ucfirst($parseMode),
            ];
                
            $result = $telegram->sendMessage($params);
            return ['message' => $result];
        } catch (\Exception $e) {
            // Log the exception in Laravel
            Log::error('Error sending Telegram message: ' . $e->getMessage());
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }


    public function OldsendMessageIntoGroup(
        $chatId,
        $message,
        $url = null,
        $parseMode = 'html',
        $isWebApp = false
    ) {
        try {
            $token = $this->getTestDealTelegramToken();
            if (!$token) {
                throw new \Exception("Telegram token not found.");
            }
    
            $telegram = new Api($token);
    
            $params = [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => ucfirst($parseMode),
            ];
    
            if ($url) {
                // Send image with caption
                $response = Http::asForm()->post("https://api.telegram.org/bot$token/sendPhoto", [
                    'chat_id'    => $chatId,
                    'photo'      => $url,
                    'caption'    => $message,
                    'parse_mode' => 'HTML',
                ]);
    
                if ($response->successful()) {
                    return ['message' => 'Photo sent successfully', 'telegram_response' => $response->json()];
                } else {
                    Log::error('Telegram Send Failed', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);
                    return ['error' => true, 'message' => 'Failed to send photo', 'response' => $response->body()];
                }
            } else {
                // Send plain text message
                $result = $telegram->sendMessage($params);
                return ['message' => 'Text message sent', 'telegram_response' => $result];
            }
        } catch (\Exception $e) {
            Log::error('Error sending Telegram message: ' . $e->getMessage());
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }
    

    protected function getTelegramTokenByBotName($botName)
    {
        return config("telegram.bots.{$botName}.token");
    }

    public function sendMessageIntoGroup(
        $chatId,
        $message,
        $url = null,
        $parseMode = 'HTML',
        $isWebApp = false,
        $botName = 'notification_bot',
        $messageThreadId = null
    ) {
        try {
            // Get token based on bot name
            $token = $this->getTelegramTokenByBotName($botName);
            if (!$token) {
                throw new \Exception("Telegram token not found for bot: {$botName}");
            }

            $telegram = new Api($token);
            $parseMode = strtoupper($parseMode);

            if ($url) {
                // Send photo with caption
                $response = Http::asForm()->post("https://api.telegram.org/bot{$token}/sendPhoto", [
                    'chat_id'    => $chatId,
                    'photo'      => $url,
                    'caption'    => $message,
                    'parse_mode' => $parseMode,
                    'message_thread_id'  => $messageThreadId,
                ]);

                if ($response->successful()) {
                    
                    return ['message' => 'Photo sent', 'telegram_response' => $response->json()];
                } else {
                    Log::error('Telegram Photo Send Failed', [
                        'status' => $response->status(),
                        'body'   => $response->body(),
                    ]);

                    if (!$isWebApp) {
                        $fallback = $telegram->sendMessage([
                            'chat_id' => $chatId,
                            'text' => $message,
                            'parse_mode' => $parseMode,
                            'message_thread_id'  => $messageThreadId,
                        ]);
                        Log::info(['warning' => 'Photo failed, fallback text sent', 'telegram_response' => $fallback]);
                        return ['warning' => 'Photo failed, fallback text sent', 'telegram_response' => $fallback];
                    }

                    return ['error' => true, 'message' => 'Failed to send photo'];
                }
            }
            $payload = [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => $parseMode,
                'message_thread_id'  => $messageThreadId,
            ];
            // Send plain message
            $result = $telegram->sendMessage($payload);
            return ['message' => 'Text sent', 'telegram_response' => $result];

        } catch (\Exception $e) {
            Log::error("Telegram Send Error [{$botName}]: " . $e->getMessage());
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }
}
