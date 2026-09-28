<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Contact;
use Illuminate\Support\Facades\Http;

class AssignAgentsToLandlords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assign:agents';

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
        info("Assigning Agents to Landlords at" . now());
        $time_start = microtime(true);

        $landlords = Contact::whereNull("agent_id")->get();
        $header  = array();
        $header[] = 'Content-Type: application/json';
        $header[] = 'Content-Length: 4000';
        $header[] = 'Connection: keep-alive';
        if($landlords){

            foreach($landlords as $landlord){

                echo"\nChecking for landlord id ". $landlord->id;
                echo"\nChecking for landlord my_crm_id ". $landlord->my_crm_id;
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . '150852b2b677aac3bc03d41f1586d69913b22486',
                ])->get('https://api-v2.mycrm.com/landlords/'.$landlord->my_crm_id);
                $res = $response->json();
                $value = $res['landlord']??[];
                
                if (!empty($value['user'])) {
                    $agent        = User::where('old_user_id', $value['user']['id'])->pluck('id');
                    if($agent){
                        echo "\n#";
                        Contact::where('id', $landlord->id)->update([
                            "agent_id" => $agent[0]?? null,
                        ]);

                    }
                    
                }

            }
        }
        $time_end = microtime(true);
        $time = $time_end - $time_start;
        $time = gmdate("H:i:s", $time);

        info( "$time minutes it takes to assign agents to landlords");
        return Command::SUCCESS;
    }
}
