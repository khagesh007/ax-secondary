<?php

namespace App\Console\Commands;

use App\Models\Community;
use App\Models\CommunityProperty;
use App\Models\Contact;
use App\Models\Property;
use App\Models\User;
use App\Models\Portal;
use App\Models\PropertyAmenity;
use App\Models\PropertyPortal;
use App\Models\PropertyCategory;
use App\Models\PropertyDetail;
use App\Models\PropertyFeatures;
use App\Models\PropertyMedia;
use App\Models\PropertyPrice;
use App\Models\PropertyStatus;
use App\Models\Region;
use App\Models\SubCommunity;
use Illuminate\Support\Facades\Http;
use Illuminate\Console\Command;
use App\Traits\FileTrait;
class MyCrmPropertyUpdate extends Command
{
    use FileTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mycrm:property';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will update the property via api and will change data_version to 2';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        
        $time_start = microtime(true);
        $page = 1;
        $totalCount = 1;
        $per_page = 20;
        info("Importing Properties from mycrm started at " . now());
        $portalPublications = array("bayut"=> "BAYUT", "dubizzle"=> "Dubizzle", "genericportal"=> "Generic Portal", "privatesite"=> "Private Site", "propertyfinder"=> "Property Finder", "rightmove" => "Right Move");
        $publishStatus = array('draft' => 'DRAFT', 'pending'=> 'PENDING', 'approved' => 'PUBLISHED', 'refused' => 'REFUSED', 'archived' => 'ARCHIVED');

        $offeringType = array('sale' => array('residential' => 'Residential for sale', 'commercial'=> 'Commercial for sale'), 'rent'=> array('residential' => 'Residential for rent', 'commercial'=> 'Commercial for rent'));
        
        // $mycrmid = Property::whereNotNull('my_crm_id')->where('my_crm_id', 1919865)
        //             ->limit('10')->pluck('my_crm_id');

