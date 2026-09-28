<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubCommunity extends Model
{
    use HasFactory, SoftDeletes;
    protected  $connection = 'mysql';
    protected $fillable = [
        "region_id",
        "community_id",
        "name",
        "alternate_name",
        "slug",
        "description",
        "path_name",
        "alternate_path_name",
        "latitude",
        "longitude",
        "created_by_id",
        "updated_by_id",
        "status",
        "created_at",
        "updated_at",
        "portal_ids",
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(SubCommunityUser::class);
    }

    public function communityProperties(): HasMany
    {
        return $this->hasMany(CommunityProperty::class);
    }
}
