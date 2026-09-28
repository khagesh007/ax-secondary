<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\LeadBitrixResponse;

trait BitrixTrait
{
    public function fetchDealCustomListInBitrixById($id)
    {
        $url = 'https://crm.axcap.ae/rest/1/8cd3l0kbdlndvtqf/crm.deal.userfield.get?id='.urlencode($id);

        $response = Http::get($url);

        if ($response->successful()) {
            return $response->json()['result'];
        } else {
            // Log::error("Error fetching Bitrix location data. Status code: " . $response->status());
            return null;
        }
    }

    public function fetchLeadCustomListInBitrixById($id)
    {
        $url = 'https://crm.axcap.ae/rest/1/8cd3l0kbdlndvtqf/crm.lead.userfield.get?id='.urlencode($id);

        $response = Http::get($url);

        if ($response->successful()) {
            return $response->json()['result'];
        } else {
            // Log::error("Error fetching Bitrix location data. Status code: " . $response->status());
            return null;
        }
    }

    public function extractFieldIdFromBitrixCustomList(array $responseResult, $fieldValue)
    {
        $fieldId = null;
        
        if ($responseResult && isset($responseResult['LIST'])) {
            $fieldList = $responseResult['LIST'];
            $fieldId = $this->findFieldIdInList($fieldList, $fieldValue);
        }
        return $fieldId;
    }
    public function extractListFromBitrixThroughFieldName(array $data, $fieldName)
    {
        $filteredData = array_filter($data, function($item) use ($fieldName) {
            return isset($item['FIELD_NAME']) && $item['FIELD_NAME'] === $fieldName;
        });
        $filteredData = array_values($filteredData);
        return !empty($filteredData) ? $filteredData[0]['LIST'] : [];

    }
    protected function getFieldIdByName($fieldName, $fieldCode)
    {
        $list = $this->extractListFromBitrixThroughFieldName($this->fetchBitrixUserFieldList(), $fieldCode);
        return $this->findFieldIdInList($list, $fieldName);
    }

    public function findFieldIdInList(array $fieldList, $fieldValue)
    {
        if (is_array($fieldValue)) {
            $filteredValue = array_filter($fieldList, function ($item) use ($fieldValue) {
                return isset($item['VALUE']) && in_array($item['VALUE'], $fieldValue);
            });
            $ids = [];
            if (count($filteredValue) > 0) {
                foreach ($filteredValue as $value) {
                    
                    $ids[] = $value['ID'];
                }
                return $ids;
            }
        } else {
            $filteredValue = array_filter($fieldList, function ($item) use ($fieldValue) {
                return isset($item['VALUE']) && $item['VALUE'] === $fieldValue;
            });

            if (count($filteredValue) > 0) {
                $firstValue = reset($filteredValue);
                return $firstValue['ID'] ?? null;
            }
        }
        return null;
    }

    public function findFieldValueInList(array $fieldList, $fieldId)
    {
        $filteredValue = array_filter($fieldList, function ($item) use ($fieldId) {
            return isset($item['ID']) && $item['ID'] == $fieldId;
        });

        if (count($filteredValue) > 0) {
            $firstValue = reset($filteredValue);
            return $firstValue['VALUE'] ?? null;
        }

        return null;
    }

