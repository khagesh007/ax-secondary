<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    use HasFactory;
    protected $hidden = ['is_correct'];
    public function answers() : \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(QuizQAnswer::class, 'quiz_question_id', 'id');
    }

}
