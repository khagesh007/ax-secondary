<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Lab404\Impersonate\Models\Impersonate;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Events\UserUpdated;
use App\Jobs\SyncUserToDatahub;

// use Spatie\Activitylog\LogOptions;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles;
    // use LogsActivity;
    use Impersonate;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $connection = 'mysql';
    protected $fillable = [
        "parent_id",
        "name",
        'bitrix_role',
        "first_name",
        "last_name",
        "email",
        "secondary_email",
        "bitrix_id",
        "email_verified_at",
        "mobile",
        "password",
        "region_id",
        "telegram_username",
        "telegram_chatid",
        "website_link",
        "facebook_link",
        "instagram_link",
        'is_report_exempt',
        "youtube_link",
        "linkedin_link",
        "bio",
        'last_deal_date',
        'joining_date',
        "license_no",
        "pp_enabled",
        "pp_name",
        "pp_photo",
        "pp_license_no",
        "slug",
        "country_id",
        "position",
        "state_id",
        "nationality_id",
        "community_id",
        "thumbnail",
        "image",
        "avatar",
        "color_code",
        "status",
        "call_tracking",
        "call_lead",
        "is_commercial",
        "api_access",
        "created_by_id",
        "updated_by_id",
        "created_at",
        "updated_at",
        'device_identifier',
        'port',
        'entity',
        'zone_id',
        'last_login',
        'listing_allowed',
        'temporary_disable',
        'telelog_entity',
    ];

    // public function getActivitylogOptions(): LogOptions
    // {
    //     return LogOptions::defaults();
    // }

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->port = 5505;
            $model->device_identifier = $model->id;
            $model->traccar_status = 1;
            $model->mic_status = 0;
        });
        static::created(function ($model) {
            // Add spatie activity log and save all the information when a user is created and who created this
            $modelUpdate = new ModelUpdate;
            $modelUpdate->model_type = get_class($model); // The class name of the Property model
            $modelUpdate->model_id = $model->id; // The ID of the Property record
            $modelUpdate->updated_at = now();
            $modelUpdate->updated_by_id = auth()->id() ?? null; // Assuming you have user authentication set up
            $modelUpdate->description = 'Created User';
            $modelUpdate->data = json_encode($model->getDirty()); // Capture the updated data
            $modelUpdate->save();
            $model->updated_at = now();
            $model->updated_by_id = auth()->id() ?? null;

            SyncUserToDatahub::dispatch($model->id)->onQueue('datahub');

        });

        static::updating(function ($model) {
            $fieldsToCheck = ['google2fa_secret', 'telegram2fa_secret', 'back_up_google2fa_secret','login_attempt'];
            if (count(array_intersect(array_keys($model->getDirty()), $fieldsToCheck)) > 0) {
                $model->updated_at = $model->getOriginal('updated_at');
                $model->updated_by_id = $model->getOriginal('updated_by_id');
            } else {
                $data = collect($model->getDirty())->map(function ($item) {
                    return is_object($item) && method_exists($item, '__toString')
                        ? (string) $item
                        : $item;
                });



                $modelUpdate = new ModelUpdate;
                $modelUpdate->model_type = get_class($model); // The class name of the Property model
                $modelUpdate->model_id = $model->id; // The ID of the Property record
                $modelUpdate->updated_at = now();
                $modelUpdate->updated_by_id = auth()->id() ?? null; // Assuming you have user authentication set up
                $modelUpdate->description = 'Updated User details';
                $modelUpdate->data = json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE); // Capture the updated data
                $modelUpdate->save();
                $model->updated_at = now();
                $model->updated_by_id = auth()->id() ?? null;
                $model->device_identifier = $model->id;
                if(env('APP_ENV')=='production' && ($model->isDirty('password')) || $model->isDirty('status')){
                    event(new UserUpdated($model));
                }


                if ($model->isDirty('status') && $model->status == 0) {
                    $model->tokens()->delete();
                    getUserLoggedOut($model->id);
                }

            }
        });
        static::updated(function ($model) {
            SyncUserToDatahub::dispatch($model->id)->onQueue('datahub');
        });
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Interact with the user's first name.
     *
     * @param  string  $value
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function google2faSecret(): Attribute
    {
        return new Attribute(
            get: fn($value) => decrypt($value),
            set: fn($value) => encrypt($value),
        );
    }

    protected $appends = [
        "full_name",
        "thumb_url",
        "image_url",
        "role_list",
    ];
    public $relationships = ['children' => 'children', 'portals' => 'portals', 'leads' => 'leads'];

    public function getFullNameAttribute()
    {
        return $this->first_name . " " . $this->last_name;
    }

    public function leads()
    {
        return $this->hasMany(Lead::class, 'agent_id');
    }
    public function dealApprovals()
    {
        return $this->hasMany(DealApproval::class, 'user_id');
    }
    public function listingLeads()
    {
        return $this->hasMany(ListingLead::class, 'agent_id');
    }
    public function listings()
    {
        return $this->hasMany(Property::class, 'agent_id');
    }
    public function getThumbUrlAttribute()
    {
        return $this->thumbnail ?? "https://ui-avatars.com/api/?name=" . urlencode($this->name) . "&color=7F9CF5&background=EBF4FF";
    }
    public function getImageUrlAttribute()
    {
        return $this->image ?? "https://ui-avatars.com/api/?name=" . urlencode($this->name) . "&color=d59a7f&background=000000&border-color=d59a7f&border=1px";
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, "created_by_id");
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, "updated_by_id");
    }

    public function portals(): HasMany
    {
        return $this->hasMany(PortalUser::class);
    }

    public function getTelegramChatidAttribute($value)
    {
        $environment = \App::environment();
        if ($environment == "production" || $value == env('TEST_TELEGRAM_CHATID')) {
            return $value;
        }
        // return "6085218419"; //saim's chat id
       //  return "795165330";//khagesh's chat id
        return env('TEST_TELEGRAM_CHATID') ?? null; //Fraz's new chat id
    }
    public function getOriginalMatchingChanges(): array
    {

        $dirty = !empty($this->getDirty()) ? $this->getDirty() : $this->getChanges();
        return collect($this->getOriginal())
            ->only(collect($dirty)->keys())
            ->toArray();
    }
    public function subCommunities(): HasMany
    {
        return $this->hasMany(SubCommunityUser::class);
    }

    public function commissionSlab(): HasMany
    {
        return $this->hasMany(CommissionSlab::class);
    }

    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id', 'id');
    }

    public static function tree()
    {
        $allUsers = User::get();
        $rootUsers = $allUsers->whereNull("parent_id");

        self::formatTree($rootUsers, $allUsers);
        return $rootUsers;
    }

    private static function formatTree($users, $allUsers)
    {
        foreach ($users as $user) {
            $user->children = $allUsers->where("parent_id", $user->id)->values();
            if ($user->children->isNotEmpty()) {
                self::formatTree($user->children, $allUsers);
            }
        }
    }

    public function children(): HasMany
    {
        return $this->hasMany(User::class, "parent_id");
    }
    public function activechildren(): HasMany
    {
        return $this->hasMany(User::class, "parent_id")->where('status', 1);
    }
    public function getAllocatedPortalsAttribute()
    {
        $portals = PortalUser::where("user_id", $this->id)->has("portal")->with("portal")->get();

        $data = [];
        foreach ($portals as $portal) {
            $data[] = [
                "id" => $portal->portal_id,
                "name" => $portal->portal->name ?? "",
                "thumbnail" => $portal->portal->thumbnail ?? null,
                "quota" => $portal->portal->quota ?? 0,
                "allocation" => $portal->allocation ?? 0,
                "used" => PropertyPortal::where("portal_id", $portal->portal_id)->whereHas("property",
                    fn($q) => $q->where("agent_id", $this->id))->count(),
            ];
        }
        return $data;
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, "parent_id");
    }

    public function childrencount()
    {
        return $this->hasMany(User::class, "parent_id")->count();
    }
    public function getParentHierarchy()
    {
        $parents = collect([$this]);

        if ($this->parent) {
            $parents = $parents->concat($this->parent->getParentHierarchy());
        }

        return $parents;
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function hasChildren()
    {
        return $this->hasMany(User::class, 'parent_id')->exists();
    }

    public function userMeta()
    {
        return $this->hasMany(UserMeta::class);
    }
    public function suspensions()
    {
        return $this->hasMany(UserMeta::class)->where('tag', 'lead_inactivity_deactivate');
    }
    public function warnings()
    {
        return $this->hasMany(UserMeta::class)->where('tag', 'lead_inactivity_warning');
    }

    public function getHasChildrenAttribute()
    {
        return $this->hasChildren();
    }
    public function events()
    {
        return $this->hasMany(Event::class, 'agent_id');
    }
    public function locations()
    {
        return $this->hasMany(UserLocation::class, 'user_id', 'id');
    }

    public function criteriaLocation()
    {
        return $this->hasMany(UserLocation::class, 'parentable');
    }

    // function subCommunities(){
    //     return $this->morphedByMany(SubCommunity::class, 'parentable');
    // }

    public function languages()
    {
        return $this->hasMany(UserMeta::class, 'user_id', 'id')->where('tag', 'language');
    }

    public function devices()
    {
        return $this->hasMany(UserDevice::class, 'user_id', 'id');
    }
    public function authtoken()
    {
        return $this->hasOne(AuthToken::class, 'user_id', 'id');
    }
    public function googleauthtoken()
    {
        return $this->hasOne(UserOAuthToken::class, 'user_id', 'id');
    }
    public function updates()
    {
        return $this->morphMany(ModelUpdate::class, 'updatable', 'model_type', 'model_id');
    }

    public function getRoleListAttribute()
    {
        return $this->roles()->pluck('name')->all();
    }

    public function kpis()
    {
        return $this->hasMany(Kpi::class, 'agent_id');
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function publicProfile(): BelongsTo
    {
        return $this->belongsTo(User::class, 'public_profile_id', 'id');
    }

}
