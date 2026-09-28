<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempPropertyFeatures extends Model
{
    use HasFactory;
    protected  $connection = 'mysql';
    protected $fillable = [
        "temp_property_id",
        "property_amenity_id",
        "created_by_id",
        "updated_by_id",
        "created_at",
        "updated_at",
    ];

    protected $with = ['amenity'];

    public function amenity()
    {
        return $this->belongsTo(PropertyAmenity::class, "property_amenity_id");
    }
}
