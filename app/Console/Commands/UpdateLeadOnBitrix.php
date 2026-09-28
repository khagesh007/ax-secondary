<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\Lead;
use Illuminate\Support\Facades\Queue;
use App\Traits\BitrixTrait;
use App\Models\LeadBitrixResponse;
use Illuminate\Support\Carbon;

class UpdateLeadOnBitrix extends Command
{
    use BitrixTrait;
    protected $errors = array();

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-lead-on-bitrix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            Log::info('UpdateLeadOnBitrix start');
            echo "UpdateLeadOnBitrix start \n";
            $leadsUpdateOnBitrix = LeadBitrixResponse::pluck('lead_id')->toArray();
            Log::info('$leadsUpdateOnBitrix');
            Log::info(count($leadsUpdateOnBitrix));
            $leads = Lead::whereNotNull('bitrix_id')->whereNotIn('id', $leadsUpdateOnBitrix)->whereYear('created_at', Carbon::now()->year)->get();
            
            Log::info('lead count');
            Log::info(count($leads));



            foreach ($leads as $lead) {
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
                        'UF_CRM_1691566273' => $this->fetchBitrixLeadGenderId($lead?->gender),
                        'UF_CRM_1552124268' => $this->fetchBitrixLeadTypeId($lead?->property?->propertyCategory?->title),
                        'UF_CRM_1553434168' => $lead?->dob,
                        'BIRTHDATE' => $lead?->dob,
                    ];
                    if ($lead->agent_id && $lead?->agent?->bitrix_id) {
                        $postData['fields']['ASSIGNED_BY_ID'] =  $lead?->agent?->bitrix_id;
                    }else{
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
                        } elseif ($lead->leadStatus->name == 'Viewing Arranged') {
                            $postData['fields']['STATUS_ID'] = 'PROCESSED';
                        } elseif ($lead->leadStatus->name == 'Unsuccessful') {
                            $postData['fields']['STATUS_ID'] = 1;
                            $postData['fields']['UF_CRM_1585912669'] = 9836;
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
                        echo "Lead $lead->first_name failed to updated on Bitrix with $lead?->bitrix_id \n";
                        $this->errors[] = [
                            'message' => 'Secondary Lead Update Request failed',
                            'status' => $response->status(),
                            'body' => $response->body(),
                        ];
                        $this->storeBitrixResponse($lead, $postData['fields'], $response->body(), false);
                    } else {
                        $res = $response->json();
                        echo "Lead $lead->first_name successfully updated on Bitrix with $lead?->bitrix_id \n";
                        Log::channel('bitrix_alert')->info("Lead $lead->first_name and id $lead->id successfully updated on Bitrix with $lead?->bitrix_id");
                        $this->storeBitrixResponse($lead, $postData['fields'], "Lead $lead->full_name updated successfully on Bitrix with $lead?->bitrix_id ", true);
                    }
                } else {
                    Log::channel('bitrix_alert')->info("Led $lead->full_name not found on Bitrix");
                }
            }

            if (count($this->errors) > 0) {
                Log::channel('bitrix_alert')->info("errors", $this->errors);
            }
            echo "UpdateLeadOnBitrix end \n";
            Log::info('UpdateLeadOnBitrix end');
        } catch (\Exception $e) {
            Log::channel('bitrix_alert')->info("error", [
                'message' => 'UpdateLeadOnBitrix failed',
                'status' => $e->getCode(),
                'body' => $e->getMessage(),
            ]);
        }
    }
}
