<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class PropertyFeatures extends Model
{
    use HasFactory;
    protected  $connection = 'mysql';
    protected $fillable = [
        "property_id",
        "property_amenity_id",
        "created_by_id",
        "updated_by_id",
        "created_at",
        "updated_at",
    ];

    protected $with = ['amenity'];

    public function amenity(): BelongsTo
    {
        return $this->belongsTo(PropertyAmenity::class, "property_amenity_id");
    }
//    public function featurePortals(): HasManyThrough
//    {
//        return $this->hasManyThrough( PropertyAmenity::class,PropertyAmenityPortal::class, 'property_amenity_id','id','property_amenity_id','property_amenity_id');
//    }
}
