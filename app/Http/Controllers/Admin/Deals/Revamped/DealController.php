<?php

namespace App\Http\Controllers\Admin\Deals\Revamped;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\DealApproval;
use App\Models\DealCommissionSource;
use App\Models\DealDeductibleType;
use App\Models\DealProgressionData;
use App\Models\User;
use App\Models\Country;
use App\Models\Deal;
use App\Models\DealInvite;
use App\Models\DealLead;
use App\Models\DealMedia;
use App\Models\DealSplitCommission;
use App\Models\Lead;
use App\Models\LeadMedia;
use App\Models\Source;
use App\Models\Medium;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Controller\Api\V1\Deals\DealApiController;

class DealController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies("Deals View"), 401);
        $data = $this->getData();

        $isAlreadyInvited = DealInvite::where('deal_id',$request->deal_id)->where('invited_to_id','!=',null)->first(); //when agent is invited to fill the details
        $data['lead_invited'] = DealInvite::where('deal_id',$request->deal_id)->where('invited_to_id','=',null)->with('invited_by')->first(); //when lead himself is invited to fill the details
        $deal_lead = DealLead::where('deal_id',$request->deal_id)->first();
        $data['isDealInviteLeadCreated'] = false;
        $data['isAlreadyInvited'] = false;
        $data['fromInvitePage'] = false;
        $deal = Deal::whereId($request->deal_id)->with('progression','agent')->first();

        $data['deal'] = $deal ?? null;
        if($deal && ($deal->status == 'COMPLETED' || $deal->status == 'APPROVED')) {
            $data['edit_btn'] = 'd-none';
        }else{
            $data['edit_btn'] = '';
        }

        $data['admin_role'] = \DB::table('roles')->where('name', 'Admin')->first();

        $data['deal_approval'] = DealApproval::where('deal_id',$deal?->id)->where('user_id',auth()->user()->id)->where('approval_status','Pending')->first() ?? null;
        $data['admin_deal_approval'] = DealApproval::where('deal_id',$deal?->id)->where('role_id',$data['admin_role']->id)->where('approval_status','Pending')->first() ?? null;
        $data['deal_sales_progression'] = DealProgressionData::where('deal_id',$deal?->id)->first() ?? null;
        $data['isDealInviteLeadDocumentUploaded'] = false;
        $data['isAllowedToUploadLeadDocuments'] = true;
        $data['isAllowedToUploadDealDocuments'] = true;

        if($isAlreadyInvited){
            $data['isAllowedToUploadLeadDocuments'] = false;
            $data['isAlreadyInvited'] = true;
            if($deal_lead->created_by_id ?? 0 == $isAlreadyInvited->invited_to_id){
                $data['isDealInviteLeadCreated'] = true;
                $data['isAllowedToUploadLeadDocuments'] = auth()->id() == $deal_lead?->created_by_id;
            }

            if(!is_null($deal)){
                $requiredDocumentList = getDealRequiredDocumentList($deal->type);
                foreach($requiredDocumentList as $documentItem){
                    if($documentItem["required"]){
                        $isUploaded = DealMedia::where("title", $documentItem["id"])
                        ->where("deal_id", $deal->id)
                        ->where("created_by_id", $isAlreadyInvited->invited_to_id)
                        ->where("model_type", Lead::class)
                        ->exists();
                        if(!$isUploaded) {
                            $data["isDealInviteLeadDocumentUploaded"] = false;
                            break;
                        }else{
                            $data["isDealInviteLeadDocumentUploaded"] = true;
                        }
                    }
                }
            }
        }

        return view('admin.deals_revamped.index')->with($data);
    }

    public function dealWithInviteLink($invite_code)
    {
        $invite = DealInvite::where('invite_code', $invite_code)->with('deal')->first();
       if(empty($invite)){
           return abort(404);
       }
        if($invite->invited_to_id != auth()->id() || $invite->deal->status != 'DRAFT'){
            return abort(404);
        }

        $data = $this->getData();
        // $data['agents'] = User::where('status', 1)->whereHas('roles', function ($query) {
        //     $notAdminRoles = getNotAdminRoles();
        //     $query->whereIn('name', $notAdminRoles);
        // })->get();
        $data['isDealInviteLeadCreated'] = false;
        $data['isAlreadyInvited'] = false;
        $data['fromInvitePage'] = true;

        $data['isDealInviteLeadDocumentUploaded'] = false;
        $data['isAllowedToUploadLeadDocuments'] = true;
        $data['isAllowedToUploadDealDocuments'] = false;
        $data['edit_btn'] = '';
        $data['deal'] = $invite->deal;

        return view('admin.deals_revamped.index')->with($data);

    }

    protected function getData(){
        $notAdmin = getNotAdmin();
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        $data['agents'] = User::when($notAdmin, function($q) use($childrenIds){
            $q->whereIn('id', $childrenIds);
        })->where('status', 1)->whereHas('roles', function ($query) {
            $notAdminRoles = getNotAdminRoles();
            $query->whereIn('name', $notAdminRoles);
        })->get();


        $data['agents_for_invite'] = User::where('status', 1)->whereHas('roles', function ($query) {
            $notAdminRoles = getNotAdminRoles();
            $query->whereIn('name', $notAdminRoles);
        })->get();


        $data['countries'] = Country::get();
        $data['sources'] = Source::get();
        $data['mediums'] = Medium::get();
       $transaction_type = array_values(\App\Models\Deal::TRANSACTION_TYPE_SELECT);

        $data['fee_types'] = DealDeductibleType::where('status', 1)->whereNotIn('name',$transaction_type)->select('id','name','type','type_value')->get();
        $data['deductible_types'] = DealDeductibleType::where('status', 1)->select('id','name','type','type_value')->get();

        return $data;
    }

    public function showApprovalPage(Request $request) {

        $deal_approval = DealApproval::where('uuid',$request->uuid)->first();
        if(is_null($deal_approval)){ abort(404);}


        $deal = Deal::find($deal_approval->deal_id);
        $agent = User::find($deal_approval->user_id);
        if(is_null($agent)) abort(404);
        $property = Property::find($deal->property_id);
        if(is_null($property)) abort(404);

        $property->load(["bed", "bath", "price"]);


        $deal_split_commissions = DealSplitCommission::with(['split'])->where('deal_id',$deal->id)->where('user_id',$agent->id)->get();

        return view("webview.deals.approval", compact("deal","deal_approval", "agent", "property","deal_split_commissions"));
    }


    public function checkDealBeforeSubmit(Request $request)
    {
        $deal_id = $request->deal_id;
        $leads = DealLead::where('deal_id',$deal_id)->get();
        $deal = Deal::where('id',$deal_id)->first();
        if($deal->type == 'Primary Resale' || $deal->type == 'Secondary Sale'){
            $lead_type = 'Buyer';
        }else{
            $lead_type = 'Tenant';
        }
        $msg = []; $pending_module = [];
        if($leads->count() == 0){
            $msg[$lead_type][] = "Please add at least one $lead_type to submit the deal.";
            $pending_module[] = 'lead';
        }
        if($leads->count() > 0){
            foreach($leads as $lead){
                if($lead->lead_type != 'Primary'){
                    $msg[$lead_type][] = "Please select a primary $lead_type.";
                    $pending_module[] = 'lead';
                    break;
                }
            }
        }
//       $arr =  getDealRequiredDocumentList($deal->type,'Contact');
//        return response()->json(['status' => 'success', 'message' => $arr]);
        $dealCommissionSources = DealCommissionSource::where('deal_id',$deal_id)->count();
        if($dealCommissionSources == 0){
            $msg['Finance'][] = "Please add at least one commission source to submit the deal.";
            $pending_module[] = 'commission';
        }
        $dealSplitCommissions = DealSplitCommission::where('deal_id',$deal_id)->count();
        if($dealSplitCommissions == 0){
            $msg['Finance'][] = "Please add at least one split commission to submit the deal.";
            $pending_module[] = 'commission';
        }

        $contact_doc_lib =  getDealRequiredDocumentList($deal->type,'Contact');
        $contact_req_doc = ''; $contact_documents = 0;
        foreach ($contact_doc_lib as $doc){
            if($doc['required']) {
               if(DealMedia::where('deal_id', $deal_id)->where('model_type', Contact::class)->where('title', $doc['id'])->doesntExist()){
                   $contact_req_doc.= $doc['name'].', ';
                   $contact_documents++;
               }

            }
        }

        if($contact_documents>0){
            $msg['Attachments'][] = "Please upload the <span class='text-danger'>$contact_req_doc</span> documents for contact to submit the deal.";
            $pending_module[] = 'attachments';
        }

        $lead_doc_lib =  getDealRequiredDocumentList($deal->type,'Lead');

        $lead_req_doc = ''; $lead_documents = 0;
        foreach ($lead_doc_lib as $doc){
            if($doc['required']) {
                if(DealMedia::where('deal_id', $deal_id)->where('model_type', Lead::class)->where('title', $doc['id'])->doesntExist()){
                    $lead_req_doc.= $doc['name'].', ';
                    $lead_documents++;
                }

            }
        }
        if($lead_documents>0){
            $msg['Attachments'][] = "Please upload the <span class='text-danger'>$lead_req_doc</span> for $lead_type documents  to submit the deal.";
            $pending_module[] = 'attachments';
        }
        $other_doc_lib =  getDealRequiredDocumentList($deal->type,'Other');

        $other_req_doc = ''; $other_documents = 0;
        foreach ($other_doc_lib as $doc){
            if($doc['required']) {
                if(DealMedia::where('deal_id', $deal_id)->where('model_type', Deal::class)->where('title', $doc['id'])->doesntExist()){
                    $other_req_doc.= $doc['name'].', ';
                    $other_documents++;
                }

            }
        }
        if($other_documents>0){
            $msg['Attachments'][] = "Please upload the <span class='text-danger'>$other_req_doc</span> documents  to submit the deal.";
            $pending_module[] = 'attachments';
        }

        if(count($msg)>0){
            return response()->json(['status' => 'error', 'message' => $msg,'pending_module' => $pending_module]);
        }else{
            return response()->json(['status' => 'success', 'message' => $msg]);
        }

    }

    public function createDealFromBitrixData(Request $request)
    {
        $validatedData = $request->validate([
            "agent_id" => "nullable|exists:users,id",
            "property_id" => "required|exists:properties,id",
            "lead_id" => "required|exists:leads,id",
            "deal_price" => "required"
        ]);
        try{
            $property = Property::find($request->property_id);
            if ($property->for == 'Residential for sale' || $property->for === 'Commercial for sale') {
                if ($property->completion_status === 'Ready Primary' || $property->completion_status === 'Off-Plan Primary') {
                    $type = "Primary Resale";
                } else {
                    $type = "Secondary Sale";
                }
                $property_new_status = 'SOLD';
            } else if ($property->for === 'Residential for rent' || $property->for === 'Commercial for rent') {
                $type = "Rental";
                $property_new_status = 'RENTED';
            } else {
                $type = "";
                $property_new_status = '';
            }

            $deal = new Deal();
            $deal->agent_id = $property->agent_id;
            $deal->property_id = $property->id;
            $deal->contact_id = $property->owner_id;
            $deal->deal_price =   str_replace(",", "", $request->deal_price);
            $deal->type = $type;
            $deal->created_by_id = auth()->id();
            if($deal->save()) {
                $deal->leads()->sync($request->lead_id);
                (new DealApiController)->afterDealCreated($deal);
                return response()->json(['status' => 'success', 'message' => 'Deal created successfully.', 'deal_id' => $deal->id]);
            }
        }catch(\Exception $e){
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }


}
