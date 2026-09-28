<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubCommunityUser extends Model
{
    use HasFactory;
    protected  $connection = 'mysql';
    protected $fillable = [
        "user_id",
        "community_id",
        "sub_community_id",
        "created_by_id",
        "updated_by_id",
        "created_at",
        "updated_at",
    ];

    public function subCommunity(): BelongsTo
    {
        return $this->belongsTo(SubCommunity::class);
    }
}
