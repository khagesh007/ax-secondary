<?php

namespace App\Http\Controllers;

use App\Events\ContractExpiring;
use App\Events\DealApproved;
use App\Events\DealCompleted;
use App\Events\DealCreated;
use App\Events\DealInviteAgent;
use App\Events\DealRejected;
use App\Events\DealResubmitted;
use App\Events\DealSubmittedForApproval;
use App\Events\EventCreated;
use App\Events\EventReminder;
use App\Events\ExclusiveExpiring;
use App\Events\LeadCreated;
use App\Events\ListingNoteCreated;
use App\Events\PublishRequested;
use App\Events\SalesProgressionAssigned;
use App\Events\SalesProgressionChanged;
use App\Events\ShootingRequestCreated;
use App\Events\ShootJobAction;
use App\Events\ShootRequestCreated;
use App\Exports\DealCommissionExportExcel;
use App\Http\Controllers\Api\ExternalApiController;
use App\Http\Controllers\Api\V1\Deals\DealApiController;
use App\Http\Controllers\Extra\PropertyReraController;
use App\Jobs\PFV2CreateListingJob;
use App\Jobs\PFV2DeleteListingJob;
use App\Jobs\PFV2UnpublishListingJob;
use App\Jobs\SendDealMsgToExternalAgent;
use App\Jobs\SendEventReminder;
use App\Mail\ContractExpiringMail;
use App\Mail\DealCommissionMail;
use App\Mail\DealSuccessLeadMail;
use App\Models\CommunityProperty;
use App\Models\Contact;
use App\Models\ContactMedia;
use App\Models\ContactNote;
use App\Models\Deal;
use App\Models\DealApproval;
use App\Models\Medium;
use App\Models\Otp;
use App\Models\PfCreditHistory;
use App\Models\PortalMeta;
use App\Models\DealInvite;
use App\Models\DealLead;
use App\Models\DealBitrixDump;
use App\Models\DealProgressionData;
use App\Models\DealSplit;
use App\Models\Event;
use App\Models\GeneralSetting;
use App\Models\Job;
use App\Models\Lead;
use App\Models\LeadMedia;
use App\Models\LeadNote;
use App\Models\ListingLead;
use App\Models\ListingLeadNote;
use App\Models\ModelUpdate;
use App\Models\NotificationEvent;
use App\Models\NotificationEventSubscriber;
use App\Models\Portal;
use App\Models\Property;
use App\Models\PropertyNote;
use App\Models\PropertyShootBooking;
use App\Models\User;
use App\Models\Source;
use App\Models\UserMeta;
use App\Models\KpiTarget;
use App\Models\UserOAuthToken;
use App\Traits\CallTrait;
use App\Traits\TelegramTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Approval;
use App\Models\Community;
use App\Models\Folder;
use App\Models\FolderMedia;
use App\Models\PropertyMedia;
use App\Models\Region;
use App\Models\SubCommunity;
use App\Traits\FileTrait;
use App\Traits\PDFTrait;
use Carbon\Carbon;
use Cjmellor\Approval\Enums\ApprovalStatus;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Pusher\Pusher;
use App\Traits\PusherTrait;
use App\Traits\DocusignTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Brick\PhoneNumber\PhoneNumber;
use Brick\PhoneNumber\PhoneNumberParseException;
use Exception;
use Illuminate\Support\Facades\Storage;
use Propaganistas\LaravelPhone\PhoneNumber as LaravelPhoneNumber;
use App\Jobs\GetBitrixDealLead;
use Spatie\Tags\Tag;
use Telegram\Bot\Laravel\Facades\Telegram;
use ZipArchive;
use Spatie\Activitylog\Models\Activity;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfReader;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use App\Jobs\DealInviteReminder;
use App\Jobs\KpiNotificationToAgents;
use App\Traits\PropertyFinderTrait;
use App\Services\Portal\PropertyFinderService;
use App\Services\Portal\PFHelperService;
class TestController extends Controller
{
    use CallTrait, PusherTrait, TelegramTrait, PDFTrait, FileTrait,DocusignTrait,PropertyFinderTrait;

    protected $counter = 0;

    public function __construct(public PropertyFinderService $propertyFinderService, public PFHelperService $pfHelperService)
    {
    }
    public function vueTest()
    {
        return view('test');
    }
    public function mapPFlocation(Request $request)
    {
        $module = $request->get('module');
        $id = $request->get('id');
        $location_id = $request->get('location_id');

        if($module == 'community'){
            $community = Community::find($id);
            if($community){
                    echo $community->name;
                if($community){
                    $portal_ids = json_decode($community->portal_ids, true) ?? [];
                    $portal_ids['property_finder_id'] = $location_id;
                    $community->portal_ids = json_encode($portal_ids);
                    $community->save();
                    return response()->json(['status' => 'success', 'message' => 'Community Location updated successfully']);
                }else{
                    return response()->json(['status' => 'error', 'message' => 'Location not found']);
                }
            }else{
                return response()->json(['status' => 'error', 'message' => 'Community not found']);
            }
        }else if($module == 'sub_community'){
            $sub_community = SubCommunity::find($id);
            if($sub_community){

                if($sub_community){
                    $portal_ids = json_decode($sub_community->portal_ids, true) ?? [];
                    $portal_ids['property_finder_id'] = $location_id;
                    $sub_community->update(['portal_ids' => $portal_ids]);
                    return response()->json(['status' => 'success', 'message' => 'Sub Community Location updated successfully']);
                }else{
                    return response()->json(['status' => 'error', 'message' => 'Location not found']);
                }
            }else{
                return response()->json(['status' => 'error', 'message' => 'Sub Community not found']);
            }
        }else if($module == 'community_property'){
            $community_property = CommunityProperty::find($id);
            if($community_property){
                // $location = $community_property->portals()->where('portal_id', 1)->first();
                if($community_property){
                    $portal_ids = json_decode($community_property->portal_ids, true) ?? [];
                    $portal_ids['property_finder_id'] = $location_id;
                    $community_property->update(['portal_ids' => $portal_ids]);
                    return response()->json(['status' => 'success', 'message' => 'Location updated successfully']);
                }else{
                    return response()->json(['status' => 'error', 'message' => 'Location not found']);
                }
            }else{
                return response()->json(['status' => 'error', 'message' => 'Community Property not found']);
            }
        }else{
            return response()->json(['status' => 'error', 'message' => 'Invalid module']);
        }

    }

    public function createPf(Request $request){
      $ref_no  = $request->get('ref_no');
      $property = Property::where('ref_no', $ref_no)->first();
      echo $property->id. ' - ref no: ' . $property->ref_no . "<br>";
      //run when environment production
        echo app()->environment();
        if(app()->environment() == 'production') {
            echo 'Dispatching PFV2CreateListingJob for property ID: ' . $property->id . "<br>";
            PFV2CreateListingJob::dispatch($property->id);
        }
       // return response()->json(['status' => 'success', 'message' => 'Property created successfully in PF']);

    }
      public function deletePf(Request $request){
      $ref_no  = $request->get('ref_no');
      $property = Property::where('ref_no', $ref_no)->first();
      echo $property->id. ' - ref no: ' . $property->ref_no . "<br>";
      //run when environment production
        echo app()->environment();
        if(app()->environment() == 'production') {
            echo 'Dispatching PFV2DeleteListingJob for property ID: ' . $property->id . "<br>";
            PFV2DeleteListingJob::dispatch($property->id);
        }
       // return response()->json(['status' => 'success', 'message' => 'Property created successfully in PF']);

    }
    public function unPublishAtPf(Request $request){
        $ref_no  = $request->get('ref_no');
        $property = Property::where('ref_no', $ref_no)->first();
        echo $property->id. ' - ref no: ' . $property->ref_no . "<br>";
        //run when environment production
        echo app()->environment();
        if(app()->environment() == 'production') {
            echo 'Dispatching PFV2UnpublishListingJob for property ID: ' . $property->id . "<br>";
            PFV2UnpublishListingJob::dispatch($property->id);
        }
        // return response()->json(['status' => 'success', 'message' => 'Property created successfully in PF']);

    }
    public function codeTest(Request $request){
    $url = "https://dubizzle.com/profolio/api-v7/stats/website-client-leads?type=call_logs&timestamp=2025-08-18 00:00:00";
        $request = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('BAYUT_AUTHORIZATION'),
        ])->get($url);
        $response = $request->json();
        if($request->status() === 403) {
            Log::error('Dubizzle Lead Import Controller: Access denied for ' . $url);
            print_r($response);
        }
        //print curl request for debugging
        if($request->successful()) {
            print_r($response);
        } else {
            Log::error('Dubizzle Lead Import Controller: Error fetching data from ' . $url);
            print_r($response);
        }



    }
    public function pfWhatsappLead(Request $request){

        try {
            $tokens = $this->getPfAccessToken('secondary');
            $access_token = $tokens['token'];

            $today = date('Y-m-d');

            $time_start = microtime(true);
            $page = 1;
            $importedCount = 0;
            $updatedCount = 0;
            $per_page = 10;




            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api-v2.mycrm.com/whatsapp-leads/?per_page=1&filters[date][from]='.$today.'&filters[date][to]='.$today,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . $access_token,
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);

            $data = json_decode($response, true);


            if (isset($data['error']) && $data['error'] == 'AuthenticationError') {
                echo 'PF Authentication when fetching whatsapp leads error code N3434';
                info('PF Authentication when fetching whatsapp leads');
                return true;
            }

            if (!is_null($data)) {

                 $total_leads_count = $data['count'];
                 $total_pages = round(ceil($total_leads_count / $per_page), 0);
                for ($page; $page <= 20; $page++) { //limit is hardcoded becuase date filter is not working as expected
                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                        CURLOPT_URL => 'https://api-v2.mycrm.com/whatsapp-leads/?per_page=' . $per_page . '&page=' . $page . '&filters[date][from]='.$today.'&filters[date][to]='.$today,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'GET',
                        CURLOPT_HTTPHEADER => array(
                            'Authorization: Bearer ' . $access_token,
                            'X-MyCRM-Expand-Data: true',
                        ),
                    ));

                    $response = curl_exec($curl);

                    curl_close($curl);

                    $data = json_decode($response, true);
                    if (isset($data['error']) && $data['error'] == 'AuthenticationError') {
                        echo 'PF Authentication when fetching whatsapp leads error code N34232';
                        info('PF Authentication when fetching whatsapp leads');
                    }
                    if (!is_null($data)) {
                        $medium = Medium::where('name', "LIKE", "%" . 'WhatsApp' . "%")->first();

                        $leads = $data['whatsapp'];
                        foreach ($leads as $lead) {
                            $mobile = '';
                            $mobile = str_replace(' ', '', $lead['phone']);
                            if (!Lead::where('my_crm_id', $lead['id'])->exists() && !Lead::where('mobile',$mobile)->where('source_id', '31')->where('lead_medium_id',$medium?->id)->exists()) {
                                $given_time = $lead['created_at'];

                                $date = new \DateTime($given_time);
                                // $date->setTimezone(new \DateTimeZone('Asia/Dubai'));

                                $formatted_time = $date->format('Y-m-d H:i:s');
                                if ($lead['property_reference'] == null) {
                                    $user = null;
                                    if ($lead['user'] != null) {
                                        $private = $lead['user']['private'] ?? null;
                                        if ($private != null) {
                                            $email = $private['email'] ?? null;
                                            $user = User::where('email', $email)->first() ?? null;
                                        }

                                    }

                                    $new_lead = new Lead();
                                    $new_lead->mobile = $mobile;
                                    $new_lead->whatsapp = $mobile;
                                    $new_lead->my_crm_id = $lead['id'];
                                    $new_lead->agent_id = $user ? $user->id : null;
                                    $new_lead->lead_medium_id = $medium?->id ?? null;
                                    $new_lead->source_id = 31;
                                    $new_lead->status = 1;
                                    $new_lead->lead_status_id = 1;
                                    $new_lead->created_at = Carbon::now();
                                    $new_lead->updated_at = Carbon::now();
                                    $new_lead->portal_created_at = $formatted_time;
                                    if ($new_lead->save()) {
                                        event(new \App\Events\LeadCreated($new_lead));
                                        activity()
                                            ->performedOn($new_lead)
                                            ->event('Created')
                                            ->log('Created New Lead From pf whatsapp Api');
                                        $leadNote = new LeadNote();
                                        $leadNote->lead_id = $new_lead->id;
                                        $responseText = json_encode($lead);

                                        $leadNote->note = 'Imported from Expert Property Finder whatsapp Api
                                <br><code><pre>' . $responseText . "</pre></code>";
                                        $leadNote->save();
                                    }
                                    $importedCount++;
                                }
                            }

                        }
                    }


                    $time_end = microtime(true);
                    // $execution_time = ($time_end - $time_start);


                }
            }
            info('Imported pf whatsapp leads: ' . $importedCount);
            echo 'Imported pf whatsapp leads: ' . $importedCount;
        }catch (\Exception $th) {
            Log::channel('discord')->error("Error in propertyFinderWhatsappLeadsNew function in ExternalApiController.php " . json_encode($th->getMessage()));
            echo 'Error in propertyFinderWhatsappLeadsNew function in ExternalApiController.php ' . json_encode($th->getMessage());

        }

    }
    public function testEmail()
    {
        $property = Property::find(1);
        $user = User::find(1);

        $event = Event::find(8);

        $job = Job::find(1);
        $requestedBy = $job->requestedBy;
        $assignee = $job->assignedTo;

        $lead = Lead::latest()->first();

        return view(
            "emails.leadFollowUpReminder",
            compact("property", "user", "event", "job", "requestedBy", "assignee", "lead")
        );
    }

    public function testPdf()
    {
        $deal = Deal::find(1);

        $deal->load(["agent", "createdBy", "lead", "contact"]);
        //        echo "<pre>";
        //        print_r($deal->toArray());die();

        //        $pdf = Pdf::loadView("pdf.dealInvoice", compact("deal"));

        return view("pdf.dealInvoice", compact("deal"));
    }
