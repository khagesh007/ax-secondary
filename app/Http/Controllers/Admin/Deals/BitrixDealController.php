<?php

namespace App\Http\Controllers\Admin\Deals;

use App\Models\CommissionType;
use App\Models\DealBitrixDump;
use App\Models\Lead;
use App\Models\Deal;
use App\Models\User;
use App\Models\Source;
use App\Models\PropertyAmenityPortal;
use App\Traits\FileTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PropertyAmenity;
use Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Jobs\GetBitrixDealLead;
class BitrixDealController extends Controller
{
    public function index()
    {

        $data['agents'] = User::whereHas("roles", function ($q) {
            $q->where("name", "Agent");
        })->select('id','first_name','last_name')->where('status',true)->get();


        return view('admin.deals_revamped.bitrix')->with($data);
    }
    public function agentReport()
    {
        $agent_id = \request('agent_id');
        if($agent_id == null){
           //return abort(404);
        }
        $data['agent'] = User::find($agent_id);
        $data['agents'] = User::whereHas("roles", function ($q) {
            $q->whereIn("name", ["Agent"]);
        })->where("status", true)->get();
        $data['total_deals'] = Deal::where('agent_id',$agent_id)->count();
        $data['completed_deals'] = Deal::where('agent_id',$agent_id)->where('status', 'Completed')->count();
        $data['in_progress_deals'] = Deal::where('agent_id',$agent_id)->where('status', '!=','Completed')->where('status', '!=','REJECTED')->count();
        $data['rejected_deals'] = Deal::where('agent_id',$agent_id)->where('status', 'REJECTED')->count();
        return view('admin.deals_revamped.bitrix.agentReport')->with($data);
    }


    private function updatePageNumber($page,$next)
    {
        Cache::put('bitrix_deals_last_page', $page);
        Cache::put('bitrix_deals_next_page', $next);
    }
    public function getFullDealData($id){
        $url = 'https://crm.axcap.ae/rest/1/xuic2qa12r3b1fkj/crm.deal.get?id='.$id;
        $response = Http::get($url);
        if ($response->status() != 200) {
            return '';
        } else {
            $response = $response->json();
            $result = $response['result'];
            return $result;
        }
    }
    public function getFullLeadData($id){

        $url = 'https://crm.axcap.ae/rest/1/xuic2qa12r3b1fkj/crm.lead.get?id='.$id;
        $response = Http::get($url);
        if ($response->status() != 200) {
            return '';
        } else {
            $response = $response->json();
            $result = $response['result'];
            return $result;
        }
    }
    public function getLeadMatch(){
        //this is a test function for GetBitrixDealLead job file
        $deal = DealBitrixDump::find(61);
        $lead = $this->getFullLeadData('154103');


        $lead_source = findSourceForBitrix($lead['SOURCE_ID']);

        $mobile = ''; $email = ''; $lead_source_matched = 0;
        if($lead['HAS_PHONE'] == 'Y'){
           $mobile = $lead['PHONE'][0]['VALUE'];
        }
        if($lead['HAS_EMAIL'] == 'Y'){
            $email = $lead['EMAIL'][0]['VALUE'];
        }
        $ax_lead = Lead::where('mobile',$mobile)
        ->when($deal->agent_id, function ($query) use ($deal) {
            return $query->where('agent_id', $deal->agent_id);
        })->where(function($q) use ($email,$mobile){
            $q->where('mobile',$mobile)->orWhere('email',$email);
        })->first();


        if($ax_lead == null){

        $source = Source::where('id',$ax_lead->source_id)->first();
        if($source->name == $lead_source){
            $lead_source_matched = 1;
        }elseif($source->name == $lead['SOURCE_ID']){
            $lead_source_matched = 1;
        }else{
            $lead_source_matched = 0;
        }
    }
         $deal->deal_leads = $lead;
         $deal->lead_source_matched = $lead_source_matched;
         $deal->save();

    }

