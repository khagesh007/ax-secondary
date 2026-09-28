<?php

namespace App\Http\Controllers\Testing;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
class BitrixUserController extends Controller
{
    public function fetchBitrixUsers()
    {

        // $users = User::where('bitrix_role', 'like', '%Primary%')->where('bitrix_role', 'not like', '%Primary Admin%')->get();
        // $sec_users = User::where('bitrix_role', 'like', '%Secondary%')->where('bitrix_role', 'not like', '%Secondary Admin%')->get();
        // $chinese_users = User::where('bitrix_role', 'like', '%Chinese%')->get();

        // $data = [
        //     'primary' => $users->count(),
        //     'secondary' => $sec_users->count(),
        //     'chinese' => $chinese_users->count(),
        // ];

        // return response()->json($data);

        $url = 'https://crm.axcap.ae/company-info/js/orgchart.json';
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Cookie: BITRIX_SM_SALE_UID=0; PHPSESSID=hjkkOXcPrgiRmKbZvtr76dtfkwr1kzJx',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $response = json_decode($response);
        $counter = 0;
        foreach ($response as $user) {
            $user = (array) $user;
            $bitrix_email = $user['Email'];
            $find_user = \App\Models\User::where('email', $bitrix_email)->first();
            if ($find_user) {
                $find_user->bitrix_role = $user['Department'];
                $find_user->save();
                $counter++;
            }

        }
        return $counter;

    }

    public function updateDealInfoInUsers()
    {

        $api_key = env('BITRIX_API_KEY');
        $users = User::where('status', true)->get();
        $counter = 0;
        $counter_start = 0;
        $counter_deal = 0;
        foreach ($users as $user) {
            $counter++;
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://crm.axcap.ae/local/webhooks/getLastDeal.php',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array('api_key' => $api_key, 'user_id' => $user->bitrix_id),
                CURLOPT_HTTPHEADER => array(
                    'Cookie: BITRIX_SM_GUEST_ID=308332; BITRIX_SM_LAST_VISIT=01.02.2024+18%3A15%3A33; BITRIX_SM_SALE_UID=0; PHPSESSID=2ZncvWlsLTrQJ21eCsLlns6STTDDm3r6',
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            $response = json_decode($response);
            if ($response->deal_date !== false) {
                $counter_deal++;
                try {
                    $inputDate = $response->deal_date;
                    $hasTime = strpos($inputDate, ':') !== false;
                    $expectedFormat = $hasTime ? 'd.m.Y h:i:s a' : 'd.m.Y';
                    $dateTimeObject = DateTime::createFromFormat($expectedFormat, $inputDate);
                    if ($dateTimeObject !== false) {
                        $formattedDateTime = $dateTimeObject->format('Y-m-d' . ($hasTime ? ' H:i:s' : ''));
                        $user->last_deal_date = $formattedDateTime;
                    } else {
                        info('Deal: '.$response->deal_date);
                        $user->last_deal_date = null;
                    }
                } catch (\Throwable $th) {
                    throw $th;
                }
            }




            if ($response->start_date !== false) {
                $counter_start++;
                try {
                    $inputDate = $response->start_date;
                    $hasTime = strpos($inputDate, ':') !== false;
                    $expectedFormat = $hasTime ? 'd.m.Y h:i:s a' : 'd.m.Y';
                    $dateTimeObject = DateTime::createFromFormat($expectedFormat, $inputDate);
                    if ($dateTimeObject !== false) {
                        $formattedDateTime = $dateTimeObject->format('Y-m-d' . ($hasTime ? ' H:i:s' : ''));
                        $user->joining_date = $formattedDateTime;

                    } else {
                        info('Joining: '.$response->start_date);
                        $user->joining_date = null;
                    }
                } catch (\Throwable $th) {
                    throw $th;
                }

            }

            $user->save();

        }
        return response()->json(['users' => $counter, 'start' => $counter_start, 'deal' => $counter_deal]);

    }

    public function updateExemption(){
        $arry = [
        712,
        83491,
        29081,
        8445,
        118652,
        76298,
        87704,
        74880,
        83052,
        87748,
        99392,
        62524,
        221183,
        223626,
        147589,
        236518,
        220730,
        233490,
        233376,
        8166,
        27,
        1,
        109,
        489,
        639,
        653,
        664,
        676,
        677,
        634,
        420,
        499,
        34,
        666,
        637,
        602,
        513,
        8166,
        616,
        691,
        48882,
        2308,
        695,
        12835,
        606,
        29081,
        83052,
        80520,
        504,
        58753,
        99928,
        50937,
        625,
        74880,
        220730,
        23339,
        91723,
        4969,
        248307,
        250604,
        248049,
        244378,
        233490,
        227423,
        223626];

        $counter = 0;
        foreach($arry as $id){

            $user = User::where('bitrix_id', $id)->first();
            if($user){
                $user->is_report_exempt = 1;
                $user->save();
                $counter++;
            }
        }
        return response()->json(['status' => 'success', 'users' => $counter]);

    }
}
