<?php

namespace App\Http\Controllers\Admin\Kpi;

use App\Http\Controllers\Controller;
use App\Models\KpiTarget;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Request;

class KpiDashboardController extends Controller
{
    public function index()
    {
        return view('admin.kpi_v2.dashboard');
    }

    public function getTargetReport(Request $request)
    {
        $month = $request->month ?? date('Y-m');
        $agent_ids = KpiTarget::where('start_date', 'like', $month . '%')->pluck('agent_id')->unique()->toArray();
        $agents = User::whereIn('id', $agent_ids)->get();
        $data = [];
        foreach ($agents as $agent) {
            $data[$agent->id] = [
                'agent' => $agent,
                'targets' => KpiTarget::where('agent_id', $agent->id)->where('start_date', 'like', $month . '%')->with('result')->get(),
            ];
        }
        $result = [
            'status' => '1',
            'data' => $data,
        ];
        return response()->json($result);
    }

    public function agentReportDetails($id)
    {

        $data['agent'] = User::where('id', $id)->first();
        return view('admin.kpi_v2.agentReport')->with($data);
    }

    public function getAgentTargetReport(Request $request)
    {
        $period = $request->month ?? date('Y-m');
        $agent_id = $request->agent_id;
        // $agent_ids = KpiTarget::where('start_date', 'like', $month . '%')->pluck('agent_id')->unique()->toArray();
        $data = [];
        $startDate = \Carbon\Carbon::createFromFormat("Y-m", $period)->startOfMonth()->format('Y-m-d');
        $endDate = \Carbon\Carbon::createFromFormat("Y-m", $period)->endOfMonth()->format('Y-m-d');
        $year = substr($period, 0, 4);
        $monthNum = substr($period, 5, 2);
        $data['agent'] = User::where('id', $agent_id)->first();
        $data['targets'] = KpiTarget::where('agent_id', $agent_id)->where('start_date', 'like', $period . '%')->with('result')->get();
//        $counts['listing_count'] =   $listings = Property::whereDate('created_at', '>=', $startDate)
//        ->whereDate('created_at', '<=', $endDate)
//        ->where('agent_id', $agent_id)
//        ->count();
//        $counts['listing_republished'] = Property::whereYear('republished_date', $year)
//                           ->whereMonth('republished_date', $monthNum)
//        ->where('agent_id', $agent_id)->count();
//        $counts['listing_pocket'] = Property::whereYear('created_at', $year)
//        ->whereMonth('created_at', $monthNum)
// ->where('agent_id', $agent_id)->count();
//         $data['count'] = $counts;
        $result = [
            'status' => '1',
            'data' => $data,
        ];
        return response()->json($result);
    }
    public function getAgentLiveReportData(Request $request)
    {
        $period = $request->month ?? date('Y-m');
        $agent_id = $request->agent_id;
        $module = $request->module ?? 'listing';
        $data = [];
        $startDate = \Carbon\Carbon::createFromFormat("Y-m", $period)->startOfMonth()->format('Y-m-d');
        $endDate = \Carbon\Carbon::createFromFormat("Y-m", $period)->endOfMonth()->format('Y-m-d');
        $year = substr($period, 0, 4);
        $monthNum = substr($period, 5, 2);

        if($module == 'listing'){
        $data['New Published'] = $listings = Property::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)->where('publish_status', 'PUBLISHED')
            ->where('agent_id', $agent_id)
            ->count();
        $data['Relisted'] = Property::whereYear('republished_date', $year)
            ->whereMonth('republished_date', $monthNum)
            ->where('agent_id', $agent_id)->count();
        $data['Pocket'] = Property::whereYear('created_at', $year)->where('publish_status', 'POCKET')
            ->whereMonth('created_at', $monthNum)->where('agent_id', $agent_id)->count();
        $data['Month Total'] = $listings = Property::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->where('agent_id', $agent_id)
            ->count();
        }else  if($module == 'calls'){
            $data['AX Contacts Calls'] = rand(1000, 10000);
            $data['AX Contacts WhatsApp Messages'] = rand(1000, 10000);
            $data['Dialed Calls'] = rand(1000, 10000);
            $data['Received Calls'] = rand(1000, 10000);
            $data['Answer Rate'] = rand(1000, 10000);
            $data['Missed Calls'] = rand(1000, 10000);
        }else  if($module == 'viewings'){
            $data['Number of viewings arranged'] = 0;
        }else  if($module == 'offers'){
            $data['Offers Received'] = rand(1000, 10000);
            $data['Offers Received'] = rand(1000, 10000);
        }else  if($module == 'listing_leads'){
            $data['Listing Leads'] = rand(1000, 10000);
        }else  if($module == 'deals'){
            $data['Deals Closed'] = rand(1000, 10000);
            $data['Total Deals'] = rand(1000, 10000);
            $data['Deal Values'] = rand(1000, 10000);
            $data['Rental Deals'] = rand(1000, 10000);
            $data['Sales Deals'] = rand(1000, 10000);
            $data['Gross revenue'] = rand(1000, 10000);
            $data['Average Rental Commission'] = rand(1000, 10000);
            $data['Average Sales Commission'] = rand(1000, 10000);
            $data['Average Rental Revenue'] = rand(1000, 10000);
            $data['Average Sales Revenue'] = rand(1000, 10000);
        }
        $result = [
            'status' => '1',
            'data' => $data,
        ];
        return response()->json($result);
    }

}
