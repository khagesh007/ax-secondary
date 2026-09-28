<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PropertyMiniResource extends JsonResource
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
            'id' =>$this->id,
            'ref_no' =>$this->ref_no,
            'title' =>$this->title,
            'description' =>$this->description,
            'for' =>$this->for,
            'unit_no' =>$this->unit_no,
            'publish_status' =>$this->publish_status,
            'photos' => PropertyPhotosResource::collection($this->photos),
            'property_category_id' => $this->property_category_id,
           // 'data' => parent::toArray($request),
        ];
        return parent::toArray($request);
    }
}
