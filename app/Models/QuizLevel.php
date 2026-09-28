<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'level',
        'time',
        'passing_grade',
        'icon',
        'badge',
        'thumbnail',
        'image',
        'status',
        'order',
        'created_by_id',
        'updated_by_id',
    ];

    public function created_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id')->select('id', 'name');
    }

    public function updated_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_id')->select('id', 'name');
    }
}
