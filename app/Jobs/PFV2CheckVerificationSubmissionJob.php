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

class PFV2CheckVerificationSubmissionJob implements ShouldQueue
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
        Log::info("PFV2 Check Listing Verification Job stared");
        $property = Property::find($this->propertyId);
        $entity = $this->getEntityByPropertyForPFV2($property);
        $this->propertyFinderService->listingVerificationList($this->listingId, $this->propertyId, $entity);
        Log::info("PFV2 Check Listing Verification Job ended");
    }
}
