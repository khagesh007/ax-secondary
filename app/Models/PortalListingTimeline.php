<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortalListingTimeline extends Model
{
    use HasFactory;
    protected $connection = 'log_db';
    protected $table = 'portal_listing_timelines';
    protected $fillable = [
        'property_id',
        'portal_id',
        'title',
        'description',
        'status',
        'response',
        'payload',
        'user_id',
    ];

    protected $casts = [
        'response' => 'array',
        'payload' => 'array',
    ];

    public function portal()
    {
        return $this->belongsTo(Portal::class, 'portal_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }
}
