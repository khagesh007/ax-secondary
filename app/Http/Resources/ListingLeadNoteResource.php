<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource;

class ListingLeadNoteResource extends JsonResource
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
            'id'=>$this->id,
            'listing_lead_id'=>$this->listing_lead_id,
            'note'=>$this->note,
            'created_at'=>$this->created_at,
            'agent' => new UserResource($this->createdBy),

        ];
        return parent::toArray($request);
    }
}
