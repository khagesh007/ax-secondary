<?php

namespace App\Http\Controllers\Api\V1\Deals;

use App\Events\DealApproved;
use App\Events\DealCompleted;
use App\Events\DealNoteAdded;
use App\Events\DealRejected;
use App\Events\DealResubmitted;
use App\Events\DealSubmittedForApproval;
use App\Events\SalesProgressionAssigned;
use App\Events\SalesProgressionChanged;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Contact;
use App\Models\ContactMedia;
use App\Models\ContactMeta;
use App\Models\Deal;
use App\Models\DealDeductibleType;
use App\Models\DealLead;
use App\Models\DealApproval;
use App\Models\DealMedia;
use App\Models\DealMeta;
use App\Models\DealProgressionData;
use App\Models\DealSplit;
use App\Models\DealTask;
use App\Models\CommissionSlab;
use App\Models\CommissionSlabUser;
use App\Models\DealNote;
use App\Models\DealSource;
use App\Models\DealStatus;
use App\Models\Lead;
use App\Models\LeadMedia;
use App\Models\DealSplitCommission;
use App\Models\LeadMeta;
use App\Models\DealSubStatus;
use App\Models\DealType;
use App\Models\FinanceType;
use App\Models\LeadSource;
use App\Models\Medium;
use App\Models\Note;
use App\Models\Property;
use App\Models\PropertyMedia;
use App\Models\TransactionType;
use App\Models\User;
use App\Traits\FileTrait;
use App\Traits\FiltersTrait;
use App\Traits\TelegramTrait;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use DB;
use Spatie\Permission\Contracts\Role;
use validator;
use App\Models\DealDeductible;
use App\Models\DealCommissionSource;
use App\Models\LeadNote;
use Illuminate\Support\Facades\Log;

class DealApiControllerBackup extends Controller
{
    use FiltersTrait, FileTrait, TelegramTrait;


    public function getDealCreateForm(Request $request)
    {
        return view('admin/deals/include/createForm', get_defined_vars())->render();
    }

    public function getDeal(Request $request)
    {
        $deal = Deal::where('id', $request->id)->with('dealType', 'dealStatus', 'agent', 'property', 'property.price', 'property.photos', 'contact', 'createdBy')->first();
        if (!empty($deal)) {
            return response()->json(['status' => '1', 'msg' => 'Deal Found', 'data' => $deal]);
        } else {
            return response()->json(['status' => '0', 'msg' => 'Deal Not Found']);
        }
    }

    public function getCommissionUsersProcess($deal_id){
        $split = DealSplit::where('deal_id', $deal_id)->pluck('id');

        $commissionUsers = DealSplitCommission::whereIn('deal_split_id', $split)->with('user')->get();

//        // Extract only the user information
//        $users = $commissionUsers->map(function ($commissionUser) {
//            return $commissionUser->user;
//        });


        $users = $commissionUsers->map(function ($commissionUser) {
            $user = $commissionUser->user;

            $dealApproval = DealApproval::where('deal_id', $commissionUser->deal_id)->where('user_id', $commissionUser->user_id)->latest()->first();
            $user->deal_approval = $dealApproval; // Associate deal approval with the user
            return $user;
        });
        return $users;
    }
    public function getCommissionUsers(Request $request)
    {


        $request->validate([
            'deal_id' => 'required|exists:deals,id',
        ]);

       $users =  getCommissionUsersProcess($request->deal_id);


        return response()->json(['status' => '1', 'msg' => 'Commission Users Found', 'data' => $users]);
    }
private function handleDealProgression($deal, $request, $action = 'create',$deal_progress_data = null)
{
       if($action == 'create') {

           $new_request = new Request();
           $new_request->transaction_type = $request->transaction_type;
           $res = $this->getSalesProgressions($new_request);
           $res = $res->getData();
           if ($res->status == '1') {
               $sales_user_id = null;
               foreach ($res->data ?? [] as $sales_user) {
                   if ($sales_user->selected == true) {
                       $sales_user_id = $sales_user->id;
                   } else {
                       continue;
                   }
               }
               if ($sales_user_id == null && count($res->data) > 0) {
                   $sales_user_id = $res->data[0]->id;
               }
               if ($sales_user_id != null) {

                   $deal_progress_data = new DealProgressionData();
                   $deal_progress_data->deal_id = $deal->id;
                   $deal_progress_data->transaction_type = $request->transaction_type;
                   $deal_progress_data->conveyancer_selection_mode = 'Auto';
                   $deal_progress_data->conveyancer_id = $sales_user_id;
                   if ($deal_progress_data->save()) {
                       event(new SalesProgressionAssigned($deal, $deal_progress_data));
                   }
               }

           }
       }else{ //updating existing deal progression
           $new_request = new Request();
           $new_request->transaction_type = $request->transaction_type;
           $res = $this->getSalesProgressions($new_request);
           $res = $res->getData();
           if ($res->status == '1') {
               $sales_user_id = null;
               foreach ($res->data ?? [] as $sales_user) {
                   if ($sales_user->selected == true) {
                       $sales_user_id = $sales_user->id;
                   } else {
                       continue;
                   }
               }
               if ($sales_user_id == null && count($res->data) > 0) {
                   $sales_user_id = $res->data[0]->id;
               }
               if ($sales_user_id != null) {
                $old_conveyancer_id = $deal_progress_data->conveyancer_id;
                   if($deal_progress_data->conveyancer_id != $sales_user_id){
                        //handle sales person change and inform old sales person
                       // note: transaction type is not changed
                       $deal_progress_data->deal_id = $deal->id;
                       $deal_progress_data->transaction_type = $request->transaction_type;
                       $deal_progress_data->conveyancer_selection_mode = 'Auto';
                       $deal_progress_data->conveyancer_id = $sales_user_id;
                       if ($deal_progress_data->save()) {
                           event(new SalesProgressionAssigned($deal, $deal_progress_data));
                           event(new SalesProgressionChanged($deal, $deal_progress_data,$old_conveyancer_id));
                       }
                   }else{
                          //only transaction type is changed
                        $old_transaction_type = $deal_progress_data->transaction_type;
                       $deal_progress_data->transaction_type = $request->transaction_type;
                       $deal_progress_data->conveyancer_selection_mode = 'Auto';
                       $deal_progress_data->save();
                       //inform user via telegram that sales progression type is changed
                       $agent_message = "Deal #".$deal->id." transaction type is changed from <b>".$old_transaction_type."</b> to <b>".$request->transaction_type."</b>";
                       $agent_message .= "

AX Secondary CRM
=================================
        ";
                         $user = User::where('id',$deal_progress_data->conveyancer_id)->first();
                       $testing_chat_id = dealTestingChatId();
                       $chat_id = $testing_chat_id != null ? $testing_chat_id : $user->telegram_chatid;
                       $this->sendTelegramMessageHtml($agent_message, [$chat_id]);
                   }


               }

           }
       }

}

    public function storeDeal(Request $request)
    {
        $request->validate([
            'agent_id' => 'required|exists:users,id',
            'property_id' => 'required|exists:properties,id',
            'transaction_type' => 'nullable|string',
        ]);


        $property = Property::find($request->property_id);
        if ($property->for == 'Residential for sale' || $property->for === 'Commercial for sale') {
            if ($property->completion_status === 'Ready Primary' || $property->completion_status === 'Off-Plan Primary') {
                $type = "Primary Resale";
            } else {
                $type = "Secondary Sale";
            }
        } else if ($property->for === 'Residential for rent' || $property->for === 'Commercial for rent') {
            $type = "Rental";
        }

        if ($request->deal_id) {
            $deal = Deal::find($request->deal_id);
            $message = "Deal Updated Successfully";
        } else {
            $deal = new Deal();
            $message = "Deal Created Successfully";
            $deal->status_message = "Deal Initalized";
        }

        $deal->agent_id = $request->agent_id;
        $deal->contact_id = $property->owner_id;
        $deal->property_id = $request->property_id;
        $deal->state = 'owner';
        $deal->type = $type;
        $deal->deal_price = $property->price->price;
        $deal->created_by_id = auth()->user()->id ?? null;
        if ($deal->save()) {

            $dealMeta = new DealMeta();
            $dealMeta->deal_id = $deal->id;
            $dealMeta->meta_key = 'listing_price';
            $dealMeta->meta_value = $property->price->price;
            $dealMeta->save();


            // For View And Edit Mode (Property)
            $dealMeta2 = new DealMeta();
            $dealMeta2->deal_id     = $deal->id;
            $dealMeta2->meta_key    = 'property_mode';
            $dealMeta2->meta_value  = $request->property_mode;
            $dealMeta2->save();
            // For View And Edit Mode (Property)

            if($request->transaction_type !='') { //if transaction type is not empty
                $deal_progress_data = DealProgressionData::where('deal_id', $deal->id)->first();
                if($request->deal_id !='' && $deal_progress_data !=null){ //user is updating the deal and deal progression data is not null
                    if($deal_progress_data->transaction_type == $request->transaction_type){ //if transaction type is same as previous
                        //nothing to change

                    }else{ //transaction type is changed needs to check for new sales progression and inform old sales progression of removal
                        $this->handleDealProgression($deal, $request, 'update',$deal_progress_data);
                    }

                }else {
                    //need to create new sales progression
                    $this->handleDealProgression($deal, $request, 'create');
                }

            }


            if ($request->deal_id == null || $request->deal_id == '') {
                event(new \App\Events\DealCreated($deal));
            }

            return response()->json(['status' => '1', 'msg' => $message, 'data' => $deal]);
        } else {
            return response()->json(['status' => '0', 'msg' => 'Deal Not Created'], 403);
        }
    }

    public function createDeal(Request $request)
    {
        $request->validate([
            'agent_id' => 'required|exists:users,id',
            'property_id' => 'required|exists:properties,id',
            'deal_price' => 'required|string',
            'commission' => 'nullable|numeric',
            'gross_commission' => 'required|string',
            'net_commission' => 'required|string',
            'commission_sources' => 'required|array',
        ]);
        $property = Property::find($request->property_id);
        $deal = new Deal();
        $deal->agent_id = $request->agent_id;
        $deal->contact_id = $request->owner_id;
        $deal->property_id = $request->property_id;
        $deal->deal_price = (int)str_replace(',', '', $request->deal_price);
        $deal->gross_commission = (double)str_replace(',', '', $request->gross_commission);
        $deal->net_commission = (double)str_replace(',', '', $request->net_commission);
        $deal->state = 'owner';
        $deal->created_by_id = auth()->user()->id ?? null;
        $propertyDocuments = PropertyMedia::where('property_id', $request->property_id)->where("type", "Other")->get();
        if ($property->for == 'Residential for sale' || $property->for === 'Commercial for sale') {
            if ($property->completion_status === 'Ready Primary' || $property->completion_status === 'Off-Plan Primary') {
                $deal->type = "Primary Resale";
            } else {
                $deal->type = "Secondary Sale";
            }
        } else if ($property->for === 'Residential for rent' || $property->for === 'Commercial for rent') {
            $deal->type = "Rental";
        }
        // Create contact media from property documents with same details
        foreach ($propertyDocuments as $propertyDocument) {
            $contactDocument = new ContactMedia();
            $contactDocument->contact_id = $request->owner_id;
            $contactDocument->type = $propertyDocument->type;
            $contactDocument->title = $propertyDocument->title;
            $contactDocument->description = $propertyDocument->description;
            $contactDocument->path = $propertyDocument->path;
            $contactDocument->original = $propertyDocument->original;
            $contactDocument->thumbnail = $propertyDocument->thumbnail;
            $contactDocument->note = $propertyDocument->note;
            $contactDocument->order = $propertyDocument->order;
            $contactDocument->save();
        }


        if ($deal->save()) {

            foreach ($request->commissions as $commission) {

                $dealCommission = new DealCommissionSource();
                $dealCommission->deal_id = $deal->id;
                $dealCommission->commission_amount = (int)str_replace(',', '', $commission['commission_amount']);
                $dealCommission->commission_percentage = $commission['percentage'];
                $dealCommission->commission_source = $commission['commission_sources']['name'] ?? null;
                $dealCommission->is_vat_applicable = $commission['vat'] ?? null;
                $dealCommission->vat_amount = $commission['vat_amount'] ?? null;
                $dealCommission->vat_percentage = 5;
                $dealCommission->created_by_id = auth()->user()->id ?? null;
                $dealCommission->save();
            }


            $dealSplit = new DealSplit();
            $dealSplit->deal_id = $deal->id;
            $dealSplit->user_id = $deal->agent_id;
            $dealSplit->commission_source = "Seller Side";
            $dealSplit->split_amount = $deal->net_commission;
            $dealSplit->created_by_id = auth()->user()->id ?? null;
            $dealSplit->save();

            $deal->load('commissionSources');

            return response()->json(['status' => '1', 'msg' => 'Deal Created Successfully', 'data' => $deal]);
        } else {
            return response()->json(['status' => '0', 'msg' => 'Deal Not Created']);
        }


    }

