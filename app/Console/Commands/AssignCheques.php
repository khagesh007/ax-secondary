<?php

namespace App\Console\Commands;
use App\Models\Property;
use Illuminate\Support\Facades\Http;
use Illuminate\Console\Command;

class AssignCheques extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assign:cheques';

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
       info("Updating Property Cheques" . now());
        $time_start = microtime(true);

        $properties = Property::where("publish_status", "PUBLISHED")->get();
        $header  = array();
        $header[] = 'Content-Type: application/json';
        $header[] = 'Content-Length: 4000';
        $header[] = 'Connection: keep-alive';
        if($properties){

            foreach($properties as $property){
                echo"\nChecking for property id ". $property->id;
                echo"\nChecking for my_crm_id id ". $property->my_crm_id;

                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . '150852b2b677aac3bc03d41f1586d69913b22486',
                ])->get('https://api-v2.mycrm.com/properties/'.$property->my_crm_id);
                $res = $response->json();
                $value = $res['property'] ?? null;
                if($value){
                    echo "\n#";
                    if($property->availability_date){
                        Property::where('id', $property->id)->update([
                            "cheques" => $value["price"]['cheques'] ?? null,
                        ]);
                    }else{
                        Property::where('id', $property->id)->update([
                            "cheques" => $value["price"]['cheques'] ?? null,
                            "availability_date" => $value["available_from"]
                        ]);
                    }
                    
                }
                

            }
        }
        $time_end = microtime(true);
        $time = $time_end - $time_start;
        $time = gmdate("H:i:s", $time);

        info( "$time minutes it takes to update property cheques");

        return Command::SUCCESS;
    }
}
