<?php

namespace App\Http\Controllers\Api\V1\TwoFactorAuthentication;

use App\Http\Controllers\Api\BaseController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TwoFaController extends BaseController
{
    public function generateToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'auth_status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(false, 'Validation Error', $validator->errors(), 422);
        }

        $user = User::where('email', $request->email)->first();
        // dd($user);
        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        return response()->json([
            'token' => $token,
        ]);

    }

    public function verifyToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(false, 'Validation Error', $validator->errors(), 422);
        }

        $user = User::where('email', $request->email)->where('api_access_token', $request->token)->first();

        if (!$user) {
            return $this->sendResponse(false, 'Authentication Error', [], 404);

        }

        return $this->sendResponse(true, 'Authentication Success', ['token' => $request->token], 200);
    }

    public function enableTwoFactor(Request $request)
    {
        $user = Auth::user();

        // Generate a new secret
        $google2fa = app('pragmarx.google2fa');

        $user->google2fa_secret = $google2fa->generateSecretKey();
        $user->two_factor_enabled = true;
        $user->save();

        // Generate the QR code URL
        $QR_Image = $google2fa->getQRCodeInline(
            config('app.name'),
            $user->email,
            $user->google2fa_secret
        );

        return response()->json([
            'QR_Image' => $QR_Image,
            'secret' => $user->google2fa_secret,
        ]);
    }

    public function verifyTwoFactor(Request $request)
    {
        $request->validate([
            'two_factor_code' => 'required',
        ]);

        $user = Auth::user();
        $google2fa = app('pragmarx.google2fa');

        $valid = $google2fa->verifyKey($user->google2fa_secret, $request->input('two_factor_code'));

        if ($valid) {
            return response()->json(['message' => '2FA code is valid.']);
        } else {
            return response()->json(['message' => 'Invalid 2FA code.'], 422);
        }
    }

    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if ($user->two_factor_enabled) {
            $twoFactorCode = $request->header('X-2FA-CODE');

            if (!$twoFactorCode) {
                return response()->json(['message' => '2FA code is required.'], 403);
            }

            $google2fa = app('pragmarx.google2fa');

            if (!$google2fa->verifyKey($user->google2fa_secret, $twoFactorCode)) {
                return response()->json(['message' => 'Invalid 2FA code.'], 403);
            }
        }

        return $next($request);
    }

}
