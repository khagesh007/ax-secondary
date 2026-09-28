<?php

namespace App\Http\Controllers\Admin\Listing;

use App\Http\Controllers\AjaxController;
use App\Http\Controllers\Controller;

use App\Models\Contact;
use App\Models\PortalListingTimeline;
use App\Models\RequestLog;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use DB;
use Spatie\Activitylog\Models\Activity;

class ListingPortalSyncLogController extends Controller
{

    public function index(){

        $statuses = PortalListingTimeline::select('status')->distinct('status')->pluck('status');
        $data['statuses'] = $statuses;

        return view('admin.listingPortal.syncLogs')->with($data);

    }
    private function applyRequestLogFilters(Builder $builder, Request $request)
    {

        return $builder->when($request->ip_address, function ($q) {
            $this->isArray("ip_address") ? $q->whereIn("ip_address", \request("ip_address")) : $q->where("ip_address", \request("ip_address"));
        })->when($request->url, function ($q) {
            $this->isArray("url") ? $q->whereIn("url", \request("url")) : $q->where("url", \request("url"));
        })->when($request->slug, function ($q) {
            $this->isArray("slug") ? $q->whereIn("slug", \request("slug")) : $q->where("slug", \request("slug"));
        })->when($request->property_id, function ($q) {
            $q->where("property_id", \request("property_id"));
        })->when($request->created_at != "", function ($q) {
            return $this->queryBuilder($q, 'created_at', \request('created_date_operator'), \request('created_at'));
        })->when(!empty($request->search['value']), function ($q) use($request){
            $searchValue = $request->search['value'];
            // Search for matching property IDs in the main DB
            $propertyIds = \App\Models\Property::on('mysql')
                ->where('ref_no', 'LIKE', '%' . $searchValue . '%')
                ->pluck('id')
                ->toArray();
            $q->where(function($query) use ($searchValue, $propertyIds) {
                $query->where('title', 'LIKE', '%' . $searchValue . '%');
                if (!empty($propertyIds)) {
                    $query->orWhereIn('property_id', $propertyIds);
                }
            });
        })->when($request->status, function ($q) {
            $q->where("status", \request("status"));
        });
    }

    public function dataTable(Request $request)
    {
        ajaxOnly($request);
        $columns = array(
            0 => 'updated_at',
            1 => 'ref_no',
            2 => 'title',
            3 => null,
            4 => null,
            5 => null,
            6 => null,

        );
        // $searchKeyWord = htmlspecialchars($request['search']['value']);
        $q = PortalListingTimeline::query();
        $totalData = $this->applyRequestLogFilters($q, $request)->count();
        // $totalData = $q->count();
        $totalFiltered = $totalData;
        $q = PortalListingTimeline::query();
        $data = $this->applyRequestLogFilters($q, $request)
            ->orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
            ->offset($request['start'])
            ->limit($request['length'])
            ->with(['user', 'portal', 'property'])
            ->get();
        $json_data = array(
            "draw" => intval($request['draw']),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data,
        );

        echo json_encode($json_data);
    }
    public function getDetails(Request $request){
        ajaxOnly($request);

        $request->validate([
            'id' => 'required|exists:log_db.portal_listing_timelines,id',
        ]);

        $timeline = PortalListingTimeline::find($request->id);

        if(!$timeline){
            return response()->json(['status'=>'0','message'=>'No data found']);
        }
        $timeline->load('user', 'portal', 'property');
        return response()->json(['status'=>'1','data'=>$timeline]);
    }
}
