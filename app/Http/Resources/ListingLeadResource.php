<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ListingLeadResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        try {
            return [
                'id' => $this->id,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'mobile' => $this->mobile,
                'unit_no' => $this->unit_no,
                'for' => $this->for,
                'bed_id' => $this->bed_id,
                'bed' => new CommonResource1($this->bed),
                'bath_id' => $this->bath_id,
                'bath' => new CommonResource1($this->bath),
                'status_id' => $this->status_id,
                'status' => new CommonResource1($this->status),
                'source_id' => $this->source_id,
                'source' => new CommonResource1($this->source),
                'community_id' => $this->community_id,
                'community' => new CommonResource1($this->community),
                'sub_community_id' => $this->sub_community_id,
                'subCommunity' => new CommonResource1($this->subCommunity),
                'community_property_id' => $this->community_property_id,
                'communityProperty' => new CommonResource1($this->communityProperty),

                'location_id' => $this->location_id,
                'location_type' => $this->location_type,
                'note' => $this->note,
                'recording_file' => $this->recording_file,
                'property_category_id' => $this->property_category_id,
                'propertyCategory' => new CommonResourceTitle($this->propertyCategory),
                'agent_id' => new UserResource($this->agent),

            ];
        } catch (\Exception $e) {
            return [];
        }
        return parent::toArray($request);
    }
}
