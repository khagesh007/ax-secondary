<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'deal_id',
        'user_id',
        'role_id',
        'action_by_id',
        'approval_status',
        'comment',
    ];

    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function actionBy()
    {
        return $this->belongsTo(User::class, 'action_by_id');
    }
}
