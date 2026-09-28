<?php

namespace App\Http\Resources\Kpi;

use Illuminate\Http\Resources\Json\JsonResource;

class KpiUserTargetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        // $achieved =$this?->result?->achieved ?? 0;
        // $achieved_percentage =  $achieved > 0 ? round(($achieved / $this->target) * 100, 2) : 0;
        $achieved = is_numeric($this?->result?->achieved) ? $this->result->achieved : 0;
        $target = is_numeric($this->target) ? $this->target : 1; // Avoid division by zero

        $achieved_percentage = ($achieved > 0 && $target > 0)
            ? round(($achieved / $target) * 100, 2)
            : 0;
//
//        $kpi_titles = [
//            'sales_value' => 'Sales Target',
//            'sales_target' => 'Sales Target',
//            'live_listings' => 'Live Listings',
//            'new_listings' => 'New Listings',
//            'tru_broker' => 'Tru Broker',
//            'super_agent' => 'Super Agent',
//            'pf_credit_limit' => 'PF Credit Limit',
//            'bayut_credit_limit' => 'Bayut Credit Limit',
//            'dubizzle_credit_limit' => 'Dubizzle Credit Limit',
//        ];
        $achieved = $this->result->achieved ?? 0;
        return [
            'target' => $this->target,
            'target_text' => 'sales_target' ? formatNumberMetricPrefixes($this->target)  :  $this->target,
            'achieved' => $this->result->achieved ?? 0,
            'achieved_text' => $this->kpi == 'sales_target' ? formatNumberMetricPrefixes($achieved)  : $achieved,
            'percentage' => $achieved_percentage,
            'kpi_title' => getKpiFullText($this->kpi),
            'kpi' => $this->kpi == 'sales_target' ? 'sales_value' : $this->kpi,

        ];
        return parent::toArray($request);
    }
}
