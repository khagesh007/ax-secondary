<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;

class ListingLead extends Model
{
    use HasFactory,SoftDeletes;
    protected  $connection = 'mysql';
    protected $casts = [
        "for" => "array",
    ];
    protected $appends=[
        'location',
    ];
    public const STATUS = [
        "New" => "New",
        "Qualified" => "Confirmed",
        "Approved" => "Approved",
    ];

    protected $fillable = [
        "first_name",
        "last_name",
        "email",
        "mobile",
        "title",
        "bed_id",
        "bath_id",
        "for",
        "unit_no",
        "type_id",
        "source_id",
        "medium_id",
        "location_id",
        "property_category_id",
        "telesales_id",
        "community_property_id",
        "community_id",
        "sub_community_id",
        "location_type",
        "recording_file",
        "note",
        "status_id",
        "contact_id",
        "property_id",
        "agent_id",
        "manager_id",
        "sale_id",
        "created_by_id",
        "updated_by_id",
        "deleted_by_id",
        'assigned_by_id',
    ];

    public static function boot()
    {
        parent::boot();
        static::created(function($model){
            // if($model->agent_id && $model->mobile){
            //     $callLogs = CallLog::whereRaw("REPLACE(client_number, ' ', '') LIKE ?", ['%' . substr($model->mobile, -8     ) . '%'])
            //             ->where('user_id', $model->agent_id)
            //             ->get();
            //     // Attach call logs to the lead
            //     $model->calls()->attach($callLogs);
            // }

        });
        static::updating(function ($model) {
            $modelUpdate = new ModelUpdate;
            $modelUpdate->model_type = get_class($model); // The class name of the Property model
            $modelUpdate->model_id = $model->id; // The ID of the Property record
            $modelUpdate->updated_at = now();
            $modelUpdate->updated_by_id = auth()->id() ?? null; // Assuming you have user authentication set up
            $modelUpdate->description = 'Updated ListingLead Details details';
            $modelUpdate->data = json_encode($model->getDirty()); // Capture the updated data
            $modelUpdate->save();
        });

        static::updated(function ($model) {
            if($model->agent_id && $model->mobile){
                // $callLogs = CallLog::whereRaw("REPLACE(client_number, ' ', '') LIKE ?", ['%' . substr($model->mobile, -8     ) . '%'])
                //         ->where('user_id', $model->agent_id)
                //         ->get();
                // // Attach call logs to the lead
                // $model->calls()->attach($callLogs);
            }
        });
    }

    public function status(){
        return $this->belongsTo(ListingLeadStatus::class);
    }
    public function propertyCategory(){
        return $this->belongsTo(PropertyCategory::class);
    }

    public function calls()
    {
        return $this->belongsToMany(CallLog::class, 'call_log_listing_leads');
    }

    public function location(): Attribute
    {
        $path_name = $this?->communityProperty?->path_name ?? $this?->subCommunity?->path_name ?? $this?->community?->path_name;
        // return $path_name;
        return Attribute::make(
            get:fn($value) => $path_name,
        );
    }
    public function communityProperty(): BelongsTo
    {
        return $this->belongsTo(CommunityProperty::class,'community_property_id','id');
    }

    public function getNoteAttribute($value)
    {
        //return $value;
        $value = str_replace(["<p>", "</p>", "<div>", "</div>"], '', $value);

        // Replace <br> tags with \n (newline)
        $value = preg_replace("/<br(\s*\/)?>/", "\n", $value);

        return $value;

    }
    public function subCommunity(): BelongsTo
    {
        return $this->belongsTo(SubCommunity::class,'sub_community_id','id');
    }
    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class,'community_id','id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, "updated_by_id");
    }
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, "created_by_id");
    }
    public function source(){
        return $this->belongsTo(Source::class);
    }
    public function medium(){
        return $this->belongsTo(Medium::class);
    }
    public function type(){
        return $this->belongsTo(Type::class);
    }
    public function bed(){
        return $this->belongsTo(Bed::class);
    }
    public function bath(){
        return $this->belongsTo(Bath::class);
    }
    public function agent(){
        return $this->belongsTo(User::class,'agent_id','id');
    }
    public function updates()
    {
        return $this->morphMany(ModelUpdate::class, 'updatable','model_type', 'model_id');
    }
}
