<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait FirebaseTrait
{
    protected $serverKey;
    public function __construct()
    {
        $this->serverKey = env('FIREBASE_SERVER_KEY') ?? 'AAAA_OxfTQE:APA91bE22wzYji8aEerbkVtrYsbtJFoxM-spY1PTFWw1sf0jo0utgl06sJsvayFuyNo772o1TYjiLz44XcRr1JrrUJ9yvmsDVHqmBgVFa_B_l4uPXpG_tnUIk_BBUuYMjHAGSyvPfxvs';
    }

    public function pushToUser($user_id, $title, $body, $data)
    {

        try {
            $user = User::find($user_id);
            $notification = [
                'title' => $title,
                'body' => $body,
            ];
            $data['token'] = $user->device_token;
            $payload = [
                'notification' => $notification,
                'data' => $data,
                'to' => $user->device_token,
                'priority' => 'high',

            ];
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $this->serverKey,
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', $payload);

            return $response->json();
        } catch (\Throwable $th) {
            return $this->sendResponse(false, $th, null, 400);
            return $th->getMessage();
        }

    }
    public function pushToUserToken($device_token, $title, $body, $data)
    {

        try {

            $notification = [
                'title' => $title,
                'body' => $body,
            ];
            $data['token'] = $device_token;
            $payload = [
                'notification' => $notification,
                'data' => $data,
                'to' => $device_token,
                'priority' => 'high',

            ];
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $this->serverKey,
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', $payload);

            return $response->json();
        } catch (\Throwable $th) {
            return $this->sendResponse(false, $th, null, 400);
            return $th->getMessage();
        }

    }
    public function silentNotification($device_token, $data)
    {
        try {
            $data['token'] = $device_token;
            $payload = [
                'data' => $data,
                'to' => $device_token,
                'priority' => 'high',

            ];
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $this->serverKey,
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', $payload);

            return $response->json();
        } catch (\Throwable $th) {
            return $this->sendResponse(false, $th, null, 400);
            return $th->getMessage();
        }
    }

    public function traccarGloablPush($data)
    {
        $users = User::where('traccar_status', 1)->whereNotNull('device_token')->select('id', 'device_token')->get();
        $count = 1;
        foreach ($users as $user) {
            $data['token'] = $user->device_token;
            if (env('APP_ENV') == 'production') {
                $this->silentNotification($user->device_token, $data);
                $count++;
            }
        }
        Log::channel('mobileApi')->info('Traccar client setting updated successfully of ' . $count . ' users');
    }

}
