<?php

namespace App\Console\Commands;
use App\Models\Property;
use Illuminate\Support\Facades\Http;
use Illuminate\Console\Command;
use App\Traits\FileTrait;
use App\Models\PropertyMedia;

class AddPropertyDocuments extends Command
{
    use FileTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:PropertyDocuments';

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
        info("Adding Property Documents" . now());
        $time_start = microtime(true);
        $header  = array();
        $header[] = 'Content-Type: application/json';
        $header[] = 'Content-Length: 4000';
        $header[] = 'Connection: keep-alive';
        $properties = Property::all();
 
        if($properties){

            foreach($properties as $property){
                echo"\n checking for property id ".$property->id;
                echo"\n checking for my_crm_id id ".$property->my_crm_id;
                $response = Http::withHeaders(['Authorization' => 'Bearer ' . '150852b2b677aac3bc03d41f1586d69913b22486',])->get('https://api-v2.mycrm.com/properties/'.$property->my_crm_id.'/documents?page=1&per_page=100');
                $res = $response->json();
                $documents = $res['documents']??[];
                foreach($documents as $document){
                    echo"\n#";
                    $document_type = array("Title Deed" => "Title Deed", "Form A" => "Form A", "Passport"=>"Owner Passport Copy");
                    $media = PropertyMedia::create([
                        "property_id" =>  $property->id,
                        "path" => $document['file']['link'] ?? null,
                        "type" => "Other",
                        "title" => $document_type[$document['tag']]?? $document['tag'],
                        "temp_image" => 1,
                    ]);
                }

            }
        }
        $time_end = microtime(true);
        $time = $time_end - $time_start;
        $time = gmdate("H:i:s", $time);

        info( "$time minutes it takes to add all images");
        return Command::SUCCESS;
    }
}
