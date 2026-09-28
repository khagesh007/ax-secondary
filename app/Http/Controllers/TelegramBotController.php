<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Keyboard\Keyboard;

class TelegramBotController extends Controller
{
    public function start()
    {
        $keyboard = Keyboard::make()
            ->inline()
            ->row([
                Keyboard::inlineButton(['text' => 'Leads', 'callback_data' => 'Leads']),
                Keyboard::inlineButton(['text' => 'Notifications', 'callback_data' => 'Notifications']),
            ]);

        $telegram = Telegram::bot("notification_bot");
        $response = $telegram->sendMessage([
            'chat_id' => request('message.chat.id'),
            'text' => 'Select a category:',
            'reply_markup' => $keyboard,
        ]);
    }

    public function button()
    {
        $category = request('callback_query.data');

        // Add logic to handle the selected category and send messages accordingly

        // Example: Send a message based on the selected category
        if ($category === 'Leads') {
            $message = 'Here are the latest lead-related messages:\n1. Lead message 1\n2. Lead message 2';
        } elseif ($category === 'Notifications') {
            $message = 'Here are the latest general notifications:\n1. Notification 1\n2. Notification 2';
        } else {
            $message = 'Invalid category';
        }
        $telegram = Telegram::bot("notification_bot");
        $response = $telegram->sendMessage([
            'chat_id' => request('callback_query.message.chat.id'),
            'text' => $message,
        ]);
    }
    public function webhook(Request $request)
    {
        $update = json_decode($request->getContent(), true);

        if (isset($update['message'])) {
            $message = $update['message'];
            $text = $message['text'];
            $chatId = $message['chat']['id'];

            if (strpos($text, '/leads') === 0) {
                // Handle leads-related message
                $this->handleLeadsMessage($chatId, $text);
            } elseif (strpos($text, '/notifications') === 0) {
                // Handle general notifications
                $this->handleNotificationsMessage($chatId, $text);
            } else {
                // Handle other messages
                $this->handleOtherMessage($chatId, $text);
            }
        }

        // Return a response to Telegram (200 OK)
        return response('OK', 200);
    }

    private function handleLeadsMessage($chatId, $messageText)
    {
        // Extract lead-related information from the message
        $leadInfo = substr($messageText, strlen('/leads'));

        // Process the lead information, e.g., save it to a database
        // You can use Laravel's Eloquent ORM for database operations
        // Example: Lead::create(['info' => $leadInfo]);

        // Respond to the user
        $responseText = "Received new lead: $leadInfo";
        $this->sendMessage($chatId, $responseText);
    }

    private function handleNotificationsMessage($chatId, $messageText)
    {
        // Extract notification information from the message
        $notification = substr($messageText, strlen('/notifications'));

        // Process the notification, e.g., send it to employees
        // You can use Laravel's notification mechanisms to send notifications
        // Example: \Notification::send($employee, new NewNotification($notification));

        // Respond to the user
        $responseText = "Notification sent: $notification";
        $this->sendMessage($chatId, $responseText);
    }

    private function handleOtherMessage($chatId, $messageText)
    {
        // Handle other types of messages as needed
        // For example, you can provide instructions or respond to unrecognized commands.
        $responseText = "Sorry, I didn't understand that command.";
        $this->sendMessage($chatId, $responseText);
    }

    private function sendMessage($chatId, $text)
    {
        // Use the Telegram bot SDK to send a message
        // Replace 'YOUR_BOT_TOKEN' with your actual bot token
        $telegram = new \Telegram\Bot\Api('6099474664:AAH8_bG-Ihx3zxKi1vgqAeASfoxFyZVMMlw');
        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => $text,
        ]);
    }
}
