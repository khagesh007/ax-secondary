<?php

namespace App\Events;

use App\Models\NotificationEvent;
use App\Models\NotificationEventSubscriber;
use App\Models\Property;
use App\Models\User;
use App\Traits\NotificationManager;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ListingNoteCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels, NotificationManager;
    public $listing_note;
    public $noti_title;
    public $noti_message;
    public $subscriber;
    public $user;
    public $notification;
    public $url;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($listing_note)
    {
        $this->listing_note = $listing_note;
        $this->url = '';
        $this->noti_title = '';
        $this->noti_message = '';
        $this->notification = '';
        $this->subscriber = '';
        $this->user = auth()->user();
    }


    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {

        $notificationEvent = NotificationEvent::where('class_name','ListingNoteCreated')->where('status',1)->first() ?? [];

        if(!empty($notificationEvent)) {
            $process = $this->listingNoteCreatedEventProcess($this->listing_note, $notificationEvent);
            if (!empty($notificationEvent) && !empty($process)) {
                $this->notification = $process['notification'];
                $this->noti_title   = $process['notification']['title'];
                $this->noti_message = $process['notification']['message'];
                $this->subscriber = $process['users'];
                $medium = json_decode($notificationEvent->medium);
                $this->user = '';
                if (in_array('WEB', $medium)) {
                    return new Channel('web-notification');
                }
            }

        }else{
            //info("ListingNoteCreated event notification not fired ".json_encode($notificationEvent));
        }
    }
}
