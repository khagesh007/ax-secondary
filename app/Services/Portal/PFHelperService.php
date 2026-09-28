<?php

namespace App\Services\Portal;

use App\Models\Community;
use App\Models\CommunityProperty;
use App\Models\ListingVerification;
use App\Models\Portal;
use App\Models\PortalListingTimeline;
use App\Models\Property;
use App\Models\Region;
use App\Models\SubCommunity;
use App\Models\User;
use App\Traits\TelegramTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use App\Traits\PropertyFinderTrait;
use Illuminate\Support\Facades\Log;

class PFHelperService
{
    use PropertyFinderTrait;

    public function __construct(protected Portal $portal)
    {
        $this->portal = $portal;
    }

}
