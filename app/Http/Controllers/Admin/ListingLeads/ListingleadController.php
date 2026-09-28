<?php

namespace App\Http\Controllers\Admin\ListingLeads;

use App\Exports\ListingLeadExport;
use App\Exports\ListingLeadFilterExport;
use App\Http\Controllers\Controller;
use App\Models\Bath;
use App\Models\Bed;
use App\Models\Community;
use App\Models\CommunityProperty;
use App\Models\Contact;
use App\Models\ContactAgent;
use App\Models\ContactNote;
use App\Models\Lead;
use App\Models\LeadCriteria;
use App\Models\LeadNote;
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
use App\Models\UserLocation;
use App\Traits\CallTrait;
use App\Traits\FileTrait;
use App\Traits\FiltersTrait;
use App\Traits\TelegramTrait;
use Crypt;
use DB;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Events\LeadTransferAppNotification;

class ListingleadController extends Controller
{

    use FiltersTrait;
    use TelegramTrait;
    use FileTrait, CallTrait;

    public function index()
    {

        abort_if(Gate::denies("Listing Leads List"), 401);
        if (auth()?->user()?->parent_id == 32 || auth()?->user()?->id == 32) {
            abort(403);
        }

        $notAdmin = auth()->user()->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }
        // $data['agents'] = User::where('status',true)->whereHas("roles", function ($q) {
        //     $q->whereIn("name", ["Agent", "Line Manager", "Team Leader", "Sales Director", "Head of Secondary"]);
        // })->when($notAdmin, fn($q) => $q->whereIn("parent_id", $childrenIds)->orWhere('id', auth()->user()->id))->where("status", true)->get();

        $data['agents'] = User::where('status', true)->whereHas("roles", function ($q) {
            $q->whereIn("name", ["Agent", "Line Manager", "Team Leader", "Sales Director", "Head of Secondary"]);
        })->where("status", true)->get();

        $data['managers'] = User::where('status', true)->whereHas("roles", function ($q) {
            $q->whereIn("name", ["Line Manager", "Team Leader", "Sales Director", "Head of Secondary"]);
        })->when($notAdmin, fn($q) => $q->whereIn("parent_id", $childrenIds)->orWhere('id', auth()->user()->id))->where("status", true)->get();

        $data['telesales'] = User::where('status', true)->whereHas("roles", function ($q) {
            $q->whereIn("name", ["Telesales"]);
        })->where("status", true)->get();

        $data['statuses'] = ListingLeadStatus::where('status', true)->orderBy('order', 'asc')->get();
        $data['types'] = Type::where('is_lead_type', 1)->where('status', true)->get();
        $data['sources'] = Source::where('is_lead_source', 1)->where('status', true)->get();
        $data['mediums'] = Medium::where('is_lead_medium', 1)->where('status', true)->get();
        $data['beds'] = Bed::where('status', true)->get();
        $data['baths'] = Bath::where('status', true)->get();
        $data['communities'] = Community::where('status', true)->get();
        // $data['sub_communities'] = SubCommunity::all();
        // $data['community_properties'] = CommunityProperty::all();
        $data['categories'] = PropertyCategory::all();

