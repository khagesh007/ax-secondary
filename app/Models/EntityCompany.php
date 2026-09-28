<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntityCompany extends Model
{
    protected $connection = 'mysql';

    protected $fillable = [
        'name',
        'orn',
        'permit_number',
        'license_number',
    ];
}
