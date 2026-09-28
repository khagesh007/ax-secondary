<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Type extends Model
{
    use HasFactory, SoftDeletes;
    public $relationships = ['leads' => 'leads', 'contacts' => 'contacts'];
    protected  $connection = 'mysql';
    protected $fillable = [
        "name",
        "status",
        'is_lead_type',
        'is_contact_type',
        'order',
        "created_by_id",
        "updated_by_id",
        "created_at",
        "updated_at",
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
        return $this->hasMany(Lead::class, 'type_id');
    }

    public function contactTypes(): BelongsToMany
    {
        return $this->belongsToMany(ContactContactType::class, 'contact_contact_types', 'type_id', 'contact_type_id');
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class, 'contact_type_id');
    }

}
