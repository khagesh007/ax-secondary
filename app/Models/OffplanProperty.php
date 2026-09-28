<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OffplanProperty extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function developer()
    {
        return $this->belongsTo(Developer::class);
    }
    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
