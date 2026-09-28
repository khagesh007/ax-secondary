<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactContactType extends Model
{
    use HasFactory;
    protected  $connection = 'mysql';
    protected $fillable = ['contact_id', 'contact_type_id'];
}
