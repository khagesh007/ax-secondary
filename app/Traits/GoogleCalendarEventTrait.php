<?php

namespace App\Traits;

use Carbon\Carbon;
use Exception;

trait GoogleCalendarEventTrait
{

    const OAUTH2_TOKEN_URI = 'https://accounts.google.com/o/oauth2/token';
    const CALENDAR_TIMEZONE_URI = 'https://www.googleapis.com/calendar/v3/users/me/settings/timezone';
    const CALENDAR_LIST = 'https://www.googleapis.com/calendar/v3/users/me/calendarList';
    const CALENDAR_EVENT = 'https://www.googleapis.com/calendar/v3/calendars/';

    const GOOGLE_OAUTH_SCOPE = "https://www.googleapis.com/auth/calendar";

    public function createEvent($event, $calendarId, $accessToken, $attendees = [], $timezone = "Asia/Dubai")
    {
        try {
            $apiURL = self::CALENDAR_EVENT . $calendarId . "/events";
            $curlPost = [
                "summary" => $event->title,
                "location" => $event->location,
                "description" => $event->description,
                "start" => [
                    "dateTime" => Carbon::parse($event->start_time)->format("Y-m-d\TH:i:sP"),
                    "timezone" => $timezone,
                ],
                "end" => [
                    "dateTime" => Carbon::parse($event->end_time)->format("Y-m-d\TH:i:sP"),
                    "timezone" => $timezone,
                ],
                "attendees" => $attendees,
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiURL);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $accessToken["access_token"], 'Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($curlPost));
            $data = json_decode(curl_exec($ch), true);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($http_code != 200) {
                return null;
            }

            return $data['id'];
        } catch (Exception $exception) {
            return null;
        }
    }

    public function updateEvent($event, $calendarId, $accessToken, $attendees = [], $timezone = "Asia/Dubai")
    {
        try {
            $apiURL = self::CALENDAR_EVENT . $calendarId . "/events/" . $event->google_calendar_event_id;

            $curlPost = [
                "summary" => $event->title,
                "location" => $event->location,
                "description" => $event->description,
                "start" => [
                    "dateTime" => Carbon::parse($event->start_time)->format("Y-m-d\TH:i:sP"),
                    "timezone" => $timezone,
                ],
                "end" => [
                    "dateTime" => Carbon::parse($event->end_time)->format("Y-m-d\TH:i:sP"),
                    "timezone" => $timezone,
                ],
                "attendees" => $attendees,
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiURL);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $accessToken["access_token"], 'Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($curlPost));
            $data = json_decode(curl_exec($ch), true);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($http_code != 200) {
                return null;
            }

            return $data['id'];
        } catch (Exception $exception) {
            return null;
        }
    }

    public function deleteEvent($event, $calendarId, $accessToken)
    {
        try {
            $apiURL = self::CALENDAR_EVENT . $calendarId . "/events/" . $event->google_calendar_event_id;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiURL);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $accessToken["access_token"], 'Content-Type: application/json'));
            $data = json_decode(curl_exec($ch), true);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($http_code != 204) {
                return null;
            }
            return true;
        } catch (Exception $exception) {
            return null;
        }
    }

    public function getEvents($calendarId, $accessToken)
    {
        try {
            $apiURL = self::CALENDAR_EVENT . $calendarId . "/events";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiURL);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $accessToken["access_token"], 'Content-Type: application/json'));
            $data = json_decode(curl_exec($ch), true);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($http_code != 200) {
                return null;
            }

            return $data['items'];
        } catch (Exception $exception) {
            return null;
        }
    }
    public function getAllEvents($calendarId, $accessToken)
    {
        $events = [];
        $nextPageToken = null;

        do {
            $apiURL = self::CALENDAR_EVENT . $calendarId . "/events";
            if ($nextPageToken) {
                $apiURL .= '?pageToken=' . $nextPageToken;
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiURL);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $accessToken["access_token"], 'Content-Type: application/json'));
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($http_code != 200) {
                return [];
            }

            $data = json_decode($response, true);
            $events = array_merge($events, $data['items']);
            $nextPageToken = isset($data['nextPageToken']) ? $data['nextPageToken'] : null;
        } while ($nextPageToken);

        return $events;
    }
}
