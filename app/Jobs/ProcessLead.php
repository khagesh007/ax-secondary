<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Lead;
use App\Models\ListingLead;
use App\Models\ListingLeadNote;
use App\Models\LeadNote;
use App\Models\Medium;
use App\Models\Community;
use App\Models\SubCommunity;
use App\Models\CommunityProperty;
use App\Models\LeadCriteriaLocation;
use App\Models\LeadCriteria;
use App\Models\Property;
use App\Models\Source;
use App\Models\Type;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Crypt;
use App\Models\User;
use App\Traits\BitrixTrait;
use App\Traits\TelegramTrait;

class ProcessLead implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $bitrixId;
    public $from_agent_id;
    public $to_agent_id;
    use TelegramTrait;
    use BitrixTrait;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->bitrixId = $data['bitrix_lead_id'];
        // $this->bitrixId = 2019180;
        $this->from_agent_id = $data['from_agent_id'] ?? null;
        $this->to_agent_id = $data['to_agent_id'] ?? null;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // info($this->bitrixId);
        // info("Running this job");
        // info($this->from_agent_id);
        // info($this->to_agent_id);
        $requestData = [
            'file' => "Process Lead",
            'bitrix_id' => $this->bitrixId,
            'from_agent_id' => $this->from_agent_id,
            'to_agent_id' => $this->to_agent_id
        ];
        info($requestData);
        try {

                if($this->to_agent_id){
                    $abuDhabiAgent = User::where('region_id', 2)->where('bitrix_id', $this->to_agent_id)->first();
                    if($abuDhabiAgent){
                        return true;
                    }
                }

                // Api User fields
                $apiSourceDetails   = "UF_CRM_1553506792";
                $apiLocation        = "UF_CRM_1553171363";
                $apiLeadType        = "UF_CRM_1561214850";
                $apiLanguage        = "UF_CRM_1553165368131";
                $apiNationality     = "UF_CRM_1553166948";
                $agent = null;
                $prevAgent = null;

                // Api User fields end here

                $header  = array();
                $header[] = 'Content-Type: application/json';
                $header[] = 'Content-Length: 4000';
                $header[] = 'Connection: keep-alive';

                // UF_CRM_1553171363 is the key for location
                $response = Http::get('https://crm.axcap.ae/rest/1/8cd3l0kbdlndvtqf/crm.lead.userfield.list?filter[FIELD_NAME]=UF_CRM_1553171363');
                $res = $response->json();
                $locs = $res['result'][0]['LIST'];
                $locations = array();
                // Putting the locations in one dimension array so I can get the data by id based.
                foreach ($locs as $record) {
                  $locations[$record['ID']] = $record['VALUE'];
                }
                // UF_CRM_1553165368131 is key for language
                $response = Http::get('https://crm.axcap.ae/rest/1/8cd3l0kbdlndvtqf/crm.lead.userfield.list?filter[FIELD_NAME]=UF_CRM_1553165368131');
                $res = $response->json();
                $langs = $res['result'][0]['LIST'];

                $languages = array();
                // Putting the languages in one dimension array so I can get the data by id based.
                foreach ($langs as $record) {
                  $languages[$record['ID']] = $record['VALUE'];
                }
                // UF_CRM_1553166948 is key for nationality
                $response = Http::get('https://crm.axcap.ae/rest/1/8cd3l0kbdlndvtqf/crm.lead.userfield.list?filter[FIELD_NAME]=UF_CRM_1553166948');
                $res = $response->json();
                $nation = $res['result'][0]['LIST'];

                $nationalities = array();
                // Putting the languages in one dimension array so I can get the data by id based.
                foreach ($nation as $record) {
                  $nationalities[$record['ID']] = $record['VALUE'];
                }

                // UF_CRM_1561214850 user field for lead type
                $lead_type_array = array(
                    6793 => 2, // Buyer
                    6794 => 3, // Landlord
                    6814 => 2, // Buyer
                    6795 => 1, // Tenant
                    6796 => 3, // Landlord
                    7263 => 12, // Agency
                    7273 => 11, // Unspecified
                    12245 => 11, // Unspecified
                    17238 => 11, // Unspecified
                );
                $leadStatusesArray =  array(
                    '4' => 10, //Not answered
                    'IN_PROCESS' => 5, //Contacted
                    "2" => 8, // Qualified
                    '3' => 9, // Successful
                    '6' => 5, // Contacted
                    '1' => 1, // New
                    'NEW' => 1,
                    'PROCESSED' => 8, // Qualified
                    'CONVERTED' => 8, // Successful
                );

                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => 'https://crm.axcap.ae/rest/1/8cd3l0kbdlndvtqf/crm.lead.list',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => array('ORDER[ID]' => 'ASC', 'FILTER[ID]' => $this->bitrixId, 'SELECT[0]' => '*', 'SELECT[1]' => 'PHONE', 'SELECT[2]' => 'EMAIL', 'SELECT[3]' => "UF_*"),
                        CURLOPT_HTTPHEADER => array(
                            'Cookie: BITRIX_SM_SALE_UID=0; PHPSESSID=KpxDF0YR3tW047SdI7gmD1XxVm7x5Ytw'
                        ),
                    ));
                    $response = curl_exec($curl);
                    curl_close($curl);
                    $response = json_decode($response);
                    $next = $response?->next ?? null;
                    $updated = 0;

                    foreach ($response?->result ?? [] as $res) {

                        $mobile = '';
                        $email = '';
                        $rawMobile = '';
                        if ($res->HAS_PHONE == 'Y' && $res?->PHONE[0]->VALUE != '') {
                            $mobile = $rawMobile = intval(preg_replace('/[^0-9]+/', '', $res->PHONE[0]->VALUE));
                            $mobile = mobileNumberFixer($mobile);
                        }
                        if ($res->HAS_EMAIL == 'Y' && $res?->EMAIL[0]->VALUE != '') {
                            $email = $res->EMAIL[0]->VALUE;
                            if (Str::length($email) < 5) {
                                $email = null;
                            }
                        }

                        $source = findSourceForBitrix($res->SOURCE_ID);

                        $sourceName = findSourceNameForBitrix($res->SOURCE_ID);

                        $source = Source::whereRaw('UPPER(name) LIKE ?', ['%' . strtoupper($source) . '%'])->pluck('id')->first() ?? null;
                        $medium = findMediumForBitrix($res->SOURCE_ID);
                        $medium = Medium::whereRaw('UPPER(name) LIKE ?', ['%' . strtoupper($medium) . '%'])->pluck('id')->first() ?? null;
                        $lead = Lead::where('bitrix_id', $res->ID)->where('need_to_be_destroyed', 0)->first();

                        if (!empty($lead)) {
                            // $lead->disableLogging();
                            $lead->whatsapp = $mobile ?? null;
                            $lead->lead_medium_id = $lead->lead_medium_id != null ? $lead->lead_medium_id : $medium;
                            $lead->for = $lead->for != null ? $lead->for : 'Unspecified';
                            $lead->lead_status_id = $leadStatusesArray[$res->STATUS_ID] ?? 3; // 3 unspecified
                            // $lead->lead_sub_status_id = '8'; // 11 unspecified
                            if($res->UF_CRM_1561214850){
                                $lead->type_id = $lead_type_array[$res->UF_CRM_1561214850];
                            }else{
                                $lead->type_id = '11'; // 11 unspecified
                            }

                            $lead->source_id =  $source;
                            $lead->updated_at = now();

                            if($this->to_agent_id && !($this->to_agent_id == 1 && $this->from_agent_id == 500)){
                                if($this->to_agent_id != 500){
                                    $agent = User::whereHas('roles')->where('bitrix_id', $this->to_agent_id)->where(function ($query) {
                                         $query->where('region_id', '<>', 2)
                                               ->orWhereNull('region_id');
                                     })->first();
                                }

                                $lead->agent_id = $agent->id ?? null;
                                if ($agent && !empty($agent?->hasRole(['Primary Agent', 'Primary Sales Manager', 'Primary Sales Director']))) {
                                            return true;
                                        }
                                if($this->from_agent_id){
                                    $prevAgent = User::where('bitrix_id', $this->from_agent_id)->first();
                                    $lead->previous_agent_id = $prevAgent->id ?? null;
                                }
                            }
                            $lead->save();

                            $leadCriteria = LeadCriteria::where('lead_id',$lead->id)->first() ?? new LeadCriteria();

                            $leadCriteria->lead_id      = $lead->id;

                            $srcDetails = "";
                            if($res->$apiSourceDetails){
                                $srcDetails = $res->UF_CRM_1553506792; //UF_CRM_1553506792
                            }

                            if($agent && $prevAgent){

                                $note = "A new lead has been transferred to you by {$prevAgent->full_name} via Bitrix.";

                                LeadNote::create([
                                        "lead_id" => $lead->id,
                                        "note" => $note,
                                        'status' => 1
                                    ]);
                                $unAssignedLead = false;
                                $message = "<b>AX Secondary CRM</b>\n\n";
                                $message .= "Hi {$agent->full_name},\n\n";
                                $message .= "A new lead has been transferred to you by {$prevAgent->full_name} via Bitrix.\n\n";
                                $message.= "<b>Lead details</b>\n";
                                $message.= "Full Name: {$lead->full_name}\n";
                                $message .= "Description: {$srcDetails}\n";
                                $message .= "==========================\n";
                                if($agent->telegram_chatid){
                                    if($agent->id == 65){ // Ben thomas id
                                        $type = '';
                                        if(Type::find($lead->type_id)){
                                            $type = Type::find($lead->type_id)->name;
                                        }
                                        $srcLocation = "";
                                        if($res->UF_CRM_1553171363){
                                            $srcLocation = $locations[$res->UF_CRM_1553171363] ?? null; //UF_CRM_1553171363
                                        }
                                        $srcDetails = "";

                                        if($res->$apiSourceDetails){
                                            $srcDetails = $res->$apiSourceDetails; //UF_CRM_1553506792
                                        }
                                        $source = '';
                                        if(Source::find($lead->source_id)){
                                            $source = Source::find($lead->source_id)->name;
                                        }
                                        $message = "<b>AX Secondary CRM</b>\nA Lead has been recorded under you in bitrix. Kindly assign it to an agent.\n";
                                        $message .= "\n<b> Property Details: </b> ";
                                        $message .= "\n<b> ID: </b> " . $lead->id??'';
                                        $message .= "\n<b> Type: </b> " . $type??'';
                                        $message .= "\n<b> Location: </b> " . $srcLocation??'';
                                        $message .= "\n<b> Source: </b> " . $source??'';
                                        $message .= "\n<b> Message: </b> " . $srcDetails??'';
                                        $message .= "\n\n<b> Lead Details: </b> ";
                                        ($lead->first_name!=" " ? $message .= "\n<b> Name: </b> " . $lead?->first_name.' '.$lead?->last_name : null);
                                        ($lead->mobile ? $message .= "\n<b> Mobile: </b> " . $lead?->mobile : null);
                                        ($lead->email ? $message .= "\n<b> Email: </b> " . $lead?->email : null);
                                        $url = route('api.thirdparty.leadAssignment', [Crypt::encryptString($lead->id), Crypt::encryptString($agent->id)]);
                                        $this->sendTelegramMessageHtml($message, [1417490671], $url, 'Assign Lead', 'lead_bot');
                                        telegramNotification('Lead',$lead->id,'1417490671',$agent->id,$message);

                                        if($lead->agent_id){
                                            $lead_hash = Crypt::encryptString($lead->id);
                                            $user_id_hash = Crypt::encryptString($lead?->agent_id);
                                            $url = route('api.thirdparty.lead_update')."?lead=".$lead_hash."&user=".$user_id_hash;
                                            $this->sendTelegramMessageHtml($message, [$agent->telegram_chatid], $url, 'Update Lead','lead_bot');
                                             telegramNotification('Lead',$lead->id,$agent->telegram_chatid,$lead->agent_id,$message);

                                        }else{
                                            $this->sendTelegramMessageHtml($message, [$agent->telegram_chatid], $url, 'Assign Lead', 'lead_bot');
                                            telegramNotification('Lead',$lead->id,$agent->telegram_chatid,$agent->id,$message);

                                        }


                                    }else{
                                        $this->sendTelegramMessageHtml($message, [$agent->telegram_chatid], null, null, 'lead_bot');

                                        telegramNotification('Lead',$lead->id,$agent->telegram_chatid,$agent->id,$message);

                                    }
                                }
                            }
                            $srcLocation = "";
                            if($res->UF_CRM_1553171363){
                                $srcLocation = $locations[$res->UF_CRM_1553171363] ?? null; //UF_CRM_1553171363
                            }
                            $srcLanguage = "";
                            if($res->UF_CRM_1553165368131){
                                $srcLanguage = $languages[$res->UF_CRM_1553165368131] ?? null; //UF_CRM_1553165368131
                            }
                            $srcNationality = "";
                            if($res->$apiNationality){
                                $srcNationality = $nationalities[$res->UF_CRM_1553166948] ?? null; //UF_CRM_1553166948
                            }
                            $projectDetails = '';
                            if($res->UF_CRM_1568907438){
                                $projectName = $this->fetchBitrixLeadProjectValue($res->UF_CRM_1568907438);
                                $projectDetails = $projectName;
                            }



                            $leadCriteria->bitrix_data = 'Imported from Bitrix24 webhook
                                    <br> ID: ' . $res->ID . '
                                    <br> Source Details: ' . $srcDetails . '
                                    <br> Source Name: ' . $sourceName . '
                                    <br> Ad_Project: ' . $projectDetails . '
                                    <br> Location: ' . $srcLocation . '
                                    <br> Nationlity: ' . $srcNationality . '
                                    <br> Language: ' . $srcLanguage . '
                                    <br> Imported at: ' . now();

                            $leadCriteria->save();


                            $updated++;
                        } else {
                             //if it's already imported from other system only create notes
                            $lead = Lead::where('email', $email)->where('mobile', $mobile)->where('source_id', $source)->where('need_to_be_destroyed', 0)->first();
                            $srcDetails = "";
                            if($res->$apiSourceDetails){
                                $srcDetails = $res->$apiSourceDetails; //UF_CRM_1553506792
                            }

                            $srcLocation = "";
                            if($res->UF_CRM_1553171363){
                                $srcLocation = $locations[$res->UF_CRM_1553171363] ?? null; //UF_CRM_1553171363
                            }
                            $srcLanguage = "";
                            if($res->UF_CRM_1553165368131){
                                $srcLanguage = $languages[$res->UF_CRM_1553165368131] ?? null; //UF_CRM_1553165368131
                            }
                            $srcNationality = "";
                            if($res->UF_CRM_1553166948){
                                $srcNationality = $nationalities[$res->UF_CRM_1553166948] ?? null; //UF_CRM_1553165368131
                            }

                            if (!empty($lead)) {

                                if ($lead->bitrix_id == null) {
                                    // DB::table('activity_log')->where('subject_type',Lead::class)->where('subject_id',$lead->id)->whereDate('created_at',">=","2023-05-04")->delete();
                                    $lead->bitrix_id = $res->ID;
                                    $lead->whatsapp = $mobile ?? null;
                                    $lead->for = $lead->for != null ? $lead->for : 'Unspecified';
                                    if($this->to_agent_id){
                                        if($this->to_agent_id != 500){
                                             $agent = User::whereHas('roles')->where('bitrix_id', $this->to_agent_id)->where(function ($query) {
                                                 $query->where('region_id', '<>', 2)
                                                       ->orWhereNull('region_id');
                                             })->first();
                                        }

                                        $lead->agent_id = $agent->id ?? null;
                                        if ($agent && $agent?->hasRole(['Primary Agent', 'Primary Sales Manager', 'Primary Sales Director'])) {
                                            return true;
                                        }
                                        if($this->from_agent_id){
                                            $prevAgent = User::where('bitrix_id', $this->from_agent_id)->first();
                                            $lead->previous_agent_id = $prevAgent->id ?? null;
                                        }
                                    }
                                    if($agent && $prevAgent){
                                        $note = "The lead has been reassigned to {$agent->full_name} from {$prevAgent->full_name} in bitrix.";

                                        LeadNote::create([
                                                "lead_id" => $lead->id,
                                                "note" => $note,
                                                'status' => 1
                                            ]);
                                        $unAssignedLead = false;
                                        $message = "<b>AX Secondary CRM</b>\n\n";
                                        $message .= "Hi {$agent->full_name},\n\n";
                                        $message .= "A new lead has been transferred to you by {$prevAgent->full_name} via Bitrix.\n\n";
                                        $message.= "<b>Lead details</b>\n";
                                        $message.= "Full Name: {$lead->full_name}\n";
                                         $message .= "Description: {$srcDetails}\n";
                                        $message .= "==========================\n";
                                        if($agent->telegram_chatid){
                                            if($agent->id == 65){ // Ben thomas id
                                                $type = '';
                                                if(Type::find($lead->type_id)){
                                                    $type = Type::find($lead->type_id)->name;
                                                }
                                                $srcLocation = "";
                                                if($res->UF_CRM_1553171363){
                                                    $srcLocation = $locations[$res->UF_CRM_1553171363] ?? null; //UF_CRM_1553171363
                                                }
                                                $srcDetails = "";

                                                if($res->$apiSourceDetails){
                                                    $srcDetails = $res->$apiSourceDetails; //UF_CRM_1553506792
                                                }
                                                $source = '';
                                                if(Source::find($lead->source_id)){
                                                    $source = Source::find($lead->source_id)->name;
                                                }
                                                $message = "<b>AX Secondary CRM</b>\nA Lead has been recorded under you in bitrix. Kindly assign it to an agent.\n";
                                                $message .= "\n<b> Property Details: </b> ";
                                                $message .= "\n<b> ID: </b> " . $lead->id??'';
                                                $message .= "\n<b> Type: </b> " . $type??'';
                                                $message .= "\n<b> Location: </b> " . $srcLocation??'';
                                                $message .= "\n<b> Source: </b> " . $source??'';
                                                $message .= "\n<b> Message: </b> " . $srcDetails??'';
                                                $message .= "\n\n<b> Lead Details: </b> ";
                                                ($lead->first_name!=" " ? $message .= "\n<b> Name: </b> " . $lead?->first_name.' '.$lead?->last_name : null);
                                                ($lead->mobile ? $message .= "\n<b> Mobile: </b> " . $lead?->mobile : null);
                                                ($lead->email ? $message .= "\n<b> Email: </b> " . $lead?->email : null);
                                                $url = route('api.thirdparty.leadAssignment', [Crypt::encryptString($lead->id), Crypt::encryptString($agent->id)]);
                                                $this->sendTelegramMessageHtml($message, [1417490671], $url, 'Assign Lead', 'lead_bot');
                                                telegramNotification('Lead',$lead->id,'1417490671',$agent->id,$message);

                                                $this->sendTelegramMessageHtml($message, [$agent->telegram_chatid], $url, 'Assign Lead', 'lead_bot');
                                                telegramNotification('Lead',$lead->id,$agent->telegram_chatid,$agent->id,$message);

                                            }else{
                                              $this->sendTelegramMessageHtml($message, [$agent->telegram_chatid], null, null, 'lead_bot');
                                              telegramNotification('Lead',$lead->id,$agent->telegram_chatid,$agent->id,$message);
                                            }

                                        }
                                    }
                                    $projectDetails = '';
                                    if($res->UF_CRM_1568907438){
                                        $projectName = $this->fetchBitrixLeadProjectValue($res->UF_CRM_1568907438);
                                        $projectDetails = $projectName;
                                    }

                                    $lead->save();
                                    $leadNote = new LeadNote();
                                    $leadNote->lead_id = $lead->id;
                                    $leadNote->note = 'Imported from Bitrix24 webhook
                                            <br> ID: ' . $res->ID . '
                                            <br> Comments: ' . $res->COMMENTS . '
                                            <br> Title: ' . $res->TITLE . '
                                            <br> Name: ' . $res->NAME . '
                                            <br> Second Name: ' . $res->SECOND_NAME . '
                                            <br> Last Name: ' . $res->LAST_NAME . '
                                            <br> Mobile: ' . $rawMobile . '
                                            <br> Email: ' . $email . '
                                            <br> SOURCE_ID: ' . $res->SOURCE_ID . '
                                            <br> Ad_Project: ' . $projectDetails . '
                                            <br>
                                            <br> Source Details: ' . $srcDetails . '
                                            <br> Source Name: ' . $sourceName . '
                                            <br> Location: ' . $srcLocation . '
                                            <br>
                                            <br> Currency: ' . $res->CURRENCY_ID . '
                                            <br> Lead created at: ' . $res->DATE_CREATE . '
                                            <br> Imported at: ' . now();
                                    $leadNote->save();


                                    $updated++;
                                } else {
                                    $lead->bitrix_id = $res->ID;
                                    $lead->lead_medium_id = $medium;
                                    $lead->source_id = $source;
                                    $lead->created_at = now();
                                    $lead->updated_at = now();
                                    if($this->to_agent_id){
                                        if($this->to_agent_id != 500){
                                            $agent = User::whereHas('roles')->where('bitrix_id', $this->to_agent_id)->where(function ($query) {
                                                     $query->where('region_id', '<>', 2)
                                                           ->orWhereNull('region_id');
                                                 })->first();
                                        }

                                        $lead->agent_id = $agent->id ?? null;
                                        if ($agent && $agent?->hasRole(['Primary Agent', 'Primary Sales Manager', 'Primary Sales Director'])) {
                                            return true;
                                        }
                                        if($this->from_agent_id){
                                            $prevAgent = User::where('bitrix_id', $this->from_agent_id)->first();
                                            $lead->previous_agent_id = $prevAgent->id ?? null;
                                        }

                                    }
                                    if($agent && $prevAgent){
                                        $note = "The lead has been reassigned to {$agent->full_name} from {$prevAgent->full_name} in bitrix.";

                                        LeadNote::create([
                                                "lead_id" => $lead->id,
                                                "note" => $note,
                                                'status' => 1
                                            ]);
                                        $unAssignedLead = false;
                                        $message = "<b>AX Secondary CRM</b>\n\n";
                                        $message .= "Hi {$agent->full_name},\n\n";
                                        $message .= "A new lead has been transferred to you by {$prevAgent->full_name} via Bitrix.\n\n";
                                        $message.= "<b>Lead details</b>\n";
                                        $message.= "Full Name: {$lead->full_name}\n";
                                         $message .= "Description: {$srcDetails}\n";
                                        $message .= "==========================\n";
                                        if($agent->telegram_chatid){
                                            if($agent->id == 65){ // Ben thomas id
                                                $type = '';
                                                if(Type::find($lead->type_id)){
                                                    $type = Type::find($lead->type_id)->name;
                                                }
                                                $srcLocation = "";
                                                if($res->UF_CRM_1553171363){
                                                    $srcLocation = $locations[$res->UF_CRM_1553171363] ?? null; //UF_CRM_1553171363
                                                }
                                                $srcDetails = "";

                                                if($res->$apiSourceDetails){
                                                    $srcDetails = $res->$apiSourceDetails; //UF_CRM_1553506792
                                                }
                                                $source = '';
                                                if(Source::find($lead->source_id)){
                                                    $source = Source::find($lead->source_id)->name;
                                                }
                                                $message = "<b>AX Secondary CRM</b>\nA Lead has been recorded under you in bitrix. Kindly assign it to an agent.\n";
                                                $message .= "\n<b> Property Details: </b> ";
                                                $message .= "\n<b> ID: </b> " . $lead->id??'';
                                                $message .= "\n<b> Type: </b> " . $type??'';
                                                $message .= "\n<b> Location: </b> " . $srcLocation??'';
                                                $message .= "\n<b> Source: </b> " . $source??'';
                                                $message .= "\n<b> Message: </b> " . $srcDetails??'';
                                                $message .= "\n\n<b> Lead Details: </b> ";
                                                ($lead->first_name!=" " ? $message .= "\n<b> Name: </b> " . $lead?->first_name.' '.$lead?->last_name : null);
                                                ($lead->mobile ? $message .= "\n<b> Mobile: </b> " . $lead?->mobile : null);
                                                ($lead->email ? $message .= "\n<b> Email: </b> " . $lead?->email : null);
                                                $url = route('api.thirdparty.leadAssignment', [Crypt::encryptString($lead->id), Crypt::encryptString($agent->id)]);
                                                $this->sendTelegramMessageHtml($message, [1417490671], $url, 'Assign Lead', 'lead_bot');
                                                telegramNotification('Lead',$lead->id,'1417490671',$agent->id,$message);

                                                $this->sendTelegramMessageHtml($message, [$agent->telegram_chatid], $url, 'Assign Lead', 'lead_bot');
                                                telegramNotification('Lead',$lead->id,$agent->telegram_chatid,$agent->id,$message);

                                            }else{
                                               $this->sendTelegramMessageHtml($message, [$agent->telegram_chatid], null, null, 'lead_bot');
                                               telegramNotification('Lead',$lead->id,$agent->telegram_chatid,$agent->id,$message);

                                            }

                                        }
                                    }


                                    $lead->save();
                                }
                                $leadCriteria = LeadCriteria::where('lead_id',$lead->id)->first() ?? new LeadCriteria();

                                $leadCriteria->lead_id      = $lead->id;

                                $leadCriteria->bitrix_data = 'Imported from Bitrix24 webhook
                                        <br> ID: ' . $res->ID . '
                                        <br> Source Details: ' . $srcDetails . '
                                        <br> Source Name: ' . $sourceName . '
                                        <br> Location: ' . $srcLocation . '
                                        <br> Nationlity: ' . $srcNationality . '
                                        <br> Language: ' . $srcLanguage . '
                                        <br> Imported at: ' . now();

                                $leadCriteria->save();

                            } else {

                                $lead = new Lead();
                                $lead->bitrix_id = $res->ID;
                                $lead->first_name = $res->NAME;
                                $lead->last_name = $res->LAST_NAME;
                                $lead->mobile = $mobile ?? null;
                                $lead->whatsapp = $mobile ?? null;
                                $lead->for = 'Unspecified';
                                $lead->email = $email ?? null;
                                $lead->lead_medium_id = $medium;
                                $lead->source_id = $source;
                                $lead->lead_status_id = $leadStatusesArray[$res->STATUS_ID] ?? 3; // 3 unspecified
                                // $lead->lead_sub_status_id = '8'; // 11 unspecified
                                if($this->to_agent_id){
                                    if($this->to_agent_id != 500){
                                        $agent = User::where('bitrix_id', $this->to_agent_id)->where(function ($query) {
                                             $query->where('region_id', '<>', 2)
                                                   ->orWhereNull('region_id');
                                         })->first();
                                    }


                                    $lead->agent_id = $agent->id ?? null;
                                    if($this->from_agent_id){
                                        $prevAgent = User::whereHas('roles')->where('bitrix_id', $this->from_agent_id)->first();
                                        $lead->previous_agent_id = $prevAgent->id ?? null;
                                    }
                                    if ($agent && $agent?->hasRole(['Primary Agent', 'Primary Sales Manager', 'Primary Sales Director'])) {
                                        return true;
                                    }
                                }
                                if($res->UF_CRM_1561214850){
                                    $lead->type_id = $lead_type_array[$res->UF_CRM_1561214850] ?? '11';
                                }else{
                                    $lead->type_id = '11'; // 11 unspecified
                                }
                                $lead->created_at = now();
                                $lead->updated_at = now();
                                if ($lead->save()) {
                                    //NOTIFICATION HERE
                                    $environment = \App::environment();
                                    $srcLocation = "";
                                    if($res->UF_CRM_1553171363){
                                        $srcLocation = $locations[$res->UF_CRM_1553171363] ?? null; //UF_CRM_1553171363
                                    }
                                    $srcDetails = "";

                                    if($res->$apiSourceDetails){
                                        $srcDetails = $res->$apiSourceDetails; //UF_CRM_1553506792
                                    }
                                    $projectDetails = '';
                                    if($res->UF_CRM_1568907438){
                                        $projectName = $this->fetchBitrixLeadProjectValue($res->UF_CRM_1568907438);
                                        $projectDetails = $projectName;
                                    }

                                    $leadNote = new LeadNote();
                                    $leadNote->lead_id = $lead->id;
                                    $leadNote->note = 'Imported from Bitrix24 webhook
                                            <br> ID: ' . $res->ID . '
                                            <br> Comments: ' . $res->COMMENTS . '
                                            <br> Title: ' . $res->TITLE . '
                                            <br> Name: ' . $res->NAME . '
                                            <br> Second Name: ' . $res->SECOND_NAME . '
                                            <br> Last Name: ' . $res->LAST_NAME . '
                                            <br> Mobile: ' . $rawMobile . '
                                            <br> Email: ' . $email . '
                                            <br> SOURCE_ID: ' . $res->SOURCE_ID . '
                                            <br> Source Name: ' . $sourceName . '
                                            <br> Ad_Project: ' . $projectDetails . '
                                            <br> Currency: ' . $res->CURRENCY_ID . '
                                            <br>
                                            <br> Source Details: ' . $srcDetails . '
                                            <br> Source Name: ' . $sourceName . '
                                            <br> Location: ' . $srcLocation . '
                                            <br>
                                            <br> Lead created at: ' . $res->DATE_CREATE . '
                                            <br> Lead updated at: ' . $res->DATE_MODIFY . '
                                            <br> Imported at ' . now();
                                    $leadNote->save();
                                    $unAssignedLead = true;

                                    if($agent && $prevAgent){
                                        $note = "The lead has been reassigned to {$agent->full_name} from {$prevAgent->full_name} in bitrix.";

                                        LeadNote::create([
                                                "lead_id" => $lead->id,
                                                "note" => $note,
                                                'status' => 1
                                            ]);
                                        $unAssignedLead = false;
                                        $message = "<b>AX Secondary CRM</b>\n\n";
                                        $message .= "Hi {$agent->full_name},\n\n";
                                        $message .= "A new lead has been transferred to you by {$prevAgent->full_name} via Bitrix.\n\n";
                                        $message.= "<b>Lead details</b>\n";
                                        $message.= "Full Name: {$lead->full_name}\n";
                                        $message .= "Description: {$srcDetails}\n";
                                        $message .= "==========================\n";
                                        if($agent->telegram_chatid){
                                            if($agent->id == 65){ // Ben thomas id
                                                $srcLocation = "";
                                                if($res->UF_CRM_1553171363){
                                                    $srcLocation = $locations[$res->UF_CRM_1553171363] ?? null; //UF_CRM_1553171363
                                                }
                                                $srcDetails = "";

                                                if($res->$apiSourceDetails){
                                                    $srcDetails = $res->$apiSourceDetails; //UF_CRM_1553506792
                                                }
                                                $type = '';
                                                if(Type::find($lead->type_id)){
                                                    $type = Type::find($lead->type_id)->name;
                                                }
                                                $source = '';
                                                if(Source::find($lead->source_id)){
                                                    $source = Source::find($lead->source_id)->name;
                                                }
                                                $message = "<b>AX Secondary CRM</b>\nA Lead has been recorded under you in bitrix. Kindly assign it to an agent.\n";
                                                $message .= "\n<b> Property Details: </b> ";
                                                $message .= "\n<b> ID: </b> " . $lead->id??'';
                                                $message .= "\n<b> Type: </b> " . $type??'';
                                                $message .= "\n<b> Location: </b> " . $srcLocation??'';
                                                $message .= "\n<b> Source: </b> " . $source??'';
                                                $message .= "\n<b> Message: </b> " . $srcDetails??'';
                                                $message .= "\n\n<b> Lead Details: </b> ";
                                                ($lead->first_name!=" " ? $message .= "\n<b> Name: </b> " . $lead?->first_name.' '.$lead?->last_name : null);
                                                ($lead->mobile ? $message .= "\n<b> Mobile: </b> " . $lead?->mobile : null);
                                                ($lead->email ? $message .= "\n<b> Email: </b> " . $lead?->email : null);
                                                $url = route('api.thirdparty.leadAssignment', [Crypt::encryptString($lead->id), Crypt::encryptString($agent->id)]);
                                                $this->sendTelegramMessageHtml($message, [1417490671], $url, 'Assign Lead', 'lead_bot');
                                                telegramNotification('Lead',$lead->id,'1417490671',$agent->id,$message);

                                                $this->sendTelegramMessageHtml($message, [$agent->telegram_chatid], $url, 'Assign Lead', 'lead_bot');
                                                telegramNotification('Lead',$lead->id,$agent->telegram_chatid,$agent->id,$message);

                                            }else{
                                               $this->sendTelegramMessageHtml($message, [$agent->telegram_chatid], null, null, 'lead_bot');
                                               telegramNotification('Lead',$lead->id,$agent->telegram_chatid,$agent->id,$message);

                                            }

                                        }
                                    }

                                    if ($environment == "production" && $unAssignedLead && (!in_array($lead->type_id, [3,4])))
                                    {
                                        $type = '';
                                        if(Type::find($lead->type_id)){
                                            $type = Type::find($lead->type_id)->name;
                                        }
                                        $source = '';
                                        if(Source::find($lead->source_id)){
                                            $source = Source::find($lead->source_id)->name;
                                        }
                                        $message = "<b>AX Secondary CRM</b>\nA Lead (Lead Pool) has been recorded in bitrix. Kindly assign it to an agent.\n";
                                        $message .= "\n<b> Property Details: </b> ";
                                        $message .= "\n<b> ID: </b> " . $lead->id??'';
                                        $message .= "\n<b> Type: </b> " . $type??'';
                                        $message .= "\n<b> Location: </b> " . $srcLocation??'';
                                        $message .= "\n<b> Source: </b> " . $source??'';
                                        $message .= "\n<b> Message: </b> " . $srcDetails??'';
                                        $message .= "\n\n<b> Lead Details: </b> ";
                                        ($lead->first_name!=" " ? $message .= "\n<b> Name: </b> " . $lead?->first_name.' '.$lead?->last_name : null);
                                        ($lead->mobile ? $message .= "\n<b> Mobile: </b> " . $lead?->mobile : null);
                                        ($lead->email ? $message .= "\n<b> Email: </b> " . $lead?->email : null);
//                                        $agents = User::whereHas("roles", function ($q) {
//                                            $q->whereIn("name", ["Line Manager", "Team Leader","Sales Director", "Head of Secondary","Admin - Abu Dhabi"]);
//                                            })
//                                            // ->where(function ($query) {
//                                            //     $query->where('region_id', '<>', 2)->orWhereNull('region_id');
//                                            // })
//                                            ->where("status", true)->get();
                                        $agents = User::where(function($q){
                                            $q->whereHas("roles", function ($q) {
                                                $q->whereIn("name", ["Line Manager", "Team Leader","Sales Director", "Head of Secondary","Admin - Abu Dhabi"]);
                                            })->orWhere('email', 'c.marvellous@axcapital.ae');
                                        })->where("status", true)->get();
                                        foreach ($agents as $agent) {
                                            if ($agent->telegram_chatid != null && $lead->agent_id==null) {
                                                $url = route('api.thirdparty.leadAssignment', [Crypt::encryptString($lead->id), Crypt::encryptString($agent->id)]);
                                                $this->sendTelegramMessageHtml($message, [$agent->telegram_chatid], $url, 'Assign Lead', 'lead_bot');
                                            }
                                            telegramNotification('Lead',$lead->id,$agent->telegram_chatid,$agent->id,$message);

                                        }
                                       // sending to Hishmat
                                       $agent = User::where('id',173)->first();
                                       if($agent){
                                           $url = route('api.thirdparty.leadAssignment', [Crypt::encryptString($lead->id), Crypt::encryptString($agent->id)]);
                                           $this->sendTelegramMessageHtml($message, [$agent->telegram_chatid], $url, 'Assign Lead', 'lead_bot');
                                       }

                                    }


                                }
                                $leadCriteria = LeadCriteria::where('lead_id',$lead->id)->first() ?? new LeadCriteria();

                                $leadCriteria->lead_id      = $lead->id;




                                $leadCriteria->bitrix_data = 'Imported from Bitrix24 webhook
                                        <br> ID: ' . $res->ID . '
                                        <br> Source Details: ' . $srcDetails . '
                                        <br> Source Name: ' . $sourceName . '
                                        <br> Location: ' . $srcLocation . '
                                        <br> Nationlity: ' . $srcNationality . '
                                        <br> Language: ' . $srcLanguage . '
                                        <br> Imported at: ' . now();

                                $leadCriteria->save();

                                $srcDetails = "";
                                if($res->UF_CRM_1553506792){
                                    $srcDetails = $res->UF_CRM_1553506792; //UF_CRM_1553506792
                                }

                                $srcLocation = "";
                                if($res->UF_CRM_1553171363){
                                    $srcLocation = $locations[$res->UF_CRM_1553171363] ?? null; //UF_CRM_1553171363
                                }
                                $srcLanguage = "";
                                if($res->UF_CRM_1553165368131){
                                    $srcLanguage = $languages[$res->UF_CRM_1553165368131] ?? null; //UF_CRM_1553165368131
                                }
                                $srcNationality = "";
                                if($res->UF_CRM_1553166948){
                                    $srcNationality = $nationalities[$res->UF_CRM_1553166948] ?? null; //UF_CRM_1553166948
                                }
                                $community = [];
                                $subCommunity = [];
                                if($srcLocation != "" && $srcLocation != null){
                                    $community = Community::where('name', 'like', "%$srcLocation%")->first();
                                    $locationsArray = [];

                                    if ($community) {

                                        $locationsArray[] = [
                                            'parentable_type' => "App\Models\Community",
                                            'parentable_id' => $community->id,
                                        ];
                                    }
                                    else{
                                        $subCommunity = SubCommunity::where('name', 'like', "%$srcLocation%")->first();
                                        if($subCommunity){
                                           $locationsArray[] = [
                                                'parentable_type' => "App\Models\SubCommunity",
                                                'parentable_id' => $subCommunity->id,
                                            ];
                                        }
                                    }
                                    if (!empty($locationsArray)) {
                                        foreach ($locationsArray as $location) {
                                            $leadCriteria->locations()->create($location);
                                        }
                                    }
                                }
                                if($lead->type_id == 3 || $lead->type_id == 4){
                                    $listing = new ListingLead();
                                    $listing->first_name = $lead->first_name;
                                    $listing->last_name = $lead->last_name;

                                    $listing->email = $lead->email;
                                    $listing->mobile = $lead->mobile;
                                    $listing->unit_no = $lead->unit_no;
                                    $listing->source_id = $lead->source_id ?? null;
                                    $listing->medium_id = $lead->medium_id;
                                    $listing->type_id = $lead->type_id;
                                    $listing->telesales_id = $lead->telesales_id;
                                    // $listing->title = $request->title;
                                    $listing->status_id = 1;

                                    $listing->for = $lead->for;


                                    $listing->note = $leadCriteria?->bitrix_data ?? null;


                                    $listing->sub_community_id = $subCommunity?->id ?? null;
                                    $listing->community_id = $community?->id ?? null;

                                    $listing->created_at = now();
                                    $listing->save();


                                    $leadNote = LeadNote::where('lead_id', $lead->id)->get();// this needs to be saved in notes
                                    foreach($leadNote as $note){
                                        $obj = new ListingLeadNote();
                                        $obj->listing_lead_id = $listing->id;
                                        $obj->note = 'Note (Migrated) => '. $note?->note;
                                        $obj->save();
                                    }

                                    if($agent){
                                        if($agent?->id == 65){
                                            $srcLocation = "";
                                            if($res->UF_CRM_1553171363){
                                                $srcLocation = $locations[$res->UF_CRM_1553171363] ?? null; //UF_CRM_1553171363
                                            }
                                            $srcDetails = "";

                                            if($res->$apiSourceDetails){
                                                $srcDetails = $res->$apiSourceDetails; //UF_CRM_1553506792
                                            }
                                            $type = '';
                                            if(Type::find($lead->type_id)){
                                                $type = Type::find($lead->type_id)->name;
                                            }
                                            $source = '';
                                            if(Source::find($lead->source_id)){
                                                $source = Source::find($lead->source_id)->name;
                                            }
                                            $message = "<b>AX Secondary CRM</b>\nA Listing Lead has been recorded under you in bitrix. Kindly assign it to an agent.\n";
                                            $message .= "\n<b> Property Details: </b> ";
                                            $message .= "\n<b> ID: </b> " . $listing->id??'';
                                            $message .= "\n<b> Type: </b> " . $type??'';
                                            $message .= "\n<b> Location: </b> " . $srcLocation??'';
                                            $message .= "\n<b> Source: </b> " . $source??'';
                                            $message .= "\n<b> Message: </b> " . $srcDetails??'';
                                            $message .= "\n\n<b> Lead Details: </b> ";
                                            ($listing->first_name!=" " ? $message .= "\n<b> Name: </b> " . $listing?->first_name.' '.$listing?->last_name : null);
                                            ($listing->mobile ? $message .= "\n<b> Mobile: </b> " . $listing?->mobile : null);
                                            ($listing->email ? $message .= "\n<b> Email: </b> " . $listing?->email : null);
                                            $url = route('api.thirdparty.listingLeadAssignment', [Crypt::encryptString($listing->id), Crypt::encryptString($agent->id)]);
                                            $this->sendTelegramMessageHtml($message, [1417490671], $url, 'Assign Listing Lead', 'lead_bot');
                                            telegramNotification('ListingLead',$lead->id,'1417490671',$agent->id,$message);

                                            $this->sendTelegramMessageHtml($message, [$agent->telegram_chatid], $url, 'Assign Listing Lead', 'lead_bot');
                                            telegramNotification('ListingLead',$lead->id,$agent->telegram_chatid,$agent->id,$message);

                                        }
                                    }
                                    else
                                    {
                                        $srcLocation = "";
                                        if($res->UF_CRM_1553171363){
                                            $srcLocation = $locations[$res->UF_CRM_1553171363] ?? null; //UF_CRM_1553171363
                                        }
                                        $srcDetails = "";

                                        if($res->$apiSourceDetails){
                                            $srcDetails = $res->$apiSourceDetails; //UF_CRM_1553506792
                                        }
                                        $type = '';
                                        if(Type::find($lead->type_id)){
                                            $type = Type::find($lead->type_id)->name;
                                        }
                                        $source = '';
                                        if(Source::find($lead->source_id)){
                                            $source = Source::find($lead->source_id)->name;
                                        }
                                        $message = "<b>AX Secondary CRM</b>\nA Listing Lead has been recorded in bitrix. Kindly assign it to an agent.\n";
                                        $message .= "\n<b> Property Details: </b> ";
                                        $message .= "\n<b> ID: </b> " . $listing->id??'';
                                        $message .= "\n<b> Type: </b> " . $type??'';
                                        $message .= "\n<b> Location: </b> " . $srcLocation??'';
                                        $message .= "\n<b> Source: </b> " . $source??'';
                                        $message .= "\n<b> Message: </b> " . $srcDetails??'';
                                        $message .= "\n\n<b> Lead Details: </b> ";
                                        ($listing->first_name!=" " ? $message .= "\n<b> Name: </b> " . $listing?->first_name.' '.$listing?->last_name : null);
                                        ($listing->mobile ? $message .= "\n<b> Mobile: </b> " . $listing?->mobile : null);
                                        ($listing->email ? $message .= "\n<b> Email: </b> " . $listing?->email : null);
//                                        $agents = User::whereHas("roles", function ($q) {
//                                            $q->whereIn("name", ["Line Manager", "Team Leader","Sales Director", "Head of Secondary","Admin - Abu Dhabi"]);
//                                            })
//                                            // ->where(function ($query) {
//                                            //     $query->where('region_id', '<>', 2)->orWhereNull('region_id');
//                                            // })
//                                            ->where("status", true)->get();
                                        $agents = User::where(function($q){
                                            $q->whereHas("roles", function ($q) {
                                                $q->whereIn("name", ["Line Manager", "Team Leader","Sales Director", "Head of Secondary","Admin - Abu Dhabi"]);
                                            })->orWhere('email', 'c.marvellous@axcapital.ae');
                                        })->where("status", true)->get();
                                        foreach ($agents as $agent) {
                                            if ($agent->telegram_chatid != null) {
                                                $url = route('api.thirdparty.listingLeadAssignment', [Crypt::encryptString($listing->id), Crypt::encryptString($agent->id)]);
                                                $this->sendTelegramMessageHtml($message, [$agent->telegram_chatid], $url, 'Assign Listing Lead', 'lead_bot');
                                            }
                                            telegramNotification('ListingLead',$lead->id,$agent->telegram_chatid,$agent->id,$message);

                                        }
                                        // sending to Hishmat
                                        $agent = User::where('id',173)->first();
                                        if($agent){
                                            $url = route('api.thirdparty.listingLeadAssignment', [Crypt::encryptString($listing->id), Crypt::encryptString($agent->id)]);
                                            $this->sendTelegramMessageHtml($message, [$agent->telegram_chatid], $url, 'Assign Listing Lead', 'lead_bot');
                                        }


                                    }
                                    $lead->converted_to_listing_lead = $listing->id;
                                    $lead->save();

                                }
                            }
                        }
                    }
                return true;
            } catch (\Exception $e) {
                info('Error: '.$e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
                return false;
            }
    }
}
