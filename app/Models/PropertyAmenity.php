<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyAmenity extends Model
{
    use HasFactory, SoftDeletes;
    protected  $connection = 'mysql';
    protected $fillable = [
        "title",
        "slug",
        "thumbnail",
        "image",
        'order',
        'icon',
        'badge',
        "color_code",
        "status",
        "amenity_type",
        "created_by_id",
        "updated_by_id",
        "created_at",
        "updated_at",
    ];

    public function created_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id')->select('id', 'name');
    }


    /**
     * Get the user that owns the phone.
     */
    public function updated_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_id')->select('id', 'name');
    }

    public function portals(): HasMany
    {
        return $this->hasMany(PropertyAmenityPortal::class);
    }
}
