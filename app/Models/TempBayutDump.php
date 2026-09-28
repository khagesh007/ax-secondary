<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempBayutDump extends Model
{
    use HasFactory;

    protected $fillable = [
        "data",
        "is_push_story_api",
    ];
}
