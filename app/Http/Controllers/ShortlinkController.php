<?php

namespace App\Http\Controllers;

use App\Models\AuthToken;
use App\Models\BulkMessage;
use App\Models\Lead;
use App\Models\LoginAttempt;
use App\Models\Shortlink;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ShortlinkController extends Controller
{
    public function linkOpener($slug)
    {
        try {

            // $slug = Shortlink::where('slug', $slug)->firstOrFail();
            // find the latest one
            $slug = Shortlink::where('slug', $slug)->latest()->firstOrFail();

            if ($slug) {
                $slug->count = $slug->count + 1;

                if ($slug->opted_in == null) {
                    $slug->opted_in = Carbon::now();
                }
                $slug->save();

                $lead = Lead::findOrFail($slug->lead_id);
                if ($lead->pf_tracking_link) {
                    if ($lead->lead_status_id == 1) {
                        $lead->lead_status_id = 5; //Contacted
                        $lead->save();
                    }
                    return redirect($slug->link);
                } else {
                    $msg = str_replace("\r", "", $slug->message);
                    $url = $slug->link . '?text=' . $msg;
                    $str = str_replace("\n", "", $url);
                    if ($lead->lead_status_id == 1) {
                        $lead->lead_status_id = 5; // Contacted
                        $lead->save();
                    }
                    return redirect($str);
                }
            }
        } catch (\Throwable $th) {
            abort(500, 'Something went wrong', ['error' => $th->getMessage(), 'file' => $th->getFile(), 'line' => $th->getLine()]);
        }
    }

    public function bitrixLogin($slug, Request $request)
    {
        try {

            $token = AuthToken::where('token', $slug)->where('is_active', true)->first();
            if ($token) {
                $user = User::find($token->user_id);
                if ($user) {
                    Auth::guard('web')->logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    $token->is_active = false;
                    $token->save();
                    $user->status = 1;

                    if ($user->roles()->exists() && $user->status) {
                        $user->back_up_google2fa_secret = $user->google2fa_secret;
                        $user->google2fa_secret = null;
                        $user->save();
                    }

                    $user->save();
                    Auth::login($user);

                    return redirect()->intended();

                    LoginAttempt::create([
                        'ip_address' => $request->ip(),
                        'email' => $user->email,
                        'medium' => 'Bitrix',
                        'status' => 'success',
                    ]);

                    if (auth()->user()->roles()->exists()) {
                        return redirect()->intended();
                    } else {
                        Auth::logout();
                        $error = array(
                            "email" => "We could not find a role. Please contact to admin.",
                        );
                        return redirect("login")->withInput()->withErrors($error);
                    }

                    return redirect('/');
                }
            } else {
                return redirect('/');
            }

        } catch (\Throwable $th) {
            Log::channel('discord')->error("Error in bitrixLogin - Shortlink Controller =>" . $th->getMessage());
            abort(500, 'Something went wrong', ['error' => $th->getMessage(), 'file' => $th->getFile(), 'line' => $th->getLine()]);
        }
    }

    public function ContactsLogin($slug, Request $request)
    {
        try {

            $token = AuthToken::where('token', $slug)->where('is_active', true)->first();
            if ($token) {
                $user = User::find($token->user_id);
                if ($user) {
                    Auth::guard('web')->logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    $token->is_active = false;
                    $token->save();
                    $user->status = 1;
                    $user->save();

                    Auth::login($user);

                    LoginAttempt::create([
                        'ip_address' => $request->ip(),
                        'email' => $user->email,
                        'medium' => 'Ax Contacts',
                        'status' => 'success',
                    ]);

                    if (auth()->user()->roles()->exists()) {
                        return redirect()->intended();
                    } else {
                        Auth::logout();
                        $error = array(
                            "email" => "We could not find a role. Please contact to admin.",
                        );
                        return redirect("login")->withInput()->withErrors($error);
                    }
                    return redirect('/');
                }
            } else {
                return redirect('/');
            }

        } catch (\Throwable $th) {
            Log::channel('discord')->error("Error in ContactsLogin - Shortlink Controller =>" . $th->getMessage());
            abort(500, 'Something went wrong', ['error' => $th->getMessage(), 'file' => $th->getFile(), 'line' => $th->getLine()]);
        }
    }
    public function smslinkClicked($slug, Request $request)
    {

        try {
            $sms = BulkMessage::where('short_url', $slug)->first();
            if ($sms) {
                if ($sms->confirmed) {
                    if ($sms->custom_link) {
                        return redirect($sms->custom_link);
                    } else {
                        return view('admin.shortlink.contact_sms_verification');
                    }
                }
                $sms->status = true;
                $sms->confirmed = true;
                $sms->link_opened_at = Carbon::now();
                $sms->save();
                if ($sms->custom_link) {
                    return redirect($sms->custom_link);
                } else {
                    return view('admin.shortlink.contact_sms_verification');
                }

            }

        } catch (\Throwable $th) {
            Log::channel('discord')->error("Error in smslinkClicked - Shortlink Controller =>" . $th->getMessage());
            abort(500, 'Something went wrong', ['error' => $th->getMessage(), 'file' => $th->getFile(), 'line' => $th->getLine()]);
        }
    }

    public function smsShortLink($slug, Request $request)
    {

        try {
            $sms = BulkMessage::where('short_url', $slug)->first();
            if ($sms) {
                if ($sms->confirmed) {
                    return view('admin.shortlink.contact_sms_verification');
                }
                $sms->status = true;
                $sms->confirmed = true;
                $sms->link_opened_at = Carbon::now();
                $sms->save();
                return view('admin.shortlink.contact_sms_verification');
            }

        } catch (\Throwable $th) {
            Log::channel('discord')->error("Error in smslinkClicked - Shortlink Controller =>" . $th->getMessage());
            abort(500, 'Something went wrong', ['error' => $th->getMessage(), 'file' => $th->getFile(), 'line' => $th->getLine()]);
        }
    }

}
