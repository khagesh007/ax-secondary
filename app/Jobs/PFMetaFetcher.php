<?php

namespace App\Jobs;

use App\Http\Controllers\Api\ExternalApiController;
use App\Models\Portal;
use App\Models\Property;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PFMetaFetcher implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $portal = Portal::where('name', 'Property Finder')->first();
        $properties = Property::where('publish_status', 'PUBLISHED')
            ->whereHas('portals', function ($q) use ($portal) {
                $q->where('portal_id', $portal->id);
            })->orderBy('ref_no', 'desc')
            ->pluck('ref_no');

        foreach ($properties as $ref_no) {
           // echo $ref_no . "\n";
            $params = new Request();
            $params->replace(['ref_no' => $ref_no]);
             (new ExternalApiController())->fetchPFData($params);

        }
    }
}
