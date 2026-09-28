<?php

namespace App\Http\Controllers\Api\V1\App;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\UserResource;
use App\Models\AppLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Facades\Log;
use Laravel\Telescope\Telescope;

class AppLogApiController extends BaseController
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth:api');
    }

    /**
     *
     *
     *Store Log
     * @group Log
     * @bodyParam title string required The title of the log. Example: "Location permission declined"
     * @bodyParam description string optional The description of the log. Example: "Location permission declined and complete details of the log"
     * @bodyParam type string required The type of the log. Example: "Bug, Info, Permission Declined, Recording Disabled, Token Expired, Other"
     * @response 200 scenario=success
     * {
    "status": true,
    "code": 200,
    "message": "Log created successfully",
    "data": {
    "user_id": 263,
    "title": "Permission denied",
    "description": "User device has denied permission for location",
    "type": "Permission Declined",
    "updated_at": "2023-08-01T06:30:20.000000Z",
    "created_at": "2023-08-01T06:30:20.000000Z",
    "id": 1
    }
    }
     *
     * @response 422 scenario="validation failed"
     * {
    "status": false,
    "code": 422,
    "message": "Validation failed",
    "data": {
    "type": [
    "The selected type is invalid."
    ]
    }
    }
     */

    public function store(Request $request)
    {
        Telescope::withoutRecording(function () {
            try {
                return $this->sendResponse(true, 'Log created successfully', [], 200);
                $validator = Validator::make($request->all(), [
                    'title' => 'required|string',
                    'description' => 'nullable|string',
                    'type' => 'required|string|in:Bug,Info,Permission Declined,Recording Disabled,Token Expired,Other',
                ]);
                if ($validator->fails()) {
                    return $this->sendResponse(false, 'Validation failed', $validator->errors(), 422);

                }else{
                        $user = Auth::user();
                        $appLog = new AppLog();
                        $appLog->user_id = $user->id;
                        $appLog->title = $request->title;
                        $appLog->description = $request->description;
                        $appLog->type = $request->type;
                        if ($appLog->save()) {
                            return $this->sendResponse(true, 'Log created successfully', $appLog, 200);
                        } else {
                            return $this->sendResponse(false, 'Unable to create log', null, 500);
                        }
                }

            } catch (\Exception $e) {
                Log::channel('discord')->error("Error in login function in AuthApiController.php " . $e->getMessage());
                return $this->sendResponse(false, 'Unable to login', null, 500);
            }
        });
    }


}
