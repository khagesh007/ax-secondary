<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelRole extends Model
{
    use HasFactory;
    protected $fillable = ['model_type','model_id','role_id'];
}
