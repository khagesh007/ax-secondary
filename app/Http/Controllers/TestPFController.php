<?php

namespace App\Http\Controllers;

use App\Jobs\PFV2CreateListingJob;
use App\Jobs\PFV2DeleteListingJob;
use App\Models\Portal;
use App\Models\Property;
use App\Models\User;
use App\Services\Portal\PropertyFinderListingVerificationService;
use App\Services\Portal\PropertyFinderService;
use App\Traits\CallTrait;
use App\Traits\TelegramTrait;
use App\Traits\FileTrait;
use App\Traits\PDFTrait;
use App\Traits\PusherTrait;
use App\Traits\DocusignTrait;
use App\Traits\PropertyFinderTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TestPFController extends Controller
{
    use CallTrait, PusherTrait, TelegramTrait, PDFTrait, FileTrait,DocusignTrait,PropertyFinderTrait;

    protected $counter = 0;

    public function __construct(protected PropertyFinderService $propertyFinderService)
    {
    }

    private function updateCreditHistoryDate()
    {
        $targets = KpiTeam::whereIn("kpi", ["bayut_credit_limit", "pf_credit_limit"])->where("start_date", ">=", date("Y-m-d"))->get();
        foreach ($targets as $target) {
            $startDate = null;
            $endDate = null;
            if ($target->kpi == "bayut_credit_limit") {
                $dates = getBayutCreditDurationDates($target->end_date);
                $startDate = $dates["start_date"];
                $endDate = $dates["end_date"];
            }

            if ($target->kpi == "pf_credit_limit") {
                $dates = getPfCreditDurationDates($target->end_date);
                $startDate = $dates["start_date"];
                $endDate = $dates["end_date"];
            }
            $target->start_date = $startDate;
            $target->end_date = $endDate;
            $target->save();

            $report = KpiTeamReport::where("kpi_id", $target->kpi_id)->first();
            if ($report) {
                $report->start_date = $startDate;
                $report->end_date = $endDate;
                $report->save();
            }
        }
    }

    public function index(Request $request)
    {
//        $this->propertyFinderService->listingVerificationList(null, null, "secondary");
//        die();

        $mainPortal = Portal::where("short_name", "PF")->latest()->first();
        $property = Property::whereHas("portals", function ($q) use ($mainPortal) {
            $q->where("portal_id", $mainPortal->id);
        })->where('ref_no', $request->ref_no)->with([
            'agent' => function ($q) {
                $q->select('id', 'name', 'first_name', 'last_name', 'pp_enabled', 'pp_name', 'pp_photo', 'pp_license_no', 'email', 'secondary_email', 'license_no', 'mobile', 'bio', 'thumbnail', 'image');
            },
            'details',
            'propertyCategory' => function ($q) {
                $q->select('id', 'title');
            },
            'propertyCategory.portals' => function ($q) use ($mainPortal) {
                $q->where('portal_id', $mainPortal->id)->select('id', 'title', 'property_category_id', 'portal_id');
            },
            'bed' => function ($q) {
                $q->select('id');
            },
            'bed.portals' => function ($q) use ($mainPortal) {
                $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bed_id', 'portal_id');
            },
            'developer',
            'price' => function ($q) {
                $q->select('id', 'property_id', 'price', 'prices', 'price_on_application');
            },
            'bath' => function ($q) {
                $q->select('id');
            },
            'bath.portals' => function ($q) use ($mainPortal) {
                $q->where('portal_id', $mainPortal->id)->select('id', 'name', 'bath_id', 'portal_id');
            },
            'amenities' => function ($q) use ($mainPortal) {
                $q->whereHas('amenity.portals', function ($q) use ($mainPortal) {
                    $q->where('portal_id', '=', $mainPortal->id);
                });
            },
            'amenities.amenity.portals' => function ($q) use ($mainPortal) {
                $q->where('portal_id', '=', $mainPortal->id)
                    ->where('title', '!=', '');
            },
            'community.region', 'subCommunity', 'communityProperty',
            'photos' => function ($q) {
                $q->select('id', 'property_id', 'path', 'title','original', 'updated_at');
            },
            'floorPlan'
        ])->first();

        if (!$property) {
            abort(404);
        }



        $portalListingId = json_decode($property->portal_ids ?? null, true)['property_finder_id'] ?? null;

        $data = $this->propertyFinderService->preparePayloadForCreateAndUpdateListing($property);

        echo "id: $portalListingId <br>";



        echo "<br>";

        echo json_encode($data['data']);die();

        if ($property) {
            $user = User::find($property->agent_id);
            $pfUserId = json_decode($user->third_party_ids, true)['pf_user_id'] ?? null;
            if (empty($pfUserId)) {
                $pfUserId = $this->propertyFinderService->createUser($user->toArray());
            }
            if ($pfUserId) {
                if ($request->type == 'publish') {
                    $this->propertyFinderService->publishListing($property->id);
                }elseif ($request->type == 'update') {
                    $this->propertyFinderService->updateListing($property->id);
                }elseif ($request->type == 'delete') {
                    $this->propertyFinderService->deleteListing($property->id);
                }elseif ($request->type == 'check') {
                    $this->propertyFinderService->listingVerificationCheckEligibility($property->id);
                }elseif ($request->type == 'submit') {
                    $this->propertyFinderService->listingVerificationSubmit($property->id);
                }elseif ($request->type == 'submissions') {
                    $this->propertyFinderService->listingVerificationList();
                }else{
                    $created = $this->propertyFinderService->createListing($property->id);
                    if ($created) {
//                        $this->propertyFinderService->publishListing($property->id);
                    }
                }
            }
        }

    }
}
