<?php

namespace App\Listeners;

use App\Events\EventCreated;
use App\Events\ShootJobStatusChange;
use App\Mail\ShootJobActionMail;
use App\Models\Event;
use App\Models\NotificationEvent;
use App\Models\PropertyShootBooking;
use App\Models\User;
use App\Traits\NotificationManager;
use App\Traits\PusherTrait;
use App\Traits\TelegramTrait;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class ShootJobStatusChangeListener
{
    use NotificationManager, TelegramTrait, PusherTrait;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    private function generateTelegramMsg($shoot_request, $statusMsg, $header)
    {
        $assigned_to = $shoot_request->assigned_to_id != null ? User::where('id', $shoot_request->assigned_to_id)->first() : null;
        $message = '<b>'.$header.'</b>';
        $message .= '


'.$statusMsg.'

<b>Requested time and date:</b>  ' . date('d/m/Y h:i A',strtotime($shoot_request->created_at)).'
<b>Preferred time and date:</b>  ' . date('d/m/Y h:i A',strtotime($shoot_request->preferred_time)).'
<b>Completed time and date:</b> ' . date('d/m/Y h:i A',strtotime($shoot_request->updated_at)).'
<b>Photographer:</b> ' . ($assigned_to != null ? $assigned_to->full_name : 'N/A') . '
<b>Sale or Rent:</b>'.$shoot_request?->property?->for.'
<b>Reference No. from CRM: </b>'.$shoot_request?->property?->ref_no.'
<b>Location/Community: </b>'.($shoot_request?->property?->communityProperty?->path_name ?? $shoot_request?->property?->subCommunity?->path_name ?? $shoot_request?->property?->community?->path_name).'
<b>Property Name:</b> '.$shoot_request?->property?->title.'
<b>No. of Bedrooms:</b> '.$shoot_request?->property?->bed?->name.'
<b>No. of Bathrooms:</b> '.$shoot_request?->property?->bath?->name.'
<b>Price:</b> '.$shoot_request?->property?->price?->price.'
<b>Unit Number:</b> '.$shoot_request?->property?->unit_no.'
<b>Access Instructions:</b> '.$shoot_request->access_information.'
<b>Is the DEWA on/off?</b> '.($shoot_request->dewa == 1 ? 'On' : 'Off').'
<b>Furnished or Unfurnished:</b> '.($shoot_request->furnished == 1 ? 'Furnished' : 'Unfurnished').'
<b>Clean/Uncleaned:</b> '.($shoot_request->cleaned == 1 ? 'Cleaned' : 'Uncleaned').'
<b>Title:</b> ' . $shoot_request->title . '
<b>Description:</b> ' . $shoot_request->description;
        if($shoot_request->status == 'Approved'){
            $message .= '

Kindly refer to the Media Shoot Guide for more information:
Photo & Video Shoot Guide: Link

Please remember to share the Owner\'s Guide with the owner before the shoot:
Owner\'s Guide for Video & Photo Shooting - Luxury: Link

If you have any questions, please feel free to reach out.

Thank you!';
        }else {
            $message .= '


AX Secondary CRM';
        }
        return $message;
    }

    public function handle(ShootJobStatusChange $event)
    {

        $notification_event = NotificationEvent::where('class_name', 'ShootJobStatusChange')->where('status', true)->first();
        if(!empty($notification_event)) {
            $shoot_request = PropertyShootBooking::where('id', $event->event->id)->with('requestedBy','property','approvedBy','rejectedBy','assignedTo')->first();
            $process = $this->shootJobStatusChangeProcess($shoot_request, $notification_event);

            $calendar = Event::where('id', $event->event->id)->with('type','contact','lead','property','attendees')->first();
            if (!empty($notification_event) && !empty($process)) {
                $arr['notification'] = $process['notification'];
                $arr['noti_title'] = $process['notification']['title'];
                $arr['noti_message'] = $process['notification']['message'];
                $arr['subscriber'] = $process['users'];
                $arr['url'] = $process['url'];
                $type = $shoot_request->is_luxury == 1 ? 'Luxury' : 'Standard';
                if($shoot_request->status == 'Completed'){
                    $statusMsg = ucfirst($type) .' property shoot has been completed. Please refer to the following details:';
                    $header = 'Your '.$type.' Shoot is Completed!';
                }else{
                    $statusMsg = ucfirst($type) .' property Shoot status has been updated:';
                    $header = 'Your '.$type.' Shoot is '.$shoot_request->status.'!';
                }


                $medium = json_decode($notification_event->medium);
                if (in_array('WEB', $medium)) {
                    $this->pusherSingleChannel('web-notification', EventCreated::class, $arr);
                }
                $noti_recipients = User::whereIn('id', $process['users'])->select('id', 'name', 'email','telegram_chatid')->get();
                if (in_array('EMAIL', $medium)) {
                    foreach ($noti_recipients as $mailUser) {
                        Mail::to($mailUser->email)->send(new \App\Mail\ShootJobStatusChange($shoot_request,  $mailUser, $statusMsg, $header));
                    }
                }
                if (in_array('TELEGRAM', $medium)) {
                    $message = $this->generateTelegramMsg($shoot_request, $statusMsg, $header);
                    foreach ($noti_recipients as $usr) {
                        if ($usr->telegram_chatid != null) {
                            $this->sendTelegramMessageHtml($message, [$usr->telegram_chatid]);
                        }
                    }
                }

            }
        }
    }
}
