<?php

namespace App\Listeners;

use App\Models\RequestLog;
use Illuminate\Auth\Events\Failed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Crypt;
class RecordFailedLoginAttempt
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Failed  $event
     * @return void
     */
    public function handle(Failed $event)
    {
        $content['user_agent'] = request()->header('User-Agent');
        $request = request()->all();
        $request['password'] = Crypt::encryptString($request['password']);
        $content['payload'] = $request;
        $content['response'] = request()->response;
        $content['full_url'] = request()->fullUrl();
        $url = explode('?',request()->fullUrl())[0];
        $log = new RequestLog();
        $log->slug = 'failed_login_attempt';
        $log->user_id = $event->user->id ?? null;
        $log->ip_address = request()->ip();
        $log->url = $url;
        $log->method = request()->method();
        $log->content = json_encode($content);
        $log->response_code = 401;
        $log->save();
    }
}
