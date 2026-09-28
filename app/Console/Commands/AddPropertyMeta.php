<?php

namespace App\Console\Commands;
use App\Models\Property;
use App\Http\Controllers\Api\ExternalApiController;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AddPropertyMeta extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:propertyMeta';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Command Adds Meta data for Properties from Property Finder';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $lastProcessedRefNo = $this->getLastProcessedRefNo();

        $properties = Property::where('is_live', true)
            ->when($lastProcessedRefNo != 0, function ($query) use ($lastProcessedRefNo) {
                $query->where('ref_no', '<', $lastProcessedRefNo);
            })
            ->whereHas('portals', function ($query) {
                $query->where('portal_id', 1);
            })
            ->orderBy('ref_no', 'desc')
            ->limit(100)
            ->pluck('ref_no')
            ;

        foreach ($properties as $ref_no) {
            $params = new Request();
            $params->replace(['ref_no' => $ref_no]);
            $pfData = (new ExternalApiController())->fetchPFData($params);
            $response = $pfData->getData(true);
            if($response['status'] == 0)
            {
                $this->updateLastProcessedRefNo(0);
                echo("\nToken Expired for Property Finder\n");
            }
            echo $ref_no . "\n";
            
        }

        if ($properties->isNotEmpty()) {
            $lastProcessedRefNo = $properties->last();
            $this->updateLastProcessedRefNo($lastProcessedRefNo);
        } else {
            // Reset last processed ref_no if there are no more properties
            $this->updateLastProcessedRefNo(0);
        }
    }

    private function getLastProcessedRefNo()
    {
        // Retrieve the last processed ref_no from a persistent storage (e.g., database, cache)

        // Example code using cache (adjust as per your storage choice)
        return cache()->get('last_processed_ref_no', 0);
    }

    private function updateLastProcessedRefNo($refNo)
    {
        // Store the last processed ref_no in a persistent storage (e.g., database, cache)

        // Example code using cache (adjust as per your storage choice)
        cache()->put('last_processed_ref_no', $refNo);
    }
}