        return view('admin.ListingLeads.index')->with($data);
    }

    public function store(Request $request)
    {
        //   dd($request->all());

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
            'recording_file' => 'nullable|mimes:mp3,wav,ogg,mpga,m4a,3gp,3gpp,amr,|max:30000',
            'property_category_id' => 'nullable',
        ]);

        if (checkBlocklist($request->mobile, ListingLead::class, $request)) {
            $result = array('status' => '0', 'msg' => 'This number is in Blacklist. Please do not contact with this number.', 'error' => 'This number is in Blacklist. Please do not contact with this number.');
            return json_encode($result);
        }

        try {
            $property = new ListingLead();
            $property->first_name = $request->first_name;
            $property->last_name = $request->last_name;
            $property->email = $request->email;
            $property->mobile = validatePhoneNumber($request->mobile);
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

            if ($request->agent_id) {
                $agent = User::where('id', $request->agent_id)->first();
                $obj = new ListingLeadNote();
                $obj->listing_lead_id = $property->id;
                $obj->note = $agent->first_name . ' ' . $agent->last_name . ' as a Agent Assigned by ' . auth()->user()->first_name . ' ' . auth()->user()->last_name . '.';
                $obj->created_by_id = auth()->id();
                $obj->save();
                $property->status_id = 2;
            }

            if ($request->telesales_id) {
                $telesales = User::where('id', $request->telesales_id)->first();
                $obj = new ListingLeadNote();
                $obj->listing_lead_id = $property->id;
                $obj->note = $telesales->first_name . ' ' . $telesales->last_name . ' as a Agent Assigned by ' . auth()->user()->first_name . ' ' . auth()->user()->last_name . '.';
                $obj->created_by_id = auth()->id();
                $obj->save();
            }

            // if ($request->manager_id && ($property->manager_id != $request->manager_id)) {
            //     if ($request->manager_id != $property->manager_id) {
            //         $manager = User::where('id', $request->manager_id)->first();

            //         $obj = new ListingLeadNote();
            //         $obj->listing_lead_id = $property->id;
            //         $obj->note = $manager->first_name . ' ' . $manager->last_name . ' as a Manager Assigned by ' . auth()->user()->first_name . ' ' . auth()->user()->last_name . '.';
            //         $obj->created_by_id = auth()->id();
            //         $obj->save();
            //         $property->status_id = 2;

            //     }
            // }

            $property->manager_id = $request->manager_id;
            $property->agent_id = $request->agent_id;
            $property->telesales_id = $request->telesales_id;
            $property->save();

            $environment = \App::environment();
            if ($environment == "production") {
                $message = "<b>AX Secondary CRM</b>\n\n<b>New Listing Lead:</b>\nA new listing lead has been received. Please assign it to an agent.";
                $agents = User::whereHas("roles", function ($q) {
                    $q->whereIn("name", ["Line Manager", "Team Leader", "Sales Director"]);
                })->where("status", true)->get();
                foreach ($agents as $agent) {
                    if ($agent->telegram_chatid != null) {
                        $this->sendTelegramMessageHtml($message, [$agent->telegram_chatid], null, null, 'lead_bot');
                    }

                    telegramNotification('ListingLead',$property->id,$agent->telegram_chatid,$agent->id,$message);

                }
            }

            return response()->json(['status' => '1', 'msg' => 'Property Added Successfully', 'id' => $property->id]);
            //code...
        } catch (\Exception $th) {

            return response()->json(['status' => '0', 'msg' => 'Something went wrong!!', 'error' => $th->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'title' => 'nullable',
            'for' => 'nullable',
            'bed_id' => 'nullable',
            'bath_id' => 'nullable',
            'location_id' => 'nullable',
            'location_type' => 'nullable',
            'source_id' => 'nullable',
            // 'medium_id' => 'nullable',
            'first_name' => 'nullable',
            'last_name' => 'nullable',
            'email' => 'nullable',
            'mobile' => 'nullable',
        ]);

        if (checkBlocklist($request->mobile, ListingLead::class, $request)) {
            $result = array('status' => '0', 'msg' => 'This number is in Blacklist. Please do not contact with this number.', 'error' => 'This number is in Blacklist. Please do not contact with this number.');
            return json_encode($result);
        }

        try {
            $property = ListingLead::find($request->id);

            $property->first_name = $request->first_name;
            $property->last_name = $request->last_name;
            $property->email = $request->email;
            $property->mobile = validatePhoneNumber($request->mobile);

            $source = explode('-', $request->source_id);
            if ($request->medium_id) {
                $medium = explode('-', $request->medium_id);
                $property->medium_id = $medium[1];

            }

            if ($request->source_id) {
                $source = explode('-', $request->source_id);
                $property->source_id = $source[1];
            } // $property->medium_id = $request->medium_id;

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
            $original = $property->getOriginal() ?? [];
            $dirty = $property->getDirty() ?? [];

            $original = array_intersect_key($original, $dirty);

            $property->save();

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
            if ($request->telesales_id && ($property->telesales_id != $request->telesales_id)) {
                if ($request->telesales_id != $property->telesales_id) {
                    $telesales = User::where('id', $request->telesales_id)->first();
                    $obj = new ListingLeadNote();
                    $obj->listing_lead_id = $property->id;
                    $obj->note = "The lead has been assigned to " . $telesales->first_name . ' ' . $telesales->last_name . "as a telesales.";
                    $obj->created_by_id = auth()->id();
                    $obj->save();
                    $property->status_id = 2;
                }

            }

            if ($request->agent_id == $property->agent_id) {
                $property->status_id = $request->status_id;
            }
            $property->agent_id = $request->agent_id;
            $property->updated_by_id = auth()->id();
            $property->telesales_id = $request->telesales_id;
            $property->save();

            $arr = ['attributes' => $dirty,
                'old' => $original, 'listing_lead_id' => $property->id];
            activity()
                ->performedOn($property)
                ->causedBy(auth()->user())
                ->withProperties($arr)
                ->event('deleted')
                ->log(auth()->user()->first_name . ' has Updated the listing lead');

            return response()->json(['status' => '1', 'msg' => 'Property Updated Successfully', 'id' => $property->id]);
            //code...
        } catch (\Exception $exception) {
            // dd($exception->getMessage());
            return response()->json(['status' => '0', 'msg' => $exception->getMessage()]);
        }
    }

    public function datatable(Request $request)
    {
        if (auth()->user()->parent_id == 32 || auth()->user()->id == 32) {
            abort(403);
        }
        $q = ListingLead::query();
        $totalData = $q->count(); // total data count
        $totalFiltered = $this->applyLeadPoolFilters($q, $request)->count();

        $columns = array(
            0 => 'updated_at',
            1 => 'id',
            2 => 'first_name',
            3 => 'mobile',
            4 => null,
            5 => null,
            6 => null,
            7 => null,
            8 => null,
            9 => null,
            10 => null,
            11 => null,
            12 => null,
            13 => null,
            14 => null,
            15 => 'updated_at',
            16 => 'created_at',
        );

        $data = $this->applyLeadPoolFilters($q, $request)
            ->with('source', 'medium', 'status', 'agent', 'type', 'bed', 'bath', 'updatedBy')
            ->offset($request['start'])
            ->limit($request['length'])
        // ->orderBy('created_at', 'desc')
            ->orderBy($columns[$request["order"][0]["column"]],
                $request["order"][0]["dir"])
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

        $notAdmin = auth()->user()->hasRole(["Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        // $contact = ListingLead::find($request->id);
        $contact = ListingLead::where('id', $request->id)
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->where(function ($q) use ($childrenIds) {
                    $q->whereIn('agent_id', $childrenIds);
                });
            })->first();
        if (!empty($contact)) {
            return response()->json(['status' => '1', 'msg' => 'Lead Found', 'data' => $contact]);
        } else {
            return response()->json(['status' => '0', 'msg' => 'Lead Not Found'], 422);
        }
    }

    public function generalBulk(Request $request)
    {

        // dd($request->all());
        $request->validate([
            'id' => 'required',
            'ids' => 'required|array',
            'action' => 'required',
        ]);

        if ($request->action == 'Re-Assign') {
            abort_if(Gate::denies("Listing Leads Assign Agent"), 401);
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
                if (ListingLead::whereIn('id', $ids)->update([
                    'agent_id' => $request->id,
                    'status_id' => 2,
                    'updated_by_id' => auth()->id(),
                    'assigned_by_id' => auth()->id(),
                    'updated_at' => date('Y-m-d H:i:s'),

                ])) {

                    if ($request->telegram_notification == 'true') {
                        $message = "\n<b> AX Secondary CRM</b>\n\n<b>" . count($ids) . " Listing Lead(s)</b> have been transferred to you by " . auth()->user()->first_name . ' ' . auth()->user()->last_name . '.' . "\n\n";
                        $message .= "<b>Listing Lead(s) Details:</b>\n";
                        // list all the ids
                        foreach ($ids as $id) {
                            $message .= "\n- <a href='" . route('listings.leads.index') . "?id=".$id."'>" . $id . "</a>";
                        }
                        $environment = \App::environment();
                        if ($environment == "production") {
                            $this->sendTelegramMessageHtml($message, [$agent->telegram_chatid], null, null, 'lead_bot');
                        }
                        foreach ($ids as $id) {
                        telegramNotification('ListingLead',$id,$agent->telegram_chatid,$agent->id,$message);
                        }
                    }
                    if(auth()->user()->id==172 || auth()->user()->hasRole('Super Admin')){
                        $assignment_by = User::where('id', auth()->user()->id)->first();
                        event(new LeadTransferAppNotification("ListingLead", $leadAgent, 'selfTransfer', $agent, $assignment_by, count($ids)));
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
        abort_if(Gate::denies("Listing Leads Archive"), 401);
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
            'listing_lead_id' => 'required|exists:listing_leads,id',
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
        $request->validate([
            'for' => 'required',
            'unit_no' => 'required',
            'mobile' => 'required',
            'email' => 'nullable',
            'first_name' => 'required',
            'agent_id' => 'required',
            'property_category_id' => 'nullable',
            'listing_location_type' => 'nullable',
            'listing_location_id' => 'nullable',
            'bed_id' => 'nullable',
            'bath_id' => 'nullable',

        ]);

        try {
            DB::beginTransaction();
            $already_contact = Contact::where('email', $request->email)
                ->where('mobile', validatePhoneNumber($request->full_mobile))
                ->where('agent_id', $request->agent_id)
                ->first();

            if ($already_contact) {
                $contact = ContactAgent::updateOrCreate([
                    'contact_id' => $already_contact->id,
                    'user_id' => $request->agent_id,
                    'created_by_id' => auth()->id(),
                ]);
                $contact_id = $already_contact->id;
            } else {
                $contact = new Contact();
                $contact->first_name = $request->first_name;
                $contact->last_name = $request->last_name;
                $contact->contact_source_id = 129;
                $contact->email = $request->email;
                $contact->mobile = validatePhoneNumber($request->mobile);

                if ($request->full_mobile) {
                    $contact->mobile = validatePhoneNumber($request->full_mobile);
                }

                $contact->agent_id = $request->agent_id;
                $contact->created_by_id = auth()->id();
                $contact->save();
                $contact_id = $contact->id;

                // $contact_type = new ContactContactType();
                // $contact_type->contact_id = $contact->id;
                // $contact_type->contact_type_id = 1;
                // $contact_type->save();
                // $contact_type = ContactContactType::create([
                //     'contact_id' => $contact->id,
                //     'contact_type_id' => 1,
                // ]);
                // add from relation contactTypes


            }

            $contact->contactTypes()->attach(3);
            $listingLead = ListingLead::find($request->listing_lead_id);
            $listingLead->updated_by_id = auth()->id();

            $listing = new Property();
            $listing->ref_no = generatePropertyRefNo();
            $listing->title = $request->title;
            $listing->for = $request->for;
            $listing->bed_id = $request->bed_id;
            $listing->bath_id = $request->bath_id;
            $listing->property_category_id = $request->property_category_id;

            if ($request->listing_location_type == "community_property") {
                $communityProperty = CommunityProperty::find($request->listing_location_id);
                $community_property_id = $communityProperty->id ?? null;
                $sub_community_id = $communityProperty->sub_community_id ?? null;
                $community_id = $communityProperty->community_id ?? null;
            } else {

                $subCommunity = SubCommunity::find($request->listing_location_id);
                $community_property_id = null;
                $sub_community_id = $subCommunity->id ?? null;
                $community_id = $subCommunity->community_id ?? null;
            }

            $listing->sub_community_id = $sub_community_id;
            $listing->community_id = $community_id;
            $listing->community_property_id = $community_property_id;

            $listing->publish_status = 'DRAFT';
            $listing->unit_no = $request->unit_no;
            $listing->owner_id = $contact_id;
            $listing->agent_id = $request->agent_id;
            $listing->created_by_id = auth()->id();
            $listing->withoutApproval()->save();

            $listingLead->property_id = $listing->id;
            $listingLead->contact_id = $contact_id;
            $listingLead->agent_id = $request->agent_id;
            $listingLead->status_id = 5;
            $listingLead->sub_community_id = $sub_community_id;
            $listingLead->community_id = $community_id;
            $listingLead->community_property_id = $community_property_id;

            $listingLead->for = $request->for;
            $listingLead->bed_id = $request->bed_id;
            $listingLead->bath_id = $request->bath_id;
            $listingLead->property_category_id = $request->property_category_id;
            $original = $listingLead->getOriginal() ?? [];
            $dirty = $listingLead->getDirty() ?? [];

            $original = array_intersect_key($original, $dirty);

            $listingLead->save();

            $listing_leads_old_notes = ListingLeadNote::where('listing_lead_id', $request->listing_lead_id)->get();

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

            foreach ($listing_leads_old_notes as $old_note) {
                $contactNote = new ContactNote();
                $contactNote->contact_id = $contact_id;
                $contactNote->note = "Note (Migrated from Listing Lead) => " . $old_note->note;
                $contactNote->created_by_id = auth()->id();
                $contactNote->status = 1;
                $contactNote->save();
            }
            DB::commit();
            $arr = ['attributes' => $dirty,
                'old' => $original, 'listing_lead_id' => $listingLead->id];
            activity()
                ->performedOn($listingLead)
                ->causedBy(auth()->user())
                ->withProperties($arr)
                ->event('updated')
                ->log(auth()->user()->first_name . ' has converted this listing lead to contact and created a listing with ref no ' . $listing->ref_no . '.');

            return redirect()->route('admin.listing.index', ['ref_number' => $listing->ref_no]);

            $result = array('status' => '1', 'msg' => "Draft listing added successfully with ref no " . $listing->ref_no . ".", 'id' => $listing->ref_no);
            return json_encode($result);
        } catch (\Throwable $th) {
            DB::rollBack();
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $th->getMessage());
            info($th->getMessage());
            return redirect()->back();
        }

    }

    public function leadToListingCheckDuplicate(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'unit_no' => 'required',
            'bath_id' => 'required',
            'bed_id' => 'required',
            'property_category_id' => 'nullable',

        ]);

        $property = Property::where('unit_no', $request->unit_no)->where('bath_id', $request->bath_id)
            ->where('bed_id', $request->bed_id)->where('property_category_id', $request->property_category_id)
            ->where($request->input('location_type') . "_id", $request->location_id)
            ->where('for', $request->for)
            ->first();

        if ($property) {
            return response(['status' => 1, 'data' => $property, 'message' => 'Duplicate Listing Found with ref no ' . $property->ref_no]);
        }

        $listingLead = ListingLead::where('unit_no', $request->unit_no)->where('bath_id', $request->bath_id)
            ->where('bed_id', $request->bed_id)->where('property_category_id', $request->property_category_id)
            ->where($request->input('location_type') . "_id", $request->location_id)
            ->whereJsonContains('for', $request->for)
            ->first();

        if ($listingLead) {
            return response(['status' => 1, 'data' => $listingLead, 'message' => 'Duplicate Listing Lead Found with ID ' . $listingLead->id]);
        }

        return response(['status' => 0, 'data' => null, 'message' => 'No Duplicate Listing Found']);

    }

    public function leadToListing(Request $request)
    {

        $request->validate([
            'first_name' => 'required',
            'mobile' => 'required',
            'for' => 'required',
            'unit_no' => 'required',
            'property_category_id' => 'required',
            'listing_location_type' => 'nullable',
            'listing_location_id' => 'nullable',

            'email' => 'nullable',
            'agent_id' => 'nullable',
            'bed_id' => 'nullable',
            'bath_id' => 'nullable',
        ]);

        $property = Property::where('unit_no', $request->unit_no)->where('bath_id', $request->bath_id)
            ->where('bed_id', $request->bed_id)->where('property_category_id', $request->property_category_id)
            ->where($request->input('listing_location_type') . "_id", $request->listing_location_id)
            ->where('for', $request->for)
            ->first();

        if ($property) {
            $result = array('status' => '3', 'msg' => 'Duplicate Listing Found with ref no ' . $property->ref_no, 'id' => $property->id);
            return json_encode($result);
        }

        $listingLead = ListingLead::where('unit_no', $request->unit_no)->where('bath_id', $request->bath_id)
            ->where('bed_id', $request->bed_id)->where('property_category_id', $request->property_category_id)
            ->where($request->input('listing_location_type') . "_id", $request->listing_location_id)
            ->whereJsonContains('for', $request->for)
            ->first();

        if ($listingLead) {
            $result = array('status' => '3', 'msg' => 'Duplicate Lead Found with ID ' . $listingLead->id, 'id' => $listingLead->id);
            return json_encode($result);
        }

        if ($request->agent_id && auth()->id() == 174) {
            $result = array('status' => '0', 'msg' => "Lead cannot be assigned");
            return json_encode($result);
        }
        try {
            // dd($request->all());
            $lead = Lead::find($request->lead_ref);
            $leadCriteria = LeadCriteria::where('lead_id', $request->lead_ref)->first(); // this needs to be saved in message
            DB::beginTransaction();
            $listing = new ListingLead();
            $listing->first_name = $request->first_name;
            $listing->last_name = $request->last_name;

            $listing->email = $request->email;
            $listing->mobile = $request->mobile;
            $listing->unit_no = $request->unit_no;
            $listing->source_id = $request->listing_source_id ?? null;
            $listing->medium_id = $request->listing_medium_id;
            $listing->type_id = $request->listing_type_id;
            $listing->telesales_id = $request->listing_telesales_id;
            // $listing->title = $request->title;
            $listing->status_id = 1;

            $listing->for = $request->for;
            if ($request->agent_id) {
                $listing->agent_id = $request->agent_id;
                $listing->status_id = 2;
            }

            $listing->property_category_id = $request->property_category_id;
            $listing->bed_id = $request->bed_id;
            $listing->bath_id = $request->bath_id;

            $listing->note = $leadCriteria?->bitrix_data ?? null;

            if ($request->listing_location_type == "community_property") {
                $communityProperty = CommunityProperty::find($request->listing_location_id);
                $community_property_id = $communityProperty->id ?? null;
                $sub_community_id = $communityProperty->sub_community_id ?? null;
                $community_id = $communityProperty->community_id ?? null;
            } else {

                $subCommunity = SubCommunity::find($request->listing_location_id);
                $community_property_id = null;
                $sub_community_id = $subCommunity->id ?? null;
                $community_id = $subCommunity->community_id ?? null;
            }

            $listing->sub_community_id = $sub_community_id;
            $listing->community_id = $community_id;
            $listing->community_property_id = $community_property_id;
            $listing->created_by_id = auth()->id();
            $listing->created_at = now();
            $listing->save();

            if ($request->note) {
                $obj = new LeadNote();
                $obj->lead_id = $lead->id;
                $obj->note = $request->note;
                $obj->created_by_id = auth()->id();
                $obj->save();
            }

            $leadNote = LeadNote::where('lead_id', $request->lead_ref)->get(); // this needs to be saved in notes
            foreach ($leadNote as $note) {
                $obj = new ListingLeadNote();
                $obj->listing_lead_id = $listing->id;
                $obj->note = 'Note (Migrated) => ' . $note?->note;
                $obj->save();
            }

            if ($lead) {
                $lead->type_id = $request->listing_type_id;
                $lead->converted_to_listing_lead = $listing->id;
                $lead->updated_by_id = auth()->id();
                $lead->status = 0;
                $lead->save();
            }

            DB::commit();

            $environment = \App::environment();
            if (auth()->user()->hasRole(['Telesales', 'Telesales Head', "Super Admin"])) {

                if ($environment == "production") {
                    $message = "<b>AX Secondary CRM</b>\nJust received a new <b>Listing Lead</b> via Telesales Agent " . auth()->user()->full_name . ". Kindly assign it to an agent.\n";
                    $message .= "\n<b> Property Details: </b> ";
                    $message .= "\n<b> ID: </b> " . $listing?->id;
                    $message .= "\n<b> Type: </b> " . $listing?->propertyCategory?->title;
                    $message .= "\n<b> Location: </b> " . $listing?->location;
                    $message .= "\n<b> Beds: </b> " . $listing?->bed?->name;
                    $message .= "\n\n<b> Owner Details: </b> ";
                    ($listing->first_name != " " ? $message .= "\n<b> Name: </b> " . $listing?->first_name . ' ' . $listing?->last_name : null);
                    ($listing->mobile ? $message .= "\n<b> Mobile: </b> " . $listing?->mobile : null);
                    ($listing->email ? $message .= "\n<b> Email: </b> " . $listing?->email : null);

                    $usersWithCommunity = UserLocation::where('parentable_type', 'App\Models\Community')
                        ->where('parentable_id', $listing->community_id)
                        ->pluck('user_id');
                    if ($usersWithCommunity) {
                        $agents = User::whereHas("roles", function ($q) {
                            $q->whereIn("name", ["Line Manager", "Team Leader", "Sales Director"]);
                        })
                            ->where('region_id', '<>', 2)
                            ->when($listing->community_id, function ($q) use ($usersWithCommunity) {
                                $q->whereIn('id', $usersWithCommunity);
                            })
                            ->where("status", true)->get();
                        foreach ($agents as $agent) {
                            if ($agent->telegram_chatid != null) {
                                $url = route('api.thirdparty.listingLeadAssignment', [Crypt::encryptString($listing->id), Crypt::encryptString($agent->id)]);

                                $this->sendTelegramMessageHtml($message, [$agent->telegram_chatid], $url, 'Assign Lead', 'lead_bot');


                                    telegramNotification('ListingLead',$listing->id,$agent->telegram_chatid,$agent->id,$message);


                            }
                        }
                    }
                }

            }

            // if ($environment == "local" && auth()->id() != 174) {
            //     $message = "<b>AX Secondary CRM</b>\n\n<b>New Listing Lead:</b>\nA new listing lead has been received. Please assign it to an agent.";
            //     $agents = User::whereHas("roles", function ($q) {
            //         $q->whereIn("name", ["Line Manager", "Team Leader"]);
            //     })->where("status", true)->get();
            //     foreach ($agents as $agent) {
            //         if ($agent->telegram_chatid != null) {
            //             $this->sendTelegramMessageHtml($message, [$agent->telegram_chatid], null, null, 'lead_bot');
            //         }
            //     }
            // }

            $result = array('status' => '1', 'msg' => "Lead to Listing converted successfully.", 'id' => $listing->id);
            return json_encode($result);
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }

    }

    public function exportExcel(Request $request)
    {
        $request->validate([
            'columns' => 'required|array',
            'filters' => 'nullable|array',
        ]);

        return Excel::download(new ListingLeadExport($request->columns, $request), "listingleads-" . time() . ".xlsx");
    }

    public function saveReason(Request $request)
    {
        try {
            $lead = ListingLead::find($request->id);
            $lead->status_id = 6;
            $lead->updated_by_id = auth()->id();
            $lead->save();

            $listingLeadNote = new ListingLeadNote();
            $listingLeadNote->listing_lead_id = $lead->id;
            $listingLeadNote->note = 'The Listing lead has been marked as unsuccessful. Reason: ' . $request->unsuccessful_note;
            $listingLeadNote->created_by_id = auth()->id();
            $listingLeadNote->status = 1;
            $listingLeadNote->save();

            $result = array('status' => '1', 'msg' => "Listing lead marked as unsuccessful.");
            return json_encode($result);

        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            return json_encode($result);
        }
    }

    public function startCall(Request $request)
    {
        $request->validate([
            "listing_lead_id" => "required|exists:listing_leads,id",
        ]);

        try {
            $listing_lead = ListingLead::find($request->listing_lead_id);
            if (empty($listing_lead->mobile)) {
                $result = array('status' => '0', 'msg' => 'Please update mobile number.');
                return json_encode($result);
            }

            // $agentEmail = $lead->agent->email;
            // if (empty($agentEmail)) {
            //     $result = array('status' => '0', 'msg' => 'Please update agent\'s email.');
            //     return json_encode($result);
            // }

            $user = User::where('id', $request->dailer_id)->first();
            $pilot_testers = config('pilot_testing.call');

            if (checkBlocklist($listing_lead->mobile, Lead::class, null)) {
                $result = array('status' => '0', 'msg' => 'This number is in Blacklist. Please do not contact with this number.', 'error' => 'This number is in Blacklist. Please do not contact with this number.');
                return json_encode($result);
            }
            $full_name = $listing_lead->first_name ?? 'Null' . ' ' . $listing_lead->last_name ?? 'Null';
            $callStared = $this->dialANumber($user->email, $listing_lead->mobile, $full_name); // Uncomment this

            if ($callStared == "error") {
                $callStared = $this->makeCall($user->email, $listing_lead->mobile); // Uncomment this
            }

            // $user = User::where('id', $request->dailer_id)->first();
            // $pilot_testers = config('pilot_testing.call');

            // if (in_array($user->id, $pilot_testers)) {
            //     if (checkBlocklist($listing_lead->mobile, ListingLead::class, null)) {
            //         $result = array('status' => '0', 'msg' => 'This number is in Blacklist. Please do not contact with this number.', 'error' => 'This number is in Blacklist. Please do not contact with this number.');
            //         return json_encode($result);
            //     }
            //     $full_name = $listing_lead->first_name ?? 'Null' . ' ' . $listing_lead->last_name ?? 'Null';
            //     Log::channel('mobileApi')->info('PILOT TESTER: Agent: ' . $user->email . ' Lead Contact: ' . $listing_lead->mobile . ' Full Name: ' . $full_name . ' Call Started');
            //     $callStared = $this->dialANumber($user->email, $listing_lead->mobile, $full_name); // Uncomment this

            // } else {
            //     if (checkBlocklist($listing_lead->mobile, ListingLead::class, null)) {
            //         $result = array('status' => '0', 'msg' => 'This number is in Blacklist. Please do not contact with this number.', 'error' => 'This number is in Blacklist. Please do not contact with this number.');
            //         return json_encode($result);
            //     }
            //     $callStared = $this->makeCall($user->email, $listing_lead->mobile); // Uncomment this
            // }

            // dd($callStared);

            // $callStared = $this->makeCall($agentEmail, $lead->mobile); // Uncomment this
            //$callStared = $this->makeCall("test@axcrm.ae", "+971588159646");// comment this

            if ($callStared == "Make call posted" || $callStared == "success") {
                ListingLeadNote::create([
                    "listing_lead_id" => $listing_lead->id,
                    "note" => "Call started",
                    "created_by_id" => auth()->id(),
                ]);
                return response()->json(['status' => '1', 'msg' => 'Call started successfully.']);
            }

            return response()->json(['status' => '0', 'msg' => 'Could not start the call, please try again.']);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
            return json_encode($result);
        }
    }

    public function leadExport()
    {
        return Excel::download(new ListingLeadFilterExport, 'dialmydata.csv');
    }

    public function filterHelper(Request $request)
    {
        $data = [];
        $operators = config('configurations.filters.ListingLead.' . $request->type . '.operators');

        if ($request->type == 'created_from' || $request->type == 'updated_from' || $request->type == 'dob_search') {
            $data = [];
        }

        if ($request->type == 'baths') {
            $data = \App\Models\Bath::all();

        }
        if ($request->type == 'beds') {
            $data = \App\Models\Bed::all();
        }
        if ($request->type == 'property_ref') {
            $operators = config('configurations.filters.Lead.property.operators');

        }

        if ($request->type == 'status') {
            $data = \App\Models\ListingLeadStatus::where('status', '1')->get();
        }

        if ($request->type == 'sources') {
            $data = \App\Models\Source::where('status', '1')->get();
        }
        if ($request->type == 'medium') {
            $data = \App\Models\Medium::where('status', '1')->get();
        }
        if ($request->type == 'type') {
            $data = \App\Models\Type::where('status', '1')->get();
        }
        if ($request->type == 'agents' || $request->type == 'previous_agents') {
            $notAdmin = auth()->user()->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);
            $childrenIds = [];
            if ($notAdmin) {
                $childrenIds = getUserChildrenIds(auth()->id());
            }
            $data = User::where('status', true)->whereHas("roles", function ($q) {
                $q->whereIn("name", ["Agent", "Primary Agent", "Sales Director", "Team Leader", "Line Manager"]);
            })->when($notAdmin, fn($q) => $q->whereIn("id", $childrenIds))->get();
        }

        return response()->json(['status' => '1', 'msg' => 'Data Found', 'data' => $data, 'operators' => $operators ?? []]);
    }
    public function autocomplete(Request $request)
    {

        $data = [];

        $superAdmin = auth()->user()->hasRole(['Super Admin']);
        $notAdmin = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }
        if ($request->filled('term')) {
            // $data = Property::select("title", "id")
            //             ->where('title', 'LIKE', '%'. $request->get('q'). '%')
            //             ->get()->take(10);

            $data = Property::where(function ($query) use ($request) {
                $term = '%' . $request->get('term') . '%';
                $query->where('title', 'LIKE', $term)
                    ->orWhere('ref_no', 'LIKE', $term)
                    ->orWhereHas('community', function ($query) use ($term) {
                        $query->where('name', 'LIKE', $term);
                    })
                    ->orWhereHas('subCommunity', function ($query) use ($term) {
                        $query->where('name', 'LIKE', $term);
                    })
                    ->orWhereHas('owner', function ($query) use ($term) {
                        $query->where('first_name', 'LIKE', $term)
                            ->orWhere('last_name', 'LIKE', $term);
                    });
            })
                ->whereIn('agent_id', $childrenIds)
                ->where('publish_status', '!=', 'ARCHIVED')
                ->limit(10)
                ->get();

        } else {
            $data = Property::with('bed')->whereIn("agent_id", $childrenIds)

                ->whereNot('publish_status', 'ARCHIVED')->latest()->limit(10)->get();
        }

        return response()->json($data);
    }
}
