<?php

namespace App\Listeners;

use App\Events\LeadStatusChanged;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LeadStatusListener
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
     * @param  \App\Events\LeadStatusChanged  $event
     * @return void
     */
    public function handle(LeadStatusChanged $event)
    {
        //
    }
}
