<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TelegramOuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(auth()->user()->telegram2fa_secret != null ){
            return redirect()->route('2fa.telegram');
        }
        return $next($request);


    }
}
