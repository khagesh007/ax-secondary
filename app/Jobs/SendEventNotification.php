<?php

namespace App\Jobs;

use App\Models\Event;
use App\Models\User;
use App\Models\UserFcm;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendEventNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $event;
    protected $action;

    /**
     * Create a new job instance.
     *
     * @param Event $event
     * @param string $action
     */
    public function __construct(Event $event, $action)
    {
        $this->event = $event;
        $this->action = $action;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // $agent = User::find($this->event->agent_id);
        $agent = User::find(172);

        if ($agent) {
            $user_id = $agent->id;
            $fcms = UserFcm::where('user_id', 172)->get();

            if ($fcms->count() > 0) {
                foreach ($fcms as $fcm) {
                    $deviceToken = $fcm->fcm_token;
                    $title = 'AX Secondary CRM';
                    $flag = '';
                    $message = '';

                    switch ($this->action) {
                        case 'created':
                            $message = "You have just created an event with ID: " . $this->event->id . ". Tap to view details.";
                            $flag = 'Event Created';
                            break;
                        case 'updated':
                            $message = "You have just updated an event with ID: " . $this->event->id . ". Tap to view details.";
                            $flag = 'Event Updated';
                            break;
                        case 'notification':
                            $message = "You have an upcoming event with ID: " . $this->event->id . ". Tap to view details.";
                            $flag = 'Event Notification';
                            break;
                        default:
                            $message = "You have an event with ID: " . $this->event->id . ". Tap to view details.";
                            $flag = 'Event';
                            break;
                    }

                    $data = [
                        'title' => (string) $title,
                        'message' => (string) $message,
                        'type' => "Test",
                        'user_id' => (string) $user_id,
                        'flag' => (string) $flag,
                        'model' => 'Event',
                        'model_id' => (string) $this->event->id,
                    ];

                    $notification = [
                        'title' => $title,
                        'body' => $message,
                    ];

                    $payload = [
                        'message' => [
                            'token' => $deviceToken,
                            'notification' => $notification,
                            'data' => $data,
                        ]
                    ];

                    // Call the function to send the notification
                    sendNotificationToMobile($payload, $flag);
                }
            }
        }
    }
}
