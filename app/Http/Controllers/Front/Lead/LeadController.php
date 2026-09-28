<?php

namespace App\Http\Controllers\Front\Lead;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\Property;
use Illuminate\Http\Request;
class LeadController extends Controller
{

    public function leadFromPreview(Request $request)
    { // lead created from public preview page
        $validatedData = $request->validate([
            "agent" => "required|exists:users,id",
            "reference_number" => "required|exists:properties,ref_no",
            "name" => "required|string",
            "phone" => "nullable|numeric",
            "email" => "required",
            //"email" => "required|string|unique:contacts",
        ]);
        try {
            $property = Property::where('ref_no', $request->reference_number)->first();
            $source = LeadSource::where('name','CRM')->first();
            $lead = new Lead();
            $lead->agent_id     = $request->agent;
            $lead->first_name   = $request->name;
            $lead->mobile        = $request->phone;
            $lead->email        = $request->email;
            $lead->property_id  = $property?->id;
            $lead->for  = $property?->for;
            $lead->source_id       = $source?->id;
            $lead->lead_status_id       = '1';
            // $lead->lead_sub_status_id   = '4';
            $lead->save();
            return response()->json([
                'status' => true,
                'message' => 'Inquiry submitted successfully',
            ]);
        }catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Please try again or contact support',
            ]);
        }

    }
}
