<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;

class DealLead extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'deal_leads';
    protected $fillable = [
        "deal_id",
        "lead_id",
        "lead_type",
        "created_at",
        "updated_at",
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->logAll()
            ->dontLogIfAttributesChangedOnly(['updated_at'])
            ->setDescriptionForEvent(fn($eventName) => auth()?->user()?->name . " has ". $eventName . " deal lead.");
    }
}
