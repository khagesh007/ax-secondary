<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReportingLeadByStatusResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        try {
            // make a resource for leads of agents that shows name and count of leads and also all the counts of each leadStatus
            return [
                'name' => $this->name,
                'leads' => $this->leads,
                'leadStatus' => $this->leadStatus,
            ];



        } catch (\Throwable $th) {
            //throw $th;
        }

        return parent::toArray($request);
    }
}
