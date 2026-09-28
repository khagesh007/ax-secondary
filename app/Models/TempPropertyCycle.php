<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempPropertyCycle extends Model
{
    use HasFactory;

    protected $fillable = [
        "temp_property_id",
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