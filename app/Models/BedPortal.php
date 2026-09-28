<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class BedPortal extends Model
{
    use HasFactory;
    protected  $connection = 'mysql';
    protected $fillable = [
        "name",
        "status",
        "bed_id",
        "portal_id",
        "created_by_id",
        "updated_by_id",

    ];
    public function portal(): BelongsTo
    {
        return $this->belongsTo(Portal::class);
    }
}
