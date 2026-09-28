<?php

namespace App\Listeners;

use App\Events\ContractExpiring;
use App\Models\NotificationEvent;
use App\Models\User;
use App\Traits\NotificationManager;
use App\Traits\PusherTrait;
use App\Traits\TelegramTrait;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContractExpiringMail;
class ContractExpiringListener
{
    use PusherTrait, NotificationManager, TelegramTrait;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\ContractExpiring  $event
     * @return void
     */
    public function handle(ContractExpiring $event)
    {

        try{
            $notification_event = NotificationEvent::where('class_name', 'ContractExpiring')->where('status','1')->first();

            if(!empty($notification_event)) {
                $process = $this->contractExpiringProcess($event->property, $event->days, $notification_event);

                if (!empty($process)) {
                    $arr = [];
                    $arr['notification'] = $process['notification'];
                    $arr['noti_title'] = $process['notification']['title'];
                    $arr['noti_message'] = $process['notification']['message'];
                    $arr['subscriber'] = $process['users'];
                    $url = $process["url"];


                    $medium = json_decode($notification_event?->medium) ?? [];
                    if (in_array('WEB', $medium)) {
                        $this->pusherSingleChannel('web-notification', ContractExpiring::class, $arr);
                    }
                    if (in_array('EMAIL', $medium)) {
                        $mail_recipients = User::whereIn('id', $process['users'])->select('id', 'name', 'email')->get();
                        foreach ($mail_recipients as $mailUser) {
                            Mail::to($mailUser->email)->send(new ContractExpiringMail($event->property, $event->days, $mailUser, $process));
                        }
                    }
                    if (in_array('TELEGRAM', $medium)) {
                        $agent = $process['agent'];
                        $day = $event->days > 1 ? 'days' : 'day';
                        $message = '<b>Listing contract expiry</b>';
                        if ($agent->telegram_chatid != null) {
                            if ($event->days == 1) {
                                $message .= '

Dear ' . $agent->first_name . ',
The contract of your listing with reference <a href="' . $url . '">#' . $event->property->ref_no . '</a> is expiring Today. Please take necessary action to prevent your listing from going offline.
AX Secondary CRM';
                            } else {
                                $message .= '

Dear ' . $agent->first_name . ',
The contract of your listing with reference <a href="' . $url . '">#' . $event->property->ref_no . '</a> is about to expire in ' . $event->days . ' ' . $day . '. Please take necessary action to prevent your listing from going offline.
AX Secondary CRM';
                            }

                            $this->sendTelegramMessageHtml($message, [$agent->telegram_chatid]);
                        }

                    }

                }
            }
        }catch (\Exception $e){
            \Log::error('Error Import INDEX no.   Data: '.$e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
        }

    }
}
