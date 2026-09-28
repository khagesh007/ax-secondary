<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ListingLeadNote extends Model
{
    use HasFactory;
    protected $fillable=[
        'listing_lead_id',
        'note',
        'status',
        'created_by_id',
        'updated_by_id',
   
    ];
    public function createdBy(): BelongsTo
    {
       return $this->belongsTo(User::class);
    }
}
