<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TraccarClientSettingResource extends JsonResource
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
            'server_url' => $this->server_url,
            'port' => $this->port,
            // convert to all small case
            'location_accuracy' => strtolower($this->location_accuracy),
            'frequency' => $this->frequency,
            'distance' => $this->distance,
            'angle' => $this->angle,
            'offline_buffering' => $this->offline_buffering,
            'track_from' => $this->track_from,
            'track_to' => $this->track_to,
            'coordinates_accuracy' => $this->coordinates_accuracy,
            'version' => $this->version,
        ];
       // return parent::toArray($request);
    }
}
