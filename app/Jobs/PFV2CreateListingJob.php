<?php

namespace App\Jobs;

use App\Models\Portal;
use App\Models\Property;
use App\Models\User;
use App\Services\Portal\PropertyFinderService;
use App\Traits\PropertyFinderTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PFV2CreateListingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    use PropertyFinderTrait;

    private PropertyFinderService $propertyFinderService;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(protected $propertyId)
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
        //check if the application environment is production
        if (app()->environment('local')) {
            return;
        }

        Log::info("PFV2CreateListingJob started");
        try {
            $mainPortal = Portal::where("short_name", "PF")->latest()->first();
            $property = Property::whereHas("portals", function ($q) use ($mainPortal) {
                $q->where("portal_id", $mainPortal->id);
            })->where('id', $this->propertyId)->first();

            if ($property) {
                $entity = $this->getEntityByPropertyForPFV2($property);
                $user = User::find($property->agent_id);
                $pfUserId = json_decode($user->third_party_ids, true)['pf_public_profile_id'] ?? null;
                if (empty($pfUserId)) {
                    $this->propertyFinderService->createUser($user->toArray(), $property->id, $entity);
                }
                $portalIds = json_decode($property->portal_ids, true) ?? [];
                $portalListingId = $portalIds['property_finder_id'] ?? null;
                if (empty($portalListingId)) {
                    $listingData = $this->propertyFinderService->findListingByReference($property->ref_no, $property->id, $entity);
                    if (!empty($listingData)) {
                        if ($property->portal_ids == null) {
                            $portal_data = [];
                        } else {
                            $portal_data = json_decode($property->portal_ids, true);
                        }
                        $portal_data['property_finder_id'] = $listingData['id'];
                        $property->withoutApproval()->update(['portal_ids' => json_encode($portal_data)]);
                        $portalListingId = $listingData['id'];
                    }
                }

                $shouldPublish = false;
                if (!empty($portalListingId)) {
                    Log::info("PFV2CreateListingJob: Updating listing for property ID: " . $property->ref_no);
                    $this->propertyFinderService->updateListing($property->id);
                }else{
                    Log::info("PFV2CreateListingJob: Creating listing for property ID: " . $property->ref_no);
                    $shouldPublish = $this->propertyFinderService->createListing($property->id);
                }

                if ($shouldPublish) {
                    $this->propertyFinderService->publishListing($property->id);
                }

                if ($portalListingId){
                    PFV2CheckVerificationSubmissionJob::dispatch($mainPortal->id, $property->id, $portalListingId)->delay(now()->addMinutes(5));
                }
            }
        } catch (\Exception $exception) {
            Log::error("PFV2CreateListingJob error: " . $exception->getMessage());
        }
        Log::info("PFV2CreateListingJob ended");
    }
}
