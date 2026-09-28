<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Lead\LeadAgentsResource;
use App\Http\Resources\Lead\LeadCriteriaResource;
use App\Models\Country;

class ReportingLeadResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        try{
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'phone' => $this->phone,
            'whatsapp' => $this->whatsapp,
            'address' => $this->address,
            'company' => $this->company,
            'dob' => $this->dob,
            'gender' => $this->gender,
            'for' => $this->for,
            'nationality' => new CountryResource(Country::find($this->nationality_id) ?? null),
            'lead_status' => new CommonResource1($this->leadStatus),
            'lead_sub_status' => new CommonResource1($this->leadSubStatus),
            'lead_type' => new CommonResource1($this->leadType),
            'lead_source' => new CommonResource1($this->leadSource),
            'lead_medium' => new CommonResource1($this->leadMedium),
            'agent' => new UserResource($this->agent),
            'additional_agent' =>   LeadAgentsResource::collection($this->leadAgents ?? []),
            'priority' => $this->priority,
            'status' => $this->status,
            'lead_assignment_date' => $this->lead_assignment_date,
            'follow_up_date' => $this->follow_up_date,
            'viewing_date' => $this->viewing_date,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
        }catch(\Exception $e){
            return [];
        }
        return parent::toArray($request);
    }
}
