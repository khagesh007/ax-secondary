<?php

namespace Database\Seeders;

use App\Models\Bath;
use App\Models\Bed;
use App\Models\ContactSource;
use App\Models\ContactStatus;
use App\Models\ContactType;
use App\Models\DealSource;
use App\Models\DealStatus;
use App\Models\DealSubStatus;
use App\Models\DealType;
use App\Models\LeadSource;
use App\Models\LeadStatus;
use App\Models\LeadSubStatus;
use App\Models\LeadType;
use App\Models\Portal;
use App\Models\PropertyAmenity;
use App\Models\PropertyAmenityPortal;
use App\Models\PropertyCategory;
use App\Models\PropertyCategoryPortal;
use App\Models\PropertyStatus;
use App\Models\TransactionType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class ParametersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $beds = array(
            array('id' => 1, 'order' => 1,'name' => "Studio", "status" => 1),
            array('id' => 2, 'order' => 2,'name' => "0 beds", "status" => 1),
            array('id' => 3, 'order' => 3,'name' => "1 bed", "status" => 1),
            array('id' => 4, 'order' => 4,'name' => "2 beds", "status" => 1),
            array('id' => 5, 'order' => 5,'name' => "3 beds", "status" => 1),
            array('id' => 6, 'order' => 6,'name' => "4 beds", "status" => 1),
            array('id' => 7, 'order' => 7,'name' => "5 beds", "status" => 1),
            array('id' => 8, 'order' => 8,'name' => "6 beds", "status" => 1),
            array('id' => 9, 'order' => 9,'name' => "7 beds", "status" => 1),
            array('id' => 10,'order' => 10, 'name' => "8 beds", "status" => 1),
            array('id' => 11,'order' => 11, 'name' => "9 beds", "status" => 1),
            array('id' => 12,'order' => 12, 'name' => "10 beds", "status" => 1),
            array('id' => 13,'order' => 13, 'name' => "11 beds", "status" => 1),
            array('id' => 14,'order' => 14, 'name' => "12 beds", "status" => 1),
            array('id' => 15,'order' => 15, 'name' => "13 beds", "status" => 1),
            array('id' => 16,'order' => 16, 'name' => "14 beds", "status" => 1),
            array('id' => 17,'order' => 17, 'name' => "15 beds", "status" => 1),
            array('id' => 18,'order' => 18, 'name' => "16 beds", "status" => 1),
            array('id' => 19,'order' => 19, 'name' => "17 beds", "status" => 1),
            array('id' => 20,'order' => 20, 'name' => "18 beds", "status" => 1),
            array('id' => 21,'order' => 21, 'name' => "19 beds", "status" => 1),
            array('id' => 22,'order' => 22, 'name' => "20 beds", "status" => 1),
            array('id' => 23,'order' => 23, 'name' => "21 beds", "status" => 1),
            array('id' => 24,'order' => 24, 'name' => "22 beds", "status" => 1),
        );

        foreach ($beds as $bed) {
            Bed::updateOrCreate([
                "id" => $bed["id"]
            ], $bed);
        }

        $baths = array(
            array('id' => 1  ,'order'=> 1, 'name' => "0 baths", "status" => 1),
            array('id' => 2,  'order'=> 2, 'name' => "1 bath", "status" => 1),
            array('id' => 3,  'order'=> 3, 'name' => "2 baths", "status" => 1),
            array('id' => 4,  'order'=> 4, 'name' => "3 baths", "status" => 1),
            array('id' => 5,  'order'=> 5, 'name' => "4 baths", "status" => 1),
            array('id' => 6,  'order'=> 6, 'name' => "5 baths", "status" => 1),
            array('id' => 7,  'order'=> 7, 'name' => "6 baths", "status" => 1),
            array('id' => 8,  'order'=> 8, 'name' => "7 baths", "status" => 1),
            array('id' => 9,  'order'=> 9, 'name' => "8 baths", "status" => 1),
            array('id' => 10,'order'=> 10, 'name' => "9 baths", "status" => 1),
            array('id' => 11,'order'=> 11, 'name' => "10 baths", "status" => 1),
            array('id' => 12,'order'=> 12, 'name' => "11 baths", "status" => 1),
            array('id' => 13,'order'=> 13, 'name' => "12 baths", "status" => 1),
            array('id' => 14,'order'=> 14, 'name' => "13 baths", "status" => 1),
            array('id' => 15,'order'=> 15, 'name' => "14 baths", "status" => 1),
            array('id' => 16,'order'=> 16, 'name' => "15 baths", "status" => 1),
            array('id' => 17,'order'=> 17, 'name' => "16 baths", "status" => 1),
            array('id' => 18,'order'=> 18, 'name' => "17 baths", "status" => 1),
            array('id' => 19,'order'=> 19, 'name' => "18 baths", "status" => 1),
            array('id' => 20,'order'=> 20, 'name' => "19 baths", "status" => 1),
            array('id' => 21,'order'=> 21, 'name' => "20 baths", "status" => 1),
        );
        foreach ($baths as $bath) {
            Bath::updateOrCreate([
                "id" => $bath["id"]
            ], $bath);
        }

        $contact_types = array(
            array('id' => 1, 'name' => "Tenant"),
            array('id' => 2, 'name' => "Buyer"),
            array('id' => 3, 'name' => "Landlord"),
            array('id' => 4, 'name' => "Seller"),
            array('id' => 5, 'name' => "Landlord+Seller"),
            array('id' => 6, 'name' => "Representative of Tenant"),
            array('id' => 7, 'name' => "Agent"),
            array('id' => 8, 'name' => "Investor"),
            array('id' => 9, 'name' => "Representative of Landlord"),
            array('id' => 10, 'name' => "Other")
        );

        foreach ($contact_types as $contactType) {
            ContactType::updateOrCreate([
                "id" => $contactType["id"]
            ], $contactType);
        }

        $property_statuses = array(
            array('id' => 1, 'title' => "Available", "status" => 0),
            array('id' => 2, 'title' => "Blocked", "status" => 0),
            array('id' => 3, 'title' => "Moved in", "status" => 0),
            array('id' => 4, 'title' => "Owner Occupied", "status" => 1),
            array('id' => 5, 'title' => "Pending", "status" => 0),
            array('id' => 6, 'title' => "Renewed", "status" => 0),
            array('id' => 7, 'title' => "Rented", "status" => 0),
            array('id' => 8, 'title' => "Reserved", "status" => 0),
            array('id' => 9, 'title' => "Sold", "status" => 0),
            array('id' => 10, 'title' => "Upcoming", "status" => 0),
            array('id' => 11, 'title' => "Tenanted", "status" => 1),
            array('id' => 12, 'title' => "Vacant", "status" => 1),
        );
        foreach ($property_statuses as $propertyStatus) {
            PropertyStatus::updateOrCreate([
                "id" => $propertyStatus["id"]
            ], $propertyStatus);
        }


         $contact_sources = array(
             array('id' => 1, 'name' => "Agent"),
             array('id' => 2, 'name' => "Agent External"),
             array('id' => 3, 'name' => "Agent Internal"),
             array('id' => 4, 'name' => "Business Card"),
             array('id' => 5, 'name' => "Client referral"),
             array('id' => 6, 'name' => "Cold Call"),
             array('id' => 7, 'name' => "Company Email"),
             array('id' => 8, 'name' => "Database"),
             array('id' => 9, 'name' => "Developer"),
             array('id' => 10, 'name' => "Direct Call"),
             array('id' => 11, 'name' => "Direct Client"),
             array('id' => 12, 'name' => "Dubizzle Feature"),
             array('id' => 13, 'name' => "Dubizzle.com"),
             array('id' => 14, 'name' => "Email campaign"),
             array('id' => 15, 'name' => "Event"),
             array('id' => 16, 'name' => "Existing client"),
             array('id' => 17, 'name' => "Facebook"),
             array('id' => 18, 'name' => "Friend or Relative"),
             array('id' => 19, 'name' => "Google"),
             array('id' => 20, 'name' => "Gulf News"),
             array('id' => 21, 'name' => "Instagram"),
             array('id' => 22, 'name' => "JustProperty "),
             array('id' => 23, 'name' => "lookup.ae"),
             array('id' => 24, 'name' => "Magazine"),
             array('id' => 25, 'name' => "Newsletter"),
             array('id' => 26, 'name' => "Newspaper advert"),
             array('id' => 27, 'name' => "Online Campaign"),
             array('id' => 28, 'name' => "Open House"),
             array('id' => 29, 'name' => "Personal Referral"),
             array('id' => 30, 'name' => "PropSpace "),
             array('id' => 31, 'name' => "Referral"),
             array('id' => 32, 'name' => "Roadshow"),
             array('id' => 33, 'name' => "Search Engine"),
             array('id' => 34, 'name' => "Signboard"),
             array('id' => 35, 'name' => "Simon"),
             array('id' => 36, 'name' => "SMS campaign"),
             array('id' => 37, 'name' => "Social media Campaign"),
             array('id' => 38, 'name' => "Twitter"),
             array('id' => 39, 'name' => "Walk-in "),
             array('id' => 40, 'name' => "Website"),
             array('id' => 41, 'name' => "Whatsapp"),
             array('id' => 42, 'name' => "Word of Mouth"),
             array('id' => 43, 'name' => "Youtube"),
             array('id' => 44, 'name' => "Other")
         );

        foreach ($contact_sources as $contactSource) {
            ContactSource::updateOrCreate([
                "id" => $contactSource["id"]
            ], $contactSource);
        }

        $contact_statuses = array(
            array('id' => 1, 'name' => "Open"),
            array('id' => 2, 'name' => "Closed"),
            array('id' => 3, 'name' => "Unspecified")
        );

        foreach ($contact_statuses as $contactStatus) {
            ContactStatus::updateOrCreate([
                "id" => $contactStatus["id"]
            ], $contactStatus);
        }

        $listing_sources = array(
            array('id' => 1, 'name' => "Agent"),
            array('id' => 2, 'name' => "Agent External"),
            array('id' => 3, 'name' => "Agent Internal"),
            array('id' => 4, 'name' => "Bayut"),
            array('id' => 5, 'name' => "Business Card"),
            array('id' => 6, 'name' => "Client referral"),
            array('id' => 7, 'name' => "Cold Call"),
            array('id' => 8, 'name' => "Company Email"),
            array('id' => 9, 'name' => "Database"),
            array('id' => 10, 'name' => "Developer"),
            array('id' => 11, 'name' => "Direct Call"),
            array('id' => 12, 'name' => "Direct Client"),
            array('id' => 13, 'name' => "Dubizzle"),
            array('id' => 14, 'name' => "Email campaign"),
            array('id' => 15, 'name' => "Event"),
            array('id' => 16, 'name' => "Existing client"),
            array('id' => 17, 'name' => "Facebook"),
            array('id' => 18, 'name' => "Friend or Relative"),
            array('id' => 19, 'name' => "Google"),
            array('id' => 20, 'name' => "Gulf News"),
            array('id' => 21, 'name' => "Instagram"),
            array('id' => 22, 'name' => "JustProperty"),
            array('id' => 23, 'name' => "lookup.ae"),
            array('id' => 24, 'name' => "Landing Page"),
            array('id' => 25, 'name' => "Magazine"),
            array('id' => 26, 'name' => "Newsletter"),
            array('id' => 27, 'name' => "Newspaper advert"),
            array('id' => 28, 'name' => "Online Campaign"),
            array('id' => 29, 'name' => "Open House"),
            array('id' => 30, 'name' => "Personal Referral"),
            array('id' => 31, 'name' => "Property Finder "),
            array('id' => 32, 'name' => "Referral"),
            array('id' => 33, 'name' => "Roadshow"),
            array('id' => 34, 'name' => "Search Engine"),
            array('id' => 35, 'name' => "Signboard"),
            array('id' => 36, 'name' => "SMS campaign"),
            array('id' => 37, 'name' => "Social media Campaign"),
            array('id' => 38, 'name' => "Twitter"),
            array('id' => 39, 'name' => "Walk-in "),
            array('id' => 40, 'name' => "Website"),
            array('id' => 41, 'name' => "Whatsapp"),
            array('id' => 42, 'name' => "Word of Mouth"),
            array('id' => 43, 'name' => "Youtube"),
            array('id' => 44, 'name' => "Other"),
            array('id' => 45, 'name' => "Online")
        );

        foreach ($listing_sources as $listing_source){
            LeadSource::updateOrCreate([
                "id" => $listing_source["id"]
            ], $listing_source);

            ContactSource::updateOrCreate([
                "id" => $listing_source["id"]
            ], $listing_source);

            DealSource::updateOrCreate([
                "id" => $listing_source["id"]
            ], $listing_source);
        }

        $lead_statuses = array(
            array('id' => 1, 'name' => "Open"),
            array('id' => 2, 'name' => "Closed"),
            array('id' => 3, 'name' => "Unspecified")
        );

        foreach ($lead_statuses as $leadStatus) {
            LeadStatus::updateOrCreate([
                "id" => $leadStatus["id"]
            ], $leadStatus);
        }

        $lead_sub_statuses = array(
            array('id' => 1, 'lead_status_id'=> 1,'name' => "In progress"),
            array('id' => 2,'lead_status_id' => 2, 'name' => "Successful"),
            array('id' => 3,'lead_status_id' => 2, 'name' => "Unsuccessful"),
            array('id' => 4,'lead_status_id' => 1, 'name' => "Not yet contacted"),
            array('id' => 5,'lead_status_id' => 1, 'name' => "Called no reply"),
            array('id' => 6,'lead_status_id' => 1, 'name' => "Follow up"),
            array('id' => 7,'lead_status_id' => 1, 'name' => "Viewing arranged"),
            array('id' => 8,'lead_status_id' => 1, 'name' => "Unspecified"),
            array('id' => 9,'lead_status_id' => 1, 'name' => "Offer Made"),
            array('id' => 10,'lead_status_id' => 1, 'name' => "Needs more info"),
            array('id' => 11,'lead_status_id' => 1, 'name' => "Budget differs"),
            array('id' => 12, 'lead_status_id'=> 1,'name' => "Needs time"),
            array('id' => 13, 'lead_status_id'=> 1,'name' => "Client to revert"),
            array('id' => 14, 'lead_status_id'=> 1,'name' => "Interested"),
            array('id' => 15, 'lead_status_id'=> 1,'name' => "Interested to Meet"),
            array('id' => 16, 'lead_status_id'=> 1,'name' => "Not Interested"),
            array('id' => 17, 'lead_status_id'=> 1,'name' => "Look-see"),
            array('id' => 18, 'lead_status_id'=> 1,'name' => "SMS sent to Agent"),
            array('id' => 19, 'lead_status_id'=> 1,'name' => "Whatsapp sent to Agent"),
            array('id' => 20, 'lead_status_id'=> 1,'name' => "Email sent to Agent"),
            array('id' => 21, 'lead_status_id'=> 1,'name' => "Client not reachable"),
            array('id' => 22, 'lead_status_id'=> 1,'name' => "Incorrect Contact details"),
            array('id' => 23, 'lead_status_id'=> 1,'name' => "Invalid inquiry"),
            array('id' => 24, 'lead_status_id'=> 1,'name' => "Price too high"),
            array('id' => 25, 'lead_status_id'=> 1,'name' => "Viewing Done"),
            array('id' => 26, 'lead_status_id'=> 1,'name' => "Prospect"),
        );

        foreach ($lead_sub_statuses as $leadSubStatus) {
            LeadSubStatus::updateOrCreate([
                "id" => $leadSubStatus["id"]
            ], $leadSubStatus);
        }

        $lead_types = array(
            array('id' => 1, 'name' => "Tenant"),
            array('id' => 2, 'name' => "Buyer"),
            array('id' => 3, 'name' => "Landlord"),
            array('id' => 4, 'name' => "Seller"),
            array('id' => 5, 'name' => "Landlord+Seller"),
            array('id' => 6, 'name' => "Representative of Tenant"),
            array('id' => 7, 'name' => "Agent"),
            array('id' => 8, 'name' => "Investor"),
            array('id' => 9, 'name' => "Representative of Landlord"),
            array('id' => 10, 'name' => "Other")
        );

        foreach ($lead_types as $leadType) {
            LeadType::updateOrCreate([
                "id" => $leadType["id"]
            ], $leadType);
        }


        $deal_statuses = array(
            array('id' => 1, 'name' => "Open"),
            array('id' => 2, 'name' => "Closed"),
            array('id' => 3, 'name' => "Unspecified")
        );

        foreach ($deal_statuses as $dealStatus) {
            DealStatus::updateOrCreate([
                "id" => $dealStatus["id"]
            ], $dealStatus);
        }

        $deal_sub_statuses = array(
            array('id' => 1, 'deal_status_id' => 2, 'name' => "Successful"),
            array('id' => 2, 'deal_status_id' => 2, 'name' => "Unsuccessful"),
            array('id' => 3, 'deal_status_id' => 2, 'name' => "Dispute"),
            array('id' => 4, 'deal_status_id' => 1, 'name' => "Pending Completion"),
            array('id' => 5, 'deal_status_id' => 1, 'name' => "Pending Signature"),
            array('id' => 6, 'deal_status_id' => 1, 'name' => "Unit Reserved"),
            array('id' => 7, 'deal_status_id' => 1, 'name' => "Pending Approval"),
            array('id' => 8, 'deal_status_id' => null, 'name' => "Unspecified"),
        );

        foreach ($deal_sub_statuses as $subStatus) {
            DealSubStatus::updateOrCreate([
                "id" => $subStatus["id"]
            ], $subStatus);
        }

        $transaction_types = array(
            array('id' => 1, 'name' => "Sale"),
            array('id' => 2, 'name' => "Resale"),
            array('id' => 3, 'name' => "Leased"),
            array('id' => 4, 'name' => "Renew Leased"),
            array('id' => 5, 'name' => "Reserved"),
            array('id' => 6, 'name' => "Renew Reserved"),
            array('id' => 7, 'name' => "Off Plan"),
        );

        foreach ($transaction_types as $transactionType) {
            TransactionType::updateOrCreate([
                "id" => $transactionType["id"]
            ], $transactionType);
        }


        $deal_types = array(
            array('id' => 1, 'name' => "Secondary Sales"),
            array('id' => 2, 'name' => "Off-plan Sales"),
            array('id' => 3, 'name' => "Rental"),
            array('id' => 4, 'name' => "Services"),
        );

        foreach ($deal_types as $dealType) {
            DealType::updateOrCreate([
                "id" => $dealType["id"]
            ], $dealType);
        }


        $property_categories = array(
            array('id' => 1, 'title' => "Apartment", 'slug' => 'apartment', 'pf_code' => 'AP', 'dubizzle_code' => 'AP','bayut_code' => 'Apartment'),
            array('id' => 2, 'title' => "Villa", 'slug' => 'villa', 'pf_code' => 'VH', 'dubizzle_code' => 'VH','bayut_code' => 'Villa'),
            array('id' => 3, 'title' => "Townhouse", 'slug' => 'townhouse', 'pf_code' => 'TH', 'dubizzle_code' => 'VH','bayut_code' => 'Townhouse'),
            array('id' => 4, 'title' => "Penthouse", 'slug' => 'penthouse', 'pf_code' => 'PH', 'dubizzle_code' => '','bayut_code' => 'Pent House'),
            array('id' => 5, 'title' => "Compound", 'slug' => 'compound', 'pf_code' => 'CD', 'dubizzle_code' => 'AP','bayut_code' => ''),
            array('id' => 6, 'title' => "Duplex", 'slug' => 'duplex', 'pf_code' => 'DX', 'dubizzle_code' => 'AP','bayut_code' => 'Duplex'),
            array('id' => 7, 'title' => "Bungalow", 'slug' => 'bungalow', 'pf_code' => 'BW', 'dubizzle_code' => 'VH','bayut_code' => ''),
            array('id' => 8, 'title' => "Full Floor", 'slug' => 'full-floor', 'pf_code' => 'FF', 'dubizzle_code' => '','bayut_code' => ''),
            array('id' => 9, 'title' => "Office", 'slug' => 'office', 'pf_code' => 'OF', 'dubizzle_code' => 'CO','bayut_code' => 'Office'),
            array('id' => 10, 'title' => "Retail", 'slug' => 'retail', 'pf_code' => 'RE', 'dubizzle_code' => 'CO','bayut_code' => ''),
            array('id' => 11, 'title' => "Hotel Apartment",'slug'=>'hotel-apartment','pf_code'=>'HA','dubizzle_code'=>'','bayut_code' => 'Hotel Apartment'),
            array('id' => 12, 'title' => "Warehouse",'slug'=>'warehouse','pf_code'=>'WH','dubizzle_code'=>'CO','bayut_code' => 'Warehouse'),
            array('id' => 13, 'title' => "Land Commercial",'slug'=>'land-commercial','pf_code'=>'LP','dubizzle_code'=>'LA','bayut_code' => 'Commercial Land'),
            array('id' => 14, 'title' => "Labour Camp",'slug'=>'labour-camp','pf_code'=>'LC','dubizzle_code'=>'CO','bayut_code' => 'Labour Camp'),
            array('id' => 15, 'title' => "Residential Building",'slug'=>'residential-building','pf_code'=>'WB','dubizzle_code'=>'','bayut_code' => 'Residential Building'),
            array('id' => 16, 'title' => "Multiple Sale Units",'slug'=>'multiple-sale-units','pf_code'=>'BU','dubizzle_code'=>'','bayut_code' => ''),
            array('id' => 17, 'title' => "Land Residential",'slug' => 'land-residential', 'pf_code' => 'LP', 'dubizzle_code' => 'LA','bayut_code' => 'Residential Land'),
            array('id' => 18, 'title' => "Commercial Full Building",'slug'=>'commercial-full-building','pf_code'=>'AP','dubizzle_code'=>'','bayut_code' => 'Commercial Building'),
            array('id' => 19, 'title' => "Loft Apartment",'slug'=>'loft-apartment','pf_code'=>'AP','dubizzle_code'=>'','bayut_code' => 'Loft Apartment'),
            array('id' => 20, 'title' => "Hotel",'slug'=>'hotel','pf_code'=>'HA','dubizzle_code'=>'','bayut_code' => ''),
            array('id' => 21, 'title' => "Half Floor",'slug'=>'half-floor','pf_code'=>'HF','dubizzle_code'=>'','bayut_code' => ''),
            array('id' => 22, 'title' => "Commercial Villa",'slug'=>'commercial-villa','pf_code'=>'','dubizzle_code'=>'','bayut_code' => ''),
            array('id' => 23, 'title' => "Factory",'slug'=>'factory','pf_code'=>'FA','dubizzle_code'=>'','bayut_code' => 'Factory'),
            array('id' => 24, 'title' => "Staff Accommodation",'slug'=>'staff-accommodation','pf_code'=>'SA','dubizzle_code'=>'','bayut_code' => ''),
            array('id' => 25, 'title' => "Multiple Rental Units",'slug'=>'multiple-rental-units','pf_code'=>'FF','dubizzle_code'=>'','bayut_code' => ''),
            array('id' => 26, 'title' => "Residential Full Floor",'slug'=>'residential-full-floor','pf_code'=>'FF','dubizzle_code'=>'','bayut_code' => 'Residential Floor'),
            array('id' => 27, 'title' => "Commercial Full Floor",'slug'=>'commercial-full-floor','pf_code'=>'HF','dubizzle_code'=>'','bayut_code' => 'Commercial Floor'),
            array('id' => 28, 'title' => "Residential Half Floor",'slug'=>'residential-half-floor','pf_code'=>'','dubizzle_code'=>'','bayut_code' => 'Residential Floor'),
            array('id' => 29, 'title' => "Commercial Half Floor",'slug'=>'commercial-half-floor','pf_code'=>'','dubizzle_code'=>'','bayut_code' => 'Commercial Floor'),
            array('id' => 30, 'title' => "Show Room",'slug'=>'showroom','pf_code'=>'SR','dubizzle_code'=>'','bayut_code' => ''),
            array('id' => 31, 'title' => "Shop",'slug'=>'shop','pf_code'=>'SH','dubizzle_code'=>'','bayut_code' => 'Shop'),
            array('id' => 32, 'title' => "Business Center",'slug'=>'business-center','pf_code'=>'BC','dubizzle_code'=>'','bayut_code' => ''),
            array('id' => 33, 'title' => "Land Industrial",'slug'=>'land-industrial','pf_code'=>'LP','dubizzle_code'=>'','bayut_code' => ''),
            array('id' => 34, 'title' => "Open Yard",'slug'=>'open-yard','pf_code'=>'','dubizzle_code'=>'','bayut_code' => ''),
            array('id' => 35, 'title' => "Land Agricultural",'slug'=>'land-agricultural','pf_code'=>'LP','dubizzle_code'=>'','bayut_code' => ''),
            array('id' => 36, 'title' => "Bulk Units",'slug'=>'bulk-units','pf_code'=>'BU','dubizzle_code'=>'','bayut_code' => ''),
            array('id' => 37, 'title' => "Farm",'slug'=>'farm','pf_code'=>'FM','dubizzle_code'=>'','bayut_code' => ''),
            array('id' => 38, 'title' => "Restaurant",'slug'=>'restaurant','pf_code'=>'RT','dubizzle_code'=>'','bayut_code' => ''),
            array('id' => 39, 'title' => "Co-working Space",'slug'=>'co-working-space','pf_code'=>'CW','dubizzle_code'=>'','bayut_code' => ''),
            array('id' => 40, 'title' => "Storage",'slug'=>'storage','pf_code'=>'ST','dubizzle_code'=>'','bayut_code' => ''),
        );

        $portals = Portal::all();

        foreach ($portals as $portal){
            foreach ($property_categories as $category) {
                $objFeat =   PropertyCategory::updateOrCreate([
                    "id" => $category["id"]
                ],[
                    "title" => $category["title"],
                    "slug" => $category["slug"],
                ]);

                $title = '';
                if ($portal->name == "Property Finder"){
                    $title = $category['pf_code'] ?? "";
                }else if ($portal->name == 'Dubizzle'){
                    $title = $category['dubizzle_code'] ?? "";
                }else if ($portal->name = 'Bayut'){
                    $title = $category['bayut_code'] ?? "";
                }else{
                    $title = "";
                }

                if (!empty($title)) {
                    PropertyCategoryPortal::updateOrCreate([
                        "property_category_id" => $objFeat->id,
                        "portal_id" => $portal["id"],
                    ], [
                        "title" => $title,
                    ]);
                }
            }
        }


        $features = array(
            array('title' => "Balcony", 'type' => 'Features', 'image' => 'balcony', 'pf_type' => 'private', 'pf_code' => 'BA', 'db_code' => 'BA', 'bayut_code' => 'Balcony'),
            array('title' => "Basement parking", 'type' => 'Features', 'image' => 'basement-parking', 'pf_type' => 'private', 'pf_code' => 'CP', 'db_code' => '', 'bayut_code' => 'Basement parking'),
            array('title' => "BBQ area", 'type' => 'Features', 'image' => 'bbq', 'pf_type' => 'private', 'pf_code' => 'BR', 'db_code' => '', 'bayut_code' => 'BBQ area'),
            array('title' => "Broadband ready", 'type' => 'Features', 'image' => 'broadband', 'pf_type' => 'private', 'pf_code' => '', 'db_code' => 'AN', 'bayut_code' => 'Broadband ready'),
            array('title' => "Built in wardrobes", 'type' => 'Features', 'image' => 'wardrobes', 'pf_type' => 'private', 'pf_code' => 'BW', 'db_code' => 'BW', 'bayut_code' => 'Built in wardrobes'),
            array('title' => "Carpets", 'type' => 'Features', 'image' => 'carpets', 'pf_type' => 'private', 'pf_code' => '', 'db_code' => '', 'bayut_code' => 'BA', 'bayut_code' => 'Carpets'),
            array('title' => "Central air conditioning", 'type' => 'Features', 'image' => 'air-conditioning', 'pf_type' => 'private', 'pf_code' => 'AC', 'db_code' => 'AC', 'bayut_code' => 'Central air conditioning'),
            array('title' => "Central heating", 'type' => 'Features', 'image' => 'heating', 'pf_type' => 'private', 'pf_code' => 'AC', 'db_code' => 'BA', 'bayut_code' => 'Central heating'
            ),
            array('title' => "Community View", 'type' => 'Features', 'image' => 'community-view', 'pf_type' => 'private', 'pf_code' => '', 'db_code' => '', 'bayut_code' => 'Community View'),
            array('title' => "Covered parking", 'type' => 'Features', 'image' => 'parking', 'pf_type' => 'commercial', 'pf_code' => 'CP', 'db_code' => 'CP', 'bayut_code' => 'Covered parking'),
            array('title' => "Driver's Room", 'type' => 'Features', 'image' => 'maids-room', 'pf_type' => 'private', 'pf_code' => 'BK', 'db_code' => 'MS', 'bayut_code' => "Driver's Room"),
            array('title' => "Fully fitted kitchen", 'type' => 'Features', 'image' => 'kitchen', 'pf_type' => 'private', 'pf_code' => '', 'db_code' => 'BK', 'bayut_code' => 'Fully fitted kitchen'),
//            array('title' => "Fully furnished", 'type' => 'Features', 'image' => 'furnished', 'pf_type' => 'private', 'pf_code' => '', 'db_code' => 'AF', 'bayut_code' => 'Fully furnished"'),
            array('title' => "Gymnasium", 'type' => 'Features', 'image' => 'gymnasium', 'pf_type' => 'commercial', 'pf_code' => 'SY', 'db_code' => 'SY', 'bayut_code' => 'Gymnasium'),
            array('title' => "Intercom", 'type' => 'Features', 'image' => 'intercom', 'pf_type' => 'private', 'pf_code' => '', 'db_code' => '', 'bayut_code' => 'Intercom'),
            array('title' => "Jacuzzi", 'type' => 'Features', 'image' => 'jacuzzi', 'pf_type' => 'private', 'pf_code' => '', 'db_code' => 'PJ', 'bayut_code' => 'Jacuzzi'),
            array('title' => "Landscaped Garden", 'type' => 'Features', 'image' => 'garden', 'pf_type' => 'private', 'pf_code' => '', 'db_code' => 'BL', 'bayut_code' => 'BA', 'bayut_code' => 'Landscaped Garden'),
            array('title' => "Laundry/washing room", 'type' => 'Features', 'image' => 'laundry', 'pf_type' => 'private', 'pf_code' => '', 'db_code' => '', 'bayut_code' => 'BA', 'bayut_code' => 'Laundry/washing room'
            ),
            array('title' => "Maid's room", 'type' => 'Features', 'image' => 'maids-room', 'pf_type' => 'private', 'pf_code' => 'MR', 'db_code' => 'MR', 'bayut_code' => 'BA', 'bayut_code' => "Maid's room"
            ),
            array('title' => "Marble floors", 'type' => 'Features', 'image' => 'marble', 'pf_type' => 'private', 'pf_code' => '', 'db_code' => '', 'bayut_code' => 'BA', 'bayut_code' => 'Marble floors'),
            array('title' => "On high floor", 'type' => 'Features', 'image' => 'high', 'pf_type' => 'private', 'pf_code' => '', 'db_code' => '', 'bayut_code' => 'BA', 'bayut_code' => 'On high floor'),
            array('title' => "On low floor", 'type' => 'Features', 'image' => 'low', 'pf_type' => 'private', 'pf_code' => '', 'db_code' => '', 'bayut_code' => 'On low floor'),
            array('title' => "On mid floor", 'type' => 'Features', 'image' => 'medium', 'pf_type' => 'private', 'pf_code' => '', 'db_code' => '', 'bayut_code' => 'On mid floor'),
            array('title' => "Part furnished", 'type' => 'Features', 'image' => 'part-furnished', 'pf_type' => 'private', 'pf_code' => '', 'db_code' => 'PA', 'bayut_code' => 'Part furnished"'),
            array('title' => "Pets allowed", 'type' => 'Features', 'image' => 'pets', 'pf_type' => 'private', 'pf_code' => 'PA', 'db_code' => '', 'bayut_code' => 'Pets allowed'),
            array('title' => "Private garage", 'type' => 'Features', 'image' => 'private-garage', 'pf_type' => 'private', 'pf_code' => '', 'db_code' => '', 'bayut_code' => 'Private garage'),
            array('title' => "Private garden", 'type' => 'Features', 'image' => 'private-garden', 'pf_type' => 'private', 'pf_code' => 'PG', 'db_code' => 'PG', 'bayut_code' => 'Private garden'
            ),
            array('title' => "Private swimming pool", 'type' => 'Features', 'image' => 'private-swimming-pool', 'pf_type' => 'private', 'pf_code' => 'PP', 'db_code' => 'PP', 'bayut_code' => 'Private swimming pool'),
            array('title' => "Satellite/Cable TV", 'type' => 'Features', 'image' => 'satellite', 'pf_type' => 'private', 'pf_code' => '', 'db_code' => '', 'bayut_code' => 'Satellite/Cable TV"'),
            array('title' => "Sauna", 'type' => 'Features', 'image' => 'sauna', 'pf_type' => 'private', 'pf_code' => '', 'db_code' => '', 'bayut_code' => 'Sauna'),
            array('title' => "Shared swimming pool", 'type' => 'Features', 'image' => 'shared-swimming-pool', 'pf_type' => 'commercial', 'pf_code' => 'SP', 'db_code' => 'SP', 'bayut_code' => 'Shared swimming pool'),
            array('title' => "Storage room", 'type' => 'Features', 'image' => 'storage-room', 'pf_type' => 'private', 'pf_code' => '', 'db_code' => '', 'bayut_code' => 'Storage room'
            ),
            array('title' => "Study", 'type' => 'Features', 'image' => 'study-room', 'pf_type' => 'private', 'pf_code' => 'ST', 'db_code' => 'ST', 'bayut_code' => 'Study'),
            array('title' => "Upgraded interior", 'type' => 'Features', 'image' => 'interior', 'pf_type' => 'private', 'pf_code' => '', 'db_code' => '', 'bayut_code' => 'Upgraded interior'),
            array('title' => "View of gardens", 'type' => 'Features', 'image' => 'gardens-view', 'pf_type' => 'private', 'pf_code' => 'BL', 'db_code' => '', 'bayut_code' => 'View of gardens'),
            array('title' => "View of golf course", 'type' => 'Features', 'image' => 'golf-course', 'pf_type' => 'private', 'pf_code' => '', 'db_code' => '', 'bayut_code' => 'View of golf course'
            ),
            array('title' => "View of parkland", 'type' => 'Features', 'image' => 'parkland', 'pf_type' => 'private', 'pf_code' => '', 'db_code' => 'VL', 'bayut_code' => 'View of parkland'
            ),
            array('title' => "View of sea/water", 'type' => 'Features', 'image' => 'sea-view', 'pf_type' => 'private', 'pf_code' => 'VW', 'db_code' => 'VW', 'bayut_code' => 'View of sea/water'),
            array('title' => "24 hours Maintenance", 'type' => 'Amenities', 'image' => '24h', 'pf_type' => 'private', 'pf_code' => '', 'db_code' => '', 'bayut_code' => '24 hours Maintenance'),
            array('title' => "Bank/ATM Facility", 'type' => 'Amenities', 'image' => 'atm', 'pf_type' => 'private', 'pf_code' => '', 'db_code' => '', 'bayut_code' => 'Bank/ATM Facility'),
            array('title' => "Basketball Court", 'type' => 'Amenities', 'image' => 'basketball', 'pf_type' => 'private', 'pf_code' => '', 'db_code' => '', 'bayut_code' => 'Basketball Court"'),
            array('title' => "Beach Access", 'type' => 'Amenities', 'image' => 'beach-access', 'pf_type' => 'private', 'pf_code' => '', 'db_code' => '', 'bayut_code' => 'Beach Access"'),
            array('title' => "Bus services", 'type' => 'Amenities', 'image' => 'bus', 'pf_type' => 'private', 'pf_code' => '', 'db_code' => '', 'bayut_code' => 'Bus services'),
            array('title' => "Business Center", 'type' => 'Amenities', 'image' => 'office', 'pf_type' => 'private', 'pf_code' => '', 'db_code' => '', 'bayut_code' => 'Business Center'),
            array('title' => "Children's nursery", 'type' => 'Amenities', 'image' => 'babies', 'pf_type' => 'private', 'pf_code' => '', 'db_code' => '', 'bayut_code' => "Children's nursery"),
            array('title' => "Children's play area", 'type' => 'Amenities', 'image' => 'playarea', 'pf_type' => 'private', 'pf_code' => 'PR', 'db_code' => '', 'bayut_code' => "Children's play area"
            ),
            array('title' => "Clubhouse", 'type' => 'Amenities', 'image' => 'dj', 'pf_type' => 'private', 'pf_code' => '', 'db_code' => '', 'bayut_code' => 'Clubhouse'),
            array('title' => "Communal gardens", 'type' => 'Amenities', 'image' => 'communal-gardens', 'pf_type' => 'private', 'pf_code' => '', 'db_code' => '', 'bayut_code' => 'Communal gardens'),
            array('title' => "Concierge service", 'type' => 'Amenities', 'image' => 'concierge', 'pf_type' => 'private', 'pf_code' => 'CS', 'db_code' => 'CS', 'bayut_code' => 'Concierge service'),
            array('title' => "Cycling tracks", 'type' => 'Amenities', 'image' => 'cycling', 'pf_type' => 'private', 'pf_code' => '', 'db_code' => '', 'bayut_code' => 'Cycling tracks'),
            array('title' => "Fitness Center", 'type' => 'Amenities', 'image' => 'fitness', 'pf_type' => 'private', 'pf_code' => '', 'db_code' => '', 'bayut_code' => 'Fitness Center'),
            array('title' => "Golf club and clubhouse", 'type' => 'Amenities', 'image' => 'golf-club', 'pf_type' => 'private', 'pf_code' => '', 'db_code' => '', 'bayut_code' => 'Golf club and clubhouse'),
            array('title' => "Laundry Service", 'type' => 'Amenities', 'image' => 'laundry-services', 'pf_type' => 'private', 'pf_code' => '', 'db_code' => '', 'bayut_code' => 'Laundry Service"'),
            array('title' => "Marina Berth", 'type' => 'Amenities', 'image' => 'marina', 'pf_type' => 'private', 'pf_code' => '', 'db_code' => '', 'bayut_code' => 'Marina Berth'),
            array('title' => "Metro station", 'type' => 'Amenities', 'image' => 'metro', 'pf_type' => 'private', 'pf_code' => '', 'db_code' => '', 'bayut_code' => 'Metro station"'),
            array('title' => "Mosque", 'type' => 'Amenities', 'image' => 'mosque', 'pf_type' => 'private', 'pf_code' => '', 'db_code' => '', 'bayut_code' => 'Mosque'),
            array('title' => "Public park", 'type' => 'Amenities', 'image' => 'park', 'pf_type' => 'private', 'pf_code' => '', 'db_code' => '', 'bayut_code' => 'Public park"'),
            array('title' => "Public parking", 'type' => 'Amenities', 'image' => 'park-public', 'pf_type' => 'private', 'pf_code' => '', 'db_code' => '', 'bayut_code' => 'Public parking'),
            array('title' => "Public transport", 'type' => 'Amenities', 'image' => 'public-transport', 'pf_type' => 'private', 'pf_code' => '', 'db_code' => '', 'bayut_code' => 'Public transport'),
            array('title' => "Recreational Facilities", 'type' => 'Amenities', 'image' => 'recreational', 'pf_type' => 'private', 'pf_code' => '', 'db_code' => '', 'bayut_code' => 'Recreational Facilities'),
            array('title' => "Restaurants", 'type' => 'Amenities', 'image' => 'restaurants', 'pf_type' => 'private', 'pf_code' => '', 'db_code' => '', 'bayut_code' => 'Restaurants'),
            array('title' => "School", 'type' => 'Amenities', 'image' => 'school', 'pf_type' => 'private', 'pf_code' => '', 'db_code' => '', 'bayut_code' => 'School'),
            array('title' => "Shopping mall", 'type' => 'Amenities', 'image' => 'mall', 'pf_type' => 'private', 'pf_code' => '', 'db_code' => '', 'bayut_code' => 'Shopping mall'),
            array('title' => "Shops", 'type' => 'Amenities', 'image' => 'online', 'pf_type' => 'private', 'pf_code' => '', 'db_code' => '', 'bayut_code' => 'Shops'),
            array('title' => "Sports academies", 'type' => 'Amenities', 'image' => 'sports', 'pf_type' => 'private', 'pf_code' => '', 'db_code' => '', 'bayut_code' => 'Sports academies'),
            array('title' => "Squash courts", 'type' => 'Amenities', 'image' => 'squash', 'pf_type' => 'private', 'pf_code' => '', 'db_code' => '', 'bayut_code' => 'Squash courts'),
            array('title' => "Tennis courts", 'type' => 'Amenities', 'image' => 'tennis', 'pf_type' => 'private', 'pf_code' => '', 'db_code' => '', 'bayut_code' => 'Tennis courts'),
            array('title' => "Valet Service", 'type' => 'Amenities', 'image' => 'valet', 'pf_type' => 'private', 'pf_code' => '', 'db_code' => '', 'bayut_code' => "Valet Service"),
            array('title' => "Walking Trails", 'type' => 'Amenities', 'image' => 'walking', 'pf_type' => 'private', 'pf_code' => '', 'db_code' => 'WC', 'bayut_code' => 'Walking Trails'),

        );

        foreach ($portals as $portal){
            foreach ($features as $feat) {
                $objFeat =   PropertyAmenity::updateOrCreate([
                    "title" => $feat["title"]
                ],[
                    "type" => $feat["type"],
                    "amenity_type" => $feat["pf_type"],
                ]);

                $title = '';
                if ($portal->name == "Property Finder"){
                    $title = $feat['pf_code'] ?? "";
                }else if ($portal->name == 'Dubizzle'){
                    $title = $feat['db_code'] ?? "";
                }else if ($portal->name = 'Bayut'){
                    $title = $feat['bayut_code'] ?? "";
                }else{
                    $title = "";
                }

                if (!empty($title)) {
                    PropertyAmenityPortal::updateOrCreate([
                        "property_amenity_id" => $objFeat->id,
                        "portal_id" => $portal["id"],
                    ], [
                        "title" => $title,
                        "type" => $portal->name == 'Property Finder' ? ($feat['pf_type'] ?? null) : null,
                    ]);
                }
            }
        }
    }
}
