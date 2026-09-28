<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;

class DealMeta extends Model
{
    use HasFactory;
    protected $table = 'deal_meta';
    protected  $connection = 'mysql';
    protected $fillable = [
        'deal_id',
        'meta_key',
        'meta_value'
    ];

    public static function getDealMeta($dealId, $metaKey)
    {
        $dealMeta = self::where('deal_id', $dealId)
            ->where('meta_key', $metaKey)
            ->first();

        return $dealMeta ? $dealMeta->meta_value : null;
    }

    public static function storeDealMeta($dealId, $metaKey, $metaValue)
    {
        $dealMeta = self::updateOrCreate(
            ['deal_id' => $dealId, 'meta_key' => $metaKey],
            ['meta_value' => $metaValue]
        );

        return $dealMeta ? true : false;
    }

    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->logAll()
            ->dontLogIfAttributesChangedOnly(['updated_at'])
            ->setDescriptionForEvent(fn($eventName) => auth()?->user()?->name . " has ". $eventName . " deal meta.");
    }
}