public function matchBitrixLead($deal, $lead){
if($lead ==null){
    echo '
    <tr>
    <td>'.$deal->bitrix_deal_id.'</td>
    <td>-</td>
    <td>-</td>
    <td>-</td>
    </tr>';
    return '';
}
$deal_data = json_decode($deal->deal_data, true);

    $lead_source = findSourceForBitrix($lead['SOURCE_ID']);

    $mobile = ''; $email = ''; $lead_source_matched = 0;
    if($lead['HAS_PHONE'] == 'Y'){
       $mobile = $lead['PHONE'][0]['VALUE'];
    }
    if($lead['HAS_EMAIL'] == 'Y'){
        $email = $lead['EMAIL'][0]['VALUE'];
    }
    // $ax_lead = Lead::where('mobile',$mobile)
    // ->when($deal->agent_id, function ($query) use ($deal) {
    //     return $query->where('agent_id', $deal->agent_id);
    // })->where(function($q) use ($email,$mobile){
    //     $q->where('mobile',$mobile)->orWhere('email',$email);
    // })->first();
    //remove +971 from mobile
    $mobile1 = str_replace("+971", "0", $mobile);
    $mobile2 = str_replace("+971", "971", $mobile);
    $mobile3 = str_replace("+971", "00971", $mobile);
    $mobile4 = str_replace("+971", "", $mobile);
    $mobile5 = "971".$mobile4;
    $mobile6 = "+971".$mobile4;
    $mobile7 = "0".$mobile5;
    $ax_lead = Lead::query()
                ->when($deal->agent_id, function ($query) use ($deal) {
                    return $query->where('agent_id', $deal->agent_id);
                })
                ->when($mobile, function ($query) use ($mobile,$mobile1,$mobile2,$mobile3,$mobile4,$mobile5,$mobile6,$mobile7) {
                    // Check for exact match or partial match using LIKE

                        $query->where('mobile', $mobile)
                        ->orWhere('mobile', 'like', "%{$mobile}%")
                        ->orWhere('mobile', $mobile1)
                        ->orWhere('mobile', $mobile2)
                        ->orWhere('mobile', $mobile3)
                        ->orWhere('mobile', $mobile4)
                        ->orWhere('mobile', $mobile5)
                        ->orWhere('mobile', $mobile6)
                        ->orWhere('mobile', $mobile7);

                })
                ->when($email, function ($query) use ($email) {
                    // Check for exact match or partial match using LIKE
                    return $query->where(function ($q) use ($email) {
                        $q->where('email', $email)
                        ->orWhere('email', 'like', "%{$email}%");
                    });
                })->with('leadType')
                ->first();

                $lead_matched = 'No'; $with_same_agent = 'No'; $source_in_crm = '-';
    if($ax_lead != null){
     //   echo "Lead found in axcrm with same agent";
        $lead_matched = 'Yes'; $with_same_agent = 'Yes';
    $source = Source::where('id',$ax_lead->source_id)->first();
    $source_in_crm = $source->name;
        if($source->name == $lead_source){
            $lead_source_matched = 1;
        }elseif($source->name == $lead['SOURCE_ID']){
            $lead_source_matched = 1;
        }else{
            $lead_source_matched = 0;
        }
    }else{


        $ax_lead = Lead::query()
        ->when($mobile, function ($query) use ($mobile,$mobile1,$mobile2,$mobile3,$mobile4,$mobile5,$mobile6,$mobile7) {
            // Check for exact match or partial match using LIKE

                $query->where('mobile', $mobile)
                ->orWhere('mobile', 'like', "%{$mobile}%")
                ->orWhere('mobile', $mobile1)
                ->orWhere('mobile', $mobile2)
                ->orWhere('mobile', $mobile3)
                ->orWhere('mobile', $mobile4)
                ->orWhere('mobile', $mobile5)
                ->orWhere('mobile', $mobile6)
                ->orWhere('mobile', $mobile7);

        })
        ->when($email, function ($query) use ($email) {
            // Check for exact match or partial match using LIKE
            return $query->where(function ($q) use ($email) {
                $q->where('email', $email)
                ->orWhere('email', 'like', "%{$email}%");
            });
        })->with('leadType')
        ->first();

        if($ax_lead != null){
            $source = Source::where('id',$ax_lead->source_id)->first();
            $source_in_crm = $source->name;
            $lead_matched = 'Yes';
           // echo "Lead found in axcrm with different agent";
        }else{
            //echo "Lead not found";
        }
    }
    //  $deal->deal_leads = $lead;
    //  $deal->lead_source_matched = $lead_source_matched;
    //  $deal->save();
     echo '
     <tr>
     <td>'.$deal->bitrix_deal_id.'</td>
 <td>'.$lead_matched.'</td>
 <td>'.$with_same_agent.'</td>
     </tr>';
     /*
     <td>'.$lead_matched.'</td>

     <td>'.$source_in_crm.'</td>
      <td>'.$mobile.'</td>
     <td>'.$email.'</td>
     <td>'.$deal_data['OPPORTUNITY'].'</td>
     <td>'.$ax_lead?->leadType?->name.'</td>
     <td>'.$ax_lead?->id.'</td>
     <td>'.$ax_lead?->mobile.'</td>
     */
}

