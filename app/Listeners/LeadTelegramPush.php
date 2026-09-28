<?php

namespace App\Listeners;

use App\Events\LeadCreated;
use App\Models\Lead;
use App\Models\User;
use App\Models\LeadNote;
use App\Models\Shortlink;
use App\Models\Property;
use App\Models\CallLog;
use App\Traits\TelegramTrait;
use Crypt;
use Log;
class LeadTelegramPush
{
    use TelegramTrait;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\LeadCreated  $event
     * @return void
     */
    public function handle(LeadCreated $event)
    {
        $lead = $event->lead;
        $isDuplicate = $lead->isDuplicate ?? false;
        $lead = Lead::with('leadSource', 'property', 'leadStatus', 'leadSubStatus', 'agent')->find($lead->id);

        if($lead->agent_id && $lead->mobile){
            $callLogs = CallLog::whereRaw("REPLACE(client_number, ' ', '') LIKE ?", ['%' . substr($lead->mobile, -8     ) . '%'])
                    ->where('user_id', $lead->agent_id)
                    ->get();
            // Attach call logs to the lead
            $lead->calls()->attach($callLogs);
        }

        $chatid = $lead?->agent?->telegram_chatid ?? null;
        $chat_ids = [$chatid];
        if($lead?->agent?->region_id == 2){
            // All the leads for Abu Dhabi region will go to only Jalal Kinana.
            $user = User::where('email','j.kinana@axcapital.ae')->first();

            $lead->agent_id = $user->id;
            $lead->save();

            LeadNote::create([
                    'lead_id' => $lead->id,
                    'note' => 'Lead has been assigned to '. $user?->full_name,
                    'status' => 1,
                ]);

            $chat_ids = [$user->telegram_chatid];
        }

        $lead_source = $lead->leadSource?->name ?? '';
        $lead_medium = $lead->leadMedium?->name ?? '';
        $lead_status = $lead->leadStatus?->name ?? '';
        $leadSubStatus = $lead->leadSubStatus?->name ?? '';
        $property_ref_no = $lead->property?->ref_no ?? '';
        //dd($property_ref_no);
        $name = $lead->first_name . ' ' . $lead->last_name;
        $name = str_replace(
            [".", ",", "<", ">", "!"], // 1. special chars to remove
            " ", // 2. replacement for the chars
            $name // 3. the original string
        );
        $whatsAppReply = "";
        $msg = str_replace("Reference"," %0a%0aReference",$lead->message);
        $msg = str_replace("Link:"," %0aLink:",$msg);
        if($lead->message){
            $whatsAppReply = "Hi, This is ".$lead?->agent?->name." from AX CAPITAL Dubai. %0a%0aI am responding to your inquiry below.%0a%0a". $msg ." "."%0a%0a Is now a good time to chat?";
        }else{
            $whatsAppReply = "Hi, This is ".$lead?->agent?->name." from AX CAPITAL Dubai.";
        }
        $duplicateTitle = "";
        if($isDuplicate){
            $duplicateTitle = "<b>Duplicate-</b>";
        }

        $message = "\n<b> AX Secondary CRM</b>\n\n".$duplicateTitle."#".$lead->id." <i> ".$lead_source." Lead has been recorded. </i>\n\n";
        $url = null;
        if($lead->id && $lead->agent_id){
            $lead_hash = Crypt::encryptString($lead->id);
            $user_id_hash = Crypt::encryptString($lead?->agent_id);

            $url = route('api.thirdparty.lead_update')."?lead=".$lead_hash."&user=".$user_id_hash;
            // $slug2 =rand(0,9999).rand(0,9999).rand(0,9999);
            // $shortlink2 = ShortLink::create([
            //     'source_id' => $lead->source_id,
            //     'slug' => $slug2,
            //     'message' => 'Click here to update lead',
            //     'link' => $url,
            //     'short_link' => env('APP_URL').'/ax/'.$slug2,
            // ]);
        }

        $slug = generateAlpha(15).rand(0,9999);

        $number = $lead->whatsapp ==null || $lead->whatsapp =='' ? $lead->mobile : $lead->whatsapp;
        $number = str_replace(' ', '', $number);
        $number = validatePhoneNumber($number);
        $shorturl =  $lead->pf_tracking_link  ?? 'https://wa.me/' . $number;

        $shortlink = Shortlink::create([
            'source_id' => $lead->source_id,
            'slug' => $slug,
            'message' => $whatsAppReply,
            'link' => $shorturl,
            'short_link' =>  route('public.linkOpener',$slug),
            'lead_id'=>$lead->id
        ]);

        // $cat = $lead->property->propertyCategory->title ?? '';
        // $unit = $lead->property?->unit_no ?? '';
        // dd($cat,$unit);

        // $property_details = $lead?->property?->propertyCategory?->title.' '. $lead?->property?->unit_no. ', '.$lead?->property?->location;
        // dd($property_details,$lead->property);
        ($name!=" " ? $message .= "<b> Name: </b> " . $name : null);
        ($lead->email ? $message .= "\n<b> Email: </b> " . $lead->email : null);
        $message .= "\n<b> Mobile: </b> " . $lead->mobile;
        $message .= "\n<b> WhatsApp: </b><a href='".$shortlink->short_link."'>WhatsApp now</a>";
        $message .= "\n\n<b> Source: </b> " . $lead_source;
        $message .= "\n<b> Medium: </b> " . $lead_medium;
        ($lead_status ? $message .= "\n<b> Status: </b> " . $lead_status : null);
        ($property_ref_no ? $message .= "\n\n<b> Property ref: </b> <a href='" . route('admin.listing.index') . "?ref_number=" . $property_ref_no . "'>" . $property_ref_no . "</a>" : null);

        ($property_ref_no ? $message .=  "\n<b> Type: </b> " . $lead?->property?->propertyCategory?->title: null);
        ($property_ref_no ? $message .=  "\n<b> Unit: </b> " . $lead?->property?->unit_no: null);
        ($property_ref_no ? $message .=  "\n<b> Location: </b> " . $lead?->property?->location: null);
        //($lead->property ? $message .= "\n<b> Property Details: </b> Unit #: " . $lead?->property?->unit_no." ; Location: ".$lead?->property?->location."; Price: AED ". $lead?->property?->price?->price : null);
        ($property_ref_no ? $message .= "\n<b> Preview URL: </b> <a href='" . route('admin.listing.properties.view', $property_ref_no) . "'> " . $property_ref_no . "</a>" : null);

       // ($lead_status ? $message .= "\n<b> Status: </b> " . $lead_status : null);

        $environment = \App::environment();
        if (($lead->status && $environment == "production") || in_array("6085218419", $chat_ids)) {
            $this->sendTelegramMessageHtml($message, $chat_ids, $url,'Update Lead','lead_bot');
            $this->sendLeadToWebhookV2($lead);

        }



    }




