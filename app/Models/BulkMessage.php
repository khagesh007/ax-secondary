<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BulkMessage extends Model
{
    use HasFactory;
    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }
    public function sender()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }

}
