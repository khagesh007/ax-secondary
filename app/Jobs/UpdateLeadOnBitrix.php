<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\Lead;
use Illuminate\Support\Facades\Queue;
use App\Traits\BitrixTrait;

class UpdateLeadOnBitrix implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use BitrixTrait;

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
            Log::channel('bitrix_alert')->info("UpdateLeadOnBitrix job start");
            $lead = Lead::find($this->leadId);
            if (!$lead) {
                Log::channel('bitrix_alert')->error("Lead with ID {$this->leadId} not found.");
                return;
            }
            Log::channel('bitrix_alert')->info("Secondary Lead $lead->full_name with id $lead->id with bitrixId $lead->bitrix_id job start");

            if ($lead && $lead->bitrix_id) {

                $languageID = $this->fetchBitrixLeadLanguageId('English');

                $postData = array();
                $postData['fields'] = [
                    'ID' => $lead->bitrix_id,
                    'TITLE' => $lead->full_name,
                    'NAME' => $lead?->first_name,
                    'LAST_NAME' => $lead?->last_name,
                    'PHONE' => [
                        [
                            'VALUE' => $lead?->mobile,
                            'VALUE_TYPE' => 'WORK',
                        ],
                    ],
                    'EMAIL' => [
                        [
                            'VALUE' => $lead?->email,
                            'VALUE_TYPE' => 'WORK',
                        ],
                    ],
                    'UF_CRM_1621687101' => $lead?->address ? $lead?->address : " ", // require for warm
                    'UF_CRM_1691566273' => $this->fetchBitrixLeadGenderId($lead?->gender ?? 'N/A'),
                    'UF_CRM_1552124268' => $this->fetchBitrixLeadTypeId($lead?->property?->propertyCategory?->title ?? 'Apartment'),
                    'UF_CRM_1553434168' => $lead?->dob,
                    'BIRTHDATE' => $lead?->dob,
                ];
                if ($lead->agent_id && $lead?->agent?->bitrix_id) {
                    $postData['fields']['ASSIGNED_BY_ID'] =  $lead?->agent?->bitrix_id;
                } else {
                    Log::channel('bitrix_alert')->error("Agent $lead->agent_id not found on Bitrix");
                }
                if ($lead->leadStatus) {

                    if ($lead->leadStatus->name == 'New') {
                        $postData['fields']['STATUS_ID'] = "NEW";
                    } elseif ($lead->leadStatus->name == 'Not Answered') {
                        $postData['fields']['STATUS_ID'] = 4;
                    } elseif ($lead->leadStatus->name == 'In Progress') {
                        $postData['fields']['STATUS_ID'] = 7;
                    } elseif ($lead->leadStatus->name == 'Contacted') {
                        $postData['fields']['STATUS_ID'] = 'IN_PROCESS';
                        $postData['fields']['UF_CRM_1553506485'][] =  $languageID;
                    } elseif ($lead->leadStatus->name == 'Qualified') {
                        $postData['fields']['STATUS_ID'] = 6;
                        $postData['fields']['UF_CRM_1553506485'][] =  $languageID;
                        $projectID = $this->getLeadProjectId($lead->bitrix_id);
                        if (!$projectID) {
                            Log::channel('bitrix_alert')->error("Project ID not found for lead $lead->bitrix_id");
                        }
                        $postData['fields']['UF_CRM_1730914494'][] = $projectID;

                    } elseif($lead->leadStatus->name == 'Under Offer'){
                        $filePath = public_path('assets/images/no_document.pdf');
                        $document = [
                            'no_document',
                            base64_encode(file_get_contents($filePath))
                        ];
                        $postData['fields']['STATUS_ID'] = 2;
                        $postData['fields']['UF_CRM_1553506485'][] =  $languageID;
                        $postData['fields']['UF_CRM_1552124458'][] = [
                            "fileData" => $document
                        ];
                    } elseif ($lead->leadStatus->name == 'Viewing Arranged') {
                        $postData['fields']['STATUS_ID'] = 'PROCESSED';
                    } elseif ($lead->leadStatus->name == 'Unsuccessful') {
                        $postData['fields']['STATUS_ID'] = 1;
                        $postData['fields']['UF_CRM_1585912669'] = 9836;
                        // $leadNote = $lead->reason ?? "Unsuccessful Reason";
                        // $authId = $lead?->agent?->bitrix_id ?? 500;
                        // SendBitrixLeadNoteJob::dispatch($lead->bitrix_id, $leadNote->note, $authId);
                    }
                }
                if ($lead?->nationality?->nationality) {
                    $nationalityID = $this->getBitrixLeadNationalityId($lead?->nationality?->nationality);
                    $postData['fields']['UF_CRM_1553166948'] =  $nationalityID; //nationality
                }

                $response = Http::post('https://crm.axcap.ae/rest/1/8cd3l0kbdlndvtqf/crm.lead.update', [
                    'id' => $lead->bitrix_id,
                    'fields' => $postData['fields'],
                ]);

                if ($response->failed()) {
                    Log::channel('bitrix_alert')->error('Secondary Lead Update Request failed', ['status' => $response->status(), 'body' => $response->body()]);
                } else {
                    $res = $response->json();
                    Log::channel('bitrix_alert')->info("Secondary Lead $lead->name updated successfully on Bitrix with $lead?->bitrix_id");
                }
            } else {
                Log::channel('bitrix_alert')->info("secondary Lead $this->leadId not found on Bitrix");
            }
        } catch (\Exception $exception) {
            Log::channel('bitrix_alert')->error('Error updating secondary lead on Bitrix', [
                'lead_id' => $this->leadId,
                'exception_message' => $exception->getMessage(),
                'stack_trace' => $exception->getTraceAsString(),
            ]);
        }
    }
    public function getLeadProjectId($leadID)
    {
        try {
            $url = "https://crm.axcap.ae/rest/1/8cd3l0kbdlndvtqf/crm.lead.get?ID=$leadID";
            $response = Http::get($url);
            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['result']) && isset($data['result']['UF_CRM_1568907438'])) {
                   return $data['result']['UF_CRM_1568907438']; 
                   
                } else {
                    Log::channel('bitrix_alert')->info("Secondary Lead $leadID Unable to fetch lead UF_CRM_1568907438");
                }
            } else {
                Log::channel('bitrix_alert')->info("Secondary Lead $leadID Unable to fetch lead data");
            }
        } catch (\Exception $exception) {

            Log::channel('bitrix_alert')->error('Error secondary lead on Bitrix', [
                'lead_id' => $leadID,
                'exception_message' => $exception->getMessage(),
                'stack_trace' => $exception->getTraceAsString(),
            ]);
        }
    }
}
