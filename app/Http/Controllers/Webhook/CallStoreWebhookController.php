<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\CallLog;
use App\Models\ListingLead;
use App\Models\Contact;
use App\Models\Lead;
use App\Models\Medium;
use App\Models\Source;
use App\Models\TempCallDump;
use App\Models\Type;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CallStoreWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $data = $request->all();
        try {
            // info($request->all());
            // info('-------------------------');
            // Log::channel('mobileApi')->info('handle');
            $temp = new TempCallDump();
            $temp->data = json_encode($data);
            $temp->save();
            // info(json_encode($data));
            $res = $this->saveResponse(json_encode($data));
            return response()->json(['message' => 'Webhook received and being processed'], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Webhook received but failed to process', 'error' => $th->getMessage()], 500);
        }
    }

    public function saveResponse($response)
    {
        // Log::channel('mobileApi')->info('Save Response');

        try {
            //dd($response);
            $decodedData = json_decode($response, true);
            $data = $decodedData;
            // Log::channel('mobileApi')->info('Save Response', ['data' => $data]);
            if ($data) {
                $callEntry = $data;
                $client_mobile = $callEntry['client_number'];
                $callType = $callEntry['call_type'];
                $duration = $callEntry['call_duration'];
                $callerName = $callEntry['caller_name'] ?? null;
                $recording = $callEntry['call_recording'] ?? null;
                $direction = $callType ?? 0;

                // if (isset($callEntry['call_started_at'])) {
                //     $start_time = convertToDubaiTimezone($callEntry['call_started_at']);
                // } else {
                //     $start_time = null;
                // }

                $start_time = $callEntry['call_started_at'] ?? null;
                $start_time = isset($start_time) ? convertToDubaiTimezone($start_time) : null;


                $end_time = $callEntry['call_ended_at'] ?? null;
                $end_time = isset($end_time) ? convertToDubaiTimezone($end_time) : null;

                // if (isset($callEntry['call_ended_at'])) {
                //     $end_time = convertToDubaiTimezone($callEntry['call_ended_at']);
                // } else {
                //     $end_time = null;
                // }

                if (isset($callEntry['call_answered_at'])) {
                    $answer_time = $callEntry['call_answered_at'] ?? null;
                    $answer_time = isset($answer_time) ? convertToDubaiTimezone($answer_time) : null;
                } else {
                    $answer_time = null;
                }



                $answered = $answer_time ? 1 : 0;
                $vendor_user_id = $callEntry['user_id'];
                $agent_email = $callEntry['user_email'] ?? null;


                $number = $client_mobile;
                $user = User::where('email', $agent_email)->first();
                $isNewLead = false;
                $medium = Medium::where("name", "Call")->pluck("id")->first();
                $systemAgent = User::where('name', "System Agent")->first();

                $user_id = 7; // System Agent Id
                if ($user) {
                    $user_id = $user->id ?? 7;
                }

                //dd("here");
                // Log::channel('mobileApi')->info('Variables Done');

                if (!$client_mobile) {
                    // Log::channel('mobileApi')->info('New Log 1');

                    $obj = new CallLog();
                    $obj->lead_id = null;
                    $obj->contact_id = null;
                    $obj->user_id = null;
                    $obj->internal_user_id = null;
                    $obj->client_number = $number;
                    $obj->call_type = $direction == 0 ? 'INCOMING' : "OUTGOING";
                    $obj->call_started_at = $start_time;
                    $obj->call_answered_at = $answer_time;
                    $obj->call_ended_at = $end_time;
                    $obj->call_duration = $duration;
                    $obj->call_recording_url = $recording;
                    $obj->call_sid = $req['event']['db_call_id'] ?? null;
                    $obj->json_dump = json_encode($data);
                    $obj->save();
                    // Log::channel('mobileApi')->info('Log 1 Done', ['obj' => $obj]);

                    return response()->json(['status' => 'success', 'message' => 'Call log created successfully without number']);
                }

                // Checking if the mobile number already exists in our users table to determine if it is an internal call
                $existingUser = User::whereRaw("REPLACE(mobile, ' ', '') LIKE ?", ['%' . substr($client_mobile, -8) . '%'])->first();
                // Checking if the client number  exists in our contacts table to save it in the call log without creating a new lead
                //dd($user_id);
                $contact = Contact::whereRaw("REPLACE(mobile, ' ', '') LIKE ?", ['%' . substr($client_mobile, -8) . '%'])
                    ->where(function ($q) use ($user_id) {
                        $q->where('agent_id', $user_id)->orWhereHas('agents', function ($q) use ($user_id) {
                            $q->where('user_id', $user_id);
                        });
                    })
                    ->first();
                //dd($contact);

                if ($existingUser) {
                    return response()->json(['status' => 'success', 'message' => 'Call log not needed for Internal User']);
                }

                // Checking if the client number  exists in our leads table to save it in the call log without creating a new lead
                $lead = Lead::whereRaw("REPLACE(mobile, ' ', '') LIKE ?", ['%' . substr($client_mobile, -8) . '%'])
                    ->when($user_id, function ($q) use ($user_id) {
                        $q->where(function ($q) use ($user_id) {
                            $q->where('agent_id', $user_id)->orWhere('telesales_id', $user_id)->orWhere('telesales_head_id', $user_id);
                        });
                    })
                    ->first();

                if ($lead) {
                    // Log::channel('mobileApi')->info(' lead New Log 3');
                    $lead->status = 1; // Activate the lead if it is inactive
                    $lead->updated_at = now(); // Updating the lead time to come on top in the list
                    $lead->lead_medium_id = $lead->lead_medium_id != null ? $lead->lead_medium_id : $medium;
                    $lead->save();
                    // Log::channel('mobileApi')->info(' lead New Log 3 Done', ['obj' => $lead]);

                    // If Role is either telesales or telesales head and the lead already exists in the system then create a call log and don't create a new lead
                    if ($user?->hasRole(["Telesales", "Telesales Head"])) {
                        // Log::channel('mobileApi')->info(' lead New Log 4');
                        $obj = new CallLog();
                        $obj->contact_id = $contact->id ?? null;
                        $obj->user_id = $user_id ?? null;
                        $obj->client_number = $client_mobile;
                        $obj->call_type = $direction == 0 ? 'INCOMING' : "OUTGOING";
                        $obj->call_started_at = $start_time;
                        $obj->call_answered_at = $answer_time;
                        $obj->call_ended_at = $end_time;
                        $obj->call_duration = $duration;
                        $obj->call_recording_url = $recording;
                        $obj->call_sid = $req['event']['db_call_id'] ?? null;
                        $obj->json_dump = json_encode($data);
                        $obj->save();
                        // Log::channel('mobileApi')->info(' lead New Log 4 Done', ['obj' => $obj]);
                        $leads = Lead::whereRaw("REPLACE(mobile, ' ', '') LIKE ?", ['%' . substr($client_mobile, -8) . '%'])->where(function ($q) use ($user_id) {
                            $q->where('agent_id', $user_id)->orWhere('telesales_id', $user_id)->orWhere('telesales_head_id', $user_id);
                        })->get();
                        if ($leads) {
                            $obj->leads()->sync($leads);
                        }
                        // Log::channel('mobileApi')->info(' lead New Log 5 Return');
                        return response()->json(['status' => 'success', 'message' => 'Call log created successfully in the begining', "call log" => $obj]);
                    }
                }

                $listingLead = ListingLead::whereRaw("REPLACE(mobile, ' ', '') LIKE ?", ['%' . substr($client_mobile, -8) . '%'])->where('agent_id', $user_id)->first();

                // Checking if the client number  exists in our leads table to save it in the call log without creating a new lead
                if (!$lead && !$contact && !$listingLead &&  !$user?->hasRole(["Telesales", "Telesales Head", "Admin"]) && $direction != 1 && (!$user || ($user->call_lead))) {
                    // Log::channel('mobileApi')->info('New Call Log 6');
                    $lead = new Lead;
                    $isNewLead = true;
                    $lead->first_name = "New";
                    $lead->mobile = $client_mobile;
                    $lead->whatsapp = $client_mobile;
                    $lead->agent_id = $user?->id; // Keep it empty if user not found
                    $lead->lead_status_id = 1; // Open
                    // $lead->lead_sub_status_id = 8;  // Unspecified
                    $lead->for = 'Unspecified';
                    $lead->lead_medium_id = $medium ?? null;

                    $source = Source::where('name', 'LIKE', '%Unspecified%')->first();
                    $typed = Type::where('name', 'LIKE', '%Unspecified%')->first();

                    $lead->source_id = $source->id ?? 50; // Call - Unspecified
                    $lead->type_id = $typed->id ?? 11; // Unspecified
                    $lead->status = 1;
                    $lead->save();
                    // Log::channel('mobileApi')->info('New Call Log 6 Done', ['obj' => $lead]);
                    event(new \App\Events\LeadCreated($lead));
                } elseif (!$lead && $user?->hasRole(["Telesales", "Telesales Head", "Admin"])) {
                    return response()->json(['status' => 'success', 'message' => 'Call log not needed']);
                }

                // Log::channel('mobileApi')->info('New Call Log 8');
                // Creating the call Log here
                $obj = new CallLog();
                $obj->contact_id = $contact->id ?? null;
                $obj->user_id = $user?->id ?? 7;
                $obj->internal_user_id = $existingUser->id ?? null;
                $obj->client_number = $client_mobile;
                $obj->call_type = $direction == 0 ? 'INCOMING' : "OUTGOING";
                $obj->call_started_at = $start_time;
                $obj->call_answered_at = $answer_time;
                $obj->call_ended_at = $end_time;
                $obj->call_duration = $duration;
                $obj->call_recording_url = $recording;
                $obj->call_sid = $req['event']['db_call_id'] ?? null;
                $obj->json_dump = json_encode($data);
                $obj->save();
                // Log::channel('mobileApi')->info('New Call Log 8 Done', ['obj' => $obj]);
                $obj->refresh();

                // Saving the call log to all Leads where the number exists
                $leads = Lead::whereRaw("REPLACE(mobile, ' ', '') LIKE ?", ['%' . substr($client_mobile, -8) . '%'])->where(function ($q) use ($user_id) {
                    $q->where('agent_id', $user_id)->orWhere('telesales_id', $user_id)->orWhere('telesales_head_id', $user_id);
                })->get();

                $listingLeads = ListingLead::whereRaw("REPLACE(mobile, ' ', '') LIKE ?", ['%' . substr($client_mobile, -8) . '%'])->where('agent_id', $user_id)->get();
                if ($leads) {
                    $obj->leads()->sync($leads);
                }
                if ($listingLeads) {
                    $obj->listingLeads()->sync($listingLeads);
                }

                // Direction 0 means Incoming call
                // Direction 1 means Outgoing call
                if (!$isNewLead && $lead) {
                    if ($answered == 1) {
                        if ($lead->lead_status_id == 1 || $lead->lead_status_id == 10) { // New Or Not Answered Lead
                            $lead->lead_status_id = 5; // Contacted
                            $lead->save();
                        }
                    } elseif ($answered == 0) {
                        if ($lead->lead_status_id == 1) {
                            $lead->lead_status_id = 5; // Not Answered
                            $lead->save();
                        }
                        // $lead->lead_sub_status_id = 5; // Called No Reply
                        //$lead->save();
                    }
                }
                if ($listingLead) {
                    if ($answered == 1) {
                        if ($listingLead->status_id == 2 || $listingLead->status_id == 5) { // Assigned Or Not Answered Listing leads
                            $listingLead->status_id = 3; // Contacted
                            $listingLead->save();
                        }
                    } elseif ($answered == 0) {
                        if ($listingLead->status_id == 2) {
                            $listingLead->status_id = 5; // Not Answered
                            $listingLead->save();
                        }
                        // $lead->lead_sub_status_id = 5; // Called No Reply
                        //$lead->save();
                    }
                }
                return response()->json(['status' => 'success', 'message' => 'Call log created successfully at the end']);
            }
        } catch (\Throwable $th) {
            info($th);
        }
    }

    public function dialANumber(Request $request)
    {

        $email = 's.bahar@axcapital.ae';
        $phone = '+971585858176';
        $name = 'Fraz Shabbir';

        $url = "https://telelog.axcrm.ae/api/dial-number";
        $client = new Client();
        $data = [
            'user_email' => $email,
            'phone' => $phone,
            'name' => $name,
        ];
        try {
            $response = $client->post($url, [
                'form_params' => $data,
            ]);

            $statusCode = $response->getStatusCode();
            $responseBody = $response->getBody()->getContents();
            info($responseBody);
        } catch (RequestException $e) {
            info($e);
            if ($e->hasResponse()) {
                $statusCode = $e->getResponse()->getStatusCode();
                $responseBody = $e->getResponse()->getBody()->getContents();
            } else {
                info('-----');
            }
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_email' => 'required|exists:users,email',
            'call_recording' => 'nullable',
            'client_number' => 'required',
            'call_type' => 'required',
            'call_duration' => 'required',
            'call_started_at' => 'required',
            'call_ended_at' => 'required',
            'call_answered_at' => 'nullable',
            'caller_name' => 'nullable',
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(false, 'Validation Error', $validator->errors(), 422);
        }

        $user = User::where('email', $request->user_email)->first();
        $request->merge(['user_id' => $user->id]);
        $request->merge(['entity_id' => $user->entity_id]);
        $call = Call::create($request->all());
        SendCallLogToWebhooksJob::dispatch($user->entity_id, $call);
        return $this->sendResponse('success', 'Call log saved successfully', $call, 200);
    }
}
