<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CallLog;
use App\Models\Lead;
use App\Models\LeadNote;
use App\Models\Medium;
use App\Models\Note;
use App\Models\Property;
use App\Models\Source;
use App\Models\User;
use Carbon\Carbon;
use Ddeboer\Imap\Server;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Response;
use App\Models\Community;
use App\Models\SubCommunity;
use App\Models\CommunityProperty;
use App\Models\LeadCriteriaLocation;
use App\Models\LeadCriteria;
use App\Traits\TelegramTrait;
class EmailParserApiController extends Controller
{
    use TelegramTrait;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
        set_time_limit(8000000);

    }

    private function sendDebugTelegramMessage($message){
        $message .= "\n\n";

        $message .= "file: " . self::class. "\n";
        $this->sendTelegramMessage($message, [1253659350, 795165330]);
    }

    private function propertyFinderLeadsEmail($message)
    {

        $mail_number = $message->getNumber();

        $subject = $message->getSubject();
        $from = $message->getFrom()->getAddress();
        $original_body = $message->getBodyText();
        $html_body = new \Html2Text\Html2Text ($message->getBodyHtml());
        $text_body = $html_body->getText();

        $reference_pattern = '/REFERENCE: ([^\s]+)/';
        $mobile_pattern = '/tel:\+(\d+)/';

        preg_match($reference_pattern, $text_body, $ref_matches);
        $ref_number = trim($ref_matches[1] ?? '');

        preg_match($mobile_pattern, $text_body, $mobile_matches);

        $mobile = trim($mobile_matches[1] ?? '');

        $mobile = validatePhoneNumber($mobile);
        if(checkBlocklistAPI($mobile,Lead::class,$message)){
            $result = array('status' => '0', 'msg' => 'This number is in Blacklist. Please do not contact with this number.', 'error' => 'This number is in Blacklist. Please do not contact with this number.');
            return json_encode($result);
        }


        if (!$mobile_matches) {
            $mobile_pattern = "/tel:(\d+)/";
            preg_match($mobile_pattern, $text_body, $mobile_matches);
            $mobile = trim($mobile_matches[1] ?? '');
        }
        $updated_text = str_replace("\n", " ", $text_body);
        $name = strtolower($this->getBetween($updated_text, "respond to", "calling or emailing")) ?? 'NA';

        $email = $this->getBetween($text_body, "Email:", "Phone:") ?? '';
        $message = $this->getBetween($text_body, "AGENT:", "You can respond") ?? '';
        if (!$message) {
            $message = $this->getBetween($original_body, "you a message:", "You can respond");
        }

        $email_raw = substr($text_body, strpos($text_body, 'emailing on'));
        $all_emails = $this->fetch_mails($email_raw);
        $email = strtolower($all_emails[0] ?? '');

        $arry = [
            'crm' => 'Property Finder',
            'mail_number' => $mail_number,
            'subject' => $subject,
            'from' => $from,
            'original_body' => $original_body,
            'html_body' => $html_body,
            'text_body' => $text_body,
            'ref_number' => $ref_number,
            'mobile' => $mobile,
            'name' => $name,
            'email' => $email,
            'message' => $message,
            'mobile_matches' => $mobile_matches[0],
        ];

        $this->decideLead($arry);

    }
    private function extractRefFromJamesEd($original_body){
        $patterns = [
            '/Internal Reference[:\s]+([a-zA-Z0-9-]+)/i',
            '/internal\s+reference[:\s]*([a-zA-Z0-9-]+)/i',
            '/reference[:\s]*([a-zA-Z0-9-]+)/i',
            '/\b(axc-\d+)\b/i', // Specific pattern for axc-* format
            '/ref[:\s]*([a-zA-Z0-9-]+)/i', // More flexible ref pattern
            '/([a-zA-Z]{2,4}-\d+)/i' // Generic code-number pattern
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $original_body, $ref_matches)) {
                return trim($ref_matches[1]);
            }
        }

        return '';
    }
    private function extractMobileNumberJEd($original_body){
        $mobile_patterns = [
            '/\+971\d{9}/',                    // +971589567739
            '/\+(\d{10,15})/',                 // Any international format +1234567890
            '/(?:\+|00)(\d{10,15})/',          // +971589567739 or 00971589567739
            '/\b(\d{10,12})\b/',               // 10-12 digit numbers 0589567739
            '/tel:\+?(\d+)/',                  // tel:+971589567739 or tel:971589567739
            '/mobile[:\s]*\+?(\d+)/i',         // Mobile: +971589567739
            '/phone[:\s]*\+?(\d+)/i',          // Phone: +971589567739
        ];

        $mobile = '';
        foreach ($mobile_patterns as $pattern) {
            if (preg_match($pattern, $original_body, $mobile_matches)) {
                $mobile = trim($mobile_matches[1] ?? $mobile_matches[0]);
                break;
            }
        }

         return validatePhoneNumber($mobile);
    }

    private function jamesEditionParse($message)
    {
        if(Lead::where('mail_number',$message->getNumber())->exists()){
            return;
        }

        $mail_number = $message->getNumber();

        $subject = $message->getSubject();
        $from = $message->getFrom()->getAddress();
        $original_body = $message->getBodyText();
        $html_body = new \Html2Text\Html2Text ($message->getBodyHtml());
        $dateTime = $message->getDate();
      $email_time = $dateTime->format('Y-m-d H:i:s');
        $text_body = $html_body->getText();

        $ref_number = $this->extractRefFromJamesEd($text_body);
        $ref_number = $ref_number !='' ? $ref_number : $this->extractRefFromJamesEd($original_body);

        $updated_text = str_replace("\n", " ", $text_body);
        $name = strtolower($this->getBetween($original_body, "Lead name ", "\r\n")) ?? 'NA';

       // $email = $this->getBetween($original_body, "[mailto:", "]") ?? '';
        //$mobile = $this->getBetween($original_body, "[tel:", "]") ?? '';

        $mobile = $this->extractMobileNumberJEd($original_body);


        if(checkBlocklistAPI($mobile,Lead::class,$message)){
            $result = array('status' => '0', 'msg' => 'This number is in Blacklist. Please do not contact with this number.', 'error' => 'This number is in Blacklist. Please do not contact with this number.');
            return json_encode($result);
        }

        $message = $this->getBetween($original_body, "*Message*", "\r\n") ?? '';
        if (!$message) {
            $message = "Message Not found";
        }

        $email_raw = substr($text_body, strpos($text_body, 'emailing on'));
        $all_emails = $this->fetch_mails($email_raw);

        $email = strtolower($all_emails[0] ?? '');
        $system_email = ['PORTALS@AXCAPITAL.AE','portals@axcapital.ae'];
        if(in_array(strtolower($email),$system_email)){
            $email = '';
        }
        $arry = [
            'crm' => 'JamesEdition',
            'mail_number' => $mail_number,
            'subject' => $subject,
            'from' => $from,
            'original_body' => $original_body,
            'html_body' => $html_body,
            'text_body' => $text_body,
            'ref_number' => $ref_number,
            'mobile' => $mobile,
            'name' => $name,
            'email' => $email,
            'message' => $message,
            'mobile_matches' => $mobile,
            'email_time' =>$email_time
        ];

       echo 'name '.$name.' email '.$email.' mobile '.$mobile.' ref '.$ref_number."\n";

       $this->decideLead($arry);
    }

    private function emiratesEstateParse($message)
    {
        $mail_number = $message->getNumber();

        $subject = $message->getSubject();
        $from = $message->getFrom()->getAddress();
        $original_body = $message->getBodyText();
        $html_body = new \Html2Text\Html2Text ($message->getBodyHtml());
        $text_body = $html_body->getText();

        $updated_text = str_replace("\n", " ", $text_body);

        $ref_number = strtolower($this->getBetween($original_body, "Ref. ID: ", "\r\n")) ?? 'NA';
        //$name = getWordAfterName($original_body);

        $pattern = '/Name:\s+(.*?)\n/s'; // Regular expression pattern to match the name

        if (preg_match($pattern, $text_body, $matches)) {
            $name = trim($matches[1]);
        } else {
            $name = "";
        }
        $email = $this->getBetween($original_body, "E-mail: ", "\r\n") ?? '';
        $mobile = $this->getBetween($original_body, "Phone: ", "\r\n") ?? '';

        $mobile = validatePhoneNumber($mobile);
        if(checkBlocklistAPI($mobile,Lead::class,$message)){
            $result = array('status' => '0', 'msg' => 'This number is in Blacklist. Please do not contact with this number.', 'error' => 'This number is in Blacklist. Please do not contact with this number.');
            return json_encode($result);
        }


        $message = $this->getBetween($original_body, "Message: ", "Source:") ?? '';
        if (!$message) {
            $message = "Message Not found";
        }

        $email_raw = substr($text_body, strpos($text_body, 'emailing on'));
        $all_emails = $this->fetch_mails($email_raw);
        $email = strtolower($all_emails[0] ?? '');
        $arry = [
            'crm' => 'Emirates.Estate',
            'mail_number' => $mail_number,
            'subject' => $subject,
            'from' => $from,
            'original_body' => $original_body,
            'html_body' => $html_body,
            'text_body' => $text_body,
            'ref_number' => $ref_number,
            'mobile' => $mobile,
            'name' => $name,
            'email' => $email,
            'message' => $message,
            'mobile_matches' => $mobile,
        ];

        $this->decideLead($arry);
    }

    private function propertyFinderLeadsCall($message)
    {

        $mail_number = $message->getNumber();

        $subject = $message->getSubject();
        $from = $message->getFrom()->getAddress();
        $to_email = $message->getTo()[0]->getAddress();

        $original_body = $message->getBodyText();
        $html_body = new \Html2Text\Html2Text ($message->getBodyHtml());
        $text_body = $html_body->getText();


        $mobile = $this->getBetween($text_body, "[tel:", "]") ?? '';

        $mobile = validatePhoneNumber($mobile);
        if(checkBlocklistAPI($mobile,Lead::class,$message)){
            $result = array('status' => '0', 'msg' => 'This number is in Blacklist. Please do not contact with this number.', 'error' => 'This number is in Blacklist. Please do not contact with this number.');
            return json_encode($result);
        }

        $agent_name = $this->getBetween($text_body, "Dear", ",") ?? '';

        // Get Recent Lead matching the number
        $trim_mobile = substr(trim($mobile), -8);

        $agent_id = User::where('secondary_email', $to_email)
            ->orWhere('email', $to_email)
            ->pluck('id')
            ->first();

        $call_start = $this->getBetween($text_body, "Call start :", "Call end") ?? '';
        $call_end = $this->getBetween($text_body, "Call end :", "Talk time :") ?? '';
        $talkTime = $this->getBetween($text_body, "Talk time :", "Waiting time :") ?? '';

        $propertyFinderProxyNumber = "042815555";
        $formatted_start_date = null;
        $formatted_finish_date = null;
        $callLog = [];

        if ($call_start) {
            $call_start_date = Carbon::createFromFormat('Y-m-d\TH:i:s\Z', $call_start);
            $call_start_date->addHours(4);
            $call_start_date = $call_start_date->setTimezone('Asia/Dubai');
            $call_start_date = $call_start_date->subMinute();

            $formatted_start_date = $call_start_date->format('Y-m-d H:i:s');
        }
        if ($call_end) {
            $call_finish_date = Carbon::createFromFormat('Y-m-d\TH:i:s\Z', $call_end);
            $call_finish_date->addHours(4);
            $call_finish_date = $call_finish_date->setTimezone('Asia/Dubai');
            $call_finish_date = $call_finish_date->addMinute();

            $formatted_finish_date = $call_finish_date->format('Y-m-d H:i:s');
        }

        if ($call_start && $call_end) {
            $callLog = CallLog::where('client_number', $propertyFinderProxyNumber)->where('user_id', $agent_id)->where('call_started_at', ">", $formatted_start_date)->where('call_ended_at', "<", $formatted_finish_date)->first();
        }

        if ($agent_id) {

            $currentDateTime = Carbon::now();
            $minsAgo = Carbon::now()->subMinute(15);

            $lead = Lead::where('agent_id', $agent_id)
                ->whereRaw("REPLACE(mobile, ' ', '') LIKE ?", ['%' . substr($trim_mobile, -8) . '%'])
                ->where('created_at', '>=', $minsAgo)
                ->where(function ($q) {
                    $q->whereIn('source_id', [31, 50])->orWhereNull('source_id');
                })
                ->where('lead_medium_id', 2)
                ->orderBy('id', 'DESC')
                ->first();

            $isUpdate = 'created';

            if ($lead) {
                $lead->update(['source_id' => 31, 'lead_medium_id' => 2]);
                $isUpdate = 'updated';

            } else {
                $trimmedMobile = validatePhoneNumber($mobile);
                $lead = Lead::where('agent_id', $agent_id)
                    ->where('mobile',$trimmedMobile)
                    ->where('source_id', 31)
                    ->where('lead_medium_id', 2)
                    ->first();
                if($lead){
                    $lead->updated_at = now();
                    $lead->save();
                }else{

                    $lead = new Lead();
                    $lead->mobile = validatePhoneNumber($mobile);
                    $lead->whatsapp = $mobile;
                    $lead->first_name = 'New';
                    $lead->source_id = 31; // PF
                    $lead->lead_medium_id = 2; // Call
                    $lead->agent_id = $agent_id;
                    $lead->lead_status_id = $talkTime == "0s" ? 1 : 5;
                    $lead->mail_number = $mail_number;
                    $lead->subject = $subject;

                    $lead->save();
                    event(new \App\Events\LeadCreated ($lead));
                }
                if ($callLog) {
                    $callLog->leads()->attach($lead);
                }

            }

            $note = LeadNote::updateOrCreate(
                ['lead_id' => $lead->id, 'note' => 'Auto Import: Lead ' . $isUpdate . ' by Parser. ' . $subject],
                ['lead_id' => $lead->id]
            );

        } // end if agent id exits
    }

    private function dubizzleLeadsCall($message)
    {

        $mail_number = $message->getNumber();

        $subject = $message->getSubject();
        $from = $message->getFrom()->getAddress();
        $to_email = $message->getTo()[0]->getAddress();

        $original_body = $message->getBodyText();
        $html_body = new \Html2Text\Html2Text ($message->getBodyHtml());
        $text_body = $html_body->getText();

        $mobile = null;
        $mobile_pattern = '/\+(\d+)/'; // Regular expression pattern to match the phone number starting with a '+'
        if (preg_match($mobile_pattern, $text_body, $matches)) {
            $mobile = $matches[1];
        }

        // Get Recent Lead matching the number
        $trim_mobile = substr(trim($mobile), -8);

        $agent_id = User::where('secondary_email', $to_email)
            ->orWhere('email', $to_email)
            ->pluck('id')
            ->first();
        // Call Parser
        $date = $this->getBetween($text_body, "DATE", "\n") ?? '';
        $time = $this->getBetween($text_body, "TIME", "\n");
        $datetime = Carbon::createFromFormat('d M Y h:i:s A', $date . ' ' . $time);
        $enddatetime = Carbon::createFromFormat('d M Y h:i:s A', $date . ' ' . $time);

        $dubizzleProxyNumbers = ["048755111", "042486700"];
        $formatted_start_date = null;
        $formatted_finish_date = null;

        $callLog = [];

        if ($agent_id) {

            if ($datetime) {
                $call_start_date = $datetime;
                $call_end_date = $enddatetime;
                $call_start_date = $call_start_date->subMinute();
                $call_end_date = $call_end_date->addMinute();

                $formatted_start_date = $call_start_date->format('Y-m-d H:i:s');
                $formatted_finish_date = $call_end_date->format('Y-m-d H:i:s');

                $callLog = CallLog::whereIn('client_number', $dubizzleProxyNumbers)
                    ->where('user_id', $agent_id)
                    ->where('call_started_at', '>', $formatted_start_date)
                    ->where('call_started_at', '<', $formatted_finish_date)
                    ->first();
            }

            $currentDateTime = Carbon::now();
            $minsAgo = Carbon::now()->subMinute(15);

            $trimmedMobile = validatePhoneNumber($mobile);

            $mobile = validatePhoneNumber($trimmedMobile);
            if(checkBlocklistAPI($mobile,Lead::class,$message)){
                $result = array('status' => '0', 'msg' => 'This number is in Blacklist. Please do not contact with this number.', 'error' => 'This number is in Blacklist. Please do not contact with this number.');
                return json_encode($result);
            }


            $lead = Lead::where('agent_id', $agent_id)
                ->whereRaw("REPLACE(mobile, ' ', '') LIKE ?", ['%' . substr($trim_mobile, -8) . '%'])
                ->where('created_at', '>=', $minsAgo)
                ->where(function ($q) {
                    $q->where('source_id', 13)->orWhereNull('source_id');
                })
                ->where('lead_medium_id', 2)
                ->orderBy('id', 'DESC')
                ->first();

            $isUpdate = 'created';

            if ($lead) {
                $isUpdate = 'updated';

            } else {
                $trimmedMobile = validatePhoneNumber($mobile);
                $lead = Lead::where('agent_id', $agent_id)
                    ->where('mobile',$trimmedMobile)
                    ->where('source_id', 13)
                    ->where('lead_medium_id', 2)
                    ->first();
                if($lead){
                    $lead->updated_at = now();
                    $lead->save();
                }else{
                    $lead = new Lead();
                    $lead->mobile =  validatePhoneNumber($mobile);
                    $lead->whatsapp = $mobile;
                    $lead->first_name = 'New';
                    $lead->source_id = 13; // Dubizzle
                    $lead->lead_medium_id = 2; // Call
                    $lead->agent_id = $agent_id;
                    $lead->lead_status_id = Str::contains($subject, "You missed a call") ? 1 : 5;
                    $lead->mail_number = $mail_number;
                    $lead->subject = $subject;
                    $lead->save();

                    event(new \App\Events\LeadCreated ($lead));
                }
                if ($callLog) {
                    $callLog->leads()->attach($lead);
                }
            }

            $note = LeadNote::updateOrCreate(
                ['lead_id' => $lead->id, 'note' => 'Auto Import: Lead ' . $isUpdate . ' by Parser. ' . $subject],
                ['lead_id' => $lead->id]
            );

        } //end if agent id exits

    }
    private function BayutLeadsCall($message)
    {

        $mail_number = $message->getNumber();

        $subject = $message->getSubject();
        $from = $message->getFrom()->getAddress();
        $to_email = $message->getTo()[0]->getAddress();

        $original_body = $message->getBodyText();
        $html_body = new \Html2Text\Html2Text ($message->getBodyHtml());
        $text_body = $html_body->getText();

        $mobile = null;
        $mobile_pattern = '/\+(\d+)/'; // Regular expression pattern to match the phone number starting with a '+'
        if (preg_match($mobile_pattern, $text_body, $matches)) {
            $mobile = $matches[1];
        }


        $mobile = validatePhoneNumber($mobile);
        if(checkBlocklistAPI($mobile,Lead::class,$message)){
            $result = array('status' => '0', 'msg' => 'This number is in Blacklist. Please do not contact with this number.', 'error' => 'This number is in Blacklist. Please do not contact with this number.');
            return json_encode($result);
        }



        // Get Recent Lead matching the number
        $trim_mobile = substr(trim($mobile), -8);

        $agent_id = User::where('secondary_email', $to_email)
            ->orWhere('email', $to_email)
            ->pluck('id')
            ->first();
        $dateString = $this->getBetween($text_body, "Call Time:\n\t\t", "\n") ?? '';
        $datetime = Carbon::createFromFormat('d M Y H:i', $dateString);

        $enddatetime = Carbon::createFromFormat('d M Y H:i', $dateString);

        $bayutProxyNumbers = ["042493001"];
        $formatted_start_date = null;
        $formatted_finish_date = null;

        $callLog = [];

        if ($agent_id) {

            if ($datetime) {
                $call_start_date = $datetime;
                $call_end_date = $enddatetime;
                $call_start_date = $call_start_date->subMinute();
                $call_end_date = $call_end_date->addMinute();

                $formatted_start_date = $call_start_date->format('Y-m-d H:i:s');
                $formatted_finish_date = $call_end_date->format('Y-m-d H:i:s');

                $callLog = CallLog::whereIn('client_number', $bayutProxyNumbers)
                    ->where('user_id', $agent_id)
                    ->where('call_started_at', '>', $formatted_start_date)
                    ->where('call_started_at', '<', $formatted_finish_date)
                    ->first();
            }

            $currentDateTime = Carbon::now();
            $minsAgo = Carbon::now()->subMinute(15);

            $lead = Lead::where('agent_id', $agent_id)
                ->whereRaw("REPLACE(mobile, ' ', '') LIKE ?", ['%' . substr($trim_mobile, -8) . '%'])
                ->where('created_at', '>=', $minsAgo)
                ->where(function ($q) {
                    $q->whereIn('source_id', [31, 4])->orWhereNull('source_id');
                })
                ->where('lead_medium_id', 2)
                ->orderBy('id', 'DESC')
                ->first();

            $isUpdate = 'created';

            if ($lead) {
                $isUpdate = 'updated';
            } else {

                $trimmedMobile = validatePhoneNumber($mobile);
                $lead = Lead::where('agent_id', $agent_id)
                ->where('mobile',$trimmedMobile)
                ->where('source_id', 4)
                ->where('lead_medium_id', 2)
                ->first();
                if($lead){
                    $lead->updated_at = now();
                    $lead->save();
                }else{
                    $lead = new Lead();
                    $lead->mobile = validatePhoneNumber($mobile);
                    $lead->whatsapp = $mobile;
                    $lead->first_name = 'New';
                    $lead->source_id = 4; // Bayut
                    $lead->lead_medium_id = 2; // Call
                    $lead->agent_id = $agent_id;
                    $lead->lead_status_id = 1; // New
                    $lead->mail_number = $mail_number;
                    $lead->subject = $subject;
                    $lead->save();
                    event(new \App\Events\LeadCreated ($lead));
                }
                if ($callLog) {
                    $callLog->leads()->attach($lead);
                }
            }

            $note = LeadNote::updateOrCreate(
                ['lead_id' => $lead->id, 'note' => 'Auto Import: Lead ' . $isUpdate . ' by Parser. ' . $subject],
                ['lead_id' => $lead->id]
            );

        } //end if agent id exits

    }

    private function propertyFinderAgentEmail($message)
    {

        $mail_number = $message->getNumber();
        $subject = $message->getSubject();
        $from = $message->getFrom()->getAddress();
        $original_body = $message->getBodyText();
        $html_body = new \Html2Text\Html2Text ($message->getBodyHtml());
        $text_body = $html_body->getText();

        $mobile_pattern = '/tel:\+(\d+)/';
        $pattern = "/tel:(\d+)/";
        // preg_match($reference_pattern, $text_body, $ref_matches);

        preg_match($mobile_pattern, $text_body, $mobile_matches);
        $mobile = trim($mobile_matches[1] ?? '');
        if (!$mobile_matches) {
            $mobile_pattern = "/tel:(\d+)/";
            preg_match($mobile_pattern, $text_body, $mobile_matches);
            $mobile = trim($mobile_matches[1] ?? '');
        }
        $name = strtolower($this->getBetween(str_replace("\n","", $text_body), "You can respond to", "by calling or emailing")) ?? 'NA';
        $inquiry_type = $this->getBetween($text_body, "TYPE OF ENQUIRY: ", "\n") ?? '';
        $agent_name = $this->getBetween($text_body, "Dear ", ",") ?? '';
        $message = $this->getBetween($text_body, $inquiry_type, "You can respond") ?? '';
        $email = $this->getBetween($text_body, "by calling or emailing on:", "+") ?? '';

        //$lead_type_id = 11; // Buyer as type
        switch ($inquiry_type) {
            case 'I am looking to buy':
                $lead_type_id = 2; // Buyer as type
                break;
            case 'I am looking to sell':
                $lead_type_id = 4; // Seller as type
                break;
            case 'I am looking to rent':
                $lead_type_id = 1; // Tenant
                break;
            default:
                $lead_type_id = 11; // Unspecified
                break;
        }

        $agent = User::where("name", 'LIKE', "%" . $agent_name . "%")->orWhere('pp_name', 'LIKE', "%" . $agent_name . "%")->first();
        $source = Source::where('name', 'LIKE', '%Property Finder%')->first();
        $medium = Medium::where('name', 'LIKE', "%" . "Email%")->first();

        $trimmedEmail = strtolower(trim($email)) ?? '';
        $trimmedMobile = trim(str_replace("tel:", "", $mobile_matches[0])) ?? '';

        $mobile = validatePhoneNumber($trimmedMobile);
        if(checkBlocklistAPI($mobile,Lead::class,$message)){
            $result = array('status' => '0', 'msg' => 'This number is in Blacklist. Please do not contact with this number.', 'error' => 'This number is in Blacklist. Please do not contact with this number.');
            return json_encode($result);
        }


        $existingLead = Lead::when($trimmedEmail, function($q) use ($trimmedEmail) {
                            $q->where('email', $trimmedEmail);
                        })
                        ->when($trimmedMobile, function($q) use ($trimmedMobile) {
                            $q->where('mobile', $trimmedMobile);
                        })
                        ->where('source_id', $source->id)
                        ->when($agent?->id,function($q) use($agent){
                            $q->where('agent_id', $agent->id);
                        })
                        ->when($medium?->id,function($q) use($medium){
                            $q->where('lead_medium_id', $medium->id);
                        })
                        ->exists();

        if($existingLead){
            return;
        }
        $lead = Lead::create(
            [
                'mail_number' => $mail_number,
                'first_name' => ucfirst(trim($name)) ?? '',
                'mail_number' => $mail_number,
                'email' => strtolower(trim($email)) ?? '',
                'mobile' => trim(str_replace("tel:", "", $mobile_matches[0])) ?? '',
                'whatsapp' => trim($mobile) ?? '',
                'message' => $message ?? '',
                'subject' => $subject . ' <' . $from . '>',
                'for' => "Unspecified",
                'agent_id' => $agent?->id,
                'type_id' => $lead_type_id,
                'source_id' => $source->id ?? 31, // Property Finder
                'lead_medium_id' => $medium?->id,
                'lead_status_id' => 1, // Open
                // 'lead_sub_status_id' => 4, //Not yet contacted
            ]
        );
        event(new \App\Events\LeadCreated ($lead));
        $note = LeadNote::updateOrCreate(
            ['lead_id' => $lead->id, 'note' => 'Auto Import> ' . $message],
            ['lead_id' => $lead->id]
        );

        $html_data = "Name: {$name}<br>";
        $html_data .= "Email: {$email}<br>";
        $html_data .= "Mobile: {$mobile}<br>";
        $html_data .= "Message: {$message}<br>";
        $html_data .= "<br>-- PF Lead imported successfully --<br><br><br>";

    }
    private function getPropertyLeadTypeId($for)
    {
        switch ($for) {
            case 'Residential for sale':
            case 'Commercial for sale':
                return 2;
            case 'Residential for rent':
            case 'Commercial for rent':
                return 1;
            default:
                return 10;
        }
    }
    private function dubizzleParse($message)
    {
        $mail_number = $message->getNumber();
        $subject = $message->getSubject();
        $from = $message->getFrom()->getAddress();
        $original_body = $message->getBodyText();
        $html_data = '';
        $html_data = $html_data . "<br>** Featching Dubizzle Inquiry Emails<br>";
        $html_data = $html_data . "------------------------------------------<br>";

        $html_body = new \Html2Text\Html2Text ($message->getBodyHtml());
        $text_body = $html_body->getText();

        $ref_number = $this->getBetween($text_body, "REF NO:", "NAME:") ?? '';
        $name = $this->getBetween($text_body, "NAME:", "TELEPHONE:") ?? 'NA';
        $email = $this->getBetween($text_body, "EMAIL:", "MESSAGE:") ?? '';
        $mobile = $this->getBetween($text_body, "tel:[", "]]") ?? '';
        $message = $this->getBetween($text_body, "MESSAGE:", "Already found someone?") ?? '';
        // //
        // $message .= '<br>(Dubizzle):: Name:' . trim($name) . '   /  Email: ' . trim($email) . '   /  Mobile: ' . trim($mobile) . '/  Agent: ' . optional($property)->agent_id . '   /  Ref#: ' . trim($ref_number) . '<br>';
        // //
        $html_data = $html_data . "Ref_number: " . $ref_number;
        $html_data = $html_data . "<br>";
        $html_data = $html_data . "Name: " . $name;
        $html_data = $html_data . "<br>";
        $html_data = $html_data . "Email: " . $email;
        $html_data = $html_data . "<br>";
        $html_data = $html_data . "Mobile: " . $mobile;
        $html_data = $html_data . "<br>";
        $html_data = $html_data . "Message: " . $message;
        $sub = $subject . ' <' . $from . '>';
        $trim_mobile = substr(trim($mobile), 1);
        //$lead_by_number  =  Lead::where('mobile', 'LIKE', "%{$trim_mobile}%")->orderBy('id', 'DESC')->first();


        $mobile = validatePhoneNumber($trim_mobile);
        if(checkBlocklistAPI($mobile,Lead::class,$message)){
            $result = array('status' => '0', 'msg' => 'This number is in Blacklist. Please do not contact with this number.', 'error' => 'This number is in Blacklist. Please do not contact with this number.');
            return json_encode($result);
        }

        $arry = [
            'crm' => 'Dubizzle',
            'mail_number' => $mail_number,
            'subject' => $subject,
            'from' => $from,
            'original_body' => $original_body,
            'html_body' => $html_body,
            'text_body' => $text_body,
            'ref_number' => $ref_number,
            'mobile' => validatePhoneNumber($mobile),
            'name' => $name,
            'email' => $email,
            'message' => $message,
            'mobile_matches' => $mobile,
        ];

        $this->decideLead($arry);

        // if (!empty(trim($ref_number)) || (trim($ref_number) != '')) {
        //     $property = Property::where('ref_no', trim($ref_number))->first();

        //     $message .= '<br>(Dubizzle):: Name:' . trim($name) . '   /  Email: ' . trim($email) . '   /  Mobile: ' . trim($mobile) . '/  Agent: ' . optional($property)->agent_id . '   /  Ref#: ' . trim($ref_number) . '<br>';
        //     $old_lead = Lead::where('email', trim($email))
        //         ->when($property, function ($query) use ($property) {
        //             return $query->where('property_id', $property->id)->where('agent_id', $property->agent_id);
        //         })->where('mobile', trim($mobile))->first();
        //     $medium = Medium::where('name', 'LIKE', "%" . "Email%")->first();
        //     if ($old_lead === null) {
        //         $lead = new Lead();
        //         $lead->mail_number = $mail_number;
        //         $lead->first_name = trim($name) ?? '';
        //         $lead->email = trim($email) ?? '';
        //         $lead->mobile = trim($mobile) ?? '';
        //         $lead->whatsapp = trim($mobile) ?? '';
        //         $lead->lead_medium_id = $medium?->id;

        //         $lead->message = $message ?? '';
        //         $lead->subject = $sub ?? '';

        //         if ($property) {
        //             $lead->for = $property->for;
        //             $lead->agent_id = $property->agent_id;
        //             $lead->property_id = $property->id;
        //             $lead->type_id = $this->getPropertyLeadTypeId($property->for);

        //         } else {
        //             $lead->for = 0;
        //         }
        //         $source = Source::where('name', 'LIKE', '%Dubizzle%')->first();

        //         $lead->lead_status_id = 1;
        //         $lead->lead_sub_status_id = 4;
        //         $lead->source_id = $source->id ?? 13; //Dubizzle source
        //         $lead->save();

        //         $note = new LeadNote();
        //         $note->lead_id = $lead->id;
        //         $note->note = 'Auto Import>  ' . $message;
        //         $note->save();
        //     } else {
        //         $old_lead->update(['first_name' => 'DUPLICATE-' . $old_lead->first_name, 'mail_number' => $mail_number]);

        //         $note = new LeadNote();
        //         $note->lead_id = $old_lead->id;
        //         $note->note = 'Auto Import>  ' . $message;
        //         $note->save();
        //     }

        //     $html_data .= "<br>-- Dubizzle Inquiry Lead imported successfully --<br><br><br>";
        // } else {
        //     $old_lead = Lead::where('email', trim($email))
        //         ->where('mobile', trim($mobile))
        //         ->first();
        //     $message .= '<br>(Dubizzle):: Name:' . trim($name) . '   /  Email: ' . trim($email) . '   /  Mobile: ' . trim($mobile) . '   /  Ref#: ' . trim($ref_number) . '<br>';

        //     if ($old_lead === null) {
        //         $source = Source::where('name', 'LIKE', '%dubizzle%')->first();
        //         $lead = new Lead();
        //         $lead->mail_number = $mail_number;
        //         $lead->first_name = trim($name) ?? '';
        //         $lead->email = trim($email) ?? '';
        //         $lead->mobile = trim($mobile) ?? '';
        //         $lead->message = $message ?? '';
        //         $lead->subject = $sub ?? '';
        //         $lead->for = 0;
        //         $lead->lead_status_id = 1;
        //         $lead->lead_sub_status_id = 4;
        //         $lead->lead_source_id = $source->id ?? 13;
        //         $lead->source_id = $source->id ?? 13;
        //         $lead->save();

        //         $note = new LeadNote();
        //         $note->lead_id = $lead->id;
        //         $note->note = 'Auto Import>  ' . $message;
        //         $note->save();
        //     } else {
        //         $old_lead->update(['first_name' => 'DUPLICATE-' . $lead_by_number->first_name, 'mail_number' => $mail_number]);

        //         $note = new LeadNote();
        //         $note->lead_id = $old_lead->id;
        //         $note->note = 'Auto Import>  ' . $message;
        //         $note->save();

        //     }

        //     $html_data = $html_data . "<br>--Dubizzle Inquiry Lead imported successfully --<br><br><br>";
        // }

    }
    private function houzaParse($message)
    {
        $mail_number = $message->getNumber();
        $subject = $message->getSubject();
        $from = $message->getFrom()->getAddress();
        $original_body = $message->getBodyText();
        $html_data = '';
        $html_data = $html_data . "<br>** Fetching Houza Lead Emails<br>";
        $html_data = $html_data . "------------------------------------------<br>";
        $html_body = new \Html2Text\Html2Text ($message->getBodyHtml());
        $text_body = $html_body->getText();

        $ref_number = $this->getBetween($original_body, "ref: ", " on houza.") ?? '';
        $name = $this->getBetween($original_body, "Name:", "\nEmail:") ?? 'NA';
        $email = $this->getBetween($original_body, "Email:", "\nMobile:") ?? '';
        $mobile = $this->getBetween($original_body, "Mobile:", "\n") ?? '';
        $message = preg_replace('/[^\da-z .]/i', '', $this->getBetween($original_body, "The message is as follows:", "Sender info") ?? '');

        $html_data = $html_data . "Ref_number: " . $ref_number;
        $html_data = $html_data . "<br>";
        $html_data = $html_data . "Name: " . $name;
        $html_data = $html_data . "<br>";
        $html_data = $html_data . "Email: " . $email;
        $html_data = $html_data . "<br>";
        $html_data = $html_data . "Mobile: " . $mobile;
        $html_data = $html_data . "<br>";
        $html_data = $html_data . "Message: " . $message;


        $mobile = validatePhoneNumber($mobile);
        if(checkBlocklistAPI($mobile,Lead::class,$message)){
            $result = array('status' => '0', 'msg' => 'This number is in Blacklist. Please do not contact with this number.', 'error' => 'This number is in Blacklist. Please do not contact with this number.');
            return json_encode($result);
        }



        $sub = $subject . ' <' . $from . '>';
        $trim_mobile = substr(trim($mobile), 1);
        $lead_by_number = Lead::where('mobile', 'LIKE', "%{$trim_mobile}%")->orderBy('id', 'DESC')->first();

        // $arry = [
        //     'crm' => 'Houza',
        //     'mail_number' => $mail_number,
        //     'subject' => $subject,
        //     'from' => $from,
        //     'original_body' => $original_body,
        //     'html_body' => $html_body,
        //     'text_body' => $text_body,
        //     'ref_number' => $ref_number,
        //     'mobile' => $mobile,
        //     'name' => $name,
        //     'email' => $email,
        //     'message' => $message,
        //     'mobile_matches' => $mobile,
        // ];

        // $this->decideLead($arry);

        if (!empty(trim($ref_number)) || (trim($ref_number) != '')) {
            $property = Property::where('ref_no', trim($ref_number))->first();

            $message .= '<br>(Houza):: Name:' . trim($name) . '   /  Email: ' . trim($email) . '   /  Mobile: ' . trim($mobile) . '/  Agent: ' . optional($property)->agent_id . '   /  Ref#: ' . trim($ref_number) . '<br>';

            if ($lead_by_number === null) {
                $lead = new Lead();
                $lead->mail_number = $mail_number;
                $lead->first_name = trim($name) ?? '';
                $lead->email = trim($email) ?? '';
                $lead->mobile = trim($mobile) ?? '';
                $lead->message = $message ?? '';
                $lead->subject = $sub ?? '';

                if ($property) {
                    $lead->for = $property->for;
                    $lead->agent_id = $property->agent_id;
                    $lead->property_id = $property->id;
                    $lead->type_id = $this->getPropertyLeadTypeId($property->for);
                } else {
                    $lead->for = 0;
                }
                $source = Source::where('name', 'LIKE', '%Houza%')->first();

                $lead->lead_status_id = 1;

                $lead->source_id = $source->id ?? 54;
                $lead->lead_medium_id = 1;
                $lead->save();

                $note = new LeadNote();
                $note->lead_id = $lead->id;
                $note->note = 'Auto Import>  ' . $message;
                $note->save();
            } else {
                $lead_by_number->update(['first_name' => 'DUPLICATE-' . $lead_by_number->first_name, 'mail_number' => $mail_number]);

                $note = new LeadNote();
                $note->lead_id = $lead_by_number->id;
                $note->note = 'Auto Import>  ' . $message;
                $note->save();
            }

            $html_data .= "<br>-- Houza  Lead imported successfully --<br><br><br>";
        } else {
            $message .= '<br>(Houza):: Name:' . trim($name) . '   /  Email: ' . trim($email) . '   /  Mobile: ' . trim($mobile) . '   /  Ref#: ' . trim($ref_number) . '<br>';

            if ($lead_by_number === null) {
                $lead = new Lead();
                $lead->mail_number = $mail_number;
                $lead->first_name = trim($name) ?? '';
                $lead->email = trim($email) ?? '';
                $lead->mobile = trim($mobile) ?? '';
                $lead->message = $message ?? '';
                $lead->subject = $sub ?? '';
                $lead->for = 0;
                $lead->lead_status_id = 1;

                $source = Source::where('name', 'LIKE', '%Houza%')->first();


                $lead->source_id = $source->id ?? 54;
                $lead->lead_medium_id = 1; //Email
                $lead->save();
                event(new \App\Events\LeadCreated ($lead));

                $note = new LeadNote();
                $note->lead_id = $lead->id;
                $note->note = 'Auto Import>  ' . $message;
                $note->save();
            } else {
                $lead_by_number->update(['first_name' => 'DUPLICATE-' . $lead_by_number->first_name, 'mail_number' => $mail_number]);

                $note = new LeadNote();
                $note->lead_id = $lead_by_number->id;
                $note->note = 'Auto Import>  ' . $message;
                $note->save();

            }

            $html_data = $html_data . "<br>--Houza Lead imported successfully --<br><br><br>";
        }

    }
    private function rightMoveParse($message)
    {
        $mail_number = $message->getNumber();
        $subject = $message->getSubject();
        $from = $message->getFrom()->getAddress();
        $original_body = $message->getBodyText();
        $html_data = '';
        $html_data = $html_data . "<br>** Fetching Right Move Lead Emails<br>";
        $html_data = $html_data . "------------------------------------------<br>";

        $html_body = new \Html2Text\Html2Text ($message->getBodyHtml());
        $text_body = $html_body->getText();

        $ref_number = $this->getBetween($text_body, "ref: ", " on right move.") ?? '';
        $name = $this->getBetween($text_body, "Name:", "\nEmail:") ?? 'NA';
        $email = $this->getBetween($text_body, "Email:", "\nMobile:") ?? '';
        $mobile = $this->getBetween($text_body, "Mobile:", "\n") ?? '';
        $message = preg_replace('/[^\da-z .]/i', '', $this->getBetween($text_body, "The message is as follows:", "Sender info") ?? '');

        $html_data = $html_data . "Ref_number: " . $ref_number;
        $html_data = $html_data . "<br>";
        $html_data = $html_data . "Name: " . $name;
        $html_data = $html_data . "<br>";
        $html_data = $html_data . "Email: " . $email;
        $html_data = $html_data . "<br>";
        $html_data = $html_data . "Mobile: " . $mobile;
        $html_data = $html_data . "<br>";
        $html_data = $html_data . "Message: " . $message;

        $sub = $subject . ' <' . $from . '>';
        $trim_mobile = substr(trim($mobile), 1);
        $lead_by_number = Lead::where('mobile', 'LIKE', "%{$trim_mobile}%")->orderBy('id', 'DESC')->first();

        if (!empty(trim($ref_number)) || (trim($ref_number) != '')) {
            $property = Property::where('ref_no', trim($ref_number))->first();

            $message .= '<br>(Right Move):: Name:' . trim($name) . '   /  Email: ' . trim($email) . '   /  Mobile: ' . trim($mobile) . '/  Agent: ' . optional($property)->agent_id . '   /  Ref#: ' . trim($ref_number) . '<br>';

            if ($lead_by_number === null) {
                $lead = new Lead();
                $lead->mail_number = $mail_number;
                $lead->first_name = trim($name) ?? '';
                $lead->email = trim($email) ?? '';
                $lead->mobile = trim($mobile) ?? '';
                $lead->message = $message ?? '';
                $lead->subject = $sub ?? '';

                if ($property) {
                    $lead->for = $property->for;
                    $lead->agent_id = $property->agent_id;
                    $lead->property_id = $property->id;
                    $lead->type_id = $this->getPropertyLeadTypeId($property->for);

                } else {
                    $lead->for = 0;
                }

                $lead->lead_status_id = 1;
                // $lead->lead_sub_status_id = 4;
                $source = Source::where('name', 'LIKE', '%CRM%')->first();

                $lead->source_id = $source->id ?? 54;
                $lead->save();

                $note = new LeadNote();
                $note->lead_id = $lead->id;
                $note->note = 'Auto Import>  ' . $message;
                $note->save();
            } else {
                $lead_by_number->update(['first_name' => 'DUPLICATE-' . $lead_by_number->first_name, 'mail_number' => $mail_number]);

                $note = new LeadNote();
                $note->lead_id = $lead_by_number->id;
                $note->note = 'Auto Import>  ' . $message;
                $note->save();
            }

            $html_data .= "<br>-- Right Move  Lead imported successfully --<br><br><br>";
        } else {
            $message .= '<br>(Right Move):: Name:' . trim($name) . '   /  Email: ' . trim($email) . '   /  Mobile: ' . trim($mobile) . '   /  Ref#: ' . trim($ref_number) . '<br>';

            if ($lead_by_number === null) {
                $lead = new Lead();
                $lead->mail_number = $mail_number;
                $lead->first_name = trim($name) ?? '';
                $lead->email = trim($email) ?? '';
                $lead->mobile = trim($mobile) ?? '';
                $lead->message = $message ?? '';
                $lead->subject = $sub ?? '';
                $lead->for = 0;
                $lead->lead_status_id = 1;
                // $lead->lead_sub_status_id = 4;
                $source = Source::where('name', 'LIKE', '%CRM%')->first();

                $lead->source_id = $source->id ?? 54;
                $lead->save();

                $note = new LeadNote();
                $note->lead_id = $lead->id;
                $note->note = 'Auto Import>  ' . $message;
                $note->save();
            } else {
                $lead_by_number->update(['first_name' => 'DUPLICATE-' . $lead_by_number->first_name, 'mail_number' => $mail_number]);

                $note = new LeadNote();
                $note->lead_id = $lead_by_number->id;
                $note->note = 'Auto Import>  ' . $message;
                $note->save();

            }

            $html_data = $html_data . "<br>--Right Move Lead imported successfully --<br><br><br>";
        }

    }

    private function bayutParse($message)
    {
        // Extract message data
        $mailNumber = $message->getNumber();
        $subject = $message->getSubject();
        $from = $message->getFrom()->getAddress();
        $original_body = $message->getBodyText();

        $htmlData = '';

        // dd($mailNumber);
        // Build HTML data string
        $htmlData .= "<br>** Fetching Bayut Agency Inquiry / Bayut Rental Inquiry Emails<br>";
        $htmlData .= "------------------------------------------<br>";

        // Parse email body
        $htmlBody = new \Html2Text\Html2Text ($message->getBodyHtml());
        $textBody = $htmlBody->getText();

        // Extract useful data
        $refNumber = $this->getBetween($textBody, "Reference:", "Bayut ID:") ?? '';
        $name = $this->getBetween($textBody, "Name:", "Email:") ?? 'NA';
        $email = $this->getBetween($textBody, "Email:", "Phone:") ?? '';
        $mobile = $this->getBetween($textBody, "Phone:", "IP Location:") ?? '';
        $message = $this->getBetween($textBody, "INQUIRY MESSAGE", "INQUIRER DETAILS") ?? '';

        // Add extracted data to HTML data string
        $htmlData .= "Ref_number: $refNumber<br>";
        $htmlData .= "Name: $name<br>";
        $htmlData .= "Email: $email<br>";
        $htmlData .= "Mobile: $mobile<br>";
        $htmlData .= "Message: $message";

        // Trim mobile number
        $trimMobile = substr(trim($mobile), 1);

        // Check if lead mobile number already exists


        $mobile = validatePhoneNumber($trimMobile);
        if(checkBlocklistAPI($mobile,Lead::class,$message)){
            $result = array('status' => '0', 'msg' => 'This number is in Blacklist. Please do not contact with this number.', 'error' => 'This number is in Blacklist. Please do not contact with this number.');
            return json_encode($result);
        }

        $arry = [
            'crm' => 'Bayut',
            'mail_number' => $mailNumber,
            'subject' => $subject,
            'from' => $from,
            'original_body' => $original_body,
            'html_body' => $htmlBody ?? '',
            'text_body' => $textBody ?? '',
            'ref_number' => $refNumber,
            'mobile' => $mobile,
            'name' => $name,
            'email' => $email,
            'message' => $message,
            'mobile_matches' => $mobile,
        ];

        $this->decideLead($arry);

        // // Check if reference number exists and fetch associated property
        // if (!empty(trim($refNumber)) || (trim($refNumber) != '')) {
        //     $property = Property::where('ref_no', $refNumber)->first();

        //     if ($property) {
        //         $message .= "<br>(Bayut):: Name: $name / Email: $email / Mobile: $mobile / Agent: $property->agent_id / Ref#: $ref_number<br>";
        //         $source = Source::where('name', 'LIKE', '%Bayut%')->first();

        //         $lead = Lead::updateOrCreate(['mail_number' => $mailNumber], [
        //             'first_name' => $name ?? '',
        //             'email' => $email ?? '',
        //             'mobile' => $mobile ?? '',
        //             'message' => $message ?? '',
        //             'subject' => $sub ?? '',
        //             'for' => $property->for,
        //             'agent_id' => $property->agent_id,
        //             'property_id' => $property->id,
        //             'lead_status_id' => 1,
        //             'lead_medium_id' => $medium?->id,
        //             'lead_sub_status_id' => 4,
        //             'type_id' => $this->getPropertyLeadTypeId($property->for),
        //             'source_id' => $source->id ?? 4,

        //         ]);
        //         $this->createLeadNote($lead->id, "Auto Import>  $message");
        //         $htmlData .= "<br>-- Bayut Inquiry Lead imported successfully --<br><br><br>";
        //     } else {
        //         $message .= "<br>(Bayut):: Name: $name / Email: $email / Mobile: $mobile / Ref#: $refNumber<br>";
        //         $source = Source::where('name', 'LIKE', '%Bayut%')->first();

        //         $lead = Lead::updateOrCreate(['mail_number' => $mailNumber], [
        //             'first_name' => $name ?? '',
        //             'email' => $email ?? '',
        //             'mobile' => $mobile ?? '',
        //             'message' => $message ?? '',
        //             'subject' => $sub ?? '',
        //             'lead_status_id' => 1,
        //             'lead_sub_status_id' => 4,
        //             'lead_medium_id' => $medium?->id,
        //             'source_id' => $source->id ?? 4,

        //         ]);
        //         $this->createLeadNote($lead->id, "Auto Import>  $message");
        //         $htmlData .= "<br>-- Bayut Inquiry Lead imported successfully --<br><br><br>";
        //     }
        // } else {

        //     $message .= "<br>(Bayut):: Name: $name / Email: $email / Mobile: $mobile / Ref#: $refNumber<br>";
        //     $source = Source::where('name', 'LIKE', '%Bayut%')->first();

        //     $lead = Lead::updateOrCreate(['mail_number' => $mailNumber], [
        //         'first_name' => $name ?? '',
        //         'email' => $email ?? '',
        //         'mobile' => $mobile ?? '',
        //         'message' => $message ?? '',
        //         'subject' => $sub ?? '',
        //         'lead_status_id' => 1,
        //         'lead_medium_id' => $medium?->id,
        //         'lead_sub_status_id' => 4,
        //         'source_id' => $source->id ?? 4,
        //     ]);
        //     $this->createLeadNote($lead->id, "Auto Import>  $message");
        //     $htmlData .= "<br>-- Bayut Inquiry Lead imported successfully --<br><br><br>";
        // }
    }
    private function createLeadNote($lead_id, $note)
    {

        LeadNote::create(['lead_id' => $lead_id,
            'note' => $note,
        ]);
        return true;
    }

    public function fetchEmails()
    {
        // dd("fetchEmails");
        // Establish IMAP server connection
        $server = new Server('imap.gmail.com');
        $emailConf = env('PARSER_MAIL_USERNAME');
        $passConf = env('PARSER_MAIL_PASSWORD');
        // If any of above value is not coming return false
        if (!$emailConf || !$passConf) {
            return false;
        }
        $connection = $server->authenticate($emailConf, $passConf);
        $mailbox = $connection->getMailbox('INBOX');

        // Get latest lead mail number
        $lastMailNumber = Lead::max('mail_number') ?? 0;
        // Get the date range for which to fetch emails
        $oneHourAgo = \Carbon\Carbon::now()->subHour();

        $yesterday = \Carbon\Carbon::now()->subDays(2);

        $messages = $mailbox->getMessages(new \Ddeboer\Imap\Search\Date\Since ($yesterday), \SORTDATE , true);

        // Process each email message
        $importedLeadsCount = 0;
        foreach ($messages as $message) {


            $mailNumber = $message->getNumber();
            // Process new leads
            $subject = $message->getSubject();

            $from = $message->getFrom()->getAddress();
            // Skip already imported leads
            $lead = Lead::where('mail_number', $mailNumber)->exists();
            if ($lead) {
                continue;
            }
            if ($from) {
//                if (($from == 'contact@support.jamesedition.com' || $from == 'contact@account.jamesedition.com') && Str::contains($subject, 'New Lead - Your Action Needed')) {
//                    echo $subject . ' -- ' . $from . '<br>\n';
//                }

                if ((str_contains($subject, 'Missed Call')) || (str_contains($subject, 'Call summary')) && $from == 'no-reply@propertyfinder.ae') {
                    //$this->propertyFinderLeadsCall($message);
                    $importedLeadsCount++;
                    $platform[] = 'propertyfinder call';

                } else if ((str_contains($subject, 'Bayut.com Lead Notification:')) && $from == 'noreply@bayut.com') {
                    // $this->bayutLeadsCall($message);
                    // $importedLeadsCount++;
                    $platform[] = 'bayut call';

                } else if (str_contains($subject, 'propertyfinder.ae - Contact')) {

//                    if (((strcasecmp($subject, "propertyfinder.ae - Contact AX CAPITAL Real Estate") === 0) && $from == "no-reply@propertyfinder.ae") || ((strcasecmp($subject, "propertyfinder.ae - Contact AX CAPITAL Real Estate - Primary") === 0) && $from == "no-reply@propertyfinder.ae") ) {
//
//                        //$this->propertyFinderLeadsEmail($message);
//                        //$importedLeadsCount++;
//                        //$platform[] = 'propertyfinder email';
//
//                    } else {
//
//                        //$this->propertyFinderAgentEmail($message);
//                        //$importedLeadsCount++;
//                        //$platform[] = 'propertyfinder agent';
//
//                    }
                } else if (Str::contains($subject, 'dubizzle - someone is interested')) {
                    $this->dubizzleParse($message);

                    $platform[] = 'dubizzle';
                    $importedLeadsCount++;

                } elseif (Str::contains($subject, "You just got a Dubizzle phone lead!") || (Str::contains($subject, "You missed a call") && Str::contains($from, 'dubizzle.com'))) {

                    //$this->dubizzleLeadsCall($message);
                    $platform[] = 'dubizzle';
                } else if (Str::contains($subject, 'Congratulations! You have received a new email lead!') ||
                    Str::contains($subject, 'New Lead! You have received a rental inquiry on Bayut') ||
                    Str::contains($subject, 'New Lead! You have received a sale inquiry on Bayut') ||
                    Str::contains($subject, 'Bayut Rental Inquiry') ||
                    Str::contains($subject, 'Bayut Sale Inquiry')) {
                    // dd("bayut");
                    // $this->bayutParse($message);
                    // $importedLeadsCount++;
                    $platform[] = 'bayut';

                } else if (Str::contains($subject, 'New houza lead')) {
                    // dd("houza");

                    $this->houzaParse($message);
                    $importedLeadsCount++;
                    $platform[] = 'houza';

                } else if (false && Str::contains($subject, 'Rightmove Overseas Qualified Lead')) {
                    // $this->rightMoveParse($message);
                    // dd("Rightmove");
                    // $importedLeadsCount++;
                } else if (Str::contains($subject, 'New Lead - Your Action Needed') && ($from == 'contact@support.jamesedition.com' || $from == 'contact@account.jamesedition.com')) {

//                    echo $subject;
//                    echo '====<br>';
                    $this->jamesEditionParse($message);
                    $importedLeadsCount++;
                    $platform[] = 'JamesEdition';
                } else if (Str::contains($subject, 'Contact request') && $from == 'crm@axcapital.ae') {

                    $this->emiratesEstateParse($message);
                    $importedLeadsCount++;
                    $platform[] = 'Emirates.Estate';
                }

            }
        }

        // dd($platform);
        $response = ['message' => $importedLeadsCount . ' leads have been imported successfully', 'status' => true, 'data' => ''];
        return response()->json($response);
    }

    private function bayutCallParse()
    {
        if (\Str::contains($subject, 'Bayut.com Lead Notification: CALL Received')) {

            $original_body = $message->getBodyText();
            $html_data = $html_data . "<br>** Featching Bayut.com Lead Notification: CALL Received Emails<br>";

            $html_data = $html_data . "------------------------------------------<br>";

            // echo "<br>** Featching Bayut.com Lead Notification: CALL Received Emails<br>";

            // echo  "------------------------------------------<br>";

            $html_body = new \Html2Text\Html2Text ($message->getBodyHtml());
            $text_body = $html_body->getText();

            //print_r( $text_body);
            $sub = $subject . ' <' . $from . '>';

            $mobile = $this->getBetween($text_body, "[tel:", "]") ?? '';
            $agent_name = $this->getBetween($text_body, "Received By:", "Call Time:") ?? '';
            $call_history = $this->getBetween($text_body, "Call Details", "Best Regards") ?? '';

            $html_data = $html_data . "Agent: " . $agent_name;
            $html_data = $html_data . "<br>";

            $html_data = $html_data . "Mobile: " . $mobile;
            $html_data = $html_data . "<br>";
            $html_data = $html_data . "Call history: " . $call_history;

            // echo "Agent: ".$agent_name;
            // echo "<br>";

            // echo "Mobile: ".$mobile;
            // echo "<br>";
            // echo "Call history: ".$call_history;

            //$lead_by_number  =  Lead::where('mobile',trim($mobile))->orderBy('updated_at', 'desc')->first();
            $call_history .= '<br>(Bayut Call):: Agent Name:' . trim($agent_name) . '   /  Mobile: ' . trim($mobile) . '<br>';

            $trim_mobile = substr(trim($mobile), 1);
            $lead_by_number = Lead::where('mobile', 'LIKE', "%{$trim_mobile}%")->orderBy('id', 'DESC')->first();

            if ($lead_by_number === null) {

                $lead = Lead::firstOrNew(['mail_number' => $mail_number], [
                    'mobile' => trim($mobile) ?? '', 'subject' => $sub ?? '',

                ]);

                foreach ($all_users as $key) {
                    $agent_fullname = $key['first_name'] . ' ' . $key['last_name'];

                    if (\Str::contains($agent_name, $agent_fullname)) {
                        $lead->agent_id = $key['id'];
                    }
                } // end foreach

                $lead->lead_status_id = 1;
                // $lead->lead_sub_status_id = 4;
                //  $lead->contact_source_id = 45;
                $lead->for = 0;
                $lead->save();
                $note = LeadNote::updateOrCreate(['lead_id' => $lead->id, 'note' => 'Auto Import>  ' . $call_history], [
                    'lead_id' => $lead->id,

                ]);
            } else {
                $lead_update = Lead::where("id", $lead_by_number->id)->update(array('first_name' => 'DUPLICATE-' . $lead_by_number['first_name'], 'mail_number' => $mail_number));

                if ($lead_update) {
                    $note = LeadNote::updateOrCreate(['lead_id' => $lead_by_number->id, 'note' => 'Auto Import>  ' . $call_history], [
                        'lead_id' => $lead_by_number->id,

                    ]);
                }
            }

            $html_data = $html_data . "<br>-- Bayut.com Lead Notification: CALL Received Lead imported successfully --<br><br><br>";

            // echo "<br>-- Bayut.com Lead Notification: CALL Received Lead imported successfully --<br><br><br>";

        }

        if (\Str::contains($subject, 'Bayut.com Lead Notification: CALL Missed')) {

            $original_body = $message->getBodyText();
            $html_data = $html_data . "<br>** Featching Bayut.com Lead Notification: CALL Missed Emails<br>";

            $html_data = $html_data . "------------------------------------------<br>";

            // echo "<br>** Featching Bayut.com Lead Notification: CALL Missed Emails<br>";

            // echo "------------------------------------------<br>";

            $html_body = new \Html2Text\Html2Text ($message->getBodyHtml());
            $text_body = $html_body->getText();

            // print_r( $text_body);

            $mobile = $this->getBetween($text_body, "[tel:", "]") ?? '';
            $agent_name = $this->getBetween($text_body, "Missed By:", "Call Time:") ?? '';
            $call_history = $this->getBetween($text_body, "Call Details", "Best Regards") ?? '';

            $html_data = $html_data . "Agent: " . $agent_name;
            $html_data = $html_data . "<br>";

            $html_data = $html_data . "Mobile: " . $mobile;
            $html_data = $html_data . "<br>";
            $html_data = $html_data . "Call history: " . $call_history;

            /*echo "Agent: ".$agent_name;
            echo "<br>";

            echo "Mobile: ".$mobile;
            echo "<br>";
            echo "Call history: ".$call_history;
             */

            $sub = $subject . ' <' . $from . '>';

            // $lead_by_number  =  Lead::where('mobile',trim($mobile))->orderBy('updated_at', 'desc')->first();
            $call_history .= '<br>(Bayut Call):: Agent Name:' . trim($agent_name) . '   /  Mobile: ' . trim($mobile) . '<br>';

            $trim_mobile = substr(trim($mobile), 1);
            $lead_by_number = Lead::where('mobile', 'LIKE', "%{$trim_mobile}%")->orderBy('id', 'DESC')->first();

            if ($lead_by_number === null) {

                $lead = Lead::firstOrNew(['mail_number' => $mail_number], [
                    'mobile' => trim($mobile) ?? '', 'subject' => $sub ?? '',

                ]);

                foreach ($all_users as $key) {
                    $agent_fullname = $key['first_name'] . ' ' . $key['last_name'];

                    if (\Str::contains($agent_name, $agent_fullname)) {
                        $lead->agent_id = $key['id'];
                    }
                } // end foreach

                $lead->lead_status_id = 1;
                // $lead->lead_sub_status_id = 4;
                $lead->for = 0;
                //  $lead->contact_source_id = 45;
                $lead->save();
                $note = LeadNote::updateOrCreate(['lead_id' => $lead->id, 'note' => 'Auto Import>  ' . $call_history], [
                    'lead_id' => $lead->id,

                ]);
            } else {
                $lead_update = Lead::where("id", $lead_by_number->id)->update(array('first_name' => 'DUPLICATE-' . $lead_by_number['first_name'], 'mail_number' => $mail_number));

                if ($lead_update) {
                    $note = LeadNote::updateOrCreate(['lead_id' => $lead_by_number->id, 'note' => 'Auto Import>  ' . $call_history], [
                        'lead_id' => $lead_by_number->id,

                    ]);
                }
            }

            $html_data = $html_data . "<br>-- Bayut.com Lead Notification: CALL Missed Lead imported successfully --<br><br><br>";

            // echo "<br>-- Bayut.com Lead Notification: CALL Missed Lead imported successfully --<br><br><br>";

        }
    }
    public function callParser($rawData)
    {

        $original_body = $message->getBodyText();
        $html_data = $html_data . "<br>** Featching Property Finder: CALL Received/Missed Call Emails<br>";
        $html_data = $html_data . "------------------------------------------<br>";

        // echo  "<br>** Featching Property Finder: CALL Received/Missed Call Emails<br>";
        // echo  "------------------------------------------<br>";
        $html_body = new \Html2Text\Html2Text ($message->getBodyHtml());
        $text_body = $html_body->getText();

        //print_r( $text_body);

        if (\Str::contains($subject, 'Missed Call')) {
            $call_history = $this->getBetween($original_body, "Missed Call", "Thanks for using");
            $call_history = str_replace('*', '', $call_history);
            $agent_name = $this->getBetween($text_body, "Call history", "You just missed") ?? '';
            $agent_name = str_replace(',', '', $agent_name);
        } else if (\Str::contains($subject, 'Call summary')) {
            $call_history = $this->getBetween($original_body, "Call summary", "Thanks for using");
            $call_history = str_replace('*', '', $call_history);
            $agent_name = $this->getBetween($text_body, "Call summary", "You just received") ?? '';
            $agent_name = str_replace(',', '', $agent_name);
        } else {
            $call_history = '';
        }

        $mobile = $this->getBetween($text_body, "[tel:", "]") ?? '';
        // $agent_name = $this->getBetween($text_body,"Received By:","Call Time:") ?? '';
        // $call_history = $this->getBetween($text_body,"Call Details","Best Regards") ?? '';

        $html_data = $html_data . "Agent: " . $agent_name;
        $html_data = $html_data . "<br>";

        $html_data = $html_data . "Mobile: " . $mobile;
        $html_data = $html_data . "<br>";
        $html_data = $html_data . "Call history: " . $call_history;

        // echo  "Agent: ".$agent_name;
        // echo "<br>";
        // echo "Mobile: ".$mobile;
        // echo  "<br>";
        // echo  "Call history: ".$call_history;

        $sub = $subject . ' <' . $from . '>';
        $trim_mobile = substr(trim($mobile), 1);
        $lead_by_number = Lead::where('mobile', 'LIKE', "%{$trim_mobile}%")->orderBy('id', 'DESC')->first();

        // $lead_by_number  =  Lead::where('mobile',trim($mobile))->orderBy('updated_at', 'desc')->first();
        //dd($mail_number, $lead_by_number['mail_number'],  $lead_by_number['mobile'], trim($mobile));

        $call_history .= '<br>(PF Call):: Agent Name:' . trim($agent_name) . '   /  Mobile: ' . trim($mobile) . '<br>';

        if ($lead_by_number === null) {

            $lead = Lead::firstOrNew(['mail_number' => $mail_number], [
                'mobile' => trim('' . $mobile) ?? '', 'subject' => $sub,

            ]);

            foreach ($all_users as $key) {
                $agent_fullname = $key['first_name'] . ' ' . $key['last_name'];

                if (\Str::contains($agent_name, $agent_fullname)) {
                    $lead->agent_id = $key['id'];
                }
            } // end foreach

            $lead->lead_status_id = 1;
            // $lead->lead_sub_status_id = 4;
            $lead->for = 0;

            // $lead->contact_source_id = 46;
            $lead->save();
            $note = LeadNote::updateOrCreate(['lead_id' => $lead->id, 'note' => 'Auto Import>  ' . trim($call_history)], [
                'lead_id' => $lead->id,

            ]);
        } else {

            $lead_update = Lead::where("id", $lead_by_number->id)->update(array('first_name' => 'DUPLICATE-' . $lead_by_number['first_name'], 'mail_number' => $mail_number));

            if ($lead_update) {
                $note = LeadNote::updateOrCreate(['lead_id' => $lead_by_number->id, 'note' => 'Auto Import>  ' . $call_history], [
                    'lead_id' => $lead_by_number->id,

                ]);
            }

            //  $lead_update = Lead::where("id",$lead_by_number->id)->update(array('first_name' => 'DUPLICATE-'.$lead_by_number['first_name']));

            //  if($lead_update)
            //  {
            //     $note = LeadNote::updateOrCreate(['lead_id' => $lead_by_number->id, 'note' => 'Auto Import>  ' . $call_history], ['lead_id' => $lead_by_number->id,

            //     ]);
            // }

        }

        $html_data = $html_data . "<br>-- Property Finder: CALL Received/Missed Lead imported successfully --<br><br><br>";

        // echo  "<br>-- Property Finder: CALL Received/Missed Lead imported successfully --<br><br><br>";

    }

    /**
     * get student tickets
     *
     * @return \Illuminate\Http\Response
     */
    public function result_emails(Request $request)
    {
        //dd($request);
        $msgs = LaravelGmail::message();

        dd($msgs);

    }

    public function get_string_between($string, $start, $end)
    {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) {
            return '';
        }

        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    public function multiexplode($delimiters, $string)
    {

        $ready = str_replace($delimiters, $delimiters[0], $string);
        $launch = explode($delimiters[0], $ready);
        return $launch;
    }

    public function getBetween($string, $start = "", $end = "")
    {
        if (strpos($string, $start)) { // required if $start not exist in $string
            $startCharCount = strpos($string, $start) + strlen($start);
            $firstSubStr = substr($string, $startCharCount, strlen($string));
            $endCharCount = strpos($firstSubStr, $end);
            if ($endCharCount == 0) {
                $endCharCount = strlen($firstSubStr);
            }
            return trim(substr($firstSubStr, 0, $endCharCount), " \t\n\r\0\x0B\xC2\xA0");
        } else {
            return '';
        }
    }

    public function fetch_mails($text)
    {

        //String that recognizes an e-mail
        $str = '/([a-z0-9_\.\-])+\@(([a-z0-9\-])+\.)+([a-z0-9]{2,4})+/i';
        preg_match_all($str, $text, $out);
        //return a blank array if not true otherwise insert the email in $out and return
        return isset($out[0]) ? $out[0] : array();
    }

    private function decideLead($arry)
    {
        $property = Property::where('ref_no', $arry['ref_number'])->first();
        // if($arry['email']=='zaidkuvappunathil@gmail.com'){
        //     dd($arry['mail_number']);
        // }
        $source = Source::where('name', 'LIKE', '%' . $arry['crm'] . '%')->first();
    $medium = Medium::where('name', 'Email')->first();

        if ($property) {

            $lead_type_id = $this->getPropertyLeadTypeId($property->for);

            $mobile = trim(str_replace("tel:", "", str_replace('+', '', $arry['mobile_matches'])));
            $old_lead = Lead::where("email", trim($arry['email']))
                ->where("property_id", $property->id)
                ->where('agent_id', $property->agent_id)->where('mobile', $mobile)
                ->when($source, function ($query) use ($source) {
                    $query->where('source_id', $source->id);
                })
                ->first();


            $for = $property->for ?? '';
            if ($old_lead) {
                if ($arry['crm'] == 'JamesEdition'){
                    $message = 'Updating existing lead '.$old_lead->id.' with same mobile number from new condition';
                    $message .= ' Mobile: ' . $mobile;
                    $message .= ' source : JamesEdition';
                    $message .= ' Lead ID: ' . $old_lead->id;
                    $message .= ' error code: K2234';
                    $this->sendDebugTelegramMessage($message);
                }

                $lead = Lead::where('id', $old_lead->id)->update(
                    [
                        'mail_number' => $arry['mail_number'],
                        'first_name' => 'DUPLICATE-' . ucfirst(trim($arry['name'])) ?? '',
                        'email' => trim($arry['email']) ?? '',
                        'mobile' => validatePhoneNumber(trim(str_replace("tel:", "", str_replace('+', '', $arry['mobile_matches']))) ?? ''),
                        'whatsapp' => trim($arry['mobile']) ?? '',
                        'message' => $arry['message'] . '-' . $arry['mail_number'] . '-' . trim($arry['email']) ?? '',
                        'subject' => $arry['subject'] . ' <' . $arry['from'] . '>',
                        'agent_id' => $property->agent_id,
                        'property_id' => $property->id,
                        'lead_medium_id' => $medium?->id,
                        'type_id' => $lead_type_id,
                        'source_id' => $source->id ?? 31, //Property Finder
                        'lead_status_id' => 1, // Open
                        // 'lead_sub_status_id' => 4, // Not yet contacted
                    ]
                );
                $note = LeadNote::updateOrCreate(
                    ['lead_id' => $old_lead->id, 'note' => 'Auto Import> ' . $arry['message']],
                    ['lead_id' => $old_lead->id]
                );
            } else {

                // if(!Lead::where('mail_number',$arry['mail_number'])->orWhere('email',$arry['email'])->first()){
                $mobileNum = validatePhoneNumber(trim(str_replace("tel:", "", str_replace('+', '', $arry['mobile_matches']))) ?? '');

                $existingLead = Lead::where('source_id', $source->id)
                    ->when($medium?->id, function ($query) use ($medium) {
                        $query->where('lead_medium_id', $medium?->id);
                    })
                    ->where('mobile', $mobileNum)
                    ->when($property->id, function ($query) use ($property) {
                        $query->where('property_id', $property->id)
                            ->where('agent_id', $property->agent_id);
                    })
                    ->first();


                if($existingLead){
                    if ($arry['crm'] == 'JamesEdition'){
                        $message = 'Duplicate lead found with same mobile number from new condition';
                        $message .= ' Mobile: ' . $mobileNum;
                        $message .= ' Lead ID: ' . $existingLead?->id;
                        $message .= ' source : JamesEdition';
                        $message .= ' error code: K2235';
                    $this->sendDebugTelegramMessage($message);
                }
                    return;
                }


                try {
                    $lead = Lead::create([
                        'mail_number' => $arry['mail_number'],
                        'first_name' => ucfirst(trim($arry['name'])) ?? '',
                        'email' => trim($arry['email']) ?? '',
                        'mobile' => validatePhoneNumber(trim(str_replace("tel:", "", str_replace('+', '', $arry['mobile_matches']))) ?? ''),
                        'whatsapp' => trim($arry['mobile']) ?? '',
                        'message' => $arry['message'] ?? '',
                        'subject' => $arry['subject'] . ' <' . $arry['from'] . '>',
                        'for' => [$property->for],
                        'agent_id' => $property->agent_id,
                        'property_id' => $property->id,
                        'type_id' => $lead_type_id,
                        'source_id' => $source->id ?? 31,
                        'lead_medium_id' => $medium?->id,
                        'lead_status_id' => 1, //Open
                        // 'lead_sub_status_id' => 4, // Not yet contacted
                    ]);

                    $criteria = null;
                    $community_property = null;
                    $region = null;
                    $community = null;
                    $sub_community = null;

                    $property = Property::where('ref_no', $arry['ref_number'])->first();
                    if ($property) {
                        $criteria = getPropertyCriteria($property->ref_no);
                        if ((!$community) && $criteria['community_id']) {
                            $community = Community::find($criteria['community_id']);
                        }
                        if ((!$sub_community) && $criteria['subCommunity_id']) {
                            $sub_community = SubCommunity::find($criteria['subCommunity_id']);
                        }
                        if ((!$community_property) && $criteria['communityProperty']) {
                            $community_property = CommunityProperty::find($criteria['communityProperty']);
                        }

                        $leadCriteria = new LeadCriteria();
                        $leadCriteria->lead_id = $lead->id;
                        $leadCriteria->furnished = $property->furnished;
                        $leadCriteria->min_bed_id = $criteria['bed_id'] ?? null;
                        $leadCriteria->max_bed_id = $criteria['bed_id'] ?? null;
                        $leadCriteria->min_bath_id = $criteria['bath_id'] ?? null;
                        $leadCriteria->max_bath_id = $criteria['bath_id'] ?? null;
                        $leadCriteria->min_price = $criteria['minPrice'] ?? null;
                        $leadCriteria->max_price = $criteria['maxPrice'] ?? null;
                        $leadCriteria->save();
                        if ($community) {
                            LeadCriteriaLocation::create([
                                'lead_criteria_id' => $leadCriteria->id,
                                'parentable_type' => Community::class,
                                'parentable_id' => $community->id,
                            ]);
                        }

                        if ($sub_community) {
                            LeadCriteriaLocation::create([
                                'lead_criteria_id' => $leadCriteria->id,
                                'parentable_type' => SubCommunity::class,
                                'parentable_id' => $sub_community->id,
                            ]);
                        }

                        if ($community_property) {
                            LeadCriteriaLocation::create([
                                'lead_criteria_id' => $leadCriteria->id,
                                'parentable_type' => CommunityProperty::class,
                                'parentable_id' => $community_property->id,
                            ]);
                        }
                        $leadCriteria->view = $criteria['view'];
                        $leadCriteria->save();
                    }

                } catch (Exception $e) {
                    return $e;
                }
                event(new \App\Events\LeadCreated ($lead));
                $note = LeadNote::updateOrCreate(
                    ['lead_id' => $lead->id, 'note' => 'Auto Import> ' . $arry['message']],
                    ['lead_id' => $lead->id]
                );
            }

            $html_data = "Ref_number: {$arry['ref_number']}<br>";
            $html_data .= "Name: {$arry['name']}<br>";
            $html_data .= "Email: {$arry['email']}<br>";
            $html_data .= "Mobile: {$arry['mobile']}<br>";
            $html_data .= "Message: {$arry['message']}<br>";
            $html_data .= "<br>-- " . $arry['crm'] . " Lead imported successfully --<br><br><br>";

        } else {
//            echo $arry['crm'].'name '.$arry['name']. ' email '.$arry['email'].' mobile '.$arry['mobile_matches'].' ref '.$arry['ref_number'].' mail number '.$arry['mail_number'].' source '.$source->name ?? 'N/A';
//            echo '\n';

            if($arry['crm']=='Property Finder' || $arry['crm']=='JamesEdition'){

                $mobile = trim(str_replace("tel:", "", str_replace('+', '', $arry['mobile_matches'])));
                $source = Source::where('name', 'LIKE', '%' . $arry['crm'] . '%')->first();
                $medium = Medium::where('name', 'LIKE', "%" . "Email%")->first();

                $lead = Lead::create([
                    'mail_number' => $arry['mail_number'],
                    'first_name' => ucfirst(trim($arry['name'])) ?? '',
                    'email' => trim($arry['email']) ?? '',
                    'mobile' => validatePhoneNumber(trim(str_replace("tel:", "", str_replace('+', '', $arry['mobile_matches']))) ?? ''),
                    'whatsapp' => trim($arry['mobile']) ?? '',
                    'message' => $arry['message'] ?? '',
                    'subject' => $arry['subject'] . ' <' . $arry['from'] . '>',
                    'source_id' => $source->id ?? 31,
                    'lead_medium_id' => $medium?->id,
                    'lead_status_id' => 1, //Open
                    // 'lead_sub_status_id' => 4, // Not yet contacted
                ]);
                event(new \App\Events\LeadCreated($lead));
            }

            $html_data = "No property found for ref_number: {$arry['ref_number']}";
        }
        // dd($arry);
    }
    public function getWordAfterName($string)
    {
        $start = "Name:";
        $end = "\n";

        if (strpos($string, $start)) {
            $startCharCount = strpos($string, $start) + strlen($start);
            $firstSubStr = substr($string, $startCharCount, strlen($string));
            $endCharCount = strpos($firstSubStr, $end);

            if ($endCharCount === false) {
                $endCharCount = strlen($firstSubStr);
            }

            $line = trim(substr($firstSubStr, 0, $endCharCount), " \t\n\r\0\x0B\xC2\xA0");

            // Extract the first word from the line
            $words = explode(' ', $line);
            $firstWord = $words[0];

            return $firstWord;
        } else {
            return '';
        }
    }
}
