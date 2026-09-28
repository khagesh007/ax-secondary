<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyMeta extends Model
{
    use HasFactory;
    protected $table = 'property_meta';
    protected  $connection = 'mysql';
    protected $fillable = [
        'property_id',
        'meta_key',
        'meta_value'
    ];

    public static function getPropertyMeta($propertyId, $metaKey)
    {
        $propertyMeta = self::where('property_id', $propertyId)
            ->where('meta_key', $metaKey)
            ->first();

        return $propertyMeta ? $propertyMeta->meta_value : null;
    }

    public static function storePropertyMeta($propertyId, $metaKey, $metaValue)
    {
        $propertyMeta = self::updateOrCreate(
            ['property_id' => $propertyId, 'meta_key' => $metaKey],
            ['meta_value' => $metaValue]
        );

        return $propertyMeta ? true : false;
    }

    public static function deletePropertyMeta($propertyId, $metaKey)
    {
        if (empty($propertyId) || empty($metaKey)) {
            return 0; // Return 0 if either property ID or meta key is empty
        }

        // Use the "where" condition to find the specific property meta entry
        $deleted = self::where('property_id', $propertyId)
            ->where('meta_key', $metaKey)
            ->delete();

        return $deleted > 0 ? 1 : 0; // Return 1 if at least one entry was deleted, otherwise 0
    }

    public static function deletePropertyMetaBatch($propertyId, $metaKeys)
    {
        if (empty($propertyId) || empty($metaKeys) || !is_array($metaKeys)) {
            return 0; // Return 0 if property ID is empty, meta keys array is empty, or metaKeys is not an array
        }

        // Use the "whereIn" condition to find and delete property meta entries with the given property ID and meta keys
        $deleted = self::where('property_id', $propertyId)
            ->whereIn('meta_key', $metaKeys)
            ->delete();

        return $deleted > 0 ? 1 : 0; // Return 1 if at least one entry was deleted, otherwise 0
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
