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

class ManagerCalendarReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, TelegramTrait;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $managers = User::whereNot('id',172)->whereHas("roles", function ($q) {
            $q->whereIn("name", ["Line Manager", "Team Leader", "Sales Director"]);
        })->where("status", true)->get();
        foreach ($managers as $manager) {
            $message = "<b>AX Secondary Calendar Activity Report</b>\n";
            $message .= "\n";
            $message .= "\n";
            $message .= "Good Evening <b>" . $manager->full_name . '</b>,' . "\n\n";

            $agent_ids = getUserChildrenIdsOnly($manager->id);

            $key = array_search($manager->id, $agent_ids);
            if ($key !== false) {
                array_splice($agent_ids, $key, 1);
            }
            $agents = User::whereNot('id',172)->where('status', true)->whereHas('roles', function ($query) {
                $query->where('name', 'Agent');
            })
            ->whereDoesntHave('events', function ($query) {
                $query->where('created_at', '>=', now()->subDays(7));
            })
            ->whereIn('id', $agent_ids)
            ->get();

            if($agents){
                $message .= "There have been <b>NO</b> recorded activities in the calendar by agents, including Viewings, Meetings, and Followups, for the past <b>7 DAYS</b> as follows:\n\n";
                foreach($agents as $agent){
                    $message.=$agent->full_name."\n";
                }

                $message .= "==========================\n";

                if ($manager->telegram_chatid) {
                    $this->sendTelegramMessageHtml($message, [$manager->telegram_chatid], null, null, 'notification_bot');
                }
            }
        }
    }
}
