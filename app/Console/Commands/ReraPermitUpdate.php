<?php

namespace App\Console\Commands;
use App\Http\Controllers\Extra\PropertyReraController;
use App\Http\Controllers\TestController;
use App\Jobs\ReraRecordUpdate;
use App\Models\Property;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReraPermitUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rera:update';

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
          Property::where('publish_status', 'PUBLISHED')
            ->where('rera_permit_number', '!=', '')
            ->whereNull('qr_link')
           // ->whereDate('rera_expiry_date', '<',Carbon::today())
            ->chunk(50, function ($properties) {
                foreach ($properties as $property) {

                    ReraRecordUpdate::dispatch($property);
                }
            });

        return Command::SUCCESS;
    }
}
