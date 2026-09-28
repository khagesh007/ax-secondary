<?php

namespace App\Http\Resources\Lead;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Property\PropertyAgentResource;
class LeadPropertyResource extends JsonResource
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
            'unit_no' => $this->unit_no,
            'location' => $this->location,
            'community' => $this->community?->name,
            'alternate_community' => $this->alternate_name,
            'sub_community' => $this->subcommunity?->name,
            'alternate_sub_community' => $this->subcommunity?->alternate_name,
            'community_property' => $this->communityProperty?->name??null,
            'alternate_community_property' => $this->communityProperty?->alternate_name??null,
            'category' => $this->category,
            'for'=> $this->for,
            'is_live' => $this->is_live,
            'publish_status' => $this->publish_status,
            'bed' => $this->bed,
            'price' => $this->price?->price ?? null,
            'first_image' => $this->first_image ? [
                'id' => $this->first_image->id,
                'thumbnail' => $this->first_image->thumbnail,
                'original' => $this->first_image->original,
            ] : null,



        ];
    }
}
