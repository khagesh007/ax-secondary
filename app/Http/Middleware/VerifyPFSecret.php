<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VerifyPFSecret
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
        // You can get secret from header or query param:
        $secret = $request->header('X-Webhook-Secret') ?? $request->query('secret');

        // You can store the secret in .env or config
        $validSecret = config('services.propertyfinder.webhook_key');
        if (!$secret || $secret !== $validSecret) {
            return response()->json(['error' => 'Unauthorized - invalid secret key'], 401);
        }

        return $next($request);
    }
}
