<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ContactResource;
use App\Http\Resources\PropertyPhotosResource;
use App\Http\Resources\Property\PropertyPortalResource;
use App\Http\Resources\Property\PropertyCategoryResource;
use App\Http\Resources\Property\PropertyBedResourceV2;
use App\Http\Resources\Property\PropertyBathResourceV2;
use App\Http\Resources\Property\PropertyAgentResource;

class ListingResourceDetailed extends JsonResource
{
    private static $data;
    private static $withoutUnitNo;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $data = self::$data;
        $withoutUnitNo = self::$withoutUnitNo;
        if ($this->for == "Residential for sale" || $this->for == "Commercial for sale") {

            $offering_type = $this->for == "Commercial for sale" ? 'CS' : 'RS';
            $rental_period = null;
        } else {

            $offering_type = $this->for == "Commercial for rent" ? 'CR' : 'RR';
            $rental_period = "Yearly";
        }
        $category = new PropertyCategoryResource($this->propertyCategory);
        $owner = new ContactResource($this->owner);

        $beds = ($this->bed ? new PropertyBedResourceV2($this->bed) : null);
        $bath = ($this->bath ? new PropertyBathResourceV2($this->bath) : null);
        $description = strip_tags($this->description, '<br>');
        $updateDescription = str_replace("<br />", "\r\n", $description);

        $amenities = array();
        $amenities_latest_data = array();
        foreach ($this->amenities ?? [] as $feature) {
            array_push($amenities, $feature?->amenity?->portals?->first()?->title);
            $updatedData["id"] = $feature?->amenity?->id;
            $updatedData["title"] = $feature?->amenity?->title;
            $updatedData["icon"] = $feature?->amenity?->thumbnail;
            array_push($amenities_latest_data, $updatedData);
        }

        // check auth id and agent id is same
        // $authId = auth()->user()->id;
        $agentId = $this->agent_id;
        $returnData = [
            'id' => $this->id,
            'title' => $this->title,
            'reference_no' => $this->ref_no,
            'for' => $this->for,
            'property_category_id' => $this->property_category_id,
            'property_category' => $category,
            'city' => $this->community?->region?->name ?? null,
            'region_id' => $this->community?->region?->id ?? null,
            'community' => $this?->community?->name ?? null,
            'community_id' => $this?->community_id ?? null,
            'sub_community' => $this?->subCommunity?->name ?? null,
            'sub_community_id' => $this?->sub_community_id ?? null,
            'community_property' => $this?->communityProperty?->name ?? null,
            'community_property_id' => $this?->community_property_id ?? null,
            'built_up_area' => $this->built_up_area,
            'plot_size' => $this->plot_size,
            'bed_id' => $this->bed_id ?? null,
            'beds' => $beds ?? null,
            'bath_id' => $this->bath_id ?? null,
            'baths' => $bath ?? null,
            'furnished' => $this->furnished,
            'price' => $this?->price?->price,
            'price_on_application' => $this?->price?->price_on_application ? 'Yes' : 'No',
            'down_payment_price' => $this->down_payment_price,
            'payment_method' => $this->payment_method,
            // first image
            'first_image' => $this->first_image,

            'full_location' => ($this?->communityProperty ? $this?->communityProperty?->path_name : $this?->subCommunity?->path_name) ?? null,
            'latitude' => ($this?->communityProperty ? $this?->communityProperty?->latitude : $this?->subCommunity?->latitude) ?? null,
            'longitude' => ($this?->communityProperty ? $this?->communityProperty?->longitude : $this?->subCommunity?->longitude) ?? null,

            'agent_id' => $this->agent_id,
            'agent' => new PropertyAgentResource($this->agent) ?? null,
            'owner' =>  $owner,
            'property_type' => $category ? $category->portals->first()->title : null,


            'last_update' => date('Y-m-d H:i:s', strtotime($this->updated_at)),
            'villa_type' => $this->villa_type,
            'description' => str_replace("<br>", "\r\n", $updateDescription) ?? '',
             'offering_type' => $offering_type,


            'rental_period' => $rental_period,
            'cheques' => $this->cheques,
            'completion_status' => $this->completion_status,
            'rera_permit_number' => $this->rera_permit_number,
            'dewa_number' => $this->dewa_number,




            'street' => $this->street,
            'floor' => $this->floor,
            'view' => $this->view,
            'property_status_id' => $this->property_status_id,
            'parking' => $this->parking,
            'availability_date' => $this->availability_date,

            'featured' => $this->featured,
            'exclusive' => $this->exclusive,
            'hot' => $this->hot,
            'build_year' => $this->build_year,
            'publish status' => $this->publish_status,
            'occupancy_status' => $this->occupancy_status,
            'video_link' => $this->video_link,
            'video_360_link' => $this->video_360_link,
            'qr_link' => $this->qr_link,
            'rera_validation_url' => $this->rera_validation_url,
            'title_deed_text' => $this->title_deed_text,

            'developer' => $this?->developer?->short_name ?? $this?->details?->developer ?? null,
            'floor_plan' => $this->floorPlan,
            'amenities_with_icon' => $amenities_latest_data,
            'amenities' => $amenities,
            'contract_start' => $this->contract_start,
            'contract_end' => $this->contract_end,
            'contract_number' => $this->contract_number,
            'rented_at' => $this->rented_at,
            'rented_until' => $this->rented_until,
            'next_available' => $this->next_available,
            'remind' => $this->remind,
            'availability_date' => $this->availability_date,
            'photos' => PropertyPhotosResource::collection($this->photos),
            'signedListingForm' => new PropertyPhotosResource($this->signedListingForm),
            'ownerPassportCopy' => new PropertyPhotosResource($this->ownerPassportCopy),
            'titleDeed' => new PropertyPhotosResource($this->titleDeed),
            'otherdocs' => PropertyPhotosResource::collection($this->otherDocuments),
            'portals' => $this->portals ? PropertyPortalResource::collection($this->portals) : null,

        ];
        if (!$withoutUnitNo) {
            $returnData['unit_no'] = $this->unit_no;
        }
        return $returnData;

    }

    public static function customCollection($resource, $data, $withoutUnitNo = true): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        self::$data = $data;
        self::$withoutUnitNo = $withoutUnitNo;
        return parent::collection($resource);
    }
}
