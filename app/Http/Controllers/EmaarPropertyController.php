<?php

namespace App\Http\Controllers;

use App\Jobs\AziziPropertyFetchJob;
use App\Jobs\EmaarPropertyFetchJob;
use App\Jobs\EmaarPropertyPublishJob;
use App\Models\Bed;
use App\Models\Developer;
use App\Models\DeveloperProject;
use App\Models\OffplanProperty;
use App\Models\PropertyCategory;
use App\Traits\FileTrait;
use DB;
use Illuminate\Http\Request;

class EmaarPropertyController extends Controller
{
    use FileTrait;

    public function startJobs()
    {
        $dumps = OffplanProperty::latest()->limit(50)->get();

        $jobs = DB::table('laravel_jobs')->get();

        return view('admin.offplan.jobs')
            ->with('dumps', $dumps)
            ->with('jobs', $jobs);
    }

    public function emaar()
    {
        dispatch(new EmaarPropertyFetchJob());

        return redirect()->back()->with('message',"Property fetching from Emaar is currently in progress.");
    }

    public function createOffPlanListing()
    {
        // dispatch(new EmaarPropertyPublishJob());
        return redirect()->back()->with('message',"This is Success Message");
    }

    public function azizi()
    {
        dispatch(new AziziPropertyFetchJob());
        return redirect()->back()->with('message',"Property fetching from Azizi is currently in progress.");
    }

