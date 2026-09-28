<?php

namespace App\Http\Controllers\Admin\User;

use App\Events\UserCreated;
use App\Events\UserUpdated;
use App\Exports\UserExport;
use App\Exports\UsersAuthToken;
use App\Exports\UsersDeviceExport;
use App\Http\Controllers\AjaxController;
use App\Http\Controllers\Api\TelegramController;
use App\Http\Controllers\Controller;
use App\Mail\SendPasswordMail;
use App\Models\AppLog;
use App\Models\Community;
use App\Models\Contact;
use App\Models\ContactAgent;
use App\Models\ContactNote;
use App\Models\CoordinateRecording;
use App\Models\Country;
use App\Models\Group;
use App\Models\Lead;
use App\Models\LeadAgent;
use App\Models\LeadNote;
use App\Models\Otp;
use App\Models\Portal;
use App\Models\PortalUser;
use App\Models\Property;
use App\Models\PropertyNote;
use App\Models\Region;
use App\Models\SubCommunity;
use App\Models\SubCommunityUser;
use App\Models\TraccarRestrictedCoordinate;
use App\Models\User;
use App\Models\UserAppLogin;
use App\Models\UserDevice;
use App\Models\UserLocation;
use App\Models\UserMeta;
use App\Traits\FileTrait;
use App\Traits\FiltersTrait;
use App\Traits\FirebaseTrait;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Role;

use Laravel\Sanctum\PersonalAccessToken;

class UserController extends Controller
{
    use FileTrait, FiltersTrait, FirebaseTrait;

    public function index()
    {

        // count no of user has role agent
        // $agentCount = User::where('status',1)->whereHas("roles", fn($q) => $q->where("name", "Agent"))->count();
        // $users = User::where('status', 1)
        // ->doesntHave('devices')
        // ->whereHas('roles', function ($query) {
        //     $query->where('name', 'Agent');
        // })
        // ->select('id', 'first_name', 'last_name', 'email')
        // ->get();

        // dd($users->count());

        abort_if(Gate::denies("Users List"), 401);
        if (auth()->user()->hasRole("Super Admin")) {
            $data['roles'] = Role::all();
        } else {
            $data['roles'] = Role::where("name", "<>", "Super Admin")->get();
        }
        $data['portals'] = Portal::where("status", true)->get(["id", "name", "quota"]);
        $data['communities'] = Community::where("status", true)->get(["id", "name"]);
        $data['countries'] = Country::where('status', true)->get();
        $data['regions'] = Region::where('status', true)->get();

        $notAdmin = auth()->user()->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent", "Primary Agent", "Primary Sales Director", "Primary Sales Manager","CFO","Finance Manager","Admin Head", "Back office Temp"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        $userGroups = [];
        $allUsers = User::whereHas("roles", fn($q) => $q->where("name", "<>", "Agent"))->with("roles:id,name")->when($notAdmin, fn($q) => $q->whereIn("parent_id", $childrenIds))->get(["id", "name", "email"]);
        foreach ($allUsers as $user) {
            if (!empty($user->roles[0]->name)) {
                $userGroups[$user->roles[0]->name][] = [
                    "id" => $user->id,
                    "name" => $user->name,
                ];
            }
        }
        $userAgents = [];

        $allAgents = User::whereHas("roles", function ($q) {
            $q->whereIn("name", ["Sales Director", "Line Manager", "Team Leader", "Head of Secondary", "CEO", "Admin", "Primary Sales Director", "Primary Sales Manager","Primary Agent","Head of Primary",
                "CFO","Finance Manager","Admin Head", "Head of Owner Relations", "Owner Relations","AX Design Team","Back office Temp","Telesales Head", "Back office Temp"]);

            // Check if the current user has the "Super Admin" role
            if (auth()->user()->hasRole("Super Admin")) {
                $q->orWhere("name", "Super Admin");
            }

        })
            ->when(auth()->user()->hasRole("Admin - Abu Dhabi"), function ($q) {
                return $q->where("region_id", 2);
            })
            ->with("roles:id,name")
            ->get(["id", "name", "email", "zone_id","status","thumbnail"]);

        foreach ($allAgents as $agent) {
            if (!empty($agent->roles[0]->name)) {
                $userAgents[$agent->roles[0]->name][] = [
                    "id" => $agent->id,
                    "name" => $agent->name,
                    "zone_id" => $agent->zone_id,
                    "status" => $agent->status,
                    "thumbnail" => $agent->thumb_url,
                ];
            }
        }
        $data["userGroups"] = $userGroups;
        $data['userAgents'] = $userAgents;
        $data['agents'] = User::whereHas("roles", fn($q) => $q->whereIn("name", ["Agent"]))->get(["id", "name", "email"]);
        $users = User::where('status', true)->get();

        // $uniqueRoles = $users->flatMap(function ($user) {
        //     return array_map('trim', explode(',', $user->bitrix_role));
        // })->unique();

        $uniqueRoles = [];

        foreach ($users as $user) {
            $roles = explode(',', $user->bitrix_role);
            $roles = array_map('trim', $roles);
            $roles = array_filter($roles);
            $uniqueRoles = array_merge($uniqueRoles, $roles);
        }
        $uniqueRoles = array_unique($uniqueRoles);

        $data['bitrix_role'] = $uniqueRoles;
        $data['groups'] = Group::whereNull("parent_id")->get(["id", "name"]);

        $data['telelog_entities'] = getTelelogEntities();

        return view('admin.user.index')->with($data);
    }

    public function treeView()
    {
        abort_if(Gate::denies("Users Tree View"), 401);
        $portals = Portal::where("status", true)->get(["id", "name", "quota"]);
        $communities = Community::where("status", true)->get(["id", "name"]);
        return view('admin.user.treeView', compact("portals", "communities"));
    }

    public function view(User $user)
    {
        $notAdmin = auth()->user()->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);
        $childrenIds = getUserChildrenIds($user->id);
        abort_if(Gate::denies("Users View") || ($notAdmin && !in_array($user->parent_id, $childrenIds)), 401);

        $user->load(["roles"]);
        $data["user"] = $user;

        $data["rentListingCount"] = Property::where("agent_id", $user->id)->where("for", "RENT")->count();
        $data["saleListingCount"] = Property::where("agent_id", $user->id)->where("for", "SALE")->count();

