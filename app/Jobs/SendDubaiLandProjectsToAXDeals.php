<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SendDubaiLandProjectsToAXDeals implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $response = Http::withHeaders([
            'consumer-id' => 'gkb3WvEG0rY9eilwXC0P2pTz8UzvLj9F',
        ])->get('https://b2c.dubailand.gov.ae/mashrooi/projects/searchlite');

        info($response->status());
        if ($response->successful()) {
            $data = $response->json();
            $projects = $data['response']['projects'];

            // Loop through projects
            foreach ($projects as $project) {
                $projectDetails[] = [
                    'number' => $project['number'],
                    'name' => $project['name']['englishName'],
                    'status' => $project['status']['englishName'],
                    'completionRatio' => $project['completionRatio'],
                    'mainDeveloperNumber' => $project['mainDeveloper']['number'],
                    'mainDeveloperName' => $project['mainDeveloper']['name']['englishName'],
                    'mainDeveloperLogo' => $project['mainDeveloper']['logo']['mediaUrl'],
                    'mainDeveloperRating' => $project['mainDeveloper']['rating'],
                    'mainDeveloperContactMobile' => $project['mainDeveloper']['contact']['mobile'],
                    'mainDeveloperContactPhone' => $project['mainDeveloper']['contact']['phone'],
                    'mainDeveloperContactEmail' => $project['mainDeveloper']['contact']['email'],
                    'mainDeveloperContactUrl' => $project['mainDeveloper']['contact']['url'],
                    'mainDeveloperIsFollowed' => $project['mainDeveloper']['isFollowed'],

                    'developerNumber' => $project['developer']['number'],
                    'developerName' => $project['developer']['name']['englishName'],
                    'developerLogo' => $project['developer']['logo']['mediaUrl'],
                    'developerRating' => $project['developer']['rating'],
                    'mainDeveloperContactMobile' => $project['developer']['contact']['mobile'],
                    'developerContactPhone' => $project['developer']['contact']['phone'],
                    'developerContactEmail' => $project['developer']['contact']['email'],
                    'developerContactUrl' => $project['developer']['contact']['url'],
                    'developerIsFollowed' => $project['developer']['isFollowed'],
                    'locationLat' => $project['location']['googleCoordinates']['latitude'],
                    'locationLong' => $project['location']['googleCoordinates']['longitude'],
                    'locationStreet' => $project['location']['street']['englishName'],
                    'locationState' => $project['location']['state']['englishName'],
                    'locationCountry' => $project['location']['country']['englishName'],
                    'locationArea' => $project['location']['area']['englishName'],

                ];

            }
        
            if (env('APP_ENV') == 'local') {
                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'X-WEBHOOK-KEY' => '1234567890',
                ])->post('http://deals.lan/webhook/dubai-land-projects/receive', $projectDetails);
            } else {
                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'X-WEBHOOK-KEY' => '1234567890',
                ])->post('http://deals.lan/webhook/dubai-land-projects/receive', $projectDetails);
            }

            if ($response->successful()) {
                return response()->json($response->json());
            } else {
                return response()->json($response->json());
            }

        } else {

            dd($response->status(), $response->body());
        }

    }
}
