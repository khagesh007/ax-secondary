<?php

namespace Database\Seeders;

use App\Models\Portal;
use App\Models\PropertyCategory;
use App\Models\PropertyCategoryPortal;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PropertyCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $property_categories = array(
            array('name' => "Apartment", 'slug' => 'apartment', 'pf_code' => 'AP', 'db_code' => 'AP', 'bayut_code' => 'Apartment'),
            array('name' => "Villa", 'slug' => 'villa', 'pf_code' => 'VH', 'db_code' => 'VH', 'bayut_code' => 'Villa'),
            array('name' => "Townhouse", 'slug' => 'townhouse', 'pf_code' => 'TH', 'db_code' => 'VH', 'bayut_code' => 'Townhouse'),
            array('name' => "Penthouse", 'slug' => 'penthouse', 'pf_code' => 'PH', 'db_code' => '', 'bayut_code' => ' Pent House'),
            array('name' => "Compound", 'slug' => 'compound', 'pf_code' => 'CD', 'db_code' => 'AP', 'bayut_code' => 'Compound'),
            array('name' => "Duplex", 'slug' => 'duplex', 'pf_code' => 'DX', 'db_code' => 'AP', 'bayut_code' => 'Duplex'),
            array('name' => "Bungalow", 'slug' => 'bungalow', 'pf_code' => 'BW', 'db_code' => 'VH', 'bayut_code' => 'Bungalow'),
            array('name' => "Full Floor", 'slug' => 'full-floor', 'pf_code' => 'FF', 'db_code' => '', 'bayut_code' => 'Residential Floor'),
            array('name' => "Office", 'slug' => 'office', 'pf_code' => 'OF', 'db_code' => 'CO', 'bayut_code' => 'Office'),
            array('name' => "Retail", 'slug' => 'retail', 'pf_code' => 'RE', 'db_code' => 'CO', 'bayut_code' => 'Shop'),
            array('name' => "Hotel Apartment", 'slug' => 'hotel-apartment', 'pf_code' => 'HA', 'db_code' => '', 'bayut_code' => ' Hotel Apartment'),
            //array('name' => "Warehouse",'slug'=>'warehouse','pf_code'=>'WH','db_code'=>'CO'),
            array('name' => "Land Commercial", 'slug' => 'land-commercial', 'pf_code' => 'LP', 'db_code' => 'LA', 'bayut_code' => 'Commercial Land'),
            //array('name' => "Labour Camp",'slug'=>'labour-camp','pf_code'=>'LC','db_code'=>'CO'),
            //array('name' => "Residential Building",'slug'=>'residential-building','pf_code'=>'WB','db_code'=>''),
            //array('name' => "Multiple Sale Units",'slug'=>'multiple-sale-units','pf_code'=>'BU','db_code'=>''),
            array('name' => "Land Residential", 'slug' => 'land-residential', 'pf_code' => 'LP', 'db_code' => 'LA', 'bayut_code' => 'Residential Land'),
            array('name' => "Mixed Use Land", 'slug' => 'mix-use-land', 'pf_code' => 'LP', 'db_code' => 'LA', 'bayut_code' => 'Mixed Use Land'),
            //array('name' => "Commercial Full Building",'slug'=>'commercial-full-building','pf_code'=>'AP','db_code'=>''),
            //array('name' => "Loft Apartment",'slug'=>'loft-apartment','pf_code'=>'AP','db_code'=>''),
            //array('name' => "Hotel",'slug'=>'hotel','pf_code'=>'HA','db_code'=>''),
            //array('name' => "Half Floor",'slug'=>'half-floor','pf_code'=>'HF','db_code'=>''),
            //array('name' => "Commercial Villa",'slug'=>'commercial-villa','pf_code'=>'','db_code'=>''),
            //array('name' => "Factory",'slug'=>'factory','pf_code'=>'','db_code'=>''),
            //array('name' => "Staff Accommodation",'slug'=>'staff-accommodation','pf_code'=>'SA','db_code'=>''),
            //array('name' => "Multiple Rental Units",'slug'=>'multiple-rental-units','pf_code'=>'FF','db_code'=>''),
            //array('name' => "Residential Full Floor",'slug'=>'residential-full-floor','pf_code'=>'FF','db_code'=>''),
            //array('name' => "Commercial Full Floor",'slug'=>'commercial-full-floor','pf_code'=>'HF','db_code'=>''),
            //array('name' => "Residential Half Floor",'slug'=>'residential-half-floor','pf_code'=>'','db_code'=>''),
            //array('name' => "Commercial Half Floor",'slug'=>'commercial-half-floor','pf_code'=>'','db_code'=>''),
            //array('name' => "Show Room",'slug'=>'showroom','pf_code'=>'SR','db_code'=>''),
            //array('name' => "Shop",'slug'=>'shop','pf_code'=>'SH','db_code'=>'','db_code'=>''),
            //array('name' => "Business Center",'slug'=>'business-center','pf_code'=>'BC','db_code'=>''),
            //array('name' => "Land Industrial",'slug'=>'land-industrial','pf_code'=>'LP','db_code'=>''),
            //array('name' => "Open Yard",'slug'=>'open-yard','pf_code'=>'','db_code'=>''),
            //array('name' => "Land Agricultural",'slug'=>'land-agricultural','pf_code'=>'LP','db_code'=>''),
         );
        // $property_categories = array(
        //     array('title' => "Apartment", 'slug' => 'apartment', 'pf_code' => 'AP', 'dubizzle_code' => 'AP'),
        //     array('title' => "Villa", 'slug' => 'villa', 'pf_code' => 'VH', 'dubizzle_code' => 'VH'),
        //     array('title' => "Townhouse", 'slug' => 'townhouse', 'pf_code' => 'TH', 'dubizzle_code' => 'VH'),
        //     array('title' => "Penthouse", 'slug' => 'penthouse', 'pf_code' => 'PH', 'dubizzle_code' => ''),
        //     array('title' => "Compound", 'slug' => 'compound', 'pf_code' => 'CD', 'dubizzle_code' => 'AP'),
        //     array('title' => "Duplex", 'slug' => 'duplex', 'pf_code' => 'DX', 'dubizzle_code' => 'AP'),
        //     array('title' => "Bungalow", 'slug' => 'bungalow', 'pf_code' => 'BW', 'dubizzle_code' => 'VH'),
        //     array('title' => "Full Floor", 'slug' => 'full-floor', 'pf_code' => 'FF', 'dubizzle_code' => ''),
        //     array('title' => "Office", 'slug' => 'office', 'pf_code' => 'OF', 'dubizzle_code' => 'CO'),
        //     array('title' => "Retail", 'slug' => 'retail', 'pf_code' => 'RE', 'dubizzle_code' => 'CO'),
        //     array('title' => "Hotel Apartment",'slug'=>'hotel-apartment','pf_code'=>'HA', 'dubizzle_code' => ''),
        //     array('title' => "Warehouse",'slug'=>'warehouse','pf_code'=>'WH', 'dubizzle_code' => 'CO'),
        //     array('title' => "Land Commercial",'slug'=>'land-commercial','pf_code'=>'LP','dubizzle_code' => 'LA'),
        //     array('title' => "Labour Camp",'slug'=>'labour-camp','pf_code'=>'LC', 'dubizzle_code' => 'CO'),
        //     array('title' => "Residential Building",'slug'=>'residential-building','pf_code'=>'WB', 'dubizzle_code' => ''),
        //     array('title' => "Multiple Sale Units",'slug'=>'multiple-sale-units','pf_code'=>'BU', 'dubizzle_code' => ''),
        //     array('title' => "Land Residential",'slug' => 'land-residential','pf_code' => 'LP','dubizzle_code' => 'LA'),
        //     array('title' => "Commercial Full Building",'slug'=>'commercial-full-building','pf_code'=>'AP','dubizzle_code' => ''),
        //     array('title' => "Loft Apartment",'slug'=>'loft-apartment','pf_code'=>'AP', 'dubizzle_code' => ''),
        //     array('title' => "Hotel",'slug'=>'hotel','pf_code'=>'HA', 'dubizzle_code' => ''),
        //     array('title' => "Half Floor",'slug'=>'half-floor','pf_code'=>'HF', 'dubizzle_code' => ''),
        //     array('title' => "Commercial Villa",'slug'=>'commercial-villa','pf_code'=>'','dubizzle_code' => ''),
        //     array('title' => "Factory",'slug'=>'factory','pf_code'=>'', 'dubizzle_code' => ''),
        //     array('title' => "Staff Accommodation",'slug'=>'staff-accommodation','pf_code'=>'SA', 'dubizzle_code' => ''),
        //     array('title' => "Multiple Rental Units",'slug'=>'multiple-rental-units','pf_code'=>'FF','dubizzle_code' => ''),
        //     array('title' => "Residential Full Floor",'slug'=>'residential-full-floor','pf_code'=>'FF', 'dubizzle_code' => ''),
        //     array('title' => "Commercial Full Floor",'slug'=>'commercial-full-floor','pf_code'=>'HF', 'dubizzle_code' => ''),
        //     array('title' => "Residential Half Floor",'slug'=>'residential-half-floor','pf_code'=>'', 'dubizzle_code' => ''),
        //     array('title' => "Commercial Half Floor",'slug'=>'commercial-half-floor','pf_code'=>'', 'dubizzle_code' => ''),
        //     array('title' => "Show Room",'slug'=>'showroom','pf_code'=>'SR','dubizzle_code' => ''),
        //     array('title' => "Shop",'slug'=>'shop','pf_code'=>'SH', 'dubizzle_code' => ''),
        //     array('title' => "Business Center",'slug'=>'business-center','pf_code'=>'BC', 'dubizzle_code' => ''),
        //     array('title' => "Land Industrial",'slug'=>'land-industrial','pf_code'=>'LP', 'dubizzle_code' => ''),
        //     array('title' => "Open Yard",'slug'=>'open-yard','pf_code'=>'', 'dubizzle_code' => ''),
        //     array('title' => "Land Agricultural",'slug'=>'land-agricultural','pf_code'=>'LP', 'dubizzle_code' => ''),
        // );
        $portals = Portal::all();
        foreach ($property_categories as $val) {
        $cat =    PropertyCategory::updateOrCreate([
                "title" => $val["name"]
        ],[
                "slug" => $val["slug"]
            ]);

                foreach ($portals as $portal) {
                    $title = '';
                    if($portal->name = 'Property Finder' && $val['pf_code'] != ''){
                        $title = $val['pf_code'];
                    } else if ($portal->name = 'Dubizzle' && $val['db_code'] != '') {
                    $title = $val['db_code'] ?? '';
                } else if ($portal->name = 'Bayut' && $val['bayut_code'] != '') {
                    $title = $val['bayut_code'] ?? '';
                }
                if(!empty($title)){
                   $obj = new PropertyCategoryPortal();
                   $obj->property_category_id = $cat->id;
                   $obj->portal_id = $portal->id;
                   $obj->title = $title;
                   $obj->save();
                }
            }
           
        }
    }
}
