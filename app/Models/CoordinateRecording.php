<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoordinateRecording extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'user_id',
        'traccar_restricted_coordinate_id',
        'path'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function coordinate()
    {
        return $this->belongsTo(TraccarRestrictedCoordinate::class, 'traccar_restricted_coordinate_id');
    }
}
