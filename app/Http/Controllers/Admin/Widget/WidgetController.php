<?php

namespace App\Http\Controllers\Admin\Widget;

use App\Http\Controllers\Controller;
use App\Models\CallLog;
use App\Traits\GoogleCalendarTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class WidgetController extends Controller
{
    public function eventWidget(Request $request)
    {
        //print_r($request->params['id']);
        $unique_id = Str::random(8);
        $active_tab = isset($request->params['activeTab']) ? $request->params['activeTab'] : 'call_log';
        $meeting_log = $request->params['meeting_log'] ? 1 : 0;
        $task_log = $request->params['task_log'] ? 1 : 0;
        $followup_log = $request->params['followup_log'] ? 1 : 0;
        $viewing_log = $request->params['viewing_log'] ? 1 : 0;
        $active_tab = $active_tab != null ? $active_tab : 'call_log';
        $id = $request->params['id'];
        $model_id = $request->params['model_key'];

        $call_log = $request->params['call_log'] ? 1 : 0;
        $html = view('admin.partials.widgets.events', get_defined_vars())->render();

        return response()->json(['status' => true, 'html' => $html, 'unique_id' => $unique_id]);

    }


    public function getCallLog(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'id' => 'required',
            'model_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => '0', 'msg' => 'Call log parameters missing']);
        }
        try {
            $notAdmin = auth()->user()->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent", "Telesales", "Telesales Head"]);
            $childrenIds = getUserChildrenIds(auth()->id());
            if ($request->model_id == 'listing_lead_id') {

                $log = CallLog::whereHas(
                    'listingLead',
                    function ($q) use ($request) {
                        $q->where('listing_lead_id', $request->id);
                    }
                )->when($notAdmin, function ($q) use ($childrenIds) {
                    $q->whereIn('user_id', $childrenIds);
                })->get();

            }else {
                if (\Auth::check()) {

                    $log = CallLog::where($request->model_id, $request->id)->when($notAdmin, function ($q) use ($childrenIds) {
                        $q->whereIn('user_id', $childrenIds);
                    })->get();
                }
            }
            if (count($log) > 0) {
                return response()->json(['status' => '1', 'msg' => 'Call log found', "call_log" => $log]);
            } else {
                return response()->json(['status' => '0', 'msg' => 'Call log not found']);
            }
        } catch (\Exception $exception) {
            return response()->json(['status' => '0', 'msg' => 'Call log not found']);
        }
    }
}
