<?php

namespace App\Console\Commands;

use App\Jobs\PropertyFinderLocationMapJob;
use App\Models\CommunityProperty;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
class PropertyFinderMapLocationId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pf:map-location-id';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will sync the location_id for all community property location.';

    /**
     * Execute the console command.
     *
     * @return int
     */

    public function handle()
    {
        $perPage = 30;
        $currentPage = Cache::get('property_finder_location_page', 1);

        $properties = CommunityProperty::where('portal_ids->property_finder_id', null)
            ->skip(($currentPage - 1) * $perPage)
            ->take($perPage)
            ->orderBy('id', 'asc')
            ->get();

        if ($properties->isEmpty()) {
            // Reset pagination when no more data found
            Cache::put('property_finder_location_page', 1);
            return Command::SUCCESS;
        }

        foreach ($properties as $community_property) {
            $portalIds = json_decode($community_property->portal_ids, true);
            if (!isset($portalIds['property_finder_id'])) {
                PropertyFinderLocationMapJob::dispatch($community_property);
            }
        }

        // Increment page number for next run
        Cache::put('property_finder_location_page', $currentPage + 1);

        return Command::SUCCESS;
    }
}
