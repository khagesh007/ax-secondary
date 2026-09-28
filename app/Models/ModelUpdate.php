<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelUpdate extends Model
{
    use HasFactory;

    protected $table = 'model_updates';

    public function updatable(): MorphTo
    {
        return $this->morphTo();
    }

    public function effectee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'model_id');
    }

    public function causer()
    {
        return $this->belongsTo(User::class, 'updated_by_id', 'id');
    }

}
