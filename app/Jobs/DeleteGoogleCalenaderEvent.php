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

class DeleteGoogleCalenaderEvent implements ShouldQueue
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
                if ($this->checkIfGoogleAccessTokenExipred($userOAuthToken->token)) {
                    $accessToken = $this->regenerateGoogleAccessToken($userOAuthToken->token);
                    $userOAuthToken->token = $accessToken;
                    $userOAuthToken->save();
                }
                $this->deleteEvent($event, $user->email, $userOAuthToken->token);
                // $this->deleteEvent($event, env("GOOGLE_CALENDAR_ID"), $userOAuthToken->token); // Use this for testing
            }
            return true;
        } catch (Exception $exception) {
            info('Error: ' . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine());
            return false;
        }
    }
}
