<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\ExternalApiController;
use Illuminate\Console\Command;

class PfWhatsAppLeadFetch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leads:fetchPfMissedWhatsAppLeads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will fetch whatsapp leads from property finder which was missed for recent days';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        (new ExternalApiController())->propertyFinderWhatsappLeadsNew();
        return Command::SUCCESS;
    }
}
