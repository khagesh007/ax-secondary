<?php

namespace App\Http\Controllers\Api;

use App\Events\LeadAppNotification;
use App\Events\ListingLeadAppNotification;
use App\Exports\LeadsNotClickedExport;
use App\Exports\WhatsappLeadsExport;

use App\Http\Controllers\Api\V1\Reporting\StatusScreenApiController;
use App\Http\Controllers\Controller;
use App\Models\Bath;
use App\Models\GeneralSetting;
use App\Models\Bed;
use App\Models\CallLog;
use App\Models\Community;
use App\Models\CommunityProperty;
use App\Models\Contact;
use App\Models\Lead;
use App\Models\LeadCriteria;
use App\Models\LeadCriteriaLocation;
use App\Models\LeadNote;
use App\Models\LeadStatus;
use App\Models\ListingLead;
use App\Models\ListingLeadDump;
use App\Models\ListingLeadNote;
use App\Models\Medium;
use App\Models\Portal;
use App\Models\Property;
use App\Models\PropertyAmenity;
use App\Models\PropertyCategory;
use App\Models\PropertyMeta;
use App\Models\Source;
use App\Models\SubCommunity;
use App\Models\Type;
use App\Models\User;
use App\Models\UserFcm;
use App\Models\UserMeta;
use App\Traits\FileTrait;
use App\Traits\PropertyFinderTrait;
use App\Traits\TelegramTrait;
use Carbon\Carbon;
use Crypt;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\Portal\PropertyFinderService;
class ExternalApiController extends Controller
{
    use FileTrait;
    use TelegramTrait, PropertyFinderTrait;


    //================================================================================================
    //==================================== Lead Related functions ====================================
    //================================================================================================

    public function __construct()
    {
       $this->propertyFinderService = app(PropertyFinderService::class);
    }

    public function back_leadForm()
    {
        $categories = PropertyCategory::all();
        $beds       = Bed::where('status', true)->orderBy('order', 'asc')->get();
        $baths      = Bath::where('status', true)->orderBy('order', 'asc')->get();
        return view('form.leads.create_lead')
            ->with('categories', $categories)
            ->with('beds', $beds)
            ->with('baths', $baths);
    }

    public function propertyFinderLeadCreated(Request $request)
    {
        try {
            info("Lead Created from Property finder API");
            info($request->all());
            return true;
        } catch (\Throwable $th) {
            info("Something went wrong in property finder lead created");
        }
    }
    public function back_submitLead(Request $request)
    {
        $validatedData = $request->validate([
            "first_name" => "required|string",
            "last_name"  => "nullable|string",
            "mobile"     => "required",
            "for"        => "required",
            "email"      => "nullable|email",
        ]);

        if (checkBlocklist($request->mobile, Lead::class, $request)) {
            $result = ['status' => '0', 'msg' => 'This number is in Blacklist. Please do not contact with this number.', 'error' => 'This number is in Blacklist. Please do not contact with this number.'];
            return json_encode($result);
        }

        try {

            foreach (Source::get() as $source) {
                if (Hash::check($source->id, $request->source)) {
                    $source_id = $source->id;
                    break;
                }
            }

            $typeOfLead = '';
            if ($request->for == 'Residential for sale' || $request->for == 'Commercial for sale') {
                $typeOfLead = 2;
            } else if ($request->for == 'Residential for rent' || $request->for == 'Commercial for rent') {
                $typeOfLead = 1;
            }

            $lead_CRM = Source::where('name', 'CRM')->first();

            $lead                 = new Lead();
            $lead->first_name     = $request->first_name;
            $lead->last_name      = $request->last_name;
            $lead->email          = $request->email;
            $lead->mobile         = $request->mobile;
            $lead->for            = $request->for;
            $lead->type_id        = $typeOfLead;

            $lead->source_id      = $source_id ?? $lead_CRM->id ?? null;
            $lead->lead_medium_id = 2;

            if ($request->full_mobile) {
                $lead->mobile = validatePhoneNumber($request->full_mobile);
            }

            $lead->lead_status_id = 1;
            $lead->save();

            LeadNote::create([
                'lead_id' => $lead->id,
                'note'    => 'Lead Created Using External Form from IP:' . $request->ip(),
                'status'  => 1,
            ]);

            if ($request->description) {
                LeadNote::create([
                    'lead_id' => $lead->id,
                    'note'    => $request->description,
                    'status'  => 1,
                ]);
            }

            $leadCriteria = new LeadCriteria();

            $leadCriteria->lead_id     = $lead->id;
            $leadCriteria->min_bed_id  = $request->min_bed_id;
            $leadCriteria->max_bed_id  = $request->max_bed_id;
            $leadCriteria->min_bath_id = $request->min_bath_id;
            $leadCriteria->max_bath_id = $request->max_bath_id;
            $leadCriteria->min_price   = $request->min_price;
            $leadCriteria->max_price   = $request->max_price;
            if ($request->file("related_file")) {
                $path     = $this->uploadDocument($request->file("related_file"), "lead");
                $call_log = CallLog::create([
                    'lead_id'            => $lead->id,
                    'call_recording_url' => $path ?? null,
                    'call_type'          => 'RECORDING',
                    'call_started_at'    => date('Y-m-d H:i:s'),
                    'call_ended_at'      => date('Y-m-d H:i:s'),
                    'call_answered_at'   => date('Y-m-d H:i:s'),
                    'call_duration'      => 0,

                ]);
            }

            if ($leadCriteria->save()) {
                $all_comunities = '';
                if ($request->communities) {
                    $communities = $request->communities;
                    foreach ($communities as $community) {
                        $all_comunities .= Community::find($community)->name . ',';
                        $obj                   = new LeadCriteriaLocation();
                        $obj->parentable_type  = Community::class;
                        $obj->parentable_id    = $community;
                        $obj->lead_criteria_id = $leadCriteria->id;
                        $obj->save();
                    }
                }
                $all_locations = '';
                if ($request->locations) {

                    $locations = $request->locations;
                    $locations = explode(',', $locations);
                    foreach ($locations as $loc) {

                        $loc_arr = explode(':', $loc);
                        if ($loc_arr[1] == 'sub_community') {
                            $all_locations .= SubCommunity::find($loc[0])->name . ',';

                            $loc_type = SubCommunity::class;
                        } else {
                            $all_locations .= CommunityProperty::find($loc[0])->name . ',';

                            $loc_type = CommunityProperty::class;
                        }
                        $obj                   = new LeadCriteriaLocation();
                        $obj->parentable_type  = $loc_type;
                        $obj->parentable_id    = $loc_arr[0];
                        $obj->lead_criteria_id = $leadCriteria->id;
                        $obj->save();
                    }
                }
            }

            $environment = \App::environment();
            if ($environment == "production") {
                $type = '';
                if (Type::find($typeOfLead)) {
                    $type = Type::find($typeOfLead)->name;
                }
                $bed = '';
                if (Bed::find($request->min_bed_id)) {
                    $bed = Bed::find($request->min_bed_id)->name;
                    if (Bed::find($request->max_bed_id)) {
                        $bed = Bed::find($request->min_bed_id)->name . ' - ' . Bed::find($request->max_bed_id)->name;
                    }
                }

                $message = "<b>AX Secondary CRM</b>\nA Lead has been recorded by FO Administrator. Kindly assign it to an agent.\n";
                $message .= "\n<b> Property Details: </b> ";
                $message .= "\n<b> ID: </b> " . $lead->id ?? '';
                $message .= "\n<b> Type: </b> " . $type ?? '';
                $message .= "\n<b> Purpose: </b> " . $request->for;
                $message .= "\n<b> Community: </b> " . $all_comunities;
                $message .= "\n<b> Location: </b> " . $all_locations;
                $message .= "\n<b> Beds: </b> " . $bed;
                $message .= "\n<b> Budget: </b> " . formatPrice($request->min_price) . ' - ' . formatPrice($request->max_price);
                $message .= "\n\n<b> Lead Details: </b> ";
                ($request->first_name != " " ? $message .= "\n<b> Name: </b> " . $request?->first_name . ' ' . $request?->last_name : null);
                ($request->mobile ? $message .= "\n<b> Mobile: </b> " . $request?->mobile : null);
                ($request->email ? $message .= "\n<b> Email: </b> " . $request?->email : null);

                $agents = User::whereHas("roles", function ($q) {
                    $q->whereIn("name", ["Line Manager", "Team Leader", "Sales Director"]);
                })->where("status", true)->get();
                foreach ($agents as $agent) {
                    if ($agent->telegram_chatid != null && $lead->agent_id == null) {

                        $url = route('api.thirdparty.leadAssignment', [Crypt::encryptString($lead->id), Crypt::encryptString($agent->id)]);
                        Log::channel('urlLogs')->info('URL lead: Manager:' . $agent->full_name, ['id' => $lead->id, 'data' => $url]);

                        $this->sendTelegramMessageHtml($message, [$agent->telegram_chatid], $url, 'Assign Lead', 'lead_bot');
                        telegramNotification('Lead', $lead->id, $agent->telegram_chatid, $agent->id, $message);
                    }
                }
            }

            return response()->json(['status' => '1', 'msg' => 'Lead Added Successfully']);
        } catch (\Throwable $th) {
            // throw $th;
            Log::channel('discord')->error("Error in submitLead function in ExternalApiController.php " . $th->getMessage());

            return response()->json(['status' => '0', 'msg' => $th->getMessage()]);
        }
    }
    public function leadForm()
    {
        $categories = PropertyCategory::all();
        $beds       = Bed::where('status', true)->orderBy('order', 'asc')->get();
        $baths      = Bath::where('status', true)->orderBy('order', 'asc')->get();
        return view('form.leads.create_lead_v2')
            ->with('categories', $categories)
            ->with('beds', $beds)
            ->with('baths', $baths);
    }
    public function submitLead(Request $request)
    {
        $validatedData = $request->validate([
            "first_name" => "required|string",
            "last_name"  => "nullable|string",
            "mobile"     => "required",
            "for"        => "required",
            "email"      => "nullable|email",
        ]);

        if (checkBlocklist($request->mobile, Lead::class, $request)) {
            $result = ['status' => '0', 'msg' => 'This number is in Blacklist. Please do not contact with this number.', 'error' => 'This number is in Blacklist. Please do not contact with this number.'];
            return json_encode($result);
        }

        try {

            foreach (Source::orderBy('id', 'desc')->get() as $source) {
                if (Hash::check($source->id, $request->source)) {
                    $source_id = $source->id;
                    break;
                }
            }

            $medium_id = 2;
            if ($request->medium) {
                foreach (Medium::orderBy('id', 'desc')->get() as $medium) {
                    if (Hash::check($medium->id, $request->medium)) {
                        $medium_id = $medium->id;
                        break;
                    }
                }
            }

            $typeOfLead = '';
            if ($request->for == 'Residential for sale' || $request->for == 'Commercial for sale') {
                $typeOfLead = 2;
            } else if ($request->for == 'Residential for rent' || $request->for == 'Commercial for rent') {
                $typeOfLead = 1;
            }

            $lead_CRM  = Source::where('name', 'CRM')->first();
            $lead_Call = Medium::where('name', 'Call')->first();

            $lead             = new Lead();
            $lead->first_name = $request->first_name;
            $lead->last_name  = $request->last_name;
            $lead->email   = $request->email;
            $lead->mobile  = $request->mobile;
            $lead->for     = $request->for;
            $lead->type_id = $typeOfLead;

            $lead->source_id      = $source_id ?? $lead_CRM->id ?? null;
            $lead->lead_medium_id = $medium_id ?? $lead_Call->id ?? null;

            if ($request->full_mobile) {
                $lead->mobile = validatePhoneNumber($request->full_mobile);
            }

            $lead->lead_status_id = 1;
            $notification         = true;
            if ($request->ref_no) {
                $property = Property::where('ref_no', $request->ref_no)->first();
                if ($property) {
                    $notification      = false;
                    $lead->agent_id    = $property->agent_id;
                    $lead->property_id = $property->id;
                }
            }

            $lead->save();

            LeadNote::create([
                'lead_id' => $lead->id,
                'note'    => 'Lead Created Using External Form from IP:' . $request->ip(),
                'status'  => 1,
            ]);

            if ($request->description) {
                LeadNote::create([
                    'lead_id' => $lead->id,
                    'note'    => $request->description,
                    'status'  => 1,
                ]);
            }

            $leadCriteria = new LeadCriteria();

            $leadCriteria->lead_id     = $lead->id;
            $leadCriteria->min_bed_id  = $request->min_bed_id;
            $leadCriteria->max_bed_id  = $request->max_bed_id;
            $leadCriteria->min_bath_id = $request->min_bath_id;
            $leadCriteria->max_bath_id = $request->max_bath_id;
            $leadCriteria->min_price   = $request->min_price;
            $leadCriteria->max_price   = $request->max_price;
            if ($request->file("related_file")) {
                $path     = $this->uploadDocument($request->file("related_file"), "lead");
                $call_log = CallLog::create([
                    'lead_id'            => $lead->id,
                    'call_recording_url' => $path ?? null,
                    'call_type'          => 'RECORDING',
                    'call_started_at'    => date('Y-m-d H:i:s'),
                    'call_ended_at'      => date('Y-m-d H:i:s'),
                    'call_answered_at'   => date('Y-m-d H:i:s'),
                    'call_duration'      => 0,

                ]);
            }

            if ($leadCriteria->save()) {
                $all_comunities = '';
                if ($request->communities) {
                    $communities = $request->communities;
                    foreach ($communities as $community) {
                        $all_comunities .= Community::find($community)->name . ',';
                        $obj                   = new LeadCriteriaLocation();
                        $obj->parentable_type  = Community::class;
                        $obj->parentable_id    = $community;
                        $obj->lead_criteria_id = $leadCriteria->id;
                        $obj->save();
                    }
                }
                $all_locations = '';
                if ($request->locations) {

                    $locations = $request->locations;
                    $locations = explode(',', $locations);
                    foreach ($locations as $loc) {

                        $loc_arr = explode(':', $loc);
                        if ($loc_arr[1] == 'sub_community') {
                            $all_locations .= SubCommunity::find($loc[0])->name . ',';

                            $loc_type = SubCommunity::class;
                        } else {
                            $all_locations .= CommunityProperty::find($loc[0])->name . ',';

                            $loc_type = CommunityProperty::class;
                        }
                        $obj                   = new LeadCriteriaLocation();
                        $obj->parentable_type  = $loc_type;
                        $obj->parentable_id    = $loc_arr[0];
                        $obj->lead_criteria_id = $leadCriteria->id;
                        $obj->save();
                    }
                }
            }

            $environment = \App::environment();
            if ($environment == "production" && $notification == true) {
                $type = '';
                if (Type::find($typeOfLead)) {
                    $type = Type::find($typeOfLead)->name;
                }
                $bed = '';
                if (Bed::find($request->min_bed_id)) {
                    $bed = Bed::find($request->min_bed_id)->name;
                    if (Bed::find($request->max_bed_id)) {
                        $bed = Bed::find($request->min_bed_id)->name . ' - ' . Bed::find($request->max_bed_id)->name;
                    }
                }

                $message = "<b>AX Secondary CRM</b>\nA Lead has been recorded by FO Administrator. Kindly assign it to an agent.\n";
                $message .= "\n<b> Property Details: </b> ";
                $message .= "\n<b> ID: </b> " . $lead->id ?? '';
                $message .= "\n<b> Type: </b> " . $type ?? '';
                $message .= "\n<b> Purpose: </b> " . $request->for;
                $message .= "\n<b> Community: </b> " . $all_comunities;
                $message .= "\n<b> Location: </b> " . $all_locations;
                $message .= "\n<b> Beds: </b> " . $bed;
                $message .= "\n<b> Budget: </b> " . formatPrice($request->min_price) . ' - ' . formatPrice($request->max_price);
                $message .= "\n\n<b> Lead Details: </b> ";
                ($request->first_name != " " ? $message .= "\n<b> Name: </b> " . $request?->first_name . ' ' . $request?->last_name : null);
                ($request->mobile ? $message .= "\n<b> Mobile: </b> " . $request?->mobile : null);
                ($request->email ? $message .= "\n<b> Email: </b> " . $request?->email : null);

                $agents = User::whereHas("roles", function ($q) {
                    $q->whereIn("name", ["Line Manager", "Team Leader", "Sales Director"]);
                })->where("status", true)->get();
                foreach ($agents as $agent) {
                    if ($agent->telegram_chatid != null && $lead->agent_id == null) {

                        $url = route('api.thirdparty.leadAssignment', [Crypt::encryptString($lead->id), Crypt::encryptString($agent->id)]);
                        Log::channel('urlLogs')->info('URL lead: Manager:' . $agent->full_name, ['id' => $lead->id, 'data' => $url]);
                        $this->sendTelegramMessageHtml($message, [$agent->telegram_chatid], $url, 'Assign Lead', 'lead_bot');

                        telegramNotification('Lead', $lead->id, $agent->telegram_chatid, $agent->id, $message);
                    }
                }
            }

            if ($environment == "production" && $notification == false && $lead->agent_id != null) {

                $type = '';
                if (Type::find($typeOfLead)) {
                    $type = Type::find($typeOfLead)->name;
                }
                $bed = '';
                if (Bed::find($request->min_bed_id)) {
                    $bed = Bed::find($request->min_bed_id)->name;
                    if (Bed::find($request->max_bed_id)) {
                        $bed = Bed::find($request->min_bed_id)->name . ' - ' . Bed::find($request->max_bed_id)->name;
                    }
                }

                $agent = User::whereHas("roles", function ($q) {
                    $q->whereIn("name", ["Agent"]);
                })->where('id', $lead->agent_id)->where("status", true)->first();
                if ($agent) {
                    $message = "<b>AX Secondary CRM</b>\n\nA new <b>Lead</b> [" . $lead?->id . "] has been recorded.\n";
                    $message .= "\n<b> Property Details: </b> ";
                    $message .= "\n<b> ID: </b> " . $lead->id ?? '';
                    if ($property) {
                        $message .= "\n<b> Property Ref: </b> " . $request->ref_no ?? '';
                    }
                    $message .= "\n<b> Type: </b> " . $type ?? '';
                    $message .= "\n<b> Purpose: </b> " . $request->for;
                    $message .= "\n<b> Community: </b> " . $all_comunities;
                    $message .= "\n<b> Location: </b> " . $all_locations;
                    $message .= "\n<b> Beds: </b> " . $bed;
                    $message .= "\n<b> Budget: </b> " . formatPrice($request->min_price) . ' - ' . formatPrice($request->max_price);
                    $message .= "\n\n<b> Lead Details: </b> ";
                    ($request->first_name != " " ? $message .= "\n<b> Name: </b> " . $request?->first_name . ' ' . $request?->last_name : null);
                    ($request->mobile ? $message .= "\n<b> Mobile: </b> " . $request?->mobile : null);
                    ($request->email ? $message .= "\n<b> Email: </b> " . $request?->email : null);

                    if ($agent->telegram_chatid != null) {
                        $this->sendTelegramMessageHtml($message, [$agent->telegram_chatid], null, null, 'lead_bot');
                    }
                    telegramNotification('Lead', $lead->id, $agent->telegram_chatid, $agent->id, $message);
                }
            }

            return response()->json(['status' => '1', 'msg' => 'Lead Added Successfully']);
        } catch (\Throwable $th) {
            Log::channel('discord')->error("Error in submitLead function in ExternalApiController.php " . $th->getMessage());

            return response()->json(['status' => '0', 'msg' => $th->getMessage()]);
        }
    }

