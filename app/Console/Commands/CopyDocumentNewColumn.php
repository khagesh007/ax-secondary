<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\CopyDocumentToNewColumnJob;
use App\Models\Property;
use App\Models\PropertyMedia;
class CopyDocumentNewColumn extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:copy-document-to-column';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $properties = Property::limit(10)->get();
        foreach ($properties ?? [] as $property) {
           //echo 'Copying doc to with_watermark: '.$property->ref_no.'-------------------';
          //  CopyDocumentToNewColumnJob::dispatch($property);
            $documents = PropertyMedia::withTrashed()->where('type','!=','Photos')->where('property_id', $property->id)->get();
           echo count($documents);
            foreach ($documents as $doc) {
               // echo 'Copying docu: '.$doc->id.'-------------------';
                    $doc->with_watermark = $doc['path'];
                    $doc->save();
            }
        }
        return Command::SUCCESS;
    }
}
