<?php

namespace App\Http\Controllers\Api;

use App\Events\LeadCreated;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Lead;
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
class EmailParserApiControllerCopy extends Controller
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
private function miraoasis(){
        $original_body = $message->getBodyText();
        $html_data = $html_data . "<br>** Featching Mira Oasis Landing Page Emails<br>";
        $html_data = $html_data . "------------------------------------------<br>";


        // echo "<br>** Featching Mira Oasis Landing Page Emails<br>";
        // echo "------------------------------------------<br>";

        $html_body = new \Html2Text\Html2Text($message->getBodyHtml());
        $text_body = $html_body->getText();

        //print_r( $text_body) ;
        $sub =  $subject . ' <' . $from . '>';
        $name = $this->getBetween($text_body, "NAME", "EMAIL") ?? 'NA';
        $email = $this->getBetween($text_body, "EMAIL", "PHONE") ?? '';
        $mobile = $this->getBetween($text_body, "PHONE", "MESSAGE") ?? '';
        $message = $this->getBetween($text_body, "MESSAGE", "Sent from") ?? '';
        $html_data = $html_data . "Name: " . $name;
        $html_data = $html_data . "<br>";
        $html_data = $html_data . "Email: " . $email;
        $html_data = $html_data . "<br>";
        $html_data = $html_data . "Mobile: " . $mobile;
        $html_data = $html_data . "<br>";
        $html_data = $html_data . "Message: " . $message;


        // echo "Name: ".$name;
        // echo  "<br>";
        // echo "Email: ".$email;
        // echo "<br>";
        // echo "Mobile: ".$mobile;
        // echo "<br>";
        // echo "Message: ".$message;


        if ($email) {

            // $lead_by_number  =  Lead::where('mobile',trim($mobile))->orderBy('updated_at', 'desc')->first();
            $message .= '<br>(Mira Oasis LP):: Name:' . trim($name) . '   /  Email: ' . trim($email) . '   /  Mobile: ' . trim($mobile) . '<br>';


            $trim_mobile = substr(trim($mobile), 1);
            $lead_by_number  =  Lead::where('mobile', 'LIKE', "%{$trim_mobile}%")->orderBy('id', 'DESC')->first();

            if ($lead_by_number === null) {

                $lead = Lead::firstOrNew(['mail_number' => $mail_number], [
                    'first_name' => trim($name) ?? '', 'email' => trim($email) ?? '', 'mobile' => trim($mobile) ?? '', 'message' => $message ?? '', 'subject' => $sub ?? '',

                ]);

                $lead->lead_status_id = 1;
                // $lead->lead_sub_status_id = 4;
                $lead->agent_id = 576; // Edgars
                $lead->for = 0;
                //  $lead->contact_source_id = 47; // Mira Oasis Landing Page
                $lead->save();
                $note = LeadNote::updateOrCreate(['lead_id' => $lead->id, 'note' => 'Auto Import>  ' . $message], [
                    'lead_id' => $lead->id,

                ]);
            } else {
                $lead_update = Lead::where("id", $lead_by_number->id)->where('mail_number', '<>', $mail_number)->update(array('first_name' => 'DUPLICATE-' . $lead_by_number['first_name'], 'mail_number' => $mail_number));

                if ($lead_update) {
                    $note = LeadNote::updateOrCreate(['lead_id' => $lead_by_number->id, 'note' => 'Auto Import>  ' . $message], [
                        'lead_id' => $lead_by_number->id,

                    ]);
                }
            }


            $html_data = $html_data . "<br>-- Mira Oasis Landing Page  Lead imported successfully --<br><br><br>";


            // echo "<br>-- Mira Oasis Landing Page  Lead imported successfully --<br><br><br>";


        }
}
private function dubizzleParseBackup($message)
    {
            $mail_number = $message->getNumber();
            $subject = $message->getSubject();
            $from = $message->getFrom()->getAddress();
            $original_body = $message->getBodyText();
            $html_data = '';
            $html_data = $html_data . "<br>** Featching Dubizzle Inquiry Emails<br>";
            $html_data = $html_data . "------------------------------------------<br>";

            // echo  "<br>** Featching Dubizzle Inquiry Emails<br>";
            // echo  "------------------------------------------<br>";

            $html_body = new \Html2Text\Html2Text($message->getBodyHtml());
            $text_body = $html_body->getText();

            //print_r( $text_body);



            $ref_number     = $this->getBetween($text_body, "REF NO:", "NAME:") ?? '';
            $name           = $this->getBetween($text_body, "NAME:", "TELEPHONE:") ?? 'NA';
            $email          = $this->getBetween($text_body, "EMAIL:", "MESSAGE:") ?? '';
            $mobile         = $this->getBetween($text_body, "tel:[", "]]") ?? '';
            $message        = $this->getBetween($text_body, "MESSAGE:", "Already found someone?") ?? '';

            $html_data = $html_data . "Ref_number: " . $ref_number;
            $html_data = $html_data . "<br>";
            $html_data = $html_data . "Name: " . $name;
            $html_data = $html_data . "<br>";
            $html_data = $html_data . "Email: " . $email;
            $html_data = $html_data . "<br>";
            $html_data = $html_data . "Mobile: " . $mobile;
            $html_data = $html_data . "<br>";
            $html_data = $html_data . "Message: " . $message;

            $sub =  $subject . ' <' . $from . '>';
            $trim_mobile = substr(trim($mobile), 1);
            $lead_by_number  =  Lead::where('mobile', 'LIKE', "%{$trim_mobile}%")->orderBy('id', 'DESC')->first();

            //dd($lead_by_number, $trim_mobile,  $lead_by_number->mobile);
            if (!empty(trim($ref_number)) || (trim($ref_number) != '')) {
                //$ref_number = 'PKH-0001';
                $property = Property::where('ref_no', trim($ref_number))->first();
                // dd($property);
                if ($property) {

                    //$lead_by_number  =  Lead::where('mobile',trim($mobile))->orderBy('updated_at', 'desc')->first();

                    $message .= '<br>(Dubizzle):: Name:' . trim($name) . '   /  Email: ' . trim($email) . '   /  Mobile: ' . trim($mobile) . '/  Agent: ' . $property->agent_id . '   /  Ref#: ' . trim($ref_number) . '<br>';


                    if ($lead_by_number === null) {

                        $lead = Lead::firstOrNew(['mail_number' => $mail_number], [
                            'first_name' => trim($name) ?? '', 'email' => trim($email) ?? '', 'mobile' => trim($mobile) ?? '', 'message' => $message ?? '', 'subject' => $sub ?? '',

                        ]);

                        $lead->for                  = $property->for;
                        $lead->agent_id             = $property->agent_id;
                        $lead->property_id          = $property->id;
                        $lead->lead_status_id       = 1;
                        // $lead->lead_sub_status_id   = 4;
                        $lead->mail_number          = $mail_number;

                        if ($property->for == 'Residential for sale') {
                            $leadTyped = Type::where('name','Buyer')->first();
                            $lead->lead_type_id = $leadTyped->id;
                            $lead->type_id = $leadTyped->id;
                        } else if ($property->for == 'Residential for rent') {
                            $leadTyped = Type::where('name','Tenant')->first();
                            $lead->lead_type_id = $leadTyped->id;
                            $lead->type_id = $leadTyped->id;

                        } else if ($property->for == 'Commercial for sale') {

                            $leadTyped = Type::where('name','Buyer')->first();
                            $lead->lead_type_id = $leadTyped->id;
                            $lead->type_id = $leadTyped->id;

                        }
                         else if ($property->for == 'Commercial for rent') {
                            $leadTyped = Type::where('name','Tenant')->first();
                            $lead->lead_type_id = $leadTyped->id;
                            $lead->type_id = $leadTyped->id;

                        } else {
                            $leadTyped = Type::where('name','Other')->first();
                            $lead->lead_type_id = $leadTyped->id;
                            $lead->type_id = $leadTyped->id;

                        }

                        //  $lead->contact_source_id = 13;
                        $lead->save();
                        $note = LeadNote::updateOrCreate(['lead_id' => $lead->id, 'note' => 'Auto Import>  ' . $message], [
                            'lead_id' => $lead->id,

                        ]);
                    } else {
                        $lead_update = Lead::where("id", $lead_by_number->id)->update(array('first_name' => 'DUPLICATE-' . $lead_by_number['first_name'], 'mail_number' => $mail_number));

                        if ($lead_update) {
                            $note = LeadNote::updateOrCreate(['lead_id' => $lead_by_number->id, 'note' => 'Auto Import>  ' . $message], [
                                'lead_id' => $lead_by_number->id,

                            ]);
                        }
                    }

                    $html_data = $html_data . "<br>-- Dubizzle Inquiry Lead imported successfully --<br><br><br>";






                    // echo "<br>-- Dubizzle Inquiry Lead imported successfully --<br><br><br>";





                } else {

                    // $lead_by_number  =  Lead::where('mobile',trim($mobile))->orderBy('updated_at', 'desc')->first();

                    $message .= '<br>(Dubizzle)::  Name:' . trim($name) . '   /  Email: ' . trim($email) . '   /  Mobile: ' . trim($mobile) . '   /  Ref#: ' . trim($ref_number) . '<br>';



                    if ($lead_by_number === null) {

                        $lead = Lead::firstOrNew(['mail_number' => $mail_number], [
                            'first_name' => trim($name) ?? '', 'email' => trim($email) ?? '', 'mobile' => trim($mobile) ?? '', 'message' => $message ?? '', 'subject' => $sub ?? '',

                        ]);

                        $lead->lead_status_id = 1;
                        // $lead->lead_sub_status_id = 4;
                        $lead->for = 0;
                        //  $lead->contact_source_id = 13;
                        $lead->save();
                        $note = LeadNote::updateOrCreate(['lead_id' => $lead->id, 'note' => 'Auto Import>  ' . $message], [
                            'lead_id' => $lead->id,

                        ]);
                    } else {

                        $lead_update = Lead::where("id", $lead_by_number->id)->update(array('first_name' => 'DUPLICATE-' . $lead_by_number['first_name'], 'mail_number' => $mail_number));

                        if ($lead_update) {
                            $note = LeadNote::updateOrCreate(['lead_id' => $lead_by_number->id, 'note' => 'Auto Import>  ' . $message], [
                                'lead_id' => $lead_by_number->id,

                            ]);
                        }
                    }


                    $html_data = $html_data . "<br>--Dubizzle Inquiry Lead imported successfully --<br><br><br>";

                    // echo "<br>--Dubizzle Inquiry Lead imported successfully --<br><br><br>";


                }
            } else {

                //$lead_by_number  =  Lead::where('mobile','like',trim($mobile))->orderBy('updated_at', 'desc')->first();

                $message .= '<br>(Dubizzle):: Name:' . trim($name) . '   /  Email: ' . trim($email) . '   /  Mobile: ' . trim($mobile) . '   /  Ref#: ' . trim($ref_number) . '<br>';
                if ($lead_by_number === null) {

                    $lead = Lead::firstOrNew(['mail_number' => $mail_number], [
                        'first_name' => trim($name) ?? '', 'email' => trim($email) ?? '', 'mobile' => trim($mobile) ?? '', 'message' => $message ?? '', 'subject' => $sub ?? '',

                    ]);

                    $lead->lead_status_id = 1;
                    // $lead->lead_sub_status_id = 4;
                    $lead->for = 0;
                    // $lead->contact_source_id = 13;
                    $lead->save();
                    $note = LeadNote::updateOrCreate(['lead_id' => $lead->id, 'note' => 'Auto Import>  ' . $message], [
                        'lead_id' => $lead->id,

                    ]);
                } else {
                    $lead_update = Lead::where("id", $lead_by_number->id)->update(array('first_name' => 'DUPLICATE-' . $lead_by_number['first_name'], 'mail_number' => $mail_number));

                    if ($lead_update) {
                        $note = LeadNote::updateOrCreate(['lead_id' => $lead_by_number->id, 'note' => 'Auto Import>  ' . $message], [
                            'lead_id' => $lead_by_number->id,

                        ]);
                    }
                }


                $html_data = $html_data . "<br>--Dubizzle Inquiry Lead imported successfully --<br><br><br>";



                // echo "<br>--Dubizzle Inquiry Lead imported successfully --<br><br><br>";


            }


            // if (\Str::contains($subject, 'You just got a Dubizzle phone lead!') || \Str::contains($subject, 'You missed a call')) {

            //     $original_body = $message->getBodyText();

            //     $html_data = $html_data . "<br>** Featching Dubizzle phone Lead Emails<br>";
            //     $html_data = $html_data . "------------------------------------------<br>";

            //     // echo "<br>** Featching Dubizzle phone Lead Emails<br>";
            //     // echo  "------------------------------------------<br>";


            //     $html_body = new \Html2Text\Html2Text($message->getBodyHtml());
            //     $text_body = $html_body->getText();

            //     //print_r( $text_body);

            //     $sub =  $subject . ' <' . $from . '>';
            //     if (\Str::contains($subject, 'You just got a Dubizzle phone lead!')) {
            //         $agent_name = $this->getBetween($text_body, "Hello", "You just") ?? '';
            //         $agent_name = str_replace(',', '', $agent_name);
            //         $mobile = $this->getBetween($text_body, "CALLER NUMBER", "Good luck") ?? '';
            //         $call_history = $this->getBetween($text_body, "for your reference:", "CALLER NUMBER") ?? '';
            //         $call_history = str_replace('*', '', $call_history);
            //     } else {

            //         $agent_name = $this->getBetween($text_body, "Hello", "A customer just tried") ?? '';
            //         $agent_name = str_replace(',', '', $agent_name);
            //         $mobile = $this->getBetween($text_body, "CALLER NUMBER", "Remember,") ?? '';
            //         $call_history = $this->getBetween($text_body, "follow up:", "CALLER NUMBER") ?? '';
            //         $call_history = str_replace('*', '', $call_history);
            //     }



            //     $html_data = $html_data . "<br>";
            //     $html_data = $html_data . "Agent: " . $agent_name;
            //     $html_data = $html_data . "<br>";
            //     $html_data = $html_data . "Mobile: " . $mobile;
            //     $html_data = $html_data . "<br>";
            //     $html_data = $html_data . "Message: " . $call_history;

            //     // echo "<br>";
            //     // echo "Agent: ".$agent_name;
            //     // echo  "<br>";
            //     // echo "Mobile: ".$mobile;
            //     // echo "<br>";
            //     // echo "Message: ".$call_history;

            //     $trim_mobile = substr(trim($mobile), 1);
            //     $lead_by_number  =  Lead::where('mobile', 'LIKE', "%{$trim_mobile}%")->orderBy('id', 'DESC')->first();

            //     //$lead_by_number  =  Lead::where('mobile',trim($mobile))->orderBy('updated_at', 'desc')->first();
            //     $call_history .= '<br>(Dubizzle Call):: Agent Name:' . trim($agent_name) . '   /  Mobile: ' . trim($mobile) . '<br>';

            //     if ($lead_by_number === null) {
            //         $lead = Lead::firstOrNew(['mail_number' => $mail_number], [
            //             'mobile' => trim($mobile) ?? '', 'subject' => $sub ?? '',

            //         ]);

            //         foreach ($all_users as $key) {
            //             $agent_fullname = $key['first_name'] . ' ' . $key['last_name'];

            //             if (\Str::contains($agent_name, $agent_fullname)) {
            //                 $lead->agent_id =  $key['id'];
            //             }
            //         } // end foreach




            //         $lead->lead_status_id = 1;
            //         $lead->lead_sub_status_id = 4;
            //         //   $lead->contact_source_id = 13;
            //         $lead->for = 0;
            //         $lead->save();
            //         $note = LeadNote::updateOrCreate(['lead_id' => $lead->id, 'note' => 'Auto Import>  ' . $call_history], [
            //             'lead_id' => $lead->id,

            //         ]);
            //     } else {
            //         $lead_update = Lead::where("id", $lead_by_number->id)->update(array('first_name' => 'DUPLICATE-' . $lead_by_number['first_name'], 'mail_number' => $mail_number));

            //         if ($lead_update) {
            //             $note = LeadNote::updateOrCreate(['lead_id' => $lead_by_number->id, 'note' => 'Auto Import>  ' . $call_history], [
            //                 'lead_id' => $lead_by_number->id,

            //             ]);
            //         }
            //     }

            //     $html_data = $html_data . "<br>-- Dubizzle phone Lead imported successfully --<br><br><br>";




            //     // echo "<br>-- Dubizzle phone Lead imported successfully --<br><br><br>";

            // }
    }
        private  function bayutParseBackup($message)
    {
            $mail_number = $message->getNumber();
            $subject    = $message->getSubject();
            $from       = $message->getFrom()->getAddress();
            $html_data  = '';
           // $original_body = $message->getBodyText();
            $html_data  = $html_data . "<br>** Featching Bayut Agency Inquiry / Bayut Rental Inquiry Emails<br>";
            $html_data  = $html_data . "------------------------------------------<br>";
            //parsing body
            $html_body  = new \Html2Text\Html2Text($message->getBodyHtml());
            $text_body  = $html_body->getText();
            $sub        = $subject . ' <' . $from . '>';
            //extracting usefull data
            $ref_number = $this->getBetween($text_body, "Reference:", "Bayut ID:") ?? '';
            $name       = $this->getBetween($text_body, "Name:", "Email:") ?? 'NA';
            $email      = $this->getBetween($text_body, "Email:", "Phone:") ?? '';
            $mobile     = $this->getBetween($text_body, "Phone:", "IP Location:") ?? '';
            $message    = $this->getBetween($text_body, "INQUIRY MESSAGE", "INQUIRER DETAILS") ?? '';

            $html_data = $html_data . "Ref_number: " . $ref_number;
            $html_data = $html_data . "<br>";
            $html_data = $html_data . "Name: " . $name;
            $html_data = $html_data . "<br>";
            $html_data = $html_data . "Email: " . $email;
            $html_data = $html_data . "<br>";
            $html_data = $html_data . "Mobile: " . $mobile;
            $html_data = $html_data . "<br>";
            $html_data = $html_data . "Message: " . $message;

            $medium = Medium::where('name', 'LIKE', "%"."Email%")->first();
            $trim_mobile = substr(trim($mobile), 1);
            //check lead mobile number if exists
            $lead_by_number  =  Lead::where('mobile', 'LIKE', "%{$trim_mobile}%")->orWhere('email', $email)->orderBy('id', 'DESC')->first();

            if (!empty(trim($ref_number)) || (trim($ref_number) != '')) {
                $property   = Property::where('ref_no', trim($ref_number))->get()->first();
                if ($property) {
                    $message .= '<br>(Bayut):: Name:' . trim($name) . '   /  Email: ' . trim($email) . '   /  Mobile: ' . trim($mobile) . '/  Agent: ' . $property->agent_id . '   /  Ref#: ' . trim($ref_number) . '<br>';
                    if ($lead_by_number === null) { //if it mobile number and email id doesn't exist on lead db
                        $lead = Lead::firstOrNew(['mail_number' => $mail_number], [
                            'first_name' => trim($name) ?? '', 'email' => trim($email) ?? '', 'mobile' => trim($mobile) ?? '', 'message' => $message ?? '', 'subject' => $sub ?? '',

                        ]);

                        $lead->for              = $property->for;
                        $lead->agent_id         = $property->agent_id;
                        $lead->property_id      = $property->id;
                        $lead->lead_status_id   = 1;
                        // $lead->lead_sub_status_id = 4;
                        $lead->mail_number      = $mail_number;

                        if ($property->for == 'Residential for sale') {
                            $leadTyped = Type::where('name', 'Buyer')->first();
                            $lead->lead_type_id = $leadTyped->id;
                            $lead->type_id = $leadTyped->id;
                        } else if ($property->for == 'Residential for rent') {
                            $leadTyped = Type::where('name', 'Tenant')->first();
                            $lead->lead_type_id = $leadTyped->id;
                            $lead->type_id = $leadTyped->id;
                        } else if ($property->for == 'Commercial for sale') {
                            $leadTyped = Type::where('name', 'Buyer')->first();
                            $lead->lead_type_id = $leadTyped->id;
                            $lead->type_id = $leadTyped->id;
                        } else if ($property->for == 'Commercial for rent') {
                            $leadTyped = Type::where('name', 'Tenant')->first();
                            $lead->lead_type_id = $leadTyped->id;
                            $lead->type_id = $leadTyped->id;
                        } else {
                            $leadTyped = Type::where('name', 'Other')->first();
                            $lead->lead_type_id = $leadTyped->id;
                            $lead->type_id = $leadTyped->id;
                        }
                        $source = Source::where('name', 'LIKE', '%Bayut%')->first();

                        $lead->source_id = $source->id??4;
                        $lead->save();

                        $note           = new LeadNote();
                        $note->lead_id  = $lead->id;
                        $note->note     = 'Auto Import>  ' . $message;
                        $note->save();
                    } else {
                        $lead_update = Lead::where("id", $lead_by_number->id)->where('mail_number', '<>', $mail_number)->update(array('first_name' => 'DUPLICATE-' . $lead_by_number['first_name'], 'mail_number' => $mail_number));
                        if ($lead_update) {
                            $note           = new LeadNote();
                            $note->lead_id  = $lead_by_number->id;
                            $note->note     = 'Auto Import>  ' . $message;
                            $note->save();
                        }
                    }

                    $html_data = $html_data . "<br>-- Bayut Inquiry Lead imported successfully --<br><br><br>";
                } else {
                    //$lead_by_number  =  Lead::where('mobile',trim($mobile))->orderBy('updated_at', 'desc')->first();
                    $message .= '<br>(Bayut):: Name:' . trim($name) . '   /  Email: ' . trim($email) . '   /  Mobile: ' . trim($mobile) . '  /  Ref#: ' . trim($ref_number) . '<br>';
                    if ($lead_by_number === null) {
                        $lead = Lead::firstOrNew(['mail_number' => $mail_number], [
                            'first_name' => trim($name) ?? '', 'email' => trim($email) ?? '', 'mobile' => trim($mobile) ?? '', 'message' => $message ?? '', 'subject' => $sub ?? '',

                        ]);

                        $lead->lead_status_id       = 1;
                        // $lead->lead_sub_status_id   = 4;
                        $source = Source::where('name', 'LIKE', '%Bayut%')->first();

                        $lead->source_id               = $souce->id??4;
                        $lead->mail_number          = $mail_number;
                        $lead->save();
                        $note           = new LeadNote();
                        $note->lead_id  = $lead->id;
                        $note->note     = 'Auto Import>  ' . $message;
                        $note->save();
                    } else {
                        $lead_update = Lead::where("id", $lead_by_number->id)->where('mail_number', '<>', $mail_number)->update(array('first_name' => 'DUPLICATE-' . $lead_by_number['first_name'], 'mail_number' => $mail_number));

                        if ($lead_update) {
                            $note = LeadNote::create(['lead_id' => $lead_by_number->id, 'note' => 'Auto Import>  ' . $message]);
                        }
                    }
                    $html_data = $html_data . "<br>-- Bayut Inquiry Lead imported successfully --<br><br><br>";
                }
            } else {

                // $lead_by_number  =  Lead::where('mobile',trim($mobile))->orderBy('updated_at', 'desc')->first();
                $message .= '<br>(Bayut):: Name:' . trim($name) . '   /  Email: ' . trim($email) . '   /  Mobile: ' . trim($mobile) . '   /  Ref#: ' . trim($ref_number) . '<br>';
                if ($lead_by_number === null) {
                    $lead = Lead::firstOrNew(['mail_number' => $mail_number], [
                        'first_name' => trim($name) ?? '', 'email' => trim($email) ?? '', 'mobile' => trim($mobile) ?? '', 'message' => $message ?? '', 'subject' => $sub ?? '',

                    ]);
                    $lead->lead_status_id = 1;
                    // $lead->lead_sub_status_id = 4;
                    $lead->for = 0;
                    $lead->mail_number = $mail_number;
                    $source = Source::where('name', 'LIKE', '%Bayut%')->first();

                    $lead->source_id = $source->id??4;
                    $lead->save();

                    $note = new LeadNote();
                    $note->lead_id = $lead->id;
                    $note->note = 'Auto Import>  ' . $message;
                    $note->save();
                } else {
                   // $lead_update = Lead::where("id", $lead_by_number->id)->where('mail_number', '<>', $mail_number)->update(array('first_name' => 'DUPLICATE-' . $lead_by_number['first_name'], 'mail_number' => $mail_number));
                    $note = new LeadNote();
                    $note->lead_id = $lead_by_number->id;
                    $note->note = 'Auto Import>  ' . $message;
                    $note->save();
                }
                $html_data = $html_data . "<br>--  Bayut Inquiry Lead imported successfully --<br><br><br>";
            }
    }
