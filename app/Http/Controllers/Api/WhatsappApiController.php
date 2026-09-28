<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Community;
use App\Models\CommunityProperty;
use App\Models\Lead;
use App\Models\LeadCriteria;
use App\Models\LeadCriteriaLocation;
use App\Models\LeadNote;
use App\Models\Medium;
use App\Models\Note;
use App\Models\Property;
use App\Models\Region;
use App\Models\Source;
use App\Models\SubCommunity;
use App\Models\TempPfDump;
use App\Models\TempBayutDump;
use App\Models\TempDubizzleDump;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Traits\TelegramTrait;
use App\Events\PortalsLeadsNotification;

class WhatsappApiController extends Controller
{
    use TelegramTrait;
    function new(Request $request)
    {
        $response = $request->json()->all();
        try {

            if (is_array($response) && $response != null) {
                if (count($response) > 0) {
                    foreach ($response as $item) {
                        if (isset($item['data']) && $item['data'] != null) {
                            $mobile = $item['data']['customer']['channel_phone_number'];
                            $mobile = validatePhoneNumber($mobile);
                            $lead = Lead::where('mobile', $mobile)->first();
                            if ($lead) {
                                $lead->mobile = $mobile;
                                $lead->first_name = $item['data']['customer']['traits']['name'];
                                $lead->lead_status_id = '1'; // 3 new
                                $lead->type_id = '11'; // 11 unspecified
                                if ($lead->save()) {
                                    $notes = 'Imported from Whatsapp Api <br>
                        Name : ' . $item['data']['customer']['traits']['name'] . '<br>
                        Phone : ' . validatePhoneNumber($item['data']['customer']['channel_phone_number']) . '<br>
                        Whatsapp Opted In : ' . $item['data']['customer']['traits']['whatsapp_opted_in'] . '<br>
                        Message Type : ' . $item['type'] . '<br>
                        Received At : ' . ($item['data']['message']['received_at_utc'] != '' ? date('d/m/y H:i', strtotime($item['data']['message']['received_at_utc'])) : '') . '<br>
                        Delivered At : ' . ($item['data']['message']['delivered_at_utc'] != '' ? date('d/m/y H:i', strtotime($item['data']['message']['delivered_at_utc'])) : '') . '<br>
                        Seen At : ' . ($item['data']['message']['seen_at_utc'] != '' ? date('d/m/y H:i', strtotime($item['data']['message']['seen_at_utc'])) : '') . '<br>
                        Message : ' . $item['data']['message']['message'];

                                    $leadNote = new LeadNote();
                                    $leadNote->lead_id = $lead->id;
                                    $leadNote->note = $notes;
                                    $leadNote->save();
                                }
                            } else {
                                $ref_no = getBetweenText($item['data']['message']['message'], "ref:", "source");
                                $source = getBetweenText($item['data']['message']['message'], "advert on", ",");
                                $property = Property::where('ref_no', $ref_no)->first();
                                $lead = new Lead();
                                $lead->mobile = $mobile;
                                $lead->whatsapp = $mobile;
                                $lead->agent_id = $property->agent_id ?? null;
                                $lead->property_id = $property->id ?? null;
                                $lead->first_name = $item['data']['customer']['traits']['name'];
                                $lead->lead_status_id = '1'; // 1 new
                                $lead->type_id = '11'; // 11 unspecified
                                $lead->source_id = (Source::whereRaw('UPPER(name) LIKE ?', ['%' . strtoupper($source) . '%'])->where('is_lead_source', '1')->first()->id ?? Source::where('name', 'Whatsapp')->where('is_lead_source', '1')->first()->id) ?? null;
                                $lead->lead_medium_id = Medium::where('name', 'Whatsapp')->where('is_lead_medium', '1')->first()->id ?? null;
                                if ($lead->save()) {

                                    $notes = 'Imported from Whatsapp Api <br>
                        Name : ' . $item['data']['customer']['traits']['name'] . '<br>
                        Phone : ' . validatePhoneNumber($item['data']['customer']['channel_phone_number']) . '<br>
                        Whatsapp Opted In : ' . $item['data']['customer']['traits']['whatsapp_opted_in'] . '<br>
                        Message Type : ' . $item['type'] . '<br>
                        Received At : ' . ($item['data']['message']['received_at_utc'] != '' ? date('d/m/y H:i', strtotime($item['data']['message']['received_at_utc'])) : '') . '<br>
                        Delivered At : ' . ($item['data']['message']['delivered_at_utc'] != '' ? date('d/m/y H:i', strtotime($item['data']['message']['delivered_at_utc'])) : '') . '<br>
                        Seen At : ' . ($item['data']['message']['seen_at_utc'] != '' ? date('d/m/y H:i', strtotime($item['data']['message']['seen_at_utc'])) : '') . '<br>
                        Message : ' . $item['data']['message']['message'];

                                    $leadNote = new LeadNote();
                                    $leadNote->lead_id = $lead->id;
                                    $leadNote->note = $notes;
                                    $leadNote->save();

                                    if ($property->agent_id) {
                                        $user = User::where('id', $property->agent_id)->first();
                                        if ($user) {
                                            event(new PortalsLeadsNotification("Lead", $lead, 'received', $user, $user, 1));
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'something went wrong', 'error' => $e->getMessage()], 500);
        }

        return response()->json(['status' => true, 'message' => 'success']);
    }

    public function pf(Request $request, $innerKey)
    {
        $data = $request->all();
        // dd($request->all());
        if ($innerKey == '1f906c6932a27eea366db20c0cf8f1dfx') {
            try {
                //$apiKey = '006699a40d5679adf22c8da54492299c'; // OLD
                $apiKey = '006699905101180188a731bd32286519'; // New
                $body = file_get_contents('php://input');
                $signature = request()->header('X-Propertyfinder-Signature') ?? '';
                $computedSignature = md5($apiKey . $body);
                //$data_with_steps = [$apiKey, $body, $signature, $computedSignature];

                // TempPfDump::create([
                //     'data' => json_encode($data),
                // ]);
                // info('Signature is valid');
                // $this->saveResponse();

                //$this->saveResponse(json_encode($data));
                TempPfDump::create([
                        'data' => json_encode($data),
                    ]);
                //disabled by khagesh- PF whatsapp lead capture is now done by API
//                if ($signature !== $computedSignature) {
//                    $leadErrorWebhookGroupId = env('TELEGRAM_LEAD_WEBHOOK_ERROR_NOTIFICATION_ERROR_GROUP_ID') ?? '';
//                    $message = $this->sendTelegramMessage("Invalid signature error on pf whatsapp lead webhook", [$leadErrorWebhookGroupId], 'lead_bot');
//                    info('Signature is invalid');
//                    throw new \RuntimeException('Invalid signature! Please check your API key.');
//                } else {
//
//                    $this->saveResponse(json_encode($data));
//
//                    return response()->json(['status' => true, 'message' => 'success'], 200);
//                }
            } catch (\Exception $e) {
                $leadErrorWebhookGroupId = env('TELEGRAM_LEAD_WEBHOOK_ERROR_NOTIFICATION_ERROR_GROUP_ID') ?? '';
                $message = $this->sendTelegramMessage("An error occured in property finder webhook in pf function with error: " . $e->getMessage() . " on line: " . $e->getLine(), [$leadErrorWebhookGroupId], 'lead_bot');

                return response()->json(['status' => false, 'message' => $e->getMessage(), 'error' => $e->getMessage()], 500);
            } // end catch
        } else {
            abort(404);
            // Return appropriate response for the else condition here
        } // end else
        // return response()->json(['status' => true, 'message' => 'success'], 200);
    } // end function
    public function bayut(Request $request, $innerKey)
    {
        $data = $request->all();
        // info($data);
        if ($innerKey == '1f906c6932a27aaa366db20c0cf8f1ffd') {
            try {
                $apiKey = '763b54a2d0e72d876f879c501b2565c6d012cf81ed2a25c35a3e14fb7344a496'; // New
                $body = file_get_contents('php://input');
                $combined_data = $apiKey . $body;
                $computedSignature = md5($combined_data, false);
                $signature = request()->header('X-Bayut-Signature') ?? '';
                TempBayutDump::create([
                    'data' => json_encode($data),
                ]);
                if ($signature !== $computedSignature) {
                    $leadErrorWebhookGroupId = env('TELEGRAM_LEAD_WEBHOOK_ERROR_NOTIFICATION_ERROR_GROUP_ID') ?? '';
                    $message = $this->sendTelegramMessage("Invalid signature error on bayut whatsapp lead webhook", [$leadErrorWebhookGroupId], 'lead_bot');
                    info('Signature is invalid');
                    throw new \RuntimeException('Invalid signature! Please check your API key.');
                } else {
                    $fixed_message = 'Bayut WA API Webhook - Main';
                    $val = $this->saveBayutLead(json_encode($data), $fixed_message);

                    return response()->json(['status' => true, 'message' => 'success'], 200);
                }
            } catch (\Exception $e) {
                $leadErrorWebhookGroupId = env('TELEGRAM_LEAD_WEBHOOK_ERROR_NOTIFICATION_ERROR_GROUP_ID') ?? '';
                $message = $this->sendTelegramMessage("An error occured in Bayut webhook in bayut function with error: " . $e->getMessage() . " on line: " . $e->getLine(), [$leadErrorWebhookGroupId], 'lead_bot');
                return response()->json(['status' => false, 'message' => $e->getMessage(), 'error' => $e->getMessage()], 500);
            } // end catch
        } else {
            return response()->json(['status' => false, 'message' => 'Unauthenticated'], 403);
        } // end else
        return response()->json(['status' => false, 'message' => 'Unauthenticated'], 403);
    }

    public function bayutPushApi(Request $request, $innerKey)
    {
        $data = $request->all();
        if ($innerKey == 'a4c9e83b7f2d45c3e8a6b79d2e3fcf6b') {
            try {
                $apiKey = '763b54a2d0e72d876f879c501b2565c6d012cf81ed2a25c35a3e14fb7344a496'; // New
                $body = file_get_contents('php://input');
                $combined_data = $apiKey . $body;
                $computedSignature = md5($combined_data, false);
                $signature = request()->header('X-Bayut-Signature') ?? '';
                TempBayutDump::create([
                    'data' => json_encode($data),
                    'is_push_story_api' => true,
                ]);
                if ($signature !== $computedSignature) {
                    $leadErrorWebhookGroupId = env('TELEGRAM_LEAD_WEBHOOK_ERROR_NOTIFICATION_ERROR_GROUP_ID') ?? '';
                    $this->sendTelegramMessage("Invalid signature error on bayut whatsapp lead webhook", [$leadErrorWebhookGroupId], 'lead_bot');
                    info('Signature is invalid');
                    throw new \RuntimeException('Invalid signature! Please check your API key.');
                } else {
                    $fixed_message = 'Bayut WA Push Story API Webhook - Main';
                    $this->saveBayutPushApiLead(json_encode($data), $fixed_message);

                    return response()->json(['status' => true, 'message' => 'success'], 200);
                }
            } catch (\Exception $e) {
                $leadErrorWebhookGroupId = env('TELEGRAM_LEAD_WEBHOOK_ERROR_NOTIFICATION_ERROR_GROUP_ID') ?? '';
                $this->sendTelegramMessage("An error occured in Bayut webhook in bayut function with error: " . $e->getMessage() . " on line: " . $e->getLine(), [$leadErrorWebhookGroupId], 'lead_bot');
                return response()->json(['status' => false, 'message' => $e->getMessage(), 'error' => $e->getMessage()], 500);
            } // end catch
        }
        return response()->json(['status' => false, 'message' => 'Unauthenticated'], 403);
    }

    public function dubizzle(Request $request, $innerKey)
    {
        $data = $request->all();
        if ($innerKey == '1f906c6932a27auw366db20b0cf8f1eed') {
            try {
                $apiKey = 'db934b2342b04cafacce28b9595468d2'; // New
                $body = file_get_contents('php://input');
                $signature = request()->header('X-Dubizzle-Signature') ?? '';
                $combined_data = $apiKey . $body;
                $computedSignature = md5($combined_data, false);
                TempDubizzleDump::create([
                    'data' => json_encode($data),
                ]);
                if ($signature !== $computedSignature) {
                    info('Signature is invalid');
                    throw new \RuntimeException('Invalid signature! Please check your API key.');
                } else {
                    $fixed_message = 'Dubizzle WA API Webhook - Main';
                    $this->saveDubizzleLead(json_encode($data), $fixed_message);

                    return response()->json(['status' => true, 'message' => 'success'], 200);
                }
            } catch (\Exception $e) {
                return response()->json(['status' => false, 'message' => $e->getMessage(), 'error' => $e->getMessage()], 500);
            } // end catch
        } else {
            return response()->json(['status' => false, 'message' => 'Unauthenticated'], 403);
        } // end else
        return response()->json(['status' => false, 'message' => 'Unauthenticated'], 403);
    }


    public function saveBayutLead($data, $fixed_message)
    {
        // info($data);
        try {
            $result = [];
            $decodedData = json_decode($data, true);
            $data = $decodedData;
            if ($data) {
                $enquirerPhoneNumber = validatePhoneNumber($data['enquirer']['phone_number']);

                $message = $data['message'] ?? '';
                $receivedAt = $data['received_at'];
                $trackingLink = $data['enquirer']['contact_link'];
                $property_ref = $data['listing']['reference'] ?? null;
                $property = null;
                $for = null;
                // dd($type, $property_ref, $id, $enquirerPhoneNumber, $language, $message, $receivedAt, $trackingLink, $agent, $locations);
                if ($property_ref) {
                    $property = Property::where('ref_no', $property_ref)->first();
                    if ($property) {
                        $for = $this->getPropertyLeadTypeId($property?->for);
                        $agent = User::where('id', $property->agent_id)->first();
                    }
                } else {
                    $agentEmail = $data['agent']['email'] ?? null;
                    if ($agentEmail) {
                        $agent = User::where('email', $agentEmail)->first();
                    }
                }


                $findlead = Lead::where('mobile', $enquirerPhoneNumber)
                    ->when($property, function ($query) use ($property) {
                        return $query->where('property_id', $property->id);
                    })
                    ->when($agent, function ($query) use ($agent) {
                        return $query->where('agent_id', $agent->id);
                    })
                    ->where('source_id', 4)->first();

                if ($findlead) {
                    $community_property = null;
                    $region = null;
                    $community = null;
                    $sub_community = null;

                    if ($property_ref) {
                        $criteria = getPropertyCriteria($property_ref);


                        $community = Community::find($property->community_id);

                        $sub_community = SubCommunity::find($property->sub_community_id);

                        $community_property = CommunityProperty::find($property->community_property_id);

                        $region = Region::find($property->region_id);
                    }
                    $leadNote = new LeadNote();
                    $leadNote->lead_id = $findlead->id;
                    $leadNote->note = $fixed_message . "</br>DUPLICATE-Lead</br>Message: " . $message . '</br> <b>Criteria Details:</b> </br> <b>Region: </b> ' . ($region?->name ?? "N/A") . '</br>  <b>Community:</b> ' . ($community?->name ?? "N/A")  . '</br>  <b>Sub Community:</b> ' . ($sub_community?->name ?? "N/A") . '</br>  <b>Building/Property :</b> ' . ($community_property?->name ?? "N/A") . '</br>  <b>Tracking Link :</b> ' . $trackingLink;
                    $leadNote->save();

                    $findlead->updated_at = now();
                    $findlead->save();
                    $findlead->isDuplicate = true;
                    event(new \App\Events\LeadCreated($findlead));
                    return response()->json(['status' => '1', 'msg' => 'Lead already exists']);
                }

                $lead = new Lead();
                $lead->first_name = $data['enquirer']['name'] ?? '';
                $lead->mobile = $enquirerPhoneNumber;
                $lead->whatsapp = $enquirerPhoneNumber;
                $lead->property_id = $property?->id ?? null;
                $lead->agent_id = $agent?->id ?? null;
                $lead->lead_status_id = 1; // New
                $lead->source_id = 4; //Bayut
                $lead->lead_medium_id = 4; // WhatsApp
                $lead->type_id = $for;
                $lead->for = [$property?->for];
                $lead->message = $message;
                $lead->pf_tracking_link = $trackingLink;
                $lead->save();
                // Event firing prior to lead criteria

                event(new \App\Events\LeadCreated($lead));

                // if ($agent->id) {
                //     $user = User::where('id', $agent->id)->first();
                //     if ($user) {
                //         event(new PortalsLeadsNotification("Lead", $lead, 'received', $user, $user, 1));
                //     }
                // }


                $community_property = null;
                $region = null;
                $community = null;
                $sub_community = null;



                $criteria = null;

                if ($property_ref) {
                    $criteria = getPropertyCriteria($property_ref);
                    $community = Community::find($property->community_id);

                    $sub_community = SubCommunity::find($property->sub_community_id);

                    $community_property = CommunityProperty::find($property->community_property_id);

                    $region = Region::find($property->region_id);
                }

                $leadNote = new LeadNote();
                $leadNote->lead_id = $lead->id;
                $leadNote->note = $fixed_message . "</br>Message: " . $message . '</br> <b>Criteria Details:</b> </br> <b>Region: </b> ' . ($region?->name ?? "N/A") . '</br>  <b>Community:</b> ' . ($community?->name ?? "N/A")  . '</br>  <b>Sub Community:</b> ' . ($sub_community?->name ?? "N/A") . '</br>  <b>Building/Property :</b> ' . ($community_property?->name ?? "N/A") . '</br>  <b>Tracking Link :</b> ' . $trackingLink;
                $leadNote->save();

                $leadCriteria = new LeadCriteria();
                $leadCriteria->lead_id = $lead->id;
                $leadCriteria->furnished = $property->furnished ?? null;
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

                $leadCriteria->view = $criteria['view'] ?? null;
                $leadCriteria->save();

                // $randomTime = rand(1, 10);
                // sleep($randomTime);
                // $response = Http::get($trackingLink);
                // info('Seconds: '.$randomTime);
                // info($response);

                return response()->json(['status' => '1', 'msg' => 'Lead Saved successfully']);
                // dd($leadCriteria, $type, $property_ref, $id, $enquirerPhoneNumber, $language, $message, $receivedAt, $trackingLink, $agent, $locations);
            } else {
                // Handle JSON decoding error
                $result = array('status' => '0', 'msg' => 'Failed to decode JSON string!!');
            }
            return $result;
        } catch (\Exception $e) {
            $result = array('status' => '0', 'msg' => $e->getMessage());
            $leadErrorWebhookGroupId = env('TELEGRAM_LEAD_WEBHOOK_ERROR_NOTIFICATION_ERROR_GROUP_ID') ?? '';
            $message = $this->sendTelegramMessage("An error occured in Bayut webhook in saveBayutLead function with error: " . $e->getMessage() . " on line: " . $e->getLine(), [$leadErrorWebhookGroupId], 'lead_bot');
            info('Data: ' . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
            return $result;
        }
    }

    public function saveBayutPushApiLead($data, $fixed_message)
    {
        try {
            $decodedData = json_decode($data, true);

            $data = $decodedData;
            if ($data) {
                $enquirerPhoneNumber = validatePhoneNumber($data['enquirer']['phone_number']);
                $message = $data['message'] ?? '';
                $trackingLink = $data['enquirer']['contact_link'];
                $type = $data['story']['type'];

                $agent = null;
                $property = null;
                $community = null;
                $intent = $data['enquirer']['intent'] ?? null;
                $typeId = null;

                if (!empty($intent)) {
                    if ($intent == 'Buying/Renting') {
                        $typeId = 2;//Buyer
                    }elseif ($intent == 'Selling/Renting') {
                        $typeId = 4;//Seller
                    }elseif ($intent == 'Investing') {
                        $typeId = 2;//Buyer
                    }elseif ($intent == 'General Inquiry') {
                        $typeId = 10;//Other
                    }
                }

                if ($type == 'property') {
                    $propertyRef = $data['story']['listing_reference'] ?? null;
                    $property = Property::where('ref_no', $propertyRef)->first();
                    if ($property) {
                        $community = Community::find($property->community_id);
                        if (empty($typeId)){
                            $typeId = $this->getPropertyLeadTypeId($property?->for);
                        }
                        $agent = User::where('id', $property->agent_id)->first();
                    }
                } elseif ($type == 'loc_purpose') {
                    if (empty($typeId)){
                        $typeId = ($data['story']['purpose'] ?? null) == 'sale' ? 2 : 1;
                    }
                    $community = Community::where('name', $data['story']['loc3_name'] ?? null)->first();
                    $agent = User::where('email', $data['agent']['email'] ?? null)->first();
                } else {
                    $agent = User::where('email', $data['agent']['email'] ?? null)->first();
                }

                $findLead = Lead::where('mobile', $enquirerPhoneNumber)
                    ->when($property && $type == 'property', function ($query) use ($property) {
                        return $query->where('property_id', $property->id);
                    })
                    ->when($typeId && $type == 'loc_purpose', function ($query) use ($typeId) {
                        return $query->where('type_id', $typeId);
                    })
                    ->when($agent, function ($query) use ($agent) {
                        return $query->where('agent_id', $agent->id);
                    })
                    ->where('source_id', 4)
                    ->first();

                if ($findLead) {
                    $community_property = null;
                    $region = null;
                    $sub_community = null;

                    if ($property) {
                        $sub_community = SubCommunity::find($property->sub_community_id);
                        $community_property = CommunityProperty::find($property->community_property_id);
                        $region = Region::find($property->region_id);
                    }

                    $leadNote = new LeadNote();
                    $leadNote->lead_id = $findLead->id;
                    $leadNote->note = $fixed_message . "</br>DUPLICATE-Lead</br>Message: " . $message . '</br> <b>Criteria Details:</b> </br> <b>Region: </b> ' . ($region?->name ?? "N/A") . '</br>  <b>Community:</b> ' . ($community?->name ?? "N/A") . '</br>  <b>Sub Community:</b> ' . ($sub_community?->name ?? "N/A") . '</br>  <b>Building/Property :</b> ' . ($community_property?->name ?? "N/A") . '</br>  <b>Tracking Link :</b> ' . $trackingLink;
                    $leadNote->save();

                    $findLead->updated_at = now();
                    $findLead->save();
                    $findLead->isDuplicate = true;
                    event(new \App\Events\LeadCreated($findLead));
                    return response()->json(['status' => '1', 'msg' => 'Lead already exists']);
                }

                $lead = new Lead();
                $lead->first_name = $data['enquirer']['name'] ?? '';
                $lead->mobile = $enquirerPhoneNumber;
                $lead->whatsapp = $enquirerPhoneNumber;
                $lead->property_id = $property?->id ?? null;
                $lead->agent_id = $agent?->id ?? null;
                $lead->lead_status_id = 1; // New
                $lead->source_id = 4; //Bayut
                $lead->lead_medium_id = 4; // WhatsApp
                $lead->type_id = $typeId;
                $lead->for = [$property?->for];
                $lead->message = $message;
                $lead->pf_tracking_link = $trackingLink;
                $lead->intent = $intent;
                $lead->save();

                // Event firing prior to lead criteria
                event(new \App\Events\LeadCreated($lead));

                $community_property = null;
                $region = null;
                $sub_community = null;
                $criteria = null;

                if ($property) {
                    $criteria = getPropertyCriteria($property->ref_no);
                    $sub_community = SubCommunity::find($property->sub_community_id);
                    $community_property = CommunityProperty::find($property->community_property_id);
                    $region = Region::find($property->region_id);
                }

                $leadNote = new LeadNote();
                $leadNote->lead_id = $lead->id;
                $leadNote->note = $fixed_message . "</br>Message: " . $message . '</br> <b>Criteria Details:</b> </br> <b>Region: </b> ' . ($region?->name ?? "N/A") . '</br>  <b>Community:</b> ' . ($community?->name ?? "N/A") . '</br>  <b>Sub Community:</b> ' . ($sub_community?->name ?? "N/A") . '</br>  <b>Building/Property :</b> ' . ($community_property?->name ?? "N/A") . '</br>  <b>Tracking Link :</b> ' . $trackingLink;
                $leadNote->save();

                $leadCriteria = new LeadCriteria();
                $leadCriteria->lead_id = $lead->id;
                $leadCriteria->furnished = $property->furnished ?? null;
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

                $leadCriteria->view = $criteria['view'] ?? null;
                $leadCriteria->save();

                return response()->json(['status' => '1', 'msg' => 'Lead Saved successfully']);
            } else {
                $result = array('status' => '0', 'msg' => 'Failed to decode JSON string!!');
            }
            return $result;
        } catch (\Exception $e) {
            $result = array('status' => '0', 'msg' => $e->getMessage());
            info('Data: ' . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
            return $result;
        }
    }

    public function saveDubizzleLead($data, $fixed_message)
    {
        try {
            $result = [];
            $decodedData = json_decode($data, true);
            $data = $decodedData;
            if ($data) {
                $enquirerPhoneNumber = validatePhoneNumber($data['enquirer']['phone_number']);

                $message = $data['message'] ?? '';
                $receivedAt = $data['received_at'];
                $trackingLink = $data['enquirer']['contact_link'];
                $property_ref = $data['listing']['reference'];

                // dd($type, $property_ref, $id, $enquirerPhoneNumber, $language, $message, $receivedAt, $trackingLink, $agent, $locations);
                $property = Property::where('ref_no', $property_ref)->first();
                $for = $this->getPropertyLeadTypeId($property?->for);
                $agent = User::where('id', $property->agent_id)->first();
                //$findlead = Lead::where('mobile', $enquirerPhoneNumber)->where('property_id', $property->id)->where('source_id', 13)->first();

                // if ($findlead) {
                //     $community_property = null;
                //     $region = null;
                //     $community = null;
                //     $sub_community = null;

                //     if($property_ref){
                //         $criteria = getPropertyCriteria($property_ref);


                //             $community = Community::find($property->community_id);

                //             $sub_community = SubCommunity::find($property->sub_community_id);

                //             $community_property = CommunityProperty::find($property->community_property_id);

                //             $region = Region::find($property->region_id);

                //     }
                //     $leadNote = new LeadNote();
                //     $leadNote->lead_id = $findlead->id;
                //     $leadNote->note = $fixed_message."</br>DUPLICATE-Lead</br>Message: " . $message . '</br> <b>Criteria Details:</b> </br> <b>Region: </b> ' . ($region?->name ?? "N/A") . '</br>  <b>Community:</b> ' . ($community?->name ?? "N/A")  . '</br>  <b>Sub Community:</b> ' . ($sub_community?->name ?? "N/A") . '</br>  <b>Building/Property :</b> ' . ($community_property?->name ?? "N/A") . '</br>  <b>Tracking Link :</b> ' . $trackingLink;
                //     $leadNote->save();

                //     $findlead->updated_at = now();
                //     $findlead->save();
                //     $findlead->isDuplicate = true;
                //     event(new \App\Events\LeadCreated ($findlead));
                //     return response()->json(['status' => '1', 'msg' => 'Lead already exists']);
                // }

                $lead = new Lead();
                $lead->first_name = $data['enquirer']['name'] ?? '';
                $lead->mobile = $enquirerPhoneNumber;
                $lead->whatsapp = $enquirerPhoneNumber;
                $lead->property_id = $property->id ?? null;
                $lead->agent_id = $agent->id ?? null;
                $lead->lead_status_id = 1; // New
                $lead->source_id = 13; //Dubizzle
                $lead->lead_medium_id = 4; // WhatsApp
                $lead->type_id = $for;
                $lead->for = [$property->for];
                $lead->message = $message;
                $lead->pf_tracking_link = $trackingLink;
                $lead->save();


                // Event firing prior to lead criteria

                event(new \App\Events\LeadCreated($lead));
                if ($agent->id) {
                    $user = User::where('id', $agent->id)->first();
                    if ($user) {
                        event(new PortalsLeadsNotification("Lead", $lead, 'received', $user, $user, 1));
                    }
                }

                $community_property = null;
                $region = null;
                $community = null;
                $sub_community = null;



                $criteria = null;

                if ($property_ref) {
                    $criteria = getPropertyCriteria($property_ref);
                    $community = Community::find($property->community_id);

                    $sub_community = SubCommunity::find($property->sub_community_id);

                    $community_property = CommunityProperty::find($property->community_property_id);

                    $region = Region::find($property->region_id);
                }

                $leadNote = new LeadNote();
                $leadNote->lead_id = $lead->id;
                $leadNote->note = $fixed_message . "</br>Message: " . $message . '</br> <b>Criteria Details:</b> </br> <b>Region: </b> ' . ($region?->name ?? "N/A") . '</br>  <b>Community:</b> ' . ($community?->name ?? "N/A")  . '</br>  <b>Sub Community:</b> ' . ($sub_community?->name ?? "N/A") . '</br>  <b>Building/Property :</b> ' . ($community_property?->name ?? "N/A") . '</br>  <b>Tracking Link :</b> ' . $trackingLink;
                $leadNote->save();

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

                // $randomTime = rand(1, 10);
                // sleep($randomTime);
                // $response = Http::get($trackingLink);
                // info('Seconds: '.$randomTime);
                // info($response);
                return response()->json(['status' => '1', 'msg' => 'Lead Saved successfully']);
                // dd($leadCriteria, $type, $property_ref, $id, $enquirerPhoneNumber, $language, $message, $receivedAt, $trackingLink, $agent, $locations);
            } else {
                // Handle JSON decoding error
                $result = array('status' => '0', 'msg' => 'Failed to decode JSON string!!');
            }
            return $result;
        } catch (\Exception $e) {
            $leadErrorWebhookGroupId = env('TELEGRAM_LEAD_WEBHOOK_ERROR_NOTIFICATION_ERROR_GROUP_ID') ?? '';
            $message = $this->sendTelegramMessage("An error occured in Dubizzle webhook in saveDubizzleLead function with error: " . $e->getMessage() . " on line: " . $e->getLine(), [$leadErrorWebhookGroupId], 'lead_bot');
            $result = array('status' => '0', 'msg' => $e->getMessage());
            info('Data: ' . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
            return $result;
        }
    }

    public function sampleResponse($id)
    {

        // return $data = '{
        //   "data":"type": "whatsapp_leads",
        //   "id": "90532257-3376-4f7b-a156-bb1b4698f09c",
        //   "attributes": {
        //   "enquirer_phone_number": "+971585000002",
        //  "Language": "en",
        //   "message": "Hello,pleasetellmemoreaboutyourproperty.",
        //   "received_at": "2022-09-14T07: 43: 16Z",
        //   "tracking_link": "https: //www.propertyfinder.ae/leads/vl/lead/message/VY/ZZZ",
        //   },
        //   "relationships": "agents":{
        //     "data": {
        //       "type": "agents",
        //       "id": "123",
        //       "attributes": {
        //         "email": "bartlomiej@propertyfinder.ae",
        //         "full_name": "Bartlomiei Krukowski",
        //         "whatsapp_phone_number": "4971585000000",
        //       },
        //       "properties": {
        //         "data":{
        //           "type": "properties",
        //           "id": "9999",
        //           "attributes": {
        //             "category": "sell",
        //             "prices": {
        //               "monthly": 8000,
        //               "yearly": 80000",
        //               "reference": "MYREE-XX",
        //               "type": "villa",
        //               "website_link": "https://www.propertyfinder.ae/to/YVYY/en",
        //             },
        //             "relationships": {
        //               "Locations": {
        //                 "data": [
        //                   {
        //                     "type": "Locations",
        //                     "id": "1",
        //                     "attributes": {
        //                       "name": "Dubai"
        //                     }
        //                   },
        //                 ]
        //               }
        //             }
        //           }
        //         }
        //       }
        //     }
        //   }
        // }';

        $dump = TempPfDump::find($id) ?? '';
        $response2 = Http::POST('http://secondary.lan/api/whatsapp/pf/1f906c6932a27eea366db20c0cf8f1dfx', [
            'data' => json_encode($dump->data),
        ]);
        // dd($response2);
        return $response2->body();
    }

    public function saveResponse($response)
    {

        try {
            $result = [];
            $decodedData = json_decode($response, true);
            $data = $decodedData;
            if ($data) {
                $type = $data['data']['type'];
                $id = $data['data']['id'];
                $enquirerPhoneNumber = validatePhoneNumber($data['data']['attributes']['enquirer_phone_number']);
                $language = $data['data']['attributes']['language'];
                $message = $data['data']['attributes']['message'];
                $receivedAt = $data['data']['attributes']['received_at'];
                $trackingLink = $data['data']['attributes']['tracking_link'];
                $property_ref = $data['data']['relationships']['properties']['data']['attributes']['reference'];
                $agent = $data['data']['relationships']['agents']['data']['attributes']['email'];
                $locations = $data['data']['relationships']['properties']['data']['relationships']['locations']['data'];
                // dd($type, $property_ref, $id, $enquirerPhoneNumber, $language, $message, $receivedAt, $trackingLink, $agent, $locations);
                $property = Property::where('ref_no', $property_ref)->first();
                $for = $this->getPropertyLeadTypeId($property?->for);
                $agent = User::where('email', $agent)->first();
                // $findlead = Lead::where('mobile', $enquirerPhoneNumber)->where('property_id', $property->id)->where('source_id', 31)->first();

                // if ($findlead) {
                //     $community_property = null;
                //     $region = null;
                //     $community = null;
                //     $sub_community = null;

                //     if($property_ref){
                //         $loc = $locations[0]['attributes']['name'] ?? null;
                //         if($loc){
                //             $region = Region::where('name', $loc)?->first();
                //         }
                //         $criteria = getPropertyCriteria($property_ref);
                //         if((!$community) && $criteria['community_id']){
                //             $community = Community::find($criteria['community_id']);
                //         }
                //         if((!$sub_community) && $criteria['subCommunity_id']){
                //              $sub_community = SubCommunity::find($criteria['subCommunity_id']);
                //         }
                //         if((!$community_property) && $criteria['communityProperty']){
                //             $community_property = CommunityProperty::find($criteria['communityProperty']);
                //         }
                //     }
                //     $leadNote = new LeadNote();
                //     $leadNote->lead_id = $findlead->id;
                //     $leadNote->note = "DUPLICATE-Lead</br>Message: " . $message . '</br> <b>Criteria Details:</b> </br> <b>Region: </b> ' . ($region?->name ?? "N/A") . '</br>  <b>Community:</b> ' . ($community?->name ?? "N/A")  . '</br>  <b>Sub Community:</b> ' . ($sub_community?->name ?? "N/A") . '</br>  <b>Building/Property :</b> ' . ($community_property?->name ?? "N/A") . '</br>  <b>Tracking Link :</b> ' . $trackingLink;
                //     $leadNote->save();

                //     $findlead->updated_at = now();
                //     $findlead->save();
                //     $findlead->isDuplicate = true;
                //     event(new \App\Events\LeadCreated ($findlead));
                //     return response()->json(['status' => '1', 'msg' => 'Lead already exists']);
                // }

                $lead = new Lead();
                $lead->mobile = $enquirerPhoneNumber;
                $lead->whatsapp = $enquirerPhoneNumber;
                $lead->property_id = $property->id ?? null;
                $lead->agent_id = $agent->id ?? null;
                $lead->lead_status_id = 1; // 3 unspecified
                $lead->source_id = 31; //Property Finder
                $lead->lead_medium_id = 4; // 3 unspecified
                $lead->type_id = $for;
                $lead->for = [$property->for];
                $lead->message = $message;
                $lead->pf_tracking_link = $trackingLink;
                $lead->save();
                // Event firing prior to lead criteria

                event(new \App\Events\LeadCreated($lead));
                if ($agent->id) {
                    $user = User::where('id', $agent->id)->first();
                    if ($user) {
                        event(new PortalsLeadsNotification("Lead", $lead, 'received', $user, $user, 1));
                    }
                }

                $community_property = null;
                $region = null;
                $community = null;
                $sub_community = null;
                foreach ($locations as $loop => $location) {
                    // if loop iterator is 1 then it is region if 2 then it is community if 3 then it is sub community and 4 then property type
                    if ($loop == 0) {
                        $region = Region::where('name', $location['attributes']['name'])->first();
                    } else if ($loop == 1) {
                        $community = Community::where('name', $location['attributes']['name'])->first();
                    } else if ($loop == 2) {
                        $sub_community = SubCommunity::where('name', $location['attributes']['name'])->first();
                    } else if ($loop == 3) {
                        $community_property = CommunityProperty::where('name', $location['attributes']['name'])->first();
                    }
                }

                $criteria = null;

                if ($property_ref) {
                    $criteria = getPropertyCriteria($property_ref);
                    if ((!$community) && $criteria['community_id']) {
                        $community = Community::find($criteria['community_id']);
                    }
                    if ((!$sub_community) && $criteria['subCommunity_id']) {
                        $sub_community = SubCommunity::find($criteria['subCommunity_id']);
                    }
                    if ((!$community_property) && $criteria['communityProperty']) {
                        $community_property = CommunityProperty::find($criteria['communityProperty']);
                    }
                }

                $leadNote = new LeadNote();
                $leadNote->lead_id = $lead->id;
                $leadNote->note = "Message: " . $message . '</br> <b>Criteria Details:</b> </br> <b>Region: </b> ' . ($region?->name ?? "N/A") . '</br>  <b>Community:</b> ' . ($community?->name ?? "N/A")  . '</br>  <b>Sub Community:</b> ' . ($sub_community?->name ?? "N/A") . '</br>  <b>Building/Property :</b> ' . ($community_property?->name ?? "N/A") . '</br>  <b>Tracking Link :</b> ' . $trackingLink;
                $leadNote->save();

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


                return response()->json(['status' => '1', 'msg' => 'Lead Saved successfully']);
                // dd($leadCriteria, $type, $property_ref, $id, $enquirerPhoneNumber, $language, $message, $receivedAt, $trackingLink, $agent, $locations);
            } else {
                // Handle JSON decoding error
                $result = array('status' => '0', 'msg' => 'Failed to decode JSON string!!');
            }
            return $result;
        } catch (\Exception $e) {
            $result = array('status' => '0', 'msg' => $e->getMessage());
            info('Data: ' . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
            $leadErrorWebhookGroupId = env('TELEGRAM_LEAD_WEBHOOK_ERROR_NOTIFICATION_ERROR_GROUP_ID') ?? '';
            $message = $this->sendTelegramMessage("An error occured in property finder webhook in save response function with error: " . $e->getMessage() . " on line: " . $e->getLine(), [$leadErrorWebhookGroupId], 'lead_bot');
            return $result;
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

    public function storeCriteria($arr)
    {
        // dd($arr);
        try {
            $communities = $arr['communities'];
            //$locations = $request->locations;

            if (LeadCriteria::where('lead_id', $arr['lead_id'])->exists()) {
                $leadCriteria = LeadCriteria::where('lead_id', $arr['lead_id'])->first();
                $flag = 'updated';
            } else {
                $leadCriteria = new LeadCriteria();
                $flag = 'created';
            }
            $leadCriteria->lead_id = $arr['lead_id'];
            // $leadCriteria->min_bed_id = $arr['min_bed_id'];
            // $leadCriteria->max_bed_id = $arr['max_bed_id'];
            // $leadCriteria->min_bath_id = $arr['min_bath_id'];
            // $leadCriteria->max_bath_id = $arr['max_bath_id'];
            // $leadCriteria->min_price = $arr['min_price'];
            // $leadCriteria->max_price = $arr['max_price'];
            $leadCriteria->communities = !empty($arr['sub_community_id']) ? $arr['sub_community_id'] : null;

            $leadCriteria->furnished = $arr['furnished'];
            $original = $leadCriteria->getOriginal() ?? [];
            $dirty = $leadCriteria->getDirty() ?? [];
            $leadCriteria->created_by_id = auth()->user()->id;

            if ($leadCriteria->save()) {
                if (LeadCriteriaLocation::where('lead_criteria_id', $leadCriteria->id)->exists()) {
                    //keeping record of old data on activity log
                    $original['community_ids'] = LeadCriteriaLocation::where('lead_criteria_id', $leadCriteria->id)->where('parentable_type', Community::class)->pluck('parentable_id')->toArray();
                    $original['sub_community_ids'] = LeadCriteriaLocation::where('lead_criteria_id', $leadCriteria->id)->where('parentable_type', SubCommunity::class)->pluck('parentable_id')->toArray();
                    $original['community_properties_ids'] = LeadCriteriaLocation::where('lead_criteria_id', $leadCriteria->id)->where('parentable_type', CommunityProperty::class)->pluck('parentable_id')->toArray();
                    LeadCriteriaLocation::where('lead_criteria_id', $leadCriteria->id)->delete(); // deleting old locations to prevent duplication
                }
                $community_ids = [];
                $sub_community_ids = [];
                $community_properties_ids = []; // for activity log
                foreach ($communities ?? [] as $community) {
                    if (LeadCriteriaLocation::where('parentable_type', Community::class)->where('parentable_id', $community)->where('lead_criteria_id', $leadCriteria->id)->exists()) {
                        continue;
                    } else {
                        $obj = new LeadCriteriaLocation();
                        $obj->parentable_type = Community::class;
                        $obj->parentable_id = $community;
                        $obj->lead_criteria_id = $leadCriteria->id;
                        $obj->save();
                        $community_ids[] = $community;
                    }
                }

                $sub_communities = $arr['sub_communities'];
                if (!empty($sub_communities) && is_array($sub_communities)) {
                    foreach ($sub_communities ?? [] as $sub_location) {
                        $sub_communities = $arr['sub_communities'];
                        if (!empty($sub_communities)) {
                            foreach ($sub_communities as $sub_location) {
                                $loc_arr = explode(':', $sub_location);
                                if ($loc_arr[1] == 'sub_community') {
                                    $loc_type = SubCommunity::class;
                                    $sub_community_ids[] = $loc_arr[0];
                                } else {
                                    $loc_type = CommunityProperty::class;
                                    $community_properties_ids[] = $loc_arr[0];
                                }
                                if (LeadCriteriaLocation::where('parentable_type', $loc_type)->where('parentable_id', $loc_arr[0])->where('lead_criteria_id', $leadCriteria->id)->exists()) {
                                    continue;
                                } else {
                                    $obj = new LeadCriteriaLocation();
                                    $obj->parentable_type = $loc_type;
                                    $obj->parentable_id = $loc_arr[0];
                                    $obj->lead_criteria_id = $leadCriteria->id;
                                    $obj->save();
                                }
                            }
                        }
                    }
                }
                //for activity log
                $dirty['community_ids'] = $community_ids;
                $dirty['sub_community_ids'] = $sub_community_ids;
                $dirty['community_properties_ids'] = $community_properties_ids;

                $arr = ['attributes' => $dirty, 'old' => $flag == 'created' ? [] : $original, 'lead_id' => $leadCriteria->lead_id];
                $leadCriteria->save();
                activity()
                    ->performedOn($leadCriteria)
                    ->causedBy(auth()->user())
                    ->withProperties($arr)
                    ->event($flag)
                    ->log(auth()->user()->first_name . ' has ' . $flag . ' lead criteria');
                // return true;
                return response()->json(['status' => '1', 'msg' => 'Lead criteria ' . $flag . ' successfully']);
            } else {
                // return false;

                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }
        } catch (\Exception $e) {
            // dd($exception);
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => 'Data: ' . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
            return response()->json($result, 500);
        }
        return json_encode($result);
    }



    // function for Abu Dhabi
    public function bayutAbuDhabi(Request $request, $innerKey)
    {
        $data = $request->all();
        info($data);
        if ($innerKey == '45b8f7a12c9e63d8aef2b09c4d03e7c2') {
            try {
                $apiKey = 'cadbd5f156701fb862a6353b6009daaf34650d16d1e9ddafbc6d0d874ff77625'; // New
                $body = file_get_contents('php://input');
                $combined_data = $apiKey . $body;
                $computedSignature = md5($combined_data, false);
                $signature = request()->header('X-Bayut-Signature') ?? '';
                TempBayutDump::create([
                    'data' => json_encode($data),
                ]);
                if ($signature !== $computedSignature) {
                    info('Signature is invalid');
                    throw new \RuntimeException('Invalid signature! Please check your API key.');
                } else {
                    $message = 'Bayut WA API  Webhook - Abu Dhabi';
                    $val = $this->saveBayutLead(json_encode($data), $message);

                    return response()->json(['status' => true, 'message' => 'success'], 200);
                }
            } catch (\Exception $e) {
                $leadErrorWebhookGroupId = env('TELEGRAM_LEAD_WEBHOOK_ERROR_NOTIFICATION_ERROR_GROUP_ID') ?? '';
                $message = $this->sendTelegramMessage("An error occured in Bayut webhook in bayutAbuDhabi function with error: " . $e->getMessage() . " on line: " . $e->getLine(), [$leadErrorWebhookGroupId], 'lead_bot');
                return response()->json(['status' => false, 'message' => $e->getMessage(), 'error' => $e->getMessage()], 500);
            } // end catch
        } else {
            return response()->json(['status' => false, 'message' => 'Unauthenticated'], 403);
        } // end else
        return response()->json(['status' => false, 'message' => 'Unauthenticated'], 403);
    }

    public function dubizzleAbuDhabi(Request $request, $innerKey)
    {
        $data = $request->all();
        if ($innerKey == 'e74d981f6ab325c8d7f0a6c23b91c548') {
            try {
                $apiKey = 'db934b2342b04cafacce28b9595468d2'; // New
                $body = file_get_contents('php://input');
                $signature = request()->header('X-Dubizzle-Signature') ?? '';
                $combined_data = $apiKey . $body;
                $computedSignature = md5($combined_data, false);
                TempDubizzleDump::create([
                    'data' => json_encode($data),
                ]);
                if ($signature !== $computedSignature) {
                    info('Signature is invalid');
                    throw new \RuntimeException('Invalid signature! Please check your API key.');
                } else {


                    $fixed_message = 'Dubizzle WA API  Webhook - Abu Dhabi';
                    $this->saveDubizzleLead(json_encode($data), $fixed_message);

                    return response()->json(['status' => true, 'message' => 'success'], 200);
                }
            } catch (\Exception $e) {
                return response()->json(['status' => false, 'message' => $e->getMessage(), 'error' => $e->getMessage()], 500);
            } // end catch
        } else {
            return response()->json(['status' => false, 'message' => 'Unauthenticated'], 403);
        } // end else
        return response()->json(['status' => false, 'message' => 'Unauthenticated'], 403);
    }

    public function bayutCommercial(Request $request, $innerKey)
    {

        $data = $request->all();
        info($data);
        if ($innerKey == '3c0a8e9b76f24d5a81d7b2e50487fca0') {
            try {
                $apiKey = '595338fb6cbf62b061f527b3264eee202dff846ffe0cd5102a221895dcf87c75'; // New
                $body = file_get_contents('php://input');
                $combined_data = $apiKey . $body;
                $computedSignature = md5($combined_data, false);
                $signature = request()->header('X-Bayut-Signature') ?? '';
                TempBayutDump::create([
                    'data' => json_encode($data),
                ]);
                if ($signature !== $computedSignature) {
                    info('Signature is invalid');
                    throw new \RuntimeException('Invalid signature! Please check your API key.');
                } else {

                    $fixed_message = 'Bayut WA API  Webhook - Commercial';
                    $val = $this->saveBayutLead(json_encode($data), $fixed_message);

                    return response()->json(['status' => true, 'message' => 'success'], 200);
                }
            } catch (\Exception $e) {
                $leadErrorWebhookGroupId = env('TELEGRAM_LEAD_WEBHOOK_ERROR_NOTIFICATION_ERROR_GROUP_ID') ?? '';
                $message = $this->sendTelegramMessage("An error occured in Bayut webhook in bayutCommercial function with error: " . $e->getMessage() . " on line: " . $e->getLine(), [$leadErrorWebhookGroupId], 'lead_bot');
                return response()->json(['status' => false, 'message' => $e->getMessage(), 'error' => $e->getMessage()], 500);
            } // end catch
        } else {
            return response()->json(['status' => false, 'message' => 'Unauthenticated'], 403);
        } // end else
        return response()->json(['status' => false, 'message' => 'Unauthenticated'], 403);
    }

    public function dubizzleCommercial(Request $request, $innerKey)
    {
        $data = $request->all();
        if ($innerKey == '1f906c6932a27auw366db20b0cf8f1eed') {
            try {
                $apiKey = 'db934b2342b04cafacce28b9595468d2'; // New
                $body = file_get_contents('php://input');
                $signature = request()->header('X-Dubizzle-Signature') ?? '';
                $combined_data = $apiKey . $body;
                $computedSignature = md5($combined_data, false);
                TempDubizzleDump::create([
                    'data' => json_encode($data),
                ]);
                if ($signature !== $computedSignature) {
                    info('Signature is invalid');
                    throw new \RuntimeException('Invalid signature! Please check your API key.');
                } else {
                    $fixed_message = 'Dubizzle WA API  Webhook - Commercial';
                    $this->saveDubizzleLead(json_encode($data), $fixed_message);

                    return response()->json(['status' => true, 'message' => 'success'], 200);
                }
            } catch (\Exception $e) {
                $leadErrorWebhookGroupId = env('TELEGRAM_LEAD_WEBHOOK_ERROR_NOTIFICATION_ERROR_GROUP_ID') ?? '';
                $message = $this->sendTelegramMessage("An error occured in Dubizzle webhook in dubizzleCommercial function with error: " . $e->getMessage() . " on line: " . $e->getLine(), [$leadErrorWebhookGroupId], 'lead_bot');
                return response()->json(['status' => false, 'message' => $e->getMessage(), 'error' => $e->getMessage()], 500);
            } // end catch
        } else {
            return response()->json(['status' => false, 'message' => 'Unauthenticated'], 403);
        } // end else
        return response()->json(['status' => false, 'message' => 'Unauthenticated'], 403);
    }
}
