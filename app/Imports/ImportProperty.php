<?php

namespace App\Imports;

use App\Models\Community;
use App\Models\CommunityProperty;
use App\Models\Contact;
use App\Models\Property;
use App\Models\PropertyCategory;
use App\Models\PropertyDetail;
use App\Models\PropertyPortal;
use App\Models\PropertyPrice;
use App\Models\PropertyStatus;
use App\Models\SubCommunity;
use Maatwebsite\Excel\Concerns\ToModel;

class ImportProperty implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {

      
        if ($row[0] != '' && $row[0] != 'Id') {
           
            $property = Property::where('my_crm_id', $row[0])->first();
            $community_property = CommunityProperty::where('name', trim($row[6]))->pluck('id');
            $community      = Community::where('name', $row[8])->pluck('id');
            $sub_community  = SubCommunity::where('name', $row[7])->pluck('id');
            $property_category = PropertyCategory::where('title', trim($row[13]))->pluck('id');
            $property_status = PropertyStatus::where('title', ucfirst($row[23]))->pluck('id');
           $landlord = Contact::where('mobile', $row[34])->orWhere('email', $row[35])->pluck('id');
           
          
           $for = '';
            if ($row[12] == 'sale' && $row[11] == 'residential') {
                $for = 'Residential for sale';
            } elseif ($row[12] == 'sale' && $row[11] == 'commercial') {
                $for = 'Commercial for sale';
            } elseif ($row[12] == 'rent' && $row[11] == 'commercial') {
                $for = 'Commercial for rent';
            } elseif ($row[12] == 'rent' && $row[11] == 'residential') {
                $for = 'Residential for rent';
            }
            if ($property) {
                $property->my_crm_id                = $row[0];
                $property->ref_no                   = $row[1];
                $property->title                    = $row[2];
                $property->unit_no                  = $row[5];
                $property->community_property_id    = $community_property[0] ?? null;
                $property->community_id             = $community[0] ?? null;
                $property->sub_community_id         = $sub_community[0] ?? null;
                $property->property_category_id     = $property_category[0] ?? null;
                $property->property_status_id       = $property_status[0] ?? null;
                $property->for                      = $for;
                $property->built_up_area            = $row[14];
                $property->bed_id                   = ($row[16]!==null && $row[16] >= 0) ? $row[16]+1 : null;
                $property->bath_id                  = ($row[17] !== null && $row[17] > 0) ?  trim($row[17]) : null;
                $property->parking                  = trim($row[21]) ?? null;
                //$property->360_link = $row[30] ?? null;
                $property->description              = preg_replace("/\\\\n/", "<br/>", $row[36]) ?? null;
                $property->owner_id                 = $landlord[0] ?? null;
                $property->rera_permit_number       = $row[51] ?? null;
                $property->rera_issue_date          = date('Y-m-d',strtotime($row[52])) ?? null;
                $property->rera_expiry_date         = date('Y-m-d', strtotime($row[53])) ?? null;
                $property->save();
                $price = PropertyPrice::where('property_id', $property->id)->first() ?? new PropertyPrice();
                $price->property_id = $property->id;
                $price->price = $row[25];
                $price->prices = $row[26];
                $price->price_on_application = $row[27];
                $price->save();
                $propertyDetails = PropertyDetail::where('property_id', $property->id)->first() ?? new PropertyDetail();
                $propertyDetails->property_id = $property->id;
                $propertyDetails->developer = $row[22] ?? null;
                return $property;
            }else{
                $property = new Property();
                $property->my_crm_id = $row[0];
                $property->ref_no = $row[1];
                $property->title = $row[2];
                $property->unit_no = $row[5];
                $property->community_property_id = $community_property[0] ?? null;
                $property->community_id  = $community[0] ?? null;
                $property->sub_community_id   = $sub_community[0] ?? null;
                $property->property_category_id = $property_category[0] ?? null;
                $property->property_status_id  = $property_status[0] ?? null;
                $property->for = $for;
                $property->built_up_area = $row[14];
                $property->bed_id                   = ($row[16] !== null && $row[16] >= 0) ? $row[16] + 1 : null;
                $property->bath_id                  = ($row[17] !== null && $row[17] > 0) ?  trim($row[17]) : null;
                $property->parking = $row[21] ?? null;
               // $property->360_link = $row[30] ?? null;
                $property->description              = preg_replace("/\\\\n/", "<br/>", $row[36]) ?? null;
                $property->owner_id                 = $landlord[0] ?? null;
                $property->rera_permit_number       = $row[51] ?? null;
                $property->rera_issue_date          = date('Y-m-d', strtotime($row[52])) ?? null;
                $property->rera_expiry_date         = date('Y-m-d', strtotime($row[53])) ?? null;
                $property->save();
                $price = new PropertyPrice();
                $price->property_id = $property->id;
                $price->price = $row[25];
                $price->save();
                $portals = [];
                if($row['70'] == 'Yes'){
                $portals[] = array('property_id' => $property->id, 'portal_id' => 1);
                }
                if($row['71'] == 'Yes'){
                $portals[] = array('property_id' => $property->id, 'portal_id' => 3);
                }
                if($row['72'] == 'Yes'){
                $portals[] = array('property_id' => $property->id, 'portal_id' => 2);
                }
                PropertyPortal::insert($portals);
             
                $propertyPortal = new PropertyPortal();
                $propertyPortal->property_id = $property->id;
                $propertyPortal->portal_id = 1;
                $propertyPortal->save();
                $propertyDetails = PropertyDetail::where('property_id', $property->id)->first() ?? new PropertyDetail();
                $propertyDetails->property_id = $property->id;
                $propertyDetails->developer = $row[22] ?? null;
                return $property;
              
            }
           
        }
        
      
    }
}
