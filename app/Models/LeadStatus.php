<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadStatus extends Model
{
    use HasFactory, SoftDeletes;
    protected  $connection = 'mysql';
    protected $fillable = [
        "name",
        'status',
        'created_by_id',
        'updated_by_id',
        'order',
        'icon',
        'badge',
        'image',
        'thumbnail',
    ];

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id')->select('id', 'name');
    }

    public function leads(){
        return $this->hasMany(Lead::class,'lead_status_id')
            ->where('converted_to_listing_lead', null)
            ->where('status', 1)
            ->where('is_archive', 0);

    }

    /**
     * Get the user that owns the phone.
     */
    public function updated_by()
    {
        return $this->belongsTo(User::class, 'updated_by_id')->select('id', 'name');
    }
}
