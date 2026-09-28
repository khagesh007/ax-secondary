<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\UserFcm;


class LeadAppNotification
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
    public function __construct($lead, $action, $agent, $manager,$count=0)
    {
        // dd($lead);
        $agent = User::find($lead->agent_id);

        $user_id = $agent->id;
        $fcm = UserFcm::where('user_id', 172)->first();
        if ($fcm) {

            $deviceToken = $fcm->fcm_token;
            $title = 'AX Secondary CRM';
            if ($action == 'assignedByManager') {
                $message = "A new Lead [" . $lead?->id . "] has been assigned to you by " . $manager?->full_name . ".";
                $flag = 'Lead Assigned';
            }  elseif ($action == 'transfer') {
                $message = $count . " Lead(s) have been transferred to you by " . $manager->first_name . ' ' . $manager->last_name . '.' ;
                $flag = 'Lead Assigned';
            }

            $flag = 'Lead';
            $model = 'Lead';


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
