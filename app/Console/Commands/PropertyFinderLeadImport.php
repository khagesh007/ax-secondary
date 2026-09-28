<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Lead;
use App\Models\User;
use App\Events\PortalsLeadsNotification;



class PropertyFinderLeadImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:name';

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
        try
        {
            $header = array();
            $header[] = 'Content-Type: application/json';
            $header[] = 'Content-Length: 4000';
            $header[] = 'Connection: keep-alive';

            $curl = curl_init();

            $todayDate = date('Y-m-d');

            curl_setopt_array($curl, array(
                  CURLOPT_URL => 'https://api-v2.mycrm.com/leads/?filters[created][from]='.$todayDate,
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'GET',
                  CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer '.$portal->token,
                  ),
            ));
            $res = curl_exec($curl);

            curl_close($curl);

            $res = json_decode($res, true);
            if (isset($res['error']) && $res['error'] == "AuthenticationError") {
                info("Property Finder Token Expired");
                return response()->json(['status' => '0', 'msg' => "token expired"]);
            }
            $data = $res['leads'] ?? null;

            if (!empty($data)) {

                foreach ($data as $key => $value) {
                    $leadMeta = LeadMeta::where('meta_key', 'mycrm_lead_id')->where('meta_value', $value['id'])->first();
                    if(empty($leadMeta)){

                        $lead = new Lead();
                        $lead->first_name = $value['first_name'];
                        $lead->last_name = $value['last_name'];
                        $lead->email = $value['email'];
                        $lead->mobile = $value['mobile'];
                        $lead->whatsapp = $value['mobile'];

                        $lead->source_id = 31; // Property Finder
                        $lead->lead_status_id = 1; // New
                       // $lead->lead_type_id = 'Buy';
                        $lead->lead_description = $value['message'];
                        $lead->lead_created_at = $value['created_at'];
                        $lead->lead_updated_at = $value['updated_at'];
                        $lead->save();



                    }
                    $lead = Lead::where('lead_id', $value['id'])->first();
                    if (empty($lead)) {
                        $lead = new Lead();
                    }
                    $lead->lead_id = $value['id'];
                    $lead->lead_name = $value['name'];
                    $lead->lead_email = $value['email'];
                    $lead->lead_phone = $value['phone'];
                    $lead->lead_source = 'Property Finder';
                    $lead->lead_status = 'New';
                    $lead->lead_type = 'Buy';
                    $lead->lead_description = $value['message'];
                    $lead->lead_created_at = $value['created_at'];
                    $lead->lead_updated_at = $value['updated_at'];
                    $lead->save();

                    $user = User::where('id', $property->agent_id)->first();
                    event(new PortalsLeadsNotification("Lead", $lead, 'received', $user, $user, 1));
                }
            }


        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
        return Command::SUCCESS;
    }
}
