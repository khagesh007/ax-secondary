<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LocationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // dd($this->resource);
        $model = get_class($this->resource);
        if($model == 'App\Models\CommunityProperty') {
            $model = 'CommunityProperty';
        }
        if($model == 'App\Models\Community') {
            $model = 'Community';
        }
        if($model == 'App\Models\SubCommunity') {
            $model = 'SubCommunity';
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'path_name' => $this->path_name,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'type' => $model,
        ];

        return parent::toArray($request);
    }
}
