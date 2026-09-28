<?php

namespace App\Http\Controllers\Admin\Deals;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;
use App\Models\Deal;
use App\Models\DealCommissionSource;
use App\Models\DealProgressionData;
use App\Models\User;
use App\Models\CommissionSlab;
use App\Models\DealSplit;
use App\Models\DealSplitCommission;
use App\Models\DealSplitReferral;
use App\Models\Property;
use App\Models\DealInvite;
use App\Models\Lead;
use App\Events\SalesProgressionAssigned;
use App\Events\SalesProgressionChanged;
use App\Mail\DealLeadInviteKYCMail;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Api\V1\Deals\DealApiController;
use App\Traits\DocusignTrait;
class DealV2Controller extends Controller
{
    use DocusignTrait;
    public function addDealCommission(Request $request)
    {
        $request->validate([
            'deal_id' => 'required|exists:deals,id',
            'deal_price' => 'required|string',
            'commission_amount' => 'required',
            'conveyancer_id' => 'nullable|exists:users,id',
            'transaction_type' => 'nullable|string',
            'deposit_amount' => 'nullable|string',
            'vat' => 'nullable',
        ]);
        try {
            $deal = Deal::find($request->deal_id);
            $deal->deal_price = (int)str_replace(',', '', $request->deal_price);
            $deal->gross_commission = (double)str_replace(',', '', $request->gross_commission);
            $deal->net_commission = (double)str_replace(',', '', $request->net_commission);
            $deal->initial_deposit = (double)str_replace(',', '', $request->deposit_amount) ?? 0;
            $deal->vat = (double)str_replace(',', '', $request->vat) ?? 0;
            $deal->save();



                $dealCommission = DealCommissionSource::where('deal_id',$request->deal_id)->first() ?? new DealCommissionSource();
                $dealCommission->deal_id = $request->deal_id;
                $dealCommission->commission_amount = (int)str_replace(',', '', $request->commission_amount);
                $dealCommission->commission_percentage = $request->commission_percentage ?? null;
                $dealCommission->commission_source = null;
                // $dealCommission->is_vat_applicable = $request->vat[$i] > 0 ? 1 : 0;
                // $dealCommission->vat_amount = $request->vat[$i] ?? null;
                // $dealCommission->vat_percentage = 5;
                $dealCommission->sales_progression_amount = (float)str_replace(',', '', $request->progression_fee) ?? null;
                $dealCommission->created_by_id = auth()->user()->id ?? null;
                $dealCommission->save();


            if ($request->conveyancer_id != null && $request->conveyancer_id != '') {

                $dpd = DealProgressionData::where('deal_id', $request->deal_id)->first() ?? new DealProgressionData();
                $old_person = $dpd->conveyancer_id ?? null;
                $dpd->deal_id = $request->deal_id;
                $dpd->conveyancer_id = $request->conveyancer_id;
                $dpd->transaction_type = $request->transaction_type;
                if($old_person != null && $old_person != $request->conveyancer_id){
                    $dpd->conveyancer_selection_mode = 'Manual';
                }
                $dpd->conveyancer_selection_mode = 'Manual';
                $dpd->progression_amount = (float)str_replace(',', '', $request->progression_fee) ?? null;
                if($dpd->save()){
                    if($old_person != null && $old_person != $request->conveyancer_id){
                       event(new SalesProgressionChanged($deal, $dpd,$old_person));
                    }else{

                    }
                }


            }else{

            }
            (new DealApiController)->refreshSplitCommissions($deal);
            $data = DealCommissionSource::where('deal_id', $deal->id)->get() ?? [];
            return response()->json(['status' => '1', 'msg' => 'Deal Commission Updated Successfully', 'data' => $data]);
        } catch (Exception $e) {
            return response()->json(['status' => '0', 'msg' => 'Meta Not Created']);
        }
    }
    public function calculateIndirectCommission($user, $role,$commission_category)
    {


        $parentCommission = CommissionSlab::whereHas('commissionSlabUsers', function ($q) use ($user) {
            $q->where('user_id', $user);
        })->where('commission_category', 'Indirect')->first();

        if (empty($parentCommission)) {
            if ($role) {
                $parentCommission = CommissionSlab::whereHas('commissionSlabUsers', function ($q) use ($role) {
                    $q->where('role_id', $role->id);
                })->where('commission_category', 'Indirect')->first();
            }
        }

        if ($parentCommission) {

            $data['commission_type'] = $parentCommission->commission_type;
            $data['calculation_from'] = $parentCommission->calculation_from;
            $data['commission_value'] = $parentCommission->commission_value;


            // if ($parentCommission->commission_type === 'Percentage' && $parentCommission->calculation_from === 'Net Commission') {
            //     $commission['commission'] = $netCommission * ($parentCommission->commission_value / 100);
            //     $remainingAmount -= $commission['commission'];
            //     $commission['remainingAmount'] = $remainingAmount;

            // } elseif ($parentCommission->commission_type === 'Percentage' && $parentCommission->calculation_from === 'Remaining Amount') {
            //     $commission['commission'] = $remainingAmount * ($parentCommission->commission_value / 100);
            //     $commission['remainingAmount'] = $remainingAmount;
            // } elseif ($parentCommission->commission_type === 'Fixed' && $parentCommission->calculation_from === 'Net Commission') {
            //     $commission['commission'] = $parentCommission->commission_value;
            //     $remainingAmount -= $parentCommission->commission_value;
            //     $commission['remainingAmount'] = $remainingAmount;
            // } elseif ($parentCommission->commission_type === 'Fixed' && $parentCommission->calculation_from === 'Remaining Amount') {
            //     $commission['commission'] = $parentCommission->commission_value;
            //     $commission['remainingAmount'] = $remainingAmount;
            // }
            // $commission['commission_type'] = $parentCommission->commission_type;
            // $commission['commission_value'] = $parentCommission->commission_value;
            // $commission['remainingAmount'] = $remainingAmount;
        }else{
            $data['commission_type'] = null;
            $data['calculation_from'] = null;
            $data['commission_value'] = null;
        }

        return $data;
    }

