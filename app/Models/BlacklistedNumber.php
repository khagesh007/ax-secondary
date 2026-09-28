<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlacklistedNumber extends Model
{
    use HasFactory;
    protected $fillable = [
        'number',
        'blocked_by',
        'unblocked_by',
        'status',
    ];
}
