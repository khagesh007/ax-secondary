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

class SecurityDashboardController extends Controller
{
    public function dashboard(){

        return view('admin.security.index');
    }
    public function requestLog(){

        return view('admin.security.requestLog');
    }
}
