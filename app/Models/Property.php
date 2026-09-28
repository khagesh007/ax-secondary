<?php

namespace App\Models;

use Cjmellor\Approval\Concerns\MustBeApproved;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\LogOptions;
use DB;
class Property extends Model
{
    use HasFactory, SoftDeletes, MustBeApproved;

    protected $connection = 'mysql';
    protected static function boot()
    {
        parent::boot();

        static::deleted(function ($model) {
            $model->deleted_by_id = auth()->id();
            $model->save();
        });
        static::created(function ($model) {
            PropertyCycle::create([
                "property_id" => $model->id,
                "status" => $model->publish_status,
                "ref_no" => $model->ref_no,
                "created_by_id" => auth()?->id() ?  auth()->id() : $model->created_by_id,
            ]);
        });
        static::creating(function ($model) {
            if ($model->community_id) {
                $community = Community::find($model->community_id);
                $model->region_id = $community->region_id;
            }
        });
        static::updating(function ($model) {
            $model->updated_by_id = auth()->id();
            $model->updated_at = now();
            if ($model->isDirty('publish_status')) {
                PropertyCycle::create([
                    "property_id" => $model->id,
                    "status" => $model->publish_status,
                    "ref_no" => $model->ref_no,
                    "created_by_id" => auth()->id(),
                ]);
            }
            $modelUpdate = new ModelUpdate;
            $modelUpdate->model_type = get_class($model); // The class name of the Property model
            $modelUpdate->model_id = $model->id; // The ID of the Property record
            $modelUpdate->updated_at = now();
            $modelUpdate->updated_by_id = auth()->id() ?? null; // Assuming you have user authentication set up
            $modelUpdate->description = 'Updated property details';
            $modelUpdate->data = json_encode($model->getDirty()); // Capture the updated data
            $modelUpdate->save();

            // if ($model->isDirty('publish_status') && $model->publish_status == 'PUBLISHED') {
            //     $model->published_date = now();
            // }

        });

        // static::updated(function ($model) {
        //     if($model->is_live == 1 && (in_array($model->publish_status, ["UNPUBLISHED" ,"POCKET" ,"ARCHIVED","DRAFT"] ))){
        //         $model->withoutApproval()->update(['is_live' => false]);
        //     }
        // });

    }

    protected $fillable = [
        "ref_no",
        "title",
        "description",
        "listing_source",
        "for",
        "region_id",
        "community_id",
        "sub_community_id",
        "community_property_id",
        "property_category_id",
        "bed_id",
        "bath_id",
        "unit_no",
        "built_up_area",
        "plot_size",
        "street",
        "floor",
        "view",
        "villa_type",
        "parking",
        "dewa_number",
        "rera_permit_number",
        "property_type_id",
        "property_status_id",
        "completion_status",
        "rented_at",
        "rented_until",
        "next_available",
        "availability_date",
        "remind",
        "furnished",
        "featured",
        "exclusive",
        "hot",
        "publish_status",
        "agent_id",
        'external_agent_id',
        "state",
        "is_live",
        "request_status_type",
        "password",
        "owner_id",
        "developer_id",
        "cheques",
        "created_by_id",
        "updated_by_id",
        "mycrm_updated",
        "video_link",
        "video_360_link",
        "qr_link",
        "title_deed_text",
        "state",
        "exclusive_start",
        "exclusive_end",
        'contract_start',
        'contract_end',
        "created_at",
        "updated_at",
        'published_date',
        'unpublished_date',
        'offplan_portal_fetch',
        'build_year',
        'down_payment_price',
        'payment_method',
        'external_agent_id',
        'contract_number',
        'refered_by',
        'offplan_dld_wavier',
        'offplan_original_price',
        'offplan_amount_paid',
        'portal_ids',
        'entity_company_id'
    ];

    protected $appends = ['first_image', 'category', 'location', 'my_property'];

    public const PUBLISH_STATUS_SELECT = [
        "DRAFT" => "Draft",
        "PENDING" => "Pending",
        "REFUSED" => "Refused",
        "POCKET" => "Pocket",
        "PUBLISHED" => "Published",
        "UNPUBLISHED" => "Unpublished",
        "ARCHIVED" => "Archived",
    ];

