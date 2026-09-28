<?php

namespace App\Jobs;

use App\Models\CommunityProperty;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\Portal\PropertyFinderService;
class PropertyFinderLocationMapJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     * this job is used by PropertyFinderMapLocationId cron command
     * @return void
     */
    private PropertyFinderService $propertyFinderService;
    public CommunityProperty $community_property;

    public function __construct(CommunityProperty $community_property)
    {
        $this->propertyFinderService = app(PropertyFinderService::class);
        $this->community_property = $community_property;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $locationId = $this->propertyFinderService->findLocationId($this->community_property);
        if ($locationId) {
            $communityProperty = CommunityProperty::where('id', $this->community_property->id)->first();
            $portalIds = json_decode($communityProperty->portal_ids, true);
            $portalIds['property_finder_id'] = $locationId;
            $communityProperty->portal_ids = $portalIds;
            $communityProperty->save();
        }
    }
}
