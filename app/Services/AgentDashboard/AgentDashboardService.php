<?php

namespace App\Services\AgentDashboard;



use App\Models\Lead;
use App\Models\Property;
use App\Models\User;

use App\Services\Dashboard\LeadReportService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class AgentDashboardService
{

    protected $masterLeadService;

    public function __construct(LeadReportService $leadReportService)
    {
        $this->masterLeadService = $leadReportService;
    }


    public function getListingData($params): array
    {
       return [
            'total_listings' =>   Property::where('is_live', '1')->where('region_id', 1)
                    ->whereIn("agent_id", $params['user_ids'])->count() ?? 0,
            'total_property_finder' => Property::where('is_live', 1)
                ->where('region_id', 1)
                ->whereHas('portals', function ($query) {
                    $query->where('portal_id', 1);
                })
                ->whereIn("agent_id", $params['user_ids'])
                ->count(),
            'total_bayut' => Property::where('is_live', 1)
                    ->where('region_id', 1)
                    ->whereIn("agent_id", $params['user_ids'])
                ->whereHas('portals', function ($query) {
                    $query->where('portal_id', 2);
                })->count() ?? 0,
            'total_dubizzle' => Property::where('is_live', 1)
                ->where('region_id', 1)
              ->whereIn("agent_id", $params['user_ids'])
                ->whereHas('portals', function ($query) {
                    $query->where('portal_id', '=', 3);
                })

                ->count() ?? 0,
            'total_other' => Property::where('is_live', 1)
                ->where('region_id', 1)
                    ->whereIn("agent_id", $params['user_ids'])
                ->whereHas('portals', function ($query) {
                    $query->whereNotIn('portal_id', [1, 2, 3]);
                })
                ->count() ?? 0,
        ];

    }
    public function getLeadsData($params):array
    {
        $params['filter_by_users'] = true;
        $source_counts = $this->masterLeadService->sourceWiseLeadData($params);
        $source_data = [];
        foreach ($source_counts as $key => $value) {
            $source_data[$value['name']] = $value['count'];
        }
        return [
            'total_leads' => Lead::where('region_id', 1)
                ->whereIn("agent_id", $params['user_ids'])
                ->where('created_at', '>=', $params['start_date'])
                ->where('created_at', '<=', $params['end_date'])
                ->count() ?? 0,
            'total_property_finder' => $source_data['pf_count'] ?? 0,
            'total_bayut' =>  $source_data['bayut_count'] ?? 0,
            'total_dubizzle' => $source_data['dubizzle_count'] ?? 0,
            'total_other' => $source_data['other_count'] ?? 0,
        ];
    }
    public function portalWiseListingData($params): array
    {
        $property_finder = [
            'total_listing'=> 0,
            'featured' => 0,
            'premium' => 0,
            'standard' => 0,
        ];
        $bayut = [
            'total_listing'=> 0,
            'signature' => 0,
            'hot' => 0,
            'basic' => 0
        ];
        $dubizzle = [
            'total_listing'=> 0,
            'featured' => 0,
            'premium' => 0,
            'standard' => 0
        ];
        $data = [
            'property_finder' => $property_finder,
            'bayut' => $bayut,
            'dubizzle' => $dubizzle
        ];
        return $data;

    }
    public function getDealSummary($params)
    {


        $start_date = $params['start_date'];
        $end_date = $params['end_date'];


            $result = Http::withHeaders([
                'token' => env('AXDEAL_SECRET_KEY'),
            ])->get('https://deals.axcrm.ae/backend/api/get-deals-sales-value?start_date=' . $start_date . '&end_date=' . $end_date.'&agent_id='.auth()->user()->id);


        $data = $result->json();

        $total_sale_value = $data['total_sale_value'];
        $total_secondary_sales_sale_value = $data['secondary_sales_value'];
        $total_primary_sale_value = $data['primary_sales_value'];
        $total_rental_sale_value = $data['rental_sales_value'];
        $total_other_deals_value = $data['other_sales_value'];

        return [
            'total_sale_value' => $total_sale_value,
            'total_secondary_sales_sale_value' => $total_secondary_sales_sale_value,
            'total_primary_sale_value' => $total_primary_sale_value,
            'total_rental_sale_value' => $total_rental_sale_value,
            'total_other_deals_value'   =>  $total_other_deals_value,
        ];
    }


}
