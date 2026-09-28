<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kpi extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'target',
        'agent_id',
        'manager_id',
    ];

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id', 'id');
    }
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id', 'id');
    }
    public function stats()
    {
        return $this->hasOne(KpiStatistic::class);
    }

}