    public  function removeValueToCustomListInBitrixById ($bitrixListId, $fieldId)
    {
        $url = 'https://crm.axcap.ae/rest/1/8cd3l0kbdlndvtqf/crm.deal.userfield.update';
        $data = [
            "ID" => $bitrixListId,
            "fields" => [
                "LIST" => [
                    [
                        "ID" => $fieldId,
                        "DEL" => "Y"
                    ]
                ]
            ]
        ];
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->post($url, $data);
            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $exception) {
            Log::error([
                'error' => $exception->getMessage(),
                'line' => $exception->getLine(),
                'file' => $exception->getFile()
            ]);
        }
    }
    public function updateValueToCustomListInBitrixById($bitrixCustomListId, string $fieldValue, $fieldId)
    {
        $url = 'https://crm.axcap.ae/rest/1/8cd3l0kbdlndvtqf/crm.deal.userfield.update';
        $data = [
            "ID" => $bitrixCustomListId,
            "fields" => [
                "LIST" => [
                    [
                        "ID" => $fieldId,
                        "VALUE"=> $fieldValue,
                        "DEF"=> "N"
                    ]
                ]
            ]
        ];
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->post($url, $data);
            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $exception) {
            Log::error([
                'error' => $exception->getMessage(),
                'line' => $exception->getLine(),
                'file' => $exception->getFile()
            ]);
            return null;
        }
    }
    public function addValueToCustomListInBitrixById($bitrixCustomListId, string $fieldValue)
    {
        $url = 'https://crm.axcap.ae/rest/1/8cd3l0kbdlndvtqf/crm.deal.userfield.update';
        $data = [
            "ID" => $bitrixCustomListId,
            "fields" => [
                "LIST" => [
                    [
                        "VALUE" => $fieldValue,
                    ]
                ]
            ]
        ];
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->post($url, $data);
            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $exception) {
            Log::error([
                'error' => $exception->getMessage(),
                'line' => $exception->getLine(),
                'file' => $exception->getFile()
            ]);
            return null;
        }
    }

    public function getBitrixTypeId($dealType)
    {

        $dealTypes = [
            'Primary Sale' => 'SALE',
            'Rental' => "5",
            'Secondary Sale' => "6",
            'Off Plan Resale' => "6",
            'Rental Renewal' => "SERVICES",
            'Primary Sale Exclusive' => "10",
            'Outside Deal' => "2"
        ];
        return $dealTypes[$dealType] ?? "SALE";
    }
    public function getBitrixCategoryId($dealType)
    {

        $dealTypes = [
            'Primary Sale' => '0',
            'Rental' => "3",
            'Off Plan Resale' => "2",
            'Secondary Sale' => "2",
        ];
        return $dealTypes[$dealType] ?? "0";
    }
    public function getBitrixStageId($dealType, $dealStage)
    {

        $dealStages = [
            'Primary' => [
                'Initiated' => 'NEW',
                'Pending' => '3',
                'In Progress' => '3',
                'AML' => '3',
                'Approved' => 1,
                'Client Payment' => 1,
                'SPA Signed' => '2',
                'Developer Invoiced' => '4',
                'Comm. Partially Received' => '5',
                'Comm. Received' => '6',
                'Closed Deal' => 'WON'

            ],
            'Secondary Sale' => [
                'Initiated' => 'C2:NEW',
                'Pending ' => 'C2:PREPARATION',
                'In Progress' => 'C2:PREPARATION',
                'AML' => 'C2:PREPAYMENT_INVOICE',
                'Approved' => 'C2:EXECUTING',
                'Invoice Issued' => 'C2:2',
                'Comm. Partially Received' => 'C2:4',
                'Comm. Received' => 'C2:3',
                'Closed Deal' => "C2:WON",
            ],
            'Off Plan Resale' => [
                'Initiated' => 'C2:NEW',
                'Pending ' => 'C2:PREPARATION',
                'In Progress' => 'C2:PREPARATION',
                'AML' => 'C2:PREPAYMENT_INVOICE',
                'Approved' => 'C2:EXECUTING',
                'Invoice Issued' => 'C2:2',
                'Comm. Partially Received' => 'C2:4',
                'Comm. Received' => 'C2:3',
                'Closed Deal' => "C2:WON",
            ],

            'Rental' => [
                'Initiated' => 'C3:NEW',
                'Pending' => 'C3:PREPARATION',
                'In Progress' => 'C2:PREPARATION',
                'AML' => 'C2:PREPARATION',
                'Approved' => 'C3:PREPAYMENT_INVOICE',
                'Invoice Issued' => 'C3:FINAL_INVOICE',
                'Comm. Partially Received' => 'C3:2',
                'Comm. Received' => 'C3:1',
                'Closed Deal' => "C3:WON",

            ]

        ];
        return $dealStages[$dealType][$dealStage] ?? "3";
    }
    public function getBitrixSourceOfFundId($sourceOfFund)
    {
        if($sourceOfFund == 'Savings'){
            $sourceOfFund  = 'Savings / Deposits';
        }elseif($sourceOfFund == 'Salary') {
            $sourceOfFund  = 'Employment Income';
        }elseif($sourceOfFund == 'Inheritance') {
            $sourceOfFund  = 'Inheritance / Gifts';
        }elseif($sourceOfFund == 'Sale of Property or Investment'){
            $sourceOfFund = 'Investment';
        }
        $bitrixFundSourceListId = env('BITRIX_FUND_SOURCE_LIST_ID');
        $response = $this->fetchDealCustomListInBitrixById($bitrixFundSourceListId);
        if ($response) {
            $sourceId = $this->extractFieldIdFromBitrixCustomList($response, $sourceOfFund);
            if($sourceId){
                return $sourceId;
            }else{
                $sourceId = $this->extractFieldIdFromBitrixCustomList($response, 'Others');
                return $sourceId;
            }
        }
        return null;
    }
    public function getBitrixDealTypeId($sourceOfFund)
    {
        $bitrixFundSourceListId = env('BITRIX_TYPE_LIST_ID');
        $response = $this->fetchDealCustomListInBitrixById($bitrixFundSourceListId);
        if ($response) {
            $typeId = $this->extractFieldIdFromBitrixCustomList($response, $sourceOfFund);
            if($typeId){
                return $typeId;
            }else{
                $typeId = $this->extractFieldIdFromBitrixCustomList($response, 'Apartment');
                return $typeId;
            }
        }
        return null;
    }
    public function fetchBitrixLeadLanguageId($languages)
    {
        $bitrixLanguageListId = env('BITRIX_LEAD_LANGUAGE_LIST_ID');

        $response = $this->fetchLeadCustomListInBitrixById($bitrixLanguageListId);
       
        if ($response) {
            $languageId = $this->extractFieldIdFromBitrixCustomList($response, $languages);
            
            if($languageId){
                return $languageId;
            }
        }
        return null;
    }

    public function fetchBitrixLeadProjectValue($projectId)
    {
        $bitrixProjectListId = env('BITRIX_LEAD_PROJECT_LIST_ID', 529);

        $response = $this->fetchLeadCustomListInBitrixById($bitrixProjectListId);

        if ($response && isset($response['LIST'])) {
            $fieldList = $response['LIST'];
            $value = $this->findFieldValueInList($fieldList, $projectId);

            if ($value) {
                return $value;
            }
        }

        return null;
    }

    public function getBitrixLeadNationalityId($nationality)
    {
        $bitrixNationalityListId  = env('BITRIX_LEAD_NATIONALITY_LIST_ID');

        $response = $this->fetchLeadCustomListInBitrixById($bitrixNationalityListId);
        if ($response) {
            $nationlityId = $this->extractFieldIdFromBitrixCustomList($response, $nationality);
            if($nationlityId){
                return $nationlityId;
            }
            /*
            else{
                if(is_array($nationlityId)){
                    Log::channel('bitrix_alert')->info("Nationality(s) '" . implode(", ", $nationlityId) . "' not found in Bitrix");
                }else{
                    Log::channel('bitrix_alert')->info("Nationality $nationlityId not found in Bitrix");
                } 
            }
            */
        }
        return null;
    }
    public function getBitrixCountryId($country)
    {
        $bitrixCountryListId  = env('BITRIX_COUNTRY_LIST_ID');

        $response = $this->fetchDealCustomListInBitrixById($bitrixCountryListId);
        if ($response) {
            $countryId = $this->extractFieldIdFromBitrixCustomList($response, $country);
            if($countryId){
                return $countryId;
            }else{
                Log::channel('bitrix_alert')->info("Country not found in Bitrix");
            }
        }
        return null;
    }

    public function getBitrixBedId($bedName)
    {
        $bitrixbedsListId = env('BITRIX_BED_LIST_ID');
        $bedArray = explode(',', $bedName);
        $bedCount = $bedArray[0] ?? null;
        if($bedCount != 'Studio'){
            $bedCount = (int) $bedCount;
            if($bedCount == 7 || $bedCount > 7 ){
                $bedCount =  '7+';
            }
            $bedCount = strval($bedCount);
        }
        
        $response = $this->fetchDealCustomListInBitrixById($bitrixbedsListId);
        if ($response) {
            $bedId = $this->extractFieldIdFromBitrixCustomList($response, $bedCount);
            
            if($bedId){
                
                return $bedId;
            }
        }
        return null;
    }

    protected function getSourceFromBitrixLeads()
    {

        $response = Http::get('https://crm.axcap.ae/rest/1/8cd3l0kbdlndvtqf/crm.status.list');

        $data = $response->json();

        $leadSources = collect($data['result'])->where('ENTITY_ID', 'SOURCE')->pluck('NAME', 'STATUS_ID')->toArray();
        return $leadSources;
    }
    protected function fetchBitrixUserFieldList()
    {
        $url = 'https://crm.axcap.ae/rest/1/8cd3l0kbdlndvtqf/crm.deal.userfield.list';

        $response = Http::get($url);

        if ($response->successful()) {
            return $response->json()['result'];
        } else {
            Log::error("Error fetching Bitrix location data. Status code: " . $response->status());
            return null;
        }
    }
    public function fetchBitrixLeadGenderId($gender)
    {
        if($gender){
            $bitrixGenderListId = env('BITRIX_LEAD_GENDER_LIST_ID');

            $response = $this->fetchLeadCustomListInBitrixById($bitrixGenderListId);
            if ($response) {
                $genderId = $this->extractFieldIdFromBitrixCustomList($response, $gender);
                if($genderId){
                    return $genderId;
                }else{
                    Log::channel('bitrix_alert')->info("gender $gender not found in Bitrix");
                }
            }
            return null;
        }
        
        return null;
    }

    public function fetchBitrixLeadTypeId($type)
    {
        if($type){
            $bitrixTypeListId = env('BITRIX_LEAD_TYPE_LIST_ID');

            $response = $this->fetchLeadCustomListInBitrixById($bitrixTypeListId);
            if ($response) {
                $genderId = $this->extractFieldIdFromBitrixCustomList($response, $type);
                if($genderId){
                    return $genderId;
                }else{
                    Log::channel('bitrix_alert')->info("Type $type not found in Bitrix");
                }
            }
            return null;
        }
        
        return null;
    }

    public function fetchBitrixClientTypeId($type)
    {
        $bitrixClientTypeListId = env('BITRIX_CLIENT_TYPE_LIST_ID');
        if($type){
            $response = $this->fetchDealCustomListInBitrixById($bitrixClientTypeListId);
            if ($response) {
                $typeId = $this->extractFieldIdFromBitrixCustomList($response, $type);
                if($typeId){
                    return $typeId;
                }else{
                    Log::channel('bitrix_alert')->info("Type $type not found in Bitrix");
                }
            }
        }
        
        return null;
    }

    public function fetchBitrixLeadSaleType($saleType = null)
    {

        $saleLists = [
            'A phone sale' => '23175',
            'Face-to-face sale' => "23176",
        ];
        return $saleLists[$saleType] ?? "23175";
    }
    public function getBitrixLeadSource($leadSource)
    {
        if($leadSource == 'Personal Referral'){
            $leadSource = 'Personal Lead';
        }elseif($leadSource == 'Agent External'){
            $leadSource = 'EXTERNAL AGENT';
        }elseif($leadSource == 'AX CAPITAL'){
            $leadSource = 'AXCAPITAL';
        }elseif($leadSource == 'Property Finder'){
            $leadSource = 'PROPERTYFINDER';
        }elseif($leadSource == 'Friend or Relative'){
            $leadSource = 'Referral';
        }elseif($leadSource == 'Website'){
            $leadSource = 'Website Visitor';
        }elseif($leadSource == 'Live chat'){
            $leadSource = 'Live chat - Online Chat';
        }
        $bitrixSourceArray = $this->getSourceFromBitrixLeads();
        $sourceArrayLower = array_map('strtolower', $bitrixSourceArray);
        $leadSourceLower = strtolower($leadSource);

        if (in_array($leadSourceLower, $sourceArrayLower)) {
            return array_search($leadSourceLower, $sourceArrayLower);
        }
        Log::channel('bitrix_alert')->info("Secondary Lead source '{$leadSource}' not found in Bitrix");
        return null;
    }
    public function storeBitrixResponse($lead, $request, $response, $isSucess)
    {
        if (!$lead) {
            return;
        }

        $dealBitrixRespone = LeadBitrixResponse::where('lead_id', $lead->id);
        $dealBitrixRespone = $dealBitrixRespone->first();

        // If the record exists, update it; otherwise, create a new one
        if (!$dealBitrixRespone) {
            $dealBitrixRespone = new LeadBitrixResponse();
            $dealBitrixRespone->lead_id = $lead->id;
        }
        $dealBitrixRespone->is_success = $isSucess;
        $dealBitrixRespone->request = json_encode($request);
        $dealBitrixRespone->response = json_encode($response);
        $dealBitrixRespone->save();
        return $dealBitrixRespone;
    }
}
