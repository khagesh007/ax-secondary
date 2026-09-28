<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\ExternalApiController;
use App\Imports\BayutAgentInsightImport;
use App\Models\Portal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Excel;

class BayutGetReportFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bayut:get-report-file {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Bayut Agent Performance';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $date = $this->argument('date');
        if (empty($date)) {
            $date = Carbon::now()->format('Y-m-d');
        }

        $startDate = Carbon::parse($date)->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::parse($date)->endOfMonth()->format('Y-m-d');

        $externalController = new ExternalApiController();
        $externalController->getAccessToken('token');
        $portal = Portal::where('name', 'Bayut')->first();
        $cookie = $portal->token;


        $bayutUserIds = User::whereNotNull('third_party_ids->bayut_user_id')
            ->pluck('third_party_ids')
            ->map(function ($thirdPartyId) {
                $thirdPartyIdArray = json_decode($thirdPartyId, true);
                return $thirdPartyIdArray['bayut_user_id'];
            })->toArray();

        $curl = curl_init();


        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://www.bayut.com/api-v7/export-users?start=$startDate&end=$endDate&slug=agent_performance_profolio_bayutuae_prod_alias/_search&portalId=1&tab=agent-performance",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
    "query": {
        "bool": {
            "filter": [
                {
                    "range": {
                        "date_nk": {
                            "gte": "'.$startDate.'",
                            "lte": "'.$endDate.'"
                        }
                    }
                },
                {
                    "terms": {
                        "bayut_backend_user_id": [' . implode(',', $bayutUserIds) . ']
                    }
                }
            ]
        }
    },
    "aggs": {
        "users": {
            "terms": {
                "field": "bayut_backend_user_id",
                "size": 65535,
                "order": {
                    "_key": "asc"
                }
            },
            "aggs": {
                "count_whatsapp_msgs_delivered": {
                    "sum": {
                        "field": "num_whatsapp_msg_delivered"
                    }
                },
                "count_whatsapp_msgs_answered": {
                    "sum": {
                        "field": "num_whatsapp_agent_contacted_lead"
                    }
                },
                "count_calls_connected": {
                    "sum": {
                        "field": "num_reply_calltracking_connected"
                    }
                },
                "count_calls_notconnected": {
                    "sum": {
                        "field": "num_reply_calltracking_not_connected"
                    }
                },
                "count_calls_missed": {
                    "sum": {
                        "field": "num_reply_calltracking_missed"
                    }
                },
                "count_calls_total": {
                    "sum": {
                        "field": "num_reply_calltracking"
                    }
                },
                "total_leads": {
                    "sum": {
                        "field": "num_reply_total"
                    }
                },
                "email_leads": {
                    "sum": {
                        "field": "num_reply_email"
                    }
                },
                "sms_leads": {
                    "sum": {
                        "field": "num_reply_sms"
                    }
                },
                "whatsapp_leads": {
                    "sum": {
                        "field": "num_reply_whatsapp_deliveries"
                    }
                },
                "phone_leads": {
                    "sum": {
                        "field": "num_reply_calltracking_deliveries"
                    }
                },
                "call_tracking_leads": {
                    "sum": {
                        "field": "num_reply_calltracking"
                    }
                },
                "call_leads": {
                    "bucket_script": {
                        "buckets_path": {
                            "call_tracking_leads": "call_tracking_leads",
                            "phone_leads": "phone_leads"
                        },
                        "script": "params.phone_leads"
                    }
                },
                "sum_whatsapp_response_time": {
                    "sum": {
                        "field": "sum_whatsapp_response_time"
                    }
                },
                "avg_response_time": {
                    "bucket_script": {
                        "buckets_path": {
                            "msgs_delivered": "count_whatsapp_msgs_delivered",
                            "sum_response_time": "sum_whatsapp_response_time"
                        },
                        "script": "if (params.msgs_delivered == 0) {return 0} else {params.sum_response_time / params.msgs_delivered}"
                    }
                },
                "call_answer_rate": {
                    "bucket_script": {
                        "buckets_path": {
                            "calls_connected": "count_calls_connected",
                            "calls_notconnected": "count_calls_notconnected",
                            "calls_missed": "count_calls_missed"
                        },
                        "script": "if ((params.calls_connected + params.calls_missed) == 0) { return 0 } else { return params.calls_connected / (params.calls_connected + params.calls_missed) }"
                    }
                },
                "response_rate": {
                    "bucket_script": {
                        "buckets_path": {
                            "msgs_delivered": "count_whatsapp_msgs_delivered",
                            "msgs_answered": "count_whatsapp_msgs_answered"
                        },
                        "script": "if (params.msgs_delivered == 0) { return 0 } else { return params.msgs_answered / params.msgs_delivered }"
                    }
                },
                "sorting": {
                    "bucket_sort": {
                        "sort": [],
                        "size": 65535
                    }
                },
                "phone_views": {
                    "sum": {
                        "field": "num_reply_phone_clicks"
                    }
                },
                "whatsapp_views": {
                    "sum": {
                        "field": "num_reply_whatsapp_clicks"
                    }
                }
            }
        }
    }
}',
            CURLOPT_HTTPHEADER => array(
                'accept: application/json',
                'accept-language: en',
                'access-control-allow-credentials: true',
                'content-type: application/json; charset=UTF-8',
                'cookie:' . $cookie,
                'dnt: 1',
                'origin: https://www.bayut.com',
                'priority: u=1, i',
                'referer: https://www.bayut.com/profolio/reports/agent-stats?tab=agent-performance&page=1&portal=bayut&perPage=25',
                'sec-ch-ua: "Not A(Brand";v="8", "Chromium";v="132", "Microsoft Edge";v="132"',
                'sec-ch-ua-mobile: ?0',
                'sec-ch-ua-platform: "macOS"',
                'sec-fetch-dest: empty',
                'sec-fetch-mode: cors',
                'sec-fetch-site: same-origin',
                'sec-gpc: 1',
                'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 Edg/132.0.0.0'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        //save response as excel file
        $file = 'bayut_report.xlsx';
        $destinationPath = public_path('uploads');
        $path = $destinationPath . '/' . $file;
        file_put_contents($path, $response);
        $this->info('File saved successfully');
        $excel_file = public_path('uploads/bayut_report.xlsx');
        if (file_exists($excel_file)) {
            try {
                Excel::import(new BayutAgentInsightImport($startDate, $endDate), $excel_file);
                $this->info('Data imported successfully');
            } catch (\Exception $e) {
                $this->error('Something went wrong: ' . $e->getMessage());
            }
        } else {
            $this->error('File not found at ' . $excel_file);
        }

        return Command::SUCCESS;
    }
}
