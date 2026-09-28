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

class BayutMetaFetcher implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $timeout = 17200;

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
        $portal = Portal::where('name', 'Bayut')->first();
        $properties = Property::where('publish_status', 'PUBLISHED')
            ->orderBy('ref_no', 'desc')
            ->whereHas('portals', function ($q) use ($portal) {
                $q->where('portal_id', $portal->id);
            })->pluck('ref_no');

        foreach ($properties as $ref_no) {
           // echo $ref_no . "\n";
            $params = new Request();
            $params->replace(['ref_no' => $ref_no]);
             (new ExternalApiController())->fetchBayutData($params);
        }


        // $portal_abu_dhabi = Portal::where('name', 'Bayut')->first();
        // $properties = Property::where('region_id',2)->where('publish_status', 'PUBLISHED')
        //     ->orderBy('ref_no', 'desc')
        //     ->whereHas('portals', function ($q) use ($portal_abu_dhabi) {
        //         $q->where('portal_id', $portal_abu_dhabi->id);
        //     })->pluck('ref_no');

        // foreach ($properties as $ref_no) {
        //     $params = new Request();
        //     $params->replace(['ref_no' => $ref_no]);
        //      (new ExternalApiController())->fetchBayutDataAbuDhabi($params);
        // }


        // $portal_commercial = Portal::where('name', 'Bayut')->first();
        // $properties = Property::whereHas('agent',function($q){
        //     $q->where('is_commercial', true);
        // })->where('publish_status', 'PUBLISHED')
        //     ->orderBy('ref_no', 'desc')
        //     ->whereHas('portals', function ($q) use ($portal_commercial) {
        //         $q->where('portal_id', $portal_commercial->id);
        //     })->pluck('ref_no');

        // foreach ($properties as $ref_no) {
        //     $params = new Request();
        //     $params->replace(['ref_no' => $ref_no]);
        //      (new ExternalApiController())->fetchBayutDataCommercial($params);
        // }

    }
}
