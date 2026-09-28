<?php

namespace App\Console\Commands;

use App\Models\GeneralSetting;
use App\Models\PfCreditHistory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class PfGetUserCreditHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pf:get-pf-user-credit-history';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will fetch the user credit history from PF API and save it to the local database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $pf_token = GeneralSetting::where('slug', 'pf-agent-data-api-token')->first();

        $access_token = $pf_token->value;

        $date = date('Y-m-d');

        echo 'Running credit history api for date: ' . $date;

        $start_date = Carbon::parse($date . ' 00:00:00')->timezone('UTC')->format('Y-m-d\TH:i:s.000\Z');
        $end_date = Carbon::parse($date . ' 23:59:59')->timezone('UTC')->format('Y-m-d\TH:i:s.000\Z');
        $curl = curl_init();
        $this->info('Fetching data from PF API for date: ' . $start_date . ' to ' . $end_date);
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api-v2.mycrm.com/clients/credits/history?filter[created_at][from]=$start_date&filter[created_at][to]=$end_date&page[offset]=0&page[limit]=500",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: ' . $access_token,
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $data = json_decode($response, true);


        if (isset($data['error']) && $data['error'] == 'AuthenticationError') {
            echo 'PF token expired when fetching credit data';

            return true;
        }
        if (isset($data['data'])) {
            $credit_histories = $data['data'];
            foreach ($credit_histories as $credit_history) {

                $offeringType = $credit_history['attributes']['description'];

                if (Str::contains($offeringType, 'Featured') || Str::contains($offeringType, 'Premium')) {
                    $pf_user_id = $credit_history['attributes']['customer']['agent']['id'] ?? null;
                    $balance = $credit_history['attributes']['wallet_value_after'] ?? null;
                    $point = $credit_history['attributes']['credits'] ?? null;
                    $action_at = $credit_history['attributes']['created_at'] ?? null;
                    $action = $credit_history['attributes']['action'] ?? null;
                    $action = $action == 'add' ? 'Credit' : 'Debit';

                    $pf_name = $credit_history['attributes']['customer']['agent']['name'] ?? null;
                    $user = User::where('third_party_ids->pf_user_id', $pf_user_id)->first();
//                        echo $pf_name.' - User name in local - '.$user?->name ?? 'null';

                    if ($user != null) {
                        $current_time = Carbon::parse($action_at)->timezone(config('app.timezone'));
                        $this->info($pf_name . ' ' . $current_time->toDateTimeString() . '  - Action - ' . $action . ' - Point - ' . $point . ' - Balance - ' . $balance);

                        // Find existing record for today
                        $existing_record = PfCreditHistory::where('user_id', $user->id)
                            ->whereDate('transaction_time', $current_time->toDateString())
                            ->orderBy('transaction_time', 'desc')
                            ->first();

                        if ($existing_record) {
                            $existing_time = Carbon::parse($existing_record->transaction_time);

                            // Compare if new action_at is later than existing transaction_time
                            if ($current_time->gt($existing_time)) {
                                // Update the existing record with new values
                                $existing_record->transaction_time = $current_time;
                                $existing_record->type = $action;
                                $existing_record->point = $point;
                                $existing_record->balance = $balance;
                                $existing_record->save();
                            }
                            // Else, do nothing (because the existing record is already the latest)
                        } else {
                            // No record for today yet, create new
                            $pf_credit_history = new PfCreditHistory();
                            $pf_credit_history->user_id = $user->id;
                            $pf_credit_history->transaction_time = $current_time;
                            $pf_credit_history->type = $action;
                            $pf_credit_history->point = $point;
                            $pf_credit_history->balance = $balance;
                            $pf_credit_history->save();
                        }
                    }
                }
            }
        }
        return Command::SUCCESS;
    }
}
