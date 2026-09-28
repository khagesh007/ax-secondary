<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PropertyNoteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {return [
        'id'=>$this->id,
        'property_id'=>$this->property_id,
        'note'=>$this->note,
        'created_at'=>$this->created_at,
        'agent' => new UserResource($this->createdBy),
    ];

        return parent::toArray($request);
    }
}
