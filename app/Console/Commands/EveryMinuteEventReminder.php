<?php

namespace App\Console\Commands;

use App\Mail\EventReminderMail;
use App\Mail\LeadFollowUpReminderMail;
use App\Models\Event;
use App\Models\Lead;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class EveryMinuteEventReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'every_minute_event_reminder:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send event reminder';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $events = Event::whereNotNull("reminder")
            ->whereNotNull("reminder_frequency")
            ->where("reminder_status", false)
            ->with(["attendees", "agent"])
            ->get();

        foreach ($events as $event){
            $time = Carbon::now();
            if ($event->reminder_frequency == "Minutes"){
                $time->subMinutes($event->reminder);
            }else if ($event->reminder_frequency == "Hours"){
                $time->subHours($event->reminder);
            }else if ($event->reminder_frequency == "Days"){
                $time->subDays($event->reminder);
            }else if ($event->reminder_frequency == "Weeks"){
                $time->subWeeks($event->reminder);
            }else continue;

            $startTime = Carbon::parse($event->start_date);
            if ($startTime->equalTo($time)){
                Mail::to($event->agent)->send(new EventReminderMail($event, $event->agent));
                foreach ($event->attendees ?? [] as $attendee){
                    Mail::to($attendee)->send(new EventReminderMail($event, $attendee));
                }
                $event->reminder_status = true;
                $event->save();
            }
        }
    }
}
