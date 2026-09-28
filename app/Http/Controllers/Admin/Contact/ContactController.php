<?php

namespace App\Http\Controllers\Admin\Contact;

use App\Http\Controllers\AjaxController;
use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\ContactAgent;
use App\Models\ContactNote;
use App\Models\ContactStatus;
use App\Models\Country;
use App\Models\Note;
use App\Models\Source;
use App\Models\Type;
use App\Models\User;
use App\Traits\CallTrait;
use App\Traits\FiltersTrait;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    use FiltersTrait, CallTrait;

    public function index()
    {
        abort_if(Gate::denies("Contacts List"), 401);

        $data['types'] = Type::where('is_contact_type', 1)->where('status', true)->get();
        $data['sources'] = Source::where('is_contact_source', 1)->where('status', true)->get();
        $data['statuses'] = ContactStatus::where('status', true)->get();
        $data['countries'] = Country::where('status', true)->get();
        $notAdmin = auth()->user()->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }
        $data['agents'] = User::where('status', true)->whereHas("roles", function ($q) {
            $q->whereIn("name", ["Agent", "Team Leader", "Head of Secondary", "Sales Director", "Line Manager"]);
        })->when($notAdmin, fn($q) => $q->whereIn("id", $childrenIds))->get();
        return view('admin.contact.index')->with($data);
    }

    public function store(Request $request)
    {
        ajaxOnly($request);
        abort_if(!auth()->user()->hasPermissionTo('Contacts Create'), 403, 'You do not have permission.');
        // dd($request->all());
        $validatedData = $request->validate([
            "agent_id" => "required|exists:users,id",
            "first_name" => "required|string",
            "last_name" => "required|string",
            "mobile" => "required|min:8|unique:contacts,mobile",
            "whatsapp" => "nullable",
            "address" => "nullable|string",
            "email" => "nullable|email:rfc,dns|unique:contacts",
            "gender" => "nullable",
            "dob" => "nullable|date",
            "follow_up_date" => "nullable|date",
            "nationality_id" => "nullable|exists:countries,id",
            "country_id" => "nullable|exists:countries,id",
            "passport" => "nullable|string",
            "passport_expiry" => "nullable|date",
            "phone" => "nullable",
            "company" => "nullable|string",
            "website" => "nullable|string",
            "contact_types" => "nullable|array",
            "contact_types.*" => "nullable|exists:types,id",
            "contact_source_id" => "required|exists:sources,id",
            "contact_status_id" => "nullable|exists:contact_statuses,id",
        ]);

        if (checkBlocklist($request->mobile, Contact::class, $request)) {
            $result = array('status' => '0', 'msg' => 'This number is in Blacklist. Please do not contact with this number.', 'error' => 'This number is in Blacklist. Please do not contact with this number.');
            return json_encode($result);
        }

        try {
            $validatedData["created_by_id"] = auth()->id();
            $validatedData['dob'] = isset($validatedData['dob']) ? date('Y-m-d', strtotime($validatedData['dob'])) : null;
            $validatedData['passport_expiry'] = isset($validatedData['passport_expiry']) ? date('Y-m-d', strtotime($validatedData['passport_expiry'])) : null;
            $contact = Contact::create($validatedData);

            if ($request->phone) {
                $contact->phone = validatePhoneNumber($request->phone);
            }
            if ($request->mobile) {
                $contact->mobile = validatePhoneNumber($request->mobile);
            }
            $contact->save();

            // if($contact->mobile && $contact->agent_id && $contact->id){

            //    $callLogsUpdated = CallLog::whereRaw("REPLACE(client_number, ' ', '') LIKE ?", ['%' . substr($contact->mobile, -8) . '%'])
            //             ->where('user_id', $contact->agent_id)
            //             ->whereNull('contact_id')
            //             ->update(['contact_id' => $contact->id]);
            // }

            $contact->contactTypes()->sync($request->contact_types ?? [3]);
            return response()->json(['status' => '1', 'msg' => 'Contact Added Successfully', "id" => $contact->id]);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage(), 'line' => $exception->getLine());

            return response()->json($result, 500);
        }
    }
    public function update(Request $request)
    {
        ajaxOnly($request);
        abort_if(!auth()->user()->hasPermissionTo('Contacts Edit'), 403, 'You do not have permission.');

        $validatedData = $request->validate([
            "id" => "required|exists:contacts,id",
            "agent_id" => "required|exists:users,id",
            "first_name" => "required|string",
            "last_name" => "required|string",
            "mobile" => "required|min:8|unique:contacts,mobile," . $request->id . ",id,deleted_at,NULL",
            "whatsapp" => "nullable",
            "address" => "nullable|string",
            "email" => "nullable|email:rfc,dns|unique:contacts,email," . $request->id . ",id,deleted_at,NULL",
            "gender" => "nullable",
            "dob" => "nullable|date",
            "follow_up_date" => "nullable|date",
            "nationality_id" => "nullable|exists:countries,id",
            "passport" => "nullable|string",
            "passport_expiry" => "nullable|date",
            "phone" => "nullable",
            "company" => "nullable|string",
            "website" => "nullable|string",
            "contact_types" => "nullable|array",
            "contact_types.*" => "nullable|exists:types,id",
            "contact_source_id" => "required|exists:sources,id",
            "contact_status_id" => "nullable|exists:contact_statuses,id",
        ]);

        if (checkBlocklist($request->mobile, Contact::class, $request)) {
            $result = array('status' => '0', 'msg' => 'This number is in Blacklist. Please do not contact with this number.', 'error' => 'This number is in Blacklist. Please do not contact with this number.');
            return json_encode($result);
        }

        try {
//            $mobile = mobileNumberFixer($validatedData["mobile"]);
//            $country_id = $validatedData['nationality_id'] ?? '';
//            $country_code  = $country_id !='' ? Country::where('id', $validatedData['nationality_id'])->pluck('code')->first() : '';
//            if(checkNumberValidity($mobile,$country_code)){
//                $validatedData["mobile"] = $mobile;
//            }else{
//                return response()->json(['status' => '0', 'msg' => 'Invalid Mobile Number']);
//            }
            $validatedData["updated_by_id"] = auth()->id();
            $validatedData['dob'] = $validatedData['dob'] ? date('Y-m-d', strtotime($validatedData['dob'])) : null;
            $validatedData['passport_expiry'] = $validatedData['passport_expiry'] ? date('Y-m-d', strtotime($validatedData['passport_expiry'])) : null;
            $contact = Contact::find($request->id);
            $notAdmin = auth()->user()->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent", "Super Admin", "Admin - Abu Dhabi"]);
            $childrenIds = [];

            if ($notAdmin) {

                $childrenIds = getUserChildrenIds(auth()->id());
                if ($contact->agent_id && !in_array($contact->agent_id, $childrenIds)) {
                    unset($validatedData['agent_id']);
                    $contact->contactTypes()->syncWithoutDetaching($request->contact_types ?? []);

                } else {
                    $contact->contactTypes()->sync($request->contact_types ?? []);
                }
            }else{
                $contact->contactTypes()->sync($request->contact_types ?? []);
            }
            $contact->update($validatedData);
            if ($request->phone) {
                $contact->phone = validatePhoneNumber($request->phone);

            }
            if ($request->mobile) {
                $contact->mobile = validatePhoneNumber($request->mobile);
            }

            $contact->save();
            return response()->json(['status' => '1', 'msg' => 'Contact Updated Successfully', "id" => $contact->id]);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
            return response()->json($result, 500);
        }
        return json_encode($result);
    }

    public function dataTable(Request $request)
    {
        ajaxOnly($request);
        abort_if(!auth()->user()->hasPermissionTo('Contacts List'), 403, 'You do not have permission.');
        $columns = array(
            0 => 'updated_at',
            1 => 'id',
            2 => 'first_name',
            3 => null,
            4 => null,
            5 => null,
            6 => null,
            7 => null,
            8 => 'updated_at',

        );

        $notAdmin = auth()->user()->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }
        // $searchKeyWord = htmlspecialchars($request['search']['value']);
        $q = Contact::query();
        $totalData = $this->applyContactFilters($q, $request)
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->where(function ($q) use ($childrenIds) {
                    $q->whereIn("agent_id", $childrenIds)->orWhereHas('contactAgents', function ($q) use ($childrenIds) {
                        $q->whereIn('user_id', $childrenIds);
                    });
                });
            })
            ->when(auth()->user()->hasRole("Admin - Abu Dhabi"), function ($q) {
                $q->where('region_id', 2);
            })
            ->count();
        $totalFiltered = $totalData;
        $query = Contact::query();
        $data = $this->applyContactFilters($query, $request)->orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
            ->offset($request['start'])
            ->limit($request['length'])
