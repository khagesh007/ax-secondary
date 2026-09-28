<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadMeta extends Model
{
    use HasFactory;
    protected $table = 'lead_meta';
    protected  $connection = 'mysql';
    protected $fillable = [
        'lead_id',
        'meta_key',
        'meta_value'
    ];

    public static function getLeadMeta($leadId, $metaKey)
    {
        $leadMeta = self::where('lead_id', $leadId)
            ->where('meta_key', $metaKey)
            ->first();

        return $leadMeta ? $leadMeta->meta_value : null;
    }

    public static function storeLeadMeta($leadId, $metaKey, $metaValue)
    {
        $leadMeta = self::updateOrCreate(
            ['lead_id' => $leadId, 'meta_key' => $metaKey],
            ['meta_value' => $metaValue]
        );

        return $leadMeta ? true : false;
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}
