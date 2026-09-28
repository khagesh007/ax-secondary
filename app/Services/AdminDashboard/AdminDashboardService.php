<?php

namespace App\Services\AdminDashboard;

use App\Models\Activity;
use App\Models\Deal;
use App\Models\Event;
use App\Models\Lead;
use App\Models\ListingLead;
use App\Models\Property;
use App\Models\PropertyCycle;
use App\Models\PropertyMedia;
use App\Models\PropertyNote;
use App\Models\PropertyShootBooking;
use App\Models\User;

use Illuminate\Support\Facades\DB;

class AdminDashboardService
{
    private function countPublishedListings($userIds, $params)
    {
        return PropertyCycle::when(!empty($params['start_date']), fn($q) => $q->whereDate('created_at', '>=', $params['start_date']))
            ->when(!empty($params['end_date']), fn($q) => $q->whereDate('created_at', '<=', $params['end_date']))
            ->whereIn('created_by_id', $userIds)
            ->where('status', 'PUBLISHED')
            ->count();
    }

    private function countListingActivity($userIds, $params)
    {
        $notesCount = PropertyNote::whereIn('created_by_id', $userIds)
            ->when(!empty($params['start_date']), function ($q) use ($params) {
                $q->whereDate('created_at', '>=', $params['start_date']);
            })
            ->when(!empty($params['end_date']), function ($q) use ($params) {
                $q->whereDate('created_at', '<=', $params['end_date']);
            })
            ->count();

        $propertyMediaCount = PropertyMedia::wherenotNull('property_id')
            ->whereIn('created_by_id', $userIds)
            ->when(!empty($params['start_date']), function ($q) use ($params) {
                $q->whereDate('created_at', '>=', $params['start_date']);
            })
            ->when(!empty($params['end_date']), function ($q) use ($params) {
                $q->whereDate('created_at', '<=', $params['end_date']);
            })->count();

        $propertyShootBookingCount = PropertyShootBooking::wherenotNull('property_id')
            ->whereIn('created_by_id', $userIds)
            ->when(!empty($params['start_date']), function ($q) use ($params) {
                $q->whereDate('created_at', '>=', $params['start_date']);
            })
            ->when(!empty($params['end_date']), function ($q) use ($params) {
                $q->whereDate('created_at', '<=', $params['end_date']);
            })->count();

        $propertyCount = Activity::where('subject_type', Property::class)
            ->where(function ($q) use ($userIds) {
                $q->whereIn('causer_id', $userIds)
                    ->where('causer_type', User::class);
            })
            ->when(!empty($params['start_date']), function ($q) use ($params) {
                $q->whereDate('created_at', '>=', $params['start_date']);
            })
            ->when(!empty($params['end_date']), function ($q) use ($params) {
                $q->whereDate('created_at', '<=', $params['end_date']);
            })->count();

        return [
            'statuses' => [
                [
                    'name' => 'Notes',
                    'count' => $notesCount,
                ],
                [
                    'name' => 'Property Media',
                    'count' => $propertyMediaCount,
                ],
                [
                    'name' => 'Property Shoot Bookings',
                    'count' => $propertyShootBookingCount
                ],
                [
                    'name' => 'Listings',
                    'count' => $propertyCount,
                ]
            ]
        ];
    }

    private function countAverageApprovalExecutionTime($userIds, $params): float
    {
        $userIds = collect($userIds)->map(fn($id) => (int) $id)->implode(','); // for safe SQL injection-free binding

        $startDateCondition = !empty($params['start_date']) ? "AND created_at >= '{$params['start_date']}'" : '';
        $endDateCondition = !empty($params['end_date']) ? "AND created_at <= '{$params['end_date']}'" : '';

        $sql = "WITH status_sequence AS (
            SELECT
                id,
                property_id,
                status,
                created_at,
                created_by_id,
                LEAD(status) OVER (PARTITION BY property_id ORDER BY created_at) AS next_status,
                LEAD(created_at) OVER (PARTITION BY property_id ORDER BY created_at) AS next_created_at
            FROM property_cycles
            WHERE created_by_id IN ({$userIds})
            {$startDateCondition}
            {$endDateCondition}
        ),
        pending_to_published AS (
            SELECT
                property_id,
                created_at AS pending_at,
                next_created_at AS published_at,
                TIMESTAMPDIFF(SECOND, created_at, next_created_at) AS time_taken_seconds
            FROM status_sequence
            WHERE status = 'PENDING' AND next_status = 'PUBLISHED'
        )
        SELECT
            AVG(time_taken_seconds) / 60 AS avg_pending_to_published_minutes
        FROM pending_to_published";

        $result = DB::selectOne($sql);

        return round($result->avg_pending_to_published_minutes ?? 0, 2);
    }

    public function getListingsData($request, $params)
    {
        $users = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['Admin']);
        })
            ->where('parent_id', 1730)
            ->with(['parent'])
            ->get();


        $listingActivityData = [];
        $publishedListingsData = [];
        $averageApprovalExecutionTimeData = [];
        foreach ($users as $user) {
            $userIds = array_merge([$user->id], renderUserChildren($user));
            $userDetails = [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'image' => $user->thumb_url,
                'parent' => $user->parent->name ?? '',
            ];

            $listingActivityData[] = array_merge($userDetails, $this->countListingActivity($userIds, $params));

            $publishedListingsData[] = array_merge($userDetails, [
                'count' => $this->countPublishedListings($userIds, $params)
            ]);

            $averageApprovalExecutionTimeData[] = array_merge($userDetails, [
                'count' => $this->countAverageApprovalExecutionTime($userIds, $params)
            ]);
        }

        return [
            'listing_activity_data' => collect($listingActivityData)->sortByDesc('count')->values()->all(),
            'published_listings_data' => collect($publishedListingsData)->sortByDesc('count')->values()->all(),
            'average_approval_execution_time_data' => collect($averageApprovalExecutionTimeData)->sortByDesc('count')->values()->all(),
        ];
    }
}
