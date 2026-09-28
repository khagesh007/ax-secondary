<?php

namespace App\Console\Commands;
use App\Models\Lead;
use App\Models\LeadNote;
use App\Models\Medium;
use App\Models\Property;
use App\Models\Source;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class LeadReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lead:report';

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
        $yesterday = Carbon::yesterday();
        $user = User::where('status', 1)->whereHas("roles", function ($q) {
            $q->whereIn("name", ["Agent", "Line Manager", "Team Leader", "Sales Director", "Head of Secondary"]);
        })->get();
        // foreach($leads as $lead){
        //     $lead
        // }
        return Command::SUCCESS;
    }
}
