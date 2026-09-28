<?php

namespace App\Http\Controllers\Api\V1\Firebase;

use App\Http\Controllers\Api\BaseController;
use App\Models\User;
use App\Models\UserFcm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class FcmApiController extends BaseController
{
    /**
     *
     *
     * Store Secondary App  Device Token
     * @group Firebase
     * @bodyParam fcm_token string required The FCM token of the Agents Device. Example: a3sd4c3s
     * @bodyParam user_id int required The ID of the User. Example: 12
     * @response 200 scenario=success
    {
    "status": true,
    "code": 200,
    "message": "Fcm token saved.",
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
    "fcm_token": [
    "The FCM token field is required."
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
            'fcm_token' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(false, 'Validation Error', $validator->errors(), 422);
        }
        try {
            $user = User::find($request->user_id);
            if ($user) {
                $findFcm = UserFcm::where('user_id', $user->id)->where('fcm_token', $request->fcm_token)->where('andriod_id',$request->andriod_id)->first();
                if ($findFcm) {
                    return $this->sendResponse(true, 'FCM token already uploaded.', null, 200);
                }else if (UserFcm::where('fcm_token', $request->fcm_token)->where('andriod_id',$request->andriod_id)->first()){
                    $fcm = UserFcm::where('fcm_token', $request->fcm_token)->where('andriod_id',$request->andriod_id)->first();
                    $fcm->user_id = $user->id;
                    // $fcm->fcm_token = $request->fcm_token;
                    // $fcm->andriod_id = $request->andriod_id;
                    $fcm->save();
                } else {
                    $fcm = new UserFcm();
                    $fcm->user_id = $user->id;
                    $fcm->fcm_token = $request->fcm_token;
                    $fcm->andriod_id = $request->andriod_id;
                    $fcm->save();
                }

                // if ($findFcm) {
                //     $fcm = $findFcm;
                // } else {
                //     $fcm = new UserFcm();
                // }
                // $fcm->user_id = $user->id;
                // $fcm->fcm_token = $request->fcm_token;
                // $fcm->save();

            }

            Log::channel('mobileApi')->info('FcmApiController: FCM Token for user ID:' . $user->id, ['data' => $request->fcm_token]);

            return $this->sendResponse(true, 'FCM token saved.', null, 200);
        } catch (\Throwable $th) {
            return $this->sendResponse(false, 'Something went wrong.', [
                'error' => $th->getMessage(),
            ], 500);
        }

    }

    // public function sendNotificationToMobile(Request $request)
    // {

    //     $validator = Validator::make($request->all(), [
    //         'user_id' => 'required|exists:users,id',
    //         'phone' => 'required',
    //         'name' => 'nullable',
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->sendResponse(false, 'Validation Error', $validator->errors(), 422);
    //     }
    //     $fcm = UserFcm::where('user_id', $user->id)->first();
    //     if ($fcm) {
    //         try {
    //             $user = User::find($request->user_id);
    //             $phone = $request->phone;
    //             $name = $request->name ?? '';
    //             $serverKey = 'AAAA_OxfTQE:APA91bE22wzYji8aEerbkVtrYsbtJFoxM-spY1PTFWw1sf0jo0utgl06sJsvayFuyNo772o1TYjiLz44XcRr1JrrUJ9yvmsDVHqmBgVFa_B_l4uPXpG_tnUIk_BBUuYMjHAGSyvPfxvs';

    //             $deviceToken = $fcm->fcm_token;

    //             $notification = [
    //                 'title' => 'Dial Number Request',
    //                 'body' => 'You have a dial number request for ' . $name . ' ' . $phone . '.',
    //             ];

    //             $data = [
    //                 'phone' => $phone,
    //                 'name' => $name,
    //                 'token' => $request->input('device_token'),
    //             ];

    //             $payload = [
    //                 'notification' => $notification,
    //                 'data' => $data,
    //                 'to' => $deviceToken,
    //                 'priority' => 'high',

    //             ];

    //             $response = Http::withHeaders([
    //                 'Authorization' => 'key=' . $serverKey,
    //                 'Content-Type' => 'application/json',
    //             ])->post('https://fcm.googleapis.com/fcm/send', $payload);

    //             return $response->json();
    //         } catch (\Throwable $th) {
    //             return $this->sendResponse(false, $th, null, 400);

    //             return $th->getMessage();
    //         }
    //     }
    // }
}
