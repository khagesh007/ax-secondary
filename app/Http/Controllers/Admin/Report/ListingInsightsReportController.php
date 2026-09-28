<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Models\Group;
use DB;
use Illuminate\Http\Request;

class ListingInsightsReportController extends Controller
{
    public function index(Request $request)
    {
        $groups = Group::with(["user"])
            ->whereNull("parent_id")
            ->orderBy("order")
            ->get();

        $data = [];

        return view("admin.reports.listing_insights.index", compact("groups", "data"));
    }
}
