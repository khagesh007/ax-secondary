<?php

namespace App\Http\Controllers\Admin\Calls;

use App\Http\Controllers\Controller;
use App\Models\Bath;
use App\Models\Bed;
use App\Models\CallLog;
use App\Models\Community;
use App\Models\CommunityProperty;
use App\Models\Country;
use App\Models\Lead;
use App\Models\LeadCriteria;
use App\Models\LeadCriteriaLocation;
use App\Models\LeadNote;
use App\Models\LeadStatus;
use App\Models\Source;
use App\Models\SubCommunity;
use App\Models\Type;
use DataTables;
use Gate;
use Illuminate\Http\Request;

class CallLogController extends Controller
{
    public function index()
    {

        abort_if(Gate::denies("Call Logs List"), 401);

        $data['types'] = Type::where('is_lead_type', 1)->where('status', true)->get();
        $data['sources'] = Source::where('is_lead_source', 1)->where('status', true)->get();
        $data['beds'] = Bed::where('status', true)->get();
        $data['baths'] = Bath::where('status', true)->get();
        $data['countries'] = Country::where('status', true)->get();

        if (auth()->user()->hasRole(['Admin', 'Super Admin'])) {
            $data['statuses'] = LeadStatus::where('status', true)->get();

        } else {
            $data['statuses'] = LeadStatus::whereNotIn('id', [3, 4])->where('status', true)->get();

        }
        // $query = CallLog::select(
        //             'id',
        //             'contact_id',
        //             'client_number',
        //             'call_type',
        //             'user_id',
        //             'call_recording_url',
        //             'call_started_at',
        //             'created_at'
        //         )->with([
        //             'leads' => function ($query) {
        //                 $query->select('first_name', 'last_name')
        //                     ->where('status', 1)
        //                     ->where('is_archive', 0)
        //                     ->limit(1);
        //             },
        //             'contact' => function ($query) {
        //                 $query->select('first_name', 'last_name')
        //                     ->where('status', 1);
        //             },
        //             'user' => function ($query) {
        //                 $query->select('id','name');
        //             }
        //         ])->where(function ($query) {
        //             $query->whereHas('leads', function ($q) {
        //                 $q->where('status', 1)->where('is_archive', 0)->limit(1);
        //             })->orWhereHas('contact', function ($q) {
        //                 $q->where('status', 1);
        //             });
        //         })->when($notAdmin, function ($query) use ($childrenIds) {
        //             $query->whereIn('user_id', $childrenIds);
        //         })->limit(10);
        return view('admin.calls.index')->with($data);
    }

    public function datatable(Request $request)
    {
        $notAdmin = auth()->user()->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        // $query = CallLog::select('call_logs.*')->with('leads', 'contact', 'user')
        // ->whereHas('leads',function($q){
        //     $q->where('status', 1)->where('is_archive', 0);
        // })
        // ->orWhereHas('contact',function($q){
        //     $q->where('status', 1);
        // })
        // ->when($notAdmin, function ($q) use ($childrenIds) {
        //     $q->whereIn('user_id', $childrenIds);
        // })->limit(10);

        // $query = CallLog::select(
        //                     'id',
        //                     'contact_id',
        //                     'client_number',
        //                     'call_type',
        //                     'user_id',
        //                     'call_recording_url',
        //                     'call_started_at',
        //                     'created_at'
        //                 )
        // ->with('leads', 'contact', 'user')
        // ->whereHas('leads',function($q){
        //     $q->where('status', 1)->where('is_archive', 0)->limit(1);
        // })
        // ->orWhereHas('contact',function($q){
        //     $q->where('status', 1);
        // })
        // ->when($notAdmin, function ($q) use ($childrenIds) {
        //     $q->whereIn('user_id', $childrenIds);
        // });
        $query = CallLog::select(
            'id',
            'contact_id',
            'client_number',
            'call_type',
            'user_id',
            'call_recording_url',
            'call_started_at',
            'call_duration',
            'created_at'
        )->with(['leads:id,first_name,last_name', 'contact:id,first_name,last_name', 'user'])
            ->where(function ($query) {
                $query->whereHas('leads', function ($q) {
                    $q->where('status', 1)->where('is_archive', 0)->limit(1);
                })->orWhereHas('contact', function ($q) {
                    $q->where('status', 1);
                })->whereHas("user", function ($q) {
                    $q->where("status", 1);
                })->orderBy("id", "desc")
                ;
            })->when($notAdmin, function ($query) use ($childrenIds) {
            $query->whereIn('user_id', $childrenIds);
        });

        // dd($query->toSql());

        // $query = CallLog::select('call_logs.*')->whereNotNull('contact_id')
        // ->whereHas('lead', function ($q) {
        //     $q->where('is_archive', 0);
        // })
        // ->with('leads', 'contact', 'user')
        // ->when($notAdmin, function ($q) use ($childrenIds) {
        //     $q->whereIn('user_id', $childrenIds);
        // })
        //dd($query->get());
        return DataTables::of($query)
            ->make(true);
    }