    public const PUBLISH_REQUEST_SELECT = [
        'UNPUBLISHED' => 'UNPUBLISHED',
        'POCKET' => 'POCKET',
        'PUBLISHED' => 'PUBLISHED',
    ];

    public const PROPERTY_STATE_SELECT = [
        'PENDING' => 'Pending',
        'APPROVED' => 'Approved',
        'REFUSED' => 'Refused',
    ];

    public const VIEWS = [
        'Beach View' => 'Beach View',
        'Burj Al Arab View' => 'Burj Al Arab View',
        'Burj Khalifa View' => 'Burj Khalifa View',
        'City View' => 'City View',
        'Community View' => 'Community View',
        'Countryside View' => 'Countryside View',
        'Creek View' => 'Creek View',
        'Canal View' => 'Canal View',
        'DIFC View' => 'DIFC View',
        'Dubai Eye View' => 'Dubai Eye View',
        'Dubai Fountain View' => 'Dubai Fountain View',
        'Downtown Dubai View' => 'Downtown Dubai View',
        'Garden View' => 'Garden View',
        'Golf Course View' => 'Golf Course View',
        'Island View' => 'Island View',
        'Lake View' => 'Lake View',
        'Marina View' => 'Marina View',
        'Palm Jumeirah View' => 'Palm Jumeirah View',
        'Park View' => 'Park View',
        'Pool View' => 'Pool View',
        'Sea View' => 'Sea View',
        'Skyline View' => 'Skyline View',
        'Sunrise View' => 'Sunrise View',
        'Sunset View' => 'Sunset View',
        'Waterfront View' => 'Waterfront View',
    ];
    public const VILLA_TYPE = [
        "SR" => "Single row",
        "BB" => "Back to back",
        "CP" => "Corner Plot",
    ];

    public const FOR_SELECT = [
        "Residential for sale" => "Residential for sale",
        "Residential for rent" => "Residential for rent",
        "Commercial for sale" => "Commercial for sale",
        "Commercial for rent" => "Commercial for rent",
    ];

    public const REMIND_SELECT = [
        "" => "Never",
        "1" => "1 Day",
        "7" => "1 Week",
        "14" => "2 Weeks",
        "70" => "10 Weeks",
        "30" => "1 Month",
        "60" => "2 Months",
        "90" => "3 Months",
        "120" => "4 Months",
        "160" => "6 Months",
    ];

    public const COMPLETION_STATUS_SELECT = [
        "Ready Primary" => "Ready Primary",
        "Ready Secondary" => "Ready Secondary",
        "Off-Plan Primary" => "Off-Plan Primary",
        "Off-Plan Secondary" => "Off-Plan Secondary",
    ];

    public const FURNISHED_SELECT = [
        "FURNISHED" => "Furnished",
        "SEMI_FURNISHED" => "Semi Furnished",
        "UNFURNISHED" => "Unfurnished",
    ];

    public const PERMIT_TYPE_SELECT = [
        "VALID" => "Valid",
        "EXPIRED" => "Expired",
        "EMPTY" => "Empty",
        "NOT_EMPTY" => "Not Empty",
    ];
    public const IS_EXCLUSIVE = [
        "YES" => "Yes",
        "NO" => "No"
    ];

    protected $casts = [
        'contract_start' => 'date:Y-m-d',
        'contract_end' => 'date:Y-m-d',
        'exclusive_start' => 'date:Y-m-d',
        'exclusive_end' => 'date:Y-m-d',
    ];

    public function details(): HasOne
    {
        return $this->hasOne(PropertyDetail::class);
    }

