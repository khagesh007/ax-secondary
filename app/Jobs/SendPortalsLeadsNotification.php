<?php

namespace App\Jobs;

use App\Models\UserFcm;
use Throwable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;


class SendPortalsLeadsNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $lead;
    public $action;
    public $agent;
    public $manager;
    public $model;
    public $count;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($model, $lead, $action, $agent, $manager, $count = 0)
    {
        $this->lead = $lead;
        $this->action = $action;
        $this->agent = $agent;
        $this->manager = $manager;
        $this->model = $model;
        $this->count = $count;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $agent = $this->agent;

            $user_id = $agent->id;
            $fcms = UserFcm::where('user_id', 172)->get();

            if ($fcms->count() > 0) {
                foreach ($fcms as $fcm) {

                    $deviceToken = $fcm->fcm_token;
                    $title = 'AX Secondary CRM';
                    $message = '';
                    if ($this->action == 'received') {
                        if ($this->model == "Lead") {
                            $message = 'You just received a new lead from '. $this->lead->leadSource->name . '.';
                            $flag = 'Lead Assigned';
                        }
                    }

                    $flag = $this->model;
                    $model = $this->model;

                    $data = [
                        'title' => (string) $title,
                        'message' => (string) $message,
                        'type' => "Test",
                        'user_id' => (string) $user_id, // Convert to string if it's not already
                        'flag' => (string) $flag, // Convert to string if it's not already
                        'model' => (string) $model, // Convert to string if it's not already
                        'model_id' => (string) $this->lead->id, // Convert to string if it's not already
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

                    // Function that sends the notification to FCM
                    sendNotificationToMobile($payload, "Test");
                }
            }
        } catch (Throwable $th) {
            info($th);
            info('error');
            throw $th;
        }
    }
}
