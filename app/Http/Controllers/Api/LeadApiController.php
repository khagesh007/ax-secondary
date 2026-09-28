<?php

namespace App\Http\Controllers\Api;
use App\Events\LeadCreated;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\LeadNote;
use App\Models\LeadSource;
use App\Models\Source;
use App\Models\Note;
use App\Models\Type;
use App\Models\Medium;
use App\Models\Property;
use App\Models\SubCommunity;
use App\Models\CommunityProperty;
use App\Models\LeadCriteriaLocation ;
use App\Models\LeadCriteria;
class LeadApiController extends Controller
{
    public function createLead(Request $request)
    {
        abort_if($request->api_token != "fYlQO814toCEdmdp", 404);
        $validatedData = $request->validate([

            "customer_name"     => "nullable|string",
            "customer_mobile"   => "required|string",
            "customer_email"    => "nullable|email",
            "lead_source_name"  => "required|string",
            "reference_no"      => "nullable|string",
            "message"           => "nullable|string",
            "return_data"       => "nullable"
        ]);

        $name               = $validatedData['customer_name'] ?? "";
        $email              = $validatedData['customer_email'] ?? "";
        $mobile             = validatePhoneNumber($validatedData['customer_mobile']);
        $lead_source_name   = $validatedData['lead_source_name'];
        $ref_no             = $validatedData['reference_no']?? null;
        $message            = $validatedData['message']?? "";

        if(checkBlocklist($mobile,Lead::class,$request)){
            $result = array('status' => '0', 'msg' => 'This number is in Blacklist. Please do not contact with this number.', 'error' => 'This number is in Blacklist. Please do not contact with this number.');
            return json_encode($result);
        }


        $lead_source = Source::where('is_lead_source',1)->where("name",'LIKE', '%'.$lead_source_name.'%')->first();
        $medium = Medium::where("name", "Api")->pluck("id")->first();

        //$lead = Lead::where('email', $validatedData['customer_email'])->orWhere('mobile', $validatedData['customer_mobile'])->first();

        $property = null;

        if($ref_no){
            $property = Property::where('ref_no', $ref_no)->with('agent')->first();
        }
        $note = "
                    Message: $message <br>
                    Name: $name <br>
                    Email: $email <br>
                    Mobile: $mobile <br>
                    Prooperty Ref no. : $ref_no <br>
                    Lead Source:  $lead_source?->name";


        // if (!is_null($property)) {
            $obj = new Lead();
            $obj->first_name = $name;
            $obj->property_id = $property?->id;
            $obj->agent_id = $property?->agent_id ?? 7;
            $obj->email = $email;
            $obj->for = $property?->for ?? "Unspecified";
            $obj->mobile = $mobile;
            $obj->whatsapp = $mobile;
            $obj->lead_medium_id = $medium;

            $obj->source_id = $lead_source?->id;
            // Code for getting Lead Type----START

            if ($property?->for == 'Residential for sale') {
                $leadTyped = Type::where('name','LIKE', '%Buyer%')->first();
                $obj->type_id = $leadTyped->id??2;
            } else if ($property?->for == 'Residential for rent') {
                $leadTyped = Type::where('name','LIKE', '%Tenant%')->first();
                $obj->type_id = $leadTyped->id??2;
            } else if ($property?->for == 'Commercial for sale') {
                $leadTyped = Type::where('name','LIKE', '%Buyer%')->first();
                $obj->type_id = $leadTyped->id??2;
            } else if ($property?->for == 'Commercial for rent') {
                $leadTyped = Type::where('name','LIKE', '%Tenant%')->first();
                $obj->type_id = $leadTyped->id??2;
            } else {
                $leadTyped = Type::where('name','LIKE', '%Other%')->first();
                $obj->type_id = $leadTyped->id??10;
            }

            // Code for getting Lead Type ----END
            if ($obj->save()) {
                event(new LeadCreated($obj));
                LeadNote::create([
                    "lead_id" => $obj->id,
                    "note" => $note
                ]);

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
                    $leadCriteria->lead_id = $obj->id;
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

                if(isset($validatedData['return_data']) && $validatedData['return_data'] && $property){
                    $data = array();

                    $data['property'] = [
                        "reference_no"  => $property->ref_no ?? null,
                        "unit_no"       => $property->unit_no ?? null,
                        "price"         => $property?->price?->price ?? null,
                        "location"      => $property?->location ?? null,
                        "agent"         => [
                            "name"              => $property?->agent->name,
                            "email"             => $property?->agent?->email,
                            "telegram_chatid"   => $property?->agent?->telegram_chatid
                            ]
                    ];
                    return response()->json(['status' => 'success', 'message' => 'Lead created successfully', 'data' => $data]);
                }
                return response()->json(['status' => 'success', 'message' => 'Lead created successfully']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Lead not created']);
            }
            return response()->json(['status' => 'error', 'message' => 'Lead not created']);
        // }

    }
}
