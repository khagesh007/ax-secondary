<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadCriteria extends Model
{
    use HasFactory;
    protected  $connection = 'mysql';
    protected $casts = [
        "communities" => "json"
    ];
    public $guarded = [];
    protected $fillable = [
        'lead_id',
        'created_by_id',
        'view',
    ];

    public function criteriaLocation(){
        return $this->hasMany(LeadCriteriaLocation::class, 'parentable');
    }
    public function locations(){
        return $this->hasMany(LeadCriteriaLocation::class,'lead_criteria_id','id');
    }
    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id')->select('id', 'name');
    }
    public function minBed(){
        return $this->belongsTo(Bed::class,'min_bed_id');
    }
    public function maxBed(){
        return $this->belongsTo(Bed::class,'max_bed_id');
    }

    public function minBath(){
        return $this->belongsTo(Bath::class,'min_bath_id');
    }
    public function maxBath(){
        return $this->belongsTo(Bath::class,'max_bath_id');
    }

    /**
     * Get the user that owns the phone.
     */
    public function updated_by()
    {
        return $this->belongsTo(User::class, 'updated_by_id')->select('id', 'name');
    }
}
