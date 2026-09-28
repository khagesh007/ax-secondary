<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\GeneralSetting;
use App\Models\UserThirdPartyData;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Traits\PropertyFinderTrait;

class PFAgentInsights extends Command
{
    use PropertyFinderTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pf:get-agent-insights';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update PF agent insights';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

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


        $startDate = Carbon::now()->subDays(60)->format('Y-m-d');
        $endDate = Carbon::now()->format('Y-m-d');

        foreach ($data['agents'] ?? [] as $item) {
            $users = User::where('third_party_ids->pf_user_id', $item['agent_id'])->get();

            foreach ($users as $user) {
                try {
                    $metaData = [
                        "pf_agent_id" => $item['agent_id'],
                        "fullname" => $item['fullname'],
                        "photo_url" => $item['photo_url'],
                        "is_super_agent" => $item['is_super_agent'],
                        "is_verified" => $item['is_verified'],
                        "total_leads" => $item['total_leads'],
                        "avg_quality_score" => round(((float)$item['avg_quality_score'] * 100), 2),
                        "verified_listing_rate" => round(((float)$item['verified_listing_rate'] * 100), 2),
                        "avg_listing_duration" => $item['avg_listing_duration'],
                        "response_rate_emails" => round(((float)$item['response_rate_emails'] * 100), 2),
                        "response_rate_calls" => round(((float)$item['response_rate_calls'] * 100), 2),
                        "response_rate_whatsapp" => round(((float)$item['response_rate_whatsapp'] * 100), 2),
                        "response_time_whatsapp" => $item['response_time_whatsapp'],
                        "overall_responsiveness" => round(((float)$item['overall_responsiveness'] * 100), 2),
                        "number_of_properties" => $item['number_of_properties'],
                        "total_whatsapp_leads" => $item['total_whatsapp_leads'],
                        "total_call_leads" => $item['total_call_leads'],
                        "total_email_leads" => $item['total_email_leads'],
                        "deleted_at" => $item['deleted_at'],
                    ];

                    $thirdPartyData = UserThirdPartyData::where('user_id', $user->id)
                        ->where('portal', 'property_finder')
                        ->where('start_date', $startDate)
                        ->where('end_date', $endDate)->first();
                    $userFullData = json_decode($thirdPartyData?->useful_data ?? null, true) ?? [];
                    if (!$thirdPartyData) {
                        $thirdPartyData = new UserThirdPartyData();
                    }

                    $thirdPartyData->user_id = $user->id;
                    $thirdPartyData->portal = 'property_finder';
                    $thirdPartyData->start_date = $startDate;
                    $thirdPartyData->end_date = $endDate;
                    $thirdPartyData->useful_data = json_encode(array_merge($userFullData, $metaData));
                    $thirdPartyData->portal_data_updated_at = Carbon::now();
                    $thirdPartyData->save();
                }catch (\Exception $exception){
                    echo $exception->getMessage();
                }
            }
        }

        return Command::SUCCESS;
    }
}
