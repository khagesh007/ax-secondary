<?php

namespace App\Http\Controllers\Admin\MyAccount;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Portal;
use App\Models\PortalUser;
use App\Models\Property;
use App\Models\SubCommunity;
use App\Models\UserMeta;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function myProfile()
    {
        $user = auth()->user();
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

    public function saveLanguages(Request $request)
    {
        foreach ($request->language_name as $key => $language) {

            saveUserMeta($request->tag, $language, $request->language_level[$key], $request->user_id);
        }
        return response()->json(["status" => 1, "msg" => "Language saved successfully"]);

    }

    public function getLanguages(Request $request)
    {
        $languages = UserMeta::where("user_id", $request->id)->where("tag", 'language')->get();
        return view('admin.user.includes.component._languages')
            ->with(["languages" => $languages, "tag" => "languages", "id" => $request->user_id]);
    }

    public function deleteLanguages(Request $request)
    {
        UserMeta::where("id", $request->id)->delete();
        return response()->json(["status" => 1, "msg" => "Language deleted successfully"]);
    }

}
