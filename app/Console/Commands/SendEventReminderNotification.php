<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Notifications\EventReminderNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendEventReminderNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'event:reminderNotification';

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

        // Get the current time
        $now = Carbon::now();

        // Fetch events that have not yet started
        $events = Event::where('start_time', '>', $now)->get();

        foreach ($events as $event) {
            // dd($event->id);
            // Calculate the reminder time based on reminder and reminder_frequency
            $reminderTime = $this->calculateReminderTime($event->start_time, $event->reminder, $event->reminder_frequency);
            dd($reminderTime);
            // If the current time is equal or past the reminder time, send notification
            if ($now->greaterThanOrEqualTo($reminderTime)) {
                // if time is less than tha "reminder_frequency" and reminder  where reminder_frequency can be hours, days or weeks, and reminder is the number of hours, days or weeks before the event.
                if ($now->diffInHours($reminderTime) <= $event->reminder) {
                    $event->user->notify(new EventReminderNotification($event));
                }

            }
        }

        return Command::SUCCESS;
    }
    /**
     * Calculate the reminder time.
     */
    private function calculateReminderTime($startTime, $reminder, $frequency)
    {
        $reminderTime = Carbon::parse($startTime);

        // Adjust reminder time based on frequency
        switch ($frequency) {
            case 'hours':
                $reminderTime->subHours($reminder);
                break;
            case 'days':
                $reminderTime->subDays($reminder);
                break;
            case 'weeks':
                $reminderTime->subWeeks($reminder);
                break;
            case 'minutes':
                $reminderTime->subMinutes($reminder);
                break;
            default:
                // If frequency is not matched, fallback to hours
                $reminderTime->subHours($reminder);
                break;
        }

        return $reminderTime;
    }
}
