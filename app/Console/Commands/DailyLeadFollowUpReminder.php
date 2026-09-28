<?php

namespace App\Console\Commands;

use App\Mail\LeadFollowUpReminderMail;
use App\Models\Lead;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class DailyLeadFollowUpReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lead_follow_up_reminder:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lead follow up reminder to agent';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $leads = Lead::where("follow_up_date", date("Y-m-d"))->with("agent")->get();

        foreach ($leads as $lead){
            if (!is_null($lead->agent)){
                Mail::to($lead->agent)->send(new LeadFollowUpReminderMail($lead));
            }
        }
    }
}
