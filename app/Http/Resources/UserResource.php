<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $user = $this;
        if ($user->children->count() > 0) {
            $is_manager = 1;
        } else {
            $is_manager = 0;
        }
        return [
            'id' => $this->id,
            'bitrix_role' => $this->bitrix_role,
            'bitrix_id' => $this->bitrix_id,
            'name' => $this->name,
            'full_name' => $this->name,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'position' => $this->position,
            'email' => $this->email,
            'secondary_email' => $this->secondary_email,
            'password' => $this->password,
            'roles' => $this->roles,
            'mobile' => $this->mobile,
            'thumbnail' => $this->thumbnail,
            'image' => $this->image,
            'avatar' => $this->avatar,
            'telegram_username' => $this->telegram_username,
            'telegram_chatid' => $this->telegram_chatid,
            'status' => $this->status,
            'locations' => $this->locations,
            'parent_id' => $this->parent_id,
            'bio' => $this->bio,
            'zone' => $this->zone,
            'is_report_exempt' => $this->is_report_exempt,
            'is_manager' => $is_manager,
            'telelog_entity' => $this->telelog_entity,
        ];
       // return parent::toArray($request);
    }
}
