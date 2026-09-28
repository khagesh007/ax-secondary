<?php

namespace App\Http\Controllers\Api;

use App\Events\LeadCreated;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Source;
use App\Models\Medium;
use App\Models\LeadNote;
use App\Models\LeadSource;
use App\Models\Note;
use App\Models\Property;
use Validator, Redirect, Response;
use Webklex\IMAP\Client;
use Ddeboer\Imap\Server;
use Ddeboer\Imap\SearchExpression;
use Ddeboer\Imap\Search\Email\To;
use Ddeboer\Imap\Search\Email\From;
use Ddeboer\Imap\Search\Text\Body;
use App\Models\User;
use App\Jobs\ProcessParsedEmail;
use Illuminate\Support\Str;
use DB;
class EmailParserApiController extends Controller
{
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

    private function propertyFinderLeadsEmail($message)
    {

        $mail_number = $message->getNumber();
        $subject = $message->getSubject();
        $from = $message->getFrom()->getAddress();
        $original_body = $message->getBodyText();
        $html_body = new \Html2Text\Html2Text($message->getBodyHtml());
        $text_body = $html_body->getText();

        $reference_pattern = '/REFERENCE: ([^\s]+)/';
        $mobile_pattern = '/tel:\+(\d+)/';

        preg_match($reference_pattern, $text_body, $ref_matches);
        $ref_number = trim($ref_matches[1] ?? '');

        preg_match($mobile_pattern, $text_body, $mobile_matches);
        $mobile = trim($mobile_matches[1] ?? '');

        $name = strtolower($this->getBetween($text_body, "respond to", "calling or emailing")) ?? 'NA';
        $email = $this->getBetween($text_body, "Email:", "Phone:") ?? '';
        $message = $this->getBetween($text_body, "AGENT:", "You can respond") ?? '';
        if (!$message) {
            $message = $this->getBetween($original_body, "you a message:", "You can respond");
        }

        $email_raw = substr($text_body, strpos($text_body, 'emailing on'));
        $all_emails = $this->fetch_mails($email_raw);
        $email = strtolower($all_emails[0] ?? '');

        $property = Property::where('ref_no', $ref_number)->first();
        if ($property) {

            $lead_type_id = $this->getPropertyLeadTypeId($property->for);
            $source = Source::where('name', 'LIKE', '%Property Finder%')->first();
            $old_lead = Lead::where("email", trim($email))->where("property_id", $property->id)->where('agent_id', $property->agent_id)->where('mobile', trim($mobile))->first();
            $medium = Medium::where('name', 'LIKE', "%"."Email%")->first();
            $for = $property->for ?? '';
            if($old_lead){
                $lead = Lead::where('id', $old_lead->id)->update(
                    [
                        'mail_number' => $mail_number,
                        'first_name' => 'DUPLICATE-' . ucfirst(trim($name)) ?? '',
                        'mail_number' => $mail_number,
                        'email' => trim($email) ?? '',
                        'mobile' => trim(str_replace("tel:","",$mobile_matches[0])) ?? '',
                        'whatsapp' => trim($mobile) ?? '',
                        'message' => $message ?? '',
                        'subject' => $subject . ' <' . $from . '>',
                        'agent_id' => $property->agent_id,
                        'property_id' => $property->id,
                        'lead_medium_id' => $medium?->id,
                        'type_id' => $lead_type_id,
                        'source_id' => $source->id??31, //Property Finder
                        'lead_status_id' => 1, // Open
                        // 'lead_sub_status_id' => 4, // Not yet contacted
                    ]
                );
                $note = LeadNote::updateOrCreate(
                    ['lead_id' => $old_lead->id, 'note' => 'Auto Import> ' . $message],
                    ['lead_id' => $old_lead->id]
                );
            }else{
                $lead = Lead::create(
                    [
                        'mail_number' => $mail_number,
                        'first_name' => ucfirst(trim($name)) ?? '',
                        'mail_number' => $mail_number,
                        'email' => trim($email) ?? '',
                        'mobile' => trim(str_replace("tel:","",$mobile_matches[0])) ?? '',
                        'whatsapp' => trim($mobile) ?? '',
                        'message' => $message ?? '',
                        'subject' => $subject . ' <' . $from . '>',
                        'for' => $property->for,
                        'agent_id' => $property->agent_id,
                        'property_id' => $property->id,
                        'type_id' => $lead_type_id,
                        'source_id' => $source->id??31,
                        'lead_medium_id' => $medium?->id,
                        'lead_status_id' => 1, //Open
                        // 'lead_sub_status_id' => 4, // Not yet contacted
                    ]
                );

                $note = LeadNote::updateOrCreate(
                    ['lead_id' => $lead->id, 'note' => 'Auto Import> ' . $message],
                    ['lead_id' => $lead->id]
                );
            }
            $html_data = "Ref_number: {$ref_number}<br>";
            $html_data .= "Name: {$name}<br>";
            $html_data .= "Email: {$email}<br>";
            $html_data .= "Mobile: {$mobile}<br>";
            $html_data .= "Message: {$message}<br>";
            $html_data .= "<br>-- PF Lead imported successfully --<br><br><br>";

        } else {
            $html_data = "No property found for ref_number: {$ref_number}";
        }
    }
    private function propertyFinderAgentEmail($message)
    {

        $mail_number = $message->getNumber();
        $subject = $message->getSubject();
        $from = $message->getFrom()->getAddress();
        $original_body = $message->getBodyText();
        $html_body = new \Html2Text\Html2Text($message->getBodyHtml());
        $text_body = $html_body->getText();


        $mobile_pattern = '/tel:\+(\d+)/';

        // preg_match($reference_pattern, $text_body, $ref_matches);


        preg_match($mobile_pattern, $text_body, $mobile_matches);
        $mobile = trim($mobile_matches[1] ?? '');

        $name = strtolower($this->getBetween($text_body, "You can respond to", "by calling or emailing on")) ?? 'NA';
        $inquiry_type = $this->getBetween($text_body, "TYPE OF ENQUIRY: ", "\n") ?? '';
        $agent_name = $this->getBetween($text_body, "Dear ", ",") ?? '';
        $message = $this->getBetween($text_body,$inquiry_type, "You can respond") ?? '';
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
                $lead_type_id =  1; // Tenant
                 break;
            default:
                $lead_type_id =  11; // Unspecified
                 break;
        }

