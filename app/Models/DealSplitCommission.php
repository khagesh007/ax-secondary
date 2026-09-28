<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
class DealSplitCommission extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        "deal_id",
        "user_id",
        "deal_split_id",
        "commission_value",
        "commission_category",
        "commission_type",
        'commission_from',
        "commission",
        "created_by_id",
        "updated_by_id"
    ];
    protected static function boot()
    {
        parent::boot();

        // Listen for the creating event (when a new record is added)
        static::created(function ($commission) {
            // Calculate the remaining amount and update the company earnings
            $dealSplit = DealSplit::find($commission->deal_split_id);

            $totalDistributedCommission = DealSplitCommission::where('deal_split_id', $commission->deal_split_id)->sum('commission');

            $remainingAmount = $dealSplit->split_amount - $totalDistributedCommission;

            CompanyEarning::updateOrCreate(
                ['deal_split_id' => $commission->deal_split_id],
                ['amount' => $remainingAmount]
            );
        });

        // Listen for the updating event (when a record is updated)
        static::updated(function ($commission) {

            $dealSplit = DealSplit::find($commission->deal_split_id);

            $totalDistributedCommission = DealSplitCommission::where('deal_split_id', $commission->deal_split_id)->sum('commission');

            $remainingAmount = $dealSplit->split_amount - $totalDistributedCommission;

            CompanyEarning::updateOrCreate(
                ['deal_split_id' => $commission->deal_split_id],
                ['amount' => $remainingAmount]
            );
        });
    }
    public function user() : \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function split() : \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(DealSplit::class,'deal_split_id','id');
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->logAll()
            ->dontLogIfAttributesChangedOnly(['updated_at'])
            ->setDescriptionForEvent(fn($eventName) => auth()?->user()?->name . " has ". $eventName . " deal split commission.");
    }

}
