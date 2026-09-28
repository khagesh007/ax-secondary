<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetingAttendee extends Model
{
    use HasFactory;
    protected  $connection = 'mysql';
    protected $fillable = [
        "meeting_id",
        "user_id",
        "confirmed",
        "note",
        "created_by_id",
        "updated_by_id",
        "created_at",
        "updated_at",
    ];
}
