<?php

namespace App\Http\Controllers\Admin\Event;

use App\Events\EventCreated;
use App\Events\EventUpdated;
use App\Http\Controllers\Admin\Contact\FollowUpController;
use App\Http\Controllers\Admin\Lead\FollowUpController as LeadFollowUpController;
use App\Http\Controllers\Admin\Listing\PropertyController;
use App\Http\Controllers\Controller;
use App\Jobs\CreateGoogleCalendarEvent;
use App\Jobs\DeleteGoogleCalenaderEvent;
use App\Jobs\RegenerateGoogleOAuthRefreshToken;
use App\Jobs\UpdateGoogleCalenaderEvent;
use App\Models\Contact;
use App\Models\ContactNote;
use App\Models\Event;
use App\Models\EventType;
use App\Models\Lead;
use App\Models\LeadNote;
use App\Models\LeadStatus;
use App\Models\Property;
use App\Models\PropertyNote;
use App\Models\User;
use App\Models\UserOAuthToken;
use App\Traits\GoogleCalendarEventTrait;
use App\Traits\GoogleCalendarTrait;
use App\Traits\OAuthTrait;
use Carbon\Carbon;
use Exception;
use Google\Client;
use Google\Service\Calendar as ServiceCalendar;
use Google\Service\Calendar\Calendar;
use Google\Service\Calendar\Event as CalendarEvent;
use Google_Client;
use Google_Service;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Spatie\GoogleCalendar\Event as GoogleCalendarEvent;

use Laravel\Socialite\Facades\Socialite;


class TestEventController extends Controller
{
    use GoogleCalendarEventTrait, OAuthTrait;

    const OAUTH2_TOKEN_URI = 'https://accounts.google.com/o/oauth2/token';
    const CALENDAR_TIMEZONE_URI = 'https://www.googleapis.com/calendar/v3/users/me/settings/timezone';
    const CALENDAR_LIST = 'https://www.googleapis.com/calendar/v3/users/me/calendarList';
    const CALENDAR_EVENT = 'https://www.googleapis.com/calendar/v3/calendars/';

    const GOOGLE_OAUTH_SCOPE = "https://www.googleapis.com/auth/calendar";




    public function test()
    {

        $userOAuthTokens = UserOAuthToken::get();



        // $users = User::get();

        User::chunk(200, function (Collection $users) {
            foreach ($users as $user) {
                $user->password = Hash::make("password");
            $user->save();
            }
        });


        // foreach($users ?? [] as $user){
        //     $user->password = Hash::make("password");
        //     $user->save();
        // }

        // var_dump($users);die;

    }



    public function social()
    {
        return Socialite::driver('google')
            ->setScopes([ServiceCalendar::CALENDAR, ServiceCalendar::CALENDAR_EVENTS])
            ->with(["access_type" => "offline", "prompt" => "consent select_account"])
            ->redirect();
    }