    public function __azizi()
    {
        OffplanProperty::where('developer_id', 48)->delete();
        $type = 'Residential';
        $page_size = 2000;
        $cookie = 'renderCtx=%7B%22pageId%22%3A%224c26d0f2-6230-4698-bbb9-b674095d0095%22%2C%22schema%22%3A%22Published%22%2C%22viewType%22%3A%22Published%22%2C%22brandingSetId%22%3A%2245f6cfa8-d465-48cb-911d-e261fdaa2a08%22%2C%22audienceIds%22%3A%22%22%7D; CookieConsentPolicy=0:1; LSKey-c$CookieConsentPolicy=0:1; oinfo=c3RhdHVzPUFDVElWRSZ0eXBlPTImb2lkPTAwRDF0MDAwMDAwdXRSUQ==; autocomplete=1; oid=00D1t000000utRQ; sid_Client=t000003cFqtt000000utRQ; clientSrc=94.206.206.53; inst=APP_09; _gcl_au=1.1.464504723.1697011372; _gid=GA1.2.1751047784.1697011372; _hjSessionUser_3074064=eyJpZCI6IjcwYmI2MmRiLTk5MmEtNTFlYS1iNzc1LTA2NDYwODIyZGRkYyIsImNyZWF0ZWQiOjE2OTcwMTEzNzI3NzUsImV4aXN0aW5nIjp0cnVlfQ==; _ym_uid=1697011373545799100; _ym_d=1697011373; _gac_UA-56309380-1=1.1697091760.Cj0KCQjwj5mpBhDJARIsAOVjBdrvacX13oaO64RS-qjd4OO1_LT-rs-0YUoE52HxvOq40HxXOWhMQBUaAgxREALw_wcB; _ga=GA1.2.875563513.1697011372; _ym_isad=1; _hjSession_3074064=eyJpZCI6ImE4NDdhODE2LWJmMDgtNGQyMC05ZGFmLTBkMDFhNTFkM2ZiNiIsImNyZWF0ZWQiOjE2OTcwOTE3NjAzOTgsImluU2FtcGxlIjpmYWxzZSwic2Vzc2lvbml6ZXJCZXRhRW5hYmxlZCI6ZmFsc2V9; _hjAbsoluteSessionInProgress=0; _ga_5WV8GZDYT6=GS1.1.1697091759.3.1.1697091760.59.0.0; _gcl_aw=GCL.1697091761.Cj0KCQjwj5mpBhDJARIsAOVjBdrvacX13oaO64RS-qjd4OO1_LT-rs-0YUoE52HxvOq40HxXOWhMQBUaAgxREALw_wcB; sfdc-stream=!sAMfMPXZfHN72O0AEZZFTB/hd0fPE/G3HT3pRp2abf25+AJj6Ah0IxVLAa5B4wuuZ9rvzEb91UUw/lg=; sid=00D1t000000utRQ!AQEAQFKkO2EmEman10226WKLsAMzfUBWd3k4rgfja6f3dRtL0JdyuLk1shDkWsQ09yWSBmMA.hD04WJIWtqcH2M.JKbn_UlK';
        info('Azizi Property Fetch Job Started');
        $token = "eyJub25jZSI6IjZZYTVjZDNFdGtSejlHTGdDQTJpU19MQ1VZbnU0czV6TDgzaGkxMlF3cHdcdTAwM2QiLCJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiIsImtpZCI6IntcInRcIjpcIjAwRDA5MDAwMDAwR3lYWVwiLFwidlwiOlwiMDJHMDkwMDAwMDAwWXFYXCIsXCJhXCI6XCJjYWltYW5zaWduZXJcIn0iLCJjcml0IjpbImlhdCJdLCJpYXQiOjE2OTcwOTE3Nzg5NzgsImV4cCI6MH0%3D..NqOiyOArebBKnrOTlK521LjMeHsPpWNLcsvnwwOrybk%3D";
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://crmx.azizidevelopments.com/s/sfsites/aura?r=6&other.SelectProperty.searchPropertiesApex=1',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'message=%7B%22actions%22%3A%5B%7B%22id%22%3A%22324%3Ba%22%2C%22descriptor%22%3A%22apex%3A%2F%2FSelectPropertyController%2FACTION%24searchPropertiesApex%22%2C%22callingDescriptor%22%3A%22markup%3A%2F%2Fc%3ASelectPropertyPage%22%2C%22params%22%3A%7B%22pageSize%22%3A%22' . $page_size . '%22%2C%22pageNumber%22%3A%221%22%2C%22sortby%22%3A%22Name%22%2C%22sortOrder%22%3A%22asc%22%2C%22selectedFilters%22%3A%5B%5D%2C%22selectedFiltersJson%22%3A%22%7B%5C%22Unit_Type1__c%5C%22%3A%5C%22' . $type . '%5C%22%2C%5C%22Unit_Status__c%5C%22%3A%5C%22Available%5C%22%7D%22%2C%22unitStatus%22%3A%22Available%22%7D%7D%5D%7D&aura.context=%7B%22mode%22%3A%22PROD%22%2C%22fwuid%22%3A%22MlRqRU5YT3pjWFRNenJranFOMWFjQXlMaWFpdmxPSTZWeEo0bWtiN0hsaXcyNDQuMjAuNC0yLjQxLjQ%22%2C%22app%22%3A%22siteforce%3AcommunityApp%22%2C%22loaded%22%3A%7B%22APPLICATION%40markup%3A%2F%2Fsiteforce%3AcommunityApp%22%3A%225e7_9lYY7rCtZgqqrY0dTg%22%2C%22COMPONENT%40markup%3A%2F%2Finstrumentation%3Ao11ySecondaryLoader%22%3A%22WAlywPtXLxVWA9DxV-jd3A%22%7D%2C%22dn%22%3A%5B%5D%2C%22globals%22%3A%7B%7D%2C%22uad%22%3Afalse%7D&aura.pageURI=%2Fs%2Fproperty-search&aura.token=' . $token,
            CURLOPT_HTTPHEADER => array(
                'authority: crmx.azizidevelopments.com',
                'accept: */*',
                'accept-language: en-US,en;q=0.9,ur;q=0.8',
                'content-type: application/x-www-form-urlencoded; charset=UTF-8',
                'cookie:' . $cookie,
                'origin: https://crmx.azizidevelopments.com',
                'referer: https://crmx.azizidevelopments.com/s/property-search',
                'sec-ch-ua: "Google Chrome";v="117", "Not;A=Brand";v="8", "Chromium";v="117"',
                'sec-ch-ua-mobile: ?0',
                'sec-ch-ua-platform: "macOS"',
                'sec-fetch-dest: empty',
                'sec-fetch-mode: cors',
                'sec-fetch-site: same-origin',
                'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/117.0.0.0 Safari/537.36',
                'x-sfdc-page-scope-id: 96bfe987-494d-4a77-bff7-e967aec44e94',
                'x-sfdc-request-id: 8473200000ff495e20',
            ),
        ));

        $response = curl_exec($curl);
        // dd($response);
        curl_close($curl);
        $data = json_decode($response, true);
        // info($data);
        $recordsData = $data['actions'][0]['returnValue']['prodList'];
        $count = 1;
        foreach ($recordsData as $key => $record) {

            try {
                DB::beginTransaction();
                $pro = OffplanProperty::updateOrCreate(
                    [
                        'unique_id' => $record['Id'],
                    ],
                    [
                        'developer_id' => 48,
                        'completion_status' => 'Off-Plan Primary' ?? null,
                        'title' => $record['Name'] . ' ' . $record['Project_Formula__c'] ?? null,
                        'project_name' => $record['Project_Formula__c'] ?? null,
                        'price' => $record['Amount__c'] ?? null,
                        'building_location' => $record['Building_Name__c'] ?? null,
                        'total_area' => $record['Total_Area_sqft__c'] ?? null,
                        'property_type' => $record['Unit_Type1__c'] ?? null,
                        'status' => $record['Unit_Status__c'] ?? null,
                        'bed_id' => $this->getBedroom($record['Unit_Type__c']) ?? null,
                        'bath_id' => null,
                        'property_usage' => $type ?? null,
                        'unit_no' => $record['Name'] ?? null,
                        'recently_added' => false,
                        'will_delete' => 1,
                    ]
                );

                $pro->save();
                $this->azizSingle($record['Id']);
                DB::commit();
                info('Azizi Property Fetched. ID: ' . $record['Id']);
            } catch (\Throwable $th) {
                DB::rollback();
                sleep(4);
                info($th);
                continue;
            }

        }
        info('Azizi Property Fetch Job Ended. Feteched ' . $count . ' Records');

    }

    public function azizSingle($propertyID)
    {
        // remove last 3 digits
        $originalPropertyID = $propertyID;
        $propertyID = substr($propertyID, 0, -3);
        $cookie = 'renderCtx=%7B%22pageId%22%3A%224c26d0f2-6230-4698-bbb9-b674095d0095%22%2C%22schema%22%3A%22Published%22%2C%22viewType%22%3A%22Published%22%2C%22brandingSetId%22%3A%2245f6cfa8-d465-48cb-911d-e261fdaa2a08%22%2C%22audienceIds%22%3A%22%22%7D; CookieConsentPolicy=0:1; LSKey-c$CookieConsentPolicy=0:1; oinfo=c3RhdHVzPUFDVElWRSZ0eXBlPTImb2lkPTAwRDF0MDAwMDAwdXRSUQ==; autocomplete=1; oid=00D1t000000utRQ; sid_Client=t000003cFqtt000000utRQ; clientSrc=94.206.206.53; inst=APP_09; _gcl_au=1.1.464504723.1697011372; _gid=GA1.2.1751047784.1697011372; _hjSessionUser_3074064=eyJpZCI6IjcwYmI2MmRiLTk5MmEtNTFlYS1iNzc1LTA2NDYwODIyZGRkYyIsImNyZWF0ZWQiOjE2OTcwMTEzNzI3NzUsImV4aXN0aW5nIjp0cnVlfQ==; _ym_uid=1697011373545799100; _ym_d=1697011373; _gac_UA-56309380-1=1.1697091760.Cj0KCQjwj5mpBhDJARIsAOVjBdrvacX13oaO64RS-qjd4OO1_LT-rs-0YUoE52HxvOq40HxXOWhMQBUaAgxREALw_wcB; _ga=GA1.2.875563513.1697011372; _ym_isad=1; _hjSession_3074064=eyJpZCI6ImE4NDdhODE2LWJmMDgtNGQyMC05ZGFmLTBkMDFhNTFkM2ZiNiIsImNyZWF0ZWQiOjE2OTcwOTE3NjAzOTgsImluU2FtcGxlIjpmYWxzZSwic2Vzc2lvbml6ZXJCZXRhRW5hYmxlZCI6ZmFsc2V9; _hjAbsoluteSessionInProgress=0; _ga_5WV8GZDYT6=GS1.1.1697091759.3.1.1697091760.59.0.0; _gcl_aw=GCL.1697091761.Cj0KCQjwj5mpBhDJARIsAOVjBdrvacX13oaO64RS-qjd4OO1_LT-rs-0YUoE52HxvOq40HxXOWhMQBUaAgxREALw_wcB; sfdc-stream=!sAMfMPXZfHN72O0AEZZFTB/hd0fPE/G3HT3pRp2abf25+AJj6Ah0IxVLAa5B4wuuZ9rvzEb91UUw/lg=; sid=00D1t000000utRQ!AQEAQFKkO2EmEman10226WKLsAMzfUBWd3k4rgfja6f3dRtL0JdyuLk1shDkWsQ09yWSBmMA.hD04WJIWtqcH2M.JKbn_UlK';

        $token = "eyJub25jZSI6IjZZYTVjZDNFdGtSejlHTGdDQTJpU19MQ1VZbnU0czV6TDgzaGkxMlF3cHdcdTAwM2QiLCJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiIsImtpZCI6IntcInRcIjpcIjAwRDA5MDAwMDAwR3lYWVwiLFwidlwiOlwiMDJHMDkwMDAwMDAwWXFYXCIsXCJhXCI6XCJjYWltYW5zaWduZXJcIn0iLCJjcml0IjpbImlhdCJdLCJpYXQiOjE2OTcwOTE3Nzg5NzgsImV4cCI6MH0%3D..NqOiyOArebBKnrOTlK521LjMeHsPpWNLcsvnwwOrybk%3D";

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://crmx.azizidevelopments.com/s/sfsites/aura?r=4&ui-force-components-controllers-recordGlobalValueProvider.RecordGvp.getRecord=1',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'message=%7B%22actions%22%3A%5B%7B%22id%22%3A%22324%3Ba%22%2C%22descriptor%22%3A%22serviceComponent%3A%2F%2Fui.force.components.controllers.recordGlobalValueProvider.RecordGvpController%2FACTION%24getRecord%22%2C%22callingDescriptor%22%3A%22UNKNOWN%22%2C%22params%22%3A%7B%22recordDescriptor%22%3A%22' . $originalPropertyID . '.undefined.FULL.null.null.null.VIEW.true.null.null.null%22%7D%7D%5D%7D&aura.context=%7B%22mode%22%3A%22PROD%22%2C%22fwuid%22%3A%22MlRqRU5YT3pjWFRNenJranFOMWFjQXlMaWFpdmxPSTZWeEo0bWtiN0hsaXcyNDQuMjAuNC0yLjQxLjQ%22%2C%22app%22%3A%22siteforce%3AcommunityApp%22%2C%22loaded%22%3A%7B%22APPLICATION%40markup%3A%2F%2Fsiteforce%3AcommunityApp%22%3A%225e7_9lYY7rCtZgqqrY0dTg%22%2C%22COMPONENT%40markup%3A%2F%2Finstrumentation%3Ao11ySecondaryLoader%22%3A%22WAlywPtXLxVWA9DxV-jd3A%22%7D%2C%22dn%22%3A%5B%5D%2C%22globals%22%3A%7B%7D%2C%22uad%22%3Afalse%7D&aura.pageURI=%2Fs%2Fdetail%2F' . $propertyID . '&aura.token=' . $token,
            CURLOPT_HTTPHEADER => array(
                'authority: crmx.azizidevelopments.com',
                'accept: */*',
                'accept-language: en-US,en;q=0.9,ur;q=0.8',
                'content-type: application/x-www-form-urlencoded; charset=UTF-8',
                'cookie:' . $cookie,
                'origin: https://crmx.azizidevelopments.com',
                'referer: https://crmx.azizidevelopments.com/s/detail/' . $propertyID,
                'sec-ch-ua: "Google Chrome";v="117", "Not;A=Brand";v="8", "Chromium";v="117"',
                'sec-ch-ua-mobile: ?0',
                'sec-ch-ua-platform: "macOS"',
                'sec-fetch-dest: empty',
                'sec-fetch-mode: cors',
                'sec-fetch-site: same-origin',
                'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/117.0.0.0 Safari/537.36',
                'x-sfdc-page-cache: f5f08a7c12e54089',
                'x-sfdc-page-scope-id: 47b798f0-4330-4ea2-a488-ddda92ce6c54',
                'x-sfdc-request-id: 247569000042e1e61a',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $data = json_decode($response, true);
        $recordsData = $data['context']['globalValueProviders'][1]['values']['records'][$originalPropertyID]['Product2']['record']['fields'];

        // sleep(2);info($recordsData['Building_Name_and_Unit__c']['value']); // unit

        // sleep(2);info($recordsData['Unit_Type__c']['value']); // bed

        // sleep(2);info($recordsData['Unit_Size__c']['value']); // size

        // sleep(2);info($recordsData['Total_Area_sqft__c']['value']); // sqft

        // sleep(2);info($recordsData['Rate_Per_Sqft__c']['value']); // rate per sqft

        // sleep(2);info($recordsData['Property_Type_Formula__c']['value']); // property type commercial or redidential

        // sleep(2);info($recordsData['ProjectName__c']['value']); // Project Name

        // sleep(2);info($recordsData['Number_of_Car_Parking_Spaces__c']['value']); //number of parkings

        // sleep(2);info($recordsData['Location__c']['value']); //Location MBR City

        // sleep(2);info($recordsData['Launched_Price__c']['value']); //lanch price
        // sleep(2);info($recordsData['Furniture__c']['value']); //furniture
        // sleep(2);info($recordsData['Description']['value']); //description
        // //CreatedDate
        // sleep(2);info($recordsData['CreatedDate']['value']); //CreatedDate
        // // CreatedBy
        // sleep(2);info($recordsData['CreatedBy']['displayValue']); //CreatedBy
        // sleep(2);info($recordsData['BuildingNameNew__c']['value']);
        // sleep(2);info($recordsData['RecordType']['value']);
        // info('----------');
        //Community__r
        // dd($recordsData['Community__r']['value']); //Community__r
        //Building__r
        // dd($recordsData['Building__r']['value']); //Building__r
        // dd($recordsData['PhaseName__c']['value']); // phase name

        $pro = OffplanProperty::updateOrCreate(
            [
                'unique_id' => $originalPropertyID,
            ],
            [

                'status' => $recordsData['Admin_Status__c']['value'],
                'building_name' => $recordsData['BuildingNameNew__c']['value'],
                'name' => $recordsData['CreatedBy']['displayValue'] ?? null,
                'description' => $recordsData['Description']['value'] ?? null,
                'bath_id' => null,
                'project_name' => $recordsData['ProjectName__c']['value'],
                'location_code' => $recordsData['Location__c']['value'],

                'parking' => $recordsData['Number_of_Car_Parking_Spaces__c']['value'],

                'plot_size' => $recordsData['Unit_Size__c']['value'],
                'total_area' => $recordsData['Total_Area_sqft__c']['value'],

                'building_location' => $recordsData['Location__c']['value'],
                // 'building_name' => $recordsData['Building__r']['value'],

                'community_name' => $recordsData['Community__r']['displayValue'],
                // 'location_id'=>$recordsData['Total_Area_sqft__c']['value'],

                'property_type' => $recordsData['RecordType']['displayValue'],
                // 'property_usage' => $recordsData['RecordType']['displayValue'],
                'property_name' => $recordsData['Community__r']['displayValue'],
                'status' => $recordsData['Unit_Status__c']['value'],
                'booking_status' => $recordsData['Unit_Status__c']['value'],

                'recently_added' => false,
                'will_delete' => 1,

                'data' => $recordsData['Total_Area_sqft__c']['value'],
                'location_data' => $recordsData['Total_Area_sqft__c']['value'],
            ]
        );

        $pro->data = [
            'amenities' => [],
            'paymentplans' => [],
            'description' => $recordsData['Description']['value'],
            'furnished' => $recordsData['Furniture__c']['value'],
            'floor' => $recordsData['Floor__r']['displayValue'],
        ];
        $pro->location_data = [
            'building' => $recordsData['Building__r']['displayValue'],
            'location' => $recordsData['Location__c']['value'],
            'property' => $recordsData['Community__r']['displayValue'],
        ];
        $pro->save();
        sleep(2);

    }

    public function getBedroom($type)
    {
        $types = [
            'Garden Apartment' => '1',
            'Terraced Apartment' => '1',
            'Studio' => '1',
            'Shop' => null,
            'Retail' => null,
            'Penthouse' => 1,
            '3 Bedroom-duplex' => 3,
            '3 Bedroom + maid' => 3,
            '3 Bedroom' => 3,
            '2 Bedroom' => 2,
            '2 Bedroom + maid' => 2,
            '2 Bedroom-duplex' => 2,
            '1 Bedroom-duplex' => 1,
            '1 Bedroom' => 1,
        ];
        return $types[$type];

    }

    public function getType($type)
    {
        $types = [
            'Garden Apartment' => 'Apartment',
            'TERRACED APARTMENT' => 'Apartment',
            'STUDIO' => 'Apartment',
            'SHOP' => 'Shop',
            'RETAIL' => 'Retail',
            'PENTHOUSE' => 'Penthouse',
            '3 BEDROOM-DUPLEX' => 'Apartment',
            '3 BEDROOM + MAID' => 'Apartment',
            '3 BEDROOM' => 'Apartment',
            '2 BEDROOM + MAID' => 'Apartment',
            '1 BEDROOM-DUPLEX' => 'Apartment',
            '1 BEDROOM' => 'Apartment',
        ];
        return $types[$type];
    }

    public function processAzizUnitNo($unit)
    {

    }

    public function importPropertoes()
    {

        $developers = Developer::has('projects')->get();
        return view('admin.offplan.import')
            ->with('developers', $developers);
    }
    public function developerProjects(Request $request)
    {
        $projects = DeveloperProject::where('developer_id', $request->developer_id)->get();
        return response()->json(['status' => '1', 'msg' => 'Projects Found', 'data' => $projects]);
    }

    public function fetchOffPlanListings(Request $request)
    {
        $project = developerProject::where('id', $request->project_id)->first();
        $dumps = OffplanProperty::where('developer_id', $request->developer_id)->where('project_name', $project->project_name)->get();
        $locations = OffplanProperty::where('developer_id', $request->developer_id)->where('project_name', $project->project_name)->distinct()->get(['location_data']);
        $beds = OffplanProperty::where('developer_id', $request->developer_id)->where('project_name', $project->project_name)->distinct()->get(['bed_id']);
        $all_beds = bed::all();

        $propertytypes = OffplanProperty::where('developer_id', $request->developer_id)->where('project_name', $project->project_name)->distinct()->get(['property_type']);
        $types = PropertyCategory::all();

        return view('admin.offplan.partials.listing')
            ->with('dumps', $dumps)
            ->with('locations', $locations)
            ->with('beds', $beds)
            ->with('all_beds', $all_beds)
            ->with('propertytypes', $propertytypes)
            ->with('types', $types);
    }

    public function terminateJob($id)
    {
        $jobIdToTerminate = $id;
        DB::table('jobs')->where('id', $jobIdToTerminate)->delete();
        return redirect()->back();

    }

    public function importProperties()
    {
        $developers = Developer::has('projects')->get();
        return view('admin.offplan.import')
            ->with('developers', $developers);
    }

    public function importPropertiesPOST(Request $request)
    {
        // dd($request->all());
        $locationMapping = $request->locationMapping;
        $bedMapping = $request->bedMapping;
        $typeMapping = $request->typeMapping;
        $properties = $request->property_id;
        $unit_no_mapping = $request->unit_no_mapping;
        $locationMapping = json_decode($request->locationMapping, true);
        // dd($locationMapping);

        EmaarPropertyPublishJob::dispatch($locationMapping, $bedMapping, $typeMapping, $properties, $unit_no_mapping);
        return redirect()->back()->with('message',"Properties are currently being imported in the background.");

    }

    public function findCommunitySubCommunity($title, $data)
    {
        foreach ($data as $item) {
            if ($item['title'] === $title) {
                return array(
                    'community' => $item['community'],
                    'sub_community' => $item['sub_community'],
                    'community_property' => $item['sub_community'],
                );
            }
        }

        return array(
            'community' => null,
            'sub_community' => null,
            'community_property' => null,
        );
    }
}
