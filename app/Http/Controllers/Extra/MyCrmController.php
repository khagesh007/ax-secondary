<?php

namespace App\Http\Controllers\Extra;

use App\Jobs\MyCrmFetchArchived;
use App\Models\Bath;
use App\Models\Bed;
use App\Models\Location;
use App\Models\Portal;
use App\Models\PropertyPortal;
use App\Models\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use App\Models\Community;
use App\Models\CommunityProperty;
use App\Models\Contact;
use App\Models\Property;
use App\Models\PropertyAmenity;
use App\Models\PropertyCategory;
use App\Models\PropertyDetail;
use App\Models\PropertyFeatures;
use App\Models\PropertyMedia;
use App\Models\PropertyPrice;
use App\Models\PropertyStatus;
use App\Models\Region;
use App\Models\SubCommunity;
use App\Models\User;
use App\Models\Country;
use App\Models\State;
use App\Models\Lead;
use App\Models\LeadCriteria;
use App\Models\LeadNote;
use Spatie\Permission\Models\Role;

use App\Traits\FileTrait;
use Intervention\Image\Facades\Image;

;

class MyCrmController extends Controller
{
    use FileTrait;

    public function __construct()
    {
        //Bearer 150852b2b677aac3bc03d41f1586d69913b22486
        $this->token = '150852b2b677aac3bc03d41f1586d69913b22486';
    }

    public function archiveSearch()
    {
        return view('admin.import.archiveMycrm');
    }

    public function syncProperty()
    {
        $header = array();
        $header[] = 'Content-Type: application/json';
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->get('https://api-v2.mycrm.com/properties?per_page=1&sort=id&sort_order=DESC');
        $data = $response->json();
        // echo '<pre>';
        // echo print_r($data['properties']);
        // echo '</pre>';
        foreach ($data['properties'] as $value) {
            $property_status = PropertyStatus::where('title', ucfirst($value['status']))->first();
            $property = Property::where('my_crm_id', $value['id'])->first() ?? new Property();
            $property->my_crm_id = $value['id'];
            $property->ref_no = $value['reference'];
            $property->title = $value['reference'];
            $property->property_status_id = $property_status->id;
            $property->floor = $value['floor_number'];
            $property->unit_no = $value['unit_number'];
            $property->street = $value['street_name'];
            $property->furnished = ucfirst($value['furnished']);
            $property->dewa_number = $value['dewa_number'];
            $property->parking = $value['parking'];
            $property->developer = $value['developer'];
            // $property->dewa_number = $value['dewa_number'];
            $property->save();

            // Image::make('https://s3-ap-southeast-1.amazonaws.com/mycrm-pro-accounts-v2/property/medium/2226/sTSOlyiYhHfrndNp.jpeg')->save(public_path('images/saveAsImageName.jpg'));
        }
    }

    public function syncSingleProperty()
    {
        $mycrmid = Property::whereNotNull('my_crm_id')->where('data_version', 1)->limit('5')->pluck('my_crm_id');
        foreach ($mycrmid as $my_crm_id) {


            info("Cron Job running at " . now());
            //  $mycrmid = Property::whereNotNull('my_crm_id')->where('data_version', 1)->limit('1')->pluck('my_crm_id');
            $header = array();
            $header[] = 'Content-Type: application/json';
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . '150852b2b677aac3bc03d41f1586d69913b22486',
            ])->get('https://api-v2.mycrm.com/properties/' . $my_crm_id);
            $res = $response->json();
            $value = $res['property'];

