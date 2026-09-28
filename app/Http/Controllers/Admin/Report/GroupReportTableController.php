<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Models\Community;
use App\Models\Contact;
use App\Models\Event;
use App\Models\EventType;
use App\Models\Group;
use App\Models\Lead;
use App\Models\LeadNote;
use App\Models\LeadStatus;
use App\Models\ListingLead;
use App\Models\ListingLeadStatus;
use App\Models\Medium;
use App\Models\Portal;
use App\Models\Property;
use App\Models\PropertyNote;
use App\Models\Source;
use App\Models\Type;
use App\Models\User;
use App\Models\UserLocation;
use App\Models\UserMeta;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class GroupReportTableController extends Controller
{
    public function index(Request $request)
    {

        if (!auth()->user()->can('Group Reports')) {
            abort(403, 'Unauthorized action.');
        }

        $users = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['Sales Director']);
        })->whereDoesntHave('group')
            ->whereHas('children')
            ->where('status', 1)
            ->get();

        $data_filtered = Group::with(['parent', 'user'])
            ->whereNull('parent_id')
            ->orderBy('order', 'asc')
            ->get();

        if ($request->child) {

            $users = User::whereHas('roles', function ($query) {
                $query->whereIn('name', ['Sales Director']);
            })->whereDoesntHave('group')
                ->whereHas('children')
                ->where('status', 1)
                ->get();

            $data_filtered = Group::with(['parent', 'user'])->where('parent_id', $request->child)->get();
            foreach ($data_filtered as $row) {
                $user_id = $row->user->id ?? null;
                $childCount = childcount($user_id) ?? 0;
                $row->child_count = $childCount;
            }

            return view('admin.reports.group')
                ->with('users', $users)
                ->with('data_filtered', $data_filtered);
        }

        foreach ($data_filtered as $row) {
            $user_id = $row->user->id ?? null;
            $childCount = childcount($user_id) ?? 0;
            $row->child_count = $childCount;
        }

        return view('admin.reports.group_table')
            ->with('users', $users)
            ->with('data_filtered', $data_filtered);
    }
}
