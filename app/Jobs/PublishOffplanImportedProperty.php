<?php

namespace App\Jobs;

use App\Models\DeveloperProject;
use App\Models\OffplanProperty;
use App\Models\Property;
use App\Models\PropertyNote;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;

class PublishOffplanImportedProperty implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $project_id;
    protected $user_id;
    public $timeout = 17200;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($project_id, $user_id)
    {
        $this->project_id = $project_id;
        $this->user_id = $user_id;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        info('Publish Offplan Imported Property');
        $counter = 0;
        $reason = '';
        $total_properties = 0;
        $developer = DeveloperProject::where('id', $this->project_id)->first();
        $projectProperty = OffplanProperty::where('developer_id', $developer->developer_id)->where('project_name', $developer->project_name)->whereNotNull('property_id')->get();

        if ($projectProperty->count() > 0) {
            foreach ($projectProperty as $offplanProperty) {
                $total_properties++;
                $property = Property::where('id', $offplanProperty->property_id)->first();
                if ($property) {
                    if ($property->publish_status == 'DRAFT') {
                        if ($property->unit_no && $property->title && $property->description && $property->built_up_area) {
                            if ($property->price && $property->photos->count() > 0) {
                                $property->publish_status = 'PUBLISHED';
                                $property->is_live = 1;
                                $property->withoutApproval()->save();

                                $property_note = new PropertyNote();
                                $property_note->property_id = $property->id;
                                $property_note->note = 'Property published via Bulk Offplan Property Import.';
                                $property_note->created_by_id = $this->user_id;
                                $property_note->status = 1;
                                $property_note->save();

                                $counter++;
                            } else {
                                Log::channel('offplan')->info('Price or Photo is missing', ['property_id' => $property->id]);
                            }
                        } else {
                            Log::channel('offplan')->info('Unit No, Title, BUI or Description is missing', ['property_id' => $property->id]);

                        }

                    }

                }
            }

        }

        info('Publish Offplan Imported Property ' . $counter . ' out of ' . $total_properties . ' properties published');
    }

}
