<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
class QuizAttempt extends Model
{
    use HasFactory;

    protected function status(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                // If "completed_at" is null and it has been more than an hour since "created_at"
                if (is_null($this->completed_at) && now()->diffInHours($this->created_at) > 1) {
                    return 'unfinished';
                }
                // Otherwise, return the default status
                return $value;
            }
        );
    }
    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function answers()
    {
        return $this->hasMany(QuizAttemptAnswer::class, 'quiz_attempt_id');
    }
}
