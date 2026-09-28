<?php

namespace App\Events;

use App\Models\Property;
use App\Models\User;
use App\Models\UserFcm;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;


class PropertyCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $property;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Property $property)
    {
        $agent = User::find($property->agent_id);
        $user_id = $agent->id;
        $fcm = UserFcm::where('user_id', $user_id)->first();
        if ($fcm) {

            $deviceToken = $fcm->fcm_token;

            $title = 'New Property Added';
            $message = 'New Property Added with Ref# ' . $property->ref_no;
            $flag = 'New Property Added';
            $model = 'Property';
            $listing = $property;

            $data = [
                'title' => (string) $title,
                'message' => (string) $message,
                'type' => "Test",
                'user_id' => (string) $user_id,   // Convert to string if it's not already
                'flag' => (string) $flag,         // Convert to string if it's not already
                'model' => (string) $model,       // Convert to string if it's not already
                'model_id' => (string) $listing->ref_no,  // Convert to string if it's not already
            ];

            $notification = [
                'title' => $title,
                'body' => $message,
            ];

            $payload = [
                'message' => [
                    'token' => $deviceToken,  // FCM registration token
                    'notification' => $notification,  // Notification payload (title and body)
                    'data' => $data,  // Custom data payload
                ]
            ];


            $function = sendNotificationToMobile($payload, $flag = "Test");
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