    public function addDealDoc(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'deal_id' => 'required|exists:deals,id',
            'file' => 'required|file',
            'model' => 'required|string',
            'id' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => '2', 'errors' => $validator->errors()], 422);
        }

        try {
            $name = Str::random(10);
            //get file real name

            $extension = $request->file->getClientOriginalExtension() ?? null;

            $fileType = getFileTypeByExtension($extension);

            $path = null;

            if ($fileType == "image") {
                $path = $this->uploadImageAndThumb($request->file, "deal/{$request->deal_id}/$name");
            } else {
                $path = $this->uploadDocument($request->file, "deal/{$request->deal_id}/$name");
                if (!empty($path)) {
                    $path = [
                        "image" => $path,
                        "thumbnail" => null
                    ];
                }
            }

            if (!empty($path["image"])) {
                $modelId = $request->deal_id;
                $modelType = Deal::class;
                if($request->model == "contact"){
                    $modelId = $request->id;
                    $modelType = Contact::class;
                }
                if($request->model == "lead"){
                    $modelId = $request->id;
                    $modelType = Lead::class;
                }

                $media = DealMedia::create([
                    "deal_id" => $request->deal_id,
                    "model_type" => $modelType,
                    "model_id" => $modelId,
                    "type" => "Other",
                    "title" => $document["title"] ?? "Other",
                    "description" => $request->file->getClientOriginalName() ?? null,
                    "extension" => $extension,
                    "thumbnail" => $path["thumbnail"],
                    "path" => $path["image"],
                    "created_by_id" => auth()->id(),
                ]);
            }

            return response()->json(['status' => '1', 'msg' => 'Deal Document Added Successfully', 'data' => $media]);
        } catch (Exception $e) {
            // Handle any exceptions here
            return response()->json(['status' => '0', 'msg' => 'An error occurred']);
        }
    }

    public function updateDealDoc(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'documents' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => '2', 'errors' => $validator->errors()], 422);
        }


        foreach ($request->documents ?? [] as $document) {
            $media = $media = DealMedia::find($document["id"]);;
            if (!is_null($media)) {
                $media->title = $document["title"] ?? $media->title;
                $media->save();
            }
        }

        DealMeta::storeDealMeta($request->deal_id, "attachments_mode", $request->attachments_mode);

        return response()->json(['status' => '1', 'msg' => 'Deal Document Updated Successfully']);
    }

    public function updateDealDocument(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'id' => 'required',
            'model' => 'required|in:contact,lead,deal',
            'title' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => '2', 'errors' => $validator->errors()], 422);
        }

        $media = null;

        if ($request->model == "deal") {
            $media = DealMedia::find($request->id);
        }

        if ($request->model == "contact") {
            $media = ContactMedia::find($request->id);
        }

        if ($request->model == "lead") {
            $media = LeadMedia::find($request->id);
        }

        if (!is_null($media)) {
            $media->title = $request->title;
            $media->save();
        }

        return response()->json(['status' => '1', 'msg' => 'Deal Document Updated Successfully']);
    }

    public function deleteDealdoc(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'deal_id' => 'required|exists:deals,id',
            'model' => 'required|string',
            'model_id' => 'nullable|numeric',
            'id' => 'required|exists:deal_media',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => '2', 'errors' => $validator->errors()], 422);
        }
        try {
            DealMedia::destroy($request->id);
            return response()->json(['status' => '1', 'msg' => 'Document Deleted Successfully']);
        } catch (Exception $e) {
            return response()->json(['status' => '0', 'msg' => 'Deal Document Not Deleted']);
        }
    }

    public function addDealCommission(Request $request)
    {
        $request->validate([
            'deal_id' => 'required|exists:deals,id',
            'deal_price' => 'required|string',
            'commission_sources' => 'required|array',
            'commission_amount' => 'required|array',
            'conveyancer_id' => 'nullable|exists:users,id',
            'transaction_type' => 'nullable|string',
        ]);
        try {
            $deal = Deal::find($request->deal_id);
            $deal->deal_price = (int)str_replace(',', '', $request->deal_price);
            $deal->gross_commission = (double)str_replace(',', '', $request->gross_commission);
            $deal->net_commission = (double)str_replace(',', '', $request->net_commission);
            $deal->save();
            $i = 0;
            foreach ($request->commission_amount as $commission) {
                $dealCommission = DealCommissionSource::find($request->commission_id[$i]) ?? new DealCommissionSource();
                $dealCommission->deal_id = $request->deal_id;
                $dealCommission->commission_amount = (int)str_replace(',', '', $commission);
                $dealCommission->commission_percentage = $request->commission_percentage[$i] ?? null;
                $dealCommission->commission_source = $request->commission_sources[$i] ?? null;
                $dealCommission->is_vat_applicable = $request->vat[$i] > 0 ? 1 : 0;
                $dealCommission->vat_amount = $request->vat[$i] ?? null;
                $dealCommission->vat_percentage = 5;
                $dealCommission->sales_progression_amount = (float)str_replace(',', '', $request->sales_progression_amount[$i]) ?? null;
                $dealCommission->created_by_id = auth()->user()->id ?? null;
                $dealCommission->save();
                $i++;
            }
            if ($request->conveyancer_id != null && $request->conveyancer_id != '') {

                $dpd = DealProgressionData::where('deal_id', $request->deal_id)->first() ?? new DealProgressionData();
               $old_person = $dpd->conveyancer_id ?? null;
                $dpd->deal_id = $request->deal_id;
                $dpd->conveyancer_id = $request->conveyancer_id;
                $dpd->transaction_type = $request->transaction_type;
                if($old_person != null && $old_person != $request->conveyancer_id){
                    $dpd->conveyancer_selection_mode = 'Manual';
                }
               // $dpd->conveyancer_selection_mode = 'Manual';
                $dpd->progression_amount = (float)str_replace(',', '', $request->progression_amount) ?? null;
                if($dpd->save()){
                    if($old_person != null && $old_person != $request->conveyancer_id){
                       event(new SalesProgressionChanged($deal, $dpd,$old_person));
                    }else{

                    }
                }

//            $deductible_type = DealDeductibleType::where('name', $request->transaction_type)->first();
//            if(!empty($deductible_type)) {
//                $new_deductible = new DealDeductible();
//                $new_deductible->deal_id = $request->deal_id;
//                $new_deductible->deal_deductible_type_id = $deductible_type->id;
//                $new_deductible->amount_type = $deductible_type->type;
//                $new_deductible->amount = (float)str_replace(',', '', $request->progression_amount) ?? null;
//                $new_deductible->created_by_id = auth()->user()->id ?? null;
//                $new_deductible->save();
//            }
            }


//            foreach ($request->commissions as $commission) {
//
//                $dealCommission = new DealCommissionSource();
//                $dealCommission->deal_id = $request->deal_id;
//                $dealCommission->commission_amount = (int)str_replace(',', '', $commission['commission_amount']);
//                $dealCommission->commission_percentage = $commission['percentage'];
//                $dealCommission->commission_source = $commission['commission_sources']['name'] ?? null;
//                $dealCommission->is_vat_applicable = $commission['vat'] ?? null;
//                $dealCommission->vat_amount = $commission['vat_amount'] ?? null;
//                $dealCommission->vat_percentage = 5;
//                $dealCommission->created_by_id = auth()->user()->id ?? null;
//                $dealCommission->save();
//            }

            $dealSplit = DealSplit::where('deal_id', $request->deal_id)->first();

            if (empty($dealSplit)) {
                $dealSplit = new DealSplit();
            }
            $dealSplit->deal_id = $deal->id;
            $dealSplit->user_id = $deal->agent_id;
            $dealSplit->commission_source = "Seller Side";
            $dealSplit->split_amount = $deal->net_commission;
            $dealSplit->created_by_id = auth()->user()->id ?? null;
            $dealSplit->save();


            $data = DealCommissionSource::where('deal_id', $deal->id)->get() ?? [];
            return response()->json(['status' => '1', 'msg' => 'Deal Commission Updated Successfully', 'data' => $data]);
        } catch (Exception $e) {
            return response()->json(['status' => '0', 'msg' => 'Meta Not Created']);
        }
    }

    public function getDealCommissions(Request $request)
    {
        $request->validate([
            'deal_id' => 'required|exists:deals,id',
        ]);
        $deal = Deal::find($request->deal_id);
        $commissions = DealCommissionSource::where('deal_id', $deal->id)->get();
        if ($commissions) {
            $deal = Deal::where('id', $request->deal_id)->with('dealMeta')->first();
            $deal_sales_progression = DealProgressionData::where('deal_id', $request->deal_id)->with('user')->first() ?? null;
            return response()->json(['status' => '1', 'msg' => 'Commissions Found', 'data' => $commissions, 'deal' => $deal,
                'sales_progression' => $deal_sales_progression]);

        } else {
            return response()->json(['status' => '0', 'msg' => 'Commissions Not Found']);
        }
    }

    public function deleteDealCommission(Request $request)
    {
        $request->validate([
            'deal_id' => 'required|exists:deals,id',
            'id' => 'required|exists:deal_commission_sources,id',
        ]);
        try {
            $dealCommission = DealCommissionSource::find($request->id);
            $deal = Deal::find($request->deal_id);
            $deal->net_commission = $deal->net_commission - $dealCommission->commission_amount;
            $deal->gross_commission = $deal->gross_commission - $dealCommission->commission_amount - $dealCommission->sales_progression_amount - $dealCommission->vat_amount;
            $deal->save();

            $dealCommission->delete();
            return response()->json(['status' => '1', 'msg' => 'Commission Deleted Successfully']);
        } catch (Exception $e) {
            return response()->json(['status' => '0', 'msg' => 'Commission Not Deleted']);
        }
    }

    public function addMetaData(Request $request)
    {
        $request->validate([
            'recordId' => 'required|integer',
            'table' => 'required|string|in:deal_meta,contact_meta,lead_meta',
            'metaData' => 'required|array',
            'metaData.*.meta_key' => 'required|string',
            'metaData.*.meta_value' => 'required|string',
        ]);
        try {
            $meta = createMeta($request->table, $request->recordId, $request->metaData);
            if ($meta) {
                return response()->json(['status' => '1', 'msg' => 'Meta Created Successfully']);
            } else {
                return response()->json(['status' => '0', 'msg' => 'Meta Not Created']);
            }
        } catch (Exception $e) {
            return response()->json(['status' => '0', 'msg' => 'Meta Not Created']);
        }
    }


    public function getDealLeads(Request $request)
    {
        $request->validate([
            'deal_id' => 'required|exists:deals,id',
        ]);

        try {
            $deal = Deal::with(['leads', 'leads.agent', 'leads.leadSource', 'leads.leadMeta', 'leads.leadMedium', 'leads.leadType', 'leads.dealMedia.createdBy', 'leads.dealMedia' => function($q){
                $q->where("deal_id", request("deal_id"));
            }])->find($request->deal_id);

            if ($deal) {
                $leads = $deal->leads->toArray(); // Convert to array for sorting

                usort($leads, function ($leadA, $leadB) {
                    if ($leadA['pivot']['lead_type'] === 'Primary') {
                        return -1; // $leadA comes first
                    } elseif ($leadB['pivot']['lead_type'] === 'Primary') {
                        return 1; // $leadB comes first
                    }
                    return 0; // no change in order
                });
                return response()->json(['status' => '1', 'msg' => 'Deal Found', 'data' => $leads]);
            } else {
                return response()->json(['status' => '0', 'msg' => 'Deal Not Found']);
            }
        } catch (Exception $e) {
            // Handle any exceptions here
            return response()->json(['status' => '0', 'msg' => 'An error occurred']);
        }
    }

    public function getDealCommissionCalculationsRevamped(Request $request)
    {
        $request->validate([
            'deal_id' => 'required|exists:deals,id',
            'refresh_calculations' => 'nullable'
        ]);

        $deal = Deal::with('splits')->find($request->deal_id);

        $dealSplits = $deal->splits;

        $calculatedCommissions = DealSplitCommission::where('deal_id', $request->deal_id)->with('user', 'split')->get();
        if ($calculatedCommissions->isEmpty() || $request->refresh_calculations == "true") {
            foreach ($dealSplits as $dealSplit) {
                $commissions = [];
                $netCommission = $dealSplit->split_amount;

                $agent = User::with('parent')->find($dealSplit->user_id);


                // Calculate the direct commission
                $agentCommission = $this->calculateCommission($agent, $netCommission, 'Direct');

                $commissions['agent_commission'] = $agentCommission;

                // Calculate indirect commissions for the parent hierarchy
                $parentHierarchy = getUserParentOnly($dealSplit->user_id);
                $remainingAmount = $netCommission - $agentCommission['commission'];

                foreach ($parentHierarchy as $parent) {
                    $parentUser = User::find($parent);
                    $parentRole = $parentUser->roles->first();
                    $parentCommission = $this->calculateIndirectCommission($parent, $parentRole, $netCommission, $remainingAmount);
                    $remainingAmount = $parentCommission['remainingAmount'];
                    if ($parentCommission['user_id']) {
                        $commissions[$parentRole->name] = $parentCommission;
                    }
                }

                // Store the calculated commissions in the database
                $commissions['deal'] = $deal;

                foreach ($commissions as $commission) {
                    if ($commission['user_id']) {
                        DealSplitCommission::updateOrCreate([
                            'deal_id' => $deal->id,
                            'deal_split_id' => $dealSplit->id,
                            'user_id' => $commission['user_id'],
                        ], $commission);
                    }
                }
            }
        }
        $notAdmin = getNotAdmin();
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }
        if (auth()->user()->hasRole(['Admin', "Super Admin"])) {
            $calculatedCommissions = DealSplit::where('deal_id', $deal->id)->with(['user', 'companyEarning', 'createdBy', 'commissions' => function ($q) use ($notAdmin, $childrenIds) {
                $q->when($notAdmin, function ($q) use ($childrenIds) {
                    $q->whereIn('user_id', $childrenIds);
                })->with('user.roles');
            }])->get();
        } else {
            $calculatedCommissions = DealSplit::where('deal_id', $deal->id)->with(['user', 'createdBy', 'commissions' => function ($q) use ($notAdmin, $childrenIds) {
                $q->when($notAdmin, function ($q) use ($childrenIds) {
                    $q->whereIn('user_id', $childrenIds);
                })->with('user.roles');
            }])->get();

        }

        return response()->json(['status' => '1', 'data' => $calculatedCommissions]);
    }

    public function updateDealSplitCommissionManual(Request $request)
    {
        $request->validate([
            'deal_split_commission_i' => 'required|exists:deal_split_commissions,id',
            'commission_value' => 'required',
            'commission' => 'required',
        ]);

        try {
            $deal_split_commission = DealSplitCommission::find($request->deal_split_commission_id);
            $deal_split_commission->commission_value = $request->commission_value;
            $deal_split_commission->commission = $request->commission;
            $deal_split_commission->save();
            $notAdmin = getNotAdmin();
            $childrenIds = [];
            if ($notAdmin) {
                $childrenIds = getUserChildrenIds(auth()->id());
            }
            if (auth()->user()->hasRole(['Admin', "Super Admin"])) {
                $calculatedCommissions = DealSplit::where('deal_id', $deal_split_commission->deal_id)->with(['user', 'companyEarning', 'createdBy', 'commissions' => function ($q) use ($notAdmin, $childrenIds) {
                    $q->when($notAdmin, function ($q) use ($childrenIds) {
                        $q->whereIn('user_id', $childrenIds);
                    })->with('user.roles');
                }])->get();
            } else {
                $calculatedCommissions = DealSplit::where('deal_id', $deal_split_commission->deal_id)->with(['user', 'createdBy', 'commissions' => function ($q) use ($notAdmin, $childrenIds) {
                    $q->when($notAdmin, function ($q) use ($childrenIds) {
                        $q->whereIn('user_id', $childrenIds);
                    })->with('user.roles');
                }])->get();
            }
            return response()->json(['status' => '1', 'msg' => 'Deal Commission Updated Successfully']);
        } catch (Exception $e) {
            return response()->json(['status' => '0', 'msg' => 'Deal Commission Not Updated'], 500);
        }

    }

    public function updateDealCommissionCalculationsRevamped(Request $request)
    {
        $request->validate([
            'deal_id' => 'required|exists:deals,id',
            'amount' => 'required|string',
            'user_id' => 'required|exists:users,id',
            'refresh_calculations' => 'nullable'
        ]);
        $request->amount = (int)str_replace(',', '', $request->amount);
        $deal = Deal::with('splits')->find($request->deal_id);

        $dealSplits = $deal->splits;

        $calculatedCommissions = DealSplitCommission::where('deal_id', $request->deal_id)->with('user', 'split')->get();
        // if ($calculatedCommissions->isEmpty()) {
        //DealSplitCommission::where('deal_id', $deal->id)->delete();
        foreach ($dealSplits as $dealSplit) {
            $commissions = [];
            $netCommission = $dealSplit->split_amount;

            if ($request->user_id == $dealSplit->user_id) {
                $agent = User::with('parent')->find($request->user_id);
                $agentCommission = [
                    'user_id' => $agent->id,
                    'commission' => $request->amount,
                    'commission_value' => ($request->amount / $netCommission) * 100,
                    'commission_category' => 'Direct',
                    'commission_type' => 'Percentage',
                ];

            } else {
                $agent = User::with('parent')->find($dealSplit->user_id);
                $agentCommission = $this->calculateCommission($agent, $netCommission, 'Direct');
            }
            // Calculate the direct commission
            $commissions['agent_commission'] = $agentCommission;

            // Calculate indirect commissions for the parent hierarchy
            $parentHierarchy = getUserParentOnly($dealSplit->user_id);
            $remainingAmount = $netCommission - $agentCommission['commission'];

            foreach ($parentHierarchy as $parent) {
                if ($request->user_id == $parent) {
                    $parentUser = User::find($parent);
                    $parentRole = $parentUser->roles->first();
                    $parentCommission = [
                        'user_id' => $parentUser->id,
                        'commission' => $request->amount,
                        'commission_value' => ($request->amount / $netCommission) * 100,
                        'commission_category' => $parentRole->name,
                        'commission_type' => 'Percentage',
                    ];
                } else {
                    $parentUser = User::find($parent);
                    $parentRole = $parentUser->roles->first();
                    $parentCommission = $this->calculateIndirectCommission($parent, $parentRole, $netCommission, $remainingAmount);
                }
                $remainingAmount = $parentCommission['remainingAmount'];
                if ($parentCommission['user_id']) {
                    $commissions[$parentRole->name] = $parentCommission;
                }
            }

            // Store the calculated commissions in the database
            $commissions['deal'] = $deal;

            foreach ($commissions as $commission) {
                if ($commission['user_id']) {
                    DealSplitCommission::updateOrCreate([
                        'deal_id' => $deal->id,
                        'deal_split_id' => $dealSplit->id,
                        'user_id' => $commission['user_id'],
                    ], $commission);
                    // dd($commission);
                }
            }
        }
        // }
        $calculatedCommissions = DealSplit::where('deal_id', $deal->id)->with('user', 'commissions.user.roles', 'createdBy', 'companyEarning')->get();
        return response()->json(['status' => '1', 'data' => $calculatedCommissions]);
    }

    public function createDealSplit(Request $request)
    {
        $request->validate([
            'id' => 'nullable|exists:deal_splits,id', // If id is present, update the existing split, otherwise create a new one
            'deal_id' => 'required|exists:deals,id',
            'agent_id' => 'required|exists:users,id',
            'split_amount' => 'required|string',
            'commission_source' => 'nullable|string'
        ]);
        $deal = Deal::find($request->deal_id);
        $totalSplitCommission = DB::table('deal_splits')
            ->where('deal_id', $request->deal_id)
            ->whereNot('user_id', $deal->agent_id)
            ->sum('split_amount');
        $split_amount = str_replace(',', '', $request->split_amount);
//
        // if ($split_amount > $deal->net_commission || $totalSplitCommission + $split_amount > $deal->net_commission) {
        //     return response()->json(['status' => '0', 'msg' => 'Split Amount Cannot Be Greater Than Net Commission']);
        // }

        $dealSplit = DealSplit::updateOrCreate([
            'id' => $request->id,
        ], [
            'deal_id' => $request->deal_id,
            'user_id' => $request->agent_id,
            'split_amount' => $split_amount,
            'split_percentage' => $request->commission,
            'commission_source' => $request->commission_source,
            'created_by_id' => auth()->user()->id ?? null,
        ]);
        if($request->agent_id != $deal->agent_id){

            $totalSplitCommission = DB::table('deal_splits')
            ->where('deal_id', $request->deal_id)
            ->whereNot('user_id', $deal->agent_id)
            ->whereNull('deleted_at')
            ->sum('split_amount');

            $remainingAmount = $deal->net_commission - $totalSplitCommission;

            $splitForPrimaryAgent = DealSplit::where('deal_id', $request->deal_id)
                ->where('user_id', $deal->agent_id)
                ->first();

            if ($splitForPrimaryAgent) {
                $splitForPrimaryAgent->split_amount = $remainingAmount;
                $splitForPrimaryAgent->save();
            }
        }

        // if($request->id == null || $request->id == '') {

            $request = new Request();
            // Added deal id in request and pass to getDealCommissionCalculationsRevamped function
            $request->merge(['deal_id' => $deal->id]);
            $request->merge(['refresh_calculations' => true]);
            $this->getDealCommissionCalculationsRevamped($request);
        // }
        return response()->json(['status' => '1', 'msg' => 'Deal Split saved successfully', 'data' => $dealSplit]);

    }

    public function getDealSplitHierarchy(Request $request)
    {
        $request->validate([
            'deal_id' => 'required|exists:deals,id',
        ]);
        $new_request = new Request();
        $new_request->merge(['deal_id' => $request->deal_id]);
        $new_request->merge(['refresh_calculations' => false]);
        $response = $this->getDealCommissionCalculationsRevamped($new_request);
        $data = $response->getData();
        $obj = $data->data;
        $res = [];
        foreach ($obj as $key => $value) {
            $res[$key]['commission_source'] = $value->commission_source;
            foreach ($value?->commissions as $key1 => $value1) {
                $res[$key]['commissions_user'][$key1] = $value1->user;
            }
        }
        return response()->json(['status' => '1', 'msg' => 'Deal Split Hierarchy', 'data' => $res]);
    }

    public function createDealApproval(Request $request)
    {
        $request->validate([
            'deal_id' => 'required|exists:deals,id',
            'user_list' => 'required|array',
            'user_list.*' => 'required|exists:users,id',
        ]);
        try {
            if (auth()->user()->hasRole(['Admin', "Super Admin"])) {
                $role = \DB::table('roles')->where('name', 'Admin')->first();
                DealApproval::updateOrCreate([
                    'deal_id' => $request->deal_id,
                    'role_id' => $role->id,
                ], [
                    'approval_status' => 'Approved',
                    'created_by_id' => auth()->user()->id ?? null,
                ]);
            }
            foreach ($request->user_list as $user) {
                if (DealApproval::where('deal_id', $request->deal_id)->where('user_id', $user)->whereIn('approval_status', ['Approved', 'Pending'])->exists()) {
                    continue;
                }
//           DealApproval::updateOrCreate([
//                'deal_id' => $request->deal_id,
//                'user_id' => $user,
//            ], [
//                'approval_status' => 'Pending',
//                'created_by_id' => auth()->user()->id ?? null,
//            ]);
                $dealApproval = new DealApproval();
                $dealApproval->uuid = Str::uuid();
                $dealApproval->deal_id = $request->deal_id;
                $dealApproval->approval_status = 'Pending';
                $dealApproval->user_id = $user;
                $dealApproval->save();
            }
            return response()->json(['status' => '1', 'msg' => 'Deal Approval Created']);
        } catch (Exception $e) {
            return response()->json(['status' => '0', 'msg' => 'Deal Approval Not Created']);
        }


    }


