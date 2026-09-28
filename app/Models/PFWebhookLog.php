<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PFWebhookLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'webhook_id',
        'type',
        'payload',
        'meta',
        'is_processed',
        'processed_at',
        'status'
    ];
    protected $casts = [
        'payload' => 'array',
        'meta' => 'array',
        'is_processed' => 'boolean',  // Cast to true/false
        'processed_at' => 'datetime', // Handle Carbon datetime instance
    ];
}
