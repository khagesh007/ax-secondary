<?php

namespace App\Http\Controllers\Admin\Deals\Report;

use App\Models\DealBitrixDump;
use App\Models\Deal;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AgentReportController extends Controller
{
    public function summary()
    {
        $agent_id = \request('agent_id');
        try{
        $data['bitrix_total_deals'] = DealBitrixDump::where('agent_id',$agent_id)->count();
        $data['bitrix_completed_deals'] = DealBitrixDump::where('agent_id',$agent_id)->where('status', 'WON')->count();
        $data['bitrix_in_progress_deals'] = DealBitrixDump::where('agent_id',$agent_id)->where('status', '!=','WON')->where('status', '!=','REJECTED')->count();
        $data['bitrix_rejected_deals'] = DealBitrixDump::where('agent_id',$agent_id)->where('status', 'REJECTED')->count();

        $data['axcrm_total_deals'] = Deal::where('agent_id',$agent_id)->count();
        $data['axcrm_completed_deals'] = Deal::where('agent_id',$agent_id)->where('status', 'Completed')->count();
        $data['axcrm_in_progress_deals'] = Deal::where('agent_id',$agent_id)->where('status', '!=','Completed')->where('status', '!=','REJECTED')->count();
        $data['axcrm_rejected_deals'] = Deal::where('agent_id',$agent_id)->where('status', 'REJECTED')->count();
            $result = ['status'=>'1','message'=>'Data fetched successfully','data'=>$data];
    }catch(\Exception $e){
            $result = ['status'=>'0','message'=>'Data not fetched'];
        }
       return response()->json($result);
    }
    public function leadSummary()
    {
        $agent_id = \request('agent_id');
        try{
        $data['total_leads'] = DealBitrixDump::where('agent_id',$agent_id)->where('lead_id','!=',null)->count();
        $data['total_deals'] = DealBitrixDump::where('agent_id',$agent_id)->count();
        $data['deal_with_leads'] = DealBitrixDump::where('agent_id',$agent_id)->where('lead_id','!=',null)->count();
        $data['deal_without_leads'] = DealBitrixDump::where('agent_id',$agent_id)->where('lead_id',null)->count();
        $data['leads_matched'] = DealBitrixDump::where('agent_id',$agent_id)->where('lead_source_matched','1')->count();
        $data['leads_not_matched'] = DealBitrixDump::where('agent_id',$agent_id)->where('lead_source_matched','0')->count();



            $result = ['status'=>'1','message'=>'Data fetched successfully','data'=>$data];
    }catch(\Exception $e){
            $result = ['status'=>'0','message'=>'Data not fetched'];
        }
       return response()->json($result);
    }
    public function recentDeals()
    {
        $agent_id = \request('agent_id');
        try{
            $today = date('Y-m-d');
            $past7days = date('Y-m-d', strtotime('-7 days'));
            $past15days = date('Y-m-d', strtotime('-15 days'));
            $past30days = date('Y-m-d', strtotime('-30 days'));
            $past60days = date('Y-m-d', strtotime('-60 days'));
            $data['bitrix_past_7_days'] = DealBitrixDump::where('agent_id',$agent_id)->whereDate('deal_created_at','>=',$past7days)->count();
            $data['bitrix_past_15_days'] = DealBitrixDump::where('agent_id',$agent_id)->whereDate('deal_created_at','>=',$past15days)->count();
            $data['bitrix_past_30_days'] = DealBitrixDump::where('agent_id',$agent_id)->whereDate('deal_created_at','>=',$past30days)->count();
            $data['bitrix_past_60_days'] = DealBitrixDump::where('agent_id',$agent_id)->whereDate('deal_created_at','>=',$past60days)->count();
            $data['bitrix_todays_deal'] = DealBitrixDump::where('agent_id',$agent_id)->whereDate('deal_created_at',$today)->count();

            $data['axcrm_past_7_days'] = Deal::where('agent_id',$agent_id)->whereDate('created_at','>=',$past7days)->count();
            $data['axcrm_past_15_days'] = Deal::where('agent_id',$agent_id)->whereDate('created_at','>=',$past15days)->count();
            $data['axcrm_past_30_days'] = Deal::where('agent_id',$agent_id)->whereDate('created_at','>=',$past30days)->count();
            $data['axcrm_past_60_days'] = Deal::where('agent_id',$agent_id)->whereDate('created_at','>=',$past60days)->count();
            $data['axcrm_todays_deal'] = Deal::where('agent_id',$agent_id)->whereDate('created_at',$today)->count();

            $result = ['status'=>'1','message'=>'Data fetched successfully','data'=>$data];
    }catch(\Exception $e){
            $result = ['status'=>'0','message'=>'Data not fetched'];
        }
       return response()->json($result);
    }
    public function bitrixDealStatusData()
    {
        $agent_id = \request('agent_id');
        try{

            $pending = ['NEW','C2:NEW'];
            $success = ['WON','C2:WON'];


            $in_progress = ['1','2','3','4','5','6'.'C2:PREPARATION','C2:EXECUTING','C2:PREPAYMENT_INVOICE','C2:STARTED','C2:WORK_IN_PROGRESS','C2:PREPAYMENT_RECEIVED',
        'C2:INVOICE','C2:PARTIALLY_PAID','C2:PAID','C2:RECONCILED',
        'C2:REOPENED','C2:POSTPONED','C2:FINAL_INVOICE','C2:5','C2:1','C2:2','C2:3','C2:4'];
            $rejected = ['LOSE','C2:LOSE','C2:CANCELLED'];
            $status['Pending'] = DealBitrixDump::where('agent_id',$agent_id)->whereIn('status',$pending)->count();
            $status['Successful'] = DealBitrixDump::where('agent_id',$agent_id)->whereIn('status',$success)->count();
            $status['In Progress'] = DealBitrixDump::where('agent_id',$agent_id)->whereIn('status',$in_progress)->count();
            $status['Cancelled/Lost'] = DealBitrixDump::where('agent_id',$agent_id)->whereIn('status',$rejected)->count();
            $data['status'] = $status;
            $result = ['status'=>'1','message'=>'Data fetched successfully','data'=>$data];

    }catch(\Exception $e){
            $result = ['status'=>'0','message'=>'Data not fetched'];
        }
       return response()->json($result);
    }

    public function dataTableBitrix(Request $request)
    {
        $columns = array(
            0 => 'updated_at',
            1 => 'id',

        );

        $notAdmin = auth()->user()->hasRole(["Sales Director","Line Manager", "Agent"]);
        $childrenIds = [];
        if ($notAdmin){
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        $searchKeyWord = htmlspecialchars($request['search']['value']);
        $agent_id = $request['agent_id'];
        $totalData = DealBitrixDump::where('agent_id', $agent_id)->count();
        $totalFiltered = $totalData;

        if (!empty($searchKeyWord)) {
            $query = DealBitrixDump::query();
            $data = $query->where('title', 'LIKE', "%{$searchKeyWord}%")
                ->when($notAdmin, fn($q) => $q->whereIn("agent_id", $childrenIds))
                ->where('agent_id', $agent_id)
                ->with('agent','lead')->get();
        } else {

            $data = DealBitrixDump::orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
                ->offset($request['start'])
                ->limit($request['length'])
                ->where('agent_id', $agent_id)
                ->when($notAdmin, fn($q) => $q->whereIn("agent_id", $childrenIds))
                ->with('agent','lead')
                ->get();
        }

        $json_data = array(
            "draw" => intval($request['draw']),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );

        echo json_encode($json_data);
    }
}
