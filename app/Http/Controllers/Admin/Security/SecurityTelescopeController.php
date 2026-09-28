<?php

namespace App\Http\Controllers\Admin\Security;

use App\Http\Controllers\AjaxController;
use App\Http\Controllers\Controller;

use App\Models\Contact;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use DB;
use Spatie\Activitylog\Models\Activity;

class SecurityTelescopeController extends Controller
{
    public function overview(){
    if(Cache::get('telescope_log_overview')){
        return response()->json(['status'=>'1','data'=>Cache::get('telescope_log_overview'),'from'=>'cache','in_cache_for'=>'2 minutes']);
    }
    $data['failed_login_attempts']['Today'] = DB::table('axcs_logs.telescope_entries')->where('type','request')->where('content->response_status','401')->whereDate('created_at', Carbon::today())->count();
    $data['failed_login_attempts']['Last 7 Days'] = DB::table('axcs_logs.telescope_entries')->where('type','request')->where('content->response_status','401')->whereDate('created_at', '>', Carbon::today()->subDays(7))->count();
    $data['too_many_requests']['Today'] = DB::table('axcs_logs.telescope_entries')->where('type','request')->where('content->response_status','429')->whereDate('created_at', Carbon::today())->count();
    $data['too_many_requests']['Last 7 Days'] = DB::table('axcs_logs.telescope_entries')->where('type','request')->where('content->response_status','429')->whereDate('created_at', '>', Carbon::today()->subDays(7))->count();
        Cache::put('telescope_log_overview', $data, now()->addMinutes(10));
        return response()->json(['status'=>'1','data'=>$data,'from'=>'live','in_cache_for'=>'10 minutes']);


    }
}
