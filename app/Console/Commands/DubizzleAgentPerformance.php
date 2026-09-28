<?php
namespace App\Console\Commands;

use App\Http\Controllers\Api\ExternalApiController;
use App\Models\Portal;
use App\Models\User;
use App\Models\UserMeta;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class DubizzleAgentPerformance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:dubizzleAgentsPerformance';

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
        $portal      = Portal::where('name', 'Bayut')->first();
        $cookie      = $portal->token;
        $metas       = UserMeta::where('meta_key', 'dubizzle_bayut_backend_user_id')->get();
        $today       = date('Y-m-d');
        $oneMonthAgo = date('Y-m-d', strtotime('-1 month'));

        foreach ($metas as $i) {
            // dd($i->meta_value);
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL            => 'https://www.bayut.com/api-v7/users/2381209/stats/search?slug=agent_performance_profolio_dubizzleuae_prod_alias%2F_search&start=' . $oneMonthAgo . '&end=' . $today . '&proxyNumberStatus=active&portalId=2&page=1&perPage=25',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING       => '',
                CURLOPT_MAXREDIRS      => 10,
                CURLOPT_TIMEOUT        => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST  => 'POST',
                CURLOPT_POSTFIELDS     => '{"query":{"bool":{"filter":[{"range":{"date_nk":{"gte":"' . $oneMonthAgo . '","lte":"' . $today . '"}}},{"terms":{"bayut_backend_user_id":["' . $i->meta_value . '"]}}]}},"aggs":{"users":{"terms":{"field":"bayut_backend_user_id","size":65535,"order":{"_key":"asc"}},"aggs":{"count_whatsapp_msgs_delivered":{"sum":{"field":"num_whatsapp_msg_delivered"}},"count_whatsapp_msgs_answered":{"sum":{"field":"num_whatsapp_agent_contacted_lead"}},"count_calls_connected":{"sum":{"field":"num_reply_calltracking_connected"}},"count_calls_notconnected":{"sum":{"field":"num_reply_calltracking_not_connected"}},"count_calls_missed":{"sum":{"field":"num_reply_calltracking_missed"}},"count_calls_total":{"sum":{"field":"num_reply_calltracking"}},"total_leads":{"sum":{"field":"num_reply_total"}},"email_leads":{"sum":{"field":"num_reply_email"}},"sms_leads":{"sum":{"field":"num_reply_sms"}},"whatsapp_leads":{"sum":{"field":"num_reply_whatsapp_deliveries"}},"phone_leads":{"sum":{"field":"num_reply_calltracking_deliveries"}},"call_tracking_leads":{"sum":{"field":"num_reply_calltracking"}},"call_leads":{"bucket_script":{"buckets_path":{"call_tracking_leads":"call_tracking_leads","phone_leads":"phone_leads"},"script":"params.phone_leads"}},"sum_whatsapp_response_time":{"sum":{"field":"sum_whatsapp_response_time"}},"avg_response_time":{"bucket_script":{"buckets_path":{"msgs_delivered":"count_whatsapp_msgs_delivered","sum_response_time":"sum_whatsapp_response_time"},"script":"if (params.msgs_delivered == 0) {return 0} else {params.sum_response_time / params.msgs_delivered}"}},"call_answer_rate":{"bucket_script":{"buckets_path":{"calls_connected":"count_calls_connected","calls_notconnected":"count_calls_notconnected","calls_missed":"count_calls_missed"},"script":"if ((params.calls_connected + params.calls_missed) == 0) { return 0 } else { return params.calls_connected / (params.calls_connected + params.calls_missed) }"}},"response_rate":{"bucket_script":{"buckets_path":{"msgs_delivered":"count_whatsapp_msgs_delivered","msgs_answered":"count_whatsapp_msgs_answered"},"script":"if (params.msgs_delivered == 0) { return 0 } else { return params.msgs_answered / params.msgs_delivered }"}},"sorting":{"bucket_sort":{"sort":[],"size":65535}},"phone_views":{"sum":{"field":"num_reply_phone_clicks"}},"whatsapp_views":{"sum":{"field":"num_reply_whatsapp_clicks"}}}}}}',
                CURLOPT_HTTPHEADER     => [
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
            ]);

            $response = curl_exec($curl);
            $res      = json_decode($response, true);
            curl_close($curl);

            $message = $res['message'] ?? null;
            if ($message == "Unauthenticated.") {
                Log::channel('urlLogs')->error("Unauthenticated");
                $externalController = new ExternalApiController();
                $externalController->getAccessToken('token');
                info('dubizzleAgentsPerformance: Unauthenticated');
                Artisan::call('fetch:dubizzleAgentsPerformance');

            }
            $data = $res['hits']['hits'] ?? [];
            $data = end($data);
            $meta = [];
            if(is_array($data['_source']) && !isset($data['_source']['agent_email']) && $data['_source']['agent_email'] == null){
                continue;
            }
            $user = User::where('email', $data['_source']['agent_email'])->first();
            if (!$user) {
                info($data['_source']['agent_email'] . ' User Not Found in System. Error in dubizzleAgentsPerformance');
                continue;
            }
            foreach ($data['_source'] as $key => $value) {
                if (in_array($key, [
                    'agent_performance_profolio_sk',
                    'agency_sk',
                    'property_agentperson_sk',
                    'agency_id',
                    'agent_id',
                    'bayut_backend_user_id',
                    'bayut_backend_agency_id',
                    'agency_name',
                    'agent_name',
                    'agent_email',
                    'agent_proxy_number',
                ])) {
                    continue;
                }

                $metaKey = 'dubizzle_' . $key;
                try {
                    // info($data['_source']['agent_email'] . ' ' . $metaKey . ' ' . $value);
                    // sleep(1);
                    UserMeta::storeUserMeta($user->id, $metaKey, $value !== '' ? $value : null);
                } catch (\Throwable $th) {
                    info($data['_source']['email'] . ' User Not Found in System. Error in DubizzleAgentStats.php');
                    continue;
                }
            }
        }


        return Command::SUCCESS;
    }
}
