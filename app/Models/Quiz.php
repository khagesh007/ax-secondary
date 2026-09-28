<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    use HasFactory;
    public function updated_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_id')->select('id', 'name');
    }
    public function created_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id')->select('id', 'name');
    }
    public function quiz_level(): BelongsTo
    {
        return $this->belongsTo(QuizLevel::class, 'quiz_level_id')->select('id', 'name');
    }
    public function attempt()
    {
        return $this->hasMany(QuizAttempt::class, 'quiz_id');
    }
    public function ratedOnly()
    {
        return $this->hasMany(QuizAttempt::class, 'quiz_id')->where('rating', '!=', null);
    }
    public function roles() : HasMany
    {
        return $this->hasMany(ModelRole::class, 'model_id', 'id')->where('model_type', Quiz::class);
    }
}
