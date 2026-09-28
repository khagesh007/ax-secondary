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

class PropertyApproval
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $property;
    public $action;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Property $property, $action)
    {
        $agent = User::find($property->agent_id);

        $user_id = $agent->id;
        $fcms = UserFcm::where('user_id', $property->agent_id)->get();
        if ($fcms->count() > 0) {
            foreach ($fcms as $fcm) {
                $deviceToken = $fcm->fcm_token;
                $message = '';
                $title = 'AX Secondary CRM';
                $flag = 'Property Approved';

                if ($action == 'approved') {
                    $message = "Your changes to Property Reference Number " . $property->ref_no . " have been Approved ✅.";
                    $flag = 'Property Approved';

                } elseif ($action == 'refused') {
                    $message = "Oops! Your changes to Property Reference Number " . $property->ref_no . " have been refused 🚫. Kindly review the note provided below for further details";
                    $flag = 'Property Refused';
                } elseif ($action == 'refused') {
                    $message = "Your property reference number" . $property->ref_no . " has been unpublished.";
                    $flag = 'Property Refused';
                } else {
                    $message = "Your property reference number" . $property->ref_no . " has been published.";
                }


                $model = 'Property';
                $listing = $property;

                $data = [
                    'title' => (string) $title,
                    'message' => (string) $message,
                    'type' => "Test",
                    'user_id' => (string) $user_id, // Convert to string if it's not already
                    'flag' => (string) $flag, // Convert to string if it's not already
                    'model' => (string) $model, // Convert to string if it's not already
                    'model_id' => (string) $listing->ref_no, // Convert to string if it's not already
                ];

                $notification = [
                    'title' => $title,
                    'body' => $message,
                ];

                $payload = [
                    'message' => [
                        'token' => $deviceToken, // FCM registration token
                        'notification' => $notification, // Notification payload (title and body)
                        'data' => $data, // Custom data payload
                    ],
                ];
                // info($user_id.' Sending Payload', $payload);
                $function = sendNotificationToMobile($payload, $flag = "Test");
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
