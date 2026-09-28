<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Deal extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;
    protected  $connection = 'mysql';
    protected $fillable = [
        "deal_type_id",
        "property_id",
        "contact_id",
        "deal_status_id",
        "deal_price",
        "type",
        "transaction_type",
        "initial_deposit",
        "gross_commission",
        "net_commission",
        "state",
        "status",
        "status_message",
        "created_by_id",
        "updated_by_id",
        "created_at",
        "updated_at",
    ];
    public const STATUS_SELECT = [
        "DRAFT" => "Draft",
        "PENDING" => "Pending"

    ];

    public const TRANSACTION_TYPE_SELECT = [
        // "Cash to Cash" => "Cash to Cash",
        // "Cash to Mortgage" => "Cash to Mortgage",
        // "Mortgage to Cash" => "Mortgage to Cash",
        // "Mortgage to Mortgage" => "Mortgage to Mortgage",
        // "Rental" => "Rental"

        "Cash Buyer to Cash Seller" => "Cash Buyer to Cash Seller",
        "Mortgage Buyer to Cash Seller" => "Mortgage Buyer to Cash Seller",
        "Gifting" => "Gifting",
        "Cash Buyer to Mortgage Seller" => "Cash Buyer to Mortgage Seller",
        "Mortgage Buyer to Mortgage Seller" => "Mortgage Buyer to Mortgage Seller",
        "Business Buyer and/or Seller" => "Business Buyer and/or Seller",
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function dealType(): BelongsTo
    {
        return $this->belongsTo(DealType::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function dealStatus(): BelongsTo
    {
        return $this->belongsTo(DealStatus::class);
    }
    public function dealMeta()
    {
        return $this->hasMany(DealMeta::class);
    }

    public function leads(){
        return $this->belongsToMany(Lead::class, 'deal_leads')->withPivot('lead_category', 'lead_type');
    }
    public function splits(){
        return $this->hasMany(DealSplit::class, 'deal_id');
    }
    public function media(): HasMany
    {
        return $this->hasMany(DealMedia::class, "deal_id")->orderBy('order', 'ASC');
    }

    public function commissionSources(): HasMany
    {
        return $this->hasMany(DealCommissionSource::class, "deal_id");
    }
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }
    public function deductibles()
    {
        return $this->hasMany(DealDeductible::class, 'deal_id');
    }
    public function progression()
    {
        return $this->hasOne(DealProgressionData::class, 'deal_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->logAll()
            ->dontLogIfAttributesChangedOnly(['updated_at'])
            ->setDescriptionForEvent(fn($eventName) => auth()?->user()?->name . " has ". $eventName . " deal.");
    }

    public function folders() {
        return $this->morphMany(Folder::class, "folderable");
    }

    public function dealMedia() {
        return $this->morphMany(DealMedia::class, "model");
    }
}
