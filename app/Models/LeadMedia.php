<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadMedia extends Model
{
    use HasFactory;
    protected  $connection = 'mysql';
    protected $fillable = [
        "lead_id",
        "title",
        "deal_id",
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

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }

}
