<?php

namespace App\Console\Commands;


use App\Jobs\PropertyFinderSubCommunityMapJob;
use App\Models\SubCommunity;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class PropertyFinderMapSubCommunityId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pf:map-sub-community-id';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will sync the property_finder_id in portal_ids json field for all sub-communities in scondary.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $perPage = 30;
        $currentPage = Cache::get('property_finder_sub_community_page', 1);

        $sub_communities = SubCommunity::where('portal_ids->property_finder_id', null)
            ->skip(($currentPage - 1) * $perPage)
            ->take($perPage)
            ->orderBy('id', 'asc')
            ->get();

        if ($sub_communities->isEmpty()) {
            // Reset pagination when no more data found
            Cache::put('property_finder_sub_community_page', 1);
            return Command::SUCCESS;
        }

        foreach ($sub_communities as $sub_community) {
            $portalIds = json_decode($sub_community->portal_ids, true);
            if (!isset($portalIds['property_finder_id'])) {
                PropertyFinderSubCommunityMapJob::dispatch($sub_community);
            }
        }

        // Increment page number for next run
        Cache::put('property_finder_sub_community_page', $currentPage + 1);
    }
}
