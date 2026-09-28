<?php

namespace App\Services\Portal;

use App\Models\ListingVerification;
use App\Models\Property;
use App\Models\User;

class ListingVerificationService
{

    public function __construct(protected PropertyFinderService $propertyFinderService)
    {
    }

    public function statusWiseCount($params)
    {
        $statuses = ListingVerification::select('status')->distinct()->pluck('status');

        $counts = ListingVerification::selectRaw('COUNT(id) as count, status')
            ->when(!empty($params['start_date']), fn($q) => $q->whereDate('created_at', '>=', $params['start_date']))
            ->when(!empty($params['end_date']), fn($q) => $q->whereDate('created_at', '<=', $params['end_date']))
            ->groupBy('status')
            ->pluck('count', 'status');

        $data = [];
        foreach ($statuses as $status) {
            $data[] = [
                'name' => $status,
                'count' => $counts[$status] ?? 0,
                'value' => $counts[$status] ?? 0,
            ];
        }

        return $data;
    }

    public function userAndStatusWiseCount($params)
    {
        $users = User::whereIn('id', ListingVerification::distinct()->pluck('user_id'))
            ->with(['parent'])
            ->get();

        $statuses = ListingVerification::select('status')->distinct()->pluck('status');
        $data = [];
        foreach ($users as $user) {
            $counts = ListingVerification::selectRaw('COUNT(id) as count, status')
                ->when(!empty($params['start_date']), fn($q) => $q->whereDate('created_at', '>=', $params['start_date']))
                ->when(!empty($params['end_date']), fn($q) => $q->whereDate('created_at', '<=', $params['end_date']))
                ->where('user_id', $user->id)
                ->groupBy('status')
                ->pluck('count', 'status');

            $statusWiseCounts = [];
            foreach ($statuses as $status) {
                $statusWiseCounts[] = [
                    'name' => $status,
                    'count' => $counts[$status] ?? 0,
                ];
            }
            $data[] = [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'image' => $user->thumb_url,
                'parent' => $user->parent->name ?? '',
                'statuses' => $statusWiseCounts
            ];
        }

        return $data;
    }

    public function getList($params)
    {
        return ListingVerification::when(!empty($params['status']), fn($q) => $q->where('status', '>=', $params['status']))
            ->when(!empty($params['start_date']), fn($q) => $q->whereDate('created_at', '>=', $params['start_date']))
            ->when(!empty($params['end_date']), fn($q) => $q->whereDate('created_at', '<=', $params['end_date']))
            ->when(!empty($params['status']), fn($q) => $q->where('status', $params['status']))
            ->when(!empty($params['order_by']) && !empty($params['order']), fn($q) => $q->orderBy($params['order_by'], $params['order']))
            ->with(['property', 'user', 'property.price'])
            ->paginate($params['per_page'] ?? 10);
    }

    public function submitForVerification($propertyId, $documents = [])
    {
        return $this->propertyFinderService->listingVerificationSubmit($propertyId, $documents);
    }

    public function reSubmitForVerification($listingVerificationId)
    {
        try {
            $listingVerification = ListingVerification::find($listingVerificationId);

            if (!$listingVerification) {
                return false;
            }

            if (empty($listingVerification->property_id)) {
                return false;
            }

            return $this->propertyFinderService->listingVerificationReSubmit($listingVerification->property_id, $listingVerification->submission_id, $listingVerification->portal_listing_id);
        } catch (\Exception $exception) {
            return false;
        }
    }

    public function checkEligibility($propertyId)
    {
        return $this->propertyFinderService->listingVerificationCheckEligibility($propertyId);
    }

    public function getPropertiesForVerifications($params)
    {
        return Property::where("is_live", true)
            ->where(function ($query) use ($params) {
                $query->where("ref_no", "like", "%" . $params['term'] . "%")
                    ->orWhere("title", "like", "%" . $params['term'] . "%")
                    ->orWhereHas("communityProperty", function ($query) use ($params) {
                        $query->where("path_name", "like", "%" . $params['term'] . "%");
                    });
            })
            ->whereNotNull("portal_ids->property_finder_id")
            ->whereDoesntHave("listingVerifications")
            ->get();
    }
}
