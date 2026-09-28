<?php

namespace App\Jobs;

use App\Models\CommunityProperty;
use App\Models\Developer;
use App\Models\DeveloperProject;
use App\Models\OffplanMeta;
use App\Models\OffplanProperty;
use App\Models\Property;
use App\Models\PropertyCategory;
use App\Models\PropertyMedia;
use App\Models\PropertyNote;
use App\Models\PropertyPortal;
use App\Models\PropertyPrice;
use App\Models\SubCommunity;
use App\Traits\FileTrait;
use DB;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EmaarPropertyPublishJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use FileTrait;

    public $timeout = 17200;

    protected $locationMapping;
    protected $bedMapping;
    protected $typeMapping;
    protected $properties;
    protected $unit_no_mapping;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($locationMapping, $bedMapping, $typeMapping, $properties, $unit_no_mapping)
    {
        $this->locationMapping = $locationMapping;
        $this->bedMapping = $bedMapping;
        $this->typeMapping = $typeMapping;
        $this->properties = $properties;
        $this->unit_no_mapping = $unit_no_mapping;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        info('Emaar Property Publish Job Started.');
        $offplans = OffplanProperty::whereIn('id', $this->properties ?? [])->get();
        $locations = $this->locationMapping;
        $beds = json_decode($this->bedMapping, true);
        $types = json_decode($this->typeMapping, true);

        if ($offplans->count() > 0) {

            foreach ($offplans as $offplan) {
                try {

                    $bed_id = $this->findBedId($offplan->bed_id, $beds);
                    $type_id = $this->findTypeId($offplan->property_type, $types);

                    $location = json_decode($offplan->location_data);

                    $building_name = $location->building;
                    $location_name = $location->location;
                    $property_name = $location->property;
                    $title = $location_name . ',' . $building_name . ',' . $property_name;

                    $property_type_id = PropertyCategory::where('title', $type_id)->first()->id ?? null;
                    $developers = Developer::where('id', $offplan->developer_id)->first();
                    $off_title = DeveloperProject::where('developer_id', $offplan->developer_id)->where('project_name', $offplan->project_name)->first();
                    $devproject = DeveloperProject::where('developer_id', $offplan->developer_id)->where('project_name', $offplan->project_name)->first();
                    $off_title = $off_title?->titles?->random(1)->first();
                    $locationDetails = revampedEmaarlocationIdentifier($offplan->location_data);
                    // dd($off_title->value);
                    if ($offplan->property_id) {
                        $findpro = Property::where('id', $offplan->property_id)->first();
                        if ($findpro) {
                            $old_unit_no = $findpro->unit_no;
                            $old_title = $findpro->title;
                            $old_description = $findpro->description;
                            if ($offplan->developer_id == 1) {
                                $findpro->unit_no = $this->processUnitNoRevamped($offplan->title);
                            } else {
                                $findpro->unit_no = $this->DamacUnitNumber($offplan->unit_no,$this->unit_no_mapping);
                            }

                            $description = processDescription($offplan->id, $offplan->developer_id);
                            $title = titleGenerator($off_title, $offplan->developer_id, $offplan->project_name);
                            $findpro->description = $description ?? $findpro->description;
                            $findpro->title = $title ?? $findpro->title;
                            $findpro->owner_id = 12656;
                            $findpro->bath_id = $bed_id ?? null;
                            $findpro->rera_permit_number = 1292466961;
                            $findpro->built_up_area = $offplan->total_area;
                            $findpro->plot_size = $offplan->plot_size;
                            $findpro->withoutApproval()->save();

                            Log::channel('discord')->info('Property Data Updated', [
                                'id' => $findpro->id,
                                'Old Title' => $old_title,
                                'New Title' => $title,
                                'Old Description' => $old_description,
                                'New Description' => $description,
                                'Old Unit No' => $old_unit_no,
                                'New Unit No' => $findpro->unit_no,
                                'Old Build up Area' => $findpro->built_up_area,
                                'New Build up Area' => $offplan->total_area,
                                'Old Plot Size' => $findpro->plot_size,
                                'New Plot Size' => $offplan->plot_size,
                            ]);

                            continue;
                        }
                    }

                    $location = json_decode($offplan->location_data);
                    $building_name = $location->building;
                    $location_name = $location->location;
                    $property_name = $location->property;
                    $title = $location_name . ',' . $building_name . ',' . $property_name;

                    $dat = $this->findCommunitySubCommunity($title, $locations);
                    if ($dat['community_property']) {
                        $community_property = CommunityProperty::where('id', $dat['community_property'])->first();
                        $community_property_id = $community_property->id ?? null;
                        $sub_community_id = $community_property?->sub_community_id ?? null;
                        $community_id = $community_property?->community_id ?? null;
                        $region_id = $sub_community->region_id ?? null;
                    }

                    if ($dat['sub_community']) {
                        $community_property_id = null;
                        $sub_community = SubCommunity::where('id', $dat['sub_community'])->first();
                        $sub_community_id = $sub_community->id ?? null;
                        $community_id = $sub_community?->community_id ?? null;
                        $region_id = $sub_community?->region_id ?? null;
                    }

                    DB::beginTransaction();
                    // $chatgpt_title = titleGenerator($off_title ?? null, $offplan->developer_id, $offplan->project_name);

                    $property = new Property();
                    $property->title = $off_title->value; // $chatgpt_title ?? $off_title->value ?? null;
                    $property->ref_no = generatePropertyRefNo();
                    $property->description = simpleDescriptionWriter($offplan->id, $offplan->developer_id);
                    $property->listing_source = $developers->full_name ?? 'Emaar';
                    $property->region_id = $region_id ?? null;
                    $property->agent_id = 4;
                    if (env('APP_ENV') == 'local') {
                        $property->owner_id = 12656;
                    } else {
                        $property->owner_id = $this->getOwnerId($offplan->developer_id);
                    }
                    $property->offplan_portal_fetch = 1;
                    $property->community_id = $community_id ?? null;
                    $property->sub_community_id = $sub_community_id ?? null;
                    $property->community_property_id = $community_property_id ?? null;

                    $property->bed_id = $bed_id ?? null;
                    $property->bath_id = $bed_id ?? null;

                    if ($offplan->developer_id == 1) {
                        $property->unit_no = $this->processUnitNoRevamped($offplan->title);
                    } else {
                        $property->unit_no = $this->DamacUnitNumber($offplan->unit_no,$this->unit_no_mapping);
                    }

                    $property->built_up_area = $offplan->total_area;
                    $property->plot_size = $offplan->plot_size;
                    $property->view = $offplan->view ?? null;
                    $property->parking = $offplan->parking ?? 0;
                    $property->furnished = "FURNISHED";
                    $property->rera_permit_number = 1292466961;
                    $property->completion_status = $offplan->completion_status;
                    $property->publish_status = 'DRAFT';
                    $property->developer_id = $offplan->developer_id;
                    $property->for = 'Residential for sale';
                    $property->property_category_id = $property_type_id;
                    $property->withoutApproval()->save();
                    $offplan->property_id = $property->id;
                    $offplan->save();
                    $property_price = new PropertyPrice();
                    $property_price->property_id = $property->id;
                    $property_price->price = $offplan->price;
                    $property_price->withoutApproval()->save();

                    $offplan_media = OffplanMeta::where('developer_project_id', $devproject->id)
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

                    $developer = Developer::where('id', $offplan->developer_id)->first();
                    $developer_name = $developer->full_name ?? 'Developer';

                    $publish_on = new PropertyPortal();
                    $publish_on->property_id = $property->id;
                    $publish_on->portal_id = 1;
                    $publish_on->save();
                    $property_note = new PropertyNote();
                    $property_note->property_id = $property->id;
                    $property_note->note = 'This property is published from ' . $developer_name . '. META ID ' . $offplan->id . ' .';
                    $property_note->created_by_id = 7;
                    $property_note->save();
                    DB::commit();
                    info('Property Created with Ref No ' . $property->ref_no . ' From ' . $developer_name . '. Meta ID ' . $offplan->id . '.');
                } catch (\Throwable $th) {
                    DB::rollback();
                    info($th);
                    continue;
                }

            }
        }
        info('Emaar Property Publish Job Completed.');
        return true;

    }

    public function processUnitNo($unit)
    {

        $unit = explode(' ', $unit);
        $unit = $unit[count($unit) - 1];
        return $unit;
    }

    public function descriptionGenerator($title, $developer, $project_name)
    {
        $apiKey = env('CHAT_API');
        $apiUrl = 'https://api.openai.com/v1/chat/completions';
        $message = $title . 'Project name is' . $project_name . ' by developer ' . $developer . '. Rewrite the following description for property listing title for AX CAPITAL company. Do not use any symbol or special character.Max characters 600-1000.';
        $response = Http::timeout(120)->withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->post($apiUrl, [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                ['role' => 'user', 'content' => $message],
            ],
        ]);
        $responseData = $response->json();
        $msg = $responseData['choices'][0]['message']['content'];
        return $msg;
    }

    public function procesUnitNumber($unit)
    {
        $lastHyphenPosition = strrpos($unit, "-");

        if ($lastHyphenPosition !== false) {
            $result = substr($unit, $lastHyphenPosition + 1);
            return $result;
        } else {
            return $unit;
        }
    }

    public function processUnitNoRevamped($input)
    {
        $parts = explode('-', $input);
        if (count($parts) >= 3) {

            $partstwo = explode(' ', $parts[count($parts) - 3]);
            if (!empty($partstwo)) {
                $lastElement = end($partstwo);
            } else {
                $lastElement = $parts[count($parts) - 3];
            }

            $lastTwoParts = $lastElement . '-' . $parts[count($parts) - 2] . '-' . $parts[count($parts) - 1];
            return $lastTwoParts;
        }if (count($parts) >= 2) {
            $lastTwoParts = $parts[count($parts) - 2] . '-' . $parts[count($parts) - 1];
            return $lastTwoParts;
        } else {
            return $input;
        }

    }

    public function findCommunitySubCommunity($title, $data)
    {
        foreach ($data ?? [] as $item) {
            if ($item['title'] === $title) {
                return array(
                    'community' => $item['community'],
                    'sub_community' => $item['sub_community'],
                    'community_property' => $item['community_property'],
                );
            }
        }

        return array(
            'community' => null,
            'sub_community' => null,
            'community_property' => null,
        );
    }

    public function findBedId($offplanbedID, $beds)
    {
        $targetBedId = $offplanbedID;
        $bed_id = null;

        foreach ($beds ?? [] as $bed) {
            foreach ($bed as $bedId => $value) {
                if ((int) $bedId === $targetBedId) {
                    $bed_id = $value;
                    break 2; // Exit both loops when a match is found
                }
            }
        }
        return $bed_id;
    }

    public function findTypeId($targetTypeId, $types)
    {
        $type_id = null;

        foreach ($types ?? [] as $type) {
            foreach ($type as $typeId => $value) {
                if ($typeId === $targetTypeId) {
                    $type_id = $value;
                    break 2; // Exit both loops when a match is found
                }
            }
        }

        return $type_id;
    }

    // function DamacUnitNumber($inputString) {
    //     $parts = explode('/', $inputString);
    //     $lastPart = end($parts);
    //     return $lastPart;
    // }

    public function __DamacUnitNumber($inputString, $case)
    {
        switch ($case) {
            case 1:
                $parts = explode('/', $inputString);
                if (count($parts) >= 3) {
                    $combinedParts = $parts[1] . '-' . $parts[2];
                    return $combinedParts;
                } else {
                    return $inputString;
                }
                break;
        }

    }
    public function DamacUnitNumber($inputString, $case)
    {
        switch ($case) {
            case 'case1':
                $parts = explode('-', $inputString);
                if (count($parts) >= 3) {
                    $combinedParts = $parts[count($parts) - 3] . '-' . $parts[count($parts) - 2] . '-' . $parts[count($parts) - 1];
                    return $combinedParts;
                } else {
                    return $inputString;
                }
                break;
            case 'case2':
                $parts = explode('-', $inputString);
                if (count($parts) >= 2) {
                    $combinedParts = $parts[count($parts) - 2] . '-' . $parts[count($parts) - 1];
                    return $combinedParts;
                } else {
                    return $inputString;
                }
                break;
            case 'case3':
                $parts = explode('-', $inputString);
                if (count($parts) >= 1) {
                    return $parts[count($parts) - 1];
                } else {
                    return $inputString;
                }
                break;
            case 'case4':
                $parts = explode('/', $inputString);
                if (count($parts) >= 3) {
                    $combinedParts = $parts[count($parts) - 3] . '-' . $parts[count($parts) - 2] . '-' . $parts[count($parts) - 1];
                    return $combinedParts;
                } else {
                    return $inputString;
                }
                break;
            case 'case5':
                $parts = explode('/', $inputString);
                if (count($parts) >= 2) {
                    $combinedParts = $parts[count($parts) - 2] . '-' . $parts[count($parts) - 1];
                    return $combinedParts;
                } else {
                    return $inputString;
                }
                break;
            case 'case6':
                $parts = explode('/', $inputString);
                if (count($parts) >= 1) {
                    return $parts[count($parts) - 1];
                } else {
                    return $inputString;
                }
                break;
            default:
                return $inputString;
        }
    }

    public function getOwnerId($id)
    {
        if ($id == 1) {
            return 12656;
        } elseif ($id == 5) {
            return 13475;
        } elseif ($id == 23) {
            return 13597;
        } elseif ($id == 48) {
            return 13613;
        } else {
            return null;
        }
    }

    //============================================================================
    //=================Transfered to Global Scope Global.php======================
    //============================================================================

    public function __descriptionRewriter($description, $developer, $project_name)
    {
        try {
            $apiKey = env('CHAT_API');
            $apiUrl = 'https://api.openai.com/v1/chat/completions';
            $message = $description . 'Project name is' . $project_name . ' by developer ' . $developer . '. Rewrite the description for property listing for AX CAPITAL company. Max limit 600-1000 characters. Do not use any symbol or special character.';
            $response = Http::timeout(120)->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post($apiUrl, [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                    ['role' => 'user', 'content' => $message],
                ],
            ]);
            $responseData = $response->json();
            // info($responseData);
            $msg = $responseData['choices'][0]['message']['content'];
            $msg = str_replace('"', '', $msg);
            return $msg;
        } catch (\Throwable $th) {
            info($th);
            return $description;
        }

    }
    public function __processDescription($id, $developer_id)
    {
        $developer = Developer::find($developer_id);
        $offplan = OffplanProperty::find($id);
        $devproj = DeveloperProject::where('developer_id', $developer->id)->where('project_name', $offplan->project_name)->first();
        $project_description = $devproj->description ?? 'Project Name is ' . $offplan->project_name . ' By ' . $developer->full_name . '.';
        $data = $offplan->data;
        if ($data) {
            $data = json_decode($data);
            $payment_plan = $data->paymentplans;
            $payment = "\n";
            $payment = $payment . "Payment Plan \n";
            foreach ($payment_plan as $plan) {
                $payment .= "Installment " . $plan->cm_Net_Percent__c . "% Amount AED " . $plan->cm_Net_Amount__c . ".\n";
            }
            $amenities = $data->amenities;
            $amenity = "\n";
            $amenity = $amenity . "Amenities\n";
            foreach ($amenities as $key => $amen) {
                $amenity .= $key . " : " . $amen . ".\n";
            }

            $description = $data->description ?? $project_description;
            $process_description = $description;

            if (!empty($payment_plan)) {
                $process_description .= "\n" . $payment;
            }
            if (!empty($amenities)) {
                $process_description .= "\n" . $amenity;
            }

            $fixed_text = $devproj->signature ?? "\nAX CAPITAL was built upon a desire to create a real estate agency of the modern era where the customer is central to everything we do. With our values clearly defined, we are committed tofoundation of AX CAPITAL and makes us different.\n\nExplore Our Services:\nAX MANAGEMENT\nAX DESIGN\nAX CORPORATE\nAX HOLIDAYS";
            $process_description .= "\n" . $fixed_text;

            return $process_description;

        } else {

            $description = descriptionRewriter($project_description, $developer->full_name, $offplan->project_name);
            $process_description = $description;
            $amenities = $devproj->amenities ?? "\nAMENITIES:\n- Infinity pool\n- Wet deck\n- Shaded pool deck\n- Sun loungers\n- Shaded kids’ pool area and play area\n- Raised lawn\n- Shaded communal area\n- Ground level boutiques and cafes\n";
            $features = $devproj->additional_description ?? "FEATURES:\n- Multiple Units Available\n- 1BR - 4BR bedroom apartments\n- 4BR – 6BR bedroom penthouses\n- BUA ranging from 4,462 sq. ft – 11,683 sq. ft.\n- Private balconies\n";
            $paymentPlans = $devproj->payment_plans ?? "PAYMENT PLANS:\n- 10% On Booking\n- 90% on construction\n- 10% on completion\n- Handover in July 2028\n";

            // $amenities = $devproj->amenities;
            // $paymentPlans = $devproj->payment_plans;
            // $features = $devproj->additional_description;

            $process_description .= "\n\n" . $amenities;
            $process_description .= "\n\n" . $features;
            $process_description .= "\n\n" . $paymentPlans;

            $fixed_text = $devproj->signature ?? "\nAX CAPITAL was built upon a desire to create a real estate agency of the modern era where the customer is central to everything we do. With our values clearly defined, we are committed tofoundation of AX CAPITAL and makes us different.\n\nExplore Our Services:\n-AX MANAGEMENT\n-AX DESIGN\n-AX CORPORATE\n-AX HOLIDAYS";
            // $fixed_text = $devproj->signature;
            $process_description .= "\n" . $fixed_text;
            return $process_description;
        }

    }
    public function __titleGenerator($title, $developer_id, $project_name)
    {
        // if (env('APP_ENV') == 'local') {
        //     return $title;
        // }

        try {
            $developer = Developer::find($developer_id);
            $apiKey = env('CHAT_API');
            $apiUrl = 'https://api.openai.com/v1/chat/completions';

            $message = $title . 'Project name is' . $project_name . ' by developer ' . $developer->full_name . '. Rewrite the following title for property listing title for AX Capital company not more than 50 characters and not less than 40. Minimum 4 words. Do not use any symbol or special character. Just use | do not use - ,+, & or any special characters in the titles  in title';

            $response = Http::timeout(120)->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post($apiUrl, [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                    ['role' => 'user', 'content' => $message],
                ],
            ]);
            $responseData = $response->json();
            // info($responseData);
            $msg = $responseData['choices'][0]['message']['content'];
            $msg = str_replace('"', '', $msg);
            return $msg;
        } catch (\Throwable $th) {
            info($th);
            return $title;
        }

    }
}
