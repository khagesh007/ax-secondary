<?php
namespace App\Services\PropertyFinder\V1;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

use App\Services\PropertyFinder\BasePropertyFinderService;

class PropertyFinderTokenService extends BasePropertyFinderService
{
    public function getToken(): ?string
    {
        return Cache::remember('pf_v1_token', now()->addMinutes(20), function () {
            $url = $this->fullUrl('auth/token');

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($url, [
                'apiKey' => $this->apiKey,
                'apiSecret' => $this->apiSecret,
            ]);
            
            if ($response->successful()) {
                return $response->json('accessToken');
            }

            logger()->error('PropertyFinder token fetch failed', [
                'response' => $response->body()
            ]);

            return null;
        });
    }
}
