<?php

namespace App\Http\Controllers\Admin\Campaign;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CampaignController extends Controller
{
    public function index()
    {
        $superAdmin = auth()->user()->hasRole(['Super Admin']);
        $notAdmin = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        $users = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['Sales Director', 'Team Leader', 'Line Manager']);
        })->whereHas('children')
            ->where('status', 1)
            ->get();

        $agents = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['Agent']);
        })
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->where(function ($q) use ($childrenIds) {
                    $q->whereIn('id', $childrenIds);
                });
            })
            ->when(auth()->user()->hasRole("Admin - Abu Dhabi"), function ($q) {
                $q->where('region_id', 2);
            })
            ->where('status', 1)
            ->get();
        return view('admin.campaigns.index')
            ->with('users', $users)
            ->with('agents', $agents);
    }

    public function datatable(Request $request)
    {
        $superAdmin = auth()->user()->hasRole(['Super Admin']);
        $notAdmin = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        $query = Campaign::select('campaigns.*');

        if ($request->agent_id) {
            $this->isArray("agent_id") ? $query->whereIn("id", \request("agent_id")) : $query->where("id", \request("agent_id"));
        }

        return DataTables::of($query->with(['approvedBy', 'createdBy', 'messages'])
                ->when($notAdmin, function ($q) use ($childrenIds) {
                    $q->where(function ($q) use ($childrenIds) {
                        $q->whereIn('created_by', $childrenIds);
                    });
                })
                ->when(auth()->user()->hasRole("Admin - Abu Dhabi"), function ($q) {
                    $q->where('region_id', 2);

                })
                ->when($request->status, function ($q) use ($request) {
                    $q->where('status', $request->status);
                })
                ->when($request->confirmation, function ($q) use ($request) {
                    $q->whereNotNull('approved_by');
                })
                ->when($request->batch_no, function ($q) use ($request) {
                    $q->where('batch_no', 'like', '%' . $request->batch_no . '%');
                })
        )->make(true);
    }

    public function edit(Request $request)
    {
        $campaign = Campaign::find($request->id);
        return view('admin.campaigns.includes._edit')
            ->with('campaign', $campaign);
    }

    public function update(Request $request)
    {
        // if ($request->status == 1) {
        //     $result = array('status' => '1', 'msg' => 'This campaign has already been approved and cannot be updated.', 'data' => []);
        //     return response()->json($result);
        // }
        $result = array('status' => '1', 'msg' => 'Campaign updated Successfully.', 'data' => []);

        $campaign = Campaign::find($request->id);
        $old_message = $campaign->message;
        $campaign->name = $request->campaign_name;
        $campaign->status = $request->status;
        $campaign->custom_link = $request->custom_link;
        $campaign->message = $request->message;
        if ($request->status == 1) {
            $campaign->approved_by = auth()->id();
            $result = array('status' => '1', 'msg' => 'Campaign Approved Successfully.', 'data' => []);
        }
        $campaign->updated_by = auth()->id();
        $campaign->save();
        if ($old_message != $request->message) {

            foreach ($campaign->messages as $bulkMessage) {

                $extra_content = ' ';
                $slug = $bulkMessage->short_url;
                $short_url = urlShortner(route('public.smslinkClicked', $slug));
                $extra_content .= $short_url;
                $message = $request->message . ' ' . $extra_content;

                $bulkMessage->custom_link = $request->custom_link;
                $bulkMessage->short_url = $slug;
                $bulkMessage->message = $message;
                $bulkMessage->created_by_id = auth()->id();
                $bulkMessage->save();
            }

        }
        return response()->json($result);
    }

}
