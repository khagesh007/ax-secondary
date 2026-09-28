<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\ExternalApiController;
use App\Models\GeneralSetting;
use App\Models\Portal;
use Illuminate\Console\Command;
use App\Models\User;
class SyncUserThirdPartyId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:user-third-party-id';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $bayutAuthTried = 0;
    protected $dubizzleAuthTried = 0;

    private function getBayoutData()
    {
        $portal = Portal::where('name', 'Bayut')->first();
        $cookie = $portal->token;
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://www.bayut.com/api-v7/agencies/9124/agent-details?tab=agent-performance&page=1&portal=bayut&perPage=50000',
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
        $res = json_decode($response, true);

        curl_close($curl);
        $message = $res['message'] ?? null;


        if ($message == "Unauthenticated.") {
            echo "Bayut unauthenticated.\n";
            info('SyncUserThirdPartyId.php at getBayutData function: Unauthenticated');
            $externalController = new ExternalApiController();
            $externalController->getAccessToken('token');
            if ($this->bayutAuthTried < 5){
                $this->bayutAuthTried++;
                return $this->getBayoutData();
            }
        }

        return $res['data'] ?? [];
    }

    private function getDubizzleData()
    {
        $portal = Portal::where('name', 'Bayut')->first();
        $cookie = $portal->token;
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://www.bayut.com/api-v7/agencies/9124/agent-details?tab=agent-performance&page=1&portal=dubizzle&perPage=50000',
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
        $res = json_decode($response, true);

        curl_close($curl);
        $message = $res['message'] ?? null;

        if ($message == "Unauthenticated.") {
            echo "Bayut unauthenticated.\n";
            info('SyncUserThirdPartyId.php at getDubizzleData function: Unauthenticated');
            $externalController = new ExternalApiController();
            $externalController->getAccessToken('token');
            if ($this->dubizzleAuthTried < 5){
                $this->dubizzleAuthTried++;
                return $this->getDubizzleData();
            }
        }

        return $res['data'] ?? [];
    }

    private function updateBayutIds()
    {
        $data = $this->getBayoutData();
        foreach ($data ?? [] as $item) {
            try {
                if (!empty($item['id']) && !empty($item['email'])) {
                    $user = User::where('email', $item['email'])->first();
                    if (!is_null($user)) {
                        $thirdPartyIds = json_decode($user->third_party_ids, true);
                        $thirdPartyIds['bayut_user_id'] = $item['id'];
                        $user->third_party_ids = json_encode($thirdPartyIds);
                        $user->save();
                    }
                }
            }catch (\Exception $exception){
                info('SyncUserThirdPartyId.php at updateBayutIds function: '. $exception->getMessage());
            }

        }
    }

    private function updateDubizzleIds()
    {
        $data = $this->getDubizzleData();

        foreach ($data ?? [] as $item) {
            try {
                if (!empty($item['id']) && !empty($item['email'])) {
                    $user = User::where('email', $item['email'])->first();
                    if (!is_null($user)) {
                        $thirdPartyIds = json_decode($user->third_party_ids, true);
                        $thirdPartyIds['dubizzle_user_id'] = $item['id'];
                        $user->third_party_ids = json_encode($thirdPartyIds);
                        $user->save();
                    }
                }
            }catch (\Exception $exception){
                info('SyncUserThirdPartyId.php at updateDubizzleIds function: '. $exception->getMessage());
            }
        }
    }

    private function updatePFIds()
    {
        try {
            $curl = curl_init();
            $pf_token = GeneralSetting::where('slug', 'pf-agent-data-api-token')->first();
            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://api-v2.mycrm.com/stats/agents',
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

            $data = json_decode($response, true);

            if (isset($data['errors'])) {
                foreach ($data['errors'] as $error) {
                    echo $error['title'] . "\n";
                }
            }

            foreach ($data['agents'] ?? [] as $item) {

                $fullName = $item['fullname'];

                $users = User::where('name', $fullName)->orWhere('pp_name', $fullName)->get();

                foreach ($users as $user) {
                    try {
                        if ($user) {
                            if (!is_null($user)) {
                                $thirdPartyIds = json_decode($user->third_party_ids, true);
                                $thirdPartyIds['pf_user_id'] = $item['agent_id'];
                                $user->third_party_ids = json_encode($thirdPartyIds);
                                $user->save();
                            }
                        }
                    }catch (\Exception $exception){
                        info('SyncUserThirdPartyId.php at updateDubizzleIds function: '. $exception->getMessage().  ', for user: '. $user->email);
                    }
                }
            }
        }catch (\Exception $exception){
            info('SyncUserThirdPartyId.php at updatePFIds function: '. $exception->getMessage());
        }
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->updateBayutIds();
        $this->updateDubizzleIds();
        $this->updatePFIds();
    }
}
