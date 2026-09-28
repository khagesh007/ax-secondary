<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserPortalResource extends JsonResource
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
            'bitrix_role' => $this->bitrix_role,
            'name' => $this->name,
            'full_name' => $this->name,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'position' => $this->position,
            'email' => $this->email,
            'secondary_email' => $this->secondary_email,
            'mobile' => $this->mobile,
            'thumbnail' => $this->thumbnail,
            'image' => $this->image,
            'avatar' => $this->avatar,
            'status' => $this->status,
            'locations' => $this->locations,
            'bio' => $this->bio,
        ];
       // return parent::toArray($request);
    }
}
