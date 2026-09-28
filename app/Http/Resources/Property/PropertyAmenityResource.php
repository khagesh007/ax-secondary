<?php

namespace App\Http\Resources\Property;

use Illuminate\Http\Resources\Json\JsonResource;

class PropertyAmenityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $portal = new PropertyAmenityPortalResource($this->portals);

        return [
             $portal
          // new PropertyAmenityPortalResource($this->portals)
        ];
        return parent::toArray($request);
    }
}
