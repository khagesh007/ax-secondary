<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealSplitReferral extends Model
{
    use HasFactory;

    protected $fillable = [
        'deal_id',
        'deal_split_id',
        'user_id',
        'referral_amount',
        'created_by_id',
        'updated_by_id',
    ];

    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }
    public function dealSplit()
    {
        return $this->belongsTo(DealSplit::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
