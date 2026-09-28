<?php

namespace App\Jobs;

use App\Models\Community;
use App\Models\SubCommunity;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\Portal\PropertyFinderService;
class PropertyFinderSubCommunityMapJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     * this job is used by PropertyFinderMapLocationId cron command
     * @return void
     */
    private PropertyFinderService $propertyFinderService;
    public SubCommunity $sub_community;

    public function __construct(SubCommunity $sub_community)
    {
        $this->propertyFinderService = app(PropertyFinderService::class);
        $this->sub_community = $sub_community;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $locationId = $this->propertyFinderService->findLocationId($this->sub_community);
        if ($locationId) {
            $subCommunity = SubCommunity::where('id', $this->sub_community->id)->first();
            $portalIds = json_decode($subCommunity->portal_ids, true);
            $portalIds['property_finder_id'] = $locationId;
            $subCommunity->portal_ids = $portalIds;
            $subCommunity->save();
        }
    }
}
