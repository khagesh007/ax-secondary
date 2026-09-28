<?php

namespace App\Console\Commands;

use App\Models\ListingLead;
use App\Models\LeadCriteria;
use App\Models\ListingLeadNote;
use App\Models\Medium;
use App\Models\Community;
use App\Models\SubCommunity;
use App\Models\CommunityProperty;
use App\Models\Source;
use App\Models\Type;
use App\Models\TempDump;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Laravel\Telescope\Telescope;

class BitrixListingLeadImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'listingLead:bitrixImport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'this will import Listing leads from bitrix crm';

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
                    6794 => 3, // Landlord
                    6796 => 3, // Landlord
                );
                
                for ($j = 1; $j <=50; $j++) {
                    //$last = Lead::max('bitrix_index') ?? 0;
                    $start_from = 0;
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
                    $response = json_decode($response);
                    $i = $start_from + 1;
                    $next = $response?->next ?? null;
                    $updated = 0;
                    foreach ($response?->result ?? [] as $res) {
                        // if($start_from > $next && $next){
                        //     return;
                        // }
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
                        $lead = ListingLead::where('bitrix_id', $res->ID)->first();


                        if (empty($lead) && array_key_exists($res->$apiLeadType, $lead_type_array)) {
                            $lead = new ListingLead();
                            $lead->bitrix_id = $res->ID;
                            
                            $lead->first_name = $res->NAME;
                            $lead->last_name = $res->LAST_NAME;
                            $lead->mobile = $mobile ?? null;
                            $lead->email = $email ?? null;
                            $lead->medium_id = $medium;
                            $lead->source_id = $source;
                            $lead->status_id = '1'; // 3 New
                            // $lead->lead_sub_status_id = '8'; // 11 unspecified
                            $lead->created_at = $res->DATE_CREATE;
                            $lead->updated_at = $res->DATE_MODIFY;
                            $srcDetails = "";
                            if($res->$apiSourceDetails){
                                $srcDetails = $res->$apiSourceDetails; //UF_CRM_1553506792
                                if(Str::contains('apartments', $srcDetails)){
                                   $type = Type::where('name', 'Apartment')->pluck('id')->first();
                                }elseif(Str::contains('villa', $srcDetails)){
                                    $type = Type::where('name', 'Villa')->pluck('id')->first();
                                }elseif(Str::contains('townhouse', $srcDetails)){
                                    $type = Type::where('name', 'townhouse')->pluck('id')->first();
                                }elseif(Str::contains('penthouse', $srcDetails)){
                                    $type = Type::where('name', 'penthouse')->pluck('id')->first();
                                }
                                elseif(Str::contains('compound', $srcDetails)){
                                    $type = Type::where('name', 'compound')->pluck('id')->first();
                                }
                                elseif(Str::contains('complex', $srcDetails)){
                                    $type = Type::where('name', 'complex')->pluck('id')->first();
                                }

                                if(Str::contains('rent', $srcDetails)){
                                    $for = "Residential for rent";
                                }
                                if(Str::contains('sale', $srcDetails) || Str::contains('sell', $srcDetails)){
                                    $for = "Residential for sale";
                                }
                            }
                           
                            $srcLocation = "";
                            if($res->$apiLocation){
                                $srcLocation = $locations[$res->$apiLocation] ?? null; //UF_CRM_1553171363
                            }
                            $srcLanguage = "";
                            if($res->$apiLanguage){
                                $srcLanguage = $languages[$res->$apiLanguage] ?? null; //UF_CRM_1553165368131
                            }
                            $srcNationality = "";
                            if($res->$apiNationality){
                                $srcNationality = $nationalities[$res->$apiNationality] ?? null; //UF_CRM_1553165368131
                            }
                            if($srcLocation){
                                $community = Community::where('name', "LIKE", "%$srcLocation%")->first();
                                $sub_community = SubCommunity::where('name', "LIKE", "%$srcLocation%")->first();
                                $communityProperty = CommunityProperty::where('name', "LIKE", "%$srcLocation%")->first();
                            }

                            $lead->community_id = $community?->id;
                            $lead->sub_community_id = $sub_community?->id;
                            $lead->community_property_id = $communityProperty?->id;

                            $lead->type_id = $type ?? null;
                            $lead->for = $for ?? 'Unspecified';

                            if ($lead->save()) {
                                $leadNote = new ListingLeadNote();
                                $leadNote->listing_lead_id = $lead->id;
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
                                        <br> Source Details: ' . $srcDetails . '
                                        <br> Location: ' . $srcLocation . '
                                        <br> Nationlity: ' . $srcNationality . '
                                        <br> Language: ' . $srcLanguage . '
                                        <br> Currency: ' . $res->CURRENCY_ID . '
                                        <br> Lead created at: ' . $res->DATE_CREATE . '
                                        <br> Lead updated at: ' . $res->DATE_MODIFY . '
                                        <br> Imported at ' . now();
                                $leadNote->save();
                            }
                            
                            //   info('Fresh Import INDEX no. ' . $i.' ID: '.$res->ID);
                        }
                    }
                        $i++;
                }
                
            } catch (\Exception $e) {
                info('Error Import INDEX no. ' . $i . ' Data: '.$e->getMessage());
                return Command::FAILURE;
            }
        });
        return Command::SUCCESS;
    }
}
