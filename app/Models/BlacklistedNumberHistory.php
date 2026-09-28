<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlacklistedNumberHistory extends Model
{
    use HasFactory;
    protected $fillable = [
        'model',
        'blacklisted_number_id',
        'user_id',
        'data',
    ];
}
