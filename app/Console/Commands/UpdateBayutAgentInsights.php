<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\ExternalApiController;
use App\Models\Portal;
use App\Models\PortalMeta;
use App\Models\User;
use App\Models\UserMeta;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class UpdateBayutAgentInsights extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:bayutAgentInsights';

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
            CURLOPT_POSTFIELDS     => '{"size":0,"query":{"bool":{"filter":[{"term":{"is_active_agent":"1"}},{"terms":{"bayut_backend_agency_id":[9124]}},{"terms":{"most_active_emirate_curr_month":["Dubai","Abu Dhabi"]}}]}},"aggs":{"trubroker_badges_earned":{"filter":{"term":{"is_trubroker_badge_prev_month":"1"}}},"quality_lister_badges_earned":{"filter":{"term":{"is_quality_lister_badge_prev_month":"1"}}},"responsive_agent_badges_earned":{"filter":{"term":{"is_responsive_agent_badge_prev_month":"1"}}},"trubroker_badges_upcoming":{"filter":{"term":{"is_trubroker_badge_curr_month":"1"}}},"quality_lister_badges_upcoming":{"filter":{"term":{"is_quality_lister_badge_curr_month":"1"}}},"responsive_agent_badges_upcoming":{"filter":{"term":{"is_responsive_agent_badge_curr_month":"1"}}}}}',

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
        $data = $res['aggregations'] ?? [];
        if(!empty($data)){
        $meta = PortalMeta::storePortalMeta($portal->id, "bayut_tru_brokers_count", $data['trubroker_badges_earned']['doc_count'] ?? null);
        $meta = PortalMeta::storePortalMeta($portal->id, "bayut_responsive_agent_count", $data['responsive_agent_badges_earned']['doc_count'] ?? null);
        $meta = PortalMeta::storePortalMeta($portal->id, "bayut_quality_lister_count", $data['quality_lister_badges_earned']['doc_count'] ?? null);
        $meta = PortalMeta::storePortalMeta($portal->id, "bayut_responsive_agent_upcoming_count", $data['responsive_agent_badges_upcoming']['doc_count'] ?? null);
        $meta = PortalMeta::storePortalMeta($portal->id, "bayut_tru_brokers_upcoming_count", $data['trubroker_badges_upcoming']['doc_count'] ?? null);
        $meta = PortalMeta::storePortalMeta($portal->id, "bayut_responsive_agent_upcoming_count", $data['responsive_agent_badges_upcoming']['doc_count'] ?? null);
        }
        $totalActiveAgents = $this->getTotalActiveAgents($cookie);
        if($totalActiveAgents > 0){
            $meta = PortalMeta::storePortalMeta($portal->id, "bayut_total_active_agents", $totalActiveAgents);
        }

        return Command::SUCCESS;
    }
    private function getTotalActiveAgents($cookie){

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL            => 'https://www.bayut.com/api-v7/agencies/9124/agent-details',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'GET',
           // CURLOPT_POSTFIELDS     => '{"size":0,"query":{"bool":{"filter":[{"term":{"is_active_agent":"1"}},{"terms":{"bayut_backend_agency_id":[9124]}},{"terms":{"most_active_emirate_curr_month":["Dubai","Abu Dhabi"]}}]}},"aggs":{"trubroker_badges_earned":{"filter":{"term":{"is_trubroker_badge_prev_month":"1"}}},"quality_lister_badges_earned":{"filter":{"term":{"is_quality_lister_badge_prev_month":"1"}}},"responsive_agent_badges_earned":{"filter":{"term":{"is_responsive_agent_badge_prev_month":"1"}}},"trubroker_badges_upcoming":{"filter":{"term":{"is_trubroker_badge_curr_month":"1"}}},"quality_lister_badges_upcoming":{"filter":{"term":{"is_quality_lister_badge_curr_month":"1"}}},"responsive_agent_badges_upcoming":{"filter":{"term":{"is_responsive_agent_badge_curr_month":"1"}}}}}',

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
        if(is_array($res) && array_key_exists('message',$res)){
            $message = $res['message'];
            if($message == "Unauthenticated."){
                Log::channel('urlLogs')->error("Unauthenticated");
                $externalController = new ExternalApiController();
                $externalController->getAccessToken('token');
                info('updateBayutAgentInsight.php: Unauthenticated');
                Artisan::call('update:bayutAgentInsights');
            }
        }
       return $res['total'] ?? 0;
    }

}
