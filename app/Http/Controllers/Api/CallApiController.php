<?php

namespace App\Http\Controllers\Api;

use App\Models\Contact;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CallLog;
use App\Models\Lead;
use App\Models\ListingLead;
use App\Models\Source;
use App\Models\Type;
use App\Models\LeadNote;
use App\Models\Medium;
use App\Models\User;
use Carbon\Carbon;

class CallApiController extends Controller
{

    public function newEntry(Request $request)
    {
       $req = $request->json()->all();

        $client_mobile = validatePhoneNumber($req['event']['client_number'] ?? null);

        $lead = Lead::where('mobile', $client_mobile)->first();
        if (!empty($lead)) {
            LeadNote::create([
                "lead_id" => $lead->id,
                "note" => json_encode($request->note)
            ]);
        } else {
            $obj = new Lead();
          //  $obj->first_name = $request->name;
          //    $obj->note = json_encode($request->all());
            if ($obj->save()) {
                LeadNote::create([
                    "lead_id" => $obj->id,
                    "note" => json_encode($request->note)

                ]);

                return response()->json(['status' => 'success', 'message' => 'Lead created successfully']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Lead not created']);
            }
        }
    }
    public function callFinish(Request $request)
    {
        $req = $request->json()->all();
        if($req['webhook']['action'] == 'call.finish'){
            $agent_email = $req['webhook']['user_login'] ?? null;
            $vendor_user_id = $req['webhook']['user_id'] ?? null;
            $account_id = $req['webhook']['account_id'] ?? null;
            $account_name = $req['webhook']['account_name'] ?? null;
            $number = $req['event']['client_number'] ?? null;
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
            if($user){
                $user_id = $user->id ?? 7;
            }

            if(!$client_mobile || $number == "+441156477354" || $number == "+447360277710" || $number =="+447888863655"){
                $obj = new CallLog();
                $obj->lead_id           = null;
                $obj->contact_id        = null;
                $obj->user_id           = null;
                $obj->internal_user_id  = null;
                $obj->client_number     = $number;
                $obj->call_type         = $direction == 0 ? 'INCOMING' : "OUTGOING";
                $obj->call_started_at   = $start_time;
                $obj->call_answered_at  = $answer_time;
                $obj->call_ended_at     = $end_time;
                $obj->call_duration     = $duration;
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
                ->where(function($q) use ($user_id) {
                    $q->where('agent_id', $user_id)->orWhereHas('agents', function($q) use ($user_id) {
                        $q->where('user_id', $user_id);
                    });
                })
                ->first();
            //dd($contact);

            if($existingUser){
                $obj = new CallLog();
                $obj->lead_id           = null;
                $obj->contact_id        = null;
                $obj->user_id           = $user->id ?? 7; //Assigning System agent if the
                $obj->internal_user_id  = $existingUser->id ?? null;
                $obj->client_number     = $client_mobile;
                $obj->call_type         = $direction == 0 ? 'INCOMING' : "OUTGOING";
                $obj->call_started_at   = $start_time;
                $obj->call_answered_at  = $answer_time;
                $obj->call_ended_at     = $end_time;
                $obj->call_duration     = $duration;
                $obj->call_recording_url = $recording;
                $obj->call_sid = $req['event']['db_call_id'] ?? null;
                $obj->json_dump = json_encode($request->all());
                $obj->save();
                return response()->json(['status' => 'success', 'message' => 'Call log created successfully without lead']);
            }

            // Checking if the client number  exists in our leads table to save it in the call log without creating a new lead
            $lead = Lead::whereRaw("REPLACE(mobile, ' ', '') LIKE ?", ['%' . substr($client_mobile, -8) . '%'])
                ->when($user_id, function($q) use($user_id){
                    $q->where(function($q) use($user_id){
                        $q->where('agent_id', $user_id)->orWhere('telesales_id', $user_id)->orWhere('telesales_head_id', $user_id);
                    });
                })
                ->first();

            if($lead){
                $lead->status = 1; // Activate the lead if it is inactive
                $lead->updated_at = now(); // Updating the lead time to come on top in the list
                $lead->lead_medium_id = $lead->lead_medium_id != null ? $lead->lead_medium_id : $medium;
                $lead->save();

                // If Role is either telesales or telesales head and the lead already exists in the system then create a call log and don't create a new lead
                if($user?->hasRole(["Telesales", "Telesales Head"])){
                    $obj = new CallLog();
                    $obj->contact_id        = $contact->id ?? null;
                    $obj->user_id           = $user_id ?? null;
                    $obj->client_number     = $client_mobile;
                    $obj->call_type         = $direction == 0 ? 'INCOMING' : "OUTGOING";
                    $obj->call_started_at   = $start_time;
                    $obj->call_answered_at  = $answer_time;
                    $obj->call_ended_at     = $end_time;
                    $obj->call_duration     = $duration;
                    $obj->call_recording_url = $recording;
                    $obj->call_sid = $req['event']['db_call_id'] ?? null;
                    $obj->json_dump = json_encode($request->all());
                    $obj->save();

                    $leads = Lead::whereRaw("REPLACE(mobile, ' ', '') LIKE ?", ['%' . substr($client_mobile, -8     ) . '%'])->where(function($q) use($user_id){
                            $q->where('agent_id', $user_id)->orWhere('telesales_id', $user_id)->orWhere('telesales_head_id', $user_id);
                        })->get();
                    if($leads){
                        $obj->leads()->sync($leads);
                    }

                    $listingLeads = ListingLead::whereRaw("REPLACE(mobile, ' ', '') LIKE ?", ['%'. substr($client_mobile, -8).'%'])->where('agent_id', $user_id)->get();

                    if($listingLeads){
                        $obj->listingLeads()->sync($listingLeads);
                    }

                    return response()->json(['status' => 'success', 'message' => 'Call log created successfully in the begining', "call log" => $obj]);
                }
            }
            // Checking if the client number  exists in our leads table to save it in the call log without creating a new lead

            $listingLead = ListingLead::whereRaw("REPLACE(mobile, ' ', '') LIKE ?", ['%' . substr($client_mobile, -8     ) . '%'])->where('agent_id', $user_id)->first();


            if(!$lead && !$contact && !$existingUser && !$listingLead && !$user?->hasRole(["Telesales", "Telesales Head", "Admin"]) && $direction != 1  && (!$user || ($user->call_tracking))){
                $lead = new Lead;
                $isNewLead = true;
                $lead->first_name = "New";
                $lead->mobile = $client_mobile;
                $lead->whatsapp = $client_mobile;
                $lead->agent_id = $user?->id; // Keep it empty if user not found
                $lead->lead_status_id = 1;   // Open
                // $lead->lead_sub_status_id = 8;  // Unspecified
                $lead->for = 'Unspecified';
                $lead->lead_medium_id = $medium ?? null;

                $source = Source::where('name', 'LIKE', '%Unspecified%')->first();
                $typed = Type::where('name', 'LIKE', '%Unspecified%')->first();

                $lead->source_id = $source->id??50; // Call - Unspecified
                $lead->type_id = $typed->id??11; // Unspecified
                $lead->status = 1;
                $lead->save();
                event(new \App\Events\LeadCreated ($lead));

            }elseif(!$lead && $user?->hasRole(["Telesales", "Telesales Head", "Admin"])){
                return response()->json(['status' => 'success', 'message' => 'Call log not needed']);
            }

            // Creating the call Log here
            $obj = new CallLog();
            $obj->contact_id        = $contact->id ?? null;
            $obj->user_id           = $user?->id ?? 7;
            $obj->internal_user_id  = $existingUser->id ?? null;
            $obj->client_number     = $client_mobile;
            $obj->call_type         = $direction == 0 ? 'INCOMING' : "OUTGOING";
            $obj->call_started_at   = $start_time;
            $obj->call_answered_at  = $answer_time;
            $obj->call_ended_at     = $end_time;
            $obj->call_duration     = $duration;
            $obj->call_recording_url = $recording;
            $obj->call_sid = $req['event']['db_call_id'] ?? null;
            $obj->json_dump = json_encode($request->all());
            $obj->save();
            $obj->refresh();


            // Saving the call log to all Leads where the number exists
            $leads = Lead::whereRaw("REPLACE(mobile, ' ', '') LIKE ?", ['%'. substr($client_mobile, -8).'%'])->where(function($q) use($user_id){
                            $q->where('agent_id', $user_id)->orWhere('telesales_id', $user_id)->orWhere('telesales_head_id', $user_id);
                        })->get();
            $listingLeads = ListingLead::whereRaw("REPLACE(mobile, ' ', '') LIKE ?", ['%'. substr($client_mobile, -8).'%'])->where('agent_id', $user_id)->get();

            if($leads){
                $obj->leads()->sync($leads);
            }
            if($listingLeads){
                $obj->listingLeads()->sync($listingLeads);
            }

            // Direction 0 means Incoming call
            // Direction 1 means Outgoing call
            if (!$isNewLead && $lead){
                if($answered == 1 && $direction == 1){
                    if($lead->lead_status_id == 1){
                        $lead->lead_status_id = 5; // Contacted
                        $lead->save();
                    }
                }
                elseif ($answered == 0){
                    //$lead->lead_status_id = 1; // Open
                    // $lead->lead_sub_status_id = 5; // Called No Reply
                    //$lead->save();
                }
            }
            return response()->json(['status' => 'success', 'message' => 'Call log created successfully at the end']);

        }
        return response()->json(['status' => 'success', 'message' => 'Lead created successfully']);
    }
    public function formatPhoneNumber($phoneNumber)
    {
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber); // remove non-digit characters

        if (substr($phoneNumber, 0, 2) == '05') {
            $phoneNumber = '+971' . substr($phoneNumber, 1);
        }
        elseif (substr($phoneNumber, 0, 1) == '5') {
            $phoneNumber = '+9715' . substr($phoneNumber, 1);
        } elseif (substr($phoneNumber, 0, 3) == '971') {
            $phoneNumber = '+' . $phoneNumber;
        } elseif (substr($phoneNumber, 0, 5) == '00971') {
            $phoneNumber = '+971' . substr($phoneNumber, 5);
        }

        return $phoneNumber;
    }

}
