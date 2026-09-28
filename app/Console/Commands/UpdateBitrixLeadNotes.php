<?php

namespace App\Console\Commands;
use App\Models\Lead;
use App\Models\ListingLeadNote;
use App\Models\ListingLead;
use App\Models\LeadCriteria;
use App\Models\Source;
use App\Models\Medium;
use App\Models\Community;
use App\Models\SubCommunity;
use App\Models\LeadNote;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

use Illuminate\Console\Command;

class UpdateBitrixLeadNotes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:BitrixLeadNotes';

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
        $apiLocation        = "UF_CRM_1553171363";
        $apiLeadType        = "UF_CRM_1561214850";
        $apiLanguage        = "UF_CRM_1553165368131";
        $apiNationality     = "UF_CRM_1553166948";
        $apiSourceDetails   = "UF_CRM_1553506792";
        $leads = Lead::whereNull('agent_id')
                        ->whereNotNull('bitrix_id')
                        ->whereNull('converted_to_listing_lead')
                        ->where('status', true)
                        ->get();
        $locations = $this->getLocations();
        $nationalities = $this->getNationalities();

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

        foreach($leads as $lead){
            echo"Lead ID: $lead->id\n";
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
                CURLOPT_POSTFIELDS => array('ORDER[ID]' => 'ASC', 'FILTER[ID]' => $lead->bitrix_id, 'SELECT[0]' => '*', 'SELECT[1]' => 'PHONE', 'SELECT[2]' => 'EMAIL', 'SELECT[3]' => "UF_*"),
                CURLOPT_HTTPHEADER => array(
                    'Cookie: BITRIX_SM_SALE_UID=0; PHPSESSID=KpxDF0YR3tW047SdI7gmD1XxVm7x5Ytw'
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            $response = json_decode($response);
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
                if($res->$apiNationality){
                    $srcNationality = $nationalities[$res->UF_CRM_1553166948] ?? null; //UF_CRM_1553166948
                }

                // $leadNote = LeadNote::where("lead_id", $lead->id)->where('note',"LIKE", "%Imported from Bitrix24%")->first();
                // dd($leadNote);
                $note = 'Imported from Bitrix24
                        <br> ID: ' . $res->ID . '
                        <br> Comments: ' . $res->COMMENTS . '
                        <br> Title: ' . $res->TITLE . '
                        <br> Name: ' . $res->NAME . '
                        <br> Second Name: ' . $res->SECOND_NAME . '
                        <br> Last Name: ' . $res->LAST_NAME . '
                        <br> Mobile: ' . $rawMobile . '
                        <br> Email: ' . $email . '
                        <br> SOURCE_ID: ' . $res->SOURCE_ID . '
                        <br> Nationlity: ' . $srcNationality . '
                        <br> Currency: ' . $res->CURRENCY_ID . '
                        <br>
                        <br> Source Details: ' . $srcDetails . '
                        <br> Source Name: ' . $sourceName . '
                        <br> Location: ' . $srcLocation . '
                        <br>
                        <br> Lead created at: ' . $res->DATE_CREATE . '
                        <br> Lead updated at: ' . $res->DATE_MODIFY . '
                        <br> Imported at: ' . now();
                $leadNote = LeadNote::where('lead_id', $lead->id)
                                    ->where('note',"LIKE", "%Imported from Bitrix24%")
                                    ->update(["note" => $note]);
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
                        $leadCriteria = LeadCriteria::where('lead_id', $lead->id)->first();
                        if(!$leadCriteria){
                            $leadCriteria = new LeadCriteria();
                            $leadCriteria->lead_id = $lead->id;
                            $leadCriteria->save();
                        }
                        foreach ($locationsArray as $location) {
                            $leadCriteria->locations()->create($location);
                        }

                        if($lead->type_id == 3 || $lead->type_id == 4){
                            echo "Listing Lead\n";
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

                            if($lead){
                                $lead->converted_to_listing_lead = $listing->id;
                                $lead->status = 0;
                                $lead->save();
                            }
                        }
                    }
                }
            }
        }
        return Command::SUCCESS;
    }

    public function getLocations(){
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

        return $locations;
    }
    public function getNationalities(){
        // UF_CRM_1553166948 is key for nationality
        $response = Http::get('https://crm.axcap.ae/rest/1/8cd3l0kbdlndvtqf/crm.lead.userfield.list?filter[FIELD_NAME]=UF_CRM_1553166948');
        $res = $response->json();
        $nation = $res['result'][0]['LIST'];

        $nationalities = array();
        // Putting the languages in one dimension array so I can get the data by id based.
        foreach ($nation as $record) {
          $nationalities[$record['ID']] = $record['VALUE'];
        }

        return $nationalities;
    }
}
