<?php

namespace App\Services\Portal;

use App\Jobs\PFV2CheckListingStateJob;
use App\Models\Community;
use App\Models\CommunityProperty;
use App\Models\ListingVerification;
use App\Models\Portal;
use App\Models\PortalListingTimeline;
use App\Models\Property;
use App\Models\PropertyPortal;
use App\Models\Region;
use App\Models\SubCommunity;
use App\Models\User;
use App\Traits\TelegramTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use App\Traits\PropertyFinderTrait;
use Illuminate\Support\Facades\Log;
use App\Jobs\PFV2CreateListingJob;

class PropertyFinderService
{
    use PropertyFinderTrait;
    use TelegramTrait;

    private string $pfBaseUrl = "https://atlas.propertyfinder.com/v1";
    private string $listingBaseUrl = "https://atlas.propertyfinder.com/v1/listings";
    private string $userBaseUrl = "https://atlas.propertyfinder.com/v1/users";
    private string $listingVerificationBaseUrl = 'https://atlas.propertyfinder.com/v1/listing-verifications';
    private string $locationBaseUrl = 'https://atlas.propertyfinder.com/v1/locations';
    private string $licenseNumber = "813677";  // corporate license number
    private string $publicProfileBaseUrl = "https://atlas.propertyfinder.com/v1/public-profiles";  // corporate license number

    public function sendDebugTelegramMessage($data, $message, $additionalParams = []){
        $message .= "\n\n";

        $message .= "file: " . self::class. "\n";

        foreach ($additionalParams as $key => $value) {
            $message .= $key . ": " . $value . "\n";
        }

        foreach ($data as $index => $item) {
            $message .= "$item \n";
        }
        $this->sendTelegramMessage($message, [1253659350, 795165330]);
    }


    public function pfCategories($for, $value = null): bool
    {
        $residential_property_types_allowed = ['apartment', 'bulk-rent-unit', 'bulk-sale-unit', 'bungalow', 'compound', 'duplex', 'full-floor', 'half-floor', 'hotel-apartment', 'land', 'penthouse', 'townhouse', 'villa', 'whole-building'];
        $commercial_property_types_allowed = ['bulk-rent-unit', 'bulk-sale-unit', 'business-center', 'co-working-space', 'factory', 'farm', 'full-floor', 'half-floor', 'labor-camp', 'land', 'office-space', 'retail', 'shop', 'show-room', 'staff-accommodation', 'villa', 'warehouse', 'whole-building'];

        if ($for == 'residential') {
            $array = $residential_property_types_allowed;
        } else if ($for == 'commercial') {
            $array = $commercial_property_types_allowed;
        } else {
            return false;
        }

        if (in_array($value, $array)) {
            return true;
        } else {
            return false;
        }
    }

    private function getPFType($for, $value)
    {
        if ($for == 'Residential for sale' || $for == 'Residential for rent') {
            if ($value == "Bulk Units"){
                return "bulk-sale-unit";
            }
        } else if ($for == 'Commercial for sale' || $for == 'Commercial for rent') {
            if ($value == "Bulk Units"){
                return "bulk-rent-unit";
            }
        }
    }

    private function getPFLocationIdByProperty($property, $entity = "secondary")
    {
        $name = null;
        $location = null;
        $locationId = null;
//        if ($property->community_property_id) {
//            $location = CommunityProperty::find($property->community_property_id);
//            $name = $location?->name;
//            if ($location && $location->portal_ids && $location->default_use) { // Check if the location has default_use only in this condition it will be used otherwise sub-community or community will be used
//                $portalIds = json_decode($location->portal_ids, true);
//                $locationId = $portalIds['property_finder_id'] ?? null;
//            }
//        }
//        if ($property->sub_community_id && $locationId == null) {
//            $location = SubCommunity::find($property->sub_community_id);
//            $name = $location?->name;
//            if ($location && $location->portal_ids) {
//                $portalIds = json_decode($location->portal_ids, true);
//                $locationId = $portalIds['property_finder_id'] ?? null;
//            }
//        }
//        if ($property->community_id && $locationId == null) {
//            $location = Community::find($property->community_id);
//            $name = $location?->name;
//            if ($location && $location->portal_ids) {
//                $portalIds = json_decode($location->portal_ids, true);
//                $locationId = $portalIds['property_finder_id'] ?? null;
//            }
//        }


        if ($property->community_property_id) {
            $location = CommunityProperty::find($property->community_property_id);
            $name = $location?->name;
            if ($location && $location->portal_ids && $location->default_use) {
                $portalIds = json_decode($location->portal_ids, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($portalIds)) {
                    $locationId = $portalIds['property_finder_id'] ?? null;
                }
            }
        }

        if ($property->sub_community_id && $locationId == null) {
            $location = SubCommunity::find($property->sub_community_id);
            $name = $location?->name;
            if ($location && $location->portal_ids) {
                $portalIds = json_decode($location->portal_ids, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($portalIds)) {
                    $locationId = $portalIds['property_finder_id'] ?? null;
                }
            }
        }

        if ($property->community_id && $locationId == null) {
            $location = Community::find($property->community_id);
            $name = $location?->name;
            if ($location && $location->portal_ids) {
                $portalIds = json_decode($location->portal_ids, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($portalIds)) {
                    $locationId = $portalIds['property_finder_id'] ?? null;
                }
            }
        }




        if (!empty($locationId)) return $locationId;

        if ($name) {
            $data = $this->findLocation($name, $entity);
            if (!empty($data)) {
                $locationId = collect($data['tree'])->firstWhere('name', $name)['id'] ?? null;
                if ($locationId) {
                    if ($location->portal_ids == null) {
                        $portal_data = [];
                    } else {
                        $portal_data = json_decode($location->portal_ids, true);
                    }
                    $portal_data['property_finder_id'] = $data['id'];
                    $location->update(['portal_ids' => json_encode($portal_data)]);
                }
            }
        }
        return $locationId;
    }

    private function validateCreateUpdatePayload(array $data, string $path = ''): array
    {
        $errors = [];

        foreach ($data as $key => $value) {
            $currentPath = $path === '' ? $key : $path . '.' . $key;
            if (is_array($value)) {
                $nestedErrors = $this->validateCreateUpdatePayload($value, $currentPath);
                $errors = array_merge($errors, $nestedErrors);
            } else {
               if ($value === null) {
                    $errors[] = "The field '$currentPath' cannot be null.";
                }elseif( (is_string($value) && trim($value) === '')){
                    $errors[] = "The field '$currentPath' cannot be empty";
                }
            }
        }

        return $errors;
    }

