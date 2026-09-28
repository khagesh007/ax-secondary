<?php
namespace App\Console\Commands;

use App\Models\GeneralSetting;
use App\Models\Portal;
use App\Models\PortalMeta;
use App\Traits\PropertyFinderTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
class UpdatePFInsight extends Command
{
    use PropertyFinderTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:pfInsight';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will update portal data with Property finder listing, agent and lead insight.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $portal = Portal::where('name', 'Property Finder')->first();

        $pf_token = GeneralSetting::where('slug', 'pf-agent-data-api-token')->first();
       // $tokens = $this->getPfAccessToken('secondary',true);
        $this->getAgentInsight($portal,$pf_token);
      //  $this->getPfListingAndMore($portal,$pf_token);
        $this->getPfLeadInsight($portal,$pf_token);
        $auth = $this->getPfAccessTokenV2('secondary');
       // $this->fetchPfListingVerifiedData($portal,$tokens);
        $this->fetchPfListingStandardCount($portal,$auth['token']);
        $this->fetchPfListingPremiumCount($portal,$auth['token']);
        $this->fetchPfListingFeaturedCount($portal,$auth['token']);
        $this->fetchAllListingCount($portal,$auth['token']);
        return Command::SUCCESS;
    }
    protected function fetchAllListingCount($portal,$token)
    {
        $month = date('Y-m-d');
        $start_date = Carbon::createFromFormat("Y-m-d", $month)->startOfMonth()->format('Y-m-d');
        $end_date = Carbon::createFromFormat("Y-m-d", $month)->endOfMonth()->format('Y-m-d');

        try{
       // $tokens = $this->getPfAccessToken('secondary',true);
        if ($token != '') {

            $headers = [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ];

            $params = [
                "page" => 1,
                "perPage" => 1,
                //"filter[ids]" => ''
            ];

            $response = Http::withHeaders($headers)->get('https://atlas.propertyfinder.com/v1/listings', $params);


            if ($response->status() === 200) {
                $data = $response->json();

                if (is_array($data) && isset($data['pagination'])) {
                    $live_listing = $data['pagination']['total'] ?? 0;
                   $res = $live_listing > 0 ?  PortalMeta::storePortalMeta($portal->id, "pf_live_listings", $live_listing, $start_date, $end_date) : null;
                }
            }
            return [];

        } else {
            return [];
        }
    }catch(\Exception $e){
        info('UpdatePFInsight cronjob fetchPfListingVerifiedData error: ' . $e->getMessage());
    }

    }

    protected function fetchPfListingVerifiedData($portal,$token)
    {
        $month = date('Y-m-d');
        $start_date = Carbon::createFromFormat("Y-m-d", $month)->startOfMonth()->format('Y-m-d');
        $end_date = Carbon::createFromFormat("Y-m-d", $month)->endOfMonth()->format('Y-m-d');
try{
       // $tokens = $this->getPfAccessToken('secondary',true);
        if ($token != '') {

            $res = Http::withHeaders([
                'authorization'       => 'Bearer ' . $token,
                'content-type'        => 'application/json',
                'User-Agent: PostmanRuntime/7.28.0',
                'x-mycrm-expand-data' => true,
            ])->get('https://api-v2.mycrm.com/properties?filters[verified]=1&filters[portal]=propertyfinder&per_page=1');
            $data = $res->json();

            if (is_array($data) && isset($data['count'])) {

                $meta = PortalMeta::storePortalMeta($portal->id, "pf_verified_listings", $data['count'] ?? null, $start_date, $end_date);
            }
            return [];

        } else {
            return [];
        }
    }catch(\Exception $e){
        info('UpdatePFInsight cronjob fetchPfListingVerifiedData error: ' . $e->getMessage());
    }

    }

    protected function fetchPfListingStandardCount($portal,$token)
    {
        $month = date('Y-m-d');
        $start_date = Carbon::createFromFormat("Y-m-d", $month)->startOfMonth()->format('Y-m-d');
        $end_date = Carbon::createFromFormat("Y-m-d", $month)->endOfMonth()->format('Y-m-d');
try{




            if ($token != '') {

                $headers = [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                ];

                $params = [
                    "page" => 1,
                    "perPage" => 1,
                    "filter[listingLevel]" => "standard"
                ];

                $response = Http::withHeaders($headers)->get('https://atlas.propertyfinder.com/v1/listings', $params);


                if ($response->status() === 200) {
                    $data = $response->json();

                    if (is_array($data) && isset($data['pagination'])) {
                        $live_listing = $data['pagination']['total'] ?? 0;
                        $res = $live_listing > 0 ?  PortalMeta::storePortalMeta($portal->id, "pf_live_listings_standard", $live_listing, $start_date, $end_date) : null;
                    }
                }
                return [];

            } else {
                return [];
            }

    }catch(\Exception $e){
        info('UpdatePFInsight cronjob fetchPfListingVerifiedData error: ' . $e->getMessage());
    }

    }
    protected function fetchPfListingPremiumCount($portal,$token)
    {
        $month = date('Y-m-d');
        $start_date = Carbon::createFromFormat("Y-m-d", $month)->startOfMonth()->format('Y-m-d');
        $end_date = Carbon::createFromFormat("Y-m-d", $month)->endOfMonth()->format('Y-m-d');
try{
    //pf_live_listings_premium
    if ($token != '') {

        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ];

        $params = [
            "page" => 1,
            "perPage" => 1,
            "filter[listingLevel]" => "premium"
        ];

        $response = Http::withHeaders($headers)->get('https://atlas.propertyfinder.com/v1/listings', $params);


        if ($response->status() === 200) {
            $data = $response->json();

            if (is_array($data) && isset($data['pagination'])) {
                $live_listing = $data['pagination']['total'] ?? 0;
                $res = $live_listing > 0 ?  PortalMeta::storePortalMeta($portal->id, "pf_live_listings_premium", $live_listing, $start_date, $end_date) : null;
            }
        }
        return [];

    } else {
        return [];
    }
    }catch(\Exception $e){
        info('UpdatePFInsight cronjob fetchPfListingVerifiedData error: ' . $e->getMessage());
    }

    }
    protected function fetchPfListingFeaturedCount($portal,$token)
    {
        $month = date('Y-m-d');
        $start_date = Carbon::createFromFormat("Y-m-d", $month)->startOfMonth()->format('Y-m-d');
        $end_date = Carbon::createFromFormat("Y-m-d", $month)->endOfMonth()->format('Y-m-d');
try{
       //pf_live_listings_featured
    if ($token != '') {

        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ];

        $params = [
            "page" => 1,
            "perPage" => 1,
            "filter[listingLevel]" => "featured"
        ];

        $response = Http::withHeaders($headers)->get('https://atlas.propertyfinder.com/v1/listings', $params);


        if ($response->status() === 200) {
            $data = $response->json();

            if (is_array($data) && isset($data['pagination'])) {
                $live_listing = $data['pagination']['total'] ?? 0;
                $res = $live_listing > 0 ?  PortalMeta::storePortalMeta($portal->id, "pf_live_listings_featured", $live_listing, $start_date, $end_date) : null;
            }
        }
        return [];

    } else {
        return [];
    }
    }catch(\Exception $e){
        info('UpdatePFInsight cronjob fetchPfListingVerifiedData error: ' . $e->getMessage());
    }

    }

    protected function getAgentInsight($portal, $pf_token): void
    {
        try{
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => 'https://api-v2.mycrm.com/stats/agents/breakdown-statistics',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'GET',
            CURLOPT_HTTPHEADER     => [
                'accept: */*',
                'accept-language: en',
                'authorization: ' . $pf_token->value ?? '',
                'domain: ',
                'origin: https://expert.propertyfinder.ae',
                'priority: u=1, i',
                'referer: https://expert.propertyfinder.ae/',
                'sec-ch-ua: "Google Chrome";v="131", "Chromium";v="131", "Not_A Brand";v="24"',
                'sec-ch-ua-mobile: ?0',
                'sec-ch-ua-platform: "macOS"',
                'sec-fetch-dest: empty',
                'sec-fetch-mode: cors',
                'sec-fetch-site: cross-site',
                'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
            ],
        ]);

        $response = curl_exec($curl);

        curl_close($curl);
        if ($response === false) {
            info('UpdatePFInsight cronjob Curl error: ' . json_encode(curl_error($curl)));
        }
        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($http_status != 200) {
            info('UpdatePFInsight cronjob HTTP error: ' . $http_status . ' at ' . date('Y-m-d H:i:s'));
        }
        $data = json_decode($response, true);

        if (is_array($data) && count($data) > 0) {
            foreach ($data as $key => $value) {
                $meta = PortalMeta::storePortalMeta($portal->id, "pf_" . $key, $value ?? null);
            }

        }
    }catch(\Exception $e){
        info('UpdatePFInsight cronjob getAgentInsight error: ' . $e->getMessage());
    }
    }

    protected function getPfListingAndMore($portal, $pf_token): void
    {
        $start_date = date('Y-m-d', strtotime('-30 days'));
        $end_date   = date('Y-m-d', strtotime('-1 days'));
        $curl       = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => 'https://expert-api.propertyfinder.ae/stats/insights/v2/performance/overview?start_date=' . $start_date . '&end_date=' . $end_date . '&offering_type=sale',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'GET',
            CURLOPT_HTTPHEADER     => [
                'accept: */*',
                'accept-language: en',
                'authorization: ' . $pf_token->value ?? '',
                'domain: ',
                'origin: https://expert.propertyfinder.ae',
                'priority: u=1, i',
                'referer: https://expert.propertyfinder.ae/',
                'sec-ch-ua: "Google Chrome";v="131", "Chromium";v="131", "Not_A Brand";v="24"',
                'sec-ch-ua-mobile: ?0',
                'sec-ch-ua-platform: "macOS"',
                'sec-fetch-dest: empty',
                'sec-fetch-mode: cors',
                'sec-fetch-site: cross-site',
                'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
            ],
        ]);

        $response = curl_exec($curl);

        curl_close($curl);
        if ($response === false) {
            info('UpdatePFInsight cronjob Curl error: ' . json_encode(curl_error($curl)));
        }
        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($http_status != 200) {
            info('UpdatePFInsight cronjob HTTP error: ' . $http_status . ' at ' . date('Y-m-d H:i:s'));
        }
        $data = json_decode($response, true);
        if (isset($data['errors'])) {
            echo 'UpdatePFInsight cronjob API error: ' . json_encode($data['errors']);
        } else {
            if (is_array($data) && count($data) > 0) {
                $selected_keys = [
                   // 'live_listings',
                    'new_listings',
                    // 'live_listings_featured',
                    // 'live_listings_premium',
                    // 'live_listings_standard',
                    'published_listings',
                    'published_listings_featured',
                    'published_listings_premium',
                    'published_listings_standard',

                ];
                $start_date = date('Y-m-d', strtotime('first day of this month'));
                $end_date   = date('Y-m-d', strtotime('last day of this month'));
                foreach ($data as $key => $value) {
                    if (in_array($key, $selected_keys)) {
                        $meta = PortalMeta::storePortalMeta($portal->id, "pf_" . $key, $value ?? null, $start_date, $end_date);
                    }
                }
            }

        }

    }

    protected function getPfLeadInsight($portal, $pf_token)
    {
        //start of this month in Y-m-d format
        $start_date = date('Y-m-d', strtotime('first day of this month'));
        $end_date   = date('Y-m-d', strtotime('last day of this month'));

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL            => 'https://api-v2.mycrm.com/stats/leads-insights?dateRange=last-30-days',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'GET',
            CURLOPT_HTTPHEADER     => [
                'accept: */*',
                'accept-language: en',
                'authorization: ' . $pf_token->value ?? '',
                'domain: ',
                'origin: https://expert.propertyfinder.ae',
                'priority: u=1, i',
                'referer: https://expert.propertyfinder.ae/',
                'sec-ch-ua: "Google Chrome";v="131", "Chromium";v="131", "Not_A Brand";v="24"',
                'sec-ch-ua-mobile: ?0',
                'sec-ch-ua-platform: "macOS"',
                'sec-fetch-dest: empty',
                'sec-fetch-mode: cors',
                'sec-fetch-site: cross-site',
                'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
            ],
        ]);

        $response = curl_exec($curl);

        curl_close($curl);
        if ($response === false) {
            info('UpdatePFInsight cronjob Curl error: ' . json_encode(curl_error($curl)));
        }
        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($http_status != 200) {
            info('UpdatePFInsight cronjob HTTP error: ' . $http_status . ' at ' . date('Y-m-d H:i:s'));
        }
        $data = json_decode($response, true);
        if (isset($data['errors'])) {
            info('UpdatePFInsight->getPfLeadInsight cronjob API error: ' . json_encode($data['errors']));
        } else {
            if (is_array($data) && count($data) > 0) {
                $selected_keys = [
                    'total_calls_count',
                    'total_calls_rate',
                    'total_email_count',
                    'total_email_rate',
                    'total_leads',
                    'total_whatsapp_count',
                    'total_whatsapp_rate',

                ];
                foreach ($data as $key => $value) {
                    if (in_array($key, $selected_keys)) {
                        $meta = PortalMeta::storePortalMeta($portal->id, "pf_" . $key, $value ?? null, $start_date, $end_date);
                    }
                }
            }

        }

    }

}
