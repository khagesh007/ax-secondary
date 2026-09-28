<?php

namespace App\Http\Resources\Property;

use Illuminate\Http\Resources\Json\JsonResource;

class PropertyAgentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // if ($this->pp_enabled) {
        //     return [
        //         "name" => $this->pp_name,
        //         "first_name" => null,
        //         "last_name" => null,
        //         "email" => $this->email,
        //         "secondary_email" => $this->secondary_email,
        //         "mobile" => $this->mobile,
        //         "license_no" => $this->pp_license_no,
        //         "photo" => $this->pp_photo,

        //     ];
        // } else {
            return [
                "id"   => $this->id,
                "name" => $this->full_name,
                "first_name" => $this->first_name,
                "last_name" => $this->last_name,
                "email" => $this->email,
                "secondary_email" => $this->secondary_email,
                "telegram_chat_id" => $this->telegram_chatid,
                "telegram_user_name" => $this->telegram_username,
                "mobile" => $this->mobile,
                "license_no" => $this->license_no,
                "photo" => $this->image ?? null,
            ];
        //}
        // return parent::toArray($request);
    }
}
