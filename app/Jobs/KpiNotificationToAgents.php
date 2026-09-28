<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Traits\TelegramTrait;
class KpiNotificationToAgents implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, TelegramTrait;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $user;
    public $data;
    public function __construct($user, $data)
    {
        $this->user = $user;
        $this->data = $data;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $name = $this->user->name;
            $sales_target = $this->data['sales_target'];
            $new_listings = $this->data['new_listings'];
            $overall_listings = $this->data['overall_listings'];
            $duration = $this->data['duration'];
            $month_name = $this->data['month_name'];
            $message = "<b>KPI Target Notification</b>

Hi $name,

A new KPI target for the month of $month_name has been set for you. Please log in to the Secondary CRM to view the full details.

In brief, your targets for $duration are as follows:

<b>Sales Target:</b> AED $sales_target
<b>Number of Listings (Overall Live):</b> $overall_listings
<b>Number of Listings (New):</b> $new_listings


Have a nice day!";
            $this->sendTelegramMessageHtml($message, [$this->user->telegram_chatid]);
        }catch (\Exception $e){

           info('Error in KpiNotificationToAgents Job: '.$e->getMessage());
        }

    }
}
