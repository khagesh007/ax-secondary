<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempDump extends Model
{
    use HasFactory;
    protected $fillable = [
        'data',
        'name',
        'flags',
        'unique_id',
        'unique_id',
        'end_at',
        'status',
    ];
}
