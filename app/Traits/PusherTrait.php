<?php

namespace App\Traits;

use Pusher\Pusher;

trait PusherTrait
{

    public function __construct()
    {

    }
    private function initPusher()
    {
       return new Pusher(env('PUSHER_APP_KEY'), env('PUSHER_APP_SECRET'), env('PUSHER_APP_ID'), [
            'cluster' => env('PUSHER_APP_CLUSTER'),
            'useTLS' => true,
        ]);
    }
    public function pusherSingleChannel($channel, $event, $data)
    {
        if(env('APP_ENV') == 'local') {
            return false;
        }
        $pusher = $this->initPusher();
        $pusher->trigger($channel, $event, $data);
    }

}
