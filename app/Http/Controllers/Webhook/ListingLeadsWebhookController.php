<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\Bath;
use App\Models\Bed;
use App\Models\CommunityProperty;
use App\Models\ListingLead;
use App\Models\ListingLeadNote;
use App\Models\Medium;
use App\Models\PropertyCategory;
use App\Models\Source;
use App\Models\SubCommunity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ListingLeadsWebhookController extends Controller
{

    // [
    //     'owner_id' => '684',
    //     'property_id' => 555,
    //     'owner_first_name' => 'CHRISTIAN',
    //     'owner_last_name' => 'ARMANDO ARAMAYO REYNA FARJE',
    //     'owner_mobile' => '+971501883574',
    //     'owner_email' => NULL,
    //     'property_type' => 'Apartment',
    //     'property_purpose' => 'Residential For Rent',
    //     'porperty_location' => 'Erin,Central Park,City Walk,Al Wasl,Dubai,UAE',
    //     'property_bed' => '1 bed',
    //     'property_bath' => '1 bath',
    //     'property_unit_no' => '208',
    //     'agent_email' => 'admin@ax.com',

    // ]
    public function handle(Request $request)
    {
        // Log::channel('mobileApi')->info('Listing Lead Import Request : AX CONTACTS', ['data' => $request->all()]);

        //  dd($request->all());
        $request->validate([
            'owner_first_name' => 'required',
            'owner_last_name' => 'nullable',
            'owner_mobile' => 'required',
            'owner_email' => 'nullable|email',
            'property_type' => 'nullable',
            'property_purpose' => 'nullable',
            'property_location' => 'nullable',
            'property_bed' => 'nullable',
            'property_bath' => 'nullable',
            'property_unit_no' => 'nullable',
            'agent_email' => 'required',
            'call_recording' => 'nullable',
        ]);

        try {
            DB::beginTransaction();

            $property_type = null;
            if ($request->property_type) {
                $type = PropertyCategory::where('title', $request->property_type)->first();
                if ($type) {
                    $property_type = $type->id;
                }
            }

            $bed_id = null;
            if ($request->property_bed) {
                $bed = Bed::where('name', $request->property_bed)->first();
                if ($bed) {
                    $bed_id = $bed->id;
                }
            }

            $bath_id = null;
            if ($request->property_bath) {
                $bath = Bath::where('name', $request->property_bath)->first();
                if ($bath) {
                    $bath_id = $bath->id;
                }
            }

            $bath_id = null;
            if ($request->property_bath) {
                $bath = Bath::where('name', $request->property_bath)->first();
                if ($bath) {
                    $bath_id = $bath->id;
                }
            }

            $medium_id = null;
            if ($request->medium_id) {
                $medium = Medium::where('name', $request->medium_id)->first();
                if ($medium) {
                    $medium_id = $medium->id;
                }
            }

            // $locationInfo = locFinder($request->porperty_location);
            // dd($locationInfo['data']['id']);

            $listing = new ListingLead();
            $listing->first_name = $request->owner_first_name;
            $listing->last_name = $request->owner_last_name;
            $listing->mobile = $request->owner_mobile;
            $listing->email = $request->owner_email;
            $listing->unit_no = $request->property_unit_no;
            $listing->bath_id = $bath_id;
            $listing->bed_id = $bed_id;
            $listing->source_id = $request->source_id; // 131
            $listing->status_id = 1;
            $listing->recording_file = $request->call_recording ?? null;

            $agent_id = null;
            if ($request->agent_email) {
                $agent = User::where('email', $request->agent_email)->first();
                if ($agent) {
                    $agent_id = $agent->id;
                    $listing->agent_id = $agent_id;
                    $listing->status_id = 2;

                }
            }

            $listing->property_category_id = $property_type;
            $listing->for = $request->property_purpose;
            $source_id = $request->source_id ?? 131;

            $source = Source::where('id', $source_id)->first();
            // if($locationInfo['type'] == 'Community'){
            //     $listing->community_id = $locationInfo['data']['id'];
            // }else if('CommunityProperty'){
            //         // dd($locationInfo['data']['community_id']);
            //     $listing->community_id = $locationInfo['data']['community_id'];
            //     $listing->sub_community_id = $locationInfo['data']['sub_community_id'];
            //     $listing->community_property_id = $locationInfo['data']['id'];

            // }

            $listing->note = 'Listing Lead Imported : ' . $source->name . ' <br><br><br>Location: ' . $request->property_location . '<br><br><br>  Webhook Data: ' . $request;
            $listing->save();

            $obj = new ListingLeadNote();
            $obj->listing_lead_id = $listing->id;
            $obj->note = 'Listing Lead Imported : ' . $source->name . ' <br><br><br>Location: ' . $request->property_location;
            $obj->created_by_id = auth()->id() ?? null;
            $obj->save();

            DB::commit();
            $community_property_id = null;
            $sub_community_id = null;
            $community_id = null;
            if ($request->property_location) {
                $property_Location = $request->property_location;
                if (strpos($property_Location, ',') !== false) {
                    $location = explode(',', $property_Location);
                    $location_name = $location[0];

                    // $community_property = CommunityProperty::where('name','ILIKE', $location_name)->first();
                    $community_property = CommunityProperty::where(DB::raw('LOWER(name)'), '=', strtolower($location_name))->first();
                    if (!$community_property) {
                        $sub_community = SubCommunity::where(DB::raw('LOWER(name)'), '=', strtolower($location_name))->first();
                        if ($sub_community) {
                            $sub_community_id = $sub_community->id;
                            $community_property_id = null;
                            $community_id = $sub_community->community_id;
                        }

                    } else {
                        $community_property_id = $community_property->id;
                        $sub_community_id = $community_property->sub_community_id;
                        $community_id = $community_property->community_id;

                    }

                } else {
                    $community_property = CommunityProperty::where('name', $property_Location)->first();
                    if (!$community_property) {
                        $sub_community = SubCommunity::where('name', $property_Location)->first();
                        $community_id = $sub_community->community_id;
                        $sub_community_id = $sub_community->id;
                        $community_property_id = null;
                    } else {
                        $community_property_id = $community_property->id;
                        $sub_community_id = $community_property->sub_community_id;
                        $community_id = $community_property->community_id;
                    }

                }
                $listing->community_property_id = $community_property_id;
                $listing->sub_community_id = $sub_community_id;
                $listing->community_id = $community_id;
                $listing->save();
            }

            Log::channel('mobileApi')->info('Listing Lead Imported : ' . $source->name ?? '', ['data' => $listing]);
            return response()->json(['message' => 'success', 'data' => $listing], 200);
        } catch (\Throwable $th) {
            DB::rollback();
            Log::channel('mobileApi')->info('Listing Lead Imported Error : ' . $source->name ?? '', ['data' => [$th, $request]]);
            return response()->json(['message' => 'error', 'data' => $th], 500);

        }

    }

    public function handleBulk(Request $request)
    {
        foreach ($request->data as $data) {
            $this->handle($data);
        }
    }
}
