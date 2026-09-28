<?php

namespace App\Http\Controllers\Admin\Approval;

use App\Events\ActionOnListingChanges;
use App\Events\PropertyApproval;
use App\Http\Resources\Property\PropertyResource;
use App\Models\Bath;
use App\Models\Bed;
use App\Models\Community;
use App\Models\CommunityProperty;
use App\Models\Contact;
use App\Models\PropertyCategory;
use App\Models\PropertyNote;
use App\Models\PropertyPrice;
use App\Models\SubCommunity;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Cjmellor\Approval\Enums\ApprovalStatus;
use App\Models\Approval;
use App\Models\Property;
use App\Models\PropertyMedia;
use App\Traits\TelegramTrait;
use App\Http\Controllers\AjaxController;
use Illuminate\Http\JsonResponse;
use App\Traits\FileTrait;
class PropertyApprovalController extends Controller
{
    use TelegramTrait, FileTrait;


    public function index()
    {
        return view('admin.listing.approval');
    }
    public function dataTable(Request $request)
    {
        $columns = array(
            0 => 'updated_at',
            1 => 'id',

        );
        $searchKeyWord = htmlspecialchars($request['search']['value']);
        $totalData = Approval::where('approvalable_type',Property::class)->count();
        $totalFiltered = $totalData;
            $data = Approval::where('approvalable_type',Property::class)->orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
                ->offset($request['start'])
                ->limit($request['length'])
                ->with('approvalable')
                ->get();
        $json_data = array(
            "draw" => intval($request['draw']),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );
        echo json_encode($json_data);
    }
    public function show(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:approvals,id',
        ]);
       // try {
            $pending = Approval::find($request->id);
            $data = (object)[];

            $data->old_data = propertyColumnsReadable($pending->original_data);
            $data->modified_data = propertyColumnsReadable($pending->new_data);
            $result = array('status' => '1', 'msg' => 'Changes details fetched successfully', 'data' => $data,'db'=>$pending);
