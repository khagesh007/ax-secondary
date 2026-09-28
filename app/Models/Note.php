<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Note extends Model
{
    use HasFactory;
    protected  $connection = 'mysql';
    protected $fillable = [
        "model",
        "model_id",
        "note",
        "status",
        "created_by_id",
        "updated_by_id",
        "created_at",
        "updated_at",
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
