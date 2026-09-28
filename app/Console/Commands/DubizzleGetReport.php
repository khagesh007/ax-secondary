<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\ExternalApiController;
use App\Models\Portal;
use App\Models\User;
use App\Models\UserThirdPartyData;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DubizzleGetReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dubizzle:get-report {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Dubizzle Agent Performance';

    protected $authTried = 0;

    private function getAgentPerformance($startDate, $endDate, $dubizzleUserId, $cookie)
    {
        $curl = curl_init();

        $params = [
            'slug' => 'agent_performance_profolio_dubizzleuae_prod_alias/_search',
            'start' => $startDate,
            'end' => $endDate,
            'proxyNumberStatus' => 'active',
            'portalId' => 2,
        ];

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://www.bayut.com/api-v7/users/2381209/stats/search?" . http_build_query($params),
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
                            "gte": "' . $startDate . '",
                            "lte": "' . $endDate . '"
                        }
                    }
                },
                {
                    "terms": {
                        "bayut_backend_user_id": [' . $dubizzleUserId . ']
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
            CURLOPT_HTTPHEADER => [
                'accept: */*',
                'accept-language: en-US,en;q=0.9,ur;q=0.8,ar;q=0.7',
                'app-name: phoenix',
                'content-type: application/json',
                'cookie: ' . $cookie,
                'origin: https://www.bayut.com',
                'priority: u=1, i',
                'referer: https://www.bayut.com/profolio/reports/agent-stats?tab=trubroker-report&page=1&portal=dubizzle',
                'sec-ch-ua: "Not A(Brand";v="8", "Chromium";v="132", "Google Chrome";v="132"',
                'sec-ch-ua-mobile: ?0',
                'sec-ch-ua-platform: "macOS"',
                'sec-fetch-dest: empty',
                'sec-fetch-mode: cors',
                'sec-fetch-site: same-origin',
                'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36',
                'x-xsrf-token: eyJpdiI6IkM0Ulo3UXVIOGxHM1BodUNhZk42VHc9PSIsInZhbHVlIjoieFlFWEx6ZTlyYzRcL04wY0FJUHlVY1g1Q1hIT2RERldKSXlLa081RjEySE9JTXd0aU1MV2VyRlpJMGlySUJubElYV1VBMjg0ODJCU1Z3TjZyOXVldWV3PT0iLCJtYWMiOiJhYmQyY2M5YWE5ZTZlYTliNGY2NTNkNjBlMGQ3ODgxNzU2MmMyODg4ZDQ2YjdhNTY2ZDNlYzMyMzU0ODQyNDFiIn0=',
            ],
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $res = json_decode($response, true);

        $message = $res['message'] ?? null;

        if ($message == "Unauthenticated.") {
            info('DubizzleGetReport.php at getAgentPerformance function: Unauthenticated');
            $externalController = new ExternalApiController();
            $externalController->getAccessToken('token');
            if ($this->authTried < 5){
                $this->authTried++;
                return $this->getAgentPerformance($startDate, $endDate, $dubizzleUserId, $cookie);
            }
        }

        $hits = $res['hits']['hits'][0]['_source'] ?? [];
        $aggregations = $res['aggregations']['users']['buckets'][0] ?? [];

        return [
            'bayut_backend_user_id' => $hits['bayut_backend_user_id'] ?? '',
            'agent_name' => $hits['agent_name'] ?? '',
            'agent_email' => $hits['agent_email'] ?? '',
            'num_reply_email' => $hits['num_reply_email'] ?? 0,
            'num_reply_phone' => $hits['num_reply_phone'] ?? 0,
            'num_reply_phone_clicks' => $hits['num_reply_phone_clicks'] ?? 0,
            'num_reply_whatsapp' => $hits['num_reply_whatsapp'] ?? 0,
            'num_reply_whatsapp_clicks' => $hits['num_reply_whatsapp_clicks'] ?? 0,
            'num_reply_whatsapp_deliveries' => $hits['num_reply_whatsapp_deliveries'] ?? 0,
            'num_reply_calltracking' => $hits['num_reply_calltracking'] ?? 0,
            'num_reply_calltracking_deliveries' => $hits['num_reply_calltracking_deliveries'] ?? 0,
            'num_reply_sms' => $hits['num_reply_sms'] ?? 0,
            'calls_total_duration_sec' => $hits['calls_total_duration_sec'] ?? 0,
            'num_reply_calltracking_connected' => $hits['num_reply_calltracking_connected'] ?? 0,
            'num_reply_calltracking_not_connected' => $hits['num_reply_calltracking_not_connected'] ?? 0,
            'num_reply_calltracking_missed' => $hits['num_reply_calltracking_missed'] ?? 0,
            'num_reply_total' => $hits['num_reply_total'] ?? 0,
            'num_live_listings' => $hits['num_live_listings'] ?? 0,
            'num_live_listings_verified' => $hits['num_live_listings_verified'] ?? 0,
            'num_live_listings_featured' => $hits['num_live_listings_featured'] ?? 0,
            'num_live_listings_premium' => $hits['num_live_listings_premium'] ?? 0,
            'num_live_listings_standard' => $hits['num_live_listings_standard'] ?? 0,
            'num_whatsapp_msg_delivered' => $hits['num_whatsapp_msg_delivered'] ?? 0,

            //Aggregations
            'whatsapp_leads' => $aggregations['whatsapp_leads']['value'] ?? 0,
            'phone_leads' => $aggregations['phone_leads']['value'] ?? 0,
            'count_calls_connected' => $aggregations['count_calls_connected']['value'] ?? 0,
            'phone_views' => $aggregations['phone_views']['value'] ?? 0,
            'total_leads' => $aggregations['total_leads']['value'] ?? 0,
            'email_leads' => $aggregations['email_leads']['value'] ?? 0,
            'count_calls_notconnected' => $aggregations['count_calls_notconnected']['value'] ?? 0,
            'count_whatsapp_msgs_answered' => $aggregations['count_whatsapp_msgs_answered']['value'] ?? 0,
            'call_tracking_leads' => $aggregations['call_tracking_leads']['value'] ?? 0,
            'whatsapp_views' => $aggregations['whatsapp_views']['value'] ?? 0,
            'count_calls_total' => $aggregations['count_calls_total']['value'] ?? 0,
            'sms_leads' => $aggregations['sms_leads']['value'] ?? 0,
            'count_calls_missed' => $aggregations['count_calls_missed']['value'] ?? 0,
            'count_whatsapp_msgs_delivered' => $aggregations['count_whatsapp_msgs_delivered']['value'] ?? 0,
            'sum_whatsapp_response_time' => $aggregations['sum_whatsapp_response_time']['value'] ?? 0,
            'call_leads' => $aggregations['call_leads']['value'] ?? 0,
            'avg_response_time' => $aggregations['avg_response_time']['value'] ?? 0,
            'call_answer_rate' => round(((float)($aggregations['call_answer_rate']['value'] ?? 0)) * 100, 2),
            'response_rate' => round(((float)($aggregations['response_rate']['value'] ?? 0)) * 100, 2),
        ];
    }

    private function getAgentDetails($dubizzleUserId, $cookie)
    {
        $curl = curl_init();

        $params = [
            'tab' => 'trubroker-report',
            'page' => 1,
            'portal' => 'dubizzle',
            'agentId' => $dubizzleUserId,
            'perPage' => 50,
        ];

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://www.bayut.com/api-v7/agencies/9124/agent-details?' . http_build_query($params),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                'accept: */*',
                'accept-language: en',
                'app-name: phoenix',
                'cookie: ' . $cookie,
                'priority: u=1, i',
                'referer: https://www.bayut.com/profolio/reports/agent-stats?tab=trubroker-report&page=1&portal=dubizzle&perPage=50&agentId=' . $dubizzleUserId,
                'sec-ch-ua: "Google Chrome";v="131", "Chromium";v="131", "Not_A Brand";v="24"',
                'sec-ch-ua-mobile: ?0',
                'sec-ch-ua-platform: "macOS"',
                'sec-fetch-dest: empty',
                'sec-fetch-mode: cors',
                'sec-fetch-site: same-origin',
                'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
            ],
        ]);

        $response = curl_exec($curl);
        $res = json_decode($response, true);

        curl_close($curl);
        $message = $res['message'] ?? null;

        if ($message == "Unauthenticated.") {
            info('DubizzleGetReport.php at getAgentDetails function: Unauthenticated');
            $externalController = new ExternalApiController();
            $externalController->getAccessToken('token');
            if ($this->authTried < 5){
                $this->authTried++;
                return $this->getAgentDetails($dubizzleUserId, $cookie);
            }
        }

        $data = $res['data'] ?? [];

        $agentDetails = $data[0] ?? [];

        return [
            'externalId' => $agentDetails['externalId'] ?? '',
            'slug' => $agentDetails['slug'] ?? '',
            'email' => $agentDetails['email'] ?? '',
            'state' => $agentDetails['state'] ?? '',
            'editState' => $agentDetails['editState'] ?? '',
            'quota_featured' => $agentDetails['quota_featured'] ?? 0,
            'verifiedListingsCount' => $agentDetails['verifiedListingsCount'] ?? 0,
            'checkedListingsCount' => $agentDetails['checkedListingsCount'] ?? 0,
            'truCheckedListingsCount' => $agentDetails['truCheckedListingsCount'] ?? 0,
            'experience' => $agentDetails['experience'] ?? 0,
            'basic' => $agentDetails['usedQuota']['basic'] ?? 0,
            'hot' => $agentDetails['usedQuota']['hot'] ?? 0,
            'signature' => $agentDetails['usedQuota']['signature'] ?? 0,
            'devBasic' => $agentDetails['usedQuota']['devBasic'] ?? 0,
            'devHot' => $agentDetails['usedQuota']['devHot'] ?? 0,
            'devSignature' => $agentDetails['usedQuota']['devSignature'] ?? 0,
            'total' => $agentDetails['usedQuota']['total'] ?? 0,
            'createdAt' => $agentDetails['createdAt'] ?? '',
            'isVerified' => $agentDetails['isVerified'] ?? false,
            'isProconnectEnabled' => $agentDetails['isProconnectEnabled'] ?? false,
            'isAgencyAdmin' => $agentDetails['isAgencyAdmin'] ?? false,
            'isActive' => $agentDetails['isActive'] ?? false,
        ];
    }

    private function getAgentStats($dubizzleUserId, $cookie)
    {
        $curl = curl_init();

        $params = [
            'slug' => 'trubroker_badges_xp_all_agents_profolio_bayutuae_prod_alias/_search',
            'skipAuthorization' => true,
        ];

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://www.bayut.com/api-v7/users/2381209/stats/search?' . http_build_query($params),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
    "size": 10,
    "from": 0,
    "track_total_hits": true,
    "query": {
        "bool": {
            "filter": [

                {
                    "term": {
                        "is_active_agent": "1"
                    }
                },
                {
                    "terms": {
                        "bayut_backend_user_id": [' . $dubizzleUserId . ']
                    }
                },
                {
                    "terms": {
                        "most_active_emirate_curr_month": [
                            "Dubai",
                            "Abu Dhabi"
                        ]
                    }
                }
            ]
        }
    }
}',
            CURLOPT_HTTPHEADER => array(
                'accept: */*',
                'accept-language: en-US,en;q=0.9,ur;q=0.8,ar;q=0.7',
                'app-name: phoenix',
                'content-type: application/json',
                'cookie: ' . $cookie,
                'origin: https://www.bayut.com',
                'priority: u=1, i',
                'referer: https://www.bayut.com/profolio/reports/agent-stats?tab=trubroker-report&page=1&portal=dubizzle',
                'sec-ch-ua: "Not A(Brand";v="8", "Chromium";v="132", "Google Chrome";v="132"',
                'sec-ch-ua-mobile: ?0',
                'sec-ch-ua-platform: "macOS"',
                'sec-fetch-dest: empty',
                'sec-fetch-mode: cors',
                'sec-fetch-site: same-origin',
                'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36',
                'x-xsrf-token: eyJpdiI6IkM0Ulo3UXVIOGxHM1BodUNhZk42VHc9PSIsInZhbHVlIjoieFlFWEx6ZTlyYzRcL04wY0FJUHlVY1g1Q1hIT2RERldKSXlLa081RjEySE9JTXd0aU1MV2VyRlpJMGlySUJubElYV1VBMjg0ODJCU1Z3TjZyOXVldWV3PT0iLCJtYWMiOiJhYmQyY2M5YWE5ZTZlYTliNGY2NTNkNjBlMGQ3ODgxNzU2MmMyODg4ZDQ2YjdhNTY2ZDNlYzMyMzU0ODQyNDFiIn0='
            ),
        ));


        $response = curl_exec($curl);
        $res = json_decode($response, true);

        curl_close($curl);
        $message = $res['message'] ?? null;
        if ($message == "Unauthenticated.") {
            $externalController = new ExternalApiController();
            $externalController->getAccessToken('token');

        }

        if ($message == "Unauthenticated.") {
            info('DubizzleGetReport.php at getAgentStats function: Unauthenticated');
            $externalController = new ExternalApiController();
            $externalController->getAccessToken('token');
            if ($this->authTried < 5){
                $this->authTried++;
                return $this->getAgentStats($dubizzleUserId, $cookie);
            }
        }

        return $res['hits']['hits'][0]['_source'] ?? [];
    }

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

        $dubizzleUserIds = User::whereNotNull('third_party_ids->dubizzle_user_id')
            ->pluck('third_party_ids')
            ->map(function ($thirdPartyId) {
                $thirdPartyIdArray = json_decode($thirdPartyId, true);
                return $thirdPartyIdArray['dubizzle_user_id'];
            })->toArray();

        foreach ($dubizzleUserIds as $dubizzleUserId) {


            $user = User::where('third_party_ids->dubizzle_user_id', $dubizzleUserId)->first();
            if ($user) {
                $agentPerformance = $this->getAgentPerformance($startDate, $endDate, $dubizzleUserId, $cookie);
                $agentDetails = $this->getAgentDetails($dubizzleUserId, $cookie);
                $agentStats = $this->getAgentStats($dubizzleUserId, $cookie);


                $thirdPartyData = UserThirdPartyData::where('user_id', $user->id)
                    ->where('portal', 'dubizzle')
                    ->where('start_date', $startDate)
                    ->where('end_date', $endDate)->first();
                $userFullData = json_decode($thirdPartyData?->useful_data ?? null, true) ?? [];
                if (!$thirdPartyData) {
                    $thirdPartyData = new UserThirdPartyData();
                }

                $thirdPartyData->user_id = $user->id;
                $thirdPartyData->portal = 'dubizzle';
                $thirdPartyData->start_date = $startDate;
                $thirdPartyData->end_date = $endDate;
                $thirdPartyData->useful_data = json_encode(array_merge($userFullData, $agentPerformance, $agentDetails, $agentStats));
                $thirdPartyData->portal_data_updated_at = Carbon::now();
                $thirdPartyData->save();
            }
        }

        return Command::SUCCESS;
    }
}
