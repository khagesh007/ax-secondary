<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TraccarClientSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        "id",
        "server_url",
        "port",
        "location_accuracy",
        "frequency",
        "distance",
        "angle",
        "offline_buffering",
        "status",
        "track_from",
        "track_to",
        "created_by_id",
        "updated_by_id",
        'password',
        'username',
        'coordinates_accuracy',
        'version',

    ];

}