//        } catch (\Exception $exception) {
//            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
      //  }
        return json_encode($result);
    }
    public function getByProperty(Request $request)
    {

        //try {
            $pendings = Approval::where('approvalable_id',$request->id)->where('approvalable_type',Property::class)->where('state','pending')->latest()->get();

           $propertyMediaCounts = PropertyMedia::where('pm1.property_id', $request->id)
    ->whereNotNull('pm1.media_changed')
    ->selectRaw('pm1.title, pm1.media_changed, count(*) as count')
    ->join('property_media as pm1', 'pm1.id', '=', 'property_media.id')
    ->groupBy('pm1.title', 'pm1.media_changed')
    ->withTrashed()
    ->get();

            if(!empty($pendings)) {
                $arr = [];
                $i = 0;
                foreach ($pendings as $pending){

                    $data = (object)[];
                    $arr[$i]['id'] = $pending->id;
                    //$data->id = $pending->id;
                    $arr[$i]['old_data'] = propertyColumnsReadable($pending->original_data);
                    $arr[$i]['modified_data'] = propertyColumnsReadable($pending->new_data);
                    $arr[$i]['new_data'] = $pending->new_data;

                    $i++;
                }
                $pricing = PropertyPrice::where('property_id', $request->id)->first();
                $property = Property::where('id', $request->id)->first();
                $approval_request_type = $property->request_status_type;
                $approval_is_live = $property->is_live;
                $metaData = [];
                $pfMeta = [];
                $bayutMeta = [];
                $dubizzleMeta = [];
                if($approval_is_live){
                    $params = new Request();
                    $params->replace(['property_id' => $property->id, "portal_slug" => "property-finder","dont_update" => true]);
                    $res = (new AjaxController())->getMetaHtml($params);

                    $data = json_decode($res->getContent(), true);

                    if($data['status']){
                        $pfMeta = $data['metaHtml'];
                    }

                    $params = new Request();
                    $params->replace(['property_id' => $property->id, "portal_slug" => "bayut", "dont_update" => true]);
                    $res = (new AjaxController())->getMetaHtml($params);
                    $data = json_decode($res->getContent(), true);
                    if($data['status']){
                        $bayutMeta = $data['metaHtml'];
                    }

                    $params = new Request();
                    $params->replace(['property_id' => $property->id, "portal_slug" => "dubizzle", "dont_update" => true]);
                    $res = (new AjaxController())->getMetaHtml($params);
                    $data = json_decode($res->getContent(), true);
                    if($data['status']){
                        $dubizzleMeta = $data['metaHtml'];
                    }


                    $metaData = [
                        "property_finder" => $pfMeta,
                        "bayut" => $bayutMeta,
                        "dubizzle" => $dubizzleMeta,
                    ];
                }

                $pricing_change = Approval::where('state', 'pending')->where('new_data->property_id', $request->id)->get();
                $pricing_change2 = Approval::where('approvalable_type', PropertyPrice::class)->where('approvalable_id', $pricing?->id)->where('state', 'pending')->get() ?? [];
                $pricings = $pricing_change->count() >0? $pricing_change : $pricing_change2; //Approval::where('state', 'pending')->where('new_data->property_id', $request->id)->get() ?? Approval::where('approvalable_id', $pricing->id)->where('state', 'pending')->get();
                if(!empty($pricings)) {
                    $x = 0;
                    $arr2 = [];
                    foreach ($pricings as $price) {
                        $arr2[$x]['id'] = $price->id;
                        $arr2[$x]['old_data'] = propertyColumnsReadable($price['original_data']);
                        $arr2[$x]['modified_data'] = propertyColumnsReadable($price['new_data']);
                        $arr2[$x]['new_data'] = $price['new_data'];
                        $x++;
                    }
                    $arr = array_merge($arr, $arr2);
                }
                if(!empty($arr)) {
                    $result = array('status' => '1', 'msg' => 'Changes details fetched successfully', 'data' => $arr,"request_type" => $approval_request_type, "is_live" => $approval_is_live, "metaData" => $metaData, 'media_changed' => $propertyMediaCounts);
                }else{
                    $result = array('status' => '0', 'msg' => 'No pending data found!!', "request_type" => $approval_request_type, "is_live" => $approval_is_live, "metaData" => $metaData, 'media_changed' => $propertyMediaCounts);
                }

            }else{
                $result = array('status' => '0', 'msg' => 'No pending data found!!', 'media_changed' => $propertyMediaCounts);
            }
//        } catch (\Exception $exception) {
//            $result = array('status' => '0', 'msg' => 'Something went wrong!!','error'=>$exception->getMessage());
//        }
        return json_encode($result);
    }

    public function action(Request $request){
        try {
            $property = Property::find($request->id);
            if($request->approval_ids != null) {
                $ids = explode(',', $request->approval_ids);

                foreach ($ids as $id) {
                    $main = Approval::where('id', $id)->first();
                    if ($request->action == 'approved') {

                        if($main->approvalable_id == null){
                            Approval::find($id)->approve(persist: true);

                            //\DB::table('approvals')->where('id',$id)->update(['state' => 'approved']);
                        }else{
                            $model = $pendingModal = $main->approvalable_type::find($main->approvalable_id);

                            if($main->approvalable_type == PropertyPrice::class){
                                $model = $property;
                            }
                            $arr['attributes'] = $main->new_data;
                            $arr['old'] = $main->original_data;
                            activity()
                                ->performedOn($model)
                                ->causedBy(auth()->user())
                                ->withProperties($arr)
                                ->event('updated')
                                ->log(auth()->user()->first_name . ' has Approved ✅ listing changes on ' . $model->ref_no);

                            $dataArray = $main->new_data->toArray();
                            if(isset($dataArray['publish_status']) && $dataArray['publish_status'] == "POCKET" ?? false){
                                $dataArray['request_status_type'] = "POCKET";
                                $dataArray['is_live'] = false;
                                if($pendingModal instanceof Property){
                                    initUnpublishAtPF($pendingModal->id);
                                }

                            }
                            $pendingModal->withoutApproval()->update($dataArray);

                        }

                        $main->update(['state' => ApprovalStatus::Approved]);
                    } else {
                        activity()
                                ->performedOn($property)
                                ->causedBy(auth()->user())
                                ->event('updated')
                                ->log(auth()->user()->first_name . ' has Refused 🚫 listing changes on ' . $property->ref_no);
                        $main->update(['state' => ApprovalStatus::Rejected]);
                    }

                }

            }

            if ($request->action == 'approved') {
                PropertyMedia::where('property_id', $request->id)->update(['media_changed' => null]);
                PropertyMedia::onlyTrashed()
                    ->where('property_id', $request->id)
                    ->update(['media_changed' => null]);

                $propertyObj2 = Property::find($request->id);
                if($propertyObj2->request_status_type == "PUBLISHED"){
                    $propertyObj2->withoutApproval()->update(['is_live' => true, 'publish_status' => "PUBLISHED"]);
                    initPublishAtPf($propertyObj2->id);
                }
                elseif($propertyObj2->request_status_type == "POCKET"){
                    $propertyObj2->withoutApproval()->update(['is_live' => false, 'publish_status' => "POCKET"]);
                    initUnpublishAtPF($propertyObj2->id);
                }
                else{
                    $propertyObj2->withoutApproval()->update(['is_live' => false, "request_status_type" => null]);
                    initUnpublishAtPF($propertyObj2->id);
                }
                $message = "<b>AX Secondary CRM</b>\nYour changes on property reference number <b>" . $property->ref_no . "</b> have been Approved ✅.\n";
                $message.="\nRegards,\n ".auth()->user()->first_name;
                $message.="\n—————————————————";


                $note = new PropertyNote();
                $note->property_id  = $property->id;
                $note->note = $message;
                $note->created_by_id = auth()->user()->id;
                $note->save();


                $chat_ids = [$property?->agent?->telegram_chatid];

                $this->sendTelegramMessageWithAttachment($message, $chat_ids, null, 'notification_bot');
                $result = array('status' => '1', 'msg' => 'Changes on property have been Approved ✅.');
                event(new ActionOnListingChanges($property, 'approved'));
                event(new PropertyApproval($property, 'approved'));

            }else{
                $property->withoutApproval()->update(['publish_status' => 'REFUSED']);
                $url = null;
                if ($request->file("refuseAttachment")) {
                    $path = $this->uploadDocument($request->file("refuseAttachment"), "Notes");
                    $url = $path;
                }

                $result = array('status' => '1', 'msg' => 'Changes on property have been Refused.');
                $message = "<b>AX Secondary CRM</b>\nOops! Your changes on property reference number  <b>" . $property->ref_no . "</b> have been <b>Refused</b> 🚫. Kindly review the note provided below for reference.\n";
                $message .= "\n<b>Reason: </b> ";
                $message .= "\n" . $request->refuseReason;
                $message.="\n\nRegards,\n".auth()->user()->first_name;
                $message.="\n—————————————————";

                $noteMessage = str_replace("\n", "<br>", $message);

                PropertyNote::create([
                    "property_id" => $property->id,
                    "note" => $noteMessage,
                    "url" => $url,
                    "created_by_id" => auth()->id(),
                ]);
                $chat_ids = [$property?->agent?->telegram_chatid];

                $this->sendTelegramMessageWithAttachment($message, $chat_ids, $url, 'notification_bot');

                event(new ActionOnListingChanges($property, 'refused'));
                event(new PropertyApproval($property, 'refused'));
            }

        } catch (\Exception $exception) {
            dd($exception);
            $result = array('status' => '0', 'msg' => 'Something went wrong!!','error'=> $exception->getMessage(), 'file' => $exception->getFile(), 'line' => $exception->getLine());
        }
        return response()->json($result);
    }
}
