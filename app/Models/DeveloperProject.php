<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeveloperProject extends Model
{
    use HasFactory;
    protected $fillable = [
        'slug',
        'developer_id',
        'project_name',
        'description',
        'status',
        'icon',
        'badge',
        'thumbnail',
        'image',
        'order',
    ];

    public function developer()
    {
        return $this->belongsTo(Developer::class);
    }
    public function images()
    {
        return $this->hasMany(OffplanMeta::class)->where('key', 'Photos');
    }
    public function titles()
    {
        return $this->hasMany(OffplanMeta::class)->where('key', 'title');
    }
}
