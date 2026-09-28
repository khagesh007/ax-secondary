<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class KpiTeamAgentReportResource extends JsonResource
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
            return [
                'id' => $this->id,
                'kpi' => $this->kpi,
                'kpi_title' => $this->kpi_title,
                'kpi_slug' => $this->kpi,
                'total' => $this->total,
                'count' => $this->count,
                'data' => $this->data,
                'daily_data' => $this->daily_data,

            ];
        } catch (\Exception $e) {
            return [];
        }
        return parent::toArray($request);
    }
}
