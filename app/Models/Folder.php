<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Folder extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = "mysql";

    protected $fillable = [
        "name",
        "slug",
        "folder_order",
        "thumbnail",
        "parent_id",
        "model_type",
        "model_id",
        "created_by_id",
        "updated_by_id"
    ];

    protected $appends = [
        "total_no_files"
    ];

    public function parent()
    {
        return $this->belongsTo(Folder::class, "parent_id");
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, "created_by_id");
    }

    public function updatedBY()
    {
        return $this->belongsTo(User::class, "updated_by_id");
    }

    public function folderMedia()
    {
        return $this->hasMany(FolderMedia::class);
    }

    public function children()
    {
        return $this->hasMany(Folder::class, 'parent_id', 'id');
    }

    public function folderable()
    {
        return $this->morphTo();
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, "folder_tags");
    }

    function getTotalNoFilesAttribute()
    {
        return FolderMedia::whereIn("folder_id", getFolderChildrenIdsByID($this->id))->select(["id", "parent_id"])->count();
    }
}
