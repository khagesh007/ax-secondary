<?php

namespace App\Jobs;

use App\Models\Property;
use App\Traits\PropertyFinderTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\Portal\PropertyFinderService;
use Illuminate\Support\Facades\Log;

class PFV2CheckListingStateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    use PropertyFinderTrait;

    private PropertyFinderService $propertyFinderService;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public $portalId, public $propertyId, public $listingId)
    {
        $this->propertyFinderService = app(PropertyFinderService::class);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info("PFV2 Check Listing State Job stared");
        $property = Property::find($this->propertyId);
        $entity = $this->getEntityByPropertyForPFV2($property);
        $data = $this->propertyFinderService->findListing($this->listingId, $this->propertyId, $entity);
        if ($data){
            $stage = $data["state"]["stage"] ?? null;
            $type = $data["state"]["type"] ?? null;
            if ($stage == "live"){
                $this->propertyFinderService->makeListingTimelineLog([
                    'portal_id' => $this->portalId,
                    'property_id' => $this->propertyId,
                    'title' => "Listing {$this->listingId} is live.",
                    'description' => "Listing {$this->listingId} published, is live state and $type type now.",
                    'status' => 'success',
                ]);
            }elseif ($stage == "draft"){
                $this->propertyFinderService->makeListingTimelineLog([
                    'portal_id' => $this->portalId,
                    'property_id' => $this->propertyId,
                    'title' => "Listing {$this->listingId} is draft.",
                    'description' => "Listing {$this->listingId} published, is draft state and $type type now.",
                    'status' => 'success',
                ]);
            }else{
                $this->propertyFinderService->makeListingTimelineLog([
                    'portal_id' => $this->portalId,
                    'property_id' => $this->propertyId,
                    'title' => "Listing {$this->listingId} is live.",
                    'description' => "Listing {$this->listingId} was published, is {$stage} stage and $type type now.",
                    'status' => 'failed',
                ]);
            }
        }
        Log::info("PFV2 Check Listing State Job ended");
    }
}
