<?php

namespace App\Events;

use App\Models\NotificationEvent;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Traits\NotificationManager;
class ActionOnListingChanges implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels, NotificationManager;
    public $property;
    public $action;
    public $noti_title;
    public $noti_message;
    public $subscriber;

    public $notification;
    public $url;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($property, $action)
    {
        $this->property = $property;
        $this->action = $action;
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


            $notification_event = NotificationEvent::where('class_name', 'ActionOnListingChanges')->where('status',1)->first();
            if(!empty($notification_event)) {
                $process = $this->propertyChangesAction($this->property, $notification_event, $this->action);

                if (!empty($notification_event) && !empty($process)) {
                    $this->notification = $process['notification'];
                    $this->noti_title = $process['notification']['title'];
                    $this->noti_message = $process['notification']['message'];
                    $this->subscriber = $process['users'];
                    $medium = json_decode($notification_event->medium);
                    $this->property = '';
                    $this->action = '';
                    if (in_array('WEB', $medium)) {
                        return new Channel('web-notification');
                    }
                }
            }


    }
}
