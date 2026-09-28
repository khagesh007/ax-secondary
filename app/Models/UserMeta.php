<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMeta extends Model
{
    use HasFactory;
    protected $table = 'user_meta';
    protected  $connection = 'mysql';
    protected $fillable = [
        'user_id',
        'meta_key',
        'meta_value',
        'tag',
    ];

    public static function getUserMeta($userId, $metaKey)
    {
        $userMeta = self::where('user_id', $userId)
            ->where('meta_key', $metaKey)
            ->first();

        return $userMeta ? $userMeta->meta_value : null;
    }

    public static function storeUserMeta($userId, $metaKey, $metaValue)
    {
        $userMeta = self::updateOrCreate(
            ['user_id' => $userId, 'meta_key' => $metaKey],
            ['meta_value' => $metaValue, 'tag' => 'portal']
        );

        return $userMeta ? true : false;
    }

    public static function deleteUserMeta($userId, $metaKey)
    {
        if (empty($userId) || empty($metaKey)) {
            return 0; // Return 0 if either user ID or meta key is empty
        }

        // Use the "where" condition to find the specific user meta entry
        $deleted = self::where('user_id', $userId)
            ->where('meta_key', $metaKey)
            ->delete();

        return $deleted > 0 ? 1 : 0; // Return 1 if at least one entry was deleted, otherwise 0
    }

    public static function deleteUserMetaBatch($userId, $metaKeys)
    {
        if (empty($userId) || empty($metaKeys) || !is_array($metaKeys)) {
            return 0; // Return 0 if user ID is empty, meta keys array is empty, or metaKeys is not an array
        }

        // Use the "whereIn" condition to find and delete user meta entries with the given user ID and meta keys
        $deleted = self::where('user_id', $userId)
            ->whereIn('meta_key', $metaKeys)
            ->delete();

        return $deleted > 0 ? 1 : 0; // Return 1 if at least one entry was deleted, otherwise 0
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public const LANGUAGES = [
        "en" => "English",
        "ar" => "Arabic",
        "Chinese" => "Chinese",
        "French" => "French",
        "German" => "German",
        "Hindi" => "Hindi",


    ];

}