//    public function checkDealApproval($deal_id)
//    {
//
//        $deal_approval = DealApproval::where('deal_id', $deal_id)->where('approval_status', 'Pending')->first();
//        if (empty($deal_approval)) {
//            $deal = Deal::find($deal_id);
//            $deal->status = 'Completed';
//            $deal->save();
//        }
//    }
    public function updateDealApprovalAdmin(Request $request)
    {
        //this will bypass the approval process and mark the deal as approved by admin

        if(!auth()->user()->hasRole(['Admin', "Super Admin"])){
            return response()->json(['status' => '0', 'msg' => 'You are not authorized to perform this action'], 401);
        }
        $request->validate([
            'deal_id' => 'required|exists:deals,id',
            'id' => 'nullable|exists:deal_approvals,id',
            'approval_status' => 'required|in:Approved,Rejected',
            'comment' => 'nullable|string'
        ]);

        if ($request->id) {
            $dealApproval = DealApproval::find($request->id);
        } else {
            $dealApproval = new DealApproval();
            $dealApproval->uuid = Str::uuid();
        }
        $dealApproval->deal_id = $request->deal_id;
        $dealApproval->approval_status = $request->approval_status;
        $dealApproval->comment = $request->comment;
        $dealApproval->action_by_id = auth()->user()->id;
        $dealApproval->ip_address = $request->ip();
        if($request->approval_status == 'Approved'){
            $dealApproval->approved_at = Carbon::now();
        }elseif ($request->approval_status == 'Rejected') {
            $dealApproval->rejected_at = Carbon::now();
        }
        $dealApproval->save();
        if ($request->approval_status == 'Rejected') {

            $deal = Deal::find($request->deal_id);
            $deal->status = 'REJECTED';
            $deal->save();

            $deal_note = new DealNote();
            $deal_note->deal_id = $request->deal_id;
            $deal_note->step = null;
            $deal_note->note = 'Deal Rejected By Admin: ' . $request->comment;
            $deal_note->created_by_id = auth()->user()->id;
            $deal_note->save();
            event(new DealRejected($deal, $dealApproval));
        }else{

            $deal = Deal::find($request->deal_id);
            $deal->status = 'APPROVED';
            $deal->status_message = 'Deal Approved & sent to finance department';
            $deal->save();

            $deal_note = new DealNote();
            $deal_note->deal_id = $request->deal_id;
            $deal_note->step = null;
            $deal_note->note = 'Deal Approved & sent to finance department: ' . $request->comment;
            $deal_note->created_by_id = auth()->user()->id;
            $deal_note->save();
            event(new DealApproved($deal));
        }


        return response()->json(['status' => '1', 'msg' => 'Updated Approval']);
    }

    public function updateDealApproval(Request $request)
    {
        $request->validate([
            'deal_id' => 'required|exists:deals,id',
            'id' => 'nullable|exists:deal_approvals,id',
            'approval_status' => 'required|in:Approved,Rejected',
            'comment' => 'nullable|string'
        ]);

        if ($request->id) {
            $dealApproval = DealApproval::find($request->id);
        } else {
            $dealApproval = new DealApproval();
            $dealApproval->uuid = Str::uuid();
        }
        $dealApproval->deal_id = $request->deal_id;
        $dealApproval->approval_status = $request->approval_status;
        $dealApproval->comment = $request->comment;
        $dealApproval->action_by_id = auth()->user()->id;
        $dealApproval->ip_address = $request->ip();
        if($request->approval_status == 'Approved'){
            $dealApproval->approved_at = Carbon::now();
        }elseif ($request->approval_status == 'Rejected') {
            $dealApproval->rejected_at = Carbon::now();
        }
        $dealApproval->save();
        if ($request->approval_status == 'Rejected') {
            if (auth()->user()->hasRole(['Admin', "Super Admin"])) {
                $deal = Deal::find($request->deal_id);
                $deal->status = 'REJECTED';
                $deal->save();
            }
            $deal_note = new DealNote();
            $deal_note->deal_id = $request->deal_id;
            $deal_note->step = null;
            $deal_note->note = 'Deal Rejected: ' . $request->comment;
            $deal_note->created_by_id = auth()->user()->id;
            $deal_note->save();
            //$this->checkDealApproval($request->deal_id);
            event(new DealRejected($deal, $dealApproval));
        }


        return response()->json(['status' => '1', 'msg' => 'Updated Approval']);
    }

    public function updateDealApprovalExternal(Request $request)
    {

        $request->validate([
//            'deal_id' => 'required|exists:deals,id',
            'uuid' => 'required|exists:deal_approvals,uuid',
            'approval_status' => 'required|in:Approved,Rejected',
//            'uuid' => 'required|exists:users,id',
            'comment' => 'nullable|string'
        ]);

//        $user_id = preg_replace('/[^0-9]/', '', $request->user_id);
//        $id = preg_replace('/[^0-9]/', '', $request->id);
//        $deal_id = preg_replace('/[^0-9]/', '', $request->deal_id);
        $dealApproval = DealApproval::where('uuid',$request->uuid)->first();
        $comment = trim($request->comment);
        $comment = htmlspecialchars($comment, ENT_QUOTES, 'UTF-8');
        $approval_status = preg_replace('/[^A-Za-z\-]/', '', $request->approval_status);
        $dealApproval->deal_id = $dealApproval->deal_id;
        $dealApproval->approval_status = $approval_status;
        $dealApproval->comment = $comment;
        $dealApproval->action_by_id = $dealApproval->user_id;
        $dealApproval->ip_address = $request->ip();
        if($request->approval_status == 'Approved'){
            $dealApproval->approved_at = Carbon::now();
        }elseif ($request->approval_status == 'Rejected') {
            $dealApproval->rejected_at = Carbon::now();
        }
        $dealApproval->save();
        if ($approval_status == 'Rejected') {
            $deal = Deal::find($dealApproval->deal_id);
            if (auth()->user()->hasRole(['Admin', "Super Admin"])) {
                $deal->status = 'Rejected';
                $deal->save();
            }
            $deal_note = new DealNote();
            $deal_note->deal_id = $dealApproval->deal_id;
            $deal_note->step = null;
            $deal_note->note = 'Deal Rejected: ' . $comment;
            $deal_note->created_by_id = $dealApproval->user_id;
            $deal_note->save();


            event(new DealRejected($deal, $dealApproval));
        }


        return response()->json(['status' => '1', 'msg' => 'Updated Approval']);
    }

    public function updateDealStatus(Request $request)
    {
        $request->validate([
            'deal_id' => 'required|exists:deals,id',
            'status' => 'required|string|in:PENDING_APPROVAL,APPROVED,REJECTED,COMPLETED',
        ]);
        try {
            $role = \DB::table('roles')->where('name', 'Admin')->first();
            $deal = Deal::find($request->deal_id);
            $old_status = $deal->status;
            $deal->status = $request->status;
            if ($deal->save()) {
                if($old_status == 'REJECTED' && $request->status == 'PENDING_APPROVAL') {
                    $deal = Deal::where('id', $request->deal_id)->with('agent', 'property', 'contact', 'createdBy')->first();
                    $deal->status_message = 'Resubmitted For Approval';
                    $deal->save();
                    $deal_approval = new DealApproval();
                    $deal_approval->uuid = Str::uuid();
                    $deal_approval->title = 'From Agent to Admin';
                    $deal_approval->deal_id = $request->deal_id;
                    $deal_approval->approval_status = 'Pending';
                    $deal_approval->role_id = $role->id;
                    $deal_approval->save();
                    $deal_note = new DealNote();
                    $deal_note->deal_id = $deal->id;
                    $deal_note->step = null;
                    $deal_note->note = 'Deal resubmitted for approval';
                    $deal_note->created_by_id = auth()->user()->id;
                    $deal_note->save();

                    event(new DealResubmitted($deal,$deal_approval));
                }else if ($request->status == 'PENDING_APPROVAL') {
                    $deal = Deal::where('id', $request->deal_id)->with('agent', 'property', 'contact', 'createdBy')->first();
                    $deal->status_message = 'Pending for Admin Approval';
                    $deal->save();
                    //add action_by == auth->user(); on $deal object
                    // $deal->action_by = auth()->user();

                    $deal_approval = new DealApproval();
                    $deal_approval->uuid = Str::uuid();
                    $deal_approval->title = 'From Agent to Admin';
                    $deal_approval->deal_id = $request->deal_id;
                    $deal_approval->approval_status = 'Pending';
                    $deal_approval->role_id = $role->id;
                    $deal_approval->save();
                    $deal_note = new DealNote();
                    $deal_note->deal_id = $deal->id;
                    $deal_note->step = null;
                    $deal_note->note = 'Deal submitted for approval';
                    $deal_note->created_by_id = auth()->user()->id;
                    $deal_note->save();

                    event(new DealSubmittedForApproval($deal));
                }else if($request->status == 'COMPLETED') {
                    $deal = Deal::where('id', $request->deal_id)->with('agent', 'property', 'contact', 'createdBy')->first();
                    $deal->status_message = 'Deal has been marked as Completed';
                    $deal->save();

                    $deal_note = new DealNote();
                    $deal_note->deal_id = $deal->id;
                    $deal_note->step = null;
                    $deal_note->note = 'Deal has been marked as Completed';
                    $deal_note->created_by_id = auth()->user()->id;
                    $deal_note->save();
                    event(new DealCompleted($deal));
                }

            }
            $result = array('status' => '1', 'msg' => "Deal status updated successfully.");
            return response()->json($result);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!','error' => $exception->getMessage());
            return response()->json($result);
        }
    }


    public function getDealSplitCommissionById(Request $request)
    {
        $request->validate([
            'split_id' => 'required|exists:deal_split_commissions,id',
        ]);

        $split_commission = DealSplitCommission::where('id', $request->split_id)->with('user')->first();

        if ($split_commission) {
            // $this->calculateCommission($split->user, $split->commission, 'Direct');
            $split = DealSplit::where('id', $split_commission->deal_split_id)->with('user')->first();
            return response()->json(['status' => '1', 'msg' => 'Split Found', 'data' => $split_commission, 'split' => $split]);
        } else {
            return response()->json(['status' => '0', 'msg' => 'Split Not Found']);
        }
    }

    public function updateDealSplitCommission(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:deal_split_commissions,id',
            'split_amount' => 'nullable|string',
            // 'commission_percentage' => 'required|numeric',

        ]);

        $split = DealSplitCommission::find($request->id);

        if ($split) {
            $split->commission = $request->split_amount;
            $split->save();

            return response()->json(['status' => '1', 'msg' => 'Split Commission Updated Successfully', 'data' => $split]);
        } else {
            return response()->json(['status' => '0', 'msg' => 'Split Not Found']);
        }
    }

    public function calculateCommission($user, $netCommission, $category)
    {
        $commission = [
            'user_id' => $user->id,
            'commission_category' => $category,
        ];

        $agentCommission = CommissionSlab::whereHas('commissionSlabUsers', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->where('commission_category', $category)->first();

        if (!$agentCommission) {
            $role = $user->roles->first();
            $agentCommission = CommissionSlab::whereHas('commissionSlabUsers', function ($q) use ($role) {
                $q->where('role_id', $role->id);
            })->where('commission_category', $category)->first();
        }

        if ($agentCommission) {
            if ($agentCommission->commission_type === 'Percentage') {
                $commission['commission'] = $netCommission * ($agentCommission->commission_value / 100);
            } elseif ($agentCommission->commission_type === 'Fixed') {
                $commission['commission'] = $agentCommission->commission_value;
            }
            $commission['commission_type'] = $agentCommission->commission_type;
            $commission['commission_value'] = $agentCommission->commission_value;
        } else {
            $commission['commission'] = 0;
            $commission['commission_type'] = "Percentage";
            $commission['commission_value'] = 0;
        }

        return $commission;
    }

    public function calculateCommissionRevamped($user, $netCommission, $category)
    {
        $commission = [
            'user_id' => $user->id,
            'commission_category' => $category,
        ];

        // Find the commission slab for the user's role or directly for the user
        $agentCommission = CommissionSlab::whereHas('commissionSlabUsers', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->where('commission_category', $category)->first();

        $totalEarnedCommission = DealSplitCommission::where('user_id', $user->id)->sum('commission');

        if (!$agentCommission) {
            $role = $user->roles->first();
            $agentCommission = CommissionSlab::whereHas('commissionSlabUsers', function ($q) use ($role) {
                $q->where('role_id', $role->id);
            })->where('commission_category', $category)->first();
        }

        if ($agentCommission) {
            // Find the commission slab limits for the agent's commission slab
            $slabLimits = CommissionSlabLimit::where('commission_slab_id', $agentCommission->id)->get();

            foreach ($slabLimits as $slabLimit) {
                if ($totalEarnedCommission >= $slabLimit->from_value && $totalEarnedCommission <= $slabLimit->to_value) {
                    // Use the commission type and value from the matching slab limit
                    $commission['commission'] = $slabLimit->commission_type === 'Percentage' ?
                        $netCommission * ($slabLimit->commission_value / 100) : $slabLimit->commission_value;
                    $commission['calculation_type'] = $slabLimit->commission_type;
                    $commission['calculation_value'] = $slabLimit->commission_value;
                    return $commission; // Return the commission for the matching slab limit
                }
            }

            // If no matching slab limit is found, use the default commission type and value from the agentCommission
            $commission['commission'] = $agentCommission->commission_type === 'Percentage' ?
                $netCommission * ($agentCommission->commission_value / 100) : $agentCommission->commission_value;
            $commission['calculation_type'] = $agentCommission->commission_type;
            $commission['calculation_value'] = $agentCommission->commission_value;
        } else {
            $commission['commission'] = 0;
            $commission['calculation_type'] = "Percentage";
            $commission['calculation_value'] = 0;
        }

        return $commission;
    }


    public function calculateIndirectCommission($user, $role, $netCommission, $remainingAmount)
    {
        $commission = [
            'user_id' => $user,
            'commission_category' => 'Indirect',
            'remainingAmount' => $remainingAmount
        ];

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
            if ($parentCommission->commission_type === 'Percentage' && $parentCommission->calculation_from === 'Net Commission') {
                $commission['commission'] = $netCommission * ($parentCommission->commission_value / 100);
                $remainingAmount -= $commission['commission'];
                $commission['remainingAmount'] = $remainingAmount;

            } elseif ($parentCommission->commission_type === 'Percentage' && $parentCommission->calculation_from === 'Remaining Amount') {
                $commission['commission'] = $remainingAmount * ($parentCommission->commission_value / 100);
                $commission['remainingAmount'] = $remainingAmount;
            } elseif ($parentCommission->commission_type === 'Fixed' && $parentCommission->calculation_from === 'Net Commission') {
                $commission['commission'] = $parentCommission->commission_value;
                $remainingAmount -= $parentCommission->commission_value;
                $commission['remainingAmount'] = $remainingAmount;
            } elseif ($parentCommission->commission_type === 'Fixed' && $parentCommission->calculation_from === 'Remaining Amount') {
                $commission['commission'] = $parentCommission->commission_value;
                $commission['remainingAmount'] = $remainingAmount;
            }
            $commission['commission_type'] = $parentCommission->commission_type;
            $commission['commission_value'] = $parentCommission->commission_value;
            $commission['remainingAmount'] = $remainingAmount;
        }

        return $commission;
    }


    public function getDealCommissionCalculations(Request $request)
    {
        $request->validate([
            'deal_id' => 'required|exists:deals,id',
        ]);
        $deal = Deal::find($request->deal_id);
        $netCommission = $deal->net_commission;
        $agent = User::with('parent')->find($deal->agent_id);

        $commissions = [];
        $parentHierarchy = getUserParentOnly($deal->agent_id);

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

            $remainingAmount = $netCommission - $commissionVal;
            foreach ($parentHierarchy as $parent) {
                $parentUser = User::find($parent);
                $parentRole = $parentUser->roles->first();
                $parentCommission = CommissionSlab::whereHas('commissionSlabUsers', function ($q) use ($parent) {
                    $q->where('user_id', $parent);
                })->where('commission_category', 'Indirect')->first();

                if (empty($parentCommission)) {
                    if ($parentRole) {
                        $parentCommission = CommissionSlab::whereHas('commissionSlabUsers', function ($q) use ($parentRole) {
                            $q->where('role_id', $parentRole->id);
                        })->where('commission_category', 'Indirect')->first();

                        if ($parentCommission) {

                            if ($parentCommission->commission_type === 'Percentage' && $parentCommission->calculation_from === 'Net Commission') {

                                $parentCommissionVal = $netCommission * ($parentCommission->commission_value / 100); // Convert percentage to decimal
                                $commissions[$parentRole->name]['commission'] = $parentCommissionVal;
                                $commissions[$parentRole->name]['calculation_from'] = "Net Commission";
                                $commissions[$parentRole->name]['user_id'] = $parent;
                                $commissions[$parentRole->name]['calculation_type'] = $parentCommission->commission_type;
                                $commissions[$parentRole->name]['calculation_value'] = $parentCommission->commission_value;
                                $remainingAmount = $remainingAmount - $parentCommissionVal;
                                $commissions[$parentRole->name]['commission_category'] = "Indirect";

                            } elseif ($parentCommission->commission_type === 'Percentage' && $parentCommission->calculation_from === 'Remaining Amount') {
                                $parentCommissionVal = $remainingAmount * ($parentCommission->commission_value / 100); // Convert percentage to decimal
                                $commissions[$parentRole->name]['commission'] = $parentCommissionVal;
                                $commissions[$parentRole->name]['calculation_from'] = "Remaining Amount";
                                $commissions[$parentRole->name]['calculation_type'] = $parentCommission->commission_type;
                                $commissions[$parentRole->name]['user_id'] = $parent;
                                $commissions[$parentRole->name]['calculation_value'] = $parentCommission->commission_value;
                                $commissions[$parentRole->name]['commission_category'] = "Indirect";

                            } elseif ($parentCommission->commission_type === 'Fixed' && $parentCommission->calculation_from === 'Net Commission') {

                                $commissions[$parentRole->name]['commission'] = $parentCommission->commission_value;
                                $commissions[$parentRole->name]['calculation_from'] = "Net Commission";
                                $commissions[$parentRole->name]['calculation_type'] = $parentCommission->commission_type;
                                $commissions[$parentRole->name]['user_id'] = $parent;
                                $commissions[$parentRole->name]['calculation_value'] = $parentCommission->commission_value;
                                $remainingAmount = $remainingAmount - $parentCommission->commission_value;
                                $commissions[$parentRole->name]['commission_category'] = "Indirect";
                            } elseif ($parentCommission->commission_type === 'Fixed' && $parentCommission->calculation_from === 'Remaining Amount') {

                                $commissions[$parentRole->name]['commission'] = $parentCommission->commission_value;
                                $commissions[$parentRole->name]['user_id'] = $parent;
                                $commissions[$parentRole->name]['calculation_from'] = "Net Commission";
                                $commissions[$parentRole->name]['calculation_type'] = $parentCommission->commission_type;
                                $commissions[$parentRole->name]['calculation_value'] = $parentCommission->commission_value;
                                $commissions[$parentRole->name]['commission_category'] = "Indirect";
                            } else {
                                $parentCommissionVal = $netCommission * ($parentCommission->commission_value / 100); // Convert percentage to decimal
                                $commissions[$parentRole->name] = $parentCommissionVal;
                                $commissions[$parentRole->name]['commission_category'] = "Indirect";
                                $commissions[$parentRole->name]['user_id'] = $parentUser->id;
                            }
                        } else {
                            $commissions[$parentRole->name]['commission'] = 0;
                            $commissions[$parentRole->name]['commission_category'] = "Indirect";
                            $commissions[$parentRole->name]['user_id'] = $parentUser->id;
                        }

                    } else {
                        $commissions[$parentRole->name]['commission'] = 0;
                        $commissions[$parentRole->name]['commission_category'] = "Indirect";
                        $commissions[$parentRole->name]['user_id'] = $parentUser->id;
                    }
                } else {

                    if ($parentCommission->commission_type === 'Percentage' && $parentCommission->calculation_from === 'Net Commission') {

                        $parentCommissionVal = $netCommission * ($parentCommission->commission_value / 100);
                        // Convert percentage to decimal
                        $commissions[$parentRole->name]['user_id'] = $parent;
                        $commissions[$parentRole->name]['commission'] = $parentCommissionVal;
                        $commissions[$parentRole->name]['calculation_from'] = "Net Commission";
                        $commissions[$parentRole->name]['calculation_type'] = $parentCommission->commission_type;
                        $commissions[$parentRole->name]['calculation_value'] = $parentCommission->commission_value;
                        $remainingAmount = $remainingAmount - $parentCommissionVal;
                        $commissions[$parentRole->name]['commission_category'] = "Indirect";


                    } elseif ($parentCommission->commission_type === 'Percentage' && $parentCommission->calculation_from === 'Remaining Amount') {

                        $parentCommissionVal = $remainingAmount * ($parentCommission->commission_value / 100); // Convert percentage to decimal
                        $commissions[$parentRole->name]['commission'] = $parentCommissionVal;
                        $commissions[$parentRole->name]['calculation_from'] = "Remaining Amount";
                        $commissions[$parentRole->name]['user_id'] = $parent;
                        $commissions[$parentRole->name]['calculation_type'] = $parentCommission->commission_type;
                        $commissions[$parentRole->name]['calculation_value'] = $parentCommission->commission_value;
                        $commissions[$parentRole->name]['commission_category'] = "Indirect";


                    } elseif ($parentCommission->commission_type === 'Fixed' && $parentCommission->calculation_from === 'Net Commission') {
                        $commissions[$parentRole->name]['user_id'] = $parent;
                        $commissions[$parentRole->name]['commission'] = $parentCommission->commission_value;
                        $commissions[$parentRole->name]['calculation_from'] = "Net Commission";
                        $commissions[$parentRole->name]['calculation_type'] = $parentCommission->commission_type;
                        $commissions[$parentRole->name]['calculation_value'] = $parentCommission->commission_value;
                        $remainingAmount = $remainingAmount - $parentCommission->commission_value;
                        $commissions[$parentRole->name]['commission_category'] = "Indirect";
                    } elseif ($parentCommission->commission_type === 'Fixed' && $parentCommission->calculation_from === 'Remaining Amount') {

                        $commissions[$parentRole->name]['commission'] = $parentCommission->commission_value;

                        $commissions[$parentRole->name]['calculation_from'] = "Net Commission";
                        $commissions[$parentRole->name]['user_id'] = $parent;
                        $commissions[$parentRole->name]['calculation_type'] = $parentCommission->commission_type;
                        $commissions[$parentRole->name]['calculation_value'] = $parentCommission->commission_value;
                        $commissions[$parentRole->name]['commission_category'] = "Indirect";

                    } else {
                        $parentCommissionVal = $netCommission * ($parentCommission->commission_value / 100); // Convert percentage to decimal
                        $commissions[$parentRole->name]['commission'] = $parentCommissionVal;
                        $commissions[$parentRole->name]['commission_category'] = "Indirect";
                        $commissions[$parentRole->name]['user_id'] = $parentUser->id;

                    }
                }
            }

        } else {
            // Calculating Commission Here
            if ($agentCommission->commission_type === 'Percentage') {
                $commissionVal = $netCommission * ($agentCommission->commission_value / 100); // Convert percentage to decimal
            } elseif ($agentCommission->commission_type === 'Fixed') {
                $commissionVal = $agentCommission->commission_value; // Use the fixed value
            } else {
                // Handle other types of commission structures if needed
                $commissionVal = 0; // Default to zero if the type is not recognized
            }
            $remainingAmount = $netCommission - $commissionVal;
            foreach ($parentHierarchy as $parent) {
                $parentUser = User::with("roles:id,name")->find($parent);
                $parentCommission = CommissionSlab::whereHas('commissionSlabUsers', function ($q) use ($parent) {
                    $q->where('user_id', $parent);
                })->where('commission_category', 'Indirect')->first();
                $parentRole = $parentUser->roles->first();

                if (empty($parentCommission)) {

                    //$parentRole = $parentUser->roles->first();
                    // dd($parentRole->name);
                    if ($parentRole) {

                        $parentCommission = CommissionSlab::whereHas('commissionSlabUsers', function ($q) use ($parentRole) {
                            $q->where('role_id', $parentRole->id);
                        })->where('commission_category', 'Indirect')->first();

                        if ($parentCommission) {

                            if ($parentCommission->commission_type === 'Percentage' && $parentCommission->calculation_from === 'Net Commission') {
                                $parentCommissionVal = $netCommission * ($parentCommission->commission_value / 100); // Convert percentage to decimal

                                $remainingAmount = $remainingAmount - $parentCommissionVal;
                                $commissions[$parentRole->name]['commission'] = $parentCommissionVal;
                                $commissions[$parentRole->name]['calculation_from'] = "Net Commission";
                                //calculation type
                                $commissions[$parentRole->name]['calculation_type'] = $parentCommission->commission_type;
                                $commissions[$parentRole->name]['commission_category'] = "Indirect";

                                //calculation value
                                $commissions[$parentRole->name]['calculation_value'] = $parentCommission->commission_value;
                                // user id
                                $commissions[$parentRole->name]['user_id'] = $parent;


                            } elseif ($parentCommission->commission_type === 'Percentage' && $parentCommission->calculation_from === 'Remaining Amount') {

                                $parentCommissionVal = $remainingAmount * ($parentCommission->commission_value / 100); // Convert percentage to decimal
                                //dd($parentRole->name);
                                $commissions[$parentRole->name]['commission'] = $parentCommissionVal;
                                $commissions[$parentRole->name]['name'] = $parentUser->full_name;
                                $commissions[$parentRole->name]['user_id'] = $parentUser->id;
                                $commissions[$parentRole->name]['calculation_from'] = "Remaining Amount";
                                //calculation type
                                $commissions[$parentRole->name]['commission_category'] = "Indirect";
                                $commissions[$parentRole->name]['calculation_type'] = $parentCommission->commission_type;
                                //calculation value
                                $commissions[$parentRole->name]['calculation_value'] = $parentCommission->commission_value;

                            } elseif ($parentCommission->commission_type === 'Fixed' && $parentCommission->calculation_from === 'Net Commission') {

                                $commissions[$parentRole->name]['commission'] = $parentCommission->commission_value;
                                $commissions[$parentRole->name]['calculation_from'] = "Net Commission";
                                $commissions[$parentRole->name]['name'] = $parentUser->full_name;
                                $commissions[$parentRole->name]['user_id'] = $parentUser->id;
                                //calculation type
                                $commissions[$parentRole->name]['calculation_type'] = $parentCommission->commission_type;
                                $commissions[$parentRole->name]['commission_category'] = "Indirect";
                                //calculation value
                                $commissions[$parentRole->name]['calculation_value'] = $parentCommission->commission_value;
                            } elseif ($parentCommission->commission_type === 'Fixed' && $parentCommission->calculation_from === 'Remaining Amount') {

                                $commissions[$parentRole->name]['commission'] = $parentCommission->commission_value;
                                $commissions[$parentRole->name]['name'] = $parentUser->full_name;
                                $commissions[$parentRole->name]['user_id'] = $parentUser->id;

                                $commissions[$parentRole->name]['calculation_from'] = "Net Commission";
                                //calculation type
                                $commissions[$parentRole->name]['calculation_type'] = $parentCommission->commission_type;
                                $commissions[$parentRole->name]['commission_category'] = "Indirect";
                                //calculation value
                                $commissions[$parentRole->name]['calculation_value'] = $parentCommission->commission_value;

                            } else {
                                $parentCommissionVal = $netCommission * ($parentCommission->commission_value / 100); // Convert percentage to decimal
                                $commissions[$parentRole->name]['commission'] = $parentCommissionVal;
                                $commissions[$parentRole->name]['name'] = $parentUser->full_name;
                                $commissions[$parentRole->name]['user_id'] = $parentUser->id;
                                $commissions[$parentRole->name]['commission_category'] = "Indirect";
                            }
                        } else {
                            $commissions[$parentRole->name]['commission'] = 0;
                            $commissions[$parentRole->name]['user_id'] = $parentUser->id;
                            // $commissions[$parentRole->name]['name'] = $parentUser->full_name;
                            // $commissions[$parentRole->name]['user_id'] = $parentUser->id;
                        }

                    } else {
                        $commissions[$parentRole->name]['commission'] = 0;
                        $commissions[$parentRole->name]['user_id'] = $parentUser->id;
                        // $commissions[$parentRole->name]['name'] = $parentUser->full_name;
                        // $commissions[$parentRole->name]['user_id'] = $parentUser->id;
                    }
                } else {

                    if ($parentCommission->commission_type === 'Percentage' && $parentCommission->calculation_from === 'Net Commission') {

                        $parentCommissionVal = $netCommission * ($parentCommission->commission_value / 100); // Convert percentage to decimal
                        $commissions[$parentRole->name]['commission'] = $parentCommissionVal;
                        $commissions[$parentRole->name]['calculation_from'] = "Net Commission";
                        $commissions[$parentRole->name]['name'] = $parentUser->full_name;
                        $commissions[$parentRole->name]['user_id'] = $parentUser->id;
                        //calculation type
                        $commissions[$parentRole->name]['calculation_type'] = $parentCommission->commission_type;
                        $commissions[$parentRole->name]['commission_category'] = "Indirect";
                        //calculation value
                        $commissions[$parentRole->name]['calculation_value'] = $parentCommission->commission_value;


                    } elseif ($parentCommission->commission_type === 'Percentage' && $parentCommission->calculation_from === 'Remaining Amount') {

                        $parentCommissionVal = $remainingAmount * ($parentCommission->commission_value / 100); // Convert percentage to decimal
                        $commissions[$parentRole->name]['commission'] = $parentCommissionVal;
                        $commissions[$parentRole->name]['calculation_from'] = "Remaining Amount";
                        $commissions[$parentRole->name]['name'] = $parentUser->full_name;
                        $commissions[$parentRole->name]['user_id'] = $parentUser->id;
                        //calculation type
                        $commissions[$parentRole->name]['commission_category'] = "Indirect";
                        $commissions[$parentRole->name]['calculation_type'] = $parentCommission->commission_type;
                        //calculation value
                        $commissions[$parentRole->name]['calculation_value'] = $parentCommission->commission_value;


                    } elseif ($parentCommission->commission_type === 'Fixed' && $parentCommission->calculation_from === 'Net Commission') {

                        $commissions[$parentRole->name]['commission'] = $parentCommission->commission_value;
                        $commissions[$parentRole->name]['calculation_from'] = "Net Commission";
                        $commissions[$parentRole->name]['name'] = $parentUser->full_name;
                        $commissions[$parentRole->name]['user_id'] = $parentUser->id;
                        //calculation type
                        $commissions[$parentRole->name]['calculation_type'] = $parentCommission->commission_type;
                        $commissions[$parentRole->name]['commission_category'] = "Indirect";
                        //calculation value
                        $commissions[$parentRole->name]['calculation_value'] = $parentCommission->commission_value;

                    } elseif ($parentCommission->commission_type === 'Fixed' && $parentCommission->calculation_from === 'Remaining Amount') {

                        $commissions[$parentRole->name]['commission'] = $parentCommission->commission_value;

                        $commissions[$parentRole->name]['calculation_from'] = "Net Commission";
                        $commissions[$parentRole->name]['name'] = $parentUser->full_name;
                        $commissions[$parentRole->name]['user_id'] = $parentUser->id;
                        // calculation type
                        $commissions[$parentRole->name]['calculation_type'] = $parentCommission->commission_type;
                        $commissions[$parentRole->name]['commission_category'] = "Indirect";
                        // calculation value
                        $commissions[$parentRole->name]['calculation_value'] = $parentCommission->commission_value;


                    } else {
                        $parentCommissionVal = $netCommission * ($parentCommission->commission_value / 100); // Convert percentage to decimal
                        $commissions[$parentRole->name]['commission'] = $parentCommissionVal;
                        $commissions[$parentRole->name]['name'] = $parentUser->full_name;
                        $commissions[$parentRole->name]['user_id'] = $parentUser->id;
                        $commissions[$parentRole->name]['commission_category'] = "Indirect";


                    }
                }
            }

        }
        //dd($commissions);
        $dealSplitCommission = [
            'deal_id' => $deal->id,
            'user_id' => $deal->agent_id,
            'commission' => $commissionVal,
            'calculation_type' => $agentCommission?->commission_type,
            'calculation_value' => $agentCommission?->commission_value,
            'commission_category' => "Direct",
            'created_by_id' => $deal->created_by_id,
        ];
        $commissions['agent_commission'] = $dealSplitCommission;

        foreach ($commissions as $commission) {
            if ($commission['user_id']) {
                DealSplitCommission::updateOrCreate(
                    [
                        'deal_id' => $deal->id,
                        'user_id' => $commission['user_id'],
                    ],
                    [
                        'commission' => $commission['commission'],
                        'commission_type' => $commission['calculation_type'] ?? null,
                        'commission_value' => $commission['calculation_value'] ?? null,
                        'commission_category' => $commission['commission_category'] ?? 'INDIRECT', //INDIRECT IS HARDCODED NEED TO REMOVE
                    ]
                );

            }


        }

        $commission = DealSplitCommission::where('deal_id', $deal->id)->with('user')->get();
        //dd($commissions);
        return response()->json(['status' => '1', 'data' => $commission]);
    }

    public function getDealCommissionCalculationsBackup(Request $request)
    {
        $request->validate([
            'deal_id' => 'required|exists:deals,id',
        ]);
        $deal = Deal::find($request->deal_id);
        $netCommission = $deal->net_commission;
        $agent = User::with('parent')->find($deal->agent_id);

        $commissions = [];
        $parentHierarchy = getUserParentOnly($deal->agent_id);

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

            $remainingAmount = $netCommission - $commissionVal;
            foreach ($parentHierarchy as $parent) {
                $parentUser = User::find($parent);
                $parentRole = $parentUser->roles->first();
                $parentCommission = CommissionSlab::whereHas('commissionSlabUsers', function ($q) use ($parent) {
                    $q->where('user_id', $parent);
                })->where('commission_category', 'Indirect')->first();

                if (empty($parentCommission)) {
                    if ($parentRole) {
                        $parentCommission = CommissionSlab::whereHas('commissionSlabUsers', function ($q) use ($parentRole) {
                            $q->where('role_id', $parentRole->id);
                        })->where('commission_category', 'Indirect')->first();

                        if ($parentCommission) {

                            if ($parentCommission->commission_type === 'Percentage' && $parentCommission->calculation_from === 'Net Commission') {

                                $parentCommissionVal = $netCommission * ($parentCommission->commission_value / 100); // Convert percentage to decimal
                                $commissions[$parentRole->name]['commission'] = $parentCommissionVal;
                                $commissions[$parentRole->name]['calculation_from'] = "Net Commission";
                                $commissions[$parentRole->name]['user_id'] = $parent;
                                $commissions[$parentRole->name]['calculation_type'] = $parentCommission->commission_type;
                                $commissions[$parentRole->name]['calculation_value'] = $parentCommission->commission_value;
                                $remainingAmount = $remainingAmount - $parentCommissionVal;
                                $commissions[$parentRole->name]['commission_category'] = "Indirect";

                            } elseif ($parentCommission->commission_type === 'Percentage' && $parentCommission->calculation_from === 'Remaining Amount') {
                                $parentCommissionVal = $remainingAmount * ($parentCommission->commission_value / 100); // Convert percentage to decimal
                                $commissions[$parentRole->name]['commission'] = $parentCommissionVal;
                                $commissions[$parentRole->name]['calculation_from'] = "Remaining Amount";
                                $commissions[$parentRole->name]['calculation_type'] = $parentCommission->commission_type;
                                $commissions[$parentRole->name]['user_id'] = $parent;
                                $commissions[$parentRole->name]['calculation_value'] = $parentCommission->commission_value;
                                $commissions[$parentRole->name]['commission_category'] = "Indirect";

                            } elseif ($parentCommission->commission_type === 'Fixed' && $parentCommission->calculation_from === 'Net Commission') {

                                $commissions[$parentRole->name]['commission'] = $parentCommission->commission_value;
                                $commissions[$parentRole->name]['calculation_from'] = "Net Commission";
                                $commissions[$parentRole->name]['calculation_type'] = $parentCommission->commission_type;
                                $commissions[$parentRole->name]['user_id'] = $parent;
                                $commissions[$parentRole->name]['calculation_value'] = $parentCommission->commission_value;
                                $remainingAmount = $remainingAmount - $parentCommission->commission_value;
                                $commissions[$parentRole->name]['commission_category'] = "Indirect";
                            } elseif ($parentCommission->commission_type === 'Fixed' && $parentCommission->calculation_from === 'Remaining Amount') {

                                $commissions[$parentRole->name]['commission'] = $parentCommission->commission_value;
                                $commissions[$parentRole->name]['user_id'] = $parent;
                                $commissions[$parentRole->name]['calculation_from'] = "Net Commission";
                                $commissions[$parentRole->name]['calculation_type'] = $parentCommission->commission_type;
                                $commissions[$parentRole->name]['calculation_value'] = $parentCommission->commission_value;
                                $commissions[$parentRole->name]['commission_category'] = "Indirect";
                            } else {
                                $parentCommissionVal = $netCommission * ($parentCommission->commission_value / 100); // Convert percentage to decimal
                                $commissions[$parentRole->name] = $parentCommissionVal;
                                $commissions[$parentRole->name]['commission_category'] = "Indirect";
                                $commissions[$parentRole->name]['user_id'] = $parentUser->id;
                            }
                        } else {
                            $commissions[$parentRole->name]['commission'] = 0;
                            $commissions[$parentRole->name]['commission_category'] = "Indirect";
                            $commissions[$parentRole->name]['user_id'] = $parentUser->id;
                        }

                    } else {
                        $commissions[$parentRole->name]['commission'] = 0;
                        $commissions[$parentRole->name]['commission_category'] = "Indirect";
                        $commissions[$parentRole->name]['user_id'] = $parentUser->id;
                    }
                } else {

                    if ($parentCommission->commission_type === 'Percentage' && $parentCommission->calculation_from === 'Net Commission') {

                        $parentCommissionVal = $netCommission * ($parentCommission->commission_value / 100);
                        // Convert percentage to decimal
                        $commissions[$parentRole->name]['user_id'] = $parent;
                        $commissions[$parentRole->name]['commission'] = $parentCommissionVal;
                        $commissions[$parentRole->name]['calculation_from'] = "Net Commission";
                        $commissions[$parentRole->name]['calculation_type'] = $parentCommission->commission_type;
                        $commissions[$parentRole->name]['calculation_value'] = $parentCommission->commission_value;
                        $remainingAmount = $remainingAmount - $parentCommissionVal;
                        $commissions[$parentRole->name]['commission_category'] = "Indirect";


                    } elseif ($parentCommission->commission_type === 'Percentage' && $parentCommission->calculation_from === 'Remaining Amount') {

                        $parentCommissionVal = $remainingAmount * ($parentCommission->commission_value / 100); // Convert percentage to decimal
                        $commissions[$parentRole->name]['commission'] = $parentCommissionVal;
                        $commissions[$parentRole->name]['calculation_from'] = "Remaining Amount";
                        $commissions[$parentRole->name]['user_id'] = $parent;
                        $commissions[$parentRole->name]['calculation_type'] = $parentCommission->commission_type;
                        $commissions[$parentRole->name]['calculation_value'] = $parentCommission->commission_value;
                        $commissions[$parentRole->name]['commission_category'] = "Indirect";


                    } elseif ($parentCommission->commission_type === 'Fixed' && $parentCommission->calculation_from === 'Net Commission') {
                        $commissions[$parentRole->name]['user_id'] = $parent;
                        $commissions[$parentRole->name]['commission'] = $parentCommission->commission_value;
                        $commissions[$parentRole->name]['calculation_from'] = "Net Commission";
                        $commissions[$parentRole->name]['calculation_type'] = $parentCommission->commission_type;
                        $commissions[$parentRole->name]['calculation_value'] = $parentCommission->commission_value;
                        $remainingAmount = $remainingAmount - $parentCommission->commission_value;
                        $commissions[$parentRole->name]['commission_category'] = "Indirect";
                    } elseif ($parentCommission->commission_type === 'Fixed' && $parentCommission->calculation_from === 'Remaining Amount') {

                        $commissions[$parentRole->name]['commission'] = $parentCommission->commission_value;

                        $commissions[$parentRole->name]['calculation_from'] = "Net Commission";
                        $commissions[$parentRole->name]['user_id'] = $parent;
                        $commissions[$parentRole->name]['calculation_type'] = $parentCommission->commission_type;
                        $commissions[$parentRole->name]['calculation_value'] = $parentCommission->commission_value;
                        $commissions[$parentRole->name]['commission_category'] = "Indirect";

                    } else {
                        $parentCommissionVal = $netCommission * ($parentCommission->commission_value / 100); // Convert percentage to decimal
                        $commissions[$parentRole->name]['commission'] = $parentCommissionVal;
                        $commissions[$parentRole->name]['commission_category'] = "Indirect";
                        $commissions[$parentRole->name]['user_id'] = $parentUser->id;

                    }
                }
            }

        } else {
            // Calculating Commission Here
            if ($agentCommission->commission_type === 'Percentage') {
                $commissionVal = $netCommission * ($agentCommission->commission_value / 100); // Convert percentage to decimal
            } elseif ($agentCommission->commission_type === 'Fixed') {
                $commissionVal = $agentCommission->commission_value; // Use the fixed value
            } else {
                // Handle other types of commission structures if needed
                $commissionVal = 0; // Default to zero if the type is not recognized
            }
            $remainingAmount = $netCommission - $commissionVal;
            foreach ($parentHierarchy as $parent) {
                $parentUser = User::with("roles:id,name")->find($parent);
                $parentCommission = CommissionSlab::whereHas('commissionSlabUsers', function ($q) use ($parent) {
                    $q->where('user_id', $parent);
                })->where('commission_category', 'Indirect')->first();
                $parentRole = $parentUser->roles->first();

                if (empty($parentCommission)) {

                    //$parentRole = $parentUser->roles->first();
                    // dd($parentRole->name);
                    if ($parentRole) {

                        $parentCommission = CommissionSlab::whereHas('commissionSlabUsers', function ($q) use ($parentRole) {
                            $q->where('role_id', $parentRole->id);
                        })->where('commission_category', 'Indirect')->first();

                        if ($parentCommission) {

                            if ($parentCommission->commission_type === 'Percentage' && $parentCommission->calculation_from === 'Net Commission') {
                                $parentCommissionVal = $netCommission * ($parentCommission->commission_value / 100); // Convert percentage to decimal

                                $remainingAmount = $remainingAmount - $parentCommissionVal;
                                $commissions[$parentRole->name]['commission'] = $parentCommissionVal;
                                $commissions[$parentRole->name]['calculation_from'] = "Net Commission";
                                //calculation type
                                $commissions[$parentRole->name]['calculation_type'] = $parentCommission->commission_type;
                                $commissions[$parentRole->name]['commission_category'] = "Indirect";

                                //calculation value
                                $commissions[$parentRole->name]['calculation_value'] = $parentCommission->commission_value;
                                // user id
                                $commissions[$parentRole->name]['user_id'] = $parent;


                            } elseif ($parentCommission->commission_type === 'Percentage' && $parentCommission->calculation_from === 'Remaining Amount') {

                                $parentCommissionVal = $remainingAmount * ($parentCommission->commission_value / 100); // Convert percentage to decimal
                                //dd($parentRole->name);
                                $commissions[$parentRole->name]['commission'] = $parentCommissionVal;
                                $commissions[$parentRole->name]['name'] = $parentUser->full_name;
                                $commissions[$parentRole->name]['user_id'] = $parentUser->id;
                                $commissions[$parentRole->name]['calculation_from'] = "Remaining Amount";
                                //calculation type
                                $commissions[$parentRole->name]['commission_category'] = "Indirect";
                                $commissions[$parentRole->name]['calculation_type'] = $parentCommission->commission_type;
                                //calculation value
                                $commissions[$parentRole->name]['calculation_value'] = $parentCommission->commission_value;

                            } elseif ($parentCommission->commission_type === 'Fixed' && $parentCommission->calculation_from === 'Net Commission') {

                                $commissions[$parentRole->name]['commission'] = $parentCommission->commission_value;
                                $commissions[$parentRole->name]['calculation_from'] = "Net Commission";
                                $commissions[$parentRole->name]['name'] = $parentUser->full_name;
                                $commissions[$parentRole->name]['user_id'] = $parentUser->id;
                                //calculation type
                                $commissions[$parentRole->name]['calculation_type'] = $parentCommission->commission_type;
                                $commissions[$parentRole->name]['commission_category'] = "Indirect";
                                //calculation value
                                $commissions[$parentRole->name]['calculation_value'] = $parentCommission->commission_value;
                            } elseif ($parentCommission->commission_type === 'Fixed' && $parentCommission->calculation_from === 'Remaining Amount') {

                                $commissions[$parentRole->name]['commission'] = $parentCommission->commission_value;
                                $commissions[$parentRole->name]['name'] = $parentUser->full_name;
                                $commissions[$parentRole->name]['user_id'] = $parentUser->id;

                                $commissions[$parentRole->name]['calculation_from'] = "Net Commission";
                                //calculation type
                                $commissions[$parentRole->name]['calculation_type'] = $parentCommission->commission_type;
                                $commissions[$parentRole->name]['commission_category'] = "Indirect";
                                //calculation value
                                $commissions[$parentRole->name]['calculation_value'] = $parentCommission->commission_value;

                            } else {
                                $parentCommissionVal = $netCommission * ($parentCommission->commission_value / 100); // Convert percentage to decimal
                                $commissions[$parentRole->name]['commission'] = $parentCommissionVal;
                                $commissions[$parentRole->name]['name'] = $parentUser->full_name;
                                $commissions[$parentRole->name]['user_id'] = $parentUser->id;
                                $commissions[$parentRole->name]['commission_category'] = "Indirect";
                            }
                        } else {
                            $commissions[$parentRole->name]['commission'] = 0;
                            $commissions[$parentRole->name]['user_id'] = $parentUser->id;
                            // $commissions[$parentRole->name]['name'] = $parentUser->full_name;
                            // $commissions[$parentRole->name]['user_id'] = $parentUser->id;
                        }

                    } else {
                        $commissions[$parentRole->name]['commission'] = 0;
                        $commissions[$parentRole->name]['user_id'] = $parentUser->id;
                        // $commissions[$parentRole->name]['name'] = $parentUser->full_name;
                        // $commissions[$parentRole->name]['user_id'] = $parentUser->id;
                    }
                } else {

                    if ($parentCommission->commission_type === 'Percentage' && $parentCommission->calculation_from === 'Net Commission') {

                        $parentCommissionVal = $netCommission * ($parentCommission->commission_value / 100); // Convert percentage to decimal
                        $commissions[$parentRole->name]['commission'] = $parentCommissionVal;
                        $commissions[$parentRole->name]['calculation_from'] = "Net Commission";
                        $commissions[$parentRole->name]['name'] = $parentUser->full_name;
                        $commissions[$parentRole->name]['user_id'] = $parentUser->id;
                        //calculation type
                        $commissions[$parentRole->name]['calculation_type'] = $parentCommission->commission_type;
                        $commissions[$parentRole->name]['commission_category'] = "Indirect";
                        //calculation value
                        $commissions[$parentRole->name]['calculation_value'] = $parentCommission->commission_value;


                    } elseif ($parentCommission->commission_type === 'Percentage' && $parentCommission->calculation_from === 'Remaining Amount') {

                        $parentCommissionVal = $remainingAmount * ($parentCommission->commission_value / 100); // Convert percentage to decimal
                        $commissions[$parentRole->name]['commission'] = $parentCommissionVal;
                        $commissions[$parentRole->name]['calculation_from'] = "Remaining Amount";
                        $commissions[$parentRole->name]['name'] = $parentUser->full_name;
                        $commissions[$parentRole->name]['user_id'] = $parentUser->id;
                        //calculation type
                        $commissions[$parentRole->name]['commission_category'] = "Indirect";
                        $commissions[$parentRole->name]['calculation_type'] = $parentCommission->commission_type;
                        //calculation value
                        $commissions[$parentRole->name]['calculation_value'] = $parentCommission->commission_value;


                    } elseif ($parentCommission->commission_type === 'Fixed' && $parentCommission->calculation_from === 'Net Commission') {

                        $commissions[$parentRole->name]['commission'] = $parentCommission->commission_value;
                        $commissions[$parentRole->name]['calculation_from'] = "Net Commission";
                        $commissions[$parentRole->name]['name'] = $parentUser->full_name;
                        $commissions[$parentRole->name]['user_id'] = $parentUser->id;
                        //calculation type
                        $commissions[$parentRole->name]['calculation_type'] = $parentCommission->commission_type;
                        $commissions[$parentRole->name]['commission_category'] = "Indirect";
                        //calculation value
                        $commissions[$parentRole->name]['calculation_value'] = $parentCommission->commission_value;

                    } elseif ($parentCommission->commission_type === 'Fixed' && $parentCommission->calculation_from === 'Remaining Amount') {

                        $commissions[$parentRole->name]['commission'] = $parentCommission->commission_value;

                        $commissions[$parentRole->name]['calculation_from'] = "Net Commission";
                        $commissions[$parentRole->name]['name'] = $parentUser->full_name;
                        $commissions[$parentRole->name]['user_id'] = $parentUser->id;
                        // calculation type
                        $commissions[$parentRole->name]['calculation_type'] = $parentCommission->commission_type;
                        $commissions[$parentRole->name]['commission_category'] = "Indirect";
                        // calculation value
                        $commissions[$parentRole->name]['calculation_value'] = $parentCommission->commission_value;


                    } else {
                        $parentCommissionVal = $netCommission * ($parentCommission->commission_value / 100); // Convert percentage to decimal
                        $commissions[$parentRole->name]['commission'] = $parentCommissionVal;
                        $commissions[$parentRole->name]['name'] = $parentUser->full_name;
                        $commissions[$parentRole->name]['user_id'] = $parentUser->id;
                        $commissions[$parentRole->name]['commission_category'] = "Indirect";


                    }
                }
            }

        }
        //dd($commissions);
        $dealSplitCommission = [
            'deal_id' => $deal->id,
            'user_id' => $deal->agent_id,
            'commission' => $commissionVal,
            'calculation_type' => $agentCommission->commission_type,
            'calculation_value' => $agentCommission->commission_value,
            'commission_category' => "Direct",
            'created_by_id' => $deal->created_by_id,
        ];
        $commissions['agent_commission'] = $dealSplitCommission;

        foreach ($commissions as $commission) {
            if ($commission['user_id']) {
                DealSplitCommission::updateOrCreate(
                    [
                        'deal_id' => $deal->id,
                        'user_id' => $commission['user_id'],
                    ],
                    [
                        'commission' => $commission['commission'],
                        'commission_type' => $commission['calculation_type'] ?? null,
                        'commission_value' => $commission['calculation_value'] ?? null,
                        'commission_category' => $commission['commission_category'] ?? null,
                    ]
                );

            }


        }

        $commission = DealSplitCommission::where('deal_id', $deal->id)->with('user')->get();
        //dd($commissions);
        return response()->json(['status' => '1', 'data' => $commission]);
    }

    public function deleteDealSplitCommission($id)
    {
        $dealSplitCommission = DealSplitCommission::find($id);
        if ($dealSplitCommission) {
            $dealSplitCommission->delete();
            return response()->json(['status' => '1', 'msg' => 'Commission Deleted']);
        } else {
            return response()->json(['status' => '0', 'msg' => 'Commission Not Found']);
        }
    }

    public function addDealLead(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'deal_id' => 'required|exists:deals,id',
            'lead_id' => 'required|exists:leads,id',
            'lead_type' => 'required|in:Primary,Secondary'
        ]);
        try {
            if (DB::table('deal_leads')->where('deal_id', $request->deal_id)->where('lead_id', $request->lead_id)->exists()) {
                return response()->json(['status' => '0', 'msg' => 'Lead Already Added']);
            }
            $deal = Deal::find($request->deal_id);
            $lead_type = $request->lead_type; //DealLead::where('deal_id', $request->deal_id)->count() > 0 ? 'Secondary' : 'Primary';
            $deal->leads()->attach($request->lead_id, ['created_by_id' => auth()->user()->id, 'lead_type' => $lead_type]);
            return response()->json(['status' => '1', 'msg' => 'Lead Added']);
        } catch (Exception $e) {
            // Handle any exceptions here
            return response()->json(['status' => '0', 'msg' => 'An error occurred']);
        }
    }

    public function getDealTasks(Request $request)
    {
        $request->validate([
            'deal_id' => 'required|exists:deals,id',
            'limit' => 'nullable|integer',
            'status' => [
                'nullable',
                Rule::in(['PENDING', 'RUNNING', 'COMPLETED', 'CANCELED'])
            ]
        ]);


        try {
            $tasks = DealTask::where('deal_id', $request->deal_id)
                ->when($request->status, function ($q) use ($request) {
                    $q->where('status', $request->status);
                })
                ->when($request->limit, function ($q) use ($request) {
                    $q->limit($request->limit);
                })->with('user')->get();
            if ($tasks) {
                return response()->json(['status' => '1', 'msg' => 'Tasks Found', 'data' => $tasks]);
            } else {
                return response()->json(['status' => '0', 'msg' => 'Tasks Not Found']);
            }
        } catch (Exception $e) {
            // Handle any exceptions here
            return response()->json(['status' => '0', 'msg' => 'An error occurred']);
        }
    }

    public function storeDealTask(Request $request)
    {
        $request->validate([
            'deal_id' => 'required|exists:deals,id',
            'contact_id' => 'nullable|integer',
            'user_id' => 'nullable|integer',
            'subject' => 'required|string',
            'description' => 'required|string',
            'due_date' => 'nullable',
            'priority' => [
                'nullable',
                Rule::in(['low', 'medium', 'high'])
            ],
            'limit' => 'nullable|integer',
            'status' => [
                'nullable',
                Rule::in(['PENDING', 'RUNNING', 'COMPLETED', 'CANCELED'])
            ]
        ]);


        try {
            $tasks = new DealTask();
            $tasks->deal_id = $request->deal_id;
            $tasks->contact_id = $request->contact_id;
            $tasks->user_id = $request->user_id;
            $tasks->subject = $request->subject;
            $tasks->description = $request->description;
            $tasks->due_date = $request->due_date;
            $tasks->status = 'PENDING';
            $tasks->created_by_id = auth()->user()->id;
            if ($tasks->save()) {
                return response()->json(['status' => '1', 'msg' => 'Tasks created successfully', 'data' => $tasks]);
            } else {
                return response()->json(['status' => '0', 'msg' => 'Tasks Not Found']);
            }
        } catch (Exception $e) {
            // Handle any exceptions here
            return response()->json(['status' => '0', 'msg' => 'An error occurred']);
        }
    }

    public function getDealNotes(Request $request)
    {
        $request->validate([
            'deal_id' => 'required|exists:deals,id',
            'step' => 'required|numeric',
        ]);

        try {
            $step = $request->step;
            $notes = DealNote::where('deal_id', $request->deal_id)
                ->where(function ($q) use ($step) {
                    $q->where('step', $step)->orWhereNull('step');
                })
                ->with('createdBy')->get();
            if ($notes) {
                return response()->json(['status' => '1', 'msg' => 'Notes Found', 'data' => $notes]);
            } else {
                return response()->json(['status' => '0', 'msg' => 'Notes Not Found']);
            }
        } catch (Exception $e) {
            // Handle any exceptions here
            return response()->json(['status' => '0', 'msg' => 'An error occurred']);
        }
    }

    public function getDealActivityLogs(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:deals,id',
        ]);
        try {
            // Retrieve the activity logs for the deal
            $commission_source = DealCommissionSource::where('deal_id', $request->id)->pluck('id')->toArray();
            $splitIds = DealSplit::where('deal_id', $request->id)->pluck('id')->toArray();
            $splitCommissionIds = DealSplitCommission::where('deal_id', $request->id)->pluck('id')->toArray();
            $dealMetaIds = DealMeta::where('deal_id', $request->id)->pluck('id')->toArray();
            $dealLeadIds = DealLead::where('deal_id', $request->id)->pluck('id')->toArray();
            $dealTaskIds = DealTask::where('deal_id', $request->id)->pluck('id')->toArray();
            $dealDeductibleIds = DealDeductible::where('deal_id', $request->id)->pluck('id')->toArray();

            $activityLogs = Activity::where(function ($q) use ($request) {
                $q->where("subject_type", Deal::class);
                $q->where("subject_id", $request->id);
            })->orWhere(function ($q) use ($commission_source) {
                $q->where("subject_type", DealCommissionSource::class);
                $q->whereIn("subject_id", $commission_source);
            })->orWhere(function ($q) use ($splitIds) {
                $q->where("subject_type", DealSplit::class);
                $q->whereIn("subject_id", $splitIds);
            })->orWhere(function ($q) use ($splitCommissionIds) {
                $q->where("subject_type", DealSplitCommission::class);
                $q->whereIn("subject_id", $splitCommissionIds);
            })->orWhere(function ($q) use ($dealMetaIds) {
                $q->where("subject_type", DealMeta::class);
                $q->whereIn("subject_id", $dealMetaIds);
            })->orWhere(function ($q) use ($dealLeadIds) {
                $q->where("subject_type", DealLead::class);
                $q->whereIn("subject_id", $dealLeadIds);
            })->orWhere(function ($q) use ($dealTaskIds) {
                $q->where("subject_type", DealTask::class);
                $q->whereIn("subject_id", $dealTaskIds);
            })->orWhere(function ($q) use ($dealDeductibleIds) {
                $q->where("subject_type", DealDeductible::class);
                $q->whereIn("subject_id", $dealDeductibleIds);
            })
                ->latest()
                ->get();

            $data = [];
            foreach ($activityLogs as $activityLog) {
                $properties['old'] = isset($activityLog->properties['old']) ? dealColumnsReadable($activityLog?->properties['old']) : [];
                $properties['attributes'] = isset($activityLog->properties['attributes']) ? dealColumnsReadable($activityLog?->properties['attributes']) : [];
                $activityLog->properties = $properties;
                $data[] = $activityLog;
            }

            return response()->json(['status' => '1', 'msg' => 'Deal Activity Logs Found', 'data' => $data]);
        } catch (\Exception $e) {
            // Handle any exceptions here
            return response()->json(['status' => '0', 'msg' => 'An error occurred', 'error' => $e->getMessage()]);
        }
    }


    public function getDealMedia(Request $request)
    {
        $request->validate([
            'deal_id' => 'required|exists:deals,id',
        ]);
        try {
            $media = DealMedia::where('deal_id', $request->deal_id)->where("model_id", $request->deal_id)->where("model_type", Deal::class)->with('createdBy')->get();
            if ($media) {
                return response()->json(['status' => '1', 'msg' => 'Media Found', 'data' => $media]);
            } else {
                return response()->json(['status' => '0', 'msg' => 'Media Not Found']);
            }
        } catch (Exception $e) {
            // Handle any exceptions here
            return response()->json(['status' => '0', 'msg' => 'An error occurred']);
        }
    }

    public function getDealType(Request $request)
    {

        $dealType = DealType::where('status', '1')->get();
        return response()->json(['status' => '1', 'msg' => 'Lead Types Found', 'data' => $dealType]);

    }

    public function updateLead(Request $request)
    {

        // dd($request->secondary_first_name);
        if ($request->mode == 'new') {
            $validator = \Validator::make($request->all(), [
                'deal_id' => 'required|exists:deals,id',
                'agent_id' => 'required|exists:users,id',
                // 'first_name' => 'required|string',
                'last_name' => 'nullable|string',
                'passport_number' => 'nullable|string',
                'name_on_passport' => 'nullable|string',
                'passport_expiry' => 'nullable',
                'passport_issue_country' => 'nullable|exists:countries,id',
                'nationality' => 'nullable|string',
                'address' => 'nullable|string',
                'country_of_residence' => 'nullable|string',
                //'mobile' =>  'required|numeric|digits_between:8,15|unique:leads,mobile',
                'mobile' => 'required|string',
                'email' => 'required|email|unique:leads,email',
                'type' => 'nullable|string',
                'phone' => 'nullable|string',
                'nationality_id' => 'nullable|exists:countries,id',
                'source_id' => 'nullable|exists:sources,id',
                'medium_id' => 'nullable|exists:mediums,id',
                'gender' => 'nullable|string',
                'dob' => 'nullable',
                'place_of_birth' => 'nullable|string',
                'occupation' => 'nullable|string',
                'uae_national' => 'nullable|boolean',
                'emirates_id' => 'nullable|string',
                'emirates_id_expiry' => 'nullable|date',
                'other_documents' => 'nullable|array',
                'other_documents.*.file' => 'required|mimes:jpg,jpeg,png,pdf|max:2048',
                'other_documents.*.title' => 'required|string',
            ]);
        } else {
            $validator = \Validator::make($request->all(), [
                'id' => 'nullable|exists:leads,id',
                'deal_id' => 'nullable|exists:deals,id',
                'agent_id' => 'nullable|exists:users,id',
                // 'first_name' => 'required|string',
                'last_name' => 'nullable|string',
                'passport_number' => 'nullable|string',
                'name_on_passport' => 'nullable|string',
                'passport_expiry' => 'nullable',
                'passport_issue_country' => 'nullable|exists:countries,id',
                'nationality' => 'nullable|string',
                'address' => 'nullable|string',
                'country_of_residence' => 'nullable|string',
                // 'mobile' => 'required|numeric',
                'email' => 'required|email',
                'type' => 'nullable|string',
                'phone' => 'nullable|string',
                'nationality_id' => 'nullable|exists:countries,id',
                'source_id' => 'nullable|exists:sources,id',
                'gender' => 'nullable|string',
                'dob' => 'nullable',
                'place_of_birth' => 'nullable|string',
                'occupation' => 'nullable|string',
                'uae_national' => 'nullable|boolean',
                'emirates_id' => 'nullable|string',
                'emirates_id_expiry' => 'nullable|date',
                'other_documents' => 'nullable|array',
                'other_documents.*.file' => 'required|mimes:jpg,jpeg,png,pdf|max:2048',
                'other_documents.*.title' => 'required|string',
            ]);
        }
        if ($validator->fails()) {
            return response()->json(['status' => '2', 'errors' => $validator->errors()], 422);
        }


        try {
            if (($request->id != null || $request->id != '') && $request->mode != 'new') {

                $lead = Lead::find($request->id);

                $lead->first_name = $request->first_name;
                $lead->last_name = $request->last_name;
                $lead->gender = $request->gender;
                $lead->dob = date('Y-m-d', strtotime($request->dob));
                // $lead->mobile = validatePhoneNumber($request->mobile);
                $lead->phone = $request->phone ?? $lead->phone;
                $lead->whatsapp = $request->whatsapp ?? $lead->whatsapp;
                $lead->email = $request->email;
                $lead->address = $request->address;
                $lead->source_id = $request->source_id ? $request->source_id : $lead->source_id;
                $lead->lead_medium_id = $request->lead_medium_id ? $request->lead_medium_id : $lead->lead_medium_id;

                $lead->nationality_id = $request->nationality_id;
                $lead->save();

                $msg = 'Lead Updated Successfully';
            } else {

                $phoneLastEightDigits = substr($request->mobile, -8);

                $lead = Lead::where('mobile', 'like', '%' . $phoneLastEightDigits)->where('agent_id', $request->agent_id)->first();

                if ($lead) {
                    return response()->json(['status' => '0', 'msg' => 'Lead with this mobile number already exists ref[' . $lead->id . ']']);
                }


                $lead = new Lead();
                $lead->agent_id = $request->agent_id;
                $lead->first_name = $request->first_name;
                $lead->last_name = $request->last_name;
                $lead->gender = $request->gender;
                $lead->dob = date('Y-m-d', strtotime($request->dob));
                $lead->phone = $request->phone;
                $lead->mobile = $request->mobile;
                $lead->whatsapp = $request->whatsapp;
                $lead->mobile = $request->mobile;
                $lead->email = $request->email;
                $lead->address = $request->address;
                $lead->source_id = $request->source_id;
                $lead->lead_medium_id = $request->lead_medium_id;
                $lead->nationality_id = $request->nationality_id;
                $lead->save();
                $msg = 'Lead Created Successfully';
                $deal = Deal::find($request->deal_id);
                $deal->leads()->attach($lead->id);

                $dealLead = DealLead::where('deal_id', $request->deal_id)->where('lead_id', $lead->id)->first();
                $dealLead->lead_type = 'Primary';
                $dealLead->created_by_id = auth()->user()->id;
                $dealLead->save();


                //lead note

                $leadNote = new LeadNote();

                $leadNote->lead_id = $lead->id;
                $leadNote->note = 'Lead Created';
                $leadNote->created_by_id = auth()->user()->id;
                $leadNote->save();






            }
            $leadId = $lead->id;


            // Storing Meta data here
            LeadMeta::storeLeadMeta($leadId, "passport_number", $request->passport_number != '' ? $request->passport_number : null);
            LeadMeta::storeLeadMeta($leadId, "name_on_passport", $request->name_on_passport != '' ? $request->name_on_passport : null);
            LeadMeta::storeLeadMeta($leadId, "emirates_id", $request->emirates_id ?? null);
            LeadMeta::storeLeadMeta($leadId, "passport_issue_date", $request->passport_issue_date ?? null);
            LeadMeta::storeLeadMeta($leadId, "passport_issue_country", $request->passport_issue_country ?? null);
            LeadMeta::storeLeadMeta($leadId, "passport_expiry", $request->passport_expiry ?? null);
            LeadMeta::storeLeadMeta($leadId, "years_in_role", $request->years_in_role ?? null);
            LeadMeta::storeLeadMeta($leadId, "establishment_address", $request->establishment_address ?? null);
            LeadMeta::storeLeadMeta($leadId, "politically_exposed_person", $request->politically_exposed_person ?? false);
            LeadMeta::storeLeadMeta($leadId, "name_on_passport", $request->name_on_passport ?? null);
            LeadMeta::storeLeadMeta($leadId, "emirates_id_expiry", $request->emirates_id_expiry ?? null);
            LeadMeta::storeLeadMeta($leadId, "uae_national", $request->uae_national ?? false);
            LeadMeta::storeLeadMeta($leadId, "occupation", $request->occupation ?? null);
            LeadMeta::storeLeadMeta($leadId, "place_of_birth", $request->place_of_birth ?? null);
            LeadMeta::storeLeadMeta($leadId, "country_of_residence", $request->country_of_residence ?? null);
            LeadMeta::storeLeadMeta($leadId, "type", !empty($request->leadtype) ? strtoupper($request->leadtype) : null);
            LeadMeta::storeLeadMeta($leadId, "aliases", $request->aliases ?? null);
            LeadMeta::storeLeadMeta($leadId, "have_ubo", $request->have_ubo ?? null);
            LeadMeta::storeLeadMeta($leadId, "leadtype", !empty($request->leadtype) ? strtoupper($request->leadtype) : null);
            LeadMeta::storeLeadMeta($leadId, "company_first_name", $request->company_first_name ?? null);
            // For View And Edit Mode (Property)
            LeadMeta::storeLeadMeta($leadId, "lead_mode", $request->lead_mode ?? null);
            // For View And Edit Mode (Property)
            LeadMeta::storeLeadMeta($leadId, "company_former_name", $request->company_former_name ?? null);
            LeadMeta::storeLeadMeta($leadId, "company_trading_name", $request->company_trading_name ?? null);
            LeadMeta::storeLeadMeta($leadId, "company_trade_license_no", $request->company_trade_license_no ?? null);
            LeadMeta::storeLeadMeta($leadId, "company_coi", $request->company_coi ?? null);
            LeadMeta::storeLeadMeta($leadId, "company_coi_date", $request->company_coi_date ?? null);
            LeadMeta::storeLeadMeta($leadId, "company_principal_address", $request->company_principal_address ?? null);
            LeadMeta::storeLeadMeta($leadId, "trade_license_issue_date", $request->trade_license_issue_date ?? null);
            LeadMeta::storeLeadMeta($leadId, "trade_license_expiry_date", $request->trade_license_expiry_date ?? null);

            ($request->uae_residency != null && $request->uae_residency != '') ? LeadMeta::storeLeadMeta($leadId, "uae_residency", $request->uae_residency ?? null) : '';
            ($request->residency_visa_no != null && $request->residency_visa_no != '') ? LeadMeta::storeLeadMeta($leadId, "residency_visa_no", $request->residency_visa_no ?? null) : '';
            ($request->visa_expiry_date != null && $request->visa_expiry_date != '') ? LeadMeta::storeLeadMeta($leadId, "visa_expiry_date", $request->visa_expiry_date ?? null) : '';
            ($request->fund_source != null && $request->fund_source != '') ? LeadMeta::storeLeadMeta($leadId, "fund_source", $request->fund_source ?? null) : '';
            ($request->payment_method != null && $request->payment_method != '') ? LeadMeta::storeLeadMeta($leadId, "payment_method", $request->payment_method ?? null) : '';
            ($request->year_of_experience != null && $request->year_of_experience != '') ? LeadMeta::storeLeadMeta($leadId, "year_of_experience", $request->year_of_experience ?? null) : '';
            ($request->employer_address != null && $request->employer_address != '') ? LeadMeta::storeLeadMeta($leadId, "employer_address", $request->employer_address ?? null) : '';
            ($request->employer_name != null && $request->employer_name != '') ? LeadMeta::storeLeadMeta($leadId, "employer_name", $request->employer_name ?? null) : '';
            ($request->annual_income != null && $request->annual_income != '') ? LeadMeta::storeLeadMeta($leadId, "annual_income", $request->annual_income ?? null) : '';
            ($request->political != null && $request->political != '') ? LeadMeta::storeLeadMeta($leadId, "political", $request->political ?? null) : '';
            if (gettype($request->other_documents) == 'array' && count($request->other_documents) > 0) {
                foreach ($request->other_documents as $document) {
                    $path = $this->uploadDocument($document['file'], "leads/{$lead->id}/{$document['file']->getClientOriginalName()}", 'local');
                    if (!empty($path)) {
                        LeadMedia::create([
                            "lead_id" => $leadId,
                            "type" => "Other",
                            "title" => $document["title"] ?? "Other",
                            "path" => $path,
                            "created_by_id" => auth()->id(),
                        ]);
                    }
                }
            }
            $leadWithMeta = Lead::with(['leadMeta', 'documents'])->find($leadId);


            foreach ($request->secondary_first_name ?? [] as $key => $value) {
                if ($request->secondary_lead_id[$key] == 0) {
                    $secondarylead = new Lead();
                    $secondarylead->agent_id = $request->agent_id;
                    $secondarylead->first_name = $value;
                    $secondarylead->last_name = $request->secondary_last_name[$key] ?? null;
                    $secondarylead->email = $request->secondary_email[$key] ?? null;
                    $secondarylead->mobile = $request->secondary_mobile[$key] ?? null;
                    $secondarylead->nationality_id = $request->secondary_nationality_id[$key] ?? null;
                    $secondarylead->save();

                    $deal = Deal::find($request->deal_id);
                    $deal->leads()->attach($secondarylead->id);

                    $dealLead = DealLead::where('deal_id', $request->deal_id)->where('lead_id', $secondarylead->id)->first();
                    $dealLead->lead_type = 'Secondary';
                    $dealLead->created_by_id = auth()->user()->id;
                    $dealLead->save();
                } else {
                    $secondarylead = Lead::find($request->secondary_lead_id[$key]);
                    $secondarylead->agent_id = $request->agent_id;
                    $secondarylead->first_name = $value;
                    $secondarylead->last_name = $request->secondary_last_name[$key] ?? null;
                    $secondarylead->email = $request->secondary_email[$key] ?? null;
                    $secondarylead->mobile = $request->secondary_mobile[$key] ?? null;
                    $secondarylead->nationality_id = $request->secondary_nationality_id[$key] ?? null;
                    $secondarylead->save();


                }

            }


            return response()->json(['status' => '1', 'msg' => $msg, 'data' => $leadWithMeta]);
        } catch (Exception $e) {
            // Handle any exceptions here
            return response()->json(['status' => '0', 'msg' => 'An error occurred']);
        }
    }

    public function storeCompanyUbo(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'lead_id' => 'nullable|exists:leads,id',
            'passport' => 'nullable|string',
            'passport_expiry' => 'nullable|date',
            'nationality' => 'nullable|string',
            'address' => 'nullable|string',
            'country_of_residence' => 'nullable|string',
            'mobile' => 'nullable',
            'phone' => 'nullable',
            'email' => 'required|email',
            'type' => 'nullable|string',
            'nationality_id' => 'nullable|exists:countries,id',
            'source_id' => 'nullable|exists:sources,id',
            'gender' => 'nullable|string',
            'dob' => 'nullable|date',
            'place_of_birth' => 'nullable|string',
            'occupation' => 'nullable|string',
            'uae_national' => 'nullable|boolean',
            'emirates_id' => 'nullable|string',
            'emirates_id_expiry' => 'nullable|date',

        ]);
        if ($validator->fails()) {
            return response()->json(['status' => '2', 'errors' => $validator->errors()], 422);
        }

        try {
            LeadMeta::storeLeadMeta($request->lead_id, "ubo", json_encode($request->all()));
            return response()->json(['status' => '1', 'msg' => 'UBO added successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => '0', 'msg' => 'An error occurred', 'error' => $e->getMessage()]);
        }
    }

    public function getUboList(Request $request)
    {

        $validator = \Validator::make($request->all(), [
            'id' => 'required|exists:leads,id'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => '2', 'errors' => $validator->errors()], 422);
        }
        try {
            $lead = Lead::find($request->id);
            $ubo = LeadMeta::where('lead_id', $request->id)->where('key', 'ubo')->get();
            if ($ubo) {
                $ubo = json_decode($ubo->value);
                return response()->json(['status' => '1', 'msg' => 'UBO found', 'data' => $ubo]);
            } else {
                return response()->json(['status' => '0', 'msg' => 'UBO not found']);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => '0', 'msg' => 'An error occurred']);
        }
    }

    public function updateContact(Request $request)
    {

        // dd($request->all());
        $validator = \Validator::make($request->all(), [
            'id' => 'required|exists:contacts,id',
            'deal_id' => 'nullable|exists:deals,id',
            'passport' => 'nullable|string',
            'passport_expiry' => 'nullable',
            'nationality' => 'nullable|string',
            'address' => 'nullable|string',
            'country_of_residence' => 'nullable|string',
            'mobile' => 'nullable',
            'phone' => 'nullable',
            'email' => 'required|email',
            'type' => 'nullable|string',
            'nationality_id' => 'nullable|exists:countries,id',
            'source_id' => 'nullable|exists:sources,id',
            'gender' => 'nullable|string',
            'dob' => 'nullable',
            'place_of_birth' => 'nullable|string',
            'occupation' => 'nullable|string',
            'uae_national' => 'nullable|boolean',
            'emirates_id' => 'nullable|string',
            'emirates_id_expiry' => 'nullable|date',
            'other_documents' => 'nullable|array',
            'other_documents.*.file' => 'required|mimes:jpg,jpeg,png,pdf|max:2048',
            'other_documents.*.title' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => '2', 'errors' => $validator->errors()], 422);
        }

        try {

            $contact = Contact::find($request->id);
            $contact->first_name = $request->first_name;
            $contact->last_name = $request->last_name;
            $contact->gender = $request->gender;
            $contact->dob = date('Y-m-d', strtotime($request->dob));
            $contact->passport = $request->passport;
            $contact->passport_expiry = date('Y-m-d', strtotime($request->passport_expiry));
            $contact->company = $request->company;
            $contact->website = $request->website;
            $contact->phone = $request->phone;
            $contact->mobile = validatePhoneNumber($request->mobile);
            $contact->whatsapp = $request->whatsapp;
            $contact->email = $request->email;
            $contact->address = $request->address;
            $contact->nationality_id = $request->nationality_id;
            $contact->save();

            $contactId = $contact->id;


            // Storing Meta data here
            ($request->type != null && $request->type != '') ? ContactMeta::storeContactMeta($contactId, "type", $request->type ?? null) : '';
            ($request->emirates_id != null && $request->emirates_id != '') ? ContactMeta::storeContactMeta($contactId, "emirates_id", $request->emirates_id ?? null) : '';
            ($request->emirates_id_expiry != null && $request->emirates_id_expiry != '') ? ContactMeta::storeContactMeta($contactId, "emirates_id_expiry", $request->emirates_id_expiry ?? null) : '';
            // ContactMeta::storeContactMeta($contactId, "passport_number", $request->passport_number != '' ? $request->passport_number : null);
            ($request->passport_issue_date != null && $request->passport_issue_date != '') ? ContactMeta::storeContactMeta($contactId, "passport_issue_date", $request->passport_issue_date ?? null) : '';
            //  ContactMeta::storeContactMeta($contactId, "passport_expiry", $request->passport_expiry ?? null);
            ($request->passport_country_id != null && $request->passport_country_id != '') ? ContactMeta::storeContactMeta($contactId, "passport_country_id", $request->passport_country_id ?? null) : '';
            ($request->uae_national != null && $request->uae_national != '') ? ContactMeta::storeContactMeta($contactId, "uae_national", $request->uae_national ?? false) : '';
            ($request->aliases != null && $request->aliases != '') ? ContactMeta::storeContactMeta($contactId, "aliases", $request->aliases ?? false) : '';
            ($request->pob != null && $request->pob != '') ? ContactMeta::storeContactMeta($contactId, "pob", $request->pob ?? false) : '';


            $metaData = $request->contact_meta ?? [];
            ContactMeta::where('contact_id', $contactId)->delete();
            foreach ($metaData as $key => $metaValue) {
                ContactMeta::storeContactMeta($contactId, $key, $metaValue);
            }

//            ContactMeta::storeContactMeta($contactId, "occupation", $request->occupation ?? null);
//            ContactMeta::storeContactMeta($contactId, "place_of_birth", $request->place_of_birth ?? null);
//            ContactMeta::storeContactMeta($contactId, "country_of_residence", $request->country_of_residence ?? null);
//            ContactMeta::storeContactMeta($contactId, "type", $request->type ?? null);

//            if (gettype($request->other_documents) == 'array' && count($request->other_documents) > 0) {
//                foreach ($request->other_documents as $document) {
//                    $path = $this->uploadDocument($document['file'], "contacts/{$contact->id}/{$document['file']->getClientOriginalName()}",'local');
//                    if (!empty($path)) {
//                        ContactMedia::create([
//                            "contact_id" => $contactId,
//                            "type" => "Other",
//                            "title" => $document["title"] ?? "Other",
//                            "path" => $path,
//                            "created_by_id" => auth()->id(),
//                        ]);
//                    }
//                }
//            }
            $contactWithMeta = Contact::with(['documents'])->find($contactId);
            Deal::where('id', $request->deal_id)->update(['state' => 'leads']);
            return response()->json(['status' => '1', 'message' => 'Contact updated successfully', 'data' => $contactWithMeta]);
        } catch (Exception $e) {
            // Handle any exceptions here
            return response()->json(['status' => '0', 'message' => 'An error occurred'], 500);
        }
    }

    public function updateContactDocuments(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'id' => 'required|exists:contacts,id',
            'other_documents' => 'nullable|array',
            'other_documents.*.file' => 'required|mimes:jpg,jpeg,png,pdf|max:2048',
            'other_documents.*.title' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => '2', 'errors' => $validator->errors()], 422);
        }

        try {
            if (gettype($request->other_documents) == 'array' && count($request->other_documents) > 0) {
                foreach ($request->other_documents as $document) {
                    $path = $this->uploadDocument($document['file'], "contacts/{$request->id}/{$document['file']->getClientOriginalName()}", 'local');
                    if (!empty($path)) {
                        ContactMedia::create([
                            "contact_id" => $request->id,
                            "type" => "Other",
                            "title" => $document["title"] ?? "Other",
                            "path" => $path,
                            "created_by_id" => auth()->id(),
                        ]);
                    }
                }
            }
            $contactWithMeta = Contact::with(['documents'])->find($request->id);
            return response()->json(['status' => '1', 'msg' => 'Contact documents updated successfully', 'data' => $contactWithMeta]);
        } catch (Exception $e) {
            // Handle any exceptions here
            return response()->json(['status' => '0', 'msg' => 'An error occurred']);
        }
    }


    public function getContact(Request $request)
    {
        $contact = Contact::where('id', $request->id)->with(['contactMeta', 'contactTypes', "dealMedia.createdBy", "dealMedia" => function($q){
            $q->where("deal_id", request("deal_id"));
        }])->first();
        return response()->json(['status' => '1', 'msg' => 'Contact Found', 'data' => $contact]);
    }

    public function updateDeal(Request $request)
    {


        $request->validate([
            'deal_id' => 'required|exists:deals,id',
            // 'deal_type_id' => 'required',
            // 'property_id' => 'required|exists:properties,id',
            //  'contact_id' => 'required|exists:contacts,id',
            'deal_status_id' => 'nullable|exists:deal_statuses,id',
            'deal_price' => 'nullable|string',
            'gross_commission' => 'nullable|string',
            'net_commission' => 'nullable|string',
            'initial_deposit' => 'nullable|numeric',
            'other_documents' => 'nullable|array',
            'other_documents.*.file' => 'required|mimes:jpg,jpeg,png,pdf|max:2048',
            'other_documents.*.title' => 'required|string',
        ]);

        try {

            $deal = Deal::find($request->deal_id);
            //   $deal->deal_type_id = $request->deal_type_id;
            //$deal->property_id = $request->property_id;
            // $deal->contact_id = $request->contact_id;
            $deal->deal_status_id = $request->deal_status_id;
            $deal->deal_price = preg_replace('/[^0-9.]/', '', $request->deal_price);
            // $deal->initial_deposit = preg_replace('/[^0-9.]/', '', $request->initial_deposit);
            $deal->net_commission = preg_replace('/[^0-9.]/', '', $request->net_commission);
            $deal->gross_commission = preg_replace('/[^0-9.]/', '', $request->gross_commission);
            $deal->save();

            $leadId = $deal->id;

//
//            // Storing Meta data here
//            LeadMeta::storeLeadMeta($leadId, "passport_number", $request->passport_number != '' ? $request->passport_number : null);
//            LeadMeta::storeLeadMeta($leadId, "emirates_id", $request->emirates_id ?? null);
//            LeadMeta::storeLeadMeta($leadId, "passport_expiry", $request->passport_expiry ?? null);
//
//            if (gettype($request->other_documents) == 'array' && count($request->other_documents) > 0) {
//                foreach ($request->other_documents as $document) {
//                    $path = $this->uploadDocument($document['file'], "leads/{$lead->id}/{$document['file']->getClientOriginalName()}");
//                    if (!empty($path)) {
//                        LeadMedia::create([
//                            "lead_id" => $leadId,
//                            "type" => "Other",
//                            "title" => $document["title"] ?? "Other",
//                            "path" => $path,
//                            "created_by_id" => auth()->id(),
//                        ]);
//                    }
//                }
//            }
//            $leadWithMeta = Lead::with(['leadMeta', 'documents'])->find($leadId);
            //$deal = $deal->load(['dealType', 'property', 'contact', 'dealStatus','agent','createdBy']);
            $deal = Deal::where('id', $deal->id)->with('dealType', 'dealStatus', 'agent', 'property', 'property.price', 'property.photos', 'contact', 'createdBy')->first();
            return response()->json(['status' => '1', 'msg' => 'Lead updated successfully', 'data' => $deal]);
        } catch (Exception $e) {
            // Handle any exceptions here
            return response()->json(['status' => '0', 'msg' => 'An error occurred']);
        }
    }


    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:deals',

        ]);

        $type = Deal::find($request->id);
        if ($type->delete()) {
            $result = array('status' => '1', 'msg' => 'Deal deleted successfully');
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function deleteMedia(Request $request)
    {

        $request->validate([
            'id' => 'required',
            'model' => 'required|in:deal,deals,lead,contact', // Validate that the model is either "deals" or "leads"
        ]);
        try {
            $table_list = array(
                'deals' => 'deal_media',
                'deal' => 'deal_media',
                'lead' => 'lead_media',
                'contact' => 'contact_media',
            );
            // Determine the table name based on the provided model
            $table = $table_list[$request->model];

            $media = DB::table($table)->find($request->id); // Use the appropriate table
            $environment = \App::environment();

            if ($media) {
                if ($environment === "production") {
                    $this->removeMediaFromBucket($media->path);
                }
                DB::table($table)->where('id', $request->id)->delete(); // Use the appropriate table
                $result = array('status' => '1', 'msg' => 'Deleted successfully');
            } else {
                $result = array('status' => '0', 'msg' => 'Media not found or Something went wrong!!');
            }
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }

        return json_encode($result);
    }


    public function deleteMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',

        ]);
        if (gettype($request->ids) == 'array' && count($request->ids) > 0) {
            $ids = $request->ids;
            if (Deal::whereIn('id', $ids)->delete()) {
                $result = array('status' => '1', 'msg' => count($ids) . ' Deals deleted successfully');
            } else {
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }
        }

        return json_encode($result);
    }

    public function deleteLead(Request $request)
    {
        $request->validate([
            'deal_id' => 'required|exists:deals,id',
            'lead_id' => 'required|exists:leads,id',
        ]);
        try {
            $deal = Deal::find($request->deal_id);
            $deal->leads()->detach($request->lead_id);

            $result = array('status' => '1', 'msg' => 'Lead deleted successfully');
            return json_encode($result);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            return json_encode($result);
        }
    }

    public function getLeadSourceList(): JsonResponse
    {
        $data = LeadSource::where('status', '1')->get();
        $result = array('status' => '1', 'msg' => "Lead Source fetched successfully.", 'data' => $data);
        return response()->json($result);
    }

    public function getLeadMediumList(): JsonResponse
    {
        $data = Medium::where('is_lead_medium', '1')->where('status', '1')->get();
        $result = array('status' => '1', 'msg' => "Lead Medium fetched successfully.", 'data' => $data);
        return response()->json($result);
    }

    public function addNote(Request $request)
    {
        $request->validate([
            'deal_id' => 'required|exists:deals,id',
            'note' => 'required|string',
            'step' => 'required|numeric',
        ]);

        try {
            $dealNote = DealNote::create([
                "deal_id" => $request->deal_id,
                "note" => $request->note,
                "step" => $request->step,
                "created_by_id" => auth()->id(),
            ]);
            // event(new DealNoteAdded($dealNote));
            $result = array('status' => '1', 'msg' => "Deal note added successfully.");
            return json_encode($result);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            return json_encode($result);
        }
    }

    public function getDealSplit(Request $request)
    {
        $request->validate([
            'split_id' => 'required|exists:deal_splits,id',
        ]);
        try {
            $dealSplit = DealSplit::where('id', $request->split_id)->with('user')->first();
            if (!empty($dealSplit)) {
                $result = array('status' => '1', 'msg' => "Deal split fetched successfully.", 'data' => $dealSplit);
                return response()->json($result);
            } else {
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
                return response()->json($result);
            }

        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            return response()->json($result);
        }
    }

    public function getDealSplits(Request $request)
    {
        $request->validate([
            'deal_id' => 'required|exists:deals,id',
        ]);
        try {
            $dealSplit = DealSplit::where('deal_id', $request->deal_id)->with('user')->get();
            if (!empty($dealSplit)) {
                $result = array('status' => '1', 'msg' => "Deal split fetched successfully.", 'data' => $dealSplit);
                return response()->json($result);
            } else {
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
                return response()->json($result);
            }

        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            return response()->json($result);
        }
    }

    public function removeDealSplit(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:deal_splits,id',
            'deal_id' => 'required|exists:deals,id',
        ]);
        try {
            $dealSplit = DealSplit::where('id', $request->id)->where('deal_id', $request->deal_id)->delete();
            if ($dealSplit) {
                $result = array('status' => '1', 'msg' => "Deal split removed successfully.");
                return response()->json($result);
            } else {
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
                return response()->json($result);
            }

        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            return response()->json($result);
        }
    }


    public function removeDealSplitCommission(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:deal_split_commissions,id',
        ]);
        try {
            if (DealSplitCommission::where('id', $request->id)->delete()) {
                $result = array('status' => '1', 'msg' => "Deal split removed successfully.");
                return response()->json($result);
            } else {
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
                return response()->json($result);
            }

        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            return response()->json($result);
        }
    }

    public function updateDealState(Request $request)
    {
        $request->validate([
            'deal_id' => 'required|exists:deals,id',
            'state' => 'required|in:initial,owner,leads,financial,attachments',
        ]);
        try {
            $deal = Deal::find($request->deal_id);
            $deal->state = $request->state;
            $deal->save();
            $result = array('status' => '1', 'msg' => "Deal state updated successfully.");
            return response()->json($result);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            return response()->json($result);
        }

    }

    public function getDeductible(Request $request)
    {
        $deal = DealDeductible::where('deal_id', $request->deal_id)->with('deductible_type')->get();
        $result = array('status' => '1', 'msg' => "Deal Deductible fetched successfully.", 'data' => $deal);
        return response()->json($result);
    }

    public function getDeductibleType(Request $request)
    {
        $data = DealDeductibleType::where('status', '1')->get();
        $result = array('status' => '1', 'msg' => "Deal Deductible type fetched successfully.", 'data' => $data);
        return response()->json($result);
    }

    public function deleteDeductible(Request $request, $id): JsonResponse
    {
        try {
            $deductible = DealDeductible::where('id', $id)->first();
//            $deal = Deal::where('id', $deductible->deal_id)->first();
//            $deal->net_commission = $deal->net_commission + $deductible->amount;
//            $deal->save();
            if ($deductible->delete()) {
                $result = array('status' => '1', 'msg' => "Deal Deductible deleted successfully.");
                return response()->json($result);
            } else {
                $result = array('status' => '0', 'msg' => 'Unable to delete Deal Deductible!!');
                return response()->json($result);
            }

        } catch (\Exception $exception) {
            return response()->json(['status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage()]);
        }
    }

    public function StoreDealDeductible(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'id' => 'nullable|exists:deal_deductibles,id',
            'deal_id' => 'required',
            'title' => 'nullable',
            'amount' => 'nullable',
            'deductible_type_id' => 'nullable',
            'fee_value' => 'nullable',
            'fee_type' => 'nullable',
        ]);

        if ($validator->fails()) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $validator->errors(), 422);
            return response()->json($result);
        }

        if (is_numeric(trim($request->deductible_type_id))) {

        } else {
            $new_deal_deductible_type = new DealDeductibleType();
            $new_deal_deductible_type->name = $request->deductible_type_id;
            $new_deal_deductible_type->type = $request->fee_type;
            $new_deal_deductible_type->type_value = $request->fee_value;
            $new_deal_deductible_type->status = 1;
            $new_deal_deductible_type->created_by_id = auth()->user()->id;
            $new_deal_deductible_type->save();
            $request->deductible_type_id = $new_deal_deductible_type->id;
        }

        //  try {
        if ($request->id == null || $request->id == '') {
            $dealDeductible = DealDeductible::where('deal_id', $request->deal_id)->where('deal_deductible_type_id', $request->deductible_type_id)->first();
            if ($dealDeductible) {
                $result = array('status' => '0', 'msg' => 'Deal Deductible already exists!!');
                return response()->json($result);
            }
        }

        $obj = DealDeductible::find($request->id) ?? new DealDeductible();
        $obj->deal_id = $request->deal_id;
        $obj->deal_deductible_type_id = $request->deductible_type_id;
        //$obj->title = $request->title;
        $obj->amount_type = "Fixed";
        $obj->amount = (float)str_replace(',', '', $request->amount);
        $obj->created_by_id = auth()->user()->id;
        if ($obj->save()) {
//                $deal = Deal::where('id', $request->deal_id)->first();
//                $deal->net_commission = $deal->net_commission -  $obj->amount;
//                $deal->save();

            $req = new Request();
            // Added deal id in request and pass to getDealCommissionCalculationsRevamped function
            $req->merge(['deal_id' => $request->deal_id]);
            $req->merge(['refresh_calculations' => true]);
            $this->getDealCommissionCalculationsRevamped($req);

            $result = array('status' => '1', 'msg' => "Deal Deductible added successfully.", 'data' => $obj);
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return response()->json($result);
//        } catch (\Exception $exception) {
//            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
//            return response()->json($result);
//        }

    }

    public function getProperty(Request $request)
    {
        $property = Property::where('id', $request->id)->with('otherDocuments')->first();
        return response()->json(['status' => '1', 'msg' => 'Property Found', 'data' => $property]);
    }

    public function pickOneSaleProgressionAgent($users, $transaction_type)
    {
        // Find the count of each user on DealProgressionData model with transaction_type = $transaction_type
        foreach ($users as $key => $user) {
            $users[$key]->sales_count = DealProgressionData::where('conveyancer_id', $user->id)
                ->where('transaction_type', $transaction_type)
                ->count();
        }

        // Sort the $users collection by sales_count
        $users = $users->sortBy('sales_count');

        // Check which user has the lowest sales_count and make that user selected = true
        $lowestSalesUser = $users->first(); // Assuming the first user has the lowest sales_count

        if ($lowestSalesUser->sales_count == 0) {
            $users->where('id', $lowestSalesUser->id)->first()->selected = true;
        } else {
            $users_with_lowest_sales_count = $users->where('sales_count', $lowestSalesUser->sales_count);
            $oldestSaleUser = $users_with_lowest_sales_count->sortBy(function ($users_with_lowest_sales_count) use ($transaction_type) {
                return DealProgressionData::where('conveyancer_id', $users_with_lowest_sales_count->id)
                    ->where('transaction_type', $transaction_type)
                    ->min('created_at');
            })->first();
            $users->where('id', $oldestSaleUser->id)->first()->selected = true;
        }

        // Alternatively, we can also consider sorting users by the earliest created_at in DealProgressionData
//        $oldestSaleUser = $users->sortBy(function($user) use($transaction_type){
//            return DealProgressionData::where('conveyancer_id', $user->id)
//                ->where('transaction_type', $transaction_type)
//                ->min('created_at');
//        })->first();
        return $users->toArray();
    }

    public function getSalesProgressions(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'transaction_type' => 'required|in:Sale,Lease',
        ]);
        $users = User::whereHas('roles', function ($query) {
            $query->where('name', 'Sales Progression');
        })->select('id', 'first_name', 'last_name')->get() ?? [];
        $users = collect($users);
        $users = $users->map(function ($user) {
            $user->selected = false;
            return $user;
        });
        if (count($users) > 0) {
            $users = $this->pickOneSaleProgressionAgent($users, $request->transaction_type);
            $result = array('status' => '1', 'msg' => "Sales Progression fetched successfully.", 'data' => $users);
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return response()->json($result);
    }

    public function calculateAML(Request $request)
    {
        $data = [
            [
                "sale_channels" => "Face 2 Face", // or Online
                "risk" => "Low", // or High for Online
                "score" => 1, // or 3 for Online
            ],
            [
                "uae_resident" => 1, // or 0
                "risk" => "Low", // or Medium for 0
                "score" => 1, // or 2 for 0
            ],
            [
                "sanction_screening_cleared" => 1, // or 0
                "risk" => "Low", // or Low for 0
                "score" => 0, // or 6 for 0
            ],
            [
                "politically_exposed_person" => 1, // or 0
                "risk" => "High", // or Low for 0
                "score" => 6, // or 0 for 0
            ],
            [
                "occupation" => "Employed", // or "Self Employed"
                "risk" => "Low", // or Medium for Self Employed
                "score" => 1, // or 2 for Self Employed
            ],
            [
                "entity_listed" => 1, // or 2 or 3
                "risk" => "Low", // or Medium for 2 and High for 3
                "score" => 1, // or 2 for 2 and 3 for 3
            ],
            [
                "nature_of_business" => 1, // or 2
                "risk" => "Low", // or High for 2
                "score" => 1, // or 3 for 2
            ],
            [
                "ownership_structure" => 1, // or 2 or 3
                "risk" => "Low", // or Medium for 2 and High for 3
                "score" => 1, // or 2 for 2 and 3 for 3
            ],
            [
                "ubo_owners" => 0, // or 1
                "risk" => "Low", // or High for 2
                "score" => 1, // or 6 for 1
            ],
            [
                "resident_or_national_of_black_list" => 0, // or 1
                "risk" => "Low", // or High for 1
                "score" => 100, // or 6 for 1
            ],
        ];
    }


}
