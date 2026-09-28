<?php

namespace App\Providers;

use App\Events\ContractExpiring;
use App\Events\DealApproved;
use App\Events\DealCompleted;
use App\Events\DealCreated;
use App\Events\DealInviteAgent;
use App\Events\DealNoteAdded;
use App\Events\PropertyApproval;
use App\Events\PropertyCreated;
use App\Events\DealRejected;
use App\Events\DealResubmitted;
use App\Events\DealSubmittedForApproval;
use App\Events\EventCreated;
use App\Events\EventReminder;
use App\Events\EventUpdated;
use App\Events\LeadAppNotification;
use App\Events\ExclusiveExpiring;
use App\Events\ListingNoteCreated;
use App\Events\RequestLimitExceeded;
use App\Events\SalesProgressionAssigned;
use App\Events\SalesProgressionChanged;
use App\Events\ShootingRequestCreated;
use App\Events\ShootJobAction;
use App\Events\ShootJobCreated;
use App\Events\ShootJobStatusChange;
use App\Events\StaleLeadAgentNotification;
use App\Listeners\ContractExpiringListener;
use App\Listeners\DealApprovedListener;
use App\Listeners\DealCompletedListener;
use App\Listeners\DealCompletedLeadListener;
use App\Listeners\DealCreatedListener;
use App\Listeners\DealInviteAgentListener;
use App\Listeners\DealRejectedListener;
use App\Listeners\DealResubmittedListener;
use App\Listeners\DealSubmittedListener;
use App\Listeners\SalesProgressionAssignedListener;
use App\Listeners\SalesProgressionChangedListener;
use App\Listeners\StaleLeadListenerAgentListener;
use App\Listeners\EventCreatedListener;
use App\Listeners\EventReminderListener;
use App\Listeners\EventUpdatedListener;
use App\Listeners\ExclusiveExpiringListener;
use App\Listeners\LeadCreatedListener;
use App\Listeners\ListingNoteCreatedListener;
use App\Listeners\RequestLimitExceededListener;
use App\Listeners\ShootingRequestListener;
use App\Listeners\ShootJobActionListener;
use App\Listeners\ShootJobCreatedListener;
use App\Listeners\ShootJobStatusChangeListener;
use App\Listeners\ShootRequestCreatedListener;
use App\Listeners\ShootRequestListener;
use App\Models\Job;
use App\Observers\JobObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\LeadCreated;
use App\Events\LeadNoteAdded;
use App\Listeners\LeadNoteListener;
use App\Listeners\LeadTelegramPush;
use App\Events\UserCreated;
use App\Listeners\DealNoteAddedListener;
use App\Listeners\UserCreatedListener;
use App\Listeners\HandleLoginSucceeded;
use PragmaRX\Google2FALaravel\Events\LoginSucceeded;
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        LoginSucceeded::class => [
            HandleLoginSucceeded::class,
        ],
        LeadCreated::class => [
            LeadTelegramPush::class,
            LeadCreatedListener::class,
        ],
        LeadNoteAdded::class => [
            LeadNoteListener::class,
        ],
        EventCreated::class => [
            EventCreatedListener::class
        ],
        StaleLeadAgentNotification::class => [
            StaleLeadListenerAgentListener::class
        ],
        EventUpdated::class => [
            EventUpdatedListener::class
        ],
        ListingNoteCreated::class => [
            ListingNoteCreatedListener::class
        ],
        ContractExpiring::class => [
            ContractExpiringListener::class
        ],
        ExclusiveExpiring::class => [
            ExclusiveExpiringListener::class
        ],
        UserCreated::class => [
            // UserCreatedListener::class,
        ],
        PropertyCreated::class => [
        ],
        PropertyUpdated::class => [
        ],
        PropertyApproval::class => [
        ],
        LeadAppNotification::class => [
        ],
        EventReminder::class => [
            EventReminderListener::class
        ],
        ShootingRequestCreated::class => [
           ShootingRequestListener::class
        ],
        ShootJobAction::class => [
            ShootJobActionListener::class
        ],
        ShootJobStatusChange::class => [
           ShootJobStatusChangeListener::class
        ],
        DealCreated::class => [
          DealCreatedListener::class
        ],

        RequestLimitExceeded::class => [
            RequestLimitExceededListener::class
        ],
        \Illuminate\Auth\Events\Failed::class => [
            \App\Listeners\RecordFailedLoginAttempt::class,
        ],
        DealNoteAdded::class => [
            DealNoteAddedListener::class
        ],
        DealSubmittedForApproval::class => [
            DealSubmittedListener::class
        ],
        DealResubmitted::class => [
            DealResubmittedListener::class
        ],
        DealRejected::class => [
          DealRejectedListener::class
        ],
        DealInviteAgent::class => [
           DealInviteAgentListener::class
        ],
        DealApproved::class => [
           DealApprovedListener::class
        ],
        DealCompleted::class => [
           DealCompletedListener::class,
            DealCompletedLeadListener::class
        ],
        SalesProgressionAssigned::class => [
          SalesProgressionAssignedListener::class
        ],
        SalesProgressionChanged::class => [
           SalesProgressionChangedListener::class
        ],

    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Job::observe(JobObserver::class);
    }
}
