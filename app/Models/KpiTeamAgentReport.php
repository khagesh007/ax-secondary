<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiTeamAgentReport extends Model
{
    use HasFactory;

    protected $casts = [
        'data' => 'array',
        'daily_data' => 'array',
    ];
}
