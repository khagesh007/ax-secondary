<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Traits\TelegramTrait;
use Illuminate\Http\Request;

class LeadReportController extends Controller
{
    use TelegramTrait;
   public function getLeadsStatusReport(Request $request)
    {

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . 'g38WtNUN9aPlLDEeK1k07UPgHecWRY3inbzINjnD',
        ])->post(route('api.v1.reporting.leads', [
            'group_by' => 'agent_id',
            'lead_status' => 'New',
            'active_agent' => '1',
        ]));
        $data = $response->json();
        $nameCountList = [];
        $message = '';
        foreach ($data['data']['breakdown'] as $item) {
            if ($item['Name'] == "" || $item['Count']==0 ) {
                continue;
            }
            $nameCountList[$item['Name']] = $item['Count'];
            // $message .= $item['Name'] . ' : ' . $item['Count'] . "\n";
        }

        arsort($nameCountList);
        $total = 0;
        $message = "🔵⚠️<b>Leads in `NEW` Status</b>⚠️🔵️\n".$message;
        $message .= "\n";
        $message .= '<b>Date</b>' . ' : ' . date('d-M-Y') . "\n\n";

        foreach ($nameCountList as $key => $value) {
            $message .= $key . ' : ' . '<b>'.$value.'</b>' . "\n";
            $total += $value;
        }

        $message .= "\n";
        $message .= '<b>Total Leads' . ' : ' . $total . "</b>\n";
        $message .= "\n";

        $this->sendTelegramMessageHtml($message, ['5970238984'], null, null, 'lead_bot');

        // $nameCountList = array_chunk($nameCountList, ceil(count($nameCountList) / 3), true);
        $message = '';



        // foreach ($nameCountList as $key => $value) {
        //     $message .="🔵⚠️<b>Leads in `NEW` Status</b>⚠️🔵️\n";
        //     $message .= 'Report  ' . ($key + 1) . "\n";
        //     foreach ($value as $name => $count) {
        //         $message .= '<b>'.$name.'</b>' . ' : ' . $count . "\n";
        //     }
        //     $message .= "\n";
        //     $this->sendTelegramMessageHtml($message, ['5970238984'], null, null, 'lead_bot');
        //     $message = '';
        // }
        return $nameCountList;
    }
}




