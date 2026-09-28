<?php

namespace App\Http\Controllers\Api\PropertyFinder\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\PFWebhookLog;

class WebHookLogApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $perPage = request('per_page', 10);
            $page = request('page', 1);
            $query = PFWebhookLog::query();
            if ($type = request('type')) {
                $query->where('type', $type);
            }
            if( $status = request('status')) {
                $query->where('status', $status);

            }
            if ($isProcessed = request('is_processed')) {
                $query->where('is_processed', filter_var($isProcessed, FILTER_VALIDATE_BOOLEAN));
            }
            if ($processedAt = request('processed_at')) {
                $query->whereDate('processed_at', $processedAt);
            }
            if ($createdAt = request('created_at')) {
                $query->whereDate('created_at', $createdAt);
            }
            if($is_processed = request('is_processed')) {
                $query->where('is_processed', filter_var($is_processed, FILTER_VALIDATE_BOOLEAN));

            }
            if ($webhookId = request('webhook_id')) {
                $query->where('webhook_id', $webhookId);
            }
    
            $logs = $query->orderBy('created_at', 'desc')->paginate($perPage, ['*'], 'page', $page);
    
            return response()->json($logs, 200);
        } catch (\Throwable $exception) {
            Log::error('Failed to fetch webhook logs', [
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Failed to fetch webhook logs'], 500);
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $log = PFWebhookLog::findOrFail($id);
            return response()->json($log, 200);
        } catch (\Throwable $exception) {
            Log::error('Failed to fetch webhook log', [
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Webhook log not found'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $log = PFWebhookLog::findOrFail($id);
            $log->update($request->all());
            return response()->json(['message' => 'Webhook log updated successfully'], 200);
        } catch (\Throwable $exception) {
            Log::error('Failed to update webhook log', [
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Failed to update webhook log'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $log = PFWebhookLog::findOrFail($id);
            $log->delete();
            return response()->json(['message' => 'Webhook log deleted successfully'], 200);
        } catch (\Throwable $exception) {
            Log::error('Failed to delete webhook log', [
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Failed to delete webhook log'], 500);
        }
    }
}
