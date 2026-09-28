<?php

namespace App\Listeners;

use App\Events\UserCreated;
use GuzzleHttp\Client;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class UserCreatedListener implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\UserCreated  $event
     * @return void
     */
    public function handle(UserCreated $event)
    {

        info('Event w');
        $user = $event->user;
        $url = "https://telelog.axcrm.ae/backend/api/create-user-from-secondary-webhook";
        $client = new Client();
        $data = [
            'user' => $user,
        ];
        try {
            $response = $client->post($url, [
                'form_params' => $data,
            ]);

            $statusCode = $response->getStatusCode();
            $responseBody = $response->getBody()->getContents();

            $userName = $user->full_name; // Replace this with the actual user's name
            $message = "New User Created:\n";
            $message .= "User Name: $userName\n";
            $message .= "Timestamp: " . now()->toDateTimeString() . "\n";
            $message .= "IP Address: " . request()->ip() . "\n";

            Log::channel('discord')->error($message);
            info($message);
        } catch (RequestException $e) {
            info($e);
            if ($e->hasResponse()) {
                $statusCode = $e->getResponse()->getStatusCode();
                $responseBody = $e->getResponse()->getBody()->getContents();
            } else {
                info('-----');
            }
        }

    }
}
