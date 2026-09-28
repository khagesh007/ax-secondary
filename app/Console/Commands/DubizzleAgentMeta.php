<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Portal;
use App\Models\User;
use App\Models\UserMeta;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Api\ExternalApiController;
use Illuminate\Support\Facades\Artisan;

class DubizzleAgentMeta extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:dubizzleAgentsMeta';

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
            CURLOPT_URL            => 'https://www.bayut.com/api-v7/agencies/9124/agent-details?tab=agent-performance&page=1&portal=dubizzle&perPage=50000',
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
                'referer: https://www.bayut.com/profolio/reports/agent-stats?tab=agent-performance&page=1&portal=dubizzle&perPage=25',
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
            info('DubizzleAgentStats.php: Unauthenticated');
            Artisan::call('fetch:dubizzleAgentsMeta');
        }


        $data  = $res['data'] ?? [];
        $count = 0;
        foreach ($data as $item) {

            $meta = [
                'dubizzle_id'                      => isset($item['id']) ? $item['id'] : null,
                'dubizzle_externalId'              => isset($item['externalId']) ? $item['externalId'] : null,
                'dubizzle_slug'                    => isset($item['slug']) ? $item['slug'] : null,
                'dubizzle_email'                   => isset($item['email']) ? $item['email'] : null,
                'dubizzle_state'                   => isset($item['state']) ? $item['state'] : null,
                'dubizzle_editState'               => isset($item['editState']) ? $item['editState'] : null,
                'dubizzle_quota_featured'          => isset($item['quota_featured']) ? $item['quota_featured'] : null,
                'dubizzle_verifiedListingsCount'   => isset($item['verifiedListingsCount']) ? $item['verifiedListingsCount'] : null,
                'dubizzle_checkedListingsCount'    => isset($item['checkedListingsCount']) ? $item['checkedListingsCount'] : null,
                'dubizzle_truCheckedListingsCount' => isset($item['truCheckedListingsCount']) ? $item['truCheckedListingsCount'] : null,
                'dubizzle_experience'              => isset($item['experience']) ? $item['experience'] : null,
                'dubizzle_basic'                   => isset($item['usedQuota']['basic']) ? $item['usedQuota']['basic'] : null,
                'dubizzle_hot'                     => isset($item['usedQuota']['hot']) ? $item['usedQuota']['hot'] : null,
                'dubizzle_signature'               => isset($item['usedQuota']['signature']) ? $item['usedQuota']['signature'] : null,
                'dubizzle_devBasic'                => isset($item['usedQuota']['devBasic']) ? $item['usedQuota']['devBasic'] : null,
                'dubizzle_devHot'                  => isset($item['usedQuota']['devHot']) ? $item['usedQuota']['devHot'] : null,
                'dubizzle_devSignature'            => isset($item['usedQuota']['devSignature']) ? $item['usedQuota']['devSignature'] : null,
                'dubizzle_total'                   => isset($item['usedQuota']['total']) ? $item['usedQuota']['total'] : null,
                'dubizzle_createdAt'               => isset($item['createdAt']) ? $item['createdAt'] : null,
                'dubizzle_isVerified'              => isset($item['isVerified']) ? $item['isVerified'] : null,
                'dubizzle_isProconnectEnabled'     => isset($item['isProconnectEnabled']) ? $item['isProconnectEnabled'] : null,
                'dubizzle_isAgencyAdmin'           => isset($item['isAgencyAdmin']) ? $item['isAgencyAdmin'] : null,
                'dubizzle_isActive'                => isset($item['isActive']) ? $item['isActive'] : null,
            ];
            $count++;
            // dd($meta);
            // Fetch the user by email
            $user = User::where('email', $meta['dubizzle_email'])->first();

            // Check if user exists
            if ($user) {
                foreach ($meta as $key => $value) {
                    UserMeta::storeUserMeta($user->id, $key, $value !== '' ? $value : null);
                }
            } else {
                // Optionally handle the case where the user is not found
                // Log or throw an error
                Log::warning("User not found for email: " . $meta['dubizzle_email']);
            }

        }
        return Command::SUCCESS;
    }
}
