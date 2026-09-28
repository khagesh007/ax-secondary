<?php

namespace App\Http\Resources;

use App\Http\Resources\Lead\LeadAgentsResource;
use App\Http\Resources\Lead\LeadCriteriaResource;
use App\Models\Country;
use Illuminate\Http\Resources\Json\JsonResource;

class LeadDataHubApiResource extends JsonResource
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
                'id' => $this->id,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'mobile' => $this->mobile,
                'phone' => $this->phone,
                'whatsapp' => $this->whatsapp,
                'address' => $this->address,
                'is_archive' => $this->is_archive,
                'is_active' => $this->is_active,
                'is_portal_lead_pool' => $this->is_portal_lead_pool,
                'is_bitrix_lead_pool'=> $this->is_bitrix_lead_pool,
                'dob' => $this->dob,
                'gender' => $this->gender,
                'for' => $this->for,
                'nationality' => new CountryResource(Country::find($this->nationality_id) ?? null),
                'agent' => new UserDataHubApiResource($this->agent),
                'lead_status' => new CommonResource1($this->leadStatus),
                'lead_type' => new CommonResource1($this->leadType),
                'lead_source' => new CommonResource1($this->leadSource),
                'lead_medium' => new CommonResource1($this->leadMedium),
                
                // 'property' => new PropertyMiniResource($this->property),
                'criteria' => new LeadCriteriaResource($this->criteria),
                'updatedBy' => new UserDataHubApiResource($this->updatedBy),
                // 'updated_by_id' => $this->updated_by_id,
                'property_type' => [
                    'id' => $this->property?->property_category_id,
                    'name' => $this->property?->category,
                ],
                'property_category_id' => $this->property ? $this->property->property_category_id : null,
                'property_category' => $this->property ? $this->property?->category : null,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ];
        
    }
}
