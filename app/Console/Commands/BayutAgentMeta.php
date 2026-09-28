<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Portal;
use App\Models\User;
use App\Models\UserMeta;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Api\ExternalApiController;
use Illuminate\Support\Facades\Artisan;

class BayutAgentMeta extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:bayutAgentsMeta';

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
        $curl   = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL            => 'https://www.bayut.com/api-v7/agencies/9124/agent-details?tab=agent-performance&page=1&portal=bayut&perPage=50000',
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
                'app-name: phoenix',
                'cookie: ' . $cookie,
                'priority: u=1, i',
                'referer: https://www.bayut.com/profolio/reports/agent-stats?tab=agent-performance&page=1&portal=bayut&perPage=25',
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
            info('BayutAgentMeta.php: Unauthenticated');
            Artisan::call('fetch:bayutAgentsMeta');

        }
        $data  = $res['data'] ?? [];
        $count = 0;
        foreach ($data as $item) {

            $meta = [
                'bayut_id'                          => isset($item['id']) ? $item['id'] : null,
                'bayut_externalId'                  => isset($item['externalId']) ? $item['externalId'] : null,
                'bayut_slug'                        => isset($item['slug']) ? $item['slug'] : null,
                'bayut_email'                       => isset($item['email']) ? $item['email'] : null,
                'bayut_state'                       => isset($item['state']) ? $item['state'] : null,
                'bayut_editState'                   => isset($item['editState']) ? $item['editState'] : null,
                'bayut_quota_featured'              => isset($item['quota_featured']) ? $item['quota_featured'] : null,
                'bayut_verifiedListingsCount'       => isset($item['verifiedListingsCount']) ? $item['verifiedListingsCount'] : null,
                'bayut_checkedListingsCount'        => isset($item['checkedListingsCount']) ? $item['checkedListingsCount'] : null,
                'bayut_truCheckedListingsCount'     => isset($item['truCheckedListingsCount']) ? $item['truCheckedListingsCount'] : null,
                'bayut_experience'                  => isset($item['experience']) ? $item['experience'] : null,
                'bayut_basic'                       => isset($item['usedQuota']['basic']) ? $item['usedQuota']['basic'] : null,
                'bayut_hot'                         => isset($item['usedQuota']['hot']) ? $item['usedQuota']['hot'] : null,
                'bayut_signature'                   => isset($item['usedQuota']['signature']) ? $item['usedQuota']['signature'] : null,
                'bayut_devBasic'                    => isset($item['usedQuota']['devBasic']) ? $item['usedQuota']['devBasic'] : null,
                'bayut_devHot'                      => isset($item['usedQuota']['devHot']) ? $item['usedQuota']['devHot'] : null,
                'bayut_devSignature'                => isset($item['usedQuota']['devSignature']) ? $item['usedQuota']['devSignature'] : null,
                'bayut_total'                       => isset($item['usedQuota']['total']) ? $item['usedQuota']['total'] : null,
                'bayut_createdAt'                   => isset($item['createdAt']) ? $item['createdAt'] : null,
                'bayut_isVerified'                  => isset($item['isVerified']) ? $item['isVerified'] : null,
                'bayut_isProconnectEnabled'         => isset($item['isProconnectEnabled']) ? $item['isProconnectEnabled'] : null,
                'bayut_isAgencyAdmin'               => isset($item['isAgencyAdmin']) ? $item['isAgencyAdmin'] : null,
                'bayut_isActive'                    => isset($item['isActive']) ? $item['isActive'] : null,
                'bayut_listingsCount_bayut_sale'    => isset($item['listingsCount']['bayut']['by_purpose']['sale']) ? $item['listingsCount']['bayut']['by_purpose']['sale'] : null,
                'bayut_listingsCount_bayut_rent'    => isset($item['listingsCount']['bayut']['by_purpose']['rent']) ? $item['listingsCount']['bayut']['by_purpose']['rent'] : null,
                'bayut_listingsCount_dubizzle_sale' => isset($item['listingsCount']['dubizzle']['by_purpose']['sale']) ? $item['listingsCount']['dubizzle']['by_purpose']['sale'] : null,
                'bayut_listingsCount_dubizzle_rent' => isset($item['listingsCount']['dubizzle']['by_purpose']['rent']) ? $item['listingsCount']['dubizzle']['by_purpose']['rent'] : null,
            ];

            $count++;
            // Fetch the user by email
            $user = User::where('email', $meta['bayut_email'])->first();

            // Check if user exists
            if ($user) {
                foreach ($meta as $key => $value) {
                    UserMeta::storeUserMeta($user->id, $key, $value !== '' ? $value : null);
                }
            } else {
                // Optionally handle the case where the user is not found
                // Log or throw an error
                Log::warning("User not found for email: " . $meta['bayut_email']);
            }

        }
        return Command::SUCCESS;
    }
}
