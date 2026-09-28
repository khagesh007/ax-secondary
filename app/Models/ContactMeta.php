<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMeta extends Model
{
    use HasFactory;
    protected $table = 'contact_meta';
    protected  $connection = 'mysql';
    protected $fillable = [
        'contact_id',
        'meta_key',
        'meta_value'
    ];

    public static function getContactMeta($contactId, $metaKey)
    {
        $contactMeta = self::where('contact_id', $contactId)
            ->where('meta_key', $metaKey)
            ->first();

        return $contactMeta ? $contactMeta->meta_value : null;
    }

    public static function storeContactMeta($contactId, $metaKey, $metaValue)
    {
        $contactMeta = self::updateOrCreate(
            ['contact_id' => $contactId, 'meta_key' => $metaKey],
            ['meta_value' => $metaValue]
        );

        return $contactMeta ? true : false;
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
}
