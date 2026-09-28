<?php

namespace App\Console\Commands;

use App\Jobs\ApplyNewWatermarkJob;
use App\Models\Property;
use App\Traits\TelegramTrait;
use Illuminate\Console\Command;

class ApplyNewWatermark extends Command
{
    use TelegramTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:apply-new-watermark';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will make a copy of orignal image and apply new watermark on it for selected properties';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $watermarkedLastProperty = $this->getLastProcessedProperty();
        $properties = Property::where('hot', '!=', 1)
            ->when($watermarkedLastProperty != 0, function ($query) use ($watermarkedLastProperty) {
                $query->where('id', '<', $watermarkedLastProperty);
            })
            ->orderBy('id', 'desc')->limit(15)->get();
            $is_trashed = false;
        if ($properties->count() == 0) {
            //trashed property
            echo 'Taking trashed properties for watermarking -------------------';
            $is_trashed = true;
          $trashed_property_id =  $this->getLastProcessedPropertyTrashed();
        $properties = Property::where('hot', '!=', 1)
            ->when($trashed_property_id != 0, function ($query) use ($trashed_property_id) {
                $query->where('id', '<', $trashed_property_id);
            })
            ->orderBy('id', 'desc')->limit(15)->withTrashed()->get();
        }
        if ($properties->count() == 0) {

            echo 'No properties found for watermarking -------------------';
            $message = 'No property left for processing for watermarking, please check';

            $this->sendTelegramMessageHtml($message, ['795165330']);
        } else {
            $i = 1;
            foreach ($properties ?? [] as $property) {
                echo 'Processing property id: ' . $property->id . ' -------------------';
                if ($i <= 10) {
                    // Dispatch the first 10 jobs to the redis connection
                    ApplyNewWatermarkJob::dispatch($property)->onConnection('redis')->onQueue('high');
                } else {
                    // Dispatch the next 5 jobs to the database connection
                    ApplyNewWatermarkJob::dispatch($property)->onConnection('database')->onQueue('default');
                }
                $i++;
                $is_trashed ? $this->updateLastProcessedPropertyTrashed($property->id) : $this->updateLastProcessedProperty($property->id);
            }
        }


        return Command::SUCCESS;
    }

    private function updateLastProcessedPropertyTrashed($id)
    {

        cache()->put('watermark_job_last_trashed_property_id', $id);
    }

    private function getLastProcessedPropertyTrashed()
    {
        return cache()->get('watermark_job_last_trashed_property_id', 0);
    }
    private function updateLastProcessedProperty($id)
    {

        cache()->put('watermark_job_last_property_id', $id);
    }

    private function getLastProcessedProperty()
    {
        return cache()->get('watermark_job_last_property_id', 0);
    }
}
