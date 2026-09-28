<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cjmellor\Approval\Concerns\MustBeApproved;

class TempPropertyPrice extends Model
{
    use HasFactory, MustBeApproved;
    protected  $connection = 'mysql';
    protected $fillable = [
        "property_id",
        "price_on_application",
        "price",
    ];
}