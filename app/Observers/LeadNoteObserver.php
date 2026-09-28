<?php

namespace App\Observers;

use App\Models\LeadNote;
use App\Jobs\SendBitrixLeadNoteJob;
use Illuminate\Support\Facades\Log;
class LeadNoteObserver
{
    /**
     * Check if note should skip Bitrix job dispatch.
     */
    private function shouldSkipBitrix(LeadNote $note): bool
    {
        $skipKeywords = [
            'imported from bitrix24',
            'Bitrix24 webhook',
        ];

        $content = strtolower($note->note);

        foreach ($skipKeywords as $keyword) {
            if (str_contains($content, $keyword)) {
                return true;
            }
        }

        return false;
    }
    /**
     * Handle the LeadNote "created" event.
     *
     * @param  \App\Models\LeadNote  $leadNote
     * @return void
     */

    public function created(LeadNote $leadNote)
    {
        if ($this->shouldSkipBitrix($leadNote)) {
            return;
        }
        $authId = $leadNote?->createdBy?->bitrix_id ?? 500;
        if($leadNote->lead && $leadNote->lead?->bitrix_id){
            SendBitrixLeadNoteJob::dispatch($leadNote->lead->bitrix_id, $leadNote->note, $authId);
        }
    }

    /**
     * Handle the LeadNote "updated" event.
     *
     * @param  \App\Models\LeadNote  $leadNote
     * @return void
     */
    public function updated(LeadNote $leadNote)
    {
        //
    }

    /**
     * Handle the LeadNote "deleted" event.
     *
     * @param  \App\Models\LeadNote  $leadNote
     * @return void
     */
    public function deleted(LeadNote $leadNote)
    {
        //
    }

    /**
     * Handle the LeadNote "restored" event.
     *
     * @param  \App\Models\LeadNote  $leadNote
     * @return void
     */
    public function restored(LeadNote $leadNote)
    {
        //
    }

    /**
     * Handle the LeadNote "force deleted" event.
     *
     * @param  \App\Models\LeadNote  $leadNote
     * @return void
     */
    public function forceDeleted(LeadNote $leadNote)
    {
        //
    }
}
