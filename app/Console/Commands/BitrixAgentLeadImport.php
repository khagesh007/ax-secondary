<?php

namespace App\Console\Commands;

use App\Models\Community;
use App\Models\Lead;
use App\Models\LeadCriteria;
use App\Models\LeadNote;
use App\Models\Medium;
use App\Models\Source;
use App\Models\SubCommunity;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Laravel\Telescope\Telescope;
use App\Events\PortalsLeadsNotification;

class BitrixAgentLeadImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bitrix:agentLeadsImport {user_id : The User id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // $user_id = $this->argument('user_id');
        // dd($user_id);
        Telescope::withoutRecording(function () {
            try {
                $user_id = $this->argument('user_id');
                $users = User::whereNotNull('bitrix_id')->where('id', $user_id)->get();

                foreach ($users as $user) {
                    // Api User fields
                    $apiSourceDetails = "UF_CRM_1553506792";
                    $apiLocation = "UF_CRM_1553171363";
                    $apiLeadType = "UF_CRM_1561214850";
                    $apiLanguage = "UF_CRM_1553165368131";
                    $apiNationality = "UF_CRM_1553166948";

                    // Api User fields end here

                    $header = array();
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

                    // Total will be changed by the api
                    $total = 50;

                    $leadStatusesArray = array(
                        '4' => 5, //Not answered
                        'IN_PROCESS' => 5, //Contacted
                        "2" => 8, // Qualified
                        '3' => 9, // Successful
                        '6' => 5, // Contacted
                        '1' => 1, // New
                        'NEW' => 1,
                        'PROCESSED' => 8, // Qualified
                    );

                    for ($start = 0; $start < $total; $start += 50) {
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
                            CURLOPT_POSTFIELDS => array('ORDER[ID]' => 'ASC', 'FILTER[ASSIGNED_BY_ID]' => $user->bitrix_id, 'SELECT[0]' => '*', 'SELECT[1]' => 'PHONE', 'SELECT[2]' => 'EMAIL', 'SELECT[3]' => "UF_*", 'start' => $start),
                            CURLOPT_HTTPHEADER => array(
                                'Cookie: BITRIX_SM_SALE_UID=0; PHPSESSID=KpxDF0YR3tW047SdI7gmD1XxVm7x5Ytw',
                            ),
                        ));
                        $response = curl_exec($curl);
                        curl_close($curl);
                        $response = json_decode($response);
                        $i = 1;
                        $next = $response?->next ?? null;
                        $updated = 0;
                        $total = $response->total ?? 50;
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

                            $source = Source::whereRaw('UPPER(name) LIKE ?', ['%' . strtoupper($source) . '%'])->pluck('id')->first() ?? null;
                            $medium = findMediumForBitrix($res->SOURCE_ID);
                            $medium = Medium::whereRaw('UPPER(name) LIKE ?', ['%' . strtoupper($medium) . '%'])->pluck('id')->first() ?? null;
                            $lead = Lead::where('bitrix_id', $res->ID)->first();

                            if ((!empty($lead)) && (array_key_exists($res->STATUS_ID, $leadStatusesArray))) {

                                $lead->disableLogging();

                                $lead->lead_medium_id = $lead->lead_medium_id != null ? $lead->lead_medium_id : $medium;

                                $lead->source_id = $lead->source_id != null ? $lead->source_id : $source;

                                $lead->lead_status_id = $leadStatusesArray[$res->STATUS_ID];

                                $date_create = Carbon::createFromFormat('Y-m-d\TH:i:sP', $res->DATE_CREATE)->toDateTimeString();
                                $date_update = Carbon::createFromFormat('Y-m-d\TH:i:sP', $res->DATE_MODIFY)->toDateTimeString();
                                $lead->created_at = $date_create;
                                $lead->agent_id = $user?->id;
                                $lead->updated_at = $date_update;
                                $lead->save();
                                $leadCriteria = LeadCriteria::where('lead_id', $lead->id)->first();

                                $srcDetails = "";
                                if ($res->$apiSourceDetails) {
                                    $srcDetails = $res->$apiSourceDetails; //UF_CRM_1553506792
                                }

                                $srcLocation = "";
                                if ($res->$apiLocation) {
                                    $srcLocation = $locations[$res->$apiLocation] ?? null; //UF_CRM_1553171363
                                }
                                $srcLanguage = "";
                                if ($res->$apiLanguage) {
                                    $srcLanguage = $languages[$res->$apiLanguage] ?? null; //UF_CRM_1553165368131
                                }
                                $srcNationality = "";
                                if ($res->$apiNationality) {
                                    $srcNationality = $nationalities[$res->$apiNationality] ?? null; //UF_CRM_1553165368131
                                }

                                if (!$leadCriteria) {
                                    $leadCriteria = new LeadCriteria();
                                    $leadCriteria->lead_id = $lead->id;
                                    $leadCriteria->bitrix_data = 'Imported from Bitrix24
                                            <br> ID: ' . $res->ID . '
                                            <br> Source Details: ' . $srcDetails . '
                                            <br> Location: ' . $srcLocation . '
                                            <br> Nationlity: ' . $srcNationality . '
                                            <br> Language: ' . $srcLanguage . '
                                            <br> Imported at: ' . now();

                                    $leadCriteria->save();
                                }

                                $community = [];
                                $subCommunity = [];
                                if ($srcLocation != "" && $srcLocation != null) {
                                    $community = Community::where('name', 'like', "%$srcLocation%")->first();
                                    $locationsArray = [];

                                    if ($community) {
                                        $locationsArray[] = [
                                            'parentable_type' => "App\Models\Community",
                                            'parentable_id' => $community->id,
                                        ];
                                    } else {
                                        $subCommunity = SubCommunity::where('name', 'like', "%$srcLocation%")->first();
                                        if ($subCommunity) {
                                            $locationsArray[] = [
                                                'parentable_type' => "App\Models\SubCommunity",
                                                'parentable_id' => $subCommunity->id,
                                            ];
                                        }
                                    }
                                    if (!empty($locationsArray)) {
                                        $leadCriteria = LeadCriteria::where('lead_id', $lead->id)->first();
                                        if (!$leadCriteria) {
                                            $leadCriteria = new LeadCriteria();
                                            $leadCriteria->lead_id = $lead->id;
                                            $leadCriteria->save();
                                        }
                                        foreach ($locationsArray as $location) {
                                            $leadCriteria->locations()->create($location);
                                        }
                                    }
                                }

                                $updated++;
                            } elseif (array_key_exists($res->STATUS_ID, $leadStatusesArray)) {
                                echo "#";
                                $lead = Lead::where('email', $email)->where('mobile', $mobile)->where('source_id', $source)->where('agent_id', $user->id)->first();
                                if (!empty($lead)) {
                                    if ($lead->bitrix_id == null) {
                                        // DB::table('activity_log')->where('subject_type',Lead::class)->where('subject_id',$lead->id)->whereDate('created_at',">=","2023-05-04")->delete();
                                        $lead->bitrix_id = $res->ID;
                                        $date_create = Carbon::createFromFormat('Y-m-d\TH:i:sP', $res->DATE_CREATE)->toDateTimeString();
                                        $date_update = Carbon::createFromFormat('Y-m-d\TH:i:sP', $res->DATE_MODIFY)->toDateTimeString();
                                        $lead->created_at = $date_create;
                                        $lead->updated_at = $date_update;

                                        $lead->lead_status_id = $leadStatusesArray[$res->STATUS_ID];
                                        $lead->save();
                                        $leadNote = new LeadNote();
                                        $leadNote->lead_id = $lead->id;
                                        $leadNote->note = 'Imported from Bitrix24
                                                    <br> ID: ' . $res->ID . '
                                                    <br> Comments: ' . $res->COMMENTS . '
                                                    <br> Title: ' . $res->TITLE . '
                                                    <br> Name: ' . $res->NAME . '
                                                    <br> Second Name: ' . $res->SECOND_NAME . '
                                                    <br> Last Name: ' . $res->LAST_NAME . '
                                                    <br> Mobile: ' . $rawMobile . '
                                                    <br> Email: ' . $email . '
                                                    <br> SOURCE_ID: ' . $res->SOURCE_ID . '
                                                    <br> Currency: ' . $res->CURRENCY_ID . '
                                                    <br> Lead created at: ' . $res->DATE_CREATE . '
                                                    <br> Imported at: ' . now();
                                        $leadNote->save();

                                        $updated++;
                                    } else {
                                        $lead->bitrix_id = $res->ID;

                                        $lead->lead_medium_id = $medium;
                                        $lead->source_id = $source;
                                        $lead->agent_id = $user->id;
                                        $date_create = Carbon::createFromFormat('Y-m-d\TH:i:sP', $res->DATE_CREATE)->toDateTimeString();
                                        $date_update = Carbon::createFromFormat('Y-m-d\TH:i:sP', $res->DATE_MODIFY)->toDateTimeString();

                                        $lead->created_at = $date_create;
                                        $lead->updated_at = $date_update;
                                        $lead->save();
                                    }
                                    $leadCriteria = LeadCriteria::where('lead_id', $lead->id)->first();

                                    $srcDetails = "";
                                    if ($res->$apiSourceDetails) {
                                        $srcDetails = $res->$apiSourceDetails; //UF_CRM_1553506792
                                    }

                                    $srcLocation = "";
                                    if ($res->$apiLocation) {
                                        $srcLocation = $locations[$res->$apiLocation] ?? null; //UF_CRM_1553171363
                                    }
                                    $srcLanguage = "";
                                    if ($res->$apiLanguage) {
                                        $srcLanguage = $languages[$res->$apiLanguage] ?? null; //UF_CRM_1553165368131
                                    }
                                    $srcNationality = "";
                                    if ($res->$apiNationality) {
                                        $srcNationality = $nationalities[$res->$apiNationality] ?? null; //UF_CRM_1553165368131
                                    }

                                    if (!$leadCriteria) {
                                        $leadCriteria = new LeadCriteria();
                                        $leadCriteria->lead_id = $lead->id;

                                        $leadCriteria->bitrix_data = 'Imported from Bitrix24
                                                    <br> ID: ' . $res->ID . '
                                                    <br> Source Details: ' . $srcDetails . '
                                                    <br> Location: ' . $srcLocation . '
                                                    <br> Nationlity: ' . $srcNationality . '
                                                    <br> Language: ' . $srcLanguage . '
                                                    <br> Imported at: ' . now();

                                        $leadCriteria->save();
                                    }
                                    if ($srcLocation != "" && $srcLocation != null) {
                                        $community = Community::where('name', 'like', "%$srcLocation%")->first();
                                        $locationsArray = [];

                                        if ($community) {
                                            $locationsArray[] = [
                                                'parentable_type' => "App\Models\Community",
                                                'parentable_id' => $community->id,
                                            ];
                                        } else {
                                            $subCommunity = SubCommunity::where('name', 'like', "%$srcLocation%")->first();
                                            if ($subCommunity) {
                                                $locationsArray[] = [
                                                    'parentable_type' => "App\Models\SubCommunity",
                                                    'parentable_id' => $subCommunity->id,
                                                ];
                                            }
                                        }
                                        if (!empty($locationsArray)) {
                                            $leadCriteria = LeadCriteria::where('lead_id', $lead->id)->first();
                                            if (!$leadCriteria) {
                                                $leadCriteria = new LeadCriteria();
                                                $leadCriteria->lead_id = $lead->id;
                                                $leadCriteria->save();
                                            }
                                            foreach ($locationsArray as $location) {
                                                $leadCriteria->locations()->create($location);
                                            }
                                        }
                                    }
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
                                    $lead->lead_status_id = $leadStatusesArray[$res->STATUS_ID];
                                    $lead->agent_id = $user->id;
                                    $lead->type_id = '11'; // 11 unspecified

                                    $date_create = Carbon::createFromFormat('Y-m-d\TH:i:sP', $res->DATE_CREATE)->toDateTimeString();
                                    $date_update = Carbon::createFromFormat('Y-m-d\TH:i:sP', $res->DATE_MODIFY)->toDateTimeString();

                                    $lead->created_at = $date_create;
                                    $lead->updated_at = $date_update;
                                    if ($lead->save()) {

                                        $leadNote = new LeadNote();
                                        $leadNote->lead_id = $lead->id;
                                        $leadNote->note = 'Imported from Bitrix24
                                                    <br> ID: ' . $res->ID . '
                                                    <br> Comments: ' . $res->COMMENTS . '
                                                    <br> Title: ' . $res->TITLE . '
                                                    <br> Name: ' . $res->NAME . '
                                                    <br> Second Name: ' . $res->SECOND_NAME . '
                                                    <br> Last Name: ' . $res->LAST_NAME . '
                                                    <br> Mobile: ' . $rawMobile . '
                                                    <br> Email: ' . $email . '
                                                    <br> SOURCE_ID: ' . $res->SOURCE_ID . '
                                                    <br> Currency: ' . $res->CURRENCY_ID . '
                                                    <br> Lead created at: ' . $res->DATE_CREATE . '
                                                    <br> Lead updated at: ' . $res->DATE_MODIFY . '
                                                    <br> Imported at ' . now();
                                        $leadNote->save();

                                        $user = User::where('id', $user->id)->first();
                                        event(new PortalsLeadsNotification("Lead", $lead, 'received', $user, $user, 1));
                                    }
                                    $leadCriteria = LeadCriteria::where('lead_id', $lead->id)->first() ?? new LeadCriteria();

                                    $leadCriteria->lead_id = $lead->id;

                                    $srcDetails = "";
                                    if ($res->$apiSourceDetails) {
                                        $srcDetails = $res->$apiSourceDetails; //UF_CRM_1553506792
                                    }

                                    $srcLocation = "";
                                    if ($res->$apiLocation) {
                                        $srcLocation = $locations[$res->$apiLocation] ?? null; //UF_CRM_1553171363
                                    }
                                    $srcLanguage = "";
                                    if ($res->$apiLanguage) {
                                        $srcLanguage = $languages[$res->$apiLanguage] ?? null; //UF_CRM_1553165368131
                                    }
                                    $srcNationality = "";
                                    if ($res->$apiNationality) {
                                        $srcNationality = $nationalities[$res->$apiNationality] ?? null; //UF_CRM_1553165368131
                                    }

                                    $leadCriteria->bitrix_data = 'Imported from Bitrix24
                                                <br> ID: ' . $res->ID . '
                                                <br> Source Details: ' . $srcDetails . '
                                                <br> Location: ' . $srcLocation . '
                                                <br> Nationlity: ' . $srcNationality . '
                                                <br> Language: ' . $srcLanguage . '
                                                <br> Imported at: ' . now();

                                    $leadCriteria->save();

                                    if ($srcLocation != "" && $srcLocation != null) {
                                        $community = Community::where('name', 'like', "%$srcLocation%")->first();
                                        $locationsArray = [];

                                        if ($community) {
                                            $locationsArray[] = [
                                                'parentable_type' => "App\Models\Community",
                                                'parentable_id' => $community->id,
                                            ];
                                        } else {
                                            $subCommunity = SubCommunity::where('name', 'like', "%$srcLocation%")->first();
                                            if ($subCommunity) {
                                                $locationsArray[] = [
                                                    'parentable_type' => "App\Models\SubCommunity",
                                                    'parentable_id' => $subCommunity->id,
                                                ];
                                            }
                                        }
                                        if (!empty($locationsArray)) {
                                            $leadCriteria = LeadCriteria::where('lead_id', $lead->id)->first();
                                            if (!$leadCriteria) {
                                                $leadCriteria = new LeadCriteria();
                                                $leadCriteria->lead_id = $lead->id;
                                                $leadCriteria->save();
                                            }
                                            foreach ($locationsArray as $location) {
                                                $leadCriteria->locations()->create($location);
                                            }
                                        }
                                    }
                                    //   info('Fresh Import INDEX no. ' . $i.' ID: '.$res->ID);
                                }
                            }
                            $i++;
                        }
                    }
                }
                echo ("Batch Data ended");
            } catch (\Exception $e) {
                dd($e->getMessage(), $e->getLine());
                return Command::FAILURE;
            }
        });
        return Command::SUCCESS;
    }
}
