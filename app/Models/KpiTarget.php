<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiTarget extends Model
{
    use HasFactory;

    public function result()
    {
        return $this->hasOne(KpiReport::class, 'kpi_id', 'id');
    }
}