    public function getParentUsers(Request $request){
        $user_id = $request->user_id;
        $parentHierarchy = getUserParentOnly($user_id);
        $data = [];
        $deal = Deal::find($request->deal_id);
        $netCommission = $deal->net_commission;
        $agent = User::with('parent')->find($user_id);

        $commissions = [];
        $type = "Direct";
        $agentCommission = CommissionSlab::whereHas('commissionSlabUsers', function ($q) use ($agent) {
            $q->where('user_id', $agent->id);
        })->where('commission_category', $type)->first();
        if (!$agentCommission) {
            $role = $agent->roles->first();
            $agentCommission = CommissionSlab::whereHas('commissionSlabUsers', function ($q) use ($role) {
                $q->where('role_id', $role->id);
            })->where('commission_category', $type)->first();
            // Calculating Commission Here
            if ($agentCommission?->commission_type === 'Percentage') {
                $commissionVal = $netCommission * ($agentCommission->commission_value / 100); // Convert percentage to decimal
            } elseif ($agentCommission?->commission_type === 'Fixed') {
                $commissionVal = $agentCommission->commission_value; // Use the fixed value
            } else {
                // Handle other types of commission structures if needed
                $commissionVal = 0; // Default to zero if the type is not recognized
            }
        }
        $agent_commission_slab = ['commission_type' =>
        $agentCommission->commission_type,
        'calculation_from' => $agentCommission->calculation_from,
        'commission_type' => $agentCommission?->commission_type,
         'commission_value' => $agentCommission->commission_value
         ];

        $data[0]['user'] = $agent;
        $data[0]['commission_slab'] = $agent_commission_slab;
        $i = 1;
        foreach ($parentHierarchy as $parent) {
            $data[$i]['user'] = $parentUser = User::find($parent);
            $parentRole = $parentUser->roles->first();
            $data[$i]['commission_slab'] =  $this->calculateIndirectCommission($parent, $parentRole,'Indirect');
            $i++;
        }
      //  $users = User::whereIn('id',$parentHierarchy)->get();
        return response()->json(['status' => '1', 'msg' => 'Data fetched successfully', 'data' => $data]);
    }

    public function getDealFinancialData(Request $request){
        $deal_id = $request->deal_id;
        $deal = Deal::find($deal_id);
        $data['dealCommission'] = DealCommissionSource::where('deal_id',$deal_id)->first();
        $data['dealProgression'] = DealProgressionData::where('deal_id',$deal_id)->first();
        $data['deal_commission_source'] = DealCommissionSource::where('deal_id',$deal_id)->first();
        return response()->json(['status' => '1', 'msg' => 'Data fetched successfully', 'data' => $data]);
    }

