<?php

namespace App\Jobs;

use App\Models\OffplanProperty;
use DB;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AziziPropertyFetchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $timeout = 7200;
    private $cookie;
    private $token;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->cookie = 'renderCtx=%7B%22pageId%22%3A%224c26d0f2-6230-4698-bbb9-b674095d0095%22%2C%22schema%22%3A%22Published%22%2C%22viewType%22%3A%22Published%22%2C%22brandingSetId%22%3A%2245f6cfa8-d465-48cb-911d-e261fdaa2a08%22%2C%22audienceIds%22%3A%22%22%7D; CookieConsentPolicy=0:1; LSKey-c$CookieConsentPolicy=0:1; oinfo=c3RhdHVzPUFDVElWRSZ0eXBlPTImb2lkPTAwRDF0MDAwMDAwdXRSUQ==; autocomplete=1; oid=00D1t000000utRQ; _gcl_au=1.1.464504723.1697011372; _hjSessionUser_3074064=eyJpZCI6IjcwYmI2MmRiLTk5MmEtNTFlYS1iNzc1LTA2NDYwODIyZGRkYyIsImNyZWF0ZWQiOjE2OTcwMTEzNzI3NzUsImV4aXN0aW5nIjp0cnVlfQ==; _ym_uid=1697011373545799100; _ym_d=1697011373; _gac_UA-56309380-1=1.1697636741.Cj0KCQjwhL6pBhDjARIsAGx8D59ocuP1JfTvIohEllCJVORUXp2y7pLfOaWHD2o9kTfno5NwCqqo33saAvI0EALw_wcB; _gcl_aw=GCL.1697636745.Cj0KCQjwhL6pBhDjARIsAGx8D59ocuP1JfTvIohEllCJVORUXp2y7pLfOaWHD2o9kTfno5NwCqqo33saAvI0EALw_wcB; _ga=GA1.2.875563513.1697011372; _ga_5WV8GZDYT6=GS1.1.1700636583.7.0.1700636583.60.0.0; sid_Client=t000003cFqtt000000utRQ; clientSrc=94.206.206.53; inst=APP_Tf; sid=00D1t000000utRQ!AQEAQPR4AGbcd7A2WUa0SmNj28rhU0EqlTR3hsX9hfuOgft3IDwllIKD9ax2qs7pFv_nSEwEDfU6owWkJMO1R05LoJo7YSG2';
        $this->token = "eyJub25jZSI6Ik42TENndXRlTVNMa2JVQ2RvVEhna1F2UldMWkx4dmR5NzFpUHZlMVRYeGdcdTAwM2QiLCJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiIsImtpZCI6IntcInRcIjpcIjAwRFRmMDAwMDAwMDAwMVwiLFwidlwiOlwiMDJHVGYwMDAwMDAwMDFkXCIsXCJhXCI6XCJjYWltYW5zaWduZXJcIn0iLCJjcml0IjpbImlhdCJdLCJpYXQiOjE3MDEzMjMwODIzOTksImV4cCI6MH0%3D..-kyLxY1mYajJ-bYRXalQUiFefWuPXZK-_4tmxQYkkkk%3D";

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $type = 'Residential';
        $page_size = 2000;
        info('Azizi Property Fetch Job Started');
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://crmx.azizidevelopments.com/s/sfsites/aura?r=7&other.SelectProperty.searchPropertiesApex=1',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'message=%7B%22actions%22%3A%5B%7B%22id%22%3A%22404%3Ba%22%2C%22descriptor%22%3A%22apex%3A%2F%2FSelectPropertyController%2FACTION%24searchPropertiesApex%22%2C%22callingDescriptor%22%3A%22markup%3A%2F%2Fc%3ASelectPropertyPage%22%2C%22params%22%3A%7B%22pageSize%22%3A%22'. $page_size .'%22%2C%22pageNumber%22%3A%221%22%2C%22sortby%22%3A%22Name%22%2C%22sortOrder%22%3A%22asc%22%2C%22selectedFilters%22%3A%5B%5D%2C%22selectedFiltersJson%22%3A%22%7B%5C%22Unit_Type1__c%5C%22%3A%5C%22' . $type . '%5C%22%2C%5C%22Unit_Status__c%5C%22%3A%5C%22Available%5C%22%7D%22%2C%22unitStatus%22%3A%22Available%22%7D%7D%5D%7D&aura.context=%7B%22mode%22%3A%22PROD%22%2C%22fwuid%22%3A%22MDM0c01pMVUtd244bVVLc2VRYzQ2UWRkdk8xRWxIam5GeGw0LU1mRHRYQ3cyNDYuMTUuNS0zLjAuNA%22%2C%22app%22%3A%22siteforce%3AcommunityApp%22%2C%22loaded%22%3A%7B%22APPLICATION%40markup%3A%2F%2Fsiteforce%3AcommunityApp%22%3A%22ncylk18YyR_zaIjevsSmBw%22%2C%22COMPONENT%40markup%3A%2F%2Finstrumentation%3Ao11ySecondaryLoader%22%3A%22iVoI_RYCX4m4O5loBTnQfA%22%7D%2C%22dn%22%3A%5B%5D%2C%22globals%22%3A%7B%7D%2C%22uad%22%3Afalse%7D&aura.pageURI=%2Fs%2Fproperty-search&aura.token=' . $this->token,
            // CURLOPT_POSTFIELDS => 'message=%7B%22actions%22%3A%5B%7B%22id%22%3A%22358%3Ba%22%2C%22descriptor%22%3A%22apex%3A%2F%2FSelectPropertyController%2FACTION%24searchPropertiesApex%22%2C%22callingDescriptor%22%3A%22markup%3A%2F%2Fc%3ASelectPropertyPage%22%2C%22params%22%3A%7B%22pageSize%22%3A%22' . $page_size . '%22%2C%22pageNumber%22%3A%221%22%2C%22sortby%22%3A%22Name%22%2C%22sortOrder%22%3A%22asc%22%2C%22selectedFilters%22%3A%5B%5D%2C%22selectedFiltersJson%22%3A%22%7B%5C%22Unit_Type1__c%5C%22%3A%5C%22' . $type . '%5C%22%2C%5C%22Unit_Status__c%5C%22%3A%5C%22Available%5C%22%7D%22%2C%22unitStatus%22%3A%22Available%22%7D%7D%5D%7D&aura.context=%7B%22mode%22%3A%22PROD%22%2C%22fwuid%22%3A%22MDM0c01pMVUtd244bVVLc2VRYzQ2UWRkdk8xRWxIam5GeGw0LU1mRHRYQ3cyNDYuMTUuMi0zLjAuNA%22%2C%22app%22%3A%22siteforce%3AcommunityApp%22%2C%22loaded%22%3A%7B%22APPLICATION%40markup%3A%2F%2Fsiteforce%3AcommunityApp%22%3A%22qK6ciwKeawCz0GENX4N0eg%22%2C%22COMPONENT%40markup%3A%2F%2Finstrumentation%3Ao11ySecondaryLoader%22%3A%22Cpu-nBuFEwwbtqFxYd7Qhw%22%7D%2C%22dn%22%3A%5B%5D%2C%22globals%22%3A%7B%7D%2C%22uad%22%3Afalse%7D&aura.pageURI=%2Fs%2Fproperty-search&aura.token=' . $this->token,

            CURLOPT_HTTPHEADER => array(
                'authority: crmx.azizidevelopments.com',
                'accept: */*',
                'accept-language: en-US,en;q=0.9,ur;q=0.8',
                'content-type: application/x-www-form-urlencoded; charset=UTF-8',
                'cookie:' . $this->cookie,
                'origin: https://crmx.azizidevelopments.com',
                'referer: https://crmx.azizidevelopments.com/s/property-search',
                'sec-ch-ua: "Chromium";v="118", "Google Chrome";v="118", "Not=A?Brand";v="99"',
                'sec-ch-ua-mobile: ?0',
                'sec-ch-ua-platform: "macOS"',
                'sec-fetch-dest: empty',
                'sec-fetch-mode: cors',
                'sec-fetch-site: same-origin',
                'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/118.0.0.0 Safari/537.36',
                'x-sfdc-page-scope-id: 5a0acca6-f977-4f58-8159-d4c0b0733d76',
                'x-sfdc-request-id: 34636500000cc77bc4',
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $data = json_decode($response, true);

        $recordsData = $data['actions'][0]['returnValue']['prodList'];
        $count = 1;
        $allListings = [];
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
                        'property_type' => $record['Unit_Type__c'] ?? null,
                        'status' => $record['Unit_Status__c'] ?? null,
                        'bed_id' => $record['Unit_Type__c'] ?? null,
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
                $allListings[] = $record['Id'];
                $count++;
                // info('#' . $count . ' | Azizi Property Fetched. ID: ' . $record['Id']);
                // info after 100 count
                if ($count % 100 == 0) {
                    info('#' . $count . ' | Azizi Property Fetched. ID: ' . $record['Id']);
                }

            } catch (\Throwable $th) {
                DB::rollback();
                sleep(4);
                info($th);
                continue;
            }

        }
        info('Azizi Property Fetch Job Ended. Fetched ' . $count . ' Records');

        $properties = OffplanProperty::where('developer_id', 48)->whereNotIn('unique_id', $allListings)->get();
        foreach ($properties as $key => $property) {
            if ($property->property_id) {
                $actual_property = Property::find($property->property_id);
                if ($actual_property) {
                    if ($actual_property->is_live) {
                        $actual_property->is_live = 0;
                        $actual_property->publish_status = 'UNPUBLISHED';
                        $actual_property->updated_at = now();
                        $actual_property->save();
                        PropertyNote::create([
                            "property_id" => $actual_property->id,
                            "note" => "This property is not available on Azizi anymore.",
                            'created_by' => 7,
                        ]);
                    }

                }
            }
        }
        info('Total Properties Fetched: ' . $count . ' IDs ' . json_encode($allListings));
        info($properties->count() . ' Properties from Azizi are Down Now.');

    }

    public function azizSingle($propertyID)
    {
        $originalPropertyID = $propertyID;
        $propertyID = substr($propertyID, 0, -3);
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
            // CURLOPT_POSTFIELDS => 'message=%7B%22actions%22%3A%5B%7B%22id%22%3A%22326%3Ba%22%2C%22descriptor%22%3A%22serviceComponent%3A%2F%2Fui.force.components.controllers.recordGlobalValueProvider.RecordGvpController%2FACTION%24getRecord%22%2C%22callingDescriptor%22%3A%22UNKNOWN%22%2C%22params%22%3A%7B%22recordDescriptor%22%3A%22' . $originalPropertyID . '.undefined.FULL.null.null.null.VIEW.true.null.null.null%22%7D%7D%5D%7D&aura.context=%7B%22mode%22%3A%22PROD%22%2C%22fwuid%22%3A%22MDM0c01pMVUtd244bVVLc2VRYzQ2UWRkdk8xRWxIam5GeGw0LU1mRHRYQ3cyNDYuMTUuMi0zLjAuNA%22%2C%22app%22%3A%22siteforce%3AcommunityApp%22%2C%22loaded%22%3A%7B%22APPLICATION%40markup%3A%2F%2Fsiteforce%3AcommunityApp%22%3A%22qK6ciwKeawCz0GENX4N0eg%22%2C%22COMPONENT%40markup%3A%2F%2Finstrumentation%3Ao11ySecondaryLoader%22%3A%22Cpu-nBuFEwwbtqFxYd7Qhw%22%7D%2C%22dn%22%3A%5B%5D%2C%22globals%22%3A%7B%7D%2C%22uad%22%3Afalse%7D&aura.pageURI=%2Fs%2Fdetail%2F' . $propertyID . '&aura.token=' . $this->token,
            CURLOPT_POSTFIELDS =>'message=%7B%22actions%22%3A%5B%7B%22id%22%3A%22356%3Ba%22%2C%22descriptor%22%3A%22serviceComponent%3A%2F%2Fui.force.components.controllers.recordGlobalValueProvider.RecordGvpController%2FACTION%24getRecord%22%2C%22callingDescriptor%22%3A%22UNKNOWN%22%2C%22params%22%3A%7B%22recordDescriptor%22%3A%22'. $originalPropertyID .'.undefined.FULL.null.null.null.VIEW.true.null.null.null%22%7D%7D%5D%7D&aura.context=%7B%22mode%22%3A%22PROD%22%2C%22fwuid%22%3A%22MDM0c01pMVUtd244bVVLc2VRYzQ2UWRkdk8xRWxIam5GeGw0LU1mRHRYQ3cyNDYuMTUuNS0zLjAuNA%22%2C%22app%22%3A%22siteforce%3AcommunityApp%22%2C%22loaded%22%3A%7B%22APPLICATION%40markup%3A%2F%2Fsiteforce%3AcommunityApp%22%3A%22ncylk18YyR_zaIjevsSmBw%22%2C%22COMPONENT%40markup%3A%2F%2Finstrumentation%3Ao11ySecondaryLoader%22%3A%22iVoI_RYCX4m4O5loBTnQfA%22%7D%2C%22dn%22%3A%5B%5D%2C%22globals%22%3A%7B%7D%2C%22uad%22%3Afalse%7D&aura.pageURI=%2Fs%2Fdetail%2F'. $propertyID .'&aura.token='. $this->token,
            CURLOPT_HTTPHEADER => array(
                'authority: crmx.azizidevelopments.com',
                'accept: */*',
                'accept-language: en-US,en;q=0.9,ur;q=0.8',
                'content-type: application/x-www-form-urlencoded; charset=UTF-8',
                'cookie:' . $this->cookie,
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
        $pro = OffplanProperty::updateOrCreate(
            [
                'unique_id' => $originalPropertyID,
            ],
            [

                'status' => $recordsData['Admin_Status__c']['value'],
                'building_name' => $recordsData['BuildingNameNew__c']['value'],
                'name' => $recordsData['CreatedBy']['displayValue'] ?? null,
                'description' => $recordsData['Description']['value'] ?? null,
                'unit_no' => $recordsData['ProductCode']['value'] ?? null,
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

                'property_type' => $recordsData['Unit_Type__c']['displayValue'] ?? null,
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

            '2 BEDROOM-DUPLEX' => 'Apartment',
            '2 BEDROOM + MAID' => 'Apartment',
            '2 BEDROOM' => 'Apartment',

            '1 BEDROOM-DUPLEX' => 'Apartment',
            '1 BEDROOM' => 'Apartment',
            '1 BEDROOM + MAID' => 'Apartment',

        ];
        return $types[$type];
    }

    public function handle2()
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://crmx.azizidevelopments.com/s/sfsites/aura?r=7&other.SelectProperty.searchPropertiesApex=1',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'message=%7B%22actions%22%3A%5B%7B%22id%22%3A%22358%3Ba%22%2C%22descriptor%22%3A%22apex%3A%2F%2FSelectPropertyController%2FACTION%24searchPropertiesApex%22%2C%22callingDescriptor%22%3A%22markup%3A%2F%2Fc%3ASelectPropertyPage%22%2C%22params%22%3A%7B%22pageSize%22%3A%22100%22%2C%22pageNumber%22%3A%221%22%2C%22sortby%22%3A%22Name%22%2C%22sortOrder%22%3A%22asc%22%2C%22selectedFilters%22%3A%5B%5D%2C%22selectedFiltersJson%22%3A%22%7B%5C%22Unit_Type1__c%5C%22%3A%5C%22Residential%5C%22%2C%5C%22Unit_Status__c%5C%22%3A%5C%22Available%5C%22%7D%22%2C%22unitStatus%22%3A%22Available%22%7D%7D%5D%7D&aura.context=%7B%22mode%22%3A%22PROD%22%2C%22fwuid%22%3A%22MDM0c01pMVUtd244bVVLc2VRYzQ2UWRkdk8xRWxIam5GeGw0LU1mRHRYQ3cyNDYuMTUuMi0zLjAuNA%22%2C%22app%22%3A%22siteforce%3AcommunityApp%22%2C%22loaded%22%3A%7B%22APPLICATION%40markup%3A%2F%2Fsiteforce%3AcommunityApp%22%3A%22qK6ciwKeawCz0GENX4N0eg%22%2C%22COMPONENT%40markup%3A%2F%2Finstrumentation%3Ao11ySecondaryLoader%22%3A%22Cpu-nBuFEwwbtqFxYd7Qhw%22%7D%2C%22dn%22%3A%5B%5D%2C%22globals%22%3A%7B%7D%2C%22uad%22%3Afalse%7D&aura.pageURI=%2Fs%2Fproperty-search&aura.token=eyJub25jZSI6ImZpQlVSbU0xWFNxWjJaUlRSXzZWWFhLY0RGOXYySjdIRVdHWkNiLWlYQmtcdTAwM2QiLCJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiIsImtpZCI6IntcInRcIjpcIjAwRDA5MDAwMDAwR3lYWVwiLFwidlwiOlwiMDJHMDkwMDAwMDAwWXFYXCIsXCJhXCI6XCJjYWltYW5zaWduZXJcIn0iLCJjcml0IjpbImlhdCJdLCJpYXQiOjE2OTc3ODI3MTg3NzYsImV4cCI6MH0%3D..7MhEQnBoWVXsMyAzx9jjW_veSMWndMeVLXiZicYXd_Y%3D',
            CURLOPT_HTTPHEADER => array(
                'authority: crmx.azizidevelopments.com',
                'accept: */*',
                'accept-language: en-US,en;q=0.9,ur;q=0.8',
                'content-type: application/x-www-form-urlencoded; charset=UTF-8',
                'cookie: renderCtx=%7B%22pageId%22%3A%224c26d0f2-6230-4698-bbb9-b674095d0095%22%2C%22schema%22%3A%22Published%22%2C%22viewType%22%3A%22Published%22%2C%22brandingSetId%22%3A%2245f6cfa8-d465-48cb-911d-e261fdaa2a08%22%2C%22audienceIds%22%3A%22%22%7D; CookieConsentPolicy=0:1; LSKey-c$CookieConsentPolicy=0:1; oinfo=c3RhdHVzPUFDVElWRSZ0eXBlPTImb2lkPTAwRDF0MDAwMDAwdXRSUQ==; autocomplete=1; oid=00D1t000000utRQ; sid_Client=t000003cFqtt000000utRQ; clientSrc=94.206.206.53; inst=APP_09; _gcl_au=1.1.464504723.1697011372; _hjSessionUser_3074064=eyJpZCI6IjcwYmI2MmRiLTk5MmEtNTFlYS1iNzc1LTA2NDYwODIyZGRkYyIsImNyZWF0ZWQiOjE2OTcwMTEzNzI3NzUsImV4aXN0aW5nIjp0cnVlfQ==; _ym_uid=1697011373545799100; _ym_d=1697011373; _gac_UA-56309380-1=1.1697636741.Cj0KCQjwhL6pBhDjARIsAGx8D59ocuP1JfTvIohEllCJVORUXp2y7pLfOaWHD2o9kTfno5NwCqqo33saAvI0EALw_wcB; _ga=GA1.1.875563513.1697011372; _gcl_aw=GCL.1697636745.Cj0KCQjwhL6pBhDjARIsAGx8D59ocuP1JfTvIohEllCJVORUXp2y7pLfOaWHD2o9kTfno5NwCqqo33saAvI0EALw_wcB; _ga_5WV8GZDYT6=GS1.1.1697636741.5.1.1697638131.60.0.0; sfdc-stream=!bdDg2t1dsI8kHTP2YA1CGBFB5udndrvngFm4I/9d4nTxsnHfPQqXd++4RjEv2SLGMAPDr3muBunYFA==; sid=00D1t000000utRQ!AQEAQMtPFg7QA5EQ5S7LFfLdIZnVqoPrhxIJ3aZwMLZoeZSylKHjftQJYaS2aezi7Bi3O2un2nb3SeM1QGoz59lAAcaUFoyD',
                'origin: https://crmx.azizidevelopments.com',
                'referer: https://crmx.azizidevelopments.com/s/property-search',
                'sec-ch-ua: "Chromium";v="118", "Google Chrome";v="118", "Not=A?Brand";v="99"',
                'sec-ch-ua-mobile: ?0',
                'sec-ch-ua-platform: "macOS"',
                'sec-fetch-dest: empty',
                'sec-fetch-mode: cors',
                'sec-fetch-site: same-origin',
                'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/118.0.0.0 Safari/537.36',
                'x-sfdc-page-scope-id: 5a0acca6-f977-4f58-8159-d4c0b0733d76',
                'x-sfdc-request-id: 34636500000cc77bc4',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;

    }
    public function dummy()
    {

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
            CURLOPT_POSTFIELDS => 'message=%7B%22actions%22%3A%5B%7B%22id%22%3A%22404%3Ba%22%2C%22descriptor%22%3A%22apex%3A%2F%2FSelectPropertyController%2FACTION%24searchPropertiesApex%22%2C%22callingDescriptor%22%3A%22markup%3A%2F%2Fc%3ASelectPropertyPage%22%2C%22params%22%3A%7B%22pageSize%22%3A%22100%22%2C%22pageNumber%22%3A%221%22%2C%22sortby%22%3A%22Name%22%2C%22sortOrder%22%3A%22asc%22%2C%22selectedFilters%22%3A%5B%5D%2C%22selectedFiltersJson%22%3A%22%7B%5C%22Unit_Type1__c%5C%22%3A%5C%22Residential%5C%22%2C%5C%22Unit_Status__c%5C%22%3A%5C%22Available%5C%22%7D%22%2C%22unitStatus%22%3A%22Available%22%7D%7D%5D%7D&aura.context=%7B%22mode%22%3A%22PROD%22%2C%22fwuid%22%3A%22MDM0c01pMVUtd244bVVLc2VRYzQ2UWRkdk8xRWxIam5GeGw0LU1mRHRYQ3cyNDYuMTUuNS0zLjAuNA%22%2C%22app%22%3A%22siteforce%3AcommunityApp%22%2C%22loaded%22%3A%7B%22APPLICATION%40markup%3A%2F%2Fsiteforce%3AcommunityApp%22%3A%22ncylk18YyR_zaIjevsSmBw%22%2C%22COMPONENT%40markup%3A%2F%2Finstrumentation%3Ao11ySecondaryLoader%22%3A%22iVoI_RYCX4m4O5loBTnQfA%22%7D%2C%22dn%22%3A%5B%5D%2C%22globals%22%3A%7B%7D%2C%22uad%22%3Afalse%7D&aura.pageURI=%2Fs%2Fproperty-search&aura.token=eyJub25jZSI6Ik42TENndXRlTVNMa2JVQ2RvVEhna1F2UldMWkx4dmR5NzFpUHZlMVRYeGdcdTAwM2QiLCJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiIsImtpZCI6IntcInRcIjpcIjAwRFRmMDAwMDAwMDAwMVwiLFwidlwiOlwiMDJHVGYwMDAwMDAwMDFkXCIsXCJhXCI6XCJjYWltYW5zaWduZXJcIn0iLCJjcml0IjpbImlhdCJdLCJpYXQiOjE3MDEzMjMwODIzOTksImV4cCI6MH0%3D..-kyLxY1mYajJ-bYRXalQUiFefWuPXZK-_4tmxQYkkkk%3D',
            CURLOPT_HTTPHEADER => array(
                'authority: crmx.azizidevelopments.com',
                'accept: */*',
                'accept-language: en-US,en;q=0.9,ur;q=0.8',
                'content-type: application/x-www-form-urlencoded; charset=UTF-8',
                'cookie: renderCtx=%7B%22pageId%22%3A%224c26d0f2-6230-4698-bbb9-b674095d0095%22%2C%22schema%22%3A%22Published%22%2C%22viewType%22%3A%22Published%22%2C%22brandingSetId%22%3A%2245f6cfa8-d465-48cb-911d-e261fdaa2a08%22%2C%22audienceIds%22%3A%22%22%7D; CookieConsentPolicy=0:1; LSKey-c$CookieConsentPolicy=0:1; oinfo=c3RhdHVzPUFDVElWRSZ0eXBlPTImb2lkPTAwRDF0MDAwMDAwdXRSUQ==; autocomplete=1; oid=00D1t000000utRQ; _gcl_au=1.1.464504723.1697011372; _hjSessionUser_3074064=eyJpZCI6IjcwYmI2MmRiLTk5MmEtNTFlYS1iNzc1LTA2NDYwODIyZGRkYyIsImNyZWF0ZWQiOjE2OTcwMTEzNzI3NzUsImV4aXN0aW5nIjp0cnVlfQ==; _ym_uid=1697011373545799100; _ym_d=1697011373; _gac_UA-56309380-1=1.1697636741.Cj0KCQjwhL6pBhDjARIsAGx8D59ocuP1JfTvIohEllCJVORUXp2y7pLfOaWHD2o9kTfno5NwCqqo33saAvI0EALw_wcB; _gcl_aw=GCL.1697636745.Cj0KCQjwhL6pBhDjARIsAGx8D59ocuP1JfTvIohEllCJVORUXp2y7pLfOaWHD2o9kTfno5NwCqqo33saAvI0EALw_wcB; _ga=GA1.2.875563513.1697011372; _ga_5WV8GZDYT6=GS1.1.1700636583.7.0.1700636583.60.0.0; sid_Client=t000003cFqtt000000utRQ; clientSrc=94.206.206.53; inst=APP_Tf; sid=00D1t000000utRQ!AQEAQPR4AGbcd7A2WUa0SmNj28rhU0EqlTR3hsX9hfuOgft3IDwllIKD9ax2qs7pFv_nSEwEDfU6owWkJMO1R05LoJo7YSG2',
                'origin: https://crmx.azizidevelopments.com',
                'referer: https://crmx.azizidevelopments.com/s/property-search',
                'sec-ch-ua: "Google Chrome";v="119", "Chromium";v="119", "Not?A_Brand";v="24"',
                'sec-ch-ua-mobile: ?0',
                'sec-ch-ua-platform: "macOS"',
                'sec-fetch-dest: empty',
                'sec-fetch-mode: cors',
                'sec-fetch-site: same-origin',
                'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36',
                'x-sfdc-page-scope-id: 2821075c-b170-443c-81c3-886c324a18d9',
                'x-sfdc-request-id: 119970000006f5b72b',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;

    }

}
