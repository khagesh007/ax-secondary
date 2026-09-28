<?php

namespace App\Http\Controllers\Api;

use App\Events\LeadCreated;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadNote;
use App\Models\LeadSource;
use App\Models\Source;
use App\Models\Note;
use App\Models\User;
use App\Events\PortalsLeadsNotification;
use App\Models\Property;

class EmailApiController extends Controller
{
    /*


    http://34.242.218.178/api/parser/lead?from=undefined&subject=undefined&customer_message=undefined&customer_name=undefined&customer_email=undefined&customer_mobile=undefined&reference_no=undefined&lead_source_id=30
    http://127.0.0.1:8000/api/parser/lead?from=undefined&subject=want%20to%20know%20rent%20cost&customer_message=please%20provide%20details&customer_name=Abhishek%20kumar&customer_email=test3@demo.com&customer_mobile=35354345345&reference_no=APR-000001&lead_source_id=30



    */

    public function newLead(Request $request)
    {

        echo json_encode($request->all());

        $from = $request->from;
        $subject = $request->subject;
        $message = $request->customer_message;
        $name = $request->customer_name;
        $email = $request->customer_email;
        $mobile = validatePhoneNumber($request->customer_mobile);
        $ref_no = $request->reference_no;
        $lead_source_id = $request->lead_source_id;
        $lead_source = Source::find($lead_source_id);
        $note = "From: $from <br>
                    Subject : $subject <br>
                    Message: $message <br>
                    Name: $name <br>
                    Email: $email <br>
                    Mobile: $mobile <br>
                    Prooperty Ref no. : $ref_no <br>
                    Lead Source:  $lead_source?->name";

        $lead = Lead::where('email', $email)->orWhere('mobile', $mobile)->first();

        if (!empty($lead)) {
            LeadNote::create([
                "lead_id" => $lead->id,
                "note" => $note
            ]);
            event(new LeadCreated($lead));
        } else {
            $property = Property::where('ref_no', $ref_no)->first();
            if (!is_null($property)) {
                $obj = new Lead();
                $obj->first_name = $name;
                $obj->property_id = $property?->id;
                $obj->agent_id = $property?->agent_id;
                $obj->email = $email;
                $obj->mobile = $mobile;
                $obj->whatsapp = $mobile;
                $obj->source_id = $lead_source_id;
                if ($obj->save()) {
                    event(new LeadCreated($obj));
                    LeadNote::create([
                        "lead_id" => $obj->id,
                        "note" => $note
                    ]);
                    if ($property->agent_id) {
                        $user = User::where('id', $property->agent_id)->first();
                        if ($user) {
                            event(new PortalsLeadsNotification("Lead", $lead, 'received', $user, $user, 1));
                        }
                    }
                    return response()->json(['status' => 'success', 'message' => 'Lead created successfully']);
                } else {
                    return response()->json(['status' => 'error', 'message' => 'Lead not created']);
                }
            }

            return response()->json(['status' => 'error', 'message' => 'Lead not created']);
        }
    }
    // web-> 'contact_source_id = 40
    // pf->contact_source_id =30
    // dubizzle -> contact_source_id = 13
    // bayut-> contact_source_id = 35


