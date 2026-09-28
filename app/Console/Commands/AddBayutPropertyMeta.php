<?php

namespace App\Console\Commands;
use App\Models\Property;
use App\Http\Controllers\Api\ExternalApiController;
use Illuminate\Http\Request;
use Illuminate\Console\Command;
use Illuminate\Http\JsonResponse;

class AddBayutPropertyMeta extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:bayutPropertyMeta';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Command Adds Meta data for Properties from Bayut and Dubizzle';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // return true;
        // normal properties
        info('Bayut Data Feeder Started');
        $lastProcessedRefNo = $this->getLastProcessedRefNo();
        $properties = Property::where('is_live', true)
            ->when($lastProcessedRefNo != 0, function ($query) use ($lastProcessedRefNo) {
                $query->where('ref_no', '<', $lastProcessedRefNo);
            })
            ->whereHas('portals', function ($query) {
                $query->whereIn('portal_id', [2,3]);
            })
            ->orderBy('ref_no', 'desc')
            ->limit(30)
            ->pluck('ref_no');
            $counter = 0;
        foreach ($properties as $ref_no) {
            $params = new Request();
            $params->replace(['ref_no' => $ref_no]);
            $bayutData = (new ExternalApiController())->fetchBayutData($params);
            $dubizzleData = (new ExternalApiController())->fetchDubizzleData($params);

            $response = $bayutData->getData(true);
            if($response['status'] == 0)
            {
                $this->updateLastProcessedRefNo(0);
                // info('Token Expired for Bayut');
                echo("\nToken Expired for Bayut\n");
                info('Token Expire');
            }
            echo $ref_no . "\n";
            $counter++;
            // $this->updateLastProcessedRefNo($ref_no);
        }



        if ($properties->isNotEmpty()) {
            $lastProcessedRefNo = $properties->last();
            $this->updateLastProcessedRefNo($lastProcessedRefNo);
        } else {
            $this->updateLastProcessedRefNo(0);
        }

        info($counter);
        // end normal properties



        // abu dhabi properties

        // $abuDhabiLastProcessedRefNo = $this->getAbuDhabiLastProcessedRefNo();
        // $properties = Property::where('region_id',2)->where('is_live', true)
        //     ->when($abuDhabiLastProcessedRefNo != 0, function ($query) use ($abuDhabiLastProcessedRefNo) {
        //         $query->where('ref_no', '<', $abuDhabiLastProcessedRefNo);
        //     })
        //     ->whereHas('portals', function ($query) {
        //         $query->whereIn('portal_id', [2,3]);
        //     })
        //     ->orderBy('ref_no', 'desc')
        //     ->limit(100)
        //     ->pluck('ref_no')
        //     ;

        // foreach ($properties as $ref_no) {
        //     // dd($ref_no);
        //     // if($ref_no == 'axc-3973858'){
        //     $params = new Request();
        //     $params->replace(['ref_no' => $ref_no]);
        //     $bayutData = (new ExternalApiController())->fetchBayutDataAbuDhabi($params);
        //     $response = $bayutData->getData(true);
        //     if($response['status'] == 0)
        //     {
        //         $this->updateAbuDhabiLastProcessedRefNo(0);
        //          echo("\nToken Expired for Bayut Abu Dhabi\n");
        //     }
        //     echo $ref_no . "\n";
        // // }
        // }

        // if ($properties->isNotEmpty()) {
        //     $abuDhabiLastProcessedRefNo = $properties->last();
        //     $this->updateAbuDhabiLastProcessedRefNo($abuDhabiLastProcessedRefNo);
        // } else {
        //     // Reset last processed ref_no if there are no more properties
        //     $this->updateAbuDhabiLastProcessedRefNo(0);
        // }

        // // end abu dhabi properties




        // // Commercial properties
        // $commercialLastProcessedRefNo = $this->getCommercialLastProcessedRefNo();
        // $properties = Property::whereHas('agent',function($q){
        //     $q->where('is_commercial', true);
        // })->where('is_live', true)
        //     ->when($commercialLastProcessedRefNo != 0, function ($query) use ($commercialLastProcessedRefNo) {
        //         $query->where('ref_no', '<', $commercialLastProcessedRefNo);
        //     })
        //     ->whereHas('portals', function ($query) {
        //         $query->whereIn('portal_id', [2,3]);
        //     })
        //     ->orderBy('ref_no', 'desc')
        //     ->limit(100)
        //     ->pluck('ref_no');

        // foreach ($properties as $ref_no) {
        //     $params = new Request();
        //     $params->replace(['ref_no' => $ref_no]);
        //     $bayutData = (new ExternalApiController())->fetchBayutDataCommercial($params);
        //     $response = $bayutData->getData(true);
        //     if($response['status'] == 0)
        //     {
        //         $this->updateCommercialLastProcessedRefNo(0);
        //          echo("\nToken Expired for Bayut Commercial\n");
        //     }
        //     echo $ref_no . "\n";
        // }

        // if ($properties->isNotEmpty()) {
        //     $commercialLastProcessedRefNo = $properties->last();
        //     $this->updateCommercialLastProcessedRefNo($commercialLastProcessedRefNo);
        // } else {
        //     // Reset last processed ref_no if there are no more properties
        //     $this->updateCommercialLastProcessedRefNo(0);
        // }
        // // end Commercial properties

    }

    private function getLastProcessedRefNo()
    {
        // Retrieve the last processed ref_no from a persistent storage (e.g., database, cache)

        // Example code using cache (adjust as per your storage choice)
        return cache()->get('last_bayut_processed_ref_no', 0);
    }

    private function updateLastProcessedRefNo($refNo)
    {
        // Store the last processed ref_no in a persistent storage (e.g., database, cache)

        // Example code using cache (adjust as per your storage choice)
        cache()->put('last_bayut_processed_ref_no', $refNo);
    }


    private function getAbuDhabiLastProcessedRefNo()
    {
        return cache()->get('abu_dhabi_last_bayut_processed_ref_no', 0);
    }

    private function updateAbuDhabiLastProcessedRefNo($refNo)
    {
        cache()->put('abu_dhabi_last_bayut_processed_ref_no', $refNo);
    }

    private function getCommercialLastProcessedRefNo()
    {
        return cache()->get('commercial_last_bayut_processed_ref_no', 0);
    }

    private function updateCommercialLastProcessedRefNo($refNo)
    {
        cache()->put('commercial_last_bayut_processed_ref_no', $refNo);
    }
}
