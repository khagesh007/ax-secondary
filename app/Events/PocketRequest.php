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

class PocketRequest implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels, NotificationManager;
    public $property;
    public $user;
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
    public function __construct($property)
    {
        $this->property = $property;
        $this->user = '';
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

        // try {
        //     $notification_event = NotificationEvent::where('class_name', 'PocketRequest')->where('status', true)->first();
        //     $process = $this->pocketRequestedProcess($this->property, $notification_event);
        //     if(!empty($notification_event) && !empty($process)) {
        //         $this->notification = $process['notification'];
        //         $this->noti_title = $process['notification']['title'];
        //         $this->noti_message = $process['notification']['message'];
        //         $this->subscriber = $process['users'];
        //         $medium = json_decode($notification_event->medium);
        //         $this->property = '';
        //         $this->user = '';
        //         if (in_array('WEB', $medium)) {
        //             return new Channel('web-notification');
        //         }
        //     }

        // } catch (\Exception $e) {
        //     \Log::info('Error Import INDEX no.   Data: '.$e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
        //     return new PrivateChannel('channel-name');
        // }
        // return new PrivateChannel('channel-name');
    }
}
