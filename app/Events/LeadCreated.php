<?php

namespace App\Events;

use App\Models\Lead;
use App\Models\LeadNote;
use App\Models\Medium;
use App\Models\Notification;
use App\Models\NotificationEvent;
use App\Models\Property;
use App\Models\Source;
use App\Models\Shortlink;
use App\Models\PropertyMeta;
use App\Traits\NotificationManager;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class LeadCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels, NotificationManager;

    public $lead;
    public $noti_title;
    public $noti_message;
    public $url;
    public $subscriber;

    public $notification;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($lead)
    {
        $this->lead = $lead;
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



        $notification_event = NotificationEvent::where('class_name', 'LeadCreated')->first();
        $process = $this->leadCreatedProcess($this->lead, $notification_event);
        $this->lead = [];
        if (!empty($notification_event) && !empty($process)) {
            $this->notification = $process['notification'];
            $this->noti_title = $process['notification']['title'];
            $this->noti_message = $process['notification']['message'];
            $this->subscriber = $process['users'];
            $medium = json_decode($notification_event?->medium) ?? [];
            if (in_array('WEB', $medium)) {
                return new Channel('web-notification');
            }
        }
        //return new Channel('web-notification');
    }



    private function sendLeadToWebhook($leadData)
    {
        // dd($leadData['first_name']);
        // $webhookUrl = 'http://127.0.0.1:8000/api/webhook/get-lead';
        $webhookUrl = 'https://crm.axcap.ae/rest/1/e6w2jdc3uuz20c84/crm.lead.add.json';
        $lead = Lead::find($leadData['id']);
        $property = Property::find($lead['property_id']);
        $location_details = '';
        if ($property) {
            if ($property->community_id != null) {
                $community = \App\Models\Community::find($property->community_id);
                $location_details = $location_details . 'Community:' . $community->name ?? '';
            }

            if ($property->sub_community_id != null) {
                $sub_community = \App\Models\SubCommunity::find($property->sub_community_id);
                $location_details = $location_details . 'SubCommunity:' . $sub_community->name ?? '';
            }
            if ($property->community_property_id != null) {
                $community_property = \App\Models\CommunityProperty::find($property->community_property_id);
                $location_details = $location_details . 'Community Property:' . $community_property->name ?? '';
            }
        }

        $source_name = null;
        if ($lead['source_id'] != null) {
            if ($lead['source_id'] == 4) {
                $source_name = 12;
            } else if ($lead['source_id'] == 13) {
                $source_name = 24;
            } else if ($lead['source_id'] == 31) {
                $source_name = 15;
            } else {
                $source_name = '';
            }

        }
        $original_source = Source::find($lead['source_id']);
        $property_details = 'Referenace No.:' . $property->ref_no .
        ', Title:' . $property->title .
        ', Property Category: ' . $property->for .
        ', Source: ' . $original_source->title ?? '-' .
        ', Build up Area ' . $property->built_up_area .
        ', Plot Size: ' . $property->plot_size .
        ', Parking: ' . $property->parking .
        ', Furnish Status: ' . $property->furnished .
        ', Location Details ' . $location_details .
        ', Property Category ' . $property->propertyCategory->title ?? '';
        $arry = [
            'NAME' => $lead['first_name'] . ' ' . $lead['last_name'],
            'EMAIL' => [
                'VALUE' => $lead['email'],
                'VALUE_TYPE' => 'WORK',
            ],
            'PHONE' => [
                'VALUE' => $lead['phone'],
                'VALUE_TYPE' => 'WORK',
            ],
            'MOBILE' => [
                'VALUE' => $lead['mobile'],
                'VALUE_TYPE' => 'WORK',
            ],
            'FOR' => $lead['for'],
            'UF_CRM_1608198550' => $source_name,
            'SOURCE_ID' => [
                'ID' => $original_source->id,
                'VALUE' => 'WEB',
            ],

            'UF_CRM_1553506792' => $property_details,

        ];
        $postData = json_encode(['fields' => $arry]);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $webhookUrl,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($postData),
            ],
            CURLOPT_RETURNTRANSFER => true,
        ]);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {

        }
        curl_close($ch);
    }
    // $arry =[
    //     'NAME' =>$lead['first_name'] . ' ' . $lead['last_name'],
    //     'EMAIL' =>[
    //         'VALUE' =>$lead['email'],
    //         'VALUE_TYPE' =>'WORK',
    //     ],
    //     'PHONE' =>[
    //         'VALUE' =>$lead['phone'],
    //         'VALUE_TYPE' =>'WORK',
    //     ],
    //     'MOBILE' =>[
    //         'VALUE' =>$lead['mobile'],
    //         'VALUE_TYPE' =>'WORK',
    //     ],
    //     'FOR' =>$lead['for'],
    //     'UF_CRM_1608198550' =>'ds',
    //     'SOURCE_ID' => [
    //         'VALUE' => 'WEB',
    //     ],
    //     'UF_CRM_1553506792'=>[
    //         'PROPERTY'=>[
    //             'ID'=>'1',
    //             'REF_NO'=>$property->ref_no,
    //             'TITLE'=>$property->title,
    //             'FOR'=>$property->for,
    //             'BUILD_UP_AREA'=>$property->built_up_area,
    //             'PLOT_SIZE'=>$property->plot_size       ,
    //             'PARKING'=>$property->parking,
    //             'FURNISHED'=>$property->furnished,
    //             'AGENT'=>$property->agent_id,
    //             'LOCATION'=>[
    //                 'COMMUNITY'=>[
    //                     'ID'=>$property->community_id,
    //                     'VALUE'=>$property->community_id,
    //                 ],
    //                 'SUB_COMMUNITY'=>[
    //                     'ID'=>$property->sub_community_id,
    //                     'VALUE'=>$property->sub_community_id,
    //             ],
    //                 'COMMNUNITY_PORPERTY'=>[
    //                     'ID'=>$property->community_property_id,
    //                     'VALUE'=>$property->community_property_id,
    //                 ],
    //             ],
    //             'CRITERIA'=>[

    //             ],
    //             'PROPERTY_CATEGORY'=>[
    //                 'ID'=>$property->property_category_id,
    //                 'VALUE'=>$property->property_category_id,
    //             ],
    //         ],
    //         'SOURCE'=>[
    //             'ID'=>$lead['source_id'],
    //             'VALUE'=>$lead['source_id'],
    //         ],
    //         'Message'=>$lead['message'],
    //     ],

    // ];
}
