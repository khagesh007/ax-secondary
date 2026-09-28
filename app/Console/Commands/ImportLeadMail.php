<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\EmailParserApiController;
use Illuminate\Console\Command;

class ImportLeadMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:leadEmails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will import leads from emails to lead table';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        (new EmailParserApiController())->fetchEmails();
        return Command::SUCCESS;
    }
}
