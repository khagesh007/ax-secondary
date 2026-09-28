<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Admin\Deals\BitrixDealController;
class BitrixDealImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deal:bitrixImport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will import deals from bitrix crm to our system.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
       // (new BitrixDealController())->fetchDeals();
        return Command::SUCCESS;
    }
}
