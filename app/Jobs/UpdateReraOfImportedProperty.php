<?php

namespace App\Jobs;

use App\Models\DeveloperProject;
use App\Models\OffplanProperty;
use App\Models\Property;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\PropertyNote;

class UpdateReraOfImportedProperty implements ShouldQueue
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
        $counter = 0;
        $reason = '';
        $total_properties = 0;
        $developer = DeveloperProject::where('id', $this->project_id)->first();
        $projectProperty = OffplanProperty::where('developer_id', $developer->developer_id)->where('project_name', $developer->project_name)->whereNotNull('property_id')->get();
        info('Rera Number Updated of Imported Properties of ' . $developer->project_name);
        if ($developer->rera_permit != null) {
            if ($projectProperty->count() > 0) {
                foreach ($projectProperty as $offplanProperty) {
                    $total_properties++;
                    $property = Property::where('id', $offplanProperty->property_id)->first();
                    if ($property) {
                        if ($property->rera_permit_number == $developer->rera_permit) {
                            continue;
                        }
                        $old_rera_permit = $property->rera_permit_number;
                        $property->rera_permit_number = $developer->rera_permit;
                        $property->withoutApproval()->save();

                        $property_note = new PropertyNote();
                        $property_note->property_id = $property->id;
                        $property_note->note = 'Rera Permit Number Updated from ' . $old_rera_permit . ' to ' . $developer->rera_permit . ' on ' . date('Y-m-d H:i:s') . '';
                        $property_note->created_by_id = $this->user_id;
                        $property_note->status = 1;
                        $property_note->save();
                        $counter++;

                    }
                }

            }
            info('Rera Number updated of  ' . $counter . ' out of ' . $total_properties . ' properties.');
        } else {
            info('Rera Number not updated as Rera Number is not available for ' . $developer->project_name);
        }
    }

}
