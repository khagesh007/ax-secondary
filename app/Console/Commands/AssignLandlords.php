<?php

namespace App\Console\Commands;
use App\Models\Property;
use App\Models\Contact;
use Illuminate\Support\Facades\Http;
use Illuminate\Console\Command;

class AssignLandlords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assign:landlords';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will assign landlords to existing properties based on the mycrm old id';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        info("Assigning Property Landlords at" . now());
        $time_start = microtime(true);

        $properties = Property::where('id', ">", 2802)->where("publish_status", "PUBLISHED")->whereNull("owner_id")->get();
        $header  = array();
        $header[] = 'Content-Type: application/json';
        $header[] = 'Content-Length: 4000';
        $header[] = 'Connection: keep-alive';
        if($properties){

            foreach($properties as $property){

                
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . '150852b2b677aac3bc03d41f1586d69913b22486',
                ])->get('https://api-v2.mycrm.com/properties/'.$property->my_crm_id);
                $res = $response->json();
                $value = $res['property'];
                echo"\nChecking for property id". $property->id;
                if (!empty($value['landlord'])) {
                    $owner        = Contact::where('my_crm_id', $value['landlord']['id'])->pluck('id');
                    if($owner){
                        echo "\n#";
                        Property::where('id', $property->id)->update([
                            "owner_id" => $owner[0]?? null,
                        ]);

                    }
                    
                }

            }
        }
        $time_end = microtime(true);
        $time = $time_end - $time_start;
        $time = gmdate("H:i:s", $time);

        info( "$time minutes it takes to upload all the images");

        return Command::SUCCESS;
    }
}
