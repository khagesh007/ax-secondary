<?php

namespace App\Jobs;

use App\Models\DeveloperProject;
use App\Models\OffplanMeta;
use App\Models\OffplanProperty;
use App\Models\Property;
use App\Models\PropertyFeatures;
use App\Models\PropertyMedia;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateOffplanPropertyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $type;
    protected $project_id;
    public $timeout = 17200;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($type, $project_id)
    {
        $this->type = $type;
        $this->project_id = $project_id;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        info('UpdateOffplanPropertyJob');
        $counter = 0;
        $developer = DeveloperProject::where('id', $this->project_id)->first();
        $projectProperty = OffplanProperty::where('developer_id', $developer->developer_id)->where('project_name', $developer->project_name)->whereNotNull('property_id')->get();

        if ($projectProperty->count() > 0) {
            foreach ($projectProperty as $offplanProperty) {
                $property = Property::where('id', $offplanProperty->property_id)->first();
                if ($property) {

                    $off_title = $developer?->titles?->random(1)->first();

                    if ($this->type == 'title') {
                        $title = titleGenerator($off_title->value, $offplanProperty->developer_id, $offplanProperty->project_name);
                        $property->title = $title ?? $property->title;
                    }

                    if ($this->type == 'description') {
                        $project_description = $developer->description ?? 'Project Name is ' . $offplanProperty->project_name . ' By ' . $developer->full_name . '.';
                        $description = processDescription($offplanProperty->id, $offplanProperty->developer_id);
                        $property->description = $description ?? $property->description;
                    }

                    if ($this->type == 'amenities') {
                        $amenities = json_decode($developer->project_amenities) ?? null;

                        PropertyFeatures::where("property_id", $property->id)->whereNotIn("property_amenity_id", $amenities ?? [])->delete();
                        foreach ($amenities ?? [] as $feature) {
                            PropertyFeatures::updateOrCreate([
                                "property_id" => $property->id,
                                "property_amenity_id" => $feature,
                            ], [
                                "created_by_id" => auth()->id(),
                                "updated_by_id" => auth()->id(),
                            ]);
                        }

                    }
                    if ($this->type == 'images') {
                        $offplan_media = OffplanMeta::where('developer_project_id', $developer->id)
                            ->where('key', 'Photos')
                            ->get();
                        $shuffledMedia = $offplan_media->shuffle();
                        foreach ($shuffledMedia as $media) {
                            $decoded_images = json_decode($media->value);
                            $aleady = PropertyMedia::where('property_id', $property->id)->where('path', $decoded_images->image)->first();
                            if ($aleady) {
                                continue;
                            } else {
                                PropertyMedia::create([
                                    "property_id" => $property->id,
                                    "type" => 'Photos',
                                    "path" => $decoded_images->image ?? null,
                                    "original" => $decoded_images->original ?? null,
                                    "thumbnail" => $decoded_images->thumbnail ?? null,
                                    "created_by_id" => auth()->id(),
                                ]);
                            }

                        }

                    }

                    // $property->description = $developer->description;
                    $property->withoutApproval()->save();
                }
                $counter++;
            }

        }
        info('UpdateOffplanPropertyJob Done Total Changes: '. $counter);
    }

}
