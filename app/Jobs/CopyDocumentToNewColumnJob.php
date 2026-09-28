<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\PropertyMedia;
use App\Models\Property;
use Log;
use App\Traits\FileTrait;
class CopyDocumentToNewColumnJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $property;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($property)
    {
        $this->property = $property;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('Copying image with watermark: '.$this->property->ref_no.'-------------------');
        try{
        $property = $this->property;
        $documents = PropertyMedia::withTrashed()->where('type','!=','Photos')->where('property_id', $property->id)->get();
        foreach ($documents as $doc) {
                $doc->with_watermark = $doc['path'];
                $doc->save();
        }
        }catch(\Exception $e){
            \Log::error($e->getMessage());
        }

    }
}