//            ->orWhereHas('agents', function($q){
//                $q->where('user_id', auth()->user()->id);
//            })
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->where(function ($q) use ($childrenIds) {
                    $q->whereIn("agent_id", $childrenIds)->orWhereHas('contactAgents', function ($q) use ($childrenIds) {
                        $q->whereIn('user_id', $childrenIds);
                    });
                });
            })
            ->when(auth()->user()->hasRole("Admin - Abu Dhabi"), function ($q) {
                $q->where('region_id', 2);
            })
            ->with('created_by', 'updated_by', 'contactTypes')
            ->get();
        $json_data = array(
            "draw" => intval($request['draw']),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data,
        );

        echo json_encode($json_data);
    }
    public function updateStatus(Request $request)
    {
        ajaxOnly($request);
        abort_if(!auth()->user()->hasPermissionTo('Contacts Edit'), 403, 'You do not have permission.');
        $request->validate([
            'id' => 'required|exists:contacts',
            'action' => 'required',
        ]);

        $status = Contact::find($request->id);
        $status->status = $request->boolean('action');
        $status->updated_by_id = auth()->user()->id;
        if ($status->save()) {
            $result = array('status' => '1', 'msg' => 'Status updated successfully');
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }
    public function delete(Request $request)
    {
        ajaxOnly($request);
        abort_if(!auth()->user()->hasPermissionTo('Contacts Delete'), 403, 'You do not have permission.');
        $request->validate([
            'id' => 'required|exists:contacts',
        ]);

        $status = Contact::find($request->id);
        if ($status->delete()) {
            $result = array('status' => '1', 'msg' => 'Contact deleted successfully');
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }
    public function getContact(Request $request)
    {
        ajaxOnly($request);
        abort_if(!auth()->user()->hasPermissionTo('Contacts View'), 403, 'You do not have permission.');
        $request->validate([
            "id" => "required|exists:contacts",
        ]);
        $notAdmin = auth()->user()->hasRole(["Head of Secondary", "Sales Director", "Team Leader", "Line Manager", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }
        $contact = Contact::where('id', $request->id)
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->where(function ($q) use ($childrenIds) {
                    $q->whereIn('agent_id', $childrenIds)->orWhereHas('contactAgents', function ($q) use ($childrenIds) {
                        $q->whereIn('user_id', $childrenIds);
                    });

                });
            })
            ->first();
        if (!empty($contact)) {
            $contact->load("contactTypes", "contactAgents");
            return response()->json(['status' => '1', 'msg' => 'Contact Found', 'data' => $contact]);
        } else {
            return response()->json(['status' => '0', 'msg' => 'Contact Not Found'], 422);
        }

    }
    public function deleteMultiple(Request $request)
    {
        ajaxOnly($request);
        abort_if(!auth()->user()->hasPermissionTo('Bulk Delete Contacts'), 403, 'You do not have permission.');
        $request->validate([
            'ids' => 'required|array',

        ]);
        if (gettype($request->ids) == 'array' && count($request->ids) > 0) {
            $ids = $request->ids;
            if (Contact::whereIn('id', $ids)->delete()) {
                $result = array('status' => '1', 'msg' => count($ids) . ' contact deleted successfully');
            } else {
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }
        }

        return json_encode($result);
    }

    public function addNote(Request $request)
    {

        $request->validate([
            'contact_id' => 'required|exists:contacts,id',
            'note' => 'required|string',
        ]);

        try {
            ContactNote::create([
                "contact_id" => $request->contact_id,
                "note" => $request->note,
                "created_by_id" => auth()->user()->id,
            ]);
            $result = array('status' => '1', 'msg' => "Contact note added successfully.");
            return json_encode($result);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            return json_encode($result);
        }
    }

    public function dataTableForProfileView(Request $request)
    {
        ajaxOnly($request);
        $columns = array(
            0 => 'id',
            1 => 'id',
            2 => 'first_name',

        );

        $searchKeyWord = htmlspecialchars($request['search']['value']);
        $totalData = Contact::where("agent_id", $request->agent_id)->count();
        $totalFiltered = $totalData;

        if (!empty($searchKeyWord)) {
            $data = Contact::where(function ($q) use ($searchKeyWord) {
                $q->where('first_name', 'LIKE', '%' . $searchKeyWord . '%')
                    ->orWhere('last_name', 'LIKE', '%' . $searchKeyWord . '%')
                    ->orWhere('email', 'LIKE', '%' . $searchKeyWord . '%')
                    ->orWhere('mobile', 'LIKE', '%' . $searchKeyWord . '%');
            })->where("agent_id", $request->agent_id)->with('created_by', 'updated_by')->get();
        } else {
            $data = Contact::orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
                ->offset($request['start'])
                ->limit($request['length'])
                ->where("agent_id", $request->agent_id)
                ->with('created_by', 'updated_by')
                ->get();
        }

        $json_data = array(
            "draw" => intval($request['draw']),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data,
        );

        echo json_encode($json_data);
    }

    public function addAgent(Request $request)
    {
        ajaxOnly($request);
        abort_if(!auth()->user()->hasPermissionTo('Contacts Additional Agents Add'), 403, 'You do not have permission.');
        $request->validate([
            'contact_id' => 'required|exists:contacts,id',
            'agent_id' => 'required|exists:users,id',
        ]);

        try {
            if (ContactAgent::where('contact_id', $request->contact_id)->where('user_id', $request->agent_id)->exists() || Contact::where('id', $request->contact_id)->where('agent_id', $request->agent_id)->exists()) {
                return response()->json(['status' => '0', 'msg' => 'Agent already added']);
            } else {
                $obj = new ContactAgent();
                $obj->contact_id = $request->contact_id;
                $obj->user_id = $request->agent_id;
                $obj->created_by_id = auth()->id();
                if ($obj->save()) {
                    $obj->load("created_by");
                    $contact = Contact::where('id', $request->contact_id)->first();
                    $agent = User::where('id', $request->agent_id)->first();
                    activity()
                        ->performedOn($contact)
                        ->causedBy(auth()->user()->id)
                        ->withProperties([])
                        ->event('updated')
                        ->log(auth()->user()->first_name . ' has added ' . $agent->first_name . ' as additional agent');
                    return response()->json(['status' => '1', 'msg' => 'Agent added successfully', "contact_agent" => $obj, "agent" => User::where('id', $obj->user_id)->first()]);
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
        ajaxOnly($request);

        $request->validate([
            'contact_id' => 'required|exists:contacts,id',
        ]);

        try {
            $agents = ContactAgent::where('contact_id', $request->contact_id)->with('agent', 'created_by')->get();
            return response()->json(['status' => '1', 'msg' => 'Agents found', "agents" => $agents]);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
            return json_encode($result);
        }
    }

    public function removeAgent(Request $request)
    {
        ajaxOnly($request);
        abort_if(!auth()->user()->hasPermissionTo('Contacts Additional Agents Remove'), 403, 'You do not have permission.');
        $request->validate([
            'id' => 'required|exists:contact_agents,id',
        ]);

        try {
            if (ContactAgent::where('id', $request->id)->exists()) {
                if (ContactAgent::where('id', $request->id)->delete()) {
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

    public function startCall(Request $request)
    {

        $request->validate([
            "contact_id" => "required|exists:contacts,id",
            'dailer_id' => 'required|exists:users,id',
        ]);

        try {
            $contact = Contact::find($request->contact_id);
            if (empty($contact->mobile)) {
                $result = array('status' => '0', 'msg' => 'Please update mobile number.');
                return json_encode($result);
            }
            // changed functionality from agent to dailer (auth user id)
            // $agentEmail = $contact->agent->email;
            // if (empty($agentEmail)) {
            //     $result = array('status' => '0', 'msg' => 'Please update agent\'s email.');
            //     return json_encode($result);
            // }

            $user = User::where('id', $request->dailer_id)->first();
            $pilot_testers = config('pilot_testing.call');

            if (checkBlocklist($contact->mobile, Contact::class, null)) {
                $result = array('status' => '0', 'msg' => 'This number is in Blacklist. Please do not contact with this number.', 'error' => 'This number is in Blacklist. Please do not contact with this number.');
                return json_encode($result);
            }
            $full_name = $contact->first_name ?? 'Null' . ' ' . $lead->contact ?? 'Null';
            $callStared = $this->dialANumber($user->email, $contact->mobile, $full_name); // Uncomment this
            if ($callStared == "error") {
                $callStared = $this->makeCall($user->email, $contact->mobile); // Uncomment this
            }

            // if(in_array($user->id, $pilot_testers)){
            //     $full_name = $contact->first_name??'Null'.' '.$contact->last_name??'Null';
            //     Log::channel('mobileApi')->info('PILOT TESTER: Agent: '.$user->email.' Contact: '.$contact->mobile.' Full Name: '.$full_name.' Call Started');
            //     $callStared = $this->dialANumber($user->email, $contact->mobile, $full_name); // Uncomment this
            // }else{
            //     // dd($user->email, $contact->mobile);
            //     $callStared = $this->makeCall($user->email, $contact->mobile); // Uncomment this
            // }

            // $callStared = $this->makeCall($agentEmail, $contact->mobile);//Old Code Uncomment this
            // $callStared = $this->makeCall("hello@hishmat.com", "+971569101609"); // comment this
            // dd($callStared);
            if ($callStared) {
                ContactNote::create([
                    "contact_id" => $contact->id,
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
        ajaxOnly($request);
        abort_if(!auth()->user()->hasPermissionTo('Contacts Additional Agents Add'), 403, 'You do not have permission.');
        // $string = implode(',', array_map(function ($value) {
        //     if ($value['name'] != 'contact_type_id' and $value['name'] != 'agent_id' and $value['name'] != 'agent_id') {
        //         $first_name = $value['value'];
        //         $name = $value['name'];
        //         $val = $value['value'];
        //         return "'$name' => '$val'";
        //     }
        // }, $request->formData));

        $request->validate([
            'id' => 'required|exists:contacts,id',
            'agents' => 'nullable|array',
            'add_new' => "nullable",
            'agents.*' => 'nullable|exists:users,id',
            'formData' => 'nullable',
        ]);
        //dd($request->formData);
        try {
            $contact = Contact::find($request->id);
            $agents = $request->agents ?? [];
            $single_agent = $request->agents ?? null;
            $agents = array_combine($agents, array_fill(0, count($agents), ["created_by_id" => auth()->id(), "created_at" => now()]));
            if ($request->add_new) {
                $agent_find = User::where('id', $single_agent[0])->first();
                $string = auth()->user()->first_name . ' ' . auth()->user()->last_name . ' added this owner details for ' . $agent_find->first_name . ' ' . $agent_find->last_name . '.';
                if ($request->formData) {
                    //$agent_find = null;
                    $first_name = '';
                    $last_name = '';
                    $email = '';
                    $string = "Contact details for agent " . $agent_find->first_name . ' ' . $agent_find->last_name . " has been updated with following details.";
                    foreach ($request->formData as $key => $value) {

                        if ($value['value'] && !($value['name'] == 'contact_type_id' || $value['name'] == 'agent_id' || $value['name'] == 'full_mobile')) {
                            $string .= ucfirst(str_replace("_", " ", $value['name']));
                            $string .= ": " . $value['value'] . "<br>";
                        }
                    }

                }
                $contact->agents()->syncWithoutDetaching($agents);
                ContactNote::create([
                    "contact_id" => $request->id,
                    "note" => $string,
                    "created_by_id" => auth()->user()->id,
                ]);
            } else {
                $contact->agents()->sync($agents);
            }
            return response()->json(['status' => '1', 'msg' => 'Agents assigned successfully', 'owner' => $contact]);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
            return json_encode($result);
        }
    }
    public function bulkAssignToAgent(Request $request)
    {
        ajaxOnly($request);
        abort_if(!auth()->user()->hasPermissionTo('Contacts Additional Agents Bulk Assign'), 403, 'You do not have permission.');
        $request->validate([
            'ids' => 'required|array',
            'agent_id' => 'required|integer',
            'type' => 'required|string',

        ]);
        try {
            if (gettype($request->ids) == 'array' && count($request->ids) > 0) {
                $ids = $request->ids;

                foreach ($ids as $contact) {
                    $contactAgent = Contact::where('id', $contact)->with('agent')->first();
                    $user = User::where('id', $request->agent_id)->first();
                    $contactNotes = new Request();
                    if ($contactAgent->agent) {
                        $msg = "Contact has been transferred from " . $contactAgent->agent->name . " to " . $user->name;
                    } else {
                        $msg = "Contact has been assigned to " . $user->name;
                    }

                    $contactNotes->replace(['contact_id' => $contact, "note" => $msg]);
                    // info('hello');
                    $this->addNote($contactNotes);

                    if ($request->type == 'additional') {

                        if (ContactAgent::where('contact_id', $contact)->where('user_id', $request->agent_id)->doesntExist() && Contact::where('agent_id', $request->agent_id)->where('id', $contact)->doesntExist()) {
                            $cAgent = new ContactAgent();
                            $cAgent->contact_id = $contact;
                            $cAgent->user_id = $request->agent_id;
                            $cAgent->created_by_id = auth()->user()->id;
                            $cAgent->save();

                        }
                    }
                }
                if ($request->type == 'primary') {
                    if (Contact::whereIn('id', $ids)->update(['agent_id' => $request->agent_id])) {
                        $result = array('status' => '1', 'msg' => count($ids) . ' Contact assigned successfully');
                    } else {
                        $result = array('status' => '0', 'msg' => 'Something went wrong!!');
                    }
                } else {
                    $result = array('status' => '1', 'msg' => count($ids) . ' Contact assigned successfully');
                }
            }
        } catch (\Exception $e) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $e->getMessage());
        }

        return json_encode($result);
    }
    public function getActivityLog(Request $request)
    {
        ajaxOnly($request);
        $params = new Request();
        $params->replace($request->all());
        $res = (new AjaxController())->activityLog($params);
        if ($res->original['data'] != null) {
            $datas = [];
            foreach ($res->original['data'] as $data) {
                $properties['old'] = isset($data->properties['old']) ? contactColumnsReadable($data?->properties['old']) : [];
                $properties['attributes'] = isset($data->properties['attributes']) ? contactColumnsReadable($data?->properties['attributes']) : [];
                $data->properties = $properties;
                $datas[] = $data;

            }
        }
        return response()->json(['data' => $datas]);
    }

    public function contactNumberFormatter()
    {

        // dd(checkformat('+1111'));
        // dispatch(new NumberFormatterJob());
        // dd('job done');
    }

    public function filterHelper(Request $request)
    {
        $data = [];
        $operators = config('configurations.filters.Contact.' . $request->type . '.operators');

        if ($request->type == 'created_from' || $request->type == 'updated_from' || $request->type == 'dob_search') {
            $data = [];
        }

        if ($request->type == 'source') {
            $data = Source::where('status', '1')->get();
        }
        if ($request->type == 'medium') {
            $data = Medium::where('status', '1')->get();
        }
        if ($request->type == 'type') {
            $data = Type::where('status', '1')->get();
        }

        if ($request->type == 'gender_search') {
            $data = \App\Models\Contact::GENDER_SELECT;
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

    // public function duplicateContacts(Request $request)
    // {
    //     $duplicateContacts = DB::table('contacts')
    //         ->select('email', 'mobile', DB::raw('RIGHT(REPLACE(mobile, " ", ""), 7) as phone_last7'), DB::raw('COUNT(*) as count'))
    //         ->groupBy('email', 'mobile', 'phone_last7') // Group by email, mobile, and last 7 digits of the phone
    //         ->having('count', '>', 1) // Only get records with duplicates
    //         ->get();

    //     return response()->json($duplicateContacts);
    // }

    public function _duplicateContacts(Request $request)
    {
        // Query to find duplicate contacts
        $duplicateContacts = DB::table('contacts')
            ->select('email', 'mobile', DB::raw('RIGHT(REPLACE(mobile, " ", ""), 7) as phone_last7'), DB::raw('COUNT(*) as count'))
            ->groupBy('email', 'mobile', 'phone_last7') // Group by email, mobile, and last 7 digits of the phone
            ->having('count', '>', 1) // Only get records with duplicates
            ->get();

        // Count the total number of infected contacts groups
        $infectedGroupCount = $duplicateContacts->count();

        // Calculate the total count of duplicates (sum of all counts in the result)
        $totalDuplicateCount = $duplicateContacts->sum('count');

        // Return both the list of duplicate contacts, the number of infected groups, and the total count of duplicates
        return response()->json([
            'duplicateContacts' => $duplicateContacts,
            'infectedGroupCount' => $infectedGroupCount, // Number of duplicate groups
            'totalDuplicateCount' => $totalDuplicateCount, // Total duplicate entries
        ]);
    }


    public function duplicateContacts(Request $request)
    {
        // Step 1: Find contacts with duplicate phone numbers (last 7 digits)
        $duplicatePhones = DB::table('contacts')
            ->select(DB::raw('RIGHT(REPLACE(mobile, " ", ""), 7) as phone_last7'))
            ->groupBy('phone_last7')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('phone_last7');

        // Step 2: Find contacts with duplicate emails
        $duplicateEmails = DB::table('contacts')
            ->select('email')
            ->groupBy('email')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('email');

        // Step 3: Get IDs for duplicate phone numbers
        $idsByPhone = DB::table('contacts')
            ->whereIn(DB::raw('RIGHT(REPLACE(mobile, " ", ""), 7)'), $duplicatePhones)
            ->pluck('id','email','mobile');

        // Step 4: Get IDs for duplicate emails
        $idsByEmail = DB::table('contacts')
            ->whereIn('email', $duplicateEmails)
            ->pluck('id','email','mobile');

        // Step 5: Get IDs for contacts that match both duplicates
        $duplicateBoth = DB::table('contacts')
            ->whereIn('email', $duplicateEmails)
            ->whereIn(DB::raw('RIGHT(REPLACE(mobile, " ", ""), 7)'), $duplicatePhones)
            ->pluck('id','email','mobile');

        // Step 6: Return the three arrays
        return response()->json([
            'duplicatePhones' => $idsByPhone,
            'duplicateEmails' => $idsByEmail,
            'duplicateBoth' => $duplicateBoth,
            'totalDuplicatePhones' => $idsByPhone->count(),
            'totalDuplicateEmails' => $idsByEmail->count(),
            'totalDuplicateBoth' => $duplicateBoth->count(),
        ]);
    }


}
