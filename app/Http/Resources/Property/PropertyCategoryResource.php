<?php

namespace App\Http\Resources\Property;

use Illuminate\Http\Resources\Json\JsonResource;

class PropertyCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
//        return [
//            'id' => '54',
//            'text' =>$this?->portals[0]?->title,
//        ];
        return parent::toArray($request);
    }
}
