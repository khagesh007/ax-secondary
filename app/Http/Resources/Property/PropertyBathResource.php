<?php

namespace App\Http\Resources\Property;

use Illuminate\Http\Resources\Json\JsonResource;

class PropertyBathResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        try {
            return [
                'id' => $this->id,
                'name' => $this?->portals->first()?->name,
            ];
        } catch (\Exception $e) {
            return [
                'id' => $this->id,
                'name' => $this->name,
            ];
        }
    }
}
