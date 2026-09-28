<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadCriteriaLocation extends Model
{
    use HasFactory;
    protected  $connection = 'mysql';
    public function parentable()
    {
        return $this->morphTo();
    }
    protected $fillable = [
        'parentable_type',
        'parentable_id',
        'lead_criteria_id'
    ];

}
