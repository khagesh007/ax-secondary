<?php

namespace App\Models;

use App\Http\Controllers\Api\V1\Deals\DealApiController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;

class DealDeductible extends Model
{
    use HasFactory;

    public function deductible_type()
    {
        return $this->belongsTo(DealDeductibleType::class, 'deal_deductible_type_id', 'id');
    }
    public function deal()
    {
        return $this->belongsTo(Deal::class, 'deal_id', 'id');
    }
    // Other model configurations and relationships

    public function file()
    { // DealMedia::class has model_type and model_id
        return $this->morphOne(DealMedia::class, 'model');
    }
    public static function boot()
    {
        parent::boot();

        // Listen for the saved event
        static::saved(function ($dealDeductible) {
            // Update the net commission when a deductible is saved
            $dealDeductible->updateNetCommission();
        });

        // You may also want to listen for the deleted event if deductibles can be deleted
        static::deleted(function ($dealDeductible) {
            // Update the net commission when a deductible is deleted
            $dealDeductible->updateNetCommission();
        });
    }

    public function updateNetCommission()
    {
        $deal = $this->deal;

        if ($deal) {

            $dealTaxes      = $deal->commissionSources->sum('vat_amount');
            $netCommission  = $deal->gross_commission - $dealTaxes;
            $deductibleFees = $deal->deductibles->sum('amount');
            $total_sales_progression = DealProgressionData::where('deal_id', $deal->id)->sum('progression_amount') ?? 0;
            $updatedNetCommission = $netCommission - $deductibleFees - $total_sales_progression;


            // Update the net commission for the deal
            $deal->update(['net_commission' => $updatedNetCommission]);
            (new DealApiController())->refreshSplitCommissions($deal);
        }
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->logAll()
            ->dontLogIfAttributesChangedOnly(['updated_at'])
            ->setDescriptionForEvent(fn($eventName) => auth()?->user()?->name . " has ". $eventName . " deal deductible.");
    }
}
