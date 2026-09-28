<?php

namespace App\Console\Commands;


use App\Events\EventReminder;
use App\Jobs\SendEventReminder;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class EventReminderChecker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calendar:reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'calendar:reminder Remind the event to the attendees before the event start time';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Event::where('reminder','>','0')
            ->where('reminder_to_attendees',1)
            ->where('start_time','>',Carbon::now())
            ->whereNull('last_reminded')
            ->chunk(20, function ($events) {
                foreach ($events as $event) {


                    if ($event->reminder_frequency == 'Minutes') {
                        $start_time = Carbon::parse($event->start_time);
                        $now = Carbon::now();
                        $diff = $start_time->diffInMinutes($now);

                        if ($diff <= $event->reminder) {

                            $event->last_reminded = now();
                            $event->save();
                            $start_in = Carbon::parse($event->start_time)->diffInMinutes(now());
                            $difference =  'in '.$start_in.' minutes';

                            $event->diff_text = $difference;
                            event(new EventReminder($event));

                        }
                    } else if ($event->reminder_frequency == 'Hours') {

                        $start_time = Carbon::parse($event->start_time);
                        $now = Carbon::now();
                        $diff = $start_time->diffInHours($now);

                        if ($diff <= $event->reminder) {

                            $event->last_reminded = now();
                            $event->save();
                            $start_in = Carbon::parse($event->start_time)->diffInHours(now());
                            if($start_in == 1){
                                $difference = 'in ' . $start_in . ' hour';
                            }else {
                                $difference = 'in ' . $start_in . ' hours';
                            }

                            $event->diff_text = $difference;
                            event(new EventReminder($event));
                        }

                    }else if ($event->reminder_frequency == 'Days') {
                        $start_time = Carbon::parse($event->start_time);
                        $now = Carbon::now();
                        $diff = $start_time->diffInDays($now);
                        if ($diff <= $event->reminder) {
                            $event->last_reminded = now();
                            $event->save();
                            $start_in = Carbon::parse($event->start_time)->diffInDays(now());
                            if($start_in == 1){
                                $difference = 'in ' . $start_in . ' day';
                            }else {
                                $difference = 'in ' . $start_in . ' days';
                            }

                            $event->diff_text = $difference;
                            event(new EventReminder($event));
                        }
                    }else if ($event->reminder_frequency == 'Weeks') {
                        $start_time = Carbon::parse($event->start_time);
                        $now = Carbon::now();
                        $diff = $start_time->diffInWeeks($now);
                        if ($diff <= $event->reminder) {
                            $event->last_reminded = now();
                            $event->save();
                            $start_in = Carbon::parse($event->start_time)->diffInDays(now());
                            if($start_in == 1){
                                $difference = 'in ' . $start_in . ' week';
                            }else {
                                $difference = 'in ' . $start_in . ' weeks';
                            }
                            $event->diff_text = $difference;
                            event(new EventReminder($event));
                        }
                    }

                }
            });
        return Command::SUCCESS;
    }
}
