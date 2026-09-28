<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserThirdPartyData extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'portal',
        'start_date',
        'end_date',
        'useful_data',
        'dump_meta',
        'portal_data_updated_at'
    ];
}