        $header  = array();
        $header[] = 'Content-Type: application/json';
        $header[] = 'Content-Length: 4000';
        $header[] = 'Connection: keep-alive';
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . '150852b2b677aac3bc03d41f1586d69913b22486',
        ])->get('https://api-v2.mycrm.com/properties?page=1&per_page=1');
        $res = $response->json();
        $properties = $res['properties'];
        $total_properties_count = $res['count'];
        $total_pages = round(ceil($total_properties_count / $per_page), 0);
        for($page; $page <= $total_pages; $page++){
                $response = Http::withHeaders(['Authorization' => 'Bearer ' . '150852b2b677aac3bc03d41f1586d69913b22486',])->get('https://api-v2.mycrm.com/properties?page='.$page.'&per_page='.$per_page);
                $res = $response->json();
                $properties = $res['properties'];
                if($properties){
                    info("Page number is $page");
                    foreach($properties as $value){
                        echo "\n#";
                        
                        $property               = Property::where('my_crm_id', $value["id"])->first();
                        $newProperty = false;
                        if(!$property){
                            $property = new Property;
                            $newProperty = true;
                            echo("\nid of the property adding is ". $value['id']);
                            info("\nid of the property adding is ". $value['id']);
                        }else{
                            echo("\nid of the property updating is ". $value['id']);
                            info("\nid of the property updating is ". $value['id']);
                        }
                        if (!empty($value)) {
                            if (!empty($value['type'])) {
                                $property_category        = PropertyCategory::where('title', ucfirst($value['type']['name']))->pluck('id');
                            } else {
                                $property_category        = [];
                            }
                            if (!empty($value['landlord'])) {
                                $owner        = Contact::where('my_crm_id', $value['landlord']['id'])->pluck('id');
                            } else {
                                $owner        = [];
                            }
                            if (!empty($value['user'])) {
                                $agent        = User::where('old_user_id', $value['user']['id'])->pluck('id');
                            } else {
                                $agent        = [];
                            }
                            if (!empty($value['location'])) {
                                $region = Region::where('name', trim($value['location']['city']))->first() ?? null;
                                if (empty($region)) {
                                    $region          = new Region();
                                    $region->name    = trim($value['location']['city']);
                                    $region->save();
                                }
                                $community      = Community::where('name', trim($value['location']['community']))->first() ?? null;
                                if (empty($community)) {
                                    $slug = $value['location']['community'];

                                    $slug = preg_replace('~[^\pL\d]+~u', "-", $slug);

                                    // transliterate
                                    $slug = iconv('utf-8', 'us-ascii//TRANSLIT', $slug);

                                    // remove unwanted characters
                                    $slug = preg_replace('~[^-\w]+~', '', $slug);

                                    // trim
                                    $slug = trim($slug, "-");

                                    // remove duplicate divider
                                    $slug = preg_replace('~-+~', "-", $slug);

                                    // lowercase
                                    $slug = strtolower($slug);

                                    if (empty($slug)) {
                                        $slug = 'n-a';
                                    }
                                    $community                  = new Community();
                                    $community->region_id       = $region->id;
                                    $community->name            = trim($value['location']['community']);
                                    $community->path_name       = trim($value['location']['community'])." $region->name";
                                    $community->slug            = $slug;
                                    
                                    $community->save();
                                }
                                $sub_community  = SubCommunity::where('name', trim($value['location']['sub_community']))->first() ?? null;
                                if (empty($sub_community)) {
                                    $slug = $value['location']['sub_community'];

                                    $slug = preg_replace('~[^\pL\d]+~u', "-", $slug);

                                    // transliterate
                                    $slug = iconv('utf-8', 'us-ascii//TRANSLIT', $slug);

                                    // remove unwanted characters
                                    $slug = preg_replace('~[^-\w]+~', '', $slug);

                                    // trim
                                    $slug = trim($slug, "-");

                                    // remove duplicate divider
                                    $slug = preg_replace('~-+~', "-", $slug);

                                    // lowercase
                                    $slug = strtolower($slug);

                                    if (empty($slug)) {
                                        $slug = 'n-a';
                                    }
                                    $sub_community = new SubCommunity();
                                    $sub_community->region_id     = $region->id;
                                    $sub_community->name = trim($value['location']['sub_community']);
                                    $sub_community->path_name = trim($value['location']['sub_community']).", ".$community->path_name.", ".$region->name;
                                    $sub_community->slug = $slug;
                                    $sub_community->community_id = $community->id;
                                    $sub_community->save();
                                }
                                
                            } else {
                                $community = null;
                                $sub_community = null;
                            }
                            $building = CommunityProperty::where('community_id', $community->id ?? null)->where('sub_community_id', $sub_community->id ?? null)->where('name', $value['location']['tower'] ?? null)->first();
                            if(empty($building)){
                                if(!empty($value['location']['tower'])){
                                    $slug = $value['location']['tower'];

                                    $slug = preg_replace('~[^\pL\d]+~u', "-", $slug);

                                    // transliterate
                                    $slug = iconv('utf-8', 'us-ascii//TRANSLIT', $slug);

                                    // remove unwanted characters
                                    $slug = preg_replace('~[^-\w]+~', '', $slug);

                                    // trim
                                    $slug = trim($slug, "-");

                                    // remove duplicate divider
                                    $slug = preg_replace('~-+~', "-", $slug);

                                    // lowercase
                                    $slug = strtolower($slug);

                                    if (empty($slug)) {
                                        $slug = 'n-a';
                                    }
                                    $building                   = new CommunityProperty();
                                    $building->name             = $value['location']['tower'] ?? null;
                                    $building->path_name        = $value['location']['tower'].", ".$sub_community->name.", ".$community->name.", ".$region->name;
                                    $building->slug             = $slug;
                                    $building->community_id     = $community->id ?? null;
                                    $building->region_id        = $region->id;
                                    $building->sub_community_id = $sub_community->id ?? null;
                                    $building->save();
                                }
                            }
                            $property_status        = PropertyStatus::where('title', ucfirst($value['status']))->first();

                            if( empty($property_status) ){
                                if(!empty($value['status'])){
                                    $property_status            = new PropertyStatus();
                                    $property_status->title     = ucfirst($value['status']) ?? null;
                                    $property_status->status    = 1;
                                    $property_status->save(); 
                                }
                            }

                            
                            $property->my_crm_id    = $value['id'];
                            $property->title        = ($value['languages'])?$value['languages'][0]['title'] : null;
                            $property->description  = null;
                            if($value['languages'] && $value['languages'][0]['description']){
                                $content = nl2br($value['languages'][0]['description']);
                                $property->description  = $content;
                            }

                            $property->ref_no       = $value['reference'];
                            $property->owner_id                     = $owner[0] ?? null;
                            $property->agent_id                     = $agent[0] ?? null;
                            $property->community_id                 = $community->id ?? null;
                            $property->sub_community_id             = $sub_community->id ?? null;
                            $property->community_property_id        = $building?->id ?? null;
                            $property->property_status_id           = $property_status?->id;
                            $property->publish_status               = $publishStatus[$value['state']] ?? "DRAFT";
                            $property->mycrm_updated                = 1;
                            $property->property_category_id         = $property_category[0] ?? null;
                            $property->floor          = $value['floor_number'];
                            $property->unit_no        = $value['unit_number'];
                            if(isset($value['licenses']['rera_listing'])){
                                $property->rera_permit_number     = $value['licenses']['rera_listing']['number'] ?? null;
                                $property->rera_issue_date     = $value['licenses']['rera_listing']['issue_date'] ?? null;
                                $property->rera_expiry_date     = $value['licenses']['rera_listing']['expiry_date'] ?? null;
                            }
                            $property->built_up_area  = $value['size'];
                            $property->street         = $value['street_name'];
                            $property->furnished      = strtoupper($value['furnished']);
                            $property->dewa_number    = $value['dewa_number'];
                            $property->parking        = $value['parking'];
                            $property->next_available = $value['available_from'];
                            $property->bath_id        = (isset($value['bathrooms']) && $value['bathrooms']!= 0)? $value['bathrooms'] :null;
                            $property->bed_id         = (isset($value['bedrooms']) && $value['bedrooms'] != 0) ? $value['bedrooms']+1 : 1;
                            $property->video_360_link       = $value['view_360'];
                            $property->completion_status    = $value['project_status'] == 'off_plan' ? 'Off plan' : 'Completed';

                            $rentOrSale = $value['price']['offering_type'];
                            $typeOfProperty = $value['type']['category'];
                            $property->for        = $offeringType[$rentOrSale][$typeOfProperty];
                            $property->title_deed_text      = $value['title_deed'];
                            $property->data_version         = '2';
                            $property->save();
                            $property->portals()->delete();
                            $portals = [];

                            $enabledPropertyPortals = collect($value['publication'], [])->where("enabled", true);
            
                            foreach($enabledPropertyPortals as $key => $enabledPropertyPortal){
                                $portal = Portal::where('name', $portalPublications[$key])->first();
                                if(isset($portal->id)){
                                    $portals [] = array('property_id' => $property->id, 'portal_id' => $portal->id);
                                }
                                
                            }

                            PropertyPortal::insert($portals);
                            $propertyDetails                = PropertyDetail::where('property_id', $property->id)->first() ?? new PropertyDetail();
                            $propertyDetails->property_id   = $property->id;
                            $propertyDetails->developer     = $value['developer'];
                            $propertyDetails->charges       = $value['charges'];
                            $propertyDetails->build_year    = $value['build_year'];
                            $propertyDetails->plot_size     = $value['plot_size'];
                            $propertyDetails->plot_number   = $value['plot_number'];
                            $propertyDetails->floors        = $value['floors'];
                            $propertyDetails->occupancy     = $value['occupancy'];
                            $propertyDetails->renovation    = $value['renovation'];
                            $propertyDetails->freehold      = $value['freehold'];
                            $propertyDetails->save();


                            $pricing                  = PropertyPrice::where('property_id', $property->id)->first() ?? new PropertyPrice();

                            $pricing->property_id     = $property->id;
                            if($value['price']['offering_type']== 'sale'){
                               $pricing->price           = $value['price']['value'] ?? null; 
                            }
                            else{
                                if($value['price']['prices']){
                                    $prc = collect($value['price']['prices'])->where("period","year")->first();
                                    if($prc){
                                        $pricing->price           = $prc['value'] ?? null; 
                                        $pricing->prices          = $prc['value']."/".$prc['period'];  
                                    }
                                    else{
                                        $pricing->price            = $value['price']['prices'][0]['value'] ?? null; 
                                        $pricing->prices           = $value['price']['prices'][0]['value']."/".$value['price']['prices'][0]['period'];
                                    }
                                }
                               
                            }
                            
                            $pricing->save();

                            $totalCount++;
                            // Deleting the old images if there are any
                            if($newProperty){
                                $property->photos()->delete();
                                // Direct images saving in db without downloading and uploading to our s3
                                $image_counter = 1;
                                foreach ($value['images'] as  $val) {
                                    $orignal_image = $val['original']['link'] ?? null;
                                    $thumbnail = $val['thumb']['link'] ?? null;

                                    if ($orignal_image) {
                                        $media = PropertyMedia::create([
                                            "property_id" =>  $property->id,
                                            "path" => $orignal_image ?? null,
                                            "thumbnail" => $thumbnail ?? null,
                                            "order" => $val['order']?? $image_counter,
                                            "type" => "Photos",
                                            "temp_image" => 1,
                                        ]);
                                    }
                                   $image_counter++; 
                                }
                            }
                            

                            // code for direct images ends here


                            // Images Store on S3 Uncomment this

                            /*foreach ($value['images'] as  $val) {
                                $orignal_image = $val['original']['link'] ?? null;
                                $image = $this->uploadImageAndThumbWithWatermarkApi(file: $orignal_image, path: "listings");
                                if (!empty($image)) {
                                    $media = PropertyMedia::create([
                                        "property_id" =>  $property->id,
                                        "path" => $image["image"] ?? null,
                                        "thumbnail" => $image["thumbnail"] ?? null,
                                        "order" => PropertyMedia::count() + 1,
                                        "type" => "Photos",
                                        "temp_image" => 0,
                                    ]);
                                }
                            }*/
                            // Images storing on s3 ends here

                            foreach ($value['amenities'] as  $value) {
                                $amenity = PropertyAmenity::where('title', $value['name'])->first();
                                if (empty($amenity)) {
                                    $amenity = new PropertyAmenity();
                                    $amenity->title = $value['name'];
                                    $amenity->type = 'Amenities';
                                    $amenity->status = 1;
                                    $amenity->save();
                                }
                                if (PropertyFeatures::where('property_id', $property->id)->where('property_amenity_id', $amenity->id)->doesntExist()) {
                                    PropertyFeatures::create(
                                        [
                                            'property_id' => $property->id,
                                            'property_amenity_id' => $amenity->id,
                                        ]
                                    );
                                }
                            }
                        }

                    }
            }
        }
        
        $time_end = microtime(true);
        $time = $time_end - $time_start;

        info( "$time seconds is the time it takes to finish this task");
        echo("\n $totalCount properties are imported successfully");
        return Command::SUCCESS;
    }
}
