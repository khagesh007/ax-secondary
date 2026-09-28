<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TraccarUserSettingResource extends JsonResource
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
            'title' => $this->title,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'radius' => $this->radius,
            'zoom' => $this->zoom,
            'stay_time' => $this->stay_time
        ];
      //  return parent::toArray($request);
    }
}
