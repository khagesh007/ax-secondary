<?php

namespace App\Console\Commands;

use App\Models\Lead;
use App\Models\User;
use App\Models\LeadNote;
use App\Traits\TelegramTrait;


use Illuminate\Support\Carbon;
use Telegram\Bot\Laravel\Facades\Telegram;

use Illuminate\Console\Command;

class LeadChangeReportGenerator extends Command
{
    use TelegramTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agent:LeadChangeReportGenerator';

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
        $currentTime = new \DateTime();
        $currentTime->setTimezone(new \DateTimeZone('Asia/Dubai')); // Replace with your desired timezone
        // Assigning the lead from here
        // Check if it's before 1:00 PM (13:00)
        if ($currentTime->format('H') < '10' || $currentTime->format('H') > '21') {
            // Perform your command logic here
            echo"Cron Ran before 10 am\n";
            return("Cron ran before 10 am");
            return Command::SUCCESS;
            // Your main command logic goes here
        }

        $usersReAssignLeads = User::whereHas('roles',function($q){
            $q->where('name','Agent');
            })->whereHas('leads', function ($query){
                // Filter leads with a new status
                $query->where('lead_status_id', 1)
                      ->whereNull('converted_to_listing_lead')
                      ->where('status', 1)
                      ->where('warning_sent', true)
                      ->where('is_archive', false)
                      ->whereRaw("TIMESTAMPDIFF(HOUR, assigned_agent_at, NOW()) > 6")
                      ;
            })->where('status', true)->get();
        $parents = [];
        foreach($usersReAssignLeads as $user){
            $newLeads = Lead::where('lead_status_id', 1)
                    ->where('agent_id', $user->id)
                    ->whereNull('converted_to_listing_lead')
                    ->where('status', 1)
                    ->where('is_archive', false)
                    ->where('warning_sent', true)
                    ->whereRaw("TIMESTAMPDIFF(HOUR, assigned_agent_at, NOW()) > 6")
                    ->get();

            $countOfLeads = $newLeads->count();
            if($newLeads){
                $message = "<b>AX Secondary CRM</b>\n\n";
                $message .= "Hi {$user->full_name},\n\n";
                $message .= "It has been more than 6 hours, and you haven't updated the status of your Newly received <b> {$countOfLeads} Lead(s)</b>.\n\n";
                $message .= "Kindly be informed that these leads have now been taken away from you. If you require further clarification, please reach out to your manager.\n";
                $message .= "==========================\n";
                if($user->telegram_chatid){
                    $this->sendTelegramMessageHtml($message, [$user->telegram_chatid], null, null, 'lead_bot');
                    $this->sendTelegramMessageHtml($message, [6085218419], null, null, 'lead_bot');
                }
                $parent = User::find($user->parent_id);
                $parents[$user->parent_id]['agents'][$user->full_name] = $countOfLeads;

                foreach($newLeads as $lead){
                    $lead->previous_agent_id = $lead->agent_id;
                    $lead->agent_id = $user->parent_id;
                    $lead->warning_sent = false;

                    $note = "The lead has been reassigned to {$parent->full_name} from {$user->full_name} because there has been no action taken by the agent in the past 6 hours since the lead was created({$lead->assigned_agent_at})";

                    LeadNote::create([
                                "lead_id" => $lead->id,
                                "note" => $note,
                                'status' => 1
                            ]);
                    $lead->save();
                }
                  
            }
        }
        if($parents){
            foreach($parents as $parent_id => $user){
                $parent = User::find($parent_id);
                $message = "<b>AX Secondary CRM</b>\n\n";
                $message .= "Hi {$parent->full_name},\n\n";
                $message .= "Following leads have been reassigned to you due to no action taken by the agent in the past 6+ hours since the leads were created:\n\n";
                foreach($user['agents'] as $agent_name => $leadCount){
                    $message.="{$agent_name} : {$leadCount}\n";
                }
                $message .= "==========================\n";
    
                if($parent->telegram_chatid){
                    $this->sendTelegramMessageHtml($message, [$parent->telegram_chatid], null, null, 'lead_bot');
                    $this->sendTelegramMessageHtml($message, [6085218419], null, null, 'lead_bot');
                }
            }
        }
        // Sending Warning from here
        $usersWithLeads = User::whereHas('roles',function($q){
            $q->where('name','Agent');
        })->whereHas('leads', function ($query) {
            // Filter leads with a new status
            $query->where('lead_status_id', 1)
                  ->whereNull('converted_to_listing_lead')
                  ->where('status', 1)
                  ->where('warning_sent', false)
                  ->where('is_archive', false)
                  ->whereRaw("TIMESTAMPDIFF(HOUR, assigned_agent_at, NOW()) >= 5")
                  ;
        })->where('status', true)->get();

        foreach ($usersWithLeads as $user) {
            $newLeads = Lead::where('lead_status_id', 1)
                    ->where('agent_id', $user->id)
                    ->whereNull('converted_to_listing_lead')
                    ->where('status', 1)
                    ->where('is_archive', false)
                    ->where('warning_sent', false)
                    ->whereRaw("TIMESTAMPDIFF(HOUR, assigned_agent_at, NOW()) >= 5")
                    ->count();

            if($newLeads){
                $message = "<b>AX Secondary CRM</b>\n\n";
                $message .= "Hi {$user->full_name},\n\n";
                $message .= "It has been 5 hours, and you haven't updated the status of your Newly received<b> {$newLeads} Lead(s)</b>. Please be aware that your leads will be taken away from you within the next <b>60 minutes</b> if the status remains unchanged.\n";
                $message .= "==========================\n";
                $updatedLeads = Lead::where('lead_status_id', 1)
                    ->where('agent_id', $user->id)
                    ->whereNull('converted_to_listing_lead')
                    ->where('status', 1)
                    ->where('is_archive', false)
                    ->where('warning_sent', false)
                    ->whereRaw("TIMESTAMPDIFF(HOUR, assigned_agent_at, NOW()) >= 5")
                    ->update(['warning_sent' => true]);

                if($user->telegram_chatid){
                    $this->sendTelegramMessageHtml($message, [$user->telegram_chatid], null, null, 'lead_bot');
                    $this->sendTelegramMessageHtml($message, [6085218419], null, null, 'lead_bot');
                }
            }
        }
        echo"Command Executed \n";
        return Command::SUCCESS;
    }
}
