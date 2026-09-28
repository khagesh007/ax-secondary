<?php

namespace App\Listeners;

use App\Events\LeadNoteAdded;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Traits\TelegramTrait;
class LeadNoteListener
{
    use TelegramTrait;
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
     * @param  \App\Events\LeadNoteAdded  $event
     * @return void
     */
    public function handle(LeadNoteAdded $event)
    {
        //Telegram push
        $note = $event->leadNote;
        $this->sendLeadNote($note);
    }
}
