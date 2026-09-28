<?php

namespace App\Http\Controllers\Admin\Telesale;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\AjaxController;
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
use App\Models\Medium;
use App\Models\Note;
use App\Models\Property;
use App\Models\Source;
use App\Models\SubCommunity;
use App\Models\Type;
use App\Models\User;
use App\Models\Shortlink;
use App\Traits\CallTrait;
use App\Traits\FiltersTrait;
use Auth;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;
class TelesaleController extends Controller
{
    use CallTrait, FiltersTrait;

    public function index()
    {
        abort_if(Gate::denies("Leads List"), 401);
        $data['types'] = Type::where('is_lead_type', 1)->where('status', true)->orderBy('order', 'asc')->get();
        $data['sources'] = Source::where('is_lead_source', 1)->where('status', true)->orderBy('order', 'asc')->get();
        $data['mediums'] = Medium::where('is_lead_medium', 1)->where('status', true)->orderBy('order', 'asc')->get();
        if (auth()->user()->hasRole(['Admin', 'Super Admin'])) {
            $data['statuses'] = LeadStatus::where('status', true)->orderBy('order', 'asc')->get();

        } else {
            $data['statuses'] = LeadStatus::where('id', '!=', 3)->where('status', true)->orderBy('order', 'asc')->get();

        }
        $data['countries'] = Country::where('status', true)->get();
        $data['properties'] = Property::latest()->limit(10)->get();
        $data['beds'] = Bed::where('status', true)->orderBy('order', 'asc')->get();
        $data['baths'] = Bath::where('status', true)->orderBy('order', 'asc')->get();
        $data['telesales'] = User::whereHas("roles", function ($q) {
            $q->whereIn("name", ["Telesales"]);
        })->get();
        $data['telesalesheads'] = User::whereHas("roles", function ($q) {
            $q->whereIn("name", ["Telesales Head"]);
        })->get();

        $notAdmin = auth()->user()->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }
        $data['agents'] = User::whereHas("roles", function ($q) {
            $q->whereIn("name", ["Agent", "Sales Director", "Team Leader", "Line Manager"]);
        })->when($notAdmin, fn($q) => $q->whereIn("id", $childrenIds))->get();

        // $data['open_sub_statuses'] = LeadSubStatus::where('lead_status_id',1)->where('status', true)->orderBy('order')->get();
        // $data['closed_sub_statuses'] = LeadSubStatus::where('lead_status_id',2)->where('status', true)->orderBy('order')->get();

        if (auth()->user()->hasRole(['Admin', 'Super Admin'])) {
            $data['open_sub_statuses'] = LeadSubStatus::where('lead_status_id', 1)->where('status', true)->orderBy('order')->get();
            $data['closed_sub_statuses'] = LeadSubStatus::where('lead_status_id', 2)->where('status', true)->orderBy('order')->get();
        } else {
            $data['open_sub_statuses'] = LeadSubStatus::where('lead_status_id', 1)->where('id', '!=', 8)->where('status', true)->orderBy('order')->get();
            $data['closed_sub_statuses'] = LeadSubStatus::where('lead_status_id', 2)->where('status', true)->orderBy('order')->get();
        }

