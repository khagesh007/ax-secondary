<?php

namespace App\Observers;

use App\Models\Event;
use Carbon\Carbon;
use App\Jobs\SendBitrixLeadEventJob;
use Illuminate\Support\Facades\Log;

class EventObserver
{
    /**
     * Handle the Event "created" event.
     *
     * @param  \App\Models\Event  $event
     * @return void
     */
    public function created(Event $event)
    {

        if($event->start_time && $event->end_time && $event->lead_id && $event?->lead->bitrix_id) {
            $templateId = 39; // Default template ID

            // Resolve meeting type from type or type->name
            $meetingType = $event->type ?? ($event->type->name ?? null);

            // Allowed types
            $allowedTypes = [
                'KETURAH',
                'Meeting Client',
                'Viewing Rent',
                'Viewing Sales',
                'Video Conference',
            ];

            // Fallback to "Meeting Client" if not in list
            if (!in_array($meetingType, $allowedTypes)) {
                $meetingType = 'Meeting Client';
            }
            
            $parameters = [
                'notes'        => $event->title ?? 'Default notes',
                'start_time'   => Carbon::parse($event->start_time)->toIso8601String(),
                'end_time'     => Carbon::parse($event->end_time)->toIso8601String(),
                'meeting_type' => $meetingType,
            ];
            Log::info("parameters", $parameters);
            SendBitrixLeadEventJob::dispatch($event?->lead->bitrix_id, $templateId, $parameters);
        }
    }

    /**
     * Handle the Event "updated" event.
     *
     * @param  \App\Models\Event  $event
     * @return void
     */
    public function updated(Event $event)
    {
        //
    }

    /**
     * Handle the Event "deleted" event.
     *
     * @param  \App\Models\Event  $event
     * @return void
     */
    public function deleted(Event $event)
    {
        //
    }

    /**
     * Handle the Event "restored" event.
     *
     * @param  \App\Models\Event  $event
     * @return void
     */
    public function restored(Event $event)
    {
        //
    }

    /**
     * Handle the Event "force deleted" event.
     *
     * @param  \App\Models\Event  $event
     * @return void
     */
    public function forceDeleted(Event $event)
    {
        //
    }
}