    public function preparePayloadForCreateAndUpdateListing($property, $isCreate = true): array
    {
        $mainPortal = Portal::where("short_name", "PF")->latest()->first();

        $fields = [
            'category' => null,
            'furnishingType' => 'unfurnished',
            'price' => [
                'type' => null,
                'amounts' => []
            ],
            'location' => [
                'id' => null
            ],
            'uaeEmirate' => 'northern_emirates',
            'reference' => (string)$property->ref_no,
            'title' => [
                'en' => $property->title ?? null
            ],
            "description" => [
                "en" => $property->description ?? null
            ],
            'size' => (int)$property->built_up_area ?? 0,
        ];

        if ($property->for == 'Residential for sale' || $property->for == 'Residential for rent') {
            $fields['category'] = 'residential';
        } else if ($property->for == 'Commercial for sale' || $property->for == 'Commercial for rent') {
            $fields['category'] = 'commercial';
        }

        $region = Region::where('id', $property->region_id)->first();
        if (!empty($region)) {
            if ($region->name == 'Dubai') {
                $fields['uaeEmirate'] = 'dubai';
            } else if ($region->name == 'Abu Dhabi') {
                $fields['uaeEmirate'] = 'abu_dhabi';
            }
        }

        if ($fields['uaeEmirate'] == 'dubai' || $fields['uaeEmirate'] == 'abu_dhabi') {
            $fields['compliance'] = [
                'advertisementLicenseIssuanceDate',
                'listingAdvertisementNumber',
                'type'
            ];
        }

        if ($property->completion_status == 'Off-Plan Primary') {
            $fields['projectStatus'] = 'off_plan_primary';
            $fields['finishingType'] = 'unfinished';
        } else if ($property->completion_status == 'Off-Plan Secondary') {
            $fields['projectStatus'] = 'off_plan';
            $fields['finishingType'] = 'fully-finished';
        } else if ($property->completion_status == 'Ready Primary') {
            $fields['projectStatus'] = 'completed_primary';
            $fields['finishingType'] = 'fully-finished';
        } else if ($property->completion_status == 'Ready Secondary') {
            $fields['projectStatus'] = 'completed';
            $fields['finishingType'] = 'fully-finished';
        }

        if (!empty($property?->propertyCategory)){
            $fields['type'] = $this->getPFType($property->for, $property?->propertyCategory);
            if (empty($fields['type'])) {
                if (!empty($property?->propertyCategory?->portals[0]->title)){
                    $fields['type'] = $this->pfCategories($fields['category'], $property?->propertyCategory?->portals[0]->title) ? $property?->propertyCategory?->portals[0]->title : null;
                }
            }
        }

        if ($property->photos) {
            $imageIndex = 0; // Use a separate counter to ensure consecutive indices
            foreach ($property->photos as $photo) {
                if (!empty($photo->original)) {
                    $fields["media"]["images"][$imageIndex]["original"] = [
                        "height" => 900,
                        "url" => $photo->original,
                        "width" => 1200
                    ];

                    if (!empty($photo->path)) {
                        $fields["media"]["images"][$imageIndex]['large'] = [
                            "height" => 900,
                            "url" => $photo->path,
                            "width" => 1200
                        ];
                        $fields["media"]["images"][$imageIndex]['watermarked'] = [
                            "height" => 900,
                            "url" => $photo->path,
                            "width" => 1200
                        ];
                    }

                    if (!empty($photo->thumbnail)) {
                        $fields["media"]["images"][$imageIndex]['thumbnail'] = [
                            "height" => 0,
                            "url" => $photo->thumbnail ?? '',
                            "width" => 0
                        ];
                    }

                    $imageIndex++; // Only increment when we actually add an image
                }
            }

            // Ensure it's always a numeric array
            if (isset($fields["media"]["images"])) {
                $fields["media"]["images"] = array_values($fields["media"]["images"]);
            }
        }


        if ($property->rera_permit_number != '' && $property->rera_permit_number != null) {

            $fields['compliance'] = [
                "listingAdvertisementNumber" => (string)$property->rera_permit_number ?? "",
                "type" => $property->region_id == '2' ? "adrec" : "rera",
                "userConfirmedDataIsCorrect" => true
            ];
            if (!empty($property->rera_issue_date)){
                $fields["compliance"] = [
                    "listingAdvertisementNumber" => (string)$property->rera_permit_number ?? "",
                    "type" => $property->region_id == '2' ? "adrec" : "rera",
                    "advertisementLicenseIssuanceDate" => $property->rera_issue_date . 'T00:00:00Z',
                    "userConfirmedDataIsCorrect" => true
                ];
            }
        }

        $gardens = ['Landscaped Garden', 'Private garden', 'Communal gardens', 'Garden view'];
        $kitchens = ['Fully fitted kitchen', 'Kitchen Appliances'];

        $type = strtolower($property?->propertyCategory?->title ?? '');
        $form_land = str_contains($type, "land") || str_contains($type, "farm");

        if (!$form_land) {
            if (!empty($property->amenities) && is_array($property->amenities)) {
                foreach ($property["amenities"] ?? [] as $amenity) {
                    if ($amenity["amenity"]["title"] == "Garden" || in_array($amenity["amenity"]["title"] ?? "", $gardens)) {
                        $fields["hasGarden"] = true;
                    }
                    if ($amenity["amenity"]["title"] == "Kitchen" || in_array($amenity["amenity"]["title"] ?? "", $kitchens)) {
                        $fields["hasKitchen"] = true;
                    }
                    if ($this->pfAmenities($amenity["amenity"]["portals"][0]["title"] ?? "", $fields['type'])) {
                        if (!empty($amenity["amenity"]["portals"][0]["title"])) {
                            $fields["amenities"][] = $amenity["amenity"]["portals"][0]["title"];
                        }
                    }
                }
            }

            $fields["bathrooms"] = (string) ($property["bath"]["portals"][0]["name"] ?? "none") ?? "none";
            $fields["bedrooms"] = (string) ($property["bed"]["portals"][0]["name"] ?? "studio") ?? "studio";
        }
        if($fields["bedrooms"] == '0'){
            //unset bedrooms if it is 0
            unset($fields["bedrooms"]);
        }
        if ($property->furnished == 'FURNISHED') {
            $fields['furnishingType'] = 'furnished';
        } else if ($property->furnished == 'SEMI-FURNISHED') {
            $fields['furnishingType'] = 'semi-furnished';
        }

        $agent = User::find($property->agent_id);

        $agentPublicProfileId = json_decode($agent->third_party_ids, true)['pf_public_profile_id'] ?? null;

        $isSale = $property->for == 'Residential for sale' || $property->for == 'Commercial for sale';

        //$price = ($property?->price?->price_on_application == 1 || $property?->price?->price_on_application == '1') ? null : ((int)$property?->price?->price ?? 0);
        $price =  (int)$property?->price?->price ?? 0;
        if ($isSale) {
            $fields["price"]["amounts"]["sale"] = $price;
            $fields["price"]["type"] = "sale";
            if ($property->payment_method != 'cash') {
                $fields["price"]["downpayment"] = (int)$property->down_payment_price ?? 0;
            }
        } else {
            $fields["price"]["amounts"]["yearly"] = $price;
            $fields["price"]["type"] = "yearly";
        }

        if (!empty($property->cheques)){
            $fields["price"]["numberOfCheques"] = (int)$property->cheques;
        }
        if (!empty($property->payment_method)){
            $fields["price"]["paymentMethods"] = explode(',', $property->payment_method);
        }

        //unset price if price_on_application is 1
        if($property?->price?->price_on_application == 1 || $property?->price?->price_on_application == '1'){
         $fields['price']['onRequest'] = true;

        }


        $location_id = $this->getPFLocationIdByProperty($property, $this->getEntityByPropertyForPFV2($property));

        if (!empty($location_id)) {
            $fields["location"]["id"] = (int)$location_id;
        }

        if ($agentPublicProfileId){
            $fields["assignedTo"] = [
                "id" => (int)$agentPublicProfileId,
            ];

            $fields["createdBy"] = [
                "id" => (int)$agentPublicProfileId,
            ];
        }

        if (!empty($property['availability_date'])){
            $fields["availableFrom"] = (string)$property['availability_date'];
        }

        if (!empty($property->developer?->short_name)){
            $fields["developer"] = (string) $property->developer?->short_name;
        }elseif (!empty($property?->details?->developer)){
            $fields["developer"] = (string)  $property?->details?->developer;
        }

        if (!empty($property->floor)){
            $fields["floorNumber"] = (string)$property->floor;
        }

        if ($property->parking > 0){
            $fields["hasParkingOnSite"] = true;
        }

        if (!empty($property->floor)){
            $fields["numberOfFloors"] = (int)$property->floor;
        }

        if(!empty($property->owner?->first_name)){
            $fields["ownerName"] = $property->owner?->first_name . ' ' . $property->owner?->last_name;
        }

        if (!empty($property->parking)){
            $parkingSlots = min($property->parking, 10); // max 10
            $fields["parkingSlots"] = (int)$parkingSlots;
        }

        if(!empty($property->plot_size)){
            $fields["plotSize"] = (int)$property->plot_size;
        }

        if (!empty($property->video_link)){
            $fields["media"]["videos"]["default"] = $property->video_link;
        }

        if (!empty($property->video_360_link)){
            $fields["media"]["videos"]["view360"] = $property->video_360_link;
        }

        if($type == "co-working-space"){
            $fields["hasParkingSpace"] = $property->parking > 0;
        }



        $criticalValidationErrors = $this->validateCreateUpdatePayload($fields);

        if (!empty($criticalValidationErrors)){
            $message = "Payload cannot be prepared to " . ($isCreate ? "create" : "update") . " listing at property finder, due to some validation errors.";
            $this->sendDebugTelegramMessage($criticalValidationErrors, $message, [
                "method" => __FUNCTION__,
                "property_id" => $property->id,
                "agent_id" => $agent->id ?? "",
                "agent_name" => $agent->name ?? "",
                "ref_no" => $property->ref_no,
            ]);

            $this->makeListingTimelineLog([
                'portal_id' => $mainPortal->id ?? null,
                'property_id' => $property->id,
                'title' => 'Validation failed before sending to Property Finder',
                'description' => $message,
                'status' => 'failed',
                'response' => $criticalValidationErrors,
                'payload' => $fields,
            ]);

            return [
                'status' => false,
                'data' => []
            ];
        }


        return [
            'status' => true,
            'data' => $fields
        ];
    }

