<?php

namespace App\Http\Controllers\Dubizzle;


use App\Http\Controllers\Controller;
use App\Models\CallLog;
use App\Models\Community;
use App\Models\CommunityProperty;
use App\Models\Lead;
use App\Events\PortalsLeadsNotification;
use App\Models\LeadCriteria;
use App\Models\LeadCriteriaLocation;
use App\Models\LeadMeta;
use App\Models\LeadNote;
use App\Models\Medium;
use App\Models\Property;
use App\Models\Source;
use App\Models\SubCommunity;
use App\Models\User;
use App\Traits\FileTrait;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DubizzleLeadImportController extends Controller
{
    use FileTrait;
        // $iterators = ['call_logs', 'leads', 'phone_views', 'sms_clicks', 'whatsapp_views', 'whatsapp_leads'];

    public function handle()
    {

        // $iterators = ['leads'];
        $iterators = ['call_logs', 'leads'];

        $min = 130;
        $timestamp = date('Y-m-d H:i:s', strtotime('-48 hours'));

        foreach ($iterators as $iterator) {
            $endpoint = 'https://dubizzle.com/profolio/api-v7/stats/website-client-leads?type=' . $iterator . '&timestamp=' . $timestamp;
            $request = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('BAYUT_AUTHORIZATION'),
            ])->get($endpoint);

            $response = $request->body();
            //check if status is 403
            if($request->status() === 403) {
                Log::error('Dubizzle Lead Import Controller: Access denied for ' . $iterator);
               print_r($response);
            }
            $encodedResponse = json_decode($response, true);
            if(gettype($encodedResponse) != 'array'){
                Log::info('Dubizzle Lead Import Controller found : ' . $iterator . ' lead count: 0');
              //  continue;
            }else{
                Log::info('Dubizzle Lead Import Controller found : ' . $iterator . ' lead count: ' . count($encodedResponse));
            }


            switch ($iterator) {
                case 'call_logs':
                    // info('Fetching Call Logs');
                    $this->handleCallLogs($encodedResponse);
                    break;
                case 'leads':
                    // info('Fetching Email Leads');
                    $this->handleLeads($encodedResponse);
                    break;
                case 'phone_views':
                    // info('Fetching Phone Views');
                    $this->handlePhoneViews($encodedResponse);
                    break;
                case 'sms_clicks':
                    // info('Fetching SMS Clicks');
                    $this->handleSmsClicks($encodedResponse);
                    break;
                case 'whatsapp_views':
                    // info('Fetching Whatsapp Views');
                    $this->handleWhatsappViews($encodedResponse);
                    break;
                case 'whatsapp_leads':
                    // info('Fetching Whatsapp Leads');
                    $this->handleWhatsappLeads($encodedResponse);
                    break;
            }

        }

    }

    public function handleAbuDhabi()
    {
        Log::channel('urlLogs')->info('Dubizzle Leads For Abu Dhabi.');

        $iterators = ['call_logs', 'leads'];

        $min = 30;
        $timestamp = date('Y-m-d H:i:s', strtotime('-' . $min . ' minutes'));

        foreach ($iterators as $iterator) {
            $endpoint = 'https://dubizzle.com/profolio/api-v7/stats/website-client-leads?type=' . $iterator . '&timestamp=' . $timestamp;
            $request = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('BAYUT_AUTHORIZATION_ABU_DHABI'),
            ])->get($endpoint);

            $response = $request->body();
            $encodedResponse = json_decode($response, true);
            // dd($encodedResponse);
            // curl_close($curl);
            switch ($iterator) {
                case 'call_logs':
                    // info('Fetching Call Logs');
                    $this->handleCallLogs($encodedResponse);
                    break;
                case 'leads':
                    // info('Fetching Email Leads');
                    $this->handleLeads($encodedResponse);
                    break;
                case 'phone_views':
                    // info('Fetching Phone Views');
                    $this->handlePhoneViews($encodedResponse);
                    break;
                case 'sms_clicks':
                    // info('Fetching SMS Clicks');
                    $this->handleSmsClicks($encodedResponse);
                    break;
                case 'whatsapp_views':
                    // info('Fetching Whatsapp Views');
                    $this->handleWhatsappViews($encodedResponse);
                    break;
                case 'whatsapp_leads':
                    // info('Fetching Whatsapp Leads');
                    $this->handleWhatsappLeads($encodedResponse);
                    break;
            }

        }

    }

    public function handleCommercial()
    {
        Log::channel('urlLogs')->info(' Dubizzle Leads For Commercial.');

        $iterators = ['call_logs', 'leads'];

        $min = 30;
        $timestamp = date('Y-m-d H:i:s', strtotime('-' . $min . ' minutes'));

        foreach ($iterators as $iterator) {
            $endpoint = 'https://dubizzle.com/profolio/api-v7/stats/website-client-leads?type=' . $iterator . '&timestamp=' . $timestamp;

            $request = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('BAYUT_AUTHORIZATION_COMMERCIAL'),
            ])
                ->get($endpoint);

            $response = $request->body();
            $encodedResponse = json_decode($response, true);
            // curl_close($curl);
            switch ($iterator) {
                case 'call_logs':
                    // info('Fetching Call Logs');
                    $this->handleCallLogs($encodedResponse);
                    break;
                case 'leads':
                    // info('Fetching Email Leads');
                    $this->handleLeads($encodedResponse);
                    break;
                case 'phone_views':
                    // info('Fetching Phone Views');
                    $this->handlePhoneViews($encodedResponse);
                    break;
                case 'sms_clicks':
                    // info('Fetching SMS Clicks');
                    $this->handleSmsClicks($encodedResponse);
                    break;
                case 'whatsapp_views':
                    // info('Fetching Whatsapp Views');
                    $this->handleWhatsappViews($encodedResponse);
                    break;
                case 'whatsapp_leads':
                    // info('Fetching Whatsapp Leads');
                    $this->handleWhatsappLeads($encodedResponse);
                    break;
            }

        }

    }

    private function handleCallLogs($response)
    {

       try {
            $counter = 0;
            Log::info("Dubizzle Call Logs Fetched Successfully. Found " . count($response ?? []) . " records.");
            foreach ($response ?? [] as $log) {

                $call_log_id = $log['call_log_id'] ?? null;
                $object_type_id = $log['object_type_id'] ?? null;
                $object_id = $log['object_id'] ?? null;
                $stats_ip_id = $log['stats_ip_id'] ?? null;
                $date = $log['date'] ?? null;
                $time = $log['time'] ?? null;
                $role_id = $log['role_id'] ?? null;
                $caller_number = $log['caller_number'] ?? null;
                $proxy_number = $log['proxy_number'] ?? null;
                $receiver_numbers_list = $log['receiver_numbers_list'] ?? null;
                $receiver_number = $log['receiver_number'] ?? null;
                $call_status = $log['call_status'] ?? null;
                $call_total_duration = $log['call_total_duration'] ?? null;
                $call_connected_duration = $log['call_connected_duration'] ?? null;
                $call_time = $log['call_time'] ?? null;
                $call_pickup_time = $log['call_pickup_time'] ?? null;
                $call_recordingurl = $log['call_recordingurl'] ?? null;
                $caller_location = $log['caller_location'] ?? null;
                $source = $log['source'] ?? null;
                $is_object_fecthed = $log['is_object_fecthed'] ?? null;
                $reference_log_id = $log['reference_log_id'] ?? null;
                $call_type = $log['call_type'] ?? null;
                $call_status = $log['call_status'] ?? null;

                $callStartTime = Carbon::parse($call_time);
                $medium = Medium::where("name", "Call")->pluck("id")->first();

                $listingReference = $log['listing_reference'] ?? null;

                if ($call_pickup_time != 'None') {
                    $callPickupTime = Carbon::parse($call_pickup_time);
                    $durationParts = explode(':', $call_total_duration);
                    // seconds
                    // dd($durationParts);
                    $totalSeconds = $durationParts[0] * 3600 + $durationParts[1] * 60 + $durationParts[2];
                    $callEndTime = $callStartTime->copy()->addSeconds($totalSeconds);
                }

                //$receiver_numbers_list = explode('#', $receiver_numbers_list);
                // $agent = User::whereRaw("REPLACE(`mobile`, ' ', '') LIKE ?", ['%' . trimSpaces(validatePhoneNumber($receiver_numbers_list[0])) . '%'])->first();

                $phoneNumber = trimSpaces(validatePhoneNumber($receiver_number));
                $lastSevenDigits = substr($phoneNumber, -7);
                $agent = User::whereRaw("RIGHT(REPLACE(`mobile`, ' ', ''), 7) = ?", [$lastSevenDigits])->first();
                $find_meta = LeadMeta::where('meta_key', 'call_log_id')->where('meta_value', $call_log_id)->first();

                if (!$find_meta) {
                    DB::beginTransaction();
                    $old_lead = null;
                    if($agent){
                        $old_lead = Lead::where('mobile', validatePhoneNumber($caller_number))->where('agent_id', $agent->id)->where('source_id', 13)->where('type_id', 11)->first();
                    }else{
                        $old_lead = Lead::where('mobile', validatePhoneNumber($caller_number))->where('source_id', 13)->where('type_id', 11)->first();
                    }
                    $duplicate = '';
                    if ($old_lead) {
                        $duplicate = 'Duplicate-';
                    }
                    if ($listingReference) {
                        $property = Property::where('ref_no', $listingReference)->first();
                        if ($property) {
                           $agent = User::where('id', $property?->agent_id)->first();
                        }
                    }

                    $lead = new Lead();
                    $lead->first_name = $duplicate . 'New';
                    $lead->agent_id = $agent?->id ?? null;
                    $lead->mobile = validatePhoneNumber($caller_number);
                    $lead->source_id = 13;
                    $lead->lead_status_id = $call_status == 'connected' ? 5 : 1;
                    $lead->lead_medium_id = 2;
                    $lead->type_id = 11;
                    $lead->portal_created_at = $log['date'] . ' '. $log['time'];
                    $lead->save();

                    if ($listingReference) {
                        $criteria = null;
                        $community_property = null;
                        $region = null;
                        $community = null;
                        $sub_community = null;
                        $for = $property->for ?? '';
                        $property = $for !='' ? Property::where('ref_no', $listingReference)->first() : null;
                        $lead_type_id = $for !='' ? $this->getPropertyLeadTypeId($for) : null;
                        $source = Source::where('name', 'LIKE', '%Dubizzle%')->first();
                        $medium = Medium::where('name', 'LIKE', "%" . "Call%")->first();

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

                            $lead->property_id = $property->id;
                            $lead->agent_id = $property?->agent_id ?? $lead->agent_id;
                            $lead->type_id = $lead_type_id;
                            $lead->for = [$property->for];
                            $lead->save();
                        }
                    }
                    $leadnote = new LeadNote();
                    $leadnote->lead_id = $lead?->id;
                    $leadnote->note = 'Auto Import> Dubizzle.com Lead Notification from Dubizzle Fetch Lead Controller: CALL ' . ucfirst($call_status);
                    $leadnote->save();

                    $call_log = new CallLog();
                    $call_log->lead_id = $lead->id;
                    $call_log->client_number = validatePhoneNumber($caller_number);
                    $call_log->call_type = 'INCOMING';
                    $call_log->user_id = $agent?->id ?? null;

                    $call_log->call_started_at = $call_time;

                    if ($call_status == 'connected') {
                        $call_log->call_answered_at = $call_pickup_time != 'None' ? $call_pickup_time : null;
                        $call_log->call_ended_at = $callEndTime->format('Y-m-d H:i:s') ?? null;
                        $call_log->call_duration = $totalSeconds ?? null;
                        $call_log->call_status = $call_status ?? null;
                        if ($call_recordingurl) {
                            $new_url = $this->uploadDocumentFromUrl(url: $call_recordingurl, path: "callLogs");
                            $call_log->call_recording_url = $new_url ?? null;
                        }
                    }

                    $call_log->json_dump = json_encode($log);
                    $call_log->save();

                    if ($lead) {
                        $call_log->leads()->sync($lead);
                    }

                    $call_log_lead_meta = new LeadMeta();
                    $call_log_lead_meta->lead_id = $lead?->id;
                    $call_log_lead_meta->meta_key = 'call_log_id';
                    $call_log_lead_meta->meta_value = $call_log_id;
                    $call_log_lead_meta->save();
                    DB::commit();
                    $counter++;
                    if (env('APP_ENV') == 'production') {
                        event(new \App\Events\LeadCreated($lead));
                    }

                    if ($agent->id) {
                        $user = User::where('id', $agent->id)->first();
                        if ($user) {
                            event(new PortalsLeadsNotification("Lead", $lead, 'received', $user, $user, 1));
                        }
                    }

                }

            }

        info($counter . ' Dubizzle Call Leads Fetched Successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::channel('discord')->info('Dubizzle Leads Fetched Failed', [
                // 'call_log_id' => $call_log_id,
                // 'log' => $log,
                'th' => $th,
                'line' => $th->getLine(),
                'message' => $th->getMessage(),
                'Controller' => 'DubizzleLeadImportController',
            ]);

        }
        Log::channel('urlLogs')->info($counter . ' Dubizzle Call Leads Fetched Successfully.');

    }

    private function handleLeads($response)
    {
        //check if response is empty
        if (empty($response)) {
            Log::channel('urlLogs')->info('No Dubizzle Leads Found. DubizzleLeadImportController@handleLeads');
            return;
        }
        $counter = 0;

 Log::info("Dubizzle email leads fetched successfully. Found " . count($response ?? []) . " records.");

        foreach ($response ?? [] as $lead) {
            if(!empty($lead)){
            $property_Id = $lead['property_id'];
            $property_reference = $lead['property_reference'];
            $current_type = $lead['current_type'];
            $date_time = $lead['date_time'];
            $message = $lead['message'];
            $client_name = $lead['client_name'];
            $client_email = $lead['client_email'];
            $client_phone = $lead['client_phone'];

            // find meta for lead
            $find_meta = LeadMeta::where('meta_key', 'dubizzle_lead_id')->where('meta_value', str_replace(' ', '', $date_time) . '-' . $property_reference . '-' . $client_phone)->first();

            if ($find_meta) {
                continue;
            }

            if ($property_reference == '') {
                continue;
            }

            $property = Property::where('ref_no', $property_reference)->first();
            if ($property) {
                $lead_type_id = $this->getPropertyLeadTypeId($property->for);
                $mobile = trim(str_replace("tel:", "", str_replace('+', '', $client_phone)));
                $source = Source::where('name', 'LIKE', '%Dubizzle%')->first();
                $medium = Medium::where('name', 'LIKE', "%" . "Email%")->first();
                $for = $property->for ?? '';
                try {
                    DB::beginTransaction();
                    $lead = Lead::create([
                        // 'mail_number' => $arry['mail_number'],
                        'first_name' => ucfirst(trim($client_name)) ?? '',
                        'email' => trim($client_email) ?? '',
                        'mobile' => validatePhoneNumber(trim(str_replace("tel:", "", str_replace('+', '', $client_phone))) ?? ''),
                        'whatsapp' => trim($client_phone) ?? '',
                        'message' => $message ?? '',
                        // 'subject' => $arry['subject'] . ' <' . $arry['from'] . '>',
                        'for' => [$property->for],
                        'agent_id' => $property->agent_id,
                        'property_id' => $property->id,
                        'type_id' => $lead_type_id,
                        'source_id' => $source->id ?? 31,
                        'lead_medium_id' => $medium?->id,
                        'lead_status_id' => 1, //Open
                        // 'lead_sub_status_id' => 4, // Not yet contacted
                        'portal_created_at' => $date_time,
                    ]);
                    $criteria = null;
                    $community_property = null;
                    $region = null;
                    $community = null;
                    $sub_community = null;

                    $property = Property::where('ref_no', $property_reference)->first();
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

                    $leadMeta = new LeadMeta();
                    $leadMeta->lead_id = $lead->id;
                    $leadMeta->meta_key = 'dubizzle_lead_id';
                    $leadMeta->meta_value = str_replace(' ', '', $date_time) . '-' . $property_reference . '-' . $client_phone;
                    $leadMeta->save();

                    DB::commit();
                } catch (Exception $e) {
                    DB::rollback();
                    info($e->getMessage());
                    continue;
                }

                if (env('APP_ENV') == 'production') {
                    event(new \App\Events\LeadCreated($lead));
                }
                $note = LeadNote::updateOrCreate(
                    ['lead_id' => $lead->id, 'note' => 'Auto Import Dubizzle> ' . $message],
                    ['lead_id' => $lead->id]
                );
                $counter++;
            }
        }
        }
        Log::channel('urlLogs')->info($counter . ' Dubizzle Leads Fetched Successfully.');

    }

    private function handleWhatsappLeads($response)
    {

        $counter = 0;
        Log::info("Dubizzle Whatsapp leads fetched successfully. Found " . count($response ?? []) . " records.");
        foreach ($response ?? [] as $view) {

            try {
                $date_time = $view['date_time'];
                $source = $view['source'];
                $listing_id = $view['listing_id'];
                $property_reference = $view['listing_reference'];
                $detail = $view['detail'];
                $actor_name = $detail['actor_name'];
                $cell = $detail['cell'];
                $message = $detail['message'];
                // $contact_link = $detail['contact_link'];
                // $delivery_notifications = $view['delivery_notifications'];
                // dd($delivery_notifications[0]['created_at']);
                // if ($delivery_notifications[0]['created_at']) {
                //     $lead_contacted_agent = $delivery_notifications[0]['created_at'];
                // }
                // if ($delivery_notifications[1]['created_at']) {
                //     $delivered = $delivery_notifications[1]['created_at'];
                // }

                // if ($delivery_notifications[2]['created_at']) {
                //     $sent = $delivery_notifications[2]['created_at'];
                // }

                $find_meta = LeadMeta::where('meta_key', 'dubizzle_lead_id_whatsapp')->where('meta_value', str_replace(' ', '', $date_time) . '-' . $property_reference . '-' . $cell . '-whatsapp')->first();
                if ($find_meta) {
                    continue;
                }
                if ($property_reference == '') {
                    continue;
                }
                $property = Property::where('ref_no', $property_reference)->first();
                if(!$property){
                    continue;
                }
                $find_lead = Lead::where('mobile', validatePhoneNumber(trim(str_replace("tel:", "", str_replace('+', '', $cell)))))
                ->where('lead_medium_id', 4)
                ->where('property_id', $property->id)
                ->where('source_id',13)->first();

                if ($find_lead) {
                    continue;
                }

                if ($property) {
                    $lead_type_id = $this->getPropertyLeadTypeId($property->for);
                    $mobile = trim(str_replace("tel:", "", str_replace('+', '', $cell)));
                    $source = Source::where('name', 'LIKE', '%Dubizzle%')->first();
                    $medium = Medium::where('name', 'LIKE', "%" . "WhatsApp%")->first();
                    $for = $property->for ?? '';
                    try {
                        DB::beginTransaction();
                        $lead = Lead::create([
                            // 'mail_number' => $arry['mail_number'],
                            'first_name' => $actor_name ?? 'New',
                            // 'email' => trim($client_email) ?? '',
                            'mobile' => validatePhoneNumber(trim(str_replace("tel:", "", str_replace('+', '', $cell))) ?? ''),
                            'whatsapp' => trim($cell) ?? '',
                            'message' => $message ?? '',
                            // 'subject' => $arry['subject'] . ' <' . $arry['from'] . '>',
                            'for' => [$property->for],
                            'agent_id' => $property->agent_id,
                            'property_id' => $property->id,
                            'type_id' => $lead_type_id,
                            'source_id' => $source->id ?? 31,
                            'lead_medium_id' => $medium?->id,
                            'lead_status_id' => 1, //Open
                            'creaatd_at' => Carbon::parse($date_time),
                        ]);


                        $criteria = null;
                        $community_property = null;
                        $region = null;
                        $community = null;
                        $sub_community = null;

                        $property = Property::where('ref_no', $property_reference)->first();
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

                        $leadMeta = new LeadMeta();
                        $leadMeta->lead_id = $lead->id;
                        $leadMeta->meta_key = 'dubizzle_lead_id_whatsapp';
                        $leadMeta->meta_value = str_replace(' ', '', $date_time) . '-' . $property_reference . '-' . $cell . '-whatsapp';
                        $leadMeta->save();

                        DB::commit();
                    } catch (Exception $e) {
                        DB::rollback();
                        info($e->getMessage());
                        continue;
                    }

                    if (env('APP_ENV') == 'production') {
                        event(new \App\Events\LeadCreated($lead));
                    }
                    $note = LeadNote::updateOrCreate(
                        ['lead_id' => $lead->id, 'note' => 'Auto Import> ' . $message],
                        ['lead_id' => $lead->id]
                    );
                    $note->save();
                    $counter++;

                }
            } catch (\Throwable $th) {
                continue;
                throw $th;
            }



        }
        info($counter . ' BaDubizzleyut WhatsApp Leads Fetched Successfully.');
        Log::channel('urlLogs')->info($counter . ' Dubizzle WhatsApp Leads Fetched Successfully.');
    }

    private function handlePhoneViews($response)
    {

        foreach ($response ?? [] as $view) {
            $date_time = $view['date_time'];
            $listing_id = $view['listing_id'];
            $listing_reference = $view['listing_reference'];
            $phone_views = $view['phone_views'];
            info($view);
            break;
            sleep(2);
        }
    }

    private function handleSmsClicks($response)
    {

        foreach ($response ?? [] as $view) {
            $date_time = $view['date_time'];
            $listing_id = $view['listing_id'];
            $listing_reference = $view['listing_reference'];
            $sms_clicks = $view['sms_clicks'];
            info($view);
            break;

            sleep(2);

        }
    }

    private function handleWhatsappViews($response)
    {

        foreach ($response ?? [] as $view) {
            $date_time = $view['date_time'];
            $listing_id = $view['listing_id'];
            $listing_reference = $view['listing_reference'];
            $whatsapp_views = $view['whatsapp_views'];
            info($view);
            break;

            sleep(2);

        }
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
}
