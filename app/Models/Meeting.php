<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Meeting extends Model
{
    use HasFactory, SoftDeletes;
    protected  $connection = 'mysql';
    public const STATUS_SELECT = ['PENDING', 'RUNNING', 'COMPLETED', 'CANCELED'];


    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id')->select('id', 'name');
    }


    /**
     * Get the user that owns the phone.
     */
    public function updated_by()
    {
        return $this->belongsTo(User::class, 'updated_by_id')->select('id', 'name');
    }

    public function attendees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, MeetingAttendee::class);
    }
}
