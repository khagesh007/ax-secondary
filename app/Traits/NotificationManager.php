<?php

namespace App\Traits;

use App\Http\Controllers\Api\V1\Deals\DealApiController;
use App\Models\CommunityProperty;
use App\Models\DealProgressionData;
use App\Models\Notification;
use App\Models\NotificationEventSubscriber;
use App\Models\NotificationUser;
use App\Models\Property;
use App\Models\PropertyShootBooking;
use App\Models\SubCommunity;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Models\PropertyPrice;
use DB;
use Illuminate\Support\Facades\Auth;

trait NotificationManager
{
    private function createNotificationUser($notificationSubscriber, $notification)
    {
        if(!empty($notificationSubscriber)) {
            foreach ($notificationSubscriber as $user) {
                if($user != null && $user != '' && $user != 0 && $user != '0') {
                    $notification_user = new NotificationUser();
                    $notification_user->user_id = $user;
                    $notification_user->notification_id = $notification->id;
                    $notification_user->save();
                }
            }
        }
    }

    public function leadCreatedProcess($lead, $notification_event)
    {
        try{
        $ref_no = $lead?->property?->ref_no ?? null;

        $agent_id = (int)$lead?->agent_id;
        $roles = json_decode($notification_event?->roles) ?? [];

        $notification = new Notification();
        $notification->title = 'New lead created';
        $notification->message = $ref_no != null ? 'New lead created for property ' . $ref_no . '. Kindly review it.' : 'New lead created #' . $lead?->id;
        $notificationSubscriber = [];
        if($notification_event){
            $notificationSubscriber = NotificationEventSubscriber::where('notification_event_id', $notification_event->id)->pluck('user_id')->toArray();
            $notification->notification_event_id = $notification_event->id;
        }

        $notification->url = route('admin.work.leads.index') . '?lead=' . $lead->id;
        $notification->save();
        if (count($roles) > 0) {
            $users_by_role = User::whereHas(
                'roles', function ($q) use ($roles) {
                $q->whereIn('id', $roles);
            }
            )->pluck('id')->toArray();
            $notificationSubscriber = array_merge($users_by_role, $notificationSubscriber);
        }
        $notificationSubscriber = array_merge([$agent_id], $notificationSubscriber);
        $notificationSubscriber = array_unique($notificationSubscriber);
        $excluded = json_decode($notification_event?->excluded) ?? [];
        $notificationSubscriber =  array_match_and_remove($notificationSubscriber,$excluded);
        $this->createNotificationUser($notificationSubscriber, $notification);
        return ['url' => $notification->url, 'users' => $notificationSubscriber, 'notification' => $notification];
        } catch (\Exception $e) {
            \Log::error('Error Import INDEX no.   Data: '.$e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
            return [];
        }
    }

    public function eventCreatedProcess($event, $notification_event)
    {

     //   try{

        $agent_id = $event->agent_id;
        $roles = $notification_event?->roles != null ? json_decode($notification_event?->roles) : [];
        $notificationSubscriber = NotificationEventSubscriber::where('notification_event_id', $notification_event->id)->pluck('user_id')->toArray() ?? [];
        $event_attendees = $event->attendees()->pluck('user_id')->toArray();
        $notification = new Notification();
        $notification->title = 'New calendar event created';
        $notification->message = 'New calendar event titled ' . $event->title . ' has been created. Please have a look.';
        $notification->notification_event_id = $notification_event->id;
        $notification->url = route('admin.events.index') . '?event=' . $event->id;
        $notification->save();

        if(is_array($roles) && count($roles) > 0) {
            $users_by_role = User::whereHas(
                'roles', function ($q) use ($roles) {
                $q->whereIn('id', $roles);
            }
            )->pluck('id')->toArray() ?? [];
            $notificationSubscriber = array_merge($users_by_role, $notificationSubscriber);
        }


        $notificationSubscriber = array_merge([$agent_id], $notificationSubscriber, $event_attendees);
        $notificationSubscriber = array_unique($notificationSubscriber);
        $excluded = $notification_event?->excluded != null ? json_decode($notification_event?->excluded) : [];
        $notificationSubscriber =  array_match_and_remove($notificationSubscriber,$excluded);
        $this->createNotificationUser($notificationSubscriber, $notification);
        return ['url' => $notification->url, 'users' => $notificationSubscriber, 'notification' => $notification];
//        } catch (\Exception $e) {
//            \Log::error($e->getMessage());
//            return [];
//        }
    }
    public function eventReminderProcess($event, $notification_event)
    {

        try{
        $agent_id = $event->agent_id;
        $roles = json_decode($notification_event?->roles) ?? [];
        $notificationSubscriber = NotificationEventSubscriber::where('notification_event_id', $notification_event->id)->pluck('user_id')->toArray();
        $event_attendees = $event->attendees()->pluck('user_id')->toArray();
        $notification = new Notification();
        $notification->title = 'Event Reminder';
        $notification->message = 'Just a friendly reminder about the upcoming calendar event titled "'.$event->title.'." It\'s set to begin '.$event->diff_text.'.';
        $notification->notification_event_id = $notification_event->id;
        $notification->url = route('admin.events.index') . '?event=' . $event->id;
        $notification->save();
        if (count($roles) > 0) {
            $users_by_role = User::whereHas(
                'roles', function ($q) use ($roles) {
                $q->whereIn('id', $roles);
            }
            )->where('status','1')->pluck('id')->toArray();
            $notificationSubscriber = array_merge($users_by_role, $notificationSubscriber);
        }

        $notificationSubscriber = array_merge([$agent_id], $notificationSubscriber, $event_attendees);
        $notificationSubscriber = array_unique($notificationSubscriber);
        $excluded = json_decode($notification_event?->excluded) ?? [];
        $notificationSubscriber =  array_match_and_remove($notificationSubscriber,$excluded);
        $this->createNotificationUser($notificationSubscriber, $notification);
        $notificationSubscriber = User::whereIn('id',$notificationSubscriber)->where('status','1')->pluck('id')->toArray();
        return ['url' => $notification->url, 'users' => $notificationSubscriber, 'notification' => $notification];
        } catch (\Exception $e) {
            \Log::error('Error Import INDEX no.   Data: '.$e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
            return [];
        }
    }

    public function publishRequestedProcess($property, $notification_event)
    {

        try {
            if($notification_event?->id){
                $agent_id = (int)$property->agent_id;
                $roles = json_decode($notification_event?->roles)??[];
                $notificationSubscriber = NotificationEventSubscriber::where('notification_event_id', $notification_event->id)->pluck('user_id')->toArray();
                $notification = new Notification();
                $notification->title = 'Listing publish request';
                $notification->message = $property->ref_no != null ? auth()->user()?->name .' has submitted a publishing request for the property Ref #' . $property->ref_no . '. Kindly review it.' : 'Listing publish request';
                $notification->notification_event_id = $notification_event->id;
                $notification->url = route('admin.listing.index') . '?ref_number=' . $property->ref_no;
                $notification->save();
                if (count($roles) > 0) {
                    $users_by_role = User::whereHas(
                        'roles', function ($q) use ($roles) {
                        $q->whereIn('id', $roles);
                    }
                    )->pluck('id')->toArray();
                    $notificationSubscriber = array_merge($users_by_role, $notificationSubscriber);
                }
                $notificationSubscriber = array_merge([$agent_id, auth()->user()?->id], $notificationSubscriber);
                $notificationSubscriber = array_unique($notificationSubscriber);
                $notificationSubscriber = array_values($notificationSubscriber);
                $excluded = json_decode($notification_event?->excluded) ?? [];
                $notificationSubscriber =  array_match_and_remove($notificationSubscriber,$excluded);
                $this->createNotificationUser($notificationSubscriber, $notification);


                return ['url' => $notification->url, 'users' => $notificationSubscriber, 'notification' => $notification];
            }
        } catch (\Exception $e) {
            \Log::error('Error Import INDEX no.   Data: '.$e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
            return [];
        }
    }
    public function unpublishRequestProcess($property, $notification_event)
    {

        try {

            $agent_id = (int)$property->agent_id;
            $roles = json_decode($notification_event?->roles) ?? [];
            $notificationSubscriber = NotificationEventSubscriber::where('notification_event_id', $notification_event->id)->pluck('user_id')->toArray() ?? [];
            $notification = new Notification();
            $notification->title = 'Listing unpublish request';
            $notification->message = $property->ref_no != null ? auth()?->user()?->name .' has submitted an unpublish request for the property Ref #' . $property->ref_no . '. Kindly review it.' : 'New unpublish request submitted';
            $notification->notification_event_id = $notification_event->id;
            $notification->url = route('admin.listing.index') . '?ref_number=' . $property->ref_no;
            $notification->save();
            if (count($roles) > 0) {
                $users_by_role = User::whereHas(
                    'roles', function ($q) use ($roles) {
                    $q->whereIn('id', $roles);
                }
                )->pluck('id')->toArray();
                $notificationSubscriber = array_merge($users_by_role, $notificationSubscriber);
            }
            $notificationSubscriber = array_merge([$agent_id, auth()?->user()?->id], $notificationSubscriber);
            $notificationSubscriber = array_unique($notificationSubscriber);
            $notificationSubscriber = array_values($notificationSubscriber);
            $excluded = json_decode($notification_event?->excluded) ?? [];
            $notificationSubscriber =  array_match_and_remove($notificationSubscriber,$excluded);
            $this->createNotificationUser($notificationSubscriber, $notification);
            return ['url' => $notification->url, 'users' => $notificationSubscriber, 'notification' => $notification];
        } catch (\Exception $e) {
            info($property);
            info($notification_event);
            \Log::error('Error Import INDEX no.   Data: '.$e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
            return [];
        }
    }

    public function pocketRequestedProcess($property, $notification_event)
    {

        try {

            $agent_id = (int)$property->agent_id;
            $roles = json_decode($notification_event?->roles) ?? [];
            $notificationSubscriber = NotificationEventSubscriber::where('notification_event_id', $notification_event->id)->pluck('user_id')->toArray();
            $notification = new Notification();
            $notification->title = 'New pocket request submitted';
            $notification->message = $property->ref_no != null ? auth()?->user()?->name . ' has submitted a publish as Pocket request for the property Ref # ' . $property->ref_no . '. Kindly review it.' : 'New Pocket request submitted';
            $notification->notification_event_id = $notification_event->id;
            $notification->url = route('admin.listing.index') . '?ref_number=' . $property->ref_no;
            $notification->save();
            if (count($roles) > 0) {
                $users_by_role = User::whereHas(
                    'roles', function ($q) use ($roles) {
                    $q->whereIn('id', $roles);
                }
                )->pluck('id')->toArray();
                $notificationSubscriber = array_merge($users_by_role, $notificationSubscriber);
            }
            $notificationSubscriber = array_merge([$agent_id, auth()?->user()?->id], $notificationSubscriber);
            $notificationSubscriber = array_unique($notificationSubscriber);
            $notificationSubscriber = array_values($notificationSubscriber);
            $excluded = json_decode($notification_event?->excluded) ?? [];
            $notificationSubscriber =  array_match_and_remove($notificationSubscriber,$excluded);
            $this->createNotificationUser($notificationSubscriber, $notification);
            return ['url' => $notification->url, 'users' => $notificationSubscriber, 'notification' => $notification];
        } catch (\Exception $e) {
            \Log::error('Error Import INDEX no.   Data: '.$e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
            return [];
        }
    }
    public function propertyChangesAction($property, $notification_event, $action)
    {

        try {

            $agent_id = (int)$property->agent_id;
            $roles = json_decode($notification_event->roles) ?? [];
            $notificationSubscriber = NotificationEventSubscriber::where('notification_event_id', $notification_event->id)->pluck('user_id')->toArray();
            $notification = new Notification();
            $notification->title = 'Listing changes '.$action.'.';
            $notification->message = $property->ref_no != null ? auth()?->user()?->name.' has '.$action.' changes on property #' . $property->ref_no  : 'Listing changes '.$action.'.';
            $notification->notification_event_id = $notification_event->id;
            $notification->url = route('admin.listing.index') . '?ref_number=' . $property->ref_no;
            $notification->save();
            if (count($roles) > 0) {
                $users_by_role = User::whereHas(
                    'roles', function ($q) use ($roles) {
                    $q->whereIn('id', $roles);
                }
                )->pluck('id')->toArray();
                $notificationSubscriber = array_merge($users_by_role, $notificationSubscriber);
            }
            $notificationSubscriber = array_merge([$agent_id, auth()?->user()?->id], $notificationSubscriber);
            $notificationSubscriber = array_unique($notificationSubscriber);
            $notificationSubscriber = array_values($notificationSubscriber);
            $excluded = json_decode($notification_event?->excluded) ?? [];
            $notificationSubscriber =  array_match_and_remove($notificationSubscriber,$excluded);
            $this->createNotificationUser($notificationSubscriber, $notification);
            return ['url' => $notification->url, 'users' => $notificationSubscriber, 'notification' => $notification];
        } catch (\Exception $e) {
            \Log::error('Error Import INDEX no.   Data: '.$e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
            return [];
        }
    }
    public function changesRequestedProcess($property, $notification_event)
    {

        try {

            //$agent_id = (int)$property->agent_id;
            $roles = json_decode($notification_event->roles) ?? [];
            $notificationSubscriber = NotificationEventSubscriber::where('notification_event_id', $notification_event->id)->pluck('user_id')->toArray();
            $notification = new Notification();
            $notification->title = 'Listing changes requested.';
            $notification->message = $property?->ref_no != null ? auth()?->user()?->first_name . ' has made some changes on the property Ref # ' . $property?->ref_no . '. Kindly review it.' : 'Some changes made on a property';
            $notification->notification_event_id = $notification_event->id;
            $notification->url = route('admin.listing.index') . '?ref_number=' . $property->ref_no;
            $notification->save();

            if (count($roles) > 0) {
                $users_by_role = User::whereHas(
                    'roles', function ($q) use ($roles) {
                    $q->whereIn('id', $roles);
                }
                )->pluck('id')->toArray();
                $notificationSubscriber = array_merge($users_by_role, $notificationSubscriber);
            }
            if(Auth::check()){
                $notificationSubscriber = array_merge([auth()?->user()?->id], $notificationSubscriber);
            }
            $notificationSubscriber = array_unique($notificationSubscriber);
            $notificationSubscriber = array_values($notificationSubscriber);
            $excluded = json_decode($notification_event?->excluded) ?? [];
            $notificationSubscriber =  array_match_and_remove($notificationSubscriber,$excluded);
            $this->createNotificationUser($notificationSubscriber, $notification);
            return ['url' => $notification->url, 'users' => $notificationSubscriber, 'notification' => $notification];
        } catch (\Exception $e) {
            \Log::error('Data: '.$e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
            return [];
        }
    }
    public function contractExpiringProcess($property, $days, $notification_event)
    {

        try {

            $agent_id = (int)$property->agent_id;
            $roles = json_decode($notification_event->roles) ?? [];
            $notificationSubscriber = NotificationEventSubscriber::where('notification_event_id', $notification_event->id)->pluck('user_id')->toArray();
            $notification = new Notification();
            if($days == '1'){
                 $notification->title = 'Your Listing will be taken offline today!!';
                $notification->message = $property->ref_no != null ? 'The contract of your listing with reference # ' . $property->ref_no . ' is expiring Today. Please take necessary action to prevent your listing from going offline.' : 'Contract is expiring today for one of the your listing';

            }else{
                $notification->title = 'The contract of your listing with reference # ' . $property->ref_no . ' is expiring in ' . $days . ' days';
                $notification->message = $property->ref_no != null ? 'The contract of your listing with reference # ' . $property->ref_no . ' is about to expire in '.$days.' day. Please take necessary action to prevent your listing from going offline.' : 'Contract is expiring in '.$days.' days for one of the your property';
            }


            $notification->notification_event_id = $notification_event->id;
            $notification->url = route('admin.listing.index') . '?ref_number=' . $property->ref_no;
            $notification->save();
            if (count($roles) > 0) {
                $users_by_role = User::whereHas(
                    'roles', function ($q) use ($roles) {
                    $q->whereIn('id', $roles);
                }
                )->pluck('id')->toArray();
                $notificationSubscriber = array_merge($users_by_role, $notificationSubscriber);
            }
            $notificationSubscriber = array_merge([$agent_id], $notificationSubscriber);

            $notificationSubscriber = array_unique($notificationSubscriber);
            $notificationSubscriber = array_values($notificationSubscriber);
            $excluded = json_decode($notification_event?->excluded) ?? [];
            $notificationSubscriber =  array_match_and_remove($notificationSubscriber,$excluded);
            $this->createNotificationUser($notificationSubscriber, $notification);
            $agent = User::where('id',$agent_id)->select('id','name','first_name','email','telegram_chatid')->first();
            $notificationSubscriber = User::whereIn('id',$notificationSubscriber)->where('region_id','!=','2')->pluck('id')->toArray();
            return ['url' => $notification->url, 'users' => $notificationSubscriber, 'notification' => $notification,'agent'=>$agent];
        } catch (\Exception $e) {
            \Log::error('Error Import INDEX no.   Data: '.$e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
            return [];
        }
    }
    public function exclusiveExpiringProcess($property, $days, $notification_event)
    {

        try {

            $agent_id = (int)$property->agent_id;
            $roles = json_decode($notification_event->roles) ?? [];
            $notificationSubscriber = NotificationEventSubscriber::where('notification_event_id', $notification_event->id)->pluck('user_id')->toArray();
            $notification = new Notification();
            $notification->title = 'Property exclusive status expiring in ' . $days . ' days.';
            $notification->message = $property->ref_no != null ? 'Exclusive status is expiring in '.$days.' days for property Ref # ' . $property->ref_no . '. Kindly review it.' : 'Exclusive status is expiring in for one of the your property';
            $notification->notification_event_id = $notification_event->id;
            $notification->url = route('admin.listing.index') . '?ref_number=' . $property->ref_no;
            $notification->save();
            if (count($roles) > 0) {
                $users_by_role = User::whereHas(
                    'roles', function ($q) use ($roles) {
                    $q->whereIn('id', $roles);
                }
                )->pluck('id')->toArray();
                $notificationSubscriber = array_merge($users_by_role, $notificationSubscriber);
            }
            $notificationSubscriber = array_merge([$agent_id], $notificationSubscriber);
            $notificationSubscriber = array_unique($notificationSubscriber);
            $notificationSubscriber = array_values($notificationSubscriber);
            $excluded = json_decode($notification_event?->excluded) ?? [];
            $notificationSubscriber =  array_match_and_remove($notificationSubscriber,$excluded);
            $this->createNotificationUser($notificationSubscriber, $notification);
            $agent = User::where('id',$agent_id)->select('id','name','email','telegram_chatid')->first();
            return ['url' => $notification->url, 'users' => $notificationSubscriber, 'notification' => $notification,'agent'=>$agent];
        } catch (\Exception $e) {
            \Log::error('Data: '.$e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
            return [];
        }
    }
    public function listingNoteCreatedEventProcess($listing_note, $notificationEvent)
    {
        try{
        $roles = json_decode($notificationEvent->roles) ?? [];
        $property = Property::where('id', $listing_note->property_id)->select('agent_id','ref_no')->first();

        $agent = User::where('id', $property->agent_id)->first();
        $admins = User::whereHas("roles", fn($q) => $q->whereIn("name", ["Super Admin","Admin"]))->pluck('id')->toArray();
        if($this->listing_note->created_by_id == $property->agent_id){
            $users = $admins;
        }else{
            $users = [$agent->id];
        }
        if(!empty($notificationEvent)) {
            $subscribers = NotificationEventSubscriber::where('id', $notificationEvent->id)->pluck('user_id')->toArray();
            $this->subscriber = array_merge($subscribers, $users);

        }
        $notificationSubscriber = array_values($users);
        if (count($roles) > 0) {
            $users_by_role = User::whereHas(
                'roles', function ($q) use ($roles) {
                $q->whereIn('id', $roles);
            }
            )->pluck('id')->toArray();
            $notificationSubscriber = array_merge($users_by_role, $notificationSubscriber);
        }

        $notification = new Notification();
        $notification->title = 'Listing note added.';
        $notification->message = $property->ref_no != null ? auth()?->user()?->name . ' has added a new note on the property Ref # ' . $property->ref_no  : 'New note added on a property';
        $notification->notification_event_id = $notificationEvent->id;
        $notification->url = route('admin.listing.index') . '?ref_number=' . $property->ref_no;
        $notification->save();
        $notificationSubscriber = array_merge([$agent->id], $notificationSubscriber);

        $notificationSubscriber = array_unique($notificationSubscriber);

        $excluded = json_decode($notificationEvent?->excluded) ?? [];
        $notificationSubscriber =  array_match_and_remove($notificationSubscriber,$excluded);
        $this->createNotificationUser($notificationSubscriber, $notification);
        return ['url' => $notification->url, 'users' => $notificationSubscriber, 'notification' => $notification];
        } catch (\Exception $e) {

            \Log::error('Data: '.$e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
            return [];
        }
    }
    public function shootRequestCreatedProcess($event, $notification_event)
    {

        //   try{

        $agent_id = $event->requested_by_id;
        $roles = json_decode($notification_event?->roles) ?? [];
        $notificationSubscriber = NotificationEventSubscriber::where('notification_event_id', $notification_event->id)->pluck('user_id')->toArray();
        $event_attendees = []; //$event->attendees()->pluck('user_id')->toArray();
        $notification = new Notification();
        $notification->title = 'New photo/video shoot request created.';
        $notification->message = 'New photo/video shoot request created by ' . $event->requestedBy->name . ' for property Ref # ' . $event->property->ref_no . '. Kindly review it.';
        $notification->notification_event_id = $notification_event->id;
        $notification->url = route('admin.jobs.shoot.index') . '?id=' . $event->id;
        $notification->save();
        if (count($roles) > 0) {
            $users_by_role = User::whereHas(
                'roles', function ($q) use ($roles) {
                $q->whereIn('id', $roles);
            }
            )->pluck('id')->toArray();
            $notificationSubscriber = array_merge($users_by_role, $notificationSubscriber);
        }

        $notificationSubscriber = array_merge([$agent_id], $notificationSubscriber, $event_attendees);
        $notificationSubscriber = array_unique($notificationSubscriber);
        $excluded = json_decode($notification_event?->excluded) ?? [];
        $notificationSubscriber =  array_match_and_remove($notificationSubscriber,$excluded);
        $this->createNotificationUser($notificationSubscriber, $notification);
        return ['url' => $notification->url, 'users' => $notificationSubscriber, 'notification' => $notification];
//        } catch (\Exception $e) {
//            \Log::error($e->getMessage());
//            return [];
//        }
    }
    public function shootRequestActionProcess($event, $notification_event)
    {

        //   try{

        $agent_id = $event->requested_by_id;
        $roles = json_decode($notification_event?->roles) ?? [];
        $notificationSubscriber = NotificationEventSubscriber::where('notification_event_id', $notification_event->id)->pluck('user_id')->toArray();
        $event_attendees = []; //$event->attendees()->pluck('user_id')->toArray();
        $notification = new Notification();
        if($event->status == 'Approved'){
             $notification->title = 'Your Photo/Video shoot request has been ' . $event->status;
            $notification->message = 'Photo/Video shoot request has been ' . $event->status . ' by ' . $event->approvedBy->name . 'for property Ref # ' . $event->property->ref_no . '. Kindly review it.';
        }elseif($event->status == 'Rejected'){
            $notification->title = 'Your Photo/Video shoot request has been ' . $event->status;
            $notification->message = 'Photo/Video shoot request has been ' . $event?->status . ' by ' . $event?->rejectedBy?->name . 'for property Ref # ' . $event->property->ref_no . '. Kindly review it.';
        }else{
            $notification->title = 'Your Photo/Video shoot request has been ' . $event->status;
            $notification->message = 'Photo/Video shoot request has been ' . $event->status . ' for property Ref # ' . $event->property->ref_no . '. Kindly review it.';
        }
        $notification->notification_event_id = $notification_event->id;
        $notification->url = route('admin.jobs.shoot.index') . '?id=' . $event->id;
        $notification->save();
        if (count($roles) > 0) {
            $users_by_role = User::whereHas(
                'roles', function ($q) use ($roles) {
                $q->whereIn('id', $roles);
            }
            )->pluck('id')->toArray();
            $notificationSubscriber = array_merge($users_by_role, $notificationSubscriber);
        }

        $notificationSubscriber = array_merge([$agent_id], $notificationSubscriber, $event_attendees);
        $notificationSubscriber = array_unique($notificationSubscriber);
        $excluded = json_decode($notification_event?->excluded) ?? [];
        $notificationSubscriber =  array_match_and_remove($notificationSubscriber,$excluded);
        $this->createNotificationUser($notificationSubscriber, $notification);
        return ['url' => $notification->url, 'users' => $notificationSubscriber, 'notification' => $notification];
//        } catch (\Exception $e) {
//            \Log::error($e->getMessage());
//            return [];
//        }
    }
    public function shootJobStatusChangeProcess($event, $notification_event)
    {

        //   try{

        $agent_id = $event->requested_by_id;
        $roles = json_decode($notification_event?->roles) ?? [];
        $notificationSubscriber = NotificationEventSubscriber::where('notification_event_id', $notification_event->id)->pluck('user_id')->toArray();
        $event_attendees = []; //$event->attendees()->pluck('user_id')->toArray();
        $notification = new Notification();
        if($event->status == 'Approved'){
             $notification->title = 'Your Photo/Video shoot request has been ' . $event->status;
            $notification->message = 'Photo/Video shoot request has been ' . $event->status . ' by ' . $event->approvedBy->name . 'for property Ref # ' . $event->property->ref_no . '. Kindly review it.';
        }elseif($event->status == 'Rejected'){
            $notification->title = 'Your Photo/Video shoot request has been ' . $event->status;
            $notification->message = 'Photo/Video shoot request has been ' . $event?->status . ' by ' . $event?->rejectedBy?->name . 'for property Ref # ' . $event->property->ref_no . '. Kindly review it.';
        }else{
            $notification->title = 'Your Photo/Video shoot request has been ' . $event->status;
            $notification->message = 'Photo/Video shoot request has been ' . $event->status . ' for property Ref # ' . $event->property->ref_no . '. Kindly review it.';
        }
        $notification->notification_event_id = $notification_event->id;
        $notification->url = route('admin.jobs.shoot.index') . '?id=' . $event->id;
        $notification->save();
        if (count($roles) > 0) {
            $users_by_role = User::whereHas(
                'roles', function ($q) use ($roles) {
                $q->whereIn('id', $roles);
            }
            )->pluck('id')->toArray();
            $notificationSubscriber = array_merge($users_by_role, $notificationSubscriber);
        }

        $notificationSubscriber = array_merge([$agent_id], $notificationSubscriber, $event_attendees);
        $notificationSubscriber = array_unique($notificationSubscriber);
        $excluded = json_decode($notification_event?->excluded) ?? [];
        $notificationSubscriber =  array_match_and_remove($notificationSubscriber,$excluded);
        $this->createNotificationUser($notificationSubscriber, $notification);
        return ['url' => $notification->url, 'users' => $notificationSubscriber, 'notification' => $notification];
//        } catch (\Exception $e) {
//            \Log::error($e->getMessage());
//            return [];
//        }
    }

    // deal realted notificaitons
    public function dealCreatedProcess($deal, $notification_event)
    {

        //   try{

        $agent_id = $deal->agent_id;
        $roles = json_decode($notification_event?->roles) ?? [];
        $notificationSubscriber = NotificationEventSubscriber::where('notification_event_id', $notification_event->id)->pluck('user_id')->toArray();

        $notification = new Notification();
        $notification->title = 'New deal created';
        $notification->message = 'New deal created for property Ref # ' . $deal->property->ref_no . '. Kindly review it.';
        $notification->notification_event_id = $notification_event->id;
        $notification->url = route('admin.work.deals.create',['id'=>$deal->id]);
        $notification->save();
        if (count($roles) > 0) {
            $users_by_role = User::whereHas(
                'roles', function ($q) use ($roles) {
                $q->whereIn('id', $roles);
            }
            )->pluck('id')->toArray();
            $notificationSubscriber = array_merge($users_by_role, $notificationSubscriber);
        }
        if($agent_id != $deal->property->agent_id)
        {
            $notificationSubscriber[] = $deal->property->agent_id;
        }
        $notificationSubscriber = array_merge([$agent_id], $notificationSubscriber);
        $notificationSubscriber = array_unique($notificationSubscriber);
        $excluded = json_decode($notification_event?->excluded) ?? [];
        $notificationSubscriber =  array_match_and_remove($notificationSubscriber,$excluded);
        $this->createNotificationUser($notificationSubscriber, $notification);
        return ['url' => $notification->url, 'users' => $notificationSubscriber, 'notification' => $notification];
//        } catch (\Exception $e) {
//            \Log::error($e->getMessage());
//            return [];
//        }
    }
    public function dealCompletedProcess($event, $notification_event)
    {

           try{

        $agent_id = $event->agent_id;
        $roles = json_decode($notification_event?->roles) ?? [];
        $notificationSubscriber = NotificationEventSubscriber::where('notification_event_id', $notification_event->id)->pluck('user_id')->toArray();
               $hirarchy_users = (new DealApiController())->getCommissionUsersProcess($event->id);
               foreach ($hirarchy_users as $hirarchy_user){
                   $notificationSubscriber[] = $hirarchy_user->id;
               }
        $notification = new Notification();
        $notification->title = 'Deal Completed';
        $notification->message = 'Deal has been successfully closed for listing # ' . $event->property->ref_no . '. Kindly review it.';
        $notification->notification_event_id = $notification_event->id;
        $notification->url = route('admin.work.deals.create',['id'=>$event->id]);
        $notification->save();
        if (count($roles) > 0) {
            $users_by_role = User::whereHas(
                'roles', function ($q) use ($roles) {
                $q->whereIn('id', $roles);
            }
            )->pluck('id')->toArray();
            $notificationSubscriber = array_merge($users_by_role, $notificationSubscriber);
        }

        $notificationSubscriber = array_merge([$agent_id], $notificationSubscriber);
        $notificationSubscriber = array_unique($notificationSubscriber);
        $excluded = json_decode($notification_event?->excluded) ?? [];
        $notificationSubscriber =  array_match_and_remove($notificationSubscriber,$excluded);
        $this->createNotificationUser($notificationSubscriber, $notification);
        return ['url' => $notification->url, 'users' => $notificationSubscriber, 'notification' => $notification];
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return [];
        }
    }
    public function dealApprovedProcess($event, $notification_event)
    {

        //   try{

        $agent_id = $event->agent_id;
        $roles = json_decode($notification_event?->roles) ?? [];
        $notificationSubscriber = NotificationEventSubscriber::where('notification_event_id', $notification_event->id)->pluck('user_id')->toArray();
        $hirarchy_users = (new DealApiController())->getCommissionUsersProcess($event->id);
        foreach ($hirarchy_users as $hirarchy_user){
            $notificationSubscriber[] = $hirarchy_user->id;
        }
        $notification = new Notification();
        $notification->title = 'Deal Completed';
        $notification->message = 'Deal has been successfully closed for listing # ' . $event->property->ref_no . '. Kindly review it.';
        $notification->notification_event_id = $notification_event->id;
        $notification->url = route('admin.work.deals.create',['id'=>$event->id]);
        $notification->save();
        if (count($roles) > 0) {
            $users_by_role = User::whereHas(
                'roles', function ($q) use ($roles) {
                $q->whereIn('id', $roles);
            }
            )->pluck('id')->toArray();
            $notificationSubscriber = array_merge($users_by_role, $notificationSubscriber);
        }

        $notificationSubscriber = array_merge([$agent_id], $notificationSubscriber);
        $notificationSubscriber = array_unique($notificationSubscriber);
        $excluded = json_decode($notification_event?->excluded) ?? [];
        $notificationSubscriber =  array_match_and_remove($notificationSubscriber,$excluded);
        $this->createNotificationUser($notificationSubscriber, $notification);
        return ['url' => $notification->url, 'users' => $notificationSubscriber, 'notification' => $notification];
//        } catch (\Exception $e) {
//            \Log::error($e->getMessage());
//            return [];
//        }
    }
    public function dealRejectedProcess($event, $notification_event, $deal_approval)
    {


        //   try{
        $action_by = User::where('id',$deal_approval->action_by_id)->first();
        $agent_id = $event->agent_id;
        $roles = json_decode($notification_event?->roles) ?? [];


        $notificationSubscriber = NotificationEventSubscriber::where('notification_event_id', $notification_event->id)->pluck('user_id')->toArray();

        $notification = new Notification();
        $notification->title = 'Deal Rejected';
        $notification->message = 'Deal information has been rejected by  # ' . $action_by->first_name . ' ' . $action_by->last_name . '. Kindly review it.';
        $notification->notification_event_id = $notification_event->id;
        $notification->url = route('admin.deals_revamped.index') . '?deal_id=' . $event->id;
        $notification->save();
        if (count($roles) > 0) {
            $users_by_role = User::whereHas(
                'roles', function ($q) use ($roles) {
                $q->whereIn('id', $roles);
            }
            )->pluck('id')->toArray();
            $notificationSubscriber = array_merge($users_by_role, $notificationSubscriber);
        }

        $notificationSubscriber = array_merge([$agent_id], $notificationSubscriber);
        $notificationSubscriber = array_unique($notificationSubscriber);
        $excluded = json_decode($notification_event?->excluded) ?? [];
        $notificationSubscriber =  array_match_and_remove($notificationSubscriber,$excluded);
        $this->createNotificationUser($notificationSubscriber, $notification);
        return ['url' => $notification->url, 'users' => $notificationSubscriber, 'notification' => $notification,'rejected_by'=>$action_by];
//        } catch (\Exception $e) {
//            \Log::error($e->getMessage());
//            return [];
//        }
    }
    public function dealSubmittedForApprovalProcess($event, $notification_event)
    {

           try{

        $agent_id = $event->agent_id;
        $roles = json_decode($notification_event?->roles) ?? [];
        $notificationSubscriber = NotificationEventSubscriber::where('notification_event_id', $notification_event->id)->pluck('user_id')->toArray();

        $notification = new Notification();
        $notification->title = 'Deal submitted for approval';
        $notification->message = $event?->action_by?->name.' has submitted a deal for approval please have a look and take action.';
        $notification->notification_event_id = $notification_event->id;
        $notification->url = route('admin.deals_revamped.index') . '?deal_id=' . $event->id;
        $notification->save();
        if (count($roles) > 0) {
            $users_by_role = User::whereHas(
                'roles', function ($q) use ($roles) {
                $q->whereIn('id', $roles);
            }
            )->pluck('id')->toArray();
            $notificationSubscriber = array_merge($users_by_role, $notificationSubscriber);
        }

        //$notificationSubscriber = array_merge([$agent_id], $notificationSubscriber);
        $notificationSubscriber = array_unique($notificationSubscriber);
        $excluded = json_decode($notification_event?->excluded) ?? [];
        $notificationSubscriber =  array_match_and_remove($notificationSubscriber,$excluded);
        $this->createNotificationUser($notificationSubscriber, $notification);
        return ['url' => $notification->url, 'users' => $notificationSubscriber, 'notification' => $notification];
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return [];
        }
    }
    public function dealReSubmittedForApprovalProcess($event, $notification_event)
    {

        try{

            $agent_id = $event->agent_id;
            $roles = json_decode($notification_event?->roles) ?? [];
            $notificationSubscriber = NotificationEventSubscriber::where('notification_event_id', $notification_event->id)->pluck('user_id')->toArray();

            $notification = new Notification();
            $notification->title = 'Deal submitted for approval';
            $notification->message = $event?->action_by?->name.' has submitted a deal for approval please have a look and take action.';
            $notification->notification_event_id = $notification_event->id;
            $notification->url = route('admin.deals_revamped.index') . '?deal_id=' . $event->id;
            $notification->save();
            if (count($roles) > 0) {
                $users_by_role = User::whereHas(
                    'roles', function ($q) use ($roles) {
                    $q->whereIn('id', $roles);
                }
                )->pluck('id')->toArray();
                $notificationSubscriber = array_merge($users_by_role, $notificationSubscriber);
            }

           // $notificationSubscriber = array_merge([$agent_id], $notificationSubscriber);
            $notificationSubscriber = array_unique($notificationSubscriber);
            $excluded = json_decode($notification_event?->excluded) ?? [];
            $notificationSubscriber =  array_match_and_remove($notificationSubscriber,$excluded);
            $this->createNotificationUser($notificationSubscriber, $notification);
            return ['url' => $notification->url, 'users' => $notificationSubscriber, 'notification' => $notification];
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return [];
        }
    }

    public function dealNoteCreatedProcess($dealNote, $notification_event, $userId)
    {

     //   try{
        $roles = $notification_event?->roles != null ? json_decode($notification_event?->roles) : [];
        $notificationSubscriber = NotificationEventSubscriber::where('notification_event_id', $notification_event->id)->pluck('user_id')->toArray() ?? [];

        $notification = new Notification();
        $notification->title = 'New deal note added.';
        $notification->message = $dealNote->note;
        $notification->notification_event_id = $notification_event->id;
        $notification->url = route('admin.work.deals.index') . '?deal_id=' . $dealNote->deal_id;
        $notification->save();

        if(is_array($roles) && count($roles) > 0) {
            $users_by_role = User::whereHas(
                'roles', function ($q) use ($roles) {
                $q->whereIn('id', $roles);
            }
            )->pluck('id')->toArray() ?? [];
            $notificationSubscriber = array_merge($users_by_role, $notificationSubscriber);
        }


        $notificationSubscriber = array_merge([$userId], $notificationSubscriber);
        $notificationSubscriber = array_unique($notificationSubscriber);
        $excluded = $notification_event?->excluded != null ? json_decode($notification_event?->excluded) : [];
        $notificationSubscriber =  array_match_and_remove($notificationSubscriber,$excluded);
        $this->createNotificationUser($notificationSubscriber, $notification);
        return ['url' => $notification->url, 'users' => $notificationSubscriber, 'notification' => $notification];
//        } catch (\Exception $e) {
//            \Log::error($e->getMessage());
//            return [];
//        }
    }
    public function dealAgentInviteProcess($deal, $notification_event, $invited_by, $invite_to)
    {

        try{

            $agent_id = $deal->agent_id;
            $roles = json_decode($notification_event?->roles) ?? [];
            $notificationSubscriber = NotificationEventSubscriber::where('notification_event_id', $notification_event->id)->pluck('user_id')->toArray();

            $notification = new Notification();
            $notification->title = 'Agent Invited to Deal';
            $notification->message = $invite_to?->name." has been invited to deal [$deal->id] by ".$invited_by?->name;
            $notification->notification_event_id = $notification_event->id;
            $notification->url = route('admin.deals_revamped.index') . '?deal_id=' . $deal->id;
            $notification->save();
            if (count($roles) > 0) {
                $users_by_role = User::whereHas(
                    'roles', function ($q) use ($roles) {
                    $q->whereIn('id', $roles);
                }
                )->pluck('id')->toArray();
                $notificationSubscriber = array_merge($users_by_role, $notificationSubscriber);
            }

            $notificationSubscriber = array_merge([$agent_id], $notificationSubscriber);
            $notificationSubscriber = array_unique($notificationSubscriber);
            $excluded = json_decode($notification_event?->excluded) ?? [];
            $notificationSubscriber =  array_match_and_remove($notificationSubscriber,$excluded);
            $this->createNotificationUser($notificationSubscriber, $notification);
            return ['url' => $notification->url, 'users' => $notificationSubscriber, 'notification' => $notification];
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return [];
        }
    }
    public function salesProgressionAssignedProcess($deal, $notification_event,$sale_progression,$sales_person)
    {

        try{

            $agent_id = $deal->agent_id;
            $roles = json_decode($notification_event?->roles) ?? [];
            $notificationSubscriber = NotificationEventSubscriber::where('notification_event_id', $notification_event->id)->pluck('user_id')->toArray();

            $notification = new Notification();
            $notification->title = 'Sales Progression Assigned';
            $notification->message = $sales_person?->name." has been assigned as sales progression for the deal [#$deal->id]";
            $notification->notification_event_id = $notification_event->id;
            $notification->url = route('admin.deals_revamped.index') . '?deal_id=' . $deal->id;
            $notification->save();
            if (count($roles) > 0) {
                $users_by_role = User::whereHas(
                    'roles', function ($q) use ($roles) {
                    $q->whereIn('id', $roles);
                }
                )->pluck('id')->toArray();
                $notificationSubscriber = array_merge($users_by_role, $notificationSubscriber);
            }

            $notificationSubscriber = array_merge([$agent_id], $notificationSubscriber);
            $notificationSubscriber = array_unique($notificationSubscriber);
            $excluded = json_decode($notification_event?->excluded) ?? [];
            $notificationSubscriber =  array_match_and_remove($notificationSubscriber,$excluded);
            $this->createNotificationUser($notificationSubscriber, $notification);
            return ['url' => $notification->url, 'users' => $notificationSubscriber, 'notification' => $notification];
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return [];
        }
    }
}
