<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAppLogin extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ip_address',
        'latitude',
        'longitude',
        'location_name',
        'device_name',
        'device_os',
        'device_version',
        'app_version',
    ];
}
