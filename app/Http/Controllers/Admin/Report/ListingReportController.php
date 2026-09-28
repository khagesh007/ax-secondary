<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Jobs\bulkReraReport;
use App\Models\Property;
use DataTables;
use Illuminate\Http\Request;

class ListingReportController extends Controller
{
    public function index()
    {
        $expired = Property::where('is_live', 1)
        // ->where('completion_status', 'Off-Plan Secondary')
            ->where('rera_expiry_date', '<', date('Y-m-d'))->count();
        $expiring_today = Property::where('is_live', 1)
        // ->where('completion_status', 'Off-Plan Secondary')
            ->where('rera_expiry_date', '>', date('Y-m-d'))->where('rera_expiry_date', '<', date('Y-m-d', strtotime('+1 days')))->count();
        $expiring_3_days = Property::where('is_live', 1)
        // ->where('completion_status', 'Off-Plan Secondary')
            ->where('rera_expiry_date', '>', date('Y-m-d'))->where('rera_expiry_date', '<', date('Y-m-d', strtotime('+3 days')))->count();
        $expiring_7_days = Property::where('is_live', 1)
        // ->where('completion_status', 'Off-Plan Secondary')
            ->where('rera_expiry_date', '>', date('Y-m-d'))->where('rera_expiry_date', '<', date('Y-m-d', strtotime('+7 days')))->count();
        $expiring_14_days = Property::where('is_live', 1)
        // ->where('completion_status', 'Off-Plan Secondary')
            ->where('rera_expiry_date', '>', date('Y-m-d'))->where('rera_expiry_date', '<', date('Y-m-d', strtotime('+14 days')))->count();
        $expiring_21_days = Property::where('is_live', 1)
        // ->where('completion_status', 'Off-Plan Secondary')
            ->where('rera_expiry_date', '>', date('Y-m-d'))->where('rera_expiry_date', '<', date('Y-m-d', strtotime('+21 days')))->count();

        $totalProperties = Property::where('is_live', 1)
        // ->where('completion_status', 'Off-Plan Secondary')
            ->count();
        // with_rera
        $withRera = Property::where('is_live', 1)
        // ->where('completion_status', 'Off-Plan Secondary')
            ->where('rera_expiry_date', '!=', null)->count();
        // without_rera
        $withoutRera = Property::where('is_live', 1)
        // ->where('completion_status', 'Off-Plan Secondary')
            ->where('rera_expiry_date', null)->count();
        return view('admin.reports.listing.index')
            ->with('expired', $expired)
            // ->with('expiring', $expiring)
            ->with('expiring_21_days', $expiring_21_days)
            ->with('expiring_14_days', $expiring_14_days)
            ->with('expiring_7_days', $expiring_7_days)
            ->with('expiring_3_days', $expiring_3_days)
            ->with('expiring_today', $expiring_today)
            ->with('totalProperties', $totalProperties)
            ->with('withRera', $withRera)
            ->with('withoutRera', $withoutRera);
    }

    public function datatable(Request $request)
    {

        try {

            $query = Property::with('propertyCategory', 'community', 'subCommunity', 'communityProperty', 'agent', 'createdBy', 'updatedBy', 'portals.portal', 'propertyStatus', 'bed', 'price');

            if ($request->duration == 'custom') {
                $from = date('Y-m-d', strtotime($request->from));
                $to = date('Y-m-d', strtotime($request->to));
                $query = $query->whereBetween('rera_expiry_date', [$from, $to]);
            } else {
                $query = $this->queryBuilder($query, 'rera_expiry_date', $request->duration);
            }

            $query = $query->when($request->publish_status, function ($query) use ($request) {
                return $query->where('publish_status', $request->publish_status);
            })->when($request->validity, function ($query) use ($request) {
                    if ($request->validity == 'expired') {
                        return $query->where('rera_expiry_date', '<', date('Y-m-d'));
                    } else if ('invalid') {
                        return $query->whereNull('rera_expiry_date');
                    } else {
                        return $query->where('rera_expiry_date', '>', date('Y-m-d'));
                    }
                })
                ->when($request->completion_status, function ($query) use ($request) {
                    $completion_status = is_array($request->completion_status) ? $request->completion_status : [$request->completion_status];
                    return $query->whereIn('completion_status', $completion_status);
                })
                ->when($request->completion_status != "", function ($query) use ($request) {
                    $completion_status = is_array($request->completion_status) ? $request->completion_status : [$request->completion_status];
                    return $query->whereIn('completion_status', $completion_status);
                })
                ->when($request->listing_type, function ($query) use ($request) {
                    if ($request->listing_type == 'live') {
                        return $query->where('is_live', true);
                    }else{
                        return $query->where('is_live', false);
                    }
                });
            return DataTables::of(($query))->make(true);
        } catch (\Throwable $th) {
            throw $th;
        }

    }

    public function updateReraContract(Request $request)
    {

        $ids = $request->ids;
        bulkReraReport::dispatch($ids);
        return response()->json(['status' => '1', 'message' => 'Rera Report Job Started'], 200);
    }

    private function queryBuilder($query, $column, $operator)
    {

        switch ($operator) {
            case 'today':
                return $query->whereDate($column, '=', now()->format('Y-m-d'));
            case 'yesterday':
                return $query->whereDate($column, '=', now()->subDay()->format('Y-m-d'));
            case 'last_month':
                $startOfMonth = now()->subMonth()->startOfMonth();
                $endOfMonth = now()->subMonth()->endOfMonth();
                return $query->whereBetween($column, [$startOfMonth, $endOfMonth]);
            case 'last_week':
                return $query->whereBetween($column, [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]);
            case 'this_month':
                return $query->whereYear($column, now()->year)->whereMonth($column, now()->month);
            case 'this_week':
                return $query->whereBetween($column, [now()->startOfWeek(), now()->endOfWeek()]);
            case 'last_7_days':
                return $query->whereBetween($column, [now()->subDays(7), now()]);
            case 'last_14_days':
                return $query->whereBetween($column, [now()->subDays(14), now()]);
            case 'last_21_days':
                return $query->whereBetween($column, [now()->subDays(21), now()]);
            case 'last_28_days':
                return $query->whereBetween($column, [now()->subDays(28), now()]);
            case 'last_30_days':
                return $query->whereBetween($column, [now()->subDays(30), now()]);
            case 'last_60_days':
                return $query->whereBetween($column, [now()->subDays(60), now()]);
            case 'last_90_days':
                return $query->whereBetween($column, [now()->subDays(90), now()]);
            case 'last_120_days':
                return $query->whereBetween($column, [now()->subDays(120), now()]);
            case 'last_180_days':
                return $query->whereBetween($column, [now()->subDays(180), now()]);
            case 'last_365_days':
                return $query->whereBetween($column, [now()->subDays(365), now()]);
            case 'custom':
                return $query->whereBetween($column, [now()->subDays($operator), now()]);
            case 'next_7_days':
                return $query->whereBetween($column, [now(), now()->addDays(7)]);
            case 'next_14_days':
                return $query->whereBetween($column, [now(), now()->addDays(14)]);
            case 'next_21_days':
                return $query->whereBetween($column, [now(), now()->addDays(21)]);
            case 'next_28_days':
                return $query->whereBetween($column, [now(), now()->addDays(28)]);
            default:
                return $query;
                throw new \InvalidArgumentException('Invalid operator: ' . $operator);
        }
    }
}
