<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\ExternalApiController;
use App\Jobs\GeneratePropertyTitleJob;
use App\Models\Community;
use App\Models\CommunityProperty;
use App\Models\Contact;
use App\Models\ContactNote;
use App\Models\DealNote;
use App\Models\DealSubStatus;
use App\Models\DeveloperProject;
use App\Models\Event;
use App\Models\Folder;
use App\Models\JobNote;
use App\Models\Lead;
use App\Models\LeadNote;
use App\Models\LeadSubStatus;
use App\Models\ListingLead;
use App\Models\ListingLeadNote;
use App\Models\Portal;
use App\Models\PortalUser;
use App\Models\Property;
use App\Models\PropertyAmenity;
use App\Models\PropertyCycle;
use App\Models\PropertyMedia;
use App\Models\TempProperty;
use App\Models\TempPropertyMedia;
use App\Models\PropertyMeta;
use App\Models\PropertyNote;
use App\Models\PropertyPortal;
use App\Models\RepublishRequest;
use App\Models\State;
use App\Models\SubCommunity;
use App\Models\SubCommunityUser;
use App\Models\User;
use App\Models\UserLastActivity;
use App\Traits\FiltersTrait;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;
use Telegram\Bot\Laravel\Facades\Telegram;

class AjaxController extends Controller
{
    use FiltersTrait;

    public function communities(Request $request)
    {
        $data = Community::when($request->region_id, function ($q) {
            $q->where("region_id", \request("region_id"));
        })->get();

        return response([
            "data" => $data,
        ]);
    }

    public function subCommunities(Request $request)
    {
        $data = SubCommunity::when($request->community_id, function ($q) {
            $q->where("community_id", \request("community_id"));
        })->when($request->communities, function ($q) {
            $q->whereIn("community_id", \request("communities"));
        })->get();

        return response([
            "data" => $data,
        ]);
    }

    public function communityProperties(Request $request)
    {
        $data = CommunityProperty::when($request->term, function ($q) {
            $q->where("path_name", "LIKE", "%" . \request("term") . "%");
        })->when($request->sub_community_id, function ($q) {
            $q->where("sub_community_id", \request("sub_community_id"));
        })->get();

        return response([
            "data" => $data,
        ]);
    }

    public function userPortals(Request $request)
    {
        $data = PortalUser::when($request->user_id, function ($q) {
            $q->where("user_id", \request("user_id"));
        })->with("portal")->get();

        return response([
            "data" => $data,
        ]);
    }

    public function userSubCommunities(Request $request)
    {
        $data = SubCommunityUser::when($request->user_id, function ($q) {
            $q->where("user_id", \request("user_id"));
        })->with("subCommunity")->get();

        return response([
            "data" => $data,
        ]);
    }