    private function sendLeadToWebhookV2($leadData)
    {
        try {
            // dd($leadData['first_name']);
            // $webhookUrl = 'http://127.0.0.1:8000/api/webhook/get-lead';
            $webhookUrl = 'https://api.axc.ae/webhook/b7698ece-08e0-4275-8636-b90adfec5f59';
            $lead = Lead::find($leadData['id']);
            $source_name = null;
            $portal_link = null;
            $property_details = null;

            if ($lead->agent_id == 4) {
                $property = Property::find($lead->property_id);
                $location_details = '';
                if ($property) {
                    if ($property->community_id != null) {
                        $community = \App\Models\Community::find($property->community_id);
                        $location_details = $location_details . $community->name ?? '';
                    }

                    if ($property->sub_community_id != null) {
                        $sub_community = \App\Models\SubCommunity::find($property->sub_community_id);
                        $location_details = $location_details . ', ' . $sub_community->name ?? '';
                    }
                    if ($property->community_property_id != null) {
                        $community_property = \App\Models\CommunityProperty::find($property->community_property_id);
                        $location_details = $location_details . ', ' . $community_property->name ?? '';
                    }




                if ($lead['source_id'] != null) {

                    if ($lead['source_id'] == 4) {
                        $source_name = 12;
                        $portalLink = \App\Models\PropertyMeta::where('property_id',$property->id)->where('meta_key','bayut_url')->first();
                        $portal_link = $portalLink?->meta_value??null;

                    } else if ($lead['source_id'] == 13) {
                        $source_name = 24;
                        $portalLink = \App\Models\PropertyMeta::where('property_id',$property->id)->where('meta_key','dubizzle_url')->first();
                        $portal_link = $portalLink?->meta_value??null;

                    } else if ($lead['source_id'] == 31) {
                        $source_name = 15;
                        $portalLink = \App\Models\PropertyMeta::where('meta_key','pf_url')->where('property_id',$property->id)->first();
                        $portal_link = $portalLink?->meta_value??null;

                    } else {
                        $source_name = '';
                    }

                }
                $original_bath = \App\Models\Bath::find($property->bath_id);
                $original_bed = \App\Models\Bed::find($property->bed_id);
                if ($original_bath) {
                    $bath = ', ' . $original_bath->name;
                } else {
                    $bath = '';
                }
                if ($original_bed) {
                    $bed = ', ' . $original_bed->name;
                } else {
                    $bed = '';
                }
                $property_details = $property->unit_no . $bath . $bed . ', ' . $property?->propertyCategory?->title . ', ' . $location_details;

            }

                $original_source = \App\Models\Source::find($lead['source_id']);
                $original_medium = \App\Models\Medium::find($lead['lead_medium_id']);


                $short_link = Shortlink::where('lead_id', $leadData['id'])->first();



                $arry = [
                    'id' => $lead->id,
                    'first_name' => $lead['first_name'] ?? null,
                    'last_name' => $lead['last_name'] ?? null,
                    'email' => $lead['email'] ?? null,
                    'mobile' => $lead['mobile'] ?? null,
                    'medium' => $original_medium?->name ?? null,
                    'message' => $lead['message'] ?? null,
                    'source' => $original_source?->name,
                    'listing_ref_no' => $property?->ref_no ?? null,
                    'listing_details' => $property_details ?? null,
                    'whatsapp_url'=>$short_link->short_link ?? null,
                    'portal_url'=>$portal_link??null,
                ];
                $postData = json_encode(['fields' => $arry]);

                $ch = curl_init();
                curl_setopt_array($ch, [
                    CURLOPT_URL => $webhookUrl,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => $postData,
                    CURLOPT_HTTPHEADER => [
                        'Content-Type: application/json',
                        'Content-Length: ' . strlen($postData),
                    ],
                    CURLOPT_RETURNTRANSFER => true,
                ]);
                $response = curl_exec($ch);

                if (curl_errno($ch)) {
                    Log::channel('discord')->error("ERROR IN SENDING DATA TO BITRIX.", [
                        'lead' => $arry,
                        'response' => $response,
                    ]);
                }

                LeadNote::create([
                    'lead_id' => $lead->id,
                    'note' => 'Lead has been transferred to Bitrix',
                    // 'created_by_id' => $lead->agent_id,
                    'status' => 1,
                ]);

                $lead->lead_status_id = 5;
                $lead->save();


                Log::channel('discord')->error("Lead Data Sent to BITRIX WEBHOOK ", [
                    'lead' => $arry,
                    'response' => $response,
                ]);

                curl_close($ch);
            }
        } catch (\Throwable $th) {
            Log::channel('discord')->error("Error In Sending Data To Bitrix.", [
                'ERROR' => $th,
            ]);
        }
    }
}
