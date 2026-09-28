<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class ReportRequestController extends Controller
{
    public function index()
    {
        return view('admin.report_requests.index');
    }
    public function send(Request $request)
    {
        $request->validate([
            'report' => 'required',
        ]);
        switch ($request->report) {
            case 'activity':
                Artisan::call('agent:activityReport');
                return response()->json(['status' => '1', 'msg' => 'msg']);
                break;
            case 'newlypublished':
                Artisan::call('property:newlyPublished');
                return response()->json(['status' => '1', 'msg' => 'msg']);
                break;
            case 'calendarreport':
                Artisan::call('agent:CalendarReportGenerator');
                return response()->json(['status' => '1', 'msg' => 'msg']);
                break;
            case 'leadreport':
                Artisan::call('agent:newStatusLeadNotification');
                return response()->json(['status' => '1', 'msg' => 'msg']);
                break;
                case 'incompleteleadreport':
                    Artisan::call('group:incompleteLeadNotification');
                    return response()->json(['status' => '1', 'msg' => 'msg']);
                    break;

            default:
                $report = 'lead';
                break;
        }

        return response()->json(['status' => '1', 'msg' => 'msg']);

    }
}