            if (!empty($value)) {
                if (!empty($value['type'])) {
                    $property_category = PropertyCategory::where('title', ucfirst($value['type']['name']))->pluck('id');
                } else {
                    $property_category = [];
                }
                if (!empty($value['landlord'])) {
                    $owner = Contact::where('my_crm_id', $value['landlord']['id'])->pluck('id');
                } else {
                    $owner = [];
                }

                if (!empty($value['location'])) {
                    $region = Region::where('name', trim($value['location']['city']))->first() ?? null;
                    if (empty($region)) {
                        $region = new Region();
                        $region->name = trim($value['location']['city']);
                        $region->save();
                    }
                    $community = Community::where('name', trim($value['location']['community']))->first() ?? null;
                    if (empty($community)) {
                        $community = new Community();
                        $community->region_id = $region->id;
                        $community->name = trim($value['location']['community']);
                        $community->save();
                    }
                    $sub_community = SubCommunity::where('name', trim($value['location']['sub_community']))->first() ?? null;
                    if (empty($sub_community)) {
                        $sub_community = new SubCommunity();
                        $sub_community->region_id = $region->id;
                        $sub_community->name = trim($value['location']['sub_community']);
                        $sub_community->community_id = $community->id;
                        $sub_community->save();
                    }
                } else {
                    $community = null;
                    $sub_community = null;
                }
                $building = CommunityProperty::where('community_id', $community->id ?? null)->where('sub_community_id', $sub_community->id ?? null)->where('name', $value['location']['tower'] ?? null)->first();
                if (empty($building)) {
                    if (!empty($value['location']['tower'])) {
                        $building = new CommunityProperty();
                        $building->name = $value['location']['tower'] ?? null;
                        $building->community_id = $community->id ?? null;
                        $building->sub_community_id = $sub_community->id ?? null;
                        $building->save();
                    }
                }
                $property_status = PropertyStatus::where('title', ucfirst($value['status']))->first();
                $property = Property::where('my_crm_id', $my_crm_id)->first();
                //$property->my_crm_id    = $value['id'];
                $property->ref_no = $value['reference'];
                $property->owner_id = $owner[0] ?? null;
                $property->community_id = $community->id ?? null;
                $property->sub_community_id = $sub_community->id ?? null;
                $property->community_property_id = $building->id ?? null;
                $property->property_status_id = $property_status->id;
                $property->property_category_id = $property_category[0] ?? null;
                $property->floor = $value['floor_number'];
                $property->unit_no = $value['unit_number'];
                $property->built_up_area = $value['size'];
                $property->street = $value['street_name'];
                $property->furnished = ucfirst($value['furnished']);
                $property->dewa_number = $value['dewa_number'];
                $property->parking = $value['parking'];
                $property->next_available = $value['available_from'];
                //$property->publish_status = strtoupper($value['state']);  //some status is coming as approved what to do with that
                $property->video_360_link = $value['view_360'];
                $property->completion_status = $value['project_status'] == 'off_plan' ? 'Off plan' : 'Completed';
                // $property->for            = !empty($value['price'])  ? 'SALE' : 'RENT';
                $property->title_deed_text = $value['title_deed'];
                $property->data_version = '2';
                $property->save();

                $propertyDetails = PropertyDetail::where('property_id', $property->id)->first() ?? new PropertyDetail();
                $propertyDetails->property_id = $property->id;
                $propertyDetails->developer = $value['developer'];
                $propertyDetails->charges = $value['charges'];
                $propertyDetails->build_year = $value['build_year'];
                $propertyDetails->plot_size = $value['plot_size'];
                $propertyDetails->plot_number = $value['plot_number'];
                $propertyDetails->floors = $value['floors'];
                $propertyDetails->occupancy = $value['occupancy'];
                $propertyDetails->renovation = $value['renovation'];
                $propertyDetails->freehold = $value['freehold'];
                $propertyDetails->save();


                $pricing = PropertyPrice::where('property_id', $property->property_id)->first() ?? new PropertyPrice();
                $pricing->property_id = $property->id;
                $pricing->price = $value['price']['value'] ?? null;
                $pricing->save();
                // foreach ($value['images'] as  $val) {
                //     $orignal_image = $val['original']['link'] ?? null;


                //     $image = $this->uploadImageAndThumbWithWatermarkApi(file: $orignal_image, path: "listings");
                //     if (!empty($image)) {
                //         $media = PropertyMedia::create([
                //             "property_id" =>  $property->id,
                //             "path" => $image["image"] ?? null,
                //             "thumbnail" => $image["thumbnail"] ?? null,
                //             "order" => PropertyMedia::count() + 1,
                //             "type" => "Photos",
                //         ]);
                //     }
                // }
                foreach ($value['amenities'] as $value) {
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

    public function syncUsers()
    {
        $page = request('page') ?? 1;
        $header = array();
        $header[] = 'Content-Type: application/json';
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->get('https://api-v2.mycrm.com/users?page=' . $page . '&per_page=50');
        $res = $response->json();
        echo '<pre>';
        print_r($res);
        echo '</pre>';
        foreach ($res['users'] as $value) {
            $user = User::where('old_user_id', $value['id'])->orWhere('email', $value['private']['email'])->first() ?? new User();

            if (isset($value['public']['nationalities'][0])) {
                $nationality_id = Country::where('code', $value['public']['nationalities'][0])->first()->id;
            } else {
                $nationality_id = NULL;
            }


            if (isset($value['public']['descriptions'][0])) {
                $descriptions = $value['public']['descriptions'][0]['content'];
            } else {
                $descriptions = NULL;
            }

            //dd($value['public']['descriptions'][0]['content']);
            $user->old_user_id = $value['id'];
            $user->name = $user->name ?? $value['private']['first_name'];
            $user->first_name = $user->first_name ?? $value['private']['first_name'];
            $user->last_name = $user->last_name ?? $value['private']['last_name'];
            $user->email = $user->email ?? $value['private']['email'];
            $user->mobile = $user->mobile ?? $value['private']['mobile'];
            $user->position = $user->position ?? $value['public']['position'];
            $user->image = $value['public']['photo']['full']['link'] ?? null;
            $user->thumbnail = $value['public']['photo']['full']['link'] ?? null;
            $user->bio = $user->bio ?? $descriptions;
            $user->license_no = $user->license_no ?? $value['public']['licence'];
            $user->linkedin_link = $user->linkedin_link ?? $value['public']['linked_in'];
            $user->nationality_id = $nationality_id ?? NULL;
            $user->password = \Hash::make(rand(100000000, 999999999));
            $user->save();

            $agentRole = Role::where("name", "Agent")->first();

            $user->assignRole($agentRole);
        }
    }

    public function syncLeads()
    {
        $page = request('page') ?? 1;
        $header[] = 'Content-Type: application/json';
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->get('https://api-v2.mycrm.com/leads?page=' . $page . '&per_page=100&sort=id&sort_order=DESC');
        $data = $response->json();
        $i = 0;
        foreach ($data['leads'] as $lead) {

            $agent_id = User::where('old_user_id', $lead['user']['id'])->first();

            $country = Country::where('name', $lead['country'])->first();
            if ($lead['status'] == 'notYetContacted') {
                $sub_status = '4';
            }

            if ($lead['source'] == 'propertyfinder') {
                $source = '31';
            } elseif ($lead['source'] == 'online') {
                $source = '45';
            } elseif ($lead['source'] == 'other') {
                $source = '44';
            }
            $obj = Lead::where('my_crm_id', $lead['id'])->first();
            if (empty($obj)) {
                $obj = Lead::where('email', $lead['email'])->orWhere('mobile')->first();
            }
            if (empty($obj)) {
                $i++;
            }
            $obj = empty($obj) ? new Lead() : $obj;
            $obj->my_crm_id = $lead['id'];
            $obj->first_name = $lead['first_name'];
            $obj->last_name = $lead['last_name'];
            $obj->email = $lead['email'];
            $obj->mobile = $lead['mobile'];
            $obj->phone = $lead['phone'];
            $obj->address = $lead['address'] ?? null;
            $obj->country_id = $country->id ?? null;
            // $obj->lead_sub_status_id = $sub_status ?? null;
            $obj->source_id = $source ?? null;
            $obj->agent_id = $agent_id->id ?? null;
            $obj->save();

            if (!empty($lead['preferences'])) {

                foreach ($lead['preferences'] as $pref) {

                    $lead_criteria = new LeadCriteria();
                    $lead_criteria->lead_id = $obj->id;
                    $lead_criteria->min_price = $pref['from_price'];
                    $lead_criteria->max_price = $pref['to_price'];
                    $lead_criteria->min_bed_id = ($pref['from_bedroom'] < 23 && $pref['from_bedroom'] > 0) ? $pref['from_bedroom'] + 1 : null;
                    $lead_criteria->max_bed_id = ($pref['to_bedroom'] < 23 && $pref['to_bedroom'] > 0) ? $pref['to_bedroom'] + 1 : null;
                    $lead_criteria->min_bath_id = ($pref['from_bathroom'] < 20 && $pref['from_bathroom'] > 0) ? $pref['from_bathroom'] : null;
                    $lead_criteria->max_bath_id = ($pref['to_bathroom'] < 20 && $pref['to_bathroom'] > 0) ? $pref['to_bathroom'] : null;
                    $lead_criteria->save();
                    $locations = [];
                    if (!empty($pref['locations'])) {
                        foreach ($pref['locations'] as $location) {
                            $locations[] = $location['community'];
                        }
                        $lead_criteria->communities = $locations;
                        $lead_criteria->save();
                    }


                    if (!empty($pref['types'])) {
                        if ($pref['offering_type'] == 'sale') {

                            if ($pref['types'][0]['category'] == 'residential') {
                                $for = "Residential for sale";
                            } elseif ('commercial') {
                                $for = "Commercial for sale";
                            } else {
                                $for = 'Unspecified';
                            }

                        } else if ($pref['offering_type'] == 'rent') {
                            if ($pref['types'][0]['category'] == 'residential') {
                                $for = "Residential for rent";
                            } elseif ('commercial') {
                                $for = "Commercial for rent";
                            } else {
                                $for = 'Unspecified';
                            }
                        } else {
                            $for = 'Unspecified';
                        }
                    }

                    $obj->for = $for;
                    $obj->save();
                }


            }
            // $note = new LeadNote();
            // $note->lead_id = $obj->id;
            // $note->note = json_encode($lead);
            // $note->save();


        }
        echo $i . ' leads imported successfully';
    }

    public function getArchived()
    {
        $header = array();
        $header[] = 'Content-Type: application/json';
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->get('https://api-v2.mycrm.com/properties?per_page=1&page=1&fields=id,reference,price,user,status,location,created_at,updated_at,import,state,type,marked_as_seen,publication,bedrooms,bathrooms,size,languages,images,furnished,landlord,available_from,charges,parking,amenities,unit_number,developer,metadata,hash,quality_score,licenses,freehold,build_year,plot_size,built_up_area,floors,occupancy,renovation,project_status,financial_status,transaction,listing_level,verifications,duplicated_list,furnished,listing_status,listing_expiry_date,rega_permit,listing_auto_renew&filters[state]=archived&sort=updated&sort_order=desc&_=1678856220087');
        $data = $response->json();
        $total = $data['count'];
        $perpage = 30;
        $n = $total / $perpage;
        $total_page = floor($n);      // 1
        $fraction = $n - $total_page;
        if ($fraction > 0) {
            $total_page = $total_page + 1;
        }
        $pages = 10;

        for ($page = 1; $page <= $pages; $page++) {
            MyCrmFetchArchived::dispatch($page, $perpage, $this->token);
        }

    }

    public function viewSingleProperty(Request $request)
    {
        try {
            $my_crm_id = (int)preg_replace('/[^0-9]+/', '', $request->keyword);
            if(Property::where('my_crm_id',$my_crm_id)->exists()){
                return response()->json(['status' => 0, 'data' => [], 'msg' => 'This property already exists in this system']);
            }
            $header = array();
            $header[] = 'Content-Type: application/json';
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
            ])->get('https://api-v2.mycrm.com/properties/' . $my_crm_id);
            $res = $response->json();

            $value = $res['property'] ?? null;

            if (!empty($value)) {
                return response()->json(['status' => 1, 'data' => $value, 'msg' => 'Property found']);
            } else {
                return response()->json(['status' => 0, 'data' => [], 'msg' => 'Property not found']);
            }
        }catch (\Exception $e){
            info("Error in viewSingleProperty ".$e->getMessage());
            return response()->json(['status' => 0, 'data' => [], 'msg' => 'Something went wrong', 'error'=>$e->getMessage()]);
        }
    }
    public function importArchive_OLD(Request $request){

        $my_crm_id = $request->id;
//        if(Property::where('my_crm_id',$my_crm_id)->exists()){
//            return response()->json(['status' => 0, 'data' => [], 'msg' => 'This property already exists in this system']);
//        }
        try {

            $header = array();
            $header[] = 'Content-Type: application/json';
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
            ])->get('https://api-v2.mycrm.com/properties/' . $my_crm_id);
            $res = $response->json();
            $value = $res['property'];

