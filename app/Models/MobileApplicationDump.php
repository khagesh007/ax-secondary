<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MobileApplicationDump extends Model
{
    use HasFactory;

    protected $fillable = [
        "payload",
        "response",
        "title"
    ];
}
