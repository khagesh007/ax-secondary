<?php

namespace App\Http\Resources\Lead;

use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class LeadAgentsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        return new UserResource($this->agent);
        return parent::toArray($request);
    }
}
