<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Contact;
use Illuminate\Support\Facades\Http;
class AddLandlords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:landlords';

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
        $per_page = 20;
        info("Importing Landlords from mycrm started at " . now());
        $salutation = array("mr"=> "Mr.", "ms"=> "Ms.", "mrs"=> "Mrs.", "dr"=> "Dr.");
        $header  = array();
        $header[] = 'Content-Type: application/json';
        $header[] = 'Content-Length: 4000';
        $header[] = 'Connection: keep-alive';
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . '150852b2b677aac3bc03d41f1586d69913b22486',
        ])->get('https://api-v2.mycrm.com/landlords?page=1&per_page=1');
        $res = $response->json();
        $landlords = $res['landlords'];
        $total_landlords_count = $res['count'];
        $total_pages = round(ceil($total_landlords_count / $per_page), 0);
        for($page; $page <= $total_pages; $page++){
            $response = Http::withHeaders(['Authorization' => 'Bearer ' . '150852b2b677aac3bc03d41f1586d69913b22486',])->get('https://api-v2.mycrm.com/landlords?page='.$page.'&per_page='.$per_page);
            $res = $response->json();
            $landlords = $res['landlords'];

            if($landlords){
                info("Page number is $page");
                foreach($landlords as $value){
                    echo "\n#";
                    
                    $landlord        = Contact::where('my_crm_id', $value["id"])->first();
                    $newLandlord = false;
                    if(!$landlord){
                        $landlord = new Contact;
                        $newLandlord = true;
                        echo("\nid of the Landlord adding is ". $value['id']);
                        info("\nid of the Landlord adding is ". $value['id']);
                        $totalCount ++;
                    }else{
                        echo("\nid of the Landlord updating is ". $value['id']);
                        info("\nid of the Landlord updating is ". $value['id']);
                    }
                    if (!empty($value)) {
                        $agent = [];
                        if(isset($value['user']['id'])){
                            $agent        = User::where('old_user_id', $value['user']['id'])->first()?? null;
                        }
                        
                        //dd($agent->id);
                       
                        $landlord->my_crm_id    = $value['id'];
                        $landlord->agent_id     = $agent->id ?? null;
                        $landlord->title        = $salutation[$value['title']]?? null;
                        $landlord->first_name   = $value['first_name']?? null;
                        $landlord->last_name    = $value['last_name']?? null;
                        $landlord->email        = $value['email']?? null;
                        $landlord->mobile       = $value['mobile']?? null;

                        $landlord->save();
                    }
                }
            }
        }
        
        $time_end = microtime(true);
        $time = $time_end - $time_start;

        info( "$time seconds is the time it takes to finish this task");
        echo("\n $totalCount landlords are imported successfully");
        return Command::SUCCESS;
    }
}
