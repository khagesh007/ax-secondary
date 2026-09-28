<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class ContactMedia extends Model
{
    use HasFactory;
    protected  $connection = 'mysql';
    protected $fillable = [
        "contact_id",
        "deal_id",
        "title",
        "description",
        "path",
        "original",
        "thumbnail",
        "extension",
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

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }
}
