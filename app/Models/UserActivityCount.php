<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserActivityCount extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "reporting_date",
        "report_generated_at",
        "data",
        "count",
        "created_at",
        "updated_at"
    ];
}
