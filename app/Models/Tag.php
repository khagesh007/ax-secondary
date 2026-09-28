<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "slug"
    ];


    public function folders()
    {
        return $this->belongsToMany(Folder::class, "folder_tags");
    }
}
