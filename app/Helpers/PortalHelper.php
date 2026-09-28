<?php

use App\Jobs\PFV2CreateListingJob;
use App\Jobs\PFV2DeleteListingJob;
use App\Jobs\PFV2UnpublishListingJob;
use App\Models\Portal;
use App\Models\PropertyPortal;

 function initPublishAtPf($property_id) :void
{
    if (env('APP_ENV') == 'production') {
        $pfv2 = Portal::where("short_name", "PF")->first();

        if ($pfv2) {
            $hasPortalAssociation = PropertyPortal::where("property_id", $property_id)
                ->where("portal_id", $pfv2->id)
                ->exists();

            try {
                if ($hasPortalAssociation) {
                    PFV2CreateListingJob::dispatch($property_id);
                }
            } catch (\Exception $e) {
                \Log::error('Failed to dispatch PFV2 job for property ' . $property_id . ': ' . $e->getMessage());
            }
        }
    }

}
function initUnpublishAtPF($property_id) :void
{
    if (env('APP_ENV') == 'production') {
        $pfv2 = Portal::where("short_name", "PF")->first();

        if ($pfv2) {
            $hasPortalAssociation = PropertyPortal::where("property_id", $property_id)
                ->where("portal_id", $pfv2->id)
                ->exists();

            try {
                if ($hasPortalAssociation) {
                   // PFV2UnpublishListingJob::dispatch($property_id, true);
                    PFV2DeleteListingJob::dispatch($property_id, true); //SAIM ASKED TO DELETE IT
                }
            } catch (\Exception $e) {
                \Log::error('Failed to dispatch PFV2 job for property ' . $property_id . ': ' . $e->getMessage());
            }
        }
    }

}
