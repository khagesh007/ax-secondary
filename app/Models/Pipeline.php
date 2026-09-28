<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;
class Pipeline extends Model
{
    use HasFactory, SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'deal_type',
        'property_details',
        'property_id',
        'deal_value',
        'commission',
        'commission_type',
        'commission_value',
        'comment',
        'priority',
        'closing_date',
        'status',
        'source_id',
        'manager_comment',
        'reason_for_loss'
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleted(function ($model) {
            $model->deleted_by = auth()->id();
            $model->save();
        });
        static::creating(function ($model) {
            $model->created_by = auth()->id();
        });
        static::updating(function ($model) {
            $model->updated_by = auth()->id();
            $model->updated_at = now();
        });
    }
    public function agent()
    {
        return $this->belongsTo(User::class, 'user_id');

    }
    public function property()
    {
        return $this->belongsTo(Property::class);
    }
    public function source()
    {
        return $this->belongsTo(Source::class);
    }
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function updatedBy(){
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function deletedBy(){
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
