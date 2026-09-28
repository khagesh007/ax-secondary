<?php

namespace App\Console\Commands;

use App\Models\CallLog;
use App\Models\Lead;
use App\Models\LeadNote;
use App\Models\LeadStatus;
use App\Models\Medium;
use App\Models\PFWebhookLog;
use App\Models\Property;
use App\Models\Source;
use App\Models\Type;
use App\Models\User;
use Illuminate\Console\Command;
use App\Traits\PropertyFinderTrait;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Admin\Lead\LeadController;
use App\Traits\FileTrait;
class PfProcessWebhookLead extends Command
{
    use PropertyFinderTrait, FileTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pf:process-webhook-lead {--date=}';

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

    private function getMediumId($channel)
    {
        if ($channel === 'whatsapp') {
            $medium = Medium::where('name', 'WhatsApp')->first();
        } elseif ($channel === 'email') {
            $medium = Medium::where('name', 'Email')->first();
        } elseif ($channel === 'call') {
            $medium = Medium::where('name', 'Call')->first();
        }
        if (!$medium) {
            return null;
        } else {
            return $medium->id;
        }
    }

    private function getLeadStatusId($status)
    {
        if ($status === 'new') {
            $leadStatus = LeadStatus::where('name', 'New')->first();
        } elseif ($status === 'replied') {
            $leadStatus = LeadStatus::where('name', 'In Progress')->first();
        } else {
            $leadStatus = LeadStatus::where('name', 'New')->first();
        }
        if (!$leadStatus) {
            return null;
        } else {
            return $leadStatus->id;
        }
    }
    private function getListingRefFromPf($pf_listing_id)
    {

        $property = Property::whereRaw("JSON_UNQUOTE(JSON_EXTRACT(portal_ids, '$.property_finder_id')) = ?", [$pf_listing_id])
            ->first();
        if ($property) {
            $this->info("Property Finder Listing ID: {$pf_listing_id} found in local database. Reference: {$property->ref_no}");
            return $property->ref_no;
        }

        $url = "https://atlas.propertyfinder.com/v1/listings"; //?filter[ids]=".$pf_listing_id
        $tokens = $this->getPfAccessToken();
        $headers = [
            'Authorization' => 'Bearer ' . $tokens['token'],
            'Content-Type' => 'application/json',
        ];
        $params = [
            'filter[ids]' => $pf_listing_id
        ];
        $response = Http::withHeaders($headers)->get($url, $params);
        if ($response->successful()) {
            $data = $response->json();
            if (isset($data['results']) && is_array($data['results']) && count($data['results']) > 0) {
                $property = $data['results'][0];
                if ($property['id'] == $pf_listing_id) {
                    $this->info("Property Finder Listing ID: {$pf_listing_id} found. Reference: {$property['reference']}");
                    return $property['reference'] ?? null;
                }else{
                    return null;
                }
            }else{

                return null;
            }
        }

        return null;
    }


