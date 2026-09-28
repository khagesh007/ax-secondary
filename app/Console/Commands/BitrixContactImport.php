<?php

namespace App\Console\Commands;

use App\Models\Contact;
use App\Models\ContactNote;
use App\Models\ContactSource;
use App\Models\Source;
use App\Models\TempDump;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class BitrixContactImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contact:bitrixImport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will import contacts from bitrix crm';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
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
                    if(!empty($contact)){
                        //   $contact->disableLogging();
                        $contact->whatsapp = $contact->whatsapp != null ? $contact->whatsapp : ($mobile ?? null);
                        $contact->bitrix_id = $res->ID;
                        $contact->bitrix_index = $i;
                        $contact->first_name = $contact->first_name !=null ? $contact->first_name : substr($res->NAME,0,255);
                        $contact->last_name = substr($res->SECOND_NAME,0,255) . ' ' . substr($res->LAST_NAME,0,255);
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
                    }else {

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
                            if($contact->bitrix_id == null) {
                                $contact->whatsapp = $contact->whatsapp != null ? $contact->whatsapp : ($mobile ?? null);
                                $contact->bitrix_id = $res->ID;
                                $contact->bitrix_index = $i;
                                $contact->first_name = $contact->first_name !=null ? $contact->first_name : substr($res->NAME,0,255);
                                $contact->last_name = substr($res->SECOND_NAME,0,255) . ' ' . substr($res->LAST_NAME,0,255);
                                $contact->dob = $res->BIRTHDATE != '' ? date('Y-m-d', strtotime($res->BIRTHDATE)) : null;
                                $contact->mobile = $mobile ?? null;
                                $contact->email = $email ?? null;
                                $contact->contact_type_id = '3';
                                //$contact->contact_source_id = $contact->contact_source_id != null ? $contact->contact_source_id : $source;
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
                            }else{
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
                            $contact->first_name = substr($res->NAME,0,255);
                            $contact->last_name = substr($res->SECOND_NAME,0,255) . ' ' . substr($res->LAST_NAME,0,255);
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

            info('Bitrix24 Import Contacts Error: ' . $exception->getMessage());
            return Command::FAILURE;
        }
        return Command::SUCCESS;
    }
}
