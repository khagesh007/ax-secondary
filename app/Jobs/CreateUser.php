<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class CreateUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $user;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
       
        $userData = $this->user;

        $header  = array();
        $header[] = 'Content-Type: application/json';
        $header[] = 'Connection: keep-alive';

        $response = Http::withHeaders($header)->post('https://api.leadbook.com/api/v1/users', [
            'data' => $userData,
        ]);

        // Process the response or send it back
        return $response;

    }
}
