<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DealMedia extends Model
{
    use HasFactory;
    protected  $connection = 'mysql';
    protected $fillable = [
        "deal_id",
        "model_type",
        "model_id",
        "title",
        "description",
        "path",
        "original",
        "thumbnail",
        "extension",
        "note",
        "order",
        "type",
        "status",
        "temp_id",
        "created_by_id",
        "created_at",
        "updated_by_id",
        "updated_at",
    ];

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class, 'deal_id');
    }
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }

    public function model(): MorphTo  {
        return $this->morphTo();
    }
}
