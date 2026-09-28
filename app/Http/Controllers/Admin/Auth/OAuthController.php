<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Models\UserOAuthToken;
use Google\Service\Calendar;
use Laravel\Socialite\Facades\Socialite;

use App\Http\Controllers\Controller;
use App\Traits\OAuthTrait;
use Illuminate\Http\Request;

class OAuthController extends Controller
{
    use OAuthTrait;

    public function authenticate()
    {
        return Socialite::driver("google")
            ->setScopes([Calendar::CALENDAR, Calendar::CALENDAR_EVENTS])
            ->with(["access_type" => "offline", "prompt" => "consent select_account"]) //Remove this line if you don't need refresh token
            ->redirect();
    }

    public function authenticateedCallback(Request $request)
    {
        $accessToken = $this->getGoogleAccessToken($request->code);

        $this->updateUserOAuthToken($accessToken, auth()->id());

        return redirect()->route("root")->with(["status", "Authenticated successfully."]);
        // return redirect()->route("root.v2")->with(["status", "Authenticated successfully."]);
    }
}
