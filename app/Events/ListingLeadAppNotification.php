<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ListingLeadAppNotification
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $lead;
    public $action;
    public $agent;
    public $manager;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($lead, $action, $agent, $manager)
    {
        $agent = User::find($lead->agent_id);

        $user_id = $agent->id;
        $fcms = UserFcm::where('user_id', $agent->id)->get();
        if ($fcms->count() > 0) {
            foreach ($fcms as $fcm) {

                $deviceToken = $fcm->fcm_token;
                $title = 'AX Secondary CRM';
                if ($action == 'assignedByManager') {
                    $message = "A new Listing Lead has been assigned to you by (" . $manager->full_name . ").";
                    $flag = 'Listing Lead Assigned';
                }

                $flag = 'ListingLead';
                $model = 'ListingLead';

                $data = [
                    'title' => (string) $title,
                    'message' => (string) $message,
                    'type' => "Test",
                    'user_id' => (string) $user_id,   // Convert to string if it's not already
                    'flag' => (string) $flag,         // Convert to string if it's not already
                    'model' => (string) $model,       // Convert to string if it's not already
                    'model_id' => (string) $lead->id,  // Convert to string if it's not already
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