    public function create_lead(Request $request)
    {

        if ($request->input('api_name') == 'dubizzle_email') {

            $property = Property::with('agent')->where('ref_number', (trim($request->input('ref_number'))))->first();

            if ($property) {
                $message = '<strong>Dubizzle Email:</strong>  <br>customer_name:'.trim($request->input('customer_name')).'<br> customer_email: '.trim($request->input('customer_email')).' <br> customer_mobile: '.trim($request->input('customer_mobile')).' <br>  customer_message: '.trim($request->input('customer_message')).'<br>  Agent: '.$property->agent->first_name.' '.$property->agent->last_name.' <br>  Property Ref#: '.trim($request->input('ref_number')).'<br>';

                $lead = Lead::where('email', trim($request->input('customer_email')))->orWhere('mobile',
                    trim($request->input('customer_mobile')))->get()->first();

                if ($lead !== null) {
                    $lead->email = trim($request->input('customer_email')) ?? $lead->email;
                    $lead->mobile = trim($request->input('customer_mobile')) ?? $lead->mobile;
                    $lead->first_name = 'DUP-'.trim($request->input('customer_name'));
                    $lead->agent_id = $lead->agent_id ?? null;
                } else {
                    $lead = Lead::create([
                        'email' => trim($request->input('customer_email')),
                        'mobile' => trim($request->input('customer_mobile')),
                        'first_name' => trim($request->input('customer_name')),
                        'agent_id' => $property->agent_id ?? null,
                    ]);
                }

                $lead->message = trim($request->input('customer_message'));
                $lead->subject = trim($request->input('subject'));
                $lead->for = $property->for;

                $lead->property_id = $property->id;
                $lead->lead_status_id = 1;
                // $lead->lead_sub_status_id = 4;

                if ($property->for == 1) {
                    $lead->contact_type_id = 2;
                } else {
                    if ($property->for == 2) {
                        $lead->contact_type_id = 1;
                    } else {
                        $lead->contact_type_id = 10;
                    }
                }

                $lead->contact_source_id = 13; // Dubizzle
                $lead->save();
                $note = LeadNote::Create(
                    [
                        'lead_id' => $lead->id,
                        'note' => '<strong>Auto Import</strong> <br>'.$message
                    ],

                );
            }
        }


        if ($request->input('api_name') == 'bayut_email') {

            $property = Property::with('agent')->where('ref_number', (trim($request->input('ref_number'))))->first();

            if ($property) {
                $message = '<strong>Bayut Email:</strong>  <br>customer_name:'.trim($request->input('customer_name')).'<br> customer_email: '.trim($request->input('customer_email')).' <br> customer_mobile: '.trim($request->input('customer_mobile')).' <br>  customer_message: '.trim($request->input('customer_message')).'<br>  Agent: '.$property->agent->first_name.' '.$property->agent->last_name.' <br>  Property Ref#: '.trim($request->input('ref_number')).'<br>';

                $lead = Lead::where('email', trim($request->input('customer_email')))->orWhere('mobile',
                    trim($request->input('customer_mobile')))->get()->first();

                if ($lead !== null) {
                    $lead->email = trim($request->input('customer_email')) ?? $lead->email;
                    $lead->mobile = trim($request->input('customer_mobile')) ?? $lead->mobile;
                    $lead->first_name = 'DUP-'.trim($request->input('customer_name'));
                    $lead->agent_id = $lead->agent_id ?? null;
                } else {
                    $lead = Lead::create([
                        'email' => trim($request->input('customer_email')),
                        'mobile' => trim($request->input('customer_mobile')),
                        'first_name' => trim($request->input('customer_name')),
                        'agent_id' => $property->agent_id,
                    ]);
                }

                $lead->message = trim($request->input('customer_message'));
                $lead->subject = trim($request->input('subject'));
                $lead->for = $property->for;

                $lead->property_id = $property->id;
                $lead->lead_status_id = 1;
                // $lead->lead_sub_status_id = 4;

                if ($property->for == 1) {
                    $lead->contact_type_id = 2;
                } else {
                    if ($property->for == 2) {
                        $lead->contact_type_id = 1;
                    } else {
                        $lead->contact_type_id = 10;
                    }
                }

                $lead->contact_source_id = 35; // Bayut
                $lead->save();
                $note = LeadNote::Create(
                    [
                        'lead_id' => $lead->id,
                        'note' => '<strong>Auto Import</strong> <br>'.$message
                    ],

                );
            }
        }

        if ($request->input('api_name') == 'pf_email') {

            $property = Property::with('agent')->where('ref_number', (trim($request->input('ref_number'))))->first();

            if ($property) {
                $message = '<strong>PF Email:</strong>  <br>customer_name:'.trim($request->input('customer_name')).'<br> customer_email: '.trim($request->input('customer_email')).' <br> customer_mobile: '.trim($request->input('customer_mobile')).' <br>  customer_message: '.trim($request->input('customer_message')).'<br>  Agent: '.$property->agent->first_name.' '.$property->agent->last_name.' <br>  Property Ref#: '.trim($request->input('ref_number')).'<br>';

                $lead = Lead::where('email', trim($request->input('customer_email')))->orWhere('mobile',
                    trim($request->input('customer_mobile')))->get()->first();

                if ($lead !== null) {
                    $lead->email = trim($request->input('customer_email')) ?? $lead->email;
                    $lead->mobile = trim($request->input('customer_mobile')) ?? $lead->mobile;
                    $lead->first_name = 'DUP-'.trim($request->input('customer_name'));
                    $lead->agent_id = $lead->agent_id ?? null;
                } else {
                    $lead = Lead::create([
                        'email' => trim($request->input('customer_email')),
                        'mobile' => trim($request->input('customer_mobile')),
                        'first_name' => trim($request->input('customer_name')),
                        'agent_id' => $property->agent_id ?? null,
                    ]);
                }

                $lead->message = trim($request->input('customer_message'));
                $lead->subject = trim($request->input('subject'));
                $lead->for = $property->for;

                $lead->property_id = $property->id;
                $lead->lead_status_id = 1;
                // $lead->lead_sub_status_id = 4;

                if ($property->for == 1) {
                    $lead->contact_type_id = 2;
                } else {
                    if ($property->for == 2) {
                        $lead->contact_type_id = 1;
                    } else {
                        $lead->contact_type_id = 10;
                    }
                }

                $lead->contact_source_id = 30; // PF
                $lead->save();
                $note = LeadNote::Create(
                    [
                        'lead_id' => $lead->id,
                        'note' => '<strong>Auto Import</strong> <br>'.$message
                    ],

                );
            }
        }
    }


