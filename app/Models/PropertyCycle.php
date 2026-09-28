<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyCycle extends Model
{
    use HasFactory;

    protected $fillable = [
        "property_id",
        "status",
        "ref_no",
        "created_by_id",
    ];

    function createdBy()
    {
        return $this->belongsTo(User::class, "created_by_id");
    }
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

}
