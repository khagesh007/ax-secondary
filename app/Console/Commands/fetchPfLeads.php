<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Traits\PropertyFinderTrait;
use App\Http\Controllers\Api\ExternalApiController;

class fetchPfLeads extends Command
{
    use PropertyFinderTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pf:Leads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to store pf leads';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //Tokens
//        $tokens = [
//            "secondary_token" => env('PROPERTY_FINDER_SECONDARY_TOKEN'),
//            "primary_token" => env('PROPERTY_FINDER_PRIMARY_TOKEN'),
//            "commercial_token" => env('PROPERTY_FINDER_COMMERCIAL_TOKEN'),
//            "abu_dhabi_token" => env('PROPERTY_FINDER_ABU_DHABI_TOKEN')
//        ];
       $types = ['primary', 'secondary', 'commercial', 'abu_dhabi'];
        foreach($types as $type){
            $tokens = $this->getPfAccessToken($type);
            $token = $tokens['token'];
            (new ExternalApiController())->propertyFinderEmailLeads($token);
        }

        return Command::SUCCESS;
    }
}
