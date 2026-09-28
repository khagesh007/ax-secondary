<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
class Event extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;
    protected  $connection = 'mysql';
    protected $fillable = [
        "google_calendar_event_id",
        "start_time",
        "end_time",
        "type",
        "priority",
        "event_status",
        "title",
        "location",
        "description",
        "invitation",
        "reminder_status",
        "email_to_attendees",
        "reminder_to_attendees",
        "reminder_date",
        "reminder",
        "reminder_frequency",
        "agent_id",
        "contact_id",
        "lead_id",
        "recurring",
        "recurring_data",
        "property_id",
        "created_by_id",
        "updated_by_id",
        "status",
        "public",
        "note",
        "created_at",
        "updated_at",
    ];

    const TYPE_SELECT = [
        'Viewing',
        'Meeting',
        'Task',
        'Schedule a call',
        'Photographer Booking',
        'Holiday/Leave',
//        'Public Holiday',
        'Other'
    ];
    const PRIORITY_SELECT = ['High', 'Medium', 'Low'];
    const EVENT_STATUS_SELECT = ['Scheduled', 'Cancelled', 'Successful', 'Unsuccessful'];
    const REMINDER_FREQUENCY_SELECT = ['Minutes','Hours','Days','Weeks'];

    public function attendees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, EventAttendee::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, "agent_id");
    }
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, "lead_id");
    }
    public function listing_lead(): BelongsTo
    {
        return $this->belongsTo(ListingLead::class, "listing_lead_id");
    }
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, "contact_id");
    }
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, "property_id");
    }
    public function type(): BelongsTo
    {
        return $this->belongsTo(EventType::class, "event_type_id");
    }
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            //->onQueue('logging')
            ->logOnlyDirty()
            ->logAll()
            ->dontLogIfAttributesChangedOnly(['updated_at'])
            ->setDescriptionForEvent(fn($eventName) => auth()?->user()?->name . " has ". $eventName . " calendar event.");
    }
}
