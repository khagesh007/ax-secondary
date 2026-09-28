<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Auth;
use Studio\Totem\Totem;
use App\Rules\NoPortraitImage;
use Illuminate\Support\Facades\Validator;
use App\Models\LeadNote;
use App\Observers\LeadNoteObserver;
use App\Models\Event;
use App\Observers\EventObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        foreach (glob(app_path() . '/Helpers/*.php') as $file) {
            require_once($file);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //

        Validator::extend('no_portrait_image', NoPortraitImage::class);
        Schema::defaultStringLength(191);
        Totem::auth(function($request) {
            // return true / false . For e.g.
            return Auth::user()->hasRole('Super Admin');
        });
        LeadNote::observe(LeadNoteObserver::class);
        Event::observe(EventObserver::class);

    }
}