    private function updateThirdPartyIds($userId, $data): bool
    {
        try {
            $user = User::find($userId);
            $thirdPartyIds = json_decode($user->third_party_ids, true);

            if (!empty($data['pf_v2_user_id'])) {
                $thirdPartyIds['pf_v2_user_id'] = $data['pf_v2_user_id'];
            }

            if (!empty($data['pf_private_id'])) {
                $thirdPartyIds['pf_private_id'] = $data['pf_private_id'];
            }

            if (!empty($data['pf_public_profile_id'])) {
                $thirdPartyIds['pf_public_profile_id'] = $data['pf_public_profile_id'];
            }

            $user->third_party_ids = json_encode($thirdPartyIds);
            $user->save();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function updateOrCreateVerification($data): void
    {
        try {
            $mainPortal = Portal::where("short_name", "PF")->latest()->first();
            $listingVerification = ListingVerification::where('submission_id', $data['id'])->first();
            if (!$listingVerification) {
                $listingVerification = new ListingVerification();
            }

            $propertyId = Property::where('portal_ids->property_finder_id', $data['listingId'])->first()->id ?? null;

            if (!$propertyId) {
                $propertyId = Property::where('ref_no', $data['listingReference'])->first()->id ?? null;
            }

            $userId = User::where('third_party_ids->pf_public_profile_id', $data['publicProfileId'])->first()->id ?? null;
            $portalId = Portal::where("short_name", "PF")->latest()->first()->id ?? null;

            $listingVerification->property_id = $propertyId;
            $listingVerification->portal_id = $portalId;
            $listingVerification->user_id = $userId;
            $listingVerification->submission_id = $data['id'];
            $listingVerification->portal_listing_id = $data['listingId'];
            $listingVerification->portal_listing_ref_no = $data['listingReference'];
            $listingVerification->public_profile_id = $data['publicProfileId'];
            $listingVerification->status = $data['status'];
            $listingVerification->remarks = $data['rejectionReason'] ?? null;
            $listingVerification->meta = $data;
            $listingVerification->portal_created_at = !empty($data['createdAt']) ? Carbon::parse($data['createdAt'])->timezone(config('app.timezone'))->format('Y-m-d H:i:s') : null;
            $listingVerification->portal_expires_at = !empty($data['expiresAt']) ? Carbon::parse($data['expiresAt'])->timezone(config('app.timezone'))->format('Y-m-d H:i:s') : null;
            $listingVerification->portal_updated_at = !empty($data['updatedAt']) ? Carbon::parse($data['updatedAt'])->timezone(config('app.timezone'))->format('Y-m-d H:i:s') : null;
            $listingVerification->save();

//            $this->makeListingTimelineLog([
//                'portal_id' => $mainPortal->id,
//                'property_id' => $propertyId,
//                'title' => 'Imported listing verification submission from Property finder successfully.',
//                'status' => 'success',
//                'response' => $data
//            ]);

        } catch (\Exception $exception) {
            Log::error("Error occurred while creating/updating listing verification entry: " . $exception->getMessage());
        }

    }

    public function pfAmenities($value, $property_type): bool
    {
        $property_types1 = ['apartment', 'bulk-rent-unit', 'bulk-sale-unit', 'bungalow', 'compound', 'duplex', 'full-floor', 'half-floor', 'hotel-apartment', 'penthouse', 'townhouse', 'villa', 'whole-building'];
        $property_types2 = ['business-center', 'co-working-space', 'factory', 'labor-camp', 'office-space', 'retail', 'shop', 'show-room', 'staff-accommodation', 'warehouse'];
        $amenities_for_type_1 = ['central-ac', 'built-in-wardrobes', 'kitchen-appliances', 'security', 'concierge', 'maid-service', 'balcony', 'private-gym', 'shared-gym', 'private-jacuzzi', 'shared-spa', 'covered-parking', 'maids-room', 'study', 'childrens-play-area', 'pets-allowed', 'barbecue-area', 'shared-pool', 'childrens-pool', 'private-garden', 'private-pool', 'view-of-water', 'view-of-landmark', 'walk-in-closet', 'lobby-in-building', 'vastu-compliant'];
        $amenities_for_type_2 = ['shared-gym', 'covered-parking', 'networked', 'shared-pool', 'dining-in-building', 'conference-room', 'lobby-in-building', 'vastu-compliant'];
        if (in_array($property_type, $property_types1)) {
            if (in_array($value, $amenities_for_type_1)) {
                return true;
            }
        } elseif (in_array($property_type, $property_types2)) {
            if (in_array($value, $amenities_for_type_2)) {
                return true;
            }
        }
        return false;

    }


    public function makeListingTimelineLog($data)
    {
        $timeline = new PortalListingTimeline();
        $timeline->user_id = $data['user_id'] ?? null;
        $timeline->portal_id = $data['portal_id'] ?? null;
        $timeline->property_id = $data['property_id'] ?? null;
        $timeline->title = $data['title'] ?? null;
        $timeline->description = $data['description'] ?? null;
        $timeline->status = $data['status'] ?? null;
        $timeline->response = $data['response'] ?? null;
        $timeline->payload = $data['payload'] ?? null;
        $timeline->save();
    }


    /*
     * Documentation: https://api-docs.propertyfinder.net/enterprise-api/index.html#tag/Listings/operation/searchListings
     */
    public function findListing($listingId, $propertyId, $entity = 'secondary')
    {
        $mainPortal = Portal::where("short_name", "PF")->latest()->first();
        try {
            $token = $this->getPfAccessTokenV2($entity);

            $headers = [
                'Authorization' => 'Bearer ' . $token["token"],
                'Content-Type' => 'application/json',
            ];

            $params = [
                "page" => 1,
                "perPage" => 100,
                "filter[ids]" => $listingId
            ];

            $response = Http::withHeaders($headers)->get($this->listingBaseUrl, $params);

            if ($response->status() === 401) {
                $this->makeListingTimelineLog([
                    'portal_id' => $mainPortal->id,
                    'property_id' => $propertyId,
                    'title' => "Token expired while fetching listing {$listingId} from Property Finder.",
                    'status' => 'failed',
                    'response' => $response->json()
                ]);
                Log::warning("Token expired. Refreshing token...");
                $token = $this->getPfAccessTokenV2($entity, true);
                $headers['Authorization'] = 'Bearer ' . $token["token"];
                $response = Http::withHeaders($headers)->get($this->listingBaseUrl, $params);
            }

            $data = $response->json();

            if ($response->failed()) {
                $this->makeListingTimelineLog([
                    'portal_id' => $mainPortal->id,
                    'property_id' => $propertyId,
                    'title' => "Request failed while finding listing {$listingId} from property finder with Status: " . $response->status(),
                    'status' => 'failed',
                    'response' => $data,
                    'payload' => $params
                ]);
            }


            if (!isset($data['results']) || !is_array($data['results']) || empty($data['results'])) {
                $params['draft'] = true;
                $response = Http::withHeaders($headers)->get($this->listingBaseUrl, $params);
            }

            $data = $response->json();

            if (!isset($data['results']) || !is_array($data['results']) || empty($data['results'])) {
                $this->makeListingTimelineLog([
                    'portal_id' => $mainPortal->id,
                    'property_id' => $propertyId,
                    'title' => "Listing {$listingId} not found in property finder.",
                    'status' => 'failed',
                    'response' => $data,
                    'payload' => $params
                ]);
            }

            if (!empty($data['results'][0])){
                $this->makeListingTimelineLog([
                    'portal_id' => $mainPortal->id,
                    'property_id' => $propertyId,
                    'title' => "Listing {$listingId} details fetched from property finder.",
                    'status' => 'success',
                    'response' => $data,
                    'payload' => $params
                ]);
                return $data['results'][0];
            }

            return null;
        } catch (\Throwable $e) {
            $this->makeListingTimelineLog([
                'portal_id' => $mainPortal->id,
                'property_id' => $propertyId,
                'title' => "Error has occurred while finding listing with id {$listingId} from Property Finder.",
                'description' => $e->getMessage(),
                'status' => 'failed',
            ]);
            return null;
        }
    }

    public function findListingByReference($refNo, $propertyId = null, $entity = 'secondary')
    {
        $mainPortal = Portal::where("short_name", "PF")->latest()->first();
        try {
            $token = $this->getPfAccessTokenV2($entity);

            $headers = [
                'Authorization' => 'Bearer ' . $token["token"],
                'Content-Type' => 'application/json',
            ];

            $params = [
                "page" => 1,
                "perPage" => 100,
                "filter[reference]" => $refNo
            ];

            $response = Http::withHeaders($headers)->get($this->listingBaseUrl, $params);

            if ($response->status() === 401) {
                $this->makeListingTimelineLog([
                    'portal_id' => $mainPortal->id,
                    'property_id' => $propertyId,
                    'title' => "Token expired while fetching listing {$refNo} from Property Finder.",
                    'status' => 'failed',
                    'response' => $response->json()
                ]);
                Log::warning("Token expired. Refreshing token...");
                $token = $this->getPfAccessTokenV2($entity, true);
                $headers['Authorization'] = 'Bearer ' . $token["token"];
                $response = Http::withHeaders($headers)->get($this->listingBaseUrl, $params);
            }

            $data = $response->json();

            if ($response->failed()) {
                $this->makeListingTimelineLog([
                    'portal_id' => $mainPortal->id,
                    'property_id' => $propertyId,
                    'title' => "Request failed while finding listing {$refNo} from property finder with Status: " . $response->status(),
                    'status' => 'failed',
                    'response' => $data,
                    'payload' => $params
                ]);
            }


            if (!isset($data['results']) || !is_array($data['results']) || empty($data['results'])) {
                $params['draft'] = true;
                $response = Http::withHeaders($headers)->get($this->listingBaseUrl, $params);
            }

            $data = $response->json();

            if (!isset($data['results']) || !is_array($data['results']) || empty($data['results'])) {
                $this->makeListingTimelineLog([
                    'portal_id' => $mainPortal->id,
                    'property_id' => $propertyId,
                    'title' => "Listing {$refNo} not found in property finder.",
                    'status' => 'failed',
                    'response' => $data,
                    'payload' => $params
                ]);
            }

            if (!empty($data['results'][0])){
                $this->makeListingTimelineLog([
                    'portal_id' => $mainPortal->id,
                    'property_id' => $propertyId,
                    'title' => "Listing {$refNo} details fetched from property finder.",
                    'status' => 'success',
                    'response' => $data,
                    'payload' => $params
                ]);
                return $data['results'][0];
            }

            return null;
        } catch (\Throwable $e) {
            $this->makeListingTimelineLog([
                'portal_id' => $mainPortal->id,
                'property_id' => $propertyId,
                'title' => "Error has occurred while finding listing with id {$refNo} from Property Finder.",
                'description' => $e->getMessage(),
                'status' => 'failed',
            ]);
            return null;
        }
    }

    /*
     * Documentation: https://api-docs.propertyfinder.net/enterprise-api/index.html#tag/Listings/operation/createListing
     */
    public function createListing($propertyId): bool
    {
        $mainPortal = Portal::where("short_name", "PF")->latest()->first();
        try {
            $property = Property::whereHas("portals", function ($q) use ($mainPortal) {
                $q->where("portal_id", $mainPortal->id);
            })->where('id', $propertyId)->with([
                'agent' => function ($q) {
                    $q->select('id', 'name', 'first_name', 'last_name', 'pp_enabled', 'pp_name', 'pp_photo', 'pp_license_no', 'email', 'secondary_email', 'license_no', 'mobile', 'bio', 'thumbnail', 'image');
                },
                'details',
                'propertyCategory' => function ($q) {
                    $q->select('id', 'title');
                },
                'propertyCategory.portals' => function ($q) use ($mainPortal) {
                    $q->where('portal_id', $mainPortal->id)->select('id', 'title', 'property_category_id', 'portal_id');
                },
                'bed' => function ($q) {
                    $q->select('id');
                },
                'bed.portals' => function ($q) use ($mainPortal) {
                    $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bed_id', 'portal_id');
                },
                'developer',
                'price' => function ($q) {
                    $q->select('id', 'property_id', 'price', 'prices', 'price_on_application');
                },
                'bath' => function ($q) {
                    $q->select('id');
                },
                'bath.portals' => function ($q) use ($mainPortal) {
                    $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bath_id', 'portal_id');
                },
                'amenities' => function ($q) use ($mainPortal) {
                    $q->whereHas('amenity.portals', function ($q) use ($mainPortal) {
                        $q->where('portal_id', '=', $mainPortal->id);
                    });
                },
                'amenities.amenity.portals' => function ($q) use ($mainPortal) {
                    $q->where('portal_id', '=', $mainPortal->id)
                        ->where('title', '!=', '');
                },
                'community.region', 'subCommunity', 'communityProperty',
                'photos' => function ($q) {
                    $q->select('id', 'property_id', 'path', 'title','original', 'updated_at');
                },
                'floorPlan'
            ])->first();

            if (!$property) {
                $this->makeListingTimelineLog([
                    'portal_id' => $mainPortal->id,
                    'property_id' => $propertyId,
                    'title' => 'Listing not found or portal is not selected for this listing.',
                    'status' => 'failed',
                ]);
                return false;
            }
            $pre_payload = $this->preparePayloadForCreateAndUpdateListing($property);

            $payload = [];
            if ($pre_payload['status']) {
                $payload = $pre_payload['data'];
            }

            if (empty($payload)) {
                return false;
            }

            $token = $this->getPFTokenBasedOnPropertyV2($property);
            $headers = [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ];

            $response = Http::withHeaders($headers)
                ->withBody(json_encode($payload), 'application/json')
                ->post($this->listingBaseUrl);

            if ($response->status() == 401) {
                $this->makeListingTimelineLog([
                    'portal_id' => $mainPortal->id,
                    'property_id' => $propertyId,
                    'title' => 'Token expired while creating listing at property finder',
                    'status' => 'failed',
                    'response' => $response->json(),
                    'payload' => $payload,
                ]);
                $token = $this->getPFTokenBasedOnPropertyV2($property, true);
                $headers['Authorization'] = 'Bearer ' . $token;
                $response = Http::withHeaders($headers)
                    ->withBody(json_encode($payload), 'application/json')
                    ->post($this->listingBaseUrl);
            }

            $data = $response->json();

            if ($response->failed()) {
                $this->makeListingTimelineLog([
                    'portal_id' => $mainPortal->id,
                    'property_id' => $propertyId,
                    'title' => 'failed to create listing at property finder.',
                    'status' => 'failed',
                    'response' => $data,
                    'payload' => $payload,
                ]);
                return false;
            }

            if ($response->successful()) {
                if (isset($data['id'])) {
                    if ($property->portal_ids == null) {
                        $portal_data = [];
                    } else {
                        $portal_data = json_decode($property->portal_ids, true);
                    }
                    $portal_data['property_finder_id'] = $data['id'];
                    $property->withoutApproval()->update(['portal_ids' => json_encode($portal_data)]);
                }
                $this->makeListingTimelineLog([
                    'portal_id' => $mainPortal->id,
                    'property_id' => $propertyId,
                    'title' => 'Created listing at property finder successfully',
                    'status' => 'success',
                    'response' => $data,
                    'payload' => $payload,
                ]);
                return true;
            }
            return false;
        } catch (\Exception $exception) {
            $this->makeListingTimelineLog([
                'portal_id' => $mainPortal->id ?? null,
                'property_id' => $propertyId,
                'title' => 'Something went wrong while creating listing at property finder',
                'description' => $exception->getMessage(),
                'payload' => $payload ?? [],
                'status' => 'failed',
            ]);
            return false;
        }
    }

    /*
     * Documentation: https://api-docs.propertyfinder.net/enterprise-api/index.html#tag/Listings/operation/updateListing
     */
    public function updateListing($propertyId): bool
    {
        try {
            $mainPortal = Portal::where("short_name", "PF")->latest()->first();
            $property = Property::whereHas("portals", function ($q) use ($mainPortal) {
                $q->where("portal_id", $mainPortal->id);
            })->where('id', $propertyId)->with([
                'agent' => function ($q) {
                    $q->select('id', 'name', 'first_name', 'last_name', 'pp_enabled', 'pp_name', 'pp_photo', 'pp_license_no', 'email', 'secondary_email', 'license_no', 'mobile', 'bio', 'thumbnail', 'image');
                },
                'details',
                'propertyCategory' => function ($q) {
                    $q->select('id', 'title');
                },
                'propertyCategory.portals' => function ($q) use ($mainPortal) {
                    $q->where('portal_id', $mainPortal->id)->select('id', 'title', 'property_category_id', 'portal_id');
                },
                'bed' => function ($q) {
                    $q->select('id');
                },
                'bed.portals' => function ($q) use ($mainPortal) {
                    $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bed_id', 'portal_id');
                },
                'developer',
                'price' => function ($q) {
                    $q->select('id', 'property_id', 'price', 'prices', 'price_on_application');
                },
                'bath' => function ($q) {
                    $q->select('id');
                },
                'bath.portals' => function ($q) use ($mainPortal) {
                    $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bath_id', 'portal_id');
                },
                'amenities' => function ($q) use ($mainPortal) {
                    $q->whereHas('amenity.portals', function ($q) use ($mainPortal) {
                        $q->where('portal_id', '=', $mainPortal->id);
                    });
                },
                'amenities.amenity.portals' => function ($q) use ($mainPortal) {
                    $q->where('portal_id', '=', $mainPortal->id)
                        ->where('title', '!=', '');
                },
                'community.region', 'subCommunity', 'communityProperty',
                'photos' => function ($q) {
                    $q->select('id', 'property_id', 'path', 'title','original', 'updated_at');
                },
                'floorPlan'
            ])->first();

            if (!$property) {
                $this->makeListingTimelineLog([
                    'portal_id' => $mainPortal->id,
                    'property_id' => $propertyId,
                    'title' => 'Property not found or portal is not selected for this property while updating listing at property finder.',
                    'status' => 'failed',
                    'response' => null
                ]);
                return false;
            }
            $payload = [];
            $pre_payload = $this->preparePayloadForCreateAndUpdateListing($property);
            if ($pre_payload['status']) {
                $payload = $pre_payload['data'];
            }

            if (empty($payload)) {
                return false;
            }

            $portalListingId = json_decode($property->portal_ids, true)['property_finder_id'] ?? null;

            if (!empty($portalListingId)) {
                $token = $this->getPFTokenBasedOnPropertyV2($property);
                $headers = [
                    'Authorization' => 'Bearer ' . $token,
                ];
                $response = Http::withHeaders($headers)
                    ->put($this->listingBaseUrl . '/' . $portalListingId, $payload);

                if ($response->status() == 401) {

                    $this->makeListingTimelineLog([
                        'portal_id' => $mainPortal->id,
                        'property_id' => $propertyId,
                        'title' => 'Token expired while updating listing at property finder',
                        'status' => 'failed',
                        'response' => $response->json(),
                        'payload' => $payload,
                    ]);

                    $token = $this->getPFTokenBasedOnPropertyV2($property, true);
                    $headers['Authorization'] = 'Bearer ' . $token;
                    $response = Http::withHeaders($headers)
                        ->put($this->listingBaseUrl . '/' . $portalListingId, $payload);
                }

                $data = $response->json();


                if ($response->status() == 404) { //this can happen if the listing is deleted from pf but id is not removed from our database
                    $this->makeListingTimelineLog([
                        'portal_id' => $mainPortal->id,
                        'property_id' => $propertyId,
                        'title' => 'Failed to update listing at property finder - Listing not found',
                        'status' => 'failed',
                        'response' => $data,
                        'payload' => $payload,
                    ]);

                    if ($property->portal_ids == null) {
                        $portal_data = [];
                    } else {
                        $portal_data = json_decode($property->portal_ids, true);
                    }
                    unset($portal_data['property_finder_id']);
                    $property->withoutApproval()->update(['portal_ids' => json_encode($portal_data)]);

                    PFV2CreateListingJob::dispatch($propertyId);
                    return true;
                }
                if ($response->status() == 422) {
                    $this->makeListingTimelineLog([
                        'portal_id' => $mainPortal->id,
                        'property_id' => $propertyId,
                        'title' => 'Failed to update listing - Validation error at PF',
                        'status' => 'failed',
                        'response' => $response->json(),
                        'payload' => $payload,
                    ]);
                    return false;
                }

                if ($response->failed() || $response->status() == 400) {
                    $this->makeListingTimelineLog([
                        'portal_id' => $mainPortal->id,
                        'property_id' => $propertyId,
                        'title' => 'Failed to update listing at property finder -Error code 2',
                        'status' => 'failed',
                        'response' => $response->json(),
                        'payload' => $payload,
                    ]);
                    return false;
                }

                if ($response->successful()) {
                    $this->makeListingTimelineLog([
                        'portal_id' => $mainPortal->id,
                        'property_id' => $propertyId,
                        'title' => 'Updated listing at Property finder successfully',
                        'status' => 'success',
                        'response' => $response->json(),
                        'payload' => $payload,
                    ]);
                    return true;
                }
            }
            return false;
        } catch (\Exception $exception) {
            $this->makeListingTimelineLog([
                'portal_id' => $mainPortal->id ?? null,
                'property_id' => $propertyId,
                'title' => 'Something went wrong while updating listing at property finder',
                'description' => $exception->getMessage(),
                'status' => 'failed',
            ]);
            return false;
        }
    }

    /*
     * Documentation: https://api-docs.propertyfinder.net/enterprise-api/index.html#tag/Listings/operation/deleteListing
     */
    public function deleteListing($propertyId): bool
    {
        try {
            $mainPortal = Portal::where("short_name", "PF")->latest()->first();
            $property = Property::where('id', $propertyId)->first();

            if (empty($property)) {
                $this->makeListingTimelineLog([
                    'portal_id' => $mainPortal->id,
                    'property_id' => $propertyId,
                    'title' => 'Property not found while deleting listing at property finder',
                    'status' => 'failed',
                    'response' => null
                ]);
                return false;
            }

            $portalListingId = json_decode($property->portal_ids, true)['property_finder_id'] ?? null;

            if (empty($portalListingId)) {
                $this->makeListingTimelineLog([
                    'portal_id' => $mainPortal->id,
                    'property_id' => $propertyId,
                    'title' => 'Unable to delete from portal no pf unique id found : ' . $propertyId,
                    'status' => 'failed',
                    'response' => null
                ]);
                return false;
            }

            $token = $this->getPFTokenBasedOnPropertyV2($property);
            $headers = [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ];

            $response = Http::withHeaders($headers)->delete($this->listingBaseUrl . '/' . $portalListingId);

            if ($response->status() == 401) {
                $this->makeListingTimelineLog([
                    'portal_id' => $mainPortal->id,
                    'property_id' => $propertyId,
                    'title' => 'Token expired while deleting listing at property finder',
                    'status' => 'failed',
                    'response' => $response->json()
                ]);
                $token = $this->getPFTokenBasedOnPropertyV2($property, true);
                $headers = [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                ];
                $response = Http::withHeaders($headers)->delete($this->listingBaseUrl . '/' . $portalListingId);
            }

            $data = $response->json();
            if ($response->failed()) {
                $this->makeListingTimelineLog([
                    'portal_id' => $mainPortal->id,
                    'property_id' => $propertyId,
                    'title' => 'Failed to delete listing from property finder.',
                    'status' => 'failed',
                    'response' => $data
                ]);
                return false;
            }
            if ($response->status() == 404) {
                $this->makeListingTimelineLog([
                    'portal_id' => $mainPortal->id,
                    'property_id' => $propertyId,
                    'title' => 'Listing ID not found at property finder.',
                    'status' => 'failed',
                    'response' => $data
                ]);

                if ($property->portal_ids == null) {
                    $portal_data = [];
                } else {
                    $portal_data = json_decode($property->portal_ids, true);
                }
                unset($portal_data['property_finder_id']);
                $property->withoutApproval()->update(['portal_ids' => json_encode($portal_data)]);

                return true;
            }
            //if response status is 204, it means the deletion was successful
            if ($response->status() == 204) {
                $this->makeListingTimelineLog([
                    'portal_id' => $mainPortal->id,
                    'property_id' => $propertyId,
                    'title' => 'Listing deleted from property finder successfully.',
                    'status' => 'success',
                    'response' => $data
                ]);

                if ($property->portal_ids == null) {
                    $portal_data = [];
                } else {
                    $portal_data = json_decode($property->portal_ids, true);
                }
                unset($portal_data['property_finder_id']);
                $property->withoutApproval()->update(['portal_ids' => json_encode($portal_data)]);

                return true;
            }
            return false;
        } catch (\Exception $exception) {
            $this->makeListingTimelineLog([
                'portal_id' => $mainPortal->id ?? null,
                'property_id' => $propertyId,
                'title' => 'Something went wrong while deleting listing at property finder',
                'description' => $exception->getMessage(),
                'status' => 'failed',
            ]);
            return false;
        }
    }
    /*
     * Unpublish listing using id
     * Documentation: https://api-docs.propertyfinder.net/enterprise-api/index.html#tag/Listings/operation/listingUnpublish
     * Api url: https://atlas.propertyfinder.com/v1/listings/{id}/unpublish
     * method: POST
     */
    public function unpublishListing($propertyId): bool
    {
        try {
            $mainPortal = Portal::where("short_name", "PF")->latest()->first();
            $property = Property::whereHas("portals", function ($q) use ($mainPortal) {
                $q->where("portal_id", $mainPortal->id);
            })->where('id', $propertyId)->first();

            if (empty($property)) {
                $this->makeListingTimelineLog([
                    'portal_id' => $mainPortal->id,
                    'property_id' => $propertyId,
                    'title' => 'Property not found while unpublishing listing at property finder',
                    'status' => 'failed',
                    'response' => null
                ]);
                return false;
            }

            $portalListingId = json_decode($property->portal_ids, true)['property_finder_id'] ?? null;

            if (empty($portalListingId)) {
                $this->makeListingTimelineLog([
                    'portal_id' => $mainPortal->id,
                    'property_id' => $propertyId,
                    'title' => 'PF Listing Id not found at local db while unpublishing listing at property finder',
                    'status' => 'failed',
                    'response' => null
                ]);
                return false;
            }

            $token = $this->getPFTokenBasedOnPropertyV2($property);
            $headers = [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ];

            $response = Http::withHeaders($headers)->post($this->listingBaseUrl . '/' . $portalListingId . '/unpublish');

            if ($response->status() == 401) {
                $this->makeListingTimelineLog([
                    'portal_id' => $mainPortal->id,
                    'property_id' => $propertyId,
                    'title' => 'Token expired while unpublishing listing at property finder',
                    'status' => 'failed',
                    'response' => $response->json()
                ]);

                $token = $this->getPFTokenBasedOnPropertyV2($property, true);
                $headers['Authorization'] = 'Bearer ' . $token;
                $response = Http::withHeaders($headers)->post($this->listingBaseUrl . '/' . $portalListingId . '/unpublish');
            }

            // Handle success response
            if ($response->status() == 200) {
                $this->makeListingTimelineLog([
                    'portal_id' => $mainPortal->id,
                    'property_id' => $propertyId,
                    'title' => 'Listing unpublished requested at property finder successfully',
                    'status' => 'success',
                    'response' => $response->json()
                ]);
                return true;
            }

            $data = $response->json();
            if ($response->failed() || $response->status() == 422 || $response->status() == 400 || $response->status() == 500) {
                $this->makeListingTimelineLog([
                    'portal_id' => $mainPortal->id,
                    'property_id' => $propertyId,
                    'title' => 'Failed to unpublish listing at property finder.',
                    'status' => 'failed',
                    'response' => $data
                ]);
                return false;
            }

            // Add explicit return for unhandled status codes
            return false;

        } catch (\Exception $exception) {
            $this->makeListingTimelineLog([
                'portal_id' => $mainPortal->id ?? null,
                'property_id' => $propertyId,
                'title' => 'Something went wrong while unpublishing listing at property finder',
                'description' => $exception->getMessage(),
                'status' => 'failed',
            ]);
            return false;
        }
    }
    /*
     * Documentation: https://api-docs.propertyfinder.net/enterprise-api/index.html#tag/Listings/operation/publishListing
     */
    public function publishListing($propertyId): bool
    {
        try {
            $mainPortal = Portal::where("short_name", "PF")->latest()->first();
            $property = Property::whereHas("portals", function ($q) use ($mainPortal) {
                $q->where("portal_id", $mainPortal->id);
            })->where('id', $propertyId)->first();

            if (empty($property)) {
                $this->makeListingTimelineLog([
                    'portal_id' => $mainPortal->id,
                    'property_id' => $propertyId,
                    'title' => 'Property not found while deleting listing at property finder',
                    'status' => 'failed',
                    'response' => null
                ]);
                return false;
            }

            $portalListingId = json_decode($property->portal_ids, true)['property_finder_id'] ?? null;

            if (empty($portalListingId)) {
                $this->makeListingTimelineLog([
                    'portal_id' => $mainPortal->id,
                    'property_id' => $propertyId,
                    'title' => 'Listing Id not found while deleting listing at property finder',
                    'status' => 'failed',
                    'response' => null
                ]);
                return false;
            }

            $token = $this->getPFTokenBasedOnPropertyV2($property);
            $headers = [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ];

            $response = Http::withHeaders($headers)->post($this->listingBaseUrl . '/' . $portalListingId . '/publish');

            if ($response->status() == 401) {
                $this->makeListingTimelineLog([
                    'portal_id' => $mainPortal->id,
                    'property_id' => $propertyId,
                    'title' => 'Token expired while publishing listing at property finder',
                    'status' => 'failed',
                    'response' => $response->json()
                ]);

                $token = $this->getPFTokenBasedOnPropertyV2($property, true);
                $headers = [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                ];

                $response = Http::withHeaders($headers)->post($this->listingBaseUrl . '/' . $portalListingId . '/publish');
            }

            $data = $response->json();

            if ($response->failed() || $response->status() == 422 || $response->status() == 400) {
                $this->makeListingTimelineLog([
                    'portal_id' => $mainPortal->id,
                    'property_id' => $propertyId,
                    'title' => 'Failed to publish listing at  property finder.',
                    'status' => 'failed',
                    'response' => $data
                ]);
                return false;
            }
            if ($response->successful()) {
                $this->makeListingTimelineLog([
                    'portal_id' => $mainPortal->id,
                    'property_id' => $propertyId,
                    'title' => 'Listing published at property finder successfully',
                    'status' => 'success',
                    'response' => $data
                ]);
                // dispatch a job to check the listing status
                PFV2CheckListingStateJob::dispatch($mainPortal->id, $propertyId, $portalListingId)->delay(now()->addMinutes(3));
                return true;
            }
            return false;
        } catch (\Exception $exception) {
            $this->makeListingTimelineLog([
                'portal_id' => $mainPortal->id ?? null,
                'property_id' => $propertyId,
                'title' => 'Something went wrong while publishing listing at property finder',
                'description' => $exception->getMessage(),
                'status' => 'failed',
            ]);
            return false;
        }
    }

    public function searchPfLocations($term, $entity = "secondary")
    {
        $allResults = [];
        try {
            $url = "https://atlas.propertyfinder.com/v1/locations";
            $tokens = $this->getPfAccessTokenV2($entity);
            $token = $tokens['token'] ?? '';

            $headers = [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ];


            $page = 1;

            do {
                $params = [
                    "search" => $term,
                    "page" => $page,
                    "perPage" => 100,
                ];
                $response = Http::withHeaders($headers)->get($url, $params);

                if ($response->failed()) {
                    Log::info('Property Finder Locations Search Failed for location ' . $term, [
                        'status' => $response->status(),
                        'body' => $response->json()
                    ]);
                    break;
                }

                if ($response->status() == 401) {
                    $this->getPfAccessTokenV2($entity, true);
                    $response = Http::withHeaders($headers)->get($url, $params);
                    Log::info('Property Finder Locations Search Token expired for location ' . $term);
                    if ($response->failed()) {
                        Log::info('Property Finder Locations Search Failed after token refresh for location ' . $term, [
                            'status' => $response->status(),
                            'body' => $response->json()
                        ]);
                        break;
                    }
                }

                $data = $response->json();

                if (isset($data['errors']) && !empty($data['errors'])) {
                    Log::info('Property Finder Location search encountered a problem for keyword ' . $term . ' ' . json_encode($data['errors']));
                    break;
                }

                if (isset($data['data']) && is_array($data['data'])) {
                    $allResults = array_merge($allResults, $data['data']);
                }

                $nextPage = $data['pagination']['nextPage'] ?? null;
                $page = $nextPage;

            } while ($nextPage !== null);

            if (empty($allResults)) {
                Log::info('Property Finder Locations Search No data found for location ' . $term);
            }

            return $allResults;
        } catch (\Exception $exception) {
            return $allResults;
        }
    }

    public function findLocation($name, $entity = "secondary")
    {

        $mainPortal = Portal::where("short_name", "PF")->latest()->first();
        try {
            $token = $this->getPfAccessTokenV2($entity);

            $headers = [
                'Authorization' => 'Bearer ' . $token["token"],
                'Content-Type' => 'application/json',
            ];

            $params = [
                "search" => $name,
                "page" => 1,
                "perPage" => 100,
            ];

            $response = Http::withHeaders($headers)->get($this->locationBaseUrl, $params);

            if ($response->status() === 401) {
                $token = $this->getPfAccessTokenV2($entity, true);
                $headers['Authorization'] = 'Bearer ' . $token["token"];
                $response = Http::withHeaders($headers)->get($this->locationBaseUrl, $params);
            }

            $data = $response->json();

            if ($response->failed()) {
                $this->makeListingTimelineLog([
                    'portal_id' => $mainPortal->id,
                    'title' => "Request failed while finding location {$name} from property finder with Status: " . $response->status(),
                    'status' => 'failed',
                    'response' => $data,
                    'payload' => $params
                ]);
            }

            if (!isset($data['data']) || !is_array($data['data']) || empty($data['data'])) {
                $this->makeListingTimelineLog([
                    'portal_id' => $mainPortal->id,
                    'title' => "Location {$name} not found in property finder.",
                    'status' => 'failed',
                    'response' => $data,
                    'payload' => $params
                ]);
            }

            if (!empty($data['data'][0])){
                return $data['data'][0];
            }

            return null;
        } catch (\Throwable $e) {
            return null;
        }
    }


    public function findLocationId($community_property)
    {
        $pf_location_id = null;
        if (!empty($community_property)) {
            $locations = $this->searchPfLocations($community_property->name);
            foreach ($locations as $location) {
                if (strtolower($community_property->name) == strtolower($location['name'])) {
                    $portal_ids = json_decode($community_property->portal_ids, true) ?? [];
                    $portal_ids['property_finder_id'] = $location['id'];
                    $community_property->portal_ids = json_encode($portal_ids);
                    $community_property->save();
                    $pf_location_id = $location['id'];
                } else {
                    continue;
                }

            }

            return $pf_location_id;

        } else {
            Log::error('Community Property not found for ID: ' . $community_property->id);
            return null;
        }

    }

    /*
     * Documentation: https://api-docs.propertyfinder.net/enterprise-api/index.html#tag/Users/paths/~1v1~1users~1/get
     */
    public function findUserByEmail($email, $entity = "secondary"): ?array
    {
        try {
            $token = $this->getPfAccessTokenV2($entity);
            $headers = [
                'Authorization' => 'Bearer ' . $token["token"],
                'Content-Type' => 'application/json',
            ];
            $response = Http::withHeaders($headers)->get($this->userBaseUrl, [
                'search' => $email,
            ]);

            if ($response->status() == 401) {
                $token = $this->getPfAccessTokenV2($entity, true);
                $headers = [
                    'Authorization' => 'Bearer ' . $token['token'],
                    'Content-Type' => 'application/json',
                ];
                $response = Http::withHeaders($headers)->get($this->userBaseUrl, [
                    'search' => $email,
                ]);
            }

            if ($response->failed()) {
                return null;
            }

            if ($response->successful()) {
                $data = $response->json()['data'];
                if (!empty($data)) {
                    return [
                        'pf_private_id' => $data[0]['id'],
                        'pf_v2_user_id' => $data[0]['publicProfile']['id'],
                        'pf_public_profile_id' => $data[0]['publicProfile']['id'],
                    ];
                }
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function searchUsers($entity = "secondary"): ?array
    {
        $users = [];
        try {
            $token = $this->getPfAccessTokenV2($entity);
            $headers = [
                'Authorization' => 'Bearer ' . $token["token"],
                'Content-Type' => 'application/json',
            ];

            $page = 1;

            do {
                $params = [
                    "page" => $page,
                    "perPage" => 100
                ];

                $response = Http::withHeaders($headers)->get($this->userBaseUrl, $params);

                if ($response->status() === 401) {
                    Log::warning("Token expired. Refreshing token...");

                    $token = $this->getPfAccessTokenV2($entity, true);
                    $headers['Authorization'] = 'Bearer ' . $token["token"];

                    $response = Http::withHeaders($headers)->get($this->userBaseUrl, $params);
                }

                $data = $response->json();

                if ($response->failed()) {
                    Log::error("Search user request failed at property finder for page $page. Status: " . $response->status());

                    if (isset($data['errors']) && !empty($data['errors'])) {
                        Log::error('Property Finder API returned errors: ' . json_encode($data['errors']));
                    }
                    break;
                }


                if (!isset($data['data']) || !is_array($data['data'])) {
                    Log::error("Missing or invalid data on page $page. Aborting.");
                    break;
                }

                foreach ($data['data'] as $item) {
                    $users[] = $item;
                }

                $nextPage = $data['pageMetadata']['nextPage'] ?? 0;
                $page = $nextPage;

            } while ($page != 0);
            return $users;
        } catch (\Throwable $e) {
            Log::error('Exception in Property Finder sync users: ' . $e->getMessage());
            return $users;
        }
    }

    /*
     * Documentation: https://api-docs.propertyfinder.net/enterprise-api/index.html#tag/Users/paths/~1v1~1users~1/post
     */
    public function createUser($data, $propertyId = null, $entity = "secondary")
    {
        $pfUserData = $this->findUserByEmail($data['email']);
        if (!empty($pfUserData)) {
            $this->updateThirdPartyIds($data['id'], $pfUserData);
            return $pfUserData['pf_public_profile_id'] ?? null;
        }
        $mainPortal = Portal::where("short_name", "PF")->latest()->first();
        if (empty($data['pp_license_no'])) {
            $this->makeListingTimelineLog([
                'portal_id' => $mainPortal->id,
                'property_id' => $propertyId,
                'user_id' => $data['id'] ?? null,
                'title' => 'User Account Creation Failed at Property Finder',
                'description' => 'User has no valid compliance details.',
                'status' => 'failed',
                'payload' => $data,
            ]);
        }
        try {
            $payload = [
                'email' => $data['email'],
                'firstName' => $data['first_name'],
                'lastName' => $data['last_name'],
                'mobile' => $data['mobile'],
                'roleId' => 3,
                'publicProfile' => [
                    'name' => $data["pp_enabled"] ? $data['pp_name'] : $data['name'],
                    'email' => $data['email'],
                    'phone' => $data['mobile'],
                    'bio' => [
                        'primary' => $data['bio'] ?? '',
                        'secondary' => $data['bio'] ?? '',
                    ],
                    'position' => [
                        'primary' => $data['position'],
                        'secondary' => $data['position'],
                    ],
                    'compliances' => [
                        [
                            'type' => 'brn',
                            'value' => $data['pp_license_no']
                        ]
                    ],
                ],
            ];

            if (!empty($data['pp_photo'])) {
                $payload['publicProfile']['imageUrl'] = $data['pp_photo'];
            }

            $token = $this->getPfAccessTokenV2($entity);
            $headers = [
                'Authorization' => 'Bearer ' . $token["token"],
                'Content-Type' => 'application/json',
            ];
            $response = Http::withHeaders($headers)->post($this->userBaseUrl, $payload);
            //IF Unauthenticated, get new token and try again
            if ($response->status() == 401) {
                $token = $this->getPfAccessTokenV2($entity, true);
                $headers = [
                    'Authorization' => 'Bearer ' . $token['token'],
                    'Content-Type' => 'application/json',
                ];
                $response = Http::withHeaders($headers)->post($this->userBaseUrl, $payload);
            }
            if ($response->failed()) {
                if ($propertyId){
                    $this->makeListingTimelineLog([
                        'portal_id' => $mainPortal->id,
                        'property_id' => $propertyId,
                        'user_id' => $data['id'] ?? null,
                        'title' => 'User Account Creation Failed at Property Finder.',
                        'description' => 'Failed to create agent account at Property Finder for  agent: ' . $data['name'],
                        'status' => 'failed',
                        'response' => $response->json(),
                        'payload' => $payload,
                    ]);
                }
                return null;
            }

            if ($response->successful()) {
                $body = $response->json();
                $this->updateThirdPartyIds($data['id'], [
                    'pf_private_id' => $body['id'],
                    'pf_v2_user_id' => $body['publicProfile']['id'],
                    'pf_public_profile_id' => $body['publicProfile']['id'],
                ]);
                return $body['publicProfile']['id'];
            }
        } catch (\Exception $e) {
            if ($propertyId){
                $this->makeListingTimelineLog([
                    'portal_id' => $mainPortal->id,
                    'property_id' => $propertyId,
                    'user_id' => $data['id'] ?? null,
                    'title' => 'User Account Creation Failed at Property Finder',
                    'description' => $e->getMessage(),
                    'status' => 'failed',
                    'payload' => $data,
                ]);
            }

            return null;
        }
        return null;
    }

    /*
     * Documentation: https://api-docs.propertyfinder.net/enterprise-api/index.html#tag/Users/paths/~1v1~1users~1/post
     */
    public function updateUserPublicProfile($data, $entity = "secondary")
    {
        $pfUserData = $this->findUserByEmail($data['email'], $entity);

        if (empty($pfUserData)){
            Log::warning("User ".$data["email"]."not found for entity $entity");
            return null;
            return $this->createUser($data, null, $entity);
        }
        $publicProfileId = $pfUserData["pf_public_profile_id"] ?? null;
        if (empty($publicProfileId)) {
            return null;
        }
        try {
            $payload = [
                'name' => $data["pp_enabled"] ? $data['pp_name'] : $data['name'],
                'email' => $data['email'],
                'phone' => $data['mobile'],
                'bio' => [
                    'primary' => $data['bio'] ?? '',
                    'secondary' => $data['bio'] ?? '',
                ],
                'position' => [
                    'primary' => $data['position'],
                    'secondary' => $data['position'],
                ],
                'compliances' => [
                    [
                        'type' => 'brn',
                        'value' => $data['pp_license_no']
                    ]
                ],
            ];

            if (!empty($data['pp_photo'])) {
                $payload['imageUrl'] = $data['pp_photo'];
            }

            $token = $this->getPfAccessTokenV2($entity);

            $headers = [
                'Authorization' => 'Bearer ' . $token["token"],
                'Content-Type' => 'application/json',
            ];
            $response = Http::withHeaders($headers)
                ->patch($this->publicProfileBaseUrl. "/".$publicProfileId, $payload);

            //IF Unauthenticated, get new token and try again
            if ($response->status() == 401) {
                Log::warning("Regenerating token to update public profile to property finder for User ".$data["email"]." for entity $entity");
                $token = $this->getPfAccessTokenV2($entity, true);
                $headers = [
                    'Authorization' => 'Bearer ' . $token["token"],
                    'Content-Type' => 'application/json',
                ];
                $response = Http::withHeaders($headers)
                    ->patch($this->publicProfileBaseUrl. "/".$publicProfileId, $payload);
            }

            if ($response->status() == 204) {
                Log::info('User'.$data['email'].' public profile updated in Property Finder.');
                return true;
            }
            if ($response->failed()) {
                Log::error('Failed to update public profile for user'.$data['email'].' in Property Finder sync users: ' . json_encode($response->body()));
//                $this->sendDebugTelegramMessage([], "failed to update public profile at property finder", []);
                return null;
            }
        } catch (\Exception $e) {
            $this->sendDebugTelegramMessage([], "'Exception in update public profile at property finder." . $e->getMessage(), []);
            return null;
        }
        return null;
    }

    /*
     * Documentation: https://api-docs.propertyfinder.net/enterprise-api/index.html#tag/Listing-verifications/operation/get_verification_submissions
     */
    public function listingVerificationList($listingId = null, $propertyId = null, $entity = "secondary"): void
    {
        try {
            set_time_limit(0);
            $mainPortal = Portal::where("short_name", "PF")->latest()->first();
            $token = $this->getPfAccessTokenV2($entity);
            $headers = [
                'Authorization' => 'Bearer ' . $token["token"],
                'Content-Type' => 'application/json',
            ];

            $page = 1;

            do {
                $params = [
                    "page" => $page,
                    "perPage" => 100,
                    "include" => "document, history",
                ];

                if (!empty($listingId)) {
                    $params['filter[listingId]'] = $listingId;
                }

                $response = Http::withHeaders($headers)->get($this->listingVerificationBaseUrl, $params);

                if ($response->status() === 401) {
                    $this->makeListingTimelineLog([
                        'portal_id' => $mainPortal->id,
                        'property_id' => $propertyId,
                        'title' => 'Token expired while fetching listing verification submission list from Property Finder.',
                        'status' => 'failed',
                        'response' => $response->json()
                    ]);
                    Log::warning("Token expired. Refreshing token...");

                    $token = $this->getPfAccessTokenV2($entity, true);
                    $headers['Authorization'] = 'Bearer ' . $token["token"];

                    $response = Http::withHeaders($headers)->get($this->listingVerificationBaseUrl, $params);
                }

                $data = $response->json();

                if ($response->failed()) {
                    $this->makeListingTimelineLog([
                        'portal_id' => $mainPortal->id,
                        'property_id' => $propertyId,
                        'title' => "Request failed to fetch listing verifications for page $page. Status: " . $response->status(),
                        'status' => 'failed',
                        'response' => $data,
                        'payload' => $params ?? []
                    ]);
                    Log::error("Request failed for page $page. Status: " . $response->status());

                    if (isset($data['errors']) && !empty($data['errors'])) {
                        Log::error('Property Finder API returned errors: ' . json_encode($data['errors']));
                    }
                    break;
                }


                if (!isset($data['submissions']) || !is_array($data['submissions'])) {
                    $this->makeListingTimelineLog([
                        'portal_id' => $mainPortal->id,
                        'property_id' => $propertyId,
                        'title' => "Missing or invalid submissions on page $page. for listing verification submission list from Property Finder.",
                        'status' => 'failed',
                        'response' => $data,
                        'payload' => $params ?? []
                    ]);
                    Log::error("Missing or invalid submissions on page $page. Aborting.");
                    break;
                }

                foreach ($data['submissions'] as $value) {
                    $this->updateOrCreateVerification($value);
                }

                $nextPage = $data['pageMetadata']['nextPage'] ?? 0;
                $page = $nextPage;

            } while ($page != 0);

        } catch (\Throwable $e) {
            Log::error('Exception in Property Finder sync: ' . $e->getMessage());
        }
    }

    public function listingVerificationSubmissionsList($listingIdIds, $entity = "secondary"): void
    {
        Log::debug("ListingIdIds: $listingIdIds, entity: $entity");
        try {
            set_time_limit(0);
            $mainPortal = Portal::where("short_name", "PF")->latest()->first();
            $token = $this->getPfAccessTokenV2($entity);
            $headers = [
                'Authorization' => 'Bearer ' . $token["token"],
                'Content-Type' => 'application/json',
            ];

            $params = [
                "page" => 1,
                "perPage" => 100,
                "include" => "document, history",
                "filter[listingId]" => $listingIdIds,
            ];

            $response = Http::withHeaders($headers)->get($this->listingVerificationBaseUrl, $params);

            if ($response->status() === 401) {
                $this->makeListingTimelineLog([
                    'portal_id' => $mainPortal->id,
                    'title' => 'Token expired while fetching listing verification submission list from Property Finder.',
                    'status' => 'failed',
                    'response' => $response->json()
                ]);
                Log::warning("Token expired. Refreshing token...");

                $token = $this->getPfAccessTokenV2($entity, true);
                $headers['Authorization'] = 'Bearer ' . $token["token"];

                $response = Http::withHeaders($headers)->get($this->listingVerificationBaseUrl, $params);
            }

            $data = $response->json();
            Log::info("before failed: " . json_encode($data));
            if ($response->failed()) {
                Log::info("failed: " . json_encode($data));
                $this->makeListingTimelineLog([
                    'portal_id' => $mainPortal->id,
                    'title' => "failed to fetch submissions, Status: " . $response->status(),
                    'status' => 'failed',
                    'response' => $data,
                    'payload' => $params
                ]);
            }

            Log::info("submissions: " . json_encode($data));

            if (!isset($data['submissions']) || !is_array($data['submissions'])) {
                $this->makeListingTimelineLog([
                    'portal_id' => $mainPortal->id,
                    'title' => "Missing or invalid submissions for listing verification submission list from Property Finder.",
                    'status' => 'failed',
                    'response' => $data,
                    'payload' => $params
                ]);
            }

            Log::info("sub: " . json_encode($data['submissions']));

            foreach ($data['submissions'] as $value) {
                $this->updateOrCreateVerification($value);
            }
        } catch (\Throwable $e) {
            Log::error('Exception in Property Finder sync: ' . $e->getMessage());
        }
    }

    /*
     * Documentation: https://api-docs.propertyfinder.net/enterprise-api/index.html#tag/Listing-verifications/paths/~1v1~1listing-verifications/post
     */
    public function listingVerificationSubmit($propertyId, $documents = []): bool
    {
        try {
            $mainPortal = Portal::where("short_name", "PF")->latest()->first();
            $property = Property::whereHas("portals", function ($q) use ($mainPortal) {
                $q->where("portal_id", $mainPortal->id);
            })->where('id', $propertyId)->first();

            if (empty($property)) {
                $this->makeListingTimelineLog([
                    'portal_id' => $mainPortal->id,
                    'property_id' => $propertyId,
                    'title' => 'Property not found while submitting listing for verification at property finder',
                    'status' => 'failed',
                    'response' => null,
                ]);
                return false;
            }

            $portalListingId = json_decode($property->portal_ids, true)['property_finder_id'] ?? null;

            if (empty($portalListingId)) {
                $this->makeListingTimelineLog([
                    'portal_id' => $mainPortal->id,
                    'property_id' => $propertyId,
                    'title' => 'Listing ID not found at property finder while submitting for verification.',
                    'status' => 'failed',
                    'response' => null,
                ]);
                return false;
            }

            $agent = User::find($property->agent_id);

            $agentPublicProfileId = json_decode($agent->third_party_ids, true)['pf_public_profile_id'] ?? null;

            if (empty($agentPublicProfileId)) {
                $this->makeListingTimelineLog([
                    'portal_id' => $mainPortal->id,
                    'property_id' => $propertyId,
                    'title' => 'Public profile ID not found at property finder while submitting for verification.',
                    'status' => 'failed',
                    'response' => null,
                ]);
                return false;
            }

            $token = $this->getPFTokenBasedOnPropertyV2($property);

            $headers = [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ];

            $payload = [
                'listingId' => $portalListingId,
                'publicProfileId' => 322938 //$agentPublicProfileId
            ];

            if (!empty($documents)) {
                $payload['documents'] = $documents;
            }

            $response = Http::withHeaders($headers)->post($this->listingVerificationBaseUrl, $payload);

            //IF Unauthenticated, get new token and try again
            if ($response->status() == 401) {
                $this->makeListingTimelineLog([
                    'portal_id' => $mainPortal->id,
                    'property_id' => $propertyId,
                    'title' => 'Token expired while submitting listing for verification at property finder',
                    'status' => 'failed',
                    'response' => $response->json(),
                    'payload' => $payload
                ]);
                $token = $this->getPFTokenBasedOnPropertyV2($property, true);
                $headers = [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                ];
                $response = Http::withHeaders($headers)->post($this->listingVerificationBaseUrl, $payload);
            }

            $data = $response->json();

            if ($response->failed()) {
                $this->makeListingTimelineLog([
                    'portal_id' => $mainPortal->id,
                    'property_id' => $propertyId,
                    'title' => 'Failed to submit listing for verification at property finder',
                    'status' => 'failed',
                    'response' => $data,
                    'payload' => $payload
                ]);
                return false;
            }

            if ($response->successful()) {
                $this->makeListingTimelineLog([
                    'portal_id' => $mainPortal->id,
                    'property_id' => $propertyId,
                    'title' => 'Listing submitted for verification at Property finder successfully',
                    'status' => 'success',
                    'response' => $data,
                    'payload' => $payload ?? []
                ]);
                $entity = $this->getEntityByPropertyForPFV2($property);
                $this->listingVerificationList($portalListingId, $propertyId, $entity);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /*
     * Documentation: https://api-docs.propertyfinder.net/enterprise-api/index.html#tag/Listing-verifications/paths/~1v1~1listing-verifications~1%7BsubmissionId%7D~1resubmit/post
     */
    public function listingVerificationReSubmit($propertyId, $submissionId, $listingId): bool
    {
        try {
            $mainPortal = Portal::where("short_name", "PF")->latest()->first();
            $property = Property::whereHas("portals", function ($q) use ($mainPortal) {
                $q->where("portal_id", $mainPortal->id);
            })->where('id', $propertyId)->first();

            if (empty($property)) {
                $this->makeListingTimelineLog([
                    'portal_id' => $mainPortal->id,
                    'property_id' => $propertyId,
                    'title' => 'Property not found while re-submitting listing for verification at property finder',
                    'status' => 'failed',
                    'response' => null,
                ]);
                return false;
            }

            $token = $this->getPFTokenBasedOnPropertyV2($property);

            $headers = [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ];

            $response = Http::withHeaders($headers)->post($this->listingVerificationBaseUrl . "/{$submissionId}/resubmit");

            //IF Unauthenticated, get new token and try again
            if ($response->status() == 401) {
                $this->makeListingTimelineLog([
                    'portal_id' => $mainPortal->id,
                    'property_id' => $propertyId,
                    'title' => 'Token expired while re-submitting listing for verification at property finder',
                    'status' => 'failed',
                    'response' => $response->json()
                ]);
                $token = $this->getPFTokenBasedOnPropertyV2($property, true);
                $headers['Authorization'] = 'Bearer ' . $token;
                $response = Http::withHeaders($headers)->post($this->listingVerificationBaseUrl . "/{$submissionId}/resubmit");
            }

            $data = $response->json();

            if ($response->failed()) {
                $this->makeListingTimelineLog([
                    'portal_id' => $mainPortal->id,
                    'property_id' => $propertyId,
                    'title' => 'Failed to re-submit listing for verification at property finder',
                    'status' => 'failed',
                    'response' => $data
                ]);
                return false;
            }

            if ($response->successful()) {
                $this->makeListingTimelineLog([
                    'portal_id' => $mainPortal->id,
                    'property_id' => $propertyId,
                    'title' => 'Listing re-submitted for verification at property finder successfully',
                    'status' => 'success',
                    'response' => $data
                ]);
                $entity = $this->getEntityByPropertyForPFV2($property);
                $this->listingVerificationList($listingId, $propertyId, $entity);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /*
     * Documentation: https://api-docs.propertyfinder.net/enterprise-api/index.html#tag/Listing-verifications/operation/checkListing_verification_eligibility
     */
    public function listingVerificationCheckEligibility($propertyId): bool
    {
        try {
            $mainPortal = Portal::where("short_name", "PF")->latest()->first();
            $property = Property::whereHas("portals", function ($q) use ($mainPortal) {
                $q->where("portal_id", $mainPortal->id);
            })->where('id', $propertyId)->first();

            if (empty($property)) {
                $this->makeListingTimelineLog([
                    'portal_id' => $mainPortal->id,
                    'property_id' => $propertyId,
                    'title' => 'Property not found while submitting listing for verification at property finder.',
                    'status' => 'failed',
                    'response' => null,
                ]);
                return false;
            }

            $portalListingId = json_decode($property->portal_ids, true)['property_finder_id'] ?? null;

            if (empty($portalListingId)) {
                Log::error('Property Finder ID not found for Property ID: ' . $propertyId);
                $this->makeListingTimelineLog([
                    'portal_id' => $mainPortal->id,
                    'property_id' => $propertyId,
                    'title' => 'Listing Id not found at property finder while submitting listing for verification.',
                    'status' => 'failed',
                    'response' => null,
                ]);
                return false;
            }

            $token = $this->getPFTokenBasedOnPropertyV2($property);
            $payload = [
                'listingId' => $portalListingId,
            ];

            $headers = [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ];
            $response = Http::withHeaders($headers)->post($this->listingVerificationBaseUrl . "/eligibility-check", $payload);
            //IF Unauthenticated, get new token and try again
            if ($response->status() == 401) {
                $this->makeListingTimelineLog([
                    'portal_id' => $mainPortal->id,
                    'property_id' => $propertyId,
                    'title' => 'Token expired while checking eligibility check for property finder',
                    'status' => 'failed',
                    'response' => $response->json(),
                    'payload' => $payload
                ]);
                $token = $this->getPFTokenBasedOnPropertyV2($property, true);
                $headers = [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                ];
                $response = Http::withHeaders($headers)->post($this->listingVerificationBaseUrl . "/eligibility-check", $payload);
            }

            $data = $response->json();

            if ($response->failed()) {
                $this->makeListingTimelineLog([
                    'portal_id' => $mainPortal->id,
                    'property_id' => $propertyId,
                    'title' => 'Failed to check eligibility at property finder.',
                    'status' => 'failed',
                    'response' => $data,
                    'payload' => $payload
                ]);
                return false;
            }

            if ($response->successful()) {
                if ($data['eligible']) {
                    $this->makeListingTimelineLog([
                        'portal_id' => $mainPortal->id,
                        'property_id' => $propertyId,
                        'title' => 'Listing is eligible to submit for verification at property finder.',
                        'status' => 'success',
                        'response' => $data,
                        'payload' => $payload
                    ]);
                    return true;
                }
                $this->makeListingTimelineLog([
                    'portal_id' => $mainPortal->id,
                    'property_id' => $propertyId,
                    'title' => 'Listing is not eligible to submit for verification at property finder.',
                    'status' => 'failed',
                    'response' => $data,
                    'payload' => $payload
                ]);
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function createUpdatePropertySync($propertyId)
    { //this will be used to create or update property in property finder directly from the propertycontroller
        try {
            $mainPortal = Portal::where("short_name", "PF")->latest()->first();
            $property = Property::whereHas("portals", function ($q) use ($mainPortal) {
                $q->where("portal_id", $mainPortal->id);
            })->where('id', $propertyId)->first();
            if ($property) {
                $user = User::find($property->agent_id);
                $pfUserId = json_decode($user->third_party_ids, true)['pf_public_profile_id'] ?? null;
                if (empty($pfUserId)) {
                    $pfUserId = $this->createUser($user->toArray(), $propertyId);
                    if (!$pfUserId) {

                        $this->makeListingTimelineLog([
                            'portal_id' => $mainPortal->id,
                            'property_id' => $property->id,
                            'title' => 'Agent Account Creation Failed at Property Finder',
                            'description' => 'Failed to create agent account at Property Finder for property ref: ' . $property->ref_no . ' and agent: ' . $user->name,
                            'status' => 'failed',
                            'response' => json_encode($pfUserId) ?? []
                        ]);
                        return; // Exit if user creation failed
                    }
                }
                if ($pfUserId) {
                    $portalListingId = json_decode($property->portal_ids, true)['property_finder_id'] ?? null;
                    $shouldPublish = false;
                    if (!empty($portalListingId)) {
                        $this->updateListing($property->id);
                    } else {
                        $shouldPublish = $this->createListing($property->id);
                    }

                    if ($shouldPublish) {
                        //   $this->propertyFinderService->publishListing($property->id);
                    }
                }
                $result = [
                    "status" => "success",
                    "message" => "Property synced successfully with Property Finder"
                ];
            }
        } catch (\Exception $exception) {
            $result = [
                "status" => "error",
                "message" => "An error occurred while syncing property with Property Finder: " . $exception->getMessage()
            ];

        }
        return $result;
    }

    public function getComplianceDetails($propertyId)
    {
        $property = Property::find($propertyId);
        if (empty($property)) {
            Log::error('Property not found for ID: ' . $propertyId);
            return null;
        }
        if (empty($property->rera_permit_number)) {
            Log::error('RERA permit number not found for Property ID: ' . $propertyId);
            return null;
        }
        if ($property->for == 'Residential for sale' || $property->for == 'Commercial for sale') {
            $for = 'Sale';
        } else if ($property->for == 'Residential for rent' || $property->for == 'Commercial for rent') {
            $for = 'Rent';
        }

        try {
            $url = 'https://atlas.propertyfinder.com/v1/compliances/' . $property->rera_permit_number . '/813677?permitType=' . $for;

            $token = $this->getPFToken();

            $headers = [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ];
            $response = Http::withHeaders($headers)->get($url);
            print_r($response->body());
            if ($response->status() == 401) {
                Log::error('Property Finder token expired while fetching compliance details for permit number: ' . $property->rera_permit_number);
                return null;
            }
            if ($response->status() == 400) {
                Log::error('Property Finder API returned a 400 error while fetching compliance details for permit number: ' . $property->rera_permit_number, [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }
            if ($response->failed()) {
                Log::error('Failed to get compliance details for permit number: ' . $property->rera_permit_number, [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }
            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['data']) && !empty($data['data'])) {
                    return $data['data'];
                } else {
                    Log::info('No compliance data found for permit number: ' . $property->rera_permit_number);
                    return null;
                }
            }
        } catch (\Exception $e) {
            Log::error('Error in getComplianceDetails: ' . $e->getMessage());
            return null;
        }

    }
    public function getComplianceDetails2($permit_number,$company_license_number, $for)
    {


        try {
            $url = 'https://atlas.propertyfinder.com/v1/compliances/' . $permit_number . '/813677?permitType=' . $for;

            $token = $this->getPFToken();

            $headers = [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ];
            $response = Http::withHeaders($headers)->get($url);

            if ($response->status() == 401) {
                Log::error('Property Finder token expired while fetching compliance details for permit number: ' . $permit_number);
                return null;
            }
            if ($response->status() == 400) {
                Log::error('Property Finder API returned a 400 error while fetching compliance details for permit number: ' .$permit_number, [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }
            if ($response->failed()) {
                Log::error('Failed to get compliance details for permit number: ' . $permit_number, [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }
            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['data']) && !empty($data['data'])) {
                    return $data['data'];
                } else {
                    Log::info('No compliance data found for permit number: ' . $permit_number);
                    return null;
                }
            }
        } catch (\Exception $e) {
            Log::error('Error in getComplianceDetails: ' . $e->getMessage());
            return null;
        }

    }
    //get leads from pf api end point = https://atlas.propertyfinder.com/v1/leads
    public function getLeadsFromPf(){
        $res = $this->getPfAccessToken();
        if(!$res || empty($res['token'])) {
            Log::error('Failed to get Property Finder access token for leads');
            return [];
        }
        $token = $res['token'];
        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ];
        $url = 'https://atlas.propertyfinder.com/v1/leads';
        $params = [
            'page' => 1,
            'perPage' => 100,
        ];
        try {
            $response = Http::withHeaders($headers)->get($url, $params);
            if ($response->status() == 401) {
                Log::error('Property Finder token expired while fetching leads');
                return [];
            }
            if ($response->failed()) {
                Log::error('Failed to get leads from Property Finder', [
                    'status' => $response->status(),
                    'body' => $response->json()
                ]);
                return [];
            }
            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['data']) && !empty($data['data'])) {
                    return json_encode($data['data']);
                } else {
                    Log::info('No leads found in Property Finder response');
                    return [];

                }
            }
        } catch (\Exception $e) {
            Log::error('Error in getLeads: ' . $e->getMessage());
            return [];
        }
    }

    public function getUpgrades($propertyId)
{
    $property = Property::find($propertyId);
    if (empty($property)) {
        Log::error('Property not found for ID: ' . $propertyId);
        return null;
    }
    $pf_listing_id = json_decode($property->portal_ids, true)['property_finder_id'] ?? null;

    if (empty($pf_listing_id)) {
        Log::error('Property Finder ID not found for Property ID: ' . $propertyId);
        return null;
    }
    try {
        $url = $this->listingBaseUrl.'/'. $pf_listing_id . '/upgrades';

        $token = $this->getPFTokenBasedOnPropertyV2($property);

        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ];
        $response = Http::withHeaders($headers)->get($url);

        if ($response->status() == 401) {
            Log::error('Property Finder token expired while fetching upgrades for permit number: ' . $property->rera_permit_number);
            return null;
        }
        if ($response->status() == 400) {
            Log::error('Property Finder API returned a 400 error while fetching upgrades for permit number: ' . $property->rera_permit_number, [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            return null;
        }
        if ($response->failed()) {
            Log::error('Failed to get upgrades for permit number: ' . $property->rera_permit_number, [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            return null;
        }
        if ($response->successful()) {
            $data = $response->json();
            if (isset($data['options']) && !empty($data['options'])) {
                return $data['options'];
            } else {
                Log::info('No upgrade data found for permit number: ' . $property->rera_permit_number);
                return null;
            }
        }
    } catch (\Exception $e) {
        Log::error('Error in getUpgrades: ' . $e->getMessage());
        return null;
    }


    }

    public function upgradeListing($params){
        $property = Property::find($params['property_id']);
        if (empty($property)) {
            Log::error('Property not found for ID: ' . $params['property_id']);
            return null;
        }
        $pf_listing_id = json_decode($property->portal_ids, true)['property_finder_id'] ?? null;

        if (empty($pf_listing_id)) {
            Log::error('Property Finder ID not found for Property ID: ' . $params['property_id']);
            return null;
        }
        try {
            $url = $this->listingBaseUrl.'/'. $pf_listing_id . '/upgrades';
            $token = $this->getPFTokenBasedOnPropertyV2($property);

            $headers = [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ];
            $payload = [
                'featureId' => $params['featureId'],
                'productId' => $params['productId'] ?? null,
                'renewalEnabled' => $params['automatic_renewal'] ?? false,
            ];
            $response = Http::withHeaders($headers)->post($url, $payload);

            if ($response->status() == 401) {
                Log::error('Property Finder token expired while upgrading listing: ' . $response->json());
                $token = $this->getPFTokenBasedOnPropertyV2($property, true);
                $headers['Authorization'] = 'Bearer ' . $token;
                $response = Http::withHeaders($headers)->post($url, $payload);
            }
            if ($response->failed()) {
                Log::error('Failed to upgrade listing: ' . $response->json());

                $this->makeListingTimelineLog(
                    [
                        'portal_id' => $property->portals()->where('short_name', 'PF')->first()->id ?? null,
                        'property_id' => $property->id,
                        'title' => 'Failed to upgrade listing at Property Finder',
                        'status' => 'failed',
                        'response' => $response->json(),
                        'payload' => $payload
                    ]
                );


                return null;
            }
            if ($response->successful()) {
                return true;
            }
        } catch (\Exception $e) {
            Log::error('Error in upgradeListing: ' . $e->getMessage());
            return null;
        }
    }

    /*
     * Documentation: https://api-docs.propertyfinder.net/enterprise-api/index.html#tag/Users/paths/~1v1~1users~1/get
     */
    public function findUserByField($value, $field = 'search', $entity = "secondary"): ?array
    {
        if (empty($value)) {
            return null;
        }
        try {
            $token = $this->getPfAccessTokenV2($entity);
            $headers = [
                'Authorization' => 'Bearer ' . $token["token"],
                'Content-Type' => 'application/json',
            ];
            $response = Http::withHeaders($headers)->get($this->userBaseUrl, [
                "$field" => $value,
            ]);


            if ($response->status() == 401) {
                $token = $this->getPfAccessTokenV2($entity, true);
                $headers = [
                    'Authorization' => 'Bearer ' . $token['token'],
                    'Content-Type' => 'application/json',
                ];
                $response = Http::withHeaders($headers)->get($this->userBaseUrl, [
                    "$field" => $value,
                ]);
            }

            if ($response->failed()) {
                return null;
            }

            if ($response->successful()) {
                $data = $response->json()['data'];
                if (!empty($data[0])) {
                    return $data[0];
                }
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function checkIfUserHasActiveCompliance($data, $entity = "secondary")
    {
        $pfUserId = json_decode($data['third_party_ids'], true)['pf_public_profile_id'] ?? null;
        $pfUserData = $this->findUserByField($pfUserId, 'publicProfileId', $entity);
        if (empty($pfUserData)) {
            $pfUserData = $this->findUserByField($data['email'], 'search', $entity);
        }
        if (empty($pfUserData)) {
            return false;
        }

        $hasValidCompliance = collect($pfUserData['publicProfile']['compliances'])->firstWhere('status', 'valid') != null;

        if ($hasValidCompliance) {
            $this->updateThirdPartyIds($data['id'], [
                'pf_private_id' => $pfUserData['id'],
                'pf_v2_user_id' => $pfUserData['publicProfile']['id'],
                'pf_public_profile_id' => $pfUserData['publicProfile']['id'],
            ]);
            return true;
        }

        return false;
    }
}
