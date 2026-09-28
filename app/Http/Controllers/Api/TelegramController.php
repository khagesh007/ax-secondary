<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Note;
use App\Models\Otp;
use App\Models\User;
use Telegram\Bot\Laravel\Facades\Telegram;
use Illuminate\Support\Facades\Log;
use App\Traits\TelegramTrait;
class TelegramController extends Controller
{
    use TelegramTrait;

    public function get(Request $request)
    {
    
        $response = Telegram::getMe();

        $botId = $response->getId();
        $firstName = $response->getFirstName();
        $username = $response->getUsername();
        print_r($response);
    }
    public function getUpdates(Request $request)
    {
    
        $response = Telegram::getUpdates();
echo '<pre>';
       
        foreach ($response as $value) {
            if($value?->message?->chat->username =='khageshoo7'){
               $chat_id =  $value?->message?->chat->id;  

              print_r($value?->message);
           
            }
          
        }
        echo '</pre>';
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
        print_r($messageId);
    }
    public function verifyTelegram($user_id, $telegram_user, $otp)
    {

        $response = Telegram::getUpdates();
        $status = false;
        foreach ($response as $value) {
            if ($value?->message?->chat->username == $telegram_user) {
               if($value?->message->text == $otp){
                $chat_id =  $value?->message?->chat->id;
                $user = User::find($user_id);
                $user->telegram_chatid = $chat_id;
                $user->save();
                $status = true;
                    Otp::where('user_id', $user_id)->where('used', 0)->where('for', 'telegram')->update(['used' => '1']);
               }
            }
        }
       return $status;
    }
    public function handleWebhook(Request $request)
    {
        Log::channel('bitrix_alert')->info("handleWebhook call");
        try{
            $update = Telegram::getWebhookUpdate();
            if ($update->isType('callback_query')) {
                $callback = $update->getCallbackQuery();
                $user = $callback->getFrom();
                $chatId = $callback->getMessage()->getChat()->getId();
                $data = $callback->getData();
            
                // Log who clicked and what
                Log::channel('bitrix_alert')->info("User: {$user->getFirstName()} ({$user->getUsername()}) clicked: $data");
            
                // Remove Telegram's loading spinner
                Telegram::answerCallbackQuery([
                    'callback_query_id' => $callback->getId(),
                    'text' => "Opening the link for you...",
                ]);
            
                // Now send the link as a message
                if ($data === 'open_link') {
                    Telegram::sendMessage([
                        'chat_id' => $chatId,
                        'text' => "👉 [Click here to open the page](https://example.com)",
                        'parse_mode' => 'Markdown',
                    ]);
                }
            }
        
        } catch (\Exception $exception) {

            Log::channel('bitrix_alert')->error('Error while fetching webhook', [
                'error' => $exception->getMessage(),
                'line' => $exception->getLine(),
                'file' => $exception->getFile()
            ]);
        }
    }
    public function sendTelegramButtonsToGroup()
    {
        try{
            $telegramMessageText = "Matching Listings\n";
            $this->sendMessage('-1002267346260',$telegramMessageText, 'HTML','Matching Lisiting', 'https://secondary.axcrm.ae/', true);

        }catch(\Exception $exception) {
            Log::error('Error while sending telegram buttons to group', [
                'error' => $exception->getMessage(),
                'line' => $exception->getLine(),
                'file' => $exception->getFile()
            ]);
        }
    }
}
