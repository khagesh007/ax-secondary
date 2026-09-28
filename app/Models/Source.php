<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Source extends Model
{
    use HasFactory, SoftDeletes;
    protected  $connection = 'mysql';
    public $relationships = ['leads' => 'leads', 'contacts' => 'contacts'];

    protected $fillable = [
        "name",
        "status",
        'is_lead_source',
        'is_contact_source',
        'order',
        'thumbnail',
        "created_by_id",
        "updated_by_id",
        "created_at",
        "updated_at",
    ];
    public function leads()
    {
        return $this->hasMany(Lead::class,'source_id');
    }
    public function contacts()
    {
        return $this->hasMany(Contact::class,'contact_source_id');
    }

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
}
