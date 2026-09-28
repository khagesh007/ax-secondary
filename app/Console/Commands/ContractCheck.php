<?php

namespace App\Console\Commands;

use App\Events\ContractExpiring;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
class ContractCheck extends Command
{
    /**
     * ------------------------Important: This command should be running one time in a day.--------------------
     *
     *
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'listing:contractCheck';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will check the contract end date and send email & notifications to the agent';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {


        DB::table('properties')->wheredate("contract_end", Carbon::today()->format('Y-m-d'))
            ->orderBy('id')
            ->select('id','ref_no','contract_start','contract_end','agent_id')
            ->where('publish_status','PUBLISHED')
            ->chunk(20, function ($properties) {
                foreach ($properties as $property) {
                    event(new ContractExpiring($property, '1'));
                }
        });
        DB::table('properties')->wheredate("contract_end", Carbon::today()->addDays(3)->format('Y-m-d'))
            ->orderBy('id')
            ->select('id','ref_no','contract_start','contract_end','agent_id')
            ->where('publish_status','PUBLISHED')
            ->chunk(20, function ($properties) {
                foreach ($properties as $property) {
                    event(new ContractExpiring($property, '3'));
                }
        });
        DB::table('properties')->wheredate("contract_end", Carbon::today()->addDays(7)->format('Y-m-d'))
            ->orderBy('id')
            ->select('id','ref_no','contract_start','contract_end','agent_id')
            ->where('publish_status','PUBLISHED')
            ->chunk(20, function ($properties) {
                foreach ($properties as $property) {
                    event(new ContractExpiring($property, '7'));
                }
        });
        DB::table('properties')->wheredate("contract_end", Carbon::today()->addDays(14)->format('Y-m-d'))
            ->orderBy('id')
            ->select('id','ref_no','contract_start','contract_end','agent_id')
            ->where('publish_status','PUBLISHED')
            ->chunk(20, function ($properties) {
                foreach ($properties as $property) {
                    event(new ContractExpiring($property, '14'));
                }
        });
        return Command::SUCCESS;
    }
}
