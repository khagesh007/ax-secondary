<?php

namespace App\Jobs;

use App\Models\Community;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\Portal\PropertyFinderService;
class PropertyFinderCommunityMapJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     * this job is used by PropertyFinderMapLocationId cron command
     * @return void
     */
    private PropertyFinderService $propertyFinderService;
    public Community $community;

    public function __construct(Community $community)
    {
        $this->propertyFinderService = app(PropertyFinderService::class);
        $this->community = $community;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $locationId = $this->propertyFinderService->findLocationId($this->community);
        if ($locationId) {
            $community = Community::where('id', $this->community->id)->first();
            $portalIds = json_decode($community->portal_ids, true);
            $portalIds['property_finder_id'] = $locationId;
            $community->portal_ids = $portalIds;
            $community->save();
        }
    }
}
