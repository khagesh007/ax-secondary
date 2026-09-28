<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TraccarRestrictedCoordinate extends Model
{
    use HasFactory;
    use SoftDeletes;

     protected $fillable = [
        "id",
        "title",
        "lat",
        "lng",
        "radius",
        "zoom",
        "status",
        "user_id",
        "created_by_id",
        "updated_by_id",

    ];


}
