<?php

namespace App\Http\Resources\Lead;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CommonResource1;
class LeadCriteriaResource extends JsonResource
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
            'lead_id' => $this->lead_id,
            'min_price' => $this->min_price,
            'max_price' => $this->max_price,
            'min_bath_id' => $this->min_bath_id,
            'min_bath'=> new CommonResource1($this->minBath),
            'max_bath_id' => $this->max_bath_id,
            'max_bath'=> new CommonResource1($this->maxBath),
            'min_bed_id' => $this->min_bed_id,
            'min_bed'=> new CommonResource1($this->minBed),
            'max_bed_id' => $this->max_bed_id,
            'max_bed'=> new CommonResource1($this->maxBed),
            'view' => $this->view,
            'bitrix_data' => $this->bitrix_data,
            'communities' => $this->communities,
            'furnished' => $this->furnished,
            'locations' => LeadCriteriaLocationResource::collection($this->locations),
            // 'locations' => $this->locations,
        ];
        return parent::toArray($request);
    }
}
