<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ContactAXResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $agentArray = [
            'id' => $this?->agent?->id,
            'full_name' => $this?->agent?->full_name,
            'mobile' => $this?->agent?->mobile,
            'email' => $this?->agent?->email,
        ];

        // map the secondary agent
        if ($this->contactAgents->count() > 0) {
            $sec_agentArray = $this->contactAgents->map(function ($item) {
                return [
                    'id' => $item->id,
                    'full_name' => $item?->agent?->full_name,
                    'mobile' => $item?->agent?->mobile,
                    'email' => $item?->agent?->email,
                    // 'source' => new CommonResource1($this->contactSource),
                ];
            });
        }

        return [
            'id' => $this->id,
            'bitrix_id' => $this->bitrix_id,
            'bitrix_index' => $this->bitrix_index,
            'my_crm_id' => $this->my_crm_id,
            'title' => $this->title,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'whatsapp' => $this->whatsapp,
            'address' => $this->address,
            'dob' => $this->dob,
            'follow_up_date' => $this->follow_up_date,
            'nationality_id' => $this->nationality_id,
            'country_id' => $this->country_id,
            'passport' => $this->passport,
            'passport_expiry' => $this->passport_expiry,
            'phone' => $this->phone,
            'company' => $this->company,
            'website' => $this->website,
            'region_id' => $this->region_id,
            'contact_source_id' => $this->contact_source_id,
            'contact_status_id' => $this->contact_status_id,
            'processed_mobile' => validatePhoneNumber($this->mobile),
            'properties' => [
                'count' => $this->properties->count(),
                'data' => PropertyResource::collection($this->activeProperties),
            ],
            'agent' => $agentArray ?? null,
            'type' => $this->contactTypes?CommonResource1::collection($this->contactTypes):null,
            'source' => $this->contactSource?new CommonResource1($this->contactSource):null,
            'gender' => $this->gender,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'secondary_agents' => $sec_agentArray ?? null,
        ];
    }
}
