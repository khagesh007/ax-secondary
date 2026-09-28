<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempBayutData extends Model
{
    use HasFactory;
    protected $fillable = ['data_type', 'title','unique_id','is_processed', 'data'];
}
