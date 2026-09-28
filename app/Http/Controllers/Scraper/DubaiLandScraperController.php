<?php

namespace App\Http\Controllers\Scraper;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Lead;

class DubaiLandScraperController extends Controller
{
    public function scrape(Request $request)
    {

        $response = Http::withHeaders([
            'consumer-id' => 'gkb3WvEG0rY9eilwXC0P2pTz8UzvLj9F',
        ])->get('https://b2c.dubailand.gov.ae/mashrooi/projects/searchlite');

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
            return response()->json($projectDetails); // Returning JSON response

        } else {

            dd($response->status(), $response->body());
        }


    }



    public function leadPoolToArchive(Request $request){
        // return Lead::where('is_archive',true)->where('utm_medium',1122)->count();

        $query = Lead::query();
        $query->where('status',true);
        $query->where('is_archive',false);
        $query->whereNull('agent_id')->whereNull('previous_agent_id');
        $query->whereIn('source_id',[4,13,31]); // bayut,Dubizzle,PropertyFinder
        // return $query->count();

        // update is_archive to true and utm_medium 1122
        $query->update(['is_archive'=>true,'utm_medium'=>1122]);

        return "Leads Moved to archive";

    }



}
