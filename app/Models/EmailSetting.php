<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Role;

class EmailSetting extends Model
{
    use HasFactory;
    protected  $connection = 'mysql';
    protected $fillable = [
        "title",
        "cc",
        "bcc",
        "reply_to",
        "status",
        "created_at",
        "updated_at",
    ];

    public const TITLES = [
        "User Registered",
        "User Password Updated",
        "Listing Added",
        "Listing Publish Request",
        "Listing Unpublish Request",
    ];

    protected $casts = [
        "cc" => "array",
        "bcc" => "array",
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, EmailSettingRole::class);
    }
}
