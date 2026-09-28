<?php

namespace App\Jobs;

use App\Models\OffplanProperty;
use App\Models\OffplanPropertyMedia;
use App\Models\Property;
use App\Models\PropertyNote;
use DB;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class EmaarPropertyFetchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 7200;
    public $tries = 3;
    private $cookie;
    private $token;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->cookie = 'renderCtx=%7B%22pageId%22%3A%224c96e070-3bce-4179-bac7-906b556e0023%22%2C%22schema%22%3A%22Published%22%2C%22viewType%22%3A%22Published%22%2C%22brandingSetId%22%3A%220f307901-5fd9-4796-a9d3-48c443bac15d%22%2C%22audienceIds%22%3A%22%22%7D; CookieConsentPolicy=0:1; LSKey-c$CookieConsentPolicy=0:1; BrowserId=JBAgrGKgEe6FUgkV9xSusg; BrowserId_sec=JBAgrGKgEe6FUgkV9xSusg; oinfo=c3RhdHVzPUFDVElWRSZ0eXBlPTYmb2lkPTAwRDU4MDAwMDAwTDRXUg==; autocomplete=1; oid=00D58000000L4WR; sid_Client=H0000066sOn8000000L4WR; clientSrc=94.206.206.53; inst=APP_4L; ak_bmsc=148E2DE95270FCDB084B9AE210E770B8~000000000000000000000000000000~YAAQJ/ttaMHp10eLAQAAgjysTBVjlviSUj9RyVF9XVBfs5Y5mmoSmsPKtx12JKdL2ukG5IUT2JW+r3UILFwoRY0YTh6lh7f0o8HhKSRRbJWtlwKT5cKGrNr5tyPwTyZ6tkhkd+Hc3MAQeJj20mUWlVE9koGSrx92MP/Y1Mx4/6vnmhHOu4iglX1mT4hs2ozdmy/FVmtZhhXmt53TnErknHKuc4reI/tElV2AMSZkxnpWKPFBHE9iZleTfvmfKnMnrRTnPern7up46L5lOXCnpyaGLHBZ58d9rh2Q3FSB0/tO3lT3syzTpQv7lNW02IRuXNNaCPusN1jIGLxSOcJRv/q5GenazzVc7krLU7G/Xlwfl8bHzzMkAF2U5Nr6Ya22599adNETPORHGp8=; sid=00D58000000L4WR!ARwAQDDXZGOUaeOYAmejJpLwh8afC4zZ4R58K9dE3BAxtfI_PgeM_8ZlKpiw9fpaJh_MYB2ES3YNOQYgl3hNSVxtTYXAsfzz; sfdc-stream=!2zUyVI+ZTtGLZOZbtnw4nuEojYKfr69Jn01rsXg0jh2FW2cVxVZOJ0ifcjFr4tdbICQnIR8u7j32ew==; bm_sv=FB2E3EF3D51D546ED2A9F54724D8A244~YAAQFfttaDPrlziLAQAAna+wTBVuzevn1G+xdsJ4sWVaaPYu1aB0FUBWQYfcACuoj2W+9QqaFBonpT+1aWzPXE7r9yOPh96cBVpVpjuqU4epJCLmx084rcvtP+PQLPDDsuvp2zi7xHjPUUaTv+R8Em5HmbnNv5hjzjRxe7tESr5+kLoMAxahBsfB/AaclhiEvEzo74bimjVUuTB9UipqvHrnMdSHUBRRwqvg5WsxIsJ8VgP6ckZuNjuohywQze/F/1k=~1';
        $this->token = 'eyJub25jZSI6InJGVXotU0dnYmtsd09oSm9RcjJuV21HT3JGQmE4RDhTTThRSThlMnN6S0lcdTAwM2QiLCJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiIsImtpZCI6IntcInRcIjpcIjAwRDRMMDAwMDAwQ21UYlwiLFwidlwiOlwiMDJHNEwwMDAwMDAwWXFYXCIsXCJhXCI6XCJjYWltYW5zaWduZXJcIn0iLCJjcml0IjpbImlhdCJdLCJpYXQiOjE2OTc3OTg3Mjg1MjYsImV4cCI6MH0%3D..NAz0GYi1iVenXgyO37iAx5TXECtXFAXsd0P_uPLtI_o%3D';

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        info('Emaar Properties Fetch Started');
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://emaarsales.my.site.com/customers/s/sfsites/aura?r=3&aura.ApexAction.execute=1',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'message=%7B%22actions%22%3A%5B%7B%22id%22%3A%22122%3Ba%22%2C%22descriptor%22%3A%22aura%3A%2F%2FApexActionController%2FACTION%24execute%22%2C%22callingDescriptor%22%3A%22UNKNOWN%22%2C%22params%22%3A%7B%22namespace%22%3A%22%22%2C%22classname%22%3A%22MYEmaarPortal_SearchController%22%2C%22method%22%3A%22getWrapperData%22%2C%22params%22%3A%7B%22strType%22%3A%22%22%2C%22propertyInventoryId%22%3A%22%22%2C%22searchKey%22%3A%22%22%2C%22mapOfFilters%22%3Anull%7D%2C%22cacheable%22%3Afalse%2C%22isContinuation%22%3Afalse%7D%7D%5D%7D&aura.context=%7B%22mode%22%3A%22PROD%22%2C%22fwuid%22%3A%22MDM0c01pMVUtd244bVVLc2VRYzQ2UWRkdk8xRWxIam5GeGw0LU1mRHRYQ3cyNDYuMTUuMi0zLjAuNA%22%2C%22app%22%3A%22siteforce%3AcommunityApp%22%2C%22loaded%22%3A%7B%22APPLICATION%40markup%3A%2F%2Fsiteforce%3AcommunityApp%22%3A%22qK6ciwKeawCz0GENX4N0eg%22%2C%22COMPONENT%40markup%3A%2F%2Finstrumentation%3Ao11ySecondaryLoader%22%3A%22Cpu-nBuFEwwbtqFxYd7Qhw%22%7D%2C%22dn%22%3A%5B%5D%2C%22globals%22%3A%7B%7D%2C%22uad%22%3Afalse%7D&aura.pageURI=%2Fcustomers%2Fs%2F%3Fc__pageName%3DListing%26c__detailView%3Dfalse&aura.token=' . $this->token,
            CURLOPT_HTTPHEADER => array(
                'authority: emaarsales.my.site.com',
                'accept: */*',
                'accept-language: en-US,en;q=0.9,ur;q=0.8',
                'content-type: application/x-www-form-urlencoded; charset=UTF-8',
                'cookie:' . $this->cookie,
                'origin: https://emaarsales.my.site.com',
                'referer: https://emaarsales.my.site.com/customers/s/?c__pageName=Listing&c__detailView=false',
                'sec-ch-ua: "Google Chrome";v="117", "Not;A=Brand";v="8", "Chromium";v="117"',
                'sec-ch-ua-mobile: ?0',
                'sec-ch-ua-platform: "macOS"',
                'sec-fetch-dest: empty',
                'sec-fetch-mode: cors',
                'sec-fetch-site: same-origin',
                'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/117.0.0.0 Safari/537.36',
                'x-sfdc-lds-endpoints: ApexActionController.execute:MYEmaarPortal_SearchController.getWrapperData',
                'x-sfdc-page-cache: 6914686046b792a7',
                'x-sfdc-page-scope-id: 123cf61c-7f31-40e9-a7ad-0a6aeb0dbabc',
                'x-sfdc-request-id: 431350000040c06f31',
            ),
        ));

        $response = curl_exec($curl);
        info($response);
        curl_close($curl);
        $data = json_decode($response, true);
        info($data);
        try {
            $bedrooms = $data['actions'][0]['returnValue']['returnValue']['bedrooms'] ?? null;
            $communities = $data['actions'][0]['returnValue']['returnValue']['communities'] ?? null;
            $communities = $data['actions'][0]['returnValue']['returnValue']['communities'] ?? null;
            $communities = $data['actions'][0]['returnValue']['returnValue']['communities'] ?? null;
            $communitywithImages = $data['actions'][0]['returnValue']['returnValue']['communitywithImages'] ?? null;
            $communityWithProperties = $data['actions'][0]['returnValue']['returnValue']['communityWithProperties'] ?? null; //array
            $lstTopProperties = $data['actions'][0]['returnValue']['returnValue']['lstTopProperties'] ?? null; //array
            $lstUnitData = $data['actions'][0]['returnValue']['returnValue']['lstUnitData'] ?? null; //array
            $properties = $data['actions'][0]['returnValue']['returnValue']['properties'] ?? null; //array
            $propertywithCommunityDetails = $data['actions'][0]['returnValue']['returnValue']['propertywithCommunityDetails'] ?? null; //array
            $propertywithImages = $data['actions'][0]['returnValue']['returnValue']['propertywithImages'] ?? null; //array
        } catch (\Throwable $th) {
            info($th);
            throw $th;
        }

        $recordsData = $data['actions'][0]['returnValue']['returnValue']['recordsData']; //array
        $count = 0;

        try {
            $allListings = [];
            foreach ($recordsData as $key => $recordData) {
                foreach ($recordData as $key => $record) {
                    try {
                        $allListings[] = $record['Id'];
                        DB::beginTransaction();
                        $pro = OffplanProperty::updateOrCreate(
                            [
                                'unique_id' => $record['Id'],
                            ],
                            [
                                'title' => $record['Location_Code__c'],
                                'project_name' => $record['Project_Name__c'],
                                'building_location' => $record['Location__c'],
                                'price' => $record['Selling_Price_Incl_VAT__c'],
                                'bed_id' => $record['No_of_Bedrooms__c'] ?? null,
                                'bath_id' => null,
                                'total_area' => $record['Total_Area__c'],
                                'property_type' => $record['Type__c'],
                                'status' => $record['Property_Status__c'],
                                'community_name' => $record['Community__c'],
                                'unit_no' => $record['Unit_Position__c'] ?? null,
                                'recently_added' => $record['Recently_Added__c'] ?? false,
                                'will_delete' => 1,
                            ]
                        );
                        $building_name = $record['Building_Location__r']['Project_Name__c'];
                        $location_name = $record['Location__r']['Building__c'];
                        $property_name = $record['Property__r']['Property_Name__c'];

                        $pro->location_data = [
                            'building' => $building_name,
                            'location' => $location_name,
                            'property' => $property_name,
                        ];

                        $pro->save();
                        $this->emaarSingle($record['Id']);
                        $count++;
                        DB::commit();
                        info('Emaar Property Fetched: ' . $count . ' ID: ' . $record['Id']);
                        sleep(5);
                    } catch (\Throwable $th) {
                        info($th);
                        continue;
                    }
                }
            }

            $properties = OffplanProperty::where('developer_id', 1)->whereNotIn('unique_id', $allListings)->get();
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
                                "note" => "This property is not available on Emaar anymore.",
                                'created_by' => 7,
                            ]);
                        }

                    }
                }
            }
            info('Total Properties Fetched: ' . $count . ' IDs ' . json_encode($allListings));

            info($properties->count() . ' Properties from Emaar are Down Now.');
            // dispatch(new AziziPropertyFetchJob());
            return $count;
        } catch (\Throwable $th) {
            info($th);
            throw $th;
        }
    }
    public function emaarSingle($propertyId)
    {

        $curl = curl_init();
        $url = 'https://emaarsales.my.site.com/customers/s/sfsites/aura?r=9&aura.ApexAction.execute=1&propertyInventoryId=' . $propertyId;
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'message=%7B%22actions%22%3A%5B%7B%22id%22%3A%22122%3Ba%22%2C%22descriptor%22%3A%22aura%3A%2F%2FApexActionController%2FACTION%24execute%22%2C%22callingDescriptor%22%3A%22UNKNOWN%22%2C%22params%22%3A%7B%22namespace%22%3A%22%22%2C%22classname%22%3A%22MYEmaarPortal_SearchController%22%2C%22method%22%3A%22getWrapperData%22%2C%22params%22%3A%7B%22strType%22%3A%22%22%2C%22propertyInventoryId%22%3A%22%22%2C%22searchKey%22%3A%22%22%2C%22mapOfFilters%22%3Anull%7D%2C%22cacheable%22%3Afalse%2C%22isContinuation%22%3Afalse%7D%7D%5D%7D&aura.context=%7B%22mode%22%3A%22PROD%22%2C%22fwuid%22%3A%22MDM0c01pMVUtd244bVVLc2VRYzQ2UWRkdk8xRWxIam5GeGw0LU1mRHRYQ3cyNDYuMTUuMi0zLjAuNA%22%2C%22app%22%3A%22siteforce%3AcommunityApp%22%2C%22loaded%22%3A%7B%22APPLICATION%40markup%3A%2F%2Fsiteforce%3AcommunityApp%22%3A%22qK6ciwKeawCz0GENX4N0eg%22%2C%22COMPONENT%40markup%3A%2F%2Finstrumentation%3Ao11ySecondaryLoader%22%3A%22Cpu-nBuFEwwbtqFxYd7Qhw%22%7D%2C%22dn%22%3A%5B%5D%2C%22globals%22%3A%7B%7D%2C%22uad%22%3Afalse%7D&aura.pageURI=%2Fcustomers%2Fs%2F%3Fc__pageName%3DListing%26c__detailView%3Dfalse&aura.token=' . $this->token,
            CURLOPT_HTTPHEADER => array(
                'authority: emaarsales.my.site.com',
                'accept: */*',
                'accept-language: en-US,en;q=0.9,ur;q=0.8',
                'content-type: application/x-www-form-urlencoded; charset=UTF-8',
                'cookie:' . $this->cookie,
                'origin: https://emaarsales.my.site.com',
                'referer: https://emaarsales.my.site.com/customers/s/?c__pageName=Listing&c__detailView=false',
                'sec-ch-ua: "Google Chrome";v="117", "Not;A=Brand";v="8", "Chromium";v="117"',
                'sec-ch-ua-mobile: ?0',
                'sec-ch-ua-platform: "macOS"',
                'sec-fetch-dest: empty',
                'sec-fetch-mode: cors',
                'sec-fetch-site: same-origin',
                'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/117.0.0.0 Safari/537.36',
                'x-sfdc-lds-endpoints: ApexActionController.execute:MYEmaarPortal_SearchController.getWrapperData',
                'x-sfdc-page-cache: 6914686046b792a7',
                'x-sfdc-page-scope-id: 123cf61c-7f31-40e9-a7ad-0a6aeb0dbabc',
                'x-sfdc-request-id: 431350000040c06f31',
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $data = json_decode($response, true);
        if (isset($data['actions'][0]['returnValue']['returnValue']['details']) && !empty($data['actions'][0]['returnValue']['returnValue']['details'])) {
            $details = $data['actions'][0]['returnValue']['returnValue']['details'];

            $prop = OffplanProperty::updateOrCreate(
                [
                    'unique_id' => $details['id'],
                ],
                [
                    'developer_id' => 1,
                    'address_details' => $details['addressDetails'] ?? null,
                    'bath_id' => $details['bathrooms'] ?? null,
                    'bed_id' => $details['bedrooms'] ?? null,
                    'booking_status' => $details['bookingStatus'] ?? null,
                    'building_name' => $details['buildingname'] ?? null,
                    'community_name' => $details['communityName'] ?? null,
                    'land_registration_fee' => $details['landregistrationfee'] ?? null,
                    'location_code' => $details['locationcode'] ?? null,
                    'unit_no' => $details['locationcode'] ?? null,
                    'location_id' => $details['locationid'] ?? null,
                    'name' => $details['name'] ?? null,
                    'parking' => $details['parking'] ?? null,
                    'plot_size' => $details['plotsqft'] ?? null,
                    'property_name' => $details['propertyname'] ?? null,
                    'property_type' => $details['propertytype'] ?? null,
                    'property_usage' => $details['propertyusage'] ?? null,
                    'selling_price' => $details['sellingprice'] ?? null,

                    'total_amount' => $details['totalamount'] ?? null,
                    'total_area' => $details['totalarea'] ?? null,

                    'recently_added' => false,
                    'will_delete' => 1,
                ]
            );

            // $gallery = $data['actions'][0]['returnValue']['returnValue']['emunites']['lstGallery'];
            // $paymentplans = $data['actions'][0]['returnValue']['returnValue']['paymentterms'];
            // $propertydocuments = $data['actions'][0]['returnValue']['returnValue']['propertydocuments'];

            $already = OffplanPropertyMedia::where('offplan_property_id', $prop->id)->get();
            if (count($already) == 0) {
                if (isset($data['actions'][0]['returnValue']['returnValue']['emunites']['lstGallery'])) {
                    $gallery = $data['actions'][0]['returnValue']['returnValue']['emunites']['lstGallery'];
                    foreach ($gallery as $key => $image) {
                        $media = new OffplanPropertyMedia();
                        $media->offplan_property_id = $prop->id;
                        $media->type = 'image';
                        $media->slug_to_map = $image['Slug_To_Map__c'];
                        $media->is_vr_enabled = $image['IS_VR_Enabled__c'];
                        $media->path = $image['Carousel__c'];
                        $media->save();
                    }
                }

                if (isset($data['actions'][0]['returnValue']['returnValue']['propertydocuments'])) {
                    $propertydocuments = $data['actions'][0]['returnValue']['returnValue']['propertydocuments'];
                    foreach ($propertydocuments as $key => $doc) {
                        $media = new OffplanPropertyMedia();
                        $media->offplan_property_id = $prop->id;
                        $media->type = 'floorplan';
                        $media->slug_to_map = $doc['title'];
                        $media->is_vr_enabled = 0;
                        $media->path = $doc['link'];
                        $media->save();
                    }}

                $description = '';
                $just_description = '';
                if (isset($data['actions'][0]['returnValue']['returnValue']['emunites']['lstCommunityContent'])) {
                    $descrp_content = $data['actions'][0]['returnValue']['returnValue']['emunites']['lstCommunityContent'];
                    foreach ($descrp_content as $key => $image) {
                        $description = $description . $image['Content__c'] . ' ';
                        $just_description = $just_description . $image['Content__c'] . ' ';
                    }
                }
                $paymentplans = [];
                if (isset($data['actions'][0]['returnValue']['returnValue']['paymentterms'])) {
                    $paymentplans = $data['actions'][0]['returnValue']['returnValue']['paymentterms'];
                    foreach ($paymentplans as $key => $plans) {
                        $description = $description . '<br>' . ' Installment Plan: ' . $plans['Installment__c'] . ' Percentage ' . $plans['cm_Net_Percent__c'] . ' Amount ' . $plans['cm_Net_Amount__c'] . '<br>';

                        $paymentplans[$key]['Installment__c'] = ' Installment Plan: ' . $plans['Installment__c'] . ' Percentage ' . $plans['cm_Net_Percent__c'] . ' Amount ' . $plans['cm_Net_Amount__c'];
                    }
                }

                $amenities = [];
                if (isset($data['actions'][0]['returnValue']['returnValue']['emunites']['lstEmunities'])) {
                    $emunities = $data['actions'][0]['returnValue']['returnValue']['emunites']['lstEmunities'];
                    foreach ($emunities as $key => $em) {
                        $amenities[$em['Feature_Title__c']] = $em['Feature_Sub_Title__c'];
                    }
                }

                $prop->description = $description;
                $prop->data = [
                    'amenities' => $amenities,
                    'paymentplans' => $paymentplans,
                    'description' => $just_description,
                ];

                $prop->save();
            }
        }

        return $response;
    }

    public function getBedroomId($str)
    {
        $match = [
            '0' => null,
            'Studio' => 1,
            '1' => 2,
            '2' => 3,
            '3' => 4,
            '4' => 5,
            '5' => 6,
            '6' => 7,
            '7' => 8,
            '8' => 9,
            '9' => 10,
            '10' => 11,
        ];
        return $match[$str];
    }

}