    public function getLog(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:call_logs,id',
        ]);
        try {
            $notAdmin = auth()->user()->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent", "Telesales", "Telesales Head"]);
            $childrenIds = getUserChildrenIds(auth()->id());

            $cat = CallLog::where('id', $request->id)
                ->when($notAdmin, function ($q) use ($childrenIds) {
                    $q->whereIn('user_id', $childrenIds);
                })->with(['leads', 'contact', 'user', 'leads.criteria', 'leads.property', 'leads.criteria.locations.parentable'])->first();
            // dd($cat->lead, $cat->id,);
            $result = array('status' => '1', 'msg' => 'Call Log details fetched successfully', 'data' => $cat);
        } catch (\Exception $exception) {

            // dd($exception->getMessage());
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function updateLead(Request $request)
    {
        //  dd($request->all());
        $request->validate([
            'lead_id' => 'required',
            'first_name' => 'required',
            'last_name' => 'nullable',
            'email' => 'nullable|email|unique:leads,email,' . $request->id,
            'phone' => 'nullable|numeric|unique:leads,phone,' . $request->id,
            'type_id' => 'required',
            'source_id' => 'required',
            'lead_status_id' => 'required',
        ]);

        if (checkBlocklist($request->mobile, Lead::class, $request)) {
            $result = array('status' => '0', 'msg' => 'This number is in Blacklist. Please do not contact with this number.', 'error' => 'This number is in Blacklist. Please do not contact with this number.');
            return json_encode($result);
        }

        try {
            $lead = Lead::where('id', $request->lead_id)->first();
            if ($lead) {
                $lead->first_name = $request->first_name;
                $lead->last_name = $request->last_name;
                $lead->email = $request->email;
                $lead->phone = $request->phone;

                if ($request->full_phone) {
                    $lead->phone = $request->full_phone;
                }

                $lead->for = $request->for;
                $lead->type_id = $request->type_id;
                $lead->source_id = $request->source_id;
                $lead->lead_status_id = $request->lead_status_id;
                // $lead->lead_sub_status_id = $request->lead_sub_status_id;
                // if ($request->property_id) {
                $lead->property_id = $request->property_id;
                // }
                $lead->save();

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

                // dd($lead);
                $result = array('status' => '1', 'msg' => 'Lead updated successfully');
                return json_encode($result);

            } else {
                $result = array('status' => '0', 'msg' => 'Lead not found');
                return json_encode($result);
            }

        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            return json_encode($result);

        }
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
            $leadCriteria->view = $validatedData["view"] = json_encode($arr['view']);
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

    public function archive(Request $request)
    {
        // dd($request->all());
        abort_if(Gate::denies("Leads Archive"), 401);
        $request->validate([
            'ids' => 'required',
            'archive_note' => 'required',
        ]);

        try {
            foreach ($request->ids as $id) {
                $call_log = CallLog::find($id);

                if ($call_log) {
                    foreach ($call_log->leads as $lead) {
                        $lead = Lead::find($lead->id);
                        if ($lead) {
                            $lead->is_archive = 1;
                            $lead->save();
                            LeadNote::create([
                                "lead_id" => $lead->id,
                                "note" => "Archive: " . $request->archive_note,
                                'status' => 1,
                                "created_by_id" => auth()->id(),
                            ]);}
                    }

                }

            }
            $result = array('status' => '1', 'msg' => "Lead(s) archived successfully.");
            return json_encode($result);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            return json_encode($result);
        }
    }

    public function multidelete(Request $request)
    {
        abort_if(Gate::denies("Call Logs Delete"), 401);
        $request->validate([
            'ids' => 'required|array',
        ]);
        if (gettype($request->ids) == 'array' && count($request->ids) > 0) {
            $ids = $request->ids;
            if (CallLog::whereIn('id', $ids)->delete()) {
                $result = array('status' => '1', 'msg' => count($ids) . ' Call Log deleted successfully');
            } else {
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }
        }
        return json_encode($result);
    }
}
