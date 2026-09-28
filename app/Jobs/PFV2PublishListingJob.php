<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\Portal\PropertyFinderService;
class PFV2PublishListingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public $propertyId)
    {
        $this->property_id = $propertyId;
        $this->propertyFinderService = app(PropertyFinderService::class);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->propertyFinderService->publishListing($this->property_id);

    }
}
