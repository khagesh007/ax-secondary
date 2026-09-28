<?php

namespace App\Http\Resources\Property;

use App\Models\PropertyFeatures;
use App\Models\PropertyAmenity;


use App\Http\Resources\PropertyPhotosResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Property\PropertyAmenityResource;

class PropertyResource extends JsonResource
{
    private static $data;
    private static $withoutUnitNo;
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
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

        $beds = ($this->bed ? new PropertyBedResource($this->bed): null);
        $bath = ($this->bath ? new PropertyBathResource($this->bath):null);
        $description = strip_tags($this->description, '<br>');
        $updateDescription = str_replace("<br />", "\r\n", $description);

        $amenities = array();
        $amenities_latest_data = array();
        foreach($this->amenities ?? [] as $feature){
            array_push($amenities, $feature?->amenity?->portals?->first()?->title);
            $updatedData["title"] = $feature?->amenity?->title;
            $updatedData["icon"] = $feature?->amenity?->thumbnail;
            array_push($amenities_latest_data, $updatedData);
        }

        $returnData = [
            'id' => $this->id,
            'reference_no' => $this->ref_no,
            'last_update'  => date('Y-m-d H:i:s',strtotime($this->updated_at)),
            'title' => $this->title,
            'description' => str_replace("<br>", "\r\n", $updateDescription) ?? '',
            'property_type' => $category?->portals->first()?->title ?? null,
            'offering_type' => $offering_type,
            'price' => $this?->price?->price,
            'price_on_application' => $this?->price?->price_on_application ? 'Yes': 'No',
            'rental_period'     => $rental_period,
            'cheques'       => $this->cheques,
            'completion_status' => $this->completion_status == 'Off plan' ? 'off-plan' : 'completed',
            'rera_permit_number' => $this->rera_permit_number,
            'dewa_number' => $this->dewa_number,
            'beds' => $beds?->portals?->first()?->name ?? null,
            'baths' => $bath?->portals?->first()?->name ?? null,
            'city' => $this->community?->region?->name ?? null,
            'community' => $this?->community?->name ?? null,
            'sub_community' => $this?->subCommunity?->name ?? null,
            'photos' => $this->photos ? PropertyPhotosResource::collection($this->photos) : null,
            'community_property' => $this?->communityProperty?->name ?? null,
            'full_location' => ($this?->communityProperty ? $this?->communityProperty?->path_name : $this?->subCommunity?->path_name) ?? null,
            'latitude' => ($this?->communityProperty ? $this?->communityProperty?->latitude : $this?->subCommunity?->latitude) ?? null,
            'longitude' => ($this?->communityProperty ? $this?->communityProperty?->longitude : $this?->subCommunity?->longitude) ?? null,
            'built_up_area' => $this->built_up_area,
            'plot_size' => $this->plot_size,
            'street' => $this->street,
            'floor' => $this->floor,
            'view' => $this->view,
            'publish_status' => $this->publish_status,
            'property_status_id' => $this->property_status_id,
            'parking' => $this->parking,
            'availability_date' => $this->availability_date,
            'furnished' => $this->furnished,
            'featured' => $this->featured,
            'exclusive' => $this->exclusive,
            'hot' => $this->hot,
            'video_link' => $this->video_link,
            'video_360_link' => $this->video_360_link,
            'qr_link' => $this->qr_link,
            'rera_validation_url' => $this->rera_validation_url,
            'title_deed_text' => $this->title_deed_text,
            'agent' => new PropertyAgentResource($this->agent) ?? null,
            'developer' =>  $this?->developer?->short_name ?? $this?->details?->developer ?? null,
            'floor_plan' => $this->floor_plan,
            'amenities_with_icon' => $amenities_latest_data,
            'amenities' => $amenities,
            // 'amenities_with_title' => $amenities_with_title,
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