    public function __owners(Request $request)
    {
        if (auth()->user()) {
            $notAdmin = auth()->user()->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);
            $childrenIds = [];
            if ($notAdmin) {
                $childrenIds = getUserChildrenIds(auth()->id());
            }

            $data = Contact::when($request->agent_id, function ($q) {
                $q->where("agent_id", \request("agent_id"))->orWhereHas("contactAgents", fn($q) => $q->where("user_id", \request("agent_id")))->where('status', true);
            })->when($request->agents, function ($q) {
                $q->whereIn("agent_id", \request("agents"));
            })->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds)->orWhereHas("contactAgents", fn($q) => $q->where("user_id", \request("agent_id")))->where('status', true);
            })->whereHas("contactTypes", fn($q) => $q->whereIn("contact_type_id", [3, 4, 5, 9]))->where('status', true)->get();

            return response([
                "data" => $data,
            ]);
        }
    }

    public function owners(Request $request)
    {
        if (auth()->user()) {
            $notAdmin = auth()->user()->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);
            $childrenIds = [];
            if ($notAdmin) {
                $childrenIds = getUserChildrenIds(auth()->id());
            }

            $data = Contact::when($request->agent_id, function ($q) {
                $q->where(function ($query) {
                    $query->where("agent_id", \request("agent_id"))
                        ->orWhereHas("contactAgents", fn($q) => $q->where("user_id", \request("agent_id")));
                })->where('status', true);
            })->when($request->agents, function ($q) {
                $q->whereIn("agent_id", \request("agents"));
            })->when($notAdmin, function ($q) use ($childrenIds) {
                $q->where(function ($query) use ($childrenIds) {
                    $query->whereIn("agent_id", $childrenIds)
                        ->orWhereHas("contactAgents", fn($q) => $q->where("user_id", \request("agent_id")));
                });
            })->whereHas("contactTypes", fn($q) => $q->whereIn("contact_type_id", [3, 4, 5, 9]))
                ->get();

            return response([
                "data" => $data,
            ]);
        }
    }

    public function propertyPhotos(Request $request)
    {
        $data = PropertyMedia::when($request->exists("temp_id"), function ($q) {
            $q->where("temp_id", \request("temp_id"));
        })->when($request->exists("property_id"), function ($q) {
            $q->where("property_id", \request("property_id"));
        })->where("type", "Photos")->where('status', true)->orderBy("order")->get();

        return response([
            "data" => $data,
        ]);
    }
    public function tempPropertyPhotos(Request $request)
    {
        $data = TempPropertyMedia::when($request->exists("temp_id"), function ($q) {
            $q->where("temp_id", \request("temp_id"));
        })->when($request->exists("property_id"), function ($q) {
            $q->where("temp_property_id", \request("property_id"));
        })->where("type", "Photos")->where('status', true)->orderBy("order")->get();

        return response([
            "data" => $data,
        ]);
    }

    public function contacts(Request $request)
    {
        $data = Contact::when($request->agent_id, function ($q) {
            $q->where("agent_id", \request("agent_id"))->whereNotNull("agent_id");
        })->when($request->contact_type_id, function ($q) {
            $q->whereHas("contactTypes", fn($q1) => $q1->where("id", \request("contact_type_id")));
        })->when($request->search, function ($q) {
            $q->where("first_name", "LIKE", "%" . \request("search") . "%")
                ->orWhere("last_name", "LIKE", "%" . \request("search") . "%")
                ->orWhere("mobile", "LIKE", "%" . \request("search") . "%")
                ->orWhere("email", "LIKE", "%" . \request("search") . "%");
        })->where('status', true)->get();

        return response([
            "data" => $data,
        ]);
    }

    public function leads(Request $request)
    {
        $data = Lead::when($request->agent_id, function ($q) {
            $q->where("agent_id", \request("agent_id"))->whereNotNull("agent_id");
        })->when($request->lead_type_id, function ($q) {
            $q->where("lead_type_id", \request("lead_type_id"))->whereNotNull("lead_type_id");
        })->where('status', true)->get();

        return response([
            "data" => $data,
        ]);
    }

    public function properties(Request $request)
    {
        $notAdmin = auth()->user()->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);
        //dd(auth()->user()->id);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }
        $data = Property::when($request->for, function ($q) {
            $this->isArray(\request("for")) ? $q->whereIn("for", \request("for")) : $q->where("for", \request("for"));
        })->when($request->agent_id, function ($q) {
            $q->where("agent_id", \request("agent_id"));
        })->when($request->owner_id, function ($q) use ($notAdmin, $childrenIds) {
            $q->where("owner_id", \request("owner_id"))->when($notAdmin, function ($q) use ($notAdmin, $childrenIds) {
                $q->whereIn('agent_id', $childrenIds);
            });
        })->with(["bed", "propertyCategory", "community", "subCommunity", "portals.portal", "price", "agent", "bed", "photos"])->get();

        return response([
            "data" => $data,
        ]);
    }

    public function propertyNotes(Request $request)
    {
        $request->validate([
            "property_id" => "required|exists:properties,id",
        ]);
        if (!\Auth::check()) {
            return response([
                "data" => [],
            ]);
        }
        $notAdmin = auth()?->user()?->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);

        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
            if ($request->property_id) {
                $property = Property::find($request->property_id);
                if (!in_array($property->agent_id ?? 7, $childrenIds)) {
                    return response([
                        "data" => [],
                    ]);
                }
            }
        }

        $data = PropertyNote::when($request->property_id, function ($q) {
            $q->where("property_id", \request("property_id"));
        })->with(["createdBy"])->get();

        return response([
            "data" => $data,
        ]);
    }

    public function getUserDetails(Request $request)
    {
        $request->validate([
            "agent_id" => "required|exists:users,id",
        ]);

        $user = User::where('id', $request->agent_id)->firstOrFail();

        $userDetails = view('admin.partials.user-profile', get_defined_vars())->render();

        return response()->json(['status' => true, 'userDetails' => $userDetails]);
    }
    public function getMetaHtml(Request $request)
    {

        try {
            $request->validate([
                "property_id" => "required|exists:properties,id",
                "portal_slug" => "required|exists:portals,slug",
                "dont_update" => "nullable|boolean",
            ]);

            $dontUpdate = $request->dont_update ?? false;
            $property = Property::find($request->property_id);
            if ($request->portal_slug == "property-finder" && (!$dontUpdate)) {
                $params = new Request();

                $params->replace(['ref_no' => $property->ref_no]);
                $data = (new ExternalApiController())->fetchPFDataV2($params);
            }


            if (($request->portal_slug == "bayut" || $request->portal_slug == "dubizzle") && (!$dontUpdate)) {
                $params = new Request();

                $params->replace(['ref_no' => $property->ref_no]);
                $data = (new ExternalApiController())->fetchBayutData($params);
            }

            $portalsArray = array("property-finder", "bayut", "dubizzle");
            $propertyMeta = PropertyMeta::where('property_id', $request->property_id)
                ->pluck('meta_value', 'meta_key');
            // dd($propertyMeta);
            if (!in_array($request->portal_slug, $portalsArray) || empty($propertyMeta)) {
                return response()->json(['status' => false, 'meta' => "Not Found"]);
            }
            // dd($request->property_id);
            $metaHtml = view('admin.partials.' . $request->portal_slug, get_defined_vars())->render();

            return response()->json(['status' => 1, 'metaHtml' => $metaHtml, 'data', $data]);

        } catch (\Exception $exception) {

            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage(), 'line' => $exception->getLine());
            return response()->json($result, 500);

        }

    }
    public function agentAllocations(Request $request)
    {
        $request->validate([
            "agent_id" => "required|exists:users,id",
        ]);

        $portals = Portal::where("status", true)->get();

        $data = [];
        foreach ($portals as $portal) {
            $data[] = [
                "id" => $portal->id,
                "name" => $portal->name,
                "quota" => $portal->quota ?? 0,
                "allocation" => PortalUser::where("user_id", $request->agent_id)->where("portal_id",
                    $portal->id)->first()->allocation ?? 0,
                "count" => PropertyPortal::where("portal_id", $portal->id)
                    ->whereHas("property", fn($q) => $q->where("agent_id", $request->agent_id))->count(),
            ];
        }
        return response([
            "data" => $data,
        ]);
    }

    public function portalQuotas(Request $request)
    {
        $portals = Portal::where("status", true)->get(["id", "name", "quota"]);
        $data = [];
        foreach ($portals as $portal) {
            $quota = $portal->quota ?? 0;
            $used = PropertyPortal::where("portal_id", $portal->id)->count();
            $allocated = PortalUser::where("portal_id", $portal->id)->when(!empty($request->user_id), fn($q) => $q->where("user_id", "<>", $request->user_id))->sum("allocation");
            $data[] = [
                "id" => $portal->id,
                "name" => $portal->name,
                "quota" => $quota,
                "used" => $used,
                "available" => $quota - $used,
                "allocated" => $allocated,
                "unallocated" => $quota - $allocated,
            ];
        }
        return response([
            "data" => $data,
        ]);
    }

    public function propertyDocuments(Request $request)
    {
        $request->validate([
            "property_id" => "required|exists:properties,id",
        ]);
        $property = Property::find($request->property_id);
        if (\Auth::check()) {
            $notAdmin = auth()->user()->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);
            $childrenIds = getUserChildrenIds(auth()->id());

            if (!(in_array($property->agent_id, $childrenIds) || auth()->user()->hasRole(["Admin", "Super Admin", "Admin - Abu Dhabi"]))) {
                return response([
                    "data" => [
                        "floor_plan" => null,
                        "title_deed" => null,
                        "owner_passport_copy" => null,
                        "signed_listing_form" => null,
                        "other_documents" => null,
                        "folder_media" => null,
                    ],
                ]);
            }
        }

        $property->load(["floorPlan", "titleDeed", "ownerPassportCopy", "signedListingForm", "otherDocuments"]);

        $floorPlan = $titleDeed = $ownerPassportCopy = $signedListingForm = null;

        $otherDocuments = [];

        if ($property->floorPlan) {
            $floorPlan = $property->floorPlan;
            $floorPlan["slug"] = Str::slug($floorPlan->title);
        }

        if ($property->titleDeed) {
            $titleDeed = $property->titleDeed;
            $titleDeed["slug"] = Str::slug($titleDeed->title);
        }

        if ($property->ownerPassportCopy) {
            $ownerPassportCopy = $property->ownerPassportCopy;
            $ownerPassportCopy["slug"] = Str::slug($ownerPassportCopy->title);
        }

        if ($property->signedListingForm) {
            $signedListingForm = $property->signedListingForm;
            $signedListingForm["slug"] = Str::slug($signedListingForm->title);
        }

        if ($property->otherDocuments) {
            foreach ($property->otherDocuments as $index => $doc) {
                $otherDocuments[$index] = $doc;
                $otherDocuments[$index]["slug"] = Str::slug($doc->title);
            }
        }

        $folders = Folder::where("model_type", Property::class)->where("model_id", $property->id)->with("folderMedia")->get();
        $folderMediaFiles = [];
        foreach ($folders as $folder) {
            foreach ($folder->folderMedia ?? [] as $folderMedia) {
                $type = $slug = $folderMedia->slug;
                if ($folderMedia->data) {
                    $type = $folderMedia->data["type"];
                }
                if (empty($type)) {
                    $slug = Str::slug($folderMedia->title);
                }
                $folderMediaFiles[] = [
                    "id" => $folderMedia->id,
                    "title" => $folderMedia->title,
                    "slug" => $slug,
                    "path" => $folderMedia->path,
                    "type" => $type,
                ];
            }
        }

        return response([
            "data" => [
                "floor_plan" => $floorPlan,
                "title_deed" => $titleDeed,
                "owner_passport_copy" => $ownerPassportCopy,
                "signed_listing_form" => $signedListingForm,
                "other_documents" => $otherDocuments,
                "folder_media" => $folderMediaFiles,
            ],
        ]);
    }

    public function temPropertyDocuments(Request $request)
    {
        $request->validate([
            "property_id" => "required|exists:temp_properties,id",
        ]);
        $property = TempProperty::find($request->property_id);
        if (\Auth::check()) {
            $notAdmin = auth()->user()->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);
            $childrenIds = getUserChildrenIds(auth()->id());

            if (!(in_array($property->agent_id, $childrenIds) || auth()->user()->hasRole(["Admin", "Super Admin", "Admin - Abu Dhabi"]))) {
                return response([
                    "data" => [
                        "floor_plan" => null,
                        "title_deed" => null,
                        "owner_passport_copy" => null,
                        "signed_listing_form" => null,
                        "other_documents" => null,
                        "folder_media" => null,
                    ],
                ]);
            }
        }

        $property->load(["floorPlan", "titleDeed", "ownerPassportCopy", "signedListingForm", "otherDocuments"]);

        $floorPlan = $titleDeed = $ownerPassportCopy = $signedListingForm = null;

        $otherDocuments = [];

        if ($property->floorPlan) {
            $floorPlan = $property->floorPlan;
            $floorPlan["slug"] = Str::slug($floorPlan->title);
        }

        if ($property->titleDeed) {
            $titleDeed = $property->titleDeed;
            $titleDeed["slug"] = Str::slug($titleDeed->title);
        }

        if ($property->ownerPassportCopy) {
            $ownerPassportCopy = $property->ownerPassportCopy;
            $ownerPassportCopy["slug"] = Str::slug($ownerPassportCopy->title);
        }

        if ($property->signedListingForm) {
            $signedListingForm = $property->signedListingForm;
            $signedListingForm["slug"] = Str::slug($signedListingForm->title);
        }

        if ($property->otherDocuments) {
            foreach ($property->otherDocuments as $index => $doc) {
                $otherDocuments[$index] = $doc;
                $otherDocuments[$index]["slug"] = Str::slug($doc->title);
            }
        }

        $folders = Folder::where("model_type", TempProperty::class)->where("model_id", $property->id)->with("folderMedia")->get();
        $folderMediaFiles = [];
        foreach ($folders as $folder) {
            foreach ($folder->folderMedia ?? [] as $folderMedia) {
                $type = $slug = $folderMedia->slug;
                if ($folderMedia->data) {
                    $type = $folderMedia->data["type"];
                }
                if (empty($type)) {
                    $slug = Str::slug($folderMedia->title);
                }
                $folderMediaFiles[] = [
                    "id" => $folderMedia->id,
                    "title" => $folderMedia->title,
                    "slug" => $slug,
                    "path" => $folderMedia->path,
                    "type" => $type,
                ];
            }
        }

        return response([
            "data" => [
                "floor_plan" => $floorPlan,
                "title_deed" => $titleDeed,
                "owner_passport_copy" => $ownerPassportCopy,
                "signed_listing_form" => $signedListingForm,
                "other_documents" => $otherDocuments,
                "folder_media" => $folderMediaFiles,
            ],
        ]);
    }

    public function usersTree(Request $request)
    {
        $notAdmin = auth()->user()->hasRole(["Sales Director", "Line Manager", "Agent"]);
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
            $users = User::with(["roles:id,name"])->whereIn("id", $childrenIds)->where("status", true)->get(["id", "name", "parent_id", "thumbnail"]);
        } else {
            $users = User::with(["roles:id,name"])->where("status", true)->whereHas('roles')->get(["id", "name", "parent_id", "thumbnail"]);
        }
        $data = [];
        foreach ($users ?? [] as $user) {
            $pid = $user->parent_id;
            $roles = collect($user->roles)->implode("name", ", ");
            $role = [
                "Admin",
                "Sales",
                "Director",
            ];
            $data[] = [
                "id" => $user->id,
                "name" => $user->name,
//                "parent_id" => $pid,
                "pid" => $pid,
                "img" => $user->thumb_url,
                "roles" => $roles,
                "Allocated Portals" => "",
                "Allocated Sub Communities" => "",
//                "tags" => [$role[rand(0,2)]],
            ];
        }

        return response([
            "data" => $data,
        ]);
    }

    public function isValueUnique(Request $request)
    {
        $request->validate([
            "value" => "required",
            "column" => "required",
            "model" => "required",
            "id" => "nullable",
            "agent_id" => "nullable",
        ]);

        try {
            $model = "App\\Models\\" . ucfirst(strtolower($request->model));
            if ($request->model == "Contact") {
                return response([
                    "data" => !$model::when($request->id, function ($q) {
                        $q->where("id", "<>", \request("id"));
                    })->where($request->column, $request->value)->exists(),
                    "existing_data" => $model::when($request->id, function ($q) {
                        $q->where("id", "<>", \request("id"));
                    })->where($request->column, $request->value)->first(),
                    "id" => $model::where($request->column, $request->value)->first()->id ?? null,
                ]);
            }
            if ($request->model = "User" && $request->column == "mobile") {
                return response([
                    "data" => !$model::when($request->id, function ($q) {
                        $q->where("id", "<>", \request("id"));
                    })->when($request->agent_id, function ($q) {
                        $q->where("agent_id", \request("agent_id"));
                    })->where($request->column, $request->value)->where('status', 1)->exists(),
                    "id" => $model::where($request->column, $request->value)->first()->id ?? null,
                ]);
            }
            return response([
                "data" => !$model::when($request->id, function ($q) {
                    $q->where("id", "<>", \request("id"));
                })->when($request->agent_id, function ($q) {
                    $q->where("agent_id", \request("agent_id"));
                })->where($request->column, $request->value)->exists(),
                "id" => $model::where($request->column, $request->value)->first()->id ?? null,
            ]);
        } catch (\Exception $exception) {
            return response([
                "data" => false,
            ]);
        }
    }

    public function leadNotes(Request $request)
    {
        $request->validate([
            "lead_id" => "required|exists:leads,id",
        ]);
        $data = LeadNote::when($request->lead_id, function ($q) {
            $q->where("lead_id", \request("lead_id"));
        })->with(["createdBy"])->get();

        return response([
            "data" => $data,
        ]);
    }

    public function renderLeadNotes(Request $request)
    {
        $request->validate([
            "lead_id" => "required|exists:leads,id",
        ]);
        $data = LeadNote::with('lead')->when($request->lead_id, function ($q) {
            $q->where("lead_id", \request("lead_id"));
        })->with(["createdBy"])->get();

        // dd($data);
        return view('admin.lead.include.partials.notes._notes_log')
            ->with('notes', $data);
    }

    public function contactNotes(Request $request)
    {
        $request->validate([
            "contact_id" => "required|exists:contacts,id",
        ]);
        $data = ContactNote::when($request->contact_id, function ($q) {
            $q->where("contact_id", \request("contact_id"));
        })->with(["createdBy"])->get();

        return response([
            "data" => $data,
        ]);
    }

    public function listingLeadNotes(Request $request)
    {
        $request->validate([
            "listing_lead_id" => "required|exists:listing_leads,id",
        ]);
        $data = ListingLeadNote::when($request->listing_lead_id, function ($q) {
            $q->where("listing_lead_id", \request("listing_lead_id"));
        })->with(["createdBy"])->get();

        return response([
            "data" => $data,
        ]);
    }

    public function dealNotes(Request $request)
    {
        $request->validate([
            "deal_id" => "required|exists:deals,id",
        ]);
        $data = DealNote::when($request->deal_id, function ($q) {
            $q->where("deal_id", \request("deal_id"));
        })->with(["createdBy"])->get();

        return response([
            "data" => $data,
        ]);
    }

    public function usersForRole(Request $request)
    {
        $request->validate([
            "for" => "required|string",
        ]);
        $notAdmin = auth()->user()->hasRole(["Sales Director", "Line Manager", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }
        $data = User::when($request->for == 5, function ($q) {
            $q->whereHas("roles", function ($q) {
                $q->whereIn("name", ["CEO", "Head of Secondary", "Sales Director", "Line Manager", "Team Leader"]);
            });
        })->when($request->for == 3, function ($q) {
            $q->whereHas("roles", function ($q) {
                $q->where("name", "Head of Secondary");
            });
        })->when($request->for == 4 || $request->for == 6, function ($q) {
            $q->whereHas("roles", function ($q) {
                $q->where("name", "Sales Director");
            });
        })->when($notAdmin, fn($q) => $q->whereIn("parent_id", $childrenIds))->get();

        return response([
            "data" => $data,
        ]);
    }

    public function leadSubStatuses(Request $request)
    {
        $data = LeadSubStatus::when($request->lead_status_id, function ($q) {
            $q->where("lead_status_id", \request("lead_status_id"));
        })->orWhere("id", 8)->get();

        return response([
            "data" => $data,
        ]);
    }

    public function dealSubStatuses(Request $request)
    {
        $data = DealSubStatus::when($request->deal_status_id, function ($q) {
            $q->where("deal_status_id", \request("deal_status_id"));
        })->orWhere("id", 8)->get();

        return response([
            "data" => $data,
        ]);
    }

    public function states(Request $request)
    {
        $data = State::when($request->country_id, function ($q) {
            $q->where("country_id", \request("country_id"));
        })->where("status", true)->get();

        return response([
            "data" => $data,
        ]);
    }

    public function activityLog(Request $request)
    {
        $notAdmin = auth()->user()->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }
        $request->validate([
            "subject_model" => "nullable",
            "subject_id" => "nullable",
            "causer_id" => "nullable",
        ]);
        $subjectModel = "App\\Models\\" . ucfirst(strtolower($request->subject_model));

        $data = Activity::when($request->subject_id, function ($q) {
            $q->where("subject_id", \request("subject_id"));
        })->when($request->subject_model, function ($q) use ($subjectModel) {
            $q->where("subject_type", $subjectModel);
        })->when($request->causer_id, function ($q) {
            $q->where("causer_id", \request("causer_id"));
        })->when($request->subject_model == 'Property', function ($q) use ($notAdmin, $childrenIds) {
            $property = Property::find(\request("subject_id"));
            if ($property && $property->agent_id && !(in_array($property->agent_id, $childrenIds)) && $notAdmin) {
                $q->where('subject_type', 'no_existent');
            } else {
                $q->orWhere('properties->property_id', \request("subject_id"));
            }

        })->when($request->subject_model == 'Lead', function ($q) {
            $q->orWhere('properties->lead_id', \request("subject_id"));
        })->when($request->subject_model == 'Contact', function ($q) use ($notAdmin, $childrenIds) {
            $q->when($notAdmin, function ($q) use ($childrenIds) {
                $q->where('causer_type', 'App\Models\User')->whereIn('causer_id', $childrenIds);
            })
                ->orWhere('properties->contact_id', \request("subject_id"));
        })->latest()->take(10)->with(["subject", "causer"])->orderBy('created_at', 'desc')->get();

        if ($request->subject_model == "lead") {
            $data = $data->map(function ($item) {
                $item->subject->load(["agent", "leadStatus", "leadSubStatus"]);
                return $item;
            });
        }

        return response([
            "data" => $data,
        ]);
    }

    public function roleActivityLog(Request $request)
    {
        $notAdmin = auth()->user()->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }
        $request->validate([
            "subject_model" => "nullable",
            "subject_id" => "nullable",
            "causer_id" => "nullable",
        ]);

        $subjectModel = "Spatie\\Permission\\Models\\" . ucfirst(strtolower($request->subject_model));

        $data = Activity::when($request->subject_id, function ($q) {
            $q->where("subject_id", \request("subject_id"));
        })->when($request->subject_model, function ($q) use ($subjectModel) {
            $q->where("subject_type", $subjectModel);
        })->when($request->causer_id, function ($q) {
            $q->where("causer_id", \request("causer_id"));
        })->latest()->take(10)->with(["subject", "causer"])->orderBy('created_at', 'desc')->get();

        if ($request->subject_model == "lead") {
            $data = $data->map(function ($item) {
                $item->subject->load(["agent", "leadStatus", "leadSubStatus"]);
                return $item;
            });
        }
        return response([
            "data" => $data,
        ]);
    }
    public function eventCalendar(Request $request)
    {
        $startDate = Carbon::parse($request->start)->format('Y-m-d H:i:s');
        $endDate = Carbon::parse($request->end)->format('Y-m-d H:i:s');

        $notAdmin = auth()->user()->hasRole(["Sales Director", "Line Manager", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }
        $events = Event::where("start_time", ">=", $startDate)
            ->where("end_time", "<=", $endDate)
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
                $q->orWhereHas("attendees", fn($q1) => $q1->whereIn("user_id", $childrenIds));
            })
            ->when($request->agents, function ($q) {
                $agents = explode(",", \request("agents"));
                $q->whereIn("agent_id", $agents);
                $q->orWhereHas("attendees", fn($q1) => $q1->whereIn("user_id", $agents));
            })
            ->when($request->type, function ($q) {
                $type = explode(",", \request("type"));
                $q->whereIn("type", $type);
            })
            ->get();

        $data = [];
        foreach ($events as $event) {
            $data[] = [
                'title' => $event->title,
                'start' => $event->start_time,
                'id' => $event->id,
                'end' => $event->end_time,
                'className' => eventTitleBadgeColor($event->type),
            ];
        }

        return response($data);
    }

    public function jobNotes(Request $request)
    {
        $request->validate([
            "job_id" => "required|exists:jobs,id",
        ]);
        $data = JobNote::when($request->job_id, function ($q) {
            $q->where("job_id", \request("job_id"));
        })->with(["createdBy"])->get();

        return response([
            "data" => $data,
        ]);
    }

    public function userCommunityProperties(Request $request)
    {
        $subCommunityIds = SubCommunityUser::when($request->user_id, function ($q) {
            $q->where("user_id", \request("user_id"));
        })->pluck("sub_community_id")->all();
        $data = CommunityProperty::whereIn("sub_community_id", $subCommunityIds)->with("subCommunity")->get();
        return response([
            "data" => $data,
        ]);
    }

    public function checkDuplicateListing(Request $request)
    {

        $request->validate([
            "for" => "required|string",
            "unit_no" => "required|string",
            "location_id" => "required",
            "location_type" => "required",
        ]);

        //dd($request->all());

        $property = Property::with('agent')->
            where('unit_no', $request->unit_no)
            ->where('for', $request->for)
            ->wherenot('id', $request->id)
            ->where($request->input('location_type') . "_id", $request->location_id)
            ->first();

        // dd($property);

        return response([
            "data" => $property,
        ]);
    }

    public function checkSimilarListing(Request $request)
    {

        $request->validate([
            "for" => "required|string",
            "unit_no" => "required|string",
            "location_id" => "required",
            "location_type" => "required",
        ]);

        //dd($request->all());

        $property = Property::where('unit_no', $request->unit_no)->where('for', $request->for)
            ->where($request->input('location_type') . "_id", $request->location_id)
            ->first();

        // dd($property);
        if ($property) {
            return response(['status' => 1, 'data' => $property, 'message' => 'Duplicate Listing Found with ref no ' . $property->ref_no]);

        }
        return response(['status' => 0, 'data' => null, 'message' => 'No Duplicate Listing Found']);

    }

    public function externalListingLeadDuplicateCheck(Request $request)
    {
        $request->validate([
            'location_id' => 'required',
            'location_type' => 'required',
            'unit_no' => 'required',
            'mobile' => 'required',
        ]);

        $lastSevenDigits = substr($request->mobile, -7);

        $property = Property::where('unit_no', $request->unit_no)
            ->where('for', $request->for)
            ->where($request->input('location_type') . "_id", $request->location_id)
            ->where('for', $request->for)
            ->whereHas('owner', function ($query) use ($request) {
                $lastSevenDigits = substr($request->mobile, -7);
                $query->whereRaw("RIGHT(mobile, 7) = ?", [$lastSevenDigits]);
            })
            ->first();

        if ($property) {
            return response(['status' => 1, 'data' => $property, 'message' => 'Record already Exists.']);
        }

        $listingLead = ListingLead::
            when($request->unit_no, function ($q) {
            $q->where("unit_no", \request("unit_no"));
        })
            ->where($request->input('location_type') . "_id", $request->location_id)
            ->whereJsonContains('for', $request->for)
            ->whereRaw("RIGHT(mobile, 7) = ?", [$lastSevenDigits])
            ->first();
        if ($listingLead) {
            return response(['status' => 1, 'data' => $listingLead, 'message' => 'Record already Exists.']);
        }
        return response(['status' => 0, 'data' => null, 'message' => 'No Duplicate Listing Found']);
    }

    public function userSubCommunityListingLeads(Request $request)
    {
        $subCommunities = [];
        // $subCommunities = SubCommunity::when($request->user_id, function ($q){
        //     $q->whereHas("users", fn($q1) => $q1->where("user_id", \request("user_id")));
        // })->with("communityProperties")->get();
        if ($request->property_id != null && $request->property_id != 'undefined') {
            $property = ListingLead::where('id', $request->property_id)->first();
            $subCommunities = SubCommunity::where('id', $property->sub_community_id)->with("communityProperties")->get();
            // $subCommunities = collect($subCommunities)->merge($property_sub_community);
        }
        return response([
            "data" => $subCommunities,
        ]);

    }
    public function userSubCommunityProperties(Request $request)
    {
        $subCommunities = [];
        // $subCommunities = SubCommunity::when($request->user_id, function ($q){
        //     $q->whereHas("users", fn($q1) => $q1->where("user_id", \request("user_id")));
        // })->with("communityProperties")->get();
        if ($request->property_id != null && $request->property_id != 'undefined') {
            $property = Property::where('id', $request->property_id)->first();
            $subCommunities = SubCommunity::where('id', $property->sub_community_id)->with("communityProperties")->get();
            // $subCommunities = collect($subCommunities)->merge($property_sub_community);
        }
        return response([
            "data" => $subCommunities,
        ]);

    }
    public function subCommunityListingLeadsSelect2(Request $request)
    {

        $term = isset($request->term) ? $request->term : '';
        if ($term == '' && $request->id != 'undefined' && $request->id != '') {
            $property = ListingLead::where('id', $request->id)->first();
            if (!empty($property)) {
                $id = $property->community_id || $property->sub_community_id;
                $subCommunities = SubCommunity::where('id', $property->sub_community_id)->with("communityProperties")->get();
                $properties = CommunityProperty::where('sub_community_id', $property->sub_community_id)->limit(30)->get();
            } else {
                return response([
                    "data" => [],
                ]);
            }
        } else {

            $subCommunities = SubCommunity::where(\DB::raw('lower(name)'), 'like', '%' . strtolower($term) . '%')
                ->orWhere(\DB::raw('lower(alternate_name)'), 'like', '%' . strtolower($term) . '%')
                ->orWhere(function ($q) use ($term) {
                    $q->orWhere(\DB::raw('lower(path_name)'), 'like', '%' . strtolower($term) . '%')
                        ->orWhere(\DB::raw('lower(alternate_path_name)'), 'like', '%' . strtolower($term) . '%');
                })
            //->orderBy('sub_communities.path_name','ASC')
                ->with("communityProperties")->limit('30')->get();

            $properties = CommunityProperty::where(\DB::raw('lower(name)'), 'like', '%' . strtolower($term) . '%')
                ->orWhere(\DB::raw('lower(alternate_name)'), 'like', '%' . strtolower($term) . '%')
                ->orWhere(function ($q) use ($term) {
                    $q->orWhere(\DB::raw('lower(path_name)'), 'like', '%' . strtolower($term) . '%')
                        ->orWhere(\DB::raw('lower(alternate_path_name)'), 'like', '%' . strtolower($term) . '%');
                })->limit('30')->get();

        }

        $data = [];
        $i = 0;
        foreach ($subCommunities as $subc) {

            $data[$i]['text'] = $subc->name . ($subc?->alternate_name ? " [ " . $subc?->alternate_name . " ]" : null);
            $data[$i]['id'] = $subc->id;
            $data[$i]['lat'] = $subc->latitude;
            $data[$i]['long'] = $subc->longitude;
            $data[$i]['path'] = $subc->path_name;
            $data[$i]['alternate_path_name'] = $subc->alternate_path_name;
            $data[$i]['type'] = 'sub_community';
            $i++;

        }
        $data2 = [];
        $i = 0;
        if (isset($properties)) {
            foreach ($properties as $property) {
                $data2[$i]['text'] = $property->name . ($property?->alternate_name ? " [ " . $property?->alternate_name . " ]" : null);
                $data2[$i]['id'] = $property->id;
                $data2[$i]['lat'] = $property->latitude;
                $data2[$i]['long'] = $property->longitude;
                $data2[$i]['path'] = $property->path_name;
                $data2[$i]['alternate_path_name'] = $property->alternate_path_name;
                $data2[$i]['type'] = 'community_property';
                $i++;
            }
        }

        $datas = array_merge($data, $data2);
        if (!empty($datas)) {
            return response([
                "data" => $datas,
                "subCommunities" => $subCommunities,
            ]);
        } else {
            return response([
                "data" => [],
            ]);
        }

    }
    public function subCommunityPropertiesSelect2(Request $request)
    {
        $term = isset($request->term) ? $request->term : '';
        if ($term == '' && $request->id != 'undefined' && $request->id != '') {
            $property = Property::where('id', $request->id)->first();
            if (!empty($property)) {
                $id = $property->community_id || $property->sub_community_id;
                $subCommunities = SubCommunity::where('id', $property->sub_community_id)->with("communityProperties")->get();
                $properties = CommunityProperty::where('sub_community_id', $property->sub_community_id)->limit(30)->get();
            } else {
                return response([
                    "data" => [],
                ]);
            }
        } else {

            //Old Code Uncomment if any issue

            // $subCommunities = SubCommunity::where(\DB::raw('lower(name)'), 'like', '%' . strtolower($term) . '%')
            //     ->orWhere(\DB::raw('lower(alternate_name)'), 'like', '%' . strtolower($term) . '%')
            //     ->orWhere(function ($q) use ($term) {
            //         $q->orWhere(\DB::raw('lower(path_name)'), 'like', '%' . strtolower($term) . '%')
            //             ->orWhere(\DB::raw('lower(alternate_path_name)'), 'like', '%' . strtolower($term) . '%');
            //     })
            //     ->with("communityProperties")->limit('30')->get();


            // New code remove if any issue
            $subCommunities = SubCommunity::where(function($q) use($term){
                $q->where(\DB::raw('lower(name)'), 'like', '%' . strtolower($term) . '%')
                    ->orWhere(\DB::raw('lower(alternate_name)'), 'like', '%' . strtolower($term) . '%')
                    ->orWhere(function ($q) use ($term) {
                        $q->orWhere(\DB::raw('lower(path_name)'), 'like', '%' . strtolower($term) . '%')
                        ->orWhere(\DB::raw('lower(alternate_path_name)'), 'like', '%' . strtolower($term) . '%');
                    });
                })
                ->where('status', 1)
                ->with("communityProperties")->limit('30')->get();

            // Ends here

            $properties = CommunityProperty::where(\DB::raw('lower(name)'), 'like', '%' . strtolower($term) . '%')
                ->orWhere(\DB::raw('lower(alternate_name)'), 'like', '%' . strtolower($term) . '%')
                ->orWhere(function ($q) use ($term) {
                    $q->orWhere(\DB::raw('lower(path_name)'), 'like', '%' . strtolower($term) . '%')
                        ->orWhere(\DB::raw('lower(alternate_path_name)'), 'like', '%' . strtolower($term) . '%');
                })->limit('30')->get();

        }

        $data = [];
        $i = 0;
        foreach ($subCommunities as $subc) {

            $data[$i]['text'] = $subc->name . ($subc?->alternate_name ? " [ " . $subc?->alternate_name . " ]" : null);
            $data[$i]['id'] = $subc->id;
            $data[$i]['lat'] = $subc->latitude;
            $data[$i]['long'] = $subc->longitude;
            $data[$i]['path'] = $subc->path_name;
            $data[$i]['alternate_path_name'] = $subc->alternate_path_name;
            $data[$i]['type'] = 'sub_community';
            $i++;

        }
        $data2 = [];
        $i = 0;
        if (isset($properties)) {
            foreach ($properties as $property) {
                $data2[$i]['text'] = $property->name . ($property?->alternate_name ? " [ " . $property?->alternate_name . " ]" : null);
                $data2[$i]['id'] = $property->id;
                $data2[$i]['lat'] = $property->latitude;
                $data2[$i]['long'] = $property->longitude;
                $data2[$i]['path'] = $property->path_name;
                $data2[$i]['alternate_path_name'] = $property->alternate_path_name;
                $data2[$i]['type'] = 'community_property';
                $i++;
            }
        }

        $datas = array_merge($data, $data2);
        if (!empty($datas)) {
            return response([
                "data" => $datas,
                "subCommunities" => $subCommunities,
            ]);
        } else {
            return response([
                "data" => [],
            ]);
        }

    }

    public function agentPropertySelect2(Request $request)
    {
        $notAdmin = auth()->user()->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }
        $properties = Property::when($request->property_id, function ($q) use ($request) {
            $q->orWhere('id', $request->property_id);
        })
            ->when($request->term, function ($q) use ($request) {
                $q->where(function ($q) use ($request) {
                    $q->where('ref_no', 'LIKE', '%' . $request->term . '%')
                        ->orWhere('unit_no', 'LIKE', '%' . $request->term . '%')
                        ->orWhereHas('subCommunity', function ($q) use ($request) {
                            $q->where('name', 'LIKE', '%' . $request->term . '%');
                        })
                        ->orWhereHas('communityProperty', function ($q) use ($request) {
                            $q->where('name', 'LIKE', '%' . $request->term . '%');
                        })
                    ;

                });
            })
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn('agent_id', $childrenIds);
            })
            ->with('owner:id,first_name,last_name,email,mobile')
            ->latest()
            ->limit(10)
            ->get();
        return response([
            "data" => $properties,
        ]);
    }
    public function agentContactSelect2(Request $request)
    {
        $notAdmin = auth()->user()->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }
        $contacts = Contact::when($request->contact_id, function ($q) use ($request) {
            $q->orWhere('id', $request->contact_id);
        })
            ->when($request->term, function ($q) use ($request) {
                $q->where(function ($q) use ($request) {
                    $q->where('mobile', 'LIKE', '%' . $request->term . '%')
                        ->orWhere('first_name', 'LIKE', '%' . $request->term . '%')
                        ->orWhere('last_name', 'LIKE', '%' . $request->term . '%');
                });
            })
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn('agent_id', $childrenIds);
            })
            ->latest()
            ->limit(10)
            ->get();
        return response([
            "data" => $contacts,
        ]);
    }
    public function agentLeadSelect2(Request $request)
    {
        $notAdmin = auth()->user()->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }
        $leads = Lead::when($request->lead_id, function ($q) use ($request) {
            $q->orWhere('id', $request->lead_id);
        })
            ->when($request->term, function ($q) use ($request) {
                $q->where(function ($q) use ($request) {
                    $q->where('mobile', 'LIKE', '%' . $request->term . '%')
                        ->orWhere('first_name', 'LIKE', '%' . $request->term . '%')
                        ->orWhere('last_name', 'LIKE', '%' . $request->term . '%');
                });
            })
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn('agent_id', $childrenIds);
            })
            ->latest()
            ->limit(10)
            ->get();
        return response([
            "data" => $leads,
        ]);
    }
    public function getCommunitiesSelect2(Request $request)
    {
        $term = isset($request->term) ? $request->term : '';
        $communities = Community::where(\DB::raw('lower(name)'), 'like', '%' . strtolower($term) . '%')
            ->orWhere(function ($q) use ($term) {
                $q->orWhere(\DB::raw('lower(path_name)'), 'like', '%' . strtolower($term) . '%');
            })->limit('30')->select('id', 'name', 'path_name', 'alternate_path_name')->get();
        return response([
            "data" => $communities,
        ]);
    }

    public function getSubCommunityPropertiesByCommunitiesSelect2(Request $request)
    {
        $q = $request->q;
        if (!empty($q)) {
            $term = isset($q['term']) ? $q['term'] : '';
        }
        $communities = isset($request->communities) ? $request->communities : [];

        $subCommunities = SubCommunity::where(function ($q) use ($term) {
            $q->where(\DB::raw('lower(name)'), 'like', '%' . strtolower($term) . '%')
                ->orWhere(\DB::raw('lower(path_name)'), 'like', '%' . strtolower($term) . '%');
        })->where(function ($q) use ($communities) {
            if (count($communities) > 0) {
                $q->orWhereIn('community_id', $communities);
            } else {
                return false;
            }
        })->with("communityProperties")->limit('40')->get();
        $properties = CommunityProperty::where(function ($q) use ($term) {
            $q->orWhere(\DB::raw('lower(path_name)'), 'like', '%' . strtolower($term) . '%')
                ->where(\DB::raw('lower(name)'), 'like', '%' . strtolower($term) . '%');
        })->where(function ($q) use ($communities) {
            if (count($communities) > 0) {
                $q->orWhereIn('community_id', $communities);
            } else {
                return false;
            }
        })->limit('40')->get();
        $data = [];
        $i = 0;
        foreach ($subCommunities as $subc) {
            if(!empty($subc->alternate_name)){
                $alter_name = '[ '.$subc->alternate_name .' ]';
            }else{
                $alter_name = '';
            }
            $name = $subc->path_name ?? $subc->name;
            $data[$i]['text'] = $name .' '. $alter_name;
            $data[$i]['id'] = $subc->id;
            $data[$i]['lat'] = $subc->latitude;
            $data[$i]['long'] = $subc->longitude;
            $data[$i]['path'] = $subc->path_name;
            $data[$i]['type'] = 'sub_community';
            $i++;

        }
        $data2 = [];
        $i = 0;
        if (isset($properties)) {
            foreach ($properties as $property) {
                if(!empty($property->alternate_name)){
                    $alter_name = '[ '.$property->alternate_name .' ]';
                }else{
                    $alter_name = '';
                }
                $name = $property->path_name ?? $property->name;

                $data2[$i]['text'] = $name .' '. $alter_name;
                $data2[$i]['id'] = $property->id;
                $data2[$i]['lat'] = $property->latitude;
                $data2[$i]['long'] = $property->longitude;
                $data2[$i]['path'] = $property->path_name;
                $data2[$i]['type'] = 'community_property';
                $i++;
            }
        }

        $datas = array_merge($data, $data2);
        if (!empty($datas)) {
            return response([
                "data" => $datas,
            ]);
        } else {
            return response([
                "data" => [],
            ]);
        }

    }
    public function contactsSelect2(Request $request)
    {
        $user_id = ($request->has('agent_id') && $request->agent_id != '') ? $request->agent_id : auth()->id();
        if ($user_id != '') {
            $user = User::find($user_id);
        } else {
            $user = auth()->user();
        }
        $notAdmin = $user->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            //$childrenIds = getUserChildrenIds($user_id);
            $childrenIds = getUserChildrenIdsOnly($user_id);
            $childrenIds[] = $user_id;
        }

        $data = Contact::when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->when($request->contact_type_id, function ($q) {
            $q->whereHas("contactTypes", fn($q1) => $q1->where("id", \request("contact_type_id")));
        })->when($request->search, function ($q) {
            $q->where(function ($q2) {
                $q2->where("first_name", "LIKE", "%" . \request("search") . "%")
                    ->orWhere("last_name", "LIKE", "%" . \request("search") . "%")
                    ->orWhere("mobile", "LIKE", "%" . \request("search") . "%")
                    ->orWhere("email", "LIKE", "%" . \request("search") . "%");
            });

        })->where('status', true)->select('id', 'first_name', 'last_name', 'mobile', 'email')->orderBy('updated_at', 'DESC')->limit('30')->get();

        return response([
            "data" => $data,
        ]);
    }
    public function leadsSelect2(Request $request)
    {
        $user_id = ($request->has('agent_id') && $request->agent_id != '') ? $request->agent_id : auth()->id();
        if ($user_id != '') {
            $user = User::find($user_id);
        } else {
            $user = auth()->user();
        }
        $notAdmin = $user->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            //$childrenIds = getUserChildrenIds($user_id);
            $childrenIds = getUserChildrenIdsOnly($user_id);
            $childrenIds[] = $user_id;
        }

        $data = Lead::when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->when($request->search, function ($q) {
            $q->where(function ($q2) {
                $q2->where("first_name", "LIKE", "%" . \request("search") . "%")
                    ->orWhere("last_name", "LIKE", "%" . \request("search") . "%")
                    ->orWhere("mobile", "LIKE", "%" . \request("search") . "%")
                    ->orWhere("email", "LIKE", "%" . \request("search") . "%");
            });
        })->where('status', true)
            ->select('id', 'agent_id', 'first_name', 'last_name', 'mobile', 'email', 'source_id', 'created_at')
            ->orderBy('updated_at', 'DESC')
            ->limit('30')->with('leadSource:id,name')->get();

        return response([
            "data" => $data,
        ]);
    }
    public function propertySelect2(Request $request)
    {
        $user_id = ($request->has('agent_id') && $request->agent_id != '') ? $request->agent_id : auth()->id();
        // dd($user_id);
        if ($user_id != '') {
            $user = User::find($user_id);
            $dates = $request->dates;
        } else {
            $user = auth()->user();
        }
        // return json_encode($user);
        $notAdmin = $user->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds($user_id);
        }

        $data = Property::when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->when($request->term, function ($q) use ($request) {
            $term = isset($request->term) ? $request->term : '';
            $q->where(function ($qry) use ($term) {
                $qry->where(\DB::raw('lower(ref_no)'), 'like', '%' . strtolower($term) . '%')
                ->orWhere(\DB::raw('lower(title)'), 'like', '%' . strtolower($term) . '%')
                ->orWhere(\DB::raw('lower(unit_no)'), 'like', '%' . strtolower($term) . '%');
            });
        })->limit('20')->orderBy('updated_at', 'DESC')->with('bed', 'price')->get();
        //->whereNotIn('publish_status', ['ARCHIVE'])
        return response([
            "data" => $data,
        ]);
    }
    public function userPropertySelect2(Request $request)
    { // this will give the list of properties only of passed agent_id
        $user_id = ($request->has('agent_id') && $request->agent_id != '') ? $request->agent_id : auth()->id();
        if ($user_id != '') {
            $user = User::find($user_id);
        } else {
            $user = auth()->user();
        }
        $data = Property::when($request->term, function ($q) use ($request) {
            $term = isset($request->term) ? $request->term : '';
            $q->where(function ($qry) use ($term) {
                $qry->where(\DB::raw('lower(ref_no)'), 'like', '%' . strtolower($term) . '%')
                    ->orWhere(\DB::raw('lower(title)'), 'like', '%' . strtolower($term) . '%');
            });
        })->limit('20')->orderBy('updated_at', 'DESC')->with('bed', 'bath', 'price')->get();
        //->whereNotIn('publish_status', ['ARCHIVE'])
        return response([
            "data" => $data,
        ]);
    }
    public function dealPropertySelect2(Request $request)
    {
        $for = $request->for ?? '';

        $notAdmin = auth()->user()->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }
       // return json_encode($childrenIds);
        $data1 = Property::when($request->agent_id, function ($q) {
            $q->where("agent_id", \request("agent_id"))->whereNotNull("agent_id");
        })->when($request->search, function ($q) {
            $q->where(function ($q) {
                $q->where("ref_no", "LIKE", "%" . \request("search") . "%")
                    ->orWhere("title", "LIKE", "%" . \request("search") . "%");
            });
        })->when($for, function ($q) use ($for) {
            if ($for == 'Offplan Resale') {
                $q->whereIn('for', ['Residential for sale', 'Commercial for sale'])
                ->whereIn('completion_status', ['Ready Primary', 'Off-Plan Primary']);
            } elseif ($for == 'Secondary Sale') {
                $q->whereIn('for', ['Residential for sale', 'Commercial for sale']);
            } else {
                $q->whereIn('for', ['Residential for rent', 'Commercial for rent']);
            }
        })->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds)->orWhereIn("external_agent_id", $childrenIds);
        })->where('publish_status', '!=', 'ARCHIVED')->with('bed', 'bath', 'price')->get();
        //->whereIn('publish_status', ['PUBLISHED', "POCKET"])
        if ($request->agent_id && $request->property_id) {
            $property = Property::when($request->agent_id, function ($q) {
                $q->where("agent_id", \request("agent_id"))->orWhere("external_agent_id", \request("agent_id"));
            })->where('id', $request->property_id)->with('bed', 'bath', 'price')->first();
            if ($property) {
                $data1->push($property);
            }
        }
