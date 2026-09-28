<?php

namespace App\Http\Controllers\Admin\Lead;

use App\Events\LeadTransferAppNotification;
use App\Exports\BitrixLead;
use App\Exports\LeadExport;
use App\Http\Controllers\AjaxController;
use App\Http\Controllers\Controller;
use App\Models\Bath;
use App\Models\Bed;
use App\Models\Community;
use App\Models\CommunityProperty;
use App\Models\Country;
use App\Models\Lead;
use App\Models\LeadAgent;
use App\Models\LeadCriteria;
use App\Models\LeadCriteriaLocation;
use App\Models\LeadNote;
use App\Models\LeadStatus;
use App\Models\LeadSubStatus;
use App\Models\LeadType;
use App\Models\Medium;
use App\Models\Note;
use App\Models\Property;
use App\Models\PropertyCategory;
use App\Models\Region;
use App\Models\Shortlink;
use App\Models\Source;
use App\Models\SubCommunity;
use App\Models\Type;
use App\Models\User;
use App\Traits\CallTrait;
use App\Traits\FiltersTrait;
use App\Traits\TelegramTrait;
use Auth;
use Carbon\Carbon;
use Crypt;
use DataTables;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class LeadController extends Controller
{
    use TelegramTrait;
    use CallTrait, FiltersTrait;

    public function index()
    {

        $notAdmin = auth()->user()->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent", "Head of Secondary"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }
        // dd(Lead::where('lead_status_id',1)->where('agent_id',)->count());

        // $query = Lead::query();
        // $query = $query->where('lead_status_id', 1)->where('is_archive', 0)->whereNotNull('agent_id');
        // $query = $query->where(function ($q) {
        //     $q->whereHas('agent', function ($q) {
        //         $q->where('status', 0);
        //     });
        // });

        // dd($query->count());

        // get name of all unique type of leads
        abort_if(Gate::denies("Leads List"), 403);
        //$types = Lead::select('type_id')->distinct()->get();

        $types = Lead::where('type_id', '!=', null)->distinct()->pluck('type_id');

        $data['lead_types'] = Type::whereIn('id', $types)->get();

        if (auth()->user()->hasRole(["Agent"])) {
            $data['types'] = Type::where('is_lead_type', 1)->where('status', true)->orderBy('order', 'asc')->get();
        } else {
            $data['types'] = $data['lead_types'];
        }

        $data['sources'] = Source::where('is_lead_source', 1)->where('status', true)->orderBy('order', 'asc')->get();
        $data['mediums'] = Medium::where('is_lead_medium', 1)->where('status', true)->orderBy('order', 'asc')->get();
        $data['categories'] = PropertyCategory::all();
        $data['managers'] = User::where('status', true)->whereHas("roles", function ($q) {
            $q->whereIn("name", ["Line Manager", "Team Leader", "Sales Director", "Head of Secondary"]);
        })->when($notAdmin, fn($q) => $q->whereIn("parent_id", $childrenIds)->orWhere('id', auth()->user()->id))->where("status", true)->get();

        if (auth()->user()->hasRole(['Admin', 'Super Admin'])) {
            $data['statuses'] = LeadStatus::where('status', true)->orderBy('order', 'asc')->get();
        } else {
            $data['statuses'] = LeadStatus::where('id', '!=', 3)->where('status', true)->orderBy('order', 'asc')->get();
        }

        // $data['statuses'] = LeadStatus::where('status', true)->orderBy('order', 'asc')->get();

        $data['countries'] = Country::where('status', true)->get();
        $data['properties'] = Property::latest()->limit(10)->get();
        $data['beds'] = Bed::where('status', true)->orderBy('order', 'asc')->get();
        $data['baths'] = Bath::where('status', true)->orderBy('order', 'asc')->get();
        $data['telesales'] = User::whereHas("roles", function ($q) {
            $q->whereIn("name", ["Telesales"]);
        })->get();
        $data['regions'] = Region::where('status', true)->get();

        $data['telesalesheads'] = User::where('status', true)->whereHas("roles", function ($q) {
            $q->whereIn("name", ["Telesales Head"]);
        })->get();
        $notAdmin = auth()->user()->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }
        $data['agents'] = User::where('status', true)->whereHas("roles", function ($q) {
            $q->whereIn("name", ["Agent", "Sales Director", "Team Leader", "Line Manager", "Head of Secondary"]);
        })->when($notAdmin, fn($q) => $q->whereIn("id", $childrenIds))->get();
        $data['all_active_agents'] = User::where('status', true)->whereHas("roles", function ($q) {
            $q->whereIn("name", ["Agent", "Sales Director", "Team Leader", "Line Manager", "Head of Secondary"]);
        })->get();
        // $data['open_sub_statuses'] = LeadSubStatus::where('lead_status_id',1)->where('status', true)->orderBy('order')->get();
        // $data['closed_sub_statuses'] = LeadSubStatus::where('lead_status_id',2)->where('status', true)->orderBy('order')->get();

        if (auth()->user()->hasRole(['Admin', 'Super Admin'])) {
            $data['open_sub_statuses'] = LeadSubStatus::where('lead_status_id', 1)->where('status', true)->orderBy('order')->get();
            $data['closed_sub_statuses'] = LeadSubStatus::where('lead_status_id', 2)->where('status', true)->orderBy('order')->get();
        } else {
            $data['open_sub_statuses'] = LeadSubStatus::where('lead_status_id', 1)->where('id', '!=', 8)->where('status', true)->orderBy('order')->get();
            $data['closed_sub_statuses'] = LeadSubStatus::where('lead_status_id', 2)->where('status', true)->orderBy('order')->get();
        }

        // dd($data['lead_types']);
        return view('admin.lead.index')->with($data);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            "agent_id" => "nullable|exists:users,id",
            "for" => "nullable|array",
            "first_name" => "nullable|string",
            "last_name" => "nullable|string",
            "mobile" => "nullable|regex:/^\+\d{1,3}\d{6,14}$/|min:8",
            "phone" => "nullable|regex:/^\+\d{1,3}\d{6,14}$/|min:8",
            "whatsapp" => "nullable|string|min:8",
            "email" => "nullable|email:rfc,dns",
            "nationality_id" => "nullable|exists:countries,id",
            "gender" => "nullable|string",
            "dob" => "nullable|date",
            "follow_up_date" => "nullable|date",
            "address" => "nullable|string",
            "company" => "nullable|string",
            "property_id" => "nullable|exists:properties,id",
            "type_id" => "nullable|exists:types,id",
            "source_id" => "nullable|exists:sources,id",
            "lead_medium_id" => "nullable|exists:mediums,id",
            "lead_status_id" => "nullable|exists:lead_statuses,id",
            // "lead_sub_status_id" => "nullable|exists:lead_sub_statuses,id",

            "telesales_head_id" => "nullable|exists:users,id",
            "telesales_id" => "nullable|exists:users,id",

            "priority" => "nullable|string",
        ]);

        if (checkBlocklist($request->mobile, Lead::class, $request)) {
            $result = array('status' => '0', 'msg' => 'This number is in Blacklist. Please do not contact with this number.', 'error' => 'This number is in Blacklist. Please do not contact with this number.');
            return json_encode($result);
        }

        try {
            $validatedData["created_by_id"] = auth()->id();
            $validatedData["status"] = 1;
            // $validatedData["lead_status_id"] = 3;
            // $validatedData["lead_sub_status_id"] = 8;
            $validatedData["for"] = is_null($request->for) ? [] : $request->for;
            if (auth()->user()->hasRole('Telesales')) {
                $validatedData["telesales_id"] = auth()->id();
            }
            if (auth()->user()->hasRole('Telesales Head')) {
                $validatedData["telesales_head_id"] = auth()->id();
            }
            $validatedData['dob'] = $request->dob ? Carbon::parse($request->dob)->format('Y-m-d') : null;
            $lead = Lead::create($validatedData);

            if ($request->phone) {
                $lead->phone = validatePhoneNumber($request->phone);
            }

            if ($request->mobile) {
                $lead->mobile = validatePhoneNumber($request->mobile);
            }
            $lead->save();

            event(new \App\Events\LeadCreated($lead));
            $arr = [];
            $arr['lead_id'] = $lead->id;
            $arr['min_bed_id'] = $request->min_bed_id;
            $arr['max_bed_id'] = $request->max_bed_id;
            $arr['min_bath_id'] = $request->min_bath_id;
            $arr['max_bath_id'] = $request->max_bath_id;
            $arr['min_price'] = $request->min_price;
            $arr['max_price'] = $request->max_price;
            $arr['communities'] = $request->communities;
            $arr['sub_communities'] = $request->sub_communities;
            $arr['furnished'] = $request->furnished;
            $arr['view'] = $request->view;
            $arr['created_by_id'] = auth()->user()->id;
            // $leadCriteria = LeadCriteria::create($arr);
            $this->storeCriteria($arr);
            // $leadCriteria = new LeadCriteria();
            // $leadCriteria->lead_id = $lead->id;
            // $leadCriteria->min_bed_id = $request->min_bed_id;
            // $leadCriteria->max_bed_id = $request->max_bed_id;
            // $leadCriteria->min_bath_id = $request->min_bath_id;
            // $leadCriteria->max_bath_id = $request->max_bath_id;
            // $leadCriteria->min_price = $request->min_price;
            // $leadCriteria->max_price = $request->max_price;
            // $leadCriteria->communities = !empty($request->sub_community_id) ? $request->sub_community_id : null;
            // $leadCriteria->furnished = $request->furnished;
            // $leadCriteria->created_by_id = auth()->user()->id;
            // $leadCriteria->save();
            return response()->json(['status' => '1', 'msg' => 'Lead Added Successfully', "id" => $lead->id]);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }
    public function storeLeadCriteria(Request $request)
    {
        //  dd($request->all());
        $validatedData = $request->validate([
            "lead_id" => "nullable|exists:leads,id",
            "min_bed_id" => "nullable|exists:beds,id",
            "max_bed_id" => "nullable|exists:beds,id",
            "min_bath_id" => "nullable|exists:baths,id",
            "max_bath_id" => "nullable|exists:baths,id",
            "min_price" => "nullable|numeric",
            "max_price" => "nullable|numeric",
            "sub_communities" => "nullable|array",
            "furnished" => "nullable|string",
        ]);
        try {
            $communities = $request->communities;
            //$locations = $request->locations;

            if (LeadCriteria::where('lead_id', $request->lead_id)->exists()) {
                $leadCriteria = LeadCriteria::where('lead_id', $request->lead_id)->first();
                $flag = 'updated';
            } else {
                $leadCriteria = new LeadCriteria();
                $flag = 'created';
            }
            $leadCriteria->lead_id = $request->lead_id;
            $leadCriteria->min_bed_id = $request->min_bed_id;
            $leadCriteria->max_bed_id = $request->max_bed_id;
            $leadCriteria->min_bath_id = $request->min_bath_id;
            $leadCriteria->max_bath_id = $request->max_bath_id;
            $leadCriteria->min_price = $request->min_price;
            $leadCriteria->max_price = $request->max_price;
            $leadCriteria->communities = !empty($request->sub_community_id) ? $request->sub_community_id : null;
            $leadCriteria->furnished = $request->furnished;
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
                $sub_communities = $request->sub_communities;
                if (!empty($sub_communities) && is_array($sub_communities)) {
                    foreach ($sub_communities ?? [] as $sub_location) {
                        $sub_communities = $request->sub_communities;
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

                $arr = [
                    'attributes' => $dirty,
                    'old' => $flag == 'created' ? [] : $original,
                    'lead_id' => $leadCriteria->lead_id
                ];
                activity()
                    ->performedOn($leadCriteria)
                    ->causedBy(auth()->user())
                    ->withProperties($arr)
                    ->event($flag)
                    ->log(auth()->user()->first_name . ' has ' . $flag . ' lead criteria');

                return response()->json(['status' => '1', 'msg' => 'Lead criteria ' . $flag . ' successfully']);
            } else {
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }
        } catch (\Exception $exception) {
            // dd($exception);
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
            return response()->json($result, 500);
        }
        return json_encode($result);
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

            $leadCriteria->lead_id = $arr['lead_id'] ?? null;
            $leadCriteria->min_bed_id = $arr['min_bed_id'] ?? null;
            $leadCriteria->max_bed_id = $arr['max_bed_id'] ?? null;
            $leadCriteria->min_bath_id = $arr['min_bath_id'] ?? null;
            $leadCriteria->max_bath_id = $arr['max_bath_id'] ?? null;
            $leadCriteria->min_price = cleanData($arr['min_price']) ?? null;
            $leadCriteria->max_price = cleanData($arr['max_price']) ?? null;
            $leadCriteria->view = $validatedData["view"] = json_encode($arr['view']) ?? null;
            $leadCriteria->communities = !empty($arr['sub_community_id']) ? $arr['sub_community_id'] : null;

            $leadCriteria->furnished = $arr['furnished'];
            $original = $leadCriteria->getOriginal() ?? [];
            $dirty = $leadCriteria->getDirty() ?? [];
            $leadCriteria->created_by_id = auth()->user()->id ?? null;

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

                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }
        } catch (\Exception $exception) {
            // dd($exception);
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());

            return response()->json($result, 500);
        }
        // info($result);
        return json_encode($result);
    }

    public function update(Request $request)
    {
        abort_if(Gate::denies("Leads Edit"), 403);
        //    dd($request->all());
        //"mobile" => "nullable|numeric|unique:leads,mobile," . $request->id,
        $validatedData = $request->validate([
            "id" => "required|exists:leads,id",
            "agent_id" => "nullable|exists:users,id",
            "for" => "nullable|array",
            "first_name" => "nullable|string",
            "last_name" => "nullable|string",
            "mobile" => "nullable|string|min:8",
            "phone" => "nullable|string|min:8",
            "whatsapp" => "nullable|string|min:8",
            //"email" => "nullable|email|unique:leads,email," . $request->id,
            "email" => "nullable|email:rfc,dns",
            "nationality_id" => "nullable|exists:countries,id",
            "gender" => "nullable|string",
            "dob" => "nullable|date",
            "follow_up_date" => "nullable|date",
            "address" => "nullable|string",
            "company" => "nullable|string",
            "property_id" => "nullable|exists:properties,id",
            "type_id" => "nullable|exists:types,id",
            "source_id" => "nullable|exists:sources,id",
            "lead_medium_id" => "nullable|exists:mediums,id",

            "lead_status_id" => "nullable|exists:lead_statuses,id",
            // "lead_sub_status_id" => "nullable|exists:lead_sub_statuses,id",

            "telesales_head_id" => "nullable|exists:users,id",
            "telesales_id" => "nullable|exists:users,id",

            "priority" => "nullable|string",
        ]);

        if (checkBlocklist($request->mobile, Lead::class, $request)) {
            $result = array('status' => '0', 'msg' => 'This number is in Blacklist. Please do not contact with this number.', 'error' => 'This number is in Blacklist. Please do not contact with this number.');
            return json_encode($result);
        }

        try {
            $validatedData["updated_by_id"] = auth()->user()->id;
            $validatedData["for"] = is_null($request->for) ? [] : $request->for;
            $lead = Lead::find($request->id);

            if ($lead->agent_id != $request->agent_id){
                $validatedData['agent_id'] = $request->agent_id;
                $validatedData['lead_status_id'] = 1;
                $validatedData['portal_lead'] = false;
                $validatedData['transfer_to_portal_lead_time'] = Carbon::now()->addHours(24);
                $validatedData['portal_lead_transferred_date'] = null;
                $validatedData['transfer_to_portal_lead_warning_sent_at'] = null;
                $validatedData['in_progress_time'] = null;
                $validatedData['in_progress_reminder_sent_at'] = null;
                $validatedData['in_progress_warning_sent_at'] = null;
                $validatedData['qualified_time'] = null;
                $validatedData['qualified_warning_sent_at'] = null;
                $validatedData['under_offer_time'] = null;
                $validatedData['under_offer_warning_sent_at'] = null;

                if ($lead->portal_lead){
                    $validatedData['taken_from_portal_lead'] = true;
                    $validatedData['taken_from_portal_lead_date'] = now();
                }
            }


            $notAdmin = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
            $childrenIds = [];
            if ($notAdmin) {
                $childrenIds = getUserChildrenIds(auth()->id());
                if (!in_array($lead->agent_id, $childrenIds)) {
                    return response()->json(['status' => '0', 'msg' => "You don't have permission to perform this action.!!"]);
                }
            }

            $original_status = $lead->lead_status_id;
            // dd($original_status);
            // if ($lead->lead_sub_status_id == 2 && $lead->lead_status_id == 2) {
            if ($lead->lead_status_id == 2) {
                return response()->json(['status' => '1', 'msg' => 'This lead cannot be updated because a contact has already been created from it.', "id" => $lead->id]);
            }

            // if ($lead->lead_status_id == 12) {
            //     return response()->json(['status' => '1', 'msg' => 'This lead is not updateable as it is Marked as unsucessfull.', "id" => $lead->id]);
            // }
            $validatedData['dob'] = $request->dob ? Carbon::parse($request->dob)->format('Y-m-d') : null;
            if ($lead->update($validatedData)) {

                if ($request->phone) {
                    $lead->phone = validatePhoneNumber($request->phone);
                }

                if ($request->phone) {
                    $lead->mobile = validatePhoneNumber($request->mobile);
                }

                $lead->save();

                // $leadCriteria = LeadCriteria::where('lead_id', $request->id)->first() ?? new LeadCriteria();
                // $leadCriteria->lead_id = $lead->id;
                // $leadCriteria->min_bed_id = $request->min_bed_id;
                // $leadCriteria->max_bed_id = $request->max_bed_id;
                // $leadCriteria->min_bath_id = $request->min_bath_id;
                // $leadCriteria->max_bath_id = $request->max_bath_id;
                // $leadCriteria->min_price = $request->min_price;
                // $leadCriteria->max_price = $request->max_price;
                // $leadCriteria->communities = !empty($request->sub_community_id) ? $request->sub_community_id : null;
                // $leadCriteria->furnished = $request->furnished;
                // $leadCriteria->created_by_id = auth()->user()->id;
                // $leadCriteria->save();

                $arr = [];
                $arr['lead_id'] = $lead->id;
                $arr['min_bed_id'] = $request->min_bed_id;
                $arr['max_bed_id'] = $request->max_bed_id;
                $arr['min_bath_id'] = $request->min_bath_id;
                $arr['max_bath_id'] = $request->max_bath_id;
                $arr['min_price'] = $request->min_price;
                $arr['max_price'] = $request->max_price;
                $arr['communities'] = $request->communities;
                $arr['sub_communities'] = $request->sub_communities;
                $arr['furnished'] = $request->furnished;
                $arr['view'] = $request->view;
                $arr['created_by_id'] = auth()->user()->id;

                // dd($arr);
                // $leadCriteria = LeadCriteria::create($arr);
                $this->storeCriteria($arr);

                // if ($request->lead_sub_status_id == 2 && $request->lead_status_id == 2) {
                //     if (Contact::where('email', $lead->email)->orWhere('mobile', $lead->mobile)->count() == 0) {
                //         $contact = new Contact();
                //         $contact->agent_id = $lead->agent_id;
                //         $contact->first_name = $lead->first_name;
                //         $contact->last_name = $lead->last_name;
                //         $contact->mobile = $lead->mobile;
                //         $contact->phone = $lead->phone;
                //         $contact->whatsapp = $lead->whatsapp;
                //         $contact->email = $lead->email;
                //         $contact->gender = $lead->gender;
                //         $contact->address = $lead->address;
                //         $contact->dob = $lead->dob;
                //         $contact->company = $lead->company;
                //         $contact->nationality_id = $lead->nationality_id;
                //         $contact->created_by_id = auth()->id();
                //         if ($contact->save()) {
                //             ContactNote::create([
                //                 "contact_id" => $contact->id,
                //                 "note" => 'This contact has been created from lead and the lead id is ' . $lead->id,

                //             ]);
                //         }
                //     }

                // }

                // dd($request->lead_status_id);
                // dd($request->all());

                if (auth()->user()->hasRole(['Telesales', 'Telesales Head'])) {
                    if ($request->lead_status_id == 8 && $original_status != $request->lead_status_id) {
                        $communities = '';
                        $sub_communities = '';
                        $locations = $lead->criteria->locations;
                        foreach ($locations as $location) {

                            if ($location->parentable_type == 'App\Models\Community') {
                                $communities = $communities . ' ' . Community::find($location->parentable_id)->name . ', ';
                            } else {
                                $sub_communities = $sub_communities . ' ' . $location->parentable_type::find($location->parentable_id)->name . ', ';
                            }
                        }

                        $environment = \App::environment();
                        if ($environment == "production") {
                            $bed = '';
                            if (Bed::find($request->min_bed_id)) {
                                $bed = Bed::find($request->min_bed_id)->name;
                                if (Bed::find($request->max_bed_id)) {
                                    $bed = Bed::find($request->min_bed_id)->name . ' - ' . Bed::find($request->max_bed_id)->name;
                                }
                            }
                            $type = '';
                            if (Type::find($request->type_id)) {
                                $type = Type::find($request->type_id)->name;
                            }

                            $message = "<b>AX Secondary CRM</b>\nA Lead has been Qualified by Telesales Agent  " . auth()->user()->full_name . ". Kindly assign it to an agent.\n";
                            $message .= "\n<b> Property Details: </b> ";
                            $message .= "\n<b> Type: </b> " . $type;
                            $message .= "\n<b> Community: </b> " . $communities;
                            $message .= "\n<b> location: </b> " . $sub_communities;
                            $message .= "\n<b> Beds: </b> " . $bed;
                            $message .= "\n<b> Budget: </b> " . formatPrice($request->min_price) . ' - ' . formatPrice($request->max_price);
                            $message .= "\n\n<b> Lead Details: </b> ";
                            ($request->first_name != " " ? $message .= "\n<b> Name: </b> " . $request?->first_name . ' ' . $request?->last_name : null);
                            ($request->mobile ? $message .= "\n<b> Mobile: </b> " . $request?->mobile : null);
                            ($request->email ? $message .= "\n<b> Email: </b> " . $request?->email : null);
                            $agents = User::whereHas("roles", function ($q) {
                                $q->whereIn("name", ["Line Manager", "Team Leader", "Sales Director"]);
                            })->where("status", true)->get();
                            // dd($message);
                            foreach ($agents as $agent) {
                                if ($agent->telegram_chatid != null) {
                                    $url = route('api.thirdparty.leadAssignment', [Crypt::encryptString($lead->id), Crypt::encryptString($agent->id)]);
                                    $this->sendTelegramMessageHtml($message, [$agent->telegram_chatid], $url, 'Assign Lead', 'lead_bot');
                                }
                                telegramNotification('Lead', $lead->id, $agent->telegram_chatid, $agent->id, $message);
                            }
                        }
                    }
                }

                return response()->json(['status' => '1', 'msg' => 'Lead Updated Successfully', "id" => $lead->id]);
            } else {
                return response()->json(['status' => '0', 'msg' => 'Something went wrong!!']);
            }
        } catch (\Exception $exception) {
            // dd($exception);
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }

    public function dataTable(Request $request)
    {

        abort_if(!auth()->user()->hasPermissionTo('Leads List'), 403, 'You do not have permission.');
        ajaxOnly($request);

        $columns = array(
            0 => 'updated_at',
            1 => 'id',
            2 => 'first_name',
            8 => 'previous_agent_id',
            10 => 'updated_at',
            11 => 'updated_at',
            12 => 'updated_at',
            13 => 'updated_at',
        );
        $superAdmin = auth()->user()->hasRole(['Super Admin']);
        $notAdmin = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        // if (auth()->user()->hasRole(['Telesales'])) {
        //     $query = Lead::query()->where('telesales_id', auth()->id())->whereNull('agent_id');

        // } else if (auth()->user()->hasRole(['Telesales Head'])) {
        //     $query = Lead::query()->whereNull('agent_id');
        // } else {
        $query = Lead::query();
        // }
        // where agent status is 1

        // $query = $query->whereHas('agent', function ($q) {
        //     $q->where('status', 1);
        // });
        // if lead has agent_id then check agent status
        if (!$request->inactive_agents) {
            $query = $query->where(function ($q) {
                $q->whereNull('agent_id')->orWhereHas('agent', function ($q) {
                    $q->where('status', 1);
                });
            });
        }
        $dataQuery = $this->applyLeadFilters($query, $request)
            ->when($notAdmin && (!auth()->user()->hasPermissionTo("Lead Pool List")), function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })
            ->when($notAdmin && $request->only_archive == 'false' && auth()->user()->hasPermissionTo("Lead Pool List") && $request->all_listings == "false" && $request->portal_lead == "false", function ($q) use ($childrenIds) {
                $q->where(function ($q) use ($childrenIds) {
                    $q->whereIn('agent_id', $childrenIds)->whereNull('converted_to_listing_lead');
                });
            })
            ->when(auth()->user()->hasRole("Admin - Abu Dhabi") && $request->all_listings == "false", function ($q) {
                $q->where('region_id', 2);
            })
            // ->when($request->all_listings == "false", function ($q) {
            //     $q->whereNotNull('agent_id');
            // })
            ->when($request->all_listings == "true", function ($q) {
                $q->where('is_archive', 0);
            })
            ->when($request->only_archive == "false", function ($q) {
                $q->where('is_archive', 0);
            })
            ->when($request->all_listings == 'false' && $request->only_archive == 'false' && $request->portal_lead == 'false', function ($q) {
                $q->whereNull('converted_to_listing_lead');
            });

//        return getSqlQuery($dataQuery, true);

        // dd($dataQuery->toSql());
        // Calculate total data count
        $totalData = $dataQuery->count();
        // $totalData = 0;

        // Calculate total filtered count
        $totalFiltered = $totalData;

        $data = $dataQuery->orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
            ->with('createdBy', 'leadSource', 'previousAgent', 'telesales', 'agent', 'property:id,ref_no,down_payment_price,property_category_id,community_property_id,community_id,sub_community_id', 'property.price:id,property_id,price', 'leadStatus', 'leadSubStatus', 'leadType', 'leadMedium', 'updatedBy')
            ->offset($request['start'])
            ->limit($request['length'])
            ->get();

        $json_data = array(
            "draw" => intval($request['draw']),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data,
        );
        // return DataTables::of($data)->make(true);

        return response()->json($json_data);
    }

    public function getLead(Request $request)
    {
        ajaxOnly($request);

        abort_if(Gate::denies('Leads View'), 403, 'You do not have permission.');
        $notAdmin = auth()->user()->hasRole(["Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        $lead = Lead::where('id', $request->id)
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->where(function ($q) use ($childrenIds) {
                    $q->whereIn('agent_id', $childrenIds);
                    $q->orWhere('agent_id', null);
                    $q->orWhere('is_archive', 1);
                });
            })
            ->with('agent')->first();

        if (!empty($lead)) {
            $lead->load(['criteria', 'property', 'criteria.locations.parentable', 'leadType', 'leadAgents', 'leadSubStatus', 'leadStatus', 'createdBy', 'updatedBy', 'leadMedium', 'documents', 'leadMeta'])->first();

            return response()->json(['status' => '1', 'msg' => 'Lead Found', 'data' => $lead]);
        } else {
            return response()->json(['status' => '0', 'msg' => 'Lead Not Found'], 422);
        }
    }
    public function delete(Request $request)
    {
        abort_if(Gate::denies("Leads Delete"), 403);
        ajaxOnly($request);
        $request->validate([
            'id' => 'required|exists:leads',

        ]);

        $type = Lead::find($request->id);
        if ($type->delete()) {
            $result = array('status' => '1', 'msg' => 'Lead deleted successfully');
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }
    public function bulkAssignLeads(Request $request)
    {
        abort_if(Gate::denies("Leads Bulk Action"), 403);
        $request->validate([
            'ids' => 'required|array',

        ]);
        if (gettype($request->ids) == 'array' && count($request->ids) > 0) {
            $ids = $request->ids;
            foreach ($ids as $lead) {
                $leadAgent = Lead::where('id', $lead)->with('agent')->first();
                $user = User::where('id', $request->agent_id)->first();
                $leadNotes = new Request();
                if ($leadAgent->agent) {
                    $msg = "Lead has been transferred from " . $leadAgent->agent->name . " to " . $user->name;
                } else {
                    $msg = "Lead has been assigned to " . $user->name;
                }
                $leadNotes->replace(['lead_id' => $lead, "note" => $msg]);
                $note = $this->addNote($leadNotes);

                if ($leadAgent->portal_lead){
                    $leadAgent->previous_agent_id = $leadAgent->agent_id;
                    $leadAgent->taken_from_portal_lead = true;
                    $leadAgent->taken_from_portal_lead_date = now();
                }

                $leadAgent->lead_status_id = 1;
                $leadAgent->portal_lead = false;
                $leadAgent->transfer_to_portal_lead_time = Carbon::now()->addHours(24);
                $leadAgent->portal_lead_transferred_date = null;
                $leadAgent->transfer_to_portal_lead_warning_sent_at = null;
                $leadAgent->in_progress_time = null;
                $leadAgent->in_progress_reminder_sent_at = null;
                $leadAgent->in_progress_warning_sent_at = null;
                $leadAgent->qualified_time = null;
                $leadAgent->qualified_warning_sent_at = null;
                $leadAgent->under_offer_time = null;
                $leadAgent->under_offer_warning_sent_at = null;

                $leadAgent->save();
            }
            $result = array('status' => '1', 'msg' => count($ids) . ' Leads assigned successfully');
        }

        return json_encode($result);
    }

    public function bulkAssignTelesales(Request $request)
    {
        abort_if(Gate::denies("Bulk Telesales Assignments"), 401);

        $request->validate([
            'ids' => 'required|array',

        ]);
        if (gettype($request->ids) == 'array' && count($request->ids) > 0) {
            $ids = $request->ids;
            foreach ($ids as $lead) {
                $leadAgent = Lead::where('id', $lead)->with('telesales')->first();
                $user = User::where('id', $request->telesales_id)->first();
                // $leadNotes = new Request();
                // if($leadAgent->agent){
                //     $msg = "Lead has been transferred from ".$leadAgent->agent->name." to ".$user->name;
                // }else{
                //     $msg = "Lead has been assigned to ".$user->name;
                // }
                // $leadNotes->replace(['lead_id' => $lead, "note"=> $msg]);
                // $note = $this->addNote($leadNotes);
            }
            if (Lead::whereIn('id', $ids)->update(['telesales_id' => $request->telesales_id])) {
                $result = array('status' => '1', 'msg' => count($ids) . ' Leads assigned successfully');
            } else {
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }
        }

        return json_encode($result);
    }

    public function bulkAssignTelesalesLeader(Request $request)
    {
        abort_if(Gate::denies("Bulk Telesales Head Assignments"), 401);

        $request->validate([
            'ids' => 'required|array',

        ]);
        if (gettype($request->ids) == 'array' && count($request->ids) > 0) {
            $ids = $request->ids;
            foreach ($ids as $lead) {
                $leadAgent = Lead::where('id', $lead)->with('telesales')->first();
                $user = User::where('id', $request->telesales_head_id)->first();
            }
            if (Lead::whereIn('id', $ids)->update(['telesales_head_id' => $request->telesales_head_id])) {
                $result = array('status' => '1', 'msg' => count($ids) . ' Leads assigned successfully');
            } else {
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }
        }

        return json_encode($result);
    }

    public function deleteMultiple(Request $request)
    {
        abort_if(Gate::denies("Bulk Delete Leads"), 403);
        $request->validate([
            'ids' => 'required|array',

        ]);
        if (gettype($request->ids) == 'array' && count($request->ids) > 0) {
            $ids = $request->ids;
            if (Lead::whereIn('id', $ids)->delete()) {
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
            'lead_id' => 'required|exists:leads,id',
            'note' => 'required|string',
        ]);

        try {
            $obj = new LeadNote();
            $obj->lead_id = $request->lead_id;
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
    public function addAgent(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'agent_id' => 'required|exists:users,id',
        ]);

        try {
            if (LeadAgent::where('lead_id', $request->lead_id)->where('user_id', $request->agent_id)->exists() || Lead::where('id', $request->lead_id)->where('agent_id', $request->agent_id)->exists()) {
                return response()->json(['status' => '0', 'msg' => 'Agent already added']);
            } else {
                $obj = new LeadAgent();
                $obj->lead_id = $request->lead_id;
                $obj->user_id = $request->agent_id;
                $obj->created_by_id = auth()->id();
                $original = $obj->getOriginal() ?? [];
                $dirty = $obj->getDirty() ?? [];
                if ($obj->save()) {
                    $obj->load("created_by");
                    $lead = Lead::where('id', $request->lead_id)->first();
                    $agent = User::where('id', $request->agent_id)->first();
                    $arr = [
                        'attributes' => $dirty,
                        'old' => $original,
                        'lead_id' => $lead->id
                    ];
                    activity()
                        ->performedOn($obj)
                        ->causedBy(auth()->user())
                        ->withProperties($arr)
                        ->event('created')
                        ->log(auth()->user()->first_name . ' has added ' . $agent->first_name . ' as additional agent');
                    return response()->json(['status' => '1', 'msg' => 'Agent added successfully', "lead_agent" => $obj, "agent" => User::where('id', $obj->user_id)->first()]);
                } else {
                    return response()->json(['status' => '0', 'msg' => 'Something went wrong!!']);
                }
            }
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
            return json_encode($result);
        }
    }
    public function getAgents(Request $request)
    {
        abort_if(Gate::denies("Leads View"), 403);
        $request->validate([
            'lead_id' => 'required|exists:leads,id',
        ]);

        try {
            $agents = LeadAgent::where('lead_id', $request->lead_id)->with('agent', 'created_by')->get();
            return response()->json(['status' => '1', 'msg' => 'Agents found', "agents" => $agents]);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
            return json_encode($result);
        }
    }

    public function removeAgent(Request $request)
    {
        abort_if(Gate::denies("Leads Edit"), 403);
        $request->validate([
            'id' => 'required|exists:lead_agents,id',
        ]);

        try {
            if (LeadAgent::where('id', $request->id)->exists()) {
                $leadAgent = LeadAgent::find($request->id);
                $original = $leadAgent->getOriginal() ?? [];
                $dirty = $leadAgent->getDirty() ?? [];
                $lead_id = $leadAgent->lead_id;
                $agent = User::where('id', $leadAgent->user_id)->first();
                if ($leadAgent->delete()) {
                    $arr = [
                        'attributes' => $dirty,
                        'old' => $original,
                        'lead_id' => $lead_id
                    ];
                    activity()
                        ->performedOn($leadAgent)
                        ->causedBy(auth()->user())
                        ->withProperties($arr)
                        ->event('deleted')
                        ->log(auth()->user()->first_name . ' has removed ' . $agent->first_name . ' from additional agent list');
                    return response()->json(['status' => '1', 'msg' => 'Agent removed successfully']);
                } else {
                    return response()->json(['status' => '0', 'msg' => 'Something went wrong!!']);
                }
            } else {
                return response()->json(['status' => '0', 'msg' => 'Agent not found']);
            }
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
            return json_encode($result);
        }
    }

    public function requestAccess(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:leads,id',
        ]);

        try {
            $lead = Lead::find($request->id);
            $agent = User::find($lead->agent_id);
            if (!is_null($agent)) {
                //Send Email to agent
                return response()->json(['status' => '1', 'msg' => 'Request sent successfully']);
            } else {
                return response()->json(['status' => '0', 'msg' => 'Agent not found']);
            }
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
            return json_encode($result);
        }
    }

    public function view(Lead $lead)
    {
        $lead->load(["criteria"]);
        $data['types'] = Type::where('is_lead_type', 1)->where('status', true)->get();
        $data['sources'] = Source::where('is_lead_source', 1)->where('status', true)->get();
        $data['mediums'] = Medium::where('is_lead_medium', 1)->where('status', true)->get();
        $data['statuses'] = LeadStatus::where('status', true)->get();
        $data['sub_statuses'] = LeadSubStatus::where('status', true)->get();
        $data['countries'] = Country::where('status', true)->get();
        $data['properties'] = Property::with("bed")->with("photos")->get();
        $data['beds'] = Bed::where('status', true)->get();
        $data['baths'] = Bath::where('status', true)->get();
        $data['sub_communities'] = SubCommunity::whereHas("users", fn($q) => $q->where("user_id", $lead->agent_id))->get();
        $data['agents'] = LeadAgent::where('lead_id', $lead->id)->with('agent', 'created_by')->get();
        $data["lead"] = $lead;
        return view("admin.lead.view")->with($data);
    }
    //
    //    public function getLeadCriteria(Request $request)
    //    {
    //       $data = LeadCriteria::where('lead_id', $request->id)->with('locations.parentable')->first();
    //
    //        if(!empty($data)){
    //            return response()->json(['status' => '1', 'msg' => 'Lead data Found', 'data' => $data]);
    //        }else{
    //            return response()->json(['status' => '0', 'msg' => 'Data Not Found']);
    //        }
    //    }

    public function startCall(Request $request)
    {
        $request->validate([
            "lead_id" => "required|exists:leads,id",
        ]);

        try {
            $lead = Lead::find($request->lead_id);
            if (empty($lead->mobile)) {
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
            if (checkBlocklist($lead->mobile, Lead::class, null)) {
                $result = array('status' => '0', 'msg' => 'This number is in Blacklist. Please do not contact with this number.', 'error' => 'This number is in Blacklist. Please do not contact with this number.');
                return json_encode($result);
            }
            $full_name = $lead->first_name ?? 'Null' . ' ' . $lead->last_name ?? 'Null';
            $callStared = $this->dialANumber($user->email, $lead->mobile, $full_name); // Uncomment this

            if ($callStared == "error") {
                $callStared = $this->makeCall($user->email, $lead->mobile); // Uncomment this
            }

            // if (checkBlocklist($lead->mobile, Lead::class, null)) {
            //     $result = array('status' => '0', 'msg' => 'This number is in Blacklist. Please do not contact with this number.', 'error' => 'This number is in Blacklist. Please do not contact with this number.');
            //     return json_encode($result);
            // }
            // if (in_array($user->id, $pilot_testers)) {
            //     $full_name = $lead->first_name ?? 'Null' . ' ' . $lead->last_name ?? 'Null';
            //     Log::channel('mobileApi')->info('PILOT TESTER: Agent: ' . $user->email . ' Lead Contact: ' . $lead->mobile . ' Full Name: ' . $full_name . ' Call Started');
            //     $callStared = $this->dialANumber($user->email, $lead->mobile, $full_name); // Uncomment this

            // } else {
            //     $callStared = $this->makeCall($user->email, $lead->mobile); // Uncomment this
            // }

            // dd($callStared);

            // $callStared = $this->makeCall($agentEmail, $lead->mobile); // Uncomment this
            //$callStared = $this->makeCall("test@axcrm.ae", "+971588159646");// comment this
            info($callStared);
            if ($callStared == "Make call posted" || $callStared == "success") {
                LeadNote::create([
                    "lead_id" => $lead->id,
                    "note" => "Call started",
                    "created_by_id" => auth()->id(),
                ]);
                return response()->json(['status' => '1', 'msg' => 'Call started successfully.']);
            }

            return response()->json(['status' => '0', 'msg' => 'Could not start the call, please try again.'], 400);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
            return response()->json($result, 400);
        }
    }

    public function assignAgents(Request $request)
    {
        abort_if(Gate::denies("Leads Assign Agent"), 403);
        // dd($request->all());
        $request->validate([
            'id' => 'required|exists:leads,id',
            'agents' => 'nullable|array',
            'agents.*' => 'nullable|exists:users,id',
        ]);

        try {
            $lead = Lead::find($request->id);
            $agents = $request->agents ?? [];
            $agents = array_combine($agents, array_fill(0, count($agents), ["created_by_id" => auth()->id(), "created_at" => now()]));
            $lead->agents()->sync($agents);
            return response()->json(['status' => '1', 'msg' => 'Contact assigned successfully']);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
            return json_encode($result);
        }
    }
    public function getActivityLog(Request $request)
    {
        $params = new Request();
        $params->replace($request->all());
        $res = (new AjaxController())->activityLog($params);
        if ($res->original['data'] != null) {
            $datas = [];
            foreach ($res->original['data'] as $data) {
                $properties['old'] = isset($data->properties['old']) ? leadColumnsReadable($data?->properties['old']) : [];
                $properties['attributes'] = isset($data->properties['attributes']) ? leadColumnsReadable($data?->properties['attributes']) : [];
                $data->properties = $properties;
                $datas[] = $data;
            }
        }

        $datas = array_reverse($datas);
        return view('admin.lead.include.partials.activity._activity_log')
            ->with('activities', $datas);

        return response()->json(['data' => $datas]);
    }

    public function archive(Request $request)
    {
        abort_if(Gate::denies("Leads Archive"), 401);
        // dd($request->all());
        // if(!auth()->user()->hasPermissionTo('Leads Archive')){
        //     $result = array('status' => '0', 'msg' => 'You are not authorized to archive leads.');
        //     return json_encode($result);
        // }

        // dd($request->all());

        $request->validate([
            'ids' => 'required',
            'archive_note' => 'required',
        ]);

        try {
            foreach ($request->ids as $id) {
                $lead = Lead::find($id);
                $lead->is_archive = 1;
                $lead->save();

                LeadNote::create([
                    "lead_id" => $lead->id,
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
    public function transferToLeadpool(Request $request)
    {
        abort_if(Gate::denies("Leads Transfer to Leadpool"), 401);

        $request->validate([
            'ids' => 'required',
            'transfer_note' => 'required',
        ]);

        try {
            DB::beginTransaction();
            foreach ($request->ids as $id) {
                $lead = Lead::find($id);
                if ($lead && $lead->agent_id) {
                    $lead->previous_agent_id = $lead->agent_id;
                    $lead->agent_id = null;
                    $lead->updated_at = now();
                    $lead->updated_by_id = auth()->id();
                    $lead->portal_lead = true;
                    $lead->portal_lead_transferred_date = now();
                    $lead->transfer_to_portal_lead_time = null;
                    $lead->taken_from_portal_lead = false;
                    $lead->save();

                    LeadNote::create([
                        "lead_id" => $lead->id,
                        "note" => "Transferred to Leadpool => " . $request->transfer_note,
                        'status' => 1,
                        "created_by_id" => auth()->id(),
                    ]);
                }
            }
            DB::commit();
            $result = array('status' => '1', 'msg' => "Lead(s) transferred to Leadpool successfully.");
            return json_encode($result);
        } catch (\Exception $exception) {
            DB::rollBack();
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            return json_encode($result);
        }
    }

    public function saveReason(Request $request)
    {
        //  dd($request->all());
        try {
            $lead = Lead::find($request->id);
            $lead->reason = $request->lead_reason;
            $lead->lead_status_id = 12;
            $lead->save();
            $result = array('status' => '1', 'msg' => "Lead(s) Status successfully.");
            return json_encode($result);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            return json_encode($result);
        }
    }

    public function generalBulk(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'ids' => 'required|array',
            'action' => 'required',
        ]);

        $portalLeadCount = 0;

        if ($request->action == 'Re-Assign') {

            abort_if(Gate::denies("Leads Assign Agent"), 401);
            if (gettype($request->ids) == 'array' && count($request->ids) > 0) {
                $ids = $request->ids;
                $counter = 0;

                foreach ($ids as $lead) {
                    $leadAgent = Lead::where('id', $lead)->first();
                    $user = User::where('id', $request->id)->first();

                    $leadNotes = new Request();
                    if ($leadAgent->agent) {
                        $msg = "Lead has been transferred from " . $leadAgent->agent->name . " to " . $user->name;
                    } else {
                        $msg = "Lead has been assigned to " . $user->name;
                    }
                    $leadNotes->replace(['lead_id' => $lead, "note" => $msg]);
                    $note = $this->addNote($leadNotes);

                    $previousAgent = $leadAgent->agent_id;
                    $leadAgent->agent_id = $request->id;
                    $leadAgent->previous_agent_id = $previousAgent;
                    $leadAgent->updated_by_id = auth()->user()->id;
                    $leadAgent->assigned_by_id = auth()->user()->id;
                    if ($leadAgent->portal_lead){
                        $portalLeadCount++;
                        $leadAgent->previous_agent_id = $leadAgent->agent_id;
                        $leadAgent->taken_from_portal_lead = true;
                        $leadAgent->taken_from_portal_lead_date = now();
                    }

                    if ($request->change_status == 'true') {
                        $leadAgent->lead_status_id = 1;
                        $leadAgent->portal_lead = false;
                        $leadAgent->transfer_to_portal_lead_time = Carbon::now()->addHours(24);
                        $leadAgent->portal_lead_transferred_date = null;
                        $leadAgent->transfer_to_portal_lead_warning_sent_at = null;
                        $leadAgent->in_progress_time = null;
                        $leadAgent->in_progress_reminder_sent_at = null;
                        $leadAgent->in_progress_warning_sent_at = null;
                        $leadAgent->qualified_time = null;
                        $leadAgent->qualified_warning_sent_at = null;
                        $leadAgent->under_offer_time = null;
                        $leadAgent->under_offer_warning_sent_at = null;
                    }
                    $leadAgent->save();
                    $counter++;

                    activity()
                        ->performedOn($leadAgent)
                        ->causedBy(auth()->user())
                        ->withProperties([])
                        ->event('updated')
                        ->log(auth()->user()->first_name . ' has added ' . $user->first_name . ' as Agent ');
                }

                // if (Lead::whereIn('id', $ids)->update(['agent_id' => $request->id, 'lead_status_id' => 1, 'updated_by_id' => auth()->user()->id])) {

                if ($counter > 0) {
                    if ($request->telegram_notification == 'true') {
                        $message = "\n<b> AX Secondary CRM</b>\n\n<b>" . $counter . " Lead(s)</b> have been transferred to you by " . auth()->user()->first_name . ' ' . auth()->user()->last_name . '.' . "\n\n";
                        $message .= "<b>Lead(s) Details:</b>\n";
                        // list all the ids
                        foreach ($ids as $id) {
                            $message .= "\n- <a href='" . route('admin.work.leads.index') . "?id=" . $id . "'>" . $id . "</a>";
                        }

                        $environment = \App::environment();
                        if ($environment == "production") {
                            $this->sendTelegramMessageHtml($message, [$user->telegram_chatid], null, null, 'lead_bot');
                        }

                        foreach ($ids as $id) {
                            // $message .= "\n- <a href='" . route('admin.work.leads.index') . "?id=".$id."'>" . $id . "</a>";
                            telegramNotification('Lead', $id, $user->telegram_chatid, $user->id, $message);
                        }
                    }
                    $assignment_by = User::where('id', auth()->user()->id)->first();
                    if ($environment == "production") {
                        event(new LeadTransferAppNotification("Lead", $leadAgent, 'transfer', $user, $assignment_by, $counter));
                    }
                    $result = array('status' => '1', 'msg' => $counter . ' Leads assigned successfully');
                } else {
                    $result = array('status' => '0', 'msg' => 'Something went wrong!!');
                }
            }
        } else if ($request->action == 'Telesales Head') {
            abort_if(Gate::denies("Bulk Telesales Head Assignments"), 401);
            if (gettype($request->ids) == 'array' && count($request->ids) > 0) {
                $ids = $request->ids;
                $counter = 0;
                foreach ($ids as $lead) {

                    $leadAgent = Lead::where('id', $lead)->first();
                    $user = User::where('id', $request->id)->first();
                    $leadAgent->telesales_head_id = $request->id;

                    if ($leadAgent->portal_lead){
                        $portalLeadCount++;
                        $leadAgent->previous_agent_id = $leadAgent->agent_id;
                        $leadAgent->taken_from_portal_lead = true;
                        $leadAgent->taken_from_portal_lead_date = now();
                    }

                    if ($request->change_status == 'true') {
                        $leadAgent->lead_status_id = 1;
                        $leadAgent->portal_lead = false;
                        $leadAgent->transfer_to_portal_lead_time = Carbon::now()->addHours(24);
                        $leadAgent->portal_lead_transferred_date = null;
                        $leadAgent->transfer_to_portal_lead_warning_sent_at = null;
                        $leadAgent->in_progress_time = null;
                        $leadAgent->in_progress_reminder_sent_at = null;
                        $leadAgent->in_progress_warning_sent_at = null;
                        $leadAgent->qualified_time = null;
                        $leadAgent->qualified_warning_sent_at = null;
                        $leadAgent->under_offer_time = null;
                        $leadAgent->under_offer_warning_sent_at = null;
                    }

                    $leadAgent->save();
                    $counter++;
                    activity()
                        ->performedOn($leadAgent)
                        ->causedBy(auth()->user())
                        ->withProperties([])
                        ->event('updated')
                        ->log(auth()->user()->first_name . ' has added ' . $user->first_name . ' as Telesales Head.');
                }
                if ($counter > 0) {

                    if ($request->telegram_notification == 'true') {
                        $message = "\n<b> AX Secondary CRM</b>\n\n<b>" . count($ids) . " Lead(s)</b> have been transferred to you by " . auth()->user()->first_name . ' ' . auth()->user()->last_name . '.' . "\n\n";
                        $environment = \App::environment();
                        if ($environment == "production") {
                            $this->sendTelegramMessageHtml($message, [$user->telegram_chatid], null, null, 'lead_bot');
                        }

                        foreach ($ids as $id) {
                            telegramNotification('Lead', $id, $user->telegram_chatid, $user->id, $message);
                        }
                    }
                    $assignment_by = User::where('id', auth()->user()->id)->first();
                    event(new LeadTransferAppNotification("Lead", $leadAgent, 'telesalesTransfer', $user, $assignment_by, $counter));

                    $result = array('status' => '1', 'msg' => count($ids) . ' Leads assigned successfully');
                } else {
                    $result = array('status' => '0', 'msg' => 'Something went wrong!!');
                }
            }
        } else if ($request->action == 'Telesales') {
            abort_if(Gate::denies("Bulk Telesales Assignments"), 401);
            if (gettype($request->ids) == 'array' && count($request->ids) > 0) {
                $ids = $request->ids;
                $counter = 0;
                foreach ($ids as $lead) {
                    $leadAgent = Lead::where('id', $lead)->first();
                    $user = User::where('id', $request->id)->first();
                    $leadAgent->telesales_id = $request->id;

                    if ($leadAgent->portal_lead){
                        $portalLeadCount++;
                        $leadAgent->previous_agent_id = $leadAgent->agent_id;
                        $leadAgent->taken_from_portal_lead = true;
                        $leadAgent->taken_from_portal_lead_date = now();
                    }
                    if ($request->change_status == 'true') {
                        $leadAgent->lead_status_id = 1;
                        $leadAgent->portal_lead = false;
                        $leadAgent->transfer_to_portal_lead_time = Carbon::now()->addHours(24);
                        $leadAgent->portal_lead_transferred_date = null;
                        $leadAgent->transfer_to_portal_lead_warning_sent_at = null;
                        $leadAgent->in_progress_time = null;
                        $leadAgent->in_progress_reminder_sent_at = null;
                        $leadAgent->in_progress_warning_sent_at = null;
                        $leadAgent->qualified_time = null;
                        $leadAgent->qualified_warning_sent_at = null;
                        $leadAgent->under_offer_time = null;
                        $leadAgent->under_offer_warning_sent_at = null;
                    }
                    $leadAgent->save();
                    $counter++;

                    activity()
                        ->performedOn($leadAgent)
                        ->causedBy(auth()->user())
                        ->withProperties([])
                        ->event('updated')
                        ->log(auth()->user()->first_name . ' has added ' . $user->first_name . ' as Telesales.');
                }
                if ($counter > 0) {
                    if ($request->telegram_notification == 'true') {
                        $message = "\n<b> AX Secondary CRM</b>\n\n<b>" . count($ids) . " Lead(s)</b> have been transferred to you by " . auth()->user()->first_name . ' ' . auth()->user()->last_name . '.' . "\n\n";
                        $environment = \App::environment();
                        if ($environment == "production") {
                            $this->sendTelegramMessageHtml($message, [$user->telegram_chatid], null, null, 'lead_bot');
                        }
                        foreach ($ids as $id) {
                            telegramNotification('Lead', $id, $user->telegram_chatid, $user->id, $message);
                        }
                    }
                    $assignment_by = User::where('id', auth()->user()->id)->first();
                    event(new LeadTransferAppNotification("Lead", $leadAgent, 'telesalesTransfer', $user, $assignment_by, count($ids)));

                    $result = array('status' => '1', 'msg' => count($ids) . ' Leads assigned successfully');
                } else {
                    $result = array('status' => '0', 'msg' => 'Something went wrong!!');
                }
            }
        }

        if ($portalLeadCount > 0) {
            if ($request->telegram_notification == 'true') {
                $message = "<b>AX Secondary CRM</b>\n\n";
                $message .= "Hi {$user->name},\n\n";
                $message .= "You have received <b>{$portalLeadCount} lead(s)</b> from the <b>Portal Lead Pool</b>, transferred by <b>".auth()->user()->name."</b>.\n\n";
                $message .= "Please take action within the timeframe, or the lead will be reverted.";

                $environment = \App::environment();
                if ($environment == "production") {
                    $this->sendTelegramMessageHtml($message, [$user->telegram_chatid], null, null, 'lead_bot');
                }
            }
        }

        return json_encode($result);
    }
    public function assignLeadsToMyself(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);
        $assigned_leads = Lead::where('assigned_by_id', auth()->user()->id)->whereDate('assigned_at', Carbon::today())->count();

        if (!auth()->user()->hasRole('Agent')) {
            return response()->json(['status' => '0', 'msg' => 'You are not authorized to assign leads to yourself.']);
        }

        if (Lead::where('agent_id', auth()->id())->whereDate('taken_from_portal_lead_date', Carbon::today())->count() >= 10) {
            return response()->json(['status' => '0', 'msg' => 'You cannot take more than 10 portal leads in a day.']);
        }
        $id = $request->id;
        if ($assigned_leads < 150) {
            $lead = Lead::where('id', $id)->whereNull('agent_id')->first();
            if ($lead) {
                if ($lead->previous_agent_id == auth()->id()){
                    return response()->json(['status' => '0', 'msg' => 'You are not allowed to take your own lead.']);
                }
                $lead->previous_agent_id = $lead->agent_id;
                $lead->agent_id = auth()->user()->id;
                $lead->lead_status_id = 1;
                $lead->assigned_by_id = auth()->user()->id;
                $lead->assigned_at = now();

                $note = 'This lead has been claimed from the Bitrix Lead Pool.';

                if ($lead->portal_lead){
                    $note = 'This lead has been claimed from the Portal Lead Pool.';
                    $lead->taken_from_portal_lead = true;
                    $lead->taken_from_portal_lead_date = now();
                }
                $lead->portal_lead = false;
                $lead->transfer_to_portal_lead_time = Carbon::now()->addHours(24);
                $lead->portal_lead_transferred_date = null;
                $lead->transfer_to_portal_lead_warning_sent_at = null;
                $lead->in_progress_time = null;
                $lead->in_progress_reminder_sent_at = null;
                $lead->in_progress_warning_sent_at = null;
                $lead->qualified_time = null;
                $lead->qualified_warning_sent_at = null;
                $lead->under_offer_time = null;
                $lead->under_offer_warning_sent_at = null;

                $lead->save();

                $leadnote = new LeadNote();
                $leadnote->lead_id = $lead->id;
                $leadnote->status = 1;
                $leadnote->note = $note;
                $leadnote->created_by_id = auth()->user()->id;
                $leadnote->save();

                activity()
                    ->performedOn($lead)
                    ->causedBy(auth()->user())
                    ->withProperties([])
                    ->event('updated')
                    ->log(auth()->user()->full_name . ' has added himself as Agent ');
            }
            $result = array('status' => '1', 'msg' => 'Lead assigned successfully');
        } else {
            $result = array('status' => '0', 'msg' => 'You can not assign more than 15 leads in a day. Total leads assigned today: ' . $assigned_leads . '.');
            // $result = array('status' => '1', 'msg' => $counter . ' Leads assigned successfully');
            return json_encode($result);
        }
        return json_encode($result);
    }


    public function assignArchivedLeadsToMyself(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);
        $assigned_leads = Lead::where('assigned_by_id', auth()->user()->id)->whereDate('assigned_at', Carbon::today())->count();

        if (!auth()->user()->hasRole('Agent')) {
            return response()->json(['status' => '0', 'msg' => 'You are not authorized to assign leads to yourself.']);
        }
        if (Lead::where('agent_id', auth()->id())->whereDate('taken_from_portal_lead_date', Carbon::today())->count() >= 10) {
            return response()->json(['status' => '0', 'msg' => 'You cannot take more than 10 portal leads in a day.']);
        }
        $id = $request->id;
        if ($assigned_leads < 150) {
            $lead = Lead::where('id', $id)->first();
            if ($lead) {
                if ($lead->previous_agent_id == auth()->id()){
                    return response()->json(['status' => '0', 'msg' => 'You are not allowed to take your own lead.']);
                }
                $lead->previous_agent_id = $lead->agent_id;
                $lead->agent_id = auth()->user()->id;
                $lead->lead_status_id = 1;
                $lead->is_archive = 0;
                $lead->assigned_by_id = auth()->user()->id;
                $lead->assigned_at = now();

                if ($lead->portal_lead){
                    $lead->taken_from_portal_lead = true;
                    $lead->taken_from_portal_lead_date = now();
                }
                $lead->portal_lead = false;
                $lead->transfer_to_portal_lead_time = Carbon::now()->addHours(24);
                $lead->portal_lead_transferred_date = null;
                $lead->transfer_to_portal_lead_warning_sent_at = null;
                $lead->in_progress_time = null;
                $lead->in_progress_reminder_sent_at = null;
                $lead->in_progress_warning_sent_at = null;
                $lead->qualified_time = null;
                $lead->qualified_warning_sent_at = null;
                $lead->under_offer_time = null;
                $lead->under_offer_warning_sent_at = null;

                $lead->save();

                $leadnote = new LeadNote();
                $leadnote->lead_id = $lead->id;
                $leadnote->status = 1;
                $leadnote->note = 'This lead has been claimed from the Archived Leads.';
                $leadnote->created_by_id = auth()->user()->id;
                $leadnote->save();

                activity()
                    ->performedOn($lead)
                    ->causedBy(auth()->user())
                    ->withProperties([])
                    ->event('updated')
                    ->log(auth()->user()->full_name . ' has added himself as Agent from Archived Leads ');
            }
            $result = array('status' => '1', 'msg' => 'Lead assigned successfully');
        } else {
            $result = array('status' => '0', 'msg' => 'You can not assign more than 15 leads in a day. Total leads assigned today: ' . $assigned_leads . '.');
            // $result = array('status' => '1', 'msg' => $counter . ' Leads assigned successfully');
            return json_encode($result);
        }
        return json_encode($result);
    }
    public function bulkAssignLeadsToMyself(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
        ]);
        $assigned_leads = Lead::where('assigned_by_id', auth()->user()->id)->whereDate('assigned_at', Carbon::today())->count();

        if (!auth()->user()->hasRole('Agent')) {
            return response()->json(['status' => '0', 'msg' => 'You are not authorized to assign leads to yourself.']);
        }

        if (gettype($request->ids) == 'array' && count($request->ids) > 0) {
            $ids = $request->ids;
            $counter = 0;
            foreach ($ids as $id) {
                $assigned_leads = Lead::where('assigned_by_id', auth()->user()->id)->whereDate('assigned_at', Carbon::today())->count();
                if ($assigned_leads < 15) {

                    $lead = Lead::where('id', $id)->whereNull('agent_id')->first();
                    if ($lead) {
                        $lead->previous_agent_id = $lead->agent_id;
                        $lead->agent_id = auth()->user()->id;
                        $lead->lead_status_id = 1;
                        $lead->assigned_by_id = auth()->user()->id;
                        $lead->assigned_at = now();
                        $note = 'This lead has been claimed from the Bitrix Lead Pool.';

                        if ($lead->portal_lead){
                            $note = 'This lead has been claimed from the Portal Lead Pool.';
                            $lead->taken_from_portal_lead = true;
                            $lead->taken_from_portal_lead_date = now();
                        }

                        $lead->portal_lead = false;
                        $lead->transfer_to_portal_lead_time = Carbon::now()->addHours(24);
                        $lead->portal_lead_transferred_date = null;
                        $lead->transfer_to_portal_lead_warning_sent_at = null;
                        $lead->in_progress_time = null;
                        $lead->in_progress_reminder_sent_at = null;
                        $lead->in_progress_warning_sent_at = null;
                        $lead->qualified_time = null;
                        $lead->qualified_warning_sent_at = null;
                        $lead->under_offer_time = null;
                        $lead->under_offer_warning_sent_at = null;
                        $lead->save();
                        $counter++;

                        $leadnote = new LeadNote();
                        $leadnote->lead_id = $lead->id;
                        $leadnote->status = 1;
                        $leadnote->note = $note;
                        $leadnote->created_by_id = auth()->user()->id;
                        $leadnote->save();

                        activity()
                            ->performedOn($lead)
                            ->causedBy(auth()->user())
                            ->withProperties([])
                            ->event('updated')
                            ->log(auth()->user()->full_name . ' has added himself as Agent ');
                    }
                } else {

                    $result = array('status' => '0', 'msg' => 'You can not assign more than 15 leads in a day. Total leads assigned today: ' . $assigned_leads . '.');
                    // $result = array('status' => '1', 'msg' => $counter . ' Leads assigned successfully');
                    return json_encode($result);
                }
            }

            if ($counter > 0) {
                if ($request->telegram_notification == 'true') {
                    $message = "\n<b> AX Secondary CRM</b>\n\n<b>" . $counter . " Lead(s)</b> have been transferred to you by " . auth()->user()->first_name . ' ' . auth()->user()->last_name . '.' . "\n\n";
                    $environment = \App::environment();
                    if ($environment == "production") {
                        $this->sendTelegramMessageHtml($message, [auth()->user()->telegram_chatid], null, null, 'lead_bot');
                    }
                    foreach ($ids as $id) {
                        telegramNotification('Lead', $id, auth()->user()->telegram_chatid, auth()->user()->id, $message);
                    }
                }
                $assignment_by = User::where('id', auth()->user()->id)->first();
                event(new LeadTransferAppNotification("Lead", $lead, 'selfTransfer', auth()->user(), $assignment_by, count($ids)));

                $result = array('status' => '1', 'msg' => $counter . ' Leads assigned successfully');
            } else {

                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }

            return json_encode($result);
        }
    }

    public function bulkAssignArchivedLeadsToMyself(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
        ]);
        $assigned_leads = Lead::where('assigned_by_id', auth()->user()->id)->whereDate('assigned_at', Carbon::today())->count();

        if (!auth()->user()->hasRole('Agent')) {
            return response()->json(['status' => '0', 'msg' => 'You are not authorized to assign leads to yourself.']);
        }

        if (gettype($request->ids) == 'array' && count($request->ids) > 0) {
            $ids = $request->ids;
            $counter = 0;
            foreach ($ids as $id) {
                $assigned_leads = Lead::where('assigned_by_id', auth()->user()->id)->whereDate('assigned_at', Carbon::today())->count();
                if ($assigned_leads < 15) {

                    $lead = Lead::where('id', $id)->first();
                    if ($lead) {
                        $lead->previous_agent_id = $lead->agent_id;
                        $lead->agent_id = auth()->user()->id;
                        $lead->lead_status_id = 1;
                        $lead->is_archive = 0;
                        $lead->assigned_by_id = auth()->user()->id;
                        $lead->assigned_at = now();

                        if ($lead->portal_lead){
                            $lead->taken_from_portal_lead = true;
                            $lead->taken_from_portal_lead_date = now();
                        }

                        $lead->portal_lead = false;
                        $lead->transfer_to_portal_lead_time = Carbon::now()->addHours(24);
                        $lead->portal_lead_transferred_date = null;
                        $lead->transfer_to_portal_lead_warning_sent_at = null;
                        $lead->in_progress_time = null;
                        $lead->in_progress_reminder_sent_at = null;
                        $lead->in_progress_warning_sent_at = null;
                        $lead->qualified_time = null;
                        $lead->qualified_warning_sent_at = null;
                        $lead->under_offer_time = null;
                        $lead->under_offer_warning_sent_at = null;
                        $lead->save();
                        $counter++;

                        $leadnote = new LeadNote();
                        $leadnote->lead_id = $lead->id;
                        $leadnote->status = 1;
                        $leadnote->note = 'This lead has been claimed from the Archived Leads.';
                        $leadnote->created_by_id = auth()->user()->id;
                        $leadnote->save();

                        activity()
                            ->performedOn($lead)
                            ->causedBy(auth()->user())
                            ->withProperties([])
                            ->event('updated')
                            ->log(auth()->user()->full_name . ' has added himself as Agent from Archived Leads ');
                    }
                } else {

                    $result = array('status' => '0', 'msg' => 'You can not assign more than 15 leads in a day. Total leads assigned today: ' . $assigned_leads . '.');
                    // $result = array('status' => '1', 'msg' => $counter . ' Leads assigned successfully');
                    return json_encode($result);
                }
            }

            if ($counter > 0) {
                if ($request->telegram_notification == 'true') {
                    $message = "\n<b> AX Secondary CRM</b>\n\n<b>" . $counter . " Lead(s)</b> have been transferred to you by " . auth()->user()->first_name . ' ' . auth()->user()->last_name . '.' . "\n\n";
                    $environment = \App::environment();
                    if ($environment == "production") {
                        $this->sendTelegramMessageHtml($message, [auth()->user()->telegram_chatid], null, null, 'lead_bot');
                    }
                    foreach ($ids as $id) {
                        telegramNotification('Lead', $id, auth()->user()->telegram_chatid, auth()->user()->id, $message);
                    }
                }
                $assignment_by = User::where('id', auth()->user()->id)->first();
                event(new LeadTransferAppNotification("Lead", $lead, 'selfTransfer', auth()->user(), $assignment_by, count($ids)));

                $result = array('status' => '1', 'msg' => $counter . ' Leads assigned successfully');
            } else {

                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }

            return json_encode($result);
        }
    }

    public function checkDuplicate()
    {
        $duplicates = Lead::select('email', 'mobile')
            ->groupBy('email', 'mobile')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        $result = [];

        foreach ($duplicates as $duplicate) {
            $leads = Lead::where('email', $duplicate->email)
                ->where('mobile', $duplicate->mobile)
                ->get(['id', 'first_name', 'last_name', 'email', 'mobile', 'property_id']);
            $result[] = $leads;
        }
        return response()->json(['status' => '1', 'msg' => 'Duplicate Leads', 'data' => $result]);
    }

    public function autocomplete(Request $request)
    {

        $notAdmin = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent", "Primary Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        $data = [];
        if ($request->filled('term')) {
            $search = $request->term;

            // $data = Property::select("title", "id")
            //             ->where('title', 'LIKE', '%'. $request->get('q'). '%')
            //             ->get()->take(10);

            $data = Property::when($notAdmin && !($request->all_listings == "true"), function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })
                ->when($notAdmin && $request->all_listings == "true", function ($q) use ($childrenIds) {
                    $q->where(function ($q) use ($childrenIds) {
                        $q->whereIn("agent_id", $childrenIds)
                            ->orWhere('publish_status', "POCKET")
                            ->orWhere('is_live', true);
                    });
                })
                ->when(auth()->user()->hasRole("Admin - Abu Dhabi") && $request->all_listings == "false", function ($q) {
                    $q->where("region_id", 2);
                })
                // ->where(function ($query) use ($search , $request)  {
                //     $query->where('title', 'LIKE', '%' . $search . '%')
                //         ->orWhere('ref_no',  $search . '%')
                //         ->orWhereHas('community', function ($query) use ($request,$search) {
                //             $query->where('name', 'LIKE', '%' . $search . '%');
                //         })
                //         ->orWhereHas('subCommunity', function ($query) use ($request,$search) {
                //             $query->where('name', 'LIKE', '%' . $search . '%');
                //         })
                //         ->orWhereHas('owner', function ($query) use ($request,$search) {
                //             $query->where(function ($query) use ($request,$search) {
                //                 $query->where('first_name', 'LIKE', '%' . $search . '%')
                //                     ->orWhere('last_name', 'LIKE', '%' . $search . '%');
                //             });
                //         });
                // })
                ->where('ref_no', 'LIKE', '%' . $search . '%')
                ->where('is_archived', false)
                ->limit(10)
                ->get();
        } else {

            $data = Property::with('bed')->when($notAdmin && !($request->all_listings == "true"), function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })
                ->when($notAdmin && $request->all_listings == "true", function ($q) use ($childrenIds) {
                    $q->where(function ($q) use ($childrenIds) {
                        $q->whereIn("agent_id", $childrenIds)->orWhere('publish_status', "POCKET")->orWhere('is_live', true);
                    });
                })
                ->when(auth()->user()->hasRole("Admin - Abu Dhabi") && $request->all_listings == "false", function ($q) {
                    $q->where("region_id", 2);
                })
                ->where('is_archived', false)
                ->latest()->limit(10)->get();
        }

        return response()->json($data);
    }

    public function autocompleteAgents(Request $request)
    {

        $data = [];
        if ($request->filled('q')) {
            $notAdmin = auth()->user()->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent", "Head of Secondary"]);
            $childrenIds = [];
            if ($notAdmin) {
                $childrenIds = getUserChildrenIds(auth()->id());
            }
            $data = User::where(function ($q) use ($request) {
                $q->where('first_name', 'LIKE', '%' . $request->get('q') . '%')->orWhere('last_name', 'LIKE', '%' . $request->get('q') . '%')->orWhere('name', 'LIKE', '%' . $request->get('q') . '%');
            })
                ->where('status', true)->whereHas("roles", function ($q) {
                    $q->whereIn("name", ["Agent", "Primary Agent", "Sales Director", "Team Leader", "Line Manager", "Head of Secondary"]);
                })->when($notAdmin, fn($q) => $q->whereIn("id", $childrenIds))->get();
        } else {
            $notAdmin = auth()->user()->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent", "Head of Secondary"]);
            $childrenIds = [];
            if ($notAdmin) {
                $childrenIds = getUserChildrenIds(auth()->id());
            }
            $data = User::where('status', true)->whereHas("roles", function ($q) {
                $q->whereIn("name", ["Agent", "Primary Agent", "Sales Director", "Team Leader", "Line Manager", "Head of Secondary"]);
            })->when($notAdmin, fn($q) => $q->whereIn("id", $childrenIds))->get();
        }
        return response()->json($data);
    }

    public function autocompleteManager(Request $request)
    {

        $data = [];
        if ($request->filled('q')) {
            $notAdmin = auth()->user()->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);
            $childrenIds = [];
            if ($notAdmin) {
                $childrenIds = getUserChildrenIds(auth()->id());
            }
            $data = User::where('first_name', 'LIKE', '%' . $request->get('q') . '%')->where('status', true)->whereHas("roles", function ($q) {
                $q->whereIn("name", ["Line Manager", "Team Leader", "Sales Director", "Head of Secondary"]);
            })->when($notAdmin, fn($q) => $q->whereIn("parent_id", $childrenIds)->orWhere('id', auth()->user()->id))->where("status", true)->limit(10)->get();
        } else {
            $notAdmin = auth()->user()->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);
            $childrenIds = [];
            if ($notAdmin) {
                $childrenIds = getUserChildrenIds(auth()->id());
            }
            $data = User::where('status', true)->whereHas("roles", function ($q) {
                $q->whereIn("name", ["Line Manager", "Team Leader", "Sales Director", "Head of Secondary"]);
            })->when($notAdmin, fn($q) => $q->whereIn("parent_id", $childrenIds)->orWhere('id', auth()->user()->id))->where("status", true)->limit(10)->get();
        }
        return response()->json($data);
    }

    public function statusUpadte(Request $request)
    {
        return "-";
        $key = LeadSubStatus::where('id', $request->id)->first();
        $count = Lead::where('lead_sub_status_id', $request->id)->get()->count();
        // return $key->name . ' (' . $count . ')';

        // get all leads even deleted ones
        // $leads = Lead::withTrashed()->get();
        // dd($leads->count());

        $leads_status = Lead::get();
        $leads_sub_status = Lead::get();
        $chunkSize = 100; // Number of leads to process in each iteration
        $change1 = 0;
        $change2 = 0;

        $statuses = [
            'In Progress',
            'Successful',
            'Unsuccessful',
            'New',
            'Not Reachable',
            'Follow Up',
            'Viewing Arranged',
            'Unspecified',
            'Invalid inquiry',
            'Prospect',
        ];

        $leads = Lead::withTrashed()->where('lead_sub_status_id', 8)->update(['lead_status_id' => 3]);
        $leads = Lead::withTrashed()->where('lead_sub_status_id', 1)->update(['lead_status_id' => 5]);
        $leads = Lead::withTrashed()->where('lead_sub_status_id', 6)->update(['lead_status_id' => 6]);
        $leads = Lead::withTrashed()->where('lead_sub_status_id', 7)->update(['lead_status_id' => 7]);
        $leads = Lead::withTrashed()->where('lead_sub_status_id', 7)->update(['lead_status_id' => 7]);
        $leads = Lead::withTrashed()->where('lead_sub_status_id', 2)->update(['lead_status_id' => 9]);
        $leads = Lead::withTrashed()->where('lead_sub_status_id', 21)->update(['lead_status_id' => 10]);
        $leads = Lead::withTrashed()->where('lead_sub_status_id', 23)->update(['lead_status_id' => 11]);
        $leads = Lead::withTrashed()->where('lead_sub_status_id', 3)->update(['lead_status_id' => 12]);
        $leads = Lead::withTrashed()->where('lead_sub_status_id', 4)->update(['lead_status_id' => 1]);
    }

    public function getResponseTime(Request $request)
    {
        $data = Shortlink::where('lead_id', $request->id)->first();
        //  if found then calculate the time between created_at and opted_in
        if ($data) {
            $created_at = $data->created_at;
            $opted_in = $data->opted_in;
            // if opted_in is null then return Not Contacted yet
            if ($opted_in == null) {
                return response()->json(['status' => '1', 'msg' => 'Not Contacted yet', 'data' => null]);
            }

            // $response_time = $created_at->diffInSeconds($opted_in);
            // $response_time = gmdate('H:i:s', $response_time);

            $response_time = $created_at->diff($opted_in);
            $response_time = $response_time->format('%h hr(s) %i min(s) %s sec(s)');

            // dd($response_time, $created_at, $opted_in);
            return response()->json(['status' => '1', 'msg' => 'Response Time Found', 'data' => $response_time]);
        } else {
            return response()->json(['status' => '0', 'msg' => 'Response Time Not Found']);
        }
    }

    public function duplicateLeads(Request $request)
    {
        $lead = Lead::find($request->id);
        if (empty($lead->mobile)) {
            return '';
        }
        $lastSevenDigits = substr($lead->mobile, -7);
        $duplicates = [];
        if (auth()->user()->hasRole(['Super Admin', 'Admin', 'Team Lead', 'Line Manager', 'Sales Director'])) {
            $duplicates = Lead::where('mobile', 'LIKE', '%' . $lastSevenDigits)
                ->where('agent_id', $lead->agent_id)
                ->where('id', '!=', $lead->id)
                ->get();
        } else if (auth()->user()->hasRole(['Agent'])) {
            $duplicates = Lead::where('mobile', 'LIKE', '%' . $lastSevenDigits)
                ->where('agent_id', $lead->agent_id)
                ->where('id', '!=', $lead->id)
                ->get();
        } else if (auth()->user()->hasRole(['Telesales'])) {
            $duplicates = Lead::where('mobile', 'LIKE', '%' . $lastSevenDigits)
                ->where('telesales_id', $lead->telesales_id)
                ->where('id', '!=', $lead->id)
                ->get();
        }
        return view('admin.lead.include.component.duplicate_leads')
            ->with('duplicates', $duplicates);
    }
    public function getLeadTypes(Request $request)
    {
        $lead_types = LeadType::where('status', '1')->get();
        return response()->json(['status' => '1', 'msg' => 'Lead Type Found', 'data' => $lead_types]);
    }

    public function getAgentsBasedOnStatus()
    {
        $agents = User::whereHas('roles', function ($query) {
            $query->where('name', 'Agent');
        })->get();
        return response()->json(['status' => '1', 'msg' => 'Agents Found', 'data' => $agents]);
    }

    public function leadExport()
    {
        return Excel::download(new BitrixLead, 'bitrix_leads.csv');
    }

    public function filterHelper(Request $request)
    {
        $data = [];
        $operators = config('configurations.filters.Lead.' . $request->type . '.operators');

        if ($request->type == 'communities') {
            $data = Community::all();
        }
        if ($request->type == 'sub_communities') {
            $data = SubCommunity::all();
        }

        if ($request->type == 'create_date') {
            $data = [];
            $operators = config('configurations.filters.Lead.create_date.operators');
        }

        if ($request->type == 'status') {
            $data = LeadStatus::where('status', '1')->get();
            $operators = config('configurations.filters.Lead.status.operators');
        }
        if ($request->type == 'source') {
            $data = Source::where('status', '1')->get();
            $operators = config('configurations.filters.Lead.sources.operators');
        }
        if ($request->type == 'medium') {
            $data = Medium::where('status', '1')->get();
            $operators = config('configurations.filters.Lead.medium.operators');
        }
        if ($request->type == 'type') {
            $data = Type::where('status', '1')->get();
            $operators = config('configurations.filters.Lead.type.operators');
        }

        if ($request->type == 'priority') {
            $data = \App\Models\Lead::PRIORITY_SELECT;
            $operators = config('configurations.filters.Lead.priority.operators');
        }
        if ($request->type == 'for') {
            $data = \App\Models\Lead::FOR_SELECT;
            $operators = config('configurations.filters.Lead.for.operators');
        }
        if ($request->type == 'telesales') {
            $data = User::whereHas("roles", function ($q) {
                $q->whereIn("name", ["Telesales"]);
            })->get();
            $operators = config('configurations.filters.Lead.telesales.operators');
        }
        if ($request->type == 'region') {
            $data = Region::where('status', '1')->get();
            $operators = config('configurations.filters.Lead.region.operators');
        }
        if ($request->type == 'property_ref') {
            $data = Property::latest()->limit(10)->get();
            $operators = config('configurations.filters.Lead.property.operators');
        }
        if ($request->type == 'furnished') {
            $data = \App\Models\Lead::FURNISHED_SELECT;
            $operators = config('configurations.filters.Lead.furnished.operators');
        }
        if ($request->type == 'gender') {
            $data = \App\Models\Lead::GENDER_SELECT;
            $operators = config('configurations.filters.Lead.gender.operators');
        }
        if ($request->type == 'lead_category') {
            $data = \App\Models\Lead::LEAD_CATEGORY;
            $operators = config('configurations.filters.Lead.lead_category.operators');
        }

        if ($request->type == 'baths') {
            $data = \App\Models\Bath::all();
            $operators = config('configurations.filters.Lead.baths.operators');
        }
        if ($request->type == 'beds') {
            $data = \App\Models\Bed::all();
            $operators = config('configurations.filters.Lead.beds.operators');
        }

        if ($request->type == 'agents' || $request->type == 'previous_agents' || $request->type == 'updated_by') {
            $operators = config('configurations.filters.Lead.agent.operators');
        }

        if ($request->type == 'managers') {
            $operators = config('configurations.filters.Lead.managers.operators');
        }


        return response()->json(['status' => '1', 'msg' => 'Data Found', 'data' => $data, 'operators' => $operators ?? []]);
    }

    public function exportExcel(Request $request)
    {
        $request->validate([
            'columns' => 'required|array',
            'filters' => 'nullable|array',
        ]);

        return Excel::download(new LeadExport($request->columns, $request), "leads" . time() . ".xlsx");
    }

    public function addQualifiedNote(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'note' => 'required|min:35',
        ]);
        try {
            $lead = Lead::find($request->id);
            $lead->lead_status_id = 8;
            $lead->save();

            $obj = new LeadNote();
            $obj->lead_id = $lead->id;
            $obj->note = $request->note;
            $obj->created_by_id = auth()->id();
            $obj->save();

            $result = array('status' => '1', 'msg' => "Lead Qualified Note added successfully.");
            return json_encode($result);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            return json_encode($result);
        }
    }

    public function transferToPortalLeadPool(Request $request)
    {
        abort_if(Gate::denies("Leads Transfer to Leadpool"), 401);

        $request->validate([
            'id' => 'required',
            'note' => 'required|min:35',
        ]);

        try {
            $lead = Lead::find($request->id);
            if ($lead && $lead->agent_id) {
                $lead->previous_agent_id = $lead->agent_id;
                $lead->agent_id = null;
                $lead->portal_lead = true;
                $lead->portal_lead_transferred_date = now();
                $lead->transfer_to_portal_lead_time = null;
                $lead->taken_from_portal_lead = false;
                $lead->updated_at = now();
                $lead->updated_by_id = auth()->id();
                $lead->save();

                LeadNote::create([
                    "lead_id" => $lead->id,
                    "note" => "Transferred to portal Lead pool => " . $request->note,
                    'status' => 1,
                    "created_by_id" => auth()->id(),
                ]);
            }
            $result = array('status' => '1', 'msg' => "Lead transferred to Portal Lead pool successfully.");
            return json_encode($result);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            return json_encode($result);
        }
    }
}
