<?php

namespace App\Console\Commands;

use App\Models\Property;
use Illuminate\Console\Command;
use App\Traits\PropertyFinderTrait;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class PFV2SyncPropertyId extends Command
{
    use PropertyFinderTrait;

    protected $signature = 'pf:sync-property-id {--reset : Reset pagination and start from page 1}';
    protected $description = 'Sync Property Finder IDs with pagination cache';

    public function handle()
    {
        $cacheKey = 'pf_sync_pagination';

        // Reset pagination if requested
        if ($this->option('reset')) {
            Cache::forget($cacheKey);
            $this->info('Pagination cache reset. Starting from page 1.');
        }

        // Get cached pagination data
        $paginationData = Cache::get($cacheKey, [
            'currentPage' => 1,
            'totalPages' => null,
            'processedCount' => 0,
            'isComplete' => false
        ]);

        // Check if sync is already complete
        if ($paginationData['isComplete']) {
            $this->info('Sync already completed. Use --reset to start over.');
            return Command::SUCCESS;
        }

        $url = 'https://atlas.propertyfinder.com/v1/listings';
        $token = $this->getPfAccessTokenV2();
        if (!$token) {
            $this->error('Failed to get Property Finder token.');
            return Command::FAILURE;
        }

        $headers = [
            'Authorization' => 'Bearer ' . $token['token'],
            'Content-Type' => 'application/json',
        ];

        $currentPage = $paginationData['currentPage'];
        $this->info("Processing page {$currentPage}...");

        $payload = [
            'page' => $currentPage,
            'limit' => 1000,
            'sort' => 'created_at',
            'order' => 'desc',
        ];

        $response = Http::withHeaders($headers)->get($url, $payload);

        if ($response->failed()) {
            $this->error('Failed to fetch listings from Property Finder: ' . $response->body());
            return Command::FAILURE;
        }

        $data = $response->json();
        $pagination = $data['pagination'] ?? [];
        $totalPages = $pagination['totalPages'] ?? 1;

        // Update total pages on first run
        if ($paginationData['totalPages'] === null) {
            $paginationData['totalPages'] = $totalPages;
        }

        $this->info("Page {$currentPage} of {$totalPages}");

        if (empty($data['results']) || !is_array($data['results'])) {
            $this->info('No listings found on this page.');
        } else {
            $pageProcessedCount = $this->processListings($data['results']);
            $paginationData['processedCount'] += $pageProcessedCount;
            $this->info("Processed {$pageProcessedCount} properties on this page.");
        }

        // Update pagination for next run
        $paginationData['currentPage']++;

        // Check if we've reached the end
        if ($paginationData['currentPage'] > $totalPages) {
            $paginationData['isComplete'] = true;
            $this->info("Sync completed! Total processed: {$paginationData['processedCount']} properties across {$totalPages} pages.");
            Cache::forget($cacheKey);
        } else {
            $this->info("Next run will process page {$paginationData['currentPage']}. Total processed so far: {$paginationData['processedCount']}");
            // Cache the updated pagination data
            Cache::put($cacheKey, $paginationData, now()->addHours(24));
        }

        return Command::SUCCESS;
    }

    private function processListings(array $listings): int
    {
        $processedCount = 0;

        foreach ($listings as $listing) {
            $propertyId = $listing['id'] ?? null;
            if (!$propertyId) {
                $this->error('Property ID not found in listing');
                continue;
            }

            // Check if property already exists
            $existingProperty = Property::where('portal_ids->property_finder_id', $propertyId)->first();
            if ($existingProperty) {
                continue;
            }

            $ref_no = $listing['reference'] ?? null;
            $property = Property::where('ref_no', $ref_no)->first();
            if (!$property) {
                $this->error("Property with reference {$ref_no} not found.");
                continue;
            }

            $currentPortalIds = json_decode($property->portal_ids, true) ?? [];
            if (isset($currentPortalIds['property_finder_id'])) {
                continue;
            }

            $property->portal_ids = json_encode(array_merge(
                $currentPortalIds,
                ['property_finder_id' => $propertyId]
            ));
            $property->withoutApproval()->save();
            $processedCount++;

            $this->info("Updated property {$ref_no} with PF ID {$propertyId}");
        }

        return $processedCount;
    }
}
