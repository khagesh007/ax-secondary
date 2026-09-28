<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\DealInviteReminder;
use App\Models\DealInvite;
class DealCheckPendingLeadInvite extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deal:checkPendingLeadInvite';

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


        $pendingInvites = DealInvite::where(function ($query) {
            $query->whereNull('form_submitted_at')
                  ->orWhere('is_signed', '0');
        })
        ->where('last_reminded', '<', Carbon::now()->subDay())->chunk(50, function ($chunk) {
            foreach($chunk as $invite){
                DealInviteReminder::dispatch($invite);
            }
        });

        return Command::SUCCESS;
    }
}
