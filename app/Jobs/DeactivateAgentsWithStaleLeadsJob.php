<?php

namespace App\Jobs;

use App\Models\Lead;
use App\Models\NotificationEvent;
use App\Models\User;
use App\Models\UserMeta;
use App\Traits\TelegramTrait;
use Carbon\Carbon;
use DB;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DeactivateAgentsWithStaleLeadsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use TelegramTrait;
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

        $currentDay = date('N'); // (1 = Monday, 7 = Sunday)

        if ($currentDay == 7) {
            return true;
        }
//-----------------------------------------------------------------------------------------
//----------------------------------IDs of Chinese Team------------------------------------
//-----------------------------------------------------------------------------------------

        $chineseTeam = array(
            333,
            4,
            319,
            36,
            205,
            21,
            38,
            58,
            40,
            39,
            446,
            64,
            167,
            634,
            660,
            286,
            105,
            213,
            238,
            268,
            32,
            275,
            1024,
        );

        $agents = User::whereNotIn('id', $chineseTeam)
            ->whereHas("roles", function ($q) {
                $q->whereIn("name", ["Agent"]);
            })->where("status", true)->get();

        $warnings = 0;
        $suspentions = 0;
        $exclusions = 0;
        $exclusions_users = [];
        $counter = 0;
        $users = [];
        $notification_event = NotificationEvent::where('class_name', 'StaleLeadAgentNotification')->where('status', true)->first();

        foreach ($agents as $agent) {

            $leads = Lead::where('agent_id', $agent->id)->where('is_archive', 0)->whereNull('converted_to_listing_lead')->where('lead_status_id', 1)->where('status', 1)->whereDate('created_at', '!=', Carbon::today())->get();
            $parent = User::where('id', $agent->parent_id)->first();
            if ($leads->count() > 4) {
//-----------------------------------------------------------------------------------------
//---------------------If this user is on Leave or Vocation--------------------------------
//-----------------------------------------------------------------------------------------

                try {
                    $event = $agent->events()->whereIn('event_type_id', [11, 12])->whereDate('start_time', Carbon::today())->whereDate('end_time', Carbon::today())->first();

                    if ($event) {
                        $exclusions++;
                        $exclusions_users[] = [
                            'id' => $agent->id,
                            'name' => $agent->full_name,
                            'leads' => $leads->count(),
                            'manager' => $parent->full_name,
                            'manager_id' => $parent->id,
                            'reason' => 'Event',
                        ];
                        continue;
                    }

                } catch (\Throwable $th) {
                    Log::channel('discord')->info('Error while checking events', [
                        'Time' => Carbon::now()->toDateTimeString(),
                        'Agent' => $agent->id,
                        'Error' => $th->getMessage(),
                    ]);
                    continue;
                }
//-----------------------------------------------------------------------------------------
//---------------------If this user in Excluded from this Notification---------------------
//-----------------------------------------------------------------------------------------

                try {
                    if (!empty($notification_event)) {
                        $excluded = json_decode($notification_event->excluded);
                        if (in_array($agent->id, $excluded ?? [])) {
                            $exclusions++;
                            $exclusions_users[] = [
                                'id' => $agent->id,
                                'name' => $agent->full_name,
                                'leads' => $leads->count(),
                                'manager' => $parent->full_name,
                                'manager_id' => $parent->id,
                            ];
                            continue;
                        }
                    }
                } catch (\Throwable $th) {
                    Log::channel('discord')->info('Error while checking excluded agents', [
                        'Time' => Carbon::now()->toDateTimeString(),
                        'Agent' => $agent->id,
                        'Error' => $th->getMessage(),
                    ]);
                    continue;
                }

//-----------------------------------------------------------------------------------------
//--------------If this user is not on Leave or Vocation or in Exclusion list--------------
//-----------Notification will only send if user is active and have 5 or more leads--------
//-----------------------------------------------------------------------------------------

                try {

                    if ($agent->status == 1) {
                        DB::beginTransaction();
                        $check_warning = UserMeta::where('user_id', $agent->id)->where('tag', 'lead_inactivity_warning')->whereDate('created_at', Carbon::today())->first();
                        if ($check_warning) {
                            // ---------------------------------------------------------------
                            // ------------If Warning Found (Suspension) will happen----------
                            // ---------------------------------------------------------------
                            $agent->status = 0;
                            $agent->save();

                            $message = "<b>AX Secondary CRM</b>\n\n";
                            $message .= "Your Secondary CRM account has been <b>⚠️ SUSPENDED ⚠️</b> due to the lack of action on <b>" . $leads->count() . "</b> leads generated in past days (excluding today).\n\nLogin to Secondary CRM now and take action on the leads.";
                            $message .= "\n";
                            $message .= "==========================\n";
                            $message .= "\n";

                            $this->sendTelegramMessageHtml($message, [$agent->telegram_chatid], null, 'Deactivate Agent', 'notification_bot');

                            $parent = User::where('id', $agent->parent_id)->first();
                            if ($parent) {
                                $message = "<b>AX Secondary CRM</b>\n\n";
                                $message .= "The Secondary CRM account of <b>" . $agent->full_name . "</b> has been <b>⚠️ SUSPENDED ⚠️</b> because there was no action taken on <b>" . $leads->count() . "</b> leads created in past days (excluding today).";
                                $message .= "\n";
                                $message .= "==========================\n";
                                $message .= "\n";
                                $this->sendTelegramMessageHtml($message, [$parent->telegram_chatid], null, 'Deactivate Agent', 'notification_bot');

                            }

                            $meta = new UserMeta();
                            $meta->user_id = $agent->id;
                            $meta->meta_key = 'payload';
                            $meta->meta_value = json_encode([
                                'leads' => $leads->count(),
                                'manager' => $parent->full_name,
                                'manager_id' => $parent->id,
                            ]);
                            $meta->tag = 'lead_inactivity_deactivate';
                            $meta->save();

                            $suspentions++;
                            $agent->status = 1;
                            $agent->save();

                            $meta = new UserMeta();
                            $meta->user_id = $agent->id;
                            $meta->meta_key = 'payload';
                            $meta->meta_value = json_encode([
                                'leads' => $leads->count(),
                                'manager' => $parent->full_name,
                                'manager_id' => $parent->id,
                            ]);
                            $meta->tag = 'lead_inactivity_autoactivation';
                            $meta->save();


                        } else {

                            // ---------------------------------------------------------------
                            // ---------If No Warning Found He will receive a warning---------
                            // ---------------------------------------------------------------

                            $message = "<b>AX Secondary CRM</b>\n\n";
                            $message .= "<b>❗️ WARNING ❗️</b> You currently have " . $leads->count() . " leads that were created in past days (excluding today)  and are still in the 'New' status.\n";
                            $message .= "\nPlease take action on these leads within the next 3 Hours, or your account may be Suspended.";
                            $message .= "\n";
                            $message .= "==========================\n";
                            $message .= "\n";

                            $this->sendTelegramMessageHtml($message, [$agent->telegram_chatid], null, 'Deactivate Agent', 'notification_bot');

                            $meta = new UserMeta();
                            $meta->user_id = $agent->id;
                            $meta->meta_key = 'payload';
                            $meta->meta_value = json_encode([
                                'leads' => $leads->count(),
                                'manager' => $parent->full_name,
                                'manager_id' => $parent->id,
                            ]);
                            $meta->tag = 'lead_inactivity_warning';
                            $meta->save();

                            $warnings++;

                        }
                        $users[] = [
                            'id' => $agent->id,
                            'name' => $agent->full_name,
                            'leads' => $leads->count(),
                            'manager' => $parent->full_name,
                            'manager_id' => $parent->id,
                        ];

                        DB::commit();
                    }

                } catch (\Throwable $th) {
                    DB::rollBack();
                    info($th);
                    continue;
                }
            }
        }

        Log::channel('discord')->info('Deactivate Agents With Stale Leads Job', [
            'Time' => Carbon::now()->toDateTimeString(),
            'Warnings' => $warnings,
            'Suspentions' => $suspentions,
            'Exclusions' => $exclusions,
        ]);

        Log::channel('discord')->info('Deactivate Agents With Stale Leads Job: Details', [
            'Users' => $users,
            'Exclusions' => $exclusions_users,
        ]);

    }
}
