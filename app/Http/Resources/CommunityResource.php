<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommunityResource extends JsonResource
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
            'slug' => $this->slug,
            'name' => $this->name,
            'alternate_name' => $this->alternate_name,
            'path_name' => $this->path_name,
            'alternate_path_name' => $this->alternate_path_name,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'region_id' => $this->region_id,
            'region' => new RegionResource($this->whenLoaded('region')),
        ];
    }
}
