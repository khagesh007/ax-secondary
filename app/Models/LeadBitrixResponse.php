<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class LeadBitrixResponse extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'lead_id',
        'is_success',
        'request',
        'response'
    ];
}
