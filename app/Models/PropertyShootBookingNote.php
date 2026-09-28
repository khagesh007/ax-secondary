<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyShootBookingNote extends Model
{
    use HasFactory;
    protected $fillable = [
        'property_shoot_booking_id',
        'note',
        'created_by_id',
        'updated_by_id',
    ];
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
