<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shortlink extends Model
{
    protected  $connection = 'mysql';
    protected $fillable = [
        'source_id',
        'slug',
        'link',
        'message',
        'short_link',
        'count',
        'opted_in',
        'lead_id'
    ];
    use HasFactory;
    public function lead(){
        return $this->belongsTo(Lead::class);
    }
}
