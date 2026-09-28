<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use GuzzleHttp\Client;

class SyncUserToDatahub implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $userId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $user = User::with('roles')->find($this->userId);

            $url_datahub = "https://datahub.axcrm.ae/backend/api/webhook/secondary/user/sync";

            $client = new Client();
            $response = $client->post($url_datahub, [
                'form_params' => $user,
            ]);

            if ($response->getStatusCode() !== 200) {
                Log::warning('User Sync returned non-200', [
                    'user_id' => $user->id,
                    'status'  => $response->getStatusCode(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('User Sync Failed', [
                'user_id' => $this->userId ?? null,
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine()
            ]);
        }
    }
}
