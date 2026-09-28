<?php

namespace App\Http\Controllers\Admin\DashboardV3;

use App\Http\Controllers\Controller;
use App\Services\AdminDashboard\AdminDashboardService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AdminDashboardController extends Controller
{
    public function __construct(public AdminDashboardService $adminDashboardService)
    {
    }

    public function index()
    {
        abort_if(Gate::denies("Admin Dashboard Reports"), 401);
        return view('admin.admin_dashboard.index');
    }

    public function listingsData(Request $request)
    {

        abort_if(Gate::denies("Admin Dashboard Reports"), 401);

        $date = $request->date;

        $params = [
            'date' => $date,
            'start_date' => $request->start_date != null ? Carbon::parse($request->start_date)->format('Y-m-d') : null,
            'end_date' => $request->end_date != null ? Carbon::parse($request->end_date)->format('Y-m-d') : null,
            'group' => 'secondary',
        ];

        $data = $this->adminDashboardService->getListingsData($request, $params);

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }

}
