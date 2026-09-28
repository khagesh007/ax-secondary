<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\LeadDataHubApiResource;
use GuzzleHttp\Client;
use App\Models\Lead;

class SyncLeadToDatahub implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $leadId;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($leadId)
    {
        $this->leadId = $leadId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $lead = Lead::find($this->leadId);

            if (is_null($lead->bitrix_id)) {
                // Load relationships
                $lead->load([
                    'agent', 'leadSource', 'property', 'nationality',
                    'leadMedium', 'leadType', 'leadStatus',
                    'criteria', 'criteria.locations.parentable', 'updatedBy'
                ]);

                // Convert resource to array
                $data = LeadDataHubApiResource::make($lead)->toArray(request());

                $url_datahub = "https://datahub.axcrm.ae/backend/api/webhook/secondary/lead/sync";

                $client = new Client();
                $response = $client->post($url_datahub, [
                    'form_params' => $data,
                ]);

                if ($response->getStatusCode() !== 200) {
                    Log::warning('Lead Sync returned non-200', [
                        'lead_id' => $lead->id,
                        'status'  => $response->getStatusCode(),
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Lead Sync Failed', [
                'lead_id' => $this->leadId ?? null,
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine()
            ]);
        }
    }
}
