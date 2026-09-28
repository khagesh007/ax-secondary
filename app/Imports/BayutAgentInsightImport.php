<?php

namespace App\Imports;

use App\Models\UserThirdPartyData;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use App\Models\User;

class BayutAgentInsightImport implements ToModel, WithHeadingRow, WithStartRow
{

    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function startRow(): int
    {
        return 20;
    }

    public function model(array $row)
    {
        $array = [
            'bayut_user_id' => $row['overview'],
            'name' => $row['1'],
            'role' => $row['2'],
            'email' => $row['3'],
            'mobile' => $row['4'],
            'proxy_number' => $row['5'],
            'whatsapp' => $row['6'],
            'landline' => $row['7'],
            'total_active_listings' => $row['8'],
            'active_basic_listings' => $row['8'] - $row['11'] - $row['10'],
            'active_hot_listings' => $row['10'],
            'active_signature_listings' => $row['11'],
            'active_trucheck_listings' => $row['12'],
            'active_checked_listings' => $row['13'],
            'call_answer_rate' => round(((float)rtrim($row['14'], '%')), 2),
            'answered_calls' => $row['15'],
            'missed_calls' => $row['16'],
            'not_connected_call' => $row['17'],
            'total_calls' => $row['18'],
            'whatsapp_response_rate' => round(((float)rtrim($row['19'], '%') * 100), 2),
            'whatsapp_response_time' => $row['20'],
            'answered_enquires' => $row['21'],
            'unanswered_enquires' => $row['22'],
            'total_enquires' => $row['23'],
            'leads_received' => $row['24'],
            'trubroker' => $row['25'],
            'quality_lister' => $row['26'],
            'responsive_broker' => $row['27'],
            'validation_activations' => $row['28'],
            'quality_lister_progress' => $row['29'],
            'response_rate' => round(((float)rtrim($row['30'], '%')), 2),
            'response_broker_progress' => $row['31'],
            'ranking' => $row['32'],
            'tru_points_earned' => $row['33'],
        ];

        $user = User::where('third_party_ids->bayut_user_id', $row['overview'])->first();
        if ($user) {
            $thirdPartyData = UserThirdPartyData::where('user_id', $user->id)
                ->where('portal', 'bayut')
                ->where('start_date', $this->startDate)
                ->where('end_date', $this->endDate)->first();
            $userFullData = json_decode($thirdPartyData?->useful_data ?? null, true) ?? [];
            if (!$thirdPartyData) {
                $thirdPartyData = new UserThirdPartyData();
            }

            $thirdPartyData->user_id = $user->id;
            $thirdPartyData->portal = 'bayut';
            $thirdPartyData->start_date = $this->startDate;
            $thirdPartyData->end_date = $this->endDate;
            $thirdPartyData->useful_data = json_encode(array_merge($userFullData, $array));
            $thirdPartyData->portal_data_updated_at = Carbon::now();
            $thirdPartyData->save();
        }
    }
}
