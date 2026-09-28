<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\LeadResource;
use App\Http\Resources\Property\PropertyResourceV2;
use App\Http\Resources\ContactResource;
use App\Http\Resources\CommonResource1;

class EventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        return [
            'id'=>$this->id,
            'event_type_id'=>$this->event_type_id,
            'event_type'=>new CommonResource1($this->type),
            'google_calendar_event_id'=>$this->google_calendar_event_id,
            'start_time'=>$this->start_time,
            'end_time'=>$this->end_time,
            'priority'=>$this->priority,
            'event_status'=>$this->event_status,
            'title'=>$this->title,
            'location'=>$this->location,
            'description'=>$this->description,
            'invitation'=>$this->invitation,
            'reminder_status'=>$this->reminder_status,
            'email_to_attendees'=>$this->email_to_attendees,
            'reminder_to_attendees'=>$this->reminder_to_attendees,
            'reminder_date'=>$this->reminder_date,
            'reminder'=>$this->reminder,
            'reminder_frequency'=>$this->reminder_frequency,
            'recurring'=>$this->recurring,
            'recurring_data'=>$this->recurring_data,
            'agent_id'=>$this->agent_id,
            'agent'=>new UserResource($this->agent),
            'contact_id'=>$this->contact_id,
            'contact'=>new ContactResource($this->contact),
            'lead_id'=>$this->lead_id,
            'lead'=>new LeadResource($this->lead),
            'property_id'=>$this->property_id,
            'property'=>new PropertyResourceV2($this->property),
            'updated_by_id'=>$this->updated_by_id,
            'status'=>$this->status,
            'public'=>$this->public,
            'note'=>$this->note,
            'attendees'=>UserResource::collection($this->attendees),
            'created_at'=>$this->created_at,
            'updated_at'=>$this->updated_at,
        ];

    }
}