    public function fetchDeals()
    {

         if (Cache::has('bitrix_deals_next_page')) {
             $current_page = Cache::get('bitrix_deals_next_page');
         } else if (Cache::has('bitrix_deals_last_page')) {
             $current_page = Cache::get('bitrix_deals_last_page');
         } else {
             $current_page = 1;
         }

         try {
//        $current_page = 13800;
             $url = 'https://crm.axcap.ae/rest/1/xuic2qa12r3b1fkj/crm.deal.list?start=' . $current_page;
             $response = Http::get($url);
             if ($response->status() != 200) {
                 return response()->json(['error' => 'Error fetching deals'], 500);
             } else {

                 $response = $response->json();

                 $next_page = $response['next'];
                 $deal_data = $response['result'];
                 if (gettype($deal_data) == 'array' && count($deal_data) > 0) {
                     foreach ($deal_data as $deal) {
                         if (!DealBitrixDump::where('bitrix_deal_id', $deal['ID'])->exists()) {
                             $dump = new DealBitrixDump();
                             $dump->bitrix_deal_id = $deal['ID'];
                             $dump->deal_data = json_encode($deal);
                             $dump->bitrix_lead_id = $deal['LEAD_ID'];
                             $dump->lead_id = $deal['LEAD_ID'] != null ? (Lead::where('bitrix_id', $deal['LEAD_ID'])->first()->id ?? null) : null;
                             $dump->agent_id = User::where('bitrix_id', $deal['ASSIGNED_BY_ID'])->first()->id ?? null;
                             //    $dump->deal_leads = $deal['LEADS'];
                             $dump->is_used = false;
                             $dump->status = $deal['STAGE_ID'];
                             $dump->deal_created_at = $deal['DATE_CREATE'];
                             $dump->save();
                             if($deal['LEAD_ID'] != null){
                                GetBitrixDealLead::dispatch($dump->id, $deal['LEAD_ID'])->onConnection('redis')->onQueue('high');
                             }

                         }
                     }
                 }


                 $this->updatePageNumber($current_page, $next_page);

             }
         }catch (\Exception $e){
           echo $e->getMessage();
         }
    }
    public function applyDealFilters($query, $request)
    {   $created_from = $request->created_from;
        $created_to = $request->created_to;
        $query->when($request->statuses, function ($q) use ($request) {
            return  is_array($request->statuses) ? $q->whereIn('status', $request->statuses) : $q->where('status', $request->statuses);
        });
        $query->when($request->agents, function ($q) use ($request) {
            return  is_array($request->agents) ? $q->whereIn('agent_id', $request->agents) : $q->where('agent_id', $request->agents);
        });
        $query->when($request->deal_sources, function ($q) use ($request) {
            return  is_array($request->deal_sources) ? $q->whereIn('deal_data->SOURCE_ID', $request->deal_sources) : $q->where('deal_data->SOURCE_ID', $request->deal_sources);
        });
        // $query->when($request->statuses, function ($q) use ($request) {
        //     return $q->where('status', $request->statuses);
        // });
        $query->when($request->leads, function ($q) use ($request) {
            return  is_array($request->leads) ? $q->whereIn('lead_id', $request->leads) : $q->where('lead_id', $request->leads);
        });
        $query->when($request->created_from, function ($q) use ($created_from) {
            return $q->whereDate('deal_created_at', '>=', $created_from);
        });
        $query->when($request->created_to, function ($q) use ($created_to) {
            return $q->whereDate('deal_created_at', '<=', $created_to);
        });
        return $query;
    }
    public function dataTable(Request $request)
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

        $qry = DealBitrixDump::query();
        $totalData = $this->applyDealFilters($qry, $request)->count();
        $totalFiltered = $totalData;

        if (!empty($searchKeyWord)) {
            $query = DealBitrixDump::query();
            $data = $this->applyDealFilters($query, $request)
                ->when($notAdmin, fn($q) => $q->whereIn("agent_id", $childrenIds))
                ->where(function($q) use ($searchKeyWord){
                    $q->orWhere('deal_data->LEAD_ID', 'LIKE', "%{$searchKeyWord}%")
                    ->orWhere('id', 'LIKE', "%{$searchKeyWord}%")
                    ->orWhere('deal_data->SOURCE_ID', 'LIKE', "%{$searchKeyWord}%");
                })
                ->with('agent','lead')->get();
        } else {
            $query = DealBitrixDump::query();
            $data = $this->applyDealFilters($query, $request)->orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
                ->offset($request['start'])
                ->limit($request['length'])
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

    public function getBitrixDeal(Request $request)
    {
        $id = $request->id;
        $deal = DealBitrixDump::find($id);

        if ($deal) {

            $data['deal'] = '';
            //$deal_data = json_decode($deal->deal_data, true);
            $deal_data = $this->getFullDealData($deal->bitrix_deal_id);

            $deal->deal_data = json_encode($deal_data);
            if($deal->deal_leads == null && $deal_data['LEAD_ID'] != null){
                $lead_data = $this->getFullLeadData($deal_data['LEAD_ID']);
                $deal->deal_leads = json_encode($lead_data);
            }

            $deal->save();
            $data['bitrix_deal'] = $deal;
            $data['bitrix_lead'] = json_encode($deal->deal_leads);
            $data['lead'] =  $deal_data['LEAD_ID'] !=null ? Lead::where('bitrix_id', $deal_data['LEAD_ID'])->first() : null;
            if(empty($data['lead']) && $deal->lead_id != null){
                $data['lead'] = Lead::where('id', $deal->lead_id)->first();
            }
            $data['agent'] = $deal_data['ASSIGNED_BY_ID'] !=null ? User::where('bitrix_id', $deal_data['ASSIGNED_BY_ID'])->first() : null;
        }else{
            return response()->json(['status'=>0, 'message'=>'Deal not found']);
        }
        $result = ['status'=>1, 'data'=>$data];
        return response()->json($result);
    }


}
