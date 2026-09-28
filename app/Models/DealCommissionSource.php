<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
class DealCommissionSource extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'deal_id',
        'commission_amount',
        'commission_percentage',
        'commission_source',
        'is_vat_applicable',
        'vat_amount',
        'vat_percentage',
        'deal_commission_sources',
        'created_by_id',
        'updated_by_id',
    ];

    protected static function boot()
    {
        parent::boot();

        // Listen for the creating event (when a new record is added)
        static::created(function ($commission) {
            // Calculate the remaining amount and update the company earnings
            $deal = Deal::find($commission->deal_id);

            $netCommission = DealCommissionSource::where('deal_id', $commission->deal_id)->sum('commission_amount');

            $tax = DealCommissionSource::where('deal_id', $commission->deal_id)->sum('vat_amount');

            $dealFee = $deal->deductibles->sum('amount');

            $deal->netCommission = $netCommission - $dealFee;
        });

        // Listen for the updating event (when a record is updated)
        static::updated(function ($commission) {

            $deal = Deal::find($commission->deal_id);

            $netCommission = DealCommissionSource::where('deal_id', $commission->deal_id)->sum('commission_amount');

            $tax = DealCommissionSource::where('deal_id', $commission->deal_id)->sum('vat_amount');

            $dealFee = $deal->deductibles->sum('amount');

            $deal->netCommission = $netCommission - $dealFee;
        });
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->logAll()
            ->dontLogIfAttributesChangedOnly(['updated_at'])
            ->setDescriptionForEvent(fn($eventName) => auth()?->user()?->name . " has ". $eventName . " deal commissions.");
    }

}
