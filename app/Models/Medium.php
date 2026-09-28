<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Medium extends Model
{
    use HasFactory,SoftDeletes;
    protected  $connection = 'mysql';
    protected $table = 'mediums';

    public $relationships = ['Leads' => 'leads'];


    protected $fillable = [
        'name',
        'status',
        'is_lead_source',
        'is_contact_source',
        'order',
        'thumbnail',
        'created_by_id',
        'updated_by_id',
    ];

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id')->select('id', 'name');
    }

    /**
     * Get the user that owns the phone.
     */
    public function updated_by()
    {
        return $this->belongsTo(User::class, 'updated_by_id')->select('id', 'name');
    }

    public function leads()
    {
        return $this->hasMany(Lead::class,'lead_medium_id');
    }

}
