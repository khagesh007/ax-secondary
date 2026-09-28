<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\TraccarUserSettingResource;
use App\Models\TraccarClientSetting;
use App\Models\TraccarRestrictedCoordinate;
use App\Models\User;
use App\Models\UserDevice;
use App\Traits\FileTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TraccarApiController extends BaseController
{
    use FileTrait;

    //
    public function __construct()
    {

    }

    /**
     *
     *
     * Traccar Settings
     *
     * Get a list of Traccar Settings
     * @group Traccar
     * @authenticated
     * @header Accept application/json

     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
    {
    "status": true,
    "code": 200,
    "message": "Setting data found",
    "data": {
    "id": 1,
    "server_url": "http://demo4.traccar.org",
    "port": "5055",
    "location_accuracy": "High",
    "frequency": "1500",
    "distance": "0",
    "angle": "0",
    "offline_buffering": 1,
    "track_from": null,
    "track_to": null,
    "status": 1
    }
    }
     *
     *
     */

    public function getSettings()
    {
        $settings = TraccarClientSetting::where('status', true)->select('id', 'server_url', 'username', 'password', 'port', 'location_accuracy', 'frequency', 'distance', 'angle', 'offline_buffering', 'track_from', 'track_to', 'status', 'coordinates_accuracy', 'version')->first();
        return $this->sendResponse(true, 'Setting data found', $settings, 200);
    }

    /**
     *
     *
     * Traccar User Settings
     *
     * Get a Traccar User Settings
     * @group Traccar
     * @authenticated
     * @header Accept application/json
     * @bodyParam id integer required ID of the user. Example: 12

     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
    {
    "status": false,
    "code": 200,
    "message": "Traccar is not enabled for this user",
    "data": []
    }
     *
     *
     */

    public function getUserSettings($id)
    {
        $user = User::find($id);

        if ($user->id != auth()->user()->id) {
            return $this->sendResponse(false, 'You are not authorized to access this user.', null, 401);
        }

        if ($user) {
            $data['setting'] = TraccarClientSetting::where('status', true)->select('id', 'server_url', 'username', 'password', 'port', 'location_accuracy', 'frequency', 'distance', 'angle', 'offline_buffering', 'track_from', 'track_to', 'status', 'coordinates_accuracy', 'version')->first();

            $data['setting']['location_accuracy'] = strtolower($data['setting']['location_accuracy']);

            $coordinates = TraccarRestrictedCoordinate::where('user_id', $id)->get();
            $user_settings = User::where('id', $id)->select('id', 'traccar_status', 'device_identifier', 'port', 'mic_status', 'status')->first();
            $data['user_setting'] = [
                'traccar_status' => $user_settings?->traccar_status,
                'device_identifier' => $user_settings?->device_identifier,
                'port' => $user_settings?->port,
                'mic_status' => $user_settings?->mic_status,
            ];

            $data['coordinates'] = TraccarUserSettingResource::collection($coordinates);
            // Log::channel('mobileApi')->info('getUserSettings: Traccar User Settings of '.$user->id, ['data' => $data]);
            return $this->sendResponse(true, 'User data found', $data, 200);
        }

        return $this->sendResponse(false, 'User not found', null, 404);
    }

    public function storeDeviceInfo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'firebase_token' => 'required',
            'traccar_device_id' => 'required',
            'device_name' => 'required',
            'device_model' => 'required',
            'device_os' => 'required',
            'device_os_version' => 'required',
            'login_ip' => 'required',
            'coordinates' => 'required',
            'last_active' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(false, 'Validation Error', $validator->errors(), 422);
        }
        try {
            $user = User::find($request->user_id);
            $user->device_token = $request->firebase_token;
            $user->save();

            $device = UserDevice::where('user_id', $request->user_id)->first();
            if ($device) {
                $device->traccar_device_id = $request->traccar_device_id;
                $device->device_name = $request->device_name;
                $device->device_model = $request->device_model;
                $device->device_os = $request->device_os;
                $device->device_os_version = $request->device_os_version;
                $device->device_token = $request->firebase_token;
                $device->login_ip = $request->login_ip;
                $device->coordinates = $request->coordinates;
                $device->last_active_at = $request->last_active;
                $device->device_name = $request->device_name;
                $device->save();
            } else {
                $device = UserDevice::create([
                    'user_id' => $request->user_id,
                    'traccar_device_id' => $request->traccar_device_id,
                    'device_name' => $request->device_name,
                    'device_model' => $request->device_model,
                    'device_os' => $request->device_os,
                    'device_os_version' => $request->device_os_version,
                    'device_token' => $request->firebase_token,
                    'login_ip' => $request->login_ip,
                    'coordinates' => $request->coordinates,
                    'last_active_at' => $request->last_active,
                    'device_name' => $request->device_name,
                ]);
            }




            Log::channel('mobileApi')->info('storeDeviceInfo: Device Info of ' . $user->id, ['data' => $device, 'Request'=>$request->all()]);

            return $this->sendResponse(true, 'Device details saved.', null, 200);
        } catch (\Throwable $th) {
            info($th->getMessage());
            info($request->all());
            return $this->sendResponse(false, 'Something went wrong.', $th->getMessage(), 500);
        }

    }

}
