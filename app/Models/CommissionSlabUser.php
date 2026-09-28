<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommissionSlabUser extends Model
{
    use HasFactory;
    // add fillable
    protected $fillable = [
        'user_id',
        'role_id',
        'commission_slab_id',
        'created_by_id',
        'updated_by_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
