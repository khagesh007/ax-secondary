<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiReport extends Model
{
    use HasFactory;

    protected $casts = [
        "data" => "array",
        "daily_data" => "array",
    ];
}
