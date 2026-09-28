<?php

namespace App\Jobs;

use App\Models\User;
use App\Traits\TelegramTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendDealMsgToExternalAgent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, TelegramTrait;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public $deal)
    {
        $this->deal = $deal;
    }

    protected function sendTelegramMsg($deal)
    {
        if($deal->type == 'Secondary Sale' || $deal->type == 'Primary Resale'){
            $lead_type = 'Buyer';
        }else{
            $lead_type = 'Tenant';
        }
        $property = $deal->property;
        $user = User::where('id',$deal->external_agent_id)->first();
        $chatId = '795165330';//$user->telegram_chat_id;
        $msg = "Hi, $user->first_name $user->last_name,\n\n";
        $msg.="You have been assigned a new deal. Ref No:# $deal->id. Please fill all the required details related to the $lead_type.";
        $msg.="\n\n<b>Deal Details:</b> \n";
        $msg.="<b>Deal Type:</b> $deal->type\n";
        $msg.="<b>Listing:</b> ".$deal->property->ref_no."\n";
        $msg.="<b>Property Type:</b>  $property?->category
<b>Agent:</b> " . $deal?->agent?->first_name . " " . $deal?->agent?->last_name . "
\n";
        $msg.="Please check your dashboard for more details.";
        $msg.="\n\nAX Secondary CRM
==========================";

        $this->sendTelegramMessageHtml($msg, [$chatId]);
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //print_r($this->deal);

        $this->sendTelegramMsg($this->deal);
    }
}
