<?php

namespace App\Http\Controllers\Admin\Security;

use App\Http\Controllers\AjaxController;
use App\Http\Controllers\Controller;

use App\Models\Contact;
use App\Models\RequestLog;
use App\Traits\FiltersTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use DB;
use Spatie\Activitylog\Models\Activity;

class SecurityRequestLogController extends Controller
{
    use FiltersTrait;
    public function overview(){
        if(Cache::get('request_log_overview')){
            return response()->json(['status'=>'1','data'=>Cache::get('request_log_overview'),'from'=>'cache','in_cache_for'=>'2 minutes']);
        }
        $data['failed_login_attempts']['Today'] = RequestLog::where('slug','failed_login_attempt')->whereDate('created_at', Carbon::today())->count();
        $data['failed_login_attempts']['Last 7 Days'] = RequestLog::where('slug','failed_login_attempt')->whereDate('created_at', '>', Carbon::today()->subDays(7))->count();
        $data['too_many_requests']['Today'] = RequestLog::where('slug','request_limit_exceeded')->whereDate('created_at', Carbon::today())->count();
        $data['too_many_requests']['Last 7 Days'] = RequestLog::where('slug','request_limit_exceeded')->whereDate('created_at', '>', Carbon::today()->subDays(7))->count();
        Cache::put('request_log_overview', $data, now()->addMinutes(2));
        return response()->json(['status'=>'1','data'=>$data,'from'=>'live','in_cache_for'=>'2 minutes']);


    }

    public function dataTable(Request $request)
    {
        ajaxOnly($request);
        abort_if(!auth()->user()->hasPermissionTo('Contacts List'), 403, 'You do not have permission.');
        $columns = array(
            0 => 'updated_at',
            1 => 'id',
            2 => 'first_name',
            3 => null,
            4 => null,
            5 => null,
            6 => null,
            7 => null,
            8 => 'updated_at',

        );
        // $searchKeyWord = htmlspecialchars($request['search']['value']);
        $q = RequestLog::query();
        $totalData = $this->applyRequestLogFilters($q, $request)->count();
       // $totalData = $q->count();
        $totalFiltered = $totalData;
        $q = RequestLog::query();
        $data = $this->applyRequestLogFilters($q, $request)
            ->orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
            ->offset($request['start'])
            ->limit($request['length'])
            ->with('user')
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
        $validator = \Validator::make($request->all(), [
            'id' => 'required|exists:ax_log.request_logs,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['status'=>'0','message'=>$validator->errors()->first()]);
        }
        $q = RequestLog::query();
        $data = $q->where('id',$request->id)->first();
        if(!$data){
            return response()->json(['status'=>'0','message'=>'No data found']);
        }
        return response()->json(['status'=>'1','data'=>$data]);
    }
}
