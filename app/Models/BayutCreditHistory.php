<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BayutCreditHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'portal',
        'portal_transaction_id',
        'date',
        'transaction_time',
        'amount',
        'type',
        'action',
    ];
}
