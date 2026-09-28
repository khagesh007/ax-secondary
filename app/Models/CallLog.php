<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CallLog extends Model
{
    use HasFactory,SoftDeletes;
    protected  $connection = 'mysql';
    protected $guarded = [];

    function user(){
        return $this->belongsTo(User::class,'user_id');
    }
    function lead(){
        return $this->belongsTo(Lead::class);
    }
    function contact(){
        return $this->belongsTo(Contact::class,'contact_id');
    }

    function leads(){
        return $this->belongsToMany(Lead::class, 'call_log_leads');
    }
    function listingLeads(){
        return $this->belongsToMany(ListingLead::class, 'call_log_listing_leads');
    }

    public function criteria(): HasOne
    {
        return $this->hasOne(LeadCriteria::class);
    }
    public function listingLead()
    {
        return $this->hasOne(CallLogListingLead::class, 'call_log_id', 'id');
    }
    // If Later on we need morph many relation
    // function leads(){
    //     return $this->morphedByMany(Lead::class, 'call_loggable');
    // }

    // function contacts(){
    //     return $this->morphedByMany(Contact::class, 'call_loggable');
    // }
}