//        $data2 = Property::when($request->agent_id, function ($q) {
//            $q->where("agent_id", \request("agent_id"))->whereNotNull("agent_id");
//        })->when($request->search, function ($q) {
//            $q->where(function ($q) {
//                $q->where("ref_no", "LIKE", "%" . \request("search") . "%")
//                    ->orWhere("title", "LIKE", "%" . \request("search") . "%");
//            });
//        })->when($for, function ($q) use($for){
//            if($for == 'Primary Resale') {
//                $q->whereIn('for', ['Residential for sale', 'Commercial for sale'])->whereIn('completion_status',['Ready Primary','Off-Plan Primary']);
//            }elseif($for == 'Secondary Sale') {
//                $q->whereIn('for', ['Residential for sale', 'Commercial for sale']);
//            }else{
//                $q->whereIn('for', ['Residential for rent', 'Commercial for rent']);
//            }
//        })->when($notAdmin, function ($q) use ($childrenIds) {
//            $q->whereIn("agent_id", $childrenIds)->orWhereIn("external_agent_id", $childrenIds);
//        })->where('publish_status', '!=', 'ARCHIVED')->with('bed', 'bath', 'price')->limit('20')->get();

        return response([
            "data" => $data1,
        ]);
    }
    public function getAgents(Request $request)
    {
        $userAgents = [];
        $allAgents = User::whereHas("roles", fn($q) => $q->whereIn("name", ["Sales Director", "Line Manager", "Agent"]))->with("roles:id,name")->get(["id", "name", "email"]);
        foreach ($allAgents as $agent) {
            if (!empty($agent->roles[0]->name)) {
                $userAgents[$agent->roles[0]->name][] = [
                    "id" => $agent->id,
                    "name" => $agent->name,
                ];
            }
        }
        return response([
            "data" => $allAgents,
        ]);
    }
    public function getUsersSelect2(Request $request)
    {
        $term = isset($request->term) ? $request->term : '';

        $users = User::where(function ($q) use ($term) {
            $q->where(\DB::raw('lower(name)'), 'like', '%' . strtolower($term) . '%')
                ->orWhere(function ($q) use ($term) {
                    $q->where(\DB::raw('lower(first_name)'), 'like', '%' . strtolower($term) . '%')
                        ->orWhere(\DB::raw('lower(last_name)'), 'like', '%' . strtolower($term) . '%');
                })
                ->orWhere(\DB::raw('lower(email)'), 'like', '%' . strtolower($term) . '%');
        })
            ->when($request->agent_id, function ($q) {
                $q->where("id", '!=', \request("agent_id"));
            })
            ->when($request->roles, function ($q) use ($request) {
                $q->whereHas("roles", fn($q1) => $q1->whereIn("name", $request->roles));
            })
            ->where('status', true)
            ->orderBy('users.name', 'ASC')
            ->limit(30)
            ->get();

        $data = [];
        $i = 0;
        foreach ($users as $user) {
            //$roles = !empty($user->roles) ? $user->roles[0]?->name : null;
            $data[$i]['text'] = $user->first_name . ' ' . $user->last_name;
            $data[$i]['id'] = $user->id;
            $data[$i]['image'] = $user?->image_url ?? null;
            $data[$i]['mobile'] = $user?->mobile ?? null;
            $data[$i]['email'] = $user?->email ?? null;
            $i++;

        }
        return response([
            "data" => $data,
        ]);
    }
    public function getAgentsSelect2(Request $request)
    {
        $term = isset($request->term) ? $request->term : '';

        $users = User::where(function ($q) use ($term) {
            $q->where(\DB::raw('lower(name)'), 'like', '%' . strtolower($term) . '%')
                ->orWhere(function ($q) use ($term) {
                    $q->where(\DB::raw('lower(first_name)'), 'like', '%' . strtolower($term) . '%')
                        ->orWhere(\DB::raw('lower(last_name)'), 'like', '%' . strtolower($term) . '%');
                })
                ->orWhere(\DB::raw('lower(email)'), 'like', '%' . strtolower($term) . '%');
        })
            ->when($request->agent_id, function ($q) {
                $q->where("id", '!=', \request("agent_id"));
            })
            ->whereHas('roles', function ($q) {
                $q->whereIn('name', ["Agent", "Team Leader", "Line Manager", "Sales Director", "Head of Secondary"]);
            })
            ->orderBy('users.name', 'ASC')
            ->limit(30)
            ->get();

        $data = [];
        $i = 0;
        foreach ($users as $user) {
            //$roles = !empty($user->roles) ? $user->roles[0]?->name : null;
            $data[$i]['text'] = $user->first_name . ' ' . $user->last_name;
            $data[$i]['id'] = $user->id;
            $data[$i]['image'] = $user?->image_url;
            $i++;

        }
        return response([
            "data" => $data,
        ]);
    }
    public function getUsersByParentSelect2(Request $request)
    {
        $q = $request->q;
        if (!empty($q)) {
            $term = isset($q['term']) ? $q['term'] : '';
        }
        $parent = isset($request->parent) ? $request->parent : [];

        $users = User::where(\DB::raw('lower(name)'), 'like', '%' . strtolower($term) . '%')
            ->where(function ($q) use ($term) {
                $q->orWhere(\DB::raw('lower(first_name)'), 'like', '%' . strtolower($term) . '%')
                    ->orWhere(\DB::raw('lower(last_name)'), 'like', '%' . strtolower($term) . '%')
                    ->orWhere(\DB::raw('lower(email)'), 'like', '%' . strtolower($term) . '%');
            })->whereIn('parent_id', $parent)->orderBy('users.name', 'ASC')->limit('30')->get();
        $data = [];
        $i = 0;
        foreach ($users as $user) {
            //$roles = !empty($user->roles) ? $user->roles[0]?->name : null;
            $data[$i]['text'] = $user->first_name . ' ' . $user->last_name;
            $data[$i]['id'] = $user->id;
            $i++;

        }
        return response([
            "data" => $data,
        ]);
    }
    public function changeUserPassword(Request $request)
    {

        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => "min:6|required_with:confirm_password|same:confirm_password",
        ]);
        if (!(Hash::check($request->get('current_password'), Auth::user()->password))) {
            return response()->json([
                'status' => 0,
                'msg' => "Your Current password does not matches with the password you provided. Please try again.",
            ], 201); // Status code
        } else {
            $user = User::find(Auth::user()->id);
            $user->password = Hash::make($request->get('password'));
            $user->update();
            if ($user) {

                $result = array('status' => '1', 'msg' => 'Password changed successfully');

            } else {
                $result = array('status' => '0', 'msg' => 'Something went wrong');
            }
            return json_encode($result);
        }
    }

    public function propertyCriteria(Request $request)
    {
        // dd($request->all());
        if ($request->id) {
            $property = Property::find($request->id);
            if ($property) {
                return getPropertyCriteria($property->ref_no);
            }
        }

    }

    public function getCommunityPropertiesBySubCommunitiesSelect2(Request $request)
    {
        // dd($request->all());
        $q = $request->q;
        if (!empty($q)) {
            $term = isset($q['term']) ? $q['term'] : '';
        }
        $subCommunities = isset($request->subCommunities) ? $request->subCommunities : [];

        $properties = CommunityProperty::where(function ($q) use ($term) {
            $q->where(\DB::raw('lower(name)'), 'like', '%' . strtolower($term) . '%')
                ->orWhere(\DB::raw('lower(path_name)'), 'like', '%' . strtolower($term) . '%');
        })->where(function ($q) use ($subCommunities) {
            if (count($subCommunities) > 0) {
                $q->orWhereIn('sub_community_id', $subCommunities);
            } else {
                return false;
            }
        })->limit('40')->get();

        // $properties = CommunityProperty::where(function ($q) use ($term) {
        //     $q->orWhere(\DB::raw('lower(path_name)'), 'like', '%' . strtolower($term) . '%')
        //         ->where(\DB::raw('lower(name)'), 'like', '%' . strtolower($term) . '%');
        // })->where(function ($q) use ($subCommunities) {
        //     if (count($subCommunities) > 0) {
        //         $q->orWhereIn('community_id', $subCommunities);
        //     } else {
        //         return false;
        //     }
        // })->limit('40')->get();
        $data = [];
        $i = 0;
        foreach ($subCommunities as $subc) {

            $data[$i]['text'] = $subc->path_name ?? $subc->name;
            $data[$i]['id'] = $subc->id;
            $data[$i]['lat'] = $subc->latitude;
            $data[$i]['long'] = $subc->longitude;
            $data[$i]['path'] = $subc->path_name;
            $data[$i]['type'] = 'sub_community';
            $i++;

        }
        $data2 = [];
        $i = 0;
        if (isset($properties)) {
            foreach ($properties as $property) {
                $data2[$i]['text'] = $property->path_name ?? $property->name;
                $data2[$i]['id'] = $property->id;
                $data2[$i]['lat'] = $property->latitude;
                $data2[$i]['long'] = $property->longitude;
                $data2[$i]['path'] = $property->path_name;
                $data2[$i]['type'] = 'community_property';
                $i++;
            }
        }

        $datas = array_merge($data, $data2);
        if (!empty($datas)) {
            return response([
                "data" => $datas,
            ]);
        } else {
            return response([
                "data" => [],
            ]);
        }

    }

    public function getUsersCommunity(Request $request)
    {
        $q = $request->q;
        if (!empty($q)) {
            $term = isset($q['term']) ? $q['term'] : '';
        }
        $communities = isset($request->communities) ? $request->communities : [];

        $subCommunities = SubCommunity::where(function ($q) use ($term) {
            $q->where(\DB::raw('lower(name)'), 'like', '%' . strtolower($term) . '%')
                ->orWhere(\DB::raw('lower(path_name)'), 'like', '%' . strtolower($term) . '%');
        })->get();

        $communities = Community::where(function ($q) use ($term) {
            $q->orWhere(\DB::raw('lower(path_name)'), 'like', '%' . strtolower($term) . '%')
                ->where(\DB::raw('lower(name)'), 'like', '%' . strtolower($term) . '%');
        })->get();
        $data = [];
        $i = 0;
        foreach ($subCommunities as $subc) {

            $data[$i]['text'] = $subc->path_name ?? $subc->name;
            $data[$i]['id'] = $subc->id;
            $data[$i]['lat'] = $subc->latitude;
            $data[$i]['long'] = $subc->longitude;
            $data[$i]['path'] = $subc->path_name;
            $data[$i]['type'] = 'sub_community';
            $i++;

        }
        $data2 = [];
        $i = 0;
        if (isset($communities)) {
            foreach ($communities as $property) {
                $data2[$i]['text'] = $property->path_name ?? $property->name;
                $data2[$i]['id'] = $property->id;
                $data2[$i]['lat'] = $property->latitude;
                $data2[$i]['long'] = $property->longitude;
                $data2[$i]['path'] = $property->path_name;
                $data2[$i]['type'] = 'community';
                $i++;
            }
        }

        $datas = array_merge($data2, $data);
        if (!empty($datas)) {
            return response([
                "data" => $datas,
            ]);
        } else {
            return response([
                "data" => [],
            ]);
        }

    }

    public function globalSearch(Request $request)
    {
        $request->validate([
            "search_keywords" => "required|string",
            "search_modal" => "nullable|string",
        ]);

        // dd($request->all());
        if ($request->search_keywords) {

            $searchKeyWord = $request->search_keywords;
            $modal = $request->search_modal ?? 'Null';

            $superAdmin = auth()->user()->hasRole(['Super Admin']);
            $notAdmin = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
            $childrenIds = [];
            if ($notAdmin) {
                $childrenIds = getUserChildrenIds(auth()->id());
            }

            switch ($modal) {
                case 'User':
                    $users = User::when($searchKeyWord != '', function ($query) use ($searchKeyWord) {
                        $query->where(function ($query) use ($searchKeyWord) {
                            $query->where(function ($query) use ($searchKeyWord) {
                                $query->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $searchKeyWord . '%']);
                            });
                            if (is_numeric($searchKeyWord)) {
                                $query->orWhere('mobile', 'LIKE', '%' . $searchKeyWord . '%');
                                $query->orWhereRaw("REPLACE(`mobile`, ' ', '') LIKE ?", ['%' . trimSpaces(validatePhoneNumber($searchKeyWord)) . '%']);

                            }

                            // ->orWhere('mobile', 'LIKE', '%' . $searchKeyWord . '%')
                            // ->orWhereRaw("REPLACE(`mobile`, ' ', '') LIKE ?", ['%' . trimSpaces(validatePhoneNumber($searchKeyWord)) . '%'])

                            $query->orWhere('email', 'LIKE', '%' . $searchKeyWord . '%');

                        });
                    });
                    $usersCount = $users->count();
                    $users = $users->take(10)->get();
                    return view('admin.layouts.components.user')
                        ->with('users', $users)
                        ->with('usersCount', $usersCount);
                case 'Property':
                    $properties = Property::whereNot('publish_status', 'ARCHIVED')->when($searchKeyWord != '', function ($query) use ($searchKeyWord) {
                        $query->where(function ($query) use ($searchKeyWord) {
                            $query->where('ref_no', 'LIKE', '%' . $searchKeyWord . '%')
                                ->orWhere('unit_no', 'LIKE', '%' . $searchKeyWord . '%')
                                ->orWhereHas('subCommunity', function ($query) use ($searchKeyWord) {
                                    $query->where('name', 'LIKE', '%' . $searchKeyWord . '%');
                                })->orWhereHas('community', function ($query) use ($searchKeyWord) {
                                $query->where('name', 'LIKE', '%' . $searchKeyWord . '%');
                            })->orWhereHas('propertyCategory', function ($query) use ($searchKeyWord) {
                                $query->where('title', 'LIKE', '%' . $searchKeyWord . '%');
                            })->orWhereHas('agent', function ($query) use ($searchKeyWord) {
                                $query->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $searchKeyWord . '%']);
                            });
                        });
                    })->when($notAdmin, fn($query) => $query->whereIn("agent_id", $childrenIds));
                    $propertiesCount = $properties->count();
                    $properties = $properties->take(10)->get();
                    return view('admin.layouts.components.property')
                        ->with('properties', $properties)
                        ->with('propertiesCount', $propertiesCount);
                case 'Lead':
                    $leads = Lead::when($searchKeyWord != '', function ($query) use ($searchKeyWord) {
                        $query->where(function ($query) use ($searchKeyWord) {

                            if (is_numeric($searchKeyWord)) {
                                $query->orWhere('mobile', 'LIKE', '%' . $searchKeyWord . '%');
                                $query->orWhereRaw("REPLACE(`mobile`, ' ', '') LIKE ?", ['%' . trimSpaces(validatePhoneNumber($searchKeyWord)) . '%']);

                            }

                            $query->where('first_name', 'LIKE', '%' . $searchKeyWord . '%')
                                ->orWhere('last_name', 'LIKE', '%' . $searchKeyWord . '%')
                                ->orWhere(function ($query) use ($searchKeyWord) {
                                    $query->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $searchKeyWord . '%']);
                                })
                                ->orWhere('id', 'LIKE', '%' . $searchKeyWord . '%')
                                ->orWhere('email', 'LIKE', '%' . $searchKeyWord . '%')
                                ->orWhere(function ($query) use ($searchKeyWord) {
                                    $query->orWhereHas('leadSource', function ($query) use ($searchKeyWord) {
                                        $query->where('name', 'LIKE', '%' . $searchKeyWord . '%');
                                    })->orWhereHas('leadMedium', function ($query) use ($searchKeyWord) {
                                        $query->where('name', 'LIKE', '%' . $searchKeyWord . '%');
                                    })->orWhereHas('leadType', function ($query) use ($searchKeyWord) {
                                        $query->where('name', 'LIKE', '%' . $searchKeyWord . '%');
                                    });
                                });

                        });
                    })->when($notAdmin, fn($query) => $query->whereIn("agent_id", $childrenIds));
                    $leadsCount = $leads->count();
                    $leads = $leads->take(10)->get();
                    return view('admin.layouts.components.leads')
                        ->with('leads', $leads)
                        ->with('leadsCount', $leadsCount);
                case 'Contact':
                    $contacts = Contact::when($searchKeyWord != '', function ($query) use ($searchKeyWord) {
                        $query->where(function ($query) use ($searchKeyWord) {

                            if (is_numeric($searchKeyWord)) {
                                $query->orWhere('mobile', 'LIKE', '%' . $searchKeyWord . '%');
                                $query->orWhereRaw("REPLACE(`mobile`, ' ', '') LIKE ?", ['%' . trimSpaces(validatePhoneNumber($searchKeyWord)) . '%']);

                            }

                            $query->where('my_crm_id', 'LIKE', '%' . $searchKeyWord . '%')
                                ->orWhere(function ($query) use ($searchKeyWord) {
                                    $query->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $searchKeyWord . '%']);
                                })
                            // ->orWhere('mobile', 'LIKE', '%' . $searchKeyWord . '%')
                            // ->orWhereRaw("REPLACE(`mobile`, ' ', '') LIKE ?", ['%' . trimSpaces(validatePhoneNumber($searchKeyWord)) . '%'])

                                ->orWhere('email', 'LIKE', '%' . $searchKeyWord . '%')
                                ->orWhere('id', 'LIKE', '%' . $searchKeyWord . '%');
                        });
                    })->when($notAdmin, fn($query) => $query->whereIn("agent_id", $childrenIds));
                    $contactsCount = $contacts->count();
                    $contacts = $contacts->take(10)->get();
                    return view('admin.layouts.components.contacts')
                        ->with('contacts', $contacts)
                        ->with('contactsCount', $contactsCount);

                case 'Listing Leads':
                    $listingleads = ListingLead::when($searchKeyWord != '', function ($query) use ($searchKeyWord) {
                        $query->where(function ($query) use ($searchKeyWord) {

                            if (is_numeric($searchKeyWord)) {
                                $query->orWhere('mobile', 'LIKE', '%' . $searchKeyWord . '%');
                                $query->orWhereRaw("REPLACE(`mobile`, ' ', '') LIKE ?", ['%' . trimSpaces(validatePhoneNumber($searchKeyWord)) . '%']);

                            }

                            $query->where('bitrix_id', 'LIKE', '%' . $searchKeyWord . '%')
                                ->orWhere(function ($query) use ($searchKeyWord) {
                                    $query->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $searchKeyWord . '%']);
                                })

                            // ->orWhereRaw("REPLACE(`mobile`, ' ', '') LIKE ?", ['%' . trimSpaces(validatePhoneNumber($searchKeyWord)) . '%'])
                                ->orWhere('email', 'LIKE', '%' . $searchKeyWord . '%')
                                ->orWhere('unit_no', 'LIKE', '%' . $searchKeyWord . '%')
                                ->orWhere('id', 'LIKE', '%' . $searchKeyWord . '%')
                                ->orWhere(function ($query) use ($searchKeyWord) {
                                    $query->orWhereHas('source', function ($query) use ($searchKeyWord) {
                                        $query->where('name', 'LIKE', '%' . $searchKeyWord . '%');
                                    })->orWhereHas('medium', function ($query) use ($searchKeyWord) {
                                        $query->where('name', 'LIKE', '%' . $searchKeyWord . '%');
                                    })->orWhereHas('type', function ($query) use ($searchKeyWord) {
                                        $query->where('name', 'LIKE', '%' . $searchKeyWord . '%');
                                    });
                                });

                        });
                    })->when($notAdmin, fn($query) => $query->whereIn("agent_id", $childrenIds));
                    $listingleadsCount = $listingleads->count();
                    $listingleads = $listingleads->take(10)->get();
                    return view('admin.layouts.components.listingleads')
                        ->with('listingleads', $listingleads)
                        ->with('listingleadsCount', $listingleadsCount);

                default:
                    $users = User::when($searchKeyWord != '', function ($query) use ($searchKeyWord) {
                        $query->where(function ($query) use ($searchKeyWord) {
                            $query->where(function ($query) use ($searchKeyWord) {
                                $query->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $searchKeyWord . '%']);
                            });
                            if (is_numeric($searchKeyWord)) {
                                $query->orWhere('mobile', 'LIKE', '%' . $searchKeyWord . '%');
                                $query->orWhereRaw("REPLACE(`mobile`, ' ', '') LIKE ?", ['%' . trimSpaces(validatePhoneNumber($searchKeyWord)) . '%']);

                            }

                            // ->orWhere('mobile', 'LIKE', '%' . $searchKeyWord . '%')
                            // ->orWhereRaw("REPLACE(`mobile`, ' ', '') LIKE ?", ['%' . trimSpaces(validatePhoneNumber($searchKeyWord)) . '%'])

                            $query->orWhere('email', 'LIKE', '%' . $searchKeyWord . '%');

                        });
                    });

                    $usersCount = $users->count();
                    $users = $users->take(10)->get();

                    $properties = Property::whereNot('publish_status', 'ARCHIVED')->when($searchKeyWord != '', function ($query) use ($searchKeyWord) {
                        $query->where(function ($query) use ($searchKeyWord) {
                            $query->where('ref_no', 'LIKE', '%' . $searchKeyWord . '%')
                                ->orWhere('unit_no', 'LIKE', '%' . $searchKeyWord . '%')
                                ->orWhereHas('subCommunity', function ($query) use ($searchKeyWord) {
                                    $query->where('name', 'LIKE', '%' . $searchKeyWord . '%');
                                })->orWhereHas('community', function ($query) use ($searchKeyWord) {
                                $query->where('name', 'LIKE', '%' . $searchKeyWord . '%');
                            })->orWhereHas('propertyCategory', function ($query) use ($searchKeyWord) {
                                $query->where('title', 'LIKE', '%' . $searchKeyWord . '%');
                            })->orWhereHas('agent', function ($query) use ($searchKeyWord) {
                                $query->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $searchKeyWord . '%']);
                            });
                        });
                    })->when($notAdmin, fn($query) => $query->whereIn("agent_id", $childrenIds));

                    $propertiesCount = $properties->count();
                    $properties = $properties->take(10)->get();

                    $leads = Lead::when($searchKeyWord != '', function ($query) use ($searchKeyWord) {
                        $query->where(function ($query) use ($searchKeyWord) {

                            if (is_numeric($searchKeyWord)) {
                                $query->orWhere('mobile', 'LIKE', '%' . $searchKeyWord . '%');
                                $query->orWhereRaw("REPLACE(`mobile`, ' ', '') LIKE ?", ['%' . trimSpaces(validatePhoneNumber($searchKeyWord)) . '%']);

                            }

                            $query->where('first_name', 'LIKE', '%' . $searchKeyWord . '%')
                                ->orWhere('last_name', 'LIKE', '%' . $searchKeyWord . '%')
                                ->orWhere(function ($query) use ($searchKeyWord) {
                                    $query->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $searchKeyWord . '%']);
                                })
                                ->orWhere('id', 'LIKE', '%' . $searchKeyWord . '%')
                                ->orWhere('email', 'LIKE', '%' . $searchKeyWord . '%')
                                ->orWhere(function ($query) use ($searchKeyWord) {
                                    $query->orWhereHas('leadSource', function ($query) use ($searchKeyWord) {
                                        $query->where('name', 'LIKE', '%' . $searchKeyWord . '%');
                                    })->orWhereHas('leadMedium', function ($query) use ($searchKeyWord) {
                                        $query->where('name', 'LIKE', '%' . $searchKeyWord . '%');
                                    })->orWhereHas('leadType', function ($query) use ($searchKeyWord) {
                                        $query->where('name', 'LIKE', '%' . $searchKeyWord . '%');
                                    });
                                });

                        });
                    })->when($notAdmin, fn($query) => $query->whereIn("agent_id", $childrenIds));

                    $leadsCount = $leads->count();
                    $leads = $leads->take(10)->get();

                    $contacts = Contact::when($searchKeyWord != '', function ($query) use ($searchKeyWord) {
                        $query->where(function ($query) use ($searchKeyWord) {

                            if (is_numeric($searchKeyWord)) {
                                $query->orWhere('mobile', 'LIKE', '%' . $searchKeyWord . '%');
                                $query->orWhereRaw("REPLACE(`mobile`, ' ', '') LIKE ?", ['%' . trimSpaces(validatePhoneNumber($searchKeyWord)) . '%']);

                            }

                            $query->where('my_crm_id', 'LIKE', '%' . $searchKeyWord . '%')
                                ->orWhere(function ($query) use ($searchKeyWord) {
                                    $query->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $searchKeyWord . '%']);
                                })
                            // ->orWhere('mobile', 'LIKE', '%' . $searchKeyWord . '%')
                            // ->orWhereRaw("REPLACE(`mobile`, ' ', '') LIKE ?", ['%' . trimSpaces(validatePhoneNumber($searchKeyWord)) . '%'])

                                ->orWhere('email', 'LIKE', '%' . $searchKeyWord . '%')
                                ->orWhere('id', 'LIKE', '%' . $searchKeyWord . '%');
                        });
                    })->when($notAdmin, fn($query) => $query->whereIn("agent_id", $childrenIds));
                    $contactsCount = $contacts->count();
                    $contacts = $contacts->take(10)->get();

                    $listingleads = ListingLead::when($searchKeyWord != '', function ($query) use ($searchKeyWord) {
                        $query->where(function ($query) use ($searchKeyWord) {

                            if (is_numeric($searchKeyWord)) {
                                $query->orWhere('mobile', 'LIKE', '%' . $searchKeyWord . '%');
                                $query->orWhereRaw("REPLACE(`mobile`, ' ', '') LIKE ?", ['%' . trimSpaces(validatePhoneNumber($searchKeyWord)) . '%']);

                            }

                            $query->where('bitrix_id', 'LIKE', '%' . $searchKeyWord . '%')
                                ->orWhere(function ($query) use ($searchKeyWord) {
                                    $query->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $searchKeyWord . '%']);
                                })

                            // ->orWhereRaw("REPLACE(`mobile`, ' ', '') LIKE ?", ['%' . trimSpaces(validatePhoneNumber($searchKeyWord)) . '%'])
                                ->orWhere('email', 'LIKE', '%' . $searchKeyWord . '%')
                                ->orWhere('unit_no', 'LIKE', '%' . $searchKeyWord . '%')
                                ->orWhere('id', 'LIKE', '%' . $searchKeyWord . '%')
                                ->orWhere(function ($query) use ($searchKeyWord) {
                                    $query->orWhereHas('source', function ($query) use ($searchKeyWord) {
                                        $query->where('name', 'LIKE', '%' . $searchKeyWord . '%');
                                    })->orWhereHas('medium', function ($query) use ($searchKeyWord) {
                                        $query->where('name', 'LIKE', '%' . $searchKeyWord . '%');
                                    })->orWhereHas('type', function ($query) use ($searchKeyWord) {
                                        $query->where('name', 'LIKE', '%' . $searchKeyWord . '%');
                                    });
                                });

                        });
                    })->when($notAdmin, fn($query) => $query->whereIn("agent_id", $childrenIds));
                    $listingleadsCount = $listingleads->count();
                    $listingleads = $listingleads->take(10)->get();

                    return view('admin.layouts.components.default')
                        ->with('users', $users)
                        ->with('usersCount', $usersCount)
                        ->with('properties', $properties)
                        ->with('propertiesCount', $propertiesCount)
                        ->with('leads', $leads)
                        ->with('leadsCount', $leadsCount)
                        ->with('contacts', $contacts)
                        ->with('contactsCount', $contactsCount)
                        ->with('listingleads', $listingleads)
                        ->with('listingleadsCount', $listingleadsCount);
            }
        }
    }

    public function getAgentsBasedOnStatus(Request $request)
    {
        $notAdmin = auth()->user()->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent", "Head of Secondary"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        if ($request->all_agents == 'true') {
            $query = User::whereHas("roles", fn($q) => $q->whereIn("name", ["Sales Director", "Line Manager", "Team Leader", "Agent", "Head of Secondary"]));
            if ($request->my_team == 'true') {
                $agents = $query->whereIn("parent_id", $childrenIds);
            }
            $agents = $query->where('status', 0)->orderBy('first_name', 'asc')->get();

        } else {
            $query = User::whereHas("roles", fn($q) => $q->whereIn("name", ["Sales Director", "Line Manager", "Team Leader", "Agent", "Head of Secondary"]));

            if ($request->my_team == 'true') {
                $agents = $query->whereIn("parent_id", $childrenIds);
            }
            $agents = $query->where('status', 1)->orderBy('first_name', 'asc')->get();

        }
        return response([
            "data" => $agents,
        ]);

    }

    public function getAllAgents(Request $request)
    {

        $notAdmin = auth()->user()->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        $query = User::whereHas("roles", fn($q) => $q->whereIn("name", ["Sales Director", "Line Manager", "Team Leader", "Agent"]));
        // $query = $query->whereIn('status',[0,1])->orderBy('first_name', 'asc')->get();
        if ($request->inactive_agent == 'false') {
            $query = $query->where('status', 1);
        }
        // $query = $query->whereIn("parent_id", $childrenIds);
        $query = $query->get();

        return response([
            "data" => $query,
        ]);

    }

    public function userAutoComplete(Request $request)
    {
        $data = [];

        if ($request->filled('q')) {
            $searchKeyWord = $request->q;
            $data = User::where(DB::raw("concat(first_name, ' ', COALESCE(last_name, ''))"), 'LIKE', "%" . $searchKeyWord . "%")->where('status', true)
                ->limit(10)
                ->get();
        } else {
            $data = User::where('status', true)->latest()->limit(10)->get();
        }

        return response()->json($data);
    }

    public function propertyAutocomplete(Request $request)
    {
        $data = [];

        if ($request->filled('q')) {

            $data = Property::where('title', 'LIKE', '%' . $request->get('q') . '%')
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
            $data = Property::with('bed')->where('publish_status', 'PUBLISHED')->latest()->limit(10)->get();
        }

        return response()->json($data);
    }
    public function leadsAutocomplete(Request $request)
    {
        $data = [];

        if ($request->filled('q')) {

            $data = Lead::where('title', 'LIKE', '%' . $request->get('q') . '%')
                ->orWhere('id', 'LIKE', '%' . $request->get('q') . '%')
                ->orWhereRaw("CONCAT(first_name, ' ', COALESCE(last_name, '')) LIKE ?", ['%' . $request->get('q') . '%'])
                ->orWhere('mobile', 'LIKE', '%' . $request->get('q') . '%')
                ->where('lead_status_id', '!=', 9)
                ->where('is_archive', false)
                ->with('leadStatus')
                ->limit(10)
                ->get();

        } else {
            $data = Lead::where('lead_status_id', '!=', 9)->where('is_archive', false)
                ->with('leadStatus')
                ->latest()->limit(10)->get();
        }

        return response()->json($data);
    }

    public function getCommunitiesByRegion(Request $request)
    {
        $data = [];
        // dd();
        if ($request->has('q') || $request->has('regions')) {
            $query = Community::where('status', true);

            if ($request->has('regions')) {
                $query->whereIn('region_id', $request->regions);
            }

            if (isset($request->q['term'])) {
                $query->where(function ($q) use ($request) {
                    $q->where('name', 'LIKE', '%' . $request->q['term'] . '%')
                        ->orWhere('path_name', 'LIKE', '%' . $request->q['term'] . '%');
                });
            }

            $data = $query->get();

        } else {
            $data = Community::where('status', true)->get();
        }

        return response()->json($data);
    }
    public function updateUserLastActivity(?int $userId = null): void
    { //update user activity in a minute only not everytime this function is called
        try {
            $userIdToUpdate = $userId ?? auth()->id();

            if (!$userIdToUpdate) {
                return; // No user to update
            }
            $sessionKey = 'last_activity_updated_' . $userIdToUpdate;
            $lastUpdated = Session::get($sessionKey);
            $now = Carbon::now();
            // Check if an update hasn't happened in the last minute
            if (!$lastUpdated || $now->diffInMinutes(Carbon::parse($lastUpdated)) >= 1) {
                // Find or create the UserLastActivity record
                UserLastActivity::updateOrCreate(
                    ['user_id' => $userIdToUpdate],
                    ['last_active' => $now]
                );
                // Store the current timestamp in the session
                Session::put($sessionKey, $now);
            }
        }catch (\Exception $e){
            info('error while recording user activity '.json_encode($e->getMessage()));
        }
    }
    public function agentDailyReport(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
        ]);
