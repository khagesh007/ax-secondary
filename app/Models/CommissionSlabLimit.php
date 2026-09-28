<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommissionSlabLimit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'from_value',
        'to_value',
        'commission_value',
        'commission_type', // Percentage, Fixed
        'commission_slab_id'
    ];
}
