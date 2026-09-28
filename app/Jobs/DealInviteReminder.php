<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Lead;
use App\Models\Deal;
use App\Models\User;
use App\Mail\DealLeadInviteKYCReminderMail;
use Mail;
use App\Traits\TelegramTrait;
class DealInviteReminder
{ //implements ShouldQueue
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, TelegramTrait;

    protected $invite;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($invite)
    {
        $this->invite = $invite;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        print_r($this->invite);
        $lead = Lead::where('id', $this->invite->lead_id)->first();
        $deal = Deal::where('id', $this->invite->deal_id)->first();
        Mail::to($lead->email)->send(new DealLeadInviteKYCReminderMail($this->invite, $lead, 'Lead Invite', 'Lead Invite'));
        $agent = User::where('id', $this->invite->invited_by_id)->first();
        if($deal->type ='Rental'){
            $client_type = 'Tenant';
        }else{
            $client_type = 'Buyer';
        }
        $message ="Hi ".$agent->name.",\n\n";
        $message.= $client_type.' has been reminded to complete the KYC for the Deal. Please follow up with the '.$client_type.'

Deal ID : '.$this->invite->deal_id.'
KYC Form Sent Date : '.globalDateTime($this->invite->created_at).'
        ';
        $this->sendTelegramMessageHtml($message, [$agent->telegram_chatid]);

        $this->invite->last_reminded = now();
        $this->invite->save();
    }
}
