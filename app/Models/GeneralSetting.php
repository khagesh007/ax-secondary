<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends Model
{
    use HasFactory;
    protected $fillable = [
        'key',
        'value',
        'module',
        'slug',
    ];

    public const MODULES = [
        "property" => "Property",
        "lead" => "Lead",
        "Listing Leads" => "Listing-Leads",
        "listing" => "Listing",
        "deal" => "Deal",
        "spot" => "Spot",
    ];

}