    public function redirect(Request $request)
    {

        // RegenerateGoogleOAuthRefreshToken::dispatch();
        die;
        $event = Event::find(749);

        // CreateGoogleCalenaderEvent::dispatch($event);
        // die;

        // DeleteGoogleCalenaderEvent::dispatch($event);
        // die;

        // if (!is_null($event->google_calendar_event_id)) {
        //     // $user = User::find($event->agent_id);

        //     $user = User::find(173);
        //     $userOAuthToken = UserOAuthToken::where("user_id", $user->id)->where("type", "google")->first();
        //     if (!is_null($userOAuthToken)) {

        //         $event->load(["attendees:id,email"]);
        //         $attendees = $event->attendees->pluck("email")->map(function ($data) {
        //             return ["email" => $data];
        //         })->all();
        //         if ($this->checkIfGoogleAccessTokenExipred($userOAuthToken->token)) {
        //             $accessToken = $this->regenerateGoogleAccessToken($userOAuthToken->token);
        //             $userOAuthToken->token = $accessToken;
        //             $userOAuthToken->save();
        //         }
        //         $attendees = [];
        //         // $googleCalendarEventId = $this->updateEvent($event, $user->email, $userOAuthToken->token, $attendees);
        //         $googleCalendarEventId = $this->updateEvent($event, env("GOOGLE_CALENDAR_ID"), $userOAuthToken->token, $attendees); // Use this for testing
        //         if (!is_null($googleCalendarEventId)) {
        //             $event->google_calendar_event_id = $googleCalendarEventId;
        //             $event->save();
        //         }
        //     }
        // }
        // die;

        dispatch(new UpdateGoogleCalenaderEvent($event));die;

        UpdateGoogleCalenaderEvent::dispatch($event);
        die;
        $event = Event::find(722);

        $event->load(["attendees:id,email"]);
        $attendees = $event->attendees->pluck("email")->map(function($data){
            return ["email" => $data];
        })->all();
        print_r($attendees);die;

        $userOAuthToken = UserOAuthToken::where("user_id", auth()->id())->first();

        echo "<pre>";
        // print_r($this->regenerateGoogleAccessToken($userOAuthToken->token));die;
        $at = [
            "access_token" => "ya29.a0AfB_byBTkD1JJ4bR4_hhsT6rjsFC-DF2H38qXVkSDwd0ocwi25QSHs0OY9Df3VSXrs56T2f19Hl4akVymzD3-H37YZNwrJ46gScv9GgHbBet5z4qdSWUfPlQQRmFhcszSOqOJh3oCdYfT7Vi2aMW7hcmvIIVfpC-bzyLaCgYKAfkSARASFQGOcNnCvgWYQJsFyzPfLf937wmrNw0171",
            "expires_in" => 3599,
            "refresh_token" => "1//0gk2hiR9YPqFECgYIARAAGBASNwF-L9IrAqklmZGgouME7O_wJDxElRptghc1xFLB04Vpvarr2xO0EhB_KPPK0tmxRKQqb3jgl5w",
            "scope" => "https://www.googleapis.com/auth/calendar.events https://www.googleapis.com/auth/calendar",
            "token_type" => "Bearer"
        ];

        $at =  [
            "access_token" => "ya29.a0AfB_byDCUexMiRiVzzovin3TM4RXWYk6q6RWCrGs3I0_rh8bX9wzKk7xWx20IHPjvE0vJq_NVYnB59BQPwBWTm78M8mDs1lObyTMsvdBPz5NpamjrKONxtmqxnggXFUklz4MxdTlRWF1NtNltp_AVzuWGNR2ZU8nnx-MaCgYKAVESARASFQGOcNnCg_k_fm8W6WNrgMRMIU6RxA0171",
            "expires_in" => 3599,
            "refresh_token" => "1//0g-Px5SzgAVpxCgYIARAAGBASNwF-L9IrLLc6908aZXUVwu5Gdff3YKBxCbQRsGMoRj4qcXovomYdbubDe3OUsDJ3wFUTqHEdoM8",
            "scope" => "https://www.googleapis.com/auth/calendar.events https://www.googleapis.com/auth/calendar",
            "token_type" => "Bearer",
            "created" => 1696064931
         ];
        $at =  [
            "access_token" => "ya29.a0AfB_byCPUuJQZXeFOP90MWu0nGbJIZysH5QiK8GkOuSn7uNoMBK6e63qtc1p-MYJy1CFqZFy0CVAgWbO3KqSvqMJ_gJYsRtqjFUgoy8a2D1_QwrAk7A9GZztpmLx1pG3CvrBTWkp2oqVHyBBTygoxltlcwtNX2aalJIfaCgYKARwSARASFQGOcNnCWdt2y9_0UJ3s6BNfqkFVNQ0171",
            "expires_in" => 3599,
            "refresh_token" => "1//0gyq4TAm1Nu0sCgYIARAAGBASNwF-L9Irz3fTduBV_FNTydR2KkHlZ6dgAJFtzpjPMZefqj-CbXDj8W9I2F3gqvO5NTcuELw-4S0",
            "scope" => "https://www.googleapis.com/auth/calendar https://www.googleapis.com/auth/calendar.events",
            "token_type" => "Bearer",
            "created" => 1696063136
         ];
        var_dump($this->checkIfGoogleAccessTokenExipred($at));die;


        $cleint = new Client();

        $cleint->setClientId(env("GOOGLE_CLIENT_ID"));
        $cleint->setClientSecret(env("GOOGLE_CLIENT_SECRET"));
        $cleint->setAccessToken($userOAuthToken->token);
        // $cleint->setAccessToken("ya29.a0AfB_byCPUuJQZXeFOP90MWu0nGbJIZysH5QiK8GkOuSn7uNoMBK6e63qtc1p-MYJy1CFqZFy0CVAgWbO3KqSvqMJ_gJYsRtqjFUgoy8a2D1_QwrAk7A9GZztpmLx1pG3CvrBTWkp2oqVHyBBTygoxltlcwtNX2aalJIfaCgYKARwSARASFQGOcNnCWdt2y9_0UJ3s6BNfqkFVNQ0171");

        var_dump($cleint->isAccessTokenExpired());die;

        // var_dump($cleint->refreshToken($userOAuthToken->token["refresh_token"]));die;

        // $test = $this->createEvent($event, "homver30@gmail.com", $userOAuthToken->token);
        // // $test = $this->createEvent($event, "homver30@gmail.com", ["access_token" => "ya29.a0AfB_byAj6HyWFGT9kx4BpVTZESPwlX8zK5sKppIUHIBDdSrxjWs_EQCcMBHUxWfILecQjrRbxVOIZGxxxy1eSj1yqZIf-_6ajlIiziO37oucsIsT-zJZb_eDfjJGR1K7wG9EwZtFpNnuK5ZrWPpM3S29qXmm7cWt5BXakwaCgYKAV0SARASFQGOcNnCH8p3MCGlbo049oAqP0158Q0173"]);
        // // $test = $this->createEvent($event, "homver30@gmail.com", ["access_token" => "ya29.a0AfB_byAOEUyVO046z5A9H2QJETz6ee6UnCgMXSK2iQ2YcchpxBa7Q2PxVmVnhzyxee2phCZnc6Zbjge25oUbYHTiZZM21XRnI11tt-2Smj7iHLS96elN_XZM-Sx0bM4Kz28SifCeOj2Niq3y5FC_pNipkbeTRHGvDAnOIgaCgYKAVQSARASFQGOcNnCesaDjAvHg3Pkusnhx_8_0Q0173"]);
        // var_dump($test);die;


        // echo "<pre>";
        // print_r($this->regenerateAccessToken($userOAuthToken->token));

        // // $data =
        // print_r($userOAuthToken->toArray());die;


        $test = $this->deleteEvent($event, env("GOOGLE_CALENDAR_ID"), $userOAuthToken->token);
        // $test = $this->updateEvent($event, env("GOOGLE_CALENDAR_ID"), $userOAuthToken->token);


        dd($test);
    }


