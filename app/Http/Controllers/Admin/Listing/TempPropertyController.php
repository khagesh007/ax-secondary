<?php

namespace App\Http\Controllers\Admin\Listing;

use App\Events\ChangesRequested;
use App\Events\ListingNoteCreated;
use App\Events\PocketRequest;
use App\Events\PropertyApproval;
use App\Events\PropertyCreated;
use App\Events\PropertyUpdated;
use App\Events\PublishRequested;
use App\Events\UnpublishRequested;
use App\Exports\PropertyExport;
use App\Http\Controllers\AjaxController;
use App\Http\Controllers\Controller;
use App\Mail\Admin\PropertyUnpublishRequestMail;
use App\Models\Bath;
// use App\Models\Filter;
use App\Models\Bed;
use App\Models\Community;
use App\Models\CommunityProperty;
use App\Models\Contact;
use App\Models\ContactAgent;
use App\Models\ContactSource;
use App\Models\Country;
use App\Models\Developer;
use App\Models\Event;
use App\Models\FolderMedia;
use App\Models\Portal;
use App\Models\Property;
use App\Models\TempProperty;
use App\Models\PropertyAmenity;
use App\Models\PropertyCategory;
use App\Models\PropertyCycle;
use App\Models\PropertyFeatures;
use App\Models\PropertyMedia;
use App\Models\TempPropertyMedia;
use App\Models\PropertyNote;
use App\Models\PropertyPortal;
use App\Models\PropertyPrice;
use App\Models\PropertyStatus;
use App\Models\Region;
use App\Models\RepublishRequest;
use App\Models\SubCommunity;
use App\Models\User;
use App\Traits\FileTrait;
use App\Traits\FiltersTrait;
use App\Traits\PDFTrait;
use App\Traits\TelegramTrait;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Intervention\Image\Facades\Image;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UnpublishedListingsExport;

class TempPropertyController extends Controller
{
    use FileTrait, FiltersTrait, TelegramTrait, PDFTrait;

    public function unpublishedListings(){
        return Excel::download(new UnpublishedListingsExport, 'unpublished_listings.xlsx');
        $threeMonthsAgo = Carbon::now()->subMonths(3);

        // Query properties with related property cycles that match the criteria
        $query = Property::whereHas('propertycycles', function ($query) use ($threeMonthsAgo) {
            $query->where('status', 'UNPUBLISHED')
                  ->where('created_at', '>=', $threeMonthsAgo);
        });

        return $query->get(['id','publish_status','ref_no']);
    }

    public function index()
    {

        abort_if(Gate::denies("Properties List"), 401);
        $notAdmin = auth()->user()->hasRole(["Primary Sales Director", "Primary Sales Manager", "Sales Director", "Team Leader", "Line Manager", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }
        $data['beds'] = Bed::where('status', true)->orderBy('order')->get();
        $data['baths'] = Bath::where('status', true)->orderBy('order')->get();
        $data['developers'] = Developer::where('status', true)->get();
        $data['all_agents'] = User::where('status', true)->whereHas("roles", function ($q) {
            $q->whereIn("name", ["Agent", "Line Manager", "Team Leader", "Sales Director", "Head of Secondary"]);
        })->when($notAdmin, fn($q) => $q->whereIn("parent_id", $childrenIds)->orWhere('id', auth()->user()->id))->get();

        $data['agents'] = User::where('status', 1)
            ->whereHas("roles", function ($q) {
                $q->whereIn("name", ["Agent", "Line Manager", "Team Leader", "Sales Director", "Head of Secondary", "CEO", "Primary Sales Director", "Primary Sales Manager", "Primary Agent"]);
            })
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("parent_id", $childrenIds)
                    ->orWhere('id', auth()->user()->id);
            })
            ->when(auth()->user()->hasRole("Admin - Abu Dhabi"), fn($q) => $q->where("region_id", "2"))
            ->get();
        $data['categories'] = PropertyCategory::where('status', true)->orderBy('order', 'asc')->get();
        $data['status'] = PropertyStatus::where('status', true)->get();
        $data['features'] = PropertyAmenity::where('status', true)->get();
        $data['portals'] = Portal::where('status', true)->get();
        $data['contactSources'] = ContactSource::where('status', true)->get();
        $data['countries'] = Country::where('status', true)->get();
        $data['regions'] = Region::where('status', true)->get();
        $data['communities'] = Community::where('status', true)->get();
        $data['refered_by'] = User::where('status', 1)
                            // ->whereHas("roles", function ($q) {
                            //     $q->whereIn("name", ["Agent", "Line Manager", "Team Leader", "Sales Director", "Head of Secondary", "CEO", "Primary Sales Director", "Primary Sales Manager"]);
                            // })
                            ->when(auth()->user()->hasRole("Admin - Abu Dhabi"), fn($q) => $q->where("region_id", "2"))
                            ->get();

        $data['owners'] = [];
        $data['telesales'] = User::where('status', true)->whereHas("roles", function ($q) {
            $q->whereIn("name", ["Telesales"]);
        })->get();

