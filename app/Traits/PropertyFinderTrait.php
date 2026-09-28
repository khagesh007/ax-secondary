<?php

namespace App\Traits;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
trait PropertyFinderTrait
{

    public function getPfAccessToken($entity = 'secondary',$force = false)
    {
        //this will fetch access token using key and secret
        try {
            if (!$force && Cache::has('pf_access_token')) {

                if($entity == 'primary'){
                    $token = Cache::get('pf_primary_access_token');
                }elseif($entity == 'commercial'){
                    $token = Cache::get('pf_commercial_access_token');
                }elseif($entity == 'abu_dhabi'){
                    $token = Cache::get('pf_abu_dhabi_access_token');
                }else{
                    $token = Cache::get('pf_secondary_access_token');
                }

                $result = ['status' => '1', 'token' => $token];
                return $result;
            } else {
                if ($entity == 'primary') {
                    $token = env('PROPERTY_FINDER_PRIMARY_TOKEN');
                    $key = env('PROPERTY_FINDER_PRIMARY_KEY');
                } elseif ($entity == 'commercial') {
                    $token = env('PROPERTY_FINDER_COMMERCIAL_TOKEN');
                    $key = env('PROPERTY_FINDER_COMMERCIAL_KEY');
                } elseif ($entity == 'abu_dhabi') {
                    $token = env('PROPERTY_FINDER_ABU_DHABI_TOKEN');
                    $key = env('PROPERTY_FINDER_ABU_DHABI_KEY');
                } else {
                    $token = env('PROPERTY_FINDER_SECONDARY_TOKEN');
                    $key = env('PROPERTY_FINDER_SECONDARY_KEY');
                }
                $secret = $token . ':' . $key;
                $base64 = base64_encode($secret);
                $url = 'https://auth.propertyfinder.com/auth/oauth/v1/token';
                $headers = [
                    'Authorization: Basic ' . $base64,
                    'Content-Type: application/json',
                ];
                $data = [
                    'scope' => 'openid',
                    'grant_type' => 'client_credentials',
                ];
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                $response = curl_exec($ch);
                if (curl_errno($ch)) {
                    echo 'Error: ' . curl_error($ch);
                    \Log::error('Error: ' . curl_error($ch));
                    $result = ['status' => '0', 'token' => ''];
                } else {
                    $decoded_response = json_decode($response, true);
                    if (isset($decoded_response['access_token'])) {
                        if($entity == 'primary'){
                            Cache::put('pf_primary_access_token', $decoded_response['access_token'], now()->addMinutes(30));
                        }elseif($entity == 'commercial'){
                            Cache::put('pf_commercial_access_token', $decoded_response['access_token'], now()->addMinutes(30));
                        }elseif($entity == 'abu_dhabi'){
                            Cache::put('pf_abu_dhabi_access_token', $decoded_response['access_token'], now()->addMinutes(30));
                        }else{
                            Cache::put('pf_secondary_access_token', $decoded_response['access_token'], now()->addMinutes(30));
                        }

                        $result = ['status' => '1', 'token' => $decoded_response['access_token']];
                    } else {
                        $result = ['status' => '0', 'token' => ''];
                    }
                }
                curl_close($ch);
            }
            } catch (\Exception $e) {
            $result = ['status' => '0', 'token' => ''];
        }
        return $result;
    }

    public function getPFTokenBasedOnProperty($property)
    {

        return $this->getJwtToken();
        $agent = $property->agent;
        if ($property->region_id == 2) {
            $token = env('PROPERTY_FINDER_ABU_DHABI_TOKEN');
             $token_data = $this->getPfAccessToken('primary');
        } elseif ($property->completion_status == "Off-Plan Primary" && (!$agent?->is_commercial)) {
            $token = env('PROPERTY_FINDER_PRIMARY_TOKEN');
            $token_data = $this->getPfAccessToken('primary');
        } elseif ($agent?->is_commercial) {
            $token = env('PROPERTY_FINDER_COMMERCIAL_TOKEN');
            $token_data = $this->getPfAccessToken('commercial');
        } else {
            $token = env('PROPERTY_FINDER_SECONDARY_TOKEN');
            $token_data = $this->getPfAccessToken('secondary');
        }
        return $token_data['token'] ?? '';
    }

    public function getPFTokenBasedOnPropertyV2($property, $force = false)
    {

        $agent = $property->agent;
        if ($property->region_id == 2) {
             $token_data = $this->getPfAccessTokenV2('abu_dhabi', $force);
        } elseif ($property->completion_status == "Off-Plan Primary" && (!$agent?->is_commercial)) {
            $token_data = $this->getPfAccessTokenV2('primary', $force);
        } elseif ($agent?->is_commercial) {
            $token_data = $this->getPfAccessTokenV2('commercial', $force);
        } else {
            $token_data = $this->getPfAccessTokenV2('secondary', $force);
        }
        return $token_data['token'] ?? '';
    }
    public function getPFToken()
    {
        $token_data = $this->getPfAccessToken('secondary');
        return $token_data['token'] ?? '';
    }