    public function developer(): BelongsTo
    {
        return $this->belongsTo(Developer::class);
    }

    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class);
    }
    public function getUnitNoAttribute($value)
    {
        if (\Auth::check()) {
            if((auth()->user()->region_id == 2 || auth()->user()->parent_id == 699) && auth()->user()->hasRole('Agent')){
                return "";
            }
            $notAdmin = auth()->user()->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent", "Telesales", "Telesales Head"]);
            $childrenIds = [];
            if ($notAdmin) {
                $childrenIds = getUserChildrenIds(auth()->id());
            }

            if (in_array($this->agent_id, $childrenIds) || auth()->user()->hasRole(["Admin", "Super Admin", "Head of Secondary"])) {
                return $value;
            } elseif (auth()->user()->hasRole("Admin - Abu Dhabi") && $this->region_id == 2) {
                return $value;
            } else {
                return "-";
            }
        } else {
            return $value;
        }

    }

    public function getContractStartAttribute($value)
    {
        if ($value) {
            return Carbon::parse($value)->format('d-m-Y');
        }
        return $value;
    }
    public function getContractEndAttribute($value)
    {
        if ($value) {
            return Carbon::parse($value)->format('d-m-Y');
        }
        return $value;
    }
    public function getMyPropertyAttribute()
    {
        if (\Auth::check()) {
            $notAdmin = auth()->user()->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent", "Telesales", "Telesales Head"]);
            $childrenIds = [];
            if ($notAdmin) {
                $childrenIds = getUserChildrenIds(auth()->id());
            }
            if (in_array($this->agent_id, $childrenIds) || auth()->user()->hasRole(["Admin", "Super Admin", "Head of Secondary", "Admin - Abu Dhabi"])) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }

    }
    public function getTitleDeedTextAttribute($value)
    {
        if (\Auth::check()) {
            $notAdmin = auth()->user()->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent", "Telesales", "Telesales Head"]);
            $childrenIds = [];
            if ($notAdmin) {
                $childrenIds = getUserChildrenIds(auth()->id());
            }
            if (in_array($this->agent_id, $childrenIds) || auth()->user()->hasRole(["Admin", "Super Admin", "Head of Secondary", "Admin - Abu Dhabi"])) {
                return $value;
            } else {
                return "-";
            }
        } else {
            return $value;
        }

    }
    public function subCommunity(): BelongsTo
    {
        return $this->belongsTo(SubCommunity::class);
    }
    protected function exclusiveStart(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value,
            set: fn($value) => $value == "" ? null : $value,
        );
    }
    public function communityProperty(): BelongsTo
    {
        return $this->belongsTo(CommunityProperty::class);
    }

    public function propertyCategory(): BelongsTo
    {
        return $this->belongsTo(PropertyCategory::class);
    }
    public function category(): Attribute
    {
        $category = $this->propertyCategory?->title;
        return Attribute::make(
            get: fn($value) => $category,
        );
    }

    public function bed(): BelongsTo
    {
        return $this->belongsTo(Bed::class);
    }

    public function bath(): BelongsTo
    {
        return $this->belongsTo(Bath::class);
    }

    public function propertyType(): BelongsTo
    {
        return $this->belongsTo(PropertyType::class);
    }

    public function propertyStatus(): BelongsTo
    {
        return $this->belongsTo(PropertyStatus::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, "agent_id");
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Contact::class, "owner_id");
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, "created_by_id");
    }
    public function location(): Attribute
    {
        $path_name = $this->communityProperty?->path_name ?? $this->subCommunity?->path_name ?? $this->community?->path_name;
        // return $path_name;
        return Attribute::make(
            get: fn($value) => $path_name,
        );
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, "updated_by_id");
    }

    public function propertyMeta()
    {
        return $this->hasMany(PropertyMeta::class);
    }

    public function propertycycles()
    {
        return $this->hasMany(PropertyCycle::class);
    }

    // public function firstpublish()
    // {
    //     return $this->hasOne(PropertyCycle::class)->where('status', 'PUBLISHED')->orderBy('created_at', 'asc');

    // }

    public function firstpublish()
    {
        // make logic that if it has many published then get the first one

        return $this->hasMany(PropertyCycle::class)
            ->where('status', 'PUBLISHED')
            ->orderBy('created_at', 'asc')
            ->limit(1);
    }
    public function firstunpublish()
    {
        return $this->hasMany(PropertyCycle::class)
            ->where('status', 'UNPUBLISHED')
            ->orderBy('created_at', 'asc')
            ->limit(1);
    }

    public function lastpublish()
    {
        return $this->hasMany(PropertyCycle::class)
            ->where('status', 'PUBLISHED')
            ->orderBy('created_at', 'desc')
            ->limit(1);
    }



    public function lastUnpublished()
{
    return $this->hasMany(PropertyCycle::class)
        ->where('status', 'PUBLISHED')
        ->orderBy('created_at', 'desc')
        ->limit(1)
        ->whereHas('property', function ($query) {
            $query->where('status', 'UNPUBLISHED');
        });
}

    public function portals(): HasMany
    {
        return $this->hasMany(PropertyPortal::class);
    }

    public function amenities(): HasMany
    {
        return $this->hasMany(PropertyFeatures::class, "property_id", "id");
    }
    public function feature(): HasManyThrough
    {
        return $this->hasManyThrough(PropertyAmenity::class, PropertyFeatures::class, "property_id", "id");
    }

    public function price(): HasOne
    {
        return $this->hasOne(PropertyPrice::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(PropertyMedia::class, "property_id")->where("type", "Photos")->orderBy('order', 'ASC');
    }

    public function getFirstImageAttribute()
    {
        return $this->photos()->first();
    }

    public function floorPlan(): HasOne
    {
        return $this->hasOne(PropertyMedia::class, "property_id")->where("type", "Floor Plan");
    }

    public function titleDeed(): HasOne
    {
        return $this->hasOne(PropertyMedia::class, "property_id")
            ->where("type", "Other")->where("title", "Title Deed");
    }

    public function ownerPassportCopy(): HasOne
    {
        return $this->hasOne(PropertyMedia::class, "property_id")
            ->where("type", "Other")->where("title", "Owner Passport Copy");
    }
    public $relationships = ['details' => 'details',
        'portals' => 'portals',
        'amenities' => 'amenities',
        'feature' => 'feature',
        'price' => 'price',
        'photos' => 'photos',
        'floorPlan' => 'floorPlan',
        'titleDeed' => 'titleDeed',
        'ownerPassportCopy' => 'ownerPassportCopy',
        'signedListingForm' => 'signedListingForm',
        'deals' => 'deals',
        'leads' => 'leads',
        'agents' => 'agents'];

    public function signedListingForm(): HasOne
    {
        return $this->hasOne(PropertyMedia::class, "property_id")
            ->where("type", "Other")->where("title", "Signed Listing Form");
    }

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }

    public function republish_request(): HasMany
    {
        return $this->hasMany(RepublishRequest::class)->where('status', 'REPUBLISH_REQUESTED');
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function getActivitylogOptions(): LogOptions
    {

        return LogOptions::defaults()
            ->logOnlyDirty()
            ->logAll()
            ->dontLogIfAttributesChangedOnly(['updated_at'])
            ->setDescriptionForEvent(fn($eventName) => (auth()?->user()?->name ?? null) . " has " . $eventName . " listing #" . $this->ref_no . ".");
    }

    public function agents(): BelongsToMany
    {
        return $this->belongsToMany(User::class, PropertyAgent::class);
    }

    public function otherDocuments(): HasMany
    {
        return $this->hasMany(PropertyMedia::class, "property_id")
            ->where("type", "Other")
            ->wherenotIn("title", ["Title Deed", "Owner Passport Copy", "Signed Listing Form"]);
    }
    public function getOriginalMatchingChanges(): array
    {

        $dirty = !empty($this->getDirty()) ? $this->getDirty() : $this->getChanges();
        return collect($this->getOriginal())
            ->only(collect($dirty)->keys())
            ->toArray();
    }
    public function updates()
    {
        return $this->morphMany(ModelUpdate::class, 'updatable', 'model_type', 'model_id');
    }

    public function folders()
    {
        return $this->morphMany(Folder::class, "folderable");
    }

    public function listingVerifications(): HasMany
    {
        return $this->hasMany(ListingVerification::class);
    }
}
