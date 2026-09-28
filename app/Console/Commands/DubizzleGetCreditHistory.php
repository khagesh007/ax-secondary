<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\ExternalApiController;
use App\Models\BayutCreditHistory;
use App\Models\Portal;
use App\Models\User;
use App\Models\UserThirdPartyData;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DubizzleGetCreditHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dubizzle:get-credit-history {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get dubizzle credit history for all users';

    protected $authTried = 0;

    private function processData($data, $users, $date)
    {
        foreach ($data as $item) {
            if (!empty($item['ad_product']['name']) && $item['ad_product']['name'] == 'listing') {
                continue;
            }

            $userIndex = array_search($item['user_id'], array_column($users, 'portal_id'));

            $user = $users[$userIndex];

            if ($user) {
                try {
                    BayutCreditHistory::updateOrCreate([
                        'user_id' => $user['id'],
                        'portal' => 'dubizzle',
                        'transaction_time' => Carbon::parse($item['created_at'])->timezone(config('app.timezone'))->format('Y-m-d H:i:s'),
                    ], [
                        'date' => $date,
                        'type' => ($item['amount'] ?? 0) >= 0 ? 'credit' : 'debit',
                        'amount' => abs($item['amount'] ?? 0),
                    ]);
                } catch (\Exception $e) {
                    info('DubizzleGetCreditHistory.php at processData function for user: '. $user['id'] .' with error: '. $e->getMessage());
                }
            }
        }
    }


    private function getCreditHistory($date, $users, $cookie, $page = 1)
    {
        $curl = curl_init();

        $params = [
            'page' => $page,
        ];

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://www.bayut.com/orion/groups/9124/currency-consumptions/search?" . http_build_query($params),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
                "includes": [
                    {
                        "relation": "adProduct"
                    }
                ],
                "sort": [
                    {
                        "field": "created_at",
                        "direction": "desc"
                    }
                ],
                "filters": [
                    {
                        "field": "portal_id",
                        "operator": "in",
                        "value": [
                            2
                        ]
                    },
                    {
                        "field": "created_at",
                        "operator": ">=",
                        "value": "' . $date . '"
                    },
                    {
                        "field": "created_at",
                        "operator": "<=",
                        "value": "' . $date . '"
                    }
                ]
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
            info('DubizzleGetCreditHistory.php at getCreditHistory function: Unauthenticated');
            $externalController = new ExternalApiController();
            $externalController->getAccessToken('token');
            if ($this->authTried < 5) {
                $this->authTried++;
                $this->getCreditHistory($date, $users, $cookie, $page);
            }
        }

        $this->processData($res['data'] ?? [], $users, $date);

        if (($res['meta']['last_page'] ?? 1) > $page) {
            $this->getCreditHistory($date, $users, $cookie, ++$page);
        }
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

        $externalController = new ExternalApiController();
        $externalController->getAccessToken('token');
        $portal = Portal::where('name', 'Bayut')->first();
        $cookie = $portal->token;

        $dateRange = getBayutCreditDurationDates($date);

        $startDate = $dateRange['start_date'];
        $endDate = $dateRange['end_date'];

        $users = User::whereNotNull('third_party_ids->dubizzle_user_id')->get(['id', 'third_party_ids->dubizzle_user_id AS portal_id'])->toArray();

        $this->getCreditHistory($date, $users, $cookie);

        $creditHistories = BayutCreditHistory::selectRaw("
                    user_id,
                    SUM(CASE WHEN type = 'credit' THEN amount ELSE 0 END) AS total_credit,
                    SUM(CASE WHEN type = 'debit' THEN amount ELSE 0 END) AS total_debit
                ")
            ->where('portal', 'dubizzle')
            ->whereDate('transaction_time', '>=', $startDate)
            ->whereDate('transaction_time', '<=', $endDate)
            ->groupBy('user_id')
            ->get();

        foreach ($creditHistories as $creditHistory) {
            UserThirdPartyData::updateOrCreate([
                'user_id' => $creditHistory->user_id,
                'portal' => 'dubizzle_credit',
                'start_date' => $startDate,
                'end_date' => $endDate,
            ], [
                'useful_data' => json_encode([
                    'credit' => $creditHistory->total_credit,
                    'debit' => $creditHistory->total_debit,
                    'consumed' => $creditHistory->total_credit - $creditHistory->total_debit,
                ]),
            ]);
        }

        return Command::SUCCESS;
    }
}
