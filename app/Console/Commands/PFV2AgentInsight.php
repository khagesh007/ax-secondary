<?php

namespace App\Console\Commands;

use App\Models\PortalMeta;
use App\Models\User;
use App\Models\UserThirdPartyData;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Traits\PropertyFinderTrait;
class PFV2AgentInsight extends Command
{
    use PropertyFinderTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pfv2:agent-insight';

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
    private function getPfUserData($auth,$page)
    {
        if(isset($auth['token'])) {
            $url = "https://atlas.propertyfinder.com/v1/users";
            $headers = [
                'Authorization' => 'Bearer ' . $auth['token'],
                'Content-Type' => 'application/json',
            ];

            $params = [
                "page" => $page,
                "perPage" => 100,
                "status" => "active"
            ];

            $response = Http::withHeaders($headers)->get($url, $params);


            if ($response->status() === 200) {
                $data = $response->json();

                if (is_array($data) && isset($data['pagination'])) {

                    return $data;
                }
            }
            return [];

        }else{
            return [];
        }

    }

    public function handle()
    {
        $auth = $this->getPfAccessTokenV2('secondary');
        $super_agent = 0;
        $page = 1;
        $start_date = now()->startOfMonth()->format('Y-m-d');
        $end_date = now()->endOfMonth()->format('Y-m-d');
        do {
            $users_data = $this->getPfUserData($auth, $page);
            $users = $users_data['data'] ?? [];
            $pagination = $users_data['pagination'] ?? [];

            $total_pages = $pagination['totalPages'] ?? 1;
            $total_agents = $pagination['total'] ?? 0;

            if (!empty($users)) {
                foreach ($users as $user) {
                    $is_super_agent = $user['publicProfile']['isSuperAgent'] ?? false;
                    if ($is_super_agent) {
                        $super_agent++;
                    }

                $crm_user = User::where('email', $user['email'])->first();
                    $metaData = [
                        "pf_agent_id" => $user['id'],
                        "fullname" => $user['firstName'] . ' ' . $user['lastName'],
                        "photo_url" => '',
                        "is_super_agent" => $user['publicProfile']['isSuperAgent'] ?? false,
                        "is_verified" => isset($user['publicProfile']) && (($user['publicProfile']['verification']['status'] === 'verified' ?? false)),
                        "total_leads" => 0,
                        "avg_quality_score" => 0,
                        "verified_listing_rate" => 0,
                        "avg_listing_duration" => 0,
                        "response_rate_emails" => 0,
                        "response_rate_calls" =>0,
                        "response_rate_whatsapp" => 0,
                        "response_time_whatsapp" => 0,
                        "overall_responsiveness" => 0,
                        "number_of_properties" => 0,
                        "total_whatsapp_leads" => 0,
                        "total_call_leads" => 0,
                        "total_email_leads" => 0,
                        "deleted_at" => null,
                    ];
                    if($crm_user) {
                        $thirdPartyData = UserThirdPartyData::where('user_id', $crm_user->id)
                            ->where('portal', 'property_finder')
                            ->where('start_date', $start_date)
                            ->where('end_date', $end_date)->first();
                        $userFullData = json_decode($thirdPartyData?->useful_data ?? null, true) ?? [];
                        if (!$thirdPartyData) {
                            $thirdPartyData = new UserThirdPartyData();
                        }

                        $thirdPartyData->user_id = $crm_user->id;
                        $thirdPartyData->portal = 'property_finder';
                        $thirdPartyData->start_date = $start_date;
                        $thirdPartyData->end_date = $end_date;
                        $thirdPartyData->useful_data = json_encode(array_merge($userFullData, $metaData));
                        $thirdPartyData->portal_data_updated_at = Carbon::now();
                        $thirdPartyData->save();
                    }

                }
            }

            echo "Processed page {$page} of {$total_pages}\n";
            $page++;

        } while ($page <= $total_pages);



        PortalMeta::updateOrCreate(
            [
                'portal_id' => 1,
                'meta_key' => 'pf_total_super_agents',
                'start_date' => $start_date,
                'end_date' => $end_date,
            ],
            [
                'meta_value' => $super_agent,
            ]
        );
        PortalMeta::updateOrCreate(
            [
                'portal_id' => 1,
                'meta_key' => 'pf_total_agents',
                'start_date' => $start_date,
                'end_date' => $end_date,
            ],
            [
                'meta_value' => $total_agents,
            ]
        );

        echo "Total Agents: " . $total_agents . "\n";
        echo "Total Super Agents: " . $super_agent . "\n";

        return Command::SUCCESS;
    }
}
