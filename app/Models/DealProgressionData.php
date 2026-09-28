<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealProgressionData extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class,'conveyancer_id','id');
    }
}
