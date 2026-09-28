<?php

namespace App\Http\Controllers\Admin\ListingLeads;

use App\Http\Controllers\Controller;
use App\Models\Bath;
use App\Models\Bed;
use App\Models\Community;
use App\Models\CommunityProperty;
use App\Models\ListingLead;
use App\Models\ListingLeadNote;
use App\Models\ListingLeadStatus;
use App\Models\Medium;
use App\Models\Property;
use App\Models\PropertyCategory;
use App\Models\PropertyNote;
use App\Models\Source;
use App\Models\SubCommunity;
use App\Models\Type;
use App\Models\User;
use App\Traits\FileTrait;
use App\Traits\FiltersTrait;
use App\Traits\TelegramTrait;
use Gate;
use Illuminate\Http\Request;

class ListingleadController extends Controller
{

    use FiltersTrait;
    use TelegramTrait;
    use FileTrait;

    public function index()
    {

        abort_if(Gate::denies("Leads List"), 401);
        $notAdmin = auth()->user()->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }
        $data['agents'] = User::whereHas("roles", function ($q) {
            $q->whereIn("name", ["Agent", "Line Manager", "Team Leader", "Sales Director", "Head of Secondary", "CEO"]);
        })->when($notAdmin, fn($q) => $q->whereIn("parent_id", $childrenIds)->orWhere('id', auth()->user()->id))->where("status", true)->get();

        $data['managers'] = User::whereHas("roles", function ($q) {
            $q->whereIn("name", ["Line Manager", "Team Leader", "Sales Director", "Head of Secondary", "CEO"]);
        })->when($notAdmin, fn($q) => $q->whereIn("parent_id", $childrenIds)->orWhere('id', auth()->user()->id))->where("status", true)->get();

        $data['statuses'] = ListingLeadStatus::where('status', true)->get();
        $data['types'] = Type::where('is_lead_type', 1)->where('status', true)->get();
        $data['sources'] = Source::where('is_lead_source', 1)->where('status', true)->get();
        $data['mediums'] = Medium::where('is_lead_medium', 1)->where('status', true)->get();
        $data['beds'] = Bed::where('status', true)->get();
        $data['baths'] = Bath::where('status', true)->get();
        $data['communities'] = Community::where('status', true)->get();
        $data['sub_communities'] = SubCommunity::where('status', true)->get();
        $data['community_properties'] = CommunityProperty::where('status', true)->get();
        $data['categories'] = PropertyCategory::all();