            if (!empty($value)) {

                if (!empty($value['type'])) {
                    $property_category = PropertyCategory::where('title', ucfirst($value['type']['name']))->pluck('id');
                } else {
                    $property_category = [];
                }
                if (!empty($value['landlord'])) {
                    $owner = Contact::where('my_crm_id', $value['landlord']['id'])->pluck('id');
                } else {
                    $owner = [];
                }

                if (!empty($value['location'])) {
                    $region = Region::where('name', trim($value['location']['city']))->first() ?? null;
                    if (empty($region)) {
                        $region = new Region();
                        $region->name = trim($value['location']['city']);
                        $region->save();
                    }
                    $community = Community::where('name', trim($value['location']['community']))->first() ?? null;
                    if (empty($community)) {
                        $community = new Community();
                        $community->region_id = $region->id;
                        $community->name = trim($value['location']['community']);
                        $community->save();
                    }
                    $sub_community = SubCommunity::where('name', trim($value['location']['sub_community']))->first() ?? null;
                    if (empty($sub_community)) {
                        $sub_community = new SubCommunity();
                        $sub_community->region_id = $region->id;
                        $sub_community->name = trim($value['location']['sub_community']);
                        $sub_community->community_id = $community->id;
                        $sub_community->save();
                    }
                } else {
                    $community = null;
                    $sub_community = null;
                }
                $building = CommunityProperty::where('community_id', $community->id ?? null)->where('sub_community_id', $sub_community->id ?? null)->where('name', $value['location']['tower'] ?? null)->first();
                if (empty($building)) {
                    if (!empty($value['location']['tower'])) {
                        $building = new CommunityProperty();
                        $building->name = $value['location']['tower'] ?? null;
                        $building->community_id = $community->id ?? null;
                        $building->sub_community_id = $sub_community->id ?? null;
                        $building->save();
                    }
                }
                $bedroom = Bed::where('id',$value['bedrooms']+1)->first();
                $bathroom = Bath::where('id',$value['bathrooms'])->first();
                $property_status = PropertyStatus::where('title', ucfirst($value['status']))->first();
                $for = '';
                if ($value['price']['offering_type'] == 'sale' && $value['type']['category'] == 'residential') {
                    $for = 'Residential for sale';
                } elseif ($value['price']['offering_type'] == 'sale' && $value['type']['category'] == 'commercial') {
                    $for = 'Commercial for sale';
                } elseif ($value['price']['offering_type'] == 'rent' && $value['type']['category'] == 'commercial') {
                    $for = 'Commercial for rent';
                } elseif ($value['price']['offering_type'] == 'rent' && $value['type']['category'] == 'residential') {
                    $for = 'Residential for rent';
                }
                if(!empty($value['user'])){
                    $agent = User::where('old_user_id',$value['user']['id'])->first();
                }else{
                    $agent = null;
                }

                $property =  Property::where('ref_no',$value['reference'])->first() ?? new Property();
                $property->my_crm_id    = $value['id'];
                $property->ref_no = $value['reference'];
                $property->agent_id= $agent->id ?? null;
                $property->owner_id = $owner[0] ?? null;
                $property->for = $for ?? null;
                $property->community_id = $community->id ?? null;
                $property->sub_community_id = $sub_community->id ?? null;
                $property->community_property_id = $building->id ?? null;
                $property->property_status_id = $property_status->id;
                $property->property_category_id = $property_category[0] ?? null;
                $property->bed_id = $bedroom->id ?? null;
                $property->bath_id = $bathroom->id ?? null;
                $property->floor = $value['floor_number'];
                $property->plot_size = $value['plot_size'];
                $property->unit_no = $value['unit_number'];
                $property->built_up_area = $value['size'];
                $property->street = $value['street_name'];
                $property->furnished = ucfirst($value['furnished']);
                $property->dewa_number = $value['dewa_number'];
                $property->parking = $value['parking'];
                $property->next_available = $value['available_from'];
                $property->publish_status = 'ARCHIVED';
                $property->video_360_link = $value['view_360'];
                $property->completion_status = $value['project_status'] == 'off_plan' ? 'Off plan' : 'Completed';
                // $property->for            = !empty($value['price'])  ? 'SALE' : 'RENT';
                $property->title_deed_text = $value['title_deed'];
                $property->data_version = '3';
                $property->is_archived = '1';
                $property->withoutApproval()->save();

                $arr = ['attributes' => $property->getDirty()];
                activity()
                    ->performedOn($property)
                    ->causedBy(auth()->user()->id)
                    ->withProperties($arr)
                    ->event('created')
                    ->log(auth()?->user()?->name . ' has imported archive listing ' . $property->ref_no);
                if($value['selected_license'] == "rera_listing"){
                    if(!empty($value['licenses'])){
                        if(isset($value['licenses']['rera_listing'])){
                            $property->rera_permit_number = $value['licenses']['rera_listing']['number'];
                            $property->rera_issue_date = $value['licenses']['rera_listing']['issue_date'];
                            $property->rera_expiry_date = $value['licenses']['rera_listing']['expiry_date'];
                            $property->save();
                        }
                    }
                }
                if(!empty($value['languages'])){
                    foreach ($value['languages'] as $lang){
                        if($lang['lang'] == 'en') {
                            $property->title = $lang['title'];
                            $property->description = $lang['description'];
                            $property->save();
                        }
                    }
                }

                $propertyDetails = PropertyDetail::where('property_id', $property->id)->first() ?? new PropertyDetail();
                $propertyDetails->property_id = $property->id;
                $propertyDetails->developer = $value['developer'];
                $propertyDetails->charges = $value['charges'];
                $propertyDetails->build_year = $value['build_year'];
                $propertyDetails->plot_size = $value['plot_size'];
                $propertyDetails->plot_number = $value['plot_number'];
                $propertyDetails->floors = $value['floors'];
                $propertyDetails->occupancy = $value['occupancy'];
                $propertyDetails->renovation = $value['renovation'];
                $propertyDetails->freehold = $value['freehold'];
                $propertyDetails->save();
            if($value['price']['offering_type'] == 'rent') {
                if ($value['price']) {
                    $price = $value['price']['prices'][0]['value'] ?? null;
                }
            }else{
                $price = $value['price']['value'] ?? null;
            }
                $pricing = PropertyPrice::where('property_id', $property->property_id)->first() ?? new PropertyPrice();
                $pricing->property_id = $property->id;
                $pricing->price = $price;
                $pricing->save();
                 foreach ($value['images'] as  $val) {
                     $orignal_image = $val['original']['link'] ?? null;


                     $image = $this->uploadImageAndThumbWithWatermarkApi(file: $orignal_image, path: "listings");
                     if (!empty($image)) {
                         $media = PropertyMedia::create([
                             "property_id" =>  $property->id,
                             "path" => $image["image"] ?? null,
                             "thumbnail" => $image["thumbnail"] ?? null,
                             "order" => PropertyMedia::count() + 1,
                             "type" => "Photos",
                         ]);
                     }
                 }
                foreach ($value['amenities'] as $value) {
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
            return response()->json(['status' => 1, 'msg' => 'Successfully imported']);
        }catch (\Exception $e){
            info("Error in importing property ".$e->getMessage());

            return response()->json(['status' => 0, 'msg' => 'Unable to import property. please contact support','error'=>$e->getMessage()]);
        }


    }
    public function importArchive(Request $request)
    {
        try{
        $my_crm_id = $request->id;
        if(Property::where('my_crm_id',$my_crm_id)->exists()){
            return response()->json(['status' => 0, 'data' => [], 'msg' => 'This property already exists in this system']);
        }
        $portalPublications = array("bayut" => "BAYUT", "dubizzle" => "Dubizzle", "genericportal" => "Generic Portal", "privatesite" => "Private Site", "propertyfinder" => "Property Finder", "rightmove" => "Right Move");
        $publishStatus = array('draft' => 'DRAFT', 'pending' => 'PENDING', 'approved' => 'PUBLISHED', 'refused' => 'REFUSED', 'archived' => 'ARCHIVED','pocket' => 'POCKET');

        $offeringType = array('sale' => array('residential' => 'Residential for sale', 'commercial' => 'Commercial for sale'), 'rent' => array('residential' => 'Residential for rent', 'commercial' => 'Commercial for rent'));

        $header = array();
        $header[] = 'Content-Type: application/json';
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->get('https://api-v2.mycrm.com/properties/' . $my_crm_id);
        $res = $response->json();
        $value = $res['property'];

        $property = new Property;

        if (!empty($value)) {
            if (!empty($value['type'])) {
                $property_category = PropertyCategory::where('title', ucfirst($value['type']['name']))->pluck('id');
            } else {
                $property_category = [];
            }
            if (!empty($value['landlord'])) {
                $owner = Contact::where('my_crm_id', $value['landlord']['id'])->pluck('id');
            } else {
                $owner = [];
            }
            if (!empty($value['user'])) {
                $agent = User::where('old_user_id', $value['user']['id'])->pluck('id');
            } else {
                $agent = [];
            }
            if (!empty($value['location'])) {
                $region = Region::where('name', trim($value['location']['city']))->first() ?? null;
                if (empty($region)) {
                    $region = new Region();
                    $region->name = trim($value['location']['city']);
                    $region->save();
                }
                $community = Community::where('name', trim($value['location']['community']))->first() ?? null;
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
                    $community = new Community();
                    $community->region_id = $region->id;
                    $community->name = trim($value['location']['community']);
                    $community->path_name = trim($value['location']['community']) . " $region->name";
                    $community->slug = $slug;

                    $community->save();
                }
                $sub_community = SubCommunity::where('name', trim($value['location']['sub_community']))->first() ?? null;
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
                    $sub_community->region_id = $region->id;
                    $sub_community->name = trim($value['location']['sub_community']);
                    $sub_community->path_name = trim($value['location']['sub_community']) . ", " . $community->path_name . ", " . $region->name;
                    $sub_community->slug = $slug;
                    $sub_community->community_id = $community->id;
                    $sub_community->save();
                }

            } else {
                $community = null;
                $sub_community = null;
            }
            $building = CommunityProperty::where('community_id', $community->id ?? null)->where('sub_community_id', $sub_community->id ?? null)->where('name', $value['location']['tower'] ?? null)->first();
            if (empty($building)) {
                if (!empty($value['location']['tower'])) {
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
                    $building = new CommunityProperty();
                    $building->name = $value['location']['tower'] ?? null;
                    $building->path_name = $value['location']['tower'] . ", " . $sub_community->name . ", " . $community->name . ", " . $region->name;
                    $building->slug = $slug;
                    $building->community_id = $community->id ?? null;
                    $building->region_id = $region->id;
                    $building->sub_community_id = $sub_community->id ?? null;
                    $building->save();
                }
            }
            $property_status = PropertyStatus::where('title', ucfirst($value['status']))->first();

            if (empty($property_status)) {
                if (!empty($value['status'])) {
                    $property_status = new PropertyStatus();
                    $property_status->title = ucfirst($value['status']) ?? null;
                    $property_status->status = 1;
                    $property_status->save();
                }
            }


            $property->my_crm_id = $value['id'];
            $property->title = ($value['languages']) ? $value['languages'][0]['title'] : null;
            $property->description = null;
            if ($value['languages'] && $value['languages'][0]['description']) {
                $content = nl2br($value['languages'][0]['description']);
                $property->description = $content;
            }

            $property->ref_no = $value['reference'];
            $property->owner_id = $owner[0] ?? null;
            $property->agent_id = $agent[0] ?? null;
            $property->community_id = $community->id ?? null;
            $property->sub_community_id = $sub_community->id ?? null;
            $property->community_property_id = $building?->id ?? null;
            $property->property_status_id = $property_status?->id;
            $property->publish_status = $publishStatus[$value['state']] ?? "DRAFT";
            $property->mycrm_updated = 1;
            $property->property_category_id = $property_category[0] ?? null;
            $property->floor = $value['floor_number'];
            $property->unit_no = $value['unit_number'];
            if (isset($value['licenses']['rera_listing'])) {
                $property->rera_permit_number = $value['licenses']['rera_listing']['number'] ?? null;
                $property->rera_issue_date = $value['licenses']['rera_listing']['issue_date'] ?? null;
                $property->rera_expiry_date = $value['licenses']['rera_listing']['expiry_date'] ?? null;
            }
            $property->built_up_area = $value['size'];
            $property->street = $value['street_name'];
            $property->furnished = strtoupper($value['furnished']);
            $property->dewa_number = $value['dewa_number'];
            $property->parking = $value['parking'];
            $property->next_available = $value['available_from'];
            $property->bath_id = (isset($value['bathrooms']) && $value['bathrooms'] != 0) ? $value['bathrooms'] : null;
            $property->bed_id = (isset($value['bedrooms']) && $value['bedrooms'] != 0) ? $value['bedrooms'] + 1 : 1;
            $property->video_360_link = $value['view_360'];
            $property->completion_status = $value['project_status'] == 'off_plan' ? 'Off plan' : 'Completed';

            $rentOrSale = $value['price']['offering_type'];
            $typeOfProperty = $value['type']['category'];
            $property->for = $offeringType[$rentOrSale][$typeOfProperty];
            $property->title_deed_text = $value['title_deed'];
            $property->data_version = '2';
            $property->withoutApproval()->save();
            $arr = ['attributes' => $property->getDirty()];
            activity()
                ->performedOn($property)
                ->causedBy(auth()->user()->id)
                ->withProperties($arr)
                ->event('created')
                ->log(auth()?->user()?->name . ' has imported archive listing ' . $property->ref_no);
            //$property->portals()->delete();
            $portals = [];

            $enabledPropertyPortals = collect($value['publication'], [])->where("enabled", true);

            foreach ($enabledPropertyPortals as $key => $enabledPropertyPortal) {
                $portal = Portal::where('name', $portalPublications[$key])->first();
                if (isset($portal->id)) {
                    $portals [] = array('property_id' => $property->id, 'portal_id' => $portal->id);
                }

            }

            PropertyPortal::insert($portals);
            $propertyDetails = PropertyDetail::where('property_id', $property->id)->first() ?? new PropertyDetail();
            $propertyDetails->property_id = $property->id;
            $propertyDetails->developer = $value['developer'];
            $propertyDetails->charges = $value['charges'];
            $propertyDetails->build_year = $value['build_year'];
            $propertyDetails->plot_size = $value['plot_size'];
            $propertyDetails->plot_number = $value['plot_number'];
            $propertyDetails->floors = $value['floors'];
            $propertyDetails->occupancy = $value['occupancy'];
            $propertyDetails->renovation = $value['renovation'];
            $propertyDetails->freehold = $value['freehold'];
            $propertyDetails->save();


            $pricing = new PropertyPrice();

            $pricing->property_id = $property->id;
            if ($value['price']['offering_type'] == 'sale') {
                $pricing->price = $value['price']['value'] ?? null;
            } else {
                if ($value['price']['prices']) {
                    $prc = collect($value['price']['prices'])->where("period", "year")->first();
                    if ($prc) {
                        $pricing->price = $prc['value'] ?? null;
                        $pricing->prices = $prc['value'] . "/" . $prc['period'];
                    } else {
                        $pricing->price = $value['price']['prices'][0]['value'] ?? null;
                        $pricing->prices = $value['price']['prices'][0]['value'] . "/" . $value['price']['prices'][0]['period'];
                    }
                }

            }

            $pricing->save();


            // Direct images saving in db without downloading and uploading to our s3
            $image_counter = 1;
            foreach ($value['images'] as $val) {
                $orignal_image = $val['original']['link'] ?? null;
                $thumbnail = $val['thumb']['link'] ?? null;

                if ($orignal_image) {
                    $media = PropertyMedia::create([
                        "property_id" => $property->id,
                        "path" => $orignal_image ?? null,
                        "thumbnail" => $thumbnail ?? null,
                        "order" => $val['order'] ?? $image_counter,
                        "type" => "Photos",
                        "temp_image" => 1,
                    ]);
                }
                $image_counter++;
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

            foreach ($value['amenities'] as $value) {
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
            return response()->json(['status' => 1, 'msg' => 'Successfully imported']);
        }catch (\Exception $e){
            info("error in property import ".$e->getMessage());
            return response()->json(['status' => 0, 'msg' => 'Unable to import property. please contact support','error'=>$e->getMessage()]);
        }
    }

    public function propertySaveImage()
    { //this will fetch selected property image and save it to the storage/public/{property id}
        $properties = ["3835833", "3812862", "3744994", "3810090", "3819767", "3820542"];
        foreach ($properties as $prop) {
            $header = array();
            $header[] = 'Content-Type: application/json';
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . '150852b2b677aac3bc03d41f1586d69913b22486',
            ])->get('https://api-v2.mycrm.com/properties/' . $prop);
            $res = $response->json();
            $value = $res['property'];
            foreach ($value['images'] as $val) {
                $orignal_image = $val['original']['link'] ?? null;

                // $filename = time().'.'.$orignal_image->getClientOriginalExtension();
                //  $orignal_image->move(public_path('images'), $filename);
                $contents = file_get_contents($orignal_image);
                $filename = rand(1000, 9999) . time() . '.png';
                \Storage::disk('public')->put($prop . '/' . $filename, $contents);

            }
        }

    }
}
