<?php

namespace App\Http\Controllers\Api\V1\Firebase;

use App\Http\Controllers\Api\BaseController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Validator;

class FirebaseApiController extends BaseController
{

    /**
     *
     *
     * Store Device Token
     * @group Firebase
     * @bodyParam device_token string required The device token of the Agents Device. Example: a3sd4c3s
     * @bodyParam user_id int required The ID of the User. Example: 12
     * @response 200 scenario=success
    {
    "status": true,
    "code": 200,
    "message": "Device token saved.",
    "data": null
    }
     * @response 422 scenario=success
    {
    "status": false,
    "code": 422,
    "message": "Validation Error",
    "data": {
    "user_id": [
    "The user id field is required."
    ],
    "device_token": [
    "The device token field is required."
    ]
    }
    }
     * @response 500 scenario=error
    {
    "status": false,
    "code": 500,
    "message": "Something went wrong.",
    "data": null
    }
     */

    public function storeDeviceToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_token' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(false, 'Validation Error', $validator->errors(), 422);
        }
        try {
            $user = User::find($request->user_id);
            $user->device_token = $request->device_token;
            $user->save();
            Log::channel('mobileApi')->info('storeDeviceToken: Firebase Token for user ID:' . $user->id, ['data' => $request->device_token]);

            return $this->sendResponse(true, 'Device token saved.', null, 200);
        } catch (\Throwable $th) {
            return $this->sendResponse(false, 'Something went wrong.', null, 500);
        }

    }

    /**
     *
     *
     * Send Notification for Dial Number
     * @group Firebase
     * @bodyParam phone string required The phone number of the contact. Example: +971787878176
     * @bodyParam user_id int required The ID of the User. Example: 12
     * @bodyParam name string The Name of the contact. Example: John Doe
     * @response 200 scenario=success
    {
    "multicast_id": 864904251808751779,
    "success": 1,
    "failure": 0,
    "canonical_ids": 0,
    "results": [
    {
    "message_id": "0:1687777706798053%f483f14ef483f14e"
    }
    ]
    }
     * @response 422 scenario=success
    {
    "status": false,
    "code": 422,
    "message": "Validation Error",
    "data": {
    "user_id": [
    "The user id field is required."
    ],
    "phone": [
    "The phone field is required."
    ]
    }
    }
     * @response 500 scenario=success
    {
    "status": false,
    "code": 500,
    "message": "Something went wrong.",
    "data": null
    }
     */
    public function DialNumber(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'phone' => 'required',
            'name' => 'nullable',
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(false, 'Validation Error', $validator->errors(), 422);
        }

        try {
            $user = User::find($request->user_id);
            $phone = $request->phone;
            $name = $request->name ?? '';
            $serverKey = 'AAAA_OxfTQE:APA91bE22wzYji8aEerbkVtrYsbtJFoxM-spY1PTFWw1sf0jo0utgl06sJsvayFuyNo772o1TYjiLz44XcRr1JrrUJ9yvmsDVHqmBgVFa_B_l4uPXpG_tnUIk_BBUuYMjHAGSyvPfxvs';
            $deviceToken = $user->device_token;

            $notification = [
                'title' => 'Dial Number Request',
                'body' => 'You have a dial number request for ' . $name . ' ' . $phone . '.',
            ];

            $data = [
                'phone' => $phone,
                'name' => $name,
                'token' => $request->input('device_token'),
            ];

            $payload = [
                'notification' => $notification,
                'data' => $data,
                'to' => $deviceToken,
                'priority' => 'high',

            ];

            $response = Http::withHeaders([
                'Authorization' => 'key=' . $serverKey,
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', $payload);

            return $response->json();
        } catch (\Throwable $th) {
            return $this->sendResponse(false, $th, null, 400);

            return $th->getMessage();
        }

    }

    /**
     *
     *
     * Token Expired Send Login
     * @group Firebase
     * @bodyParam user_id int required The ID of the User. Example: 172
     * @bodyParam device_token string required The Device Token of the User's device. Example: eYY72dvZRNOm8RMHzlDxbl:APA91bHuck9a2Tt-zSZvi8_CKyyBAu
     * @response 200 scenario=success
    {
    "multicast_id": 7266664702045533561,
    "success": 1,
    "failure": 0,
    "canonical_ids": 0,
    "results": [
    {
    "message_id": "0:1689337734403152%f483f14ef483f14e"
    }
    ]
    }

     * @response 422 scenario=success
    {
    "status": false,
    "code": 422,
    "message": "Validation Error",
    "data": {
    "user_id": [
    "The user id field is required."
    ],
    "device_token": [
    "The device token field is required."
    ]
    }
    }
     * @response 200 scenario=failure
    {
    "multicast_id": 2217725075382891651,
    "success": 0,
    "failure": 1,
    "canonical_ids": 0,
    "results": [
    {
    "error": "InvalidRegistration"
    }
    ]
    }

     */

    public function tokenExpiredSendLogin(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_token' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(false, 'Validation Error', $validator->errors(), 422);
        }

        try {
            $user = User::find($request->user_id);
            $serverKey = 'AAAA_OxfTQE:APA91bE22wzYji8aEerbkVtrYsbtJFoxM-spY1PTFWw1sf0jo0utgl06sJsvayFuyNo772o1TYjiLz44XcRr1JrrUJ9yvmsDVHqmBgVFa_B_l4uPXpG_tnUIk_BBUuYMjHAGSyvPfxvs';

            $deviceToken = $request->device_token;

            $notification = [
                'title' => 'Login Expired',
                'body' => 'Please tap to login again.',
            ];

            $data = [
                'flag' => "Expired",
            ];

            $payload = [
                'notification' => $notification,
                'data' => $data,
                'to' => $deviceToken,
                'priority' => 'high',

            ];

            $response = Http::withHeaders([
                'Authorization' => 'key=' . $serverKey,
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', $payload);

            Log::channel('mobileApi')->info('tokenExpiredSendLogin: Firebase Token for user ID:' . $user->id, ['data' => $request->device_token]);

            return $response->json();
        } catch (\Throwable $th) {
            return $this->sendResponse(false, $th, null, 400);

            return $th->getMessage();
        }

    }

    /**
     *
     *
     * Permission Denied
     * @group Firebase
     * @bodyParam user_id int required The ID of the User. Example: 172
     * @bodyParam device_token string required The Device Token of the User's device. Example: eYY72dvZRNOm8RMHzlDxbl:APA91bHuck9a2Tt-zSZvi8_CKyyBAu
     * @response 200 scenario=success
    {
    "multicast_id": 7266664702045533561,
    "success": 1,
    "failure": 0,
    "canonical_ids": 0,
    "results": [
    {
    "message_id": "0:1689337734403152%f483f14ef483f14e"
    }
    ]
    }
     * @response 422 scenario=success
    {
    "status": false,
    "code": 422,
    "message": "Validation Error",
    "data": {
    "user_id": [
    "The user id field is required."
    ],
    "device_token": [
    "The device token field is required."
    ]
    }
    }

     * @response 200 scenario=failure
    {
    "multicast_id": 1063299415492231526,
    "success": 0,
    "failure": 1,
    "canonical_ids": 0,
    "results": [
    {
    "error": "NotRegistered"
    }
    ]
    }
     * */

    public function permissionDenied(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_token' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(false, 'Validation Error', $validator->errors(), 422);
        }

        try {
            $user = User::find($request->user_id);
            $serverKey = 'AAAA_OxfTQE:APA91bE22wzYji8aEerbkVtrYsbtJFoxM-spY1PTFWw1sf0jo0utgl06sJsvayFuyNo772o1TYjiLz44XcRr1JrrUJ9yvmsDVHqmBgVFa_B_l4uPXpG_tnUIk_BBUuYMjHAGSyvPfxvs';

            $deviceToken = $request->device_token;

            $notification = [
                'title' => 'Permission Denied',
                'body' => 'Please tap to login again.',
            ];

            $data = [
                'flag' => "AccessDenied",
            ];

            $payload = [
                'notification' => $notification,
                'data' => $data,
                'to' => $deviceToken,
                'priority' => 'high',

            ];

            $response = Http::withHeaders([
                'Authorization' => 'key=' . $serverKey,
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', $payload);

            return $response->json();
        } catch (\Throwable $th) {
            return $this->sendResponse(false, $th, null, 400);

            return $th->getMessage();
        }

    }

    /**
     *
     *
     * New Lead
     * @group Firebase
     * @bodyParam user_id int required The ID of the User. Example: 172
     * @bodyParam device_token string required The Device Token of the User's device. Example: eYY72dvZRNOm8RMHzlDxbl:APA91bHuck9a2Tt-zSZvi8_CKyyBAu
     * @response 200 scenario=success
    {
    "multicast_id": 7266664702045533561,
    "success": 1,
    "failure": 0,
    "canonical_ids": 0,
    "results": [
    {
    "message_id": "0:1689337734403152%f483f14ef483f14e"
    }
    ]
    }
     * @response 422 scenario=success
    {
    "status": false,
    "code": 422,
    "message": "Validation Error",
    "data": {
    "user_id": [
    "The user id field is required."
    ],
    "device_token": [
    "The device token field is required."
    ]
    }
    }

     * @response 200 scenario=failure
    {
    "multicast_id": 1063299415492231526,
    "success": 0,
    "failure": 1,
    "canonical_ids": 0,
    "results": [
    {
    "error": "NotRegistered"
    }
    ]
    }
     * */
    public function lead(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_token' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(false, 'Validation Error', $validator->errors(), 422);
        }

        try {
            $user = User::find($request->user_id);
            $serverKey = 'AAAA_OxfTQE:APA91bE22wzYji8aEerbkVtrYsbtJFoxM-spY1PTFWw1sf0jo0utgl06sJsvayFuyNo772o1TYjiLz44XcRr1JrrUJ9yvmsDVHqmBgVFa_B_l4uPXpG_tnUIk_BBUuYMjHAGSyvPfxvs';

            $deviceToken = $request->device_token;

            $notification = [
                'title' => 'Lead',
                'body' => 'You have a new lead.',
            ];

            $data = [
                'flag' => "CreateLead",
            ];

            $payload = [
                'notification' => $notification,
                'data' => $data,
                'to' => $deviceToken,
                'priority' => 'high',

            ];

            $response = Http::withHeaders([
                'Authorization' => 'key=' . $serverKey,
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', $payload);

            return $response->json();
        } catch (\Throwable $th) {
            return $this->sendResponse(false, $th, null, 400);

            return $th->getMessage();
        }

    }

    /**
     *
     *
     * Battery Low Notification
     * @group Firebase
     * @bodyParam user_id int required The ID of the User. Example: 172
     * @bodyParam device_token string required The Device Token of the User's device. Example: eYY72dvZRNOm8RMHzlDxbl:APA91bHuck9a2Tt-zSZvi8_CKyyBAu
     * @response 200 scenario=success
    {
    "multicast_id": 7266664702045533561,
    "success": 1,
    "failure": 0,
    "canonical_ids": 0,
    "results": [
    {
    "message_id": "0:1689337734403152%f483f14ef483f14e"
    }
    ]
    }

     * @response 422 scenario=success
    {
    "status": false,
    "code": 422,
    "message": "Validation Error",
    "data": {
    "user_id": [
    "The user id field is required."
    ],
    "device_token": [
    "The device token field is required."
    ]
    }
    }
     * @response 200 scenario=failure
    {
    "multicast_id": 2217725075382891651,
    "success": 0,
    "failure": 1,
    "canonical_ids": 0,
    "results": [
    {
    "error": "InvalidRegistration"
    }
    ]
    }

     */

    public function BatteryLowNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_token' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(false, 'Validation Error', $validator->errors(), 422);
        }

        try {
            $user = User::find($request->user_id);
            $serverKey = 'AAAA_OxfTQE:APA91bE22wzYji8aEerbkVtrYsbtJFoxM-spY1PTFWw1sf0jo0utgl06sJsvayFuyNo772o1TYjiLz44XcRr1JrrUJ9yvmsDVHqmBgVFa_B_l4uPXpG_tnUIk_BBUuYMjHAGSyvPfxvs';
            $deviceToken = $request->device_token;
            $notification = [
                'title' => 'Battery Low',
                'body' => 'Please charge your device.',
            ];

            $data = [
                'flag' => "BatteryLow",
            ];

            $payload = [
                'data' => $data,
                'to' => $deviceToken,
                'priority' => 'high',

            ];

            $response = Http::withHeaders([
                'Authorization' => 'key=' . $serverKey,
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', $payload);
            Log::channel('mobileApi')->info('BatteryLowNotification: Firebase Token for user ID:' . $user->id, ['data' => $request->device_token]);
            return $response->json();
        } catch (\Throwable $th) {
            return $this->sendResponse(false, $th, null, 400);
            return $th->getMessage();
        }

    }

}


// $user = User::find($request->user_id);
// $phone = '923000000000';
// $serverKey = 'AAAAUCGKF2c:APA91bHHIFcBDahABv3R8EeizHXhVODRTBp2Vh4KozXh89kyN60U93Q1ckT5XOso6NreuRMP3XdqMyFPxRhDYyCHx_VBsz95w3EjS64ynhD-7DtTGGis6jrl-hWc58R-o_NvAvaGwZxn';
// $deviceToken = $user->device_token;

// $url = 'https://fcm.googleapis.com/fcm/send';
// $headers = [
//     'Content-Type' => 'application/json',
//     'Authorization' => 'key=AAAA_OxfTQE:APA91bE22wzYji8aEerbkVtrYsbtJFoxM-spY1PTFWw1sf0jo0utgl06sJsvayFuyNo772o1TYjiLz44XcRr1JrrUJ9yvmsDVHqmBgVFa_B_l4uPXpG_tnUIk_BBUuYMjHAGSyvPfxvs',
// ];
// $data = [
//     'to' => $deviceToken,
//     'data' => [
//         'message' => 'Number Dial Request',
//         'phoneNumber' => $phone,
//         'name' => 'Muhammad Usman',
//     ],
// ];

// try {
//     $response = Http::withHeaders($headers)->post($url, $data);

//     $statusCode = $response->status();
//     $content = $response->body();

// } catch (\Exception $e) {
//     return $e->getMessage();
// }
