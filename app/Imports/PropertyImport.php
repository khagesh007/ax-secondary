<?php

namespace App\Imports;

use App\Models\Community;
use App\Models\CommunityProperty;
use App\Models\Developer;
use App\Models\DeveloperProject;
use App\Models\OffplanMeta;
use App\Models\Property;
use App\Models\PropertyMedia;
use App\Models\PropertyNote;
use App\Models\PropertyPortal;
use App\Models\PropertyPrice;
use App\Models\Region;
use App\Models\SubCommunity;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use DB;
class PropertyImport implements ToModel, WithStartRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $region = null;
        $community = null;
        $community_property = null;
        $sub_community = null;
        $count = 0;

        if (!empty($row)) {
            if (isset($row[0])) {
                $unique_id = $row[0];
                // info('Unique ID: ' . $unique_id);
            }
            if (isset($row[1])) {
                $developer_name = $row[1];
                // info('Developer Name: ' . $developer_name);
            }
            if (isset($row[2])) {
                $developer_id = $row[2];
                // info('Developer ID: ' . $developer_id);
            }
            if (isset($row[3])) {
                $project_name = $row[3];
                // info('Project Name: ' . $project_name);
            }
            if (isset($row[4])) {
                $unit_no = $row[4];
                // info('Unit No: ' . $unit_no);
            }
            if (isset($row[5])) {
                $title = $row[5];
                // info('Title: ' . $title);
            }
            if (isset($row[6])) {
                $description = $row[6];
            }
            if (isset($row[7])) {
                $no_of_baths = $row[7];
            }
            if (isset($row[8])) {
                $bath_id = $row[8];
            }
            if (isset($row[9])) {
                $no_of_beds = $row[9];
            }
            if (isset($row[10])) {
                $bed_id = $row[10];
            }
            if (isset($row[11])) {
                $property_category_name = $row[11];
            }
            if (isset($row[12])) {
                $property_category_id = $row[12];
            }
            if (isset($row[13])) {
                $for = $row[13];
            }
            if (isset($row[14])) {
                $region_name = $row[14];
                $region = Region::where('name', $row[14])->first();
            }
            if (isset($row[15])) {
                $region_id = $row[15];
                $region = Region::where('id', $row[15])->first();
            }
            if (isset($row[16])) {
                $community_name = $row[16];
                $community = Community::where('name', $row[16])->orWhere('alternate_name', $row[16])->first();
            }

            if (isset($row[17])) {
                $community_id = $row[17];
                $community = Community::where('id', $row[17])->first();
            }

            if (isset($row[18])) {
                $sub_community_name = $row[18];
                $sub_community = SubCommunity::where('name', $row[18])->orWhere('alternate_name', $row[18])->first();
            }
            if (isset($row[19])) {
                $sub_community_id = $row[19];
                $sub_community = SubCommunity::where('id', $row[19])->first();
            }
            if (isset($row[20])) {
                $community_property_name = $row[20];
                $community_property = CommunityProperty::where('name', $row[20])->orWhere('alternate_name', $row[20])->first();

            }
            if (isset($row[21])) {
                $community_property_id = $row[21];
                $community_property = CommunityProperty::where('id', $row[21])->first();
            }

            if (isset($row[22])) {
                $parking = $row[22];
            }
            if (isset($row[23])) {
                $plot_size = $row[23];
            }
            if (isset($row[24])) {
                $built_up_area = $row[24];
            }
            if (isset($row[25])) {
                $selling_price = $row[25];
            }
            if (isset($row[26])) {
                $price = $row[26];
            }
            if (isset($row[27])) {
                $land_registration_fee = $row[27];
            }
            if (isset($row[28])) {
                $applicable_fee = $row[28];
            }
            if (isset($row[29])) {
                $total_amount = $row[29];
            }
            if (isset($row[30])) {
                $miscellaneous = $row[30];
            }
            if (isset($row[31])) {
                $status = $row[31];
            }
            $developer_id = 1;
            $project_name = 'Bayview';
            $developer_project = DeveloperProject::where('developer_id', $developer_id)->where('project_name', $project_name)->first();

            $title_data = $developer_project?->titles?->random(1)->first();

            // $chat_gpt_title = titleGenerator($title_data, $developer_project->developer_id, $developer_project->project_name);
            // $chat_gpt_description = processDescription($developer_project->id, $developer_project->developer_id);

            try {
                DB::beginTransaction();
                // Property Creation
                $find_property_query = Property::where('unit_no', $unit_no);
                if ($community->id) {
                    $find_property_query->where('community_id', $community->id);
                }
                if ($sub_community->id) {
                    $find_property_query->where('sub_community_id', $sub_community->id);
                }
                if ($community_property->id) {
                    $find_property_query->where('community_property_id', $community_property->id);
                }
                $find_property = $find_property_query->first();

                if ($find_property) {
                    $property = $find_property;
                } else {
                    $property = new Property();
                }
                $property->ref_no = generatePropertyRefNo();
                $property->title = $title; // generated by GPT3
                $property->description = $description; // generated by GPT3
                $property->for = $for;
                $property->region_id = $region->id;
                $property->community_id = $community->id;
                $property->sub_community_id = $sub_community->id;
                $property->community_property_id = $community_property->id;
                $property->bath_id = $bath_id;
                $property->bed_id = $bed_id;
                $property->unit_no = $unit_no;
                $property->built_up_area = $built_up_area;
                $property->plot_size = $plot_size;
                $property->parking = $parking;
                $property->developer_id = $developer_id;
                $property->property_category_id = $property_category_id;
                $property->furnished = "FURNISHED";
                $property->rera_permit_number = 1292466961;
                if (env('APP_ENV') == 'local') {
                    $property->owner_id = 12656;
                } else {
                    $property->owner_id = $this->getOwnerId($offplan->developer_id);
                }
                $property->agent_id = 4;
                $property->publish_status = 'DRAFT';

                $property->withoutApproval()->save();
                // Property Price Creation
                $property_price = new PropertyPrice();
                $property_price->property_id = $property->id;
                $property_price->price = $price;
                $property_price->withoutApproval()->save();

                // Property Media Creation

                if (!$find_property) {
                    $offplan_media = OffplanMeta::where('developer_project_id', $developer_project->id)
                        ->where('key', 'Photos')
                        ->get();

                    $shuffledMedia = $offplan_media->shuffle();
                    foreach ($shuffledMedia as $media) {
                        $decoded_images = json_decode($media->value);
                        PropertyMedia::create([
                            "property_id" => $property->id,
                            "type" => 'Photos',
                            "path" => $decoded_images->image ?? null,
                            "original" => $decoded_images->original ?? null,
                            "thumbnail" => $decoded_images->thumbnail ?? null,
                            "created_by_id" => auth()->id(),
                        ]);
                    }
                    // Property Portal Creation
                    $publish_on = new PropertyPortal();
                    $publish_on->property_id = $property->id;
                    $publish_on->portal_id = 1;
                    $publish_on->save();

                    $developer = Developer::where('id', $developer_id)->first();
                    $developer_name = $developer->full_name ?? 'Developer';
                    // Property Note Creation
                    $property_note = new PropertyNote();
                    $property_note->property_id = $property->id;
                    $property_note->note = 'This property is published from ' . $developer_name . '. From the Excel Upload.';
                    $property_note->created_by_id = 7;
                    $property_note->save();
                } else {
                    $property_note = new PropertyNote();
                    $property_note->property_id = $property->id;
                    $property_note->note = 'This property is Updated from ' . $developer_name . '. From the Excel Upload.';
                    $property_note->created_by_id = 7;
                    $property_note->save();
                }
                info('Property Created/Updated: ' . $property->id);
                DB::commit();
                return $property;
            } catch (\Throwable $th) {
                DB::rollback();
                // info($th->getMessage());
                return null; // Return null or handle error accordingly
                // throw $th;
            }
        }
    }

    public function startRow(): int
    {
        return 2; // Skip the first row (header) and start importing from the second row
    }

}