        return view('admin.ListingLeads.index')->with($data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable',
            "for" => "nullable",
            'bed_id' => 'nullable',
            'bath_id' => 'nullable',
            'location_id' => 'nullable',
            'location_type' => 'nullable',
            'source_id' => 'nullable',
            'first_name' => 'nullable',
            'last_name' => 'nullable',
            'email' => 'nullable',
            'mobile' => 'nullable',
            'recording_file' => 'nullable|mimes:mp3',
            'property_category_id' => 'nullable',
        ]);

        try {
            $property = new ListingLead();
            $property->first_name = $request->first_name;
            $property->last_name = $request->last_name;
            $property->email = $request->email;
            $property->mobile = $request->mobile;
            $property->source_id = 50;
            if ($request->source_id) {
                $source = explode('-', $request->source_id);
                $property->source_id = $source[1];
            }

            if ($request->medium_id) {
                $medium = explode('-', $request->medium_id);
                $property->medium_id = $medium[1];
            }

            $property->title = $request->title;
            $property->for = is_null($request->for) ? [] : $request->for;
            $property->bed_id = $request->bed_id;
            $property->bath_id = $request->bath_id;
            $property->location_id = $request->location_id;
            $property->location_type = $request->location_type;

            $property->status_id = $request->status_id ?? 1;
            $property->unit_no = $request->unit_no;
            $property->note = $request->note;
            $property->property_category_id = $request->property_category_id;

            if ($request->location_type == "community_property") {
                $communityProperty = CommunityProperty::find($request->location_id);
                $community_property_id = $communityProperty->id ?? null;
                $sub_community_id = $communityProperty->sub_community_id ?? null;
                $community_id = $communityProperty->community_id ?? null;
            } else {

                $subCommunity = SubCommunity::find($request->location_id);
                $community_property_id = null;
                $sub_community_id = $subCommunity->id ?? null;
                $community_id = $subCommunity->community_id ?? null;
            }

            if ($request->file("recording_file")) {
                $path = $this->uploadDocument($request->file("recording_file"), "lead_listing");
                $property->recording_file = $path;
            }

            $property->sub_community_id = $sub_community_id;
            $property->community_id = $community_id;
            $property->community_property_id = $community_property_id;
            $property->save();
            if ($request->agent_id && ($property->agent_id != $request->agent_id)) {
                // dd($request->agent_id, $property->agent_id);
                if ($request->agent_id != $property->agent_id) {
                    $agent = User::where('id', $request->agent_id)->first();
                    $obj = new ListingLeadNote();
                    $obj->listing_lead_id = $property->id;
                    $obj->note = $agent->first_name . ' ' . $agent->last_name . ' as a Agent Assigned by ' . auth()->user()->first_name . ' ' . auth()->user()->last_name . '.';
                    $obj->created_by_id = auth()->id();
                    $obj->save();
                    $property->status_id = 2;
                }
            }

            if ($request->manager_id && ($property->manager_id != $request->manager_id)) {
                if ($request->manager_id != $property->manager_id) {
                    $manager = User::where('id', $request->manager_id)->first();

                    $obj = new ListingLeadNote();
                    $obj->listing_lead_id = $property->id;
                    $obj->note = $manager->first_name . ' ' . $manager->last_name . ' as a Manager Assigned by ' . auth()->user()->first_name . ' ' . auth()->user()->last_name . '.';
                    $obj->created_by_id = auth()->id();
                    $obj->save();
                    $property->status_id = 2;

                }
            }
            $property->manager_id = $request->manager_id;
            $property->agent_id = $request->agent_id;
            $property->save();

            return response()->json(['status' => '1', 'msg' => 'Property Added Successfully', 'id' => $property->id]);
            //code...
        } catch (\Exception $th) {

            return response()->json(['status' => '0', 'msg' => $th->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'title' => 'nullable',
            'for' => 'nullable',
            'bed_id' => 'nullable',
            'bath_id' => 'nullable',
            'location_id' => 'nullable',
            'location_type' => 'nullable',
            'source_id' => 'nullable',
            // 'medium_id' => 'required',
            'first_name' => 'nullable',
            'last_name' => 'nullable',
            'email' => 'nullable',
            'mobile' => 'nullable',
        ]);

        try {
            $property = ListingLead::find($request->id);
            $property->first_name = $request->first_name;
            $property->last_name = $request->last_name;
            $property->email = $request->email;
            $property->mobile = $request->mobile;

            $source = explode('-', $request->source_id);
            if ($request->medium_id) {
                $medium = explode('-', $request->medium_id);
                $property->medium_id = $medium[1];

            }

            if ($request->source_id) {
                $source = explode('-', $request->source_id);
                $property->source_id = $source[1];
            }

            $property->title = $request->title;
            $property->for = $request->for;
            $property->bed_id = $request->bed_id;
            $property->bath_id = $request->bath_id;
            $property->location_id = $request->location_id;
            $property->location_type = $request->location_type;
            $property->property_category_id = $request->property_category_id;

            $property->unit_no = $request->unit_no;
            $property->note = $request->note;

            if ($request->location_type == "community_property") {
                $communityProperty = CommunityProperty::find($request->location_id);
                $community_property_id = $communityProperty->id ?? null;
                $sub_community_id = $communityProperty->sub_community_id ?? null;
                $community_id = $communityProperty->community_id ?? null;
            } else {

                $subCommunity = SubCommunity::find($request->location_id);
                $community_property_id = null;
                $sub_community_id = $subCommunity->id ?? null;
                $community_id = $subCommunity->community_id ?? null;
            }

            $property->sub_community_id = $sub_community_id;
            $property->community_id = $community_id;
            $property->community_property_id = $community_property_id;

            if ($request->agent_id && ($property->agent_id != $request->agent_id)) {
                // dd($request->agent_id, $property->agent_id);
                if ($request->agent_id != $property->agent_id) {
                    $agent = User::where('id', $request->agent_id)->first();
                    $obj = new ListingLeadNote();
                    $obj->listing_lead_id = $property->id;
                    $obj->note = "The lead has been assigned to " . $agent->first_name . ' ' . $agent->last_name . ".";
                    $obj->created_by_id = auth()->id();
                    $obj->save();
                    $property->status_id = 2;
                }

            }

            if ($request->agent_id == $property->agent_id) {
                $property->status_id = $request->status_id;
            }
            $property->agent_id = $request->agent_id;
            $property->save();

            return response()->json(['status' => '1', 'msg' => 'Property Updated Successfully', 'id' => $property->id]);
            //code...
        } catch (\Exception $exception) {
            // dd($exception->getMessage());
            return response()->json(['status' => '0', 'msg' => $th->getMessage()]);
        }
    }

    public function datatable(Request $request)
    {

        $columns = array(
            0 => null,
            1 => null,
            2 => null,
        );

        $notAdmin = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        $q = ListingLead::query();
        $totalData = $q->count(); // total data count
        $totalFiltered = $this->applyLeadPoolFilters($q, $request)->count();

        $data = $this->applyLeadPoolFilters($q, $request)
            ->with('source', 'medium', 'status', 'agent')
            ->offset($request['start'])
            ->limit($request['length'])
            ->orderBy('updated_at', 'DESC')
            ->get();

        $json_data = array(
            "draw" => intval($request['draw']),
            "recordsTotal" => $totalFiltered,
            "recordsFiltered" => $totalFiltered,
            "data" => $data,
        );
        echo json_encode($json_data);
    }

    public function getLead(Request $request)
    {
        $contact = ListingLead::find($request->id);
        // dd($contact);
        if (!empty($contact)) {
            return response()->json(['status' => '1', 'msg' => 'Lead Found', 'data' => $contact]);
        } else {
            return response()->json(['status' => '0', 'msg' => 'Lead Not Found']);
        }
    }

    public function generalBulk(Request $request)
    {

        // dd($request->all());
        $request->validate([
            'ids' => 'required|array',
            'action' => 'required',
        ]);

        if ($request->action == 'Re-Assign') {
            abort_if(Gate::denies("Leads Assign Agent"), 401);
            if (gettype($request->ids) == 'array' && count($request->ids) > 0) {
                $ids = $request->ids;
                $agent = User::where('id', $request->id)->first();
                foreach ($ids as $lead) {
                    // dd($lead);
                    $leadAgent = ListingLead::where('id', $lead)->first();
                    $leadNote = new ListingLeadNote();
                    $leadNote->listing_lead_id = $leadAgent->id;
                    $leadNote->note = 'The lead has been assigned to ' . $agent->first_name . ' ' . $agent->last_name . '.';
                    $leadNote->created_by_id = auth()->id();
                    $leadNote->save();

                }
                if (ListingLead::whereIn('id', $ids)->update(['agent_id' => $request->id])) {

                    if ($request->telegram_notification == 'true') {
                        $message = "\n<b> AX Secondary CRM</b>\n\n<b>" . count($ids) . " Listing Lead(s) </b> have been transferred to you by  " . auth()->user()->first_name . ' ' . auth()->user()->last_name . '.' . "\n\n";
                        $this->sendTelegramMessageHtml($message, $agent->telegram_chatid, 'lead_bot');
                        // $this->sendTelegramMessageHtml($message, '5970238984', '','pUpdate Lead','lead_bot');
                        foreach ($ids as $id) {
                            telegramNotification('ListingLead', $id, $agent->telegram_chatid, $agent->id, $message);
                        }

                    }

                    $result = array('status' => '1', 'msg' => count($ids) . ' Leads assigned successfully');
                } else {
                    $result = array('status' => '0', 'msg' => 'Something went wrong!!');
                }
            }
        } else if ($request->action == 'Manager') {
            if (gettype($request->ids) == 'array' && count($request->ids) > 0) {
                $ids = $request->ids;
                foreach ($ids as $lead) {
                    $leadAgent = ListingLead::where('id', $lead)->first();
                }
                if (ListingLead::whereIn('id', $ids)->update(['manager_id' => $request->id])) {
                    $result = array('status' => '1', 'msg' => count($ids) . ' Leads assigned successfully');
                } else {
                    $result = array('status' => '0', 'msg' => 'Something went wrong!!');
                }

            }
        }
        return json_encode($result);
    }

    public function archive(Request $request)
    {
        abort_if(Gate::denies("Leads Archive"), 401);
        $request->validate([
            'ids' => 'required',
            'archive_note' => 'required',
        ]);

        try {
            foreach ($request->ids as $id) {
                $lead = ListingLead::find($id);
                $lead->is_archive = 1;
                $lead->save();

                ListingLeadNote::create([
                    "listing_lead_id" => $lead->id,
                    "note" => "Archive: " . $request->archive_note,
                    'status' => 1,
                    "created_by_id" => auth()->id(),
                ]);
            }

            $result = array('status' => '1', 'msg' => "Lead(s) archived successfully.");
            return json_encode($result);
        } catch (\Exception $exception) {
            // dd($exception);
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            return json_encode($result);
        }
    }

    public function deleteMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',

        ]);
        if (gettype($request->ids) == 'array' && count($request->ids) > 0) {
            $ids = $request->ids;
            if (ListingLead::whereIn('id', $ids)->delete()) {
                $result = array('status' => '1', 'msg' => count($ids) . ' Leads deleted successfully');
            } else {
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }
        }
        return json_encode($result);
    }

    public function addNote(Request $request)
    {
        $request->validate([
            'listing_lead_id' => 'required|exists:leads,id',
            'note' => 'required|string',
        ]);

        try {
            $obj = new ListingLeadNote();
            $obj->listing_lead_id = $request->listing_lead_id;
            $obj->note = $request->note;
            $obj->created_by_id = auth()->id();
            $obj->save();
            // event(new LeadNoteAdded($obj));
            $result = array('status' => '1', 'msg' => "Lead note added successfully.");
            return json_encode($result);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
            return json_encode($result);
        }
    }

    public function convertToListing(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'title' => 'nullable',
            'for' => 'required',
            'bed_id' => 'required',
            'bath_id' => 'required',

            'source_id' => 'nullable',

            'owner_id' => 'required',
            'listing_agent_id' => 'required',

        ]);
        $listingLead = ListingLead::find($request->listing_lead_id);
        $listing = new Property();
        $listing->ref_no = generatePropertyRefNo();
        $listing->title = $request->title;
        $listing->for = $request->for;
        $listing->bed_id = $request->bed_id;
        $listing->bath_id = $request->bath_id;
        $listing->community_id = $listingLead->community_id;
        $listing->sub_community_id = $listingLead->sub_community_id;
        $listing->community_property_id = $listingLead->community_property_id;
        $listing->publish_status = 'DRAFT';
        $listing->unit_no = $request->unit_no;
        $listing->owner_id = $request->owner_id;
        $listing->agent_id = $request->listing_agent_id;
        $listing->created_by_id = auth()->id();
        $listing->withoutApproval()->save();

        $listingLead->property_id = $listing->id;
        $listingLead->contact_id = $request->owner_id;
        $listingLead->status_id = 5;
        $listingLead->save();

        $obj = new ListingLeadNote();
        $obj->listing_lead_id = $request->listing_lead_id;
        $obj->note = 'The lead has been successfully converted into an owner. Ref no ' . $listing->ref_no . '.';
        $obj->created_by_id = auth()->id();
        $obj->save();

        $propertyNote = new PropertyNote();
        $propertyNote->property_id = $listing->id;
        $propertyNote->note = 'The listing created using the Listing Lead module. Listing Lead ID: ' . $listingLead->id . '.';
        $propertyNote->created_by_id = auth()->id();
        $propertyNote->save();

        $result = array('status' => '1', 'msg' => "Draft listing added successfully with ref no " . $listing->ref_no . ".", 'id' => $listing->ref_no);
        return json_encode($result);
    }

}
