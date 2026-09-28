<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admin\Lead\LeadController;
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
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Traits\PropertyFinderTrait;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Traits\FileTrait;

class PfV2FetchProcessLead extends Command
{
    use PropertyFinderTrait, FileTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pf:fetch-process-lead {--date=} {--reset : Reset pagination and start from page 1}';

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
                } else {
                    return null;
                }
            } else {

                return null;
            }
        }

        return null;
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

    private function processLead($payload)
    {

        $id = preg_replace('/\D/', '', $payload['id']);


        $mobile = '';
        $email = '';
        foreach ($payload['sender']['contacts'] ?? [] as $contact) {
            if (isset($contact['type']) && $contact['type'] === 'phone') {
                $mobile = $contact['value'];
            }
            if (isset($contact['type']) && $contact['type'] === 'email') {
                $email = $contact['value'];
            }
        }

        $channel = $payload['channel'] ?? '';


        if ($channel == 'call' || $channel == 'email' || $channel == 'whatsapp') {
        //if ($channel == 'email' || $channel == 'whatsapp') {
            $log = PFWebhookLog::where('webhook_id', $payload['id'])->first();
            $payloadFromLog = $log->payload['payload'] ?? [];
            $pf_listing_id = $payloadFromLog['listing']['id'] ?? '';
            $ref_no = $payloadFromLog['listing']['reference'] ?? '';
           if($pf_listing_id == '' || $pf_listing_id == null){
               $pf_listing_id = $payload['listing']['id'] ?? '';
               $ref_no = $payload['listing']['reference'] ?? '';
           }

            $agent_pf_id = $payload['publicProfile']['id'] ?? '';
            $status = $payload['status'] ?? '';
            $source = Source::where('name', 'Property Finder')->first();
            $medium_id = $this->getMediumId($channel);
            $property = $ref_no ? Property::where('ref_no', $ref_no)->first() : null;

            $user = User::where('third_party_ids->pf_user_id', $agent_pf_id)->first(); //pf_private_id currently using public profile id

            $this->info("Processing for Property Finder ID: {$id} channel is {$channel} and status is {$status}");

            //check if property finder id already exists in leads
            if (!Lead::whereJsonContains('portal_ids->property_finder_id', $id)->exists()) {

                $name = $payload['sender']['name'] ?? null;
                $name = $name ? trim($name) : null;
                $name = $payloadFromLog['sender']['name'] ?? '';
                $lead = new Lead();
                $lead->portal_ids = json_encode(['property_finder_id' => $id]);
                $lead->first_name = $name ?? '';
                $lead->mobile = $mobile ?? '';
                $lead->email = $email ?? '';
                $lead->whatsapp = $mobile ?? '';
                $lead->source_id = $source?->id ?? null;
                $lead->lead_medium_id = $medium_id ?? null;
                $lead->lead_status_id = $this->getLeadStatusId($status) ?? null;
                $lead->agent_id = $user?->id ?? null;
                $lead->property_id = $property?->id ?? null;
                $lead->pf_tracking_link = $payload['responseLink'] ?? null;
                $lead->created_at = date('Y-m-d H:i:s', strtotime($payload['createdAt']));
                if ($lead->save()) {

                    event(new \App\Events\LeadCreated($lead));

                    if (!empty($log)) {
                        if ($pf_listing_id != '' && $pf_listing_id != null) {
                            $property_ref = ($ref_no !='' && $ref_no != null) ? $ref_no : $this->getListingRefFromPf($pf_listing_id);
                            if ($property_ref) {
                                $property = Property::where('ref_no', $property_ref)->first();

                                if ($property) {
                                    $this->info("Associating Property ID: {$property->id} with Lead ID: {$lead->id}");
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
                            }
                        }
                        $log->is_processed = true;
                        $log->processed_at = now();
                        $log->status = 'processed';
                        $log->save();
                        $this->info("Processed log ID: {$log->id}");

                    }

                    if ($channel == 'call') {
                        $call_recording_url = $payload['call']['recordFile'] ?? null;
                        if ($call_recording_url) {
                            $this->info("Processing call recording for lead ID: {$lead->id} from PF ID: {$id}");
                            $path = $this->uploadAudioAsMp3FromUrl($call_recording_url, "lead");

                            $callLog = new CallLog();
                            $callLog->lead_id = $lead->id;
                            $callLog->call_recording_url = $path ?? null;
                            $callLog->call_type = 'RECORDING';
                            $callLog->call_started_at = date('Y-m-d H:i:s', strtotime($payload['createdAt']));
                            $callLog->call_ended_at = null;
                            $callLog->call_answered_at = null;
                            $callLog->call_duration = $payload['call']['talkTime'] ?? 0;
                            $callLog->call_status = $payload['call']['talkTime'] > 0 ? 'connected' : 'missed';
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
                    $note->note = "Lead created from PF V2 Lead fetch API. PF ID: {$id} <br>
Lead created at PF : " . date('Y-m-d H:i:s', strtotime($payload['createdAt'])) . "<br>
" . json_encode($payload);
                    $note->save();

                    //  AXurlShortner($payload['responseLink']); //create shortlink
                }
            }else{
                $this->info("Lead with Property Finder ID: {$id} already exists. Skipping creation.");

            }

            if($log) {
                $log->is_processed = true;
                $log->processed_at = now();
                $log->status = 'processed';
                $log->save();
            }
        }


    }


    public function handle()
    {
        $cacheKey = 'pf_fetch_lead_pagination';

        // Reset pagination if requested
        if ($this->option('reset')) {
            Cache::forget($cacheKey);
            $this->info('Pagination cache reset. Starting from page 1.');
        }

        // Get cached pagination data
        $paginationData = Cache::get($cacheKey, [
            'currentPage' => 1,
            'totalPages' => null,
            'processedCount' => 0,
            'isComplete' => false
        ]);

        // Check if sync is already complete
        if ($paginationData['isComplete']) {
            $this->info('Lead fetch already completed. Use --reset to start over.');
            return Command::SUCCESS;
        }

        $url = 'https://atlas.propertyfinder.com/v1/leads';
        $token = $this->getPfAccessTokenV2('secondary');
        if (!$token) {
            $this->error('Failed to get Property Finder token.');
            return Command::FAILURE;
        }

        $headers = [
            'Authorization' => 'Bearer ' . $token['token'],
            'Content-Type' => 'application/json',
        ];

        $currentPage = $paginationData['currentPage'];
        $this->info("Processing page {$currentPage}...");
        $date = $this->option('date') ?? null ;
        //if no date is provided, use the current date
        if (!$date) {
            $date = Carbon::now()->toDateString();
        } else {
            $date = Carbon::parse($date)->toDateString();
        }

       // use this format Carbon::parse('2025-04-30 09:54:18', 'Asia/Dubai')->toRfc3339String();
        $from = Carbon::parse($date)->startOfDay()->toRfc3339String();
       // $to = Carbon::parse($date)->endOfDay()->toRfc3339String();
        $payload = [
            'page' => $currentPage,
            'createdAtFrom' => $from,
        ];


        $response = Http::withHeaders($headers)->get($url, $payload);

        if ($response->failed()) {
            $this->error('Failed to fetch leads from Property Finder: ' . $response->body());
            return Command::FAILURE;
        }

        $data = $response->json();
        $pagination = $data['pagination'] ?? [];
        $totalPages = $pagination['totalPages'] ?? 1;

        // Update total pages on first run
        if ($paginationData['totalPages'] === null) {
            $paginationData['totalPages'] = $totalPages;
        }

        $this->info("Page {$currentPage} of {$totalPages}");

        if (empty($data['data'])) {
            $this->info('No leads found on this page.');
        } else {
            $pageProcessedCount = 0;
            foreach ($data['data'] ?? [] as $leadFromPf) {
                $leadFromPf = (array)$leadFromPf;
                $this->processLead($leadFromPf);
                //if $leadFromPf['createdAt] == 2025-07-25 then stop the loop and reset the pagination
                if( isset($leadFromPf['createdAt']) && Carbon::parse($leadFromPf['createdAt'])->toDateString() < $date) {
                    //reset pagination
                    $paginationData['currentPage'] = 1;
                    $paginationData['processedCount'] = 0;
                    $paginationData['isComplete'] = true;
                    $this->info("Reached the end of leads for date: {$date}. Stopping further processing.");
                    break;
                }


                $pageProcessedCount++;
            }
            $paginationData['processedCount'] += $pageProcessedCount;
            $this->info("Processed {$pageProcessedCount} leads on this page.");
        }

        // Update pagination for next run
        $paginationData['currentPage']++;

        // Check if we've reached the end
        if ($paginationData['currentPage'] > $totalPages) {
            $paginationData['isComplete'] = true;
            $this->info("Lead fetch completed! Total processed: {$paginationData['processedCount']} leads across {$totalPages} pages.");
            Cache::forget($cacheKey);
        } else {
            $this->info("Next run will process page {$paginationData['currentPage']}. Total processed so far: {$paginationData['processedCount']}");
            Cache::put($cacheKey, $paginationData, now()->addHours(24));
        }

        return Command::SUCCESS;
    }
}
