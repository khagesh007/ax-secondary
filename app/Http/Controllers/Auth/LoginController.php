<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Traits\TelegramTrait;
use Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use TelegramTrait;

    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
     */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);


        session(['email' => $request->email]);
        session(['password' => $request->password]);
        session(['otp_count' => 1]);

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
        if($find_user && $find_user->back_up_google2fa_secret){
            $find_user->google2fa_secret = $find_user->back_up_google2fa_secret;
            $find_user->telegram2fa_secret = null;
            $find_user->save();
        }
        if ($find_user && $find_user->google2fa_secret == null) {
            $find_user->google2fa_secret = $registration_data['google2fa_secret'];
            $find_user->telegram2fa_secret = null;
            $find_user->save();
            return view('google2fa.register', ['QR_Image' => $QR_Image, 'secret' => $registration_data['google2fa_secret']]);
        }

        $remember = ($request->boolean('remember')) ? true : false;
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $remember)) {
            if (auth()->user()->roles()->exists() && auth()->user()->status) {
                if (auth()->user()->temporary_disable==true) {
                    Auth::logout();
                    $error = array(
                        "email" => "Your Account is suspended. Please contact your Manager.",
                    );
                    return redirect("login")->withInput()->withErrors($error);
                }

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
    }

    public function showLoginForm(Request $request)
    {
        $data['email'] = '';
        $email = $request->email;
        if ($email) {
            $data['email'] = $email;
        }
        return view('auth.login', ['email' => $data['email']]);
    }



    public function completeRegistration(Request $request)
    {
        $registrationData = session('registration_data');

        if (!$registrationData) {
            return redirect()->route('login')->with('error', 'Registration data not found.');
        }

        $request->merge($registrationData);

        $remember = ($request->boolean('remember')) ? true : false;
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $remember)) {
            if (auth()->user()->roles()->exists() && auth()->user()->status) {

                auth()->user()->google2fa_secret = $request->google2fa_secret;
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
                    "email" => "We could not find a role. Please contact the admin.",
                );
                return redirect("login")->withInput()->withErrors($error);
            }
        }

        $error = array(
            "email" => "These credentials do not match our records.",
        );
        return redirect("login")->withInput()->withErrors($error);
    }

    public function verification(Request $request)
    {
        // get previous link
        // $previous = url()->previous();


        $registrationData = session('registration_data');
        if (!$registrationData) {
            return redirect()->route('login')->with('error', 'Registration data not found.');
        }

        $request->merge($registrationData);

        $remember = ($request->boolean('remember')) ? true : false;
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $remember)) {
            if (auth()->user()->roles()->exists() && auth()->user()->status) {

                auth()->user()->google2fa_secret = $request->google2fa_secret;
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


}
