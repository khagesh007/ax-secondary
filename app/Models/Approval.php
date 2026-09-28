<?php
namespace App\Models;

use Cjmellor\Approval\Enums\ApprovalStatus;
use Cjmellor\Approval\Scopes\ApprovalStateScope;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
//use App\Traits\CreatedUpdatedBy;
class Approval extends Model
{
   // use CreatedUpdatedBy;
    protected $guarded = [];

    protected $casts = [
        'new_data' => AsArrayObject::class,
        'original_data' => AsArrayObject::class,
        'state' => ApprovalStatus::class,
    ];

    public static function booted()
    {
        static::addGlobalScope(new ApprovalStateScope());


    }

    public function approvalable(): MorphTo
    {
        return $this->morphTo();
    }

}
