<?php

namespace App\Http\Resources\Bath;

use Illuminate\Http\Resources\Json\JsonResource;

class BathResource extends JsonResource
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
            'name' => $this?->name,
            'order' => $this?->order,
        ];
    }
}
