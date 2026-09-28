<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TempPropertyPortal extends Model
{
    use HasFactory;
    protected  $connection = 'mysql';
    protected $fillable = [
        "temp_property_id",
        "portal_id",
        "created_by_id",
        "updated_by_id",
        "created_at",
        "updated_at",
    ];

    public function portal(): BelongsTo
    {
        return $this->belongsTo(Portal::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(TempProperty::class);
    }
}
