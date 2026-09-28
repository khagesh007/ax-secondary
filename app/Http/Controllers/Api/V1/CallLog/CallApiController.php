<?php

namespace App\Http\Controllers\Api\V1\CallLog;

use App\Http\Controllers\Api\BaseController;
use App\Models\CallLog;
use App\Models\Contact;
use App\Models\Lead;
use App\Models\ListingLead;
use App\Models\Medium;
use App\Models\Source;
use App\Models\Type;
use App\Models\User;
use App\Traits\FileTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Validator;

class CallApiController extends BaseController
{
    use FileTrait;
    public function __construct()
    {

    }

    /**
     * Store a Call log
     * @group Call Log
     * @authenticated

     * @bodyParam user_id int required Id of the User/Agent. Example: 78
     * @bodyParam client_number string required  client_number for the caller. Example: +971 55 123 4567
     * @bodyParam call_type boolean required  call_type for the call [1 for Incoming and 0 for Outgoing]. Example: [1 for Incoming and 0 for Outgoing]
     * @bodyParam call_duration string required  call_duration for the call. Example: 00:00:00

     * @bodyParam call_started_at string required  call_started_at for the call. Example: 2021-08-10 00:00:00
     * @bodyParam call_ended_at string required  call_ended_at for the call. Example: 2021-08-10 00:00:00
     * @bodyParam call_answered_at string optional  call_answered_at for the call. Example: 2021-08-10 00:00:00
     * @bodyParam caller_name string  caller_name for the call. Example: John Doe
     * @bodyParam call_recording Call Recording of the user. Example: recording.mp3

     * @response 200 scenario=success
     * {
    "status": "success",
    "message": "Call log created successfully",
    "data": {
    "id": 75225,
    "lead_id": 44387,
    "lead_follow_up_id": null,
    "contact_id": null,
    "contact_follow_up_id": null,
    "client_number": "223344",
    "call_type": "OUTGOING",
    "internal_user_id": null,
    "user_id": 1,
    "call_started_at": "1970-01-01 04:35:13",
    "call_answered_at": "1970-01-01 04:35:13",
    "call_ended_at": "1970-01-01 04:35:13",
    "call_duration": "99",
    "call_status": null,
    "call_sid": null,
    "call_recording_url": "https://s3.eu-west-1.amazonaws.com/secondary.crm/documents/calls/wj5nyAYxyzgD5DDC0ahEU6Q502883bEhzqhz8va1.mp3",
    "call_recording_sid": null,
    "json_dump": "{\"user_id\":\"1\",\"client_number\":\"223344\",\"call_started_at\":\"2023-02-27 13:19:29\",\"call_ended_at\":\"2023-02-27 13:19:29\",\"call_answered_at\":\"2023-02-27 13:19:29\",\"call_type\":\"1\",\"call_answered\":\"0\",\"call_duration\":\"99\",\"call_date\":\"2\",\"call_time\":\"2\",\"action\":\"call.finish\",\"call_recording\":{}}",
    "created_at": "2023-05-10T05:02:24.000000Z",
    "updated_at": "2023-05-10T05:02:24.000000Z"
    }
     *}
     */
    public function store(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
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

            if ($request->action == 'call.finish') {
                $userID = $request->user_id;
                $number = $request->client_number ?? null;

                if (checkBlocklist($number, CallLog::class, $request)) {
                    $result = array('status' => '0', 'msg' => 'This number is in Blacklist. Please do not contact with this number.', 'error' => 'This number is in Blacklist. Please do not contact with this number.');
                    return json_encode($result);
                }

                $direction = $request->call_type ?? 0;
                $client_mobile = validatePhoneNumber($number);
                $start_time = $request->call_started_at;
                // "!empty($request->call_started_at) ? Carbon::createFromTimestamp($request->call_started_at) : null;"
                $end_time = $request->call_ended_at;
                // "!empty($request->call_ended_at) ? Carbon::createFromTimestamp($request->call_ended_at) : null;"
                $answer_time = $request->call_answered_at ?? null;
                // "!empty($request->call_answered_at) ? Carbon::createFromTimestamp($request->call_answered_at) : null;"
                $duration = $request->call_duration ?? null;
                $answered = $request->call_answered ?? 0;

                $user = User::where('id', $userID)->first();
                $isNewLead = false;
                $medium = Medium::where("name", 'LIKE', "%Call%")->pluck("id")->first();
                $systemAgent = User::where('name', "System Agent")->first();
                $user_id = 7; // System Agent Id
                if ($user) {
                    $user_id = $user->id;
                }
                $recording = $request->call_recording ?? null;

                if (!$client_mobile) {
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
                    $obj->json_dump = json_encode($request->all());
                    $obj->save();
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
                    $obj = new CallLog();
                    $obj->lead_id = null;
                    $obj->contact_id = null;
                    $obj->user_id = $user->id ?? 7; //Assigning System agent if the
                    $obj->internal_user_id = $existingUser->id ?? null;
                    $obj->client_number = $client_mobile;
                    $obj->call_type = $direction == 0 ? 'INCOMING' : "OUTGOING";
                    $obj->call_started_at = $start_time;
                    $obj->call_answered_at = $answer_time;
                    $obj->call_ended_at = $end_time;
                    $obj->call_duration = $duration;
                    $obj->call_recording_url = $recording;
                    $obj->call_sid = $req['event']['db_call_id'] ?? null;
                    $obj->json_dump = json_encode($request->all());
                    $obj->save();
                    return response()->json(['status' => 'success', 'message' => 'Call log created successfully without lead']);
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
                    $lead->status = 1; // Activate the lead if it is inactive
                    $lead->updated_at = now(); // Updating the lead time to come on top in the list
                    $lead->lead_medium_id = $lead->lead_medium_id != null ? $lead->lead_medium_id : $medium;
                    $lead->save();

                    // If Role is either telesales or telesales head and the lead already exists in the system then create a call log and don't create a new lead
                    if ($user?->hasRole(["Telesales", "Telesales Head"])) {
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
                        $obj->json_dump = json_encode($request->all());
                        $obj->save();

                        $leads = Lead::whereRaw("REPLACE(mobile, ' ', '') LIKE ?", ['%' . substr($client_mobile, -8) . '%'])->where(function ($q) use ($user_id) {
                            $q->where('agent_id', $user_id)->orWhere('telesales_id', $user_id)->orWhere('telesales_head_id', $user_id);
                        })->get();
                        if ($leads) {
                            $obj->leads()->sync($leads);
                        }

                        return response()->json(['status' => 'success', 'message' => 'Call log created successfully in the begining', "call log" => $obj]);
                    }
                }

                // Checking if the client number  exists in our leads table to save it in the call log without creating a new lead
                if (!$lead && !$contact && !$existingUser && !$user?->hasRole(["Telesales", "Telesales Head"]) && $direction != 1 && (!$user || ($user->call_tracking))) {
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
                    event(new \App\Events\LeadCreated($lead));

                } elseif (!$lead && $user?->hasRole(["Telesales", "Telesales Head"])) {
                    return response()->json(['status' => 'success', 'message' => 'Call log not needed']);
                }

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
                $obj->json_dump = json_encode($request->all());
                $obj->save();
                $obj->refresh();

                // Saving the call log to all Leads where the number exists
                $leads = Lead::whereRaw("REPLACE(mobile, ' ', '') LIKE ?", ['%' . substr($client_mobile, -8) . '%'])->where(function ($q) use ($user_id) {
                    $q->where('agent_id', $user_id)->orWhere('telesales_id', $user_id)->orWhere('telesales_head_id', $user_id);
                })->get();
                if ($leads) {
                    $obj->leads()->sync($leads);
                }
                $listingLeads = ListingLead::whereRaw("REPLACE(mobile, ' ', '') LIKE ?", ['%' . substr($client_mobile, -8) . '%'])->where('agent_id', $user_id)->get();

                if ($listingLeads) {
                    $obj->listingLeads()->sync($listingLeads);
                }

                // Direction 0 means Incoming call
                // Direction 1 means Outgoing call
                if (!$isNewLead && $lead) {
                    if ($answered == 1 && $direction == 1) {
                        $lead->lead_status_id = 1; // Open
                        //4 is not yet contacted

                        // $lead->lead_sub_status_id = in_array($lead->lead_sub_status_id, [4,5,8]) ? 1 :$lead->lead_sub_status_id; // In progress
                        $lead->save();
                    } elseif ($answered == 0) {
                        $lead->lead_status_id = 1; // Open
                        // $lead->lead_sub_status_id = 5; // Called No Reply
                        $lead->save();
                    }
                }
                return $this->sendResponse(true, 'Call log created successfully', $obj, 200);

                // return response()->json(['status' => 'success', 'message' => 'Call log created successfully', 'data' => $obj]);
            }
        } catch (\Throwable $th) {
            Log::channel('discord')->error("Error in store function in CallApiController.php " . $th->getMessage());

            return $this->sendResponse(false, $th->getMessage(), null, 400);
        }
        // return response()->json(['status' => 'success', 'message' => 'Call log created successfully']);

