<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;


class Portal extends Model
{
    use HasFactory;
    protected  $connection = 'mysql';
    protected $fillable = [
        "name",
        "short_name",
        "slug",
        "quota",
        "thumbnail",
        "image",
        "xml_url",
        "token",
        'token_abu_dhabi',
        'token_commercial',
        "status",
        "created_by_id",
        "updated_by_id",
        "created_at",
        "updated_at",
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, "created_by_id");
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, "updated_by_id");
    }

    public function users(): HasMany
    {
        return $this->hasMany(PortalUser::class);
    }
    public function properties(): HasManyThrough
    {
        return $this->hasManyThrough(Property::class, PropertyPortal::class, "portal_id", "id", "id", "property_id");
    }
}
