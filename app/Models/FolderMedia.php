<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FolderMedia extends Model
{
    use HasFactory, SoftDeletes;

    protected  $connection = 'mysql';

    protected $fillable = [
        "folder_id",
        "title",
        "slug",
        "description",
        "path",
        "disk",
        "extension",
        "size",
        "data",
        "data_editable",
        "model_type",
        "model_id",
        "original",
        "thumbnail",
        "note",
        "order",
        "type",
        "status",
        "temp_id",
        "created_by_id",
        "created_at",
        "updated_by_id",
        "updated_at",
    ];

    protected $casts = [
        'data' => 'json'
    ];

    protected $appends = [
        "formatted_size"
    ];

    public function getFormattedSizeAttribute()
    {
        return convertAndFormatFileSize($this->size);
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
