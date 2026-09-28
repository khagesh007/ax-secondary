<?php

namespace App\Events;

use App\Models\UserFcm;
use App\Models\Source;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Jobs\SendPortalsLeadsNotification;

class PortalsLeadsNotification
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $lead;
    public $action;
    public $agent;
    public $manager;
    public $model;
    public $count;
    // event(new PortalsLeadsNotification("Lead", $leadAgent, 'received', $user, $assignment_by, $counter));

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($model, $lead, $action, $agent, $manager, $count = 0)
    {

        // try {
        //     SendPortalsLeadsNotification::dispatch($model, $lead, $action, $agent, $manager, $count);
        // } catch (\Throwable $th) {
        //     info($th);
        //     throw $th;
        // }

        try {
            $agent = $agent;

            $user_id = $agent->id;
            $fcms = UserFcm::where('user_id', $user_id)->get();

            if ($fcms->count() > 0) {
                foreach ($fcms as $fcm) {

                    $deviceToken = $fcm->fcm_token;
                    $title = 'AX Secondary CRM';
                    $message = 'You have a new Lead';
                    if ($action == 'received') {
                        if ($model == "Lead") {
                            // $source = $lead->leadSource->name;
                            // $source = Source::find($this->lead->source_id);
                            $message = 'You just received a new lead from Portal' . '.';
                            // info($message);
                            // $message = 'You just received a new lead from '. $this->lead?->leadSource?->name . '.';
                            $flag = 'Lead Assigned';

                        }
                    }

                    $flag = $model;
                    $model = $model;

                    $data = [
                        'title' => (string) $title,
                        'message' => (string) $message,
                        'type' => "Test",
                        'user_id' => (string) $user_id, // Convert to string if it's not already
                        'flag' => (string) $flag, // Convert to string if it's not already
                        'model' => (string) $model, // Convert to string if it's not already
                        'model_id' => (string) $lead->id, // Convert to string if it's not already
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

                    $function = sendNotificationToMobile($payload, $flag = "Test");
                }
            }
        } catch (\Throwable $th) {

            throw $th;
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
