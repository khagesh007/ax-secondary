<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Api\V1\Reporting\StatusScreenApiController;
use App\Models\Region;
use App\Models\Community;
use App\Models\SubCommunity;
use App\Models\CommunityProperty;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class GetPFLocationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:pfLocations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $time_start = microtime(true);
        $page = 1;
        $totalCount = 1;
        $per_page = 100;
        info("Importing locations from mycrm");

        $refresh_token = Cache::get('pf_refresh_token_mycrm') ?? '';
        $tokens = (new StatusScreenApiController())->propertyFinderAuth($refresh_token);
        
        
        if (isset($tokens['access_token'])) {
            $access_token = $tokens['access_token'];
            $response = Http::withHeaders([
                'authorization' => 'Bearer ' . $access_token,
                'content-type' => 'application/json',
                'x-mycrm-expand-data' => true,
            ])->get('https://api-v2.mycrm.com/locations?filters[]="Abu dhabi"&per_page=10');
            //return $res;
        
            $res = $response->json();
            $locations = $res['location'];
            $total_locations_count = $res['count'];
            $total_pages = round(ceil($total_locations_count / $per_page), 0);
            for($page; $page <= $total_pages; $page++){
                    $response = Http::withHeaders([
                        'authorization' => 'Bearer ' . $access_token,
                        'content-type' => 'application/json',
                        'x-mycrm-expand-data' => true,
                    ])->get('https://api-v2.mycrm.com/locations?filters[]="Abu dhabi"&page='.$page.'&per_page='.$per_page);
                    $res = $response->json();
                    $locations = $res['location'];
                    
                    if($locations){
                        info("Page number is $page");
                        foreach($locations as $location){
                            echo "\n#";
                            if($location["city"] == "Abu Dhabi"){
                                echo("\n Community is {$location['community']}");
                                $region = Region::where('name', $location["city"])->first();
                                if(!$region){
                                    $region = new Region();
                                    $region->name = $location["city"];
                                    $region->path_name = $location["city"];
                                    $region->save();
                                }
                                if($location["sub_community"] && $location["community"]){
                                    $community = Community::where('name', $location["community"])->first();
                                    if(!$community){
                                        $community = new Community();
                                        $community->name = $location["community"];
                                        $community->path_name = $location["community"].", ".$location["city"];
                                        $community->latitude = $location["latitude"];
                                        $community->longitude = $location["longitude"];
                                        $community->description = "Imported from mycrm";
                                        $community->region_id = $region->id;
                                        $community->save();
                                    }
                                    $subCommunity = SubCommunity::where('name', $location["sub_community"])->first();
                                    if(!$subCommunity){
                                        $subCommunity = new SubCommunity();
                                        $subCommunity->name = $location["sub_community"];
                                        $subCommunity->region_id = $region->id;
                                        $subCommunity->community_id = $community->id;
                                        $subCommunity->path_name = $location["sub_community"].", ".$location["community"].", ".$location["city"];
                                        $subCommunity->description = "Imported from mycrm";
                                        $subCommunity->latitude = $location["latitude"];
                                        $subCommunity->longitude = $location["longitude"];
                                        $subCommunity->save();
                                    }
                                    if($location["tower"]){
                                       $communityProperty = CommunityProperty::where('name', $location["tower"])->first();
                                        if(!$communityProperty){
                                            $communityProperty = new CommunityProperty();
                                            $communityProperty->name = $location["tower"];
                                            $communityProperty->region_id = $region->id;
                                            $communityProperty->community_id = $community->id;
                                            $communityProperty->path_name = $location["tower"].", ".$location["sub_community"].", ".$location["community"].", ".$location["city"];
                                            $communityProperty->description = "Imported from mycrm";
                                            $communityProperty->sub_community_id = $subCommunity->id;
                                            $communityProperty->latitude = $location["latitude"];
                                            $communityProperty->longitude = $location["longitude"];
                                            $communityProperty->save();
                                        } 
                                    }
                                    $community = [];
                                    $subCommunity = [];
                                    $communityProperty = [];
                                    
                                }
                                $totalCount++;
                            }

                        }
                }
            }
            
            $time_end = microtime(true);
            $time = $time_end - $time_start;

            info( "$time seconds is the time it takes to finish this task");
            echo("\n $totalCount properties are imported successfully");
        } else {
            return [];
        }
        return Command::SUCCESS;
    }
}