    public function getJwtToken($force = false)
    {
        /*
         * This is for Property Finder Enterprise API
         * This function is used to get the JWT token for Property Finder.
         * The token is cached for 30 minutes.
         */
        if(!$force && Cache::has('pf_jwt_token')) {
            return Cache::get('pf_jwt_token');
        }
        $url = "https://atlas.propertyfinder.com/v1/auth/token";
        $headers = [
            'Content-Type: application/json'
        ];
        $data = [
            'apiKey' => env('PROPERTY_FINDER_SECONDARY_KEY_V2'),
            'apiSecret' => env('PROPERTY_FINDER_SECONDARY_TOKEN_V2')
        ];
        $resopnse = Http::withHeaders($headers)->post($url, $data);
        if ($resopnse->failed()) {
            \Log::error('Error fetching Property Finder JWT token: ' . $resopnse->body());
            return '';
        }
        $decoded_response = $resopnse->json();
        if (isset($decoded_response['accessToken'])) {
            Cache::put('pf_jwt_token', $decoded_response['accessToken'], now()->addMinutes(30));
            return $decoded_response['accessToken'];
        }else{
            \Log::error('Error fetching Property Finder JWT token: ' . $resopnse->body());
            return '';
        }
    }


public function getPfAccessTokenV2($entity = 'secondary', $force = false)
{
    /*
     * This is for Property Finder Enterprise API V2
     * This function is used to get the JWT token for Property Finder.
     * The token is cached for 30 minutes.
     */

    // Check cache first (if not forced)
    $cacheKey = 'pf_jwt_token_' . $entity;
    if (!$force && Cache::has($cacheKey)) {
        return [
            'status' => '1',
            'token' => Cache::get($cacheKey)
        ];
    }

    try {
        // Get API credentials based on entity
        if ($entity == 'primary') {
            $apiKey = env('PROPERTY_FINDER_PRIMARY_KEY_V2');
            $apiSecret = env('PROPERTY_FINDER_PRIMARY_TOKEN_V2');
        } elseif ($entity == 'commercial') {
            $apiKey = env('PROPERTY_FINDER_COMMERCIAL_KEY_V2');
            $apiSecret = env('PROPERTY_FINDER_COMMERCIAL_TOKEN_V2');
        } elseif ($entity == 'abu_dhabi') {
            $apiKey = env('PROPERTY_FINDER_ABU_DHABI_KEY_V2');
            $apiSecret = env('PROPERTY_FINDER_ABU_DHABI_TOKEN_V2');
        } else {
            // Default to secondary
            $apiKey = env('PROPERTY_FINDER_SECONDARY_KEY_V2');
            $apiSecret = env('PROPERTY_FINDER_SECONDARY_TOKEN_V2');
        }



        $url = "https://atlas.propertyfinder.com/v1/auth/token";
        $headers = [
            'Content-Type: application/json'
        ];
        $data = [
            'apiKey' => $apiKey,
            'apiSecret' => $apiSecret
        ];

        $response = Http::withHeaders($headers)->post($url, $data);

        if ($response->failed()) {
            \Log::error("Error fetching Property Finder JWT token for {$entity}: " . $response->body());
            return ['status' => '0', 'token' => ''];
        }

        $decoded_response = $response->json();

        if (isset($decoded_response['accessToken'])) {
            // Cache the token for 30 minutes
            Cache::put($cacheKey, $decoded_response['accessToken'], now()->addMinutes(30));
            return [
                'status' => '1',
                'token' => $decoded_response['accessToken']
            ];
        } else {
            \Log::error("Error fetching Property Finder JWT token for {$entity}: " . $response->body());
            return ['status' => '0', 'token' => ''];
        }

    } catch (\Exception $e) {
        \Log::error("Exception while fetching Property Finder JWT token for {$entity}: " . $e->getMessage());
        return ['status' => '0', 'token' => ''];
    }
}

    public function getEntityByPropertyForPFV2($property)
    {
        $agent = $property->agent;
        if ($property->region_id == 2) {
            return 'abu_dhabi';
        } elseif ($property->completion_status == "Off-Plan Primary" && (!$agent?->is_commercial)) {
            return 'primary';
        } elseif ($agent?->is_commercial) {
            return 'commercial';
        }
        return 'secondary';
    }



}
