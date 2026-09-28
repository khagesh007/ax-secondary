<?php

namespace App\Providers;

use App\Events\RequestLimitExceeded;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    // protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));
            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/ajax.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/misc.php'));
            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/reporting.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/thirdparty.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));

//            Route::prefix('api/pfv1')
//                ->middleware('api')
//                ->namespace($this->namespace)
//                ->group(base_path('routes/pf/v1.php')); // 👈 updated path
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
        RateLimiter::for('admin_rate', function (Request $request) {
            return Limit::perMinute(500)->by(optional($request->user())->id ?: $request->ip());
        });
        RateLimiter::for('agent_rate', function (Request $request) {
            return Limit::perMinute(auth()->user()->api_rate_limit > 0 ? auth()->user()->api_rate_limit : 500)->response(function (Request $request, array $headers) {
                $full_url = $request->fullUrl();
                $url = explode('?', $full_url)[0];
                //  $ip = $request->ip();
//                $previous_request = CacheFacade::get($url.':'.$ip) ?? 0;
//                CacheFacade::put($url.':'.$ip,$previous_request+1,now()->addMinutes(2));
//                if($previous_request > 10) {

                $data['request'] = $request;
                $data['user'] = auth()->user() ?? null;
                $data['url'] = $url;
                event(new RequestLimitExceeded($data));
                //  }
                return response()->json([
                    'status' => false,
                    'code' => 429,
                    'message' => 'Too many request...',
                    'data' => []], 429, $headers);

            });
        });

    }
}
