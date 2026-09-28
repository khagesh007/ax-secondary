<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KpiOtherReport extends Model
{
    use HasFactory;

    protected $fillable = [
        "manager_id",
        "agent_id",
        "model",
        "kpi",
        "kpi_title",
        "total",
        "count",
        "data",
        "daily_data",
        "start_date",
        "end_date",
        "duration",
    ];

    protected $casts = [
        "data" => "array",
        "daily_data" => "array",
    ];

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }
}
