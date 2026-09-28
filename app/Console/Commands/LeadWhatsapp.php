<?php

namespace App\Console\Commands;

use App\Models\Lead;
use App\Models\LeadNote;
use App\Models\Medium;
use App\Models\Property;
use App\Models\Source;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class LeadWhatsapp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lead:whatsapp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import lead from whatsapp api';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $response = Http::get('http://127.0.0.1:8001/whatsapp.json');
            $response = json_decode($response->body());
            foreach ($response as $item) {
                $mobile = $item?->data?->customer?->channel_phone_number;
                $lead = Lead::where('mobile', $mobile)->first();
                if ($lead) {
                    $lead->mobile = $mobile;
                    $lead->first_name = $item?->data?->customer?->traits?->name;
                    $lead->lead_status_id = '3'; // 3 unspecified
                    if ($lead->save()) {
                        $notes = 'Imported from Whatsapp Api <br>
                        Name : ' . $item?->data?->customer?->traits?->name . '<br>
                        Phone : ' . $item?->data?->customer?->channel_phone_number . '<br>
                        Whatsapp Opted In : ' . $item?->data?->customer?->traits?->whatsapp_opted_in . '<br>
                        Message Type : ' . $item?->type . '<br>
                        Received At : ' . ($item?->data?->message?->received_at_utc != '' ? date('d/m/y H:i', strtotime($item?->data?->message?->received_at_utc)) : '') . '<br>
                        Delivered At : ' . ($item?->data?->message?->delivered_at_utc != '' ? date('d/m/y H:i', strtotime($item?->data?->message?->delivered_at_utc)) : '') . '<br>
                        Seen At : ' . ($item?->data?->message?->seen_at_utc != '' ? date('d/m/y H:i', strtotime($item?->data?->message?->seen_at_utc)) : '') . '<br>
                        Message : ' . $item?->data?->message?->message;

                        $leadNote = new LeadNote();
                        $leadNote->lead_id = $lead->id;
                        $leadNote->note = $notes;
                        $leadNote->save();
                    }
                } else {
                    $ref_no = getBetweenText($item?->data?->message?->message, "ref:", "source");
                    $source = getBetweenText($item?->data?->message?->message, "advert on", ",");
                    $property = Property::where('ref_no', $ref_no)->first();
                    $lead = new Lead();
                    $lead->mobile = $mobile;
                    $lead->whatsapp = $mobile;
                    $lead->agent_id = $property->agent_id ?? null;
                    $lead->property_id = $property->id ?? null;
                    $lead->first_name = $item?->data?->customer?->traits?->name;
                    $lead->lead_status_id = '3'; // 3 unspecified
                    $lead->source_id = (Source::whereRaw('UPPER(name) LIKE ?', ['%' . strtoupper($source) . '%'])->where('is_lead_source', '1')->first()->id ?? Source::where('name', 'Whatsapp')->where('is_lead_source', '1')->first()->id) ?? null;
                    $lead->lead_medium_id = Medium::where('name', 'Whatsapp')->where('is_lead_medium', '1')->first()->id ?? null;
                    if ($lead->save()) {

                        $notes = 'Imported from Whatsapp Api <br>
                        Name : ' . $item?->data?->customer?->traits?->name . '<br>
                        Phone : ' . $item?->data?->customer?->channel_phone_number . '<br>
                        Whatsapp Opted In : ' . $item?->data?->customer?->traits?->whatsapp_opted_in . '<br>
                        Message Type : ' . $item?->type . '<br>
                        Received At : ' . ($item?->data?->message?->received_at_utc != '' ? date('d/m/y H:i', strtotime($item?->data?->message?->received_at_utc)) : '') . '<br>
                        Delivered At : ' . ($item?->data?->message?->delivered_at_utc != '' ? date('d/m/y H:i', strtotime($item?->data?->message?->delivered_at_utc)) : '') . '<br>
                        Seen At : ' . ($item?->data?->message?->seen_at_utc != '' ? date('d/m/y H:i', strtotime($item?->data?->message?->seen_at_utc)) : '') . '<br>
                        Message : ' . $item?->data?->message?->message;

                        $leadNote = new LeadNote();
                        $leadNote->lead_id = $lead->id;
                        $leadNote->note = $notes;
                        $leadNote->save();
                        if ($property->agent_id) {
                            $user = User::where('id', $property->agent_id)->first();
                            if ($user) {
                                event(new PortalsLeadsNotification("Lead", $lead, 'received', $user, $user, 1));
                            }
                        }

                    }
                }
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            \Log::error(['error' => $e->getMessage(), 'line' => $e->getLine(), 'file' => 'App\Console\Commands\LeadWhatsapp']);
            return Command::FAILURE;
        }
    }
}
