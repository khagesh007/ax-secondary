<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Property;
use App\Models\User;
use App\Models\UserFcm;

class PropertyUpdated
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
        $fcm = UserFcm::where('user_id', 172)->first();
        if ($fcm) {

            $deviceToken = $fcm->fcm_token;
            // $deviceToken ="ekYlGBJFT16ivrsQ7j1YNt:APA91bH-E1-v5H0hcB9Xo0fv6Aq7CFYjw9eiT-pqa9w8610w1MZqb_5JoZZo66ycEbu9B8KGMfIfEJ1zfJcdJJatZfeMIq7K788-Fu1pj6rmULjltwJeSF-iIGaf017l4-aXlRLmQtI6";
            // $deviceToken ="eZUr2lT-SxqVsGcOKoBvAi:APA91bF9U1u3u-tnixOnRF-QZffhkZ3uSc-NezNG6pS8uX3zN8Q0Kd6LRlMuHCifr48ZZ81WxqRfBYwf-WaMG4m1JlYrnocxrqukinA9AeonbqVma71wxEBLZs534L_S9Rr0Khc61syC";
            $title = 'Property Updated';
            $message = 'Your property with Reference # '.$property->ref_no.' has been successfully updated.';
            $flag = 'Property Updated';
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
