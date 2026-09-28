<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;

class DealTask extends Model
{
    use HasFactory, SoftDeletes;
    protected  $connection = 'mysql';
    protected $fillable = [
        'id',
        'deal_id',
        'user_id',
        'contact_id',
        'subject',
        'description',
        'status',
        'due_date',
        'priority',
        'created_by_id',
        'updated_by_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    public const STATUS_SELECT = ['PENDING', 'RUNNING', 'COMPLETED', 'CANCELED'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id')->select('id', 'name');
    }


    /**
     * Get the user that owns the phone.
     */
    public function updated_by()
    {
        return $this->belongsTo(User::class, 'updated_by_id')->select('id', 'name');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->logAll()
            ->dontLogIfAttributesChangedOnly(['updated_at'])
            ->setDescriptionForEvent(fn($eventName) => auth()?->user()?->name . " has ". $eventName . " deal task.");
    }
}
