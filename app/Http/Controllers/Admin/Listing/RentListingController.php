<?php

namespace App\Http\Controllers\Admin\Listing;

use App\Models\Community;
use App\Models\Contact;
use App\Models\ContactSource;
use App\Models\PropertyAmenity;
use App\Models\PropertyFeatures;
use App\Models\PropertyMedia;
use App\Models\PropertyPortal;
use App\Models\PropertyPrice;
use App\Models\SubCommunity;
use App\Traits\FileTrait;
use App\Traits\FiltersTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Bed;
use App\Models\Bath;
use App\Models\Country;
use App\Models\User;
use App\Models\Portal;
use App\Models\PropertyCategory;
use App\Models\PropertyStatus;
use App\Models\Property;
use Illuminate\Support\Facades\Gate;

class RentListingController extends Controller
{
    use FileTrait, FiltersTrait;

    public function index()
    {
        abort_if(Gate::denies("Rents List"), 401);
        $notAdmin = auth()->user()->hasRole(["Sales Director","Line Manager", "Agent"]);
        $childrenIds = [];
        if ($notAdmin){
            $childrenIds = getUserChildrenIds(auth()->id());
        }
        $data['beds'] = Bed::where('status', true)->get();
        $data['baths'] = Bath::where('status', true)->get();
        $data['agents'] = User::whereHas("roles", function ($q) {
            $q->whereIn("name", ["Agent"]);
        })->when($notAdmin, fn($q) => $q->whereIn("parent_id", $childrenIds))->where("status", true)->get();
        $data['categories'] = PropertyCategory::where('status', true)->get();
        $data['status'] = PropertyStatus::where('status', true)->get();
        $data['features'] = PropertyAmenity::where('status', true)->get();
        $data['portals'] = Portal::where('status', true)->get();
        $data['contactSources'] = ContactSource::where('status', true)->get();
        $data['countries'] = Country::where('status', true)->get();
        $data['owners'] = [];
        $data['subCommunities'] = [];
        $data['communities'] = Community::where("status", true)->get();
        if (auth()->user()->hasRole("Agent")){
            $data['owners'] = Contact::whereIn("agent_id", $childrenIds)->whereIn("contact_type_id", [3, 5, 9])->where('status', true)->get();
            $data["subCommunities"] = SubCommunity::whereHas("users", function ($q){
                $q->where("user_id", auth()->id());
            })->get();
        }
        return view('admin.listing.rent.index')->with($data);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            "agent_id" => "required|exists:users,id",
            "owner_id" => "required|exists:contacts,id",
            "property_category_id" => "required|exists:property_categories,id",
            "bed_id" => "nullable|exists:beds,id",
            "bath_id" => "nullable|exists:baths,id",
//            "community_id" => "required|exists:communities,id",
            "sub_community_id" => "required|exists:sub_communities,id",
            "community_property_id" => "nullable|exists:community_properties,id",
            "unit_no" => "required",
            "built_up_area" => "required",
             "publish_status" => "required",
            "rera_permit_number" => "required_if:publish_status,PUBLISHED",
            "parking" => "required|numeric",
            "view" => "nullable",
            "title" => "required",
            "price" => "required",
            "description" => "nullable",
            "furnished" => "required",
            //"featured" => "required",
            "exclusive" => "required",
            "features" => "nullable|array",
            "property_status_id" => "nullable|exists:property_statuses,id",
            "rented_at" => "nullable|numeric",
            "rented_until" => "nullable|date",
            "next_available" => "nullable|date",
            "remind" => "nullable|string",
            "portals" => "nullable|array",
            "floor_plan" => "nullable|mime:jpg,jpeg,png",
            "video_link" => "nullable|string",
            "360_link" => "nullable|string",
            "qr_link" => "nullable|string",
            "cheques" => "nullable|string",
        ]);

        try {

            $validatedData["for"] = "RENT";
            $validatedData["ref_no"] = generatePropertyRefNo();
            $validatedData["created_by_id"] = auth()->id();
            $validatedData["community_id"] = SubCommunity::find($request->sub_community_id)->community_id ?? null;
            $validatedData["publish_status"] = $request->publish_status ?? "DRAFT";
            $property = Property::create($validatedData);

            if ($request->hasFile("floor_plan")) {
                $images = $this->uploadImageAndThumbWithWatermark($request->file("floor_plan"));
                if (!empty($images)) {
                    PropertyMedia::updateOrCreate([
                        "property_id" => $request->id,
                        "type" => "Floor Plan",
                    ], [
                        "path" => $images["image"] ?? null,
                        "thumbnail" => $images["thumbnail"] ?? null,
                        "created_by_id" => auth()->id(),
                    ]);
                }
            }

            if ($request->hasFile("title_deed")) {
                $path = $this->uploadDocument($request->file("title_deed"), "listings");
                if (!empty($path)) {
                    PropertyMedia::updateOrCreate([
                        "property_id" => $request->id,
                        "type" => "Other",
                        "title" => "Title Deed",
                    ], [
                        "path" => $path ?? null,
                    ]);
                }
            }

            if ($request->hasFile("owner_passport_copy")) {
                $path = $this->uploadDocument($request->file("owner_passport_copy"), "listings");
                if (!empty($path)) {
                    PropertyMedia::updateOrCreate([
                        "property_id" => $request->id,
                        "type" => "Other",
                        "title" => "Owner Passport Copy",
                    ], [
                        "path" => $path ?? null,
                    ]);
                }
            }

            if ($request->hasFile("signed_listing_form")) {
                $path = $this->uploadDocument($request->file("signed_listing_form"), "listings");
                if (!empty($path)) {
                    PropertyMedia::updateOrCreate([
                        "property_id" => $request->id,
                        "type" => "Other",
                        "title" => "Signed Listing Form",
                    ], [
                        "path" => $path ?? null,
                    ]);
                }
            }

            PropertyPrice::create([
                "property_id" => $property->id,
                "price" => $request->price,
            ]);

            if ($request->temp_id) {
                PropertyMedia::where("temp_id", $request->temp_id)->update([
                    "property_id" => $property->id,
                    "temp_id" => null,
                ]);
            }

            foreach ($request->portals ?? [] as $portal) {
                PropertyPortal::create([
                    "property_id" => $property->id,
                    "portal_id" => $portal,
                    "created_by_id" => auth()->id(),
                ]);
            }

            foreach ($request->features ?? [] as $feature) {
                PropertyFeatures::create([
                    "property_id" => $property->id,
                    "property_amenity_id" => $feature,
                    "created_by_id" => auth()->id(),
                ]);
            }
            $result = array('status' => '1', 'msg' => 'Listing added successfully', "id" => $property->id);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }

    public function dataTable(Request $request)
    {
        $columns = array(
            0 => 'updated_at',
            1 => 'id',
            2 => 'ref_no',
            3 => 'unit_no',
            4 => 'property_category_id',
            5 => 'community_id',
            6 => 'sub_community_id',
            7 => 'community_property_id',
            8 => 'bed_id',
            10 => 'agent_id',
            11 => 'updated_by_id',
            12 => 'updated_at',
        );

        $notAdmin = auth()->user()->hasRole(["Sales Director","Line Manager", "Agent"]);
        $childrenIds = [];
        if ($notAdmin){
            $childrenIds = getUserChildrenIds(auth()->id());
        }
        $searchKeyWord = htmlspecialchars($request['search']['value']);
        $q = Property::query();
        $totalData = $this->applyPropertyFilters($q, $request)->when(auth()->user()->hasRole("Agent"), function ($q){
            $q->where("agent_id", auth()->id());
        })->where("for", "RENT")->when($notAdmin, fn($q) => $q->whereIn("agent_id", $childrenIds))->count();
        $totalFiltered = $totalData;

        if (!empty($searchKeyWord)) {
            $q = Property::query();
            $data = $this->applyPropertyFilters($q, $request)->when(auth()->user()->hasRole("Agent"), function ($q){
                $q->where("agent_id", auth()->id());
            })->where(function ($q) use ($searchKeyWord) {
                $q->where('ref_no', 'LIKE', '%'.$searchKeyWord.'%')
                ->orWhere('title', 'LIKE', '%' . $searchKeyWord . '%');
            })->where("for", "RENT")->with('propertyCategory', 'community', 'subCommunity', 'communityProperty',
                'propertyCategory', 'agent', 'createdBy', 'updatedBy', 'portals.portal', 'price', 'propertyStatus',
                'bed')
                ->when($notAdmin, fn($q) => $q->whereIn("agent_id", $childrenIds))
                ->get();
        } else {
            $q = Property::query();
            $data = $this->applyPropertyFilters($q, $request)->orderBy($columns[$request["order"][0]["column"]],
                $request["order"][0]["dir"])->where("for", "RENT")
                ->when(auth()->user()->hasRole("Agent"), function ($q){
                    $q->where("agent_id", auth()->id());
                })->offset($request['start'])
                ->limit($request['length'])
                ->with('propertyCategory', 'community', 'subCommunity', 'communityProperty', 'propertyCategory',
                    'agent', 'createdBy', 'updatedBy', 'portals.portal', 'price', 'propertyStatus', 'bed')
                ->when($notAdmin, fn($q) => $q->whereIn("agent_id", $childrenIds))
                ->get();
        }

        $json_data = array(
            "draw" => intval($request['draw']),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );
        echo json_encode($json_data);
    }

    public function update(Request $request)
    {

        $validatedData = $request->validate([
            "id" => "required|exists:properties,id",
            "agent_id" => "required|exists:users,id",
            "owner_id" => "required|exists:contacts,id",
            "property_category_id" => "required|exists:property_categories,id",
            "bed_id" => "nullable|exists:beds,id",
            "bath_id" => "nullable|exists:baths,id",
//            "community_id" => "required|exists:communities,id",
            "sub_community_id" => "required|exists:sub_communities,id",
            "community_property_id" => "nullable|exists:community_properties,id",
            "unit_no" => "required",
            "built_up_area" => "required",
            "publish_status" => "required",
            "rera_permit_number" => "required_if:publish_status,PUBLISHED",
            "parking" => "required|numeric",
            "view" => "nullable",
            "title" => "required",
            "price" => "required",
            "description" => "nullable",
            "furnished" => "required",
//            "featured" => "required",
            "exclusive" => "required",
            "features" => "nullable|array",
            "property_status_id" => "nullable|exists:property_statuses,id",
            "rented_at" => "nullable|numeric",
            "rented_until" => "nullable|date",
            "next_available" => "nullable|date",
            "remind" => "nullable|string",
            "portals" => "nullable|array",
            "floor_plan" => "nullable|mime:jpg,jpeg,png",
            "video_link" => "nullable|string",
            "360_link" => "nullable|string",
            "qr_link" => "nullable|string",
            "cheques" => "nullable|string",
        ]);

        try {

            $validatedData["for"] = "RENT";
            $validatedData["updated_by_id"] = auth()->id();
            $validatedData["updated_at"] = now();
            $validatedData["community_id"] = SubCommunity::find($request->sub_community_id)->community_id ?? null;
            $property = Property::find($request->id);
            $validatedData["publish_status"] = $request->publish_status ?? $property->publish_status;
            $property->update($validatedData);

            if ($request->hasFile("floor_plan")) {
                $images = $this->uploadImageAndThumbWithWatermark($request->file("floor_plan"));
                if (!empty($images)) {
                    PropertyMedia::updateOrCreate([
                        "property_id" => $request->id,
                        "type" => "Floor Plan",
                    ], [

                        "path" => $images["image"] ?? null,
                        "thumbnail" => $images["thumbnail"] ?? null,
                        "created_by_id" => auth()->id(),
                    ]);
                }
            }

            if ($request->hasFile("title_deed")) {
                $path = $this->uploadDocument($request->file("title_deed"), "listings");
                if (!empty($path)) {
                    PropertyMedia::updateOrCreate([
                        "property_id" => $request->id,
                        "type" => "Other",
                        "title" => "Title Deed",
                    ], [
                        "path" => $path ?? null,
                    ]);
                }
            }

            if ($request->hasFile("owner_passport_copy")) {
                $path = $this->uploadDocument($request->file("owner_passport_copy"), "listings");
                if (!empty($path)) {
                    PropertyMedia::updateOrCreate([
                        "property_id" => $request->id,
                        "type" => "Other",
                        "title" => "Owner Passport Copy",
                    ], [
                        "path" => $path ?? null,
                    ]);
                }
            }

            if ($request->hasFile("signed_listing_form")) {
                $path = $this->uploadDocument($request->file("signed_listing_form"), "listings");
                if (!empty($path)) {
                    PropertyMedia::updateOrCreate([
                        "property_id" => $request->id,
                        "type" => "Other",
                        "title" => "Signed Listing Form",
                    ], [
                        "path" => $path ?? null,
                    ]);
                }
            }

            PropertyPrice::updateOrCreate([
                "property_id" => $property->id,
            ], [
                "price" => $request->price,
            ]);

            PropertyPortal::where("property_id", $property->id)->whereNotIn("portal_id",
                $request->portals ?? [])->delete();
            foreach ($request->portals ?? [] as $portal) {
                PropertyPortal::updateOrCreate([
                    "property_id" => $property->id,
                    "portal_id" => $portal,
                ], [
                    "created_by_id" => auth()->id(),
                    "updated_by_id" => auth()->id(),
                ]);
            }
            PropertyFeatures::where("property_id", $property->id)->whereNotIn("property_amenity_id",
                $request->features ?? [])->delete();
            foreach ($request->features ?? [] as $feature) {
                PropertyFeatures::updateOrCreate([
                    "property_id" => $property->id,
                    "property_amenity_id" => $feature,
                ], [
                    "created_by_id" => auth()->id(),
                    "updated_by_id" => auth()->id(),
                ]);
            }
            $result = array('status' => '1', 'msg' => 'Listing updated successfully', "id" => $property->id);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }
}
