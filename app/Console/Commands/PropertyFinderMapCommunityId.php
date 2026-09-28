<?php

namespace App\Console\Commands;

use App\Jobs\PropertyFinderCommunityMapJob;
use App\Models\Community;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class PropertyFinderMapCommunityId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pf:map-community-id';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $perPage = 30;
        $currentPage = Cache::get('property_finder_community_page', 1);

        $communities = Community::where('portal_ids->property_finder_id', null)
            ->skip(($currentPage - 1) * $perPage)
            ->take($perPage)
            ->orderBy('id', 'asc')
            ->get();

        if ($communities->isEmpty()) {
            // Reset pagination when no more data found
            Cache::put('property_finder_community_page', 1);
            return Command::SUCCESS;
        }

        foreach ($communities as $community) {
            $portalIds = json_decode($community->portal_ids, true);
            if (!isset($portalIds['property_finder_id'])) {
                PropertyFinderCommunityMapJob::dispatch($community);
            }
        }

        // Increment page number for next run
        Cache::put('property_finder_community_page', $currentPage + 1);
        return Command::SUCCESS;
    }
}
