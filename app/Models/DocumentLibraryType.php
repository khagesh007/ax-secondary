<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentLibraryType extends Model
{
    use HasFactory;

    protected $dates = [
        'deleted_at',
    ];

    protected $appends = [
        "thumb_url",
        "image_url",
    ];

    public const Frequencies = [
        'Daily',
        'Weekly',
        'Monthly',
        'Quarterly',
        'Biannually',
        'Annually',
        'Not applicable',
    ];


    public function getImageUrlAttribute()
    {
        return $this->image ?? "https://ui-avatars.com/api/?name=".urlencode($this->name)."&color=d59a7f&background=000000&border-color=d59a7f&border=1px";
    }

    public function getThumbUrlAttribute()
    {
        return $this->thumbnail ?? "https://ui-avatars.com/api/?name=".urlencode($this->name)."&color=7F9CF5&background=EBF4FF";
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }
}
