<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
class AgentLastDealDateReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agent:last-deal-date-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private function getData($userIds)
    {
        $response = Http::withHeaders([
            'token' => env('AXDEAL_SECRET_KEY'),
        ])->post('https://deals.axcrm.ae/backend/api/report/get-agents-last-announced-deal-date', [
            'users' => $userIds
        ]);

        if ($response->status() == 200) {
            return $response->json();
        } else {
            return [];
        }
    }

    private function updateData($data)
    {
        foreach ($data as $value) {
            User::where('id', $value['secondary_user_id'])->update([
                'last_deal_date' => $value['date'],
            ]);
        }
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $userIds = User::whereHas("roles", function ($q) {
            $q->where("name", "agent");
        })->where("status", 1)->pluck("id");

        foreach (collect($userIds)->chunk(40) as $chunk) {
            $data = $this->getData($chunk->toArray());
            $this->updateData($data);
        }

        return Command::SUCCESS;
    }
}
