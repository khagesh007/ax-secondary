<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OauthToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'application',
        'access_token',
        'refresh_token',
        'token_type',
        'meta',
        'expires_at',
        'refresh_expires_at'
    ];


}