public function docusignCallback(Request $request)
{
    $res = $request->all();
    $code = $res['code'];
    $response = Http::withBasicAuth(env('DOCUSIGN_CLIENT_ID'), env('DOCUSIGN_CLIENT_SECRET'))

    ->post('https://account-d.docusign.com/oauth/token', [
        'grant_type' => 'authorization_code',
        'code' => $code,

    ]);



$result = $response->json();
print_r($result);

    //$this->docusignService->callback($request->all());
}
    public function makeEditablePdf()
    {
        //MAKE EDITABLE PDF



       // return view('pdf_template');

       // Load HTML content from Blade view
       $html = view('pdf_template')->render();

       // Create an instance of mPDF
       $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4']);

       // Set mPDF to be aware of form fields
       $htmls = '
       <style>
       textarea {
       }
       input {
       }
       select {
           font-family: Arial
       }
       </style>
       <body>
       <h2>Active Forms</h2>

       <form action="formsubmit.php" method="post">
       <b>Input Text</b>
       <input type="text" size="90" name="inputfield" value="" title="The title attribute works like a tool-tip" />
       <br /><br />

       <b>Readonly</b>
       <input type="text" size="90" name="readonlyfield" value="Read-only field. The value will be exported on submit" title="The title does not do anything if the field is read-only" readonly="readonly" />
       <br /><br />

       <b>Disabled</b>
       <input type="text" size="90" name="disabledfield" value="Disabled field. The value will not be exported on submit" title="The title does not do anything if the field is disabled." disabled="disabled" />
       <br /><br />

       <b>Required</b>
       <input type="text" size="90" name="requiredfield" value="" title="This field must be entered before you can submit." required="required" />
       <br /><br />

       <b>Hidden</b>
       <input type="hidden" name="hiddenfield" value="value_of_hidden_field">
       <br /><br />

       <b>Input Password</b>
       <input type="password" size="40" name="password" value="mysecretpassword" title="This field is set to act as a password field." >
       <br /><br />

       <b>Calculations</b>
       <input type="text" size="20" name="maths1" value="0" onChange="var a = this.getField(\'maths1\').value;
       var b = this.getField(\'maths2\').value;
       this.getField(\'maths3\').value = a + b;
       " /> +
       <input type="text" size="20" name="maths2" value="0" onChange="var a = this.getField(\'maths1\').value;
       var b = this.getField(\'maths2\').value;
       this.getField(\'maths3\').value = a + b;
       " /> =
       <input type="text" size="20" name="maths3" value="" readonly="readonly"/>
       <br /><br />


       <b>Textarea</b>
       <textarea name="textareafield" spellcheck="true" rows="5" cols="80" wrap="virtual" title="Textarea tooltip">Quisque viverra. Etiam id libero at magna pellentesque aliquet. Nulla sit amet ipsum id enim tempus dictum. Quisque viverra. Etiam id libero at magna pellentesque aliquet. Nulla sit amet ipsum id enim tempus dictum. Quisque viverra. Etiam id libero at magna pellentesque aliquet. Nulla sit amet ipsum id enim tempus dictum. Quisque viverra. Etiam id libero at magna pellentesque aliquet. Nulla sit amet ipsum id enim tempus dictum. Quisque viverra. Etiam id libero at magna pellentesque aliquet. Nulla sit amet ipsum id enim tempus dictum. Quisque viverra. Etiam id libero at magna pellentesque aliquet. Nulla sit amet ipsum id enim tempus dictum. Quisque viverra. Etiam id libero at magna pellentesque aliquet. Nulla sit amet ipsum id enim tempus dictum. Quisque viverra. Etiam id libero at magna pellentesque aliquet. Nulla sit amet ipsum id enim tempus dictum. </textarea>
       <br /><br />


       <b>First language</b>
       <select name="first_language" editable="editable">
       <option value="" >- Please select or type in -</option>
       <option value="Arabic" >&#1575;&#1604;&#1593;&#1585;&#1576;&#1610;&#1577;</option>
       <option value="Bulgarian" >&#1073;&#1098;&#1083;&#1075;&#1072;&#1088;&#1089;&#1082;&#1080; &#1077;&#1079;&#1080;&#1082;</option>
       <option value="Chinese" >&#27721;&#35821;</option>
       <option value="Czech" >&#269;esky</option>
       <option value="English" >English</option>
       <option value="French" >Fran&#231;ais</option>
       <option value="Greek" >&#917;&#955;&#955;&#951;&#957;&#953;&#954;&#940;</option>
       <option value="Japanese" >&#26085;&#26412;&#35486;</option>
       <option value="Latvian" >Latvie&#353;u valoda</option>
       <option value="Russian" >&#1056;&#1091;&#1089;&#1089;&#1082;&#1086;</option>
       <option value="Spanish" >Espa&#241;ol</option>
       <option value="Turkish" >T&#252;rk&#231;e</option>
       </select>
       &nbsp;&nbsp;&nbsp;&nbsp;

       You have selected: <input type="text" size="20" name="first_language_display" value="" readonly="readonly" onCalculate="
       var choice = this.getField(\'first_language\').value;
       this.getField(\'first_language_display\').value = choice;
       "/>

       <br /><br />


       <div>
       <b>Spoken language(s)</b><br />
       <select size="6" multiple="multiple" name="spoken_languages">
       <option value="Arabic" >&#1575;&#1604;&#1593;&#1585;&#1576;&#1610;&#1577;</option>
       <option value="Bulgarian" >&#1073;&#1098;&#1083;&#1075;&#1072;&#1088;&#1089;&#1082;&#1080; &#1077;&#1079;&#1080;&#1082;</option>
       <option value="Chinese" >&#27721;&#35821;</option>
       <option value="Czech" >&#269;esky</option>
       <option value="English" selected="selected">English</option>
       <option value="French" >Fran&#231;ais</option>
       <option value="Greek" >&#917;&#955;&#955;&#951;&#957;&#953;&#954;&#940;</option>
       <option value="Japanese" >&#26085;&#26412;&#35486;</option>
       <option value="Latvian" >Latvie&#353;u valoda</option>
       <option value="Russian" >&#1056;&#1091;&#1089;&#1089;&#1082;&#1086;</option>
       <option value="Spanish" >Espa&#241;ol</option>
       <option value="Turkish" >T&#252;rk&#231;e</option>
       </select>
       </div>
       <br /><br />



       <b>How many languages do you speak?</b> &nbsp;&nbsp;&nbsp;&nbsp;
       <input type="radio" name="num_languages" value="1" checked="checked" > One &nbsp;&nbsp;&nbsp;&nbsp;
       <input type="radio" name="num_languages" value="2" > Two &nbsp;&nbsp;&nbsp;&nbsp;
       <input type="radio" name="num_languages" value="3" > Three &nbsp;&nbsp;&nbsp;&nbsp;
       <input type="radio" name="num_languages" value="4" > Four &nbsp;&nbsp;&nbsp;&nbsp;
       <input type="radio" name="num_languages" value="5" > Five +

       <br /><br />


       <input type="checkbox" name="email" value="email_subscrbe" checked="checked" /> Please send me regular information by e-mail<br>
       <input type="checkbox" name="phone" value="phone-me" disabled="disabled" /> Please phone me (not available at present)<br>
       <input type="checkbox" name="subscribe" value="newsletter_subscribe" /> Please subscribe me to the Newsletter
       <br /><br />

       <br /><br />

       Click this button to see Javascript in action:
       <input type="image" name="imageButton" value="imagesubmitted" src="assets/goto.gif" title="Click to run javascript" onClick="
       var npass = this.getField(\'password\').value;
       app.alert(\'Value of password field is: \\\'\' + npass + \'\\\'\');
       var nButton = app.alert({
       cMsg: \'Do you want to change the input text field to: T\u00f6rkylempij\u00e4?\',
       cTitle: \'A message from A. C. Robat\',
       nIcon: 2, nType: 2
       });
       if ( nButton == 4 ) {
       this.getField(\'inputfield\').value = \'T\u00f6rkylempij\u00e4\';
       }
       " />

       <br /><br />

       <input type="submit" name="disabled_submit" value="Disabled" disabled="disabled" title="This Submit button is disabled" />

       <input type="reset" name="resetfield" value="Reset" />

       <input type="submit" name="submit" value="Submit" title="Click here to submit" />

       <input type="submit" name="submit_in_Turkish" value="Submit (T&#252;rk&#231;e)" title="Click here to submit in T&#252;rk&#231;e" />

       <input type="button" name="javascriptButton" value="Javascript Button" onClick="
       var npass = this.getField(\'password\').value;
       app.alert(\'Value of password field is: \\\'\' + npass + \'\\\'\');
       var nButton = app.alert({
       cMsg: \'Do you want to change the input text field to: T\u00f6rkylempij\u00e4?\',
       cTitle: \'A message from A. C. Robat\',
       nIcon: 2, nType: 2
       });
       if ( nButton == 4 ) {
       this.getField(\'inputfield\').value = \'T\u00f6rkylempij\u00e4\';
       }
       " />
       <br /><br />


       </form>
       ';



       //==============================================================
       $mpdf->useActiveForms = true;

       /*
       // Try playing around with these (these are also in config.php)
       $mpdf->formSubmitNoValueFields = true;

       $mpdf->formExportType = 'xfdf'; // 'html' or 'xfdf'
       $mpdf->formSelectDefaultOption = true;	// for Select drop down box; if no option is explicitly maked as selected,
                                   // this determines whether to select 1st option (as per browser)
                                   // - affects whether "required" attribute is relevant
       $mpdf->form_border_color = '0.0 0.820 0.0';
       $mpdf->form_background_color = '0.941 0.941 0.941';
       $mpdf->form_border_width = '1';
       $mpdf->form_border_style = 'S';

       $mpdf->form_radio_color = '0.0 0.820 0.0';
       $mpdf->form_radio_background_color = '0.941 0.5 0.5';

       $mpdf->form_button_border_color = '0.0 0.820 0.0';
       $mpdf->form_button_background_color = '0.941 0.941 0.941';
       $mpdf->form_button_border_width = '1';
       $mpdf->form_button_border_style = 'S';
       */

       $mpdf->WriteHTML($html);

       //==============================================================
       // JAVASCRIPT FOR WHOLE DOCUMENT
    //    $mpdf->SetJS('
    //    var dialogTitle = "Enter details";
    //    var defaultAnswer = "";
    //    var reply = app.response("This is javascript set to run when the document opens. Enter value for first field", dialogTitle, defaultAnswer);
    //    if (reply != null) {
    //    this.getField("inputfield").value = reply;
    //    }
    //    ');
       //==============================================================
       // OUTPUT

       $mpdf->Output(); exit;






    }
    public function getLeadreportData()
    { //this will fetch and show lead count in table format

        $agents = User::where('parent_id','23')
        ->whereHas('roles', function($q){
            $q->where('name','agent');
        })
        ->where('status',1)
        ->get();
        $sources = Source::whereIn('name',['Bayut','Dubizzle','Property Finder'])->where('status',1)->get();
        $start = "2024-04-01";
        $end = "2024-06-30";
       echo '<table border="1">';
       $tr = '<tr><th>Agent Name</th><th>Total Lead Count</th>';
       foreach($sources as $source){
           $tr .= '<th>'.$source->name.'</th>';
         }
         $tr .= '</tr>';
            echo $tr;
        foreach($agents as $agent){
            $leadCount = Lead::where('agent_id',$agent->id)
            ->whereBetween('created_at',[$start,$end])
            ->count();
            $leadSourceCount = [];
            foreach($sources as $source){
                $leadSourceCount[$source->name] = Lead::where('agent_id',$agent->id)
                ->where('source_id',$source->id)
                ->whereBetween('created_at',[$start,$end])
                ->count();
            }
            $data = '<tr><td>'.$agent->name.'</td><td>'.$leadCount.'</td>';
            foreach($leadSourceCount as $count){
                $data .= '<td>'.$count.'</td>';
            }
            $data .= '</tr>';
            echo $data;

        }
        echo '</table>';

    }


    public function testMakeCall()
    {
        var_dump($this->makeCall("971521569330"));
    }
    public function leadTest()
    {
        $lead = Lead::where('id', '6342')->with('criteria', 'criteria.locations.parentable')->first();

        return response()->json($lead);
    }

    public function approvalTest()
    {
        $main = Approval::find('68')->approve();
        //print_r($main);
        //$this->model = new $main->approvalable_type;
        //  $this->model::withoutApproval()->insert($main->new_data->toArray());
        //  return  $main->update(['state' => ApprovalStatus::Rejected]);

    }
    public function reraCheck(Request $request)
    {
        $purposeType = isset($request->purposeType) ? $request->purposeType : '1';
        if (isset($request->type) && $request->type == 'pro') {
            $response = Http::withHeaders([
                'authorizationkey' => '7XQEAIISX9PLFNTZBAKHQP16G',
            ])->timeout(30)->get('https://b2bgateway.dubailand.gov.ae/permit/validate/listing/authority/' . $request->permit_number);


            $response = $response->json();

            echo '--------------------------------------';
            if (is_array($response)) {
                echo '<pre>';
                print_r($response);
                echo '</pre>';
            } else {
                print_r($response);
            }
        } else {
            $response = Http::withHeaders([
                'authorizationkey' => 'O3IJUM7BVGC1F2IN9Q9YRA31R',
            ])->get('https://qagate.dubailand.gov.ae/permit/validate/listing/authority/' . $request->permit_number);
            $response = $response->json();

            echo '--------------------------------------';
            if (is_array($response)) {
                echo '<pre>';
                print_r($response);
                echo '</pre>';
            } else {
                print_r($response);
            }
        }
    }
    public function reraCheck2()
    {
        $arr = [
            "result" => "success",
        ];
        echo '<pre>';
        print_r($arr);
        echo '</pre>';
    }
    public function reraCheckQr(Request  $request)
    {

        $purposeType = isset($request->purposeType) ? $request->purposeType : '1';
        if (isset($request->type) && $request->type == 'pro') {
            $response = Http::withHeaders([
                'authorizationkey' => env('RERA_PROD_KEY'),
            ])->timeout(60)->get('https://b2bgateway.dubailand.gov.ae/permit/validate/listing/authority/' . $request->permit_number . '?isGenerateQrCode=true&purposeType=' . $purposeType);

            return response()->json($response->json());
            // $response = $response->json();


            //            if (is_array($response)) {
            //                echo '<pre>';
            //                print_r($response);
            //                echo '</pre>';
            //            } else {
            //                print_r($response);
            //            }
        } else {
            $response = Http::withHeaders([
                'authorizationkey' => 'O3IJUM7BVGC1F2IN9Q9YRA31R',
            ])->get('https://qagate.dubailand.gov.ae/permit/validate/listing/authority/' . $request->permit_number . '?isGenerateQrCode=true&purposeType=' . $purposeType);
            $response = $response->json();

            echo '--------------------------------------';
            if (is_array($response)) {
                echo '<pre>';
                print_r($response);
                echo '</pre>';
            } else {
                print_r($response);
            }
        }
    }
    public function reraCheckJson(Request $request)
    {

        $path = public_path() . ('/json/rera.json');


        if (!File::exists($path)) {
            throw new \Exception("Invalid File");
        } else {
            //read json file

            $json = json_decode(File::get($path), true);

            if (is_array($json) && !empty($json['result'])) {
                $collection = collect($json['result']);
                $sorted = $collection->sortBy('endDate'); //collection will sort only to desc order can't sort to asc order
                $sorted->values()->all();
                $collection2 = collect($sorted);
                $collection2 = $collection2->reverse(); // collection will sort to asc order
                foreach ($collection2 as $value) {
                    echo $value['startDate'] . ' -- ' . $value['endDate'] . '<br>';
                    if (!empty($value['property'])) {
                        echo $value['property']['permitTypeNameEn'] . ' -- ' . $value['property']['permitTypeNameAr'] . '<br>';
                    }
                    echo '<br>';
                }
                $collection2 = $collection->toArray();
                $selectedOne = getReleventDateRange($collection2);
                echo 'selected one --';
                print_r($selectedOne['startDate'] . ' -- ' . $selectedOne['endDate']);
            } else {
                return response()->json(['message' => 'Invalid listing or listing not found at RERA'], 422);
            }
        }
    }
    public function reraMassUpdate()
    {
        $properties = Property::where('is_live', 1)
            ->where('rera_permit_number', '!=', '')
            ->whereDate('rera_expiry_date', '<',Carbon::today())
            ->orderBy('ref_no', 'desc')
            ->limit(2000)
            ->get();

        foreach ($properties ?? [] as $property) {
            $req = new Request();
            $req->merge(['id' => $property->id]);
            echo "\n";
            echo $property->id;
            $res = (new PropertyReraController())->propertyCheckRera($req);

            //            $res = (new PropertyReraController())->reraCheckWithQr($property->rera_permit_number, $property->for);
            //            if (!empty($res)) {
            //
            //                if ($res['qr'] != '') {
            //                    $url = (new PropertyReraController())->base64ToImage($res['qr']);
            //                    $property->qr_link = $url ?? null;
            //                }
            //                $property->rera_issue_date = date('Y-m-d', strtotime($res['start_date']));
            //                $property->rera_expiry_date = date('Y-m-d', strtotime($res['end_date']));
            //                $property->withoutApproval()->save();
            //            }
        }
    }
    public function syncPermissions()
    {
        $i = 1;
        User::chunk(5, function ($users) use (&$i) {
            foreach ($users as $user) {
                $roles = $user->roles;
                echo 'No.' . $i . ' ->' . $user->id . ' -- ' . $user->name . '<br>';
                $permissions = $roles->flatMap(function ($role) {
                    return $role->permissions;
                })->unique();
                $user->syncPermissions($permissions);
                $i++;
            }
        });
    }

    public function pdfgen_bkp()
    {
        $filePath = public_path("sample.pdf");


        $outputFilePath = public_path("sample_output.pdf");
        $this->fillPDFFile($filePath, $outputFilePath);

        return response()->file($outputFilePath);
    }

    public function fillPDFFile_bkp($file, $outputFilePath)
    {
        $fpdi = new FPDI;

        $count = $fpdi->setSourceFile($file);

        for ($i = 1; $i <= $count; $i++) {

            $template = $fpdi->importPage($i);
            $size = $fpdi->getTemplateSize($template);
            $fpdi->AddPage($size['orientation'], array($size['width'], $size['height']));
            $fpdi->useTemplate($template);

            $fpdi->SetFont("helvetica", "", 11);
            $fpdi->SetTextColor(0, 0, 0);

            $left = 10;
            $top = 46;
            $text = "axcapital.ae";
            $fpdi->Text($left, $top, $text);

            $fpdi->Image("https://secondary.axcrm.ae/assets/images/logo/ax-short-logo.png", 80, 20, 20, 5, 'PNG');
        }

        return $fpdi->Output($outputFilePath, 'F');
    }

    public function pdfgen()
    {


       // $file = $this->fillAndGenerateMarketingFormALeasingFillable();




        $filePath = public_path("document-templates/FormF1Seller1Buyer.pdf");


        $outputFilePath = storage_path("app/public/FormF1Seller1Buyer.pdf");
        $this->formf1seller1buyer($filePath, $outputFilePath);

        return response()->file($outputFilePath);
    }
    public function formf1seller1buyer($file, $outputFilePath)
    {
        $fpdi = new FPDI;

        $count = $fpdi->setSourceFile($file);


        for ($i = 1; $i <= $count; $i++) {

            $template = $fpdi->importPage($i);
            $size = $fpdi->getTemplateSize($template);
            $fpdi->AddPage($size['orientation'], array($size['width'], $size['height']));
            $fpdi->useTemplate($template);
            $fpdi->SetFont('Arial', '', 8);
            // $fpdi->SetFont("helvetica", "", 11);
            $fpdi->SetTextColor(0, 0, 0);
            if($i == 1) {




                $fpdi->Text(24, 55.5, "Landlord name"); //name of owner
                $fpdi->Text(24, 63, "American"); //nationality
                $fpdi->Text(25, 70.5, "ID78978"); //ID Card number
                $fpdi->Text(23, 78, "pass 78978"); //passport number
                $fpdi->Text(15, 85.8, "56567567"); //mobile
                $fpdi->Text(15, 93.5, "7777777"); //mobile
                $fpdi->Text(15, 101, "999999"); //address
                $fpdi->Text(15, 108.5, "khagesh@demo.com"); //email


                $fpdi->Text(120, 78, "20/03/2024"); // expiry date
                $fpdi->Text(120, 86.5, "PO BOX 678"); // po box
                $fpdi->Text(120, 93, "FAX67767"); //FAX
        //buyer details
                $fpdi->Text(25, 124.5, "BUYER name"); //name of BUYER
                $fpdi->Text(24, 133, "American"); //nationality
                $fpdi->Text(26, 142, "ID78978"); //ID Card number
                $fpdi->Text(23, 150, "pass 78978"); //passport number
                $fpdi->Text(15, 159.5, "56567567"); //mobile
                $fpdi->Text(15, 168, "7777777"); //phone
                $fpdi->Text(17, 175.5, "999999"); //address
                $fpdi->Text(15, 181.5, "khagesh@demo.com"); //email
//
//
                $fpdi->Text(126, 150, "20/03/2024"); // expiry date
                $fpdi->Text(126, 159.5, "PO BOX 678"); // po box
                $fpdi->Text(126, 168, "FAX67767"); //FAX

                //property details
                $fpdi->Text(25, 210.5, "Active"); //property status
                $fpdi->Text(23, 218, "Community"); //type of area
                $fpdi->Text(23, 225.5, "Delhi"); //Location
                $fpdi->Text(28, 233, "4BHK"); //Type of property


            }else  if($i == 2) {

            }

            //  $fpdi->Image("https://secondary.axcrm.ae/assets/images/logo/ax-short-logo.png", 80, 20, 20, 5, 'PNG');
        }

        return $fpdi->Output($outputFilePath, 'F');
    }
    public function kycComplianceDForm($file, $outputFilePath)
    {
        $fpdi = new FPDI;

        $count = $fpdi->setSourceFile($file);


        for ($i = 1; $i <= $count; $i++) {

            $template = $fpdi->importPage($i);
            $size = $fpdi->getTemplateSize($template);
            $fpdi->AddPage($size['orientation'], array($size['width'], $size['height']));
            $fpdi->useTemplate($template);
            $fpdi->SetFont('Arial', '', 11);
            // $fpdi->SetFont("helvetica", "", 11);
            $fpdi->SetTextColor(0, 0, 0);
            if($i == 1) {




                $fpdi->Text(21, 77, "AXE 78787"); //we
                $fpdi->Text(140, 77, "TRADE343"); //trade license no.
                $fpdi->Text(52, 84, "REG676"); //REG NO.


                $fpdi->Text(21, 156.5, "ax capital"); //COMPANY NAME
                $fpdi->Text(142, 156.5, "TRADE34343"); //agency fee

                //share holders and passports
                $fpdi->Text(24, 171, "ax capital"); //share holder name 1
                $fpdi->Text(142, 171, "TRADE34343"); //passport number 1
                $fpdi->Text(24, 186, "ax capital"); //share holder name 2
                $fpdi->Text(142, 186, "TRADE34343"); //passport number 2
                $fpdi->Text(24, 201, "ax capital"); //share holder name 3
                $fpdi->Text(142, 201, "TRADE34343"); //passport number 2

                //table form
                $fpdi->Text(123, 240, "Yes"); //Does the firm have
                $fpdi->Text(123, 255, "Yes"); //Are the procedures
                $fpdi->SetFont('Arial', '', 9);
                $long_text = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla nec purus feugiat, molestie ipsum et";
                $broken_text = wordwrap($long_text, 40, "||");
                $exploded = explode("||", $broken_text);
                foreach ($exploded as $key => $value) {
                    $fpdi->Text(140, 233 + ($key * 4.5), $value);
                }



                $long_text = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla nec purus feugiat, molestie ipsum et";
                $broken_text = wordwrap($long_text, 40, "||");
                $exploded = explode("||", $broken_text);
                foreach ($exploded as $key => $value) {
                    $fpdi->Text(140, 250 + ($key * 4.5), $value);
                }

            }else  if($i == 2) {
                $fpdi->Text(124, 38, "Yes"); //Do you have procedures
                $fpdi->Text(124, 54, "Yes"); //Do you have procedures in place to identify Politically
                $fpdi->Text(124, 70, "Yes"); //record retention procedures
                $fpdi->Text(124, 86, "Yes"); //Has your company or any of the shareholders
                $fpdi->Text(124, 104, "Yes"); //Dose your company conduct Client Risk Asse
                $fpdi->Text(124, 120, "Yes"); //Does the firm have procedures to establish a
                $fpdi->Text(124, 136, "Yes"); //m have a process to review and, where
                $fpdi->Text(124, 154, "Yes"); //systems & procedures in place to screen
                $fpdi->Text(124, 170, "Yes"); //Do you have a designated and Independent
                $fpdi->Text(124, 186, "Yes"); //Do you have a regular AML/CFT/Sanctio
                $fpdi->Text(124, 204, "Yes"); //internal audit function


                $fpdi->SetFont('Arial', '', 9);
                $long_text = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla nec purus feugiat, molestie ipsum et";
                $broken_text = wordwrap($long_text, 40, "||");
                $exploded = explode("||", $broken_text);
                foreach ($exploded as $key => $value) {
                    $fpdi->Text(140, 32 + ($key * 4.5), $value);
                }
                //Do you have procedures in place to identify Politically
                $long_text = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla nec purus feugiat, molestie ipsum et";
                $broken_text = wordwrap($long_text, 40, "||");
                $exploded = explode("||", $broken_text);
                foreach ($exploded as $key => $value) {
                    $fpdi->Text(140, 49 + ($key * 4.5), $value);
                }
            //have record retention procedures in place
                $long_text = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla nec purus feugiat, molestie ipsum et";
                $broken_text = wordwrap($long_text, 40, "||");
                $exploded = explode("||", $broken_text);
                foreach ($exploded as $key => $value) {
                    $fpdi->Text(140, 66 + ($key * 4.5), $value);
                }
                //Has your company or any of the
                $long_text = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla nec purus feugiat, molestie ipsum et";
                $broken_text = wordwrap($long_text, 40, "||");
                $exploded = explode("||", $broken_text);
                foreach ($exploded as $key => $value) {
                    $fpdi->Text(140, 82 + ($key * 4.5), $value);
                }
                //Dose your company conduct Client Risk
                $long_text = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla nec purus feugiat, molestie ipsum et";
                $broken_text = wordwrap($long_text, 40, "||");
                $exploded = explode("||", $broken_text);
                foreach ($exploded as $key => $value) {
                    $fpdi->Text(140, 98 + ($key * 4.5), $value);
                }
                //Does the firm have procedures to establish
                $long_text = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla nec purus feugiat, molestie ipsum et";
                $broken_text = wordwrap($long_text, 40, "||");
                $exploded = explode("||", $broken_text);
                foreach ($exploded as $key => $value) {
                    $fpdi->Text(140, 114 + ($key * 4.5), $value);
                }

                //Have a process to review and, where
                //appropriate, update client
                $long_text = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla nec purus feugiat, molestie ipsum et";
                $broken_text = wordwrap($long_text, 40, "||");
                $exploded = explode("||", $broken_text);
                foreach ($exploded as $key => $value) {
                    $fpdi->Text(140, 131 + ($key * 4.5), $value);
                }
                //procedures in place to screen new
                //clients as per the regulatory requirement
                $long_text = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla nec purus feugiat, molestie ipsum et";
                $broken_text = wordwrap($long_text, 40, "||");
                $exploded = explode("||", $broken_text);
                foreach ($exploded as $key => $value) {
                    $fpdi->Text(140, 148 + ($key * 4.5), $value);
                }

                //designated and independed money laundering
                $long_text = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla nec purus feugiat, molestie ipsum et";
                $broken_text = wordwrap($long_text, 40, "||");
                $exploded = explode("||", $broken_text);
                foreach ($exploded as $key => $value) {
                    $fpdi->Text(140, 165 + ($key * 4.5), $value);
                }
                //AML/CFT/Sanctions training program
                $long_text = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla nec purus feugiat, molestie ipsum et";
                $broken_text = wordwrap($long_text, 40, "||");
                $exploded = explode("||", $broken_text);
                foreach ($exploded as $key => $value) {
                    $fpdi->Text(140, 181 + ($key * 4.5), $value);
                }
                //internal audit function
                $long_text = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla nec purus feugiat, molestie ipsum et";
                $broken_text = wordwrap($long_text, 40, "||");
                $exploded = explode("||", $broken_text);
                foreach ($exploded as $key => $value) {
                    $fpdi->Text(140, 197 + ($key * 4.5), $value);
                }
            }

            //  $fpdi->Image("https://secondary.axcrm.ae/assets/images/logo/ax-short-logo.png", 80, 20, 20, 5, 'PNG');
        }

        return $fpdi->Output($outputFilePath, 'F');
    }
    public function salesReservationForm($file, $outputFilePath)
    {
        $fpdi = new FPDI;

        $count = $fpdi->setSourceFile($file);


        for ($i = 1; $i <= $count; $i++) {

            $template = $fpdi->importPage($i);
            $size = $fpdi->getTemplateSize($template);
            $fpdi->AddPage($size['orientation'], array($size['width'], $size['height']));
            $fpdi->useTemplate($template);
            $fpdi->SetFont('Arial', '', 11);
           // $fpdi->SetFont("helvetica", "", 11);
            $fpdi->SetTextColor(0, 0, 0);
            if($i == 1) {


                    $fpdi->Image(public_path("assets/icons/check-mark.png"), 118, 34, 4, 4, 'PNG'); // residential
                    $fpdi->Image(public_path("assets/icons/check-mark.png"), 118, 39, 4, 4, 'PNG'); //commercial

                $fpdi->Image(public_path("assets/icons/check-mark.png"), 136, 48, 4, 4, 'PNG'); //contract type Individual
                $fpdi->Image(public_path("assets/icons/check-mark.png"), 158, 48, 4, 4, 'PNG'); //contract type Corporate
                $fpdi->Image(public_path("assets/icons/check-mark.png"), 179, 48, 4, 4, 'PNG'); //contract type Government

                $fpdi->Image(public_path("assets/icons/check-mark.png"), 63, 64, 4, 4, 'PNG'); //adults
                $fpdi->Image(public_path("assets/icons/check-mark.png"), 88, 64, 4, 4, 'PNG'); //children

                $fpdi->Text(49, 40, "AXE 78787"); //name of property
                $fpdi->Text(49, 50, "Tenant"); //buyer/tenant
                $fpdi->SetFont('Arial', '', 9);

                $fpdi->Text(144, 37.5, "U7877"); //unit#
                $fpdi->Text(185, 37.5, "XYZ"); //Unit Type#
                $fpdi->Text(144, 42.5, "AXE 7878"); //Unit Ref#
                $fpdi->Text(185, 42.5, "unit area"); //Unit AREA

                $fpdi->SetFont('Arial', '', 11);
                $fpdi->Text(117, 66.5, "18"); //children age
                $fpdi->Text(170, 66.5, "Married"); //marital status
                $fpdi->Text(49, 59, "Johan Doe"); //Name of occupant
                $fpdi->Text(49, 76, "AS5SY78"); //Tenant passport
                $fpdi->Text(154, 76, "AS5SY78"); //Type of visa

                $fpdi->Text(49, 85, "01/01/2004"); //Date of birth
                $fpdi->Text(154, 85, "visa7878"); //visa number

                $fpdi->Text(49, 92.5, "Software Engineer"); //Profession
                $fpdi->Text(154, 92.5, "Indian"); //visa number

                $fpdi->Text(49, 100.5, "077654981"); //Office Number
                $fpdi->Text(110, 100.5, "87374889"); //Mobile
                $fpdi->Text(170, 100.5, "SDAF8787"); //fax

                $fpdi->Text(49, 110, "test@email.com"); //email
                $fpdi->Text(49, 118.5, "Facebook Inc"); //name company
                $fpdi->Text(49, 128, "Joe Tribe"); //name of landlord

                $fpdi->Text(49, 138, "01/01/2024"); //Contract start date
                $fpdi->Text(154, 138, "2yrs"); //Lease Term

                $fpdi->Text(57, 147.5, "1,20,000"); //Annual rent
                $fpdi->Text(154, 147.5, "6"); //Lease Term

                $fpdi->Text(57, 157.5, "1,20,000"); //security dep
                $fpdi->Text(166, 157.5, "6000"); //agency fee









            }

            //  $fpdi->Image("https://secondary.axcrm.ae/assets/images/logo/ax-short-logo.png", 80, 20, 20, 5, 'PNG');
        }

        return $fpdi->Output($outputFilePath, 'F');
    }
    public function leasingReservationForm($file, $outputFilePath)
    {
        $fpdi = new FPDI;

        $count = $fpdi->setSourceFile($file);


        for ($i = 1; $i <= $count; $i++) {

            $template = $fpdi->importPage($i);
            $size = $fpdi->getTemplateSize($template);
            $fpdi->AddPage($size['orientation'], array($size['width'], $size['height']));
            $fpdi->useTemplate($template);
            $fpdi->SetFont('Arial', '', 11);
           // $fpdi->SetFont("helvetica", "", 11);
            $fpdi->SetTextColor(0, 0, 0);
            if($i == 1) {


                    $fpdi->Image(public_path("assets/icons/check-mark.png"), 118, 34, 4, 4, 'PNG'); // residential
                    $fpdi->Image(public_path("assets/icons/check-mark.png"), 118, 39, 4, 4, 'PNG'); //commercial

                $fpdi->Image(public_path("assets/icons/check-mark.png"), 136, 48, 4, 4, 'PNG'); //contract type Individual
                $fpdi->Image(public_path("assets/icons/check-mark.png"), 158, 48, 4, 4, 'PNG'); //contract type Corporate
                $fpdi->Image(public_path("assets/icons/check-mark.png"), 179, 48, 4, 4, 'PNG'); //contract type Government

                $fpdi->Image(public_path("assets/icons/check-mark.png"), 63, 64, 4, 4, 'PNG'); //adults
                $fpdi->Image(public_path("assets/icons/check-mark.png"), 88, 64, 4, 4, 'PNG'); //children

                $fpdi->Text(49, 40, "AXE 78787"); //name of property
                $fpdi->Text(49, 50, "Tenant"); //buyer/tenant
                $fpdi->SetFont('Arial', '', 9);

                $fpdi->Text(144, 37.5, "U7877"); //unit#
                $fpdi->Text(185, 37.5, "XYZ"); //Unit Type#
                $fpdi->Text(144, 42.5, "AXE 7878"); //Unit Ref#
                $fpdi->Text(185, 42.5, "unit area"); //Unit AREA

                $fpdi->SetFont('Arial', '', 11);
                $fpdi->Text(117, 66.5, "18"); //children age
                $fpdi->Text(170, 66.5, "Married"); //marital status
                $fpdi->Text(49, 59, "Johan Doe"); //Name of occupant
                $fpdi->Text(49, 76, "AS5SY78"); //Tenant passport
                $fpdi->Text(154, 76, "AS5SY78"); //Type of visa

                $fpdi->Text(49, 85, "01/01/2004"); //Date of birth
                $fpdi->Text(154, 85, "visa7878"); //visa number

                $fpdi->Text(49, 92.5, "Software Engineer"); //Profession
                $fpdi->Text(154, 92.5, "Indian"); //visa number

                $fpdi->Text(49, 100.5, "077654981"); //Office Number
                $fpdi->Text(110, 100.5, "87374889"); //Mobile
                $fpdi->Text(170, 100.5, "SDAF8787"); //fax

                $fpdi->Text(49, 110, "test@email.com"); //email
                $fpdi->Text(49, 118.5, "Facebook Inc"); //name company
                $fpdi->Text(49, 128, "Joe Tribe"); //name of landlord

                $fpdi->Text(49, 138, "01/01/2024"); //Contract start date
                $fpdi->Text(154, 138, "2yrs"); //Lease Term

                $fpdi->Text(57, 147.5, "1,20,000"); //Annual rent
                $fpdi->Text(154, 147.5, "6"); //Lease Term

                $fpdi->Text(57, 157.5, "1,20,000"); //security dep
                $fpdi->Text(167, 157.5, "6000"); //agency fee









            }

            //  $fpdi->Image("https://secondary.axcrm.ae/assets/images/logo/ax-short-logo.png", 80, 20, 20, 5, 'PNG');
        }

        return $fpdi->Output($outputFilePath, 'F');
    }
    public function salesletterOfIntent($file, $outputFilePath)
    {
        $fpdi = new FPDI;

        $count = $fpdi->setSourceFile($file);


        for ($i = 1; $i <= $count; $i++) {

            $template = $fpdi->importPage($i);
            $size = $fpdi->getTemplateSize($template);
            $fpdi->AddPage($size['orientation'], array($size['width'], $size['height']));
            $fpdi->useTemplate($template);

            $fpdi->SetFont("helvetica", "", 11);
            $fpdi->SetTextColor(0, 0, 0);
            if($i == 1) {

                $fpdi->Text(32, 86, "20/02/2024"); //Date
                $fpdi->Text(42, 106, "AXE-34324234"); //Property
                $fpdi->Text(42, 119, "Subject"); //Subject
                $fpdi->Text(44, 132, "Kiran Rao"); //Purchaser



                $fpdi->Text(35, 166, "50,000"); //Offer
                $fpdi->Text(66, 178, "9000"); //Agency commission

                $fpdi->Text(63, 192, "2000"); //Security Deposit
                $fpdi->Text(135, 192, "XYZ"); //held by


                $fpdi->Text(25, 240, "1500"); //WITHOUT liability




            }

            //  $fpdi->Image("https://secondary.axcrm.ae/assets/images/logo/ax-short-logo.png", 80, 20, 20, 5, 'PNG');
        }

        return $fpdi->Output($outputFilePath, 'F');
    }
    public function keyHandoverForm($file, $outputFilePath)
    {
        $fpdi = new FPDI;

        $count = $fpdi->setSourceFile($file);


        for ($i = 1; $i <= $count; $i++) {

            $template = $fpdi->importPage($i);
            $size = $fpdi->getTemplateSize($template);
            $fpdi->AddPage($size['orientation'], array($size['width'], $size['height']));
            $fpdi->useTemplate($template);

            $fpdi->SetFont("helvetica", "", 11);
            $fpdi->SetTextColor(0, 0, 0);
            if($i == 1) {

                $fpdi->Text(30, 78, "20/02/2024"); //Date
                $fpdi->Text(95, 78, "01:00 PM"); //Time
                $fpdi->Text(50, 96, "AXE-34324234 Apartment"); //Property details
                $fpdi->Text(40, 113, "Left side"); //Main door
                $fpdi->Text(122, 113, "Parking"); //Parking
                $fpdi->Text(49, 122, "Left side"); //Main door keys
                $fpdi->Text(130, 122, "access card"); //access card
                $fpdi->Text(62, 131, "SYJ"); //Main Door Access Card
                $fpdi->Text(130, 131, "Vijay Transporter"); //Transporter
                $fpdi->Text(70, 139, "yty"); //Main Door Remote Control
                $fpdi->Text(137, 139, "JDFU"); //Main Door Remote Control
                $fpdi->Text(34, 149, "other"); //Main Door Remote Control
                $long_text = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla nec purus feugiat, molestie ipsum et, consequat nibh. Etiam non elit dui. Nulla nec purus feugiat, molestie ipsum et, consequat nibh. Etiam non elit dui. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit.";
                $broken_text = wordwrap($long_text, 100, "||");
                $exploded = explode("||", $broken_text);
                foreach ($exploded as $key => $value) {
                    $fpdi->Text(20, 162 + ($key * 7.5), $value);
                }
                $fpdi->Text(23, 196, "Rajesh kumar"); //first person name
                $fpdi->Text(21, 207, "Rakhi Verma"); //first person name



            }

            //  $fpdi->Image("https://secondary.axcrm.ae/assets/images/logo/ax-short-logo.png", 80, 20, 20, 5, 'PNG');
        }

        return $fpdi->Output($outputFilePath, 'F');
    }

    public function formAcontract($file, $outputFilePath)
    {
        $fpdi = new FPDI;

        $count = $fpdi->setSourceFile($file);


        for ($i = 1; $i <= $count; $i++) {

            $template = $fpdi->importPage($i);
            $size = $fpdi->getTemplateSize($template);
            $fpdi->AddPage($size['orientation'], array($size['width'], $size['height']));
            $fpdi->useTemplate($template);

            $fpdi->SetFont("helvetica", "", 11);
            $fpdi->SetTextColor(0, 0, 0);
            if($i == 1) {

                $fpdi->Text(88, 55.6, "CONTR34343"); //Contract number
                $fpdi->Text(40, 84, "AX Capital"); //Office name
                $fpdi->Text(45, 91, "DUBAI LAND DEPT"); //License authority
                $fpdi->Text(30, 97, "ORN7878"); //ORN name
                $fpdi->Text(135, 97, "license 687687"); //License number
                $fpdi->Text(30, 104, "FAX 6767"); //Fax
                $fpdi->Text(121, 104, "PHONE NUMBER 6767"); //PHONE NUMBER
                $fpdi->Text(128, 111, "Arabic"); //العنوان
                $fpdi->Text(35, 111, "main business block"); //Address
                $fpdi->Text(35, 119, "info@axcrm.ae"); //email
                $fpdi->Text(40, 125, "Agent sadfkj"); //agent name
                $fpdi->Text(128, 125, "Agent sadfkj"); //اسم الوسيط
                $fpdi->Text(32, 132, "BRN3434"); //BRN
                $fpdi->Text(122, 132, "67687687"); //MOBILE

                //THE PROPERTY OWNER’S DETAILS
                $fpdi->Text(43, 150, "Owner name left"); //Name of owner
                $fpdi->Text(125, 150, "Owner name right"); //Name of owner right
                $fpdi->Text(43, 157, "ID7878"); //ID CARD NUMBER
                $fpdi->Text(127, 157, "DUBAI"); //Nationality
                $fpdi->Text(40, 164, "PASSPORT3434"); //PASSPORT NUMBER
                $fpdi->Text(35, 171, "4545454"); //OWNER MOBILE
                $fpdi->Text(35, 178, "07715436677"); //OWNER PHONE
                $fpdi->Text(35, 185, "SACRED COLONY"); //ADDRESS
                $fpdi->Text(35, 192, "OWNER@GMAIL.COM"); //OWNER EMAIL
                $fpdi->Text(128, 164, "01/01/2024"); //Expiry date
                $fpdi->Text(125, 171, "UAE PO OFFICE"); //PO BOX
                $fpdi->Text(124, 178, "OWNER FAX NUMBER"); //OWNER FAX
                $fpdi->Text(124, 185, "JLSDF JDSF"); //العنوان


            }else if($i == 2) {


                $fpdi->Text(88, 55.6, "CONTR34343"); //Contract number
                $fpdi->Text(42, 82, "AX Capital"); //PROPERTY STATUS
                $fpdi->Text(40, 89, "COMMERCIAL"); //Type of Area
                $fpdi->Text(36, 96, "Location"); //Location
                $fpdi->Text(44, 103, "Condo"); //Type of Property
                $fpdi->Text(32, 110, "AREA 51"); //Area
                $fpdi->Text(40, 118, "rented"); //present use
                $fpdi->Text(55, 124, "5yr"); //Approx Age
                $fpdi->Text(50, 131, "10"); //no. of car parks
                $fpdi->Text(50, 138, "10"); //no of bathrooms
                $fpdi->Text(55, 145, "10"); //unit no.
                $fpdi->Text(40, 152, "10"); //no. of floor
                $fpdi->Text(35, 159, "swimming pool, community hall, gym"); //facilities
                $fpdi->Text(42, 166, "Club, party hall"); //extra facilities
                $fpdi->Text(51, 173, "family only colony"); //additional information

                $fpdi->Text(130, 82, "AX Capital"); //plot number
                $fpdi->Text(136, 89, "Title deed"); //Title Deed Number
                $fpdi->Text(135, 96, "PROP67"); //property number
                $fpdi->Text(130, 103, "Raheja"); //Project Name
                $fpdi->Text(140, 110, "JDSA8787878"); //Owner’s Association No
                $fpdi->Text(140, 117, "COM 78"); //Owner’s Association No
                $fpdi->Text(140, 131, "5"); //no. of BEDROOMS
                $fpdi->Text(140, 138, "2"); //no. of kitchen
                $fpdi->Text(140, 145, "4"); //floor no.

                $fpdi->Text(135, 159, "TEXT 343"); //الخدمات
                $fpdi->Text(135, 166, "TEXT 222"); //
                $fpdi->Text(135, 173, "TEXT 1111"); //

            //PROPERTY FINANCIAL
                $fpdi->Text(40, 191, "5,00,00"); //LISTED PRICE
                $fpdi->Text(132, 191, "4,70,00"); //Original PRICE
                $fpdi->Text(40, 198, "4,90,00"); //Paid amount
                $fpdi->Text(134, 198, "20,00"); //Balance amount
                $fpdi->Text(43, 205, "900"); //service charge
                $fpdi->Text(45, 212, "ACTIVE"); //Mortgage status
                $fpdi->Text(30, 219, "Central Bank"); //Bank
                $fpdi->Text(49, 226, "10000"); //pre-closure charges
                $fpdi->Text(48, 233, "10000"); //Payment schedule

                $fpdi->Text(70, 247, "20/02/2024"); //date
                $fpdi->Text(70, 254, "20/02/2024"); //date
                $fpdi->Text(125, 247, "3000"); //amount
                $fpdi->Text(125, 254, "4000"); //amount


            }else if($i == 3) {
                //page 3
                $fpdi->Text(88, 55.6, "CONTR34343"); //Contract number
                $fpdi->Text(50, 75, "Yes"); //is property rented
                $fpdi->Text(150, 75, "Yes"); //هل العقار مؤجر؟

                $fpdi->Text(50, 93, "01/04/2024"); //contract start date
                $fpdi->Text(57, 100, "4000"); //commission amount
                $fpdi->Text(41, 107, "Full"); //contract type

                $fpdi->Text(138, 93, "01/04/2024"); //contract end date
                $fpdi->Text(132, 100, "4500"); //commission amount
                $fpdi->Text(135, 107, "Part"); //contract type
                //FIRST PARTY
                $fpdi->Text(32, 132, "khagesh chandrakar"); //name
                $fpdi->Text(135, 132, "khagesh chandrakar"); //name
                $fpdi->Text(32, 139, "Some title"); //title
                $fpdi->Text(32, 146, "01/04/2024"); //date

                $fpdi->Text(22, 198, "Rakesh"); //owner name 1
                $fpdi->Text(22, 205, "Akash"); //owner name 2
                $fpdi->Text(22, 212, "MEENA"); //owner name 3

                $fpdi->Text(90, 198, "Rakesh"); //owner name 1
                $fpdi->Text(90, 205, "Akash"); //owner name 2
                $fpdi->Text(90, 211, "MEENA"); //owner name 3

                $fpdi->Text(22, 238, "Anita"); //Legal Representative
                $fpdi->Text(22, 245, "Suresh"); //Legal Representative
                $fpdi->Text(22, 252, "Kailash"); //Legal Representative

                $fpdi->Text(70, 238, "Anita"); //Legal Representative arabic
                $fpdi->Text(70, 245, "Suresh"); //Legal Representative arabic
                $fpdi->Text(70, 252, "Kailash"); //Legal Representative arabic

                $fpdi->Text(117, 238, "435435"); //attorney number
                $fpdi->Text(117, 245, "3454345"); //attorney number
                $fpdi->Text(117, 252, "34534534"); //attorney number
            }

            //  $fpdi->Image("https://secondary.axcrm.ae/assets/images/logo/ax-short-logo.png", 80, 20, 20, 5, 'PNG');
        }

        return $fpdi->Output($outputFilePath, 'F');
    }
    public function formB($file, $outputFilePath)
    {
        $fpdi = new FPDI;

        $count = $fpdi->setSourceFile($file);


        for ($i = 1; $i <= $count; $i++) {

            $template = $fpdi->importPage($i);
            $size = $fpdi->getTemplateSize($template);
            $fpdi->AddPage($size['orientation'], array($size['width'], $size['height']));
            $fpdi->useTemplate($template);

            $fpdi->SetFont("helvetica", "", 9);
            $fpdi->SetTextColor(0, 0, 0);
            if($i == 1) {

                $fpdi->Text(93, 52.5, "CONTR34343"); //Contract number
//                $fpdi->Text(46, 78, "AX Capital"); //Office name
//                $fpdi->Text(52, 85, "DUBAI LAND DEPT"); //License authority
//                $fpdi->Text(40, 92, "ORN7878"); //ORN name
//                $fpdi->Text(136, 93, "license 687687"); //License number
//                $fpdi->Text(30, 104, "FAX 6767"); //Fax
//                $fpdi->Text(121, 104, "PHONE NUMBER 6767"); //PHONE NUMBER
//                $fpdi->Text(128, 111, "Arabic"); //العنوان
//                $fpdi->Text(35, 111, "main business block"); //Address
//                $fpdi->Text(35, 119, "info@axcrm.ae"); //email
//                $fpdi->Text(40, 125, "Agent sadfkj"); //agent name
//                $fpdi->Text(128, 125, "Agent sadfkj"); //اسم الوسيط
//                $fpdi->Text(32, 132, "BRN3434"); //BRN
//                $fpdi->Text(122, 132, "67687687"); //MOBILE

                //THE PROPERTY OWNER’S DETAILS
                $fpdi->Text(48, 141, "Owner name left"); //Name of owner
                $fpdi->Text(50, 147, "ID7878"); //ID CARD NUMBER
                $fpdi->Text(128, 147, "DUBAI"); //Nationality

                $fpdi->Text(45, 154, "PASSPORT3434"); //PASSPORT NUMBER
                $fpdi->Text(40, 161, "4545454"); //OWNER MOBILE
                $fpdi->Text(40, 167, "07715436677"); //OWNER PHONE
                $fpdi->Text(42, 175, "SACRED COLONY"); //ADDRESS
                $fpdi->Text(37, 180, "OWNER@GMAIL.COM"); //OWNER EMAIL
                $fpdi->Text(128, 141, "Owner name right"); //Name of owner right
                $fpdi->Text(129, 154, "01/01/2024"); //Expiry date
                $fpdi->Text(125, 161, "UAE PO OFFICE"); //PO BOX
                $fpdi->Text(124, 167, "OWNER FAX NUMBER"); //OWNER FAX
                $fpdi->Text(124, 175, "JLSDF JDSF"); //العنوان

                $fpdi->Text(45, 203, "AX Capital Family Homes"); //Type of area
                $fpdi->Text(50, 210, "COMMERCIAL"); //Type of property
                $fpdi->Text(50, 216, "6000"); //Buyer’s Budget:
                $fpdi->Text(37, 223, "1890sqft"); //Area
                $fpdi->Text(55, 230, "Yes"); //Tenanted property
                $fpdi->Text(130, 229, "Yes"); //present use
                $fpdi->Text(127, 203, "Burj khalifa"); //Location
                $fpdi->Text(56, 237, "Should be couple"); //special req

            }else if($i == 2) {


                $fpdi->Text(92, 52.5, "CONTR444"); //Contract number
                $fpdi->Text(45, 71, "485000"); //Ready cash
                $fpdi->Text(132, 71, "485000"); //Finance aed
                $fpdi->Text(51, 77, "20/04/2024"); //date of approval
                $fpdi->Text(132, 77, "Central Bank"); //BANK
            //commission and duration of the agreement
                $fpdi->Text(45, 94, "20/04/2024"); //start date
                $fpdi->Text(130, 94, "20/04/2024"); //end date
                $fpdi->Text(56, 101, "2050"); //Broker commission
                $fpdi->Text(135.5, 100.5, "Central Bank"); //activity reporting

//FIRST PARTY [THE BROKER OFFICE]
                $fpdi->Text(40, 132, "JDSA8787878"); //title
                $fpdi->Text(40, 137, "01/02/2024"); //date




            //second party
                $fpdi->Text(43, 186, "Mahesh"); //Buyer name english
                $fpdi->Text(43, 191, "Suresh"); //Buyer name english
                $fpdi->Text(43, 196, "Dinesh"); //Buyer name english

                $fpdi->Text(98, 186, "Mahesh"); //Buyer name arabic
                $fpdi->Text(98, 191, "Suresh"); //Buyer name arabic
                $fpdi->Text(98, 196, "Dinesh"); //Buyer name arabic

                $fpdi->Text(37, 223, "Anita"); //Legal Representative
                $fpdi->Text(37, 228, "Suresh"); //Legal Representative
                $fpdi->Text(37, 233, "Kailash"); //Legal Representative


                $fpdi->Text(80, 223, "Anita"); //Legal Representative arabic
                $fpdi->Text(80, 228, "Suresh"); //Legal Representative arabic
                $fpdi->Text(80, 233, "Kailash"); //Legal Representative arabic


                $fpdi->Text(117, 223, "435435"); //attorney number
                $fpdi->Text(117, 228, "3454345"); //attorney number
                $fpdi->Text(117, 233, "34534534"); //attorney number





            }

            //  $fpdi->Image("https://secondary.axcrm.ae/assets/images/logo/ax-short-logo.png", 80, 20, 20, 5, 'PNG');
        }

        return $fpdi->Output($outputFilePath, 'F');
    }
    public function kycEntityGen($file, $outputFilePath)
    {
        $fpdi = new FPDI;

        $count = $fpdi->setSourceFile($file);


        for ($i = 1; $i <= $count; $i++) {

            $template = $fpdi->importPage($i);
            $size = $fpdi->getTemplateSize($template);
            $fpdi->AddPage($size['orientation'], array($size['width'], $size['height']));
            $fpdi->useTemplate($template);

            $fpdi->SetFont("helvetica", "", 11);
            $fpdi->SetTextColor(0, 0, 0);
        if($i == 1) {

            $fpdi->Text(70, 120, "AX Capital"); //Full legal name
            $fpdi->Text(70, 135, "Trading AX Capital"); //Trading name
            $fpdi->Text(70, 148, "Trading Number"); //Trading number
            $fpdi->Text(100, 162, "Dubai"); //Country of incorporation
            $fpdi->Text(93, 176, "20/01/2024"); //Date of incorporation
            $fpdi->Text(70, 188, "Business park"); //Registered address
            $fpdi->Text(85, 201, "Dubai Business Dept"); //Trade license issuing authority
            //contact details
            $fpdi->Text(28, 230, "342343242"); //Telephone number
            $fpdi->Text(72, 230, "info@axcrm.ae"); //Email id
            $fpdi->Text(114, 230, "01/01/2024"); //Trade license issue date:
            $fpdi->Text(160, 230, "01/01/2024"); //Trade license expiry date
            //ubo details
            $fpdi->Text(160, 330, "khagesh"); //Trade license expiry date

        }else if($i == 2) {
            $fpdi->Text(50, 51, "Mukesh Verma"); //Full name
            $fpdi->Text(172, 51, "20%"); //Percentage owed
            $fpdi->Text(50, 69, "Rina Verma"); //Full name
            $fpdi->Text(172, 69, "20%"); //Percentage owed

            $fpdi->Text(33, 109, "Johan Doe"); //Full name
            $fpdi->Text(135, 109, "Director"); //Relationship

            $fpdi->Text(33, 122, "Alex Doe"); //Full name
            $fpdi->Text(135, 122, "Director"); //Relationship

            $fpdi->Text(35, 163, "Call Center"); //Business details. (products/ services)
            $fpdi->Text(35, 191, "USA, Canada, Brazil"); //Which are the primary countries

        }else if($i == 3) {
            //page 3

            $fpdi->Text(27, 50, "Manager"); //individual’s occupation
            $fpdi->Text(112, 50, "Google inc"); //f Establishment or Employer
            //countries
            $fpdi->Image(public_path("assets/icons/dot.png"), 127.65, 70.8, 9, 9, 'PNG'); //if yes
            $fpdi->Image(public_path("assets/icons/dot.png"), 127.65, 75.31, 9, 9, 'PNG'); //if No
            //PEP
            $fpdi->Image(public_path("assets/icons/dot.png"), 138.2, 109, 9, 9, 'PNG'); //if Yes
            $fpdi->Image(public_path("assets/icons/dot.png"), 138.2, 113.6, 9, 9, 'PNG'); //if No

            $fpdi->Text(90, 190, "ALEX"); //person acting on behalf of company
            $fpdi->Text(90, 201, "CEO"); //relationship with the company
            $fpdi->Text(90, 210, "20/02/2024"); //date
        }

            //  $fpdi->Image("https://secondary.axcrm.ae/assets/images/logo/ax-short-logo.png", 80, 20, 20, 5, 'PNG');
        }

        return $fpdi->Output($outputFilePath, 'F');
    }
    public function fillPDFFile($file, $outputFilePath)
    {
        $fpdi = new FPDI;

        $count = $fpdi->setSourceFile($file);

        for ($i = 1; $i <= $count; $i++) {

            $template = $fpdi->importPage($i);
            $size = $fpdi->getTemplateSize($template);
            $fpdi->AddPage($size['orientation'], array($size['width'], $size['height']));
            $fpdi->useTemplate($template);

            $fpdi->SetFont("helvetica", "", 11);
            $fpdi->SetTextColor(0, 0, 0);

            $fpdi->Text(25, 41, "Owner Name");
            $fpdi->Text(27, 49, "Landlord Name");

            $fpdi->Text(27, 56, "Tenant Name");

            $fpdi->Text(27, 63, "Tenant Email");
            $fpdi->Text(130, 63, "Landlord Email");

            $fpdi->Text(27, 71, "Tenant Phone");
            $fpdi->Text(130, 71, "Landlord Phone");

            $fpdi->Text(27, 78, "Building Name");
            $fpdi->Text(130, 78, "Location");


            $fpdi->Text(30, 86, "1200"); //Property Size
            $fpdi->Text(100, 86, "Commercial"); //Property Type

            $fpdi->Image("https://secondary.axcrm.ae/assets/images/logo/ax-short-logo.png", 80, 20, 20, 5, 'PNG');
        }

        return $fpdi->Output($outputFilePath, 'F');
    }

    public function requestZipFile($request){
        $property = Property::find(7610);

            $property->load([
                "owner:id,first_name,last_name,passport,passport_expiry",
                "propertyStatus:id,title",
                "community:id,name",
                "bed:id,name",
                "bath:id,name",
                "price:id,price",
                "agent:id,name",
            ]);

            $folder = createPropertyFolder($property);

            if ($folder !== false) {
                $temporaryFolderPath = "file-manager/temporary-archives/".$property->ref_no . "/";
                $zipFileName = $property->ref_no . time();

                if($request->boolean("marketing-form-a-leasing-fillable")){
                    $marketingFormFile = FolderMedia::where("data->type", "marketing-form-a-leasing-fillable")
                    ->where("data->property_id", $property->id)->latest()->first();

                    if(is_null($marketingFormFile)){
                        $data = $this->prepareMarketingFormData($property);

                        $name = "Marketing Form A - Leasing Fillable generated by " . auth()->user()->name. " on " . date("Y-m-d h-i-s");
                        $slug = \Illuminate\Support\Str::slug($name);

                        $path =  $this->fillAndGenerateMarketingFormALeasingFillable($data, $slug, $folder["folder_path"]);
                        if (is_null($path)) {
                            return response()->json(['status' => '0', 'msg' => 'Unable to generate file.']);
                        }
                        $fileName = $folder["folder_path"] . $slug . ".pdf";
                        $data["filename"] = $slug . ".pdf";
                        $marketingFormFile = FolderMedia::updateOrCreate([
                            "folder_id" => $folder["id"],
                            "title" => $name,
                        ], [
                            "type" => "Document",
                            "path" => $fileName,
                            "extension" => "pdf",
                            "size" => Storage::disk("local")->size("public/".$fileName) >> 10, //Size in KB
                            "created_by_id" => auth()->id(),
                            "data" => $data,
                            "data_editable" => true
                        ]);
                    }

                    $path = $this->copyFileFromUrlToFolder("http://127.0.0.1:8001/storage/" . $marketingFormFile->path, "", $temporaryFolderPath);
                }

                $propertyMedia = PropertyMedia::where("property_id", $property->id)->get();

                foreach($propertyMedia as $media){
                    $fileName = ($media->title ?? pathinfo($media->path, PATHINFO_FILENAME));
                    if(!$request->boolean("title_deed") && $fileName == "Title Deed"){
                        continue;
                    }

                    if(!$request->boolean("owner_passport_copy") && $fileName == "Owner Passport Copy"){
                        continue;
                    }

                    if(!$request->boolean("signed_listing_form") && $fileName == "Signed Listing Form"){
                        continue;
                    }

                    if(!$request->boolean("photos") && $media["type"] == "Photos"){
                        continue;
                    }

                    $this->copyFileFromUrlToFolder($media->path, $fileName, $temporaryFolderPath);
                }

                $filePath = $this->makeZipFromFolder($temporaryFolderPath, $zipFileName);

                $sourceFilePath = storage_path("app/public/". $filePath);
                if(File::exists($sourceFilePath)){
                    $destinatationPath = storage_path("app/public/". $folder["folder_path"] .$zipFileName. ".zip");
                    File::move($sourceFilePath, $destinatationPath);
                    FolderMedia::create([
                        "folder_id" => $folder["id"],
                        "type" => getFolderMediaTypeByExtension($path["extension"]),
                        "title" => $zipFileName,
                        "path" => $folder["folder_path"] .$zipFileName. ".zip",
                        "extension" => "zip",
                        "size" => Storage::disk("local")->size("public/".$folder["folder_path"] .$zipFileName. ".zip") >> 10, //Size in KB
                        "created_by_id" => auth()->id(),
                    ]);
                }
                $result = array('status' => '1', 'msg' => "Listing zip request sent successfully.", "downloadUrl" => asset("storage/" . $folder["folder_path"] .$zipFileName. ".zip"));
                return json_encode($result);
            }
    }

    public function downloadZipFileTest(Request $request)
    {



        $this->requestZipFile($request);

        die;



        // echo "<pre>";
        // print_r($property->toArray());die;
















        echo "<pre>";
        print_r($data);die;
        print_r($marketingFormFile->toArray());die;
        print_r($propertyFolderPath);die;


        $folder = Folder::find(168);

        $folder->withAnyTags(["tag 1"]);

        echo "<pre>";
        print_r($folder->toArray());die;

        $tags = Tag::all();

        echo "<pre>";
        print_r($tags->toArray());die;





        $property = Property::find(300);

        createPropertyFolder($property);

        // die;

        echo "<pre>";
        print_r($property->toArray());die;




        $test = $this->makeZipFromFolder("root-folder-1/sub-folder-1", "sub-folder-1");
        var_dump($test);die;
        // die;
        // $test = $this->makeZipFromFolder($property->ref_no);

        // return response()->download($test, $property->ref_no .".zip");





        $folder = Folder::create([
            "name" => $property->ref_no,
            "slug" => generateFolderSlug($property->ref_no),
            "folder_order" => 1,
            "model_type" => Property::class,
            "model_id" => $property->id,
            "created_by_id" => auth()->id()
        ]);


        $propertyMedia = PropertyMedia::where("property_id", $property->id)->get();

        foreach($propertyMedia as $media){
            $fileName = ($media->title ?? ($media->type . "_" .time().date("YmdHis")));
            $path = $this->copyFileFromUrlToFolder($media->original, $fileName, $folder->slug);
            if(!is_null($path)){
                if (!empty($path)) {
                    FolderMedia::create([
                        "folder_id" => $folder->id,
                        "type" => "Document",
                        "title" => $path["filename"],
                        "path" => $path["path"],
                        "extension" => $path["extension"],
                        "size" => $path["size"] >> 10, //Size in KB
                        "created_by_id" => auth()->id(),
                    ]);
                }
            }
        }
    }
}
