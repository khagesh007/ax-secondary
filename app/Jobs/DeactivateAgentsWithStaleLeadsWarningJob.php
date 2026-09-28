<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeactivateAgentsWithStaleLeadsWarningJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
        {
            $agents = User::role('agent')->where('status_id', 1)->get();
            foreach ($agents as $agent) {
                $leads = Lead::where('agent_id', $agent->id)->where('status_id', 1)->whereDate('created_at', '!=', Carbon::today())->get();
                if ($leads->count() > 5) {
                    try {
                        DB::beginTransaction();
                        if ($agent->status == 1) {

                            $message = "You currently have 10 leads that were created in past days ( excluding today)  and are still in the 'New' status. Please take action on these leads within the next 60 minutes, or your account may be suspended.";
                            $url = route('login');
                            $this->sendTelegramMessageHtml($message, [$agent->telegram_chatid], null, 'Deactivate Agent', 'lead_bot');

                            $meta = new UserMeta();
                            $meta->user_id = $agent->id;
                            $meta->key = 'warning_payload';
                            $meta->value = json_encode([
                                'leads' => $leads->count(),
                                'manager' => $parent->full_name,
                                'manager_id' => $parent->id,
                            ]);
                            $meta->flag = 'warning_payload';
                            $meta->save();
                        }
                        DB::commit();
                    } catch (\Throwable $th) {
                        DB::rollBack();
                        info($th);
                        continue;
                    }
                }
            }
        }
    }
}
