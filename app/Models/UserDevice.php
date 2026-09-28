<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserDevice extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'traccar_device_id',
        'user_id',
        'device_name',
        'device_model',
        'device_os',
        'device_os_version',
        'device_token',
        'login_ip',
        'coordinates',
        'last_active_at',
    ];


    function agent(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