$this->updateUserLastActivity(auth()->id());
        $chineseTeam = array(
            333,
            4,
            319,
            36,
            205,
            21,
            38,
            58,
            40,
            39,
            446,
            64,
            167,
            634,
            660,
            286,
            105,
            213,
            238,
            268,
            32,
            275,
            1024,
        );

        $checkRole = auth()->user()->hasRole(['Agent']);
        // check if user_id is in chinese team

        if ($checkRole && !in_array($request->user_id, $chineseTeam)) {
            $user = $request->user_id;
            $leads = Lead::where('agent_id', $user)
                ->where('is_archive', 0)
                ->whereNull('converted_to_listing_lead')
                ->where('lead_status_id', 1)
                ->where('status', 1)
                ->whereDate('created_at', '!=', Carbon::today())
                ->get();
            $old_msg = "You have <b>" . $leads->count() . "</b> leads in <b>'New'</b> status created in past days. Act on them within 3 hours (by 12:30 PM) to avoid account suspension in";
            $msg = "You have <b>" . $leads->count() . "</b> leads in <b>'New'</b> status created in past days. Act on them by 12:30 PM every day to avoid account suspension, effective from Oct 27th, 2023.";
            if ($leads->count() > 4) {
                return response()->json([
                    'status' => 1,
                    'msg' => $old_msg,

                ], 201);
            } else {
                return response()->json([
                    'status' => 0,
                    'msg' => 'You have ' . $leads->count() . ' leads to follow up today',
                ], 201);
            }
        } else {
            return response()->json([
                'status' => 0,
                'msg' => 'You are not a agent or in chinese team',
            ], 201);
        }

    }

    public function generateDescriptionWithChatGpt(Request $request)
    {
        $description = $request->description;
        $project_name = $request->project_name;
        $developer = $request->developer_name;

        $apiKey = env('CHAT_API');
        $apiUrl = 'https://api.openai.com/v1/chat/completions';
        $message = $description . 'Project name is' . $project_name . ' by developer ' . $developer . '. Rewrite the following description for property listing for AX CAPITAL company. Do not use any symbol or special character. Just you can use is | if required generate about 750 to 2000 characters.';
        $response = Http::timeout(120)->withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->post($apiUrl, [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                ['role' => 'user', 'content' => $message],
            ],
        ]);
        $responseData = $response->json();
        $msg = $responseData['choices'][0]['message']['content'];

        $developer = DeveloperProject::where('id', $request->dev_id)->first();
        $developer->description = $msg;
        $developer->save();

        return $msg;
    }

    public function generateTitleWithChatGpt(Request $request)
    {
        $keywords = $request->keywords;
        $total = $request->total_titles;
        $dev_id = $request->dev_id;

        GeneratePropertyTitleJob::dispatch($keywords, $total, $dev_id);

        return response()->json([
            'status' => 1,
            'msg' => 'Your Titles will be ready in few minutes',
        ], 200);
    }

    public function loadAmenities(Request $request)
    {
        $amenities = PropertyAmenity::where('status', 1)->get();
        $project = DeveloperProject::where('id', $request->dev_id)->first();
        return view('admin.offplan.partials._amenities')
            ->with('amenities', $amenities)
            ->with('project', $project);
    }

    public function saveAmenities(Request $request)
    {
        $amenities = $request->amenities;
        $dev_id = $request->dev_id;
        $developer = DeveloperProject::where('id', $dev_id)->first();

        $developer->project_amenities = json_encode($amenities);
        $developer->save();

        return response()->json([
            'status' => 1,
            'msg' => 'Amenities saved successfully',
        ], 200);
    }

    public function checkDuplicateLead(Request $request)
    {
        $request->validate([
            'mobile' => 'required',
            'agent_id' => 'required',
        ]);

        $lead = Lead::where('mobile', $request->mobile)->where('agent_id', $request->agent_id)->first();

        if ($lead) {
            return response()->json([
                'status' => 1,
                'msg' => 'Lead already exist',
            ], 200);
        } else {
            return response()->json([
                'status' => 0,
                'msg' => 'Lead not exist',
            ], 200);

        }
    }

    public function updatePermission(Request $request)
    {
        // dd($request->all());
        $user = User::find($request->user_id);
        $key = $request->key;
        $value = $request->value;
        if ($value == 'true') {
            $value = 1;
        } else {
            $value = 0;
        }

        $user->update([$key => $value]);

        return response()->json([
            'status' => 1,
            'msg' => 'Record updated successfully',
        ], 200);
    }

    public function bulkReraCheck(Request $request)
    {
        //    $properties = Property::where('is_archived',false)->where('state','APPROVED')->whereNotNull('rera_permit_number')->where('publish_status','PUBLISHED')->where('is_live',true)->get();
        //    dd($properties);
        // foreach($properties as $property){
        try {
            $property = Property::find($request->id);
            $header = array();
            $header[] = 'Content-Type: application/json';
            $header[] = 'Content-Length: 4000';
            $header[] = 'Connection: keep-alive';

            $response = Http::post('https://secondary.axcrm.ae/api/testing/rera/checkPermit', [
                'permit_number' => $property->rera_permit_number,
                'for' => $property->for,
                'api_token' => 'fYlQO814toCEdmdp',
            ]);
            $res = $response;
            if (empty($res)) {
                $response = Http::post('https://secondary.axcrm.ae/api/testing/rera/checkPermitWithoutQr', [
                    'permit_number' => $property->rera_permit_number,
                    'for' => $property->for,
                    'api_token' => 'fYlQO814toCEdmdp',
                ]);
                $res = $response->json();
            }
            info($res);
            // if(!empty($res)) {
            //     $qr = $res['qr'] ?? '';
            //     if($qr !=''){
            //         $url = $this->base64ToImage($qr);
            //         $property->qr_link = $url ?? null;
            //     }
            //     $property->rera_issue_date = date('Y-m-d',strtotime($res['start_date']));
            //     $property->rera_expiry_date = date('Y-m-d',strtotime($res['end_date']));
            //     $property->rera_validation_url = $res['validation_url'] ?? null;
            //     $property->withoutApproval()->save();
            //     $res['qr_link'] = $property->qr_link ?? null;
            //     return response()->json(['status'=>'1','message' => 'Property RERA Check Successfully', 'data' => $res], 200);
            // }else{
            //     Property::where('id', $property->id)->update(['unable_to_generate_qr' => 1]);
            //     return response()->json(['status'=>'0','message' => 'Listing not found at RERA', 'data' => $res], 200);
            // }
        } catch (\Exception $e) {
            return response()->json(['status' => '0', 'message' => 'Something went wrong!!', 'error' => $e->getMessage(), 'error_line' => $e->getLine()], 500);
        }
        info('------');
        // break;
        // }

    }

    public function getCommunities(Request $request)
    {
        $query = $request->get('q');
        $data = Community::where('name', 'like', '%' . $query . '%')
            ->orWhere('alternate_name', 'like', '%' . $query . '%')
            ->orWhere('path_name', 'like', '%' . $query . '%')
            ->orWhere('latitude', 'like', '%' . $query . '%')
            ->orWhere('longitude', 'like', '%' . $query . '%')
            ->get();
        return response()->json($data);
    }

    public function getSubCommunities(Request $request)
    {

        $query = $request->get('q');
        $data = SubCommunity::where(function ($q) use ($request) {
            $q->whereIn('community_id', [$request->community_id]);
        })
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%')
                    ->orWhere('alternate_name', 'like', '%' . $query . '%')
                    ->orWhere('path_name', 'like', '%' . $query . '%')
                    ->orWhere('latitude', 'like', '%' . $query . '%')
                    ->orWhere('longitude', 'like', '%' . $query . '%');
            })
            ->get();
        return response()->json($data);
    }

    public function getDeletedDocuments(Request $request)
    {
        $data = PropertyMedia::where('property_id', $request->property_id)->where('type', 'Other')->onlyTrashed()->get();
        return view('admin.listing.includes.partials.deleted_documents')
            ->with('data', $data);

    }

    public function updatePropertyDocument(Request $request)
    {

        $property_media = PropertyMedia::where('property_id', $request->property_id)->where('title', $request->document_name)->first();
        if ($property_media) {
            $property_media->delete();

            $propertyMedia = PropertyMedia::where('id', $request->document_id)->onlyTrashed()->first();
            $propertyMedia->restore();

            $propertyMedia->title = $request->document_name;
            $propertyMedia->save();

        } else {
            $propertyMedia = PropertyMedia::where('id', $request->document_id)->onlyTrashed()->first();
            $propertyMedia->restore();
        }

        return response()->json(['status' => 1, 'msg' => 'Document restored successfully'], 200);
    }

    public function getRepublishRequests(Request $request)
    {
        $data = RepublishRequest::where('property_id', $request->id)->where('status', 'REPUBLISH_REQUESTED')->count();

        if ($data > 0) {
            if (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin - Abu Dhabi')) {
                return response()->json(['status' => 1, 'msg' => 'Republish request already exist'], 200);
            }
            return response()->json(['status' => 0, 'msg' => 'Republish request not exist'], 200);

        } else {
            return response()->json(['status' => 0, 'msg' => 'Republish request not exist'], 200);
        }
    }

    public function getRepublishRequestsData(Request $request)
    {
        $data = RepublishRequest::where('property_id', $request->id)->where('status', 'REPUBLISH_REQUESTED')->first();
        $property = Property::where('id', $request->id)->first();

        $pf_level = PropertyMeta::where('property_id', $request->id)->where('meta_key', 'pf_level')->first();
        $bayut_level = PropertyMeta::where('property_id', $request->id)->where('meta_key', 'bayut_level')->first();
        $dubizzle_level = PropertyMeta::where('property_id', $request->id)->where('meta_key', 'dubizzle_level')->first();

        $pf_metas = PropertyMeta::where('property_id', $request->id)->where('meta_key', 'like', 'pf_%')->get();
        $bayut_metas = PropertyMeta::where('property_id', $request->id)->where('meta_key', 'like', 'bayut_%')->get();
        $dubizzle_metas = PropertyMeta::where('property_id', $request->id)->where('meta_key', 'like', 'dubizzle_%')->get();

        return view('admin.listing.includes.partials._republish_request_approval')
            ->with('data', $data)
            ->with('property', $property)
            ->with('pf_level', $pf_level)
            ->with('bayut_level', $bayut_level)
            ->with('dubizzle_level', $dubizzle_level)
            ->with('pf_metas', $pf_metas)
            ->with('bayut_metas', $bayut_metas)
            ->with('dubizzle_metas', $dubizzle_metas);
    }

    public function republishRequestResponse(Request $request)
    {
        $request->validate([
            'issue_date' => 'required',
            'expiry_date' => 'required',
            'status' => 'required',
            'id' => 'required',
            'request_id' => 'required',
        ]);
        $republishRequest = RepublishRequest::where('id', $request->request_id)->first();
        $property = Property::where('id', $republishRequest->property_id)->first();

        if ($request->status == 'REPUBLISH_APPROVED') {

            $old_ref_no = $property->ref_no;
            $property->ref_no = generatePropertyRefNo();
            if ($property->publish_status == 'PUBLISHED') {
                $property->publish_status = 'PUBLISHED';
                $property->is_live = 1;

            } else if ($property->publish_status == 'UNPUBLISHED') {
                $property->publish_status = 'PENDING';
            }
            $property->contract_start = $request->issue_date;
            $property->contract_end = $request->expiry_date;
            $property->withoutApproval()->save();

            $propertyCycle = new PropertyCycle();
            $propertyCycle->property_id = $property->id;
            $propertyCycle->status = $property->publish_status;
            $propertyCycle->ref_no = $property->ref_no;
            $propertyCycle->created_by_id = auth()->id();
            $propertyCycle->save();

            $republishRequest->status = 'REPUBLISH_APPROVED';
            $republishRequest->old_ref_no = $old_ref_no;
            $republishRequest->new_ref_no = $property->ref_no;
            $republishRequest->issue_date = $request->issue_date;
            $republishRequest->expiry_date = $request->expiry_date;
            $republishRequest->admin_id = auth()->id();
            $republishRequest->save();

            $signedFormChanged = null;
            $signedForm = PropertyMedia::where('property_id', $property->id)->where('type', 'Other')->where('title', 'Signed Listing Form')->exists();
            $signedFormChanged = $signedForm ? "Updated" : "Added";

            $signed_form = PropertyMedia::where('property_id', $property->id)->where('type', 'Other')->where('title', 'Signed Listing Form')->first() ?? new PropertyMedia();
            $signed_form->property_id = $property->id;
            $signed_form->type = 'Other';
            $signed_form->password = $request->contract_file_password;
            $signed_form->title = 'Signed Listing Form';
            $signed_form->media_changed = $signedFormChanged;
            $signed_form->path = $republishRequest->file ?? null;
            $signed_form->created_by_id = auth()->id();

            $orignal = $signed_form->getOriginal() ?? [];
            $dirty = $signed_form->getDirty() ?? [];
            $signed_form->save();

            $arr = ['attributes' => $dirty,
                'old' => $orignal, 'property_id' => $property->id];
            activity()
                ->performedOn($signed_form)
                ->causedBy(auth()->user()->id)
                ->withProperties($arr)
                ->event('created')
                ->log(auth()->user()->first_name . ' has uploaded signed listing form');

            $message = "<b>AX Secondary CRM</b>\nYour republish request for property reference number <b>" . $old_ref_no . "</b> have been Approved ✅.\n";
            $message .= "Old Ref No: " . $old_ref_no . "\n";
            $message .= "New Ref No: " . $property->ref_no . "\n";
            $message .= "\nRegards,\n " . auth()->user()->first_name;
            $message .= "\n—————————————————";

            $note = new PropertyNote();
            $note->property_id = $property->id;
            $note->note = $message;
            $note->created_by_id = auth()->user()->id;
            $note->save();

            $telegram = Telegram::bot('notification_bot');
            $telegramMessage = $this->sendTelegramMessage($telegram, $message, $property->agent->telegram_chatid);

            return response()->json(['status' => 1, 'msg' => 'Republish request approved successfully and new ref no is generated', 'id' => $property->id], 200);
        }

        if ($request->status == 'REPUBLISH_REJECTED') {
            $republishRequest->status = 'REPUBLISH_REJECTED';
            $republishRequest->admin_id = auth()->id();
            $republishRequest->note = $request->note;
            $republishRequest->save();

            $message = "<b>AX Secondary CRM</b>\nOops! Your request for property republish  <b>" . $property->ref_no . "</b> have been <b>Refused</b> 🚫. Kindly review the note provided below for reference.\n";
            $message .= "\n<b>Reason: </b> ";
            $message .= "\n" . $request->note;
            $message .= "\n\nRegards,\n" . auth()->user()->first_name;
            $message .= "\n—————————————————";
            $telegram = Telegram::bot('notification_bot');
            $telegramMessage = $this->sendTelegramMessage($telegram, $message, $property->agent->telegram_chatid);

            $note = new PropertyNote();
            $note->property_id = $property->id;
            $note->note = $message;
            $note->created_by_id = auth()->user()->id;
            $note->save();

            return response()->json(['status' => 1, 'msg' => 'Your Republish request has been rejected.', 'id' => $property->id], 200);

        }
        return response()->json(['status' => 0, 'msg' => 'Something went wrong'], 200);

    }

    public function sendTelegramMessage($telegram, $message)
    {

        if (env('APP_ENV') == 'production') {

            $chatids = [
                '1417490671',
                '5970238984',
            ];

            foreach ($chatids as $chatid) {
                try {
                    $telegram->sendMessage([
                        'chat_id' => $chatid,
                        'text' => $message,
                        'parse_mode' => 'html',
                    ]);
                    \Log::info('Telegram message sent to chat_id: ' . $chatid . ' message is: ' . $message);
                } catch (\Throwable $th) {
                    \Log::info('Telegram error log: ' . $th->getMessage() . " chat_id is: " . $chatid . " message is: " . $message);
                    continue;
                }
            }

        } else {

            $chatids = [
                '5970238984',
                // '1417490671',
            ];
            foreach ($chatids as $chatid) {
                try {
                    $telegram->sendMessage([
                        'chat_id' => $chatid,
                        'text' => $message,
                        'parse_mode' => 'html',
                    ]);
                    \Log::info('Telegram message sent to chat_id: ' . $chatid . ' message is: ' . $message);

                } catch (\Throwable $th) {
                    \Log::info('Telegram error log: ' . $th->getMessage() . " chat_id is: " . $chatid . " message is: " . $message);
                    continue;
                }
            }

        }

    }

    public function updatePortalMeta(Request $request)
    {
        $property = Property::where('id', $request->id)->first();
        $ref_no = $property->ref_no;

        if ($request->portal_id == 'Property Finder') {
            $params = new Request();
            $params->replace(['ref_no' => $ref_no]);
            (new ExternalApiController())->fetchPFData($params);

            $metas = PropertyMeta::where('property_id', $request->id)->where('meta_key', 'like', 'pf_%')->get();
            return view('admin.listing.includes.partials._pf_meta_data')
                ->with('metas', $metas)
                ->with('property', $property);
        }
        if ($request->portal_id == 'Bayut') {
            $params = new Request();
            $params->replace(['ref_no' => $ref_no]);
            (new ExternalApiController())->fetchBayutData($params);

            $metas = PropertyMeta::where('property_id', $request->id)->where('meta_key', 'like', 'bayut_%')->get();
            return view('admin.listing.includes.partials._bayut_meta_data')
                ->with('metas', $metas)
                ->with('property', $property);
        }
        return response()->json(['status' => 1, 'msg' => 'Meta updated successfully'], 200);
    }

    public function fetchPortalMetaData(Request $request)
    {

        $property = Property::where('id', $request->id)->first();
        $ref_no = $property->ref_no;
        $params = new Request();
        $params->replace(['ref_no' => $ref_no]);

        (new ExternalApiController())->fetchPFData($params);
        (new ExternalApiController())->fetchBayutData($params);

        $pf_level = PropertyMeta::where('property_id', $request->id)->where('meta_key', 'pf_level')->first();
        $bayut_level = PropertyMeta::where('property_id', $request->id)->where('meta_key', 'bayut_level')->first();
        $dubizzle_level = PropertyMeta::where('property_id', $request->id)->where('meta_key', 'dubizzle_level')->first();

        $pf_metas = PropertyMeta::where('property_id', $request->id)->where('meta_key', 'like', 'pf_%')->get();
        $bayut_metas = PropertyMeta::where('property_id', $request->id)->where('meta_key', 'like', 'bayut_%')->get();
        $dubizzle_metas = PropertyMeta::where('property_id', $request->id)->where('meta_key', 'like', 'dubizzle_%')->get();

        return view('admin.listing.includes.partials._portal_meta_data')
            ->with('pf_level', $pf_level)
            ->with('bayut_level', $bayut_level)
            ->with('dubizzle_level', $dubizzle_level)
            ->with('pf_metas', $pf_metas)
            ->with('bayut_metas', $bayut_metas)
            ->with('dubizzle_metas', $dubizzle_metas);
    }

    public function fetchPortalMetaDataAgent(Request $request)
    {

        $property = Property::where('id', $request->id)->first();

        $pf_level = PropertyMeta::where('property_id', $request->id)->where('meta_key', 'pf_level')->first();
        $bayut_level = PropertyMeta::where('property_id', $request->id)->where('meta_key', 'bayut_level')->first();
        $dubizzle_level = PropertyMeta::where('property_id', $request->id)->where('meta_key', 'dubizzle_level')->first();

        $pf_metas = PropertyMeta::where('property_id', $request->id)->where('meta_key', 'like', 'pf_%')->get();
        $bayut_metas = PropertyMeta::where('property_id', $request->id)->where('meta_key', 'like', 'bayut_%')->get();
        $dubizzle_metas = PropertyMeta::where('property_id', $request->id)->where('meta_key', 'like', 'dubizzle_%')->get();

        return view('admin.listing.includes.partials._portal_meta_data_agent')
            ->with('pf_level', $pf_level)
            ->with('bayut_level', $bayut_level)
            ->with('dubizzle_level', $dubizzle_level)
            ->with('pf_metas', $pf_metas)
            ->with('bayut_metas', $bayut_metas)
            ->with('dubizzle_metas', $dubizzle_metas);
    }

    public function getNoOfDaysListingWasPublished(Request $request)
    {
        $property = Property::where('id', $request->id)->first();
        if (auth()->user()->hasRole('Agent')) {
            if ($property->agent_id != auth()->id()) {
                return response()->json(['status' => 0, 'days' => 0], 200);
            }
        }

        $proeprtyStatus = PropertyCycle::where('property_id', $property->id)->where('status', 'PUBLISHED')->orderBy('id', 'desc')->first();
        if ($proeprtyStatus) {
            $days = $proeprtyStatus->created_at->diffInDays(Carbon::now());
        } else {
            return response()->json(['status' => 1, 'days' => 0], 200);
        }
        return response()->json(['status' => 1, 'days' => $days], 200);
    }

    public function getPermissionToPublish(Request $request)
    {
        $user = User::where('id', $request->agent_id)->first();
        if(!$user){
            return response()->json(['status' => 1,'permission'=>0, 'msg' => 'You have reached your maximum limit of listings'], 200);
        }

        $propertyCount = Property::where('agent_id', $user->id)->where('publish_status', 'PUBLISHED')->count();
        $userAllowedListings = $user->listing_allowed;

        // $userAllowedListings = 5;
        // $propertyCount = 5;

        if($userAllowedListings == null){
            return response()->json(['status' => 0,'permission'=>1, 'msg' => 'You can publish this listing'], 200);
        }
        if($propertyCount < $userAllowedListings){
            return response()->json(['status' => 0,'permission'=>1, 'msg' => 'You can publish this listing'], 200);
        }else{
            return response()->json(['status' => 1,'permission'=>0, 'msg' => 'You have reached your maximum limit of listings'], 200);
        }


    }

    public function checkAgentEligibility(Request $request)
    {
       $agent_id = $request->agent_id;
        $agent = User::where('id', $agent_id)->first();
        if(!$agent){
            return response()->json(['status' => 0, 'msg' => 'Agent not found'], 404);
        }
        if ($agent->license_no !=null){
            return response()->json(['status' => 1, 'msg' => 'This agent is eligible'], 200);
        }
        if ($agent->pp_enabled == 1 || $agent->pp_enabled == '1'){
            return response()->json(['status' => 1,  'msg' => 'This agent is eligible'], 200);
        }
       if(Property::where('agent_id', $agent_id)->where('is_live', 1)->count() > 0){
            return response()->json(['status' => 1,'msg' => 'This agent is eligible'], 200);
        }
            return response()->json(['status' => 0, 'msg' => 'This agent is not eligible for bulk listing transfer. Please contact admin'], 200);


    }
}
