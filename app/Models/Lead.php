<?php

namespace App\Models;

use App\Traits\TelegramTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Jobs\UpdateLeadOnBitrix;
use App\Jobs\SyncLeadToDatahub;
class Lead extends Model
{
    use HasFactory, SoftDeletes, LogsActivity, TelegramTrait;

    protected $connection = 'mysql';

    protected static function boot()
    {
        parent::boot();

        static::deleted(function ($model) {
            $model->deleted_by_id = auth()->id();
            $model->save();
        });
        static::creating(function ($model) {
            $model->assigned_agent_at = now();
            if ($model->agent_id) {
                $user = User::find($model->agent_id);
                $model->region_id = $user->region_id ?? 1;
            }
            if ($model->lead_status_id == 1){
                $model->transfer_to_portal_lead_time = Carbon::now()->addHours(24);
            }

            if ($model->lead_status_id == 5){
                $model->in_progress_time = Carbon::now();
            }

            if ($model->lead_status_id == 8){
                $model->qualified_time = Carbon::now();
            }

            if ($model->lead_status_id == 30){
                $model->under_offer_time = Carbon::now();
            }
        });
        static::created(function ($model) {
            SyncLeadToDataHub::dispatch($model->id)->onQueue('datahub');
        });
        static::updating(function ($model) {
            $model->updated_by_id = auth()->id();
            $model->updated_at = now();
            if ($model->isDirty('agent_id')) {
                $model->warning_sent = false;
                $model->assigned_agent_at = now();
            }
            if ($model->agent_id) {
                $user = User::find($model->agent_id);
                $model->region_id = $user->region_id ?? 1;
            }
            if ($model->isDirty('lead_status_id')) {
                if ($model->lead_status_id == 1){
                    $model->transfer_to_portal_lead_time = Carbon::now()->addHours(24);
                    $model->portal_lead = false;
                    $model->portal_lead_transferred_date = null;
                }
                if ($model->lead_status_id == 5){
                    $model->in_progress_time = Carbon::now();
                    $model->transfer_to_portal_lead_time = null;
                }
                if ($model->lead_status_id == 8){
                    $model->qualified_time = Carbon::now();
                    $model->transfer_to_portal_lead_time = null;
                }
                if ($model->lead_status_id == 13){
                    $model->under_offer_time = Carbon::now();
                    $model->transfer_to_portal_lead_time = null;
                }
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

        static::updated(function ($model) {

            if ($model->bitrix_id) {
                $envoirnment = env('APP_ENV');
                if ($envoirnment == 'production') {
                    UpdateLeadOnBitrix::dispatch($model->id);
                }
            }
            SyncLeadToDatahub::dispatch($model->id)->onQueue('datahub');
        });
    }

    protected $fillable = [
        'mail_number',
        'subject',
        'message',
        "agent_id",
        'telesales_id',
        'telesales_head_id',
        "for",
        "first_name",
        "region_id",
        "last_name",
        "mobile",
        "phone",
        "whatsapp",
        "email",
        "nationality_id",
        "gender",
        "dob",
        "address",
        "company",
        "property_id",
        "portal_lead",
        "portal_lead_transferred_date",
        "transfer_to_portal_lead_time",
        "transfer_to_portal_lead_reminder_sent_at",
        "transfer_to_portal_lead_warning_sent_at",
        "taken_from_portal_lead",
        "taken_from_portal_lead_date",
        "lead_type_id",
        "lead_source_id",
        'source_id',
        'type_id',
        "lead_medium_id",
        "warning_sent",
        "lead_status_id",
        'status',
        'pf_tracking_link',
        "assigned_agent_at",
        // "lead_sub_status_id",
        "follow_up_date",
        "priority",
        "created_by_id",
        "updated_by_id",
        "created_at",
        "updated_at",
        'deleted_by_id',
        'converted_to_listing_lead',
        'assigned_by_id',
        'assigned_at',
        'portal_created_at',
        'previous_agent_id',
        'in_progress_time',
        'in_progress_reminder_sent_at',
        'in_progress_warning_sent_at',
        'qualified_time',
        'under_offer_time',
        'qualified_warning_sent_at',
        'under_offer_warning_sent_at',
    ];

    protected $casts = [
        "for" => "array",
    ];
    protected $appends = [
        'title',
        'is_active',
        'is_portal_lead_pool',
        'is_bitrix_lead_pool',
    ];

    public function getFullNameAttribute()
    {
        return $this->first_name . " " . $this->last_name;
    }

    public const PRIORITY_SELECT = [
        "Urgent",
        "High",
        "Normal",
        "Low",
    ];

    public const FOR_SELECT = [
        "Residential for sale" => "Residential for sale",
        "Residential for rent" => "Residential for rent",
        "Commercial for sale" => "Commercial for sale",
        "Commercial for rent" => "Commercial for rent",
        "Unspecified" => "Unspecified",
    ];
    public const LEAD_CATEGORY = [
        "Commercial" => "Commercial",
        "Abu Dhabi" => "Abu Dhabi",
        "Secondary" => "Secondary",
//        "Primary" => "Primary",
//        "Chinese" => "Chinese",
    ];

    public const FURNISHED_SELECT = [
        "FURNISHED" => "Furnished",
        "SEMI_FURNISHED" => "Semi Furnished",
        "UNFURNISHED" => "Unfurnished",
    ];
    public const GENDER_SELECT = [
        'Male' => 'Male',
        'Female' => 'Female',
    ];

    public function title(): Attribute
    {
        $title = $this->first_name . " " . $this->last_name . " - " . $this->mobile . " - " . $this->email;
        // return $path_name;
        return Attribute::make(
            get: fn($value) => $title,
        );
    }
    public function criteria(): HasOne
    {
        return $this->hasOne(LeadCriteria::class);
    }
    public function notes()
    {
        return $this->hasMany(LeadNote::class, 'lead_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
    public function leadSource(): BelongsTo
    {
        return $this->belongsTo(Source::class, 'source_id');
    }
    public function nationality()
    {
        return $this->belongsTo(Country::class, 'nationality_id');
    }

    public function leadMedium(): BelongsTo
    {
        return $this->belongsTo(Medium::class, 'lead_medium_id');
    }

    public function leadType(): BelongsTo
    {
        return $this->belongsTo(Type::class, 'type_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(LeadMedia::class, "lead_id")->orderBy('order', 'ASC');
    }

    public function previousAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'previous_agent_id');
    }

    public function telesales(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function leadStatus(): BelongsTo
    {
        return $this->belongsTo(LeadStatus::class);
    }
    public function leadMeta()
    {
        return $this->hasMany(LeadMeta::class);
    }
    public function leadSubStatus(): BelongsTo
    {
        return $this->belongsTo(LeadSubStatus::class);
    }

    public function leadAgents(): HasMany
    {
        return $this->hasMany(LeadAgent::class);
    }

    public function callLogs(): HasMany
    {
        return $this->hasMany(CallLog::class);
    }
    public function calls()
    {
        return $this->belongsToMany(CallLog::class, 'call_log_leads');
    }


    public function getCallLogsAttribute($value)
    {
        $oldCalls = $this->callLogs()->get();
        $newCalls = $this->calls()->get();
        return $newCalls->merge($oldCalls);
    }

    // public function calls(): MorphToMany
    // {
    //     return $this->morphToMany(CallLog::class, 'call_loggable');
    // }
    public function getOriginalMatchingChanges(): array
    {

        $dirty = !empty($this->getDirty()) ? $this->getDirty() : $this->getChanges();
        return collect($this->getOriginal())
            ->only(collect($dirty)->keys())
            ->toArray();
    }

    public function getActivitylogOptions(): LogOptions
    {

        return LogOptions::defaults()
            ->logOnlyDirty()
            ->logAll()
            ->dontLogIfAttributesChangedOnly(['updated_at'])
            ->setDescriptionForEvent(fn($eventName) => auth()?->user()?->name ?? '' . " has " . $eventName . " lead.");
    }

    public function agents(): BelongsToMany
    {
        return $this->belongsToMany(User::class, LeadAgent::class);
    }


    public function getMobileAttribute($value)
    {
        if (\Auth::check()) {
            if (empty($this->agent_id) && \Auth::user()->hasRole('Agent')) {
                return '***********';
            } else {
                return $value;
            }
        } else {
            return $value;
        }
    }

    public function getWhatsappAttribute($value)
    {
        if (\Auth::check()) {
            if (empty($this->agent_id) && \Auth::user()->hasRole('Agent')) {
                return '***********';
            } else {
                return $value;
            }
        } else {
            return $value;
        }
    }
    public function getPhoneAttribute($value)
    {
        if (\Auth::check()) {
            if (empty($this->agent_id) && \Auth::user()->hasRole('Agent')) {
                return '***********';
            } else {
                return $value;
            }
        } else {
            return $value;
        }
    }
    public function updates()
    {
        return $this->morphMany(ModelUpdate::class, 'updatable', 'model_type', 'model_id');
    }

    public function folders()
    {
        return $this->morphMany(Folder::class, "folderable");
    }

    public function dealMedia()
    {
        return $this->morphMany(DealMedia::class, "model");
    }

    public function shortlink()
    {
        return $this->hasOne(Shortlink::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function getIsActiveAttribute(): bool
    {
        if ((int)$this->is_archive === 1) {
            return false;
        }

        $converted = $this->getAttribute('converted_to_listing_lead');

        return $this->agent_id !== null && is_null($converted);
    }

    public function getIsPortalLeadPoolAttribute(): bool
    {
        if ((int)$this->is_archive === 1) {
            return false;
        }
        return (bool)($this->attributes['portal_lead'] ?? false);
    }

    public function getIsBitrixLeadPoolAttribute(): bool
    {
        if ((int)$this->is_archive === 1) {
            return false;
        }

        $portalLead = $this->getAttribute('portal_lead') ?? 0;
        $converted = $this->getAttribute('converted_to_listing_lead');
        $needDestroy = (int)($this->getAttribute('need_to_be_destroyed') ?? 0);

        return $this->agent_id === null
            && !$portalLead
            && is_null($converted)
            && $needDestroy === 0;
    }
}
