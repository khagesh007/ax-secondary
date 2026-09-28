<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Property\PropertyAgentResource;
class PropertyResource extends JsonResource
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
            'owner_id' => $this->owner_id,
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

            'for'=> $this->for,
            'plot_size' => $this->plot_size,
            'built_up_area' => $this->built_up_area,
            'is_live' => $this->is_live,
            'agent' => new PropertyAgentResource($this->agent) ?? null,

        ];
    }
}
