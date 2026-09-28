<?php

namespace App\Http\Controllers\admin\Device;

use App\Http\Controllers\Controller;
use App\Models\UserDevice;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index()
    {
        $devices = UserDevice::all();

        $devices_name = [];
        foreach ($devices as $device) {
            $deviceName = explode(' ', $device->device_name);
            if (count($deviceName) > 1) {
                $deviceName = $deviceName[0] . ' ' . $deviceName[1];
                $fullName = explode(' ', $device->agent->full_name);
                $fullName = $fullName[0] . ' ' . $fullName[1];
                if ($fullName != $deviceName) {
                    $devices_name[] = $device->id;
                }
            }
        }
        $missmatched = UserDevice::whereIn('id', $devices_name)->get();

        $namesArray = [];

        $userDevices = UserDevice::all();

        foreach ($userDevices as $userDevice) {
            $deviceName = $userDevice->device_name;

            $pos = strpos($deviceName, 'v10');

            if ($pos !== false) {
                $name = substr($deviceName, 0, $pos);

                $namesArray[] = $name;
            }
        }

        $matchingUsers = \App\Models\User::whereIn(
            \DB::raw("CONCAT(first_name, ' ', last_name)"),
            $namesArray
        )->get();

        // dd($matchingUsers);

        return view('admin.device.index')
            ->with('devices', $devices)
            ->with('missmatched', $missmatched);
    }

    public function datatable(Request $request)
    {
        $columns = array(
            0 => 'updated_at',
            1 => 'id',
            2 => 'name',

        );
        $searchKeyWord = htmlspecialchars($request['search']['value']);
        $totalData = UserDevice::count();
        $totalFiltered = $totalData;

        if (!empty($searchKeyWord)) {
            $data = UserDevice::where(function ($q) use ($searchKeyWord) {
                $q->where('device_name', 'LIKE', '%' . $searchKeyWord . '%');
            })->with('agent')->limit($request['length'])->get();
            $totalFiltered = $data->count();

        } else {
            $data = UserDevice::orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
                ->offset($request['start'])
                ->limit($request['length'])
                ->with('agent')
                ->get();
            $totalFiltered = $data->count();

        }

        $json_data = array(
            "draw" => intval($request['draw']),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data,
        );

        echo json_encode($json_data);
    }
    public function destroy(Request $request)
    {
        $device = UserDevice::find($request->id);
        $device->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Device deleted successfully!',
        ]);
    }
}
