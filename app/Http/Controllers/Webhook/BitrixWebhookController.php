<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;

use App\Models\TourLog;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BitrixWebhookController extends Controller
{

    public function bitrixTourLog(Request $request)
    {
        $token = $request->get('key');
        if( !$token || $token !== 'AX!Bitrix!TourLog@!3@' ) {
            $request_details = [
                'ip' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'headers' => $request->headers->all(),
            ];
            Log::channel('bitrix_tour_log')->error('Unauthorized access attempt to Bitrix Tour Log', ['key' => $request_details]);
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }
        $data = $request->all() ?? [];
        Log::channel('bitrix_tour_log')->info('Bitrix Tour Log received at secondary.', [json_encode($data,true)]);
        try{

        $log = new TourLog();
        $log->meta = json_encode($data);
        $log->save();


        } catch (\Exception $e) {
            Log::error('Error saving Bitrix Tour Log: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to save log'], 500);
        }

        return response()->json(['status' => 'success']);
    }


}
