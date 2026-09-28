<?php

namespace App\Console\Commands\PropertyFinder;

use App\Models\Portal;
use App\Models\Property;
use App\Services\Portal\PropertyFinderService;
use App\Traits\PropertyFinderTrait;
use Illuminate\Console\Command;

class PFV2FetchVerificationSubmissionCommand extends Command
{
    use PropertyFinderTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pfv2:fetch-verification-submissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $data = [];
    protected $entities = ["secondary", "primary", "abu_dhabi", "commercial"];

    protected PropertyFinderService $propertyFinderService;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->propertyFinderService = app(PropertyFinderService::class);
        $data = [];

        Property::where("is_live", true)
            ->whereNotNull("portal_ids->property_finder_id")
            ->whereDoesntHave("listingVerifications")
            ->with(["agent"])
            ->chunk(50, function ($properties) use (&$data) {
                foreach ($properties as $property) {
                    $portalIds = json_decode($property->portal_ids, true) ?? [];
                    $portalListingId = $portalIds['property_finder_id'] ?? null;
                    if (!empty($portalListingId)) {
                        $entity = $this->getEntityByPropertyForPFV2($property);
                        $data[$entity][] = $portalListingId;
                    }
                }
            });

        foreach ($data as $entity => $portalIds) {
            collect($portalIds)->chunk(50)->each(function ($chunkedIds) use ($entity) {
                if (!empty($chunkedIds)) {
                    $this->propertyFinderService->listingVerificationSubmissionsList($chunkedIds->implode(", "), $entity);
                }
            });
        }

        return Command::SUCCESS;
    }
}
