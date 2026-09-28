<?php

namespace App\Jobs;

use App\Models\OffplanMeta;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class GeneratePropertyTitleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $timeout = 1200;

    protected $keywords;
    protected $total;
    protected $dev_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($keywords, $total, $dev_id, )
    {
        $this->keywords = $keywords;
        $this->total = $total;
        $this->dev_id = $dev_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $keywords = $this->keywords;
        $total = $this->total;
        $dev_id = $this->dev_id;

        for ($i = 0; $i < $total; $i++) {
            $apiKey = env('CHAT_API');
            $apiUrl = 'https://api.openai.com/v1/chat/completions';
            $message = 'Write the following title for property listing for AX CAPITAL company 3-4 words. Do not use any symbol or special character. Just you can use is | if required generate about 50 to 100 characters. Keywords are ' . $keywords;
            $response = Http::timeout(120)->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post($apiUrl, [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                    ['role' => 'user', 'content' => $message],
                ],
            ]);
            $responseData = $response->json();
            $msg = $responseData['choices'][0]['message']['content'];

            $developer = new OffplanMeta();
            $developer->developer_project_id = $dev_id;
            $developer->key = 'title';
            $developer->value = $msg;
            $developer->save();

        }

    }
}
