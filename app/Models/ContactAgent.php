<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactAgent extends Model
{
    use HasFactory;
    protected  $connection = 'mysql';
    protected $fillable = [
        "contact_id",
        "user_id",
        "created_by_id",
        "updated_by_id",
        "created_at",
        "updated_at",
    ];
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if($model->user_id && $model->contact_id){
                $user = User::find($model->user_id);
                $contact_id = Contact::where('id',$model->contact_id)->update(['region_id' => $user?->region_id]);

                // $contact = Contact::where('id', $model->contact_id)->first();

                // if($contact?->mobile){
                //     $callLogsUpdated = CallLog::whereRaw("REPLACE(client_number, ' ', '') LIKE ?", ['%' . substr($contact->mobile, -8) . '%'])
                //         ->where('user_id', $model->user_id)
                //         ->whereNull('contact_id')
                //         ->update(['contact_id' => $model->contact_id]);
                // }
                
            }
        });
        static::updating(function ($model) {
            $model->updated_by_id = auth()->id();
            $model->updated_at = now();
            if($model->user_id && $model->contact_id){
                $user = User::find($model->user_id);
                $contact_id = Contact::where('id',$model->contact_id)->update(['region_id' => $user?->region_id]);
            }
        });
    }
    public function agent()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id')->select('id', 'name');
    }

    public function updated_by()
    {
        return $this->belongsTo(User::class, 'updated_by_id')->select('id', 'name');
    }
}