        echo strtolower(trim($email));
        echo "\nagent name is \n";
        print_r($agent_name) ;
        echo "\nlead type is \n";
        print_r($inquiry_type) ;
        echo "\nlead type is \n";
        print_r($lead_type_id) ;


        $agent = User::where("name", 'LIKE', "%".$agent_name."%")->orWhere('pp_name', 'LIKE', "%".$agent_name."%")->first();
        $medium = Medium::where('name', 'LIKE', "%"."Email%")->first();
        $lead = Lead::create(
            [
                'mail_number' => $mail_number,
                'first_name' => ucfirst(trim($name)) ?? '',
                'mail_number' => $mail_number,
                'email' => strtolower(trim($email)) ?? '',
                'mobile' => trim(str_replace("tel:","",$mobile_matches[0])) ?? '',
                'whatsapp' => trim($mobile) ?? '',
                'message' => $message ?? '',
                'subject' => $subject . ' <' . $from . '>',
                'for' => "Unspecified",
                'agent_id' => $agent?->id,
                'type_id' => $lead_type_id,
                'source_id' => $source->id??31, // Property Finder
                'lead_medium_id' => $medium?->id,
                'lead_status_id' => 1, // Open
                // 'lead_sub_status_id' => 4, //Not yet contacted
                    ]
                );

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
    private function getPropertyLeadTypeId($for) {
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

        $html_body = new \Html2Text\Html2Text($message->getBodyHtml());
        $text_body = $html_body->getText();

        $ref_number = $this->getBetween($text_body, "REF NO:", "NAME:") ?? '';
        $name = $this->getBetween($text_body, "NAME:", "TELEPHONE:") ?? 'NA';
        $email = $this->getBetween($text_body, "EMAIL:", "MESSAGE:") ?? '';
        $mobile = $this->getBetween($text_body, "tel:[", "]]") ?? '';
        $message = $this->getBetween($text_body, "MESSAGE:", "Already found someone?") ?? '';

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

        if (!empty(trim($ref_number)) || (trim($ref_number) != '')) {
            $property = Property::where('ref_no', trim($ref_number))->first();

            $message .= '<br>(Dubizzle):: Name:' . trim($name) . '   /  Email: ' . trim($email) . '   /  Mobile: ' . trim($mobile) . '/  Agent: ' . optional($property)->agent_id . '   /  Ref#: ' . trim($ref_number) . '<br>';
            $old_lead = Lead::where('email', trim($email))
                                ->when($property, function ($query) use ($property) {
                                    return $query->where('property_id', $property->id)->where('agent_id', $property->agent_id);
                                })
                                ->where('mobile', trim($mobile))
                                ->first();
            $medium = Medium::where('name', 'LIKE', "%"."Email%")->first();
            if ($old_lead === null) {
                $lead = new Lead();
                $lead->mail_number = $mail_number;
                $lead->first_name = trim($name) ?? '';
                $lead->email = trim($email) ?? '';
                $lead->mobile = trim($mobile) ?? '';
                $lead->whatsapp = trim($mobile) ?? '';
                $lead->lead_medium_id = $medium?->id;

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
                $source = Source::where('name', 'LIKE', '%Dubizzle%')->first();

                $lead->lead_status_id = 1;
                // $lead->lead_sub_status_id = 4;
                $lead->source_id = $source->id??13; //Dubizzle source
                $lead->save();

                $note = new LeadNote();
                $note->lead_id = $lead->id;
                $note->note = 'Auto Import>  ' . $message;
                $note->save();
            } else {
                $old_lead->update(['first_name' => 'DUPLICATE-' . $old_lead->first_name, 'mail_number' => $mail_number]);

                $note = new LeadNote();
                $note->lead_id = $old_lead->id;
                $note->note = 'Auto Import>  ' . $message;
                $note->save();
            }

            $html_data .= "<br>-- Dubizzle Inquiry Lead imported successfully --<br><br><br>";
        } else {
            $old_lead = Lead::where('email', trim($email))
                                ->where('mobile', trim($mobile))
                                ->first();
            $message .= '<br>(Dubizzle):: Name:' . trim($name) . '   /  Email: ' . trim($email) . '   /  Mobile: ' . trim($mobile) . '   /  Ref#: ' . trim($ref_number) . '<br>';

            if ($old_lead === null) {
                $source = Source::where('name', 'LIKE', '%dubizzle%')->first();
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
                $lead->source_id = $source->id ?? 13;
                $lead->save();

                $note = new LeadNote();
                $note->lead_id = $lead->id;
                $note->note = 'Auto Import>  ' . $message;
                $note->save();
            } else {
                $old_lead->update(['first_name' => 'DUPLICATE-' . $lead_by_number->first_name, 'mail_number' => $mail_number]);

                $note = new LeadNote();
                $note->lead_id = $old_lead->id;
                $note->note = 'Auto Import>  ' . $message;
                $note->save();


            }


        $html_data = $html_data . "<br>--Dubizzle Inquiry Lead imported successfully --<br><br><br>";
        }


    }

