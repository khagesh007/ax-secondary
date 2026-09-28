<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendBitrixLeadNoteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $leadId;
    public $comment;
    public $authorId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($leadId, $comment, $authorId = 500)
    {
        $this->leadId = $leadId;
        $this->comment = $comment;
        $this->authorId = $authorId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::channel('bitrix_alert')->info("Lead Note Bitrix job start");

        $url = 'https://crm.axcap.ae/rest/1/8cd3l0kbdlndvtqf/crm.timeline.comment.add';

        $fields = [
            'fields' => [
                'ENTITY_ID'   => $this->leadId,
                'ENTITY_TYPE' => 'lead',
                'COMMENT'     => $this->comment,
                'AUTHOR_ID'   => $this->authorId,
            ],
        ];

        try {
            $response = Http::asForm()->post($url, $fields);
            if (!$response->successful()) {
                Log::channel('bitrix_alert')->error('❌ Bitrix lead not API failed', [
                    'lead_id'  => $this->leadId,
                    'status'   => $response->status(),
                    'response' => $response->body(),
                ]);

            } else {
                Log::channel('bitrix_alert')->info("✅ Lead Note {$this->leadId} created successfully on Bitrix");
            }
            Log::channel('bitrix_alert')->info("Lead Note Bitrix job end");
        } catch (\Exception $exception) {
            Log::channel('bitrix_alert')->error('❌ Bitrix lead note creation failed', [
                'lead_id'  => $this->leadId,
                'error' => $exception->getMessage(),
                'line' => $exception->getLine(),
                'file' => $exception->getFile()
            ]);
        }
    }
}
