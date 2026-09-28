<?php

namespace App\Http\Controllers\Api\V1\MatchingListing;
use App\Http\Controllers\Api\BaseController;
use App\Http\Controllers\Controller;
use App\Jobs\MatchListingsJob;
use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\TempProperty;
use App\Models\PropertyCategory;
use App\Models\Bed;
use App\Models\Bath;
use App\Models\Group;
use App\Models\Community;
use App\Models\CommunityProperty;
use App\Models\SubCommunity;
use Illuminate\Support\Facades\Log;
use App\Traits\TelegramTrait;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\PropertyFeatures;
use App\Models\TempPropertyFeatures;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use App\Models\PropertyPrice;
use App\Models\TempPropertyPrice;
use App\Http\Resources\MatchingListing\MatchingListingResource;
use App\Http\Resources\Property\PropertyResource;
use Illuminate\Support\Str;

class MatchingListingApiController extends BaseController
{
    use TelegramTrait;

    public function matchingListing(Request $request)
    {
        try {
            $perPage = request('per_page', 10);
            $page = request('page', 1);
            $beds = $request->input('bed_ids');
            $baths = $request->input('bath_ids');
            $minPrice = $request->input('min_price');
            $maxPrice = $request->input('max_price');
            $minSize = $request->input('min_size');
            $maxSize = $request->input('max_size');
            $categories = $request->input('category_ids');
            $completionStatus = $request->input('completion_status');
            $for = $request->input('for');
            $locations = $request->input('locations');
            // $location = $request->input('location_id');
            // $locationType = $request->input('location_type');
            $furnishedListReq = $request->input('furnished');
            $views = $request->input('views');
            $units = $request->input('unit_no');
            $searchKeywords = $request->input('search_keywords');
            $agentId = $request->input('agent_id');
            $telegramChatId = $request->input('telegram_chat_id');
            $refNumbers = $request->input('ref_no');
            $furnishedList = ['FURNISHED',  'Furnished', 'UNFURNISHED', 'Semi Furnished', 'SEMI_FURNISHED', 'SEMI-FURNISHED'];

            $query = Property::with('agent', 'community', 'subCommunity', 'communityProperty', 'photos', 'bed', 'price', 'bath', 'propertyCategory', 'feature')->whereHas('photos');

            $query->when($agentId, function ($query) use ($agentId) {
                $query->where('agent_id', $agentId);
            })->when($telegramChatId, function ($query) use ($telegramChatId) {
                $query->whereHas('agent', function ($query) use ($telegramChatId) {
                    $query->where('telegram_chatid', $telegramChatId);
                });
            })->when($refNumbers, function ($query) use ($refNumbers) {
                $query->where('ref_no', $refNumbers);
            })->when($beds, function ($query) use ($beds) {
                $query->whereIn('bed_id', $beds);
            })->when($baths, function ($query) use ($baths) {
                $query->whereIn('bath_id', $baths);
            })->when($units, function ($query) use ($units) {
                $query->whereIn('unit_no', $units);
            })->when($minPrice, function ($query) use ($minPrice) {
                $query->where('down_payment_price', '>=', $minPrice);
            })->when($maxPrice, function ($query) use ($maxPrice) {
                $query->where('down_payment_price', '<=', $maxPrice);
            })->when($minSize, function ($query) use ($minSize) {
                $query->where('built_up_area', '>=', intval($minSize));
            })->when($maxSize, function ($query) use ($maxSize) {
                $query->where('built_up_area', '<=', intval($maxSize));
            })->when($categories, function ($query) use ($categories) {
                $query->whereIn('property_category_id', $categories);
            })->when($views, function ($query) use ($views) {
                foreach ($views as $view) {
                    if (is_array($view)) {
                        foreach ($view as $v) {
                            $query->where('view', 'like', '%' . $v . '%');
                        }
                    } else {
                        $query->where('view', 'like', '%' . $view . '%');
                    }
                }
            })->when($furnishedListReq, function ($query) use ($furnishedListReq, $furnishedList) {
                foreach($furnishedListReq as $furnished){
                    if(in_array($furnished, $furnishedList)){
                        if(in_array($furnished, ['Semi Furnished', 'SEMI_FURNISHED', 'SEMI-FURNISHED'])){
                            $query->whereIn('furnished', ['Semi Furnished', 'SEMI_FURNISHED', 'SEMI-FURNISHED']);
                        }elseif(in_array($furnished, ['FURNISHED', 'furnished'])){
                            $query->whereIn('furnished', ['FURNISHED', 'Furnished']);
                        }elseif(in_array($furnished, ['UNFURNISHED', 'unfurnished'])){
                            $query->whereIn('furnished', ['UNFURNISHED', 'unfurnished']);
                        }
                    }
                }
            })->when($for, function ($query) use ($for) {
                if($for == 'buy'){
                    $query->whereIn('for', ['Residential for sale', 'Commercial for sale']);
                } elseif($for =='rent'){
                    $query->whereIn('for', ['Residential for rent', 'Commercial for rent']);
                }
            })
            ->when(!empty($locations), function ($query) use ($locations) {
                $query->where(function ($query) use ($locations) {
                    foreach ($locations as $index => $location) {
                        if (!empty($location['type']) && !empty($location['id'])) {
                            $column = $location['type'] . '_id';
                            // Use 'orWhere' after the first condition
                            if ($index === 0) {
                                $query->where($column, $location['id']);
                            } else {
                                $query->orWhere($column, $location['id']);
                            }
                        }
                    }
                });
            })->when($completionStatus && $completionStatus !== 'all', function ($query) use ($completionStatus) {
                $query->where(function ($query) use ($completionStatus) {
                    $query->where('completion_status', 'like', '%' . $completionStatus . '%');
                });
            })
            ->when($searchKeywords, function ($query) use ($searchKeywords) {
                $query->where(function ($query) use ($searchKeywords) {
                    $query->where('title', 'like', '%' . $searchKeywords . '%');
                });
            });

            $data = $query->whereIn('publish_status', ['PUBLISHED', 'POCKET'])->orderBy('created_at', 'desc')->paginate($perPage, ['*'], 'page', $page);

            $data = MatchingListingResource::collection($data);

            return $this->sendPaginationResponse($data, 'Data fetched', $page, $perPage, 'Matching Listings', $data->total());
            // $this->sendTelegramMessageHtml('Test', ['-1002267346260'], 'http://secondary.lan/', 'Matching Lisiting');

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred. Please try again' . json_encode($e->getMessage() . ' file: ' . $e->getFile()),
            ], 500);
        }
    }
    public function sendListingRequirementIntoTelegram(Request $request)
    {
        try{
            $data = $request->all();
            $validator = Validator::make($data, [
                "telegram_chat_id" => "required|exists:users,telegram_chatid",
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $user = User::where('telegram_chatid', $data['telegram_chat_id'])->first();
            $userName = $user->name;
            $userTelegramName = $user->telegram_username;
            $useTelegramId = $user->telegram_chatid;
            $userMobile = $user->mobile;


            $beds = $request->input('bed_ids');
            $buyerType = $request->input('buyer_type', null);
            $buyerTimeline = $request->input('buyer_timeline', null);
            $baths = $request->input('bath_ids');
            $groupTopic = $request->input('group_topic', null);
            Log::info('groupTopic: ' . $groupTopic);
            $minPrice = $request->input('min_price');
            $maxPrice = $request->input('max_price');
            $minSize = $request->input('min_size');
            $maxSize = $request->input('max_size');
            $categories = $request->input('category_ids');
            $completionStatus = $request->input('completion_status');
            $note = $request->input('note');
            $for = $request->input('for');
            $locations = $request->input('locations');
            $furnishedListReq = $request->input('furnished');
            $views = $request->input('views');
            if(is_array($views)){
                $views = implode(', ', $views);
            }


            $furnishedListReq = array_map(function ($item) {
                return ucwords(str_replace('-', '-', $item));
            }, $furnishedListReq);
            $furnishedListReq = implode(', ', $furnishedListReq);

            $bedReq = array();
            if(count($beds) > 0){
                $bedReq = Bed::whereIn('id', $beds)->pluck('name')->toArray();
            }
            $bedReq = implode(', ', $bedReq);
            $bathReq = array();
            if(count($baths) > 0){
                $bathReq = Bath::whereIn('id', $baths)->pluck('name')->toArray();
            }
            $bathReq = implode(', ', $bathReq);

            $categoriesReq = array();
            if(count($categories) > 0){
                $categoriesReq = PropertyCategory::whereIn('id', $categories)->pluck('title')->toArray();
            }
            $categoriesReq = implode(', ', $categoriesReq);

            $locationReq = [];
            if (count($locations) > 0) {
                foreach ($locations as $location) {
                    if ($location['type'] == 'community') {
                        $community = Community::select('name')->find($location['id']);
                        if ($community) $locationReq[] = $community->name;
                    } elseif ($location['type'] == 'sub_community') {
                        $subCommunity = SubCommunity::select('name')->find($location['id']);
                        if ($subCommunity) $locationReq[] = $subCommunity->name;
                    } elseif ($location['type'] == 'community_property') {
                        $communityProperty = CommunityProperty::select('name')->find($location['id']);
                        if ($communityProperty) $locationReq[] = $communityProperty->name;
                    }
                }
            }
            $locationReq = implode(', ', $locationReq);

            $message = "<b>Client Requirement Inquiry</b> \n\n";
            $message .= "Good day,\n\n";
            $message .= "I have a client with the following requirement. Kindly let me know if you have anything that matches or is similar\n\n";
            $message .= " 🏢 <b>Type:</b> " . (!empty($categoriesReq) ? Str::title($categoriesReq) : 'Not Specified') . "\n\n";
            $message .= " 🏷️ <b>Purpose:</b> " . (!empty($for) ? Str::title($for) : 'Not Specified') . "\n\n";
            $message .= " 📍 <b>Location:</b> " . (!empty($locationReq) ? Str::title($locationReq) : 'Not Specified') . "\n\n";
            $message .= " 🛏 <b>Bed:</b> " . (!empty($bedReq) ? Str::title($bedReq) : 'Not Specified') . "\n\n";
            $message .= " 🛁 <b>Bath:</b> " . (!empty($bathReq) ? Str::title($bathReq) : 'Not Specified') . "\n\n";
            $message .= " 🪑 <b>Furnishing:</b> " . (!empty($furnishedListReq) ? Str::title($furnishedListReq) : 'Not Specified') . "\n\n";
            $message .= " 👀 <b>View:</b> " . (!empty($views) ? Str::title($views) : 'Not Specified') . "\n\n";

            if (is_numeric($minSize) && is_numeric($maxSize)) {
                $minSizeFormatted = number_format($minSize);
                $maxSizeFormatted = number_format($maxSize);

                $areaText = $minSize > 0 ? "$minSizeFormatted - $maxSizeFormatted sqft" : "$maxSizeFormatted sqft";
                $message .= " 📏 <b>Area:</b> $areaText\n\n";
            } elseif (is_numeric($maxSize)) {
                $message .= " 📏 <b>Area:</b> " . number_format($maxSize) . " sqft\n\n";
            } elseif (is_numeric($minSize)) {
                $message .= " 📏 <b>Area:</b> " . number_format($minSize) . " sqft\n\n";
            } else {
                $message .= " 📏 <b>Area:</b> Not specified\n\n";
            }
            if (is_numeric($minPrice) && is_numeric($maxPrice)) {
                $minPriceFormatted = number_format($minPrice);
                $maxPriceFormatted = number_format($maxPrice);

                $priceText = $minPrice > 0 ? "AED $minPriceFormatted - AED $maxPriceFormatted" : "AED $maxPriceFormatted";
                $message .= " 💰 <b>Price:</b> $priceText\n\n";
            } elseif (is_numeric($maxPrice)) {
                $message .= " 💰 <b>Price:</b> AED " . number_format($maxPrice) . "\n\n";
            } elseif (is_numeric($minPrice)) {
                $message .= " 💰 <b>Price:</b> AED " . number_format($minPrice) . "\n\n";
            } else {
                $message .= " 💰 <b>Price: Not specified\n";
            }
            if($for == 'buy'){
                $message .= "🔑 <b>Buyer Type:</b> " . (!empty($buyerType) ? Str::title($buyerType) : 'Not Specified') . "\n\n";
                $message .= "⏳ <b>Buyer Timeline:</b> " . (!empty($buyerTimeline) ? Str::title($buyerTimeline) : 'Not Specified') . "\n\n";
            }
            if($for == 'rent'){
                $message .= "⏳ <b>Tenant Timeline:</b> " . (!empty($buyerTimeline) ? $buyerTimeline : 'Not Specified') . "\n\n";
            }

            if (!empty($note)) {
                $message .= "\n<b>Additional Note:</b> $note\n\n";
            }

            // $message .= "Posted By <b><a href=\"tg://user?id=$useTelegramId\">$userName</a> </b> | Mobile: <a href=\"tel:$userMobile\">$userMobile</a>";

            if ($userTelegramName) {
                $message .= "Posted By <b><a href=\"https://t.me/$userTelegramName\">@$userTelegramName</a></b>";
            } else {
                $message .= "Posted By <b>$userName</b>";
            }
            $message .= " | Mobile: <a href=\"tel:$userMobile\">$userMobile</a>";


            $chatId = config("telegram.bots.matching_bot.super_group_chat_id");
            $this->sendMessageIntoGroup($chatId, $message, messageThreadId:$groupTopic);

            return $this->sendResponse(true, 'Your requirement posted in the group successfully.', $message, 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }
    public function sendTempListingIntoGroup($propertyId, $group_topic = null)
    {
        try{
            $property = TempProperty::find($propertyId);
            if($property){
                $agentName = $property?->agent?->name;
                $agentTelegramName = $property?->agent?->telegram_username;
                $agentTelegramId = $property?->agent?->telegram_chatid;
                $agentMobile = $property?->agent?->mobile;

                $url = config('app.url') . "/front/auth/listing-details/{$property->id}";

                $message = "<b> New Listing </b>\n\n";
                $message .= " Good day,\n\n";
                $message .= " I have a listing available below. Please let me know if you have any clients looking for something similar.\n\n";
                $message .= " 🏠 <b>Title: </b>{$property->title}\n\n";
                $message .= " 🏷️ <b>Purpose:</b> " .Str::title($property->for). "\n\n";
                $message .= " 🏢 <b>Type:</b> " . Str::title($property?->category) . "\n\n";
                $message .= " 📍 <b>Location:</b> " .(!empty($property?->location) ? $property?->location : 'Not Specified')  . "\n\n";
                $message .= " 🛏 <b>Bed:</b> " . (!empty($property?->bed?->name) ? Str::title($property?->bed?->name) : 'Not Specified') . "\n\n";
                $message .= " 🛁 <b>Bath:</b> " . (!empty($property?->bath?->name) ? Str::title($property?->bath?->name) : 'Not Specified'). "\n\n";
                $message .= " 🪑 <b>Furnishing:</b> " . (!empty($property?->furnished) ? Str::title($property?->furnished) : 'Not Specified') . "\n\n";
                $views = is_array($property->view) ? $property->view : json_decode($property->view, true);
                $message .= " 👀 <b>View:</b> " . (!empty($views) ? implode(', ', $views) : 'Not Specified') . "\n\n";
                $message .= " 📏 <b>Area:</b> " .(!empty($property?->built_up_area) ? $property?->built_up_area. " sqft" : 'Not Specified')  . "\n\n";
                $message .= " 💰 <b>Price (AED):</b> " .(!empty($property?->down_payment_price) ? number_format($property?->down_payment_price) : 'Not specified')  . "\n\n";
                // $message .= "Agent <b><a href=\"tg://user?id=$agentTelegramId\">$agentName</a> </b> | Mobile: <a href=\"tel:$agentMobile\">$agentMobile</a>";


                if ($agentTelegramName) {
                    $message .= "Agent <b><a href=\"https://t.me/$agentTelegramName\">@$agentName</a></b>";
                } else {
                    $message .= "Posted By <b>$agentName</b>";
                }
                $message .= " | Mobile: <a href=\"tel:$agentMobile\">$agentMobile</a>";                    
                
                $message .= "\n\n";


                $chatId = config("telegram.bots.matching_bot.super_group_chat_id");

                $this->sendMessageIntoGroup($chatId, $message, 'https://secondary.axcrm.ae/images/defaultListing.png',botName:'notification_bot', messageThreadId: $group_topic);
            }else{
                return $this->sendResponse(true, 'Temp Listing not found', null, 200);
                Log::channel('bitrix_alert')->info( "Listing not found $propertyId" );
            }

        } catch (\Exception $e) {
            Log::channel('bitrix_alert')->info(
                "Error sendTempListingIntoGroup",[
                    'status' => false,
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            );
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }
    public function searchMyListing(Request $request)
    {
        try {

            $data = $request->all();
            $validator = Validator::make($data, [
                "telegram_chat_id" => "required|exists:users,telegram_chatid",
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $perPage = request('per_page', 10);
            $page = request('page', 1);
            $searchKeywords = $request->input('search_keywords');
            $agentId = $request->input('agent_id');
            //$telegramChatId = $request->input('telegram_chat_id') 6085218419;
            $telegramChatId = null ;

            $query = Property::with('community:id,name,path_name', 'subCommunity:id,name,path_name', 'communityProperty:id,name,path_name')
                ->whereHas('photos');

            $query->when($searchKeywords, function ($q) use ($searchKeywords) {

                    $q->where(function ($qry) use ($searchKeywords) {
                        $qry->where(DB::raw('lower(ref_no)'), 'like', '%' . strtolower($searchKeywords) . '%')
                        ->orWhere(DB::raw('lower(title)'), 'like', '%' . strtolower($searchKeywords) . '%')
                        ->orWhere(DB::raw('lower(unit_no)'), 'like', '%' . strtolower($searchKeywords) . '%')
                        ->orWhereHas('communityProperty', function ($q2) use ($searchKeywords) {
                            $q2->where(DB::raw('lower(path_name)'), 'like', "%$searchKeywords%");
                        })
                        ->orWhereHas('subCommunity', function ($q2) use ($searchKeywords) {
                            $q2->where(DB::raw('lower(path_name)'), 'like', "%$searchKeywords%");
                        })
                        ->orWhereHas('community', function ($q2) use ($searchKeywords) {
                            $q2->where(DB::raw('lower(path_name)'), 'like', "%$searchKeywords%");
                        });
                    });
                })->when($agentId, function ($query) use ($agentId) {
                    $query->where('agent_id', $agentId);
                })->when($telegramChatId, function ($query) use ($telegramChatId) {
                    $query->whereHas('agent', function ($query) use ($telegramChatId) {
                        $query->where('telegram_chatid', $telegramChatId);
                    });
                })->whereIn('publish_status', ['PUBLISHED', 'POCKET']);



            $data = $query->orderBy('created_at', 'desc')->paginate($perPage, ['*'], 'page', $page);

            $data = MatchingListingResource::collection($data);
            return $this->sendPaginationResponse($data, 'Data fetched', $page, $perPage, 'Matching Listings', $data->total());

        }catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred. Please try again' . json_encode($e->getMessage() . ' file: ' . $e->getFile()),
            ], 500);
        }
    }
    public function createListing(Request $request)
    {
        try {
            $data = $request->all();
            $validator = Validator::make($data, [
                'title' => 'required|string|min:35|max:50',
                'description' => 'required|string|min:50',
                'bed_id' => 'required|exists:beds,id',
                'bath_id' => 'required|exists:baths,id',
                'price' => 'required|numeric',
                'for' => ['required', Rule::in(\App\Models\Lead::FOR_SELECT)],
                'telegram_chat_id'  => 'required|integer|exists:users,telegram_chatid',
                'property_category_id' => 'required|integer|exists:property_categories,id',
                "location_type" => "required|string",
                "location_id" => "required",
                "built_up_area" => "nullable|numeric",
                "plot_size" => "nullable|numeric",
                'furnished' => ['required', Rule::in(array_keys(\App\Models\Property::FURNISHED_SELECT))],
                'view' => 'required|array',
                'view.*' => ['required', Rule::in(\App\Models\Property::VIEWS)],
                "features" => "required|array",
                'features.*' => 'required|exists:property_amenities,id',
                'parking' => 'required|numeric',
                'group_topic' => "nullable|numeric",
            ]);

            if ($validator->fails()) {

                return response()->json(
                    [
                        'errors' => $validator->errors(),
                        'message' => $validator->errors()->all(),
                    ], 422);
            }

            $property = new TempProperty();

            $property->title = $data['title'];
            $property->description = $data['description'];
            $property->bed_id = $data['bed_id'];
            $property->bath_id = $data['bath_id'];
            $property->down_payment_price = $data['price'];
            $property->for = $data['for'];
            $property->furnished = $data['furnished'];
            $property->parking = $data['parking'];
            $property->property_category_id = $data['property_category_id'];
            $property->view = json_encode($data['view']);

            if($data['telegram_chat_id']){
                $agent = User::where('telegram_chatid',$data['telegram_chat_id'])->first();
                $property->agent_id = $agent->id;
                $property->created_by_id = $agent->id;
            }

            if ($data['location_type'] == "community_property") {
                $communityProperty = CommunityProperty::find(str_replace("?community_property", "", $request->location_id));
                $community_property_id = $communityProperty->id ?? null;
                $sub_community_id = $communityProperty->sub_community_id ?? null;
                $community_id = $communityProperty->community_id ?? null;
            } else {

                $subCommunity = SubCommunity::find(str_replace("?sub_community", "", $request->location_id));
                $community_property_id = null;
                $sub_community_id = $subCommunity->id ?? null;
                $community_id = $subCommunity->community_id ?? null;
            }

            $property->community_property_id = $community_property_id;
            $property->sub_community_id = $sub_community_id;
            $property->community_id = $community_id;
            $property->built_up_area = $data['built_up_area'];
            $property->plot_size = $data['plot_size'];
            $property->furnished = $data['furnished'];
            $property->view = json_encode($data['view']);
            $property->publish_status = 'POCKET'; // DRAFT,  POCKET


            $property->ref_no = generateTempPropertyRefNo();
            $saved = $property->withoutApproval()->save();

            $objp = new TempPropertyPrice();
            $objp->temp_property_id = $property->id;
            $objp->price = str_replace(",", "", $property->down_payment_price);
            $objp->price_on_application = 0;
            $objp->withoutApproval()->save();

            if (!$saved) {

                return response()->json([
                    'status' => false,
                    'message' => 'Property failed to save.'
                ], 500);
            }

            foreach ($data['features'] ?? [] as $feature) {
                TempPropertyFeatures::create([
                    "temp_property_id" => $property->id,
                    "property_amenity_id" => $feature,
                    "created_by_id" => $property->created_by_id,
                ]);
            }
            $this->sendTempListingIntoGroup($property->id, $request->group_topic);
            return response()->json([
                'status' => true,
                'message' => 'Property created successfully',
                'property' => $property
            ], 200);

        }  catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Unexpected error: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }
    public function listingDetail(Request $request)
    {
        try {
            $property = Property::find($request->id);
            if (!$property) {
                return response()->json([
                    'status' => false,
                    'message' => 'Property not found'
                ], 404);
            }
            $property->load([
                'agent',
                'community:id,name,path_name',
                'subCommunity:id,name,path_name',
                'communityProperty:id,name,path_name',
                'photos',
                'bed:id,name',
                'price:id,property_id,price',
                'bath:id,name',
                'propertyCategory:id,title',
                'feature'
            ]);
            return response()->json([
                'status' => true,
                'message' => 'Property details fetched successfully',
                'data' => $property
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Unexpected error: ' . $e->getMessage(),
                'file' => $e->getFile(),
            ], 500);
        }
    }
    public function sendListingsToUser(Request $request)
    {
        try{
            $data = $request->all();
            $validator = Validator::make($data, [
                "ids" => "required|array",
                "ids.*" => "required|exists:properties,id",
                //"listings" => "required|array",
                //"listings.*.id" => "required|exists:properties,id",
                "telegram_chat_id" => "required|exists:users,telegram_chatid",
                'via' => ['required', Rule::in(['telegram', 'email'])],
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $user = User::where('telegram_chatid', $data['telegram_chat_id'])->first();
            $via = $data['via'];
            if($user) {
                if($via == "telegram"){
                    $this->sendListingsViaTelegram($data['ids'], $user);
                }elseif($data['via'] == "email"){
                    $this->sendListingsViaEmail($data['ids'], $user);
                }

                return $this->sendResponse(true, "Listing results sent to your $via successfully", null, 200);
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'User not found',
                ], 500);
            }
        }catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'message' => 'An error occurred. Please try again' . json_encode($e->getMessage() . ' file: ' . $e->getFile()),
            ], 500);
        }
    }
    public function sendListingsViaTelegram($listingIds, $user)
    {
        try{
            foreach($listingIds as $listingId) {
                $property = Property::with(['bed', 'bath', 'agent'])->find($listingId);
                if($property){
                    $agentName = $property?->agent?->name;
                    $agentTelegramName = $property?->agent?->telegram_username;
                    $agentTelegramId = $property?->agent?->telegram_chatid;
                    $agentMobile = $property?->agent?->mobile;

                    $url = config('app.url') . "/front/auth/listing-details/{$property->id}";
                    $message = "\n\n";
                    //$message .= " 🏠 <b>Title:</b> <a href=\"$url\">{$property->title}</a>\n\n";
                    $message .= " <a href=\"$url\">{$property->title}</a>\n\n";

                    $message .= " 🏷️ <b>Purpose:</b> " .Str::title($property->for). "\n\n";
                    $message .= " 🏢 <b>Type:</b> " . Str::title($property?->category) . "\n\n";
                    $message .= " 📍 <b>Location:</b> " .(!empty($property?->location) ? $property?->location : 'Not Specified')  . "\n\n";
                    $message .= " 🛏 <b>Bed:</b> " . (!empty($property?->bed?->name) ? Str::title($property?->bed?->name) : 'Not Specified') . "\n\n";
                    $message .= " 🛁 <b>Bath:</b> " . (!empty($property?->bath?->name) ? Str::title($property?->bath?->name) : 'Not Specified'). "\n\n";
                    $message .= " 🪑 <b>Furnishing:</b> " . (!empty($property?->furnished) ? Str::title($property?->furnished) : 'Not Specified') . "\n\n";
                    $views = is_array($property->view) ? $property->view : json_decode($property->view, true);
                    $message .= " 👀 <b>View:</b> " . (!empty($views) ? implode(', ', $views) : 'Not Specified') . "\n\n";
                    $message .= " 📏 <b>Area:</b> " .(!empty($property?->built_up_area) ? $property?->built_up_area : 'Not Specified')  . "\n\n";
                    $message .= " 💰 <b>Price (AED):</b> " .(!empty($property?->down_payment_price) ? number_format($property?->down_payment_price) : 'Not Specified')  . "\n\n";
                    //$message .= "Agent <b><a href=\"tg://user?id=$agentTelegramId\">$agentName</a> </b> | Mobile: <a href=\"tel:$agentMobile\">$agentMobile</a>";
                    // if ($agentTelegramName) {
                    //     $agentLink = "<a href=\"https://t.me/{$agentTelegramName}\">{$agentName}</a>";
                    // } elseif ($agentTelegramId) {
                    //     $agentLink = "<a href=\"tg://user?id={$agentTelegramId}\">{$agentName}</a>";
                    // } else {
                    //     $agentLink = $agentName;
                    // }
                    
                    // $mobileLink = "<a href=\"tel:{$agentMobile}\">{$agentMobile}</a>";
                    // $message .= "Agent <b>{$agentLink}</b> | Mobile: {$mobileLink}";

                    if ($agentTelegramName) {
                        $message .= "Agent <b><a href=\"https://t.me/$agentTelegramName\">@$agentName</a></b>";
                    } else {
                        $message .= "Agent <b>$agentName</b>";
                    }
                    $message .= " | Mobile: <a href=\"tel:$agentMobile\">$agentMobile</a>";                    
                    $message .= "\n\n";

                    $url =  $property->photos->first() ? $property->photos->first()->path : 'https://secondary.axcrm.ae/images/defaultListing.png';
                    
                    $this->sendTelegramMessageWithAttachment($message, [$user->telegram_chatid], $url, 'notification_bot'); // 1417490671
                }
            }

        }catch (\Throwable $exception) {

            Log::channel('bitrix_alert')->error('Error sending listings telegrams', [
                'error' => $exception->getMessage(),
                'line' => $exception->getLine(),
                'file' => $exception->getFile()
            ]);
        }
    }
    public function sendListingsViaEmail($listingIds ,$user)
    {
        try{
            if($user) {

                $propertyIds = $listingIds;
                $properties = Property::with(['bed', 'bath', 'agent'])->whereIn("id", $propertyIds)->get();

                MatchListingsJob::dispatch($user, $properties);
                return $this->sendResponse(true, 'Listing results sent to your email successfully', null, 200);
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'User not found',
                ], 500);
            }
        }catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred. Please try again' . json_encode($e->getMessage() . ' file: ' . $e->getFile()),
            ], 500);
        }
    }
    public function sendListingsToTelegramGroup(Request $request)
    {
        try{

            $data = $request->all();
            $validator = Validator::make($data, [
                "listings" => "required|array",
                "listings.*.id" => "required|exists:properties,id",
                "telegram_chat_id" => "required|exists:users,telegram_chatid",
                'group_topic' => "nullable|numeric",
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $user = User::where('telegram_chatid', $data['telegram_chat_id'])->first();

            foreach($data['listings'] as $listing) {
                $property = Property::with(['bed', 'bath', 'agent'])->where('id', $listing['id'])->where('agent_id', $user->id)->first();
                //$property = Property::with(['bed', 'bath', 'agent'])->where('id', $listing['id'])->first();
                if($property){
                    $agentName = $property?->agent?->name;
                    $agentTelegramName = $property?->agent?->telegram_username;
                    $agentTelegramId = $property?->agent?->telegram_chatid;
                    $agentMobile = $property?->agent?->mobile;

                    $url = config('app.url') . "/front/auth/listing-details/{$property->id}";
                    $message = "\n\n";

                    $message = "<b> New Listing </b>\n\n";
                    $message .= " Good day,\n\n";
                    $message .= " I have a listing available below. Please let me know if you have any clients looking for something similar.\n\n";

                    $message .= " <a href=\"$url\">{$property->title}</a>\n\n";
                    $message .= " 🏷️ <b>Purpose:</b> " .Str::title($property->for). "\n\n";
                    $message .= " 🏢 <b>Type:</b> " . Str::title($property?->category) . "\n\n";
                    $message .= " 📍 <b>Location:</b> " .(!empty($property?->location) ? $property?->location : 'Not Specified')  . "\n\n";
                    $message .= " 🛏 <b>Bed:</b> " . (!empty($property?->bed?->name) ? Str::title($property?->bed?->name) : 'Not Specified') . "\n\n";
                    $message .= " 🛁 <b>Bath:</b> " . (!empty($property?->bath?->name) ? Str::title($property?->bath?->name) : 'Not Specified'). "\n\n";
                    $message .= " 🪑 <b>Furnishing:</b> " . (!empty($property?->furnished) ? Str::title($property?->furnished) : 'Not Specified') . "\n\n";
                    $views = is_array($property->view) ? $property->view : json_decode($property->view, true);
                    $message .= " 👀 <b>View:</b> " . (!empty($views) ? implode(', ', $views) : 'Not Specified') . "\n\n";
                    $message .= " 📏 <b>Area:</b> " .(!empty($property?->built_up_area) ? $property?->built_up_area : 'Not Specified')  . "\n\n";
                    $message .= " 💰 <b>Price (AED):</b> " .(!empty($property?->price?->price) ? number_format($property?->price?->price) : 'Not Specified')  . "\n\n";
            
                    if ($agentTelegramName) {
                        $message .= "Posted By <b><a href=\"https://t.me/$agentTelegramName\">@$agentName</a></b>";
                    } else {
                        $message .= "Posted By <b>$agentName</b>";
                    }
                    $message .= " | Mobile: <a href=\"tel:$agentMobile\">$agentMobile</a>";                    
                    
                    $message .= "\n\n";

                    $url =  $property->photos->first() ? $property->photos->first()->path : 'https://secondary.axcrm.ae/images/defaultListing.png';

                    $chatId = config("telegram.bots.matching_bot.super_group_chat_id");
                    $this->sendMessageIntoGroup($chatId, $message, $url, botName:'notification_bot', messageThreadId: $data['group_topic']);
                    
                }else{
                    Log::info("Property not found for ID: {$listing['id']}", [
                        'user_id' => $user->id,
                        'telegram_chat_id' => $data['telegram_chat_id']
                    ]);
                }
            }

            return $this->sendResponse(true, "Listing results sent to group successfully", null, 200);

        } catch (\Throwable $exception) {

            Log::channel('bitrix_alert')->error('Error sending listings telegram group', [
                'error' => $exception->getMessage(),
                'line' => $exception->getLine(),
                'file' => $exception->getFile()
            ]);
        }
    }
}