private function propertyFinder($subject, $from, $message, $html_data)
{
    echo '<pre>';
    print_r($message);
    echo '</pre>';
    exit();
        if ($subject == 'propertyfinder.ae - Contact Ax Capital Real Estate'  && $from == 'no-reply@propertyfinder.ae') {

            // if (\Str::contains($subject, 'propertyfinder.ae - Contact AX CAPITAL') )
            // {

            $original_body = $message->getBodyText();
            $html_data = $html_data . "<br>** Featching Property Finder Emails<br>";
            $html_data = $html_data . "------------------------------------------<br>";
            // echo "<br>** Featching Property Finder Emails<br>";
            // echo "------------------------------------------<br>";
            $html_body = new \Html2Text\Html2Text($message->getBodyHtml());
            $text_body = $html_body->getText();

            // print_r($text_body);
            // echo "---------------<br>";

            $ref_number = substr($text_body, strpos($text_body, 'REFERENCE:') + strlen('REFERENCE:'), 10);

            $name       = $this->getBetween($text_body, "respond to", "calling or emailing") ?? 'NA';
            $email      = $this->getBetween($text_body, "Email:", "Phone:") ?? '';
            $mobile     = $this->getBetween($text_body, "[tel:", "]") ?? '';
            $message    = $this->getBetween($text_body, "AGENT:", "You can respond") ?? '';

            if (!$message) {
                $message = $this->getBetween($original_body, "you a message:", "You can respond");
            }


            $email_raw = substr($text_body, strpos($text_body, 'emailing on'));


            $all_emails = $this->fetch_mails($email_raw);
            $email = $all_emails[0];




            $html_data = $html_data . "Ref_number: " . $ref_number;
            $html_data = $html_data . "<br>";
            $html_data = $html_data . "Name: " . $name;
            $html_data = $html_data . "<br>";
            $html_data = $html_data . "Email: " . $email;
            $html_data = $html_data . "<br>";
            $html_data = $html_data . "Mobile: " . $mobile;
            $html_data = $html_data . "<br>";
            $html_data = $html_data . "Message: " . $message;

            // echo  "Ref_number: ".$ref_number;
            // echo "<br>";
            // echo "Name: ".$name;
            // echo "<br>";
            // echo "Email: ".$email;
            // echo "<br>";
            // echo "Mobile: ".$mobile;
            // echo "<br>";
            // echo "Message: ".$message;



            $sub =  $subject . ' <' . $from . '>';
            $trim_mobile = substr(trim($mobile), 1);
            $lead_by_number  =  Lead::where('mobile', 'LIKE', "%{$trim_mobile}%")->orderBy('id', 'DESC')->first();



            if (!empty(trim($ref_number)) || (trim($ref_number) != '')) {
                $property = Property::where('ref_no', (trim($ref_number)))->get()
                    ->first();
                if ($property) {
                    //$lead_by_number  =  Lead::where('mobile',trim($mobile))->orderBy('updated_at', 'desc')->first();
                    $message .= '<br>(PF)::  Name:' . trim($name) . '   /  Email: ' . trim($email) . '   /  Mobile: ' . trim($mobile) . '  /  Agent: ' . $property->agent_id . '  /  Ref#: ' . trim($ref_number) . '<br>';
                    if ($lead_by_number === null) {
                        $lead = Lead::firstOrNew(['mail_number' => $mail_number], [
                            'first_name' => trim($name) ?? '', 'email' => trim($email) ?? '', 'mobile' => '' . trim($mobile) ?? '', 'message' => $message ?? '', 'subject' => $sub ?? '',

                        ]);

                        $lead->for              = $property->for;
                        $lead->agent_id         = $property->agent_id;
                        $lead->property_id      = $property->id;
                        $lead->lead_status_id   = 1;
                        // $lead->lead_sub_status_id = 4;

                        if ($property->for == 1) {
                            $lead->contact_type_id = 2;
                        } else if ($property->for == 2) {
                            $lead->contact_type_id = 1;
                        } else if ($property->for == 3) {
                            $lead->contact_type_id = 1;
                        } else {
                            $lead->contact_type_id = 10;
                        }

                        //  $lead->contact_source_id = 46;
                        $lead->save();
                        $note = LeadNote::updateOrCreate(['lead_id' => $lead->id, 'note' => 'Auto Import>  ' . $message], [
                            'lead_id' => $lead->id,

                        ]);
                    } else {
                        $lead_update = Lead::where("id", $lead_by_number->id)->where('mail_number', '<>', $mail_number)->update(array('first_name' => 'DUPLICATE-' . $lead_by_number['first_name'], 'mail_number' => $mail_number));

                        if ($lead_update) {
                            $note = LeadNote::updateOrCreate(['lead_id' => $lead_by_number->id, 'note' => 'Auto Import>  ' . $message], [
                                'lead_id' => $lead_by_number->id,

                            ]);
                        }
                    }

                    $html_data = $html_data . "<br>-- PF Lead imported successfully --<br><br><br>";

                    //} // end else


                    // echo  "<br>-- PF Lead imported successfully --<br><br><br>";

                    // $note = new \App\Models\LeadNote();
                    // $note->note = 'Auto Import>  ' . $message;
                    // $note->lead_id = $lead->id;
                    // $note->save();



                } else {


                    //$lead_by_number  =  Lead::where('mobile',trim($mobile))->orderBy('updated_at', 'desc')->first();


                    $message .= '<br>(PF)::  Name:' . trim($name) . '   /  Email: ' . trim($email) . '   /  Mobile: ' . trim($mobile) . ' /  Ref#: ' . trim($ref_number) . '<br>';



                    if ($lead_by_number === null) {

                        $lead = Lead::firstOrNew(['mail_number' => $mail_number], [
                            'first_name' => trim($name) ?? '', 'email' => trim($email) ?? '', 'mobile' => trim($mobile) ?? '', 'message' => $message ?? '', 'subject' => $sub ?? '',

                        ]);

                        $lead->lead_status_id = 1;
                        // $lead->lead_sub_status_id = 4;
                        $lead->for = 0;
                        //  $lead->contact_source_id = 46;
                        $lead->save();
                        $note = LeadNote::updateOrCreate(['lead_id' => $lead->id, 'note' => 'Auto Import>  ' . $message], [
                            'lead_id' => $lead->id,

                        ]);
                    } else {
                        $lead_update = Lead::where("id", $lead_by_number->id)->where('mail_number', '<>', $mail_number)->update(array('first_name' => 'DUPLICATE-' . $lead_by_number['first_name'], 'mail_number' => $mail_number));

                        if ($lead_update) {
                            $note = LeadNote::updateOrCreate(['lead_id' => $lead_by_number->id, 'note' => 'Auto Import>  ' . $message], [
                                'lead_id' => $lead_by_number->id,

                            ]);
                        }
                    }

                    $html_data = $html_data . "<br>-- PF Lead imported successfully --<br><br><br>";

                    // echo  "<br>-- PF Lead imported successfully --<br><br><br>";

                }
            } else {



                $message .= '<br>(PF)::  Name:' . trim($name) . '   /  Email: ' . trim($email) . '   /  Mobile: ' . trim($mobile) . ' /  Ref#: ' . trim($ref_number) . '<br>';




                if ($lead_by_number === null) {

                    $lead = Lead::firstOrNew(['mail_number' => $mail_number], [
                        'first_name' => trim($name) ?? '', 'email' => trim($email) ?? '', 'mobile' => trim($mobile) ?? '', 'message' => $message ?? '', 'subject' => $sub ?? '',

                    ]);

                    $lead->lead_status_id = 1;
                    // $lead->lead_sub_status_id = 4;
                    $lead->for = 0;
                    // $lead->contact_source_id = 46;
                    $lead->save();
                    $note = LeadNote::updateOrCreate(['lead_id' => $lead->id, 'note' => 'Auto Import>  ' . $message], [
                        'lead_id' => $lead->id,

                    ]);
                } else {
                    $lead_update = Lead::where("id", $lead_by_number->id)->where('mail_number', '<>', $mail_number)->update(array('first_name' => 'DUPLICATE-' . $lead_by_number['first_name'], 'mail_number' => $mail_number));

                    if ($lead_update) {
                        $note = LeadNote::updateOrCreate(['lead_id' => $lead_by_number->id, 'note' => 'Auto Import>  ' . $message], [
                            'lead_id' => $lead_by_number->id,

                        ]);
                    }
                }
                $html_data = $html_data . "<br>-- PF Lead imported successfully --<br><br><br>";

                // echo  "<br>-- PF Lead imported successfully --<br><br><br>";


            }
        }

        // if (($subject=='Missed Call' || $subject=='Call summary') && ($from =='no-reply@propertyfinder.ae'))
        // {
        if (\Str::contains($subject, 'Call summary') || \Str::contains($subject, 'Missed Call')) {
            // $this->callParser($message,$mail_number,$sub,$html_data);
        }
                            // }
}
    public function fetch_emails_php(Request $request)
    {
        $html_data ='';

        // try
        // {

            $server = new Server('imap.gmail.com');

            $connection = $server->authenticate('secondary_leads@axcrm.ae', 'ffzljasiohhogmud');

            $mailbox = $connection->getMailbox('INBOX');

            $yesterday = Carbon::yesterday();
            $today = Carbon::today();
            $now = Carbon::now();
            $weekStart = $now->subDays($now->dayOfWeek)->setTime(0, 0);
            $agoDate = $now->subDays($now->dayOfWeek)->subWeek();//

            $last_mail_number =Lead::latest()->first()->mail_number ?? '0';

            //print_r( $yesterday);                          // int(0)
            $countr=0;
            $day = $today->subDays(5);

            $messages = $mailbox->getMessages(new \Ddeboer\Imap\Search\Date\Since($day) , \SORTDATE, // Sort criteria
                false
            // Descending order
            );
            $parsed = [];

            $pf = true;
            $dubizzle = true;
            $bayut = true;
            $miraoasis = true;



            //$messages = $mailbox->getMessages();
            foreach ($messages as $message)
            {

            print_r($message->getSubject());
                // echo "----------".$mail_number;
                  echo "======-----------==========----------------============-------------------=======<br>";
                //  echo "==".$last_mail_number;


                $mail_number = $message->getNumber();


                if($mail_number > $last_mail_number){
                // if($mail_number =1288){



                    $subject = $message->getSubject();
                    $from = $message->getFrom()->getAddress();

                    $html_data= $html_data."================COUNTER: ".$countr."<br><br>";

                    // echo "================COUNTER: ".$countr."<br><br>";

                    // print_r($message->getBodyText());
                    // $msg = $message->getBodyText().split('*Reference*', $message->getBodyText()) ;
                    //$msg = explode('*', trim($message->getBodyText()))[0];
                    //$msg= strstr($message->getBodyText(), '*Reference*');  // Displays "llo, there!"
                    if ($message->getFrom())
                    {

                        $all_users= $data['users'] = User::whereHas("roles", function ($q)
                        {
                            $q->whereIn("name", ['Agent']);
                        })->select(['id','first_name','last_name'])
                            ->where('status',1)->get();



                        if ($pf)
                        {


                            $this->propertyFinder($message, $html_data);

                        } // end PF If
                                exit('exit 1');
                        if ($dubizzle)
                        {

                            if (\Str::contains($subject, 'dubizzle - someone is interested'))
                            {

                                $original_body = $message->getBodyText();

                                $html_data= $html_data. "<br>** Featching Dubizzle Inquiry Emails<br>";
                                $html_data= $html_data. "------------------------------------------<br>";

                                // echo  "<br>** Featching Dubizzle Inquiry Emails<br>";
                                // echo  "------------------------------------------<br>";

                                $html_body = new \Html2Text\Html2Text($message->getBodyHtml());
                                $text_body = $html_body->getText();

                                //print_r( $text_body);



                                $ref_number = $this->getBetween($text_body,"REF NO:","NAME:") ?? '';
                                $name = $this->getBetween($text_body,"NAME:","TELEPHONE:") ?? 'NA';
                                $email = $this->getBetween($text_body,"EMAIL:","MESSAGE:") ?? '';
                                $mobile = $this->getBetween($text_body,"tel:[","]]") ?? '';
                                $message = $this->getBetween($text_body,"MESSAGE:","Already found someone?") ?? '';

                                $html_data= $html_data. "Ref_number: ".$ref_number;
                                $html_data= $html_data. "<br>";
                                $html_data= $html_data. "Name: ".$name;
                                $html_data= $html_data. "<br>";
                                $html_data= $html_data. "Email: ".$email;
                                $html_data= $html_data. "<br>";
                                $html_data= $html_data. "Mobile: ".$mobile;
                                $html_data= $html_data. "<br>";
                                $html_data= $html_data. "Message: ".$message;




                                // echo  "Ref_number: ".$ref_number;
                                // echo "<br>";
                                // echo "Name: ".$name;
                                // echo "<br>";
                                // echo "Email: ".$email;
                                // echo "<br>";
                                // echo "Mobile: ".$mobile;
                                // echo "<br>";
                                // echo "Message: ".$message;

                                //  exit();


                                $sub=  $subject.' <'. $from.'>';
                                $trim_mobile = substr(trim($mobile), 1);
                                $lead_by_number  =  Lead::where('mobile', 'LIKE', "%{$trim_mobile}%")->orderBy('id', 'DESC')->first();

                                //dd($lead_by_number, $trim_mobile,  $lead_by_number->mobile);
                                if (!empty(trim($ref_number)) || (trim($ref_number)!=''))
                                {
                                    //$ref_number = 'PKH-0001';
                                    $property = Property::where('ref_no', trim($ref_number))->first();
                                    // dd($property);
                                    if ($property)
                                    {

                                        //$lead_by_number  =  Lead::where('mobile',trim($mobile))->orderBy('updated_at', 'desc')->first();

                                        $message.='<br>(Dubizzle):: Name:'.trim($name).'   /  Email: '.trim($email).'   /  Mobile: '.trim($mobile).'/  Agent: '.$property->agent_id.'   /  Ref#: '.trim($ref_number).'<br>';


                                        if($lead_by_number === null)
                                        {

                                            $lead = Lead::firstOrNew(['mail_number' => $mail_number], ['first_name' => trim($name) ??'', 'email' => trim($email) ??'', 'mobile' => trim($mobile) ??'', 'message' => $message??'', 'subject' => $sub??'',

                                            ]);

                                            $lead->for = $property->for;
                                            $lead->agent_id = $property->agent_id;
                                            $lead->property_id = $property->id;
                                            $lead->lead_status_id = 1;
                                            // $lead->lead_sub_status_id = 4;

                                            if ($property->for == 1)
                                            {
                                                $lead->contact_type_id = 2;
                                            }
                                            else if ($property->for == 2)
                                            {
                                                $lead->contact_type_id = 1;
                                            }
                                            else if ($property->for == 3)
                                            {
                                                $lead->contact_type_id = 1;
                                            }
                                            else
                                            {
                                                $lead->contact_type_id = 10;
                                            }

                                          //  $lead->contact_source_id = 13;
                                            $lead->save();
                                            $note = LeadNote::updateOrCreate(['lead_id' => $lead->id, 'note' => 'Auto Import>  ' . $message], ['lead_id' => $lead->id,

                                            ]);


                                        }else
                                        {
                                            $lead_update = Lead::where("id",$lead_by_number->id)->update(array('first_name' => 'DUPLICATE-'.$lead_by_number['first_name'], 'mail_number' => $mail_number));

                                            if($lead_update)
                                            {
                                                $note = LeadNote::updateOrCreate(['lead_id' => $lead_by_number->id, 'note' => 'Auto Import>  ' . $message], ['lead_id' => $lead_by_number->id,

                                                ]);
                                            }

                                        }

                                        $html_data= $html_data. "<br>-- Dubizzle Inquiry Lead imported successfully --<br><br><br>";






                                        // echo "<br>-- Dubizzle Inquiry Lead imported successfully --<br><br><br>";





                                    }else{

                                        // $lead_by_number  =  Lead::where('mobile',trim($mobile))->orderBy('updated_at', 'desc')->first();

                                        $message.='<br>(Dubizzle)::  Name:'.trim($name).'   /  Email: '.trim($email).'   /  Mobile: '.trim($mobile).'   /  Ref#: '.trim($ref_number).'<br>';



                                        if($lead_by_number === null)
                                        {

                                            $lead = Lead::firstOrNew(['mail_number' => $mail_number], ['first_name' => trim($name) ??'', 'email' => trim($email) ??'', 'mobile' => trim($mobile) ??'', 'message' => $message??'', 'subject' => $sub??'',

                                            ]);

                                            $lead->lead_status_id = 1;
                                            // $lead->lead_sub_status_id = 4;
                                            $lead->for = 0;
                                          //  $lead->contact_source_id = 13;
                                            $lead->save();
                                            $note = LeadNote::updateOrCreate(['lead_id' => $lead->id, 'note' => 'Auto Import>  ' . $message], ['lead_id' => $lead->id,

                                            ]);

                                        }else
                                        {

                                            $lead_update = Lead::where("id",$lead_by_number->id)->update(array('first_name' => 'DUPLICATE-'.$lead_by_number['first_name'], 'mail_number' => $mail_number));

                                            if($lead_update)
                                            {
                                                $note = LeadNote::updateOrCreate(['lead_id' => $lead_by_number->id, 'note' => 'Auto Import>  ' . $message], ['lead_id' => $lead_by_number->id,

                                                ]);
                                            }

                                        }


                                        $html_data= $html_data. "<br>--Dubizzle Inquiry Lead imported successfully --<br><br><br>";

                                        // echo "<br>--Dubizzle Inquiry Lead imported successfully --<br><br><br>";


                                    }

                                }else{

                                    //$lead_by_number  =  Lead::where('mobile','like',trim($mobile))->orderBy('updated_at', 'desc')->first();

                                    $message.='<br>(Dubizzle):: Name:'.trim($name).'   /  Email: '.trim($email).'   /  Mobile: '.trim($mobile).'   /  Ref#: '.trim($ref_number).'<br>';
                                    if($lead_by_number === null)
                                    {

                                        $lead = Lead::firstOrNew(['mail_number' => $mail_number], ['first_name' => trim($name) ??'', 'email' => trim($email) ??'', 'mobile' => trim($mobile) ??'', 'message' => $message??'', 'subject' => $sub??'',

                                        ]);

                                        $lead->lead_status_id = 1;
                                        // $lead->lead_sub_status_id = 4;
                                        $lead->for = 0;
                                       // $lead->contact_source_id = 13;
                                        $lead->save();
                                        $note = LeadNote::updateOrCreate(['lead_id' => $lead->id, 'note' => 'Auto Import>  ' . $message], ['lead_id' => $lead->id,

                                        ]);

                                    }else
                                    {
                                        $lead_update = Lead::where("id",$lead_by_number->id)->update(array('first_name' => 'DUPLICATE-'.$lead_by_number['first_name'], 'mail_number' => $mail_number));

                                        if($lead_update)
                                        {
                                            $note = LeadNote::updateOrCreate(['lead_id' => $lead_by_number->id, 'note' => 'Auto Import>  ' . $message], ['lead_id' => $lead_by_number->id,

                                            ]);
                                        }

                                    }


                                    $html_data= $html_data. "<br>--Dubizzle Inquiry Lead imported successfully --<br><br><br>";



                                    // echo "<br>--Dubizzle Inquiry Lead imported successfully --<br><br><br>";


                                }

                            }



                            if (\Str::contains($subject, 'You just got a Dubizzle phone lead!') || \Str::contains($subject, 'You missed a call'))
                            {

                                $original_body = $message->getBodyText();

                                $html_data= $html_data. "<br>** Featching Dubizzle phone Lead Emails<br>";
                                $html_data= $html_data. "------------------------------------------<br>";

                                // echo "<br>** Featching Dubizzle phone Lead Emails<br>";
                                // echo  "------------------------------------------<br>";


                                $html_body = new \Html2Text\Html2Text($message->getBodyHtml());
                                $text_body = $html_body->getText();

                                //print_r( $text_body);

                                $sub=  $subject.' <'. $from.'>';
                                if(\Str::contains($subject, 'You just got a Dubizzle phone lead!')){
                                    $agent_name = $this->getBetween($text_body,"Hello","You just") ?? '';
                                    $agent_name = str_replace(',', '', $agent_name);
                                    $mobile = $this->getBetween($text_body,"CALLER NUMBER","Good luck") ?? '';
                                    $call_history = $this->getBetween($text_body,"for your reference:","CALLER NUMBER") ?? '';
                                    $call_history = str_replace('*', '', $call_history);

                                }else{

                                    $agent_name = $this->getBetween($text_body,"Hello","A customer just tried") ?? '';
                                    $agent_name = str_replace(',', '', $agent_name);
                                    $mobile = $this->getBetween($text_body,"CALLER NUMBER","Remember,") ?? '';
                                    $call_history = $this->getBetween($text_body,"follow up:","CALLER NUMBER") ?? '';
                                    $call_history = str_replace('*', '', $call_history);


                                }



                                $html_data= $html_data. "<br>";
                                $html_data= $html_data. "Agent: ".$agent_name;
                                $html_data= $html_data. "<br>";
                                $html_data= $html_data. "Mobile: ".$mobile;
                                $html_data= $html_data. "<br>";
                                $html_data= $html_data. "Message: ".$call_history;

                                // echo "<br>";
                                // echo "Agent: ".$agent_name;
                                // echo  "<br>";
                                // echo "Mobile: ".$mobile;
                                // echo "<br>";
                                // echo "Message: ".$call_history;

                                $trim_mobile = substr(trim($mobile), 1);
                                $lead_by_number  =  Lead::where('mobile', 'LIKE', "%{$trim_mobile}%")->orderBy('id', 'DESC')->first();

                                //$lead_by_number  =  Lead::where('mobile',trim($mobile))->orderBy('updated_at', 'desc')->first();
                                $call_history.='<br>(Dubizzle Call):: Agent Name:'.trim($agent_name).'   /  Mobile: '.trim($mobile).'<br>';

                                if($lead_by_number === null)
                                {
                                    $lead = Lead::firstOrNew(['mail_number' => $mail_number], ['mobile' => trim($mobile) ??'', 'subject' => $sub??'',

                                    ]);

                                    foreach ($all_users as $key) {
                                        $agent_fullname = $key['first_name'].' '.$key['last_name'];

                                        if(\Str::contains($agent_name,$agent_fullname))
                                        {
                                            $lead->agent_id =  $key['id'];

                                        }

                                    } // end foreach




                                    $lead->lead_status_id = 1;
                                    // $lead->lead_sub_status_id = 4;
                                 //   $lead->contact_source_id = 13;
                                    $lead->for = 0;
                                    $lead->save();
                                    $note = LeadNote::updateOrCreate(['lead_id' => $lead->id, 'note' => 'Auto Import>  ' . $call_history], ['lead_id' => $lead->id,

                                    ]);


                                }else
                                {
                                    $lead_update = Lead::where("id",$lead_by_number->id)->update(array('first_name' => 'DUPLICATE-'.$lead_by_number['first_name'], 'mail_number' => $mail_number));

                                    if($lead_update)
                                    {
                                        $note = LeadNote::updateOrCreate(['lead_id' => $lead_by_number->id, 'note' => 'Auto Import>  ' . $call_history], ['lead_id' => $lead_by_number->id,

                                        ]);
                                    }

                                }

                                $html_data= $html_data. "<br>-- Dubizzle phone Lead imported successfully --<br><br><br>";




                                // echo "<br>-- Dubizzle phone Lead imported successfully --<br><br><br>";

                            }

                        } // end Dubizzle If

                        if ($bayut)
                        {

                            if (\Str::contains($subject, 'Congratulations! You have received a new email lead!') || \Str::contains($subject,'New Lead! You have received a rental inquiry on Bayut') || \Str::contains($subject, 'Bayut Rental Inquiry') ||  \Str::contains($subject, 'Bayut Sale Inquiry') )
                            {
                                //\Str::contains($subject,'Bayut Agency Inquiry') || \Str::contains($subject, 'Bayut Rental Inquiry') ||
                                // if(false){
                                // print_r('Message ID: '.$message->getNumber().'<br><br><br>Subject: '.$message->getSubject().'____________: '.strstr($message->getBodyText(), '*:').'-------------------------------<br><br><br><br><br><br>');
                                //$original_body = $message->getBodyText();
                                //echo "<br>==============<br>";


                                // echo $message->getBodyText();
                                //  echo "<br>==============<br>";
                                //  print_r($message->getFrom());
                                //  echo "<br>--------------<br>";
                                //  print_r($message->getNumber());
                                //  echo "<br>--------------<br>";
                                //  print_r($original_body);
                                //  echo "<br>--------------<br>";


                                $original_body = $message->getBodyText();
                                $html_data= $html_data. "<br>** Featching Bayut Agency Inquiry / Bayut Rental Inquiry Emails<br>";
                                $html_data= $html_data. "------------------------------------------<br>";

                                // echo "<br>** Featching Bayut Agency Inquiry / Bayut Rental Inquiry Emails<br>";
                                // echo  "------------------------------------------<br>";

                                $html_body = new \Html2Text\Html2Text($message->getBodyHtml());
                                $text_body = $html_body->getText();

                                //print_r( $text_body);

                                $sub=  $subject.' <'. $from.'>';
                                $ref_number = $this->getBetween($text_body,"Reference:","Bayut ID:") ?? '';
                                $name = $this->getBetween($text_body,"Name:","Email:") ?? 'NA';
                                $email = $this->getBetween($text_body,"Email:","Phone:") ?? '';
                                $mobile = $this->getBetween($text_body,"Phone:","IP Location:") ?? '';
                                $message = $this->getBetween($text_body,"INQUIRY MESSAGE","INQUIRER DETAILS") ?? '';

                                $html_data= $html_data. "Ref_number: ".$ref_number;
                                $html_data= $html_data. "<br>";
                                $html_data= $html_data. "Name: ".$name;
                                $html_data= $html_data. "<br>";
                                $html_data= $html_data. "Email: ".$email;
                                $html_data= $html_data. "<br>";
                                $html_data= $html_data. "Mobile: ".$mobile;
                                $html_data= $html_data. "<br>";
                                $html_data= $html_data. "Message: ".$message;

                                /* echo  "Ref_number: ".$ref_number;
                                 echo "<br>";
                                 echo "Name: ".$name;
                                 echo "<br>";
                                 echo "Email: ".$email;
                                 echo "<br>";
                                 echo "Mobile: ".$mobile;
                                 echo "<br>";
                                 echo "Message: ".$message;

                                 exit();
                                 */

                                $trim_mobile = substr(trim($mobile), 1);
                                $lead_by_number  =  Lead::where('mobile', 'LIKE', "%{$trim_mobile}%")->orderBy('id', 'DESC')->first();



                                if (!empty(trim($ref_number)) || (trim($ref_number)!=''))
                                {
                                    //$ref_number = 'PKH-0001';
                                    $property = Property::where('ref_no', trim($ref_number))->get()
                                        ->first();
                                    // dd($property);
                                    if ($property)
                                    {

                                        //$lead_by_number  =  Lead::where('mobile',trim($mobile))->orderBy('updated_at', 'desc')->first();
                                        $message.='<br>(Bayut):: Name:'.trim($name).'   /  Email: '.trim($email).'   /  Mobile: '.trim($mobile).'/  Agent: '.$property->agent_id.'   /  Ref#: '.trim($ref_number).'<br>';



                                        if($lead_by_number === null)
                                        {
                                            $lead = Lead::firstOrNew(['mail_number' => $mail_number], ['first_name' => trim($name) ??'', 'email' => trim($email) ??'', 'mobile' => trim($mobile) ??'', 'message' => $message??'', 'subject' => $sub??'',

                                            ]);

                                            $lead->for = $property->for;
                                            $lead->agent_id = $property->agent_id;
                                            $lead->property_id = $property->id;
                                            $lead->lead_status_id = 1;
                                            // $lead->lead_sub_status_id = 4;

                                            if ($property->for == 1)
                                            {
                                                $lead->contact_type_id = 2;
                                            }
                                            else if ($property->for == 2)
                                            {
                                                $lead->contact_type_id = 1;
                                            }
                                            else if ($property->for == 3)
                                            {
                                                $lead->contact_type_id = 1;
                                            }
                                            else
                                            {
                                                $lead->contact_type_id = 10;
                                            }

                                           // $lead->contact_source_id = 45;
                                            $lead->save();
                                            $note = LeadNote::updateOrCreate(['lead_id' => $lead->id, 'note' => 'Auto Import>  ' . $message], ['lead_id' => $lead->id,

                                            ]);

                                        }else
                                        {
                                            $lead_update = Lead::where("id",$lead_by_number->id)->where('mail_number','<>',$mail_number)->update(array('first_name' => 'DUPLICATE-'.$lead_by_number['first_name'], 'mail_number' => $mail_number));

                                            if($lead_update)
                                            {
                                                $note = LeadNote::updateOrCreate(['lead_id' => $lead_by_number->id, 'note' => 'Auto Import>  ' . $message], ['lead_id' => $lead_by_number->id,

                                                ]);
                                            }

                                        }

                                        $html_data= $html_data. "<br>-- Bayut Inquiry Lead imported successfully --<br><br><br>";




                                        // echo  "<br>-- Bayut Inquiry Lead imported successfully --<br><br><br>";

                                        // $note = new \App\Models\LeadNote();
                                        // $note->note = 'Auto Import>  ' . $message;
                                        // $note->lead_id = $lead->id;
                                        // $note->save();



                                    }
                                    else
                                    {

                                        //$lead_by_number  =  Lead::where('mobile',trim($mobile))->orderBy('updated_at', 'desc')->first();
                                        $message.='<br>(Bayut):: Name:'.trim($name).'   /  Email: '.trim($email).'   /  Mobile: '.trim($mobile).'  /  Ref#: '.trim($ref_number).'<br>';

                                        if($lead_by_number === null)
                                        {
                                            $lead = Lead::firstOrNew(['mail_number' => $mail_number], ['first_name' => trim($name) ??'', 'email' => trim($email) ??'', 'mobile' => trim($mobile) ??'', 'message' => $message??'', 'subject' => $sub??'',

                                            ]);

                                            $lead->lead_status_id = 1;
                                            // $lead->lead_sub_status_id = 4;
                                           // $lead->contact_source_id = 45;
                                            $lead->save();
                                            $note = LeadNote::updateOrCreate(['lead_id' => $lead->id, 'note' => 'Auto Import>  ' . $message], ['lead_id' => $lead->id,

                                            ]);

                                        }else
                                        {
                                            $lead_update = Lead::where("id",$lead_by_number->id)->where('mail_number','<>',$mail_number)->update(array('first_name' => 'DUPLICATE-'.$lead_by_number['first_name'], 'mail_number' => $mail_number));

                                            if($lead_update)
                                            {
                                                $note = LeadNote::updateOrCreate(['lead_id' => $lead_by_number->id, 'note' => 'Auto Import>  ' . $message], ['lead_id' => $lead_by_number->id,

                                                ]);
                                            }

                                        }


                                        $html_data= $html_data. "<br>-- Bayut Inquiry Lead imported successfully --<br><br><br>";






                                        // echo  "<br>-- Bayut Inquiry Lead imported successfully --<br><br><br>";
                                    }

                                }else{

                                    // $lead_by_number  =  Lead::where('mobile',trim($mobile))->orderBy('updated_at', 'desc')->first();
                                    $message.='<br>(Bayut):: Name:'.trim($name).'   /  Email: '.trim($email).'   /  Mobile: '.trim($mobile).'   /  Ref#: '.trim($ref_number).'<br>';




                                    if($lead_by_number === null)
                                    {

                                        $lead = Lead::firstOrNew(['mail_number' => $mail_number], ['first_name' => trim($name) ??'', 'email' => trim($email) ??'', 'mobile' => trim($mobile) ??'', 'message' => $message??'', 'subject' => $sub??'',

                                        ]);
                                        $lead->lead_status_id = 1;
                                        // $lead->lead_sub_status_id = 4;
                                        $lead->for = 0;
                                      //  $lead->contact_source_id = 45;
                                        $lead->save();
                                        $note = LeadNote::updateOrCreate(['lead_id' => $lead->id, 'note' => 'Auto Import>  ' . $message], ['lead_id' => $lead->id,

                                        ]);

                                    }else
                                    {
                                        $lead_update = Lead::where("id",$lead_by_number->id)->where('mail_number','<>',$mail_number)->update(array('first_name' => 'DUPLICATE-'.$lead_by_number['first_name'], 'mail_number' => $mail_number));

                                        if($lead_update)
                                        {
                                            $note = LeadNote::updateOrCreate(['lead_id' => $lead_by_number->id, 'note' => 'Auto Import>  ' . $message], ['lead_id' => $lead_by_number->id,

                                            ]);
                                        }

                                    }


                                    $html_data= $html_data. "<br>--  Bayut Inquiry Lead imported successfully --<br><br><br>";





                                    // echo "<br>--  Bayut Inquiry Lead imported successfully --<br><br><br>";

                                }

                            }



                            if (\Str::contains($subject, 'Bayut.com Lead Notification: CALL Received'))
                            {


                                $original_body = $message->getBodyText();
                                $html_data= $html_data. "<br>** Featching Bayut.com Lead Notification: CALL Received Emails<br>";

                                $html_data= $html_data. "------------------------------------------<br>";

                                // echo "<br>** Featching Bayut.com Lead Notification: CALL Received Emails<br>";

                                // echo  "------------------------------------------<br>";

                                $html_body = new \Html2Text\Html2Text($message->getBodyHtml());
                                $text_body = $html_body->getText();

                                //print_r( $text_body);
                                $sub=  $subject.' <'. $from.'>';

                                $mobile = $this->getBetween($text_body,"[tel:","]") ?? '';
                                $agent_name = $this->getBetween($text_body,"Received By:","Call Time:") ?? '';
                                $call_history = $this->getBetween($text_body,"Call Details","Best Regards") ?? '';


                                $html_data= $html_data. "Agent: ".$agent_name;
                                $html_data= $html_data. "<br>";

                                $html_data= $html_data. "Mobile: ".$mobile;
                                $html_data= $html_data. "<br>";
                                $html_data= $html_data. "Call history: ".$call_history;


                                // echo "Agent: ".$agent_name;
                                // echo "<br>";

                                // echo "Mobile: ".$mobile;
                                // echo "<br>";
                                // echo "Call history: ".$call_history;

                                //$lead_by_number  =  Lead::where('mobile',trim($mobile))->orderBy('updated_at', 'desc')->first();
                                $call_history.='<br>(Bayut Call):: Agent Name:'.trim($agent_name).'   /  Mobile: '.trim($mobile).'<br>';

                                $trim_mobile = substr(trim($mobile), 1);
                                $lead_by_number  =  Lead::where('mobile', 'LIKE', "%{$trim_mobile}%")->orderBy('id', 'DESC')->first();

                                if($lead_by_number === null)
                                {

                                    $lead = Lead::firstOrNew(['mail_number' => $mail_number], ['mobile' => trim($mobile) ??'', 'subject' => $sub??'',

                                    ]);

                                    foreach ($all_users as $key) {
                                        $agent_fullname = $key['first_name'].' '.$key['last_name'];

                                        if(\Str::contains($agent_name,$agent_fullname))
                                        {
                                            $lead->agent_id =  $key['id'];

                                        }

                                    } // end foreach



                                    $lead->lead_status_id = 1;
                                    // $lead->lead_sub_status_id = 4;
                                  //  $lead->contact_source_id = 45;
                                    $lead->for = 0;
                                    $lead->save();
                                    $note = LeadNote::updateOrCreate(['lead_id' => $lead->id, 'note' => 'Auto Import>  ' . $call_history], ['lead_id' => $lead->id,

                                    ]);

                                }else
                                {
                                    $lead_update = Lead::where("id",$lead_by_number->id)->update(array('first_name' => 'DUPLICATE-'.$lead_by_number['first_name'], 'mail_number' => $mail_number));

                                    if($lead_update)
                                    {
                                        $note = LeadNote::updateOrCreate(['lead_id' => $lead_by_number->id, 'note' => 'Auto Import>  ' . $call_history], ['lead_id' => $lead_by_number->id,

                                        ]);
                                    }

                                }


                                $html_data= $html_data. "<br>-- Bayut.com Lead Notification: CALL Received Lead imported successfully --<br><br><br>";








                                // echo "<br>-- Bayut.com Lead Notification: CALL Received Lead imported successfully --<br><br><br>";

                            }

                            if (\Str::contains($subject, 'Bayut.com Lead Notification: CALL Missed'))
                            {

                                $original_body = $message->getBodyText();
                                $html_data= $html_data. "<br>** Featching Bayut.com Lead Notification: CALL Missed Emails<br>";

                                $html_data= $html_data. "------------------------------------------<br>";

                                // echo "<br>** Featching Bayut.com Lead Notification: CALL Missed Emails<br>";

                                // echo "------------------------------------------<br>";


                                $html_body = new \Html2Text\Html2Text($message->getBodyHtml());
                                $text_body = $html_body->getText();

                                // print_r( $text_body);


                                $mobile = $this->getBetween($text_body,"[tel:","]") ?? '';
                                $agent_name = $this->getBetween($text_body,"Missed By:","Call Time:") ?? '';
                                $call_history = $this->getBetween($text_body,"Call Details","Best Regards") ?? '';


                                $html_data= $html_data. "Agent: ".$agent_name;
                                $html_data= $html_data. "<br>";

                                $html_data= $html_data. "Mobile: ".$mobile;
                                $html_data= $html_data. "<br>";
                                $html_data= $html_data. "Call history: ".$call_history;


                                /*echo "Agent: ".$agent_name;
                                echo "<br>";

                                echo "Mobile: ".$mobile;
                                echo "<br>";
                                echo "Call history: ".$call_history;
                                */



                                $sub=  $subject.' <'. $from.'>';


                                // $lead_by_number  =  Lead::where('mobile',trim($mobile))->orderBy('updated_at', 'desc')->first();
                                $call_history.='<br>(Bayut Call):: Agent Name:'.trim($agent_name).'   /  Mobile: '.trim($mobile).'<br>';

                                $trim_mobile = substr(trim($mobile), 1);
                                $lead_by_number  =  Lead::where('mobile', 'LIKE', "%{$trim_mobile}%")->orderBy('id', 'DESC')->first();

                                if($lead_by_number === null)
                                {


                                    $lead = Lead::firstOrNew(['mail_number' => $mail_number], ['mobile' => trim($mobile) ??'', 'subject' => $sub??'',

                                    ]);

                                    foreach ($all_users as $key) {
                                        $agent_fullname = $key['first_name'].' '.$key['last_name'];

                                        if(\Str::contains($agent_name,$agent_fullname))
                                        {
                                            $lead->agent_id =  $key['id'];

                                        }

                                    } // end foreach



                                    $lead->lead_status_id = 1;
                                    // $lead->lead_sub_status_id = 4;
                                    $lead->for = 0;
                                  //  $lead->contact_source_id = 45;
                                    $lead->save();
                                    $note = LeadNote::updateOrCreate(['lead_id' => $lead->id, 'note' => 'Auto Import>  ' . $call_history], ['lead_id' => $lead->id,

                                    ]);

                                }else
                                {
                                    $lead_update = Lead::where("id",$lead_by_number->id)->update(array('first_name' => 'DUPLICATE-'.$lead_by_number['first_name'], 'mail_number' => $mail_number));

                                    if($lead_update)
                                    {
                                        $note = LeadNote::updateOrCreate(['lead_id' => $lead_by_number->id, 'note' => 'Auto Import>  ' . $call_history], ['lead_id' => $lead_by_number->id,

                                        ]);
                                    }

                                }

                                $html_data= $html_data. "<br>-- Bayut.com Lead Notification: CALL Missed Lead imported successfully --<br><br><br>";







                                // echo "<br>-- Bayut.com Lead Notification: CALL Missed Lead imported successfully --<br><br><br>";


                            }

                        } // end bayut If


                        if ($miraoasis)
                        {

                            if (\Str::contains($subject, 'Mira Oasis Landing Page Enquiry') && $from =='hishmat@pkproperties.ae')
                            {

                                $original_body = $message->getBodyText();
                                $html_data= $html_data. "<br>** Featching Mira Oasis Landing Page Emails<br>";
                                $html_data= $html_data. "------------------------------------------<br>";


                                // echo "<br>** Featching Mira Oasis Landing Page Emails<br>";
                                // echo "------------------------------------------<br>";

                                $html_body = new \Html2Text\Html2Text($message->getBodyHtml());
                                $text_body = $html_body->getText();

                                //print_r( $text_body) ;
                                $sub=  $subject.' <'. $from.'>';
                                $name = $this->getBetween($text_body,"NAME","EMAIL") ?? 'NA';
                                $email = $this->getBetween($text_body,"EMAIL","PHONE") ?? '';
                                $mobile = $this->getBetween($text_body,"PHONE","MESSAGE") ?? '';
                                $message = $this->getBetween($text_body,"MESSAGE","Sent from") ?? '';
                                $html_data= $html_data. "Name: ".$name;
                                $html_data= $html_data. "<br>";
                                $html_data= $html_data. "Email: ".$email;
                                $html_data= $html_data. "<br>";
                                $html_data= $html_data. "Mobile: ".$mobile;
                                $html_data= $html_data. "<br>";
                                $html_data= $html_data. "Message: ".$message;


                                // echo "Name: ".$name;
                                // echo  "<br>";
                                // echo "Email: ".$email;
                                // echo "<br>";
                                // echo "Mobile: ".$mobile;
                                // echo "<br>";
                                // echo "Message: ".$message;


                                if($email)
                                {

                                    // $lead_by_number  =  Lead::where('mobile',trim($mobile))->orderBy('updated_at', 'desc')->first();
                                    $message.='<br>(Mira Oasis LP):: Name:'.trim($name).'   /  Email: '.trim($email).'   /  Mobile: '.trim($mobile).'<br>';


                                    $trim_mobile = substr(trim($mobile), 1);
                                    $lead_by_number  =  Lead::where('mobile', 'LIKE', "%{$trim_mobile}%")->orderBy('id', 'DESC')->first();

                                    if($lead_by_number === null)
                                    {

                                        $lead = Lead::firstOrNew(['mail_number' => $mail_number], ['first_name' => trim($name) ??'', 'email' => trim($email) ??'', 'mobile' => trim($mobile) ??'', 'message' => $message??'', 'subject' => $sub??'',

                                        ]);

                                        $lead->lead_status_id = 1;
                                        // $lead->lead_sub_status_id = 4;
                                        $lead->agent_id = 576; // Edgars
                                        $lead->for = 0;
                                      //  $lead->contact_source_id = 47; // Mira Oasis Landing Page
                                        $lead->save();
                                        $note = LeadNote::updateOrCreate(['lead_id' => $lead->id, 'note' => 'Auto Import>  ' . $message], ['lead_id' => $lead->id,

                                        ]);

                                    }else
                                    {
                                        $lead_update = Lead::where("id",$lead_by_number->id)->where('mail_number','<>',$mail_number)->update(array('first_name' => 'DUPLICATE-'.$lead_by_number['first_name'], 'mail_number' => $mail_number));

                                        if($lead_update)
                                        {
                                            $note = LeadNote::updateOrCreate(['lead_id' => $lead_by_number->id, 'note' => 'Auto Import>  ' . $message], ['lead_id' => $lead_by_number->id,

                                            ]);
                                        }

                                    }


                                    $html_data= $html_data. "<br>-- Mira Oasis Landing Page  Lead imported successfully --<br><br><br>";


                                    // echo "<br>-- Mira Oasis Landing Page  Lead imported successfully --<br><br><br>";


                                }

                            }



                        } // end Mira Oasis If

                    }
                    $countr++;

                    //print_r('<br>Agent: '.strstr($message->getBodyText(), '*Agent*').'-------------------------------<br>');
                    //print_r('<br>Agent: '.strstr($message->getBodyText(), '*Agent*').'-------------------------------<br>');
                }// end if mail number is greater than last
                else{
                    // $response = ['message' => 'No New Leads found to import', 'status' => true, 'data' => $html_data, ];
                    // return Response()->json($response);

                }

            } // end foreach


            $response = ['message' => $countr.' Leads have been imported successfully', 'status' => true, 'data' => '', ];
            return Response()->json($response);


            // exit();
        // }catch(\Exception $e){
        //     $response = [
        //         'message'               => $e->getMessage(),
        //         'status'                => false,
        //         'data'                => $html_data,
        //     ];

        //     return Response()->json($response);

        // } // end exception

    } // end function

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
            return substr($firstSubStr, 0, $endCharCount);
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
