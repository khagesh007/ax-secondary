<?php

namespace App\Http\Resources\Property;

use Illuminate\Http\Resources\Json\JsonResource;

class PropertyAmenitiesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */

    public function toArray($request)
    {
       //return new PropertyAmenityResource($this->whenLoaded('amenity'));

         return new PropertyAmenityPortalResource($this->amenity?->portals);

        return parent::toArray($request);
    }
}
