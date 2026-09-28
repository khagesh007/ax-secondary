<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckTelegramApiToken
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
        {
            // Check if the user is authenticated
            if (!Auth::check()) {
                return response()->json([
                    'error' => 'Unauthenticated',
                    'message' => 'You must be logged in to access this resource.',
                ], 401); // 401 Unauthorized
            }

            // Check if the authenticated user has an empty telegram_token
            if (empty(Auth::user()->telegram_api_token)) {
                return response()->json([
                    'error' => 'Missing Telegram Token',
                    'message' => 'Please add your Telegram token to proceed.',
                ], 403); // 403 Forbidden
            }

            return $next($request);
        }
    }
}
