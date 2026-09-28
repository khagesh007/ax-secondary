<?php

namespace App\Http\Controllers\Api;

use App\Models\Portal;
use App\Models\Property;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\ArrayToXml\ArrayToXml;
use DOMDocument;
use DOMImplementation;
use DOMNode;
use Exception;
use Illuminate\Support\Str;

class XmlFeedController extends Controller
{
    private static $xml = null;
    private $last_update = null;
    private $listing_count = null;

    public function propertyFinderRevamped(Request $request)
    {
        try{
        $mainPortal = Portal::where("slug", "property-finder")->latest()->first();
        if (is_null($mainPortal)) {
            abort(404);
        }
        abort_if($mainPortal->xml_url != $request->key, 404);

        $last_update = Property::whereHas("portals", function ($q) use ($mainPortal) {
            $q->where("portal_id", $mainPortal->id);
        })->orderBy('updated_at', 'desc')
        ->select('updated_at')->first()->toArray();

        $listing_count = Property::whereHas("portals", function ($q) use ($mainPortal) {
            $q->where("portal_id", $mainPortal->id);
        })->where(['publish_status' => 'PUBLISHED'])->count();

        $prop = [];

        $completionStatusMapping = [
            'Ready Primary' => 'completed_primary',
            'Ready Secondary' => 'completed',
            'Off-Plan Primary' => 'off_plan_primary',
            'Off-Plan Secondary' => 'off_plan',
        ];

        $furnishedMapping = [
            'FURNISHED' => 'Yes',
            'SEMI_FURNISHED' => 'Partly',
        ];


        $pp = Property::whereHas("portals", function ($q) use ($mainPortal) {
                $q->where("portal_id", $mainPortal->id);
            })
            ->where(['publish_status' => 'PUBLISHED'])
            ->with([
                'agent' => function($q){
                    $q->select('id','name','first_name','last_name','pp_enabled','pp_name','pp_photo', 'pp_license_no', 'email','secondary_email','license_no','mobile','bio');
                },
                'details',
                'propertyCategory' => function ($q) {
                    $q->select('id');
                }, 
                'propertyCategory.portals'=> function($q) use ($mainPortal){
                    $q->where('portal_id', $mainPortal->id)->select('id', 'title','property_category_id', 'portal_id');
                },
                'bed' => function ($q) {
                    $q->select('id');
                },
                'bed.portals' => function($q) use ($mainPortal){
                    $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bed_id', 'portal_id');
                },'developer', 
                'price'=> function ($q) {
                    $q->select('id','property_id','price','prices','price_on_application');
                },
                'bath' => function ($q) {
                    $q->select('id');
                }, 
                'bath.portals' => function($q) use ($mainPortal){
                    $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bath_id', 'portal_id');
                }, 
                'amenities' => function ($q) use ($mainPortal) {
                        $q->whereHas('amenity.portals', function($q) use ($mainPortal) {
                              $q->where('portal_id', '=', $mainPortal->id);
                          });
                    },
                    'amenities.amenity.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', '=', $mainPortal->id)
                          ->where('title', '!=', '');
                    },
                'community.region', 'subCommunity', 'communityProperty',
                'photos' => function($q){
                    $q->select('id','property_id','path','title','updated_at');
                }, 
                'floorPlan'])
            ->orderBy('properties.created_at', 'desc')
            ->get();

        foreach ($pp as $key => $value) {
            $data['@attributes'] = ['last_update' => date('Y-m-d H:i:s', strtotime($value['updated_at']))];
            $data['reference_number'] = $value['ref_no'];
            $data['permit_number'] = $value['rera_permit_number'] ?? '';
            $data['price'] = [];
            if ($value['for'] == "Residential for sale" || $value['for'] == "Commercial for sale") {
                $data['price'] = $value['price']["price"] ?? 0;
                $data['offering_type'] = $value['for'] == "Commercial for sale" ? 'CS' : 'RS';
            } else {
                $data['price']['yearly'] = $value['price']["price"] ?? 0;
                $data['offering_type'] = $value['for'] == "Commercial for rent" ? 'CR' : 'RR';
                $data['cheques'] = $value['cheques'] ?? null;
            }
            $data['property_type'] = $value['propertyCategory']['portals'][0]["title"] ?? "";
            $data['price_on_application'] = 'No';

            $data['availability_date'] = $value?->availability_date;
            $data['title_deed']['@cdata'] = $value?->title_deed_text ?? '';
            $data['city']['@cdata'] = $value['community']['region']['name'] ?? 'Dubai';
            $data['community']['@cdata'] = $value['community']['name'] ?? '';
            $data['sub_community']['@cdata'] = $value['subCommunity']['name'] ?? '';

            if(($value?->communityProperty?->name ?? null) && (!$value?->communityProperty?->default_use)){
                $data['property_name']['@cdata'] =  '';
            }else{
                $data['property_name']['@cdata'] = $value['communityProperty']['name'] ?? '';
            }



            $data['title_en']['@cdata'] = $value['title'] ?? '';
            //$data['description_en']['@cdata'] = $value['description'] ?? '';

            $description = strip_tags($value['description'], '<br>');
            $data['description_en']['@cdata'] = str_replace("<br>", "\r\n", $description) ?? '';

            $data['completion_status'] = $completionStatusMapping[$value['completion_status']] ?? 'completed';
            $data['floor'] = $value['floor'] ?? '';
            $data['parking'] = $value['parking'] ?? 0;
            $data['furnished'] = $furnishedMapping[$value['furnished']] ?? 'No';
            $data['size'] = $value['built_up_area'] ?? '';
            $data['bedroom'] = $value["bed"]["portals"][0]["name"] ?? 0;
            $data['bathroom'] = $value["bath"]["portals"] [0]["name"] ?? 0;
            $data['view'] = $value['view'] ?? '';
            $privateAmenities = [];
            $commercialAmenities = [];
            $features = [];

            foreach ($value["amenities"] ?? [] as $amenity) {
                $amenityType = $amenity["amenity"]["amenity_type"]??[];
                if ($amenityType == "private") {
                    $privateAmenities[] = $amenity["amenity"]["portals"][0]['title'];
                }
                if ($amenityType == "commercial") {
                    $commercialAmenities[] = $amenity["amenity"]["portals"][0]['title'];
                }
                $features[] = $amenity["amenity"]["portals"][0]["title"];
            }
            $data['private_amenities'] = implode(",", $privateAmenities);
            $data['commercial_amenities'] = implode(",", $commercialAmenities);
            $data['amenities'] = implode(',', $features);

            //$data['view'] = $value['view'] ?? '';

            if ($value['agent']) {
                $data['agent']['id'] = $value['agent']['id'];

                if($value['agent']['pp_enabled']){
                    $data['agent']['name']['@cdata'] = $value['agent']['pp_name'];
                    $data['agent']['photo'] = $value['agent']['pp_photo'] ?? '';
                    $data['agent']['license_no'] = $value['agent']['pp_license_no'] ?? ''; 
                }else{
                    $data['agent']['name']['@cdata'] = $value['agent']['first_name'].' '.$value['agent']['last_name'];
                    $data['agent']['photo'] = $value['agent']['thumb_url'] ?? '';
                    $data['agent']['license_no'] = $value['agent']['license_no'] ?? '';
                }
                if($value['agent']['secondary_email'] != ""){
                    $data['agent']['email'] = $value['agent']['secondary_email'] ?? '';
                }else{
                    $data['agent']['email'] = $value['agent']['email'] ?? '';
                }
                $number = str_replace("+971", "0", $value['agent']['mobile']) ?? '';
                $data['agent']['phone'] = str_replace(' ', '', $number);
                $data['agent']['info'] = $value['agent']['bio'] ?? '';

            }
            $data['developer'] = $value['developer']['short_name'] ??  $value['details']['developer'] ?? null;
            $data['photo'] = [];
            if ($value['photos']) {
                $url = [];
                foreach ($value['photos'] as $key => $photo) {
                    $dd['@attributes'] = ['last_updated' => date('Y-m-d H:i:s',strtotime($photo['updated_at'])), 'watermark' => 'yes'];
                    $dd['@value'] = $photo['path'];
                    $url['url'][] = $dd;
                }
                $data['photo'] = $url;
            }

            $data['video_tour_url'] = $value['video_link'] ?? '';
            $data['view360'] = $value['video_360_link'] ?? '';

            $data['floor_plan'] = [];

            if ($value['floor_plan']) {
                $url = [];
                $dd['@attributes'] = ['last_updated' => $value['floor_plan']['updated_at'], 'watermark' => 'no'];
                $dd['@value'] = $value['floor_plan']['path']?? null;
                $url['url'][] = $dd;
                $data['floor_plan'] = $url;
            }

            $prop['property'][] = $data;
            if (!empty($value["communityProperty"])){
                $data['geopoints'] = $value["communityProperty"]["latitude"] .", ". $value["communityProperty"]["longitude"];
            }elseif (!empty($value["subCommunity"])){
                $data['geopoints'] = $value["subCommunity"]["latitude"] .", ". $value["subCommunity"]["longitude"];
            }else{
                $data['geopoints'] = '';//$value['latitude'] . ',' . $value['longitude'];
            }
        }

        $last_update = $last_update['updated_at'];
        $this->listing_count = $listing_count;
        $pfObj['last_update'] = date('Y-m-d H:i:s', strtotime($last_update));
        $pfObj['listing_count'] = $listing_count;

        $xml = $this->createXML($pfObj, 'list', $prop);
        $xxx = $xml->saveXML();