    public function GetUserCalendarTimezone($access_token)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::CALENDAR_TIMEZONE_URI);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $access_token));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $data = json_decode(curl_exec($ch), true);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($http_code != 200) {
            $error_msg = 'Failed to fetch timezone';
            if (curl_errno($ch)) {
                $error_msg = curl_error($ch);
            }
            throw new Exception('Error ' . $http_code . ': ' . $error_msg);
        }

        return $data['value'];
    }

    public function GetCalendarsList($access_token)
    {
        $url_parameters = array();

        $url_parameters['fields'] = 'items(id,summary,timeZone)';
        $url_parameters['minAccessRole'] = 'owner';

        $url_calendars = self::CALENDAR_LIST . '?' . http_build_query($url_parameters);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url_calendars);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $access_token));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $data = json_decode(curl_exec($ch), true);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($http_code != 200) {
            $error_msg = 'Failed to get calendars list';
            if (curl_errno($ch)) {
                $error_msg = curl_error($ch);
            }
            throw new Exception('Error ' . $http_code . ': ' . $error_msg);
        }

        return $data['items'];
    }



    private function getTimezoneOffset($timezone = 'America/Los_Angeles')
    {
        $current   = timezone_open($timezone);
        $utcTime  = new \DateTime('now', new \DateTimeZone('UTC'));
        $offsetInSecs =  timezone_offset_get($current, $utcTime);
        $hoursAndSec = gmdate('H:i', abs($offsetInSecs));
        return stripos($offsetInSecs, '-') === false ? "+{$hoursAndSec}" : "-{$hoursAndSec}";
    }
}
