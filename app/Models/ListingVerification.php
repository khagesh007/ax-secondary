<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ListingVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'portal_id',
        'user_id',
        'submission_id',
        'portal_listing_id',
        'portal_listing_ref_no',
        'public_profile_id',
        'status',
        'remarks',
        'meta',
        'portal_created_at',
        'portal_expires_at',
        'portal_updated_at',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function portal(): BelongsTo
    {
        return $this->belongsTo(Portal::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
