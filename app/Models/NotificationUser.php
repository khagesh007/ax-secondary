<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationUser extends Model
{
    use HasFactory;
    protected  $connection = 'mysql';

    public function notification(): BelongsTo
    {
        return $this->belongsTo(Notification::class);
    }
}
