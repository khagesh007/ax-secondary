<?php

namespace App\Console;

use App\Models\Lead;
use App\Models\LeadNote;
use App\Models\Medium;
use App\Models\Source;
use App\Models\TempDump;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BitrixLeadImport_using_cache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'lead:bitrixImport {start_from?}';

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


        if($this->argument('start_from') !=''){
            $start_from = $this->argument('start_from');
            Cache::put('bitrix_lead_page', $start_from);
        }else {
            if (Cache::has('bitrix_lead_page')) {
                $start_from = Cache::get('bitrix_lead_page');
            } else {
                Cache::put('bitrix_lead_page', '0');
                $start_from = 0;
            }
        }




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
            CURLOPT_POSTFIELDS => array('ORDER[ID]' => 'ASC','FILTER[ASSIGNED_BY_ID]' => '500','SELECT[0]' => '*','SELECT[1]'=>'PHONE','SELECT[2]'=>'EMAIL','start' => $start_from),
            CURLOPT_HTTPHEADER => array(
                'Cookie: BITRIX_SM_SALE_UID=0; PHPSESSID=KpxDF0YR3tW047SdI7gmD1XxVm7x5Ytw'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $dump = TempDump::where('name','BITRIX_LEAD')->where('start_from',$start_from)->where('end_at',$response->next ?? $start_from)->first();
        if(!empty($dump)) {
            $dump->data = $response;
            $dump->name = 'BITRIX_LEAD';
            $dump->start_from = $start_from;
            $dump->end_at = $response->next ?? $start_from;
            $dump->status = '1';
            $dump->save();
        }else {
            $dump = new TempDump();
            $dump->data = $response;
            $dump->name = 'BITRIX_LEAD';
            $dump->start_from = $start_from;
            $dump->end_at = $response->next ?? $start_from;
            $dump->status = '1';
            $dump->save();
        }
        $response = json_decode($response);
        $i = 0; $imported = 0;
        foreach ($response?->result ?? [] as $res) {

            $mobile = '';
            $email = '';
            $rawMobile = '';
            if ($res->HAS_PHONE == 'Y' && $res->PHONE[0]->VALUE != '') {
                $mobile = $rawMobile = intval(preg_replace('/[^0-9]+/', '', $res->PHONE[0]->VALUE));
                $mobile = mobileNumberFixer($mobile);
            }
            if ($res->HAS_EMAIL == 'Y' && $res->EMAIL[0]->VALUE != '') {
                $email = $res->EMAIL[0]->VALUE;
                if (Str::length($email) < 5) {
                    $email = null;
                }
            }
            $source = is_numeric($res->SOURCE_ID) ? bitrixSource($res->SOURCE_ID) : (bitrixMediumGetId($res->SOURCE_ID) != null ? bitrixSource(bitrixMediumGetId($res->SOURCE_ID)) : $res->SOURCE_ID);
            $source = !is_numeric($source) ?  ( Source::where(DB::raw('BINARY `name`'), $source)->pluck('id')->first() ?? null) : null;
            $medium = is_numeric($res->SOURCE_ID) ? bitrixMediumById($res->SOURCE_ID) : (bitrixMediumGetId($res->SOURCE_ID) != null ? bitrixMediumById(bitrixMediumGetId($res->SOURCE_ID)) : $res->SOURCE_ID);
            $medium = !is_numeric($medium) ?  ( Medium::where(DB::raw('BINARY `name`'), $medium)->pluck('id')->first() ?? null) : null;
            if(Lead::where('bitrix_id', $res->ID)->exists()) {

                $lead = Lead::where('bitrix_id', $res->ID)->first();
                $lead->bitrix_id = $res->ID;
                $lead->first_name = $res->NAME;
                $lead->last_name = $res->LAST_NAME;
                $lead->mobile = $mobile ?? null;
                $lead->email = $email ?? null;
                $lead->lead_medium_id = $medium;
                $lead->source_id = $source;
                $lead->lead_status_id = '3'; // 3 unspecified
                $lead->created_at = $res->DATE_CREATE;
                $lead->updated_at = $res->DATE_MODIFY;
                if ($lead->save()) {
                    LeadNote::where('lead_id', $lead->id)->delete();
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
                    <br> Lead created at: ' . $res->DATE_CREATE.'
                    <br> Lead updated at: ' . $res->DATE_MODIFY.'
                    <br> Imported at: '.now();
                    $leadNote->save();


                }
            }else{ //if it's already imported from other system only create notes
            $lead = Lead::whereRaw('UPPER(first_name) LIKE ?', ['%' . strtoupper($res->NAME) . '%'])->where('email', $email)->where('mobile', $mobile)->first();
            if (!empty($lead)) {
                //$lead->bitrix_id = $res->ID;
               // $lead->save();
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
                    <br> Lead created at: ' . $res->DATE_CREATE.'
                    <br> Imported at: '.now();
                $leadNote->save();
                $imported++;
            } else {
                $lead = new Lead();
                $lead->bitrix_id = $res->ID;
                $lead->first_name = $res->NAME;
                $lead->last_name = $res->LAST_NAME;
                $lead->mobile = $mobile ?? null;
                $lead->email = $email ?? null;
                $lead->lead_medium_id = $medium;
                $lead->source_id = $source;
                $lead->lead_status_id = '3'; // 3 unspecified
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
                    <br> Currency: ' . $res->CURRENCY_ID . '
                    <br> Lead created at: ' . $res->DATE_CREATE.'
                    <br> Lead updated at: ' . $res->DATE_MODIFY.'
                    <br> Imported at '.now();
                    $leadNote->save();

                    $imported++;
                }
            }
        }

            $i++;
        }
        $next = $response->next ?? $start_from;
        Cache::put('bitrix_lead_page', $next);
        \Log::info('Bitrix Import Leads fetch started from '.$start_from.' total fetched '.$i.' and imported '.$imported.' leads completed at'.now());
        return Command::SUCCESS;
    }
}
