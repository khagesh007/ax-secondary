<?php

namespace App\Console\Commands;
use App\Models\Property;
use Illuminate\Support\Facades\Http;
use Illuminate\Console\Command;
use App\Traits\FileTrait;
use App\Models\PropertyMedia;

class UploadPropertyMedia extends Command
{
    use FileTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upload:propertyImages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Command will upload property images to s3';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        info("Uploading Property Images started at" . now());
        $time_start = microtime(true);

        $properties = Property::where("my_crm_id", "3827089")->with('photos')->get();

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
                    $property->photos()->delete();
                    $images = $value['images'];

                    foreach ($images  as  $image) {
                        $orignal_image = $image['original']['link']?? null;
                        if($orignal_image){
                            $image_counter = 1;
                            $media = $this->uploadImageAndThumbWithWatermarkApi(file: $orignal_image, path: "listings");
                            if (!empty($media) && !isset($media['error'])) {
                                echo"\nOrignal Image downloaded for ". $property->my_crm_id;
                                $PropertyMedia = PropertyMedia::create([
                                            "property_id" =>  $property->id,
                                            "path" => $media["image"] ?? null,
                                            "thumbnail" => $media["thumbnail"] ?? null,
                                            "order" => $image['order']?? $image_counter,
                                            "type" => "Photos",
                                        ]);
                                $image_counter++;
                            }else{
                                echo"\nWatermark Image downloaded for ". $property->my_crm_id;
                                $orignal_image = $image['full']['link']?? null;
                                $media = $this->uploadImageAndThumbWithWatermarkApi(file: $orignal_image, path: "listings", watermark:"");
                                if (!empty($media) && !isset($media['error'])) {
                                    $PropertyMedia = PropertyMedia::create([
                                                "property_id" =>  $property->id,
                                                "path" => $media["image"] ?? null,
                                                "thumbnail" => $media["thumbnail"] ?? null,
                                                "order" => $image['order']?? $image_counter,
                                                "type" => "Photos",
                                            ]);
                                    $image_counter++;
                                }
                            }
                        }

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
