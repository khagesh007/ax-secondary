<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Job extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;
    protected  $connection = 'mysql';
    protected $fillable = [
        "type",
        "priority",
        "job_status",
        "description",
        "deadline_date",
        "delivery_date",
        "property_id",
        "requested_by_id",
        "approved_by_id",
        "assigned_to_id",
        "note",
        "created_by_id",
        "updated_by_id",
        "created_at",
        "updated_at",
    ];

    const TYPE_SELECT = [
        'Photos/Video Shoot',
        'Photo Editing',
        'Video Editing',
        'Social Media Marketing',
        'SM Paid Marketing',
        'Flayer/Poster Design',
        'Email Marketing',
        'SMS Marketing',
        'CRM Changes',
        'Website Changes',
        'Other'
    ];
    const PRIORITY_SELECT = ['Urgent', 'High', 'Normal', 'Low'];
    const JOB_STATUS_SELECT = [
        'Created',
        'Waiting for Approval',
        'Approved',
        'Under Process',
        'Completed',
        'Canceled',
        'On-Hold'
    ];

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, "requested_by_id");
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, "approved_by_id");
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
            ->setDescriptionForEvent(fn($eventName) => auth()?->user()?->name . " has ". $eventName . " job.");
    }
}
