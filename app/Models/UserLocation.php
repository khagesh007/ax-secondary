<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLocation extends Model
{
    use HasFactory;
    public function parentable()
    {
        return $this->morphTo();
    }
    protected $fillable = [
        'parentable_id',
        'parentable_type',
        'user_id'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
