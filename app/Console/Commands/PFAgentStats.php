<?php
namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserMeta;
use App\Models\GeneralSetting;
use Illuminate\Console\Command;
use App\Traits\PropertyFinderTrait;
use Illuminate\Support\Facades\Http;

class PFAgentStats extends Command
{
    use PropertyFinderTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:pfAgentsStats';

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

        $curl = curl_init();
        $pf_token = GeneralSetting::where('slug', 'pf-agent-data-api-token')->first();
        curl_setopt_array($curl, [
            CURLOPT_URL            => 'https://api-v2.mycrm.com/stats/agents',
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
                'authorization: ' . $pf_token->value??'',
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
         info('PFAgentStats cronjob Curl error: ' . json_encode(curl_error($curl)));
            return Command::FAILURE;
        }
        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($http_status != 200) {
            info('PFAgentStats cronjob HTTP error: ' . $http_status .' at '.date('Y-m-d H:i:s'));
            return Command::FAILURE;
        }
        $data = json_decode($response, true);
        foreach ($data['agents'] as $item) {
            $fullName                   = $item['fullname'];
//            if($fullName == 'Grogorii Balardin'){
//                $fullName = 'Mohammad Tameem';
//            }
            list($firstName, $lastName) = explode(' ', $fullName, 2);

            $users = User::where('name', $fullName)->orWhere('pp_name',$fullName)->get();
          foreach ($users as $user){
            if ($user) {
                $metaData = [
                    "pf_agent_id"               => $item['agent_id'],
                    "pf_fullname"               => $item['fullname'],
                    "pf_photo_url"              => $item['photo_url'],
                    "pf_is_super_agent"         => $item['is_super_agent'],
                    "pf_is_verified"            => $item['is_verified'],
                    "pf_total_leads"            => $item['total_leads'],
                    "pf_avg_quality_score"      => $item['avg_quality_score'],
                    "pf_verified_listing_rate"  => $item['verified_listing_rate'],
                    "pf_avg_listing_duration"   => $item['avg_listing_duration'],
                    "pf_response_rate_emails"   => $item['response_rate_emails'],
                    "pf_response_rate_calls"    => $item['response_rate_calls'],
                    "pf_response_rate_whatsapp" => $item['response_rate_whatsapp'],
                    "pf_response_time_whatsapp" => $item['response_time_whatsapp'],
                    "pf_overall_responsiveness" => $item['overall_responsiveness'],
                    "pf_number_of_properties"   => $item['number_of_properties'],
                    "pf_total_whatsapp_leads"   => $item['total_whatsapp_leads'],
                    "pf_total_call_leads"       => $item['total_call_leads'],
                    "pf_total_email_leads"      => $item['total_email_leads'],
                    "pf_deleted_at"             => $item['deleted_at'],
                ];

                foreach ($metaData as $metaKey => $metaValue) {
                    try {
                        UserMeta::storeUserMeta(
                            $user->id,
                            $metaKey,
                            $metaValue !== '' ? $metaValue : null
                        );
                    } catch (\Throwable $th) {
                        info($item['fullname'] . ' - Error storing metadata for user. Check BayutTryBroker.php');
                        continue;
                    }
                }
            } else {

                info($fullName . ' - User not found in the system. Check BayutTryBroker.php');
            }
          }
        }

        return Command::SUCCESS;
    }
}
