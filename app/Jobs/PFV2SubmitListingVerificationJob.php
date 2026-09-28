<?php

namespace App\Jobs;

use App\Models\Portal;
use App\Models\Property;
use App\Models\User;
use App\Services\Portal\PropertyFinderService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PFV2SubmitListingVerificationJob implements ShouldQueue
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
        try {
            $mainPortal = Portal::where("short_name", "PF")->latest()->first();
            $property = Property::whereHas("portals", function ($q) use ($mainPortal) {
                $q->where("portal_id", $mainPortal->id);
            })->where('id', $this->propertyId)->first();
            if ($property) {
                $user = User::find($property->agent_id);
                $pfUserId = json_decode($user->third_party_ids, true)['pf_user_id'] ?? null;
                if (empty($pfUserId)) {
                    $pfUserId = $this->propertyFinderService->createUser($user->toArray());
                }
                if ($pfUserId) {
                    $submitted = $this->propertyFinderService->listingVerificationSubmit($property->id);
                }
            }
        } catch (\Exception $exception) {

        }
    }
}
