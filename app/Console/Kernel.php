<?php

namespace App\Console;

use App\Console\Commands\PortalLeadPoolAutomation\SendInProgressLeadReminderCommand;
use App\Console\Commands\PortalLeadPoolAutomation\SendInProgressLeadWarningCommand;
use App\Console\Commands\PortalLeadPoolAutomation\SendNewLeadReminderCommand;
use App\Console\Commands\PortalLeadPoolAutomation\SendNewLeadWarningCommand;
use App\Console\Commands\PortalLeadPoolAutomation\TransferInProgressLeadsToPoolCommand;
use App\Console\Commands\PortalLeadPoolAutomation\TransferNewLeadsToPoolCommand;
use App\Console\Commands\PortalLeadPoolAutomation\TransferQualifiedLeadsToPoolCommand;
use App\Console\Commands\PortalLeadPoolAutomation\TransferUnderOfferLeadsToPoolCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
class Kernel extends ConsoleKernel
{
    protected $commands = [
        SendNewLeadReminderCommand::class,
        SendNewLeadWarningCommand::class,
        TransferNewLeadsToPoolCommand::class,
        SendInProgressLeadReminderCommand::class,
        SendInProgressLeadWarningCommand::class,
        TransferInProgressLeadsToPoolCommand::class,
        TransferQualifiedLeadsToPoolCommand::class,
        TransferUnderOfferLeadsToPoolCommand::class,
    ];
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        //$schedule->command('bayutleadsfetch:cron')->everyMinute();

        // $schedule->command('agentsstaleleads:cron')->dailyAt('9:30');
        // $schedule->command('agentsstaleleads:cron')->dailyAt('12:30');

        // $schedule->command('mycrm:property')->everyMinute();
        //$schedule->command('parse:email')->everyTwoHours();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
