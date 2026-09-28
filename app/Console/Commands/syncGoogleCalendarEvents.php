<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\User;
use App\Models\UserOAuthToken;
use App\Traits\GoogleCalendarEventTrait;
use App\Traits\OAuthTrait;
use Carbon\Carbon;
use Google_Client;
use Google_Service_Calendar;
use Illuminate\Console\Command;

class syncGoogleCalendarEvents extends Command
{
    use GoogleCalendarEventTrait;
    use OAuthTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'google:syncEvents';

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
    public function __handle()
    {
        $users = User::whereHas('googleauthtoken', function ($query) {
            $query->where('type', 'google');
        })->get();
        foreach ($users as $user) {
            $userOAuthToken = UserOAuthToken::where("user_id", $user->id)->where("type", "google")->first();
            if (!is_null($userOAuthToken)) {
                // $event->load(["attendees:id,email"]);
                // $attendees = $event->attendees->pluck("email")->map(function ($data) {
                //     return ["email" => $data];
                // })->all();

                if ($this->checkIfGoogleAccessTokenExipred($userOAuthToken->token)) {
                    $accessToken = $this->regenerateGoogleAccessToken($userOAuthToken->token);
                    $userOAuthToken->token = $accessToken;
                    $userOAuthToken->save();
                }
                // $googleCalendarEventId = $this->createEvent($event, $user->email, $userOAuthToken->token, $attendees);
                // $googleCalendarEventId = $this->createEvent($event, env("GOOGLE_CALENDAR_ID"), $userOAuthToken->token, $attendees); // Use this for testing
                // if (!is_null($googleCalendarEventId)) {
                //     $event->google_calendar_event_id = $googleCalendarEventId;
                //     $event->save();
                // }
            }

            $accessToken = $user->googleauthtoken->token;
            $calendarId = $user->email;
            $events = $this->getEvents($calendarId, $accessToken);
            // foreach ($events as $event) {
            //     $event = Event::where('google_event_id', $event['id'])->first();
            //     if (!$event) {
            //         $event = new Event();
            //     }
            //     $event->title = $event['summary'];
            //     $event->location = $event['location'];
            //     $event->description = $event['description'];
            //     $event->start_time = Carbon::parse($event['start']['dateTime']);
            //     $event->end_time = Carbon::parse($event['end']['dateTime']);
            //     $event->google_event_id = $event['id'];
            //     $event->save();
            // }
            info($events);

        }

    }

    public function handle()
    {
        $event = $this->getAllEvents();

        dd($event);
        $users = User::whereHas('googleauthtoken', function ($query) {
            $query->where('type', 'google');
        })->get();

        foreach ($users as $user) {
            $userOAuthToken = UserOAuthToken::where("user_id", $user->id)->where("type", "google")->first();

            if (!is_null($userOAuthToken)) {
                // if ($this->checkIfGoogleAccessTokenExpired($userOAuthToken->token)) {
                //     $accessToken = $this->regenerateGoogleAccessToken($userOAuthToken->token);
                //     $userOAuthToken->token = $accessToken;
                //     $userOAuthToken->save();
                // }
                try {
                    $accessToken = $user->googleauthtoken->token;
                    $calendarId = $user->email;
                    $events = $this->getEvents($calendarId, $accessToken);
                    info($events);
                    foreach ($events as $event) {
                        $this->saveEvent($event, $user->id);
                    }
                } catch (\Throwable $th) {
                    continue;
                    throw $th;
                }

            }
        }
    }

    public function saveEvent($eventData, $userId)
    {
        // Save the event into your system here
        $event = Event::where('google_event_id', $eventData['id'])->first();

        if (!$event) {
            $event = new Event();
        }

        $event->user_id = $userId;
        $event->title = $eventData['summary'];
        $event->location = isset($eventData['location']) ? $eventData['location'] : null;
        $event->description = isset($eventData['description']) ? $eventData['description'] : null;
        $event->start_time = Carbon::parse($eventData['start']['dateTime']);
        $event->end_time = Carbon::parse($eventData['end']['dateTime']);
        $event->google_event_id = $eventData['id'];
        $event->save();
    }

    public function getAllEvents()
    {

        // Initialize Google client
        $client = new Google_Client();
        $client->setApplicationName('Your Application Name');
        $client->setScopes(Google_Service_Calendar::CALENDAR_EVENTS);

        $userAccessTokens = UserOAuthToken::where('type', 'google')->pluck('token')->all();
        $allEvents = [];

        foreach ($userAccessTokens as $accessToken) {
            try {
                $accessToken = $accessToken["access_token"];
                $client->setAccessToken($accessToken);

                if ($client->isAccessTokenExpired()) {
                    continue;
                }
                $service = new Google_Service_Calendar($client);
                $calendarId = 'primary';

                $events = $service->events->listEvents($calendarId);
                $items = $events->getItems();
                $allEvents = array_merge($allEvents, $items);
                dd($allEvents);
            } catch (\Exception $e) {
                continue;
                error_log('Error fetching events: ' . $e->getMessage());
            }
        }

        return response()->json($allEvents);
    }

}
