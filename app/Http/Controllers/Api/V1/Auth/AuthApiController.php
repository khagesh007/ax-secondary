<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\UserAppLogin;
use App\Traits\TelegramTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Validator;

class AuthApiController extends BaseController
{
    use TelegramTrait;

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'forgetPassword', 'verifyOtp', 'setNewPassword', 'resendOtp']]);
    }
    use TelegramTrait;

    /**
     *
     *
     *Login
     * @group Auth
     * @bodyParam email email required The email of the User. Example: abc@axcapital.ae
     * @bodyParam password string required The password of the User. Example: Pa$$w0rd
     * @response 200 scenario=success
     * {
    "status": true,
    "code": 200,
    "message": "Login successful",
    "data": {
    "token": "2|jA7PfVl1hTqVUVkcbyPSASkJTBIOulXwSpolWnBl",
    "user": {
    "id": 1,
    "name": "Saim Aziz",
    "full_name": "Saim Aziz",
    "first_name": "Saim",
    "last_name": "Aziz",
    "position": null,
    "email": "s.mirza@axcapital.ae",
    "secondary_email": "s.mirza@axcapital.ae",
    "mobile": null,
    "thumbnail": null,
    "image": null,
    "avatar": "avatar-1.jpg",
    "telegram_username": null,
    "telegram_chatid": null,
    "status": 1
    }
    }
     *
     * @response 401 scenario="not found"
     * {
    "status": false,
    "code": 401,
    "message": "User not found or account is disabled",
    "data": null
     */

    public function login(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ]);
            if ($validator->fails()) {
                return $this->sendResponse(false, 'Validation failed', $validator->errors(), 422);
            }



            if (User::where('email', $request->email)->whereNot('status', '1')->first()) {
                $user = User::where('email', $request->email)->first();
                $user->tokens()->delete();
                return $this->sendResponse(false, 'User not found or account is disabled', null, 401);
            }

            if (User::where('email', $request->email)->where('temporary_disable',1)->first()) {
                $user = User::where('email', $request->email)->first();
                $user->tokens()->delete();
                return $this->sendResponse(false, 'User not found or account is Temporary disabled', null, 401);
            }

            $user = User::where('email', $request->email)->where('status', '1')->first();
            if (!$user) {
                return $this->sendResponse(false, 'User not found or account is disabled', null, 401);
            }
            // if (!$user->hasRole(['Admin', 'Super Admin']) && $user->api_access == 0) {
            //     return $this->sendResponse(false, 'You don\'t have permission!!', null, 401);
            // }

            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $authUser = Auth::user();
                $data['token'] = $authUser->createToken('user_app')->plainTextToken;
                $data['user'] = new UserResource($authUser);

                // $user = \App\Models\User::find($authUser->id);
                // $otp = rand(100000, 999999);
                // $user->telegram_otp = $otp;
                // $user->save();
                // $mssage = "Hi, " . $user->first_name . ",\nYour one-time password (OTP) for AX Secondary CRM login is: " . $otp;
                // if ($user->telegram_chatid != null) {
                //     $this->sendTelegramMessage($mssage, [$user->telegram_chatid], null, null, 'notification_bot');
                // }

                return $this->sendResponse(true, 'Login successful', $data, 200);
            } else {
                return $this->sendResponse(false, 'Unable to login', null, 401);
            }
        } catch (\Exception $e) {
            Log::channel('discord')->error("Error in login function in AuthApiController.php " . $e->getMessage());
            return $this->sendResponse(false, 'Unable to login', null, 500);
        }
    }
    /**
     * Logout
     *
     * Logout current session and invalidate the token.
     * @group Auth
     * @authenticated
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @authenticated
     * @response 200 scenario="Successful Logout"
     * {
    "status": true,
    "code": 200,
    "message": "Logout successful",
    "data": null
    }
     * */
    public function logout(Request $request)
    {
        try {
            auth()->user()->currentAccessToken()->delete();
            return $this->sendResponse(true, 'Logout successful', null, 200);
        } catch (\Exception $e) {
            Log::channel('discord')->error("Error in login function in AuthApiController.php " . $e->getMessage());
            return $this->sendResponse(false, 'Unable to logout', null, 500);
        }
    }

    /**
     * Agent Status
     *
     * Check Agent Status
     * @group Auth
     * @authenticated
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @authenticated
     * @response 200 scenario="Role Agent"
    {
    "status": true,
    "code": 200,
    "message": "Agent",
    "data": {
    "role": "Agent"
    },
    "page": 1,
    "per_page": 10
    }
     * @response 200 scenario="Role Not Agent"
    {
    "status": true,
    "code": 200,
    "message": "Agent",
    "data": {
    "role": "Not an Agent"
    },
    "page": 1,
    "per_page": 10
    }
     * */
    public function checkAgentStatus(Request $request)
    {
        try {
            if (auth()->user()->hasRole(['Agent'])) {
                return $this->sendResponse(true, 'Agent', [
                    'role' => 'Agent',
                ], 200);
            } else {
                return $this->sendResponse(false, 'Not an Agent', [
                    'role' => 'Not an Agent',
                ], 200);
            }

            return $this->sendResponse(true, 'Logout successful', null, 200);
        } catch (\Exception $e) {
            Log::channel('discord')->error("Error in login function in AuthApiController.php " . $e->getMessage());
            return $this->sendResponse(false, 'Unable to logout', null, 500);
        }
    }

    /**
     *
     *
     * Forget Password
     * @group Auth
     * @bodyParam email email required The email of the User. Example: abc@axcapital.ae
     * @response 200 scenario=success
    {
    "status": true,
    "code": 200,
    "message": "OTP sent to your Telegram",
    "data": null,
    "page": 1,
    "per_page": 10
    }
     *
     * @response 401 scenario="not found"
     * {
    "status": false,
    "code": 401,
    "message": "User not found or account is disabled",
    "data": null
     */

    public function forgetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);
        if ($validator->fails()) {
            return $this->sendResponse(false, 'Validation failed', $validator->errors(), 422);
        }

        $user = User::where('email', $request->email)->where('status', 1)->first();
        if (!$user) {
            return $this->sendResponse(false, 'User not found or account is disabled', null, 401);
        }
        $otp = mt_rand(100000, 999999);
        $user->otp = $otp;
        $user->save();

        $message = "<b>AX Secondary CRM</b>\n\n";
        $message .= "A password change request has been generated from the mobile application.\n\n";
        $message .= "Your password reset OTP is: <strong>" . $otp . "</strong>";

        $this->sendTelegramMessageHtml($message, [$user->telegram_chatid], null, 'Password Change', 'notification_bot');
        return $this->sendResponse(true, 'OTP sent to your email', null, 200);
    }

    /**
     *
     *
     * Verify Otp
     * @group Auth
     * @bodyParam email email required The email of the User. Example: abc@axcapital.ae
     * @bodyParam otp in required The otp that you will receive on the User Telegram. Example: 8986

     * @response 200 scenario=success
    {
    "status": true,
    "code": 200,
    "message": "OTP verified",
    "data": null,
    "page": 1,
    "per_page": 10
    }
     *
     * @response 401 scenario="not found"
    {
    "status": false,
    "code": 401,
    "message": "Invalid OTP",
    "data": null,
    "page": 1,
    "per_page": 10
    }
     */

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendResponse(false, 'Validation failed', $validator->errors(), 422);
        }

        $user = User::where('email', $request->email)->where('otp', $request->otp)->first();
        if (!$user) {
            return $this->sendResponse(false, 'Invalid OTP', null, 401);
        }
        // $user->otp = null;
        // $user->save();
        return $this->sendResponse(true, 'OTP verified', null, 200);
    }

    /**
     *
     *
     * Verify Otp
     * @group Auth
     * @bodyParam email email required The email of the User. Example: abc@axcapital.ae
     * @bodyParam otp in required The otp that you will receive on the User Telegram. Example: 8986
     * @bodyParam password in string The new password that you want to set. Example: Pa$$w0rd
     * @bodyParam confirm_password in string The new password that you want to set. Example: Pa$$w0rd

     * @response 200 scenario=success
    {
    "status": true,
    "code": 200,
    "message": "Password updated",
    "data": null,
    "page": 1,
    "per_page": 10
    }
     *
     * @response 401 scenario="not found"
    {
    "status": false,
    "code": 401,
    "message": "User not found or account is disabled",
    "data": null
    }
     * @response 422 scenario="Validation failed"

    {
    "status": false,
    "code": 422,
    "message": "Validation failed",
    "data": {
    "confirm_password": [
    "The confirm password and password must match."
    ]
    },
    "page": 1,
    "per_page": 10
    }
     */
    public function setNewPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
            'otp' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendResponse(false, 'Validation failed', $validator->errors(), 422);
        }

        $user = User::where('email', $request->email)->where('otp', $request->otp)->first();
        if (!$user) {
            return $this->sendResponse(false, 'User not found', null, 401);
        }
        $user->password = bcrypt($request->password);
        $user->otp = null;
        $user->save();

        $message = "<b>AX Secondary CRM</b>\n\n";
        $message .= "Your password has been changed successfully.\n\n";
        $url = 'https://secondary.axcrm.ae/login';
        $this->sendTelegramMessageHtml($message, [$user->telegram_chatid], $url, 'Login', 'notification_bot');
        return $this->sendResponse(true, 'Password updated', null, 200);
    }

    public function resendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);
        if ($validator->fails()) {
            return $this->sendResponse(false, 'Validation failed', $validator->errors(), 422);
        }

        $user = User::where('email', $request->email)->where('status', 1)->first();
        if (!$user) {
            return $this->sendResponse(false, 'User not found or account is disabled', null, 401);
        }
        $otp = mt_rand(100000, 999999);
        $user->otp = $otp;
        $user->save();

        $message = "<b>AX Secondary CRM</b>\n\n";
        $message .= "A password change request has been generated from the mobile application.\n\n";
        $message .= "Your password reset OTP is: <strong>" . $otp . "</strong>";
        $this->sendTelegramMessageHtml($message, [$user->telegram_chatid], null, 'Password Change', 'notification_bot');
        return $this->sendResponse(true, 'OTP sent to your email', null, 200);
    }

    public function saveLoginInfo(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'latitude' => 'required',
                'longitude' => 'required',
                'location_name' => 'required',
                'device_name' => 'required',
                'device_os' => 'required',
                'device_version' => 'required',
                'app_version' => 'required',
            ]);
            if ($validator->fails()) {
                return $this->sendResponse(false, 'Validation failed', $validator->errors(), 422);
            }


            UserAppLogin::create([
                'user_id' => \auth()->id(),
                'ip_address' => $request->ip(),
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'location_name' => $request->location_name,
                'device_name' => $request->device_name,
                'device_os' => $request->device_os,
                'device_version' => $request->device_version,
                'app_version' => $request->app_version,
            ]);


            return $this->sendResponse(true, 'Login info saved successful.', [], 200);
        } catch (\Exception $e) {
            Log::channel('discord')->error("Error in saveLoginInfo function in AuthApiController.php " . $e->getMessage());
            return $this->sendResponse(false, 'Something went wrong.', null, 500);
        }
    }
}
