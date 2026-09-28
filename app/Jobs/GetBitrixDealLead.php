<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Controllers\Admin\Deals\BitrixDealController;
use App\Models\DealBitrixDump;
use App\Models\Lead;
use App\Models\Source;
class GetBitrixDealLead implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $deal_id;
    public $lead_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($deal_id, $lead_id)
    {
        $this->deal_id = $deal_id;
        $this->lead_id = $lead_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $deal = DealBitrixDump::find($this->deal_id);
        sleep(2);
        $lead = (new BitrixDealController)->getFullLeadData($this->lead_id);
        $lead_source = findSourceForBitrix($lead['SOURCE_ID']);

        $mobile = ''; $email = ''; $lead_source_matched = 0;
        if($lead['HAS_PHONE'] == 'Y'){
           $mobile = $lead['PHONE'][0]['VALUE'];
        }
        if($lead['HAS_EMAIL'] == 'Y'){
            $email = $lead['EMAIL'][0]['VALUE'];
        }
        // $ax_lead = Lead::where('mobile',$mobile)
        // ->when($deal->agent_id, function ($query) use ($deal) {
        //     return $query->where('agent_id', $deal->agent_id);
        // })->where(function($q) use ($email,$mobile){
        //     $q->where('mobile',$mobile)->orWhere('email',$email);
        // })->first();
        $ax_lead = Lead::query()
                    ->when($deal->agent_id, function ($query) use ($deal) {
                        return $query->where('agent_id', $deal->agent_id);
                    })
                    ->when($mobile, function ($query) use ($mobile) {
                        // Check for exact match or partial match using LIKE
                        return $query->where(function ($q) use ($mobile) {
                            $q->where('mobile', $mobile)
                            ->orWhere('mobile', 'like', "%{$mobile}%");
                        });
                    })
                    ->when($email, function ($query) use ($email) {
                        // Check for exact match or partial match using LIKE
                        return $query->where(function ($q) use ($email) {
                            $q->where('email', $email)
                            ->orWhere('email', 'like', "%{$email}%");
                        });
                    })
                    ->first();



        if($ax_lead != null){

        $source = Source::where('id',$ax_lead->source_id)->first();
            if($source->name == $lead_source){
                $lead_source_matched = 1;
            }elseif($source->name == $lead['SOURCE_ID']){
                $lead_source_matched = 1;
            }else{
                $lead_source_matched = 0;
            }
        }
         $deal->deal_leads = $lead;
         $deal->lead_source_matched = $lead_source_matched;
         $deal->save();
    }
}
