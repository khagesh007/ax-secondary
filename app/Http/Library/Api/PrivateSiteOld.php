<?php

namespace App\Http\Library\Api;
class PrivateSiteOld
{
    protected $properties;
    protected $mainPortal;
    protected $portal;

    public function __construct($properties, $mainPortal)
    {
        $this->properties = $properties;
        $this->portal = $mainPortal;
    }

    public function execute()
    {
        $data = [];
        if (!empty($this->properties)) {
            $i = 0;
            foreach ($this->properties as $property) {
                $data[$i]['id'] = $property['id'];

                $data[$i]['ref_no'] = $property['ref_no'];
                $data[$i]['title'] = $property['title'];
                $data[$i]['description'] = $property['description'];
                if (!empty($property['property_category'])) {
                    foreach ($property->property_category?->portals as $portalCat) {
                        if ($this->portal->id == $portalCat->portal_id) {
                            $data[$i]['category'] = $portalCat->title;
                        }
                    }
                } else {
                    $data[$i]['category'] = null;
                }

                if ($property['for'] == "Residential for sale" || $property['for'] == "Commercial for sale") {

                    $data[$i]['offering_type'] = $property['for'] == "Commercial for sale" ? 'CS' : 'RS';
                } else {

                    $data[$i]['offering_type'] = $property['for'] == "Commercial for rent" ? 'CR' : 'RR';
                }
                $data[$i]['rera_permit_number'] = $property['rera_permit_number'];
                $data[$i]['dewa_number'] = $property['dewa_number'];


                $data[$i]['price'] = !empty($property['price']) ? $property['price']['price'] : null;

                if (!empty($property['bed'])) {
                    foreach ($property->bed?->portals as $port) {
                        if ($this->portal->id == $port->portal_id) {
                            $data[$i]['beds'] = $port->name;
                        }
                    }
                } else {
                    $data[$i]['beds'] = null;
                }
                if (!empty($property['bath'])) {
                    foreach ($property->bath?->portals as $port) {
                        if ($this->portal->id == $port->portal_id) {
                            $data[$i]['bath'] = $port->name;
                        }
                    }
                } else {
                    $data[$i]['bath'] = null;
                }


                $data[$i]['built_up_area'] = $property['built_up_area'];
                $data[$i]['plot_size'] = $property['plot_size'];
                $data[$i]['street'] = $property['street'];
                $data[$i]['floor'] = $property['floor'];
                $data[$i]['view'] = $property['view'];
                $data[$i]['parking'] = $property['parking'];

                if ($property['completion_status'] == 'Off plan') {
                    $data[$i]['completion_status'] = 'off-plan';
                } else {
                    $data[$i]['completion_status'] = 'completed';
                }
                $data[$i]['availability_date'] = $property['availability_date'];
                $data[$i]['furnished'] = $property['furnished'];
                $data[$i]['featured'] = $property['featured'];
                $data[$i]['exclusive'] = $property['exclusive'];
                $data[$i]['hot'] = $property['hot'];
                $data[$i]['video_link'] = $property['video_link'];
                $data[$i]['video_360_link'] = $property['video_360_link'];
                $data[$i]['qr_link'] = $property['qr_link'];
                $data[$i]['title_deed_text'] = $property['title_deed_text'];

                $data[$i]['community'] = !empty($property['community']) ? $property['community']['name'] : null;
                $data[$i]['sub_community'] = !empty($property['sub_community']) ? $property['sub_community']['name'] : null;
                $data[$i]['community_property'] = !empty($property['community_property']) ? $property['community_property']['name'] : null;
                $data[$i]['full_location'] = !empty($property['community_property']) ? $property['community_property']['path_name'] : null;
                $data[$i]['latitude'] = !empty($property['community_property']) ? $property['community_property']['latitude'] : null;
                $data[$i]['longitude'] = !empty($property['community_property']) ? $property['community_property']['longitude'] : null;
              //  $data[$i]['developer'] = $property['details']['developer'];

                if ($property->agent->pp_enabled) {
                    $data[$i]['agent'] = [
                        "name" => $property->agent->pp_name,
                        "first_name" => null,
                        "last_name" => null,
                        "email" => $property->agent->email,
                        "secondary_email" => $property->agent->secondary_email,
                        "mobile" => $property->agent->mobile,
                        "license_no" => $property->agent->pp_license_no,
                        "photo" => $property->agent->pp_photo,

                    ];
                } else {
                    $data[$i]['agent'] = [
                        "name" => $property->agent->full_name,
                        "first_name" => $property->agent->first_name,
                        "last_name" => $property->agent->last_name,
                        "email" => $property->agent->email,
                        "secondary_email" => $property->agent->secondary_email,
                        "mobile" => $property->agent->mobile,
                        "license_no" => $property->agent->license_no,
                        "photo" => $property->agent->image ?? null,
                    ];
                }
                $data[$i]['amenities'] = [];
                if (!empty($property['amenities']))
                    foreach ($property['amenities'] as $feature) {

                        foreach ($feature?->amenity?->portals as $portals) {
                            if ($this->portal->id == $portals->portal_id) {
                                $data[$i]['amenities'][] = $portals->title;
                            }
                        }
                    }
                $data[$i]['photos'] = [];
                foreach ($property?->photos as $photo) {
                    $data[$i]['photos'][] = $photo->path;
                }
                $data[$i]['floor_plan'] = $property['floor_plan'];
                $i++;
            }
        }
        return $data;
    }
}

// agent full photo email phone number, rera permit
