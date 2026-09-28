<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class DealSplit extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $connection = 'mysql';
    protected $fillable = [
        'deal_id',
        'user_id',
        'split_amount',
        'split_percentage',
        'commission_source',
        'description',
        'approved_by_id',
        'created_by_id',
        'updated_by_id',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function commissions()
    {
        return $this->hasMany(DealSplitCommission::class);
    }
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function companyEarning()
    {
        return $this->hasOne(CompanyEarning::class);
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->logAll()
            ->dontLogIfAttributesChangedOnly(['updated_at'])
            ->setDescriptionForEvent(fn($eventName) => auth()?->user()?->name . " has ". $eventName . " deal split.");
    }

}
