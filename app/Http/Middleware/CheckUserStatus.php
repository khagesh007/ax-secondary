<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckUserStatus
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->status == false) {
            Auth::logout();
            return redirect()->route('login')->with('status', 'Your account is inactive. Please log in again.');
        }

        return $next($request);
    }
}
