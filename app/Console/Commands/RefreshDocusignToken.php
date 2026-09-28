<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OauthToken;
use Carbon\Carbon;
use App\Traits\DocusignTrait;
class RefreshDocusignToken extends Command
{
    use DocusignTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'docusign:refreshToken';

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
        $token = OauthToken::where('application', 'docusign')->first();
        //check if token is expiring in less than 1 hour because we need to refresh it before it expires
        //it expires in 8 hours
       // if($token->expires_at < now()->addHour()){
           $this->refreshAccessTokenDocusign();
        //}
    }
}
