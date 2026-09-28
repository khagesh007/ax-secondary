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

class SendBitrixLeadEventJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $leadId;
    public $templateId;
    public $parameters;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($leadId, $templateId = 39, array $parameters = [])
    {
        $this->leadId = $leadId;
        $this->templateId = $templateId;
        $this->parameters = $parameters;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $url = 'https://crm.axcap.ae/rest/1/8cd3l0kbdlndvtqf/bizproc.workflow.start';

        $payload = [
            'DOCUMENT_ID' => ['crm', 'CCrmDocumentLead', $this->leadId],
            'TEMPLATE_ID' => $this->templateId,
            'PARAMETERS'  => $this->parameters,
        ];

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($url, $payload);

            if (!$response->successful()) {
                Log::channel('bitrix_alert')->error('❌ Bitrix workflow not API failed', [
                    'lead_id'  => $this->leadId,
                    'status'   => $response->status(),
                    'response' => $response->body(),
                ]);
            } else {
                Log::channel('bitrix_alert')->info("✅ Bitrix workflow for lead {$this->leadId} created successfully on Bitrix");
            }
        } catch (\Exception $e) {
            Log::channel('bitrix_alert')->error('❌ Exception during Bitrix workflow start', [
                'lead_id' => $this->leadId,
                'error'   => $e->getMessage(),
            ]);
        }
    }
}
