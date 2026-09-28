<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Traits\PropertyFinderTrait;
use App\Http\Controllers\Api\ExternalApiController;

class FetchPropertyFinderWhatsappLeadsDateWise extends Command
{
    use PropertyFinderTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:property-finder-whatsapp-leads-date-wise {date?}';

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
        //$tokens = $this->getPfAccessToken('secondary');
        $types = ['secondary', 'primary', 'commercial', 'abu_dhabi'];
        foreach ($types as $type) {
            $tokens = $this->getPfAccessToken($type);
            $token = $tokens['token'];
            (new ExternalApiController())->propertyFinderWhatsappLeadsDateWise($token , $this->argument('date'));
        }

        return Command::SUCCESS;
    }
}