    public function updateSaveMyCallUser(Request $request)
    {
        $request->validate([
            'email'    => 'required',
            'activate' => 'required|boolean',
        ]);
        $fields = '{"api_key":"hne5jfjkgqn8orsrmx5yi4ai9u4ajf3k","action":"company.activate_employee","user_name":"otabek.navruzov@axcapital.ae", "employee_user_name": "' . $request->email . '", "activate": ' . $request->activate . '}';
        $curl   = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL            => 'https://axsecondary.moizvonki.ru/api/v1',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => $fields,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public function leadEdit(Request $request)
    {
        if (env('APP_ENV') == 'production') {
            $id      = Crypt::decryptString($request->lead);
            $user_id = Crypt::decryptString($request->user);
        } else {
            $id      = $request->lead;
            $user_id = $request->user;
        }

        $user = User::findOrFail($user_id);
        $lead = Lead::find($id);

        $properties = Property::where('agent_id', $user->id)->get();

        $sources = Source::where('id', '!=', 50)->where('status', true)->where('is_lead_source', true)->orderBy('order')->get();
        $mediums = Medium::where('status', true)->orderBy('order')->get();

        $open_sub_statuses = LeadStatus::whereNotIn('id', ['3', '12', '9'])->where('status', true)->orderBy('order')->get();
        // $closed_sub_statuses = LeadSubStatus::where('lead_status_id', 2)->where('status', true)->orderBy('order')->get();

        return view('form.leadsStatus')
            ->with('sources', $sources)
            ->with('mediums', $mediums)
            ->with('open_sub_statuses', $open_sub_statuses)
            ->with('lead', $lead)
            ->with('user', $user)
            ->with('properties', $properties);
    }

    public function leadUpdate(Request $request)
    {
        // $id = Crypt::decryptString($request->lead);
        // $user_id = Crypt::decryptString($request->user);

        if (env('APP_ENV') == 'production') {
            $id      = Crypt::decryptString($request->lead);
            $user_id = Crypt::decryptString($request->user);
        } else {
            $id      = $request->lead;
            $user_id = $request->user;
        }

        // $id = $request->lead;
        // $user_id = $request->user;

        $lead    = Lead::find($id);
        $user    = User::find($user_id);
        $orignal = $lead->getOriginal();

        $key                 = $request->key;
        $lead->$key          = $request->id;
        $lead->updated_by_id = $user->id;

        if ($key == 'property_id') {
            $criteria           = null;
            $community_property = null;
            $region             = null;
            $community          = null;
            $sub_community      = null;

            $property = Property::find($request->id);
            if ($property) {
                $criteria = getPropertyCriteria($property->ref_no);
                if ((! $community) && $criteria['community_id']) {
                    $community = Community::find($criteria['community_id']);
                }
                if ((! $sub_community) && $criteria['subCommunity_id']) {
                    $sub_community = SubCommunity::find($criteria['subCommunity_id']);
                }
                if ((! $community_property) && $criteria['communityProperty']) {
                    $community_property = CommunityProperty::find($criteria['communityProperty']);
                }
                $leadCriteria = LeadCriteria::where('lead_id', $lead->id)->first();
                if (! $leadCriteria) {
                    $leadCriteria = new LeadCriteria();
                }
                $leadCriteria->lead_id     = $lead->id;
                $leadCriteria->furnished   = $property->furnished;
                $leadCriteria->min_bed_id  = $criteria['bed_id'] ?? null;
                $leadCriteria->max_bed_id  = $criteria['bed_id'] ?? null;
                $leadCriteria->min_bath_id = $criteria['bath_id'] ?? null;
                $leadCriteria->max_bath_id = $criteria['bath_id'] ?? null;
                $leadCriteria->min_price   = $criteria['minPrice'] ?? null;
                $leadCriteria->max_price   = $criteria['maxPrice'] ?? null;
                $leadCriteria->save();
                LeadCriteriaLocation::where('lead_criteria_id', $leadCriteria->id)->delete();

                if ($community) {
                    // delete previous locations

                    LeadCriteriaLocation::create([
                        'lead_criteria_id' => $leadCriteria->id,
                        'parentable_type'  => Community::class,
                        'parentable_id'    => $community->id,
                    ]);
                }

                if ($sub_community) {
                    LeadCriteriaLocation::create([
                        'lead_criteria_id' => $leadCriteria->id,
                        'parentable_type'  => SubCommunity::class,
                        'parentable_id'    => $sub_community->id,
                    ]);
                }

                if ($community_property) {
                    LeadCriteriaLocation::create([
                        'lead_criteria_id' => $leadCriteria->id,
                        'parentable_type'  => CommunityProperty::class,
                        'parentable_id'    => $community_property->id,
                    ]);
                }
                $leadCriteria->view = $criteria['view'];
                $leadCriteria->save();
            } else {
                $leadCriteria = LeadCriteria::where('lead_id', $lead->id)->first();
                if ($leadCriteria) {
                    $leadCriteria->furnished   = null;
                    $leadCriteria->min_bed_id  = null;
                    $leadCriteria->max_bed_id  = null;
                    $leadCriteria->min_bath_id = null;
                    $leadCriteria->max_bath_id = null;
                    $leadCriteria->min_price   = null;
                    $leadCriteria->max_price   = null;
                    $leadCriteria->view        = null;
                    $leadCriteria->save();
                    LeadCriteriaLocation::where('lead_criteria_id', $leadCriteria->id)->delete();
                }
            }
        }

        $dirty   = ! empty($lead->getDirty()) ? $lead->getDirty() : $lead->getChanges();
        $orignal = collect($orignal)
            ->only(collect($dirty)->keys())
            ->toArray();

        if ($lead->save()) {
            $arr = [
                'attributes' => $dirty,
                'old'                => $orignal,
                'lead_id' => $lead->id
            ];
            $arr = [
                'attributes' => $dirty,
                'old'                => ! empty($orignal) ? $orignal : $lead->getOriginalMatchingChanges()
            ];
            $test = activity()
                ->performedOn($lead)
                ->causedBy($user->id)
                ->withProperties($arr)
                ->event('updated')
                ->log($user->first_name . ' has updated Lead ' . $lead->id);

            return response()->json(['status' => '1', 'msg' => 'Lead Updated Successfully']);
        }

        return response()->json(['status' => '1', 'msg' => 'Lead ' . ucfirst(str_replace('id', '', str_replace('_', ' ', $request->key))) . ' Updated Successfully']);
    }

    public function propertyFinderEmailLeads($bearerToken)
    {
        $time_start    = microtime(true);
        $page          = 1;
        $importedCount = 0;
        $per_page      = 20;
        $currentDate   = Carbon::now();
        $formattedDate = $currentDate->format('Y-m-d');

        //$formattedDate = "2024-02-04";
        //$bearerToken = "rCyjT.GLTeX4jHhlEsBYKSnnlv25iOMgOy7YtfJC";

        $header   = [];
        $header[] = 'Content-Type: application/json';
        $header[] = 'Content-Length: 4000';
        $header[] = 'Connection: keep-alive';
        $response = Http::withHeaders([
            'Authorization'       => 'Bearer ' . $bearerToken,
            'x-requested-with'    => 'XMLHttpRequest',
            'x-mycrm-expand-data' => 'true',
            'User-Agent: PostmanRuntime/7.28.0',
        ])->get('https://api-v2.mycrm.com/leads?page=1&per_page=1&filters[created][from]=' . $formattedDate);
        $res = $response->json();
        //        dd($res);
        $leads             = $res['leads'] ?? [];
        $total_leads_count = $res['count'] ?? 0;
        $total_pages       = round(ceil($total_leads_count / $per_page), 0);
        for ($page; $page <= $total_pages; $page++) {
            $response = Http::withHeaders(
                [
                    'Authorization'       => 'Bearer ' . $bearerToken,
                    'x-requested-with'    => 'XMLHttpRequest',
                    'User-Agent: PostmanRuntime/7.28.0',
                    'x-mycrm-expand-data' => 'true',
                ]
            )->get('https://api-v2.mycrm.com/leads?page=' . $page . '&per_page=' . $per_page . '&filters[created][from]=' . $formattedDate);
            $res   = $response->json();
            $leads = $res['leads'] ?? [];
            if ($leads) {
                foreach ($leads as $value) {
                    echo "\n#";
                    echo ($value['id']);
                    $lead = Lead::where('mobile', "LIKE", "%" . substr($value['mobile'], -7))->where('my_crm_id', $value['id'])->first();

                    if ($lead) {
                        continue;
                    }

                    $newLead   = false;
                    $source_id = 31;

                    if (! empty($value)) {
                        // Checking Source
                        switch ($value['source']) {
                            case 'propertyfinder':
                                $source_id = 31; // Property Finder Id
                                break;
                            case 'online':
                                $source_id = 45; // Online Id
                                break;
                            case 'other':
                                $source_id = 44; // Other Id
                                break;
                            default:
                                $source_id = 31; // Property Finder Id
                                break;
                        }
                        if ($value['source_channel'] == "ourWebsite") {
                            $medium = Medium::where('name', "%" . 'Website Form' . "%")->first();
                        } else {
                            $medium = Medium::where('name', "LIKE", "%" . 'Email' . "%")->first();
                        }
                        // Checking
                        switch ($value['status']) {
                            case 'notYetContacted':
                                $sub_status = '4'; // Not Yet Contacted
                                break;
                            case 'prospect':
                                $sub_status = '26'; // Prospect
                                break;
                            case 'calledNoReply':
                                $sub_status = '5'; // Called No Reply
                                break;
                        }
                        $for                = null;
                        $from_bed_id        = null;
                        $to_bed_id          = null;
                        $from_bath_id       = null;
                        $to_bath_id         = null;
                        $community          = null;
                        $sub_community      = null;
                        $community_property = null;
                        $from_price         = null;
                        $to_price           = null;
                        $pattern            = '/ref:\s*(\w+-\d+)/';

                        $message   = $value['notes'][0]['body'] ?? "";
                        $property  = [];
                        $type      = [];
                        $typeValue = "";
                        // Perform the regular expression match
                        if (preg_match($pattern, $message, $matches)) {
                            $referenceNumber = $matches[1];
                            $property        = Property::where('ref_no', $referenceNumber)->first();
                            if ($property) {
                                switch ($property->for) {
                                    case 'Residential for sale':
                                        $typeValue = 'Buyer'; // Not Yet Contacted
                                        break;
                                    case 'Residential for rent':
                                        $typeValue = 'Tenant'; // Prospect
                                        break;
                                    case 'Commercial for sale':
                                        $typeValue = 'Buyer'; // Called No Reply
                                        break;
                                    case 'Commercial for rent':
                                        $typeValue = 'Tenant'; // Called No Reply
                                        break;
                                }
                                $type = Type::where('name', $typeValue)->first();
                            }
                        }

                        if ($value['preferences']) {
                            if (isset($value['preferences'][0]['offering_type']) && $value['preferences'][0]['offering_type'] != "") {
                                $for = $value['preferences'][0]['types'][0]['category'] . " for " . $value['preferences'][0]['offering_type'];
                            }
                            if ($value['preferences'][0]['from_bedroom'] == 0) {
                                $from_bed_id = Bed::where('name', "LIKE", "Studio")->firstOrFail();
                            } elseif ($value['preferences'][0]['from_bedroom'] != null) {
                                $from_bed_id = Bed::where('name', "LIKE", $value['preferences'][0]['from_bedroom'] . " bed%")->firstOrFail();
                            }

                            if ($value['preferences'][0]['to_bedroom'] == 0) {
                                $to_bed_id = Bed::where('name', "LIKE", "Studio")->firstOrFail();
                            } elseif ($value['preferences'][0]['to_bedroom'] != null) {
                                $to_bed_id = Bed::where('name', "LIKE", $value['preferences'][0]['to_bedroom'] . " bed%")->firstOrFail();
                            }

                            if ($value['preferences'][0]['from_bathroom'] != 0 && $value['preferences'][0]['from_bathroom'] != null) {
                                $from_bath_id = Bath::where('name', "LIKE", $value['preferences'][0]['from_bathroom'] . " bath%")->firstOrFail();
                            }
                            if ($value['preferences'][0]['to_bathroom'] != 0 && $value['preferences'][0]['from_bathroom'] != null) {
                                $to_bath_id = Bath::where('name', "LIKE", $value['preferences'][0]['to_bathroom'] . " bath%")->firstOrFail();
                            }

                            if (isset($value['preferences'][0]['locations'][0]['community'])) {
                                $community = Community::where('name', "LIKE", $value['preferences'][0]['locations'][0]['community'])->first();
                            }
                            if (isset($value['preferences'][0]['locations'][0]['sub_community'])) {
                                $sub_community = SubCommunity::where('name', "LIKE", $value['preferences'][0]['locations'][0]['sub_community'])->first();
                            } else {
                                if ($property->sub_community_id ?? null) {
                                    $sub_community = SubCommunity::find($property?->sub_community_id);
                                }
                            }
                            if (isset($value['preferences'][0]['locations'][0]['tower'])) {
                                $community_property = CommunityProperty::where('name', "LIKE", $value['preferences'][0]['locations'][0]['tower'])->first();
                            } else {
                                if ($property->community_property_id ?? null) {
                                    $community_property = CommunityProperty::find($property?->community_property_id);
                                }
                            }
                            $from_price = $value['preferences'][0]['from_price'];
                            $to_price   = $value['preferences'][0]['to_price'];
                        }
                        $user = null;
                        if (isset($value['user']['public']['email'])) {
                            if ($value['user']['public']['email'] == "abudhabi@axcapital.ae") {
                                $user = User::where('email', "j.kinana@axcapital.ae")->first();
                            } else {
                                $user = User::where('email', $value['user']['public']['email'])->first();
                                if (! $user) {
                                    $users = Http::withHeaders([
                                        'Authorization' => 'Bearer ' . $bearerToken,
                                        'User-Agent: PostmanRuntime/7.28.0',
                                    ])->get('https://api-v2.mycrm.com/users/' . $value['user']['id']);
                                    $userRes = $users->json();
                                    //dd($userRes);
                                    $userData = $userRes['user'] ?? [];

                                    if ($userData) {
                                        if (isset($userData['private']['email'])) {
                                            if ($userData['private']['email']) {
                                                $user = User::where('email', $userData['private']['email'])->first();
                                                if ($user) {
                                                    $user->old_user_id = $value['user']['id'];
                                                    $user->save();
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        if (! $lead) {
                            $lead = new Lead();

                            $lead->my_crm_id      = $value['id'];
                            $lead->agent_id       = $user?->id;
                            $lead->first_name     = $value['first_name'];
                            $lead->last_name      = $value['last_name'];
                            $lead->email          = $value['email'];
                            $lead->type_id        = $type->id ?? null;
                            $lead->phone          = $value['phone'];
                            $lead->for            = $for ? [ucfirst($for)] : null;
                            $lead->mobile         = $value['mobile'];
                            $lead->whatsapp       = $value['mobile'];
                            $lead->address        = $value['address'];
                            $lead->message        = $message ?? "";
                            $lead->lead_status_id = 1; // New
                            $lead->property_id    = $property->id ?? null;
                            // $lead->lead_sub_status_id = $sub_status ?? null;
                            $lead->lead_medium_id = $medium?->id ?? null;
                            $lead->source_id      = $source_id;
                            $lead->portal_created_at      = ($value['created_at'] ?? null) ? Carbon::parse($value['created_at']) : null;

                            $lead->save();
                            $lead->refresh();
                            $importedCount++;
                            event(new \App\Events\LeadCreated($lead));
                            activity()
                                ->performedOn($lead)
                                ->event('Created')
                                ->log('Created New Lead From Parser Api');
                        }

                        $leadCriteria              = LeadCriteria::where('lead_id', $lead->id)->first() ?? new LeadCriteria();
                        $leadCriteria->lead_id     = $lead->id;
                        $leadCriteria->min_bed_id  = $from_bed_id?->id;
                        $leadCriteria->max_bed_id  = $to_bed_id?->id;
                        $leadCriteria->min_bath_id = $from_bath_id?->id;
                        $leadCriteria->max_bath_id = $to_bath_id?->id;
                        $leadCriteria->min_price   = $from_price ?? null;
                        $leadCriteria->max_price   = $to_price ?? null;

                        $leadCriteria->save();

                        $leadNote          = new LeadNote();
                        $leadNote->lead_id = $lead->id;
                        $leadNote->note    = 'Imported from Expert Property Finder Api
                        <br> Message: ' . $message;
                        $leadNote->save();

                        LeadCriteriaLocation::where('lead_criteria_id', $leadCriteria->id)->delete(); // deleting old locations to prevent duplication
                        if ($community) {
                            $communityLocation = LeadCriteriaLocation::where('parentable_type', Community::class)->where('parentable_id', $community->id)->where('lead_criteria_id', $leadCriteria->id)->exists();
                            if (! $communityLocation) {
                                $obj                   = new LeadCriteriaLocation();
                                $obj->parentable_type  = Community::class;
                                $obj->parentable_id    = $community?->id;
                                $obj->lead_criteria_id = $leadCriteria->id;
                                $obj->save();
                            }
                        }
                        if ($sub_community) {
                            $communityLocation = LeadCriteriaLocation::where('parentable_type', SubCommunity::class)->where('parentable_id', $sub_community?->id)->where('lead_criteria_id', $leadCriteria->id)->exists();
                            if (! $communityLocation) {
                                $obj                   = new LeadCriteriaLocation();
                                $obj->parentable_type  = SubCommunity::class;
                                $obj->parentable_id    = $sub_community?->id;
                                $obj->lead_criteria_id = $leadCriteria->id;
                                $obj->save();
                            }
                        }
                        if ($community_property) {
                            $communityLocation = LeadCriteriaLocation::where('parentable_type', CommunityProperty::class)->where('parentable_id', $community_property?->id)->where('lead_criteria_id', $leadCriteria->id)->exists();
                            if (! $communityLocation) {
                                $obj                   = new LeadCriteriaLocation();
                                $obj->parentable_type  = CommunityProperty::class;
                                $obj->parentable_id    = $community_property?->id;
                                $obj->lead_criteria_id = $leadCriteria->id;
                                $obj->save();
                            }
                        }
                    }
                }
            }
        }
        $time_end = microtime(true);
        $time     = $time_end - $time_start;
        echo ("\n $importedCount leads are imported successfully");
        return $importedCount;
    }
    public function propertyFinderWhatsappLeadsNew($token)
    {
        try {
            $access_token = $token;

            $today = date('Y-m-d');
            $dateFrom = $today;
            //   $time_start = microtime(true);
            $page = 1;
            $importedCount = 0;
            //   $updatedCount = 0;
            $per_page = 10;




            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api-v2.mycrm.com/whatsapp-leads/?per_page=1&filters[date][from]=' . $dateFrom . '&filters[date][to]=' . $today,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . $access_token,
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);

            $data = json_decode($response, true);


            if (isset($data['error']) && $data['error'] == 'AuthenticationError') {
                echo 'PF Authentication when fetching whatsapp leads error code N3434';
                info('PF Authentication when fetching whatsapp leads');
                return true;
            }

            if (!is_null($data)) {
                echo "Coming inside this condition\n";
                $total_leads_count = $data['count'];
                echo "Total leads count: " . $total_leads_count . "\n";
                $total_pages = round(ceil($total_leads_count / $per_page), 0);
                for ($page; $page <= $total_pages; $page++) { //limit is hardcoded becuase date filter is not working as expected
                    $curl = curl_init();
                    echo "Fetching page: " . $page . "\n";
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => 'https://api-v2.mycrm.com/whatsapp-leads/?per_page=' . $per_page . '&page=' . $page . '&filters[date][from]=' . $dateFrom . '&filters[date][to]=' . $today,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'GET',
                        CURLOPT_HTTPHEADER => array(
                            'Authorization: Bearer ' . $access_token,
                            'X-MyCRM-Expand-Data: true',
                        ),
                    ));

                    $response = curl_exec($curl);

                    curl_close($curl);

                    $data = json_decode($response, true);
                    if (isset($data['error']) && $data['error'] == 'AuthenticationError') {
                        echo 'PF Authentication when fetching whatsapp leads error code N34232';
                        info('PF Authentication when fetching whatsapp leads');
                    }
                    if (!is_null($data)) {
                        $medium = Medium::where('name', "LIKE", "%" . 'WhatsApp' . "%")->first();

                        $leads = $data['whatsapp'];
                        foreach ($leads as $lead) {
                            $mobile = str_replace(' ', '', $lead['phone']);
                            if (!Lead::where('my_crm_id', $lead['id'])->exists()) {
                                $given_time = $lead['created_at'];
                                $date = new \DateTime($given_time);
                                // $date->setTimezone(new \DateTimeZone('Asia/Dubai'));

                                $formatted_time = $date->format('Y-m-d H:i:s');
                                if ($lead['property_reference'] == null) {
                                    $user = null;
                                    if ($lead['user'] != null) {
                                        $private = $lead['user']['private'] ?? null;
                                        if ($private != null) {
                                            $email = $private['email'] ?? null;
                                            $user = User::where('email', $email)->first() ?? null;
                                        }
                                    }
                                    $new_lead = new Lead();
                                    $new_lead->mobile = $mobile;
                                    $new_lead->whatsapp = $mobile;
                                    $new_lead->my_crm_id = $lead['id'];
                                    $new_lead->agent_id = $user ? $user->id : null;
                                    $new_lead->lead_medium_id = $medium?->id ?? null;
                                    $new_lead->source_id = 31;
                                    $new_lead->status = 1;
                                    $new_lead->lead_status_id = 1;
                                    $new_lead->created_at = Carbon::now();
                                    $new_lead->updated_at = Carbon::now();
                                    $new_lead->portal_created_at = $formatted_time;
                                    if ($new_lead->save()) {
                                        event(new \App\Events\LeadCreated($new_lead));
                                        activity()
                                            ->performedOn($new_lead)
                                            ->event('Created')
                                            ->log('Created New Lead From pf whatsapp Api');
                                        $leadNote = new LeadNote();
                                        $leadNote->lead_id = $new_lead->id;
                                        $responseText = json_encode($lead);

                                        $leadNote->note = 'Imported from Expert Property Finder whatsapp Api
                                <br><code><pre>' . $responseText . "</pre></code>";
                                        $leadNote->save();
                                    }
                                    $importedCount++;
                                }
                            }
                        }
                    }


                    //    $time_end = microtime(true);
                    // $execution_time = ($time_end - $time_start);


                }
            }
            echo 'Imported pf whatsapp leads: ' . $importedCount;
        } catch (\Exception $th) {
            Log::channel('discord')->error("Error in propertyFinderWhatsappLeadsNew function in ExternalApiController.php " . json_encode($th->getMessage()));
            echo 'Error in propertyFinderWhatsappLeadsNew function in ExternalApiController.php ' . json_encode($th->getMessage());
        }
    }
    public function propertyFinderWhatsappLeadsDateWise($token, $for_date = null)
    { //this function is used to fetch whatsapp leads from property finder for old dates
        try {
            $access_token = $token;


            $today = $for_date ? $for_date : date('Y-m-d');
            $dateFrom = $today;
            //   $time_start = microtime(true);
            $page = 1;
            $importedCount = 0;
            //   $updatedCount = 0;
            $per_page = 10;




            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api-v2.mycrm.com/whatsapp-leads/?per_page=1&filters[date][from]=' . $dateFrom . '&filters[date][to]=' . $today,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . $access_token,
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);

            $data = json_decode($response, true);


            if (isset($data['error']) && $data['error'] == 'AuthenticationError') {
                echo 'PF Authentication when fetching whatsapp leads error code N3434';
                info('PF Authentication when fetching whatsapp leads');
                return true;
            }

            if (!is_null($data)) {

                $total_leads_count = $data['count'];
                //   echo "Total leads count: " . $total_leads_count . "\n";
                $total_pages = round(ceil($total_leads_count / $per_page), 0);
                for ($page; $page <= $total_pages; $page++) {
                    $curl = curl_init();
                    //     echo "Fetching page: " . $page . "\n";
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => 'https://api-v2.mycrm.com/whatsapp-leads/?per_page=' . $per_page . '&page=' . $page . '&filters[date][from]=' . $dateFrom . '&filters[date][to]=' . $today,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'GET',
                        CURLOPT_HTTPHEADER => array(
                            'Authorization: Bearer ' . $access_token,
                            'X-MyCRM-Expand-Data: true',
                        ),
                    ));

                    $response = curl_exec($curl);

                    curl_close($curl);

                    $data = json_decode($response, true);
                    if (isset($data['error']) && $data['error'] == 'AuthenticationError') {
                        echo 'PF Authentication when fetching whatsapp leads error code N34232';
                        info('PF Authentication when fetching whatsapp leads');
                    }
                    if (!is_null($data)) {
                        $medium = Medium::where('name', "LIKE", "%" . 'WhatsApp' . "%")->first();

                        $leads = $data['whatsapp'];
                        echo "Total leads in this page: " . count($leads) . "\n";
                        //   echo json_encode($leads, JSON_PRETTY_PRINT) . "\n";
                        foreach ($leads as $lead) {
                            $mobile = str_replace(' ', '', $lead['phone']);
                            if (!Lead::where('my_crm_id', $lead['id'])->exists()) {
                                $given_time = $lead['created_at'];
                                $date = new \DateTime($given_time);
                                // $date->setTimezone(new \DateTimeZone('Asia/Dubai'));

                                $formatted_time = $date->format('Y-m-d H:i:s');
                                if ($lead['property_reference'] == null) {
                                    $user = null;
                                    if ($lead['user'] != null) {
                                        $private = $lead['user']['private'] ?? null;
                                        if ($private != null) {
                                            $email = $private['email'] ?? null;
                                            $user = User::where('email', $email)->first() ?? null;
                                        }
                                    }
                                    $new_lead = new Lead();
                                    $new_lead->mobile = $mobile;
                                    $new_lead->whatsapp = $mobile;
                                    $new_lead->my_crm_id = $lead['id'];
                                    $new_lead->agent_id = $user ? $user->id : null;
                                    $new_lead->lead_medium_id = $medium?->id ?? null;
                                    $new_lead->source_id = 31;
                                    $new_lead->status = 1;
                                    $new_lead->lead_status_id = 1;
                                    $new_lead->created_at = Carbon::parse($lead['created_at']);
                                    $new_lead->updated_at = Carbon::parse($lead['updated_at']);
                                    $new_lead->portal_created_at = $formatted_time;
                                    if ($new_lead->save()) {
                                        event(new \App\Events\LeadCreated($new_lead));
                                        activity()
                                            ->performedOn($new_lead)
                                            ->event('Created')
                                            ->log('Created New Lead From pf whatsapp Api');
                                        $leadNote = new LeadNote();
                                        $leadNote->lead_id = $new_lead->id;
                                        $responseText = json_encode($lead);

                                        $leadNote->note = 'Imported from Expert Property Finder whatsapp Api
                                <br><code><pre>' . $responseText . "</pre></code>";
                                        $leadNote->save();
                                    }
                                    $importedCount++;
                                } else {
                                    $property = Property::where('ref_no', $lead['property_reference'])->first();
                                    if ($property) {
                                        switch ($property->for) {
                                            case 'Residential for sale':
                                                $typeValue = 'Buyer'; // Not Yet Contacted
                                                break;
                                            case 'Residential for rent':
                                                $typeValue = 'Tenant'; // Prospect
                                                break;
                                            case 'Commercial for sale':
                                                $typeValue = 'Buyer'; // Called No Reply
                                                break;
                                            case 'Commercial for rent':
                                                $typeValue = 'Tenant'; // Called No Reply
                                                break;
                                        }
                                        $type = Type::where('name', $typeValue)->first();
                                    }
                                    $new_lead = new Lead();
                                    $new_lead->mobile = $mobile;
                                    $new_lead->whatsapp = $mobile;
                                    $new_lead->my_crm_id = $lead['id'];
                                    $new_lead->agent_id = $property?->agent_id ?? null;
                                    $new_lead->property_id = $property?->id ?? null;
                                    $new_lead->lead_medium_id = $medium?->id ?? null;
                                    $new_lead->source_id = 31;
                                    $new_lead->type_id        = $type->id ?? null;
                                    $new_lead->status = 1;
                                    $new_lead->lead_status_id = 1;
                                    $new_lead->created_at = Carbon::parse($lead['created_at']);
                                    $new_lead->updated_at = Carbon::parse($lead['updated_at']);
                                    $new_lead->portal_created_at = $formatted_time;
                                    if ($new_lead->save()) {
                                        event(new \App\Events\LeadCreated($new_lead));
                                        $leadCriteria              = LeadCriteria::where('lead_id', $new_lead->id)->first() ?? new LeadCriteria();
                                        $leadCriteria->lead_id     = $new_lead->id;
                                        $leadCriteria->min_bed_id  = $property->bed_id ?? null;
                                        $leadCriteria->max_bed_id  = $property->bed_id ?? null;
                                        $leadCriteria->min_bath_id = $property->bath_id ?? null;
                                        $leadCriteria->max_bath_id = $property->bath_id ?? null;
                                        $leadCriteria->min_price   = $property->price->price ?? null;
                                        $leadCriteria->max_price   = $property->price->price ?? null;

                                        $leadCriteria->save();
                                        if ($property?->community_id != null) {
                                            LeadCriteriaLocation::updateOrCreate(
                                                [
                                                    'lead_criteria_id' => $leadCriteria->id,
                                                    'parentable_type' => Community::class,
                                                    'parentable_id' => $property?->community_id ?? null,
                                                ]
                                            );
                                        }
                                        if ($property?->sub_community_id != null) {
                                            LeadCriteriaLocation::updateOrCreate(
                                                [
                                                    'lead_criteria_id' => $leadCriteria->id,
                                                    'parentable_type' => SubCommunity::class,
                                                    'parentable_id' => $property?->sub_community_id ?? null,
                                                ]
                                            );
                                        }
                                        if ($property?->sub_community_id != null) {
                                            LeadCriteriaLocation::updateOrCreate(
                                                [
                                                    'lead_criteria_id' => $leadCriteria->id,
                                                    'parentable_type' => CommunityProperty::class,
                                                    'parentable_id' => $property?->sub_community_id ?? null,
                                                ]
                                            );
                                        }

                                        activity()
                                            ->performedOn($new_lead)
                                            ->event('Created')
                                            ->log('Created New Lead From pf whatsapp Api');
                                        $leadNote = new LeadNote();
                                        $leadNote->lead_id = $new_lead->id;
                                        $responseText = json_encode($lead);

                                        $leadNote->note = 'Imported from Expert Property Finder whatsapp Api
                                <br><code><pre>' . $responseText . "</pre></code>";
                                        $leadNote->save();
                                    }
                                    $importedCount++;
                                }
                            }
                        }
                    }


                    //    $time_end = microtime(true);
                    // $execution_time = ($time_end - $time_start);


                }
            }
            echo 'Imported pf whatsapp leads: ' . $importedCount;
        } catch (\Exception $th) {
            Log::channel('discord')->error("Error in propertyFinderWhatsappLeadsNew function in ExternalApiController.php " . json_encode($th->getMessage()));
            echo 'Error in propertyFinderWhatsappLeadsNew function in ExternalApiController.php ' . json_encode($th->getMessage());
        }
    }


    public function propertyFinderCallLeads($bearerToken, $type)
    {

        // $bearerToken = 'eyJhbGciOiJSUzI1NiIsInR5cCIgOiAiSldUIiwia2lkIiA6ICJIYzY0Z2lpMHpxYzUwRXNrMjFPZ3ExTkdQQUVuRmZOWEN1akdsc1BkdnYwIn0.eyJleHAiOjE3MzM3NTQ2NzIsImlhdCI6MTczMzc0NzQ3MiwiYXV0aF90aW1lIjoxNzMzNzQ3NDcwLCJqdGkiOiIwNThmNmE1Ni05MDQwLTQzM2ItYTc0Yy1hNmU4MTE2ZTY4N2YiLCJpc3MiOiJodHRwczovL2F1dGgucHJvcGVydHlmaW5kZXIuY29tL2Jyb2tlci9yZWFsbXMvcHJvcGVydHlmaW5kZXIiLCJhdWQiOiJwZmV4cGVydCIsInN1YiI6IjE2MzcwNCIsInR5cCI6IkJlYXJlciIsImF6cCI6InBmZXhwZXJ0Iiwibm9uY2UiOiJXMGY2bDc1WEZaNEpianMzWVNoek5FdDNockxEWDVFUldXQVN3SGJVIiwic2Vzc2lvbl9zdGF0ZSI6ImQzNzljYzM2LTM3NDMtNGU3NS1iN2ZmLTU1NGQ3ZDJlZmNlNiIsInJlYWxtX2FjY2VzcyI6eyJyb2xlcyI6WyJ2Mi5saXN0aW5nLmNyZWF0ZSIsInYyLm5ld19wcm9qZWN0cy5tYW5hZ2UiLCJ2Mi50cmFuc2FjdGlvbnMubWFuYWdlIiwicHJvZmlsZS5yZWFkIiwidjIubGlzdGluZy5wcmV2aWV3IiwicHJvcGVydHkucmVhZCIsInByb3BlcnR5LnB1Ymxpc2giLCJ2Mi5jcmVkaXRfdXNhZ2VfaGlzdG9yeS52aWV3IiwicHJvcGVydHkudXBkYXRlIiwicHJvcGVydHkuZGVsZXRlIiwidjIubGlzdGluZy52aWV3IiwibGFuZGxvcmQucmVhZC5vdGhlci5hdHRyaWJ1dGVzIiwidjIub3duZXJzLnZpZXciLCJ2Mi5jb21wYW55X3dhdGVybWFyay5tYW5hZ2UiLCJsZWFkLmRlbGV0ZS5vdGhlciIsInYyLnVzZXJfcHJvZmlsZS51cGRhdGUiLCJsYW5kbG9yZC5yZWFkIiwicHJvZmlsZS51cGRhdGUiLCJ2Mi5saXN0aW5nLnVucHVibGlzaCIsInYyLnVzZXJfbm90aWZpY2F0aW9uX3ByZWZlcmVuY2UudXBkYXRlIiwidjIucGZfYWNhZGVteS52aWV3IiwidW1hX2F1dGhvcml6YXRpb24iLCJ2Mi5vd25lcnMubWFuYWdlIiwibGFuZGxvcmQudXBkYXRlLm90aGVyIiwidjIubGlzdGluZy5hdXRvX3JlbmV3IiwidjIudXNlcnNfbGlzdC52aWV3IiwidjIudmF0X2RvY3VtZW50LnZpZXciLCJ2Mi5jbGllbnRfcHJvZmlsZS52aWV3IiwidjIuY3JlZGl0X3VzYWdlX2hpc3RvcnkuZXhwb3J0IiwicHJvcGVydHkudXBkYXRlLm90aGVyIiwidjIudXNlcl92ZXJpZmljYXRpb24ubWFuYWdlIiwibGVhZC5yZWFkLm90aGVyLmF0dHJpYnV0ZXMiLCJ2Mi5saXN0aW5nLmNsYWltX3RyYW5zYWN0aW9uIiwidjIuYWdlbnRfaW5zaWdodHMudmlldyIsInYyLmxpc3RpbmcudXBncmFkZV90b19mZWF0dXJlZCIsInYyLmNvbnRyYWN0c19pbnZvaWNlcy52aWV3IiwibGFuZGxvcmQuZGVsZXRlIiwiY2xpZW50LnJlYWQiLCJsZWFkLnVwZGF0ZSIsInYyLmxpc3RpbmcuYXJjaGl2ZSIsInYyLnBlcm1pc3Npb25zLnZpZXciLCJ2Mi53aGF0c2FwcF9sZWFkcy52aWV3IiwicHJvcGVydHkuZGVsZXRlLm90aGVyIiwicHJvcGVydHkuYXBwcm92ZS5vdGhlciIsInYyLnVzZXJfcHJvZmlsZS52aWV3IiwidjIubGlzdGluZy51cGdyYWRlX3RvX3ByZW1pdW0iLCJsZWFkLnJlYWQiLCJ2Mi5jdXN0b21fcm9sZXMubWFuYWdlIiwidjIubGVhZHNfaW5zaWdodHMudmlldyIsInYyLnN0YXR1cy52aWV3IiwidjIucmVndWxhcl9sZWFkcy52aWV3IiwidjIudXNlci5jcmVhdGUiLCJ2Mi5saXN0aW5nLmFzc2lnbiIsInYyLmxpc3RpbmdzX2FjdGlvbl90cmFja2VyLm1hbmFnZSIsInYyLmxpc3RpbmcudXBkYXRlX2RldGFpbHMiLCJ2Mi5wb3J0YWxzLm1hbmFnZSIsInVzZXIucmVhZCIsInByb3BlcnR5LnB1Ymxpc2gub3RoZXIiLCJvZmZsaW5lX2FjY2VzcyIsInYyLmNyZWRpdHNfdG9fdGVhbXMubWFuYWdlIiwidjIuYWN0aXZpdHlfbG9nLnZpZXciLCJ2Mi51c2VyX2RvY3VtZW50cy5tYW5hZ2UiLCJ2Mi50cmFuc2FjdGlvbnMudmlldyIsInYyLmxpc3RpbmcuZGVsZXRlIiwidjIuc2luZ2xlX2FnZW50X2luc2lnaHRzLnZpZXciLCJ2Mi5yZWd1bGFyX2xlYWRzLm1hbmFnZSIsImxlYWQuZGVsZXRlIiwidXNlci51cGRhdGUiLCJ2Mi5jcmVkaXRzX3RvX2FnZW50cy5tYW5hZ2UiLCJ2Mi5jcmVkaXRfcmV0dXJuLmNyZWF0ZSIsInYyLmNyZWRpdF90b3B1cC5jcmVhdGUiLCJ2Mi5jcmVkaXRfY2FsY3VsYXRvci52aWV3IiwidjIubGlzdGluZy5wdWJsaXNoIiwidjIuY3RzX2xhbmRpbmdfcGFnZS52aWV3IiwidjIuaG9saWRheV9tb2RlLm1hbmFnZSIsInYyLmxpc3RpbmdzX2FjdGlvbl90cmFja2VyLnZpZXciLCJsZWFkLnVwZGF0ZS5vdGhlciIsInYyLmxpc3Rpbmcuc3BvdGxpZ2h0IiwibGVhZC5yZWFkLm90aGVyIiwidjIudXNlci5kZWxldGUiLCJ2Mi5saXN0aW5nX3BlcmZvcm1hbmNlX2Rhc2hib2FyZC52aWV3IiwiZGVmYXVsdC1yb2xlcy1wZmV4cGVydCIsImxhbmRsb3JkLnJlYWQub3RoZXIiLCJwcm9wZXJ0eS5yZWFkLm90aGVyIiwidjIubGlzdGluZy52aWV3X29wdGlvbnMiLCJwcm9wZXJ0eS5yZWFkLm90aGVyLmF0dHJpYnV0ZXMiLCJwcm9wZXJ0eS5hcHByb3ZlIiwidjIubGlzdGluZy5hcHByb3ZlX3JlamVjdCIsImxhbmRsb3JkLmRlbGV0ZS5vdGhlciIsImxhbmRsb3JkLnVwZGF0ZSIsInYyLmNhbGxfbGVhZHMudmlldyIsImNsaWVudC51cGRhdGUiLCJ2Mi5saXN0aW5nLmRvd25ncmFkZSJdfSwic2NvcGUiOiJvcGVuaWQgZGVmYXVsdCIsInNpZCI6ImQzNzljYzM2LTM3NDMtNGU3NS1iN2ZmLTU1NGQ3ZDJlZmNlNiIsImNvdW50cnkiOiJBRSIsInJvbGUiOiJhZG1pbiIsInBmX2Jyb2tlcl9pZCI6IjM0MjciLCJwZl9pbXBvcnRlZF9pZCI6IkFFLTEwNjE4NyIsImNsaWVudF9pZCI6IjIyMjYiLCJiYXNlX3JvbGVfa2V5IjoiYWRtaW4iLCJzZl9hY2NvdW50X251bWJlciI6IjExMTcwOCIsInVzZXJfaWQiOiIxNjM3MDQiLCJwZl9hZ2VudF9pZCI6IkFFLTI1MjMxNyIsImlidCI6dHJ1ZSwicGZfY2xpZW50X2lkIjoiMzMwMSIsImFwcGxpY2F0aW9uSWQiOiIxYWFjNDE1NC1jYzEyLTQzZTEtYTgzZC1jOTk4ZTJhYjBlMDUiLCJhY2Nlc3NfY29udHJvbF9yb2xlX2lkIjoiNCIsImVtYWlsIjoicy5taXJ6YUBheGNhcGl0YWwuYWUifQ.XvitK68ZMfrewClZ0fn2f4rPQyiBxOvWxyid5fvn9raWaFmJf-lVsSl_YpkBXCjDad26oD-pg2dI6ERPQALJ21IQWfan2X_6y31ozdr6QxLAVYHXWjP21pR7uTgC4ZkJs0ryrMiF7sXUVN7hl7mWOhVtJq7gDlbV1tBZgc0QmEnkS9EPxg6cWnDOdyvRpj8S12g7WPfj6IAvxw6m7dYV6OTbDfLis99q0WjuTWqCdIoazyIZXQCuHODkF66CmrvGuJzkJK-GJ1GDHj2SKDV3DBOjoQFEouvlCkia1IKyIwXD0u8j8AEneWSPJzhBEKyDrVsM9vDHTQiC5GzKrEG9gQ';
        $time_start    = microtime(true);
        $page          = 1;
        $importedCount = 0;
        $updatedCount  = 0;
        $per_page      = 20;

        $currentDate   = Carbon::now();
        $formattedDate = $currentDate->format('Y-m-d');
        $header        = [];
        // $formattedDate = "2024-12-03";
        $header[] = 'Content-Type: application/json';
        $header[] = 'Content-Length: 4000';
        $header[] = 'Connection: keep-alive';
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $bearerToken,
            'User-Agent: PostmanRuntime/7.28.0',
        ])->timeout(120)->get('https://api-v2.mycrm.com/calltrackings?sort=id&sort_order=ASC&page=1&per_page=1&filters[date][from]=' . $formattedDate);
        $res = $response->json();

        if (isset($res['error']) && $res['error'] == 'AuthenticationError') {
            info('PF Authentication for ' . $type . ' Error for Call Tracking');
            return true;
        }

        $leads             = $res['call_trackings'] ?? [];
        $total_leads_count = $res['count'] ?? 0;
        $total_pages       = round(ceil($total_leads_count / $per_page), 0);
        // dd($total_pages);
        for ($page; $page <= $total_pages; $page++) {
            $response = Http::withHeaders([
                'Authorization'       => 'Bearer ' . $bearerToken,
                'x-requested-with'    => 'XMLHttpRequest',
                'User-Agent: PostmanRuntime/7.28.0',
                'x-mycrm-expand-data' => 'true',

            ])->get('https://api-v2.mycrm.com/calltrackings?sort=id&sort_order=ASC&page=' . $page . '&filters[date][from]=' . $formattedDate . '&per_page=' . $per_page);
            $res = $response->json();

            $leads = $res['call_trackings'] ?? [];
            if ($leads) {

                foreach ($leads as $value) {
                    echo "\n#";
                    echo ($value['id']);

                    $mobileNumber = trim($value['phone']);
                    $lead         = Lead::where('my_crm_id', $value['id'])->first();
                    $newLead      = false;
                    $source_id    = 31;

                    if ($lead) {
                        continue;
                    }

                    if (! empty($value)) {
                        // Checking Source
                        switch ($value['imported_from']) {
                            case 'propertyfinder':
                                $source_id = 31; // Property Finder Id
                                break;
                            case 'online':
                                $source_id = 45; // Online Id
                                break;
                            case 'other':
                                $source_id = 44; // Other Id
                                break;
                            default:
                                $source_id = 31; // Property Finder Id
                                break;
                        }

                        $medium = Medium::where('name', "LIKE", "%" . 'Call' . "%")->first();

                        $for                = null;
                        $from_bed_id        = null;
                        $to_bed_id          = null;
                        $from_bath_id       = null;
                        $to_bath_id         = null;
                        $community          = null;
                        $sub_community      = null;
                        $community_property = null;
                        $from_price         = null;
                        $to_price           = null;
                        $property           = null;

                        $propertyReference = $value['reference'] ?? null;
                        if ($propertyReference) {
                            // $property = Property::where('ref_no', $propertyReference)->first();
                            // if($property){
                            //     $for = $property->for;
                            //     $from_bed_id = $property?->bed?->id;
                            //     $to_bed_id = $property?->bed?->id;
                            //     $from_bath_id = $property->bath?->id;
                            //     $to_bed_id = $property?->bath?->id;
                            //     $from_price = $property?->price?->price ?? null;
                            //     $to_price = $property?->price?->price ?? null;
                            //     if($property->community_id ?? null){
                            //         $community = Community::find($property->community_id);
                            //     }
                            //     if($property->sub_community_id ?? null){
                            //         $sub_community = SubCommunity::find($property->sub_community_id);
                            //     }
                            //     if($property->community_property_id ?? null){
                            //         $community_property = CommunityProperty::find($property->community_property_id);
                            //     }
                            // }

                        }
                        $user = null;
                        // dd($value['user']);
                        if (isset($value['user']['public']['email'])) {
                            if ($value['user']['public']['email'] == "abudhabi@axcapital.ae") {
                                $user = User::where('email', "j.kinana@axcapital.ae")->first();
                            } else {
                                $user = User::where('email', $value['user']['public']['email'])->first();
                                if (! $user) {
                                    $users = Http::withHeaders([
                                        'Authorization' => 'Bearer ' . $bearerToken,
                                        'User-Agent: PostmanRuntime/7.28.0',
                                    ])->get('https://api-v2.mycrm.com/users/' . $value['user']['id']);
                                    $userRes = $users->json();
                                    //dd($userRes);
                                    $userData = $userRes['user'] ?? [];

                                    if ($userData) {
                                        if (isset($userData['private']['email'])) {
                                            if ($userData['private']['email']) {
                                                $user = User::where('email', $userData['private']['email'])->first();
                                                if ($user) {
                                                    $user->old_user_id = $value['user']['id'];
                                                    $user->save();
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        $leadStatusesArray = [
                            "answered"    => 5,
                            "voicemail"   => 10,
                            "canceled"    => 10,
                            "notanswered" => 10,
                        ];

                        if (! $lead) {
                            $lead = new Lead();

                            $lead->my_crm_id      = $value['id'];
                            $lead->agent_id       = $user?->id;
                            $lead->property_id    = $property?->id ?? null;
                            $lead->first_name     = $value['first_name'] ?? null;
                            $lead->last_name      = $value['last_name'] ?? null;
                            $lead->email          = $value['email'] ?? null;
                            $lead->phone          = $mobileNumber;
                            $lead->for            = $for ? [ucfirst($for)] : null;
                            $lead->mobile         = $mobileNumber;
                            $lead->whatsapp       = $mobileNumber;
                            $lead->address        = $value['address'] ?? null;
                            $lead->lead_status_id = $leadStatusesArray[$value['status']] ?? 1;
                            $lead->type_id   = 11; // Unspecified
                            // $lead->lead_sub_status_id = $sub_status ?? null;
                            $lead->lead_medium_id = $medium?->id ?? null;
                            $lead->source_id      = $source_id;
                            $lead->portal_created_at      = ($value['created_at'] ?? null) ? Carbon::parse($value['created_at']) : null;

                            $lead->save();
                            $lead->refresh();
                            $importedCount++;
                            event(new \App\Events\LeadCreated($lead));
                            activity()
                                ->performedOn($lead)
                                ->event('Created')
                                ->log('Created New Lead From Parser Api');
                            $leadNote          = new LeadNote();
                            $leadNote->lead_id = $lead->id;
                            $responseText      = json_encode($value);

                            $leadNote->note = 'Imported from Expert Property Finder Call Tracking Api
                                <br><code><pre>' . $responseText . "</pre></code>";
                            $leadNote->save();
                        }
                        // else{
                        //     $lead->agent_id = $user?->id;
                        //     $lead->save();
                        //     $leadNote = new LeadNote();
                        //     $leadNote->lead_id = $lead->id;
                        //     $responseText = json_encode($value);

                        //     $leadNote->note = 'Updated from Expert Property Finder Call Tracking Api
                        //     <br><code><pre>'. $responseText."</pre></code>";
                        //     $leadNote->save();
                        // }

                        $leadCriteria = LeadCriteria::where('lead_id', $lead->id)->first() ?? new LeadCriteria();

                        $leadCriteria->lead_id     = $lead->id;
                        $leadCriteria->min_bed_id  = $from_bed_id;
                        $leadCriteria->max_bed_id  = $to_bed_id;
                        $leadCriteria->min_bath_id = $from_bath_id;
                        $leadCriteria->max_bath_id = $to_bath_id;
                        $leadCriteria->min_price   = $from_price ?? null;
                        $leadCriteria->max_price   = $to_price ?? null;

                        $leadCriteria->save();

                        LeadCriteriaLocation::where('lead_criteria_id', $leadCriteria->id)->delete(); // deleting old locations to prevent duplication
                        if ($community) {
                            $communityLocation = LeadCriteriaLocation::where('parentable_type', Community::class)->where('parentable_id', $community->id)->where('lead_criteria_id', $leadCriteria->id)->exists();
                            if (! $communityLocation) {
                                $obj                   = new LeadCriteriaLocation();
                                $obj->parentable_type  = Community::class;
                                $obj->parentable_id    = $community?->id;
                                $obj->lead_criteria_id = $leadCriteria->id;
                                $obj->save();
                            }
                        }
                        if ($sub_community) {
                            $communityLocation = LeadCriteriaLocation::where('parentable_type', SubCommunity::class)->where('parentable_id', $sub_community?->id)->where('lead_criteria_id', $leadCriteria->id)->exists();
                            if (! $communityLocation) {
                                $obj                   = new LeadCriteriaLocation();
                                $obj->parentable_type  = SubCommunity::class;
                                $obj->parentable_id    = $sub_community?->id;
                                $obj->lead_criteria_id = $leadCriteria->id;
                                $obj->save();
                            }
                        }
                        if ($community_property) {
                            $communityLocation = LeadCriteriaLocation::where('parentable_type', CommunityProperty::class)->where('parentable_id', $community_property?->id)->where('lead_criteria_id', $leadCriteria->id)->exists();
                            if (! $communityLocation) {
                                $obj                   = new LeadCriteriaLocation();
                                $obj->parentable_type  = CommunityProperty::class;
                                $obj->parentable_id    = $community_property?->id;
                                $obj->lead_criteria_id = $leadCriteria->id;
                                $obj->save();
                            }
                        }
                    }
                }
            }
        }
        $time_end = microtime(true);
        $time     = $time_end - $time_start;
        echo ("\n $importedCount leads are imported successfully");
        return $importedCount;
    }

    public function leadUpdateStatus(Request $request)
    {
        // activity()->withoutLogs(function () use ($request) {
        activity()->disableLogging();

        if (env('APP_ENV') == 'production') {
            $id      = Crypt::decryptString($request->lead);
            $user_id = Crypt::decryptString($request->user);
        } else {
            $id      = $request->lead;
            $user_id = $request->user;
        }

        // $id = Crypt::decryptString($request->lead);
        // $user_id = Crypt::decryptString($request->user);

        $lead                 = Lead::find($id);
        $orignal              = $lead->getOriginal();
        $user                 = User::find($user_id);
        $lead->lead_status_id = $request->status_id;
        $lead->updated_by_id  = $user->id;

        $dirty   = ! empty($lead->getDirty()) ? $lead->getDirty() : $lead->getChanges();
        $orignal = collect($orignal)
            ->only(collect($dirty)->keys())
            ->toArray();
        // });

        if ($request->status_id == 8) {
            if (empty($request->note) || strlen($request->note ?? "") < 35) {
                return response()->json(['status' => '0', 'msg' => 'Minimum 35 characters required.'], 422);
            }
        }

        if ($lead->save()) {
            activity()->enableLogging();

            $arr = [
                'attributes' => $dirty,
                'old'                => $orignal,
                'lead_id' => $lead->id
            ];
            $arr = [
                'attributes' => $dirty,
                'old'                => ! empty($orignal) ? $orignal : $lead->getOriginalMatchingChanges()
            ];
            $test = activity()
                ->performedOn($lead)
                ->causedBy($user->id)
                ->withProperties($arr)
                ->event('updated')
                ->log($user->first_name . ' has updated Lead Status ' . $lead->id);

            if (!empty($request->note)) {
                $obj = new LeadNote();
                $obj->lead_id = $lead->id;
                $obj->note = $request->note;
                $obj->created_by_id = $user_id;
                $obj->save();
            }

            return response()->json(['status' => '1', 'msg' => 'Lead Status Updated Successfully']);
        }

        // dd($user);

    }

    public function archiveLead(Request $request)
    {

        if (env('APP_ENV') == 'production') {
            $id      = Crypt::decryptString($request->lead_id);
            $user_id = Crypt::decryptString($request->user_id);
        } else {
            $id      = $request->lead_id;
            $user_id = $request->user_id;
        }

        // $id = Crypt::decryptString($request->lead_id);
        // $user_id = Crypt::decryptString($request->user_id);

        $lead = Lead::find($id);
        $user = User::find($user_id);

        $lead->is_archive    = 1;
        $lead->updated_by_id = $user->id;
        $lead->save();

        LeadNote::create([
            "lead_id"       => $lead->id,
            "note"          => "Archive: " . $request->archive_note,
            'status'        => 1,
            "created_by_id" => $user->id,
        ]);

        // dd($user);
        $test = activity()
            ->performedOn($lead)
            ->causedBy($user->id)
            ->event('updated')
            ->log($user->first_name . ' archived this lead ' . $lead->id);
        return response()->json(['status' => '1', 'msg' => 'Lead Archived Updated Successfully']);
    }

    public function leadAssignment($id, $m_id)
    {

        if (env('APP_ENV') == 'production') {
            $manager_id = Crypt::decryptString($m_id);
            $lead_id    = Crypt::decryptString($id);
        } else {
            $manager_id = $m_id;
            $lead_id    = $id;
        }

        $lead = Lead::find($lead_id);

        $manager = User::find($manager_id);
        // dd($manager);
        if ($lead && ($lead->agent_id && $lead->agent_id !== 65)) {
            $agent = User::find($lead->agent_id);

            if ($agent->id == $lead->assigned_by_id) {
                $msg = 'This lead [' . $lead->id . '] has been claimed from lead pool by ' . $agent->full_name . '.';
            }

            if ($agent->id !== $lead->assigned_by_id) {

                $assignment_by = User::find($lead->assigned_by_id);
                $assignmentBy  = $assignment_by->full_name ?? 'System';
                $msg           = 'This lead [' . $lead->id . '] has already been assigned to ' . $agent->full_name . ' by ' . $assignmentBy . '.';
            }

            return view('form.message')
                ->with('msg', $msg);
        }

        $manager = User::whereHas("roles", function ($q) {
            $q->whereIn("name", ['Line Manager', 'Team Leader', 'Sales Director', 'Head of Secondary', 'Super Admin']);
        })->where('id', $manager_id)->where("status", true)->first();

        if (! $manager) {
            abort(403);
        }

        $notAdmin    = $manager->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent", "Super Admin"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds($manager->id);
        }
        $query  = User::whereHas("roles", fn($q) => $q->whereIn("name", ["Sales Director", "Line Manager", "Team Leader", "Agent", "Super Admin"]));
        $agents = $query->when($childrenIds, function ($q) use ($childrenIds) {
            $q->whereIn('id', $childrenIds);
        });
        $agents = $query->where('status', 1)->orderBy('first_name', 'asc')->get();

        // dd($agents);
        // $users = User::whereHas("roles", function ($q) {
        //     $q->whereIn("name", ["Agent"]);
        // })->where('parent_id',$manager->id)->where("status", true)->get();
        // dd($manager);
        return view('form.leadAssignment')
            ->with('lead', $lead)
            ->with('users', $agents)
            ->with('manager', $manager);
    }

    public function leadAssignmentPost(Request $request)
    {

        $request->validate([
            'agent_id'   => 'required|exists:users,id',
            'manager_id' => 'required|exists:users,id',
            'lead_id'    => 'required|exists:leads,id',
        ]);

        try {
            $lead            = Lead::find($request->lead_id);
            $communities     = '';
            $sub_communities = '';
            $locations       = $lead?->criteria?->locations ?? [];
            foreach ($locations as $location) {

                if ($location->parentable_type == 'App\Models\Community') {
                    $communities = $communities . ' ' . Community::find($location->parentable_id)->name . ', ';
                } else {

                    $sub_communities = $sub_communities . ' ' . $location->parentable_type::find($location->parentable_id)->name . ', ';
                }
            }
            $agent         = User::find($request->agent_id);
            $manager       = User::find($request->manager_id);
            $assignment_by = User::find($manager->id);

            if ($lead->agent_id == null) {
                $lead->agent_id       = $request->agent_id;
                $lead->lead_status_id = 1;
                $lead->updated_by_id  = $manager->id;
                $lead->assigned_by_id = $manager->id;
                $lead->save();
            } else {

                if ($assignment_by->id == $lead->agent_id) {
                    return response()->json(['status' => '3', 'msg' => 'This lead [' . $lead?->id . '] has been claimed from lead pool by ' . $lead?->agent?->full_name . '.', 'data' => []]);
                } else {
                    return response()->json(['status' => '3', 'msg' => 'This lead [' . $lead?->id . '] has already been assigned to ' . $lead?->agent?->full_name . ' by ' . $assignment_by?->full_name . '.', 'data' => []]);
                }
            }

            $obj                = new LeadNote();
            $obj->lead_id       = $lead->id;
            $obj->note          = $agent->first_name . ' ' . $agent->last_name . ' as a Agent Assigned by ' . $assignment_by->full_name . '.';
            $obj->created_by_id = auth()->id();
            $obj->save();

            $environment = \App::environment();
            $min_beds    = null;
            $max_beds    = null;
            $baths       = null;
            if ($lead->criteria?->min_bed_id) {
                $min_beds = Bed::find($lead->criteria?->min_bed_id);
            }
            if ($lead->criteria?->max_bed_id) {
                $max_beds = Bed::find($lead->criteria?->max_bed_id);
            }

            if ($environment == "production") {

                $message = "<b>AX Secondary CRM</b>\n\nA new <b>Lead</b> [" . $lead?->id . "] has been assigned to you by " . $assignment_by?->full_name . ".\n";
                $message .= "\n<b> Property Details: </b> ";
                // $message .= "\n<b> Purpose: </b> " . $lead->for;
                ($lead->for == null ? $message .= "\n<b> Purpose: </b> " . $lead?->for : null);
                $message .= "\n<b> Community: </b> " . $communities;
                $message .= "\n<b> Location: </b> " . $sub_communities;
                $message .= "\n<b> Beds: </b> " . $min_beds?->name . ' - ' . $max_beds?->name;
                $message .= "\n<b> Budget: </b> " . formatPrice($lead?->criteria?->min_price) . ' - ' . formatPrice($lead?->criteria?->max_price);
                $message .= "\n\n<b> Lead Details: </b> ";
                ($lead->first_name != " " ? $message .= "\n<b> Name: </b> " . $lead?->first_name . ' ' . $lead?->last_name : null);
                $agents = User::whereHas("roles", function ($q) {
                    $q->whereIn("name", ["Agent"]);
                })->where('id', $lead->agent_id)->where("status", true)->get();

                if ($agent->telegram_chatid != null) {
                    $this->sendTelegramMessageHtml($message, [$agent->telegram_chatid], null, null, 'lead_bot');
                }
                telegramNotification('Lead', $lead->id, $agent->telegram_chatid, $agent->id, $message);

                event(new LeadAppNotification($lead, 'assignedByManager', $agent, $assignment_by));
            }

            return response()->json(['status' => '1', 'msg' => 'Lead Assign to ' . $agent->full_name . '.', 'data' => []]);
        } catch (\Throwable $th) {
            info($th);
            return response()->json(['status' => '0', 'msg' => 'Something went wrong!!', 'error' => "External Api Controller: " . $th->getLine()]);
        }
    }

    //=======================================================================================================
    //==================================== Listing Lead Related functions ===================================
    //=======================================================================================================

    public function ListingLeadCreate(Request $request)
    {
        //  dd($request->all());
        $categories = PropertyCategory::all();
        $beds       = Bed::all();
        $baths      = Bath::all();
        $types      = Type::all();
        $mediums    = Medium::all();
        $inhouse    = 1;

        if ($request->source) {
            $source = explode('-', $request->source);
            if ($source[1] == 129) {
                $inhouse = 0;
            }
        }

        return view('form.leadListing')
            ->with('categories', $categories)
            ->with('beds', $beds)
            ->with('baths', $baths)
            ->with('types', $types)
            ->with('mediums', $mediums)
            ->with('inhouse', $inhouse);
    }

    public function storeListingLead(Request $request)
    {
        $lead_dump       = new ListingLeadDump();
        $lead_dump->data = json_encode($request->all());
        $lead_dump->save();

        Log::channel('urlLogs')->info('Thirdparty form data.', ['data' => $request->all()]);

        $request->validate([
            'first_name'           => 'required',
            'last_name'            => 'nullable',
            'mobile'               => 'required',
            'location_id'          => 'required',
            'location_type'        => 'required',
            "for"                  => "required",
            'source_id'            => 'nullable',

            'user_id'              => 'nullable',
            'bed_id'               => 'nullable',
            'bath_id'              => 'nullable',
            'email'                => 'nullable|email',
            'recording_file'       => 'nullable|mimes:mp3,wav,ogg,mpga,m4a,3gp,3gpp,amr,|max:30000',
            'property_category_id' => 'nullable',
        ]);

        if (checkBlocklist($request->mobile, ListingLead::class, $request)) {
            $result = ['status' => '0', 'msg' => 'This number is in Blacklist. Please do not contact with this number.', 'error' => 'This number is in Blacklist. Please do not contact with this number.'];
            return json_encode($result);
        }

        try {

            $lastSevenDigits = substr($request->mobile, -7);

            $property = Property::where('unit_no', $request->unit_no)
                ->where('for', $request->for)
                ->where('publish_status', 'PUBLISHED')
                ->where('is_live', 1)
                ->where($request->input('location_type') . "_id", $request->location_id)
                ->whereHas('owner', function ($query) use ($request) {
                    $lastSevenDigits = substr($request->mobile, -7);
                    $query->whereRaw("RIGHT(mobile, 7) = ?", [$lastSevenDigits]);
                })
                ->first();
            if ($property) {
                $lead_dump->status = 0; // lead already existed
                $lead_dump->save();
                Log::channel('offplan')->info('Listing Lead Added But Listing Lead Already Exists.', ['data' => $request->all()]);
                return response()->json(['status' => '1', 'msg' => 'Listing Lead Added Successfully', 'id' => $property->id]);
            }

            $listingLead = ListingLead::when($request->unit_no, function ($q) {
                $q->where("unit_no", \request("unit_no"));
            })
                ->when($request->for, function ($q) {
                    $q->whereJsonContains('for', \request("for"));
                })
                ->where($request->input('location_type') . "_id", $request->location_id)
                ->whereRaw("RIGHT(mobile, 7) = ?", [$lastSevenDigits])
                ->first();

            if ($listingLead) {
                $exist_note                  = new ListingLeadNote();
                $exist_note->listing_lead_id = $listingLead->id;
                $exist_note->note            = generateMessage($request->all(), 'Listing Leads');
                $exist_note->save();
                $lead_dump->status = 2; // to avoid duplicate entry in listing_lead_dumps table means NOTE only saved
                $lead_dump->save();
                return response()->json(['status' => '1', 'msg' => 'Listing Lead Added Successfully', 'id' => $listingLead->id]);
                // return response(['status' => 1, 'data' => $listingLead, 'message' => 'Record already Exists.']);
            }
            $property             = new ListingLead();
            $property->first_name = $request->first_name;
            $property->last_name  = $request->last_name;
            $property->email      = $request->email;
            $property->mobile     = validatePhoneNumber($request->mobile);

            $property->source_id = 50;
            if ($request->source_id) {
                $source              = explode('-', $request->source_id);
                $property->source_id = $source[1];
            }
            if ($request->medium_id) {
                $medium              = explode('-', $request->medium_id);
                $property->medium_id = $medium[1];
            }
            if ($request->user_id) {
                $user                    = explode('-', $request->user_id);
                $property->created_by_id = $user[1];
            }

            $property->for           = is_null($request->for) ? [] : $request->for;
            $property->bed_id        = $request->bed_id;
            $property->bath_id       = $request->bath_id;
            $property->location_id   = $request->location_id;
            $property->location_type = $request->location_type;

            $property->unit_no              = $request->unit_no;
            $property->note                 = $request->note;
            $property->property_category_id = $request->property_category_id;

            if ($request->location_type == "community_property") {
                $communityProperty     = CommunityProperty::find($request->location_id);
                $community_property_id = $communityProperty->id ?? null;
                $sub_community_id      = $communityProperty->sub_community_id ?? null;
                $community_id          = $communityProperty->community_id ?? null;
            } else {

                $subCommunity          = SubCommunity::find($request->location_id);
                $community_property_id = null;
                $sub_community_id      = $subCommunity->id ?? null;
                $community_id          = $subCommunity->community_id ?? null;
            }

            if ($request->file("recording_file")) {
                $path                     = $this->uploadDocument($request->file("recording_file"), "lead_listing");
                $property->recording_file = $path;
            }

            $property->sub_community_id      = $sub_community_id;
            $property->community_id          = $community_id;
            $property->community_property_id = $community_property_id;

            $property->manager_id = $request->manager_id;
            $property->agent_id   = $request->agent_id;
            $property->status_id  = 1; // new listing lead added
            $property->save();
            $lead_dump->status = 1; // new listing lead added
            $lead_dump->save();

            $environment = \App::environment();
            if ($environment == "production") {
                $message = "<b>AX Secondary CRM</b>\nJust received a new <b>Listing Lead</b> via " . $property?->source?->name . ". Kindly assign it to an agent.\n";
                $message .= "\n<b> Property Details: </b> ";
                $message .= "\n<b> ID: </b> " . $property?->id;
                $message .= "\n<b> Type: </b> " . $property?->propertyCategory?->title;
                $message .= "\n<b> Location: </b> " . $property->location;
                $message .= "\n<b> Beds: </b> " . $property?->bed?->name;
                $message .= "\n\n<b> Owner Details: </b> ";
                ($property->first_name != " " ? $message .= "\n<b> Name: </b> " . $property?->first_name . ' ' . $property?->last_name : null);
                ($property->mobile ? $message .= "\n<b> Mobile: </b> " . $property?->mobile : null);
                ($property->email ? $message .= "\n<b> Email: </b> " . $property?->email : null);

                // $usersWithCommunity = UserLocation::where('parentable_type', 'App\Models\Community')
                //     ->where('parentable_id', $property->community_id)
                //     ->pluck('user_id');

                // if ($usersWithCommunity) {
                $agents = User::whereHas("roles", function ($q) {
                    $q->whereIn("name", ["Line Manager", "Team Leader", "Sales Director"]);
                })->where('region_id', '!=', 2)
                    // ->when($property->community_id, function ($q) use ($usersWithCommunity) {
                    //     $q->whereIn('id', $usersWithCommunity);
                    // })
                    ->where("status", true)->get();
                foreach ($agents as $agent) {
                    if ($agent->telegram_chatid != null) {
                        $url = route('api.thirdparty.listingLeadAssignment', [Crypt::encryptString($property->id), Crypt::encryptString($agent->id)]);
                        Log::channel('urlLogs')->info('URL Listing lead: Manager:' . $agent->full_name, ['id' => $property->id, 'data' => $url]);
                        $this->sendTelegramMessageHtml($message, [$agent->telegram_chatid], $url, 'Assign Lead', 'lead_bot');
                    }
                    telegramNotification('ListingLead', $lead->id, $agent->telegram_chatid, $agent->id, $message);
                }
                // }
            }
            return response()->json(['status' => '1', 'msg' => 'Listing Lead Added Successfully', 'id' => $property->id]);
        } catch (\Exception $th) {
            return response()->json(['status' => '0', 'msg' => "External Api Controller line " . $th]);
        }
    }

    public function validateNumbersContacts(Request $request)
    {
        $model = $request->model;
        $count = $request->count;

        $contacts = $model::take($count)->get();
        $phones   = 0;
        $mobiles  = 0;
        foreach ($contacts as $con) {
            if ($con->phone != null && $con->phone != '' && $con->phone != 'null') {
                $con->phone = validatePhoneNumber($con->phone);
                $phones++;
            }
            $num = validatePhoneNumber($con->phone);
            if ($con->mobile != null && $con->mobile != '' && $con->mobile != 'null') {
                $con->mobile = validatePhoneNumber($con->mobile);
                $mobiles++;
            }
            $con->save();
        }
        return response()->json(['status' => '1', 'msg' => 'Numbers Updated Successfully', 'data' => ['phone' => $phones, 'mobile' => $mobiles]]);
    }

    public function listingLeadAssignment($id, $m_id)
    {
        $manager_id = Crypt::decryptString($m_id);
        $lead_id    = Crypt::decryptString($id);
        $lead       = ListingLead::find($lead_id);

        if ($lead && $lead->agent_id) {
            $agent       = User::find($lead->agent_id);
            $manager     = User::find($lead->manager_id);
            $managerName = $manager->full_name ?? 'System';
            $msg         = 'This Listing lead [' . $lead->id . '] has already been assigned to ' . $agent->full_name . ' by ' . $managerName . '.';
            return view('form.message')
                ->with('msg', $msg);
        }

        $manager = User::whereHas("roles", function ($q) {
            $q->whereIn("name", ['Line Manager', 'Team Leader', 'Sales Director', 'Head of Secondary', 'Super Admin']);
        })->where('id', $manager_id)->where("status", true)->first();

        if (! $manager) {
            abort(403);
        }

        // $users = User::whereHas("roles", function ($q) {
        //     $q->whereIn("name", ["Agent"]);
        // })->where('parent_id',$manager->id)->where("status", true)->get();

        $notAdmin    = $manager->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent", "Head of Secondary", 'Super Admin']);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds($manager->id);
        }
        $query  = User::whereHas("roles", fn($q) => $q->whereIn("name", ["Sales Director", "Line Manager", "Team Leader", "Agent", 'Super Admin']));
        $agents = $query->whereIn("id", $childrenIds);
        $agents = $query->where('status', 1)->orderBy('first_name', 'asc')->get();

        // dd($manager);

        return view('form.listingLeadsAssignment')
            ->with('lead', $lead)
            ->with('users', $agents)
            ->with('manager', $manager);
    }

    public function listingLeadAssignmentPost(Request $request)
    {
        $request->validate([
            'agent_id'        => 'required|exists:users,id',
            'manager_id'      => 'required|exists:users,id',
            'listing_lead_id' => 'required|exists:listing_leads,id',
        ]);

        try {
            $lead    = ListingLead::find($request->listing_lead_id);
            $agent   = User::find($request->agent_id);
            $manager = User::find($request->manager_id);
            if ($lead->agent_id == null) {
                $lead->agent_id       = $request->agent_id;
                $lead->manager_id     = $request->manager_id;
                $lead->status_id      = 2;
                $lead->assigned_by_id = $request->manager_id;
                $lead->save();
            } else {
                return response()->json(['status' => '3', 'msg' => 'This Listing lead [' . $lead->id . '] has already been assigned to ' . $lead->agent->full_name . '.', 'data' => []]);
            }

            $obj                  = new ListingLeadNote();
            $obj->listing_lead_id = $lead->id;
            $obj->note            = 'Listing Lead has been assigned to ' . $agent->first_name . ' ' . $agent->last_name . ' by ' . $manager->full_name . '.';
            $obj->created_by_id   = auth()->id();
            $obj->save();

            $environment = \App::environment();
            if ($environment == "production") {
                $message = "<b>AX Secondary CRM</b>\n\nA new <b>Listing Lead </b> has been assigned to you by (" . $manager->full_name . ").\n";
                $message .= "\n<b> Property Details: </b> ";
                $message .= "\n<b> ID: </b> " . $lead?->id;
                $message .= "\n<b> Type: </b> " . $lead?->propertyCategory?->title;
                $message .= "\n<b> Location: </b> " . $lead->location;
                $message .= "\n<b> Beds: </b> " . $lead?->bed?->name;
                $message .= "\n\n<b> Owner Details: </b> ";
                ($lead->first_name != " " ? $message .= "\n<b> Name: </b> " . $lead?->first_name . ' ' . $lead?->last_name : null);
                if ($agent->telegram_chatid != null) {
                    $this->sendTelegramMessageHtml($message, [$agent->telegram_chatid], null, null, 'lead_bot');
                }
                telegramNotification('ListingLead', $lead->id, $agent->telegram_chatid, $agent->id, $message);

                event(new ListingLeadAppNotification($lead, 'assignedByManager', $agent, $manager));
            }
            return response()->json(['status' => '1', 'msg' => 'Listing Lead Assign to ' . $agent->full_name . '.', 'data' => []]);
        } catch (\Throwable $th) {
            return response()->json(['status' => '0', 'msg' => 'Something went wrong!!', 'error' => "External Api Controller: " . $th->getMessage()]);
        }
    }

    //=======================================================================================================
    //==================================== Property Finder Related functions ================================
    //=======================================================================================================

    public function fetchPFData(Request $request)
    {

        $validatedData = $request->validate([
            "ref_no" => "required|exists:properties,ref_no",
        ]);
        $portal = Portal::where('name', 'Property Finder')->first();
        $agent  = [];
        try {
            $property = Property::where('ref_no', $request->ref_no)->first();
            if ($property->agent_id) {
                $agent = User::find($property->agent_id);
            }

            // if ($property->region_id == 2) {
            //     $token = env('PROPERTY_FINDER_ABU_DHABI_TOKEN');
            //      $token_data = $this->getPfAccessToken('primary');
            // } elseif ($property->completion_status == "Off-Plan Primary" && (!$agent?->is_commercial)) {
            //     $token = env('PROPERTY_FINDER_PRIMARY_TOKEN');
            //     $token_data = $this->getPfAccessToken('primary');
            // } elseif ($agent?->is_commercial) {
            //     $token = env('PROPERTY_FINDER_COMMERCIAL_TOKEN');
            //     $token_data = $this->getPfAccessToken('commercial');
            // } else {
            //     $token = env('PROPERTY_FINDER_SECONDARY_TOKEN');
            //     $token_data = $this->getPfAccessToken('secondary');
            // }
            $token = $this->getPFTokenBasedOnProperty($property);

            $header   = [];
            $header[] = 'Content-Type: application/json';
            $header[] = 'Content-Length: 4000';
            $header[] = 'Connection: keep-alive';

            $curl = curl_init();
            //dd($token);
            curl_setopt_array($curl, [
                CURLOPT_URL            => 'https://api-v2.mycrm.com/properties?filters[reference]=' . $request->ref_no,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING       => '',
                CURLOPT_MAXREDIRS      => 10,
                CURLOPT_TIMEOUT        => 200,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST  => 'GET',
                CURLOPT_HTTPHEADER     => [
                    'Authorization: Bearer ' . $token,
                    'User-Agent: PostmanRuntime/7.28.0',
                ],
            ]);

            $res = curl_exec($curl);

            curl_close($curl);

            $res = json_decode($res, true);

            if (isset($res['error']) && $res['error'] == "AuthenticationError") {
                info("Property Finder Token Expired. Property Ref # is " . $request->ref_no);
                return response()->json(['status' => '0', 'msg' => "token expired"]);
            }
            $data = $res['properties'] ?? null;
            //info($data);
            // return $data;
            if (empty($data[0])) {

                $dataDeleted = PropertyMeta::deletePropertyMetaBatch(
                    $property->id,
                    [
                        "pf_rank",
                        "pf_rank_details",
                        "pf_level",
                        "pf_status",
                        "pf_verified",
                    ]
                );
            }

            if (! empty($data[0])) {
                // Storing Rank Value
                $rank = PropertyMeta::storePropertyMeta($property->id, "pf_rank", $data[0]['quality_score']['value'] ?? null);

                // Storing Rank details
                $rank_details = PropertyMeta::storePropertyMeta($property->id, "pf_rank_details", json_encode($data[0]['quality_score']['details'] ?? '') ?? null);
                // Storing Level
                $product_level = PropertyMeta::storePropertyMeta($property->id, "pf_level", ucfirst($data[0]['listing_level']) ?? null);

                $product_level_expiry = PropertyMeta::storePropertyMeta($property->id, "pf_level_expiry", $data[0]['listing_level_date'] ?? null);

                // Storing Status
                $status      = PropertyMeta::storePropertyMeta($property->id, "pf_status", ucfirst($data[0]['listing_status']) ?? null);
                $timestamp   = $data[0]['created_at'];
                $utcDateTime = Carbon::createFromFormat('Y-m-d\TH:i:sP', $timestamp);
                $uaeDateTime = $utcDateTime->setTimezone('Asia/Dubai');

                $uaeTime = $uaeDateTime->toDateTimeString();
                // Storing Status
                $date = PropertyMeta::storePropertyMeta($property->id, "pf_created_at", $uaeTime ?? null);

                $timestamp = $data[0]['created_at'];

                $utcDateTime = Carbon::createFromFormat('Y-m-d\TH:i:sP', $timestamp);
                $uaeDateTime = $utcDateTime->setTimezone('Asia/Dubai');

                $uaeTime = $uaeDateTime->toDateTimeString();

                $date = PropertyMeta::storePropertyMeta($property->id, "pf_updated_at", $uaeTime ?? null);
                // Storing Verified
                $tag      = $data[0]['quality_score']['details']['verified']['tag'] ?? '';
                $verified = PropertyMeta::storePropertyMeta($property->id, "pf_verified", $tag == "Yes" ? "Verified" : "Not Verified");
                // Url of the listing
                $propertyId = $data[0]['quality_score']['property_id'] ?? null;
                $urlLink    = null;
                if ($propertyId) {
                    $urlLink = "https://www.propertyfinder.ae/to/" . $data[0]["quality_score"]["property_id"] . "/en";
                }

                $url = PropertyMeta::storePropertyMeta($property->id, "pf_url", $urlLink ?? null);
            }
            return response()->json(['status' => '1', 'msg' => 'Data Fetched Successfully', 'data', $data]);
        } catch (\Exception $exception) {
            $result = ['status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage(), 'line' => $exception->getLine(), 'file' => $exception->getFile()];
            info($result);
            return response()->json($result);
        }
    }
    public function fetchPFDataV2(Request $request)
    {

        $validatedData = $request->validate([
            "ref_no" => "required|exists:properties,ref_no",
        ]);
        $portal = Portal::where('name', 'Property Finder')->first();
        $agent  = [];
        try {
            $property = Property::where('ref_no', $request->ref_no)->first();
            if ($property->agent_id) {
                $agent = User::find($property->agent_id);
            }

           // $token = $this->getPFTokenBasedOnPropertyV2($property);
            $data = $this->propertyFinderService->findListingByReference($request->ref_no);



            $pf_data = $res['properties'] ?? null;
            //info($data);
            // return $data;
            if (empty($data)) {

                $dataDeleted = PropertyMeta::deletePropertyMetaBatch(
                    $property->id,
                    [
                        "pf_rank",
                        "pf_rank_details",
                        "pf_level",
                        "pf_status",
                        "pf_verified",
                    ]
                );
            }

            if (! empty($data)) {

                // Storing Rank Value
                $rank = PropertyMeta::storePropertyMeta($property->id, "pf_rank", $data['qualityScore']['value'] ?? null);

                // Storing Rank details
                $rank_details = PropertyMeta::storePropertyMeta($property->id, "pf_rank_details", json_encode($data['qualityScore']['details'] ?? '') ?? null);
                // Storing Level
                $pf_product_level = isset($data['products']['standard']) ? 'Standard' : (isset($data['products']['premium']) ? 'Premium' : (isset($data['products']['featured']) ? 'Featured' : null));
                PropertyMeta::storePropertyMeta($property->id, "pf_level", $pf_product_level ?? null);
//
//                $product_level_expiry = PropertyMeta::storePropertyMeta($property->id, "pf_level_expiry", $data[0]['listing_level_date'] ?? null);

                // Storing Status
                $stage = $data['state']['stage'] ?? null;
                $listing_status = $stage == 'live' ? 'Online' : 'Offline';
                PropertyMeta::storePropertyMeta($property->id, "pf_status", ucfirst($listing_status) ?? null);
                $timestamp = $data['createdAt'];

                $uaeDateTime = Carbon::parse($timestamp)->setTimezone('Asia/Dubai')->format('Y-m-d H:i:s');


                 PropertyMeta::storePropertyMeta($property->id, "pf_created_at", $uaeDateTime ?? null);

                $timestamp = $data['updatedAt'];

                $uaeDateTime = Carbon::parse($timestamp)->setTimezone('Asia/Dubai')->format('Y-m-d H:i:s');


                PropertyMeta::storePropertyMeta($property->id, "pf_updated_at", $uaeDateTime ?? null);

                // Storing Verified
                $verified = $data['qualityScore']['details']['verified'] ?? [];
                $tag      = (isset($verified['tag']) &&  $verified['tag'] == 'Yes') ? 'Verified' : 'Not Verified';
                 PropertyMeta::storePropertyMeta($property->id, "pf_verified", $tag ?? null);
                // Url of the listing
                $propertyId = $data['id'] ?? null;
                $urlLink    = null;
                if ($propertyId) {
                    $urlLink = "https://www.propertyfinder.ae/go/" . $propertyId;
                }

                $url = PropertyMeta::storePropertyMeta($property->id, "pf_url", $urlLink ?? null);
            }
            return response()->json(['status' => '1', 'msg' => 'Data Fetched Successfully', 'data', $data]);
        } catch (\Exception $exception) {
            $result = ['status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage(), 'line' => $exception->getLine(), 'file' => $exception->getFile()];
            info($result);
            return response()->json($result);
        }
    }
    public function fetchBayutData(Request $request)
    {
        // return true;
        // $token = $this->getBayutToken();
        // $validatedData = $request->validate([
        //     "ref_no" => "required|exists:properties,ref_no",
        // ]);
        $portal = Portal::where('name', 'Bayut')->first();

        try {

            $cookie = $portal->token;
            $ref_no = $request->ref_no;
            // $ref_no = 'axc-3985730';

            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL            => 'https://www.bayut.com/api-v7/agents/2381209/listings?page=1&perPage=10&portalTab=active&imageStatus=all&listingKey=' . $ref_no . '&load=adProductPricing&portalIds=1&portalState=active',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING       => '',
                CURLOPT_MAXREDIRS      => 10,
                CURLOPT_TIMEOUT        => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST  => 'GET',
                CURLOPT_HTTPHEADER     => [
                    'accept: */*',
                    'accept-language: en',
                    'app-name: phoenix',
                    'cookie: ' . $cookie,
                    'priority: u=1, i',
                    'referer: https://www.bayut.com/profolio/listings/manage?page=1&perPage=10&portalTab=active&imageStatus=all&portal=bayut&listingKey=' . $ref_no,
                    'sec-ch-ua: "Google Chrome";v="131", "Chromium";v="131", "Not_A Brand";v="24"',
                    'sec-ch-ua-mobile: ?0',
                    'sec-ch-ua-platform: "macOS"',
                    'sec-fetch-dest: empty',
                    'sec-fetch-mode: cors',
                    'sec-fetch-site: same-origin',
                    'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
                    'x-xsrf-token: eyJpdiI6IkRXOThDWmJLbmxyTDNkZmRwUzlXbkE9PSIsInZhbHVlIjoiczJncldOTzN6aERLZGpcL1dJOGFlV29ZN1NqSUV3WVBRWlFPQ2FXdlZiZ09cLzU5OGlUdlh2NzlQTnNZUld6NEc3OXlQK0t2RENwT0dXNXA5ZThxeVpudz09IiwibWFjIjoiMDczZWJlZjRhYzEyOTc1ZDZkMWNlZDIzMjJiYTIwMjdjMmI1YzVlMjFkMTYxYjY0ZGY2Y2Q1OGYwMWZjN2YxOSJ9',
                ],
            ]);

            $response = curl_exec($curl);
            // dd($response);
            curl_close($curl);

            $res = json_decode($response, true);

            curl_close($curl);

            $message = $res['message'] ?? null;

            if ($message == "Unauthenticated.") {
                sleep(2);
                Log::channel('urlLogs')->error("Unauthenticated");
                // $this->getBayutToken('hishmat@axcapital.ae', 'His@1234!', 'token');
                $this->getAccessToken('token');
                // info("Bayut Cookies Expired for Dubai");
                return response()->json(['status' => '0', 'msg' => "token expired"]);
            }

            $data = $res['data'] ?? [];
            // return($res['data']);
            $property = Property::where('ref_no', $ref_no)->first();
            if (empty($data[0])) {

                $dataDeleted = PropertyMeta::deletePropertyMetaBatch(
                    $property->id,
                    [
                        "bayut_rank",
                        // "dubizzle_rank",
                        "bayut_level",
                        // "dubizzle_level",
                        "bayut_status",
                        // "dubizzle_trucheck_status",
                        "bayut_trucheck_status",
                    ]
                );
            }

            if (! empty($data[0])) {
                // Storing Rank Value

                $rank = PropertyMeta::storePropertyMeta($property->id, "bayut_rank", round($data[0]['quality']['listing_rank'] ?? 0));

                // Storing Rank Value
                // $dubrank = PropertyMeta::storePropertyMeta($property->id, "dubizzle_rank", round($data[0]['quality']['listing_rank'] ?? 0));

                // Storing Level
                $bayut_product_level = PropertyMeta::storePropertyMeta($property->id, "bayut_level", ucfirst($data[0]['variant']) ?? null);

                // Storing Level
                // $dubizzle_product_level = PropertyMeta::storePropertyMeta($property->id, "dubizzle_level", ucfirst($data[0]['variant']) ?? null);
                // dd($data[0]['variant']);
                foreach ($data[0]['adProducts'] as $adProduct) {
                    if ($adProduct['portal']['name'] == "dubizzle") {
                        // $dubizzle_product_level = PropertyMeta::storePropertyMeta($property->id, "dubizzle_level", ucfirst($adProduct['adProductName']) ?? null);
                        if ($adProduct['expiresAt']) {
                            $timestamp = $adProduct['expiresAt'];

                            $utcDateTime = Carbon::createFromFormat('Y-m-d\TH:i:s.u\Z', $timestamp, 'UTC');
                            $uaeDateTime = $utcDateTime->setTimezone('Asia/Dubai');

                            $uaeTime                       = $uaeDateTime->toDateTimeString();
                            $dubizzle_product_level_expiry = PropertyMeta::storePropertyMeta($property->id, "dubizzle_level_expires_at", $uaeTime ?? null);
                        }
                    }
                }
                foreach ($data[0]['usedAdProducts'] as $adProduct) {
                    if ($adProduct['portal']['name'] == "bayut" && $bayut_product_level == $adProduct['adProductName']) {
                        if ($adProduct['expiresAt']) {
                            $timestamp = $adProduct['expiresAt'];

                            $utcDateTime = Carbon::createFromFormat('Y-m-d\TH:i:s.u\Z', $timestamp, 'UTC');
                            $uaeDateTime = $utcDateTime->setTimezone('Asia/Dubai');

                            $uaeTime                    = $uaeDateTime->toDateTimeString();
                            $bayut_product_level_expiry = PropertyMeta::storePropertyMeta($property->id, "bayut_level_expires_at", $uaeTime ?? null);
                        }
                    }
                }
                foreach ($data[0]['portals'] as $portal) {
                    if ($portal['name'] == "bayut") {
                        $timestamp = $portal['createdAt'];

                        $utcDateTime = Carbon::createFromFormat('Y-m-d\TH:i:s.u\Z', $timestamp, 'UTC');
                        $uaeDateTime = $utcDateTime->setTimezone('Asia/Dubai');

                        $uaeTime      = $uaeDateTime->toDateTimeString();
                        $bayutCreated = PropertyMeta::storePropertyMeta($property->id, "bayut_created_at", $uaeTime ?? null);
                    } elseif ($portal['name'] == "dubizzle") {
                        $timestamp = $portal['createdAt'];

                        $utcDateTime = Carbon::createFromFormat('Y-m-d\TH:i:s.u\Z', $timestamp, 'UTC');
                        $uaeDateTime = $utcDateTime->setTimezone('Asia/Dubai');

                        $uaeTime             = $uaeDateTime->toDateTimeString();
                        $bayutCreated        = PropertyMeta::storePropertyMeta($property->id, "dubizzle_created_at", $uaeTime ?? null);
                        $dubizzleRefreshedAt = PropertyMeta::storePropertyMeta($property->id, "dubizzle_refreshed_at", $portal['refreshedAt'] ?? null);
                    }
                }
                // Storing Status
                $bayutstatus = PropertyMeta::storePropertyMeta($property->id, "bayut_status", ucfirst($data[0]['status']) ?? null);
                $timestamp   = $data[0]['updatedAt'];

                $utcDateTime = Carbon::createFromFormat('Y-m-d\TH:i:s.u\Z', $timestamp, 'UTC');
                $uaeDateTime = $utcDateTime->setTimezone('Asia/Dubai');

                $uaeTime      = $uaeDateTime->toDateTimeString();
                $bayutupdated = PropertyMeta::storePropertyMeta($property->id, "bayut_updated_at", $uaeTime ?? null);

                $dubizzleUpdatedAt = PropertyMeta::storePropertyMeta($property->id, "dubizzle_updated_at", $uaeTime ?? null);

                $dubizzlestatus = PropertyMeta::storePropertyMeta($property->id, "dubizzle_status", ucfirst($data[0]['status']) ?? null);

                $bayutReviewStatus    = PropertyMeta::storePropertyMeta($property->id, "bayut_review_status", ucfirst($data[0]['reviewStatus']) ?? null);
                $dubizzleReviewStatus = PropertyMeta::storePropertyMeta($property->id, "dubizzle_review_status", ucfirst($data[0]['reviewStatus']) ?? null);

                // Storing Verified
                $verified = PropertyMeta::storePropertyMeta($property->id, "bayut_trucheck_status", ucfirst($data[0]['trucheckStatus']));
                // Storing Verified
                $verified = PropertyMeta::storePropertyMeta($property->id, "dubizzle_trucheck_status", ucfirst($data[0]['trucheckStatus']));
                // Url of the listing
                $bayutUrl = PropertyMeta::storePropertyMeta($property->id, "bayut_url", $data[0]['legacyUrl'] ?? null);
                // dd($data[0]['legacyUrl']);
                // Dubizzle Url of the listing
                $dubizzleUrl = PropertyMeta::storePropertyMeta($property->id, "dubizzle_url", $data[0]['adAttributes']['external_url'] ?? null);
            }
            return response()->json(['status' => '1', 'data' => $res ?? []]);
        } catch (\Exception $exception) {
            $result = ['status' => '0', 'msg' => 'Something went wrong!!', 'error' => "External Api Controller " . $exception->getMessage(), 'line' => $exception->getLine()];
            info($result);
            return response()->json($result);
        }
    }

    public function fetchDubizzleData(Request $request)
    {
        // return true;
        // $token = $this->getBayutToken();
        // $validatedData = $request->validate([
        //     "ref_no" => "required|exists:properties,ref_no",
        // ]);
        $portal = Portal::where('name', 'Bayut')->first();

        try {

            $cookie = $portal->token;
            $ref_no = $request->ref_no;
            // $ref_no = 'axc-3985730';

            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL            => 'https://www.bayut.com/api-v7/agents/2381209/listings?page=1&perPage=10&portalTab=active&imageStatus=all&listingKey=' . $ref_no . '&load=adProductPricing&portalIds=2&portalState=active',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING       => '',
                CURLOPT_MAXREDIRS      => 10,
                CURLOPT_TIMEOUT        => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST  => 'GET',
                CURLOPT_HTTPHEADER     => [
                    'accept: */*',
                    'accept-language: en',
                    'app-name: phoenix',
                    'cookie: ' . $cookie,
                    'priority: u=1, i',
                    'referer: https://www.bayut.com/profolio/listings/manage?page=1&perPage=10&portalTab=active&imageStatus=all&portal=bayut&listingKey=' . $ref_no,
                    'sec-ch-ua: "Google Chrome";v="131", "Chromium";v="131", "Not_A Brand";v="24"',
                    'sec-ch-ua-mobile: ?0',
                    'sec-ch-ua-platform: "macOS"',
                    'sec-fetch-dest: empty',
                    'sec-fetch-mode: cors',
                    'sec-fetch-site: same-origin',
                    'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
                    'x-xsrf-token: eyJpdiI6IkRXOThDWmJLbmxyTDNkZmRwUzlXbkE9PSIsInZhbHVlIjoiczJncldOTzN6aERLZGpcL1dJOGFlV29ZN1NqSUV3WVBRWlFPQ2FXdlZiZ09cLzU5OGlUdlh2NzlQTnNZUld6NEc3OXlQK0t2RENwT0dXNXA5ZThxeVpudz09IiwibWFjIjoiMDczZWJlZjRhYzEyOTc1ZDZkMWNlZDIzMjJiYTIwMjdjMmI1YzVlMjFkMTYxYjY0ZGY2Y2Q1OGYwMWZjN2YxOSJ9',
                ],
            ]);

            $response = curl_exec($curl);
            // dd($response);
            curl_close($curl);

            $res = json_decode($response, true);

            curl_close($curl);

            $message = $res['message'] ?? null;

            if ($message == "Unauthenticated.") {
                sleep(2);
                Log::channel('urlLogs')->error("Unauthenticated");
                // $this->getBayutToken('hishmat@axcapital.ae', 'His@1234!', 'token');
                $this->getAccessToken('token');
                // info("Bayut Cookies Expired for Dubai");
                return response()->json(['status' => '0', 'msg' => "token expired"]);
            }

            $data = $res['data'] ?? [];
            // return($res['data']);
            $property = Property::where('ref_no', $ref_no)->first();
            if (empty($data[0])) {

                $dataDeleted = PropertyMeta::deletePropertyMetaBatch(
                    $property->id,
                    [
                        "dubizzle_rank",
                        "dubizzle_level",
                        "dubizzle_trucheck_status",
                    ]
                );
            }

            if (! empty($data[0])) {
                // Storing Rank Value

                // $rank = PropertyMeta::storePropertyMeta($property->id, "bayut_rank", round($data[0]['quality']['listing_rank'] ?? 0));

                // Storing Rank Value
                $dubrank = PropertyMeta::storePropertyMeta($property->id, "dubizzle_rank", round($data[0]['quality']['listing_rank'] ?? 0));

                // Storing Level
                $bayut_product_level = PropertyMeta::storePropertyMeta($property->id, "dubizzle_level", ucfirst($data[0]['variant']) ?? null);

                // Storing Level
                // $dubizzle_product_level = PropertyMeta::storePropertyMeta($property->id, "dubizzle_level", "Standard" ?? null);
                // dd($data[0]['variant']);

                foreach ($data[0]['adProducts'] as $adProduct) {
                    if ($adProduct['portal']['name'] == "dubizzle") {
                        $dubizzle_product_level = PropertyMeta::storePropertyMeta($property->id, "dubizzle_level", ucfirst($adProduct['adProductName']) ?? null);
                        if ($adProduct['expiresAt']) {
                            $timestamp = $adProduct['expiresAt'];

                            $utcDateTime = Carbon::createFromFormat('Y-m-d\TH:i:s.u\Z', $timestamp, 'UTC');
                            $uaeDateTime = $utcDateTime->setTimezone('Asia/Dubai');

                            $uaeTime                       = $uaeDateTime->toDateTimeString();
                            $dubizzle_product_level_expiry = PropertyMeta::storePropertyMeta($property->id, "dubizzle_level_expires_at", $uaeTime ?? null);
                        }
                    }
                }

                foreach ($data[0]['usedAdProducts'] as $adProduct) {
                    if ($adProduct['portal']['name'] == "dubizzle") {
                        $dubizzle_product_level = PropertyMeta::storePropertyMeta($property->id, "dubizzle_level", ucfirst($adProduct['adProductName']) ?? null);
                        if ($adProduct['expiresAt']) {
                            $timestamp = $adProduct['expiresAt'];

                            $utcDateTime = Carbon::createFromFormat('Y-m-d\TH:i:s.u\Z', $timestamp, 'UTC');
                            $uaeDateTime = $utcDateTime->setTimezone('Asia/Dubai');

                            $uaeTime                       = $uaeDateTime->toDateTimeString();
                            $dubizzle_product_level_expiry = PropertyMeta::storePropertyMeta($property->id, "dubizzle_level_expires_at", $uaeTime ?? null);
                        }
                    }
                }

                foreach ($data[0]['portals'] as $portal) {
                    if ($portal['name'] == "dubizzle") {
                        $timestamp = $portal['createdAt'];

                        $utcDateTime = Carbon::createFromFormat('Y-m-d\TH:i:s.u\Z', $timestamp, 'UTC');
                        $uaeDateTime = $utcDateTime->setTimezone('Asia/Dubai');

                        $uaeTime             = $uaeDateTime->toDateTimeString();
                        $bayutCreated        = PropertyMeta::storePropertyMeta($property->id, "dubizzle_created_at", $uaeTime ?? null);
                        $dubizzleRefreshedAt = PropertyMeta::storePropertyMeta($property->id, "dubizzle_refreshed_at", $portal['refreshedAt'] ?? null);
                    }
                }
                // Storing Status
                // $bayutstatus = PropertyMeta::storePropertyMeta($property->id, "bayut_status", ucfirst($data[0]['status']) ?? null);
                $timestamp = $data[0]['updatedAt'];

                $utcDateTime = Carbon::createFromFormat('Y-m-d\TH:i:s.u\Z', $timestamp, 'UTC');
                $uaeDateTime = $utcDateTime->setTimezone('Asia/Dubai');

                $uaeTime = $uaeDateTime->toDateTimeString();
                // $bayutupdated = PropertyMeta::storePropertyMeta($property->id, "bayut_updated_at", $uaeTime ?? null);

                $dubizzleUpdatedAt = PropertyMeta::storePropertyMeta($property->id, "dubizzle_updated_at", $uaeTime ?? null);

                $dubizzlestatus = PropertyMeta::storePropertyMeta($property->id, "dubizzle_status", ucfirst($data[0]['status']) ?? null);

                // $bayutReviewStatus    = PropertyMeta::storePropertyMeta($property->id, "bayut_review_status", ucfirst($data[0]['reviewStatus']) ?? null);
                $dubizzleReviewStatus = PropertyMeta::storePropertyMeta($property->id, "dubizzle_review_status", ucfirst($data[0]['reviewStatus']) ?? null);

                // Storing Verified
                // $verified = PropertyMeta::storePropertyMeta($property->id, "bayut_trucheck_status", ucfirst($data[0]['trucheckStatus']));
                // Storing Verified
                $verified = PropertyMeta::storePropertyMeta($property->id, "dubizzle_trucheck_status", ucfirst($data[0]['trucheckStatus']));
                // Url of the listing
                // $bayutUrl = PropertyMeta::storePropertyMeta($property->id, "bayut_url", $data[0]['legacyUrl'] ?? null);
                // dd($data[0]['legacyUrl']);
                // Dubizzle Url of the listing
                $dubizzleUrl = PropertyMeta::storePropertyMeta($property->id, "dubizzle_url", $data[0]['adAttributes']['external_url'] ?? null);
            }
            return response()->json(['status' => '1', 'data' => $res ?? []]);
        } catch (\Exception $exception) {
            $result = ['status' => '0', 'msg' => 'Something went wrong!!', 'error' => "External Api Controller " . $exception->getMessage(), 'line' => $exception->getLine()];
            info($result);
            return response()->json($result);
        }
    }

    public function fetchBayutDataCommercial(Request $request)
    {
        return true;
        $validatedData = $request->validate([
            "ref_no" => "required|exists:properties,ref_no",
        ]);
        $portal = Portal::where('name', 'Bayut')->first();
        try {

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL            => 'https://www.bayut.com/api-v7/agents/2252419/listings?page=1&perPage=10&portalTab=active&imageStatus=all&listingKey=' . $request->ref_no . '&load=adProductPricing&portalIds=1&portalState=active',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING       => '',
                CURLOPT_MAXREDIRS      => 10,
                CURLOPT_TIMEOUT        => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST  => 'GET',
                CURLOPT_HTTPHEADER     => [
                    'accept: */*',
                    'accept-language: en',
                    'cookie: kc_access_token=' . $portal->token_commercial,
                    'priority: u=1, i',
                    'referer: https://www.bayut.com/profolio/listings/manage?page=1&perPage=10&portalTab=active&imageStatus=all&portal=bayut&listingKey=' . $request->ref_no,
                    'sec-ch-ua: "Not/A)Brand";v="8", "Chromium";v="126", "Google Chrome";v="126"',
                    'sec-ch-ua-mobile: ?0',
                    'sec-ch-ua-platform: "macOS"',
                    'sec-fetch-dest: empty',
                    'sec-fetch-mode: cors',
                    'sec-fetch-site: same-origin',
                    'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
                    'x-xsrf-token: eyJpdiI6IlhXZGVra3p1QURPallBUlVWdjVaZUE9PSIsInZhbHVlIjoiK3V5Y0JMQVQrRGNWSkplWHNWMlF6RkZmd05GSWY4UEtobXJDWDJkckZCczRsZDhQM29MNXRORmxJRGhBTVd4Z0ZyVEszYlAza2hYZW1PT1FlZmYrTFE9PSIsIm1hYyI6ImFlMmJmNjUxN2NjMWVkYmIxNzRlOWE2ZDg1ZDI0MjdkOWVkYmUxOWU1Zjk4MTRkMDNmYmUyNzUwOThjMDEzZDIifQ==',
                ],
            ]);

            $response = curl_exec($curl);

            $res = json_decode($response, true);
            curl_close($curl);

            $message = $res['message'] ?? null;
            if ($message == "Unauthenticated.") {
                $this->getBayutToken('commercial@axcapital.ae', 'D@x2024RE', 'token_commercial');
                info("Bayut Cookies Expired for Commercial");
                return response()->json(['status' => '0', 'msg' => "token expired"]);
            }
            $data = $res['data'] ?? [];
            // return($data);
            //return($res['data']);
            $property = Property::where('ref_no', $request->ref_no)->first();

            if (empty($data[0])) {

                $dataDeleted = PropertyMeta::deletePropertyMetaBatch(
                    $property->id,
                    [
                        "bayut_rank",
                        "dubizzle_rank",
                        "bayut_level",
                        "dubizzle_level",
                        "bayut_status",
                        "dubizzle_trucheck_status",
                        "bayut_trucheck_status",
                    ]
                );
            }

            if (! empty($data[0])) {
                // Storing Rank Value

                $rank = PropertyMeta::storePropertyMeta($property->id, "bayut_rank", round($data[0]['quality']['listing_rank'] ?? 0));

                // Storing Rank Value
                $dubrank = PropertyMeta::storePropertyMeta($property->id, "dubizzle_rank", round($data[0]['quality']['listing_rank'] ?? 0));

                // Storing Level
                $bayut_product_level = PropertyMeta::storePropertyMeta($property->id, "bayut_level", ucfirst($data[0]['variant']) ?? null);
                // Storing Level
                $dubizzle_product_level = PropertyMeta::storePropertyMeta($property->id, "dubizzle_level", "Standard" ?? null);
                foreach ($data[0]['adProducts'] as $adProduct) {
                    if ($adProduct['portal']['name'] == "dubizzle") {
                        $dubizzle_product_level = PropertyMeta::storePropertyMeta($property->id, "dubizzle_level", ucfirst($adProduct['adProductName']) ?? null);
                        if ($adProduct['expiresAt']) {
                            $timestamp = $adProduct['expiresAt'];

                            $utcDateTime = Carbon::createFromFormat('Y-m-d\TH:i:s.u\Z', $timestamp, 'UTC');
                            $uaeDateTime = $utcDateTime->setTimezone('Asia/Dubai');

                            $uaeTime                       = $uaeDateTime->toDateTimeString();
                            $dubizzle_product_level_expiry = PropertyMeta::storePropertyMeta($property->id, "dubizzle_level_expires_at", $uaeTime ?? null);
                        }
                    }
                }
                foreach ($data[0]['usedAdProducts'] as $adProduct) {
                    if ($adProduct['portal']['name'] == "bayut" && $bayut_product_level == $adProduct['adProductName']) {
                        if ($adProduct['expiresAt']) {
                            $timestamp = $adProduct['expiresAt'];

                            $utcDateTime = Carbon::createFromFormat('Y-m-d\TH:i:s.u\Z', $timestamp, 'UTC');
                            $uaeDateTime = $utcDateTime->setTimezone('Asia/Dubai');

                            $uaeTime                    = $uaeDateTime->toDateTimeString();
                            $bayut_product_level_expiry = PropertyMeta::storePropertyMeta($property->id, "bayut_level_expires_at", $uaeTime ?? null);
                        }
                    }
                }
                foreach ($data[0]['portals'] as $portal) {
                    if ($portal['name'] == "bayut") {
                        $timestamp = $portal['createdAt'];

                        $utcDateTime = Carbon::createFromFormat('Y-m-d\TH:i:s.u\Z', $timestamp, 'UTC');
                        $uaeDateTime = $utcDateTime->setTimezone('Asia/Dubai');

                        $uaeTime      = $uaeDateTime->toDateTimeString();
                        $bayutCreated = PropertyMeta::storePropertyMeta($property->id, "bayut_created_at", $uaeTime ?? null);
                    } elseif ($portal['name'] == "dubizzle") {
                        $timestamp = $portal['createdAt'];

                        $utcDateTime = Carbon::createFromFormat('Y-m-d\TH:i:s.u\Z', $timestamp, 'UTC');
                        $uaeDateTime = $utcDateTime->setTimezone('Asia/Dubai');

                        $uaeTime             = $uaeDateTime->toDateTimeString();
                        $bayutCreated        = PropertyMeta::storePropertyMeta($property->id, "dubizzle_created_at", $uaeTime ?? null);
                        $dubizzleRefreshedAt = PropertyMeta::storePropertyMeta($property->id, "dubizzle_refreshed_at", $portal['refreshedAt'] ?? null);
                    }
                }
                // Storing Status
                $bayutstatus = PropertyMeta::storePropertyMeta($property->id, "bayut_status", ucfirst($data[0]['status']) ?? null);
                $timestamp   = $data[0]['updatedAt'];

                $utcDateTime = Carbon::createFromFormat('Y-m-d\TH:i:s.u\Z', $timestamp, 'UTC');
                $uaeDateTime = $utcDateTime->setTimezone('Asia/Dubai');

                $uaeTime      = $uaeDateTime->toDateTimeString();
                $bayutupdated = PropertyMeta::storePropertyMeta($property->id, "bayut_updated_at", $uaeTime ?? null);

                $dubizzleUpdatedAt = PropertyMeta::storePropertyMeta($property->id, "dubizzle_updated_at", $uaeTime ?? null);

                $dubizzlestatus = PropertyMeta::storePropertyMeta($property->id, "dubizzle_status", ucfirst($data[0]['status']) ?? null);

                $bayutReviewStatus    = PropertyMeta::storePropertyMeta($property->id, "bayut_review_status", ucfirst($data[0]['reviewStatus']) ?? null);
                $dubizzleReviewStatus = PropertyMeta::storePropertyMeta($property->id, "dubizzle_review_status", ucfirst($data[0]['reviewStatus']) ?? null);

                // Storing Verified
                $verified = PropertyMeta::storePropertyMeta($property->id, "bayut_trucheck_status", ucfirst($data[0]['trucheckStatus']));
                // Storing Verified
                $verified = PropertyMeta::storePropertyMeta($property->id, "dubizzle_trucheck_status", ucfirst($data[0]['trucheckStatus']));
                // Url of the listing
                $bayutUrl = PropertyMeta::storePropertyMeta($property->id, "bayut_url", $data[0]['legacyUrl'] ?? null);
                // Dubizzle Url of the listing
                $dubizzleUrl = PropertyMeta::storePropertyMeta($property->id, "dubizzle_url", $data[0]['adAttributes']['external_url'] ?? null);
            }
            return response()->json(['status' => '1', 'data' => $res ?? []]);
        } catch (\Exception $exception) {
            $result = ['status' => '0', 'msg' => 'Something went wrong!!', 'error' => "External Api Controller " . $exception->getMessage(), 'line' => $exception->getLine()];
            info($result);
            return response()->json($result);
        }
    }

    public function fetchBayutDataAbuDhabi(Request $request)
    {
        return true;
        $validatedData = $request->validate([
            "ref_no" => "required|exists:properties,ref_no",
        ]);
        $portal = Portal::where('name', 'Bayut')->first();
        try {
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL            => 'https://www.bayut.com/api-v7/agents/2228844/listings?page=1&perPage=10&portalTab=active&imageStatus=all&listingKey=' . $request->ref_no . '&load=adProductPricing&portalIds=1&portalState=active',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING       => '',
                CURLOPT_MAXREDIRS      => 10,
                CURLOPT_TIMEOUT        => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST  => 'GET',
                CURLOPT_HTTPHEADER     => [
                    'accept: */*',
                    'accept-language: en',
                    'cookie: kc_access_token=' . $portal->token_abu_dhabi,
                    'priority: u=1, i',
                    'referer: https://www.bayut.com/profolio/listings/manage?page=1&perPage=10&portalTab=active&imageStatus=all&portal=bayut&listingKey=' . $request->ref_no,
                    'sec-ch-ua: "Not/A)Brand";v="8", "Chromium";v="126", "Google Chrome";v="126"',
                    'sec-ch-ua-mobile: ?0',
                    'sec-ch-ua-platform: "macOS"',
                    'sec-fetch-dest: empty',
                    'sec-fetch-mode: cors',
                    'sec-fetch-site: same-origin',
                    'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
                    'x-xsrf-token: eyJpdiI6IlhXZGVra3p1QURPallBUlVWdjVaZUE9PSIsInZhbHVlIjoiK3V5Y0JMQVQrRGNWSkplWHNWMlF6RkZmd05GSWY4UEtobXJDWDJkckZCczRsZDhQM29MNXRORmxJRGhBTVd4Z0ZyVEszYlAza2hYZW1PT1FlZmYrTFE9PSIsIm1hYyI6ImFlMmJmNjUxN2NjMWVkYmIxNzRlOWE2ZDg1ZDI0MjdkOWVkYmUxOWU1Zjk4MTRkMDNmYmUyNzUwOThjMDEzZDIifQ==',
                ],
            ]);

            $response = curl_exec($curl);

            $res = json_decode($response, true);
            curl_close($curl);

            $message = $res['message'] ?? null;
            if ($message == "Unauthenticated.") {
                // $this->getBayutToken('abudhabi@axcapital.ae', 'AD@ax123!', 'token_abu_dhabi');
                info("Bayut Cookies Expired for Abu Dhabi");
                return response()->json(['status' => '0', 'msg' => "token expired"]);
            }
            $data     = $res['data'] ?? [];
            $property = Property::where('ref_no', $request->ref_no)->first();

            if (empty($data[0])) {

                $dataDeleted = PropertyMeta::deletePropertyMetaBatch(
                    $property->id,
                    [
                        "bayut_rank",
                        "dubizzle_rank",
                        "bayut_level",
                        "dubizzle_level",
                        "bayut_status",
                        "dubizzle_trucheck_status",
                        "bayut_trucheck_status",
                    ]
                );
            }
            if (! empty($data[0])) {
                // Storing Rank Value
                // info($data);
                $rank = PropertyMeta::storePropertyMeta($property->id, "bayut_rank", round($data[0]['quality']['listing_rank'] ?? 0));

                // Storing Rank Value
                $dubrank = PropertyMeta::storePropertyMeta($property->id, "dubizzle_rank", round($data[0]['quality']['listing_rank'] ?? 0));

                // Storing Level
                $bayut_product_level = PropertyMeta::storePropertyMeta($property->id, "bayut_level", ucfirst($data[0]['variant']) ?? null);
                // Storing Level
                $dubizzle_product_level = PropertyMeta::storePropertyMeta($property->id, "dubizzle_level", "Standard" ?? null);
                foreach ($data[0]['adProducts'] as $adProduct) {
                    if ($adProduct['portal']['name'] == "dubizzle") {
                        $dubizzle_product_level = PropertyMeta::storePropertyMeta($property->id, "dubizzle_level", ucfirst($adProduct['adProductName']) ?? null);
                        if ($adProduct['expiresAt']) {
                            $timestamp = $adProduct['expiresAt'];

                            $utcDateTime = Carbon::createFromFormat('Y-m-d\TH:i:s.u\Z', $timestamp, 'UTC');
                            $uaeDateTime = $utcDateTime->setTimezone('Asia/Dubai');

                            $uaeTime                       = $uaeDateTime->toDateTimeString();
                            $dubizzle_product_level_expiry = PropertyMeta::storePropertyMeta($property->id, "dubizzle_level_expires_at", $uaeTime ?? null);
                        }
                    }
                }
                foreach ($data[0]['usedAdProducts'] as $adProduct) {
                    if ($adProduct['portal']['name'] == "bayut" && $bayut_product_level == $adProduct['adProductName']) {
                        if ($adProduct['expiresAt']) {
                            $timestamp = $adProduct['expiresAt'];

                            $utcDateTime = Carbon::createFromFormat('Y-m-d\TH:i:s.u\Z', $timestamp, 'UTC');
                            $uaeDateTime = $utcDateTime->setTimezone('Asia/Dubai');

                            $uaeTime                    = $uaeDateTime->toDateTimeString();
                            $bayut_product_level_expiry = PropertyMeta::storePropertyMeta($property->id, "bayut_level_expires_at", $uaeTime ?? null);
                        }
                    }
                }
                foreach ($data[0]['portals'] as $portal) {
                    if ($portal['name'] == "bayut") {
                        $timestamp = $portal['createdAt'];

                        $utcDateTime = Carbon::createFromFormat('Y-m-d\TH:i:s.u\Z', $timestamp, 'UTC');
                        $uaeDateTime = $utcDateTime->setTimezone('Asia/Dubai');

                        $uaeTime      = $uaeDateTime->toDateTimeString();
                        $bayutCreated = PropertyMeta::storePropertyMeta($property->id, "bayut_created_at", $uaeTime ?? null);
                    } elseif ($portal['name'] == "dubizzle") {
                        $timestamp = $portal['createdAt'];

                        $utcDateTime = Carbon::createFromFormat('Y-m-d\TH:i:s.u\Z', $timestamp, 'UTC');
                        $uaeDateTime = $utcDateTime->setTimezone('Asia/Dubai');

                        $uaeTime             = $uaeDateTime->toDateTimeString();
                        $bayutCreated        = PropertyMeta::storePropertyMeta($property->id, "dubizzle_created_at", $uaeTime ?? null);
                        $dubizzleRefreshedAt = PropertyMeta::storePropertyMeta($property->id, "dubizzle_refreshed_at", $portal['refreshedAt'] ?? null);
                    }
                }
                // Storing Status
                // dd($property->id);

                $bayutstatus = PropertyMeta::storePropertyMeta($property->id, "bayut_status", ucfirst($data[0]['status']) ?? null);
                $timestamp   = $data[0]['updatedAt'];

                $utcDateTime = Carbon::createFromFormat('Y-m-d\TH:i:s.u\Z', $timestamp, 'UTC');
                $uaeDateTime = $utcDateTime->setTimezone('Asia/Dubai');

                $uaeTime      = $uaeDateTime->toDateTimeString();
                $bayutupdated = PropertyMeta::storePropertyMeta($property->id, "bayut_updated_at", $uaeTime ?? null);

                $dubizzleUpdatedAt = PropertyMeta::storePropertyMeta($property->id, "dubizzle_updated_at", $uaeTime ?? null);

                $dubizzlestatus = PropertyMeta::storePropertyMeta($property->id, "dubizzle_status", ucfirst($data[0]['status']) ?? null);

                $bayutReviewStatus    = PropertyMeta::storePropertyMeta($property->id, "bayut_review_status", ucfirst($data[0]['reviewStatus']) ?? null);
                $dubizzleReviewStatus = PropertyMeta::storePropertyMeta($property->id, "dubizzle_review_status", ucfirst($data[0]['reviewStatus']) ?? null);

                // Storing Verified
                $verified = PropertyMeta::storePropertyMeta($property->id, "bayut_trucheck_status", ucfirst($data[0]['trucheckStatus']));
                // Storing Verified
                $verified = PropertyMeta::storePropertyMeta($property->id, "dubizzle_trucheck_status", ucfirst($data[0]['trucheckStatus']));
                // Url of the listing
                $bayutUrl = PropertyMeta::storePropertyMeta($property->id, "bayut_url", $data[0]['legacyUrl'] ?? null);
                // Dubizzle Url of the listing
                $dubizzleUrl = PropertyMeta::storePropertyMeta($property->id, "dubizzle_url", $data[0]['adAttributes']['external_url'] ?? null);
            }
            return response()->json(['status' => '1', 'data' => $res ?? []]);
        } catch (\Exception $exception) {
            $result = ['status' => '0', 'msg' => 'Something went wrong!!', 'error' => "External Api Controller " . $exception->getMessage(), 'line' => $exception->getLine()];
            info($result);
            return response()->json($result);
        }
    }

    public function getBayutToken($username, $password, $field)
    {
        return true;
        // info('Getting Bayut Token for ' . $field);
        $cacheKey     = 'bayut_token_requests_count_' . $field;
        $cacheTime    = 3 * 60 * 60; // 3 hours in seconds
        $requestCount = Cache::get($cacheKey, 0);

        // Check if the count has reached 3
        // if ($requestCount >= 3) {
        // info('Request limit reached for ' . $field . '. Please try again after 3 hours.');
        // return null; // Or return an appropriate error response
        // }
        // return null;
        info('Request count for ' . $field . ': ' . $requestCount);
        // Increment the request count and store it in cache with a 3-hour expiration
        Cache::put($cacheKey, $requestCount + 1, $cacheTime);

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => 'https://auth.bayut.com/auth/realms/bayut/protocol/openid-connect/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => 'grant_type=password&client_id=frontend&scope=openid&type=email_password&email=' . $username . '&password=' . $password,
            CURLOPT_HTTPHEADER     => [
                'accept: application/json',
                'accept-language: en',
                'content-type: application/x-www-form-urlencoded',
                'cookie: AUTH_SESSION_ID=ca83a6a5-c33c-46b3-8fb5-3ce31ef41fe0.keycloak-bayut-production-keycloakx-1-36064; AUTH_SESSION_ID_LEGACY=ca83a6a5-c33c-46b3-8fb5-3ce31ef41fe0.keycloak-bayut-production-keycloakx-1-36064; settings=%7B%22area%22%3Anull%2C%22currency%22%3A%22AED%22%2C%22installBanner%22%3Atrue%2C%22searchHitsLayout%22%3A%22LIST%22%7D; userLocation=%7B%22countryCode%22%3A%22AE%22%2C%22countryName%22%3A%22United%20Arab%20Emirates%22%2C%22cityName%22%3Anull%7D; _gid=GA1.2.794532779.1722254614; anonymous_session_id=40250193-4a36-4822-9b3a-86e2ca20f1ef; device_id=lz6y0msdzpsgrcq7m; _gcl_au=1.1.120528754.1722254762; FPID=FPID2.2.fS5UACrZMf4Efp7USLXMR9LXi0eLF997OyUw%2FUNYav0%3D.1712574835; FPLC=c3lDyUvzC3ZRqg2wPXo5Cu3y8hWaoY4hQPh0M2yoLZFfgwGQ58bbnazC%2B6CD5aSv7iaB7NnjFrgMwMERFuFpyucsYjKx8DqcElMwOFp%2FwsCLXFdqsGVMEBNSkkPTLQ%3D%3D; _scid=95f9db64-6321-4447-bc85-a611d9e38d32; _ScCbts=%5B%5D; _cc_id=7b9a438022c17158f1de9e8e0ccf2628; panoramaId_expiry=1722859563761; panoramaId=a2439c48dc25254cc566d5093fd74945a7029fda753469122dcfe727c6a2eab5; panoramaIdType=panoIndiv; _sctr=1%7C1722196800000; engagement_cookie=%7C%20%20%7C; AMP_TOKEN=%24NOT_FOUND; _uetsid=ec4535304da211ef92a6017a2ebcf216; _uetvid=ec456cf04da211efb55547554a1bd9c8; _scid_r=95f9db64-6321-4447-bc85-a611d9e38d32; _ga=GA1.2.418363703.1712574835; referrer=%2Ffor-sale%2Fproperty%2Fuae%2F%3Fsort%3Ddate_desc; original_referrer=https%3A%2F%2Fwww.bayut.com%2Faccount%2Fcallback%3FredirectPath%3D%252Ffor-sale%252Fproperty%252Fdubai%252F%253Fsort%253Ddate_desc%26loginType%3Demail_link%26authFlow%3Dlogin; landing_url=%2Ffor-sale%2Fproperty%2Fdubai%2F%3Fsort%3Ddate_desc; _ga_JMZ5C490RT=GS1.1.1722254761.1.0.1722255006.0.0.0; XSRF-TOKEN=eyJpdiI6IjVZMnpyU1I3SFAyOWhuVzNEN1I0UEE9PSIsInZhbHVlIjoicWw2TE9scFpLcjd3bWdTK2NINTVrZ1FQc1JLeTJlNnZybXVHTGZxOFBlRU9JU0FcL3IzRFRUYXhIMlRwdER6NlFlZ3ZFWmpsQUIrTHRlOW9JdjcrVmR3PT0iLCJtYWMiOiIwZTAxM2ZkZTU4YmYyZWQzZWIyNmE3NmM0MGE4MTEwZGU5ZmQxNWY2ZWY0NmIwYWQ3ZjY2NmE5YTdlOTQwMzExIn0%3D; byt_session=eyJpdiI6ImxITGNoVXlPM3N0WFNaK2hLbWo5Q2c9PSIsInZhbHVlIjoicjBrSGNISDNFRzZKc2NnRnpyMkF4S3BxWlJEaDdUQlhZWm5zUHBrZ1dNMys4UENmMFNvM3pvdW1UXC8xVTFDeUN2am5sb2VnZHNBVDhaQlJXWHRmUmlnPT0iLCJtYWMiOiI1MWM0ODcxY2YxNTY1MTJiY2IyNGM3MDdkZTJiZmFiMmQ2Yjc1MzM2NTNlYTMxOTIxZDAwMGY4Mzc0YmE5MTkwIn0%3D; _gat_UA-201547-17=1; _gat_UA-210991773-1=1; moe_uuid=574a1675-55b7-42c3-9932-bb8e1213df60; _ga_YTDB8TQ5Q9=GS1.1.1722254488.2.1.1722255490.53.0.0',
                'origin: https://www.bayut.com',
                'priority: u=1, i',
                'referer: https://www.bayut.com/',
                'sec-ch-ua: "Not/A)Brand";v="8", "Chromium";v="126", "Google Chrome";v="126"',
                'sec-ch-ua-mobile: ?0',
                'sec-ch-ua-platform: "macOS"',
                'sec-fetch-dest: empty',
                'sec-fetch-mode: cors',
                'sec-fetch-site: same-site',
                'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
            ],
        ]);

        $response = curl_exec($curl);

        curl_close($curl);

        $res = json_decode($response, true);
        // dd($res);
        $portal         = Portal::where('name', 'Bayut')->first();
        $portal->$field = $res['access_token'];
        $portal->save();
        info($res['access_token'] . ' Bayut Token for ' . $field);
        return $res['access_token'];
    }

    public function __getBayutToken()
    {

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL            => 'https://auth.bayut.com/auth/realms/bayut/protocol/openid-connect/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => 'grant_type=password&client_id=frontend&scope=openid&type=email_password&email=l.gapunuan%40axcapital.ae&password=Teacup%402024',
            CURLOPT_HTTPHEADER     => [
                'accept: application/json',
                'accept-language: en',
                'content-type: application/x-www-form-urlencoded',
                'cookie: AUTH_SESSION_ID=ca83a6a5-c33c-46b3-8fb5-3ce31ef41fe0.keycloak-bayut-production-keycloakx-1-36064; AUTH_SESSION_ID_LEGACY=ca83a6a5-c33c-46b3-8fb5-3ce31ef41fe0.keycloak-bayut-production-keycloakx-1-36064; settings=%7B%22area%22%3Anull%2C%22currency%22%3A%22AED%22%2C%22installBanner%22%3Atrue%2C%22searchHitsLayout%22%3A%22LIST%22%7D; userLocation=%7B%22countryCode%22%3A%22AE%22%2C%22countryName%22%3A%22United%20Arab%20Emirates%22%2C%22cityName%22%3Anull%7D; _gid=GA1.2.794532779.1722254614; anonymous_session_id=40250193-4a36-4822-9b3a-86e2ca20f1ef; device_id=lz6y0msdzpsgrcq7m; _gcl_au=1.1.120528754.1722254762; FPID=FPID2.2.fS5UACrZMf4Efp7USLXMR9LXi0eLF997OyUw%2FUNYav0%3D.1712574835; FPLC=c3lDyUvzC3ZRqg2wPXo5Cu3y8hWaoY4hQPh0M2yoLZFfgwGQ58bbnazC%2B6CD5aSv7iaB7NnjFrgMwMERFuFpyucsYjKx8DqcElMwOFp%2FwsCLXFdqsGVMEBNSkkPTLQ%3D%3D; _scid=95f9db64-6321-4447-bc85-a611d9e38d32; _ScCbts=%5B%5D; _cc_id=7b9a438022c17158f1de9e8e0ccf2628; panoramaId_expiry=1722859563761; panoramaId=a2439c48dc25254cc566d5093fd74945a7029fda753469122dcfe727c6a2eab5; panoramaIdType=panoIndiv; _sctr=1%7C1722196800000; engagement_cookie=%7C%20%20%7C; AMP_TOKEN=%24NOT_FOUND; _uetsid=ec4535304da211ef92a6017a2ebcf216; _uetvid=ec456cf04da211efb55547554a1bd9c8; _scid_r=95f9db64-6321-4447-bc85-a611d9e38d32; _ga=GA1.2.418363703.1712574835; referrer=%2Ffor-sale%2Fproperty%2Fuae%2F%3Fsort%3Ddate_desc; original_referrer=https%3A%2F%2Fwww.bayut.com%2Faccount%2Fcallback%3FredirectPath%3D%252Ffor-sale%252Fproperty%252Fdubai%252F%253Fsort%253Ddate_desc%26loginType%3Demail_link%26authFlow%3Dlogin; landing_url=%2Ffor-sale%2Fproperty%2Fdubai%2F%3Fsort%3Ddate_desc; _ga_JMZ5C490RT=GS1.1.1722254761.1.0.1722255006.0.0.0; XSRF-TOKEN=eyJpdiI6IjVZMnpyU1I3SFAyOWhuVzNEN1I0UEE9PSIsInZhbHVlIjoicWw2TE9scFpLcjd3bWdTK2NINTVrZ1FQc1JLeTJlNnZybXVHTGZxOFBlRU9JU0FcL3IzRFRUYXhIMlRwdER6NlFlZ3ZFWmpsQUIrTHRlOW9JdjcrVmR3PT0iLCJtYWMiOiIwZTAxM2ZkZTU4YmYyZWQzZWIyNmE3NmM0MGE4MTEwZGU5ZmQxNWY2ZWY0NmIwYWQ3ZjY2NmE5YTdlOTQwMzExIn0%3D; byt_session=eyJpdiI6ImxITGNoVXlPM3N0WFNaK2hLbWo5Q2c9PSIsInZhbHVlIjoicjBrSGNISDNFRzZKc2NnRnpyMkF4S3BxWlJEaDdUQlhZWm5zUHBrZ1dNMys4UENmMFNvM3pvdW1UXC8xVTFDeUN2am5sb2VnZHNBVDhaQlJXWHRmUmlnPT0iLCJtYWMiOiI1MWM0ODcxY2YxNTY1MTJiY2IyNGM3MDdkZTJiZmFiMmQ2Yjc1MzM2NTNlYTMxOTIxZDAwMGY4Mzc0YmE5MTkwIn0%3D; _gat_UA-201547-17=1; _gat_UA-210991773-1=1; moe_uuid=574a1675-55b7-42c3-9932-bb8e1213df60; _ga_YTDB8TQ5Q9=GS1.1.1722254488.2.1.1722255490.53.0.0',
                'origin: https://www.bayut.com',
                'priority: u=1, i',
                'referer: https://www.bayut.com/',
                'sec-ch-ua: "Not/A)Brand";v="8", "Chromium";v="126", "Google Chrome";v="126"',
                'sec-ch-ua-mobile: ?0',
                'sec-ch-ua-platform: "macOS"',
                'sec-fetch-dest: empty',
                'sec-fetch-mode: cors',
                'sec-fetch-site: same-site',
                'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
            ],
        ]);

        $response = curl_exec($curl);

        curl_close($curl);

        $res = json_decode($response, true);

        $portal        = Portal::where('name', 'Bayut')->first();
        $portal->token = $res['access_token'];
        $portal->save();

        return $res['access_token'];
    }

    public function fetchBayutLeads(Request $request)
    {
        $validatedData = $request->validate([
            "ref_no" => "required|exists:properties,ref_no",
        ]);
        try {

            $header   = [];
            $header[] = 'Content-Type: application/json';
            $header[] = 'Content-Length: 4000';
            $header[] = 'Connection: keep-alive';
            $response = Http::withHeaders([
                'authority'                        => 'www.bayut.com',
                'accept'                           => 'application/json',
                'accept-language'                  => 'en',
                'access-control-allow-credentials' => 'true',
                'app-name'                         => 'phoenix',
                'authorization'                    => 'Bearer 6637280|skMfmXIrCnq7MNptVjqDHex7AxAM3N3g3x5t5Cmj',
                'cookie'                           => '__rtbh.lid=%7B%22eventType%22%3A%22lid%22%2C%22id%22%3A%22W77Wm88s9IfpTXRgkS65%22%7D; _fbp=fb.1.1679983787731.204545561; FPID=FPID2.2.NDV0zoYEtJick4Xag3%2BigZGo%2FeYKmWe3hLqR8EadX6w%3D.1679983787; device_id=lhrpyve9obs9m6vvi; os_name=N%2FA; os_version=N%2FA; browser_name=N%2FA; browser_version=N%2FA; __rtbh.uid=%7B%22eventType%22%3A%22uid%22%2C%22id%22%3A%221091659263.1679983787%22%7D; th_capi_em=c75c59ac4c4474939e0492f50c2b1fbc60c94a79fab597dadfe200e825dae549; __zlcmid=1FulpM4kWzOKRt2; csrftoken=tcMrIAiGUNmrzLKQgPbt52igpRlCn0WvvcjAX2TzlEGWvo9cSG1NjrhSNYsSngBx; _cc_id=e57d4441052a3d18ef7bcbc80e8910ba; _gcl_au=1.1.671814188.1696483379; settings=%7B%22area%22%3A%22SQFT%22%2C%22currency%22%3A%22AED%22%2C%22installBanner%22%3Atrue%2C%22searchHitsLayout%22%3A%22LIST%22%7D; abTests=%7B%22CompactNavBar%22%3A%22original%22%2C%22SearchHistoryDesktop%22%3A%22original%22%2C%22SearchHistoryCompact%22%3A%22original%22%7D; banners=%7B%7D; userLocation=%7B%22countryCode%22%3A%22AE%22%2C%22countryName%22%3A%22United%20Arab%20Emirates%22%2C%22cityName%22%3Anull%7D; userGeoLocation=%7B%22coordinates%22%3Anull%2C%22closestLocation%22%3Anull%2C%22loading%22%3Afalse%2C%22error%22%3Anull%7D; anonymous_session_id=c8f9bcd7-8b73-461e-9422-3ca86f59ecd8; __gads=ID=a946565b1e79a3bb:T=1684328951:RT=1698648227:S=ALNI_MZt_XkQBSdHicw5tNaqRLlOx9kMlg; __gpi=UID=00000c1972f5551b:T=1684328951:RT=1698648227:S=ALNI_MbMMKGOfZxwB-ktZYH2oFuJvyYzbQ; _gid=GA1.2.1863963851.1698648228; _uetsid=ad87446076ef11eea96e7978a574698c; _uetvid=a5b534302aad11eea4617b3816384a05; PHPSESSID=g2h6v9r5l177sf1a1bc02sgc04; XSRF-TOKEN=eyJpdiI6IkxRd1YxeFFXRFhUQnJVOWpzQVJnTGc9PSIsInZhbHVlIjoiNGV5eFdaY0xTeVBVVnNGRVMyVjJRZXZLQ1wvZVErRWZoZmxVQWV4aTAxM2RaZzZBXC9DKzhUM1JvUU4zWDBpOWViemsybGxudldMOE5ha2JQaWtFVWFIdz09IiwibWFjIjoiZjlkNTZhNmU1ZDU4MTkxZjdiMjRkOWNkZjM3ODBmOGMzNjZiZjI3MGZkYjMwYmM1YjJjODY3ZWVkMDBiMmY2MSJ9; byt_session=eyJpdiI6Ik1CamtlSEtFdmR6ZnMzMHNUaXNsZVE9PSIsInZhbHVlIjoic2ozYkZTS1RpUkhsbTByNkdJXC9qZU1yaHVpTkxlbUJaV2ZoTGQxbk5Qck02cmVPVHVIR3FZXC85WFJYNDlHQzFwdVNJNXI5VFFoQnNYaXNyTVMyZm43dz09IiwibWFjIjoiMmU1MWE2ZjQwNGM5ZDYyZWNhMGQwYmZhMWU0MzQyODAwMDNlNDFiYmI1NWFkODA1NTBhMDljYWU3MzljOTM3NCJ9; panoramaId_expiry=1699253027996; panoramaId=6f1c231e53ecfd143665c32dc80dbd9563ce920c04b3c68aa27a39ffd6b3e9b0; panoramaIdType=panoIndiv; _ga_JMZ5C490RT=GS1.1.1698648228.68.0.1698648228.0.0.0; AMP_TOKEN=%24NOT_FOUND; FPLC=JRCQYjSBy72aYNSWVdLJ0LGZ3kcExxS7K6Oytc6Qj6KOK8L8NjZV%2Fo%2FkxF4cbObjoIL7yBe3CDkjFXBUrJQYgdcaW35lFvpViShB5Fbzd38EvehCfB44yM94kJRR7Q%3D%3D; cto_bundle=YPB6Yl82clBrd1JxUjZwY3RRVTFXMERCSHZxSzg1cklveDBKUkFBSGFqOEclMkJHSE4xaGNDNThIazdtejd1cjFIZmhhWXdMaWRqJTJGJTJGcUNMbHBTSzBTNXJFZ2tKc1VpJTJGbWREUjFHdWxRSlVtaDVxa0J1ajU0NGNxMTBJZnNRWW9YcDdpTVpqQ3dkRkdxRHVSRU5zOHZ5RllsSUZkRk9XZWNpbmFjWiUyQklXRVVwV2VMWHlaYm9EdHI2NVBqTkhDcEtiJTJCS21HaWpvS0RIWUd5NFkzdDBRYm84d3o3cXJ4czAwdFVPSUFSUWtqdWNaU0NQZFNpTEszd1ppS0J4dTBTb0FaVjJBU0tu; moe_uuid=49b14d76-0641-40b5-8ebe-bd0b60b3a127; fbm_104487306412437=base_domain=.bayut.com; fbsr_104487306412437=G45BThnzqhmbVESJ_w8UmPkTcdQ-6PzDSFolciI_j-Q.eyJ1c2VyX2lkIjoiMjQwNzIyODUxMjc4Mjk0OCIsImNvZGUiOiJBUUNVVTVleXc1U3k1aGNjQ25KZVA4TDcxcGppOV9leWVQT0hKSk5TREY4VS1udE8tMldMQ3paWGNrOF9UcUpkd0xGREdhTUl5ckRDODBBVWJNeEowbFlLN0VFLUFYay10QlBJalpzZWlxR2Itd2F1bTlJYkF6aGhlRDVFVldiT0VpUjNWc3NDZnlod0t3SkxQTUNzQ0RKbmtLcXp4cFVMeHJScG9xUGhLZEtzd0NzR091RWFDbG5NbFRDSWRpanJ6U1YyNU1ha0VsWUtkYXZBYTdTVnoxdWVWRXVLYmU4Y0ZUOVp6eG9FcmhpdXNrQ3pvbjBIRkN6clVFblJLWmhrRU1sZ0swNDA1eXdod3R5QWYzY25fVFppcDFpeVpuOUhoUXlwZ0NOTERkOUFxZjB2X1JxbldXVDlySUN6QzhMWk9YSTFabWg4bmdoYmtSNEJRNWdxa3A3SSIsIm9hdXRoX3Rva2VuIjoiRUFBQmZCOWpJUVpBVUJPMmo3bUxIVU8weEYwbnF0MERYdzNnM09kMjlLNnVON0lQWDRmUXlSVng3bEJOcnZsTXhNV3dVR2RicFh5ZlFVR3JIMFVTdjY1VlRrZm9LZ09UR1pBTlpCekdUeWlRUFpCM3pOaTNaQWt0SGZ4aG9PVEV4am0yeEU5VnBFWkF3VTlYbGx1UlpBeGh5d09KcmRvOEk0Tml6cXJkc3BKM1pCdVZaQVRxUlFtU3pVNm9QY2RqNHNIYlIzRDF5cjJ6WDM4MVpDMjJRaUQybDBaRCIsImFsZ29yaXRobSI6IkhNQUMtU0hBMjU2IiwiaXNzdWVkX2F0IjoxNjk4NjQ4MjI5fQ; byt_cd=hARaQoV66tWCRywVQmyTW%2FsQKXm6jfDTcYD5ziFyDJrPLR9ZQNF3%2BPys4WOUc5qVh2jSnBj2xG8ZCRSCj3bG17aStI7j70fU6bzsoorEvOJ3542LJ98TzA%3D%3D; session=s%3Aac3ad6dc-0b1e-45d1-99f3-c1512f3db584.7c5Uf%2F8gqodxMQIGIfX%2F4ghcndHQybS5QIRdZnc6ioc; _ga_YTDB8TQ5Q9=GS1.1.1698648227.68.1.1698648247.40.0.0; landing_url=%2F; __Host-next-auth.csrf-token=ca384ac249f82200614f2ed3d9e70912fbbdf4edaa6e2808e8ec7e0cabc55cb2%7Cd48aa8a211cc2ef54d852d782c0d71fdb26e0287df83e4a5da1a207734792104; __Secure-next-auth.callback-url=https%3A%2F%2Fbeta-profolio.vercel.app; __Secure-next-auth.session-token=eyJhbGciOiJkaXIiLCJlbmMiOiJBMjU2R0NNIn0..7IPWo7xCyVG27gmG.Ml_mxxN4zkjEtcsB5zY2vtU0qHapiSEHY0b75gAxVshHef74NcCt-H-nw0oALyu9Gm2J9sXvteLI4T5MNSkQKd2hmqTTmCSUxEJoNRudc1eKEBm52YQLEVb7_8jLcSBlUxjM51i79m5BUu9LRcSdvjBzrxJzvmv35FxRwGTGoUdTF-x6a-aH-F4PaZ9QwXzhte7SdXWmdmY9raaII8UymbShFLh49Y39pMuRIC45LgrVLTrpaQGW2q3VvizSzQw3QB5vojLCsNiSmVcJeWsYntlIbmHalmHT2OIWz1BvAG24qLuzG_9fahM6FoUBfi2afVsQaGlzRJfc7ytiwBBlosRQCGBOJv0l5SiQhQLcqcFBkYKuKREXSTXor5fluIhssHh1b33_OVjf1_xU9Zwo4Q00kGP_vn8dEfuQLpQRhuDi4Xu_k_q1UBxtmRm8uG2kpnCyj1xbMcHplfg5GFwEwWUQ6-swGgP_k870G8FrDRQs3yrjsIeaflr83_x4MHOVDzhStmiidKpTVvs3GTQZPoKA4a75bF-zQuj8Uu3vhWO6K1MQSHkNs3FL2ChRhOWhNveiiooSETgJhWOGsj86oLHWExICVPCna0xp0lufSVUOk3ZuuyxkCWVC2WvEDfm_45iQMSKStQcA3UcWdBKojicu-ubD2R1sIvC3z1z8BlDACx3e3XFfGH8dizYps9G3RsoL2if8BbStSj73CUfiheaNY_xO_pUxmwQvarSmJ4OiWG10HXAxaYUO1SVjpYIIGfWzChCYgA7IdCHJDYLvpqRmn17SMV3ra0WeV7zv3AqRi5Stodl2ZJ9wljBuLSWLEqhOB1S7tyjZoe7SZnBDZMmIFKsAZCXXTv3PqNn2uJTxFwsPVmUUI30kISUU_Pecmvcswl1fhcHQ3nvL-x46lI2dLOqPGmTbct_hqmycsOmOX7EIIUj6wqlMqyRCnrlIA3FNJ-YPei_5jPRDlSyyXQDW8zIxtuFNcpc6sNqIETQoxvr6p2Y_34URCnH6ei8JYThfAg4_Wx36WITrSl4YLA6ALLiqpYJZaqKPcNN3eX6sjmbZfxHocJYxuU2WMq92qSymyMgIYXKfCmK-bj8UWqpfU3VxkiOdrbbHsgSBwDrTguFdndjrYGdCRaZD9be3l1Tnt11cEX7zLMRuEDFtzva5nbgIHnIi_qqUpnhWL4GcKBPH4s07pCPyKhchPz-xjURVwu1XLaL6Eei7vpjcdmXg0b21A4i4wzsUZ89I_gpHRigAXb95OIQcFzocoJx8VYMrTDVzJp933a476LKpsfghXeu6eacVT1IuOxDV7ekf34IvJGmInzE4o1OhYEWrPiV5MY2hAB5yneuPYf7KDg8jfBiJte9Fk4MFNE-BMgzDfUZ3-2lzofHBkJZTd_tj9jEqHSz7AjtVNOgCOAhjvz05XTSp2BNMd0D9jJGjEx3jWUTTh1CkTHsePiG0WOifad0Qt6t_xVt2NPomtwHDhlt7mtqON6YFXEGw3g21EH55FQhol-6zplcpNOTxY-aAFsXX8iqHzHPz5w76rcL7YENCbIfsj0K3oD6c5eO12l3_j3AxUMrsTD0rlYg2lOGoDWFvPy43EYqdRNx8zHNnAt1ihp10-he5LyFOlDlIkEj0ShGkFSuk67EL1OSqBnpUdgH_-oVwBaNQHTRg6AcONTyRmCZecwzhhEwyQIetTvQ0SCOQUq6XvXKW8VBFLDu3cPzsezraf0gIAEwynCBXyVc-rMTtnmiXROshsBtyHyHtLReSxm4XDYHyRVVB22sz9Fl7_vspcLr8nZdYOEYI3P4hwTguZeuD7m6JFG8x_g4EpmJc-ke5okgtgvzZKJEeEAfwN9jzUw9WL27lCACe7CZQoWIAXGkJFumyAGOBCynlYx-DfaYS7eScaxm9r0TZKKT3mSUbBRRzMUyhIB0NtTH4rsFVM1wxvUjlnpAR1beJ3z1IO77KPscuCuXurOFMyhR9eZEVWb7Gq6KorL7q457lk9pX2eiwVxerpQGaal0rIXcX-Z4zAZtx-w0QRgtA3NIQ5ui9l-FLUftrwehHGA8vphndt34ECdNNffGzMGdDYgl_XHTXXezN-RdGk46QBXrHW7npEMUtoKmSczbQWZzXYG-OTbjiQ9Baxt5UYyQtHdIOVk9fr5r_VEfWHrygBGOspJ4nsLTgUMmqLNSX0kT7Gipi0fHMVlnjY7ALGVx92aK1xBhFUw2yUsCRHaeTHcGPi9aqVlhRjzE7hxW-F_VsikHXTGB7FIAQXsH87I__qJQNqhuc9dQqeeqLzDUyPJPux2Izb9YFHpn-41hljBWzbtHP3ksI5Vja23ZSmj2_JaTndpYuzFgZVnBGblyEu7zJcn0V_Au8SxFAnSWBKqYiwD7mbKXHnRmJCTgmDZVAxbKqG9S11VUP_0XR9j2qC5cYjAu5Xs33yxXpsUhpvamGTkXF1T5Ll7MncQ-rZ2QsThP8C2DngpETD6lWdnaXGyoZbJU.N-wOZCZy6uK0A7F_OTubDA; _ga=GA1.2.1091659263.1679983787; _gat_UA-201547-17=1; _ga_KSWMYG1K3Z=GS1.1.1698648280.42.1.1698648562.0.0.0',
                'referer'                          => 'https://www.bayut.com/profolio/listings/manage',
                'sec-ch-ua'                        => '"Google Chrome";v="113", "Chromium";v="113", "Not-A.Brand";v="24"',
                'sec-ch-ua-mobile'                 => '?0',
                'sec-ch-ua-platform'               => '"macOS"',
                'sec-fetch-dest'                   => 'empty',
                'sec-fetch-mode'                   => 'cors',
                'sec-fetch-site'                   => 'same-origin',
                'user-agent'                       => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36',
                'x-vercel-protection-bypass'       => 'rTPSuApMZPMyrst9W5mOZO85wojrWEan',
            ])
                ->get('https://www.bayut.com/api-v7/agents/1927829/listings?page=1&perPage=1&imageStatus=all&status=all&listingKey=' . $request->ref_no);

            $res  = $response->json();
            $data = $res['data'];
            //return($res['data']);
            $property = Property::where('ref_no', $request->ref_no)->first();

            if (empty($data[0])) {

                $dataDeleted = PropertyMeta::deletePropertyMetaBatch(
                    $property->id,
                    [
                        "bayut_rank",
                        "dubizzle_rank",
                        "bayut_level",
                        "dubizzle_level",
                        "bayut_status",
                        "dubizzle_trucheck_status",
                        "bayut_trucheck_status",
                    ]
                );
            }

            if (! empty($data[0])) {
                // Storing Rank Value
                $rank = PropertyMeta::storePropertyMeta($property->id, "bayut_rank", round($data[0]['quality']['listing_rank']));

                // Storing Rank Value
                $dubrank = PropertyMeta::storePropertyMeta($property->id, "dubizzle_rank", round($data[0]['quality']['listing_rank']));

                // Storing Level
                $bayut_product_level = PropertyMeta::storePropertyMeta($property->id, "bayut_level", ucfirst($data[0]['variant']) ?? null);
                // Storing Level
                $dubizzle_product_level = PropertyMeta::storePropertyMeta($property->id, "dubizzle_level", "Standard" ?? null);
                foreach ($data[0]['adProducts'] as $adProduct) {
                    if ($adProduct['portal']['name'] == "dubizzle") {
                        $dubizzle_product_level = PropertyMeta::storePropertyMeta($property->id, "dubizzle_level", ucfirst($adProduct['adProductName']) ?? null);
                    }
                    // elseif ($adProduct['portal']['name'] == "bayut") {
                    //     $bayut_product_level = PropertyMeta::storePropertyMeta($property->id, "bayut_level", ucfirst($adProduct['adProductName']) ?? null);
                    // }
                }
                foreach ($data[0]['portals'] as $portal) {
                    if ($portal['name'] == "bayut") {
                        $timestamp = $portal['createdAt'];

                        $utcDateTime = Carbon::createFromFormat('Y-m-d\TH:i:s.u\Z', $timestamp, 'UTC');
                        $uaeDateTime = $utcDateTime->setTimezone('Asia/Dubai');

                        $uaeTime      = $uaeDateTime->toDateTimeString();
                        $bayutCreated = PropertyMeta::storePropertyMeta($property->id, "bayut_created_at", $uaeTime ?? null);
                    } elseif ($portal['name'] == "dubizzle") {
                        $timestamp = $portal['createdAt'];

                        $utcDateTime = Carbon::createFromFormat('Y-m-d\TH:i:s.u\Z', $timestamp, 'UTC');
                        $uaeDateTime = $utcDateTime->setTimezone('Asia/Dubai');

                        $uaeTime             = $uaeDateTime->toDateTimeString();
                        $bayutCreated        = PropertyMeta::storePropertyMeta($property->id, "dubizzle_created_at", $uaeTime ?? null);
                        $dubizzleRefreshedAt = PropertyMeta::storePropertyMeta($property->id, "dubizzle_refreshed_at", $portal['refreshedAt'] ?? null);
                    }
                }
                // Storing Status
                $bayutstatus = PropertyMeta::storePropertyMeta($property->id, "bayut_status", ucfirst($data[0]['status']) ?? null);
                $timestamp   = $data[0]['updatedAt'];

                $utcDateTime = Carbon::createFromFormat('Y-m-d\TH:i:s.u\Z', $timestamp, 'UTC');
                $uaeDateTime = $utcDateTime->setTimezone('Asia/Dubai');

                $uaeTime      = $uaeDateTime->toDateTimeString();
                $bayutupdated = PropertyMeta::storePropertyMeta($property->id, "bayut_updated_at", $uaeTime ?? null);

                $dubizzleUpdatedAt = PropertyMeta::storePropertyMeta($property->id, "dubizzle_updated_at", $uaeTime ?? null);

                $dubizzlestatus = PropertyMeta::storePropertyMeta($property->id, "dubizzle_status", ucfirst($data[0]['status']) ?? null);

                $bayutReviewStatus    = PropertyMeta::storePropertyMeta($property->id, "bayut_review_status", ucfirst($data[0]['reviewStatus']) ?? null);
                $dubizzleReviewStatus = PropertyMeta::storePropertyMeta($property->id, "dubizzle_review_status", ucfirst($data[0]['reviewStatus']) ?? null);

                // Storing Verified
                $verified = PropertyMeta::storePropertyMeta($property->id, "bayut_trucheck_status", ucfirst($data[0]['trucheckStatus']));
                // Storing Verified
                $verified = PropertyMeta::storePropertyMeta($property->id, "dubizzle_trucheck_status", ucfirst($data[0]['trucheckStatus']));
                // Url of the listing
                $bayutUrl = PropertyMeta::storePropertyMeta($property->id, "bayut_url", $data[0]['legacyUrl'] ?? null);
                // Dubizzle Url of the listing
                $dubizzleUrl = PropertyMeta::storePropertyMeta($property->id, "dubizzle_url", $data[0]['adAttributes']['external_url'] ?? null);
            }
            return $data[0];
        } catch (\Exception $exception) {
            $result = ['status' => '0', 'msg' => 'Something went wrong!!', 'error' => "External Api Controller " . $exception->getMessage()];
            return $result;
        }
    }

    public function fetchPropertyData(Request $request)
    {
        $validatedData = $request->validate([
            "ref_no" => "required",
        ]);

        $property = Property::where('ref_no', $request->ref_no)->first();
        $token    = $this->getPFTokenBasedOnProperty($property);
        if ($token != '' && $token != null) {
            $res = Http::withHeaders([
                'authorization'       => 'Bearer ' . $token,
                'content-type'        => 'application/json',
                'User-Agent: PostmanRuntime/7.28.0',
                'x-mycrm-expand-data' => true,
            ])->get('https://api-v2.mycrm.com/properties?filters[reference]=' . $request->ref_no);
            //return $res;
            $data = $res->json();
            return $data;
        } else {
            return [];
        }
    }
    public function fetchLocationData(Request $request)
    {
        $validatedData = $request->validate([
            "name" => "required",
        ]);

        $refresh_token = Cache::get('pf_refresh_token_mycrm') ?? '';
        $tokens        = (new StatusScreenApiController())->propertyFinderAuth($refresh_token);
        if (isset($tokens['access_token'])) {
            $access_token = $tokens['access_token'];
            $res          = Http::withHeaders([
                'authorization'       => 'Bearer ' . $access_token,
                'content-type'        => 'application/json',
                'User-Agent: PostmanRuntime/7.28.0',
                'x-mycrm-expand-data' => true,
            ])->get('https://api-v2.mycrm.com/locations?filters[string]=' . $request->name);
            //return $res;
            $data = $res->json();
            return $data;
        } else {
            return [];
        }
    }

    public function revertUserProfile(Request $request)
    {

        $data = $request->validate([
            'email' => 'required|email',
            'image' => 'required',
            'thumbnail' => 'nullable'
        ]);

        $user = User::where('email', $data['email'])->first();

        $user->update([
            'image' => $data['image'],
            'thumbnail' => $data['thumbnail']
        ]);

        return response()->json(['status' => 'success', 'message' => 'User profile reverted successfully']);
    }

    public function fetchPfLocationData(Request $request)
    {
        $validatedData = $request->validate([
            "search"             => "required",
            "sub_community"      => "nullable",
            "community_property" => "nullable",
        ]);

        $tokens = $this->getPfAccessToken('secondary');
        if (isset($tokens['token']) && $tokens['token'] != '') {

            $res = Http::withHeaders([
                'authorization'       => 'Bearer ' . $tokens['token'],
                'content-type'        => 'application/json',
                'User-Agent: PostmanRuntime/7.28.0',
                'x-mycrm-expand-data' => true,
            ])->get('https://api-v2.mycrm.com/locations?filters[string]=' . $request->search['term']);
            $data = $res->json();
            //return $data['location'];
            $locations = $data['location'] ?? [];
            if ($request->sub_community) {
                $filteredArray = array_filter($locations, function ($element) {
                    return $element['sub_community'] !== null;
                });

                $sub_communities_names = [];
                foreach ($filteredArray as $item) {
                    $sub_communities_names[] = $item['sub_community'];
                }
                //$namesNotInDatabase = SubCommunity::whereNotIn('name', $sub_communities_names)->pluck('name')->toArray();
                $namesNotInDatabase = array_filter($sub_communities_names, function ($name) {
                    return ! SubCommunity::where('name', $name)->exists();
                });
                //return $namesNotInDatabase;
                $updatedArray = [];
                foreach ($filteredArray as $item) {
                    if (in_array($item['sub_community'], $namesNotInDatabase)) {
                        $updatedArray[] = $item;
                    }
                }
                //return $namesNotInDatabase;
                return $updatedArray;
            }
            if ($request->community_property) {
                $filteredArray = array_filter($locations, function ($element) {
                    return $element['tower'] !== null;
                });

                //return $filteredArray;

                $sub_communities_names = [];
                $filteredArray         = array_filter($filteredArray, function ($item) {
                    $sub_community = $item['sub_community'];
                    $tower         = $item['tower'];

                    $subCom           = SubCommunity::where('name', $sub_community)->first();
                    $existsInDatabase = CommunityProperty::where('name', $tower)
                        ->where('sub_community_id', $subCom ? $subCom->id : null)
                        ->exists();

                    return ! $existsInDatabase;
                });

                return $filteredArray;
            }
            return [];
        } else {
            return [];
        }
    }
    public function fetchPfListingVerifiedData(Request $request)
    {
        $validatedData = $request->validate([
            "search"             => "required",
            "sub_community"      => "nullable",
            "community_property" => "nullable",
        ]);

        $tokens = $this->getPfAccessToken('secondary');
        if (isset($tokens['token']) && $tokens['token'] != '') {

            $res = Http::withHeaders([
                'authorization'       => 'Bearer ' . $tokens['token'],
                'content-type'        => 'application/json',
                'User-Agent: PostmanRuntime/7.28.0',
                'x-mycrm-expand-data' => true,
            ])->get('https://api-v2.mycrm.com/locations?filters[string]=' . $request->search['term']);
            $data = $res->json();
            //return $data['location'];
            $locations = $data['location'] ?? [];
            return $filteredArray;

            return [];
        } else {
            return [];
        }
    }
    public function getCommunityDetails(Request $request)
    {
        $validatedData = $request->validate([
            "community" => "required",
        ]);

        $community = Community::where('name', $request->community)->with('region')->first();

        if ($community) {
            return $community;
        } else {
            return [];
            $refresh_token = Cache::get('pf_refresh_token_mycrm') ?? '';
            $tokens        = (new StatusScreenApiController())->propertyFinderAuth($refresh_token);
            if (isset($tokens['access_token'])) {
                $access_token = $tokens['access_token'];
                $res          = Http::withHeaders([
                    'authorization'       => 'Bearer ' . $access_token,
                    'content-type'        => 'application/json',
                    'User-Agent: PostmanRuntime/7.28.0',
                    'x-mycrm-expand-data' => true,
                ])->get('https://api-v2.mycrm.com/locations?filters[string]=' . $request->search['term']);
                $data = $res->json();
                //return $data['location'];
                $locations = $data['location'] ?? [];
                if ($request->sub_community) {
                    $filteredArray = array_filter($locations, function ($element) {
                        return $element['sub_community'] !== null;
                    });
                    return $filteredArray;
                }
                if ($request->community_property) {
                    $filteredArray = array_filter($locations, function ($element) {
                        return $element['tower'] !== null;
                    });

                    return $filteredArray;
                }
                return $data['location'];
            } else {
                return [];
            }
        }
    }
    public function getSubCommunityDetails(Request $request)
    {
        $validatedData = $request->validate([
            "sub_community" => "required",
        ]);

        $subCommunity = SubCommunity::where('name', $request->sub_community)->with('community')->first();

        if ($subCommunity) {
            return $subCommunity;
        } else {
            //return [];
            $tokens = $this->getPfAccessToken('secondary');
            // $refresh_token = Cache::get('pf_refresh_token_mycrm') ?? '';
            // $tokens = (new StatusScreenApiController())->propertyFinderAuth($refresh_token);
            if (isset($tokens['token']) && $tokens['token'] != '') {
                $res = Http::withHeaders([
                    'authorization'       => 'Bearer ' . $tokens['token'],
                    'User-Agent: PostmanRuntime/7.28.0',
                    'content-type'        => 'application/json',
                    'x-mycrm-expand-data' => true,
                ])->get('https://api-v2.mycrm.com/locations?per_page=50&filters[string]=' . $request->sub_community);
                $data = $res->json();
                //return $data['location'];
                $locations = $data['location'] ?? [];
                if ($request->sub_community) {
                    $filteredArray = array_filter($locations, function ($element) use ($request) {
                        return $element['sub_community'] == $request->sub_community && $element['tower'] == null;
                    });
                    if (! empty($filteredArray)) {
                        $firstMatchingResult        = array_values($filteredArray)[0];
                        $subCommunity               = new SubCommunity();
                        $subCommunity->name         = $firstMatchingResult['sub_community'];
                        $community                  = Community::where('name', $firstMatchingResult['community'])->first();
                        $subCommunity->community_id = $community->id ?? null;
                        $subCommunity->latitude     = $firstMatchingResult['latitude'];
                        $subCommunity->longitude    = $firstMatchingResult['longitude'];
                        $subCommunity->save();

                        $subCommunity->load('community');

                        return $subCommunity;
                    } else {
                        return [];
                    }
                }

                return [];
            } else {
                return [];
            }
        }
    }
    //=======================================================================================================
    //=================================== General Functions =================================================
    //=======================================================================================================

    public function encryption(Request $request)
    {
        return Crypt::encryptString($request->id);
    }

    public function decryption(Request $request)
    {
        return Crypt::decryptString($request->id);
    }

    public function getAjaxLead(Request $request)
    {
        if (env('APP_ENV') == 'production') {
            $id = Crypt::decryptString($request->id);
        } else {
            $id = $request->id;
        }

        $contact = Lead::find($id);
        $contact->load(['criteria', 'criteria.locations.parentable', 'leadAgents', 'leadSubStatus', 'leadStatus'])->first();
        if (! empty($contact)) {
            return response()->json(['status' => '1', 'msg' => 'Lead Found', 'data' => $contact]);
        } else {
            return response()->json(['status' => '0', 'msg' => 'Contact Not Found']);
        }
    }

    public function catchTelelogsWebhook(Request $request)
    {
        info($request->all());
        sleep(1);
        info($request);
    }

    public function mycrmData(Request $request)
    {
        // $validatedData = $request->validate([
        //     "ref_no" => "required|exists:properties,ref_no",
        // ]);

        $portal = Portal::where('name', 'Property Finder')->first();
        $agent  = [];
        try {
            $property = Property::where('ref_no', 'axc-3978474')->first();
            if ($property->agent_id) {
                $agent = User::find($property->agent_id);
            }

            // if ($property->region_id == 2) {
            //     $token = env('PROPERTY_FINDER_ABU_DHABI_TOKEN');
            // } elseif ($property->completion_status == "Off-Plan Primary" && (!$agent?->is_commercial)) {
            //     $token = env('PROPERTY_FINDER_PRIMARY_TOKEN');
            // } elseif ($agent?->is_commercial) {
            //     $token = env('PROPERTY_FINDER_COMMERCIAL_TOKEN');
            // } else {
            //     $token = env('PROPERTY_FINDER_SECONDARY_TOKEN');
            // }
            $token    = $this->getPFTokenBasedOnProperty($property);
            $header   = [];
            $header[] = 'Content-Type: application/json';
            $header[] = 'Content-Length: 4000';
            $header[] = 'Connection: keep-alive';

            $curl = curl_init();
            //dd($token);
            curl_setopt_array($curl, [
                CURLOPT_URL            => 'https://api-v2.mycrm.com/properties?filters[reference]=' . $request->ref_no,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING       => '',
                CURLOPT_MAXREDIRS      => 10,
                CURLOPT_TIMEOUT        => 200,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST  => 'GET',
                CURLOPT_HTTPHEADER     => [
                    // 'Authorization: Bearer eyJhbGciOiJSUzI1NiIsInR5cCIgOiAiSldUIiwia2lkIiA6ICJIYzY0Z2lpMHpxYzUwRXNrMjFPZ3ExTkdQQUVuRmZOWEN1akdsc1BkdnYwIn0',
                    'authorization: Bearer ' . $token,
                    'User-Agent: PostmanRuntime/7.28.0',
                ],
            ]);

            $res = curl_exec($curl);

            curl_close($curl);

            $res = json_decode($res, true);
            return $res;
            return response()->json(['status' => '1', 'msg' => 'Data Fetched Successfully', 'data', $res]);

            if (isset($res['error']) && $res['error'] == "AuthenticationError") {
                info("Property Finder Token Expired. Property Ref # is " . $request->ref_no);
                return response()->json(['status' => '0', 'msg' => "token expired"]);
            }
            $data = $res['properties'] ?? null;
            //info($data);
            if (empty($data[0])) {

                $dataDeleted = PropertyMeta::deletePropertyMetaBatch(
                    $property->id,
                    [
                        "pf_rank",
                        "pf_rank_details",
                        "pf_level",
                        "pf_status",
                        "pf_verified",
                    ]
                );
            }

            if (! empty($data[0])) {
                // Storing Rank Value
                $rank = PropertyMeta::storePropertyMeta($property->id, "pf_rank", $data[0]['quality_score']['value'] ?? null);

                // Storing Rank details
                $rank_details = PropertyMeta::storePropertyMeta($property->id, "pf_rank_details", json_encode($data[0]['quality_score']['details'] ?? '') ?? null);
                // Storing Level
                $product_level = PropertyMeta::storePropertyMeta($property->id, "pf_level", ucfirst($data[0]['listing_level']) ?? null);

                $product_level_expiry = PropertyMeta::storePropertyMeta($property->id, "pf_level_expiry", $data[0]['listing_level_date'] ?? null);

                // Storing Status
                $status      = PropertyMeta::storePropertyMeta($property->id, "pf_status", ucfirst($data[0]['listing_status']) ?? null);
                $timestamp   = $data[0]['created_at'];
                $utcDateTime = Carbon::createFromFormat('Y-m-d\TH:i:sP', $timestamp);
                $uaeDateTime = $utcDateTime->setTimezone('Asia/Dubai');

                $uaeTime = $uaeDateTime->toDateTimeString();
                // Storing Status
                $date = PropertyMeta::storePropertyMeta($property->id, "pf_created_at", $uaeTime ?? null);

                $timestamp = $data[0]['created_at'];

                $utcDateTime = Carbon::createFromFormat('Y-m-d\TH:i:sP', $timestamp);
                $uaeDateTime = $utcDateTime->setTimezone('Asia/Dubai');

                $uaeTime = $uaeDateTime->toDateTimeString();

                $date = PropertyMeta::storePropertyMeta($property->id, "pf_updated_at", $uaeTime ?? null);
                // Storing Verified
                $tag      = $data[0]['quality_score']['details']['verified']['tag'] ?? '';
                $verified = PropertyMeta::storePropertyMeta($property->id, "pf_verified", $tag == "Yes" ? "Verified" : "Not Verified");
                // Url of the listing
                $propertyId = $data[0]['quality_score']['property_id'] ?? null;
                $urlLink    = null;
                if ($propertyId) {
                    $urlLink = "https://www.propertyfinder.ae/to/" . $data[0]["quality_score"]["property_id"] . "/en";
                }

                $url = PropertyMeta::storePropertyMeta($property->id, "pf_url", $urlLink ?? null);
            }
            return response()->json(['status' => '1', 'msg' => 'Data Fetched Successfully', 'data', $data]);
        } catch (\Exception $exception) {
            $result = ['status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage(), 'line' => $exception->getLine(), 'file' => $exception->getFile()];
            info($result);
            return response()->json($result);
        }
    }

    public function getContactsId(Request $request)
    {
        $records = Contact::whereIn('contact_source_id', [129, 131, 133])
            ->whereDoesntHave('contactTypes')
            ->get();
        return $records->pluck('id');
        $done = 0;
        $ids  = [];
        foreach ($records as $record) {
            $done++;
            $ids[] = $record->id;
            $record->contactTypes()->attach(3);
        }
        return response()->json(['status' => '1', 'msg' => 'Data Fetched Successfully', 'data' => $done, 'ids' => $ids]);
    }

    public function getContacts(Request $request)
    {
        $records = Contact::whereIn('contact_source_id', [129, 131, 133])
            ->whereDoesntHave('contactTypes')
            ->get();
        $done = 0;
        $ids  = [];
        foreach ($records as $record) {
            $done++;
            $ids[] = $record->id;
            $record->contactTypes()->attach(3);
        }

        return response()->json(['status' => '1', 'msg' => 'Data Fetched Successfully', 'data' => $done, 'ids' => $ids]);
    }

    public function emptyNoti($token)
    {
        $fcm = UserFcm::where('fcm_token', $token)->first();

        if ($fcm) {

            $data = [
                'title'    => (string) 'User Logged Out',
                'message'  => (string) 'User Logged Out',
                'type'     => "Test",
                'user_id'  => (string) $fcm->user_id, // Convert to string if it's not already
                'flag'     => (string) 'Logout',      // Convert to string if it's not already
                'model'    => (string) 'Logout',      // Convert to string if it's not already
                'model_id' => (string) $fcm->user_id, // Convert to string if it's not already
            ];

            $notification = [
                'title' => 'User Logged Out',
                'body'  => 'User Logged Out',
            ];

            $payload = [
                'message' => [
                    'token'        => $fcm->fcm_token, // FCM registration token
                    'notification' => $notification,   // Notification payload (title and body)
                    'data'         => $data,           // Custom data payload
                ],
            ];

            $payload = [
                'message' => [
                    'token'        => $token,
                    // 'token' => 'dAs7bBXBRkWLdyZlZFaPA7:APA91bEuRKe8TbwmZcJQXzihQG82Q4OdWDa_hnUbFrq9wRfo8nus3UEWmiTkh-rzmevCD7_pupuii6pJXWBNN1L4kcnfSFQLwPtvAKw6nEHlbW869nYWT5Fai5fL8PLu0-ODPMXE_t4q',  // FCM registration token
                    // 'token'=>'cOJsQmdTQuyCvoaWbYJ0rE:APA91bHPLJq71J9AyI74yfnlBR86LE1j0dyzUUZ7GBegwxag9NQBWMKBLK1jebTdbMO5OAjx51rlIbCIRuL6WM2vOO81uhLd4xywKAeq7DUFkVv4T-vFHnPdFjFqE3vJdZC0Bdm0u5xD',
                    // 'token'=>'crGUegySQ4u0wjsdCH9trL:APA91bGJX9sH3KpGrqCMvuoM8cWodyYoqdZ7lNJA8XcF4WJ7F0rLam9J6iAigNx4v7korU31CJMwnSeY-4vhuK09LMDAXXRavPbRhhj5UxLR-nVEuMEy4XmRSLnlmXQHfDTydw2UTtLE',
                    'notification' => $notification, // Notification payload (title and body)
                    'data'         => $data,         // Custom data payload
                ],
            ];

            $function = sendNotificationToMobile($payload, $flag = "Test");
            return $function;
        } else {
            return 'No FCM Found';
        }
    }

    public function propertyFinderWhatsappLeads(Request $request)
    {
        $bearerToken = 'HxdBu.8PCjBsFNkOLvPwuWyysufbxuuFSWzKjPiA';
        // dd($bearerToken);
        $time_start    = microtime(true);
        $page          = 1;
        $importedCount = 0;
        $per_page      = 20;
        $currentDate   = Carbon::now();
        $formattedDate = $currentDate->format('Y-m-d');

        //$formattedDate = "2024-02-04";
        //$bearerToken = "rCyjT.GLTeX4jHhlEsBYKSnnlv25iOMgOy7YtfJC";

        $header   = [];
        $header[] = 'Content-Type: application/json';
        $header[] = 'Content-Length: 4000';
        $header[] = 'Connection: keep-alive';
        $response = Http::withHeaders([
            'Authorization'       => 'Bearer ' . $bearerToken,
            'x-requested-with'    => 'XMLHttpRequest',
            'x-mycrm-expand-data' => 'true',
            'User-Agent: PostmanRuntime/7.28.0',
        ])->get('https://api-v2.mycrm.com/whatsapp-leads?per_page=1500&page=4&filters[date][from]=2024-09-17&filters[date][to]=2024-09-24&sort=date&sort_order=desc&_=1727266817686');

        $res = $response->json();

        //dd($res);
        $leads             = $res['leads'] ?? [];
        $total_leads_count = $res['count'] ?? 0;
        $total_pages       = round(ceil($total_leads_count / $per_page), 0);
        for ($page; $page <= $total_pages; $page++) {
            $response = Http::withHeaders(
                [
                    'Authorization'       => 'Bearer ' . $bearerToken,
                    'x-requested-with'    => 'XMLHttpRequest',
                    'User-Agent: PostmanRuntime/7.28.0',
                    'x-mycrm-expand-data' => 'true',
                ]
            )->get('https://api-v2.mycrm.com/leads?page=' . $page . '&per_page=' . $per_page . '&filters[created][from]=' . $formattedDate);
            $res = $response->json();
            return $res;
            $leads = $res['leads'] ?? [];
            if ($leads) {
                foreach ($leads as $value) {
                    echo "\n#";
                    echo ($value['id']);
                    $lead = Lead::where('mobile', "LIKE", "%" . substr($value['mobile'], -7))->where('my_crm_id', $value['id'])->first();

                    if ($lead) {
                        continue;
                    }

                    $newLead   = false;
                    $source_id = 31;

                    if (! empty($value)) {
                        // Checking Source
                        switch ($value['source']) {
                            case 'propertyfinder':
                                $source_id = 31; // Property Finder Id
                                break;
                            case 'online':
                                $source_id = 45; // Online Id
                                break;
                            case 'other':
                                $source_id = 44; // Other Id
                                break;
                            default:
                                $source_id = 31; // Property Finder Id
                                break;
                        }
                        if ($value['source_channel'] == "ourWebsite") {
                            $medium = Medium::where('name', "%" . 'Website Form' . "%")->first();
                        } else {
                            $medium = Medium::where('name', "LIKE", "%" . 'Email' . "%")->first();
                        }
                        // Checking
                        switch ($value['status']) {
                            case 'notYetContacted':
                                $sub_status = '4'; // Not Yet Contacted
                                break;
                            case 'prospect':
                                $sub_status = '26'; // Prospect
                                break;
                            case 'calledNoReply':
                                $sub_status = '5'; // Called No Reply
                                break;
                        }
                        $for                = null;
                        $from_bed_id        = null;
                        $to_bed_id          = null;
                        $from_bath_id       = null;
                        $to_bath_id         = null;
                        $community          = null;
                        $sub_community      = null;
                        $community_property = null;
                        $from_price         = null;
                        $to_price           = null;
                        $pattern            = '/ref:\s*(\w+-\d+)/';

                        $message   = $value['notes'][0]['body'] ?? "";
                        $property  = [];
                        $type      = [];
                        $typeValue = "";
                        // Perform the regular expression match
                        if (preg_match($pattern, $message, $matches)) {
                            $referenceNumber = $matches[1];
                            $property        = Property::where('ref_no', $referenceNumber)->first();
                            if ($property) {
                                switch ($property->for) {
                                    case 'Residential for sale':
                                        $typeValue = 'Buyer'; // Not Yet Contacted
                                        break;
                                    case 'Residential for rent':
                                        $typeValue = 'Tenant'; // Prospect
                                        break;
                                    case 'Commercial for sale':
                                        $typeValue = 'Buyer'; // Called No Reply
                                        break;
                                    case 'Commercial for rent':
                                        $typeValue = 'Tenant'; // Called No Reply
                                        break;
                                }
                                $type = Type::where('name', $typeValue)->first();
                            }
                        }

                        if ($value['preferences']) {
                            if (isset($value['preferences'][0]['offering_type']) && $value['preferences'][0]['offering_type'] != "") {
                                $for = $value['preferences'][0]['types'][0]['category'] . " for " . $value['preferences'][0]['offering_type'];
                            }
                            if ($value['preferences'][0]['from_bedroom'] == 0) {
                                $from_bed_id = Bed::where('name', "LIKE", "Studio")->firstOrFail();
                            } elseif ($value['preferences'][0]['from_bedroom'] != null) {
                                $from_bed_id = Bed::where('name', "LIKE", $value['preferences'][0]['from_bedroom'] . " bed%")->firstOrFail();
                            }

                            if ($value['preferences'][0]['to_bedroom'] == 0) {
                                $to_bed_id = Bed::where('name', "LIKE", "Studio")->firstOrFail();
                            } elseif ($value['preferences'][0]['to_bedroom'] != null) {
                                $to_bed_id = Bed::where('name', "LIKE", $value['preferences'][0]['to_bedroom'] . " bed%")->firstOrFail();
                            }

                            if ($value['preferences'][0]['from_bathroom'] != 0 && $value['preferences'][0]['from_bathroom'] != null) {
                                $from_bath_id = Bath::where('name', "LIKE", $value['preferences'][0]['from_bathroom'] . " bath%")->firstOrFail();
                            }
                            if ($value['preferences'][0]['to_bathroom'] != 0 && $value['preferences'][0]['from_bathroom'] != null) {
                                $to_bath_id = Bath::where('name', "LIKE", $value['preferences'][0]['to_bathroom'] . " bath%")->firstOrFail();
                            }

                            if (isset($value['preferences'][0]['locations'][0]['community'])) {
                                $community = Community::where('name', "LIKE", $value['preferences'][0]['locations'][0]['community'])->first();
                            }
                            if (isset($value['preferences'][0]['locations'][0]['sub_community'])) {
                                $sub_community = SubCommunity::where('name', "LIKE", $value['preferences'][0]['locations'][0]['sub_community'])->first();
                            } else {
                                if ($property->sub_community_id ?? null) {
                                    $sub_community = SubCommunity::find($property?->sub_community_id);
                                }
                            }
                            if (isset($value['preferences'][0]['locations'][0]['tower'])) {
                                $community_property = CommunityProperty::where('name', "LIKE", $value['preferences'][0]['locations'][0]['tower'])->first();
                            } else {
                                if ($property->community_property_id ?? null) {
                                    $community_property = CommunityProperty::find($property?->community_property_id);
                                }
                            }
                            $from_price = $value['preferences'][0]['from_price'];
                            $to_price   = $value['preferences'][0]['to_price'];
                        }
                        $user = null;
                        if (isset($value['user']['public']['email'])) {
                            if ($value['user']['public']['email'] == "abudhabi@axcapital.ae") {
                                $user = User::where('email', "j.kinana@axcapital.ae")->first();
                            } else {
                                $user = User::where('email', $value['user']['public']['email'])->first();
                                if (! $user) {
                                    $users = Http::withHeaders([
                                        'Authorization' => 'Bearer ' . $bearerToken,
                                        'User-Agent: PostmanRuntime/7.28.0',
                                    ])->get('https://api-v2.mycrm.com/users/' . $value['user']['id']);
                                    $userRes = $users->json();
                                    //dd($userRes);
                                    $userData = $userRes['user'] ?? [];

                                    if ($userData) {
                                        if (isset($userData['private']['email'])) {
                                            if ($userData['private']['email']) {
                                                $user = User::where('email', $userData['private']['email'])->first();
                                                if ($user) {
                                                    $user->old_user_id = $value['user']['id'];
                                                    $user->save();
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        if (! $lead) {
                            $lead = new Lead();

                            $lead->my_crm_id      = $value['id'];
                            $lead->agent_id       = $user?->id;
                            $lead->first_name     = $value['first_name'];
                            $lead->last_name      = $value['last_name'];
                            $lead->email          = $value['email'];
                            $lead->type_id        = $type->id ?? null;
                            $lead->phone          = $value['phone'];
                            $lead->for            = $for ? [ucfirst($for)] : null;
                            $lead->mobile         = $value['mobile'];
                            $lead->whatsapp       = $value['mobile'];
                            $lead->address        = $value['address'];
                            $lead->message        = $message ?? "";
                            $lead->lead_status_id = 1; // New
                            $lead->property_id    = $property->id ?? null;
                            // $lead->lead_sub_status_id = $sub_status ?? null;
                            $lead->lead_medium_id = $medium?->id ?? null;
                            $lead->source_id      = $source_id;

                            $lead->save();
                            $lead->refresh();
                            $importedCount++;
                            event(new \App\Events\LeadCreated($lead));
                            activity()
                                ->performedOn($lead)
                                ->event('Created')
                                ->log('Created New Lead From Parser Api');
                        }

                        $leadCriteria = LeadCriteria::where('lead_id', $lead->id)->first() ?? new LeadCriteria();

                        $leadCriteria->lead_id     = $lead->id;
                        $leadCriteria->min_bed_id  = $from_bed_id?->id;
                        $leadCriteria->max_bed_id  = $to_bed_id?->id;
                        $leadCriteria->min_bath_id = $from_bath_id?->id;
                        $leadCriteria->max_bath_id = $to_bath_id?->id;
                        $leadCriteria->min_price   = $from_price ?? null;
                        $leadCriteria->max_price   = $to_price ?? null;

                        $leadCriteria->save();

                        $leadNote          = new LeadNote();
                        $leadNote->lead_id = $lead->id;
                        $leadNote->note    = 'Imported from Expert Property Finder Api
                        <br> Message: ' . $message;
                        $leadNote->save();

                        LeadCriteriaLocation::where('lead_criteria_id', $leadCriteria->id)->delete(); // deleting old locations to prevent duplication
                        if ($community) {
                            $communityLocation = LeadCriteriaLocation::where('parentable_type', Community::class)->where('parentable_id', $community->id)->where('lead_criteria_id', $leadCriteria->id)->exists();
                            if (! $communityLocation) {
                                $obj                   = new LeadCriteriaLocation();
                                $obj->parentable_type  = Community::class;
                                $obj->parentable_id    = $community?->id;
                                $obj->lead_criteria_id = $leadCriteria->id;
                                $obj->save();
                            }
                        }
                        if ($sub_community) {
                            $communityLocation = LeadCriteriaLocation::where('parentable_type', SubCommunity::class)->where('parentable_id', $sub_community?->id)->where('lead_criteria_id', $leadCriteria->id)->exists();
                            if (! $communityLocation) {
                                $obj                   = new LeadCriteriaLocation();
                                $obj->parentable_type  = SubCommunity::class;
                                $obj->parentable_id    = $sub_community?->id;
                                $obj->lead_criteria_id = $leadCriteria->id;
                                $obj->save();
                            }
                        }
                        if ($community_property) {
                            $communityLocation = LeadCriteriaLocation::where('parentable_type', CommunityProperty::class)->where('parentable_id', $community_property?->id)->where('lead_criteria_id', $leadCriteria->id)->exists();
                            if (! $communityLocation) {
                                $obj                   = new LeadCriteriaLocation();
                                $obj->parentable_type  = CommunityProperty::class;
                                $obj->parentable_id    = $community_property?->id;
                                $obj->lead_criteria_id = $leadCriteria->id;
                                $obj->save();
                            }
                        }
                    }
                }
            }
        }
        $time_end = microtime(true);
        $time     = $time_end - $time_start;
        echo ("\n $importedCount leads are imported successfully");
        return $importedCount;
    }

    public function _whatsappPFLeads()
    {

        $counter = 1;
        // run foreach for 1-4
        for ($counter; $counter <= 4; $counter++) {

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL            => 'https://api-v2.mycrm.com/whatsapp-leads?per_page=100&page=1&filters%5C[date%5C]%5C[from%5C]=2024-09-17&filters%5C[date%5C]%5C[to%5C]=2024-09-24&sort=date&sort_order=desc&_=1727266817687',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING       => '',
                CURLOPT_MAXREDIRS      => 10,
                CURLOPT_TIMEOUT        => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST  => 'GET',
                CURLOPT_HTTPHEADER     => [
                    'accept: application/json, text/javascript, */*; q=0.01',
                    'accept-language: en',
                    'authorization: Bearer eyJhbGciOiJSUzI1NiIsInR5cCIgOiAiSldUIiwia2lkIiA6ICJIYzY0Z2lpMHpxYzUwRXNrMjFPZ3ExTkdQQUVuRmZOWEN1akdsc1BkdnYwIn0.eyJleHAiOjE3MjcyNzM1NTgsImlhdCI6MTcyNzI2NjM1OCwiYXV0aF90aW1lIjoxNzI3MjY2MzU1LCJqdGkiOiI4Y2M1NzhkNS0xMTRhLTRkOWMtYmMyNC1iMGI3ODQzZWI2NjciLCJpc3MiOiJodHRwczovL2F1dGgucHJvcGVydHlmaW5kZXIuY29tL2Jyb2tlci9yZWFsbXMvcHJvcGVydHlmaW5kZXIiLCJhdWQiOiJwZmV4cGVydCIsInN1YiI6IjE2MzcwNCIsInR5cCI6IkJlYXJlciIsImF6cCI6InBmZXhwZXJ0Iiwibm9uY2UiOiJRWDRqRlpHOFg4a2d3NkxSMlYzYko1NVFGWlhwOW5vbEg4eUYzV2gyIiwic2Vzc2lvbl9zdGF0ZSI6ImFjMjRiZWM1LTZhZjAtNGY1My05MjJmLWI2ZTc0ODdlMjllNSIsInJlYWxtX2FjY2VzcyI6eyJyb2xlcyI6WyJ2Mi5saXN0aW5nLmNyZWF0ZSIsInYyLm5ld19wcm9qZWN0cy5tYW5hZ2UiLCJ2Mi50cmFuc2FjdGlvbnMubWFuYWdlIiwicHJvZmlsZS5yZWFkIiwidjIubGlzdGluZy5wcmV2aWV3IiwicHJvcGVydHkucmVhZCIsInByb3BlcnR5LnB1Ymxpc2giLCJ2Mi5jcmVkaXRfdXNhZ2VfaGlzdG9yeS52aWV3IiwicHJvcGVydHkudXBkYXRlIiwicHJvcGVydHkuZGVsZXRlIiwidjIubGlzdGluZy52aWV3IiwibGFuZGxvcmQucmVhZC5vdGhlci5hdHRyaWJ1dGVzIiwidjIub3duZXJzLnZpZXciLCJ2Mi5jb21wYW55X3dhdGVybWFyay5tYW5hZ2UiLCJsZWFkLmRlbGV0ZS5vdGhlciIsInYyLnVzZXJfcHJvZmlsZS51cGRhdGUiLCJsYW5kbG9yZC5yZWFkIiwicHJvZmlsZS51cGRhdGUiLCJ2Mi5saXN0aW5nLnVucHVibGlzaCIsInYyLnVzZXJfbm90aWZpY2F0aW9uX3ByZWZlcmVuY2UudXBkYXRlIiwidjIucGZfYWNhZGVteS52aWV3IiwidW1hX2F1dGhvcml6YXRpb24iLCJ2Mi5vd25lcnMubWFuYWdlIiwibGFuZGxvcmQudXBkYXRlLm90aGVyIiwidjIubGlzdGluZy5hdXRvX3JlbmV3IiwidjIudXNlcnNfbGlzdC52aWV3IiwidjIudmF0X2RvY3VtZW50LnZpZXciLCJ2Mi5jbGllbnRfcHJvZmlsZS52aWV3IiwidjIuY3JlZGl0X3VzYWdlX2hpc3RvcnkuZXhwb3J0IiwicHJvcGVydHkudXBkYXRlLm90aGVyIiwidjIudXNlcl92ZXJpZmljYXRpb24ubWFuYWdlIiwibGVhZC5yZWFkLm90aGVyLmF0dHJpYnV0ZXMiLCJ2Mi5saXN0aW5nLmNsYWltX3RyYW5zYWN0aW9uIiwidjIuYWdlbnRfaW5zaWdodHMudmlldyIsInYyLmxpc3RpbmcudXBncmFkZV90b19mZWF0dXJlZCIsInYyLmNvbnRyYWN0c19pbnZvaWNlcy52aWV3IiwibGFuZGxvcmQuZGVsZXRlIiwiY2xpZW50LnJlYWQiLCJsZWFkLnVwZGF0ZSIsInYyLmxpc3RpbmcuYXJjaGl2ZSIsInYyLnBlcm1pc3Npb25zLnZpZXciLCJ2Mi53aGF0c2FwcF9sZWFkcy52aWV3IiwicHJvcGVydHkuZGVsZXRlLm90aGVyIiwicHJvcGVydHkuYXBwcm92ZS5vdGhlciIsInYyLnVzZXJfcHJvZmlsZS52aWV3IiwidjIubGlzdGluZy51cGdyYWRlX3RvX3ByZW1pdW0iLCJsZWFkLnJlYWQiLCJ2Mi5jdXN0b21fcm9sZXMubWFuYWdlIiwidjIubGVhZHNfaW5zaWdodHMudmlldyIsInYyLnN0YXR1cy52aWV3IiwidjIucmVndWxhcl9sZWFkcy52aWV3IiwidjIudXNlci5jcmVhdGUiLCJ2Mi5saXN0aW5nLmFzc2lnbiIsInYyLmxpc3RpbmdzX2FjdGlvbl90cmFja2VyLm1hbmFnZSIsInYyLmxpc3RpbmcudXBkYXRlX2RldGFpbHMiLCJ2Mi5wb3J0YWxzLm1hbmFnZSIsInVzZXIucmVhZCIsInByb3BlcnR5LnB1Ymxpc2gub3RoZXIiLCJvZmZsaW5lX2FjY2VzcyIsInYyLmNyZWRpdHNfdG9fdGVhbXMubWFuYWdlIiwidjIuYWN0aXZpdHlfbG9nLnZpZXciLCJ2Mi51c2VyX2RvY3VtZW50cy5tYW5hZ2UiLCJ2Mi50cmFuc2FjdGlvbnMudmlldyIsInYyLmxpc3RpbmcuZGVsZXRlIiwidjIuc2luZ2xlX2FnZW50X2luc2lnaHRzLnZpZXciLCJ2Mi5yZWd1bGFyX2xlYWRzLm1hbmFnZSIsImxlYWQuZGVsZXRlIiwidXNlci51cGRhdGUiLCJ2Mi5jcmVkaXRzX3RvX2FnZW50cy5tYW5hZ2UiLCJ2Mi5jcmVkaXRfcmV0dXJuLmNyZWF0ZSIsInYyLmNyZWRpdF90b3B1cC5jcmVhdGUiLCJ2Mi5jcmVkaXRfY2FsY3VsYXRvci52aWV3IiwidjIubGlzdGluZy5wdWJsaXNoIiwidjIuY3RzX2xhbmRpbmdfcGFnZS52aWV3IiwidjIuaG9saWRheV9tb2RlLm1hbmFnZSIsInYyLmxpc3RpbmdzX2FjdGlvbl90cmFja2VyLnZpZXciLCJsZWFkLnVwZGF0ZS5vdGhlciIsInYyLmxpc3Rpbmcuc3BvdGxpZ2h0IiwibGVhZC5yZWFkLm90aGVyIiwidjIudXNlci5kZWxldGUiLCJ2Mi5saXN0aW5nX3BlcmZvcm1hbmNlX2Rhc2hib2FyZC52aWV3IiwiZGVmYXVsdC1yb2xlcy1wZmV4cGVydCIsImxhbmRsb3JkLnJlYWQub3RoZXIiLCJwcm9wZXJ0eS5yZWFkLm90aGVyIiwidjIubGlzdGluZy52aWV3X29wdGlvbnMiLCJwcm9wZXJ0eS5yZWFkLm90aGVyLmF0dHJpYnV0ZXMiLCJwcm9wZXJ0eS5hcHByb3ZlIiwidjIubGlzdGluZy5hcHByb3ZlX3JlamVjdCIsImxhbmRsb3JkLmRlbGV0ZS5vdGhlciIsImxhbmRsb3JkLnVwZGF0ZSIsInYyLmNhbGxfbGVhZHMudmlldyIsImNsaWVudC51cGRhdGUiLCJ2Mi5saXN0aW5nLmRvd25ncmFkZSJdfSwic2NvcGUiOiJvcGVuaWQgZGVmYXVsdCIsInNpZCI6ImFjMjRiZWM1LTZhZjAtNGY1My05MjJmLWI2ZTc0ODdlMjllNSIsImNvdW50cnkiOiJBRSIsInJvbGUiOiJhZG1pbiIsInBmX2Jyb2tlcl9pZCI6IjM0MjciLCJwZl9pbXBvcnRlZF9pZCI6IkFFLTEwNjE4NyIsImNsaWVudF9pZCI6IjIyMjYiLCJiYXNlX3JvbGVfa2V5IjoiYWRtaW4iLCJzZl9hY2NvdW50X251bWJlciI6IjExMTcwOCIsInVzZXJfaWQiOiIxNjM3MDQiLCJwZl9hZ2VudF9pZCI6IkFFLTI1MjMxNyIsImlidCI6dHJ1ZSwicGZfY2xpZW50X2lkIjoiMzMwMSIsImFwcGxpY2F0aW9uSWQiOiIxYWFjNDE1NC1jYzEyLTQzZTEtYTgzZC1jOTk4ZTJhYjBlMDUiLCJhY2Nlc3NfY29udHJvbF9yb2xlX2lkIjoiNCIsImVtYWlsIjoicy5taXJ6YUBheGNhcGl0YWwuYWUifQ.kIx2jO9E7p4SK8zVg4HGjdkFcw2h8MhPFHh6QL5dnkD39qN9lEnbpU9IEkg4trnjv4DfmXjrjYkpPLHk6zHV5i_rp6sVWIiAcixNBckJKzVKFyAAPwNmCGBGXsYBOZS2rvVu_bT3AKgxtFVqyZ64kh_UI8C4sXlqgVbxAFzmT1F0-9Q5epWfuSdt9o5nm0JBHgNYJJ_9FtjUaQ42RYG08MpjAqQQz3XLu2IhDPdUQHNzSbO3ipqwnTpllbYzt-czQloLvmz8Rg57Ts9lDHP4eGQn8ONdnfztECPzNKJPwcG1hbM0_MPqkLAL-e3SrdL5hW4jUrx4ojNz9dWTGTjiPQ',
                    'content-type: application/json',
                    'origin: https://expert.propertyfinder.ae',
                    'priority: u=1, i',
                    'sec-ch-ua: "Chromium";v="128", "Not;A=Brand";v="24", "Google Chrome";v="128"',
                    'sec-ch-ua-mobile: ?0',
                    'sec-ch-ua-platform: "macOS"',
                    'sec-fetch-dest: empty',
                    'sec-fetch-mode: cors',
                    'sec-fetch-site: cross-site',
                    'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36',
                    'x-mycrm-expand-data: true',
                    'x-requested-with: XMLHttpRequest',
                ],
            ]);

            $response = curl_exec($curl);
            curl_close($curl);

            $res = json_decode($response, true);
        }
    }

    public function whatsappPFLeads()
    {
        $leads_not_clicked = Lead::where('source_id', 31)->where('lead_medium_id', 4)
            ->whereBetween('created_at', ['2023-09-17 00:00:00', '2023-09-25 00:00:00'])
            ->whereHas('shortlink', function ($query) {
                $query->whereNull('opted_in');
            })
            ->count();

        return Excel::download(new LeadsNotClickedExport, 'leads_not_clicked.xlsx');

        $leads = Lead::where('source_id', 31)->where('lead_medium_id', 4)
            ->whereBetween('created_at', ['2023-09-17 00:00:00', '2023-09-25 00:00:00'])
            ->count();

        dd('Total Leads: ' . $leads, 'Total Leads Not Clicked: ' . $leads_not_clicked);

        $leadsData = [];
        $curl      = curl_init();

        for ($i = 1; $i <= 15; $i++) {
            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL            => 'https://api-v2.mycrm.com/whatsapp-leads?per_page=1&page=100&filters%5C[date%5C]%5C[from%5C]=2024-09-17&filters%5C[date%5C]%5C[to%5C]=2024-09-24&sort=date&sort_order=desc&_=1727276132077',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING       => '',
                CURLOPT_MAXREDIRS      => 10,
                CURLOPT_TIMEOUT        => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST  => 'GET',
                CURLOPT_HTTPHEADER     => [
                    'accept: application/json, text/javascript, */*; q=0.01',
                    'accept-language: en',
                    'authorization: Bearer eyJhbGciOiJSUzI1NiIsInR5cCIgOiAiSldUIiwia2lkIiA6ICJIYzY0Z2lpMHpxYzUwRXNrMjFPZ3ExTkdQQUVuRmZOWEN1akdsc1BkdnYwIn0.eyJleHAiOjE3MjcyODMzMjksImlhdCI6MTcyNzI3NjEyOSwiYXV0aF90aW1lIjoxNzI3MjY2MzU1LCJqdGkiOiI2NGRhY2VkMS1iODNjLTQ4OWMtODI3Mi1iMjFkYjNhNjc5YmYiLCJpc3MiOiJodHRwczovL2F1dGgucHJvcGVydHlmaW5kZXIuY29tL2Jyb2tlci9yZWFsbXMvcHJvcGVydHlmaW5kZXIiLCJhdWQiOiJwZmV4cGVydCIsInN1YiI6IjE2MzcwNCIsInR5cCI6IkJlYXJlciIsImF6cCI6InBmZXhwZXJ0Iiwibm9uY2UiOiJRWDRqRlpHOFg4a2d3NkxSMlYzYko1NVFGWlhwOW5vbEg4eUYzV2gyIiwic2Vzc2lvbl9zdGF0ZSI6ImFjMjRiZWM1LTZhZjAtNGY1My05MjJmLWI2ZTc0ODdlMjllNSIsInJlYWxtX2FjY2VzcyI6eyJyb2xlcyI6WyJ2Mi5saXN0aW5nLmNyZWF0ZSIsInYyLm5ld19wcm9qZWN0cy5tYW5hZ2UiLCJ2Mi50cmFuc2FjdGlvbnMubWFuYWdlIiwicHJvZmlsZS5yZWFkIiwidjIubGlzdGluZy5wcmV2aWV3IiwicHJvcGVydHkucmVhZCIsInByb3BlcnR5LnB1Ymxpc2giLCJ2Mi5jcmVkaXRfdXNhZ2VfaGlzdG9yeS52aWV3IiwicHJvcGVydHkudXBkYXRlIiwicHJvcGVydHkuZGVsZXRlIiwidjIubGlzdGluZy52aWV3IiwibGFuZGxvcmQucmVhZC5vdGhlci5hdHRyaWJ1dGVzIiwidjIub3duZXJzLnZpZXciLCJ2Mi5jb21wYW55X3dhdGVybWFyay5tYW5hZ2UiLCJsZWFkLmRlbGV0ZS5vdGhlciIsInYyLnVzZXJfcHJvZmlsZS51cGRhdGUiLCJsYW5kbG9yZC5yZWFkIiwicHJvZmlsZS51cGRhdGUiLCJ2Mi5saXN0aW5nLnVucHVibGlzaCIsInYyLnVzZXJfbm90aWZpY2F0aW9uX3ByZWZlcmVuY2UudXBkYXRlIiwidjIucGZfYWNhZGVteS52aWV3IiwidW1hX2F1dGhvcml6YXRpb24iLCJ2Mi5vd25lcnMubWFuYWdlIiwibGFuZGxvcmQudXBkYXRlLm90aGVyIiwidjIubGlzdGluZy5hdXRvX3JlbmV3IiwidjIudXNlcnNfbGlzdC52aWV3IiwidjIudmF0X2RvY3VtZW50LnZpZXciLCJ2Mi5jbGllbnRfcHJvZmlsZS52aWV3IiwidjIuY3JlZGl0X3VzYWdlX2hpc3RvcnkuZXhwb3J0IiwicHJvcGVydHkudXBkYXRlLm90aGVyIiwidjIudXNlcl92ZXJpZmljYXRpb24ubWFuYWdlIiwibGVhZC5yZWFkLm90aGVyLmF0dHJpYnV0ZXMiLCJ2Mi5saXN0aW5nLmNsYWltX3RyYW5zYWN0aW9uIiwidjIuYWdlbnRfaW5zaWdodHMudmlldyIsInYyLmxpc3RpbmcudXBncmFkZV90b19mZWF0dXJlZCIsInYyLmNvbnRyYWN0c19pbnZvaWNlcy52aWV3IiwibGFuZGxvcmQuZGVsZXRlIiwiY2xpZW50LnJlYWQiLCJsZWFkLnVwZGF0ZSIsInYyLmxpc3RpbmcuYXJjaGl2ZSIsInYyLnBlcm1pc3Npb25zLnZpZXciLCJ2Mi53aGF0c2FwcF9sZWFkcy52aWV3IiwicHJvcGVydHkuZGVsZXRlLm90aGVyIiwicHJvcGVydHkuYXBwcm92ZS5vdGhlciIsInYyLnVzZXJfcHJvZmlsZS52aWV3IiwidjIubGlzdGluZy51cGdyYWRlX3RvX3ByZW1pdW0iLCJsZWFkLnJlYWQiLCJ2Mi5jdXN0b21fcm9sZXMubWFuYWdlIiwidjIubGVhZHNfaW5zaWdodHMudmlldyIsInYyLnN0YXR1cy52aWV3IiwidjIucmVndWxhcl9sZWFkcy52aWV3IiwidjIudXNlci5jcmVhdGUiLCJ2Mi5saXN0aW5nLmFzc2lnbiIsInYyLmxpc3RpbmdzX2FjdGlvbl90cmFja2VyLm1hbmFnZSIsInYyLmxpc3RpbmcudXBkYXRlX2RldGFpbHMiLCJ2Mi5wb3J0YWxzLm1hbmFnZSIsInVzZXIucmVhZCIsInByb3BlcnR5LnB1Ymxpc2gub3RoZXIiLCJvZmZsaW5lX2FjY2VzcyIsInYyLmNyZWRpdHNfdG9fdGVhbXMubWFuYWdlIiwidjIuYWN0aXZpdHlfbG9nLnZpZXciLCJ2Mi51c2VyX2RvY3VtZW50cy5tYW5hZ2UiLCJ2Mi50cmFuc2FjdGlvbnMudmlldyIsInYyLmxpc3RpbmcuZGVsZXRlIiwidjIuc2luZ2xlX2FnZW50X2luc2lnaHRzLnZpZXciLCJ2Mi5yZWd1bGFyX2xlYWRzLm1hbmFnZSIsImxlYWQuZGVsZXRlIiwidXNlci51cGRhdGUiLCJ2Mi5jcmVkaXRzX3RvX2FnZW50cy5tYW5hZ2UiLCJ2Mi5jcmVkaXRfcmV0dXJuLmNyZWF0ZSIsInYyLmNyZWRpdF90b3B1cC5jcmVhdGUiLCJ2Mi5jcmVkaXRfY2FsY3VsYXRvci52aWV3IiwidjIubGlzdGluZy5wdWJsaXNoIiwidjIuY3RzX2xhbmRpbmdfcGFnZS52aWV3IiwidjIuaG9saWRheV9tb2RlLm1hbmFnZSIsInYyLmxpc3RpbmdzX2FjdGlvbl90cmFja2VyLnZpZXciLCJsZWFkLnVwZGF0ZS5vdGhlciIsInYyLmxpc3Rpbmcuc3BvdGxpZ2h0IiwibGVhZC5yZWFkLm90aGVyIiwidjIudXNlci5kZWxldGUiLCJ2Mi5saXN0aW5nX3BlcmZvcm1hbmNlX2Rhc2hib2FyZC52aWV3IiwiZGVmYXVsdC1yb2xlcy1wZmV4cGVydCIsImxhbmRsb3JkLnJlYWQub3RoZXIiLCJwcm9wZXJ0eS5yZWFkLm90aGVyIiwidjIubGlzdGluZy52aWV3X29wdGlvbnMiLCJwcm9wZXJ0eS5yZWFkLm90aGVyLmF0dHJpYnV0ZXMiLCJwcm9wZXJ0eS5hcHByb3ZlIiwidjIubGlzdGluZy5hcHByb3ZlX3JlamVjdCIsImxhbmRsb3JkLmRlbGV0ZS5vdGhlciIsImxhbmRsb3JkLnVwZGF0ZSIsInYyLmNhbGxfbGVhZHMudmlldyIsImNsaWVudC51cGRhdGUiLCJ2Mi5saXN0aW5nLmRvd25ncmFkZSJdfSwic2NvcGUiOiJvcGVuaWQgZGVmYXVsdCIsInNpZCI6ImFjMjRiZWM1LTZhZjAtNGY1My05MjJmLWI2ZTc0ODdlMjllNSIsImNvdW50cnkiOiJBRSIsInJvbGUiOiJhZG1pbiIsInBmX2Jyb2tlcl9pZCI6IjM0MjciLCJwZl9pbXBvcnRlZF9pZCI6IkFFLTEwNjE4NyIsImNsaWVudF9pZCI6IjIyMjYiLCJiYXNlX3JvbGVfa2V5IjoiYWRtaW4iLCJzZl9hY2NvdW50X251bWJlciI6IjExMTcwOCIsInVzZXJfaWQiOiIxNjM3MDQiLCJwZl9hZ2VudF9pZCI6IkFFLTI1MjMxNyIsImlidCI6dHJ1ZSwicGZfY2xpZW50X2lkIjoiMzMwMSIsImFwcGxpY2F0aW9uSWQiOiIxYWFjNDE1NC1jYzEyLTQzZTEtYTgzZC1jOTk4ZTJhYjBlMDUiLCJhY2Nlc3NfY29udHJvbF9yb2xlX2lkIjoiNCIsImVtYWlsIjoicy5taXJ6YUBheGNhcGl0YWwuYWUifQ.bxsmF-HruMNA0QTBIarirxTqwPSAmHtbtFGtFGFNfQCVI1_1clvLgoHvQEMsyFs176w22a0xL6zD2DcJRtWda24kfoDg4QGsqCev94OChR_sycinPSKUbFyLoUpTZvizWPRZ1encmsvBBYnvzX_8kejOPwBXTfdinl8d7EI9dgTlNjXvfpLiX7hbOXllDhCec-0SmGfQU5X8wTf2f1yyIC9Pk3oPZGPbVlCg72uwGR5cy3TRbY0oFXYONycJPJ6HM1B8XXjAbSQsFmB7vifRYQ66xOl5fQfeQshRCRft3ocTXjDM1j615bNmiRCnh5ddnj958MErav5Saj0iJ4n2_A',
                    'content-type: application/json',
                    'origin: https://expert.propertyfinder.ae',
                    'priority: u=1, i',
                    'sec-ch-ua: "Chromium";v="128", "Not;A=Brand";v="24", "Google Chrome";v="128"',
                    'sec-ch-ua-mobile: ?0',
                    'sec-ch-ua-platform: "macOS"',
                    'sec-fetch-dest: empty',
                    'sec-fetch-mode: cors',
                    'sec-fetch-site: cross-site',
                    'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36',
                    'x-mycrm-expand-data: true',
                    'x-requested-with: XMLHttpRequest',
                ],
            ]);

            $response = curl_exec($curl);
            $result   = json_decode($response, true);
            if (isset($result['whatsapp']) && count($result['whatsapp']) > 0) {
                foreach ($result['whatsapp'] as $lead) {
                    $leadsData[] = [
                        $lead['id'],
                        $lead['phone'],
                        $lead['user']['id'],
                        $lead['user']['public']['first_name'],
                        $lead['user']['public']['last_name'],
                        $lead['user']['public']['phone'],
                        $lead['user']['public']['licence'],
                        $lead['property']['id'],
                        $lead['property']['reference'],
                        $lead['property']['location']['community'],
                    ];
                }
            }
        }

        curl_close($curl);
        dd($leadsData);
        return Excel::download(new WhatsappLeadsExport($leadsData), 'whatsapp_leads.xlsx');
    }

    public function portalTesting(Request $request)
    {
        return propertyTotalCounter($request->user_id);
    }

    public function bayutToken()
    {
        $bayut = Portal::where('name', 'Bayut')->first();
        return view('admin.token_update.bayut')
            ->with('bayut', $bayut);
    }
    public function bayutTokenUpdate(Request $request)
    {
        $bayut = Portal::where('name', 'Bayut')->first();
        if ($request->token) {
            $bayut->token = $request->token;
        }
        if ($request->token_commercial) {
            $bayut->token_commercial = $request->token_commercial;
        }
        if ($request->token_abu_dhabi) {
            $bayut->token_abu_dhabi = $request->token_abu_dhabi;
        }
        $bayut->save();
        Artisan::call('add:bayutPropertyMeta');
        // dispatch(new BayutCookieUpdate());
        $result = ['status' => '1', 'msg' => "Token Updated"];
        return json_encode($result);
    }

    public function getMetaData(Request $request)
    {
        // dd(endsWithNumberInBrackets("Sec-232: Hi, I have a question about Palm Jumeirah villas."));

        // $metas = PropertyMeta::where('meta_key', 'like', 'dubizzle' . '%')->orWhere('meta_key', 'like', 'bayut' . '%')->orderBy('id', 'desc')->delete();
        $metas = PropertyMeta::where('meta_key', 'like', 'dubizzle' . '%')->orWhere('meta_key', 'like', 'bayut' . '%')->orderBy('updated_at', 'desc')->limit(500)->get();
        return view('admin.token_update.bayut_meta')
            ->with('metas', $metas);
    }
    public function deleteMetaData(Request $request)
    {
        $count     = PropertyMeta::where('meta_key', 'like', 'dubizzle' . '%')->orWhere('meta_key', 'like', 'bayut' . '%')->orderBy('id', 'desc')->count();
        $metas     = PropertyMeta::where('meta_key', 'like', 'dubizzle' . '%')->orWhere('meta_key', 'like', 'bayut' . '%')->orderBy('id', 'desc')->delete();
        $nullCount = PropertyMeta::whereNull('meta_key')->orderBy('id', 'desc')->count();
        $metas     = PropertyMeta::whereNull('meta_key')->orderBy('id', 'desc')->delete();
        return "Data Cleared " . $count . " Nullcount " . $nullCount;
    }

    public function getLeadMessage(Request $request)
    {
        $query = Lead::query();
        $query = $query->where('message', 'like', 'Sec-%')    // Messages starting with "Sec-"
            // ->whereRaw('message NOT REGEXP "\\(\\d+\\)$"');
            ->whereRaw("message NOT REGEXP '\\\\(\\\\d+\\\\)$'"); // Adjusted escaping
        // ->where('lead_status_id',1)
        if ($request->agent_id) {
            $query->where('agent_id', \request("agent_id"));
        }
        if ($request->lead_status_id) {
            $query->where('lead_status_id', \request("lead_status_id"));
        }
        if ($request->get_not_archive) {
            $query->where('is_archive', 0);
        }
        $leads = $query->get();

        return view('admin.token_update.lead_message')
            ->with('leads', $leads);
    }

    public function getLeadMessageUpdate(Request $request)
    {
        $validatedData = $request->validate([
            'id'     => 'required|exists:leads,id',
            'action' => 'required|boolean',
            'key'    => 'required',
        ]);

        $status                          = Lead::findOrFail($validatedData['id']);
        $status->{$validatedData['key']} = $validatedData['action'];
        $status->updated_by_id           = auth()->user()->id;

        if ($status->save()) {
            $result = ['status' => 200, 'msg' => 'Lead Status updated successfully'];
        } else {
            $result = ['status' => 500, 'msg' => 'Something went wrong!!'];
        }

        return response()->json($result);
    }
    public function getLeadMessageDelete(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'integer', // Ensure each ID is an integer
        ]);

        try {
            // Retrieve the IDs from the request
            $ids = $request->input('ids');

            // Delete the leads with the given IDs
            foreach ($ids as $id) {
                $lead = Lead::find($id);
                if ($lead) {
                    $lead_criteria = LeadCriteria::where('lead_id', $id)->first();
                    if ($lead_criteria) {
                        $lead_criteria_locations = LeadCriteriaLocation::where('lead_criteria_id', $lead_criteria->id)->get();
                        if ($lead_criteria_locations) {
                            foreach ($lead_criteria_locations as $location) {
                                $location->delete();
                            }
                        }
                    }

                    $lead->criteria()->delete();
                    $lead->notes()->delete();
                    $lead->delete();
                }
            }

            return response()->json([
                'message' => 'Leads deleted successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while deleting leads.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function _getAccessToken()
    {
        $url = "https://square-cake-5fcc.axcmeta.workers.dev/";

        // Send GET request
        $response = Http::withOptions(['allow_redirects' => true])->get($url);
        return $response;

        $headers = $response->headers();

        // Check for Set-Cookie headers
        if (isset($headers['set-cookie'])) {
            $cookies = $headers['set-cookie'];

            // Loop through cookies to find kc_access_token
            foreach ($cookies as $cookie) {
                if (strpos($cookie, 'kc_access_token') !== false) {
                    // Extract the kc_access_token value
                    preg_match('/kc_access_token=([^;]+)/', $cookie, $matches);
                    if (! empty($matches[1])) {
                        return $matches[1];
                    }
                }
            }
        }

        // Return null if token is not found
        return 'null';
    }

    public function getAccessToken($field)
    {
        // The URL you need to send the request to
        $url = "https://square-cake-5fcc.axcmeta.workers.dev/";

        try {
            // Send a GET request to the URL
            $response = Http::get($url);

            // Check if the request was successful
            if ($response->successful()) {
                // Parse the response cookies
                $cookies = $response->json('responseCookies');

                // Check if kc_refresh_token exists in the response cookies
                if (strpos($cookies, 'kc_refresh_token') !== false) {
                    // Use a regex to extract the kc_access_token from the cookies
                    preg_match('/kc_access_token\s*=\s*([^;]+)/', $cookies, $matches);

                    // Return the kc_access_token if found
                    if (isset($matches[1])) {
                        $genToken       = 'kc_access_token=' . $matches[1] . '; HttpOnly; Max-Age=900; Domain=.bayut.com; Secure; SameSite=Lax; Path=/';
                        $portal         = Portal::where('name', 'Bayut')->first();
                        $portal->$field = $genToken;
                        $portal->save();
                        // dd($response, $genToken);
                        info($genToken . ' Bayut Token for ' . $field);
                    } else {
                        throw new Exception("kc_access_token not found in response cookies.");
                    }
                } else {
                    throw new Exception("kc_refresh_token not found in response cookies.");
                }
            } else {
                throw new Exception("Failed to fetch data from the URL. Status: " . $response->status());
            }
        } catch (Exception $e) {
            // Handle errors
            return "Error: " . $e->getMessage();
        }
    }

    public function bayutSecondaryUsersMeta(Request $request)
    {

        $portal = Portal::where('name', 'Bayut')->first();
        $cookie = $portal->token;
        $curl   = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL            => 'https://www.bayut.com/api-v7/agencies/9124/agent-details?tab=agent-performance&page=1&portal=bayut&perPage=50000',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'GET',
            CURLOPT_HTTPHEADER     => [
                'accept: */*',
                'accept-language: en',
                'app-name: phoenix',
                'cookie: ' . $cookie,
                'priority: u=1, i',
                'referer: https://www.bayut.com/profolio/reports/agent-stats?tab=agent-performance&page=1&portal=bayut&perPage=25',
                'sec-ch-ua: "Google Chrome";v="131", "Chromium";v="131", "Not_A Brand";v="24"',
                'sec-ch-ua-mobile: ?0',
                'sec-ch-ua-platform: "macOS"',
                'sec-fetch-dest: empty',
                'sec-fetch-mode: cors',
                'sec-fetch-site: same-origin',
                'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
            ],
        ]);

        $response = curl_exec($curl);
        $res      = json_decode($response, true);

        curl_close($curl);
        $message = $res['message'] ?? null;

        if ($message == "Unauthenticated.") {
            sleep(2);
            Log::channel('urlLogs')->error("Unauthenticated");
            $this->getAccessToken('token');
            return response()->json(['status' => '0', 'msg' => "token expired"]);
        }
        $data  = $res['data'] ?? [];
        $count = 0;
        foreach ($data as $item) {
            dd($item);
            $meta = [
                'bayut_id'                          => isset($item['id']) ? $item['id'] : null,
                'bayut_externalId'                  => isset($item['externalId']) ? $item['externalId'] : null,
                'bayut_slug'                        => isset($item['slug']) ? $item['slug'] : null,
                'bayut_email'                       => isset($item['email']) ? $item['email'] : null,
                'bayut_state'                       => isset($item['state']) ? $item['state'] : null,
                'bayut_editState'                   => isset($item['editState']) ? $item['editState'] : null,
                'bayut_quota_featured'              => isset($item['quota_featured']) ? $item['quota_featured'] : null,
                'bayut_verifiedListingsCount'       => isset($item['verifiedListingsCount']) ? $item['verifiedListingsCount'] : null,
                'bayut_checkedListingsCount'        => isset($item['checkedListingsCount']) ? $item['checkedListingsCount'] : null,
                'bayut_truCheckedListingsCount'     => isset($item['truCheckedListingsCount']) ? $item['truCheckedListingsCount'] : null,
                'bayut_experience'                  => isset($item['experience']) ? $item['experience'] : null,
                'bayut_basic'                       => isset($item['usedQuota']['basic']) ? $item['usedQuota']['basic'] : null,
                'bayut_hot'                         => isset($item['usedQuota']['hot']) ? $item['usedQuota']['hot'] : null,
                'bayut_signature'                   => isset($item['usedQuota']['signature']) ? $item['usedQuota']['signature'] : null,
                'bayut_devBasic'                    => isset($item['usedQuota']['devBasic']) ? $item['usedQuota']['devBasic'] : null,
                'bayut_devHot'                      => isset($item['usedQuota']['devHot']) ? $item['usedQuota']['devHot'] : null,
                'bayut_devSignature'                => isset($item['usedQuota']['devSignature']) ? $item['usedQuota']['devSignature'] : null,
                'bayut_total'                       => isset($item['usedQuota']['total']) ? $item['usedQuota']['total'] : null,
                'bayut_createdAt'                   => isset($item['createdAt']) ? $item['createdAt'] : null,
                'bayut_isVerified'                  => isset($item['isVerified']) ? $item['isVerified'] : null,
                'bayut_isProconnectEnabled'         => isset($item['isProconnectEnabled']) ? $item['isProconnectEnabled'] : null,
                'bayut_isAgencyAdmin'               => isset($item['isAgencyAdmin']) ? $item['isAgencyAdmin'] : null,
                'bayut_isActive'                    => isset($item['isActive']) ? $item['isActive'] : null,
                'bayut_listingsCount_bayut_sale'    => isset($item['listingsCount']['bayut']['by_purpose']['sale']) ? $item['listingsCount']['bayut']['by_purpose']['sale'] : null,
                'bayut_listingsCount_bayut_rent'    => isset($item['listingsCount']['bayut']['by_purpose']['rent']) ? $item['listingsCount']['bayut']['by_purpose']['rent'] : null,
                'bayut_listingsCount_dubizzle_sale' => isset($item['listingsCount']['dubizzle']['by_purpose']['sale']) ? $item['listingsCount']['dubizzle']['by_purpose']['sale'] : null,
                'bayut_listingsCount_dubizzle_rent' => isset($item['listingsCount']['dubizzle']['by_purpose']['rent']) ? $item['listingsCount']['dubizzle']['by_purpose']['rent'] : null,
            ];

            $count++;
            // Fetch the user by email
            $user = User::where('email', $meta['bayut_email'])->first();

            // Check if user exists
            if ($user) {
                foreach ($meta as $key => $value) {
                    UserMeta::storeUserMeta($user->id, $key, $value !== '' ? $value : null);
                }
            } else {
                // Optionally handle the case where the user is not found
                // Log or throw an error
                Log::warning("User not found for email: " . $meta['bayut_email']);
            }
        }
    }

    public function dubizzleSecondaryUsersMeta(Request $request)
    {
        $portal = Portal::where('name', 'Bayut')->first();
        $cookie = $portal->token;
        $curl   = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL            => 'https://www.bayut.com/api-v7/agencies/9124/agent-details?tab=agent-performance&page=1&portal=dubizzle&perPage=50000',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'GET',
            CURLOPT_HTTPHEADER     => [
                'accept: */*',
                'accept-language: en',
                'app-name: phoenix',
                'cookie: ' . $cookie,
                'priority: u=1, i',
                'referer: https://www.bayut.com/profolio/reports/agent-stats?tab=agent-performance&page=1&portal=dubizzle&perPage=25',
                'sec-ch-ua: "Google Chrome";v="131", "Chromium";v="131", "Not_A Brand";v="24"',
                'sec-ch-ua-mobile: ?0',
                'sec-ch-ua-platform: "macOS"',
                'sec-fetch-dest: empty',
                'sec-fetch-mode: cors',
                'sec-fetch-site: same-origin',
                'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
            ],
        ]);

        $response = curl_exec($curl);
        $res      = json_decode($response, true);

        curl_close($curl);
        $message = $res['message'] ?? null;

        if ($message == "Unauthenticated.") {
            sleep(2);
            Log::channel('urlLogs')->error("Unauthenticated");
            $this->getAccessToken('token');
            return response()->json(['status' => '0', 'msg' => "token expired"]);
        }
        $data  = $res['data'] ?? [];
        $count = 0;
        foreach ($data as $item) {

            $meta = [
                'dubizzle_id'                      => isset($item['id']) ? $item['id'] : null,
                'dubizzle_externalId'              => isset($item['externalId']) ? $item['externalId'] : null,
                'dubizzle_slug'                    => isset($item['slug']) ? $item['slug'] : null,
                'dubizzle_email'                   => isset($item['email']) ? $item['email'] : null,
                'dubizzle_state'                   => isset($item['state']) ? $item['state'] : null,
                'dubizzle_editState'               => isset($item['editState']) ? $item['editState'] : null,
                'dubizzle_quota_featured'          => isset($item['quota_featured']) ? $item['quota_featured'] : null,
                'dubizzle_verifiedListingsCount'   => isset($item['verifiedListingsCount']) ? $item['verifiedListingsCount'] : null,
                'dubizzle_checkedListingsCount'    => isset($item['checkedListingsCount']) ? $item['checkedListingsCount'] : null,
                'dubizzle_truCheckedListingsCount' => isset($item['truCheckedListingsCount']) ? $item['truCheckedListingsCount'] : null,
                'dubizzle_experience'              => isset($item['experience']) ? $item['experience'] : null,
                'dubizzle_basic'                   => isset($item['usedQuota']['basic']) ? $item['usedQuota']['basic'] : null,
                'dubizzle_hot'                     => isset($item['usedQuota']['hot']) ? $item['usedQuota']['hot'] : null,
                'dubizzle_signature'               => isset($item['usedQuota']['signature']) ? $item['usedQuota']['signature'] : null,
                'dubizzle_devBasic'                => isset($item['usedQuota']['devBasic']) ? $item['usedQuota']['devBasic'] : null,
                'dubizzle_devHot'                  => isset($item['usedQuota']['devHot']) ? $item['usedQuota']['devHot'] : null,
                'dubizzle_devSignature'            => isset($item['usedQuota']['devSignature']) ? $item['usedQuota']['devSignature'] : null,
                'dubizzle_total'                   => isset($item['usedQuota']['total']) ? $item['usedQuota']['total'] : null,
                'dubizzle_createdAt'               => isset($item['createdAt']) ? $item['createdAt'] : null,
                'dubizzle_isVerified'              => isset($item['isVerified']) ? $item['isVerified'] : null,
                'dubizzle_isProconnectEnabled'     => isset($item['isProconnectEnabled']) ? $item['isProconnectEnabled'] : null,
                'dubizzle_isAgencyAdmin'           => isset($item['isAgencyAdmin']) ? $item['isAgencyAdmin'] : null,
                'dubizzle_isActive'                => isset($item['isActive']) ? $item['isActive'] : null,
                // 'dubizzle_listingsCount_bayut_sale'    => isset($item['listingsCount']['bayut']['by_purpose']['sale']) ? $item['listingsCount']['bayut']['by_purpose']['sale'] : null,
                // 'dubizzle_listingsCount_bayut_rent'    => isset($item['listingsCount']['bayut']['by_purpose']['rent']) ? $item['listingsCount']['bayut']['by_purpose']['rent'] : null,
                // 'dubizzle_listingsCount_dubizzle_sale' => isset($item['listingsCount']['dubizzle']['by_purpose']['sale']) ? $item['listingsCount']['dubizzle']['by_purpose']['sale'] : null,
                // 'dubizzle_listingsCount_dubizzle_rent' => isset($item['listingsCount']['dubizzle']['by_purpose']['rent']) ? $item['listingsCount']['dubizzle']['by_purpose']['rent'] : null,
            ];
            $count++;
            // dd($meta);
            // Fetch the user by email
            $user = User::where('email', $meta['dubizzle_email'])->first();

            // Check if user exists
            if ($user) {
                foreach ($meta as $key => $value) {
                    UserMeta::storeUserMeta($user->id, $key, $value !== '' ? $value : null);
                }
            } else {
                // Optionally handle the case where the user is not found
                // Log or throw an error
                Log::warning("User not found for email: " . $meta['dubizzle_email']);
            }
        }
    }

    public function bayutTruBrokerApi()
    {
        // dd('Bayut TruBroker API');
        $portal = Portal::where('name', 'Bayut')->first();
        $cookie = $portal->token;

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL            => 'https://www.bayut.com/api-v7/users/2381209/stats/search?slug=trubroker_badges_xp_all_agents_profolio_bayutuae_prod_alias%2F_search&skipAuthorization=true',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            // CURLOPT_POSTFIELDS     => '{"size":200,"from":0,"track_total_hits":true,"query":{"bool":{"filter":[{"term":{"is_active_agent":"1"}},{"terms":{"bayut_backend_user_id":[]}},{"terms":{"most_active_emirate_curr_month":["Dubai","Abu Dhabi"]}}]}}}',
            // CURLOPT_POSTFIELDS     => '{"size":200,"from":0,"track_total_hits":true,"query":{"bool":{"filter":[{"term":{"is_active_agent":"1"}},{"terms":{"most_active_emirate_curr_month":["Dubai","Abu Dhabi"]}}]}}}',
            CURLOPT_POSTFIELDS     => '{"size":200,"from":0,"track_total_hits":true,"query":{"bool":{"filter":[{"term":{"is_active_agent":"1"}},{"terms":{"bayut_backend_agency_id":[9124]}},{"term":{"is_trubroker_badge_prev_month":"1"}},{"terms":{"most_active_emirate_curr_month":["Dubai","Abu Dhabi"]}}]}},"aggs":{"all_agent_external_ids":{"terms":{"field":"agent_external_id","size":50000}}}}',

            CURLOPT_HTTPHEADER     => [
                'accept: */*',
                'accept-language: en-US,en;q=0.9,ur;q=0.8,ar;q=0.7',
                'app-name: phoenix',
                'content-type: application/json',
                'cookie: ' . $cookie,
                'origin: https://www.bayut.com',
                'priority: u=1, i',
                'referer: https://www.bayut.com/profolio/reports/agent-stats?tab=trubroker-report&page=1&portal=bayut&badges=is_trubroker_badge_prev_month',
                'sec-ch-ua: "Google Chrome";v="131", "Chromium";v="131", "Not_A Brand";v="24"',
                'sec-ch-ua-mobile: ?0',
                'sec-ch-ua-platform: "macOS"',
                'sec-fetch-dest: empty',
                'sec-fetch-mode: cors',
                'sec-fetch-site: same-origin',
                'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
            ],
        ]);

        $response = curl_exec($curl);
        $res      = json_decode($response, true);

        curl_close($curl);
        $message = $res['message'] ?? null;

        if ($message == "Unauthenticated.") {
            sleep(2);
            Log::channel('urlLogs')->error("Unauthenticated");
            $this->getAccessToken('token');
            return response()->json(['status' => '0', 'msg' => "token expired"]);
        }
        $data = $res['hits']['hits'] ?? [];
        // return $data;
        $meta = [];
        foreach ($data as $item) {
            $user = User::where('email', $item['_source']['email'])->first();
            // Assuming $user is defined somewhere in your loop, otherwise assign it appropriately.
            foreach ($item['_source'] as $key => $value) {

                // Skip the specified keys
                if (in_array($key, ['user_langs_en', 'user_langs_ar', 'user_lang_codes_en'])) {
                    continue;
                }

                // Prepare the key as 'bayut_' followed by the original key
                $metaKey = 'bayut_' . $key;
                try {
                    UserMeta::storeUserMeta($user->id, $metaKey, $value !== '' ? $value : null);
                } catch (\Throwable $th) {
                    dd($item['_source']['email']);
                    throw $th;
                }
                // Save the key and value in UserMeta, defaulting to null if value is empty
            }
        }

        return $meta;
    }

    public function getBayutData(Request $request)
    {
        Artisan::call('add:bayutPropertyMeta');
        return "Bayut Data Fetched";
    }

    public function dashboardTest(Request $request)
    {
        $portal = Portal::where('name', 'Bayut')->first();

        // Total number of properties where is_live = true
        $totalPropertiesCount = Property::whereHas('propertyMeta', function ($query) {
            // Ensure property has 'bayut_status' as 'active'
            $query->where('meta_key', 'bayut_status')
                ->where('meta_value', 'active');
        })
            ->where('is_live', true) // Only consider live properties
            ->whereHas('propertyMeta', function ($query) {
                // Ensure property has 'bayut_level' (this is needed for the total count)
                $query->where('meta_key', 'bayut_level');
            })
            ->count();

        // Get all unique bayut_levels
        $uniqueBayutLevels = PropertyMeta::where('meta_key', 'bayut_level')
            ->distinct()
            ->pluck('meta_value');

        // Count the number of properties for each bayut_level
        $bayutLevelCounts = [];
        foreach ($uniqueBayutLevels as $bayutLevel) {
            $count = Property::whereHas('propertyMeta', function ($query) use ($bayutLevel) {
                $query->where('meta_key', 'bayut_level')
                    ->where('meta_value', $bayutLevel);
            })
                ->where('is_live', true)
                ->count();

            $bayutLevelCounts[$bayutLevel] = $count;
        }

        // Average bayut_rank
        $averageBayutRank = round(PropertyMeta::where('meta_key', 'bayut_rank')
            ->join('properties', 'property_meta.property_id', '=', 'properties.id')
            ->where('properties.is_live', true)
            ->avg('meta_value'), 2);

        // Count properties based on bayut_rank ranges
        $rankRanges = [
            '0-40'   => 0,
            '41-60'  => 0,
            '61-80'  => 0,
            '81-100' => 0,
        ];

        $propertyRanks = PropertyMeta::where('meta_key', 'bayut_rank')
            ->join('properties', 'property_meta.property_id', '=', 'properties.id')
            ->where('properties.is_live', true)
            ->pluck('meta_value');

        foreach ($propertyRanks as $rank) {
            if ($rank >= 0 && $rank <= 40) {
                $rankRanges['0-40']++;
            } elseif ($rank >= 41 && $rank <= 60) {
                $rankRanges['41-60']++;
            } elseif ($rank >= 61 && $rank <= 80) {
                $rankRanges['61-80']++;
            } elseif ($rank >= 81 && $rank <= 100) {
                $rankRanges['81-100']++;
            }
        }

        // Percentage of properties where bayut_trucheck_status is 'trucheckStatus'
        $trucheckStatusCount = PropertyMeta::where('meta_key', 'bayut_trucheck_status')
            ->where('meta_value', 'Truchecked')
            ->join('properties', 'property_meta.property_id', '=', 'properties.id')
            ->where('properties.is_live', true)
            ->count();

        $trucheckStatusPercentage = $totalPropertiesCount > 0
            ? ($trucheckStatusCount / $totalPropertiesCount) * 100
            : 0;

        // Count of bayut_status as active and inactive
        $bayutStatusCounts = PropertyMeta::where('meta_key', 'bayut_status')
            ->whereIn('meta_value', ['active', 'inactive'])
            ->join('properties', 'property_meta.property_id', '=', 'properties.id')
            ->where('properties.is_live', true)
            ->select('meta_value', \DB::raw('count(*) as count'))
            ->groupBy('meta_value')
            ->pluck('count', 'meta_value')
            ->toArray();

        $verified_count = Property::whereNot('completion_status', 'Off-Plan Primary')->whereHas('propertyMeta', function ($query) {
            $query->where('meta_key', 'bayut_review_status')
                ->where('meta_value', 'Approved');
        })->whereHas('portals', function ($q) use ($portal) {
            $q->where('portal_id', $portal->id);
        })
            // ->when($notAdmin, function ($q) use ($childrenIds) {
            //     $q->whereIn("agent_id", $childrenIds);
            // })
            // ->when(! empty($dates), function ($q) use ($dates) {
            //     $q->whereDate('properties.created_at', '>=', $dates['startDate'])->whereDate('properties.created_at', '<=', $dates['endDate']);
            // })
            ->where('is_live', '1')->count();

        // Prepare all data
        $result = [
            'total_properties_count'     => $totalPropertiesCount,
            'bayut_level_counts'         => $bayutLevelCounts,
            'average_bayut_rank'         => $averageBayutRank,
            'bayut_rank_ranges'          => $rankRanges,
            'trucheckStatusCount'        => $trucheckStatusCount,
            'trucheck_status_percentage' => round($trucheckStatusPercentage, 2),
            'bayut_status_counts'        => $bayutStatusCounts,
            'verified_count'             => $verified_count,
        ];

        return response()->json($result);
    }
    public function dubizzledashboardTest(Request $request)
    {
        $portal = Portal::where('name', 'Dubizzle')->first();

        // Total number of properties where is_live = true
        $totalPropertiesCount = Property::whereHas('propertyMeta', function ($query) {
            // Ensure property has 'bayut_status' as 'active'
            $query->where('meta_key', 'dubizzle_status')
                ->where('meta_value', 'active');
        })
            ->where('is_live', true) // Only consider live properties
            ->whereHas('propertyMeta', function ($query) {
                // Ensure property has 'dubizzle_level' (this is needed for the total count)
                $query->where('meta_key', 'dubizzle_level');
            })
            ->count();

        // Get all unique dubizzle_levels
        $uniqueBayutLevels = PropertyMeta::where('meta_key', 'dubizzle_level')
            ->distinct()
            ->pluck('meta_value');

        // Count the number of properties for each dubizzle_level
        $bayutLevelCounts = [];
        foreach ($uniqueBayutLevels as $bayutLevel) {
            $count = Property::whereHas('propertyMeta', function ($query) use ($bayutLevel) {
                $query->where('meta_key', 'dubizzle_level')
                    ->where('meta_value', $bayutLevel);
            })
                ->where('is_live', true)
                ->count();

            $bayutLevelCounts[$bayutLevel] = $count;
        }

        // Average dubizzle_rank
        $averageBayutRank = round(PropertyMeta::where('meta_key', 'dubizzle_rank')
            ->join('properties', 'property_meta.property_id', '=', 'properties.id')
            ->where('properties.is_live', true)
            ->avg('meta_value'), 2);

        // Count properties based on dubizzle_rank ranges
        $rankRanges = [
            '0-40'   => 0,
            '41-60'  => 0,
            '61-80'  => 0,
            '81-100' => 0,
        ];

        $propertyRanks = PropertyMeta::where('meta_key', 'dubizzle_rank')
            ->join('properties', 'property_meta.property_id', '=', 'properties.id')
            ->where('properties.is_live', true)
            ->pluck('meta_value');

        foreach ($propertyRanks as $rank) {
            if ($rank >= 0 && $rank <= 40) {
                $rankRanges['0-40']++;
            } elseif ($rank >= 41 && $rank <= 60) {
                $rankRanges['41-60']++;
            } elseif ($rank >= 61 && $rank <= 80) {
                $rankRanges['61-80']++;
            } elseif ($rank >= 81 && $rank <= 100) {
                $rankRanges['81-100']++;
            }
        }

        // Percentage of properties where dubizzle_trucheck_status is 'trucheckStatus'
        $trucheckStatusCount = PropertyMeta::where('meta_key', 'dubizzle_trucheck_status')
            ->where('meta_value', 'Truchecked')
            ->join('properties', 'property_meta.property_id', '=', 'properties.id')
            ->where('properties.is_live', true)
            ->count();

        $trucheckStatusPercentage = $totalPropertiesCount > 0
            ? ($trucheckStatusCount / $totalPropertiesCount) * 100
            : 0;

        // Count of dubizzle_status as active and inactive
        $bayutStatusCounts = PropertyMeta::where('meta_key', 'dubizzle_status')
            ->whereIn('meta_value', ['active', 'inactive'])
            ->join('properties', 'property_meta.property_id', '=', 'properties.id')
            ->where('properties.is_live', true)
            ->select('meta_value', \DB::raw('count(*) as count'))
            ->groupBy('meta_value')
            ->pluck('count', 'meta_value')
            ->toArray();

        $verified_count = Property::whereNot('completion_status', 'Off-Plan Primary')->whereHas('propertyMeta', function ($query) {
            $query->where('meta_key', 'dubizzle_review_status')
                ->where('meta_value', 'Approved');
        })->whereHas('portals', function ($q) use ($portal) {
            $q->where('portal_id', $portal->id);
        })
            // ->when($notAdmin, function ($q) use ($childrenIds) {
            //     $q->whereIn("agent_id", $childrenIds);
            // })
            // ->when(! empty($dates), function ($q) use ($dates) {
            //     $q->whereDate('properties.created_at', '>=', $dates['startDate'])->whereDate('properties.created_at', '<=', $dates['endDate']);
            // })
            ->where('is_live', '1')->count();

        // Prepare all data
        $result = [
            'total_properties_count'     => $totalPropertiesCount,
            'dubizzle_level_counts'      => $bayutLevelCounts,
            'average_dubizzle_rank'      => $averageBayutRank,
            'dubizzle_rank_ranges'       => $rankRanges,
            'trucheckStatusCount'        => $trucheckStatusCount,
            'trucheck_status_percentage' => round($trucheckStatusPercentage, 2),
            'dubizzle_status_counts'     => $bayutStatusCounts,
            'verified_count'             => $verified_count,
        ];

        return response()->json($result);
    }

    public function pftest()
    {
        $p = Property::where('publish_status', 'published')->where('is_live', 1)
            ->whereHas('propertyMeta', function ($query) {
                $query->where('meta_key', 'pf_level')
                    ->where('meta_value', '');
            })->pluck('id');

        dd($p);
    }

    public function checkDubizzleData(Request $request)
    {
        $property = Property::where('ref_no', $request->ref_no)->first();
        $meta     = PropertyMeta::where('property_id', $property->id)->get();
        return response()->json($meta);
    }

    public function dubizzleApi(Request $request)
    {
        $portal      = Portal::where('name', 'Bayut')->first();
        $cookie      = $portal->token;
        $metas       = UserMeta::where('meta_key', 'dubizzle_bayut_backend_user_id')->get();
        $today       = date('Y-m-d');
        $oneMonthAgo = date('Y-m-d', strtotime('-1 month'));

        foreach ($metas as $i) {
            // dd($i->meta_value);
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL            => 'https://www.bayut.com/api-v7/users/2381209/stats/search?slug=agent_performance_profolio_dubizzleuae_prod_alias%2F_search&start=' . $oneMonthAgo . '&end=' . $today . '&proxyNumberStatus=active&portalId=2&page=1&perPage=25',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING       => '',
                CURLOPT_MAXREDIRS      => 10,
                CURLOPT_TIMEOUT        => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST  => 'POST',
                CURLOPT_POSTFIELDS     => '{"query":{"bool":{"filter":[{"range":{"date_nk":{"gte":"' . $oneMonthAgo . '","lte":"' . $today . '"}}},{"terms":{"bayut_backend_user_id":["' . $i->meta_value . '"]}}]}},"aggs":{"users":{"terms":{"field":"bayut_backend_user_id","size":65535,"order":{"_key":"asc"}},"aggs":{"count_whatsapp_msgs_delivered":{"sum":{"field":"num_whatsapp_msg_delivered"}},"count_whatsapp_msgs_answered":{"sum":{"field":"num_whatsapp_agent_contacted_lead"}},"count_calls_connected":{"sum":{"field":"num_reply_calltracking_connected"}},"count_calls_notconnected":{"sum":{"field":"num_reply_calltracking_not_connected"}},"count_calls_missed":{"sum":{"field":"num_reply_calltracking_missed"}},"count_calls_total":{"sum":{"field":"num_reply_calltracking"}},"total_leads":{"sum":{"field":"num_reply_total"}},"email_leads":{"sum":{"field":"num_reply_email"}},"sms_leads":{"sum":{"field":"num_reply_sms"}},"whatsapp_leads":{"sum":{"field":"num_reply_whatsapp_deliveries"}},"phone_leads":{"sum":{"field":"num_reply_calltracking_deliveries"}},"call_tracking_leads":{"sum":{"field":"num_reply_calltracking"}},"call_leads":{"bucket_script":{"buckets_path":{"call_tracking_leads":"call_tracking_leads","phone_leads":"phone_leads"},"script":"params.phone_leads"}},"sum_whatsapp_response_time":{"sum":{"field":"sum_whatsapp_response_time"}},"avg_response_time":{"bucket_script":{"buckets_path":{"msgs_delivered":"count_whatsapp_msgs_delivered","sum_response_time":"sum_whatsapp_response_time"},"script":"if (params.msgs_delivered == 0) {return 0} else {params.sum_response_time / params.msgs_delivered}"}},"call_answer_rate":{"bucket_script":{"buckets_path":{"calls_connected":"count_calls_connected","calls_notconnected":"count_calls_notconnected","calls_missed":"count_calls_missed"},"script":"if ((params.calls_connected + params.calls_missed) == 0) { return 0 } else { return params.calls_connected / (params.calls_connected + params.calls_missed) }"}},"response_rate":{"bucket_script":{"buckets_path":{"msgs_delivered":"count_whatsapp_msgs_delivered","msgs_answered":"count_whatsapp_msgs_answered"},"script":"if (params.msgs_delivered == 0) { return 0 } else { return params.msgs_answered / params.msgs_delivered }"}},"sorting":{"bucket_sort":{"sort":[],"size":65535}},"phone_views":{"sum":{"field":"num_reply_phone_clicks"}},"whatsapp_views":{"sum":{"field":"num_reply_whatsapp_clicks"}}}}}}',
                CURLOPT_HTTPHEADER     => [
                    'accept: */*',
                    'accept-language: en-US,en;q=0.9,ur;q=0.8,ar;q=0.7',
                    'app-name: phoenix',
                    'content-type: application/json',
                    'cookie: ' . $cookie,
                    'origin: https://www.bayut.com',
                    'priority: u=1, i',
                    'referer: https://www.bayut.com/profolio/reports/agent-stats?tab=trubroker-report&page=1&portal=dubizzle',
                    'sec-ch-ua: "Not A(Brand";v="8", "Chromium";v="132", "Google Chrome";v="132"',
                    'sec-ch-ua-mobile: ?0',
                    'sec-ch-ua-platform: "macOS"',
                    'sec-fetch-dest: empty',
                    'sec-fetch-mode: cors',
                    'sec-fetch-site: same-origin',
                    'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36',
                    'x-xsrf-token: eyJpdiI6IkM0Ulo3UXVIOGxHM1BodUNhZk42VHc9PSIsInZhbHVlIjoieFlFWEx6ZTlyYzRcL04wY0FJUHlVY1g1Q1hIT2RERldKSXlLa081RjEySE9JTXd0aU1MV2VyRlpJMGlySUJubElYV1VBMjg0ODJCU1Z3TjZyOXVldWV3PT0iLCJtYWMiOiJhYmQyY2M5YWE5ZTZlYTliNGY2NTNkNjBlMGQ3ODgxNzU2MmMyODg4ZDQ2YjdhNTY2ZDNlYzMyMzU0ODQyNDFiIn0=',
                ],
            ]);

            $response = curl_exec($curl);
            $res      = json_decode($response, true);
            curl_close($curl);

            $message = $res['message'] ?? null;
            if ($message == "Unauthenticated.") {
                Log::channel('urlLogs')->error("Unauthenticated");
                $externalController = new ExternalApiController();
                $externalController->getAccessToken('token');
                info('DubizzleAgentStats.php: Unauthenticated');
                Artisan::call('fetch:dubizzleAgentsStats');
            }
            $data = $res['hits']['hits'] ?? [];
            $data = end($data);
            $meta = [];
            $user = User::where('email', $data['_source']['agent_email'])->first();
            foreach ($data['_source'] as $key => $value) {
                if (in_array($key, [
                    'agent_performance_profolio_sk',
                    'agency_sk',
                    'property_agentperson_sk',
                    'agency_id',
                    'agent_id',
                    'bayut_backend_user_id',
                    'bayut_backend_agency_id',
                    'agency_name',
                    'agent_name',
                    'agent_email',
                    'agent_proxy_number',
                ])) {
                    continue;
                }

                $metaKey = 'dubizzle_' . $key;
                try {
                    // info($data['_source']['agent_email'] . ' ' . $metaKey . ' ' . $value);
                    // sleep(1);
                    UserMeta::storeUserMeta($user->id, $metaKey, $value !== '' ? $value : null);
                } catch (\Throwable $th) {
                    info($data['_source']['email'] . ' User Not Found in System. Error in DubizzleAgentStats.php');
                    continue;
                }
            }
        }

        return Command::SUCCESS;
    }

    public function PfToken(Request $request)
    {
        $pf_token = GeneralSetting::where('slug', 'pf-agent-data-api-token')->first();
        if (! $pf_token) {
            $pf_token         = new GeneralSetting();
            $pf_token->module = 'Portal';
            $pf_token->slug   = 'pf-agent-data-api-token';
            $pf_token->key    = 'pf-agent-data-api-token';
            $pf_token->value  = '';
            $pf_token->save();
        }
        return view('admin.token_update.pf')
            ->with('pf_token', $pf_token);
    }
    public function PfTokenUpdate(Request $request)
    {
        $pf_token = GeneralSetting::where('slug', 'pf-agent-data-api-token')->first();
        if (! $pf_token) {
            $pf_token         = new GeneralSetting();
            $pf_token->module = 'Portal';
            $pf_token->slug   = 'pf-agent-data-api-token';
            $pf_token->key    = 'pf-agent-data-api-token';
            $pf_token->value  = '';
        }
        $pf_token->value = $request->token;
        $pf_token->save();
        Artisan::call('fetch:pfAgentsStats');
        $result = ['status' => '1', 'msg' => "Token Updated"];
        return response()->json($result);
    }

    public function bayutAgentInsight(Request $request)
    {

        return view('admin.token_update.bayutAgentInsightImport')
            ->with('pf_token', []);
    }

    public function showMatchListingsForm()
    {
        $maxPrice = Property::max('down_payment_price');
        $maxSize = Property::max('built_up_area');
        $beds = Bed::where("status", true)->get();
        $baths = Bath::where("status", true)->get();
        $propertyCategories = PropertyCategory::with('portals.portal')->where('status', true)->orderBy('order')->get();
        return view('form.listings.matchListingsForm', compact('beds', 'baths', 'propertyCategories', 'maxPrice', 'maxSize'));
    }

    public function matching()
    {
        return view('form.listings.matching');
    }

    public function matchListings()
    {
        return view('form.listings.matchListings');
    }

    public function searchListings(Request $request)
    {
        return view('form.listings.searchListings');
    }

    public function showSearchListingsForm()
    {
        $beds = Bed::where("status", true)->get();
        $baths = Bath::where("status", true)->get();
        $features = PropertyAmenity::where('status', true)->get();
        $propertyCategories = PropertyCategory::with('portals.portal')->where('status', true)->orderBy('order')->get();
        return view('form.listings.searchListingsForm', compact('beds', 'baths', 'propertyCategories', 'features'));
    }
    public function getMediums()
    {
        $mediums = Medium::where('status', true)->get();
        return response()->json($mediums);
    }
    public function getSources()
    {
        $sources = Source::where('status', true)->get();
        return response()->json($sources);
    }

}