        if (auth()->user()->hasRole("Agent")) {
            $data['owners'] = Contact::whereIn("agent_id", $childrenIds)->orWhereHas("contactAgents", fn($q) => $q->whereIn("user_id", $childrenIds))->where('status', true)->whereHas("contactTypes", fn($q) => $q->whereIn("contact_type_id", [3, 4, 5, 9]))->where('status', true)->get();
        }
        return view('admin.listing.temp_index')->with($data);
    }


    public function view(Property $property)
    {

        // abort_if(Gate::denies("Properties View") || ($notAdmin && !in_array($property->agent_id, $childrenIds)), 401);
        $data['property'] = $property->load(['agent', 'community', 'subCommunity', 'communityProperty', 'price', 'propertyCategory', 'bed', 'bath', 'amenities.amenity', 'photos', 'propertyStatus']);
        if (Auth::guest() && $data['property']->publish_status != 'PUBLISHED') {
            abort(401);
        }
        return view('admin.listing.view')->with($data);
    }
    public function print(Property $property, Request $request)
    {
        $data['property'] = $property->load(['agent', 'community', 'subCommunity', 'communityProperty', 'price', 'propertyCategory', 'bed', 'bath', 'amenities.amenity', 'photos', 'propertyStatus']);
        if ($data['property']->is_live == false && $data['property']->publish_status != 'POCKET') {
            abort(401);

        }
        if ($request->description) {
            $data['property']->description = $request->description;
        }
        $data['amenities'] = '';
        if ($request->features) {
            $data['amenities'] = $request->features;
        }
        $show_price = false;
        if ($request->show_price == 'true') {
            $show_price = true;
        }
        $data['show_price'] = $show_price;

        $show_profile = false;
        if ($request->show_profile == 'yes') {
            $show_profile = true;
        }
        $data['show_profile'] = $show_profile;
        if ($request->image_1) {
            $data['images'][] = PropertyMedia::find($request->image_1);

        }
        if ($request->image_2) {
            $data['images'][] = PropertyMedia::find($request->image_2);
        }
        if ($request->image_3) {
            $data['images'][] = PropertyMedia::find($request->image_3);
        }

        if ($request->image) {
            $data['image'] = PropertyMedia::find($request->image);
        }

        if ($request->pretext) {
            $data['pretext'] = $request->pretext;
        }
        if ($request->posttext) {
            $data['posttext'] = $request->posttext;
        }

        if ($request->location) {
            $data['location'] = $request->location;
        }
        $profile_user = 'agent';
        if ($request->profile_user == 'me') {
            $profile_user = 'me';
        }
        $data['profile_user'] = $profile_user;

        $data['longitude'] = $data['property']->communityProperty->longitude ?? $data['property']->subCommunity->latitude ?? '';
        $data['latitude'] = $data['property']->communityProperty->latitude ?? $data['property']->subCommunity->latitude ?? '';
        return view('admin.listing.new_2_pdf')->with($data);
        return view('admin.listing.pdf')->with($data);
    }
    public function listingAdvertisement(Property $property, Request $request)
    {
        if (!auth()->user()->hasRole(['Admin', 'Super Admin'])) {
            abort(403);
        }
        $data['property'] = $property->load(['agent', 'community', 'subCommunity', 'communityProperty', 'price', 'propertyCategory', 'bed', 'bath', 'amenities.amenity', 'photos', 'propertyStatus']);
        // if ($data['property']->is_live == false && $data['property']->publish_status != 'POCKET') {
        //     abort(401);

        // }
        if ($request->description) {
            $data['property']->description = $request->description;
        }
        $data['amenities'] = '';
        if ($request->features) {
            $data['amenities'] = $request->features;
        }
        $show_price = false;
        if ($request->show_price == 'true') {
            $show_price = true;
        }
        $data['show_price'] = $show_price;

        $show_profile = false;
        if ($request->show_profile == 'yes') {
            $show_profile = true;
        }
        $data['show_profile'] = $show_profile;
        if ($request->image_1) {
            $data['images'][] = PropertyMedia::find($request->image_1);

        }
        if ($request->image_2) {
            $data['images'][] = PropertyMedia::find($request->image_2);
        }
        if ($request->image_3) {
            $data['images'][] = PropertyMedia::find($request->image_3);
        }

        if ($request->image) {
            $data['image'] = PropertyMedia::find($request->image);
        }

        if ($request->pretext) {
            $data['pretext'] = $request->pretext;
        }
        if ($request->posttext) {
            $data['posttext'] = $request->posttext;
        }

        if ($request->location) {
            $data['location'] = $request->location;
        }
        $profile_user = 'agent';
        if ($request->profile_user == 'me') {
            $profile_user = 'me';
        }
        $data['profile_user'] = $profile_user;

        $data['longitude'] = $data['property']->communityProperty->longitude ?? $data['property']->subCommunity->latitude ?? '';
        $data['latitude'] = $data['property']->communityProperty->latitude ?? $data['property']->subCommunity->latitude ?? '';
        return view('admin.listing.includes.listing_advertisement')->with($data);
    }

    public function generatePDF(Request $request, $id)
    {
        //  dd($request->all());
        $property = Property::where('ref_no', $id)->first();
        if ($property) {
            $data['property'] = $property->load(['agent', 'community', 'subCommunity', 'communityProperty', 'price', 'propertyCategory', 'bed', 'bath', 'amenities.amenity', 'photos', 'propertyStatus']);
            if ($request->description) {
                $data['property']->description = $request->description;
            }
            $data['amenities'] = '';
            if ($request->features) {
                $data['amenities'] = $request->features;
            }
            $show_price = false;
            if ($request->show_price == 'true') {
                $show_price = true;
            }
            $data['show_price'] = $show_price;

            if ($request->image_1) {
                $data['images'][] = PropertyMedia::find($request->image_1);

            }
            if ($request->image_2) {
                $data['images'][] = PropertyMedia::find($request->image_2);
            }
            if ($request->image_3) {
                $data['images'][] = PropertyMedia::find($request->image_3);
            }
            $show_profile = false;
            if ($request->show_profile == 'yes') {
                $show_profile = true;
            }
            $data['show_profile'] = $show_profile;

            $profile_user = 'agent';
            if ($request->profile_user == 'me') {
                $profile_user = 'me';
            }
            $data['profile_user'] = $profile_user;

            return view('admin.listing.pdf_preview')->with($data);
        } else {
            return redirect()->back()->with('error', 'Property not found');
        }

    }

    public function generatePOST(Request $request, $id)
    {
        //  dd($request->all());
        $property = Property::where('ref_no', $id)->first();
        $data['property'] = $property->load(['agent', 'community', 'subCommunity', 'communityProperty', 'price', 'propertyCategory', 'bed', 'bath', 'amenities.amenity', 'photos', 'propertyStatus']);

        $show_price = false;
        $show_profile = false;
        if ($request->show_price == 'true') {
            $show_price = true;
        }

        $show_profile = false;
        if ($request->show_profile == 'yes') {
            $show_profile = true;
        }
        $data['show_profile'] = $show_profile;
        $data['show_price'] = $show_price;

        if ($request->image) {
            $data['image'] = PropertyMedia::find($request->image);
        }

        if ($request->location) {
            $data['location'] = $request->location;
        }

        $profile_user = 'agent';
        if ($request->profile_user == 'me') {
            $profile_user = 'me';
        }
        $data['profile_user'] = $profile_user;

        return view('admin.listing.post_preview')->with($data);

    }

    public function generateBrochure(Request $request, $id)
    {
        //  dd($request->all());
        $property = Property::where('ref_no', $id)->first();
        if ($property) {
            $data['property'] = $property->load(['agent', 'community', 'subCommunity', 'communityProperty', 'price', 'propertyCategory', 'bed', 'bath', 'amenities.amenity', 'photos', 'propertyStatus']);
            if ($request->description) {
                $data['property']->description = $request->description;
            }
            $data['amenities'] = '';
            if ($request->features) {
                $data['amenities'] = $request->features;
            }
            $show_price = false;
            if ($request->show_price == 'true') {
                $show_price = true;
            }
            $data['show_price'] = $show_price;

            if ($request->image_1) {
                $data['images'][] = PropertyMedia::find($request->image_1);

            }
            if ($request->image_2) {
                $data['images'][] = PropertyMedia::find($request->image_2);
            }
            if ($request->image_3) {
                $data['images'][] = PropertyMedia::find($request->image_3);
            }
            if ($request->image_4) {
                $data['images'][] = PropertyMedia::find($request->image_4);
            }
            $show_profile = false;
            if ($request->show_profile == 'yes') {
                $show_profile = true;
            }
            $data['show_profile'] = $show_profile;

            $profile_user = 'agent';
            if ($request->profile_user == 'me') {
                $profile_user = 'me';
            }
            $data['profile_user'] = $profile_user;

            return view('pdf.index')->with($data);
        } else {
            return redirect()->back()->with('error', 'Property not found');
        }

    }
    // actual download thing
    public function newDownloadBrochure(Request $request, $id)
    {
        //  dd($request->all());
        $property = Property::where('ref_no', $id)->first();
        if ($property) {
            $data['property'] = $property->load(['agent', 'community', 'subCommunity', 'communityProperty', 'price', 'propertyCategory', 'bed', 'bath', 'amenities.amenity', 'photos', 'propertyStatus']);
            if ($request->description) {
                $data['property']->description = $request->description;
            }
            $data['amenities'] = '';
            if ($request->features) {
                $data['amenities'] = $request->features;
            }
            $show_price = false;
            if ($request->show_price == 'true') {
                $show_price = true;
            }
            $data['show_price'] = $show_price;

            if ($request->image_1) {
                $data['images'][] = PropertyMedia::find($request->image_1);

            }
            if ($request->image_2) {
                $data['images'][] = PropertyMedia::find($request->image_2);
            }
            if ($request->image_3) {
                $data['images'][] = PropertyMedia::find($request->image_3);
            }
            if ($request->image_4) {
                $data['images'][] = PropertyMedia::find($request->image_4);
            }
            $show_profile = false;
            if ($request->show_profile == 'yes') {
                $show_profile = true;
            }
            $data['show_profile'] = $show_profile;

            $profile_user = 'agent';
            if ($request->profile_user == 'me') {
                $profile_user = 'me';
            }
            $data['profile_user'] = $profile_user;

            return view('pdf.index_download')->with($data);
        } else {
            return redirect()->back()->with('error', 'Property not found');
        }

    }

    public function downloadBrochure(Request $request, $id)
    {

        $property = Property::where('ref_no', $id)->first();
        if ($property) {
            $data['property'] = $property->load(['agent', 'community', 'subCommunity', 'communityProperty', 'price', 'propertyCategory', 'bed', 'bath', 'amenities.amenity', 'photos', 'propertyStatus']);
            if ($request->description) {
                $data['property']->description = $request->description;
            }
            $data['amenities'] = '';
            if ($request->features) {
                $data['amenities'] = $request->features;
            }
            $show_price = false;
            if ($request->show_prices == 'true') {
                $show_price = true;
            }
            $data['show_price'] = $show_price;

            if ($request->image_1) {
                $data['images'][] = PropertyMedia::find($request->image_1);

            }
            if ($request->image_2) {
                $data['images'][] = PropertyMedia::find($request->image_2);
            }
            if ($request->image_3) {
                $data['images'][] = PropertyMedia::find($request->image_3);
            }
            if ($request->image_4) {
                $data['images'][] = PropertyMedia::find($request->image_4);
            }
            $show_profile = false;
            if ($request->show_profile == 'yes') {
                $show_profile = true;
            }
            $data['show_profile'] = $show_profile;

            $profile_user = 'agent';
            if ($request->profile_user == 'me') {
                $profile_user = 'me';
            }
            $data['profile_user'] = $profile_user;

            return view('pdf.index_download')->with($data);
        } else {
            return redirect()->back()->with('error', 'Property not found');
        }

    }
    public function printNew(Property $property)
    {
        // abort_if(Gate::denies("Properties View") || ($notAdmin && !in_array($property->agent_id, $childrenIds)), 401);
        $data['property'] = $property->load(['agent', 'community', 'subCommunity', 'communityProperty', 'price', 'propertyCategory', 'bed', 'bath', 'amenities.amenity', 'photos', 'propertyStatus']);
        if (Auth::guest() && $data['property']->publish_status == 'PUBLISHED') {
            abort(401);
        }
        return view('admin.listing.pdfNew')->with($data);
    }

    public function getProperty(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:temp_properties',
        ]);
        try {
            $property = TempProperty::withTrashed()->find($request->id);
            $notAdmin = auth()->user()->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);
            $childrenIds = getUserChildrenIds(auth()->id());
            $property['editable'] = false;
            if (in_array($property->agent_id, $childrenIds) || auth()->user()->hasRole(["Admin", "Super Admin"]) || ($property->region_id == 2 && auth()->user()->hasRole("Admin - Abu Dhabi"))) {
                $property['editable'] = true;
            }

            if ((auth()->user()->region_id == 2 || auth()->user()->parent_id == 699) && auth()->user()->hasRole('Agent')) {
                $property['editable'] = false;
            }

            $description = strip_tags($property->description, '<br>');
            $description = str_replace("<br>", '', $property->description);
            $updateDescription = str_replace("<br />", '', $description);
            $property->description = $updateDescription;
            if ($property->view) {
                $property->view = array_filter(json_decode($property->view) ?? []);
            }
            if ($property->villa_type) {
                $property->villa_type = array_filter(json_decode($property->villa_type) ?? []);
            }

            $property->load(["portals.portal", "propertyMeta", "price", "amenities.amenity", "createdBy", "updatedBy", "agent:id,name", "owner", "updates"]);

            if ($property->publish_status === 'POCKET') {
                $property->setRelation('portals', collect()); // Set an empty collection as the portals relation
            }
            $result = array('status' => '1', 'msg' => 'Property details fetched successfully', 'data' => $property);
        } catch (\Exception $e) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $e->getMessage());
        }
        return json_encode($result);
    }

    public function delete(Request $request)
    {
        
        $request->validate([
            'id' => 'required|exists:properties',
            'hard' => 'required',
        ]);

        $status = TempProperty::find($request->id);
        $status->deleted_by_id = auth()->user()->id;
        $status->withoutApproval()->delete();
        $user = auth()->user();

        if (($user->hasRole('Agent') && $status->publish_status != "DRAFT") || ($status->region_id != 2 && auth()->user()->hasRole("Admin - Abu Dhabi"))) {
            $result = array('status' => '0', 'msg' => 'You can not delete this property');
            return json_encode($result);
        }

        if ($status->delete()) {
            PropertyNote::create([
                "property_id" => $request->id,
                "note" => "Property deleted by " . auth()->user()->name,
                "created_by_id" => auth()->id(),
            ]);

            $result = array('status' => '1', 'msg' => 'Property deleted successfully');
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
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
            $count = 0;
            foreach ($ids as $id) {
                $property = TempProperty::where('id', $id)->where('publish_status', 'DRAFT')->first();
                if ($property) {
                    $property->deleted_by_id = auth()->user()->id;
                    $property->withoutApproval()->save();
                    $property->delete();
                    $count++;

                    PropertyNote::create([
                        "property_id" => $id,
                        "note" => "Property deleted by " . auth()->user()->name,
                        "created_by_id" => auth()->id(),
                    ]);

                }
            }
            $result = array('status' => '1', 'msg' => $count . ' Draft properties deleted successfully');
        }
        return json_encode($result);
    }

    public function deleteMultipleImages(Request $request)
    {
        $request->validate([
            'ids' => 'required',
        ]);

        $ids = $request->ids;
        if (gettype($ids) == 'array' && count($ids) > 0) {
            $property_id = PropertyMedia::where('id', $ids[0])->first()?->property_id ?? null;

            foreach ($ids ?? [] as $media_id) {
                $media = TempPropertyMedia::find($media_id);
                $environment = \App::environment();
                if ($media) {
                    $property = TempProperty::find($media->property_id);
                    if ($property?->is_live && !auth()->user()->hasRole(["Super Admin", "Admin"])) {
                        $propObj2 = TempProperty::find($property->id);
                        $propObj2->withoutApproval()->update(['publish_status' => 'PENDING']);
                        $media->media_changed = "Deleted";
                    }
                    $media->deleted_by_id = auth()->user()->id;
                    $media->save();
                    $media->delete();

                    $arr = ['attributes' => [],
                        'old' => $request->ids, 'property_id' => $property_id];
                    activity()
                        ->performedOn($media)
                        ->causedBy(auth()->user())
                        ->withProperties($arr)
                        ->event('deleted')
                        ->log(auth()->user()->first_name . ' has deleted multiple (' . count($ids) . ') listing images');
                }
            }
        }
        $result = array('status' => '1', 'msg' => "Photos removed successfully.");
        return json_encode($result);

        $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        return json_encode($result);
    }
    public function bulkAssignAgents(Request $request)
    {
        if (!auth()->user()->hasPermissionTo('Properties Assign Agents')) {
            $result = array('status' => '0', 'msg' => "You don't have permission to bulk assign the agents", 'error' => "You don't have permission to bulk assign the agents", 'line' => "");
            return response()->json($result, 403);
        }
        $request->validate([
            'ids' => 'required|array',

        ]);
        if (gettype($request->ids) == 'array' && count($request->ids) > 0) {
            $ids = $request->ids;
            foreach ($ids as $property) {
                $propertyAgent = Property::where('id', $property)->with('agent')->first();

                // Assigning the owner to agent
                if ($propertyAgent && $propertyAgent->owner_id) {

                    $owner = Contact::where('id', $propertyAgent->owner_id)->with('contactAgents')->first();

                    $ownerAgents = $owner->contactAgents->pluck('id');

                    if ($owner->agent_id == null) {
                        $owner->update(['agent_id' => $request->agent_id]);
                    } elseif (!(in_array($request->agent_id, $ownerAgents->toArray()) || $request->agent_id == $owner->agent_id)) {
                        $obj = new ContactAgent();
                        $obj->contact_id = $owner->id;
                        $obj->user_id = $request->agent_id;
                        $obj->created_by_id = auth()->id();
                        $obj->save();
                    }

                }
                $user = User::where('id', $request->agent_id)->first();
                $propertyNotes = new Request();
                if ($propertyAgent->agent) {
                    $msg = "Property has been transferred from " . $propertyAgent->agent->name . " to " . $user->name;
                } else {
                    $msg = "Property has been assigned to " . $user->name;
                }
                $propertyNotes->replace(['property_id' => $property, "note" => $msg]);
                $note = $this->addNote($propertyNotes);
            }
            if (Property::whereIn('id', $ids)->update(['agent_id' => $request->agent_id, 'updated_by_id' => auth()->id()])) {
                $result = array('status' => '1', 'msg' => count($ids) . ' Property assigned successfully');
            } else {
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }
        }

        return json_encode($result);
    }

    public function uploadPhoto(Request $request)
    {

        $request->validate([
            'file' => 'required|mimes:jpg,jpeg,png',
            'temp_id' => 'nullable',
            'property_id' => 'nullable',
            'watermark' => 'nullable',
        ]);

        //  dd($request->all());
        if ($request->watermark) {
            $watermark = $request->watermark == "78645675" ? "assets/images/logo/watermarkv3.png" : '';
        } else {
            $watermark = '';
        }

        if (auth()->user()->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"])) {
            $watermark = 'assets/images/logo/watermarkv3.png';
        }
        $property = Property::find($request->property_id);

        // $watermark = $request->watermark=="78645675" ? "assets/images/logo/watermarkv3.png": '';
        $data = $this->uploadImageAndThumbWithWatermark(file: $request->file("file"), path: "listings", watermark: $watermark);
        if (!empty($data)) {
            $media = new PropertyMedia();
            if ($property) {
                if ($property->is_live && !auth()->user()->hasRole(["Super Admin", "Admin"])) {
                    $propObj2 = Property::find($property->id);
                    $propObj2->withoutApproval()->update(['publish_status' => 'PENDING']);
                    $media->media_changed = "Added";
                }
            }

            $media->property_id = $request->property_id;
            $media->temp_id = $request->temp_id;
            $media->path = $data["image_v2"] ?? null;
            $media->original = $data["original"] ?? null;
            $media->thumbnail = $data["thumbnail"] ?? null;
            $media->order = PropertyMedia::count() + 1;
            $media->type = "Photos";
            $media->created_by_id = auth()->id();
            $dirty = $media->getDirty() ?? [];
            $media->save();

            //if (!auth()->user()->hasRole(["Super Admin", "Admin"])) {

            //logging starts

            $arr = ['attributes' => $dirty,
                'old' => [], 'property_id' => $request->property_id];

            try {
                activity()
                    ->performedOn($media)
                    ->causedBy(auth()->user())
                    ->withProperties($arr)
                    ->event('created')
                    ->log(auth()->user()->first_name . ' has uploaded an image ');
                $result = array('status' => '1', 'msg' => "Photo uploaded successfully.", 'data' => $media);
                return json_encode($result);

            } catch (\Throwable $th) {
                dd($media, $arr, $dirty, $th);
            }

            // }

            dd($result);
        }
        $result = array('status' => '0', 'msg' => 'Something went wrong!!', "error");
        return response()->json($result, 500);
    }

    public function removePhoto(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'media_id' => 'required|exists:property_media,id',
        ]);

        $media = PropertyMedia::find($request->media_id);
        $original = $media?->getOriginal() ?? [];
        $dirty = $media?->getDirty() ?? [];

        $environment = \App::environment();
        if ($media) {
            $property = [];
            if ($media->property_id) {
                $property = Property::find($media->property_id);
            }
            // if ($environment == "production") {
            //     $this->removePhotoFromBucket($request->media_id);
            // }
            if ($property) {
                if ($property->is_live && !auth()->user()->hasRole(["Super Admin", "Admin"])) {
                    $propObj2 = Property::find($property->id);
                    $propObj2->withoutApproval()->update(['publish_status' => 'PENDING']);
                    $media->media_changed = "Deleted";
                }
            }

            $media->deleted_by_id = auth()->id();
            $media->save();
            $arr = ['attributes' => $dirty, 'old' => $original, 'property_id' => $media->property_id, 'media_id' => $media->id];
            $media->delete();

            activity()
                ->performedOn($media)
                ->causedBy(auth()->user())
                ->withProperties($arr)
                ->event('deleted')
                ->log(auth()->user()->first_name . ' has deleted listing image');
            $result = array('status' => '1', 'msg' => "Photo removed successfully.");
            return json_encode($result);
        }
        $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        return json_encode($result);
        // } else {
        //     return response()->json(['status' => '0', 'msg' => 'You can not delete this photo.']);
        // }

    }

    public function removeFromPortal(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'portal_id' => 'required|exists:portals,id',
        ]);

        try {
            $portal = Portal::find($request->portal_id);
            PropertyPortal::where("property_id", $request->property_id)->where("portal_id",
                $request->portal_id)->delete();
            PropertyNote::create([
                "property_id" => $request->property_id,
                "note" => "Listing has been Unpublished from " . $portal->name,
                "created_by_id" => auth()->id(),
            ]);
            $result = array('status' => '1', 'msg' => "Property removed from portal successfully.");
            return json_encode($result);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            return json_encode($result);
        }
    }

    public function toggleHot(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
        ]);

        try {
            $property = Property::find($request->property_id);
            $note = "Listing has been ";
            if ($property->hot) {
                $property->hot = false;
            } else {
                $property->hot = true;
            }
            $property->save();
            PropertyNote::create([
                "property_id" => $request->property_id,
                "note" => "Listing has been " . ($property->hot ? "Marked" : "Unmarked") . " as Hot Property",
                "created_by_id" => auth()->id(),
            ]);
            $result = array('status' => '1', 'msg' => "Property hot status changed successfully.");
            return json_encode($result);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            return json_encode($result);
        }
    }

    public function addNote(Request $request)
    {
        //  dd($request->all());
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'note' => 'required_without:image|string',
            'image' => 'nullable',
        ]);

        // $notAdmin = auth()->user()->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);
        $notAdmin = auth()->user()->hasRole(["Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
            $property = Property::find($request->property_id);
            if (!in_array($property->agent_id, $childrenIds)) {
                $result = array('status' => '0', 'msg' => "You don't have permission to add note");
                return json_encode($result);
            }
        }
        try {

            $propertyNote = new PropertyNote();
            $propertyNote->property_id = $request->property_id;
            $propertyNote->note = $request->note;

            if ($request->image && $request->image !== 'undefined') {
                $request->validate([
                    'image' => 'required|mimes:jpg,jpeg,png',
                ]);
                $path = $this->uploadDocument($request->file("image"), "Notes");
                $propertyNote->url = $path ?? null;
            }

            $propertyNote->created_by_id = auth()->id();
            $propertyNote->save();
            event(new ListingNoteCreated($propertyNote));
            $result = array('status' => '1', 'msg' => "Listing note added successfully.");
            return json_encode($result);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
            return json_encode($result);
        }
    }

    public function reOrderPhotos(Request $request)
    {
        if (auth()->user()->hasPermissionTo('Change Media Order') || auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Admin - Abu Dhabi')) {
            $request->validate([
                'ids' => 'required|',
                'property_id' => 'required|exists:properties,id',
            ]);

            try {
                $property = Property::find($request->property_id);

                if ($property->agent_id != auth()->id() && !auth()->user()->hasRole(['Super Admin', "Admin", "Admin - Abu Dhabi"])) {
                    $result = array('status' => '0', 'msg' => 'You do not have permission to change media order');
                    return json_encode($result);
                }

                $ids = explode(",", $request->ids);
                foreach ($ids ?? [] as $index => $id) {
                    if (!empty($id)) {
                        // PropertyMedia::find($id)->update(["order" => $index + 1]);
                        if (PropertyMedia::find($id)) {
                            $media = PropertyMedia::find($id);
                            if ($property->is_live && !auth()->user()->hasRole(["Admin", "SuperAdmin"])) {
                                $media->media_changed = "Order Updated";
                            }

                            $media->order = $index + 1;
                            $media->updated_by_id = auth()->id();
                            $media->save();

                        }
                    }
                }

                if ($property->is_live && !auth()->user()->hasRole(["Super Admin", "Admin"])) {
                    $property->withoutApproval()->update(['publish_status' => 'PENDING']);
                    activity()
                        ->performedOn($property)
                        ->causedBy(auth()->user())
                        ->withProperties([])
                        ->event('updated')
                        ->log('Images order has been changed by ' . auth()->user()->first_name);
                }
                $result = array('status' => '1', 'msg' => "Photo orders has been changed successfully.");
                return json_encode($result);
            } catch (\Exception $exception) {
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
                return json_encode($result);
            }
        } else {
            $result = array('status' => '0', 'msg' => 'You do not have permission to change media order');
            return json_encode($result);

        }
    }

    public function duplicateProperty(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:properties',
            'for' => 'required|string',
            'copy_images' => 'nullable',
            'duplicate_agent_id' => 'required|exists:users,id',
        ]);

        DB::beginTransaction();
        try {
            $oldPropertyId = $request->id;

            $oldProperty = Property::find($oldPropertyId);
            $duplicates = PropertyNote::where('note', 'like', '%Listing created as copy of ' . $oldProperty->ref_no . '%')->count();
            // dd($duplicates);
            $unit_no = $oldProperty->unit_no;
            $agent_id = $oldProperty->agent_id;
            $features = PropertyFeatures::where("property_id", $oldPropertyId)->get()->toArray();
            $notes = PropertyNote::where("property_id", $oldPropertyId)->get()->toArray();
            $portals = PropertyPortal::where("property_id", $oldPropertyId)->get()->toArray();
            $prices = PropertyPrice::where("property_id", $oldPropertyId)->get()->toArray();
            $categortID = $oldProperty["property_category_id"];
            $old_ref_no = $oldProperty["ref_no"];
            $owner_id = $oldProperty["owner_id"];
            $oldProperty = $oldProperty->toArray();
            $oldProperty["for"] = strtoupper($request->for);

            // $relist_reasons = ['Relist (Agent Request)', 'Relist (Older than 3 months)'];
            // if (isset($request->duplication_reason) && in_array($request->duplication_reason, $relist_reasons)) {
            //     $oldProperty["ref_no"] = generatePropertyRefNo().'-r'.($duplicates+1);
            // }else{
                $oldProperty["ref_no"] = generatePropertyRefNo();

            // }


            $oldProperty["created_by"] = auth()->id();
            $oldProperty["updated_by"] = auth()->id();
            $oldProperty["publish_status"] = 'DRAFT';
            $oldProperty["is_live"] = false;
            $oldProperty["updated_at"] = Carbon::now();
            $oldProperty["created_at"] = Carbon::now();
            $oldProperty["unit_no"] = null;
            $oldProperty["title_deed_text"] = null;
            $oldProperty["contract_start"] = null;
            $oldProperty["contract_end"] = null;
            $oldProperty["agent_id"] = $request->duplicate_agent_id;
            $oldDescription = $oldProperty["description"];

            $property = new Property();
            $property->ref_no = $oldProperty["ref_no"];
            $property->is_duplicated = true;
            $property->duplicated_from =$old_ref_no;
            $property->duplicate_reason = $request->duplication_reason;
            $property->withoutApproval()->save();


            if ($request->duplication_reason == 'Copy Content Only') {
                $property->title = 'Duplicate of ' . $old_ref_no;
                $property->description = $oldDescription;
                $property->for = strtoupper($request->for);
                $property->agent_id = $request->duplicate_agent_id;
                $property->publish_status = 'DRAFT';
                $property->is_live = false;
                $property->property_category_id = $categortID;

                $property->withoutApproval()->save();
            } else {
                $property->withoutApproval()->update($oldProperty);
            }

            if ($request->boolean("copy_images")) {

                $photos = PropertyMedia::where("property_id", $oldPropertyId)->where("type", "Photos")->get()->toArray();
                foreach ($photos ?? [] as $photo) {
                    $photo["property_id"] = $property->id;
                    //$imageUrl = $this->copyPhoto($photo["path"], "listings");
                    $imageV2Url = $this->copyPhoto($photo["path"], "listings/with_watermark"); // Need to be removed
                    $originalUrl = $this->copyPhoto($photo["original"], "listings");
                    $thumbUrl = $this->copyPhoto($photo["thumbnail"], "listings");
                    if (!empty($imageV2Url)) {
                        $media = PropertyMedia::create([
                            "property_id" => $property->id,
                            "path" => $imageV2Url ?? null,
                            "original" => $originalUrl ?? null,
                            "thumbnail" => $thumbUrl ?? null,
                            "order" => $photo['order'] ?? 10,
                            "type" => "Photos",
                            "created_by_id" => auth()->id(),
                        ]);
                    }
                }
            }

            // if ($request->duplication_reason == 'Copy Content Only'){
            foreach ($features ?? [] as $feature) {
                $feature["property_id"] = $property->id;
                PropertyFeatures::create($feature);
            }
            // }

            /*foreach ($notes ?? [] as $note){
            $note["property_id"] = $property->id;
            PropertyNote::create($note);
            }
             */
            if ($request->duplication_reason != 'Copy Content Only') {
                foreach ($portals ?? [] as $portal) {
                    $portal["property_id"] = $property->id;
                    PropertyPortal::create($portal);
                }
            }
            if ($request->duplication_reason != 'Copy Content Only') {
                foreach ($prices ?? [] as $price) {
                    $objp = new PropertyPrice();
                    $objp->property_id = $property->id;
                    $objp->price = $price['price'];
                    $objp->prices = $price['prices'];
                    $objp->price_on_application = $price['price_on_application'];
                    $objp->withoutApproval()->save();
                }
            }

            $reasons = ['Relist (Agent Request)', 'Relist (Older than 3 months)', 'Offline in PF', 'Offline in Bayut / Dubizzle'];
            if (isset($request->duplication_reason) && in_array($request->duplication_reason, $reasons)) {

                $p_notes = PropertyNote::where("property_id", $oldPropertyId)->get();
                foreach ($p_notes as $p_note) {
                    $new = new PropertyNote();
                    $new->property_id = $property->id;
                    $new->note = $p_note->note;
                    $new->created_by_id = auth()->id();
                    $new->save();
                }
                if ($p_notes->count() > 0) {
                    $note = new \App\Models\PropertyNote();
                    $note->note = '====================';
                    $note->created_by_id = auth()->id();
                    $note->property_id = $property->id;
                    $note->save();
                }
                // dd(!auth()->user()->hasRole('Agent'), $agent_id, auth()->id());
                if (!auth()->user()->hasRole('Agent') || $agent_id == auth()->id()) {
                    $property->unit_no = $unit_no;
                    $property->owner_id = $owner_id;
                    $property->withoutApproval()->save();
                    $docs = PropertyMedia::where("property_id", $oldPropertyId)->where("type", '!=', "Photos")->get()->toArray();
                    foreach ($docs ?? [] as $photo) {
                        $photo["property_id"] = $property->id;
                        $type = $photo['type'];
                        if ($type == null) {
                            $type = 'documents';
                        }

                        $imageUrl = $this->copyDocument($photo["path"], "listings/" . $property->ref_no . "/" . $type);
                        if (!empty($imageUrl)) {
                            $media = PropertyMedia::create([
                                "property_id" => $property->id,
                                "title" => $photo['title'],
                                "description" => $photo['description'],
                                "path" => $imageUrl ?? null,
                                "order" => $photo['order'] ?? 10,
                                "type" => $photo['type'],
                                "note" => $photo['note'],
                                "password" => $photo['type'],
                                "status" => $photo['status'],
                                "created_by_id" => auth()->id(),
                            ]);
                        }
                    }
                }

            } else if ($request->duplication_reason == 'Copy Content Only') {
                $p_notes = PropertyNote::where("property_id", $oldPropertyId)->get();
                if ($request->duplication_reason != 'Copy Content Only') {

                    foreach ($p_notes as $p_note) {
                        $new = new PropertyNote();
                        $new->property_id = $property->id;
                        $new->note = $p_note->note;
                        $new->created_by_id = auth()->id();
                        $new->save();
                    }
                    if ($p_notes->count() > 0) {
                        $note = new \App\Models\PropertyNote();
                        $note->note = '====================';
                        $note->created_by_id = auth()->id();
                        $note->property_id = $property->id;
                        $note->save();
                    }
                }

            }

            $note = new \App\Models\PropertyNote();
            $note->note = 'Listing created as copy of ' . $old_ref_no . ' with reason: ' . $request->duplication_reason;
            $note->created_by_id = auth()->id();
            $note->property_id = $property->id;
            $note->save();

            DB::commit();
            $result = array('status' => '1', 'msg' => 'Property duplicated successfully');
            return json_encode($result);
        } catch (\Exception $exception) {
            DB::rollBack();
            // dd($exception);
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', "error" => $exception->getMessage(), "line" => $exception->getLine());
            // Return 500 server error
            return response()->json($result, 500);
            // $result = array('status' => '0', 'msg' => $exception->getMessage());
        }
        return json_encode($result);

    }

    public function publishRequest(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:properties,id',
            'request_note' => 'required|string',
            'return_array' => 'nullable|boolean',
        ]);

        try {
            $portals = Portal::whereIn("name", ["Property Finder", "Bayut", "Dubizzle", "Generic portal"])->get();
            $property = Property::find($request->id);

            // Checking if the person has access to unpublish this listing
            $notAdmin = auth()->user()->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);

            $childrenIds = [];
            if ($notAdmin) {
                $childrenIds = getUserChildrenIds(auth()->id());
                if (!in_array($property->agent_id, $childrenIds)) {
                    $result = array('status' => '0', 'message' => 'You are not authorized to perform this action', 'error' => '', 'line' => '');
                    return response()->json($result, 500);
                }
            }
            $property->publish_status = "PENDING";
            $property->request_status_type = "PUBLISHED";
            $property->updated_by_id = auth()->id();
            $property->withoutApproval()->save();

            //  // Adding is Live here so we can know from frontend that this listing came for published
            // $propertyObj2 = Property::find($request->id);
            // $propertyObj2->publish_status = "PUBLISHED";
            // $propertyObj2->is_live = 1;
            // $propertyObj2->save();

            PropertyNote::create([
                "property_id" => $request->id,
                "note" => "Request Publish: " . $request->request_note,
                "created_by_id" => auth()->id(),
            ]);
            foreach ($portals as $key => $portal) {
                PropertyPortal::updateOrCreate(["property_id" => $property->id, "portal_id" => $portal->id]);
            }

            event(new PublishRequested($property));
            $adminEmail = getAdminEmail();
            if (!empty($adminEmail)) {
                //Mail::to($adminEmail)->send(new PropertyPublishRequestMail($property, auth()->user()));
            }
            $result = array('status' => '1', 'msg' => "Listing publish request sent successfully.");
            if ($request->return_array) {
                return $result;
            }
            return json_encode($result);
        } catch (\Exception $exception) {
            info($exception);
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            return json_encode($result);
        }
    }

    public function unpublishRequest(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:properties,id',
            'request_note' => 'required|string',
        ]);

        try {

            $property = Property::find($request->id);
            $propertyObj2 = Property::find($request->id);
            // Checking if the person has access to unpublish this listing
            $notAdmin = auth()->user()->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);

            $childrenIds = [];
            if ($notAdmin) {
                $childrenIds = getUserChildrenIds(auth()->id());
                if (!in_array($property->agent_id, $childrenIds)) {
                    $result = array('status' => '0', 'message' => 'You are not authorized to perform this action', 'error' => '', 'line' => '');
                    return response()->json($result, 500);
                }
            }
            $property->update([
                "updated_by_id" => auth()->id(),
                "publish_status" => "UNPUBLISHED",
            ]);
            $propertyObj2->withoutApproval()->update([
                "publish_status" => "PENDING",
                "request_status_type" => "UNPUBLISHED",
            ]);
            PropertyNote::create([
                "property_id" => $request->id,
                "note" => "Request Unpublish: " . $request->request_note,
                "created_by_id" => auth()->id(),
            ]);
            event(new UnpublishRequested($property));
            $adminEmail = getAdminEmail();
            if (!empty($adminEmail)) {

                Mail::to($adminEmail)->send(new PropertyUnpublishRequestMail($property, auth()->user()));

            }
            $result = array('status' => '1', 'msg' => "Listing unpublish request sent successfully.");
            return json_encode($result);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
            return json_encode($result);
        }
    }

    public function assignAgents(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:properties,id',
            'agents' => 'nullable|array',
            'agents.*' => 'nullable|exists:users,id',
        ]);

        try {

            $property = Property::find($request->id);
            $property->agents()->sync($request->agents ?? []);

            return response()->json(['status' => '1', 'msg' => 'Agents assigned successfully']);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
            return json_encode($result);
        }
    }




    public function dataTable(Request $request)
    {

        $columns = array(
            0 => 'updated_at',
            1 => 'id',
            2 => 'ref_no',
            3 => 'unit_no',
            4 => 'property_category_id',
            5 => 'sub_community_id',
            6 => 'bed_id',
            7 => null,
            8 => 'agent_id',
            9 => 'updated_by_id',
            10 => 'pp.price',
            11 => null,
            12 => "state",
            13 => "updated_at",
            14 => "rera_permit_number",
            15 => "rera_expiry_date",
            16 => "for",
            17 => null,
            18 => null,
        );

        $notAdmin = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent", "Primary Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        $q = TempProperty::leftJoin('temp_property_prices as pp', 'temp_properties.id', '=', 'pp.temp_property_id');

        $totalData = $q->count(); // total data count
        
        // Apply property filters
        $query = $this->applyPropertyFilters($q, $request, false);
        $query->when($notAdmin && !($request->all_listings == "true"), function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })
            ->when($notAdmin && $request->all_listings == "true", function ($q) use ($childrenIds) {
                $q->where(function ($q) use ($childrenIds) {
                    $q->whereIn("agent_id", $childrenIds)->orWhere('publish_status', "POCKET")->orWhere('is_live', true);
                });
            })
            ->when(auth()->user()->hasRole("Admin - Abu Dhabi") && $request->all_listings == "false", function ($q) {
                $q->where("region_id", 2);
            });
        $totalFiltered = $query->count();

        $data = $this->applyPropertyFilters($q, $request, true) // Actual Data Filter
            ->when(auth()->user()->hasRole("Agent") && !($request->all_listings == "true"), function ($q) use ($childrenIds) {
                $q->where("agent_id", $childrenIds);
            })
            ->when($notAdmin && $request->all_listings == "true", function ($q) use ($childrenIds) {
                $q->where(function ($q) use ($childrenIds) {
                    $q->whereIn("agent_id", $childrenIds)->orWhere('publish_status', "POCKET")->orWhere('is_live', true);
                });
            })
            ->with('propertyCategory', 'community', 'subCommunity', 'communityProperty', 'propertyCategory',
                'agent', 'createdBy', 'updatedBy', 'portals.portal', 'propertyStatus', 'bed', 'owner')
            ->offset($request['start'])
            ->limit($request['length'])
            ->select('temp_properties.*', 'pp.price')
            ->orderBy($columns[$request["order"][0]["column"]],
                $request["order"][0]["dir"])
            ->when(auth()->user()->hasRole("Admin - Abu Dhabi") && $request->all_listings == "false", function ($q) {
                $q->where("region_id", 2);
            })
            ->get();
        $json_data = array(
            "draw" => intval($request['draw']),
            "recordsTotal" => $totalFiltered,
            "recordsFiltered" => $totalFiltered,
            "data" => $data,
        );
        echo json_encode($json_data);
    }

    public function exportExcel(Request $request)
    {
        $request->validate([
            'columns' => 'required|array',
            'filters' => 'nullable|array',
            'archived' => 'nullable|string',
        ]);
        if(isset($request->archived) && $request->archived == "on"){
            $request->archived = true;
        }else{
            $request->archived = false;
        }
        return Excel::download(new PropertyExport($request->columns, $request), "listings" . time() . ".xlsx");

    }

    public function __getActivityLog(Request $request)
    {
        $params = new Request();
        $params->replace($request->all());
        $res = (new AjaxController())->activityLog($params);
        if ($res->isEmpty()) {
            return response()->json(['data' => []]);
        }

        if ($res?->original['data'] != null) {
            $datas = [];
            foreach ($res->original['data'] as $data) {
                $properties['old'] = isset($data->properties['old']) ? propertyColumnsReadable($data?->properties['old']) : [];
                $properties['attributes'] = isset($data->properties['attributes']) ? propertyColumnsReadable($data?->properties['attributes']) : [];
                $data->properties = $properties;
                $datas[] = $data;

            }
        }
        return response()->json(['data' => $datas]);
    }

    public function getActivityLog(Request $request)
    {
        //    $request->validate([
        //         'subject_id' => 'required|exists:properties,id',
        //         'subject_model' => 'required|string',
        //     ]);
        if ($request->subject_id == null) {
            $datas = [];
            $datas = array_reverse($datas);
            return view('admin.listing.includes.partials._activity_log')
                ->with('activities', $datas);
        }
        $params = new Request();
        $params->replace($request->all());
        $res = (new AjaxController())->activityLog($params);
        if ($res->isEmpty()) {
            return response()->json(['data' => []]);
        }

        if ($res?->original['data'] != null) {
            $datas = [];
            foreach ($res->original['data'] as $data) {
                $properties['old'] = isset($data->properties['old']) ? propertyColumnsReadable($data?->properties['old']) : [];
                $properties['attributes'] = isset($data->properties['attributes']) ? propertyColumnsReadable($data?->properties['attributes']) : [];
                $data->properties = $properties;
                $datas[] = $data;

            }
        }
        $datas = array_reverse($datas);
        return view('admin.listing.includes.partials._activity_log')
            ->with('activities', $datas);
    }

    public function logActivity($model, $dirty, $message)
    {

    }

    public function archive(Request $request)
    {
        // dd($request);
        $request->validate([
            'id' => 'required|exists:properties,id',
            'archive_note' => 'required',
        ]);

        try {
            $property = Property::find($request->id);
            $property->publish_status = "ARCHIVED";
            $property->is_live = false;
            $property->updated_by_id = auth()->id();

            $property->withoutApproval()->save();

            PropertyNote::create([
                "property_id" => $request->id,
                "note" => "Archive: " . $request->archive_note,
                "created_by_id" => auth()->id(),
            ]);
            $result = array('status' => '1', 'msg' => "Listing archived successfully.");
            return json_encode($result);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            return json_encode($result);
        }
    }

    public function getAgents(Request $request)
    {
        $notAdmin = auth()->user()->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }
        if ($request->val == 1) {
            $data['agents'] = User::whereHas("roles", function ($q) {
                $q->whereIn("name", ["Agent", "Line Manager", "Team Leader", "Sales Director", "Head of Secondary", "CEO"]);
            })->when(!$notAdmin, fn($q) => $q->whereIn("parent_id", $childrenIds)->orWhere('id', auth()->user()->id))->where("status", true)->get();

        } else {
            $data['agents'] = User::whereHas("roles", function ($q) {
                $q->whereIn("name", ["Agent", "Line Manager", "Team Leader", "Sales Director", "Head of Secondary", "CEO"]);
            })->when($notAdmin, fn($q) => $q->whereIn("parent_id", $childrenIds)->orWhere('id', auth()->user()->id))->where("status", true)->get();

        }

        return response()->json($data);
    }
    public function bulkMarkedImages(Request $request)
    {
        //  dd($request->all());
        $request->validate([
            'id' => 'required|exists:properties,id',
        ]);
        $property = Property::where('id', $request->id)->first();
        $mediaFiles = $property->photos;
        $zip = new \ZipArchive();
        $zipName = 'property_images_' . $property->id . '.zip';

        // $zipName = 'property_images.zip';
        $zipPath = public_path('app/' . $zipName);

        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {

            foreach ($mediaFiles as $media) {

                $fileName = basename($media->path);
                if (file_get_contents($media->path)) {
                    $fileContent = file_get_contents($media->path);
                    $zip->addFromString($fileName, $fileContent);
                }
            }
            $zip->close();
            // return response()->download($zipPath)->deleteFileAfterSend();
            return response()->json(['message' => 'Zip file created successfully.', 'downloadUrl' => 'app/' . $zipName, 'zipName' => $zipName]);

        } else {
            return response()->json(['message' => 'Failed to create zip file.'], 500);
        }

    }

    public function bulkImages(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:properties,id',
        ]);
        $property = Property::where('id', $request->id)->first();
        $mediaFiles = $property->photos;
        $zip = new \ZipArchive();
        $zipName = 'Original_Property_images_' . $property->id . '.zip';
        $zipPath = public_path('app/' . $zipName);
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            foreach ($mediaFiles as $media) {
                if (!empty($media) && !isset($media['error'])) {
                    $fileName = basename($media->original);

                    if ($media->original) {
                        try {
                            $fileContent = file_get_contents($media->original);
                        } catch (\Throwable $th) {
                            continue;
                        }
                    }
                    if (isset($fileContent)) {
                        $zip->addFromString($fileName, $fileContent);
                    }

                }

            }
            $zip->close();
            return response()->json(['message' => 'Zip file created successfully.', 'downloadUrl' => 'app/' . $zipName, 'zipName' => $zipName]);

        } else {
            return response()->json(['message' => 'Failed to create zip file.'], 500);
        }
    }

    public function bulkDownloadNotForMarketingImagesBackup(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:properties,id',
        ]);

        $property = Property::where('id', $request->id)->first();
        $mediaFiles = $property->photos;
        $zip = new \ZipArchive();
        $zipName = 'Original_Property_images_' . $property->id . '.zip';
        $zipPath = public_path('app/' . $zipName);

        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            foreach ($mediaFiles as $media) {
                if (!empty($media) && !isset($media['error'])) {
                    $fileName = basename($media->original);

                    if ($media->original) {
                        try {
                            $fileContent = file_get_contents($media->original);

                            // Create an Intervention Image instance from the file content
                            $image = Image::make($fileContent);

                            // Load the watermark image
                            $watermarkPath = public_path('assets/images/logo/marketing.png');
                            $watermark = Image::make($watermarkPath);

                            // Resize the watermark image to a percentage of the original image
                            $watermark->resize($image->width() * 0.5, null, function ($constraint) {
                                $constraint->aspectRatio();
                            });

                            // Insert the watermark at the center of the image
                            $image->insert($watermark, 'center');

                            // Get the image content with the watermark
                            $fileContentWithWatermark = $image->encode();

                            // Add the image with watermark to the zip file
                            $zip->addFromString($fileName, $fileContentWithWatermark);

                        } catch (\Throwable $th) {
                            info($th->getMessage());
                            continue;
                        }
                    }
                }
            }

            $zip->close();
            return response()->json(['message' => 'Zip file created successfully.', 'downloadUrl' => 'app/' . $zipName, 'zipName' => $zipName]);

        } else {
            return response()->json(['message' => 'Failed to create zip file.'], 500);
        }
    }
    public function bulkDownloadNotForMarketingImages(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:properties,id',
        ]);

        $property = Property::where('id', $request->id)->first();
        $mediaFiles = $property->photos;
        $zip = new \ZipArchive();
        $zipName = 'Original_Property_images_' . $property->id . '.zip';
        $zipPath = public_path('app/' . $zipName);

        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            foreach ($mediaFiles as $media) {
                if (!empty($media) && !isset($media['error'])) {
                    $fileName = basename($media->original);

                    if ($media->original) {
                        try {
                            $fileContent = file_get_contents($media->original);

                            // Create an Intervention Image instance from the file content
                            $image = Image::make($fileContent);

                            // Add the text watermark
                            // $textWatermark = 'Not for Marketing';
                            // $image->text($textWatermark, $image->width() / 2, $image->height() / 2, function ($font) {
                            //     $font->file(public_path('assets/fonts/boxicons.ttf')); // Replace with the path to your font file
                            //     $font->size(200);
                            //     $font->color('#000000'); // Replace with the color you want for the text
                            //     $font->align('center');
                            //     $font->valign('middle');
                            // });

                            // // Get the image content with the text watermark
                            // $fileContentWithWatermark = $image->encode();

                            $textWatermark = 'NOT FOR MARKETING';
                            $image->text($textWatermark, $image->width() / 2, $image->height() / 2, function ($font) {
                                $font->file(public_path('/assets/fonts/helvetica-neue-normal.ttf')); // Replace with the path to your font file
                                $font->size(48);
                                $font->color('#ffffff'); // Replace with the color you want for the text
                                $font->align('center');
                                $font->valign('middle');
                            });

                            // Get the image content with the text watermark
                            $fileContentWithWatermark = $image->encode();

                            // Add the image with text watermark to the zip file
                            //$zip->addFromString($fileName, $fileContentWithWatermark);

                            // Add the image with text watermark to the zip file
                            $zip->addFromString($fileName, $fileContentWithWatermark);

                        } catch (\Throwable $th) {
                            info($th->getMessage());
                            continue;
                        }
                    }
                }
            }

            $zip->close();
            return response()->json(['message' => 'Zip file created successfully.', 'downloadUrl' => 'app/' . $zipName, 'zipName' => $zipName]);

        } else {
            return response()->json(['message' => 'Failed to create zip file.'], 500);
        }
    }

    public function bulkDocuments(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:properties,id',
        ]);
        $property = Property::where('id', $request->id)->first();
        $signedListingForm = $property->signedListingForm;
        $floorPlan = $property->floorPlan;
        $titleDeed = $property->titleDeed;
        $ownerPassportCopy = $property->ownerPassportCopy;
        $otherDocuments = $property->otherDocuments;

        $zip = new \ZipArchive();
        $zipName = 'Property_documents_' . $property->id . '.zip';
        $zipPath = public_path('app/' . $zipName);
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {

            foreach ($otherDocuments as $media) {
                if (!empty($media) && !isset($media['error'])) {
                    $fileName = $media->title . '_' . basename($media->path);
                    $fileContent = file_get_contents($media->path);
                    $zip->addFromString($fileName, $fileContent);
                }
            }

            if (!empty($floorPlan) && !isset($floorPlan['error'])) {
                $fileName = 'floorplan_' . basename($floorPlan->path);
                // $fileName = 'floorPlan';
                $fileContent = file_get_contents($floorPlan->path);

                $zip->addFromString($fileName, $fileContent);
            }
            // dd($signedListingForm, $titleDeed, $ownerPassportCopy);
            if (!empty($signedListingForm) && !isset($signedListingForm['error'])) {
                $fileName = $signedListingForm->title . '_' . basename($signedListingForm->path);
                // $fileName = 'signedListingForm';
                $fileContent = file_get_contents($signedListingForm->path);

                $zip->addFromString($fileName, $fileContent);
            }
            if (!empty($titleDeed) && !isset($titleDeed['error'])) {
                $fileName = $titleDeed->title . '_' . basename($titleDeed->path);
                // $fileName = 'titledeed';
                $fileContent = file_get_contents($titleDeed->path);
                $zip->addFromString($fileName, $fileContent);
            }
            if (!empty($ownerPassportCopy) && !isset($ownerPassportCopy['error'])) {
                $fileName = $ownerPassportCopy->title . '_' . basename($ownerPassportCopy->path);
                // $fileName = 'ownerpassportcopy';
                $fileContent = file_get_contents($ownerPassportCopy->path);
                $zip->addFromString($fileName, $fileContent);
            }

            $zip->close();
            return response()->json(['message' => 'Zip file created successfully.', 'downloadUrl' => 'app/' . $zipName, 'zipName' => $zipName]);

        } else {
            return response()->json(['message' => 'Failed to create zip file.'], 500);
        }
    }

    //

    public function getEvents(Request $request)
    {
        $notAdmin = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }
        $key = $request->key;

        $data = Event::where($key, $request->id)
            ->when($request->type != "All", function ($q) {
                $q->where("type", \request("type"));
            })->when($notAdmin, function ($q) use ($childrenIds) {
            $q->where(function ($q1) use ($childrenIds) {
                $q1->whereIn("agent_id", $childrenIds);
                $q1->orWhereHas("attendees", fn($q2) => $q2->whereIn("user_id", $childrenIds));
            });

        })->with("attendees", "contact", "lead", "type")->get();

        return response([
            "data" => $data,
        ]);
    }
    public function refuseProperty(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'id' => 'required|exists:properties,id',
            'refuseReason' => 'required',
            'refuseAttachment' => 'nullable|mimes:png,jpg,jpeg',
        ]);

        $property = Property::find($request->id);

        $property->state = "REFUSED";
        $property->publish_status = "REFUSED";
        $property->withoutApproval()->save();
        $url = null;
        if ($request->file("refuseAttachment")) {
            $path = $this->uploadDocument($request->file("refuseAttachment"), "Notes");
            $url = $path;
        }

        activity()
            ->performedOn($property)
            ->causedBy(auth()->user())
            ->event('updated')
            ->log(auth()->user()->first_name . ' has Refused 🚫 listing changes on ' . $property->ref_no);

        $message = "<b>AX Secondary CRM</b>\nOops! Your changes on  property reference number  <b>" . $property->ref_no . "</b> have been <b>Refused</b> 🚫. Kindly review the note provided below for reference.\n";
        $message .= "\n<b> Reason: </b> ";
        $message .= "\n " . $request->refuseReason;
        $message .= "\n\nRegards,\n" . auth()->user()->first_name;
        $message .= "\n—————————————————";

        $noteMessage = str_replace("\n", "<br>", $message);
        PropertyNote::create([
            "property_id" => $request->id,
            "note" => $noteMessage,
            "url" => $url,
            "created_by_id" => auth()->id(),
        ]);

        $chat_ids = [$property?->agent?->telegram_chatid];

        $this->sendTelegramMessageWithAttachment($message, $chat_ids, $url, 'notification_bot');

        return response()->json(['status' => 1, 'msg' => 'Listing Refused'], 200);
    }
    public function approveProperty(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'id' => 'required|exists:properties,id',
        ]);

        if (!auth()->user()->hasPermissionTo('Approve Listing') && (!auth()->user()->hasRole("Super Admin"))) {
            return response()->json(['status' => 0, 'msg' => 'You do not have permission to perform this action'], 200);
        }

        $property = Property::find($request->id);
        if(isset($request->duplicate) && $request->duplicate == 'true'){
            $duplicates = 0;
            if($property->duplicated_from){
                $duplicates = Property::where('duplicated_from',$property->duplicated_from)->whereNot('ref_no',$property->ref_no)->count();
                // $duplicates = PropertyNote::where('note', 'like', '%Listing created as copy of ' . $property->duplicated_from . '%')->count();
                // $duplicates = $duplicates - 1;
            }
            $lastId = $property->ref_no ?? null;
            if ($lastId && preg_match('/(.*?)-r\d+$/', $lastId, $matches)) {
                $lastId = $matches[1];
            }

            if (!preg_match('/-r\d$/', $property->ref_no)) {
                $property->ref_no = $property->ref_no . '-r' . ($duplicates+1);
            }

            $property->withoutApproval()->save();
            $property = Property::find($request->id);
        }

        PropertyMedia::where('property_id', $request->id)->update(['media_changed' => null]);
        PropertyMedia::onlyTrashed()
            ->where('property_id', $request->id)
            ->update(['media_changed' => null]);
        $property->publish_status = $property->request_status_type ? $property->request_status_type : $property->publish_status;
        $message = null;
        if ($property->request_status_type == 'PUBLISHED') {
            if (!$property->is_live || $property->published_date == null) {

                if ($property->request_status_type == "PUBLISHED" && $property->published_date==null) {
                    $property->published_date = now();
                }

                // dd($property->request_status_type,$property->publish_status);
                $message = "<b>AX Secondary CRM</b>\nCongratulations! Your property reference number <b>" . $property->ref_no . "</b> have been <b>Approved</b> ✅ and Published. Best of luck with potential leads!\n";
                $message .= "\nRegards,\n" . auth()->user()->first_name;
                $message .= "\n—————————————————";
                PropertyNote::create([
                    "property_id" => $request->id,
                    "note" => $message,
                    "created_by_id" => auth()->id(),
                ]);

                // $duplicates = $this->checkDuplicateListings($request->id);

                // foreach ($duplicates as $duplicatess) {

                //     $duplicatess->need_to_archive = true;
                //     $duplicatess->by_property_id = $request->id;
                //     $duplicatess->withoutApproval()->save();
                //         PropertyNote::create([
                //         "property_id" => $duplicatess->id,
                //         "note" => "Archive: Duplicate listing of " . $property->ref_no,
                //         "created_by_id" => auth()->id(),
                //     ]);
                // }

                // dd($duplicates);

            } else {
                $property->republished_date = now();
                // On approve remove the revisions as well
                $message = "<b>AX Secondary CRM</b>\nYour changes on property reference number <b>" . $property->ref_no . "</b> have been <b>Approved</b> ✅.\n";
                $message .= "\nRegards,\n" . auth()->user()->first_name;
                $message .= "\n—————————————————";
                PropertyNote::create([
                    "property_id" => $request->id,
                    "note" => $message,
                    "created_by_id" => auth()->id(),
                ]);
            }
            $property->is_live = true;

            // $duplicates = $this->checkDuplicateListings($request->id);

            // foreach ($duplicates as $duplicatess) {

            //     $duplicatess->need_to_archive = true;
            //     $duplicatess->by_property_id = $request->id;
            //     $duplicatess->withoutApproval()->save();
            //     PropertyNote::create([
            //         "property_id" => $duplicatess->id,
            //         "note" => "Archive: Duplicate listing of " . $property->ref_no,
            //         "created_by_id" => auth()->id(),
            //     ]);
            // }

        }
        if ($property->request_status_type == 'POCKET') {
            $message = "<b>AX Secondary CRM</b>\nYour changes on property reference number <b>" . $property->ref_no . "</b> have been <b>Approved</b> ✅.\n";
            $message .= "\nRegards,\n" . auth()->user()->first_name;
            $message .= "\n—————————————————";
            PropertyNote::create([
                "property_id" => $request->id,
                "note" => $message,
                "created_by_id" => auth()->id(),
            ]);
        }
        $property->withoutApproval()->save();
        activity()
            ->performedOn($property)
            ->causedBy(auth()->user())
            ->event('updated')
            ->log(auth()->user()->first_name . ' has Approved ✅ listing changes on ' . $property->ref_no);

        $chat_ids = [$property?->agent?->telegram_chatid];
        if ($message) {
            $this->sendTelegramMessageWithAttachment($message, $chat_ids, null, 'notification_bot');
        }

        return response()->json(['status' => 1, 'msg' => 'Listing Approved'], 200);
    }

    private function checkDuplicateScore($property, $value)
    {
        $score = 0;
        if ($property?->unit_no == $value->unit_no) {
            $score += 20;
        }
        if ($property->community_id == $value->community_id) {
            $score += 20;
        }
        if ($property->sub_community_id == $value->sub_community_id) {
            $score += 20;
        }
        if ($property->community_property_id == $value->community_property_id) {
            $score += 20;
        }
        if ($property->for == $value->for) {
            $score += 20;
        }
        //$titleSimilarity = wordSimilarity($property->title, $value->title);
        //$score += round($titleSimilarity) * 10;
        // if ($value->publish_status == 'ARCHIVED' && $score > 80) {
        //     $score = 80;
        // }
        if ($score > 100) {
            $score = 100;
        }
        return $score;

    }

    public function checkDuplicateListings($id)
    {
        //add permit, remove community,
        $duplicate = [];
        $property = Property::where('id', $id)->with('agent', 'community', 'subCommunity', 'communityProperty')->first();
        $datetime = Carbon::parse($property->created_at);
        $properties1 = Property::where('id', '!=', $id)
            ->where('unit_no', $property->unit_no)
        // ->where('rera_permit_number', $property->rera_permit_number)
            ->where('for', $property->for)
        // ->when($property->sub_community_id, function ($q) use ($property) {
        //     $q->where('sub_community_id', $property->sub_community_id);
        // })
            ->when($property->community_property_id, function ($q) use ($property) {
                $q->where('community_property_id', $property->community_property_id);
            })
        //->where('created_at', '<', $datetime)
        // ->whereIn('publish_status', ['PUBLISHED', 'POCKET','PENDING','ARCHIVED','DRAFT'])
            ->with('agent', 'community', 'subCommunity', 'communityProperty')
            ->get();
        $properties2 = Property::where('id', '!=', $id)
            ->where('unit_no', $property->unit_no)
            ->where('for', $property->for)
            ->when($property->sub_community_id, function ($q) use ($property) {
                $q->where('sub_community_id', $property->sub_community_id);
            })
        // ->when($property->community_property_id, function ($q) use ($property) {
        //     $q->where('community_property_id', $property->community_property_id);
        // })
            ->where('created_at', '<', $datetime)
        // ->whereIn('publish_status', ['PUBLISHED', 'POCKET','PENDING','ARCHIVED','DRAFT'])
            ->with('agent', 'community', 'subCommunity', 'communityProperty')
            ->get();

        $unPublishedArchived = Property::where('id', '!=', $id)
            ->when($property->unit_no, function ($q) use ($property) {
                $q->where('unit_no', $property->unit_no);
            })
            ->where('for', $property->for)
            ->when($property->community_id, function ($q) use ($property) {
                $q->where('community_id', $property->community_id);
            })
            ->when($property->sub_community_id, function ($q) use ($property) {
                $q->where('sub_community_id', $property->sub_community_id);
            })
            ->when($property->community_property_id, function ($q) use ($property) {
                $q->where('community_property_id', $property->community_property_id);
            })
        // ->whereIn('publish_status', ['UNPUBLISHED', 'ARCHIVED', 'ARCHIVED','DRAFT'])
            ->where('created_at', '<', $datetime)
        //   ->where('community_property_id',$property->community_property_id)
            ->with('agent', 'community', 'subCommunity', 'communityProperty')->get();

        foreach ($properties1 as $key => $value) {
            $value->duplicate_score = $this->checkDuplicateScore($property, $value);
            $duplicate[] = $value;
        }
        foreach ($properties2 as $key => $value) {
            $value->duplicate_score = $this->checkDuplicateScore($property, $value);
            $duplicate[] = $value;
        }

        // foreach ($unPublishedArchived as $key => $value) {
        //     $value->duplicate_score = $this->checkDuplicateScore($property, $value);
        //     $duplicate[] = $value;
        // }

        $duplicate = collect($duplicate)->sortByDesc('duplicate_score')->unique('id')->values()->all();

        //check if duplicate is more than 0
        if (count($duplicate) > 0) {
            return $duplicate;
            return Response::json(['status' => 1, 'property' => $property, 'data' => $duplicate]);
            // return ids of all duplicates
            $ids = [];
            foreach ($duplicate as $key => $value) {
                $ids[$value->id] = $value->duplicate_score;

            }
            return $ids;

        } else {
            return [];
            return Response::json(['status' => 0, 'property' => $property, 'data' => [], 'message' => 'No duplicate found']);
        }

    }

    public function getLocationData($params)
    {
        /* This function can be used to fetch the location ids for store and update */

        if ($params['location_type'] == "community_property") {
            $communityProperty = CommunityProperty::find(str_replace("?community_property", "", $params['location_id']));
            $params["community_property_id"] = $communityProperty->id ?? null;
            $params["sub_community_id"] = $communityProperty->sub_community_id ?? null;
            $params["community_id"] = $communityProperty->community_id ?? null;
            $params["region_id"] = $communityProperty->region_id ?? null;
        } else {
            $subCommunity = SubCommunity::find(str_replace("?sub_community", "", $params['location_id']));
            $params["community_property_id"] = null;
            $params["sub_community_id"] = $subCommunity->id ?? null;
            $params["community_id"] = $subCommunity->community_id ?? null;
            $params["region_id"] = $subCommunity->region_id ?? null;
        }
        return $params;
    }

    public function notAdmin()
    {
        $notAdmin = auth()->user()->hasRole(["Primary Sales Director", "Primary Sales Manager", "Sales Director", "Line Manager", "Team Leader", "Agent", "Primary Agent"]);
        return $notAdmin;
    }

    public function getChangeInListing(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'id' => 'required|exists:properties,id',
            'key' => 'required|string',
            'value' => 'required|string',
        ]);

        $key = $request->key;
        $value = $request->value;
        $id = $request->id;

        if ($request->key == 'price') {
            $value = str_replace(',', '', $request->value);
            $p_price = PropertyPrice::where('property_id', $request->id)->first();
            if ($p_price) {
                $price_val = $p_price->price;
                if ($price_val == $value) {
                    return response()->json(['status' => 0, 'msg' => 'No change in listing ' . $key . ' ' . $value], 200);
                } else {
                    return response()->json(['status' => 1, 'msg' => 'Change in listing ' . $key . ' ' . $value], 200);
                }
            } else {
                return response()->json(['status' => 1, 'msg' => ''], 200);
            }
        }
        $property = Property::find($request->id);

        if ($request->key == 'location_id') {
            $location_id = explode('?', $request->value);
            $typ = $location_id[1] . '_id';
            $find_id = $location_id[0];

            if ($property->$typ == $find_id) {
                return response()->json(['status' => 0, 'msg' => 'No change in listing ' . $key . ' ' . $value], 200);
            } else {
                return response()->json(['status' => 1, 'msg' => 'Change in listing ' . $key . ' ' . $value], 200);
            }
        }

        $property_val = $property->$key;
        if ($property_val == $value) {
            return response()->json(['status' => 0, 'msg' => 'No change in listing ' . $key . ' ' . $value], 200);
        } else {
            return response()->json(['status' => 1, 'msg' => 'Change in listing ' . $key . ' ' . $value], 200);
        }
    }

    public function getPropertyTimeline(Request $request)
    {
        $cycles = PropertyCycle::where('property_id', $request->id)->get()->groupBy('ref_no');
        return view('admin.listing.includes.partials._timeline')->with('cyclesGrouped', $cycles);
    }

    // public function getPropertyActivityLog(Request $request){

    //     return view('admin.listing.includes.partials._activity_log')->with('activities', $activities);
    // }

    public function requestDownloadZip(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:properties,id',
        ]);

        try {

            $property = Property::find($request->id);

            $property->load([
                "owner:id,first_name,last_name,passport,passport_expiry",
                "propertyStatus:id,title",
                "community:id,name",
                "bed:id,name",
                "bath:id,name",
                "price:id,price",
                "agent:id,name",
            ]);

            $folder = createPropertyFolder($property);

            if ($folder !== false) {
                $folderPath = "file-manager/" . $property->ref_no;
                $zipFileName = $property->ref_no . time();

                $fileUrls = [];

                if ($request->boolean("marketing-form-a-leasing-fillable")) {
                    $marketingFormFile = FolderMedia::where("data->type", "marketing-form-a-leasing-fillable")
                        ->where("data->property_id", $property->id)->latest()->first();

                    if (is_null($marketingFormFile)) {
                        $data = $this->prepareMarketingFormData($property);

                        $name = "Marketing Form A - Leasing Fillable generated by " . auth()->user()->name . " on " . date("Y-m-d h-i-s");
                        $slug = \Illuminate\Support\Str::slug($name);

                        $path = $this->fillAndGenerateMarketingFormALeasingFillable($data, $slug, "file-manager/temp-files");
                        if (is_null($path)) {
                            return response()->json(['status' => '0', 'msg' => 'Unable to generate file.']);
                        }
                        $fileUrls[] = $path["path"];
                    } else {
                        $fileUrls[] = $marketingFormFile->path;
                    }
                }

                $folderMedia = FolderMedia::whereIn("id", $request->folder_media ?? [])->get();

                foreach ($folderMedia as $media) {
                    $fileUrls[] = $media->path;
                }

                $propertyMedia = PropertyMedia::whereIn("id", $request->property_media ?? [])->get();
                foreach ($propertyMedia as $media) {
                    $filePaths[] = $media->path;
                }

                if ($request->boolean("photos")) {
                    $propertyMedia = PropertyMedia::where("property_id", $request->id)->where("type", "Photos")->get();
                    foreach ($propertyMedia as $media) {
                        $fileUrls[] = $media->path;
                    }
                }

                $filePath = $this->makeZipFromUrls($filePaths, $folderPath, $zipFileName);

                if (is_null($filePath)) {
                    return json_encode(array('status' => '0', 'msg' => 'Something went wrong!!'));
                }

                FolderMedia::create([
                    "folder_id" => $folder["id"],
                    "type" => getFolderMediaTypeByExtension("zip"),
                    "title" => "Zip File requested by " . auth()->user()->name,
                    "path" => $filePath["path"],
                    "disk" => $filePath["disk"],
                    "extension" => "zip",
                    "size" => $filePath["size"] >> 10, //Size in KB
                    "created_by_id" => auth()->id(),
                ]);
                $result = array('status' => '1', 'msg' => "Listing zip request sent successfully.", "downloadUrl" => $filePath["path"]);
                return json_encode($result);
            }
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            return json_encode($result);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
            return json_encode($result);
        }
    }

    public function filterHelper(Request $request)
    {
        $data = [];
        $operators = config('configurations.filters.Listing.' . $request->type . '.operators');

        // dd($operators);
        if ($request->type == 'created_from' || $request->type == 'updated_from' || $request->type == 'dob_search') {
            $data = [];
        }

        if ($request->type == 'baths') {
            $data = \App\Models\Bath::all();

        }
        if ($request->type == 'beds') {
            $data = \App\Models\Bed::all();
        }

        if ($request->type == 'statuses') {
            $data = \App\Models\Property::PUBLISH_STATUS_SELECT;
        }
        if ($request->type == 'sources') {
            $data = \App\Models\Source::where('status', '1')->get();
        }
        if ($request->type == 'medium') {
            $data = \App\Models\Medium::where('status', '1')->get();
        }
        if ($request->type == 'types') {
            $data = \App\Models\Type::where('status', '1')->get();
        }
        if ($request->type == 'portals') {
            $data = Portal::where('status', true)->get();
        }
        if ($request->type == 'regions') {
            $data = Region::where('status', '1')->get();
        }
        if ($request->type == 'property_statuses') {
            $data = PropertyStatus::where('status', '1')->get();
        }
        if ($request->type == 'for') {
            $data = \App\Models\Property::FOR_SELECT;
        }
        if ($request->type == 'permit_type') {
            $data = \App\Models\Property::PERMIT_TYPE_SELECT;
        }
        if ($request->type == 'furnished') {
            $data = \App\Models\Property::FURNISHED_SELECT;
        }
        if ($request->type == 'completion_status') {
            $data = \App\Models\Property::COMPLETION_STATUS_SELECT;
        }

        if ($request->type == 'property_request_status') {
            $data = \App\Models\Property::PUBLISH_REQUEST_SELECT;
        }
        if ($request->type == 'state') {
            $data = \App\Models\Property::PROPERTY_STATE_SELECT;
        }
        if ($request->type == 'unit_type') {
            $data = \App\Models\PropertyCategory::where('status', 1)->get();
        }

        if ($request->type == 'agents' || $request->type == 'previous_agents') {

        }

        if ($request->type == 'updated_by') {

        }

        if ($request->type == 'managers') {

        }

        return response()->json(['status' => '1', 'msg' => 'Data Found', 'data' => $data, 'operators' => $operators ?? []]);
    }

    public function filterOpener(Request $request)
    {
        $filter = Filter::find($request->filter_id);
        $request = new Request();
        $request->replace(json_decode($filter->request, true));

        return $request;

        dd(route('admin.listing.properties.filterOpenerRedirect', ['id' => $request->filter_id]));
    }