    public function saveDealSplit(Request $request){
        $request->validate([
            'id' => 'nullable|exists:deal_splits,id',
            'deal_id' => 'required|exists:deals,id',
            'agent_id' => 'required|exists:users,id',
          //  'commission' => 'required',
            'split_amount' => 'required',
            'split_percentage' => 'required',
        ]);

        $deal = Deal::find($request->deal_id);
        $deal_splits = DealSplit::find($request->id) ?? new DealSplit();
        $deal_splits->deal_id = $request->deal_id;
        $deal_splits->split_amount = str_replace(',', '', $request->split_amount);
        $deal_splits->user_id   = $request->agent_id;
        $deal_splits->split_percentage = $request->split_percentage;
        if($request->id == null){
            $deal_splits->created_by_id = auth()->user()->id;
        }else{
        $deal_splits->updated_by_id = auth()->user()->id;
        }
        if($deal_splits->save()){
           foreach($request->user_id ?? [] as $key => $user){
            DealSplitCommission::updateOrCreate([
                'deal_id' => $request->deal_id,
                'deal_split_id' => $deal_splits->id,

                'user_id' => $user,

            ], ['updated_by_id' => auth()->user()->id,
            'commission_value' => $request->commission_percentage[$key],
            'commission_type' => 'Percentage',
            'commission_category' => $deal->agent_id == $user ? 'Direct'  : 'Indirect',
            'commission_from' => $request->user_commission_from[$key],
            'commission' => str_replace(',', '', $request->commission_amount[$key]),]);
           }

           if($request->referral_agent_id != null && $request->referral_agent_id != ''){
            DealSplitReferral::where('deal_id',$request->deal_id)->where('deal_split_id',$deal_splits->id)->delete();
            $referral = DealSplitReferral::updateOrCreate([
                'deal_id' => $request->deal_id,
                'deal_split_id' => $deal_splits->id,
                'user_id' => $request->referral_agent_id
            ], ['referral_amount' => str_replace(',', '', $request->referral_amount),'updated_by_id' => auth()->user()->id,]);
           }

        }
        if($request->id == null || $request->id == ''){
            (new DealApiController)->refreshSplitCommissions($deal);
        }



        return response()->json(['status' => '1', 'msg' => 'Deal Split Updated Successfully']);
    }

    public function getDealReferral(Request $request){
        $deal_id = $request->deal_id;

        $data = DealSplitReferral::where('deal_id',$deal_id)->with('user')->get();
        return response()->json(['status' => '1', 'msg' => 'Data fetched successfully', 'data' => $data]);
    }

    public function getPropertyDetails(Request $request){
        $deal_id = $request->deal_id;
        $deal = Deal::find($deal_id);
        if($deal->property_id != null){
            $data = Property::where('id',$deal->property_id)->with('bed','bath')->first();
            return response()->json(['status' => '1', 'msg' => 'Data fetched successfully', 'data' => $data]);
        }else{
            $data = [];
            return response()->json(['status' => '0', 'msg' => 'No Property Found', 'data' => $data]);
        }

    }
    public function inviteLead(Request $request){
        $request->validate([
            'deal_id' => 'required|exists:deals,id',
            'lead_id' => 'required|exists:leads,id',
        ]);
            if(DealInvite::where('deal_id',$request->deal_id)->where('lead_id',$request->lead_id)->where('invited_to_id','=',null)->exists()){
                return response()->json(['status' => '0', 'msg' => 'Lead Already Invited']);
            }else{
                $lead = Lead::find($request->lead_id);
                if($lead->email == null || $lead->email == ''){
                    return response()->json(['status' => '0', 'msg' => 'Please add email to the lead first']);
                }


                $invite = new DealInvite();
                $invite->deal_id = $request->deal_id;
                $invite->lead_id = $request->lead_id;
                $invite->invited_by_id = auth()->user()->id;
                $invite->invite_code = \Str::random(15);
                $invite->created_at = Carbon::now();
                $invite->last_reminded = Carbon::now();
                if($invite->save()){
                    $deal = Deal::find($request->deal_id);
                    $invite->load('invited_by');
                    $this->sendInvitationDocusign($deal,$lead,$invite);
                  //  Mail::to($lead->email)->send(new DealLeadInviteKYCMail($invite, $lead, 'Lead Invite', 'Lead Invite'));
                    return response()->json(['status' => '1', 'msg' => 'Lead Invited Successfully', 'data' => $invite]);
                }else{
                    return response()->json(['status' => '0', 'msg' => 'Something went wrong!!']);
                }
            }
        }
}
