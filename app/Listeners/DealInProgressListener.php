<?php

namespace App\Listeners;

use App\Events\DealInProgress;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DealInProgressListener
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
     * @param  \App\Events\DealInProgress  $event
     * @return void
     */
    public function handle(DealInProgress $event)
    {
        //
    }
}
