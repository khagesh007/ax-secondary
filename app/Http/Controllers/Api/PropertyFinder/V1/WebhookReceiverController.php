<?php

namespace App\Http\Controllers\Api\PropertyFinder\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\PFWebhookLog;
class WebhookReceiverController extends Controller
{
    public function handle(Request $request)
    {

        $raw = $request->getContent(); // always get raw body
        $data = json_decode($raw, true); // decode to array


        Log::channel('pf_webhook')->info('Raw Request Data', ['request' => $request->all()]);
        Log::channel('pf_webhook')->info('PropertyFinder Webhook Received - Parsed', ['data' => $data]);

        if (!isset($data['type']) || !isset($data['payload'])) {
            Log::channel('pf_webhook')->warning('Invalid webhook format', ['data' => $data]);
            return response()->json(['error' => 'Invalid webhook format'], 400);
        }
        try {
            PFWebhookLog::create([
                'webhook_id'   => $data['id'] ?? null,
                'type'         => $data['type'] ?? null,
                'payload'      => $data ?? [],
                'meta'         => $data['meta'] ?? [],
                'is_processed' => false,
                'processed_at' => null,
                'status'       => 'pending',
            ]);
            return response()->json(['status' => 'ok']);
        } catch (\Throwable $e) {
            Log::channel('pf_webhook')->error('Failed to store webhook', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Internal Server Error'], 500);
        }

    }
}
