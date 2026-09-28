<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PropertyPhotosResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'full' => $this->path,
            'original' => $this->original,
            'password' => $this->password
        ];
        // return parent::toArray($request);
    }
}
