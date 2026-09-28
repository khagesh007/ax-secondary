<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Contact extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;
    protected  $connection = 'mysql';
    protected static function boot()
    {
        parent::boot();

        static::deleted(function ($model) {
            $model->deleted_by_id = auth()->id();
            $model->save();
        });
        static::creating(function ($model) {
            if($model->agent_id){
                $user = User::find($model->agent_id);
                $model->region_id = $user->region_id;
            }
        });
        static::updating(function ($model) {
            $model->updated_by_id = auth()->id();
            $model->updated_at = now();
            if($model->agent_id){
                $user = User::find($model->agent_id);
                $model->region_id = $user->region_id;
            }
            $modelUpdate = new ModelUpdate;
            $modelUpdate->model_type = get_class($model); // The class name of the Property model
            $modelUpdate->model_id = $model->id; // The ID of the Property record
            $modelUpdate->updated_at = now();
            $modelUpdate->updated_by_id = auth()->id() ?? null; // Assuming you have user authentication set up
            $modelUpdate->description = 'Updated Lead details';
            $modelUpdate->data = json_encode($model->getDirty()); // Capture the updated data
            $modelUpdate->save();
        });
    }


    protected $fillable = [
        "first_name",
        "last_name",
        "agent_id",
        "my_crm_id",
        "mobile",
        "email",
        "whatsapp",
        "address",
        "gender",
        "region_id",
        "dob",
        "follow_up_date",
        "country_id",
        "nationality_id",
        "passport",
        "passport_expiry",
        "phone",
        "company",
        "website",
        "status",
        "contact_type_id",
        "contact_source_id",
        "contact_status_id",
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
    // Primary Agent
    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, "agent_id");
    }


    public function properties(){
        return $this->hasMany(Property::class, 'owner_id');
    }

    public function activeProperties(){
        return $this->hasMany(Property::class, 'owner_id')->where('is_live', 1);
    }

    // public function contactTypes(): BelongsToMany
    // {
    //     return $this->belongsToMany(ContactType::class, ContactContactType::class);
    // }

    public function contactTypes(): BelongsToMany
    {
        return $this->belongsToMany(Type::class, 'contact_contact_types','contact_id', 'contact_type_id',);
    }

    public function contactSource(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }

    // If Later we need to morphmany this
    // public function calls(): MorphToMany
    // {
    //     return $this->morphToMany(CallLog::class, 'call_loggable');
    // }
    public function callLogs(): HasMany
    {
        return $this->hasMany(CallLog::class);
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            //->onQueue('logging')
            ->logOnlyDirty()
            ->logAll()
            ->dontLogIfAttributesChangedOnly(['updated_at'])
            ->setDescriptionForEvent(fn($eventName) => auth()?->user()?->name . " has ". $eventName . " contact.");
    }
    public function documents(): HasMany
    {
        return $this->hasMany(ContactMedia::class, "contact_id")->orderBy('order', 'ASC');
    }
    // Additional Agents of the Contact
    public function contactAgents(): HasMany
    {
        return $this->hasMany(ContactAgent::class);
    }
    public function contactMeta()
    {
        return $this->hasMany(ContactMeta::class);
    }

    public function agents(): BelongsToMany
    {
        return $this->belongsToMany(User::class, ContactAgent::class);
    }
    public function getDobAttribute($date)
    {
        return $date != null ? globalDate($date) : null;
    }
    public function updates()
    {
        return $this->morphMany(ModelUpdate::class, 'updatable','model_type', 'model_id');
    }

    public function dealMedia() {
        return $this->morphMany(DealMedia::class, "model");
    }
    
    public const GENDER_SELECT = [
        'Male'=>'Male',
        'Female'=>'Female',
    ];
}
