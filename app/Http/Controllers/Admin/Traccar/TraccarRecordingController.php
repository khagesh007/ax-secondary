<?php

namespace App\Http\Controllers\Admin\Traccar;

use App\Http\Controllers\Controller;
use App\Models\CoordinateRecording;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TraccarRecordingController extends Controller
{
    public function index()
    {

        $data = CoordinateRecording::whereIn('id', function ($query) {
            $query->selectRaw('MIN(id)')
                ->from('coordinate_recordings')
                ->groupBy('user_id');
        })->with('user')->get();
        return view('admin.traccar_recording.index')
            ->with('data', $data);
    }

    public function show(Request $request, $id)
    {

        // $sevenDaysAgo = Carbon::now()->subDays(14);
        // $user = User::find($id);
        // $data = CoordinateRecording::with('coordinate')
        // ->where('user_id', $id)
        // ->whereHas('coordinate', function ($query) {
        //     $query->whereNull('deleted_at');
        // })->where('created_at', '>=', $sevenDaysAgo)
        // ->orderBy('created_at', 'desc') // Move orderBy before get
        // ->get()
        // ->groupBy(function ($val) {
        //     return Carbon::parse($val->created_at)->format('Y-m-d');
        // });

        $user = User::find($id);
        $sevenDaysAgo = Carbon::now()->subDays(7); // Adjusted to retrieve records from the last 7 days
        $data = CoordinateRecording::with('coordinate')
            ->where('user_id', $id)
            ->whereHas('coordinate', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->where('created_at', '>=', $sevenDaysAgo)
            ->orderBy('created_at', 'desc')
            ->paginate(1);
        $data = $data->groupBy(function ($val) {
            return Carbon::parse($val->created_at)->format('Y-m-d');
        });


        if ($request->ajax()) {
            $user = User::find($id);
            $dateToCompare = Carbon::parse($request->date)->subDay();
            $data = CoordinateRecording::with('coordinate')
                ->where('user_id', $id)
                ->whereHas('coordinate', function ($query) {
                    $query->whereNull('deleted_at');
                })
                ->where(function ($query) use ($dateToCompare) {
                    $query->whereDate('created_at', '=', $dateToCompare->toDateString())
                        ->orWhere(function ($subquery) use ($dateToCompare) {
                            $subquery->where('created_at', '<', $dateToCompare)
                                ->orderBy('created_at', 'desc')
                                ->limit(1);
                        });
                })
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            $data = $data->groupBy(function ($val) {
                return Carbon::parse($val->created_at)->format('Y-m-d');
            });

            return view('admin.traccar_recording.include.data')
                ->with('data', $data)
                ->with('user', $user);
        }

        return view('admin.traccar_recording.show')
            ->with('data', $data)
            ->with('user', $user);
    }

}
