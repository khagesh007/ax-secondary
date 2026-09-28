<?php
namespace App\Services\PropertyFinder;

abstract class BasePropertyFinderService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $apiSecret;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.propertyfinder.base_url'), '/') . '/';
        // $this->apiKey = config('services.propertyfinder.api_key');
        // $this->apiSecret = config('services.propertyfinder.api_secret');

    }

    public function fullUrl(string $path): string
    {
        return $this->baseUrl . ltrim($path, '/');
    }
}