        $data["subCommunities"] = SubCommunity::whereHas("users", fn($q) => $q->where("user_id", $user->id))->get();

        $portals = Portal::whereHas("users", fn($q) => $q->where("user_id", $user->id))->where("status", true)->get(["id", "name", "quota", "thumbnail"]);

        $data["portalsQuotas"] = [];
        foreach ($portals as $portal) {
            $data["portalsQuotas"][] = [
                "id" => $portal->id,
                "name" => $portal->name,
                "thumbnail" => $portal->thumbnail,
                "quota" => $portal->quota ?? 0,
                "allocation" => PortalUser::where("user_id", $user->id)->where("portal_id",
                    $portal->id)->first()->allocation ?? 0,
            ];
        }

        $data["recentLeads"] = Lead::with(["leadAgents.agent", "leadStatus"])->where("agent_id", $user->id)->latest()->take(5)->get();

        return view('admin.user.view')->with($data);
    }
    public function view2(User $user)
    {
        $notAdmin = auth()->user()->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);

        $childrenIds = getUserChildrenIds($user->id);
        abort_if(Gate::denies("Users View") || ($notAdmin && !in_array($user->parent_id, $childrenIds)), 401);

        $user->load(["roles"]);
        $data["user"] = $user;

        $data["rentListingCount"] = Property::where("agent_id", $user->id)->where("for", "RENT")->count();
        $data["saleListingCount"] = Property::where("agent_id", $user->id)->where("for", "SALE")->count();

        $data["subCommunities"] = SubCommunity::whereHas("users", fn($q) => $q->where("user_id", $user->id))->get();

        $portals = Portal::whereHas("users", fn($q) => $q->where("user_id", $user->id))->where("status", true)->get(["id", "name", "quota", "thumbnail"]);

        $data["portalsQuotas"] = [];
        foreach ($portals as $portal) {
            $data["portalsQuotas"][] = [
                "id" => $portal->id,
                "name" => $portal->name,
                "thumbnail" => $portal->thumbnail,
                "quota" => $portal->quota ?? 0,
                "allocation" => PortalUser::where("user_id", $user->id)->where("portal_id",
                    $portal->id)->first()->allocation ?? 0,
            ];
        }

        $data["recentLeads"] = Lead::with(["leadAgents.agent", "leadStatus"])->where("agent_id", $user->id)->latest()->take(5)->get();

        return view('admin.user.edit')->with($data);
    }

    public function store(Request $request)
    {
        $superAdminId = Role::where('name', "Super Admin")->first();
        $validatedData = $request->validate([
            "roles" => "required|array",
            "roles.*" => "required|exists:roles,id",
            "first_name" => "required|string",
            "last_name" => "required|string",
            "mobile" => [
                "required",
                "string",
                Rule::unique('users')->where(function ($query) {
                    $query->where('status', true);
                }),
            ],
            "email" => "required|string|email:rfc,dns|unique:users",
            "secondary_email" => "nullable|string|email:rfc,dns",
            "bitrix_id" => "nullable",
            "nationality_id" => "nullable|exists:countries,id",
            "license_no" => "nullable",
            "api_access" => "nullable",
            "status" => "nullable",
            "region_id" => "required",
            "bio" => "nullable",
            'last_deal_date' => 'nullable',
            'joining_date' => 'nullable',
            "position" => "nullable",
            'bitrix_role' => 'nullable',
            "call_tracking" => "nullable",
            "call_lead" => "nullable",
            "is_commercial" => "nullable",
            "telegram_username" => "nullable",
            "telegram_chatid" => "nullable",
            "website_link" => "nullable",
            "facebook_link" => "nullable",
            "instagram_link" => "nullable",
            "youtube_link" => "nullable",
            "linkedin_link" => "nullable",
            "photo" => "nullable|mimes:jpg,jpeg,png",
            "parent_id" => "nullable|exists:users,id",
            "entity" => "nullable",
            'telelog_entity' => 'nullable',
            "zone_id" => "nullable|exists:groups,id",
            'listing_allowed' => 'nullable',
        ]);
        if (in_array($superAdminId?->id ?? 1, $validatedData['roles']) && !auth()->user()->hasRole("Super Admin")) {
            $result = array('status' => '0', 'msg' => "You don't have permission to create super admin user");
            return json_encode($result);
        }
        $ceoRole = Role::where("name", "CEO")->first();

        if ($ceoRole && !in_array($ceoRole->id, $request->roles) && empty($request->parent_id)) {
            // The "CEO" role doesn't exist in the request roles, and parent_id is empty
            return response()->json(['message' => 'Parent is required', 'status' => 2], 400);
        }

        // if ($parentIdRequired) {
        //     if (empty($request->parent_id)) {
        //         return response([
        //             "errors" => [
        //                 "parent_id" => [
        //                     "Please select parent user",
        //                 ],
        //             ],
        //         ], 422);
        //     }
        // } else {
        //     $validatedData["parent_id"] = 3;
        // }

        try {
            $validatedData["slug"] = Str::slug($request->first_name . " " . $request->last_name, "-");
            $validatedData["password"] = Hash::make(Str::random(10));
            $validatedData["name"] = $request->first_name . " " . $request->last_name;
            $validatedData["created_by_id"] = auth()->id();
            $validatedData["status"] = $request->boolean("status");
            $validatedData["call_tracking"] = $request->boolean("call_tracking");
            $validatedData["is_report_exempt"] = $request->boolean("is_report_exempt");
            $validatedData["call_lead"] = $request->boolean("call_lead");
            $validatedData["is_commercial"] = $request->boolean("is_commercial");
            $validatedData["api_access"] = $request->boolean("api_access");

            if ($request->hasFile("photo")) {
                $images = $this->uploadImageAndThumb($request->file("photo"), "avatar");
                $validatedData["image"] = $images["image"] ?? null;
                $validatedData["thumbnail"] = $images["thumbnail"] ?? null;
            } else {
                unset($validatedData["image"]);
            }

            $user = User::create($validatedData);
            $user->syncRoles($request->roles);
            if (env('APP_ENV') == 'production') {
                event(new UserCreated($user));
            }
            $result = array('status' => '1', 'msg' => 'User added successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }

    public function usersForDatatable(Request $request)
    {
        $columns = array(
            0 => 'updated_at',
            1 => 'id',
            2 => 'name',
            4 => 'email',
            5 => 'updated_by_id',
            6 => 'updated_at',
            7 => 'created_by_id',
            8 => 'created_at',
        );

        $notAdmin = auth()->user()->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        $query = User::query();
        //$totalData = User::when($notAdmin, fn($q) => $q->whereIn("parent_id", $childrenIds))->count();
        $totalData = $this->applyUserFilters($query, $request)->when($notAdmin, fn($q) => $q->whereIn("id", $childrenIds))->when(auth()->user()->hasRole("Admin - Abu Dhabi"), fn($q) => $q->where("region_id", 2))->count();
        $totalFiltered = $totalData;

        $query = User::query();

        $data = $this->applyUserFilters($query, $request)->orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])

            ->offset($request['start'])
            ->limit($request['length'])
            ->when($notAdmin, fn($q) => $q->whereIn("id", $childrenIds))
            ->with('createdBy', 'updatedBy', 'roles', 'parent', 'suspensions')
            ->when(auth()->user()->hasRole("Admin - Abu Dhabi"), fn($q) => $q->where("region_id", 2))
            ->get();

        $json_data = array(
            "draw" => intval($request['draw']),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data,
        );

        echo json_encode($json_data);
    }

    public function getUser(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:users,id',
        ]);
        try {
            $cat = User::where("id", $request->id)->when(!auth()->user()->hasRole("Super Admin"), function ($q) {
                $q->where(function ($q) {
                    $q->whereHas("roles", fn($q) => $q->where("name", "<>", "Super Admin"))->orWhereDoesntHave('roles');
                });
            })->first();
            $cat->append("allocatedPortals");
            $cat->load("roles", "portals.portal", "subCommunities.subCommunity", "parent", 'locations', 'locations.parentable');
            $result = array('status' => '1', 'msg' => 'User details fetched successfully', 'data' => $cat);
        } catch (\Exception $exception) {

            $result = array('status' => '0', 'msg' => $exception->getMessage());
        }
        return json_encode($result);
    }

    public function update(Request $request)
    {

        $superAdminId = Role::where('name', "Super Admin")->first();
        $validatedData = $request->validate([
            "id" => "required|exists:users",
            "roles" => "required|array",
            "roles.*" => "required|exists:roles,id",
            "first_name" => "required|string",
            "last_name" => "required|string",
            "mobile" => [
                "required",
                "string",
                Rule::unique('users')->where(function ($query) {
                    $query->where('status', true);
                })->ignore($request->id),
            ],
            "email" => "required|string|email:rfc,dns|unique:users,email," . $request->id,
            "secondary_email" => "nullable|string|email:rfc,dns",
            "bitrix_id" => "nullable",
            "nationality_id" => "nullable|exists:countries,id",
            "license_no" => "nullable",
            "status" => "nullable",
            "call_tracking" => "nullable",
            "call_lead" => "nullable",
            "is_commercial" => "nullable",
            "api_access" => "nullable",
            "bio" => "nullable",
            'last_deal_date' => 'nullable',
            'joining_date' => 'nullable',
            "region_id" => "required",
            "position" => "nullable",
            "telegram_username" => "nullable",
            "telegram_chatid" => "nullable",
            "website_link" => "nullable",
            "facebook_link" => "nullable",
            "instagram_link" => "nullable",
            "entity" => "nullable",
            "telelog_entity" => "nullable",
            "youtube_link" => "nullable",
            "linkedin_link" => "nullable",
            "bitrix_role" => "nullable",
            "photo" => "nullable|mimes:jpg,jpeg,png",
            "parent_id" => "nullable|exists:users,id",
            "zone_id" => "nullable|exists:groups,id",
            'listing_allowed' => 'nullable',
        ]);

        if (in_array($superAdminId?->id ?? 1, $validatedData['roles']) && !auth()->user()->hasRole("Super Admin")) {
            $result = array('status' => '0', 'msg' => "You don't have permission to update super admin user");
            return json_encode($result);
        }

        $notAdmin = auth()->user()->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);

        $ceoRole = Role::where("name", "CEO")->first();

        if ($ceoRole && !in_array($ceoRole->id, $request->roles) && empty($request->parent_id)) {
            // The "CEO" role doesn't exist in the request roles, and parent_id is empty
            return response()->json(['message' => 'Parent is required', 'status' => 2], 400);
        }
        try {

            $validatedData["slug"] = Str::slug($request->first_name . " " . $request->last_name, "-");
            $validatedData["name"] = $request->first_name . " " . $request->last_name;
            $validatedData["updated_by_id"] = auth()->id();
            // if($validatedData["status"]){
            $validatedData["status"] = $request->boolean("status");
            // }else{
            //     $validatedData["status"] = 0;
            // }

            // $validatedData["call_tracking"] = $request->boolean("call_tracking");
            // $validatedData["call_lead"] = $request->boolean("call_lead");
            // $validatedData["is_commercial"] = $request->boolean("is_commercial");
            // $validatedData["api_access"] = $request->boolean("api_access");
            // $validatedData["is_report_exempt"] = $request->boolean("is_report_exempt");

            if ($request->hasFile("photo")) {
                $images = $this->uploadImageAndThumb($request->file("photo"), "avatar");
                $validatedData["image"] = $images["image"] ?? null;
                $validatedData["thumbnail"] = $images["thumbnail"] ?? null;
            } else {
                unset($validatedData["image"]);
            }

            $user = User::find($request->id);
            $orignal = $user->getOriginal();

            $user->update($validatedData);

            if (!$notAdmin) {
                $user->syncRoles($request->roles);
            }

            if (env('APP_ENV') == 'production') {
                event(new UserUpdated($user));
            }
            $dirty = !empty($user->getDirty()) ? $user->getDirty() : $user->getChanges();
            $orignal = collect($orignal)
                ->only(collect($dirty)->keys())
                ->toArray();

            $arr = ['attributes' => $dirty, 'old' => !empty($orignal) ? $orignal : $user->getOriginalMatchingChanges()];
            activity()->performedOn($user)->causedBy(auth()->user())->withProperties($arr)->event('updated')
                ->log(auth()->user()->first_name . ' has updated User ' . $user->id);

            $result = array('status' => '1', 'msg' => 'User details updated successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }
    public function setApiAccess(Request $request)
    {
        $request->validate([
            "id" => "required|exists:users",
            "api_access" => "nullable",
            "api_rate_limit" => "numeric|nullable",
        ]);
        try {
            $access = $request->api_access == 'on' ? 1 : 0;
            $user = User::find($request->id);
            $orignal = $user->getOriginal();

            $user->api_access = $access;
            $user->api_rate_limit = $request->api_rate_limit ?? 0;
            $user->save();
            if (env('APP_ENV') == 'production') {
                event(new UserUpdated($user));
            }

            if ($user->api_access == '0') {
                $this->revokeApiTokens($user);
            }
            if ($access) {
                $result = array('status' => '1', 'msg' => 'Api Access details updated successfully');
            } else {
                $result = array('status' => '1', 'msg' => 'Api Access details updated successfully');
            }
            $dirty = !empty($user->getDirty()) ? $user->getDirty() : $user->getChanges();
            $orignal = collect($orignal)
                ->only(collect($dirty)->keys())
                ->toArray();
            $arr = ['attributes' => $dirty, 'old' => !empty($orignal) ? $orignal : $user->getOriginalMatchingChanges()];
            activity()->performedOn($user)->causedBy(auth()->user())->withProperties($arr)->event('updated')
                ->log(auth()->user()->first_name . ' has updated User ' . $user->id);

        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);

    }

    private function revokeApiTokens($user)
    {

        if (is_array($user) && count($user) > 0) {
            $user = User::whereIn('id', $user)->get();

            foreach ($user as $u) {
                $u->tokens()->delete();
            }
        } else {
            $user->tokens()->delete();
        }

        return true;
    }
    public function setTraccarDetails(Request $request)
    {
        $request->validate([
            "id" => "required|exists:users",
            "traccar_status" => "nullable",
            "device_identifier" => "nullable",
            "port" => "nullable",
            "mic_status" => "nullable",
        ]);
        try {

            $traccar_status = $request->traccar_status == 'on' ? 1 : 0;
            $mic_status = $request->mic_status == 'on' ? 1 : 0;
            $device_identifier = $request->device_identifier;
            $port = $request->port;
            $user = User::find($request->id);
            $orignal = $user->getOriginal();

            $user->traccar_status = $traccar_status;
            $user->port = $port;
            $user->device_identifier = $device_identifier;
            $user->mic_status = $mic_status;
            if ($user->save()) {
                $res = [];
                if ($user->wasChanged('traccar_status') || $user->wasChanged('port') || $user->wasChanged('device_identifier') || $user->wasChanged('mic_status')) {
                    $data = array(
                        'flag' => 'traccar_user_setting_updated',
                    );
                    $res = $this->silentNotification($user->device_token, $data);
                }
                $result = array('status' => '1', 'msg' => 'Traccar details updated successfully', 'firebase_response' => $res);
            } else {
                $result = array('status' => '1', 'msg' => 'Traccar details updated successfully');
            }
            $dirty = !empty($user->getDirty()) ? $user->getDirty() : $user->getChanges();
            $original = collect($orignal)
                ->only(collect($dirty)->keys())
                ->toArray();
            $arr = ['attributes' => $dirty, 'old' => $original];
            activity()->performedOn($user)
                ->causedBy(auth()->user())
                ->withProperties($arr)->event('updated')
                ->log(auth()->user()->first_name . ' has updated User ' . $user->id);
            Log::channel('mobileApi')->info('setTraccarDetails: Sent Traccar details to:' . $user->id);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }

        return json_encode($result);

    }

    public function setUserLocationCordinates(Request $request)
    {
        $request->validate([
            "user_id" => "required|exists:users,id",
            "id" => "nullable|exists:traccar_restricted_coordinates",
            "title" => "nullable",
            "latitude" => "nullable",
            "longitude" => "nullable",
            "radius" => "nullable",
            "zoom" => "nullable",
            "stay_time" => "nullable",
        ]);
        try {
            if ($request->id != null) {
                $obj = TraccarRestrictedCoordinate::find($request->id);
            } else {
                $obj = new TraccarRestrictedCoordinate();
            }
            $obj->user_id = $request->user_id;
            $obj->title = $request->title;
            $obj->lat = $request->latitude;
            $obj->lng = $request->longitude;
            $obj->radius = $request->radius;
            $obj->zoom = $request->zoom;
            $obj->stay_time = $request->stay_time;
            $obj->created_by_id = auth()->id();
            if ($obj->save()) {
                $data = array(
                    'flag' => 'traccar_user_setting_updated',
                );
                $user = User::find($request->user_id);
                $res = $this->silentNotification($user->device_token, $data);
                $result = array('status' => '1', 'msg' => 'Location details updated successfully', 'firebase_response' => $res);
            } else {
                $result = array('status' => '1', 'msg' => 'Location details updated successfully');
            }

        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }
    public function deleteUserCordinates(Request $request)
    {
        $request->validate([
            "id" => "required|exists:traccar_restricted_coordinates",
        ]);
        try {
            $obj = TraccarRestrictedCoordinate::find($request->id);
            $user_id = $obj->user_id;
            if ($obj->delete()) {
                $data = array(
                    'flag' => 'traccar_user_setting_updated',
                );
                $user = User::find($obj->user_id);
                $res = $this->silentNotification($user->device_token, $data);
                $result = array('status' => '1', 'msg' => 'Location details deleted successfully', 'firebase_response' => $res);
            } else {
                $result = array('status' => '0', 'msg' => 'Location not deleted');
            }

        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }
    public function getUserLocationCordinates(Request $request)
    {
        $request->validate([
            "id" => "required|exists:traccar_restricted_coordinates",

        ]);
        try {
            $data = TraccarRestrictedCoordinate::find($request->id);
            if (!empty($data)) {
                $result = array('status' => '1', 'msg' => 'Location details updated successfully', 'data' => $data);
            } else {
                $result = array('status' => '1', 'msg' => 'Location details updated successfully');
            }

        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }
    public function getUserLocationCorrdinates(Request $request)
    {
        $request->validate([
            "id" => "required|exists:users",
        ]);
        try {
            $obj = TraccarRestrictedCoordinate::where('user_id', $request->id)->get() ?? [];
            if (count($obj) > 0) {
                $result = array('status' => '1', 'msg' => 'Location details fetched successfully', 'data' => $obj);
            } else {
                $result = array('status' => '0', 'msg' => 'Location details not found');
            }

        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }
    public function allocatePortals(Request $request)
    {
        $request->validate([
            "id" => "required|exists:users",
            'portals' => 'required|array',
        ]);

        try {
            $user = User::find($request->id);
            foreach ($request->portals ?? [] as $id => $portal) {
                if (!empty($portal["allocation"])) {
                    PortalUser::updateOrCreate([
                        "user_id" => $user->id,
                        "portal_id" => $id,
                    ], [
                        "allocation" => $portal["allocation"],
                        "created_by_id" => auth()->id(),
                        "updated_by_id" => auth()->id(),
                    ]);
                } else {
                    PortalUser::where("user_id", $user->id)->where("portal_id", $id)->delete();
                }
            }
            $result = array('status' => '1', 'msg' => 'Portals allocated successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }

    public function Back_allocateSubCommunities(Request $request)
    {
        $request->validate([
            "id" => "required|exists:users",
            "community_id" => "nullable|exists:communities,id",
            'sub_communities' => 'required|array',
        ]);

        try {
            $user = User::find($request->id);
            $user->update([
                "updated_by_id" => auth()->id(),
                "community_id" => $request->community_id,
            ]);

            SubCommunityUser::where("user_id", $user->id)->whereNotIn("sub_community_id",
                $request->sub_communities ?? [])->delete();
            foreach ($request->sub_communities ?? [] as $index => $subCommunity) {
                SubCommunityUser::updateOrCreate([
                    "user_id" => $user->id,
                    "sub_community_id" => $subCommunity,
                ], [
                    "community_id" => $request->community_id,
                    "created_by_id" => auth()->id(),
                    "updated_by_id" => auth()->id(),
                ]);
            }
            $result = array('status' => '1', 'msg' => 'Sub Communities allocated successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }

    public function __allocateSubCommunities(Request $request)
    {

        // dd($request->all());
        $request->validate([
            "id" => "required|exists:users",
            "community_id" => "nullable|exists:communities,id",
            'sub_communities' => 'required|array',
        ]);

        try {
            $user = User::find($request->id);
            $user->update([
                "updated_by_id" => auth()->id(),
            ]);

            $subCommunities = $request->sub_communities;

            foreach ($subCommunities as $subCommunityString) {
                list($id, $type) = explode(':', $subCommunityString);

                $userLocation = new UserLocation();
                $userLocation->parentable_id = $id;
                $userLocation->parentable_type = getParentableClass($type);
                $userLocation->user_id = $user->id;
                $userLocation->save();

            }

            $result = array('status' => '1', 'msg' => 'Location(s) allocated successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }

    public function allocateSubCommunities(Request $request)
    {
        $request->validate([
            "id" => "required|exists:users",
            'sub_communities' => 'required|array',
        ]);

        try {
            if (auth()->user()->hasRole(['Super Admin', 'Admin'])) {
                $user = User::find($request->id);
                $orignal = $user->getOriginal();

                $user->update([
                    "updated_by_id" => auth()->id(),
                ]);
                $subCommunities = $request->sub_communities;
                $updatedUserLocationIds = [];

                foreach ($subCommunities as $subCommunityString) {
                    list($id, $type) = explode(':', $subCommunityString);
                    $parentableType = Str::ucfirst(getParentableClass($type));
                    $userLocation = UserLocation::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'parentable_id' => $id,
                            'parentable_type' => $parentableType,
                        ]
                    );
                    $updatedUserLocationIds[] = $userLocation->id;
                }
                UserLocation::where('user_id', $user->id)->whereNotIn('id', $updatedUserLocationIds)->delete();

                // $dirty = !empty($user->getDirty()) ? $user->getDirty() : $user->getChanges();
                // $orignal = collect($orignal)->only(collect($dirty)->keys())->toArray();

                // $arr = ['attributes' => $dirty, 'old' => !empty($orignal) ? $orignal : $user->getOriginalMatchingChanges()];
                // activity()->performedOn($user)->causedBy(auth()->user()->id)->withProperties($arr)->event('updated')->log(auth()->user()->first_name . ' has updated User ' . $user->id);
            } else {
                $result = array('status' => '1', 'msg' => 'Cannot allocate location(s). You do not have permission to allocate location(s) to this user.');

            }

            $result = array('status' => '1', 'msg' => 'Location(s) allocated successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }

    public function assignPublicProfile(Request $request)
    {
        $validatedData = $request->validate([
            "id" => "required|exists:users",
            "pp_enabled" => "nullable",
            "pp_name" => "required|string",
            "pp_image" => "nullable|mimes:jpg,jpeg,png",
            "pp_license_no" => "required|string",
        ]);
        try {
            $user = User::find($request->id);
            $orignal = $user->getOriginal();

            $validatedData["pp_enabled"] = $request->boolean("pp_enabled");
            if ($request->hasFile("pp_image")) {
                $images = $this->uploadImageAndThumb($request->file("pp_image"), "avatar");
                $validatedData["pp_photo"] = $images["image"] ?? null;
            } else {
                unset($validatedData["pp_photo"]);
            }
            if (!$validatedData["pp_enabled"]) {
                $validatedData["pp_photo"] = null;
                $validatedData["pp_name"] = null;
                $validatedData["pp_license_no"] = null;

            }
            $user->updated_at = now();
            $user->updated_by_id = auth()->id();
            $user->update($validatedData);

            $dirty = !empty($user->getDirty()) ? $user->getDirty() : $user->getChanges();
            $orignal = collect($orignal)
                ->only(collect($dirty)->keys())
                ->toArray();
            $arr = ['attributes' => $dirty, 'old' => !empty($orignal) ? $orignal : $user->getOriginalMatchingChanges()];
            activity()->performedOn($user)->causedBy(auth()->user())->withProperties($arr)->event('updated')
                ->log(auth()->user()->first_name . ' has updated User ' . $user->id);

            $result = array('status' => '1', 'msg' => 'Public profile assigned successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:users',
            'action' => 'required',
        ]);

        $user = User::find($request->id);
        $user->status = $request->boolean('action');
        $user->updated_by_id = auth()->user()->id;
        if ($user->save()) {

            if ($user->status == '0') {
                $this->revokeApiTokens($user);
            }
            $result = array('status' => '1', 'msg' => 'User status updated successfully');
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }

        if (env('APP_ENV') == 'production') {
            event(new UserUpdated($user));
        }

        return json_encode($result);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:users',
        ]);

        $user = User::find($request->id);
        $this->revokeApiTokens($user);
        if ($user->delete()) {
            $result = array('status' => '1', 'msg' => 'User deleted successfully');
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        if (env('APP_ENV') == 'production') {
            event(new UserUpdated($user));
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
            $this->revokeApiTokens($ids);
            if (User::whereIn('id', $ids)->delete()) {
                $result = array('status' => '1', 'msg' => count($ids) . ' Users deleted successfully');
            } else {
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }
        } else {
            // dd($request->ids);
        }
        return json_encode($result);
    }

    public function changeParentId(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:users',
            'parent_id' => 'required|exists:users,id',
        ]);

        $user = User::find($request->id);
        $user->parent_id = $request->parent_id;
        if ($user->save()) {
            $result = array('status' => '1', 'msg' => 'Parent changed successfully', 'dat' => $user);
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function saveAndSendCredentails(Request $request)
    {
        $validatedData = $request->validate([
            "id" => "required|exists:users",
            "password" => "required|confirmed|min:6",
            "type" => "required|string",
        ]);

        try {

            $validatedData["password"] = Hash::make($request->password);
            $user = User::find($request->id);
            $user->tokens()->delete();

            $user->update($validatedData);
            //send email

            if (env('APP_ENV') == 'production') {
                event(new UserUpdated($user));
            }

            if ($request->type == "send") {
                $data = [
                    "email" => $user->email,
                    "name" => $user->name,
                    "password" => $request->password,
                ];
                Mail::to($user)->send(new SendPasswordMail($data));
                $result = array('status' => '1', 'msg' => 'Credentials saved and sent successfully');
            } else {
                $result = array('status' => '1', 'msg' => 'Credentials saved successfully');
            }
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }

    public function updateProfile(Request $request)
    {
        $validatedData = $request->validate([
            "id" => "required|exists:users",
            "first_name" => "required|string",
            "last_name" => "required|string",
            "mobile" => "required|string|unique:users,mobile," . $request->id,
            "email" => "required|string|unique:users,email," . $request->id,
            "country_id" => "nullable",
            "state_id" => "nullable",
            "nationality_id" => "nullable",
            "licence_no" => "nullable",
            "status" => "nullable",
            "bio" => "nullable",
        ]);

        try {
            $validatedData["slug"] = Str::slug($request->first_name . " " . $request->last_name, "-");
            $validatedData["name"] = $request->first_name . " " . $request->last_name;
            $validatedData["updated_by_id"] = auth()->id();

            $user = User::find($request->id);
            $user->update($validatedData);

            if (env('APP_ENV') == 'production') {
                event(new UserUpdated($user));
            }

            $result = array('status' => '1', 'msg' => 'User details updated successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }

    public function updateSocialMedia(Request $request)
    {
        $validatedData = $request->validate([
            "id" => "required|exists:users",
            "website_link" => "nullable",
            "facebook_link" => "nullable",
            "instagram_link" => "nullable",
            "youtube_link" => "nullable",
            "linkedin_link" => "nullable",
        ]);

        try {
            $validatedData["updated_by_id"] = auth()->id();
            $user = User::find($request->id);
            $user->update($validatedData);
            $result = array('status' => '1', 'msg' => 'User social media details updated successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }

    public function sendTelegramOtp(Request $request)
    {
        $validatedData = $request->validate([
            "id" => "required|exists:users",
        ]);

        try {

            $old_unused = Otp::where('user_id', $request->id)->where('used', 0)->where('for', 'telegram')->first();
            if (!empty($old_unused)) {
                $result = array('status' => '1', 'msg' => 'OTP sent successfully', 'otp' => $old_unused->otp);
                return response()->json($result);
            }
            Otp::where('user_id', $request->id)->where('used', 0)->where('for', 'telegram')->update(['used' => '1']);
            $uotp = rand(100000, 999999);
            $otp = new Otp();
            $otp->user_id = $request->id;
            $otp->for = "telegram";
            $otp->otp = $uotp;
            if ($otp->save()) {
                $result = array('status' => '1', 'msg' => 'OTP sent successfully', 'otp' => $uotp);
            } else {
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }
            return response()->json($result);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }

    public function verifyTelegramOtp(Request $request)
    {
        $validatedData = $request->validate([
            "id" => "required|exists:users",
        ]);

        try {
            $old_unused = Otp::where('user_id', $request->id)->where('used', 0)->where('for', 'telegram')->first();
            $user = User::where('id', $request->id)->first();
            if ((new TelegramController)->verifyTelegram($user->id, $user->telegram_username, $old_unused->otp)) {
                $result = array('status' => '1', 'msg' => 'Your telegram account has been connected successfully');
            } else {
                $result = array('status' => '0', 'msg' => 'Unable to verify please try again!');
            }
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return response()->json($result);
    }
    public function getUserDataCount(Request $request)
    {
        $validatedData = $request->validate([
            "id" => "required|exists:users",

        ]);

        try {
            $data['contacts'] = Contact::where('agent_id', $request->id)->count();
            $data['contact_additional_agent'] = ContactAgent::where('user_id', $request->id)->count();
            $data['property'] = Property::where('agent_id', $request->id)->count();
            $data['lead'] = Lead::where('agent_id', $request->id)->count();
            $data['lead_additional_agent'] = LeadAgent::where('user_id', $request->id)->count();
            $data['user'] = User::where('id', $request->id)->with('roles')->first();

            $result = array('status' => '1', 'msg' => 'fetched successfully', 'data' => $data);

        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return response()->json($result);
    }
    public function assignDataToUser(Request $request)
    {
        $validatedData = $request->validate([
            "id" => "required|exists:users",
            "assign_only" => "required|numeric:1,2",
        ]);

        try {
            $user = User::where('id', $request->id)->first();
            //$systemUser = User::where('email','default@axcapital.ae')->first();

            $properties = Property::where('agent_id', $user->id)->pluck('id');
            foreach ($properties as $property) {
                $new_user = User::where('id', $request->property_user)->first();
                Property::where('id', $property)->update(['agent_id' => $request->property_user]);
                $note = new PropertyNote();
                $note->property_id = $property;
                $note->note = 'Property agent changed to ' . $new_user->name . ' from ' . $user->name;
                $note->created_by_id = auth()->user()->id;
                $note->save();
            }
            $contacts = Contact::where('agent_id', $user->id)->pluck('id');
            foreach ($contacts as $contact) {
                $new_user = User::where('id', $request->contact_user)->first();
                Contact::where('id', $contact)->update(['agent_id' => $request->contact_user]);
                $note = new ContactNote();
                $note->contact_id = $contact;
                $note->note = 'Contact agent changed to ' . $new_user->name . ' from ' . $user->name;
                $note->created_by_id = auth()->user()->id;
                $note->save();
            }
            ContactAgent::where('user_id', $user->id)->update(['user_id' => $request->contact_user]);
            $leads = Lead::where('agent_id', $user->id)->pluck('id');
            foreach ($leads as $lead) {
                $new_user = User::where('id', $request->lead_user)->first();
                Lead::where('id', $lead)->update(['agent_id' => $request->lead_user]);
                $note = new LeadNote();
                $note->lead_id = $lead;
                $note->note = 'Lead agent changed to ' . $new_user->name . ' from ' . $user->name;
                $note->created_by_id = auth()->user()->id;
                $note->save();
            }
            LeadAgent::where('user_id', $user->id)->update(['user_id' => $request->lead_user]);
            if ($request->assign_only != 1) {
                if ($user->delete()) {
                    if (env('APP_ENV') == 'production') {
                        event(new UserUpdated($user));
                    }
                    $result = array('status' => '1', 'msg' => 'Data transferred to new users and selected user deleted successfully');
                } else {
                    $result = array('status' => '0', 'msg' => 'Something went wrong!!');
                }
            } else {
                $result = array('status' => '1', 'msg' => 'Data assigned to new user successfully', 'data' => $request->all());
            }

        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
            return response()->json($result, 500);
        }
        return response()->json($result);
    }
    public function assignDataToUserDeactivation(Request $request)
    {
        // dd($request->all());
        $validatedData = $request->validate([
            "id" => "required|exists:users",
            "assign_only" => "required|numeric:1,2",
        ]);

        try {
            $user = User::where('id', $request->id)->first();
            $user->tokens()->delete();
            //$systemUser = User::where('email','default@axcapital.ae')->first();

            $properties = Property::where('agent_id', $user->id)->pluck('id');
            foreach ($properties as $property) {
                $new_user = User::where('id', $request->property_user)->first();
                Property::where('id', $property)->update(['agent_id' => $request->property_user, 'previous_agent_id' => $user->id]);
                $note = new PropertyNote();
                $note->property_id = $property;
                $note->note = 'Property agent changed to ' . $new_user->name . ' from ' . $user->name;
                $note->created_by_id = auth()->user()->id;
                $note->save();
            }
            $contacts = Contact::where('agent_id', $user->id)->pluck('id');
            foreach ($contacts as $contact) {
                $new_user = User::where('id', $request->contact_user)->first();
                Contact::where('id', $contact)->update(['agent_id' => $request->contact_user, 'previous_agent_id' => $user->id]);
                $note = new ContactNote();
                $note->contact_id = $contact;
                $note->note = 'Contact agent changed to ' . $new_user->name . ' from ' . $user->name;
                $note->created_by_id = auth()->user()->id;
                $note->save();
            }
            ContactAgent::where('user_id', $user->id)->update(['user_id' => $request->contact_user]);
            $leads = Lead::where('agent_id', $user->id)->pluck('id');
            foreach ($leads as $lead) {
                $new_user = User::where('id', $request->lead_user)->first();
                if ($request->send_to_leadpool && $request->send_to_leadpool == 1) {

                    $leadAgent = Lead::where('id', $lead)->first();
                    if ($leadAgent->lead_status_id != 12 && $leadAgent->is_archive == 0) {
                        $leadAgent->agent_id = null;
                        $leadAgent->previous_agent_id = $user->id;
                        $leadAgent->updated_by_id = auth()->user()->id;
                        $leadAgent->save();

                        $note = new LeadNote();
                        $note->lead_id = $lead;
                        $note->note = 'Lead agent changed to Lead Pool from ' . $user->name;
                        $note->created_by_id = auth()->user()->id;
                        $note->save();
                    }

                } else {
                    // Lead::where('id', $lead)->update(['agent_id' => $request->lead_user,'previous_agent_id'=>$user->id,'updated_by_id'=>auth()->user()->id]);
                    $leadAgent = Lead::where('id', $lead)->first();
                    if ($leadAgent->lead_status_id != 12 && $leadAgent->is_archive == 0) {
                        $leadAgent->agent_id = $request->lead_user;
                        $leadAgent->previous_agent_id = $user->id;
                        $leadAgent->updated_by_id = auth()->user()->id;
                        $leadAgent->save();

                        $note = new LeadNote();
                        $note->lead_id = $lead;
                        $note->note = 'Lead agent changed to ' . $new_user->name . ' from ' . $user->name;
                        $note->created_by_id = auth()->user()->id;
                        $note->save();
                    }
                }

            }
            LeadAgent::where('user_id', $user->id)->update(['user_id' => $request->lead_user]);
            if ($request->assign_only != 1) {
                if ($user) {
                    $user->status = 0;
                    $user->save();

                    if (env('APP_ENV') == 'production') {
                        event(new UserUpdated($user));
                    }

                    $result = array('status' => '1', 'msg' => 'Data transferred to new users and selected user Deactivated successfully');
                } else {
                    $result = array('status' => '0', 'msg' => 'Something went wrong!!');
                }
            } else {
                $result = array('status' => '1', 'msg' => 'Data assigned to new user successfully', 'data' => $request->all());
            }

        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
            return response()->json($result, 500);
        }
        return response()->json($result);
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new UserExport($request->columns), "users" . time() . ".xlsx");
    }

    public function _getActivityLog(Request $request)
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

//    public function getActivityLog(Request $request)
//    {
//
//        $changes = \App\Models\ModelUpdate::with('causer')->where('model_type', 'App\Models\User')->where('model_id', $request->subject_id)->orderBy('id', 'desc')->limit('20')->get();
//
//        if ($changes != null) {
//            $datas = [];
//            foreach ($changes as $data) {
//                $properties['old'] = isset($data->data) ? $data->data : [];
//                $properties['attributes'] = isset($data->data) ? $data?->data : [];
//                $data->properties = $data->data;
//                $datas[] = $data;
//
//            }
//        }
//        return response()->json(['data' => $datas]);
//    }
    public function getActivityLog(Request $request)
    {
        $perPage = $request->input('per_page', 10); // Default to 20 items per page if not provided
        $changes = \App\Models\ModelUpdate::with('causer')
            ->where('model_type', 'App\Models\User')
            ->where('model_id', $request->subject_id)
            ->orderBy('id', 'desc')
            //->whereNotNull('updated_by_id')
            ->whereRaw("NOT JSON_CONTAINS_PATH(data, 'one', '$.last_login')")
            ->whereRaw("NOT JSON_CONTAINS_PATH(data, 'one', '$.remember_token')")
            ->whereRaw("NOT JSON_CONTAINS_PATH(data, 'one', '$.third_party_ids')")
            ->paginate($perPage);

        $changes->getCollection()->transform(function ($data) {
            $data->properties = [
                'old' => isset($data->data) ? $data->data : [],
                'attributes' => isset($data->data) ? $data->data : [],
            ];
            return $data;
        });

        return response()->json($changes);
    }

    public function getAllActivityLog(Request $request, $id)
    {

        // $activities = Activity::where('causer_id', $id)->orderBy('created_at', 'desc')->limit('10')->get()->groupBy(function ($item, $key) {
        //     return $item->created_at->format('Y-m-d');
        // });

        $activities = Activity::where('causer_id', $id)->where('created_at', '>=', Carbon::now()->subDays(30))->orderBy('created_at', 'desc')->get()->groupBy(function ($item, $key) {
            return $item->created_at->format('Y-m-d');
        });

        $activities = $activities->map(function ($item, $key) {
            return $item->groupBy('subject_type');
        });

        return view('admin.user.activity_log', compact('activities'));
        return response()->json(['data' => $activities]);
    }

    public function checkMobileAppStatus(Request $request)
    {
        $logs = AppLog::where('user_id', $request->id)->where('created_at', '>=', Carbon::now()->subHours(12))->orderBy('created_at', 'desc')->first();
        // dd($logs);
        if ($logs && $logs->count() > 0) {
            $result = array('status' => '1', 'msg' => 'Mobile App is active');
        } else {
            $result = array('status' => '0', 'msg' => 'Mobile App is not active');
        }
        return response()->json($result);

    }

    public function checkTraccarStatus(Request $request)
    {
        $logs = CoordinateRecording::where('user_id', $request->id)->where('created_at', '>=', Carbon::now()->subMinutes(15))->orderBy('created_at', 'desc')->first();

        if ($logs && $logs->count() > 0) {
            $result = array('status' => '1', 'msg' => 'Traccar is active');
        } else {
            $result = array('status' => '0', 'msg' => 'Traccar is not active');
        }
        return response()->json($result);

    }

    public function logoutFromMobileApp(Request $request)
    {

        $request->validate([
            'id' => 'required|exists:users',
        ]);

        try {
            $user = User::find($request->id);
            $serverKey = 'AAAA_OxfTQE:APA91bE22wzYji8aEerbkVtrYsbtJFoxM-spY1PTFWw1sf0jo0utgl06sJsvayFuyNo772o1TYjiLz44XcRr1JrrUJ9yvmsDVHqmBgVFa_B_l4uPXpG_tnUIk_BBUuYMjHAGSyvPfxvs';
            $deviceToken = $user->device_token;

            $notification = [
                'title' => 'Login Expired',
                'body' => 'Please tap to login again.',
            ];

            $data = [
                'flag' => "Expired",
            ];

            $payload = [
                'notification' => $notification,
                'data' => $data,
                'to' => $deviceToken,
            ];

            $response = Http::withHeaders([
                'Authorization' => 'key=' . $serverKey,
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', $payload);

            $result = array('status' => '1', 'msg' => 'Notification sent successfully');
            return $result;

        } catch (\Throwable $th) {
            $result = array('status' => '0', 'msg' => 'Notification not sent.', 'error' => $th->getMessage());
            return $result;
        }

    }

    public function getUserDevices(Request $request)
    {
       // $devices = UserDevice::where('user_id', $request->id)->get();
        $devices = UserAppLogin::where('user_id', $request->id)->get();
        return view('admin.user.includes.component._devices')->with('devices', $devices);
    }
    public function getUserSuspensions(Request $request)
    {
        $suspensions = UserMeta::where('user_id', $request->id)
            ->where(function ($query) {
                $query->where('tag', 'lead_inactivity_deactivate')
                    ->orWhere('tag', 'lead_inactivity_warning');
            })->orderBy('created_at', 'desc')
            ->get();
        return view('admin.user.includes.component._suspension_data')->with('suspensions', $suspensions);
    }

    public function webHookTestResponse()
    {

        $user = User::latest()->first();

        $url = "https://telelog.axcrm.ae/backend/api/create-user-from-secondary-webhook";
        $client = new Client();
        $data = [

            'name' => $user->first_name . ' ' . $user->last_name,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'position' => $user->position,
            'password' => $user->password,
            'mobile' => $user->mobile,
            'thumbnail' => $user->thumbnail,
            'image' => $user->image,
            'avatar' => $user->avatar,
            'telegram_username' => $user->telegram_username,
            'telegram_chatid' => $user->telegram_chatid,
            'status' => $user->status,
            'entity_id' => 1,
            'roles' => $user->getRoleNames()->implode(', '),

        ];
        try {
            $response = $client->post($url, [
                'form_params' => $data,
            ]);

            $statusCode = $response->getStatusCode();
            $responseBody = $response->getBody()->getContents();

            $userName = $user->full_name; // Replace this with the actual user's name
            $message = "New User Created:\n";
            $message .= "User Name: $userName\n";
            $message .= "Timestamp: " . now()->toDateTimeString() . "\n";
            $message .= "IP Address: " . request()->ip() . "\n";

            info($message);
            info($data);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $statusCode = $e->getResponse()->getStatusCode();
                $responseBody = $e->getResponse()->getBody()->getContents();

            }
        }

    }

    public function UserWithOutDevices()
    {
        return Excel::download(new UsersDeviceExport, 'users.csv');
    }

    public function UserWithOutAuthToken()
    {

        return Excel::download(new UsersAuthToken, 'users-with-no-authtoken.csv');

        // $users = User::where('status',1)->whereDoesntHave('authToken')->whereHas('roles', function ($query) {
        //     $query->whereIn('name', ['Agent','Line Manager','Team Leader','Sales Director']);
        // })->get();
    }
}
