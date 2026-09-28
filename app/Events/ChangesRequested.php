<?php

namespace App\Events;

use App\Models\NotificationEvent;
use App\Traits\NotificationManager;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChangesRequested implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels, NotificationManager;

    public $property;
    public $noti_title;
    public $noti_message;
    public $subscriber;

    public $notification;
    public $url;
    public function __construct($property)
    {
        $this->property = $property;
        $this->url = '';
        $this->noti_title = '';
        $this->noti_message = '';
        $this->notification = '';
        $this->subscriber = '';

    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        try {

            $notification_event = NotificationEvent::where('class_name', 'ChangesRequested')->where('status',1)->first();
            if(!empty($notification_event)) {
                $process = $this->changesRequestedProcess($this->property, $notification_event);
                if (!empty($notification_event) && !empty($process)) {
                    $this->notification = $process['notification'];
                    $this->noti_title = $process['notification']['title'];
                    $this->noti_message = $process['notification']['message'];
                    $this->subscriber = $process['users'];
                    $medium = json_decode($notification_event->medium);
                    $this->property = '';
                    $this->user = '';
                   // info('notificaiton users'.json_encode($this->subscriber));
                    if (in_array('WEB', $medium)) {
                        return new Channel('web-notification');
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::info('Data: '.$e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
            return false;
        }

    }
}