//     public function filterOpenerRedirect($id){
//         $filter = Filter::find($id);
//         // now add this $filter data to request and call the filter function
//         $request = new Request();
//         // add $filter->request to $request this is json data
//         $request->replace(json_decode($filter->request, true));
// // remove search_ from each key just once if its 2 times
//         return $request;

//     }

    public function filterOpenerRedirect($id)
    {
        $filter = Filter::find($id);
        $request = new Request();
        $requestData = json_decode($filter->request, true);

        $keyCounts = array_count_values(array_keys($requestData));

        // Remove prefix "search_" from keys that occur exactly twice
        foreach ($requestData as $key => $value) {
            if (substr($key, 0, 7) === "search_" && $keyCounts[$key] === 2) {
                $newKey = substr($key, 7);
                $requestData[$newKey] = $value;
                unset($requestData[$key]);
            }
        }

        $request->replace($requestData);
        return $request;
    }
    public function publishRequestByAdmin(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'unpublish_note' => 'required|string',
        ]);

        $property = Property::find($request->property_id);
        if ($property->publish_status == "UNPUBLISHED") {
            return response()->json(['status' => 0, 'msg' => 'Property is already unpublished.'], 200);
        }
        $old_status = $property->publish_status;
        $property->publish_status = "UNPUBLISHED";
        $property->is_live = false;
        if ($property->unpublished_date == null) {
            $property->unpublished_date = now();
        }
        $property->admin_unpublish_reason = $request->unpublish_note;
        $reraNumber = $property->rera_permit_number;

        $note = "Property unpublished by " . auth()->user()->name . " due to " . $request->unpublish_note;
        $arr = [
            'attributes' => [
                'publish_status' => 'UNPUBLISHED',
            ],
            'old' => [
                'publish_status' => $old_status,
            ],
            'property_id' => $request->property_id,
        ];

        if ($request->clear_permit_number == 1) {
            $property->rera_permit_number = null;
            $note = "Property unpublished by " . auth()->user()->name . " due to " . $request->unpublish_note . " and RERA Permit Number cleared ( " . $reraNumber . " ).";
            $dirty = $property->getDirty() ?? [];
            $arr = [
                'attributes' => [
                    'rera_permit_number' => '',
                    'publish_status' => 'UNPUBLISHED',
                ],
                'old' => [
                    'rera_permit_number' => $reraNumber,
                    'publish_status' => $old_status,
                ],
                'property_id' => $request->property_id,
            ];

        }

        activity()
            ->performedOn($property)
            ->causedBy(auth()->user())
            ->withProperties($arr)
            ->event('created')
            ->log(auth()->user()->first_name . ' unpublished the listing');

        $property->withoutApproval()->save();
        $property_note = PropertyNote::create([
            "property_id" => $request->property_id,
            "note" => $note,
            "created_by_id" => auth()->id(),
        ]);
        $agent = User::find($property->agent_id);
        $message = "<b>AX Secondary CRM</b>\n\n";
        $message .= "Your property reference number <b>" . $property->ref_no . "</b> has been <b>UNPUBLISHED</b>.\n\n";
        $message .= "Reason:\n\n";
        $message .= $request->unpublish_note;
        $message .= "\n\nRegards,\n";
        $message .= auth()->user()->name;

        if ($agent->telegram_chatid != null) {
            $this->sendTelegramMessageHtml($message, [$agent->telegram_chatid], null, null, 'lead_bot');
        }

        telegramNotification('Property', $property->id, $agent->telegram_chatid, $agent->id, $message);
        if (env('APP_ENV') == 'production') {
            event(new PropertyApproval($property, 'unpublished'));
        }
        return response()->json(['status' => 1, 'msg' => 'Publish request updated successfully.'], 200);
    }

    public function updateDescription()
    {
        // find all the properties with description that contains "- AX HOLIDAYS" and remove it
        $properties = Property::where('is_live', 1)->where('publish_status', 'PUBLISHED')->where('description', 'like', '%- AX HOLIDAYS%')->get();
        // dd($properties->count());
        $counter = 0;
        foreach ($properties as $property) {

            $property->description = str_replace("- AX HOLIDAYS", "", $property->description);
            $property->withoutApproval()->save();
            $counter++;
        }

        return response()->json(['status' => 1, 'msg' => $counter . ' properties updated successfully.'], 200);
    }

    public function republishRequest(Request $request)
    {

        $request->validate([
            'id' => 'required|exists:properties,id',
            'contract_file' => 'required',
        ]);

        $property = Property::find($request->id);

        $newStatus = new RepublishRequest();
        $newStatus->status = 'REPUBLISH_REQUESTED';
        $newStatus->user_id = auth()->user()->id;
        $newStatus->property_id = $request->id;
        $path = $this->uploadDocument($request->file("contract_file"), "listings/" . $property->ref_no . "/Signed Listing Form");
        $newStatus->file = $path ?? null;
        $newStatus->password = $request->contract_file_password;
        $newStatus->issue_date = $request->issue_date;
        $newStatus->expiry_date = $request->expiry_date;
        $newStatus->save();
        return response()->json(['status' => 1, 'msg' => 'Republish request updated successfully.'], 200);
    }
}