    private function dubizzleParse__($message)
    {

        $mail_number = $message->getNumber();
        $subject = $message->getSubject();
        $from = $message->getFrom()->getAddress();
        $original_body = $message->getBodyText();
        $html_body = new \Html2Text\Html2Text($message->getBodyHtml());
        $text_body = $html_body->getText();

        $reference_pattern = '/Ref No: ([^\s]+)/';
        $mobile_pattern = '/tel:\+(\d+)/';

        preg_match($reference_pattern, $text_body, $ref_matches);
        $ref_number = trim($ref_matches[1] ?? '');

        preg_match($mobile_pattern, $text_body, $mobile_matches);
        $mobile = trim($mobile_matches[1] ?? '');

        $name = strtolower($this->getBetween($text_body, "respond to", "calling or emailing")) ?? 'NA';
        $email = $this->getBetween($text_body, "Email:", "Telephone:") ?? '';
        $message = $this->getBetween($text_body, "AGENT:", "You can respond") ?? '';
        if (!$message) {
            $message = $this->getBetween($original_body, "you a message:", "You can respond");
        }

        $email_raw = substr($text_body, strpos($text_body, 'emailing on'));
        $all_emails = $this->fetch_mails($email_raw);
        $email = strtolower($all_emails[0] ?? '');

        $property = Property::where('ref_no', $ref_number)->first();
        if ($property) {

            $lead_type_id = $this->getPropertyLeadTypeId($property->for);
            $source = Source::where('name', 'LIKE', '%Dubizzle%')->first();
            $old_lead = Lead::where("email", trim($email))->where("property_id", $property->id)->where('agent_id', $property->agent_id)->where('mobile', trim($mobile))->first();
            $medium = Medium::where('name', 'LIKE', "%"."Email%")->first();
            $for = $property->for ?? '';
            if($old_lead){
                $lead = Lead::where('id', $old_lead->id)->update(
                    [
                        'mail_number' => $mail_number,
                        'first_name' => 'DUPLICATE-' . ucfirst(trim($name)) ?? '',
                        'mail_number' => $mail_number,
                        'email' => trim($email) ?? '',
                        'mobile' => trim(str_replace("tel:","",$mobile_matches[0])) ?? '',
                        'whatsapp' => trim($mobile) ?? '',
                        'message' => $message ?? '',
                        'subject' => $subject . ' <' . $from . '>',
                        'agent_id' => $property->agent_id,
                        'property_id' => $property->id,
                        'lead_medium_id' => $medium?->id,
                        'type_id' => $lead_type_id,
                        'source_id' => $source->id??13, //dubizzle
                        'lead_status_id' => 1, // Open
                        // 'lead_sub_status_id' => 4, // Not yet contacted
                    ]
                );
                $note = LeadNote::updateOrCreate(
                    ['lead_id' => $old_lead->id, 'note' => 'Auto Import> ' . $message],
                    ['lead_id' => $old_lead->id]
                );
            }else{
                $lead = Lead::create(
                    [
                        'mail_number' => $mail_number,
                        'first_name' => ucfirst(trim($name)) ?? '',
                        'mail_number' => $mail_number,
                        'email' => trim($email) ?? '',
                        'mobile' => trim(str_replace("tel:","",$mobile_matches[0])) ?? '',
                        'whatsapp' => trim($mobile) ?? '',
                        'message' => $message ?? '',
                        'subject' => $subject . ' <' . $from . '>',
                        'for' => $property->for,
                        'agent_id' => $property->agent_id,
                        'property_id' => $property->id,
                        'type_id' => $lead_type_id,
                        'source_id' => $source->id??13,
                        'lead_medium_id' => $medium?->id,
                        'lead_status_id' => 1, //Open
                        // 'lead_sub_status_id' => 4, // Not yet contacted
                    ]
                );

                $note = LeadNote::updateOrCreate(
                    ['lead_id' => $lead->id, 'note' => 'Auto Import> ' . $message],
                    ['lead_id' => $lead->id]
                );
            }
            $html_data = "Ref_number: {$ref_number}<br>";
            $html_data .= "Name: {$name}<br>";
            $html_data .= "Email: {$email}<br>";
            $html_data .= "Mobile: {$mobile}<br>";
            $html_data .= "Message: {$message}<br>";
            $html_data .= "<br>-- PF Lead imported successfully --<br><br><br>";

        } else {
            $html_data = "No property found for ref_number: {$ref_number}";
        }
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

        $html_body = new \Html2Text\Html2Text($message->getBodyHtml());
        $text_body = $html_body->getText();

        $ref_number = $this->getBetween($text_body, "ref: ", " on houza.") ?? '';
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
        $lead_by_number  =  Lead::where('mobile', 'LIKE', "%{$trim_mobile}%")->orderBy('id', 'DESC')->first();

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
                    $lead->lead_type_id = $this->getPropertyLeadTypeId($property->for);
                    $lead->type_id = $this->getPropertyLeadTypeId($property->for);
                } else {
                    $lead->for = 0;
                }
                $source = Source::where('name', 'LIKE', '%CRM%')->first();

