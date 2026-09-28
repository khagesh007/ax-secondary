<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobNote extends Model
{
    use HasFactory;
    protected  $connection = 'mysql';
    protected $fillable = [
        "job_id",
        "note",
        "created_by_id",
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
