<?php

namespace App\Http\Resources\MatchingListing;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Property\PropertyAgentResource;
class MatchingListingResource extends JsonResource
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
            'ref_no' => $this->ref_no,
            'title' => $this->title,
            'developer' =>  $this?->developer?->short_name ?? $this?->details?->developer ?? null,
            'unit_no' => $this->unit_no,
            'location' => $this->location,
            'community' => $this->community?->name,
            'sub_community' => $this->subcommunity?->name,
            'community_property' => $this->communityProperty?->name??null,
            'category' => $this->category,
            'for'=> $this->for,
            'is_live' => $this->is_live,
            'publish_status' => $this->publish_status,
            'completion_status' => $this->completion_status,
            'property_status' => $this->propertyStatus?->title,
            'furnished' => $this->furnished,
            'featured' => $this->featured,
            'exclusive' => $this->exclusive,
            'bed' => $this->bed?->name ?? null,
            'bath' => $this->bath?->name ?? null,
            'built_up_area' => $this->built_up_area,
            'view' => $this->view,
            'price' => $this->price?->price,
            'first_image' => $this->first_image ? [
                'id' => $this->first_image->id,
                'thumbnail' => $this->photos->first() ? $this->photos->first()->path : 'https://secondary.axcrm.ae/images/defaultListing.png',
                'original' => $this->photos->first() ? $this->photos->first()->path : 'https://secondary.axcrm.ae/images/defaultListing.png',
            ] : [
                'id' => null,
                'thumbnail' => 'https://secondary.axcrm.ae/images/defaultListing.png',
                'original' => 'https://secondary.axcrm.ae/images/defaultListing.png',
            ],
            'agent' => new PropertyAgentResource($this->agent) ?? null,
        ];
    }
}
