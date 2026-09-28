<?php

namespace App\Http\Controllers\Api\PropertyFinder\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\PropertyFinder\V1\PropertyFinderTokenService;
use App\Traits\PropertyFinderTrait;

class WebhookApiController extends Controller
{
    use PropertyFinderTrait;

    protected PropertyFinderTokenService $tokenService;
    protected ?string $token;

    public function __construct(PropertyFinderTokenService $tokenService)
    {
        $this->tokenService = $tokenService;
        //$this->token = $this->tokenService->getToken();
        $tokens = $this->getPfAccessToken('secondary');

        if (isset($tokens['token']) && $tokens['token'] != '') {
            $this->token = $tokens['token'];
        }
        if (!$this->token) {
            logger()->error('Failed to fetch PropertyFinder API token in WebhookApiController constructor.');
            abort(500, 'Unable to fetch API token.');
        }
    }

    public function index(Request $request)
    {
        try {
            $url = $this->tokenService->fullUrl('webhooks');

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
                'Accept' => 'application/json',
            ])->get($url);

            if ($response->successful()) {

                return $this->sendDataResponse($response->json(), 'Webhooks retrieved successfully.');
            }

            Log::channel('pf_webhook')->error('PropertyFinder webhook list fetch failed', [
                'response' => $response->body(),
                'status' => $response->status(),
            ]);

            return response()->json(['error' => 'Failed to fetch webhook list'], 500);
        } catch (\Throwable $exception) {
            $statusCode = ($exception instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface)
                ? $exception->getStatusCode()
                : 500;

            return $this->sendException(
                'Error',
                'An error occurred while retrieving Amenities.',
                $exception->getMessage(),
                $statusCode,
                $exception->getLine(),
                $exception->getFile()
            );
        }
    }
    public function store(Request $request, string $eventId, ?string $secret = null)
    {
        $validEvents = [
            'user.created',
            'user.updated',
            'user.deleted',
            'user.activated',
            'user.deactivated',
            'lead.created',
            'lead.updated',
            'lead.assigned',
        ];

        if (!in_array($eventId, $validEvents)) {
            return response()->json(['error' => 'Invalid event ID'], 422);
        }

        try {
            $url = $this->tokenService->fullUrl('webhooks');

            $callbackUrl = 'https://secondary.axcrm.ae/api/pfv1/webhooks/propertyfinder';

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
                'Accept' => 'application/json',
            ])->post($url, [
                'eventId' => $eventId,
                'callbackUrl' => $callbackUrl,
                'secret' => $secret,
            ]);

            if ($response->successful()) {
                return $this->sendDataResponse($response->json(), 'Webhook subscription created successfully.');
            }

            Log::channel('pf_webhook')->error('PropertyFinder webhook subscription failed', [
                'response' => $response->body(),
                'status' => $response->status(),
            ]);

            return response()->json(['error' => 'Failed to subscribe to webhook'], 500);
        } catch (\Throwable $exception) {
            return $this->sendException(
                'Error',
                'An error occurred while subscribing to the webhook.',
                $exception->getMessage(),
                ($exception instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface)
                    ? $exception->getStatusCode()
                    : 500,
                $exception->getLine(),
                $exception->getFile()
            );
        }

    }

    public function delete($eventId)
    {
        try{
            if (empty($eventId)) {
                return response()->json(['error' => 'Event ID is required'], 400);
            }

            $url = $this->tokenService->fullUrl("webhooks/{$eventId}");
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
                'Accept' => 'application/problem+json',
            ])->delete($url);

            if ($response->successful()) {
                return $this->sendDataResponse($response->json(), 'Webhook unsubscription  successfully.');
            }

            Log::channel('pf_webhook')->error('PropertyFinder webhook subscription failed', [
                'response' => $response->body(),
                'status' => $response->status(),
            ]);

            return response()->json(['error' => 'Failed to unsubscribe from webhook'], 500);
        } catch (\Throwable $exception) {
            $statusCode = ($exception instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface)
                ? $exception->getStatusCode()
                : 500;

            return $this->sendException(
                'Error',
                'An error occurred while unsubscribing to the webhook.',
                $exception->getMessage(),
                $statusCode,
                $exception->getLine(),
                $exception->getFile()
            );
        }

    }

    public function subscribeToAll(?string $secret = null)
    {
        try{
            $events = [
                'user.created',
                'user.updated',
                'user.deleted',
                'user.activated',
                'user.deactivated',
                'lead.created',
                'lead.updated',
                'lead.assigned',
                'publicProfile.verification.approved',
                'publicProfile.verification.rejected'
            ];

            $url = $this->tokenService->fullUrl('webhooks');
            $callbackUrl = 'https://secondary.axcrm.ae/api/pfv1/webhooks/propertyfinder';

            $results = [];
            $token = $this->getPfAccessTokenV2('secondary');


            foreach ($events as $eventId) {
                try {
                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $token['token'],
                        'Accept' => 'application/json',
                    ])->post($url, [
                        'eventId' => $eventId,
                        'callbackUrl' => $callbackUrl,
                        'secret' => $secret,
                    ]);

                    if ($response->successful()) {
                        $results[$eventId] = 'Subscribed';
                    } else {
                        $results[$eventId] = 'Failed: ' . $response->status();
                        logger()->error('Subscription failed for ' . $eventId, ['response' => $response->body()]);
                    }
                } catch (\Throwable $e) {
                    $results[$eventId] = 'Exception: ' . $e->getMessage();
                }
            }

            return response()->json(['results' => $results]);

        }catch (\Throwable $exception) {
            $statusCode = ($exception instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface)
                ? $exception->getStatusCode()
                : 500;

            return $this->sendException(
                'Error',
                'An error occurred while unsubscribing to the webhook.',
                $exception->getMessage(),
                $statusCode,
                $exception->getLine(),
                $exception->getFile()
            );
        }
    }
}
