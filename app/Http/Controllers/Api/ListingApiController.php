<?php

namespace App\Http\Controllers\Api;

use App\Events\LeadCreated;
use App\Http\Controllers\Admin\Listing\PropertyController;
use App\Http\Controllers\Api\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Library\Api\Commercial;
use App\Http\Library\Api\GenericPortal;
use App\Http\Library\Api\PrivateSite;
use App\Http\Library\Api\PrivateSiteOld;
use App\Models\Lead;
use App\Models\LeadNote;
use App\Models\LeadSource;
use App\Models\Note;
use App\Models\Portal;
use App\Models\Property;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ListingApiController extends BaseController
{


    public function getListingUser(Request $request)
    {
        abort_if($request->api_token != "fYlQO814toCEdmdp", 404);
        $validatedData = $request->validate([
            "reference_no" => "nullable|string",
        ]);

        $property = Property::where("ref_no", $validatedData['reference_no'])->with(["agent:id,first_name,last_name,email,telegram_chatid"])->get(["id", "title", "agent_id"]);

        if (!$property) {
            return response()->json(['status' => 'error', 'message' => 'Property not found']);
        }
        return response()->json(['status' => 'success', 'message' => 'Property User details', 'data' => $property]);
    }

    public function getAll2(Request $request)
    {
        $mainPortal = Portal::where("slug", "private-site")->latest()->first();
        if (is_null($mainPortal)) {
            abort(404);
        }
        $pp = Property::where('is_live', true)->whereHas("portals", function ($q) use ($mainPortal) {
            $q->where("portal_id", $mainPortal->id);
        })->with(['feature.portals' => function ($q) use ($mainPortal) {
            $q->where('portal_id', '=', $mainPortal->id);
        }])->orderBy('properties.created_at', 'asc')
            ->first();


        return \Response::make($pp, '200')->header('Content-Type', 'json');
    }

    public function privateSite(Request $request, PrivateSite $privateSite)
    {
        abort_if($request->api_token != "fYlQO814toCEdmdp", 404);
        $response = $privateSite->run($request);
        return $this->sendResponse($response['status'], $response['message'], $response['data'], $response['code']);
    }
    public function commercialSite(Request $request, Commercial $commercial)
    {
        abort_if($request->api_token != "fYlQO814tothodaCEdmdpCommercial", 404);
        $response = $commercial->run($request);
        return $this->sendResponse($response['status'], $response['message'], $response['data'], $response['code']);
    }
    public function genericPortal(Request $request, GenericPortal $genericPortal)
    {
        abort_if($request->api_token != "fzlQA14tgBEdidp", 404);
        $response = $genericPortal->run($request);
        return $this->sendResponse($response['status'], $response['message'], $response['data'], $response['code'], $request->page ?? 1, $request->per_page ?? 10);
    }
    public function genericPortalAxMap(Request $request, GenericPortal $genericPortal)
    {
        $withoutUnitNo = false; // Default true that means api will not get Unit number by default
        $response = $genericPortal->run($request, $withoutUnitNo);
        return $this->sendResponse($response['status'], $response['message'], $response['data'], $response['code'], $request->page ?? 1, $request->per_page ?? 10);
    }
}
