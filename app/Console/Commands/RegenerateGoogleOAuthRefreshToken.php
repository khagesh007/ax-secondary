<?php

namespace App\Console\Commands;

use App\Models\UserOAuthToken;
use App\Traits\OAuthTrait;
use Illuminate\Console\Command;

class RegenerateGoogleOAuthRefreshToken extends Command
{
    use OAuthTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'oauth:regenrate-google-access-token';

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
        $userOAuthTokens = UserOAuthToken::get();
        foreach ($userOAuthTokens ?? [] as $userOAuthToken) {
            if ($this->checkIfGoogleAccessTokenExipred($userOAuthToken->token)) {
                $this->updateUserOAuthToken($userOAuthToken->token, $userOAuthToken->user_id);
            }
        }
        return Command::SUCCESS;
    }
}
