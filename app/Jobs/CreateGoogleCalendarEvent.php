<?php

namespace App\Jobs;

use App\Models\Event;
use App\Models\User;
use App\Models\UserOAuthToken;
use App\Traits\GoogleCalendarEventTrait;
use App\Traits\OAuthTrait;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateGoogleCalendarEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, GoogleCalendarEventTrait, OAuthTrait;
    public $event;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $event = $this->event;
            $user = User::find($event->agent_id);
            $userOAuthToken = UserOAuthToken::where("user_id", $user->id)->where("type", "google")->first();
            if (!is_null($userOAuthToken)) {
                $event->load(["attendees:id,email"]);
                $attendees = $event->attendees->pluck("email")->map(function ($data) {
                    return ["email" => $data];
                })->all();
                if ($this->checkIfGoogleAccessTokenExipred($userOAuthToken->token)) {
                    $accessToken = $this->regenerateGoogleAccessToken($userOAuthToken->token);
                    $userOAuthToken->token = $accessToken;
                    $userOAuthToken->save();
                }
                $googleCalendarEventId = $this->createEvent($event, $user->email, $userOAuthToken->token, $attendees);
                // $googleCalendarEventId = $this->createEvent($event, env("GOOGLE_CALENDAR_ID"), $userOAuthToken->token, $attendees); // Use this for testing
                if (!is_null($googleCalendarEventId)) {
                    $event->google_calendar_event_id = $googleCalendarEventId;
                    $event->save();
                }
            }
            return true;
        } catch (Exception $exception) {
            info('Error: ' . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine());
            return false;
        }
    }
}
