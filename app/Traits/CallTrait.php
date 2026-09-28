<?php

namespace App\Traits;

use GuzzleHttp\Client;

trait CallTrait
{
    public function makeCall($userName, $number)
    {
        $fields = '{"user_name":"' . $userName . '","api_key":"hne5jfjkgqn8orsrmx5yi4ai9u4ajf3k","action":"calls.make_call", "to": "' . $number . '"}';
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://axsecondary.moizvonki.ru/api/v1',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $fields,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public function dialANumber($userEmail, $userPhone, $userName)
    {
        // $email = 's.bahar@axcapital.ae';
        // $phone = '+971585858176';
        // $name = 'System User';


        try {
            $email = $userEmail;
            $phone = $userPhone;
            $name = $userName;

            // $url = "https://telelog.axcrm.ae/backend/api/dial-number";
            $url = "https://telelog.axcrm.ae/backend/api/webhooks/calls/dial-number";
            $client = new Client();
            $data = [
                'user_email' => $email,
                'phone' => $phone,
                'name' => $name,
            ];
            $response = $client->post($url, [
                'form_params' => $data,
            ]);

            $statusCode = $response->getStatusCode();
            $responseBody = $response->getBody()->getContents();
            return 'success';
        } catch (\Exception $e) {
            info($e);
            return 'error';
            if ($e->hasResponse()) {
                $statusCode = $e->getResponse()->getStatusCode();
                $responseBody = $e->getResponse()->getBody()->getContents();
                return $responseBody . '-----' . $statusCode;
            } else {
                info('Error sending request: Telelog Webhook. ' . $e->getMessage());
            }
        }

    }

}
