<?php

namespace App\Events;

use App\Models\User;
use App\Models\UserFcm;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeadTransferAppNotification
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $lead;
    public $action;
    public $agent;
    public $manager;
    public $model;
    public $count;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($model, $lead, $action, $agent, $manager, $count = 0)
    {
        try {
        $agent =$agent;

        $user_id = $agent->id;
        $fcms = UserFcm::where('user_id', $agent->id)->get();


        if ($fcms->count() > 0) {
            foreach ($fcms as $fcm) {

                $deviceToken = $fcm->fcm_token;
                $title = 'AX Secondary CRM';
                if ($action == 'transfer') {
                    if ($model == "Lead") {
                        $message = $count . " Lead(s) have been transferred to you by " . $manager->first_name . ' ' . $manager->last_name . '.';
                        $flag = 'Lead Assigned';
                    } elseif ($model == "ListingLead") {
                        $message = $count . " Listing Lead(s) have been transferred to you by " . $manager->first_name . ' ' . $manager->last_name . '.';
                        $flag = 'Listing Lead Assigned';
                    }
                } elseif ($action == 'telesalesTransfer') {
                    if ($model == "Lead") {
                        $message = $count . " Lead(s) have been transferred to you by " . $manager->first_name . ' ' . $manager->last_name . '.';
                        $flag = 'Lead Assigned';
                    }
                } elseif ($action == 'selfTransfer') {
                    if ($model == "Lead") {
                        $message = $count . " Lead(s) have been transferred to you by " . $manager->first_name . ' ' . $manager->last_name . '.';
                        $flag = 'Lead Assigned';
                    }
                    if ($model == "ListingLead") {
                        $message = $count . " Listing Lead(s) have been transferred to you by " . $manager->first_name . ' ' . $manager->last_name . '.';
                        $flag = 'Lead Assigned';
                    }
                }

                $flag = $model;
                $model = $model;

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
        } catch (\Throwable $th) {
            info($th);info('error');
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
