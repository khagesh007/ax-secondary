<?php

namespace App\Http\Controllers\Admin\Deals;

use App\Models\CommissionType;
use App\Models\Deal;
use App\Models\DealBitrixDump;
use App\Models\Lead;
use App\Models\Property;
use App\Models\PropertyAmenityPortal;
use App\Models\User;
use App\Traits\FileTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PropertyAmenity;
use Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class DealDashboardController extends Controller
{



public function dashboard(Request $request)
    {


        $notAdmin = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }
        $portalWiseCounts = [];

        $data["portalWiseCounts"] = 0;
        $data["total_deals"] = Deal::count();
        $data["propertyForRentCount"] = 12;
        $data["propertyForSaleCount"] = 5;
        $data["completed_deals"] = Deal::where('status', 'Completed')->count();
        $data["in_progress_deals"] = Deal::where('status', '!=','Completed')->where('status', '!=','REJECTED')->count();
        $data["rejected_deals"] = Deal::where('status', 'REJECTED')->count();
        $data['event_types'] = [];
        return view('admin.deals_revamped.dashboard')->with($data);
    }

    public function getDealStatusData(Request $request){
        $agent_id = ($request->has('agent_id') && $request->agent_id != '') ?? '';
        if ($agent_id != '') {
            $user = User::find($agent_id);
        } else {
            $user = auth()->user();
        }

        $notAdmin = $user->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds($user->id);
        }
        $dealStatus = [
            'DRAFT' => 0,
            'PENDING_APPROVAL' => 0,
            'Completed' => 0,
            'REJECTED' => 0,
            'CANCELLED' => 0
        ];
        foreach ($dealStatus as $key => $value) {
            $dealStatus[$key] = Deal::where('status', $key)->where('agent_id',$agent_id)->count();
        }
        $data['deal_status'] = $dealStatus;



        $startMonth = date('Y-m-01'); // Start from the first day of the current month
        $deals6month = [];
        // dd($childrenIds);
        for ($i = 0; $i < 6; $i++) {
            $targetMonth = date('Y-m-01', strtotime("-$i months", strtotime($startMonth)));
            $deals6month['months'][] = date('F', strtotime($targetMonth));
            $deals6month['new_deals'][] = Deal::whereYear('created_at', '=', date('Y', strtotime($targetMonth)))
                ->whereMonth('created_at', '=', date('m', strtotime($targetMonth)))
                ->when($agent_id, function ($query) use ($agent_id) {
                    return $query->where('agent_id', $agent_id);
                })
                ->count() ?? 0;
            $deals6month['completed_deals'][] = Deal::whereYear('created_at', '=', date('Y', strtotime($targetMonth)))
                ->whereMonth('created_at', '=', date('m', strtotime($targetMonth)))
                ->when($agent_id, function ($query) use ($agent_id) {
                    return $query->where('agent_id', $agent_id);
                })
                ->where('status', 'Completed')
                ->count() ?? 0;
            $deals6month['in_progress_deals'][] = Deal::whereYear('created_at', '=', date('Y', strtotime($targetMonth)))
                ->whereMonth('created_at', '=', date('m', strtotime($targetMonth)))
                ->where('status', '!=', 'Completed')
                ->where('status', '!=', 'REJECTED')
                ->when($agent_id, function ($query) use ($agent_id) {
                    return $query->where('agent_id', $agent_id);
                })
                ->count() ?? 0;
            $deals6month['rejected_deals'][] = Deal::whereYear('created_at', '=', date('Y', strtotime($targetMonth)))
                ->whereMonth('created_at', '=', date('m', strtotime($targetMonth)))
                ->where('status', 'REJECTED')
                ->when($agent_id, function ($query) use ($agent_id) {
                    return $query->where('agent_id', $agent_id);
                })
                ->count() ?? 0;
        }

    $data['deals6month'] = $deals6month;
        $result = [
            'status' => 'success',
            'data' => $data
        ];
        return response()->json($result);
    }
    public function agentDealOverview()
    {


        $data['users'] = $users = User::where('bitrix_role', 'like', '%Primary%')->whereHas('roles', function ($q) {
            $q->where('name', 'Agent');
        })->where('status','1')->select('id', 'name')->get();
        $agent_listings = [];
        foreach ($users as $user) {
            $agent_listings[$user->id]['name'] = $user->name;
            $agent_listings[$user->id]['completed'] = Deal::where('agent_id', $user->id)->where('status', 'Completed')->count();
            $agent_listings[$user->id]['in_progress'] = Deal::where('agent_id', $user->id)->where('status', '!=', 'Completed')->where('status', '!=', 'REJECTED')->count();
            $agent_listings[$user->id]['rejected'] = Deal::where('agent_id', $user->id)->where('status', 'REJECTED')->count();
        }
        $data['agent_ax_data'] = $agent_listings;
        $agnet_bitrix_deals = [];
        foreach($users as $user){
            $agnet_bitrix_deals[$user->id]['name'] = $user->name;
            $agnet_bitrix_deals[$user->id]['completed'] = DealBitrixDump::where('agent_id', $user->id)->whereIn('status', ['WON','C2:WON'])->count();
            $agnet_bitrix_deals[$user->id]['in_progress'] = DealBitrixDump::where('agent_id', $user->id)->whereNotIn('status', ['WON','C2:WON','C2:LOSE','LOSE'])->count();
            $agnet_bitrix_deals[$user->id]['rejected'] = DealBitrixDump::where('agent_id', $user->id)->whereIn('status', ['LOSE','C2:LOSE'])->count();

        }
        $data['agent_bitrix_data'] = $agnet_bitrix_deals;

        //Cache::put('dashboard_agents_overview', $data, 30);
        return response()->json(['status' => '1', 'data' => $data, 'data_type' => 'fresh']);
    }
    public function getBitrixDashboardData()
    {
        $data['total_deals'] = DealBitrixDump::count();
        $data['successful_deals'] = DealBitrixDump::where('status', 'WON')->orWhere('status','C2:WON')->count();
        $data['pending_deals'] = DealBitrixDump::where('status', '!=','WON')
            ->where('status','!=','LOSE')
            ->where('status','!=','C2:LOSE')
            ->where('status','!=','C2:WON')
            ->count();
        $data['lost_deals'] = DealBitrixDump::where('status', 'LOSE')->orWhere('status','C2:LOSE')->count();
        $deal_status_array = getBitrixDealStage();
        $deal_status_list = array_keys($deal_status_array);
        $data['deal_status'] = [];
        foreach ($deal_status_list as $list){
            if (array_key_exists($list,$data['deal_status'])){ //check if key exists becuase we have multiple same stages
                if($data['deal_status'][$deal_status_array[$list]] == $deal_status_array[$list]){
                    $data['deal_status'][$deal_status_array[$list]] = $data['deal_status'][$deal_status_array[$list]] + DealBitrixDump::where('status', $list)->count();
                }
            }else{
                $data['deal_status'][$deal_status_array[$list]] = $list;//DealBitrixDump::where('status', $list)->count();
            }

        }

        foreach ($deal_status_array as $key => $value) {
            if(array_key_exists($value,$data['deal_status'])){

                   $data['deal_status'][$value] = (int)$data['deal_status'][$value] + DealBitrixDump::where('status', $key)->count();
            }else{
                $data['deal_status'][$value] = DealBitrixDump::where('status', $key)->count();
            }
        }


        $startMonth = date('Y-m-01'); // Start from the first day of the current month
        $deals6month = [];
        // dd($childrenIds);
        for ($i = 0; $i < 6; $i++) {
            $targetMonth = date('Y-m-01', strtotime("-$i months", strtotime($startMonth)));
            $deals6month['months'][] = date('F', strtotime($targetMonth));
            $deals6month['new_deals'][] = DealBitrixDump::whereYear('deal_created_at', '=', date('Y', strtotime($targetMonth)))
                ->whereMonth('deal_created_at', '=', date('m', strtotime($targetMonth)))
                ->count() ?? 0;
            $deals6month['completed_deals'][] = DealBitrixDump::whereYear('deal_created_at', '=', date('Y', strtotime($targetMonth)))
                ->whereMonth('deal_created_at', '=', date('m', strtotime($targetMonth)))
//                ->where('status', 'Completed')
                ->count() ?? 0;
            $deals6month['in_progress_deals'][] = DealBitrixDump::whereYear('deal_created_at', '=', date('Y', strtotime($targetMonth)))
                ->whereMonth('deal_created_at', '=', date('m', strtotime($targetMonth)))
//                ->where('status', '!=', 'Completed')
//                ->where('status', '!=', 'REJECTED')
                ->count() ?? 0;
            $deals6month['rejected_deals'][] = DealBitrixDump::whereYear('deal_created_at', '=', date('Y', strtotime($targetMonth)))
                ->whereMonth('deal_created_at', '=', date('m', strtotime($targetMonth)))
//                ->where('status', 'REJECTED')
                ->count() ?? 0;
        }

        $data['deals6month'] = $deals6month;

        return response()->json(['status' => '1', 'data' => $data]);
    }

}