    public function create_call_lead(Request $request)
    {


        try {

            if ($request->get('customer_mobile')) {
                $lead_action = "";
                $agent_id = null;


                $message = '<strong>'.trim($request->get('call_status')).':</strong>  <br> customer_mobile: '.trim($request->get('customer_mobile')).' <br>  Agent name: '.trim($request->get('agent_name')).'<br>  Source ID: '.trim($request->get('lead_source_id')).'<br>';

                $lead = Lead::where('mobile', trim($request->get('customer_mobile')))->get()->first();

                if ($lead !== null) {
                    $lead->mobile = trim($request->get('customer_mobile'));
                    $lead->first_name = 'DUP-'.$lead->first_name;
                    $lead->agent_id = $lead->agent_id ?? null;
                    $agent_id = $lead->agent_id;
                    $lead_action = "Updated";
                } else {

                    $all_users = $data['users'] = \App\User::whereHas("roles", function ($q) {
                        $q->whereIn("name", ['Agent']);
                    })->select(['id', 'first_name', 'last_name'])
                        ->where('status', 1)->get();


                    foreach ($all_users as $key) {
                        $agent_fullname = $key['first_name'].' '.$key['last_name'];

                        if (\Str::contains(trim($request->get('agent_name')), $agent_fullname)) {
                            $agent_id = $key['id'];
                        }
                    } // end foreach


                    $lead_action = "Created";
                }


                $lead = Lead::create([
                    'mobile' => trim($request->get('customer_mobile')),
                    'agent_id' => $agent_id ?? null,
                ]);


                $lead->subject = trim($request->get('subject'));
                $lead->contact_source_id = trim($request->get('lead_source_id'));
                $lead->for = 0;

                $lead->lead_status_id = 1;
                // $lead->lead_sub_status_id = 4;

                $lead->save();


                $note = LeadNote::Create(
                    [
                        'lead_id' => $lead->id,
                        'note' => '<strong>Auto Import</strong> <br>'.$message
                    ]

                );

                $response = ['message' => 'Lead '.$lead_action.' Successfully', 'status' => true];
                return Response()->json($response);

                // dd($lead);

            }
        } catch (\Exception $e) {
            $response = [
                'message' => 'Listing Follow up Reminder Notification failed to sent',
                'status' => true,
                'data' => $e->getMessage(),
            ];
            return Response()->json($response);
        }
    }


    function replace_between($str, $needle_start, $needle_end, $replacement)
    {
        $pos = strpos($str, $needle_start);
        $start = $pos === false ? 0 : $pos + strlen($needle_start);

        $pos = strpos($str, $needle_end, $start);
        $end = $start === false ? strlen($str) : $pos;

        return substr_replace($str, $replacement, $start, $end - $start);
    }

}
