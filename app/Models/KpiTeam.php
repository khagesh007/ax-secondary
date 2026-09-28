<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiTeam extends Model
{
    use HasFactory;
   

    protected $fillable = [
        'manager_id',
        'group_id',
        'model',
        'kpi',
        'target',
        'duration',
        'start_date',
        'end_date',
        'is_processed',
        'order',
        'image',
        'icon',
        'badge',
        'created_by_id',
        'updated_by_id',
    ];

    public function result()
    {
        return $this->hasOne(KpiTeamReport::class, 'kpi_id', 'id');
    }
}
