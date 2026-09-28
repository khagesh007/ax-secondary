<?php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Telegram\Bot\Laravel\Facades\Telegram;

class DealInsightsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'group:dealInsights';

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

        $twoMonthsAgo = Carbon::now()->subMonths(2);
        $secondary_agents = User::where('status', true)->where('is_report_exempt', 0)
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })
            ->where('bitrix_role', 'like', '%Secondary%')
            ->where('bitrix_role', 'not like', '%Secondary Admin%')
            ->where('bitrix_role', 'not like', '%Primary%')
            ->where('region_id', '!=', 2)
            ->where('joining_date', '<', $twoMonthsAgo)
            ->whereNull('last_deal_date')
            ->orderBy('joining_date', 'asc')
            ->get();

        $message_lead_secondary = "<b>SECONDARY</b> \n";
        foreach ($secondary_agents as $user) {
            $message_lead_secondary .= ' <b>' . $user->full_name . '</b> - ' . Carbon::parse($user->joining_date)->format('d M, Y') . "\n";
        }
        $primary_users = User::where('status', true)->where('is_report_exempt', 0)
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })
            ->where('bitrix_role', 'like', '%Primary%')
            ->where('bitrix_role', 'not like', '%Primary Admin%')
            ->where('bitrix_role', 'not like', '%Secondary%')
            ->where('bitrix_role', 'not like', '%Abu-Dhabi Team%')
            ->where('region_id', '!=', 2)
            ->where('joining_date', '<', $twoMonthsAgo)
            ->whereNull('last_deal_date')
            ->orderBy('joining_date', 'asc')
            ->get();
        $message_lead_primary = "<b>PRIMARY</b> \n";
        foreach ($primary_users as $user) {
            $message_lead_primary .= ' <b>' . $user->full_name . '</b> - ' . Carbon::parse($user->joining_date)->format('d M, Y') . "\n";
        }

        $abu_dhabi = User::where('status', true)->where('is_report_exempt', 0)
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })
            ->where('region_id', 2)
            ->where('joining_date', '<', $twoMonthsAgo)
            ->whereNull('last_deal_date')
            ->orderBy('joining_date', 'asc')
            ->get();
        $message_lead_abu_dhabi = "<b>ABU DHABI</b> \n";
        foreach ($abu_dhabi as $user) {
            $message_lead_abu_dhabi .= ' <b>' . $user->full_name . '</b> - ' . Carbon::parse($user->joining_date)->format('d M, Y') . "\n";
        }

        $chinese = User::where('status', true)->where('is_report_exempt', 0)
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })
            ->where('bitrix_role', 'like', '%Chinese%')
            ->where('bitrix_role', 'not like', '%Chinese Admin%')
            ->where('region_id', '!=', 2)
            ->where('joining_date', '<', $twoMonthsAgo)
            ->whereNull('last_deal_date')
            ->orderBy('joining_date', 'asc')
            ->get();
        $message_lead_chinese = "<b>CHINESE</b> \n";
        foreach ($chinese as $user) {
            $message_lead_chinese .= ' <b>' . $user->full_name . '</b> - ' . Carbon::parse($user->joining_date)->format('d M, Y') . "\n";
        }
        $message = "<b>AX Secondary CRM</b>\n\n";
        $message .= "🚨 0 DEALS SINCE JOINING (MORE THAN 2 MONTH) 🚨\n\n";
        $message .= $message_lead_secondary;
        $message .= "\n";
        // $message .= $message_lead_primary;
        // $message .= "\n";
        $message .= $message_lead_abu_dhabi;
        $message .= "\n";
        // $message .= $message_lead_chinese;
        $telegram = Telegram::bot('notification_bot');
        $telegramMessage = $this->sendTelegramMessage($telegram, $message);

        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

        $secondary_agents = User::where('status', true)->where('is_report_exempt', 0)
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })
            ->where('bitrix_role', 'like', '%Secondary%')
            ->where('bitrix_role', 'not like', '%Secondary Admin%')
            ->where('bitrix_role', 'not like', '%Primary%')
            ->where('region_id', '!=', 2)
            ->whereDate('joining_date', '<=', now()->subMonths(2)->subWeeks(1)) // Filter for joining date more than 2 months and 1 week ago
            ->orderBy('joining_date', 'asc')
            ->get();

        $secondary_agents = $secondary_agents->map(function ($agent) {
            $daysSinceLastDeal = now()->diffInDays($agent->last_deal_date ?? $agent->joining_date ?? $agent->created_at);
            $agent->days_since_last_deal = $daysSinceLastDeal;
            return $agent;
        });
        $sorted_secondary_agents = $secondary_agents->sortByDesc('days_since_last_deal');

        // dd($secondary_agents);

        $message_lead_secondary = "<b>SECONDARY</b> \n";
        foreach ($sorted_secondary_agents as $user) {
            if($user->days_since_last_deal > 39){
                $message_lead_secondary .= ' <b>' . $user->full_name . '</b> - ' . $user->days_since_last_deal. "\n";
            }
        }
        $primary_users = User::where('status', true)->where('is_report_exempt', 0)
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })
            ->where('bitrix_role', 'like', '%Primary%')
            ->where('bitrix_role', 'not like', '%Primary Admin%')
            ->where('bitrix_role', 'not like', '%Secondary%')
            ->where('bitrix_role', 'not like', '%Abu-Dhabi Team%')
            ->whereDate('joining_date', '<=', now()->subMonths(2)->subWeeks(1)) // Filter for joining date more than 2 months and 1 week ago
            ->where('region_id', '!=', 2)

            ->orderBy('joining_date', 'asc')
            ->get();
            $primary_users = $primary_users->map(function ($agent) {
                $daysSinceLastDeal = now()->diffInDays($agent->last_deal_date ?? $agent->joining_date ?? $agent->created_at);
                $agent->days_since_last_deal = $daysSinceLastDeal;
                return $agent;
            });
            $sorted_primary_users = $primary_users->sortByDesc('days_since_last_deal');

        $message_lead_primary = "<b>PRIMARY</b> \n";
        foreach ($sorted_primary_users as $user) {
            if($user->days_since_last_deal > 39){
            $message_lead_primary .= ' <b>' . $user->full_name . '</b> - ' .  $user->days_since_last_deal . "\n";
            }
        }

        $abu_dhabi = User::where('status', true)->where('is_report_exempt', 0)
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })
            ->where('region_id', 2)
            ->whereDate('joining_date', '<=', now()->subMonths(2)->subWeeks(1)) // Filter for joining date more than 2 months and 1 week ago
            ->orderBy('joining_date', 'asc')
            ->get();
            $abu_dhabi = $abu_dhabi->map(function ($agent) {
                $daysSinceLastDeal = now()->diffInDays($agent->last_deal_date ?? $agent->joining_date ?? $agent->created_at);
                $agent->days_since_last_deal = $daysSinceLastDeal;
                return $agent;
            });
            $sorted_abu_dhabi = $abu_dhabi->sortByDesc('days_since_last_deal');

        $message_lead_abu_dhabi = "<b>ABU DHABI</b> \n";
        foreach ($sorted_abu_dhabi as $user) {
            if($user->days_since_last_deal > 39){

            $message_lead_abu_dhabi .= ' <b>' . $user->full_name . '</b> - ' . $user->days_since_last_deal  . "\n";
        }}

        $chinese = User::where('status', true)->where('is_report_exempt', 0)
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })
            ->where('bitrix_role', 'like', '%Chinese%')
            ->where('bitrix_role', 'not like', '%Chinese Admin%')
            ->where('region_id', '!=', 2)
            ->whereDate('joining_date', '<=', now()->subMonths(2)->subWeeks(1)) // Filter for joining date more than 2 months and 1 week ago
            ->orderBy('joining_date', 'asc')
            ->get();

            $chinese = $chinese->map(function ($agent) {
                $daysSinceLastDeal = now()->diffInDays($agent->last_deal_date ?? $agent->joining_date ?? $agent->created_at);
                $agent->days_since_last_deal = $daysSinceLastDeal;
                return $agent;
            });
            $sorted_chinese = $chinese->sortByDesc('days_since_last_deal');

        $message_lead_chinese = "<b>CHINESE</b> \n";
        foreach ($sorted_chinese as $user) {
            if($user->days_since_last_deal > 39){
            $message_lead_chinese .= ' <b>' . $user->full_name . '</b> - ' .  $user->days_since_last_deal . "\n";
        }}
        $message = "<b>AX Secondary CRM</b>\n\n";
        $message .= "⚠️DAYS SINCE LAST DEAL⚠️ \n\n";
        $message .= $message_lead_secondary;
        $message .= "\n";
        // $message .= $message_lead_primary;
        // $message .= "\n";
        $message .= $message_lead_abu_dhabi;
        $message .= "\n";
        // $message .= $message_lead_chinese;
        $telegram = Telegram::bot('notification_bot');
        $telegramMessage = $this->sendTelegramMessage($telegram, $message);

        return Command::SUCCESS;
    }

    public function sendTelegramMessage($telegram, $message)
    {
        try {
            if (env('APP_ENV') == 'production') {
                $telegram->sendMessage([
                    'chat_id' => '5970238984',
                    'text' => $message,
                    'parse_mode' => 'html',
                ]);
            } else {
                $telegram->sendMessage([
                    'chat_id' => '5970238984',
                    'text' => $message,
                    'parse_mode' => 'html',
                ]);
            }

            return true;
        } catch (\Throwable $th) {
            \Log::info('Telegram error log: ' . $th->getMessage() . " chat_id is: -1001435755800 message is: " . $message);
            return false;
        }
    }
}
