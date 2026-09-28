<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealInvite extends Model
{
    use HasFactory;

    public function deal()
    {
        return $this->belongsTo(Deal::class, 'deal_id');
    }
    public function media()
    {
        return $this->belongsTo(DealMedia::class, 'deal_media_id');
    }
    public function invited_by()
    {
        return $this->belongsTo(User::class, 'invited_by_id');
    }
    public function invited_to()
    {
        return $this->belongsTo(User::class, 'invited_to_id');
    }
}
