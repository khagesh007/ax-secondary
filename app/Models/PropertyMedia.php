<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyMedia extends Model
{
    use HasFactory, SoftDeletes;
    protected  $connection = 'mysql';
    protected $fillable = [
        "property_id",
        "title",
        "description",
        "path",
        "original",
        "thumbnail",
        'password',
        "note",
        "order",
        "type",
        "temp_image",
        "not_uploaded_on_s3",
        "status",
        "media_changed",
        "temp_id",
        "created_by_id",
        "created_at",
        "updated_by_id",
        "updated_at",
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
