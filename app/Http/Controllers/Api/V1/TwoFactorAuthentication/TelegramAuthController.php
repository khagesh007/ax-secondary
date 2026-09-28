<?php

namespace App\Http\Controllers\Api\V1\TwoFactorAuthentication;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Auth;
use App\Models\User;

class TelegramAuthController extends BaseController
{
    public function verifyToken(Request $request){
        $validator = Validator::make($request->all(), [
            "otp" => "required",
            'user_id' => 'required|exists:users,id',
        ]);
        if ($validator->fails()) {
            return $this->sendResponse(false, 'Validation Error', $validator->errors(), 422);
        }
        // dd(auth()->user()->telegram_otp);
        $user = User::where('id', $request->user_id)->first();
        if($user->telegram_otp == $request->otp){
            $user->telegram_otp = null;
            $user->telegram_api_token = rand(100000, 999999);
            $user->save();
            return $this->sendResponse(true, 'OTP Verified Successfully', ['telegram_api_token' => $user->telegram_api_token], 200);
        }else{
            $user->telegram_api_token = null;
            $user->save();
            return $this->sendResponse(false, 'Invalid OTP', [], 422);
        }
    }


    public function resentOtp(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);
        if ($validator->fails()) {
            return $this->sendResponse(false, 'Validation Error', $validator->errors(), 422);
        }

        $user = \App\Models\User::find($request->user_id);
        $otp = rand(100000, 999999);
        $user->telegram_otp = $otp;
        $user->save();
        $mssage = "Hi, " . $user->first_name . ",\nYour one-time password (OTP) for AX Secondary CRM login is: " . $otp;
        if ($user->telegram_chatid != null) {
            $this->sendTelegramMessage($mssage, [$user->telegram_chatid], null, null, 'notification_bot');
        }
        return $this->sendResponse(true, 'OTP Sent Successfully');

    }
}
