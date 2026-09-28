<?php
namespace App\Console\Commands;

use App\Http\Controllers\Api\ExternalApiController;
use App\Models\Portal;
use App\Models\User;
use App\Models\UserMeta;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

class BayutAgentStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:bayutAgentsStats';

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

        $portal = Portal::where('name', 'Bayut')->first();
        $cookie = $portal->token;

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL            => 'https://www.bayut.com/api-v7/users/2381209/stats/search?slug=trubroker_badges_xp_all_agents_profolio_bayutuae_prod_alias%2F_search&skipAuthorization=true',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            // CURLOPT_POSTFIELDS     => '{"size":200,"from":0,"track_total_hits":true,"query":{"bool":{"filter":[{"term":{"is_active_agent":"1"}},{"terms":{"bayut_backend_user_id":[]}},{"terms":{"most_active_emirate_curr_month":["Dubai","Abu Dhabi"]}}]}}}',
            // CURLOPT_POSTFIELDS     => '{"size":200,"from":0,"track_total_hits":true,"query":{"bool":{"filter":[{"term":{"is_active_agent":"1"}},{"terms":{"most_active_emirate_curr_month":["Dubai","Abu Dhabi"]}}]}}}',
            CURLOPT_POSTFIELDS     => '{"size":200,"from":0,"track_total_hits":true,"query":{"bool":{"filter":[{"term":{"is_active_agent":"1"}},{"terms":{"bayut_backend_agency_id":[9124]}},{"term":{"is_trubroker_badge_prev_month":"1"}},{"terms":{"most_active_emirate_curr_month":["Dubai","Abu Dhabi"]}}]}},"aggs":{"all_agent_external_ids":{"terms":{"field":"agent_external_id","size":50000}}}}',

            CURLOPT_HTTPHEADER     => [
                'accept: */*',
                'accept-language: en-US,en;q=0.9,ur;q=0.8,ar;q=0.7',
                'app-name: phoenix',
                'content-type: application/json',
                'cookie: ' . $cookie,
                'origin: https://www.bayut.com',
                'priority: u=1, i',
                'referer: https://www.bayut.com/profolio/reports/agent-stats?tab=trubroker-report&page=1&portal=bayut&badges=is_trubroker_badge_prev_month',
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
        $res      = json_decode($response, true);

        curl_close($curl);
        $message = $res['message'] ?? null;

        if ($message == "Unauthenticated.") {

            Log::channel('urlLogs')->error("Unauthenticated");
            $externalController = new ExternalApiController();
            $externalController->getAccessToken('token');
            info('BayutAgentStats.php: Unauthenticated');
            Artisan::call('fetch:bayutAgentsStats');

        }
        $data = $res['hits']['hits'] ?? [];
        $meta = [];
        foreach ($data as $item) {
            $user = User::where('email', $item['_source']['email'])->first();
            foreach ($item['_source'] as $key => $value) {

                // Skip the specified keys
                if (in_array($key, ['user_langs_en', 'user_langs_ar', 'user_lang_codes_en'])) {
                    continue;
                }

                $metaKey = 'bayut_' . $key;
                try {
                    UserMeta::storeUserMeta($user->id, $metaKey, $value !== '' ? $value : null);
                } catch (\Throwable $th) {
                    info($item['_source']['email'] . ' User Not Found in System. Error in BayutTryBroker.php' );
                    continue;
                }
            }
        }

        return Command::SUCCESS;

    }
}
