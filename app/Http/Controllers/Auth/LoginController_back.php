<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
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

        $google2fa = app('pragmarx.google2fa');

        $registration_data = $request->all();

        $registration_data["google2fa_secret"] = $google2fa->generateSecretKey();

        $request->session()->flash('registration_data', $registration_data);

        $QR_Image = $google2fa->getQRCodeInline(
            config('app.name'),
            $registration_data['email'],
            $registration_data['google2fa_secret']
        );

        return view('auth.google2fa.qr', ['QR_Image' => $QR_Image, 'secret' => $registration_data['google2fa_secret']]);

        $remember = ($request->boolean('remember')) ? true : false;
        $credentials = $request->only('email', 'password');
        // $credentials['status'] = 1;
        //dd(Auth::attempt($credentials, $remember));
        if (Auth::attempt($credentials, $remember)) {
            if (auth()->user()->roles()->exists() && auth()->user()->status) {
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

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function completeRegistration(Request $request)
    {
        $request->merge(session('registration_data'));

        return $this->login($request);
    }



}
