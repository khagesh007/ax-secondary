<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Jobs\AgentLeadNotificationJob;
use App\Jobs\ManagerLeadNotificationJob;
use App\Models\Lead;
use App\Traits\TelegramTrait;
use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;

class LeadReportController extends Controller
{
    use TelegramTrait;
    public function getLeadsStatusReport(Request $request)
    {

        // dispatch(new AgentLeadNotificationJob());
        // dispatch(new ManagerLeadNotificationJob());

        $groupBy = 'agent_id';
        $query = Lead::where('status', true)->where('lead_status_id', 1)->where('is_active', 0);
        $query->groupBy('agent_id');
        $leadsCount = $query->select($groupBy, \DB::raw('COUNT(*) as count'))->get();
        $leadsCount = $leadsCount->map(function ($item) use ($groupBy) {
            $relation = 'agent';
            $name = $item->$relation->full_name ?? $item->$relation->title ?? '';
            return ['Name' => $name, 'Count' => $item->count];
        });
        $nameCountList = [];
        $message = '';
        foreach ($leadsCount as $item) {
            if ($item['Name'] == "" || $item['Count'] == 0) {
                continue;
            }
            $nameCountList[$item['Name']] = $item['Count'];
        }

        arsort($nameCountList);
        $total = 0;
        $message = "<b>AX Secondary CRM</b>\n\n";
        $message .= "🔴⚠️<b>Leads in `NEW` Status</b>⚠️🔴\n";
        $message .= "\n";
        $message .= '<b>Date</b>' . ' : ' . date('d-M-Y') . "\n\n";
        foreach ($nameCountList as $key => $value) {
            if ($value > 2) {
                $message .= $key . ' : ' . '<b>' . $value . '</b>' . "\n";
            }$total += $value;
        }
        $message .= "\n";
        $message .= '<b>Total Leads' . ' : ' . $total . "</b> in 'NEW' status.\n";
        $message .= "\n";
        $message .= "==========================\n";
        $message .= "\n";
        $message .= "🔵<b>Leads by Source (Last 24 Hours)</b> 🔵\n";
        $message .= "\n";
        $groupBy = 'source_id';
        $query = Lead::where('status', true);
        $query->groupBy('source_id');
        $leadsCount = $query->select($groupBy, \DB::raw('COUNT(*) as count'))
            ->with('leadSource')
            ->whereHas('leadSource', function ($query) {
                $query->where('status', true);
            })
            ->where('created_at', '>=', now()->subHours(24))
            ->get();

        $leadsCount = $leadsCount->sortBy(function ($item) {
            return $item->leadSource->order ?? 0;
        })->map(function ($item) {
            $name = $item->leadSource->name ?? '';
            $order = $item->leadSource->order ?? '';
            return ['Name' => $name, 'Count' => $item->count];
        });
        $total = 0;
        foreach ($leadsCount as $item) {
            if ($item['Name'] == "" || $item['Count'] == 0) {
                continue;
            }
            $message .= $item['Name'] . ' : ' . '<b>' . $value . '</b>' . "\n";
            $total += $value;
        }

        $message .= "\n";
        $message .= '<b>Last 24 hours Leads' . ' : ' . $total . "</b>.\n";
        $message .= "\n";

        $telegram = Telegram::bot('AxBot');
        $telegramMessage = $this->sendTelegramMessage($telegram, $message);

    }

    public function agentLeadReport(Request $request)
    {

        dispatch(new AgentLeadNotificationJob());
        dd('Job dispatched.');

    }

    public function managerLeadReport(Request $request)
    {
        dispatch(new ManagerLeadNotificationJob());
        dd('Job dispatched.');
    }

    public function sendTelegramMessage($telegram, $message)
    {
        try {
            $telegram->sendMessage([
                // 'chat_id' => '-1001854372037',
                'chat_id' => '-1001854372037',
                'text' => $message,
                'parse_mode' => 'html',
            ]);
            return true;
        } catch (\Throwable $th) {
            \Log::info('Telegram error log: ' . $th->getMessage() . " chat_id is: -1001854372037 message is: " . $message);
            return false;
        }
    }

}