        return $this->sendResponse(true, 'No Action Found', null, 200);
    }

    /**
     * Upload Recording
     * @group Call Log
    @authenticated

     */
    public function uploadRecording(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'call_recording' => 'required|file',
            ]);

            if ($validator->fails()) {
                return $this->sendResponse(false, 'Validation Error', $validator->errors(), 422);
            }

            if ($request->file("call_recording")) {
                $recording = $this->uploadDocument($request->file("call_recording"), "test_calls", 's3');
                return $this->sendResponse(true, 'Recording Uplaoded successfully', $recording, 200);
            }
            return $this->sendResponse(false, 'File not uploaded', null, 400);

        } catch (\Throwable $th) {
            Log::channel('discord')->error("Error in uploadRecording function in CallApiController.php " . $th->getMessage());

            return $this->sendResponse(false, $th->getMessage(), null, 400);
        }

    }
    public function callFinish(Request $request)
    {

        $req = $request->json()->all();
        if ($req['webhook']['action'] == 'call.finish') {
            $agent_email = $req['webhook']['user_login'] ?? null;
            $vendor_user_id = $req['webhook']['user_id'] ?? null;
            $account_id = $req['webhook']['account_id'] ?? null;
            $account_name = $req['webhook']['account_name'] ?? null;
            $number = $req['event']['client_number'] ?? null;

            if (checkBlocklist($number, CallLog::class, $request)) {
                $result = array('status' => '0', 'msg' => 'This number is in Blacklist. Please do not contact with this number.', 'error' => 'This number is in Blacklist. Please do not contact with this number.');
                return json_encode($result);
            }

            $client_mobile = validatePhoneNumber($number);
            $recording = $req['event']['recording'] ?? null;
            $direction = $req['event']['direction'] ?? 0;
            $start_time = !empty($req['event']['start_time']) ? Carbon::createFromTimestamp($req['event']['start_time']) : null;
            $end_time = !empty($req['event']['end_time']) ? Carbon::createFromTimestamp($req['event']['end_time']) : null;
            $answer_time = !empty($req['event']['answer_time']) ? Carbon::createFromTimestamp($req['event']['answer_time']) : null;
            $duration = $req['event']['duration'] ?? null;
            $answered = $req['event']['answered'] ?? 0;
            $user = User::where('email', $agent_email)->first();
            $isNewLead = false;
            $medium = Medium::where("name", "Call")->pluck("id")->first();

            $systemAgent = User::where('name', "System Agent")->first();

            $user_id = 7; // System Agent Id
            if ($user) {
                $user_id = $user->id ?? 7;
            }

            if (!$client_mobile || $number == "+441156477354" || $number == "+447360277710" || $number == "+447888863655") {
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
                $obj->json_dump = json_encode($request->all());
                $obj->save();
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
                $obj = new CallLog();
                $obj->lead_id = null;
                $obj->contact_id = null;
                $obj->user_id = $user->id ?? 7; //Assigning System agent if the
                $obj->internal_user_id = $existingUser->id ?? null;
                $obj->client_number = $client_mobile;
                $obj->call_type = $direction == 0 ? 'INCOMING' : "OUTGOING";
                $obj->call_started_at = $start_time;
                $obj->call_answered_at = $answer_time;
                $obj->call_ended_at = $end_time;
                $obj->call_duration = $duration;
                $obj->call_recording_url = $recording;
                $obj->call_sid = $req['event']['db_call_id'] ?? null;
                $obj->json_dump = json_encode($request->all());
                $obj->save();
                return response()->json(['status' => 'success', 'message' => 'Call log created successfully without lead']);
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
                $lead->status = 1; // Activate the lead if it is inactive
                $lead->updated_at = now(); // Updating the lead time to come on top in the list
                $lead->lead_medium_id = $lead->lead_medium_id != null ? $lead->lead_medium_id : $medium;
                $lead->save();

                // If Role is either telesales or telesales head and the lead already exists in the system then create a call log and don't create a new lead
                if ($user?->hasRole(["Telesales", "Telesales Head"])) {
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
                    $obj->json_dump = json_encode($request->all());
                    $obj->save();

                    $leads = Lead::whereRaw("REPLACE(mobile, ' ', '') LIKE ?", ['%' . substr($client_mobile, -8) . '%'])->where(function ($q) use ($user_id) {
                        $q->where('agent_id', $user_id)->orWhere('telesales_id', $user_id)->orWhere('telesales_head_id', $user_id);
                    })->get();
                    if ($leads) {
                        $obj->leads()->sync($leads);
                    }
                    $listingLeads = ListingLead::whereRaw("REPLACE(mobile, ' ', '') LIKE ?", ['%' . substr($client_mobile, -8) . '%'])->where('agent_id', $user_id)->get();

                    if ($listingLeads) {
                        $obj->listingLeads()->sync($listingLeads);
                    }

                    return response()->json(['status' => 'success', 'message' => 'Call log created successfully in the begining', "call log" => $obj]);
                }
            }

            // Checking if the client number  exists in our leads table to save it in the call log without creating a new lead
            $listingLead = ListingLead::whereRaw("REPLACE(mobile, ' ', '') LIKE ?", ['%' . substr($client_mobile, -8) . '%'])->where('agent_id', $user_id)->first();
            if (!$lead && !$contact && !$existingUser && !$listingLead && !$user?->hasRole(["Telesales", "Telesales Head"]) && $direction != 1 && (!$user || ($user->call_tracking))) {
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
                event(new \App\Events\LeadCreated($lead));

            } elseif (!$lead && $user?->hasRole(["Telesales", "Telesales Head"])) {
                return response()->json(['status' => 'success', 'message' => 'Call log not needed']);
            }

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
            $obj->json_dump = json_encode($request->all());
            $obj->save();
            $obj->refresh();

            // Saving the call log to all Leads where the number exists
            $leads = Lead::whereRaw("REPLACE(mobile, ' ', '') LIKE ?", ['%' . substr($client_mobile, -8) . '%'])->where(function ($q) use ($user_id) {
                $q->where('agent_id', $user_id)->orWhere('telesales_id', $user_id)->orWhere('telesales_head_id', $user_id);
            })->get();
            if ($leads) {
                $obj->leads()->sync($leads);
            }

            // Direction 0 means Incoming call
            // Direction 1 means Outgoing call
            if (!$isNewLead && $lead) {
                if ($answered == 1 && $direction == 1) {
                    $lead->lead_status_id = 1; // Open
                    //4 is not yet contacted

                    // $lead->lead_sub_status_id = in_array($lead->lead_sub_status_id, [4,5,8]) ? 1 :$lead->lead_sub_status_id; // In progress
                    $lead->save();
                } elseif ($answered == 0) {
                    $lead->lead_status_id = 1; // Open
                    // $lead->lead_sub_status_id = 5; // Called No Reply
                    $lead->save();
                }
            }
            return response()->json(['status' => true, 'message' => 'Call log created successfully at the end']);

        }
        return response()->json(['status' => true, 'message' => 'Lead created successfully']);
    }

    public function formatPhoneNumber($phoneNumber)
    {
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber); // remove non-digit characters

        if (substr($phoneNumber, 0, 2) == '05') {
            $phoneNumber = '+971' . substr($phoneNumber, 1);
        } elseif (substr($phoneNumber, 0, 1) == '5') {
            $phoneNumber = '+9715' . substr($phoneNumber, 1);
        } elseif (substr($phoneNumber, 0, 3) == '971') {
            $phoneNumber = '+' . $phoneNumber;
        } elseif (substr($phoneNumber, 0, 5) == '00971') {
            $phoneNumber = '+971' . substr($phoneNumber, 5);
        }

        return $phoneNumber;
    }

    /**
     * Get Call Logs
     * @group Call Log
     * @authenticated

     * @response 200 scenario=success
    {
    "status": true,
    "code": 200,
    "message": "Call Logs",
    "data": [
    {
    "id": 36239,
    "lead_id": 16563,
    "lead_follow_up_id": null,
    "contact_id": null,
    "contact_follow_up_id": null,
    "client_number": "+971588159646",
    "call_type": "OUTGOING",
    "internal_user_id": null,
    "user_id": 172,
    "call_started_at": "2023-04-07 11:28:40",
    "call_answered_at": null,
    "call_ended_at": "2023-04-07 11:28:50",
    "call_duration": "0",
    "call_status": null,
    "call_sid": "38798",
    "call_recording_url": null,
    "call_recording_sid": null,
    "json_dump": "{\"event\": {\"src_id\": null, \"answered\": 0, \"duration\": 0, \"end_time\": 1680866930, \"src_slot\": null, \"direction\": 1, \"recording\": null, \"user_name\": \"Saim Agent\", \"db_call_id\": 38798, \"event_type\": 4, \"src_number\": null, \"start_time\": 1680866920, \"answer_time\": 0, \"client_name\": null, \"event_grant\": 1, \"upload_time\": 1680866935, \"client_number\": \"+971588159646\", \"event_created\": 1680866935, \"user_group_id\": 2, \"event_expiration\": 0, \"event_pbx_call_id\": \"141.1680866920\"}, \"webhook\": {\"action\": \"call.finish\", \"user_id\": 141, \"account_id\": 67616, \"user_login\": \"smirza277@gmail.com\", \"account_name\": \"axsecondary\"}}",
    "created_at": "2023-04-07T07:28:59.000000Z",
    "updated_at": "2023-05-12T14:16:11.000000Z",
    "deleted_at": null,
    "need_to_be_destroyed": 0,
    "user": {
    "id": 172,
    "bitrix_role": null,
    "entity": null,
    "group_id": null,
    "parent_id": 1310,
    "public_profile_id": null,
    "old_user_id": null,
    "slug": "muhammad-saim-agent-aziz",
    "title": null,
    "name": "Muhammad Saim Agent Aziz",
    "first_name": "Muhammad Saim Agent",
    "last_name": "Aziz",
    "position": "Dev",
    "email": "smirza277@gmail.com",
    "secondary_email": "smirza277@gmail.com",
    "email_verified_at": null,
    "last_login": "2024-09-12 15:07:32",
    "mobile": "+971588159645",
    "api_access": 0,
    "api_rate_limit": 0,
    "telegram_username": null,
    "telegram_chatid": "5970238984",
    "call_tracking_user_id": null,
    "call_tracking_account_id": null,
    "bitrix_id": null,
    "website_link": null,
    "facebook_link": null,
    "instagram_link": null,
    "youtube_link": null,
    "linkedin_link": null,
    "bio": null,
    "license_no": null,
    "pp_enabled": null,
    "pp_name": null,
    "pp_photo": null,
    "pp_license_no": null,
    "country_id": null,
    "state_id": null,
    "nationality_id": null,
    "community_id": null,
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/thumb_168457219720230520124317.jpg",
    "image": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/168457219720230520124317.jpg",
    "avatar": null,
    "color_code": null,
    "status": 1,
    "region_id": 1,
    "traccar_status": 1,
    "device_identifier": "172",
    "port": "5055",
    "mic_status": 1,
    "device_token": "eeHUT4cDSpCXSLuXS9Vfnb:APA91bG25Q-F8MrTCeb7bP9wwCyUDQERFVChZ2rNZhjAm-OurL58LN8AcCfrI_1DeAaVAZ8J9wJccnEtQ3bfC9NMW7UnxohuQZzCixslZjwN3U5yt9uEAMgqsuTLCCPMzbE2fK7BUNLl",
    "call_tracking": 0,
    "call_lead": 0,
    "is_commercial": 0,
    "created_by_id": 1,
    "updated_by_id": 172,
    "otp": "628061",
    "created_at": "2023-02-15T06:42:31.000000Z",
    "updated_at": "2024-09-12T11:07:32.000000Z",
    "deleted_at": null,
    "google2fa_secret": null,
    "telegram2fa_secret": null,
    "back_up_google2fa_secret": "TMMA5SAN6HDMHEQT",
    "login_attempt": 0,
    "joining_date": "2019-02-28 11:39:59",
    "last_deal_date": null,
    "is_report_exempt": 0,
    "zone_id": 5,
    "full_name": "Muhammad Saim Agent Aziz",
    "thumb_url": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/thumb_168457219720230520124317.jpg",
    "image_url": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/168457219720230520124317.jpg",
    "role_list": [
    "Agent"
    ]
    },
    "lead": null
    }
    ],
    "page": 1,
    "per_page": 10
    }
     */

    public function index(Request $request)
    {
        if(!auth()->user()){
            return $this->sendResponse(false, 'User not found', null, 400);
        }
        $callLogs = CallLog::with(['user', 'lead'])->where('user_id', auth()->user()->id)->get();
        return $this->sendResponse(true, 'Call Logs', $callLogs, 200);
    }
}

// DB::beginTransaction();

// $path = null;
// if ($request->file("call_recording")) {
//     $path = $this->uploadDocument($request->file("call_recording"), "calls");
// }
// $already_lead = Lead::where('mobile', $request->client_number)->first();

// if (!$already_lead) {
//     $client_name = $request->client_name ?? $request->client_number;
//     $lead = new Lead();
//     $lead->first_name = $request->client_number;
//     $lead->mobile = $request->client_number;
//     $lead->save();
//     $lead_id = $lead->id;
// } else {
//     $lead_id = $already_lead->id;
// }
// $already_contact = Contact::where('mobile', $request->client_number)->first();
// if (!$already_contact) {
//     $client_name = $request->client_name ?? $request->client_number;
//     $contact = new contact();
//     $contact->first_name = $request->client_number;
//     $contact->mobile = $request->client_number;
//     $contact->save();
//     $contact_id = $contact->id;
// } else {
//     $contact_id = $already_contact->id;
// }

// //