                $lead->lead_status_id = 1;
                // $lead->lead_sub_status_id = 4;
                $lead->source_id = $source->id??54;
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
                // $lead->lead_sub_status_id = 4;
                $source = Source::where('name', 'LIKE', '%CRM%')->first();

                $lead->source_id = $source->id??54;
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

        $html_body = new \Html2Text\Html2Text($message->getBodyHtml());
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
        $lead_by_number  =  Lead::where('mobile', 'LIKE', "%{$trim_mobile}%")->orderBy('id', 'DESC')->first();

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
                    $lead->lead_type_id = $this->getPropertyLeadTypeId($property->for);
                    $lead->type_id = $this->getPropertyLeadTypeId($property->for);

                } else {
                    $lead->for = 0;
                }

                $lead->lead_status_id = 1;
                // $lead->lead_sub_status_id = 4;
                $source = Source::where('name', 'LIKE', '%CRM%')->first();

                $lead->source_id = $source->id??54;
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

                $lead->source_id = $source->id??54;
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

    private  function bayutParse($message){
        // Extract message data
        $mailNumber = $message->getNumber();
        $subject = $message->getSubject();
        $from = $message->getFrom()->getAddress();
        $htmlData = '';

        // Build HTML data string
        $htmlData .= "<br>** Fetching Bayut Agency Inquiry / Bayut Rental Inquiry Emails<br>";
        $htmlData .= "------------------------------------------<br>";

        // Parse email body
        $htmlBody = new \Html2Text\Html2Text($message->getBodyHtml());
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
        $leadByNumber = Lead::where('mobile', 'LIKE', "%{$trimMobile}%")
            ->orWhere('email', $email)
            ->orderBy('id', 'DESC')
            ->first();
        $medium = Medium::where('name', 'LIKE', "%"."Email%")->first();
        // Check if reference number exists and fetch associated property
        if (!empty(trim($refNumber)) || (trim($refNumber) != '')) {
            $property = Property::where('ref_no', $refNumber)->first();

            if ($property) {
                $message .= "<br>(Bayut):: Name: $name / Email: $email / Mobile: $mobile / Agent: $property->agent_id / Ref#: $refNumber<br>";
                $source = Source::where('name', 'LIKE', '%Bayut%')->first();

                $old_lead = Lead::where("email", $email)->where("property_id", $property->id)->where('agent_id', $property->agent_id)->where('mobile', $mobile)->first();
                if($old_lead){
                    $lead = Lead::where('id', $old_lead->id)->update(
                        [
                            'mail_number' => $mailNumber,
                            'first_name' => 'DUPLICATE-' . $name ?? '',
                            'email' => $email ?? '',
                            'mobile' => $mobile ?? '',
                            'message' => 'TEST',
                            'subject' => $subject ?? '',
                            'for' => 1,
                            'agent_id' => $property->agent_id??'',
                            'property_id' => $property->id??'',
                            'lead_status_id' => 1,
                            'lead_medium_id' => $medium?->id??'',
                            // 'lead_sub_status_id' => 4,
                            'type_id' => $this->getPropertyLeadTypeId($property->for),
                            'source_id' => $source->id??4,
                        ]
                    );
                    $note = LeadNote::updateOrCreate(
                        ['lead_id' => $old_lead->id, 'note' => 'Auto Import> ' . 'TEST'],
                        ['lead_id' => $old_lead->id]
                    );
                }else{
                    $lead = Lead::create([
                        'mail_number' => $mailNumber,
                        'first_name' => $name ?? '',
                        'email' => $email ?? '',
                        'mobile' => $mobile ?? '',
                        'message' => $message ?? '',
                        'subject' => $subject ?? '',
                        'for' => 1,
                        'agent_id' => $property->agent_id,
                        'property_id' => $property->id,
                        'lead_status_id' => 1,
                        'lead_medium_id' => $medium?->id,
                        // 'lead_sub_status_id' => 4,
                        'type_id' => $this->getPropertyLeadTypeId($property->for),
                        'source_id' => $source->id??4,
                    ]);
                    $note = LeadNote::updateOrCreate(
                        ['lead_id' => $lead->id, 'note' => 'Auto Import> ' . $message],
                        ['lead_id' => $lead->id]
                    );
                }
                // $lead = Lead::updateOrCreate(['mail_number' => $mailNumber], [
                //     'first_name' => $name ?? '',
                //     'email' => $email ?? '',
                //     'mobile' => $mobile ?? '',
                //     'message' => $message ?? '',
                //     'subject' => $sub ?? '',
                //     'for' => $property->for,
                //     'agent_id' => $property->agent_id,
                //     'property_id' => $property->id,
                //     'lead_status_id' => 1,
                //     'lead_medium_id' => $medium?->id,
                //     'lead_sub_status_id' => 4,
                //     'type_id' => $this->getPropertyLeadTypeId($property->for),
                //     'source_id' => $source->id??4,

                // ]);
                // $this->createLeadNote($lead->id, "Auto Import>  $message");
                $htmlData .= "<br>-- Bayut Inquiry Lead imported successfully --<br><br><br>";
            } else {
                $message .= "<br>(Bayut):: Name: $name / Email: $email / Mobile: $mobile / Ref#: $refNumber<br>";
                $source = Source::where('name', 'LIKE', '%Bayut%')->first();

                $old_lead = Lead::where("email", $email)->where('mobile', $mobile)->first();
                if($old_lead){
                    $lead = Lead::where('id', $old_lead->id)->update(
                        [
                            'mail_number' => $mailNumber,
                            'first_name' =>'DUPLICATE-' .  $name ?? '',
                            'email' => $email ?? '',
                            'mobile' => $mobile ?? '',
                            'message' => $message ?? '',
                            'subject' => $sub ?? '',
                            'lead_status_id' => 1,
                            'lead_medium_id' => $medium?->id,
                            // 'lead_sub_status_id' => 4,
                            'source_id' => $source->id??4,
                        ]
                    );
                    $note = LeadNote::updateOrCreate(
                        ['lead_id' => $old_lead->id, 'note' => 'Auto Import> ' . $message],
                        ['lead_id' => $old_lead->id]
                    );
                }else{
                    $lead = Lead::create([
                        'mail_number' => $mailNumber,
                        'first_name' => $name ?? '',
                        'email' => $email ?? '',
                        'mobile' => $mobile ?? '',
                        'message' => $message ?? '',
                        'subject' => $sub ?? '',
                        'lead_status_id' => 1,
                        'lead_medium_id' => $medium?->id,
                        // 'lead_sub_status_id' => 4,
                        'source_id' => $source->id??4,
                    ]);
                    $note = LeadNote::updateOrCreate(
                        ['lead_id' => $lead->id, 'note' => 'Auto Import> ' . $message],
                        ['lead_id' => $lead->id]
                    );
                }
                // $this->createLeadNote($lead->id, "Auto Import>  $message");
                $htmlData .= "<br>-- Bayut Inquiry Lead imported successfully --<br><br><br>";
            }
        }else{

                $message .= "<br>(Bayut):: Name: $name / Email: $email / Mobile: $mobile / Ref#: $ref_number<br>";
                $source = Source::where('name', 'LIKE', '%Bayut%')->first();
                // 'DUPLICATE-' .
                $lead = Lead::updateOrCreate(['mail_number' => $mailNumber], [
                        'first_name' => $name ?? '',
                        'email' => $email ?? '',
                        'mobile' => $mobile ?? '',
                        'message' => $message ?? '',
                        'subject' => $sub ?? '',
                        'lead_status_id' => 1,
                        'lead_medium_id' => $medium?->id,
                        // 'lead_sub_status_id' => 4,
                        'source_id' => $source->id??4,
                    ]);
                $this->createLeadNote($lead->id, "Auto Import>  $message");
                $htmlData .= "<br>-- Bayut Inquiry Lead imported successfully --<br><br><br>";
        }
    }


    private function createLeadNote($lead_id, $note) {

        LeadNote::create(['lead_id' => $lead_id,
                         'note' => $note
                     ]);
        return true;
    }


    public function fetchEmails()
    {
        // Establish IMAP server connection
        $server = new Server('imap.gmail.com');
        $connection = $server->authenticate('secondary_leads@axcrm.ae', 'ffzljasiohhogmud');
        $mailbox = $connection->getMailbox('INBOX');

        // Get latest lead mail number
        $lastMailNumber = Lead::latest()->value('mail_number') ?? 0;

        // Get the date range for which to fetch emails
        $yesterday = Carbon::yesterday();
        $messages = $mailbox->getMessages(new \Ddeboer\Imap\Search\Date\Since($yesterday), \SORTDATE, false);

        // Process each email message
        $importedLeadsCount = 0;
        foreach ($messages as $message) {
            $mailNumber = $message->getNumber();


            // Process new leads
            $subject = $message->getSubject();
            $from = $message->getFrom()->getAddress();

            // Skip already imported leads
            if ($mailNumber <= $lastMailNumber) {
                continue;
            }
            if ($from) {
                if (false && str_contains($subject, 'propertyfinder.ae - Contact')) {
                    if($subject == 'propertyfinder.ae - Contact AX Capital Real Estate' && $from == 'no-reply@propertyfinder.ae'){
                        $this->propertyFinderLeadsEmail($message);
                        $importedLeadsCount++;
                    }else{
                        $this->propertyFinderAgentEmail($message);
                        $importedLeadsCount++;
                    }
                } else if (Str::contains($subject, 'dubizzle - someone is interested')) {
                    $this->dubizzleParse($message);
                    $importedLeadsCount++;
                } else if (false && Str::contains($subject, 'Congratulations! You have received a new email lead!') ||
                    Str::contains($subject, 'New Lead! You have received a rental inquiry on Bayut') ||
                    Str::contains($subject, 'New Lead! You have received a sale inquiry on Bayut') ||
                    Str::contains($subject, 'Bayut Rental Inquiry') ||
                    Str::contains($subject, 'Bayut Sale Inquiry')) {
                    //dd("bayut");
                     $this->bayutParse($message);
                    $importedLeadsCount++;
                }else if (false && Str::contains($subject, 'New houza lead')) {
                    $this->houzaParse($message);
                    $importedLeadsCount++;
                }else if (false &&Str::contains($subject, 'Rightmove Overseas Qualified Lead')) {
                    $this->rightMoveParse($message);
                    $importedLeadsCount++;
                }
            }
        }

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

            $html_body = new \Html2Text\Html2Text($message->getBodyHtml());
            $text_body = $html_body->getText();

            //print_r( $text_body);
            $sub =  $subject . ' <' . $from . '>';

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
            $lead_by_number  =  Lead::where('mobile', 'LIKE', "%{$trim_mobile}%")->orderBy('id', 'DESC')->first();

            if ($lead_by_number === null) {

                $lead = Lead::firstOrNew(['mail_number' => $mail_number], [
                    'mobile' => trim($mobile) ?? '', 'subject' => $sub ?? '',

                ]);

                foreach ($all_users as $key) {
                    $agent_fullname = $key['first_name'] . ' ' . $key['last_name'];

                    if (\Str::contains($agent_name, $agent_fullname)) {
                        $lead->agent_id =  $key['id'];
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


            $html_body = new \Html2Text\Html2Text($message->getBodyHtml());
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



            $sub =  $subject . ' <' . $from . '>';


            // $lead_by_number  =  Lead::where('mobile',trim($mobile))->orderBy('updated_at', 'desc')->first();
            $call_history .= '<br>(Bayut Call):: Agent Name:' . trim($agent_name) . '   /  Mobile: ' . trim($mobile) . '<br>';

            $trim_mobile = substr(trim($mobile), 1);
            $lead_by_number  =  Lead::where('mobile', 'LIKE', "%{$trim_mobile}%")->orderBy('id', 'DESC')->first();

            if ($lead_by_number === null) {


                $lead = Lead::firstOrNew(['mail_number' => $mail_number], [
                    'mobile' => trim($mobile) ?? '', 'subject' => $sub ?? '',

                ]);

                foreach ($all_users as $key) {
                    $agent_fullname = $key['first_name'] . ' ' . $key['last_name'];

                    if (\Str::contains($agent_name, $agent_fullname)) {
                        $lead->agent_id =  $key['id'];
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
            $html_body = new \Html2Text\Html2Text($message->getBodyHtml());
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




            $sub =  $subject . ' <' . $from . '>';
            $trim_mobile = substr(trim($mobile), 1);
            $lead_by_number  =  Lead::where('mobile', 'LIKE', "%{$trim_mobile}%")->orderBy('id', 'DESC')->first();


            // $lead_by_number  =  Lead::where('mobile',trim($mobile))->orderBy('updated_at', 'desc')->first();
            //dd($mail_number, $lead_by_number['mail_number'],  $lead_by_number['mobile'], trim($mobile));

            $call_history .= '<br>(PF Call):: Agent Name:' . trim($agent_name) . '   /  Mobile: ' . trim($mobile) . '<br>';

            if ($lead_by_number === null) {

                $lead = Lead::firstOrNew(['mail_number' => $mail_number], [
                    'mobile' => trim('' . $mobile) ?? '', 'subject' => $sub

                ]);




                foreach ($all_users as $key) {
                    $agent_fullname = $key['first_name'] . ' ' . $key['last_name'];

                    if (\Str::contains($agent_name, $agent_fullname)) {
                        $lead->agent_id =  $key['id'];
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

    function get_string_between($string, $start, $end)
    {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    function multiexplode($delimiters, $string)
    {

        $ready = str_replace($delimiters, $delimiters[0], $string);
        $launch = explode($delimiters[0], $ready);
        return $launch;
    }

    function getBetween($string, $start = "", $end = "")
    {
        if (strpos($string, $start)) { // required if $start not exist in $string
            $startCharCount = strpos($string, $start) + strlen($start);
            $firstSubStr = substr($string, $startCharCount, strlen($string));
            $endCharCount = strpos($firstSubStr, $end);
            if ($endCharCount == 0) {
                $endCharCount = strlen($firstSubStr);
            }
            return trim(substr($firstSubStr, 0, $endCharCount)," \t\n\r\0\x0B\xC2\xA0");
        } else {
            return '';
        }
    }


    function fetch_mails($text){

        //String that recognizes an e-mail
        $str = '/([a-z0-9_\.\-])+\@(([a-z0-9\-])+\.)+([a-z0-9]{2,4})+/i';
        preg_match_all($str, $text, $out);
        //return a blank array if not true otherwise insert the email in $out and return
        return isset($out[0]) ? $out[0] : array();
    }
}
