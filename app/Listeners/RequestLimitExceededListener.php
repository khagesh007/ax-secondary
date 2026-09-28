<?php

namespace App\Listeners;

use App\Events\RequestLimitExceeded;
use App\Models\RequestLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RequestLimitExceededListener
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
     * @param  \App\Events\RequestLimitExceeded  $event
     * @return void
     */
    public function handle(RequestLimitExceeded $event)
    {
        $req = $event->data['request'] ?? null;
        if(!$req) return;
        $content['user_agent'] = $req->header('User-Agent');
        $content['payload'] = $req->all();
        $content['response'] = $req->response;
        $content['meta'] = $req->meta;
        $content['full_url'] = $req->fullUrl();

        $request_log = new RequestLog();
        $request_log->user_id = $event->data['user']['id'] ?? null;
        $request_log->slug = 'request_limit_exceeded';
        $request_log->ip_address = $req->ip();
        $request_log->url = $event->data['url'];//$req->fullUrl();
        $request_log->method = $req->method();
        $request_log->content = json_encode($content);
        $request_log->response_code = 429;
        $request_log->save();
    }
}