        return \Response::make($xxx, '200')->header('Content-Type', 'text/xml');
        }catch(\Exception $e){
            
        }
    }
    public function propertyFinder(Request $request)
    {
        
        $mainPortal = Portal::where("name", "Property Finder")->latest()->first();
        if (is_null($mainPortal)) {
            abort(404);
        }
        abort_if($mainPortal->xml_url != $request->key, 404);

        $last_update = Property::whereHas("portals", function ($q) use ($mainPortal) {
            $q->where("portal_id", $mainPortal->id);
        })->orderBy('updated_at', 'desc')
            ->select('updated_at')->first()->toArray();

        $listing_count = Property::whereHas("portals", function ($q) use ($mainPortal) {
            $q->where("portal_id", $mainPortal->id);
        })->where(['publish_status' => 'PUBLISHED'])->count();

        $prop = [];

        $pp = Property::whereHas("portals", function ($q) use ($mainPortal) {
                $q->where("portal_id", $mainPortal->id);
            })
            ->where(['publish_status' => 'PUBLISHED'])
            ->with([
                'agent' => function($q){
                    $q->select('id','name','first_name','last_name','pp_enabled','pp_name','pp_photo', 'pp_license_no', 'email','secondary_email','license_no','mobile','bio');
                },
                'details',
                'propertyCategory' => function ($q) {
                    $q->select('id');
                }, 
                'propertyCategory.portals'=> function($q) use ($mainPortal){
                    $q->where('portal_id', $mainPortal->id)->select('id', 'title','property_category_id', 'portal_id');
                },
                'bed' => function ($q) {
                    $q->select('id');
                },
                'bed.portals' => function($q) use ($mainPortal){
                    $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bed_id', 'portal_id');
                },'developer', 
                'price'=> function ($q) {
                    $q->select('id','property_id','price','prices','price_on_application');
                },
                'bath' => function ($q) {
                    $q->select('id');
                }, 
                'bath.portals' => function($q) use ($mainPortal){
                    $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bath_id', 'portal_id');
                }, 
                'amenities' => function ($q) use ($mainPortal) {
                        $q->whereHas('amenity.portals', function($q) use ($mainPortal) {
                              $q->where('portal_id', '=', $mainPortal->id);
                          });
                    },
                    'amenities.amenity.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', '=', $mainPortal->id)
                          ->where('title', '!=', '');
                    },
                'community.region', 'subCommunity', 'communityProperty',
                'photos' => function($q){
                    $q->select('id','property_id','path','title','updated_at');
                }, 
                'floorPlan'])
            ->orderBy('properties.created_at', 'desc')
            ->get()
            ->toArray();
        foreach ($pp as $key => $value) {
            $data['@attributes'] = ['last_update' => date('Y-m-d H:i:s',strtotime($value['updated_at']))];
            $data['reference_number'] = $value['ref_no'];
            $data['permit_number'] = $value['rera_permit_number'] ?? '';
            $data['price'] = [];
            if ($value['for'] == "Residential for sale" || $value['for'] == "Commercial for sale") {
                $data['price'] = $value['price']["price"] ?? 0;
                $data['offering_type'] = $value['for'] == "Commercial for sale" ? 'CS' : 'RS';
            } else {
                $data['price']['yearly'] = $value['price']["price"] ?? 0;
                $data['offering_type'] = $value['for'] == "Commercial for rent" ? 'CR' : 'RR';
                $data['cheques'] = $value['cheques'] ?? null;
            }

            $data['property_type'] = $value['property_category']['portals'][0]["title"] ?? "";
            $data['price_on_application'] = $value['price']['price_on_application'] ? 'Yes': 'No';
            $data['availability_date'] = $value['availability_date'];
            $data['title_deed']['@cdata'] = $value['title_deed_text'] ?? '';
            $data['city']['@cdata'] = $value['community']['region']['name'] ?? 'Dubai';
            $data['community']['@cdata'] = $value['community']['name'] ?? '';
            $data['sub_community']['@cdata'] = $value['sub_community']['name'] ?? '';
            $data['property_name']['@cdata'] = $value['community_property']['name'] ?? '';
            $data['title_en']['@cdata'] = $value['title'] ?? '';
            $description = strip_tags($value['description'], '<br>');
            $updateDescription = str_replace("<br />", "\r\n", $description);
            $data['description_en']['@cdata'] = str_replace("<br>", "\r\n", $updateDescription) ?? '';
            //$data['description_en']['@cdata'] = $value['description'] ?? '';
            //$data['completion_status'] = $value['completion_status'] ?? "";
            $data['completion_status'] = match ($value['completion_status']) {
                'Ready Primary' => 'completed_primary',
                'Ready Secondary' => 'completed',
                'Off-Plan Primary' => 'off_plan_primary',
                'Off-Plan Secondary' => 'off_plan',
                default => 'completed',
            };
            $data['floor'] = $value['floor'] ?? '';

            $data['parking'] = $value['parking'] ?? 0;

            if ($value['furnished'] == 'FURNISHED') {
                $data['furnished'] = 'Yes';
            } else {
                if ($value['furnished'] == 'SEMI_FURNISHED') {
                    $data['furnished'] = 'Partly';
                } else {
                    $data['furnished'] = 'No';
                }
            }

            $data['size'] = str_replace(",","",$value['built_up_area']) ?? '';
            // $data['bedroom'] = collect($value["bed"]["portals"] ?? [])->where("portal_id",
            //     $mainPortal->id)->first()["name"] ?? 0;

            $data['bedroom'] = $value["bed"]["portals"][0]["name"] ?? 0;
            $data['bathroom'] = $value["bath"]["portals"][0]["name"] ?? 0;

            $data['view'] = $value['view'] ?? '';
            $privateAmenities = [];
            $commercialAmenities = [];
            $features = [];

            foreach ($value["amenities"] ?? [] as $amenity) {
                $amenityType = $amenity["amenity"]["amenity_type"]??[];
                if ($amenityType == "private") {
                    $privateAmenities[] = $amenity["amenity"]["portals"][0]['title'];
                }
                if ($amenityType == "commercial") {
                    $commercialAmenities[] = $amenity["amenity"]["portals"][0]['title'];
                }
                $features[] = $amenity["amenity"]["portals"][0]["title"];
            }
            $data['private_amenities'] = implode(",", $privateAmenities);
            $data['commercial_amenities'] = implode(",", $commercialAmenities);

            $data['amenities'] = implode(',', $features);

            if ($value['agent']) {
                $data['agent']['id'] = $value['agent']['id'];

                if($value['agent']['pp_enabled']){
                    $data['agent']['name']['@cdata'] = $value['agent']['pp_name'];
                    $data['agent']['photo'] = $value['agent']['pp_photo'] ?? '';
                    $data['agent']['license_no'] = $value['agent']['pp_license_no'] ?? ''; 
                }else{
                    $data['agent']['name']['@cdata'] = $value['agent']['first_name'].' '.$value['agent']['last_name'];
                    $data['agent']['photo'] = $value['agent']['thumb_url'] ?? '';
                    $data['agent']['license_no'] = $value['agent']['license_no'] ?? '';
                }
                if($value['agent']['secondary_email'] != ""){
                    $data['agent']['email'] = $value['agent']['secondary_email'] ?? '';
                }else{
                    $data['agent']['email'] = $value['agent']['email'] ?? '';
                }
                $number = str_replace("+971", "0", $value['agent']['mobile']) ?? '';
                $data['agent']['phone'] = str_replace(' ', '', $number);
                $data['agent']['info'] = $value['agent']['bio'] ?? '';

            }
            $data['developer'] = $value['developer']['short_name'] ??  $value['details']['developer'] ?? null;
            $data['photo'] = [];
            if ($value['photos']) {
                $url = [];
                foreach ($value['photos'] as $key => $photo) {
                    $dd['@attributes'] = ['last_updated' => date('Y-m-d H:i:s',strtotime($photo['updated_at'])), 'watermark' => 'yes'];
                    $dd['@value'] = $photo['path'];
                    $url['url'][] = $dd;
                }
                $data['photo'] = $url;
            }

            $data['video_tour_url'] = $value['video_link'] ?? '';
            $data['view360'] = $value['video_360_link'] ?? '';
            $data['floor_plan'] = [];
            if ($value['floor_plan']) {
                $url = [];
                $dd['@attributes'] = ['last_updated' => $value['floor_plan']['updated_at'], 'watermark' => 'no'];
                $dd['@value'] = $value['floor_plan']['path']?? null;
                $url['url'][] = $dd;
                $data['floor_plan'] = $url;
            }
          
            $prop['property'][] = $data;
            if (!empty($value["community_property"])){
                $data['geopoints'] = $value["community_property"]["latitude"] .", ". $value["community_property"]["longitude"];
            }elseif (!empty($value["sub_community"])){
                $data['geopoints'] = $value["sub_community"]["latitude"] .", ". $value["sub_community"]["longitude"];
            }else{
                $data['geopoints'] = '';//$value['latitude'] . ',' . $value['longitude'];
            }
        }

        $last_update = $last_update['updated_at'];
        $this->listing_count = $listing_count;
        $pfObj['last_update'] = date('Y-m-d H:i:s', strtotime($last_update));
        $pfObj['listing_count'] = $listing_count;

        $xml = $this->createXML($pfObj, 'list', $prop);
        $xxx = $xml->saveXML();

        return \Response::make($xxx, '200')->header('Content-Type', 'text/xml');
    }

    public function propertyFinderUpdated(Request $request)
    {
        
        $mainPortal = Portal::where("name", "Property Finder")->latest()->first();
        if (is_null($mainPortal)) {
            abort(404);
        }
        abort_if($mainPortal->xml_url != $request->key, 404);

        $last_update = Property::whereHas("portals", function ($q) use ($mainPortal) {
            $q->where("portal_id", $mainPortal->id);
        })->orderBy('updated_at', 'desc')
            ->select('updated_at')->first()->toArray();

        $listing_count = Property::whereHas("portals", function ($q) use ($mainPortal) {
            $q->where("portal_id", $mainPortal->id);
        })->where(['publish_status' => 'PUBLISHED'])->count();

        $prop = [];

        $pp = Property::whereHas("portals", function ($q) use ($mainPortal) {
                $q->where("portal_id", $mainPortal->id);
            })
            ->where(['publish_status' => 'PUBLISHED'])
            ->with([
                'agent' => function($q){
                    $q->select('id','name','first_name','last_name','pp_enabled','pp_name','pp_photo', 'pp_license_no', 'email','secondary_email','license_no','mobile','bio');
                },
                'details',
                'propertyCategory' => function ($q) {
                    $q->select('id');
                }, 
                'propertyCategory.portals'=> function($q) use ($mainPortal){
                    $q->where('portal_id', $mainPortal->id)->select('id', 'title','property_category_id', 'portal_id');
                },
                'bed' => function ($q) {
                    $q->select('id');
                },
                'bed.portals' => function($q) use ($mainPortal){
                    $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bed_id', 'portal_id');
                },'developer', 
                'price'=> function ($q) {
                    $q->select('id','property_id','price','prices','price_on_application');
                },
                'bath' => function ($q) {
                    $q->select('id');
                }, 
                'bath.portals' => function($q) use ($mainPortal){
                    $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bath_id', 'portal_id');
                }, 
                'amenities' => function ($q) use ($mainPortal) {
                        $q->whereHas('amenity.portals', function($q) use ($mainPortal) {
                              $q->where('portal_id', '=', $mainPortal->id);
                          });
                    },
                    'amenities.amenity.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', '=', $mainPortal->id)
                          ->where('title', '!=', '');
                    },
                'community.region', 'subCommunity', 'communityProperty',
                'photos' => function($q){
                    $q->select('id','property_id','path','title','updated_at');
                }, 
                'floorPlan'])
            ->orderBy('properties.created_at', 'desc')
            ->get();
        foreach ($pp as $key => $value) {
            $data['@attributes'] = ['last_update' => date('Y-m-d H:i:s',strtotime($value?->updated_at))];
            $data['reference_number'] = $value?->ref_no;
            $data['permit_number'] = $value?->rera_permit_number ?? '';
            $data['price'] = [];
            if ($value?->for == "Residential for sale" || $value?->for == "Commercial for sale") {
                $data['price'] = $value?->price?->price ?? 0;
                $data['offering_type'] = $value?->for == "Commercial for sale" ? 'CS' : 'RS';
            } else {
                $data['price']['yearly'] = $value?->price?->price ?? 0;
                $data['offering_type'] = $value?->for == "Commercial for rent" ? 'CR' : 'RR';
                $data['cheques'] = $value['cheques'] ?? null;
            }

            $data['property_type'] = $value?->propertyCategory?->portals?->first()?->title ?? "";
            $data['price_on_application'] = $value?->price?->price_on_application ? 'Yes': 'No';
            $data['availability_date'] = $value?->availability_date;
            $data['title_deed']['@cdata'] = $value?->title_deed_text ?? '';
            $data['city']['@cdata'] = $value?->community?->region?->name ?? 'Dubai';
            $data['community']['@cdata'] = $value?->community?->name ?? '';
            $data['sub_community']['@cdata'] = $value?->subCommunity?->name ?? '';
            $data['property_name']['@cdata'] = $value?->communityProperty?->name ?? '';
            $data['title_en']['@cdata'] = $value?->title ?? '';
            $description = strip_tags($value?->description, '<br>');
            $updateDescription = str_replace("<br />", "\r\n", $description);
            $data['description_en']['@cdata'] = str_replace("<br>", "\r\n", $updateDescription) ?? '';
            //$data['description_en']['@cdata'] = $value['description'] ?? '';
            //$data['completion_status'] = $value['completion_status'] ?? "";
            $data['completion_status'] = match ($value?->completion_status) {
                'Ready Primary' => 'completed_primary',
                'Ready Secondary' => 'completed',
                'Off-Plan Primary' => 'off_plan_primary',
                'Off-Plan Secondary' => 'off_plan',
                default => 'completed',
            };
            $data['floor'] = $value?->floor ?? '';

            $data['parking'] = $value?->parking ?? 0;

            if ($value['furnished'] == 'FURNISHED') {
                $data['furnished'] = 'Yes';
            } else {
                if ($value['furnished'] == 'SEMI_FURNISHED') {
                    $data['furnished'] = 'Partly';
                } else {
                    $data['furnished'] = 'No';
                }
            }

            $data['size'] = str_replace(",","",$value?->built_up_area) ?? '';
            // $data['bedroom'] = collect($value["bed"]["portals"] ?? [])->where("portal_id",
            //     $mainPortal->id)->first()["name"] ?? 0;

            $data['bedroom'] = $value?->bed?->portals?->first()?->name ?? 0;
            $data['bathroom'] = $value?->bath?->portals?->first()?->name ?? 0;

            $data['view'] = $value?->view ?? '';
            $privateAmenities = [];
            $commercialAmenities = [];
            $features = [];

            foreach ($value?->amenities ?? [] as $amenity) {
                $amenityType = $amenity?->amenity?->amenity_type??[];
                if ($amenityType == "private") {
                    $privateAmenities[] = $amenity?->amenity?->portals?->first()?->title ?? '';
                }
                if ($amenityType == "commercial") {
                    $commercialAmenities[] = $amenity?->amenity?->portals?->first()?->title ?? '';
                }
                $features[] = $amenity?->amenity?->portals?->first()?->title ?? '';
            }
            $data['private_amenities'] = implode(",", $privateAmenities);
            $data['commercial_amenities'] = implode(",", $commercialAmenities);

            $data['amenities'] = implode(',', $features);

            if ($value['agent']) {
                $data['agent']['id'] = $value?->agent?->id;

                if($value?->agent?->pp_enabled){
                    $data['agent']['name']['@cdata'] = $value?->agent?->pp_name;
                    $data['agent']['photo'] = $value?->agent?->pp_photo ?? '';
                    $data['agent']['license_no'] = $value?->agent?->pp_license_no ?? ''; 
                }else{
                    $data['agent']['name']['@cdata'] = $value?->agent?->first_name.' '.$value?->agent?->last_name;
                    $data['agent']['photo'] = $value?->agent?->thumb_url ?? '';
                    $data['agent']['license_no'] = $value?->agent?->license_no ?? '';
                }
                if($value?->agent?->secondary_email != ""){
                    $data['agent']['email'] = $value?->agent?->secondary_email ?? '';
                }else{
                    $data['agent']['email'] = $value?->agent?->email ?? '';
                }
                $number = str_replace("+971", "0", $value?->agent?->mobile) ?? '';
                $data['agent']['phone'] = str_replace(' ', '', $number);
                $data['agent']['info'] = $value?->agent?->bio ?? '';

            }
            $data['developer'] = $value?->developer?->short_name ??  $value?->details?->developer ?? null;
            $data['photo'] = [];
            if ($value?->photos) {
                $url = [];
                foreach ($value?->photos ?? [] as $key => $photo) {
                    $dd['@attributes'] = ['last_updated' => date('Y-m-d H:i:s',strtotime($photo?->updated_at)), 'watermark' => 'yes'];
                    $dd['@value'] = $photo?->path;
                    $url['url'][] = $dd;
                }
                $data['photo'] = $url;
            }

            $data['video_tour_url'] = $value?->video_link ?? '';
            $data['view360'] = $value?->video_360_link ?? '';
            $data['floor_plan'] = [];
            if ($value['floor_plan']) {
                $url = [];
                $dd['@attributes'] = ['last_updated' => $value?->floor_plan?->updated_at, 'watermark' => 'no'];
                $dd['@value'] = $value?->floor_plan?->path ?? null;
                $url['url'][] = $dd;
                $data['floor_plan'] = $url;
            }
          
            $prop['property'][] = $data;
            if (!empty($value?->communityProperty)){
                $data['geopoints'] = $value?->communityProperty?->latitude .", ". $value?->communityProperty?->longitude;
            }elseif (!empty($value?->subCommunity)){
                $data['geopoints'] = $value?->subCommunity?->latitude .", ". $value?->subCommunity?->longitude;
            }else{
                $data['geopoints'] = '';//$value['latitude'] . ',' . $value['longitude'];
            }
        }

        $last_update = $last_update['updated_at'];
        $this->listing_count = $listing_count;
        $pfObj['last_update'] = date('Y-m-d H:i:s', strtotime($last_update));
        $pfObj['listing_count'] = $listing_count;

        $xml = $this->createXML($pfObj, 'list', $prop);
        $xxx = $xml->saveXML();

        return \Response::make($xxx, '200')->header('Content-Type', 'text/xml');
    }


    public function dubizzle(Request $request)
    {
        $mainPortal = Portal::where("name", "Dubizzle")->latest()->first();
        if (is_null($mainPortal)) {
            abort(404);
        }
        abort_if($mainPortal->xml_url != $request->key, 404);

        $prop = [];

        $pp = Property::whereHas("portals", function ($q) use ($mainPortal) {
                $q->where("portal_id", $mainPortal->id);
            })
            ->where(['publish_status' => 'PUBLISHED'])
            ->with([
                'agent' => function($q){
                    $q->select('id','name','first_name','last_name','pp_enabled','pp_name','pp_photo', 'pp_license_no', 'email','secondary_email','license_no','mobile','bio');
                },
                'details',
                'propertyCategory' => function ($q) {
                    $q->select('id');
                }, 
                'propertyCategory.portals'=> function($q) use ($mainPortal){
                    $q->where('portal_id', $mainPortal->id)->select('id', 'title','property_category_id', 'portal_id');
                },
                'bed' => function ($q) {
                    $q->select('id');
                },
                'bed.portals' => function($q) use ($mainPortal){
                    $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bed_id', 'portal_id');
                },'developer', 
                'price'=> function ($q) {
                    $q->select('id','property_id','price','prices','price_on_application');
                },
                'bath' => function ($q) {
                    $q->select('id');
                }, 
                'bath.portals' => function($q) use ($mainPortal){
                    $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bath_id', 'portal_id');
                }, 
                'amenities' => function ($q) use ($mainPortal) {
                        $q->whereHas('amenity.portals', function($q) use ($mainPortal) {
                              $q->where('portal_id', '=', $mainPortal->id);
                          });
                    },
                    'amenities.amenity.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', '=', $mainPortal->id)
                          ->where('title', '!=', '');
                    },
                'community.region', 'subCommunity', 'communityProperty',
                'photos' => function($q){
                    $q->select('id','property_id','path','title','updated_at');
                }, 
                'floorPlan'])
            ->orderBy('properties.created_at', 'desc')
            ->get()
            ->toArray();


        foreach ($pp as $key => $value) {
            if ($value['publish_status'] == 'UNPUBLISHED') {
                $data['status'] = 'deleted';
            } else {
                $data['status'] = 'vacant';
            }

            if ($value['for'] == 'Residential for sale' || $value['for'] == 'Commercial for sale') {
                $data['type'] = 'SP';
            } else {
                $data['type'] = 'RP';
            }

            $data['subtype'] = $value['property_category']['portals'][0]['title'] ?? "";

            $data['commercialtype'] = 'IN';
            $data['refno'] = $value['ref_no'];
            $data['title']['@cdata'] = $value['title'] ?? '';
            $data['description']['@cdata'] = strip_tags($value['description']) ?? '';
            $data['size'] = $value['built_up_area'] ?? '';
            $data['sizeunits'] = 'SqFt';
            $data['price'] = $value['price'] ? $value['price']['price'] : 0;
            $data['locationtext']['@cdata'] = $value['community']['name'] ?? '';
            if ($value['for'] == 'Residential for sale' || $value['for'] == 'Commercial for sale') {
                $data['rentispaid'] = '';
            } else {
                $data['rentispaid'] = 'YR';
            }

            if ($value['furnished'] == 'FURNISHED') {
                $data['furnished'] = 1;
            } else {
                $data['furnished'] = 0;
            }

            $data['building']['@cdata'] = $value['community_property']['name'] ?? ($value['sub_community']['name'] ?? "");
            $data['city'] = 2;

            if ($value['for'] == 'Residential for rent' || $value['for'] == 'Commercial for rent') {
                $data['rentpriceterm'] = 'YR';
            }
            $data['pricecurrency'] = 'AED';
            $data['lastupdated'] = $value['updated_at'];

            $data['bedrooms'] = $value["bed"]["portals"][0]["name"] ?? 0;
            $data['bathrooms'] = $value["bath"]["portals"][0]["name"] ?? 0;

            $privateAmenities = [];
            $commercialAmenities = [];
            foreach ($value["amenities"] ?? [] as $amenity) {
                $amenityType = $amenity["amenity"]["amenity_type"]??[];
                if ($amenityType == "private") {
                    $privateAmenities[] = $amenity["amenity"]["portals"][0]['title'];
                }
                if ($amenityType == "commercial") {
                    $commercialAmenities[] = $amenity["amenity"]["portals"][0]['title'];
                }
            }
            $data['privateamenities'] = implode(",", $privateAmenities);
            $data['commercialamenities'] = implode(",", $commercialAmenities);

            if (!empty($value["community_property"])){
                $data['geopoint'] = $value["community_property"]["latitude"] .", ". $value["community_property"]["longitude"];
            }elseif (!empty($value["sub_community"])){
                $data['geopoint'] = $value["sub_community"]["latitude"] .", ". $value["sub_community"]["longitude"];
            }else{
                $data['geopoint'] = '';//$value['latitude'] . ',' . $value['longitude'];
            }

            if ($value['agent']) {
                $data['contactemail'] = $value['agent']['email'];
                $data['contactnumber'] = str_replace("+971", "0", $value['agent']['mobile']) ?? '';
            }
            $data['photos'] = [];
            if ($value['photos']) {
                $url = '';
                foreach ($value['photos'] as $key => $photo) {
                    $url .= $photo['path'].'|';
                }

                $data['photos'] = substr($url, 0, -1);
            }
            $data['video_url'] = $value['link_youtube'] ?? '';
            $data['view360'] = $value['video_360_link'] ?? '';

            //dd( $data['photo']);
            $data['price_on_application'] = 'No';
            $data['permit_number'] = $value['rera_permit_number'] ?? '';

            $prop['property'][] = $data;
        } // end foreach

        $pfObj = [];

        $xml = $this->createXML($pfObj, 'dubizzlepropertyfeed', $prop);
        $xxx = $xml->saveXML();

        return \Response::make($xxx, '200')->header('Content-Type', 'text/xml');
    }

    public function dubizzle_hourly(Request $request)
    {
        $mainPortal = Portal::where("name", "Dubizzle")->latest()->first();
        if (is_null($mainPortal)) {
            abort(404);
        }
        abort_if($mainPortal->xml_url != $request->key, 404);

        $oneHourAgo = Carbon::now()->subHour()->format("Y-m-d H:i:s");

        $prop = [];

        $pp = Property::whereHas("portals", function ($q) use ($mainPortal) {
            $q->where("portal_id", $mainPortal->id);
        })
            ->where("updated_at", ">=", $oneHourAgo)
            ->with('agent', 'propertyCategory.portals',
                'bed.portals', 'price', 'bath.portals', 'amenities.amenity.portals',
                'community.region', 'subCommunity', 'communityProperty',
                'photos', 'floorPlan')
            ->orderBy('properties.created_at', 'desc')
            ->get()
            ->toArray();

        foreach ($pp as $key => $value) {
            if ($value['publish_status'] == 'UNPUBLISHED') {
                $data['status'] = 'deleted';
            } else {
                $data['status'] = 'vacant';
            }

            if ($value['for'] == 'Residential for sale' || $value['for'] == 'Commercial for sale') {
                $data['type'] = 'SP';
            } else {
                $data['type'] = 'RP';
            }

            $data['subtype'] = collect($value['property_category']['portals'] ?? [])->where("portal_id",
                $mainPortal->id)->first()["title"] ?? "";
            $data['commercialtype'] = 'IN';
            $data['refno'] = $value['ref_no'];
            $data['title']['@cdata'] = $value['title'] ?? '';
            $data['description']['@cdata'] = strip_tags($value['description']) ?? '';
            $data['size'] = $value['built_up_area'] ?? '';
            $data['sizeunits'] = 'SqFt';
            $data['price'] = $value['price'] ? $value['price']['price'] : 0;
            $data['locationtext']['@cdata'] = $value['community']['name'] ?? '';
            if ($value['for'] == 'Residential for sale' || $value['for'] == 'Commercial for sale') {
                $data['rentispaid'] = '';
            } else {
                $data['rentispaid'] = 'YR';
            }

            if ($value['furnished'] == 'FURNISHED') {
                $data['furnished'] = 1;
            } else {
                $data['furnished'] = 0;
            }

            $data['building']['@cdata'] = $value['community_property']['name'] ?? ($value['sub_community']['name'] ?? "");
            $data['city'] = 2;

            if ($value['for'] == 'Residential for rent' || $value['for'] == 'Commercial for rent') {
                $data['rentpriceterm'] = 'YR';
            }
            $data['pricecurrency'] = 'AED';
            $data['lastupdated'] = $value['updated_at'];

            $data['bedrooms'] = collect($value["bed"]["portals"] ?? [])->where("portal_id",
                $mainPortal->id)->first()["name"] ?? 0;
            $data['bathrooms'] = collect($value["bath"]["portals"] ?? [])->where("portal_id",
                $mainPortal->id)->first()["name"] ?? 0;

            $privateAmenities = [];
            $commercialAmenities = [];
            foreach ($value["amenities"] ?? [] as $amenity) {
                $amenityType = $amenity["amenity"]["amenity_type"] ?? [];
                foreach ($amenity["amenity"]["portals"] ?? [] as $portal) {
                    if ($portal["portal_id"] == $mainPortal->id && !empty($portal["title"])) {
                        if ($amenityType == "private") {
                            $privateAmenities[] = $portal["title"];
                        }

                        if ($amenityType == "commercial") {
                            $commercialAmenities[] = $portal["title"];
                        }
                    }
                }
            }
            $data['privateamenities'] = implode(",", $privateAmenities);
            $data['commercialamenities'] = implode(",", $commercialAmenities);

            if (!empty($value["community_property"])){
                $data['geopoint'] = $value["community_property"]["latitude"] .", ". $value["community_property"]["longitude"];
            }elseif (!empty($value["sub_community"])){
                $data['geopoint'] = $value["sub_community"]["latitude"] .", ". $value["sub_community"]["longitude"];
            }else{
                $data['geopoint'] = '';//$value['latitude'] . ',' . $value['longitude'];
            }

            if ($value['agent']) {
                $data['contactemail'] = $value['agent']['email'];
                $data['contactnumber'] = str_replace("+971", "0", $value['agent']['mobile']) ?? '';
            }
            $data['photos'] = [];
            if ($value['photos']) {
                $url = '';
                foreach ($value['photos'] as $key => $photo) {
                    $url .= $photo['path'].'|';
                }

                $data['photos'] = substr($url, 0, -1);
            }
            $data['video_url'] = $value['link_youtube'] ?? '';
            $data['view360'] = $value['link_360'] ?? '';

            $data['price_on_application'] = 'No';
            $data['permit_number'] = $value['rera_permit_number'] ?? '';

            $prop['property'][] = $data;

        } // end foreach

        $pfObj = [];

        $xml = $this->createXML($pfObj, 'dubizzlepropertyfeed', $prop);
        $xxx = $xml->saveXML();

        return \Response::make($xxx, '200')->header('Content-Type', 'text/xml');
    }

    public function bayut(Request $request)
    {
        $mainPortal = Portal::where("name", "Bayut")->latest()->first();
        if (is_null($mainPortal)) {
            abort(404);
        }
        abort_if($mainPortal->xml_url != $request->key, 404);

        $prop = [];

        $pp = Property::whereHas("portals", function ($q) use ($mainPortal) {
                $q->where("portal_id", $mainPortal->id);
            })
            ->where(['publish_status' => 'PUBLISHED'])
            ->with([
                'agent' => function($q){
                    $q->select('id','name','first_name','last_name','pp_enabled','pp_name','pp_photo', 'pp_license_no', 'email','secondary_email','license_no','mobile','bio');
                },
                'details',
                'propertyCategory' => function ($q) {
                    $q->select('id');
                }, 
                'propertyCategory.portals'=> function($q) use ($mainPortal){
                    $q->where('portal_id', $mainPortal->id)->select('id', 'title','property_category_id', 'portal_id');
                },
                'bed' => function ($q) {
                    $q->select('id');
                },
                'bed.portals' => function($q) use ($mainPortal){
                    $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bed_id', 'portal_id');
                },'developer', 
                'price'=> function ($q) {
                    $q->select('id','property_id','price','prices','price_on_application');
                },
                'bath' => function ($q) {
                    $q->select('id');
                }, 
                'bath.portals' => function($q) use ($mainPortal){
                    $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bath_id', 'portal_id');
                }, 
                'amenities' => function ($q) use ($mainPortal) {
                        $q->whereHas('amenity.portals', function($q) use ($mainPortal) {
                              $q->where('portal_id', '=', $mainPortal->id);
                          });
                    },
                    'amenities.amenity.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', '=', $mainPortal->id)
                          ->where('title', '!=', '');
                    },
                'community.region', 'subCommunity', 'communityProperty',
                'photos' => function($q){
                    $q->select('id','property_id','path','title','updated_at');
                }, 
                'floorPlan'])
            ->orderBy('properties.created_at', 'desc')
            ->get()
            ->toArray();

        foreach ($pp as $key => $value) {
            $data['Property_Ref_No'] = $value['ref_no'];

            $data['Rent_Frequency']['@cdata'] = '';
            if ($value['for'] == "Residential for sale" || $value['for'] == "Commercial for sale") {
                $data['Property_purpose']['@cdata'] = 'Buy';
            } else {
                $data['Property_purpose']['@cdata'] = 'Rent';
                $data['Rent_Frequency']['@cdata'] = 'yearly';
            }

            $data['Property_Type']['@cdata'] = $value['property_category']['portals'][0]['title'] ?? "";
            $data['Property_Status']['@cdata'] = 'live';


            $data['City']['@cdata'] = $value['community']['region']['name'] ?? 'Dubai';
            $data['Locality']['@cdata'] = $value['community']['name'] ?? '';
            $data['Sub_Locality']['@cdata'] = $value['sub_community']['name'];
            $data['Tower_Name']['@cdata'] = $value['community_property']['name'] ?? '';
            $data['Property_Title']['@cdata'] = $value['title'] ?? '';
            $data['Property_Description']['@cdata'] = $value['description'] ?? '';
            if ($value['furnished'] == 'FURNISHED') {
                $data['Furnished']['@cdata'] = 'Yes';
            } else {
                if ($value['furnished'] == 'SEMI_FURNISHED') {
                    $data['Furnished']['@cdata'] = 'Partly';
                } else {
                    $data['Furnished']['@cdata'] = 'No';
                }
            }

            $data['Property_Size']['@cdata'] = $value['built_up_area'].'' ?? '';
            $data['Property_Size_Unit']['@cdata'] = "SQFT";

            $data['bedroom'] = $value["bed"]["portals"][0]["name"] ?? 0;
            $data['bathroom'] = $value["bath"]["portals"][0]["name"] ?? 0;

            $data['Price']['@cdata'] = ($value['price']['price'] ?? '').'';
            if ($value['agent']) {
                $data['Listing_Agent']['@cdata'] = $value['agent']['first_name'].' '.$value['agent']['last_name'];
                if ($value['agent']['email'] == $value['agent']['email']) {
                    $data['Listing_Agent_Email']['@cdata'] = $value['agent']['email'];
                } else {
                    $data['Listing_Agent_Email']['@cdata'] = $value['agent']['email'];
                }
                $data['Listing_Agent_Phone']['@cdata'] = $value['agent']['mobile'];
            }

            foreach ($value["amenities"] ?? [] as $amenity) {
                $data['Features']['Feature'][]['@cdata'] = $amenity["amenity"]["portals"][0]['title'];
            }

            $data['Images'] = [];

            if ($value['photos']) {
                foreach ($value['photos'] as $key => $photo) {
                    $data['Images']['Image'][]['@cdata'] = $photo['path'];
                }
            }

            $data['Videos']['Video']['@cdata'] = $value['video_link'];
            $data['Last_Updated']['@cdata'] =  date('Y-m-d H:i:s', strtotime($value['updated_at']));
            $data['Permit_Number']['@cdata'] = $value['rera_permit_number'] ?? '';

            if ($value['completion_status'] == 'Off-Plan Primary' || $value['completion_status']=='Off-Plan Secondary') {
                $data['Off_Plan']['@cdata'] = 'Yes';
            } else {
                $data['Off_Plan']['@cdata'] = 'No';
            }

            $prop['Property'][] = $data;
        } // end foreach

        $xml = $this->createXML([], 'Properties', $prop);
        $xxx = $xml->saveXML();

        return \Response::make($xxx, '200')->header('Content-Type', 'text/xml');
    }
    public function bayut_dubizzle(Request $request)
    {
        $mainPortal = Portal::where("name", "Bayut")->latest()->first();
        if (is_null($mainPortal)) {
            abort(404);
        }
        abort_if($mainPortal->xml_url != $request->key, 404);

        $prop = [];

        $pp = Property::whereHas("portals", function ($q) use ($mainPortal) {
                $q->where("portal_id", $mainPortal->id)->orWhere('portal_id', 3);
            })
            ->where(['publish_status' => 'PUBLISHED'])
            ->with([
                'agent' => function($q){
                    $q->select('id','name','first_name','last_name','pp_enabled','pp_name','pp_photo', 'pp_license_no', 'email','secondary_email','license_no','mobile','bio');
                },
                'details',
                'propertyCategory' => function ($q) {
                    $q->select('id');
                }, 
                'propertyCategory.portals'=> function($q) use ($mainPortal){
                    $q->where('portal_id', $mainPortal->id)->select('id', 'title','property_category_id', 'portal_id');
                },
                'bed' => function ($q) {
                    $q->select('id');
                },
                'bed.portals' => function($q) use ($mainPortal){
                    $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bed_id', 'portal_id');
                },'developer', 
                'price'=> function ($q) {
                    $q->select('id','property_id','price','prices','price_on_application');
                },
                'bath' => function ($q) {
                    $q->select('id');
                }, 
                'bath.portals' => function($q) use ($mainPortal){
                    $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bath_id', 'portal_id');
                }, 
                'amenities' => function ($q) use ($mainPortal) {
                        $q->whereHas('amenity.portals', function($q) use ($mainPortal) {
                              $q->where('portal_id', '=', $mainPortal->id);
                          });
                    },
                    'amenities.amenity.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', '=', $mainPortal->id)
                          ->where('title', '!=', '');
                    },
                'community.region', 'subCommunity', 'communityProperty','portals',
                'photos' => function($q){
                    $q->select('id','property_id','path','title','updated_at');
                }, 
                'floorPlan'])
            ->orderBy('properties.created_at', 'desc')
            ->get()
            ->toArray();

        foreach ($pp as $key => $value) {
            $data['Property_Ref_No'] = $value['ref_no'];

            $data['Rent_Frequency']['@cdata'] = '';
            if ($value['for'] == "Residential for sale" || $value['for'] == "Commercial for sale") {
                $data['Property_purpose']['@cdata'] = 'Buy';
            } else {
                $data['Property_purpose']['@cdata'] = 'Rent';
                $data['Rent_Frequency']['@cdata'] = 'yearly';
            }
            
            $data['Property_Type']['@cdata'] = $value['property_category']['portals'][0]['title'] ?? '';
            $data['Property_Status']['@cdata'] = 'live';


            $data['City']['@cdata'] = $value['community']['region']['name'] ?? 'Dubai';
            $data['Locality']['@cdata'] = $value['community']['alternate_name'] ?? $value['community']['name'] ?? '';
            $data['Sub_Locality']['@cdata'] = $value['sub_community']['alternate_name'] ?? $value['sub_community']['name']?? '';
            if(($value['community_property']['alternate_name']?? null) && (!$value['community_property']['alternate_use'])){
                $data['Tower_Name']['@cdata'] = '';
            }else{
                $data['Tower_Name']['@cdata'] = $value['community_property']['alternate_name'] ?? $value['community_property']['name'] ?? '';
            }
            $data['Property_Title']['@cdata'] = $value['title'] ?? '';
            //$data['Property_Description']['@cdata'] = $value['description'] ?? '';
            $description = strip_tags($value['description'], '<br>');
            $updateDescription = str_replace("<br />", "\r\n", $description);
            $data['Property_Description']['@cdata'] = str_replace("<br>", "\r\n", $updateDescription) ?? '';
            if ($value['furnished'] == 'FURNISHED') {
                $data['Furnished']['@cdata'] = 'Yes';
            } else {
                if ($value['furnished'] == 'SEMI_FURNISHED') {
                    $data['Furnished']['@cdata'] = 'Partly';
                } else {
                    $data['Furnished']['@cdata'] = 'No';
                }
            }

            $data['Property_Size']['@cdata'] = str_replace( ',', '', $value['built_up_area'].'') ?? '';
            $data['Property_Size_Unit']['@cdata'] = "SQFT";

            $data['bedroom'] = $value["bed"]["portals"][0]["name"] ?? 0;
            $data['bathroom'] = $value["bath"]["portals"][0]["name"] ?? 0;

            $data['Price']['@cdata'] = ($value['price']['price'] ?? '').'';
            if ($value['agent']) {
                $data['Listing_Agent']['@cdata'] = $value['agent']['first_name'].' '.$value['agent']['last_name'];
                
                $data['Listing_Agent_Email']['@cdata'] = $value['agent']['secondary_email']!=""?$value['agent']['secondary_email']:$value['agent']['email'];
                if($value['agent']['pp_enabled']){
                    $data['Listing_Agent']['@cdata'] = $value['agent']['pp_name'];
                }
                $data['Listing_Agent_Phone']['@cdata'] = $value['agent']['mobile'];
            }
            $data['Features']['Feature'] = array();
            foreach ($value["amenities"] ?? [] as $amenity) {

                $data['Features']['Feature'][]['@cdata'] = $amenity["amenity"]["portals"][0]['title'];
                // foreach ($amenity["amenity"]["portals"] ?? [] as $portal) {
                //     if ($portal["portal_id"] == $mainPortal->id && !empty($portal["title"])) {
                //         $data['Features']['Feature'][]['@cdata'] = $portal["title"];
                //     }
                // }
            }

            $data['Images'] = [];

            if ($value['photos']) {
                foreach ($value['photos'] as $key => $photo) {
                    $data['Images']['Image'][]['@cdata'] = $photo['path'];
                }
            }

            $data['Videos']['Video']['@cdata'] = $value['video_link'];
            $data['Last_Updated']['@cdata'] = date('Y-m-d H:i:s', strtotime($value['updated_at']));
            $data['Permit_Number']['@cdata'] = $value['rera_permit_number'] ?? '';

            if ($value['completion_status'] == 'Off-Plan Primary' || $value['completion_status']=='Off-Plan Secondary') {
                $data['Off_Plan']['@cdata'] = 'Yes';
            } else {
                $data['Off_Plan']['@cdata'] = 'No';
            }
            $data['Portals'] = [];
            
            foreach($value['portals'] as $portal){
                if($portal['portal_id'] == 3){
                    $data['Portals']['Portal'][]['@cdata'] = 'Dubizzle';
                }
                if($portal['portal_id'] == 2){
                    $data['Portals']['Portal'][]['@cdata'] = 'Bayut';
                }
            }

            $prop['Property'][] = $data;
        } // end foreach

        $xml = $this->createXML([], 'Properties', $prop);
        $xxx = $xml->saveXML();
        return \Response::make($xxx, '200')->header('Content-Type', 'text/xml');
    }

    public function bayutDubizzleUpdated(Request $request)
    {
        $mainPortal = Portal::where("name", "Bayut")->latest()->first();
        if (is_null($mainPortal)) {
            abort(404);
        }
        abort_if($mainPortal->xml_url != $request->key, 404);

        $prop = [];

        $pp = Property::whereHas("portals", function ($q) use ($mainPortal) {
                $q->where("portal_id", $mainPortal->id)->orWhere('portal_id', 3);
            })
            ->where(['publish_status' => 'PUBLISHED'])
            ->with([
                'agent' => function($q){
                    $q->select('id','name','first_name','last_name','pp_enabled','pp_name','pp_photo', 'pp_license_no', 'email','secondary_email','license_no','mobile','bio');
                },
                'details',
                'propertyCategory' => function ($q) {
                    $q->select('id');
                }, 
                'propertyCategory.portals'=> function($q) use ($mainPortal){
                    $q->where('portal_id', $mainPortal->id)->select('id', 'title','property_category_id', 'portal_id');
                },
                'bed' => function ($q) {
                    $q->select('id');
                },
                'bed.portals' => function($q) use ($mainPortal){
                    $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bed_id', 'portal_id');
                },'developer', 
                'price'=> function ($q) {
                    $q->select('id','property_id','price','prices','price_on_application');
                },
                'bath' => function ($q) {
                    $q->select('id');
                }, 
                'bath.portals' => function($q) use ($mainPortal){
                    $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bath_id', 'portal_id');
                }, 
                'amenities' => function ($q) use ($mainPortal) {
                        $q->whereHas('amenity.portals', function($q) use ($mainPortal) {
                              $q->where('portal_id', '=', $mainPortal->id);
                          });
                    },
                    'amenities.amenity.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', '=', $mainPortal->id)
                          ->where('title', '!=', '');
                    },
                'community.region', 'subCommunity', 'communityProperty','portals',
                'photos' => function($q){
                    $q->select('id','property_id','path','title','updated_at');
                }, 
                'floorPlan'])
            ->orderBy('properties.created_at', 'desc')
            ->get();

        foreach ($pp as $key => $value) {
            $data['Property_Ref_No'] = $value?->ref_no;

            $data['Rent_Frequency']['@cdata'] = '';
            if ($value?->for == "Residential for sale" || $value?->for == "Commercial for sale") {
                $data['Property_purpose']['@cdata'] = 'Buy';
            } else {
                $data['Property_purpose']['@cdata'] = 'Rent';
                $data['Rent_Frequency']['@cdata'] = 'yearly';
            }
            $data['Property_Type']['@cdata'] = $value?->propertyCategory?->portals?->first()?->title?? '';
            //$data['Property_Type']['@cdata'] = $value['property_category']['portals'][0]['title'] ?? '';
            $data['Property_Status']['@cdata'] = 'live';


            $data['City']['@cdata'] = $value?->community?->region?->name ?? 'Dubai';
            $data['Locality']['@cdata'] = $value?->community?->alternate_name ?? $value?->community?->name ?? '';
            $data['Sub_Locality']['@cdata'] = $value?->subCommunity?->alternate_name ?? $value?->subCommunity?->name?? '';
            if(($value?->communityProperty?->alternate_name ?? null) && (!$value?->communityProperty?->alternate_use)){
                $data['Tower_Name']['@cdata'] = '';
            }else{
                $data['Tower_Name']['@cdata'] = $value?->communityProperty?->alternate_name ?? $value?->communityProperty?->name ?? '';
            }
            $data['Property_Title']['@cdata'] = $value->title ?? '';
            //$data['Property_Description']['@cdata'] = $value['description'] ?? '';
            $description = strip_tags($value->description, '<br>');
            $updateDescription = str_replace("<br />", "\r\n", $description);
            $data['Property_Description']['@cdata'] = str_replace("<br>", "\r\n", $updateDescription) ?? '';
            if ($value->furnished == 'FURNISHED') {
                $data['Furnished']['@cdata'] = 'Yes';
            } else {
                if ($value['furnished'] == 'SEMI_FURNISHED') {
                    $data['Furnished']['@cdata'] = 'Partly';
                } else {
                    $data['Furnished']['@cdata'] = 'No';
                }
            }

            $data['Property_Size']['@cdata'] = str_replace( ',', '', $value['built_up_area'].'') ?? '';
            $data['Property_Size_Unit']['@cdata'] = "SQFT";

            $data['bedroom'] = $value?->bed?->portals?->first()?->name ?? 0;
            $data['bathroom'] = $value?->bath?->portals->first()->name ?? 0;

            $data['Price']['@cdata'] = ($value?->price?->price ?? '').'';
            if ($value['agent']) {
                $data['Listing_Agent']['@cdata'] = $value?->agent?->first_name.' '.$value?->agent?->last_name;
                
                $data['Listing_Agent_Email']['@cdata'] = $value?->agent?->secondary_email!=""?$value?->agent?->secondary_email:$value?->agent?->email;
                if($value['agent']['pp_enabled']){
                    $data['Listing_Agent']['@cdata'] = $value?->agent?->pp_name;
                }
                $data['Listing_Agent_Phone']['@cdata'] = $value?->agent?->mobile;
            }
            $data['Features']['Feature'] = array();
            foreach ($value["amenities"] ?? [] as $amenity) {

                $data['Features']['Feature'][]['@cdata'] = $amenity?->amenity?->portals?->first()?->title;
                // foreach ($amenity["amenity"]["portals"] ?? [] as $portal) {
                //     if ($portal["portal_id"] == $mainPortal->id && !empty($portal["title"])) {
                //         $data['Features']['Feature'][]['@cdata'] = $portal["title"];
                //     }
                // }
            }

            $data['Images'] = [];

            if ($value['photos']) {
                foreach ($value->photos ?? [] as $key => $photo) {
                    $data['Images']['Image'][]['@cdata'] = $photo?->path;
                }
            }

            $data['Videos']['Video']['@cdata'] = $value?->video_link;
            $data['Last_Updated']['@cdata'] = date('Y-m-d H:i:s', strtotime($value?->updated_at));
            $data['Permit_Number']['@cdata'] = $value['rera_permit_number'] ?? '';

            if ($value['completion_status'] == 'Off-Plan Primary' || $value?->completion_status == 'Off-Plan Secondary') {
                $data['Off_Plan']['@cdata'] = 'Yes';
            } else {
                $data['Off_Plan']['@cdata'] = 'No';
            }
            $data['Portals'] = [];
            
            foreach($value['portals'] as $portal){
                if($portal['portal_id'] == 3){
                    $data['Portals']['Portal'][]['@cdata'] = 'Dubizzle';
                }
                if($portal['portal_id'] == 2){
                    $data['Portals']['Portal'][]['@cdata'] = 'Bayut';
                }
            }

            $prop['Property'][] = $data;
        } // end foreach

        $xml = $this->createXML([], 'Properties', $prop);
        $xxx = $xml->saveXML();
        return \Response::make($xxx, '200')->header('Content-Type', 'text/xml');
        
        $xmlContent = $xml->saveXML();

        // Set the chunk size (number of characters per chunk)
        $chunkSize = 500;

        // Generate the response in chunks
        return \Response::stream(function () use ($xmlContent, $chunkSize) {
            $length = strlen($xmlContent);
            $start = 0;

            while ($start < $length) {
                $chunk = substr($xmlContent, $start, $chunkSize);
                $start += $chunkSize;

                echo $chunk;

                // Flush the output buffer to send the current chunk
                flush();
            }
        }, 200, [
            'Content-Type' => 'text/xml',
        ]);
        return \Response::make($xxx, '200')->header('Content-Type', 'text/xml');
    }

    public function privatesite()
    {

        $mainPortal = Portal::where("slug", "private-site")->latest()->first();
        if (is_null($mainPortal)) {
            abort(404);
        }
        //  abort_if($mainPortal->xml_url != $request->key, 404);

        $last_update = Property::whereHas("portals", function ($q) use ($mainPortal) {
                            $q->where("portal_id", $mainPortal->id);
                        })
                        ->where(['publish_status' => 'PUBLISHED'])
                        ->orderBy('updated_at', 'desc')
                        ->select('updated_at')->first()->toArray();

        $listing_count = Property::whereHas("portals", function ($q) use ($mainPortal) {
                            $q->where("portal_id", $mainPortal->id);
                        })
                        ->where(['publish_status' => 'PUBLISHED'])
                        ->count();

        $prop = [];

        $pp = Property::where('publish_status', 'PUBLISHED')->whereHas("portals", function ($q) use ($mainPortal) {
            $q->where("portal_id", $mainPortal->id);
        })->with(
            'details',
            'agent',
            'propertyCategory.portals',
            'bed.portals',
            'price',
            'bath.portals',
            'amenities.amenity.portals',
            'community.region',
            'subCommunity',
            'communityProperty',
            'photos',
            'floorPlan'
        )
        ->orderBy('properties.created_at', 'desc')
        ->get()
            ->toArray();
           
        foreach ($pp as $key => $value) {
            $data['@attributes'] = ['last_update' => date('Y-m-d H:i:s', strtotime($value['updated_at']))];
            $data['permit_number'] = $value['rera_permit_number'] ?? '';
            $data['reference_number'] = $value['ref_no'];
            $data['price'] = [];
            if ($value['for'] == "Residential for sale" || $value['for'] == "Commercial for sale") {
                $data['price'] = $value['price']["price"] ?? 0;
                $data['offering_type'] = $value['for'] == "Commercial for sale" ? 'CS' : 'RS';
            } else {
                $data['price']['yearly'] = $value['price']["price"] ?? 0;
                $data['offering_type'] = $value['for'] == "Commercial for rent" ? 'CR' : 'RR';
            }
            $data['property_type'] = collect($value['property_category']['portals'] ?? [])->where(
                "portal_id",
                $mainPortal->id
            )->first()["title"] ?? "";
    
            $data['price_on_application'] = 'No';
           
            $data['city']['@cdata'] = $value['community']['region']['name'] ?? 'Dubai';
            $data['community']['@cdata'] = $value['community']['name'] ?? '';
            $data['sub_community']['@cdata'] = $value['sub_community']['name'] ?? '';
            $data['property_name']['@cdata'] = $value['community_property']['name'] ?? '';

           

            $data['title_en']['@cdata'] = $value['title'] ?? '';
            $data['description_en']['@cdata'] = $value['description'] ?? '';
            $data['completion_status'] = $value['completion_status'] ?? "";
          
            $features = [];

            foreach ($value["amenities"] ?? [] as $amenity) {
                $amenityType = $amenity["amenity"]["amenity_type"] ?? [];
                foreach ($amenity["amenity"]["portals"] ?? [] as $portal) {
                    if ($portal["portal_id"] == $mainPortal->id && !empty($portal["title"])) {
                        $features[] = $portal["title"];
                    }
                }
            }

            $data['amenities'] = implode(',', $features);
            $data['size'] = $value['built_up_area'] ?? '';
            $data['bedroom'] = collect($value["bed"]["portals"] ?? [])->where(
                "portal_id",
                $mainPortal->id
            )->first()["name"] ?? 0;
            $data['bathroom'] = collect($value["bath"]["portals"] ?? [])->where(
                "portal_id",
                $mainPortal->id
            )->first()["name"] ?? 0;

            //$data['view'] = $value['view'] ?? '';
           

            if ($value['agent']) {
              
                $data['agent']['name']['@cdata'] = $value['agent']['first_name'] . ' ' . $value['agent']['last_name'];
                $data['agent']['email'] = $value['agent']['secondary_email'] != ""? $value['agent']['secondary_email']:$value['agent']['email'] ?? '';
                $data['agent']['phone'] = str_replace("+971", "0", $value['agent']['mobile']) ?? '';
                $data['agent']['license_no'] = $value['agent']['license_no'] ?? '';
                $data['agent']['photo'] = $value['agent']['thumb_url'] ?? '';
                if($value['agent']['pp_enabled']){
                    $data['agent']['name']['@cdata'] = $value['agent']['pp_name'];
                    $data['agent']['photo'] = $value['agent']['pp_photo'] ?? '';
                    $data['agent']['license_no'] = $value['agent']['pp_license_no'] ?? '';
                }
             
              
            }
            $data['developer'] = $value['details']['developer'] ?? null;
            $data['parking'] = $value['parking'] ?? 0;

            if ($value['furnished'] == 'FURNISHED') {
                $data['furnished'] = 'Yes';
            } else {
                if ($value['furnished'] == 'SEMI_FURNISHED') {
                    $data['furnished'] = 'Partly';
                } else {
                    $data['furnished'] = 'No';
                }
            }
            $data['photo'] = [];
            if ($value['photos']) {
                $url = [];
                foreach ($value['photos'] as $key => $photo) {
                    $dd['@attributes'] = ['last_updated' => date('Y-m-d H:i:s', strtotime($photo['updated_at'])), 'watermark' => 'yes'];
                    $dd['@value'] = $photo['path'];
                    $url['url'][] = $dd;
                }
                $data['photo'] = $url;
            }

           

           
            $prop['property'][] = $data;
            $data['geopoints'] = '';
            if (!empty($value["community_property"])) {
                if($value["community_property"]["latitude"] && $value["community_property"]["longitude"]){
                    $data['geopoints'] = $value["community_property"]["latitude"] . ", " . $value["community_property"]["longitude"];
                }
                
            } elseif (!empty($value["sub_community"])) {
                if($value["sub_community"]["latitude"] && $value["sub_community"]["longitude"]){
                    $data['geopoints'] = $value["sub_community"]["latitude"] . ", " . $value["sub_community"]["longitude"];
                }
                
            } else {
                $data['geopoints'] = ''; //$value['latitude'] . ',' . $value['longitude'];
            }
        }

        $last_update = $last_update['updated_at'];
        $this->listing_count = $listing_count;
        $pfObj['last_update'] = date('Y-m-d H:i:s', strtotime($last_update));
        $pfObj['listing_count'] = $listing_count;

        $xml = $this->createXML($pfObj, 'list', $prop);
        $xxx = $xml->saveXML();

        return \Response::make($xxx, '200')->header('Content-Type', 'text/xml');
    }
    public function jamesEdition()
    {
        try{
        $mainPortal = Portal::where("slug", "james-edition")->latest()->first();
        $genericPortal = Portal::where("slug", "generic-portal")->latest()->first();
        if (is_null($mainPortal)) {
            abort(404);
        }
        //  abort_if($mainPortal->xml_url != $request->key, 404);

        $last_update = Property::whereHas("portals", function ($q) use ($mainPortal) {
            $q->where("portal_id", $mainPortal->id);
        })->orderBy('updated_at', 'desc')
        ->select('updated_at')->first()->toArray();

        $listing_count = Property::whereHas("portals", function ($q) use ($mainPortal) {
            $q->where("portal_id", $mainPortal->id);
        })->where(['publish_status' => 'PUBLISHED'])->count();

        $prop = [];

        $pp = Property::whereHas("portals", function ($q) use ($mainPortal) {
                $q->where("portal_id", $mainPortal->id);
            })
            ->where(['publish_status' => 'PUBLISHED'])
            ->with([
                'agent' => function($q){
                    $q->select('id','name','first_name','last_name','pp_enabled','pp_name','pp_photo', 'pp_license_no', 'email','secondary_email','license_no','mobile','bio');
                },
                'details',
                'propertyCategory' => function ($q) {
                    $q->select('id');
                }, 
                'propertyCategory.portals'=> function($q) use ($mainPortal){
                    $q->where('portal_id', $mainPortal->id)->select('id', 'title','property_category_id', 'portal_id');
                },
                'bed' => function ($q) {
                    $q->select('id');
                },
                'bed.portals' => function($q) use ($mainPortal){
                    $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bed_id', 'portal_id');
                },'developer', 
                'price'=> function ($q) {
                    $q->select('id','property_id','price','prices','price_on_application');
                },
                'bath' => function ($q) {
                    $q->select('id');
                }, 
                'bath.portals' => function($q) use ($mainPortal){
                    $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bath_id', 'portal_id');
                }, 
                'amenities' => function ($q) use ($mainPortal) {
                        $q->whereHas('amenity.portals', function($q) use ($mainPortal) {
                              $q->where('portal_id', '=', $mainPortal->id);
                          });
                    },
                    'amenities.amenity.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', '=', $mainPortal->id)
                          ->where('title', '!=', '');
                    },
                'community.region', 'subCommunity', 'communityProperty',
                'photos' => function($q){
                    $q->select('id','property_id','path','title','updated_at');
                }, 
                'floorPlan'])
            ->orderBy('properties.created_at', 'desc')
            ->get()
            ->toArray();

        foreach ($pp as $key => $value) {
            $data['@attributes'] = ['last_update' => date('Y-m-d H:i:s', strtotime($value['updated_at']))];
            $data['permit_number'] = $value['rera_permit_number'] ?? '';
            $data['reference_number'] = $value['ref_no'];
            $data['price'] = [];
            if ($value['for'] == "Residential for sale" || $value['for'] == "Commercial for sale") {
                $data['price'] = $value['price']["price"] ?? 0;
                $data['offering_type'] = $value['for'] == "Commercial for sale" ? 'CS' : 'RS';
            } else {
                $data['price']['yearly'] = $value['price']["price"] ?? 0;
                $data['offering_type'] = $value['for'] == "Commercial for rent" ? 'CR' : 'RR';
            }
            $data['property_type'] = $value['property_category']['portals'][0]["title"] ?? "";
            $data['price_on_application'] = 'No';

            $data['city']['@cdata'] = $value['community']['region']['name'] ?? 'Dubai';
            $data['community']['@cdata'] = $value['community']['name'] ?? '';
            $data['sub_community']['@cdata'] = $value['sub_community']['name'] ?? '';
            $data['property_name']['@cdata'] = $value['community_property']['name'] ?? '';



            $data['title_en']['@cdata'] = $value['title'] ?? '';
            //$data['description_en']['@cdata'] = $value['description'] ?? '';

            $description = strip_tags($value['description'], '<br>');
            $data['description_en']['@cdata'] = str_replace("<br>", "\r\n", $description) ?? '';

            $data['completion_status'] = $value['completion_status'] ?? "";

            $features = [];

            foreach ($value["amenities"] ?? [] as $amenity) {
                $amenityType = $amenity["amenity"]["amenity_type"] ?? [];
                $features[] = $amenity["amenity"]["portals"][0]["title"];
            }

            $data['amenities'] = implode(',', $features);
            $data['size'] = $value['built_up_area'] ?? '';
            $data['bedroom'] = $value["bed"]["portals"][0]['name'] ?? 0;
            $data['bathroom'] = $value["bath"]["portals"][0]["name"] ?? 0;

            //$data['view'] = $value['view'] ?? '';


            if ($value['agent']) {
                if($value['agent']['pp_enabled']){
                    $data['agent']['name']['@cdata'] = $value['agent']['pp_name'];
                    $data['agent']['photo'] = $value['agent']['pp_photo'] ?? '';
                    $data['agent']['license_no'] = $value['agent']['pp_license_no'] ?? '';
                }else{
                    $data['agent']['name']['@cdata'] = $value['agent']['first_name'] . ' ' . $value['agent']['last_name'];
                    $data['agent']['photo'] = $value['agent']['thumb_url'] ?? '';
                    $data['agent']['license_no'] = $value['agent']['license_no'] ?? '';
                }

                $data['agent']['email'] = $value['agent']['secondary_email']!="" ? $value['agent']['secondary_email']: $value['agent']['email'] ?? '';
                $data['agent']['phone'] = str_replace("+971", "0", $value['agent']['mobile']) ?? '';
            }
            $data['developer'] = $value['details']['developer'] ?? null;
            $data['parking'] = $value['parking'] ?? 0;

            if ($value['furnished'] == 'FURNISHED') {
                $data['furnished'] = 'Yes';
            } else {
                if ($value['furnished'] == 'SEMI_FURNISHED') {
                    $data['furnished'] = 'Partly';
                } else {
                    $data['furnished'] = 'No';
                }
            }
            $data['photo'] = [];
            if ($value['photos']) {
                $url = [];
                foreach ($value['photos'] as $key => $photo) {
                    $dd['@attributes'] = ['last_updated' => date('Y-m-d H:i:s', strtotime($photo['updated_at'])), 'watermark' => 'yes'];
                    $dd['@value'] = $photo['path'];
                    $url['url'][] = $dd;
                }
                $data['photo'] = $url;
            }




            $prop['property'][] = $data;
            if (!empty($value["community_property"])) {
                $data['geopoints'] = $value["community_property"]["latitude"] . ", " . $value["community_property"]["longitude"];
            } elseif (!empty($value["sub_community"])) {
                $data['geopoints'] = $value["sub_community"]["latitude"] . ", " . $value["sub_community"]["longitude"];
            } else {
                $data['geopoints'] = ''; //$value['latitude'] . ',' . $value['longitude'];
            }
        }

        $last_update = $last_update['updated_at'];
        $this->listing_count = $listing_count;
        $pfObj['last_update'] = date('Y-m-d H:i:s', strtotime($last_update));
        $pfObj['listing_count'] = $listing_count;

        $xml = $this->createXML($pfObj, 'list', $prop);
        $xxx = $xml->saveXML();

        return \Response::make($xxx, '200')->header('Content-Type', 'text/xml');
        }catch(\Exception $e){
            
        }
    }
    public function jamesEditionUpdated()
    {
        try{
        $mainPortal = Portal::where("slug", "james-edition")->latest()->first();
        $genericPortal = Portal::where("slug", "generic-portal")->latest()->first();
        if (is_null($mainPortal)) {
            abort(404);
        }
        //  abort_if($mainPortal->xml_url != $request->key, 404);

        $last_update = Property::whereHas("portals", function ($q) use ($mainPortal) {
            $q->where("portal_id", $mainPortal->id);
        })->orderBy('updated_at', 'desc')
        ->select('updated_at')->first()->toArray();

        $listing_count = Property::whereHas("portals", function ($q) use ($mainPortal) {
            $q->where("portal_id", $mainPortal->id);
        })->where(['publish_status' => 'PUBLISHED'])->count();

        $prop = [];

        $pp = Property::whereHas("portals", function ($q) use ($mainPortal) {
                $q->where("portal_id", $mainPortal->id);
            })
            ->where(['publish_status' => 'PUBLISHED'])
            ->with([
                'agent' => function($q){
                    $q->select('id','name','first_name','last_name','pp_enabled','pp_name','pp_photo', 'pp_license_no', 'email','secondary_email','license_no','mobile','bio');
                },
                'details',
                'propertyCategory' => function ($q) {
                    $q->select('id');
                }, 
                'propertyCategory.portals'=> function($q) use ($mainPortal){
                    $q->where('portal_id', $mainPortal->id)->select('id', 'title','property_category_id', 'portal_id');
                },
                'bed' => function ($q) {
                    $q->select('id');
                },
                'bed.portals' => function($q) use ($mainPortal){
                    $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bed_id', 'portal_id');
                },'developer', 
                'price'=> function ($q) {
                    $q->select('id','property_id','price','prices','price_on_application');
                },
                'bath' => function ($q) {
                    $q->select('id');
                }, 
                'bath.portals' => function($q) use ($mainPortal){
                    $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bath_id', 'portal_id');
                }, 
                'amenities' => function ($q) use ($mainPortal) {
                        $q->whereHas('amenity.portals', function($q) use ($mainPortal) {
                              $q->where('portal_id', '=', $mainPortal->id);
                          });
                    },
                    'amenities.amenity.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', '=', $mainPortal->id)
                          ->where('title', '!=', '');
                    },
                'community.region', 'subCommunity', 'communityProperty',
                'photos' => function($q){
                    $q->select('id','property_id','path','title','updated_at');
                }, 
                'floorPlan'])
            ->orderBy('properties.created_at', 'desc')
            ->get();

        foreach ($pp as $key => $value) {
            $data['@attributes'] = ['last_update' => date('Y-m-d H:i:s', strtotime($value['updated_at']))];
            $data['permit_number'] = $value['rera_permit_number'] ?? '';
            $data['reference_number'] = $value['ref_no'];
            $data['price'] = [];
            if ($value['for'] == "Residential for sale" || $value['for'] == "Commercial for sale") {
                $data['price'] = $value['price']["price"] ?? 0;
                $data['offering_type'] = $value['for'] == "Commercial for sale" ? 'CS' : 'RS';
            } else {
                $data['price']['yearly'] = $value['price']["price"] ?? 0;
                $data['offering_type'] = $value['for'] == "Commercial for rent" ? 'CR' : 'RR';
            }
            $data['property_type'] = $value['propertyCategory']['portals'][0]["title"] ?? "";
            $data['price_on_application'] = 'No';

            $data['city']['@cdata'] = $value['community']['region']['name'] ?? 'Dubai';
            $data['community']['@cdata'] = $value['community']['name'] ?? '';
            $data['sub_community']['@cdata'] = $value['subCommunity']['name'] ?? '';
            $data['property_name']['@cdata'] = $value['communityProperty']['name'] ?? '';



            $data['title_en']['@cdata'] = $value['title'] ?? '';
            //$data['description_en']['@cdata'] = $value['description'] ?? '';

            $description = strip_tags($value['description'], '<br>');
            $data['description_en']['@cdata'] = str_replace("<br>", "\r\n", $description) ?? '';

            $data['completion_status'] = $value['completion_status'] ?? "";

            $features = [];

            foreach ($value["amenities"] ?? [] as $amenity) {
                $amenityType = $amenity["amenity"]["amenity_type"] ?? [];
                $features[] = $amenity["amenity"]["portals"][0]["title"];
            }

            $data['amenities'] = implode(',', $features);
            $data['size'] = $value['built_up_area'] ?? '';
            $data['bedroom'] = $value["bed"]["portals"][0]['name'] ?? 0;
            $data['bathroom'] = $value["bath"]["portals"][0]["name"] ?? 0;

            //$data['view'] = $value['view'] ?? '';


            if ($value['agent']) {
                if($value['agent']['pp_enabled']){
                    $data['agent']['name']['@cdata'] = $value['agent']['pp_name'];
                    $data['agent']['photo'] = $value['agent']['pp_photo'] ?? '';
                    $data['agent']['license_no'] = $value['agent']['pp_license_no'] ?? '';
                }else{
                    $data['agent']['name']['@cdata'] = $value['agent']['first_name'] . ' ' . $value['agent']['last_name'];
                    $data['agent']['photo'] = $value['agent']['thumb_url'] ?? '';
                    $data['agent']['license_no'] = $value['agent']['license_no'] ?? '';
                }

                $data['agent']['email'] = $value['agent']['secondary_email']!="" ? $value['agent']['secondary_email']: $value['agent']['email'] ?? '';
                $data['agent']['phone'] = str_replace("+971", "0", $value['agent']['mobile']) ?? '';
            }
            $data['developer'] = $value['details']['developer'] ?? null;
            $data['parking'] = $value['parking'] ?? 0;

            if ($value['furnished'] == 'FURNISHED') {
                $data['furnished'] = 'Yes';
            } else {
                if ($value['furnished'] == 'SEMI_FURNISHED') {
                    $data['furnished'] = 'Partly';
                } else {
                    $data['furnished'] = 'No';
                }
            }
            $data['photo'] = [];
            if ($value['photos']) {
                $url = [];
                foreach ($value['photos'] as $key => $photo) {
                    $dd['@attributes'] = ['last_updated' => date('Y-m-d H:i:s', strtotime($photo['updated_at'])), 'watermark' => 'yes'];
                    $dd['@value'] = $photo['path'];
                    $url['url'][] = $dd;
                }
                $data['photo'] = $url;
            }




            $prop['property'][] = $data;
            if (!empty($value["communityProperty"])) {
                $data['geopoints'] = $value["communityProperty"]["latitude"] . ", " . $value["communityProperty"]["longitude"];
            } elseif (!empty($value["subCommunity"])) {
                $data['geopoints'] = $value["subCommunity"]["latitude"] . ", " . $value["subCommunity"]["longitude"];
            } else {
                $data['geopoints'] = ''; //$value['latitude'] . ',' . $value['longitude'];
            }
        }

        $last_update = $last_update['updated_at'];
        $this->listing_count = $listing_count;
        $pfObj['last_update'] = date('Y-m-d H:i:s', strtotime($last_update));
        $pfObj['listing_count'] = $listing_count;

        $xml = $this->createXML($pfObj, 'list', $prop);
        $xxx = $xml->saveXML();

        return \Response::make($xxx, '200')->header('Content-Type', 'text/xml');
        }catch(\Exception $e){
            
        }
    }
    public function houza()
    {
        try{
        $mainPortal = Portal::where("slug", "houza")->latest()->first();
        $genericPortal = Portal::where("slug", "generic-portal")->latest()->first();
        if (is_null($mainPortal)) {
            abort(404);
        }
        //  abort_if($mainPortal->xml_url != $request->key, 404);

        $last_update = Property::whereHas("portals", function ($q) use ($mainPortal) {
            $q->where("portal_id", $mainPortal->id);
        })->orderBy('updated_at', 'desc')
        ->select('updated_at')->first()->toArray();

        $listing_count = Property::whereHas("portals", function ($q) use ($mainPortal) {
            $q->where("portal_id", $mainPortal->id);
        })->where(['publish_status' => 'PUBLISHED'])->count();

        $prop = [];

        $pp = Property::whereHas("portals", function ($q) use ($mainPortal) {
                $q->where("portal_id", $mainPortal->id);
            })
            ->where(['publish_status' => 'PUBLISHED'])
            ->with([
                'agent' => function($q){
                    $q->select('id','name','first_name','last_name','pp_enabled','pp_name','pp_photo', 'pp_license_no', 'email','secondary_email','license_no','mobile','bio');
                },
                'details',
                'propertyCategory' => function ($q) {
                    $q->select('id');
                }, 
                'propertyCategory.portals'=> function($q) use ($mainPortal){
                    $q->where('portal_id', $mainPortal->id)->select('id', 'title','property_category_id', 'portal_id');
                },
                'bed' => function ($q) {
                    $q->select('id');
                },
                'bed.portals' => function($q) use ($mainPortal){
                    $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bed_id', 'portal_id');
                },'developer', 
                'price'=> function ($q) {
                    $q->select('id','property_id','price','prices','price_on_application');
                },
                'bath' => function ($q) {
                    $q->select('id');
                }, 
                'bath.portals' => function($q) use ($mainPortal){
                    $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bath_id', 'portal_id');
                }, 
                'amenities' => function ($q) use ($mainPortal) {
                        $q->whereHas('amenity.portals', function($q) use ($mainPortal) {
                              $q->where('portal_id', '=', $mainPortal->id);
                          });
                    },
                    'amenities.amenity.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', '=', $mainPortal->id)
                          ->where('title', '!=', '');
                    },
                'community.region', 'subCommunity', 'communityProperty',
                'photos' => function($q){
                    $q->select('id','property_id','path','title','updated_at');
                }, 
                'floorPlan'])
            ->orderBy('properties.created_at', 'desc')
            ->get()
            ->toArray();
        foreach ($pp as $key => $value) {
            $data['@attributes'] = ['last_update' => date('Y-m-d H:i:s', strtotime($value['updated_at']))];
            $data['permit_number'] = $value['rera_permit_number'] ?? '';
            $data['reference_number'] = $value['ref_no'];
            $data['price'] = [];
            if ($value['for'] == "Residential for sale" || $value['for'] == "Commercial for sale") {
                $data['price'] = $value['price']["price"] ?? 0;
                $data['offering_type'] = $value['for'] == "Commercial for sale" ? 'CS' : 'RS';
            } else {
                $data['price']['yearly'] = $value['price']["price"] ?? 0;
                $data['offering_type'] = $value['for'] == "Commercial for rent" ? 'CR' : 'RR';
            }
            $data['property_type'] = $value['property_category']['portals'][0]["title"] ?? "";
            $data['price_on_application'] = 'No';

            $data['city']['@cdata'] = $value['community']['region']['name'] ?? 'Dubai';
            $data['community']['@cdata'] = $value['community']['name'] ?? '';
            $data['sub_community']['@cdata'] = $value['sub_community']['name'] ?? '';
            $data['property_name']['@cdata'] = $value['community_property']['name'] ?? '';



            $data['title_en']['@cdata'] = $value['title'] ?? '';
            //$data['description_en']['@cdata'] = $value['description'] ?? '';

            $description = strip_tags($value['description'], '<br>');
            $data['description_en']['@cdata'] = str_replace("<br>", "\r\n", $description) ?? '';

            $data['completion_status'] = $value['completion_status'] ?? "";

            $features = [];

            foreach ($value["amenities"] ?? [] as $amenity) {
                $amenityType = $amenity["amenity"]["amenity_type"] ?? [];
                $features[] = $amenity["amenity"]["portals"][0]["title"];
            }

            $data['amenities'] = implode(',', $features);
            $data['size'] = $value['built_up_area'] ?? '';
            $data['bedroom'] = $value["bed"]["portals"][0]["name"] ?? 0;
            $data['bathroom'] = $value["bath"]["portals"][0]["name"] ?? 0;

            //$data['view'] = $value['view'] ?? '';


            if ($value['agent']) {
                if($value['agent']['pp_enabled']){
                    $data['agent']['name']['@cdata'] = $value['agent']['pp_name'];
                    $data['agent']['photo'] = $value['agent']['pp_photo'] ?? '';
                    $data['agent']['license_no'] = $value['agent']['pp_license_no'] ?? '';
                }else{
                    $data['agent']['name']['@cdata'] = $value['agent']['first_name'] . ' ' . $value['agent']['last_name'];
                    $data['agent']['photo'] = $value['agent']['thumb_url'] ?? '';
                    $data['agent']['license_no'] = $value['agent']['license_no'] ?? '';
                }

                $data['agent']['email'] = $value['agent']['secondary_email']!="" ? $value['agent']['secondary_email']: $value['agent']['email'] ?? '';
                $data['agent']['phone'] = str_replace("+971", "0", $value['agent']['mobile']) ?? '';
            }
            $data['developer'] = $value['details']['developer'] ?? null;
            $data['parking'] = $value['parking'] ?? 0;

            if ($value['furnished'] == 'FURNISHED') {
                $data['furnished'] = 'Yes';
            } else {
                if ($value['furnished'] == 'SEMI_FURNISHED') {
                    $data['furnished'] = 'Partly';
                } else {
                    $data['furnished'] = 'No';
                }
            }
            $data['photo'] = [];
            if ($value['photos']) {
                $url = [];
                foreach ($value['photos'] as $key => $photo) {
                    $dd['@attributes'] = ['last_updated' => date('Y-m-d H:i:s', strtotime($photo['updated_at'])), 'watermark' => 'yes'];
                    $dd['@value'] = $photo['path'];
                    $url['url'][] = $dd;
                }
                $data['photo'] = $url;
            }




            $prop['property'][] = $data;
            if (!empty($value["community_property"])) {
                $data['geopoints'] = $value["community_property"]["latitude"] . ", " . $value["community_property"]["longitude"];
            } elseif (!empty($value["sub_community"])) {
                $data['geopoints'] = $value["sub_community"]["latitude"] . ", " . $value["sub_community"]["longitude"];
            } else {
                $data['geopoints'] = ''; //$value['latitude'] . ',' . $value['longitude'];
            }
        }

        $last_update = $last_update['updated_at'];
        $this->listing_count = $listing_count;
        $pfObj['last_update'] = date('Y-m-d H:i:s', strtotime($last_update));
        $pfObj['listing_count'] = $listing_count;

        $xml = $this->createXML($pfObj, 'list', $prop);
        $xxx = $xml->saveXML();

        return \Response::make($xxx, '200')->header('Content-Type', 'text/xml');
        }catch(\Exception $e){
            
        }
    }
    public function houzaUpdated()
    {
        try{
        $mainPortal = Portal::where("slug", "houza")->latest()->first();
        $genericPortal = Portal::where("slug", "generic-portal")->latest()->first();
        if (is_null($mainPortal)) {
            abort(404);
        }
        //  abort_if($mainPortal->xml_url != $request->key, 404);

        $last_update = Property::whereHas("portals", function ($q) use ($mainPortal) {
            $q->where("portal_id", $mainPortal->id);
        })->orderBy('updated_at', 'desc')
        ->select('updated_at')->first()->toArray();

        $listing_count = Property::whereHas("portals", function ($q) use ($mainPortal) {
            $q->where("portal_id", $mainPortal->id);
        })->where(['publish_status' => 'PUBLISHED'])->count();

        $prop = [];

        $pp = Property::whereHas("portals", function ($q) use ($mainPortal) {
                $q->where("portal_id", $mainPortal->id);
            })
            ->where(['publish_status' => 'PUBLISHED'])
            ->with([
                'agent' => function($q){
                    $q->select('id','name','first_name','last_name','pp_enabled','pp_name','pp_photo', 'pp_license_no', 'email','secondary_email','license_no','mobile','bio');
                },
                'details',
                'propertyCategory' => function ($q) {
                    $q->select('id');
                }, 
                'propertyCategory.portals'=> function($q) use ($mainPortal){
                    $q->where('portal_id', $mainPortal->id)->select('id', 'title','property_category_id', 'portal_id');
                },
                'bed' => function ($q) {
                    $q->select('id');
                },
                'bed.portals' => function($q) use ($mainPortal){
                    $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bed_id', 'portal_id');
                },'developer', 
                'price'=> function ($q) {
                    $q->select('id','property_id','price','prices','price_on_application');
                },
                'bath' => function ($q) {
                    $q->select('id');
                }, 
                'bath.portals' => function($q) use ($mainPortal){
                    $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bath_id', 'portal_id');
                }, 
                'amenities' => function ($q) use ($mainPortal) {
                        $q->whereHas('amenity.portals', function($q) use ($mainPortal) {
                              $q->where('portal_id', '=', $mainPortal->id);
                          });
                    },
                    'amenities.amenity.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', '=', $mainPortal->id)
                          ->where('title', '!=', '');
                    },
                'community.region', 'subCommunity', 'communityProperty',
                'photos' => function($q){
                    $q->select('id','property_id','path','title','updated_at');
                }, 
                'floorPlan'])
            ->orderBy('properties.created_at', 'desc')
            ->get();
        foreach ($pp as $key => $value) {
            $data['@attributes'] = ['last_update' => date('Y-m-d H:i:s', strtotime($value['updated_at']))];
            $data['permit_number'] = $value['rera_permit_number'] ?? '';
            $data['reference_number'] = $value['ref_no'];
            $data['price'] = [];
            if ($value['for'] == "Residential for sale" || $value['for'] == "Commercial for sale") {
                $data['price'] = $value['price']["price"] ?? 0;
                $data['offering_type'] = $value['for'] == "Commercial for sale" ? 'CS' : 'RS';
            } else {
                $data['price']['yearly'] = $value['price']["price"] ?? 0;
                $data['offering_type'] = $value['for'] == "Commercial for rent" ? 'CR' : 'RR';
            }
            $data['property_type'] = $value['propertyCategory']['portals'][0]["title"] ?? "";
            $data['price_on_application'] = 'No';

            $data['city']['@cdata'] = $value['community']['region']['name'] ?? 'Dubai';
            $data['community']['@cdata'] = $value['community']['name'] ?? '';
            $data['sub_community']['@cdata'] = $value['subCommunity']['name'] ?? '';
            $data['property_name']['@cdata'] = $value['communityProperty']['name'] ?? '';



            $data['title_en']['@cdata'] = $value['title'] ?? '';
            //$data['description_en']['@cdata'] = $value['description'] ?? '';

            $description = strip_tags($value['description'], '<br>');
            $data['description_en']['@cdata'] = str_replace("<br>", "\r\n", $description) ?? '';

            $data['completion_status'] = $value['completion_status'] ?? "";

            $features = [];

            foreach ($value["amenities"] ?? [] as $amenity) {
                $amenityType = $amenity["amenity"]["amenity_type"] ?? [];
                $features[] = $amenity["amenity"]["portals"][0]["title"];
            }

            $data['amenities'] = implode(',', $features);
            $data['size'] = $value['built_up_area'] ?? '';
            $data['bedroom'] = $value["bed"]["portals"][0]["name"] ?? 0;
            $data['bathroom'] = $value["bath"]["portals"][0]["name"] ?? 0;

            //$data['view'] = $value['view'] ?? '';


            if ($value['agent']) {
                if($value['agent']['pp_enabled']){
                    $data['agent']['name']['@cdata'] = $value['agent']['pp_name'];
                    $data['agent']['photo'] = $value['agent']['pp_photo'] ?? '';
                    $data['agent']['license_no'] = $value['agent']['pp_license_no'] ?? '';
                }else{
                    $data['agent']['name']['@cdata'] = $value['agent']['first_name'] . ' ' . $value['agent']['last_name'];
                    $data['agent']['photo'] = $value['agent']['thumb_url'] ?? '';
                    $data['agent']['license_no'] = $value['agent']['license_no'] ?? '';
                }

                $data['agent']['email'] = $value['agent']['secondary_email']!="" ? $value['agent']['secondary_email']: $value['agent']['email'] ?? '';
                $data['agent']['phone'] = str_replace("+971", "0", $value['agent']['mobile']) ?? '';
            }
            $data['developer'] = $value['details']['developer'] ?? null;
            $data['parking'] = $value['parking'] ?? 0;

            if ($value['furnished'] == 'FURNISHED') {
                $data['furnished'] = 'Yes';
            } else {
                if ($value['furnished'] == 'SEMI_FURNISHED') {
                    $data['furnished'] = 'Partly';
                } else {
                    $data['furnished'] = 'No';
                }
            }
            $data['photo'] = [];
            if ($value['photos']) {
                $url = [];
                foreach ($value['photos'] as $key => $photo) {
                    $dd['@attributes'] = ['last_updated' => date('Y-m-d H:i:s', strtotime($photo['updated_at'])), 'watermark' => 'yes'];
                    $dd['@value'] = $photo['path'];
                    $url['url'][] = $dd;
                }
                $data['photo'] = $url;
            }




            $prop['property'][] = $data;
            if (!empty($value["communityProperty"])) {
                $data['geopoints'] = $value["communityProperty"]["latitude"] . ", " . $value["communityProperty"]["longitude"];
            } elseif (!empty($value["subCommunity"])) {
                $data['geopoints'] = $value["subCommunity"]["latitude"] . ", " . $value["subCommunity"]["longitude"];
            } else {
                $data['geopoints'] = ''; //$value['latitude'] . ',' . $value['longitude'];
            }
        }

        $last_update = $last_update['updated_at'];
        $this->listing_count = $listing_count;
        $pfObj['last_update'] = date('Y-m-d H:i:s', strtotime($last_update));
        $pfObj['listing_count'] = $listing_count;

        $xml = $this->createXML($pfObj, 'list', $prop);
        $xxx = $xml->saveXML();

        return \Response::make($xxx, '200')->header('Content-Type', 'text/xml');
        }catch(\Exception $e){
            
        }
    }


    public function emiratesEstate()
    {
        try{
        $mainPortal = Portal::where("slug", "emiratesestate")->latest()->first();
        $genericPortal = Portal::where("slug", "generic-portal")->latest()->first();
        if (is_null($mainPortal)) {
            abort(404);
        }
        //  abort_if($mainPortal->xml_url != $request->key, 404);

        $last_update = Property::whereHas("portals", function ($q) use ($mainPortal) {
            $q->where("portal_id", $mainPortal->id);
        })->orderBy('updated_at', 'desc')
        ->select('updated_at')->first()->toArray();

        $listing_count = Property::whereHas("portals", function ($q) use ($mainPortal) {
            $q->where("portal_id", $mainPortal->id);
        })->where(['publish_status' => 'PUBLISHED'])->count();

        $prop = [];

        $pp = Property::whereHas("portals", function ($q) use ($mainPortal) {
                $q->where("portal_id", $mainPortal->id);
            })
            ->where(['publish_status' => 'PUBLISHED'])
            ->with([
                'agent' => function($q){
                    $q->select('id','name','first_name','last_name','pp_enabled','pp_name','pp_photo', 'pp_license_no', 'email','secondary_email','license_no','mobile','bio');
                },
                'details',
                'propertyCategory' => function ($q) {
                    $q->select('id');
                }, 
                'propertyCategory.portals'=> function($q) use ($mainPortal){
                    $q->where('portal_id', $mainPortal->id)->select('id', 'title','property_category_id', 'portal_id');
                },
                'bed' => function ($q) {
                    $q->select('id');
                },
                'bed.portals' => function($q) use ($mainPortal){
                    $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bed_id', 'portal_id');
                },'developer', 
                'price'=> function ($q) {
                    $q->select('id','property_id','price','prices','price_on_application');
                },
                'bath' => function ($q) {
                    $q->select('id');
                }, 
                'bath.portals' => function($q) use ($mainPortal){
                    $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bath_id', 'portal_id');
                }, 
                'amenities' => function ($q) use ($mainPortal) {
                        $q->whereHas('amenity.portals', function($q) use ($mainPortal) {
                              $q->where('portal_id', '=', $mainPortal->id);
                          });
                    },
                    'amenities.amenity.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', '=', $mainPortal->id)
                          ->where('title', '!=', '');
                    },
                'community.region', 'subCommunity', 'communityProperty',
                'photos' => function($q){
                    $q->select('id','property_id','path','title','updated_at');
                }, 
                'floorPlan'])
            ->orderBy('properties.created_at', 'desc')
            ->get()
            ->toArray();
        foreach ($pp as $key => $value) {
            $data['@attributes'] = ['last_update' => date('Y-m-d H:i:s', strtotime($value['updated_at']))];
            $data['permit_number'] = $value['rera_permit_number'] ?? '';
            $data['reference_number'] = $value['ref_no'];
            $data['price'] = [];
            if ($value['for'] == "Residential for sale" || $value['for'] == "Commercial for sale") {
                $data['price'] = $value['price']["price"] ?? 0;
                $data['offering_type'] = $value['for'] == "Commercial for sale" ? 'CS' : 'RS';
            } else {
                $data['price'] = $value['price']["price"] ?? 0;
                $data['rental_period'] = 'Y';
                $data['offering_type'] = $value['for'] == "Commercial for rent" ? 'CR' : 'RR';
            }
            $data['property_type'] = $value['property_category']['portals'][0]["title"] ?? "";
            $data['price_on_application'] = 'No';

            $data['city']['@cdata'] = $value['community']['region']['name'] ?? 'Dubai';
            $data['community']['@cdata'] = $value['community']['name'] ?? '';
            $data['sub_community']['@cdata'] = $value['sub_community']['name'] ?? '';
            $data['property_name']['@cdata'] = $value['community_property']['name'] ?? '';



            $data['title_en']['@cdata'] = $value['title'] ?? '';
            //$data['description_en']['@cdata'] = $value['description'] ?? '';

            $description = strip_tags($value['description'], '<br>');
            $data['description_en']['@cdata'] = str_replace("<br>", "\r\n", $description) ?? '';

            $data['completion_status'] = $value['completion_status'] ?? "";

            $features = [];

            foreach ($value["amenities"] ?? [] as $amenity) {
                $amenityType = $amenity["amenity"]["amenity_type"] ?? [];
                $features[] = $amenity["amenity"]["portals"][0]["title"];
            }

            $data['amenities'] = implode(',', $features);
            $data['size'] = $value['built_up_area'] ?? '';
            $data['bedroom'] = $value["bed"]["portals"][0]["name"] ?? 0;
            $data['bathroom'] = $value["bath"]["portals"][0]["name"] ?? 0;

            //$data['view'] = $value['view'] ?? '';


            if ($value['agent']) {
                if($value['agent']['pp_enabled']){
                    $data['agent']['name']['@cdata'] = $value['agent']['pp_name'];
                    $data['agent']['photo'] = $value['agent']['pp_photo'] ?? '';
                    $data['agent']['license_no'] = $value['agent']['pp_license_no'] ?? '';
                }else{
                    $data['agent']['name']['@cdata'] = $value['agent']['first_name'] . ' ' . $value['agent']['last_name'];
                    $data['agent']['photo'] = $value['agent']['thumb_url'] ?? '';
                    $data['agent']['license_no'] = $value['agent']['license_no'] ?? '';
                }

                $data['agent']['email'] = $value['agent']['secondary_email']!="" ? $value['agent']['secondary_email']: $value['agent']['email'] ?? '';
                $data['agent']['phone'] = str_replace("+971", "0", $value['agent']['mobile']) ?? '';
            }
            $data['developer'] = $value['details']['developer'] ?? null;
            $data['parking'] = $value['parking'] ?? 0;

            if ($value['furnished'] == 'FURNISHED') {
                $data['furnished'] = 'Yes';
            } else {
                if ($value['furnished'] == 'SEMI_FURNISHED') {
                    $data['furnished'] = 'Partly';
                } else {
                    $data['furnished'] = 'No';
                }
            }
            $data['photo'] = [];
            if ($value['photos']) {
                $url = [];
                foreach ($value['photos'] as $key => $photo) {
                    $dd['@attributes'] = ['last_updated' => date('Y-m-d H:i:s', strtotime($photo['updated_at'])), 'watermark' => 'yes'];
                    $dd['@value'] = $photo['path'];
                    $url['url'][] = $dd;
                }
                $data['photo'] = $url;
            }




            $prop['property'][] = $data;
            if (!empty($value["community_property"])) {
                $data['geopoints'] = $value["community_property"]["longitude"] . ", " . $value["community_property"]["latitude"];
            } elseif (!empty($value["sub_community"])) {
                $data['geopoints'] = $value["sub_community"]["longitude"] . ", " . $value["sub_community"]["latitude"];
            } else {
                $data['geopoints'] = ''; //$value['latitude'] . ',' . $value['longitude'];
            }
        }

        $last_update = $last_update['updated_at'];
        $this->listing_count = $listing_count;
        $pfObj['last_update'] = date('Y-m-d H:i:s', strtotime($last_update));
        $pfObj['listing_count'] = $listing_count;

        $xml = $this->createXML($pfObj, 'list', $prop);
        $xxx = $xml->saveXML();

        return \Response::make($xxx, '200')->header('Content-Type', 'text/xml');
        }catch(\Exception $e){
            
        }
    }

    public function emiratesEstateUpdated()
    {
        try{
        $mainPortal = Portal::where("slug", "emiratesestate")->latest()->first();
        $genericPortal = Portal::where("slug", "generic-portal")->latest()->first();
        if (is_null($mainPortal)) {
            abort(404);
        }
        //  abort_if($mainPortal->xml_url != $request->key, 404);

        $last_update = Property::whereHas("portals", function ($q) use ($mainPortal) {
            $q->where("portal_id", $mainPortal->id);
        })->orderBy('updated_at', 'desc')
        ->select('updated_at')->first()->toArray();

        $listing_count = Property::whereHas("portals", function ($q) use ($mainPortal) {
            $q->where("portal_id", $mainPortal->id);
        })->where(['publish_status' => 'PUBLISHED'])->count();

        $prop = [];

        $pp = Property::whereHas("portals", function ($q) use ($mainPortal) {
                $q->where("portal_id", $mainPortal->id);
            })
            ->where(['publish_status' => 'PUBLISHED'])
            ->with([
                'agent' => function($q){
                    $q->select('id','name','first_name','last_name','pp_enabled','pp_name','pp_photo', 'pp_license_no', 'email','secondary_email','license_no','mobile','bio');
                },
                'details',
                'propertyCategory' => function ($q) {
                    $q->select('id');
                }, 
                'propertyCategory.portals'=> function($q) use ($mainPortal){
                    $q->where('portal_id', $mainPortal->id)->select('id', 'title','property_category_id', 'portal_id');
                },
                'bed' => function ($q) {
                    $q->select('id');
                },
                'bed.portals' => function($q) use ($mainPortal){
                    $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bed_id', 'portal_id');
                },'developer', 
                'price'=> function ($q) {
                    $q->select('id','property_id','price','prices','price_on_application');
                },
                'bath' => function ($q) {
                    $q->select('id');
                }, 
                'bath.portals' => function($q) use ($mainPortal){
                    $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bath_id', 'portal_id');
                }, 
                'amenities' => function ($q) use ($mainPortal) {
                        $q->whereHas('amenity.portals', function($q) use ($mainPortal) {
                              $q->where('portal_id', '=', $mainPortal->id);
                          });
                    },
                    'amenities.amenity.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', '=', $mainPortal->id)
                          ->where('title', '!=', '');
                    },
                'community.region', 'subCommunity', 'communityProperty',
                'photos' => function($q){
                    $q->select('id','property_id','path','title','updated_at');
                }, 
                'floorPlan'])
            ->orderBy('properties.created_at', 'desc')
            ->get();
        foreach ($pp as $key => $value) {
            $data['@attributes'] = ['last_update' => date('Y-m-d H:i:s', strtotime($value['updated_at']))];
            $data['permit_number'] = $value['rera_permit_number'] ?? '';
            $data['reference_number'] = $value['ref_no'];
            $data['price'] = [];
            if ($value['for'] == "Residential for sale" || $value['for'] == "Commercial for sale") {
                $data['price'] = $value['price']["price"] ?? 0;
                $data['offering_type'] = $value['for'] == "Commercial for sale" ? 'CS' : 'RS';
            } else {
                $data['price'] = $value['price']["price"] ?? 0;
                $data['rental_period'] = 'Y';
                $data['offering_type'] = $value['for'] == "Commercial for rent" ? 'CR' : 'RR';
            }
            $data['property_type'] = $value['propertyCategory']['portals'][0]["title"] ?? "";
            $data['price_on_application'] = 'No';

            $data['city']['@cdata'] = $value['community']['region']['name'] ?? 'Dubai';
            $data['community']['@cdata'] = $value['community']['name'] ?? '';
            $data['sub_community']['@cdata'] = $value['subCommunity']['name'] ?? '';
            $data['property_name']['@cdata'] = $value['communityProperty']['name'] ?? '';



            $data['title_en']['@cdata'] = $value['title'] ?? '';
            //$data['description_en']['@cdata'] = $value['description'] ?? '';

            $description = strip_tags($value['description'], '<br>');
            $data['description_en']['@cdata'] = str_replace("<br>", "\r\n", $description) ?? '';

            $data['completion_status'] = $value['completion_status'] ?? "";

            $features = [];

            foreach ($value["amenities"] ?? [] as $amenity) {
                $amenityType = $amenity["amenity"]["amenity_type"] ?? [];
                $features[] = $amenity["amenity"]["portals"][0]["title"];
            }

            $data['amenities'] = implode(',', $features);
            $data['size'] = $value['built_up_area'] ?? '';
            $data['bedroom'] = $value["bed"]["portals"][0]["name"] ?? 0;
            $data['bathroom'] = $value["bath"]["portals"][0]["name"] ?? 0;

            //$data['view'] = $value['view'] ?? '';


            if ($value['agent']) {
                if($value['agent']['pp_enabled']){
                    $data['agent']['name']['@cdata'] = $value['agent']['pp_name'];
                    $data['agent']['photo'] = $value['agent']['pp_photo'] ?? '';
                    $data['agent']['license_no'] = $value['agent']['pp_license_no'] ?? '';
                }else{
                    $data['agent']['name']['@cdata'] = $value['agent']['first_name'] . ' ' . $value['agent']['last_name'];
                    $data['agent']['photo'] = $value['agent']['thumb_url'] ?? '';
                    $data['agent']['license_no'] = $value['agent']['license_no'] ?? '';
                }

                $data['agent']['email'] = $value['agent']['secondary_email']!="" ? $value['agent']['secondary_email']: $value['agent']['email'] ?? '';
                $data['agent']['phone'] = str_replace("+971", "0", $value['agent']['mobile']) ?? '';
            }
            $data['developer'] = $value['details']['developer'] ?? null;
            $data['parking'] = $value['parking'] ?? 0;

            if ($value['furnished'] == 'FURNISHED') {
                $data['furnished'] = 'Yes';
            } else {
                if ($value['furnished'] == 'SEMI_FURNISHED') {
                    $data['furnished'] = 'Partly';
                } else {
                    $data['furnished'] = 'No';
                }
            }
            $data['photo'] = [];
            if ($value['photos']) {
                $url = [];
                foreach ($value['photos'] as $key => $photo) {
                    $dd['@attributes'] = ['last_updated' => date('Y-m-d H:i:s', strtotime($photo['updated_at'])), 'watermark' => 'yes'];
                    $dd['@value'] = $photo['path'];
                    $url['url'][] = $dd;
                }
                $data['photo'] = $url;
            }




            $prop['property'][] = $data;
            if (!empty($value["communityProperty"])) {
                $data['geopoints'] = $value["communityProperty"]["longitude"] . ", " . $value["communityProperty"]["latitude"];
            } elseif (!empty($value["subCommunity"])) {
                $data['geopoints'] = $value["subCommunity"]["longitude"] . ", " . $value["subCommunity"]["latitude"];
            } else {
                $data['geopoints'] = ''; //$value['latitude'] . ',' . $value['longitude'];
            }
        }

        $last_update = $last_update['updated_at'];
        $this->listing_count = $listing_count;
        $pfObj['last_update'] = date('Y-m-d H:i:s', strtotime($last_update));
        $pfObj['listing_count'] = $listing_count;

        $xml = $this->createXML($pfObj, 'list', $prop);
        $xxx = $xml->saveXML();

        return \Response::make($xxx, '200')->header('Content-Type', 'text/xml');
        }catch(\Exception $e){
            
        }
    }



    public function rightMove()
    {
        try{
        $mainPortal = Portal::where("slug", "right-move")->latest()->first();
        $genericPortal = Portal::where("slug", "generic-portal")->latest()->first();
        if (is_null($mainPortal)) {
            abort(404);
        }
        //  abort_if($mainPortal->xml_url != $request->key, 404);

        $last_update = Property::whereHas("portals", function ($q) use ($mainPortal) {
            $q->where("portal_id", $mainPortal->id);
        })->orderBy('updated_at', 'desc')
        ->select('updated_at')->first()->toArray();

        $listing_count = Property::whereHas("portals", function ($q) use ($mainPortal) {
            $q->where("portal_id", $mainPortal->id);
        })->where(['publish_status' => 'PUBLISHED'])->count();

        $prop = [];

        $pp = Property::whereHas("portals", function ($q) use ($mainPortal) {
                $q->where("portal_id", $mainPortal->id);
            })
            ->where(['publish_status' => 'PUBLISHED'])
            ->with([
                'agent' => function($q){
                    $q->select('id','name','first_name','last_name','pp_enabled','pp_name','pp_photo', 'pp_license_no', 'email','secondary_email','license_no','mobile','bio');
                },
                'details',
                'propertyCategory' => function ($q) {
                    $q->select('id');
                }, 
                'propertyCategory.portals'=> function($q) use ($mainPortal){
                    $q->where('portal_id', $mainPortal->id)->select('id', 'title','property_category_id', 'portal_id');
                },
                'bed' => function ($q) {
                    $q->select('id');
                },
                'bed.portals' => function($q) use ($mainPortal){
                    $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bed_id', 'portal_id');
                },'developer', 
                'price'=> function ($q) {
                    $q->select('id','property_id','price','prices','price_on_application');
                },
                'bath' => function ($q) {
                    $q->select('id');
                }, 
                'bath.portals' => function($q) use ($mainPortal){
                    $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bath_id', 'portal_id');
                }, 
                'amenities' => function ($q) use ($mainPortal) {
                        $q->whereHas('amenity.portals', function($q) use ($mainPortal) {
                              $q->where('portal_id', '=', $mainPortal->id);
                          });
                    },
                    'amenities.amenity.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', '=', $mainPortal->id)
                          ->where('title', '!=', '');
                    },
                'community.region', 'subCommunity', 'communityProperty',
                'photos' => function($q){
                    $q->select('id','property_id','path','title','updated_at');
                }, 
                'floorPlan'])
            ->orderBy('properties.created_at', 'desc')
            ->get()
            ->toArray();

        foreach ($pp as $key => $value) {
            $data['@attributes'] = ['last_update' => date('Y-m-d H:i:s', strtotime($value['updated_at']))];
            $data['permit_number'] = $value['rera_permit_number'] ?? '';
            $data['reference_number'] = $value['ref_no'];
            $data['price'] = [];
            if ($value['for'] == "Residential for sale" || $value['for'] == "Commercial for sale") {
                $data['price'] = $value['price']["price"] ?? 0;
                $data['offering_type'] = $value['for'] == "Commercial for sale" ? 'CS' : 'RS';
            } else {
                $data['price']['yearly'] = $value['price']["price"] ?? 0;
                $data['offering_type'] = $value['for'] == "Commercial for rent" ? 'CR' : 'RR';
            }
            $data['property_type'] = $value['property_category']['portals'][0]["title"] ?? "";
            $data['price_on_application'] = 'No';

            $data['city']['@cdata'] = $value['community']['region']['name'] ?? 'Dubai';
            $data['community']['@cdata'] = $value['community']['name'] ?? '';
            $data['sub_community']['@cdata'] = $value['sub_community']['name'] ?? '';
            $data['property_name']['@cdata'] = $value['community_property']['name'] ?? '';



            $data['title_en']['@cdata'] = $value['title'] ?? '';
            //$data['description_en']['@cdata'] = $value['description'] ?? '';

            $description = strip_tags($value['description'], '<br>');
            $data['description_en']['@cdata'] = str_replace("<br>", "\r\n", $description) ?? '';

            $data['completion_status'] = $value['completion_status'] ?? "";

            $features = [];

            foreach ($value["amenities"] ?? [] as $amenity) {
                $amenityType = $amenity["amenity"]["amenity_type"] ?? [];
                $features[] =  $amenity["amenity"]["portals"][0]["title"];
            }

            $data['amenities'] = implode(',', $features);
            $data['size'] = $value['built_up_area'] ?? '';
            $data['bedroom'] = $value["bed"]["portals"][0]["name"] ?? 0;
            $data['bathroom'] = $value["bath"]["portals"][0]["name"] ?? 0;

            //$data['view'] = $value['view'] ?? '';


            if ($value['agent']) {
                if($value['agent']['pp_enabled']){
                    $data['agent']['name']['@cdata'] = $value['agent']['pp_name'];
                    $data['agent']['photo'] = $value['agent']['pp_photo'] ?? '';
                    $data['agent']['license_no'] = $value['agent']['pp_license_no'] ?? '';
                }else{
                    $data['agent']['name']['@cdata'] = $value['agent']['first_name'] . ' ' . $value['agent']['last_name'];
                    $data['agent']['photo'] = $value['agent']['thumb_url'] ?? '';
                    $data['agent']['license_no'] = $value['agent']['license_no'] ?? '';
                }

                $data['agent']['email'] = $value['agent']['secondary_email']!="" ? $value['agent']['secondary_email']: $value['agent']['email'] ?? '';
                $data['agent']['phone'] = str_replace("+971", "0", $value['agent']['mobile']) ?? '';
            }
            $data['developer'] = $value['details']['developer'] ?? null;
            $data['parking'] = $value['parking'] ?? 0;

            if ($value['furnished'] == 'FURNISHED') {
                $data['furnished'] = 'Yes';
            } else {
                if ($value['furnished'] == 'SEMI_FURNISHED') {
                    $data['furnished'] = 'Partly';
                } else {
                    $data['furnished'] = 'No';
                }
            }
            $data['photo'] = [];
            if ($value['photos']) {
                $url = [];
                foreach ($value['photos'] as $key => $photo) {
                    $dd['@attributes'] = ['last_updated' => date('Y-m-d H:i:s', strtotime($photo['updated_at'])), 'watermark' => 'yes'];
                    $dd['@value'] = $photo['path'];
                    $url['url'][] = $dd;
                }
                $data['photo'] = $url;
            }




            $prop['property'][] = $data;
            if (!empty($value["community_property"])) {
                $data['geopoints'] = $value["community_property"]["latitude"] . ", " . $value["community_property"]["longitude"];
            } elseif (!empty($value["sub_community"])) {
                $data['geopoints'] = $value["sub_community"]["latitude"] . ", " . $value["sub_community"]["longitude"];
            } else {
                $data['geopoints'] = ''; //$value['latitude'] . ',' . $value['longitude'];
            }
        }

        $last_update = $last_update['updated_at'];
        $this->listing_count = $listing_count;
        $pfObj['last_update'] = date('Y-m-d H:i:s', strtotime($last_update));
        $pfObj['listing_count'] = $listing_count;

        $xml = $this->createXML($pfObj, 'list', $prop);
        $xxx = $xml->saveXML();

        return \Response::make($xxx, '200')->header('Content-Type', 'text/xml');
        }catch(\Exception $e){
            
        }
    }
    public function rightMoveUpdated()
    {
        try{
        $mainPortal = Portal::where("slug", "right-move")->latest()->first();
        $genericPortal = Portal::where("slug", "generic-portal")->latest()->first();
        if (is_null($mainPortal)) {
            abort(404);
        }
        //  abort_if($mainPortal->xml_url != $request->key, 404);

        $last_update = Property::whereHas("portals", function ($q) use ($mainPortal) {
            $q->where("portal_id", $mainPortal->id);
        })->orderBy('updated_at', 'desc')
        ->select('updated_at')->first()->toArray();

        $listing_count = Property::whereHas("portals", function ($q) use ($mainPortal) {
            $q->where("portal_id", $mainPortal->id);
        })->where(['publish_status' => 'PUBLISHED'])->count();

        $prop = [];

        $pp = Property::whereHas("portals", function ($q) use ($mainPortal) {
                $q->where("portal_id", $mainPortal->id);
            })
            ->where(['publish_status' => 'PUBLISHED'])
            ->with([
                'agent' => function($q){
                    $q->select('id','name','first_name','last_name','pp_enabled','pp_name','pp_photo', 'pp_license_no', 'email','secondary_email','license_no','mobile','bio');
                },
                'details',
                'propertyCategory' => function ($q) {
                    $q->select('id');
                }, 
                'propertyCategory.portals'=> function($q) use ($mainPortal){
                    $q->where('portal_id', $mainPortal->id)->select('id', 'title','property_category_id', 'portal_id');
                },
                'bed' => function ($q) {
                    $q->select('id');
                },
                'bed.portals' => function($q) use ($mainPortal){
                    $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bed_id', 'portal_id');
                },'developer', 
                'price'=> function ($q) {
                    $q->select('id','property_id','price','prices','price_on_application');
                },
                'bath' => function ($q) {
                    $q->select('id');
                }, 
                'bath.portals' => function($q) use ($mainPortal){
                    $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bath_id', 'portal_id');
                }, 
                'amenities' => function ($q) use ($mainPortal) {
                        $q->whereHas('amenity.portals', function($q) use ($mainPortal) {
                              $q->where('portal_id', '=', $mainPortal->id);
                          });
                    },
                    'amenities.amenity.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', '=', $mainPortal->id)
                          ->where('title', '!=', '');
                    },
                'community.region', 'subCommunity', 'communityProperty',
                'photos' => function($q){
                    $q->select('id','property_id','path','title','updated_at');
                }, 
                'floorPlan'])
            ->orderBy('properties.created_at', 'desc')
            ->get();

        foreach ($pp as $key => $value) {
            $data['@attributes'] = ['last_update' => date('Y-m-d H:i:s', strtotime($value['updated_at']))];
            $data['permit_number'] = $value['rera_permit_number'] ?? '';
            $data['reference_number'] = $value['ref_no'];
            $data['price'] = [];
            if ($value['for'] == "Residential for sale" || $value['for'] == "Commercial for sale") {
                $data['price'] = $value['price']["price"] ?? 0;
                $data['offering_type'] = $value['for'] == "Commercial for sale" ? 'CS' : 'RS';
            } else {
                $data['price']['yearly'] = $value['price']["price"] ?? 0;
                $data['offering_type'] = $value['for'] == "Commercial for rent" ? 'CR' : 'RR';
            }
            $data['property_type'] = $value['propertyCategory']['portals'][0]["title"] ?? "";
            $data['price_on_application'] = 'No';

            $data['city']['@cdata'] = $value['community']['region']['name'] ?? 'Dubai';
            $data['community']['@cdata'] = $value['community']['name'] ?? '';
            $data['sub_community']['@cdata'] = $value['subCommunity']['name'] ?? '';
            $data['property_name']['@cdata'] = $value['communityProperty']['name'] ?? '';



            $data['title_en']['@cdata'] = $value['title'] ?? '';
            //$data['description_en']['@cdata'] = $value['description'] ?? '';

            $description = strip_tags($value['description'], '<br>');
            $data['description_en']['@cdata'] = str_replace("<br>", "\r\n", $description) ?? '';

            $data['completion_status'] = $value['completion_status'] ?? "";

            $features = [];

            foreach ($value["amenities"] ?? [] as $amenity) {
                $amenityType = $amenity["amenity"]["amenity_type"] ?? [];
                $features[] =  $amenity["amenity"]["portals"][0]["title"];
            }

            $data['amenities'] = implode(',', $features);
            $data['size'] = $value['built_up_area'] ?? '';
            $data['bedroom'] = $value["bed"]["portals"][0]["name"] ?? 0;
            $data['bathroom'] = $value["bath"]["portals"][0]["name"] ?? 0;

            //$data['view'] = $value['view'] ?? '';


            if ($value['agent']) {
                if($value['agent']['pp_enabled']){
                    $data['agent']['name']['@cdata'] = $value['agent']['pp_name'];
                    $data['agent']['photo'] = $value['agent']['pp_photo'] ?? '';
                    $data['agent']['license_no'] = $value['agent']['pp_license_no'] ?? '';
                }else{
                    $data['agent']['name']['@cdata'] = $value['agent']['first_name'] . ' ' . $value['agent']['last_name'];
                    $data['agent']['photo'] = $value['agent']['thumb_url'] ?? '';
                    $data['agent']['license_no'] = $value['agent']['license_no'] ?? '';
                }

                $data['agent']['email'] = $value['agent']['secondary_email']!="" ? $value['agent']['secondary_email']: $value['agent']['email'] ?? '';
                $data['agent']['phone'] = str_replace("+971", "0", $value['agent']['mobile']) ?? '';
            }
            $data['developer'] = $value['details']['developer'] ?? null;
            $data['parking'] = $value['parking'] ?? 0;

            if ($value['furnished'] == 'FURNISHED') {
                $data['furnished'] = 'Yes';
            } else {
                if ($value['furnished'] == 'SEMI_FURNISHED') {
                    $data['furnished'] = 'Partly';
                } else {
                    $data['furnished'] = 'No';
                }
            }
            $data['photo'] = [];
            if ($value['photos']) {
                $url = [];
                foreach ($value['photos'] as $key => $photo) {
                    $dd['@attributes'] = ['last_updated' => date('Y-m-d H:i:s', strtotime($photo['updated_at'])), 'watermark' => 'yes'];
                    $dd['@value'] = $photo['path'];
                    $url['url'][] = $dd;
                }
                $data['photo'] = $url;
            }




            $prop['property'][] = $data;
            if (!empty($value["communityProperty"])) {
                $data['geopoints'] = $value["communityProperty"]["latitude"] . ", " . $value["communityProperty"]["longitude"];
            } elseif (!empty($value["subCommunity"])) {
                $data['geopoints'] = $value["subCommunity"]["latitude"] . ", " . $value["subCommunity"]["longitude"];
            } else {
                $data['geopoints'] = ''; //$value['latitude'] . ',' . $value['longitude'];
            }
        }

        $last_update = $last_update['updated_at'];
        $this->listing_count = $listing_count;
        $pfObj['last_update'] = date('Y-m-d H:i:s', strtotime($last_update));
        $pfObj['listing_count'] = $listing_count;

        $xml = $this->createXML($pfObj, 'list', $prop);
        $xxx = $xml->saveXML();

        return \Response::make($xxx, '200')->header('Content-Type', 'text/xml');
        }catch(\Exception $e){
            
        }
    }
    public function genericportal()
    {
        try{
        $mainPortal = Portal::where("slug", "generic-portal")->latest()->first();
        if (is_null($mainPortal)) {
            abort(404);
        }
        //  abort_if($mainPortal->xml_url != $request->key, 404);

        $last_update = Property::whereHas("portals", function ($q) use ($mainPortal) {
            $q->where("portal_id", $mainPortal->id);
        })->orderBy('updated_at', 'desc')
        ->select('updated_at')->first()->toArray();

        $listing_count = Property::whereHas("portals", function ($q) use ($mainPortal) {
            $q->where("portal_id", $mainPortal->id);
        })->where(['publish_status' => 'PUBLISHED'])->count();

        $prop = [];

        $pp = Property::whereHas("portals", function ($q) use ($mainPortal) {
                $q->where("portal_id", $mainPortal->id);
            })
            ->where(['publish_status' => 'PUBLISHED'])
            ->with([
                'agent' => function($q){
                    $q->select('id','name','first_name','last_name','pp_enabled','pp_name','pp_photo', 'pp_license_no', 'email','secondary_email','license_no','mobile','bio');
                },
                'details',
                'propertyCategory' => function ($q) {
                    $q->select('id');
                }, 
                'propertyCategory.portals'=> function($q) use ($mainPortal){
                    $q->where('portal_id', $mainPortal->id)->select('id', 'title','property_category_id', 'portal_id');
                },
                'bed' => function ($q) {
                    $q->select('id');
                },
                'bed.portals' => function($q) use ($mainPortal){
                    $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bed_id', 'portal_id');
                },'developer', 
                'price'=> function ($q) {
                    $q->select('id','property_id','price','prices','price_on_application');
                },
                'bath' => function ($q) {
                    $q->select('id');
                }, 
                'bath.portals' => function($q) use ($mainPortal){
                    $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bath_id', 'portal_id');
                }, 
                'amenities' => function ($q) use ($mainPortal) {
                        $q->whereHas('amenity.portals', function($q) use ($mainPortal) {
                              $q->where('portal_id', '=', $mainPortal->id);
                          });
                    },
                    'amenities.amenity.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', '=', $mainPortal->id)
                          ->where('title', '!=', '');
                    },
                'community.region', 'subCommunity', 'communityProperty',
                'photos' => function($q){
                    $q->select('id','property_id','path','title','updated_at');
                }, 
                'floorPlan'])
            ->orderBy('properties.created_at', 'desc')
            ->get()
            ->toArray();

        foreach ($pp as $key => $value) {
            $data['@attributes'] = ['last_update' => date('Y-m-d H:i:s', strtotime($value['updated_at']))];
            $data['permit_number'] = $value['rera_permit_number'] ?? '';
            $data['reference_number'] = $value['ref_no'];
            $data['price'] = [];
            if ($value['for'] == "Residential for sale" || $value['for'] == "Commercial for sale") {
                $data['price'] = $value['price']["price"] ?? 0;
                $data['offering_type'] = $value['for'] == "Commercial for sale" ? 'CS' : 'RS';
            } else {
                $data['price']['yearly'] = $value['price']["price"] ?? 0;
                $data['offering_type'] = $value['for'] == "Commercial for rent" ? 'CR' : 'RR';
            }
            $data['property_type'] = $value['property_category']['portals'][0]["title"] ?? "";
            $data['price_on_application'] = 'No';

            $data['city']['@cdata'] = $value['community']['region']['name'] ?? 'Dubai';
            $data['community']['@cdata'] = $value['community']['name'] ?? '';
            $data['sub_community']['@cdata'] = $value['sub_community']['name'] ?? '';
            $data['property_name']['@cdata'] = $value['community_property']['name'] ?? '';



            $data['title_en']['@cdata'] = $value['title'] ?? '';
            //$data['description_en']['@cdata'] = $value['description'] ?? '';

            $description = strip_tags($value['description'], '<br>');
            $data['description_en']['@cdata'] = str_replace("<br>", "\r\n", $description) ?? '';

            $data['completion_status'] = $value['completion_status'] ?? "";

            $features = [];

            foreach ($value["amenities"] ?? [] as $amenity) {
                $amenityType = $amenity["amenity"]["amenity_type"] ?? [];
                $features[] = $amenity["amenity"]["portals"][0]["title"];
            }

            $data['amenities'] = implode(',', $features);
            $data['size'] = $value['built_up_area'] ?? '';
            $data['bedroom'] = $value["bed"]["portals"][0]["name"] ?? 0;
            $data['bathroom'] = $value["bath"]["portals"] [0]["name"] ?? 0;

            //$data['view'] = $value['view'] ?? '';


            if ($value['agent']) {
                if($value['agent']['pp_enabled']){
                    $data['agent']['name']['@cdata'] = $value['agent']['pp_name'];
                    $data['agent']['photo'] = $value['agent']['pp_photo'] ?? '';
                    $data['agent']['license_no'] = $value['agent']['pp_license_no'] ?? '';
                }else{
                    $data['agent']['name']['@cdata'] = $value['agent']['first_name'] . ' ' . $value['agent']['last_name'];
                    $data['agent']['photo'] = $value['agent']['thumb_url'] ?? '';
                    $data['agent']['license_no'] = $value['agent']['license_no'] ?? '';
                }

                $data['agent']['email'] = $value['agent']['secondary_email']!="" ? $value['agent']['secondary_email']: $value['agent']['email'] ?? '';
                $data['agent']['phone'] = str_replace("+971", "0", $value['agent']['mobile']) ?? '';
            }
            $data['developer'] = $value['details']['developer'] ?? null;
            $data['parking'] = $value['parking'] ?? 0;

            if ($value['furnished'] == 'FURNISHED') {
                $data['furnished'] = 'Yes';
            } else {
                if ($value['furnished'] == 'SEMI_FURNISHED') {
                    $data['furnished'] = 'Partly';
                } else {
                    $data['furnished'] = 'No';
                }
            }
            $data['photo'] = [];
            if ($value['photos']) {
                $url = [];
                foreach ($value['photos'] as $key => $photo) {
                    $dd['@attributes'] = ['last_updated' => date('Y-m-d H:i:s', strtotime($photo['updated_at'])), 'watermark' => 'yes'];
                    $dd['@value'] = $photo['path'];
                    $url['url'][] = $dd;
                }
                $data['photo'] = $url;
            }




            $prop['property'][] = $data;
            if (!empty($value["community_property"])) {
                $data['geopoints'] = $value["community_property"]["latitude"] . ", " . $value["community_property"]["longitude"];
            } elseif (!empty($value["sub_community"])) {
                $data['geopoints'] = $value["sub_community"]["latitude"] . ", " . $value["sub_community"]["longitude"];
            } else {
                $data['geopoints'] = ''; //$value['latitude'] . ',' . $value['longitude'];
            }
        }

        $last_update = $last_update['updated_at'];
        $this->listing_count = $listing_count;
        $pfObj['last_update'] = date('Y-m-d H:i:s', strtotime($last_update));
        $pfObj['listing_count'] = $listing_count;

        $xml = $this->createXML($pfObj, 'list', $prop);
        $xxx = $xml->saveXML();

        return \Response::make($xxx, '200')->header('Content-Type', 'text/xml');
        }catch(\Exception $e){
            
        }
    }
    public function genericPortalUpdated()
    {
        try{
        $mainPortal = Portal::where("slug", "generic-portal")->latest()->first();
        if (is_null($mainPortal)) {
            abort(404);
        }
        //  abort_if($mainPortal->xml_url != $request->key, 404);

        $last_update = Property::whereHas("portals", function ($q) use ($mainPortal) {
            $q->where("portal_id", $mainPortal->id);
        })->orderBy('updated_at', 'desc')
        ->select('updated_at')->first()->toArray();

        $listing_count = Property::whereHas("portals", function ($q) use ($mainPortal) {
            $q->where("portal_id", $mainPortal->id);
        })->where(['publish_status' => 'PUBLISHED'])->count();

        $prop = [];

        $pp = Property::whereHas("portals", function ($q) use ($mainPortal) {
                $q->where("portal_id", $mainPortal->id);
            })
            ->where(['publish_status' => 'PUBLISHED'])
            ->with([
                'agent' => function($q){
                    $q->select('id','name','first_name','last_name','pp_enabled','pp_name','pp_photo', 'pp_license_no', 'email','secondary_email','license_no','mobile','bio');
                },
                'details',
                'propertyCategory' => function ($q) {
                    $q->select('id');
                }, 
                'propertyCategory.portals'=> function($q) use ($mainPortal){
                    $q->where('portal_id', $mainPortal->id)->select('id', 'title','property_category_id', 'portal_id');
                },
                'bed' => function ($q) {
                    $q->select('id');
                },
                'bed.portals' => function($q) use ($mainPortal){
                    $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bed_id', 'portal_id');
                },'developer', 
                'price'=> function ($q) {
                    $q->select('id','property_id','price','prices','price_on_application');
                },
                'bath' => function ($q) {
                    $q->select('id');
                }, 
                'bath.portals' => function($q) use ($mainPortal){
                    $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bath_id', 'portal_id');
                }, 
                'amenities' => function ($q) use ($mainPortal) {
                        $q->whereHas('amenity.portals', function($q) use ($mainPortal) {
                              $q->where('portal_id', '=', $mainPortal->id);
                          });
                    },
                    'amenities.amenity.portals' => function ($q) use ($mainPortal) {
                        $q->where('portal_id', '=', $mainPortal->id)
                          ->where('title', '!=', '');
                    },
                'community.region', 'subCommunity', 'communityProperty',
                'photos' => function($q){
                    $q->select('id','property_id','path','title','updated_at');
                }, 
                'floorPlan'])
            ->orderBy('properties.created_at', 'desc')
            ->get();

        foreach ($pp as $key => $value) {
            $data['@attributes'] = ['last_update' => date('Y-m-d H:i:s', strtotime($value['updated_at']))];
            $data['permit_number'] = $value['rera_permit_number'] ?? '';
            $data['reference_number'] = $value['ref_no'];
            $data['price'] = [];
            if ($value['for'] == "Residential for sale" || $value['for'] == "Commercial for sale") {
                $data['price'] = $value['price']["price"] ?? 0;
                $data['offering_type'] = $value['for'] == "Commercial for sale" ? 'CS' : 'RS';
            } else {
                $data['price']['yearly'] = $value['price']["price"] ?? 0;
                $data['offering_type'] = $value['for'] == "Commercial for rent" ? 'CR' : 'RR';
            }
            $data['property_type'] = $value['propertyCategory']['portals'][0]["title"] ?? "";
            $data['price_on_application'] = 'No';

            $data['city']['@cdata'] = $value['community']['region']['name'] ?? 'Dubai';
            $data['community']['@cdata'] = $value['community']['name'] ?? '';
            $data['sub_community']['@cdata'] = $value['subCommunity']['name'] ?? '';
            $data['property_name']['@cdata'] = $value['communityProperty']['name'] ?? '';



            $data['title_en']['@cdata'] = $value['title'] ?? '';
            //$data['description_en']['@cdata'] = $value['description'] ?? '';

            $description = strip_tags($value['description'], '<br>');
            $data['description_en']['@cdata'] = str_replace("<br>", "\r\n", $description) ?? '';

            $data['completion_status'] = $value['completion_status'] ?? "";

            $features = [];

            foreach ($value["amenities"] ?? [] as $amenity) {
                $amenityType = $amenity["amenity"]["amenity_type"] ?? [];
                $features[] = $amenity["amenity"]["portals"][0]["title"];
            }

            $data['amenities'] = implode(',', $features);
            $data['size'] = $value['built_up_area'] ?? '';
            $data['bedroom'] = $value["bed"]["portals"][0]["name"] ?? 0;
            $data['bathroom'] = $value["bath"]["portals"] [0]["name"] ?? 0;

            //$data['view'] = $value['view'] ?? '';


            if ($value['agent']) {
                if($value['agent']['pp_enabled']){
                    $data['agent']['name']['@cdata'] = $value['agent']['pp_name'];
                    $data['agent']['photo'] = $value['agent']['pp_photo'] ?? '';
                    $data['agent']['license_no'] = $value['agent']['pp_license_no'] ?? '';
                }else{
                    $data['agent']['name']['@cdata'] = $value['agent']['first_name'] . ' ' . $value['agent']['last_name'];
                    $data['agent']['photo'] = $value['agent']['thumb_url'] ?? '';
                    $data['agent']['license_no'] = $value['agent']['license_no'] ?? '';
                }

                $data['agent']['email'] = $value['agent']['secondary_email']!="" ? $value['agent']['secondary_email']: $value['agent']['email'] ?? '';
                $data['agent']['phone'] = str_replace("+971", "0", $value['agent']['mobile']) ?? '';
            }
            $data['developer'] = $value['details']['developer'] ?? null;
            $data['parking'] = $value['parking'] ?? 0;

            if ($value['furnished'] == 'FURNISHED') {
                $data['furnished'] = 'Yes';
            } else {
                if ($value['furnished'] == 'SEMI_FURNISHED') {
                    $data['furnished'] = 'Partly';
                } else {
                    $data['furnished'] = 'No';
                }
            }
            $data['photo'] = [];
            if ($value['photos']) {
                $url = [];
                foreach ($value['photos'] as $key => $photo) {
                    $dd['@attributes'] = ['last_updated' => date('Y-m-d H:i:s', strtotime($photo['updated_at'])), 'watermark' => 'yes'];
                    $dd['@value'] = $photo['path'];
                    $url['url'][] = $dd;
                }
                $data['photo'] = $url;
            }




            $prop['property'][] = $data;
            if (!empty($value["communityProperty"])) {
                $data['geopoints'] = $value["communityProperty"]["latitude"] . ", " . $value["communityProperty"]["longitude"];
            } elseif (!empty($value["subCommunity"])) {
                $data['geopoints'] = $value["subCommunity"]["latitude"] . ", " . $value["subCommunity"]["longitude"];
            } else {
                $data['geopoints'] = ''; //$value['latitude'] . ',' . $value['longitude'];
            }
        }

        $last_update = $last_update['updated_at'];
        $this->listing_count = $listing_count;
        $pfObj['last_update'] = date('Y-m-d H:i:s', strtotime($last_update));
        $pfObj['listing_count'] = $listing_count;

        $xml = $this->createXML($pfObj, 'list', $prop);
        $xxx = $xml->saveXML();

        return \Response::make($xxx, '200')->header('Content-Type', 'text/xml');
        }catch(\Exception $e){
            
        }
    }

    /**
     * @var string
     */
    private static $encoding = 'UTF-8';

    /**
     * Convert an Array to XML.
     *
     * @param  string  $node_name  - name of the root node to be converted
     * @param  array  $arr  - array to be converted
     * @param  array  $docType  - optional docType
     * @param  array  $pfObj  - array to be converted
     *
     * @return DomDocument
     * @throws Exception
     */
    public static function createXML($pfObj = [], $node_name, $arr = [], $docType = [])
    {
        $xml = self::getXMLRoot();

        // BUG 008 - Support <!DOCTYPE>
        if ($docType) {
            $xml->appendChild((new DOMImplementation())
                ->createDocumentType($docType['name'] ?? '', $docType['publicId'] ?? '', $docType['systemId'] ?? ''));
        }

        $xml->appendChild(self::convert($node_name, $arr, $pfObj));
        self::$xml = null; // clear the xml node in the class for 2nd time use.
        return $xml;
    }

    /**
     * Initialize the root XML node [optional].
     *
     * @param  string  $version
     * @param  string  $encoding
     * @param  bool  $standalone
     * @param  bool  $format_output
     */
    public static function init($version = '1.0', $encoding = 'utf-8', $standalone = false, $format_output = true)
    {
        self::$xml = new DomDocument($version, $encoding);
        self::$xml->xmlStandalone = $standalone;
        self::$xml->formatOutput = $format_output;
        self::$encoding = $encoding;
    }

    /**
     * Get string representation of boolean value.
     *
     * @param  mixed  $v
     *
     * @return string
     */
    private static function bool2str($v)
    {
        //convert boolean to text value.
        $v = $v === true ? 'true' : $v;
        $v = $v === false ? 'false' : $v;

        return $v;
    }

    /**
     * Convert an Array to XML.
     *
     * @param  string  $node_name  - name of the root node to be converted
     * @param  array  $arr  - array to be converted
     *
     * @return DOMNode
     *
     * @throws Exception
     */
    private static function convert($node_name, $arr = [], $pfObj = [])
    {
        //dd($node_name,$pfObj);
        $xml = self::getXMLRoot();
        $node = $xml->createElement($node_name);
        if ($node_name == 'list') {
            $node->setAttribute('last_update', $pfObj['last_update'] ?? '');
            $node->setAttribute('listing_count', $pfObj['listing_count'] ?? '');
        }

        if (is_array($arr)) {
            // get the attributes first.;
            if (array_key_exists('@attributes', $arr) && is_array($arr['@attributes'])) {
                foreach ($arr['@attributes'] as $key => $value) {
                    if (!self::isValidTagName($key)) {
                        throw new Exception('[Array2XML] Illegal character in attribute name. attribute: '.$key.' in node: '.$node_name);
                    }
                    $node->setAttribute($key, self::bool2str($value));
                }
                unset($arr['@attributes']); //remove the key from the array once done.

            }

            // check if it has a value stored in @value, if yes store the value and return
            // else check if its directly stored as string
            if (array_key_exists('@value', $arr)) {
                $node->appendChild($xml->createTextNode(self::bool2str($arr['@value'])));
                unset($arr['@value']); //remove the key from the array once done.
                //return from recursion, as a note with value cannot have child nodes.
                return $node;
            } elseif (array_key_exists('@cdata', $arr)) {
                $node->appendChild($xml->createCDATASection(self::bool2str($arr['@cdata'])));
                unset($arr['@cdata']); //remove the key from the array once done.
                //return from recursion, as a note with cdata cannot have child nodes.
                return $node;
            }
        }

        //create subnodes using recursion
        if (is_array($arr)) {
            // recurse to get the node for that key
            foreach ($arr as $key => $value) {
                if (!self::isValidTagName($key)) {
                    throw new Exception('[Array2XML] Illegal character in tag name. tag: '.$key.' in node: '.$node_name);
                }
                if (is_array($value) && is_numeric(key($value))) {
                    // MORE THAN ONE NODE OF ITS KIND;
                    // if the new array is numeric index, means it is array of nodes of the same kind
                    // it should follow the parent key name
                    foreach ($value as $k => $v) {
                        $node->appendChild(self::convert($key, $v));
                    }
                } else {
                    // ONLY ONE NODE OF ITS KIND
                    $node->appendChild(self::convert($key, $value));
                }
                unset($arr[$key]); //remove the key from the array once done.

            }
        }

        // after we are done with all the keys in the array (if it is one)
        // we check if it has any text value, if yes, append it.
        if (!is_array($arr)) {
            $node->appendChild($xml->createTextNode(self::bool2str($arr)));
        }

        return $node;
    }

    /**
     * Get the root XML node, if there isn't one, create it.
     *
     * @return DomDocument|null
     */
    private static function getXMLRoot()
    {
        if (empty(self::$xml)) {
            self::init();
        }

        return self::$xml;
    }

    /**
     * Check if the tag name or attribute name contains illegal characters
     * Ref: http://www.w3.org/TR/xml/#sec-common-syn.
     *
     * @param  string  $tag
     *
     * @return bool
     */
    private static function isValidTagName($tag)
    {
        $pattern = '/^[a-z_]+[a-z0-9\:\-\.\_]*[^:]*$/i';

        return preg_match($pattern, $tag, $matches) && $matches[0] == $tag;
    }

}
