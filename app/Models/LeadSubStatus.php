<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadSubStatus extends Model
{
    use HasFactory, SoftDeletes;
    protected  $connection = 'mysql';
    protected $fillable = [
        "name",
        "status",
        "lead_status_id",
        "created_by_id",
        "updated_by_id",
        "created_at",
        "updated_at",
        'order',
        'icon',
        'badge',
        'image',
        'thumbnail',
    ];

    public function created_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id')->select('id', 'name');
    }

    /**
     * Get the user that owns the phone.
     */
    public function updated_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_id')->select('id', 'name');
    }

    public function leadStatus(): BelongsTo
    {
        return $this->belongsTo(LeadStatus::class);
    }
}
