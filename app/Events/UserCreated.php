<?php

namespace App\Events;

use GuzzleHttp\Client;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use \App\Models\User;
use App\Models\TraccarRestrictedCoordinate;

// use Illuminate\Support\Facades\Log;

class UserCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $user;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {

        $this->user = $user;
        // $user->device_identifier = $user->id;
        // $user->port = 5055;
        // $user->traccar_status = 1;
        // $user->mic_status = 0;
        // $user->save();

        TraccarRestrictedCoordinate::create([
            'title' => '',
            'lat' => 0,
            'lng' => 0,
            'radius' => 0,
            'zoom' => 0,
            'status' => 1,
            'user_id' => $user->id,
            'created_by_id' => 1,
            'stay_time' => 0,
        ]);

        // $url = "https://telelog.axcrm.ae/api/create-user-from-secondary-webhook";
        // $url_ax_contact ="http://192.168.50.138:4000/api/user-created-webhook";

        $url = "https://telelog.axcrm.ae/backend/api/webhooks/users/user-created";
        $url_ax_contact ="https://contacts.axcrm.ae/api/user-created-webhook";
        $url_ax_chat ="https://chatapi.axcrm.ae/api/user-created-webhook";
        $url_deals = "https://deals.axcrm.ae/backend/api/webhook/secondary/user/sync";
        //$url_datahub = "https://datahub.axcrm.ae/backend/api/webhook/secondary/user/sync";


        if ($user->children->count() > 0) {
            $is_manager = 1;
        } else {
            $is_manager = 0;
        }
        $roles = $user->getRoleNames();
        $roles_v2 = [];
        foreach ($roles as $role) {
            $roles_v2[] = $role;
        }

        $data = [
            'name' => $user->first_name . ' ' . $user->last_name,
            'first_name' => $user->first_name,
            'id' => $user->id,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'secondary_email' => $user->secondary_email,
            'position' => $user->position,
            'password' => $user->password,
            'mobile' => $user->mobile,
            'thumbnail' => $user->thumbnail,
            'image' => $user->image,
            'avatar' => $user->avatar,
            'telegram_username' => $user->telegram_username,
            'telegram_chatid' => $user->telegram_chatid,
            'status' => $user->status,
            'entity_id' => 1,
            'parent_id' => $user->parent_id,

            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
            'updated_by_id' => $user->updated_by_id,
            'created_by_id' => $user->created_by_id,
            'deleted_at' => $user->deleted_at,
            'bitrix_id' => $user->bitrix_id,
            'bitrix_role' => $user->bitrix_role,
            'roles' => $user->getRoleNames()->implode(', '),
            'is_manager' => $is_manager,
            'roles_v2' => $roles_v2,
            'zone_id' => $user->zone_id,
            'telelog_entity' => $user->telelog_entity,

        ];

        try {
            $client = new Client();
            $response = $client->post($url, [
                'form_params' => $data,
            ]);


            $client_sec = new Client();
            $response = $client_sec->post($url_ax_contact, [
                'form_params' => $data,
            ]);
            $client_ax_chat = new Client();
            $response = $client->post($url_ax_chat, [
                'form_params' => $data,
            ]);


            $client_ax_deals = new Client();
            $response = $client_ax_deals->post($url_deals, [
                'headers' => [
                    'Authorization' => env('AXDEAL_SECRET_KEY'),
                ],
                'form_params' => $data,
            ]);


            // $client_datahub = new Client();
            // $response = $client_datahub->post($url_datahub, [
            //     'form_params' => $data,
            // ]);

            $statusCode = $response->getStatusCode();
            $responseBody = $response->getBody()->getContents();

            $userName = $user->full_name;
            $message = "New User Created:\n";
            $message .= "User Name: $userName\n";
            $message .= "Timestamp: " . now()->toDateTimeString() . "\n";
            $message .= "IP Address: " . request()->ip() . "\n";

            info($message);

        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $statusCode = $e->getResponse()->getStatusCode();
                $responseBody = $e->getResponse()->getBody()->getContents();

            }
        }
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
