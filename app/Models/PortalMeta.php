<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class PortalMeta extends Model
{
    use HasFactory;
    protected $table = 'portal_meta';
    protected  $connection = 'mysql';
    protected $fillable = [
        'portal_id',
        'meta_key',
        'meta_value',
        'start_date',
        'end_date',
        'updated_at'
    ];

    public static function getPortalMeta($portalId, $metaKey, $start_date = null)
    {

       // if($start_date == null){
            $portalMeta = self::where('portal_id', $portalId)
            ->where('meta_key', $metaKey)
            ->orderBy('start_date', 'desc')
            ->first();
        // }else{
        //     $portalMeta = self::where('portal_id', $portalId)
        //     ->where('meta_key', $metaKey)
        //     ->where('start_date', $start_date)
        //     ->first();
        // }

        return $portalMeta ? $portalMeta->meta_value : null;
    }

    public static function storePortalMeta($portalId, $metaKey, $metaValue, $start_date = null, $end_date = null)
    {
        $date = date('Y-m-d');
        $start_date = $start_date ? $start_date : Carbon::parse($date)->startOfMonth()->format('Y-m-d');
        $end_date = $end_date ? $end_date : Carbon::parse($date)->endOfMonth()->format('Y-m-d');
        $portalMeta = self::updateOrCreate(
            ['portal_id' => $portalId, 'meta_key' => $metaKey,
            'start_date' => $start_date, 'end_date' => $end_date],
            ['meta_value' => $metaValue, 'updated_at' => now()]
        );

        return $portalMeta ? true : false;
    }


    public static function deletePortalMeta($portalId, $metaKey)
    {
        if (empty($portalId) || empty($metaKey)) {
            return 0; // Return 0 if either portal ID or meta key is empty
        }

        // Use the "where" condition to find the specific portal meta entry
        $deleted = self::where('portal_id', $portalId)
            ->where('meta_key', $metaKey)
            ->delete();

        return $deleted > 0 ? 1 : 0; // Return 1 if at least one entry was deleted, otherwise 0
    }

    public static function deletePortalMetaBatch($portalId, $metaKeys)
    {
        if (empty($portalId) || empty($metaKeys) || !is_array($metaKeys)) {
            return 0; // Return 0 if portal ID is empty, meta keys array is empty, or metaKeys is not an array
        }

        // Use the "whereIn" condition to find and delete portal meta entries with the given portal ID and meta keys
        $deleted = self::where('portal_id', $portalId)
            ->whereIn('meta_key', $metaKeys)
            ->delete();

        return $deleted > 0 ? 1 : 0; // Return 1 if at least one entry was deleted, otherwise 0
    }

    public function portal()
    {
        return $this->belongsTo(Portal::class);
    }
}
