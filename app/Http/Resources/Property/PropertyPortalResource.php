<?php

namespace App\Http\Resources\Property;

use Illuminate\Http\Resources\Json\JsonResource;

class PropertyPortalResource extends JsonResource
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
            'property_portal_id' => $this->id,
            'id' => $this->portal_id,
            'name' => $this->portal->name,
            'image' => $this->portal->image,
            'thumbnail' => $this->portal->thumbnail,
        ];
    }
}