        return view('admin.telesales.index')->with($data);
    }

    public function store(Request $request)
    {
        //    dd($request->all());
        $validatedData = $request->validate([
            "agent_id" => "nullable|exists:users,id",
            "for" => "nullable|array",
            "first_name" => "required|string",
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

        try {
            $validatedData["created_by_id"] = auth()->id();
            $validatedData["status"] = 1;
            // $validatedData["lead_status_id"] = 3;
            // $validatedData["lead_sub_status_id"] = 8;
            $validatedData["for"] = is_null($request->for) ? [] : $request->for;
            $lead = Lead::create($validatedData);
            $lead->save();
            event(new \App\Events\LeadCreated ($lead));
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

                $arr = ['attributes' => $dirty,
                    'old' => $flag == 'created' ? [] : $original, 'lead_id' => $leadCriteria->lead_id];
                activity()
                    ->performedOn($leadCriteria)
                    ->causedBy(auth()->user()->id)
                    ->withProperties($arr)
                    ->event($flag)
                    ->log(auth()->user()->first_name . ' has ' . $flag . ' lead criteria');

                return response()->json(['status' => '1', 'msg' => 'Lead criteria ' . $flag . ' successfully']);

            } else {
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }
        } catch (\Exception $exception) {
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
            $leadCriteria->lead_id = $arr['lead_id'];
            $leadCriteria->min_bed_id = $arr['min_bed_id'];
            $leadCriteria->max_bed_id = $arr['max_bed_id'];
            $leadCriteria->min_bath_id = $arr['min_bath_id'];
            $leadCriteria->max_bath_id = $arr['max_bath_id'];
            $leadCriteria->min_price = $arr['min_price'];
            $leadCriteria->max_price = $arr['max_price'];
            $leadCriteria->communities = !empty($arr['sub_community_id']) ? $arr['sub_community_id'] : null;

            $leadCriteria->furnished = $arr['furnished'];
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
                    ->causedBy(auth()->user()->id)
                    ->withProperties($arr)
                    ->event($flag)
                    ->log(auth()->user()->first_name . ' has ' . $flag . ' lead criteria');
                // return true;
                return response()->json(['status' => '1', 'msg' => 'Lead criteria ' . $flag . ' successfully']);

            } else {
                // return false;

                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
            return response()->json($result, 500);
        }
        return json_encode($result);
    }

    public function update(Request $request)
    {
        // dd($request->all());
        //"mobile" => "nullable|numeric|unique:leads,mobile," . $request->id,
        $validatedData = $request->validate([
            "id" => "required|exists:leads,id",
            "agent_id" => "nullable|exists:users,id",
            "for" => "nullable|array",
            "first_name" => "required|string",
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
        try {
            $validatedData["updated_by_id"] = auth()->user()->id;
            $validatedData["for"] = is_null($request->for) ? [] : $request->for;
            $lead = Lead::find($request->id);
            // if ($lead->lead_sub_status_id == 2 && $lead->lead_status_id == 2) {
            if ($lead->lead_status_id == 2) {
                return response()->json(['status' => '1', 'msg' => 'This lead cannot be updated because a contact has already been created from it.', "id" => $lead->id]);
            }
            if ($lead->update($validatedData)) {

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
                $arr['created_by_id'] = auth()->user()->id;
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

                return response()->json(['status' => '1', 'msg' => 'Lead Updated Successfully', "id" => $lead->id]);
            } else {
                return response()->json(['status' => '0', 'msg' => 'Something went wrong!!']);
            }

        } catch (\Exception $exception) {
            //  dd($exception);
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }

    public function dataTable(Request $request)
    {
        // dd($request->all());
        $columns = array(
            0 => 'updated_at',
            1 => 'id',
            2 => 'first_name',
            10 => 'updated_at',
        );
        $superAdmin = auth()->user()->hasRole(['Super Admin']);
        $notAdmin = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        if (auth()->user()->hasRole(['Telesales'])) {
            $query = Lead::query()->where('telesales_id', auth()->id())->whereNull('agent_id');

        } else if (auth()->user()->hasRole(['Telesales Head'])) {
            $query = Lead::query()->where('telesales_head_id', auth()->id())->whereNull('agent_id');
        } else {
            $query = Lead::query()->whereNull('agent_id');
        }

        // dd($query->count());
        $totalData = $query->when($request->boolean("archive") && $superAdmin, function ($q) {
            $q->where(function ($q) {
                $q->whereIn("is_archive", [0, 1])->orWhereIn("status", [0, 1]);
            });
        }, function ($q) {
            $q->where("status", 1)->where('is_archive', 0);
        })->when($notAdmin, fn($q) => $q->whereIn("agent_id", $childrenIds))->count(); // for agent total leads count
        $totalFiltered = $this->applyLeadFilters($query, $request)->when($notAdmin, fn($q) => $q->whereIn("agent_id", $childrenIds))->count();
        $data = $this->applyLeadFilters($query, $request)->orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
            ->offset($request['start'])
            ->limit($request['length'])
            ->with('createdBy', 'leadSource', 'agent', 'leadStatus', 'leadSubStatus', 'leadType')
            ->when($notAdmin, fn($q) => $q->whereIn("agent_id", $childrenIds))
            ->get();
        $json_data = array(
            "draw" => intval($request['draw']),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data,
        );

        echo json_encode($json_data);
    }

    public function getLead(Request $request)
    {
        $contact = Lead::find($request->id);
        $contact->load(['criteria', 'criteria.locations.parentable', 'leadAgents', 'leadSubStatus', 'leadStatus', 'createdBy', 'updatedBy'])->first();
        // dd($contact);
        if (!empty($contact)) {
            return response()->json(['status' => '1', 'msg' => 'Lead Found', 'data' => $contact]);
        } else {
            return response()->json(['status' => '0', 'msg' => 'Contact Not Found']);
        }
    }
    public function delete(Request $request)
    {
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
            }
            if (Lead::whereIn('id', $ids)->update(['agent_id' => $request->agent_id])) {
                $result = array('status' => '1', 'msg' => count($ids) . ' Leads assigned successfully');
            } else {
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }
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
                    $arr = ['attributes' => $dirty,
                        'old' => $original, 'lead_id' => $lead->id];
                    activity()
                        ->performedOn($obj)
                        ->causedBy(auth()->user()->id)
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
                    $arr = ['attributes' => $dirty,
                        'old' => $original, 'lead_id' => $lead_id];
                    activity()
                        ->performedOn($leadAgent)
                        ->causedBy(auth()->user()->id)
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
            $callStared = $this->makeCall($user->email, $lead->mobile); // Uncomment this

            // $callStared = $this->makeCall($agentEmail, $lead->mobile); // Uncomment this
            //$callStared = $this->makeCall("test@axcrm.ae", "+971588159646");// comment this

            if ($callStared == "Make call posted") {
                LeadNote::create([
                    "lead_id" => $lead->id,
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

    public function assignAgents(Request $request)
    {
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
            return response()->json(['status' => '1', 'msg' => 'Agents assigned successfully']);
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
        return response()->json(['data' => $datas]);
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
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            return json_encode($result);
        }
    }

    public function saveReason(Request $request)
    {
        try {
            $lead = Lead::find($request->id);
            // $lead->reason = $request->reason;
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

        // dd($request->all());
        $request->validate([
            'ids' => 'required|array',
            'action' => 'required',
        ]);

        if ($request->action == 'Re-Assign') {

            abort_if(Gate::denies("Leads Assign Agent"), 401);
            if (gettype($request->ids) == 'array' && count($request->ids) > 0) {
                $ids = $request->ids;
                foreach ($ids as $lead) {
                    $leadAgent = Lead::where('id', $lead)->first();
                    $user = User::where('id', $request->id)->first();
                    activity()
                        ->performedOn($leadAgent)
                        ->causedBy(auth()->user()->id)
                        ->withProperties([])
                        ->event('updated')
                        ->log(auth()->user()->first_name . ' has added ' . $user->first_name . ' as Agent ');

                }
                if (Lead::whereIn('id', $ids)->update(['agent_id' => $request->id])) {

                    $result = array('status' => '1', 'msg' => count($ids) . ' Leads assigned successfully');
                } else {
                    $result = array('status' => '0', 'msg' => 'Something went wrong!!');
                }
            }
        } else if ($request->action == 'Telesales Head') {
            abort_if(Gate::denies("Bulk Telesales Head Assignments"), 401);
            if (gettype($request->ids) == 'array' && count($request->ids) > 0) {
                $ids = $request->ids;
                foreach ($ids as $lead) {
                    $leadAgent = Lead::where('id', $lead)->first();
                    $user = User::where('id', $request->id)->first();
                    activity()
                        ->performedOn($leadAgent)
                        ->causedBy(auth()->user()->id)
                        ->withProperties([])
                        ->event('updated')
                        ->log(auth()->user()->first_name . ' has added ' . $user->first_name . ' as Telesales Head.');
                }
                if (Lead::whereIn('id', $ids)->update(['telesales_head_id' => $request->id])) {
                    $result = array('status' => '1', 'msg' => count($ids) . ' Leads assigned successfully');
                } else {
                    $result = array('status' => '0', 'msg' => 'Something went wrong!!');
                }
            }

        } else if ($request->action == 'Telesales') {
            abort_if(Gate::denies("Bulk Telesales Assignments"), 401);
            if (gettype($request->ids) == 'array' && count($request->ids) > 0) {
                $ids = $request->ids;
                foreach ($ids as $lead) {
                    $leadAgent = Lead::where('id', $lead)->first();
                    $user = User::where('id', $request->id)->first();

                    activity()
                        ->performedOn($leadAgent)
                        ->causedBy(auth()->user()->id)
                        ->withProperties([])
                        ->event('updated')
                        ->log(auth()->user()->first_name . ' has added ' . $user->first_name . ' as Telesales.');

                }
                if (Lead::whereIn('id', $ids)->update(['telesales_id' => $request->id])) {
                    $result = array('status' => '1', 'msg' => count($ids) . ' Leads assigned successfully');
                } else {
                    $result = array('status' => '0', 'msg' => 'Something went wrong!!');
                }
            }
        }

        return json_encode($result);
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
        $data = [];

        if ($request->filled('q')) {
            // $data = Property::select("title", "id")
            //             ->where('title', 'LIKE', '%'. $request->get('q'). '%')
            //             ->get()->take(10);

            $data = Property::select("ref_no", "title", "id")
                ->where('title', 'LIKE', '%' . $request->get('q') . '%')
                ->orWhere('ref_no', 'LIKE', '%' . $request->get('q') . '%')
                ->orWhereHas('community', function ($query) use ($request) {
                    $query->where('name', 'LIKE', '%' . $request->get('q') . '%');
                })
                ->orWhereHas('subCommunity', function ($query) use ($request) {
                    $query->where('name', 'LIKE', '%' . $request->get('q') . '%');
                })
                ->orWhereHas('owner', function ($query) use ($request) {
                    $query->where(function ($query) use ($request) {
                        $query->where('first_name', 'LIKE', '%' . $request->get('q') . '%')
                            ->orWhere('last_name', 'LIKE', '%' . $request->get('q') . '%');
                    });
                })->where('publish_status', 'PUBLISHED')
                ->limit(10)
                ->get();

        } else {
            $data = Property::where('publish_status', 'PUBLISHED')->latest()->select("ref_no", "title", "id")->limit(10)->get();
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
                return response()->json(['status' => '1', 'msg' => 'Not Contacted yet', 'data' => 'Not Contacted yet.']);
            }
            $response_time = $created_at->diffInSeconds($opted_in);
            $response_time = gmdate('H:i:s', $response_time);
            return response()->json(['status' => '1', 'msg' => 'Response Time Found', 'data' => $response_time]);
        } else {
            return response()->json(['status' => '0', 'msg' => 'Response Time Not Found']);
        }
    }
}
