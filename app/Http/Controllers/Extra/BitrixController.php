<?php

namespace App\Http\Controllers\Extra;

use App\Models\Contact;
use App\Models\ContactNote;
use App\Models\Country;
use App\Models\Location;
use App\Models\Medium;
use App\Models\Route;
use App\Models\Source;
use App\Models\TempDump;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;

use App\Models\Lead;

use App\Models\LeadNote;
use Illuminate\Support\Str;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Traits\FileTrait;
use Intervention\Image\Facades\Image;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class BitrixController extends Controller
{

    public function importSource()
    {
        $input = 6;

        exit();
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://crm.axcap.ae/rest/1/8cd3l0kbdlndvtqf/crm.status.list?filter[ENTITY_ID]=SOURCE',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('ORDER[ID]' => 'DESC', 'FILTER[ENTITY_ID]=SOURCE', 'SELECT[0]' => '*'),
            CURLOPT_HTTPHEADER => array(
                'Cookie: BITRIX_SM_SALE_UID=0; PHPSESSID=KpxDF0YR3tW047SdI7gmD1XxVm7x5Ytw'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);

        //        if(TempDump::where('name','BITRIX_LEAD_SOURCE')->doesntExist()) {
        //            $obj = new TempDump();
        //            $obj->name = 'BITRIX_LEAD_SOURCE';
        //            $obj->data = $response;
        //            $obj->save();
        //        }
        $response = json_decode($response);
        foreach ($response?->result ?? [] as $res) {
            echo '"' . $res->STATUS_ID . '" => "' . $res->ID . '",<br>';
        }
    }

    public function importLeadFromDump(Request $request)
    {
        $strat_from = $request->start_from;

        //    $source = TempDump::where('name','BITRIX_LEAD_SOURCE')->first();
        //
        //    $source = json_decode($source->data);
        //    $source = $source->result;
        //
        //    $source = collect($source)->pluck('NAME','STATUS_ID');
        //    echo '<pre>';
        //        print_r($source);
        //    echo '</pre>';
        //    $source = $source->toArray();
        //    $source = array_flip($source);

    }

    public function importLead(Request $request)
    {
        try {
            for ($j = 0; $j < 5; $j++) {
                $last = Lead::max('bitrix_index') ?? 0;
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
                    CURLOPT_POSTFIELDS => array('ORDER[ID]' => 'ASC', 'FILTER[ASSIGNED_BY_ID]' => '500', 'SELECT[0]' => '*', 'SELECT[1]' => 'PHONE', 'SELECT[2]' => 'EMAIL', 'start' => $start),
                    CURLOPT_HTTPHEADER => array(
                        'Cookie: BITRIX_SM_SALE_UID=0; PHPSESSID=KpxDF0YR3tW047SdI7gmD1XxVm7x5Ytw'
                    ),
                ));
                $response = curl_exec($curl);
                curl_close($curl);
                $dump = TempDump::where('name', 'BITRIX_LEAD')->where('start_from', $start_from)->where('end_at', $response->next ?? $start_from)->first();
                if (!empty($dump)) {
                    $dump->data = $response;
                    $dump->name = 'BITRIX_LEAD';
                    $dump->start_from = $start_from;
                    $dump->end_at = $response->next ?? $start_from;
                    $dump->status = '1';
                    $dump->save();
                } else {
                    $dump = new TempDump();
                    $dump->data = $response;
                    $dump->name = 'BITRIX_LEAD';
                    $dump->start_from = $start_from;
                    $dump->end_at = $response->next ?? $start_from;
                    $dump->status = '1';
                    $dump->save();
                }
                $response = json_decode($response);
                $i = $start_from + 1;

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
                    $source = Source::whereRaw('UPPER(name) LIKE ?', ['%' . strtoupper($source) . '%'])->pluck('id')->first() ?? null;
                    $medium = findMediumForBitrix($res->SOURCE_ID);
                    $medium = Medium::whereRaw('UPPER(name) LIKE ?', ['%' . strtoupper($medium) . '%'])->pluck('id')->first() ?? null;
                    $lead = Lead::where('bitrix_id', $res->ID)->first();
                    if (!empty($lead)) {
                        $lead->disableLogging();
                        $lead->whatsapp = $mobile ?? null;
                        $lead->lead_medium_id = $lead->lead_medium_id != null ? $lead->lead_medium_id : $medium;
                        $lead->for = $lead->for != null ? $lead->for : 'Unspecified';
                        $lead->lead_status_id = '1'; // 3 unspecified
                        // $lead->lead_sub_status_id = '8'; // 11 unspecified
                        $lead->type_id = '11'; // 11 unspecified
                        $lead->bitrix_index = $i;
                        $lead->source_id = $lead->source_id != null ? $lead->source_id : $source;
                        $lead->save();

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
                                <br> Lead created at: ' . $res->DATE_CREATE . '
                                <br> Lead updated at: ' . $res->DATE_MODIFY . '
                                <br> Imported at: ' . now();
                        $leadNote->save();
                        //   DB::table('activity_log')->where('subject_type',Lead::class)->where('event','updated')->where('subject_id',$lead->id)->whereDate('created_at',Carbon::today())->delete();

                        $updated++;
                        //  info('Duplicate found by bitrix id Import INDEX no. ' . $i . ' ID: ' . $res->ID);


                    } else { //if it's already imported from other system only create notes
                        $lead = Lead::where('email', $email)->where('mobile', $mobile)->where('lead_source_id', $source)->first();
                        if (!empty($lead)) {
                            if ($lead->bitrix_id == null) {
                                $lead->disableLogging();
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
                                        <br> Currency: ' . $res->CURRENCY_ID . '
                                        <br> Lead created at: ' . $res->DATE_CREATE . '
                                        <br> Imported at: ' . now();
                                $leadNote->save();

                                // DB::table('activity_log')->where('subject_type',Lead::class)->where('event','updated')->where('subject_id',$lead->id)->whereDate('created_at',Carbon::today())->delete();
                                $updated++;
                            } else {
                                $lead->bitrix_id = $res->ID;
                                $lead->bitrix_index = $i;
                                $lead->lead_medium_id = $medium;
                                $lead->source_id = $source;
                                $lead->save();
                            }
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
                            $lead->lead_status_id = '1'; // 3 unspecified
                            // $lead->lead_sub_status_id = '8'; // 11 unspecified
                            $lead->type_id = '11'; // 11 unspecified
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
            }
        } catch (\Exception $e) {
            info('Error Import INDEX no. ' . $i . ' ID: ' . $res->ID . ' ' . $e->getMessage());
        }
    }


    public function deleteExisting()
    {

        //where bitrix_id is not null
        echo Lead::whereNotNull('bitrix_id')->count();
        $leads = Lead::whereNotNull('bitrix_id')->limit(200)->pluck('id')->toArray();

        echo 'Selected leads ' . (!empty($leads) ? count($leads) : 0) . '<br>';
        $leadNote = LeadNote::where('lead_id', '23864')->count();
        echo 'Selected lead notes ' . (!empty($leadNote) ? $leadNote : 0) . '<br>';
        if (count($leads) > 0) {
            LeadNote::whereIn('lead_id', $leads)->delete();
        }
        DB::table('leads')->whereNotNull('bitrix_id')->orderBy('id')->chunk(100, function (Collection $leads) {
            foreach ($leads as $lead) {

                LeadNote::where('lead_id', $lead->id)->delete();
                Lead::where('id', $lead->id)->delete();
            }
        });

        echo 'Left bitrix lead ' . Lead::whereNotNull('bitrix_id')->count();
    }

    public function createMapOfSource()
    {

        $path = public_path() . ('/json/bitrix_source.json');
        if (!File::exists($path)) {
            throw new \Exception("Invalid File");
        } else {
            $json = json_decode(File::get($path), true);
            foreach ($json['result'] as $item) {
                //  echo '"' . $item["STATUS_ID"] . '" => "' . $item["medium"] . '",<br>';
                if (Source::where('name', $item['secondary'])->doesntExist()) {
                    $obj = new Source();
                    $obj->name = $item['secondary'];
                    $obj->is_contact_source = 1;
                    $obj->is_lead_source = 1;
                    if ($obj->save()) {
                        echo '-----------------Source created ' . $item['secondary'] . '<br>';
                    }
                }
            }
        }
    }
    public function createMapNationality()
    {

        $path = public_path() . ('/json/countries.json');
        if (!File::exists($path)) {
            throw new \Exception("Invalid File");
        } else {
            $json = json_decode(File::get($path), true);
            foreach ($json as $item) {

                if (Country::where('code', $item["alpha_2_code"])->exists()) {
                    $obj = Country::where('code', $item["alpha_2_code"])->first();
                    $obj->nationality = getFirstValue($item["nationality"]);
                    if ($obj->save()) {
                        echo '-----------------Country updated ' .  $obj->name . '=>' . $obj->nationality . '-- from -' . $item['nationality'] . '<br>';
                    }
                }
            }
        }
    }

    public function importContact()
    {
        try {

            for ($j = 0; $j < 5; $j++) {
                $last = Contact::max('bitrix_index') ?? 0;
                $start_from = (int)floor($last / 50) * 50;
                $start = intval($start_from);
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://crm.axcap.ae/rest/1/8cd3l0kbdlndvtqf/crm.contact.list',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => array('ORDER[ID]' => 'ASC', 'SELECT[0]' => '*', 'SELECT[1]' => 'PHONE', 'SELECT[2]' => 'EMAIL', 'start' => $start),
                    CURLOPT_HTTPHEADER => array(
                        'Cookie: BITRIX_SM_SALE_UID=0; PHPSESSID=KpxDF0YR3tW047SdI7gmD1XxVm7x5Ytw'
                    ),
                ));
                $response = curl_exec($curl);
                curl_close($curl);
                $dump = TempDump::where('name', 'BITRIX_CONTACT')->where('start_from', $start_from)->where('end_at', $response->next ?? $start_from)->first();
                if (!empty($dump)) {
                    $dump->data = $response;
                    $dump->name = 'BITRIX_CONTACT';
                    $dump->start_from = $start_from;
                    $dump->end_at = $response->next ?? $start_from;
                    $dump->status = '1';
                    $dump->save();
                } else {
                    $dump = new TempDump();
                    $dump->data = $response;
                    $dump->name = 'BITRIX_CONTACT';
                    $dump->start_from = $start_from;
                    $dump->end_at = $response->next ?? $start_from;
                    $dump->status = '1';
                    $dump->save();
                }
                $response = json_decode($response);
                $i = $start + 1;
                $imported = 0;
                foreach ($response?->result ?? [] as $res) {
                    $mobile = '';
                    $email = '';
                    $rawMobile = '';
                    if ($res->HAS_PHONE == 'Y' && $res->PHONE[0]->VALUE != '') {
                        $mobile = $rawMobile = $res->PHONE[0]->VALUE;
                        $mobile = mobileNumberFixer($mobile);
                    }
                    if ($res->HAS_EMAIL == 'Y' && $res->EMAIL[0]->VALUE != '') {
                        $email = $res->EMAIL[0]->VALUE;
                    }
                    $source = $source_name = findSourceForBitrix($res->SOURCE_ID);
                    $source = Source::whereRaw('UPPER(name) LIKE ?', ['%' . strtoupper($source) . '%'])->pluck('id')->first() ?? null;
                    $contact = Contact::where('bitrix_id', $res->ID)->first();
                    if (!empty($contact)) {
                        //   $contact->disableLogging();
                        $contact->whatsapp = $contact->whatsapp != null ? $contact->whatsapp : ($mobile ?? null);
                        $contact->bitrix_id = $res->ID;
                        $contact->bitrix_index = $i;
                        $contact->first_name = $contact->first_name != null ? $contact->first_name : substr($res->NAME, 0, 255);
                        $contact->last_name = substr($res->SECOND_NAME, 0, 255) . ' ' . substr($res->LAST_NAME, 0, 255);
                        $contact->dob = $res->BIRTHDATE != '' ? date('Y-m-d', strtotime($res->BIRTHDATE)) : null;
                        $contact->mobile = $mobile ?? null;
                        $contact->email = $email ?? null;
                        $contact->contact_type_id = '3';
                        $contact->contact_source_id =  $source;
                        $contact->created_at = $res->DATE_CREATE != '' ? date('Y-m-d H:i:s', strtotime($res->DATE_CREATE)) : null;
                        $contact->updated_at = $res->DATE_MODIFY != '' ? date('Y-m-d H:i:s', strtotime($res->DATE_MODIFY)) : null;
                        if ($contact->save()) {
                            $contact->contactTypes()->sync([3]);
                            $imported++;
                            ContactNote::where('contact_id', $contact->id)->delete();
                            $contactNote = new ContactNote();
                            $contactNote->contact_id = $contact->id;
                            $contactNote->note = 'Imported from Bitrix24
                                    <br> ID: ' . $res->ID . '
                                    <br> Comments: ' . $res->COMMENTS . '
                                    <br> Name: ' . $res->NAME . '
                                    <br> Second Name: ' . $res->SECOND_NAME . '
                                    <br> Last Name: ' . $res->LAST_NAME . '
                                    <br> Birthdate: ' . $res->BIRTHDATE . '
                                    <br> Mobile: ' . $rawMobile . '
                                    <br> Email: ' . $email . '
                                    <br> Source: ' . $res->SOURCE_ID . '
                                    <br> Created At: ' . $res->DATE_CREATE . '
                                    <br> Updated At: ' . $res->DATE_MODIFY;
                            $contactNote->save();
                        }
                    } else {

                        $contact = Contact::where(function ($query) use ($email) {
                            if ($email != null) {
                                $query->where('email', $email);
                            } else {
                                return false;
                            }
                        })->where(function ($query) use ($mobile) {
                            if ($mobile != null) {
                                $query->where('mobile', $mobile);
                            } else {
                                return false;
                            }
                        })->first();
                        if (!empty($contact)) {
                            if ($contact->bitrix_id == null) {
                                $contact->whatsapp = $contact->whatsapp != null ? $contact->whatsapp : ($mobile ?? null);
                                $contact->bitrix_id = $res->ID;
                                $contact->bitrix_index = $i;
                                $contact->first_name = $contact->first_name != null ? $contact->first_name : substr($res->NAME, 0, 255);
                                $contact->last_name = substr($res->SECOND_NAME, 0, 255) . ' ' . substr($res->LAST_NAME, 0, 255);
                                $contact->dob = $res->BIRTHDATE != '' ? date('Y-m-d', strtotime($res->BIRTHDATE)) : null;
                                $contact->mobile = $mobile ?? null;
                                $contact->email = $email ?? null;
                                $contact->contact_type_id = '3';
                                //$contact->contact_source_id = $contact->contact_source_id != null ? $contact->contact_source_id : $source;
                                $contact->created_at = $res->DATE_CREATE != '' ? date('Y-m-d H:i:s', strtotime($res->DATE_CREATE)) : null;
                                $contact->updated_at = $res->DATE_MODIFY != '' ? date('Y-m-d H:i:s', strtotime($res->DATE_MODIFY)) : null;
                                if ($contact->save()) {
                                    $contact->contactTypes()->sync([3]);
                                    $imported++;

                                    $contactNote = new ContactNote();
                                    $contactNote->contact_id = $contact->id;
                                    $contactNote->note = 'Imported from Bitrix24
                                    <br> ID: ' . $res->ID . '
                                    <br> Comments: ' . $res->COMMENTS . '
                                    <br> Name: ' . $res->NAME . '
                                    <br> Second Name: ' . $res->SECOND_NAME . '
                                    <br> Last Name: ' . $res->LAST_NAME . '
                                    <br> Birthdate: ' . $res->BIRTHDATE . '
                                    <br> Mobile: ' . $rawMobile . '
                                    <br> Email: ' . $email . '
                                    <br> Source: ' . $res->SOURCE_ID . '
                                    <br> Created At: ' . $res->DATE_CREATE . '
                                    <br> Updated At: ' . $res->DATE_MODIFY;
                                    $contactNote->save();
                                }
                            } else {
                                $contact->whatsapp = $contact->whatsapp != null ? $contact->whatsapp : ($mobile ?? null);
                                $contact->bitrix_id = $res->ID;
                                $contact->bitrix_index = $i;
                                $contact->dob = $res->BIRTHDATE != '' ? date('Y-m-d', strtotime($res->BIRTHDATE)) : null;
                                $contact->contact_type_id = '3';
                                $contact->contact_source_id = $contact->contact_source_id != null ? $contact->contact_source_id : $source;
                                $contact->created_at = $res->DATE_CREATE != '' ? date('Y-m-d H:i:s', strtotime($res->DATE_CREATE)) : null;
                                $contact->updated_at = $res->DATE_MODIFY != '' ? date('Y-m-d H:i:s', strtotime($res->DATE_MODIFY)) : null;
                                $contact->save();
                                ContactNote::where('contact_id', $contact->id)->delete();
                                $contactNote = new ContactNote();
                                $contactNote->contact_id = $contact->id;
                                $contactNote->note = 'Imported from Bitrix24
                                    <br> ID: ' . $res->ID . '
                                    <br> Comments: ' . $res->COMMENTS . '
                                    <br> Name: ' . $res->NAME . '
                                    <br> Second Name: ' . $res->SECOND_NAME . '
                                    <br> Last Name: ' . $res->LAST_NAME . '
                                    <br> Birthdate: ' . $res->BIRTHDATE . '
                                    <br> Mobile: ' . $rawMobile . '
                                    <br> Email: ' . $email . '
                                    <br> Source: ' . $res->SOURCE_ID . '
                                    <br> Created At: ' . $res->DATE_CREATE . '
                                    <br> Updated At: ' . $res->DATE_MODIFY;
                                $contactNote->save();
                            }
                        } else {
                            $contact = Contact::whereRaw('UPPER(first_name) LIKE ?', ['%' . strtoupper($res->NAME) . '%'])
                                ->whereRaw('UPPER(last_name) LIKE ?', ['%' . strtoupper($res->LAST_NAME) . '%'])
                                ->where('mobile', $mobile)
                                ->where('email', $email)
                                ->first();
                            if (empty($contact)) {
                                $contact = new Contact();
                            }
                            $contact->bitrix_id = $res->ID;
                            $contact->bitrix_index = $i;
                            $contact->first_name = substr($res->NAME, 0, 255);
                            $contact->last_name = substr($res->SECOND_NAME, 0, 255) . ' ' . substr($res->LAST_NAME, 0, 255);
                            $contact->dob = $res->BIRTHDATE != '' ? date('Y-m-d', strtotime($res->BIRTHDATE)) : null;
                            $contact->mobile = $mobile ?? null;
                            $contact->whatsapp = $contact->whatsapp != null ? $contact->whatsapp : ($mobile ?? null);
                            $contact->email = $email ?? null;
                            $contact->contact_source_id = $contact->contact_source_id != null ? $contact->contact_source_id : $source;
                            $contact->contact_type_id = '3';
                            $contact->contact_status_id = '3';
                            $contact->created_at = $res->DATE_CREATE != '' ? date('Y-m-d H:i:s', strtotime($res->DATE_CREATE)) : null;
                            $contact->updated_at = $res->DATE_MODIFY != '' ? date('Y-m-d H:i:s', strtotime($res->DATE_MODIFY)) : null;
                            if ($contact->save()) {
                                $contact->contactTypes()->sync([3]);
                                $contactNote = new ContactNote();
                                $contactNote->contact_id = $contact->id;
                                $contactNote->note = 'Imported from Bitrix24
                                <br> ID: ' . $res->ID . '
                                <br> Comments: ' . $res->COMMENTS . '
                                <br> Name: ' . $res->NAME . '
                                <br> Second Name: ' . $res->SECOND_NAME . '
                                <br> Last Name: ' . $res->LAST_NAME . '
                                <br> Birthdate: ' . $res->BIRTHDATE . '
                                <br> Mobile: ' . $rawMobile . '
                                <br> Email: ' . $email . '
                                <br> Source: ' . $res->SOURCE_ID . '
                                <br> Created At: ' . $res->DATE_CREATE . '
                                <br> Updated At: ' . $res->DATE_MODIFY;
                                $contactNote->save();
                            }
                        }
                    }
                    $i++;
                }
            }
        } catch (\Exception $exception) {
            print_r($exception->getMessage());
            exit();
            \Illuminate\Support\Facades\Log::info("Bitrix Controller " . $exception->getMessage());
        }
    }

    public function deleteOldLeadNote()
    {
        $leads = Lead::whereNotNull('bitrix_id')->where('created_at', Carbon::now()->subDays(30))->get();
        foreach ($leads as $lead) {
            LeadNote::where('lead_id', $lead->id)->delete();
        }
    }
}
