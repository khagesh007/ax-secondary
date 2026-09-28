<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\ExternalApiController;
use App\Models\Portal;
use App\Models\PortalMeta;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class UpdateBayutListingInsights extends Command
{
    protected $signature = 'update:bayutListingInsights';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will fetch bayut listing summary for reporting';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $portal = Portal::where('name', 'Bayut')->first();
        $dubizzle = Portal::where('name', 'Dubizzle')->first();
        $cookie = $portal->token;

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL            => 'https://www.bayut.com/api-v7/groups/9124/ad-aggregates',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'GET',
            // CURLOPT_POSTFIELDS     => '{"size":200,"from":0,"track_total_hits":true,"query":{"bool":{"filter":[{"term":{"is_active_agent":"1"}},{"terms":{"bayut_backend_user_id":[]}},{"terms":{"most_active_emirate_curr_month":["Dubai","Abu Dhabi"]}}]}}}',
            // CURLOPT_POSTFIELDS     => '{"size":200,"from":0,"track_total_hits":true,"query":{"bool":{"filter":[{"term":{"is_active_agent":"1"}},{"terms":{"most_active_emirate_curr_month":["Dubai","Abu Dhabi"]}}]}}}',
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

        curl_close($curl);
        $message = $res['message'] ?? null;

        if ($message == "Unauthenticated.") {

            Log::channel('urlLogs')->error("Unauthenticated");
            $externalController = new ExternalApiController();
            $externalController->getAccessToken('token');
            info('UpdateBayutListingInsights.php: Unauthenticated');
            Artisan::call('update:bayutListingInsights');
        }else{
        $data = $res['data'] ?? [];
        $meta = PortalMeta::storePortalMeta($portal->id, "bayut_active_listings", $data['by_portal']['bayut']['by_state']['active'] ?? null);
        $meta = PortalMeta::storePortalMeta($portal->id, "bayut_active_sale_listings", $data['by_portal']['bayut']['by_purpose']['sale']['active'] ?? null);
        $meta = PortalMeta::storePortalMeta($portal->id, "bayut_active_rental_listings", $data['by_portal']['bayut']['by_purpose']['rent']['active'] ?? null);
        $meta = PortalMeta::storePortalMeta($portal->id, "bayut_basic_listings", $data['by_portal']['bayut']['ad_products']['listing'] ?? null);
        $meta = PortalMeta::storePortalMeta($portal->id, "bayut_hot_listings", $data['by_portal']['bayut']['ad_products']['hot'] ?? null);
        $meta = PortalMeta::storePortalMeta($portal->id, "bayut_signature_listings", $data['by_portal']['bayut']['ad_products']['signature'] ?? null);

        $total_tru_checked_listings = $this->getBayutTruCheckedListingCount($cookie);
        if($total_tru_checked_listings > 0){
            $meta = PortalMeta::storePortalMeta($portal->id, "bayut_tru_checked_listings", $total_tru_checked_listings);
        }
        $total_checked_listings = $this->getBayutCheckedListingCount($cookie);
        if($total_checked_listings > 0){
            $meta = PortalMeta::storePortalMeta($portal->id, "bayut_checked_listings", $total_checked_listings);
        }

        //Dubizzle
        $meta = PortalMeta::storePortalMeta($dubizzle->id, "dubizzle_featured_listings", $data['by_portal']['dubizzle']['ad_products']['featured'] ?? null);
        $meta = PortalMeta::storePortalMeta($dubizzle->id, "dubizzle_basic_listings", $data['by_portal']['dubizzle']['ad_products']['listing'] ?? null);
        $meta = PortalMeta::storePortalMeta($dubizzle->id, "dubizzle_premium_listings", $data['by_portal']['dubizzle']['ad_products']['premium'] ?? null);
        $meta = PortalMeta::storePortalMeta($dubizzle->id, "dubizzle_active_listings", $data['by_portal']['dubizzle']['by_state']['active'] ?? null);
        $meta = PortalMeta::storePortalMeta($dubizzle->id, "dubizzle_rent_listings", $data['by_portal']['dubizzle']['by_purpose']['rent']['active'] ?? null);
        $meta = PortalMeta::storePortalMeta($dubizzle->id, "dubizzle_sale_listings", $data['by_portal']['dubizzle']['by_purpose']['sale']['active'] ?? null);

        $total_verified_listings = $this->getDubizzleVerifiedListing($cookie);
        $meta = PortalMeta::storePortalMeta($dubizzle->id, "dubizzle_verified_listings", $total_verified_listings);
    }
        return Command::SUCCESS;
    }
    private function getDubizzleVerifiedListing($cookie){

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL            => 'https://www.bayut.com/api-v7/agents/2381209/listings?page=1&perPage=1&portalTab=active&verificationStatus=verified&portalIds=2&portalState=active',
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
                info('updateBayutInsight.php: Unauthenticated');
                Artisan::call('update:bayutAgentInsights');
            }
        }
       return $res['total'] ?? 0;
    }
    private function getBayutTruCheckedListingCount($cookie){

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL            => 'https://www.bayut.com/api-v7/agents/2381209/listings?page=1&perPage=1&portalTab=active&imageStatus=all&verificationStatus=truchecked&load=adProductPricing&portalIds=1&portalState=active',
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
                info('updateBayutInsight.php: Unauthenticated');
                Artisan::call('update:bayutAgentInsights');
            }
        }
       return $res['total'] ?? 0;
    }
    private function getBayutCheckedListingCount($cookie){

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL            => 'https://www.bayut.com/api-v7/agents/2381209/listings?page=1&perPage=10&portalTab=active&imageStatus=all&verificationStatus=checked&load=adProductPricing&portalIds=1&portalState=active',
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
                info('updateBayutInsight.php: Unauthenticated');
                Artisan::call('update:bayutAgentInsights');
            }
        }
       return $res['total'] ?? 0;
    }
}
