<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Traits\TelegramTrait;
use Auth;
use Illuminate\Http\Request;

class LoginHelperController extends Controller
{
    use TelegramTrait;
    public function telegramLogin(Request $request)
    {

        $email = session('email');
        $password = session('password');
        $otp_count = session('otp_count');
        session(['registration_data' => ['email' => $email, 'password' => $password]]);
        $registrationData = session('registration_data');
        if (!$registrationData) {
            return redirect()->route('login')->with('error', 'Registration data not found.');
        }

        $request->merge($registrationData);

        $remember = ($request->boolean('remember')) ? true : false;
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $remember)) {
            if (auth()->user()->roles()->exists() && auth()->user()->status) {

                if (auth()->user()->telegram2fa_secret == $request->one_time_password) {
                    auth()->user()->telegram2fa_secret = null;

                    auth()->user()->last_login = now();
                    auth()->user()->save();
                    return redirect()->intended();

                } else {

                    $error = array(
                        "email" => "You entered a wrong OTP. Please try again.",
                    );
                    return redirect("login-with-telegram")->withInput()->withErrors($error);

                }

            } elseif (!auth()->user()->status) {
                Auth::logout();
                $error = array(
                    "email" => "Your Account is suspended. Please contact your Manager.",
                );
                return redirect("login")->withInput()->withErrors($error);
            } else {
                Auth::logout();
                $error = array(
                    "email" => "We could not find a role. Please contact the admin.",
                );
                return redirect("login")->withInput()->withErrors($error);
            }
        }

        $error = array(
            "email" => "These credentials do not match our records.",
        );
        return redirect("login")->withInput()->withErrors($error);

        return redirect(route('root'));

    }

    public function loginWithTelegram(Request $request)
    {
        // dd($request->all());
        $email = session('email');
        $password = session('password');
        $otp_count = session('otp_count');

        session(['registration_data' => ['email' => $email, 'password' => $password]]);
        $registrationData = session('registration_data');
        if ($registrationData == null) {
            return redirect()->route('login');
        }
        $request->merge(session('registration_data'));
        // dd($request->all());
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $remember = ($request->boolean('remember')) ? true : false;
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $remember)) {
            if (auth()->user()->roles()->exists() && auth()->user()->status) {
                if ($request->auth_cancel) {
                    auth()->user()->back_up_google2fa_secret = null;
                    auth()->user()->google2fa_secret = null;
                } else {
                    auth()->user()->back_up_google2fa_secret = auth()->user()->google2fa_secret;
                    auth()->user()->google2fa_secret = null;
                }

                auth()->user()->last_login = now();
                auth()->user()->save();

                if ($otp_count == 1) {
                    $this->regenerateOtp();
                    session(['otp_count' => 2]);
                }

                return view('google2fa.telegram');
            } elseif (!auth()->user()->status) {
                Auth::logout();
                $error = array(
                    "email" => "Your Account is suspended. Please contact your Manager.",
                );
                return redirect("login")->withInput()->withErrors($error);
            } else {
                Auth::logout();
                $error = array(
                    "email" => "We could not find a role. Please contact to admin.",
                );
                return redirect("login")->withInput()->withErrors($error);
            }
        }
        $error = array(
            "email" => "These credentials do not match our records.",
        );
        return redirect("login")->withInput()->withErrors($error);

        return $this->login($request);
    }

    public function sendTelegramOtp($id)
    {
        $user = \App\Models\User::find($id);
        $otp = rand(100000, 999999);
        $user->telegram2fa_secret = $otp;
        $user->save();
        $mssage = "Hi, " . $user->first_name . ",\nYour one-time password (OTP) for AX Secondary CRM login is: " . $otp;
        if ($user->telegram_chatid != null) {
            $this->sendTelegramMessage($mssage, [$user->telegram_chatid], null, null, 'notification_bot');
        }
    }

    public function regenerateOtp()
    {

        $this->sendTelegramOtp(auth()->user()->id);
        $result = array('status' => '1', 'msg' => 'Otp sent successfully.');
        return json_encode($result);

    }

    public function skipRegistration(Request $request)
    {
        $email = session('email');
        $password = session('password');
        $otp_count = session('otp_count');

        session(['registration_data' => ['email' => $email, 'password' => $password]]);
        $registrationData = session('registration_data');
        if ($registrationData == null) {
            return redirect()->route('login');
        }
        $request->merge(session('registration_data'));

        $remember = ($request->boolean('remember')) ? true : false;
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $remember)) {
            if (auth()->user()->roles()->exists() && auth()->user()->status) {
                auth()->user()->back_up_google2fa_secret = auth()->user()->google2fa_secret;
                auth()->user()->google2fa_secret = null;
                auth()->user()->last_login = now();
                auth()->user()->save();
                return redirect()->intended();
            } elseif (!auth()->user()->status) {
                Auth::logout();
                $error = array(
                    "email" => "Your Account is suspended. Please contact your Manager.",
                );
                return redirect("login")->withInput()->withErrors($error);
            } else {
                Auth::logout();
                $error = array(
                    "email" => "We could not find a role. Please contact to admin.",
                );
                return redirect("login")->withInput()->withErrors($error);
            }
        }
        $error = array(
            "email" => "These credentials do not match our records.",
        );
        return redirect("login")->withInput()->withErrors($error);

        return $this->login($request);
    }

    public function goBack(Request $request)
    {
        $email = session('email');
        $password = session('password');
        $otp_count = session('otp_count');
        session(['registration_data' => ['email' => $email, 'password' => $password]]);
        $registrationData = session('registration_data');
        if ($registrationData == null) {
            return redirect()->route('login');
        }
        $request->merge(session('registration_data'));

        $findUser = \App\Models\User::where('email', $email)->first();
        if ($findUser && $findUser->back_up_google2fa_secret) {
            $findUser->google2fa_secret = $findUser->back_up_google2fa_secret;
            $findUser->telegram2fa_secret = null;
            $findUser->save();
            return redirect()->route('login');
        } else {
            $google2fa = app('pragmarx.google2fa');
            $registration_data = $request->all();
            $registration_data["google2fa_secret"] = $google2fa->generateSecretKey();
            $request->session()->flash('registration_data', $registration_data);
            $QR_Image = $google2fa->getQRCodeInline(
                config('app.name'),
                $registration_data['email'],
                $registration_data['google2fa_secret']
            );
            $find_user = \App\Models\User::where('email', $registration_data['email'])->first();
            if ($find_user && $find_user->back_up_google2fa_secret) {
                $find_user->telegram2fa_secret = null;
                $find_user->google2fa_secret = $find_user->back_up_google2fa_secret;
                $find_user->save();
            }
            if ($find_user && $find_user->google2fa_secret == null) {
                $find_user->telegram2fa_secret = null;
                $find_user->google2fa_secret = $registration_data['google2fa_secret'];
                $find_user->save();
                return view('google2fa.register', ['QR_Image' => $QR_Image, 'secret' => $registration_data['google2fa_secret']]);
            }
        }
        return redirect()->route('login');

    }

    public function regenerateQr()
    {
        if (auth()->user()) {
            auth()->user()->google2fa_secret = null;
            auth()->user()->back_up_google2fa_secret = null;
            auth()->user()->save();
            Auth::logout();
            return redirect()->route('login');
        } else {
            return redirect()->route('login');
        }
    }
}
