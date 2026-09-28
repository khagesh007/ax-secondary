<?php

namespace App\Console\Commands;

use App\Models\Lead;
use App\Models\User;
use App\Models\LeadCriteria;
use App\Models\LeadNote;
use App\Models\Medium;
use App\Models\Source;
use App\Models\TempDump;
use Carbon\Carbon;
use App\Events\PortalsLeadsNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Laravel\Telescope\Telescope;

class BitrixLeadImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'lead:bitrixImport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'this will import leads from bitrix crm';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Telescope::withoutRecording(function () {
            try {

                // Api User fields
                $apiSourceDetails   = "UF_CRM_1553506792";
                $apiLocation        = "UF_CRM_1553171363";
                $apiLeadType        = "UF_CRM_1561214850";
                $apiLanguage        = "UF_CRM_1553165368131";
                $apiNationality     = "UF_CRM_1553166948";


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
                    '4' => 5, //Not answered
                    'IN_PROCESS' => 5, //Contacted
                    "2" => 8, // Qualified
                    '3' => 9, // Successful
                    '6' => 5, // Contacted
                    '1' => 1, // New
                    'NEW' => 1,
                    'PROCESSED' => 8, // Qualified
                    'CONVERTED' => 8, // Successful
                );

                for ($j = 1; $j <= 5; $j++) {
                    $last = Lead::where('need_to_be_destroyed', 0)->max('bitrix_index') ?? 0;
                    echo "\n last is $last";
                    //dd($last);
                    $start_from = (int)floor($last / 50) * 50;
                    $start = intval($start_from);
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
                        CURLOPT_POSTFIELDS => array('ORDER[ID]' => 'ASC', 'FILTER[ASSIGNED_BY_ID]' => '500', 'SELECT[0]' => '*', 'SELECT[1]' => 'PHONE', 'SELECT[2]' => 'EMAIL', 'SELECT[3]' => "UF_*", 'start' => $start),
                        CURLOPT_HTTPHEADER => array(
                            'Cookie: BITRIX_SM_SALE_UID=0; PHPSESSID=KpxDF0YR3tW047SdI7gmD1XxVm7x5Ytw'
                        ),
                    ));
                    $response = curl_exec($curl);
                    curl_close($curl);
                    // $dump = TempDump::where('name', 'BITRIX_LEAD')->where('start_from', $start_from)->where('end_at', $response->next ?? $start_from)->first();
                    // if (!empty($dump)) {
                    //     $dump->data = $response;
                    //     $dump->name = 'BITRIX_LEAD';
                    //     $dump->start_from = $start_from;
                    //     $dump->end_at = $response->next ?? $start_from;
                    //     $dump->status = '1';
                    //     $dump->save();
                    // } else {
                    //     $dump = new TempDump();
                    //     $dump->data = $response;
                    //     $dump->name = 'BITRIX_LEAD';
                    //     $dump->start_from = $start_from;
                    //     $dump->end_at = $response->next ?? $start_from;
                    //     $dump->status = '1';
                    //     $dump->save();
                    // }
                    $response = json_decode($response);
                    $i = $start_from + 1;
                    $next = $response?->next ?? null;
                    $updated = 0;
                    foreach ($response?->result ?? [] as $res) {
                        if ($start_from > $next && $next) {
                            return;
                        }
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


                            $lead->disableLogging();
                            $lead->whatsapp = $mobile ?? null;
                            $lead->lead_medium_id = $lead->lead_medium_id != null ? $lead->lead_medium_id : $medium;
                            $lead->for = $lead->for != null ? $lead->for : 'Unspecified';
                            $lead->lead_status_id = $leadStatusesArray[$res->STATUS_ID] ?? 3; // 3 unspecified
                            // $lead->lead_sub_status_id = '8'; // 11 unspecified
                            if ($res->UF_CRM_1561214850) {
                                $lead->type_id = $lead_type_array[$res->UF_CRM_1561214850];
                            } else {
                                $lead->type_id = '11'; // 11 unspecified
                            }


                            $lead->bitrix_index = $i;
                            $lead->source_id =  $source;
                            $lead->created_at = $res->DATE_CREATE;
                            $lead->updated_at = $res->DATE_MODIFY;
                            $lead->save();

                            //echo("\nIndex checking is ". $i);
                            // echo("\nBitrix id is ". $res->ID);
                            $leadCriteria = LeadCriteria::where('lead_id', $lead->id)->first() ?? new LeadCriteria();

                            $leadCriteria->lead_id      = $lead->id;

                            $srcDetails = "";
                            if ($res->$apiSourceDetails) {
                                $srcDetails = $res->$apiSourceDetails; //UF_CRM_1553506792
                            }

                            $srcLocation = "";
                            if ($res->UF_CRM_1553171363) {
                                $srcLocation = $locations[$res->UF_CRM_1553171363] ?? null; //UF_CRM_1553171363
                            }
                            $srcLanguage = "";
                            if ($res->UF_CRM_1553165368131) {
                                $srcLanguage = $languages[$res->UF_CRM_1553165368131] ?? null; //UF_CRM_1553165368131
                            }
                            $srcNationality = "";
                            if ($res->$apiNationality) {
                                $srcNationality = $nationalities[$res->UF_CRM_1553166948] ?? null; //UF_CRM_1553166948
                            }



                            $leadCriteria->bitrix_data = 'Imported from Bitrix24
                                    <br> ID: ' . $res->ID . '
                                    <br> Source Details: ' . $srcDetails . '
                                    <br> Source Name: ' . $sourceName . '
                                    <br> Location: ' . $srcLocation . '
                                    <br> Nationlity: ' . $srcNationality . '
                                    <br> Language: ' . $srcLanguage . '
                                    <br> Imported at: ' . now();

                            $leadCriteria->save();
                            // LeadNote::where('lead_id', $lead->id)->delete();
                            // $leadNote = new LeadNote();
                            // $leadNote->lead_id = $lead->id;
                            // $leadNote->note = 'Imported from Bitrix24
                            //         <br> ID: ' . $res->ID . '
                            //         <br> Comments: ' . $res->COMMENTS . '
                            //         <br> Title: ' . $res->TITLE . '
                            //         <br> Name: ' . $res->NAME . '
                            //         <br> Second Name: ' . $res->SECOND_NAME . '
                            //         <br> Last Name: ' . $res->LAST_NAME . '
                            //         <br> Mobile: ' . $rawMobile . '
                            //         <br> Email: ' . $email . '
                            //         <br> SOURCE_ID: ' . $res->SOURCE_ID . '
                            //         <br> Currency: ' . $res->CURRENCY_ID . '
                            //         <br> Lead created at: ' . $res->DATE_CREATE . '
                            //         <br> Lead updated at: ' . $res->DATE_MODIFY . '
                            //         <br> Imported at: ' . now();
                            // $leadNote->save();

                            $updated++;
                            //  info('Duplicate found by bitrix id Import INDEX no. ' . $i . ' ID: ' . $res->ID);
                        } else { //if it's already imported from other system only create notes
                            $lead = Lead::where('email', $email)->where('mobile', $mobile)->where('source_id', $source)->where('need_to_be_destroyed', 0)->first();
                            if (!empty($lead)) {
                                if ($lead->bitrix_id == null) {
                                    // DB::table('activity_log')->where('subject_type',Lead::class)->where('subject_id',$lead->id)->whereDate('created_at',">=","2023-05-04")->delete();
                                    $lead->bitrix_id = $res->ID;
                                    $lead->bitrix_index = $i;
                                    $lead->whatsapp = $mobile ?? null;
                                    $lead->for = $lead->for != null ? $lead->for : 'Unspecified';
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
                                            <br> Source Name: ' . $sourceName . '
                                            <br> Currency: ' . $res->CURRENCY_ID . '
                                            <br> Lead created at: ' . $res->DATE_CREATE . '
                                            <br> Imported at: ' . now();
                                    $leadNote->save();


                                    $updated++;
                                } else {
                                    $lead->bitrix_id = $res->ID;
                                    $lead->bitrix_index = $i;
                                    $lead->lead_medium_id = $medium;
                                    $lead->source_id = $source;
                                    $lead->created_at = $res->DATE_CREATE;
                                    $lead->updated_at = $res->DATE_MODIFY;
                                    $lead->save();
                                }
                                $leadCriteria = LeadCriteria::where('lead_id', $lead->id)->first() ?? new LeadCriteria();

                                $leadCriteria->lead_id      = $lead->id;

                                $srcDetails = "";
                                if ($res->$apiSourceDetails) {
                                    $srcDetails = $res->$apiSourceDetails; //UF_CRM_1553506792
                                }

                                $srcLocation = "";
                                if ($res->UF_CRM_1553171363) {
                                    $srcLocation = $locations[$res->UF_CRM_1553171363] ?? null; //UF_CRM_1553171363
                                }
                                $srcLanguage = "";
                                if ($res->UF_CRM_1553165368131) {
                                    $srcLanguage = $languages[$res->UF_CRM_1553165368131] ?? null; //UF_CRM_1553165368131
                                }
                                $srcNationality = "";
                                if ($res->UF_CRM_1553166948) {
                                    $srcNationality = $nationalities[$res->UF_CRM_1553166948] ?? null; //UF_CRM_1553165368131
                                }



                                $leadCriteria->bitrix_data = 'Imported from Bitrix24
                                        <br> ID: ' . $res->ID . '
                                        <br> Source Details: ' . $srcDetails . '
                                        <br> Source Name: ' . $sourceName . '
                                        <br> Location: ' . $srcLocation . '
                                        <br> Nationlity: ' . $srcNationality . '
                                        <br> Language: ' . $srcLanguage . '
                                        <br> Imported at: ' . now();

                                $leadCriteria->save();
                                //   info('Duplicate found by mobile or email Import INDEX no. ' . $i . ' ID: ' . $res->ID);

                            } else {

                                $lead = new Lead();
                                $lead->bitrix_id = $res->ID;
                                $lead->bitrix_index = $i;
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

                                if ($res->UF_CRM_1561214850) {
                                    $lead->type_id = $lead_type_array[$res->UF_CRM_1561214850] ?? '11';
                                } else {
                                    $lead->type_id = '11'; // 11 unspecified
                                }
                                $lead->created_at = $res->DATE_CREATE;
                                $lead->updated_at = $res->DATE_MODIFY;
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
                                            <br> Source Name: ' . $sourceName . '
                                            <br> Currency: ' . $res->CURRENCY_ID . '
                                            <br> Lead created at: ' . $res->DATE_CREATE . '
                                            <br> Lead updated at: ' . $res->DATE_MODIFY . '
                                            <br> Imported at ' . now();
                                    $leadNote->save();

                                    // $user = User::where('id', $property->agent_id)->first();
                                    // event(new PortalsLeadsNotification("Lead", $lead, 'received', $user, $user, 1));
                                }
                                $leadCriteria = LeadCriteria::where('lead_id', $lead->id)->first() ?? new LeadCriteria();

                                $leadCriteria->lead_id      = $lead->id;

                                $srcDetails = "";
                                if ($res->$apiSourceDetails) {
                                    $srcDetails = $res->$apiSourceDetails; //UF_CRM_1553506792
                                }

                                $srcLocation = "";
                                if ($res->UF_CRM_1553171363) {
                                    $srcLocation = $locations[$res->UF_CRM_1553171363] ?? null; //UF_CRM_1553171363
                                }
                                $srcLanguage = "";
                                if ($res->UF_CRM_1553165368131) {
                                    $srcLanguage = $languages[$res->UF_CRM_1553165368131] ?? null; //UF_CRM_1553165368131
                                }
                                $srcNationality = "";
                                if ($res->UF_CRM_1553166948) {
                                    $srcNationality = $nationalities[$res->UF_CRM_1553166948] ?? null; //UF_CRM_1553166948
                                }



                                $leadCriteria->bitrix_data = 'Imported from Bitrix24
                                        <br> ID: ' . $res->ID . '
                                        <br> Source Details: ' . $srcDetails . '
                                        <br> Source Name: ' . $sourceName . '
                                        <br> Location: ' . $srcLocation . '
                                        <br> Nationlity: ' . $srcNationality . '
                                        <br> Language: ' . $srcLanguage . '
                                        <br> Imported at: ' . now();

                                $leadCriteria->save();
                                //   info('Fresh Import INDEX no. ' . $i.' ID: '.$res->ID);

                            }
                        }
                        $i++;
                    }
                }
                //info("Batch Data ended");
                echo ("\nBatch Data ended");
            } catch (\Exception $e) {
                info('Error Import INDEX no. ' . $i . ' Data: ' . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
                return Command::FAILURE;
            }
        });
        return Command::SUCCESS;
    }
}
