<?php

namespace App\Console\Commands;

use App\Models\UserOAuthToken;
use Illuminate\Console\Command;

class DeleteExpiredInvalidOauthTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'purge:invalid-oauth-tokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will delete invalid token grants from UserOauthTokens table every day at midnight';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $count = 0;
        try {
            $tokenToDelete = '{"error":"invalid_grant","error_description":"Account has been deleted"}';

            $deletedCount = UserOAuthToken::where('token', $tokenToDelete)->delete();

            $count += $deletedCount;
            $tokenToDelete = '{"error":"invalid_grant","error_description":"Bad Request"}';

            $deletedCount = UserOAuthToken::where('token', $tokenToDelete)->delete();
            $count += $deletedCount;

            $this->info('Total deleted invalid Oauth tokens count ' . $count);
            info('Total deleted invalid Oauth tokens ' . $count);
        }catch (\Exception $e) {
            $this->info('Error while deleting invalid Oauth tokens' . $count);
           info('Error while deleting invalid Oauth tokens ' . json_encode($e->getMessage()));
            return Command::FAILURE;
        }
        return Command::SUCCESS;
    }
}
