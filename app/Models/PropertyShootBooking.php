<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PropertyShootBooking extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;
    protected $connection = 'mysql';
    const STATUS_SELECT = [
        'Waiting for Approval',
        'In Progress',
        'Approved',
        'Rejected',
        'Completed'
    ];
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, "requested_by_id");
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, "approved_by_id");
    }
    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, "rejected_by_id");
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, "assigned_to_id");
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, "created_by_id");
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, "updated_by_id");
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->logAll()
            ->dontLogIfAttributesChangedOnly(['updated_at'])
            ->setDescriptionForEvent(fn($eventName) => auth()?->user()?->name . " has ". $eventName . " property shoot request.");
    }
}
