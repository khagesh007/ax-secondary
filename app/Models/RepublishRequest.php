<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepublishRequest extends Model
{
    use HasFactory;
    function user(){
        return $this->belongsTo(User::class);
    }
    function admin(){
        return $this->belongsTo(User::class);
    }
}
