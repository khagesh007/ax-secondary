<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PropertyImport;


class ExcelPublishPropertyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $excelfile;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($excelfile)
    {
        $this->excelfile = $excelfile;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $excelfile = $this->excelfile;
        Excel::import(new PropertyImport(), $excelfile);

    }
}