    public function handle()
    {
        $date = $this->option('date');
        if ($date) {
            $this->info("Processing logs for date: {$date}");
            $date = \Carbon\Carbon::parse($date)->toDateString();
            if (!$date) {
                $this->error("Invalid date format. Please use 'YYYY-MM-DD'.");
                return Command::FAILURE;
            }

        } else {
            $this->info("Processing logs for today: " . now()->toDateString());
            $date = date('Y-m-d'); // Default to today's date if no date is provided
        }

        $logs = PFWebhookLog::where('is_processed', false)
          //  ->where('type', 'lead.created')
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->whereDate('created_at', $date) // Filter logs by the specified date
            ->get();

        foreach ($logs as $log) {

            $payload = $log->payload;
            $payload = is_array($payload) ? $payload['payload'] : json_decode($payload, true);
            $channel = $payload['channel'] ?? '';


        if($channel == 'call' || $channel == 'email' || $channel == 'whatsapp') {

            $id = preg_replace('/\D/', '', $log->payload['id']);
            $payload = $log->payload['payload'];
            $mobile = ''; $email = '';
            foreach ($payload['sender']['contacts'] ?? [] as $contact) {
                if (isset($contact['type']) && $contact['type'] === 'phone') {
                    $mobile = $contact['value'];
                }
                if (isset($contact['type']) && $contact['type'] === 'email') {
                    $email = $contact['value'];
                }
            }
            $pf_listing_id = $payload['listing']['id'] ?? '';
            $property_ref = $payload['listing']['reference'] ?? '';
            $agent_pf_id = $payload['publicProfile']['id'] ?? '';
            $status = $payload['status'] ?? '';
            $source = Source::where('name', 'Property Finder')->first();
            $medium_id = $this->getMediumId($channel);
            $property = null;
            $property_ref = $property_ref !='' ? $property_ref : $this->getListingRefFromPf($pf_listing_id);
            if ($property_ref) {
                $property = Property::where('ref_no', $property_ref)->first();
            }
            $user = User::where('third_party_ids->pf_user_id', $agent_pf_id)->first(); //pf_private_id currently using public profile id


            $this->info("Processing log ID: {$log->id} for Property Finder ID: {$id} channel is {$channel} and status is {$status}");

            //check if property finder id already exists in leads

                //check if lead already exists with same mobile number & source_id & medium_id
//                if (Lead::where('mobile', $mobile)
//                    ->where('source_id', $source?->id ?? null)
//                    ->where('lead_medium_id', $medium_id ?? null)
//                    ->exists()) {
//                    $this->info("Lead with mobile {$mobile} already exists. Skipping lead creation.");
//                } else {
                    $lead = Lead::whereJsonContains('portal_ids->property_finder_id', $id)->first();
                    $lead_entry_type = $lead ? 'Existing' : 'New';
                    if(!$lead){
                        $lead = new Lead();
                    }
                    $lead->portal_ids = json_encode(['property_finder_id' => $id]);
                    $lead->first_name = $payload['sender']['name'] ?? '';
                    $lead->mobile = $mobile ?? '';
                    $lead->email = $email ?? '';
                    $lead->whatsapp = $mobile ?? '';
                    $lead->source_id = $source?->id ?? null;
                    $lead->lead_medium_id = $medium_id ?? null;
                    $lead->lead_status_id = $this->getLeadStatusId($status) ?? null;
                    $lead->agent_id = $user?->id ?? null;
                    $lead->property_id = $property?->id ?? null;
                    $lead->pf_tracking_link = $payload['responseLink'] ?? null;
                    $lead->created_at = isset($payload['timestamp']) ? date('Y-m-d H:i:s', strtotime($payload['timestamp'])) : now();
                    if ($lead->save()) {
                        // $this->info("Lead created with ID: {$lead->id} for Property Finder ID: {$id}");
                        if ($lead_entry_type == 'New'){
                            event(new \App\Events\LeadCreated($lead));
                    }
                        if ($channel == 'call') {
                            $call_recording_url = $payload['call']['recordFile'] ?? null;
                            if ($call_recording_url) {
                                $path = $this->uploadAudioAsMp3FromUrl($call_recording_url, "lead");

                                $callLog = new CallLog();
                                $callLog->lead_id = $lead->id;
                                $callLog->call_recording_url = $path ?? null;
                                $callLog->call_type = 'RECORDING';
                                $callLog->call_started_at = date('Y-m-d H:i:s', strtotime($payload['timestamp']));
                                $callLog->call_ended_at = null;
                                $callLog->call_answered_at = null;
                                $callLog->call_duration = $payload['call']['talkTime'] ?? 0;
                                if ($callLog->save()) {
                                    $callLog->leads()->attach($lead->id);
                                }
                            }
                        }

                        if ($property) {

                            if ($property->for == 'Commercial for rent' || $property->for == 'Residential for rent') {
                                $type = Type::where('name', 'Tenant')->first();
                            } else if ($property->for == 'Commercial for sale' || $property->for == 'Residential for sale') {

                                $type = Type::where('name', 'Buyer')->first();
                            }
                            else {
                                $type = Type::where('name', 'Unspecified')->first();
                            }
                            $lead->type_id = $type?->id ?? null;
                            $lead->save();


                            $this->info("Storing lead criteria for lead ID: {$lead->id} based on property ID: {$property->ref_no}");
                            $price = $property->price?->price ?? null;
                            $min_price = $price - ($price * 0.1);
                            $max_price = $price + ($price * 0.1);
                            $arr = [];
                            $arr['lead_id'] = $lead->id;
                            $arr['communities'] = [$property?->community_id];
                            $arr['sub_communities'] = [$property?->sub_community_id];
                            $arr['furnished'] = $property?->furnished ?? null;
                            $arr['view'] = json_decode($property?->view) ?? null;
                            $arr['min_bed_id'] = $property?->bed_id ?? null;
                            $arr['max_bed_id'] = $property?->bed_id ?? null;
                            $arr['min_bath_id'] = $property?->bath_id ?? null;
                            $arr['max_bath_id'] = $property?->bath_id ?? null;
                            $arr['min_price'] = $min_price ?? null;
                            $arr['max_price'] = $max_price ?? null;
                            (new LeadController())->storeCriteria($arr); //using controller method to store lead criteria
                        }


                        $note = new LeadNote();
                        $note->lead_id = $lead->id;
                        $note->note = "Lead created at  via Property Finder Webhook. PF ID: {$id} <br> " . json_encode($log->payload);
                        $note->save();

                        //  AXurlShortner($payload['responseLink']); //create shortlink
                    //}
                }


            $log->is_processed = true;
            $log->processed_at = now();
            $log->status = 'processed';
            $log->save();

            $this->info("Processed log ID: {$log->id}");
        }



        }

        return Command::SUCCESS;
    }
}
