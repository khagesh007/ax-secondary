<?php

namespace App\Events;

use App\Models\Event;
use App\Models\User;
use App\Models\UserFcm;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Jobs\SendEventNotification;

class EventCreatedMobile
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $property;
    public $action;
    public $event;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Event $event, $action)
    {
        $this->event = $event;
        $this->action = $action;

        // Dispatch the job to handle the notification logic
        // SendEventNotification::dispatch($event, $action);

        $agent = User::find($event->agent_id);

        $user_id = $agent->id;
        $fcms = UserFcm::where('user_id', $event->agent_id)->get();
        if ($fcms->count() > 0) {
            foreach ($fcms as $fcm) {
                $deviceToken = $fcm->fcm_token;
                $title = 'AX Secondary CRM';
                $message = 'You have a new Event';
                if ($action == 'created') {
                    $message = "You have just created an event with ID: " . $event->id . ". Tap to view details.";
                    $flag = 'Event Created';

                } elseif ($action == 'updated') {
                    $message = "You have just updated an event with ID: " . $event->id . ". Tap to view details.";
                    $flag = 'Event Updated';
                }
                elseif ($action == 'notification') {
                    $message = "You have an upcoming event with ID: " . $event->id . ". Tap to view details.";
                    $flag = 'Event Notification';
                }else{
                    $message = "You have an event with ID: " . $event->id . ". Tap to view details.";
                    $flag = 'Event';
                }


                $model = 'Event';
                $event = $event;

                $data = [
                    'title' => (string) $title,
                    'message' => (string) $message,
                    'type' => "Test",
                    'user_id' => (string) $user_id,   // Convert to string if it's not already
                    'flag' => (string) $flag,         // Convert to string if it's not already
                    'model' => (string) $model,       // Convert to string if it's not already
                    'model_id' => (string) $event->id,  // Convert to string if it's not already
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
