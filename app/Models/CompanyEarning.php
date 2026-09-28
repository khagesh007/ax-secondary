<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyEarning extends Model
{
    use HasFactory;
    protected $fillable = ['deal_split_id', 'amount'];

    public function dealSplit()
    {
        return $this->belongsTo(DealSplit::class);
    }
}
