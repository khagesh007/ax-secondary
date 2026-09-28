<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyCategoryPortal extends Model
{
    use HasFactory;
    protected  $connection = 'mysql';
    protected $fillable = [
        "property_category_id",
        "portal_id",
        "title",
        "created_by_id",
        "updated_by_id",
        "created_at",
        "updated_at",
    ];

    public function propertyCategory(): BelongsTo
    {
        return $this->belongsTo(PropertyCategory::class);
    }

    public function portal(): BelongsTo
    {
        return $this->belongsTo(Portal::class);
    }
}
