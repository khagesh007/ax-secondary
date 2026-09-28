<?php

namespace App\Jobs;

use App\Http\Controllers\Extra\PropertyReraController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ReraRecordUpdate implements ShouldQueue
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
     //   Log::info('ReraRecordUpdate Job started for property ID: ' . $this->property->id);
        $req = new Request();
        $req->merge(['id' => $this->property->id]);
        $res = (new PropertyReraController())->propertyCheckRera($req);
    }
}
