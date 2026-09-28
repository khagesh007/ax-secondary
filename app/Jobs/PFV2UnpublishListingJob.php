<?php

namespace App\Jobs;

use App\Models\Portal;
use App\Models\Property;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\Portal\PropertyFinderService;
class PFV2UnpublishListingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
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
        $mainPortal = Portal::where("short_name", "PF")->latest()->first();
        try {
            $property = Property::where('id', $this->propertyId)->first();
            if ($property) {
                $portal_ids = json_decode($property->portal_ids ?? null, true);
                if (empty($portal_ids) || empty($portal_ids['property_finder_id'])) {
                    return; // Exit if no Property Finder ID is found
                }else {
                    $user = User::find($property->agent_id);
                    $pfUserId = json_decode($user->third_party_ids, true)['pf_public_profile_id'] ?? null;
                    if (empty($pfUserId)) {
                        $pfUserId = $this->propertyFinderService->createUser($user->toArray());
                    }
                    if ($pfUserId) {
                        $this->propertyFinderService->unpublishListing($property->id);
                    }
                }
            }
        } catch (\Exception $exception) {
            $this->propertyFinderService->makeListingTimelineLog([
                'portal_id' => $mainPortal->id,
                'property_id' => $this->propertyId,
                'title' => 'Token expired while unpublishing listing at property finder',
                'status' => 'failed',
                'response' => json_encode([
                    'error' => $exception->getMessage(),
                    'trace' => $exception->getTraceAsString()
                ])
            ]);
        }
    }
}
